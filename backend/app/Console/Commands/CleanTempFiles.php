<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Carbon\Carbon;

class CleanTempFiles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'temp:clean {--hours=1 : Eliminar archivos con más de X horas}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Elimina archivos temporales antiguos (Excel, CSV, imágenes) de todas las carpetas temp';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $hours = (int) $this->option('hours');
        $threshold = Carbon::now()->subHours($hours);

        $this->info("🧹 Limpiando archivos temporales con más de {$hours} hora(s) de antigüedad...");

        $deletedCount = 0;
        $freedSpace = 0;

        // Rutas a limpiar
        $paths = [
            storage_path('app/private/temp'),
            storage_path('app/temp'),
            storage_path('app/temp_excel'),
            storage_path('app/uploads/temp'),
        ];

        // Buscar carpetas de tenants
        $tenantPaths = glob(storage_path('tenant*/app/temp'));
        $paths = array_merge($paths, $tenantPaths);

        foreach ($paths as $path) {
            if (!File::isDirectory($path)) {
                continue;
            }

            $this->line("📂 Escaneando: {$path}");

            $files = File::allFiles($path);

            foreach ($files as $file) {
                // Solo archivos Excel, CSV o imágenes temporales
                $extension = strtolower($file->getExtension());
                if (!in_array($extension, ['csv', 'xlsx', 'xls', 'jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                    continue;
                }

                $lastModified = Carbon::createFromTimestamp($file->getMTime());

                if ($lastModified->lt($threshold)) {
                    $size = $file->getSize();
                    $freedSpace += $size;

                    if (File::delete($file->getPathname())) {
                        $deletedCount++;
                        $this->line("  ❌ " . $file->getFilename() . " (" . $this->formatBytes($size) . ")");
                    }
                }
            }
        }

        if ($deletedCount > 0) {
            $this->info("✅ Eliminados {$deletedCount} archivos temporales");
            $this->info("💾 Espacio liberado: " . $this->formatBytes($freedSpace));
        } else {
            $this->info("✨ No hay archivos antiguos para eliminar");
        }

        return 0;
    }

    /**
     * Formatear bytes a KB, MB, GB
     */
    private function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        for ($i = 0; $bytes > 1024; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, $precision) . ' ' . $units[$i];
    }
}
