// Servicio optimizado para el dashboard empresarial
import { apiCall } from './apiService'

export const optimizedDashboardService = {
  /**
   * Obtener todos los datos del dashboard optimizados con cache
   * @returns {Promise<Object>} Datos completos del dashboard
   */
  async getDashboardData() {
    try {
      const response = await apiCall('/optimized/dashboard')
      
      if (response.success) {
        return {
          success: true,
          data: {
            // Métricas principales para las tarjetas del dashboard
            metrics: {
              todaySales: {
                amount: response.data.metrics?.ingresos_hoy || 0,
                count: response.data.metrics?.ventas_hoy || 0
              },
              totalTransactions: response.data.metrics?.total_transacciones || 0,
              averageSale: response.data.metrics?.promedio_venta || 0,
              productsOutOfStock: response.data.metrics?.productos_agotados || 0,
              activeProducts: response.data.metrics?.active_products || 0,
              activeCustomers: response.data.metrics?.active_customers || 0,
              openSessions: response.data.metrics?.open_sessions || 0
            },
            
            // Productos más vendidos
            topProducts: response.data.top_products || [],
            
            // Sesiones de caja activas
            activeSessions: response.data.active_sessions || [],
            
            // Información del cache
            cacheInfo: response.data.cache_info || null
          }
        }
      }
      
      return { success: false, error: 'No data received' }
    } catch (error) {
      console.error('Error fetching optimized dashboard data:', error)
      return { 
        success: false, 
        error: error.message,
        // Datos fallback en caso de error
        data: {
          metrics: {
            todaySales: { amount: 0, count: 0 },
            totalTransactions: 0,
            averageSale: 0,
            productsOutOfStock: 0,
            activeProducts: 0,
            activeCustomers: 0,
            openSessions: 0
          },
          topProducts: [],
          activeSessions: [],
          cacheInfo: null
        }
      }
    }
  },

  /**
   * Obtener solo las métricas principales
   * @returns {Promise<Object>} Métricas del dashboard
   */
  async getMainMetrics() {
    try {
      const response = await apiCall('/optimized/metrics')
      return response
    } catch (error) {
      console.error('Error fetching metrics:', error)
      return { success: false, error: error.message }
    }
  },

  /**
   * Obtener transacciones recientes
   * @param {number} limit - Número de transacciones a obtener
   * @returns {Promise<Object>} Transacciones recientes
   */
  async getRecentTransactions(limit = 10) {
    try {
      const response = await apiCall(`/optimized/recent-transactions?limit=${limit}`)
      return response
    } catch (error) {
      console.error('Error fetching recent transactions:', error)
      return { success: false, error: error.message, data: [] }
    }
  },

  /**
   * Limpiar el cache del dashboard
   * @returns {Promise<Object>} Resultado de la limpieza
   */
  async clearCache() {
    try {
      const response = await apiCall('/optimized/clear-cache', 'POST')
      return response
    } catch (error) {
      console.error('Error clearing cache:', error)
      return { success: false, error: error.message }
    }
  },

  /**
   * Obtener el estado actual del cache
   * @returns {Promise<Object>} Estado del cache
   */
  async getCacheStatus() {
    try {
      const response = await apiCall('/optimized/cache-status')
      return response
    } catch (error) {
      console.error('Error fetching cache status:', error)
      return { success: false, error: error.message }
    }
  },

  /**
   * Comparar rendimiento entre endpoint normal y optimizado
   * @returns {Promise<Object>} Comparación de tiempos
   */
  async benchmarkPerformance() {
    try {
      const startOptimized = performance.now()
      const optimizedResponse = await apiCall('/optimized/dashboard')
      const optimizedTime = performance.now() - startOptimized

      const startRegular = performance.now()
      const regularResponse = await apiCall('/dashboard')
      const regularTime = performance.now() - startRegular

      return {
        success: true,
        benchmark: {
          optimized: {
            time: optimizedTime,
            cached: optimizedResponse.data?.cache_info ? true : false,
            size: JSON.stringify(optimizedResponse).length
          },
          regular: {
            time: regularTime,
            size: JSON.stringify(regularResponse).length
          },
          improvement: {
            timeReduction: ((regularTime - optimizedTime) / regularTime * 100).toFixed(2),
            speedupFactor: (regularTime / optimizedTime).toFixed(2)
          }
        }
      }
    } catch (error) {
      console.error('Error benchmarking performance:', error)
      return { success: false, error: error.message }
    }
  }
}

export default optimizedDashboardService