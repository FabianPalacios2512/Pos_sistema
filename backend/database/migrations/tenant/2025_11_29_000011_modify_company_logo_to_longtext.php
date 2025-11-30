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
        if (Schema::hasTable('system_settings') && Schema::hasColumn('system_settings', 'company_logo')) {
            Schema::table('system_settings', function (Blueprint $table) {
                // Cambiar company_logo de TEXT a LONGTEXT para soportar imágenes grandes en base64
                $table->longText('company_logo')->nullable()->change();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('system_settings', function (Blueprint $table) {
            $table->text('company_logo')->nullable()->change();
        });
    }
};
