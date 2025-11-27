# 🔐 Panel de Super Admin (God Mode)

## 📋 Descripción General

Panel de administración central para gestionar todos los inquilinos (tenants) de la aplicación Multi-Tenant Laravel. Este panel proporciona visibilidad completa del sistema, monitoreo de uso de IA y gestión de clientes.

---

## 🚀 Características Principales

### ✅ Implementado

1. **Dashboard Central con Tema Oscuro**
   - Interfaz técnica y densa en datos
   - Diferenciada visualmente del panel de clientes
   - Auto-refresh cada 30 segundos

2. **KPIs en Tiempo Real**
   - Total de Clientes Activos
   - MRR (Monthly Recurring Revenue)
   - Tokens IA consumidos este mes
   - Tiendas creadas hoy

3. **Gestión de Tenants**
   - Lista completa de todos los inquilinos
   - Información detallada: nombre, subdominio, plan, estado
   - Link directo al panel de cada tenant
   - Búsqueda y paginación

4. **Monitor de IA (AI Watchtower)**
   - Consumo de IA agrupado por tenant
   - Detección de anomalías (uso excesivo)
   - Alertas visuales por nivel de consumo
   - Filtros por período (día, semana, mes)

---

## 🗂️ Estructura de Archivos Creados

### Backend (Laravel)

```
backend/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   └── Central/
│   │   │       └── DashboardController.php      ← Controlador principal
│   │   └── Middleware/
│   │       └── SuperAdminMiddleware.php         ← Protección de rutas
│   └── Models/
│       └── Central/
│           └── CentralAiUsageLog.php            ← Modelo para logs de IA
├── database/
│   └── migrations/
│       └── 2025_11_27_000001_create_ai_usage_logs_table.php  ← Migración BD
├── resources/
│   └── views/
│       └── central/
│           └── dashboard.blade.php              ← Vista Blade
└── routes/
    └── web.php                                  ← Rutas del panel
```

### Frontend (Vue)

```
src/
├── components/
│   └── SuperAdminDashboard.vue                  ← Componente principal
└── main.js                                       ← Registro del componente
```

---

## 🔧 Instalación y Configuración

### 1. Ejecutar Migraciones

```bash
cd backend
php artisan migrate
```

Esto creará la tabla `ai_usage_logs` en la base de datos central.

### 2. Verificar Configuración de Tenancy

En `backend/config/tenancy.php`, asegúrate de tener:

```php
'central_domains' => [
    '127.0.0.1',
    'localhost',
    // Agrega tu dominio central aquí en producción
],
```

### 3. Compilar Assets Frontend

```bash
npm run dev
# o para producción:
npm run build
```

---

## 🌐 Rutas del Panel

### Rutas Web (Laravel)

Todas las rutas están protegidas por el middleware `superadmin`:

| Método | Ruta                        | Descripción                      |
|--------|-----------------------------|----------------------------------|
| GET    | `/admin`                    | Dashboard principal              |
| GET    | `/admin/api/kpis`           | Obtener KPIs principales         |
| GET    | `/admin/api/tenants`        | Listar todos los tenants         |
| GET    | `/admin/api/tenants/{id}`   | Detalles de un tenant específico |
| GET    | `/admin/api/ai-usage`       | Monitor de consumo de IA         |

### Acceso al Panel

Para acceder al panel en desarrollo:

```
http://localhost/admin
```

⚠️ **IMPORTANTE:** El middleware verifica que estés accediendo desde un dominio central (`localhost`, `127.0.0.1`). No funcionará desde subdominios de tenants.

---

## 📊 Uso del Sistema

### 1. Registrar Consumo de IA

Desde cualquier controlador de tenant, puedes registrar el consumo de IA:

