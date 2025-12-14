<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Stancl\Tenancy\Concerns\HasATenantsOption;
use Stancl\Tenancy\Concerns\TenantAwareCommand;

class MigrateMultisede extends Command
{
    use TenantAwareCommand, HasATenantsOption;

    protected $signature = 'multisede:migrate';
    protected $description = 'Ejecutar migraciones de multisede en todos los tenants';

    public function handle()
    {
        $this->info('🏢 Iniciando migración de sistema multisede...');

        $migrations = [
            '2025_11_30_000001_create_warehouses_table.php',
            '2025_11_30_000002_create_product_warehouse_table.php',
            '2025_11_30_000003_create_stock_transfers_table.php',
            '2025_11_30_000004_create_stock_transfer_items_table.php',
            '2025_11_30_000005_add_warehouse_id_to_cash_sessions_table.php',
            '2025_11_30_000006_add_warehouse_id_to_inventory_movements_table.php',
            '2025_11_30_000007_migrate_existing_stock_to_warehouses.php',
        ];

        $tenants = tenancy()->all();

        foreach ($migrations as $migration) {
            $this->info("📦 Ejecutando: $migration");
            $path = "database/migrations/tenant/$migration";

            foreach ($tenants as $tenant) {
                $tenant->run(function () use ($path, $tenant) {
                    $this->line("   Tenant: " . $tenant->getTenantKey());
                    \Artisan::call('migrate', [
                        '--path' => $path,
                        '--force' => true,
                    ]);
                    $this->line("   ✅ Completado");
                });
            }
        }

        $this->info('✅ Sistema multisede instalado exitosamente');
    }
}
