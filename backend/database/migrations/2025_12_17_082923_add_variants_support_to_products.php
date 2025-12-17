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
        // 1. Modificar tabla products
        Schema::table('products', function (Blueprint $table) {
            $table->enum('product_type', ['simple', 'variable'])->default('simple')->after('id');
            
            // Hacer campos nullable para productos variables
            $table->string('sku')->nullable()->change();
            $table->string('barcode')->nullable()->change();
            $table->decimal('cost_price', 10, 2)->nullable()->change();
            $table->decimal('sale_price', 10, 2)->nullable()->change();
        });

        // 2. Crear tabla product_options (Atributos: Talla, Color)
        Schema::create('product_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->string('name'); // Ej: "Talla", "Color"
            $table->timestamps();
        });

        // 3. Crear tabla product_option_values (Valores: S, M, Rojo)
        Schema::create('product_option_values', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_option_id')->constrained('product_options')->onDelete('cascade');
            $table->string('value'); // Ej: "S", "Rojo"
            $table->timestamps();
        });

        // 4. Crear tabla product_variants (Las combinaciones)
        Schema::create('product_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->string('sku')->unique()->nullable();
            $table->string('barcode')->unique()->nullable();
            $table->decimal('price', 10, 2)->nullable(); // Precio específico de la variante
            $table->integer('stock')->default(0); // Stock caché
            $table->json('options_summary')->nullable(); // Resumen rápido: {"Talla": "S", "Color": "Azul"}
            $table->timestamps();
        });

        // 5. Crear tabla pivote product_variant_option_value
        Schema::create('product_variant_option_value', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_variant_id')->constrained('product_variants')->onDelete('cascade');
            $table->foreignId('product_option_value_id')->constrained('product_option_values')->onDelete('cascade');
        });

        // 6. Crear tabla product_images (Galería)
        Schema::create('product_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->text('image_url');
            $table->boolean('is_primary')->default(false);
            $table->integer('order')->default(0);
            $table->timestamps();
        });

        // 7. Actualizar tabla product_warehouse (Inventario Multi-sede)
        Schema::table('product_warehouse', function (Blueprint $table) {
            $table->foreignId('product_variant_id')->nullable()->after('product_id')->constrained('product_variants')->onDelete('cascade');
            // Índice compuesto para búsquedas rápidas
            $table->index(['product_id', 'product_variant_id', 'warehouse_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // 1. Revertir cambios en product_warehouse
        Schema::table('product_warehouse', function (Blueprint $table) {
            $table->dropForeign(['product_variant_id']);
            $table->dropColumn('product_variant_id');
            $table->dropIndex(['product_id', 'product_variant_id', 'warehouse_id']);
        });

        // 2. Eliminar tablas nuevas
        Schema::dropIfExists('product_images');
        Schema::dropIfExists('product_variant_option_value');
        Schema::dropIfExists('product_variants');
        Schema::dropIfExists('product_option_values');
        Schema::dropIfExists('product_options');

        // 3. Revertir cambios en products
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('product_type');
            // Nota: Revertir nullable a not null puede fallar si hay datos nulos, 
            // así que lo dejamos nullable o requeriría limpieza de datos.
            // Por seguridad en rollback, solo quitamos la columna nueva.
        });
    }
};
