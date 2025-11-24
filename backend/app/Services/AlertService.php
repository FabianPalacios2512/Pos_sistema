<?php

namespace App\Services;

use App\Models\ActiveAlert;
use App\Models\Product;
use App\Models\UserNotificationView;
use Illuminate\Support\Facades\DB;

class AlertService
{
    /**
     * Actualizar alertas automáticamente cuando cambia el stock de un producto
     */
    public static function updateProductAlerts($productId)
    {
        $product = Product::find($productId);

        if (!$product) {
            return;
        }

        // Lógica de alertas
        $stockLowKey = "stock_low_{$productId}";
        $stockOutKey = "stock_out_{$productId}";

        // 1. Verificar stock agotado (critical)
        if ($product->current_stock <= 0 && $product->min_stock > 0) {
            // Crear/activar alerta de stock agotado
            ActiveAlert::createOrUpdate(
                $stockOutKey,
                $productId,
                'stock_out',
                'critical',
                "El producto '{$product->name}' está sin stock",
                [
                    'current_stock' => $product->current_stock,
                    'min_stock' => $product->min_stock
                ]
            );

            // Desactivar alerta de stock bajo si existe
            ActiveAlert::deactivate($stockLowKey);

        }
        // 2. Verificar stock bajo (warning)
        else if ($product->current_stock <= $product->min_stock && $product->min_stock > 0 && $product->current_stock > 0) {
            // Crear/activar alerta de stock bajo
            ActiveAlert::createOrUpdate(
                $stockLowKey,
                $productId,
                'stock_low',
                'warning',
                "El producto '{$product->name}' tiene stock bajo ({$product->current_stock} unidades)",
                [
                    'current_stock' => $product->current_stock,
                    'min_stock' => $product->min_stock
                ]
            );

            // Desactivar alerta de stock agotado si existe
            ActiveAlert::deactivate($stockOutKey);
        }
        // 3. Stock normal - desactivar ambas alertas
        else {
            ActiveAlert::deactivate($stockLowKey);
            ActiveAlert::deactivate($stockOutKey);
        }

        // 4. Invalidar las vistas de notificaciones para forzar recálculo
        self::invalidateNotificationViews();
    }

    /**
     * Invalidar las vistas de notificaciones para que se recalculen
     */
    private static function invalidateNotificationViews()
    {
        // Actualizar la fecha de viewed_items para forzar recálculo
        UserNotificationView::where('type', 'alerts')
            ->update(['updated_at' => now()]);
    }

    /**
     * Obtener alertas activas para las notificaciones
     */
    public static function getActiveAlertsForNotifications()
    {
        return ActiveAlert::getActive()->map(function($alert) {
            return [
                'id' => $alert->alert_key,
                'alert_key' => $alert->alert_key,
                'title' => $alert->alert_type === 'stock_out' ? 'Producto agotado' : 'Stock bajo',
                'message' => $alert->message,
                'severity' => $alert->severity,
                'category' => 'stock',
                'product_id' => $alert->product_id,
                'product_name' => $alert->product->name ?? 'Producto no encontrado',
                'current_stock' => $alert->metadata['current_stock'] ?? 0,
                'min_stock' => $alert->metadata['min_stock'] ?? 0,
                'created_at' => $alert->created_at->toISOString(),
                'action_url' => "/products/{$alert->product_id}/restock",
                'action_text' => 'Reabastecer'
            ];
        })->toArray();
    }
}
