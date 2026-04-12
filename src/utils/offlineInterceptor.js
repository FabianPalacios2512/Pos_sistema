/**
 * Axios Interceptor para Modo Offline
 * Captura peticiones fallidas y las guarda para sincronización posterior
 */

import axios from 'axios'
import offlineSyncManager from './offlineSync.js'

// Tipos de operaciones que se pueden hacer offline
const SYNCABLE_OPERATIONS = {
  // Ventas
  'POST:/api/sales': { type: 'sale', priority: 1, label: 'Venta' },
  'PUT:/api/sales': { type: 'sale_update', priority: 2, label: 'Actualización de venta' },
  
  // Productos
  'POST:/api/products': { type: 'product', priority: 3, label: 'Nuevo producto' },
  'PUT:/api/products': { type: 'product_update', priority: 3, label: 'Actualización de producto' },
  
  // Inventario
  'POST:/api/inventory': { type: 'inventory', priority: 2, label: 'Movimiento de inventario' },
  'PUT:/api/inventory': { type: 'inventory_update', priority: 2, label: 'Actualización de inventario' },
  
  // Clientes
  'POST:/api/customers': { type: 'customer', priority: 4, label: 'Nuevo cliente' },
  'PUT:/api/customers': { type: 'customer_update', priority: 4, label: 'Actualización de cliente' },
  
  // Gastos
  'POST:/api/expenses': { type: 'expense', priority: 2, label: 'Nuevo gasto' },
  'PUT:/api/expenses': { type: 'expense_update', priority: 2, label: 'Actualización de gasto' },
  
  // Cajas
  'POST:/api/cash-sessions': { type: 'cash_session', priority: 1, label: 'Sesión de caja' },
  'PUT:/api/cash-sessions': { type: 'cash_session_update', priority: 1, label: 'Cierre de caja' },
}

/**
 * Verificar si una operación es sincronizable
 */
function isSyncableOperation(config) {
  const method = config.method?.toUpperCase()
  const url = config.url
  
  // Solo POST, PUT, PATCH son sincronizables
  if (!['POST', 'PUT', 'PATCH'].includes(method)) {
    return false
  }
  
  // Buscar coincidencia en operaciones sincronizables
  for (const pattern in SYNCABLE_OPERATIONS) {
    const [patternMethod, patternUrl] = pattern.split(':')
    if (method === patternMethod && url?.includes(patternUrl)) {
      return SYNCABLE_OPERATIONS[pattern]
    }
  }
  
  return false
}

/**
 * Configurar interceptores de Axios
 */
export function setupOfflineInterceptor() {
  // Request interceptor - marcar peticiones
  axios.interceptors.request.use(
    (config) => {
      // Agregar flag de online/offline
      config.metadata = {
        startTime: Date.now(),
        isOnline: navigator.onLine
      }
      return config
    },
    (error) => {
      return Promise.reject(error)
    }
  )

  // Response interceptor - capturar errores de red
  axios.interceptors.response.use(
    (response) => {
      // Petición exitosa
      return response
    },
    async (error) => {
      const config = error.config
      
      // Verificar si es un error de red (sin conexión)
      const isNetworkError = !error.response && error.message === 'Network Error'
      const isTimeout = error.code === 'ECONNABORTED'
      
      if ((isNetworkError || isTimeout) && !navigator.onLine) {
        // Verificar si es una operación sincronizable
        const syncInfo = isSyncableOperation(config)
        
        if (syncInfo) {
          try {
            // Guardar operación para sincronización posterior
            await offlineSyncManager.savePendingOperation({
              type: syncInfo.type,
              label: syncInfo.label,
              priority: syncInfo.priority,
              endpoint: config.url,
              method: config.method.toUpperCase(),
              data: config.data,
              params: config.params,
              headers: config.headers
            })
            
            // Retornar una respuesta simulada de éxito
            return Promise.resolve({
              data: {
                success: true,
                offline: true,
                message: `${syncInfo.label} guardado. Se sincronizará cuando vuelva la conexión.`,
                pendingSync: true
              },
              status: 200,
              statusText: 'OK (Offline)',
              config
            })
          } catch (dbError) {
            console.error('Error guardando operación offline:', dbError)
          }
        } else {
          // Operación no sincronizable
        }
      }
      
      // Propagar el error original
      return Promise.reject(error)
    }
  )
}

/**
 * Verificar estado de sincronización
 */
export async function getSyncStatus() {
  const pendingCount = await offlineSyncManager.getPendingCount()
  const isOnline = navigator.onLine
  
  return {
    isOnline,
    pendingCount,
    hasPendingOperations: pendingCount > 0,
    canSync: isOnline && pendingCount > 0
  }
}

/**
 * Forzar sincronización manual
 */
export async function forceSyncNow() {
  if (!navigator.onLine) {
    throw new Error('No hay conexión a internet')
  }
  
  return await offlineSyncManager.syncPendingOperations()
}

export default {
  setupOfflineInterceptor,
  getSyncStatus,
  forceSyncNow
}
