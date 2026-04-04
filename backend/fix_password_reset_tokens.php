<?php

/**
 * FIX: Agregar columnas faltantes a password_reset_tokens (base de datos central)
 *
 * Problema: SQLSTATE[42S22] Column not found 'tenant_id' en password_reset_tokens
 * Causa: Migraciones add_tenant_id y add_expires_at no se aplicaron en producción
 *
 * Uso: php fix_password_reset_tokens.php
 */

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

echo "=== FIX: password_reset_tokens columns ===\n\n";

$connection = 'mysql'; // Base de datos central

// Obtener columnas actuales
$columns = DB::connection($connection)
    ->getSchemaBuilder()
    ->getColumnListing('password_reset_tokens');

echo "Columnas actuales: " . implode(', ', $columns) . "\n\n";

$fixed = 0;

// 1. Agregar tenant_id si no existe
if (!in_array('tenant_id', $columns)) {
    echo "→ Agregando columna 'tenant_id'...\n";
    DB::connection($connection)->statement(
        "ALTER TABLE `password_reset_tokens` ADD COLUMN `tenant_id` VARCHAR(255) NULL AFTER `token`"
    );
    echo "  ✓ tenant_id agregada\n";
    $fixed++;
} else {
    echo "  ✓ tenant_id ya existe\n";
}

// 2. Agregar expires_at si no existe
if (!in_array('expires_at', $columns)) {
    echo "→ Agregando columna 'expires_at'...\n";
    DB::connection($connection)->statement(
        "ALTER TABLE `password_reset_tokens` ADD COLUMN `expires_at` TIMESTAMP NULL AFTER `tenant_id`"
    );
    echo "  ✓ expires_at agregada\n";
    $fixed++;
} else {
    echo "  ✓ expires_at ya existe\n";
}

// 3. Agregar 'used' si no existe
if (!in_array('used', $columns)) {
    echo "→ Agregando columna 'used'...\n";
    DB::connection($connection)->statement(
        "ALTER TABLE `password_reset_tokens` ADD COLUMN `used` TINYINT(1) NOT NULL DEFAULT 0 AFTER `expires_at`"
    );
    echo "  ✓ used agregada\n";
    $fixed++;
} else {
    echo "  ✓ used ya existe\n";
}

// 4. Agregar ip_address si no existe
if (!in_array('ip_address', $columns)) {
    echo "→ Agregando columna 'ip_address'...\n";
    DB::connection($connection)->statement(
        "ALTER TABLE `password_reset_tokens` ADD COLUMN `ip_address` VARCHAR(45) NULL"
    );
    echo "  ✓ ip_address agregada\n";
    $fixed++;
} else {
    echo "  ✓ ip_address ya existe\n";
}

// 5. Agregar user_agent si no existe
if (!in_array('user_agent', $columns)) {
    echo "→ Agregando columna 'user_agent'...\n";
    DB::connection($connection)->statement(
        "ALTER TABLE `password_reset_tokens` ADD COLUMN `user_agent` TEXT NULL"
    );
    echo "  ✓ user_agent agregada\n";
    $fixed++;
} else {
    echo "  ✓ user_agent ya existe\n";
}

// 6. Agregar updated_at si no existe
if (!in_array('updated_at', $columns)) {
    echo "→ Agregando columna 'updated_at'...\n";
    DB::connection($connection)->statement(
        "ALTER TABLE `password_reset_tokens` ADD COLUMN `updated_at` TIMESTAMP NULL"
    );
    echo "  ✓ updated_at agregada\n";
    $fixed++;
} else {
    echo "  ✓ updated_at ya existe\n";
}

// 7. Verificar columnas finales
$columnsAfter = DB::connection($connection)
    ->getSchemaBuilder()
    ->getColumnListing('password_reset_tokens');

echo "\nColumnas después del fix: " . implode(', ', $columnsAfter) . "\n";
echo "\n=== Resultado: $fixed columna(s) agregada(s) ===\n";
echo "✅ La recuperación de contraseña debería funcionar ahora.\n";
