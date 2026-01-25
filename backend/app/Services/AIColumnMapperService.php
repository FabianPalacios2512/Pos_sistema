<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class AIColumnMapperService
{
    /**
     * Campos disponibles en el sistema de productos
     */
    private array $systemFields = [
        'name' => [
            'label' => 'Nombre del producto',
            'required' => true,
            'type' => 'string',
            'synonyms' => ['nombre', 'producto', 'descripcion', 'item', 'articulo', 'name', 'product', 'description', 'detalle', 'referencia']
        ],
        'sale_price' => [
            'label' => 'Precio de venta',
            'required' => true,
            'type' => 'number',
            'synonyms' => ['precio', 'venta', 'pvp', 'price', 'valor', 'precio_venta', 'precio venta', 'p.v.p', 'precio unitario', 'precio unit']
        ],
        'cost_price' => [
            'label' => 'Precio de costo',
            'required' => false,
            'type' => 'number',
            'synonyms' => ['costo', 'compra', 'cost', 'precio_compra', 'precio compra', 'costo unitario', 'costo unit', 'precio costo']
        ],
        'current_stock' => [
            'label' => 'Stock actual',
            'required' => false,
            'type' => 'number',
            'synonyms' => ['stock', 'cantidad', 'existencia', 'qty', 'inventario', 'cant', 'unidades', 'existencias', 'disponible', 'und']
        ],
        'sku' => [
            'label' => 'Código SKU',
            'required' => false,
            'type' => 'string',
            'synonyms' => ['sku', 'codigo', 'code', 'referencia', 'ref', 'codigo producto', 'cod', 'código']
        ],
        'barcode' => [
            'label' => 'Código de barras',
            'required' => false,
            'type' => 'string',
            'synonyms' => ['barcode', 'codigo_barras', 'codigo barras', 'ean', 'upc', 'cod barras', 'código de barras']
        ],
        'category' => [
            'label' => 'Categoría',
            'required' => false,
            'type' => 'string',
            'synonyms' => ['categoria', 'category', 'tipo', 'linea', 'familia', 'grupo', 'clasificacion', 'línea']
        ],
        'description' => [
            'label' => 'Descripción adicional',
            'required' => false,
            'type' => 'string',
            'synonyms' => ['descripcion_adicional', 'notas', 'observaciones', 'detalle', 'comentarios', 'info']
        ],
        'min_stock' => [
            'label' => 'Stock mínimo',
            'required' => false,
            'type' => 'number',
            'synonyms' => ['stock_minimo', 'stock minimo', 'minimo', 'min', 'alerta']
        ],
        'wholesale_price' => [
            'label' => 'Precio mayorista',
            'required' => false,
            'type' => 'number',
            'synonyms' => ['mayorista', 'wholesale', 'precio_mayorista', 'precio mayorista', 'por mayor']
        ],
        'unit' => [
            'label' => 'Unidad de medida',
            'required' => false,
            'type' => 'string',
            'synonyms' => ['unidad', 'unit', 'medida', 'um', 'u.m.', 'unidad medida']
        ],
        'supplier' => [
            'label' => 'Proveedor',
            'required' => false,
            'type' => 'string',
            'synonyms' => ['proveedor', 'supplier', 'vendor', 'distribuidor', 'fabricante', 'marca', 'prov', 'prv', 'suplidor', 'abastecedor']
        ],
        'brand' => [
            'label' => 'Marca',
            'required' => false,
            'type' => 'string',
            'synonyms' => ['marca', 'brand', 'fabricante', 'manufacturer', 'lab', 'laboratorio']
        ],
        'image_url' => [
            'label' => 'URL de imagen',
            'required' => false,
            'type' => 'string',
            'synonyms' => ['imagen', 'image', 'foto', 'photo', 'url_imagen', 'image_url', 'picture', 'img']
        ]
    ];

    /**
     * API Keys de Groq (rotación)
     */
    private function getApiKeys(): array
    {
        // Usar config() en lugar de env() para compatibilidad con config cacheado
        return array_filter([
            config('services.groq.api_key_1'),
            config('services.groq.api_key_2'),
            config('services.groq.api_key_3'),
            config('services.groq.api_key_4'),
            config('services.groq.api_key_5'),
            config('services.groq.api_key_6'),
            config('services.groq.api_key_7'),
            config('services.groq.api_key_8'),
            config('services.groq.api_key_9'),
            config('services.groq.api_key_10'),
        ]);
    }

    /**
     * Analizar columnas con IA (Groq)
     *
     * @param array $headers Headers del Excel
     * @param array $sampleData Muestra de datos (5-10 filas)
     * @return array Mapeo sugerido
     */
    public function analyzeColumnsWithAI(array $headers, array $sampleData): array
    {
        // Primero intentar mapeo local (rápido y gratis)
        $localMapping = $this->tryLocalMapping($headers, $sampleData);

        // Si el mapeo local tiene alta confianza (>80%), usarlo
        if ($localMapping['confidence'] >= 80) {
            Log::info('[AIColumnMapper] Using local mapping with confidence: ' . $localMapping['confidence']);
            return $localMapping;
        }

        // Si no, usar Groq para análisis más inteligente
        Log::info('[AIColumnMapper] Local mapping confidence low, using Groq AI');
        return $this->analyzeWithGroq($headers, $sampleData, $localMapping);
    }

    /**
     * Intentar mapeo local con fuzzy matching
     */
    private function tryLocalMapping(array $headers, array $sampleData): array
    {
        $mapping = [];
        $mappedFields = [];
        $totalConfidence = 0;
        $mappedCount = 0;

        foreach ($headers as $header) {
            $headerLower = $this->normalizeString($header);
            $bestMatch = null;
            $bestScore = 0;

            foreach ($this->systemFields as $field => $config) {
                // No mapear el mismo campo dos veces
                if (in_array($field, $mappedFields)) {
                    continue;
                }

                // Buscar en sinónimos
                foreach ($config['synonyms'] as $synonym) {
                    $score = $this->calculateSimilarity($headerLower, $this->normalizeString($synonym));

                    if ($score > $bestScore && $score > 0.6) {
                        $bestScore = $score;
                        $bestMatch = $field;
                    }
                }
            }

            if ($bestMatch) {
                $mapping[$header] = $bestMatch;
                $mappedFields[] = $bestMatch;
                $totalConfidence += $bestScore * 100;
                $mappedCount++;
            } else {
                $mapping[$header] = 'ignore';
            }
        }

        // Calcular confianza promedio
        $avgConfidence = $mappedCount > 0 ? $totalConfidence / $mappedCount : 0;

        // Penalizar si faltan campos obligatorios
        $hasName = in_array('name', $mappedFields);
        $hasPrice = in_array('sale_price', $mappedFields);

        if (!$hasName) $avgConfidence -= 30;
        if (!$hasPrice) $avgConfidence -= 30;

        return [
            'column_mapping' => $mapping,
            'confidence' => max(0, round($avgConfidence)),
            'method' => 'local_fuzzy',
            'warnings' => $this->generateWarnings($mappedFields),
            'suggestions' => $this->generateSuggestions($headers, $mapping)
        ];
    }

    /**
     * Analizar con Groq AI
     */
    private function analyzeWithGroq(array $headers, array $sampleData, array $localMapping): array
    {
        $apiKeys = $this->getApiKeys();

        if (empty($apiKeys)) {
            Log::warning('[AIColumnMapper] No Groq API keys configured, using local mapping');
            return $localMapping;
        }

        // Preparar prompt
        $prompt = $this->buildPrompt($headers, $sampleData);

        foreach ($apiKeys as $index => $apiKey) {
            try {
                $response = Http::timeout(30)->withHeaders([
                    'Authorization' => 'Bearer ' . $apiKey,
                    'Content-Type' => 'application/json',
                ])->post('https://api.groq.com/openai/v1/chat/completions', [
                    'model' => 'llama-3.3-70b-versatile',
                    'messages' => [
                        [
                            'role' => 'system',
                            'content' => 'Eres un experto en análisis de datos de inventario y productos. Tu tarea es analizar headers de archivos Excel y mapearlos a campos de un sistema de inventario. SIEMPRE responde ÚNICAMENTE con JSON válido, sin texto adicional ni explicaciones.'
                        ],
                        [
                            'role' => 'user',
                            'content' => $prompt
                        ]
                    ],
                    'temperature' => 0.1,
                    'max_tokens' => 1000,
                ]);

                if ($response->successful()) {
                    $responseData = $response->json();
                    $content = $responseData['choices'][0]['message']['content'] ?? '';

                    Log::info('[AIColumnMapper] Groq response: ' . $content);

                    // Parsear JSON de la respuesta
                    $aiResult = $this->parseAIResponse($content);

                    if ($aiResult) {
                        $aiResult['method'] = 'groq_ai';
                        return $aiResult;
                    }
                }
            } catch (\Exception $e) {
                Log::error('[AIColumnMapper] Groq API error with key ' . ($index + 1) . ': ' . $e->getMessage());
                continue;
            }
        }

        // Si Groq falla, retornar mapeo local
        Log::warning('[AIColumnMapper] All Groq keys failed, using local mapping');
        return $localMapping;
    }

    /**
     * Construir prompt para Groq
     */
    private function buildPrompt(array $headers, array $sampleData): string
    {
        $fieldsDescription = [];
        foreach ($this->systemFields as $field => $config) {
            $required = $config['required'] ? '(OBLIGATORIO)' : '(opcional)';
            $fieldsDescription[] = "- {$field}: {$config['label']} {$required}";
        }

        $sampleRows = array_slice($sampleData, 0, 5);
        $sampleFormatted = [];
        foreach ($sampleRows as $index => $row) {
            $sampleFormatted[] = "Fila " . ($index + 1) . ": " . json_encode($row, JSON_UNESCAPED_UNICODE);
        }

        return <<<PROMPT
Analiza estos headers y datos de un archivo Excel de inventario de una tienda.

HEADERS DEL ARCHIVO:
{$this->formatArray($headers)}

MUESTRA DE DATOS (primeras 5 filas):
{$this->formatSampleRows($sampleRows, $headers)}

CAMPOS DISPONIBLES EN EL SISTEMA:
{$this->formatArray($fieldsDescription)}

INSTRUCCIONES:
1. Analiza cada header y determina a qué campo del sistema corresponde
2. Si un header no corresponde a ningún campo, usa "ignore"
3. Los campos "name" y "sale_price" son OBLIGATORIOS
4. Considera variaciones en español e inglés, abreviaciones, etc.

Responde ÚNICAMENTE con este JSON (sin texto adicional):
{
    "column_mapping": {
        "HEADER_ORIGINAL": "campo_sistema_o_ignore"
    },
    "confidence": 0-100,
    "warnings": ["lista de advertencias si faltan campos importantes"],
    "suggestions": ["sugerencias para el usuario"]
}
PROMPT;
    }

    /**
     * Parsear respuesta de IA
     */
    private function parseAIResponse(string $content): ?array
    {
        // Limpiar respuesta (puede venir con ```json ... ```)
        $content = preg_replace('/```json\s*/', '', $content);
        $content = preg_replace('/```\s*/', '', $content);
        $content = trim($content);

        try {
            $data = json_decode($content, true, 512, JSON_THROW_ON_ERROR);

            if (isset($data['column_mapping'])) {
                return [
                    'column_mapping' => $data['column_mapping'],
                    'confidence' => $data['confidence'] ?? 85,
                    'warnings' => $data['warnings'] ?? [],
                    'suggestions' => $data['suggestions'] ?? []
                ];
            }
        } catch (\Exception $e) {
            Log::error('[AIColumnMapper] Failed to parse AI response: ' . $e->getMessage());
        }

        return null;
    }

    /**
     * Calcular similitud entre dos strings (Levenshtein normalizado)
     */
    private function calculateSimilarity(string $str1, string $str2): float
    {
        // Si son iguales
        if ($str1 === $str2) return 1.0;

        // Si uno contiene al otro
        if (str_contains($str1, $str2) || str_contains($str2, $str1)) {
            return 0.9;
        }

        // Levenshtein normalizado
        $maxLen = max(strlen($str1), strlen($str2));
        if ($maxLen === 0) return 0;

        $distance = levenshtein($str1, $str2);
        return 1 - ($distance / $maxLen);
    }

    /**
     * Normalizar string para comparación
     */
    private function normalizeString(string $str): string
    {
        // Minúsculas
        $str = mb_strtolower($str);
        // Remover acentos
        $str = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $str);
        // Remover caracteres especiales
        $str = preg_replace('/[^a-z0-9\s]/', '', $str);
        // Normalizar espacios
        $str = preg_replace('/\s+/', ' ', trim($str));

        return $str;
    }

    /**
     * Generar advertencias
     */
    private function generateWarnings(array $mappedFields): array
    {
        $warnings = [];

        if (!in_array('name', $mappedFields)) {
            $warnings[] = 'No se detectó una columna para el nombre del producto (OBLIGATORIO)';
        }

        if (!in_array('sale_price', $mappedFields)) {
            $warnings[] = 'No se detectó una columna para el precio de venta (OBLIGATORIO)';
        }

        if (!in_array('cost_price', $mappedFields)) {
            $warnings[] = 'No se detectó precio de costo - se establecerá en 0';
        }

        if (!in_array('current_stock', $mappedFields)) {
            $warnings[] = 'No se detectó columna de stock - se establecerá en 0';
        }

        return $warnings;
    }

    /**
     * Generar sugerencias
     */
    private function generateSuggestions(array $headers, array $mapping): array
    {
        $suggestions = [];

        $ignoredColumns = array_filter($mapping, fn($v) => $v === 'ignore');
        if (count($ignoredColumns) > 0) {
            $suggestions[] = 'Hay ' . count($ignoredColumns) . ' columna(s) que no se mapearon. Revisa si contienen información útil.';
        }

        return $suggestions;
    }

    /**
     * Formatear array para prompt
     */
    private function formatArray(array $arr): string
    {
        return implode("\n", array_map(function($item, $key) {
            if (is_numeric($key)) {
                return "- {$item}";
            }
            return "- {$key}: {$item}";
        }, $arr, array_keys($arr)));
    }

    /**
     * Formatear filas de muestra
     */
    private function formatSampleRows(array $rows, array $headers): string
    {
        $formatted = [];
        foreach ($rows as $index => $row) {
            $rowStr = "Fila " . ($index + 1) . ": ";
            $pairs = [];
            foreach ($headers as $i => $header) {
                $value = $row[$i] ?? '';
                if (!empty($value)) {
                    $pairs[] = "{$header}=\"{$value}\"";
                }
            }
            $rowStr .= implode(", ", $pairs);
            $formatted[] = $rowStr;
        }
        return implode("\n", $formatted);
    }

    /**
     * Obtener definición de campos del sistema
     */
    public function getSystemFields(): array
    {
        return $this->systemFields;
    }
}
