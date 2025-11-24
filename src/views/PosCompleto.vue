<template>
  <!-- Sistema POS Empresarial Completo -->
  <div :class="{ 'dark': isDarkMode }" class="min-h-screen bg-gradient-to-br from-slate-50 to-blue-50/30 dark:bg-gray-900 transition-colors duration-300">
    
    <!-- Sidebar Component -->
    <Sidebar 
      :currentModule="currentModule"
      :sidebarOpen="sidebarOpen"
      :sidebarCollapsed="sidebarCollapsed"
      @change-module="setCurrentModule"
      @toggle-sidebar="toggleSidebar"
      @update:sidebarCollapsed="sidebarCollapsed = $event"
    />

    <!-- Área Principal de Contenido Adaptable -->
    <div class="transition-all duration-300" 
         :class="{
           'lg:ml-60': !sidebarCollapsed,
           'lg:ml-20': sidebarCollapsed
         }">
      
      <!-- Header Corporativo Profesional -->
      <AppHeader
        :module-title="getModuleTitle()"
        :module-description="getModuleDescription()"
        :current-user="currentUser"
        :current-module="currentModule"
        :auto-hide-enabled="autoHideEnabled"
        :sidebar-collapsed="sidebarCollapsed"
        :should-show-settings="shouldShowModule('settings')"
        @toggleSidebar="sidebarOpen = !sidebarOpen"
        @toggleAutoHide="autoHideEnabled = !autoHideEnabled"
        @toggleSidebarCollapsed="sidebarCollapsed = !sidebarCollapsed"
        @navigate-to-settings="setCurrentModule('settings')"
        @logout="handleLogout"
      />




      <!-- Contenido Principal -->
      <main class="flex-1">
        
        <!-- Dashboard -->
        <div v-if="currentModule === 'dashboard'">
          <DashboardView 
            :sales-data="salesData"
            :products-count="productsList.length"
            :low-stock="lowStockProducts"
            :recent-sales="recentSales"
            :notifications="notifications"
            @change-module="setCurrentModule"
          />
        </div>

        <!-- Punto de Venta -->
        <div v-if="currentModule === 'pos'">
          <PosView 
            ref="posViewRef"
            @sale-completed="handleSaleCompleted" 
            @create-invoice="handleCreateQuote"
            @search-quote="handleSearchQuote"
            @cart-status-changed="handleCartStatusChanged"
          />
        </div>

        <!-- Módulos restantes se cargan dinámicamente -->
        <div v-if="currentModule !== 'dashboard' && currentModule !== 'pos'" class="bg-slate-50">
          <component
            :is="currentModuleComponent"
            v-bind="getModuleProps()"
            @change-module="setCurrentModule"
            @open-quotation-in-pos="handleOpenQuotationInPos"
          />
        </div>
        
      </main>
    </div>

    <!-- Overlay para móvil -->
    <div
      v-if="sidebarOpen"
      @click="sidebarOpen = false"
      class="fixed inset-0 bg-black bg-opacity-50 z-40 lg:hidden"
    ></div>

    <!-- Modal de Confirmación - Salir del POS con productos en carrito -->
    <div v-if="showCartWarningModal" class="fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4">
      <div class="bg-white dark:bg-gray-900 rounded-3xl shadow-2xl max-w-lg w-full transform transition-all duration-300 scale-100 border border-orange-200 dark:border-orange-800">
        <!-- Header -->
        <div class="bg-gradient-to-r from-orange-600 via-orange-500 to-red-600 dark:from-orange-900 dark:via-orange-800 dark:to-red-900 p-6 rounded-t-3xl border-b border-orange-300 dark:border-orange-700">
          <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-white/20 dark:bg-orange-900 rounded-xl flex items-center justify-center">
              <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16c-.77.833.192 2.5 1.732 2.5z" />
              </svg>
            </div>
            <div>
              <h3 class="text-2xl font-extrabold text-white tracking-tight drop-shadow">¡Productos Pendientes!</h3>
              <p class="text-orange-100 text-sm mt-1">Tienes productos en el carrito de ventas</p>
            </div>
          </div>
        </div>

        <div class="p-8 space-y-6">
          <div class="text-center space-y-4">
            <div class="w-16 h-16 bg-orange-100 dark:bg-orange-900 rounded-full flex items-center justify-center mx-auto">
              <svg class="w-8 h-8 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-2.5 5M7 13l2.5 5m6-5v6a2 2 0 01-2 2H9a2 2 0 01-2-2v-6m8 0V9a2 2 0 00-2-2H9a2 2 0 00-2 2v4.01" />
              </svg>
            </div>
            
            <div>
              <h4 class="text-xl font-bold text-gray-900 dark:text-gray-100 mb-2">
                Tienes productos pendientes por vender
              </h4>
              <p class="text-gray-600 dark:text-gray-400 leading-relaxed">
                Si sales del módulo POS ahora, perderás todos los productos que tienes en el carrito actual. 
                ¿Qué deseas hacer?
              </p>
            </div>
          </div>

          <div class="flex flex-col sm:flex-row gap-3">
            <button 
              @click="cancelModuleChange"
              class="flex-1 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white font-semibold py-4 px-6 rounded-2xl transition-all duration-300 transform hover:scale-[1.02] active:scale-[0.98] shadow-lg hover:shadow-xl"
            >
              <div class="flex items-center justify-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-2.5 5M7 13l2.5 5m6-5v6a2 2 0 01-2 2H9a2 2 0 01-2-2v-6m8 0V9a2 2 0 00-2-2H9a2 2 0 00-2 2v4.01" />
                </svg>
                <span>Continuar Vendiendo</span>
              </div>
            </button>
            
            <button 
              @click="confirmModuleChange"
              class="flex-1 bg-gradient-to-r from-gray-600 to-gray-700 hover:from-gray-700 hover:to-gray-800 text-white font-semibold py-4 px-6 rounded-2xl transition-all duration-300 transform hover:scale-[1.02] active:scale-[0.98] shadow-lg hover:shadow-xl"
            >
              <div class="flex items-center justify-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                <span>Ir a {{ getModuleName(pendingModule) }}</span>
              </div>
            </button>
          </div>
        </div>
      </div>
    </div>

  </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted, onUnmounted, nextTick, defineAsyncComponent, watch } from 'vue'
import { useRouter } from 'vue-router'
import authService from '../services/authService.js'
import { invoicesService } from '../services/invoicesService.js'
import { customersService } from '../services/customersService.js'
import { inventoryService } from '../services/inventoryService.js'
import { hasPermission, PERMISSIONS } from '../middleware/auth.js'
import { useSessionTimeout } from '../composables/useSessionTimeout.js'

// Importar componente Sidebar
import Sidebar from '../components/Sidebar.vue'

// Importar componente AppHeader
import AppHeader from '../components/AppHeader.vue'

// Router
const router = useRouter()

// Sistema de timeout de sesión
const sessionTimeout = useSessionTimeout()

