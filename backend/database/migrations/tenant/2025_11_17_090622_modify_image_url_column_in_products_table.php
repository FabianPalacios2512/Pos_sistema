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
        if (Schema::hasTable('products') && Schema::hasColumn('products', 'image_url')) {
            Schema::table('products', function (Blueprint $table) {
                // Cambiar el tipo de la columna image_url de string (255 chars) a longText
                // para soportar imágenes base64 que pueden ser muy grandes
                $table->longText('image_url')->nullable()->change();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Revertir al tipo original
            $table->string('image_url')->nullable()->change();
        });
    }
};
