<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f4f4f5;
        }
        .email-container {
            max-width: 600px;
            margin: 40px auto;
            background: white;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .header {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            padding: 40px 30px;
            text-align: center;
        }
        .logo {
            width: 60px;
            height: 60px;
            margin-bottom: 20px;
        }
        .header h1 {
            color: white;
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 8px;
        }
        .header p {
            color: #94a3b8;
            font-size: 14px;
        }
        .content {
            padding: 40px 30px;
        }
        .greeting {
            font-size: 22px;
            font-weight: 600;
            color: #0f172a;
            margin-bottom: 20px;
        }
        .message {
            font-size: 16px;
            color: #475569;
            margin-bottom: 30px;
            line-height: 1.8;
        }
        .cta-button {
            display: inline-block;
            background: linear-gradient(135deg, #0f172a 0%, #334155 100%);
            color: white;
            text-decoration: none;
            padding: 16px 36px;
            border-radius: 10px;
            font-weight: 600;
            font-size: 16px;
            margin: 30px 0;
            box-shadow: 0 4px 12px rgba(15, 23, 42, 0.3);
            transition: all 0.3s;
        }
        .cta-button:hover {
            background: linear-gradient(135deg, #000000 0%, #1e293b 100%);
            box-shadow: 0 6px 16px rgba(15, 23, 42, 0.4);
        }
        .footer {
            background: #f8fafc;
            padding: 30px;
            text-align: center;
            border-top: 1px solid #e2e8f0;
        }
        .footer p {
            color: #64748b;
            font-size: 13px;
            margin-bottom: 8px;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <!-- Header -->
        <div class="header">
            <h1>{{ $title }}</h1>
            <p>105 POS - Sistema de Gestión Empresarial</p>
        </div>

        <!-- Content -->
        <div class="content">
            <div class="greeting">
                Hola {{ $name }},
            </div>

            <div class="message">
                {!! $message !!}
            </div>

            @if(isset($cta_url) && isset($cta_text))
            <!-- CTA Button -->
            <center>
                <a href="{{ $cta_url }}" class="cta-button">
                    {{ $cta_text }}
                </a>
            </center>
            @endif

            <div class="message">
                <p style="margin-top: 20px;">
                    <strong>Equipo de 105 POS</strong>
                </p>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p><strong>105 POS</strong> - Sistema de Gestión Empresarial</p>
            <p>Soporte: <a href="mailto:support@105pos.com" style="color: #0f172a;">support@105pos.com</a></p>

            <p style="margin-top: 20px; font-size: 11px; color: #94a3b8;">
                © {{ $year }} 105 POS. Todos los derechos reservados.
            </p>
        </div>
    </div>
</body>
</html>
