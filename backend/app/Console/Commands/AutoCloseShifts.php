<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Tenant;
use App\Models\AttendanceLog;
use App\Models\CashSession;
use Carbon\Carbon;

class AutoCloseShifts extends Command
{
    protected $signature = 'shifts:auto-close';
    protected $description = 'Cierre automático de jornadas y cajas abiertas al final del día operativo (00:00)';

    public function handle()
    {
        $tenants = Tenant::all();

        $this->info("Auto-cierre de jornadas: Procesando {$tenants->count()} tenants...\n");

        $totalShiftsClosed = 0;
        $totalCashForced = 0;

        foreach ($tenants as $tenant) {
            try {
                $tenant->run(function () use (&$totalShiftsClosed, &$totalCashForced, $tenant) {
                    $this->info("Tenant: {$tenant->id}");

                    // 1. Find ALL open shifts from any past day (entry without matching exit)
                    $openShifts = $this->findOpenShifts();

                    foreach ($openShifts as $shift) {
                        // Close at 23:59:59 of the ENTRY day
                        $entryDate = Carbon::parse($shift['entry_log']->event_at);
                        $closeAt = $entryDate->copy()->endOfDay(); // 23:59:59 of entry day

                        AttendanceLog::create([
                            'user_id' => $shift['user_id'],
                            'event_type' => 'exit',
                            'event_at' => $closeAt,
                            'verification_score' => 0,
                            'closed_by' => 'system',
                            'is_auto_closed' => true,
                            'auto_close_note' => "Cierre automático del día operativo {$entryDate->toDateString()}",
                        ]);

                        $totalShiftsClosed++;
                        $this->line("   Jornada cerrada: user_id {$shift['user_id']} (entrada: {$entryDate->toDateTimeString()})");
                    }

                    // 2. Force-close open cash sessions with pending audit status
                    $openCashSessions = CashSession::where('status', 'open')->get();

                    foreach ($openCashSessions as $session) {
                        // Calculate expected amount before forcing close
                        $session->updateSalesTotals();
                        $expectedAmount = $session->calculateExpectedAmount();

                        $session->update([
                            'status' => 'forced_closed',
                            'closed_at' => Carbon::now(),
                            'expected_amount' => $expectedAmount,
                            'closing_notes' => 'Cierre forzado por sistema - Pendiente de arqueo del cajero',
                        ]);

                        $totalCashForced++;
                        $this->line("   Caja forzada (session_id: {$session->id}, user_id: {$session->user_id})");
                    }
                });
            } catch (\Exception $e) {
                $this->error("   Error en tenant {$tenant->id}: {$e->getMessage()}");
            }
        }

        $this->info("\nAuto-cierre finalizado:");
        $this->info("   Jornadas cerradas: {$totalShiftsClosed}");
        $this->info("   Cajas forzadas: {$totalCashForced}");

        return Command::SUCCESS;
    }

    /**
     * Find users with an entry but no exit on any PAST day (before today).
     * This catches shifts left open yesterday or even earlier.
     */
    private function findOpenShifts(): array
    {
        $today = Carbon::today();

        // Get all entry logs from BEFORE today that have no matching exit on the same day
        $entryLogs = AttendanceLog::where('event_type', 'entry')
            ->where('event_at', '<', $today)
            ->orderBy('event_at', 'asc')
            ->get();

        $openShifts = [];

        foreach ($entryLogs as $entry) {
            $entryDate = Carbon::parse($entry->event_at)->toDateString();

            // Check if there's an exit on that same day for this user
            $hasExit = AttendanceLog::where('user_id', $entry->user_id)
                ->where('event_type', 'exit')
                ->whereDate('event_at', $entryDate)
                ->exists();

            if (!$hasExit) {
                // Use user_id + date as key to avoid duplicates (multiple entries same day)
                $key = $entry->user_id . '_' . $entryDate;
                if (!isset($openShifts[$key])) {
                    $openShifts[$key] = [
                        'user_id' => $entry->user_id,
                        'entry_log' => $entry,
                    ];
                }
            }
        }

        return $openShifts;
    }
}
