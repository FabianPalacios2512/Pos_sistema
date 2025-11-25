<template>
  <div class="min-h-screen bg-gray-100 font-sans">
    <div class="p-4 lg:p-6 space-y-4 animate-fade-in">
      
      <!-- Header Elegante Enterprise -->
      <div class="flex items-center justify-between pb-4 border-b border-gray-300">
        <div class="flex items-center space-x-4">
          <div class="w-12 h-12 bg-gradient-to-br from-blue-600 to-indigo-600 rounded-xl flex items-center justify-center shadow-lg">
            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
            </svg>
          </div>
          <div>
            <h1 class="text-2xl font-bold text-gray-900">Inventario Inteligente</h1>
            <p class="text-sm text-gray-600">Sistema de análisis predictivo y gestión avanzada</p>
          </div>
        </div>
        
        <div class="flex items-center space-x-3">
          <!-- Navegación de Secciones -->
          <div class="flex items-center space-x-1 bg-white rounded-xl p-1 border border-gray-200 shadow-sm">
            <button
              v-for="section in sections"
              :key="section.id"
              @click="switchToSection(section.id)"
              :class="[
                'px-3 py-2 text-xs font-semibold rounded-lg transition-all duration-200 flex items-center space-x-2',
                activeSection === section.id
                  ? 'bg-blue-50 text-blue-700 shadow-sm'
                  : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50'
              ]"
            >
              <i :class="section.icon"></i>
              <span>{{ section.name }}</span>
            </button>
          </div>

          <!-- Botón Actualizar -->
          <button 
            @click="refreshCurrentSection"
            class="px-4 py-2.5 bg-gradient-to-r from-sky-500 to-blue-500 hover:from-sky-600 hover:to-blue-600 text-white rounded-xl text-sm font-semibold transition-all duration-200 flex items-center space-x-2 shadow-sm hover:shadow-md"
          >
            <svg class="w-4 h-4" :class="{ 'animate-spin': connectionStatus === 'Cargando...' }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
            </svg>
            <span>Actualizar</span>
          </button>
        </div>
      </div>

      <!-- Contenido Principal -->
      <!-- Vista General -->
      <div v-if="activeSection === 'overview'" class="space-y-4">
        
        <!-- Estado de Conexión y Errores -->
        <div v-if="connectionStatus || error" class="bg-white rounded-lg shadow-sm p-3 border border-gray-200">
          <div v-if="error" class="flex items-center space-x-2 text-red-600">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <span class="text-sm font-semibold">{{ error }}</span>
          </div>
          <div v-else-if="connectionStatus === 'Cargando...'" class="flex items-center space-x-2 text-blue-600">
            <svg class="w-5 h-5 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
            </svg>
            <span class="text-sm font-semibold">{{ connectionStatus }}</span>
          </div>
          <div v-else class="flex items-center space-x-2 text-green-600">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <span class="text-sm font-semibold">{{ connectionStatus }}</span>
          </div>
        </div>
        
        <!-- Filtros de Período (Compactos) -->
        <div class="bg-white rounded-lg shadow-sm p-3 border border-gray-200">
          <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
            <span class="text-xs font-semibold text-gray-700">Período:</span>
            <div class="flex items-center gap-2 flex-wrap">
              <select 
                v-model="selectedPeriod"
                @change="handlePeriodChange"
                class="bg-white border border-gray-300 rounded-lg px-3 py-2 text-xs focus:ring-2 focus:ring-blue-500 focus:border-transparent min-w-[140px]"
              >
                <option value="day">Hoy</option>
                <option value="week">Esta Semana</option>
                <option value="month" selected>Este Mes</option>
                <option value="year">Este Año</option>
                <option value="custom">Personalizado</option>
              </select>
              
              <template v-if="selectedPeriod === 'custom'">
                <div class="relative">
                  <input
                    type="date"
                    v-model="customDateRange.start"
                    @change="loadDashboardData"
                    :max="new Date().toISOString().split('T')[0]"
                    class="bg-white border border-gray-300 rounded-lg px-3 py-2 text-xs focus:ring-2 focus:ring-blue-500 min-w-[150px]"
                  >
                </div>
              </template>
            </div>
          </div>
        </div>

        <!-- Dashboard Principal - KPIs Limpios y Profesionales -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
          <!-- PRODUCTOS ACTIVOS -->
          <div class="bg-white rounded-2xl p-5 border border-gray-200 hover:border-gray-300 transition-all duration-200 hover:shadow-lg">
            <div class="flex items-center space-x-4">
              <div class="w-12 h-12 bg-blue-50 rounded-xl flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                </svg>
              </div>
              <div class="flex-1 min-w-0">
                <div class="flex items-center justify-between mb-2">
                  <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wide">PRODUCTOS ACTIVOS</h3>
                  <span class="text-xs font-medium text-blue-700 bg-blue-100 px-2 py-1 rounded-full">
                    Stock
                  </span>
                </div>
                <p class="text-2xl font-bold text-gray-900 mb-1">{{ metrics.activeProducts || 0 }}</p>
                <p class="text-sm text-gray-500">de {{ metrics.totalProducts || 0 }} totales</p>
              </div>
            </div>
          </div>

          <!-- VALOR INVERTIDO -->
          <div class="bg-white rounded-2xl p-5 border border-gray-200 hover:border-gray-300 transition-all duration-200 hover:shadow-lg">
            <div class="flex items-center space-x-4">
              <div class="w-12 h-12 bg-green-50 rounded-xl flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
              </div>
              <div class="flex-1 min-w-0">
                <div class="flex items-center justify-between mb-2">
                  <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wide">VALOR INVERTIDO</h3>
                  <span class="text-xs font-medium text-gray-500 bg-gray-100 px-2 py-1 rounded-lg">
                    Costo
                  </span>
                </div>
                <p class="text-2xl font-bold text-gray-900 mb-1">{{ formatCurrency(metrics.totalInventoryValue) }}</p>
                <p class="text-sm text-gray-500">costo × stock</p>
              </div>
            </div>
          </div>

          <!-- VALOR POTENCIAL -->
          <div class="bg-white rounded-2xl p-5 border border-gray-200 hover:border-gray-300 transition-all duration-200 hover:shadow-lg">
            <div class="flex items-center space-x-4">
              <div class="w-12 h-12 bg-cyan-50 rounded-xl flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6 text-cyan-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                </svg>
              </div>
              <div class="flex-1 min-w-0">
                <div class="flex items-center justify-between mb-2">
                  <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wide">VALOR POTENCIAL</h3>
                  <span class="text-xs font-medium text-gray-500 bg-gray-100 px-2 py-1 rounded-lg">
                    Venta
                  </span>
                </div>
                <p class="text-2xl font-bold text-gray-900 mb-1">{{ formatCurrency(metrics.totalSaleValue || 0) }}</p>
                <p class="text-sm text-gray-500">precio × stock</p>
              </div>
            </div>
          </div>

          <!-- GANANCIA ESTIMADA -->
          <div class="bg-white rounded-2xl p-5 border border-gray-200 hover:border-gray-300 transition-all duration-200 hover:shadow-lg">
            <div class="flex items-center space-x-4">
              <div class="w-12 h-12 bg-purple-50 rounded-xl flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
              </div>
              <div class="flex-1 min-w-0">
                <div class="flex items-center justify-between mb-2">
                  <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wide">GANANCIA EST.</h3>
                  <span class="text-xs font-medium text-gray-500 bg-gray-100 px-2 py-1 rounded-lg">
                    Margen
                  </span>
                </div>
                <p class="text-2xl font-bold text-gray-900 mb-1">{{ formatCurrency((metrics.totalSaleValue || 0) - (metrics.totalInventoryValue || 0)) }}</p>
                <p class="text-sm text-gray-500">venta - costo</p>
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
                  <!-- ICONO FLECHA SIN FONDO (Visual Polish - Movimientos) -->
                  <div class="flex items-center justify-center w-7 h-7 flex-shrink-0">
                    <!-- Flecha Verde hacia ABAJO para Entrada/Compra -->
                    <svg v-if="movement.type === 'purchase'" class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 14l-7 7m0 0l-7-7m7 7V3" />
                    </svg>
                    <!-- Flecha Roja hacia ARRIBA para Salida/Venta -->
                    <svg v-else-if="movement.type === 'sale'" class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 10l7-7m0 0l7 7m-7-7v18" />
                    </svg>
                    <!-- Ícono genérico para otros tipos -->
                    <svg v-else class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
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
              <div class="relative">
                <input
                  type="date"
                  v-model="customDateRange.start"
                  @change="loadProductsData"
                  :max="new Date().toISOString().split('T')[0]"
                  class="pl-3 pr-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 min-w-[150px]"
                >
              </div>
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
        <!-- Grid Principal de KPIs - Estilo Limpio como Panel de Control -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
          <!-- TOTAL PRODUCTOS -->
          <div class="bg-white rounded-2xl p-5 border border-gray-200 hover:border-gray-300 transition-all duration-200 hover:shadow-lg">
            <div class="flex items-center space-x-4">
              <div class="w-12 h-12 bg-blue-50 rounded-xl flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                </svg>
              </div>
              <div class="flex-1 min-w-0">
                <div class="flex items-center justify-between mb-2">
                  <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wide">TOTAL PRODUCTOS</h3>
                  <span class="text-xs font-medium text-gray-500 bg-gray-100 px-2 py-1 rounded-lg">
                    en el sistema
                  </span>
                </div>
                <p class="text-2xl font-bold text-gray-900 mb-1">{{ productsMetrics.total }}</p>
              </div>
            </div>
          </div>

          <!-- ACTIVOS -->
          <div class="bg-white rounded-2xl p-5 border border-gray-200 hover:border-gray-300 transition-all duration-200 hover:shadow-lg">
            <div class="flex items-center space-x-4">
              <div class="w-12 h-12 bg-green-50 rounded-xl flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
              </div>
              <div class="flex-1 min-w-0">
                <div class="flex items-center justify-between mb-2">
                  <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wide">ACTIVOS</h3>
                  <span class="text-xs font-medium text-green-700 bg-green-100 px-2 py-1 rounded-full">
                    disponibles
                  </span>
                </div>
                <p class="text-2xl font-bold text-gray-900 mb-1">{{ productsMetrics.active }}</p>
              </div>
            </div>
          </div>

          <!-- STOCK BAJO -->
          <div class="bg-white rounded-2xl p-5 border border-gray-200 hover:border-gray-300 transition-all duration-200 hover:shadow-lg">
            <div class="flex items-center space-x-4">
              <div class="w-12 h-12 bg-yellow-50 rounded-xl flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>
              </div>
              <div class="flex-1 min-w-0">
                <div class="flex items-center justify-between mb-2">
                  <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wide">STOCK BAJO</h3>
                  <span class="text-xs font-medium text-red-700 bg-red-100 px-2 py-1 rounded-full">
                    alertas
                  </span>
                </div>
                <p class="text-2xl font-bold text-gray-900 mb-1">{{ productsMetrics.lowStock }}</p>
              </div>
            </div>
          </div>

          <!-- VALOR TOTAL -->
          <div class="bg-white rounded-2xl p-5 border border-gray-200 hover:border-gray-300 transition-all duration-200 hover:shadow-lg">
            <div class="flex items-center space-x-4">
              <div class="w-12 h-12 bg-purple-50 rounded-xl flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
              </div>
              <div class="flex-1 min-w-0">
                <div class="flex items-center justify-between mb-2">
                  <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wide">VALOR TOTAL</h3>
                  <span class="text-xs font-medium text-gray-500 bg-gray-100 px-2 py-1 rounded-lg">
                    Inventario
                  </span>
                </div>
                <p class="text-2xl font-bold text-gray-900 mb-1">{{ formatCurrency(productsMetrics.totalValueSale) }}</p>
                <p class="text-sm text-gray-500">Costo: {{ formatCurrency(productsMetrics.totalValueCost) }}</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Tabla de Productos -->
        <div v-if="productsData && productsData.products && productsData.products.length > 0" class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
          <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50">
            <div class="flex items-center justify-between">
              <div>
                <h3 class="text-base font-bold text-gray-900">Lista de Productos</h3>
                <p class="text-xs text-gray-500 mt-1">{{ productsData.products.length }} productos encontrados</p>
              </div>
            </div>
          </div>
          
          <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
              <thead class="bg-gray-50/50">
                <tr>
                  <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wide">Producto</th>
                  <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wide">Categoría</th>
                  <th class="px-4 py-3 text-center text-xs font-bold text-gray-600 uppercase tracking-wide">Stock</th>
                  <th class="px-4 py-3 text-center text-xs font-bold text-gray-600 uppercase tracking-wide">Precio</th>
                  <th class="px-4 py-3 text-center text-xs font-bold text-gray-600 uppercase tracking-wide">Rotación</th>
                  <th class="px-4 py-3 text-center text-xs font-bold text-gray-600 uppercase tracking-wide">Rentabilidad</th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200">
                <tr v-for="product in productsData.products" :key="product.id"
                    class="hover:bg-gray-50 transition-colors">
                  <td class="px-4 py-3">
                    <div class="text-sm font-semibold text-gray-900">{{ product.name }}</div>
                    <div class="text-xs text-gray-500 mt-0.5">SKU: {{ product.sku || 'N/A' }}</div>
                  </td>
                  <td class="px-4 py-3">
                    <span class="inline-flex items-center px-2.5 py-1 bg-blue-50 text-blue-700 text-xs font-semibold rounded-lg border border-blue-100">
                      {{ product.category_name || product.category || 'Sin categoría' }}
                    </span>
                  </td>
                  <td class="px-4 py-3 text-center">
                    <div class="font-mono text-base font-bold" :class="[
                      product.current_stock <= 0 ? 'text-red-600' :
                      product.current_stock <= product.min_stock ? 'text-amber-600' :
                      'text-gray-900'
                    ]">
                      {{ product.current_stock }}
                    </div>
                    <div class="text-xs text-gray-500 mt-0.5">{{ product.unit || 'unidades' }}</div>
                  </td>
                  <td class="px-4 py-3 text-center">
                    <div class="font-mono text-base font-bold text-gray-900">{{ formatCurrency(product.sale_price) }}</div>
                    <div class="text-xs text-gray-500 mt-0.5">Costo: {{ formatCurrency(product.cost_price) }}</div>
                  </td>
                  <td class="px-4 py-3 text-center">
                    <span :class="[
                      'px-2.5 py-1 text-xs font-bold rounded-full',
                      product.rotation_class === 'A' ? 'bg-green-100 text-green-700 border border-green-300' :
                      product.rotation_class === 'B' ? 'bg-blue-100 text-blue-700 border border-blue-300' :
                      'bg-amber-100 text-amber-700 border border-amber-300'
                    ]">
                      Clase {{ product.rotation_class || 'C' }}
                    </span>
                    <div class="text-xs text-gray-500 mt-1">{{ product.units_sold || 0 }} unidades vendidas</div>
                  </td>
                  <td class="px-4 py-3 text-center">
                    <div class="font-mono text-lg font-bold" :class="[
                      parseFloat(product.margin_percentage || 0) >= 40 ? 'text-green-600' :
                      parseFloat(product.margin_percentage || 0) >= 20 ? 'text-blue-600' :
                      'text-amber-600'
                    ]">
                      {{ parseFloat(product.margin_percentage || 0).toFixed(1) }}%
                    </div>
                    <div class="text-xs text-gray-500 mt-0.5">margen</div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
          
          <!-- Paginación -->
          <div v-if="productsData.pagination && productsData.pagination.total > filters.itemsPerPage" class="bg-gray-50 border-t border-gray-200 px-4 py-3">
            <div class="flex items-center justify-between">
              <div class="text-sm text-gray-700">
                Mostrando {{ ((filters.currentPage - 1) * filters.itemsPerPage) + 1 }} - 
                {{ Math.min(filters.currentPage * filters.itemsPerPage, productsData.pagination.total) }} 
                de {{ productsData.pagination.total }} productos
              </div>
              <div class="flex items-center space-x-2">
                <button @click="filters.currentPage--; loadProductsData()" 
                        :disabled="filters.currentPage <= 1"
                        class="px-3 py-1.5 text-sm bg-white border border-gray-300 rounded-lg hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
                  Anterior
                </button>
                <span class="px-3 py-1.5 text-sm font-medium text-gray-900">
                  {{ filters.currentPage }} / {{ Math.ceil(productsData.pagination.total / filters.itemsPerPage) }}
                </span>
                <button @click="filters.currentPage++; loadProductsData()" 
                        :disabled="filters.currentPage >= Math.ceil(productsData.pagination.total / filters.itemsPerPage)"
                        class="px-3 py-1.5 text-sm bg-white border border-gray-300 rounded-lg hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
                  Siguiente
                </button>
              </div>
            </div>
          </div>
        </div>
        
        <!-- Estado Vacío -->
        <div v-else-if="productsData && productsData.products && productsData.products.length === 0" class="bg-white rounded-2xl shadow-sm border border-gray-200 p-12">
          <div class="text-center">
            <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
            </svg>
            <h3 class="text-lg font-semibold text-gray-900 mb-2">No hay productos</h3>
            <p class="text-sm text-gray-500">No se encontraron productos con los filtros seleccionados.</p>
          </div>
        </div>
        
        <!-- Cargando -->
        <div v-else class="bg-white rounded-2xl shadow-sm border border-gray-200 p-12">
          <div class="text-center">
            <svg class="w-16 h-16 mx-auto text-blue-500 animate-spin mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
            </svg>
            <h3 class="text-lg font-semibold text-gray-900 mb-2">Cargando productos...</h3>
            <p class="text-sm text-gray-500">Por favor espera un momento.</p>
          </div>
        </div>
      </div>

      <div v-if="activeSection === 'movements'" class="space-y-6 animate-fade-in">
        <!-- Header y Filtros -->
        <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h2 class="text-lg font-bold text-gray-900">Movimientos de Inventario</h2>
                    <p class="text-sm text-gray-500">Registro detallado de entradas y salidas</p>
                </div>
                
                <div class="flex flex-wrap items-center gap-3">
                    <!-- Selector de Período -->
                    <select 
                      v-model="selectedPeriod"
                      @change="handlePeriodChange"
                      class="bg-gray-50 border border-gray-200 text-gray-700 text-sm rounded-xl focus:ring-blue-500 focus:border-blue-500 block p-2.5"
                    >
                      <option value="day">Hoy</option>
                      <option value="week">Esta Semana</option>
                      <option value="month">Este Mes</option>
                      <option value="year">Este Año</option>
                      <option value="custom">Personalizado</option>
                    </select>
                    
                    <!-- Campos de fecha personalizada -->
                    <template v-if="selectedPeriod === 'custom'">
                      <div class="relative">
                        <input
                          type="date"
                          v-model="customDateRange.start"
                          @change="loadMovementsData"
                          :max="new Date().toISOString().split('T')[0]"
                          class="bg-gray-50 border border-gray-200 text-gray-700 text-sm rounded-xl focus:ring-blue-500 focus:border-blue-500 block p-2.5 min-w-[150px]"
                        >
                      </div>
                    </template>
        
                    <!-- Filtro por Tipo -->
                    <select 
                      v-model="movementsFilters.type"
                      @change="loadMovementsData"
                      class="bg-gray-50 border border-gray-200 text-gray-700 text-sm rounded-xl focus:ring-blue-500 focus:border-blue-500 block p-2.5"
                    >
                      <option value="">Todos los tipos</option>
                      <option value="entry">Solo Entradas</option>
                      <option value="exit">Solo Salidas</option>
                    </select>
        
                    <!-- Botón Exportar -->
                    <button 
                      @click="exportMovements"
                      class="text-white bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-600 hover:to-teal-600 focus:ring-4 focus:ring-emerald-300 font-medium rounded-xl text-sm px-5 py-2.5 text-center inline-flex items-center transition-all duration-200 shadow-sm hover:shadow-md"
                    >
                      <i class="fas fa-file-excel mr-2"></i>
                      Exportar
                    </button>
                </div>
            </div>
        </div>

        <!-- Tarjetas de Resumen - Diseño Limpio -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4">
          <!-- Total Movimientos -->
          <div class="bg-white rounded-2xl p-4 border border-gray-200 hover:border-gray-300 transition-all duration-200 hover:shadow-lg">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-blue-50 rounded-lg flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-exchange-alt text-blue-600"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Movimientos</p>
                    <p class="text-xl font-bold text-gray-900">{{ movementsData?.summary?.total_movements || 0 }}</p>
                </div>
            </div>
          </div>

          <!-- Entradas -->
          <div class="bg-white rounded-2xl p-4 border border-gray-200 hover:border-gray-300 transition-all duration-200 hover:shadow-lg">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-green-50 rounded-lg flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-arrow-down text-green-600"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Entradas</p>
                    <p class="text-xl font-bold text-gray-900">{{ movementsData?.summary?.total_entries || 0 }}</p>
                </div>
            </div>
          </div>

          <!-- Salidas -->
          <div class="bg-white rounded-2xl p-4 border border-gray-200 hover:border-gray-300 transition-all duration-200 hover:shadow-lg">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-red-50 rounded-lg flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-arrow-up text-red-600"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Salidas</p>
                    <p class="text-xl font-bold text-gray-900">{{ movementsData?.summary?.total_exits || 0 }}</p>
                </div>
            </div>
          </div>

          <!-- Valor Entradas -->
          <div class="bg-white rounded-2xl p-4 border border-gray-200 hover:border-gray-300 transition-all duration-200 hover:shadow-lg">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-teal-50 rounded-lg flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-dollar-sign text-teal-600"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">V. Entradas</p>
                    <p class="text-lg font-bold text-gray-900">{{ formatCurrency(movementsData?.summary?.total_entry_value || 0) }}</p>
                </div>
            </div>
          </div>

          <!-- Valor Salidas -->
          <div class="bg-white rounded-2xl p-4 border border-gray-200 hover:border-gray-300 transition-all duration-200 hover:shadow-lg">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-orange-50 rounded-lg flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-dollar-sign text-orange-600"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">V. Salidas</p>
                    <p class="text-lg font-bold text-gray-900">{{ formatCurrency(movementsData?.summary?.total_exit_value || 0) }}</p>
                </div>
            </div>
          </div>

          <!-- Movimiento Neto -->
          <div class="bg-white rounded-2xl p-4 border border-gray-200 hover:border-gray-300 transition-all duration-200 hover:shadow-lg">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-purple-50 rounded-lg flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-chart-line text-purple-600"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Balance</p>
                    <p class="text-lg font-bold" :class="(movementsData?.summary?.net_movement || 0) >= 0 ? 'text-gray-900' : 'text-red-600'">
                      {{ formatCurrency(movementsData?.summary?.net_movement || 0) }}
                    </p>
                </div>
            </div>
          </div>
        </div>

        <!-- Tabla de Movimientos -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
          <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50 flex items-center justify-between">
            <div>
                <h3 class="text-lg font-bold text-gray-900">Historial de Movimientos</h3>
                <p class="text-xs text-gray-500 mt-1">Registro cronológico de operaciones</p>
            </div>
            <div class="flex items-center space-x-2">
                <span class="px-2 py-1 bg-blue-50 text-blue-700 text-xs font-semibold rounded-lg border border-blue-100">
                    {{ movementsData?.movements?.length || 0 }} registros
                </span>
            </div>
          </div>
          <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
              <thead class="bg-gray-50">
                <tr>
                  <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wide">Fecha</th>
                  <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wide">Flujo</th>
                  <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wide">Tipo Movimiento</th>
                  <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wide">Producto</th>
                  <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wide">Cantidad</th>
                  <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wide">Precio Unit.</th>
                  <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wide">Total</th>
                  <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wide">Fuente</th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200">
                <!-- Movimientos con iconografía de flujo -->
                <tr v-for="movement in movementsData?.movements || []" :key="movement.movement_id" 
                    class="hover:bg-gray-50 transition-colors duration-150"
                    :class="movement.movement_type === 'entry' ? 'border-l-4 border-l-green-500' : 'border-l-4 border-l-red-500'">
                  
                  <!-- Fecha -->
                  <td class="px-4 py-3 whitespace-nowrap">
                    <div class="flex items-center space-x-2">
                      <i class="fas fa-calendar-day text-gray-400 text-xs"></i>
                      <span class="text-sm text-gray-700 font-medium">{{ formatDate(movement.movement_date) }}</span>
                    </div>
                  </td>
                  
                  <!-- FLUJO - Flechas SVG Minimalistas -->
                  <td class="px-4 py-3 whitespace-nowrap">
                    <!-- Entrada: Flecha hacia abajo-izquierda (Verde Esmeralda) -->
                    <svg v-if="movement.movement_type === 'entry'" 
                         class="w-6 h-6 text-emerald-600" 
                         fill="none" 
                         stroke="currentColor" 
                         viewBox="0 0 24 24"
                         title="Entrada">
                      <path stroke-linecap="round" 
                            stroke-linejoin="round" 
                            stroke-width="2.5" 
                            d="M19 14l-7 7m0 0l-7-7m7 7V3"></path>
                    </svg>
                    <!-- Salida: Flecha hacia arriba-derecha (Rojo Suave) -->
                    <svg v-else-if="movement.movement_type === 'exit'" 
                         class="w-6 h-6 text-red-500" 
                         fill="none" 
                         stroke="currentColor" 
                         viewBox="0 0 24 24"
                         title="Salida">
                      <path stroke-linecap="round" 
                            stroke-linejoin="round" 
                            stroke-width="2.5" 
                            d="M5 10l7-7m0 0l7 7m-7-7v18"></path>
                    </svg>
                    <!-- Ajuste: Flecha bidireccional -->
                    <svg v-else 
                         class="w-6 h-6 text-blue-500" 
                         fill="none" 
                         stroke="currentColor" 
                         viewBox="0 0 24 24"
                         title="Ajuste">
                      <path stroke-linecap="round" 
                            stroke-linejoin="round" 
                            stroke-width="2.5" 
                            d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"></path>
                    </svg>
                  </td>
                  
                  <!-- Tipo de Movimiento - Texto Limpio -->
                  <td class="px-4 py-3 whitespace-nowrap">
                    <span class="text-sm font-medium text-gray-700">{{ movement.movement_reason }}</span>
                  </td>
                  
                  <!-- Producto - Solo Nombre -->
                  <td class="px-4 py-3">
                    <span class="text-sm font-medium text-gray-900 truncate max-w-xs">{{ movement.product_name }}</span>
                  </td>
                  
                  <!-- CANTIDAD - Con formato de color -->
                  <td class="px-4 py-3 whitespace-nowrap">
                    <div class="flex items-center space-x-2">
                      <!-- Signo visual -->
                      <span v-if="movement.movement_type === 'entry'" 
                            class="inline-flex items-center justify-center w-6 h-6 bg-green-100 text-green-700 rounded-full text-xs font-bold">
                        +
                      </span>
                      <span v-else-if="movement.movement_type === 'exit'" 
                            class="inline-flex items-center justify-center w-6 h-6 bg-red-100 text-red-700 rounded-full text-xs font-bold">
                        -
                      </span>
                      <span v-else 
                            class="inline-flex items-center justify-center w-6 h-6 bg-blue-100 text-blue-700 rounded-full text-xs font-bold">
                        =
                      </span>
                      
                      <!-- Número con color -->
                      <span class="text-base font-bold" 
                            :class="movement.movement_type === 'entry' 
                              ? 'text-green-600' 
                              : movement.movement_type === 'exit' 
                                ? 'text-red-600' 
                                : 'text-blue-600'">
                        {{ Math.abs(movement.quantity) }}
                      </span>
                    </div>
                  </td>
                  
                  <!-- Precio Unitario -->
                  <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600 font-medium">
                    {{ formatCurrency(movement.unit_price) }}
                  </td>
                  
                  <!-- Total con color -->
                  <td class="px-4 py-3 whitespace-nowrap">
                    <span class="text-sm font-bold" 
                          :class="movement.movement_type === 'entry' 
                            ? 'text-green-600' 
                            : movement.movement_type === 'exit' 
                              ? 'text-red-600' 
                              : 'text-blue-600'">
                      {{ formatCurrency(Math.abs(movement.total_value)) }}
                    </span>
                  </td>
                  
                  <!-- FUENTE - Enlace Sutil -->
                  <td class="px-4 py-3 whitespace-nowrap">
                    <button v-if="movement.document_number && movement.document_number !== 'N/A'"
                            @click="viewMovementDocument(movement)"
                            class="text-sm font-mono text-slate-600 hover:text-slate-900 hover:underline decoration-slate-400 decoration-1 underline-offset-2 transition-all duration-150 cursor-pointer">
                      {{ movement.document_number }}
                    </button>
                    <span v-else class="text-sm text-gray-400 italic">
                      —
                    </span>
                  </td>
                </tr>
                
                <!-- Estado sin movimientos -->
                <tr v-if="!movementsData?.movements || movementsData.movements.length === 0">
                  <td colspan="7" class="px-6 py-12 text-center">
                    <div class="flex flex-col items-center justify-center py-8">
                      <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mb-4">
                        <i class="fas fa-box-open text-gray-400 text-2xl"></i>
                      </div>
                      <h3 class="text-lg font-bold text-gray-900 mb-1">No hay movimientos</h3>
                      <p class="text-sm text-gray-500">No se encontraron registros con los filtros seleccionados</p>
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
                  <select v-model="movementsFilters.itemsPerPage" 
                          @change="movementsFilters.currentPage = 1; loadMovementsData()"
                          class="text-sm border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="10">10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                  </select>
                  <span class="text-sm text-gray-700">por página</span>
                </div>
                
                <div class="text-sm text-gray-700">
                  Mostrando {{ movementsPaginationInfo.start }} a {{ movementsPaginationInfo.end }} de {{ movementsPaginationInfo.total }}
                </div>
              </div>
              
              <div class="flex items-center space-x-1">
                <button @click="movementsFilters.currentPage--; loadMovementsData()" 
                        :disabled="movementsFilters.currentPage === 1"
                        class="p-1.5 text-gray-500 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
                  <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                  </svg>
                </button>
                <span class="px-2.5 py-1 text-xs font-medium text-gray-900">
                  {{ movementsFilters.currentPage }} / {{ movementsTotalPages }}
                </span>
                <button @click="movementsFilters.currentPage++; loadMovementsData()" 
                        :disabled="movementsFilters.currentPage >= movementsTotalPages"
                        class="p-1.5 text-gray-500 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
                  <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                  </svg>
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Vista por Cliente -->
      <div v-if="activeSection === 'customers'" class="space-y-6 animate-fade-in">
        <!-- Header y Filtros -->
        <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h2 class="text-lg font-bold text-gray-900">Análisis por Cliente</h2>
                    <p class="text-sm text-gray-500">Comportamiento y valor de clientes</p>
                </div>
                
                <div class="flex flex-wrap items-center gap-3">
                    <!-- Selector de Período -->
                    <select 
                      v-model="selectedPeriod"
                      @change="handlePeriodChange"
                      class="bg-gray-50 border border-gray-200 text-gray-700 text-sm rounded-xl focus:ring-blue-500 focus:border-blue-500 block p-2.5"
                    >
                      <option value="day">Hoy</option>
                      <option value="week">Esta Semana</option>
                      <option value="month">Este Mes</option>
                      <option value="year">Este Año</option>
                      <option value="custom">Personalizado</option>
                    </select>
                    
                    <!-- Campos de fecha personalizada -->
                    <template v-if="selectedPeriod === 'custom'">
                      <div class="relative">
                        <input
                          type="date"
                          v-model="customDateRange.start"
                          @change="loadCustomersData"
                          :max="new Date().toISOString().split('T')[0]"
                          class="bg-gray-50 border border-gray-200 text-gray-700 text-sm rounded-xl focus:ring-blue-500 focus:border-blue-500 block p-2.5 min-w-[150px]"
                        >
                      </div>
                    </template>
        
                    <!-- Ordenar por -->
                    <select 
                      v-model="customersFilters.sortBy"
                      @change="loadCustomersData"
                      class="bg-gray-50 border border-gray-200 text-gray-700 text-sm rounded-xl focus:ring-blue-500 focus:border-blue-500 block p-2.5"
                    >
                      <option value="total_spent">Valor Total</option>
                      <option value="total_purchases">Más Compras</option>
                      <option value="unique_products_bought">Más Productos</option>
                    </select>
        
                    <!-- Botón Exportar -->
                    <button 
                      @click="exportCustomers"
                      class="text-white bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-600 hover:to-teal-600 focus:ring-4 focus:ring-emerald-300 font-medium rounded-xl text-sm px-5 py-2.5 text-center inline-flex items-center transition-all duration-200 shadow-sm hover:shadow-md"
                    >
                      <i class="fas fa-file-excel mr-2"></i>
                      Exportar
                    </button>
                </div>
            </div>
        </div>

        <!-- Tarjetas de Resumen -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4" v-if="customersData">
          <!-- Total Clientes -->
          <div class="bg-white rounded-2xl p-4 border border-gray-200 hover:border-gray-300 transition-all duration-200 hover:shadow-lg">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-blue-50 rounded-lg flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-users text-blue-600"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Clientes</p>
                    <p class="text-xl font-bold text-gray-900">{{ customersData.summary.total_customers }}</p>
                </div>
            </div>
          </div>

          <!-- Ingresos Totales -->
          <div class="bg-white rounded-2xl p-4 border border-gray-200 hover:border-gray-300 transition-all duration-200 hover:shadow-lg">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-green-50 rounded-lg flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-dollar-sign text-green-600"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Ingresos</p>
                    <p class="text-lg font-bold text-gray-900">{{ formatCurrency(customersData.summary.total_revenue) }}</p>
                </div>
            </div>
          </div>

          <!-- Ganancia Total -->
          <div class="bg-white rounded-2xl p-4 border border-gray-200 hover:border-gray-300 transition-all duration-200 hover:shadow-lg">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-teal-50 rounded-lg flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-chart-line text-teal-600"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Ganancia</p>
                    <p class="text-lg font-bold text-gray-900">{{ formatCurrency(customersData.summary.total_profit) }}</p>
                </div>
            </div>
          </div>

          <!-- Valor Promedio -->
          <div class="bg-white rounded-2xl p-4 border border-gray-200 hover:border-gray-300 transition-all duration-200 hover:shadow-lg">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-purple-50 rounded-lg flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-user-star text-purple-600"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Promedio</p>
                    <p class="text-lg font-bold text-gray-900">{{ formatCurrency(customersData.summary.avg_customer_value) }}</p>
                </div>
            </div>
          </div>

          <!-- Descuento Prom. -->
          <div class="bg-white rounded-2xl p-4 border border-gray-200 hover:border-gray-300 transition-all duration-200 hover:shadow-lg">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-orange-50 rounded-lg flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-percentage text-orange-600"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Descuento</p>
                    <p class="text-lg font-bold text-gray-900">{{ formatPercentage(customersData.summary.avg_discount) }}</p>
                </div>
            </div>
          </div>

          <!-- Cliente Top -->
          <div class="bg-white rounded-2xl p-4 border border-gray-200 hover:border-gray-300 transition-all duration-200 hover:shadow-lg">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-indigo-50 rounded-lg flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-crown text-indigo-600"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Top Cliente</p>
                    <p class="text-sm font-bold text-gray-900 truncate" v-if="customersData.summary.top_customer">
                      {{ customersData.summary.top_customer.name }}
                    </p>
                    <p class="text-sm font-bold text-gray-900" v-else>N/A</p>
                </div>
            </div>
          </div>
        </div>

        <!-- Tabla de Clientes -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
          <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50 flex items-center justify-between">
            <div>
                <h3 class="text-lg font-bold text-gray-900">Análisis de Clientes</h3>
                <p class="text-xs text-gray-500 mt-1">Ranking y comportamiento de compra</p>
            </div>
            <div class="flex items-center space-x-2">
                <span class="px-2 py-1 bg-indigo-50 text-indigo-700 text-xs font-semibold rounded-lg border border-indigo-100">
                    {{ customersData?.customers?.length || 0 }} clientes
                </span>
            </div>
          </div>
          <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100">
              <thead class="bg-gray-50/50">
                <tr>
                  <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Cliente</th>
                  <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Total Compras</th>
                  <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Total Gastado</th>
                  <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Productos Únicos</th>
                  <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Items Totales</th>
                  <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Frecuencia</th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-100" v-if="customersData && customersData.customers">
                <tr v-for="customer in customersData.customers" :key="customer.customer_id" class="hover:bg-gray-50/50 transition-colors">
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center">
                      <!-- AVATAR CON INICIALES (Visual Polish - Clientes) -->
                      <div class="w-9 h-9 rounded-full flex items-center justify-center shadow-sm border border-gray-200"
                           style="background-color: #F1F5F9;">
                        <span class="text-xs font-bold" style="color: #475569;">
                          {{ getInitials(customer.customer_name) }}
                        </span>
                      </div>
                      <div class="ml-3">
                        <div class="text-sm font-bold text-gray-900">{{ customer.customer_name }}</div>
                        <div class="text-xs text-gray-500" v-if="customer.email">{{ customer.email }}</div>
                      </div>
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-50 text-blue-700 border border-blue-100">
                      {{ customer.total_purchases }} compras
                    </span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-emerald-600">
                    {{ formatCurrency(customer.total_spent) }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                    {{ customer.unique_products_bought }} productos
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                    {{ customer.total_items_bought }} items
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center">
                        <div class="text-sm font-medium text-gray-900 mr-2">
                          {{ Math.round((customer.total_items_bought / customer.total_purchases) * 10) / 10 }}
                        </div>
                        <span class="text-xs text-gray-500">items/compra</span>
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
                  Mostrando {{ customersPaginationInfo.start }} a {{ customersPaginationInfo.end }} de {{ customersPaginationInfo.total }}
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
                        :disabled="customersFilters.currentPage >= customersTotalPages"
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

      <!-- Vista de Proveedores -->
      <div v-if="activeSection === 'suppliers'" class="space-y-6 animate-fade-in">
        
        <!-- Tarjetas de Resumen -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
          <!-- Total Proveedores -->
          <div class="bg-white rounded-2xl p-5 border border-gray-300 hover:border-gray-400 transition-all duration-200 hover:shadow-lg">
            <div class="flex items-center space-x-4">
              <div class="w-12 h-12 bg-gray-100 rounded-xl flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
              </div>
              <div class="flex-1 min-w-0">
                <div class="flex items-center justify-between mb-2">
                  <h3 class="text-sm font-semibold text-gray-700">Total Proveedores</h3>
                </div>
                <p class="text-2xl font-bold text-gray-900 mb-1">{{ suppliersData?.summary?.total_suppliers || 0 }}</p>
                <p class="text-sm text-gray-500">{{ suppliersData?.summary?.active_suppliers || 0 }} activos</p>
              </div>
            </div>
          </div>

          <!-- Cuentas por Pagar -->
          <div class="bg-white rounded-2xl p-5 border border-gray-300 hover:border-gray-400 transition-all duration-200 hover:shadow-lg">
            <div class="flex items-center space-x-4">
              <div class="w-12 h-12 bg-red-50 rounded-xl flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
              </div>
              <div class="flex-1 min-w-0">
                <div class="flex items-center justify-between mb-2">
                  <h3 class="text-sm font-semibold text-gray-700">Cuentas por Pagar</h3>
                </div>
                <p class="text-2xl font-bold text-gray-900 mb-1">{{ formatCurrency(suppliersData?.summary?.total_debt || 0) }}</p>
                <p class="text-sm text-gray-500">Dinero que debo a proveedores</p>
              </div>
            </div>
          </div>

          <!-- Mejor Proveedor -->
          <div class="bg-white rounded-2xl p-5 border border-gray-300 hover:border-gray-400 transition-all duration-200 hover:shadow-lg">
            <div class="flex items-center space-x-4">
              <div class="w-12 h-12 bg-blue-50 rounded-xl flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                </svg>
              </div>
              <div class="flex-1 min-w-0">
                <div class="flex items-center justify-between mb-2">
                  <h3 class="text-sm font-semibold text-gray-700">Mejor Proveedor</h3>
                </div>
                <p class="text-lg font-bold text-gray-900 mb-1 truncate">
                  {{ suppliersData?.best_supplier?.name || 'N/A' }}
                </p>
                <p class="text-sm text-gray-500">Al que más le compro</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Tabla de Proveedores -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
          <div class="bg-gray-50 border-b border-gray-200 px-4 py-3 flex items-center justify-between">
            <div>
              <h2 class="text-base font-bold text-gray-900">Lista de Proveedores</h2>
              <p class="text-xs text-gray-500 mt-0.5">{{ suppliersData?.suppliers?.length || 0 }} proveedores registrados</p>
            </div>
          </div>

          <div v-if="!suppliersData" class="p-12 text-center">
            <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3 animate-pulse">
              <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
              </svg>
            </div>
            <p class="text-sm font-semibold text-gray-700">Cargando proveedores...</p>
          </div>

          <div v-else-if="suppliersData?.suppliers?.length === 0" class="text-center py-12">
            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
              <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
              </svg>
            </div>
            <p class="text-sm font-semibold text-gray-700">No hay proveedores</p>
            <p class="text-xs text-gray-500 mt-1">Agrega proveedores para empezar</p>
          </div>

          <div v-else class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
              <thead class="bg-gray-50">
                <tr>
                  <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wide">Proveedor</th>
                  <th class="px-4 py-3 text-center text-xs font-bold text-gray-600 uppercase tracking-wide">Tiempo Entrega</th>
                  <th class="px-4 py-3 text-center text-xs font-bold text-gray-600 uppercase tracking-wide">Última Compra</th>
                  <th class="px-4 py-3 text-center text-xs font-bold text-gray-600 uppercase tracking-wide">Estado</th>
                  <th class="px-4 py-3 text-center text-xs font-bold text-gray-600 uppercase tracking-wide">Acciones</th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200">
                <tr v-for="supplier in paginatedSuppliers" :key="supplier.id" class="hover:bg-gray-50 transition-colors">
                  <td class="px-4 py-4">
                    <div>
                      <div class="text-sm font-semibold text-gray-900">{{ supplier.name }}</div>
                      <div class="text-xs text-gray-500 mt-0.5">
                        {{ supplier.contact_name || 'Sin contacto' }}
                      </div>
                      <div class="text-xs text-gray-400 mt-0.5 flex items-center space-x-2">
                        <span v-if="supplier.phone">📞 {{ supplier.phone }}</span>
                        <span v-if="supplier.city">📍 {{ supplier.city }}</span>
                      </div>
                    </div>
                  </td>
                  <td class="px-4 py-4 text-center">
                    <div class="text-sm font-medium text-gray-900">
                      {{ supplier.delivery_time || '2-3 días' }}
                    </div>
                  </td>
                  <td class="px-4 py-4 text-center">
                    <div class="text-sm text-gray-900" v-if="supplier.last_purchase_date">
                      {{ formatDate(supplier.last_purchase_date) }}
                    </div>
                    <div class="text-xs text-gray-400" v-else>Sin compras</div>
                  </td>
                  <td class="px-4 py-4 text-center">
                    <span :class="[
                      'px-2 py-1 text-xs font-semibold rounded-full',
                      supplier.status === 'active' 
                        ? 'bg-green-100 text-green-700' 
                        : 'bg-gray-100 text-gray-700'
                    ]">
                      {{ supplier.status === 'active' ? 'Activo' : 'Inactivo' }}
                    </span>
                  </td>
                  <td class="px-4 py-4 text-center">
                    <div class="flex items-center justify-center space-x-2">
                      <button class="px-3 py-1.5 text-xs font-medium text-blue-600 bg-blue-50 hover:bg-blue-100 rounded-lg transition-colors">
                        Ver Productos
                      </button>
                      <button class="px-3 py-1.5 text-xs font-medium text-green-600 bg-green-50 hover:bg-green-100 rounded-lg transition-colors">
                        Nuevo Pedido
                      </button>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- Paginador -->
          <div v-if="suppliersData?.suppliers && suppliersData.suppliers.length > 0" class="bg-white border-t border-gray-200 px-4 py-3 flex items-center justify-between">
            <div class="flex items-center space-x-3">
              <div class="flex items-center space-x-2">
                <span class="text-xs font-medium text-gray-700">Mostrar:</span>
                <select v-model="suppliersFilters.itemsPerPage" @change="suppliersFilters.currentPage = 1"
                        class="border border-gray-300 rounded-lg px-2 py-1 text-xs focus:outline-none focus:ring-2 focus:ring-blue-500">
                  <option value="10">10</option>
                  <option value="25">25</option>
                  <option value="50">50</option>
                  <option value="100">100</option>
                </select>
                <span class="text-xs text-gray-700">por página</span>
              </div>
              <div class="text-xs text-gray-700">
                Mostrando {{ suppliersPaginationInfo.start }} a {{ suppliersPaginationInfo.end }} de {{ suppliersPaginationInfo.total }}
              </div>
            </div>
            
            <div class="flex items-center space-x-1">
              <button @click="suppliersFilters.currentPage = 1" :disabled="suppliersFilters.currentPage === 1"
                      class="p-1.5 text-gray-500 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"></path>
                </svg>
              </button>
              <button @click="suppliersFilters.currentPage--" :disabled="suppliersFilters.currentPage === 1"
                      class="p-1.5 text-gray-500 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
              </button>
              
              <div class="flex items-center space-x-1">
                <button v-for="page in suppliersTotalPages" :key="page" @click="suppliersFilters.currentPage = page"
                        :class="[
                          'px-2.5 py-1 text-xs font-medium rounded-lg transition-colors',
                          page === suppliersFilters.currentPage 
                            ? 'bg-blue-600 text-white border border-blue-600' 
                            : 'text-gray-500 bg-white border border-gray-300 hover:bg-gray-50'
                        ]">
                  {{ page }}
                </button>
              </div>
              
              <button @click="suppliersFilters.currentPage++" :disabled="suppliersFilters.currentPage === suppliersTotalPages"
                      class="p-1.5 text-gray-500 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
              </button>
              <button @click="suppliersFilters.currentPage = suppliersTotalPages" :disabled="suppliersFilters.currentPage === suppliersTotalPages"
                      class="p-1.5 text-gray-500 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7"></path>
                </svg>
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Vista de Alertas -->
      <div v-if="activeSection === 'alerts'" class="space-y-6 animate-fade-in">
        
        <!-- Header con Filtros -->
        <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm">
          <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h2 class="text-lg font-bold text-gray-900">Centro de Alertas</h2>
                <p class="text-sm text-gray-500">Monitoreo de salud del inventario</p>
            </div>
            
            <div class="flex items-center gap-3">
              <div class="flex items-center space-x-2 bg-gray-50 rounded-xl p-1 border border-gray-200">
                  <span class="text-xs font-medium text-gray-500 px-2">Filtrar:</span>
                  <select 
                    v-model="alertsFilters.severity"
                    @change="loadAlertsData"
                    class="bg-transparent border-none text-sm font-medium text-gray-700 focus:ring-0 py-1 pr-8 pl-2"
                  >
                    <option value="">Todas</option>
                    <option value="critical">Críticas</option>
                    <option value="warning">Advertencias</option>
                    <option value="info">Información</option>
                  </select>
              </div>
              
              <button 
                @click="loadAlertsData"
                class="p-2.5 bg-white hover:bg-gray-50 text-gray-600 rounded-xl border border-gray-200 shadow-sm transition-all hover:shadow-md"
                title="Actualizar"
              >
                <i class="fas fa-sync-alt"></i>
              </button>
            </div>
          </div>
        </div>

        <!-- 4 Tarjetas de Resumen Horizontales -->
        <div v-if="alertsData" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
          <!-- Críticas (Rojo - Alerta) -->
          <div class="bg-white rounded-2xl p-4 border border-gray-200 hover:border-gray-300 transition-all duration-200 hover:shadow-lg">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-rose-50 rounded-lg flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-exclamation-circle text-rose-600"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Críticas</p>
                    <p class="text-xl font-bold text-gray-900">{{ alertsData.summary.critical || 0 }}</p>
                </div>
            </div>
          </div>

          <!-- Advertencias -->
          <div class="bg-white rounded-2xl p-4 border border-gray-200 hover:border-gray-300 transition-all duration-200 hover:shadow-lg">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-amber-50 rounded-lg flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-exclamation-triangle text-amber-600"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Advertencias</p>
                    <p class="text-xl font-bold text-gray-900">{{ alertsData.summary.warning || 0 }}</p>
                </div>
            </div>
          </div>

          <!-- Información -->
          <div class="bg-white rounded-2xl p-4 border border-gray-200 hover:border-gray-300 transition-all duration-200 hover:shadow-lg">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-sky-50 rounded-lg flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-info-circle text-sky-600"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Información</p>
                    <p class="text-xl font-bold text-gray-900">{{ alertsData.summary.info || 0 }}</p>
                </div>
            </div>
          </div>

          <!-- Total -->
          <div class="bg-white rounded-2xl p-4 border border-gray-200 hover:border-gray-300 transition-all duration-200 hover:shadow-lg">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-slate-50 rounded-lg flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-bell text-slate-600"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Total Alertas</p>
                    <p class="text-xl font-bold text-gray-900">{{ alertsData.summary.total_alerts || 0 }}</p>
                </div>
            </div>
          </div>
        </div>

        <!-- Alertas del Sistema - DISEÑO TECHNICAL LIST -->
        <div v-if="alertsData && alertsData.alerts?.length > 0" class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
          <div class="px-6 py-3.5 border-b border-gray-200 flex items-center justify-between">
            <div>
              <h3 class="text-base font-bold text-gray-900">Centro de Alertas</h3>
              <p class="text-xs text-gray-500 mt-0.5">{{ alertsData.alerts.length }} notificaciones agrupadas</p>
            </div>
            <div class="flex items-center space-x-4">
              <button
                @click="markAllAlertsAsReviewed"
                class="text-xs font-medium text-gray-500 hover:text-gray-900 transition-colors duration-150"
              >
                Marcar todas leídas
              </button>
            </div>
          </div>
          
          <!-- Lista de Alertas Agrupadas - Technical List -->
          <div class="divide-y divide-gray-100">
            <div v-for="group in groupedAlerts" :key="group.category + group.severity" 
                 class="transition-colors duration-150">
              
              <!-- Header del Grupo (Acordeón Limpio) -->
              <div class="px-6 py-3.5 flex items-center justify-between hover:bg-gray-50 transition-colors duration-150">
                <div 
                  @click="toggleAlertGroup(group.category, group.severity)"
                  class="flex items-center space-x-3 flex-1 cursor-pointer"
                >
                  <!-- Icono SVG Lineal 16px (sin fondo) -->
                  <svg :class="[
                    'w-4 h-4 flex-shrink-0',
                    group.severity === 'critical' ? 'text-red-600' :
                    group.severity === 'warning' ? 'text-amber-600' :
                    'text-blue-600'
                  ]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path v-if="group.severity === 'critical'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    <path v-else-if="group.severity === 'warning'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                  </svg>
                  
                  <!-- Información del grupo -->
                  <div class="flex-1 min-w-0">
                    <div class="flex items-center space-x-2">
                      <h4 class="text-sm font-semibold text-gray-900">
                        {{ getCategoryTitle(group.category) }}
                      </h4>
                      <span class="text-xs text-gray-500">
                        ({{ group.alerts.length }})
                      </span>
                    </div>
                    <p class="text-xs text-gray-500 mt-0.5">
                      {{ group.alerts.length > 1 
                          ? `${group.alerts.length} ${getCategoryDescription(group.category)}` 
                          : group.alerts[0].message.substring(0, 60) + '...' }}
                    </p>
                  </div>
                  
                  <!-- Chevron gris sutil -->
                  <svg :class="[
                    'w-3.5 h-3.5 text-gray-400 transition-transform duration-200 flex-shrink-0',
                    expandedGroups.includes(group.category + group.severity) ? 'rotate-90' : ''
                  ]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                  </svg>
                </div>
                
                <!-- Acciones de texto sutiles -->
                <div class="ml-4 flex items-center space-x-3 flex-shrink-0">
                  <button
                    @click.stop="resolveAlertGroup(group)"
                    class="text-xs font-medium text-gray-500 hover:text-gray-900 transition-colors duration-150"
                  >
                    Marcar leídas
                  </button>
                  <button
                    @click.stop="toggleAlertGroup(group.category, group.severity)"
                    class="text-xs font-medium text-blue-600 hover:text-blue-700 transition-colors duration-150"
                  >
                    Ver detalles
                  </button>
                </div>
              </div>
              
              <!-- Detalles del Grupo (Expandible) -->
              <transition name="expand">
                <div v-if="expandedGroups.includes(group.category + group.severity)" 
                     class="bg-gray-50">
                  <div class="pl-12 pr-6 py-3 space-y-2">
                    <!-- Cada alerta individual -->
                    <div v-for="alert in group.alerts" :key="alert.id"
                         class="flex items-start justify-between py-2 border-b border-gray-100 last:border-0 hover:bg-white/50 px-3 -mx-3 rounded transition-colors duration-150">
                      
                      <!-- Contenido de la alerta -->
                      <div class="flex-1 min-w-0 pr-4">
                        <h5 class="text-sm font-medium text-gray-900 mb-0.5">{{ alert.title }}</h5>
                        <p class="text-xs text-gray-600 leading-relaxed mb-1.5">{{ alert.message }}</p>
                        
                        <!-- Metadata inline -->
                        <div class="flex flex-wrap items-center gap-x-3 gap-y-1 text-xs text-gray-500">
                          <span class="flex items-center">
                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            {{ formatDate(alert.created_at) }}
                          </span>
                          <span v-if="alert.product_name" class="flex items-center">
                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                            </svg>
                            {{ alert.product_name }}
                          </span>
                          <span v-if="alert.invoice_number" class="flex items-center">
                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            {{ alert.invoice_number }}
                          </span>
                          <span v-if="alert.discount_value" class="flex items-center font-semibold text-red-600">
                            {{ alert.discount_value }}
                          </span>
                        </div>
                      </div>
                      
                      <!-- Acciones de texto -->
                      <div class="flex items-center space-x-3 flex-shrink-0">
                        <button
                          v-if="alert.action_url || alert.invoice_id"
                          @click="viewAlertDetails(alert)"
                          class="text-xs font-medium text-blue-600 hover:text-blue-700 transition-colors duration-150"
                        >
                          Ver detalles
                        </button>
                        
                        <button
                          @click="markAlertAsReviewed(alert)"
                          class="text-xs font-medium text-gray-500 hover:text-gray-900 transition-colors duration-150"
                        >
                          Marcar leída
                        </button>
                      </div>
                    </div>
                  </div>
                </div>
              </transition>
            </div>
          </div>
          
          <!-- Paginación (si hay muchos grupos) -->
          <div v-if="groupedAlerts.length > 10" class="bg-gray-50 px-6 py-3 border-t border-gray-200">
            <div class="flex items-center justify-between">
              <div class="text-sm text-gray-700">
                {{ groupedAlerts.length }} grupos de alertas
              </div>
              <button
                @click="loadAlertsData"
                class="px-3 py-1.5 bg-white hover:bg-gray-50 text-gray-700 text-xs font-semibold rounded-lg border border-gray-300 transition-all duration-200"
              >
                <i class="fas fa-sync-alt mr-1.5"></i>
                Actualizar
              </button>
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
        
        <!-- Filtros Ejecutivos de Predicciones -->
        <div class="bg-white rounded-2xl shadow-sm p-4 border border-gray-200">
          <div class="flex flex-wrap items-center gap-3 justify-between">
            <div class="flex items-center gap-3">
              <div class="flex items-center gap-2">
                <i class="fas fa-calendar-alt text-blue-600 text-sm"></i>
                <span class="text-xs font-semibold text-gray-700">Horizonte de Pronóstico:</span>
              </div>
              <select 
                v-model="predictionsFilters.forecastDays"
                @change="loadPredictionsData"
                class="pl-3 pr-8 py-2 text-sm border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white font-medium"
              >
                <option :value="7">7 días</option>
                <option :value="14">14 días</option>
                <option :value="30">30 días</option>
                <option :value="60">60 días</option>
                <option :value="90">90 días</option>
              </select>
              
              <!-- Toggle para productos saludables -->
              <div class="ml-4 flex items-center space-x-2 px-3 py-2 bg-gray-50 rounded-xl border border-gray-200">
                <input 
                  type="checkbox" 
                  id="showHealthy"
                  v-model="predictionsFilters.showHealthy"
                  class="w-4 h-4 text-slate-600 rounded focus:ring-2 focus:ring-slate-500"
                >
                <label for="showHealthy" class="text-xs font-semibold text-gray-700 cursor-pointer">
                  <i class="fas fa-eye text-slate-600 mr-1"></i>
                  Mostrar Stock Seguro
                </label>
              </div>
            </div>
            
            <button 
              @click="loadPredictionsData"
              class="px-4 py-2 bg-slate-50 hover:bg-slate-100 text-slate-600 text-sm font-semibold rounded-xl border border-slate-200 shadow-sm transition-all duration-200 flex items-center space-x-2 hover:shadow-md hover:border-slate-300"
            >
              <i class="fas fa-sync-alt text-xs"></i>
              <span>Actualizar</span>
            </button>
          </div>
        </div>

        <!-- Análisis de Tendencias - Ejecutivo Minimalista -->
        <div v-if="predictionsData" class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
          <div class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-slate-50 to-blue-50/30">
            <div class="flex items-center justify-between">
              <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-gradient-to-br from-blue-600 to-indigo-600 rounded-xl flex items-center justify-center shadow-sm">
                  <i class="fas fa-chart-line text-white text-lg"></i>
                </div>
                <div>
                  <h3 class="text-base font-bold text-gray-900">Análisis de Tendencias IA</h3>
                  <p class="text-xs text-gray-600 mt-0.5">Predicciones basadas en Machine Learning</p>
                </div>
              </div>
              <span class="px-2.5 py-1 bg-gradient-to-r from-blue-50 to-indigo-50 text-blue-700 text-xs font-semibold rounded-lg border border-blue-100">
                <i class="fas fa-brain mr-1"></i>IA
              </span>
            </div>
          </div>
          
          <!-- Tarjetas de Tendencias - Grid 3 Columnas -->
          <div class="grid grid-cols-1 md:grid-cols-3 gap-4 p-6">
            
            <!-- Tendencia Ventas -->
            <div class="bg-white rounded-xl p-4 border border-gray-200 hover:border-gray-300 hover:shadow-lg transition-all duration-200">
              <div class="flex items-center justify-between mb-3">
                <div class="flex items-center space-x-2">
                  <div class="w-8 h-8 bg-emerald-50 rounded-lg flex items-center justify-center">
                    <i class="fas fa-dollar-sign text-emerald-600"></i>
                  </div>
                  <h3 class="text-sm font-bold text-gray-900">Ventas</h3>
                </div>
                <div class="p-1.5 bg-gray-50 rounded-lg">
                  <i :class="[
                    'fas text-sm',
                    predictionsData.trend_analysis.sales.trend === 'positive' 
                      ? 'fa-arrow-trend-up text-emerald-600' 
                      : 'fa-arrow-trend-down text-rose-600'
                  ]"></i>
                </div>
              </div>
              <div class="space-y-2 text-xs">
                <div class="flex justify-between items-center">
                  <span class="text-gray-600 font-medium">Actual:</span>
                  <span class="font-bold text-gray-900">{{ formatCurrency(predictionsData.trend_analysis.sales.current) }}</span>
                </div>
                <div class="flex justify-between items-center">
                  <span class="text-gray-500">Anterior:</span>
                  <span class="font-semibold text-gray-600">{{ formatCurrency(predictionsData.trend_analysis.sales.previous) }}</span>
                </div>
                <div class="pt-2 mt-2 border-t border-gray-200 flex justify-between items-center">
                  <span class="text-gray-700 font-semibold">Variación:</span>
                  <span :class="[
                    'text-sm font-bold px-2 py-0.5 rounded-lg',
                    predictionsData.trend_analysis.sales.growth_percentage >= 0 
                      ? 'bg-emerald-50 text-emerald-700' 
                      : 'bg-rose-50 text-rose-700'
                  ]">
                    {{ predictionsData.trend_analysis.sales.growth_percentage >= 0 ? '+' : '' }}{{ predictionsData.trend_analysis.sales.growth_percentage }}%
                  </span>
                </div>
              </div>
            </div>

          <!-- Tendencia Transacciones -->
          <div class="bg-white rounded-xl p-4 border border-gray-200 hover:border-gray-300 hover:shadow-lg transition-all duration-200">
            <div class="flex items-center justify-between mb-3">
              <div class="flex items-center space-x-2">
                <div class="w-8 h-8 bg-sky-50 rounded-lg flex items-center justify-center">
                  <i class="fas fa-receipt text-sky-600"></i>
                </div>
                <h3 class="text-sm font-bold text-gray-900">Transacciones</h3>
              </div>
              <div class="p-1.5 bg-gray-50 rounded-lg">
                <i :class="[
                  'fas text-sm',
                  predictionsData.trend_analysis.transactions.trend === 'positive' 
                    ? 'fa-arrow-trend-up text-sky-600' 
                    : 'fa-arrow-trend-down text-rose-600'
                ]"></i>
              </div>
            </div>
            <div class="space-y-2 text-xs">
              <div class="flex justify-between items-center">
                <span class="text-gray-600 font-medium">Actual:</span>
                <span class="font-bold text-gray-900">{{ formatNumber(predictionsData.trend_analysis.transactions.current) }}</span>
              </div>
              <div class="flex justify-between items-center">
                <span class="text-gray-500">Anterior:</span>
                <span class="font-semibold text-gray-600">{{ formatNumber(predictionsData.trend_analysis.transactions.previous) }}</span>
              </div>
              <div class="pt-2 mt-2 border-t border-gray-200 flex justify-between items-center">
                <span class="text-gray-700 font-semibold">Variación:</span>
                <span :class="[
                  'text-sm font-bold px-2 py-0.5 rounded-lg',
                  predictionsData.trend_analysis.transactions.growth_percentage >= 0 
                    ? 'bg-sky-50 text-sky-700' 
                    : 'bg-rose-50 text-rose-700'
                ]">
                  {{ predictionsData.trend_analysis.transactions.growth_percentage >= 0 ? '+' : '' }}{{ predictionsData.trend_analysis.transactions.growth_percentage }}%
                </span>
              </div>
            </div>
          </div>

          <!-- Tendencia Ticket Promedio -->
          <div class="bg-white rounded-xl p-4 border border-gray-200 hover:border-gray-300 hover:shadow-lg transition-all duration-200">
            <div class="flex items-center justify-between mb-3">
              <div class="flex items-center space-x-2">
                <div class="w-8 h-8 bg-purple-50 rounded-lg flex items-center justify-center">
                  <i class="fas fa-ticket text-purple-600"></i>
                </div>
                <h3 class="text-sm font-bold text-gray-900">Ticket Promedio</h3>
              </div>
              <div class="p-1.5 bg-gray-50 rounded-lg">
                <i :class="[
                  'fas text-sm',
                  predictionsData.trend_analysis.average_ticket.trend === 'positive' 
                    ? 'fa-arrow-trend-up text-purple-600' 
                    : 'fa-arrow-trend-down text-rose-600'
                ]"></i>
              </div>
            </div>
            <div class="space-y-2 text-xs">
              <div class="flex justify-between items-center">
                <span class="text-gray-600 font-medium">Actual:</span>
                <span class="font-bold text-gray-900">{{ formatCurrency(predictionsData.trend_analysis.average_ticket.current) }}</span>
              </div>
              <div class="flex justify-between items-center">
                <span class="text-gray-500">Anterior:</span>
                <span class="font-semibold text-gray-600">{{ formatCurrency(predictionsData.trend_analysis.average_ticket.previous) }}</span>
              </div>
              <div class="pt-2 mt-2 border-t border-gray-200 flex justify-between items-center">
                <span class="text-gray-700 font-semibold">Variación:</span>
                <span :class="[
                  'text-sm font-bold px-2 py-0.5 rounded-lg',
                  predictionsData.trend_analysis.average_ticket.growth_percentage >= 0 
                    ? 'bg-purple-50 text-purple-700' 
                    : 'bg-rose-50 text-rose-700'
                ]">
                  {{ predictionsData.trend_analysis.average_ticket.growth_percentage >= 0 ? '+' : '' }}{{ predictionsData.trend_analysis.average_ticket.growth_percentage }}%
                </span>
              </div>
            </div>
          </div>
          </div>
        </div>

        <!-- Predicción de Agotamiento de Stock con IA - DISEÑO ENTERPRISE CLEAN -->
        <div v-if="predictionsData && predictionsData.stock_depletion?.length > 0" 
             class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
          <div class="px-6 py-4 border-b border-gray-200 bg-white">
            <div class="flex items-center justify-between">
              <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-gradient-to-br from-slate-600 to-slate-700 rounded-xl flex items-center justify-center shadow-sm">
                  <i class="fas fa-chart-bar text-white text-lg"></i>
                </div>
                <div>
                  <h3 class="text-base font-bold text-gray-900">Análisis de Inventario</h3>
                  <p class="text-xs text-gray-600 mt-0.5">Proyección de agotamiento de stock</p>
                </div>
              </div>
              <div class="flex items-center space-x-3">
                <div class="flex items-center space-x-2 text-xs text-gray-600">
                  <span class="font-mono font-semibold text-red-600">{{ criticalProductsCount }}</span>
                  <span>Críticos</span>
                  <span class="mx-2 text-gray-300">•</span>
                  <span class="font-mono font-semibold text-amber-600">{{ warningProductsCount }}</span>
                  <span>Atención</span>
                  <span v-if="predictionsFilters.showHealthy" class="mx-2 text-gray-300">•</span>
                  <span v-if="predictionsFilters.showHealthy" class="font-mono font-semibold text-green-600">{{ healthyProductsCount }}</span>
                  <span v-if="predictionsFilters.showHealthy">Saludables</span>
                </div>
              </div>
            </div>
          </div>
          <div class="overflow-x-auto">
            <table class="min-w-full">
              <thead class="bg-white border-b border-gray-200">
                <tr>
                  <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Producto</th>
                  <th class="px-4 py-3 text-center text-xs font-semibold text-gray-700 uppercase tracking-wider">Stock Actual</th>
                  <th class="px-4 py-3 text-center text-xs font-semibold text-gray-700 uppercase tracking-wider">Consumo Diario</th>
                  <th class="px-4 py-3 text-center text-xs font-semibold text-gray-700 uppercase tracking-wider">Agotamiento</th>
                  <th class="px-4 py-3 text-center text-xs font-semibold text-gray-700 uppercase tracking-wider">Acción</th>
                </tr>
              </thead>
              <tbody class="bg-white">
                <tr v-for="item in paginatedStockDepletion" :key="item.product_id"
                    class="border-b border-gray-100 hover:bg-gray-50 transition-colors duration-150">
                  <td class="px-4 py-4">
                    <div>
                      <div class="text-sm font-semibold text-gray-900 mb-1.5">{{ item.product_name }}</div>
                      <!-- Barra de urgencia fina (4px) debajo del nombre -->
                      <div class="w-full h-1 bg-gray-100 rounded-full overflow-hidden">
                        <div 
                          :class="[
                            'h-full transition-all duration-500',
                            item.days_until_depletion < 7 ? 'bg-red-500' :
                            item.days_until_depletion < 30 ? 'bg-amber-500' :
                            'bg-green-500'
                          ]"
                          :style="{ 
                            width: item.days_until_depletion < 7 ? '15%' :
                                   item.days_until_depletion < 30 ? '50%' :
                                   '100%'
                          }"
                        ></div>
                      </div>
                    </div>
                  </td>
                  <td class="px-4 py-4 text-center">
                    <div class="font-mono text-base font-bold text-gray-900">{{ item.current_stock }}</div>
                    <div class="text-xs text-gray-500 mt-0.5">unidades</div>
                  </td>
                  <td class="px-4 py-4 text-center">
                    <div class="font-mono text-sm font-semibold text-gray-700">{{ item.daily_average_sales }}</div>
                    <div class="text-xs text-gray-500 mt-0.5">un/día</div>
                  </td>
                  <td class="px-4 py-4 text-center">
                    <!-- Fecha relativa limpia -->
                    <div v-if="item.days_until_depletion >= 30">
                      <div class="text-sm font-semibold text-green-700">{{ getRelativeTimeText(item.days_until_depletion) }}</div>
                      <div class="text-xs text-gray-500 mt-0.5">Stock amplio</div>
                    </div>
                    <div v-else>
                      <div :class="[
                        'text-sm font-bold mb-0.5',
                        item.days_until_depletion < 7 ? 'text-red-600' : 'text-amber-600'
                      ]">
                        {{ getRelativeTimeText(item.days_until_depletion) }}
                      </div>
                      <div class="text-xs text-gray-500 mt-0.5">
                        {{ formatDate(item.estimated_depletion_date) }}
                      </div>
                    </div>
                  </td>
                  <td class="px-4 py-4 text-center">
                    <!-- Botón outline 'Reponer' solo para críticos/advertencias -->
                    <button
                      v-if="item.days_until_depletion < 30"
                      @click="createPurchaseOrder(item)"
                      :class="[
                        'px-3 py-1.5 text-xs font-semibold rounded-lg border-2 transition-all duration-150',
                        item.days_until_depletion < 7 
                          ? 'border-red-500 text-red-600 hover:bg-red-50' 
                          : 'border-amber-500 text-amber-600 hover:bg-amber-50'
                      ]"
                    >
                      Reponer
                    </button>
                    <!-- Sin acción para productos saludables -->
                    <span v-else class="text-xs text-gray-400">—</span>
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
        
        <!-- Mensaje "¡Todo bajo control!" cuando NO hay productos críticos ni de atención -->
        <div v-else-if="predictionsData && criticalProductsCount === 0 && warningProductsCount === 0"
             class="bg-gradient-to-br from-green-50 to-emerald-50 rounded-2xl shadow-sm border-2 border-green-200 p-12 text-center">
          <div class="flex flex-col items-center space-y-4">
            <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center animate-bounce">
              <i class="fas fa-check-circle text-green-600 text-4xl"></i>
            </div>
            <div>
              <h3 class="text-2xl font-bold text-green-900 mb-2">¡Todo bajo control!</h3>
              <p class="text-green-700 text-sm max-w-md mx-auto">
                No hay productos críticos ni en estado de atención. Tu inventario está en excelente estado.
              </p>
            </div>
            <div class="flex items-center space-x-4 mt-4 text-sm">
              <div class="flex items-center space-x-2 text-green-700">
                <i class="fas fa-box-open"></i>
                <span class="font-semibold">{{ healthyProductsCount }} productos saludables</span>
              </div>
              <button
                @click="predictionsFilters.showHealthy = true"
                class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-xs font-bold rounded-lg transition-all duration-200 shadow-md hover:shadow-lg"
              >
                <i class="fas fa-eye mr-1.5"></i>
                Ver Inventario Completo
              </button>
            </div>
          </div>
        </div>

        <!-- Recomendaciones de Compra con IA - DISEÑO URGENTE PROFESIONAL -->
        <div v-if="predictionsData && predictionsData.purchase_recommendations?.length > 0" 
             class="bg-white rounded-2xl shadow-sm border-2 border-red-200 overflow-hidden">
          <!-- Header urgente pero profesional -->
          <div class="px-6 py-4 border-b border-red-200 bg-gradient-to-r from-red-50 to-rose-50">
            <div class="flex items-center justify-between">
              <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-red-600 rounded-xl flex items-center justify-center shadow-sm">
                  <i class="fas fa-exclamation-triangle text-white text-lg"></i>
                </div>
                <div>
                  <h3 class="text-base font-bold text-red-900">Recomendaciones de Compra</h3>
                  <p class="text-xs text-red-700 mt-0.5">Productos que requieren reabastecimiento</p>
                </div>
              </div>
              <div class="flex items-center space-x-2">
                <span class="px-2.5 py-1 bg-red-600 text-white text-xs font-bold rounded-lg">
                  {{ predictionsData.purchase_recommendations.length }}
                </span>
                <span class="text-xs text-red-700 font-semibold">urgentes</span>
              </div>
            </div>
          </div>
          
          <div class="overflow-x-auto">
            <table class="min-w-full">
              <thead class="bg-white border-b border-red-100">
                <tr>
                  <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Producto</th>
                  <th class="px-4 py-3 text-center text-xs font-semibold text-gray-700 uppercase tracking-wider">Stock</th>
                  <th class="px-4 py-3 text-center text-xs font-semibold text-gray-700 uppercase tracking-wider">Comprar</th>
                  <th class="px-4 py-3 text-center text-xs font-semibold text-gray-700 uppercase tracking-wider">Inversión</th>
                  <th class="px-4 py-3 text-center text-xs font-semibold text-gray-700 uppercase tracking-wider">Prioridad</th>
                  <th class="px-4 py-3 text-center text-xs font-semibold text-gray-700 uppercase tracking-wider">Acción</th>
                </tr>
              </thead>
              <tbody class="bg-white">
                <tr v-for="item in paginatedPurchaseRecommendations" :key="item.product_id"
                    class="border-b border-gray-100 hover:bg-red-50/30 transition-colors duration-150">
                  <td class="px-4 py-4">
                    <div>
                      <div class="text-sm font-semibold text-gray-900">{{ item.product_name }}</div>
                      <div class="text-xs text-gray-500 mt-1">
                        <span class="font-mono">{{ item.daily_demand }}</span> un/día
                      </div>
                    </div>
                  </td>
                  <td class="px-4 py-4 text-center">
                    <div class="font-mono text-base font-bold" :class="[
                      item.current_stock <= 10 ? 'text-red-600' : 'text-gray-900'
                    ]">{{ item.current_stock }}</div>
                    <div class="text-xs text-gray-500 mt-0.5">unidades</div>
                  </td>
                  <td class="px-4 py-4 text-center">
                    <div class="font-mono text-lg font-bold text-red-600">{{ item.recommended_purchase }}</div>
                    <div class="text-xs text-gray-500 mt-0.5">unidades</div>
                  </td>
                  <td class="px-4 py-4 text-center">
                    <div class="font-mono text-base font-bold text-gray-900">{{ formatCurrency(item.estimated_cost) }}</div>
                    <div class="text-xs text-gray-500 mt-0.5">estimado</div>
                  </td>
                  <td class="px-4 py-4 text-center">
                    <span class="text-xs font-semibold" :class="[
                      item.priority === 'critical' ? 'text-red-600' :
                      item.priority === 'high' ? 'text-amber-600' :
                      'text-gray-600'
                    ]">
                      {{ item.priority === 'critical' ? 'Urgente' : 
                         item.priority === 'high' ? 'Alta' : 
                         item.priority === 'medium' ? 'Media' : 'Baja' }}
                    </span>
                  </td>
                  <td class="px-4 py-4 text-center">
                    <button
                      @click="createPurchaseOrder(item)"
                      class="px-3 py-1.5 bg-red-600 hover:bg-red-700 text-white text-xs font-semibold rounded-lg transition-all duration-150"
                    >
                      Reponer
                    </button>
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

        <!-- Pronóstico de Ventas con Machine Learning - Ejecutivo -->
        <div v-if="predictionsData && predictionsData.sales_forecast?.length > 0" 
             class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
          <div class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-emerald-50 to-green-50/50">
            <div class="flex items-center justify-between">
              <div class="flex items-center space-x-3">
                <div class="p-2.5 bg-white/60 backdrop-blur-sm rounded-xl shadow-sm">
                  <i class="fas fa-chart-line text-emerald-600 text-lg"></i>
                </div>
                <div>
                  <h3 class="text-base font-bold text-gray-900">Pronóstico de Ventas con Machine Learning</h3>
                  <p class="text-xs text-gray-600 mt-0.5">Proyección para los próximos {{ predictionsFilters.forecastDays }} días</p>
                </div>
              </div>
              <span class="px-2.5 py-1 bg-emerald-100/70 text-emerald-700 text-xs font-semibold rounded-lg border border-emerald-200">
                {{ predictionsFilters.forecastDays }} días
              </span>
            </div>
          </div>
          <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
              <thead class="bg-gray-50/50">
                <tr>
                  <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wide">Producto</th>
                  <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wide">Ventas Históricas</th>
                  <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wide">Pronóstico IA</th>
                  <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wide">Tendencia</th>
                  <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wide">Confianza</th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200">
                <tr v-for="item in paginatedSalesForecast" :key="item.product_id"
                    class="hover:bg-gray-50 transition-colors">
                  <td class="px-4 py-3">
                    <div class="text-sm font-semibold text-gray-900">{{ item.product_name }}</div>
                    <div class="text-xs text-gray-500 mt-0.5">
                      <i class="fas fa-exchange-alt mr-1"></i>{{ item.transactions }} transacciones
                    </div>
                  </td>
                  <td class="px-4 py-3">
                    <span class="text-sm font-semibold text-gray-700">{{ item.historical_sales }}</span>
                    <span class="text-xs text-gray-500 ml-1">unidades</span>
                  </td>
                  <td class="px-4 py-3">
                    <span class="inline-flex items-center px-2.5 py-1 bg-emerald-100 text-emerald-700 text-xs font-bold rounded-lg">
                      <i class="fas fa-brain mr-1.5"></i>{{ Math.abs(item.forecast_sales) }} unidades
                    </span>
                  </td>
                  <td class="px-4 py-3">
                    <span :class="[
                      'inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-semibold',
                      item.trend === 'growing' ? 'bg-green-100 text-green-700' :
                      item.trend === 'declining' ? 'bg-rose-100 text-rose-700' :
                      'bg-gray-100 text-gray-700'
                    ]">
                      <i :class="[
                        'fas mr-1.5',
                        item.trend === 'growing' ? 'fa-arrow-trend-up' :
                        item.trend === 'declining' ? 'fa-arrow-trend-down' :
                        'fa-minus'
                      ]"></i>
                      {{ item.trend === 'growing' ? 'Creciendo' : item.trend === 'declining' ? 'Decreciendo' : 'Estable' }}
                    </span>
                  </td>
                  <td class="px-4 py-3">
                    <span :class="[
                      'px-2.5 py-1 text-xs font-semibold rounded-full',
                      item.confidence === 'high' ? 'bg-emerald-100 text-emerald-700' :
                      'bg-amber-100 text-amber-700'
                    ]">
                      {{ item.confidence === 'high' ? 'ALTA' : 'MEDIA' }}
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

    <!-- Componente de Chat IA 105 -->
    <AI105Chat />
  </div>
