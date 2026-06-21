<template>
  <!-- Sistema POS Empresarial Completo -->
  <div :class="{ 'dark': isDarkMode }" class="bg-[#F9FAFB] dark:bg-gradient-to-b dark:from-zinc-900 dark:via-zinc-950 dark:to-black transition-colors duration-300 overflow-x-hidden" style="height: 100%; display: flex; flex-direction: column;">
    
    <!-- Modal de Suscripción Expirada (NO se puede cerrar) -->
    <SubscriptionExpiredModal />
    
    <!-- Sidebar Component -->
    <Sidebar 
      :currentModule="currentModule"
      :sidebarOpen="sidebarOpen"
      :sidebarCollapsed="sidebarCollapsed"
      :isFastFoodMode="isFastFoodMode"
      @change-module="setCurrentModule"
      @toggle-sidebar="toggleSidebar"
      @update:sidebarCollapsed="sidebarCollapsed = $event"
    />

    <!-- Contenedor Principal con Chat IA Lateral -->
    <div class="flex flex-1 min-h-0 min-w-0 transition-all duration-300"
         :class="{
           'lg:ml-[264px]': !sidebarCollapsed,
           'lg:ml-[68px]': sidebarCollapsed
         }">
      
      <!-- Área Principal de Contenido -->
      <div class="flex-1 flex flex-col min-h-0 min-w-0 overflow-x-hidden">
        
        <!-- Header Corporativo Profesional - Siempre ancho completo -->
        <AppHeader
          :module-title="getModuleTitle()"
          :module-description="getModuleDescription()"
          :current-user="currentUser"
          :current-module="currentModule"
          :current-warehouse="currentWarehouse"
          :sidebar-collapsed="sidebarCollapsed"
          :should-show-settings="shouldShowModule('settings')"
          @toggleSidebar="toggleSidebar"
          @toggleSidebarCollapsed="sidebarCollapsed = !sidebarCollapsed"
          @navigate-to-settings="handleNavigateSettings"
          @navigate-to-profile="handleNavigateProfile"
          @toggle-radio="handleToggleRadio"
          @logout="handleLogout"
          @show-help="handleShowHelp"
          @notifications-opened="aiChatStore.close()"
          @profile-dropdown-opened="aiChatStore.close()"
        />




        <!-- Contenido Principal - En POS el chat flota, en otras vistas hace espacio -->
        <main class="flex-1 min-h-0 min-w-0 overflow-y-auto transition-all duration-300"
              :class="{ 'ai-chat-content-spacing': aiChatStore.isOpen.value && currentModule !== 'pos' }">
        
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
        <div v-if="currentModule === 'pos'" style="height: 100%;">
          <PosView 
            :key="posRefreshKey"
            ref="posViewRef"
            @sale-completed="handleSaleCompleted" 
            @create-invoice="handleCreateQuote"
            @search-quote="handleSearchQuote"
            @cart-status-changed="handleCartStatusChanged"
            @warehouse-changed="handleWarehouseChange"
            @change-module="setCurrentModule"
          />
        </div>

        <!-- Módulos restantes se cargan dinámicamente -->
        <div v-if="currentModule !== 'dashboard' && currentModule !== 'pos'" style="height: 100%; overflow-y: auto;">
          <component
            :is="currentModuleComponent"
            v-bind="getModuleProps()"
            @change-module="setCurrentModule"
            @open-quotation-in-pos="handleOpenQuotationInPos"
            @open-return-in-pos="handleOpenReturnInPos"
            @refresh="loadInvoices"
          />
        </div>
        
      </main>
      </div>
    </div>

    <!-- Panel de Chat IA 105 ahora es global (App.vue) -->

    <!-- Modal de Confirmación - Salir del POS con productos en carrito -->
    <Teleport to="body">
      <Transition
        enter-active-class="transition ease-out duration-300"
        enter-from-class="opacity-0"
        enter-to-class="opacity-100"
        leave-active-class="transition ease-in duration-200"
        leave-from-class="opacity-100"
        leave-to-class="opacity-0"
      >
        <div v-if="showCartWarningModal" class="fixed inset-0 bg-black/70 dark:bg-black/85 flex items-center justify-center z-[100] p-4">
          <Transition
            enter-active-class="transition ease-out duration-300"
            enter-from-class="opacity-0 scale-95 translate-y-4"
            enter-to-class="opacity-100 scale-100 translate-y-0"
            leave-active-class="transition ease-in duration-200"
            leave-from-class="opacity-100 scale-100 translate-y-0"
            leave-to-class="opacity-0 scale-95 translate-y-4"
          >
            <div v-if="showCartWarningModal" class="bg-white dark:bg-[#1e1e24] rounded-3xl shadow-2xl max-w-sm w-full overflow-hidden border border-gray-100 dark:border-zinc-800">
              
              <!-- Contenido Principal -->
              <div class="p-8 text-center">
                <!-- Icono animado -->
                <div class="relative w-20 h-20 mx-auto mb-6">
                  <div class="absolute inset-0 bg-amber-100 dark:bg-amber-900/30 rounded-full animate-ping opacity-30"></div>
                  <div class="relative w-20 h-20 bg-gradient-to-br from-amber-400 to-orange-500 rounded-full flex items-center justify-center shadow-lg shadow-amber-500/30">
                    <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                  </div>
                </div>
                
                <!-- Texto -->
                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">
                  ¡Espera un momento!
                </h3>
                <p class="text-sm text-gray-500 dark:text-zinc-400 leading-relaxed max-w-xs mx-auto">
                  Tienes productos en el carrito. Si cambias de módulo perderás la venta actual.
                </p>
              </div>

              <!-- Botones -->
              <div class="px-6 pb-6 space-y-3">
                <button 
                  @click="cancelModuleChange"
                  class="w-full bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-600 hover:to-teal-700 text-white font-semibold py-3.5 px-6 rounded-xl transition-all duration-200 shadow-lg shadow-emerald-500/25 hover:shadow-emerald-500/40 hover:scale-[1.02] active:scale-[0.98] flex items-center justify-center gap-2"
                >
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                  </svg>
                  Seguir vendiendo
                </button>
                
                <button 
                  @click="confirmModuleChange"
                  class="w-full bg-gray-100 dark:bg-zinc-800 hover:bg-gray-200 dark:hover:bg-zinc-700 text-gray-700 dark:text-zinc-300 font-medium py-3.5 px-6 rounded-xl transition-all duration-200 flex items-center justify-center gap-2"
                >
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                  </svg>
                  Salir y perder carrito
                </button>
              </div>
            </div>
          </Transition>
        </div>
      </Transition>
    </Teleport>

    <!-- Radio Player Modal (Global) -->
    <RadioPlayerModal 
      :is-open="radioWidgetOpen"
      @close="radioWidgetOpen = false"
    />

    <!-- Botón Flotante IA 105 - Solo visible fuera del POS -->
    <Transition
      enter-active-class="transition-all duration-300 ease-out"
      enter-from-class="opacity-0 scale-90 translate-y-2"
      enter-to-class="opacity-100 scale-100 translate-y-0"
      leave-active-class="transition-all duration-200 ease-in"
      leave-from-class="opacity-100 scale-100 translate-y-0"
      leave-to-class="opacity-0 scale-90 translate-y-2"
    >
      <button
        v-if="!aiChatStore.isOpen.value && currentModule !== 'pos'"
        @click="aiChatStore.open"
        class="fixed bottom-6 right-6 z-50 group w-14 h-14 rounded-full transition-all duration-300 hover:scale-110 active:scale-95 flex items-center justify-center"
        title="Asistente IA 105"
      >
        <!-- Glow pulsante de fondo - IA está viva -->
        <div class="absolute inset-0 rounded-full bg-gradient-to-r from-blue-400 via-violet-400 to-pink-400 opacity-30 blur-lg animate-ai-glow group-hover:opacity-50"></div>
        
        <!-- Círculo Glassmorphism principal -->
        <div class="absolute inset-0 rounded-full bg-white/80 dark:bg-zinc-900/80 backdrop-blur-xl border border-white/50 dark:border-zinc-700/50 shadow-xl shadow-violet-500/10 dark:shadow-violet-500/5"
             style="background: linear-gradient(135deg, rgba(255,255,255,0.9), rgba(255,255,255,0.7)); box-shadow: 0 8px 32px rgba(167,139,250,0.2), inset 0 0 0 1px rgba(255,255,255,0.5);">
        </div>
        
        <!-- Borde gradiente brillante -->
        <div class="absolute inset-0 rounded-full p-[1.5px] bg-gradient-to-br from-blue-400 via-violet-400 to-pink-400 opacity-60 group-hover:opacity-100 transition-opacity">
          <div class="w-full h-full rounded-full bg-white dark:bg-zinc-900"></div>
        </div>
        
        <!-- Icono Sparkles con gradiente Gemini -->
        <div class="relative z-10">
          <svg class="w-6 h-6" viewBox="0 0 24 24" fill="url(#gemini-gradient-fab)">
            <defs>
              <linearGradient id="gemini-gradient-fab" x1="0%" y1="0%" x2="100%" y2="100%">
                <stop offset="0%" style="stop-color:#60A5FA"/>
                <stop offset="50%" style="stop-color:#A78BFA"/>
                <stop offset="100%" style="stop-color:#F472B6"/>
              </linearGradient>
            </defs>
            <path d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09zM18.259 8.715L18 9.75l-.259-1.035a3.375 3.375 0 00-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 002.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 002.456 2.456L21.75 6l-1.035.259a3.375 3.375 0 00-2.456 2.456z"/>
          </svg>
        </div>
        
        <!-- Pulso de vida sutil -->
        <div class="absolute inset-0 rounded-full border-2 border-violet-400/30 animate-ping" style="animation-duration: 2s;"></div>
      </button>
    </Transition>

  </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted, onUnmounted, nextTick, defineAsyncComponent, watch } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import authService from '../services/authService.js'
