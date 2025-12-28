#!/usr/bin/env php
<?php

/**
 * 🔍 Script de Diagnóstico para Errores de Tenant
 *
 * Este script diagnostica dos problemas críticos:
 * 1. Pagos duplicados no asignando plan
 * 2. Error "Tenant could not be identified" al hacer login
 *
 * USO:
 *   php diagnose_tenant_errors.php
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "\n";
echo "╔══════════════════════════════════════════════════════════════╗\n";
echo "║  🔍 DIAGNÓSTICO DE ERRORES DE TENANT - 105POS               ║\n";
echo "╚══════════════════════════════════════════════════════════════╝\n";
echo "\n";

// ========================================
// PROBLEMA 1: Pago Duplicado (fabiana)
// ========================================
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "📦 PROBLEMA 1: Pago Duplicado - Tenant 'fabiana'\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

$reference = 'plan_fabiana_1766942956271';
$tenantId = 'fabiana';

echo "🔍 Buscando pending_payment con reference: {$reference}\n";
$pendingPayments = DB::connection('mysql')
    ->table('pending_payments')
    ->where('reference', $reference)
    ->get();

if ($pendingPayments->count() > 0) {
    echo "✅ Encontrados " . $pendingPayments->count() . " registro(s) de pago\n\n";
    foreach ($pendingPayments as $pp) {
        echo "  ID: {$pp->id}\n";
        echo "  Tenant ID: {$pp->tenant_id}\n";
        echo "  Plan: {$pp->plan}\n";
        echo "  Status: {$pp->status}\n";
        echo "  Payment Frequency: {$pp->payment_frequency}\n";
        echo "  Amount: " . ($pp->amount_in_cents / 100) . " COP\n";
        echo "  Created: {$pp->created_at}\n";
        echo "  Updated: {$pp->updated_at}\n";
        echo "  ─────────────────────────────────────────\n\n";
    }

    if ($pendingPayments->count() > 1) {
        echo "⚠️ ALERTA: Existen múltiples registros para la misma referencia de pago\n";
        echo "   Esto indica un problema de race condition o creación duplicada.\n\n";
    }
} else {
    echo "❌ No se encontró ningún registro de pago con esa referencia\n\n";
}

echo "🔍 Verificando tenant 'fabiana'...\n";
$tenant = DB::connection('mysql')->table('tenants')->where('id', $tenantId)->first();

if ($tenant) {
    echo "✅ Tenant encontrado\n";
    echo "  ID: {$tenant->id}\n";
    echo "  Business Name: {$tenant->business_name}\n";
    echo "  Plan Actual: {$tenant->plan}\n";
    echo "  Subscription Ends: {$tenant->subscription_ends_at}\n";
    echo "  Created: {$tenant->created_at}\n\n";
} else {
    echo "❌ Tenant NO encontrado\n\n";
}

// ========================================
// PROBLEMA 2: Tenant No Identificado (maria-jose)
// ========================================
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "🌐 PROBLEMA 2: Tenant No Identificado - 'maria-jose'\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

$subdomain = 'maria-jose';
$baseDomain = env('CENTRAL_DOMAIN', '105pos.pro');
$fullDomain = "{$subdomain}.{$baseDomain}";

echo "🔍 Buscando tenant con subdominio: {$subdomain}\n";
echo "   Dominio completo esperado: {$fullDomain}\n\n";

// Buscar en tabla domains
$domains = DB::connection('mysql')
    ->table('domains')
    ->where('domain', 'LIKE', "%{$subdomain}%")
    ->get();

if ($domains->count() > 0) {
    echo "✅ Encontrados " . $domains->count() . " dominio(s)\n\n";
    foreach ($domains as $domain) {
        echo "  ID: {$domain->id}\n";
        echo "  Domain: {$domain->domain}\n";
        echo "  Tenant ID: {$domain->tenant_id}\n";
        echo "  ─────────────────────────────────────────\n";

        // Verificar que el tenant existe
        $tenantForDomain = DB::connection('mysql')
            ->table('tenants')
            ->where('id', $domain->tenant_id)
            ->first();

        if ($tenantForDomain) {
            echo "  ✅ Tenant asociado existe\n";
            echo "     Business Name: {$tenantForDomain->business_name}\n";
            echo "     Plan: {$tenantForDomain->plan}\n";
            echo "     Subscription Ends: {$tenantForDomain->subscription_ends_at}\n";
        } else {
            echo "  ❌ PROBLEMA: Domain existe pero tenant asociado NO existe\n";
            echo "     Esto causará error 'Tenant could not be identified'\n";
        }
        echo "\n";
    }
} else {
    echo "❌ No se encontró ningún dominio con ese subdominio\n";
    echo "   CAUSA PROBABLE: El dominio no se creó al registrar el tenant\n\n";

    // Buscar si existe el tenant sin dominio
    $tenantWithoutDomain = DB::connection('mysql')
        ->table('tenants')
        ->where('id', 'LIKE', "%{$subdomain}%")
        ->orWhere('business_name', 'LIKE', "%{$subdomain}%")
        ->get();

    if ($tenantWithoutDomain->count() > 0) {
        echo "⚠️ Encontrados tenant(s) que podrían corresponder:\n\n";
        foreach ($tenantWithoutDomain as $t) {
            echo "  Tenant ID: {$t->id}\n";
            echo "  Business Name: {$t->business_name}\n";
            echo "  Plan: {$t->plan}\n";
            echo "  ─────────────────────────────────────────\n\n";
        }
        echo "   SOLUCIÓN: Crear manualmente el registro en tabla 'domains'\n\n";
    }
}

// ========================================
// CONFIGURACIÓN GENERAL
// ========================================
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "⚙️ CONFIGURACIÓN GENERAL\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

echo "🌐 Central Domain: " . env('CENTRAL_DOMAIN', 'NO CONFIGURADO') . "\n";
echo "🔧 App Environment: " . env('APP_ENV', 'N/A') . "\n";
echo "🔧 App URL: " . env('APP_URL', 'N/A') . "\n\n";

echo "📊 Estadísticas de la Base de Datos:\n";
$totalTenants = DB::connection('mysql')->table('tenants')->count();
$totalDomains = DB::connection('mysql')->table('domains')->count();
$totalPendingPayments = DB::connection('mysql')->table('pending_payments')->count();

echo "  Total Tenants: {$totalTenants}\n";
echo "  Total Domains: {$totalDomains}\n";
echo "  Total Pending Payments: {$totalPendingPayments}\n\n";

if ($totalTenants !== $totalDomains) {
    echo "⚠️ ALERTA: Número de tenants ({$totalTenants}) no coincide con número de domains ({$totalDomains})\n";
    echo "   Esto indica que hay tenants sin dominio asociado o dominios huérfanos\n\n";

    // Buscar tenants sin dominio
    $tenantsWithoutDomain = DB::connection('mysql')
        ->table('tenants')
        ->leftJoin('domains', 'tenants.id', '=', 'domains.tenant_id')
        ->whereNull('domains.id')
        ->select('tenants.*')
        ->get();

    if ($tenantsWithoutDomain->count() > 0) {
        echo "   Tenants sin dominio:\n";
        foreach ($tenantsWithoutDomain as $t) {
            echo "     - {$t->id} ({$t->business_name})\n";
        }
        echo "\n";
    }
}

// ========================================
// RECOMENDACIONES
// ========================================
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "💡 RECOMENDACIONES\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

echo "1. PROBLEMA DE PAGOS DUPLICADOS:\n";
echo "   - Agregar índice único en pending_payments(reference) para evitar duplicados\n";
echo "   - Implementar locking optimista en TenantPlanController::updatePlan\n";
echo "   - Validar idempotencia antes de procesar pago\n\n";

echo "2. PROBLEMA DE TENANT NO IDENTIFICADO:\n";
echo "   - Verificar que TenantRegisterController::register crea el domain correctamente\n";
echo "   - Revisar middleware InitializeTenancyByDomain\n";
echo "   - Asegurar que CENTRAL_DOMAIN está configurado en .env\n";
echo "   - Crear manualmente domains faltantes para tenants existentes\n\n";

echo "3. SCRIPT DE REPARACIÓN:\n";
echo "   - Ejecutar: php artisan tenants:fix-missing-domains\n";
echo "   - Verificar: php artisan tenants:list\n\n";

echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "✅ DIAGNÓSTICO COMPLETADO\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";
