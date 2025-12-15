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
        Schema::create('web_catalog_configs', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_id')->index();
            
            // General
            $table->boolean('store_active')->default(true);
            
            // Brand Identity
            $table->string('logo_url')->nullable();
            $table->string('banner_url')->nullable();
            $table->string('primary_color', 7)->default('#10B981');
            $table->string('template', 50)->default('modern-grid'); // 'simple-list' or 'modern-grid'
            
            // Inventory Visibility
            $table->text('visible_categories')->nullable(); // JSON array of category IDs
            $table->boolean('hide_out_of_stock')->default(false);
            
            // Orders Configuration
            $table->string('whatsapp_number', 20)->default('+57');
            $table->text('custom_message')->nullable();
            
            // Business Rules
            $table->decimal('delivery_cost', 10, 2)->default(0.00);
            $table->decimal('minimum_order', 10, 2)->default(0.00);
            $table->boolean('sync_with_cash_register')->default(false);
            
            $table->timestamps();
            
            // Unique constraint: one config per tenant (redundant in tenant DB but harmless)
            // $table->unique('tenant_id'); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('web_catalog_configs');
    }
};
