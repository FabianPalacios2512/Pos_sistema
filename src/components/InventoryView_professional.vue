<template>
  <div class="min-h-screen font-sans bg-gradient-to-br from-gray-50 via-white to-slate-100 dark:bg-gradient-to-b dark:from-[#141417] dark:via-slate-900 dark:to-[#0a0a0c] transition-colors duration-300 px-8">
    <div class="p-4 lg:p-6 space-y-6 pb-8 animate-fade-in">
      
      <!-- Header Simple y Elegante -->
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
          <h1 class="text-2xl font-semibold text-gray-900 dark:text-white tracking-tight">Control de Inventario</h1>
        </div>
        
        <div class="flex items-center space-x-3">
          <!-- Botón Neutro (Actualizar) -->
          <button @click="refreshInventoryData" 
                  :disabled="loading"
                  class="px-4 py-2.5 bg-white dark:bg-zinc-900 hover:bg-slate-50 dark:hover:bg-zinc-800 text-slate-600 dark:text-zinc-200 text-sm font-bold rounded-xl border border-gray-300 dark:border-zinc-800 shadow-sm transition-all duration-200 flex items-center space-x-2"
                  :class="{ 'opacity-50 cursor-not-allowed': loading }">
            <svg class="w-4 h-4" :class="{'animate-spin': loading}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
            </svg>
            <span>{{ loading ? 'Actualizando' : 'Actualizar' }}</span>
          </button>
          
          <!-- Botón Principal (Nuevo Movimiento) -->
          <button @click="openMovementModal"
                  class="px-5 py-2.5 bg-slate-900 dark:bg-slate-700 hover:bg-black dark:hover:bg-slate-600 text-white text-sm font-bold rounded-xl shadow-lg shadow-slate-400/40 dark:shadow-slate-900/50 transition-all duration-300 transform hover:scale-105 flex items-center space-x-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
            </svg>
            <span>Nuevo Movimiento</span>
          </button>
        </div>
      </div>
      
      <!-- Métricas Principales - Estilo Ghost/Glass -->
      <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 mb-4">
        <!-- Total Productos -->
        <div class="bg-white/80 dark:bg-zinc-800/40  rounded-xl px-4 py-3 border-0 shadow-[0_4px_20px_-4px_rgba(0,0,0,0.08)] dark:shadow-[0_4px_20px_-4px_rgba(0,0,0,0.3)] hover:shadow-[0_8px_30px_-4px_rgba(0,0,0,0.12)] dark:hover:shadow-[0_8px_30px_-4px_rgba(0,0,0,0.4)] transition-all duration-200">
          <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-purple-50 dark:bg-purple-950/30 rounded-lg flex items-center justify-center flex-shrink-0">
              <svg class="w-5 h-5 text-purple-500 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z"></path>
              </svg>
            </div>
            <div class="flex-1 min-w-0">
              <p class="text-[11px] font-medium text-gray-500 dark:text-zinc-400 uppercase tracking-wide">Total Productos</p>
              <p class="text-xl font-bold text-gray-900 dark:text-white">{{ totalProducts }}</p>
            </div>
          </div>
        </div>

        <!-- Stock Bajo -->
        <div class="bg-white/80 dark:bg-zinc-800/40  rounded-xl px-4 py-3 border-0 shadow-[0_4px_20px_-4px_rgba(0,0,0,0.08)] dark:shadow-[0_4px_20px_-4px_rgba(0,0,0,0.3)] hover:shadow-[0_8px_30px_-4px_rgba(0,0,0,0.12)] dark:hover:shadow-[0_8px_30px_-4px_rgba(0,0,0,0.4)] transition-all duration-200">
          <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-amber-50 dark:bg-amber-950/30 rounded-lg flex items-center justify-center flex-shrink-0">
              <svg class="w-5 h-5 text-amber-500 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"></path>
              </svg>
            </div>
            <div class="flex-1 min-w-0">
              <p class="text-[11px] font-medium text-gray-500 dark:text-zinc-400 uppercase tracking-wide">Stock Bajo</p>
              <p class="text-xl font-bold text-gray-900 dark:text-white">{{ lowStockProducts }}</p>
            </div>
          </div>
        </div>

        <!-- Valor Inventario -->
        <div class="bg-white/80 dark:bg-zinc-800/40  rounded-xl px-4 py-3 border-0 shadow-[0_4px_20px_-4px_rgba(0,0,0,0.08)] dark:shadow-[0_4px_20px_-4px_rgba(0,0,0,0.3)] hover:shadow-[0_8px_30px_-4px_rgba(0,0,0,0.12)] dark:hover:shadow-[0_8px_30px_-4px_rgba(0,0,0,0.4)] transition-all duration-200">
          <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-emerald-50 dark:bg-emerald-950/30 rounded-lg flex items-center justify-center flex-shrink-0">
              <svg class="w-5 h-5 text-emerald-500 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
              </svg>
            </div>
            <div class="flex-1 min-w-0">
              <p class="text-[11px] font-medium text-gray-500 dark:text-zinc-400 uppercase tracking-wide">Valor Total</p>
              <p class="text-xl font-bold text-gray-900 dark:text-white">${{ totalInventoryValue.toLocaleString() }}</p>
            </div>
          </div>
        </div>

        <!-- Movimientos Hoy -->
        <div class="bg-white/80 dark:bg-zinc-800/40  rounded-xl px-4 py-3 border-0 shadow-[0_4px_20px_-4px_rgba(0,0,0,0.08)] dark:shadow-[0_4px_20px_-4px_rgba(0,0,0,0.3)] hover:shadow-[0_8px_30px_-4px_rgba(0,0,0,0.12)] dark:hover:shadow-[0_8px_30px_-4px_rgba(0,0,0,0.4)] transition-all duration-200">
          <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-blue-50 dark:bg-blue-950/30 rounded-lg flex items-center justify-center flex-shrink-0">
              <svg class="w-5 h-5 text-blue-500 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 21L3 16.5m0 0L7.5 12M3 16.5h13.5m0-13.5L21 7.5m0 0L16.5 12M21 7.5H7.5"></path>
              </svg>
            </div>
            <div class="flex-1 min-w-0">
              <p class="text-[11px] font-medium text-gray-500 dark:text-zinc-400 uppercase tracking-wide">Movimientos Hoy</p>
              <p class="text-xl font-bold text-gray-900 dark:text-white">{{ todayMovements }}</p>
            </div>
          </div>
        </div>
      </div>

      <!-- 🏢 Selector de Sede/Bodega - Diseño minimalista integrado -->
      <div v-if="showWarehouseFilter" class="flex items-center justify-between py-3 px-4 bg-white/80 dark:bg-zinc-800/40  rounded-xl border-0 shadow-[0_4px_20px_-4px_rgba(0,0,0,0.08)] dark:shadow-[0_4px_20px_-4px_rgba(0,0,0,0.3)]">
        <div class="flex items-center gap-3">
          <div class="w-9 h-9 bg-gray-100 dark:bg-zinc-700/50 rounded-lg flex items-center justify-center">
            <svg class="w-4 h-4 text-gray-600 dark:text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
              <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21m-3.75 3.75h.008v.008h-.008v-.008zm0 3h.008v.008h-.008v-.008zm0 3h.008v.008h-.008v-.008z"></path>
            </svg>
          </div>
          <div class="flex items-center gap-2">
            <span class="text-sm font-medium text-gray-500 dark:text-zinc-400">Bodega:</span>
            <select 
              v-model="selectedWarehouse"
              @change="refreshInventoryData"
              class="px-3 py-1.5 bg-transparent border-0 text-sm font-semibold text-gray-900 dark:text-white focus:ring-0 cursor-pointer hover:bg-gray-100 dark:hover:bg-zinc-700/50 rounded-lg transition-colors"
            >
              <option v-for="warehouse in warehouses" :key="warehouse.id" :value="warehouse.id" class="bg-white dark:bg-zinc-800">
                {{ warehouse.name }}{{ warehouse.is_default ? ' (Principal)' : '' }}
              </option>
            </select>
          </div>
        </div>
        <span class="text-xs text-gray-400 dark:text-zinc-500 hidden sm:flex items-center gap-1">
          <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z"></path>
          </svg>
          Filtrando por esta sede
        </span>
      </div>
      
      <!-- Contenedor Unificado: Pestañas + Contenido -->
      <div class="bg-white dark:bg-zinc-900 rounded-2xl shadow-xl dark:shadow-black/50 border border-gray-200 dark:border-zinc-800 overflow-hidden">
        <!-- Tabs estilo Proveedores -->
        <div class="border-b border-gray-200 dark:border-zinc-800">
          <nav class="flex px-6" aria-label="Tabs">
            <button @click="activeTab = 'stock'" 
                    :class="[
                      'group inline-flex items-center gap-2 py-4 px-1 border-b-2 font-medium text-sm transition-colors mr-8',
                      activeTab === 'stock' 
                        ? 'border-gray-900 dark:border-white text-gray-900 dark:text-white' 
                        : 'border-transparent text-gray-500 dark:text-zinc-400 hover:text-gray-700 dark:hover:text-zinc-300 hover:border-gray-300 dark:hover:border-zinc-600'
                    ]">
              <svg class="w-4 h-4" :class="activeTab === 'stock' ? 'text-gray-900 dark:text-white' : 'text-gray-400 dark:text-zinc-500 group-hover:text-gray-500 dark:group-hover:text-zinc-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z"></path>
              </svg>
              Stock Actual
            </button>
            <button @click="activeTab = 'movements'" 
                    :class="[
                      'group inline-flex items-center gap-2 py-4 px-1 border-b-2 font-medium text-sm relative transition-colors mr-8',
                      activeTab === 'movements' 
                        ? 'border-gray-900 dark:border-white text-gray-900 dark:text-white' 
                        : 'border-transparent text-gray-500 dark:text-zinc-400 hover:text-gray-700 dark:hover:text-zinc-300 hover:border-gray-300 dark:hover:border-zinc-600'
                    ]">
              <svg class="w-4 h-4" :class="activeTab === 'movements' ? 'text-gray-900 dark:text-white' : 'text-gray-400 dark:text-zinc-500 group-hover:text-gray-500 dark:group-hover:text-zinc-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 21L3 16.5m0 0L7.5 12M3 16.5h13.5m0-13.5L21 7.5m0 0L16.5 12M21 7.5H7.5"></path>
              </svg>
              Movimientos
              <span v-if="unreadMovementsCount > 0" 
                    class="ml-1 inline-flex items-center justify-center w-5 h-5 text-[10px] font-bold text-white bg-rose-500 rounded-full">
                {{ unreadMovementsCount }}
              </span>
            </button>
            <button @click="activeTab = 'alerts'" 
                    :class="[
                      'group inline-flex items-center gap-2 py-4 px-1 border-b-2 font-medium text-sm relative transition-colors',
                      activeTab === 'alerts' 
                        ? 'border-gray-900 dark:border-white text-gray-900 dark:text-white' 
                        : 'border-transparent text-gray-500 dark:text-zinc-400 hover:text-gray-700 dark:hover:text-zinc-300 hover:border-gray-300 dark:hover:border-zinc-600'
                    ]">
              <svg class="w-4 h-4" :class="activeTab === 'alerts' ? 'text-gray-900 dark:text-white' : 'text-gray-400 dark:text-zinc-500 group-hover:text-gray-500 dark:group-hover:text-zinc-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0"></path>
              </svg>
              Alertas
              <span v-if="unreadAlertsCount > 0" 
                    class="ml-1 inline-flex items-center justify-center w-5 h-5 text-[10px] font-bold text-white bg-rose-500 rounded-full">
                {{ unreadAlertsCount }}
              </span>
            </button>
          </nav>
        </div>
        
        <!-- Contenido Stock Actual -->
        <div v-if="activeTab === 'stock'">
          <!-- Indicador de carga -->
          <div v-if="loading" class="flex items-center justify-center py-12">
            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-slate-600"></div>
            <span class="ml-3 text-sm text-slate-600">Cargando productos...</span>
          </div>
          
          <div v-else>
            <!-- Filtros Compactos -->
            <div class="p-4 border-b border-gray-200 dark:border-zinc-800 bg-gray-50 dark:bg-zinc-800/50">
              <div class="flex flex-wrap items-center gap-3">
                <div class="flex-1 min-w-48 relative">
                  <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-gray-400 dark:text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                  </svg>
                  <input v-model="searchTerm" 
                         type="text" 
                         placeholder="Buscar productos..." 
                         class="w-full pl-9 pr-3 py-2 text-sm border border-gray-300 dark:border-zinc-700 rounded-lg bg-white dark:bg-zinc-900 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-zinc-500 focus:ring-2 focus:ring-slate-900 dark:focus:ring-blue-500 focus:border-transparent transition-all shadow-sm">
                </div>
                
                <select v-model="categoryFilter" 
                        class="pl-3 pr-8 py-2 text-sm border border-gray-300 dark:border-zinc-700 rounded-lg bg-white dark:bg-zinc-900 text-gray-700 dark:text-zinc-300 focus:ring-2 focus:ring-slate-900 dark:focus:ring-blue-500 focus:border-transparent min-w-36 shadow-sm h-[38px]">
                  <option value="">Todas las categorías</option>
                  <option v-for="category in categories" :key="category.id" :value="category.name">
                    {{ category.name }}
                  </option>
                </select>
                
                <select v-model="stockFilter" 
                        class="pl-3 pr-8 py-2 text-sm border border-gray-300 dark:border-zinc-700 rounded-lg bg-white dark:bg-zinc-900 text-gray-700 dark:text-zinc-300 focus:ring-2 focus:ring-slate-900 dark:focus:ring-blue-500 focus:border-transparent min-w-36 shadow-sm h-[38px]">
                  <option value="">Todo el stock</option>
                  <option value="low">Stock bajo</option>
                  <option value="normal">Stock normal</option>
                  <option value="high">Stock alto</option>
                </select>
                
                <button @click="searchTerm = ''; categoryFilter = ''; stockFilter = ''" 
                        class="p-2 text-gray-500 dark:text-zinc-400 hover:text-red-500 dark:hover:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors border border-transparent hover:border-red-200 dark:hover:border-red-800 h-[38px] w-[38px] flex items-center justify-center"
                        title="Limpiar filtros">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                          d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                  </svg>
                </button>
              </div>
            </div>
          
          <!-- Tabla de Stock -->
          <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-zinc-800">
              <thead class="bg-gray-50 dark:bg-zinc-900">
                <tr>
                  <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 dark:text-zinc-400 uppercase tracking-wider">Producto</th>
                  <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 dark:text-zinc-400 uppercase tracking-wider">Categoría</th>
                  <th class="px-6 py-4 text-center text-xs font-bold text-gray-500 dark:text-zinc-400 uppercase tracking-wider">Stock</th>
                  <th class="px-6 py-4 text-center text-xs font-bold text-gray-500 dark:text-zinc-400 uppercase tracking-wider">Ventas</th>
                  <th class="px-6 py-4 text-right text-xs font-bold text-gray-500 dark:text-zinc-400 uppercase tracking-wider">Ingresos</th>
                  <th class="px-6 py-4 text-center text-xs font-bold text-gray-500 dark:text-zinc-400 uppercase tracking-wider">Estado</th>
                  <th class="px-6 py-4 text-center text-xs font-bold text-gray-500 dark:text-zinc-400 uppercase tracking-wider">Acciones</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-200 dark:divide-zinc-800 bg-white dark:bg-zinc-900">
                <tr v-for="product in paginatedProducts" :key="product.id" class="hover:bg-slate-50 dark:hover:bg-zinc-800/50 hover:shadow-sm transition-colors group">
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center">
                      <!-- Imagen o Ícono del producto -->
                      <div class="w-12 h-12 rounded-lg mr-4 shadow-sm border border-gray-200 dark:border-zinc-700 overflow-hidden flex-shrink-0"
                           :class="getProductImage(product) ? 'bg-white dark:bg-zinc-800' : 'bg-gray-100 dark:bg-zinc-800 flex items-center justify-center'">
                        <img v-if="getProductImage(product)"
                             :src="getProductImage(product)" 
                             :alt="product.name"
                             class="w-full h-full object-cover">
                        <svg v-else class="w-6 h-6 text-gray-400 dark:text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.75 7.5h16.5m-15.75 0l1.5-1.875A2.625 2.625 0 018.25 4.5h7.5a2.625 2.625 0 012.25 1.125l1.5 1.875"/>
                        </svg>
                      </div>
                      <div>
                        <p class="text-sm font-bold text-gray-900 dark:text-white">{{ product.name }}</p>
                        <p class="text-xs text-gray-500 dark:text-zinc-400 font-medium">{{ product.barcode || 'Sin código' }}</p>
                      </div>
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 dark:bg-zinc-800 text-gray-800 dark:text-zinc-300">
                      {{ product.category }}
                    </span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-center">
                    <div class="flex flex-col items-center">
                      <span class="text-sm font-black text-gray-900 dark:text-white">{{ product.current_stock || 0 }}</span>
                      <span class="text-[10px] text-gray-500 dark:text-zinc-400">Min: {{ product.min_stock || 10 }}</span>
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-center">
                    <div class="flex flex-col items-center">
                      <span class="text-sm font-bold text-gray-700 dark:text-zinc-300">{{ product.total_sold || 0 }}</span>
                      <span class="text-[10px] text-gray-400 dark:text-zinc-500">unidades</span>
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-right">
                    <div class="flex flex-col items-end">
                      <span class="text-sm font-black text-emerald-600 dark:text-emerald-400">${{ parseFloat(product.total_revenue || 0).toLocaleString() }}</span>
                      <span class="text-[10px] text-gray-400 dark:text-zinc-500">Precio: ${{ parseFloat(product.sale_price || 0).toLocaleString() }}</span>
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-center">
                    <span :class="[
                      'inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wide border',
                      product.current_stock <= product.min_stock 
                        ? 'bg-rose-50 dark:bg-rose-950 text-rose-700 dark:text-rose-400 border-rose-100 dark:border-rose-800' 
                        : 'bg-emerald-50 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-400 border-emerald-100 dark:border-emerald-800'
                    ]">
                      {{ product.current_stock <= product.min_stock ? 'Bajo Stock' : 'En Stock' }}
                    </span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-center">
                    <div class="flex items-center justify-center gap-2">
                      <button @click="adjustStock(product)" 
                              class="p-2.5 text-gray-400 dark:text-zinc-500 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-lg transition-all"
                              title="Ajustar Stock">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                      </button>
                      <button @click="viewMovements(product)" 
                              class="p-2.5 text-gray-400 dark:text-zinc-500 hover:text-gray-900 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-zinc-800 rounded-lg transition-all"
                              title="Ver Historial">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                      </button>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
          
          <!-- Componente de Paginación - Solo mostrar si hay más de 10 productos -->
          <TablePaginator
            v-if="filteredProducts.length > 10"
            :current-page="currentPage"
            :total-pages="totalPages"
            :total-items="filteredProducts.length"
            :items-per-page="itemsPerPage"
            label="productos"
            @update:currentPage="goToPage"
            @update:itemsPerPage="changeItemsPerPage"
          />
          </div>
        </div>
        
        <!-- Contenido Movimientos -->
        <div v-if="activeTab === 'movements'" class="p-5">
          <MovementsSection 
            :data="movementsData" 
            :loading="movementsLoading"
            @refresh="loadMovementsData"
            @filter-change="handleMovementsFilter"
            @page-change="handleMovementsPageChange"
          />
        </div>
        
        <!-- Contenido Alertas -->
        <div v-if="activeTab === 'alerts'" class="p-6 animate-fade-in">
          <!-- Lista de Alertas - Sin KPIs redundantes -->
          <div class="space-y-3">
            <div v-if="stockAlerts.length === 0" class="text-center py-16">
              <div class="w-16 h-16 bg-emerald-50 dark:bg-emerald-950/30 rounded-2xl flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-emerald-500 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
              </div>
              <p class="text-lg font-semibold text-gray-900 dark:text-white mb-1">¡Todo en orden!</p>
              <p class="text-sm text-gray-500 dark:text-zinc-400">Todos los productos tienen stock suficiente</p>
            </div>

            <div v-for="alert in stockAlerts" :key="alert.id" 
                 class="bg-white/80 dark:bg-zinc-800/40  rounded-xl p-4 border-0 shadow-[0_4px_20px_-4px_rgba(0,0,0,0.08)] dark:shadow-[0_4px_20px_-4px_rgba(0,0,0,0.3)] hover:shadow-[0_8px_30px_-4px_rgba(0,0,0,0.12)] dark:hover:shadow-[0_8px_30px_-4px_rgba(0,0,0,0.4)] transition-all duration-200">
              <div class="flex items-center justify-between gap-4">
                <div class="flex items-center gap-4 flex-1 min-w-0">
                  <!-- Imagen o Ícono del producto -->
                  <div class="w-12 h-12 rounded-xl overflow-hidden flex-shrink-0 ring-2 ring-white dark:ring-zinc-700"
                       :class="getProductImage(alert) ? 'bg-white dark:bg-zinc-800' : 'bg-gray-100 dark:bg-zinc-800 flex items-center justify-center'">
                    <img v-if="getProductImage(alert)"
                         :src="getProductImage(alert)" 
                         :alt="alert.name"
                         class="w-full h-full object-cover">
                    <svg v-else class="w-6 h-6 text-gray-400 dark:text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.75 7.5h16.5m-15.75 0l1.5-1.875A2.625 2.625 0 018.25 4.5h7.5a2.625 2.625 0 012.25 1.125l1.5 1.875"/>
                    </svg>
                  </div>
                  
                  <!-- Información del producto -->
                  <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2 mb-1">
                      <h4 class="text-sm font-semibold text-gray-900 dark:text-white truncate">{{ alert.name }}</h4>
                      <span v-if="alert.stock === 0" 
                            class="px-2 py-0.5 rounded-md text-[10px] font-bold uppercase bg-rose-100 dark:bg-rose-950/50 text-rose-600 dark:text-rose-400">
                        Agotado
                      </span>
                      <span v-else 
                            class="px-2 py-0.5 rounded-md text-[10px] font-bold uppercase bg-amber-100 dark:bg-amber-950/50 text-amber-600 dark:text-amber-400">
                        Bajo Stock
                      </span>
                    </div>
                    <div class="flex items-center gap-4 text-xs text-gray-500 dark:text-zinc-400">
                      <span>Stock: <strong class="text-gray-900 dark:text-white">{{ alert.stock }}</strong> / {{ alert.min_stock }}</span>
                      <span>Categoría: <strong class="text-gray-700 dark:text-zinc-300">{{ alert.category }}</strong></span>
                    </div>
                  </div>
                </div>
                
                <!-- Botón de acción -->
                <button @click="adjustStock(alert)" 
                        class="px-4 py-2 bg-gray-900 dark:bg-white/10 hover:bg-black dark:hover:bg-white/20 text-white text-xs font-semibold rounded-lg transition-all duration-200 flex items-center gap-1.5 flex-shrink-0">
                  <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                  </svg>
                  Reponer
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
      
    </div>
    
    <!-- Modal Nuevo Movimiento Mejorado -->
    <div v-if="showMovementModal" 
         class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50"
         @click.self="closeMovementModal">
      <div class="bg-white rounded-xl p-6 max-w-lg w-full shadow-2xl max-h-[90vh] overflow-y-auto border border-slate-200">
        <!-- Header del Modal -->
        <div class="flex items-center justify-between pb-4 border-b border-slate-200 mb-6">
          <div class="flex items-center space-x-3">
            <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center">
              <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
              </svg>
            </div>
            <div>
              <h3 class="text-base font-bold text-slate-900">Nuevo Movimiento</h3>
              <p class="text-xs text-slate-600">Registra entradas y salidas de inventario</p>
            </div>
          </div>
          <button @click="closeMovementModal" 
                  class="text-slate-400 hover:text-slate-600 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
          </button>
        </div>
        
        <div class="space-y-6">
          <!-- Filtro por Categoría -->
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">Filtrar por Categoría (Opcional)</label>
            <select v-model="modalCategoryFilter" 
                    class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
              <option value="">Todas las categorías</option>
              <option v-for="category in modalCategoriesForSelect" :key="category" :value="category">
                {{ category }}
              </option>
            </select>
          </div>

          <!-- Búsqueda de Producto -->
          <div class="relative">
            <label class="block text-sm font-semibold text-gray-700 mb-2">
              Buscar Producto
              <span class="text-xs font-normal text-gray-500">(por nombre, código de barras, SKU o ID)</span>
            </label>
            <input 
              ref="modalProductInput"
              v-model="modalSearchTerm" 
              @input="onSearchInput"
              @focus="modalShowDropdown = modalSearchTerm.length >= 1"
              type="text" 
              class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm"
              placeholder="Escribe para buscar producto..."
              autocomplete="off">
            
            <!-- Producto Seleccionado -->
            <div v-if="selectedProduct && !modalShowDropdown" 
                 class="mt-3 p-4 bg-green-50 border border-green-200 rounded-xl">
              <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                  <div v-if="selectedProduct.image_url" class="w-12 h-12 rounded-lg overflow-hidden border border-green-300 flex-shrink-0">
                    <img :src="selectedProduct.image_url" 
                         :alt="selectedProduct.name"
                         class="w-full h-full object-cover"
                         @error="(e) => e.target.parentElement.innerHTML = `<div class='w-full h-full bg-green-100 flex items-center justify-center'><span class='text-sm font-bold text-green-700'>${selectedProduct.name.charAt(0).toUpperCase()}</span></div>`">
                  </div>
                  <div v-else class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center flex-shrink-0 border border-green-300">
                    <span class="text-lg font-bold text-green-700">{{ selectedProduct.name.charAt(0).toUpperCase() }}</span>
                  </div>
                  <div>
                    <p class="font-semibold text-green-800">{{ selectedProduct.name }}</p>
                    <p class="text-sm text-green-600">
                      Stock actual: {{ selectedProduct.current_stock || 0 }} unidades
                    </p>
                    <p class="text-xs text-green-500">{{ selectedProduct.barcode || 'Sin código' }}</p>
                  </div>
                </div>
                <button @click="clearProductSelection" 
                        class="p-2 text-green-600 hover:text-green-800 hover:bg-green-100 rounded-lg transition-colors">
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                  </svg>
                </button>
              </div>
            </div>

            <!-- Dropdown de Resultados -->
            <div v-if="modalShowDropdown && modalFilteredProducts.length > 0" 
                 class="absolute z-10 w-full mt-1 bg-white border border-gray-300 rounded-xl shadow-xl max-h-60 overflow-y-auto">
              <div v-for="product in modalFilteredProducts" 
                   :key="product.id"
                   @click="selectProduct(product)"
                   class="p-4 hover:bg-gray-50 cursor-pointer border-b border-gray-100 last:border-b-0">
                <div class="flex items-center justify-between gap-3">
                  <div class="flex items-center gap-3 flex-1">
                    <div v-if="product.image_url" class="w-10 h-10 rounded-lg overflow-hidden border border-gray-200 flex-shrink-0">
                      <img :src="product.image_url" 
                           :alt="product.name"
                           class="w-full h-full object-cover"
                           @error="(e) => e.target.style.display='none'">
                    </div>
                    <div v-else class="w-10 h-10 rounded-lg flex items-center justify-center flex-shrink-0 border border-gray-200" :style="{backgroundColor: `hsl(${product.id * 137.5 % 360}, 70%, 92%)`}">
                      <span class="text-sm font-bold" :style="{color: `hsl(${product.id * 137.5 % 360}, 80%, 35%)`}">
                        {{ product.name.charAt(0).toUpperCase() }}
                      </span>
                    </div>
                    <div class="flex-1 min-w-0">
                      <p class="font-semibold text-gray-900">{{ product.name }}</p>
                      <p class="text-sm text-gray-600">{{ product.category }}</p>
                      <p class="text-xs text-gray-500">
                        {{ product.barcode || 'Sin código' }} | Stock: {{ product.current_stock || 0 }}
                      </p>
                    </div>
                  </div>
                  <div class="text-right">
                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold"
                          :class="getStockStatusClass(product.current_stock || 0, product.min_stock || 10)">
                      {{ getStockStatusLabel(product.current_stock || 0, product.min_stock || 10) }}
                    </span>
                  </div>
                </div>
              </div>
            </div>

            <!-- Mensaje cuando no hay resultados -->
            <div v-if="modalShowDropdown && modalSearchTerm.length >= 1 && modalFilteredProducts.length === 0" 
                 class="absolute z-10 w-full mt-1 bg-white border border-gray-300 rounded-xl shadow-xl p-4 text-center text-gray-500">
              No se encontraron productos con: "{{ modalSearchTerm }}"
            </div>
          </div>
          
          <!-- Tipo y Cantidad -->
          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-2">Tipo de Movimiento</label>
              <select v-model="newMovementForm.type" 
                      class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                <option value="entrada">📦 Entrada</option>
                <option value="salida">📤 Salida</option>
              </select>
            </div>
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-2">Cantidad</label>
              <input v-model="newMovementForm.quantity" 
                     type="number" 
                     min="1"
                     class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm"
                     placeholder="0">
            </div>
          </div>
          
          <!-- Motivo -->
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">Motivo del Movimiento</label>
            <input v-model="newMovementForm.reason" 
                   type="text" 
                   class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm"
                   placeholder="Ej: Compra, Venta, Ajuste de inventario, Devolución...">
          </div>

          <!-- Alerta Stock Bajo -->
          <div v-if="selectedProduct && newMovementForm.type === 'salida' && 
                     parseInt(newMovementForm.quantity || 0) > (selectedProduct.current_stock || 0)" 
               class="p-4 bg-red-50 border border-red-200 rounded-xl">
            <div class="flex items-center space-x-3">
              <div class="w-8 h-8 bg-red-100 rounded-lg flex items-center justify-center">
                <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>
              </div>
              <div>
                <p class="text-sm font-semibold text-red-800">Stock insuficiente</p>
                <p class="text-xs text-red-700">
                  Solo hay {{ selectedProduct.current_stock || 0 }} unidades disponibles.
                </p>
              </div>
            </div>
          </div>
        </div>
        
        <!-- Botones de Acción -->
        <div class="bg-slate-50 border-t border-slate-200 px-6 py-4 -mx-6 -mb-6 mt-6 flex justify-end gap-3">
          <button @click="closeMovementModal" 
                  class="px-4 py-2.5 bg-white hover:bg-slate-50 text-slate-600 text-sm font-semibold rounded-xl border border-slate-300 transition-all duration-200">
            Cancelar
          </button>
          <button @click="createMovement" 
                  :disabled="!selectedProduct || !newMovementForm.quantity || !newMovementForm.reason"
                  class="px-5 py-2.5 bg-black hover:bg-slate-900 disabled:bg-gray-300 disabled:cursor-not-allowed text-white rounded-lg text-sm font-bold transition-all duration-200 shadow-sm">
            Registrar Movimiento
          </button>
        </div>
      </div>
    </div>

    <!-- Modal Ajustar Stock -->
    <div v-if="showAdjustModal" 
         class="fixed inset-0 bg-black/60  flex items-center justify-center p-4 z-50 animate-fade-in"
         @click.self="showAdjustModal = false">
      <div class="bg-white dark:bg-zinc-900 rounded-2xl w-full max-w-5xl shadow-2xl border border-gray-200 dark:border-zinc-800 flex flex-col max-h-[90vh] animate-scale-in">
        
        <!-- Header Mejorado -->
        <div class="bg-white dark:bg-zinc-900 border-b border-gray-200 dark:border-zinc-800 px-8 py-5 flex-shrink-0">
          <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
              <div class="w-12 h-12 bg-gray-100 dark:bg-zinc-800 rounded-xl flex items-center justify-center border border-gray-200 dark:border-zinc-700">
                <svg class="w-6 h-6 text-gray-700 dark:text-zinc-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
              </div>
              <div>
                <h3 class="text-xl font-bold text-gray-900 dark:text-white">Ajustar Stock</h3>
                <p class="text-xs text-gray-500 dark:text-zinc-400 mt-0.5">Modifica la cantidad en inventario</p>
              </div>
            </div>
            <button @click="showAdjustModal = false" 
                    class="p-2 hover:bg-gray-100 dark:hover:bg-zinc-800 rounded-lg transition-all">
              <svg class="w-5 h-5 text-gray-600 dark:text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
              </svg>
            </button>
          </div>
        </div>
        
        <!-- Contenido del Modal -->
        <div class="flex-1 overflow-y-auto bg-gray-50 dark:bg-zinc-950 p-6" v-if="selectedProductForAdjust">
          
          <!-- Información del Producto -->
          <div class="bg-white dark:bg-zinc-900 rounded-xl p-5 shadow-sm border border-gray-200 dark:border-zinc-800 mb-5">
            <div class="flex items-center space-x-4">
              <div class="w-12 h-12 bg-gray-100 dark:bg-zinc-800 rounded-xl flex items-center justify-center border border-gray-200 dark:border-zinc-700 flex-shrink-0">
                <span class="text-base font-bold text-blue-600 dark:text-blue-400">{{ selectedProductForAdjust.name.charAt(0) }}</span>
              </div>
              <div class="flex-1">
                <h4 class="text-base font-bold text-gray-900 dark:text-white">{{ selectedProductForAdjust.name }}</h4>
                <div class="flex items-center gap-3 mt-1">
                  <span class="text-xs text-gray-600 dark:text-zinc-400">{{ selectedProductForAdjust.category }}</span>
                  <span v-if="productVariants.length > 0" class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-purple-50 dark:bg-purple-950 text-purple-700 dark:text-purple-400 border border-purple-200 dark:border-purple-800">
                    👗 MODA/VARIANTES
                  </span>
                  <div v-else class="flex items-center gap-3">
                    <span class="text-xs text-gray-500 dark:text-zinc-500">Stock:</span>
                    <span class="text-sm font-bold text-blue-700 dark:text-blue-400">{{ selectedProductForAdjust.current_stock || 0 }} uds</span>
                    <span class="text-xs text-gray-400 dark:text-zinc-600">|</span>
                    <span class="text-xs text-gray-500 dark:text-zinc-500">Mínimo:</span>
                    <span class="text-sm font-semibold text-gray-700 dark:text-zinc-300">{{ selectedProductForAdjust.min_stock || 10 }} uds</span>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Grid de Formulario -->
          <div class="grid grid-cols-12 gap-5">
            
            <!-- Columna Izquierda: Variantes (si existen) -->
            <div v-if="productVariants.length > 0" class="col-span-5 space-y-5">
              <div class="bg-white dark:bg-zinc-900 rounded-xl p-5 shadow-sm border border-gray-200 dark:border-zinc-800">
                <h4 class="text-sm font-bold text-gray-900 dark:text-white mb-3">Selecciona la Variante</h4>
                <div class="space-y-2 max-h-[320px] overflow-y-auto">
                  <button
                    v-for="variant in productVariants"
                    :key="variant.id"
                    @click="selectVariant(variant)"
                    type="button"
                    :class="[
                      'w-full p-3 rounded-lg border-2 transition-all text-left',
                      selectedVariant?.id === variant.id
                        ? 'border-purple-500 bg-purple-50 dark:bg-purple-950/30'
                        : 'border-gray-200 dark:border-zinc-700 hover:border-purple-300 dark:hover:border-purple-700'
                    ]"
                  >
                    <div class="flex items-start gap-2">
                      <svg v-if="selectedVariant?.id === variant.id" class="w-5 h-5 text-purple-600 dark:text-purple-400 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                      </svg>
                      <div class="flex-1 min-w-0">
                        <p class="font-bold text-gray-900 dark:text-white text-sm mb-1">
                          {{ (typeof variant.options_summary === 'string' ? JSON.parse(variant.options_summary) : variant.options_summary).map(opt => `${opt.name}: ${opt.value}`).join(' | ') }}
                        </p>
                        <div class="flex items-center gap-2 text-xs text-gray-600 dark:text-zinc-400">
                          <span>{{ variant.sku }}</span>
                          <span class="text-gray-300 dark:text-zinc-700">|</span>
                          <span class="font-bold text-blue-700 dark:text-blue-400">{{ variant.stock || 0 }} uds</span>
                          <span class="text-gray-300 dark:text-zinc-700">|</span>
                          <span>${{ Number(variant.price || 0).toLocaleString('es-CO') }}</span>
                        </div>
                      </div>
                    </div>
                  </button>
                </div>
                <p v-if="adjustForm.errors.variant" class="text-red-600 dark:text-red-400 text-xs mt-2 flex items-center">
                  <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 15.5c-.77.833.192 2.5 1.732 2.5z"></path>
                  </svg>
                  {{ adjustForm.errors.variant }}
                </p>
              </div>
            </div>

            <!-- Columna Derecha: Formulario de Ajuste -->
            <div :class="productVariants.length > 0 ? 'col-span-7' : 'col-span-12'">
              <div class="bg-white dark:bg-zinc-900 rounded-xl p-5 shadow-sm border border-gray-200 dark:border-zinc-800 space-y-4">
                
                <!-- Tipo de Ajuste -->
                <div>
                  <label class="block text-xs font-semibold text-gray-700 dark:text-zinc-300 mb-2">Tipo de Ajuste</label>
                  <div class="grid grid-cols-2 gap-3">
                    <button type="button" @click="setQuickAdjustment('restock')"
                            :class="['p-3 rounded-xl border-2 transition-all', 
                                     adjustForm.adjustmentType === 'restock' 
                                       ? 'border-green-500 bg-green-50 dark:bg-green-950/30 text-green-700 dark:text-green-400' 
                                       : 'border-gray-200 dark:border-zinc-700 hover:border-green-300 dark:hover:border-green-700 text-gray-700 dark:text-zinc-400']">
                      <div class="flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        <span class="font-semibold text-sm">Restock</span>
                      </div>
                    </button>
                    <button type="button" @click="setQuickAdjustment('correction')"
                            :class="['p-3 rounded-xl border-2 transition-all', 
                                     adjustForm.adjustmentType === 'correction' 
                                       ? 'border-orange-500 bg-orange-50 dark:bg-orange-950/30 text-orange-700 dark:text-orange-400' 
                                       : 'border-gray-200 dark:border-zinc-700 hover:border-orange-300 dark:hover:border-orange-700 text-gray-700 dark:text-zinc-400']">
                      <div class="flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        <span class="font-semibold text-sm">Corrección</span>
                      </div>
                    </button>
                  </div>
                </div>

                <!-- Nuevo Stock -->
                <div>
                  <label class="block text-xs font-semibold text-gray-700 dark:text-zinc-300 mb-2">Nuevo Stock</label>
                  <div class="relative">
                    <input v-model="adjustForm.new_stock" 
                           type="number" 
                           min="0"
                           step="1"
                           :class="['w-full px-4 py-3 pr-12 border-2 rounded-xl transition-all focus:outline-none text-sm',
                                    adjustForm.errors.new_stock 
                                      ? 'border-red-300 focus:border-red-500 bg-red-50 dark:bg-red-950/20' 
                                      : 'border-gray-200 dark:border-zinc-700 focus:border-blue-500 dark:focus:border-blue-400 bg-white dark:bg-zinc-800 text-gray-900 dark:text-white']"
                           placeholder="Ingresa la cantidad nueva"
                           @blur="validateNewStock">
                    <span class="absolute right-3 top-1/2 -translate-y-1/2 text-sm text-gray-400 dark:text-zinc-500 font-medium">uds</span>
                  </div>
                  <p v-if="adjustForm.errors.new_stock" class="text-red-600 dark:text-red-400 text-xs mt-1.5 flex items-center">
                    <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 15.5c-.77.833.192 2.5 1.732 2.5z"></path>
                    </svg>
                    {{ adjustForm.errors.new_stock }}
                  </p>
                </div>
                
                <!-- Motivo -->
                <div>
                  <label class="block text-xs font-semibold text-gray-700 dark:text-zinc-300 mb-2">Motivo del Ajuste</label>
                  <select v-model="adjustForm.reason" 
                          :class="['w-full px-4 py-3 border-2 rounded-xl transition-all focus:outline-none text-sm',
                                   adjustForm.errors.reason 
                                     ? 'border-red-300 focus:border-red-500 bg-red-50 dark:bg-red-950/20' 
                                     : 'border-gray-200 dark:border-zinc-700 focus:border-blue-500 dark:focus:border-blue-400 bg-white dark:bg-zinc-800 text-gray-900 dark:text-white']"
                          @change="validateReason">
                    <option value="">Selecciona un motivo...</option>
                    <option value="Reposición de inventario">Reposición de inventario</option>
                    <option value="Conteo físico">Conteo físico</option>
                    <option value="Producto dañado">Producto dañado</option>
                    <option value="Producto vencido">Producto vencido</option>
                    <option value="Corrección de error">Corrección de error</option>
                    <option value="Merma">Merma</option>
                    <option value="Devolución proveedor">Devolución proveedor</option>
                    <option value="Otro">Otro motivo</option>
                  </select>
                  <input v-if="adjustForm.reason === 'Otro'" 
                         v-model="adjustForm.customReason"
                         type="text" 
                         class="w-full px-4 py-3 border-2 border-gray-200 dark:border-zinc-700 rounded-xl focus:outline-none focus:border-blue-500 dark:focus:border-blue-400 bg-white dark:bg-zinc-800 text-gray-900 dark:text-white text-sm mt-2"
                         placeholder="Especifica el motivo...">
                  <p v-if="adjustForm.errors.reason" class="text-red-600 dark:text-red-400 text-xs mt-1.5 flex items-center">
                    <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 15.5c-.77.833.192 2.5 1.732 2.5z"></path>
                    </svg>
                    {{ adjustForm.errors.reason }}
                  </p>
                </div>

              </div>

              <!-- Resumen del Cambio -->
              <div v-if="adjustForm.new_stock !== '' && !isNaN(adjustForm.new_stock)" 
                   class="col-span-12 relative overflow-hidden rounded-xl border-2 transition-all"
                   :class="stockDifferenceColor">
                <div class="p-4">
                  <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                      <div class="w-10 h-10 rounded-full flex items-center justify-center"
                           :class="stockDifferenceIconBg">
                        <svg class="w-5 h-5" :class="stockDifferenceIconColor" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                :d="stockDifference >= 0 ? 'M12 6v6m0 0v6m0-6h6m-6 0H6' : 'M18 12H6'"></path>
                        </svg>
                      </div>
                      <div>
                        <p class="font-bold text-lg" :class="stockDifferenceTextColor">
                          {{ stockDifference >= 0 ? '+' : '' }}{{ stockDifference }} unidades
                        </p>
                        <p class="text-sm opacity-75">
                          {{ selectedVariant ? selectedVariant.stock : selectedProductForAdjust.current_stock || 0 }} → {{ adjustForm.new_stock }}
                        </p>
                      </div>
                    </div>
                    <div class="text-right">
                      <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold"
                            :class="stockDifference >= 0 ? 'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-400' : 'bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-400'">
                        {{ stockDifference >= 0 ? '📦 Entrada' : '📤 Salida' }}
                      </span>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          
          <!-- Footer: Botones de Acción -->
          <div class="bg-gray-50 dark:bg-zinc-900/50 border-t border-gray-200 dark:border-zinc-800 px-6 py-4 -mx-6 -mb-6 flex justify-end gap-3">
            <button @click="showAdjustModal = false" 
                    class="px-5 py-2.5 bg-white dark:bg-zinc-900 hover:bg-slate-50 dark:hover:bg-zinc-800 text-slate-600 dark:text-zinc-200 text-sm font-bold rounded-xl border border-slate-200 dark:border-zinc-800 shadow-sm transition-all duration-200">
              Cancelar
            </button>
            <button @click="processStockAdjustment" 
                    :disabled="!isFormValid"
                    class="px-6 py-2.5 bg-slate-900 dark:bg-slate-700 hover:bg-black dark:hover:bg-slate-600 disabled:bg-gray-300 dark:disabled:bg-zinc-700 disabled:cursor-not-allowed text-white rounded-xl text-sm font-bold transition-all duration-300 shadow-lg shadow-slate-400/40 dark:shadow-slate-900/50">
              {{ adjustmentLoading ? 'Procesando...' : 'Confirmar Ajuste' }}
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal Historial de Producto -->
    <div v-if="showHistoryModal" 
         class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50"
         @click.self="showHistoryModal = false">
      <div class="bg-white rounded-2xl p-8 max-w-4xl w-full max-h-[90vh] overflow-hidden shadow-2xl transform transition-all">
        <!-- Header del Modal -->
        <div class="flex items-center justify-between mb-6">
          <div class="flex items-center space-x-3">
            <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
              <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
              </svg>
            </div>
            <div>
              <h3 class="text-xl font-bold text-gray-900">Historial de Movimientos</h3>
              <p class="text-sm text-gray-500" v-if="selectedProductForHistory">
                {{ selectedProductForHistory.name }} - {{ selectedProductForHistory.category }}
              </p>
            </div>
          </div>
          <button @click="showHistoryModal = false" 
                  class="text-gray-400 hover:text-gray-600 transition-colors">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
          </button>
        </div>

        <!-- Información del Producto -->
        <div v-if="selectedProductForHistory" class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl p-4 border border-blue-100 mb-6">
          <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="text-center">
              <p class="text-sm text-gray-600">Stock Actual</p>
              <p class="text-2xl font-bold text-blue-600">{{ selectedProductForHistory.current_stock || 0 }}</p>
            </div>
            <div class="text-center">
              <p class="text-sm text-gray-600">Stock Mínimo</p>
              <p class="text-lg font-semibold text-gray-700">{{ selectedProductForHistory.min_stock || 0 }}</p>
            </div>
            <div class="text-center">
              <p class="text-sm text-gray-600">Precio Venta</p>
              <p class="text-lg font-semibold text-green-600">${{ parseFloat(selectedProductForHistory.sale_price || 0).toLocaleString() }}</p>
            </div>
            <div class="text-center">
              <p class="text-sm text-gray-600">Estado</p>
              <span :class="[
                'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium',
                getStockStatusClass(selectedProductForHistory.current_stock, selectedProductForHistory.min_stock)
              ]">
                {{ getStockStatusLabel(selectedProductForHistory.current_stock, selectedProductForHistory.min_stock) }}
              </span>
            </div>
          </div>
        </div>

        <!-- Filtros del Historial -->
        <div class="flex flex-col sm:flex-row gap-4 mb-6">
          <div class="flex-1">
            <label class="block text-sm font-medium text-gray-700 mb-2">Período</label>
            <select v-model="historyDateFilter" 
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                    @change="filterMovements">
              <option value="all">Todos los períodos</option>
              <option value="today">Hoy</option>
              <option value="week">Última semana</option>
              <option value="month">Último mes</option>
            </select>
          </div>
          <div class="flex-1">
            <label class="block text-sm font-medium text-gray-700 mb-2">Tipo de Movimiento</label>
            <select v-model="historyTypeFilter" 
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                    @change="filterMovements">
              <option value="all">Todos los tipos</option>
              <option value="entrada">Entradas</option>
              <option value="salida">Salidas</option>
              <option value="adjustment">Ajustes</option>
            </select>
          </div>
          <div class="flex items-end">
            <button @click="loadProductHistory" 
                    :disabled="historyLoading"
                    class="px-4 py-2 bg-blue-600 hover:bg-blue-700 disabled:bg-gray-400 text-white rounded-lg font-medium flex items-center gap-2 transition-colors">
              <svg class="w-4 h-4" :class="{'animate-spin': historyLoading}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
              </svg>
              {{ historyLoading ? 'Cargando...' : 'Actualizar' }}
            </button>
          </div>
        </div>

        <!-- Lista de Movimientos -->
        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden max-h-96 overflow-y-auto">
          <div v-if="historyLoading" class="flex items-center justify-center py-12">
            <div class="text-center">
              <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 mx-auto mb-2"></div>
              <p class="text-gray-600">Cargando historial...</p>
            </div>
          </div>
          
          <div v-else-if="filteredMovements.length === 0" class="flex items-center justify-center py-12">
            <div class="text-center">
              <svg class="w-12 h-12 text-gray-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
              </svg>
              <p class="text-gray-600">No hay movimientos para mostrar</p>
              <p class="text-sm text-gray-400">Prueba cambiando los filtros</p>
            </div>
          </div>

          <div v-else>
            <div class="divide-y divide-gray-200">
              <div v-for="movement in filteredMovements" :key="movement.id" 
                   class="px-6 py-4 hover:bg-gray-50 transition-colors">
                <div class="flex items-center justify-between">
                  <div class="flex items-center space-x-4">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center"
                         :class="getMovementIconBg(movement.type)">
                      <svg class="w-5 h-5" :class="getMovementIconColor(movement.type)" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              :d="getMovementIconPath(movement.type)"></path>
                      </svg>
                    </div>
                    <div>
                      <div class="flex items-center space-x-2">
                        <span class="font-semibold text-gray-900">
                          {{ getQuantityDisplay(movement) }}
                        </span>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                              :class="getMovementTypeClass(movement.type)">
                          {{ getMovementTypeLabel(movement.type) }}
                        </span>
                      </div>
                      <p class="text-sm text-gray-600">{{ movement.reference || movement.notes || 'Sin motivo especificado' }}</p>
                      <p class="text-xs text-gray-400">{{ formatDateTime(movement.movement_date || movement.created_at) }}</p>
                    </div>
                  </div>
                  <div class="text-right">
                    <p class="text-sm font-medium text-gray-900">
                      Stock resultante: {{ movement.new_stock || 'N/A' }}
                    </p>
                    <p class="text-xs text-gray-500" v-if="movement.user">
                      Por: {{ movement.user.name || movement.user }}
                    </p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Footer del Modal -->
        <div class="flex items-center justify-between mt-6 pt-4 border-t border-gray-200">
          <div class="text-sm text-gray-600">
            Total: {{ filteredMovements.length }} movimientos
          </div>
          <button @click="showHistoryModal = false" 
                  class="px-6 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl font-semibold transition-colors">
            Cerrar
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted, nextTick, watch, onActivated } from 'vue'
import api from '../services/api.js'
import { productsService } from '../services/productsService.js'
import { categoriesService } from '../services/categoriesService.js'
import { inventoryService } from '../services/inventoryService.js'
import { warehouseService } from '../services/warehouseService.js' // NUEVO
import { inventoryStore } from '../store/inventory.js'
import { notificationStore } from '../store/notifications.js'
import { useToast } from '../composables/useToast.js'
import { appStore } from '../store/appStore.js' // NUEVO: para obtener el plan
import { useAutoRefresh } from '../composables/useRouteState.js'
import MovementsSection from './inventory/sections/MovementsSection.vue'
import TablePaginator from './TablePaginator.vue'

