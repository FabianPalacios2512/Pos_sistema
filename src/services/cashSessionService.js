import { ref, readonly } from 'vue'
import apiClient from './apiClient'

/**
 * Servicio para manejo de sesiones de caja
 */
export const cashSessionService = {
  /**
   * Obtener la sesión de caja actual del usuario
   */
  async getCurrentSession() {
    try {
      const response = await apiClient.get('/cash-sessions/current')
      return response.data
    } catch (error) {
      console.error('Error al obtener sesión actual:', error)
      throw error
    }
  },

  /**
   * Verificar si el usuario tiene una sesión abierta
   */
  async checkSession() {
    try {
      const response = await apiClient.get('/cash-sessions/check')
      return response.data
    } catch (error) {
      console.error('Error al verificar sesión:', error)
      throw error
    }
  },

  /**
   * Abrir nueva sesión de caja
   * @param {Object} sessionData - Datos de apertura
   * @param {number} sessionData.opening_amount - Monto inicial
   * @param {string} sessionData.opening_notes - Notas de apertura (opcional)
   */
  async openSession(sessionData) {
    try {
      const response = await apiClient.post('/cash-sessions/open', sessionData)
      return response.data
    } catch (error) {
      console.error('Error al abrir sesión:', error)
      throw error
    }
  },

  /**
   * Cerrar sesión de caja actual
   * @param {Object} closeData - Datos de cierre
   * @param {number} closeData.actual_amount - Monto real en caja
   * @param {string} closeData.closing_notes - Notas de cierre (opcional)
   */
  async closeSession(closeData) {
    try {
      const response = await apiClient.post('/cash-sessions/close', closeData)
      return response.data
    } catch (error) {
      console.error('Error al cerrar sesión:', error)
      throw error
    }
  },

  /**
   * Obtener estadísticas de la sesión actual
   */
  async getSessionStats() {
    try {
      const response = await apiClient.get('/cash-sessions/stats')
      return response.data
    } catch (error) {
      console.error('Error al obtener estadísticas:', error)
      throw error
    }
  },

  /**
   * Forzar actualización de totales de la sesión
   */
  async updateTotals() {
    try {
      const response = await apiClient.post('/cash-sessions/update-totals')
      return response.data
    } catch (error) {
      console.error('Error al actualizar totales:', error)
      throw error
    }
  },

  /**
   * Obtener resumen del día actual
   */
  async getDailySummary() {
    try {
      const response = await apiClient.get('/cash-sessions/daily-summary')
      return response.data
    } catch (error) {
      console.error('Error al obtener resumen diario:', error)
      throw error
    }
  },

  /**
   * Obtener historial de sesiones
   * @param {Object} params - Parámetros de consulta
   * @param {number} params.limit - Límite de resultados
   * @param {number} params.page - Página actual
   */
  async getHistory(params = {}) {
    try {
      const response = await apiClient.get('/cash-sessions/history', { params })
      return response.data
    } catch (error) {
      console.error('Error al obtener historial:', error)
      throw error
    }
  }
}

/**
 * Composable para manejo de estado de sesiones de caja
 */
export function useCashSession() {
  const currentSession = ref(null)
  const hasOpenSession = ref(false)
  const isLoading = ref(false)
  const error = ref(null)
  const sessionStats = ref(null)

  /**
   * Cargar sesión actual
   */
  const loadCurrentSession = async () => {
    try {
      isLoading.value = true
      error.value = null
      
      const response = await cashSessionService.getCurrentSession()
      
      if (response.success && response.session) {
        currentSession.value = response.session
        hasOpenSession.value = true
      } else {
        currentSession.value = null
        hasOpenSession.value = false
      }
    } catch (err) {
      error.value = err.response?.data?.message || 'Error al cargar sesión'
      currentSession.value = null
      hasOpenSession.value = false
    } finally {
      isLoading.value = false
    }
  }

  /**
   * Verificar estado de sesión
   */
  const checkSessionStatus = async () => {
    try {
      const response = await cashSessionService.checkSession()
      hasOpenSession.value = response.hasOpenSession
      return response.hasOpenSession
    } catch (err) {
      error.value = err.response?.data?.message || 'Error al verificar sesión'
      hasOpenSession.value = false
      return false
    }
  }

  /**
   * Abrir nueva sesión
   */
  const openSession = async (sessionData) => {
    try {
      isLoading.value = true
      error.value = null
      
      const response = await cashSessionService.openSession(sessionData)
      
      if (response.success) {
        currentSession.value = response.session
        hasOpenSession.value = true
        return response
      } else {
        throw new Error(response.message || 'Error al abrir sesión')
      }
    } catch (err) {
      error.value = err.response?.data?.message || err.message || 'Error al abrir sesión'
      throw err
    } finally {
      isLoading.value = false
    }
  }

  /**
   * Cerrar sesión actual
   */
  const closeSession = async (closeData) => {
    try {
      isLoading.value = true
      error.value = null
      
      const response = await cashSessionService.closeSession(closeData)
      
      if (response.success) {
        currentSession.value = response.session
        hasOpenSession.value = false
        return response
      } else {
        throw new Error(response.message || 'Error al cerrar sesión')
      }
    } catch (err) {
      error.value = err.response?.data?.message || err.message || 'Error al cerrar sesión'
      throw err
    } finally {
      isLoading.value = false
    }
  }

  /**
   * Cargar estadísticas de sesión
   */
  const loadSessionStats = async () => {
    try {
      const response = await cashSessionService.getSessionStats()
      
      if (response.success) {
        sessionStats.value = response.stats
        return response.stats
      }
    } catch (err) {
      error.value = err.response?.data?.message || 'Error al cargar estadísticas'
      throw err
    }
  }

  /**
   * Actualizar totales de sesión
   */
  const updateSessionTotals = async () => {
    try {
      const response = await cashSessionService.updateTotals()
      
      if (response.success && response.session) {
        currentSession.value = response.session
      }
      
      return response
    } catch (err) {
      error.value = err.response?.data?.message || 'Error al actualizar totales'
      throw err
    }
  }

  /**
   * Formatear monto como moneda
   */
  const formatCurrency = (amount) => {
    if (amount === null || amount === undefined) return '$0.00'
    return new Intl.NumberFormat('es-CO', {
      style: 'currency',
      currency: 'COP',
      minimumFractionDigits: 0,
      maximumFractionDigits: 0
    }).format(amount)
  }

  /**
   * Obtener estado de la diferencia (positivo, negativo, exacto)
   */
  const getDifferenceStatus = (difference) => {
    if (difference === null || difference === undefined) return 'unknown'
    if (difference > 0) return 'excess'
    if (difference < 0) return 'shortage'
    return 'exact'
  }

  /**
   * Validar si se puede procesar una venta
   */
  const canProcessSale = () => {
    return hasOpenSession.value && currentSession.value && currentSession.value.status === 'open'
  }

  return {
    // Estado
    currentSession: readonly(currentSession),
    hasOpenSession: readonly(hasOpenSession),
    isLoading: readonly(isLoading),
    error: readonly(error),
    sessionStats: readonly(sessionStats),
    
    // Métodos
    loadCurrentSession,
    checkSessionStatus,
    openSession,
    closeSession,
    loadSessionStats,
    updateSessionTotals,
    
    // Utilidades
    formatCurrency,
    getDifferenceStatus,
    canProcessSale
  }
}

export default cashSessionService