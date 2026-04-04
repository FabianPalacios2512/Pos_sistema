<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Agrega el campo business_layout_type para determinar qué vista de POS cargar.
     * Valores: 'general' (3 columnas), 'fashion' (2 columnas boutique), 'fast_food' (2 columnas comida rápida)
     */
    public function up(): void
    {
        Schema::table('system_settings', function (Blueprint $table) {
            $table->string('business_layout_type', 50)->default('general')->after('store_type')
                  ->comment('Tipo de layout POS: general, fashion, fast_food');
            $table->boolean('business_layout_selected')->default(false)->after('business_layout_type')
                  ->comment('Si el usuario ya seleccionó su tipo de negocio');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('system_settings', function (Blueprint $table) {
            $table->dropColumn(['business_layout_type', 'business_layout_selected']);
        });
    }
};