import authStore from '../store/auth.js'
import { invoicesService } from '../services/invoicesService.js'
import { customersService } from '../services/customersService.js'
import { inventoryService } from '../services/inventoryService.js'
import { hasPermission, PERMISSIONS } from '../middleware/auth.js'
import { useSessionTimeout } from '../composables/useSessionTimeout.js'
import { useModuleNavigation } from '../composables/useModuleNavigation.js'
import { useRouteState } from '../composables/useRouteState.js'
import { appStore } from '../store/appStore.js'
import { aiChatStore } from '../store/aiChatStore.js'
import { useUIContextStore } from '../store/uiContextStore.js'
import { useToast } from '../composables/useToast.js'

// Importar componente Sidebar
import Sidebar from '../components/Sidebar.vue'

// Importar componente AppHeader
import AppHeader from '../components/AppHeader.vue'

// Importar RadioPlayerModal
import RadioPlayerModal from '../components/RadioPlayerModal.vue'

// Importar Modal de Suscripción Expirada
import SubscriptionExpiredModal from '../components/SubscriptionExpiredModal.vue'

// Router
const router = useRouter()
const route = useRoute()
const { showSuccess, showWarning } = useToast()

// Navegación global de módulos (para chat AI y otros componentes)
const { onModuleChange } = useModuleNavigation()

