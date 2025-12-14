# 🔐 Guía de Implementación: Google OAuth 2.0 en 105 POS

## ✅ Estado de Implementación

### Backend Completado (100%)
- ✅ Controlador `GoogleAuthController.php` creado (233 líneas)
- ✅ Rutas OAuth agregadas a `api.php`:
  - `POST /api/auth/google/redirect` - Inicia autenticación
  - `GET /api/auth/google/callback` - Callback de Google
  - `POST /api/auth/google/login` - Login para usuarios existentes
- ✅ Variables de entorno configuradas en `.env`:
  ```env
  GOOGLE_CLIENT_ID=
  GOOGLE_CLIENT_SECRET=
  GOOGLE_REDIRECT_URI="${APP_URL}/api/auth/google/callback"
  ```

### Frontend Completado (100%)
- ✅ Servicio `googleAuthService.js` creado
- ✅ Botón "Continuar con Google" agregado a `SaasRegister.vue`
- ✅ Estado de carga (`isGoogleLoading`) implementado
- ✅ Función `signInWithGoogle()` conectada al botón

---

## 📋 Pasos Finales (ACCIÓN REQUERIDA)

### 1. Obtener Credenciales de Google Cloud Console

**Ve a:** https://console.cloud.google.com/

#### Paso 1.1: Crear Proyecto
1. Haz clic en el selector de proyectos (arriba a la izquierda)
2. Clic en **"Nuevo Proyecto"**
3. Nombre del proyecto: **"105 POS Sistema"**
4. Clic en **"Crear"**

#### Paso 1.2: Habilitar Google+ API
1. En el menú lateral: **APIs y servicios** → **Biblioteca**
2. Busca: **"Google+ API"** o **"People API"**
3. Clic en **"Habilitar"**

#### Paso 1.3: Crear Credenciales OAuth 2.0
1. En el menú lateral: **APIs y servicios** → **Credenciales**
2. Clic en **"+ Crear credenciales"** → **"ID de cliente de OAuth"**
3. Selecciona tipo: **"Aplicación web"**
4. Nombre: **"105 POS OAuth Client"**

5. **URIs de redireccionamiento autorizados** (agregar AMBOS):
   ```
   http://localhost:3000/api/auth/google/callback
   https://105pos.pro/api/auth/google/callback
   ```
   
6. Clic en **"Crear"**

#### Paso 1.4: Copiar Credenciales
Verás un modal con:
- **ID de cliente**: `123456789-abcdefg.apps.googleusercontent.com`
- **Secreto de cliente**: `GOCSPX-xyz123abc456`

**¡Copia ambos valores!**

---

### 2. Configurar Variables de Entorno

Abre: `backend/.env` y actualiza:

```env
# ==================== GOOGLE OAUTH CONFIGURATION ====================
# Obtén estas credenciales en: https://console.cloud.google.com/
GOOGLE_CLIENT_ID=TU_CLIENT_ID_AQUI.apps.googleusercontent.com
GOOGLE_CLIENT_SECRET=GOCSPX-TU_CLIENT_SECRET_AQUI
GOOGLE_REDIRECT_URI="${APP_URL}/api/auth/google/callback"
```

**Ejemplo:**
```env
GOOGLE_CLIENT_ID=123456789-abcdefg123hij456klm789.apps.googleusercontent.com
GOOGLE_CLIENT_SECRET=GOCSPX-xyz123abc456def789ghi012
GOOGLE_REDIRECT_URI="${APP_URL}/api/auth/google/callback"
```

⚠️ **IMPORTANTE**: 
- NO compartas el `GOOGLE_CLIENT_SECRET` públicamente
- NO lo subas a GitHub (ya está en `.gitignore`)
- Usa variables de entorno en producción

---

### 3. Configurar Pantalla de Consentimiento (OAuth Consent Screen)

Google requiere configurar la pantalla que verán los usuarios:

1. En **APIs y servicios** → **Pantalla de consentimiento de OAuth**
2. Selecciona: **"Externo"** (para permitir cualquier cuenta de Google)
3. Completa información requerida:
   - **Nombre de la app**: `105 POS`
   - **Correo de soporte**: tu_email@empresa.com
   - **Logo de la app**: (opcional, puedes subir logo de 105 POS)
   - **Dominio autorizado**: `105pos.pro`
   - **Correo de contacto**: tu_email@empresa.com

4. **Ámbitos (Scopes)**: Ya están configurados en el código
   - `openid`
   - `email`
   - `profile`

5. **Usuarios de prueba** (mientras está en modo Testing):
   - Agrega tu correo de Gmail para hacer pruebas
   - Puedes agregar hasta 100 usuarios de prueba

6. Guarda y continúa hasta completar el wizard

---

## 🔄 Flujo de Autenticación OAuth 2.0

