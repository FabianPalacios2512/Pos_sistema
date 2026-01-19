import { ref, reactive, computed } from 'vue'
import { productsService } from '../services/productsService.js'
import { categoriesService } from '../services/categoriesService.js'
import { customersService } from '../services/customersService.js'
import { cashSessionService } from '../services/cashSessionService.js'
import apiClient from '../services/apiClient.js'

// 🏪 Store global de la aplicación para datos precargados
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

    // 🔄 Si force=true, resetear el estado de loading por si quedó bloqueado
    if (force) {
      this.loading.products = false
    }

    try {
      this.loading.products = true
      
      // 🏪 Si no se pasa warehouse_id, intentar usar el de la sesión activa
      const targetWarehouseId = warehouseId || this.cashSession.current?.warehouse_id
      
      // Usar endpoint optimizado para POS con filtro de bodega y scope
      const params = targetWarehouseId ? { 
        warehouse_id: targetWarehouseId,
        scope: searchScope 
      } : {}
      
      const response = await productsService.getForPos(params)
      
      if (response.success) {
        // 👗 Los productos ya vienen con sus variantes desde el backend
        const productsFormatted = response.data
          .filter(product => product.active)
          .map((product) => {
            return {
              ...product,
              variants: product.variants || [],
              current_stock: product.current_stock || 0, // ✅ Campo real de la BD
              stock: product.current_stock || 0, // Alias para compatibilidad
              warehouses: product.warehouse_stock || [],
              is_remote: product.is_remote || false,
              alternative_warehouses: product.alternative_warehouses || [],
              price: parseFloat(product.price || 0),
              category_name: product.category_name || 'Sin categoría',
              category_color: product.category_color || '#6b7280',
              image_url: product.image || null
            }
          })
        
        this.products = productsFormatted
        console.log(`✅ [appStore] Productos cargados: ${productsFormatted.length} productos activos`)
      } else {
        console.warn('⚠️ [appStore] Respuesta sin datos de productos:', response)
      }
    } catch (error) {
      console.error('❌ Error precargando productos:', error)
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
      // console.error('❌ Error precargando categorías:', error)
    } finally {
      this.loading.categories = false
    }
  },
  
  async loadCustomers(force = false) {
    // Si force=true, SIEMPRE recarga sin importar el estado de loading
    if (!force && this.loading.customers) return
    
    try {
      this.loading.customers = true
  // console.log('👥 Precargando clientes...')
      
      const response = await customersService.getAll()
      if (response.success) {
        this.customers = response.data
      }
    } catch (error) {
  // console.error('❌ Error precargando clientes:', error)
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
      console.error('❌ Error precargando métodos de pago:', error.response?.data || error.message)
    } finally {
      this.loading.paymentMethods = false
    }
  },

  async loadSystemSettings(force = false) {
    if (this.loading.systemSettings && !force) return
    
    // 👑 Super Admin: No cargar settings de tenant
    const user = JSON.parse(localStorage.getItem('user') || '{}');
    if (user?.role === 'superadmin') {
      console.log('👑 Super Admin - omitiendo loadSystemSettings');
      return;
    }
    
    try {
      this.loading.systemSettings = true
      
      const response = await apiClient.get('/system-settings')
      if (response.data.success) {
        // El backend devuelve un objeto directo, no un array de key-value
        this.systemSettings = response.data.data
        // 🔒 Guardar el plan del tenant
        this.tenantPlan = response.data.tenant_plan || 'free_trial'
        console.log('🔍 [appStore] Plan del tenant cargado:', this.tenantPlan)
        
        // 🏪 Guardar nombre del negocio
        this.businessName = response.data.data?.business_name || response.data.tenant?.business_name || 'Mi Tienda'
        
        // 🏢 IMPORTANTE: Agregar business_name a systemSettings si no viene en settings pero sí en tenant
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
        console.log('⚠️ [Store] Error 403 - dejando que el modal maneje el bloqueo')
        
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
      // Para otros errores, sí lanzarlos
      throw error
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
      
    } catch (error) {
      console.error('❌ Error precargando sesión de caja:', error)
      this.cashSession.current = null
      this.cashSession.hasOpenSession = false
      this.cashSession.lastChecked = new Date()
      this.cashSession.initialized = true
    } finally {
      this.loading.cashSession = false
    }
  },

  // Actualizar estado de sesión de caja (cuando se abre/cierra)
  updateCashSession(session, hasOpen) {
    this.cashSession.current = session
    this.cashSession.hasOpenSession = hasOpen
    this.cashSession.lastChecked = new Date()
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
    
    // 🚫 NO inicializar en rutas públicas sin subdominio (registro, login, select-plan)
    const publicRoutes = ['/register', '/login', '/select-plan', '/payment/success', '/payment/failed']
    const currentPath = window.location.pathname
    const hostname = window.location.hostname
    
    // Verificar si estamos en localhost/127.0.0.1 SIN subdominio
    const isMainDomainLocalhost = hostname === 'localhost' || hostname === '127.0.0.1'
    const isPublicRoute = publicRoutes.some(route => currentPath.startsWith(route))
    
    if (isMainDomainLocalhost && isPublicRoute) {
      console.log('🚫 Ruta pública sin subdominio - omitiendo inicialización del store')
      this.initialized = true
      return
    }
    
    // Verificar si es super admin
    const user = JSON.parse(localStorage.getItem('user') || '{}');
    if (user?.role === 'superadmin' || user?.is_super_admin) {
      console.log('👑 Super Admin detectado - omitiendo carga de datos de tenant');
      this.initialized = true;
      return;
    }
    
    // PRIMERO: Cargar systemSettings para verificar estado de suscripción
    try {
      await this.loadSystemSettings()
    } catch (error) {
      console.error('⚠️ Error cargando systemSettings:', error.message)
      // Continuar aunque falle (puede ser superadmin sin tenant)
    }
    
    // ⛔ Si la suscripción está expirada, NO cargar datos operacionales
    if (this.isSubscriptionExpired) {
      console.log('⛔ Suscripción expirada - omitiendo carga de datos operacionales')
      this.initialized = true
      return
    }
    
    // Si la suscripción está activa, cargar todos los datos
    // 🏪 Cargar sesión de caja para obtener el warehouse_id
    await this.loadCashSession()
    
    // Cargar productos usando el warehouse_id de la sesión (si existe)
    await this.loadProducts()
    
    // Cargar el resto de datos en paralelo
    await Promise.all([
      this.loadCategories(),
      this.loadCustomers(),
      this.loadPaymentMethods()
    ])
    
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