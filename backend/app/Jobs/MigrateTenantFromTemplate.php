<?php

declare(strict_types=1);

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Stancl\Tenancy\Contracts\TenantWithDatabase;

/**
 * Replaces MigrateDatabase job with a fast SQL template restore.
 * Instead of running 60+ migrations one by one (~24s), this imports
 * a pre-built schema dump in a single operation (~1s).
 */
class MigrateTenantFromTemplate implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $tenant;

    public function __construct(TenantWithDatabase|Model $tenant)
    {
        $this->tenant = $tenant;
    }

    public function handle(): void
    {
        $templatePath = database_path('tenant_template.sql');
        $dbName = 'tenant' . $this->tenant->getTenantKey();

        // If template doesn't exist, fall back to running migrations normally
        if (!file_exists($templatePath)) {
            Log::warning("⚠️ Template SQL not found at {$templatePath}, falling back to migrations");
            \Illuminate\Support\Facades\Artisan::call('tenants:migrate', [
                '--tenants' => [$this->tenant->getTenantKey()],
            ]);
            return;
        }

        $mysqlUser = config('database.connections.mysql.username', 'root');
        $mysqlPass = config('database.connections.mysql.password', '');
        $mysqlHost = config('database.connections.mysql.host', '127.0.0.1');
        $mysqlPort = config('database.connections.mysql.port', '3306');

        // Build mysql command
        $cmd = sprintf(
            'mysql -u%s %s -h%s -P%s %s < %s 2>&1',
            escapeshellarg($mysqlUser),
            $mysqlPass ? '-p' . escapeshellarg($mysqlPass) : '',
            escapeshellarg($mysqlHost),
            escapeshellarg((string) $mysqlPort),
            escapeshellarg($dbName),
            escapeshellarg($templatePath)
        );

        $output = [];
        $exitCode = 0;
        exec($cmd, $output, $exitCode);

        if ($exitCode !== 0) {
            $error = implode("\n", $output);
            Log::error("❌ Failed to import tenant template", [
                'tenant' => $this->tenant->getTenantKey(),
                'exit_code' => $exitCode,
                'error' => $error,
            ]);
            throw new \RuntimeException("Failed to import tenant template for {$dbName}: {$error}");
        }

        Log::info("✅ Tenant DB created from template", [
            'tenant' => $this->tenant->getTenantKey(),
            'database' => $dbName,
        ]);
    }
}
