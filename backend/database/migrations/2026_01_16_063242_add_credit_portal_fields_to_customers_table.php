<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * 🎯 Portal de Créditos Públicos
     * - credit_id: ID único visible al cliente (ej: CRD-000001)
     * - credit_access_token: Token para acceso directo via link
     */
    public function up(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            // ID de crédito visible al cliente (ej: CRD-000001)
            $table->string('credit_id', 20)->nullable()->unique()->after('credit_active');

            // Token de acceso directo (para links en email/whatsapp)
            $table->string('credit_access_token', 64)->nullable()->unique()->after('credit_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn(['credit_id', 'credit_access_token']);
        });
    }
};
