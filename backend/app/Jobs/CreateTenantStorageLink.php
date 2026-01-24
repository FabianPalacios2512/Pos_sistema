<?php

namespace App\Jobs;

use Stancl\Tenancy\Contracts\Tenant;
use Stancl\Tenancy\Contracts\TenantWithDatabase;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

/**
 * Job para crear el symlink de storage cuando se crea un tenant.
 * 
 * Crea DOS symlinks:
 * 1. storage/app/public/tenants/{tenant_id} -> storage/tenant{tenant_id}/app/public
 * 2. public/storage/tenants/{tenant_id} -> storage/tenant{tenant_id}/app/public
 */
class CreateTenantStorageLink
{
    public function handle(Tenant $tenant): void
    {
        $tenantId = $tenant->getTenantKey();
        
        Log::info("🔗 [Tenant Storage] Creando symlinks para tenant: {$tenantId}");
        
        try {
            // Ruta del storage del tenant (donde se guardan realmente los archivos)
            $tenantStoragePath = storage_path("tenant{$tenantId}/app/public");
            
            // Asegurar que el directorio del tenant exista
            if (!is_dir($tenantStoragePath)) {
                File::makeDirectory($tenantStoragePath, 0755, true);
                Log::info("📁 [Tenant Storage] Directorio creado: {$tenantStoragePath}");
            }
            
            // Crear subdirectorios necesarios
            $subdirs = ['products', 'logos', 'temp'];
            foreach ($subdirs as $subdir) {
                $subdirPath = "{$tenantStoragePath}/{$subdir}";
                if (!is_dir($subdirPath)) {
                    File::makeDirectory($subdirPath, 0755, true);
                }
            }
            
            // ========================================
            // SYMLINK 1: storage/app/public/tenants/{tenant_id}
            // ========================================
            $storageTenantsDir = storage_path('app/public/tenants');
            if (!is_dir($storageTenantsDir)) {
                File::makeDirectory($storageTenantsDir, 0755, true);
            }
            
            $symlinkPath1 = "{$storageTenantsDir}/{$tenantId}";
            if (!is_link($symlinkPath1) && !is_dir($symlinkPath1)) {
                if (@symlink($tenantStoragePath, $symlinkPath1)) {
                    Log::info("✅ [Tenant Storage] Symlink 1 creado: {$symlinkPath1}");
                } else {
                    Log::warning("⚠️ [Tenant Storage] No se pudo crear symlink 1: {$symlinkPath1}");
                }
            }
            
            // ========================================
            // SYMLINK 2: public/storage/tenants/{tenant_id}
            // ========================================
            $publicTenantsDir = public_path('storage/tenants');
            if (!is_dir($publicTenantsDir)) {
                // Asegurar que public/storage existe
                $publicStorageDir = public_path('storage');
                if (!is_dir($publicStorageDir)) {
                    File::makeDirectory($publicStorageDir, 0755, true);
                }
                File::makeDirectory($publicTenantsDir, 0755, true);
            }
            
            $symlinkPath2 = "{$publicTenantsDir}/{$tenantId}";
            if (!is_link($symlinkPath2) && !is_dir($symlinkPath2)) {
                if (@symlink($tenantStoragePath, $symlinkPath2)) {
                    Log::info("✅ [Tenant Storage] Symlink 2 creado (público): {$symlinkPath2}");
                } else {
                    Log::warning("⚠️ [Tenant Storage] No se pudo crear symlink 2: {$symlinkPath2}");
                }
            }
            
            Log::info("🎉 [Tenant Storage] Symlinks configurados exitosamente para: {$tenantId}");
            
        } catch (\Exception $e) {
            Log::error("❌ [Tenant Storage] Error creando symlinks para tenant {$tenantId}: {$e->getMessage()}");
            // No lanzar excepción para no detener la creación del tenant
        }
    }
}
