# 📧 CONFIGURACIÓN DE EMAILS - 105 POS CRM

## 🎯 Resumen Ejecutivo
Este documento explica cómo configurar el sistema de emails para:
- ✅ Email de bienvenida al registrarse
- 🔐 Recuperación de contraseña
- ✅ Confirmación de cambio de contraseña
- 🔔 Notificaciones del sistema

---

## 📋 Opciones de Configuración

### ✅ **Opción 1: Gmail con App Password (RECOMENDADA PARA EMPEZAR)**

#### Ventajas:
- ✅ Funciona inmediatamente
- ✅ GRATIS (500 emails/día)
- ✅ No requiere dominio personalizado
- ✅ Fácil de configurar

#### Pasos:

1. **Crear App Password en Gmail:**
   - Ve a: https://myaccount.google.com/security
   - Activa "Verificación en 2 pasos" (si no lo tienes)
   - Ve a "Contraseñas de aplicaciones"
   - Genera una contraseña para "Correo"
   - Copia la contraseña de 16 caracteres

2. **Configurar `.env` en backend:**

```env
# EMAILS (Gmail)
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=105pos@gmail.com
MAIL_PASSWORD=xxxx xxxx xxxx xxxx  # Tu App Password de 16 dígitos
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=105pos@gmail.com
MAIL_FROM_NAME="105 POS"
```

3. **Probar**:
```bash
cd backend
php artisan tinker
>>> Mail::raw('Test email', function($msg) { $msg->to('tu_email@example.com')->subject('Test'); });
```

---

### 🚀 **Opción 2: Cloudflare Email Routing + Gmail (PROFESIONAL)**

#### Ventajas:
- ✅ Dominio personalizado: `support@105pos.com`
- ✅ GRATIS (sin límites)
- ✅ Profesional y confiable
- ✅ Redirección automática a Gmail

#### Pasos:

1. **Configurar en Cloudflare:**
   - Panel Cloudflare → Email → Email Routing
   - Habilitar Email Routing
   - Agregar dominio: `105pos.com`
   - Crear alias: `support@105pos.com` → `105pos@gmail.com`
   - Verificar DNS records automáticos

2. **Configurar DNS (Cloudflare hace esto automático):**
```dns
MX    @    v=mx1.cloudflare.net    Priority: 10
TXT   @    v=spf1 include:_spf.mx.cloudflare.net ~all
```

3. **Configurar `.env` (igual que Gmail):**
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=105pos@gmail.com
MAIL_PASSWORD=xxxx xxxx xxxx xxxx
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=support@105pos.com  # ← Dominio personalizado
MAIL_FROM_NAME="105 POS Support"
```

**Nota**: Los emails se envían desde Gmail pero aparecen como `support@105pos.com` (si configuras Gmail Reply-To)

---

### 💼 **Opción 3: SendGrid (ESCALABLE)**

#### Ventajas:
- ✅ 100 emails/día GRATIS
- ✅ Analytics y tracking
- ✅ APIs profesionales
- ✅ Excelente deliverability

#### Pasos:

1. **Crear cuenta en SendGrid:**
   - https://sendgrid.com/free/
   - Verificar email
   - Crear API Key

2. **Configurar `.env`:**
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.sendgrid.net
MAIL_PORT=587
MAIL_USERNAME=apikey
MAIL_PASSWORD=SG.xxxxxxxxxxxxxxxxxxxxxxxxx  # Tu API Key
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=support@105pos.com
MAIL_FROM_NAME="105 POS"
```

---

## 🛠️ Configuración del Sistema

### 1. Variables de Entorno Necesarias

Agrega estas variables a `/backend/.env`:

```env
# ==========================================
# 📧 CONFIGURACIÓN DE EMAILS
# ==========================================

# Proveedor de Email
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=105pos@gmail.com
MAIL_PASSWORD=xxxx xxxx xxxx xxxx  # Gmail App Password
MAIL_ENCRYPTION=tls

# Remitente
MAIL_FROM_ADDRESS=support@105pos.com
MAIL_FROM_NAME="105 POS Support"

# URLs del sistema (para links en emails)
APP_URL=https://105pos.com
APP_FRONTEND_URL=https://105pos.com

# Configuración de Password Reset
PASSWORD_RESET_TOKEN_EXPIRY=60  # minutos (1 hora)
PASSWORD_RESET_MAX_ATTEMPTS=5   # máximo intentos por hora
```

### 2. Migrar Base de Datos

```bash
cd backend
php artisan migrate
```

