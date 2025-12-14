<template>
  <!-- Sidebar Profesional - Diseño Limpio -->
  <div 
    class="sidebar-container fixed inset-y-0 left-0 z-50 transform transition-all duration-300 ease-in-out flex flex-col"
    style="width: 240px; background-color: #FFFFFF; border-right: 1px solid #E0E0E0;"
    :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
  >
    
    <!-- Logo y Marca del Sistema -->
    <div style="padding: 20px 15px; border-bottom: 1px solid #E0E0E0;">
      <div class="flex items-center gap-3">
        <!-- Logo Simple -->
        <div style="width: 40px; height: 40px; background-color: #007BFF; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
          <svg style="width: 24px; height: 24px; color: white;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
          </svg>
        </div>
        
        <!-- Texto del Logo -->
        <div>
          <h1 style="font-size: 18px; font-weight: 700; color: #1F2937; margin: 0;">POS Enterprise</h1>
          <p style="font-size: 12px; color: #9CA3AF; margin: 0;">Sistema Punto de Venta</p>
        </div>
      </div>
    </div>

    <!-- Menú de Navegación Principal -->
    <nav class="flex-1 overflow-y-auto py-4" style="scrollbar-width: thin; scrollbar-color: #E0E0E0 transparent;">
      
      <!-- Dashboard -->
      <div class="mb-6">
        <button
          @click="$emit('change-module', 'dashboard')"
          :class="getMenuClass('dashboard')"
        >
          <div v-if="sidebarCollapsed" :class="getIconClass('dashboard')">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path>
            </svg>
          </div>
          <div v-else class="flex items-center w-full">
            <div :class="getIconClass('dashboard')">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path>
              </svg>
            </div>
            <div class="ml-4 text-left flex-1 min-w-0">
              <div class="font-bold text-sm tracking-wide leading-5 truncate">Dashboard</div>
              <div class="text-xs opacity-80 font-medium leading-4 truncate">Panel de control</div>
            </div>
          </div>
        </button>
      </div>

      <!-- OPERACIONES -->
      <div class="mb-6">
        <!-- Separador -->
        <div v-show="!sidebarCollapsed" class="relative mb-4 px-3">
          <div class="absolute inset-0 flex items-center" aria-hidden="true">
            <div class="w-full border-t border-slate-600/30"></div>
          </div>
          <div class="relative flex justify-center">
            <div class="bg-gradient-to-r from-slate-800 via-slate-900 to-slate-800 px-4 py-1.5 rounded-full border border-slate-700/50 shadow-lg backdrop-blur-sm">
              <h3 class="text-xs font-bold text-slate-300 uppercase tracking-wider flex items-center space-x-2">
                <svg class="w-3 h-3 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                </svg>
                <span>Operaciones</span>
              </h3>
            </div>
          </div>
        </div>
        <div v-if="sidebarCollapsed" class="mx-2 mb-4">
          <div class="h-px bg-gradient-to-r from-slate-700 via-slate-600 to-slate-700"></div>
        </div>
        
        <div class="space-y-2">
          <!-- Punto de Venta -->
          <button
            @click="$emit('change-module', 'pos')"
            :class="getMenuClass('pos', true)"
          >
            <div v-if="sidebarCollapsed" :class="getIconClass('pos', true)">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
              </svg>
            </div>
            <div v-else class="flex items-center w-full">
              <div :class="getIconClass('pos', true)">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                </svg>
              </div>
              <div class="ml-4 text-left flex-1 min-w-0">
                <div class="font-bold text-sm tracking-wide leading-5 truncate">Punto de Venta</div>
                <div class="text-xs opacity-80 font-medium leading-4 truncate">Terminal de ventas</div>
              </div>
            </div>
          </button>

          <!-- Facturación -->
          <button
            @click="$emit('change-module', 'invoices')"
            :class="getMenuClass('invoices')"
          >
            <div v-if="sidebarCollapsed" :class="getIconClass('invoices')">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
              </svg>
            </div>
            <div v-else class="flex items-center w-full">
              <div :class="getIconClass('invoices')">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
              </div>
              <div class="ml-4 text-left flex-1 min-w-0">
                <div class="font-bold text-sm tracking-wide leading-5 truncate">Facturación</div>
                <div class="text-xs opacity-80 font-medium leading-4 truncate">Documentos fiscales</div>
              </div>
            </div>
          </button>

          <!-- Devoluciones -->
          <button
            @click="$emit('change-module', 'returns-management')"
            :class="getMenuClass('returns-management')"
          >
            <div v-if="sidebarCollapsed" :class="getIconClass('returns-management')">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 15v-1a4 4 0 00-4-4H8m0 0l3 3m-3-3l3-3m9 14V5a2 2 0 00-2-2H6a2 2 0 00-2 2v16l4-2 4 2 4-2 4 2z"></path>
              </svg>
            </div>
            <div v-else class="flex items-center w-full">
              <div :class="getIconClass('returns-management')">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 15v-1a4 4 0 00-4-4H8m0 0l3 3m-3-3l3-3m9 14V5a2 2 0 00-2-2H6a2 2 0 00-2 2v16l4-2 4 2 4-2 4 2z"></path>
              </svg>
              </div>
              <div class="ml-4 text-left flex-1 min-w-0">
                <div class="font-bold text-sm tracking-wide leading-5 truncate">Devoluciones</div>
                <div class="text-xs opacity-80 font-medium leading-4 truncate">Control y seguimiento</div>
              </div>
            </div>
          </button>
        </div>
      </div>

      <!-- INVENTARIO -->
      <div class="mb-6">
        <!-- Separador -->
        <div v-show="!sidebarCollapsed" class="relative mb-4 px-3">
          <div class="absolute inset-0 flex items-center" aria-hidden="true">
            <div class="w-full border-t border-slate-600/30"></div>
          </div>
          <div class="relative flex justify-center">
            <div class="bg-gradient-to-r from-slate-800 via-slate-900 to-slate-800 px-4 py-1.5 rounded-full border border-slate-700/50 shadow-lg backdrop-blur-sm">
              <h3 class="text-xs font-bold text-slate-300 uppercase tracking-wider flex items-center space-x-2">
                <svg class="w-3 h-3 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                </svg>
                <span>Inventario</span>
              </h3>
            </div>
          </div>
        </div>
        <div v-if="sidebarCollapsed" class="mx-2 mb-4">
          <div class="h-px bg-gradient-to-r from-slate-700 via-slate-600 to-slate-700"></div>
        </div>

        <div class="space-y-2">
          <!-- Productos -->
          <button
            @click="$emit('change-module', 'products')"
            :class="getMenuClass('products')"
          >
            <div v-if="sidebarCollapsed" :class="getIconClass('products')">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
              </svg>
            </div>
            <div v-else class="flex items-center w-full">
              <div :class="getIconClass('products')">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                </svg>
              </div>
              <div class="ml-4 text-left flex-1 min-w-0">
                <div class="font-bold text-sm tracking-wide leading-5 truncate">Productos</div>
                <div class="text-xs opacity-80 font-medium leading-4 truncate">Catálogo completo</div>
              </div>
            </div>
          </button>

          <!-- Categorías -->
          <button
            @click="$emit('change-module', 'categories')"
            :class="getMenuClass('categories')"
          >
            <div v-if="sidebarCollapsed" :class="getIconClass('categories')">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
              </svg>
            </div>
            <div v-else class="flex items-center w-full">
              <div :class="getIconClass('categories')">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                </svg>
              </div>
              <div class="ml-4 text-left flex-1 min-w-0">
                <div class="font-bold text-sm tracking-wide leading-5 truncate">Categorías</div>
                <div class="text-xs opacity-80 font-medium leading-4 truncate">Organización</div>
              </div>
            </div>
          </button>

          <!-- Control de Stock -->
          <button
            @click="$emit('change-module', 'stock')"
            :class="getMenuClass('stock')"
          >
            <div v-if="sidebarCollapsed" :class="getIconClass('stock')">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
              </svg>
            </div>
            <div v-else class="flex items-center w-full">
              <div :class="getIconClass('stock')">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
              </div>
              <div class="ml-4 text-left flex-1 min-w-0">
                <div class="font-bold text-sm tracking-wide leading-5 truncate">Control Stock</div>
                <div class="text-xs opacity-80 font-medium leading-4 truncate">Existencias</div>
              </div>
            </div>
          </button>

          <!-- Inventario Inteligente -->
          <button
            @click="$emit('change-module', 'intelligent_inventory')"
            :class="getMenuClass('intelligent_inventory')"
          >
            <div v-if="sidebarCollapsed" :class="getIconClass('intelligent_inventory')">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
              </svg>
            </div>
            <div v-else class="flex items-center w-full">
              <div :class="getIconClass('intelligent_inventory')">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                </svg>
              </div>
              <div class="ml-4 text-left flex-1 min-w-0">
                <div class="font-bold text-sm tracking-wide leading-5 truncate">Inventario IA</div>
                <div class="text-xs opacity-80 font-medium leading-4 truncate">Análisis inteligente</div>
              </div>
            </div>
          </button>
        </div>
      </div>

      <!-- RELACIONES -->
      <div class="mb-6">
        <!-- Separador -->
        <div v-show="!sidebarCollapsed" class="relative mb-4 px-3">
          <div class="absolute inset-0 flex items-center" aria-hidden="true">
            <div class="w-full border-t border-slate-600/30"></div>
          </div>
          <div class="relative flex justify-center">
            <div class="bg-gradient-to-r from-slate-800 via-slate-900 to-slate-800 px-4 py-1.5 rounded-full border border-slate-700/50 shadow-lg backdrop-blur-sm">
              <h3 class="text-xs font-bold text-slate-300 uppercase tracking-wider flex items-center space-x-2">
                <svg class="w-3 h-3 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                </svg>
                <span>Relaciones</span>
              </h3>
            </div>
          </div>
        </div>
        <div v-if="sidebarCollapsed" class="mx-2 mb-4">
          <div class="h-px bg-gradient-to-r from-slate-700 via-slate-600 to-slate-700"></div>
        </div>

        <div class="space-y-2">
          <!-- Clientes -->
          <button
            @click="$emit('change-module', 'customers')"
            :class="getMenuClass('customers')"
          >
            <div v-if="sidebarCollapsed" :class="getIconClass('customers')">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
              </svg>
            </div>
            <div v-else class="flex items-center w-full">
              <div :class="getIconClass('customers')">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                </svg>
              </div>
              <div class="ml-4 text-left flex-1 min-w-0">
                <div class="font-bold text-sm tracking-wide leading-5 truncate">Clientes</div>
                <div class="text-xs opacity-80 font-medium leading-4 truncate">Base de datos</div>
              </div>
            </div>
          </button>

          <!-- Proveedores -->
          <button
            @click="$emit('change-module', 'suppliers')"
            :class="getMenuClass('suppliers')"
          >
            <div v-if="sidebarCollapsed" :class="getIconClass('suppliers')">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
              </svg>
            </div>
            <div v-else class="flex items-center w-full">
              <div :class="getIconClass('suppliers')">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                </svg>
              </div>
              <div class="ml-4 text-left flex-1 min-w-0">
                <div class="font-bold text-sm tracking-wide leading-5 truncate">Proveedores</div>
                <div class="text-xs opacity-80 font-medium leading-4 truncate">Gestión</div>
              </div>
            </div>
          </button>

          <!-- Usuarios -->
          <button
            @click="$emit('change-module', 'users')"
            :class="getMenuClass('users')"
          >
            <div v-if="sidebarCollapsed" :class="getIconClass('users')">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
              </svg>
            </div>
            <div v-else class="flex items-center w-full">
              <div :class="getIconClass('users')">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
              </div>
              <div class="ml-4 text-left flex-1 min-w-0">
                <div class="font-bold text-sm tracking-wide leading-5 truncate">Usuarios</div>
                <div class="text-xs opacity-80 font-medium leading-4 truncate">Administración</div>
              </div>
            </div>
          </button>
        </div>
      </div>

      <!-- ADMINISTRACIÓN -->
      <div class="mb-6">
        <!-- Separador -->
        <div v-show="!sidebarCollapsed" class="relative mb-4 px-3">
          <div class="absolute inset-0 flex items-center" aria-hidden="true">
            <div class="w-full border-t border-slate-600/30"></div>
          </div>
          <div class="relative flex justify-center">
            <div class="bg-gradient-to-r from-slate-800 via-slate-900 to-slate-800 px-4 py-1.5 rounded-full border border-slate-700/50 shadow-lg backdrop-blur-sm">
              <h3 class="text-xs font-bold text-slate-300 uppercase tracking-wider flex items-center space-x-2">
                <svg class="w-3 h-3 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
                <span>Administración</span>
              </h3>
            </div>
          </div>
        </div>
        <div v-if="sidebarCollapsed" class="mx-2 mb-4">
          <div class="h-px bg-gradient-to-r from-slate-700 via-slate-600 to-slate-700"></div>
        </div>

        <div class="space-y-2">
          <!-- Panel Admin -->
          <button
            @click="$emit('change-module', 'admin-panel')"
            :class="getMenuClass('admin-panel')"
          >
            <div v-if="sidebarCollapsed" :class="getIconClass('admin-panel')">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
              </svg>
            </div>
            <div v-else class="flex items-center w-full">
              <div :class="getIconClass('admin-panel')">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
              </div>
              <div class="ml-4 text-left flex-1 min-w-0">
                <div class="font-bold text-sm tracking-wide leading-5 truncate">Panel Admin</div>
                <div class="text-xs opacity-80 font-medium leading-4 truncate">Configuración</div>
              </div>
            </div>
          </button>

          <!-- Reportes -->
          <button
            @click="$emit('change-module', 'reports')"
            :class="getMenuClass('reports')"
          >
            <div v-if="sidebarCollapsed" :class="getIconClass('reports')">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
              </svg>
            </div>
            <div v-else class="flex items-center w-full">
              <div :class="getIconClass('reports')">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
              </div>
              <div class="ml-4 text-left flex-1 min-w-0">
                <div class="font-bold text-sm tracking-wide leading-5 truncate">Reportes</div>
                <div class="text-xs opacity-80 font-medium leading-4 truncate">Análisis</div>
              </div>
            </div>
          </button>
        </div>
      </div>

    </nav>

    <!-- Footer del Sidebar -->
    <div class="border-t border-slate-700/50 p-4 bg-slate-900/50">
      <button
        @click="$emit('change-module', 'settings')"
        :class="getMenuClass('settings')"
        class="w-full"
      >
        <div v-if="sidebarCollapsed" :class="getIconClass('settings')">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
          </svg>
        </div>
        <div v-else class="flex items-center w-full">
          <div :class="getIconClass('settings')">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
            </svg>
          </div>
          <div class="ml-4 text-left flex-1 min-w-0">
            <div class="font-bold text-sm tracking-wide leading-5 truncate">Configuración</div>
            <div class="text-xs opacity-80 font-medium leading-4 truncate">Sistema</div>
          </div>
        </div>
      </button>

      <!-- Botón para colapsar/expandir -->
      <button
        v-if="!sidebarCollapsed"
        @click="$emit('toggle-sidebar')"
        class="w-full mt-3 px-3 py-2 bg-slate-800/50 hover:bg-slate-700/50 text-slate-300 rounded-lg transition-colors duration-200 flex items-center justify-center space-x-2 text-xs font-medium"
      >
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"></path>
        </svg>
        <span>Colapsar menú</span>
      </button>
    </div>
  </div>
