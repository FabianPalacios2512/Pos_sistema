<template>
  <!-- Sistema POS Empresarial Completo -->
  <div :class="{ 'dark': isDarkMode }" class="min-h-screen bg-gray-50 dark:bg-gray-900 transition-colors duration-300">
    
    <!-- Sidebar Profesional y Minimalista -->
    <div class="sidebar-container fixed inset-y-0 left-0 z-50 bg-white dark:bg-gray-800 border-r border-gray-200 dark:border-gray-700 transform transition-all duration-300 ease-in-out flex flex-col" 
         :class="[
           sidebarOpen ? 'translate-x-0' : '-translate-x-full',
           'w-60'
         ]"
         @mouseenter="handleSidebarMouseEnter"
         @mouseleave="handleSidebarMouseLeave">
      
      <!-- Logo y Marca Profesional -->
      <div class="p-6 border-b border-gray-200 dark:border-gray-700">
        <div class="flex items-center space-x-3">
          <!-- Logo Simple y Profesional -->
          <div class="flex-shrink-0">
            <div class="w-10 h-10 bg-blue-600 rounded-lg flex items-center justify-center">
              <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
              </svg>
            </div>
          </div>
          
          <!-- Información del Sistema -->
          <div class="flex-1 min-w-0">
            <h1 class="text-lg font-semibold text-gray-900 dark:text-white truncate">
              POS Enterprise
            </h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 truncate">Sistema Punto de Venta</p>
          </div>
        </div>
      </div>

      <!-- Menú de Navegación Principal -->
      <nav class="flex-1 overflow-y-auto py-4 px-4">
        
        <!-- Dashboard -->
        <div v-if="shouldShowModule('dashboard')" class="mb-2">
          <button
            @click="setCurrentModule('dashboard')"
            class="w-full flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-all duration-200"
            :class="currentModule === 'dashboard' 
              ? 'bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 border-l-3 border-blue-600' 
              : 'text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 hover:text-blue-600 dark:hover:text-blue-400'"
          >
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path>
            </svg>
            Dashboard
          </button>
        </div>

        <!-- Sección: Operaciones -->
        <div class="mb-6">
          <!-- Título de Sección -->
          <h3 class="px-3 mb-2 text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider">
            Operaciones
          </h3>
          </div>
          
          <div class="space-y-1">
            <!-- Punto de Venta -->
            <button
              v-if="shouldShowModule('pos')"
              @click="setCurrentModule('pos')"
              class="w-full flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-all duration-200"
              :class="currentModule === 'pos' 
                ? 'bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 border-l-3 border-blue-600' 
                : 'text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 hover:text-blue-600 dark:hover:text-blue-400'"
            >
              <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
              </svg>
              Punto de Venta
            </button>

            <!-- Facturación -->
            <button
              v-if="shouldShowModule('sales')"
              @click="setCurrentModule('invoices')"
              class="w-full flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-all duration-200"
              :class="currentModule === 'invoices' 
                ? 'bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 border-l-3 border-blue-600' 
                : 'text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 hover:text-blue-600 dark:hover:text-blue-400'"
            >
              <!-- Vista colapsada: solo icono centrado -->
              <div v-if="sidebarCollapsed" :class="getPOSIconClass('invoices')">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
              </div>
              <!-- Vista expandida: icono + texto -->
              <div v-else class="flex items-center w-full">
                <div :class="getPOSIconClass('invoices')">
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

            <!-- Gestión de Devoluciones -->
            <button
              v-if="shouldShowModule('returns-management')"
              @click="setCurrentModule('returns-management')"
              :class="getPOSMenuClass('returns-management')"
            >
              <!-- Vista colapsada: solo icono centrado -->
              <div v-if="sidebarCollapsed" :class="getPOSIconClass('returns-management')">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 15v-1a4 4 0 00-4-4H8m0 0l3 3m-3-3l3-3m9 14V5a2 2 0 00-2-2H6a2 2 0 00-2 2v16l4-2 4 2 4-2 4 2z"></path>
                </svg>
              </div>
              <!-- Vista expandida: icono + texto -->
              <div v-else class="flex items-center w-full">
                <div :class="getPOSIconClass('returns-management')">
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

        <!-- Sección: Inventario Mejorada -->
        <div v-if="shouldShowModule('products')" class="mb-6">
          <div v-show="!sidebarCollapsed" class="relative mb-4 px-3">
            <div class="absolute inset-0 flex items-center" aria-hidden="true">
              <div class="w-full border-t border-gradient-to-r from-transparent via-slate-600/30 to-transparent"></div>
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
            <div class="h-px bg-gradient-to-r from-slate-700 via-slate-600 to-slate-700 shadow-sm"></div>
          </div>
          
          <div class="space-y-1.5">
            <!-- Productos -->
            <button
              @click="setCurrentModule('products')"
              class="w-full flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-all duration-200"
              :class="currentModule === 'products' 
                ? 'bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 border-l-3 border-blue-600' 
                : 'text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 hover:text-blue-600 dark:hover:text-blue-400'"
            >
              <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
              </svg>
              Productos
            </button>

            <!-- Categorías -->
            <button
              v-if="shouldShowModule('categories')"
              @click="setCurrentModule('categories')"
              class="w-full flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-all duration-200"
              :class="currentModule === 'categories' 
                ? 'bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 border-l-3 border-blue-600' 
                : 'text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 hover:text-blue-600 dark:hover:text-blue-400'"
            >
              <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
              </svg>
              Categorías
                  <div class="text-xs opacity-80 font-medium leading-4 truncate">Clasificación</div>
                </div>
              </div>
            </button>

            <!-- Control de Stock -->
            <button
              v-if="shouldShowModule('stock')"
              @click="setCurrentModule('stock')"
              class="w-full flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-all duration-200"
              :class="currentModule === 'stock' 
                ? 'bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 border-l-3 border-blue-600' 
                : 'text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 hover:text-blue-600 dark:hover:text-blue-400'"
            >
              <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
              </svg>
              Control Stock
            </button>

            <!-- Inventario Inteligente -->
            <button
              v-if="shouldShowModule('intelligent_inventory')"
              @click="setCurrentModule('intelligent_inventory')"
              class="w-full flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-all duration-200"
              :class="currentModule === 'intelligent_inventory' 
                ? 'bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 border-l-3 border-blue-600' 
                : 'text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 hover:text-blue-600 dark:hover:text-blue-400'"
            >
              <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
              </svg>
              Inventario IA
            </button>
          </div>
        </div>

        <!-- Sección: Relaciones -->
        <div v-if="shouldShowModule('customers') || shouldShowModule('suppliers')" class="mb-4">
          <div class="px-3 py-2">
            <h3 class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
              Relaciones
            </h3>
          </div>
          
          <div class="space-y-1">
            <!-- Clientes -->
            <button
              v-if="shouldShowModule('customers')"
              @click="setCurrentModule('customers')"
              class="w-full flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-all duration-200"
              :class="currentModule === 'customers' 
                ? 'bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 border-l-3 border-blue-600' 
                : 'text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 hover:text-blue-600 dark:hover:text-blue-400'"
            >
              <!-- Estado colapsado -->
              <div v-if="sidebarCollapsed" :class="getPOSIconClass('customers')">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
              </div>
              <!-- Estado expandido -->
              <div v-else class="flex items-center w-full">
                <div :class="getPOSIconClass('customers')" class="rounded-lg p-2">
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
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
              v-if="shouldShowModule('suppliers')"
              @click="setCurrentModule('suppliers')"
              class="w-full flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-all duration-200"
              :class="currentModule === 'suppliers' 
                ? 'bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 border-l-3 border-blue-600' 
                : 'text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 hover:text-blue-600 dark:hover:text-blue-400'"
            >
              <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
              </svg>
              Proveedores
            </button>
          </div>
        </div>

        <!-- Sección: Sistema -->
        <div v-if="shouldShowModule('users') || shouldShowModule('reports') || shouldShowModule('cash-admin')" class="mb-4">
          <div class="px-3 py-2">
            <h3 class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
              Administración
            </h3>
          </div>
          
          <div class="space-y-1">
            <!-- Usuarios -->
            <button
              v-if="shouldShowModule('users')"
              @click="setCurrentModule('users')"
              class="w-full flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-all duration-200"
              :class="currentModule === 'users' 
                ? 'bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 border-l-3 border-blue-600' 
                : 'text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 hover:text-blue-600 dark:hover:text-blue-400'"
            >
              <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
              </svg>
              Usuarios
            </button>

            <!-- Administración de Cajas -->
            <button
              v-if="shouldShowModule('cash-admin')"
              @click="setCurrentModule('cash-admin')"
              class="w-full flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-all duration-200"
              :class="currentModule === 'cash-admin' 
                ? 'bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 border-l-3 border-blue-600' 
                : 'text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 hover:text-blue-600 dark:hover:text-blue-400'"
            >
              <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
              </svg>
              Panel Administrativo
            </button>

            <!-- Reportes -->
            <button
              v-if="shouldShowModule('reports')"
              @click="setCurrentModule('reports')"
              class="w-full flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-all duration-200"
              :class="currentModule === 'reports' 
                ? 'bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 border-l-3 border-blue-600' 
                : 'text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 hover:text-blue-600 dark:hover:text-blue-400'"
            >
              <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
              </svg>
              Reportes
            </button>
          </div>
        </div>
      </nav>

      <!-- Footer del Sidebar -->
      <div class="border-t border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800">        
        <div class="p-4 space-y-3">
          <!-- Usuario Actual Mejorado -->
          <!-- Usuario Actual Mejorado (eliminado por solicitud) -->
          
          <!-- Controles del Sistema Mejorados -->
          <div class="flex items-center gap-3">
            <!-- Configuración -->
            <button
              v-if="shouldShowModule('settings')"
              @click="setCurrentModule('settings')"
              class="group flex-1 p-3.5 rounded-2xl bg-gradient-to-br from-slate-800/40 via-slate-700/40 to-slate-800/40 hover:from-slate-700/60 hover:via-slate-600/60 hover:to-slate-700/60 text-slate-300 hover:text-white transition-all duration-300 backdrop-blur-sm border border-slate-600/30 hover:border-slate-500/50 hover:shadow-lg hover:scale-[1.02]"
              :title="sidebarCollapsed ? 'Configuración' : ''"
            >
              <div class="flex items-center justify-center" :class="sidebarCollapsed ? '' : 'space-x-3'">
                <div class="w-6 h-6 flex items-center justify-center">
                  <svg class="w-5 h-5 group-hover:rotate-45 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                  </svg>
                </div>
                <span v-show="!sidebarCollapsed" class="text-sm font-semibold tracking-wide">Ajustes</span>
              </div>
            </button>
            
            <!-- Cerrar Sesión -->
            <button
              v-show="!sidebarCollapsed"
              @click="logout"
              class="group p-3.5 rounded-2xl bg-gradient-to-br from-red-500/20 via-red-600/20 to-red-700/20 hover:from-red-500/30 hover:via-red-600/30 hover:to-red-700/30 text-red-300 hover:text-red-200 transition-all duration-300 backdrop-blur-sm border border-red-500/30 hover:border-red-400/50 hover:shadow-lg hover:shadow-red-500/20 hover:scale-105"
              title="Cerrar Sesión"
            >
              <div class="w-6 h-6 flex items-center justify-center">
                <svg class="w-5 h-5 group-hover:translate-x-0.5 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                </svg>
              </div>
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Área Principal de Contenido Adaptable -->
    <div class="transition-all duration-300" 
         :class="{
           'lg:ml-72': !sidebarCollapsed,
           'lg:ml-20': sidebarCollapsed
         }">
      
      <!-- Header Corporativo Profesional -->
  <header class="sticky top-0 z-40 bg-gray-50/95 shadow-md border-b border-gray-200 backdrop-blur-xl">
        <div class="px-6 py-3">
          <div class="flex items-center justify-between">
            <!-- Izquierda: Controles de menú y título -->
            <div class="flex items-center space-x-4 flex-1 min-w-0">
              <!-- Botón menú móvil -->
              <button
                @click="sidebarOpen = !sidebarOpen"
                class="lg:hidden p-2 rounded-lg text-slate-200 hover:text-white hover:bg-slate-800 transition-all duration-200"
                title="Abrir menú"
              >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                </svg>
              </button>
              <!-- Botón menú inteligente restaurado -->
              <button
                @click="autoHideEnabled = !autoHideEnabled"
                class="hidden lg:flex p-2 rounded-lg transition-all duration-200 border"
                :class="autoHideEnabled 
                  ? 'text-blue-600 bg-blue-50 border-blue-200 shadow-sm hover:bg-blue-100' 
                  : 'text-gray-500 hover:text-gray-700 hover:bg-gray-100 border-transparent'"
                :title="autoHideEnabled ? 'Desactivar menú inteligente' : 'Activar menú inteligente'"
              >
                <svg class="w-4 h-4" fill="none" :stroke="autoHideEnabled ? '#2563eb' : 'currentColor'" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                </svg>
              </button>
              <!-- Botón colapsar sidebar -->
              <button
                @click="sidebarCollapsed = !sidebarCollapsed"
                class="hidden lg:flex p-2 rounded-lg text-gray-500 hover:text-gray-700 hover:bg-gray-100 transition-all duration-200 border border-transparent"
                :title="sidebarCollapsed ? 'Expandir menú' : 'Contraer menú'"
              >
                <svg class="w-4 h-4 transition-transform duration-300" :class="{ 'rotate-180': sidebarCollapsed }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"></path>
                </svg>
              </button>
              <!-- Título del Módulo -->
              <div class="flex-1 min-w-0">
                <h1 class="text-xl font-semibold text-gray-900 truncate tracking-tight">
                  {{ getModuleTitle() }}
                </h1>
                <p class="text-sm text-gray-500 truncate font-medium">
                  {{ getModuleDescription() }}
                </p>
              </div>
            </div>
            <!-- Derecha: Fecha, hora y usuario -->
            <div class="flex items-center space-x-6 flex-1 justify-end">
              <div class="hidden md:flex flex-col items-end">
                <span class="text-sm font-medium text-gray-700">{{ currentDate }}</span>
                <span class="text-lg font-bold text-blue-600">{{ currentTime }}</span>
              </div>
              <!-- Perfil de Usuario -->
              <div class="relative">
                <button
                  @click="userDropdownOpen = !userDropdownOpen"
                  class="flex items-center space-x-3 px-3 py-2 rounded-lg text-gray-700 hover:bg-gray-100 transition-all duration-200 border border-transparent hover:border-gray-300"
                >
                  <!-- Avatar -->
                  <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-indigo-400 rounded-full flex items-center justify-center shadow-sm">
                    <span class="text-white font-semibold text-sm">{{ currentUser.initials }}</span>
                  </div>
                  <!-- Información del Usuario -->
                  <div class="hidden lg:block text-left">
                    <div class="font-medium text-sm text-gray-900">{{ currentUser.name }}</div>
                    <div class="text-xs text-gray-500 font-medium">{{ currentUser.role.toUpperCase() }}</div>
                  </div>
                  <!-- Icono Dropdown -->
                  <svg class="w-4 h-4 text-gray-400 transition-transform duration-200" :class="{ 'rotate-180': userDropdownOpen }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                  </svg>
                </button>
                <!-- Dropdown Menu Profesional -->
                <div 
                  v-if="userDropdownOpen"
                  class="absolute right-0 mt-2 w-64 bg-white rounded-xl shadow-lg border border-gray-100 py-2 z-50 backdrop-blur-sm"
                  @click.stop="() => {}"
                >
                  <!-- Información del Usuario -->
                  <div class="px-4 py-3 border-b border-gray-50">
                    <div class="flex items-center space-x-3">
                      <div class="w-10 h-10 bg-gradient-to-br from-blue-600 to-indigo-600 rounded-full flex items-center justify-center shadow-sm">
                        <span class="text-white font-semibold">{{ currentUser.initials }}</span>
                      </div>
                      <div>
                        <div class="font-semibold text-gray-900">{{ currentUser.name }}</div>
                        <div class="text-sm text-gray-500 font-medium">{{ currentUser.role.toUpperCase() }}</div>
                      </div>
                    </div>
                  </div>
                  <!-- Opciones del Menu -->
                  <div class="py-2">
                    <!-- Configuración -->
                    <button
                      v-if="shouldShowModule('settings')"
                      @click="setCurrentModule('settings'); userDropdownOpen = false"
                      class="w-full flex items-center px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 transition-all duration-200 font-medium"
                    >
                      <svg class="w-4 h-4 mr-3 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                      </svg>
                      Configuración
                    </button>
                    <!-- Separador -->
                    <div class="border-t border-gray-100 my-2 mx-4"></div>
                    <!-- Cerrar Sesión -->
                    <button
                      @click="handleLogout"
                      class="w-full flex items-center px-4 py-2.5 text-sm text-red-600 hover:bg-red-50 transition-all duration-200 font-medium"
                    >
                      <svg class="w-4 h-4 mr-3 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                      </svg>
                      Cerrar Sesión
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </header>




      <!-- Contenido Principal -->
      <main class="flex-1 p-6">
        
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
        <component
          v-if="currentModule !== 'dashboard' && currentModule !== 'pos'"
          :is="currentModuleComponent"
          v-bind="getModuleProps()"
          @change-module="setCurrentModule"
          @open-quotation-in-pos="handleOpenQuotationInPos"
        />
        
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

