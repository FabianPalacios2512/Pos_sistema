<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bienvenido a 105 POS</title>
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
            font-size: 24px;
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
        .credentials-box {
            background: #f8fafc;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            padding: 24px;
            margin: 30px 0;
        }
        .credentials-box h3 {
            color: #0f172a;
            font-size: 18px;
            margin-bottom: 16px;
            font-weight: 600;
        }
        .credential-item {
            display: flex;
            justify-content: space-between;
            padding: 12px 0;
            border-bottom: 1px solid #e2e8f0;
        }
        .credential-item:last-child {
            border-bottom: none;
        }
        .credential-label {
            color: #64748b;
            font-weight: 500;
        }
        .credential-value {
            color: #0f172a;
            font-weight: 600;
            font-family: 'Courier New', monospace;
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
        .features {
            background: #f8fafc;
            border-radius: 12px;
            padding: 24px;
            margin: 30px 0;
        }
        .feature-item {
            display: flex;
            align-items: start;
            margin-bottom: 16px;
        }
        .feature-item:last-child {
            margin-bottom: 0;
        }
        .feature-icon {
            width: 24px;
            height: 24px;
            background: #0f172a;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 12px;
            flex-shrink: 0;
        }
        .feature-text {
            color: #475569;
            font-size: 14px;
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
        .social-links {
            margin: 20px 0;
        }
        .social-links a {
            color: #64748b;
            text-decoration: none;
            margin: 0 10px;
            font-size: 13px;
        }
        .plan-badge {
            display: inline-block;
            background: #10b981;
            color: white;
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            margin-left: 10px;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <!-- Header -->
        <div class="header">
            <div style="width: 80px; height: 80px; background: white; border-radius: 16px; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px;">
                <img src="{{ $url }}/logo.png" alt="105 POS Logo" style="width: 50px; height: 50px; object-fit: contain;">
            </div>
            <h1>105 POS</h1>
            <p>Sistema de Gestión Empresarial</p>
        </div>

        <!-- Content -->
        <div class="content">
            <div class="greeting">
                🎉 ¡Bienvenido, {{ $name }}!
            </div>

            <div class="message">
                <p>Estamos emocionados de tenerte en <strong>105 POS</strong>. Tu cuenta ha sido creada exitosamente y ya puedes comenzar a gestionar tu negocio de forma profesional.</p>
            </div>

            <!-- Credenciales -->
            <div class="credentials-box">
                <h3>📋 Información de tu Cuenta</h3>
                <div class="credential-item">
                    <span class="credential-label">Negocio:</span>
                    <span class="credential-value">{{ $business_name }}</span>
                </div>
                <div class="credential-item">
                    <span class="credential-label">Email:</span>
                    <span class="credential-value">{{ $email }}</span>
                </div>
                <div class="credential-item">
                    <span class="credential-label">Tu Sitio:</span>
                    <span class="credential-value">{{ $subdomain }}.105pos.pro</span>
                </div>
            </div>

            <!-- CTA Button -->
            <center>
                <a href="{{ $login_url }}" class="cta-button" style="display: inline-block; background: linear-gradient(135deg, #0f172a 0%, #334155 100%); color: white; text-decoration: none; padding: 18px 48px; border-radius: 12px; font-weight: 600; font-size: 17px; margin: 30px 0; box-shadow: 0 4px 12px rgba(15, 23, 42, 0.3);">
                    🚀 Acceder a mi cuenta
                </a>
            </center>

            <!-- Features -->
            <div class="features">
                <div class="feature-item">
                    <div class="feature-icon">✓</div>
                    <div class="feature-text">
                        <strong>Punto de Venta Profesional:</strong> Vende rápido y gestiona inventario en tiempo real
                    </div>
                </div>
                <div class="feature-item">
                    <div class="feature-icon">✓</div>
                    <div class="feature-text">
                        <strong>Reportes y Analytics:</strong> Toma decisiones basadas en datos reales de tu negocio
                    </div>
                </div>
                <div class="feature-item">
                    <div class="feature-icon">✓</div>
                    <div class="feature-text">
                        <strong>Multi-usuario:</strong> Asigna roles y permisos a tu equipo de trabajo
                    </div>
                </div>
                @if(in_array($plan, ['premium', 'enterprise']))
                <div class="feature-item">
                    <div class="feature-icon">✓</div>
                    <div class="feature-text">
                        <strong>Multi-sede:</strong> Gestiona múltiples sucursales desde un solo lugar
                    </div>
                </div>
                @endif
            </div>

            <div class="message">
                <p>Si tienes alguna pregunta o necesitas ayuda, no dudes en contactarnos. Estamos aquí para ayudarte a tener éxito.</p>
                <p style="margin-top: 20px;">
                    <strong>¡Éxitos con tu negocio! 🚀</strong><br>
                    El equipo de 105 POS
                </p>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p><strong>105 POS</strong> - Sistema de Gestión Empresarial</p>
            <p>{{ $url }}</p>

            <div class="social-links">
                <a href="mailto:support@105pos.com">Soporte</a> •
                <a href="{{ $url }}/docs">Documentación</a> •
                <a href="{{ $url }}/help">Centro de Ayuda</a>
            </div>

            <p style="margin-top: 20px; font-size: 11px; color: #94a3b8;">
                Este email fue enviado a {{ $email }} porque creaste una cuenta en 105 POS.<br>
                © {{ $year }} 105 POS. Todos los derechos reservados.
            </p>
        </div>
    </div>
</body>
</html>
