<?php

namespace App\Services;

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

/**
 * 📧 Servicio Centralizado de Emails - 105 POS
 *
 * Gestiona todos los envíos de email del sistema:
 * - Bienvenida al registrarse
 * - Recuperación de contraseña
 * - Notificaciones del sistema
 */
class EmailService
{
    /**
     * 🎉 Enviar email de bienvenida al registrar nuevo tenant
     *
     * @param array $data [
     *   'email' => 'usuario@example.com',
     *   'name' => 'Nombre del Usuario',
     *   'business_name' => 'Mi Empresa',
     *   'subdomain' => 'miempresa',
     *   'password' => 'password temporal (opcional)',
     *   'plan' => 'free/professional/premium/enterprise'
     * ]
     * @return bool
     */
    public static function sendWelcomeEmail(array $data): bool
    {
        try {
            $templateData = [
                'name' => $data['name'] ?? 'Usuario',
                'email' => $data['email'],
                'business_name' => $data['business_name'] ?? 'Tu Negocio',
                'subdomain' => $data['subdomain'],
                'url' => config('app.url'),
                'login_url' => config('app.url') . '/login',
                'password' => $data['password'] ?? null,
                'plan' => $data['plan'] ?? 'free',
                'year' => date('Y')
            ];

            Mail::send('emails.welcome', $templateData, function ($message) use ($data) {
                $message->to($data['email'], $data['name'] ?? 'Usuario')
                    ->subject('¡Bienvenido a 105 POS! 🎉 - Tu cuenta está lista');

                // Remitente profesional
                $message->from(
                    config('mail.from.address'),
                    config('mail.from.name')
                );
            });


            return true;
        } catch (\Exception $e) {
            Log::error('❌ Error enviando email de bienvenida', [
                'email' => $data['email'] ?? 'unknown',
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * 🔐 Enviar email de recuperación de contraseña
     *
     * @param array $data [
     *   'email' => 'usuario@example.com',
     *   'name' => 'Nombre del Usuario',
     *   'code' => '123456',
     *   'expires_at' => '2024-12-13 15:30:00'
     * ]
     * @return bool
     */
    public static function sendPasswordResetEmail(array $data): bool
    {
        try {
            $templateData = [
                'name' => $data['name'] ?? 'Usuario',
                'email' => $data['email'],
                'code' => $data['code'],
                'expires_minutes' => 15, // 15 minutos
                'year' => date('Y')
            ];

            Mail::send('emails.password-reset-code', $templateData, function ($message) use ($data) {
                $message->to($data['email'], $data['name'] ?? 'Usuario')
                    ->subject('105 POS: Codigo de verificacion de seguridad');

                $message->from(
                    config('mail.from.address'),
                    config('mail.from.name')
                );
            });


            return true;
        } catch (\Exception $e) {
            Log::error('❌ Error enviando email de recuperación', [
                'email' => $data['email'] ?? 'unknown',
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * ✅ Enviar confirmación de contraseña cambiada
     *
     * @param array $data [
     *   'email' => 'usuario@example.com',
     *   'name' => 'Nombre del Usuario',
     *   'changed_at' => '2024-12-13 14:30:00'
     * ]
     * @return bool
     */
    public static function sendPasswordChangedEmail(array $data): bool
    {
        try {
            $templateData = [
                'name' => $data['name'] ?? 'Usuario',
                'email' => $data['email'],
                'changed_at' => $data['changed_at'] ?? now()->format('Y-m-d H:i:s'),
                'support_url' => config('app.url') . '/support',
                'year' => date('Y')
            ];

            Mail::send('emails.password-changed', $templateData, function ($message) use ($data) {
                $message->to($data['email'], $data['name'] ?? 'Usuario')
                    ->subject('✅ Tu contraseña ha sido actualizada - 105 POS');

                $message->from(
                    config('mail.from.address'),
                    config('mail.from.name')
                );
            });


            return true;
        } catch (\Exception $e) {
            Log::error('❌ Error enviando email de confirmación', [
                'email' => $data['email'] ?? 'unknown',
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * 🔔 Enviar email genérico de notificación
     *
     * @param array $data [
     *   'email' => 'usuario@example.com',
     *   'name' => 'Nombre',
     *   'subject' => 'Asunto del email',
     *   'title' => 'Título principal',
     *   'message' => 'Mensaje HTML o texto',
     *   'cta_text' => 'Texto del botón (opcional)',
     *   'cta_url' => 'URL del botón (opcional)'
     * ]
     * @return bool
     */
    public static function sendNotification(array $data): bool
    {
        try {
            $templateData = [
                'name' => $data['name'] ?? 'Usuario',
                'title' => $data['title'],
                'message' => $data['message'],
                'cta_text' => $data['cta_text'] ?? null,
                'cta_url' => $data['cta_url'] ?? null,
                'year' => date('Y')
            ];

            Mail::send('emails.notification', $templateData, function ($message) use ($data) {
                $message->to($data['email'], $data['name'] ?? 'Usuario')
                    ->subject($data['subject']);

                $message->from(
                    config('mail.from.address'),
                    config('mail.from.name')
                );
            });


            return true;
        } catch (\Exception $e) {
            Log::error('❌ Error enviando notificación', [
                'email' => $data['email'] ?? 'unknown',
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }
}