<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('staff_transfers')) {
            Schema::create('staff_transfers', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id');
                $table->unsignedBigInteger('from_warehouse_id');
                $table->unsignedBigInteger('to_warehouse_id');
                $table->unsignedBigInteger('transferred_by');
                $table->string('reason')->nullable();
                $table->unsignedBigInteger('closed_session_id')->nullable();
                $table->timestamps();

                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
                $table->foreign('from_warehouse_id')->references('id')->on('warehouses')->onDelete('cascade');
                $table->foreign('to_warehouse_id')->references('id')->on('warehouses')->onDelete('cascade');
                $table->foreign('transferred_by')->references('id')->on('users')->onDelete('cascade');
                $table->foreign('closed_session_id')->references('id')->on('cash_sessions')->onDelete('set null');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('staff_transfers');
    }
};
