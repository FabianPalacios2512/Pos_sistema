<?php

namespace App\Console\Commands;

use App\Models\Tenant;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class SendTrialExpirationEmails extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'trial:send-expiration-emails';

    /**
     * The console command description.
     */
    protected $description = 'Envía emails de recordatorio de expiración de trial a tenants en Trial Express';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔍 Buscando tenants en Trial Express...');

        // Obtener todos los tenants con plan trial_express que tienen subscription_ends_at
        $tenants = Tenant::where('plan', 'trial_express')
            ->whereNotNull('subscription_ends_at')
            ->get();

        $this->info("📧 Procesando {$tenants->count()} tenants...");

        $emailsSent = 0;
        $now = Carbon::now();

        foreach ($tenants as $tenant) {
            $expiresAt = Carbon::parse($tenant->subscription_ends_at);
            $daysRemaining = $now->diffInDays($expiresAt, false);

            // Determinar qué email enviar según días restantes
            $emailType = null;

            if ($daysRemaining == 1) {
                $emailType = 'one_day_left';
            } elseif ($daysRemaining == 0) {
                $emailType = 'last_day';
            } elseif ($daysRemaining < 0 && $daysRemaining >= -1) {
                $emailType = 'expired';
            }

            // Enviar email si aplica
            if ($emailType) {
                try {
                    // Obtener email del negocio desde el tenant
                    $tenant->run(function () use ($tenant, $emailType, $daysRemaining) {
                        $systemSettings = \DB::table('system_settings')->first();
                        $businessEmail = $systemSettings->company_email ?? null;

                        if (!$businessEmail) {
                            $this->warn("⚠️  Tenant {$tenant->id}: Sin email registrado");
                            return;
                        }

                        // Enviar email según tipo
                        $this->sendTrialEmail($businessEmail, $tenant->business_name, $emailType, abs($daysRemaining));
                        $this->info("✅ Email '{$emailType}' enviado a {$businessEmail} (Tenant: {$tenant->id})");
                    });

                    $emailsSent++;
                } catch (\Exception $e) {
                    $this->error("❌ Error enviando email a tenant {$tenant->id}: {$e->getMessage()}");
                }
            }
        }

        $this->info("✅ Proceso completado. Emails enviados: {$emailsSent}");
    }

    /**
     * Enviar email de trial según tipo
     */
    private function sendTrialEmail($email, $businessName, $type, $daysRemaining)
    {
        $subject = '';
        $message = '';
        $upgradeUrl = env('APP_URL') . '/upgrade';

        switch ($type) {
            case 'one_day_left':
                $subject = "⏰ ¡Te queda 1 día de prueba gratis en 105 POS!";
                $message = "
                    <h2>Hola {$businessName},</h2>
                    <p>Tu prueba gratuita de <strong>105 POS</strong> termina mañana.</p>
                    <p>No pierdas acceso a todas las funciones premium que has estado usando:</p>
                    <ul>
                        <li>✅ Control total de inventario</li>
                        <li>✅ Reportes ejecutivos en tiempo real</li>
                        <li>✅ Gestión de cajas y ventas</li>
                        <li>✅ Y mucho más...</li>
                    </ul>
                    <p><a href='{$upgradeUrl}' style='display: inline-block; padding: 12px 24px; background: #1e40af; color: white; text-decoration: none; border-radius: 8px; font-weight: bold;'>Actualizar mi plan ahora</a></p>
                ";
                break;

            case 'last_day':
                $subject = "🚨 ¡Último día de tu prueba gratis en 105 POS!";
                $message = "
                    <h2>Hola {$businessName},</h2>
                    <p><strong>Hoy es tu último día</strong> de prueba gratuita de 105 POS.</p>
                    <p>Actualiza ahora y no pierdas:</p>
                    <ul>
                        <li>🔒 Acceso completo al sistema</li>
                        <li>📊 Toda tu información y datos</li>
                        <li>💼 Continuidad en tu operación</li>
                    </ul>
                    <p><a href='{$upgradeUrl}' style='display: inline-block; padding: 12px 24px; background: #dc2626; color: white; text-decoration: none; border-radius: 8px; font-weight: bold;'>Actualizar AHORA</a></p>
                ";
                break;

            case 'expired':
                $subject = "❌ Tu prueba de 105 POS ha expirado";
                $message = "
                    <h2>Hola {$businessName},</h2>
                    <p>Tu periodo de prueba de <strong>105 POS</strong> ha finalizado.</p>
                    <p>Para recuperar el acceso completo a tu sistema y todos tus datos, selecciona un plan:</p>
                    <ul>
                        <li>💼 <strong>Plan Emprendedor</strong>: $25,000/mes</li>
                        <li>🚀 <strong>Plan Negocio Pro</strong>: $50,000/mes (Recomendado)</li>
                    </ul>
                    <p><a href='{$upgradeUrl}' style='display: inline-block; padding: 12px 24px; background: #1e40af; color: white; text-decoration: none; border-radius: 8px; font-weight: bold;'>Ver planes y actualizar</a></p>
                ";
                break;
        }

        // Enviar email (usando Mail facade de Laravel)
        Mail::send([], [], function ($m) use ($email, $subject, $message) {
            $m->to($email)
                ->subject($subject)
                ->html($message);
        });
    }
}
