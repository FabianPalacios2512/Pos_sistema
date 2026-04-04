<template>
  <div class="min-h-screen font-sans bg-gradient-to-b from-gray-50 via-gray-100 to-gray-200 dark:bg-gradient-to-b dark:from-[#141417] dark:via-slate-900 dark:to-[#0a0a0c] transition-colors duration-300 px-8">
    <div class="p-4 lg:p-6 space-y-6 pb-8 animate-fade-in">
      
      <!-- Header -->
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4">
        <div>
          <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Control de Inventario</h1>
          <p class="text-sm text-gray-600 dark:text-zinc-400 mt-1">Gestiona stock, movimientos y alertas de productos</p>
        </div>
        
        <div class="flex items-center space-x-3">
          <!-- Botón Actualizar - Gemini style -->
          <button @click="refreshInventoryData" 
                  :disabled="loading"
                  class="px-5 py-2.5 bg-white dark:bg-zinc-900 hover:bg-slate-50 dark:hover:bg-zinc-800 text-slate-600 dark:text-zinc-200 text-sm font-bold rounded-xl border border-slate-200 dark:border-zinc-800 shadow-sm transition-all duration-200 flex items-center space-x-2"
                  :class="{ 'opacity-50 cursor-not-allowed': loading }">
            <svg class="w-4 h-4" :class="{'animate-spin': loading}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
              <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
            </svg>
            <span>{{ loading ? 'Actualizando' : 'Actualizar' }}</span>
          </button>
          
          <!-- Botón Principal - Negro/Slate como el resto del sistema -->
          <button @click="openMovementModal"
                  class="px-6 py-2.5 bg-slate-900 dark:bg-slate-700 hover:bg-black dark:hover:bg-slate-600 text-white text-sm font-bold rounded-xl shadow-lg shadow-slate-400/40 dark:shadow-slate-900/50 transition-all duration-300 flex items-center space-x-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
              <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
            </svg>
            <span>Nuevo Movimiento</span>
          </button>
        </div>
      </div>
      
      <!-- KPIs - Gemini Style -->
      <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
        <!-- Total Productos -->
        <div class="bg-white dark:bg-zinc-900/80 backdrop-blur-sm rounded-2xl px-4 py-4 border border-gray-300 dark:border-zinc-800/60 hover:border-gray-400 dark:hover:border-zinc-700/80 transition-all duration-200 hover:shadow-md dark:shadow-lg dark:shadow-black/30">
          <div class="flex items-center gap-3">
            <div class="w-11 h-11 bg-purple-50 dark:bg-zinc-800/50 rounded-xl border border-purple-100 dark:border-white/5 flex items-center justify-center flex-shrink-0">
              <svg class="w-5 h-5 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z"></path>
              </svg>
            </div>
            <div class="flex-1 min-w-0">
              <p class="text-xs font-medium text-gray-600 dark:text-zinc-400 uppercase tracking-wide">Total Productos</p>
              <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ totalProducts }}</p>
            </div>
          </div>
        </div>

        <!-- Stock Bajo -->
        <div class="bg-white dark:bg-zinc-900/80 backdrop-blur-sm rounded-2xl px-4 py-4 border border-gray-300 dark:border-zinc-800/60 hover:border-gray-400 dark:hover:border-zinc-700/80 transition-all duration-200 hover:shadow-md dark:shadow-lg dark:shadow-black/30">
          <div class="flex items-center gap-3">
            <div class="w-11 h-11 bg-amber-50 dark:bg-zinc-800/50 rounded-xl border border-amber-100 dark:border-white/5 flex items-center justify-center flex-shrink-0">
              <svg class="w-5 h-5 text-amber-500 dark:text-amber-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"></path>
              </svg>
            </div>
            <div class="flex-1 min-w-0">
              <p class="text-xs font-medium text-gray-600 dark:text-zinc-400 uppercase tracking-wide">Stock Bajo</p>
              <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ lowStockProducts }}</p>
            </div>
          </div>
        </div>

        <!-- Valor Inventario -->
        <div class="bg-white dark:bg-zinc-900/80 backdrop-blur-sm rounded-2xl px-4 py-4 border border-gray-300 dark:border-zinc-800/60 hover:border-gray-400 dark:hover:border-zinc-700/80 transition-all duration-200 hover:shadow-md dark:shadow-lg dark:shadow-black/30">
          <div class="flex items-center gap-3">
            <div class="w-11 h-11 bg-emerald-50 dark:bg-zinc-800/50 rounded-xl border border-emerald-100 dark:border-white/5 flex items-center justify-center flex-shrink-0">
              <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
              </svg>
            </div>
            <div class="flex-1 min-w-0">
              <p class="text-xs font-medium text-gray-600 dark:text-zinc-400 uppercase tracking-wide">Valor Total</p>
              <p class="text-2xl font-bold text-gray-900 dark:text-white">${{ totalInventoryValue.toLocaleString() }}</p>
            </div>
          </div>
        </div>

        <!-- Movimientos Hoy -->
        <div class="bg-white dark:bg-zinc-900/80 backdrop-blur-sm rounded-2xl px-4 py-4 border border-gray-300 dark:border-zinc-800/60 hover:border-gray-400 dark:hover:border-zinc-700/80 transition-all duration-200 hover:shadow-md dark:shadow-lg dark:shadow-black/30">
          <div class="flex items-center gap-3">
            <div class="w-11 h-11 bg-blue-50 dark:bg-zinc-800/50 rounded-xl border border-blue-100 dark:border-white/5 flex items-center justify-center flex-shrink-0">
              <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 21L3 16.5m0 0L7.5 12M3 16.5h13.5m0-13.5L21 7.5m0 0L16.5 12M21 7.5H7.5"></path>
              </svg>
            </div>
            <div class="flex-1 min-w-0">
              <p class="text-xs font-medium text-gray-600 dark:text-zinc-400 uppercase tracking-wide">Movimientos Hoy</p>
              <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ todayMovements }}</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Selector de Sede/Bodega - Gemini Style -->
      <div v-if="showWarehouseFilter" class="flex items-center justify-between py-3 px-4 bg-gray-50 dark:bg-zinc-900 rounded-2xl">
        <div class="flex items-center gap-3">
          <div class="w-9 h-9 bg-gray-200 dark:bg-zinc-700 rounded-full flex items-center justify-center">
            <svg class="w-4 h-4 text-gray-600 dark:text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
              <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21m-3.75 3.75h.008v.008h-.008v-.008zm0 3h.008v.008h-.008v-.008zm0 3h.008v.008h-.008v-.008z"></path>
            </svg>
          </div>
          <div class="flex items-center gap-2">
            <span class="text-sm font-medium text-gray-600 dark:text-zinc-400">Bodega:</span>
            <select 
              v-model="selectedWarehouse"
              @change="refreshInventoryData"
              class="px-3 py-1.5 bg-transparent border-0 text-sm font-medium text-gray-900 dark:text-white focus:ring-0 cursor-pointer hover:bg-gray-200 dark:hover:bg-zinc-700 rounded-full transition-colors"
            >
              <option v-for="warehouse in warehouses" :key="warehouse.id" :value="warehouse.id" class="bg-white dark:bg-zinc-800">
                {{ warehouse.name }}{{ warehouse.is_default ? ' (Principal)' : '' }}
              </option>
            </select>
          </div>
        </div>
        <span class="text-xs text-gray-600 dark:text-zinc-400 hidden sm:flex items-center gap-1">
          <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z"></path>
          </svg>
          Filtrando por esta sede
        </span>
      </div>
      
      <!-- Contenedor Principal - Gemini Style -->
      <!-- Contenedor Principal -->
      <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-gray-300 dark:border-zinc-800 shadow-xl dark:shadow-black/50 overflow-hidden">
        <!-- Tabs -->
        <div class="border-b border-gray-200 dark:border-zinc-800 bg-gray-50 dark:bg-zinc-900">
          <nav class="flex px-6" aria-label="Tabs">
            <button @click="activeTab = 'stock'" 
                    :class="[
                      'group inline-flex items-center gap-2 py-4 px-1 border-b-2 font-medium text-sm transition-colors mr-8',
                      activeTab === 'stock' 
                        ? 'border-blue-600 dark:border-blue-400 text-blue-600 dark:text-blue-400' 
                        : 'border-transparent text-gray-600 dark:text-zinc-400 hover:text-gray-900 dark:hover:text-white hover:border-gray-200 dark:hover:border-zinc-700'
                    ]">
              <svg class="w-4 h-4" :class="activeTab === 'stock' ? 'text-blue-600 dark:text-blue-400' : 'text-gray-600 dark:text-zinc-400 group-hover:text-gray-900 dark:group-hover:text-white'" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z"></path>
              </svg>
              Stock Actual
            </button>
            <button @click="activeTab = 'movements'" 
                    :class="[
                      'group inline-flex items-center gap-2 py-4 px-1 border-b-2 font-medium text-sm relative transition-colors mr-8',
                      activeTab === 'movements' 
                        ? 'border-blue-600 dark:border-blue-400 text-blue-600 dark:text-blue-400' 
                        : 'border-transparent text-gray-600 dark:text-zinc-400 hover:text-gray-900 dark:hover:text-white hover:border-gray-200 dark:hover:border-zinc-700'
                    ]">
              <svg class="w-4 h-4" :class="activeTab === 'movements' ? 'text-blue-600 dark:text-blue-400' : 'text-gray-600 dark:text-zinc-400 group-hover:text-gray-900 dark:group-hover:text-white'" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 21L3 16.5m0 0L7.5 12M3 16.5h13.5m0-13.5L21 7.5m0 0L16.5 12M21 7.5H7.5"></path>
              </svg>
              Movimientos
              <span v-if="unreadMovementsCount > 0" 
                    class="ml-1 inline-flex items-center justify-center w-5 h-5 text-[10px] font-medium text-white bg-rose-600 rounded-full">
                {{ unreadMovementsCount }}
              </span>
            </button>
            <button @click="activeTab = 'alerts'" 
                    :class="[
                      'group inline-flex items-center gap-2 py-4 px-1 border-b-2 font-medium text-sm relative transition-colors',
                      activeTab === 'alerts' 
                        ? 'border-blue-600 dark:border-blue-400 text-blue-600 dark:text-blue-400' 
                        : 'border-transparent text-gray-600 dark:text-zinc-400 hover:text-gray-900 dark:hover:text-white hover:border-gray-200 dark:hover:border-zinc-700'
                    ]">
              <svg class="w-4 h-4" :class="activeTab === 'alerts' ? 'text-blue-600 dark:text-blue-400' : 'text-gray-600 dark:text-zinc-400 group-hover:text-gray-900 dark:group-hover:text-white'" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0"></path>
              </svg>
              Alertas
              <span v-if="unreadAlertsCount > 0" 
                    class="ml-1 inline-flex items-center justify-center w-5 h-5 text-[10px] font-medium text-white bg-rose-600 rounded-full">
                {{ unreadAlertsCount }}
              </span>
            </button>
          </nav>
        </div>
        
        <!-- Contenido Stock Actual -->
        <div v-if="activeTab === 'stock'">
          <!-- Indicador de carga - Gemini style -->
          <div v-if="loading" class="flex items-center justify-center py-12">
            <svg class="animate-spin w-8 h-8 text-blue-600 dark:text-blue-400" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span class="ml-3 text-sm text-gray-600 dark:text-zinc-400">Cargando productos...</span>
          </div>
          
          <div v-else>
            <!-- Filtros - Gemini Style -->
            <div class="p-4 bg-gray-50 dark:bg-zinc-800">
              <div class="flex flex-wrap items-center gap-3">
                <div class="flex-1 min-w-48 relative">
                  <svg class="absolute left-3.5 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-600 dark:text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                  </svg>
                  <input v-model="searchTerm" 
                         type="text" 
                         placeholder="Buscar productos..." 
                         class="w-full pl-11 pr-4 py-3 text-sm rounded-xl bg-white dark:bg-zinc-700 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-zinc-500 focus:outline-none focus:ring-2 focus:ring-blue-400 transition-all duration-200">
                </div>
                
                <select v-model="categoryFilter" 
                        class="px-4 py-3 text-sm rounded-xl bg-white dark:bg-zinc-700 text-gray-900 dark:text-white font-medium focus:outline-none focus:ring-2 focus:ring-blue-400 transition-all duration-200 min-w-40 cursor-pointer">
                  <option value="">Todas las categorías</option>
                  <option v-for="category in categories" :key="category.id" :value="category.name">
                    {{ category.name }}
                  </option>
                </select>
                
                <select v-model="stockFilter" 
                        class="px-4 py-3 text-sm rounded-xl bg-white dark:bg-zinc-700 text-gray-900 dark:text-white font-medium focus:outline-none focus:ring-2 focus:ring-blue-400 transition-all duration-200 min-w-36 cursor-pointer">
                  <option value="">Todo el stock</option>
                  <option value="low">Stock bajo</option>
                  <option value="normal">Stock normal</option>
                  <option value="high">Stock alto</option>
                </select>
                
                <button @click="searchTerm = ''; categoryFilter = ''; stockFilter = ''" 
                        class="p-2.5 text-gray-600 dark:text-zinc-400 hover:text-rose-600 dark:hover:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-600/20 rounded-xl transition-all duration-200"
                        title="Limpiar filtros">
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" 
                          d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                  </svg>
                </button>
              </div>
            </div>
          
          <!-- Tabla de Stock - Gemini Style -->
          <div class="overflow-x-auto">
            <table class="min-w-full">
              <thead class="bg-gray-50 dark:bg-zinc-800">
                <tr>
                  <th class="px-6 py-3.5 text-left text-xs font-medium text-gray-600 dark:text-zinc-400 uppercase tracking-wider">Producto</th>
                  <th class="px-6 py-3.5 text-left text-xs font-medium text-gray-600 dark:text-zinc-400 uppercase tracking-wider">Categoría</th>
                  <th class="px-6 py-3.5 text-center text-xs font-medium text-gray-600 dark:text-zinc-400 uppercase tracking-wider">Stock</th>
                  <th class="px-6 py-3.5 text-center text-xs font-medium text-gray-600 dark:text-zinc-400 uppercase tracking-wider">Ventas</th>
                  <th class="px-6 py-3.5 text-right text-xs font-medium text-gray-600 dark:text-zinc-400 uppercase tracking-wider">Ingresos</th>
                  <th class="px-6 py-3.5 text-center text-xs font-medium text-gray-600 dark:text-zinc-400 uppercase tracking-wider">Estado</th>
                  <th class="px-6 py-3.5 text-center text-xs font-medium text-gray-600 dark:text-zinc-400 uppercase tracking-wider">Acciones</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-200 dark:divide-zinc-700 bg-white dark:bg-zinc-900">
                <tr v-for="product in paginatedProducts" :key="product.id" class="hover:bg-gray-50 dark:hover:bg-zinc-800 transition-colors duration-150 group">
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center">
                      <!-- Imagen del producto -->
                      <div class="w-12 h-12 rounded-xl mr-4 overflow-hidden flex-shrink-0"
                           :class="getProductImage(product) ? 'bg-white dark:bg-zinc-800' : 'bg-gray-50 dark:bg-zinc-800 flex items-center justify-center'">
                        <img v-if="getProductImage(product)"
                             :src="getProductImage(product)" 
                             :alt="product.name"
                             class="w-full h-full object-cover">
                        <svg v-else class="w-6 h-6 text-gray-600 dark:text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.75 7.5h16.5m-15.75 0l1.5-1.875A2.625 2.625 0 018.25 4.5h7.5a2.625 2.625 0 012.25 1.125l1.5 1.875"/>
                        </svg>
                      </div>
                      <div>
                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ product.name }}</p>
                        <p class="text-xs text-gray-600 dark:text-zinc-400">{{ product.barcode || 'Sin código' }}</p>
                      </div>
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-gray-50 dark:bg-zinc-700 text-gray-900 dark:text-white">
                      {{ product.category }}
                    </span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-center">
                    <div class="flex flex-col items-center">
                      <span class="text-sm font-bold text-gray-900 dark:text-white">{{ product.current_stock || 0 }}</span>
                      <span class="text-[10px] text-gray-600 dark:text-zinc-400">Min: {{ product.min_stock || 10 }}</span>
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-center">
                    <div class="flex flex-col items-center">
                      <span class="text-sm font-medium text-gray-900 dark:text-white">{{ product.total_sold || 0 }}</span>
                      <span class="text-[10px] text-gray-600 dark:text-zinc-400">unidades</span>
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-right">
                    <div class="flex flex-col items-end">
                      <span class="text-sm font-semibold text-emerald-600 dark:text-emerald-400">${{ parseFloat(product.total_revenue || 0).toLocaleString() }}</span>
                      <span class="text-[10px] text-gray-600 dark:text-zinc-400">Precio: ${{ parseFloat(product.sale_price || 0).toLocaleString() }}</span>
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-center">
                    <span :class="[
                      'inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-medium uppercase tracking-wide',
                      product.current_stock <= product.min_stock 
                        ? 'bg-rose-50 dark:bg-rose-900/20 text-rose-600 dark:text-rose-400' 
                        : 'bg-emerald-50 dark:bg-emerald-900/20 text-emerald-600 dark:text-emerald-400'
                    ]">
                      {{ product.current_stock <= product.min_stock ? 'Bajo Stock' : 'En Stock' }}
                    </span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-center">
                    <div class="flex items-center justify-center gap-1">
                      <button @click="adjustStock(product)" 
                              class="p-2 text-gray-600 dark:text-zinc-400 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-xl transition-all duration-200"
                              title="Ajustar Stock">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                          <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                      </button>
                      <button @click="viewMovements(product)" 
                              class="p-2 text-gray-600 dark:text-zinc-400 hover:text-purple-600 dark:hover:text-purple-400 hover:bg-purple-50 dark:hover:bg-purple-900/20 rounded-xl transition-all duration-200"
                              title="Ver Historial">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
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
        
        <!-- Contenido Alertas - Gemini Style -->
        <div v-if="activeTab === 'alerts'" class="p-6 animate-fade-in">
          <!-- Lista de Alertas -->
          <div class="space-y-3">
            <div v-if="stockAlerts.length === 0" class="text-center py-16">
              <div class="w-16 h-16 bg-emerald-50 dark:bg-emerald-900/20 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
              </div>
              <p class="text-lg font-semibold text-gray-900 dark:text-white mb-1">¡Todo en orden!</p>
              <p class="text-sm text-gray-600 dark:text-zinc-400">Todos los productos tienen stock suficiente</p>
            </div>

            <div v-for="alert in stockAlerts" :key="alert.id" 
                 class="bg-white dark:bg-zinc-800 rounded-2xl p-4 hover:bg-gray-50 dark:hover:bg-zinc-700 transition-all duration-200">
              <div class="flex items-center justify-between gap-4">
                <div class="flex items-center gap-4 flex-1 min-w-0">
                  <!-- Imagen o Ícono del producto -->
                  <div class="w-12 h-12 rounded-xl overflow-hidden flex-shrink-0"
                       :class="getProductImage(alert) ? 'bg-white dark:bg-zinc-800' : 'bg-gray-50 dark:bg-zinc-700 flex items-center justify-center'">
                    <img v-if="getProductImage(alert)"
                         :src="getProductImage(alert)" 
                         :alt="alert.name"
                         class="w-full h-full object-cover">
                    <svg v-else class="w-6 h-6 text-gray-600 dark:text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.75 7.5h16.5m-15.75 0l1.5-1.875A2.625 2.625 0 018.25 4.5h7.5a2.625 2.625 0 012.25 1.125l1.5 1.875"/>
                    </svg>
                  </div>
                  
                  <!-- Información del producto -->
                  <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2 mb-1">
                      <h4 class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ alert.name }}</h4>
                      <span v-if="alert.stock === 0" 
                            class="px-2 py-0.5 rounded-full text-[10px] font-medium uppercase bg-rose-50 dark:bg-rose-900/20 text-rose-600 dark:text-rose-400">
                        Agotado
                      </span>
                      <span v-else 
                            class="px-2 py-0.5 rounded-full text-[10px] font-medium uppercase bg-amber-50 dark:bg-amber-500/20 text-amber-500 dark:text-amber-300">
                        Bajo Stock
                      </span>
                    </div>
                    <div class="flex items-center gap-4 text-xs text-gray-600 dark:text-zinc-400">
                      <span>Stock: <strong class="text-gray-900 dark:text-white">{{ alert.stock }}</strong> / {{ alert.min_stock }}</span>
                      <span>Categoría: <strong class="text-gray-900 dark:text-white">{{ alert.category }}</strong></span>
                    </div>
                  </div>
                </div>
                
                <!-- Botón de acción - Gemini Style -->
                <button @click="adjustStock(alert)" 
                        class="px-4 py-2 bg-blue-600 hover:bg-blue-700 dark:bg-blue-400 dark:hover:bg-blue-300 text-white dark:text-gray-900 text-xs font-medium rounded-xl transition-all duration-200 flex items-center gap-1.5 flex-shrink-0">
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
    
    <!-- Modal Nuevo Movimiento - Gemini Style -->
    <div v-if="showMovementModal" 
         class="fixed inset-0 bg-black/40 backdrop-blur-sm flex items-center justify-center p-4 z-50"
         @click.self="closeMovementModal">
      <div class="bg-white dark:bg-zinc-900 rounded-2xl p-6 max-w-lg w-full max-h-[90vh] overflow-y-auto">
        <!-- Header del Modal -->
        <div class="flex items-center justify-between pb-4 border-b border-gray-200 dark:border-zinc-700 mb-6">
          <div class="flex items-center space-x-3">
            <div class="w-10 h-10 bg-blue-50 dark:bg-blue-900/20 rounded-full flex items-center justify-center">
              <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
              </svg>
            </div>
            <div>
              <h3 class="text-base font-semibold text-gray-900 dark:text-white">Nuevo Movimiento</h3>
              <p class="text-xs text-gray-600 dark:text-zinc-400">Registra entradas y salidas de inventario</p>
            </div>
          </div>
          <button @click="closeMovementModal" 
                  class="p-2 text-gray-600 dark:text-zinc-400 hover:bg-gray-50 dark:hover:bg-zinc-800 rounded-full transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
              <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
          </button>
        </div>
        
        <div class="space-y-6">
          <!-- Filtro por Categoría -->
          <div>
            <label class="block text-sm font-medium text-gray-900 dark:text-white mb-2">Filtrar por Categoría (Opcional)</label>
            <select v-model="modalCategoryFilter" 
                    class="w-full px-4 py-3 rounded-xl bg-gray-50 dark:bg-zinc-800 text-gray-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 transition-all">
              <option value="">Todas las categorías</option>
              <option v-for="category in modalCategoriesForSelect" :key="category" :value="category">
                {{ category }}
              </option>
            </select>
          </div>

          <!-- Búsqueda de Producto -->
          <div class="relative">
            <label class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
              Buscar Producto
              <span class="text-xs font-normal text-gray-600 dark:text-zinc-400">(por nombre, código de barras, SKU o ID)</span>
            </label>
            <input 
              ref="modalProductInput"
              v-model="modalSearchTerm" 
              @input="onSearchInput"
              @focus="modalShowDropdown = modalSearchTerm.length >= 1"
              type="text" 
              class="w-full px-4 py-3 rounded-xl bg-gray-50 dark:bg-zinc-800 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-zinc-500 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 transition-all"
              placeholder="Escribe para buscar producto..."
              autocomplete="off">
            
            <!-- Producto Seleccionado -->
            <div v-if="selectedProduct && !modalShowDropdown" 
                 class="mt-3 p-4 bg-emerald-50 dark:bg-emerald-900/20 rounded-2xl">
              <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                  <div v-if="selectedProduct.image_url" class="w-12 h-12 rounded-xl overflow-hidden flex-shrink-0">
                    <img :src="selectedProduct.image_url" 
                         :alt="selectedProduct.name"
                         class="w-full h-full object-cover"
                         @error="(e) => e.target.parentElement.innerHTML = `<div class='w-full h-full bg-emerald-100 flex items-center justify-center'><span class='text-sm font-medium text-emerald-600 dark:text-emerald-400'>${selectedProduct.name.charAt(0).toUpperCase()}</span></div>`">
                  </div>
                  <div v-else class="w-12 h-12 bg-emerald-50 dark:bg-emerald-900/30 rounded-xl flex items-center justify-center flex-shrink-0">
                    <span class="text-lg font-medium text-emerald-600 dark:text-emerald-400">{{ selectedProduct.name.charAt(0).toUpperCase() }}</span>
                  </div>
                  <div>
                    <p class="font-medium text-emerald-600 dark:text-emerald-400">{{ selectedProduct.name }}</p>
                    <p class="text-sm text-emerald-600/80 dark:text-emerald-400/80">
                      Stock actual: {{ selectedProduct.current_stock || 0 }} unidades
                    </p>
                    <p class="text-xs text-emerald-600/60 dark:text-emerald-400/60">{{ selectedProduct.barcode || 'Sin código' }}</p>
                  </div>
                </div>
                <button @click="clearProductSelection" 
                        class="p-2 text-emerald-600 dark:text-emerald-400 hover:bg-emerald-50 rounded-full transition-colors">
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path>
                  </svg>
                </button>
              </div>
            </div>

            <!-- Dropdown de Resultados -->
            <div v-if="modalShowDropdown && modalFilteredProducts.length > 0" 
                 class="absolute z-10 w-full mt-2 bg-white dark:bg-zinc-800 rounded-2xl shadow-lg max-h-60 overflow-y-auto">
              <div v-for="product in modalFilteredProducts" 
                   :key="product.id"
                   @click="selectProduct(product)"
                   class="p-4 hover:bg-gray-50 dark:hover:bg-zinc-700 cursor-pointer border-b border-gray-200 dark:border-zinc-700 last:border-b-0 first:rounded-t-2xl last:rounded-b-2xl transition-colors">
                <div class="flex items-center justify-between gap-3">
                  <div class="flex items-center gap-3 flex-1">
                    <div v-if="product.image_url" class="w-10 h-10 rounded-xl overflow-hidden flex-shrink-0">
                      <img :src="product.image_url" 
                           :alt="product.name"
                           class="w-full h-full object-cover"
                           @error="(e) => e.target.style.display='none'">
                    </div>
                    <div v-else class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0" :style="{backgroundColor: `hsl(${product.id * 137.5 % 360}, 70%, 92%)`}">
                      <span class="text-sm font-medium" :style="{color: `hsl(${product.id * 137.5 % 360}, 80%, 35%)`}">
                        {{ product.name.charAt(0).toUpperCase() }}
                      </span>
                    </div>
                    <div class="flex-1 min-w-0">
                      <p class="font-medium text-gray-900 dark:text-white">{{ product.name }}</p>
                      <p class="text-sm text-gray-600 dark:text-zinc-400">{{ product.category }}</p>
                      <p class="text-xs text-gray-600 dark:text-zinc-400">
                        {{ product.barcode || 'Sin código' }} | Stock: {{ product.current_stock || 0 }}
                      </p>
                    </div>
                  </div>
                  <div class="text-right">
                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium"
                          :class="getStockStatusClass(product.current_stock || 0, product.min_stock || 10)">
                      {{ getStockStatusLabel(product.current_stock || 0, product.min_stock || 10) }}
                    </span>
                  </div>
                </div>
              </div>
            </div>

            <!-- Mensaje cuando no hay resultados -->
            <div v-if="modalShowDropdown && modalSearchTerm.length >= 1 && modalFilteredProducts.length === 0" 
                 class="absolute z-10 w-full mt-2 bg-white dark:bg-zinc-800 rounded-2xl shadow-lg p-4 text-center text-gray-600 dark:text-zinc-400">
              No se encontraron productos con: "{{ modalSearchTerm }}"
            </div>
          </div>
          
          <!-- Tipo y Cantidad -->
          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-900 dark:text-white mb-2">Tipo de Movimiento</label>
              <select v-model="newMovementForm.type" 
                      class="w-full px-4 py-3 rounded-xl bg-gray-50 dark:bg-zinc-800 text-gray-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 transition-all">
                <option value="entrada">📦 Entrada</option>
                <option value="salida">📤 Salida</option>
              </select>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-900 dark:text-white mb-2">Cantidad</label>
              <input v-model="newMovementForm.quantity" 
                     type="number" 
                     min="1"
                     class="w-full px-4 py-3 rounded-xl bg-gray-50 dark:bg-zinc-800 text-gray-900 dark:text-white placeholder-gray-400 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 transition-all"
                     placeholder="0">
            </div>
          </div>
          
          <!-- Motivo -->
          <div>
            <label class="block text-sm font-medium text-gray-900 dark:text-white mb-2">Motivo del Movimiento</label>
            <input v-model="newMovementForm.reason" 
                   type="text" 
                   class="w-full px-4 py-3 rounded-xl bg-gray-50 dark:bg-zinc-800 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-zinc-500 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 transition-all"
                   placeholder="Ej: Compra, Venta, Ajuste de inventario, Devolución...">
          </div>

          <!-- Alerta Stock Bajo -->
          <div v-if="selectedProduct && newMovementForm.type === 'salida' && 
                     parseInt(newMovementForm.quantity || 0) > (selectedProduct.current_stock || 0)" 
               class="p-4 bg-rose-50 dark:bg-rose-900/20 rounded-2xl">
            <div class="flex items-center space-x-3">
              <div class="w-8 h-8 bg-rose-600/20 rounded-full flex items-center justify-center">
                <svg class="w-4 h-4 text-rose-600 dark:text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>
              </div>
              <div>
                <p class="text-sm font-medium text-rose-600 dark:text-rose-400">Stock insuficiente</p>
                <p class="text-xs text-rose-600/80 dark:text-rose-400/80">
                  Solo hay {{ selectedProduct.current_stock || 0 }} unidades disponibles.
                </p>
              </div>
            </div>
          </div>
        </div>
        
        <!-- Botones de Acción - Gemini Style -->
        <div class="flex justify-end gap-3 pt-6 mt-6 border-t border-gray-200 dark:border-zinc-700">
          <button @click="closeMovementModal" 
                  class="px-5 py-2.5 text-blue-600 dark:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/20 text-sm font-medium rounded-xl transition-all duration-200">
            Cancelar
          </button>
          <button @click="createMovement" 
                  :disabled="!selectedProduct || !newMovementForm.quantity || !newMovementForm.reason"
                  class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 disabled:bg-gray-200 disabled:text-gray-400 dark:bg-blue-400 dark:hover:bg-blue-300 dark:disabled:bg-zinc-700 dark:disabled:text-gray-600 text-white dark:text-gray-900 rounded-full text-sm font-medium transition-all duration-200 disabled:cursor-not-allowed">
            Registrar Movimiento
          </button>
        </div>
      </div>
    </div>

    <!-- Modal Ajustar Stock - Gemini Style -->
    <div v-if="showAdjustModal" 
         class="fixed inset-0 bg-black/40 backdrop-blur-sm flex items-center justify-center p-4 z-50 animate-fade-in"
         @click.self="showAdjustModal = false">
      <div class="bg-white dark:bg-zinc-900 rounded-2xl w-full max-w-5xl flex flex-col max-h-[90vh] animate-scale-in">
        
        <!-- Header - Gemini Style -->
        <div class="bg-white dark:bg-zinc-900 border-b border-gray-200 dark:border-zinc-700 px-8 py-5 flex-shrink-0 rounded-t-2xl">
          <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
              <div class="w-12 h-12 bg-blue-50 dark:bg-blue-900/20 rounded-full flex items-center justify-center">
                <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                  <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
              </div>
              <div>
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white">Ajustar Stock</h3>
                <p class="text-xs text-gray-600 dark:text-zinc-400 mt-0.5">Modifica la cantidad en inventario</p>
              </div>
            </div>
            <button @click="showAdjustModal = false" 
                    class="p-2 hover:bg-gray-50 dark:hover:bg-zinc-800 rounded-full transition-all">
              <svg class="w-5 h-5 text-gray-600 dark:text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
              </svg>
            </button>
          </div>
        </div>
        
        <!-- Contenido del Modal -->
        <div class="flex-1 overflow-y-auto bg-gray-50 dark:bg-zinc-950 p-6" v-if="selectedProductForAdjust">
          
          <!-- Información del Producto -->
          <div class="bg-white dark:bg-zinc-900 rounded-2xl p-5 mb-5">
            <div class="flex items-center space-x-4">
              <div class="w-12 h-12 bg-blue-50 dark:bg-blue-900/20 rounded-full flex items-center justify-center flex-shrink-0">
                <span class="text-base font-medium text-blue-600 dark:text-blue-400">{{ selectedProductForAdjust.name.charAt(0) }}</span>
              </div>
              <div class="flex-1">
                <h4 class="text-base font-medium text-gray-900 dark:text-white">{{ selectedProductForAdjust.name }}</h4>
                <div class="flex items-center gap-3 mt-1">
                  <span class="text-xs text-gray-600 dark:text-zinc-400">{{ selectedProductForAdjust.category }}</span>
                  <span v-if="productVariants.length > 0" class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-purple-50 dark:bg-purple-900/20 text-purple-600 dark:text-purple-400">
                    👗 MODA/VARIANTES
                  </span>
                  <div v-else class="flex items-center gap-3">
                    <span class="text-xs text-gray-600 dark:text-zinc-400">Stock:</span>
                    <span class="text-sm font-semibold text-blue-600 dark:text-blue-400">{{ selectedProductForAdjust.current_stock || 0 }} uds</span>
                    <span class="text-xs text-gray-600 dark:text-zinc-400">|</span>
                    <span class="text-xs text-gray-600 dark:text-zinc-400">Mínimo:</span>
                    <span class="text-sm font-medium text-gray-900 dark:text-white">{{ selectedProductForAdjust.min_stock || 10 }} uds</span>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Grid de Formulario -->
          <div class="grid grid-cols-12 gap-5">
            
            <!-- Columna Izquierda: Variantes (si existen) -->
            <div v-if="productVariants.length > 0" class="col-span-5 space-y-5">
              <div class="bg-white dark:bg-zinc-900 rounded-2xl p-5">
                <h4 class="text-sm font-medium text-gray-900 dark:text-white mb-3">Selecciona la Variante</h4>
                <div class="space-y-2 max-h-[320px] overflow-y-auto">
                  <button
                    v-for="variant in productVariants"
                    :key="variant.id"
                    @click="selectVariant(variant)"
                    type="button"
                    :class="[
                      'w-full p-3 rounded-xl transition-all text-left',
                      selectedVariant?.id === variant.id
                        ? 'bg-purple-50 dark:bg-purple-900/20 ring-2 ring-purple-600 dark:ring-purple-400'
                        : 'bg-gray-50 dark:bg-zinc-800 hover:bg-gray-200 dark:hover:bg-zinc-700'
                    ]"
                  >
                    <div class="flex items-start gap-2">
                      <svg v-if="selectedVariant?.id === variant.id" class="w-5 h-5 text-purple-600 dark:text-purple-400 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                      </svg>
                      <div class="flex-1 min-w-0">
                        <p class="font-medium text-gray-900 dark:text-white text-sm mb-1">
                          {{ (typeof variant.options_summary === 'string' ? JSON.parse(variant.options_summary) : variant.options_summary).map(opt => `${opt.name}: ${opt.value}`).join(' | ') }}
                        </p>
                        <div class="flex items-center gap-2 text-xs text-gray-600 dark:text-zinc-400">
                          <span>{{ variant.sku }}</span>
                          <span class="text-gray-200 dark:text-zinc-700">|</span>
                          <span class="font-medium text-blue-600 dark:text-blue-400">{{ variant.stock || 0 }} uds</span>
                          <span class="text-gray-200 dark:text-zinc-700">|</span>
                          <span>${{ Number(variant.price || 0).toLocaleString('es-CO') }}</span>
                        </div>
                      </div>
                    </div>
                  </button>
                </div>
                <p v-if="adjustForm.errors.variant" class="text-rose-600 dark:text-rose-400 text-xs mt-2 flex items-center">
                  <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 15.5c-.77.833.192 2.5 1.732 2.5z"></path>
                  </svg>
                  {{ adjustForm.errors.variant }}
                </p>
              </div>
            </div>

            <!-- Columna Derecha: Formulario de Ajuste -->
            <div :class="productVariants.length > 0 ? 'col-span-7' : 'col-span-12'">
              <div class="bg-white dark:bg-zinc-900 rounded-2xl p-5 space-y-4">
                
                <!-- Tipo de Ajuste -->
                <div>
                  <label class="block text-xs font-medium text-gray-900 dark:text-white mb-2">Tipo de Ajuste</label>
                  <div class="grid grid-cols-2 gap-3">
                    <button type="button" @click="setQuickAdjustment('restock')"
                            :class="['p-3 rounded-xl transition-all', 
                                     adjustForm.adjustmentType === 'restock' 
                                       ? 'bg-emerald-50 dark:bg-emerald-900/20 text-emerald-600 dark:text-emerald-400 ring-2 ring-[#1e8e3e] dark:ring-[#81c995]' 
                                       : 'bg-gray-50 dark:bg-zinc-800 hover:bg-gray-200 dark:hover:bg-zinc-700 text-gray-600 dark:text-zinc-400']">
                      <div class="flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        <span class="font-medium text-sm">Restock</span>
                      </div>
                    </button>
                    <button type="button" @click="setQuickAdjustment('correction')"
                            :class="['p-3 rounded-xl transition-all', 
                                     adjustForm.adjustmentType === 'correction' 
                                       ? 'bg-amber-50 dark:bg-amber-500/20 text-amber-500 dark:text-amber-300 ring-2 ring-amber-500 dark:ring-amber-300' 
                                       : 'bg-gray-50 dark:bg-zinc-800 hover:bg-gray-200 dark:hover:bg-zinc-700 text-gray-600 dark:text-zinc-400']">
                      <div class="flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        <span class="font-medium text-sm">Corrección</span>
                      </div>
                    </button>
                  </div>
                </div>

                <!-- Nuevo Stock -->
                <div>
                  <label class="block text-xs font-medium text-gray-900 dark:text-white mb-2">Nuevo Stock</label>
                  <div class="relative">
                    <input v-model="adjustForm.new_stock" 
                           type="number" 
                           min="0"
                           step="1"
                           :class="['w-full px-4 py-3 pr-12 rounded-full transition-all focus:outline-none focus:ring-2 focus:ring-blue-400 text-sm',
                                    adjustForm.errors.new_stock 
                                      ? 'bg-rose-50 dark:bg-rose-900/20 text-rose-600 dark:text-rose-400' 
                                      : 'bg-gray-50 dark:bg-zinc-800 text-gray-900 dark:text-white']"
                           placeholder="Ingresa la cantidad nueva"
                           @blur="validateNewStock">
                    <span class="absolute right-4 top-1/2 -translate-y-1/2 text-sm text-gray-600 dark:text-zinc-400 font-medium">uds</span>
                  </div>
                  <p v-if="adjustForm.errors.new_stock" class="text-rose-600 dark:text-rose-400 text-xs mt-1.5 flex items-center">
                    <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 15.5c-.77.833.192 2.5 1.732 2.5z"></path>
                    </svg>
                    {{ adjustForm.errors.new_stock }}
                  </p>
                </div>
                
                <!-- Motivo -->
                <div>
                  <label class="block text-xs font-medium text-gray-900 dark:text-white mb-2">Motivo del Ajuste</label>
                  <select v-model="adjustForm.reason" 
                          :class="['w-full px-4 py-3 rounded-full transition-all focus:outline-none focus:ring-2 focus:ring-blue-400 text-sm cursor-pointer',
                                   adjustForm.errors.reason 
                                     ? 'bg-rose-50 dark:bg-rose-900/20 text-rose-600 dark:text-rose-400' 
                                     : 'bg-gray-50 dark:bg-zinc-800 text-gray-900 dark:text-white']"
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
                         class="w-full px-4 py-3 rounded-full focus:outline-none focus:ring-2 focus:ring-blue-400 bg-gray-50 dark:bg-zinc-800 text-gray-900 dark:text-white text-sm mt-2"
                         placeholder="Especifica el motivo...">
                  <p v-if="adjustForm.errors.reason" class="text-rose-600 dark:text-rose-400 text-xs mt-1.5 flex items-center">
                    <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 15.5c-.77.833.192 2.5 1.732 2.5z"></path>
                    </svg>
                    {{ adjustForm.errors.reason }}
                  </p>
                </div>

              </div>

              <!-- Resumen del Cambio -->
              <div v-if="adjustForm.new_stock !== '' && !isNaN(adjustForm.new_stock)" 
                   class="col-span-12 relative overflow-hidden rounded-2xl transition-all"
                   :class="stockDifferenceColor">
                <div class="p-4">
                  <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                      <div class="w-10 h-10 rounded-full flex items-center justify-center"
                           :class="stockDifferenceIconBg">
                        <svg class="w-5 h-5" :class="stockDifferenceIconColor" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                          <path stroke-linecap="round" stroke-linejoin="round" 
                                :d="stockDifference >= 0 ? 'M12 6v6m0 0v6m0-6h6m-6 0H6' : 'M18 12H6'"></path>
                        </svg>
                      </div>
                      <div>
                        <p class="font-semibold text-lg" :class="stockDifferenceTextColor">
                          {{ stockDifference >= 0 ? '+' : '' }}{{ stockDifference }} unidades
                        </p>
                        <p class="text-sm opacity-75">
                          {{ selectedVariant ? selectedVariant.stock : selectedProductForAdjust.current_stock || 0 }} → {{ adjustForm.new_stock }}
                        </p>
                      </div>
                    </div>
                    <div class="text-right">
                      <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium"
                            :class="stockDifference >= 0 ? 'bg-emerald-50 dark:bg-emerald-900/20 text-emerald-600 dark:text-emerald-400' : 'bg-rose-50 dark:bg-rose-900/20 text-rose-600 dark:text-rose-400'">
                        {{ stockDifference >= 0 ? '📦 Entrada' : '📤 Salida' }}
                      </span>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          
          <!-- Footer: Botones de Acción - Gemini Style -->
          <div class="flex justify-end gap-3 pt-6 mt-6 border-t border-gray-200 dark:border-zinc-700">
            <button @click="showAdjustModal = false" 
                    class="px-5 py-2.5 text-blue-600 dark:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/20 text-sm font-medium rounded-xl transition-all duration-200">
              Cancelar
            </button>
            <button @click="processStockAdjustment" 
                    :disabled="!isFormValid"
                    class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 disabled:bg-gray-200 disabled:text-gray-400 dark:bg-blue-400 dark:hover:bg-blue-300 dark:disabled:bg-zinc-700 dark:disabled:text-gray-600 text-white dark:text-gray-900 rounded-full text-sm font-medium transition-all duration-200 disabled:cursor-not-allowed">
              {{ adjustmentLoading ? 'Procesando...' : 'Confirmar Ajuste' }}
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal Historial de Producto - Gemini Style -->
    <div v-if="showHistoryModal" 
         class="fixed inset-0 bg-black/40 backdrop-blur-sm flex items-center justify-center p-4 z-50"
         @click.self="showHistoryModal = false">
      <div class="bg-white dark:bg-zinc-900 rounded-2xl p-8 max-w-4xl w-full max-h-[90vh] overflow-hidden transform transition-all">
        <!-- Header del Modal -->
        <div class="flex items-center justify-between mb-6">
          <div class="flex items-center space-x-3">
            <div class="w-10 h-10 bg-blue-50 dark:bg-blue-900/20 rounded-full flex items-center justify-center">
              <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
              </svg>
            </div>
            <div>
              <h3 class="text-xl font-semibold text-gray-900 dark:text-white">Historial de Movimientos</h3>
              <p class="text-sm text-gray-600 dark:text-zinc-400" v-if="selectedProductForHistory">
                {{ selectedProductForHistory.name }} - {{ selectedProductForHistory.category }}
              </p>
            </div>
          </div>
          <button @click="showHistoryModal = false" 
                  class="p-2 text-gray-600 dark:text-zinc-400 hover:bg-gray-50 dark:hover:bg-zinc-800 rounded-full transition-colors">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
              <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
          </button>
        </div>

        <!-- Información del Producto -->
        <div v-if="selectedProductForHistory" class="bg-gray-100 dark:bg-zinc-800 rounded-2xl p-4 mb-6">
          <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="text-center">
              <p class="text-sm text-gray-600 dark:text-zinc-400">Stock Actual</p>
              <p class="text-2xl font-semibold text-blue-600 dark:text-blue-400">{{ selectedProductForHistory.current_stock || 0 }}</p>
            </div>
            <div class="text-center">
              <p class="text-sm text-gray-600 dark:text-zinc-400">Stock Mínimo</p>
              <p class="text-lg font-medium text-gray-900 dark:text-white">{{ selectedProductForHistory.min_stock || 0 }}</p>
            </div>
            <div class="text-center">
              <p class="text-sm text-gray-600 dark:text-zinc-400">Precio Venta</p>
              <p class="text-lg font-medium text-emerald-600 dark:text-emerald-400">${{ parseFloat(selectedProductForHistory.sale_price || 0).toLocaleString() }}</p>
            </div>
            <div class="text-center">
              <p class="text-sm text-gray-600 dark:text-zinc-400">Estado</p>
              <span :class="[
                'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium',
                getStockStatusClass(selectedProductForHistory.current_stock, selectedProductForHistory.min_stock)
              ]">
                {{ getStockStatusLabel(selectedProductForHistory.current_stock, selectedProductForHistory.min_stock) }}
              </span>
            </div>
          </div>
        </div>

        <!-- Filtros del Historial - Gemini Style -->
        <div class="flex flex-col sm:flex-row gap-4 mb-6">
          <div class="flex-1">
            <label class="block text-sm font-medium text-gray-900 dark:text-white mb-2">Período</label>
            <select v-model="historyDateFilter" 
                    class="w-full px-4 py-3 rounded-xl bg-gray-50 dark:bg-zinc-800 text-gray-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 cursor-pointer"
                    @change="filterMovements">
              <option value="all">Todos los períodos</option>
              <option value="today">Hoy</option>
              <option value="week">Última semana</option>
              <option value="month">Último mes</option>
            </select>
          </div>
          <div class="flex-1">
            <label class="block text-sm font-medium text-gray-900 dark:text-white mb-2">Tipo de Movimiento</label>
            <select v-model="historyTypeFilter" 
                    class="w-full px-4 py-3 rounded-xl bg-gray-50 dark:bg-zinc-800 text-gray-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 cursor-pointer"
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
                    class="px-5 py-3 bg-blue-600 hover:bg-blue-700 disabled:bg-gray-200 disabled:text-gray-400 dark:bg-blue-400 dark:hover:bg-blue-300 dark:disabled:bg-zinc-700 text-white dark:text-gray-900 rounded-full font-medium flex items-center gap-2 transition-colors text-sm">
              <svg class="w-4 h-4" :class="{'animate-spin': historyLoading}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
              </svg>
              {{ historyLoading ? 'Cargando...' : 'Actualizar' }}
            </button>
          </div>
        </div>

        <!-- Lista de Movimientos - Gemini Style -->
        <div class="bg-gray-50 dark:bg-zinc-800 rounded-2xl overflow-hidden max-h-96 overflow-y-auto">
          <div v-if="historyLoading" class="flex items-center justify-center py-12">
            <div class="text-center">
              <svg class="animate-spin w-8 h-8 text-blue-600 dark:text-blue-400 mx-auto mb-2" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              <p class="text-gray-600 dark:text-zinc-400">Cargando historial...</p>
            </div>
          </div>
          
          <div v-else-if="filteredMovements.length === 0" class="flex items-center justify-center py-12">
            <div class="text-center">
              <svg class="w-12 h-12 text-gray-600 dark:text-zinc-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
              </svg>
              <p class="text-gray-900 dark:text-white">No hay movimientos para mostrar</p>
              <p class="text-sm text-gray-600 dark:text-zinc-400">Prueba cambiando los filtros</p>
            </div>
          </div>

          <div v-else>
            <div class="divide-y divide-gray-200 dark:divide-zinc-700">
              <div v-for="movement in filteredMovements" :key="movement.id" 
                   class="px-6 py-4 hover:bg-white dark:hover:bg-zinc-900 transition-colors">
                <div class="flex items-center justify-between">
                  <div class="flex items-center space-x-4">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center"
                         :class="getMovementIconBg(movement.type)">
                      <svg class="w-5 h-5" :class="getMovementIconColor(movement.type)" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" 
                              :d="getMovementIconPath(movement.type)"></path>
                      </svg>
                    </div>
                    <div>
                      <div class="flex items-center space-x-2">
                        <span class="font-medium text-gray-900 dark:text-white">
                          {{ getQuantityDisplay(movement) }}
                        </span>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                              :class="getMovementTypeClass(movement.type)">
                          {{ getMovementTypeLabel(movement.type) }}
                        </span>
                      </div>
                      <p class="text-sm text-gray-600 dark:text-zinc-400">{{ movement.reference || movement.notes || 'Sin motivo especificado' }}</p>
                      <p class="text-xs text-gray-600 dark:text-zinc-400">{{ formatDateTime(movement.movement_date || movement.created_at) }}</p>
                    </div>
                  </div>
                  <div class="text-right">
                    <p class="text-sm font-medium text-gray-900 dark:text-white">
                      Stock resultante: {{ movement.new_stock || 'N/A' }}
                    </p>
                    <p class="text-xs text-gray-600 dark:text-zinc-400" v-if="movement.user">
                      Por: {{ movement.user.name || movement.user }}
                    </p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Footer del Modal - Gemini Style -->
        <div class="flex items-center justify-between mt-6 pt-4 border-t border-gray-200 dark:border-zinc-700">
          <div class="text-sm text-gray-600 dark:text-zinc-400">
            Total: {{ filteredMovements.length }} movimientos
          </div>
          <button @click="showHistoryModal = false" 
                  class="px-5 py-2.5 text-blue-600 dark:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-full font-medium transition-colors text-sm">
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
import { useUIContextStore } from '../store/uiContextStore.js' // 🧠 IA
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
    
    // 🚀 OPTIMIZACIÓN: Cargar datos en paralelo en lugar de secuencial
    // Esto reduce el tiempo de carga de ~4s a ~1-2s
    await Promise.all([
      loadWarehousesIfAvailable(),
      loadProducts(),
      loadCategories(),
      loadMovementsData()
    ])
    
    // Inicializar notificaciones (puede ser secuencial - no crítico para UI)
    await notificationStore.initializeLastVisited()
    await notificationStore.loadNotifications()
    notificationStore.startPolling(15000)
    
    // 🧠 Inicializar contexto para IA
    setTimeout(() => {
      updateScreenContextForAI()
    }, 500)
  } catch (error) {
    console.error('Error en mounted:', error)
  } finally {
    loading.value = false
  }
})