// Props del componente
const props = defineProps({
  moduleName: {
    type: String,
    default: 'inventory'
  }
})

// Eventos emitidos
const emit = defineEmits(['navigate', 'changeModule', 'change-module', 'openQuotationInPos', 'refresh'])

// Sistema de toasts
const { showSuccess, showError, showWarning } = useToast()

// 🏢 NUEVO: Estado para multi-sede
const warehouses = ref([])
const selectedWarehouse = ref(null)
const showWarehouseFilter = ref(false)

// Estado reactivo
const activeTab = ref('stock')
const searchTerm = ref('')
const categoryFilter = ref('')
const stockFilter = ref('')
const showMovementModal = ref(false)
const loading = ref(false)

// Estado para movimientos y notificaciones
const movementsData = ref(null)
const movementsLoading = ref(false)
const movementsFilters = ref({
  period: 'year',
  type: '',
  user: '',
  productSearch: ''
})

// Estado de paginación
const currentPage = ref(1)
const itemsPerPage = ref(10)

// Formulario nuevo movimiento
const newMovementForm = ref({
  product_id: '',
  type: 'entrada',
  quantity: '',
  reason: ''
})

// Variables para el modal mejorado
const modalCategoryFilter = ref('')
const modalSearchTerm = ref('')
const modalShowDropdown = ref(false)
const modalProductInput = ref(null)

