<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('cash_sessions')) {
            DB::statement("ALTER TABLE cash_sessions MODIFY COLUMN status ENUM('open', 'closed', 'forced_closed') NOT NULL DEFAULT 'open'");
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('cash_sessions')) {
            // First update any forced_closed back to closed
            DB::table('cash_sessions')->where('status', 'forced_closed')->update(['status' => 'closed']);
            DB::statement("ALTER TABLE cash_sessions MODIFY COLUMN status ENUM('open', 'closed') NOT NULL DEFAULT 'open'");
        }
    }
};
