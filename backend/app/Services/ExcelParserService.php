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
     */
    private function normalizeValue(string $field, string $value): mixed
    {
        // Campos numéricos
        $numericFields = ['sale_price', 'cost_price', 'wholesale_price', 'current_stock', 'min_stock', 'max_stock'];

        if (in_array($field, $numericFields)) {
            // Limpiar formato de moneda
            $value = preg_replace('/[^\d.,\-]/', '', $value);
            // Convertir comas a puntos
            $value = str_replace(',', '.', $value);
            // Si tiene múltiples puntos, es formato europeo (1.234.567,89)
            if (substr_count($value, '.') > 1) {
                $value = str_replace('.', '', $value);
            }

            return is_numeric($value) ? floatval($value) : 0;
        }

        // Campos booleanos
        if (in_array($field, ['active', 'manage_stock', 'allow_decimal'])) {
            $trueValues = ['si', 'sí', 'yes', '1', 'true', 'activo', 'x'];
            return in_array(strtolower(trim($value)), $trueValues);
        }

        return $value;
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
