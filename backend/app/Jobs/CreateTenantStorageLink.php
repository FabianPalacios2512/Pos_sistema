<?php

declare(strict_types=1);

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Stancl\Tenancy\Contracts\TenantWithDatabase;

/**
 * Job para crear el symlink de storage cuando se crea un tenant.
 * 
 * ARQUITECTURA DE SYMLINKS:
 * ========================
 * 1. public/storage -> ../storage/app/public (symlink principal, creado por php artisan storage:link)
 * 2. storage/app/public/tenants/{tenant_id} -> storage/tenant{tenant_id}/app/public (este job)
 * 
 * Con esta estructura, las URLs funcionan así:
 * https://tenant.105pos.pro/storage/tenants/{tenant_id}/products/image.jpg
 *   -> public/storage (symlink) -> storage/app/public
 *   -> storage/app/public/tenants/{tenant_id} (symlink) -> storage/tenant{tenant_id}/app/public
 *   -> storage/tenant{tenant_id}/app/public/products/image.jpg (archivo real)
 * 
 * ⚠️ IMPORTANTE: NO crear symlinks dentro de public/storage/tenants/ directamente,
 * ya que eso requeriría que public/storage sea un directorio real en lugar de symlink.
 */
class CreateTenantStorageLink implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /** @var TenantWithDatabase|Model */
    protected $tenant;

    /**
     * Create a new job instance.
     *
     * @param TenantWithDatabase $tenant
     */
    public function __construct(TenantWithDatabase $tenant)
    {
        $this->tenant = $tenant;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $tenantId = $this->tenant->getTenantKey();
        
        try {
            // IMPORTANTE: usar base_path() en lugar de storage_path().
            // Este job corre dentro del contexto del tenant (tenancy ya inicializado),
            // por lo que storage_path() devuelve storage/tenant{id}/ con el prefijo
            // del tenant. Usar storage_path("tenant{id}/...") causaría la ruta doble:
            // storage/tenant{id}/tenant{id}/... — incorrecto.
            $tenantStoragePath = base_path("storage/tenant{$tenantId}/app/public");
            
            if (!is_dir($tenantStoragePath)) {
                File::makeDirectory($tenantStoragePath, 0755, true);
            }
            
            $subdirs = ['products', 'logos', 'temp'];
            foreach ($subdirs as $subdir) {
                $subdirPath = "{$tenantStoragePath}/{$subdir}";
                if (!is_dir($subdirPath)) {
                    File::makeDirectory($subdirPath, 0755, true);
                }
            }
            
            // Igual: usar base_path() para el directorio de tenants en storage/
            $storageTenantsDir = base_path('storage/app/public/tenants');
            if (!is_dir($storageTenantsDir)) {
                File::makeDirectory($storageTenantsDir, 0755, true);
            }
            
            $symlinkPath = "{$storageTenantsDir}/{$tenantId}";
            
            if (is_dir($symlinkPath) && !is_link($symlinkPath)) {
                File::deleteDirectory($symlinkPath);
            }
            
            if (is_link($symlinkPath) && !file_exists($symlinkPath)) {
                unlink($symlinkPath);
            }
            
            if (!is_link($symlinkPath)) {
                if (!@symlink($tenantStoragePath, $symlinkPath)) {
                    Log::warning("[Tenant Storage] No se pudo crear symlink: {$symlinkPath}");
                }
            }
            
            // También crear symlink directamente en public/storage/tenants/
            // (para cuando public/storage es un directorio real, no symlink)
            $publicTenantsDir = public_path('storage/tenants');
            if (!is_dir($publicTenantsDir)) {
                File::makeDirectory($publicTenantsDir, 0755, true);
            }
            
            $publicSymlinkPath = "{$publicTenantsDir}/{$tenantId}";
            
            if (is_dir($publicSymlinkPath) && !is_link($publicSymlinkPath)) {
                File::deleteDirectory($publicSymlinkPath);
            }
            
            if (is_link($publicSymlinkPath) && !file_exists($publicSymlinkPath)) {
                unlink($publicSymlinkPath);
            }
            
            if (!is_link($publicSymlinkPath)) {
                if (!@symlink($tenantStoragePath, $publicSymlinkPath)) {
                    Log::warning("[Tenant Storage] No se pudo crear symlink público: {$publicSymlinkPath}");
                }
            }
            
        } catch (\Exception $e) {
            Log::error("[Tenant Storage] Error creando symlinks para tenant {$tenantId}: {$e->getMessage()}");
        }
    }
}
