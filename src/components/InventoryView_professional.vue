<template>
  <div class="min-h-screen font-sans mx-8" style="background-color: #F4F6F8;">
    <div class="p-4 lg:p-6 space-y-6 pb-8 animate-fade-in">
      
      <!-- Header Simple y Elegante -->
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-6 border-b border-slate-200">
        <div class="flex items-center space-x-4">
          <div class="w-12 h-12 bg-slate-900 rounded-xl flex items-center justify-center shadow-lg shadow-slate-900/20">
            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
            </svg>
          </div>
          <div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">Control de Inventario</h1>
            <p class="text-sm text-slate-500 font-medium">Gestiona el stock y movimientos de productos</p>
          </div>
        </div>
        
        <div class="flex items-center space-x-3">
          <!-- Botón Neutro (Actualizar) -->
          <button @click="refreshInventoryData" 
                  :disabled="loading"
                  class="px-4 py-2.5 bg-white hover:bg-slate-50 text-slate-600 text-sm font-bold rounded-xl border border-slate-200 shadow-sm transition-all duration-200 flex items-center space-x-2 hover:border-slate-300"
                  :class="{ 'opacity-50 cursor-not-allowed': loading }">
            <svg class="w-4 h-4" :class="{'animate-spin': loading}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
            </svg>
            <span>{{ loading ? 'Actualizando' : 'Actualizar' }}</span>
          </button>
          
          <!-- Botón Principal (Nuevo Movimiento) -->
          <button @click="openMovementModal"
                  class="px-5 py-2.5 bg-slate-900 hover:bg-black text-white text-sm font-bold rounded-xl shadow-lg shadow-slate-900/20 transition-all duration-300 transform hover:scale-105 flex items-center space-x-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
            </svg>
            <span>Nuevo Movimiento</span>
          </button>
        </div>
      </div>
      
      <!-- Métricas Principales -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <!-- Productos en Stock -->
        <div class="bg-white rounded-xl p-4 border border-slate-100 shadow-sm hover:shadow-md transition-all duration-200 group">
          <div class="flex items-center justify-between mb-3">
            <div class="w-10 h-10 bg-blue-50 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform duration-200">
              <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
              </svg>
            </div>
            <span class="text-[10px] font-bold text-blue-700 bg-blue-50 px-2 py-1 rounded-full uppercase tracking-wide">Total</span>
          </div>
          <div>
            <p class="text-2xl font-black text-slate-900 mb-0.5">{{ totalProductsInStock }}</p>
            <p class="text-xs text-slate-500 font-medium">Productos en stock</p>
          </div>
        </div>

        <!-- Stock Bajo -->
        <div class="bg-white rounded-xl p-4 border border-slate-100 shadow-sm hover:shadow-md transition-all duration-200 group">
          <div class="flex items-center justify-between mb-3">
            <div class="w-10 h-10 bg-rose-50 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform duration-200">
              <svg class="w-5 h-5 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
              </svg>
            </div>
            <span class="text-[10px] font-bold text-rose-700 bg-rose-50 px-2 py-1 rounded-full uppercase tracking-wide">Alerta</span>
          </div>
          <div>
            <p class="text-2xl font-black text-slate-900 mb-0.5">{{ lowStockProducts }}</p>
            <p class="text-xs text-slate-500 font-medium">Requieren reposición</p>
          </div>
        </div>

        <!-- Valor Inventario -->
        <div class="bg-white rounded-xl p-4 border border-slate-100 shadow-sm hover:shadow-md transition-all duration-200 group">
          <div class="flex items-center justify-between mb-3">
            <div class="w-10 h-10 bg-emerald-50 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform duration-200">
              <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
              </svg>
            </div>
            <span class="text-[10px] font-bold text-emerald-700 bg-emerald-50 px-2 py-1 rounded-full uppercase tracking-wide">Valor</span>
          </div>
          <div>
            <p class="text-2xl font-black text-slate-900 mb-0.5">${{ totalInventoryValue.toLocaleString() }}</p>
            <p class="text-xs text-slate-500 font-medium">Valor total inventario</p>
          </div>
        </div>

        <!-- Movimientos Hoy -->
        <div class="bg-white rounded-xl p-4 border border-slate-100 shadow-sm hover:shadow-md transition-all duration-200 group">
          <div class="flex items-center justify-between mb-3">
            <div class="w-10 h-10 bg-amber-50 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform duration-200">
              <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
              </svg>
            </div>
            <span class="text-[10px] font-bold text-amber-700 bg-amber-50 px-2 py-1 rounded-full uppercase tracking-wide">Hoy</span>
          </div>
          <div>
            <p class="text-2xl font-black text-slate-900 mb-0.5">{{ todayMovements }}</p>
            <p class="text-xs text-slate-500 font-medium">Entradas y salidas</p>
          </div>
        </div>
      </div>
      
      <!-- Contenedor Unificado: Pestañas + Contenido -->
      <div class="bg-white rounded-xl shadow-lg border border-slate-200 overflow-hidden mb-8">
        <div class="border-b border-slate-200">
          <nav class="flex space-x-8 px-6 py-1">
            <button @click="activeTab = 'stock'" 
                    :class="[
                      'py-4 px-1 border-b-2 font-semibold text-sm transition-colors',
                      activeTab === 'stock' 
                        ? 'border-slate-900 text-slate-900' 
                        : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300'
                    ]">
              Stock Actual
            </button>
            <button @click="activeTab = 'movements'" 
                    :class="[
                      'py-4 px-1 border-b-2 font-semibold text-sm relative transition-colors',
                      activeTab === 'movements' 
                        ? 'border-slate-900 text-slate-900' 
                        : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300'
                    ]">
              Movimientos
              <span v-if="unreadMovementsCount > 0" 
                    class="absolute -top-1 -right-1 inline-flex items-center justify-center px-2 py-0.5 text-xs font-bold leading-none text-white bg-rose-500 rounded-full">
                {{ unreadMovementsCount }}
              </span>
            </button>
            <button @click="activeTab = 'alerts'" 
                    :class="[
                      'py-4 px-1 border-b-2 font-semibold text-sm relative transition-colors',
                      activeTab === 'alerts' 
                        ? 'border-slate-900 text-slate-900' 
                        : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300'
                    ]">
              Alertas
              <span v-if="unreadAlertsCount > 0" 
                    class="absolute -top-1 -right-1 inline-flex items-center justify-center px-2 py-0.5 text-xs font-bold leading-none text-white bg-rose-500 rounded-full">
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
            <div class="p-4 border-b border-slate-200 bg-slate-50/50">
              <div class="flex flex-wrap items-center gap-3">
                <div class="flex-1 min-w-48 relative">
                  <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                  </svg>
                  <input v-model="searchTerm" 
                         type="text" 
                         placeholder="Buscar productos..." 
                         class="w-full pl-9 pr-3 py-2 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-slate-900 focus:border-transparent transition-all shadow-sm">
                </div>
                
                <select v-model="categoryFilter" 
                        class="pl-3 pr-8 py-2 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-slate-900 focus:border-transparent min-w-36 shadow-sm bg-white">
                  <option value="">Todas las categorías</option>
                  <option v-for="category in categories" :key="category.id" :value="category.name">
                    {{ category.name }}
                  </option>
                </select>
                
                <select v-model="stockFilter" 
                        class="pl-3 pr-8 py-2 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-slate-900 focus:border-transparent min-w-36 shadow-sm bg-white">
                  <option value="">Todo el stock</option>
                  <option value="low">Stock bajo</option>
                  <option value="normal">Stock normal</option>
                  <option value="high">Stock alto</option>
                </select>
                
                <button @click="searchTerm = ''; categoryFilter = ''; stockFilter = ''" 
                        class="p-2 text-slate-500 hover:text-slate-700 hover:bg-slate-200 rounded-lg transition-colors border border-transparent hover:border-slate-300"
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
            <table class="min-w-full divide-y divide-slate-200">
              <thead class="bg-slate-50">
                <tr>
                  <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Producto</th>
                  <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Categoría</th>
                  <th class="px-6 py-4 text-center text-xs font-bold text-slate-500 uppercase tracking-wider">Stock</th>
                  <th class="px-6 py-4 text-center text-xs font-bold text-slate-500 uppercase tracking-wider">Ventas</th>
                  <th class="px-6 py-4 text-right text-xs font-bold text-slate-500 uppercase tracking-wider">Ingresos</th>
                  <th class="px-6 py-4 text-center text-xs font-bold text-slate-500 uppercase tracking-wider">Estado</th>
                  <th class="px-6 py-4 text-center text-xs font-bold text-slate-500 uppercase tracking-wider">Acciones</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-slate-200 bg-white">
                <tr v-for="product in paginatedProducts" :key="product.id" class="hover:bg-slate-50 transition-colors group">
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center">
                      <!-- Icono de producto mejorado -->
                      <div class="w-10 h-10 rounded-lg flex items-center justify-center mr-4 shadow-sm border border-slate-100" :style="{backgroundColor: `hsl(${product.id * 137.5 % 360}, 70%, 95%)`}">
                        <span class="text-lg font-bold" :style="{color: `hsl(${product.id * 137.5 % 360}, 70%, 45%)`}">
                          {{ product.name.charAt(0) }}
                        </span>
                      </div>
                      <div>
                        <p class="text-sm font-bold text-slate-900">{{ product.name }}</p>
                        <p class="text-xs text-slate-500 font-medium">{{ product.barcode || 'Sin código' }}</p>
                      </div>
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-800">
                      {{ product.category }}
                    </span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-center">
                    <div class="flex flex-col items-center">
                      <span class="text-sm font-black text-slate-900">{{ product.current_stock || 0 }}</span>
                      <span class="text-[10px] text-slate-500">Min: {{ product.min_stock || 10 }}</span>
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-center">
                    <div class="flex flex-col items-center">
                      <span class="text-sm font-bold text-slate-700">{{ product.total_sold || 0 }}</span>
                      <span class="text-[10px] text-slate-400">unidades</span>
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-right">
                    <div class="flex flex-col items-end">
                      <span class="text-sm font-black text-emerald-600">${{ parseFloat(product.total_revenue || 0).toLocaleString() }}</span>
                      <span class="text-[10px] text-slate-400">Precio: ${{ parseFloat(product.sale_price || 0).toLocaleString() }}</span>
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-center">
                    <span :class="[
                      'inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wide border',
                      getStockStatusClass(product.current_stock, product.min_stock)
                    ]">
                      {{ getStockStatusLabel(product.current_stock, product.min_stock) }}
                    </span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-center">
                    <div class="flex items-center justify-center gap-2">
                      <button @click="adjustStock(product)" 
                              class="p-2.5 text-slate-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-all"
                              title="Ajustar Stock">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                      </button>
                      <button @click="viewMovements(product)" 
                              class="p-2.5 text-slate-400 hover:text-slate-900 hover:bg-slate-100 rounded-lg transition-all"
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
          
          <!-- Componente de Paginación -->
          <div v-if="filteredProducts.length > 0" class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 mt-6">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between space-y-4 lg:space-y-0">
              
              <!-- Información de páginas -->
              <div class="flex items-center space-x-4">
                <div class="text-sm text-gray-600">
                  Mostrando <span class="font-semibold">{{ (currentPage - 1) * itemsPerPage + 1 }}</span> a 
                  <span class="font-semibold">{{ Math.min(currentPage * itemsPerPage, filteredProducts.length) }}</span> 
                  de <span class="font-semibold">{{ filteredProducts.length }}</span> productos
                </div>
                
                <!-- Selector de elementos por página -->
                <div class="flex items-center space-x-2">
                  <label class="text-sm text-gray-600">Por página:</label>
                  <select v-model="itemsPerPage" @change="changeItemsPerPage(itemsPerPage)"
                          class="border border-gray-300 rounded-lg px-3 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="10">10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                  </select>
                </div>
              </div>

              <!-- Controles de navegación -->
              <div v-if="totalPages > 1" class="flex items-center space-x-2">
                <!-- Botón Anterior -->
                <button @click="prevPage" 
                        :disabled="currentPage === 1"
                        :class="[
                          'px-3 py-2 rounded-lg font-medium transition-all flex items-center space-x-1',
                          currentPage === 1 
                            ? 'bg-gray-100 text-gray-400 cursor-not-allowed' 
                            : 'bg-gray-200 hover:bg-gray-300 text-gray-700'
                        ]">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                  </svg>
                  <span>Anterior</span>
                </button>

                <!-- Números de página -->
                <div class="flex items-center space-x-1">
                  <template v-for="page in Math.min(totalPages, 5)" :key="page">
                    <button @click="goToPage(page)"
                            :class="[
                              'w-10 h-10 rounded-lg font-medium transition-all',
                              currentPage === page 
                                ? 'bg-blue-600 text-white' 
                                : 'bg-gray-100 hover:bg-gray-200 text-gray-700'
                            ]">
                      {{ page }}
                    </button>
                  </template>
                  
                  <!-- Dots si hay más páginas -->
                  <span v-if="totalPages > 5" class="px-2 text-gray-500">...</span>
                  
                  <!-- Última página si hay más de 5 -->
                  <button v-if="totalPages > 5" @click="goToPage(totalPages)"
                          :class="[
                            'w-10 h-10 rounded-lg font-medium transition-all',
                            currentPage === totalPages 
                              ? 'bg-blue-600 text-white' 
                              : 'bg-gray-100 hover:bg-gray-200 text-gray-700'
                          ]">
                    {{ totalPages }}
                  </button>
                </div>

                <!-- Botón Siguiente -->
                <button @click="nextPage" 
                        :disabled="currentPage === totalPages"
                        :class="[
                          'px-3 py-2 rounded-lg font-medium transition-all flex items-center space-x-1',
                          currentPage === totalPages 
                            ? 'bg-gray-100 text-gray-400 cursor-not-allowed' 
                            : 'bg-gray-200 hover:bg-gray-300 text-gray-700'
                        ]">
                  <span>Siguiente</span>
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                  </svg>
                </button>
              </div>
            </div>
          </div>
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
          <!-- Métricas de Resumen -->
          <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <!-- Total Alertas -->
            <div class="bg-white rounded-xl p-5 border border-slate-200 shadow-sm">
              <div class="flex items-center justify-between">
                <div>
                  <p class="text-xs font-medium text-slate-500 uppercase tracking-wide mb-1">Total Alertas</p>
                  <p class="text-2xl font-bold text-slate-900">{{ stockAlerts.length }}</p>
                </div>
                <div class="w-10 h-10 bg-slate-50 rounded-lg flex items-center justify-center">
                  <svg class="w-5 h-5 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                  </svg>
                </div>
              </div>
            </div>
            
            <!-- Stock Agotado -->
            <div class="bg-white rounded-xl p-5 border border-slate-200 shadow-sm">
              <div class="flex items-center justify-between">
                <div>
                  <p class="text-xs font-medium text-slate-500 uppercase tracking-wide mb-1">Agotado</p>
                  <p class="text-2xl font-bold text-red-600">{{ stockAlerts.filter(a => a.stock === 0).length }}</p>
                </div>
                <div class="w-10 h-10 bg-red-50 rounded-lg flex items-center justify-center">
                  <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                  </svg>
                </div>
              </div>
            </div>
            
            <!-- Stock Crítico -->
            <div class="bg-white rounded-xl p-5 border border-slate-200 shadow-sm">
              <div class="flex items-center justify-between">
                <div>
                  <p class="text-xs font-medium text-slate-500 uppercase tracking-wide mb-1">Crítico</p>
                  <p class="text-2xl font-bold text-orange-600">{{ stockAlerts.filter(a => a.stock > 0 && a.stock < a.min_stock / 2).length }}</p>
                </div>
                <div class="w-10 h-10 bg-orange-50 rounded-lg flex items-center justify-center">
                  <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                  </svg>
                </div>
              </div>
            </div>
            
            <!-- Stock Bajo -->
            <div class="bg-white rounded-xl p-5 border border-slate-200 shadow-sm">
              <div class="flex items-center justify-between">
                <div>
                  <p class="text-xs font-medium text-slate-500 uppercase tracking-wide mb-1">Bajo</p>
                  <p class="text-2xl font-bold text-amber-600">{{ stockAlerts.filter(a => a.stock >= a.min_stock / 2 && a.stock <= a.min_stock).length }}</p>
                </div>
                <div class="w-10 h-10 bg-amber-50 rounded-lg flex items-center justify-center">
                  <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"></path>
                  </svg>
                </div>
              </div>
            </div>
          </div>

          <!-- Lista de Alertas -->
          <div class="space-y-3">
            <div v-if="stockAlerts.length === 0" class="text-center py-12 bg-white rounded-xl border border-slate-200 shadow-sm">
              <div class="w-16 h-16 bg-green-50 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
              </div>
              <p class="text-lg font-semibold text-slate-900 mb-1">Sin alertas pendientes</p>
              <p class="text-sm text-slate-500">Todos los productos tienen stock adecuado</p>
            </div>

            <div v-for="alert in stockAlerts" :key="alert.id" 
                 class="bg-white rounded-xl p-5 border border-slate-200 shadow-sm hover:shadow-md transition-shadow">
              <div class="flex items-start justify-between">
                <div class="flex items-start space-x-4 flex-1">
                  <!-- Icono de producto -->
                  <div class="w-12 h-12 bg-slate-100 rounded-lg flex items-center justify-center flex-shrink-0">
                    <span class="text-lg font-bold text-slate-600">{{ alert.name.charAt(0) }}</span>
                  </div>
                  
                  <!-- Información del producto -->
                  <div class="flex-1 min-w-0">
                    <div class="flex items-center space-x-3 mb-2">
                      <h4 class="text-base font-semibold text-slate-900">{{ alert.name }}</h4>
                      <span v-if="alert.stock === 0" 
                            class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-700">
                        Agotado
                      </span>
                      <span v-else-if="alert.stock < alert.min_stock / 2" 
                            class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-orange-100 text-orange-700">
                        Crítico
                      </span>
                      <span v-else 
                            class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-amber-100 text-amber-700">
                        Bajo
                      </span>
                    </div>
                    
                    <div class="flex items-center space-x-6 text-sm">
                      <div class="flex items-center space-x-2">
                        <span class="text-slate-500">Stock:</span>
                        <span :class="[
                          'font-semibold',
                          alert.stock === 0 ? 'text-red-600' : alert.stock < alert.min_stock / 2 ? 'text-orange-600' : 'text-amber-600'
                        ]">{{ alert.stock }}</span>
                        <span class="text-slate-400">/</span>
                        <span class="text-slate-600">{{ alert.min_stock }}</span>
                      </div>
                      
                      <div class="flex items-center space-x-2">
                        <span class="text-slate-500">Categoría:</span>
                        <span class="text-slate-700">{{ alert.category }}</span>
                      </div>
                      
                      <div v-if="alert.stock > 0" class="flex items-center space-x-2">
                        <span class="text-slate-500">Falta:</span>
                        <span class="font-medium text-slate-700">{{ Math.max(0, alert.min_stock - alert.stock) }}</span>
                      </div>
                    </div>
                    
                    <!-- Barra de progreso -->
                    <div class="mt-3 w-full bg-slate-100 h-1.5 rounded-full overflow-hidden">
                      <div 
                        :class="[
                          'h-full',
                          alert.stock === 0 ? 'bg-red-500' : alert.stock < alert.min_stock / 2 ? 'bg-orange-500' : 'bg-amber-500'
                        ]"
                        :style="{ width: `${Math.min(100, (alert.stock / alert.min_stock) * 100)}%` }"
                      ></div>
                    </div>
                  </div>
                </div>
                
                <!-- Botón de acción -->
                <div class="ml-4">
                  <button @click="adjustStock(alert)" 
                          class="px-4 py-2 bg-slate-900 hover:bg-black text-white text-sm font-medium rounded-lg transition-colors flex items-center space-x-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    <span>Reabastecer</span>
                  </button>
                </div>
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
                  <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                    <span class="text-sm font-bold text-green-700">{{ selectedProduct.name.charAt(0) }}</span>
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
                <div class="flex items-center justify-between">
                  <div class="flex-1">
                    <p class="font-semibold text-gray-900">{{ product.name }}</p>
                    <p class="text-sm text-gray-600">{{ product.category }}</p>
                    <p class="text-xs text-gray-500">
                      {{ product.barcode || 'Sin código' }} | Stock: {{ product.current_stock || 0 }}
                    </p>
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
                  class="px-4 py-2.5 bg-white hover:bg-slate-50 text-slate-600 text-sm font-semibold rounded-lg border border-slate-300 transition-all duration-200">
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
         class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50"
         @click.self="showAdjustModal = false">
      <div class="bg-white rounded-xl p-6 max-w-lg w-full shadow-2xl border border-slate-200">
        <!-- Header del Modal -->
        <div class="flex items-center justify-between pb-4 border-b border-slate-200 mb-6">
          <div class="flex items-center space-x-3">
            <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center">
              <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
              </svg>
            </div>
            <div>
              <h3 class="text-base font-bold text-slate-900">Ajustar Stock</h3>
              <p class="text-xs text-slate-600">Modifica la cantidad en inventario</p>
            </div>
          </div>
          <button @click="showAdjustModal = false" 
                  class="text-slate-400 hover:text-slate-600 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
          </button>
        </div>
        
        <div v-if="selectedProductForAdjust" class="space-y-6">
          <!-- Información del Producto -->
          <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-2xl p-5 border border-blue-100">
            <div class="flex items-center space-x-4">
              <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center flex-shrink-0">
                <span class="text-base font-bold text-blue-600">{{ selectedProductForAdjust.name.charAt(0) }}</span>
              </div>
              <div class="flex-1">
                <h4 class="text-base font-bold text-gray-900">{{ selectedProductForAdjust.name }}</h4>
                <p class="text-sm text-gray-600">{{ selectedProductForAdjust.category }}</p>
                <div class="flex items-center space-x-4 mt-2">
                  <div class="flex items-center space-x-2">
                    <span class="text-xs font-medium text-gray-500 uppercase tracking-wide">Stock Actual:</span>
                    <span class="text-sm font-bold text-blue-700">{{ selectedProductForAdjust.current_stock || 0 }} uds</span>
                  </div>
                  <div class="flex items-center space-x-2">
                    <span class="text-xs font-medium text-gray-500 uppercase tracking-wide">Mínimo:</span>
                    <span class="text-sm font-semibold text-gray-700">{{ selectedProductForAdjust.min_stock || 10 }} uds</span>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Tipo de Ajuste -->
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-3">Tipo de Ajuste</label>
            <div class="grid grid-cols-2 gap-4">
              <button @click="setQuickAdjustment('restock')"
                      :class="['p-4 rounded-xl border-2 transition-all', 
                               adjustForm.adjustmentType === 'restock' 
                                 ? 'border-green-500 bg-green-50 text-green-700' 
                                 : 'border-gray-200 hover:border-green-300 text-gray-700']">
                <div class="flex items-center justify-center space-x-2">
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                  </svg>
                  <span class="font-semibold">Restock</span>
                </div>
              </button>
              <button @click="setQuickAdjustment('correction')"
                      :class="['p-4 rounded-xl border-2 transition-all', 
                               adjustForm.adjustmentType === 'correction' 
                                 ? 'border-orange-500 bg-orange-50 text-orange-700' 
                                 : 'border-gray-200 hover:border-orange-300 text-gray-700']">
                <div class="flex items-center justify-center space-x-2">
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                  </svg>
                  <span class="font-semibold">Corrección</span>
                </div>
              </button>
            </div>
          </div>

          <!-- Nuevo Stock -->
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">Nuevo Stock</label>
            <div class="relative">
              <input v-model="adjustForm.new_stock" 
                     type="number" 
                     min="0"
                     step="1"
                     :class="['w-full px-4 py-3 pr-12 border-2 rounded-xl transition-colors focus:outline-none',
                              adjustForm.errors.new_stock 
                                ? 'border-red-300 focus:border-red-500 bg-red-50' 
                                : 'border-gray-200 focus:border-blue-500 focus:bg-blue-50']"
                     placeholder="Ingresa la cantidad nueva"
                     @input="validateNewStock">
              <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                <span class="text-gray-400 text-sm">uds</span>
              </div>
            </div>
            <p v-if="adjustForm.errors.new_stock" class="text-red-600 text-sm mt-1 flex items-center">
              <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 15.5c-.77.833.192 2.5 1.732 2.5z"></path>
              </svg>
              {{ adjustForm.errors.new_stock }}
            </p>
          </div>
          
          <!-- Motivo -->
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">Motivo del Ajuste</label>
            <div class="space-y-2">
              <select v-model="adjustForm.reason" 
                      :class="['w-full px-4 py-3 border-2 rounded-xl transition-colors focus:outline-none',
                               adjustForm.errors.reason 
                                 ? 'border-red-300 focus:border-red-500 bg-red-50' 
                                 : 'border-gray-200 focus:border-blue-500 focus:bg-blue-50']"
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
                     class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-blue-500 focus:bg-blue-50"
                     placeholder="Especifica el motivo...">
            </div>
            <p v-if="adjustForm.errors.reason" class="text-red-600 text-sm mt-1 flex items-center">
              <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 15.5c-.77.833.192 2.5 1.732 2.5z"></path>
              </svg>
              {{ adjustForm.errors.reason }}
            </p>
          </div>

          <!-- Resumen del Cambio -->
          <div v-if="adjustForm.new_stock !== '' && !isNaN(adjustForm.new_stock)" 
               class="relative overflow-hidden rounded-xl border-2 transition-all"
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
                      {{ selectedProductForAdjust.current_stock || 0 }} → {{ adjustForm.new_stock }}
                    </p>
                  </div>
                </div>
                <div class="text-right">
                  <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold"
                        :class="stockDifference >= 0 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'">
                    {{ stockDifference >= 0 ? '📦 Entrada' : '📤 Salida' }}
                  </span>
                </div>
              </div>
            </div>
          </div>
        </div>
        
        <!-- Botones de Acción -->
        <div class="bg-slate-50 border-t border-slate-200 px-6 py-4 -mx-6 -mb-6 mt-6 flex justify-end gap-3">
          <button @click="showAdjustModal = false" 
                  class="px-4 py-2.5 bg-white hover:bg-slate-50 text-slate-600 text-sm font-semibold rounded-lg border border-slate-300 transition-all duration-200">
            Cancelar
          </button>
          <button @click="processStockAdjustment" 
                  :disabled="!isFormValid"
                  class="px-5 py-2.5 bg-black hover:bg-slate-900 disabled:bg-gray-300 disabled:cursor-not-allowed text-white rounded-lg text-sm font-bold transition-all duration-200 shadow-sm">
            {{ adjustmentLoading ? 'Procesando...' : 'Confirmar Ajuste' }}
          </button>
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
import { ref, computed, onMounted, onUnmounted, nextTick, watch } from 'vue'
import { productsService } from '../services/productsService.js'
import { categoriesService } from '../services/categoriesService.js'
import { inventoryService } from '../services/inventoryService.js'
import { inventoryStore } from '../store/inventory.js'
import { notificationStore } from '../store/notifications.js'
import { useToast } from '../composables/useToast.js'
import MovementsSection from './inventory/sections/MovementsSection.vue'

