# 🔒 Protección de Registro Multi-Tenant

## ✅ Cambios Implementados

### 1. **Protección en Router (`src/router/index.js`)**

Agregado `beforeEnter` guard a la ruta `/register`:

```javascript
{
  path: '/register',
  name: 'Register',
  component: SaasRegister,
  beforeEnter: (to, from, next) => {
    // 🔒 PROTECCIÓN: Solo permitir /register en app central
    const hostname = window.location.hostname
    const parts = hostname.split('.')
    
    // Si NO es localhost directo, bloquear
    if (hostname !== 'localhost' && hostname !== '127.0.0.1') {
      const isSubdomain = parts.length > 2 || (parts.length === 2 && parts[0] !== 'localhost')
      
      if (isSubdomain) {
        console.warn('⚠️ Acceso a /register bloqueado desde subdominio')
        // Redirigir a login del tenant
        next('/login')
        return
      }
    }
    
    next()
  },
  meta: {
    title: 'Crear Cuenta - 105 POS',
    requiresAuth: false
  }
}
```

### 2. **Protección en Componente (`src/views/SaasRegister.vue`)**

Agregado verificación en `onMounted`:

```javascript
onMounted(() => {
  // 🔒 PROTECCIÓN: Solo permitir registro en app central (sin subdominio)
  const hostname = window.location.hostname
  const parts = hostname.split('.')
  
  // Si hostname tiene más de 2 partes (ej: tenant.localhost) o es un subdominio
  // Y NO es 'localhost' directo, redirigir a app central
  if (hostname !== 'localhost' && hostname !== '127.0.0.1') {
    const isSubdomain = parts.length > 2 || (parts.length === 2 && parts[0] !== 'localhost')
    
    if (isSubdomain) {
      console.warn('⚠️ Intento de acceder a /register desde subdominio. Redirigiendo a app central...')
      const protocol = window.location.protocol
      const port = window.location.port ? `:${window.location.port}` : ''
      // Redirigir a la app central
      window.location.href = `${protocol}//localhost${port}/register`
      return
    }
  }
  
  // ... resto del código
})
```

### 3. **Corrección de Directiva Vue**

```vue
<!-- ANTES (ERROR) -->
<span v-else">Creando cuenta...</span>

<!-- DESPUÉS (CORRECTO) -->
<span v-else>Creando cuenta...</span>
```

### 4. **Limpieza de URL con `/welcome`**

Función `goToPlanSelection` mejorada:

```javascript
const goToPlanSelection = () => {
  const registrationData = localStorage.getItem('registration_data')
  
  if (registrationData) {
    const data = JSON.parse(registrationData)
    if (data.redirect_url) {
      // Limpiar cualquier /welcome o path extra del redirect_url
      let cleanUrl = data.redirect_url.replace(/\/welcome\/?$/, '').replace(/\/$/, '')
      const targetUrl = cleanUrl + '/select-plan'
      console.log('✅ Redirigiendo a tenant:', targetUrl)
      window.location.href = targetUrl
    } else {
      // Fallback...
    }
  }
}
```

---

## 🧪 Flujo de Prueba Completo

### Escenario 1: Registro Normal desde App Central ✅

1. **Usuario navega a:** `http://localhost:3000/register`
2. **Llena formulario:** 
   - Nombre empresa: "Mi Tienda"
   - Subdominio: `mitienda`
   - Email, contraseña, etc.
3. **Backend crea tenant y devuelve:**
   ```json
   {
     "redirect_url": "http://mitienda.localhost:3000/welcome",
     "tenant_id": 123
   }
   ```
4. **Frontend guarda en localStorage:**
   ```javascript
   {
     subdomain: "mitienda",
     redirect_url: "http://mitienda.localhost:3000/welcome",
     tenant_id: 123,
     ...
   }
   ```
5. **Muestra Step 3:** Pantalla de éxito con botón "Elegir Mi Plan"
6. **Usuario hace click en "Elegir Mi Plan"**
7. **`goToPlanSelection` ejecuta:**
   - Lee `redirect_url`: `http://mitienda.localhost:3000/welcome`
   - Limpia `/welcome`: `http://mitienda.localhost:3000`
   - Agrega `/select-plan`: `http://mitienda.localhost:3000/select-plan`
8. **Redirige a:** `http://mitienda.localhost:3000/select-plan` ✅

---

### Escenario 2: Intento de Acceso Directo a `/register` desde Subdominio ❌→✅

#### **Intento A: Navegación directa**

1. **Usuario navega a:** `http://mitienda.localhost:3000/register`
2. **Router Guard detecta subdominio**
3. **Bloquea acceso y redirige a:** `http://mitienda.localhost:3000/login`

#### **Intento B: Manipulación de URL**

