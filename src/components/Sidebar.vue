<template>
  <!-- Sidebar Empresarial Limpio (Shopify/Stripe Style) -->
  <div 
    class="sidebar-container fixed inset-y-0 left-0 z-50 transform transition-all duration-300 ease-in-out flex flex-col lg:translate-x-0 bg-gray-50 dark:bg-[#25252d] border-r border-gray-200 dark:border-zinc-700/50"
    :style="{
      width: sidebarCollapsed ? '80px' : '260px'
    }"
    :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
  >
    
    <!-- Logo y Marca - Estilo Empresarial Sobrio -->
    <div class="flex items-center border-b border-gray-200 dark:border-zinc-700/50" :class="sidebarCollapsed ? 'h-16 px-4 justify-center' : 'p-5'">
      <div class="flex items-center" :class="sidebarCollapsed ? '' : 'gap-3'">
        <!-- Logo Personalizado -->
        <div class="flex items-center justify-center flex-shrink-0">
          <img src="/logo.png" alt="Logo" class="w-10 h-10 object-contain">
        </div>
        
        <!-- Texto Negro Sobrio -->
        <div v-show="!sidebarCollapsed">
          <h1 class="text-[17px] font-bold text-gray-900 dark:text-white leading-tight tracking-tight">105 POS</h1>
          <p class="text-[11px] text-gray-600 dark:text-gray-400 leading-tight font-medium">Sistema Empresarial</p>
        </div>
      </div>
    </div>

    <!-- Navegación Principal -->
    <nav class="flex-1 overflow-y-auto py-6 scrollbar-thin scrollbar-track-transparent scrollbar-thumb-gray-300 dark:scrollbar-thumb-zinc-700">
      
      <!-- DASHBOARD -->
      <div v-if="hasModuleAccess('dashboard')" :style="sidebarCollapsed ? 'padding: 0 16px;' : 'padding: 0 16px;'">
        <div
          @click="$emit('change-module', 'dashboard')"
          class="menu-item"
          :class="[currentModule === 'dashboard' ? 'active' : '', sidebarCollapsed ? 'collapsed' : '']"
          :title="sidebarCollapsed ? 'Dashboard' : ''"
        >
          <!-- Dashboard: Cuadrícula de métricas/paneles -->
          <svg style="width: 18px; height: 18px; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h4a1 1 0 011 1v5a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM14 5a1 1 0 011-1h4a1 1 0 011 1v2a1 1 0 01-1 1h-4a1 1 0 01-1-1V5zM4 15a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1H5a1 1 0 01-1-1v-4zM14 12a1 1 0 011-1h4a1 1 0 011 1v7a1 1 0 01-1 1h-4a1 1 0 01-1-1v-7z"></path>
          </svg>
          <span v-show="!sidebarCollapsed" class="menu-text">Dashboard</span>
        </div>
      </div>

      <!-- OPERACIONES -->
      <div v-if="hasModuleAccess('pos') || hasModuleAccess('invoices') || hasModuleAccess('returns')" class="mt-7 px-4">
        <!-- Línea divisoria cuando está colapsado -->
        <div v-if="sidebarCollapsed" class="border-t border-gray-200 dark:border-white/10 mb-4"></div>
        <h3 v-show="!sidebarCollapsed" class="section-title">OPERACIONES</h3>
        
        <div
          v-if="hasModuleAccess('pos')"
          @click="$emit('change-module', 'pos')"
          class="menu-item"
          :class="[currentModule === 'pos' ? 'active' : '', sidebarCollapsed ? 'collapsed' : '']"
          :title="sidebarCollapsed ? 'Punto de Venta' : ''"
        >
          <!-- Punto de Venta: Terminal/Caja registradora moderna -->
          <svg style="width: 18px; height: 18px; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
          </svg>
          <span v-show="!sidebarCollapsed" class="menu-text">Punto de Venta</span>
        </div>

        <div
          v-if="hasModuleAccess('invoices')"
          @click="$emit('change-module', 'invoices')"
          class="menu-item"
          :class="[currentModule === 'invoices' ? 'active' : '', sidebarCollapsed ? 'collapsed' : '']"
          :title="sidebarCollapsed ? 'Facturas' : ''"
        >
          <!-- Facturas: Recibo con check -->
          <svg style="width: 18px; height: 18px; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l2 2 4-4m0-9H7a2 2 0 00-2 2v14l3.5-2 3.5 2 3.5-2 3.5 2V5a2 2 0 00-2-2h-2"></path>
          </svg>
          <span v-show="!sidebarCollapsed" class="menu-text">Facturas</span>
        </div>

        <div
          v-if="hasModuleAccess('returns')"
          @click="$emit('change-module', 'returns-management')"
          class="menu-item"
          :class="[currentModule === 'returns-management' ? 'active' : '', sidebarCollapsed ? 'collapsed' : '']"
          :title="sidebarCollapsed ? 'Devoluciones' : ''"
        >
          <!-- Devoluciones: Paquete con flecha de retorno -->
          <svg style="width: 18px; height: 18px; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 15v-1a4 4 0 00-4-4H8m0 0l3 3m-3-3l3-3m9 14V5a2 2 0 00-2-2H6a2 2 0 00-2 2v16l4-2 4 2 4-2 4 2z"></path>
          </svg>
          <span v-show="!sidebarCollapsed" class="menu-text">Devoluciones</span>
        </div>
      </div>

      <!-- INVENTARIO -->
      <div v-if="hasModuleAccess('products') || hasModuleAccess('categories') || hasModuleAccess('stock') || hasModuleAccess('intelligent_inventory')" class="mt-7 px-4">
        <!-- Línea divisoria cuando está colapsado -->
        <div v-if="sidebarCollapsed" class="border-t border-gray-200 dark:border-white/10 mb-4"></div>
        <h3 v-show="!sidebarCollapsed" class="section-title">INVENTARIO</h3>
        
        <div
          v-if="hasModuleAccess('products')"
          @click="$emit('change-module', 'products')"
          class="menu-item"
          :class="[currentModule === 'products' ? 'active' : '', sidebarCollapsed ? 'collapsed' : '']"
          :title="sidebarCollapsed ? 'Productos' : ''"
        >
          <svg style="width: 18px; height: 18px; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
          </svg>
          <span v-show="!sidebarCollapsed" class="menu-text">Productos</span>
        </div>

        <div
          v-if="hasModuleAccess('categories')"
          @click="$emit('change-module', 'categories')"
          class="menu-item"
          :class="[currentModule === 'categories' ? 'active' : '', sidebarCollapsed ? 'collapsed' : '']"
          :title="sidebarCollapsed ? 'Categorías' : ''"
        >
          <svg style="width: 18px; height: 18px; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
          </svg>
          <span v-show="!sidebarCollapsed" class="menu-text">Categorías</span>
        </div>

        <div
          v-if="hasModuleAccess('stock')"
          @click="$emit('change-module', 'stock')"
          class="menu-item"
          :class="[currentModule === 'stock' ? 'active' : '', sidebarCollapsed ? 'collapsed' : '']"
          :title="sidebarCollapsed ? 'Gestión de Stock' : ''"
        >
          <!-- Gestión de Stock: Cajas apiladas/almacén -->
          <svg style="width: 18px; height: 18px; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path>
          </svg>
          <span v-show="!sidebarCollapsed" class="menu-text">Gestión de Stock</span>
        </div>

        <div
          v-if="hasModuleAccess('intelligent_inventory')"
          @click="$emit('change-module', 'intelligent_inventory')"
          class="menu-item"
          :class="[currentModule === 'intelligent_inventory' ? 'active' : '', sidebarCollapsed ? 'collapsed' : '']"
          :title="sidebarCollapsed ? 'Inventario Inteligente' : ''"
        >
          <!-- Inventario IA: Chip/cerebro con circuito -->
          <svg style="width: 18px; height: 18px; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 9l2 2 4-4"></path>
          </svg>
          <span v-show="!sidebarCollapsed" class="menu-text">Inventario IA</span>
        </div>
      </div>

      <!-- TIENDA ONLINE -->
      <div v-if="showWebCatalog" class="mt-7 px-4">
        <!-- Línea divisoria cuando está colapsado -->
        <div v-if="sidebarCollapsed" class="border-t border-gray-200 dark:border-white/10 mb-4"></div>
        <h3 v-show="!sidebarCollapsed" class="section-title">TIENDA ONLINE</h3>
        
        <div
          @click="$emit('change-module', 'web-catalog-config')"
          class="menu-item"
          :class="[currentModule === 'web-catalog-config' ? 'active' : '', sidebarCollapsed ? 'collapsed' : '']"
          :title="sidebarCollapsed ? 'Catálogo Web' : ''"
        >
          <!-- Catálogo Web: Tienda online con carrito -->
          <svg style="width: 18px; height: 18px; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
          </svg>
          <span v-show="!sidebarCollapsed" class="menu-text">Catálogo Web</span>
        </div>
      </div>

      <!-- MULTISEDE (Premium/Enterprise Only + Permiso) -->
      <div v-if="showMultisede" class="mt-7 px-4">
        <!-- Línea divisoria cuando está colapsado -->
        <div v-if="sidebarCollapsed" class="border-t border-gray-200 dark:border-white/10 mb-4"></div>
        <h3 v-show="!sidebarCollapsed" class="section-title">MULTISEDE</h3>
        
        <div
          @click="$emit('change-module', 'warehouses')"
          class="menu-item"
          :class="[currentModule === 'warehouses' ? 'active' : '', sidebarCollapsed ? 'collapsed' : '']"
          :title="sidebarCollapsed ? 'Gestión de Sedes' : ''"
        >
          <!-- Gestión de Sedes: Múltiples ubicaciones/mapa -->
          <svg style="width: 18px; height: 18px; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
          </svg>
          <span v-show="!sidebarCollapsed" class="menu-text">Gestión de Sedes</span>
        </div>

      </div>

      <!-- RELACIONES -->
      <div v-if="hasModuleAccess('customers') || hasModuleAccess('suppliers')" class="mt-7 px-4">
        <!-- Línea divisoria cuando está colapsado -->
        <div v-if="sidebarCollapsed" class="border-t border-gray-200 dark:border-white/10 mb-4"></div>
        <h3 v-show="!sidebarCollapsed" class="section-title">RELACIONES</h3>
        
        <div
          v-if="hasModuleAccess('customers')"
          @click="$emit('change-module', 'customers')"
          class="menu-item"
          :class="[currentModule === 'customers' ? 'active' : '', sidebarCollapsed ? 'collapsed' : '']"
          :title="sidebarCollapsed ? 'Clientes' : ''"
        >
          <svg style="width: 18px; height: 18px; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
          </svg>
          <span v-show="!sidebarCollapsed" class="menu-text">Clientes</span>
        </div>

        <div
          v-if="hasModuleAccess('customers') && isCreditiendaEnabled && ['premium', 'enterprise'].includes(appStore.tenantPlan)"
          @click="$emit('change-module', 'accounts-receivable')"
          class="menu-item"
          :class="[currentModule === 'accounts-receivable' ? 'active' : '', sidebarCollapsed ? 'collapsed' : '']"
          :title="sidebarCollapsed ? 'CrediTienda' : ''"
        >
          <!-- CrediTienda: Tarjeta de crédito -->
          <svg style="width: 18px; height: 18px; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
          </svg>
          <span v-show="!sidebarCollapsed" class="menu-text">CrediTienda</span>
        </div>

        <div
          v-if="hasModuleAccess('suppliers')"
          @click="$emit('change-module', 'purchase-orders')"
          class="menu-item"
          :class="[currentModule === 'purchase-orders' ? 'active' : '', sidebarCollapsed ? 'collapsed' : '']"
          :title="sidebarCollapsed ? 'Proveedores' : ''"
        >
          <!-- Proveedores: Camión de entrega -->
          <svg style="width: 18px; height: 18px; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 17h1m6 0h1M3 11l2-6h10l2 6M3 11v6h2m0 0a2 2 0 104 0m-4 0h4m8 0h2v-6m-2 6a2 2 0 104 0m-4 0h4m-6-6V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v6"></path>
          </svg>
          <span v-show="!sidebarCollapsed" class="menu-text">Proveedores</span>
        </div>
      </div>

      <!-- SISTEMA -->

      <div class="mt-7 mb-6 px-4">
        <!-- Línea divisoria cuando está colapsado -->
        <div v-if="sidebarCollapsed" class="border-t border-gray-200 dark:border-white/10 mb-4"></div>
        <h3 v-show="!sidebarCollapsed" class="section-title">SISTEMA</h3>
        
        <div
          v-if="hasModuleAccess('users')"
          @click="$emit('change-module', 'users')"
          class="menu-item"
          :class="[currentModule === 'users' ? 'active' : '', sidebarCollapsed ? 'collapsed' : '']"
          :title="sidebarCollapsed ? 'Usuarios' : ''"
        >
          <!-- Usuarios: Persona con escudo/seguridad -->
          <svg style="width: 18px; height: 18px; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
          </svg>
          <span v-show="!sidebarCollapsed" class="menu-text">Usuarios</span>
        </div>

        <div
          v-if="hasModuleAccess('cash_register')"
          @click="$emit('change-module', 'cash-admin')"
          class="menu-item"
          :class="[currentModule === 'cash-admin' ? 'active' : '', sidebarCollapsed ? 'collapsed' : '']"
          :title="sidebarCollapsed ? 'Control de Cajas' : ''"
        >
          <!-- Control de Cajas: Caja registradora/cajón -->
          <svg style="width: 18px; height: 18px; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
          </svg>
          <span v-show="!sidebarCollapsed" class="menu-text">Control de Cajas</span>
        </div>

        <div
          @click="$emit('change-module', 'expenses')"
          class="menu-item"
          :class="[currentModule === 'expenses' ? 'active' : '', sidebarCollapsed ? 'collapsed' : '']"
          :title="sidebarCollapsed ? 'Gastos Operativos' : ''"
        >
          <!-- Gastos Operativos: Flecha hacia abajo con moneda (egresos) -->
          <svg style="width: 18px; height: 18px; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"></path>
          </svg>
          <span v-show="!sidebarCollapsed" class="menu-text">Gastos Operativos</span>
        </div>

        <div
          v-if="hasModuleAccess('reports')"
          @click="$emit('change-module', 'reports')"
          class="menu-item"
          :class="[currentModule === 'reports' ? 'active' : '', sidebarCollapsed ? 'collapsed' : '']"
          :title="sidebarCollapsed ? 'Reportes' : ''"
        >
          <!-- Reportes: Gráfico de barras -->
          <svg style="width: 18px; height: 18px; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
          </svg>
          <span v-show="!sidebarCollapsed" class="menu-text">Reportes</span>
        </div>

        <div
          v-if="hasModuleAccess('settings')"
          @click="$emit('change-module', 'settings')"
          class="menu-item"
          :class="[currentModule === 'settings' ? 'active' : '', sidebarCollapsed ? 'collapsed' : '']"
          :title="sidebarCollapsed ? 'Configuración' : ''"
        >
          <svg style="width: 18px; height: 18px; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
          </svg>
          <span v-show="!sidebarCollapsed" class="menu-text">Configuración</span>
        </div>
      </div>
      
    </nav>
  </div>
