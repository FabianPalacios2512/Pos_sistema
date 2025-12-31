<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 🔔 Agrega configuración de recordatorios automáticos para CreditiTenda
     */
    public function up(): void
    {
        // Agregar campos de configuración de recordatorios a system_settings
        if (!Schema::hasColumn('system_settings', 'reminder_frequency')) {
            Schema::table('system_settings', function (Blueprint $table) {
                $table->string('reminder_frequency')->default('manual')
                      ->comment('Frecuencia de recordatorios: manual, daily, weekly, biweekly');
            });
        }

        if (!Schema::hasColumn('system_settings', 'reminder_send_hour')) {
            Schema::table('system_settings', function (Blueprint $table) {
                $table->integer('reminder_send_hour')->default(9)
                      ->comment('Hora del día para enviar recordatorios (6-20)');
            });
        }

        if (!Schema::hasColumn('system_settings', 'reminder_min_days_overdue')) {
            Schema::table('system_settings', function (Blueprint $table) {
                $table->integer('reminder_min_days_overdue')->default(1)
                      ->comment('Días mínimos de mora para enviar recordatorio');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('system_settings', function (Blueprint $table) {
            $table->dropColumn([
                'reminder_frequency',
                'reminder_send_hour',
                'reminder_min_days_overdue'
            ]);
        });
    }
};
