<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class EmailController extends Controller
{
    /**
     * 📧 Enviar email genérico (HTML personalizado)
     * Usado para mensajes de bienvenida, notificaciones, etc.
     */
    public function sendGenericEmail(Request $request)
    {
        try {
            $validated = $request->validate([
                'to' => 'required|email',
                'subject' => 'required|string|max:255',
                'html' => 'required|string'
            ]);

            // Enviar email usando la configuración de Laravel Mail
            Mail::send([], [], function ($message) use ($validated) {
                $message->to($validated['to'])
                    ->subject($validated['subject'])
                    ->html($validated['html']);
            });


            return response()->json([
                'success' => true,
                'message' => 'Email enviado correctamente'
            ]);

        } catch (\Exception $e) {
            Log::error('❌ Error enviando email', [
                'error' => $e->getMessage(),
                'to' => $request->to ?? 'unknown'
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al enviar el email',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}