// Variables para el modal de ajuste
const showAdjustModal = ref(false)
const adjustForm = ref({
  product_id: '',
  variant_id: null, // 👗 NUEVO: Para productos fashion
  new_stock: '',
  reason: '',
  customReason: '',
  adjustmentType: '',
  errors: {
    new_stock: '',
    reason: '',
    variant: '' // 👗 Error de variante
  }
})
const selectedProductForAdjust = ref(null)
const productVariants = ref([]) // 👗 NUEVO: Lista de variantes
const selectedVariant = ref(null) // 👗 NUEVO: Variante seleccionada
const adjustmentLoading = ref(false)

// Variables para el modal de historial
const showHistoryModal = ref(false)
const selectedProductForHistory = ref(null)
const productMovements = ref([])
const historyLoading = ref(false)
const historyDateFilter = ref('all') // all, today, week, month
const historyTypeFilter = ref('all') // all, entrada, salida, adjustment

// Datos reales desde API
const products = ref([])
const categories = ref([])
const recentMovements = ref([])

// Computed properties
const filteredProducts = computed(() => {
  let filtered = products.value

  if (searchTerm.value) {
    const term = searchTerm.value.toLowerCase()
    filtered = filtered.filter(product => 
      product.name.toLowerCase().includes(term) ||
      product.barcode.includes(term)
    )
  }

  if (categoryFilter.value) {
    filtered = filtered.filter(product => product.category === categoryFilter.value)
  }

  if (stockFilter.value) {
    filtered = filtered.filter(product => {
      if (stockFilter.value === 'low') return product.stock <= product.min_stock
      if (stockFilter.value === 'normal') return product.stock > product.min_stock && product.stock <= product.min_stock * 2
      if (stockFilter.value === 'high') return product.stock > product.min_stock * 2
      return true
    })
  }

  return filtered
})