```
┌─────────────────────────────────────────────────────────────────┐
│ 1. Usuario hace clic en "Continuar con Google"                 │
│    (SaasRegister.vue → signInWithGoogle())                     │
└───────────────────────────┬─────────────────────────────────────┘
                            │
                            ▼
┌─────────────────────────────────────────────────────────────────┐
│ 2. Frontend llama a: POST /api/auth/google/redirect            │
│    Backend responde con Google OAuth URL                       │
└───────────────────────────┬─────────────────────────────────────┘
                            │
                            ▼
┌─────────────────────────────────────────────────────────────────┐
│ 3. Navegador redirige a Google                                 │
│    https://accounts.google.com/o/oauth2/v2/auth?...            │
└───────────────────────────┬─────────────────────────────────────┘
                            │
                            ▼
┌─────────────────────────────────────────────────────────────────┐
│ 4. Usuario autoriza en Google (pantalla de consentimiento)     │
└───────────────────────────┬─────────────────────────────────────┘
                            │
                            ▼
┌─────────────────────────────────────────────────────────────────┐
│ 5. Google redirige a: GET /api/auth/google/callback?code=XXX   │
│    Backend recibe el código de autorización                    │
└───────────────────────────┬─────────────────────────────────────┘
                            │
                            ▼
┌─────────────────────────────────────────────────────────────────┐
│ 6. Backend intercambia código por access_token                 │
│    POST https://oauth2.googleapis.com/token                    │
└───────────────────────────┬─────────────────────────────────────┘
                            │
                            ▼
┌─────────────────────────────────────────────────────────────────┐
│ 7. Backend obtiene info del usuario de Google                  │
│    GET https://www.googleapis.com/oauth2/v2/userinfo           │
│    Respuesta: { email, name, picture, google_id }              │
└───────────────────────────┬─────────────────────────────────────┘
                            │
                            ▼
┌─────────────────────────────────────────────────────────────────┐
│ 8. Backend verifica si el usuario ya existe                    │
│    - SI EXISTE: Login automático                               │
│    - NO EXISTE: Crear tenant + usuario                         │
└───────────────────────────┬─────────────────────────────────────┘
                            │
                            ▼
┌─────────────────────────────────────────────────────────────────┐
│ 9. Backend crea:                                                │
│    - Tenant con subdomain auto-generado (slug del nombre)      │
│    - Admin user con google_id                                  │
│    - Warehouse por defecto                                     │
│    - Plan trial_express (7 días)                               │
└───────────────────────────┬─────────────────────────────────────┘
                            │
                            ▼
┌─────────────────────────────────────────────────────────────────┐
│ 10. Backend redirige a: /select-plan?tenant_id=X&...           │
│     Usuario continúa con selección de plan                     │
└─────────────────────────────────────────────────────────────────┘
```

---

## 🧪 Cómo Probar el Flujo Completo

### Prueba Local (http://localhost:3000)

1. **Inicia el servidor backend:**
   ```bash
   cd backend
   php artisan serve
   # Corriendo en: http://127.0.0.1:8000
   ```

2. **Inicia el servidor frontend:**
   ```bash
   npm run dev
   # Corriendo en: http://localhost:3000
   ```

3. **Prueba el flujo:**
   - Ve a: http://localhost:3000/register
   - Haz clic en **"Continuar con Google"**
   - Deberías ver pantalla de Google pidiendo autorización
   - Después de autorizar, verás:
     - Console log: `✅ Usuario autenticado con Google`
     - Redirección a `/select-plan?tenant_id=...`

4. **Verifica en la base de datos:**
   ```sql
   -- Verificar tenant creado
   SELECT * FROM tenants ORDER BY id DESC LIMIT 1;
   
   -- Verificar usuario con google_id
   SELECT id, name, email, google_id, tenant_id 
   FROM users 
   WHERE google_id IS NOT NULL 
   ORDER BY id DESC LIMIT 1;
   ```

### Prueba en Producción (https://105pos.pro)

1. **Sube cambios al servidor:**
   ```bash
   # Compila frontend
   npm run build
   
   # Sube archivos (vía FTP, rsync, git pull, etc.)
   # Asegúrate de actualizar backend/.env con las credenciales reales
   ```

2. **Prueba igual que en local pero desde:**
   - https://105pos.pro/register

---

## 🔍 Debugging y Solución de Problemas

### Error: "redirect_uri_mismatch"
**Causa**: La URI de redirección no coincide con las configuradas en Google Cloud Console.

**Solución**:
1. Ve a Google Cloud Console → Credenciales
2. Edita el OAuth Client
3. Verifica que las URIs autorizadas sean EXACTAS:
   ```
   http://localhost:3000/api/auth/google/callback
   https://105pos.pro/api/auth/google/callback
   ```
