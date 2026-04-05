<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('attendance_logs')) {
            DB::statement("ALTER TABLE attendance_logs MODIFY COLUMN event_type ENUM('entry','exit','break_start','break_end') NOT NULL");
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('attendance_logs')) {
            DB::statement("ALTER TABLE attendance_logs MODIFY COLUMN event_type ENUM('entry','exit') NOT NULL");
        }
    }
};
