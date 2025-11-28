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
            // Solo agregar surcharge_amount si no existe (payment_method y cash_session_id ya existen)
            if (!Schema::hasColumn('invoices', 'surcharge_amount')) {
                $table->decimal('surcharge_amount', 12, 2)->default(0)->after('payment_method');
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
