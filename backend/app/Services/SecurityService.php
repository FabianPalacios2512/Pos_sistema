<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SecurityService
{
    // ── Configuración ──────────────────────────────────────
    const MAX_FAILED_PER_USER    = 5;      // Intentos fallidos → bloqueo de cuenta
    const MAX_FAILED_PER_IP      = 15;     // Intentos por IP → bloqueo temporal
    const IP_SWEEP_THRESHOLD     = 5;      // Emails distintos desde una IP en 15 min → sweep
    const IP_BLOCK_MINUTES       = 30;     // Duración bloqueo IP
    const ATTEMPT_WINDOW_MINUTES = 15;     // Ventana de análisis
    const LOG_RETENTION_DAYS     = 30;     // Días para limpiar logs antiguos

    // Scoring
    const SCORE_MULTIPLE_FAILS   = 2;
    const SCORE_IP_CHANGE        = 3;
    const SCORE_UNUSUAL_HOUR     = 2;
    const SCORE_THRESHOLD        = 10;     // Umbral → marcar como sospechoso

    // ── Registrar intento de login ────────────────────────
    public function recordAttempt(string $email, string $ip, string $userAgent, string $result, ?string $failReason = null, ?string $tenantId = null): void
    {
        try {
            DB::connection('mysql')->table('login_attempts')->insert([
                'email'        => $email,
                'ip_address'   => $ip,
                'user_agent'   => mb_substr($userAgent ?? '', 0, 500),
                'tenant_id'    => $tenantId,
                'result'       => $result,
                'fail_reason'  => $failReason,
                'attempted_at' => now(),
            ]);
        } catch (\Exception $e) {
            Log::error('SecurityService: Error recording attempt', ['error' => $e->getMessage()]);
        }
    }

    // ── Verificar si IP está bloqueada ────────────────────
    public function isIpBlocked(string $ip): bool
    {
        try {
            return DB::connection('mysql')->table('blocked_ips')
                ->where('ip_address', $ip)
                ->where(function ($q) {
                    $q->where('expires_at', '>', now())
                      ->orWhere('is_permanent', true);
                })
                ->exists();
        } catch (\Exception $e) {
            return false;
        }
    }

    // ── Verificar si usuario está bloqueado por seguridad ─
    public function isUserBlocked(string $email): bool
    {
        try {
            $record = DB::connection('mysql')->table('user_risk_scores')
                ->where('email', $email)
                ->where('status', 'blocked')
                ->first();
            return $record !== null;
        } catch (\Exception $e) {
            return false;
        }
    }

    // ── Obtener motivo de bloqueo ─────────────────────────
    public function getBlockReason(string $email): ?string
    {
        try {
            $record = DB::connection('mysql')->table('user_risk_scores')
                ->where('email', $email)
                ->where('status', 'blocked')
                ->first();
            return $record->block_reason ?? null;
        } catch (\Exception $e) {
            return null;
        }
    }

    // ── Analizar post-intento fallido ─────────────────────
    public function analyzeAfterFailedAttempt(string $email, string $ip): array
    {
        $actions = [];

        try {
            $window = now()->subMinutes(self::ATTEMPT_WINDOW_MINUTES);

            // 1. Contar fallos consecutivos de este usuario
            $userFails = DB::connection('mysql')->table('login_attempts')
                ->where('email', $email)
                ->where('result', 'fail')
                ->where('attempted_at', '>=', $window)
                ->count();

            if ($userFails >= self::MAX_FAILED_PER_USER) {
                $this->blockUser($email, 'Múltiples intentos fallidos de inicio de sesión (' . $userFails . ' intentos)');
                $actions[] = 'account_blocked';

                $this->createSecurityEvent(
                    'brute_force', 'high', $email, $ip,
                    ['consecutive_fails' => $userFails],
                    'account_locked'
                );
            }

            // 2. Contar fallos desde esta IP
            $ipFails = DB::connection('mysql')->table('login_attempts')
                ->where('ip_address', $ip)
                ->where('result', 'fail')
                ->where('attempted_at', '>=', $window)
                ->count();

            if ($ipFails >= self::MAX_FAILED_PER_IP) {
                $this->blockIp($ip, 'Demasiados intentos fallidos desde esta IP (' . $ipFails . ' intentos)');
                $actions[] = 'ip_blocked';

                $this->createSecurityEvent(
                    'brute_force', 'high', null, $ip,
                    ['ip_fails' => $ipFails],
                    'ip_blocked'
                );
            }

            // 3. Detectar IP sweep (muchos emails distintos desde misma IP)
            $distinctEmails = DB::connection('mysql')->table('login_attempts')
                ->where('ip_address', $ip)
                ->where('result', 'fail')
                ->where('attempted_at', '>=', $window)
                ->distinct('email')
                ->count('email');

            if ($distinctEmails >= self::IP_SWEEP_THRESHOLD) {
                $this->blockIp($ip, 'Escaneo de cuentas detectado: ' . $distinctEmails . ' emails distintos');
                $actions[] = 'ip_blocked_sweep';

                $this->createSecurityEvent(
                    'ip_sweep', 'critical', null, $ip,
                    ['distinct_emails' => $distinctEmails],
                    'ip_blocked'
                );
            }

            // 4. Actualizar risk score
            $this->updateRiskScore($email, self::SCORE_MULTIPLE_FAILS);

        } catch (\Exception $e) {
            Log::error('SecurityService: Error analyzing attempt', ['error' => $e->getMessage()]);
        }

        return $actions;
    }

    // ── Analizar login exitoso (anomalías) ────────────────
    public function analyzeSuccessfulLogin(string $email, string $ip): void
    {
        try {
            // Resetear fallos consecutivos exitosamente
            // (no borrar records, solo limpiar score incremental)

            // Verificar hora inusual (fuera de 6am-11pm)
            $hour = (int) now()->format('H');
            if ($hour < 6 || $hour > 23) {
                $this->updateRiskScore($email, self::SCORE_UNUSUAL_HOUR);
                $this->createSecurityEvent(
                    'unusual_hour', 'low', $email, $ip,
                    ['login_hour' => $hour],
                    'alert_raised'
                );
            }

            // Verificar si IP cambió vs. último login exitoso
            $lastSuccess = DB::connection('mysql')->table('login_attempts')
                ->where('email', $email)
                ->where('result', 'success')
                ->orderBy('attempted_at', 'desc')
                ->skip(1)->first();

            if ($lastSuccess && $lastSuccess->ip_address !== $ip) {
                $this->updateRiskScore($email, self::SCORE_IP_CHANGE);
            }

            // Reducir score por login exitoso (-1 gradual)
            $this->reduceRiskScore($email, 1);

        } catch (\Exception $e) {
            Log::error('SecurityService: Error analyzing success', ['error' => $e->getMessage()]);
        }
    }

    // ── Bloquear usuario ──────────────────────────────────
    public function blockUser(string $email, string $reason): void
    {
        DB::connection('mysql')->table('user_risk_scores')->updateOrInsert(
            ['email' => $email],
            [
                'status'       => 'blocked',
                'block_reason' => $reason,
                'blocked_at'   => now(),
                'updated_at'   => now(),
            ]
        );
    }

    // ── Desbloquear usuario ───────────────────────────────
    public function unblockUser(string $email): bool
    {
        try {
            DB::connection('mysql')->table('user_risk_scores')
                ->where('email', $email)
                ->update([
                    'status'       => 'normal',
                    'risk_score'   => 0,
                    'block_reason' => null,
                    'blocked_at'   => null,
                    'updated_at'   => now(),
                ]);

            $this->createSecurityEvent(
                'account_unblocked', 'low', $email, null,
                ['action' => 'manual_unblock'],
                'account_unlocked'
            );

            return true;
        } catch (\Exception $e) {
            Log::error('SecurityService: Error unblocking user', ['error' => $e->getMessage()]);
            return false;
        }
    }

    // ── Bloquear IP ───────────────────────────────────────
    public function blockIp(string $ip, string $reason, bool $permanent = false): void
    {
        $failCount = DB::connection('mysql')->table('login_attempts')
            ->where('ip_address', $ip)
            ->where('result', 'fail')
            ->where('attempted_at', '>=', now()->subMinutes(self::ATTEMPT_WINDOW_MINUTES))
            ->count();

        DB::connection('mysql')->table('blocked_ips')->updateOrInsert(
            ['ip_address' => $ip],
            [
                'reason'        => $reason,
                'attempt_count' => $failCount,
                'blocked_at'    => now(),
                'expires_at'    => $permanent ? now()->addYears(10) : now()->addMinutes(self::IP_BLOCK_MINUTES),
                'is_permanent'  => $permanent,
                'updated_at'    => now(),
            ]
        );
    }

    // ── Desbloquear IP ────────────────────────────────────
    public function unblockIp(string $ip): bool
    {
        try {
            DB::connection('mysql')->table('blocked_ips')
                ->where('ip_address', $ip)
                ->delete();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    // ── Crear evento de seguridad ─────────────────────────
    public function createSecurityEvent(string $type, string $severity, ?string $email, ?string $ip, array $details = [], ?string $action = null): void
    {
        try {
            DB::connection('mysql')->table('security_events')->insert([
                'event_type'   => $type,
                'severity'     => $severity,
                'email'        => $email,
                'ip_address'   => $ip,
                'details'      => json_encode($details),
                'action_taken' => $action,
                'created_at'   => now(),
            ]);
        } catch (\Exception $e) {
            Log::error('SecurityService: Error creating event', ['error' => $e->getMessage()]);
        }
    }

    // ── Actualizar risk score ─────────────────────────────
    private function updateRiskScore(string $email, int $points): void
    {
        $record = DB::connection('mysql')->table('user_risk_scores')
            ->where('email', $email)
            ->first();

        $newScore = ($record->risk_score ?? 0) + $points;
        $status = $newScore >= self::SCORE_THRESHOLD ? 'suspicious' : ($record->status ?? 'normal');

        // No degradar de 'blocked' a 'suspicious'
        if ($record && $record->status === 'blocked') {
            $status = 'blocked';
        }

        $history = $record && $record->score_history ? json_decode($record->score_history, true) : [];
        $history[] = ['points' => $points, 'timestamp' => now()->toIso8601String()];
        // Mantener solo últimos 20 eventos
        $history = array_slice($history, -20);

        DB::connection('mysql')->table('user_risk_scores')->updateOrInsert(
            ['email' => $email],
            [
                'risk_score'     => $newScore,
                'status'         => $status,
                'last_scored_at' => now(),
                'score_history'  => json_encode($history),
                'updated_at'     => now(),
            ]
        );

        // Si alcanza umbral, crear evento
        if ($newScore >= self::SCORE_THRESHOLD && ($record->status ?? 'normal') !== 'suspicious') {
            $this->createSecurityEvent(
                'risk_threshold', 'medium', $email, null,
                ['risk_score' => $newScore],
                'alert_raised'
            );
        }
    }

    // ── Reducir score gradual (login exitoso) ─────────────
    private function reduceRiskScore(string $email, int $points): void
    {
        DB::connection('mysql')->table('user_risk_scores')
            ->where('email', $email)
            ->where('status', '!=', 'blocked')
            ->where('risk_score', '>', 0)
            ->decrement('risk_score', $points);
    }

    // ── Dashboard de seguridad (SuperAdmin) ───────────────
    public function getDashboardData(): array
    {
        $today = now()->startOfDay();

        $failedToday = DB::connection('mysql')->table('login_attempts')
            ->where('result', 'fail')
            ->where('attempted_at', '>=', $today)
            ->count();

        $successToday = DB::connection('mysql')->table('login_attempts')
            ->where('result', 'success')
            ->where('attempted_at', '>=', $today)
            ->count();

        $blockedAccounts = DB::connection('mysql')->table('user_risk_scores')
            ->where('status', 'blocked')
            ->count();

        $suspiciousAccounts = DB::connection('mysql')->table('user_risk_scores')
            ->where('status', 'suspicious')
            ->count();

        $blockedIps = DB::connection('mysql')->table('blocked_ips')
            ->where(function ($q) {
                $q->where('expires_at', '>', now())
                  ->orWhere('is_permanent', true);
            })
            ->count();

        $recentEvents = DB::connection('mysql')->table('security_events')
            ->orderBy('created_at', 'desc')
            ->limit(50)
            ->get()
            ->map(function ($e) {
                $e->details = json_decode($e->details, true);
                return $e;
            });

        $topAttackedEmails = DB::connection('mysql')->table('login_attempts')
            ->where('result', 'fail')
            ->where('attempted_at', '>=', now()->subHours(24))
            ->select('email', DB::raw('COUNT(*) as attempts'))
            ->groupBy('email')
            ->orderByDesc('attempts')
            ->limit(10)
            ->get();

        $topAttackIps = DB::connection('mysql')->table('login_attempts')
            ->where('result', 'fail')
            ->where('attempted_at', '>=', now()->subHours(24))
            ->select('ip_address', DB::raw('COUNT(*) as attempts'))
            ->groupBy('ip_address')
            ->orderByDesc('attempts')
            ->limit(10)
            ->get();

        $blockedAccountsList = DB::connection('mysql')->table('user_risk_scores')
            ->where('status', 'blocked')
            ->orderByDesc('blocked_at')
            ->get();

        $blockedIpsList = DB::connection('mysql')->table('blocked_ips')
            ->where(function ($q) {
                $q->where('expires_at', '>', now())
                  ->orWhere('is_permanent', true);
            })
            ->orderByDesc('blocked_at')
            ->get();

        return [
            'kpis' => [
                'failed_today'         => $failedToday,
                'success_today'        => $successToday,
                'blocked_accounts'     => $blockedAccounts,
                'suspicious_accounts'  => $suspiciousAccounts,
                'blocked_ips'          => $blockedIps,
            ],
            'recent_events'       => $recentEvents,
            'top_attacked_emails' => $topAttackedEmails,
            'top_attack_ips'      => $topAttackIps,
            'blocked_accounts'    => $blockedAccountsList,
            'blocked_ips'         => $blockedIpsList,
        ];
    }

    // ── Limpiar logs antiguos ─────────────────────────────
    public function cleanupOldLogs(): int
    {
        $cutoff = now()->subDays(self::LOG_RETENTION_DAYS);

        $deleted = DB::connection('mysql')->table('login_attempts')
            ->where('attempted_at', '<', $cutoff)
            ->delete();

        // Limpiar IPs bloqueadas expiradas
        DB::connection('mysql')->table('blocked_ips')
            ->where('is_permanent', false)
            ->where('expires_at', '<', now())
            ->delete();

        // Limpiar eventos antiguos resueltos
        DB::connection('mysql')->table('security_events')
            ->where('created_at', '<', $cutoff)
            ->where('resolved', true)
            ->delete();

        return $deleted;
    }
}