// Computed para notificaciones
const unreadMovementsCount = computed(() => {
  return notificationStore.unreadMovementsCount.value
})

const unreadAlertsCount = computed(() => {
  return notificationStore.unreadAlertsCount.value
})

// Computed para total de productos
const totalProducts = computed(() => {
  return products.value.length
})

// Computed properties para paginación
const totalPages = computed(() => {
  const filtered = filteredProducts.value || []
  const perPage = itemsPerPage.value || 10
  return Math.ceil(filtered.length / perPage)
})
const paginatedProducts = computed(() => {
  const filtered = filteredProducts.value || []
  const perPage = itemsPerPage.value || 10
  const page = currentPage.value || 1
  const start = (page - 1) * perPage
  const end = start + perPage
  return filtered.slice(start, end)
})

// Reset page when filters change
const resetPagination = () => {
  currentPage.value = 1
}

const totalProductsInStock = computed(() => products.value.reduce((sum, p) => sum + (p.current_stock || 0), 0))
const lowStockProducts = computed(() => products.value.filter(p => (p.current_stock || 0) <= (p.min_stock || 10)).length)
const totalInventoryValue = computed(() => products.value.reduce((sum, p) => sum + ((p.current_stock || 0) * parseFloat(p.sale_price || 0)), 0))
const todayMovements = computed(() => recentMovements.value.length)
const stockAlerts = computed(() => products.value.filter(p => (p.current_stock || 0) <= (p.min_stock || 10)))

