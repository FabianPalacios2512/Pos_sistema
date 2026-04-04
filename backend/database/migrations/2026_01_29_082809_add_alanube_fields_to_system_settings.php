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
        Schema::table('system_settings', function (Blueprint $table) {
            // Proveedor de facturación electrónica
            $table->enum('electronic_invoice_provider', ['none', 'factus', 'alanube'])
                  ->default('none')
                  ->after('factus_enabled');
            
            // Campos para Alanube
            $table->string('alanube_company_id', 50)->nullable()->after('electronic_invoice_provider');
            $table->string('alanube_test_set_id', 50)->nullable()->after('alanube_company_id');
            $table->enum('alanube_status', ['pending', 'testing', 'active', 'error'])
                  ->default('pending')
                  ->after('alanube_test_set_id');
            
            // Datos fiscales del comercio (para DIAN)
            $table->string('company_dv', 1)->nullable()->after('company_document');
            $table->string('company_city_code', 10)->nullable()->after('company_address');
            $table->string('company_department_code', 5)->nullable()->after('company_city_code');
            $table->string('tax_regime', 20)->default('R-99-PN')->after('company_department_code');
            
            // Datos de resolución DIAN (para producción)
            $table->string('dian_resolution_number', 50)->nullable()->after('tax_regime');
            $table->string('dian_prefix', 10)->nullable()->after('dian_resolution_number');
            $table->bigInteger('dian_min_number')->nullable()->after('dian_prefix');
            $table->bigInteger('dian_max_number')->nullable()->after('dian_min_number');
            $table->bigInteger('dian_current_number')->nullable()->after('dian_max_number');
            $table->date('dian_start_date')->nullable()->after('dian_current_number');
            $table->date('dian_end_date')->nullable()->after('dian_start_date');
            $table->string('dian_technical_key', 100)->nullable()->after('dian_end_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('system_settings', function (Blueprint $table) {
            $table->dropColumn([
                'electronic_invoice_provider',
                'alanube_company_id',
                'alanube_test_set_id',
                'alanube_status',
                'company_dv',
                'company_city_code',
                'company_department_code',
                'tax_regime',
                'dian_resolution_number',
                'dian_prefix',
                'dian_min_number',
                'dian_max_number',
                'dian_current_number',
                'dian_start_date',
                'dian_end_date',
                'dian_technical_key'
            ]);
        });
    }
};
