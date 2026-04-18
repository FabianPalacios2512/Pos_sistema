<template>
  <div class="min-h-screen font-sans bg-gray-50 dark:bg-[#131314] transition-colors duration-300 px-8">
    <div class="p-4 lg:p-6 space-y-6 pb-8 animate-fade-in">
      
      <!-- Header -->
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4">
        <div>
          <h1 class="text-2xl font-black text-gray-900 dark:text-white">Control de Inventario</h1>
          <p class="text-sm text-gray-500 dark:text-zinc-400 mt-1">Gestiona stock, movimientos y alertas de productos</p>
        </div>
        
        <div class="flex items-center space-x-3">
          <!-- Botón Actualizar - Gemini style -->
          <button @click="refreshInventoryData" 
                  :disabled="loading"
                  class="px-5 py-2.5 bg-white dark:bg-zinc-900 hover:bg-gray-50 dark:hover:bg-zinc-800 text-gray-700 dark:text-zinc-200 text-sm font-semibold rounded-md border border-gray-300 dark:border-zinc-700 shadow-sm transition-all duration-200 flex items-center space-x-2"
                  :class="{ 'opacity-50 cursor-not-allowed': loading }">
            <svg class="w-4 h-4" :class="{'animate-spin': loading}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
              <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
            </svg>
            <span>{{ loading ? 'Actualizando' : 'Actualizar' }}</span>
          </button>
          
          <!-- Botón Principal - Negro/Slate como el resto del sistema -->
          <button @click="openMovementModal"
                  class="px-6 py-2.5 bg-gray-900 dark:bg-white hover:bg-gray-800 dark:hover:bg-gray-100 text-white dark:text-zinc-900 text-sm font-semibold rounded-md  transition-all duration-300 flex items-center space-x-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
              <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
            </svg>
            <span>Nuevo Movimiento</span>
          </button>
        </div>
      </div>
      
      <!-- KPIs — Metrics Ribbon (Vercel/Linear) -->
      <div class="bg-white dark:bg-zinc-900 rounded-md border border-gray-200 dark:border-zinc-800 grid grid-cols-2 lg:grid-cols-4 divide-x divide-gray-100 dark:divide-zinc-800 mb-4">
        <div class="flex flex-col gap-1 px-5 py-4">
          <div class="flex items-center justify-between">
            <p class="text-xs text-gray-400 dark:text-zinc-500 uppercase tracking-wider font-medium">Total Productos</p>
            <svg class="w-4 h-4 text-blue-500 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
          </div>
          <p class="text-2xl font-black text-gray-900 dark:text-white tabular-nums">{{ totalProducts }}</p>
          <p class="text-xs text-gray-400 dark:text-zinc-500">En inventario</p>
        </div>
        <div class="flex flex-col gap-1 px-5 py-4">
          <div class="flex items-center justify-between">
            <p class="text-xs text-gray-400 dark:text-zinc-500 uppercase tracking-wider font-medium">Stock Bajo</p>
            <svg class="w-4 h-4 text-amber-500 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
          </div>
          <p class="text-2xl font-semibold tabular-nums" :class="lowStockProducts > 0 ? 'text-amber-600 dark:text-amber-400' : 'text-gray-900 dark:text-white'">{{ lowStockProducts }}</p>
          <p class="text-xs" :class="lowStockProducts > 0 ? 'text-amber-500 dark:text-amber-400' : 'text-gray-400 dark:text-zinc-500'">{{ lowStockProducts > 0 ? 'Requieren reposición' : 'Todo abastecido' }}</p>
        </div>
        <div class="flex flex-col gap-1 px-5 py-4">
          <div class="flex items-center justify-between">
            <p class="text-xs text-gray-400 dark:text-zinc-500 uppercase tracking-wider font-medium">Valor Potencial</p>
            <svg class="w-4 h-4 text-emerald-500 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
          </div>
          <p class="text-2xl font-black text-gray-900 dark:text-white tabular-nums">${{ totalInventoryValue.toLocaleString() }}</p>
          <p class="text-xs text-gray-400 dark:text-zinc-500">precio × stock</p>
        </div>
        <div class="flex flex-col gap-1 px-5 py-4">
          <div class="flex items-center justify-between">
            <p class="text-xs text-gray-400 dark:text-zinc-500 uppercase tracking-wider font-medium">Movimientos Hoy</p>
            <svg class="w-4 h-4 text-violet-500 dark:text-violet-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M7.5 21L3 16.5m0 0L7.5 12M3 16.5h13.5m0-13.5L21 7.5m0 0L16.5 12M21 7.5H7.5"/></svg>
          </div>
          <p class="text-2xl font-black text-gray-900 dark:text-white tabular-nums">{{ todayMovements }}</p>
          <p class="text-xs text-gray-400 dark:text-zinc-500">Actualizado hoy</p>
        </div>
      </div>

      <!-- Selector de Sede - Prominente entre KPIs y tabla -->
      <div v-if="showWarehouseFilter" class="flex items-center gap-3 px-1">
        <div class="flex items-center gap-2">
          <svg class="w-4 h-4 text-gray-400 dark:text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 21v-7.5a.75.75 0 01.75-.75h3a.75.75 0 01.75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349m-16.5 11.65V9.35m0 0a3.001 3.001 0 003.75-.615A2.993 2.993 0 009.75 9.75c.896 0 1.7-.393 2.25-1.016a2.993 2.993 0 002.25 1.016c.896 0 1.7-.393 2.25-1.016a3.001 3.001 0 003.75.614m-16.5 0a3.004 3.004 0 01-.621-4.72L4.318 3.44A1.5 1.5 0 015.378 3h13.243a1.5 1.5 0 011.06.44l1.19 1.189a3 3 0 01-.621 4.72m-13.5 8.65h3.75a.75.75 0 00.75-.75V13.5a.75.75 0 00-.75-.75H6.75a.75.75 0 00-.75.75v3.15c0 .415.336.75.75.75z"/>
          </svg>
          <span class="text-xs font-semibold text-gray-500 dark:text-zinc-400 uppercase tracking-wider">Sede:</span>
        </div>
        <div class="flex items-center gap-1.5 flex-wrap">
          <button @click="selectedWarehouse = null; refreshInventoryData()"
                  :class="selectedWarehouse === null ? 'bg-gray-900 dark:bg-white text-white dark:text-gray-900 shadow-sm' : 'bg-white dark:bg-zinc-800 text-gray-600 dark:text-zinc-400 border border-gray-200 dark:border-zinc-700 hover:bg-gray-50 dark:hover:bg-zinc-700'"
                  class="px-3 py-1.5 text-xs font-bold rounded-lg transition-all">
            Todas
          </button>
          <button v-for="wh in warehouses" :key="wh.id"
                  @click="selectedWarehouse = wh.id; refreshInventoryData()"
                  :class="selectedWarehouse === wh.id ? 'bg-gray-900 dark:bg-white text-white dark:text-gray-900 shadow-sm' : 'bg-white dark:bg-zinc-800 text-gray-600 dark:text-zinc-400 border border-gray-200 dark:border-zinc-700 hover:bg-gray-50 dark:hover:bg-zinc-700'"
                  class="px-3 py-1.5 text-xs font-bold rounded-lg transition-all">
            {{ wh.name }}{{ wh.is_default ? ' ★' : '' }}
          </button>
        </div>
      </div>

      <!-- Contenedor Principal -->
      <div class="bg-white dark:bg-zinc-900 rounded-md border border-gray-200 dark:border-zinc-800 shadow-sm overflow-hidden">
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
            <!-- Filtros -->
            <div class="px-4 py-3 bg-gray-50 dark:bg-zinc-800">
              <div class="flex flex-wrap items-center gap-3">
                <div class="flex-1 min-w-48 relative">
                  <svg class="absolute left-3.5 top-1/2 transform -translate-y-1/2 w-4 h-4 text-gray-400 dark:text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                  </svg>
                  <input v-model="searchTerm" 
                         type="text" 
                         placeholder="Buscar productos..." 
                         class="w-full pl-10 pr-4 py-2.5 text-sm rounded-xl bg-white dark:bg-zinc-700 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-zinc-500 focus:outline-none focus:ring-2 focus:ring-blue-400 transition-all duration-200">
                </div>
                
                <select v-model="categoryFilter" 
                        class="px-3 py-2.5 text-sm rounded-xl bg-white dark:bg-zinc-700 text-gray-900 dark:text-white font-medium focus:outline-none focus:ring-2 focus:ring-blue-400 transition-all duration-200 min-w-40 cursor-pointer">
                  <option value="">Todas las categorías</option>
                  <option v-for="category in categories" :key="category.id" :value="category.name">
                    {{ category.name }}
                  </option>
                </select>
                
                <select v-model="stockFilter" 
                        class="px-3 py-2.5 text-sm rounded-xl bg-white dark:bg-zinc-700 text-gray-900 dark:text-white font-medium focus:outline-none focus:ring-2 focus:ring-blue-400 transition-all duration-200 min-w-36 cursor-pointer">
                  <option value="">Todo el stock</option>
                  <option value="low">Stock bajo</option>
                  <option value="normal">Stock normal</option>
                  <option value="high">Stock alto</option>
                </select>
                
                <button @click="clearAllFilters" 
                        class="p-2 text-gray-600 dark:text-zinc-400 hover:text-rose-600 dark:hover:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-600/20 rounded-xl transition-all duration-200"
                        title="Limpiar filtros">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" 
                          d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                  </svg>
                </button>
              </div>
            </div>
          
          <!-- Tabla de Stock -->
          <div class="overflow-x-auto" style="scrollbar-width: thin;">
            <table class="min-w-full">
              <thead class="bg-gray-50 dark:bg-zinc-800">
                <tr>
                  <th class="px-4 py-2.5 text-left text-xs font-medium text-gray-600 dark:text-zinc-400 uppercase tracking-wider">Producto</th>
                  <th class="px-4 py-2.5 text-left text-xs font-medium text-gray-600 dark:text-zinc-400 uppercase tracking-wider">Categoría</th>
                  <!-- Modo Matriz: columnas por sede -->
                  <template v-if="!selectedWarehouse && warehouses.length > 1">
                    <th v-for="wh in visibleWarehouses" :key="wh.id" class="px-3 py-2.5 text-center text-xs font-medium text-gray-600 dark:text-zinc-400 uppercase tracking-wider whitespace-nowrap min-w-[90px]">
                      <div class="flex items-center justify-center gap-1">
                        <svg class="w-3 h-3 text-gray-400 dark:text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                        {{ wh.name }}
                      </div>
                    </th>
                  </template>
                  <template v-else>
                    <th class="px-4 py-2.5 text-center text-xs font-medium text-gray-600 dark:text-zinc-400 uppercase tracking-wider">
                      Stock{{ selectedWarehouseName ? ' — ' + selectedWarehouseName : '' }}
                    </th>
                  </template>
                  <th class="px-4 py-2.5 text-center text-xs font-medium text-gray-600 dark:text-zinc-400 uppercase tracking-wider">Ventas</th>
                  <th class="px-4 py-2.5 text-right text-xs font-medium text-gray-600 dark:text-zinc-400 uppercase tracking-wider">Ingresos</th>
                  <th class="px-4 py-2.5 text-center text-xs font-medium text-gray-600 dark:text-zinc-400 uppercase tracking-wider">Estado</th>
                  <th class="px-4 py-2.5 text-center text-xs font-medium text-gray-600 dark:text-zinc-400 uppercase tracking-wider">Acciones</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-100 dark:divide-zinc-800 bg-white dark:bg-zinc-900">
                <tr v-for="product in paginatedProducts" :key="product._rowKey || product.id" class="hover:bg-gray-50 dark:hover:bg-zinc-800/50 transition-colors duration-150 group">
                  <td class="px-4 py-2">
                    <div class="flex items-center">
                      <div class="w-9 h-9 rounded-lg mr-3 overflow-hidden flex-shrink-0"
                           :class="getProductImage(product) ? 'bg-white dark:bg-zinc-800' : 'bg-gray-50 dark:bg-zinc-800 flex items-center justify-center'">
                        <img v-if="getProductImage(product)"
                             :src="getProductImage(product)" 
                             :alt="product.name"
                             class="w-full h-full object-cover">
                        <svg v-else class="w-4 h-4 text-gray-400 dark:text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.75 7.5h16.5m-15.75 0l1.5-1.875A2.625 2.625 0 018.25 4.5h7.5a2.625 2.625 0 012.25 1.125l1.5 1.875"/>
                        </svg>
                      </div>
                      <div class="min-w-0">
                        <p class="text-sm font-medium text-gray-900 dark:text-white leading-tight truncate">{{ product.name }}</p>
                        <div v-if="product._isVariantRow && product._variantOptions" class="flex flex-wrap items-center gap-1 mt-1">
                          <template v-for="(opt, idx) in product._variantOptions" :key="idx">
                            <span v-if="opt.name.toLowerCase() === 'color' && String(opt.value).startsWith('#')" class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded bg-gray-100 dark:bg-zinc-800 border border-gray-200 dark:border-zinc-700">
                              <span class="inline-block w-3.5 h-3.5 rounded-full border border-gray-300 dark:border-zinc-500" :style="{ backgroundColor: opt.value }"></span>
                              <span class="text-[10px] font-medium text-gray-500 dark:text-zinc-400">Color</span>
                            </span>
                            <span v-else class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded bg-gray-100 dark:bg-zinc-800 border border-gray-200 dark:border-zinc-700">
                              <span class="text-[9px] font-medium text-gray-400 dark:text-zinc-500 uppercase">{{ opt.name }}</span>
                              <span class="text-[11px] font-bold text-gray-700 dark:text-zinc-200 uppercase">{{ opt.value }}</span>
                            </span>
                          </template>
                        </div>
                        <p v-if="product.barcode" class="text-[10px] text-gray-400 dark:text-zinc-500 font-mono mt-0.5">{{ product.barcode }}</p>
                      </div>
                    </div>
                  </td>
                  <td class="px-4 py-2 whitespace-nowrap">
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-medium bg-gray-50 dark:bg-zinc-800 text-gray-700 dark:text-zinc-300">
                      {{ product.category?.name || product.category || 'Sin categoría' }}
                    </span>
                  </td>
                  <!-- Modo Matriz: stock por cada sede -->
                  <template v-if="!selectedWarehouse && warehouses.length > 1">
                    <td v-for="wh in visibleWarehouses" :key="wh.id" class="px-3 py-2 whitespace-nowrap text-center">
                      <span v-if="getProductWarehouseStock(product, wh.id) !== null"
                            :class="[
                              'text-sm font-bold tabular-nums',
                              getProductWarehouseStock(product, wh.id) <= (product.min_stock || 10)
                                ? 'text-rose-600 dark:text-rose-400'
                                : 'text-gray-900 dark:text-white'
                            ]">
                        {{ getProductWarehouseStock(product, wh.id) }}
                      </span>
                      <span v-else class="text-[11px] text-gray-300 dark:text-zinc-600">—</span>
                    </td>
                  </template>
                  <template v-else>
                    <td class="px-4 py-2 whitespace-nowrap text-center">
                      <div class="flex flex-col items-center">
                        <span :class="[
                          'text-sm font-bold',
                          product.current_stock <= (product.min_stock || 10)
                            ? 'text-rose-600 dark:text-rose-400'
                            : 'text-gray-900 dark:text-white'
                        ]">{{ product.current_stock || 0 }}</span>
                        <span class="text-[10px] text-gray-400 dark:text-zinc-500">Min: {{ product.min_stock || 10 }}</span>
                      </div>
                    </td>
                  </template>
                  <td class="px-4 py-2 whitespace-nowrap text-center">
                    <span class="text-sm font-medium text-gray-900 dark:text-white">{{ product.total_sold || 0 }}</span>
                  </td>
                  <td class="px-4 py-2 whitespace-nowrap text-right">
                    <span class="text-sm font-semibold text-emerald-600 dark:text-emerald-400">${{ parseFloat(product.total_revenue || 0).toLocaleString() }}</span>
                  </td>
                  <td class="px-4 py-2 whitespace-nowrap text-center">
                    <span :class="[
                      'inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wide',
                      (product.current_stock || 0) <= (product.min_stock || 10) 
                        ? 'bg-rose-50 dark:bg-rose-900/20 text-rose-600 dark:text-rose-400' 
                        : 'bg-emerald-50 dark:bg-emerald-900/20 text-emerald-600 dark:text-emerald-400'
                    ]">
                      {{ (product.current_stock || 0) <= (product.min_stock || 10) ? 'Bajo' : 'OK' }}
                    </span>
                  </td>
                  <td class="px-4 py-2 whitespace-nowrap text-center">
                    <div class="flex items-center justify-center gap-0.5">
                      <button @click="adjustStock(product)" 
                              class="p-1.5 text-gray-500 dark:text-zinc-400 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-lg transition-all"
                              title="Ajustar Stock">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                          <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                      </button>
                      <button @click="viewMovements(product)" 
                              class="p-1.5 text-gray-500 dark:text-zinc-400 hover:text-purple-600 dark:hover:text-purple-400 hover:bg-purple-50 dark:hover:bg-purple-900/20 rounded-lg transition-all"
                              title="Ver Historial">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
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
                        class="px-4 py-2 bg-blue-600 hover:bg-blue-700 dark:bg-blue-400 dark:hover:bg-blue-300 text-white dark:text-zinc-900 text-xs font-medium rounded-xl transition-all duration-200 flex items-center gap-1.5 flex-shrink-0">
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
                  class="p-2 text-gray-600 dark:text-zinc-400 hover:bg-gray-50 dark:hover:bg-zinc-800 rounded-md transition-colors">
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
                        class="p-2 text-emerald-600 dark:text-emerald-400 hover:bg-emerald-50 rounded-md transition-colors">
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
                      <p class="text-sm text-gray-600 dark:text-zinc-400">{{ product.category?.name || product.category || 'Sin categoría' }}</p>
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
                <option value="entrada">Entrada</option>
                <option value="salida">Salida</option>
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
                  class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 disabled:bg-gray-200 disabled:text-gray-400 dark:bg-blue-400 dark:hover:bg-blue-300 dark:disabled:bg-zinc-700 dark:disabled:text-gray-600 text-white dark:text-zinc-900 rounded-full text-sm font-medium transition-all duration-200 disabled:cursor-not-allowed">
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
        
        <!-- Header compacto -->
        <div class="bg-white dark:bg-zinc-900 border-b border-gray-200 dark:border-zinc-700 px-6 py-3 flex-shrink-0 rounded-t-2xl">
          <div class="flex items-center justify-between">
            <div>
              <h3 class="text-lg font-bold text-gray-900 dark:text-white">Ajustar Stock</h3>
              <div class="flex items-center gap-2 mt-0.5">
                <p class="text-xs text-gray-500 dark:text-zinc-500">Modifica la cantidad en inventario</p>
                <template v-if="adjustModalWarehouses.length > 1 && adjustModalSelectedWarehouse">
                  <span class="text-gray-300 dark:text-zinc-600">·</span>
                  <span class="text-xs font-semibold text-slate-600 dark:text-slate-400 flex items-center gap-1">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                    {{ adjustModalWarehouses.find(w => w.id === adjustModalSelectedWarehouse)?.name }}
                  </span>
                </template>
                <template v-else-if="adjustModalWarehouses.length > 1 && !adjustModalSelectedWarehouse">
                  <span class="text-gray-300 dark:text-zinc-600">·</span>
                  <span class="text-xs font-semibold text-amber-600 dark:text-amber-400">⚠ Selecciona una sede</span>
                </template>
              </div>
            </div>
            <button @click="showAdjustModal = false" 
                    class="p-1.5 hover:bg-gray-100 dark:hover:bg-zinc-800 rounded-md transition-all">
              <svg class="w-5 h-5 text-gray-400 dark:text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
              </svg>
            </button>
          </div>
        </div>
        
        <!-- Contenido del Modal -->
        <div class="flex-1 overflow-y-auto bg-gray-50 dark:bg-zinc-950 p-6" v-if="selectedProductForAdjust">
          
          <!-- Información del Producto -->
          <div class="bg-white dark:bg-zinc-900 rounded-xl px-5 py-3 mb-4 border border-gray-200 dark:border-zinc-800">
            <div class="flex items-center justify-between">
              <div>
                <h4 class="text-sm font-semibold text-gray-900 dark:text-white">{{ selectedProductForAdjust.name }}</h4>
                <div class="flex items-center gap-2 mt-0.5">
                  <span class="text-xs text-gray-500 dark:text-zinc-500">{{ selectedProductForAdjust.category?.name || selectedProductForAdjust.category || 'Sin categoría' }}</span>
                  <span v-if="productVariants.length > 0" class="text-xs text-gray-400 dark:text-zinc-500">· {{ productVariants.length }} variantes</span>
                </div>
              </div>
              <div v-if="productVariants.length === 0" class="text-right">
                <span class="text-sm font-bold text-gray-900 dark:text-white">{{ adjustModalCurrentStock }}</span>
                <span class="text-xs text-gray-500 dark:text-zinc-500 ml-1">/ mín {{ selectedProductForAdjust.min_stock || 10 }}</span>
              </div>
            </div>
          </div>

          <!-- Selector de Sede -->
          <div v-if="adjustModalWarehouses.length > 1" class="bg-white dark:bg-zinc-900 rounded-xl px-5 py-3 mb-4 border border-gray-200 dark:border-zinc-800">
            <label class="block text-xs font-semibold text-gray-500 dark:text-zinc-400 uppercase tracking-wider mb-2">
              <div class="flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
                ¿En qué sede ajustar el stock?
              </div>
            </label>
            <div class="grid gap-2" :class="adjustModalWarehouses.length <= 3 ? `grid-cols-${adjustModalWarehouses.length}` : 'grid-cols-3'">
              <button
                v-for="wh in adjustModalWarehouses"
                :key="wh.id"
                type="button"
                @click="adjustModalSelectedWarehouse = wh.id; onAdjustWarehouseChange()"
                :class="[
                  'px-3 py-2.5 rounded-lg transition-all text-left border text-sm font-medium',
                  adjustModalSelectedWarehouse === wh.id
                    ? 'bg-slate-50 dark:bg-slate-800/40 border-slate-700 dark:border-slate-400 text-gray-900 dark:text-white'
                    : 'bg-white dark:bg-zinc-800 border-gray-200 dark:border-zinc-700/50 text-gray-600 dark:text-zinc-400 hover:bg-gray-50 dark:hover:bg-zinc-700/50'
                ]"
              >
                <div class="flex items-center justify-between">
                  <span class="truncate">{{ wh.name }}</span>
                  <svg v-if="adjustModalSelectedWarehouse === wh.id" class="w-4 h-4 text-slate-700 dark:text-slate-400 flex-shrink-0 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                  </svg>
                </div>
              </button>
            </div>
          </div>

          <!-- Grid de Formulario -->
          <!-- Mensaje cuando no se ha seleccionado sede -->
          <!-- Sin sede seleccionada -->
          <div v-if="adjustModalWarehouses.length > 1 && !adjustModalSelectedWarehouse" class="flex flex-col items-center justify-center py-10 text-center">
            <div class="w-14 h-14 rounded-2xl bg-amber-50 dark:bg-amber-950 flex items-center justify-center mb-3">
              <svg class="w-7 h-7 text-amber-500 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
              </svg>
            </div>
            <p class="text-sm font-semibold text-gray-700 dark:text-zinc-300">Selecciona la sede primero</p>
            <p class="text-xs text-gray-400 dark:text-zinc-500 mt-1">Elige en cuál sede quieres ajustar el inventario</p>
          </div>
          <!-- Sede seleccionada pero producto no existe en ella -->
          <div v-else-if="adjustModalSelectedWarehouse && !selectedWarehouseHasProduct" class="flex flex-col items-center justify-center py-8 text-center px-4">
            <div class="w-14 h-14 rounded-2xl bg-rose-50 dark:bg-rose-950 flex items-center justify-center mb-3">
              <svg class="w-7 h-7 text-rose-500 dark:text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/>
              </svg>
            </div>
            <p class="text-sm font-bold text-gray-800 dark:text-zinc-200">Producto no registrado en esta sede</p>
            <p class="text-xs text-gray-500 dark:text-zinc-400 mt-1.5 max-w-xs leading-relaxed">
              <strong>{{ selectedProductForAdjust?.name }}</strong> no tiene inventario en esta sede. Ajustar aquí directamente puede causar inconsistencias en la base de datos.
            </p>
            <div class="mt-4 w-full max-w-xs space-y-2 text-left">
              <div class="flex items-start gap-2.5 bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-800 rounded-lg px-3 py-2.5">
                <svg class="w-4 h-4 text-amber-500 dark:text-amber-400 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                <p class="text-xs text-amber-700 dark:text-amber-400"><span class="font-bold">Opción A:</span> Ve a <strong>Traslados</strong> y mueve unidades desde otra sede</p>
              </div>
              <div class="flex items-start gap-2.5 bg-blue-50 dark:bg-blue-950/40 border border-blue-200 dark:border-blue-800 rounded-lg px-3 py-2.5">
                <svg class="w-4 h-4 text-blue-500 dark:text-blue-400 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                <p class="text-xs text-blue-700 dark:text-blue-400"><span class="font-bold">Opción B:</span> Edita el producto y <strong>habilita esta sede</strong> en "Stock por Sede"</p>
              </div>
            </div>
          </div>
          <div v-else class="grid grid-cols-12 gap-5">
            
            <!-- Columna Izquierda: Variantes (si existen) -->
            <div v-if="productVariants.length > 0" class="col-span-5 space-y-5">
              <div class="bg-white dark:bg-zinc-900 rounded-2xl p-5">
                <h4 class="text-xs font-semibold text-gray-500 dark:text-zinc-400 uppercase tracking-wider mb-2">Selecciona la Variante</h4>
                <div class="space-y-1 max-h-[320px] overflow-y-auto">
                  <button
                    v-for="variant in productVariants"
                    :key="variant.id"
                    @click="selectVariant(variant)"
                    type="button"
                    :class="[
                      'w-full px-3 py-2.5 rounded-lg transition-all text-left border',
                      selectedVariant?.id === variant.id 
                        ? 'bg-slate-50 dark:bg-slate-800/40 border-l-[3px] border-l-slate-700 dark:border-l-slate-400 border-t-gray-200 border-r-gray-200 border-b-gray-200 dark:border-t-zinc-700 dark:border-r-zinc-700 dark:border-b-zinc-700'
                        : 'bg-white dark:bg-zinc-800/50 border-gray-200 dark:border-zinc-700/50 hover:bg-gray-50 dark:hover:bg-zinc-700/50'
                    ]"
                  >
                    <div class="flex items-center justify-between">
                      <div class="flex-1 min-w-0">
                        <p class="font-medium text-gray-900 dark:text-white text-sm">
                          {{ formatVariantLabel(variant) }}
                        </p>
                        <div class="flex items-center gap-2 text-xs text-gray-500 dark:text-zinc-400 mt-0.5">
                          <span class="font-mono">{{ variant.sku }}</span>
                          <span class="text-gray-300 dark:text-zinc-600">·</span>
                          <span>${{ Number(variant.price || 0).toLocaleString('es-CO') }}</span>
                        </div>
                      </div>
                      <div class="text-right pl-3">
                        <span class="text-sm font-bold" :class="(variant.stock || 0) <= (selectedProductForAdjust?.min_stock || 10) ? 'text-rose-600 dark:text-rose-400' : 'text-gray-900 dark:text-white'">{{ variant.stock || 0 }}</span>
                        <span class="text-xs text-gray-400 dark:text-zinc-500 ml-0.5">uds</span>
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
                  <label class="block text-xs font-semibold text-gray-500 dark:text-zinc-400 uppercase tracking-wider mb-2">Tipo de Ajuste</label>
                  <div class="grid grid-cols-2 gap-2">
                    <button type="button" @click="setQuickAdjustment('restock')"
                            :class="['px-3 py-2.5 rounded-lg transition-all border text-sm font-medium',
                                     adjustForm.adjustmentType === 'restock' 
                                       ? 'bg-gray-900 dark:bg-white text-white dark:text-gray-900 border-gray-900 dark:border-white'
                                       : 'bg-white dark:bg-zinc-800 text-gray-600 dark:text-zinc-400 border-gray-300 dark:border-zinc-700 hover:bg-gray-50 dark:hover:bg-zinc-700']">
                      <div class="flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        <span>Restock</span>
                      </div>
                    </button>
                    <button type="button" @click="setQuickAdjustment('correction')"
                            :class="['px-3 py-2.5 rounded-lg transition-all border text-sm font-medium',
                                     adjustForm.adjustmentType === 'correction'
                                       ? 'bg-gray-900 dark:bg-white text-white dark:text-gray-900 border-gray-900 dark:border-white'
                                       : 'bg-white dark:bg-zinc-800 text-gray-600 dark:text-zinc-400 border-gray-300 dark:border-zinc-700 hover:bg-gray-50 dark:hover:bg-zinc-700']">
                      <div class="flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        <span>Corrección</span>
                      </div>
                    </button>
                  </div>
                </div>

                <!-- Nuevo Stock -->
                <div>
                  <label class="block text-xs font-semibold text-gray-500 dark:text-zinc-400 uppercase tracking-wider mb-2">Nuevo Stock</label>
                  <div class="relative">
                    <input v-model="adjustForm.new_stock"
                           type="number"
                           min="0"
                           step="1"
                           :class="['w-full px-4 py-3 pr-12 rounded-lg transition-all focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm border',
                                    adjustForm.errors.new_stock
                                      ? 'bg-rose-50 dark:bg-rose-900/20 text-rose-600 dark:text-rose-400 border-rose-300 dark:border-rose-700'
                                      : 'bg-white dark:bg-zinc-800 text-gray-900 dark:text-white border-gray-300 dark:border-zinc-600']"
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
                  <label class="block text-xs font-semibold text-gray-500 dark:text-zinc-400 uppercase tracking-wider mb-2">Motivo del Ajuste</label>
                  <select v-model="adjustForm.reason"
                          :class="['w-full px-4 py-3 rounded-lg transition-all focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm cursor-pointer border',
                                   adjustForm.errors.reason
                                     ? 'bg-rose-50 dark:bg-rose-900/20 text-rose-600 dark:text-rose-400 border-rose-300 dark:border-rose-700'
                                     : 'bg-white dark:bg-zinc-800 text-gray-900 dark:text-white border-gray-300 dark:border-zinc-600']"
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
                         class="w-full px-4 py-3 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400 bg-gray-50 dark:bg-zinc-800 text-gray-900 dark:text-white text-sm mt-2 border border-gray-300 dark:border-zinc-600"
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
                   class="mt-4 relative overflow-hidden rounded-2xl transition-all border"
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
                        {{ stockDifference >= 0 ? 'Entrada' : 'Salida' }}
                      </span>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Footer: Botones de Acción -->
          <div class="flex justify-end gap-3 pt-4 mt-4 border-t border-gray-200 dark:border-zinc-700">
            <button @click="showAdjustModal = false" 
                    class="px-5 py-2.5 text-gray-600 dark:text-zinc-400 hover:bg-gray-100 dark:hover:bg-zinc-800 text-sm font-medium rounded-lg border border-gray-300 dark:border-zinc-700 transition-all duration-200">
              Cancelar
            </button>
            <button @click="processStockAdjustment"
                    :disabled="!isFormValid || adjustmentLoading"
                    class="px-6 py-2.5 bg-gray-900 hover:bg-black disabled:bg-gray-200 disabled:text-gray-400 dark:bg-white dark:hover:bg-gray-100 dark:disabled:bg-zinc-700 dark:disabled:text-zinc-500 text-white dark:text-gray-900 rounded-lg text-sm font-semibold transition-all duration-200 disabled:cursor-not-allowed">
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
              <h3 class="text-xl font-black text-gray-900 dark:text-white">Historial de Movimientos</h3>
              <p class="text-sm text-gray-600 dark:text-zinc-400" v-if="selectedProductForHistory">
                {{ selectedProductForHistory.name }} - {{ selectedProductForHistory.category }}
              </p>
            </div>
          </div>
          <button @click="showHistoryModal = false" 
                  class="p-2 text-gray-600 dark:text-zinc-400 hover:bg-gray-50 dark:hover:bg-zinc-800 rounded-md transition-colors">
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
                    class="px-5 py-3 bg-blue-600 hover:bg-blue-700 disabled:bg-gray-200 disabled:text-gray-400 dark:bg-blue-400 dark:hover:bg-blue-300 dark:disabled:bg-zinc-700 text-white dark:text-zinc-900 rounded-full font-medium flex items-center gap-2 transition-colors text-sm">
              <svg class="w-4 h-4" :class="{'animate-spin': historyLoading}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
              </svg>
              {{ historyLoading ? 'Cargando...' : 'Actualizar' }}
            </button>
          </div>
        </div>

        <!-- Lista de Movimientos - Gemini Style -->
        <div class="bg-gray-50 dark:bg-zinc-800 rounded-md overflow-hidden max-h-96 overflow-y-auto">
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
import { useAuth } from '../store/auth.js'
import { useUIContextStore } from '../store/uiContextStore.js' // IA
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

