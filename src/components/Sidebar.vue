<template>
  <!-- Overlay para mobile -->
  <Teleport to="body">
    <Transition
      enter-active-class="transition ease-out duration-300"
      enter-from-class="opacity-0"
      enter-to-class="opacity-100"
      leave-active-class="transition ease-in duration-200"
      leave-from-class="opacity-100"
      leave-to-class="opacity-0"
    >
      <div 
        v-if="sidebarOpen" 
        class="lg:hidden fixed inset-0 z-40 bg-black/50 backdrop-blur-sm"
        @click="$emit('toggle-sidebar')"
      ></div>
    </Transition>
  </Teleport>
  
  <!-- Sidebar Premium - Linear/Notion Style -->
  <aside 
    class="sidebar-root fixed inset-y-0 left-0 z-50 flex flex-col lg:translate-x-0 bg-white dark:bg-[#111113] border-r border-gray-200/80 dark:border-zinc-800/80"
    :style="{ width: sidebarCollapsed ? '68px' : '264px' }"
    :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
  >
    
    <!-- Header: Logo -->
    <div class="flex items-center h-[68px] border-b border-gray-100 dark:border-zinc-800/60" :class="sidebarCollapsed ? 'px-0 justify-center' : 'px-4'">
      
      <!-- Logo area -->
      <div class="flex items-center flex-1 min-w-0" :class="sidebarCollapsed ? 'justify-center' : 'gap-3'">
        <div class="flex items-center justify-center flex-shrink-0 w-9 h-9">
          <img src="/logo.png" alt="Logo" class="w-8 h-8 object-contain rounded-lg">
        </div>
        
        <Transition
          enter-active-class="transition-all duration-200 ease-out"
          enter-from-class="opacity-0 -translate-x-2"
          enter-to-class="opacity-100 translate-x-0"
          leave-active-class="transition-all duration-150 ease-in"
          leave-from-class="opacity-100"
          leave-to-class="opacity-0"
        >
          <div v-if="!sidebarCollapsed" class="min-w-0">
            <h1 class="text-[15px] font-semibold text-gray-900 dark:text-white leading-tight tracking-tight truncate">105 POS</h1>
            <p class="text-[12px] text-gray-400 dark:text-zinc-500 leading-tight font-medium truncate">Sistema Empresarial</p>
          </div>
        </Transition>
      </div>

      <!-- Close button (mobile only) -->
      <button 
        v-if="!sidebarCollapsed"
        @click="$emit('toggle-sidebar')"
        class="lg:hidden flex items-center justify-center w-8 h-8 text-gray-400 dark:text-zinc-500 hover:text-gray-600 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-zinc-800 rounded-lg transition-colors mr-1"
      >
        <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
        </svg>
      </button>
    </div>

    <!-- Navigation -->
        <nav class="flex-1 overflow-y-auto overflow-x-hidden py-3 sidebar-scrollbar" :class="sidebarCollapsed ? 'px-2' : 'px-4'"
          @mousemove="handleNavHover"
          @mouseleave="hideTooltip(true)"
          @scroll="hideTooltip(true)"
    >
      
      <!-- DASHBOARD -->
      <div v-if="hasModuleAccess('dashboard')">
        <div
          @click="$emit('change-module', 'dashboard')"
          class="menu-item group"
          :class="[currentModule === 'dashboard' ? 'active' : '', sidebarCollapsed ? 'collapsed' : '']"
          :data-tooltip="sidebarCollapsed ? 'Dashboard' : ''"
        >
          <svg class="menu-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M4 5a1 1 0 011-1h4a1 1 0 011 1v5a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM14 5a1 1 0 011-1h4a1 1 0 011 1v2a1 1 0 01-1 1h-4a1 1 0 01-1-1V5zM4 15a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1H5a1 1 0 01-1-1v-4zM14 12a1 1 0 011-1h4a1 1 0 011 1v7a1 1 0 01-1 1h-4a1 1 0 01-1-1v-7z"></path>
          </svg>
          <span v-if="!sidebarCollapsed" class="menu-text">Dashboard</span>
        </div>
      </div>

      <!-- OPERACIONES -->
      <div v-if="hasModuleAccess('pos') || hasModuleAccess('invoices') || hasModuleAccess('returns')">
        <div v-if="sidebarCollapsed" class="section-divider"></div>
        <h3 v-else class="text-[11px] font-black text-gray-400 dark:text-zinc-600 uppercase tracking-[0.2em] mt-8 mb-3 px-4">Operaciones</h3>
        
        <div
          v-if="hasModuleAccess('pos')"
          @click="$emit('change-module', 'pos')"
          class="menu-item group"
          :class="[currentModule === 'pos' ? 'active' : '', sidebarCollapsed ? 'collapsed' : '']"
          :data-tooltip="sidebarCollapsed ? 'Punto de Venta' : ''"
        >
          <svg class="menu-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
          </svg>
          <span v-if="!sidebarCollapsed" class="menu-text">Punto de Venta</span>
        </div>

        <div
          v-if="hasModuleAccess('invoices')"
          @click="$emit('change-module', 'invoices')"
          class="menu-item group"
          :class="[currentModule === 'invoices' ? 'active' : '', sidebarCollapsed ? 'collapsed' : '']"
          :data-tooltip="sidebarCollapsed ? 'Facturas' : ''"
        >
          <svg class="menu-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M9 14l2 2 4-4m0-9H7a2 2 0 00-2 2v14l3.5-2 3.5 2 3.5-2 3.5 2V5a2 2 0 00-2-2h-2"></path>
          </svg>
          <span v-if="!sidebarCollapsed" class="menu-text">Facturas</span>
        </div>

        <div
          v-if="hasModuleAccess('returns')"
          @click="$emit('change-module', 'returns-management')"
          class="menu-item group"
          :class="[currentModule === 'returns-management' ? 'active' : '', sidebarCollapsed ? 'collapsed' : '']"
          :data-tooltip="sidebarCollapsed ? 'Devoluciones' : ''"
        >
          <svg class="menu-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M16 15v-1a4 4 0 00-4-4H8m0 0l3 3m-3-3l3-3m9 14V5a2 2 0 00-2-2H6a2 2 0 00-2 2v16l4-2 4 2 4-2 4 2z"></path>
          </svg>
          <span v-if="!sidebarCollapsed" class="menu-text">Devoluciones</span>
        </div>

        <div
          v-if="canAccessUsersModule"
          @click="$emit('change-module', 'my-attendance')"
          class="menu-item group"
          :class="[currentModule === 'my-attendance' ? 'active' : '', sidebarCollapsed ? 'collapsed' : '']"
          :data-tooltip="sidebarCollapsed ? 'Mi Jornada' : ''"
        >
          <svg class="menu-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
          </svg>
          <span v-if="!sidebarCollapsed" class="menu-text">Mi Jornada</span>
        </div>
      </div>

      <!-- INVENTARIO -->
      <div v-if="hasModuleAccess('products') || hasModuleAccess('categories') || (hasModuleAccess('stock') && !isFashionStore) || hasModuleAccess('intelligent_inventory')">
        <div v-if="sidebarCollapsed" class="section-divider"></div>
        <h3 v-else class="text-[11px] font-black text-gray-400 dark:text-zinc-600 uppercase tracking-[0.2em] mt-8 mb-3 px-4">Inventario</h3>
        
        <div
          v-if="hasModuleAccess('products')"
          @click="$emit('change-module', 'products')"
          class="menu-item group"
          :class="[currentModule === 'products' ? 'active' : '', sidebarCollapsed ? 'collapsed' : '']"
          :data-tooltip="sidebarCollapsed ? 'Productos' : ''"
        >
          <svg class="menu-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
          </svg>
          <span v-if="!sidebarCollapsed" class="menu-text">Productos</span>
        </div>

        <div
          v-if="hasModuleAccess('categories')"
          @click="$emit('change-module', 'categories')"
          class="menu-item group"
          :class="[currentModule === 'categories' ? 'active' : '', sidebarCollapsed ? 'collapsed' : '']"
          :data-tooltip="sidebarCollapsed ? 'Categorias' : ''"
        >
          <svg class="menu-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
          </svg>
          <span v-if="!sidebarCollapsed" class="menu-text">Categorias</span>
        </div>

        <div
          v-if="hasModuleAccess('stock') && !isFashionStore"
          @click="$emit('change-module', 'stock')"
          class="menu-item group"
          :class="[currentModule === 'stock' ? 'active' : '', sidebarCollapsed ? 'collapsed' : '']"
          :data-tooltip="sidebarCollapsed ? 'Gestion de Stock' : ''"
        >
          <svg class="menu-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path>
          </svg>
          <span v-if="!sidebarCollapsed" class="menu-text">Gestion de Stock</span>
        </div>

        <div
          v-if="hasModuleAccess('intelligent_inventory')"
          @click="$emit('change-module', 'intelligent_inventory')"
          class="menu-item group"
          :class="[currentModule === 'intelligent_inventory' ? 'active' : '', sidebarCollapsed ? 'collapsed' : '']"
          :data-tooltip="sidebarCollapsed ? 'Inventario IA' : ''"
        >
          <svg class="menu-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 9l2 2 4-4"></path>
          </svg>
          <span v-if="!sidebarCollapsed" class="menu-text">Inventario IA</span>
        </div>
      </div>

      <!-- TIENDA ONLINE -->
      <div v-if="showWebCatalog">
        <div v-if="sidebarCollapsed" class="section-divider"></div>
        <h3 v-else class="text-[11px] font-black text-gray-400 dark:text-zinc-600 uppercase tracking-[0.2em] mt-8 mb-3 px-4">Tienda Online</h3>
        
        <div
          @click="$emit('change-module', 'web-catalog-config')"
          class="menu-item group"
          :class="[currentModule === 'web-catalog-config' ? 'active' : '', sidebarCollapsed ? 'collapsed' : '']"
          :data-tooltip="sidebarCollapsed ? 'Catalogo Web' : ''"
        >
          <svg class="menu-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
          </svg>
          <span v-if="!sidebarCollapsed" class="menu-text">Catalogo Web</span>
        </div>
      </div>

      <!-- MULTISEDE -->
      <div v-if="showMultisede">
        <div v-if="sidebarCollapsed" class="section-divider"></div>
        <h3 v-else class="text-[11px] font-black text-gray-400 dark:text-zinc-600 uppercase tracking-[0.2em] mt-8 mb-3 px-4">Multisede</h3>
        
        <div
          @click="$emit('change-module', 'warehouses')"
          class="menu-item group"
          :class="[currentModule === 'warehouses' ? 'active' : '', sidebarCollapsed ? 'collapsed' : '']"
          :data-tooltip="sidebarCollapsed ? 'Gestion de Sedes' : ''"
        >
          <svg class="menu-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
          </svg>
          <span v-if="!sidebarCollapsed" class="menu-text">Gestion de Sedes</span>
        </div>
      </div>

      <!-- RELACIONES -->
      <div v-if="hasModuleAccess('customers') || hasModuleAccess('suppliers')">
        <div v-if="sidebarCollapsed" class="section-divider"></div>
        <h3 v-else class="text-[11px] font-black text-gray-400 dark:text-zinc-600 uppercase tracking-[0.2em] mt-8 mb-3 px-4">Relaciones</h3>
        
        <div
          v-if="hasModuleAccess('customers')"
          @click="$emit('change-module', 'customers')"
          class="menu-item group"
          :class="[currentModule === 'customers' ? 'active' : '', sidebarCollapsed ? 'collapsed' : '']"
          :data-tooltip="sidebarCollapsed ? 'Clientes' : ''"
        >
          <svg class="menu-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
          </svg>
          <span v-if="!sidebarCollapsed" class="menu-text">Clientes</span>
        </div>

        <div
          v-if="hasModuleAccess('customers') && isCreditiendaEnabled && ['premium', 'enterprise'].includes(appStore.tenantPlan)"
          @click="$emit('change-module', 'accounts-receivable')"
          class="menu-item group"
          :class="[currentModule === 'accounts-receivable' ? 'active' : '', sidebarCollapsed ? 'collapsed' : '']"
          :data-tooltip="sidebarCollapsed ? 'CrediTienda' : ''"
        >
          <svg class="menu-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
          </svg>
          <span v-if="!sidebarCollapsed" class="menu-text">CrediTienda</span>
        </div>

        <div
          v-if="hasModuleAccess('suppliers')"
          @click="$emit('change-module', 'purchase-orders')"
          class="menu-item group"
          :class="[currentModule === 'purchase-orders' ? 'active' : '', sidebarCollapsed ? 'collapsed' : '']"
          :data-tooltip="sidebarCollapsed ? 'Proveedores' : ''"
        >
          <svg class="menu-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M8 17h1m6 0h1M3 11l2-6h10l2 6M3 11v6h2m0 0a2 2 0 104 0m-4 0h4m8 0h2v-6m-2 6a2 2 0 104 0m-4 0h4m-6-6V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v6"></path>
          </svg>
          <span v-if="!sidebarCollapsed" class="menu-text">Proveedores</span>
        </div>
      </div>

      <!-- SISTEMA -->
      <div class="mb-2">
        <div v-if="sidebarCollapsed" class="section-divider"></div>
        <h3 v-else class="text-[11px] font-black text-gray-400 dark:text-zinc-600 uppercase tracking-[0.2em] mt-8 mb-3 px-4">Sistema</h3>
        
        <div
          v-if="hasModuleAccess('users') && canAccessUsersModule"
          @click="$emit('change-module', 'users')"
          class="menu-item group"
          :class="[currentModule === 'users' ? 'active' : '', sidebarCollapsed ? 'collapsed' : '']"
          :data-tooltip="sidebarCollapsed ? 'Usuarios' : ''"
        >
          <svg class="menu-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
          </svg>
          <span v-if="!sidebarCollapsed" class="menu-text">Usuarios</span>
        </div>

        <div
          v-if="hasModuleAccess('users') && canAccessUsersModule"
          @click="$emit('change-module', 'attendance')"
          class="menu-item group"
          :class="[currentModule === 'attendance' ? 'active' : '', sidebarCollapsed ? 'collapsed' : '']"
          :data-tooltip="sidebarCollapsed ? 'Punteo Jornada' : ''"
        >
          <svg class="menu-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
          </svg>
          <span v-if="!sidebarCollapsed" class="menu-text">Punteo Jornada</span>
        </div>

        <div
          v-if="hasModuleAccess('cash_register')"
          @click="$emit('change-module', 'cash-admin')"
          class="menu-item group"
          :class="[currentModule === 'cash-admin' ? 'active' : '', sidebarCollapsed ? 'collapsed' : '']"
          :data-tooltip="sidebarCollapsed ? 'Control de Cajas' : ''"
        >
          <svg class="menu-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
          </svg>
          <span v-if="!sidebarCollapsed" class="menu-text">Control de Cajas</span>
        </div>

        <div
          v-if="hasModuleAccess('expenses') || hasModuleAccess('pos') || hasModuleAccess('cash_register')"
          @click="$emit('change-module', 'expenses')"
          class="menu-item group"
          :class="[currentModule === 'expenses' ? 'active' : '', sidebarCollapsed ? 'collapsed' : '']"
          :data-tooltip="sidebarCollapsed ? 'Movimientos de Caja' : ''"
        >
          <svg class="menu-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"></path>
          </svg>
          <span v-if="!sidebarCollapsed" class="menu-text">Movimientos de Caja</span>
        </div>

        <div
          v-if="hasModuleAccess('reports')"
          @click="$emit('change-module', 'reports')"
          class="menu-item group"
          :class="[currentModule === 'reports' ? 'active' : '', sidebarCollapsed ? 'collapsed' : '']"
          :data-tooltip="sidebarCollapsed ? 'Reportes' : ''"
        >
          <svg class="menu-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
          </svg>
          <span v-if="!sidebarCollapsed" class="menu-text">Reportes</span>
        </div>

        <div
          v-if="hasModuleAccess('settings')"
          @click="$emit('change-module', 'settings')"
          class="menu-item group"
          :class="[currentModule === 'settings' ? 'active' : '', sidebarCollapsed ? 'collapsed' : '']"
          :data-tooltip="sidebarCollapsed ? 'Configuracion' : ''"
        >
          <svg class="menu-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
          </svg>
          <span v-if="!sidebarCollapsed" class="menu-text">Configuracion</span>
        </div>
      </div>
      
    </nav>

    <!-- Footer: Collapse toggle + branding -->
    <div class="border-t border-gray-100 dark:border-zinc-800/60 p-3 flex items-center" :class="sidebarCollapsed ? 'justify-center' : 'justify-between'">
      <p v-if="!sidebarCollapsed" class="text-[10px] text-gray-300 dark:text-zinc-700 font-medium tracking-wide select-none pl-1">105 POS &middot; v3</p>
      <button 
        @click="$emit('update:sidebarCollapsed', !sidebarCollapsed)"
        class="hidden lg:flex items-center justify-center w-8 h-8 rounded-lg transition-all duration-200 text-gray-400 dark:text-zinc-600 hover:text-gray-600 dark:hover:text-zinc-400 hover:bg-gray-100 dark:hover:bg-zinc-800"
        :title="sidebarCollapsed ? 'Expandir menú' : 'Colapsar menú'"
      >
        <svg class="w-4 h-4 transition-transform duration-200" :class="sidebarCollapsed ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
      </button>
    </div>
  </aside>

  <!-- Tooltip (teleported to body to avoid overflow clipping) -->
  <Teleport to="body">
    <Transition
      enter-active-class="transition ease-out duration-100"
      enter-from-class="opacity-0 scale-95"
      enter-to-class="opacity-100 scale-100"
      leave-active-class="transition ease-in duration-75"
      leave-from-class="opacity-100"
      leave-to-class="opacity-0 scale-95"
    >
      <div
        v-if="tooltip.visible"
        class="fixed z-[9999] pointer-events-none -translate-y-1/2 px-3 py-1.5 rounded-lg text-xs font-medium whitespace-nowrap bg-gray-900 text-white dark:bg-zinc-700 dark:text-zinc-100 shadow-lg"
        :style="{ top: tooltip.y + 'px', left: tooltip.x + 'px' }"
      >
        {{ tooltip.text }}
      </div>
    </Transition>
  </Teleport>
