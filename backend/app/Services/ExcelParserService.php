<?php

namespace App\Services;

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\Csv;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ExcelParserService
{
    /**
     * Parsear archivo Excel/CSV y extraer headers + datos
     *
     * @param string $filePath Ruta al archivo
     * @return array ['headers' => [...], 'data' => [...], 'total_rows' => int]
     */
    public function parseFile(string $filePath): array
    {
        try {
            $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));

            if ($extension === 'csv') {
                return $this->parseCsv($filePath);
            }

            return $this->parseExcel($filePath);
        } catch (\Exception $e) {
            Log::error('[ExcelParser] Error parsing file: ' . $e->getMessage());
            throw new \Exception('Error al leer el archivo: ' . $e->getMessage());
        }
    }

    /**
     * Parsear archivo Excel (.xlsx, .xls)
     */
    private function parseExcel(string $filePath): array
    {
        $spreadsheet = IOFactory::load($filePath);
        $worksheet = $spreadsheet->getActiveSheet();

        $data = [];
        $headers = [];
        $rowIndex = 0;

        foreach ($worksheet->getRowIterator() as $row) {
            $cellIterator = $row->getCellIterator();
            $cellIterator->setIterateOnlyExistingCells(false);

            $rowData = [];
            foreach ($cellIterator as $cell) {
                $value = $cell->getValue();
                // Limpiar y normalizar el valor
                $rowData[] = $this->cleanValue($value);
            }

            // Primera fila = headers
            if ($rowIndex === 0) {
                $headers = array_map(function($h) {
                    return $this->cleanHeader($h);
                }, $rowData);
            } else {
                // Solo agregar filas que no estén completamente vacías
                if ($this->hasData($rowData)) {
                    $data[] = $rowData;
                }
            }

            $rowIndex++;
        }

        // Eliminar columnas vacías al final
        $headers = $this->trimEmptyColumns($headers);
        $data = array_map(function($row) use ($headers) {
            return array_slice($row, 0, count($headers));
        }, $data);

        return [
            'headers' => $headers,
            'data' => $data,
            'total_rows' => count($data),
            'sample_data' => array_slice($data, 0, 10) // Primeras 10 filas para IA
        ];
    }

    /**
     * Parsear archivo CSV
     */
    private function parseCsv(string $filePath): array
    {
        $reader = new Csv();

        // Detectar delimitador automáticamente
        $delimiter = $this->detectCsvDelimiter($filePath);
        $reader->setDelimiter($delimiter);

        // Detectar encoding
        $content = file_get_contents($filePath);
        if (!mb_check_encoding($content, 'UTF-8')) {
            $content = mb_convert_encoding($content, 'UTF-8', 'ISO-8859-1');
            file_put_contents($filePath, $content);
        }

        $spreadsheet = $reader->load($filePath);
        $worksheet = $spreadsheet->getActiveSheet();

        $data = [];
        $headers = [];
        $rowIndex = 0;

        foreach ($worksheet->getRowIterator() as $row) {
            $cellIterator = $row->getCellIterator();
            $cellIterator->setIterateOnlyExistingCells(false);

            $rowData = [];
            foreach ($cellIterator as $cell) {
                $rowData[] = $this->cleanValue($cell->getValue());
            }

            if ($rowIndex === 0) {
                $headers = array_map(function($h) {
                    return $this->cleanHeader($h);
                }, $rowData);
            } else {
                if ($this->hasData($rowData)) {
                    $data[] = $rowData;
                }
            }

            $rowIndex++;
        }

        $headers = $this->trimEmptyColumns($headers);
        $data = array_map(function($row) use ($headers) {
            return array_slice($row, 0, count($headers));
        }, $data);

        return [
            'headers' => $headers,
            'data' => $data,
            'total_rows' => count($data),
            'sample_data' => array_slice($data, 0, 10)
        ];
    }

    /**
     * Detectar delimitador de CSV
     */
    private function detectCsvDelimiter(string $filePath): string
    {
        $delimiters = [',', ';', "\t", '|'];
        $handle = fopen($filePath, 'r');
        $firstLine = fgets($handle);
        fclose($handle);

        $maxCount = 0;
        $detectedDelimiter = ',';

        foreach ($delimiters as $delimiter) {
            $count = substr_count($firstLine, $delimiter);
            if ($count > $maxCount) {
                $maxCount = $count;
                $detectedDelimiter = $delimiter;
            }
        }

        return $detectedDelimiter;
    }

    /**
     * Limpiar valor de celda
     */
    private function cleanValue($value): string
    {
        if ($value === null) {
            return '';
        }

        // Convertir a string
        $value = (string) $value;

        // Limpiar espacios
        $value = trim($value);

        // Remover caracteres especiales problemáticos
        $value = preg_replace('/[\x00-\x1F\x7F]/', '', $value);

        return $value;
    }

    /**
     * Limpiar header
     */
    private function cleanHeader($header): string
    {
        $header = $this->cleanValue($header);

        // Si está vacío, generar nombre genérico
        if (empty($header)) {
            return 'columna_' . uniqid();
        }

        return $header;
    }

    /**
     * Verificar si la fila tiene datos
     */
    private function hasData(array $row): bool
    {
        foreach ($row as $cell) {
            if (!empty(trim($cell))) {
                return true;
            }
        }
        return false;
    }

    /**
     * Eliminar columnas vacías al final
     */
    private function trimEmptyColumns(array $headers): array
    {
        while (count($headers) > 0 && empty(trim(end($headers)))) {
            array_pop($headers);
        }
        return $headers;
    }

    /**
     * Aplicar mapeo de columnas a los datos
     *
     * @param array $data Datos del Excel
     * @param array $headers Headers originales
     * @param array $mapping Mapeo de columnas ['header_original' => 'campo_sistema']
     * @return array Datos mapeados listos para importar
     */
    public function applyMapping(array $data, array $headers, array $mapping): array
    {
        $mappedData = [];

        foreach ($data as $rowIndex => $row) {
            $mappedRow = [
                'row_number' => $rowIndex + 2, // +2 porque empezamos en fila 2 (fila 1 = headers)
                'original_data' => [],
                'mapped_data' => [],
                'validation' => [
                    'is_valid' => true,
                    'errors' => [],
                    'warnings' => []
                ]
            ];

            // Guardar datos originales
            foreach ($headers as $index => $header) {
                $mappedRow['original_data'][$header] = $row[$index] ?? '';
            }

            // Aplicar mapeo
            foreach ($mapping as $originalColumn => $systemField) {
                if ($systemField === 'ignore' || empty($systemField)) {
                    continue;
                }

                $columnIndex = array_search($originalColumn, $headers);
                if ($columnIndex !== false) {
                    $value = $row[$columnIndex] ?? '';
                    $mappedRow['mapped_data'][$systemField] = $this->normalizeValue($systemField, $value);
                }
            }

            // Validar fila
            $mappedRow['validation'] = $this->validateRow($mappedRow['mapped_data']);

            $mappedData[] = $mappedRow;
        }

        return $mappedData;
    }

    /**
     * Normalizar valor según el tipo de campo
     * 
     * Soporta formatos de moneda colombiana:
     * - $2.500 = 2500
     * - 2.500 = 2500 (punto como separador de miles)
     * - 2500 = 2500
     * - 2.500,00 = 2500 (formato europeo/colombiano)
     * - 2,500.00 = 2500 (formato americano)
     */
    private function normalizeValue(string $field, string $value): mixed
    {
        // Campos numéricos
        $numericFields = ['sale_price', 'cost_price', 'wholesale_price', 'current_stock', 'min_stock', 'max_stock'];

        if (in_array($field, $numericFields)) {
            $value = $this->parseColombianCurrency($value, $field);
            return $value;
        }

        // Campos booleanos
        if (in_array($field, ['active', 'manage_stock', 'allow_decimal'])) {
            $trueValues = ['si', 'sí', 'yes', '1', 'true', 'activo', 'x'];
            return in_array(strtolower(trim($value)), $trueValues);
        }

        return $value;
    }

    /**
     * Parsear valores de moneda colombiana
     * 
     * En Colombia:
     * - Separador de miles: punto (.)
     * - Separador decimal: coma (,)
     * - Símbolo: $ (peso colombiano)
     * 
     * Ejemplos soportados:
     * - $2.500 → 2500
     * - $2.500,50 → 2500.50
     * - 2.500 → 2500
     * - 2500 → 2500
     * - 2,500.00 → 2500 (formato americano)
     * - $ 1.234.567 → 1234567
     */
    private function parseColombianCurrency(string $value, string $field = ''): float
    {
        // Si está vacío, retornar 0
        if (empty(trim($value))) {
            return 0.0;
        }

        // Guardar valor original para debug
        $original = $value;

        // 1. Remover símbolo de peso ($) y espacios
        $value = preg_replace('/[$\s]/', '', $value);

        // 2. Remover cualquier otro símbolo de moneda (€, USD, COP, etc.)
        $value = preg_replace('/[^\d.,\-]/', '', $value);

        // Si está vacío después de limpiar, retornar 0
        if (empty($value)) {
            return 0.0;
        }

        // 3. Detectar el formato basándose en la posición y cantidad de separadores
        $dotCount = substr_count($value, '.');
        $commaCount = substr_count($value, ',');

        // 4. Analizar patrones
        if ($dotCount === 0 && $commaCount === 0) {
            // Solo números: 2500
            return floatval($value);
        }

        if ($dotCount === 0 && $commaCount === 1) {
            // Formato: 2500,50 (coma decimal europeo/colombiano)
            $value = str_replace(',', '.', $value);
            return floatval($value);
        }

        if ($dotCount === 1 && $commaCount === 0) {
            // Puede ser: 2.500 (miles) o 25.50 (decimal)
            // Si el punto está seguido de exactamente 3 dígitos, es separador de miles
            if (preg_match('/\.(\d{3})$/', $value)) {
                // Es separador de miles: 2.500 → 2500
                $value = str_replace('.', '', $value);
                return floatval($value);
            } else {
                // Es decimal: 25.50 → 25.50
                return floatval($value);
            }
        }

        if ($dotCount >= 1 && $commaCount === 1) {
            // Determinar cuál es el decimal basándose en la posición
            $lastDot = strrpos($value, '.');
            $lastComma = strrpos($value, ',');

            if ($lastComma > $lastDot) {
                // Formato colombiano/europeo: 1.234.567,89
                // Puntos son miles, coma es decimal
                $value = str_replace('.', '', $value);
                $value = str_replace(',', '.', $value);
                return floatval($value);
            } else {
                // Formato americano: 1,234,567.89
                // Comas son miles, punto es decimal
                $value = str_replace(',', '', $value);
                return floatval($value);
            }
        }

        if ($dotCount >= 2 && $commaCount === 0) {
            // Formato: 1.234.567 (solo puntos como miles, sin decimales)
            $value = str_replace('.', '', $value);
            return floatval($value);
        }

        if ($commaCount >= 2 && $dotCount === 0) {
            // Formato: 1,234,567 (solo comas como miles, formato americano sin decimales)
            $value = str_replace(',', '', $value);
            return floatval($value);
        }

        if ($commaCount >= 1 && $dotCount === 1) {
            // Similar al caso anterior pero invertido
            $lastDot = strrpos($value, '.');
            $lastComma = strrpos($value, ',');

            if ($lastDot > $lastComma) {
                // Formato americano: 1,234.89
                $value = str_replace(',', '', $value);
                return floatval($value);
            } else {
                // Formato europeo: 1.234,89
                $value = str_replace('.', '', $value);
                $value = str_replace(',', '.', $value);
                return floatval($value);
            }
        }

        // Fallback: intentar limpiar todo y convertir
        $value = preg_replace('/[^\d.]/', '', str_replace(',', '.', $value));
        return floatval($value) ?: 0.0;
    }

    /**
     * Validar una fila de datos
     */
    private function validateRow(array $data): array
    {
        $validation = [
            'is_valid' => true,
            'errors' => [],
            'warnings' => []
        ];

        // Nombre es obligatorio
        if (empty($data['name'] ?? '')) {
            $validation['is_valid'] = false;
            $validation['errors'][] = 'El nombre del producto es obligatorio';
        }

        // Precio de venta es obligatorio y debe ser > 0
        $salePrice = $data['sale_price'] ?? 0;
        if ($salePrice <= 0) {
            $validation['is_valid'] = false;
            $validation['errors'][] = 'El precio de venta debe ser mayor a 0';
        }

        // Advertencias (no bloquean)
        $costPrice = $data['cost_price'] ?? 0;
        if ($costPrice <= 0) {
            $validation['warnings'][] = 'No se especificó precio de costo';
        }

        $stock = $data['current_stock'] ?? 0;
        if ($stock <= 0) {
            $validation['warnings'][] = 'Stock en 0 o no especificado';
        }

        // Margen negativo
        if ($costPrice > 0 && $salePrice > 0 && $costPrice >= $salePrice) {
            $validation['warnings'][] = 'El costo es mayor o igual al precio de venta';
        }

        return $validation;
    }
}