// NUEVO: Estado para multi-sede
const warehouses = ref([])

// --- ROLES Y PERMISOS ---
const { user: authUser } = useAuth()
const isVendedor = computed(() => {
  const role = authUser.value?.role?.name || authUser.value?.role || ''
  const r = role.toLowerCase()
  return r === 'vendedor' || r === 'cajero'
})
const visibleWarehouses = computed(() => {
  if (isVendedor.value && authUser.value?.warehouse_id) {
    return warehouses.value.filter(w => w.id === authUser.value.warehouse_id)
  }
  return warehouses.value
})
const selectedWarehouse = ref(null)
const showWarehouseFilter = ref(false)

// Computed: nombre de la sede seleccionada
const selectedWarehouseName = computed(() => {
  if (!selectedWarehouse.value) return ''
  const wh = warehouses.value.find(w => w.id === selectedWarehouse.value)
  return wh ? wh.name : ''
})

// Limpiar todos los filtros
const clearAllFilters = () => {
  searchTerm.value = ''
  categoryFilter.value = ''
  stockFilter.value = ''
  if (showWarehouseFilter.value) {
    const defaultWh = warehouses.value.find(w => w.is_default)
    selectedWarehouse.value = defaultWh?.id || warehouses.value[0]?.id || null
    refreshInventoryData()
  }
}

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
  variant_id: null, // NUEVO: Para productos fashion
  new_stock: '',
  reason: '',
  customReason: '',
  adjustmentType: '',
  errors: {
    new_stock: '',
    reason: '',
    variant: '' // Error de variante
  }
})
const selectedProductForAdjust = ref(null)
const productVariants = ref([]) // NUEVO: Lista de variantes
const selectedVariant = ref(null) // NUEVO: Variante seleccionada
const adjustmentLoading = ref(false)
const adjustModalWarehouses = ref([]) // Sedes disponibles para el ajuste
const adjustModalSelectedWarehouse = ref(null) // Sede seleccionada en el modal

