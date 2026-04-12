/**
 * Sistema de Validación de Tiempo Offline
 * Previene uso fraudulento limitando tiempo máximo sin conexión
 */

const OFFLINE_TIME_LIMIT = 48 * 60 * 60 * 1000 // 48 horas de trabajo offline
const CHECK_INTERVAL = 5000 // Verificar cada 5 segundos
const STORAGE_KEY = 'offline_start_time'

class OfflineTimeValidator {
  constructor() {
    this.offlineStartTime = null
    this.checkInterval = null
    this.listeners = []
    this.isOnline = navigator.onLine
    this.hasExceededLimit = false
    
    this.init()
  }

  init() {
    // Cargar tiempo de inicio offline guardado
    const saved = localStorage.getItem(STORAGE_KEY)
    if (saved) {
      this.offlineStartTime = parseInt(saved, 10)
      // Verificar si ya excedió el límite al cargar
      this.checkTimeLimit()
    }

    // Escuchar cambios de conexión
    window.addEventListener('online', () => this.handleOnline())
    window.addEventListener('offline', () => this.handleOffline())

    // Iniciar verificación periódica si ya está offline
    if (!this.isOnline) {
      this.startChecking()
    }
  }

  handleOffline() {
    this.isOnline = false
    
    // Guardar tiempo de inicio si es la primera vez
    if (!this.offlineStartTime) {
      this.offlineStartTime = Date.now()
      localStorage.setItem(STORAGE_KEY, this.offlineStartTime.toString())
    }

    this.startChecking()
  }

  handleOnline() {
    this.isOnline = true
    this.hasExceededLimit = false
    
    // Limpiar tiempo offline guardado
    this.offlineStartTime = null
    localStorage.removeItem(STORAGE_KEY)
    
    // Detener verificaciones
    this.stopChecking()
    
    // Notificar a listeners
    this.notifyListeners({ type: 'online_restored' })
  }

  startChecking() {
    if (this.checkInterval) return // Ya está verificando

    this.checkInterval = setInterval(() => {
      this.checkTimeLimit()
    }, CHECK_INTERVAL)

  }

  stopChecking() {
    if (this.checkInterval) {
      clearInterval(this.checkInterval)
      this.checkInterval = null
    }
  }

  checkTimeLimit() {
    if (this.isOnline || !this.offlineStartTime) {
      return false
    }

    const currentTime = Date.now()
    const timeOffline = currentTime - this.offlineStartTime
    const remainingTime = OFFLINE_TIME_LIMIT - timeOffline

    if (timeOffline >= OFFLINE_TIME_LIMIT && !this.hasExceededLimit) {
      this.hasExceededLimit = true
      console.error(`LÍMITE DE TIEMPO OFFLINE EXCEDIDO: ${Math.floor(timeOffline / 1000)}s`)
      
      // Notificar a listeners
      this.notifyListeners({
        type: 'time_limit_exceeded',
        offlineTime: timeOffline,
        limit: OFFLINE_TIME_LIMIT
      })

      return true
    }

    // Log de advertencia cuando faltan 10 segundos
    if (remainingTime <= 10000 && remainingTime > 5000 && !this.hasExceededLimit) {
      console.warn(`Quedan ${Math.floor(remainingTime / 1000)} segundos antes de requerir conexión`)
      this.notifyListeners({
        type: 'time_limit_warning',
        remainingTime
      })
    }

    return false
  }

  getOfflineStatus() {
    if (this.isOnline) {
      return {
        isOnline: true,
        offlineTime: 0,
        remainingTime: OFFLINE_TIME_LIMIT,
        hasExceededLimit: false,
        percentage: 0
      }
    }

    if (!this.offlineStartTime) {
      return {
        isOnline: false,
        offlineTime: 0,
        remainingTime: OFFLINE_TIME_LIMIT,
        hasExceededLimit: false,
        percentage: 0
      }
    }

    const currentTime = Date.now()
    const offlineTime = currentTime - this.offlineStartTime
    const remainingTime = Math.max(0, OFFLINE_TIME_LIMIT - offlineTime)
    const percentage = Math.min(100, (offlineTime / OFFLINE_TIME_LIMIT) * 100)

    return {
      isOnline: false,
      offlineTime,
      remainingTime,
      hasExceededLimit: this.hasExceededLimit,
      percentage,
      offlineStartTime: this.offlineStartTime
    }
  }

  /**
   * Agregar listener para eventos
   */
  onStatusChange(callback) {
    this.listeners.push(callback)
  }

  /**
   * Notificar a todos los listeners
   */
  notifyListeners(data) {
    this.listeners.forEach(listener => {
      try {
        listener(data)
      } catch (error) {
        console.error('Error en listener de OfflineTimeValidator:', error)
      }
    })
  }

  /**
   * Forzar reconexión (para uso en modal)
   */
  requireReconnection() {
    if (this.isOnline) {
      return true // Ya está online
    }

    return false // Aún offline
  }

  /**
   * Resetear validación (solo para testing)
   */
  reset() {
    console.warn('Reseteando validación de tiempo offline (SOLO TESTING)')
    this.offlineStartTime = null
    this.hasExceededLimit = false
    localStorage.removeItem(STORAGE_KEY)
    this.stopChecking()
  }

  /**
   * Obtener configuración actual
   */
  getConfig() {
    return {
      timeLimit: OFFLINE_TIME_LIMIT,
      timeLimitFormatted: this.formatTime(OFFLINE_TIME_LIMIT),
      checkInterval: CHECK_INTERVAL,
      isTestMode: OFFLINE_TIME_LIMIT < 60000 // Menor a 1 minuto = test mode
    }
  }

  /**
   * Formatear tiempo en formato legible
   */
  formatTime(ms) {
    const seconds = Math.floor(ms / 1000)
    const minutes = Math.floor(seconds / 60)
    const hours = Math.floor(minutes / 60)

    if (hours > 0) {
      return `${hours}h ${minutes % 60}m`
    } else if (minutes > 0) {
      return `${minutes}m ${seconds % 60}s`
    } else {
      return `${seconds}s`
    }
  }
}

// Singleton
const offlineTimeValidator = new OfflineTimeValidator()

// Configuración inicial
const config = offlineTimeValidator.getConfig()

export default offlineTimeValidator