// Props del componente
const props = defineProps({
  moduleName: {
    type: String,
    default: 'inventory'
  }
})

// Eventos emitidos
const emit = defineEmits(['navigate', 'changeModule'])

// Sistema de toasts
const { showSuccess, showError, showWarning } = useToast()

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
  new_stock: '',
  reason: '',
  customReason: '',
  adjustmentType: '',
  errors: {
    new_stock: '',
    reason: ''
  }
})
const selectedProductForAdjust = ref(null)
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
  return parseInt(adjustForm.value.new_stock) - (selectedProductForAdjust.value.current_stock || 0)
})

const stockDifferenceColor = computed(() => {
  const diff = stockDifference.value
  if (diff > 0) return 'border-green-200 bg-green-50'
  if (diff < 0) return 'border-red-200 bg-red-50'
  return 'border-gray-200 bg-gray-50'
})

const stockDifferenceIconBg = computed(() => {
  const diff = stockDifference.value
  if (diff > 0) return 'bg-green-100'
  if (diff < 0) return 'bg-red-100'
  return 'bg-gray-100'
})

const stockDifferenceIconColor = computed(() => {
  const diff = stockDifference.value
  if (diff > 0) return 'text-green-600'
  if (diff < 0) return 'text-red-600'
  return 'text-gray-600'
})

