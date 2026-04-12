/**
 * Sistema de Sincronización Offline
 * Gestiona operaciones cuando no hay conexión y las sincroniza cuando vuelve
 */

import axios from 'axios'

const DB_NAME = 'POS_OfflineDB'
const DB_VERSION = 1
const STORE_NAME = 'pendingOperations'

class OfflineSyncManager {
  constructor() {
    this.db = null
    this.isOnline = navigator.onLine
    this.syncInProgress = false
    this.listeners = []
    
    this.initDB()
    this.setupEventListeners()
  }

  /**
   * Inicializar IndexedDB
   */
  async initDB() {
    return new Promise((resolve, reject) => {
      const request = indexedDB.open(DB_NAME, DB_VERSION)

      request.onerror = () => reject(request.error)
      request.onsuccess = () => {
        this.db = request.result
        resolve(this.db)
      }

      request.onupgradeneeded = (event) => {
        const db = event.target.result
        
        if (!db.objectStoreNames.contains(STORE_NAME)) {
          const store = db.createObjectStore(STORE_NAME, { 
            keyPath: 'id', 
            autoIncrement: true 
          })
          store.createIndex('timestamp', 'timestamp', { unique: false })
          store.createIndex('type', 'type', { unique: false })
          store.createIndex('status', 'status', { unique: false })
        }
      }
    })
  }

  /**
   * Configurar listeners de conectividad
   */
  setupEventListeners() {
    window.addEventListener('online', () => {
      this.isOnline = true
      this.notifyListeners('online')
      this.syncPendingOperations()
    })

    window.addEventListener('offline', () => {
      this.isOnline = false
      this.notifyListeners('offline')
    })
  }

  /**
   * Registrar listener de cambios de estado
   */
  onStatusChange(callback) {
    this.listeners.push(callback)
  }

  /**
   * Notificar a todos los listeners
   */
  notifyListeners(status) {
    this.listeners.forEach(callback => callback(status, this.isOnline))
  }

  /**
   * Guardar operación pendiente en IndexedDB
   */
  async savePendingOperation(operation) {
    if (!this.db) await this.initDB()

    return new Promise((resolve, reject) => {
      const transaction = this.db.transaction([STORE_NAME], 'readwrite')
      const store = transaction.objectStore(STORE_NAME)

      const operationData = {
        ...operation,
        timestamp: Date.now(),
        status: 'pending',
        retries: 0,
        maxRetries: 3
      }

      const request = store.add(operationData)

      request.onsuccess = () => {
        resolve(request.result)
      }
      request.onerror = () => reject(request.error)
    })
  }

  /**
   * Obtener todas las operaciones pendientes
   */
  async getPendingOperations() {
    if (!this.db) await this.initDB()

    return new Promise((resolve, reject) => {
      const transaction = this.db.transaction([STORE_NAME], 'readonly')
      const store = transaction.objectStore(STORE_NAME)
      const index = store.index('status')
      const request = index.getAll('pending')

      request.onsuccess = () => resolve(request.result)
      request.onerror = () => reject(request.error)
    })
  }

  /**
   * Obtener cantidad de operaciones pendientes
   */
  async getPendingCount() {
    if (!this.db) await this.initDB()

    return new Promise((resolve, reject) => {
      const transaction = this.db.transaction([STORE_NAME], 'readonly')
      const store = transaction.objectStore(STORE_NAME)
      const index = store.index('status')
      const request = index.count('pending')

      request.onsuccess = () => resolve(request.result)
      request.onerror = () => reject(request.error)
    })
  }

  /**
   * Actualizar estado de operación
   */
  async updateOperationStatus(id, status, error = null) {
    if (!this.db) await this.initDB()

    return new Promise((resolve, reject) => {
      const transaction = this.db.transaction([STORE_NAME], 'readwrite')
      const store = transaction.objectStore(STORE_NAME)
      const request = store.get(id)

      request.onsuccess = () => {
        const operation = request.result
        if (operation) {
          operation.status = status
          operation.syncedAt = Date.now()
          if (error) operation.error = error
          
          const updateRequest = store.put(operation)
          updateRequest.onsuccess = () => resolve()
          updateRequest.onerror = () => reject(updateRequest.error)
        }
      }
      request.onerror = () => reject(request.error)
    })
  }

  /**
   * Eliminar operación completada
   */
  async deleteOperation(id) {
    if (!this.db) await this.initDB()

    return new Promise((resolve, reject) => {
      const transaction = this.db.transaction([STORE_NAME], 'readwrite')
      const store = transaction.objectStore(STORE_NAME)
      const request = store.delete(id)

      request.onsuccess = () => resolve()
      request.onerror = () => reject(request.error)
    })
  }

  /**
   * Sincronizar todas las operaciones pendientes
   */
  async syncPendingOperations() {
    if (this.syncInProgress || !this.isOnline) {
      return
    }

    this.syncInProgress = true
    try {
      const operations = await this.getPendingOperations()
      
      if (operations.length === 0) {
        this.syncInProgress = false
        return
      }

      // Ordenar por timestamp (más antiguas primero)
      operations.sort((a, b) => a.timestamp - b.timestamp)

      let successful = 0
      let failed = 0

      for (const operation of operations) {
        try {
          await this.syncOperation(operation)
          await this.deleteOperation(operation.id)
          successful++
        } catch (error) {
          console.error(`Error sincronizando ${operation.type}:`, error)
          
          // Incrementar reintentos
          operation.retries = (operation.retries || 0) + 1
          
          if (operation.retries >= operation.maxRetries) {
            await this.updateOperationStatus(operation.id, 'failed', error.message)
            failed++
          } else {
            // Mantener como pendiente para reintentar
          }
        }
      }

      // Notificar resultado
      this.notifyListeners('synced', {
        successful,
        failed,
        total: operations.length
      })

    } catch (error) {
      console.error('Error en sincronización:', error)
    } finally {
      this.syncInProgress = false
    }
  }

  /**
   * Sincronizar una operación específica
   */
  async syncOperation(operation) {
    const { type, endpoint, method, data, params } = operation

    // Realizar la petición al servidor
    const response = await axios({
      method: method || 'POST',
      url: endpoint,
      data,
      params,
      headers: {
        'X-Offline-Sync': 'true',
        'X-Operation-Timestamp': operation.timestamp
      }
    })

    return response.data
  }

  /**
   * Obtener contador de operaciones pendientes
   */
  async getPendingCount() {
    const operations = await this.getPendingOperations()
    return operations.length
  }

  /**
   * Limpiar operaciones antiguas fallidas (más de 7 días)
   */
  async cleanupOldOperations() {
    if (!this.db) await this.initDB()

    const sevenDaysAgo = Date.now() - (7 * 24 * 60 * 60 * 1000)

    return new Promise((resolve, reject) => {
      const transaction = this.db.transaction([STORE_NAME], 'readwrite')
      const store = transaction.objectStore(STORE_NAME)
      const index = store.index('timestamp')
      const request = index.openCursor(IDBKeyRange.upperBound(sevenDaysAgo))

      let deleted = 0

      request.onsuccess = (event) => {
        const cursor = event.target.result
        if (cursor) {
          if (cursor.value.status === 'failed') {
            cursor.delete()
            deleted++
          }
          cursor.continue()
        } else {
          resolve(deleted)
        }
      }

      request.onerror = () => reject(request.error)
    })
  }
}

// Singleton instance
const offlineSyncManager = new OfflineSyncManager()

export default offlineSyncManager
