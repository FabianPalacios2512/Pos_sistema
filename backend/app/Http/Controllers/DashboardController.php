<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Obtener ventas del día en hora Colombia (UTC-5)
     */
    public function ventasHoy(Request $request)
    {
        // Fecha de hoy en Colombia
        $fechaColombia = now('America/Bogota')->format('Y-m-d');

        $query = DB::table('invoices')
            ->where('status', 'paid')
            ->whereDate('date', $fechaColombia);

        // Si se pasa user_id (para vendedor), filtrar por sesión de caja
        if ($request->has('user_id')) {
            $query->whereIn('cash_session_id', function ($sub) use ($request) {
                $sub->select('id')->from('cash_sessions')
                    ->where('user_id', $request->user_id);
            });
        }

        $ventas = $query->select(DB::raw('COUNT(*) as transacciones, COALESCE(SUM(total),0) as total'))
            ->first();

        return response()->json([
            'fecha_colombia' => $fechaColombia,
            'transacciones' => (int)($ventas->transacciones ?? 0),
            'total' => (float)($ventas->total ?? 0)
        ]);
    }
}
