import apiClient from './apiClient.js'

class ReturnsService {
  /**
   * Obtener lista de devoluciones
   */
  async getReturns(params = {}) {
    try {
      const response = await apiClient.get('/returns', { params })
      return response.data
    } catch (error) {
      throw new Error(error.response?.data?.message || 'Error al obtener devoluciones')
    }
  }

  /**
   * Buscar factura para devolución
   */
  async searchInvoice(invoiceNumber) {
    try {
      const response = await apiClient.post('/returns/search-invoice', {
        invoice_number: invoiceNumber
      })
      return response.data
    } catch (error) {
      throw new Error(error.response?.data?.message || 'Error al buscar factura')
    }
  }

  /**
   * Crear nueva devolución
   */
  async createReturn(returnData) {
    try {
      const response = await apiClient.post('/returns', returnData)
      return response.data
    } catch (error) {
      if (error.response?.status === 400 && error.response.data?.requires_cash_session) {
        throw {
          type: 'cash_session_required',
          message: error.response.data.message
        }
      }
      throw new Error(error.response?.data?.message || 'Error al procesar devolución')
    }
  }

  /**
   * Obtener detalles de una devolución específica
   */
  async getReturn(id) {
    try {
      const response = await apiClient.get(`/returns/${id}`)
      return response.data
    } catch (error) {
      throw new Error(error.response?.data?.message || 'Error al obtener devolución')
    }
  }

  /**
   * Obtener detalles completos de una devolución (alias para compatibilidad)
   */
  async getReturnDetails(id) {
    return this.getReturn(id)
  }

  /**
   * Cancelar devolución
   */
  async cancelReturn(id) {
    try {
      const response = await apiClient.put(`/returns/${id}/cancel`)
      return response.data
    } catch (error) {
      throw new Error(error.response?.data?.message || 'Error al cancelar devolución')
    }
  }

  /**
   * Validar si se puede procesar una devolución
   */
  validateReturnItems(items, originalInvoice) {
    const errors = []

    // Validar que originalInvoice y sus items existan
    if (!originalInvoice || !originalInvoice.invoice_items) {
      errors.push('No se encontraron items en la factura original')
      return {
        isValid: false,
        errors
      }
    }

    items.forEach(item => {
      const originalItem = originalInvoice.invoice_items.find(i => i.product_id === item.product_id)
      
      if (!originalItem) {
        errors.push(`Producto ${item.product_id} no encontrado en factura original`)
        return
      }

      if (item.quantity > (originalItem.available_for_return || originalItem.quantity)) {
        errors.push(`No se pueden devolver ${item.quantity} unidades de ${originalItem.product?.name || 'producto'}. Solo ${originalItem.available_for_return || originalItem.quantity} disponibles`)
      }

      if (item.quantity <= 0) {
        errors.push(`Cantidad debe ser mayor a 0 para ${originalItem.product?.name || 'producto'}`)
      }
    })

    return {
      isValid: errors.length === 0,
      errors
    }
  }

  /**
   * Calcular totales de devolución
   */
  calculateReturnTotals(items, originalInvoice) {
    let subtotal = 0
    let taxAmount = 0

    // Validar que los parámetros existan
    if (!items || !originalInvoice || !originalInvoice.invoice_items) {
      return {
        subtotal: 0,
        taxAmount: 0,
        total: 0
      }
    }

    items.forEach(item => {
      // Buscar en invoice_items (que es la relación correcta)
      const originalItem = originalInvoice.invoice_items.find(i => i.product_id === item.product_id)
      if (originalItem && originalItem.unit_price) {
        const itemSubtotal = (item.quantity || 0) * originalItem.unit_price
        subtotal += itemSubtotal
      }
    })

    // Calcular IVA proporcional basado en los totales de la factura
    // Esto funciona incluso si los items no tienen tax_amount
    const invoiceSubtotal = parseFloat(originalInvoice.subtotal || 0)
    const invoiceTaxAmount = parseFloat(originalInvoice.tax_amount || 0)
    
    if (invoiceSubtotal > 0 && subtotal > 0) {
      // IVA proporcional: (subtotal devuelto / subtotal factura) * IVA total factura
      taxAmount = (subtotal / invoiceSubtotal) * invoiceTaxAmount
    }

    const total = subtotal + taxAmount

    return {
      subtotal: Number(subtotal.toFixed(2)),
      taxAmount: Number(taxAmount.toFixed(2)),
      total: Number(total.toFixed(2))
    }
  }

  /**
   * Formatear datos para crear devolución
   */
  formatReturnData(invoiceData, selectedItems, returnInfo) {
    return {
      original_invoice_id: invoiceData.invoice.id,
      reason: returnInfo.reason,
      refund_method: returnInfo.refundMethod,
      notes: returnInfo.notes || '',
      items: selectedItems.map(item => ({
        product_id: item.product_id,
        quantity: item.quantity,
        reason: item.reason || ''
      }))
    }
  }
}

export const returnsService = new ReturnsService()
export default returnsService