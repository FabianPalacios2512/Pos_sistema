<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Tenant;
use Stancl\Tenancy\Database\Models\Domain;
use Illuminate\Support\Facades\DB;

class FixMissingDomains extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tenants:fix-missing-domains {--dry-run : Ejecutar sin hacer cambios}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Repara tenants que no tienen dominio asoci ado en la tabla domains';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $isDryRun = $this->option('dry-run');

        if ($isDryRun) {
            $this->warn('🔍 MODO DRY-RUN: Solo se mostrarán los cambios sin aplicarlos');
        }

        $this->info('🔍 Buscando tenants sin dominio...');

        // Obtener todos los tenants
        $allTenants = Tenant::all();
        $tenantsWithoutDomain = [];

        foreach ($allTenants as $tenant) {
            $domainCount = Domain::where('tenant_id', $tenant->id)->count();

            if ($domainCount === 0) {
                $tenantsWithoutDomain[] = $tenant;
            }
        }

        if (empty($tenantsWithoutDomain)) {
            $this->info('✅ Todos los tenants tienen dominio asignado');
            return 0;
        }

        $this->warn("⚠️ Encontrados " . count($tenantsWithoutDomain) . " tenant(s) sin dominio");
        $this->newLine();

        $baseDomain = env('CENTRAL_DOMAIN', '105pos.pro');
        $this->info("📍 Dominio base: {$baseDomain}");
        $this->newLine();

        foreach ($tenantsWithoutDomain as $tenant) {
            $this->line("━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━");
            $this->info("Tenant ID: {$tenant->id}");
            $this->info("Business Name: {$tenant->business_name}");
            $this->info("Plan: {$tenant->plan}");

            // Generar dominio basado en el tenant ID
            $suggestedDomain = $tenant->id . '.' . $baseDomain;
            $this->info("Dominio sugerido: {$suggestedDomain}");

            // Verificar si el dominio ya existe (podría estar asignado a otro tenant)
            $existingDomain = Domain::where('domain', $suggestedDomain)->first();

            if ($existingDomain) {
                $this->error("❌ CONFLICTO: El dominio {$suggestedDomain} ya existe (tenant: {$existingDomain->tenant_id})");
                $this->warn("   Acción: Omitiendo este tenant. Se debe resolver manualmente.");
            } else {
                if (!$isDryRun) {
                    try {
                        // Crear el dominio
                        $tenant->domains()->create([
                            'domain' => $suggestedDomain
                        ]);

                        $this->info("✅ Dominio creado: {$suggestedDomain}");
                    } catch (\Exception $e) {
                        $this->error("❌ Error al crear dominio: " . $e->getMessage());
                    }
                } else {
                    $this->comment("   [DRY-RUN] Se crearía: {$suggestedDomain}");
                }
            }

            $this->newLine();
        }

        $this->newLine();

        if ($isDryRun) {
            $this->warn('🔍 MODO DRY-RUN: No se realizaron cambios');
            $this->info('💡 Ejecuta sin --dry-run para aplicar los cambios');
        } else {
            $this->info('✅ Proceso completado');
        }

        return 0;
    }
}