// Computed properties para el modal de ajuste mejorado
const stockDifference = computed(() => {
  if (!selectedProductForAdjust.value || adjustForm.value.new_stock === '' || isNaN(adjustForm.value.new_stock)) {
    return 0
  }
  
  // Si hay variante seleccionada, usar su stock; si no, usar el del producto
  const currentStock = selectedVariant.value 
    ? selectedVariant.value.stock 
    : (selectedProductForAdjust.value.current_stock || 0)
    
  return parseInt(adjustForm.value.new_stock) - currentStock
})

const stockDifferenceColor = computed(() => {
  const diff = stockDifference.value
  if (diff > 0) return 'border-green-200 dark:border-green-800 bg-green-50 dark:bg-green-900/30'
  if (diff < 0) return 'border-red-200 dark:border-red-800 bg-red-50 dark:bg-red-900/30'
  return 'border-gray-200 dark:border-zinc-700 bg-gray-50 dark:bg-zinc-800'
})

const stockDifferenceIconBg = computed(() => {
  const diff = stockDifference.value
  if (diff > 0) return 'bg-green-100 dark:bg-green-900'
  if (diff < 0) return 'bg-red-100 dark:bg-red-900'
  return 'bg-gray-100 dark:bg-zinc-800'
})

const stockDifferenceIconColor = computed(() => {
  const diff = stockDifference.value
  if (diff > 0) return 'text-green-600 dark:text-green-400'
  if (diff < 0) return 'text-red-600 dark:text-red-400'
  return 'text-gray-600 dark:text-zinc-400'
})

