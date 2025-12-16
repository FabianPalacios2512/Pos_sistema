<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('web_catalog_configs', function (Blueprint $table) {
            // Cambiar a LONGTEXT para soportar imágenes base64
            $table->longText('logo_url')->nullable()->change();
            $table->longText('banner_url')->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('web_catalog_configs', function (Blueprint $table) {
            $table->string('logo_url')->nullable()->change();
            $table->string('banner_url')->nullable()->change();
        });
    }
};
