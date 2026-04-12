<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Cache;

class SystemToolsController extends Controller
{
    /**
     * System health check - real-time server metrics, services, CPU, RAM, disk, DB
     */
    public function health()
    {
        try {
            // ==================== SERVER INFO ====================
            $phpVersion = phpversion();
            $laravelVersion = app()->version();
            $serverSoftware = $_SERVER['SERVER_SOFTWARE'] ?? 'CLI';
            $os = php_uname('s') . ' ' . php_uname('r');
            $hostname = gethostname();

            // ==================== CPU (Real-time) ====================
            $cpu = $this->getCpuInfo();

            // ==================== RAM (Real-time) ====================
            $ram = $this->getRamInfo();

            // ==================== System Uptime ====================
            $uptime = $this->getUptime();

            // ==================== Load Average ====================
            $loadAvg = $this->getLoadAverage();

            // ==================== Network Interfaces ====================
            $network = $this->getNetworkInfo();

            // ==================== PHP Memory ====================
            $memoryLimit = ini_get('memory_limit');
            $memoryUsage = round(memory_get_usage(true) / 1024 / 1024, 2);
            $memoryPeak = round(memory_get_peak_usage(true) / 1024 / 1024, 2);

            // ==================== Disk ====================
            $diskFree = disk_free_space('/');
            $diskTotal = disk_total_space('/');
            $diskUsed = $diskTotal - $diskFree;
            $diskPercent = round(($diskUsed / $diskTotal) * 100, 1);

            // ==================== Database check ====================
            $dbStatus = 'connected';
            $dbVersion = '';
            $dbSize = 0;
            $dbUptime = 0;
            $dbConnections = 0;
            $dbMaxConnections = 0;
            $dbQueries = 0;
            try {
                $dbVersion = DB::connection('mysql')->select('SELECT VERSION() as version')[0]->version;
                $dbName = config('database.connections.mysql.database');
                $sizeResult = DB::connection('mysql')->select(
                    "SELECT SUM(data_length + index_length) as size FROM information_schema.TABLES WHERE table_schema = ?",
                    [$dbName]
                );
                $dbSize = round(($sizeResult[0]->size ?? 0) / 1024 / 1024, 2);

                // MySQL real-time stats
                $uptimeResult = DB::select("SHOW GLOBAL STATUS LIKE 'Uptime'");
                $dbUptime = (int) ($uptimeResult[0]->Value ?? 0);
                $connResult = DB::select("SHOW GLOBAL STATUS LIKE 'Threads_connected'");
                $dbConnections = (int) ($connResult[0]->Value ?? 0);
                $maxConnResult = DB::select("SHOW VARIABLES LIKE 'max_connections'");
                $dbMaxConnections = (int) ($maxConnResult[0]->Value ?? 0);
                $queryResult = DB::select("SHOW GLOBAL STATUS LIKE 'Questions'");
                $dbQueries = (int) ($queryResult[0]->Value ?? 0);
            } catch (\Exception $e) {
                $dbStatus = 'error';
            }

            // ==================== Tenant databases ====================
            $tenantDbs = [];
            $totalTenantSize = 0;
            try {
                $tenants = DB::connection('mysql')->table('tenants')->get();
                foreach ($tenants as $tenant) {
                    $tenantDbName = 'tenant_' . $tenant->id;
                    try {
                        $sizeResult = DB::connection('mysql')->select(
                            "SELECT SUM(data_length + index_length) as size FROM information_schema.TABLES WHERE table_schema = ?",
                            [$tenantDbName]
                        );
                        $size = round(($sizeResult[0]->size ?? 0) / 1024 / 1024, 2);
                        $totalTenantSize += $size;

                        $tableCount = DB::connection('mysql')->select(
                            "SELECT COUNT(*) as count FROM information_schema.TABLES WHERE table_schema = ?",
                            [$tenantDbName]
                        );

                        $tenantData = json_decode($tenant->data, true);

                        $tenantDbs[] = [
                            'id' => $tenant->id,
                            'database' => $tenantDbName,
                            'size_mb' => $size,
                            'tables' => $tableCount[0]->count ?? 0,
                            'business_name' => $tenantData['business_name'] ?? $tenant->id,
                            'status' => $tenantData['status'] ?? 'active',
                        ];
                    } catch (\Exception $e) {
                        $tenantDbs[] = [
                            'id' => $tenant->id,
                            'database' => $tenantDbName,
                            'size_mb' => 0,
                            'tables' => 0,
                            'business_name' => $tenant->id,
                            'status' => 'error',
                        ];
                    }
                }
            } catch (\Exception $e) {
                // skip
            }

            // ==================== Services Status ====================
            $services = $this->getServicesStatus();

            // ==================== PHP Extensions ====================
            $criticalExtensions = ['pdo', 'pdo_mysql', 'mbstring', 'openssl', 'json', 'curl', 'gd', 'xml', 'bcmath'];
            $extensionStatus = [];
            foreach ($criticalExtensions as $ext) {
                $extensionStatus[$ext] = extension_loaded($ext);
            }

            // ==================== Storage ====================
            $logsSize = 0;
            $cacheSizeVal = 0;
            try {
                if (File::isDirectory(storage_path('logs'))) {
                    foreach (File::allFiles(storage_path('logs')) as $file) {
                        $logsSize += $file->getSize();
                    }
                }
                if (File::isDirectory(storage_path('framework/cache'))) {
                    foreach (File::allFiles(storage_path('framework/cache')) as $file) {
                        $cacheSizeVal += $file->getSize();
                    }
                }
            } catch (\Exception $e) {
                // skip
            }

            // ==================== Top Processes ====================
            $topProcesses = $this->getTopProcesses();

            return response()->json([
                'success' => true,
                'timestamp' => now()->toIso8601String(),
                'data' => [
                    'server' => [
                        'php_version' => $phpVersion,
                        'laravel_version' => $laravelVersion,
                        'server_software' => $serverSoftware,
                        'os' => $os,
                        'hostname' => $hostname,
                        'timezone' => config('app.timezone'),
                        'environment' => config('app.env'),
                        'debug_mode' => config('app.debug'),
                    ],
                    'cpu' => $cpu,
                    'ram' => $ram,
                    'uptime' => $uptime,
                    'load_average' => $loadAvg,
                    'network' => $network,
                    'php_memory' => [
                        'limit' => $memoryLimit,
                        'usage_mb' => $memoryUsage,
                        'peak_mb' => $memoryPeak,
                    ],
                    'disk' => [
                        'total_gb' => round($diskTotal / 1024 / 1024 / 1024, 2),
                        'used_gb' => round($diskUsed / 1024 / 1024 / 1024, 2),
                        'free_gb' => round($diskFree / 1024 / 1024 / 1024, 2),
                        'percent_used' => $diskPercent,
                    ],
                    'database' => [
                        'status' => $dbStatus,
                        'version' => $dbVersion,
                        'central_size_mb' => $dbSize,
                        'total_tenant_size_mb' => round($totalTenantSize, 2),
                        'tenant_count' => count($tenantDbs),
                        'tenants' => $tenantDbs,
                        'uptime_seconds' => $dbUptime,
                        'connections' => $dbConnections,
                        'max_connections' => $dbMaxConnections,
                        'total_queries' => $dbQueries,
                    ],
                    'services' => $services,
                    'storage' => [
                        'logs_size_mb' => round($logsSize / 1024 / 1024, 2),
                        'cache_size_mb' => round($cacheSizeVal / 1024 / 1024, 2),
                    ],
                    'extensions' => $extensionStatus,
                    'top_processes' => $topProcesses,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error checking system health: ' . $e->getMessage(),
            ], 500);
        }
    }

    // ==================== REAL-TIME SYSTEM HELPERS ====================

    private function getCpuInfo(): array
    {
        $result = [
            'model' => 'Unknown',
            'cores' => 1,
            'threads' => 1,
            'usage_percent' => 0,
            'per_core' => [],
        ];

        try {
            // CPU model
            $cpuInfo = @file_get_contents('/proc/cpuinfo');
            if ($cpuInfo) {
                if (preg_match('/model name\s*:\s*(.+)/i', $cpuInfo, $m)) {
                    $result['model'] = trim($m[1]);
                }
                $result['threads'] = substr_count($cpuInfo, 'processor');
                $uniqueCores = [];
                preg_match_all('/core id\s*:\s*(\d+)/i', $cpuInfo, $coreMatches);
                if (!empty($coreMatches[1])) {
                    $uniqueCores = array_unique($coreMatches[1]);
                    $result['cores'] = count($uniqueCores);
                } else {
                    $result['cores'] = $result['threads'];
                }
            }

            // CPU usage from /proc/stat (instant snapshot)
            $stat1 = @file_get_contents('/proc/stat');
            usleep(100000); // 100ms sample
            $stat2 = @file_get_contents('/proc/stat');

            if ($stat1 && $stat2) {
                $lines1 = explode("\n", $stat1);
                $lines2 = explode("\n", $stat2);

                // Total CPU
                $cpu1 = preg_split('/\s+/', trim($lines1[0]));
                $cpu2 = preg_split('/\s+/', trim($lines2[0]));

                $idle1 = (int) ($cpu1[4] ?? 0);
                $idle2 = (int) ($cpu2[4] ?? 0);
                $total1 = array_sum(array_slice(array_map('intval', $cpu1), 1));
                $total2 = array_sum(array_slice(array_map('intval', $cpu2), 1));

                $totalDiff = $total2 - $total1;
                $idleDiff = $idle2 - $idle1;

                if ($totalDiff > 0) {
                    $result['usage_percent'] = round((1 - ($idleDiff / $totalDiff)) * 100, 1);
                }

                // Per-core usage
                for ($i = 1; $i <= $result['threads'] && $i < count($lines1) && $i < count($lines2); $i++) {
                    if (!str_starts_with($lines1[$i], 'cpu') || !str_starts_with($lines2[$i], 'cpu')) break;
                    $c1 = preg_split('/\s+/', trim($lines1[$i]));
                    $c2 = preg_split('/\s+/', trim($lines2[$i]));
                    $cIdle1 = (int) ($c1[4] ?? 0);
                    $cIdle2 = (int) ($c2[4] ?? 0);
                    $cTotal1 = array_sum(array_slice(array_map('intval', $c1), 1));
                    $cTotal2 = array_sum(array_slice(array_map('intval', $c2), 1));
                    $cTotalD = $cTotal2 - $cTotal1;
                    $cIdleD = $cIdle2 - $cIdle1;
                    $result['per_core'][] = $cTotalD > 0 ? round((1 - ($cIdleD / $cTotalD)) * 100, 1) : 0;
                }
            }
        } catch (\Exception $e) {
            // ignore
        }

        return $result;
    }

    private function getRamInfo(): array
    {
        $result = [
            'total_mb' => 0,
            'used_mb' => 0,
            'free_mb' => 0,
            'available_mb' => 0,
            'percent_used' => 0,
            'buffers_mb' => 0,
            'cached_mb' => 0,
            'swap_total_mb' => 0,
            'swap_used_mb' => 0,
        ];

        try {
            $meminfo = @file_get_contents('/proc/meminfo');
            if ($meminfo) {
                $parse = function ($key) use ($meminfo) {
                    if (preg_match("/{$key}:\s+(\d+)/", $meminfo, $m)) {
                        return round((int) $m[1] / 1024, 1); // kB -> MB
                    }
                    return 0;
                };

                $result['total_mb'] = $parse('MemTotal');
                $result['free_mb'] = $parse('MemFree');
                $result['available_mb'] = $parse('MemAvailable');
                $result['buffers_mb'] = $parse('Buffers');
                $result['cached_mb'] = $parse('Cached');
                $result['used_mb'] = round($result['total_mb'] - $result['available_mb'], 1);
                $result['percent_used'] = $result['total_mb'] > 0
                    ? round(($result['used_mb'] / $result['total_mb']) * 100, 1) : 0;
                $result['swap_total_mb'] = $parse('SwapTotal');
                $swapFree = $parse('SwapFree');
                $result['swap_used_mb'] = round($result['swap_total_mb'] - $swapFree, 1);
            }
        } catch (\Exception $e) {
            // ignore
        }

        return $result;
    }

    private function getUptime(): array
    {
        $result = ['seconds' => 0, 'formatted' => '—'];
        try {
            $uptimeRaw = @file_get_contents('/proc/uptime');
            if ($uptimeRaw) {
                $seconds = (int) floatval(explode(' ', trim($uptimeRaw))[0]);
                $result['seconds'] = $seconds;
                $days = floor($seconds / 86400);
                $hours = floor(($seconds % 86400) / 3600);
                $mins = floor(($seconds % 3600) / 60);
                $parts = [];
                if ($days > 0) $parts[] = "{$days}d";
                if ($hours > 0) $parts[] = "{$hours}h";
                $parts[] = "{$mins}m";
                $result['formatted'] = implode(' ', $parts);
            }
        } catch (\Exception $e) {
            // ignore
        }
        return $result;
    }

    private function getLoadAverage(): array
    {
        $load = sys_getloadavg();
        return [
            '1min' => round($load[0] ?? 0, 2),
            '5min' => round($load[1] ?? 0, 2),
            '15min' => round($load[2] ?? 0, 2),
        ];
    }

    private function getNetworkInfo(): array
    {
        $interfaces = [];
        try {
            $netDev = @file_get_contents('/proc/net/dev');
            if ($netDev) {
                $lines = explode("\n", $netDev);
                foreach ($lines as $line) {
                    if (preg_match('/^\s*(\w+):\s*(\d+)\s+\d+\s+\d+\s+\d+\s+\d+\s+\d+\s+\d+\s+\d+\s+(\d+)/', $line, $m)) {
                        $iface = $m[1];
                        if ($iface === 'lo') continue;
                        $interfaces[] = [
                            'name' => $iface,
                            'rx_bytes' => (int) $m[2],
                            'tx_bytes' => (int) $m[3],
                            'rx_mb' => round((int) $m[2] / 1024 / 1024, 1),
                            'tx_mb' => round((int) $m[3] / 1024 / 1024, 1),
                        ];
                    }
                }
            }
        } catch (\Exception $e) {
            // ignore
        }
        return $interfaces;
    }

    private function getServicesStatus(): array
    {
        $services = [];
        $checks = [
            ['name' => 'Nginx', 'service' => 'nginx', 'port' => 80],
            ['name' => 'PHP-FPM 8.3', 'service' => 'php8.3-fpm', 'port' => null],
            ['name' => 'MySQL', 'service' => 'mysql', 'port' => 3306],
            ['name' => 'WhatsApp Server', 'service' => 'whatsapp-pos', 'port' => 3002],
        ];

        foreach ($checks as $svc) {
            $status = 'stopped';
            $pid = null;
            $memoryMb = null;

            // Check via systemctl (fast)
            $systemctlOutput = @shell_exec("systemctl is-active {$svc['service']} 2>/dev/null");
            if ($systemctlOutput !== null && trim($systemctlOutput) === 'active') {
                $status = 'running';
            }

            // Get PID and memory if running
            if ($status === 'running') {
                $pidOutput = @shell_exec("systemctl show -p MainPID {$svc['service']} 2>/dev/null");
                if ($pidOutput && preg_match('/MainPID=(\d+)/', $pidOutput, $pm)) {
                    $pid = (int) $pm[1];
                    if ($pid > 0) {
                        $statm = @file_get_contents("/proc/{$pid}/statm");
                        if ($statm) {
                            $pages = (int) explode(' ', $statm)[1]; // RSS pages
                            $memoryMb = round($pages * 4096 / 1024 / 1024, 1);
                        }
                    }
                }
            }

            // Port check as fallback
            if ($status === 'stopped' && $svc['port']) {
                $conn = @fsockopen('127.0.0.1', $svc['port'], $errno, $errstr, 1);
                if ($conn) {
                    $status = 'running';
                    fclose($conn);
                }
            }

            $services[] = [
                'name' => $svc['name'],
                'service' => $svc['service'],
                'status' => $status,
                'pid' => $pid,
                'memory_mb' => $memoryMb,
                'port' => $svc['port'],
            ];
        }

        return $services;
    }

    private function getTopProcesses(): array
    {
        $processes = [];
        try {
            $output = @shell_exec("ps aux --sort=-%mem 2>/dev/null | head -8");
            if ($output) {
                $lines = explode("\n", trim($output));
                array_shift($lines); // remove header
                foreach ($lines as $line) {
                    $parts = preg_split('/\s+/', trim($line), 11);
                    if (count($parts) >= 11) {
                        $processes[] = [
                            'user' => $parts[0],
                            'pid' => (int) $parts[1],
                            'cpu' => (float) $parts[2],
                            'mem' => (float) $parts[3],
                            'rss_mb' => round((int) $parts[5] / 1024, 1),
                            'command' => substr($parts[10], 0, 60),
                        ];
                    }
                }
            }
        } catch (\Exception $e) {
            // ignore
        }
        return $processes;
    }

    /**
     * Read and parse Laravel log files with filtering
     */
    public function getLogs(Request $request)
    {
        try {
            $level = $request->get('level', '');
            $search = $request->get('search', '');
            $date = $request->get('date', '');
            $perPage = min((int) $request->get('per_page', 50), 200);
            $page = max((int) $request->get('page', 1), 1);

            $logPath = storage_path('logs');
            $logs = [];
            $logFiles = [];

            // Find log files
            if (File::isDirectory($logPath)) {
                $files = File::files($logPath);
                foreach ($files as $file) {
                    if ($file->getExtension() === 'log') {
                        $logFiles[] = [
                            'name' => $file->getFilename(),
                            'size_mb' => round($file->getSize() / 1024 / 1024, 2),
                            'modified' => date('Y-m-d H:i:s', $file->getMTime()),
                        ];
                    }
                }
            }

            // Determine which log file to read
            $targetFile = $request->get('file', '');
            if (empty($targetFile)) {
                // Default: today's log or laravel.log
                $todayLog = storage_path('logs/laravel-' . date('Y-m-d') . '.log');
                $defaultLog = storage_path('logs/laravel.log');
                if (File::exists($todayLog)) {
                    $targetFile = 'laravel-' . date('Y-m-d') . '.log';
                } elseif (File::exists($defaultLog)) {
                    $targetFile = 'laravel.log';
                }
            }

            if ($targetFile) {
                $fullPath = storage_path('logs/' . basename($targetFile));
                if (File::exists($fullPath)) {
                    $fileSize = File::size($fullPath);
                    
                    // Read last 500KB max to avoid memory issues
                    $maxRead = 512 * 1024;
                    $content = '';
                    if ($fileSize > $maxRead) {
                        $handle = fopen($fullPath, 'r');
                        fseek($handle, -$maxRead, SEEK_END);
                        $content = fread($handle, $maxRead);
                        fclose($handle);
                        // Skip first partial line
                        $content = substr($content, strpos($content, "\n") + 1);
                    } else {
                        $content = File::get($fullPath);
                    }

                    // Parse log entries
                    $pattern = '/\[(\d{4}-\d{2}-\d{2}[T ]\d{2}:\d{2}:\d{2}[^\]]*)\]\s+(\w+)\.(\w+):\s*(.*?)(?=\[\d{4}-\d{2}-\d{2}|\z)/s';
                    preg_match_all($pattern, $content, $matches, PREG_SET_ORDER);

                    foreach ($matches as $match) {
                        $timestamp = trim($match[1]);
                        $channel = $match[2];
                        $logLevel = strtolower($match[3]);
                        $message = trim($match[4]);

                        // Apply filters
                        if ($level && $logLevel !== strtolower($level)) continue;
                        if ($search && stripos($message, $search) === false) continue;
                        if ($date && strpos($timestamp, $date) === false) continue;

                        // Separate message from stack trace
                        $parts = explode("\n", $message, 2);
                        $mainMessage = trim($parts[0]);
                        $stackTrace = isset($parts[1]) ? trim($parts[1]) : '';

                        $logs[] = [
                            'timestamp' => $timestamp,
                            'channel' => $channel,
                            'level' => $logLevel,
                            'message' => mb_substr($mainMessage, 0, 500),
                            'stack_trace' => mb_substr($stackTrace, 0, 3000),
                            'has_trace' => !empty($stackTrace),
                        ];
                    }

                    // Reverse to show newest first
                    $logs = array_reverse($logs);
                }
            }

            // Paginate
            $total = count($logs);
            $offset = ($page - 1) * $perPage;
            $paginatedLogs = array_slice($logs, $offset, $perPage);

            // Count by level
            $levelCounts = ['emergency' => 0, 'alert' => 0, 'critical' => 0, 'error' => 0, 'warning' => 0, 'notice' => 0, 'info' => 0, 'debug' => 0];
            foreach ($logs as $log) {
                if (isset($levelCounts[$log['level']])) {
                    $levelCounts[$log['level']]++;
                }
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'logs' => $paginatedLogs,
                    'total' => $total,
                    'page' => $page,
                    'per_page' => $perPage,
                    'total_pages' => ceil($total / $perPage),
                    'level_counts' => $levelCounts,
                    'log_files' => $logFiles,
                    'current_file' => $targetFile,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error reading logs: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Clear log files
     */
    public function clearLogs(Request $request)
    {
        try {
            $file = $request->get('file', '');
            
            if ($file) {
                $fullPath = storage_path('logs/' . basename($file));
                if (File::exists($fullPath)) {
                    File::put($fullPath, '');
                    return response()->json(['success' => true, 'message' => "Log file {$file} cleared"]);
                }
                return response()->json(['success' => false, 'message' => 'File not found'], 404);
            }

            // Clear all log files
            $logPath = storage_path('logs');
            if (File::isDirectory($logPath)) {
                foreach (File::files($logPath) as $logFile) {
                    if ($logFile->getExtension() === 'log') {
                        File::put($logFile->getPathname(), '');
                    }
                }
            }

            return response()->json(['success' => true, 'message' => 'All log files cleared']);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error clearing logs: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Run maintenance operations (cache clear, optimize, etc.)
     */
    public function maintenance(Request $request)
    {
        try {
            $action = $request->get('action', '');
            $output = '';

            switch ($action) {
                case 'clear-cache':
                    Artisan::call('cache:clear');
                    $output = Artisan::output();
                    break;

                case 'clear-config':
                    Artisan::call('config:clear');
                    $output = Artisan::output();
                    break;

                case 'clear-route':
                    Artisan::call('route:clear');
                    $output = Artisan::output();
                    break;

                case 'clear-view':
                    Artisan::call('view:clear');
                    $output = Artisan::output();
                    break;

                case 'clear-all':
                    Artisan::call('cache:clear');
                    $o1 = Artisan::output();
                    Artisan::call('config:clear');
                    $o2 = Artisan::output();
                    Artisan::call('route:clear');
                    $o3 = Artisan::output();
                    Artisan::call('view:clear');
                    $o4 = Artisan::output();
                    $output = $o1 . $o2 . $o3 . $o4;
                    break;

                case 'optimize':
                    Artisan::call('optimize');
                    $output = Artisan::output();
                    break;

                case 'migrate-status':
                    Artisan::call('migrate:status');
                    $output = Artisan::output();
                    break;

                case 'storage-link':
                    Artisan::call('storage:link');
                    $output = Artisan::output();
                    break;

                default:
                    return response()->json([
                        'success' => false,
                        'message' => 'Invalid action. Allowed: clear-cache, clear-config, clear-route, clear-view, clear-all, optimize, migrate-status, storage-link',
                    ], 400);
            }

            return response()->json([
                'success' => true,
                'message' => "Action '{$action}' executed successfully",
                'output' => trim($output),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => "Error running '{$action}': " . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get environment and configuration info
     */
    public function environment()
    {
        try {
            return response()->json([
                'success' => true,
                'data' => [
                    'app' => [
                        'name' => config('app.name'),
                        'env' => config('app.env'),
                        'debug' => config('app.debug'),
                        'url' => config('app.url'),
                        'timezone' => config('app.timezone'),
                        'locale' => config('app.locale'),
                    ],
                    'database' => [
                        'driver' => config('database.default'),
                        'host' => config('database.connections.mysql.host'),
                        'port' => config('database.connections.mysql.port'),
                        'database' => config('database.connections.mysql.database'),
                    ],
                    'cache' => [
                        'driver' => config('cache.default'),
                    ],
                    'session' => [
                        'driver' => config('session.driver'),
                        'lifetime' => config('session.lifetime'),
                    ],
                    'mail' => [
                        'mailer' => config('mail.default'),
                        'host' => config('mail.mailers.smtp.host'),
                        'port' => config('mail.mailers.smtp.port'),
                    ],
                    'tenancy' => [
                        'central_domains' => config('tenancy.central_domains', []),
                        'database_prefix' => 'tenant_',
                    ],
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error reading environment: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Quick actions for specific tenant databases
     */
    public function tenantDatabaseInfo($tenantId)
    {
        try {
            $tenant = DB::connection('mysql')->table('tenants')->where('id', $tenantId)->first();
            if (!$tenant) {
                return response()->json(['success' => false, 'message' => 'Tenant not found'], 404);
            }

            $dbName = 'tenant_' . $tenantId;
            
            // Get all tables with row counts
            $tables = DB::connection('mysql')->select(
                "SELECT table_name, table_rows, ROUND(data_length / 1024 / 1024, 2) as data_mb, 
                        ROUND(index_length / 1024 / 1024, 2) as index_mb,
                        ROUND((data_length + index_length) / 1024 / 1024, 2) as total_mb,
                        create_time, update_time
                 FROM information_schema.TABLES 
                 WHERE table_schema = ? 
                 ORDER BY (data_length + index_length) DESC",
                [$dbName]
            );

            $tableInfo = [];
            foreach ($tables as $table) {
                $tableInfo[] = [
                    'name' => $table->table_name ?? $table->TABLE_NAME,
                    'rows' => $table->table_rows ?? $table->TABLE_ROWS ?? 0,
                    'data_mb' => $table->data_mb,
                    'index_mb' => $table->index_mb,
                    'total_mb' => $table->total_mb,
                    'created' => $table->create_time ?? $table->CREATE_TIME,
                    'updated' => $table->update_time ?? $table->UPDATE_TIME,
                ];
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'tenant_id' => $tenantId,
                    'database' => $dbName,
                    'tables' => $tableInfo,
                    'total_tables' => count($tableInfo),
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error reading tenant database: ' . $e->getMessage(),
            ], 500);
        }
    }
}