// Importar componentes de módulos
const DashboardView = defineAsyncComponent(() => import('../components/DashboardView.vue'))
const PosView = defineAsyncComponent(() => import('../components/PosView.vue'))
const ProductsView = defineAsyncComponent(() => import('../components/ProductsView_professional.vue'))
const CustomersView = defineAsyncComponent(() => import('../components/CustomersView_clean.vue'))
const InventoryView = defineAsyncComponent(() => import('../components/InventoryView_professional.vue'))
const IntelligentInventoryView = defineAsyncComponent(() => import('../components/IntelligentInventoryView_Simple.vue'))
const ReportsView = defineAsyncComponent(() => import('../components/ReportsView.vue'))
const ReportsMenuView = defineAsyncComponent(() => import('../components/ReportsMenuView.vue'))
const SettingsView = defineAsyncComponent(() => import('../components/SettingsView.vue'))
const UsersView = defineAsyncComponent(() => import('../components/UsersView.vue'))
const RolesView = defineAsyncComponent(() => import('../components/RolesView.vue'))
const CategoriesView = defineAsyncComponent(() => import('../components/CategoriesView.vue'))
const SuppliersView = defineAsyncComponent(() => import('../components/SuppliersView.vue'))
const InvoicesView = defineAsyncComponent(() => import('../components/InvoicesView.vue'))
const CashAdminView = defineAsyncComponent(() => import('../components/CashAdminView.vue'))
const ReturnsManagementView = defineAsyncComponent(() => import('../components/ReturnsManagementView.vue'))
const UsersManagementView = defineAsyncComponent(() => import('../components/UsersManagementView_WORKING.vue'))

// Componentes temporales para módulos no desarrollados aún
const PlaceholderView = defineAsyncComponent(() => import('../components/PlaceholderView.vue'))

// ===== ESTADO REACTIVO GLOBAL =====

// Configuración UI
const isDarkMode = ref(false)
const sidebarOpen = ref(true)
const sidebarCollapsed = ref(true) // Nueva variable para estado colapsado del sidebar (inicia cerrado)
const currentModule = ref('dashboard')

// Usuario actual - obtenido de la autenticación
const currentUser = ref({
  name: 'Cargando...',
  role: 'user',
  initials: '??',
  permissions: []
})

// ===== MENÚ INTELIGENTE =====
const autoHideEnabled = ref(true) // Habilitar auto-hide
const isMouseNearEdge = ref(false) // Mouse cerca del borde izquierdo
const isMouseOnSidebar = ref(false) // Mouse sobre el sidebar
const autoHideTimeout = ref(null) // Timeout para auto-hide
const edgeDetectionZone = 5 // Zona muy pequeña para "golpe" al borde
const sidebarSafeZone = 20 // Solo 20px extra después del sidebar
const autoHideDelay = 1500 // 1.5 segundos máximo
const lastMouseX = ref(0) // Para detectar movimiento rápido
const mouseSpeed = ref(0) // Velocidad del mouse

// Modal de confirmación para salir del POS con productos en carrito
const showCartWarningModal = ref(false)
const pendingModule = ref('') // Módulo al que se quiere navegar
const cartHasItems = ref(false) // Estado del carrito

// Referencias a componentes
const posViewRef = ref(null)
const pendingPosAction = ref(null) // Acción pendiente para ejecutar cuando PosView se monte

// Notificaciones del sistema
const notifications = ref([
  { id: 1, type: 'warning', message: 'Stock bajo en 3 productos', time: '2 min' },
  { id: 2, type: 'info', message: 'Nueva venta registrada', time: '5 min' },
  { id: 3, type: 'success', message: 'Respaldo completado', time: '1 hour' }
])

// Cliente General - ID fijo para ventas sin cliente específico
const defaultCustomerId = ref(null)

// Obtener el primer módulo accesible para el usuario
const getFirstAccessibleModule = () => {
  const moduleOrder = [
    'dashboard',
    'pos', 
    'products',
    'customers',
    'categories',
    'stock',
    'suppliers',
    'users',
    'roles',
    'reports',
    'settings'
  ]
  
  for (const module of moduleOrder) {
    if (hasModulePermission(module)) {
      return module
    }
  }
  
  // Si no tiene acceso a ningún módulo, usar dashboard por defecto
  return 'dashboard'
}

// Inicializar usuario autenticado
const initializeUser = () => {
  const user = authService.getUser()
  console.log('🔧 [initializeUser] Usuario desde authService:', user)
  
  if (user) {
    currentUser.value = {
      id: user.id,
      name: user.name,
      email: user.email,
      role: user.role, // ✅ Guardar el objeto completo del rol, no solo el nombre
      initials: user.name.split(' ').map(n => n[0]).join('').toUpperCase().substring(0, 2)
    }
    
    console.log('✅ [initializeUser] currentUser actualizado:', currentUser.value)
    console.log('🔑 [initializeUser] Permisos del rol:', currentUser.value.role?.permissions)
    
    // Establecer módulo inicial basándose en los permisos del usuario
    currentModule.value = getFirstAccessibleModule()
    
    // Debug logs removed for production
  } else {
    console.log('❌ [initializeUser] No hay usuario en authService')
  }
}

// Obtener permisos según el rol
const getUserPermissions = (role) => {
  const rolePermissions = {
    admin: ['all'],
    cajero: ['pos', 'customers', 'products_view', 'inventory_view', 'reports_basic', 'returns-management'],
    vendedor: ['pos', 'customers', 'products_view']
  }
  return rolePermissions[role] || []
}

// Verificar si el usuario tiene permisos para un módulo
const hasModulePermission = (module) => {
  console.log('🔍 [hasModulePermission] =====================================')
  console.log('🔍 [hasModulePermission] Verificando módulo:', module)
  console.log('👤 [hasModulePermission] currentUser.value:', currentUser.value)
  
  // Si no hay usuario o rol, denegar acceso
  if (!currentUser.value || !currentUser.value.role) {
    console.log('❌ [hasModulePermission] No user or role for module:', module)
    alert(`DEBUG: No hay usuario o rol\nUsuario: ${currentUser.value}\nRol: ${currentUser.value?.role}`)
    return false
  }

  const userPermissions = currentUser.value.role.permissions || []
  
  console.log(`� [hasModulePermission] User permissions (${userPermissions.length}):`, userPermissions)
  
  // Si tiene permiso ALL o admin, tiene acceso a todo
  if (userPermissions.includes('ALL') || userPermissions.includes('admin')) {
    console.log(`✅ [hasModulePermission] User has ALL/admin permission`)
    return true
  }
  
  // Mapeo de módulos especiales a permisos reales de la base de datos
  const modulePermissionMap = {
    'returns-management': 'returns.view',  // Devoluciones → permiso de ver devoluciones
    'cash-admin': 'settings.view'          // Panel Admin → permiso de configuración
  }
  
  // Si el módulo tiene un mapeo especial, verificar ese permiso específico
  if (modulePermissionMap[module]) {
    const requiredPermission = modulePermissionMap[module]
    const hasAccess = userPermissions.includes(requiredPermission)
    console.log(`${hasAccess ? '✅' : '❌'} [hasModulePermission] Module ${module} mapped to ${requiredPermission}: ${hasAccess}`)
    return hasAccess
  }
  
  // Para módulos normales, verificar si tiene al menos un permiso que comience con el nombre del módulo
  // Por ejemplo: para 'pos', buscar 'pos.view', 'pos.create_sale', etc.
  const hasAccess = userPermissions.some(permission => permission.startsWith(`${module}.`))
  
  console.log(`${hasAccess ? '✅' : '❌'} [hasModulePermission] Module ${module}: ${hasAccess}`)
  
  return hasAccess
}

