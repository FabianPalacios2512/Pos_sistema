# 🔄 Sistema de Sincronización Offline - Documentación Completa

## 📋 Índice

1. [Visión General](#visión-general)
2. [Arquitectura del Sistema](#arquitectura-del-sistema)
3. [Archivos Creados](#archivos-creados)
4. [Cómo Funciona](#cómo-funciona)
5. [Operaciones Sincronizables](#operaciones-sincronizables)
6. [Flujo de Sincronización](#flujo-de-sincronización)
7. [Interfaz de Usuario](#interfaz-de-usuario)
8. [Casos de Uso](#casos-de-uso)
9. [Configuración Avanzada](#configuración-avanzada)
10. [Troubleshooting](#troubleshooting)

---

## 🎯 Visión General

El sistema de sincronización offline permite que la aplicación **POS funcione completamente sin conexión a internet**, guardando todas las operaciones críticas en IndexedDB y sincronizándolas automáticamente cuando la conexión se restablece.

### ✨ Características Principales

- ✅ **Operación 100% Offline**: Ventas, inventario, gastos funcionan sin internet
- ✅ **Sincronización Automática**: Detecta conexión y sube datos al servidor
- ✅ **Reintentos Inteligentes**: 3 intentos por operación con backoff exponencial
- ✅ **UI en Tiempo Real**: Badge flotante muestra estado de sincronización
- ✅ **Priorización**: Ventas se sincronizan primero que otros registros
- ✅ **Limpieza Automática**: Operaciones fallidas se eliminan después de 7 días
- ✅ **Transparente al Usuario**: El sistema funciona igual online o offline

---

## 🏗️ Arquitectura del Sistema

```
┌─────────────────────────────────────────────────────────────┐
│                     FRONTEND (Vue 3)                        │
│  ┌──────────────┐  ┌──────────────┐  ┌─────────────────┐   │
│  │  Componentes │  │   Services   │  │  OfflineBadge   │   │
│  │     Vue      │  │ (API calls)  │  │    (UI Real-    │   │
│  │              │  │              │  │     Time)       │   │
│  └──────┬───────┘  └──────┬───────┘  └────────┬────────┘   │
│         │                 │                    │            │
│         └─────────────────┼────────────────────┘            │
│                           │                                 │
│                  ┌────────▼────────┐                        │
│                  │ Axios Instance  │                        │
│                  │  (HTTP Client)  │                        │
│                  └────────┬────────┘                        │
│                           │                                 │
│                  ┌────────▼────────────┐                    │
│                  │ offlineInterceptor  │◄───────┐           │
│                  │  (Captura errores)  │        │           │
│                  └────────┬────────────┘        │           │
│                           │                     │           │
│                  ┌────────▼────────────┐        │           │
│                  │  offlineSyncManager │        │           │
│                  │   (Gestión de cola) │────────┘           │
│                  └────────┬────────────┘                    │
│                           │                                 │
│                  ┌────────▼────────┐                        │
│                  │   IndexedDB     │                        │
│                  │ (POS_OfflineDB) │                        │
│                  └─────────────────┘                        │
└─────────────────────────────────────────────────────────────┘
                           │
                           │ (Cuando hay conexión)
                           │
                  ┌────────▼────────┐
                  │  Backend API    │
                  │  (Laravel 10)   │
                  └────────┬────────┘
                           │
                  ┌────────▼────────┐
                  │  MySQL Database │
                  └─────────────────┘
```

---

## 📁 Archivos Creados

### 1. **`src/utils/offlineSync.js`** (374 líneas)
**Gestor principal de sincronización offline**

- `OfflineSyncManager`: Clase singleton que gestiona IndexedDB
- `initDB()`: Inicializa base de datos con schema
- `savePendingOperation()`: Guarda operación en cola
- `syncPendingOperations()`: Sincroniza todas las operaciones pendientes
- `getPendingCount()`: Cuenta operaciones pendientes
- `cleanupOldOperations()`: Elimina operaciones fallidas antiguas

**Base de datos IndexedDB:**
```javascript
DB_NAME: 'POS_OfflineDB'
STORE_NAME: 'pendingOperations'
Indexes: timestamp, type, status
```

### 2. **`src/utils/offlineInterceptor.js`** (167 líneas)
**Interceptor de Axios para captura automática**

- `setupOfflineInterceptor()`: Configura interceptores request/response
- `isSyncableOperation()`: Determina si operación puede guardarse offline
- `getSyncStatus()`: Estado actual de sincronización
- `forceSyncNow()`: Sincronización manual

**Operaciones Sincronizables:**
```javascript
'POST:/api/sales' -> Prioridad 1
'POST:/api/cash-sessions' -> Prioridad 1
'POST:/api/expenses' -> Prioridad 2
'POST:/api/inventory' -> Prioridad 2
'POST:/api/products' -> Prioridad 3
'POST:/api/customers' -> Prioridad 4
```

### 3. **`src/components/OfflineSyncBadge.vue`** (369 líneas)
**Badge flotante con UI de estado de sincronización**

**Características:**
- Badge flotante en esquina inferior derecha
- Indicador de contador de operaciones pendientes
- Panel expandible con lista de operaciones
- Botón de sincronización manual
- Estados visuales: syncing, offline, idle
- Animaciones suaves (slide-up, scale-up)

**Props reactivos:**
- `pendingCount`: Número de operaciones pendientes
- `isOnline`: Estado de conexión
- `syncStatus`: 'idle' | 'syncing' | 'offline'

### 4. **Modificaciones en archivos existentes**

**`src/main.js`:**
```javascript
import { setupOfflineInterceptor } from './utils/offlineInterceptor.js'
setupOfflineInterceptor()
```

**`src/App.vue`:**
```vue
import OfflineSyncBadge from './components/OfflineSyncBadge.vue'

<template>
  <OfflineSyncBadge />
</template>
```

---

## ⚙️ Cómo Funciona

### 🔄 Flujo Normal (CON conexión)

```
Usuario hace acción (ej: venta)
    ↓
Componente Vue llama API service
    ↓
Service usa Axios para POST /api/sales
    ↓
Request pasa por offlineInterceptor
    ↓
Interceptor detecta navigator.onLine = true
    ↓
Request va directo al servidor
    ↓
Respuesta exitosa devuelta al componente
```

### 🔴 Flujo Offline (SIN conexión)

```
Usuario hace acción (ej: venta)
    ↓
Componente Vue llama API service
    ↓
Service usa Axios para POST /api/sales
    ↓
Axios lanza error "Network Error"
    ↓
Interceptor CAPTURA el error
    ↓
Verifica: navigator.onLine = false
    ↓
Busca si es operación sincronizable (POST:/api/sales)
    ↓
offlineSyncManager.savePendingOperation({
  type: 'sale',
  endpoint: '/api/sales',
  method: 'POST',
  data: {...},
  priority: 1,
  timestamp: Date.now()
})
    ↓
Guarda en IndexedDB
    ↓
Devuelve respuesta simulada de éxito
    ↓
Usuario ve confirmación normal (no sabe que está offline)
    ↓
Badge muestra "1 operación pendiente"
```

### 🟢 Flujo de Sincronización (Vuelve conexión)

```
navigator.onLine = true (evento detectado)
    ↓
offlineSyncManager escucha evento 'online'
    ↓
Llama automáticamente syncPendingOperations()
    ↓
Lee todas las operaciones con status='pending'
    ↓
Ordena por prioridad (ventas primero)
    ↓
Para cada operación:
  ├─ Hace axios({ method, url, data })
  ├─ Si éxito -> marca status='synced' y elimina
  ├─ Si falla -> incrementa retries
  └─ Si retries > 3 -> marca status='failed'
    ↓
Badge actualiza contador en tiempo real
    ↓
Cuando todas sincronizan -> Badge desaparece
```

---

## 📊 Operaciones Sincronizables

### Tabla de Prioridades

| Operación | Endpoint | Prioridad | Motivo |
|-----------|----------|-----------|--------|
| **Ventas** | `POST /api/sales` | 🔴 1 | Crítico - Dinero en efectivo |
| **Cierre de caja** | `PUT /api/cash-sessions` | 🔴 1 | Crítico - Cuadre de caja |
| **Gastos** | `POST /api/expenses` | 🟠 2 | Importante - Registro contable |
| **Inventario** | `POST /api/inventory` | 🟠 2 | Importante - Control de stock |
| **Productos** | `POST /api/products` | 🟡 3 | Normal - Puede esperar |
| **Clientes** | `POST /api/customers` | 🟢 4 | Baja - No crítico |

### Agregar Nueva Operación Sincronizable

**Paso 1:** Editar `src/utils/offlineInterceptor.js`

```javascript
const SYNCABLE_OPERATIONS = {
  // ... operaciones existentes
  
  // Nueva operación
  'POST:/api/transfers': { 
    type: 'stock_transfer', 
    priority: 2, 
    label: 'Transferencia de stock' 
  },
}
```

**Paso 2:** Probar offline

1. Desconectar internet (modo avión o DevTools Network Offline)
2. Realizar la nueva operación
3. Verificar que aparece en el badge
4. Reconectar internet
5. Verificar que se sincroniza automáticamente

---

## 🎨 Interfaz de Usuario

### Badge Flotante

**Ubicación:** Esquina inferior derecha (fixed, z-index 50)

**Estados visuales:**

#### 1. **Syncing (Sincronizando)**
```
┌─────────────────────────────────┐
│ 🔄 SINCRONIZANDO                │
│    Subiendo al servidor...      │
└─────────────────────────────────┘
```
- Icono: Spinner girando
- Color: Azul con gradiente (blue-600 to indigo-600)
- Animación: Pulse

#### 2. **Offline (Sin conexión)**
```
┌─────────────────────────────────┐
│ 📡 SIN CONEXIÓN           [ 3 ] │
│    3 operaciones guardadas      │
└─────────────────────────────────┘
```
- Icono: WiFi tachado
- Contador: Badge rojo con número
- Color: Azul con gradiente

#### 3. **Pending (Pendiente)**
```
┌─────────────────────────────────┐
│ 🔄 PENDIENTE              [ 5 ] │
│    5 operaciones                │
└─────────────────────────────────┘
```
- Icono: Flecha circular
- Contador: Badge rojo animado (bounce)

### Panel de Detalles

**Al hacer clic en el badge se expande:**

```
┌───────────────────────────────────────────┐
│ Operaciones Pendientes              [ X ] │
├───────────────────────────────────────────┤
│  📄 Venta                    [Pendiente]  │
│     Hace 5 min                            │
│     /api/sales                            │
├───────────────────────────────────────────┤
│  📦 Nuevo producto           [Pendiente]  │
│     Hace 10 min                           │
│     /api/products                         │
├───────────────────────────────────────────┤
│  💰 Nuevo gasto              [Pendiente]  │
│     Hace 15 min                           │
│     /api/expenses                         │
│     ⚠️ Reintentos: 2/3                    │
├───────────────────────────────────────────┤
│  [ Sincronizar ahora ]  [ Refrescar ]    │
└───────────────────────────────────────────┘
```

**Características:**
- Scroll vertical si hay muchas operaciones
- Timestamps relativos ("Hace 5 min")
- Badges de estado (Pendiente/Sincronizando/Fallido)
- Indicador de reintentos si hay fallos
- Botón "Sincronizar ahora" para forzar sync

---

## 💡 Casos de Uso

### Caso 1: Ventas en Zona Sin Cobertura

**Escenario:** Vendedor en feria al aire libre con internet intermitente

**Flujo:**
1. 📶 Pierde señal de internet
2. ✅ Realiza 10 ventas normalmente
3. 💾 Cada venta se guarda en IndexedDB
4. 🔴 Badge muestra "10 operaciones pendientes"
5. 📶 Vuelve a tener internet
6. 🔄 Sistema sincroniza automáticamente las 10 ventas
7. ✅ Badge desaparece cuando todo se sube

**Ventaja:** Cero pérdida de ventas, experiencia fluida

### Caso 2: Cierre de Caja sin Internet

**Escenario:** Cortaron el servicio de internet al final del día

**Flujo:**
1. 🏪 Cajero intenta cerrar caja
2. 🔴 No hay internet
3. ✅ Sistema guarda cierre offline
4. 💾 Se guardan: monto final, billetes, monedas, diferencia
5. 📊 Cajero puede irse tranquilo
6. 🌅 Al día siguiente hay internet
7. 🔄 Cierre se sube automáticamente al abrir el sistema

**Ventaja:** Proceso de cierre no depende de conexión

### Caso 3: Registro de Gastos en Movimiento

**Escenario:** Gerente registra gastos desde el celular en taxi

**Flujo:**
1. 📱 Abre PWA en celular
2. 🚕 Está en movimiento, señal intermitente
3. ✅ Registra 3 gastos (gasolina, comida, insumos)
4. 💾 Se guardan localmente
5. 🏢 Llega a oficina con WiFi
6. 🔄 Gastos se sincronizan automáticamente

**Ventaja:** Registros en tiempo real sin preocuparse por conexión

### Caso 4: Fallo del Servidor

**Escenario:** Servidor backend está caído temporalmente

**Flujo:**
1. ⚠️ Servidor devuelve error 500
2. 🔄 Sistema reintenta 3 veces (con backoff)
3. ❌ Después de 3 intentos marca como "fallido"
4. 👤 Usuario ve notificación en badge
5. 🔧 Se arregla el servidor
6. 🔄 Usuario hace clic en "Sincronizar ahora"
7. ✅ Operaciones fallidas se reintentan y suben

**Ventaja:** Sistema robusto ante fallos temporales

---

## 🛠️ Configuración Avanzada

### Ajustar Reintentos

**Archivo:** `src/utils/offlineSync.js`

```javascript
// Cambiar de 3 a 5 reintentos
const operationData = {
  // ...
  maxRetries: 5  // <- Aquí
}
```

### Cambiar Tiempo de Limpieza

**Archivo:** `src/utils/offlineSync.js`

```javascript
async cleanupOldOperations() {
  const sevenDaysAgo = Date.now() - (14 * 24 * 60 * 60 * 1000)  // 14 días
  // ...
}
```

### Modificar Intervalo de Refresco del Badge

**Archivo:** `src/components/OfflineSyncBadge.vue`

```javascript
onMounted(() => {
  // Cambiar de 5 a 10 segundos
  const interval = setInterval(refreshData, 10000)
  // ...
})
```

### Deshabilitar Sincronización Automática

**Archivo:** `src/utils/offlineSync.js`

```javascript
setupEventListeners() {
  // Comentar estas líneas para modo manual solo
  // window.addEventListener('online', () => {
  //   this.isOnline = true
  //   this.syncPendingOperations()
  // })
}
```

### Agregar Notificaciones Toast

**En OfflineSyncManager:**

```javascript
import { showToast } from '../utils/toast.js'

async syncPendingOperations() {
  // ...
  
  if (successful > 0) {
    showToast(`✅ ${successful} operaciones sincronizadas`, 'success')
  }
  
  if (failed > 0) {
    showToast(`⚠️ ${failed} operaciones fallaron`, 'warning')
  }
}
```

---

## 🐛 Troubleshooting

### Problema: Badge no aparece

**Síntomas:** El badge no se muestra aunque hay operaciones pendientes

**Solución:**
1. Verificar que `OfflineSyncBadge` está importado en `App.vue`
2. Abrir DevTools -> Application -> IndexedDB -> POS_OfflineDB
3. Verificar si hay registros en `pendingOperations`
4. Revisar consola por errores de inicialización

### Problema: Operaciones no se sincronizan automáticamente

**Síntomas:** Vuelve internet pero operaciones quedan pendientes

**Solución:**
1. Verificar que el evento `online` se dispara:
   ```javascript
   window.addEventListener('online', () => console.log('ONLINE'))
   ```
2. Revisar que `syncPendingOperations()` no está bloqueada
3. Usar botón "Sincronizar ahora" manualmente
4. Verificar logs en consola

### Problema: IndexedDB no inicializa

**Síntomas:** Error "IDBOpenDBRequest failed"

**Solución:**
1. Verificar que el navegador soporta IndexedDB
2. Limpiar datos del sitio en DevTools
3. Verificar permisos del navegador
4. Probar en ventana incógnito

### Problema: Operaciones se duplican

**Síntomas:** Una venta se registra 2 veces

**Solución:**
1. Verificar que el endpoint backend es idempotente
2. Agregar timestamps o UUIDs únicos:
   ```javascript
   data: {
     ...originalData,
     _offline_id: crypto.randomUUID()
   }
   ```
3. Backend debe validar duplicados por `_offline_id`

### Problema: Badge tarda en actualizarse

**Síntomas:** Contador no cambia en tiempo real

**Solución:**
1. Reducir intervalo de refresco:
   ```javascript
   setInterval(refreshData, 2000)  // 2 segundos
   ```
2. Usar listeners de offlineSyncManager:
   ```javascript
   offlineSyncManager.onStatusChange(() => refreshData())
   ```

---

## 📊 Monitoreo y Analytics

### Ver Estado de IndexedDB

**Chrome DevTools:**
1. F12 -> Application
2. Storage -> IndexedDB -> POS_OfflineDB
3. Ver `pendingOperations`

**Campos importantes:**
- `status`: 'pending' | 'synced' | 'failed'
- `retries`: Número de intentos
- `timestamp`: Cuándo se creó
- `type`: Tipo de operación

### Logs de Consola

**Mensajes importantes:**
```
✅ IndexedDB inicializada para offline sync
💾 Operación guardada offline: sale
🔴 Sin conexión detectada para: /api/sales
🔄 Iniciando sincronización de operaciones pendientes...
📦 Sincronizando 5 operaciones...
✅ Sincronización completada: 5 exitosas, 0 fallidas
```

### Estadísticas de Uso

**Agregar tracking en offlineSync.js:**

```javascript
async syncPendingOperations() {
  const startTime = Date.now()
  const operations = await this.getPendingOperations()
  
  // ... sincronización ...
  
  const syncTime = Date.now() - startTime
  
  // Enviar analytics
  analytics.track('offline_sync_completed', {
    operationsCount: operations.length,
    successful,
    failed,
    syncTimeMs: syncTime
  })
}
```

---

## 🚀 Próximos Pasos (Mejoras Futuras)

### 1. Resolución de Conflictos
Manejar casos donde datos cambiaron en servidor mientras estaba offline

### 2. Compresión de Datos
Comprimir payloads grandes antes de guardar en IndexedDB

### 3. Sync Selectivo
Permitir al usuario elegir qué operaciones sincronizar

### 4. Background Sync API
Usar Service Worker Background Sync para sincronizar incluso con app cerrada

### 5. Modo Offline First
Guardar TODAS las operaciones offline primero, sincronizar después

### 6. Exportar/Importar
Permitir exportar operaciones pendientes a JSON para backup

---

## 📚 Referencias

- [IndexedDB API - MDN](https://developer.mozilla.org/en-US/docs/Web/API/IndexedDB_API)
- [Axios Interceptors](https://axios-http.com/docs/interceptors)
- [Navigator.onLine - MDN](https://developer.mozilla.org/en-US/docs/Web/API/Navigator/onLine)
- [Service Worker Background Sync](https://web.dev/periodic-background-sync/)

---

## ✅ Checklist de Validación

Antes de dar por terminado el sistema, verificar:

- [ ] Badge aparece cuando hay operaciones pendientes
- [ ] Badge desaparece cuando todo se sincroniza
- [ ] Icono cambia según estado (syncing, offline, idle)
- [ ] Contador muestra número correcto
- [ ] Panel de detalles lista todas las operaciones
- [ ] Timestamps se muestran correctamente ("Hace X min")
- [ ] Botón "Sincronizar ahora" funciona
- [ ] Operaciones se priorizan correctamente (ventas primero)
- [ ] Reintentos funcionan (máximo 3 intentos)
- [ ] Operaciones fallidas se marcan correctamente
- [ ] Limpieza de operaciones antiguas funciona
- [ ] Sistema funciona en Chrome, Firefox, Safari
- [ ] Sistema funciona en móviles (Android/iOS)
- [ ] No hay memory leaks (verificar con DevTools)
- [ ] IndexedDB se inicializa correctamente

---

**Sistema implementado por:** GitHub Copilot  
**Versión:** 1.0.0  
**Fecha:** 2024  
**Compatibilidad:** Chrome 80+, Firefox 75+, Safari 14+, Edge 80+
