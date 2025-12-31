<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Services\ExcelParserService;
use App\Services\AIColumnMapperService;
use Illuminate\Support\Facades\Log;

/**
 * ==========================================
 * 🧪 RUTAS DE PRUEBA PARA EXCEL IMPORT AI
 * ==========================================
 *
 * Sin protección de auth para facilitar testing
 * Estas rutas NO deben estar en producción
 */

Route::prefix('test-excel')->group(function () {

    /**
     * 🔍 Prueba 1: Analizar CSV de prueba existente
     * GET /test-excel/analyze-sample?file=nombre.csv (opcional)
     */
    Route::get('/analyze-sample', function (Request $request) {
        try {
            $parser = new ExcelParserService();
            $mapper = new AIColumnMapperService();

            // Permitir especificar un archivo como parámetro
            $fileName = $request->query('file', 'import_4bMWjrttggA4Rj7FTtqo.csv');
            $testFile = storage_path("tenantlas_las/app/temp/imports/{$fileName}");

            if (!file_exists($testFile)) {
                return response()->json([
                    'success' => false,
                    'error' => 'Archivo de prueba no encontrado',
                    'path' => $testFile,
                    'hint' => 'Usa ?file=nombre.csv para especificar otro archivo'
                ], 404);
            }

            Log::info('[TEST] Iniciando análisis de archivo de prueba', ['file' => $testFile]);

            // 1. Parsear el archivo
            $parseResult = $parser->parseFile($testFile);

            Log::info('[TEST] Archivo parseado exitosamente', [
                'headers' => $parseResult['headers'],
                'total_rows' => $parseResult['total_rows']
            ]);

            // 2. Analizar con IA
            $aiAnalysis = $mapper->analyzeColumnsWithAI(
                $parseResult['headers'],
                $parseResult['sample_data']
            );

            Log::info('[TEST] Análisis de IA completado', [
                'method' => $aiAnalysis['method'],
                'confidence' => $aiAnalysis['confidence']
            ]);

            return response()->json([
                'success' => true,
                'message' => '✅ Análisis completado exitosamente',
                'file_info' => [
                    'path' => $testFile,
                    'total_rows' => $parseResult['total_rows'],
                    'headers' => $parseResult['headers']
                ],
                'ai_analysis' => [
                    'method' => $aiAnalysis['method'],
                    'confidence' => $aiAnalysis['confidence'],
                    'column_mapping' => $aiAnalysis['column_mapping'],
                    'warnings' => $aiAnalysis['warnings'],
                ],
                'sample_data' => array_slice($parseResult['sample_data'], 0, 3) // Primeras 3 filas
            ]);

        } catch (\Exception $e) {
            Log::error('[TEST] Error en análisis: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
    });

    /**
     * 🔍 Prueba 2: Subir nuevo archivo CSV para analizar
     * POST /test-excel/upload-test
     */
    Route::post('/upload-test', function (Request $request) {
        try {
            if (!$request->hasFile('file')) {
                return response()->json([
                    'success' => false,
                    'error' => 'No se recibió ningún archivo'
                ], 400);
            }

            $file = $request->file('file');
            $parser = new ExcelParserService();
            $mapper = new AIColumnMapperService();

            Log::info('[TEST] Archivo recibido', [
                'name' => $file->getClientOriginalName(),
                'size' => $file->getSize(),
                'mime' => $file->getMimeType(),
                'extension' => $file->getClientOriginalExtension()
            ]);

            // Guardar temporalmente
            $filename = 'test_' . time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('temp/test_imports', $filename);
            $fullPath = storage_path('app/' . $path);

            Log::info('[TEST] Archivo guardado', ['path' => $fullPath]);

            // Parsear
            $parseResult = $parser->parseFile($fullPath);

            Log::info('[TEST] Archivo parseado', [
                'headers' => $parseResult['headers'],
                'total_rows' => $parseResult['total_rows']
            ]);

            // Analizar con IA
            $aiAnalysis = $mapper->analyzeColumnsWithAI(
                $parseResult['headers'],
                $parseResult['sample_data']
            );

            Log::info('[TEST] Análisis IA completado', [
                'method' => $aiAnalysis['method'],
                'confidence' => $aiAnalysis['confidence']
            ]);

            return response()->json([
                'success' => true,
                'message' => '✅ Archivo procesado exitosamente',
                'file_info' => [
                    'original_name' => $file->getClientOriginalName(),
                    'saved_as' => $filename,
                    'size_kb' => round($file->getSize() / 1024, 2),
                    'total_rows' => $parseResult['total_rows'],
                    'headers' => $parseResult['headers']
                ],
                'ai_analysis' => [
                    'method' => $aiAnalysis['method'],
                    'confidence' => $aiAnalysis['confidence'],
                    'column_mapping' => $aiAnalysis['column_mapping'],
                    'warnings' => $aiAnalysis['warnings'],
                ],
                'sample_data' => array_slice($parseResult['sample_data'], 0, 5)
            ]);

        } catch (\Exception $e) {
            Log::error('[TEST] Error procesando archivo: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
    });

    /**
     * 🔍 Prueba 3: Verificar configuración de APIs
     * GET /test-excel/check-config
     */
    Route::get('/check-config', function () {
        $groqKeys = [];
        for ($i = 1; $i <= 14; $i++) {
            $key = env("GROQ_API_KEY_{$i}");
            if ($key) {
                $groqKeys["GROQ_API_KEY_{$i}"] = substr($key, 0, 15) . '...' . substr($key, -10);
            }
        }

        $geminiKey = env('GEMINI_API_KEY');

        return response()->json([
            'success' => true,
            'config' => [
                'groq_keys_configured' => count($groqKeys),
                'groq_keys' => $groqKeys,
                'gemini_configured' => !empty($geminiKey),
                'gemini_key_preview' => $geminiKey ? substr($geminiKey, 0, 20) . '...' : null
            ]
        ]);
    });

    /**
     * 🔍 Prueba 4: Ver archivos disponibles para prueba
     * GET /test-excel/list-test-files
     */
    Route::get('/list-test-files', function () {
        $dirs = [
            'tenantlas_las/app/temp/imports/',
            'app/temp/test_imports/'
        ];

        $files = [];
        foreach ($dirs as $dir) {
            $fullPath = storage_path($dir);
            if (is_dir($fullPath)) {
                $dirFiles = scandir($fullPath);
                foreach ($dirFiles as $file) {
                    if ($file !== '.' && $file !== '..') {
                        $files[] = [
                            'name' => $file,
                            'path' => $fullPath . $file,
                            'size_kb' => round(filesize($fullPath . $file) / 1024, 2),
                            'modified' => date('Y-m-d H:i:s', filemtime($fullPath . $file))
                        ];
                    }
                }
            }
        }

        return response()->json([
            'success' => true,
            'total_files' => count($files),
            'files' => $files
        ]);
    });
});