4. ⚠️ NO agregues slash final: `/callback/` ❌
5. Guarda cambios (pueden tardar unos minutos en propagarse)

### Error: "invalid_client"
**Causa**: `GOOGLE_CLIENT_ID` o `GOOGLE_CLIENT_SECRET` incorrectos.

**Solución**:
1. Verifica que copiaste correctamente las credenciales
2. NO debe haber espacios ni saltos de línea
3. Reinicia el servidor Laravel después de cambiar `.env`

### Error: "Access blocked: This app's request is invalid"
**Causa**: Pantalla de consentimiento no configurada o app no publicada.

**Solución**:
1. Ve a **Pantalla de consentimiento de OAuth**
2. Completa todos los campos requeridos
3. Si está en modo "Testing", agrega tu email como usuario de prueba
4. Para uso público, solicita verificación de Google (puede tardar semanas)

### El callback nunca se ejecuta
**Causa**: `GOOGLE_REDIRECT_URI` no apunta al backend.

**Solución**:
1. Verifica `.env`:
   ```env
   APP_URL=http://localhost:3000  # En desarrollo
   GOOGLE_REDIRECT_URI="${APP_URL}/api/auth/google/callback"
   ```
2. En producción:
   ```env
   APP_URL=https://105pos.pro
   ```

### Usuario creado pero sin google_id
**Causa**: Bug en el código del controlador.

**Solución**:
1. Revisa `GoogleAuthController.php` línea 117-127
2. Verifica que `$googleUser['id']` se guarde correctamente:
   ```php
   'google_id' => $googleUser['id']
   ```

---

## 📊 Datos de Prueba

### Cuenta de Google de Prueba
Usa tu propia cuenta de Gmail para probar. Google permite hasta 100 usuarios de prueba mientras la app está en modo "Testing".

### Ejemplo de Respuesta de Google API
```json
{
  "id": "1234567890",
  "email": "usuario@gmail.com",
  "verified_email": true,
  "name": "Juan Pérez",
  "given_name": "Juan",
  "family_name": "Pérez",
  "picture": "https://lh3.googleusercontent.com/a/...",
  "locale": "es"
}
```

---

## 🎯 Próximos Pasos Opcionales

### 1. Agregar Google Sign In al Login
Permite que usuarios existentes inicien sesión con Google en `/login`.

**Archivo**: `src/views/LoginView.vue`
```vue
<button @click="loginWithGoogle">
  Iniciar Sesión con Google
</button>
```

### 2. Mostrar Foto de Perfil
Guarda `picture` URL de Google y muéstrala en el perfil del usuario.

### 3. Sincronizar Calendario/Contactos
Solicita scopes adicionales:
```php
'scope' => 'openid email profile https://www.googleapis.com/auth/calendar'
```

### 4. Publicar App para Producción
1. Completa la verificación de Google (proceso largo)
2. Cambia de modo "Testing" a "Production"
3. Solicita revisión de seguridad (si usas scopes sensibles)

---

## 🔐 Seguridad y Mejores Prácticas

✅ **Implementado:**
- Authorization Code Flow (más seguro que Implicit Flow)
- Client Secret guardado server-side (nunca expuesto al frontend)
- State parameter para prevenir CSRF
- DB transactions para operaciones atómicas
- google_id almacenado para diferenciar usuarios OAuth

⚠️ **Recomendaciones:**
- Usa HTTPS en producción (ya implementado)
- Rota credenciales OAuth periódicamente
- Monitorea intentos de acceso sospechosos
- Implementa rate limiting en endpoints OAuth
- Agrega logs de auditoría para OAuth logins

---

## 📞 Soporte

Si encuentras problemas:

1. **Revisa logs del backend:**
   ```bash
   tail -f backend/storage/logs/laravel.log
   ```

2. **Revisa console del navegador:**
   - F12 → Console
   - Busca errores de CORS, network, etc.

3. **Verifica configuración:**
   ```bash
   # Backend
   cat backend/.env | grep GOOGLE
   
   # Google Cloud Console
   # Credenciales → OAuth 2.0 Client IDs
   ```

---

## ✅ Checklist Final

Antes de marcar como completado:

- [ ] Credenciales de Google Cloud Console obtenidas
- [ ] Variables de entorno actualizadas en `backend/.env`
- [ ] Pantalla de consentimiento configurada
- [ ] URIs de redirección autorizadas agregadas
- [ ] Prueba local exitosa (http://localhost:3000/register)
- [ ] Usuario creado en base de datos con `google_id`
- [ ] Redirección a `/select-plan` funciona
- [ ] Prueba en producción exitosa (https://105pos.pro/register)
- [ ] Documentación revisada

---

**¡Implementación completada al 100%!** 🎉

Solo falta obtener las credenciales de Google Cloud Console y probar el flujo completo.
