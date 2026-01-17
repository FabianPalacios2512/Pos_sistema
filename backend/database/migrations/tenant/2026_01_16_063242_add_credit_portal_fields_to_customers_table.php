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
            if (!Schema::hasColumn('customers', 'credit_id')) {
                $table->string('credit_id', 20)->nullable()->after('credit_active');
            }

            // Token de acceso directo (para links en email/whatsapp)
            if (!Schema::hasColumn('customers', 'credit_access_token')) {
                $table->string('credit_access_token', 64)->nullable()->after('credit_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            if (Schema::hasColumn('customers', 'credit_id')) {
                $table->dropColumn('credit_id');
            }
            if (Schema::hasColumn('customers', 'credit_access_token')) {
                $table->dropColumn('credit_access_token');
            }
        });
    }
};
