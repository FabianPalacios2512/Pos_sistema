<?php

namespace App\Http\Controllers\Api\Central;

use App\Http\Controllers\Controller;
use App\Models\Central\SupportTicket;
use App\Models\Central\SupportTicketMessage;
use App\Mail\TicketCreatedUserMail;
use App\Mail\TicketCreatedAdminMail;
use App\Mail\TicketRepliedMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;

class SupportTicketController extends Controller
{
    /**
     * Crear un nuevo caso de soporte (Endpoint público/usuario)
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_email' => 'required|email|max:255',
            'cc_emails' => 'nullable|string|max:500',
            'user_name' => 'required|string|max:255',
            'subject' => 'required|string|max:255',
            'description' => 'required|string|max:5000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // Generar número de caso: CASO-105-YYYYMMDD-XXXX
        $date = now()->format('Ymd');
        $randomStr = strtoupper(Str::random(4));
        // Alternativa: Podríamos usar un contador en lugar de random para el XXXX
        // Pero random evita problemas de concurrencia de forma simple.
        $ticketNumber = "CASO-105-{$date}-{$randomStr}";

        // Asegurar unicidad (muy poco probable que choque por el random, pero por seguridad)
        while (SupportTicket::where('ticket_number', $ticketNumber)->exists()) {
            $randomStr = strtoupper(Str::random(4));
            $ticketNumber = "CASO-105-{$date}-{$randomStr}";
        }

        // Intentar obtener el tenant_id si viene en el request o si podemos extraerlo (en caso de usarse desde el POS)
        // Como la ruta podría llamarse desde un subdominio, el frontend puede enviarlo explícitamente.
        $tenantId = $request->input('tenant_id', tenant('id'));

        // Fallback robusto: extraer del host
        if (!$tenantId) {
            $host = $request->getHost();
            $parts = explode('.', $host);
            if (count($parts) >= 2 && $parts[0] !== 'www') {
                $tenantId = str_replace('-', '_', $parts[0]);
            }
        }

        $ticket = SupportTicket::create([
            'ticket_number' => $ticketNumber,
            'tenant_id' => $tenantId,
            'user_email' => $request->input('user_email'),
            'cc_emails' => $request->input('cc_emails'),
            'user_name' => $request->input('user_name'),
            'subject' => $request->input('subject'),
            'description' => $request->input('description'),
            'status' => 'open',
        ]);

        // Enviar correos
        try {
            // Preparar correo al usuario
            $userMail = Mail::to($ticket->user_email);
            
            // Añadir CC si existen
            if ($ticket->cc_emails) {
                $ccList = array_map('trim', explode(',', $ticket->cc_emails));
                $ccList = array_filter($ccList, function($email) {
                    return filter_var($email, FILTER_VALIDATE_EMAIL);
                });
                if (!empty($ccList)) {
                    $userMail->cc($ccList);
                }
            }
            
            // Enviar
            $userMail->send(new TicketCreatedUserMail($ticket));
            
            // Correo al Admin de 105 POS
            Mail::to('105code.pos@gmail.com')->send(new TicketCreatedAdminMail($ticket));
        } catch (\Exception $e) {
            \Log::error('Error al enviar correos de ticket: ' . $e->getMessage());
            // No detenemos la respuesta exitosa si falla el correo.
        }

        return response()->json([
            'success' => true,
            'message' => 'Caso generado exitosamente',
            'ticket' => $ticket
        ]);
    }

    /**
     * Obtener todos los casos (Para el God Mode)
     */
    public function indexAdmin(Request $request)
    {
        try {
            $query = SupportTicket::with('messages');

            // Filtros (opcional)
            if ($request->filled('status')) {
                $status = $request->input('status');
                // Evitar error 500 por Data Truncated (Enum) en strict mode
                if (in_array($status, ['open', 'in_progress', 'resolved', 'closed'])) {
                    $query->where('status', $status);
                }
            }
            if ($request->filled('search')) {
                $search = $request->input('search');
                $query->where(function($q) use ($search) {
                    $q->where('ticket_number', 'like', "%{$search}%")
                      ->orWhere('user_name', 'like', "%{$search}%")
                      ->orWhere('user_email', 'like', "%{$search}%")
                      ->orWhere('subject', 'like', "%{$search}%")
                      ->orWhere('tenant_id', 'like', "%{$search}%");
                });
            }

            $tickets = $query->orderBy('created_at', 'desc')->paginate(20);

            return response()->json([
                'success' => true,
                'tickets' => $tickets
            ]);
        } catch (\Exception $e) {
            \Log::error('Error fetching admin support tickets: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error fetching tickets: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener casos de un tenant específico (Para el Cliente / Mis Casos)
     */
    public function indexUser(Request $request)
    {
        try {
            $tenantId = $request->input('tenant_id', tenant('id'));
            
            // Fallback robusto: extraer del host
            if (!$tenantId) {
                $host = $request->getHost();
                $parts = explode('.', $host);
                if (count($parts) >= 2 && $parts[0] !== 'www') {
                    $tenantId = str_replace('-', '_', $parts[0]);
                }
            }
            
            if (!$tenantId) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se pudo identificar el tenant'
                ], 400);
            }

            $query = SupportTicket::with('messages')->where('tenant_id', $tenantId);

            $tickets = $query->orderBy('created_at', 'desc')->get();

            return response()->json([
                'success' => true,
                'tickets' => $tickets
            ]);
        } catch (\Exception $e) {
            \Log::error('Error fetching user support tickets: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error fetching tickets: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Actualizar estado de un caso (Para el God Mode)
     */
    public function updateStatusAdmin(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:open,in_progress,resolved,closed'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $ticket = SupportTicket::find($id);

        if (!$ticket) {
            return response()->json([
                'success' => false,
                'message' => 'Ticket no encontrado'
            ], 404);
        }

        $ticket->status = $request->input('status');
        $ticket->save();

        return response()->json([
            'success' => true,
            'message' => 'Estado actualizado exitosamente',
            'ticket' => $ticket
        ]);
    }

    /**
     * Responder a un caso desde God Mode (Admin)
     */
    public function replyAdmin(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'message' => 'required|string|max:5000',
            'status' => 'nullable|in:open,in_progress,resolved,closed' // Permite cambiar el estado al mismo tiempo
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $ticket = SupportTicket::find($id);

        if (!$ticket) {
            return response()->json(['success' => false, 'message' => 'Ticket no encontrado'], 404);
        }

        // Crear el mensaje
        $reply = SupportTicketMessage::create([
            'support_ticket_id' => $ticket->id,
            'sender_type' => 'admin',
            'message' => $request->input('message')
        ]);

        // Actualizar el estado si se envía, o ponerlo en in_progress por defecto si estaba abierto
        $newStatus = $request->input('status');
        if (!$newStatus && $ticket->status === 'open') {
            $newStatus = 'in_progress';
        }
        
        if ($newStatus) {
            $ticket->status = $newStatus;
            $ticket->save();
        }

        // Enviar correo al usuario
        try {
            $mail = Mail::to($ticket->user_email);
            
            // Si hay correos en CC
            if ($ticket->cc_emails) {
                // Separar por coma y limpiar espacios
                $ccList = array_map('trim', explode(',', $ticket->cc_emails));
                // Filtrar los válidos
                $ccList = array_filter($ccList, function($email) {
                    return filter_var($email, FILTER_VALIDATE_EMAIL);
                });
                
                if (!empty($ccList)) {
                    $mail->cc($ccList);
                }
            }
            
            $mail->send(new TicketRepliedMail($ticket, $reply));
        } catch (\Exception $e) {
            \Log::error('Error al enviar correo de respuesta de ticket: ' . $e->getMessage());
        }

        return response()->json([
            'success' => true,
            'message' => 'Respuesta enviada exitosamente',
            'ticket' => $ticket->load('messages') // Devuelve el ticket actualizado con sus mensajes
        ]);
    }

    /**
     * Responder a un caso desde Cliente (User)
     */
    public function replyUser(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'message' => 'required|string|max:5000'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $tenantId = $request->input('tenant_id', tenant('id'));

        // Fallback robusto: extraer del host
        if (!$tenantId) {
            $host = $request->getHost();
            $parts = explode('.', $host);
            if (count($parts) >= 2 && $parts[0] !== 'www') {
                $tenantId = str_replace('-', '_', $parts[0]);
            }
        }

        $ticket = SupportTicket::where('id', $id)->where('tenant_id', $tenantId)->first();

        if (!$ticket) {
            return response()->json(['success' => false, 'message' => 'Ticket no encontrado'], 404);
        }

        // Si el ticket está resuelto o cerrado, no permitir al usuario responder
        if (in_array($ticket->status, ['resolved', 'closed'])) {
            return response()->json(['success' => false, 'message' => 'Este caso ya se encuentra resuelto o cerrado y no admite nuevas respuestas.'], 403);
        }

        // Crear el mensaje
        SupportTicketMessage::create([
            'support_ticket_id' => $ticket->id,
            'sender_type' => 'user',
            'message' => $request->input('message')
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Respuesta enviada exitosamente',
            'ticket' => $ticket->load('messages')
        ]);
    }
}