// Sistema de persistencia de ruta/módulo
const { saveCurrentModule, restoreLastModule, markRefresh, wasRecentlyRefreshed } = useRouteState()

// Sistema de timeout de sesión
const sessionTimeout = useSessionTimeout()

// Store de contexto UI para IA de voz
const uiContext = useUIContextStore()

// Importar componentes de módulos
const DashboardView = defineAsyncComponent(() => import('../components/DashboardView_BI.vue'))
const PosView = defineAsyncComponent(() => import('../components/PosView.vue'))
const ProductsViewStandard = defineAsyncComponent(() => import('../components/ProductsView_professional.vue'))
const FashionProductList = defineAsyncComponent(() => import('../components/FashionProductList.vue'))
const CustomersView = defineAsyncComponent(() => import('../components/CustomersView_clean.vue'))
const InventoryView = defineAsyncComponent(() => import('../components/InventoryView_professional.vue'))
const IntelligentInventoryView = defineAsyncComponent(() => import('../components/IntelligentInventoryView_Simple.vue'))
const ReportsView = defineAsyncComponent(() => import('../components/ReportsView.vue'))
const ReportsMenuView = defineAsyncComponent(() => import('../components/ReportsMenuView.vue'))
const SettingsView = defineAsyncComponent(() => import('../components/SettingsView.vue'))
const UsersView = defineAsyncComponent(() => import('../components/UsersView.vue'))
const RolesView = defineAsyncComponent(() => import('../components/RolesView.vue'))
const CategoriesView = defineAsyncComponent(() => import('../components/CategoriesView.vue'))
const PurchaseOrdersView = defineAsyncComponent(() => import('../components/PurchaseOrdersView_MasterDetail.vue'))
const InvoicesView = defineAsyncComponent(() => import('../components/InvoicesView.vue'))
const CashAdminView = defineAsyncComponent(() => import('../components/CashAdminView.vue'))
const ReturnsManagementView = defineAsyncComponent(() => import('../components/ReturnsManagementView.vue'))
const UsersManagementView = defineAsyncComponent(() => import('../components/UsersManagementView_Professional.vue'))
const ExpensesManager = defineAsyncComponent(() => import('./ExpensesManager.vue'))
const AccountsReceivableView = defineAsyncComponent(() => import('../components/CreditiTendaView.vue'))

// Web Catalog
const WebCatalogConfig = defineAsyncComponent(() => import('./WebCatalogConfig.vue'))
const WebCatalogUpgrade = defineAsyncComponent(() => import('./WebCatalogUpgrade.vue'))

// Multisede
const WarehousesView = defineAsyncComponent(() => import('../components/WarehousesView_MasterDetail.vue'))
const StockTransfersView = defineAsyncComponent(() => import('../components/StockTransfersView.vue'))

// Mi Perfil
const MyProfileView = defineAsyncComponent(() => import('../components/MyProfileView.vue'))

// Punteo Biométrico
const AttendanceCheckView = defineAsyncComponent(() => import('../components/AttendanceCheckView.vue'))

// Mi Jornada (asistencia personal para vendedores)
const MyAttendanceView = defineAsyncComponent(() => import('../components/MyAttendanceView.vue'))

// Componentes temporales para módulos no desarrollados aún
const PlaceholderView = defineAsyncComponent(() => import('../components/PlaceholderView.vue'))

// ===== ESTADO REACTIVO GLOBAL =====

