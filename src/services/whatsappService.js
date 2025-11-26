import apiClient from './apiClient.js'
import axios from 'axios'

export const whatsappService = {
  // Obtener estado de conexión de WhatsApp
  async getStatus() {
    try {
      // Llamar directamente al servidor WhatsApp para evitar timeout
      const response = await axios.get('http://localhost:3002/status', { timeout: 3000 })
      return {
        success: true,
        status: response.data
      }
    } catch (error) {
      // Error silencioso - no mostrar en consola para evitar spam
      return {
        success: false,
        status: { connected: false }
      }
    }
  },

  // Obtener código QR para autenticación
  async getQRCode() {
    try {
      const response = await apiClient.get('/whatsapp/qr')
      return response.data
    } catch (error) {
      console.error('Error obteniendo QR code:', error)
      return {
        success: false,
        message: error.message
      }
    }
  },

  // Inicializar servicio WhatsApp
  async initialize() {
    try {
      const response = await apiClient.post('/whatsapp/initialize')
      return response.data
    } catch (error) {
      console.error('Error inicializando WhatsApp:', error)
      return {
        success: false,
        message: error.message
      }
    }
  },

  // Desconectar WhatsApp
  async disconnect() {
    try {
      const response = await apiClient.post('/whatsapp/disconnect')
      return response.data
    } catch (error) {
      console.error('Error desconectando WhatsApp:', error)
      return {
        success: false,
        message: error.message
      }
    }
  },

  // Limpiar sesión de WhatsApp
  async cleanSession() {
    try {
      const response = await apiClient.post('/whatsapp/clean-session')
      return response.data
    } catch (error) {
      console.error('Error limpiando sesión de WhatsApp:', error)
      return {
        success: false,
        message: error.message
      }
    }
  },

  // Enviar factura por WhatsApp (usando PDF generado)
  async sendInvoiceWithPDF(phone, pdfBlob, message, fileName, customerName = '') {
    try {
      const formData = new FormData()
      formData.append('phone', phone)
      formData.append('message', message)
      formData.append('customerName', customerName)
      formData.append('pdf', pdfBlob, fileName)

      const response = await apiClient.post('/whatsapp/send-pdf', formData, {
        headers: {
          'Content-Type': 'multipart/form-data'
        },
        timeout: 60000 // 60 segundos para envío de PDF por WhatsApp
      })
      return response.data
    } catch (error) {
      console.error('Error enviando PDF por WhatsApp:', error)
      return {
        success: false,
        message: error.message
      }
    }
  },

  // Enviar factura por WhatsApp (usando ID de factura - método anterior)
  async sendInvoice(invoiceId, phone, customerName = '') {
    try {
      const response = await apiClient.post('/whatsapp/send-invoice', {
        invoice_id: invoiceId,
        phone: phone,
        customer_name: customerName
      })
      return response.data
    } catch (error) {
      console.error('Error enviando factura por WhatsApp:', error)
      return {
        success: false,
        message: error.message
      }
    }
  },

  // Enviar cotización por WhatsApp (usando PDF generado)
  async sendQuotationWithPDF(phone, pdfBlob, message, fileName, customerName = '') {
    try {
      console.log('📤 Preparando envío de cotización por WhatsApp:', {
        phone,
        fileName,
        customerName,
        pdfSize: pdfBlob.size,
        messageLength: message.length
      })

      const formData = new FormData()
      formData.append('phone', phone)
      formData.append('message', message)
      formData.append('customerName', customerName)
      formData.append('pdf', pdfBlob, fileName)

      const response = await apiClient.post('/whatsapp/send-quotation-pdf', formData, {
        headers: {
          'Content-Type': 'multipart/form-data'
        },
        timeout: 60000 // 60 segundos para envío de PDF por WhatsApp
      })
      return response.data
    } catch (error) {
      console.error('Error enviando cotización PDF por WhatsApp:', error)
      
      // Obtener detalles del error de validación
      let errorMessage = error.message
      if (error.response?.data?.message) {
        errorMessage = error.response.data.message
      } else if (error.response?.data?.error) {
        errorMessage = error.response.data.error
      }
      
      console.error('Detalles del error 422:', {
        status: error.response?.status,
        data: error.response?.data,
        headers: error.response?.headers
      })
      
      return {
        success: false,
        message: errorMessage,
        details: error.response?.data
      }
    }
  },

  // Función simplificada para enviar cotización desde cualquier vista
  async sendQuotationFromData(quotationData, pdfBlob) {
    try {
      console.log('📤 Enviando cotización desde datos:', quotationData)
      
      // Extraer información necesaria
      let phone = quotationData.customer_phone
      
      // Validar y formatear el número de teléfono
      if (phone) {
        // Limpiar el número (quitar espacios, guiones, etc.)
        phone = phone.replace(/[\s\-\(\)]/g, '')
        
        // Agregar prefijo +57 si no lo tiene
        if (!phone.startsWith('+57')) {
          if (phone.startsWith('57')) {
            phone = '+' + phone
          } else if (phone.startsWith('3')) {
            phone = '+57' + phone
          } else {
            // Si el número no es válido, usar número por defecto
            phone = '+573134540533' // Número por defecto 
          }
        }
        
        // Validar formato final
        if (!/^\+57[3][0-9]{9}$/.test(phone)) {
          console.warn('⚠️ Número de teléfono inválido:', phone, 'usando número por defecto')
          phone = '+573134540533'
        }
      } else {
        // Si no hay teléfono del cliente, usar número por defecto
        console.warn('⚠️ No se encontró teléfono del cliente, usando número por defecto')
        phone = '+573134540533'
      }
      
      console.log('📱 Teléfono a usar:', phone)
      
      const customerName = quotationData.customer || 'Cliente'
      const fileName = `cotizacion-${quotationData.code}.pdf`
      const message = `Hola ${customerName}, te enviamos tu cotización ${quotationData.code} por un total de $${quotationData.total?.toLocaleString() || 0}. ¡Gracias por tu preferencia!`
      
      // Usar la función existente
      return await this.sendQuotationWithPDF(phone, pdfBlob, message, fileName, customerName)
      
    } catch (error) {
      console.error('Error en sendQuotationFromData:', error)
      return {
        success: false,
        message: error.message || 'Error al enviar cotización'
      }
    }
  },

  // Enviar cotización por WhatsApp (usando ID de cotización)
  async sendQuotation(quotationId, phone, customerName = '') {
    try {
      const requestData = {
        quotation_id: quotationId,
        phone: phone,
        customer_name: customerName
      }
      
      console.log('📡 Enviando request a API:', requestData)
      
      const response = await apiClient.post('/whatsapp/send-quotation', requestData)
      
      console.log('📡 Response de API:', response.data)
      
      return response.data
    } catch (error) {
      console.error('Error enviando cotización por WhatsApp:', error)
      
      return {
        success: false,
        message: error.response?.data?.message || error.message
      }
    }
  }
}