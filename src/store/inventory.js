import { ref } from 'vue'
import apiClient from '../services/apiClient'

// Estado global usando Composition API
const overview = ref(null)
const products = ref(null)
const movements = ref(null)
const customers = ref(null)
const suppliers = ref(null)
const alerts = ref(null)
const predictions = ref(null)
const loading = ref(false)
const error = ref(null)

// Servicios del inventario inteligente
export const inventoryStore = {
  // Estado
  overview,
  products,
  movements,
  customers,
  suppliers,
  alerts,
  predictions,
  loading,
  error,

  // Métodos para las APIs
  
  /**
   * 1. VISTA GENERAL DEL INVENTARIO
   */
  async getOverview() {
    try {
      loading.value = true
      error.value = null
      
      const response = await apiClient.get('/inventory/overview')
      overview.value = response.data.data
      
      return response.data
    } catch (err) {
      error.value = err.response?.data?.message || 'Error al obtener vista general'
      throw err
    } finally {
      loading.value = false
    }
  },

  /**
   * 2. VISTA DE PRODUCTOS
   */
  async getProducts(filters = {}) {
    try {
      loading.value = true
      error.value = null
      
      const params = new URLSearchParams()
      Object.entries(filters).forEach(([key, value]) => {
        if (value !== null && value !== undefined && value !== '') {
          params.append(key, value)
        }
      })
      
      const response = await apiClient.get(`/inventory/products?${params.toString()}`)
      products.value = response.data.data
      
      return response.data
    } catch (err) {
      error.value = err.response?.data?.message || 'Error al obtener productos'
      throw err
    } finally {
      loading.value = false
    }
  },

  /**
   * 3. MOVIMIENTOS DE INVENTARIO
   */
  async getMovements(filters = {}) {
    try {
      loading.value = true
      error.value = null
      
      const params = new URLSearchParams()
      Object.entries(filters).forEach(([key, value]) => {
        if (value !== null && value !== undefined && value !== '') {
          params.append(key, value)
        }
      })
      
      const response = await apiClient.get(`/inventory/movements?${params.toString()}`)
      movements.value = response.data.data
      
      return response.data
    } catch (err) {
      error.value = err.response?.data?.message || 'Error al obtener movimientos'
      throw err
    } finally {
      loading.value = false
    }
  },

  /**
   * Registrar nuevo movimiento de inventario
   */
  async recordMovement(movementData) {
    try {
      loading.value = true
      error.value = null
      
      const response = await apiClient.post('/inventory/movements', movementData)
      
      // Actualizar estado local si es necesario
      if (movements.value) {
        // Recargar movimientos para mostrar el nuevo registro
        await this.getMovements()
      }
      
      return response.data
    } catch (err) {
      error.value = err.response?.data?.message || 'Error al registrar movimiento'
      throw err
    } finally {
      loading.value = false
    }
  },

  /**
   * 4. VISTA DE CLIENTES
   */
  async getCustomers(filters = {}) {
    try {
      loading.value = true
      error.value = null
      
      const params = new URLSearchParams()
      Object.entries(filters).forEach(([key, value]) => {
        if (value !== null && value !== undefined && value !== '') {
          params.append(key, value)
        }
      })
      
      const response = await apiClient.get(`/inventory/customers?${params.toString()}`)
      customers.value = response.data.data
      
      return response.data
    } catch (err) {
      error.value = err.response?.data?.message || 'Error al obtener análisis de clientes'
      throw err
    } finally {
      loading.value = false
    }
  },

  /**
   * 5. PROVEEDORES
   */
  async getSuppliers(filters = {}) {
    try {
      loading.value = true
      error.value = null
      
      const params = new URLSearchParams()
      Object.entries(filters).forEach(([key, value]) => {
        if (value !== null && value !== undefined && value !== '') {
          params.append(key, value)
        }
      })
      
      const response = await apiClient.get(`/inventory/suppliers?${params.toString()}`)
      suppliers.value = response.data.data
      
      return response.data
    } catch (err) {
      error.value = err.response?.data?.message || 'Error al obtener análisis de proveedores'
      throw err
    } finally {
      loading.value = false
    }
  },

  /**
   * 6. SISTEMA DE ALERTAS
   */
  async getAlerts() {
    try {
      loading.value = true
      error.value = null
      
      const response = await apiClient.get('/inventory/alerts')
      alerts.value = response.data.data
      
      return response.data
    } catch (err) {
      error.value = err.response?.data?.message || 'Error al obtener alertas'
      throw err
    } finally {
      loading.value = false
    }
  },

  /**
   * 7. PREDICCIONES
   */
  async getPredictions(options = {}) {
    try {
      loading.value = true
      error.value = null
      
      const params = new URLSearchParams()
      Object.entries(options).forEach(([key, value]) => {
        if (value !== null && value !== undefined && value !== '') {
          params.append(key, value)
        }
      })
      
      const response = await apiClient.get(`/inventory/predictions?${params.toString()}`)
      predictions.value = response.data.data
      
      return response.data
    } catch (err) {
      error.value = err.response?.data?.message || 'Error al obtener predicciones'
      throw err
    } finally {
      loading.value = false
    }
  },

  /**
   * MÉTODOS AUXILIARES
   */
  
  // Limpiar estado
  clearState() {
    overview.value = null
    products.value = null
    movements.value = null
    customers.value = null
    suppliers.value = null
    alerts.value = null
    predictions.value = null
    error.value = null
  },

  // Actualizar datos de una sección específica
  async refreshSection(section, filters = {}) {
    switch (section) {
      case 'overview':
        return await this.getOverview()
      case 'products':
        return await this.getProducts(filters)
      case 'movements':
        return await this.getMovements(filters)
      case 'customers':
        return await this.getCustomers(filters)
      case 'suppliers':
        return await this.getSuppliers(filters)
      case 'alerts':
        return await this.getAlerts()
      case 'predictions':
        return await this.getPredictions(filters)
      default:
        throw new Error(`Sección desconocida: ${section}`)
    }
  },

  // Obtener resumen de alertas (para indicadores en la interfaz)
  getAlertsSummary() {
    if (!alerts.value) return { total: 0, high: 0, medium: 0, low: 0 }
    
    return alerts.value.summary || { total: 0, high: 0, medium: 0, low: 0 }
  }
}