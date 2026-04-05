<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CashMovement;
use App\Models\CashSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class CashMovementController extends Controller
{
    /**
     * Listar movimientos de la sesión activa o por session_id
     */
    public function index(Request $request)
    {
        try {
            $sessionId = $request->get('cash_session_id');

            if (!$sessionId) {
                $session = CashSession::getOpenSessionForUser(Auth::id());
                $sessionId = $session?->id;
            }

            if (!$sessionId) {
                return response()->json([
                    'success' => true,
                    'data' => [],
                    'message' => 'No hay sesión de caja activa'
                ]);
            }

            $movements = CashMovement::where('cash_session_id', $sessionId)
                ->with('user:id,name')
                ->orderBy('created_at', 'desc')
                ->get();

            $summary = [
                'total_ingresos' => $movements->where('type', 'ingreso')->sum('amount'),
                'total_egresos' => $movements->where('type', 'egreso')->sum('amount'),
                'count' => $movements->count(),
            ];

            return response()->json([
                'success' => true,
                'data' => $movements,
                'summary' => $summary
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener movimientos: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Registrar un movimiento de caja (ingreso o egreso)
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'type' => 'required|in:ingreso,egreso',
                'amount' => 'required|numeric|min:0.01',
                'concept' => 'required|string|max:255',
                'reference' => 'nullable|string|max:100',
                'notes' => 'nullable|string|max:500',
            ]);

            $userId = Auth::id();
            if (!$userId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Usuario no autenticado'
                ], 401);
            }

            $session = CashSession::getOpenSessionForUser($userId);
            if (!$session) {
                return response()->json([
                    'success' => false,
                    'message' => 'Debes tener una caja abierta para registrar movimientos'
                ], 400);
            }

            $movement = CashMovement::create([
                'cash_session_id' => $session->id,
                'user_id' => $userId,
                'type' => $request->type,
                'amount' => $request->amount,
                'concept' => $request->concept,
                'reference' => $request->reference,
                'notes' => $request->notes,
            ]);

            $movement->load('user:id,name');
            $session->updateSalesTotals();
            $session->calculateExpectedAmount();
            $session->save();

            $typeLabel = $request->type === 'ingreso' ? 'Ingreso' : 'Egreso';

            return response()->json([
                'success' => true,
                'message' => "$typeLabel registrado correctamente",
                'data' => $movement,
                'session' => $session->fresh(['user', 'warehouse'])
            ], 201);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Datos inválidos',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al registrar movimiento: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Eliminar un movimiento (solo si la sesión está abierta)
     */
    public function destroy($id)
    {
        try {
            $movement = CashMovement::findOrFail($id);
            $session = $movement->cashSession;

            if ($session->status !== 'open') {
                return response()->json([
                    'success' => false,
                    'message' => 'No se puede eliminar un movimiento de una caja cerrada'
                ], 400);
            }

            $userId = Auth::id();
            if ($movement->user_id !== $userId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Solo puedes eliminar tus propios movimientos'
                ], 403);
            }

            $movement->delete();
            $session->updateSalesTotals();
            $session->calculateExpectedAmount();
            $session->save();

            return response()->json([
                'success' => true,
                'message' => 'Movimiento eliminado',
                'session' => $session->fresh(['user', 'warehouse'])
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar movimiento: ' . $e->getMessage()
            ], 500);
        }
    }
}
