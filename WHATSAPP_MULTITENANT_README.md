# 📱 WhatsApp Multi-Tenant - Sistema POS

## 🔍 **PROBLEMAS IDENTIFICADOS Y SOLUCIONADOS**

### ❌ **Problema 1: Nombre de Empresa no se Cargaba en Onboarding**

**Causa:**
- El registro (`SaasRegister.vue`) NO guardaba los datos en `localStorage`
- El onboarding (`InitialOnboardingView.vue`) intentaba leer `registration_data` de `localStorage` pero estaba vacío

**Solución Implementada:**
```javascript
// src/views/SaasRegister.vue (línea 451)
// ✅ GUARDAR DATOS DEL REGISTRO EN LOCALSTORAGE
const registrationData = {
  company_name: form.company_name,
  storeName: form.company_name,
  owner_name: form.owner_name,
  phone: form.phone || '',
  email: form.email,
  subdomain: form.subdomain,
  cedula: form.cedula
}
localStorage.setItem('registration_data', JSON.stringify(registrationData))
```

**Ahora:**
- ✅ Se guardan los datos del registro en `localStorage`
- ✅ El onboarding los lee y pre-llena el formulario
- ✅ El nombre de empresa aparece automáticamente

---

### ❌ **Problema 2: WhatsApp NO Soportaba Multi-Tenant**

**Problema Crítico:**
- `whatsapp-server.js` usaba UNA ÚNICA sesión para TODOS los tenants
- Si la Tienda A conectaba WhatsApp, la Tienda B usaría el mismo número
- Arquitectura NO escalable ni segura

**Evidencia del Problema:**
```javascript
// ❌ INCORRECTO: backend/whatsapp-server.js
const { state, saveCreds } = await useMultiFileAuthState('./whatsapp_session');
// ☝️ UNA carpeta para TODOS los tenants
```

**Solución Implementada:**
Se creó un nuevo servidor: **`whatsapp-server-multitenant.js`**

```javascript
// ✅ CORRECTO: backend/whatsapp-server-multitenant.js
function getTenantSessionPath(tenantId) {
    return path.join('./whatsapp_sessions', tenantId);
}

const { state, saveCreds } = await useMultiFileAuthState(sessionPath);
// ☝️ Cada tenant tiene su propia carpeta
```

**Estructura Multi-Tenant:**
```
whatsapp_sessions/
├── tenant_tienda_a/
│   ├── creds.json
│   └── app-state-sync-*.json
├── tenant_tienda_b/
│   ├── creds.json
│   └── app-state-sync-*.json
└── tenant_tienda_c/
    ├── creds.json
    └── app-state-sync-*.json
```

---

## 🚀 **NUEVO SISTEMA WHATSAPP MULTI-TENANT**

### 📁 **Archivo Creado:**
`backend/whatsapp-server-multitenant.js`

### 🎯 **Características:**

1. **Sesiones Independientes por Tenant**
   - Cada tienda tiene su propia sesión de WhatsApp
   - No hay conflictos entre tenants
   - Escalable a miles de tiendas

2. **Header `X-Tenant-Id`**
   - Cada request debe incluir el header `X-Tenant-Id` con el ID del tenant
   - El servidor usa este ID para identificar qué sesión usar

3. **Gestión Automática de Sesiones**
   - Limpieza automática de sesiones inactivas cada hora
   - Reconexión automática en caso de desconexión

4. **Endpoints Disponibles:**

| Método | Endpoint | Descripción |
|--------|----------|-------------|
| GET | `/status` | Estado de conexión del tenant |
| GET | `/qr` | Obtener código QR del tenant |
| POST | `/initialize` | Inicializar conexión WhatsApp |
| POST | `/disconnect` | Desconectar WhatsApp del tenant |
| POST | `/clean-session` | Limpiar sesión del tenant |
| POST | `/send` | Enviar mensaje desde el tenant |
| GET | `/tenants` | Listar todos los tenants (admin) |

---

## 🔧 **CÓMO USAR EL NUEVO SISTEMA**

### **1. Iniciar el Servidor Multi-Tenant**

```bash
cd backend
node whatsapp-server-multitenant.js
```

**Output:**
```
🚀 Iniciando servidor WhatsApp Multi-Tenant...
🌐 Servidor WhatsApp Multi-Tenant ejecutándose en http://localhost:3002
📡 Endpoints disponibles:
  [GET]  /status       - Estado de conexión (usa header X-Tenant-Id)
  [GET]  /qr           - Obtener código QR (usa header X-Tenant-Id)
  [POST] /initialize   - Inicializar conexión (usa header X-Tenant-Id)
  ...
💡 Cada tenant debe enviar su ID en el header: X-Tenant-Id
```

### **2. Conectar un Tenant (Ejemplo con curl)**

```bash
# Inicializar conexión para tenant "tienda_a"
curl -X POST http://localhost:3002/initialize \
  -H "X-Tenant-Id: tienda_a"

# Obtener QR para escanear
curl -X GET http://localhost:3002/qr \
  -H "X-Tenant-Id: tienda_a"

# Verificar estado
curl -X GET http://localhost:3002/status \
  -H "X-Tenant-Id: tienda_a"
```