// Configuración UI
const isDarkMode = ref(false)
const sidebarOpen = ref(false) // Inicia cerrado - handleResize lo abrirá en desktop
const sidebarCollapsed = ref(localStorage.getItem('sidebarOpen') === 'false') // Default: open (sidebarOpen !== 'false')
watch(sidebarCollapsed, (val) => localStorage.setItem('sidebarOpen', (!val).toString()))
const isMobileDevice = ref(false) // Detectar dispositivos táctiles
const currentModule = ref('pos') // Módulo inicial: POS
const moduleQueryParams = ref({}) // Query params para módulos (ej: {filter: 'inactive'})

// Estado del Radio Widget
const radioWidgetOpen = ref(false)

// Usuario actual - obtenido de la autenticación
const currentUser = ref({
  name: 'Cargando...',
  role: 'user',
  initials: '??',
  permissions: []
})

// Warehouse actual (para mostrar en header cuando está en POS)
const currentWarehouse = ref(null)

// Modal de confirmación para salir del POS con productos en carrito
const showCartWarningModal = ref(false)
const pendingModule = ref('') // Módulo al que se quiere navegar
const cartHasItems = ref(false) // Estado del carrito

// Referencias a componentes
const posViewRef = ref(null)
const pendingPosAction = ref(null) // Acción pendiente para ejecutar cuando PosView se monte
const posRefreshKey = ref(Date.now()) // Key para forzar recreación del POS

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
    'pos',      // POS es la primera opción
    'dashboard',
    'products',
    'customers',
    'categories',
    'stock',
    'purchase-orders',
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
  
  // Si no tiene acceso a ningún módulo, usar POS por defecto
  return 'pos'
}

// Inicializar usuario autenticado
const initializeUser = () => {
  const user = authService.getUser()
  
  if (user) {
    currentUser.value = {
      id: user.id,
      name: user.name || 'Usuario',
      email: user.email,
      role: user.role,
      initials: (user.name || 'U')
        .split(' ')
        .map(n => n[0])
        .join('')
        .toUpperCase()
        .substring(0, 2)
    }
    
    // Establecer módulo inicial basándose en los permisos del usuario
    currentModule.value = getFirstAccessibleModule()
  }
}

// Reactivamente actualizar la UI cuando authStore recibe datos frescos del backend
watch(() => authStore.state.user, (newUser) => {
  if (newUser && newUser.name && newUser.name !== currentUser.value.name) {
    currentUser.value = {
      id: newUser.id,
      name: newUser.name,
      email: newUser.email,
      role: newUser.role,
      initials: (newUser.name || 'U')
        .split(' ')
        .map(n => n[0])
        .join('')
        .toUpperCase()
        .substring(0, 2)
    }
  }
}, { deep: true })

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
  // Módulos que siempre están disponibles para todos los usuarios autenticados
  // (tienen su propio control de acceso interno)
  const alwaysAvailableModules = ['expenses']
  if (alwaysAvailableModules.includes(module)) {
    return true
  }

  // Verificación especial para Catálogo Web (Solo Premium/Enterprise)
  if (module === 'web-catalog-config') {
    const tenantPlan = appStore.tenantPlan || 'free_trial'
    const allowedPlans = ['premium', 'enterprise']
    return allowedPlans.includes(tenantPlan)
  }
  
  // Si no hay usuario o rol, denegar acceso
  if (!currentUser.value || !currentUser.value.role) {
    return false
  }

  const userPermissions = currentUser.value.role.permissions || []
  
  // Si tiene permiso ALL o admin, tiene acceso a todo
  if (userPermissions.includes('ALL') || userPermissions.includes('admin')) {
    return true
  }
  
  // Mapeo de módulos especiales a permisos reales de la base de datos
  const modulePermissionMap = {
    'returns-management': 'returns.view',     // Devoluciones → permiso de ver devoluciones
    'cash-admin': 'settings.view',            // Panel Admin → permiso de configuración
    'accounts-receivable': 'customers.view',  // Cuentas por Cobrar → permiso de ver clientes
    'warehouses': 'stock.view',               // Gestión de Sedes → permiso de ver stock
    'stock-transfers': 'stock.transfer',      // Traslados → permiso de transferir stock
    'purchase-orders': 'suppliers.view',      // Órdenes de Compra → permiso de ver proveedores
    'users-management': 'users.view',         // Usuarios y Roles → permiso de ver usuarios (desde IA)
    'users': 'users.view',                    // Usuarios (desde menú) → permiso de ver usuarios
    'attendance': 'users.view',                 // Punteo Biométrico → permiso de ver usuarios
    'roles': 'users.view',                    // Roles → permiso de ver usuarios
    'intelligent_inventory': 'products.view', // Inventario Inteligente → permiso de ver productos
    'categories': 'categories.view'           // Categorías → permiso de ver categorías
  }
  
  // Si el módulo tiene un mapeo especial, verificar ese permiso específico
  if (modulePermissionMap[module]) {
    const requiredPermission = modulePermissionMap[module]
    return userPermissions.includes(requiredPermission)
  }
  
  // Para módulos normales, verificar si tiene al menos un permiso que comience con el nombre del módulo
  // Por ejemplo: para 'pos', buscar 'pos.view', 'pos.create_sale', etc.
  return userPermissions.some(permission => permission.startsWith(`${module}.`))
}

