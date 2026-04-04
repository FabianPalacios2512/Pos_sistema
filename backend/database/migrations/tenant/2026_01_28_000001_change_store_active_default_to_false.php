<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Cambia el valor por defecto de store_active a FALSE para que
     * las nuevas tiendas no tengan el catálogo web activo por defecto.
     * Esto evita que se muestren productos antes de configurar el catálogo.
     */
    public function up(): void
    {
        if (Schema::hasTable('web_catalog_configs')) {
            Schema::table('web_catalog_configs', function (Blueprint $table) {
                $table->boolean('store_active')->default(false)->change();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('web_catalog_configs')) {
            Schema::table('web_catalog_configs', function (Blueprint $table) {
                $table->boolean('store_active')->default(true)->change();
            });
        }
    }
};