const stockDifferenceTextColor = computed(() => {
  const diff = stockDifference.value
  if (diff > 0) return 'text-green-800 dark:text-green-400'
  if (diff < 0) return 'text-red-800 dark:text-red-400'
  return 'text-gray-800 dark:text-zinc-300'
})

const isFormValid = computed(() => {
  return adjustForm.value.new_stock !== '' && 
         !isNaN(adjustForm.value.new_stock) && 
         adjustForm.value.reason !== '' &&
         !adjustForm.value.errors.new_stock &&
         !adjustForm.value.errors.reason &&
         (adjustForm.value.reason !== 'Otro' || adjustForm.value.customReason.trim() !== '')
})

// Computed properties para el modal de historial
const filteredMovements = computed(() => {
  let filtered = productMovements.value

  // Filtrar por fecha (esto ya se hace en la API, pero por si acaso)
  if (historyDateFilter.value !== 'all') {
    const now = new Date()
    const today = new Date(now.getFullYear(), now.getMonth(), now.getDate())
    
    filtered = filtered.filter(movement => {
      const movementDate = new Date(movement.movement_date || movement.created_at)
      
      switch (historyDateFilter.value) {
        case 'today':
          return movementDate >= today
        case 'week':
          const weekAgo = new Date(today.getTime() - 7 * 24 * 60 * 60 * 1000)
          return movementDate >= weekAgo
        case 'month':
          const monthAgo = new Date(today.getTime() - 30 * 24 * 60 * 60 * 1000)
          return movementDate >= monthAgo
        default:
          return true
      }
    })
  }

  // Filtrar por tipo (esto ya se hace en la API, pero por si acaso)
  if (historyTypeFilter.value !== 'all') {
    filtered = filtered.filter(movement => {
      const movementType = movement.type.toLowerCase()
      const filterType = historyTypeFilter.value.toLowerCase()
      
      // Mapear tipos en español e inglés
      if (filterType === 'entrada') {
        return movementType === 'entrada' || movementType === 'entry' || movementType === 'in'
      } else if (filterType === 'salida') {
        return movementType === 'salida' || movementType === 'exit' || movementType === 'out' || movementType === 'sale'
      } else if (filterType === 'adjustment') {
        return movementType === 'adjustment' || movementType === 'ajuste'
      }
      
      return movementType === filterType
    })
  }

  // Ordenar por fecha descendente
  return filtered.sort((a, b) => new Date(b.movement_date || b.created_at) - new Date(a.movement_date || a.created_at))
})

// Computed properties para el modal mejorado
const modalFilteredProducts = computed(() => {
  let filtered = products.value

  // Filtrar por categoría si está seleccionada
  if (modalCategoryFilter.value) {
    filtered = filtered.filter(product => product.category === modalCategoryFilter.value)
  }

  // Filtrar por término de búsqueda si existe
  if (modalSearchTerm.value && modalSearchTerm.value.length >= 1) {
    const term = modalSearchTerm.value.toLowerCase()
    filtered = filtered.filter(product => {
      return (
        product.name.toLowerCase().includes(term) ||
        (product.barcode && product.barcode.toLowerCase().includes(term)) ||
        (product.sku && product.sku.toLowerCase().includes(term)) ||
        product.id.toString().includes(term)
      )
    })
  }

  // Limitar a 10 resultados para mejor rendimiento
  return filtered.slice(0, 10)
})

const selectedProduct = computed(() => {
  return products.value.find(p => p.id.toString() === newMovementForm.value.product_id)
})

const modalCategoriesForSelect = computed(() => {
  const uniqueCategories = [...new Set(products.value.map(p => p.category).filter(Boolean))]
  return uniqueCategories.sort()
})

// Funciones de paginación
const nextPage = () => {
  if (currentPage.value < totalPages.value) {
    currentPage.value++
  }
}

const prevPage = () => {
  if (currentPage.value > 1) {
    currentPage.value--
  }
}

const goToPage = (page) => {
  if (page >= 1 && page <= totalPages.value) {
    currentPage.value = page
  }
}

const changeItemsPerPage = (newItemsPerPage) => {
  itemsPerPage.value = parseInt(newItemsPerPage) || 10
  currentPage.value = 1
}

// Watchers para resetear paginación cuando cambien los filtros
watch([searchTerm, categoryFilter, stockFilter], () => {
  resetPagination()
})

// Funciones para cargar datos reales
const loadProducts = async () => {
  try {
    loading.value = true

    // 🏢 Construir parámetros con filtro de warehouse si está activo
    const params = { 
      per_page: 1000,
      status: 'all', // Obtener todos los productos (activos e inactivos)
      _t: Date.now() // Cache busting
    }
    
    if (showWarehouseFilter.value && selectedWarehouse.value) {
      params.warehouse_id = selectedWarehouse.value
      console.log(`📦 Filtrando productos por warehouse ID: ${selectedWarehouse.value}`)
    }

    // 🖼️ USAR EL MISMO ENDPOINT QUE PRODUCTSVIEW para obtener imágenes
    const response = await productsService.getAll(params)
    console.log('📦 [InventoryView] Respuesta completa del endpoint:', response)

    // Los productos pueden venir en response.data.data (paginación) o directamente en response.data
    const productsList = response.data?.data || response.data || []
    console.log('📦 [InventoryView] Lista de productos extraída:', productsList.length)
    
    // Verificar el primer producto para ver estructura de imágenes
    if (productsList.length > 0) {
      console.log('📦 [InventoryView] Primer producto (estructura completa):', productsList[0])
      console.log('📦 [InventoryView] Campos de imagen del primer producto:', {
        image_url: productsList[0].image_url,
        image: productsList[0].image,
        images: productsList[0].images,
        img: productsList[0].img
      })
      console.log('💰 [DEBUG VENTAS] total_sold:', productsList[0].total_sold, 'total_revenue:', productsList[0].total_revenue)
    }

    products.value = productsList.map(product => ({
      ...product,
      type: product.type || 'simple', // 👗 IMPORTANTE: Incluir tipo de producto
      category: product.category ? product.category.name : 'Sin categoría',
      current_stock: product.current_stock || 0, // campo real de la BD
      stock: product.current_stock || 0, // alias para compatibilidad
      min_stock: product.min_stock || 10,
      barcode: product.barcode || `BAR${product.id}${Date.now().toString().slice(-4)}`,
      price: parseFloat(product.sale_price || product.price || 0),
      image_url: product.image_url || product.image || null,
      images: product.images || [], // 🖼️ Incluir array de imágenes para getProductImage()
      total_sold: parseInt(product.total_sold || 0), // 📊 Ventas totales (backend las calcula)
      total_revenue: parseFloat(product.total_revenue || 0) // 💰 Ingresos totales (backend los calcula)
    }))

    const warehouseInfo = showWarehouseFilter.value ? ` (Sede: ${selectedWarehouse.value})` : ' (Todas las sedes)'
    console.log(`Productos cargados${warehouseInfo}:`, products.value.length)
  } catch (error) {
    console.error('Error cargando productos:', error)
  } finally {
    loading.value = false
  }
}

const loadCategories = async () => {
  try {
    const response = await categoriesService.getAll()
    categories.value = response.data.filter(cat => cat.active)
  } catch (error) {
    console.error('Error cargando categorías:', error)
  }
}

// Función para cargar datos de movimientos con filtros y paginación
const loadMovementsData = async (filters = null) => {
  try {
    movementsLoading.value = true
    console.log('🔄 Cargando movimientos para Control de Inventario...')
    
    // Usar filtros pasados o los filtros actuales
    const currentFilters = filters || movementsFilters.value
    
    // Construir parámetros de consulta
    const params = new URLSearchParams({
      period: currentFilters.period || 'year',
      per_page: currentFilters.per_page || 50,
      page: currentFilters.page || 1,
      ...(currentFilters.type && { type: currentFilters.type }),
      ...(currentFilters.user && { user: currentFilters.user }),
      ...(currentFilters.product && { product: currentFilters.product })
    })
    
    // Llamada a la API de movimientos (usando endpoint de prueba con parámetros)
    const data = await api.get(`/inventory/test/movements?${params}`)
    console.log('✅ Movimientos cargados:', data)
    
    // El endpoint de prueba devuelve: { success: true, data: { movements: [...], summary: {...} } }
    if (data.success && data.data) {
      const movements = data.data.movements || []
      const summary = data.data.summary || {}
      
      // Los datos ya vienen en el formato correcto desde el endpoint de prueba
      movementsData.value = {
        summary: {
          total_movements: summary.total_movements || 0,
          total_entries: summary.total_entries || 0,
          total_exits: summary.total_exits || 0,
          total_entry_value: summary.total_entry_value || 0,
          total_exit_value: summary.total_exit_value || 0
        },
        movements: movements,
        pagination: data.data.pagination || {}
      }
      
      console.log('📊 Datos estructurados para MovementsSection:', movementsData.value)
    } else {
      throw new Error('Formato de respuesta inesperado')
    }
    
  } catch (error) {
    console.error('❌ Error cargando movimientos:', error)
    showError('Error al cargar movimientos: ' + error.message)
    
    // Datos de fallback si hay error
    movementsData.value = {
      summary: {
        total_movements: 0,
        total_entries: 0,
        total_exits: 0,
        total_entry_value: 0,
        total_exit_value: 0
      },
      movements: [],
      pagination: {}
    }
  } finally {
    movementsLoading.value = false
  }
}

// Manejar cambios de filtros en movimientos
const handleMovementsFilter = (filters) => {
  movementsFilters.value = { ...filters, page: 1 } // Reset page cuando cambien filtros
  loadMovementsData(movementsFilters.value)
}

// Manejar cambios de página en movimientos
const handleMovementsPageChange = (page) => {
  movementsFilters.value = { ...movementsFilters.value, page }
  loadMovementsData(movementsFilters.value)
}

// Métodos
const getStockStatusLabel = (stock, minStock) => {
  if (stock <= minStock) return 'Stock Bajo'
  if (stock <= minStock * 2) return 'Stock Normal'
  return 'Stock Alto'
}

const getStockStatusClass = (stock, minStock) => {
  if (stock <= minStock) return 'bg-rose-100 text-rose-700'
  if (stock <= minStock * 2) return 'bg-amber-100 text-amber-700'
  return 'bg-emerald-100 text-emerald-700'
}

const formatDate = (dateString) => {
  const date = new Date(dateString)
  return date.toLocaleDateString('es-ES')
}

// Funciones de utilidad para el modal de historial
const formatDateTime = (dateString) => {
  const date = new Date(dateString)
  return date.toLocaleDateString('es-ES') + ' ' + date.toLocaleTimeString('es-ES', { hour: '2-digit', minute: '2-digit' })
}

