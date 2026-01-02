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
        Schema::table('pending_payments', function (Blueprint $table) {
            // 🔐 Token de verificación para acceso seguro sin webhook
            $table->string('verification_token')->nullable()->after('reference');
            $table->string('gateway')->default('wompi')->after('customer_email'); // epayco o wompi
            
            // Actualizar estados para incluir 'approved' y 'rejected'
            $table->dropColumn('status');
        });
        
        // Agregar columna status con nuevos valores
        Schema::table('pending_payments', function (Blueprint $table) {
            $table->enum('status', ['pending', 'approved', 'rejected', 'completed', 'failed'])
                ->default('pending')
                ->after('gateway');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pending_payments', function (Blueprint $table) {
            $table->dropColumn('verification_token');
            $table->dropColumn('gateway');
            $table->dropColumn('status');
        });
        
        // Restaurar columna status original
        Schema::table('pending_payments', function (Blueprint $table) {
            $table->enum('status', ['pending', 'completed', 'failed'])->default('pending');
        });
    }
};
