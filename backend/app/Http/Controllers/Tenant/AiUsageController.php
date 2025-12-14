<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Services\AiUsageService;
use Illuminate\Http\JsonResponse;

class AiUsageController extends Controller
{
    protected $aiUsageService;

    public function __construct(AiUsageService $aiUsageService)
    {
        $this->aiUsageService = $aiUsageService;
    }

    /**
     * Obtener estado de uso de IA del tenant actual
     */
    public function getUsageStatus(): JsonResponse
    {
        $tenantId = tenant('id');

        if (!$tenantId) {
            return response()->json([
                'success' => false,
                'message' => 'No se pudo identificar el tenant',
            ], 403);
        }

        $stats = $this->aiUsageService->getUsageStats($tenantId);

        return response()->json([
            'success' => true,
            'data' => $stats,
        ]);
    }

    /**
     * Verificar si puede hacer una petición específica
     */
    public function checkLimit(int $estimatedTokens = 0): JsonResponse
    {
        $tenantId = tenant('id');

        if (!$tenantId) {
            return response()->json([
                'success' => false,
                'message' => 'No se pudo identificar el tenant',
            ], 403);
        }

        $check = $this->aiUsageService->canMakeRequest($tenantId, $estimatedTokens);

        return response()->json([
            'success' => true,
            'data' => $check,
        ]);
    }
}
