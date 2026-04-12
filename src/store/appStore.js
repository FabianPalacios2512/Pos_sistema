import { ref, reactive, computed } from 'vue'
import { productsService } from '../services/productsService.js'
import { categoriesService } from '../services/categoriesService.js'
import { customersService } from '../services/customersService.js'
import { cashSessionService } from '../services/cashSessionService.js'
import apiClient from '../services/apiClient.js'

// Función helper para actualizar el contexto de IA (lazy import para evitar ciclos)
let uiContextStoreInstance = null
const getUIContextStore = async () => {
  if (!uiContextStoreInstance) {
    const { useUIContextStore } = await import('./uiContextStore.js')
    uiContextStoreInstance = useUIContextStore()
  }
  return uiContextStoreInstance
}

// Helper para actualizar el estado de caja en el contexto de IA
const updateCashContextForAI = async (hasOpenSession, currentSession) => {
  try {
    const uiContext = await getUIContextStore()
    if (uiContext?.updateGlobalBusinessSection) {
      uiContext.updateGlobalBusinessSection('caja', {
        estado: hasOpenSession ? 'abierta' : 'cerrada',
        montoActual: currentSession?.current_balance || 0
      })
    }
  } catch (error) {
    // Silenciar error si el store no está disponible aún
  }
}

