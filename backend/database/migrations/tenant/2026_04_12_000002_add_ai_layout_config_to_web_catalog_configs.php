<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('web_catalog_configs', function (Blueprint $table) {
            $table->json('ai_layout_config')->nullable()->after('ai_cross_sell_messages');
        });
    }

    public function down(): void
    {
        Schema::table('web_catalog_configs', function (Blueprint $table) {
            $table->dropColumn('ai_layout_config');
        });
    }
};