</template>

<script>
import { ref, computed } from 'vue'

export default {
  name: 'Sidebar',
  props: {
    currentModule: {
      type: String,
      default: 'dashboard'
    },
    sidebarOpen: {
      type: Boolean,
      default: true
    },
    sidebarCollapsed: {
      type: Boolean,
      default: false
    }
  },
  emits: ['change-module', 'toggle-sidebar', 'update:sidebarCollapsed'],
  setup(props, { emit }) {
    const handleMouseEnter = () => {
      // Expandir automáticamente al pasar el mouse cuando está colapsado
      if (props.sidebarCollapsed) {
        emit('update:sidebarCollapsed', false)
      }
    }

    const handleMouseLeave = () => {
      // Opcional: colapsar al salir (puedes activar esto si quieres ese comportamiento)
      // emit('update:sidebarCollapsed', true)
    }

    const getMenuClass = (module, isHighlighted = false) => {
      const isActive = props.currentModule === module
      const baseClasses = 'relative w-full rounded-xl transition-all duration-300 transform group overflow-hidden'
      
      if (props.sidebarCollapsed) {
        return baseClasses + (isActive 
          ? ' bg-gradient-to-br from-blue-600 to-blue-700 shadow-lg shadow-blue-600/30 scale-105'
          : ' hover:bg-slate-700/50 hover:scale-105')
      }
      
      return baseClasses + (isActive 
        ? ' bg-gradient-to-r from-blue-600 to-indigo-600 shadow-lg shadow-blue-600/30 scale-105'
        : ' hover:bg-slate-700/50 hover:scale-105') + ' px-4 py-3'
    }

    const getIconClass = (module, isHighlighted = false) => {
      const isActive = props.currentModule === module
      const baseClasses = 'relative flex items-center justify-center transition-all duration-300'
      
      if (props.sidebarCollapsed) {
        return baseClasses + ' w-full h-12' + (isActive 
          ? ' text-white'
          : ' text-slate-300 group-hover:text-white')
      }
      
      return baseClasses + ' w-10 h-10 rounded-xl flex-shrink-0' + (isActive 
        ? ' bg-white/10 text-white'
        : ' bg-slate-700/30 text-slate-300 group-hover:bg-slate-600/50 group-hover:text-white')
    }

    return {
      handleMouseEnter,
      handleMouseLeave,
      getMenuClass,
      getIconClass
    }
  }
}
</script>

<style scoped>
/* Scrollbar personalizado */
.custom-scrollbar::-webkit-scrollbar {
  width: 6px;
}

.custom-scrollbar::-webkit-scrollbar-track {
  background: rgba(30, 41, 59, 0.3);
  border-radius: 10px;
}

.custom-scrollbar::-webkit-scrollbar-thumb {
  background: rgba(100, 116, 139, 0.5);
  border-radius: 10px;
}

.custom-scrollbar::-webkit-scrollbar-thumb:hover {
  background: rgba(100, 116, 139, 0.7);
}
</style>
