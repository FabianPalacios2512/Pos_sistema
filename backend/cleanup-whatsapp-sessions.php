<?php
/**
 * Script de limpieza de sesiones WhatsApp huérfanas
 * Elimina carpetas de sesiones de tenants que ya no existen
 * Ejecutar semanalmente via cron
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

$sessionsPath = __DIR__ . '/whatsapp_sessions';
$qrsPath = __DIR__ . '/whatsapp_qrs';

// Obtener tenants activos
$activeTenants = DB::connection('mysql')->table('tenants')->pluck('id')->map(function($id) {
    return str_replace('_', '-', $id); // Normalizar formato (holas_mud -> holas-mud)
})->toArray();

echo "Tenants activos: " . count($activeTenants) . "\n";

// Limpiar sesiones huérfanas
if (is_dir($sessionsPath)) {
    $sessionFolders = array_diff(scandir($sessionsPath), ['.', '..']);
    foreach ($sessionFolders as $folder) {
        $normalizedFolder = str_replace('-', '_', $folder);
        if (!in_array($folder, $activeTenants) && !in_array($normalizedFolder, $activeTenants)) {
            $fullPath = $sessionsPath . '/' . $folder;
            if (is_dir($fullPath)) {
                echo "Eliminando sesión huérfana: $folder\n";
                shell_exec("rm -rf " . escapeshellarg($fullPath));
            }
        }
    }
}

// Limpiar QRs huérfanos
if (is_dir($qrsPath)) {
    $qrFiles = array_diff(scandir($qrsPath), ['.', '..']);
    foreach ($qrFiles as $file) {
        $tenantId = preg_replace('/_qr\.txt$/', '', $file);
        $normalizedTenant = str_replace('-', '_', $tenantId);
        if (!in_array($tenantId, $activeTenants) && !in_array($normalizedTenant, $activeTenants)) {
            $fullPath = $qrsPath . '/' . $file;
            echo "Eliminando QR huérfano: $file\n";
            unlink($fullPath);
        }
    }
}

echo "Limpieza completada.\n";