</template>

<script setup>
import { defineProps, defineEmits, onMounted, computed } from 'vue'
import { usePermissions } from '../composables/usePermissions.js'
import { useCreditienda } from '../composables/useCreditienda.js'
import { appStore } from '../store/appStore.js'
import authService from '../services/authService.js'

// Props
defineProps({
  currentModule: {
    type: String,
    default: 'pos' // Módulo por defecto: POS
  },
  sidebarOpen: {
    type: Boolean,
    default: true
  },
  sidebarCollapsed: {
    type: Boolean,
    default: false
  }
})

// Emits
defineEmits(['change-module', 'toggle-sidebar', 'update:sidebarCollapsed'])

// Permisos
const { hasModuleAccess, currentUser, userPermissions } = usePermissions()

// Creditienda
const { isCreditiendaEnabled } = useCreditienda()

// Computed para verificar si debería mostrar MULTISEDE (reactivo)
// Solo mostrar para planes Premium y Enterprise
const showMultisede = computed(() => {
  const tenantPlan = appStore.tenantPlan || 'free_trial'
  const allowedPlans = ['premium', 'enterprise']
  
  // Verificar si el plan actual permite multi-sede
  return allowedPlans.includes(tenantPlan)
})

// Computed para verificar si debería mostrar Catálogo Web (reactivo)
// Solo mostrar para planes Premium y Enterprise
const showWebCatalog = computed(() => {
  const tenantPlan = appStore.tenantPlan || 'free_trial'
  const allowedPlans = ['premium', 'enterprise']
  
  // Verificar si el plan actual permite catálogo web
  return allowedPlans.includes(tenantPlan)
})

