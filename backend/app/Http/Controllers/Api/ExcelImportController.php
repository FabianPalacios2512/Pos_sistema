<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ExcelParserService;
use App\Services\AIColumnMapperService;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ExcelImportController extends Controller
{
    protected ExcelParserService $excelParser;
    protected AIColumnMapperService $columnMapper;

    public function __construct(ExcelParserService $excelParser, AIColumnMapperService $columnMapper)
    {
        $this->excelParser = $excelParser;
        $this->columnMapper = $columnMapper;
    }

    /**
     * Paso 1: Subir archivo y analizar con IA
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function upload(Request $request)
    {
        // Validación más flexible para permitir diferentes tipos MIME de Excel/CSV
        $request->validate([
            'file' => 'required|file|max:10240', // Max 10MB
        ]);

        // Validar extensión manualmente
        $file = $request->file('file');
        $extension = strtolower($file->getClientOriginalExtension());
        $allowedExtensions = ['xlsx', 'xls', 'csv'];

        if (!in_array($extension, $allowedExtensions)) {
            return response()->json([
                'success' => false,
                'message' => 'El archivo debe ser Excel (.xlsx, .xls) o CSV (.csv)'
            ], 422);
        }

        try {
            // Guardar archivo temporalmente
            $filename = 'import_' . Str::random(20) . '.' . $extension;
            $path = $file->storeAs('temp/imports', $filename);
            $fullPath = storage_path('app/' . $path);


            // Parsear archivo
            $parseResult = $this->excelParser->parseFile($fullPath);


            // Analizar con IA
            $aiAnalysis = $this->columnMapper->analyzeColumnsWithAI(
                $parseResult['headers'],
                $parseResult['sample_data']
            );


            // Guardar datos en sesión/cache para el siguiente paso
            $importId = Str::random(32);
            cache()->put('excel_import_' . $importId, [
                'file_path' => $fullPath,
                'headers' => $parseResult['headers'],
                'total_rows' => $parseResult['total_rows'],
                'data' => $parseResult['data'],
                'column_mapping' => $aiAnalysis['column_mapping'],
            ], now()->addHours(2));

            return response()->json([
                'success' => true,
                'import_id' => $importId,
                'file_name' => $file->getClientOriginalName(),
                'total_rows' => $parseResult['total_rows'],
                'headers' => $parseResult['headers'],
                'sample_data' => $parseResult['sample_data'],
                'ai_analysis' => [
                    'column_mapping' => $aiAnalysis['column_mapping'],
                    'confidence' => $aiAnalysis['confidence'],
                    'method' => $aiAnalysis['method'] ?? 'local',
                    'warnings' => $aiAnalysis['warnings'],
                    'suggestions' => $aiAnalysis['suggestions']
                ],
                'system_fields' => $this->columnMapper->getSystemFields()
            ]);

        } catch (\Exception $e) {
            Log::error('[ExcelImport] Upload error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error al procesar el archivo: ' . $e->getMessage()
            ], 400);
        }
    }

    /**
     * Paso 2: Aplicar mapeo y obtener preview
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function preview(Request $request)
    {
        $request->validate([
            'import_id' => 'required|string',
            'column_mapping' => 'required|array'
        ]);

        $importId = $request->input('import_id');
        $columnMapping = $request->input('column_mapping');

        // Recuperar datos del cache
        $importData = cache()->get('excel_import_' . $importId);

        if (!$importData) {
            return response()->json([
                'success' => false,
                'message' => 'Sesión de importación expirada. Por favor, suba el archivo nuevamente.'
            ], 400);
        }

        try {
            // Aplicar mapeo a todos los datos
            $mappedData = $this->excelParser->applyMapping(
                $importData['data'],
                $importData['headers'],
                $columnMapping
            );

            // Buscar categorías existentes para sugerir
            $existingCategories = Category::pluck('name', 'id')->toArray();

            // Estadísticas
            $stats = $this->calculateStats($mappedData);

            // Actualizar cache con el mapeo actualizado
            $importData['column_mapping'] = $columnMapping;
            $importData['mapped_data'] = $mappedData;
            cache()->put('excel_import_' . $importId, $importData, now()->addHours(2));

            return response()->json([
                'success' => true,
                'preview_data' => $mappedData,
                'stats' => $stats,
                'existing_categories' => $existingCategories
            ]);

        } catch (\Exception $e) {
            Log::error('[ExcelImport] Preview error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error al generar preview: ' . $e->getMessage()
            ], 400);
        }
    }

    /**
     * Paso 3: Importar productos a la base de datos
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function import(Request $request)
    {
        $request->validate([
            'import_id' => 'required|string',
            'products' => 'required|array',
            'products.*.name' => 'required|string',
            'products.*.sale_price' => 'required|numeric|min:0',
        ]);

        $importId = $request->input('import_id');
        $products = $request->input('products');
        $warehouseId = $request->input('warehouse_id'); // Bodega destino opcional

        // Verificar sesión
        $importData = cache()->get('excel_import_' . $importId);

        if (!$importData) {
            return response()->json([
                'success' => false,
                'message' => 'Sesión de importación expirada.'
            ], 400);
        }

        // Si no se especifica bodega, usar la primera bodega activa disponible
        if (!$warehouseId) {
            // Buscar la primera bodega disponible (sin depender de is_main que puede no existir)
            $defaultWarehouse = \App\Models\Warehouse::first();

            // Si no hay ninguna bodega, crear una por defecto
            if (!$defaultWarehouse) {
                $defaultWarehouse = \App\Models\Warehouse::create([
                    'name' => 'Bodega Principal',
                    'description' => 'Bodega principal creada automáticamente',
                    'is_active' => true,
                ]);
            }

            $warehouseId = $defaultWarehouse->id;
        }

        $imported = 0;
        $updated = 0; // Contador de productos actualizados
        $errors = [];
        $skipped = 0;

        DB::beginTransaction();

        try {
            foreach ($products as $index => $productData) {
                try {
                    // Preparar datos del producto
                    $preparedData = $this->prepareProductData($productData);
                    $stockFromExcel = $preparedData['current_stock'] ?? 0;

                    // Verificar si ya existe (por SKU o código de barras)
                    $existingProduct = null;

                    if (!empty($preparedData['sku'])) {
                        $existingProduct = Product::where('sku', $preparedData['sku'])->first();
                    }
                    if (!$existingProduct && !empty($preparedData['barcode'])) {
                        $existingProduct = Product::where('barcode', $preparedData['barcode'])->first();
                    }

                    if ($existingProduct) {
                        // ✅ ACTUALIZAR producto existente en lugar de saltarlo
                        $previousStock = $existingProduct->current_stock ?? 0;

                        // Actualizar campos del producto (excepto el stock por ahora)
                        $updateData = [
                            'name' => $preparedData['name'],
                            'description' => $preparedData['description'] ?? $existingProduct->description,
                            'category_id' => $preparedData['category_id'] ?? $existingProduct->category_id,
                            'supplier_id' => $preparedData['supplier_id'] ?? $existingProduct->supplier_id,
                            'cost_price' => $preparedData['cost_price'] > 0 ? $preparedData['cost_price'] : $existingProduct->cost_price,
                            'sale_price' => $preparedData['sale_price'] > 0 ? $preparedData['sale_price'] : $existingProduct->sale_price,
                            'wholesale_price' => $preparedData['wholesale_price'] > 0 ? $preparedData['wholesale_price'] : $existingProduct->wholesale_price,
                            'min_stock' => $preparedData['min_stock'] ?? $existingProduct->min_stock,
                            'max_stock' => $preparedData['max_stock'] ?? $existingProduct->max_stock,
                            'unit' => $preparedData['unit'] ?? $existingProduct->unit,
                            'image_url' => $preparedData['image_url'] ?? $existingProduct->image_url,
                        ];

                        // Si el barcode viene en el Excel y el producto no tiene, actualizarlo
                        if (!empty($preparedData['barcode']) && empty($existingProduct->barcode)) {
                            $updateData['barcode'] = $preparedData['barcode'];
                        }

                        $existingProduct->update($updateData);

                        // Actualizar stock si viene en el Excel
                        if ($stockFromExcel > 0 && $warehouseId) {
                            // Actualizar current_stock del producto
                            $existingProduct->current_stock = $stockFromExcel;
                            $existingProduct->save();

                            // Actualizar o crear en la tabla pivote product_warehouse
                            $existingProduct->warehouses()->syncWithoutDetaching([
                                $warehouseId => ['stock' => $stockFromExcel]
                            ]);

                            // Solo registrar movimiento si hay diferencia de stock
                            if ($stockFromExcel != $previousStock) {
                                $movementType = $stockFromExcel > $previousStock ? 'in' : 'out';
                                $quantityDiff = abs($stockFromExcel - $previousStock);

                                \App\Models\InventoryMovement::create([
                                    'product_id' => $existingProduct->id,
                                    'warehouse_id' => $warehouseId,
                                    'type' => $movementType,
                                    'reason' => 'adjustment',
                                    'quantity' => $quantityDiff,
                                    'previous_stock' => $previousStock,
                                    'new_stock' => $stockFromExcel,
                                    'reference' => 'Actualización desde Excel',
                                    'user_id' => auth()->id() ?? 1,
                                    'movement_date' => now()
                                ]);
                            }
                        }

                        $updated++;
                        continue;
                    }

                    // Crear producto nuevo
                    $product = Product::create($preparedData);

                    // Asignar stock a la bodega si hay una configurada
                    if ($warehouseId && $stockFromExcel > 0) {
                        // Sincronizar con la tabla pivote product_warehouse
                        $product->warehouses()->attach($warehouseId, ['stock' => $stockFromExcel]);

                        // Actualizar current_stock del producto
                        $product->current_stock = $stockFromExcel;
                        $product->save();

                        // Registrar movimiento de inventario
                        \App\Models\InventoryMovement::create([
                            'product_id' => $product->id,
                            'warehouse_id' => $warehouseId,
                            'type' => 'in',
                            'reason' => 'purchase',
                            'quantity' => $stockFromExcel,
                            'previous_stock' => 0,
                            'new_stock' => $stockFromExcel,
                            'reference' => 'Importación Excel',
                            'user_id' => auth()->id() ?? 1,
                            'movement_date' => now()
                        ]);
                    }

                    $imported++;

                } catch (\Exception $e) {
                    Log::error('[ExcelImport] Error importing product: ' . $e->getMessage(), [
                        'product' => $productData['name'] ?? 'N/A',
                        'error' => $e->getMessage()
                    ]);
                    $errors[] = [
                        'row' => $index + 1,
                        'name' => $productData['name'] ?? 'N/A',
                        'error' => $e->getMessage()
                    ];
                }
            }

            DB::commit();

            // Limpiar cache y archivo temporal
            cache()->forget('excel_import_' . $importId);
            if (isset($importData['file_path']) && file_exists($importData['file_path'])) {
                @unlink($importData['file_path']);
            }


            return response()->json([
                'success' => true,
                'message' => "Importación completada",
                'stats' => [
                    'imported' => $imported,
                    'updated' => $updated,
                    'skipped' => $skipped,
                    'errors' => count($errors)
                ],
                'errors' => $errors
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('[ExcelImport] Import error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error durante la importación: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Preparar datos del producto para inserción
     */
    private function prepareProductData(array $data): array
    {
        // Manejar categoría (puede ser nombre o ID)
        $categoryId = null;
        if (!empty($data['category'])) {
            if (is_numeric($data['category'])) {
                $categoryId = (int) $data['category'];
            } else {
                // Buscar o crear categoría por nombre
                $category = Category::firstOrCreate(
                    ['name' => trim($data['category'])],
                    ['description' => 'Importado desde Excel', 'active' => true]
                );
                $categoryId = $category->id;
            }
        }

        // Si no hay categoría, usar o crear "General" por defecto
        if (!$categoryId) {
            $defaultCategory = Category::firstOrCreate(
                ['name' => 'General'],
                ['description' => 'Categoría por defecto para importaciones', 'active' => true]
            );
            $categoryId = $defaultCategory->id;
        }

        // Generar SKU automático si no viene en el Excel
        $sku = !empty($data['sku']) ? trim($data['sku']) : $this->generateAutoSku($data['name']);

        // Manejar proveedor (puede ser nombre o ID)
        $supplierId = null;
        if (!empty($data['supplier'])) {
            $supplierName = is_string($data['supplier']) ? trim($data['supplier']) : (string)$data['supplier'];

            if (is_numeric($supplierName)) {
                // Verificar si existe el proveedor con ese ID
                $existingSupplier = \App\Models\Supplier::find((int)$supplierName);
                if ($existingSupplier) {
                    $supplierId = $existingSupplier->id;
                }
            }

            // Si no es ID o no existe, buscar/crear por nombre
            if (!$supplierId && !empty($supplierName)) {
                // Buscar primero por nombre
                $supplier = \App\Models\Supplier::where('name', $supplierName)->first();

                if (!$supplier) {
                    // Generar documento único temporal (IMP-XXXXXX)
                    $tempDocument = 'IMP-' . strtoupper(substr(md5($supplierName . time()), 0, 8));

                    $supplier = \App\Models\Supplier::create([
                        'name' => $supplierName,
                        'document' => $tempDocument,
                        'contact_person' => null,
                        'email' => null,
                        'phone' => null,
                        'address' => null,
                        'active' => true,
                        'notes' => 'Importado automáticamente desde Excel - Actualizar documento real'
                    ]);
                }
                $supplierId = $supplier->id;
            }
        }

        return [
            'name' => trim($data['name']),
            'description' => $data['description'] ?? null,
            'sku' => $sku,
            'barcode' => !empty($data['barcode']) ? trim($data['barcode']) : null,
            'category_id' => $categoryId,
            'supplier_id' => $supplierId,
            'cost_price' => floatval($data['cost_price'] ?? 0),
            'sale_price' => floatval($data['sale_price']),
            'wholesale_price' => floatval($data['wholesale_price'] ?? 0),
            'current_stock' => intval($data['current_stock'] ?? 0),
            'min_stock' => intval($data['min_stock'] ?? 5),
            'max_stock' => intval($data['max_stock'] ?? 100),
            'unit' => $data['unit'] ?? 'unidad',
            'image_url' => $data['image_url'] ?? null,
            'manage_stock' => true,
            'active' => true,
        ];
    }

    /**
     * Generar SKU automático basado en el nombre del producto
     */
    private function generateAutoSku(string $productName): string
    {
        // Tomar las primeras 3 letras del nombre (mayúsculas, sin acentos)
        $prefix = strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', iconv('UTF-8', 'ASCII//TRANSLIT', $productName)), 0, 3));
        if (empty($prefix)) {
            $prefix = 'PRD';
        }

        // Agregar timestamp para hacerlo único
        $timestamp = substr(time(), -6);
        $random = rand(100, 999);

        return $prefix . '-' . $timestamp . $random;
    }

    /**
     * Calcular estadísticas del preview
     */
    private function calculateStats(array $mappedData): array
    {
        $valid = 0;
        $withWarnings = 0;
        $invalid = 0;
        $totalValue = 0;
        $totalStock = 0;

        foreach ($mappedData as $row) {
            if ($row['validation']['is_valid']) {
                if (empty($row['validation']['warnings'])) {
                    $valid++;
                } else {
                    $withWarnings++;
                }
            } else {
                $invalid++;
            }

            $price = floatval($row['mapped_data']['sale_price'] ?? 0);
            $stock = intval($row['mapped_data']['current_stock'] ?? 0);
            $totalValue += $price * $stock;
            $totalStock += $stock;
        }

        return [
            'total' => count($mappedData),
            'valid' => $valid,
            'with_warnings' => $withWarnings,
            'invalid' => $invalid,
            'total_stock_value' => round($totalValue, 2),
            'total_units' => $totalStock
        ];
    }

    /**
     * Cancelar importación y limpiar archivos temporales
     */
    public function cancel(Request $request)
    {
        $importId = $request->input('import_id');

        if ($importId) {
            $importData = cache()->get('excel_import_' . $importId);

            if ($importData && isset($importData['file_path']) && file_exists($importData['file_path'])) {
                @unlink($importData['file_path']);
            }

            cache()->forget('excel_import_' . $importId);
        }

        return response()->json([
            'success' => true,
            'message' => 'Importación cancelada'
        ]);
    }

    /**
     * Descargar plantilla de ejemplo
     */
    public function downloadTemplate()
    {
        $templatePath = resource_path('templates/plantilla_productos.xlsx');

        if (!file_exists($templatePath)) {
            // Crear plantilla básica
            $this->createTemplate($templatePath);
        }

        return response()->download($templatePath, 'plantilla_productos_105pos.xlsx');
    }

    /**
     * Crear plantilla de ejemplo
     */
    private function createTemplate(string $path)
    {
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Headers
        $headers = ['Nombre', 'Precio Venta', 'Precio Costo', 'Stock', 'Código', 'Código Barras', 'Categoría', 'Descripción', 'Stock Mínimo'];
        $sheet->fromArray($headers, null, 'A1');

        // Ejemplo de datos
        $examples = [
            ['Coca Cola 350ml', 2500, 1800, 50, 'COC001', '7701234567890', 'Bebidas', 'Refresco de cola', 10],
            ['Pan Tajado Bimbo', 5500, 4000, 20, 'PAN001', '', 'Panadería', '', 5],
            ['Leche Alquería 1L', 4200, 3200, 30, 'LEC001', '7709876543210', 'Lácteos', 'Leche entera', 15],
        ];
        $sheet->fromArray($examples, null, 'A2');

        // Estilo headers
        $sheet->getStyle('A1:I1')->getFont()->setBold(true);
        $sheet->getStyle('A1:I1')->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setRGB('4F46E5');
        $sheet->getStyle('A1:I1')->getFont()->getColor()->setRGB('FFFFFF');

        // Auto-size columns
        foreach (range('A', 'I') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Crear directorio si no existe
        $dir = dirname($path);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save($path);
    }
}