// Router
const router = useRouter()

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

// Componentes temporales para módulos no desarrollados aún
const PlaceholderView = defineAsyncComponent(() => import('../components/PlaceholderView.vue'))

// ===== ESTADO REACTIVO GLOBAL =====

// Configuración UI
const isDarkMode = ref(false)
const sidebarOpen = ref(true)
const sidebarCollapsed = ref(true) // Nueva variable para estado colapsado del sidebar (inicia cerrado)
const currentModule = ref('dashboard')
const currentTime = ref('')
const currentDate = ref('')
const userDropdownOpen = ref(false)

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
  if (user) {
    currentUser.value = {
      name: user.name,
      role: user.role?.name || user.role,
      initials: user.name.split(' ').map(n => n[0]).join('').toUpperCase().substring(0, 2),
      permissions: user.role?.permissions || [] // Usar permisos del rol
    }
    
    // Establecer módulo inicial basándose en los permisos del usuario
    currentModule.value = getFirstAccessibleModule()
    
    // Debug logs removed for production
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
  // Mapeo de módulos a permisos específicos del backend
  const modulePermissions = {
    dashboard: ['dashboard.view'],
    pos: ['sales.view', 'sales.create'],
    products: ['products.view'],
    customers: ['customers.view'],
    categories: ['categories.view'],
    stock: ['inventory.view'],
    intelligent_inventory: ['inventory.analytics', 'inventory.view'],
    suppliers: ['suppliers.view'],
    users: ['users.view'],
    roles: ['roles.view'],
    reports: ['reports.view'],
    settings: ['settings.view'],
    sales: ['sales.view'],
    'returns-management': ['sales.view', 'returns.view']
  }
  
  const requiredPerms = modulePermissions[module] || []
  
  // Si no hay permisos requeridos para este módulo, permitir acceso
  if (requiredPerms.length === 0) return true
  
  // Verificar si el usuario tiene al menos uno de los permisos requeridos
  return requiredPerms.some(perm => currentUser.value.permissions.includes(perm))
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
    users: UsersView,
    roles: RolesView,
    reports: ReportsMenuView,
    settings: SettingsView,
    'cash-admin': CashAdminView,
    'returns-management': ReturnsManagementView
  }
  return moduleComponents[currentModule.value] || null
})