Esto creará la tabla `password_reset_tokens` con:
- `email`: Email del usuario
- `token`: Token único de recuperación
- `expires_at`: Fecha de expiración
- `used`: Si el token ya fue utilizado
- `ip_address`: IP que solicitó el reset
- `user_agent`: Navegador del usuario

---

## 📧 Uso del Sistema de Emails

### Email de Bienvenida

```php
use App\Services\EmailService;

// Al registrar un nuevo tenant
EmailService::sendWelcomeEmail([
    'email' => 'usuario@example.com',
    'name' => 'Juan Pérez',
    'business_name' => 'Mi Empresa SAS',
    'subdomain' => 'miempresa',
    'password' => 'temporal123',  // opcional
    'plan' => 'professional'
]);
```

### Recuperación de Contraseña

```php
// Solicitar reset
EmailService::sendPasswordResetEmail([
    'email' => 'usuario@example.com',
    'name' => 'Juan Pérez',
    'token' => 'abc123def456',
    'expires_at' => now()->addHour()
]);
```

### Confirmación de Cambio

```php
// Después de cambiar contraseña
EmailService::sendPasswordChangedEmail([
    'email' => 'usuario@example.com',
    'name' => 'Juan Pérez',
    'changed_at' => now()->format('Y-m-d H:i:s')
]);
```

---

## 🔐 Sistema de Recuperación de Contraseña

### Flujo Completo:

1. **Usuario olvida contraseña**
   - Hace clic en "¿Olvidaste tu contraseña?"
   - Ingresa su email

2. **Sistema genera token**
   - Crea token único seguro (64 caracteres)
   - Guarda en DB con expiración de 1 hora
   - Envía email con link de reset

3. **Usuario hace clic en el link**
   - Valida que el token exista
   - Valida que no haya expirado
   - Valida que no haya sido usado
   - Muestra formulario para nueva contraseña

4. **Usuario ingresa nueva contraseña**
   - Valida que cumpla requisitos (min 8 caracteres)
   - Cambia la contraseña
   - Marca el token como usado
   - Envía email de confirmación

---

## 🧪 Testing

### Probar envío de email:

```bash
cd backend
php artisan tinker

# Test básico
>>> use App\Services\EmailService;
>>> EmailService::sendWelcomeEmail([
    'email' => 'tu_email@gmail.com',
    'name' => 'Test User',
    'business_name' => 'Test Business',
    'subdomain' => 'test',
    'plan' => 'free'
]);
```

### Verificar logs:

```bash
tail -f backend/storage/logs/laravel.log
```

---

## 🚨 Troubleshooting

### ❌ Error: "Failed to authenticate"
**Solución**: Verifica que el App Password sea correcto (sin espacios)

### ❌ Error: "Connection timeout"
**Solución**: 
- Verifica que el puerto 587 esté abierto
- Usa `MAIL_PORT=465` con `MAIL_ENCRYPTION=ssl`

### ❌ Emails no llegan
**Solución**:
- Revisa carpeta de SPAM
- Verifica límite diario de Gmail (500 emails)
- Revisa logs: `storage/logs/laravel.log`

### ❌ Error: "Domain not verified" (SendGrid)
**Solución**: Verifica el dominio en SendGrid agregando DNS records

---

## 📊 Límites por Proveedor

| Proveedor | Emails Gratis/Día | Límite Total | Costo Extra |
|-----------|-------------------|--------------|-------------|
| Gmail | 500 | 500/día | N/A |
| SendGrid | 100 | 100/día | $19.95/mes (40k) |
| Mailgun | 166 | 5,000/mes | $35/mes (50k) |
| Cloudflare Routing | ∞ | Sin límite | GRATIS |

---

## 🎯 Recomendación Final

Para **105 POS** recomiendo:

1. **Fase 1 (Ahora)**: 
   - Usar **Gmail SMTP** (rápido, funciona ya)
   - Configurar en 5 minutos
   - Suficiente para beta/MVP

2. **Fase 2 (Después)**: 
   - Agregar **Cloudflare Email Routing**
   - Dominio personalizado `support@105pos.com`
   - Emails se ven más profesionales

3. **Fase 3 (Escalado)**:
   - Migrar a **SendGrid/Mailgun**
   - Analytics y tracking
   - Más de 500 usuarios activos/día

---

## 📞 Soporte

- **Email técnico**: dev@105pos.com
- **Soporte usuarios**: support@105pos.com
- **Documentación**: https://docs.105pos.com/emails

---

**Última actualización**: 13 de diciembre de 2025
**Versión del sistema**: 1.0.0
