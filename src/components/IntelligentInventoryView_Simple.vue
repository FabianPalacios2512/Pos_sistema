<template>
  <div class="min-h-screen bg-gray-100 font-sans">
    <div class="p-4 lg:p-6 space-y-4 animate-fade-in">
      
      <!-- Header Simple y Profesional -->
      <div class="flex items-center justify-between pb-4 border-b border-gray-300">
        <div class="flex items-center space-x-4">
          <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
            </svg>
          </div>
          <div>
            <h1 class="text-xl font-bold text-gray-900">Inventario Inteligente</h1>
            <p class="text-xs text-gray-600">Sistema de análisis y gestión</p>
          </div>
        </div>
        
        <div class="flex items-center space-x-2">
          <!-- Navegación de Secciones -->
          <div class="flex items-center space-x-1 bg-white rounded-lg p-1 border border-gray-200">
            <button
              v-for="section in sections"
              :key="section.id"
              @click="switchToSection(section.id)"
              :class="[
                'px-2.5 py-1.5 text-xs font-medium rounded transition-colors',
                activeSection === section.id
                  ? 'bg-blue-600 text-white'
                  : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50'
              ]"
            >
              <i :class="section.icon" class="mr-1 text-xs"></i>
              {{ section.name }}
            </button>
          </div>

          <!-- Botón Actualizar -->
          <button 
            @click="testConnection"
            class="px-3 py-1.5 bg-white hover:bg-gray-50 text-gray-700 text-xs font-medium rounded-lg border border-gray-300 transition-colors flex items-center space-x-1.5"
          >
            <svg class="w-3.5 h-3.5" :class="{ 'animate-spin': connectionStatus.includes('funcionando') }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
            </svg>
            <span>Actualizar</span>
          </button>
        </div>
      </div>

      <!-- Contenido Principal -->
      <!-- Vista General -->
      <div v-if="activeSection === 'overview'" class="space-y-4">
        
        <!-- Filtros de Período (Compactos) -->
        <div class="bg-white rounded-lg shadow-sm p-3 border border-gray-200">
          <div class="flex items-center justify-between">
            <span class="text-xs font-semibold text-gray-700">Período:</span>
            <div class="flex items-center space-x-2">
              <select 
                v-model="selectedPeriod"
                @change="handlePeriodChange"
                class="bg-white border border-gray-300 rounded-lg px-3 py-2 text-xs focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              >
                <option value="day">Hoy</option>
                <option value="week">Esta Semana</option>
                <option value="month" selected>Este Mes</option>
                <option value="year">Este Año</option>
                <option value="custom">Personalizado</option>
              </select>
              
              <template v-if="selectedPeriod === 'custom'">
                <input
                  type="date"
                  v-model="customDateRange.start"
                  @input="loadDashboardData"
                  :max="new Date().toISOString().split('T')[0]"
                  class="bg-white border border-gray-300 rounded-lg px-3 py-2 text-xs focus:ring-2 focus:ring-blue-500"
                >
              </template>
            </div>
          </div>
        </div>

        <!-- Dashboard Principal - 4 Métricas Compactas -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-3">
          <!-- Total Productos Activos -->
          <div class="bg-white rounded-2xl p-5 border border-gray-300 hover:border-gray-400 transition-all duration-200 hover:shadow-lg">
            <div class="flex items-center space-x-4">
              <div class="w-12 h-12 bg-gray-100 rounded-xl flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                </svg>
              </div>
              <div class="flex-1 min-w-0">
                <div class="flex items-center justify-between mb-2">
                  <h3 class="text-sm font-semibold text-gray-700">Productos Activos</h3>
                  <span class="text-xs font-medium text-blue-700 bg-blue-50 px-2 py-1 rounded-lg border border-blue-200">
                    BD
                  </span>
                </div>
                <p class="text-2xl font-bold text-gray-900 mb-1">{{ metrics.activeProducts || 0 }}</p>
                <p class="text-sm text-gray-500">de {{ metrics.totalProducts || 0 }} totales</p>
              </div>
            </div>
          </div>

          <!-- Valor COSTO -->
          <div class="bg-white rounded-2xl p-5 border border-gray-300 hover:border-gray-400 transition-all duration-200 hover:shadow-lg">
            <div class="flex items-center space-x-4">
              <div class="w-12 h-12 bg-gray-100 rounded-xl flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
              </div>
              <div class="flex-1 min-w-0">
                <div class="flex items-center justify-between mb-2">
                  <h3 class="text-sm font-semibold text-gray-700">Valor Invertido</h3>
                  <span class="text-xs font-medium text-amber-700 bg-amber-50 px-2 py-1 rounded-lg border border-amber-200">
                    Costo
                  </span>
                </div>
                <p class="text-2xl font-bold text-gray-900 mb-1">{{ formatCurrency(metrics.totalInventoryValue) }}</p>
                <p class="text-sm text-gray-500">costo × stock</p>
              </div>
            </div>
          </div>

          <!-- Valor VENTA -->
          <div class="bg-white rounded-2xl p-5 border border-gray-300 hover:border-gray-400 transition-all duration-200 hover:shadow-lg">
            <div class="flex items-center space-x-4">
              <div class="w-12 h-12 bg-gray-100 rounded-xl flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6 text-cyan-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                </svg>
              </div>
              <div class="flex-1 min-w-0">
                <div class="flex items-center justify-between mb-2">
                  <h3 class="text-sm font-semibold text-gray-700">Valor Potencial</h3>
                  <span class="text-xs font-medium text-cyan-700 bg-cyan-50 px-2 py-1 rounded-lg border border-cyan-200">
                    Venta
                  </span>
                </div>
                <p class="text-2xl font-bold text-gray-900 mb-1">{{ formatCurrency(metrics.totalSaleValue || 0) }}</p>
                <p class="text-sm text-gray-500">precio × stock</p>
              </div>
            </div>
          </div>

          <!-- Ganancia POTENCIAL -->
          <div class="bg-white rounded-2xl p-5 border border-gray-300 hover:border-gray-400 transition-all duration-200 hover:shadow-lg">
            <div class="flex items-center space-x-4">
              <div class="w-12 h-12 bg-gray-100 rounded-xl flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                </svg>
              </div>
              <div class="flex-1 min-w-0">
                <div class="flex items-center justify-between mb-2">
                  <h3 class="text-sm font-semibold text-gray-700">Ganancia</h3>
                  <span class="text-xs font-medium text-green-700 bg-green-50 px-2 py-1 rounded-lg border border-green-200">
                    {{ (metrics.inventoryProfitMargin || 0).toFixed(1) }}%
                  </span>
                </div>
                <p class="text-2xl font-bold text-gray-900 mb-1">{{ formatCurrency(metrics.potentialProfit || 0) }}</p>
                <p class="text-sm text-gray-500">margen potencial</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Métricas Secundarias - 3 Columnas Compactas -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
          <!-- Ventas del Período -->
          <div class="bg-white rounded-2xl p-4 border border-gray-300">
            <div class="flex items-center justify-between mb-3">
              <div class="flex items-center space-x-2">
                <div class="w-8 h-8 bg-purple-50 rounded-lg flex items-center justify-center">
                  <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                  </svg>
                </div>
                <h3 class="text-sm font-semibold text-gray-900">Ventas del Período</h3>
              </div>
            </div>
            <div class="text-center">
              <p class="text-xl font-bold text-purple-600">{{ formatCurrency(metrics.monthlySales || 0) }}</p>
              <p class="text-xs text-gray-600 mt-1">{{ monthlyTransactions || 0 }} transacciones</p>
            </div>
          </div>

          <!-- Stock Crítico -->
          <div class="bg-white rounded-2xl p-4 border border-gray-300">
            <div class="flex items-center justify-between mb-3">
              <div class="flex items-center space-x-2">
                <div class="w-8 h-8 bg-red-50 rounded-lg flex items-center justify-center">
                  <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                  </svg>
                </div>
                <h3 class="text-sm font-semibold text-gray-900">Alertas de Stock</h3>
              </div>
            </div>
            <div class="flex items-center justify-around">
              <div class="text-center">
                <p class="text-xl font-bold text-red-600">{{ metrics.lowStockProducts || 0 }}</p>
                <p class="text-xs text-gray-600 mt-1">Stock bajo</p>
              </div>
              <div class="h-8 w-px bg-gray-200"></div>
              <div class="text-center">
                <p class="text-xl font-bold text-red-700">{{ metrics.outOfStockProducts || 0 }}</p>
                <p class="text-xs text-gray-600 mt-1">Sin stock</p>
              </div>
            </div>
          </div>

          <!-- Ganancias Realizadas -->
          <div class="bg-white rounded-2xl p-4 border border-gray-300">
            <div class="flex items-center justify-between mb-3">
              <div class="flex items-center space-x-2">
                <div class="w-8 h-8 bg-emerald-50 rounded-lg flex items-center justify-center">
                  <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                  </svg>
                </div>
                <h3 class="text-sm font-semibold text-gray-900">Ganancias Realizadas</h3>
              </div>
            </div>
            <div class="text-center">
              <p class="text-xl font-bold text-emerald-600">{{ formatCurrency(metrics.monthlySales || 0) }}</p>
              <p class="text-xs text-gray-600 mt-1">{{ getPeriodLabel() }}</p>
            </div>
          </div>
        </div>

        <!-- Sección de Productos y Movimientos - 2 Columnas Compactas -->
        <div v-if="overviewData" class="grid grid-cols-1 lg:grid-cols-2 gap-3">
          <!-- Productos Más Vendidos -->
          <div v-if="overviewData.data.topSellingProducts?.length > 0" class="bg-white rounded-2xl border border-gray-300 p-5">
            <div class="bg-gray-50 border-b border-gray-200 px-5 py-3 -mx-5 -mt-5 mb-4 rounded-t-2xl">
              <div class="flex items-center space-x-2">
                <div class="w-8 h-8 bg-yellow-50 rounded-lg flex items-center justify-center">
                  <svg class="w-4 h-4 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path>
                  </svg>
                </div>
                <div>
                  <h3 class="text-base font-bold text-gray-900">Top Productos</h3>
                  <p class="text-xs text-gray-500">Más vendidos del período</p>
                </div>
              </div>
            </div>
            <div class="space-y-2">
              <div v-for="(product, index) in overviewData.data.topSellingProducts.slice(0, 5)" 
                   :key="product.id" 
                   class="flex items-center justify-between p-2.5 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                <div class="flex items-center space-x-3 flex-1 min-w-0">
                  <div class="w-7 h-7 bg-gradient-to-r from-yellow-400 to-orange-500 rounded-lg flex items-center justify-center text-white font-bold text-xs flex-shrink-0">
                    {{ index + 1 }}
                  </div>
                  <div class="flex-1 min-w-0">
                    <p class="font-semibold text-gray-900 text-sm truncate">{{ product.name }}</p>
                    <p class="text-xs text-gray-600">{{ product.total_quantity_sold }} unidades</p>
                  </div>
                </div>
                <div class="text-right ml-2">
                  <p class="font-bold text-green-600 text-sm">{{ formatCurrency(product.total_revenue) }}</p>
                </div>
              </div>
            </div>
          </div>

          <!-- Movimientos Recientes -->
          <div v-if="overviewData.data.recentMovements?.length > 0" class="bg-white rounded-2xl border border-gray-300 p-5">
            <div class="bg-gray-50 border-b border-gray-200 px-5 py-3 -mx-5 -mt-5 mb-4 rounded-t-2xl">
              <div class="flex items-center space-x-2">
                <div class="w-8 h-8 bg-blue-50 rounded-lg flex items-center justify-center">
                  <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                  </svg>
                </div>
                <div>
                  <h3 class="text-base font-bold text-gray-900">Movimientos</h3>
                  <p class="text-xs text-gray-500">Actividad reciente</p>
                </div>
              </div>
            </div>
            <div class="space-y-2">
              <div v-for="movement in overviewData.data.recentMovements.slice(0, 5)" 
                   :key="movement.id" 
                   class="flex items-center justify-between p-2.5 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                <div class="flex items-center space-x-3 flex-1 min-w-0">
                  <div :class="[
                    'w-7 h-7 rounded-lg flex items-center justify-center text-white text-xs flex-shrink-0',
                    getMovementTypeClass(movement.type)
                  ]">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="getMovementTypeIcon(movement.type)"></path>
                    </svg>
                  </div>
                  <div class="flex-1 min-w-0">
                    <p class="font-semibold text-gray-900 text-sm truncate">{{ movement.product?.name }}</p>
                    <p class="text-xs text-gray-600">{{ formatMovementType(movement.type) }} - {{ formatDate(movement.created_at) }}</p>
                  </div>
                </div>
                <div class="text-right ml-2">
                  <p :class="['font-bold text-sm', movement.quantity > 0 ? 'text-green-600' : 'text-red-600']">
                    {{ movement.quantity > 0 ? '+' : '' }}{{ movement.quantity }}
                  </p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Vista por Productos -->
      <div v-if="activeSection === 'products'" class="space-y-4">
        
        <!-- Filtros Compactos -->
        <div class="bg-white rounded-lg shadow-sm p-3 border border-gray-200">
          <div class="flex flex-wrap items-center gap-3">
            <!-- Búsqueda -->
            <div class="flex-1 min-w-48 relative">
              <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
              </svg>
              <input
                v-model="filters.search"
                @input="debounceSearch"
                type="text"
                placeholder="Buscar productos..."
                class="w-full pl-9 pr-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              >
            </div>
            
            <!-- Selector de Período -->
            <select 
              v-model="selectedPeriod"
              @change="handlePeriodChange"
              class="pl-3 pr-8 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 min-w-36"
            >
              <option value="day">Hoy</option>
              <option value="week">Esta Semana</option>
              <option value="month">Este Mes</option>
              <option value="year">Este Año</option>
              <option value="custom">Personalizado</option>
            </select>
            
            <!-- Campos de fecha personalizada -->
            <template v-if="selectedPeriod === 'custom'">
              <input
                type="date"
                v-model="customDateRange.start"
                @input="loadProductsData"
                :max="new Date().toISOString().split('T')[0]"
                class="pl-3 pr-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
              >
            </template>

            <!-- Filtro por Categoría -->
            <select 
              v-model="filters.category"
              @change="loadProductsData"
              class="pl-3 pr-8 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 min-w-44"
            >
              <option value="">Todas las categorías</option>
              <option v-for="cat in availableCategories" :key="cat.id" :value="cat.id">
                {{ cat.name }}
              </option>
            </select>

            <!-- Botón Limpiar Filtros -->
            <button 
              @click="clearFilters" 
              class="p-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition-colors"
              title="Limpiar filtros">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
              </svg>
            </button>
          </div>
        </div>

        <!-- Resumen Compacto - 6 Métricas -->
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3">
          <div class="bg-white rounded-lg p-3 border border-gray-200 shadow-sm">
            <div class="flex items-center space-x-2">
              <div class="w-8 h-8 bg-blue-50 rounded-lg flex items-center justify-center flex-shrink-0">
                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                </svg>
              </div>
              <div class="flex-1 min-w-0">
                <p class="text-xs font-medium text-gray-500">Total</p>
                <p class="text-base font-bold text-gray-900">{{ productsData?.summary?.total_products || 0 }}</p>
              </div>
            </div>
          </div>
          
          <div class="bg-white rounded-lg p-3 border border-gray-200 shadow-sm">
            <div class="flex items-center space-x-2">
              <div class="w-8 h-8 bg-green-50 rounded-lg flex items-center justify-center flex-shrink-0">
                <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
              </div>
              <div class="flex-1 min-w-0">
                <p class="text-xs font-medium text-gray-500">En Venta</p>
                <p class="text-base font-bold text-gray-900">{{ productsData?.summary?.products_in_stock || 0 }}</p>
              </div>
            </div>
          </div>
          
          <div class="bg-white rounded-lg p-3 border border-gray-200 shadow-sm">
            <div class="flex items-center space-x-2">
              <div class="w-8 h-8 bg-amber-50 rounded-lg flex items-center justify-center flex-shrink-0">
                <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
              </div>
              <div class="flex-1 min-w-0">
                <p class="text-xs font-medium text-gray-500">V. Costo</p>
                <p class="text-sm font-bold text-gray-900">{{ formatCurrency(productsData?.summary?.total_value_cost || 0) }}</p>
              </div>
            </div>
          </div>
          
          <div class="bg-white rounded-lg p-3 border border-gray-200 shadow-sm">
            <div class="flex items-center space-x-2">
              <div class="w-8 h-8 bg-blue-50 rounded-lg flex items-center justify-center flex-shrink-0">
                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
              </div>
              <div class="flex-1 min-w-0">
                <p class="text-xs font-medium text-gray-500">V. Venta</p>
                <p class="text-sm font-bold text-gray-900">{{ formatCurrency(productsData?.summary?.total_value_sale || 0) }}</p>
              </div>
            </div>
          </div>
          
          <div class="bg-white rounded-lg p-3 border border-gray-200 shadow-sm">
            <div class="flex items-center space-x-2">
              <div class="w-8 h-8 bg-green-50 rounded-lg flex items-center justify-center flex-shrink-0">
                <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                </svg>
              </div>
              <div class="flex-1 min-w-0">
                <p class="text-xs font-medium text-gray-500">Ganancia</p>
                <p class="text-sm font-bold text-gray-900">{{ formatCurrency(productsData?.summary?.total_profit_potential || 0) }}</p>
              </div>
            </div>
          </div>
          
          <div class="bg-white rounded-lg p-3 border border-gray-200 shadow-sm">
            <div class="flex items-center space-x-2">
              <div class="w-8 h-8 bg-red-50 rounded-lg flex items-center justify-center flex-shrink-0">
                <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>
              </div>
              <div class="flex-1 min-w-0">
                <p class="text-xs font-medium text-gray-500">Sin Stock</p>
                <p class="text-base font-bold text-gray-900">{{ productsData?.summary?.out_of_stock || 0 }}</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Tabla de Productos Compacta -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
          <div class="bg-gray-50 border-b border-gray-200 px-5 py-4 flex items-center justify-between">
            <div>
              <h2 class="text-base font-bold text-gray-900">Detalle de Productos</h2>
              <p class="text-xs text-gray-500 mt-0.5">{{ productsData?.products?.length || 0 }} productos mostrados</p>
            </div>
          </div>
          
          <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
              <thead class="bg-gray-50">
                <tr>
                  <th class="px-3 py-2 text-left text-xs font-bold text-gray-600 uppercase tracking-wide">Producto</th>
                  <th class="px-3 py-2 text-left text-xs font-bold text-gray-600 uppercase tracking-wide">Stock</th>
                  <th class="px-3 py-2 text-left text-xs font-bold text-gray-600 uppercase tracking-wide">Precios</th>
                  <th class="px-3 py-2 text-left text-xs font-bold text-gray-600 uppercase tracking-wide">Rentabilidad</th>
                  <th class="px-3 py-2 text-left text-xs font-bold text-gray-600 uppercase tracking-wide">Ventas</th>
                  <th class="px-3 py-2 text-left text-xs font-bold text-gray-600 uppercase tracking-wide">Rotación</th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200">
                <tr v-for="product in productsData?.products || []" :key="product.id" class="hover:bg-gray-50 transition-colors">
                  <!-- Producto -->
                  <td class="px-3 py-3">
                    <div>
                      <div class="text-sm font-semibold text-gray-900">{{ product.name }}</div>
                      <div class="text-xs text-gray-500">{{ product.barcode || product.sku || 'Sin código' }}</div>
                      <div class="text-xs text-gray-400">{{ product.category }}</div>
                    </div>
                  </td>
                  
                  <!-- Stock -->
                  <td class="px-3 py-3">
                    <div class="flex items-center space-x-2">
                      <span :class="[
                        'px-2 py-0.5 rounded-full text-xs font-semibold',
                        getStockStatusClass(product.stock_status)
                      ]">
                        {{ getStockStatusText(product.stock_status) }}
                      </span>
                      <div class="text-xs">
                        <div class="font-semibold text-gray-900">{{ product.current_stock }}</div>
                        <div class="text-gray-500">Min: {{ product.min_stock }}</div>
                      </div>
                    </div>
                  </td>
                  
                  <!-- Precios -->
                  <td class="px-3 py-3 text-xs">
                    <div>
                      <div class="text-gray-900 font-medium">{{ formatCurrency(product.sale_price) }}</div>
                      <div class="text-gray-500">{{ formatCurrency(product.cost_price) }}</div>
                    </div>
                  </td>
                  
                  <!-- Rentabilidad -->
                  <td class="px-3 py-3 text-xs">
                    <div>
                      <div class="font-bold text-green-600">{{ formatCurrency(product.unit_profit) }}</div>
                      <div class="text-gray-600">{{ product.unit_profit_margin }}% margen</div>
                      <div class="text-gray-400">Tot: {{ formatCurrency(product.total_profit_potential) }}</div>
                    </div>
                  </td>
                  
                  <!-- Ventas del Período -->
                  <td class="px-3 py-3 text-xs">
                    <div>
                      <div class="font-semibold text-blue-600">{{ product.period_sales.quantity }} unid</div>
                      <div class="text-gray-600">{{ formatCurrency(product.period_sales.revenue) }}</div>
                    </div>
                  </td>
                  
                  <!-- Rotación -->
                  <td class="px-3 py-3 text-xs">
                    <span :class="[
                      'px-2 py-0.5 rounded-full text-xs font-semibold',
                      getRotationClass(product.rotation_days)
                    ]">
                      {{ product.rotation_days === 999 ? 'Sin ventas' : `${product.rotation_days}d` }}
                    </span>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
          
          <!-- Paginación Compacta -->
          <div v-if="productsData?.products?.length > 0" class="bg-white border-t border-gray-200 px-4 py-3 flex items-center justify-between">
            <div class="flex items-center space-x-3">
              <div class="flex items-center space-x-2">
                <span class="text-xs font-medium text-gray-700">Mostrar:</span>
                <select v-model="filters.itemsPerPage" 
                        @change="filters.currentPage = 1; loadProductsData()"
                        class="border border-gray-300 rounded-lg px-2 py-1 text-xs focus:outline-none focus:ring-2 focus:ring-blue-500">
                  <option value="10">10</option>
                  <option value="25">25</option>
                  <option value="50">50</option>
                  <option value="100">100</option>
                </select>
                <span class="text-xs text-gray-700">por página</span>
              </div>
              
              <div class="text-xs text-gray-700">
                Mostrando {{ productsPaginationInfo.start }} a {{ productsPaginationInfo.end }} de {{ productsPaginationInfo.total }}
              </div>
            </div>
            
            <div class="flex items-center space-x-1">
              <button @click="filters.currentPage--; loadProductsData()" 
                      :disabled="filters.currentPage === 1"
                      class="p-1.5 text-gray-500 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
              </button>
              <span class="px-2.5 py-1 text-xs font-medium text-gray-900">
                {{ filters.currentPage }} / {{ productsTotalPages }}
              </span>
              <button @click="filters.currentPage++; loadProductsData()" 
                      :disabled="filters.currentPage >= productsTotalPages"
                      class="p-1.5 text-gray-500 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Vista de Movimientos -->
      <div v-if="activeSection === 'movements'" class="space-y-6">
        <div class="flex items-center justify-between mb-6">
          <h2 class="text-xl font-semibold text-gray-900">Movimientos de Inventario</h2>
          
          <!-- Controles y Filtros -->
          <div class="flex items-center space-x-4">
            <!-- Selector de Período -->
            <select 
              v-model="selectedPeriod"
              @change="handlePeriodChange"
              class="bg-white border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500"
            >
              <option value="day">Hoy</option>
              <option value="week">Esta Semana</option>
              <option value="month">Este Mes</option>
              <option value="year">Este Año</option>
              <option value="custom">Rango Personalizado</option>
            </select>
            
            <!-- Campos de fecha personalizada -->
            <template v-if="selectedPeriod === 'custom'">
              <input
                type="date"
                v-model="customDateRange.start"
                @input="loadMovementsData"
                :max="new Date().toISOString().split('T')[0]"
                class="bg-white border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500"
                placeholder="Fecha"
                required
              >
            </template>

            <!-- Filtro por Tipo -->
            <select 
              v-model="movementsFilters.type"
              @change="loadMovementsData"
              class="bg-white border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500"
            >
              <option value="">Todos los tipos</option>
              <option value="entry">Solo Entradas</option>
              <option value="exit">Solo Salidas</option>
            </select>

            <!-- Botón Exportar -->
            <button 
              @click="exportMovements"
              class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors"
            >
              📊 Exportar
            </button>
          </div>
        </div>

        <!-- Tarjetas de Resumen -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-4 mb-6">
          <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center">
              <div class="p-2 bg-blue-100 rounded-lg">
                <i class="fas fa-exchange-alt text-blue-600"></i>
              </div>
              <div class="ml-3">
                <p class="text-sm font-medium text-gray-600">Total Movimientos</p>
                <p class="text-lg font-bold text-gray-900">{{ movementsData?.summary?.total_movements || 0 }}</p>
              </div>
            </div>
          </div>

          <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center">
              <div class="p-2 bg-green-100 rounded-lg">
                <i class="fas fa-arrow-up text-green-600"></i>
              </div>
              <div class="ml-3">
                <p class="text-sm font-medium text-gray-600">Entradas</p>
                <p class="text-lg font-bold text-green-600">{{ movementsData?.summary?.total_entries || 0 }}</p>
              </div>
            </div>
          </div>

          <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center">
              <div class="p-2 bg-red-100 rounded-lg">
                <i class="fas fa-arrow-down text-red-600"></i>
              </div>
              <div class="ml-3">
                <p class="text-sm font-medium text-gray-600">Salidas</p>
                <p class="text-lg font-bold text-red-600">{{ movementsData?.summary?.total_exits || 0 }}</p>
              </div>
            </div>
          </div>

          <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center">
              <div class="p-2 bg-emerald-100 rounded-lg">
                <i class="fas fa-dollar-sign text-emerald-600"></i>
              </div>
              <div class="ml-3">
                <p class="text-sm font-medium text-gray-600">Valor Entradas</p>
                <p class="text-lg font-bold text-emerald-600">{{ formatCurrency(movementsData?.summary?.total_entry_value || 0) }}</p>
              </div>
            </div>
          </div>

          <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center">
              <div class="p-2 bg-orange-100 rounded-lg">
                <i class="fas fa-dollar-sign text-orange-600"></i>
              </div>
              <div class="ml-3">
                <p class="text-sm font-medium text-gray-600">Valor Salidas</p>
                <p class="text-lg font-bold text-orange-600">{{ formatCurrency(movementsData?.summary?.total_exit_value || 0) }}</p>
              </div>
            </div>
          </div>

          <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center">
              <div class="p-2 bg-indigo-100 rounded-lg">
                <i class="fas fa-chart-line text-indigo-600"></i>
              </div>
              <div class="ml-3">
                <p class="text-sm font-medium text-gray-600">Movimiento Neto</p>
                <p class="text-lg font-bold" :class="(movementsData?.summary?.net_movement || 0) >= 0 ? 'text-green-600' : 'text-red-600'">
                  {{ formatCurrency(movementsData?.summary?.net_movement || 0) }}
                </p>
              </div>
            </div>
          </div>
        </div>

        <!-- Tabla de Movimientos -->
        <div class="bg-white rounded-lg shadow">
          <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Historial de Movimientos</h3>
          </div>
          <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
              <thead class="bg-gray-50">
                <tr>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipo</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Producto</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cantidad</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Precio Unit.</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Documento</th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200">
                <!-- Movimientos -->
                <tr v-for="movement in movementsData?.movements || []" :key="movement.movement_id" class="hover:bg-gray-50">
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                    {{ formatDate(movement.movement_date) }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <span 
                      class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                      :class="movement.movement_type === 'entry' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'"
                    >
                      <i :class="movement.movement_type === 'entry' ? 'fas fa-plus mr-1' : 'fas fa-minus mr-1'"></i>
                      {{ movement.movement_reason }}
                    </span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                    {{ movement.product_name }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm" :class="movement.quantity >= 0 ? 'text-green-600' : 'text-red-600'">
                    {{ Math.abs(movement.quantity) }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                    {{ formatCurrency(movement.unit_price) }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm font-medium" :class="movement.total_value >= 0 ? 'text-green-600' : 'text-red-600'">
                    {{ formatCurrency(Math.abs(movement.total_value)) }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    {{ movement.document_number }}
                  </td>
                </tr>
                
                <!-- Estado sin movimientos -->
                <tr v-if="!movementsData?.movements || movementsData.movements.length === 0">
                  <td colspan="7" class="px-6 py-12 text-center">
                    <div class="flex flex-col items-center">
                      <i class="fas fa-box-open text-gray-400 text-4xl mb-4"></i>
                      <h3 class="text-lg font-medium text-gray-900 mb-2">No hay movimientos</h3>
                      <p class="text-gray-500">No se encontraron movimientos con los filtros aplicados</p>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
          
          <!-- Controles de Paginación para Movimientos -->
          <div v-if="movementsData?.movements && movementsData.movements.length > 0" class="bg-white px-6 py-3 border-t border-gray-200 rounded-b-lg">
            <div class="flex items-center justify-between">
              <!-- Items por página y información -->
              <div class="flex items-center space-x-4">
                <div class="flex items-center space-x-2">
                  <label class="text-sm text-gray-700">Mostrar:</label>
                  <select v-model="movementsFilters.itemsPerPage" @change="movementsFilters.currentPage = 1; loadMovementsData()" 
                          class="text-sm border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="10">10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                  </select>
                  <span class="text-sm text-gray-700">por página</span>
                </div>
                
                <!-- Información de paginación -->
                <div class="text-sm text-gray-700">
                  Mostrando {{ movementsPaginationInfo.start }} a {{ movementsPaginationInfo.end }} de {{ movementsPaginationInfo.total }} movimientos
                </div>
              </div>
              
              <!-- Controles de paginación -->
              <div class="flex items-center space-x-2">
                <!-- Botón Primera página -->
                <button @click="movementsFilters.currentPage = 1; loadMovementsData()" 
                        :disabled="movementsFilters.currentPage === 1"
                        class="px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"></path>
                  </svg>
                </button>
                
                <!-- Botón Anterior -->
                <button @click="movementsFilters.currentPage--; loadMovementsData()" 
                        :disabled="movementsFilters.currentPage === 1"
                        class="px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                  </svg>
                </button>
                
                <!-- Números de página -->
                <div class="flex items-center space-x-1">
                  <template v-for="page in movementsTotalPages" :key="page">
                    <button v-if="page === 1 || page === movementsTotalPages || Math.abs(page - movementsFilters.currentPage) <= 2"
                            @click="movementsFilters.currentPage = page; loadMovementsData()"
                            :class="[
                              'px-3 py-2 text-sm font-medium rounded-lg transition-colors',
                              page === movementsFilters.currentPage 
                                ? 'bg-indigo-600 text-white border border-indigo-600' 
                                : 'text-gray-500 bg-white border border-gray-300 hover:bg-gray-50'
                            ]">
                      {{ page }}
                    </button>
                    <span v-else-if="Math.abs(page - movementsFilters.currentPage) === 3" class="px-2 text-gray-400">...</span>
                  </template>
                </div>
                
                <!-- Botón Siguiente -->
                <button @click="movementsFilters.currentPage++; loadMovementsData()" 
                        :disabled="movementsFilters.currentPage === movementsTotalPages"
                        class="px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                  </svg>
                </button>
                
                <!-- Botón Última página -->
                <button @click="movementsFilters.currentPage = movementsTotalPages; loadMovementsData()" 
                        :disabled="movementsFilters.currentPage === movementsTotalPages"
                        class="px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7"></path>
                  </svg>
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Vista por Cliente -->
      <div v-if="activeSection === 'customers'" class="space-y-6">
        <div class="flex items-center justify-between mb-6">
          <h2 class="text-xl font-semibold text-gray-900">Análisis por Cliente</h2>
          
          <!-- Controles y Filtros -->
          <div class="flex items-center space-x-4">
            <!-- Selector de Período -->
            <select 
              v-model="selectedPeriod"
              @change="handlePeriodChange"
              class="bg-white border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500"
            >
              <option value="day">Hoy</option>
              <option value="week">Esta Semana</option>
              <option value="month">Este Mes</option>
              <option value="year">Este Año</option>
              <option value="custom">Rango Personalizado</option>
            </select>
            
            <!-- Campos de fecha personalizada -->
            <template v-if="selectedPeriod === 'custom'">
              <input
                type="date"
                v-model="customDateRange.start"
                @input="loadCustomersData"
                :max="new Date().toISOString().split('T')[0]"
                class="bg-white border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500"
                placeholder="Fecha"
                required
              >
            </template>

            <!-- Ordenar por -->
            <select 
              v-model="customersFilters.sortBy"
              @change="loadCustomersData"
              class="bg-white border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500"
            >
              <option value="total_spent">Valor Total</option>
              <option value="total_purchases">Más Compras</option>
              <option value="unique_products_bought">Más Productos</option>
            </select>

            <!-- Botón Exportar -->
            <button 
              @click="exportCustomers"
              class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors"
            >
              📊 Exportar
            </button>
          </div>
        </div>

        <!-- Tarjetas de Resumen -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-4 mb-6" v-if="customersData">
          <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center">
              <div class="p-2 bg-blue-100 rounded-lg">
                <i class="fas fa-users text-blue-600"></i>
              </div>
              <div class="ml-3">
                <p class="text-sm font-medium text-gray-600">Total Clientes</p>
                <p class="text-lg font-bold text-gray-900">{{ customersData.summary.total_customers }}</p>
              </div>
            </div>
          </div>

          <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center">
              <div class="p-2 bg-green-100 rounded-lg">
                <i class="fas fa-dollar-sign text-green-600"></i>
              </div>
              <div class="ml-3">
                <p class="text-sm font-medium text-gray-600">Ingresos Totales</p>
                <p class="text-lg font-bold text-green-600">{{ formatCurrency(customersData.summary.total_revenue) }}</p>
              </div>
            </div>
          </div>

          <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center">
              <div class="p-2 bg-emerald-100 rounded-lg">
                <i class="fas fa-chart-line text-emerald-600"></i>
              </div>
              <div class="ml-3">
                <p class="text-sm font-medium text-gray-600">Ganancia Total</p>
                <p class="text-lg font-bold text-emerald-600">{{ formatCurrency(customersData.summary.total_profit) }}</p>
              </div>
            </div>
          </div>

          <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center">
              <div class="p-2 bg-purple-100 rounded-lg">
                <i class="fas fa-user-star text-purple-600"></i>
              </div>
              <div class="ml-3">
                <p class="text-sm font-medium text-gray-600">Valor Promedio</p>
                <p class="text-lg font-bold text-purple-600">{{ formatCurrency(customersData.summary.avg_customer_value) }}</p>
              </div>
            </div>
          </div>

          <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center">
              <div class="p-2 bg-orange-100 rounded-lg">
                <i class="fas fa-percentage text-orange-600"></i>
              </div>
              <div class="ml-3">
                <p class="text-sm font-medium text-gray-600">Descuento Prom.</p>
                <p class="text-lg font-bold text-orange-600">{{ formatPercentage(customersData.summary.avg_discount) }}</p>
              </div>
            </div>
          </div>

          <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center">
              <div class="p-2 bg-indigo-100 rounded-lg">
                <i class="fas fa-crown text-indigo-600"></i>
              </div>
              <div class="ml-3">
                <p class="text-sm font-medium text-gray-600">Cliente Top</p>
                <p class="text-sm font-bold text-indigo-600" v-if="customersData.summary.top_customer">
                  {{ customersData.summary.top_customer.name }}
                </p>
              </div>
            </div>
          </div>
        </div>

        <!-- Tabla de Clientes -->
        <div class="bg-white rounded-lg shadow">
          <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Análisis de Clientes</h3>
          </div>
          <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
              <thead class="bg-gray-50">
                <tr>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cliente</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Compras</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Gastado</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Productos Únicos</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Items Totales</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Frecuencia</th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200" v-if="customersData && customersData.customers">
                <tr v-for="customer in customersData.customers" :key="customer.customer_id" class="hover:bg-gray-50">
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center">
                      <div class="w-8 h-8 bg-indigo-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-user text-indigo-600 text-sm"></i>
                      </div>
                      <div class="ml-3">
                        <div class="text-sm font-medium text-gray-900">{{ customer.customer_name }}</div>
                        <div class="text-sm text-gray-500" v-if="customer.email">{{ customer.email }}</div>
                      </div>
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                      {{ customer.total_purchases }} compras
                    </span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-green-600">
                    {{ formatCurrency(customer.total_spent) }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    {{ customer.unique_products_bought }} productos
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    {{ customer.total_items_bought }} items
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-gray-900">
                      {{ Math.round((customer.total_items_bought / customer.total_purchases) * 10) / 10 }} items/compra
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
          
          <!-- Controles de Paginación para Clientes -->
          <div v-if="customersData?.customers && customersData.customers.length > 0" class="bg-white px-6 py-3 border-t border-gray-200 rounded-b-lg">
            <div class="flex items-center justify-between">
              <!-- Items por página y información -->
              <div class="flex items-center space-x-4">
                <div class="flex items-center space-x-2">
                  <label class="text-sm text-gray-700">Mostrar:</label>
                  <select v-model="customersFilters.itemsPerPage" @change="customersFilters.currentPage = 1; loadCustomersData()" 
                          class="text-sm border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="10">10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                  </select>
                  <span class="text-sm text-gray-700">por página</span>
                </div>
                
                <!-- Información de paginación -->
                <div class="text-sm text-gray-700">
                  Mostrando {{ customersPaginationInfo.start }} a {{ customersPaginationInfo.end }} de {{ customersPaginationInfo.total }} clientes
                </div>
              </div>
              
              <!-- Controles de paginación -->
              <div class="flex items-center space-x-2">
                <!-- Botón Primera página -->
                <button @click="customersFilters.currentPage = 1; loadCustomersData()" 
                        :disabled="customersFilters.currentPage === 1"
                        class="px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"></path>
                  </svg>
                </button>
                
                <!-- Botón Anterior -->
                <button @click="customersFilters.currentPage--; loadCustomersData()" 
                        :disabled="customersFilters.currentPage === 1"
                        class="px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                  </svg>
                </button>
                
                <!-- Números de página -->
                <div class="flex items-center space-x-1">
                  <template v-for="page in customersTotalPages" :key="page">
                    <button v-if="page === 1 || page === customersTotalPages || Math.abs(page - customersFilters.currentPage) <= 2"
                            @click="customersFilters.currentPage = page; loadCustomersData()"
                            :class="[
                              'px-3 py-2 text-sm font-medium rounded-lg transition-colors',
                              page === customersFilters.currentPage 
                                ? 'bg-indigo-600 text-white border border-indigo-600' 
                                : 'text-gray-500 bg-white border border-gray-300 hover:bg-gray-50'
                            ]">
                      {{ page }}
                    </button>
                    <span v-else-if="Math.abs(page - customersFilters.currentPage) === 3" class="px-2 text-gray-400">...</span>
                  </template>
                </div>
                
                <!-- Botón Siguiente -->
                <button @click="customersFilters.currentPage++; loadCustomersData()" 
                        :disabled="customersFilters.currentPage === customersTotalPages"
                        class="px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                  </svg>
                </button>
                
                <!-- Botón Última página -->
                <button @click="customersFilters.currentPage = customersTotalPages; loadCustomersData()" 
                        :disabled="customersFilters.currentPage === customersTotalPages"
                        class="px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7"></path>
                  </svg>
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Vista de Alertas -->
      <div v-if="activeSection === 'alerts'" class="space-y-4">
        
        <!-- Header con Filtros -->
        <div class="bg-white rounded-lg border border-gray-200 p-4">
          <div class="flex items-center justify-between">
            <div class="flex items-center space-x-3">
              <span class="text-sm font-medium text-gray-700">Filtrar por severidad:</span>
              <select 
                v-model="alertsFilters.severity"
                @change="loadAlertsData"
                class="px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
              >
                <option value="">Advertencias</option>
                <option value="critical">Críticas</option>
                <option value="warning">Advertencias</option>
                <option value="info">Información</option>
              </select>
            </div>
            
            <button 
              @click="loadAlertsData"
              class="px-4 py-2 bg-white hover:bg-gray-50 text-gray-700 text-sm font-medium rounded-lg border border-gray-300 transition-colors flex items-center space-x-2"
            >
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
              </svg>
              <span>Actualizar</span>
            </button>
          </div>
        </div>

        <!-- 4 Tarjetas de Resumen Horizontales -->
        <div v-if="alertsData" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
          <!-- Críticas -->
          <div class="bg-white rounded-lg border border-gray-200 p-4">
            <div class="flex items-center space-x-3">
              <div class="w-12 h-12 bg-red-50 rounded-lg flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>
              </div>
              <div class="flex-1">
                <p class="text-sm text-gray-600 mb-1">Críticas</p>
                <p class="text-3xl font-bold text-gray-900">{{ alertsData.summary.critical || 0 }}</p>
              </div>
            </div>
          </div>

          <!-- Advertencias -->
          <div class="bg-white rounded-lg border border-gray-200 p-4">
            <div class="flex items-center space-x-3">
              <div class="w-12 h-12 bg-amber-50 rounded-lg flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
              </div>
              <div class="flex-1">
                <p class="text-sm text-gray-600 mb-1">Advertencias</p>
                <p class="text-3xl font-bold text-gray-900">{{ alertsData.summary.warning || 0 }}</p>
              </div>
            </div>
          </div>

          <!-- Información -->
          <div class="bg-white rounded-lg border border-gray-200 p-4">
            <div class="flex items-center space-x-3">
              <div class="w-12 h-12 bg-blue-50 rounded-lg flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
              </div>
              <div class="flex-1">
                <p class="text-sm text-gray-600 mb-1">Información</p>
                <p class="text-3xl font-bold text-gray-900">{{ alertsData.summary.info || 0 }}</p>
              </div>
            </div>
          </div>

          <!-- Total -->
          <div class="bg-white rounded-lg border border-gray-200 p-4">
            <div class="flex items-center space-x-3">
              <div class="w-12 h-12 bg-gray-100 rounded-lg flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                </svg>
              </div>
              <div class="flex-1">
                <p class="text-sm text-gray-600 mb-1">Total</p>
                <p class="text-3xl font-bold text-gray-900">{{ alertsData.summary.total_alerts || 0 }}</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Alertas del Sistema - Header -->
        <div v-if="alertsData && alertsData.alerts?.length > 0" class="bg-white rounded-lg border border-gray-200">
          <div class="border-b border-gray-200 px-5 py-4">
            <div class="flex items-center justify-between">
              <div>
                <h3 class="text-base font-semibold text-gray-900">Alertas del Sistema</h3>
                <p class="text-sm text-gray-500 mt-1">{{ alertsData.alerts.length }} notificaciones activas</p>
              </div>
              <span class="px-3 py-1 bg-blue-50 text-blue-700 text-sm font-medium rounded border border-blue-200">
                IA
              </span>
            </div>
          </div>
          
          <!-- Grid de 3 columnas igual a la imagen -->
          <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-4 p-5">
            <div v-for="alert in paginatedAlerts" :key="alert.id" 
                 class="bg-white border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
              <!-- Header de la alerta con título y badge -->
              <div class="flex items-start justify-between mb-3">
                <div class="flex items-center space-x-2">
                  <div :class="[
                    'w-10 h-10 rounded-lg flex items-center justify-center flex-shrink-0',
                    alert.severity === 'critical' ? 'bg-red-50' :
                    alert.severity === 'warning' ? 'bg-amber-50' :
                    'bg-blue-50'
                  ]">
                    <svg class="w-5 h-5" :class="[
                      alert.severity === 'critical' ? 'text-red-500' :
                      alert.severity === 'warning' ? 'text-amber-500' :
                      'text-blue-500'
                    ]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path v-if="alert.severity === 'critical'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                      <path v-else-if="alert.severity === 'warning'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                      <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                  </div>
                  <h4 class="text-sm font-semibold text-gray-900">{{ alert.title }}</h4>
                </div>
                <span :class="[
                  'px-2.5 py-1 text-xs font-medium rounded flex-shrink-0',
                  alert.severity === 'critical' ? 'bg-red-50 text-red-700 border border-red-200' :
                  alert.severity === 'warning' ? 'bg-amber-50 text-amber-700 border border-amber-200' :
                  'bg-blue-50 text-blue-700 border border-blue-200'
                ]">
                  {{ alert.severity === 'critical' ? 'Crítico' : alert.severity === 'warning' ? 'Aviso' : 'Info' }}
                </span>
              </div>
              
              <!-- Mensaje -->
              <p class="text-sm text-gray-700 mb-3 line-clamp-2">{{ alert.message }}</p>
              
              <!-- Metadata con badges -->
              <div class="flex flex-wrap items-center gap-2 text-xs text-gray-600 mb-3">
                <span class="bg-gray-50 px-2 py-1 rounded border border-gray-200">
                  {{ alert.category }}
                </span>
                <span class="bg-gray-50 px-2 py-1 rounded border border-gray-200">
                  {{ formatDate(alert.created_at) }}
                </span>
                <span v-if="alert.product_name" class="bg-gray-50 px-2 py-1 rounded border border-gray-200 truncate max-w-full">
                  {{ alert.product_name }}
                </span>
              </div>
              
              <!-- Botón de acción -->
              <button v-if="alert.action_text" 
                      class="w-full px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded transition-colors">
                {{ alert.action_text }}
              </button>
            </div>
          </div>
          
          <!-- Paginación Alertas -->
          <div v-if="alertsData.alerts.length > 10" class="bg-gray-50 px-6 py-3 border-t border-gray-200">
            <div class="flex items-center justify-between">
              <div class="text-sm text-gray-700">
                Mostrando {{ ((alertsCurrentPage - 1) * 10) + 1 }} - {{ Math.min(alertsCurrentPage * 10, alertsData.alerts.length) }} de {{ alertsData.alerts.length }} alertas
              </div>
              <div class="flex items-center space-x-2">
                <button @click="alertsCurrentPage--" 
                        :disabled="alertsCurrentPage <= 1"
                        class="px-3 py-1 text-sm bg-white border border-gray-300 rounded hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed">
                  Anterior
                </button>
                <span class="px-3 py-1 text-sm font-medium text-gray-900">
                  {{ alertsCurrentPage }} / {{ Math.ceil(alertsData.alerts.length / 10) }}
                </span>
                <button @click="alertsCurrentPage++" 
                        :disabled="alertsCurrentPage >= Math.ceil(alertsData.alerts.length / 10)"
                        class="px-3 py-1 text-sm bg-white border border-gray-300 rounded hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed">
                  Siguiente
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Sin alertas -->
        <div v-else-if="alertsData && alertsData.alerts?.length === 0" 
             class="bg-white rounded-lg shadow-sm border border-gray-200 p-12 text-center">
          <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
          </div>
          <h3 class="text-lg font-medium text-gray-900 mb-2">¡Todo en orden!</h3>
          <p class="text-gray-600">No hay alertas activas en este momento.</p>
        </div>
      </div>

      <!-- Vista de Predicciones -->
      <div v-if="activeSection === 'predictions'" class="space-y-4">
        
        <!-- Filtros de Predicciones -->
        <div class="bg-white rounded-lg shadow-sm p-3 border border-gray-200">
          <div class="flex items-center justify-between">
            <div class="flex items-center space-x-3">
              <span class="text-xs font-medium text-gray-700">Pronóstico para:</span>
              <select 
                v-model="predictionsFilters.forecastDays"
                @change="loadPredictionsData"
                class="px-3 py-1.5 text-xs border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
              >
                <option :value="7">7 días</option>
                <option :value="14">14 días</option>
                <option :value="30">30 días</option>
              </select>
            </div>
            
            <button 
              @click="loadPredictionsData"
              class="px-3 py-1.5 bg-white hover:bg-gray-50 text-gray-700 text-xs font-medium rounded-lg border border-gray-300 transition-colors flex items-center space-x-1.5"
            >
              <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
              </svg>
              <span>Actualizar</span>
            </button>
          </div>
        </div>

        <!-- Análisis de Tendencias -->
        <div v-if="predictionsData" class="bg-white rounded-lg shadow-sm border border-gray-200">
          <div class="bg-gray-50 border-b border-gray-200 px-4 py-3">
            <div class="flex items-center justify-between">
              <div>
                <h3 class="text-sm font-bold text-gray-900">Análisis de Tendencias</h3>
                <p class="text-xs text-gray-500 mt-0.5">Predicciones basadas en algoritmos de IA</p>
              </div>
              <span class="px-2 py-1 bg-blue-50 text-blue-700 text-xs font-medium rounded border border-blue-200">
                IA
              </span>
            </div>
          </div>
          
          <!-- Tarjetas de Tendencias -->
          <div class="grid grid-cols-1 md:grid-cols-3 gap-3 p-4">
            <!-- Tendencia Ventas -->
            <div class="bg-white rounded-lg p-3 border border-gray-200">
              <div class="flex items-center justify-between mb-2">
                <div class="flex items-center space-x-2">
                  <div class="w-8 h-8 bg-green-50 rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                  </div>
                  <h3 class="text-xs font-semibold text-gray-900">Ventas</h3>
                </div>
                <svg :class="[
                  'w-4 h-4',
                  predictionsData.trend_analysis.sales.trend === 'positive' ? 'text-green-600' : 'text-red-600'
                ]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path v-if="predictionsData.trend_analysis.sales.trend === 'positive'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                  <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"></path>
                </svg>
              </div>
              <div class="space-y-1.5 text-xs">
                <div class="flex justify-between">
                  <span class="text-gray-600">Actual:</span>
                  <span class="font-semibold">{{ formatCurrency(predictionsData.trend_analysis.sales.current) }}</span>
                </div>
                <div class="flex justify-between">
                  <span class="text-gray-600">Anterior:</span>
                  <span class="font-medium text-gray-500">{{ formatCurrency(predictionsData.trend_analysis.sales.previous) }}</span>
                </div>
                <div class="pt-1.5 border-t flex justify-between items-center">
                  <span class="text-gray-700 font-medium">Cambio:</span>
                  <span :class="[
                    'text-sm font-bold',
                    predictionsData.trend_analysis.sales.growth_percentage >= 0 ? 'text-green-600' : 'text-red-600'
                  ]">
                    {{ predictionsData.trend_analysis.sales.growth_percentage >= 0 ? '+' : '' }}{{ predictionsData.trend_analysis.sales.growth_percentage }}%
                  </span>
                </div>
              </div>
            </div>
          </div>

          <!-- Tendencia Transacciones -->
          <div class="bg-white rounded-2xl p-4 border border-gray-300 hover:shadow-lg transition-all duration-200">
            <div class="flex items-center justify-between mb-3">
              <div class="flex items-center space-x-2">
                <div class="w-8 h-8 bg-sky-50 rounded-lg flex items-center justify-center">
                  <svg class="w-4 h-4 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                  </svg>
                </div>
                <h3 class="text-sm font-bold text-gray-900">📊 Transacciones</h3>
              </div>
              <svg :class="[
                'w-5 h-5',
                predictionsData.trend_analysis.transactions.trend === 'positive' ? 'text-green-600' : 'text-red-600'
              ]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path v-if="predictionsData.trend_analysis.transactions.trend === 'positive'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"></path>
              </svg>
            </div>
            <div class="space-y-2 text-xs">
              <div class="flex justify-between">
                <span class="text-gray-600">Período actual:</span>
                <span class="font-semibold">{{ formatNumber(predictionsData.trend_analysis.transactions.current) }}</span>
              </div>
              <div class="flex justify-between">
                <span class="text-gray-600">Período anterior:</span>
                <span class="font-medium text-gray-500">{{ formatNumber(predictionsData.trend_analysis.transactions.previous) }}</span>
              </div>
              <div class="pt-2 border-t flex justify-between items-center">
                <span class="text-gray-700 font-medium">Crecimiento IA:</span>
                <span :class="[
                  'text-base font-bold',
                  predictionsData.trend_analysis.transactions.growth_percentage >= 0 ? 'text-green-600' : 'text-red-600'
                ]">
                  {{ predictionsData.trend_analysis.transactions.growth_percentage >= 0 ? '+' : '' }}{{ predictionsData.trend_analysis.transactions.growth_percentage }}%
                </span>
              </div>
            </div>
          </div>

          <!-- Tendencia Ticket Promedio -->
          <div class="bg-white rounded-2xl p-4 border border-gray-300 hover:shadow-lg transition-all duration-200">
            <div class="flex items-center justify-between mb-3">
              <div class="flex items-center space-x-2">
                <div class="w-8 h-8 bg-purple-50 rounded-lg flex items-center justify-center">
                  <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"></path>
                  </svg>
                </div>
                <h3 class="text-sm font-bold text-gray-900">🎫 Ticket Promedio</h3>
              </div>
              <svg :class="[
                'w-5 h-5',
                predictionsData.trend_analysis.average_ticket.trend === 'positive' ? 'text-green-600' : 'text-red-600'
              ]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path v-if="predictionsData.trend_analysis.average_ticket.trend === 'positive'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"></path>
              </svg>
            </div>
            <div class="space-y-2 text-xs">
              <div class="flex justify-between">
                <span class="text-gray-600">Período actual:</span>
                <span class="font-semibold">{{ formatCurrency(predictionsData.trend_analysis.average_ticket.current) }}</span>
              </div>
              <div class="flex justify-between">
                <span class="text-gray-600">Período anterior:</span>
                <span class="font-medium text-gray-500">{{ formatCurrency(predictionsData.trend_analysis.average_ticket.previous) }}</span>
              </div>
              <div class="pt-2 border-t flex justify-between items-center">
                <span class="text-gray-700 font-medium">Crecimiento IA:</span>
                <span :class="[
                  'text-base font-bold',
                  predictionsData.trend_analysis.average_ticket.growth_percentage >= 0 ? 'text-green-600' : 'text-red-600'
                ]">
                  {{ predictionsData.trend_analysis.average_ticket.growth_percentage >= 0 ? '+' : '' }}{{ predictionsData.trend_analysis.average_ticket.growth_percentage }}%
                </span>
              </div>
            </div>
          </div>
        </div>

        <!-- Predicción de Agotamiento de Stock con IA -->
        <div v-if="predictionsData && predictionsData.stock_depletion?.length > 0" 
             class="bg-white rounded-lg shadow-sm border border-gray-200">
          <div class="bg-gradient-to-r from-red-50 to-orange-50 border-b border-red-100 px-5 py-4">
            <div class="flex items-start space-x-3">
              <div class="w-10 h-10 bg-red-100 rounded-xl flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
              </div>
              <div class="flex-1">
                <h3 class="text-base font-bold text-gray-900 mb-1">⏰ Predicción de Agotamiento de Stock</h3>
                <p class="text-xs text-gray-600 leading-relaxed">
                  La IA analiza tu historial de ventas y predice cuándo se agotará cada producto. 
                  Planifica tus compras con anticipación y evita pérdidas por falta de stock.
                </p>
              </div>
              <span class="px-2 py-1 bg-red-100 text-red-700 text-xs font-semibold rounded-lg border border-red-200 flex-shrink-0">
                {{ predictionsData.stock_depletion.length }} productos
              </span>
            </div>
          </div>
          <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
              <thead class="bg-gray-50">
                <tr>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Producto</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stock Actual</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Días Restantes</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha Estimada</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Urgencia</th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200">
                <tr v-for="item in paginatedStockDepletion" :key="item.product_id"
                    class="hover:bg-gray-50">
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm font-medium text-gray-900">{{ item.product_name }}</div>
                    <div class="text-sm text-gray-500">Venta diaria: {{ item.daily_average_sales }}</div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                    {{ item.current_stock }} unidades
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                    {{ item.days_until_depletion }} días
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                    {{ formatDate(item.estimated_depletion_date) }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <span :class="[
                      'px-2 py-1 text-xs font-medium rounded-full',
                      item.urgency === 'critical' ? 'bg-red-100 text-red-800' :
                      item.urgency === 'high' ? 'bg-orange-100 text-orange-800' :
                      item.urgency === 'medium' ? 'bg-yellow-100 text-yellow-800' :
                      'bg-green-100 text-green-800'
                    ]">
                      {{ item.urgency.toUpperCase() }}
                    </span>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
          
          <!-- Paginación Stock Depletion -->
          <div v-if="predictionsData.stock_depletion.length > 10" class="bg-gray-50 px-6 py-3 border-t border-gray-200">
            <div class="flex items-center justify-between">
              <div class="text-sm text-gray-700">
                Mostrando {{ ((predictionsCurrentPage - 1) * 10) + 1 }} - {{ Math.min(predictionsCurrentPage * 10, predictionsData.stock_depletion.length) }} de {{ predictionsData.stock_depletion.length }} productos
              </div>
              <div class="flex items-center space-x-2">
                <button @click="predictionsCurrentPage--" 
                        :disabled="predictionsCurrentPage <= 1"
                        class="px-3 py-1 text-sm bg-white border border-gray-300 rounded hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed">
                  Anterior
                </button>
                <span class="px-3 py-1 text-sm font-medium text-gray-900">
                  {{ predictionsCurrentPage }} / {{ Math.ceil(predictionsData.stock_depletion.length / 10) }}
                </span>
                <button @click="predictionsCurrentPage++" 
                        :disabled="predictionsCurrentPage >= Math.ceil(predictionsData.stock_depletion.length / 10)"
                        class="px-3 py-1 text-sm bg-white border border-gray-300 rounded hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed">
                  Siguiente
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Recomendaciones de Compra con IA -->
        <div v-if="predictionsData && predictionsData.purchase_recommendations?.length > 0" 
             class="bg-white rounded-lg shadow-sm border border-gray-200">
          <div class="bg-gradient-to-r from-blue-50 to-indigo-50 border-b border-blue-100 px-5 py-4">
            <div class="flex items-start space-x-3">
              <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
              </div>
              <div class="flex-1">
                <h3 class="text-base font-bold text-gray-900 mb-1">🛒 Recomendaciones Inteligentes de Compra</h3>
                <p class="text-xs text-gray-600 leading-relaxed">
                  Basado en tus patrones de venta y niveles de stock, la IA calcula exactamente cuántas unidades 
                  debes comprar de cada producto para optimizar tu inventario y costos.
                </p>
              </div>
              <span class="px-2 py-1 bg-blue-100 text-blue-700 text-xs font-semibold rounded-lg border border-blue-200 flex-shrink-0">
                {{ predictionsData.purchase_recommendations.length }} recomendaciones
              </span>
            </div>
          </div>
          <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
              <thead class="bg-gray-50">
                <tr>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Producto</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stock Actual</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Comprar</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Costo Estimado</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Prioridad</th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200">
                <tr v-for="item in paginatedPurchaseRecommendations" :key="item.product_id"
                    class="hover:bg-gray-50">
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm font-medium text-gray-900">{{ item.product_name }}</div>
                    <div class="text-sm text-gray-500">Demanda diaria: {{ item.daily_demand }}</div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                    {{ item.current_stock }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-indigo-600">
                    {{ item.recommended_purchase }} unidades
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                    {{ formatCurrency(item.estimated_cost) }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <span :class="[
                      'px-2 py-1 text-xs font-medium rounded-full',
                      item.priority === 'critical' ? 'bg-red-100 text-red-800' :
                      item.priority === 'high' ? 'bg-orange-100 text-orange-800' :
                      item.priority === 'medium' ? 'bg-yellow-100 text-yellow-800' :
                      'bg-green-100 text-green-800'
                    ]">
                      {{ item.priority.toUpperCase() }}
                    </span>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
          
          <!-- Paginación Recomendaciones -->
          <div v-if="predictionsData.purchase_recommendations.length > 10" class="bg-gray-50 px-6 py-3 border-t border-gray-200">
            <div class="flex items-center justify-between">
              <div class="text-sm text-gray-700">
                Mostrando {{ ((predictionsCurrentPage - 1) * 10) + 1 }} - {{ Math.min(predictionsCurrentPage * 10, predictionsData.purchase_recommendations.length) }} de {{ predictionsData.purchase_recommendations.length }} recomendaciones
              </div>
              <div class="flex items-center space-x-2">
                <button @click="predictionsCurrentPage--" 
                        :disabled="predictionsCurrentPage <= 1"
                        class="px-3 py-1 text-sm bg-white border border-gray-300 rounded hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed">
                  Anterior
                </button>
                <span class="px-3 py-1 text-sm font-medium text-gray-900">
                  {{ predictionsCurrentPage }} / {{ Math.ceil(predictionsData.purchase_recommendations.length / 10) }}
                </span>
                <button @click="predictionsCurrentPage++" 
                        :disabled="predictionsCurrentPage >= Math.ceil(predictionsData.purchase_recommendations.length / 10)"
                        class="px-3 py-1 text-sm bg-white border border-gray-300 rounded hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed">
                  Siguiente
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Pronóstico de Ventas con Machine Learning -->
        <div v-if="predictionsData && predictionsData.sales_forecast?.length > 0" 
             class="bg-white rounded-lg shadow-sm border border-gray-200">
          <div class="bg-gradient-to-r from-emerald-50 to-green-50 border-b border-emerald-100 px-5 py-4">
            <div class="flex items-start space-x-3">
              <div class="w-10 h-10 bg-emerald-100 rounded-xl flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"></path>
                </svg>
              </div>
              <div class="flex-1">
                <h3 class="text-base font-bold text-gray-900 mb-1">📈 Pronóstico de Ventas con Machine Learning</h3>
                <p class="text-xs text-gray-600 leading-relaxed">
                  Algoritmos de ML predicen las ventas futuras de cada producto para los próximos {{ predictionsFilters.forecastDays }} días.
                  Planifica tu producción, compras y estrategias de marketing con datos precisos.
                </p>
              </div>
              <span class="px-2 py-1 bg-emerald-100 text-emerald-700 text-xs font-semibold rounded-lg border border-emerald-200 flex-shrink-0">
                {{ predictionsFilters.forecastDays }} días
              </span>
            </div>
          </div>
          <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
              <thead class="bg-gray-50">
                <tr>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Producto</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ventas Históricas</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pronóstico</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tendencia</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Confianza</th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200">
                <tr v-for="item in paginatedSalesForecast" :key="item.product_id"
                    class="hover:bg-gray-50">
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm font-medium text-gray-900">{{ item.product_name }}</div>
                    <div class="text-sm text-gray-500">{{ item.transactions }} transacciones</div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                    {{ item.historical_sales }} unidades
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-green-600">
                    {{ Math.abs(item.forecast_sales) }} unidades
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <span :class="[
                      'flex items-center text-sm',
                      item.trend === 'growing' ? 'text-green-600' :
                      item.trend === 'declining' ? 'text-red-600' :
                      'text-gray-600'
                    ]">
                      <i :class="[
                        'fas mr-1',
                        item.trend === 'growing' ? 'fa-arrow-up' :
                        item.trend === 'declining' ? 'fa-arrow-down' :
                        'fa-minus'
                      ]"></i>
                      {{ item.trend }}
                    </span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <span :class="[
                      'px-2 py-1 text-xs font-medium rounded-full',
                      item.confidence === 'high' ? 'bg-green-100 text-green-800' :
                      'bg-yellow-100 text-yellow-800'
                    ]">
                      {{ item.confidence.toUpperCase() }}
                    </span>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
          
          <!-- Paginación Pronóstico -->
          <div v-if="predictionsData.sales_forecast.length > 10" class="bg-gray-50 px-6 py-3 border-t border-gray-200">
            <div class="flex items-center justify-between">
              <div class="text-sm text-gray-700">
                Mostrando {{ ((predictionsCurrentPage - 1) * 10) + 1 }} - {{ Math.min(predictionsCurrentPage * 10, predictionsData.sales_forecast.length) }} de {{ predictionsData.sales_forecast.length }} pronósticos
              </div>
              <div class="flex items-center space-x-2">
                <button @click="predictionsCurrentPage--" 
                        :disabled="predictionsCurrentPage <= 1"
                        class="px-3 py-1 text-sm bg-white border border-gray-300 rounded hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed">
                  Anterior
                </button>
                <span class="px-3 py-1 text-sm font-medium text-gray-900">
                  {{ predictionsCurrentPage }} / {{ Math.ceil(predictionsData.sales_forecast.length / 10) }}
                </span>
                <button @click="predictionsCurrentPage++" 
                        :disabled="predictionsCurrentPage >= Math.ceil(predictionsData.sales_forecast.length / 10)"
                        class="px-3 py-1 text-sm bg-white border border-gray-300 rounded hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed">
                  Siguiente
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Otras secciones (placeholder para las restantes) -->
      <div v-if="!['overview', 'products', 'movements', 'customers', 'alerts', 'predictions'].includes(activeSection)" class="bg-white rounded-lg shadow p-6">
        <div class="text-center py-12 text-gray-500">
          <i :class="getCurrentSectionIcon()" class="text-4xl mb-4 text-gray-300"></i>
          <p class="text-lg font-medium">{{ getCurrentSectionName() }}</p>
          <p class="text-sm">Esta sección estará disponible próximamente</p>
        </div>
      </div>
    </div>

    <!-- Componente de notificaciones toast -->
    <ToastNotifications 
      ref="toastRef"
      @action-click="handleAlertAction"
      @dismiss-forever="handleDismissForever"
    />
  </div>
