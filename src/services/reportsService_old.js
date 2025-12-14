import apiClient from './apiClient.js'
import { invoicesService } from './invoicesService.js'
import { productsService } from './productsService.js'
import { customersService } from './customersService.js'
import axios from 'axios'

const API_URL = '/api'

export const reportsService = {
  // Obtener datos de ventas por período
  async getSalesData(period = 'month') {
    try {
      console.log(`📊 getSalesData llamado para período: ${period}`)
      
      // USAR ENDPOINT PÚBLICO DIRECTO (sin autenticación)
      const response = await axios.get(`${API_URL}/dashboard/sales`, {
        params: { period }
      })
      
      if (response.data?.success) {
        console.log('✅ Datos de ventas obtenidos:', response.data.data)
        return response.data.data
      }
      
      console.warn('⚠️ Respuesta sin éxito:', response.data)
      return {
        totalSales: 0,
        totalTransactions: 0,
        averageTicket: 0
      }
      
    } catch (error) {
      console.error('❌ Error en getSalesData:', error)
      return {
        totalSales: 0,
        totalTransactions: 0,
        averageTicket: 0
      }
    }
  },
          break
        case 'week':
          startDate = new Date()
          startDate.setDate(startDate.getDate() - 6) // 7 días: hoy + 6 días atrás
          startDate.setHours(0, 0, 0, 0)
          endDate = new Date()
          endDate.setHours(23, 59, 59, 999)
          break
        case 'month':
          startDate = new Date()
          startDate.setDate(startDate.getDate() - 29) // 30 días: hoy + 29 días atrás
          startDate.setHours(0, 0, 0, 0)
          endDate = new Date()
          endDate.setHours(23, 59, 59, 999)
          break
        case 'quarter':
          startDate = new Date()
          startDate.setMonth(startDate.getMonth() - 3)
          startDate.setHours(0, 0, 0, 0)
          endDate = new Date()
          endDate.setHours(23, 59, 59, 999)
          break
        case 'year':
          startDate = new Date()
          startDate.setDate(startDate.getDate() - 364) // 365 días: hoy + 364 días atrás
          startDate.setHours(0, 0, 0, 0)
          endDate = new Date()
          endDate.setHours(23, 59, 59, 999)
          break
        default:
          startDate = new Date()
          startDate.setMonth(startDate.getMonth() - 1)
          startDate.setHours(0, 0, 0, 0)
          endDate = new Date()
          endDate.setHours(23, 59, 59, 999)
      }
      
      // Debug logs removed for production
      
      // SOLO FACTURAS PAGADAS - Filtro estricto según tus datos reales
      const paidInvoices = invoices.filter(invoice => {
        // Incluir facturas con estado "paid" (como en tu BD) o variantes en español
        const isPaid = invoice.status === 'paid' || 
                      invoice.status === 'Pagada' || 
                      invoice.status === 'pagada'
        return isPaid
      })
      
      console.log(`📊 Facturas pagadas encontradas: ${paidInvoices.length}`)
      if (paidInvoices.length > 0) {
        console.log(`📊 Primera factura pagada:`, paidInvoices[0])
      }
      
      // Para período "today", detectar la fecha más reciente con datos
      if (period === 'today' && paidInvoices.length > 0) {
        // Encontrar la fecha más reciente en las facturas pagadas
        const dates = paidInvoices.map(inv => {
          const rawDate = inv.date || inv.created_at || inv.invoice_date
          return new Date(rawDate)
        }).sort((a, b) => b - a) // Ordenar de más reciente a más antigua
        
        const mostRecentDate = dates[0]
        
        // Si no hay facturas de hoy, informar que se usarán datos más recientes
        const today = new Date()
        const todayStr = today.toLocaleDateString('es-CO')
        const recentStr = mostRecentDate.toLocaleDateString('es-CO')
        
        if (todayStr !== recentStr) {
        }
      }
      
      // Filtrar facturas PAGADAS por período
      const filteredInvoices = paidInvoices.filter((invoice, index) => {
        // Usar el campo 'date' que es el que realmente contiene la fecha de la factura
        const rawDate = invoice.date || invoice.created_at || invoice.invoice_date || invoice.timestamp
        let invoiceDate = new Date(rawDate)
        
        // CORRECCIÓN: Asegurar parseo correcto de fechas ISO
        if (rawDate && typeof rawDate === 'string') {
          if (rawDate.includes('-') && rawDate.length === 10) {
            // Formato ISO YYYY-MM-DD
            const parts = rawDate.split('-')
            if (parts.length === 3) {
              // Crear fecha correcta: año, mes-1, día 
              invoiceDate = new Date(parseInt(parts[0]), parseInt(parts[1]) - 1, parseInt(parts[2]))
            }
          } else if (rawDate.includes('/')) {
            // Formato DD/MM/YYYY
            const parts = rawDate.split('/')
            if (parts.length === 3) {
              invoiceDate = new Date(parts[2], parts[1] - 1, parts[0])
            }
          }
        }
        
        // Comparar fechas usando solo día, mes y año (ignorar horas)
        const invoiceDay = invoiceDate.getDate()
        const invoiceMonth = invoiceDate.getMonth()
        const invoiceYear = invoiceDate.getFullYear()
        
        const startDay = startDate.getDate()
        const startMonth = startDate.getMonth() 
        const startYear = startDate.getFullYear()
        
        const endDay = endDate.getDate()
        const endMonth = endDate.getMonth()
        const endYear = endDate.getFullYear()
        
        let isInPeriod = false
        
        if (period === 'today') {
          // Para hoy: SOLO buscar facturas del día actual (no incluir ayer)
          const today = new Date()
          const isToday = invoiceDay === today.getDate() && 
                         invoiceMonth === today.getMonth() && 
                         invoiceYear === today.getFullYear()
          
          isInPeriod = isToday
          
          // Debug logs removed for production
          
        } else {
          // Para otros períodos: usar rango completo
          isInPeriod = invoiceDate >= startDate && invoiceDate <= endDate
        }
        
        // Debug logs removed for production
        
        return isInPeriod
      })
      
      
      // Summary logs removed for production
      
      // Calcular métricas básicas - INCLUYENDO DEVOLUCIONES
      const grossSales = filteredInvoices.reduce((sum, invoice) => {
        const total = parseFloat(invoice.total || invoice.amount || invoice.grand_total || 0)
        return sum + Math.abs(total)
      }, 0)
      
      // 🔧 NUEVA LÓGICA: Las facturas ya están ajustadas por las devoluciones
      // No necesitamos restar devoluciones porque ya se aplicaron a las facturas
      const totalReturns = 0 // Siempre 0 porque las devoluciones ya modificaron las facturas
      
      // Las ventas netas son simplemente las facturas (ya ajustadas)
      const netSales = grossSales
      
      const totalTransactions = filteredInvoices.length
      const averageTicket = totalTransactions > 0 ? netSales / totalTransactions : 0
      
      
      return {
        success: true,
        data: {
          totalSales: netSales, // Ahora es igual a grossSales (facturas ya ajustadas)
          grossSales: grossSales, // Facturas totales
          totalReturns: 0, // Siempre 0 en la nueva lógica
          totalTransactions,
          averageTicket: Math.abs(averageTicket),
          invoices: filteredInvoices
        }
      }
    } catch (error) {
      console.error('Error en getSalesData:', error)
      return {
        success: false,
        error: error.message
      }
    }
  },

  // Obtener ventas diarias para gráficos
  async getDailySales(period = 'month') {
    try {
      const salesData = await this.getSalesData(period)
      
      if (!salesData.success) {
        return salesData
      }
      
      const invoices = salesData.data.invoices
      
      const dailySalesMap = new Map()
      
      // Crear entradas para todos los días del período
      const now = new Date()
      let days = 7 // por defecto
      
      switch (period) {
        case 'today':
          days = 1
          break
        case 'week':
          days = 7
          break
        case 'month':
          days = 30
          break
        case 'quarter':
          days = 90
          break
        case 'year':
          days = 365
          break
      }
      
      // Inicializar todos los días con 0 - desde hace X días hasta HOY
      const dateArray = []
      const today = new Date()
      
      for (let i = days - 1; i >= 0; i--) {
        const date = new Date(today)
        date.setDate(today.getDate() - i)
        const dateKey = date.toISOString().split('T')[0] // Usar formato YYYY-MM-DD
        dailySalesMap.set(dateKey, 0)
        dateArray.push(date)
      }
      
      // Debug logs removed for production
      
      
      // Usar SOLO las facturas pagadas que ya fueron filtradas en getSalesData
      const paidInvoices = invoices.filter(invoice => {
        return invoice.status === 'paid' || 
               invoice.status === 'Pagada' || 
               invoice.status === 'pagada'
      })
      
      
      // Agrupar ventas por día usando SOLO facturas pagadas
      paidInvoices.forEach((invoice, index) => {
        // Usar el campo 'date' que es el que realmente contiene la fecha de la factura
        const rawDate = invoice.date || invoice.created_at || invoice.invoice_date || invoice.timestamp
        let invoiceDate = new Date(rawDate)
        
        // CORRECCIÓN: Parsear fecha correctamente y manejar zona horaria
        if (rawDate && typeof rawDate === 'string') {
          if (rawDate.includes('-')) {
            // Formato ISO YYYY-MM-DD o YYYY-MM-DD HH:mm:ss
            if (rawDate.length === 10) {
              // Solo fecha YYYY-MM-DD
              invoiceDate = new Date(rawDate + 'T12:00:00') // Agregar hora para evitar problemas de zona horaria
            } else {
              // Fecha con hora, usar Date directamente pero ajustar a mediodia
              invoiceDate = new Date(rawDate)
              invoiceDate.setHours(12, 0, 0, 0) // Normalizar a mediodia para evitar problemas de zona horaria
            }
          } else if (rawDate.includes('/')) {
            // Formato DD/MM/YYYY
            const parts = rawDate.split('/')
            if (parts.length === 3) {
              invoiceDate = new Date(parts[2], parts[1] - 1, parts[0], 12, 0, 0)
            }
          }
        } else if (rawDate instanceof Date) {
          invoiceDate = new Date(rawDate)
          invoiceDate.setHours(12, 0, 0, 0)
        }
        
        const dateKey = invoiceDate.toISOString().split('T')[0] // YYYY-MM-DD
        const total = Math.abs(parseFloat(invoice.total || invoice.amount || invoice.grand_total || 0))
        
        // Debug logs removed for production
        
        if (dailySalesMap.has(dateKey)) {
          const currentTotal = dailySalesMap.get(dateKey) || 0
          dailySalesMap.set(dateKey, currentTotal + total)
        } else {
          // Solo agregar fechas que estén dentro del rango esperado
          const today = new Date()
          const diffDays = Math.floor((today - invoiceDate) / (1000 * 60 * 60 * 24))
          if (diffDays >= 0 && diffDays < days) {
            dailySalesMap.set(dateKey, total)
          }
        }
      })
      
      // Convertir a array ordenado por fecha CON fechas y totales
      const dailySalesWithDates = Array.from(dailySalesMap.entries())
        .map(([dateKey, total]) => ({
          date: dateKey, // YYYY-MM-DD format
          dateObject: new Date(dateKey + 'T00:00:00'),
          total
        }))
        .sort((a, b) => a.dateObject - b.dateObject)
      
      // Para compatibilidad, también devolver solo los totales
      const dailySales = dailySalesWithDates.map(item => item.total)
      
      // Debug summary removed for production
      
      return {
        success: true,
        data: dailySales, // Array de números para compatibilidad
        dataWithDates: dailySalesWithDates // Array de objetos con fechas
      }
    } catch (error) {
      console.error('Error en getDailySales:', error)
      return {
        success: false,
        error: error.message
      }
    }
  },

  // Obtener productos más vendidos
  async getTopProducts(period = 'month', limit = 5) {
    try {
      
      const salesData = await this.getSalesData(period)
      
      if (!salesData.success) {
        return salesData
      }
      
      
      // Obtener datos completos de productos para imágenes
      const productsResponse = await productsService.getAll()
      const productsMap = new Map()
      
      if (productsResponse.success && productsResponse.data?.products) {
        productsResponse.data.products.forEach(product => {
          productsMap.set(product.id, product)
        })
      }
      
      const invoices = salesData.data.invoices
      const productSales = new Map()
      
      
      // Procesar items de todas las facturas
      invoices.forEach((invoice, invIndex) => {
        if (invoice.items && Array.isArray(invoice.items)) {
          
          invoice.items.forEach((item, itemIndex) => {
            const productId = item.product_id || item.id
            const productName = item.product?.name || item.product_name || item.name || item.description || 'Producto sin nombre'
            const quantity = Math.abs(parseInt(item.quantity || 1))
            const unitPrice = Math.abs(parseFloat(item.unit_price || item.price || item.sale_price || 0))
            const revenue = quantity * unitPrice
            
            // Debug para los primeros items - comentado para reducir ruido
            if (false && itemIndex < 3) {
              console.log(`   - Producto: ${productName}`)
              console.log(`   - Cantidad: ${quantity}`)
              console.log(`   - Precio unitario: ${unitPrice}`)
              console.log(`   - Revenue calculado: ${revenue}`)
              console.log(`   - item.unit_price: ${item.unit_price}`)
              console.log(`   - item.price: ${item.price}`)
              console.log(`   - item.sale_price: ${item.sale_price}`)
              console.log(`   - Item completo:`, item)
            }
            
            // Obtener datos de imagen del producto desde el mapa de productos
            const fullProduct = productsMap.get(productId)
            const productImage = fullProduct?.image || fullProduct?.img || item.product?.image || item.product?.img || item.image || item.img
            const productImageUrl = fullProduct?.imageUrl || fullProduct?.image_url || item.product?.imageUrl || item.product?.image_url || item.imageUrl || item.image_url
            
            if (false && itemIndex < 3) {
              console.log(`   - fullProduct:`, fullProduct ? 'encontrado' : 'no encontrado')
              console.log(`   - productImage: ${productImage || 'sin imagen'}`)
              console.log(`   - productImageUrl: ${productImageUrl || 'sin URL'}`)
              if (fullProduct) {
                console.log(`   - Campos imagen en fullProduct:`, {
                  image: fullProduct.image,
                  img: fullProduct.img,
                  imageUrl: fullProduct.imageUrl,
                  image_url: fullProduct.image_url
                })
              }
            }
            
            if (productSales.has(productId)) {
              const existing = productSales.get(productId)
              productSales.set(productId, {
                ...existing,
                sold: existing.sold + quantity,
                revenue: existing.revenue + revenue
              })
            } else {
              productSales.set(productId, {
                id: productId,
                name: productName,
                sold: quantity,
                revenue,
                image: productImage,
                imageUrl: productImageUrl,
                // Información completa del producto si está disponible
                product: item.product
              })
            }
          })
        }
      })
      
      // Convertir a array y ordenar por ventas
      const topProducts = Array.from(productSales.values())
        .sort((a, b) => b.revenue - a.revenue)
        .slice(0, limit)
      
      // Debug logs removed for production
      
      return {
        success: true,
        data: topProducts
      }
    } catch (error) {
      console.error('Error en getTopProducts:', error)
      return {
        success: false,
        error: error.message
      }
    }
  },

  // Obtener ventas por categoría
  async getSalesByCategory(period = 'month') {
    try {
      const salesData = await this.getSalesData(period)
      
      if (!salesData.success) {
        return salesData
      }
      
      // Obtener productos para mapear categorías
      const productsResponse = await productsService.getAll()
      const products = productsResponse.success ? productsResponse.data.data : []
      
      const invoices = salesData.data.invoices
      const categorySales = new Map()
      
      // Procesar items de todas las facturas
      invoices.forEach(invoice => {
        if (invoice.items && Array.isArray(invoice.items)) {
          invoice.items.forEach(item => {
            const productId = item.product_id || item.id
            const quantity = Math.abs(parseInt(item.quantity || 1))
            const unitPrice = Math.abs(parseFloat(item.unit_price || item.price || item.sale_price || 0))
            const revenue = quantity * unitPrice
            
            // Buscar categoría del producto
            const product = products.find(p => p.id === productId)
            const categoryName = product?.category?.name || item.product?.category?.name || 'Sin categoría'
            
            if (categorySales.has(categoryName)) {
              categorySales.set(categoryName, categorySales.get(categoryName) + revenue)
            } else {
              categorySales.set(categoryName, revenue)
            }
          })
        }
      })
      
      // Convertir a array de objetos
      const salesByCategory = Array.from(categorySales.entries())
        .map(([name, sales]) => ({ name, sales }))
        .sort((a, b) => b.sales - a.sales)
      
      return {
        success: true,
        data: salesByCategory
      }
    } catch (error) {
      console.error('Error en getSalesByCategory:', error)
      return {
        success: false,
        error: error.message
      }
    }
  },

  // Obtener métodos de pago utilizados
  async getPaymentMethods(period = 'month') {
    try {
      const salesData = await this.getSalesData(period)
      
      if (!salesData.success) {
        return salesData
      }
      
      const invoices = salesData.data.invoices
      const paymentStats = new Map()
      const totalSales = salesData.data.totalSales
      
      
      // Contar métodos de pago usando el campo payment_method directamente
      invoices.forEach((invoice, index) => {
        // El campo se llama payment_method según tus logs
        const method = invoice.payment_method || invoice.payment || 'cash'
        const amount = Math.abs(parseFloat(invoice.total || 0))
        

        
        if (paymentStats.has(method)) {
          paymentStats.set(method, paymentStats.get(method) + amount)
        } else {
          paymentStats.set(method, amount)
        }
      })
      
      
      
      // Convertir a porcentajes
      const paymentMethods = Array.from(paymentStats.entries())
        .map(([method, amount]) => {
          const methodNames = {
            cash: 'Efectivo',
            card: 'Tarjeta',
            transfer: 'Transferencia',
            'bank_transfer': 'Transferencia Bancaria',
            qr: 'QR/Móvil',
            mobile: 'QR/Móvil',
            other: 'Otros'
          }
          
          const percentage = totalSales > 0 ? Math.round((amount / totalSales) * 100) : 0
          const transactionCount = invoices.filter(inv => inv.payment_method === method).length
          
          return {
            name: methodNames[method] || method,
            percentage: percentage,
            amount: amount,
            count: transactionCount
          }
        })
        .sort((a, b) => b.percentage - a.percentage)
        
      
      return {
        success: true,
        data: paymentMethods
      }
    } catch (error) {
      console.error('Error en getPaymentMethods:', error)
      return {
        success: false,
        error: error.message
      }
    }
  },

  // Obtener información de inventario con stock bajo
  async getLowStockProducts() {
    try {
      const productsResponse = await productsService.getAll()
      
      if (!productsResponse.success) {
        throw new Error('Error al cargar productos')
      }
      
      const products = productsResponse.data.data
      // Stock crítico: current_stock <= min_stock (incluye productos en stock mínimo)
      const lowStockProducts = products
        .filter(product => product.active && (product.current_stock || 0) <= (product.min_stock || 5))
        .map(product => ({
          id: product.id,
          name: product.name,
          stock: product.current_stock || 0,
          current_stock: product.current_stock || 0,
          min_stock: product.min_stock || 5,
          category: product.category?.name || 'Sin categoría',
          image_url: product.image_url || null
        }))
        .sort((a, b) => a.stock - b.stock)
      
      console.log(`📊 Stock crítico detectado: ${lowStockProducts.length} productos`)
      
      return {
        success: true,
        data: lowStockProducts // Datos reales
      }
    } catch (error) {
      console.error('Error en getLowStockProducts:', error)
      return {
        success: false,
        error: error.message
      }
    }
  },

  // Obtener estadísticas generales de productos
  async getProductStats() {
    try {
      const productsResponse = await productsService.getAll()
      
      if (!productsResponse.success) {
        throw new Error('Error al cargar productos')
      }
      
      const products = productsResponse.data.data
      const activeProducts = products.filter(product => product.active)
      // Usar la misma lógica que gestión de productos: current_stock <= min_stock
      const lowStockCount = activeProducts.filter(product => (product.current_stock || 0) <= (product.min_stock || 0)).length
      
      
      return {
        success: true,
        data: {
          totalProducts: activeProducts.length,
          lowStockCount: lowStockCount,
          totalProductsIncludingInactive: products.length
        }
      }
    } catch (error) {
      console.error('Error en getProductStats:', error)
      return {
        success: false,
        error: error.message
      }
    }
  },

  // Obtener estadísticas de clientes
  async getCustomerStats(period = 'month') {
    try {
      const [salesData, customersResponse] = await Promise.all([
        this.getSalesData(period),
        customersService.getAll()
      ])
      
      if (!salesData.success || !customersResponse.success) {
        throw new Error('Error al cargar datos')
      }
      
      const invoices = salesData.data.invoices
      const customers = customersResponse.data
      
      // Contar clientes únicos que compraron en el período
      const activeCustomers = new Set()
      invoices.forEach(invoice => {
        if (invoice.customer_id) {
          activeCustomers.add(invoice.customer_id)
        }
      })
      
      return {
        success: true,
        data: {
          totalCustomers: customers.length,
          activeCustomers: activeCustomers.size,
          newCustomers: 0, // Requeriría campo de fecha de registro
          customerRetention: activeCustomers.size > 0 ? Math.round((activeCustomers.size / customers.length) * 100) : 0
        }
      }
    } catch (error) {
      console.error('Error en getCustomerStats:', error)
      return {
        success: false,
        error: error.message
      }
    }
  },

  // Obtener últimas transacciones reales
  async getRecentTransactions(limit = 4) {
    try {
      // Obtener todas las facturas
      const invoicesResponse = await invoicesService.getInvoices()
      
      if (!invoicesResponse.success) {
        throw new Error('Error al cargar facturas')
      }

      const invoices = invoicesResponse.data || []

      // Filtrar solo facturas pagadas y ordenar por fecha
      const paidInvoices = invoices.filter(invoice => {
        const isPaid = invoice.status === 'Pagada' || 
                      invoice.status === 'pagada' || 
                      invoice.status === 'paid'
        return isPaid
      }).sort((a, b) => {
        // Ordenar por fecha más reciente primero
        const dateA = new Date(a.date || a.created_at || a.invoice_date)
        const dateB = new Date(b.date || b.created_at || b.invoice_date)
        return dateB - dateA
      })


      // Tomar las más recientes y formatear para el dashboard
      const recentTransactions = paidInvoices.slice(0, limit).map(invoice => {
        const transactionDate = invoice.date || invoice.created_at || invoice.invoice_date
        const total = parseFloat(invoice.total || invoice.amount || invoice.grand_total || 0)
        
        return {
          id: invoice.id || invoice.number,
          total: Math.abs(total), // Asegurar que sea positivo
          date: new Date(transactionDate).toISOString(),
          status: 'Pagado',
          invoiceNumber: invoice.number || invoice.invoice_number || `INV-${invoice.id}`,
          customer: invoice.customer_name || 'Cliente General'
        }
      })


      return {
        success: true,
        data: recentTransactions
      }

    } catch (error) {
      console.error('Error al obtener transacciones recientes:', error)
      return {
        success: false,
        data: [],
        error: error.message
      }
    }
  },

  // Obtener datos de devoluciones por período - USANDO ENDPOINT DE MÉTRICAS
  async getReturnsData(period = 'month') {
    try {
      console.log('🔄 getReturnsData usando endpoint de métricas para período:', period)
      
      const response = await apiClient.get(`/returns/metrics/${period}`)
      
      if (!response.data.success) {
        console.warn('❌ No se pudieron obtener métricas de devoluciones:', response.data.message)
        return {
          success: false,
          error: response.data.message || 'Error obteniendo métricas de devoluciones',
          data: { totalReturns: 0, returnsCount: 0 }
        }
      }

      console.log('✅ Métricas de devoluciones obtenidas:', response.data.data)
      
      return {
        success: true,
        data: response.data.data
      }

    } catch (error) {
      console.error('❌ Error en getReturnsData:', error.message)
      // En caso de error, devolver 0 para que el dashboard funcione
      return {
        success: false,
        error: error.message,
        data: { totalReturns: 0, returnsCount: 0 }
      }
    }
  }
}