// Stock actual mostrado en el modal (cambia según sede seleccionada)
const adjustModalCurrentStock = computed(() => {
  if (!selectedProductForAdjust.value) return 0
  if (adjustModalWarehouses.value.length > 1 && adjustModalSelectedWarehouse.value) {
    // Buscar stock en la sede seleccionada
    const pw = selectedProductForAdjust.value.warehouses?.find(
      w => (w.id || w.warehouse_id) === adjustModalSelectedWarehouse.value
    )
    return pw?.pivot?.stock ?? pw?.stock_quantity ?? selectedProductForAdjust.value.current_stock ?? 0
  }
  return selectedProductForAdjust.value.current_stock || 0
})

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
      (product.barcode && product.barcode.toLowerCase().includes(term)) ||
      (product._variantDetailLabel && product._variantDetailLabel.toLowerCase().includes(term))
    )
  }

  if (categoryFilter.value) {
    filtered = filtered.filter(product => product.category === categoryFilter.value)
  }

  if (stockFilter.value) {
    filtered = filtered.filter(product => {
      const stock = product.current_stock || 0
      const minStock = product.min_stock || 10
      if (stockFilter.value === 'low') return stock <= minStock
      if (stockFilter.value === 'normal') return stock > minStock && stock <= minStock * 2
      if (stockFilter.value === 'high') return stock > minStock * 2
      return true
    })
  }

  return filtered
})

