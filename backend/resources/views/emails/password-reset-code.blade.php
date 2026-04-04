<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>105 POS: Codigo de verificacion de seguridad</title>
</head>
<body style="margin: 0; padding: 0; background-color: #F1F5F9; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif; -webkit-font-smoothing: antialiased;">

    <!-- Wrapper -->
    <table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color: #F1F5F9; padding: 48px 16px;">
        <tr>
            <td align="center" valign="top">

                <!-- Card principal -->
                <table width="100%" cellpadding="0" cellspacing="0" border="0" style="max-width: 580px; background-color: #FFFFFF; border-radius: 8px; overflow: hidden; border: 1px solid #E2E8F0;">

                    <!-- ===== CABECERA ===== -->
                    <tr>
                        <td style="padding: 32px 48px 28px 48px;">
                            <table width="100%" cellpadding="0" cellspacing="0" border="0">
                                <tr>
                                    <td valign="middle">
                                        <span style="font-size: 18px; font-weight: 800; color: #0F172A; letter-spacing: -0.3px;">105 POS</span>
                                        <span style="display: inline-block; margin-left: 8px; font-size: 11px; font-weight: 600; color: #64748B; text-transform: uppercase; letter-spacing: 1px; vertical-align: middle;">Pro</span>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Linea separadora -->
                    <tr>
                        <td style="padding: 0 48px;">
                            <div style="height: 1px; background-color: #E2E8F0;"></div>
                        </td>
                    </tr>

                    <!-- ===== CUERPO ===== -->
                    <tr>
                        <td style="padding: 40px 48px 32px 48px;">

                            <!-- Saludo -->
                            <p style="margin: 0 0 8px 0; font-size: 15px; font-weight: 400; color: #334155; line-height: 1.6;">Hola <strong style="color: #0F172A; font-weight: 600;">{{ $name }}</strong>,</p>

                            <!-- Descripcion -->
                            <p style="margin: 0 0 32px 0; font-size: 15px; font-weight: 400; color: #475569; line-height: 1.7;">
                                Hemos recibido una solicitud para acceder a tu cuenta en 105 POS. Por favor, introduce el siguiente codigo de verificacion para continuar:
                            </p>

                            <!-- Contenedor del codigo -->
                            <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-bottom: 32px;">
                                <tr>
                                    <td align="center" style="background-color: #F8FAFC; border: 1px solid #E2E8F0; border-radius: 8px; padding: 28px 24px;">
                                        <p style="margin: 0 0 10px 0; font-size: 11px; font-weight: 700; color: #94A3B8; text-transform: uppercase; letter-spacing: 2px;">Codigo de verificacion</p>
                                        <p style="margin: 0; font-size: 38px; font-weight: 700; color: #0F172A; letter-spacing: 12px; font-family: 'Courier New', 'Lucida Console', monospace;">{{ $code }}</p>
                                    </td>
                                </tr>
                            </table>

                            <!-- Contexto de seguridad -->
                            <p style="margin: 0; font-size: 13px; font-weight: 400; color: #64748B; line-height: 1.75;">
                                Este codigo expirara en <strong style="color: #475569;">15 minutos</strong>. Si no has intentado iniciar sesion ni solicitar este codigo, te recomendamos cambiar tu contrasena inmediatamente y contactar a soporte.
                            </p>

                        </td>
                    </tr>

                    <!-- Linea separadora -->
                    <tr>
                        <td style="padding: 0 48px;">
                            <div style="height: 1px; background-color: #E2E8F0;"></div>
                        </td>
                    </tr>

                    <!-- ===== FOOTER ===== -->
                    <tr>
                        <td style="padding: 24px 48px; text-align: center;">
                            <p style="margin: 0; font-size: 12px; font-weight: 400; color: #94A3B8; line-height: 1.6;">
                                &copy; {{ date('Y') }} 105 POS Pro. Todos los derechos reservados.
                            </p>
                        </td>
                    </tr>

                </table>
                <!-- /Card principal -->

            </td>
        </tr>
    </table>

</body>
</html>