// Verificar si un módulo debe mostrarse en el menú
const shouldShowModule = (module) => {
  return hasModulePermission(module)
}

// Manejar logout
const handleLogout = async () => {
  if (confirm('¿Está seguro de que desea cerrar sesión?')) {
    await authService.logout()
    router.push('/login')
  }
}

// Cargar facturas y cotizaciones desde la base de datos
const loadInvoices = async () => {
  try {
    // Debug logs removed for production
    const result = await invoicesService.getAllDocuments()
    
    if (result && result.data) {
      // Transformar datos de la API al formato local
      const transformedInvoices = result.data.map(document => ({
        id: document.id,
        invoiceNumber: document.custom_number || document.number || document.invoice_number,
        invoice_number: document.invoice_number, // Campo original de la BD
        date: document.date || document.sale_date,
        due_date: document.due_date,
        customer: document.customer?.name || document.customer_name || 'Cliente General',
        customer_name: document.customer_name,
        customer_id: document.customer_id,
        cashier: document.cashier_name || 'Vendedor',
        items: document.items || document.sale_items || [],
        subtotal: parseFloat(document.subtotal || 0),
        tax: parseFloat(document.tax || document.tax_amount || 0),
        total: parseFloat(document.total || document.total_amount || 0),
        total_amount: document.total_amount,
        created_at: document.created_at,
        payments: [{
          method: document.payment_method || 'Efectivo',
          amount: parseFloat(document.payment_amount || document.total || document.total_amount || 0)
        }],
        status: document.status || 'draft',
        type: document.status === 'quotation' ? 'Cotización' : 'Factura' // Corregir esto para usar status
      }))
      
      invoicesList.value = transformedInvoices
    } else {
      invoicesList.value = []
    }
  } catch (error) {
    console.error('❌ Error cargando facturas desde BD:', error)
    // Si falla, inicializar como array vacío
    invoicesList.value = []
  }
}

// Función para mapear métodos de pago a códigos correctos
const getPaymentMethodCode = (paymentMethod) => {
  // Debug logs removed for production
  
  const methodMap = {
    'Efectivo': 'cash',
    'Tarjeta': 'card', 
    'Transferencia': 'transfer',
    'Transferencia Bancaria': 'transfer',
    'QR': 'qr',
    'Móvil': 'mobile',
    'cash': 'cash',
    'card': 'card',
    'transfer': 'transfer',
    'qr': 'qr',
    'mobile': 'mobile'
  }
  
  const mappedMethod = methodMap[paymentMethod] || 'cash'
  // Debug logs removed for production
  return mappedMethod
}

// Función para asegurar que existe el Cliente General en BD
const ensureDefaultCustomer = async () => {
  try {
    
    // Buscar si ya existe un cliente "Cliente General"
    const customersResponse = await customersService.getAll()
    if (customersResponse.success) {
      const existingGeneral = customersResponse.data.find(customer => 
        customer.name === 'Cliente General' || 
        customer.name === 'General' ||
        customer.email === 'general@sistema.local'
      )
      
      if (existingGeneral) {
        defaultCustomerId.value = existingGeneral.id
        return defaultCustomerId.value
      }
    }
    
    // Si no existe, crearlo
    // Debug logs removed for production
    const newCustomer = {
      name: 'Cliente General',
      email: 'general@sistema.local',
      phone: '000-000-0000',
      address: 'Dirección General',
      identification: '00000000000',
      active: true
    }
    
    const createResponse = await customersService.create(newCustomer)
    if (createResponse.success) {
      defaultCustomerId.value = createResponse.data.id
      return defaultCustomerId.value
    } else {
      throw new Error('No se pudo crear el Cliente General')
    }
    
  } catch (error) {
    console.error('❌ Error manejando Cliente General:', error)
    // Como fallback, usar ID 7 (Cliente General)
    defaultCustomerId.value = 7
    return defaultCustomerId.value
  }
}

// 🏭 Actualizar stock de productos vendidos
const updateSoldProductsStock = async (soldItems) => {
  
  try {
    // Procesar cada producto vendido
    for (const item of soldItems) {
      // Debug logs removed for production
      
      const stockData = {
        quantity: -parseInt(item.quantity), // Negativo porque es una salida/venta
        type: 'sale',
        reference: `Venta POS - ${item.quantity} unidades vendidas`
      }
      
      try {
        const response = await inventoryService.updateProductStock(item.id, stockData)
      } catch (productError) {
        console.error(`❌ Error actualizando stock del producto ${item.name} (ID: ${item.id}):`, productError)
        // Continuar con los demás productos aunque uno falle
      }
    }
    
  } catch (error) {
    console.error('❌ Error general actualizando stock de productos vendidos:', error)
    // No lanzamos el error para que no interrumpa la venta
  }
}

// Manejar ventas completadas desde el POS
const handleSaleCompleted = async (saleData) => {
  // Debug logs removed for production
  
  try {
    // La factura ya fue creada en PosView.vue, solo agregamos a la lista local
    // Debug logs removed for production
    
    const newInvoice = {
      id: saleData.id || Date.now(), // ID real del backend o temporal
      invoiceNumber: saleData.invoiceNumber, // Ya tiene el número correcto del backend
      date: saleData.date,
      due_date: saleData.due_date,
      customer: saleData.customer || 'Cliente General',
      cashier: saleData.cashier,
      items: saleData.items,
      subtotal: saleData.subtotal,
      tax: saleData.tax,
      total: saleData.total,
      payments: saleData.payments,
      status: 'paid', // Las ventas del POS están pagadas
      type: 'Factura'
    }
    
    // Agregar la venta a la lista de facturas local
    invoicesList.value.unshift(newInvoice)
    
    // Debug logs removed for production
    
    // Recargar facturas desde BD para asegurar sincronización  
    setTimeout(() => {
      loadInvoices()
    }, 1000)
    
  } catch (error) {
    console.error('❌ Error procesando venta completada:', error)
  }
  
  // Agregar también a ventas recientes
  recentSales.value.unshift({
    id: saleData.id,
    date: saleData.date,
    customer: saleData.customer || 'Cliente General',
    items: saleData.items.length,
    total: saleData.total,
    payment_method: saleData.payments[0]?.method || 'Efectivo',
    cashier: saleData.cashier,
    status: 'Completada'
  })
  
  // Mantener solo las últimas 20 ventas
  if (recentSales.value.length > 20) {
    recentSales.value = recentSales.value.slice(0, 20)
  }
  
  // Actualizar estadísticas del dashboard
  salesData.value.today.sales += 1
  salesData.value.today.revenue += saleData.total
  
  // Ya no usar console.log después del error handling
}

