<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Change severity column to support 'info' level
        // Since it's a VARCHAR(20), no schema change needed
        // Just ensure the column allows 'info' values (it already does since it's varchar)

        // Add index on severity for filtering
        if (!$this->hasIndex('tenant_error_logs', 'tenant_error_logs_severity_index')) {
            DB::statement('ALTER TABLE tenant_error_logs ADD INDEX tenant_error_logs_severity_index (severity)');
        }
    }

    public function down(): void
    {
        // Remove severity index if exists
        try {
            DB::statement('ALTER TABLE tenant_error_logs DROP INDEX tenant_error_logs_severity_index');
        } catch (\Throwable $e) {
            // Index might not exist
        }
    }

    private function hasIndex(string $table, string $indexName): bool
    {
        $indexes = DB::select("SHOW INDEX FROM {$table} WHERE Key_name = ?", [$indexName]);
        return count($indexes) > 0;
    }
};