onMounted(async () => {
  // Componente montado
})
</script>

<style scoped>
/* Botón de Colapsar */
.collapse-button {
  display: flex;
  align-items: center;
  padding: 8px 12px;
  background-color: #F0F2F5;
  border: 1px solid #E0E0E0;
  border-radius: 6px;
  color: #6B7280;
  font-size: 13px;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s ease;
}

.collapse-button:hover {
  background-color: #E0E7FF;
  color: #007BFF;
  border-color: #007BFF;
}

/* Títulos de Secciones - Estilo Empresarial Limpio */
.section-title {
  font-size: 11px;
  font-weight: 600;
  color: #9CA3AF;
  text-transform: uppercase;
  letter-spacing: 1px;
  margin-bottom: 12px;
  margin-top: 8px;
  padding: 0 8px;
}

.dark .section-title {
  color: #71717a; /* Zinc-500 - Más visible en dark */
}

/* Items del Menú - Estilo Empresarial Sobrio (Shopify/Stripe) */
.menu-item {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 10px 16px;
  margin-bottom: 2px;
  border-radius: 8px;
  color: #4B5563;
  font-size: 14px;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s ease;
  position: relative;
  background-color: transparent;
}

.dark .menu-item {
  color: #a1a1aa; /* Zinc-400 - High contrast en dark */
}