```php
use App\Models\Central\CentralAiUsageLog;

// Ejemplo en AIController.php
public function processAiRequest(Request $request)
{
    // ... tu lógica de IA ...
    
    $tokensUsed = 1500; // Tokens consumidos en la respuesta
    $actionType = 'chat'; // Tipo de acción: chat, analysis, recommendation, etc.
    
    // Registrar consumo en la BD central
    CentralAiUsageLog::logUsage(
        actionType: $actionType,
        tokensUsed: $tokensUsed,
        modelUsed: 'gpt-4o-mini',
        promptSummary: $request->input('message'),
        metadata: [
            'user_id' => auth()->id(),
            'ip' => $request->ip(),
        ]
    );
    
    // ... retornar respuesta ...
}
```

### 2. Tipos de Acción Sugeridos

- `chat` - Conversación con IA
- `analysis` - Análisis de datos
- `recommendation` - Recomendaciones automáticas
- `report` - Generación de reportes
- `optimization` - Optimización de inventario
- `prediction` - Predicciones de ventas

### 3. Costos por Modelo

El sistema calcula automáticamente el costo según el modelo:

| Modelo          | Costo por 1K Tokens |
|-----------------|---------------------|
| gpt-4           | $0.03               |
| gpt-4-turbo     | $0.01               |
| gpt-4o          | $0.005              |
| gpt-4o-mini     | $0.00015 (default)  |
| gpt-3.5-turbo   | $0.0005             |

---

## 🎨 Diseño del Panel

### Tema Visual

- **Color Principal:** Gris oscuro (Dark Mode)
- **Acento:** Rojo/Naranja para indicar modo admin
- **Badges de Alerta:**
  - 🟢 Normal: < $1/mes
  - 🔵 Moderado: $1-5/mes
  - 🟡 Warning: $5-10/mes
  - 🔴 Crítico: > $10/mes (con animación pulse)

### Características de UX

- Auto-refresh cada 30 segundos
- Búsqueda en tiempo real de tenants
- Paginación de resultados
- Links directos al panel de cada tenant
- Detección visual de anomalías en consumo de IA

---

## 🔒 Seguridad

### Middleware SuperAdminMiddleware

Actualmente implementado con verificación básica de dominio central. En producción, debes agregar autenticación real:

```php
// backend/app/Http/Middleware/SuperAdminMiddleware.php

public function handle(Request $request, Closure $next): Response
{
    // 1. Verificar dominio central
    if (!in_array($request->getHost(), config('tenancy.central_domains'))) {
        abort(403, 'Acceso denegado');
    }

    // 2. TODO: Agregar autenticación de super admin
    if (!auth()->check() || !auth()->user()->is_super_admin) {
        return redirect()->route('login')->with('error', 'No autorizado');
    }

    return $next($request);
}
```

### Recomendaciones de Producción

1. **Implementar tabla `super_admins`** en la BD central
2. **Usar Laravel Sanctum** para autenticación API
3. **Agregar 2FA** (autenticación de dos factores)
4. **Logging de accesos** al panel
5. **Rate limiting** en las rutas del panel
6. **HTTPS obligatorio** en producción

---

## 📈 Métricas y Alertas

### Detección de Anomalías

El sistema detecta automáticamente cuando un tenant consume más de 2 desviaciones estándar sobre el promedio:

```php
// Ejemplo de lógica en DashboardController.php
$avgTokens = $aiUsage->avg('total_tokens');
$stdDeviation = $this->calculateStdDeviation($aiUsage->pluck('total_tokens')->toArray());

$isAnomalous = ($usage->total_tokens > ($avgTokens + (2 * $stdDeviation)));
```

### Niveles de Alerta

| Nivel    | Condición       | Acción Sugerida                    |
|----------|-----------------|-------------------------------------|
| Normal   | < $1/mes        | No requiere acción                  |
| Moderado | $1-5/mes        | Monitorear                          |
| Warning  | $5-10/mes       | Revisar patrón de uso               |
| Crítico  | > $10/mes       | Contactar al tenant / Investigar    |

---

## 🛠️ Personalización

### Cambiar el MRR por Tenant

