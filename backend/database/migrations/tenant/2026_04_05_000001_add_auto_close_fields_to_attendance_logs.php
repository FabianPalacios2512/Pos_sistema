<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('attendance_logs', function (Blueprint $table) {
            if (!Schema::hasColumn('attendance_logs', 'closed_by')) {
                $table->string('closed_by', 20)->default('user')->after('user_agent');
            }
            if (!Schema::hasColumn('attendance_logs', 'is_auto_closed')) {
                $table->boolean('is_auto_closed')->default(false)->after('closed_by');
            }
            if (!Schema::hasColumn('attendance_logs', 'auto_close_note')) {
                $table->text('auto_close_note')->nullable()->after('is_auto_closed');
            }
        });
    }

    public function down(): void
    {
        Schema::table('attendance_logs', function (Blueprint $table) {
            $table->dropColumn(['closed_by', 'is_auto_closed', 'auto_close_note']);
        });
    }
};
