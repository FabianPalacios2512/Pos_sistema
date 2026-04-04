<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('biometric_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->text('base_image_path')->nullable()->comment('Ruta de la imagen base almacenada');
            $table->json('descriptors_json')->comment('Float32Array de 128 valores serializado como JSON');
            $table->boolean('active')->default(true);
            $table->timestamp('enrolled_at')->useCurrent();
            $table->timestamps();

            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('biometric_profiles');
    }
};
