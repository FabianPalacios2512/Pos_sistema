<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ValidateAndFixTenantSchemas extends Command
{
    protected $signature = 'tenants:validate-schemas {--fix : Aplicar correcciones automáticamente}';
    protected $description = 'Valida y opcionalmente repara los esquemas de todas las bases de datos de tenants';

    // Esquema esperado de la tabla customers
    protected $expectedSchema = [
        'credit_photo' => [
            'type' => 'text',
            'nullable' => true,
            'after' => 'credit_active',
            'migration' => 'ALTER TABLE customers ADD COLUMN credit_photo TEXT NULL AFTER credit_active;'
        ],
        // Agregar aquí más columnas cuando se detecten problemas futuros
    ];

    public function handle()
    {
        $this->info('═══════════════════════════════════════════════');
        $this->info('   VALIDACIÓN DE ESQUEMAS TENANTS');
        $this->info('═══════════════════════════════════════════════');
        $this->newLine();

        $fix = $this->option('fix');

        if (!$fix) {
            $this->warn('⚠️  Modo SOLO LECTURA - Use --fix para aplicar correcciones');
            $this->newLine();
        }

        try {
            // Obtener todos los tenants
            $tenants = DB::connection('mysql')->table('tenants')->get();

            if ($tenants->isEmpty()) {
                $this->error('❌ No se encontraron tenants');
                return 1;
            }

            $this->info("📊 Tenants encontrados: " . count($tenants));
            $this->newLine();

            $summary = [
                'total' => count($tenants),
                'ok' => 0,
                'issues' => 0,
                'fixed' => 0,
                'errors' => 0
            ];

            foreach ($tenants as $tenant) {
                $dbName = 'tenant' . $tenant->id;
                $this->line("┌─ Validando: <fg=cyan;options=bold>$dbName</> (Negocio: {$tenant->business_name})");

                try {
                    // Verificar que la base de datos existe
                    $databases = DB::select("SHOW DATABASES LIKE '$dbName'");
                    if (empty($databases)) {
                        $this->warn("│  ⚠️  Base de datos no existe, saltando...");
                        $this->line("└─");
                        $this->newLine();
                        $summary['errors']++;
                        continue;
                    }

                    // Obtener columnas actuales
                    $currentColumns = DB::select("SELECT COLUMN_NAME
                                                 FROM INFORMATION_SCHEMA.COLUMNS
                                                 WHERE TABLE_SCHEMA = '$dbName'
                                                 AND TABLE_NAME = 'customers'");

                    if (empty($currentColumns)) {
                        $this->error("│  ❌ Tabla 'customers' no existe");
                        $this->line("└─");
                        $this->newLine();
                        $summary['errors']++;
                        continue;
                    }

                    $currentColumnNames = array_map(fn($col) => $col->COLUMN_NAME, $currentColumns);

                    // Verificar cada columna esperada
                    $needsFix = false;
                    $fixesApplied = [];

                    foreach ($this->expectedSchema as $columnName => $columnInfo) {
                        if (!in_array($columnName, $currentColumnNames)) {
                            $needsFix = true;
                            $this->warn("│  ⚠️  Falta columna: <fg=yellow;options=bold>$columnName</>");
                            $summary['issues']++;

                            // Aplicar la migración si está en modo fix
                            if ($fix) {
                                try {
                                    DB::connection('mysql')->statement("USE `$dbName`");
                                    DB::connection('mysql')->statement($columnInfo['migration']);

                                    $this->info("│  ✅ Columna agregada: <fg=green;options=bold>$columnName</>");
                                    $fixesApplied[] = $columnName;
                                } catch (\Exception $e) {
                                    $this->error("│  ❌ Error al agregar '$columnName': " . $e->getMessage());
                                }
                            }
                        }
                    }

                    // No es necesario regresar a otra base de datos

                    if (!$needsFix) {
                        $this->info("│  ✅ Esquema correcto");
                        $summary['ok']++;
                    } elseif ($fix && !empty($fixesApplied)) {
                        $this->info("│  🔧 Correcciones aplicadas: " . count($fixesApplied));
                        $summary['fixed']++;
                    } elseif (!$fix) {
                        $this->warn("│  🔍 Requiere corrección (use --fix)");
                    } else {
                        $summary['errors']++;
                    }

                } catch (\Exception $e) {
                    $this->error("│  ❌ Error: " . $e->getMessage());
                    $summary['errors']++;
                }

                $this->line("└─");
                $this->newLine();
            }

            // Resumen final
            $this->info('═══════════════════════════════════════════════');
            $this->info('                  RESUMEN FINAL');
            $this->info('═══════════════════════════════════════════════');
            $this->newLine();

            $this->line("Total tenants:       <fg=blue;options=bold>{$summary['total']}</>");
            $this->line("✅ Sin problemas:   <fg=green;options=bold>{$summary['ok']}</>");

            if ($fix) {
                $this->line("🔧 Corregidos:      <fg=yellow;options=bold>{$summary['fixed']}</>");
            } else {
                $this->line("⚠️  Con problemas:  <fg=yellow;options=bold>{$summary['issues']}</>");
            }

            $this->line("❌ Con errores:     <fg=red;options=bold>{$summary['errors']}</>");
            $this->newLine();

            if ($fix && $summary['fixed'] > 0) {
                $this->info('✨ Se aplicaron correcciones exitosamente');
                $this->newLine();
            }

            if (!$fix && $summary['issues'] > 0) {
                $this->warn('💡 Ejecute: php artisan tenants:validate-schemas --fix');
                $this->newLine();
            }

            if ($summary['errors'] > 0) {
                $this->warn('⚠️  Algunos tenants requieren atención manual');
                $this->newLine();
                return 1;
            }

            $this->info('🎉 ¡Todos los esquemas están sincronizados!');
            $this->newLine();

            return 0;

        } catch (\Exception $e) {
            $this->error('❌ ERROR CRÍTICO: ' . $e->getMessage());
            $this->error($e->getTraceAsString());
            return 1;
        }
    }
}
