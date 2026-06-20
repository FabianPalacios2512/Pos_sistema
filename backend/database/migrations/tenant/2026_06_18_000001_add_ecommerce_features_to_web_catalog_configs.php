<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('web_catalog_configs', function (Blueprint $table) {
            $table->json('ai_ecommerce_features')->nullable()->after('ai_layout_config');
            $table->json('ai_fake_reviews')->nullable()->after('ai_ecommerce_features');
        });
    }

    public function down(): void
    {
        Schema::table('web_catalog_configs', function (Blueprint $table) {
            $table->dropColumn(['ai_ecommerce_features', 'ai_fake_reviews']);
        });
    }
};