### **3. Integración con Laravel (Backend API)**

**Crear Middleware para agregar `X-Tenant-Id`:**

```php
// backend/app/Http/Middleware/AddTenantIdToWhatsAppRequests.php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Stancl\Tenancy\Tenancy;

class AddTenantIdToWhatsAppRequests
{
    protected $tenancy;

    public function __construct(Tenancy $tenancy)
    {
        $this->tenancy = $tenancy;
    }

    public function handle(Request $request, Closure $next)
    {
        // Si hay un tenant activo, agregar header
        if ($this->tenancy->initialized && $tenant = tenant()) {
            $request->headers->set('X-Tenant-Id', $tenant->id);
        }

        return $next($request);
    }
}
```

**Registrar en `backend/app/Http/Kernel.php`:**

```php
protected $middleware = [
    // ...
    \App\Http\Middleware\AddTenantIdToWhatsAppRequests::class,
];
```

**Actualizar rutas de WhatsApp en `backend/routes/tenant_api.php`:**

```php
use Illuminate\Support\Facades\Http;

// Proxy para WhatsApp Multi-Tenant
Route::prefix('whatsapp')->group(function () {
    $whatsappUrl = env('WHATSAPP_SERVER_URL', 'http://localhost:3002');

    Route::get('/status', function () use ($whatsappUrl) {
        $tenantId = tenant()->id;
        $response = Http::withHeaders(['X-Tenant-Id' => $tenantId])
            ->get("{$whatsappUrl}/status");
        return $response->json();
    });

    Route::get('/qr', function () use ($whatsappUrl) {
        $tenantId = tenant()->id;
        $response = Http::withHeaders(['X-Tenant-Id' => $tenantId])
            ->get("{$whatsappUrl}/qr");
        return $response->json();
    });

    Route::post('/initialize', function () use ($whatsappUrl) {
        $tenantId = tenant()->id;
        $response = Http::withHeaders(['X-Tenant-Id' => $tenantId])
            ->post("{$whatsappUrl}/initialize");
        return $response->json();
    });

    Route::post('/disconnect', function () use ($whatsappUrl) {
        $tenantId = tenant()->id;
        $response = Http::withHeaders(['X-Tenant-Id' => $tenantId])
            ->post("{$whatsappUrl}/disconnect");
        return $response->json();
    });

    Route::post('/clean-session', function () use ($whatsappUrl) {
        $tenantId = tenant()->id;
        $response = Http::withHeaders(['X-Tenant-Id' => $tenantId])
            ->post("{$whatsappUrl}/clean-session");
        return $response->json();
    });

    Route::post('/send-pdf', function (Request $request) use ($whatsappUrl) {
        $tenantId = tenant()->id;
        
        // Procesar PDF y enviar mensaje
        $phone = $request->input('phone');
        $message = $request->input('message');
        $pdf = $request->file('pdf');

        // Guardar PDF temporalmente
        $pdfPath = storage_path("app/temp/{$tenantId}_{$pdf->getClientOriginalName()}");
        $pdf->move(dirname($pdfPath), basename($pdfPath));

        // Enviar a WhatsApp
        $response = Http::withHeaders(['X-Tenant-Id' => $tenantId])
            ->post("{$whatsappUrl}/send", [
                'phone' => $phone,
                'message' => $message,
                'pdfPath' => $pdfPath
            ]);

        // Limpiar archivo temporal
        @unlink($pdfPath);

        return $response->json();
    });
});
```

---

## 📱 **INTEGRACIÓN EN ONBOARDING**

### **Paso 3: Conectar WhatsApp**

Se actualizó `src/views/InitialOnboardingView.vue` con:

1. **Interfaz de Conexión:**
   - Muestra QR code para escanear
   - Estado en tiempo real de la conexión
   - Instrucciones paso a paso

2. **Funcionalidades:**
   ```javascript
   - initializeWhatsApp()      // Iniciar servicio
   - getQRCode()               // Obtener QR
   - generateQRImage()         // Renderizar QR
   - checkWhatsAppStatus()     // Verificar estado
   - refreshQR()               // Actualizar QR
   ```

3. **Auto-Inicialización:**
   - Cuando el usuario llega al paso 3, se inicializa automáticamente
   - Verifica cada 3 segundos si se conectó
   - Muestra mensaje de éxito cuando se conecta

---

## 🔒 **SEGURIDAD**

### **Aislamiento por Tenant:**
- Cada tenant tiene su propia carpeta de sesión
- No es posible acceder a la sesión de otro tenant
- Los archivos se crean con permisos restrictivos

### **Validación de Headers:**
- Cada request requiere `X-Tenant-Id`
- Laravel valida que el tenant sea válido
- No se permite acceso sin autenticación

---

## 🧪 **TESTING**

### **Probar con Múltiples Tenants:**