// Manejar creación de cotizaciones
const handleCreateQuote = async (quoteData) => {
  try {
    
    // Usar el mismo servicio de facturas pero con estado de cotización
    const result = await invoicesService.createQuote(quoteData)
    
    if (result.success) {
      
      // Actualizar la lista de facturas para incluir la nueva cotización
      await loadInvoices()
      
      // Mostrar modal de cotización exitosa con el código correcto del backend
      if (result.data && result.data.number) {
        // Construir datos para el modal usando la información del backend
        const modalQuotationData = {
          code: result.data.number, // Usar código real del backend (ej: COT-000005)
          customer: result.data.customer?.name || 'Cliente General',
          total: result.data.total,
          items: result.data.invoice_items?.map(item => ({
            name: item.product_name,
            quantity: item.quantity,
            price: item.unit_price,
            subtotal: item.subtotal
          })) || [],
          message: 'El cliente puede usar este código para realizar la compra posteriormente.',
          unavailableItems: []
        }
        
        // Mostrar el modal directamente aquí (no necesitamos emit)
        // Simulamos el showQuotationModal del PosView
        alert(`✅ Cotización creada exitosamente\n\nCódigo: ${modalQuotationData.code}\nCliente: ${modalQuotationData.customer}\nTotal: $${modalQuotationData.total.toLocaleString()}`)
      }
      
      return { success: true, data: result.data }
    } else {
      throw new Error(result.message || 'Error al crear cotización')
    }
  } catch (error) {
    console.error('❌ Error al crear cotización:', error)
    return { success: false, error: error.message }
  }
}

// Manejar búsqueda de cotizaciones
const handleSearchQuote = async (quoteCode) => {
  try {
    // Debug logs removed for production
    
    const result = await invoicesService.searchQuote(quoteCode)
    // Debug logs removed for production
    
    if (result.success && result.data && result.data.length > 0) {
      // Debug logs removed for production
      
      // Encontrar la cotización específica por código
      const quote = result.data.find(invoice => 
        invoice.invoice_number === quoteCode && invoice.status === 'quotation'
      )
      
      // Debug logs removed for production
      
      if (quote) {
        // Debug logs removed for production
        return { success: true, data: quote }
      } else {
        // Debug logs removed for production
        return { success: false, message: 'Cotización no encontrada' }
      }
    } else {
      // Debug logs removed for production
      return { success: false, message: 'Cotización no encontrada' }
    }
  } catch (error) {
    console.error('❌ [handleSearchQuote] Error al buscar cotización:', error)
    return { success: false, error: error.message }
  }
}

// Manejar cambios en el estado del carrito
const handleCartStatusChanged = (hasItems) => {
  cartHasItems.value = hasItems;
}

// Manejar apertura de cotización desde módulo de facturas
const handleOpenQuotationInPos = (quotationData) => {
  
  // Cambiar al módulo POS
  setCurrentModule('pos')
  
  // Emitir evento para que PosView cargue la cotización
  // Usamos nextTick para asegurar que el componente POS esté montado
  nextTick(() => {
    // El PosView debe tener un método para cargar una cotización específica
    // Podemos usar un ref o store compartido para pasar los datos
    window.quotationToLoad = quotationData
  })
}

// ===== DATOS PRINCIPALES DEL SISTEMA =====

// Categorías de productos
const categoriesList = ref([
  { id: 1, name: 'Snacks', description: 'Productos de snacks y botanas', color: '#f59e0b', active: true },
  { id: 2, name: 'Bebidas', description: 'Bebidas frías y calientes', color: '#3b82f6', active: true },
  { id: 3, name: 'Hogar', description: 'Productos para el hogar', color: '#10b981', active: true },
  { id: 4, name: 'Farmacia', description: 'Productos farmacéuticos básicos', color: '#ef4444', active: true },
  { id: 5, name: 'Dulces', description: 'Dulces y confitería', color: '#8b5cf6', active: true },
  { id: 6, name: 'Panadería', description: 'Pan y productos de panadería', color: '#f97316', active: true },
  { id: 7, name: 'Lácteos', description: 'Productos lácteos y derivados', color: '#06b6d4', active: true },
  { id: 8, name: 'Electrónicos', description: 'Accesorios electrónicos básicos', color: '#6366f1', active: true }
])

// Lista completa de productos con más datos
const productsList = ref([
  {
    id: 1, 
    name: 'Papas Fritas Original',
    description: 'Papas fritas sabor natural, 150g',
    category_id: 1,
    category: 'Snacks',
    barcode: '7702001234567',
    price: 2500,
    cost: 1500,
    stock: 45,
    min_stock: 10,
    image: 'https://images.unsplash.com/photo-1566478989037-eec170784d0b?w=300&h=300&fit=crop',
    active: true,
    supplier_id: 1,
    created_at: '2024-01-15',
    updated_at: '2024-10-13'
  },
  {
    id: 2,
    name: 'Coca Cola 350ml',
    description: 'Bebida gaseosa Coca Cola 350ml',
    category_id: 2,
    category: 'Bebidas',
    barcode: '7702002345678',
    price: 2800,
    cost: 1800,
    stock: 120,
    min_stock: 20,
    image: 'https://images.unsplash.com/photo-1561758033-d89a9ad46330?w=300&h=300&fit=crop',
    active: true,
    supplier_id: 2,
    created_at: '2024-01-15',
    updated_at: '2024-10-13'
  },
  {
    id: 3,
    name: 'Papel Higiénico x4',
    description: 'Pack de 4 rollos de papel higiénico',
    category_id: 3,
    category: 'Hogar',
    barcode: '7702003456789',
    price: 8500,
    cost: 5200,
    stock: 8, // Stock bajo
    min_stock: 15,
    image: 'https://images.unsplash.com/photo-1584464491033-06628f3a6b7b?w=300&h=300&fit=crop',
    active: true,
    supplier_id: 3,
    created_at: '2024-01-15',
    updated_at: '2024-10-13'
  },
  {
    id: 4,
    name: 'Acetaminofén 500mg',
    description: 'Caja de 20 tabletas de acetaminofén',
    category_id: 4,
    category: 'Farmacia',
    barcode: '7702004567890',
    price: 3800,
    cost: 2300,
    stock: 25,
    min_stock: 10,
    image: 'https://images.unsplash.com/photo-1584308666744-24d5c474f2ae?w=300&h=300&fit=crop',
    active: true,
    supplier_id: 4,
    created_at: '2024-01-15',
    updated_at: '2024-10-13'
  },
  {
    id: 5,
    name: 'Chocolatina Jet',
    description: 'Barra de chocolate con leche 50g',
    category_id: 5,
    category: 'Dulces',
    barcode: '7702005678901',
    price: 2100,
    cost: 1300,
    stock: 67,
    min_stock: 20,
    image: 'https://images.unsplash.com/photo-1606312619070-d48b4c652a52?w=300&h=300&fit=crop',
    active: true,
    supplier_id: 5,
    created_at: '2024-01-15',
    updated_at: '2024-10-13'
  }
])

// Lista de proveedores
const suppliersList = ref([
  {
    id: 1,
    name: 'Distribuidora Snacks S.A.',
    contact_name: 'Carlos Mendoza',
    email: 'carlos@snacksdist.com',
    phone: '+57 301 234 5678',
    address: 'Calle 123 #45-67, Bogotá',
    tax_id: '900123456-7',
    payment_terms: '30 días',
    active: true,
    products_count: 15,
    total_purchases: 2500000,
    created_at: '2024-01-10'
  },
  {
    id: 2,
    name: 'Bebidas del Valle Ltda.',
    contact_name: 'María González',
    email: 'maria@bebidasvalle.com',
    phone: '+57 302 345 6789',
    address: 'Carrera 78 #12-34, Medellín',
    tax_id: '900234567-8',
    payment_terms: '15 días',
    active: true,
    products_count: 25,
    total_purchases: 4800000,
    created_at: '2024-01-12'
  },
  {
    id: 3,
    name: 'Productos Hogar Nacional',
    contact_name: 'Jorge Ramírez',
    email: 'jorge@hogarnacional.com',
    phone: '+57 303 456 7890',
    address: 'Avenida 45 #67-89, Cali',
    tax_id: '900345678-9',
    payment_terms: '45 días',
    active: true,
    products_count: 30,
    total_purchases: 3200000,
    created_at: '2024-01-15'
  }
])

