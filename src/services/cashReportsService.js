import apiClient from './apiClient.js'

export const cashReportsService = {
  
  // Obtener métricas generales de caja en tiempo real
  async getCashMetrics(period = 'today', customDate = null, customEndDate = null) {
    try {
      const params = { period }
      if (period === 'custom' && customDate) {
        params.custom_date = customDate
        if (customEndDate) params.custom_end_date = customEndDate
      }
      
      const response = await apiClient.get('/cash-reports/dashboard', { params })
      return response.data
    } catch (error) {
      console.error('Error obteniendo métricas de caja:', error)
      return { success: false, message: 'Error al cargar métricas de caja' }
    }
  },

  // Obtener comparativa detallada entre cajeros
  async getCashierComparison(period = 'today', customDate = null, customEndDate = null) {
    try {
      const params = { period }
      if (period === 'custom' && customDate) {
        params.custom_date = customDate
        if (customEndDate) params.custom_end_date = customEndDate
      }
      
      const response = await apiClient.get('/cash-reports/cashiers', { params })
      return response.data
    } catch (error) {
      console.error('Error obteniendo comparativa de cajeros:', error)
      return { success: false, message: 'Error al cargar comparativa de cajeros' }
    }
  },

  // Obtener top mejores sesiones
  async getTopSessions(period = 'week', customDate = null, customEndDate = null, limit = 5) {
    try {
      const params = { period, limit }
      if (period === 'custom' && customDate) {
        params.custom_date = customDate
        if (customEndDate) params.custom_end_date = customEndDate
      }
      
      const response = await apiClient.get('/cash-reports/top-sessions', { params })
      return response.data
    } catch (error) {
      console.error('Error obteniendo top sesiones:', error)
      return { success: false, message: 'Error al cargar mejores sesiones' }
    }
  },

  // Obtener análisis de eficiencia por hora
  async getHourlyEfficiency(period = 'today', customDate = null, customEndDate = null, cashierId = null) {
    try {
      const params = { period }
      if (period === 'custom' && customDate) {
        params.custom_date = customDate
        if (customEndDate) params.custom_end_date = customEndDate
      }
      if (cashierId) params.cashier_id = cashierId
      
      const response = await apiClient.get('/cash-reports/hourly', { params })
      return response.data
    } catch (error) {
      console.error('Error obteniendo eficiencia por hora:', error)
      return { success: false, message: 'Error al cargar eficiencia por hora' }
    }
  },

  // Obtener distribución de métodos de pago
  async getPaymentMethods(period = 'today', customDate = null, customEndDate = null) {
    try {
      const params = { period }
      if (period === 'custom' && customDate) {
        params.custom_date = customDate
        if (customEndDate) params.custom_end_date = customEndDate
      }
      
      const response = await apiClient.get('/cash-reports/payment-methods', { params })
      return response.data
    } catch (error) {
      console.error('Error obteniendo métodos de pago:', error)
      return { success: false, message: 'Error al cargar métodos de pago' }
    }
  },

  // Obtener alertas y recomendaciones automáticas
  async getCashAlerts(period = 'today') {
    try {
      const response = await apiClient.get('/cash-reports/alerts', {
        params: { period }
      })
      return response.data
    } catch (error) {
      console.error('Error obteniendo alertas de caja:', error)
      return { success: false, message: 'Error al cargar alertas' }
    }
  },

  // Obtener resumen general de la caja
  async getCashSummary() {
    try {
      const response = await apiClient.get('/cash-reports/summary')
      return response.data
    } catch (error) {
      console.error('Error obteniendo resumen de caja:', error)
      return { success: false, message: 'Error al cargar resumen de caja' }
    }
  },

  // Obtener movimientos de dinero detallados
  async getCashMovements(cashSessionId = null, period = 'today') {
    try {
      const params = { period }
      if (cashSessionId) params.session_id = cashSessionId
      
      const response = await apiClient.get('/cash-reports/movements', { params })
      return response.data
    } catch (error) {
      console.error('Error obteniendo movimientos de caja:', error)
      return { success: false, message: 'Error al cargar movimientos de caja' }
    }
  },

  // NUEVA FUNCIÓN: Obtener datos estructurados para gráficos de tendencia
  async getTrendChartData(period = 'week') {
    try {
      const params = { period }
      
      const response = await apiClient.get('/cash-reports/trend-chart', { params })
      return response.data
    } catch (error) {
      console.error('Error obteniendo datos de tendencia:', error)
      return { success: false, message: 'Error al cargar datos de tendencia' }
    }
  }

}
