# 🔒 Solución al Problema de Autenticación con Google OAuth

## 🐛 Problema Original

Al intentar iniciar sesión con Google OAuth, el usuario era redirigido correctamente pero la sesión no se establecía, resultando en:

```
❌ Usuario no autenticado, continuando al login
Tu sesión ha expirado
```

### Causa Raíz

El flujo tenía **dos problemas críticos**:

1. **Middleware interceptaba antes de procesar el token**: El middleware `redirectIfAuth` verificaba autenticación antes de que LoginView pudiera procesar el `google_login_token`

2. **Token temporal inválido**: El frontend generaba un token aleatorio (`google_XXX`) en lugar de usar un token Sanctum real del backend, causando que `/api/me` fallara con 401 Unauthorized

## ✅ Solución Implementada

### 1. Middleware de Autenticación (`src/middleware/auth.js`)

**Cambio**: Detectar parámetro `google_login_token` en la URL y permitir acceso a `/login` sin redirección.

```javascript
export const redirectIfAuth = (to, from, next) => {
  // 🔥 IMPORTANTE: Permitir acceso a login si viene con token de Google OAuth
  const hasGoogleToken = to.query.google_login_token || to.query.google_token
  
  if (hasGoogleToken) {
    console.log('🔑 Token de Google detectado, permitiendo acceso a login para procesar autenticación')
    next()
    return
  }
  
  // ... resto del código
}
```

**Por qué funciona**: Evita que el middleware redirija al usuario antes de que LoginView pueda procesar el token de Google.

---

### 2. Backend - Google Auth Controller (PHP)

**Cambio**: Generar token Sanctum real en lugar de solo devolver datos del usuario.

```php
public function getGoogleLoginSession(Request $request)
{
    // ... validación de token temporal
    
    $tenant = \App\Models\Tenant::find($sessionData['tenant_id']);
    
    // 🔥 Ejecutar en contexto del tenant para generar token Sanctum
    $tenant->run(function () use ($sessionData, &$authToken, &$userData) {
        $user = \App\Models\User::with('role')->find($sessionData['user_id']);
        
        // 🔥 Generar token Sanctum REAL
        $authToken = $user->createToken('google-auth-token')->plainTextToken;
        
        $userData = [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role ? [
                'id' => $user->role->id,
                'name' => $user->role->name
            ] : null,
            'tenant_id' => $sessionData['tenant_id']
        ];
    });

    return response()->json([
        'success' => true,
        'data' => [
            'token' => $authToken,  // ✅ Token Sanctum real
            'user' => $userData
        ]
    ]);
}
```

**Por qué funciona**: 
- Genera un token Sanctum válido que el backend reconoce
- Ejecuta en el contexto correcto del tenant
- Incluye toda la información del usuario necesaria

---

### 3. Frontend - LoginView Component

**Cambio**: Usar token Sanctum real del backend en lugar de generar uno temporal.

```javascript
const response = await cleanAxios.get(`/api/auth/google/login-session?token=${googleLoginToken}`)

if (response.data.success && response.data.data) {
  const { token, user } = response.data.data
  
  // 🔥 Guardar token Sanctum REAL (no temporal)
  localStorage.setItem('authToken', token)
  localStorage.setItem('user', JSON.stringify(user))
  
  // Configurar en axios global
  axios.defaults.headers.common['Authorization'] = `Bearer ${token}`
  
  // Redirigir a /pos
  window.location.href = '/pos'
}
```

**Por qué funciona**: 
- Usa el token Sanctum real que el backend reconoce
- Persiste correctamente la sesión
- Permite que `/api/me` funcione correctamente

---

### 4. Configuración Backend (.env)

**Cambio**: Asegurar que `GOOGLE_REDIRECT_URI` sea exacto al configurado en Google Cloud Console.

```env
# ❌ ANTES (dinámico, causaba problemas)
GOOGLE_REDIRECT_URI="${APP_URL}/api/auth/google/callback"

# ✅ AHORA (exacto, funciona)
GOOGLE_REDIRECT_URI=http://localhost:3000/api/auth/google/callback
```