// Lista de clientes
const customersList = ref([
  {
    id: 1,
    name: 'Ana Patricia López',
    document_type: 'CC',
    document_number: '12345678',
    email: 'ana.lopez@email.com',
    phone: '+57 320 123 4567',
    address: 'Calle 45 #12-34, Apt 501',
    birth_date: '1985-03-15',
    total_purchases: 850000,
    last_purchase: '2024-10-10',
    loyalty_points: 425,
    active: true,
    created_at: '2024-02-01'
  },
  {
    id: 2,
    name: 'Roberto Silva Hernández',
    document_type: 'CC',
    document_number: '87654321',
    email: 'roberto.silva@email.com',
    phone: '+57 321 234 5678',
    address: 'Carrera 23 #56-78',
    birth_date: '1978-08-22',
    total_purchases: 1200000,
    last_purchase: '2024-10-12',
    loyalty_points: 600,
    active: true,
    created_at: '2024-01-25'
  }
])

// Usuarios del sistema
const usersList = ref([
  {
    id: 1,
    name: 'Administrador Principal',
    username: 'admin',
    email: 'admin@tienda.com',
    role: 'Administrador',
    permissions: ['all'],
    active: true,
    last_login: '2024-10-13 09:30:00',
    created_at: '2024-01-01'
  },
  {
    id: 2,
    name: 'Maria Fernanda Castro',
    username: 'maria.castro',
    email: 'maria@tienda.com',
    role: 'Cajera',
    permissions: ['pos', 'customers', 'reports_view'],
    active: true,
    last_login: '2024-10-13 08:45:00',
    created_at: '2024-02-15'
  },
  {
    id: 3,
    name: 'Juan Carlos Pérez',
    username: 'juan.perez',
    email: 'juan@tienda.com',
    role: 'Supervisor',
    permissions: ['pos', 'inventory', 'customers', 'reports'],
    active: true,
    last_login: '2024-10-12 18:20:00',
    created_at: '2024-03-01'
  }
])

// Lista de roles del sistema
const rolesList = ref([
  {
    id: 1,
    name: 'Administrador',
    description: 'Acceso completo a todas las funciones del sistema',
    permissions: ['all'],
    users_count: 1,
    active: true,
    created_at: '2024-01-01'
  },
  {
    id: 2,
    name: 'Cajera',
    description: 'Acceso al punto de venta y gestión básica de clientes',
    permissions: ['pos', 'customers', 'reports_view'],
    users_count: 3,
    active: true,
    created_at: '2024-01-01'
  },
  {
    id: 3,
    name: 'Supervisor',
    description: 'Acceso a inventario, ventas y reportes',
    permissions: ['pos', 'inventory', 'customers', 'reports', 'products_view'],
    users_count: 2,
    active: true,
    created_at: '2024-01-01'
  }
])

// Módulos disponibles del sistema
const availableModules = ref([
  {
    id: 'dashboard',
    name: 'Panel de Control',
    description: 'Vista general del negocio y métricas clave',
    category: 'Principal'
  },
  {
    id: 'pos',
    name: 'Punto de Venta',
    description: 'Realizar ventas y gestionar transacciones',
    category: 'Ventas'
  },
  {
    id: 'invoices',
    name: 'Facturación',
    description: 'Generar facturas, cotizaciones y documentos',
    category: 'Ventas'
  },
  {
    id: 'products',
    name: 'Productos',
    description: 'Gestionar catálogo de productos',
    category: 'Inventario'
  },
  {
    id: 'categories',
    name: 'Categorías',
    description: 'Organizar productos por categorías',
    category: 'Inventario'
  },
  {
    id: 'stock',
    name: 'Control de Stock',
    description: 'Controlar entradas, salidas y niveles',
    category: 'Inventario'
  },
  {
    id: 'suppliers',
    name: 'Proveedores',
    description: 'Administrar proveedores y órdenes',
    category: 'Relaciones'
  },
  {
    id: 'customers',
    name: 'Clientes',
    description: 'Gestionar base de datos de clientes',
    category: 'Relaciones'
  },
  {
    id: 'users',
    name: 'Usuarios',
    description: 'Administrar empleados del sistema',
    category: 'Administración'
  },
  {
    id: 'cash-admin',
    name: 'Panel Administrativo',
    description: 'Gestión y monitoreo general del sistema',
    category: 'Administración'
  },
  {
    id: 'roles',
    name: 'Gestión de Roles',
    description: 'Crear roles y asignar permisos',
    category: 'Administración'
  },
  {
    id: 'reports',
    name: 'Reportes',
    description: 'Analizar ventas, inventario y rendimiento',
    category: 'Administración'
  },
  {
    id: 'returns-management',
    name: 'Gestión de Devoluciones',
    description: 'Control y seguimiento completo de devoluciones',
    category: 'Ventas'
  },
  {
    id: 'settings',
    name: 'Configuración',
    description: 'Configurar empresa y preferencias',
    category: 'Administración'
  }
])

// Ventas recientes
const recentSales = ref([
  {
    id: 1001,
    date: '2024-10-13 14:30:00',
    customer: 'Ana Patricia López',
    items: 3,
    total: 8300,
    payment_method: 'Tarjeta',
    cashier: 'Maria Castro',
    status: 'Completada'
  },
  {
    id: 1002,
    date: '2024-10-13 13:45:00',
    customer: 'Cliente General',
    items: 1,
    total: 2800,
    payment_method: 'Efectivo',
    cashier: 'Maria Castro',
    status: 'Completada'
  },
  {
    id: 1003,
    date: '2024-10-13 12:20:00',
    customer: 'Roberto Silva',
    items: 5,
    total: 15600,
    payment_method: 'Transferencia',
    cashier: 'Juan Pérez',
    status: 'Completada'
  }
])

// Lista de facturas/ventas del POS
const invoicesList = ref([])

// Datos de ventas para reportes
const salesData = ref({
  today: {
    sales: 45,
    revenue: 1250000,
    items_sold: 156,
    average_ticket: 27777
  },
  week: {
    sales: 312,
    revenue: 8640000,
    items_sold: 1089,
    average_ticket: 27692
  },
  month: {
    sales: 1287,
    revenue: 35680000,
    items_sold: 4523,
    average_ticket: 27731
  }
})

// ===== COMPUTED PROPERTIES =====

// Productos con stock bajo
const lowStockProducts = computed(() => {
  return productsList.value.filter(product => product.stock <= product.min_stock)
})

// Componente dinámico basado en el módulo actual
const currentModuleComponent = computed(() => {
  const moduleComponents = {
    products: ProductsView,
    categories: CategoriesView,
    stock: InventoryView,
    intelligent_inventory: IntelligentInventoryView,
    suppliers: SuppliersView,
    customers: CustomersView,
    invoices: InvoicesView,
    users: UsersManagementView,
    roles: RolesView,
    reports: ReportsMenuView,
    settings: SettingsView,
    'cash-admin': CashAdminView,
    'returns-management': ReturnsManagementView
  }
  return moduleComponents[currentModule.value] || null
})