// Helper para cargar datos globales del negocio para la IA
const loadGlobalBusinessDataForAI = async () => {
  try {
    const uiContext = await getUIContextStore()
    if (!uiContext?.updateGlobalBusinessSection) {
      return
    }
    
    // ═══════════════════════════════════════════════════════════════
    // 1. INVENTARIO - desde productos ya cargados
    // ═══════════════════════════════════════════════════════════════
    if (appStore.products && appStore.products.length > 0) {
      const products = appStore.products
      const activeProducts = products.filter(p => p.active !== false)
      const stockBajoCount = activeProducts.filter(p => (p.current_stock || 0) <= (p.min_stock || 0)).length
      const sinStockCount = activeProducts.filter(p => (p.current_stock || 0) === 0).length
      
      let valorInvertido = 0
      let valorPotencial = 0
      activeProducts.forEach(p => {
        const stock = p.current_stock || 0
        // El modelo usa cost_price y sale_price como campos principales
        const cost = parseFloat(p.cost_price || p.purchase_price || p.cost || 0)
        const price = parseFloat(p.sale_price || p.price || 0)
        valorInvertido += stock * cost
        valorPotencial += stock * price
      })
      
      uiContext.updateGlobalBusinessSection('inventario', {
        productosActivos: activeProducts.length,
        productosTotal: products.length,
        stockBajo: stockBajoCount,
        sinStock: sinStockCount,
        valorInvertido: Math.round(valorInvertido),
        valorPotencial: Math.round(valorPotencial),
        gananciaEstimada: Math.round(valorPotencial - valorInvertido)
      })
      
      // Alertas de stock
      const productosStockBajo = activeProducts
        .filter(p => (p.current_stock || 0) <= (p.min_stock || 0) && (p.current_stock || 0) > 0)
        .slice(0, 10)
        .map(p => ({ nombre: p.name, stock: p.current_stock || 0, minimo: p.min_stock || 0 }))
      
      const productosSinStock = activeProducts
        .filter(p => (p.current_stock || 0) === 0)
        .slice(0, 10)
        .map(p => ({ nombre: p.name }))
      
      uiContext.updateGlobalBusinessSection('alertas', {
        productosStockBajo,
        productosSinStock
      })
    }
    
    // ═══════════════════════════════════════════════════════════════
    // 2. VENTAS - cargar ventas de hoy y del mes
    // ═══════════════════════════════════════════════════════════════
    try {
      // Cargar ventas de hoy y estadísticas del mes en paralelo
      const [ventasHoyRes, dashboardStatsRes] = await Promise.all([
        apiClient.get('/dashboard/ventas-hoy').catch(() => ({ data: {} })),
        apiClient.get('/dashboard/stats').catch(() => ({ data: { success: false } }))
      ])
      
      let ventasHoy = 0
      let transaccionesHoy = 0
      let ventasMes = 0
      let transaccionesMes = 0
      
      // Extraer ventas de hoy - el endpoint devuelve { total, transacciones, fecha_colombia }
      if (ventasHoyRes.data) {
        ventasHoy = parseFloat(ventasHoyRes.data.total || 0)
        transaccionesHoy = parseInt(ventasHoyRes.data.transacciones || 0)
      }
      
      // Extraer ventas del mes desde dashboard/stats
      // La estructura es: data.summary.month_sales.amount y data.summary.month_sales.count
      if (dashboardStatsRes.data?.success && dashboardStatsRes.data?.data) {
        const stats = dashboardStatsRes.data.data
        const summary = stats.summary || stats
        
        // Los campos están en summary.month_sales.amount y summary.month_sales.count
        if (summary.month_sales) {
          ventasMes = parseFloat(summary.month_sales.amount || 0)
          transaccionesMes = parseInt(summary.month_sales.count || 0)
        } else {
          // Fallback a campos planos
          ventasMes = parseFloat(stats.monthSales || stats.month_sales || 0)
          transaccionesMes = parseInt(stats.monthSalesCount || stats.month_sales_count || 0)
        }
      }
      
      const ticketPromedio = transaccionesHoy > 0 ? Math.round(ventasHoy / transaccionesHoy) : 0
      
      uiContext.updateGlobalBusinessSection('ventas', {
        ventasHoy,
        transaccionesHoy,
        ventasMes,
        transaccionesMes,
        ticketPromedio
      })
    } catch (e) {
      // Silenciar error de ventas
    }
    
    // ═══════════════════════════════════════════════════════════════
    // 3. CAJA - estado de sesión actual
    // ═══════════════════════════════════════════════════════════════
    try {
      if (appStore.cashSession?.current) {
        const session = appStore.cashSession.current
        const estado = appStore.cashSession.hasOpenSession ? 'abierta' : 'cerrada'
        const montoActual = parseFloat(session.total_sales || session.current_amount || 0)
        
        uiContext.updateGlobalBusinessSection('caja', {
          estado,
          montoActual,
          montoInicial: parseFloat(session.opening_amount || 0),
          ventasSesion: parseFloat(session.total_sales || 0),
          cajero: session.user?.name || 'Desconocido'
        })
      }
    } catch (e) {
      // Silenciar error de caja
    }
    
    // ═══════════════════════════════════════════════════════════════
    // 4. GASTOS - cargar gastos del mes y hoy
    // ═══════════════════════════════════════════════════════════════
    try {
      // Usar el endpoint de estadísticas que tiene resumen
      const [gastosStatsRes, gastosListRes] = await Promise.all([
        apiClient.get('/expenses/statistics').catch(() => ({ data: { success: false } })),
        apiClient.get('/expenses').catch(() => ({ data: { success: false } }))
      ])
      
      let gastosMes = 0
      let gastosHoy = 0
      
      // Obtener total del mes desde estadísticas
      if (gastosStatsRes.data?.success && gastosStatsRes.data?.data) {
        gastosMes = parseFloat(gastosStatsRes.data.data.current_month || gastosStatsRes.data.data.total_expenses || 0)
      }
      
      // Calcular gastos de hoy desde la lista
      if (gastosListRes.data?.success && gastosListRes.data?.data) {
        const gastos = Array.isArray(gastosListRes.data.data) ? gastosListRes.data.data : []
        
        // Obtener fecha de hoy en zona horaria Colombia
        const now = new Date()
        const colombiaOffset = -5 * 60 // UTC-5
        const localOffset = now.getTimezoneOffset()
        const colombiaTime = new Date(now.getTime() + (localOffset + colombiaOffset) * 60000)
        const hoy = colombiaTime.toISOString().split('T')[0]
        
        gastos.forEach(g => {
          const monto = parseFloat(g.amount || g.total || 0)
          // Comparar con fecha del gasto (puede ser expense_date, date o created_at)
          const fechaGasto = (g.expense_date || g.date || g.created_at || '').split('T')[0]
          if (fechaGasto === hoy) {
            gastosHoy += monto
          }
        })
      }
      
      uiContext.updateGlobalBusinessSection('gastos', {
        gastosMes,
        gastosHoy
      })
    } catch (e) {
      // Silenciar error de gastos
    }
    
    // ═══════════════════════════════════════════════════════════════
    // 5. GANANCIAS - calcular en base a ventas y gastos
    // ═══════════════════════════════════════════════════════════════
    // Leer valores ACTUALIZADOS del globalBusinessData (es un ref, necesita .value)
    const globalData = uiContext.globalBusinessData.value || uiContext.globalBusinessData
    const ventasMesActual = globalData.ventas?.ventasMes || 0
    const gastosMesActual = globalData.gastos?.gastosMes || 0
    
    const gananciaBrutaMes = ventasMesActual
    const gananciaNeta = gananciaBrutaMes - gastosMesActual
    const margenPromedio = gananciaBrutaMes > 0 ? Math.round((gananciaNeta / gananciaBrutaMes) * 100) : 0
    
    uiContext.updateGlobalBusinessSection('ganancias', {
      gananciaBrutaMes,
      gananciaNeta,
      margenPromedio
    })
    
    // ═══════════════════════════════════════════════════════════════
    // 6. DEVOLUCIONES - cargar devoluciones de hoy y del mes
    // ═══════════════════════════════════════════════════════════════
    try {
      // Cargar métricas de devoluciones de hoy y del mes en paralelo
      const [devolucionesHoyRes, devolucionesMesRes] = await Promise.all([
        apiClient.get('/returns/metrics/today').catch(() => ({ data: { success: false } })),
        apiClient.get('/returns/metrics/month').catch(() => ({ data: { success: false } }))
      ])
      
      let devolucionesHoy = 0
      let montoHoy = 0
      let devolucionesMes = 0
      let montoMes = 0
      
      // Extraer devoluciones de hoy
      if (devolucionesHoyRes.data?.success && devolucionesHoyRes.data?.data) {
        devolucionesHoy = parseInt(devolucionesHoyRes.data.data.returnsCount || 0)
        montoHoy = parseFloat(devolucionesHoyRes.data.data.totalReturns || 0)
      }
      
      // Extraer devoluciones del mes
      if (devolucionesMesRes.data?.success && devolucionesMesRes.data?.data) {
        devolucionesMes = parseInt(devolucionesMesRes.data.data.returnsCount || 0)
        montoMes = parseFloat(devolucionesMesRes.data.data.totalReturns || 0)
      }
      
      uiContext.updateGlobalBusinessSection('devoluciones', {
        devolucionesHoy,
        devolucionesMes,
        montoHoy,
        montoMes
      })
    } catch (e) {
      // Silenciar error de devoluciones
    }
    
    // ═══════════════════════════════════════════════════════════════
    // 7. ÚLTIMA FACTURA - para consultas rápidas de la IA
    // ═══════════════════════════════════════════════════════════════
    try {
      const ultimaFacturaRes = await apiClient.get('/invoices', {
        params: { limit: 1, sort: 'created_at', order: 'desc' }
      }).catch(() => ({ data: { success: false } }))
      
      if (ultimaFacturaRes.data?.success && ultimaFacturaRes.data?.data?.length > 0) {
        const f = ultimaFacturaRes.data.data[0]
        uiContext.updateGlobalBusinessSection('ultimaFactura', {
          numero: f.number || 'N/A',
          cliente: f.customer?.name || f.customer_name || 'Cliente General',
          total: parseFloat(f.total || 0),
          fecha: f.date || f.created_at,
          productos: f.items_count || (f.items ? f.items.length : 0),
          vendedor: f.seller_name || 'No registrado',
          metodoPago: f.payment_method || 'Efectivo'
        })
      }
    } catch (e) {
      // Silenciar error de última factura
    }
    
  } catch (error) {
    console.error('[appStore] Error cargando datos globales para IA:', error)
  }
}

