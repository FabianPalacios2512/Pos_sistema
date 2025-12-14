<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recupera tu contraseña</title>
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
            background: linear-gradient(135deg, #dc2626 0%, #ef4444 100%);
            padding: 40px 30px;
            text-align: center;
        }
        .header-icon {
            font-size: 48px;
            margin-bottom: 16px;
        }
        .header h1 {
            color: white;
            font-size: 26px;
            font-weight: 700;
            margin-bottom: 8px;
        }
        .header p {
            color: #fecaca;
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
        .warning-box {
            background: #fef3c7;
            border-left: 4px solid #f59e0b;
            padding: 20px;
            margin: 30px 0;
            border-radius: 8px;
        }
        .warning-box p {
            color: #92400e;
            font-size: 14px;
            margin-bottom: 8px;
        }
        .warning-box p:last-child {
            margin-bottom: 0;
        }
        .cta-button {
            display: inline-block;
            background: linear-gradient(135deg, #dc2626 0%, #ef4444 100%);
            color: white;
            text-decoration: none;
            padding: 18px 40px;
            border-radius: 10px;
            font-weight: 600;
            font-size: 17px;
            margin: 30px 0;
            box-shadow: 0 4px 12px rgba(220, 38, 38, 0.3);
            transition: all 0.3s;
        }
        .cta-button:hover {
            background: linear-gradient(135deg, #b91c1c 0%, #dc2626 100%);
            box-shadow: 0 6px 16px rgba(220, 38, 38, 0.4);
        }
        .token-box {
            background: #f8fafc;
            border: 2px dashed #cbd5e1;
            border-radius: 12px;
            padding: 20px;
            margin: 30px 0;
            text-align: center;
        }
        .token-box p {
            color: #64748b;
            font-size: 13px;
            margin-bottom: 12px;
        }
        .token-code {
            font-family: 'Courier New', monospace;
            font-size: 18px;
            font-weight: 600;
            color: #0f172a;
            background: white;
            padding: 12px 20px;
            border-radius: 8px;
            display: inline-block;
            border: 1px solid #e2e8f0;
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
        .security-notice {
            background: #f1f5f9;
            border-radius: 10px;
            padding: 20px;
            margin: 30px 0;
        }
        .security-notice h3 {
            color: #0f172a;
            font-size: 16px;
            margin-bottom: 12px;
            font-weight: 600;
        }
        .security-notice ul {
            list-style: none;
            padding-left: 0;
        }
        .security-notice li {
            color: #475569;
            font-size: 14px;
            margin-bottom: 8px;
            padding-left: 24px;
            position: relative;
        }
        .security-notice li:before {
            content: "🔒";
            position: absolute;
            left: 0;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <!-- Header -->
        <div class="header">
            <div class="header-icon">🔐</div>
            <h1>Recuperación de Contraseña</h1>
            <p>105 POS - Solicitud de restablecimiento</p>
        </div>

        <!-- Content -->
        <div class="content">
            <div class="greeting">
                Hola {{ $name }},
            </div>

            <div class="message">
                <p>Recibimos una solicitud para restablecer la contraseña de tu cuenta <strong>{{ $email }}</strong>.</p>
                <p>Si no realizaste esta solicitud, puedes ignorar este email de forma segura.</p>
            </div>

            <!-- CTA Button -->
            <center>
                <a href="{{ $reset_url }}" class="cta-button">
                    🔓 Restablecer mi contraseña
                </a>
            </center>

            <!-- Warning Box -->
            <div class="warning-box">
                <p><strong>⏰ Este enlace expira en {{ $expires_minutes }} minutos</strong></p>
                <p>Por seguridad, el enlace de recuperación solo es válido por tiempo limitado.</p>
            </div>

            <!-- Alternative Token Box (por si no funciona el botón) -->
            <div class="token-box">
                <p>Si el botón no funciona, copia y pega este enlace en tu navegador:</p>
                <div class="token-code" style="word-break: break-all; font-size: 12px;">
                    {{ $reset_url }}
                </div>
            </div>

            <!-- Security Notice -->
            <div class="security-notice">
                <h3>🔒 Consejos de Seguridad</h3>
                <ul>
                    <li>Nunca compartas tu contraseña con nadie</li>
                    <li>Usa una contraseña única y fuerte (mínimo 8 caracteres)</li>
                    <li>No reutilices contraseñas de otras cuentas</li>
                    <li>Si no solicitaste este cambio, contacta a soporte inmediatamente</li>
                </ul>
            </div>

            <div class="message">
                <p>Si tienes problemas para restablecer tu contraseña o no solicitaste este cambio, contacta a nuestro equipo de soporte.</p>
                <p style="margin-top: 20px;">
                    <strong>Equipo de Seguridad 105 POS</strong><br>
                    <a href="mailto:support@105pos.com" style="color: #dc2626;">support@105pos.com</a>
                </p>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p><strong>105 POS</strong> - Sistema de Gestión Empresarial</p>

            <p style="margin-top: 20px; font-size: 11px; color: #94a3b8;">
                Este email fue enviado a {{ $email }} porque solicitaste restablecer tu contraseña.<br>
                Si no reconoces esta actividad, ignora este mensaje o contacta a soporte.<br>
                © {{ $year }} 105 POS. Todos los derechos reservados.
            </p>
        </div>
    </div>
</body>
</html>
