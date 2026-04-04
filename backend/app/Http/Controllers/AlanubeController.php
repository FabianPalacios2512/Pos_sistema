<?php

namespace App\Http\Controllers;

use App\Services\AlanubeService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

/**
 * Controlador para gestión de Facturación Electrónica con Alanube
 */
class AlanubeController extends Controller
{
    /**
     * ═══════════════════════════════════════════════════════════════════
     * Obtener estado actual de facturación electrónica del tenant
     * ═══════════════════════════════════════════════════════════════════
     */
    public function getStatus(): JsonResponse
    {
        try {
            $settings = DB::table('system_settings')->first();

            return response()->json([
                'success' => true,
                'data' => [
                    'provider' => $settings->electronic_invoice_provider ?? 'none',
                    'alanube' => [
                        'company_id' => $settings->alanube_company_id ?? null,
                        'status' => $settings->alanube_status ?? 'pending',
                        'test_set_id' => $settings->alanube_test_set_id ?? null
                    ],
                    'company' => [
                        'name' => $settings->company_name ?? null,
                        'nit' => $settings->company_document ?? null,
                        'dv' => $settings->company_dv ?? null,
                        'address' => $settings->company_address ?? null,
                        'city_code' => $settings->company_city_code ?? null,
                        'department_code' => $settings->company_department_code ?? null,
                        'email' => $settings->company_email ?? null,
                        'phone' => $settings->company_phone ?? null,
                        'tax_regime' => $settings->tax_regime ?? 'R-99-PN'
                    ],
                    'dian_resolution' => [
                        'number' => $settings->dian_resolution_number ?? null,
                        'prefix' => $settings->dian_prefix ?? null,
                        'min_number' => $settings->dian_min_number ?? null,
                        'max_number' => $settings->dian_max_number ?? null,
                        'current_number' => $settings->dian_current_number ?? null,
                        'start_date' => $settings->dian_start_date ?? null,
                        'end_date' => $settings->dian_end_date ?? null
                    ]
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error obteniendo estado de facturación electrónica', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al obtener estado'
            ], 500);
        }
    }

    /**
     * ═══════════════════════════════════════════════════════════════════
     * Guardar datos fiscales del comercio
     * ═══════════════════════════════════════════════════════════════════
     */
    public function saveFiscalData(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'company_name' => 'required|string|max:255',
            'company_document' => 'required|string|max:20', // NIT sin DV
            'company_dv' => 'required|string|size:1',
            'company_address' => 'required|string|max:255',
            'company_city_code' => 'required|string|max:10',
            'company_department_code' => 'required|string|max:5',
            'company_email' => 'required|email|max:255',
            'company_phone' => 'nullable|string|max:20',
            'tax_regime' => 'nullable|string|max:20'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Datos inválidos',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::table('system_settings')
                ->update([
                    'company_name' => $request->company_name,
                    'company_document' => $request->company_document,
                    'company_dv' => $request->company_dv,
                    'company_address' => $request->company_address,
                    'company_city_code' => $request->company_city_code,
                    'company_department_code' => $request->company_department_code,
                    'company_email' => $request->company_email,
                    'company_phone' => $request->company_phone,
                    'tax_regime' => $request->tax_regime ?? 'R-99-PN'
                ]);

            Log::info('✅ Datos fiscales guardados', [
                'nit' => $request->company_document,
                'tenant' => tenant('id')
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Datos fiscales guardados correctamente'
            ]);
        } catch (\Exception $e) {
            Log::error('Error guardando datos fiscales', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al guardar datos fiscales'
            ], 500);
        }
    }

    /**
     * ═══════════════════════════════════════════════════════════════════
     * Registrar empresa en Alanube
     * ═══════════════════════════════════════════════════════════════════
     */
    public function registerCompany(Request $request): JsonResponse
    {
        try {
            $settings = DB::table('system_settings')->first();

            // Verificar que tenga datos fiscales completos
            if (empty($settings->company_document) || empty($settings->company_name)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Primero debes guardar los datos fiscales de tu empresa'
                ], 400);
            }

            $alanube = AlanubeService::create();

            if (!$alanube->isConfigured()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Alanube no está configurado en el servidor'
                ], 500);
            }

            // Crear empresa en Alanube
            $result = $alanube->createCompany([
                'name' => $settings->company_name,
                'nit' => $settings->company_document,
                'dv' => $settings->company_dv ?? '0'
            ]);

            if ($result['success'] && isset($result['data']['company']['id'])) {
                $companyId = $result['data']['company']['id'];

                // Guardar company_id en BD
                DB::table('system_settings')->update([
                    'alanube_company_id' => $companyId,
                    'alanube_status' => 'testing'
                ]);

                Log::info('✅ Empresa registrada en Alanube', [
                    'company_id' => $companyId,
                    'tenant' => tenant('id')
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Empresa registrada en Alanube',
                    'data' => [
                        'company_id' => $companyId
                    ]
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al registrar empresa en Alanube',
                    'errors' => $result['data']['errors'] ?? []
                ], 400);
            }
        } catch (\Exception $e) {
            Log::error('Error registrando empresa en Alanube', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al registrar empresa: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * ═══════════════════════════════════════════════════════════════════
     * Ejecutar Set de Pruebas DIAN
     * ═══════════════════════════════════════════════════════════════════
     */
    public function runTestSet(): JsonResponse
    {
        try {
            $settings = DB::table('system_settings')->first();

            if (empty($settings->alanube_company_id)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Primero debes registrar tu empresa en Alanube'
                ], 400);
            }

            $alanube = AlanubeService::create();
            $result = $alanube->runTestSet($settings->alanube_company_id, 'invoices');

            if ($result['success'] && isset($result['data']['testSet'])) {
                $testSet = $result['data']['testSet'];
                $status = $testSet['status'] ?? 'unknown';

                // Actualizar estado
                DB::table('system_settings')->update([
                    'alanube_test_set_id' => $testSet['id'] ?? null,
                    'alanube_status' => $status === 'ACCEPTED' ? 'active' : 'testing'
                ]);

                Log::info('✅ Set de pruebas completado', [
                    'status' => $status,
                    'tenant' => tenant('id')
                ]);

                return response()->json([
                    'success' => true,
                    'message' => $status === 'ACCEPTED' 
                        ? '¡Empresa habilitada para facturación electrónica!' 
                        : 'Set de pruebas en proceso',
                    'data' => [
                        'test_set_id' => $testSet['id'] ?? null,
                        'status' => $status,
                        'errors' => $testSet['errors'] ?? []
                    ]
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Error en set de pruebas DIAN',
                    'errors' => $result['data']['errors'] ?? []
                ], 400);
            }
        } catch (\Exception $e) {
            Log::error('Error en set de pruebas DIAN', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error en set de pruebas: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * ═══════════════════════════════════════════════════════════════════
     * Activar/Desactivar proveedor de facturación electrónica
     * ═══════════════════════════════════════════════════════════════════
     */
    public function setProvider(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'provider' => 'required|in:none,factus,alanube'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $provider = $request->provider;
            $settings = DB::table('system_settings')->first();

            // Validar que Alanube esté configurado si se quiere activar
            if ($provider === 'alanube') {
                if (empty($settings->alanube_company_id)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Primero debes registrar tu empresa en Alanube'
                    ], 400);
                }
                if ($settings->alanube_status !== 'active') {
                    return response()->json([
                        'success' => false,
                        'message' => 'Tu empresa aún no está habilitada. Ejecuta el set de pruebas DIAN.'
                    ], 400);
                }
            }

            DB::table('system_settings')->update([
                'electronic_invoice_provider' => $provider
            ]);

            Log::info('✅ Proveedor de facturación electrónica actualizado', [
                'provider' => $provider,
                'tenant' => tenant('id')
            ]);

            return response()->json([
                'success' => true,
                'message' => $provider === 'none' 
                    ? 'Facturación electrónica desactivada' 
                    : "Proveedor cambiado a {$provider}"
            ]);
        } catch (\Exception $e) {
            Log::error('Error cambiando proveedor', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al cambiar proveedor'
            ], 500);
        }
    }

    /**
     * ═══════════════════════════════════════════════════════════════════
     * Guardar datos de resolución DIAN (para producción)
     * ═══════════════════════════════════════════════════════════════════
     */
    public function saveResolution(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'resolution_number' => 'required|string|max:50',
            'prefix' => 'required|string|max:10',
            'min_number' => 'required|integer|min:1',
            'max_number' => 'required|integer|gt:min_number',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'technical_key' => 'nullable|string|max:100'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::table('system_settings')->update([
                'dian_resolution_number' => $request->resolution_number,
                'dian_prefix' => $request->prefix,
                'dian_min_number' => $request->min_number,
                'dian_max_number' => $request->max_number,
                'dian_current_number' => $request->min_number, // Iniciar en el mínimo
                'dian_start_date' => $request->start_date,
                'dian_end_date' => $request->end_date,
                'dian_technical_key' => $request->technical_key
            ]);

            Log::info('✅ Resolución DIAN guardada', [
                'resolution' => $request->resolution_number,
                'prefix' => $request->prefix,
                'tenant' => tenant('id')
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Resolución DIAN guardada correctamente'
            ]);
        } catch (\Exception $e) {
            Log::error('Error guardando resolución DIAN', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al guardar resolución'
            ], 500);
        }
    }

    /**
     * ═══════════════════════════════════════════════════════════════════
     * Obtener ciudades de Colombia (para selector)
     * ═══════════════════════════════════════════════════════════════════
     */
    public function getCities(): JsonResponse
    {
        // Ciudades principales de Colombia con códigos DANE
        $cities = [
            ['code' => '11001', 'name' => 'Bogotá D.C.', 'department' => '11'],
            ['code' => '05001', 'name' => 'Medellín', 'department' => '05'],
            ['code' => '76001', 'name' => 'Cali', 'department' => '76'],
            ['code' => '08001', 'name' => 'Barranquilla', 'department' => '08'],
            ['code' => '13001', 'name' => 'Cartagena', 'department' => '13'],
            ['code' => '68001', 'name' => 'Bucaramanga', 'department' => '68'],
            ['code' => '50001', 'name' => 'Villavicencio', 'department' => '50'],
            ['code' => '54001', 'name' => 'Cúcuta', 'department' => '54'],
            ['code' => '25754', 'name' => 'Soacha', 'department' => '25'],
            ['code' => '66001', 'name' => 'Pereira', 'department' => '66'],
            ['code' => '17001', 'name' => 'Manizales', 'department' => '17'],
            ['code' => '41001', 'name' => 'Neiva', 'department' => '41'],
            ['code' => '73001', 'name' => 'Ibagué', 'department' => '73'],
            ['code' => '15001', 'name' => 'Tunja', 'department' => '15'],
            ['code' => '23001', 'name' => 'Montería', 'department' => '23'],
            ['code' => '52001', 'name' => 'Pasto', 'department' => '52'],
            ['code' => '63001', 'name' => 'Armenia', 'department' => '63'],
            ['code' => '47001', 'name' => 'Santa Marta', 'department' => '47'],
            ['code' => '19001', 'name' => 'Popayán', 'department' => '19'],
            ['code' => '44001', 'name' => 'Riohacha', 'department' => '44'],
        ];

        return response()->json([
            'success' => true,
            'data' => $cities
        ]);
    }
}