// ===== MÉTODOS =====

// Alternar modo oscuro
const toggleDarkMode = () => {
  isDarkMode.value = !isDarkMode.value
  localStorage.setItem('pos-dark-mode', isDarkMode.value.toString())
}

// Función para toggle del sidebar
const toggleSidebar = () => {
  sidebarCollapsed.value = !sidebarCollapsed.value
}

// Cambiar módulo actual
const setCurrentModule = (module, options = {}) => {
  // Verificar permisos antes de cambiar módulo
  if (!hasModulePermission(module)) {
    alert('No tienes permisos para acceder a este módulo')
    return
  }
  
  // Si está en POS y hay productos en el carrito, mostrar confirmación
  if (currentModule.value === 'pos' && cartHasItems.value && module !== 'pos') {
    pendingModule.value = module
    showCartWarningModal.value = true
    return
  }

  currentModule.value = module
  
  // Manejar acciones especiales después del cambio de módulo
  if (options.action && module === 'pos') {
    console.log('🔄 Acción especial detectada:', options.action, 'para módulo:', module)
    if (options.action === 'open-close-cash-modal') {
      console.log('🔄 Guardando acción pendiente: openCloseCashModal')
      // Guardar la acción para ejecutar cuando el componente se monte
      pendingPosAction.value = 'openCloseCashModal'
    } else if (options.action === 'open-open-cash-modal') {
      console.log('🔄 Guardando acción pendiente: openOpenCashModal')
      // Guardar la acción para ejecutar cuando el componente se monte
      pendingPosAction.value = 'openOpenCashModal'
    }
  }
  
  // Cerrar sidebar en móvil después de selección
  if (window.innerWidth < 1024) {
    sidebarOpen.value = false
  }
}

// Función para ejecutar acciones en el componente PosView
const triggerPosAction = async (action) => {
  console.log('🎯 triggerPosAction llamado con acción:', action)
  console.log('🎯 posViewRef.value:', posViewRef.value)
  console.log('🎯 currentModule.value:', currentModule.value)
  
  if (posViewRef.value && currentModule.value === 'pos') {
    try {
      switch (action) {
        case 'openCloseCashModal':
          console.log('🎯 Intentando abrir modal de cerrar caja')
          // Acceder a la función del PosView para abrir el modal de cerrar caja
          if (posViewRef.value.openCloseCashModal) {
            console.log('🎯 Llamando a openCloseCashModal() directamente')
            await posViewRef.value.openCloseCashModal()
          } else if (posViewRef.value.showCloseCashModal) {
            console.log('🎯 Llamando a showCloseCashModal() como alternativa')
            await posViewRef.value.showCloseCashModal()
          } else {
            console.log('🎯 Intentando acceso directo a showCashCloseModal')
            // Fallback: acceder directamente a la variable reactiva
            if (posViewRef.value.showCashCloseModal !== undefined) {
              posViewRef.value.showCashCloseModal = true
              console.log('🎯 showCashCloseModal asignado a true')
            } else {
              console.warn('🎯 ❌ No se encontró ninguna forma de abrir el modal')
            }
          }
          break
        case 'openOpenCashModal':
          console.log('🎯 Intentando abrir modal de abrir caja')
          // Acceder a la función del PosView para abrir el modal de abrir caja
          if (posViewRef.value.openOpenCashModal) {
            console.log('🎯 Llamando a openOpenCashModal() directamente')
            posViewRef.value.openOpenCashModal()
          } else if (posViewRef.value.showOpenCashModal) {
            console.log('🎯 Llamando a showOpenCashModal() como alternativa')
            posViewRef.value.showOpenCashModal()
          } else {
            console.log('🎯 Intentando acceso directo a showCashOpenModal')
            // Fallback: acceder directamente a la variable reactiva
            if (posViewRef.value.showCashOpenModal !== undefined) {
              posViewRef.value.showCashOpenModal = true
              console.log('🎯 showCashOpenModal asignado a true')
            } else {
              console.warn('🎯 ❌ No se encontró ninguna forma de abrir el modal de abrir caja')
            }
          }
          break
        default:
          console.warn(`Acción no reconocida en PosView: ${action}`)
      }
    } catch (error) {
      console.error('Error ejecutando acción en PosView:', error)
    }
  }
}

// Función para confirmar el cambio de módulo perdiendo los productos del carrito
const confirmModuleChange = () => {
  currentModule.value = pendingModule.value
  showCartWarningModal.value = false
  pendingModule.value = ''
  // Cerrar sidebar en móvil después de selección
  if (window.innerWidth < 1024) {
    sidebarOpen.value = false
  }
}

// Función para cancelar el cambio de módulo y seguir vendiendo
const cancelModuleChange = () => {
  showCartWarningModal.value = false
  pendingModule.value = ''
}

// Función para obtener el nombre legible del módulo
const getModuleName = (module) => {
  const moduleNames = {
    'dashboard': 'Dashboard',
    'pos': 'Punto de Venta',
    'invoices': 'Facturas',
    'products': 'Productos',
    'categories': 'Categorías',
    'stock': 'Inventario',
    'intelligent_inventory': 'Inventario Inteligente',
    'customers': 'Clientes',
    'suppliers': 'Proveedores',
    'reports': 'Reportes',
    'settings': 'Configuración'
  }
  return moduleNames[module] || 'Módulo'
}

// Obtener clases CSS para elementos del menú mejoradas
const getMenuItemClass = (module) => {
  const baseClasses = 'w-full flex items-center text-left transition-all duration-300 rounded-xl font-medium relative group';
  const expandedClasses = 'px-4 py-3 text-sm';
  const collapsedClasses = 'px-3 py-3 justify-center';
  
  const isActive = currentModule.value === module;
  
  let classes = `${baseClasses} ${sidebarCollapsed.value ? collapsedClasses : expandedClasses}`;
  
  if (isActive) {
    classes += ' bg-gradient-to-r from-blue-600 to-purple-600 text-white shadow-lg transform scale-[1.02] ring-2 ring-blue-200';
  } else {
    classes += ' text-gray-700 dark:text-gray-300 hover:bg-gradient-to-r hover:from-blue-50 hover:to-purple-50 dark:hover:from-gray-700 dark:hover:to-gray-600 hover:text-blue-700 dark:hover:text-blue-300 hover:shadow-md hover:scale-[1.01]';
  }
  
  return classes;
}