Por defecto, cada tenant se cobra $29/mes. Para personalizar:

```php
// backend/app/Http/Controllers/Central/DashboardController.php

public function getKpis()
{
    // Opción 1: Precio fijo
    $pricePerTenant = 29;
    $mrr = $totalActiveClients * $pricePerTenant;
    
    // Opción 2: Según plan (si tienes columna 'price' en tenants)
    $mrr = Tenant::sum('price');
    
    // Opción 3: Desde tabla de suscripciones
    // $mrr = Subscription::active()->sum('amount');
}
```

### Agregar Campos Personalizados a Tenants

1. Modificar migración de `tenants`:

```php
Schema::table('tenants', function (Blueprint $table) {
    $table->string('business_name')->nullable();
    $table->string('contact_email')->nullable();
    $table->decimal('monthly_price', 8, 2)->default(29.00);
});
```

2. Actualizar el modelo `Tenant`:

```php
public static function getCustomColumns(): array
{
    return [
        'id',
        'plan',
        'subscription_ends_at',
        'business_name',    // Nuevo
        'contact_email',    // Nuevo
        'monthly_price',    // Nuevo
    ];
}
```

---

## 🐛 Troubleshooting

### Error: "Tenant no encontrado"

**Causa:** Intentando acceder desde un subdominio de tenant en lugar del dominio central.

**Solución:** Accede desde `http://localhost/admin` o tu dominio central configurado.

---

### Error: "ai_usage_logs table doesn't exist"

**Causa:** No se ejecutó la migración.

**Solución:**
```bash
cd backend
php artisan migrate
```

---

### El componente Vue no se carga

**Causa:** El componente no está registrado o los assets no están compilados.

**Solución:**
```bash
# Recompilar assets
npm run dev

# Verificar que el componente esté importado en main.js
```

---

## 📚 Próximas Mejoras Sugeridas

- [ ] **Sistema de Autenticación:** Implementar login de super admin
- [ ] **Gestión de Planes:** CRUD de planes (Free, Premium, Enterprise)
- [ ] **Facturación:** Integración con Stripe/PayPal
- [ ] **Notificaciones:** Alertas por email cuando un tenant exceda límites
- [ ] **Exportación de Datos:** Descargar reportes en CSV/Excel
- [ ] **Suspensión de Tenants:** Suspender/reactivar tenants desde el panel
- [ ] **Logs de Actividad:** Registro de todas las acciones de super admin
- [ ] **Gráficos Avanzados:** Chart.js para visualización de tendencias
- [ ] **Dashboard de Costos:** Proyección de gastos de infraestructura
- [ ] **Backup Management:** Gestión de backups por tenant

---

## 📝 Notas Adicionales

### Base de Datos

- La tabla `ai_usage_logs` vive en la **base de datos CENTRAL** (no en la BD de cada tenant)
- Usa la conexión `mysql` (configurada en `config/database.php`)
- Tiene foreign key a la tabla `tenants`

### Modelo de Datos

```sql
-- Tabla central de tenants (ya existe)
tenants
  - id (string, PK)
  - plan (string)
  - subscription_ends_at (datetime)
  - data (json)
  - created_at, updated_at

-- Tabla central de dominios (ya existe)
domains
  - id (int, PK)
  - domain (string)
  - tenant_id (FK a tenants)
  - created_at, updated_at

-- Tabla central de logs de IA (nueva)
ai_usage_logs
  - id (bigint, PK)
  - tenant_id (FK a tenants)
  - action_type (string)
  - tokens_used (int)
  - estimated_cost (decimal)
  - model_used (string)
  - prompt_summary (text)
  - metadata (json)
  - created_at, updated_at
```

---

## 👥 Contacto y Soporte

Para más información sobre el panel de Super Admin, consulta la documentación del proyecto o contacta al equipo de desarrollo.

---

**Versión:** 1.0.0  
**Fecha:** 27 de noviembre de 2025  
**Estado:** ✅ Implementado y Funcional
