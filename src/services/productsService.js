import { apiCall } from './api.js'

// Products API Service
export const productsService = {
  // Obtener todos los productos
  async getAll(params = {}) {
    const queryString = new URLSearchParams(params).toString()
    const endpoint = `/products${queryString ? `?${queryString}` : ''}`
    return await apiCall(endpoint)
  },

  // Obtener productos optimizados para POS (sin paginación, campos mínimos)
  async getForPos() {
    return await apiCall('/products-pos')
  },

  // Obtener un producto específico
  async getById(id) {
    return await apiCall(`/products/${id}`)
  },

  // Crear nuevo producto
  async create(productData) {
    return await apiCall('/products', {
      method: 'POST',
      body: JSON.stringify(productData)
    })
  },

  // Actualizar producto
  async update(id, productData) {
    return await apiCall(`/products/${id}`, {
      method: 'PUT',
      body: JSON.stringify(productData)
    })
  },

  // Eliminar producto
  async delete(id) {
    return await apiCall(`/products/${id}`, {
      method: 'DELETE'
    })
  },

  // Obtener productos con stock bajo
  async getLowStock() {
    return await apiCall('/products/low-stock')
  },

  // Actualizar stock de producto
  async updateStock(id, stockData) {
    return await apiCall(`/products/${id}/update-stock`, {
      method: 'POST',
      body: JSON.stringify(stockData)
    })
  }
}

// Categories API Service
export const categoriesService = {
  // Obtener todas las categorías
  async getAll() {
    return await apiCall('/categories')
  },

  // Obtener una categoría específica
  async getById(id) {
    return await apiCall(`/categories/${id}`)
  },

  // Crear nueva categoría
  async create(categoryData) {
    return await apiCall('/categories', {
      method: 'POST',
      body: JSON.stringify(categoryData)
    })
  },

  // Actualizar categoría
  async update(id, categoryData) {
    return await apiCall(`/categories/${id}`, {
      method: 'PUT',
      body: JSON.stringify(categoryData)
    })
  },

  // Eliminar categoría
  async delete(id) {
    return await apiCall(`/categories/${id}`, {
      method: 'DELETE'
    })
  }
}

// Auth API Service
export const authService = {
  // Login
  async login(credentials) {
    return await apiCall('/login', {
      method: 'POST',
      body: JSON.stringify(credentials)
    })
  },

  // Logout
  async logout() {
    return await apiCall('/logout', {
      method: 'POST'
    })
  },

  // Obtener usuario actual
  async getCurrentUser() {
    return await apiCall('/me')
  }
}

// Dashboard API Service
export const dashboardService = {
  // Obtener estadísticas optimizadas
  async getStats() {
    return await apiCall('/optimized/dashboard')
  },
  
  // Obtener métricas principales
  async getMetrics() {
    return await apiCall('/optimized/metrics')
  },
  
  // Obtener transacciones recientes
  async getRecentTransactions(limit = 10) {
    return await apiCall(`/optimized/recent-transactions?limit=${limit}`)
  },
  
  // Limpiar cache del dashboard
  async clearCache() {
    return await apiCall('/optimized/clear-cache', 'POST')
  },
  
  // Estado del cache
  async getCacheStatus() {
    return await apiCall('/optimized/cache-status')
  }
}