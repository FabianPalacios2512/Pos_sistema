import apiClient from './apiClient.js'
import axios from 'axios'

// Version: 3.0 - Multi-tenant WhatsApp Service con detección automática de contexto
/**
 * Helper para detectar tenant_id y crear cliente apropiado
 * SIEMPRE usa modo directo (puerto 3002) cuando hay un tenant_id identificable
 */
const getWhatsAppClient = () => {
  let tenantId = null
  
  // 1. Intentar desde localStorage (onboarding)
  const registrationData = localStorage.getItem('registration_data')
  if (registrationData) {
    const data = JSON.parse(registrationData)
    tenantId = data.subdomain
  }
  
  // 2. Extraer del hostname (tenant.localhost:3000 → tenant)
  if (!tenantId && window.location.hostname !== 'localhost' && window.location.hostname.includes('.')) {
    tenantId = window.location.hostname.split('.')[0]
  }
  
  // 3. Extraer de query params (?subdomain=XXX o ?tenant=XXX)
  if (!tenantId) {
    const urlParams = new URLSearchParams(window.location.search)
    tenantId = urlParams.get('subdomain') || urlParams.get('tenant')
  }
  
  // 4. Fallback: extraer del pathname (ej: /onboarding/tenant-name)
  if (!tenantId) {
    const pathParts = window.location.pathname.split('/').filter(p => p)
    const onboardingIndex = pathParts.indexOf('onboarding')
    if (onboardingIndex !== -1 && pathParts[onboardingIndex + 1]) {
      tenantId = pathParts[onboardingIndex + 1]
    }
  }
  
  // Si tenemos tenant_id: usar /api/whatsapp/ (proxy Nginx)
  if (tenantId) {
    // Normalizar tenant_id: reemplazar guiones con guiones bajos
    // Los subdomains usan guiones pero la DB usa guiones bajos
    const normalizedTenantId = tenantId.replace(/-/g, '_')
    
    // En producción usar /api/whatsapp/ (proxy Nginx)
    // En desarrollo usar puerto 3002 directo
    const isLocalhost = window.location.hostname === 'localhost' || 
                       window.location.hostname === '127.0.0.1' ||
                       window.location.hostname.includes('.localhost')
    
    const whatsappBaseURL = import.meta.env.VITE_WHATSAPP_URL || 
                           (isLocalhost
                             ? 'http://localhost:3002' 
                             : `https://${tenantId}.105pos.pro/api/whatsapp`)
    
    const client = axios.create({
      baseURL: whatsappBaseURL,
      headers: {
        'X-Tenant-Id': normalizedTenantId
      },
      // Agregar timeout para evitar que el error tarde mucho
      timeout: 10000
    })
    
    // Interceptor para silenciar errores de CORS en consola
    client.interceptors.response.use(
      response => response,
      error => {
        // Silenciar errores de red/CORS en la consola del navegador
        if (error.code === 'ERR_NETWORK' || error.message.includes('Network Error')) {
          // No loggear nada - servicio WhatsApp no disponible es esperado en producción
          return Promise.reject({ silent: true, originalError: error })
        }
        return Promise.reject(error)
      }
    )
    
    return {
      isDirect: true,
      client: client
    }
  }
  
  // Sin tenant_id: usar apiClient (raro, solo para super admin sin contexto)
  return {
    isDirect: false,
    client: apiClient
  }
}

