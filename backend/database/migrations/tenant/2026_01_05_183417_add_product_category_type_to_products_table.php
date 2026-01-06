<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * PROBLEMA 3: Agregar campo para distinguir productos "general" vs "fashion"
     * Independientemente de si tienen variantes o no
     */
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Campo para identificar si el producto es de tienda general o de moda
            $table->enum('store_category', ['general', 'fashion'])
                  ->default('general')
                  ->after('product_type')
                  ->comment('Categoría de tienda: general (retail) o fashion (moda con estilos)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('store_category');
        });
    }
};
