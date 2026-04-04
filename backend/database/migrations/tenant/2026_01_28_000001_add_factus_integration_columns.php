<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Agregar campos para integración con Factus (facturación electrónica DIAN)
     */
    public function up(): void
    {
        Schema::table('system_settings', function (Blueprint $table) {
            // Configuración de Factus
            if (!Schema::hasColumn('system_settings', 'factus_enabled')) {
                $table->boolean('factus_enabled')->default(false)->after('invoice_footer_message');
            }
            
            if (!Schema::hasColumn('system_settings', 'factus_sandbox')) {
                $table->boolean('factus_sandbox')->default(true)->after('factus_enabled');
            }
            
            if (!Schema::hasColumn('system_settings', 'factus_client_id')) {
                $table->string('factus_client_id', 255)->nullable()->after('factus_sandbox');
            }
            
            if (!Schema::hasColumn('system_settings', 'factus_client_secret')) {
                $table->string('factus_client_secret', 255)->nullable()->after('factus_client_id');
            }
            
            if (!Schema::hasColumn('system_settings', 'factus_username')) {
                $table->string('factus_username', 255)->nullable()->after('factus_client_secret');
            }
            
            if (!Schema::hasColumn('system_settings', 'factus_password')) {
                $table->string('factus_password', 255)->nullable()->after('factus_username');
            }
            
            // Rango de numeración predeterminado
            if (!Schema::hasColumn('system_settings', 'factus_numbering_range_id')) {
                $table->unsignedInteger('factus_numbering_range_id')->nullable()->after('factus_password');
            }
            
            // Municipio para el establecimiento
            if (!Schema::hasColumn('system_settings', 'factus_municipality_id')) {
                $table->string('factus_municipality_id', 10)->nullable()->after('factus_numbering_range_id');
            }
            
            // Nombre del establecimiento para Factus
            if (!Schema::hasColumn('system_settings', 'factus_establishment_name')) {
                $table->string('factus_establishment_name', 255)->nullable()->after('factus_municipality_id');
            }
        });
        
        // Agregar campos CUFE a la tabla de invoices para guardar datos de facturación electrónica
        Schema::table('invoices', function (Blueprint $table) {
            if (!Schema::hasColumn('invoices', 'cufe')) {
                $table->string('cufe', 96)->nullable()->after('notes')->index();
            }
            
            if (!Schema::hasColumn('invoices', 'factus_number')) {
                $table->string('factus_number', 50)->nullable()->after('cufe');
            }
            
            if (!Schema::hasColumn('invoices', 'qr_code')) {
                $table->text('qr_code')->nullable()->after('factus_number');
            }
            
            if (!Schema::hasColumn('invoices', 'qr_image')) {
                $table->longText('qr_image')->nullable()->after('qr_code');
            }
            
            if (!Schema::hasColumn('invoices', 'factus_status')) {
                $table->string('factus_status', 50)->nullable()->after('qr_image');
            }
            
            if (!Schema::hasColumn('invoices', 'factus_validated_at')) {
                $table->timestamp('factus_validated_at')->nullable()->after('factus_status');
            }
            
            if (!Schema::hasColumn('invoices', 'factus_response')) {
                $table->json('factus_response')->nullable()->after('factus_validated_at');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('system_settings', function (Blueprint $table) {
            $columns = [
                'factus_enabled',
                'factus_sandbox',
                'factus_client_id',
                'factus_client_secret',
                'factus_username',
                'factus_password',
                'factus_numbering_range_id',
                'factus_municipality_id',
                'factus_establishment_name'
            ];
            
            foreach ($columns as $column) {
                if (Schema::hasColumn('system_settings', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
        
        Schema::table('invoices', function (Blueprint $table) {
            $columns = [
                'cufe',
                'factus_number',
                'qr_code',
                'qr_image',
                'factus_status',
                'factus_validated_at',
                'factus_response'
            ];
            
            foreach ($columns as $column) {
                if (Schema::hasColumn('invoices', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
