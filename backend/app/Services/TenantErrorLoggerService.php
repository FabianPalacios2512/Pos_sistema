<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TenantErrorLoggerService
{
    /**
     * Log a tenant event (info, warning, error) - not from an exception.
     * Use this for important business events that the super admin should see.
     */
    public static function logEvent(string $tenantId, string $severity, string $type, string $message, array $context = []): void
    {
        try {
            $hash = hash('sha256', $tenantId . '|' . $type . '|' . $message);

            $existing = DB::connection('mysql')->table('tenant_error_logs')
                ->where('tenant_id', $tenantId)
                ->where('error_hash', $hash)
                ->first();

            if ($existing) {
                DB::connection('mysql')->table('tenant_error_logs')
                    ->where('id', $existing->id)
                    ->update([
                        'occurrence_count' => $existing->occurrence_count + 1,
                        'last_seen_at' => now(),
                        'resolved' => false,
                        'resolved_at' => null,
                        'context' => json_encode($context),
                        'updated_at' => now(),
                    ]);
            } else {
                DB::connection('mysql')->table('tenant_error_logs')->insert([
                    'tenant_id' => $tenantId,
                    'error_hash' => $hash,
                    'severity' => $severity,
                    'type' => $type,
                    'message' => mb_substr($message, 0, 2000),
                    'file' => null,
                    'line' => null,
                    'context' => json_encode($context),
                    'occurrence_count' => 1,
                    'first_seen_at' => now(),
                    'last_seen_at' => now(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        } catch (\Throwable $e) {
            // Never break the app
        }
    }

    /**
     * Record an error for a specific tenant.
     * Deduplicates by error_hash (same message+type+file = same error).
     */
    public function record(string $tenantId, \Throwable $exception, array $extra = []): void
    {
        try {
            $type = get_class($exception);
            $message = $exception->getMessage();
            $file = $exception->getFile();
            $line = $exception->getLine();

            // Build hash from type + message + file (not line, since lines shift)
            $hash = hash('sha256', $tenantId . '|' . $type . '|' . $message . '|' . $file);

            $context = array_filter([
                'request_url' => $extra['url'] ?? null,
                'request_method' => $extra['method'] ?? null,
                'user_id' => $extra['user_id'] ?? null,
                'user_agent' => $extra['user_agent'] ?? null,
                'ip' => $extra['ip'] ?? null,
                'trace_snippet' => $this->getTraceSnippet($exception),
            ]);

            $existing = DB::connection('mysql')->table('tenant_error_logs')
                ->where('tenant_id', $tenantId)
                ->where('error_hash', $hash)
                ->first();

            if ($existing) {
                // Increment counter, update last_seen, unmark resolved if re-occurs
                DB::connection('mysql')->table('tenant_error_logs')
                    ->where('id', $existing->id)
                    ->update([
                        'occurrence_count' => $existing->occurrence_count + 1,
                        'last_seen_at' => now(),
                        'resolved' => false,
                        'resolved_at' => null,
                        'context' => json_encode($context),
                        'line' => $line,
                        'updated_at' => now(),
                    ]);
            } else {
                DB::connection('mysql')->table('tenant_error_logs')->insert([
                    'tenant_id' => $tenantId,
                    'error_hash' => $hash,
                    'severity' => $this->classifySeverity($exception),
                    'type' => $this->shortenType($type),
                    'message' => mb_substr($message, 0, 2000),
                    'file' => $this->shortenPath($file),
                    'line' => $line,
                    'context' => json_encode($context),
                    'occurrence_count' => 1,
                    'first_seen_at' => now(),
                    'last_seen_at' => now(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        } catch (\Throwable $e) {
            // Never let error logging break the app
            Log::warning('[TenantErrorLogger] Failed to record: ' . $e->getMessage());
        }
    }

    /**
     * Classify severity based on exception type.
     */
    private function classifySeverity(\Throwable $e): string
    {
        $class = get_class($e);

        $critical = ['PDOException', 'QueryException', 'ErrorException', 'FatalError'];
        foreach ($critical as $c) {
            if (str_contains($class, $c)) return 'critical';
        }

        $warnings = ['NotFoundHttpException', 'ModelNotFoundException', 'ValidationException', 'TokenMismatchException'];
        foreach ($warnings as $w) {
            if (str_contains($class, $w)) return 'warning';
        }

        return 'error';
    }

    /**
     * Shorten exception class name.
     */
    private function shortenType(string $type): string
    {
        $parts = explode('\\', $type);
        return end($parts);
    }

    /**
     * Shorten file path to be relative.
     */
    private function shortenPath(string $path): string
    {
        $basePath = base_path();
        if (str_starts_with($path, $basePath)) {
            return ltrim(str_replace($basePath, '', $path), '/\\');
        }
        return $path;
    }

    /**
     * Get first 5 lines of trace (compact).
     */
    private function getTraceSnippet(\Throwable $e): string
    {
        $lines = [];
        foreach (array_slice($e->getTrace(), 0, 5) as $frame) {
            $f = isset($frame['file']) ? basename($frame['file']) : '?';
            $l = $frame['line'] ?? '?';
            $fn = $frame['function'] ?? '?';
            $lines[] = "{$f}:{$l} → {$fn}()";
        }
        return implode("\n", $lines);
    }

    /**
     * Get error counts per tenant (for the client list).
     */
    public function getErrorCountsPerTenant(): array
    {
        return DB::connection('mysql')->table('tenant_error_logs')
            ->select('tenant_id', DB::raw('COUNT(*) as total_errors'), DB::raw('SUM(CASE WHEN resolved = 0 THEN 1 ELSE 0 END) as active_errors'))
            ->groupBy('tenant_id')
            ->get()
            ->keyBy('tenant_id')
            ->toArray();
    }

    /**
     * Get errors for a specific tenant.
     */
    public function getTenantErrors(string $tenantId, bool $includeResolved = false, int $limit = 50): array
    {
        $query = DB::connection('mysql')->table('tenant_error_logs')
            ->where('tenant_id', $tenantId)
            ->orderBy('last_seen_at', 'desc')
            ->limit($limit);

        if (!$includeResolved) {
            $query->where('resolved', false);
        }

        return $query->get()->toArray();
    }

    /**
     * Mark an error as resolved.
     */
    public function resolveError(int $errorId): bool
    {
        return DB::connection('mysql')->table('tenant_error_logs')
            ->where('id', $errorId)
            ->update([
                'resolved' => true,
                'resolved_at' => now(),
                'updated_at' => now(),
            ]) > 0;
    }

    /**
     * Analyze pending errors with Groq AI and fill ai_summary.
     */
    public function analyzeWithAI(int $errorId): ?string
    {
        $error = DB::connection('mysql')->table('tenant_error_logs')
            ->where('id', $errorId)
            ->first();

        if (!$error) return null;

        $apiKeys = $this->getGroqKeys();
        if (empty($apiKeys)) return null;

        $prompt = $this->buildAnalysisPrompt($error);

        foreach ($apiKeys as $key) {
            try {
                $response = Http::timeout(15)->withHeaders([
                    'Authorization' => 'Bearer ' . $key,
                    'Content-Type' => 'application/json',
                ])->post('https://api.groq.com/openai/v1/chat/completions', [
                    'model' => 'llama-3.3-70b-versatile',
                    'messages' => [
                        ['role' => 'system', 'content' => 'Eres un experto en debugging de aplicaciones Laravel/PHP con Vue.js. Analiza errores de producción y da diagnósticos claros y concisos. Responde SIEMPRE en español. Máximo 3 oraciones: 1) Qué pasó, 2) Por qué probablemente ocurrió, 3) Cómo solucionarlo. NO uses markdown ni listas, solo texto plano directo.'],
                        ['role' => 'user', 'content' => $prompt]
                    ],
                    'temperature' => 0.2,
                    'max_tokens' => 200,
                ]);

                if ($response->successful()) {
                    $summary = $response->json('choices.0.message.content');
                    if ($summary) {
                        $summary = trim($summary);
                        DB::connection('mysql')->table('tenant_error_logs')
                            ->where('id', $errorId)
                            ->update(['ai_summary' => $summary, 'updated_at' => now()]);
                        return $summary;
                    }
                }
            } catch (\Throwable $e) {
                continue;
            }
        }

        return null;
    }

    /**
     * Batch analyze all errors without AI summary.
     */
    public function analyzeAllPending(string $tenantId = null, int $limit = 10): int
    {
        $query = DB::connection('mysql')->table('tenant_error_logs')
            ->whereNull('ai_summary')
            ->where('resolved', false)
            ->orderBy('occurrence_count', 'desc')
            ->limit($limit);

        if ($tenantId) {
            $query->where('tenant_id', $tenantId);
        }

        $errors = $query->get();
        $analyzed = 0;

        foreach ($errors as $error) {
            if ($this->analyzeWithAI($error->id)) {
                $analyzed++;
            }
        }

        return $analyzed;
    }

    private function buildAnalysisPrompt(object $error): string
    {
        $context = json_decode($error->context ?? '{}', true);
        return "Error en tienda '{$error->tenant_id}':\n"
            . "Tipo: {$error->type}\n"
            . "Mensaje: {$error->message}\n"
            . "Archivo: {$error->file}:{$error->line}\n"
            . "Veces ocurrido: {$error->occurrence_count}\n"
            . ($context['request_url'] ?? '' ? "URL: {$context['request_url']}\n" : '')
            . ($context['trace_snippet'] ?? '' ? "Trace:\n{$context['trace_snippet']}\n" : '')
            . "\nDiagnóstico conciso:";
    }

    private function getGroqKeys(): array
    {
        return array_filter([
            config('services.groq.api_key_1'),
            config('services.groq.api_key_2'),
            config('services.groq.api_key_3'),
            config('services.groq.api_key_4'),
            config('services.groq.api_key_5'),
            config('services.groq.api_key_6'),
            config('services.groq.api_key_7'),
            config('services.groq.api_key_8'),
            config('services.groq.api_key_9'),
            config('services.groq.api_key_10'),
        ]);
    }
}