1. **Usuario está en:** `http://mitienda.localhost:3000/pos`
2. **Cambia URL manualmente a:** `http://mitienda.localhost:3000/register`
3. **`onMounted` en SaasRegister detecta subdominio**
4. **Redirige a:** `http://localhost:3000/register` (app central)

---

### Escenario 3: URLs Permitidas ✅

| URL | ¿Permitida? | Motivo |
|-----|-------------|--------|
| `http://localhost:3000/register` | ✅ SÍ | App central |
| `http://127.0.0.1:3000/register` | ✅ SÍ | App central (IP) |
| `http://mitienda.localhost:3000/register` | ❌ NO | Subdominio → Redirige a `/login` |
| `http://cualquiercosa.localhost:3000/register` | ❌ NO | Subdominio → Redirige a `/login` |
| `http://test123.localhost:3000/register` | ❌ NO | Subdominio → Redirige a `/login` |

---

## 🔍 Debugging

### Consola del Navegador

Cuando alguien intenta acceder a `/register` desde un subdominio, verás:

```
⚠️ Acceso a /register bloqueado desde subdominio
```

O:

```
⚠️ Intento de acceder a /register desde subdominio. Redirigiendo a app central...
```

### LocalStorage

Después de completar el registro, inspecciona `localStorage`:

```javascript
// En DevTools Console:
JSON.parse(localStorage.getItem('registration_data'))

// Debe mostrar:
{
  tenant_id: 123,
  redirect_url: "http://mitienda.localhost:3000/welcome",
  subdomain: "mitienda",
  company_name: "Mi Tienda",
  email: "...",
  ...
}
```

---

## 🐛 Problemas Conocidos y Soluciones

### ❓ "Me redirige a `/register` después de completar el formulario"

**Causa posible:**
- El backend está devolviendo `redirect_url` con `/register` en vez de `/welcome`

**Solución:**
Verificar en `backend/app/Http/Controllers/Api/TenantRegisterController.php` línea 197:

```php
// DEBE SER:
$redirectUrl = $protocol . $domainToCreate . ':3000/welcome';

// NO:
$redirectUrl = $protocol . $domainToCreate . ':3000/register';
```

---

### ❓ "Cualquier subdominio puede ver la pantalla de registro"

**Causa:**
- Protecciones no están funcionando
- Cache del navegador

**Solución:**
1. Hard refresh: `Ctrl + Shift + R` (Windows/Linux) o `Cmd + Shift + R` (Mac)
2. Verificar consola del navegador por mensajes de bloqueo
3. Verificar que el código actualizado está compilado:
   ```bash
   # Reiniciar Vite
   npm run dev
   ```

---

### ❓ "Error: Failed to resolve directive: else""

**Causa:**
- Directiva Vue malformada: `v-else"`

**Solución:**
Ya corregido en línea 325 de `SaasRegister.vue`:
```vue
<span v-else>Creando cuenta...</span>
```

---

## ✅ Validación Final

### Checklist de Pruebas

- [ ] `http://localhost:3000/register` → Muestra formulario de registro
- [ ] Completar registro → Muestra Step 3 (pantalla de éxito)
- [ ] Click "Elegir Mi Plan" → Redirige a `http://[subdomain].localhost:3000/select-plan`
- [ ] `http://tenant1.localhost:3000/register` → Redirige a `/login` o app central
- [ ] `http://cualquiercosa.localhost:3000/register` → Redirige a `/login` o app central
- [ ] Consola sin errores de directivas Vue
- [ ] LocalStorage contiene `registration_data` con `redirect_url`

---

## 📊 Arquitectura Final

```
┌─────────────────────────────────────────────────────┐
│         APP CENTRAL (localhost:3000)                │
│  - /register ✅ (único lugar para registrarse)      │
│  - /login ✅                                         │
│  - /admin ✅ (super admin)                          │
└─────────────────────────────────────────────────────┘
                        │
                        │ Registro crea tenant
                        ▼
┌─────────────────────────────────────────────────────┐
│    TENANT SUBDOMAIN (tenant.localhost:3000)         │
│  - /register ❌ → Redirige a /login                 │
│  - /select-plan ✅                                   │
│  - /login ✅                                         │
│  - /pos ✅                                           │
│  - /invoices ✅                                      │
│  - ... (todas las rutas del POS)                    │
└─────────────────────────────────────────────────────┘
```

---

## 🎯 Próximos Pasos

1. **Refrescar navegador** (Ctrl + Shift + R)
2. **Probar registro completo:**
   - Ir a `http://localhost:3000/register`
   - Registrar nuevo tenant
   - Verificar que muestra Step 3
   - Click "Elegir Mi Plan"
   - Confirmar redirección correcta
3. **Intentar acceder a `/register` desde subdominio:**
   - Navegar a `http://testxyz.localhost:3000/register`
   - Confirmar que bloquea y redirige
4. **Reportar cualquier comportamiento inesperado**