const getMovementIconBg = (type) => {
  switch (type) {
    case 'entrada':
    case 'entry':
    case 'in':
      return 'bg-green-100'
    case 'salida':
    case 'exit':
    case 'out':
    case 'sale':
      return 'bg-red-100'
    case 'adjustment':
    case 'ajuste':
      return 'bg-orange-100'
    default:
      return 'bg-gray-100'
  }
}

const getMovementIconColor = (type) => {
  switch (type) {
    case 'entrada':
    case 'entry':
    case 'in':
      return 'text-green-600'
    case 'salida':
    case 'exit':
    case 'out':
    case 'sale':
      return 'text-red-600'
    case 'adjustment':
    case 'ajuste':
      return 'text-orange-600'
    default:
      return 'text-gray-600'
  }
}

const getMovementIconPath = (type) => {
  switch (type) {
    case 'entrada':
    case 'entry':
    case 'in':
      return 'M12 6v6m0 0v6m0-6h6m-6 0H6'
    case 'salida':
    case 'exit':
    case 'out':
    case 'sale':
      return 'M18 12H6'
    case 'adjustment':
    case 'ajuste':
      return 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z'
    default:
      return 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'
  }
}

const getMovementTypeClass = (type) => {
  switch (type) {
    case 'entrada':
    case 'entry':
    case 'in':
      return 'bg-green-100 text-green-800'
    case 'salida':
    case 'exit':
    case 'out':
    case 'sale':
      return 'bg-red-100 text-red-800'
    case 'adjustment':
    case 'ajuste':
      return 'bg-orange-100 text-orange-800'
    default:
      return 'bg-gray-100 text-gray-800'
  }
}

const getMovementTypeLabel = (type) => {
  switch (type) {
    case 'entrada':
    case 'entry':
    case 'in':
      return 'Entrada'
    case 'salida':
    case 'exit':
    case 'out':
    case 'sale':
      return 'Salida'
    case 'adjustment':
    case 'ajuste':
      return 'Ajuste'
    default:
      return 'Movimiento'
  }
}

const getQuantityDisplay = (movement) => {
  // Calcular la diferencia real basada en previous_stock y new_stock
  const previousStock = movement.previous_stock || 0
  const newStock = movement.new_stock || 0
  const realDifference = newStock - previousStock
  
  // Si tenemos la diferencia real, usarla
  if (movement.previous_stock !== null && movement.new_stock !== null) {
    return realDifference > 0 ? `+${realDifference}` : `${realDifference}`
  }
  
  // Fallback: usar quantity del movimiento
  const quantity = movement.quantity || 0
  
  // Para tipos específicos, ajustar el signo
  const type = movement.type?.toLowerCase()
  if (type === 'entrada' || type === 'entry' || type === 'in') {
    return `+${Math.abs(quantity)}`
  } else if (type === 'salida' || type === 'exit' || type === 'out' || type === 'sale') {
    return `-${Math.abs(quantity)}`
  } else {
    // Para ajustes, mostrar tal como viene (puede ser + o -)
    return quantity > 0 ? `+${quantity}` : `${quantity}`
  }
}

// Funciones de validación para el modal mejorado
const validateNewStock = () => {
  const stock = adjustForm.value.new_stock
  adjustForm.value.errors.new_stock = ''
  
  if (stock === '') {
    adjustForm.value.errors.new_stock = 'El stock es requerido'
  } else if (isNaN(stock) || parseInt(stock) < 0) {
    adjustForm.value.errors.new_stock = 'El stock debe ser un número mayor o igual a 0'
  } else if (parseInt(stock) > 99999) {
    adjustForm.value.errors.new_stock = 'El stock no puede ser mayor a 99,999'
  }
}

const validateReason = () => {
  adjustForm.value.errors.reason = ''
  
  if (!adjustForm.value.reason) {
    adjustForm.value.errors.reason = 'Debe seleccionar un motivo'
  } else if (adjustForm.value.reason === 'Otro' && !adjustForm.value.customReason.trim()) {
    adjustForm.value.errors.reason = 'Debe especificar el motivo personalizado'
  }
}

const setQuickAdjustment = (type) => {
  adjustForm.value.adjustmentType = type
  
  // Solo pre-llenar el motivo, NO cambiar las cantidades
  if (type === 'restock') {
    adjustForm.value.reason = 'Reposición de inventario'
  } else if (type === 'correction') {
    adjustForm.value.reason = 'Conteo físico'
  }
  
  // Limpiar errores al seleccionar tipo
  adjustForm.value.errors.reason = ''
}

const adjustStock = async (product) => {
  // Abrir modal de ajuste en lugar de prompt
  selectedProductForAdjust.value = product
  productVariants.value = []
  selectedVariant.value = null
  
  adjustForm.value = {
    product_id: product.id,
    variant_id: null,
    new_stock: '', // Dejar vacío para que el usuario ingrese la cantidad que desee
    reason: '',
    customReason: '',
    adjustmentType: '',
    errors: {
      new_stock: '',
      reason: '',
      variant: ''
    }
  }
  
  // 👗 Si el producto es tipo "variable" (fashion), cargar variantes
  if (product.type === 'variable') {
    try {
      const response = await api.get(`/products/${product.id}`)
      if (response.data && response.data.variants) {
        productVariants.value = response.data.variants
        console.log('👗 Variantes cargadas:', productVariants.value)
      }
    } catch (error) {
      console.error('Error cargando variantes:', error)
      showWarning('Error al cargar las variantes del producto')
    }
  }
  
  showAdjustModal.value = true
}

// 👗 Función para seleccionar una variante
const selectVariant = (variant) => {
  selectedVariant.value = variant
  adjustForm.value.variant_id = variant.id
  adjustForm.value.new_stock = '' // Resetear el input cuando se cambia de variante
  adjustForm.value.errors.variant = ''
  console.log('👗 Variante seleccionada:', variant)
}

// Nueva función para procesar el ajuste con la API
const processStockAdjustment = async () => {
  try {
    // 👗 Validar que se haya seleccionado variante si es producto fashion
    if (productVariants.value.length > 0 && !selectedVariant.value) {
      adjustForm.value.errors.variant = 'Debes seleccionar una variante para ajustar'
      showWarning('Por favor selecciona una variante')
      return
    }
    
    // Validar formulario antes de enviar
    validateNewStock()
    validateReason()
    
    if (!isFormValid.value) {
      showWarning('Por favor corrige los errores en el formulario')
      return
    }
    
    adjustmentLoading.value = true
    
    const newStock = parseInt(adjustForm.value.new_stock)
    // 👗 Si es producto con variantes, usar el stock de la variante seleccionada
    const currentStock = selectedVariant.value 
      ? (selectedVariant.value.stock || 0)
      : (selectedProductForAdjust.value.current_stock || 0)
    const difference = newStock - currentStock
    
    if (difference === 0) {
      showWarning('El stock no ha cambiado')
      adjustmentLoading.value = false
      return
    }
    
    // Preparar el motivo final
    const finalReason = adjustForm.value.reason === 'Otro' 
      ? adjustForm.value.customReason.trim() 
      : adjustForm.value.reason
    
    console.log('Enviando ajuste a la API:', { 
      product: selectedProductForAdjust.value.name,
      variant_id: adjustForm.value.variant_id, // 👗 Incluir variant_id
      variant: selectedVariant.value ? (typeof selectedVariant.value.options_summary === 'string' ? JSON.parse(selectedVariant.value.options_summary) : selectedVariant.value.options_summary) : null,
      from: currentStock,
      to: newStock,
      difference,
      reason: finalReason,
      warehouse_id: selectedWarehouse.value
    })
    
    // 🏢 Incluir warehouse_id y variant_id si está disponible
    const response = await inventoryService.adjustStock(
      adjustForm.value.product_id, 
      newStock, 
      finalReason,
      selectedWarehouse.value,
      adjustForm.value.variant_id // 👗 NUEVO: pasar variant_id
    )
    
    if (response && response.success) {
      console.log('✅ Ajuste de stock registrado exitosamente en la BD')
      
      // ⏱️ Pequeño delay para asegurar que el backend termine de actualizar
      await new Promise(resolve => setTimeout(resolve, 100))
      
      // ✅ RECARGAR PRODUCTOS PARA ACTUALIZAR LA VISTA LOCAL
      await loadProducts()
      
      // 🔥 FORZAR ACTUALIZACIÓN EN EL STORE GLOBAL (para POS)
      console.log('🔄 Actualizando store global después del ajuste...')
      await appStore.loadProducts(selectedWarehouse.value, 'general', true)
      
      // 🔥 EMITIR EVENTO GLOBAL para que ProductsView recargue
      console.log('📢 Emitiendo evento global para recargar productos en todos los módulos...')
      window.dispatchEvent(new CustomEvent('products-updated', { 
        detail: { 
          source: 'inventory-adjustment',
          productId: adjustForm.value.product_id,
          newStock: newStock
        } 
      }))
      
      // 🔥 ACTUALIZAR NOTIFICACIONES AUTOMÁTICAMENTE
      console.log('📬 Actualizando notificaciones después del ajuste...')
      await notificationStore.loadNotifications()
      
      // Agregar movimiento a la vista local
      recentMovements.value.unshift({
        id: Date.now(),
        product_name: selectedProductForAdjust.value.name,
        type: difference > 0 ? 'entrada' : 'salida',
        quantity: Math.abs(difference),
        reason: finalReason,
        created_at: new Date().toISOString().split('T')[0]
      })
      
      // Cerrar modal y mostrar mensaje de éxito
      showAdjustModal.value = false
      
      const changeType = difference > 0 ? 'incrementado' : 'reducido'
      const changeAmount = Math.abs(difference)
      showSuccess(`Stock ${changeType} en ${changeAmount} unidades para ${selectedProductForAdjust.value.name}`)
      
    } else {
      const errorMessage = response?.message || 'Error desconocido al ajustar el stock'
      showError('Error: ' + errorMessage)
    }
    
  } catch (error) {
    console.error('Error ajustando stock:', error)
    const errorMessage = error?.response?.data?.message || error?.message || 'Error de conexión con el servidor'
    showError('Error al conectar con el servidor: ' + errorMessage)
  } finally {
    adjustmentLoading.value = false
  }
}

// Funciones para el modal de historial
const viewMovements = async (product) => {
  console.log('Ver movimientos de:', product.name)
  selectedProductForHistory.value = product
  historyDateFilter.value = 'all'
  historyTypeFilter.value = 'all'
  showHistoryModal.value = true
  await loadProductHistory()
}

