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
  async getForPos(params = {}) {
    // Agregar timestamp para evitar caché del navegador
    const cacheBuster = Date.now()
    const allParams = { ...params, _t: cacheBuster }
    const queryString = new URLSearchParams(allParams).toString()
    const url = `/products-pos?${queryString}`
    return await apiCall(url)
  },

  // Obtener un producto específico
  async getById(id) {
    return await apiCall(`/products/${id}`)
  },

  // Crear nuevo producto
  async create(productData) {
    const isFormData = productData instanceof FormData
    return await apiCall('/products', {
      method: 'POST',
      body: isFormData ? productData : JSON.stringify(productData)
    })
  },

  // Actualizar producto
  async update(id, productData) {
    // Si es FormData (tiene imágenes), usar POST con _method
    if (productData instanceof FormData) {
      return await apiCall(`/products/${id}`, {
        method: 'POST',
        body: productData
        // NO enviar headers: {}, dejar que apiCall maneje los headers de autenticación
      })
    }
    
    // Si es objeto normal, usar PUT con JSON
    return await apiCall(`/products/${id}`, {
      method: 'PUT',
      body: JSON.stringify(productData)
    })
  },

  // Eliminar producto
  async delete(id, reason = null) {
    return await apiCall(`/products/${id}`, {
      method: 'DELETE',
      body: JSON.stringify({ reason })
    })
  },

  // Obtener productos eliminados (papelera)
  async getTrash() {
    return await apiCall('/products/trash')
  },

  // Restaurar producto eliminado
  async restore(id) {
    return await apiCall(`/products/${id}/restore`, {
      method: 'POST'
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
  },

  // Eliminar imagen de un producto
  async deleteImage(id) {
    return await apiCall(`/products/${id}/delete-image`, {
      method: 'DELETE'
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