</template>

<script>
import { ref, reactive, onMounted, computed } from 'vue'
import ToastNotifications from './ToastNotifications.vue'

export default {
  name: 'IntelligentInventoryView',
  components: {
    ToastNotifications
  },
  setup() {
    // Estado reactivo
    const activeSection = ref('overview')
    const connectionStatus = ref('')
    const error = ref('')
    const overviewData = ref(null)
    const selectedPeriod = ref('month')
    const toastRef = ref(null)
    
    // Rango de fechas personalizado
    const customDateRange = reactive({
      start: '',
      end: ''
    })
    
    // Datos para vista de productos
    const productsData = ref(null)
    const availableCategories = ref([])
    const availableSuppliers = ref([])
    const filters = reactive({
      category: '',
      supplier: '',
      search: '',
      currentPage: 1,
      itemsPerPage: 25
    })
    
    // Datos para vista de movimientos
    const movementsData = ref(null)
    const movementsFilters = reactive({
      type: '', // 'entry' o 'exit'
      user: '',
      product: '',
      dateRange: 'week',
      currentPage: 1,
      itemsPerPage: 25
    })
    
    // Datos para vista de clientes
    const customersData = ref(null)
    const customersFilters = reactive({
      customer: '',
      minPurchases: 1,
      sortBy: 'total_spent',
      currentPage: 1,
      itemsPerPage: 25
    })

    // Datos para vista de alertas
    const alertsData = ref(null)
    const alertsCurrentPage = ref(1)
    const alertsFilters = reactive({
      severity: '',
      category: ''
    })

    // Datos para vista de predicciones
    const predictionsData = ref(null)
    const predictionsCurrentPage = ref(1)
    const predictionsFilters = reactive({
      forecastDays: 30,
      period: 'month'
    })
    
    const metrics = reactive({
      totalProducts: 0,
      activeProducts: 0,
      lowStockProducts: 0,
      outOfStockProducts: 0,
      totalInventoryValue: 0,
      monthlySales: 0,
      averageProfitMargin: 0
    })

    // Datos adicionales para el dashboard
    const monthlyTransactions = ref(0)
    const stockVariation = ref({
      entries: 0,
      exits: 0,
      net: 0
    })

    // Configuración de secciones
    const sections = [
      { id: 'overview', name: 'Vista General', icon: 'fas fa-tachometer-alt' },
      { id: 'products', name: 'Productos', icon: 'fas fa-boxes' },
      { id: 'movements', name: 'Movimientos', icon: 'fas fa-exchange-alt' },
      { id: 'customers', name: 'Clientes', icon: 'fas fa-users' },
      { id: 'alerts', name: 'Alertas', icon: 'fas fa-bell' },
      { id: 'predictions', name: 'Predicciones', icon: 'fas fa-chart-line' }
    ]

    // Métodos
    const formatCurrency = (amount) => {
      if (!amount && amount !== 0) return '$0'
      return new Intl.NumberFormat('es-CO', {
        style: 'currency',
        currency: 'COP',
        minimumFractionDigits: 0,
        maximumFractionDigits: 0
      }).format(amount)
    }

    const formatDate = (dateString) => {
      return new Date(dateString).toLocaleDateString('es-CO', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
      })
    }

    const formatNumber = (number) => {
      if (!number && number !== 0) return '0'
      return new Intl.NumberFormat('es-CO').format(number)
    }

    const formatPercentage = (percentage) => {
      if (!percentage && percentage !== 0) return '0%'
      return new Intl.NumberFormat('es-CO', {
        style: 'percent',
        minimumFractionDigits: 1,
        maximumFractionDigits: 1
      }).format(percentage / 100)
    }

    const getChangeClass = (change) => {
      if (change > 0) return 'text-green-600'
      if (change < 0) return 'text-red-600'
      return 'text-gray-600'
    }

    const getChangeIcon = (change) => {
      if (change > 0) return 'fas fa-arrow-up'
      if (change < 0) return 'fas fa-arrow-down'
      return 'fas fa-minus'
    }

    const formatMovementType = (type) => {
      const types = {
        'sale': 'Venta',
        'purchase': 'Compra',
        'adjustment': 'Ajuste',
        'return': 'Devolución',
        'transfer': 'Transferencia'
      }
      return types[type] || type
    }

    const getMovementTypeClass = (type) => {
      const classes = {
        'sale': 'bg-red-500',
        'purchase': 'bg-green-500',
        'adjustment': 'bg-blue-500',
        'return': 'bg-yellow-500',
        'transfer': 'bg-purple-500'
      }
      return classes[type] || 'bg-gray-500'
    }

    const getMovementTypeIcon = (type) => {
      const icons = {
        'sale': 'fas fa-arrow-down',
        'purchase': 'fas fa-arrow-up',
        'adjustment': 'fas fa-edit',
        'return': 'fas fa-undo',
        'transfer': 'fas fa-exchange-alt'
      }
      return icons[type] || 'fas fa-question'
    }

    const getCurrentSectionName = () => {
      const section = sections.find(s => s.id === activeSection.value)
      return section ? section.name : 'Sección Desconocida'
    }

    const getCurrentSectionIcon = () => {
      const section = sections.find(s => s.id === activeSection.value)
      return section ? section.icon : 'fas fa-question-circle'
    }

    const testConnection = async () => {
      connectionStatus.value = ''
      error.value = ''
      overviewData.value = null


      try {
        // Primero probar la conexión básica a la API
        const response = await fetch(`${API_BASE_URL}/api/test`)
        
        if (!response.ok) {
          throw new Error(`HTTP ${response.status}: ${response.statusText}`)
        }

        const testData = await response.json()
        connectionStatus.value = `API funcionando: ${testData.message}`

        // Intentar cargar datos del inventario
        try {
          const inventoryResponse = await fetch(`${API_BASE_URL}/api/inventory/test/dashboard`)
          
          if (inventoryResponse.ok) {
            const inventoryData = await inventoryResponse.json()
            overviewData.value = inventoryData
            
            if (inventoryData.success && inventoryData.data) {
              // Actualizar métricas si están disponibles
              if (inventoryData.data.metrics) {
                Object.assign(metrics, inventoryData.data.metrics)
              }
              
              // Actualizar datos adicionales
              if (inventoryData.data.monthlyTransactions) {
                monthlyTransactions.value = inventoryData.data.monthlyTransactions
              }
              
              if (inventoryData.data.stockVariation) {
                stockVariation.value = inventoryData.data.stockVariation
              }
              
              connectionStatus.value += ' • Dashboard completo cargado'
            }
          } else {
            const errorText = await inventoryResponse.text()
            connectionStatus.value += ` • Inventario: Error ${inventoryResponse.status}`
            console.log('Respuesta de inventario:', errorText.substring(0, 200))
          }
        } catch (inventoryError) {
          connectionStatus.value += ' • Inventario: pendiente de configuración'
          console.log('Inventario aún no configurado:', inventoryError.message)
        }

      } catch (err) {
        error.value = `Error de conexión: ${err.message}`
        console.error('Error testing connection:', err)
      }
    }

    // Método para obtener etiqueta del período
    const getPeriodLabel = () => {
      const labels = {
        'day': 'de hoy',
        'week': 'de esta semana', 
        'month': 'de este mes',
        'year': 'de este año'
      }
      return labels[selectedPeriod.value] || 'del período'
    }

    // Renombrar testConnection a loadDashboardData para mayor claridad
    // Funciones para alertas automáticas
    const loadAndShowAlerts = async () => {
      // Solo mostrar alertas automáticas si estamos en la vista overview
      if (activeSection.value !== 'overview') {
        return
      }
      
      try {
        const response = await fetch(`http://localhost:8000/api/inventory/test/alerts?period=${selectedPeriod.value}`)
        const data = await response.json()
        
        if (data.success && data.data.alerts?.length > 0) {
          // Mostrar solo las alertas críticas automáticamente en la vista general
          const criticalAlerts = data.data.alerts.filter(alert => alert.severity === 'critical').slice(0, 3)
          
          criticalAlerts.forEach((alert, index) => {
            setTimeout(() => {
              // Verificar nuevamente que seguimos en overview antes de mostrar
              if (activeSection.value === 'overview' && toastRef.value) {
                toastRef.value.show({
                  ...alert,
                  autoClose: true,
                  duration: 10000 // 10 segundos
                })
              }
            }, index * 2000) // Espaciar las alertas 2 segundos
          })
        }
      } catch (err) {
        console.error('Error cargando alertas automáticas:', err)
      }
    }

    // Manejar cambio de período
    const handlePeriodChange = () => {
      // Si cambia a custom, limpiar fechas para que el usuario las seleccione
      if (selectedPeriod.value === 'custom') {
        customDateRange.start = ''
        customDateRange.end = ''
        // No cargar datos hasta que el usuario seleccione al menos una fecha de inicio
        return
      }
      
      // Para otros períodos, cargar datos según la vista activa
      switch (activeSection.value) {
        case 'overview':
          loadDashboardData()
          break
        case 'products':
          loadProductsData()
          break
        case 'movements':
          loadMovementsData()
          break
        case 'customers':
          loadCustomersData()
          break
        default:
          break
      }
    }

    const handleAlertAction = (alert) => {
      console.log('Acción de alerta:', alert)
      if (alert.action_url) {
        // Aquí podrías navegar a una página específica o abrir un modal
        console.log('Navegando a:', alert.action_url)
      }
    }

    const handleDismissForever = async (alert) => {
      try {
        const response = await fetch('http://localhost:8000/api/inventory/test/alerts/dismiss', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json'
          },
          body: JSON.stringify({
            alert_key: alert.alert_key || alert.id,
            alert_type: alert.category,
            product_id: alert.product_id,
            user_id: 1 // Default user para pruebas
          })
        })

        const data = await response.json()
        
        if (data.success) {
          console.log('Alerta descartada correctamente')
          // Opcional: mostrar mensaje de confirmación
          if (toastRef.value) {
            toastRef.value.show({
              title: 'Alerta descartada',
              message: 'No volverás a ver esta alerta para este producto',
              type: 'success',
              autoClose: true,
              duration: 3000
            })
          }
        }
      } catch (err) {
        console.error('Error descartando alerta:', err)
      }
    }

    const loadDashboardData = async () => {
      connectionStatus.value = ''
      error.value = ''
      overviewData.value = null


      try {
        // Construir URL con parámetros
        let url = `${API_BASE_URL}/api/inventory/test/dashboard?period=${selectedPeriod.value}`
        
        // Si es rango personalizado, agregar fechas
        if (selectedPeriod.value === 'custom' && customDateRange.start) {
          url += `&start_date=${customDateRange.start}`
          // Si no hay end_date, usar la misma fecha que start_date (mismo día)
          const endDate = customDateRange.end || customDateRange.start
          url += `&end_date=${endDate}`
        }
        
        // Cargar datos del dashboard
        const inventoryResponse = await fetch(url)
        
        if (inventoryResponse.ok) {
          const inventoryData = await inventoryResponse.json()
          overviewData.value = inventoryData
          
          if (inventoryData.success && inventoryData.data) {
            // Actualizar métricas
            if (inventoryData.data.metrics) {
              Object.assign(metrics, inventoryData.data.metrics)
            }
            
            // Actualizar datos adicionales
            if (inventoryData.data.monthlyTransactions) {
              monthlyTransactions.value = inventoryData.data.monthlyTransactions
            }
            
            if (inventoryData.data.stockVariation) {
              stockVariation.value = inventoryData.data.stockVariation
            }
            
            connectionStatus.value = 'Dashboard actualizado correctamente'
            
            // Las alertas automáticas ya no se cargan aquí
            // Solo se mostrarán en el dashboard principal del POS
          }
        } else {
          throw new Error(`Error ${inventoryResponse.status}`)
        }
      } catch (err) {
        error.value = `Error cargando datos: ${err.message}`
        console.error('Error loading dashboard:', err)
      }
    }

    // Método para refrescar solo los datos de overview
    const refreshOverviewData = async () => {
      await loadDashboardData()
    }

    // Cargar datos de productos
    const loadProductsData = async () => {
      try {
        const params = new URLSearchParams()
        
        params.append('period', selectedPeriod.value)
        params.append('page', filters.currentPage)
        params.append('per_page', filters.itemsPerPage)
        if (filters.category) params.append('category', filters.category)
        if (filters.supplier) params.append('supplier', filters.supplier) 
        if (filters.search) params.append('search', filters.search)
        
        // Si es rango personalizado, agregar fechas
        if (selectedPeriod.value === 'custom' && customDateRange.start) {
          params.append('start_date', customDateRange.start)
          // Si no hay end_date, usar la misma fecha que start_date (mismo día)
          const endDate = customDateRange.end || customDateRange.start
          params.append('end_date', endDate)
        }
        
        const response = await fetch(`${API_BASE_URL}/api/inventory/test/products?${params}`)
        
        if (response.ok) {
          const data = await response.json()
          if (data.success) {
            productsData.value = data.data
            availableCategories.value = data.data.filters.categories
            availableSuppliers.value = data.data.filters.suppliers
          }
        }
      } catch (error) {
        console.error('Error cargando productos:', error)
      }
    }

    // Cargar datos de movimientos
    const loadMovementsData = async () => {
      try {
        console.log('🔄 === INICIO CARGA MOVIMIENTOS ===')
        console.log('🔧 movementsData.value antes:', movementsData.value)
        console.log('📅 selectedPeriod.value:', selectedPeriod.value)
        console.log('🔍 movementsFilters:', movementsFilters)
        
        const params = new URLSearchParams()
        
        params.append('period', selectedPeriod.value)
        if (movementsFilters.type) params.append('type', movementsFilters.type)
        if (movementsFilters.user) params.append('user', movementsFilters.user)
        if (movementsFilters.product) params.append('product', movementsFilters.product)
        
        // Si es rango personalizado, agregar fechas
        if (selectedPeriod.value === 'custom' && customDateRange.start) {
          params.append('start_date', customDateRange.start)
          // Si no hay end_date, usar la misma fecha que start_date (mismo día)
          const endDate = customDateRange.end || customDateRange.start
          params.append('end_date', endDate)
        }
        
        const fullUrl = `${API_BASE_URL}/api/inventory/test/movements?${params}`
        console.log('📡 URL completa:', fullUrl)
        
        const response = await fetch(fullUrl)
        console.log('📊 Response status:', response.status)
        console.log('📊 Response ok:', response.ok)
        console.log('📊 Response headers:', Object.fromEntries(response.headers.entries()))
        
        if (response.ok) {
          const data = await response.json()
          console.log('✅ Data recibida completa:', data)
          console.log('🔑 Keys en data:', Object.keys(data))
          console.log('🔑 Keys en data.data:', Object.keys(data.data || {}))
          
          if (data.success) {
            console.log('✅ API success=true')
            console.log('📋 Movements array:', data.data.movements)
            console.log('📊 Movements length:', data.data.movements?.length)
            
            movementsData.value = data.data
            console.log('✅ movementsData.value actualizado:', movementsData.value)
            console.log('📋 movementsData.value.movements:', movementsData.value?.movements?.length)
          } else {
            console.error('❌ API returned success:false', data)
            movementsData.value = { movements: [], summary: {} }
          }
        } else {
          console.error('❌ HTTP Error:', response.status, response.statusText)
          const errorText = await response.text()
          console.error('❌ Error response:', errorText)
          movementsData.value = { movements: [], summary: {} }
        }
      } catch (error) {
        console.error('❌ Error cargando movimientos:', error)
        console.error('❌ Error stack:', error.stack)
        movementsData.value = { movements: [], summary: {} }
      }
      console.log('🔄 === FIN CARGA MOVIMIENTOS ===')
    }

    // Cargar datos de clientes
    const loadCustomersData = async () => {
      try {
        const params = new URLSearchParams()
        
        params.append('period', selectedPeriod.value)
        if (customersFilters.customer) params.append('customer', customersFilters.customer)
        if (customersFilters.minPurchases) params.append('min_purchases', customersFilters.minPurchases)
        
        // Si es rango personalizado, agregar fechas
        if (selectedPeriod.value === 'custom' && customDateRange.start) {
          params.append('start_date', customDateRange.start)
          // Si no hay end_date, usar la misma fecha que start_date (mismo día)
          const endDate = customDateRange.end || customDateRange.start
          params.append('end_date', endDate)
        }
        
        const response = await fetch(`${API_BASE_URL}/api/inventory/test/customers?${params}`)
        
        if (response.ok) {
          const data = await response.json()
          if (data.success) {
            customersData.value = data.data
          }
        }
      } catch (error) {
        console.error('Error cargando clientes:', error)
      }
    }

    // Cargar datos de alertas
    const loadAlertsData = async () => {
      try {
        const params = new URLSearchParams()
        
        params.append('period', selectedPeriod.value)
        if (alertsFilters.severity) params.append('severity', alertsFilters.severity)
        if (alertsFilters.category) params.append('category', alertsFilters.category)
        
        const response = await fetch(`${API_BASE_URL}/api/inventory/test/alerts?${params}`)
        
        if (response.ok) {
          const data = await response.json()
          if (data.success) {
            alertsData.value = data.data
          }
        }
      } catch (error) {
        console.error('Error cargando alertas:', error)
      }
    }

    // Cargar datos de predicciones
    const loadPredictionsData = async () => {
      try {
        const params = new URLSearchParams()
        
        params.append('period', selectedPeriod.value)
        params.append('forecast_days', predictionsFilters.forecastDays)
        
        const response = await fetch(`${API_BASE_URL}/api/inventory/test/predictions?${params}`)
        
        if (response.ok) {
          const data = await response.json()
          if (data.success) {
            predictionsData.value = data.data
          }
        }
      } catch (error) {
        console.error('Error cargando predicciones:', error)
      }
    }

    // Función para cambiar de sección y cargar datos correspondientes
    const switchToSection = (sectionId) => {
      activeSection.value = sectionId
      
      // Limpiar notificaciones toast al cambiar de sección (si las hubiera)
      if (toastRef.value) {
        toastRef.value.clear()
      }
      
      // Cargar datos específicos de la sección
      switch (sectionId) {
        case 'overview':
          loadDashboardData()
          break
        case 'products':
          loadProductsData()
          break
        case 'movements':
          loadMovementsData()
          break
        case 'customers':
          loadCustomersData()
          break
        case 'alerts':
          loadAlertsData()
          break
        case 'predictions':
          loadPredictionsData()
          break
        default:
          // Para otras secciones no hacer nada
          break
      }
    }

    // Exportar movimientos
    const exportMovements = () => {
      if (!movementsData.value || !movementsData.value.movements) return
      
      const csvContent = [
        'Fecha,Tipo,Producto,Cantidad,Precio Unitario,Total,Documento',
        ...movementsData.value.movements.map(m => 
          `${formatDate(m.movement_date)},${m.movement_reason},"${m.product_name}",${m.quantity},${m.unit_price},${m.total_value},"${m.document_number}"`
        )
      ].join('\n')
      
      const blob = new Blob([csvContent], { type: 'text/csv' })
      const url = window.URL.createObjectURL(blob)
      const a = document.createElement('a')
      a.href = url
      a.download = `movimientos_${selectedPeriod.value}_${new Date().toISOString().split('T')[0]}.csv`
      a.click()
      window.URL.revokeObjectURL(url)
    }

    // Exportar clientes
    const exportCustomers = () => {
      if (!customersData.value || !customersData.value.customers) return
      
      const csvContent = [
        'Cliente,Email,Total Compras,Total Gastado,Productos Únicos,Items Totales',
        ...customersData.value.customers.map(c => 
          `"${c.customer_name}","${c.email || ''}",${c.total_purchases},${c.total_spent},${c.unique_products_bought},${c.total_items_bought}`
        )
      ].join('\n')
      
      const blob = new Blob([csvContent], { type: 'text/csv' })
      const url = window.URL.createObjectURL(blob)
      const a = document.createElement('a')
      a.href = url
      a.download = `clientes_${selectedPeriod.value}_${new Date().toISOString().split('T')[0]}.csv`
      a.click()
      window.URL.revokeObjectURL(url)
    }

    // Debounce para búsqueda
    let searchTimeout = null
    const debounceSearch = () => {
      clearTimeout(searchTimeout)
      searchTimeout = setTimeout(() => {
        loadProductsData()
      }, 300)
    }

    // Limpiar filtros
    const clearFilters = () => {
      filters.search = ''
      filters.category = ''
      filters.currentPage = 1
      loadProductsData()
    }

    // Métodos de utilidad para productos
    const getStockStatusClass = (status) => {
      switch (status) {
        case 'out': return 'bg-red-100 text-red-800'
        case 'low': return 'bg-yellow-100 text-yellow-800'
        default: return 'bg-green-100 text-green-800'
      }
    }

    const getStockStatusText = (status) => {
      switch (status) {
        case 'out': return 'Sin stock'
        case 'low': return 'Stock bajo'
        default: return 'Stock OK'
      }
    }

    const getRotationClass = (days) => {
      if (days === 999) return 'bg-gray-100 text-gray-800'
      if (days > 60) return 'bg-red-100 text-red-800'
      if (days > 30) return 'bg-yellow-100 text-yellow-800'
      return 'bg-green-100 text-green-800'
    }

    // Cargar datos automáticamente al montar el componente
    onMounted(() => {
      loadDashboardData()
      loadProductsData()
      loadMovementsData()
      loadCustomersData()
    })

    // Computed properties para paginación de movimientos
    const movementsTotalPages = computed(() => {
      const totalItems = movementsData.value?.summary?.total_movements || 0
      return Math.ceil(totalItems / movementsFilters.itemsPerPage)
    })

    const movementsPaginationInfo = computed(() => {
      const totalItems = movementsData.value?.summary?.total_movements || 0
      const start = (movementsFilters.currentPage - 1) * movementsFilters.itemsPerPage + 1
      const end = Math.min(movementsFilters.currentPage * movementsFilters.itemsPerPage, totalItems)
      
      return {
        start: totalItems > 0 ? start : 0,
        end: totalItems > 0 ? end : 0,
        total: totalItems
      }
    })

    // Computed properties para paginación de clientes
    const customersTotalPages = computed(() => {
      const totalItems = customersData.value?.summary?.total_customers || 0
      return Math.ceil(totalItems / customersFilters.itemsPerPage)
    })

    const customersPaginationInfo = computed(() => {
      const totalItems = customersData.value?.summary?.total_customers || 0
      const start = (customersFilters.currentPage - 1) * customersFilters.itemsPerPage + 1
      const end = Math.min(customersFilters.currentPage * customersFilters.itemsPerPage, totalItems)
      
      return {
        start: totalItems > 0 ? start : 0,
        end: totalItems > 0 ? end : 0,
        total: totalItems
      }
    })

    // Computed properties para paginación de alertas
    const paginatedAlerts = computed(() => {
      if (!alertsData.value?.alerts) return []
      const start = (alertsCurrentPage.value - 1) * 10
      const end = start + 10
      return alertsData.value.alerts.slice(start, end)
    })

    // Computed properties para paginación de productos
    const productsTotalPages = computed(() => {
      const totalItems = productsData.value?.summary?.total_products || 0
      return Math.ceil(totalItems / filters.itemsPerPage)
    })

    const productsPaginationInfo = computed(() => {
      const totalItems = productsData.value?.summary?.total_products || 0
      const start = (filters.currentPage - 1) * filters.itemsPerPage + 1
      const end = Math.min(filters.currentPage * filters.itemsPerPage, totalItems)
      
      return {
        start: totalItems > 0 ? start : 0,
        end: totalItems > 0 ? end : 0,
        total: totalItems
      }
    })

    // Computed properties para paginación de predicciones
    const paginatedStockDepletion = computed(() => {
      if (!predictionsData.value?.stock_depletion) return []
      const start = (predictionsCurrentPage.value - 1) * 10
      const end = start + 10
      return predictionsData.value.stock_depletion.slice(start, end)
    })

    const paginatedPurchaseRecommendations = computed(() => {
      if (!predictionsData.value?.purchase_recommendations) return []
      const start = (predictionsCurrentPage.value - 1) * 10
      const end = start + 10
      return predictionsData.value.purchase_recommendations.slice(start, end)
    })

    const paginatedSalesForecast = computed(() => {
      if (!predictionsData.value?.sales_forecast) return []
      const start = (predictionsCurrentPage.value - 1) * 10
      const end = start + 10
      return predictionsData.value.sales_forecast.slice(start, end)
    })

    return {
      // Estado
      activeSection,
      connectionStatus,
      error,
      overviewData,
      selectedPeriod,
      toastRef,
      customDateRange,
      metrics,
      monthlyTransactions,
      stockVariation,
      productsData,
      availableCategories,
      availableSuppliers,
      filters,
      productsTotalPages,
      productsPaginationInfo,
      movementsData,
      movementsFilters,
      movementsTotalPages,
      movementsPaginationInfo,
      customersData,
      customersFilters,
      customersTotalPages,
      customersPaginationInfo,
      alertsData,
      alertsCurrentPage,
      alertsFilters,
      paginatedAlerts,
      predictionsData,
      predictionsCurrentPage,
      predictionsFilters,
      paginatedStockDepletion,
      paginatedPurchaseRecommendations,
      paginatedSalesForecast,
      
      // Configuración
      sections,
      
      // Métodos
      formatCurrency,
      formatDate,
      formatNumber,
      formatPercentage,
      getChangeClass,
      getChangeIcon,
      formatMovementType,
      getMovementTypeClass,
      getMovementTypeIcon,
      getCurrentSectionName,
      getCurrentSectionIcon,
      getPeriodLabel,
      handlePeriodChange,
      loadDashboardData,
      refreshOverviewData,
      loadProductsData,
      loadMovementsData,
      loadCustomersData,
      loadAlertsData,
      loadPredictionsData,
      switchToSection,
      loadAndShowAlerts,
      handleAlertAction,
      handleDismissForever,
      debounceSearch,
      clearFilters,
      exportMovements,
      exportCustomers,
      getStockStatusClass,
      getStockStatusText,
      getRotationClass,
      testConnection: loadDashboardData
    }
  }
}
</script>

<style scoped>
.inventory-intelligent-dashboard {
  min-height: 100vh;
  display: flex;
  flex-direction: column;
  background-color: #f8fafc;
}

/* Animación fade-in */
@keyframes fadeIn {
  from {
    opacity: 0;
    transform: translateY(10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.animate-fade-in {
  animation: fadeIn 0.4s ease-out;
}

/* Animaciones para las transiciones */
.fade-enter-active, .fade-leave-active {
  transition: opacity 0.3s ease;
}

.fade-enter-from, .fade-leave-to {
  opacity: 0;
}

/* Responsive design */
@media (max-width: 768px) {
  .inventory-intelligent-dashboard {
    height: auto;
    min-height: 100vh;
  }
  
  .px-6 {
    padding-left: 1rem;
    padding-right: 1rem;
  }
}
</style>