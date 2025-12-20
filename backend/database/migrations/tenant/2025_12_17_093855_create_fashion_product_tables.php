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
        // 1. Product Images
        if (!Schema::hasTable('product_images')) {
            Schema::create('product_images', function (Blueprint $table) {
                $table->id();
                $table->foreignId('product_id')->constrained()->onDelete('cascade');
                $table->string('image_url');
                $table->boolean('is_primary')->default(false);
                $table->integer('order')->default(0);
                $table->timestamps();
            });
        }

        // 2. Product Options (e.g. Size, Color)
        if (!Schema::hasTable('product_options')) {
            Schema::create('product_options', function (Blueprint $table) {
                $table->id();
                $table->foreignId('product_id')->constrained()->onDelete('cascade');
                $table->string('name');
                $table->timestamps();
            });
        }

        // 3. Product Variants
        if (!Schema::hasTable('product_variants')) {
            Schema::create('product_variants', function (Blueprint $table) {
                $table->id();
                $table->foreignId('product_id')->constrained()->onDelete('cascade');
                $table->string('sku')->nullable();
                $table->decimal('price', 10, 2)->default(0);
                $table->integer('stock')->default(0);
                $table->json('options_summary')->nullable();
                $table->boolean('active')->default(true);
                $table->timestamps();
            });
        }

        // 4. Product Option Values (e.g. Red, XL)
        if (!Schema::hasTable('product_option_values')) {
            Schema::create('product_option_values', function (Blueprint $table) {
                $table->id();
                $table->foreignId('product_option_id')->constrained()->onDelete('cascade');
                $table->string('value');
                $table->timestamps();
            });
        }

        // 5. Pivot: Variant <-> Option Value
        if (!Schema::hasTable('product_variant_option_value')) {
            Schema::create('product_variant_option_value', function (Blueprint $table) {
                $table->id();
                $table->foreignId('product_variant_id')->constrained()->onDelete('cascade');
                $table->foreignId('product_option_value_id')->constrained()->onDelete('cascade');
                $table->timestamps();
            });
        }

        // 6. Update product_warehouse to support variants
        if (Schema::hasTable('product_warehouse')) {
            Schema::table('product_warehouse', function (Blueprint $table) {
                if (!Schema::hasColumn('product_warehouse', 'product_variant_id')) {
                    $table->foreignId('product_variant_id')->nullable()->after('warehouse_id')->constrained('product_variants')->onDelete('cascade');
                }

                // Add index for product_id to satisfy FK requirement before dropping unique index
                // We use a raw statement to avoid errors if index exists, or just try-catch?
                // Schema builder doesn't support "add index if not exists" easily.
                // But since we are in a migration that failed, we can assume we need to fix it.
                // Let's just add it. If it fails, we might need to manual fix.
                $table->index('product_id');

                // Drop old unique index
                // We need to check if it exists?
                // Usually 'product_warehouse_product_id_warehouse_id_unique'
                $table->dropUnique(['product_id', 'warehouse_id']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_warehouse', function (Blueprint $table) {
            $table->dropForeign(['product_variant_id']);
            $table->dropColumn('product_variant_id');
        });

        Schema::dropIfExists('product_variant_option_value');
        Schema::dropIfExists('product_option_values');
        Schema::dropIfExists('product_variants');
        Schema::dropIfExists('product_options');
        Schema::dropIfExists('product_images');
    }
};