**Importante**: Este valor debe coincidir EXACTAMENTE con el URI configurado en Google Cloud Console.

---

## 🔄 Flujo Corregido

### Paso a Paso

1. **Usuario hace clic** en "Continuar con Google" en `/register` o `/login`

2. **Frontend solicita URL** de autorización al backend:
   ```
   POST /api/auth/google/redirect
   ```

3. **Backend genera URL** de Google OAuth y la retorna

4. **Usuario es redirigido** a Google para autenticarse

5. **Google redirige de vuelta** al backend:
   ```
   GET http://localhost:3000/api/auth/google/callback?code=XXX
   ```

6. **Backend procesa el callback**:
   - Intercambia código por token de acceso de Google
   - Obtiene datos del usuario de Google
   - Busca si el usuario ya existe en algún tenant
   - Si existe: genera token temporal de sesión (`google_login_XXX`)
   - Si no existe: redirige al registro con datos de Google

7. **Backend redirige al frontend** del tenant con token temporal:
   ```
   http://subdominio.localhost:3000/login?google_login_token=XXX
   ```

8. **Middleware permite acceso** a `/login` al detectar `google_login_token`

9. **LoginView procesa el token**:
   - Llama a `/api/auth/google/login-session?token=XXX`
   - Backend encuentra sesión en caché
   - Backend genera token Sanctum REAL en contexto del tenant
   - Retorna token Sanctum + datos de usuario

10. **Frontend guarda la sesión**:
    - `localStorage.setItem('authToken', token)` ← Token Sanctum real
    - `localStorage.setItem('user', JSON.stringify(user))`
    - Configura axios con el token

11. **Redirección exitosa** a `/pos`

12. **Subsecuentes requests funcionan** porque tienen token Sanctum válido

---

## 🔍 Verificación

### En Consola del Navegador (DevTools)

Deberías ver:
```
🔑 Token de Google detectado en URL, procesando autenticación...
📡 Obteniendo datos de sesión de Google desde: http://...
✅ Datos de usuario recibidos: {...}
✅ Token Sanctum recibido: 1|XXXX...
✅ Token Sanctum guardado en localStorage y axios configurado
✅ Usuario autenticado con Google: Nombre (email@example.com)
🔄 Redirigiendo a /pos
```

### En Laravel Logs (`backend/storage/logs/laravel.log`)

Deberías ver:
```
✅ Usuario existente encontrado - Iniciando sesión automática
✅ Token Sanctum generado para usuario de Google
```

### En localStorage

Verifica que existan:
```javascript
localStorage.getItem('authToken')  // Token Sanctum real (1|XXXX...)
localStorage.getItem('user')        // JSON con datos del usuario
```

---

## 🚨 Troubleshooting

### Si sigue fallando:

1. **Verificar Google Cloud Console**:
   - URI de redirección debe ser: `http://localhost:3000/api/auth/google/callback`
   - Credenciales correctas en `.env`

2. **Limpiar caché**:
   ```bash
   # Backend
   cd backend
   php artisan cache:clear
   php artisan config:clear
   
   # Frontend
   npm run build
   ```

3. **Verificar que el usuario existe** en la base de datos del tenant

4. **Revisar logs** de Laravel para errores detallados

5. **Verificar que el tenant tiene dominios** correctamente configurados

---

## 📝 Archivos Modificados

1. ✅ `src/middleware/auth.js` - Permite acceso con google_login_token
2. ✅ `backend/app/Http/Controllers/GoogleAuthController.php` - Genera token Sanctum real
3. ✅ `src/components/LoginView.vue` - Usa token Sanctum del backend
4. ✅ `backend/.env` - GOOGLE_REDIRECT_URI exacto

---

## ✨ Resultado

✅ Login con Google OAuth funciona correctamente  
✅ Sesión persiste después del login  
✅ Token Sanctum válido para todas las API calls  
✅ Usuario puede navegar a `/pos` sin problemas  
✅ Soporte multi-tenant funcional  
