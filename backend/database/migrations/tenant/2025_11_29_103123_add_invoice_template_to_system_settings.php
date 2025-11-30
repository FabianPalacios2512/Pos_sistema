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
        if (Schema::hasTable('system_settings')) {
            Schema::table('system_settings', function (Blueprint $table) {
                if (!Schema::hasColumn('system_settings', 'invoice_template')) {
                    $table->string('invoice_template', 50)->default('classic')->after('invoice_footer_message');
                }
                if (!Schema::hasColumn('system_settings', 'qr_style')) {
                    $table->string('qr_style', 50)->default('rounded')->after('invoice_template');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('system_settings', function (Blueprint $table) {
            $table->dropColumn(['invoice_template', 'qr_style']);
        });
    }
};
