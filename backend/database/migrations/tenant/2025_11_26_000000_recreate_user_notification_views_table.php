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
        Schema::dropIfExists('user_notification_views');

        Schema::create('user_notification_views', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('type', ['movements', 'alerts']); // Tipo de notificación
            $table->timestamp('last_viewed_at'); // Cuándo vio por última vez
            $table->json('viewed_items')->nullable(); // IDs específicos que vio (opcional)
            $table->timestamps();

            // Índice único para evitar duplicados por usuario y tipo
            $table->unique(['user_id', 'type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_notification_views');
    }
};
