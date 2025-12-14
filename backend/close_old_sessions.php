<?php
/**
 * Script para cerrar sesiones de caja antiguas
 *
 * Uso: php close_old_sessions.php [--days=7] [--dry-run]
 *
 * Opciones:
 *   --days=N    Cerrar sesiones con más de N días de antigüedad (por defecto: 7)
 *   --dry-run   Mostrar qué sesiones se cerrarían sin cerrarlas realmente
 *   --all       Cerrar TODAS las sesiones abiertas (usar con precaución)
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\CashSession;
use Illuminate\Support\Facades\DB;

// Parsear argumentos
$days = 7;
$dryRun = false;
$closeAll = false;

foreach ($argv as $arg) {
    if (strpos($arg, '--days=') === 0) {
        $days = (int) substr($arg, 7);
    } elseif ($arg === '--dry-run') {
        $dryRun = true;
    } elseif ($arg === '--all') {
        $closeAll = true;
    }
}

echo "🔍 CIERRE DE SESIONES DE CAJA ANTIGUAS\n\n";

if ($dryRun) {
    echo "⚠️  MODO DRY-RUN: No se realizarán cambios\n\n";
}

// Obtener sesiones a cerrar
$query = CashSession::where('status', 'open')->with('user');

if (!$closeAll) {
    $cutoffDate = now()->subDays($days);
    $query->where('created_at', '<', $cutoffDate);
    echo "📅 Buscando sesiones abiertas hace más de {$days} días...\n\n";
} else {
    echo "⚠️  CERRANDO TODAS LAS SESIONES ABIERTAS\n\n";
}

$sessionsToClose = $query->get();

if ($sessionsToClose->isEmpty()) {
    echo "✅ No hay sesiones que cerrar\n";
    exit(0);
}

echo "📊 Sesiones encontradas: {$sessionsToClose->count()}\n\n";

foreach ($sessionsToClose as $session) {
    $duration = now()->diffInDays($session->created_at);
    echo "  🔓 ID: {$session->id}\n";
    echo "     Usuario: {$session->user->name} (ID: {$session->user_id})\n";
    echo "     Abierta desde: {$session->created_at->format('Y-m-d H:i:s')}\n";
    echo "     Antigüedad: {$duration} días\n";
    echo "     Total ventas: $" . number_format($session->total_sales, 0) . "\n";

    if (!$dryRun) {
        try {
            DB::beginTransaction();

            // Calcular totales finales
            $expectedAmount = $session->opening_amount + $session->cash_sales;

            $session->update([
                'status' => 'closed',
                'closing_date' => now()->toDateString(),
                'closing_time' => now()->toTimeString(),
                'actual_amount' => $expectedAmount, // Asumiendo que está correcto
                'difference' => 0,
                'closing_status' => 'exact',
                'closing_notes' => 'Cerrada automáticamente por script - sesión antigua'
            ]);

            DB::commit();
            echo "     ✅ Cerrada exitosamente\n";
        } catch (\Exception $e) {
            DB::rollBack();
            echo "     ❌ Error al cerrar: {$e->getMessage()}\n";
        }
    } else {
        echo "     ℹ️  Sería cerrada (dry-run)\n";
    }

    echo "\n";
}

if ($dryRun) {
    echo "\n💡 Para cerrar estas sesiones, ejecuta el script sin --dry-run\n";
    echo "   Ejemplo: php close_old_sessions.php --days={$days}\n";
} else {
    echo "\n✅ Proceso completado\n";
}