/* Menu Item Colapsado */
.menu-item.collapsed {
  justify-content: center;
  padding: 12px;
  gap: 0;
}

/* Hover State - Gris Muy Sutil */
.menu-item:hover {
  background-color: #F3F4F6;
  color: #111827;
}

.dark .menu-item:hover {
  background-color: #27272a; /* Zinc-800 - High contrast hover */
  color: #ffffff;
}

.menu-item:hover svg {
  color: #374151;
}

.dark .menu-item:hover svg {
  color: #e4e4e7; /* Zinc-200 */
}

/* Active State - Minimalismo Puro */
.menu-item.active {
  background-color: #F0FDF4;
  color: #065F46;
  font-weight: 600;
  padding-left: 20px;
}

.dark .menu-item.active {
  background-color: #10b98133; /* Emerald con alpha - Brilla en dark */
  color: #34d399; /* Emerald-400 - High contrast */
  font-weight: 600;
}

/* Barra Verde Sutil a la Izquierda */
.menu-item.active::before {
  content: '';
  position: absolute;
  left: 0;
  top: 50%;
  transform: translateY(-50%);
  width: 3px;
  height: 60%;
  background-color: #059669;
  border-radius: 0 2px 2px 0;
}

.dark .menu-item.active::before {
  background-color: #10b981; /* Emerald-500 - Más brillante en dark */
}

.menu-item.active svg {
  color: #059669;
}

.dark .menu-item.active svg {
  color: #34d399; /* Emerald-400 */
}

/* Texto del Menú */
.menu-text {
  flex: 1;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}
</style>