const stockDifferenceTextColor = computed(() => {
  const diff = stockDifference.value
  if (diff > 0) return 'text-green-800'
  if (diff < 0) return 'text-red-800'
  return 'text-gray-800'
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

    // Usar el endpoint de inventario que incluye métricas de ventas
    const response = await inventoryService.getProducts({ per_page: 10000 })
    console.log('Respuesta inventario (todos):', response)

    // Los productos pueden venir en response.data.data (paginación) o directamente en response.data
    const productsList = response.data?.data || response.data || []

    products.value = productsList.map(product => ({
      ...product,
      category: product.category ? product.category.name : 'Sin categoría',
      current_stock: product.current_stock || product.stock || 0,
      stock: product.current_stock || product.stock || 0,
      min_stock: product.min_stock || 10,
      barcode: product.barcode || `BAR${product.id}${Date.now().toString().slice(-4)}`,
      price: parseFloat(product.sale_price || product.price || 0)
    }))

    console.log('Productos cargados (total):', products.value.length)
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
    const apiUrl = import.meta.env.VITE_API_URL || `${API_BASE_URL}`
    const response = await fetch(`${apiUrl}/api/inventory/test/movements?${params}`, {
      method: 'GET',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json'
      }
    })
    
    if (!response.ok) {
      throw new Error(`HTTP error! status: ${response.status}`)
    }
    
    const data = await response.json()
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
  adjustForm.value = {
    product_id: product.id,
    new_stock: '', // Dejar vacío para que el usuario ingrese la cantidad que desee
    reason: '',
    customReason: '',
    adjustmentType: '',
    errors: {
      new_stock: '',
      reason: ''
    }
  }
  showAdjustModal.value = true
}

// Nueva función para procesar el ajuste con la API
const processStockAdjustment = async () => {
  try {
    // Validar formulario antes de enviar
    validateNewStock()
    validateReason()
    
    if (!isFormValid.value) {
      showWarning('Por favor corrige los errores en el formulario')
      return
    }
    
    adjustmentLoading.value = true
    
    const newStock = parseInt(adjustForm.value.new_stock)
    const currentStock = selectedProductForAdjust.value.current_stock || 0
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
      from: currentStock,
      to: newStock,
      difference,
      reason: finalReason
    })
    
    const response = await inventoryService.adjustStock(
      adjustForm.value.product_id, 
      newStock, 
      finalReason
    )
    
    if (response && response.success) {
      console.log('Ajuste registrado exitosamente en la BD')
      
      // Recargar productos para obtener datos actualizados
      await loadProducts()
      
      // 🔥 ACTUALIZAR NOTIFICACIONES AUTOMÁTICAMENTE
      console.log('Actualizando notificaciones después del ajuste...')
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

// Inicialización
onMounted(async () => {
  try {
    loading.value = true
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