// Verificar si un módulo debe mostrarse en el menú
const shouldShowModule = (module) => {
  return hasModulePermission(module)
}

// Funciones que cierran el chat antes de ejecutar la acción
const handleNavigateSettings = () => {
  aiChatStore.close()
  setCurrentModule('settings')
}

const handleShowHelp = () => {
  aiChatStore.close()
  moduleQueryParams.value = { tab: 'help' }
  router.push({ query: { tab: 'help' } })
  setCurrentModule('settings')
}

const handleNavigateProfile = () => {
  aiChatStore.close()
  setCurrentModule('my-profile')
}

const handleToggleRadio = () => {
  aiChatStore.close()
  radioWidgetOpen.value = !radioWidgetOpen.value
}

// Manejar logout (el modal de confirmación ya está en AppHeader)
const handleLogout = async () => {
  aiChatStore.close()
  await authService.logout()
  router.push('/login')
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
        seller_name: document.seller_name, // Pass seller_name explicitly
        cashier: document.seller_name || document.cashier_name || 'Vendedor',
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
    console.error('Error cargando facturas desde BD:', error)
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

// Función para obtener el Consumidor Final del sistema (creado por el seeder)
const ensureDefaultCustomer = async () => {
  try {
    const customersResponse = await customersService.getAll()
    if (customersResponse.success) {
      const consumidorFinal = customersResponse.data.find(customer => 
        customer.document_number === '222222222222'
      )
      if (consumidorFinal) {
        defaultCustomerId.value = consumidorFinal.id
        return defaultCustomerId.value
      }
    }
    defaultCustomerId.value = null
    return null
  } catch (error) {
    console.error('Error obteniendo Consumidor Final:', error)
    defaultCustomerId.value = null
    return null
  }
}

// Actualizar stock de productos vendidos
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
        console.error(`Error actualizando stock del producto ${item.name} (ID: ${item.id}):`, productError)
        // Continuar con los demás productos aunque uno falle
      }
    }
    
  } catch (error) {
    console.error('Error general actualizando stock de productos vendidos:', error)
    // No lanzamos el error para que no interrumpa la venta
  }
}

