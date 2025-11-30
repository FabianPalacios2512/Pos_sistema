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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('sku')->unique();
            $table->string('barcode')->nullable()->unique();
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            // No crear foreign key aquí, se crea después en add_supplier_relation_to_products_table
            $table->unsignedBigInteger('supplier_id')->nullable();
            $table->decimal('cost_price', 10, 2);
            $table->decimal('sale_price', 10, 2);
            $table->decimal('wholesale_price', 10, 2)->nullable();
            $table->integer('current_stock')->default(0);
            $table->integer('min_stock')->default(5);
            $table->integer('max_stock')->nullable();
            $table->string('unit')->default('unidad'); // unidad, kg, litro, etc
            $table->boolean('manage_stock')->default(true);
            $table->boolean('active')->default(true);
            $table->string('image_url')->nullable();
            $table->json('tags')->nullable(); // Para búsquedas
            $table->timestamps();

            $table->index(['active']);
            $table->index(['sku']);
            $table->index(['barcode']);
            $table->index(['category_id']);
            $table->index(['current_stock']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
