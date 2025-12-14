<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Código de Recuperación</title>
</head>
<body style="margin: 0; padding: 0; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Arial, sans-serif; background-color: #f8fafc; line-height: 1.6;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background-color: #f8fafc; padding: 40px 20px;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" style="background-color: white; max-width: 600px;">

                    <!-- Header Compacto -->
                    <tr>
                        <td style="padding: 32px 40px 24px 40px; text-align: center; border-bottom: 1px solid #e2e8f0;">
                            <div style="font-size: 16px; font-weight: 700; color: #0f172a; margin-bottom: 12px; letter-spacing: 1px;">105 POS</div>
                            <h1 style="margin: 0; font-size: 22px; font-weight: 700; color: #0f172a;">Código de Recuperación</h1>
                        </td>
                    </tr>

                    <!-- Contenido -->
                    <tr>
                        <td style="padding: 32px 40px;">
                            <p style="margin: 0 0 16px 0; font-size: 15px; color: #475569;">Hola <strong>{{ $name }}</strong>,</p>

                            <p style="margin: 0 0 24px 0; font-size: 14px; color: #64748b; line-height: 1.6;">
                                Recibimos una solicitud para restablecer tu contraseña. Usa el siguiente código:
                            </p>

                            <!-- Código -->
                            <table width="100%" cellpadding="0" cellspacing="0" style="margin: 0 0 24px 0;">
                                <tr>
                                    <td style="background-color: #0f172a; padding: 24px; text-align: center;">
                                        <div style="color: rgba(255,255,255,0.6); font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 8px;">Tu Código</div>
                                        <div style="color: white; font-size: 36px; font-weight: 700; letter-spacing: 8px; font-family: 'Courier New', monospace;">{{ $code }}</div>
                                    </td>
                                </tr>
                            </table>

                            <p style="margin: 0 0 16px 0; font-size: 13px; color: #94a3b8;">
                                <strong style="color: #475569;">Expira en 15 minutos.</strong> Si no solicitaste este cambio, ignora este correo.
                            </p>
                        </td>
                    </tr>

                    <!-- Footer Compacto -->
                    <tr>
                        <td style="padding: 24px 40px; background-color: #f8fafc; border-top: 1px solid #e2e8f0; text-align: center;">
                            <p style="margin: 0 0 4px 0; font-size: 13px; color: #64748b;">
                                <strong style="color: #0f172a;">105 POS</strong> - Sistema de Gestión Empresarial
                            </p>
                            <p style="margin: 0; font-size: 12px; color: #94a3b8;">
                                © {{ date('Y') }} 105 POS. Todos los derechos reservados.
                            </p>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>
</html>
