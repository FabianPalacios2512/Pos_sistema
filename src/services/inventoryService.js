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
      
      console.log('Datos enviados a la API:', apiData)
      const response = await inventoryService.updateProductStock(movementData.product_id, apiData)
      console.log('Respuesta de la API:', response)
      
      return response
    } catch (error) {
      console.error('Error en createMovement:', error)
      throw error
    }
  },

  // Ajustar stock directamente (para la función ajustar)
  adjustStock: async (productId, newStock, reason = 'Ajuste manual') => {
    try {
      // Primero obtenemos el stock actual del producto
      const productsResponse = await api.get(`/products/${productId}`)
      const currentStock = productsResponse.data.current_stock || 0
      
      // Calculamos la diferencia (lo que necesitamos sumar o restar)
      const difference = parseInt(newStock) - currentStock
      
      const apiData = {
        quantity: difference, // Enviamos la diferencia, no el valor absoluto
        type: 'adjustment',
        reference: reason
      }
      
      console.log('Ajustando stock:', {
        productId,
        currentStock,
        newStock: parseInt(newStock),
        difference,
        apiData
      })
      
      const response = await api.post(`/products/${productId}/update-stock`, apiData)
      return response
    } catch (error) {
      console.error('Error adjusting stock:', error)
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