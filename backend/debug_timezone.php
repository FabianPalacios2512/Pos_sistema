<?php
require_once 'vendor/autoload.php';
require_once 'bootstrap/app.php';

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

echo "=== DIAGNÓSTICO DE VENTAS POR HORA ===" . PHP_EOL;
echo "Fecha/hora actual del servidor (UTC): " . Carbon::now()->toDateTimeString() . PHP_EOL;
echo "Fecha/hora en Colombia (UTC-5): " . Carbon::now()->setTimezone('America/Bogota')->toDateTimeString() . PHP_EOL;
echo PHP_EOL;

// Obtener ventas de los últimos 2 días con zona horaria
echo "=== VENTAS RECIENTES (UTC vs Colombia) ===" . PHP_EOL;
$recentSales = DB::table('invoices')
    ->where('status', 'paid')
    ->where('created_at', '>=', Carbon::now()->subDays(2))
    ->orderBy('created_at', 'desc')
    ->limit(10)
    ->get(['id', 'created_at', 'total']);

foreach ($recentSales as $sale) {
    $utcTime = Carbon::parse($sale->created_at);
    $colombiaTime = $utcTime->setTimezone('America/Bogota');

    echo "Venta #" . $sale->id . " - UTC: " . $utcTime->toDateTimeString() .
         " | Colombia: " . $colombiaTime->toDateTimeString() .
         " | Total: $" . number_format($sale->total) . PHP_EOL;
}

echo PHP_EOL . "=== VENTAS AGRUPADAS POR HORA (Hoy en Colombia) ===" . PHP_EOL;

// Definir el rango de "hoy" en Colombia
$nowColombia = Carbon::now()->setTimezone('America/Bogota');
$startOfDayColombia = $nowColombia->copy()->startOfDay();
$endOfDayColombia = $nowColombia->copy()->endOfDay();

// Convertir a UTC para la consulta
$startOfDayUTC = $startOfDayColombia->copy()->setTimezone('UTC');
$endOfDayUTC = $endOfDayColombia->copy()->setTimezone('UTC');

echo "Rango de búsqueda (Colombia): " . $startOfDayColombia->toDateTimeString() . " a " . $endOfDayColombia->toDateTimeString() . PHP_EOL;
echo "Rango de búsqueda (UTC): " . $startOfDayUTC->toDateTimeString() . " a " . $endOfDayUTC->toDateTimeString() . PHP_EOL;

$hourlyData = DB::table('invoices')
    ->whereBetween('created_at', [$startOfDayUTC->toDateTimeString(), $endOfDayUTC->toDateTimeString()])
    ->where('status', 'paid')
    ->select([
        DB::raw('HOUR(CONVERT_TZ(created_at, "+00:00", "-05:00")) as hour_colombia'),
        DB::raw('COUNT(*) as transactions'),
        DB::raw('SUM(total) as sales')
    ])
    ->groupBy(DB::raw('HOUR(CONVERT_TZ(created_at, "+00:00", "-05:00"))'))
    ->orderBy('hour_colombia')
    ->get();

if ($hourlyData->count() > 0) {
    foreach ($hourlyData as $hour) {
        echo "Hora " . sprintf('%02d:00', $hour->hour_colombia) .
             " Colombia - Transacciones: " . $hour->transactions .
             " - Ventas: $" . number_format($hour->sales) . PHP_EOL;
    }
} else {
    echo "❌ No hay ventas para hoy en Colombia." . PHP_EOL;

    // Verificar si hay ventas ayer
    echo PHP_EOL . "=== VERIFICANDO VENTAS DE AYER ===" . PHP_EOL;
    $yesterdayColombia = $nowColombia->copy()->subDay();
    $startYesterday = $yesterdayColombia->copy()->startOfDay()->setTimezone('UTC');
    $endYesterday = $yesterdayColombia->copy()->endOfDay()->setTimezone('UTC');

    $yesterdayCount = DB::table('invoices')
        ->whereBetween('created_at', [$startYesterday->toDateTimeString(), $endYesterday->toDateTimeString()])
        ->where('status', 'paid')
        ->count();

    echo "Ventas de ayer: " . $yesterdayCount . PHP_EOL;
}

echo PHP_EOL . "=== CONFIGURACIÓN ACTUAL DEL SISTEMA ===" . PHP_EOL;
echo "Zona horaria PHP: " . date_default_timezone_get() . PHP_EOL;
echo "Zona horaria MySQL: " . DB::select("SELECT @@session.time_zone as tz")[0]->tz . PHP_EOL;
