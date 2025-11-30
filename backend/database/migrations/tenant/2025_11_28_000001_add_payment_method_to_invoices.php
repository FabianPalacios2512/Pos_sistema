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
        Schema::table('invoices', function (Blueprint $table) {
            // Solo agregar surcharge_amount si no existe
            if (!Schema::hasColumn('invoices', 'surcharge_amount')) {
                // Verificar si payment_method existe para usar after(), sino agregar al final
                if (Schema::hasColumn('invoices', 'payment_method')) {
                    $table->decimal('surcharge_amount', 12, 2)->default(0)->after('payment_method');
                } else {
                    $table->decimal('surcharge_amount', 12, 2)->default(0);
                }
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            if (Schema::hasColumn('invoices', 'surcharge_amount')) {
                $table->dropColumn('surcharge_amount');
            }
        });
    }
};
