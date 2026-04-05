<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('attendance_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('event_type', ['entry', 'exit', 'break_start', 'break_end'])->comment('Tipo de evento: entrada, salida, inicio/fin de break');
            $table->timestamp('event_at')->useCurrent()->comment('Momento exacto del punteo');
            $table->text('captured_image_path')->nullable()->comment('Ruta de imagen capturada para auditoría');
            $table->decimal('verification_score', 5, 4)->comment('Distancia euclidiana (0=perfecto, <0.4=match)');
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'event_at']);
            $table->index('event_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendance_logs');
    }
};
