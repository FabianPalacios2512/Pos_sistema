<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f1f5f9; margin: 0; padding: 20px; }
        .container { max-width: 600px; margin: 0 auto; background: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); border-top: 4px solid #ef4444; }
        .header { background: #fff; padding: 24px; border-bottom: 1px solid #e2e8f0; }
        .title { font-size: 18px; font-weight: 700; color: #ef4444; margin: 0; }
        .content { padding: 32px; }
        .row { margin-bottom: 16px; border-bottom: 1px solid #f1f5f9; padding-bottom: 16px; }
        .row:last-child { border-bottom: none; }
        .label { font-size: 12px; color: #64748b; text-transform: uppercase; font-weight: 700; margin-bottom: 4px; }
        .value { font-size: 15px; color: #1e293b; }
        .desc-box { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; padding: 16px; font-size: 14px; color: #334155; line-height: 1.5; white-space: pre-wrap; margin-top: 8px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1 class="title">NUEVO CASO DE SOPORTE REPORTADO</h1>
        </div>
        <div class="content">
            <div class="row">
                <div class="label">Número de Ticket</div>
                <div class="value" style="font-weight: 700; color: #0f172a;">{{ $ticket->ticket_number }}</div>
            </div>
            <div class="row">
                <div class="label">Empresa (Tenant ID)</div>
                <div class="value">{{ $ticket->tenant_id ?? 'No especificado / Público' }}</div>
            </div>
            <div class="row">
                <div class="label">Usuario Remitente</div>
                <div class="value">{{ $ticket->user_name }} &lt;{{ $ticket->user_email }}&gt;</div>
            </div>
            <div class="row">
                <div class="label">Asunto</div>
                <div class="value font-weight-bold">{{ $ticket->subject }}</div>
            </div>
            <div class="row">
                <div class="label">Descripción del Problema</div>
                <div class="desc-box">{{ $ticket->description }}</div>
            </div>
        </div>
    </div>
</body>
</html>
