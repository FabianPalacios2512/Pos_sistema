<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Agrega soporte para diferentes Unidades de Medida (UOM) en productos.
     * Permite ventas a granel con cantidades decimales (kg, metros, litros, etc).
     */
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // 📏 Unidad de medida del producto
            $table->enum('measurement_unit', [
                'unit',  // Unidades (Default - iPhones, TVs, etc)
                'kg',    // Kilogramos (Papas, Carne, etc)
                'g',     // Gramos (Especias, Café, etc)
                'm',     // Metros (Tela, Cable, etc)
                'cm',    // Centímetros (Cinta, etc)
                'l',     // Litros (Gasolina, Leche, etc)
                'ml'     // Mililitros (Perfumes, Medicinas, etc)
            ])->default('unit')->after('sale_price')
              ->comment('Unidad de medida: unit=Unidades, kg=Kilogramos, g=Gramos, m=Metros, cm=Centímetros, l=Litros, ml=Mililitros');

            // ✅ ¿Permite decimales en cantidad?
            $table->boolean('allow_decimal')->default(false)->after('measurement_unit')
                  ->comment('Si true, permite cantidades decimales (ej: 1.5 kg). Si false, solo enteros (ej: 2 unidades)');
            
            // 📝 Índice para búsquedas
            $table->index('measurement_unit');
        });

        // 🔄 Actualizar productos existentes
        DB::statement("
            UPDATE products 
            SET allow_decimal = CASE 
                WHEN measurement_unit IN ('kg', 'g', 'm', 'cm', 'l', 'ml') THEN true
                ELSE false
            END
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex(['measurement_unit']);
            $table->dropColumn(['measurement_unit', 'allow_decimal']);
        });
    }
};
