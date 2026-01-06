<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Agrega el campo 'store_type' para diferenciar productos creados en tienda general vs moda
     *
     * - 'general': Productos tradicionales (sin variantes complejas)
     * - 'fashion': Productos de moda (con o sin variantes de talla/color)
     */
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->enum('store_type', ['general', 'fashion'])
                  ->default('general')
                  ->after('product_type')
                  ->comment('Tipo de tienda donde se creó el producto');
        });

        // 🔄 Actualizar productos existentes con variantes como 'fashion'
        \DB::statement("
            UPDATE products
            SET store_type = 'fashion'
            WHERE product_type = 'variable'
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('store_type');
        });
    }
};
