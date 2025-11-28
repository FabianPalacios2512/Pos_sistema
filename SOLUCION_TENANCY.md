# 🔧 SOLUCIÓN: Error 404/500 en Gastos - Problema de Multi-Tenancy

## 🎯 PROBLEMA IDENTIFICADO

El error ocurre porque estás accediendo al sistema desde `localhost:3000` pero el backend usa **multi-tenancy** y requiere que accedas desde el **dominio del tenant**.

### ❌ Incorrecto (causa error 404):
```
http://localhost:3000
```

### ✅ Correcto (funcionará):
```
http://asasasa.localhost:3000
http://venta-de-gorras.localhost:3000
```

---

## 🔍 ¿POR QUÉ SUCEDE ESTO?

El middleware `PreventAccessFromCentralDomains` de Laravel Tenancy **bloquea** las peticiones que vienen desde el dominio central (`localhost`) porque las rutas en `/api/*` son **solo para tenants**.

Cuando accedes desde `localhost:3000`:
1. Frontend hace request a `/api/expenses/categories`
2. Vite proxy envía a `http://127.0.0.1:8000/api/expenses/categories` con header `Host: localhost:3000`
3. Laravel ve el host `localhost` (dominio central) ❌
4. Middleware `PreventAccessFromCentralDomains` rechaza la petición con 404

Cuando accedes desde `asasasa.localhost:3000`:
1. Frontend hace request a `/api/expenses/categories`
2. Vite proxy envía a `http://127.0.0.1:8000/api/expenses/categories` con header `Host: asasasa.localhost:3000`
3. Laravel identifica tenant `asasasa` por el subdominio ✅
4. Middleware permite la petición y carga la base de datos del tenant
5. Controlador responde con datos

---

## 💡 SOLUCIÓN INMEDIATA

### Paso 1: Verificar tenants disponibles

```bash
cd backend
mysql -u root pos_sistema -e "SELECT d.domain, t.business_name FROM domains d JOIN tenants t ON d.tenant_id = t.id;"
```

**Tenants disponibles:**
- `asasasa.localhost` → Tienda ASASASA
- `asqwqw.localhost` → Comercial ASQWQW
- `naturatienda` → Tienda Natura
- `venta-de-gorras.localhost` → Venta de Gorras Premium

### Paso 2: Acceder desde dominio del tenant

En tu navegador, **cierra todas las pestañas** y abre una nueva en:

```
http://asasasa.localhost:3000
```

O cualquiera de los otros dominios `.localhost:3000`

### Paso 3: Iniciar sesión

Inicia sesión con las credenciales del tenant correspondiente.

### Paso 4: Probar módulo de gastos

Ahora ve al módulo "Gastos Operativos" y debería cargar correctamente sin errores 404/500.

---

## 🛠️ VERIFICACIÓN TÉCNICA

Si quieres verificar que el problema era el dominio, puedes hacer esto:

### Antes (con error):
```bash
curl -H "Accept: application/json" http://localhost:8000/api/expenses/categories
# Respuesta: 404 - PreventAccessFromCentralDomains
```

### Después (funciona):
```bash
curl -H "Accept: application/json" -H "Host: asasasa.localhost" http://127.0.0.1:8000/api/expenses/categories
# Respuesta: 401 Unauthenticated (pero ruta encontrada ✅)
```

---

## 📚 ENTENDIENDO MULTI-TENANCY

Este sistema usa **tenancy por subdominio**:

- Cada tenant tiene su **propia base de datos** (ej: `tenant_asasasa`)
- El sistema identifica el tenant por el **subdominio en la URL**
- Rutas en `tenant_api.php` solo funcionan con dominios de tenant
- Rutas en `api.php` funcionan en dominio central

```
├── localhost:3000          → Dominio Central (admin, god-mode)
│   └── /admin/api/*        → API central (usuarios, tenants)
│
└── {tenant}.localhost:3000 → Dominio Tenant (POS, ventas, etc)
    └── /api/*              → API del tenant (productos, ventas, gastos)
```

---

## 🎓 PARA EL FUTURO

**Siempre accede al sistema así:**

1. **Admin/God Mode**: `http://localhost:3000/god-mode`
2. **Operación del Tenant**: `http://{tenant}.localhost:3000`

**Nunca** intentes usar módulos de tenant (ventas, gastos, inventario) desde `localhost` sin subdominio.

---

## ✅ RESUMEN

1. ❌ **Problema**: Acceso desde `localhost:3000` → Error 404/500
2. ✅ **Solución**: Acceder desde `asasasa.localhost:3000` (o cualquier tenant)
3. 🎯 **Razón**: Multi-tenancy requiere subdominio para identificar tenant
4. 🚀 **Resultado**: Todos los módulos (gastos, ventas, etc) funcionarán correctamente

---

**¿Listo?** Cierra el navegador, abre `http://asasasa.localhost:3000`, inicia sesión y prueba nuevamente. ¡Debería funcionar! 🎉
