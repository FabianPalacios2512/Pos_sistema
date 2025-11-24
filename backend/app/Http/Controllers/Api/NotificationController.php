<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UserNotificationView;
use App\Models\InventoryMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class NotificationController extends Controller
{
    /**
     * Obtener contadores de notificaciones no vistas
     */
    public function getCounts(Request $request)
    {
        try {
            // Para pruebas, usar ID de usuario fijo si no hay autenticación
            $userId = Auth::id() ?? 1; // Usar usuario ID 1 como fallback

            // Obtener última vista de movimientos
            $movementsView = UserNotificationView::where('user_id', $userId)
                ->where('type', 'movements')
                ->first();

            // Obtener última vista de alertas
            $alertsView = UserNotificationView::where('user_id', $userId)
                ->where('type', 'alerts')
                ->first();

            // Contar movimientos nuevos (últimas 24 horas comparado con última vista)
            $movementsCount = 0;
            if ($movementsView) {
                $movementsCount = InventoryMovement::where('movement_date', '>', $movementsView->last_viewed_at)
                    ->count();
            } else {
                // Si nunca ha visto, contar movimientos de las últimas 24 horas
                $movementsCount = InventoryMovement::where('movement_date', '>', Carbon::now()->subDay())
                    ->count();
            }

            // Para alertas, usar el nuevo sistema de alertas activas
            $alertsCount = 0;
            try {
                $activeAlerts = \App\Services\AlertService::getActiveAlertsForNotifications();
                $currentAlertsCount = count($activeAlerts);

                if ($alertsView) {
                    // Si ya vio alertas antes, solo mostrar si hay más alertas que la última vez
                    $lastSeenCount = $alertsView->viewed_items['alerts_count'] ?? 0;
                    $alertsCount = max(0, $currentAlertsCount - $lastSeenCount);
                } else {
                    // Si nunca ha visto, mostrar todas las alertas actuales
                    $alertsCount = $currentAlertsCount;
                }
            } catch (\Exception $e) {
                // Si falla, asumir que hay algunas alertas
                $alertsCount = $alertsView ? 0 : 5;
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'movements_count' => $movementsCount,
                    'alerts_count' => $alertsCount,
                    'last_movements_view' => $movementsView?->last_viewed_at,
                    'last_alerts_view' => $alertsView?->last_viewed_at,
                    'user_id' => $userId // Para debug
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener contadores de notificaciones',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Marcar movimientos como vistos
     */
    public function markMovementsAsViewed(Request $request)
    {
        try {
            $userId = Auth::id() ?? 1; // Usuario fallback para pruebas

            UserNotificationView::updateOrCreate(
                [
                    'user_id' => $userId,
                    'type' => 'movements'
                ],
                [
                    'last_viewed_at' => Carbon::now(),
                    'viewed_items' => null // Podemos agregar IDs específicos si es necesario
                ]
            );

            return response()->json([
                'success' => true,
                'message' => 'Movimientos marcados como vistos',
                'user_id' => $userId,
                'timestamp' => Carbon::now()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al marcar movimientos como vistos',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Marcar alertas como vistas
     */
    public function markAlertsAsViewed(Request $request)
    {
        try {
            $userId = Auth::id() ?? 1; // Usuario fallback para pruebas

            // Obtener el count actual de alertas usando el nuevo sistema
            $currentAlertsCount = 0;
            try {
                $activeAlerts = \App\Services\AlertService::getActiveAlertsForNotifications();
                $currentAlertsCount = count($activeAlerts);
            } catch (\Exception $e) {
                // Si falla, usar 0
                $currentAlertsCount = 0;
            }

            UserNotificationView::updateOrCreate(
                [
                    'user_id' => $userId,
                    'type' => 'alerts'
                ],
                [
                    'last_viewed_at' => Carbon::now(),
                    'viewed_items' => [
                        'alerts_count' => $currentAlertsCount
                    ]
                ]
            );

            return response()->json([
                'success' => true,
                'message' => 'Alertas marcadas como vistas',
                'user_id' => $userId,
                'alerts_count_seen' => $currentAlertsCount,
                'timestamp' => Carbon::now()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al marcar alertas como vistas',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
