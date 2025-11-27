<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('ai_plan_limits', function (Blueprint $table) {
            $table->id();
            $table->string('plan_name')->unique(); // free_trial, basic, premium, enterprise
            $table->integer('requests_per_hour')->default(0); // Límite de peticiones por hora
            $table->integer('requests_per_day')->default(0); // Límite de peticiones por día
            $table->integer('tokens_per_request')->default(0); // Tokens máximos por petición
            $table->integer('tokens_per_day')->default(0); // Tokens máximos por día
            $table->boolean('unlimited')->default(false); // Plan sin límites
            $table->timestamps();
        });

        // Insertar límites predefinidos para cada plan
        DB::table('ai_plan_limits')->insert([
            [
                'plan_name' => 'free_trial',
                'requests_per_hour' => 8,
                'requests_per_day' => 50,
                'tokens_per_request' => 500,
                'tokens_per_day' => 10000,
                'unlimited' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'plan_name' => 'basic',
                'requests_per_hour' => 30,
                'requests_per_day' => 300,
                'tokens_per_request' => 1000,
                'tokens_per_day' => 50000,
                'unlimited' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'plan_name' => 'premium',
                'requests_per_hour' => 100,
                'requests_per_day' => 1000,
                'tokens_per_request' => 2000,
                'tokens_per_day' => 200000,
                'unlimited' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'plan_name' => 'enterprise',
                'requests_per_hour' => 0,
                'requests_per_day' => 0,
                'tokens_per_request' => 0,
                'tokens_per_day' => 0,
                'unlimited' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_plan_limits');
    }
};
