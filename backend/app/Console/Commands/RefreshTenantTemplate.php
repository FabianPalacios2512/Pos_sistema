<?php

namespace App\Console\Commands;

use App\Models\Tenant;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class RefreshTenantTemplate extends Command
{
    protected $signature = 'tenant:refresh-template';
    protected $description = 'Regenerate the tenant SQL template from a fresh migrated database';

    public function handle(): int
    {
        $this->info('🔄 Refreshing tenant template...');

        $templateId = 'template';
        $dbName = 'tenant' . $templateId;
        $outputPath = database_path('tenant_template.sql');

        // 1. Clean up old template if exists
        $existing = Tenant::find($templateId);
        if ($existing) {
            $this->info('  Removing old template tenant...');
            $existing->domains()->delete();
            DB::statement("DROP DATABASE IF EXISTS `{$dbName}`");
            $existing->forceDelete();
        }

        // 2. Create fresh template tenant (triggers CreateDatabase + MigrateDatabase via events)
        // Temporarily swap back to real migrations for template creation
        $this->info('  Creating template tenant with all migrations...');

        // Create DB manually and run migrations directly (bypass our template job)
        DB::statement("CREATE DATABASE IF NOT EXISTS `{$dbName}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");

        // Insert tenant record without triggering events
        DB::table('tenants')->insert([
            'id' => $templateId,
            'data' => json_encode([
                'business_name' => 'Template',
                'plan' => 'template',
                'subscription_ends_at' => now()->addYears(5)->toDateTimeString(),
            ]),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Run migrations on the template DB
        $this->info('  Running all migrations...');
        $this->call('tenants:migrate', ['--tenants' => [$templateId]]);

        // 3. Dump schema + migrations data
        $this->info('  Dumping schema...');

        $mysqlUser = config('database.connections.mysql.username', 'root');
        $mysqlPass = config('database.connections.mysql.password', '');
        $mysqlHost = config('database.connections.mysql.host', '127.0.0.1');
        $mysqlPort = config('database.connections.mysql.port', '3306');

        $passFlag = $mysqlPass ? '-p' . escapeshellarg($mysqlPass) : '';

        // Schema dump (no data)
        $cmd1 = sprintf(
            'mysqldump -u%s %s -h%s -P%s --no-data %s > %s 2>&1',
            escapeshellarg($mysqlUser),
            $passFlag,
            escapeshellarg($mysqlHost),
            escapeshellarg((string) $mysqlPort),
            escapeshellarg($dbName),
            escapeshellarg($outputPath)
        );
        exec($cmd1, $out1, $code1);
        if ($code1 !== 0) {
            $this->error('Failed to dump schema: ' . implode("\n", $out1));
            return 1;
        }

        // Append migrations table data
        $cmd2 = sprintf(
            'mysqldump -u%s %s -h%s -P%s --no-create-info %s migrations >> %s 2>&1',
            escapeshellarg($mysqlUser),
            $passFlag,
            escapeshellarg($mysqlHost),
            escapeshellarg((string) $mysqlPort),
            escapeshellarg($dbName),
            escapeshellarg($outputPath)
        );
        exec($cmd2, $out2, $code2);
        if ($code2 !== 0) {
            $this->error('Failed to dump migrations data: ' . implode("\n", $out2));
            return 1;
        }

        // 4. Clean up template tenant
        $this->info('  Cleaning up template tenant...');
        DB::statement("DROP DATABASE IF EXISTS `{$dbName}`");
        DB::table('tenants')->where('id', $templateId)->delete();

        $lines = count(file($outputPath));
        $this->info("✅ Template refreshed: {$outputPath} ({$lines} lines)");
        $this->info('   Run this command after adding new tenant migrations.');

        return 0;
    }
}
