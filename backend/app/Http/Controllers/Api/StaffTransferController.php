<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CashSession;
use App\Models\StaffTransfer;
use App\Models\User;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StaffTransferController extends Controller
{
    /**
     * Listar historial de traslados de personal
     */
    public function index(Request $request)
    {
        $query = StaffTransfer::with([
            'user:id,name,email',
            'fromWarehouse:id,name',
            'toWarehouse:id,name',
            'transferredBy:id,name',
        ])->orderBy('created_at', 'desc');

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('warehouse_id')) {
            $query->where(function ($q) use ($request) {
                $q->where('from_warehouse_id', $request->warehouse_id)
                  ->orWhere('to_warehouse_id', $request->warehouse_id);
            });
        }

        $transfers = $query->paginate($request->get('per_page', 20));

        return response()->json([
            'success' => true,
            'data' => $transfers,
        ]);
    }

    /**
     * Obtener empleados trasladables (con sede asignada o sin sede)
     */
    public function transferableUsers()
    {
        $users = User::with(['role:id,name', 'warehouse:id,name'])
            ->where('active', true)
            ->whereHas('role', function ($q) {
                $q->whereIn(DB::raw('LOWER(name)'), ['cajero', 'vendedor', 'administrador de sede', 'almacenista']);
            })
            ->select('id', 'name', 'email', 'role_id', 'warehouse_id')
            ->orderBy('name')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $users,
        ]);
    }

    /**
     * Ejecutar traslado de personal entre sedes
     */
    public function transfer(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'to_warehouse_id' => 'required|exists:warehouses,id',
            'reason' => 'nullable|string|max:255',
        ]);

        $targetUser = User::with('role')->findOrFail($request->user_id);
        $toWarehouse = Warehouse::where('active', true)->findOrFail($request->to_warehouse_id);

        // No se puede trasladar a la misma sede
        if ($targetUser->warehouse_id && (int)$targetUser->warehouse_id === (int)$toWarehouse->id) {
            return response()->json([
                'success' => false,
                'message' => "El empleado ya está asignado a \"{$toWarehouse->name}\".",
            ], 422);
        }

        // No se puede trasladar admins globales
        $roleName = strtolower($targetUser->role->name ?? '');
        if (in_array($roleName, ['administrador', 'admin', 'superadmin'])) {
            return response()->json([
                'success' => false,
                'message' => 'Los administradores globales no requieren asignación de sede.',
            ], 422);
        }

        $fromWarehouseId = $targetUser->warehouse_id;
        $fromWarehouseName = $fromWarehouseId
            ? (Warehouse::find($fromWarehouseId)->name ?? 'Sin sede')
            : 'Sin sede';

        $closedSessionId = null;

        DB::beginTransaction();
        try {
            // 1. Cerrar sesión de caja abierta en la sede anterior (seguridad)
            $openSession = CashSession::where('user_id', $targetUser->id)
                ->where('status', 'open')
                ->first();

            if ($openSession) {
                $openSession->updateSalesTotals();
                $openSession->calculateExpectedAmount();
                $openSession->status = CashSession::STATUS_FORCED_CLOSED;
                $openSession->closed_at = now();
                $openSession->closing_notes = "Cierre automático por traslado a {$toWarehouse->name}";
                $openSession->save();
                $closedSessionId = $openSession->id;
            }

            // 2. Actualizar sede del usuario
            $targetUser->warehouse_id = $toWarehouse->id;
            $targetUser->save();

            // 3. Registrar traslado en historial
            $transfer = StaffTransfer::create([
                'user_id' => $targetUser->id,
                'from_warehouse_id' => $fromWarehouseId ?? $toWarehouse->id,
                'to_warehouse_id' => $toWarehouse->id,
                'transferred_by' => Auth::id(),
                'reason' => $request->reason,
                'closed_session_id' => $closedSessionId,
            ]);

            DB::commit();

            $transfer->load(['user:id,name', 'fromWarehouse:id,name', 'toWarehouse:id,name']);

            return response()->json([
                'success' => true,
                'message' => "Empleado \"{$targetUser->name}\" trasladado de \"{$fromWarehouseName}\" a \"{$toWarehouse->name}\"."
                    . ($closedSessionId ? ' Se cerró automáticamente su sesión de caja activa.' : ''),
                'data' => $transfer,
                'session_closed' => $closedSessionId !== null,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al ejecutar el traslado: ' . $e->getMessage(),
            ], 500);
        }
    }
}
