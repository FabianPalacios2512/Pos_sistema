<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Actualización en tu caso de soporte</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-w-lg mx-auto; padding: 20px;">
    
    <div style="background-color: #f8fafc; padding: 20px; border-radius: 8px; border: 1px solid #e2e8f0; margin-bottom: 20px;">
        <h2 style="color: #0f172a; margin-top: 0;">¡Hola, {{ $ticket->user_name }}!</h2>
        <p>El equipo de soporte de 105 POS ha respondido a tu caso <strong>{{ $ticket->ticket_number }}</strong>.</p>
    </div>

    <div style="background-color: #ffffff; padding: 20px; border-left: 4px solid #3b82f6; border-radius: 4px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); margin-bottom: 20px;">
        <p style="margin-top: 0; color: #64748b; font-size: 13px;">Respuesta del administrador:</p>
        <p style="font-size: 15px; color: #1e293b; white-space: pre-wrap;">{{ $replyMessage->message }}</p>
    </div>

    <div style="background-color: #f1f5f9; padding: 15px; border-radius: 8px; font-size: 14px; margin-bottom: 20px;">
        <p style="margin: 0;"><strong>Asunto original:</strong> {{ $ticket->subject }}</p>
        <p style="margin: 5px 0 0 0;"><strong>Estado actual:</strong> 
            @if($ticket->status === 'open') Abierto
            @elseif($ticket->status === 'in_progress') En progreso
            @elseif($ticket->status === 'resolved') Resuelto
            @else Cerrado @endif
        </p>
    </div>

    <p style="font-size: 14px; color: #475569;">
        Puedes ver el historial completo y responder de vuelta iniciando sesión en tu panel de control de 105 POS, en la sección de <strong>Centro de Ayuda > Mis Casos</strong>.
    </p>

    <hr style="border: 0; border-top: 1px solid #e2e8f0; margin: 30px 0;">
    
    <p style="font-size: 12px; color: #94a3b8; text-align: center;">
        Este es un mensaje automático del sistema de soporte de 105 POS.<br>
        Por favor no respondas directamente a este correo.
    </p>

</body>
</html>
