<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('credit_payments', function (Blueprint $table) {
            if (!Schema::hasColumn('credit_payments', 'cash_session_id')) {
                $table->unsignedBigInteger('cash_session_id')->nullable()->after('user_id');
                $table->foreign('cash_session_id')->references('id')->on('cash_sessions')->onDelete('set null');
            }
            if (!Schema::hasColumn('credit_payments', 'reference')) {
                $table->string('reference', 100)->nullable()->after('method');
            }
        });
    }

    public function down(): void
    {
        Schema::table('credit_payments', function (Blueprint $table) {
            if (Schema::hasColumn('credit_payments', 'cash_session_id')) {
                $table->dropForeign(['cash_session_id']);
                $table->dropColumn('cash_session_id');
            }
            if (Schema::hasColumn('credit_payments', 'reference')) {
                $table->dropColumn('reference');
            }
        });
    }
};
