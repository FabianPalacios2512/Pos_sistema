<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('web_catalog_configs', function (Blueprint $table) {
            // AI Onboarding - Business Description
            $table->text('business_description')->nullable()->after('sync_with_cash_register');

            // Pilar 1: Visual IA - Colors & Fonts
            $table->json('ai_color_palette')->nullable()->after('business_description');
            $table->json('ai_fonts')->nullable()->after('ai_color_palette');
            $table->string('ai_recommended_template', 50)->nullable()->after('ai_fonts');

            // Pilar 2: Content IA - Generated Texts
            $table->json('ai_banner_texts')->nullable()->after('ai_recommended_template');
            $table->text('ai_about_us')->nullable()->after('ai_banner_texts');
            $table->json('ai_value_messages')->nullable()->after('ai_about_us');
            $table->json('ai_announcements')->nullable()->after('ai_value_messages');

            // Pilar 4: Conversion IA - Cross-sell Messages
            $table->json('ai_cross_sell_messages')->nullable()->after('ai_announcements');

            // Metadata
            $table->timestamp('ai_generated_at')->nullable()->after('ai_cross_sell_messages');
        });
    }

    public function down(): void
    {
        Schema::table('web_catalog_configs', function (Blueprint $table) {
            $table->dropColumn([
                'business_description',
                'ai_color_palette',
                'ai_fonts',
                'ai_recommended_template',
                'ai_banner_texts',
                'ai_about_us',
                'ai_value_messages',
                'ai_announcements',
                'ai_cross_sell_messages',
                'ai_generated_at',
            ]);
        });
    }
};