// Store global de la aplicación para datos precargados
export const appStore = reactive({
  // Estados de carga
  loading: {
    products: false,
    categories: false,
    customers: false,
    systemSettings: false,
    paymentMethods: false,
    cashSession: false
  },
  
  // Datos precargados
  products: [],
  categories: [],
  customers: [],
  systemSettings: {},
  paymentMethods: [],
  tenantPlan: 'free_trial', // Plan del tenant (free_trial, basic, premium, enterprise)
  tenant: {}, // Información completa del tenant (plan, límites, fechas, etc.)
  businessName: '', // Nombre del negocio
  
  // Estado de sesión de caja (para evitar verificaciones repetidas)
  cashSession: {
    current: null,
    hasOpenSession: false,
    lastChecked: null,
    initialized: false
  },
  
  // Estado de inicialización
  initialized: false,
  isSubscriptionExpired: false, // Estado para controlar expiración (lo maneja SubscriptionExpiredModal)
  
  // Métodos para cargar datos
  async loadProducts(warehouseId = null, searchScope = 'local', force = false) {
    // Si force=true, SIEMPRE recarga sin importar el estado de loading
    if (!force && this.loading.products) return // Evitar cargas duplicadas solo si NO es force

    // Si force=true, resetear el estado de loading por si quedó bloqueado
    if (force) {
      this.loading.products = false
    }

    try {
      this.loading.products = true
      
      // Si no se pasa warehouse_id, intentar usar el de la sesión activa
      const targetWarehouseId = warehouseId || this.cashSession.current?.warehouse_id
      
      // Usar endpoint optimizado para POS con filtro de bodega y scope
      const params = targetWarehouseId ? { 
        warehouse_id: targetWarehouseId,
        scope: searchScope 
      } : {}
      
      const response = await productsService.getForPos(params)
      
      if (response.success) {
        // Los productos ya vienen con sus variantes desde el backend
        const productsFormatted = response.data
          .filter(product => product.active)
          .map((product) => {
            return {
              ...product,
              variants: product.variants || [],
              current_stock: product.current_stock || 0, // Campo real de la BD
              stock: product.current_stock || 0, // Alias para compatibilidad
              warehouses: product.warehouse_stock || [],
              is_remote: product.is_remote || false,
              alternative_warehouses: product.alternative_warehouses || [],
              // Precios - asegurar que ambos campos estén disponibles
              price: parseFloat(product.price || product.sale_price || 0),
              sale_price: parseFloat(product.sale_price || product.price || 0),
              cost_price: parseFloat(product.cost_price || 0),
              category_name: product.category_name || 'Sin categoría',
              category_color: product.category_color || '#6b7280',
              image_url: product.image || null
            }
          })
        
        this.products = productsFormatted
      } else {
        console.warn('[appStore] Respuesta sin datos de productos:', response)
      }
    } catch (error) {
      console.error('Error precargando productos:', error)
    } finally {
      this.loading.products = false
    }
  },
  
  async loadCategories() {
    if (this.loading.categories) return

    try {
      this.loading.categories = true
      // Usar endpoint optimizado para POS
      const response = await categoriesService.getForPos()
      if (response.success) {
        // Las categorías ya vienen filtradas desde el backend
        this.categories = response.data
      }
    } catch (error) {
      // console.error('Error precargando categorías:', error)
    } finally {
      this.loading.categories = false
    }
  },
  
  async loadCustomers(force = false) {
    // Si force=true, SIEMPRE recarga sin importar el estado de loading
    if (!force && this.loading.customers) return
    
    try {
      this.loading.customers = true
  // console.log('Precargando clientes...')
      
      const response = await customersService.getAll()
      if (response.success) {
        this.customers = response.data
      }
    } catch (error) {
  // console.error('Error precargando clientes:', error)
    } finally {
      this.loading.customers = false
    }
  },

  async loadPaymentMethods(force = false) {
    // Si force=true, SIEMPRE recarga sin importar el estado de loading
    if (!force && this.loading.paymentMethods) return
    
    try {
      this.loading.paymentMethods = true
      
      const response = await apiClient.get('/payment-methods')
      if (response.data.success) {
        this.paymentMethods = response.data.data
      }
    } catch (error) {
      console.error('Error precargando métodos de pago:', error.response?.data || error.message)
    } finally {
      this.loading.paymentMethods = false
    }
  },

  async loadSystemSettings(force = false) {
    if (this.loading.systemSettings && !force) return
    
    // Super Admin: No cargar settings de tenant
    const user = JSON.parse(localStorage.getItem('user') || '{}');
    if (user?.role === 'superadmin') {
      return;
    }
    
    try {
      this.loading.systemSettings = true
      
      const response = await apiClient.get('/system-settings')
      if (response.data.success) {
        // El backend devuelve un objeto directo, no un array de key-value
        this.systemSettings = response.data.data
        // Guardar el plan del tenant
        this.tenantPlan = response.data.tenant_plan || 'free_trial'
        
        // Guardar nombre del negocio
        this.businessName = response.data.data?.business_name || response.data.tenant?.business_name || 'Mi Tienda'
        
        // IMPORTANTE: Agregar business_name a systemSettings si no viene en settings pero sí en tenant
        if (!this.systemSettings.business_name && response.data.tenant?.business_name) {
          this.systemSettings.business_name = response.data.tenant.business_name
        }
        
        // 🆕 Guardar información completa del tenant (si viene del backend)
        if (response.data.tenant) {
          this.tenant = response.data.tenant
        } else {
          // Crear estructura por defecto si no viene del backend
          this.tenant = {
            plan_type: this.tenantPlan,
            subscription_status: 'active',
            subscription_start_date: new Date().toISOString().split('T')[0],
            subscription_end_date: new Date(Date.now() + 30 * 24 * 60 * 60 * 1000).toISOString().split('T')[0],
            max_users: 1, // Default para free/basic
            max_products: null, // null = ilimitado
            max_invoices: null // null = ilimitado
          }
        }
      }
    } catch (error) {
      // Los errores 403 por suscripción se manejan en SubscriptionExpiredModal
      // NO bloquear aquí - solo establecer valores por defecto
      if (error.response?.status === 403) {
        // Establecer valores por defecto para evitar errores
        this.systemSettings = {
          onboarding_completed: true,
          business_name: 'Mi Negocio'
        }
        this.businessName = 'Mi Negocio'
        this.tenant = {
          id: error.response?.data?.tenant_id || 'unknown',
          plan_type: 'unknown',
          subscription_status: 'unknown'
        }
        return
      }
      // Para otros errores, establecer valores por defecto y continuar
      // Esto evita que la app quede en estado incompleto si el backend falla temporalmente
      console.error('[Store] Error cargando systemSettings:', error.response?.status, error.message)
      if (!this.systemSettings) {
        this.systemSettings = {
          onboarding_completed: false,
          business_name: 'Mi Negocio'
        }
      }
      if (!this.tenantPlan) {
        this.tenantPlan = 'free_trial'
      }
      if (!this.businessName) {
        this.businessName = 'Mi Negocio'
      }
    } finally {
      this.loading.systemSettings = false
    }
  },

  async loadCashSession(force = false) {
    // Si force=true, resetear el estado de loading primero
    if (force && this.loading.cashSession) {
      this.loading.cashSession = false
    }
    
    // Evitar cargas duplicadas solo si NO es force
    if (!force && this.loading.cashSession) return
    
    try {
      this.loading.cashSession = true
      
      const response = await cashSessionService.getCurrentSession()
      
      if (response.success && response.session) {
        this.cashSession.current = response.session
        this.cashSession.hasOpenSession = true
      } else {
        this.cashSession.current = null
        this.cashSession.hasOpenSession = false
      }
      
      this.cashSession.lastChecked = new Date()
      this.cashSession.initialized = true
      
      // Actualizar contexto global para IA de voz
      updateCashContextForAI(this.cashSession.hasOpenSession, this.cashSession.current)
      
    } catch (error) {
      console.error('Error precargando sesión de caja:', error)
      this.cashSession.current = null
      this.cashSession.hasOpenSession = false
      this.cashSession.lastChecked = new Date()
      this.cashSession.initialized = true
      
      // Actualizar contexto global para IA de voz (caja cerrada por error)
      updateCashContextForAI(false, null)
    } finally {
      this.loading.cashSession = false
    }
  },

  // Actualizar estado de sesión de caja (cuando se abre/cierra)
  updateCashSession(session, hasOpen) {
    this.cashSession.current = session
    this.cashSession.hasOpenSession = hasOpen
    this.cashSession.lastChecked = new Date()
    
    // Actualizar contexto global para IA de voz
    updateCashContextForAI(hasOpen, session)
  },

  // Verificar si necesita recargar sesión (cada 5 minutos)
  shouldRefreshCashSession() {
    if (!this.cashSession.lastChecked) return true
    
    const now = new Date()
    const diff = now - this.cashSession.lastChecked
    return diff > 5 * 60 * 1000 // 5 minutos
  },
  
  // Inicializar todos los datos
  async initialize() {
    if (this.initialized) return
    
    // NO inicializar en rutas públicas sin subdominio (registro, login, select-plan)
    const publicRoutes = ['/register', '/login', '/select-plan', '/payment/success', '/payment/failed']
    const currentPath = window.location.pathname
    const hostname = window.location.hostname
    
    // Verificar si estamos en localhost/127.0.0.1 SIN subdominio
    const isMainDomainLocalhost = hostname === 'localhost' || hostname === '127.0.0.1'
    const isPublicRoute = publicRoutes.some(route => currentPath.startsWith(route))
    
    if (isMainDomainLocalhost && isPublicRoute) {
      this.initialized = true
      return
    }
    
    // Verificar si es super admin
    const user = JSON.parse(localStorage.getItem('user') || '{}');
    if (user?.role === 'superadmin' || user?.is_super_admin) {
      this.initialized = true;
      return;
    }
    
    // PRIMERO: Cargar systemSettings para verificar estado de suscripción
    try {
      await this.loadSystemSettings()
    } catch (error) {
      console.error('Error cargando systemSettings:', error.message)
      // Establecer valores por defecto para que la app funcione
      if (!this.systemSettings) {
        this.systemSettings = {
          onboarding_completed: false,
          business_name: 'Mi Negocio'
        }
      }
      if (!this.tenantPlan) {
        this.tenantPlan = 'free_trial'
      }
      if (!this.businessName) {
        this.businessName = 'Mi Negocio'
      }
      // Continuar inicialización aunque falle (los datos se cargarán después)
    }
    
    // Si la suscripción está expirada, NO cargar datos operacionales
    if (this.isSubscriptionExpired) {
      this.initialized = true
      return
    }
    
    // Si la suscripción está activa, cargar todos los datos
    try {
      // Cargar sesión de caja para obtener el warehouse_id
      await this.loadCashSession()
      
      // Cargar productos usando el warehouse_id de la sesión (si existe)
      await this.loadProducts()
      
      // Cargar el resto de datos en paralelo
      await Promise.all([
        this.loadCategories(),
        this.loadCustomers(),
        this.loadPaymentMethods()
      ])
      
      // Cargar datos globales del negocio para la IA (esperar a que termine)
      await loadGlobalBusinessDataForAI()
    } catch (error) {
      console.error('Error cargando datos operacionales:', error.message)
      // Continuar: la app funcionará con datos parciales y se recargarán cuando sea necesario
    }
    
    this.initialized = true
  },
  
  // Método para refrescar datos específicos
  async refresh(dataType) {
    switch (dataType) {
      case 'products':
        this.products = []
        await this.loadProducts()
        break
      case 'categories':
        this.categories = []
        await this.loadCategories()
        break
      case 'customers':
        this.customers = []
        await this.loadCustomers()
        break
      case 'paymentMethods':
        this.paymentMethods = []
        await this.loadPaymentMethods()
        break
      case 'systemSettings':
        this.systemSettings = {}
        await this.loadSystemSettings()
        break
      case 'cashSession':
        this.cashSession.initialized = false
        await this.loadCashSession(true) // force = true
        break
      case 'all':
        this.products = []
        this.categories = []
        this.customers = []
        this.paymentMethods = []
        this.systemSettings = {}
        this.cashSession.initialized = false
        await this.initialize()
        break
    }
  }
  // El polling de suscripción ahora se maneja directamente en SubscriptionExpiredModal
})

// 🆕 Computed properties para acceso simplificado a datos del tenant
export const subscriptionEndDate = computed(() => appStore.tenant?.subscription_end_date || null)
export const subscriptionStatus = computed(() => appStore.tenant?.subscription_status || 'unknown')
export const maxUsers = computed(() => appStore.tenant?.max_users || 1)