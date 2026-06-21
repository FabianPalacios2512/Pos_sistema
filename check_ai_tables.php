<?php
// Check which tenant databases have ai_usage_logs table and count rows
require_once __DIR__ . '/backend/vendor/autoload.php';
$app = require_once __DIR__ . '/backend/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

$databases = DB::select('SHOW DATABASES');
foreach ($databases as $db) {
    $name = $db->Database;
    if (str_starts_with($name, 'tenant') && $name !== 'tenants') {
        $t = DB::select("SELECT COUNT(*) as cnt FROM information_schema.tables WHERE table_schema = ? AND table_name = 'ai_usage_logs'", [$name]);
        if ($t[0]->cnt > 0) {
            $rows = DB::select("SELECT COUNT(*) as total FROM {$name}.ai_usage_logs");
            echo "$name => ai_usage_logs EXISTS, rows: {$rows[0]->total}\n";
        } else {
            echo "$name => NO ai_usage_logs table\n";
        }
    }
}
