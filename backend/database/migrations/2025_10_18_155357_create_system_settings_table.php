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
        Schema::create('system_settings', function (Blueprint $table) {
            $table->id();

            // Información de la empresa
            $table->string('company_name')->default('Mi Empresa');
            $table->string('company_document')->nullable();
            $table->string('company_phone')->nullable();
            $table->string('company_email')->nullable();
            $table->text('company_address')->nullable();
            $table->string('company_logo')->nullable();

            // Configuración de IVA/Impuestos
            $table->boolean('iva_enabled')->default(true);
            $table->decimal('iva_percentage', 5, 2)->default(19.00);
            $table->string('iva_display_name')->default('IVA');

            // Configuración de facturación
            $table->string('invoice_prefix')->default('FACT-');
            $table->integer('invoice_number_start')->default(1);
            $table->integer('invoice_current_number')->default(1);
            $table->text('invoice_footer_message')->nullable();
            $table->boolean('require_customer')->default(false);

            // Configuración de descuentos
            $table->boolean('discounts_enabled')->default(true);
            $table->boolean('customer_discounts_enabled')->default(true);
            $table->boolean('promo_codes_enabled')->default(true);

            // Configuración de métodos de pago
            $table->json('payment_methods')->nullable(); // ['efectivo' => true, 'tarjeta' => true, etc.]

            // Configuración del POS
            $table->boolean('auto_apply_discounts')->default(true);
            $table->boolean('show_product_images')->default(true);
            $table->integer('products_per_page')->default(12);

            // Configuración de notificaciones
            $table->boolean('low_stock_alerts')->default(true);
            $table->integer('low_stock_threshold')->default(5);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('system_settings');
    }
};
