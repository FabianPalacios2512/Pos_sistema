<?php

declare(strict_types=1);

namespace App\Listeners;

use Illuminate\Support\Facades\Log;
use Stancl\Tenancy\Events\TenancyBootstrapped;

/**
 * Listener que se ejecuta cada vez que se inicializa un tenant.
 * Verifica y crea el symlink de storage si no existe.
 * 
 * Esto asegura que las imágenes funcionen para TODOS los tenants,
 * sin importar cómo fueron creados o si el deploy rompió los symlinks.
 */
class EnsureTenantStorageLink
{
    public function handle(TenancyBootstrapped $event): void
    {
        try {
            $tenantId = $event->tenancy->tenant->getTenantKey();
            if (!$tenantId) {
                return;
            }

            $symlinkPath = public_path("storage/tenants/{$tenantId}");

            // Fast path: symlink already exists and works
            if (is_link($symlinkPath) && is_dir($symlinkPath)) {
                return;
            }

            $tenantStoragePath = base_path("storage/tenant{$tenantId}/app/public");

            // Ensure tenant storage directory exists
            if (!is_dir($tenantStoragePath)) {
                @mkdir($tenantStoragePath, 0755, true);
            }

            // Ensure subdirectories exist
            foreach (['products', 'logos', 'temp'] as $subdir) {
                $subdirPath = "{$tenantStoragePath}/{$subdir}";
                if (!is_dir($subdirPath)) {
                    @mkdir($subdirPath, 0755, true);
                }
            }

            // Ensure parent directories exist
            $tenantsDir = public_path('storage/tenants');
            if (!is_dir($tenantsDir)) {
                @mkdir($tenantsDir, 0755, true);
            }

            // Handle broken symlink
            if (is_link($symlinkPath) && !is_dir($symlinkPath)) {
                @unlink($symlinkPath);
            }

            // Handle real directory (replace with symlink)
            if (is_dir($symlinkPath) && !is_link($symlinkPath)) {
                // Move existing files to tenant storage
                $files = @scandir($symlinkPath);
                if ($files) {
                    foreach ($files as $file) {
                        if ($file === '.' || $file === '..') continue;
                        $src = "{$symlinkPath}/{$file}";
                        $dst = "{$tenantStoragePath}/{$file}";
                        if (!file_exists($dst)) {
                            @rename($src, $dst);
                        }
                    }
                }
                @rmdir($symlinkPath);
                if (is_dir($symlinkPath)) {
                    @exec("rm -rf " . escapeshellarg($symlinkPath));
                }
            }

            // Create symlink
            if (!is_link($symlinkPath)) {
                @symlink($tenantStoragePath, $symlinkPath);
            }
        } catch (\Exception $e) {
            Log::error("[Tenant Storage Bootstrap] Error: {$e->getMessage()}");
        }
    }
}
