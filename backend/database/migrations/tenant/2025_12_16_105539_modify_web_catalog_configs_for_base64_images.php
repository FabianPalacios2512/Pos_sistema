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
        Schema::table('web_catalog_configs', function (Blueprint $table) {
            // Cambiar de VARCHAR(255) a LONGTEXT para soportar imágenes base64
            $table->longText('logo_url')->nullable()->change();
            $table->longText('banner_url')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('web_catalog_configs', function (Blueprint $table) {
            // Revertir a VARCHAR(255)
            $table->string('logo_url', 255)->nullable()->change();
            $table->string('banner_url', 255)->nullable()->change();
        });
    }
};