// Funciones CSS mejoradas para diseño profesional
const getPOSMenuClass = (module, isPrimary = false) => {
  const isActive = currentModule.value === module;
  
  let baseClasses = 'group w-full flex items-center transition-all duration-300 relative overflow-hidden';
  
  // Ajustar padding y justificación según el estado del sidebar
  if (sidebarCollapsed.value) {
    baseClasses += ' justify-center p-2 rounded-xl';
  } else {
    baseClasses += ' p-2.5 rounded-xl mx-1';
  }
  
  if (isActive) {
    if (isPrimary) {
      // Botón primario activo - diseño premium
      return baseClasses + ' bg-gradient-to-br from-emerald-500 via-blue-600 to-indigo-700 text-white shadow-2xl shadow-blue-600/40 ring-2 ring-emerald-400/30 transform scale-[1.02] border border-white/10';
    } else {
      // Botón secundario activo - elegante y profesional
      return baseClasses + ' bg-gradient-to-br from-blue-600/90 via-indigo-600/90 to-blue-700/90 text-white shadow-xl shadow-blue-500/30 ring-1 ring-blue-400/30 border border-white/10 backdrop-blur-sm';
    }
  } else {
    // Estado inactivo - sutil y moderno
    return baseClasses + ' bg-slate-800/20 hover:bg-gradient-to-br hover:from-slate-700/40 hover:via-slate-600/50 hover:to-slate-700/40 text-slate-300 hover:text-white hover:shadow-lg backdrop-blur-sm border border-slate-700/20 hover:border-slate-500/40 hover:scale-[1.01] hover:ring-1 hover:ring-slate-500/20';
  }
};

const getPOSIconClass = (module, isPrimary = false) => {
  const isActive = currentModule.value === module;
  
  // Clase base con mejor centrado y transiciones suaves
  let baseClasses = 'relative flex items-center justify-center transition-all duration-300 group-hover:scale-105';
  
  // Ajustar tamaño según el estado del sidebar
  if (sidebarCollapsed.value) {
    baseClasses += ' w-8 h-8 rounded-lg';
  } else {
    baseClasses += ' w-9 h-9 rounded-lg p-2';
  }
  
  if (isActive) {
    if (isPrimary) {
      // Icono primario activo - destacado
      return baseClasses + ' bg-white/25 text-white shadow-xl ring-2 ring-white/20 backdrop-blur-sm';
    } else {
      // Icono secundario activo - profesional
      return baseClasses + ' bg-white/20 text-white shadow-lg ring-1 ring-white/15 backdrop-blur-sm';
    }
  } else {
    // Icono inactivo - sutil con hover elegante
    return baseClasses + ' bg-slate-700/25 group-hover:bg-slate-600/40 text-slate-400 group-hover:text-white group-hover:shadow-md group-hover:ring-1 group-hover:ring-slate-500/30 backdrop-blur-sm';
  }
};

const getUserInitials = () => {
  const name = currentUser.value?.name || 'Usuario';
  return name.split(' ').map(n => n[0]).join('').substring(0, 2).toUpperCase();
};

// Función helper para renderizar botones del menú adaptables
const renderMenuButton = (module, icon, title, subtitle, isPrimary = false, badge = null) => {
  return {
    module,
    icon,
    title,
    subtitle,
    isPrimary,
    badge,
    classes: getPOSMenuClass(module, isPrimary),
    iconClasses: getPOSIconClass(module, isPrimary)
  };
};

// Obtener título del módulo actual
const getModuleTitle = () => {
  if (currentModule.value === 'pos') {
    return 'Punto de Venta'
  }
  // Para otros módulos, mostrar nombre bonito del proyecto sin emoji
  return '105 POS Pro'
}

// Obtener descripción del módulo actual
const getModuleDescription = () => {
  if (currentModule.value === 'pos') {
    return 'Realizar ventas y gestionar transacciones'
  }
  // Descripciones profesionales sin emojis
  const descriptions = {
    dashboard: 'Panel de Control • Métricas en tiempo real',
    products: 'Catálogo de Productos • Gestión inteligente de inventario',
    categories: 'Categorías • Organización perfecta de productos',
    stock: 'Control de Stock • Inventario optimizado',
    suppliers: 'Proveedores • Gestión de compras y relaciones comerciales',
    customers: 'Base de Clientes • CRM integrado y seguimiento',
    invoices: 'Facturación • Historial completo de ventas',
    'returns-management': 'Devoluciones • Control y seguimiento administrativo',
    users: 'Usuarios • Gestión de accesos y perfiles',
    roles: 'Roles y Permisos • Control de seguridad avanzado',
    reports: 'Reportes • Análisis inteligente de datos',
    settings: 'Configuración • Personalización del sistema'
  }
  return descriptions[currentModule.value] || '✨ Sistema de gestión empresarial avanzado'
}

// Obtener props para el módulo actual
const getModuleProps = () => {
  const baseProps = {
    moduleName: currentModule.value,
    onNavigate: setCurrentModule
  }
  
  const specificProps = {
    products: { products: productsList.value, categories: categoriesList.value },
    categories: { categories: categoriesList.value },
    stock: { products: productsList.value, movements: [] },
    suppliers: { suppliers: suppliersList.value },
    customers: { customers: customersList.value },
    invoices: { invoices: invoicesList.value, customers: customersList.value, products: productsList.value },
    users: {},
    roles: { modules: availableModules.value },
    reports: { salesData: salesData.value, products: productsList.value },
    settings: { companyInfo: {}, systemSettings: {} }
  }
  
  return { ...baseProps, ...(specificProps[currentModule.value] || {}) }
}

// Cargar configuración guardada
const loadSettings = () => {
  try {
    const savedDarkMode = localStorage.getItem('pos-dark-mode')
    if (savedDarkMode !== null) {
      isDarkMode.value = savedDarkMode === 'true'
    }
  } catch (error) {
    console.log('Error loading settings:', error)
  }
}

// ===== WATCHERS =====

// Watcher para ejecutar acciones pendientes cuando el componente PosView se monte
watch(posViewRef, async (newRef) => {
  if (newRef && pendingPosAction.value) {
    console.log('🔄 PosView montado, ejecutando acción pendiente:', pendingPosAction.value)
    // Ejecutar acción pendiente con un pequeño delay para asegurar que esté completamente inicializado
    setTimeout(async () => {
      await triggerPosAction(pendingPosAction.value)
      pendingPosAction.value = null // Limpiar acción pendiente
    }, 50)
  }
})

// ===== LIFECYCLE HOOKS =====

let timeInterval

