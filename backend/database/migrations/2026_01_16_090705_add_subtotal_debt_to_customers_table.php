<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * subtotal_debt = Deuda SIN recargo (para calcular crédito disponible)
     * current_debt = Deuda CON recargo (lo que el cliente realmente debe pagar)
     */
    public function up(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->decimal('subtotal_debt', 15, 2)->default(0)->after('current_debt')
                  ->comment('Deuda sin recargo - para calcular crédito disponible');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn('subtotal_debt');
        });
    }
};
