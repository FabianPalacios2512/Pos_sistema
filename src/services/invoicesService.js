// invoicesService.js
import { apiCall } from './api.js'
import api from './api.js'

export const invoicesService = {
  // Obtener todas las facturas
  async getInvoices() {
    try {
      const response = await apiCall('/invoices')
      return response
    } catch (error) {
      console.error('Error al obtener facturas:', error)
      throw error
    }
  },

  // Obtener una factura específica
  async getInvoice(id) {
    try {
      const response = await apiCall(`/invoices/${id}`)
      return response
    } catch (error) {
      console.error('Error al obtener la factura:', error)
      throw error
    }
  },

  // Crear nueva factura
  async createInvoice(invoiceData) {
    try {
      const response = await apiCall('/invoices', {
        method: 'POST',
        body: JSON.stringify(invoiceData)
      })
      return response
    } catch (error) {
      console.error('Error al crear factura:', error)
      throw error
    }
  },

  // Crear factura desde POS (sin autenticación)
  async createPosInvoice(invoiceData) {
    try {
      const response = await apiCall('/pos/invoices', {
        method: 'POST',
        body: JSON.stringify(invoiceData)
      })
      return response
    } catch (error) {
      console.error('Error al crear factura desde POS:', error)
      throw error
    }
  },

  // Actualizar factura
  async updateInvoice(id, invoiceData) {
    try {
      const response = await apiCall(`/invoices/${id}`, {
        method: 'PUT',
        body: JSON.stringify(invoiceData)
      })
      return response
    } catch (error) {
      console.error('Error al actualizar factura:', error)
      throw error
    }
  },

  // Eliminar factura
  async deleteInvoice(id) {
    try {
      const response = await apiCall(`/invoices/${id}`, {
        method: 'DELETE'
      })
      return response
    } catch (error) {
      console.error('Error al eliminar factura:', error)
      throw error
    }
  },

  // Obtener estadísticas de facturas
  async getStats() {
    try {
      const response = await apiCall('/invoices/stats')
      return response
    } catch (error) {
      console.error('Error al obtener estadísticas:', error)
      throw error
    }
  },

  // Cambiar estado de factura
  async updateStatus(id, status) {
    try {
      const response = await apiCall(`/invoices/${id}`, {
        method: 'PUT',
        body: JSON.stringify({ status })
      })
      return response
    } catch (error) {
      console.error('Error al actualizar estado:', error)
      throw error
    }
  },

  // Crear nueva cotización
  async createQuote(data) {
    try {
      const response = await api.post('/pos/invoices', {
        ...data,
        type: 'quote',
        status: 'quotation'
      })
      
      return response
    } catch (error) {
      console.error('Error creating quote:', error)
      throw error
    }
  },

  // Buscar cotización por código
  async searchQuote(quoteCode) {
    try {
      // Usar directamente la ruta pública que busca en ambas tablas (sales e invoices)
      const publicResponse = await api.get(`/quotes/search/${quoteCode}`)
      
      if (publicResponse.success && publicResponse.data) {
        return {
          success: true,
          data: [publicResponse.data]
        }
      }
      
      return { success: false, data: [], message: 'Cotización no encontrada' }
      
    } catch (error) {
      // Solo loggeamos errores técnicos, no cuando simplemente no se encuentra la cotización
      if (error.message !== 'Cotización no encontrada') {
        console.error('❌ Error técnico al buscar cotización:', error)
      }
      return { success: false, data: [], message: error.message || 'Cotización no encontrada' }
    }
  },

  // Convertir cotización a venta (crear nueva factura y eliminar cotización)
  async convertQuoteToSale(quoteCode, conversionData) {
    try {
      
      // Primero buscar la cotización para obtener su ID y datos
      const searchResult = await this.searchQuote(quoteCode)
      
      if (searchResult.success && searchResult.data && searchResult.data.length > 0) {
        const quotation = searchResult.data[0]
        console.log('📄 Cotización encontrada para conversión:', quotation)
        
        // Asegurar que los items tengan el formato correcto
        const formattedItems = (conversionData.items || quotation.invoice_items || quotation.items || []).map(item => ({
          product_id: item.product_id || item.id || 0,
          product_name: item.product_name || item.name || 'Producto',
          quantity: parseFloat(item.quantity || 1),
          unit_price: parseFloat(item.unit_price || item.price || 0)
        }))
        
        // Crear nueva factura usando los datos de la cotización
        const newInvoiceData = {
          type: 'invoice',
          customer_id: conversionData.customer_id || quotation.customer_id || 7,
          customer_name: conversionData.customer_name || quotation.customer_name || 'Cliente General',
          date: new Date().toISOString().split('T')[0],
          due_date: conversionData.due_date || new Date().toISOString().split('T')[0],
          payment_method: conversionData.payment_method || 'efectivo',
          status: 'paid',
          items: formattedItems,
          subtotal: parseFloat(conversionData.subtotal || quotation.subtotal || 0),
          tax_amount: parseFloat(conversionData.tax_amount || quotation.tax_amount || 0),
          total: parseFloat(conversionData.total || quotation.total || 0),
          notes: conversionData.notes || quotation.notes || '',
          customer_email: conversionData.customer_email || quotation.customer_email || '',
          customer_phone: conversionData.customer_phone || quotation.customer_phone || ''
        }
        
        console.log('📝 Datos de factura a crear:', newInvoiceData)
        
        // Crear nueva factura usando el método correcto
        const newInvoiceResponse = await this.createPosInvoice(newInvoiceData)
        
        if (newInvoiceResponse.success) {
          console.log('✅ Nueva factura creada:', newInvoiceResponse.data)
          
          // Cancelar la cotización original cambiando su status
          try {
            await api.patch(`/invoices/${quotation.id}`, {
              status: 'cancelled'
            })
            console.log('✅ Cotización cancelada automáticamente')
          } catch (updateError) {
            console.warn('⚠️ No se pudo cancelar la cotización, pero la factura fue creada:', updateError)
          }
          
          return {
            success: true,
            data: newInvoiceResponse.data,
            message: 'Cotización convertida a venta exitosamente'
          }
        } else {
          throw new Error('Error al crear la nueva factura')
        }
      } else {
        throw new Error('Cotización no encontrada para conversión')
      }
    } catch (error) {
      console.error('Error converting quote to sale:', error)
      throw error
    }
  },

  // Obtener todas las cotizaciones (desde invoices)
  async getQuotes() {
    try {
      const response = await api.get('/invoices?type=quote')
      return response
    } catch (error) {
      console.error('Error getting quotes:', error)
      throw error
    }
  },

  // Obtener datos combinados de facturas y cotizaciones
  async getAllDocuments() {
    try {
      // Obtener facturas y cotizaciones en paralelo
      const [invoicesResponse, quotesResponse] = await Promise.all([
        this.getInvoices(),
        this.getQuotes()
      ])

      // Combinar y normalizar los datos
      const invoices = invoicesResponse.data || []
      const quotes = quotesResponse.data?.data || []

      // Normalizar cotizaciones para que tengan el mismo formato que facturas
      const normalizedQuotes = quotes.map(quote => ({
        ...quote,
        type: 'quote',
        status: quote.status,
        number: quote.invoice_number,
        date: quote.sale_date,
        customer: quote.customer || { name: 'Cliente General' },
        total: quote.total_amount,
        items: quote.sale_items || []
      }))

      // Combinar todos los documentos
      const allDocuments = [...invoices, ...normalizedQuotes]

      // Ordenar por fecha (más recientes primero)
      allDocuments.sort((a, b) => new Date(b.date) - new Date(a.date))

      return {
        success: true,
        data: allDocuments,
        message: 'Documentos obtenidos exitosamente'
      }
    } catch (error) {
      console.error('Error getting all documents:', error)
      throw error
    }
  }
}