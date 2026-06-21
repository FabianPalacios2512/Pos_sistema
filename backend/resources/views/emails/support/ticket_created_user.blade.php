<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f4f4f5; margin: 0; padding: 20px; }
        .container { max-width: 600px; margin: 0 auto; background: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); }
        .header { background: #0f172a; padding: 24px; text-align: center; }
        .header img { height: 40px; }
        .content { padding: 32px; }
        .title { font-size: 20px; font-weight: 700; color: #1e293b; margin-bottom: 16px; }
        .text { color: #475569; line-height: 1.6; margin-bottom: 24px; }
        .ticket-box { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 20px; margin-bottom: 24px; text-align: center; }
        .ticket-number { font-size: 24px; font-weight: 800; color: #3b82f6; letter-spacing: 1px; }
        .ticket-details { text-align: left; margin-top: 16px; font-size: 14px; color: #64748b; }
        .ticket-details strong { color: #334155; }
        .footer { background: #f8fafc; padding: 20px; text-align: center; font-size: 12px; color: #94a3b8; }
        .footer a { color: #3b82f6; text-decoration: none; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1 style="color: white; margin: 0;">105 POS</h1>
        </div>
        <div class="content">
            <div class="title">Hola {{ $ticket->user_name }}, hemos recibido tu solicitud de soporte.</div>
            <div class="text">
                Gracias por contactarte con el equipo de soporte técnico de 105 POS. Hemos generado un ticket para tu caso y nuestro equipo te responderá lo más pronto posible.
            </div>
            
            <div class="ticket-box">
                <div style="font-size: 12px; color: #64748b; text-transform: uppercase; font-weight: 700; margin-bottom: 8px;">Número de Caso</div>
                <div class="ticket-number">{{ $ticket->ticket_number }}</div>
                
                <div class="ticket-details">
                    <div><strong>Asunto:</strong> {{ $ticket->subject }}</div>
                    <div style="margin-top: 8px;"><strong>Fecha:</strong> {{ $ticket->created_at->format('d/m/Y H:i A') }}</div>
                </div>
            </div>
            
            <div class="text" style="font-size: 14px;">
                Te mantendremos informado sobre el progreso de tu caso. Si necesitas agregar información adicional, por favor responde a este correo.
            </div>
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} 105 POS - Sistema Empresarial Inteligente. Todos los derechos reservados.<br>
            Este es un correo automático, pero puedes responder a él directamente.
        </div>
    </div>
</body>
</html>
