import apiClient from './apiClient.js'

export const invoiceService = {
  // Obtener todas las facturas
  async getInvoices() {
    try {
      const response = await apiClient.get('/invoices')
      return response.data.data // El backend devuelve {success: true, data: [...]}
    } catch (error) {
      console.error('Error obteniendo facturas:', error)
      throw error
    }
  },

  // Obtener una factura específica
  async getInvoice(id) {
    try {
      const response = await apiClient.get(`/invoices/${id}`)
      return response.data.data // El backend devuelve {success: true, data: {...}}
    } catch (error) {
      console.error('Error obteniendo factura:', error)
      throw error
    }
  },

  // Actualizar factura
  async updateInvoice(id, data) {
    try {
      const response = await apiClient.put(`/invoices/${id}`, data)
      return response.data
    } catch (error) {
      console.error('Error actualizando factura:', error)
      throw error
    }
  },

  // Eliminar factura (solo admin)
  async deleteInvoice(id, adminCredentials = null) {
    try {
      const response = await apiClient.delete(`/invoices/${id}`, {
        data: adminCredentials
      })
      return response.data
    } catch (error) {
      console.error('Error eliminando factura:', error)
      throw error
    }
  },

  // Generar PDF de factura
  async generatePDF(id) {
    try {
      const response = await apiClient.post(`/invoices/generate-pdf`, {
        invoice_id: id
      }, {
        responseType: 'blob'
      })
      return response.data
    } catch (error) {
      console.error('Error generando PDF:', error)
      throw error
    }
  },

  // Obtener estadísticas de facturas
  async getStats() {
    try {
      const response = await apiClient.get('/invoices/stats')
      return response.data
    } catch (error) {
      console.error('Error obteniendo estadísticas:', error)
      throw error
    }
  }
}