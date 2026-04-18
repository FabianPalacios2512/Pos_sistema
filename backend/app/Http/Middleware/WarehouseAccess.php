<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Warehouse;

class WarehouseAccess
{
    /**
     * Middleware de control de acceso por sede.
     * 
     * Valida que un usuario no-admin solo pueda interactuar
     * con recursos de su sede asignada (warehouse_id).
     * Admins tienen acceso sin restricción.
     */
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'No autenticado',
            ], 401);
        }

        // Solo aplica restricción si hay más de 1 sede activa
        $totalWarehouses = Warehouse::where('active', true)->count();
        if ($totalWarehouses <= 1) {
            return $next($request);
        }

        // Admins pasan sin restricción
        $user->loadMissing('role');
        $roleName = strtolower($user->role->name ?? '');
        if (in_array($roleName, ['administrador', 'admin', 'superadmin'])) {
            return $next($request);
        }

        // Usuario sin sede asignada = bloqueado
        if (!$user->warehouse_id) {
            return response()->json([
                'success' => false,
                'code' => 'NO_WAREHOUSE_ASSIGNED',
                'message' => 'No tienes una sede asignada. Contacta a tu administrador.',
            ], 403);
        }

        // Verificar warehouse_id en request (body o query)
        $requestedWarehouseId = $request->input('warehouse_id')
            ?? $request->route('warehouseId')
            ?? $request->query('warehouse_id');

        if ($requestedWarehouseId && (int)$requestedWarehouseId !== (int)$user->warehouse_id) {
            $userWarehouse = Warehouse::find($user->warehouse_id);
            return response()->json([
                'success' => false,
                'code' => 'WAREHOUSE_ACCESS_DENIED',
                'message' => "Acceso restringido. Estás asignado a \"{$userWarehouse->name}\". No tienes permisos para operar en otras sedes.",
                'user_warehouse' => [
                    'id' => $user->warehouse_id,
                    'name' => $userWarehouse->name ?? 'Sede desconocida',
                ],
            ], 403);
        }

        return $next($request);
    }
}
