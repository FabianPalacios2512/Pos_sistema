<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('stock_transfer_items')) {
            return;
        }

        if (Schema::hasColumn('stock_transfer_items', 'product_variant_id')) {
            return;
        }

        Schema::table('stock_transfer_items', function (Blueprint $table) {
            $table->unsignedBigInteger('product_variant_id')->nullable()->after('product_id');
            
            $table->foreign('product_variant_id')
                ->references('id')
                ->on('product_variants')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('stock_transfer_items')) {
            return;
        }

        if (!Schema::hasColumn('stock_transfer_items', 'product_variant_id')) {
            return;
        }

        Schema::table('stock_transfer_items', function (Blueprint $table) {
            try {
                $table->dropForeign(['product_variant_id']);
            } catch (\Throwable $e) {
                // Ignore missing FK constraint in inconsistent tenant schemas.
            }
            $table->dropColumn('product_variant_id');
        });
    }
};