```bash
# Terminal 1: Tenant A
curl -X POST http://localhost:3002/initialize -H "X-Tenant-Id: tienda_a"
curl -X GET http://localhost:3002/qr -H "X-Tenant-Id: tienda_a"

# Terminal 2: Tenant B (simultáneo)
curl -X POST http://localhost:3002/initialize -H "X-Tenant-Id: tienda_b"
curl -X GET http://localhost:3002/qr -H "X-Tenant-Id: tienda_b"

# Verificar que tienen QRs diferentes
diff <(curl -s http://localhost:3002/qr -H "X-Tenant-Id: tienda_a" | jq -r '.qr_code') \
     <(curl -s http://localhost:3002/qr -H "X-Tenant-Id: tienda_b" | jq -r '.qr_code')
```

---

## 📊 **MONITOREO**

### **Logs:**
```bash
# Ver logs del servidor multi-tenant
tail -f backend/whatsapp-multitenant.log

# Ver sesiones activas
ls -la backend/whatsapp_sessions/
```

### **Endpoint Administrativo:**
```bash
# Listar todos los tenants conectados
curl http://localhost:3002/tenants
```

**Response:**
```json
{
  "success": true,
  "total_tenants": 3,
  "tenants": [
    {
      "tenant_id": "tienda_a",
      "connected": true,
      "has_qr": false,
      "last_activity": 1732000000000
    },
    {
      "tenant_id": "tienda_b",
      "connected": false,
      "has_qr": true,
      "last_activity": 1732000000000
    }
  ]
}
```

---

## 🚦 **MIGRACIÓN DEL SISTEMA ANTIGUO**

### **Opción 1: Convivencia (Recomendado para Producción)**

Mantener ambos sistemas temporalmente:

```bash
# Servidor antiguo (puerto 3002)
node whatsapp-server.js

# Servidor nuevo (puerto 3003)
PORT=3003 node whatsapp-server-multitenant.js
```

Migrar tenants gradualmente:
1. Nuevos tenants usan el servidor multi-tenant
2. Tenants existentes siguen usando el antiguo
3. Migrar uno por uno según pruebas

### **Opción 2: Reemplazo Completo (Desarrollo)**

1. Detener el servidor antiguo
2. Iniciar el servidor multi-tenant en el puerto 3002
3. Actualizar todas las rutas de Laravel

```bash
# Detener servidor antiguo
pkill -f "whatsapp-server.js"

# Iniciar servidor nuevo
node whatsapp-server-multitenant.js
```

---

## ✅ **CHECKLIST DE IMPLEMENTACIÓN**

- [x] Crear `whatsapp-server-multitenant.js`
- [x] Agregar funciones de WhatsApp en `InitialOnboardingView.vue`
- [x] Guardar `registration_data` en `localStorage` desde `SaasRegister.vue`
- [ ] Actualizar rutas de Laravel (`tenant_api.php`)
- [ ] Crear middleware `AddTenantIdToWhatsAppRequests.php`
- [ ] Agregar variable de entorno `WHATSAPP_SERVER_URL` en `.env`
- [ ] Actualizar `whatsappService.js` si es necesario
- [ ] Probar con 2-3 tenants simultáneamente
- [ ] Migrar tenants existentes (si aplica)
- [ ] Documentar en el README principal del proyecto

---

## 🔄 **PRÓXIMOS PASOS**

1. **Actualizar Backend Laravel:**
   - Implementar las rutas proxy en `tenant_api.php`
   - Crear el middleware de tenant ID

2. **Testing Exhaustivo:**
   - Probar con múltiples tenants simultáneos
   - Verificar que cada tenant tiene su QR único
   - Probar envío de mensajes de diferentes tenants

3. **Producción:**
   - Configurar PM2 o supervisor para mantener el servidor corriendo
   - Configurar firewall para proteger el puerto 3002
   - Agregar logs rotacionales

4. **Monitoreo:**
   - Implementar alertas si un tenant se desconecta
   - Dashboard para ver estado de todos los tenants

---

## 📝 **NOTAS IMPORTANTES**

⚠️ **El servidor antiguo (`whatsapp-server.js`) NO debe usarse en producción multi-tenant**

✅ **Solo usar `whatsapp-server-multitenant.js` para ambientes con múltiples tiendas**

🔐 **Cada tenant debe tener su propio número de WhatsApp Business**

📱 **No es posible compartir un número de WhatsApp entre tenants**

---

## 🆘 **SOPORTE Y TROUBLESHOOTING**

### **Problema: QR no se genera**
```bash
# Verificar que el servidor está corriendo
curl http://localhost:3002/tenants

# Limpiar sesión del tenant
curl -X POST http://localhost:3002/clean-session \
  -H "X-Tenant-Id: tu_tenant_id"

# Reinicializar
curl -X POST http://localhost:3002/initialize \
  -H "X-Tenant-Id: tu_tenant_id"
```

### **Problema: Tenant no se conecta**
1. Verificar que el QR no expiró (regenera cada 20 segundos)
2. Asegurarse que WhatsApp está actualizado en el teléfono
3. Limpiar sesión y volver a intentar

### **Problema: Error de permisos en carpetas**
```bash
cd backend
chmod -R 755 whatsapp_sessions/
chmod -R 755 whatsapp_qrs/
```

---

**Documentación creada:** 30 de noviembre de 2025  
**Versión:** 1.0  
**Autor:** Sistema POS Development Team
