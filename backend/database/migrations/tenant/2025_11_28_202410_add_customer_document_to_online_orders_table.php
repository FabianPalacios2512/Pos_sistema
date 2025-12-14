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
        if (Schema::hasTable('online_orders') && !Schema::hasColumn('online_orders', 'customer_document')) {
            Schema::table('online_orders', function (Blueprint $table) {
                $table->string('customer_document', 20)->nullable()->after('customer_phone');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('online_orders', function (Blueprint $table) {
            $table->dropColumn('customer_document');
        });
    }
};
