/**
 * Servicio de Facturación Electrónica con Factus (DIAN Colombia)
 * 
 * Este servicio permite:
 * - Configurar credenciales de Factus
 * - Validar facturas ante la DIAN
 * - Descargar PDF/XML de facturas validadas
 * - Consultar rangos de numeración
 */

import { apiCall, API_CONFIG, getTenantId } from './api.js'

export const factusService = {
  /**
   * Obtener estado de configuración de Factus
   */
  async getStatus() {
    try {
      const response = await apiCall('/factus/status')
      return response
    } catch (error) {
      console.error('Error obteniendo estado de Factus:', error)
      return {
        success: false,
        data: {
          enabled: false,
          configured: false,
          sandbox: true
        }
      }
    }
  },

  /**
   * Guardar configuración de Factus
   */
  async saveConfig(config) {
    try {
      const response = await apiCall('/factus/config', {
        method: 'POST',
        body: JSON.stringify(config)
      })
      return response
    } catch (error) {
      console.error('Error guardando configuración de Factus:', error)
      throw error
    }
  },

  /**
   * Probar conexión con Factus
   */
  async testConnection() {
    try {
      const response = await apiCall('/factus/test-connection', {
        method: 'POST'
      })
      return response
    } catch (error) {
      console.error('Error probando conexión con Factus:', error)
      throw error
    }
  },

  /**
   * Obtener rangos de numeración disponibles
   */
  async getNumberingRanges() {
    try {
      const response = await apiCall('/factus/numbering-ranges')
      return response
    } catch (error) {
      console.error('Error obteniendo rangos de numeración:', error)
      throw error
    }
  },

  /**
   * Validar factura ante la DIAN
   * @param {number} invoiceId - ID de la factura a validar
   * @param {boolean} sendEmail - Enviar correo al cliente
   */
  async validateInvoice(invoiceId, sendEmail = false) {
    try {
      const response = await apiCall(`/factus/invoices/${invoiceId}/validate`, {
        method: 'POST',
        body: JSON.stringify({ send_email: sendEmail })
      })
      return response
    } catch (error) {
      console.error('Error validando factura:', error)
      throw error
    }
  },

  /**
   * Descargar PDF de factura validada desde Factus
   * @param {number} invoiceId - ID de la factura
   */
  async downloadPDF(invoiceId) {
    try {
      const response = await apiCall(`/factus/invoices/${invoiceId}/pdf`)
      
      if (response.success && response.data?.pdf_base64) {
        // Decodificar Base64 y crear descarga
        const byteCharacters = atob(response.data.pdf_base64)
        const byteNumbers = new Array(byteCharacters.length)
        for (let i = 0; i < byteCharacters.length; i++) {
          byteNumbers[i] = byteCharacters.charCodeAt(i)
        }
        const byteArray = new Uint8Array(byteNumbers)
        const blob = new Blob([byteArray], { type: 'application/pdf' })
        
        // Crear enlace de descarga
        const url = URL.createObjectURL(blob)
        const link = document.createElement('a')
        link.href = url
        link.download = response.data.file_name || `factura_dian_${invoiceId}.pdf`
        document.body.appendChild(link)
        link.click()
        document.body.removeChild(link)
        URL.revokeObjectURL(url)
        
        return { success: true, message: 'PDF descargado correctamente' }
      }
      
      throw new Error(response.message || 'Error al descargar PDF')
    } catch (error) {
      console.error('Error descargando PDF de Factus:', error)
      throw error
    }
  },

  /**
   * Descargar XML de factura validada desde Factus
   * @param {number} invoiceId - ID de la factura
   */
  async downloadXML(invoiceId) {
    try {
      const response = await apiCall(`/factus/invoices/${invoiceId}/xml`)
      
      if (response.success && response.data?.xml_base64) {
        // Decodificar Base64 y crear descarga
        const byteCharacters = atob(response.data.xml_base64)
        const byteNumbers = new Array(byteCharacters.length)
        for (let i = 0; i < byteCharacters.length; i++) {
          byteNumbers[i] = byteCharacters.charCodeAt(i)
        }
        const byteArray = new Uint8Array(byteNumbers)
        const blob = new Blob([byteArray], { type: 'application/xml' })
        
        // Crear enlace de descarga
        const url = URL.createObjectURL(blob)
        const link = document.createElement('a')
        link.href = url
        link.download = response.data.file_name || `factura_dian_${invoiceId}.xml`
        document.body.appendChild(link)
        link.click()
        document.body.removeChild(link)
        URL.revokeObjectURL(url)
        
        return { success: true, message: 'XML descargado correctamente' }
      }
      
      throw new Error(response.message || 'Error al descargar XML')
    } catch (error) {
      console.error('Error descargando XML de Factus:', error)
      throw error
    }
  },

  /**
   * Obtener estado de validación de una factura
   * @param {number} invoiceId - ID de la factura
   */
  async getInvoiceStatus(invoiceId) {
    try {
      const response = await apiCall(`/factus/invoices/${invoiceId}/status`)
      return response
    } catch (error) {
      console.error('Error obteniendo estado de factura:', error)
      throw error
    }
  },

  /**
   * Listar facturas validadas ante la DIAN
   * @param {object} filters - Filtros opcionales { from_date, to_date }
   */
  async getValidatedInvoices(filters = {}) {
    try {
      const params = new URLSearchParams(filters)
      const response = await apiCall(`/factus/invoices/validated?${params.toString()}`)
      return response
    } catch (error) {
      console.error('Error obteniendo facturas validadas:', error)
      throw error
    }
  },

  /**
   * Verificar si Factus está habilitado y configurado
   */
  async isEnabled() {
    try {
      const status = await this.getStatus()
      return status.success && status.data?.enabled && status.data?.configured
    } catch (error) {
      return false
    }
  }
}

export default factusService
