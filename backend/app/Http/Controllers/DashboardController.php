<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Obtener ventas del día en hora Colombia (UTC-5)
     */
    public function ventasHoy(Request $request)
    {
        // Fecha de hoy en Colombia
        $fechaColombia = now('America/Bogota')->format('Y-m-d');

        // Query: filtrar por campo 'date' (fecha de la factura)
        $ventas = DB::table('invoices')
            ->where('status', 'paid')
            ->whereDate('date', $fechaColombia)
            ->select(DB::raw('COUNT(*) as transacciones, COALESCE(SUM(total),0) as total'))
            ->first();

        return response()->json([
            'fecha_colombia' => $fechaColombia,
            'transacciones' => (int)($ventas->transacciones ?? 0),
            'total' => (float)($ventas->total ?? 0)
        ]);
    }
}
