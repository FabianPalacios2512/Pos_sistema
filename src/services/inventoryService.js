import api from './api.js'

export const inventoryService = {
  // Actualizar stock de producto (esto automáticamente crea el movimiento)
  updateProductStock: async (productId, stockData) => {
    try {
      const response = await api.post(`/products/${productId}/update-stock`, stockData)
      return response
    } catch (error) {
      console.error('Error updating product stock:', error)
      throw error
    }
  },

  // Crear movimiento de inventario (mapeo para compatibilidad)
  createMovement: async (movementData) => {
    try {
      // Convertir el formato del modal al formato de la API
      const apiData = {
        quantity: parseInt(movementData.quantity),
        type: movementData.type === 'entrada' ? 'purchase' : 'sale',
        reference: movementData.reason
      }

      const response = await inventoryService.updateProductStock(movementData.product_id, apiData)
      return response
    } catch (error) {
      console.error('Error en createMovement:', error)
      throw error
    }
  },

  // Ajustar stock directamente (para la función ajustar)
  adjustStock: async (productId, newStock, reason = 'Ajuste manual', warehouseId = null, variantId = null) => {
    try {
      // Construir URL con warehouse_id para obtener stock per-warehouse correcto
      const warehouseParam = warehouseId ? `?warehouse_id=${warehouseId}` : ''
      let currentStock = 0
      
      if (variantId) {
        // Para variantes, obtener stock de la variante específica (filtrado por warehouse)
        const productsResponse = await api.get(`/products/${productId}${warehouseParam}`)
        const variant = productsResponse.data.variants?.find(v => v.id === variantId)
        currentStock = variant ? (variant.stock || 0) : 0
      } else {
        // Para productos normales
        const productsResponse = await api.get(`/products/${productId}${warehouseParam}`)
        currentStock = productsResponse.data.current_stock || 0
      }

      // Calculamos la diferencia (lo que necesitamos sumar o restar)
      const difference = parseInt(newStock) - currentStock

      const apiData = {
        quantity: difference,
        type: 'adjustment',
        reference: reason,
        warehouse_id: warehouseId,
        variant_id: variantId
      }

      const response = await api.post(`/products/${productId}/update-stock`, apiData)
      return response
    } catch (error) {
      console.error('Error adjusting stock:', error)
      throw error
    }
  },

  // Obtener productos con métricas de inventario
  getProducts: async (params = {}) => {
    try {
      const queryString = new URLSearchParams(params).toString()
      const endpoint = `/inventory/products${queryString ? `?${queryString}` : ''}`
      const response = await api.get(endpoint)
      return response
    } catch (error) {
      console.error('Error fetching inventory products:', error)
      throw error
    }
  },

  // Obtener movimientos de inventario (pendiente implementar en backend)
  getMovements: async (params = {}) => {
    try {
      // Por ahora retornamos datos mock hasta implementar el endpoint
      return {
        success: true,
        data: [],
        message: 'Movimientos obtenidos exitosamente'
      }
    } catch (error) {
      console.error('Error fetching inventory movements:', error)
      throw error
    }
  },

  // Obtener movimientos por producto (pendiente implementar en backend)
  getMovementsByProduct: async (productId) => {
    try {
      // Por ahora retornamos datos mock hasta implementar el endpoint
      return {
        success: true,
        data: [],
        message: 'Movimientos del producto obtenidos exitosamente'
      }
    } catch (error) {
      console.error('Error fetching product movements:', error)
      throw error
    }
  }
}