</template>

<script setup>
import { defineProps, defineEmits, onMounted, computed, reactive } from 'vue'
import { usePermissions } from '../composables/usePermissions.js'
import { useCreditienda } from '../composables/useCreditienda.js'
import { appStore } from '../store/appStore.js'
import authService from '../services/authService.js'

defineProps({
  currentModule: {
    type: String,
    default: 'pos'
  },
  sidebarOpen: {
    type: Boolean,
    default: true
  },
  sidebarCollapsed: {
    type: Boolean,
    default: false
  },
  isFastFoodMode: {
    type: Boolean,
    default: false
  }
})

defineEmits(['change-module', 'toggle-sidebar', 'update:sidebarCollapsed'])

const { hasModuleAccess, currentUser, userPermissions } = usePermissions()
const { isCreditiendaEnabled } = useCreditienda()

// Tooltip state
const tooltip = reactive({ visible: false, text: '', x: 0, y: 0 })
let tooltipTimeout = null

const hideTooltip = (immediate = false) => {
  clearTimeout(tooltipTimeout)
  if (immediate) {
    tooltip.visible = false
    return
  }

  tooltipTimeout = setTimeout(() => {
    tooltip.visible = false
  }, 40)
}

const handleNavHover = (event) => {
  clearTimeout(tooltipTimeout)
  const item = event.target.closest('.menu-item.collapsed[data-tooltip]')
  if (item && item.dataset.tooltip) {
    const rect = item.getBoundingClientRect()
    tooltip.text = item.dataset.tooltip
    
    // Obtenemos el nivel de zoom aplicado globalmente (para Windows 125%+)
    const zoom = parseFloat(document.documentElement.style.zoom || '1') || 1
    
    // Al dividir por el zoom neutralizamos la pérdida de coordenadas, 
    // haciendo que el tooltip quede exactamente alineado visualmente al centro del botón.
    tooltip.x = (rect.right / zoom) + 8
    tooltip.y = (rect.top + (rect.height / 2)) / zoom
    
    tooltip.visible = true
  } else {
    hideTooltip()
  }
}

