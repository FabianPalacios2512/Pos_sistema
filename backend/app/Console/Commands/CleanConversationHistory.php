<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Stancl\Tenancy\Facades\Tenancy;

class CleanConversationHistory extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'conversation:clean {--hours=1 : Horas de antigüedad antes de eliminar}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Limpia el historial de conversaciones antiguas de la IA para evitar acumulación de datos';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $hours = $this->option('hours');
        $this->info("🧹 Limpiando historial de conversaciones de IA (> {$hours} hora(s))...");

        try {
            // Obtener todos los tenants
            $tenants = \App\Models\Tenant::all();
            $totalDeleted = 0;
            $tenantsProcessed = 0;

            foreach ($tenants as $tenant) {
                tenancy()->initialize($tenant);

                try {
                    // Verificar si la tabla existe
                    if (!DB::getSchemaBuilder()->hasTable('conversation_history')) {
                        continue;
                    }

                    // Eliminar conversaciones antiguas
                    $deleted = DB::table('conversation_history')
                        ->where('created_at', '<', now()->subHours($hours))
                        ->delete();

                    if ($deleted > 0) {
                        $totalDeleted += $deleted;
                        $tenantsProcessed++;
                        $this->line("  ✅ Tenant {$tenant->id}: {$deleted} registros eliminados");
                        Log::info("[CleanConversationHistory] Tenant {$tenant->id}: {$deleted} conversaciones eliminadas");
                    }

                } catch (\Exception $e) {
                    $this->error("  ❌ Error en tenant {$tenant->id}: {$e->getMessage()}");
                    Log::error("[CleanConversationHistory] Error en tenant {$tenant->id}: " . $e->getMessage());
                }

                tenancy()->end();
            }

            if ($totalDeleted > 0) {
                $this->info("✅ Total: {$totalDeleted} conversaciones eliminadas de {$tenantsProcessed} tenant(s)");
            } else {
                $this->info("ℹ️  No se encontraron conversaciones antiguas para eliminar");
            }

            return Command::SUCCESS;

        } catch (\Exception $e) {
            $this->error("❌ Error general: {$e->getMessage()}");
            Log::error("[CleanConversationHistory] Error general: " . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
