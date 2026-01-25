#!/usr/bin/env php
<?php
/**
 * Script para arreglar los symlinks de storage de todos los tenants.
 * 
 * Este script:
 * 1. Verifica que public/storage sea un symlink a ../storage/app/public
 * 2. Crea symlinks para cada tenant en storage/app/public/tenants/{tenant_id}
 * 
 * Uso: php fix_tenant_symlinks.php
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

echo "🔧 Arreglando symlinks de tenants...\n\n";

// ========================================
// PASO 1: Arreglar public/storage
// ========================================
$publicStoragePath = public_path('storage');
$expectedTarget = '../storage/app/public';

echo "📁 Verificando public/storage...\n";

// Si existe como directorio real, eliminarlo
if (is_dir($publicStoragePath) && !is_link($publicStoragePath)) {
    echo "   ⚠️  public/storage es un directorio real, eliminando...\n";
    
    // Primero mover cualquier contenido importante
    $backupPath = storage_path('backup_public_storage_' . date('Y-m-d_H-i-s'));
    rename($publicStoragePath, $backupPath);
    echo "   📦 Backup creado en: {$backupPath}\n";
}

// Eliminar symlink roto si existe
if (is_link($publicStoragePath) && !file_exists(readlink($publicStoragePath))) {
    echo "   ⚠️  Symlink roto encontrado, eliminando...\n";
    unlink($publicStoragePath);
}

// Crear el symlink correcto
if (!is_link($publicStoragePath)) {
    if (symlink($expectedTarget, $publicStoragePath)) {
        echo "   ✅ Symlink creado: public/storage -> {$expectedTarget}\n";
    } else {
        echo "   ❌ Error creando symlink. Ejecutar manualmente:\n";
        echo "      cd " . public_path() . " && ln -sf {$expectedTarget} storage\n";
    }
} else {
    $currentTarget = readlink($publicStoragePath);
    if ($currentTarget === $expectedTarget) {
        echo "   ✅ Symlink ya existe y es correcto\n";
    } else {
        echo "   ⚠️  Symlink existe pero apunta a: {$currentTarget}\n";
        echo "   🔄 Corrigiendo...\n";
        unlink($publicStoragePath);
        symlink($expectedTarget, $publicStoragePath);
        echo "   ✅ Symlink corregido\n";
    }
}

echo "\n";

// ========================================
// PASO 2: Crear directorio tenants en storage/app/public
// ========================================
$storageTenantsDir = storage_path('app/public/tenants');
if (!is_dir($storageTenantsDir)) {
    File::makeDirectory($storageTenantsDir, 0755, true);
    echo "📁 Directorio creado: storage/app/public/tenants\n";
}

// ========================================
// PASO 3: Obtener todos los tenants
// ========================================
echo "🔍 Buscando tenants...\n";

$tenants = DB::table('tenants')->pluck('id');

if ($tenants->isEmpty()) {
    echo "   ⚠️  No se encontraron tenants\n";
    exit(0);
}

echo "   Encontrados: " . $tenants->count() . " tenants\n\n";

// ========================================
// PASO 4: Crear symlinks para cada tenant
// ========================================
$fixed = 0;
$skipped = 0;
$errors = 0;

foreach ($tenants as $tenantId) {
    echo "🔗 Procesando tenant: {$tenantId}\n";
    
    // Ruta del storage del tenant
    $tenantStoragePath = storage_path("tenant{$tenantId}/app/public");
    
    // Crear directorio si no existe
    if (!is_dir($tenantStoragePath)) {
        File::makeDirectory($tenantStoragePath, 0755, true);
        echo "   📁 Directorio creado: {$tenantStoragePath}\n";
        
        // Crear subdirectorios
        foreach (['products', 'logos', 'temp'] as $subdir) {
            $subdirPath = "{$tenantStoragePath}/{$subdir}";
            if (!is_dir($subdirPath)) {
                File::makeDirectory($subdirPath, 0755, true);
            }
        }
    }
    
    // Ruta del symlink
    $symlinkPath = "{$storageTenantsDir}/{$tenantId}";
    
    // Si existe como directorio real, eliminarlo
    if (is_dir($symlinkPath) && !is_link($symlinkPath)) {
        echo "   ⚠️  Eliminando directorio real...\n";
        File::deleteDirectory($symlinkPath);
    }
    
    // Si existe como symlink roto, eliminarlo
    if (is_link($symlinkPath) && !file_exists(readlink($symlinkPath))) {
        echo "   ⚠️  Eliminando symlink roto...\n";
        unlink($symlinkPath);
    }
    
    // Crear symlink si no existe
    if (!is_link($symlinkPath)) {
        if (@symlink($tenantStoragePath, $symlinkPath)) {
            echo "   ✅ Symlink creado: {$symlinkPath}\n";
            $fixed++;
        } else {
            echo "   ❌ Error creando symlink\n";
            $errors++;
        }
    } else {
        // Verificar que apunte al destino correcto
        $currentTarget = readlink($symlinkPath);
        if ($currentTarget === $tenantStoragePath) {
            echo "   ✅ Symlink ya existe y es correcto\n";
            $skipped++;
        } else {
            echo "   ⚠️  Symlink apunta a ruta incorrecta: {$currentTarget}\n";
            unlink($symlinkPath);
            if (@symlink($tenantStoragePath, $symlinkPath)) {
                echo "   ✅ Symlink corregido\n";
                $fixed++;
            } else {
                echo "   ❌ Error corrigiendo symlink\n";
                $errors++;
            }
        }
    }
}

echo "\n";
echo "════════════════════════════════════════\n";
echo "📊 RESUMEN:\n";
echo "   ✅ Arreglados: {$fixed}\n";
echo "   ⏭️  Ya correctos: {$skipped}\n";
echo "   ❌ Errores: {$errors}\n";
echo "════════════════════════════════════════\n";

if ($errors > 0) {
    echo "\n⚠️  Hubo errores. Verificar permisos del servidor.\n";
    exit(1);
}

echo "\n✅ Todos los symlinks están configurados correctamente.\n";
