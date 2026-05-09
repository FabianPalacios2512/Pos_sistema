<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('web_catalog_configs', function (Blueprint $table) {
            if (!Schema::hasColumn('web_catalog_configs', 'catalog_media')) {
                // JSON con imágenes y videos por componente de diseño
                // Estructura: { hero_images: [], lookbook_images: [], lookbook_video: '',
                //               bento_main: '', bento_detail: '', editorial_image: '' }
                $table->longText('catalog_media')->nullable()->after('banner_url');
            }
        });
    }

    public function down(): void
    {
        Schema::table('web_catalog_configs', function (Blueprint $table) {
            if (Schema::hasColumn('web_catalog_configs', 'catalog_media')) {
                $table->dropColumn('catalog_media');
            }
        });
    }
};
