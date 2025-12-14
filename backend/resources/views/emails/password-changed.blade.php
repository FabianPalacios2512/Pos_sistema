<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contraseña Actualizada</title>
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
                            <h1 style="margin: 0; font-size: 22px; font-weight: 700; color: #0f172a;">Contraseña Actualizada</h1>
                        </td>
                    </tr>

                    <!-- Badge de Éxito -->
                    <tr>
                        <td style="padding: 24px 40px 0 40px;">
                            <div style="background-color: #d1fae5; border-left: 3px solid #10b981; padding: 12px 16px; margin-bottom: 24px;">
                                <p style="margin: 0; font-size: 13px; color: #065f46; font-weight: 600;">
                                    Tu contraseña se cambió correctamente
                                </p>
                            </div>
                        </td>
                    </tr>

                    <!-- Contenido -->
                    <tr>
                        <td style="padding: 0 40px 32px 40px;">
                            <p style="margin: 0 0 16px 0; font-size: 14px; color: #64748b; line-height: 1.6;">
                                La contraseña de tu cuenta <strong style="color: #0f172a;">{{ $email }}</strong> ha sido actualizada exitosamente.
                            </p>

                            <!-- Info Compacta -->
                            <table width="100%" cellpadding="0" cellspacing="0" style="margin: 0 0 20px 0; background-color: #f8fafc; border: 1px solid #e2e8f0;">
                                <tr>
                                    <td style="padding: 12px 16px; border-bottom: 1px solid #e2e8f0;">
                                        <span style="font-size: 12px; color: #64748b; font-weight: 600;">Fecha:</span>
                                        <span style="font-size: 12px; color: #0f172a; float: right;">{{ $changed_at }}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 12px 16px;">
                                        <span style="font-size: 12px; color: #64748b; font-weight: 600;">Estado:</span>
                                        <span style="font-size: 12px; color: #10b981; float: right; font-weight: 600;">Actualizada</span>
                                    </td>
                                </tr>
                            </table>

                            <p style="margin: 0; font-size: 13px; color: #94a3b8; line-height: 1.6;">
                                Si no realizaste este cambio, contacta a soporte inmediatamente.
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