const loadProductHistory = async () => {
  if (!selectedProductForHistory.value) return
  
  try {
    historyLoading.value = true
    
    // Preparar filtros para la API según el controlador de Laravel
    const filters = {
      product_id: selectedProductForHistory.value.id,
      per_page: 100 // Traer más registros para el historial
    }
    
    // Agregar filtro de fecha si está seleccionado
    if (historyDateFilter.value !== 'all') {
      const now = new Date()
      let startDate
      
      switch (historyDateFilter.value) {
        case 'today':
          startDate = new Date(now.getFullYear(), now.getMonth(), now.getDate())
          break
        case 'week':
          startDate = new Date(now.getTime() - 7 * 24 * 60 * 60 * 1000)
          break
        case 'month':
          startDate = new Date(now.getTime() - 30 * 24 * 60 * 60 * 1000)
          break
      }
      
      if (startDate) {
        filters.date_from = startDate.toISOString().split('T')[0]
      }
    }
    
    // Agregar filtro de tipo si está seleccionado
    if (historyTypeFilter.value !== 'all') {
      filters.type = historyTypeFilter.value
    }
    
    console.log('Cargando historial con filtros:', filters)
    
    // Llamar a la API real usando el inventoryStore
    const response = await inventoryStore.getMovements(filters)
    
    if (response && response.data) {
      // La API retorna datos paginados, extraemos los items
      const movements = response.data.data || response.data
      productMovements.value = movements
      console.log('Historial cargado exitosamente:', productMovements.value.length, 'movimientos')
      console.log('Datos recibidos:', response.data)
    } else {
      productMovements.value = []
      console.warn('Respuesta vacía de la API')
    }
    
  } catch (error) {
    console.error('Error cargando historial:', error)
    showError('Error al cargar el historial del producto: ' + (error.message || 'Error desconocido'))
    productMovements.value = []
  } finally {
    historyLoading.value = false
  }
}

const filterMovements = () => {
  // Los filtros se aplican automáticamente a través de la computed property filteredMovements
  console.log('Aplicando filtros:', historyDateFilter.value, historyTypeFilter.value)
}

const createMovement = async () => {
  try {
    loading.value = true
    
    const product = products.value.find(p => p.id == newMovementForm.value.product_id)
    if (!product) {
      showError('Producto no encontrado')
      return
    }
    
    const quantity = parseInt(newMovementForm.value.quantity)
    
    // Validar stock suficiente para salidas
    if (newMovementForm.value.type === 'salida') {
      const currentStock = product.current_stock || 0
      if (quantity > currentStock) {
        showWarning(`No hay suficiente stock. Stock actual: ${currentStock}`)
        return
      }
    }
    
    // Enviar movimiento a la API
    const movementData = {
      product_id: newMovementForm.value.product_id,
      type: newMovementForm.value.type,
      quantity: quantity,
      reason: newMovementForm.value.reason
    }
    
    console.log('Enviando movimiento a la API:', movementData)
    const response = await inventoryService.createMovement(movementData)
    
    console.log('Respuesta completa de la API:', response)
    
    if (response && response.success) {
      console.log('Movimiento registrado exitosamente en la BD')
      
      // Recargar productos para obtener datos actualizados
      await loadProducts()
      
      // 🔥 ACTUALIZAR NOTIFICACIONES AUTOMÁTICAMENTE
      console.log('Actualizando notificaciones después del movimiento...')
      await notificationStore.loadNotifications()
      
      // Agregar movimiento a la vista local
      recentMovements.value.unshift({
        id: Date.now(),
        product_name: product.name,
        type: newMovementForm.value.type,
        quantity: quantity,
        reason: newMovementForm.value.reason,
        created_at: new Date().toISOString().split('T')[0]
      })
      
      // Limpiar formulario y cerrar modal
      closeMovementModal()
      
      // Mostrar mensaje de éxito
      alert('✅ Movimiento registrado exitosamente')
    } else {
      const errorMessage = response?.message || 'Error desconocido al registrar el movimiento'
      alert('❌ Error: ' + errorMessage)
    }
    
  } catch (error) {
    console.error('Error registrando movimiento:', error)
    const errorMessage = error?.response?.data?.message || error?.message || 'Error de conexión con el servidor'
    alert('❌ Error al conectar con el servidor: ' + errorMessage)
  } finally {
    loading.value = false
  }
}

const generateReport = () => {
  console.log('Navegando a reportes de inventario...')
  emit('navigate', 'reports')
  emit('changeModule', 'reports')
}

// Funciones del modal mejorado
const selectProduct = (product) => {
  newMovementForm.value.product_id = product.id.toString()
  modalSearchTerm.value = `${product.name} (${product.barcode || 'Sin código'})`
  modalShowDropdown.value = false
}

const clearProductSelection = () => {
  newMovementForm.value.product_id = ''
  modalSearchTerm.value = ''
  modalShowDropdown.value = false
}

const onSearchInput = () => {
  newMovementForm.value.product_id = ''
  modalShowDropdown.value = modalSearchTerm.value.length >= 1
}

const resetModalFilters = () => {
  modalCategoryFilter.value = ''
  modalSearchTerm.value = ''
  newMovementForm.value.product_id = ''
  modalShowDropdown.value = false
}

const openMovementModal = () => {
  resetModalFilters()
  showMovementModal.value = true
  // Focus en el input después de que el modal se renderice
  nextTick(() => {
    if (modalProductInput.value) {
      modalProductInput.value.focus()
    }
  })
}

const closeMovementModal = () => {
  showMovementModal.value = false
  resetModalFilters()
  // Limpiar formulario
  newMovementForm.value = {
    product_id: '',
    type: 'entrada',
    quantity: '',
    reason: ''
  }
}

// 🏢 NUEVO: Cargar warehouses según plan del tenant
const loadWarehousesIfAvailable = async () => {
  try {
    const tenantPlan = appStore.tenantPlan
    
    // Solo para planes Premium y Enterprise
    if (!['premium', 'enterprise'].includes(tenantPlan)) {
      showWarehouseFilter.value = false
      return
    }
    
    // Cargar warehouses usando getAll()
    const response = await warehouseService.getAll()
    const warehousesData = response.data || response
    warehouses.value = warehousesData.warehouses || []
    
    // Mostrar filtro solo si hay más de 1 sede
    showWarehouseFilter.value = warehouses.value.length > 1
    
    if (showWarehouseFilter.value) {
      // Seleccionar warehouse por defecto
      const defaultWh = warehouses.value.find(w => w.is_default)
      selectedWarehouse.value = defaultWh?.id || warehouses.value[0]?.id
      
      console.log('🏢 Multi-sede habilitado:', {
        plan: tenantPlan,
        warehouses: warehouses.value.length,
        selected: selectedWarehouse.value
      })
    }
  } catch (error) {
    console.error('Error cargando warehouses:', error)
    showWarehouseFilter.value = false
  }
}

// Función para refrescar datos
const refreshInventoryData = async () => {
  try {
    loading.value = true
    console.log('Refrescando datos de inventario...')
    await loadProducts()
    await loadCategories()
    console.log('Datos de inventario actualizados')
  } catch (error) {
    console.error('Error refrescando datos:', error)
    alert('Error al actualizar los datos')
  } finally {
    loading.value = false
  }
}

// 🖼️ Función utilitaria para manejo inteligente de imágenes (copiada de ProductsView)
const getProductImage = (product) => {
  // Usar el origen actual (mismo puerto que el frontend/proxy)
  const baseUrl = window.location.origin
  
  // 1. Intentar con el array de imágenes (relación images)
  if (product?.images && Array.isArray(product.images) && product.images.length > 0) {
    const primaryImage = product.images.find(img => img.is_primary) || product.images[0]
    if (primaryImage?.image_url) {
      const imageUrl = primaryImage.image_url
      // Si la imagen es base64, devolverla directamente
      if (imageUrl.startsWith('data:image')) {
        return imageUrl
      }
      // Fix relative URLs for tenant backend - usar el mismo origen
      if (imageUrl.startsWith('/storage')) {
        return `${baseUrl}${imageUrl}`
      }
      // URL externa completa
      if (imageUrl.startsWith('http://') || imageUrl.startsWith('https://')) {
        return imageUrl
      }
      return imageUrl
    }
  }
  
  // 2. Intentar múltiples propiedades de imagen (fallback)
  const imageUrl = product?.image_url || product?.image || product?.img || product?.photo
  
  if (!imageUrl) {
    return null
  }
  
  // Si la imagen es base64 (data:image), devolverla directamente
  if (imageUrl.startsWith('data:image')) {
    return imageUrl
  }
  
  // Si hay imagen URL, procesarla
  if (imageUrl.length > 5) {
    // Fix relative URLs for tenant backend - usar el mismo origen
    if (imageUrl.startsWith('/storage')) {
      return `${baseUrl}${imageUrl}`
    }
    // URL externa completa
    if (imageUrl.startsWith('http://') || imageUrl.startsWith('https://')) {
      return imageUrl
    }
    return imageUrl
  }
  
  return null
}

// 🎨 Generar avatar dinámico SVG con iniciales del producto (copiado de ProductsView)
const generateDynamicAvatar = (name) => {
  // Obtener las primeras 2 letras del nombre
  const initials = (name || 'P')
    .split(' ')
    .map(word => word.charAt(0))
    .slice(0, 2)
    .join('')
    .toUpperCase() || 'P'
  
  // Generar un color único basado en el nombre
  let hash = 0
  for (let i = 0; i < (name || '').length; i++) {
    hash = name.charCodeAt(i) + ((hash << 5) - hash)
  }
  
  // Usar hue del hash para generar color HSL (sin rojo ni rosa)
  const hue = Math.abs(hash % 220) + 140 // Rango 140-360 (evita rojos y rosas)
  const saturation = 70 // Saturación moderada
  const lightness = 35 // Oscuridad adecuada para buen contraste
  
  // Crear SVG inline
  const svg = `
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 80 80" width="80" height="80">
      <rect width="80" height="80" rx="12" fill="hsl(${hue}, ${saturation}%, ${lightness}%)"/>
      <text x="40" y="46" font-size="28" font-weight="700" fill="white" text-anchor="middle" dominant-baseline="middle" font-family="Inter, system-ui, sans-serif">${initials}</text>
    </svg>
  `
  
  // Retornar como data URI
  return `data:image/svg+xml,${encodeURIComponent(svg)}`
}

// 🚨 Manejar errores de carga de imagen (copiado de ProductsView)
const handleImageError = (event, product) => {
  // Evitar bucle infinito
  if (event.target.dataset.errorHandled) return
  event.target.dataset.errorHandled = true
  
  // Usar el nombre del producto si está disponible en el contexto, o 'Producto' por defecto
  const name = product?.name || event.target.alt || 'Producto'
  event.target.src = generateDynamicAvatar(name)
}

// Inicialización
onMounted(async () => {
  try {
    loading.value = true
    
    // 🏢 NUEVO: Cargar warehouses si el plan lo permite
    await loadWarehousesIfAvailable()
    
    await loadProducts()
    await loadCategories()
    await loadMovementsData()
    
    // Inicializar notificaciones
    await notificationStore.initializeLastVisited()
    await notificationStore.loadNotifications()
    notificationStore.startPolling(15000)
  } catch (error) {
    console.error('Error en mounted:', error)
  } finally {
    loading.value = false
  }
})

// 🔄 AUTO-REFRESH al reactivar el componente
onActivated(async () => {
  console.log('🔄 [InventoryView] Component activated - Refreshing data...')
  // Recarga silenciosa de datos críticos
  try {
    await loadProducts()
    await loadMovementsData()
  } catch (error) {
    console.error('Error en auto-refresh de inventario:', error)
  }
})

// 🔥 LIMPIAR POLLING AL DESMONTAR COMPONENTE
onUnmounted(() => {
  notificationStore.stopPolling()
})

// Watcher para cargar datos cuando se cambia de tab
watch(activeTab, async (newTab) => {
  if (newTab === 'movements') {
    await loadMovementsData()
    await notificationStore.markMovementsAsViewed()
  } else if (newTab === 'alerts') {
    await notificationStore.markAlertsAsViewed()
  }
})
</script>

<style scoped>
/* Transiciones suaves */
* {
  transition: all 0.2s ease-in-out;
}
</style>