// Handler para actualizar warehouse actual desde PosView
const handleWarehouseChange = (warehouse) => {
  currentWarehouse.value = warehouse
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
    console.error('Error procesando venta completada:', error)
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
        showSuccess(`Cotización creada exitosamente. Código: ${modalQuotationData.code}, Cliente: ${modalQuotationData.customer}, Total: $${modalQuotationData.total.toLocaleString()}`)
      }
      
      return { success: true, data: result.data }
    } else {
      throw new Error(result.message || 'Error al crear cotización')
    }
  } catch (error) {
    console.error('Error al crear cotización:', error)
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
    console.error('[handleSearchQuote] Error al buscar cotización:', error)
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

// Manejar apertura de devolución desde módulo de facturas
const handleOpenReturnInPos = (invoiceNumber) => {
  
  // Cambiar al módulo POS
  setCurrentModule('pos')
  
  // Esperar a que el POS esté montado y abrir el modal de devoluciones con el número de factura
  nextTick(() => {
    setTimeout(() => {
      if (posViewRef.value && posViewRef.value.openReturnsModalWithInvoice) {
        posViewRef.value.openReturnsModalWithInvoice(invoiceNumber)
      } else {
        // Si no está disponible, intentar de nuevo en un momento
        pendingPosAction.value = () => {
          if (posViewRef.value?.openReturnsModalWithInvoice) {
            posViewRef.value.openReturnsModalWithInvoice(invoiceNumber)
            pendingPosAction.value = null
          }
        }
      }
    }, 300)
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
    id: 'purchase-orders',
    name: 'Proveedores',
    description: 'Gestión de proveedores y órdenes de compra',
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

// Fast Food Mode Detection (para sidebar y UI)
const isFastFoodMode = computed(() => {
  const settings = appStore.systemSettings || {}
  const storeType = settings.store_type
  
  // Prioridad 1: Configuración explícita
  if (storeType) {
    return storeType === 'restaurant' || storeType === 'fast_food' || storeType === 'food'
  }
  
  // Prioridad 2: Auto-detección por nombre del negocio
  const foodKeywords = ['restaurante', 'restaurant', 'comida', 'food', 'fast food', 'cafeteria', 'pizzeria', 
    'burger', 'hamburguesa', 'pollo', 'asadero', 'fritanga', 'fritos', 'empanadas', 'arepas']
  const businessName = (settings.business_name || appStore.businessName || '').toLowerCase()
  return foodKeywords.some(kw => businessName.includes(kw))
})

// Productos con stock bajo
const lowStockProducts = computed(() => {
  return productsList.value.filter(product => product.stock <= product.min_stock)
})

// Componente dinámico basado en el módulo actual
const currentModuleComponent = computed(() => {
  const moduleComponents = {
    products: ProductsViewStandard,
    categories: CategoriesView,
    stock: InventoryView,
    intelligent_inventory: IntelligentInventoryView,
    'purchase-orders': PurchaseOrdersView,
    customers: CustomersView,
    invoices: InvoicesView,
    'accounts-receivable': AccountsReceivableView,
    users: UsersManagementView,
    'users-management': UsersManagementView,  // Alias para navegación desde IA
    roles: RolesView,
    reports: ReportsMenuView,
    settings: SettingsView,
    'cash-admin': CashAdminView,
    'returns-management': ReturnsManagementView,
    expenses: ExpensesManager,
    warehouses: WarehousesView,
    'stock-transfers': StockTransfersView,
    'web-catalog-config': WebCatalogConfig,
    'my-profile': MyProfileView,
    'attendance': AttendanceCheckView,
    'my-attendance': MyAttendanceView
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
  if (isMobileDevice.value || window.innerWidth < 1024) {
    // En móvil: abrir/cerrar el drawer completo (expandido)
    sidebarOpen.value = !sidebarOpen.value
    if (sidebarOpen.value) {
      sidebarCollapsed.value = false // Siempre expandido en móvil
    }
  } else {
    // En desktop: colapsar/expandir
    sidebarCollapsed.value = !sidebarCollapsed.value
  }
}

// Cambiar módulo actual
const setCurrentModule = (module, options = {}) => {
  // Módulos especiales accesibles para todos los usuarios (sin verificación de permisos)
  const publicModules = ['my-profile', 'dashboard', 'pos', 'my-attendance']
  
  // Verificar permisos antes de cambiar módulo
  // SOLO si el usuario ya está cargado (evitar check durante inicialización)
  // Y si NO es un módulo público
  if (currentUser.value && currentUser.value.name !== 'Cargando...' && !publicModules.includes(module) && !hasModulePermission(module)) {
    // Obtener nombre del rol para mensaje informativo
    const roleName = currentUser.value?.role?.name || 'tu rol actual'
    const moduleNames = {
      'users': 'Usuarios y Roles',
      'users-management': 'Usuarios y Roles',
      'roles': 'Gestión de Roles',
      'categories': 'Categorías',
      'intelligent_inventory': 'Inventario Inteligente',
      'purchase-orders': 'Proveedores',
      'warehouses': 'Sedes y Bodegas',
      'accounts-receivable': 'Creditienda',
      'returns-management': 'Devoluciones',
      'cash-admin': 'Panel Administrativo',
      'reports': 'Reportes',
      'settings': 'Configuración'
    }
    const moduleFriendlyName = moduleNames[module] || module
    
    // Notificar al contexto UI del error de permisos (para que la IA pueda responder)
    uiContext.setLastNavigationError({
      module: module,
      moduleName: moduleFriendlyName,
      roleName: roleName,
      message: `No tienes permiso para acceder a ${moduleFriendlyName}. Tu rol de "${roleName}" no incluye este módulo.`
    })
    
    // Solo mostrar alert si NO viene de navegación por IA (options.fromAI)
    if (!options.fromAI) {
      showWarning('No tienes permisos para acceder a este módulo')
    }
    return false
  }
  
  // Limpiar cualquier error de navegación previo
  uiContext.setLastNavigationError(null)
  
  // Si está en POS y hay productos en el carrito, mostrar confirmación
  if (currentModule.value === 'pos' && cartHasItems.value && module !== 'pos') {
    pendingModule.value = module
    showCartWarningModal.value = true
    return false
  }

  currentModule.value = module
  
  // Notificar al contexto UI para la IA de voz
  uiContext.setCurrentModule(module)
  
  // � Si volvemos al POS, forzar recreación del componente para refrescar datos
  if (module === 'pos') {
    posRefreshKey.value = Date.now()
  }
  
  // �Cerrar sidebar en móvil al cambiar de módulo
  sidebarOpen.value = false
  
  // PERSISTIR el módulo actual en localStorage
  saveCurrentModule(module)
  
  // Manejar acciones especiales después del cambio de módulo
  if (options.action && module === 'pos') {
    if (options.action === 'open-close-cash-modal') {
      // Guardar la acción para ejecutar cuando el componente se monte
      pendingPosAction.value = 'openCloseCashModal'
    } else if (options.action === 'open-open-cash-modal') {
      // Guardar la acción para ejecutar cuando el componente se monte
      pendingPosAction.value = 'openOpenCashModal'
    }
  }
  
  // Manejar apertura del modal de devoluciones
  if (options.openReturnsModal && module === 'pos') {
    pendingPosAction.value = 'openReturnsModal'
  }
  
  // Cerrar sidebar en móvil después de selección
  if (window.innerWidth < 1024) {
    sidebarOpen.value = false
  }
}

// Función para ejecutar acciones en el componente PosView
const triggerPosAction = async (action) => {
  if (posViewRef.value && currentModule.value === 'pos') {
    try {
      switch (action) {
        case 'openCloseCashModal':
          // Acceder a la función del PosView para abrir el modal de cerrar caja
          if (posViewRef.value.openCloseCashModal) {
            await posViewRef.value.openCloseCashModal()
          } else if (posViewRef.value.showCloseCashModal) {
            await posViewRef.value.showCloseCashModal()
          } else {
            // Fallback: acceder directamente a la variable reactiva
            if (posViewRef.value.showCashCloseModal !== undefined) {
              posViewRef.value.showCashCloseModal = true
            } else {
              console.warn('No se encontró ninguna forma de abrir el modal de cerrar caja')
            }
          }
          break
        case 'openOpenCashModal':
          // Acceder a la función del PosView para abrir el modal de abrir caja
          if (posViewRef.value.openOpenCashModal) {
            posViewRef.value.openOpenCashModal()
          } else if (posViewRef.value.showOpenCashModal) {
            posViewRef.value.showOpenCashModal()
          } else {
            // Fallback: acceder directamente a la variable reactiva
            if (posViewRef.value.showCashOpenModal !== undefined) {
              posViewRef.value.showCashOpenModal = true
            } else {
              console.warn('No se encontró ninguna forma de abrir el modal de abrir caja')
            }
          }
          break
        case 'openReturnsModal':
          // Acceder a la función del PosView para abrir el modal de devoluciones
          if (posViewRef.value.openReturnsModal) {
            posViewRef.value.openReturnsModal()
          } else if (posViewRef.value.showReturnsModal !== undefined) {
            posViewRef.value.showReturnsModal = true
          } else {
            console.warn('No se encontró ninguna forma de abrir el modal de devoluciones')
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
    'purchase-orders': 'Proveedores',
    'expenses': 'Movimientos de Caja',
    'expense-categories': 'Categorías de Gastos',
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
    classes += ' text-gray-700 dark:text-zinc-300 hover:bg-gradient-to-r hover:from-blue-50 hover:to-purple-50 dark:hover:from-gray-700 dark:hover:to-gray-600 hover:text-blue-700 dark:hover:text-blue-300 hover:shadow-md hover:scale-[1.01]';
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
      return baseClasses + ' bg-gradient-to-br from-blue-600/90 via-indigo-600/90 to-blue-700/90 text-white shadow-xl shadow-blue-500/30 ring-1 ring-blue-400/30 border border-white/10 ';
    }
  } else {
    // Estado inactivo - sutil y moderno
    return baseClasses + ' bg-slate-800/20 hover:bg-gradient-to-br hover:from-slate-700/40 hover:via-slate-600/50 hover:to-slate-700/40 text-slate-300 hover:text-white hover:shadow-lg  border border-slate-700/20 hover:border-slate-500/40 hover:scale-[1.01] hover:ring-1 hover:ring-slate-500/20';
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
      return baseClasses + ' bg-white/25 text-white shadow-xl ring-2 ring-white/20 ';
    } else {
      // Icono secundario activo - profesional
      return baseClasses + ' bg-white/20 text-white shadow-lg ring-1 ring-white/15 ';
    }
  } else {
    // Icono inactivo - sutil con hover elegante
    return baseClasses + ' bg-slate-700/25 group-hover:bg-slate-600/40 text-slate-400 group-hover:text-white group-hover:shadow-md group-hover:ring-1 group-hover:ring-slate-500/30 ';
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
    intelligent_inventory: 'Inventario Inteligente • Análisis predictivo y optimización',
    'purchase-orders': 'Proveedores • Gestión de compras y órdenes de compra',
    customers: 'Base de Clientes • CRM integrado y seguimiento',
    invoices: 'Facturación • Historial completo de ventas',
    'returns-management': 'Devoluciones • Control y seguimiento administrativo',
    expenses: 'Movimientos de Caja • Ingresos, egresos y gastos',
    'expense-categories': 'Categorías de Gastos • Organización de egresos',
    users: 'Usuarios • Gestión de accesos y perfiles',
    roles: 'Roles y Permisos • Control de seguridad avanzado',
    reports: 'Reportes • Análisis inteligente de datos',
    settings: 'Configuración • Personalización del sistema',
    'web-catalog-config': 'Catálogo Web • Personaliza tu tienda online sin código',
    'accounts-receivable': 'CreditiTenda • Gestión completa de créditos',
    warehouses: 'Gestión de Sedes • Control multisede',
    'cash-admin': 'Control de Cajas • Supervisión de turnos',
    'my-profile': 'Mi Perfil • Información personal y seguridad',
    'attendance': 'Punteo de Jornada • Control biométrico de asistencia',
    'my-attendance': 'Mi Jornada • Control de asistencia personal'
  }
  return descriptions[currentModule.value] || 'Sistema de gestión empresarial avanzado'
}

// Obtener props para el módulo actual
const getModuleProps = () => {
  const baseProps = {
    moduleName: currentModule.value,
    onNavigate: setCurrentModule,
    queryParams: moduleQueryParams.value // Pasar query params a los módulos (filtros)
  }
  
  const specificProps = {
    products: { products: productsList.value, categories: categoriesList.value },
    categories: { categories: categoriesList.value },
    stock: { products: productsList.value, movements: [] },
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
  }
}

// ===== WATCHERS =====

// Watcher para sincronizar módulo actual con cambios en la ruta (navegación desde IA o otros componentes)
watch(() => route.params.module, (newModule, oldModule) => {
  if (newModule && newModule !== currentModule.value) {
    setCurrentModule(newModule)
  }
}, { immediate: true })

// Watcher para sincronizar query params
watch(() => route.query, (newQuery) => {
  if (newQuery) {
    moduleQueryParams.value = newQuery
  }
}, { immediate: true })

// Watcher para ejecutar acciones pendientes cuando el componente PosView se monte
watch(posViewRef, async (newRef) => {
  if (newRef && pendingPosAction.value) {
    // Ejecutar acción pendiente con un pequeño delay para asegurar que esté completamente inicializado
    setTimeout(async () => {
      // Si pendingPosAction es una función, ejecutarla directamente
      if (typeof pendingPosAction.value === 'function') {
        await pendingPosAction.value()
      } else {
        // Si es un string, usar triggerPosAction
        await triggerPosAction(pendingPosAction.value)
      }
      pendingPosAction.value = null // Limpiar acción pendiente
    }, 50)
  }
})

// Watcher para AUTO-REFRESH cuando entras al módulo POS
watch(() => currentModule.value, async (newModule, oldModule) => {
  // ACTUALIZAR UI CONTEXT STORE PARA IA (sincronizar módulo actual)
  uiContext.setCurrentModule(newModule)
  
  if (newModule === 'pos' && oldModule !== 'pos') {
    // Acabas de entrar al POS desde otro módulo
    try {
      // Forzar recarga de productos, clientes y métodos de pago
      if (appStore.cashSession.current?.warehouse_id) {
        const scope = 'local' // Puedes ajustar según tu lógica
        await appStore.loadProducts(appStore.cashSession.current.warehouse_id, scope, true)
        await appStore.loadCustomers(true)
        await appStore.loadPaymentMethods(true)
      }
    } catch (error) {
      console.error('Error en auto-refresh al entrar al POS:', error)
    }
  }
}, { immediate: true })

// ===== LIFECYCLE HOOKS =====

let timeInterval

onMounted(() => {
  // Marcar que se hizo refresh (para detección en componentes hijos)
  markRefresh()
  
  // Inicializar usuario autenticado
  initializeUser()
  
  // Verificar autenticación
  if (!authService.isAuthenticated()) {
    router.push('/login')
    return
  }
  
  // RESTAURAR el último módulo si se hizo refresh
  const lastModule = restoreLastModule()
  if (lastModule && lastModule !== 'pos') {
    currentModule.value = lastModule
  }
  
  // Registrar listener para navegación global (desde chat AI u otros componentes)
  onModuleChange((moduleName, queryParams = {}) => {
    // Pasar fromAI: true para que NO muestre alert() cuando falla por permisos
    setCurrentModule(moduleName, { fromAI: true })
    
    // Si hay query params (filtros), almacenarlos para que los módulos los usen
    if (Object.keys(queryParams).length > 0) {
      moduleQueryParams.value = queryParams
    } else {
      moduleQueryParams.value = {}
    }
  })
  
  // Cargar configuración
  loadSettings()
  
  // Cargar facturas desde base de datos
  loadInvoices()
  
  // Inicializar Cliente General para ventas sin cliente específico
  ensureDefaultCustomer()
  
  // Detectar dispositivos táctiles
  isMobileDevice.value = 'ontouchstart' in window || navigator.maxTouchPoints > 0
  
  // Ajustar sidebar según tamaño de pantalla
  const handleResize = () => {
    const isDesktop = window.innerWidth >= 1024
    if (isDesktop) {
      sidebarOpen.value = true
    } else {
      // En móvil: cerrar sidebar por defecto
      sidebarOpen.value = false
    }
  }
  
  window.addEventListener('resize', handleResize)
  handleResize()
})

onUnmounted(() => {
  window.removeEventListener('resize', handleResize)
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

/* Ajuste para contenido cuando el chat IA está abierto - Solo en desktop y NO en POS */
@media (min-width: 640px) {
  .ai-chat-content-spacing {
    padding-right: 400px;
  }
}

/* ════════════════════════════════════════════════════════════════════════════
   ANIMACIÓN GLOW PULSANTE PARA BOTÓN IA FLOTANTE
   Efecto de "vida" - La IA está esperando
════════════════════════════════════════════════════════════════════════════ */
@keyframes ai-glow {
  0%, 100% {
    opacity: 0.3;
    transform: scale(1);
  }
  50% {
    opacity: 0.6;
    transform: scale(1.15);
  }
}

.animate-ai-glow {
  animation: ai-glow 3s ease-in-out infinite;
}
</style>