const showMultisede = computed(() => {
  const tenantPlan = appStore.tenantPlan || 'free_trial'
  const allowedPlans = ['premium', 'enterprise']
  const hasPlan = allowedPlans.includes(tenantPlan)
  const hasPermission = hasModuleAccess('settings') || hasModuleAccess('users')
  return hasPlan && hasPermission
})

const showWebCatalog = computed(() => {
  const tenantPlan = appStore.tenantPlan || 'free_trial'
  const allowedPlans = ['premium', 'enterprise']
  const hasPlan = allowedPlans.includes(tenantPlan)
  const hasPermission = hasModuleAccess('settings') || hasModuleAccess('users')
  return hasPlan && hasPermission
})

const isFashionStore = computed(() => {
  return appStore.systemSettings?.store_type === 'fashion'
})

const canAccessUsersModule = computed(() => {
  const tenantPlan = appStore.tenantPlan || 'free_trial'
  const allowedPlans = ['premium', 'enterprise']
  return allowedPlans.includes(tenantPlan)
})

onMounted(async () => {
  // Mounted
})
</script>

<style scoped>
/* === Sidebar Root === */
.sidebar-root {
  transition: width 0.2s cubic-bezier(0.4, 0, 0.2, 1);
  will-change: width;
  overflow-x: hidden;
  overflow-y: hidden;
}