// ===== MÉTODOS =====

// Actualizar fecha y hora
const updateDateTime = () => {
  const now = new Date()
  currentTime.value = now.toLocaleTimeString('es-ES', { 
    hour: '2-digit', 
    minute: '2-digit',
    second: '2-digit'
  })
  currentDate.value = now.toLocaleDateString('es-ES', { 
    weekday: 'long',
    year: 'numeric', 
    month: 'long', 
    day: 'numeric' 
  })
}

// Alternar modo oscuro
const toggleDarkMode = () => {
  isDarkMode.value = !isDarkMode.value
  localStorage.setItem('pos-dark-mode', isDarkMode.value.toString())
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
  
  // Event listener para cerrar dropdown del usuario al hacer clic fuera
  const handleClickOutside = (event) => {
    if (userDropdownOpen.value && !event.target.closest('.relative')) {
      userDropdownOpen.value = false
    }
  }
  document.addEventListener('click', handleClickOutside)
  
  // Cargar configuración
  loadSettings()
  
  // Cargar facturas desde base de datos
  loadInvoices()
  
  // Inicializar Cliente General para ventas sin cliente específico
  ensureDefaultCustomer()
  
  // Inicializar fecha y hora
  updateDateTime()
  
  // Actualizar cada segundo
  timeInterval = setInterval(updateDateTime, 1000)
  
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
  if (timeInterval) {
    clearInterval(timeInterval)
  }
  
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