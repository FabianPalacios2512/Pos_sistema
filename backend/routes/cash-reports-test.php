<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;

function testDatabaseConnection() {
    try {
        // Test basic database connection
        $result = DB::select('SELECT COUNT(*) as count FROM sales');

        return response()->json([
            'success' => true,
            'database_connected' => true,
            'sales_count' => $result[0]->count ?? 0,
            'message' => 'Database connection successful'
        ]);

    } catch (Exception $e) {
        return response()->json([
            'success' => false,
            'database_connected' => false,
            'error' => $e->getMessage(),
            'message' => 'Database connection failed'
        ], 500);
    }
}

function getSimpleCashData() {
    try {
        // Get basic cash data
        $salesCount = DB::table('sales')->count();
        $usersCount = DB::table('users')->count();
        $totalSales = DB::table('sales')->sum('total_amount');

        return response()->json([
            'success' => true,
            'data' => [
                'total_sales' => $totalSales ?: 0,
                'total_transactions' => $salesCount ?: 0,
                'average_sale' => $salesCount > 0 ? round($totalSales / $salesCount, 2) : 0,
                'active_users' => $usersCount ?: 0
            ]
        ]);

    } catch (Exception $e) {
        return response()->json([
            'success' => false,
            'error' => $e->getMessage()
        ], 500);
    }
}