/* === Custom Scrollbar === */
.sidebar-scrollbar {
  scrollbar-width: thin;
  scrollbar-color: transparent transparent;
}
.sidebar-scrollbar:hover {
  scrollbar-color: rgba(0, 0, 0, 0.1) transparent;
}
.dark .sidebar-scrollbar:hover {
  scrollbar-color: rgba(255, 255, 255, 0.08) transparent;
}
.sidebar-scrollbar::-webkit-scrollbar {
  width: 4px;
}
.sidebar-scrollbar::-webkit-scrollbar-track {
  background: transparent;
}
.sidebar-scrollbar::-webkit-scrollbar-thumb {
  background: transparent;
  border-radius: 4px;
}
.sidebar-scrollbar:hover::-webkit-scrollbar-thumb {
  background: rgba(0, 0, 0, 0.1);
}
.dark .sidebar-scrollbar:hover::-webkit-scrollbar-thumb {
  background: rgba(255, 255, 255, 0.08);
}

/* === Section Divider (collapsed) === */
.section-divider {
  height: 1px;
  margin: 12px 8px;
  background: linear-gradient(90deg, transparent, #e5e7eb, transparent);
}
.dark .section-divider {
  background: linear-gradient(90deg, transparent, rgba(255,255,255,0.06), transparent);
}

/* === Menu Icon === */
.menu-icon {
  width: 20px;
  height: 20px;
  flex-shrink: 0;
  stroke-width: 1.75;
  color: #9CA3AF;
  transition: color 0.15s ease;
}
.dark .menu-icon {
  color: #71717a;
}
.menu-item.collapsed .menu-icon {
  width: 22px;
  height: 22px;
  stroke-width: 2;
}

/* === Menu Items === */
.menu-item {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 10px 16px 10px 12px;
  margin-bottom: 2px;
  border-radius: 8px;
  border-left: 4px solid transparent;
  color: #374151;
  font-size: 15px;
  font-weight: 500;
  line-height: 1.4;
  cursor: pointer;
  transition: all 0.15s ease;
  position: relative;
  user-select: none;
}
.dark .menu-item {
  color: #a1a1aa;
}

/* === Collapsed State === */
.menu-item.collapsed {
  justify-content: center;
  padding: 10px 0;
  gap: 0;
  margin: 1px auto;
  width: 44px;
  height: 44px;
  border-radius: 10px;
  border-left: none;
}

/* === Hover === */
.menu-item:hover {
  background-color: #F3F4F6;
  color: #111827;
}
.menu-item:hover .menu-icon {
  color: #6B7280;
}
.menu-item.collapsed:hover {
  background-color: #F3F4F6;
  color: #111827;
}
.dark .menu-item:hover {
  background-color: rgba(255, 255, 255, 0.06);
  color: #e4e4e7;
}
.dark .menu-item:hover .menu-icon {
  color: #a1a1aa;
}
.dark .menu-item.collapsed:hover {
  background-color: rgba(255, 255, 255, 0.08);
  color: #fafafa;
}

/* === Active State — Verde Corporativo === */
.menu-item.active {
  background-color: #ECFDF5;
  color: #047857;
  font-weight: 700;
  border-left-color: #059669;
}
.dark .menu-item.active {
  background-color: rgba(16, 185, 129, 0.08);
  color: #6ee7b7;
  border-left-color: #34d399;
}

.menu-item.active .menu-icon {
  color: #059669;
}
.dark .menu-item.active .menu-icon {
  color: #34d399;
}

/* Active collapsed */
.menu-item.collapsed.active {
  background-color: #ECFDF5;
  color: #059669;
  border-left: none;
}
.dark .menu-item.collapsed.active {
  background-color: rgba(16, 185, 129, 0.12);
  color: #6ee7b7;
}
.menu-item.collapsed.active::before {
  content: '';
  position: absolute;
  left: 0;
  top: 50%;
  transform: translateY(-50%);
  width: 4px;
  height: 20px;
  background-color: #059669;
  border-radius: 0 4px 4px 0;
}
.dark .menu-item.collapsed.active::before {
  background-color: #34d399;
}

/* === Menu Text === */
.menu-text {
  flex: 1;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  line-height: 1.3;
}

</style>
