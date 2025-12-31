<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 🎯 CrediTienda: Agregar campo para foto de cliente con crédito
     */
    public function up(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->string('credit_photo', 500)->nullable()->after('credit_active')
                  ->comment('URL o path de la foto del cliente para CrediTienda');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn('credit_photo');
        });
    }
};
