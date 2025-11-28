import { ref, reactive } from 'vue'
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
  
  // Estado de sesión de caja (para evitar verificaciones repetidas)
  cashSession: {
    current: null,
    hasOpenSession: false,
    lastChecked: null,
    initialized: false
  },
  
  // Estado de inicialización
  initialized: false,
  
  // Métodos para cargar datos
  async loadProducts() {
    if (this.loading.products) return // Evitar cargas duplicadas

    try {
      this.loading.products = true
      // Usar endpoint optimizado para POS
      const response = await productsService.getForPos()
      if (response.success) {
        this.products = response.data
          .filter(product => product.active && product.stock > 0)
          .map(product => ({
            ...product,
            stock: product.stock,
            price: parseFloat(product.price || 0),
            category_name: product.category_name || 'Sin categoría',
            category_color: product.category_color || '#6b7280'
          }))
      }
    } catch (error) {
      // console.error('❌ Error precargando productos:', error)
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
  
  async loadCustomers() {
    if (this.loading.customers) return
    
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

  async loadPaymentMethods() {
    if (this.loading.paymentMethods) return
    
    try {
      this.loading.paymentMethods = true
      console.log('💳 Precargando métodos de pago...')
      
      const response = await apiClient.get('/payment-methods')
      console.log('📦 Respuesta payment-methods:', response.data)
      if (response.data.success) {
        this.paymentMethods = response.data.data
        console.log('✅ Métodos de pago cargados:', this.paymentMethods.length)
      }
    } catch (error) {
      console.error('❌ Error precargando métodos de pago:', error.response?.data || error.message)
    } finally {
      this.loading.paymentMethods = false
    }
  },

  async loadSystemSettings(force = false) {
    if (this.loading.systemSettings && !force) return
    
    try {
      this.loading.systemSettings = true
      
      const response = await apiClient.get('/system-settings')
      if (response.data.success) {
        // El backend devuelve un objeto directo, no un array de key-value
        this.systemSettings = response.data.data
        console.log('⚙️ System settings loaded/reloaded:', this.systemSettings)
      }
    } catch (error) {
  // console.error('❌ Error precargando configuración:', error)
    } finally {
      this.loading.systemSettings = false
    }
  },

  async loadCashSession() {
    if (this.loading.cashSession) return // Evitar cargas duplicadas
    
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
    
    // Verificar si es super admin
    const user = JSON.parse(localStorage.getItem('user') || '{}');
    if (user?.is_super_admin) {
      console.log('🔐 Super Admin detectado - omitiendo carga de datos de tenant');
      this.initialized = true;
      return;
    }
  
  // console.log('🚀 Inicializando store global...')
    
    // Cargar todos los datos en paralelo (incluyendo sesión de caja)
    await Promise.all([
      this.loadProducts(),
      this.loadCategories(),
      this.loadCustomers(),
      this.loadPaymentMethods(),
      this.loadSystemSettings(),
      this.loadCashSession() // ✅ Cargar sesión de caja SOLO UNA VEZ
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
        await this.loadCashSession()
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
})