</template>

<script>
import { ref, reactive, onMounted, computed } from 'vue'
import ToastNotifications from './ToastNotifications.vue'
import AI105Chat from './AI105Chat.vue'
import { API_CONFIG } from '../services/api.js'
import { getInitials } from '../utils/avatarUtils.js'

export default {
  name: 'IntelligentInventoryView',
  components: {
    ToastNotifications,
    AI105Chat
  },
  setup() {
    const API_BASE_URL = API_CONFIG.BASE_URL

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

    // Datos para vista de proveedores
    const suppliersData = ref(null)
    const suppliersFilters = reactive({
      supplier: '',
      sortBy: 'total_purchases_amount',
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
    const expandedGroups = ref([]) // Para controlar qué grupos están expandidos

    // Datos para vista de predicciones
    const predictionsData = ref(null)
    const predictionsCurrentPage = ref(1)
    const predictionsFilters = reactive({
      forecastDays: 30,
      period: 'month',
      showHealthy: false // Por defecto: SOLO CRÍTICOS y ATENCIÓN
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
      { id: 'suppliers', name: 'Proveedores', icon: 'fas fa-building' },
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

    const calculateProfitMargin = (summary) => {
      if (!summary || !summary.total_value_cost || summary.total_value_cost === 0) return '0.0'
      const margin = ((summary.total_profit_potential / summary.total_value_cost) * 100)
      return margin.toFixed(1)
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

    // Función para refrescar la sección actual
    const refreshCurrentSection = () => {
      console.log('🔄 Refrescando sección:', activeSection.value)
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
        case 'alerts':
          loadAlertsData()
          break
        case 'predictions':
          loadPredictionsData()
          break
      }
    }

    const testConnection = async () => {
      // Redirigir a loadDashboardData
      await loadDashboardData()
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

    // ============================================
    // FUNCIONES PARA AGRUPACIÓN INTELIGENTE DE ALERTAS
    // ============================================
    
    // Computed para agrupar alertas por categoría y severidad
    const groupedAlerts = computed(() => {
      if (!alertsData.value?.alerts) return []
      
      const groups = {}
      
      alertsData.value.alerts.forEach(alert => {
        const key = `${alert.category}_${alert.severity}`
        
        if (!groups[key]) {
          groups[key] = {
            category: alert.category,
            severity: alert.severity,
            alerts: []
          }
        }
        
        groups[key].alerts.push(alert)
      })
      
      // Convertir a array y ordenar por severidad (crítico primero)
      return Object.values(groups).sort((a, b) => {
        const severityOrder = { 'critical': 0, 'warning': 1, 'info': 2 }
        return severityOrder[a.severity] - severityOrder[b.severity]
      })
    })
    
    // Función para obtener icono según categoría (iconos significativos)
    const getCategoryIcon = (category) => {
      const icons = {
        'discounts': 'fas fa-percent',            // % para descuentos
        'fraud': 'fas fa-shield-alt',             // Escudo para fraude
        'security': 'fas fa-shield-alt',          // Escudo para seguridad
        'stock': 'fas fa-cubes',                  // Cubos para stock/inventario
        'inventory': 'fas fa-cubes',              // Cubos para inventario
        'low_stock': 'fas fa-cubes',              // Cubos para stock bajo
        'expiration': 'fas fa-sync-alt',          // Flechas circulares para rotación/expiración
        'timing': 'fas fa-sync-alt',              // Flechas circulares para timing
        'deadlines': 'fas fa-sync-alt',           // Flechas circulares para plazos
        'rotation': 'fas fa-sync-alt',            // Flechas circulares para rotación
        'sales': 'fas fa-chart-line',             // Gráfico para ventas
        'customers': 'fas fa-users',              // Usuarios para clientes
        'system': 'fas fa-cog'                    // Engranaje para sistema
      }
      return icons[category] || 'fas fa-bell'
    }
    
    // Función para obtener título legible de categoría
    const getCategoryTitle = (category) => {
      const titles = {
        'discounts': 'Descuentos Aplicados',
        'fraud': 'Actividad Sospechosa',
        'security': 'Alertas de Seguridad',
        'stock': 'Problemas de Inventario',
        'inventory': 'Gestión de Inventario',
        'low_stock': 'Stock Crítico',
        'expiration': 'Rotación de Inventario',
        'timing': 'Alertas de Tiempo',
        'deadlines': 'Plazos Importantes',
        'rotation': 'Rotación de Productos',
        'sales': 'Anomalías en Ventas',
        'customers': 'Alertas de Clientes',
        'system': 'Notificaciones del Sistema'
      }
      return titles[category] || category.replace('_', ' ').toUpperCase()
    }
    
    // Función para obtener descripción del grupo
    const getCategoryDescription = (category) => {
      const descriptions = {
        'discounts': 'descuentos aplicados',
        'fraud': 'actividades sospechosas',
        'security': 'alertas de seguridad',
        'stock': 'problemas de stock',
        'inventory': 'movimientos de inventario',
        'low_stock': 'productos con stock bajo',
        'expiration': 'alertas de rotación',
        'timing': 'alertas de tiempo',
        'deadlines': 'plazos pendientes',
        'rotation': 'productos con baja rotación',
        'sales': 'anomalías en ventas',
        'customers': 'alertas de clientes',
        'system': 'notificaciones del sistema'
      }
      return descriptions[category] || 'alertas'
    }
    
    // Función para toggle de grupos expandidos
    const toggleAlertGroup = (category, severity) => {
      const key = category + severity
      const index = expandedGroups.value.indexOf(key)
      
      if (index > -1) {
        expandedGroups.value.splice(index, 1)
      } else {
        expandedGroups.value.push(key)
      }
    }
    
    // Función para ver detalles de alerta (navegar a factura, producto, etc)
    const viewAlertDetails = (alert) => {
      console.log('Ver detalles de alerta:', alert)
      
      if (toastRef.value) {
        toastRef.value.show({
          title: 'Navegación',
          message: `Abriendo detalles de: ${alert.title}`,
          type: 'info',
          autoClose: true,
          duration: 2000
        })
      }
      
      // TODO: Implementar navegación real según tipo de alerta
      // if (alert.invoice_id) -> router.push(`/invoices/${alert.invoice_id}`)
      // if (alert.product_id) -> router.push(`/products/${alert.product_id}`)
      // if (alert.action_url) -> window.open(alert.action_url, '_blank')
    }
    
    // Función para marcar alerta como revisada
    const markAlertAsReviewed = async (alert) => {
      try {
        // TODO: Implementar llamada al backend
        // await fetch(`${API_BASE_URL}/alerts/${alert.id}/reviewed`, { method: 'POST' })
        
        if (toastRef.value) {
          toastRef.value.show({
            title: '✓ Alerta marcada como revisada',
            message: `La alerta "${alert.title}" ha sido marcada como revisada`,
            type: 'success',
            autoClose: true,
            duration: 3000
          })
        }
        
        // Remover alerta de la lista localmente
        if (alertsData.value?.alerts) {
          const index = alertsData.value.alerts.findIndex(a => a.id === alert.id)
          if (index > -1) {
            alertsData.value.alerts.splice(index, 1)
          }
        }
        
        console.log('Alerta marcada como revisada:', alert.id)
      } catch (error) {
        console.error('Error marcando alerta como revisada:', error)
        
        if (toastRef.value) {
          toastRef.value.show({
            title: 'Error',
            message: 'No se pudo marcar la alerta como revisada',
            type: 'error',
            autoClose: true,
            duration: 3000
          })
        }
      }
    }
    
    // Función para justificar alerta (descuentos, fraude)
    const justifyAlert = async (alert) => {
      // TODO: Abrir modal de justificación
      const justification = prompt(`Justifica la acción para: ${alert.title}`)
      
      if (!justification) return
      
      try {
        // TODO: Implementar llamada al backend
        // await fetch(`${API_BASE_URL}/alerts/${alert.id}/justify`, {
        //   method: 'POST',
        //   body: JSON.stringify({ justification })
        // })
        
        if (toastRef.value) {
          toastRef.value.show({
            title: '✓ Alerta justificada',
            message: `La justificación ha sido guardada: "${justification.substring(0, 40)}..."`,
            type: 'success',
            autoClose: true,
            duration: 3000
          })
        }
        
        // Remover alerta de la lista
        if (alertsData.value?.alerts) {
          const index = alertsData.value.alerts.findIndex(a => a.id === alert.id)
          if (index > -1) {
            alertsData.value.alerts.splice(index, 1)
          }
        }
        
        console.log('Alerta justificada:', alert.id, justification)
      } catch (error) {
        console.error('Error justificando alerta:', error)
        
        if (toastRef.value) {
          toastRef.value.show({
            title: 'Error',
            message: 'No se pudo guardar la justificación',
            type: 'error',
            autoClose: true,
            duration: 3000
          })
        }
      }
    }
    
    // Función para marcar todas las alertas como revisadas
    const markAllAlertsAsReviewed = async () => {
      if (!confirm('¿Estás seguro de marcar todas las alertas como revisadas?')) return
      
      try {
        // TODO: Implementar llamada al backend
        // await fetch(`${API_BASE_URL}/alerts/reviewed-all`, { method: 'POST' })
        
        if (toastRef.value) {
          toastRef.value.show({
            title: '✓ Todas las alertas revisadas',
            message: `${alertsData.value?.alerts?.length || 0} alertas marcadas como revisadas`,
            type: 'success',
            autoClose: true,
            duration: 3000
          })
        }
        
        // Limpiar alertas localmente
        if (alertsData.value) {
          alertsData.value.alerts = []
        }
        
        console.log('Todas las alertas marcadas como revisadas')
      } catch (error) {
        console.error('Error marcando todas las alertas:', error)
        
        if (toastRef.value) {
          toastRef.value.show({
            title: 'Error',
            message: 'No se pudieron marcar todas las alertas',
            type: 'error',
            autoClose: true,
            duration: 3000
          })
        }
      }
    }
    
    // Función para resolver grupo completo de alertas
    const resolveAlertGroup = async (group) => {
      const groupName = getCategoryTitle(group.category)
      const alertCount = group.alerts.length
      
      if (!confirm(`¿Resolver todas las ${alertCount} alertas de "${groupName}"?\n\nEsto marcará todas las alertas del grupo como revisadas.`)) return
      
      try {
        // TODO: Implementar llamada al backend para resolver grupo
        // await fetch(`${API_BASE_URL}/alerts/resolve-group`, {
        //   method: 'POST',
        //   body: JSON.stringify({
        //     category: group.category,
        //     severity: group.severity,
        //     alert_ids: group.alerts.map(a => a.id)
        //   })
        // })
        
        if (toastRef.value) {
          toastRef.value.show({
            title: `✅ Grupo Resuelto`,
            message: `${alertCount} alertas de "${groupName}" marcadas como revisadas`,
            type: 'success',
            autoClose: true,
            duration: 4000
          })
        }
        
        // Remover todas las alertas del grupo localmente
        if (alertsData.value?.alerts) {
          const alertIds = group.alerts.map(a => a.id)
          alertsData.value.alerts = alertsData.value.alerts.filter(
            alert => !alertIds.includes(alert.id)
          )
        }
        
        // Colapsar el grupo después de resolverlo
        const key = group.category + group.severity
        const index = expandedGroups.value.indexOf(key)
        if (index > -1) {
          expandedGroups.value.splice(index, 1)
        }
        
        console.log(`Grupo resuelto: ${group.category} (${alertCount} alertas)`)
      } catch (error) {
        console.error('Error resolviendo grupo de alertas:', error)
        
        if (toastRef.value) {
          toastRef.value.show({
            title: 'Error',
            message: 'No se pudo resolver el grupo de alertas',
            type: 'error',
            autoClose: true,
            duration: 3000
          })
        }
      }
    }

    // ============================================
    // FUNCIONES PARA MOVIMIENTOS
    // ============================================
    
    // Función para ver documento de movimiento (factura, orden de compra, etc)
    const viewMovementDocument = (movement) => {
      console.log('Ver documento de movimiento:', movement)
      
      if (toastRef.value) {
        toastRef.value.show({
          title: '📄 Abriendo Documento',
          message: `Documento: ${movement.document_number}\nTipo: ${movement.movement_reason}`,
          type: 'info',
          autoClose: true,
          duration: 2500
        })
      }
      
      // TODO: Implementar navegación real según tipo de documento
      // Ejemplo de lógica de navegación:
      // if (movement.movement_reason.includes('Venta')) {
      //   router.push(`/facturas/${movement.reference_id}`)
      // } else if (movement.movement_reason.includes('Compra')) {
      //   router.push(`/compras/${movement.reference_id}`)
      // } else if (movement.document_url) {
      //   window.open(movement.document_url, '_blank')
      // }
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
      // Si cambia a custom, no limpiar las fechas, mantener las actuales
      // Esto permite que el usuario vea los últimos datos seleccionados
      
      // Cargar datos según la vista activa (incluso para custom)
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
        case 'alerts':
          loadAlertsData()
          break
        case 'predictions':
          loadPredictionsData()
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
      connectionStatus.value = 'Cargando...'
      error.value = ''
      overviewData.value = null

      try {
        // Construir URL con parámetros
        let url = `${API_BASE_URL}/inventory/test/dashboard?period=${selectedPeriod.value}`
        
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
          } else {
            console.warn('⚠️ Respuesta sin success o data:', inventoryData)
          }
        } else {
          const errorText = await inventoryResponse.text()
          throw new Error(`Error ${inventoryResponse.status}: ${errorText}`)
        }
      } catch (err) {
        error.value = `Error cargando datos: ${err.message}`
        connectionStatus.value = 'Error al cargar'
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
          const endDate = customDateRange.end || customDateRange.start
          params.append('end_date', endDate)
        }
        
        const response = await fetch(`${API_BASE_URL}/products/analytics?${params}`)
        
        if (response.ok) {
          const data = await response.json()
          
          if (data.success && data.data) {
            productsData.value = {
              products: data.data.products || [],
              pagination: data.data.pagination || {},
              summary: data.data.summary || {},
              filters: data.data.filters || { categories: [], suppliers: [] }
            }
            
            availableCategories.value = productsData.value.filters.categories || []
            availableSuppliers.value = productsData.value.filters.suppliers || []
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
        
        const fullUrl = `${API_BASE_URL}/inventory/test/movements?${params}`
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
        
        const response = await fetch(`${API_BASE_URL}/inventory/test/customers?${params}`)
        
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

    // Cargar datos de proveedores
    const loadSuppliersData = async () => {
      try {
        console.log('🔄 Cargando datos de proveedores...')
        const response = await fetch(`${API_BASE_URL}/suppliers/analytics`)
        
        console.log('📡 Response status:', response.status)
        
        if (response.ok) {
          const data = await response.json()
          console.log('📦 Datos recibidos:', data)
          if (data.success) {
            suppliersData.value = data.data
            console.log('✅ suppliersData actualizado:', suppliersData.value)
          }
        }
      } catch (error) {
        console.error('❌ Error cargando proveedores:', error)
      }
    }

    // Cargar datos de alertas
    const loadAlertsData = async () => {
      try {
        const params = new URLSearchParams()
        
        params.append('period', selectedPeriod.value)
        if (alertsFilters.severity) params.append('severity', alertsFilters.severity)
        if (alertsFilters.category) params.append('category', alertsFilters.category)
        
        const response = await fetch(`${API_BASE_URL}/inventory/test/alerts?${params}`)
        
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
        
        const response = await fetch(`${API_BASE_URL}/inventory/test/predictions?${params}`)
        
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
        case 'suppliers':
          loadSuppliersData()
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

    // Función para convertir días a texto relativo limpio
    const getRelativeTimeText = (days) => {
      if (days === 0) return 'Hoy'
      if (days === 1) return 'Mañana'
      if (days === 2) return 'En 2 días'
      if (days < 7) return `En ${days} días`
      if (days < 14) return `En ${Math.floor(days / 7)} semana${Math.floor(days / 7) > 1 ? 's' : ''}`
      if (days < 30) return `En ${Math.floor(days / 7)} semanas`
      if (days < 60) return `En ${Math.floor(days / 30)} mes`
      if (days < 365) return `En ${Math.floor(days / 30)} meses`
      return `En ${Math.floor(days / 365)} año${Math.floor(days / 365) > 1 ? 's' : ''}`
    }

    // Función para crear orden de compra (nuevo feature)
    const createPurchaseOrder = (item) => {
      console.log('📦 Creando orden de compra para:', item)
      
      // Determinar cantidad sugerida
      const quantity = item.recommended_purchase || item.current_stock * 2
      const estimatedCost = item.estimated_cost || 0
      
      // Mostrar confirmación con toast
      if (toastRef.value) {
        toastRef.value.show({
          title: '🛒 Orden de Compra Creada',
          message: `Producto: ${item.product_name}\nCantidad: ${quantity} unidades\nCosto estimado: ${formatCurrency(estimatedCost)}`,
          type: 'success',
          autoClose: true,
          duration: 5000,
          actions: [
            {
              label: 'Ver Orden',
              onClick: () => {
                console.log('Ver orden de compra')
                // Aquí puedes navegar a la vista de órdenes de compra
              }
            },
            {
              label: 'Imprimir',
              onClick: () => {
                console.log('Imprimir orden de compra')
                // Aquí puedes generar PDF de la orden
              }
            }
          ]
        })
      } else {
        // Fallback si no hay toast disponible
        alert(`✅ Orden de compra creada:\n\nProducto: ${item.product_name}\nCantidad: ${quantity} unidades\nCosto: ${formatCurrency(estimatedCost)}`)
      }
      
      // TODO: Integrar con backend para crear orden de compra real
      // const response = await fetch(`${API_BASE_URL}/purchase-orders`, {
      //   method: 'POST',
      //   headers: { 'Content-Type': 'application/json' },
      //   body: JSON.stringify({
      //     product_id: item.product_id,
      //     quantity: quantity,
      //     estimated_cost: estimatedCost,
      //     priority: item.priority || item.urgency,
      //     notes: `Orden generada automáticamente por predicción de agotamiento`
      //   })
      // })
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

    // Computed properties para paginación de proveedores
    const suppliersTotalPages = computed(() => {
      if (!suppliersData.value?.suppliers) return 1
      return Math.ceil(suppliersData.value.suppliers.length / suppliersFilters.itemsPerPage)
    })

    const suppliersPaginationInfo = computed(() => {
      const totalItems = suppliersData.value?.suppliers?.length || 0
      const start = (suppliersFilters.currentPage - 1) * suppliersFilters.itemsPerPage + 1
      const end = Math.min(suppliersFilters.currentPage * suppliersFilters.itemsPerPage, totalItems)
      
      return {
        start: totalItems > 0 ? start : 0,
        end: totalItems > 0 ? end : 0,
        total: totalItems
      }
    })

    const paginatedSuppliers = computed(() => {
      if (!suppliersData.value?.suppliers) return []
      const start = (suppliersFilters.currentPage - 1) * suppliersFilters.itemsPerPage
      const end = start + suppliersFilters.itemsPerPage
      return suppliersData.value.suppliers.slice(start, end)
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
      
      // Filtrar según showHealthy
      let filtered = predictionsData.value.stock_depletion
      
      if (!predictionsFilters.showHealthy) {
        // Solo mostrar productos críticos (<7 días) y atención (7-30 días)
        filtered = filtered.filter(item => item.days_until_depletion < 30)
      }
      
      const start = (predictionsCurrentPage.value - 1) * 10
      const end = start + 10
      return filtered.slice(start, end)
    })
    
    // Computed para contar productos críticos y atención
    const criticalProductsCount = computed(() => {
      if (!predictionsData.value?.stock_depletion) return 0
      return predictionsData.value.stock_depletion.filter(i => i.days_until_depletion < 7).length
    })
    
    const warningProductsCount = computed(() => {
      if (!predictionsData.value?.stock_depletion) return 0
      return predictionsData.value.stock_depletion.filter(i => i.days_until_depletion >= 7 && i.days_until_depletion < 30).length
    })
    
    const healthyProductsCount = computed(() => {
      if (!predictionsData.value?.stock_depletion) return 0
      return predictionsData.value.stock_depletion.filter(i => i.days_until_depletion >= 30).length
    })

    // Computed properties para métricas de productos calculadas
    const productsMetrics = computed(() => {
      if (!productsData.value?.products) {
        return {
          total: 0,
          active: 0,
          lowStock: 0,
          totalValueSale: 0,
          totalValueCost: 0
        }
      }
      
      const products = productsData.value.products
      
      return {
        total: productsData.value.summary?.total_products || products.length,
        active: products.filter(p => p.active && p.current_stock > 0).length,
        lowStock: products.filter(p => p.current_stock <= p.min_stock && p.current_stock > 0).length,
        totalValueSale: products.reduce((sum, p) => sum + (parseFloat(p.sale_price || 0) * parseFloat(p.current_stock || 0)), 0),
        totalValueCost: products.reduce((sum, p) => sum + (parseFloat(p.cost_price || 0) * parseFloat(p.current_stock || 0)), 0)
      }
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
      productsMetrics,
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
      suppliersData,
      suppliersFilters,
      suppliersTotalPages,
      suppliersPaginationInfo,
      paginatedSuppliers,
      alertsData,
      alertsCurrentPage,
      alertsFilters,
      paginatedAlerts,
      expandedGroups,
      groupedAlerts,
      predictionsData,
      predictionsCurrentPage,
      predictionsFilters,
      paginatedStockDepletion,
      paginatedPurchaseRecommendations,
      paginatedSalesForecast,
      criticalProductsCount,
      warningProductsCount,
      healthyProductsCount,
      getRelativeTimeText,
      
      // Configuración
      sections,
      
      // Métodos
      formatCurrency,
      formatDate,
      formatNumber,
      calculateProfitMargin,
      formatPercentage,
      getChangeClass,
      getChangeIcon,
      formatMovementType,
      getMovementTypeClass,
      getMovementTypeIcon,
      getInitials, // Helper para avatares con iniciales
      getCurrentSectionName,
      getCurrentSectionIcon,
      getPeriodLabel,
      handlePeriodChange,
      loadDashboardData,
      refreshOverviewData,
      refreshCurrentSection,
      testConnection,
      loadProductsData,
      loadMovementsData,
      loadCustomersData,
      loadSuppliersData,
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
      createPurchaseOrder,
      
      // Funciones de agrupación inteligente de alertas
      getCategoryIcon,
      getCategoryTitle,
      getCategoryDescription,
      toggleAlertGroup,
      viewAlertDetails,
      markAlertAsReviewed,
      justifyAlert,
      markAllAlertsAsReviewed,
      resolveAlertGroup,
      
      // Funciones de movimientos
      viewMovementDocument
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

/* Transiciones para acordeón de alertas */
.expand-enter-active,
.expand-leave-active {
  transition: all 0.3s ease;
  max-height: 2000px;
  overflow: hidden;
}

.expand-enter-from,
.expand-leave-to {
  opacity: 0;
  max-height: 0;
  padding-top: 0;
  padding-bottom: 0;
}

.expand-enter-to,
.expand-leave-from {
  opacity: 1;
  max-height: 2000px;
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