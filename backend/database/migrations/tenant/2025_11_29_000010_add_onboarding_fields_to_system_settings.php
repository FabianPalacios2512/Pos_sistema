<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('system_settings')) {
            Schema::table('system_settings', function (Blueprint $table) {
// Configuración de plantilla de factura
                        if (!Schema::hasColumn('system_settings', 'invoice_template')) {
                            $table->string('invoice_template')->default('classic')->after('invoice_footer_message');
                        }
                        // Estilo del código QR
                        if (!Schema::hasColumn('system_settings', 'qr_style')) {
                            $table->string('qr_style')->default('rounded')->after('invoice_template');
                        }
                        // Número de WhatsApp Business
                        if (!Schema::hasColumn('system_settings', 'whatsapp_business_number')) {
                            $table->string('whatsapp_business_number')->nullable()->after('company_phone');
                        }
                        // Flag de onboarding completado
                        if (!Schema::hasColumn('system_settings', 'onboarding_completed')) {
                            $table->boolean('onboarding_completed')->default(false)->after('qr_style');
                        }
            
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
                'invoice_template',
                'qr_style',
                'whatsapp_business_number',
                'onboarding_completed'
            ]);
        });
    }
};