// 🔄 AUTO-REFRESH al reactivar el componente
onActivated(async () => {
  console.log('🔄 [InventoryView] Component activated - Refreshing data...')
  // 🚀 Recarga en paralelo de datos críticos
  try {
    await Promise.all([
      loadProducts(),
      loadMovementsData()
    ])
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
  // Actualizar contexto para IA cuando cambia el tab
  updateScreenContextForAI()
})

// 🧠 CONCIENCIA DE PANTALLA PARA IA
const updateScreenContextForAI = () => {
  const uiContext = useUIContextStore()
  
  // Formatear moneda para la IA
  const formatCurrencyForAI = (value) => {
    return new Intl.NumberFormat('es-CO').format(value)
  }
  
  // Productos con stock bajo (alerta)
  const productosStockBajo = stockAlerts.value.slice(0, 10).map(p => ({
    nombre: p.name,
    stockActual: p.current_stock || 0,
    stockMinimo: p.min_stock || 10,
    categoria: p.category || 'Sin categoría'
  }))
  
  // Productos visibles en la tabla actual
  const productosVisibles = paginatedProducts.value.slice(0, 10).map(p => ({
    id: p.id,
    nombre: p.name,
    categoria: p.category,
    stock: p.current_stock || 0,
    ventas: p.total_sold || 0,
    ingresos: formatCurrencyForAI(p.total_revenue || 0),
    estado: (p.current_stock || 0) <= (p.min_stock || 10) ? 'bajo_stock' : 'en_stock'
  }))
  
  // Obtener datos de movimientos reales
  const movimientosResumen = movementsData.value?.summary || {}
  const totalMovimientos = movimientosResumen.total_movements || 0
  const entradasHoy = movimientosResumen.total_entries || 0
  const salidasHoy = movimientosResumen.total_exits || 0
  const valorEntradas = movimientosResumen.total_entry_value || 0
  const valorSalidas = movimientosResumen.total_exit_value || 0
  
  // Datos del contexto
  const contextData = {
    pestañaActiva: activeTab.value === 'stock' ? 'Stock Actual' : 
                   activeTab.value === 'movements' ? 'Movimientos' : 'Alertas',
    kpis: {
      totalProductos: totalProducts.value,
      stockBajo: lowStockProducts.value,
      valorInventario: formatCurrencyForAI(totalInventoryValue.value),
      movimientosHoy: totalMovimientos,
      entradasHoy: entradasHoy,
      salidasHoy: salidasHoy,
      valorEntradas: formatCurrencyForAI(valorEntradas),
      valorSalidas: formatCurrencyForAI(valorSalidas)
    },
    filtrosActivos: {
      busqueda: searchTerm.value || null,
      categoria: categoryFilter.value || null,
      stock: stockFilter.value || null
    },
    bodegaSeleccionada: selectedWarehouse.value ? 
      warehouses.value.find(w => w.id === selectedWarehouse.value)?.name : 'Todas las sedes',
    alertasStock: productosStockBajo,
    productosVisibles: productosVisibles,
    cantidadFiltrada: filteredProducts.value.length,
    instrucciones: {
      pestañas: 'Puedo cambiar entre: Stock Actual, Movimientos, y Alertas',
      buscar: 'Puedo buscar productos por nombre',
      filtrar: 'Puedo filtrar por categoría o por nivel de stock (bajo, normal, alto)',
      alertas: `Hay ${lowStockProducts.value} productos con stock bajo`,
      ajustar: 'Puedo ajustar el stock de un producto directamente aquí'
    }
  }
  
  // Registrar acciones disponibles
  uiContext.registerAction('cambiarTabInventario', (params) => {
    const tab = params?.tab || 'stock'
    const tabMap = { 'stock': 'stock', 'movimientos': 'movements', 'alertas': 'alerts' }
    activeTab.value = tabMap[tab] || tab
    return { 
      success: true, 
      message: `Cambiando a ${tab}`
    }
  })
  
  uiContext.registerAction('buscarInventario', (params) => {
    const texto = params?.texto || ''
    searchTerm.value = texto
    return { 
      success: true, 
      message: `Buscando "${texto}"...`,
      resultados: filteredProducts.value.length
    }
  })
  
  uiContext.registerAction('filtrarInventarioPorStock', (params) => {
    const filtro = params?.filtro || ''
    const filtroMap = { 'bajo': 'low', 'normal': 'normal', 'alto': 'high', 'todos': '' }
    stockFilter.value = filtroMap[filtro] || filtro
    return { 
      success: true, 
      message: filtro ? `Filtrando productos con stock ${filtro}` : 'Mostrando todos los productos',
      resultados: filteredProducts.value.length
    }
  })
  
  uiContext.registerAction('filtrarInventarioPorCategoria', (params) => {
    const categoria = params?.categoria || ''
    categoryFilter.value = categoria
    return { 
      success: true, 
      message: categoria ? `Filtrando por categoría: ${categoria}` : 'Mostrando todas las categorías',
      resultados: filteredProducts.value.length
    }
  })
  
  uiContext.registerAction('limpiarFiltrosInventario', () => {
    searchTerm.value = ''
    categoryFilter.value = ''
    stockFilter.value = ''
    return { success: true, message: 'Filtros limpiados' }
  })
  
  uiContext.registerAction('verAlertasInventario', () => {
    activeTab.value = 'alerts'
    return { 
      success: true, 
      message: `Mostrando ${lowStockProducts.value} productos con stock bajo`
    }
  })
  
  uiContext.registerAction('nuevoMovimiento', () => {
    openMovementModal()
    return { success: true, message: 'Modal de nuevo movimiento abierto' }
  })
  
  // 🧠 ACCIÓN: Editar campo de producto (principalmente stock) desde inventario
  uiContext.registerAction('editarCampoProducto', async (params) => {
    const { nombreProducto, campo, nuevoValor } = params
    
    // Solo soportamos edición de stock en Inventario
    const campoLower = campo?.toLowerCase() || ''
    if (!campoLower.includes('stock') && !campoLower.includes('cantidad')) {
      return { 
        success: false, 
        message: 'En Control de Inventario solo puedo ajustar el stock. Para otros campos, ve a Gestión de Productos.' 
      }
    }
    
    // Buscar el producto
    let productoEncontrado = null
    
    if (nombreProducto) {
      // Buscar por nombre
      const nombreNormalizado = nombreProducto.toLowerCase().trim()
      productoEncontrado = products.value.find(p => 
        p.name.toLowerCase().includes(nombreNormalizado)
      )
    } else if (filteredProducts.value.length === 1) {
      // Si hay solo un producto filtrado, usar ese
      productoEncontrado = filteredProducts.value[0]
    } else if (filteredProducts.value.length > 1) {
      // Hay varios productos, necesitamos especificar cuál
      return { 
        success: false, 
        message: `Hay ${filteredProducts.value.length} productos. ¿Cuál quieres ajustar? Dime el nombre exacto.` 
      }
    }
    
    if (!productoEncontrado) {
      return { success: false, message: `No encontré el producto${nombreProducto ? ` "${nombreProducto}"` : ''}` }
    }
    
    // Parsear el nuevo valor de stock
    const nuevoStock = parseInt(nuevoValor)
    if (isNaN(nuevoStock) || nuevoStock < 0) {
      return { success: false, message: 'El stock debe ser un número válido mayor o igual a 0' }
    }
    
    try {
      // Hacer el ajuste de stock directamente
      const response = await inventoryService.adjustStock(
        productoEncontrado.id,
        nuevoStock,
        'Ajuste por asistente de voz',
        selectedWarehouse.value,
        null // No hay variant_id en ajuste simple
      )
      
      if (response && response.success) {
        // Recargar productos para actualizar la vista
        await loadProducts()
        
        // Actualizar store global
        await appStore.loadProducts(selectedWarehouse.value, 'general', true)
        
        // Emitir evento para otros módulos
        window.dispatchEvent(new CustomEvent('products-updated', { 
          detail: { 
            source: 'inventory-voice-adjustment',
            productId: productoEncontrado.id,
            newStock: nuevoStock
          } 
        }))
        
        const stockAnterior = productoEncontrado.current_stock || 0
        const diferencia = nuevoStock - stockAnterior
        const tipoMovimiento = diferencia > 0 ? 'aumentó' : 'disminuyó'
        
        return { 
          success: true, 
          message: `Listo. El stock de ${productoEncontrado.name} ${tipoMovimiento} de ${stockAnterior} a ${nuevoStock} unidades.`,
          producto: productoEncontrado.name,
          stockAnterior,
          stockNuevo: nuevoStock
        }
      } else {
        return { success: false, message: 'No pude guardar el ajuste de stock' }
      }
    } catch (error) {
      console.error('Error ajustando stock:', error)
      return { success: false, message: 'Error al ajustar el stock' }
    }
  })
  
  // Actualizar el store de contexto
  uiContext.setScreenData(contextData)
}

// Watcher para actualizar contexto cuando cambian los datos
watch([products, searchTerm, categoryFilter, stockFilter, activeTab, movementsData], () => {
  updateScreenContextForAI()
}, { deep: true })
</script>

<style scoped>
/* Transiciones suaves */
* {
  transition: all 0.2s ease-in-out;
}
</style>