// Computed para notificaciones
const unreadMovementsCount = computed(() => {
  return notificationStore.unreadMovementsCount.value
})

const alertsHaveBeenViewed = ref(false)
const lastSeenAlertsCount = ref(0)

// Computed para total de productos (contar productos únicos, no filas de variantes)
const totalProducts = computed(() => {
  const uniqueParentIds = new Set(products.value.map(p => p._parentProductId))
  return uniqueParentIds.size
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
const totalInventoryValue = computed(() => products.value.reduce((sum, p) => sum + ((p.current_stock || 0) * parseFloat(p.sale_price || p.price || 0)), 0))
const todayMovements = computed(() => recentMovements.value.length)
const stockAlerts = computed(() => products.value.filter(p => (p.current_stock || 0) <= (p.min_stock || 10)).map(p => ({
  ...p,
  stock: p.current_stock || 0,
  name: p._isVariantRow ? `${p._parentName} (${p._variantLabel})` : p.name
})))

const unreadAlertsCount = computed(() => {
  const currentCount = stockAlerts.value?.length || 0
  if (alertsHaveBeenViewed.value && currentCount <= lastSeenAlertsCount.value) return 0
  if (currentCount === 0) return 0
  return alertsHaveBeenViewed.value ? currentCount - lastSeenAlertsCount.value : currentCount
})

// Computed properties para el modal de ajuste mejorado
const stockDifference = computed(() => {
  if (!selectedProductForAdjust.value || adjustForm.value.new_stock === '' || isNaN(adjustForm.value.new_stock)) {
    return 0
  }
  
  // Si hay variante seleccionada, usar su stock; si no, usar stock de la sede seleccionada
  const currentStock = selectedVariant.value 
    ? selectedVariant.value.stock 
    : adjustModalCurrentStock.value
    
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

// Computed: detecta si el producto/variante EXISTE en la sede seleccionada
// (diferente a stock 0 — aquí se verifica que haya un registro en product_warehouse)
const selectedWarehouseHasProduct = computed(() => {
  if (!adjustModalSelectedWarehouse.value || !selectedProductForAdjust.value) return true // si no hay sede seleccionada aún, no bloquear
  const isVariable = selectedProductForAdjust.value.type === 'variable' || selectedProductForAdjust.value.product_type === 'variable'
  if (isVariable) {
    // Para productos variables: hay producto si hay al menos 1 variante cargada para esta sede
    return productVariants.value.length > 0
  } else {
    // Para productos simples: verificar que la sede esté en el array warehouses del producto
    const warehouses = selectedProductForAdjust.value.warehouses || []
    return warehouses.some(wh => {
      const id = wh.id ?? wh.warehouse_id
      return parseInt(id) === parseInt(adjustModalSelectedWarehouse.value)
    })
  }
})

const isFormValid = computed(() => {
  // Si hay múltiples sedes, se requiere elegir una
  if (adjustModalWarehouses.value.length > 1 && !adjustModalSelectedWarehouse.value) return false
  // Bloquear si el producto no existe en la sede seleccionada
  if (!selectedWarehouseHasProduct.value) return false
  return adjustForm.value.new_stock !== '' && 
         !isNaN(adjustForm.value.new_stock) && 
         adjustForm.value.reason !== '' &&
         !adjustForm.value.errors.new_stock &&
         !adjustForm.value.errors.reason &&
         (adjustForm.value.reason !== 'Otro' || adjustForm.value.customReason.trim() !== '')
})

// Computed: Stock global para el modal de detalles (suma de todas las variantes o stock del producto)
const productDetailGlobalStock = computed(() => {
  if (productVariants.value.length > 0) {
    return productVariants.value.reduce((sum, v) => sum + (v.stock || 0), 0)
  }
  return selectedProductForAdjust.value?.current_stock || 0
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

    // Construir parámetros con filtro de warehouse si está activo
    const params = { 
      per_page: 1000,
      status: 'all', // Obtener todos los productos (activos e inactivos)
      with_sales: true, // Incluir datos de ventas e ingresos
      _t: Date.now() // Cache busting
    }
    
    if (showWarehouseFilter.value && selectedWarehouse.value) {
      params.warehouse_id = selectedWarehouse.value
    }

    // USAR EL MISMO ENDPOINT QUE PRODUCTSVIEW para obtener imágenes
    const response = await productsService.getAll(params)
    // Los productos pueden venir en response.data.data (paginación) o directamente en response.data
    const productsList = response.data?.data || response.data || []
    // Verificar el primer producto para ver estructura de imágenes
    if (productsList.length > 0) {
    }

    // Mapear y expandir productos: variantes se muestran como filas separadas
    const expandedProducts = []
    for (const product of productsList) {
      const baseProduct = {
        ...product,
        type: product.type || product.product_type || 'simple',
        category: product.category
          ? (typeof product.category === 'string' ? product.category : (product.category.name || 'Sin categoría'))
          : 'Sin categoría',
        current_stock: product.current_stock || 0,
        stock: product.current_stock || 0,
        min_stock: product.min_stock || 10,
        barcode: product.barcode || `BAR${product.id}${Date.now().toString().slice(-4)}`,
        price: parseFloat(product.sale_price || product.price || 0),
        sale_price: product.sale_price || product.price || 0,
        image_url: product.image_url || product.image || null,
        images: product.images || [],
        total_sold: parseInt(product.total_sold || 0),
        total_revenue: parseFloat(product.total_revenue || 0)
      }

      const isVariable = baseProduct.type === 'variable'
      const variants = product.variants || []

      if (isVariable && variants.length > 0) {
        // Expandir cada variante como fila independiente
        for (const variant of variants) {
          const optsSummary = typeof variant.options_summary === 'string'
            ? JSON.parse(variant.options_summary)
            : (variant.options_summary || [])
          // Limpiar opciones: convertir hex colors a nombres legibles
          const cleanedOptions = optsSummary.map(o => ({
            name: o.name,
            value: o.value
          }))
          const displayValue = (o) => {
            if (o.name.toLowerCase() === 'color' && String(o.value).startsWith('#')) {
              return hexToColorName(o.value)
            }
            return o.value
          }
          const variantLabel = cleanedOptions.map(o => displayValue(o)).join(' / ')
          const variantDetailLabel = cleanedOptions.map(o => `${o.name}: ${displayValue(o)}`).join(' · ')

          expandedProducts.push({
            ...baseProduct,
            _rowKey: `${product.id}-v${variant.id}`,
            _isVariantRow: true,
            _parentProductId: product.id,
            _variantId: variant.id,
            _variantData: variant,
            _variantWarehouses: variant.warehouses || [],
            _variantLabel: variantLabel,
            _variantDetailLabel: variantDetailLabel,
            _variantOptions: cleanedOptions,
            _parentName: baseProduct.name,
            name: baseProduct.name,
            current_stock: variant.stock || 0,
            stock: variant.stock || 0,
            sale_price: variant.price || baseProduct.sale_price,
            price: parseFloat(variant.price || baseProduct.sale_price || 0),
            barcode: variant.sku || variant.barcode || baseProduct.barcode,
            total_sold: parseInt(variant.total_sold || 0),
            total_revenue: parseFloat(variant.total_revenue || 0)
          })
        }
      } else {
        // Producto simple: mostrar tal cual
        expandedProducts.push({
          ...baseProduct,
          _rowKey: `p${product.id}`,
          _isVariantRow: false,
          _parentProductId: product.id,
          _variantId: null,
          _variantData: null,
          _variantLabel: null,
          _variantDetailLabel: null,
          _variantOptions: null,
          _parentName: baseProduct.name
        })
      }
    }
    products.value = expandedProducts
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
      
    } else {
      throw new Error('Formato de respuesta inesperado')
    }
    
  } catch (error) {
    console.error('Error cargando movimientos:', error)
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
  // Determinar si estamos trabajando con una fila de variante expandida
  const isVariantRow = product._isVariantRow
  const parentProductId = product._parentProductId || product.id
  
  // Crear un objeto "padre" para el modal
  const parentForModal = {
    ...product,
    id: parentProductId,
    name: product._parentName || product.name,
    current_stock: product.current_stock || 0
  }
  
  selectedProductForAdjust.value = parentForModal
  productVariants.value = []
  selectedVariant.value = null
  
  // Configurar sedes disponibles para el ajuste
  adjustModalWarehouses.value = warehouses.value.filter(w => w.active !== false)
  if (adjustModalWarehouses.value.length > 1) {
    // Con múltiples sedes, forzar selección manual — no pre-seleccionar para evitar errores
    adjustModalSelectedWarehouse.value = null
  } else if (adjustModalWarehouses.value.length === 1) {
    adjustModalSelectedWarehouse.value = adjustModalWarehouses.value[0].id
  } else {
    adjustModalSelectedWarehouse.value = null
  }
  
  adjustForm.value = {
    product_id: parentProductId,
    variant_id: null,
    new_stock: '',
    reason: '',
    customReason: '',
    adjustmentType: '',
    errors: {
      new_stock: '',
      reason: '',
      variant: ''
    }
  }
  
  // Si el producto es tipo "variable" (fashion), cargar variantes con stock de la sede seleccionada
  const isVariable = product.type === 'variable' || product.product_type === 'variable'
  if (isVariable) {
    try {
      const whParam = adjustModalSelectedWarehouse.value ? `?warehouse_id=${adjustModalSelectedWarehouse.value}` : ''
      const response = await api.get(`/products/${parentProductId}${whParam}`)
      if (response.data && response.data.variants) {
        productVariants.value = response.data.variants
      }
      // Actualizar datos del producto con la respuesta completa (incluye warehouses)
      if (response.data) {
        const fresh = { ...response.data }
        if (fresh.category && typeof fresh.category === 'object') {
          fresh.category = fresh.category.name || 'Sin categoría'
        }
        selectedProductForAdjust.value = { ...parentForModal, ...fresh }
      }
    } catch (error) {
      showWarning('Error al cargar las variantes del producto')
    }
  } else {
    // Para productos simples, cargar datos frescos con info de warehouses
    try {
      const response = await api.get(`/products/${parentProductId}`)
      if (response.data) {
        const fresh = { ...response.data }
        if (fresh.category && typeof fresh.category === 'object') {
          fresh.category = fresh.category.name || 'Sin categoría'
        }
        selectedProductForAdjust.value = { ...parentForModal, ...fresh }
      }
    } catch (error) {
      // Usar los datos que ya tenemos
    }
  }
  
  showAdjustModal.value = true
}

// Navegar al módulo de gestión de stock (Bodegas) filtrando por este producto
const navigateToStockDistribution = (product) => {
  showAdjustModal.value = false
  emit('navigate', 'warehouses')
  emit('changeModule', 'warehouses')
}

// Recargar variantes cuando cambia la sede en el modal de ajuste
const onAdjustWarehouseChange = async () => {
  if (!selectedProductForAdjust.value) return
  const productId = selectedProductForAdjust.value.id
  const whId = adjustModalSelectedWarehouse.value
  
  // Resetear selección de variante y stock
  selectedVariant.value = null
  adjustForm.value.variant_id = null
  adjustForm.value.new_stock = ''
  
  const isVariable = selectedProductForAdjust.value.type === 'variable' || selectedProductForAdjust.value.product_type === 'variable'
  try {
    const whParam = whId ? `?warehouse_id=${whId}` : ''
    const response = await api.get(`/products/${productId}${whParam}`)
    if (response.data) {
      if (isVariable && response.data.variants) {
        productVariants.value = response.data.variants
      }
      selectedProductForAdjust.value = { ...selectedProductForAdjust.value, ...response.data }
    }
  } catch (error) {
    // Mantener datos actuales
  }
}

// Función para parsear options_summary de variante a array de objetos
const parseVariantOptions = (optsSummary) => {
  try {
    const opts = typeof optsSummary === 'string' ? JSON.parse(optsSummary) : optsSummary
    if (!Array.isArray(opts)) return []
    return opts.map(o => ({ name: o.name || '', value: o.value || '' }))
  } catch { return [] }
}

// Función para formatear label de variante sin hex codes
const formatVariantLabel = (variant) => {
  try {
    const opts = parseVariantOptions(variant.options_summary)
    if (opts.length === 0) return variant.name || 'Variante'
    return opts.map(opt => {
      if (opt.name.toLowerCase() === 'color' && String(opt.value).startsWith('#')) {
        return `${opt.name}: ●`
      }
      return `${opt.name}: ${opt.value}`
    }).join(' / ')
  } catch { return variant.name || 'Variante' }
}

// Función para seleccionar una variante
const selectVariant = (variant) => {
  selectedVariant.value = variant
  adjustForm.value.variant_id = variant.id
  adjustForm.value.new_stock = '' // Resetear el input cuando se cambia de variante
  adjustForm.value.errors.variant = ''
}

// Nueva función para procesar el ajuste con la API
const processStockAdjustment = async () => {
  try {
    // Validar que se haya seleccionado variante si es producto fashion
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
    // Si es producto con variantes, usar el stock de la variante seleccionada
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
    
    // Incluir warehouse_id y variant_id si está disponible
    const warehouseForAdjust = adjustModalSelectedWarehouse.value || selectedWarehouse.value
    const response = await inventoryService.adjustStock(
      adjustForm.value.product_id, 
      newStock, 
      finalReason,
      warehouseForAdjust,
      adjustForm.value.variant_id // NUEVO: pasar variant_id
    )
    
    if (response && response.success) {
      // ⏱️ Pequeño delay para asegurar que el backend termine de actualizar
      await new Promise(resolve => setTimeout(resolve, 100))
      
      // RECARGAR PRODUCTOS PARA ACTUALIZAR LA VISTA LOCAL
      await loadProducts()
      
      // FORZAR ACTUALIZACIÓN EN EL STORE GLOBAL (para POS)
      await appStore.loadProducts(selectedWarehouse.value, 'general', true)
      
      // EMITIR EVENTO GLOBAL para que ProductsView recargue
      window.dispatchEvent(new CustomEvent('products-updated', { 
        detail: { 
          source: 'inventory-adjustment',
          productId: adjustForm.value.product_id,
          newStock: newStock
        } 
      }))
      
      // ACTUALIZAR NOTIFICACIONES AUTOMÁTICAMENTE
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
  // Mantener datos del producto, incluyendo info de variante
  const productForHistory = {
    ...product,
    id: product._parentProductId || product.id,
    name: product._isVariantRow 
      ? `${product._parentName} — ${product._variantLabel}` 
      : product.name
  }
  selectedProductForHistory.value = productForHistory
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
    
    // Llamar a la API real usando el inventoryStore
    const response = await inventoryStore.getMovements(filters)
    
    if (response && response.data) {
      // La API retorna datos paginados, extraemos los items
      const movements = response.data.data || response.data
      productMovements.value = movements
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
    
    const response = await inventoryService.createMovement(movementData)
    
    if (response && response.success) {
      // Recargar productos para obtener datos actualizados
      await loadProducts()
      
      // ACTUALIZAR NOTIFICACIONES AUTOMÁTICAMENTE
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
      showSuccess('Movimiento registrado exitosamente')
    } else {
      const errorMessage = response?.message || 'Error desconocido al registrar el movimiento'
      showError('Error: ' + errorMessage)
    }
    
  } catch (error) {
    console.error('Error registrando movimiento:', error)
    const errorMessage = error?.response?.data?.message || error?.message || 'Error de conexión con el servidor'
    showError('Error al conectar con el servidor: ' + errorMessage)
  } finally {
    loading.value = false
  }
}

const generateReport = () => {
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

// NUEVO: Cargar warehouses según plan del tenant
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
      // Por defecto mostrar "Todas" las sedes para vista completa
      selectedWarehouse.value = null
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
    await loadProducts()
    await loadCategories()
  } catch (error) {
    console.error('Error refrescando datos:', error)
    showError('Error al actualizar los datos')
  } finally {
    loading.value = false
  }
}

// Convertir código hex a nombre de color legible
const hexToColorName = (hex) => {
  const colors = {
    '#FF0000': 'Rojo', '#DC2626': 'Rojo', '#EF4444': 'Rojo', '#DB0A0A': 'Rojo',
    '#B91C1C': 'Rojo Oscuro', '#7F1D1D': 'Vino',
    '#FF4500': 'Naranja Rojo', '#FF6600': 'Naranja', '#F97316': 'Naranja', '#EA580C': 'Naranja',
    '#FF8C00': 'Naranja Oscuro', '#FFA500': 'Naranja',
    '#FFD700': 'Dorado', '#FFFF00': 'Amarillo', '#EAB308': 'Amarillo', '#FACC15': 'Amarillo',
    '#FDE047': 'Amarillo Claro',
    '#00FF00': 'Verde', '#008000': 'Verde', '#22C55E': 'Verde', '#16A34A': 'Verde',
    '#4ADE80': 'Verde Claro', '#166534': 'Verde Oscuro', '#14532D': 'Verde Bosque',
    '#10B981': 'Esmeralda', '#059669': 'Esmeralda',
    '#00FFFF': 'Cyan', '#06B6D4': 'Cyan', '#0891B2': 'Cyan',
    '#0000FF': 'Azul', '#2563EB': 'Azul', '#3B82F6': 'Azul', '#1D4ED8': 'Azul',
    '#60A5FA': 'Azul Claro', '#1E3A8A': 'Azul Oscuro', '#000080': 'Azul Marino',
    '#6366F1': 'Índigo', '#4F46E5': 'Índigo',
    '#8B5CF6': 'Violeta', '#7C3AED': 'Violeta', '#A855F7': 'Púrpura', '#9333EA': 'Púrpura',
    '#800080': 'Morado',
    '#EC4899': 'Rosa', '#DB2777': 'Rosa', '#F472B6': 'Rosa Claro',
    '#FF69B4': 'Rosa', '#FF1493': 'Rosa Fuerte', '#FFC0CB': 'Rosa Pastel',
    '#FFFFFF': 'Blanco', '#F5F5F5': 'Blanco Humo', '#FAFAFA': 'Blanco',
    '#000000': 'Negro', '#1A1A1A': 'Negro', '#171717': 'Negro',
    '#808080': 'Gris', '#6B7280': 'Gris', '#9CA3AF': 'Gris Claro', '#4B5563': 'Gris Oscuro',
    '#D4D4D4': 'Gris Claro', '#374151': 'Gris Oscuro',
    '#A52A2A': 'Marrón', '#92400E': 'Marrón', '#78350F': 'Marrón Oscuro',
    '#D2691E': 'Caramelo', '#8B4513': 'Café',
    '#F5F5DC': 'Beige', '#FEF3C7': 'Crema', '#FFFDD0': 'Crema',
  }
  const upper = hex.toUpperCase()
  if (colors[upper]) return colors[upper]
  // Aproximar por distancia RGB
  const r = parseInt(upper.slice(1,3), 16), g = parseInt(upper.slice(3,5), 16), b = parseInt(upper.slice(5,7), 16)
  if (r > 200 && g < 80 && b < 80) return 'Rojo'
  if (r > 200 && g > 100 && g < 180 && b < 80) return 'Naranja'
  if (r > 200 && g > 200 && b < 100) return 'Amarillo'
  if (r < 80 && g > 150 && b < 80) return 'Verde'
  if (r < 80 && g > 150 && b > 150) return 'Cyan'
  if (r < 80 && g < 80 && b > 150) return 'Azul'
  if (r > 100 && r < 180 && g < 80 && b > 150) return 'Púrpura'
  if (r > 180 && g < 100 && b > 150) return 'Rosa'
  if (r > 220 && g > 220 && b > 220) return 'Blanco'
  if (r < 50 && g < 50 && b < 50) return 'Negro'
  if (Math.abs(r - g) < 30 && Math.abs(g - b) < 30) return 'Gris'
  if (r > 120 && g < 100 && b < 60) return 'Marrón'
  return hex // fallback: mostrar hex si no se reconoce
}

// Obtener stock de un producto en una sede específica (para modo matriz)
const getProductWarehouseStock = (product, warehouseId) => {
  // Para filas de variante, usar los warehouses de la variante
  if (product._isVariantRow) {
    const variantWarehouses = product._variantWarehouses || []
    if (variantWarehouses.length > 0) {
      const wh = variantWarehouses.find(w => w.id === warehouseId || w.warehouse_id === warehouseId)
      if (wh) return wh.pivot?.stock ?? wh.stock ?? 0
    }
    return null
  }
  // Para productos simples, usar los warehouses del producto
  if (product.warehouses && product.warehouses.length > 0) {
    const wh = product.warehouses.find(w => w.id === warehouseId || w.warehouse_id === warehouseId)
    if (wh) {
      const pivotStock = wh.pivot?.stock ?? wh.stock ?? 0
      if (pivotStock === 0 && product.current_stock > 0 && product.warehouses.length === 1) {
        return product.current_stock
      }
      return pivotStock
    }
    return null
  }
  if (warehouseId === 1) return product.current_stock || 0
  return null
}

// Función utilitaria para manejo inteligente de imágenes (copiada de ProductsView)
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

// Generar avatar dinámico SVG con iniciales del producto (copiado de ProductsView)
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

// Manejar errores de carga de imagen (copiado de ProductsView)
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
    
    // Cargar warehouses PRIMERO (necesario para filtrar variantes por sede)
    // Luego cargar el resto en paralelo
    await loadWarehousesIfAvailable()
    
    await Promise.all([
      loadProducts(),
      loadCategories(),
      loadMovementsData()
    ])
    
    // Inicializar notificaciones (puede ser secuencial - no crítico para UI)
    await notificationStore.initializeLastVisited()
    await notificationStore.loadNotifications()
    notificationStore.startPolling(15000)
    
    // Inicializar contexto para IA
    setTimeout(() => {
      updateScreenContextForAI()
    }, 500)
  } catch (error) {
    console.error('Error en mounted:', error)
  } finally {
    loading.value = false
  }
})

// AUTO-REFRESH al reactivar el componente
onActivated(async () => {
  // Recarga en paralelo de datos críticos
  try {
    await Promise.all([
      loadProducts(),
      loadMovementsData()
    ])
  } catch (error) {
    console.error('Error en auto-refresh de inventario:', error)
  }
})

// LIMPIAR POLLING AL DESMONTAR COMPONENTE
onUnmounted(() => {
  notificationStore.stopPolling()
})

// Watcher para cargar datos cuando se cambia de tab
watch(activeTab, async (newTab) => {
  if (newTab === 'movements') {
    await loadMovementsData()
    await notificationStore.markMovementsAsViewed()
  } else if (newTab === 'alerts') {
    alertsHaveBeenViewed.value = true
    lastSeenAlertsCount.value = stockAlerts.value?.length || 0
    await notificationStore.markAlertsAsViewed()
  }
  // Actualizar contexto para IA cuando cambia el tab
  updateScreenContextForAI()
})

// CONCIENCIA DE PANTALLA PARA IA
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
  
  // ACCIÓN: Editar campo de producto (principalmente stock) desde inventario
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