export const whatsappService = {
  // Obtener estado de conexión de WhatsApp
  async getStatus() {
    try {
      const { isDirect, client } = getWhatsAppClient()
      const endpoint = isDirect ? '/status' : '/whatsapp/status'
      
      const response = await client.get(endpoint)
      return {
        success: true,
        status: isDirect ? response.data.status : response.data.status
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
      const { isDirect, client } = getWhatsAppClient()
      const endpoint = isDirect ? '/qr' : '/whatsapp/qr'
      
      const response = await client.get(endpoint)
      return response.data
    } catch (error) {
      // Error silencioso - solo loguear en modo desarrollo
      if (import.meta.env.DEV) {
        console.warn('WhatsApp QR no disponible:', error.message)
      }
      return {
        success: false,
        message: error.message
      }
    }
  },

  // Inicializar servicio WhatsApp
  async initialize() {
    try {
      const { isDirect, client } = getWhatsAppClient()
      const endpoint = isDirect ? '/initialize' : '/whatsapp/initialize'
      
      const response = await client.post(endpoint)
      return response.data
    } catch (error) {
      // Error silencioso - solo loguear en modo desarrollo
      if (import.meta.env.DEV) {
        console.warn('WhatsApp initialize falló:', error.message)
      }
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
      const { isDirect, client } = getWhatsAppClient()
      
      if (isDirect) {
        // Modo directo: Convertir PDF Blob a base64 y enviar al servidor Node.js
        const reader = new FileReader()
        const base64Promise = new Promise((resolve, reject) => {
          reader.onloadend = () => resolve(reader.result)
          reader.onerror = reject
          reader.readAsDataURL(pdfBlob)
        })
        
        const base64Data = await base64Promise
        
        // Enviar al servidor multi-tenant
        const response = await client.post('/send', {
          phone: phone,
          message: message,
          pdfBase64: base64Data,
          fileName: fileName,
          customerName: customerName
        })
        
        return response.data
      } else {
        // Modo Laravel (fallback antiguo)
        const formData = new FormData()
        formData.append('phone', phone)
        formData.append('message', message)
        formData.append('customerName', customerName)
        formData.append('pdf', pdfBlob, fileName)

        const response = await client.post('/whatsapp/send-pdf', formData, {
          headers: {
            'Content-Type': 'multipart/form-data'
          },
          timeout: 60000
        })
        return response.data
      }
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

      const { isDirect, client } = getWhatsAppClient()
      
      if (isDirect) {
        // Modo directo: Convertir PDF Blob a base64 y enviar al servidor Node.js
        const reader = new FileReader()
        const base64Promise = new Promise((resolve, reject) => {
          reader.onloadend = () => resolve(reader.result)
          reader.onerror = reject
          reader.readAsDataURL(pdfBlob)
        })
        
        const base64Data = await base64Promise
        
        // Enviar al servidor multi-tenant
        const response = await client.post('/send', {
          phone: phone,
          message: message,
          pdfBase64: base64Data,
          fileName: fileName,
          customerName: customerName
        })
        
        return response.data
      } else {
        // Modo Laravel (fallback antiguo)
        const formData = new FormData()
        formData.append('phone', phone)
        formData.append('message', message)
        formData.append('customerName', customerName)
        formData.append('pdf', pdfBlob, fileName)

        const response = await client.post('/whatsapp/send-quotation-pdf', formData, {
          headers: {
            'Content-Type': 'multipart/form-data'
          },
          timeout: 60000
        })
        return response.data
      }
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
  },

  // Función genérica para enviar documentos por WhatsApp (facturas, cotizaciones, órdenes de compra)
  async sendDocumentByWhatsApp(phone, pdfBlob, documentNumber, documentType = 'invoice', customerName = '') {
    try {
      // Validar y formatear el número de teléfono
      let formattedPhone = phone.replace(/[\s\-\(\)]/g, '')
      
      if (!formattedPhone.startsWith('+57')) {
        if (formattedPhone.startsWith('57')) {
          formattedPhone = '+' + formattedPhone
        } else if (formattedPhone.startsWith('3')) {
          formattedPhone = '+57' + formattedPhone
        }
      }

      // Usar nombre del cliente si está disponible, sino "Cliente"
      const clientName = customerName || 'Cliente'
      
      // Generar mensaje según el tipo de documento con nombre del cliente
      const messages = {
        invoice: `📄 *Factura ${documentNumber}*\n\n¡Hola ${clientName}!\n\nAdjunto encontrarás tu factura. ¡Gracias por tu compra!`,
        quotation: `📋 *Cotización ${documentNumber}*\n\n¡Hola ${clientName}!\n\nAdjunto encontrarás la cotización solicitada. Quedamos atentos a tus comentarios.`,
        purchase_order: `📦 *Orden de Compra ${documentNumber}*\n\n¡Hola ${clientName}!\n\nAdjunto encontrarás la orden de compra. Por favor confirmar recepción y disponibilidad de productos. ¡Gracias!`
      }

      const fileNames = {
        invoice: `factura-${documentNumber}.pdf`,
        quotation: `cotizacion-${documentNumber}.pdf`,
        purchase_order: `orden-compra-${documentNumber}.pdf`
      }

      const message = messages[documentType] || messages.invoice
      const fileName = fileNames[documentType] || fileNames.invoice

      const { isDirect, client } = getWhatsAppClient()
      
      if (isDirect) {
        // Modo directo: Convertir PDF Blob a base64 y enviar al servidor Node.js
        const reader = new FileReader()
        const base64Promise = new Promise((resolve, reject) => {
          reader.onloadend = () => resolve(reader.result)
          reader.onerror = reject
          reader.readAsDataURL(pdfBlob)
        })
        
        const base64Data = await base64Promise
        
        // Enviar al servidor multi-tenant
        const response = await client.post('/send', {
          phone: formattedPhone,
          message: message,
          pdfBase64: base64Data,
          fileName: fileName
        })
        
        return response.data
      } else {
        // Modo Laravel (fallback)
        const formData = new FormData()
        formData.append('phone', formattedPhone)
        formData.append('message', message)
        formData.append('pdf', pdfBlob, fileName)

        const response = await client.post('/whatsapp/send-pdf', formData, {
          headers: {
            'Content-Type': 'multipart/form-data'
          },
          timeout: 60000
        })
        return response.data
      }
    } catch (error) {
      console.error('Error enviando documento por WhatsApp:', error)
      return {
        success: false,
        message: error.message
      }
    }
  }
}