onMounted(() => {
  // Inicializar usuario autenticado
  initializeUser()
  
  // Verificar autenticación
  if (!authService.isAuthenticated()) {
    router.push('/login')
    return
  }
  
  // Configurar token de autenticación temporal para desarrollo (si no existe uno real)
  if (!localStorage.getItem('auth_token')) {
    localStorage.setItem('auth_token', '2|9bqn3alwfSdQZVAYFp10z4RqcFHUcS6X8IiFMIJDb632dab8')
  }
  
  // Cargar configuración
  loadSettings()
  
  // Cargar facturas desde base de datos
  loadInvoices()
  
  // Inicializar Cliente General para ventas sin cliente específico
  ensureDefaultCustomer()
  
  // Ajustar sidebar según tamaño de pantalla
  const handleResize = () => {
    if (window.innerWidth >= 1024) {
      sidebarOpen.value = true
    }
  }
  
  window.addEventListener('resize', handleResize)
  handleResize()
  
  // Inicializar variables del menú inteligente
  lastMouseX.value = 0
  
  // ===== MENÚ INTELIGENTE - AUTO-HIDE =====
  const handleMouseMove = (event) => {
    if (!autoHideEnabled.value) return
    
    // Calcular velocidad del mouse para detectar "golpe"
    const currentMouseX = event.clientX
    mouseSpeed.value = Math.abs(currentMouseX - lastMouseX.value)
    lastMouseX.value = currentMouseX
    
    // Detección de borde y zona segura más precisas
    const nearEdge = currentMouseX <= edgeDetectionZone
    const sidebarWidth = sidebarCollapsed.value ? 80 : 288 // w-20 vs w-72
    const inSidebarSafeZone = currentMouseX <= sidebarWidth + sidebarSafeZone // Solo +20px
    
    isMouseNearEdge.value = nearEdge
    
    // ACTIVACIÓN: Solo expandir con "golpe" rápido al borde (velocidad > 10px)
    if (nearEdge && sidebarCollapsed.value && mouseSpeed.value > 10) {
      sidebarCollapsed.value = false
      console.log('Expandiendo menú por golpe rápido al borde, velocidad:', mouseSpeed.value)
    }
    
    // ZONA SEGURA: Sidebar + pequeño margen
    if (isMouseOnSidebar.value || inSidebarSafeZone) {
      if (autoHideTimeout.value) {
        clearTimeout(autoHideTimeout.value)
        autoHideTimeout.value = null
      }
      return
    }
    
    // ZONA DE CIERRE: Fuera de la zona segura
    if (!sidebarCollapsed.value && !isMouseOnSidebar.value) {
      if (autoHideTimeout.value) {
        clearTimeout(autoHideTimeout.value)
      }
      
      autoHideTimeout.value = setTimeout(() => {
        if (!isMouseOnSidebar.value && !isMouseNearEdge.value && autoHideEnabled.value) {
          console.log('Colapsando menú - fuera de zona segura')
          sidebarCollapsed.value = true
        }
      }, autoHideDelay)
    }
  }
  
  const handleSidebarMouseEnter = () => {
    console.log('Mouse ENTRA al sidebar') // Debug temporal
    isMouseOnSidebar.value = true
    // Cancelar INMEDIATAMENTE cualquier timeout de cierre
    if (autoHideTimeout.value) {
      clearTimeout(autoHideTimeout.value)
      autoHideTimeout.value = null
    }
  }
  
  const handleSidebarMouseLeave = () => {
    console.log('Mouse SALE del sidebar') // Debug temporal
    isMouseOnSidebar.value = false
    
    // Solo iniciar el cierre si el auto-hide está habilitado y el menú está expandido
    if (autoHideEnabled.value && !sidebarCollapsed.value) {
      console.log('Iniciando timeout de cierre desde mouseLeave...') // Debug temporal
      // Tiempo más corto - máximo 2 segundos
      autoHideTimeout.value = setTimeout(() => {
        console.log('Ejecutando cierre desde mouseLeave - Mouse en sidebar:', isMouseOnSidebar.value, 'Cerca del borde:', isMouseNearEdge.value) // Debug
        // Verificación final antes de cerrar
        if (!isMouseOnSidebar.value && !isMouseNearEdge.value && autoHideEnabled.value) {
          sidebarCollapsed.value = true
          console.log('Menú cerrado desde mouseLeave') // Debug
        }
      }, 2000) // Exactamente 2 segundos
    }
  }
  
  // Función para manejar clicks fuera del sidebar (más preciso)
  const handleClickOutsideSidebar = (event) => {
    if (!autoHideEnabled.value || sidebarCollapsed.value) return
    
    // Click más preciso - según la imagen, después de donde empieza el contenido verde
    const clickX = event.clientX
    const sidebarWidth = 288 // w-72 expandido
    const safeClickZone = sidebarWidth + sidebarSafeZone // Solo +20px como en mousemove
    
    if (clickX > safeClickZone) {
      console.log('Click fuera de zona segura - cerrando menú') // Debug
      sidebarCollapsed.value = true
      // Cancelar cualquier timeout pendiente
      if (autoHideTimeout.value) {
        clearTimeout(autoHideTimeout.value)
        autoHideTimeout.value = null
      }
    }
  }
  
  // Event listeners para menú inteligente
  document.addEventListener('mousemove', handleMouseMove)
  document.addEventListener('click', handleClickOutsideSidebar)
  
  // Agregar event listeners al sidebar (ya configurados en el template)
})

onUnmounted(() => {
  // Limpiar event listeners del menú inteligente
  document.removeEventListener('mousemove', handleMouseMove)
  document.removeEventListener('click', handleClickOutsideSidebar)
  if (autoHideTimeout.value) {
    clearTimeout(autoHideTimeout.value)
  }
})
</script>

<style scoped>
/* Transiciones suaves para el sidebar */
.sidebar-enter-active,
.sidebar-leave-active {
  transition: transform 0.3s ease-in-out;
}

.sidebar-enter-from,
.sidebar-leave-to {
  transform: translateX(-100%);
}

/* Scrollbar personalizado moderno para el sidebar POS */
.custom-scrollbar::-webkit-scrollbar {
  width: 6px;
}

.custom-scrollbar::-webkit-scrollbar-track {
  background: rgba(15, 23, 42, 0.3);
  border-radius: 10px;
}

.custom-scrollbar::-webkit-scrollbar-thumb {
  background: linear-gradient(180deg, #475569 0%, #334155 100%);
  border-radius: 10px;
  border: 1px solid rgba(71, 85, 105, 0.3);
}

.custom-scrollbar::-webkit-scrollbar-thumb:hover {
  background: linear-gradient(180deg, #64748b 0%, #475569 100%);
}

/* Scrollbar para navegación legacy (compatible) */
nav::-webkit-scrollbar {
  width: 4px;
}

nav::-webkit-scrollbar-track {
  background: transparent;
}

nav::-webkit-scrollbar-thumb {
  background: #cbd5e1;
  border-radius: 2px;
}

nav::-webkit-scrollbar-thumb:hover {
  background: #94a3b8;
}

.dark nav::-webkit-scrollbar-thumb {
  background: #4b5563;
}

.dark nav::-webkit-scrollbar-thumb:hover {
  background: #6b7280;
}

/* Efectos hover mejorados para botones del menú */
nav button {
  transition: all 0.2s ease-in-out;
}

nav button:hover:not(.bg-primary-600) {
  transform: translateX(4px);
}

/* Animaciones para el contenido principal */
main > div {
  animation: fadeInUp 0.3s ease-out;
}

@keyframes fadeInUp {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

/* Estilos de scrollbar personalizados */
.scrollbar-thin {
  scrollbar-width: thin;
  scrollbar-color: rgba(156, 163, 175, 0.5) transparent;
}

.scrollbar-thin::-webkit-scrollbar {
  width: 6px;
}

.scrollbar-thin::-webkit-scrollbar-track {
  background: transparent;
}

.scrollbar-thin::-webkit-scrollbar-thumb {
  background-color: rgba(156, 163, 175, 0.5);
  border-radius: 3px;
}

.scrollbar-thin::-webkit-scrollbar-thumb:hover {
  background-color: rgba(156, 163, 175, 0.7);
}

.dark .scrollbar-thin {
  scrollbar-color: rgba(75, 85, 99, 0.5) transparent;
}

.dark .scrollbar-thin::-webkit-scrollbar-thumb {
  background-color: rgba(75, 85, 99, 0.5);
}

.dark .scrollbar-thin::-webkit-scrollbar-thumb:hover {
  background-color: rgba(75, 85, 99, 0.7);
}
</style>