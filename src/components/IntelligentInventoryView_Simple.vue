<template>
  <div class="min-h-screen font-sans bg-gradient-to-b from-gray-50 via-gray-100 to-gray-200 dark:bg-gradient-to-b dark:from-zinc-950 dark:via-slate-900 dark:to-zinc-950 transition-colors duration-300 px-8">
    <div class="p-4 lg:p-6 space-y-6 pb-8 animate-fade-in">
      
      <!-- Header con Navegación Integrada -->
      <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 pb-2">
        <div>
          <h1 class="text-2xl font-semibold text-gray-900 dark:text-white tracking-tight">Inventario Inteligente</h1>
          <p class="text-sm text-gray-400 dark:text-zinc-500 mt-1">Sistema de análisis predictivo y gestión avanzada</p>
        </div>
        
        <div class="flex items-center gap-3">
          <!-- Navegación de Secciones -->
          <nav class="flex items-center bg-gray-100 dark:bg-zinc-800/80 rounded-full p-1">
            <button
              v-for="section in sections"
              :key="section.id"
              @click="switchToSection(section.id)"
              :class="[
                'px-4 py-2 text-sm font-medium rounded-full transition-all duration-200',
                activeSection === section.id
                  ? 'bg-white dark:bg-zinc-700 text-gray-900 dark:text-white shadow-sm'
                  : 'text-gray-500 dark:text-zinc-400 hover:text-gray-900 dark:hover:text-white hover:bg-white/60 dark:hover:bg-zinc-700/50'
              ]"
            >
              {{ section.name }}
            </button>
          </nav>

          <!-- Botón Actualizar -->
          <button 
            @click="refreshCurrentSection"
            class="px-5 py-2.5 bg-gray-900 dark:bg-zinc-700 hover:bg-black dark:hover:bg-zinc-600 text-white text-sm font-medium rounded-full transition-all duration-200 flex items-center gap-2"
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
        
        <!-- Solo mostrar errores - Gemini -->
        <div v-if="error" class="bg-red-50 dark:bg-red-500/10 rounded-md p-3">
          <div class="flex items-center gap-2 text-red-500 dark:text-red-400">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <span class="text-sm font-medium">{{ error }}</span>
          </div>
        </div>
        
        <!-- Filtros de Período - Gemini Style -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 bg-white dark:bg-zinc-900 rounded-md border border-gray-200 dark:border-zinc-800 px-5 py-4">
          <span class="text-sm font-medium text-gray-500 dark:text-zinc-400">Período:</span>
          <div class="flex items-center gap-2 flex-wrap">
            <select 
              v-model="selectedPeriod"
              @change="handlePeriodChange"
              class="bg-white dark:bg-zinc-800 rounded-full px-4 py-2.5 text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-[#1a73e8] dark:focus:ring-[#8ab4f8] focus:outline-none min-w-[140px] font-medium"
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
                @change="loadDashboardData"
                :max="new Date().toISOString().split('T')[0]"
                class="bg-white dark:bg-zinc-800 rounded-full px-4 py-2.5 text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-[#1a73e8] focus:outline-none min-w-[150px]"
              >
            </template>
          </div>
        </div>

        <!-- Dashboard Principal - KPIs Metrics Ribbon -->
        <div class="bg-white dark:bg-zinc-900 rounded-md border border-gray-200 dark:border-zinc-800 grid grid-cols-2 lg:grid-cols-4 divide-x divide-gray-100 dark:divide-zinc-800">
          <div class="flex flex-col gap-1 px-5 py-4">
            <div class="flex items-center justify-between">
              <p class="text-xs text-gray-400 dark:text-zinc-500 uppercase tracking-wider font-medium">Productos Activos</p>
              <svg class="w-4 h-4 text-blue-500 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
            </div>
            <p class="text-2xl font-black text-gray-900 dark:text-white tabular-nums">{{ metrics.activeProducts || 0 }}</p>
            <p class="text-xs text-gray-400 dark:text-zinc-500">de {{ metrics.totalProducts || 0 }} totales</p>
          </div>
          <div class="flex flex-col gap-1 px-5 py-4">
            <div class="flex items-center justify-between">
              <p class="text-xs text-gray-400 dark:text-zinc-500 uppercase tracking-wider font-medium">Valor Invertido</p>
              <svg class="w-4 h-4 text-emerald-500 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <p class="text-2xl font-black text-gray-900 dark:text-white tabular-nums">{{ formatCurrency(metrics.totalInventoryValue) }}</p>
            <p class="text-xs text-gray-400 dark:text-zinc-500">costo × stock</p>
          </div>
          <div class="flex flex-col gap-1 px-5 py-4">
            <div class="flex items-center justify-between">
              <p class="text-xs text-gray-400 dark:text-zinc-500 uppercase tracking-wider font-medium">Valor Potencial</p>
              <svg class="w-4 h-4 text-violet-500 dark:text-violet-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
            </div>
            <p class="text-2xl font-black text-gray-900 dark:text-white tabular-nums">{{ formatCurrency(metrics.totalSaleValue || 0) }}</p>
            <p class="text-xs text-gray-400 dark:text-zinc-500">precio × stock</p>
          </div>
          <div class="flex flex-col gap-1 px-5 py-4">
            <div class="flex items-center justify-between">
              <p class="text-xs text-gray-400 dark:text-zinc-500 uppercase tracking-wider font-medium">Ganancia Est.</p>
              <svg class="w-4 h-4 text-amber-500 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <p class="text-2xl font-black text-gray-900 dark:text-white tabular-nums">{{ formatCurrency((metrics.totalSaleValue || 0) - (metrics.totalInventoryValue || 0)) }}</p>
            <p class="text-xs text-gray-400 dark:text-zinc-500">venta - costo</p>
          </div>
        </div>

        <!-- Métricas Secundarias - Metrics Ribbon -->
        <div class="bg-white dark:bg-zinc-900 rounded-md border border-gray-200 dark:border-zinc-800 grid grid-cols-2 lg:grid-cols-5 divide-x divide-gray-100 dark:divide-zinc-800">
          <div class="flex flex-col gap-1 px-5 py-4">
            <p class="text-xs text-gray-400 dark:text-zinc-500 uppercase tracking-wider font-medium">Ventas</p>
            <p class="text-2xl font-black text-gray-900 dark:text-white tabular-nums">{{ formatCurrency(metrics.monthlySales || 0) }}</p>
            <p class="text-xs text-gray-400 dark:text-zinc-500">{{ monthlyTransactions || 0 }} transacciones</p>
          </div>
          <div class="flex flex-col gap-1 px-5 py-4">
            <p class="text-xs text-gray-400 dark:text-zinc-500 uppercase tracking-wider font-medium">Alertas Stock</p>
            <div class="flex items-baseline gap-1">
              <span class="text-2xl font-black text-amber-500 dark:text-amber-400 tabular-nums">{{ metrics.lowStockProducts || 0 }}</span>
              <span class="text-xs text-gray-400 dark:text-zinc-500">/</span>
              <span class="text-2xl font-black text-red-500 dark:text-red-400 tabular-nums">{{ metrics.outOfStockProducts || 0 }}</span>
            </div>
            <p class="text-xs text-gray-400 dark:text-zinc-500">bajo / sin stock</p>
          </div>
          <div class="flex flex-col gap-1 px-5 py-4">
            <p class="text-xs text-gray-400 dark:text-zinc-500 uppercase tracking-wider font-medium">Ganancias</p>
            <p class="text-2xl font-black text-emerald-600 dark:text-emerald-400 tabular-nums">{{ formatCurrency(metrics.monthlySales || 0) }}</p>
            <p class="text-xs text-gray-400 dark:text-zinc-500">{{ getPeriodLabel() }}</p>
          </div>
          <div class="flex flex-col gap-1 px-5 py-4">
            <p class="text-xs text-gray-400 dark:text-zinc-500 uppercase tracking-wider font-medium">Gastos</p>
            <p class="text-2xl font-black text-gray-900 dark:text-white tabular-nums">{{ formatCurrency(metrics.totalExpenses || 0) }}</p>
            <p class="text-xs text-gray-400 dark:text-zinc-500">{{ getPeriodLabel() }}</p>
          </div>
          <div class="flex flex-col gap-1 px-5 py-4">
            <p class="text-xs text-gray-400 dark:text-zinc-500 uppercase tracking-wider font-medium">Ganancia Neta</p>
            <p class="text-2xl font-black text-emerald-600 dark:text-emerald-400 tabular-nums">{{ formatCurrency(metrics.netProfit || 0) }}</p>
            <p class="text-xs text-gray-400 dark:text-zinc-500">ventas - gastos</p>
          </div>
        </div>

        <!-- Sección de Productos y Movimientos - Gemini Cards -->
        <div v-if="overviewData" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
          <!-- Top Productos -->
          <div v-if="overviewData.data.topSellingProducts?.length > 0" class="bg-white dark:bg-zinc-900 rounded-md border border-gray-200 dark:border-zinc-800 overflow-hidden">
            <div class="px-5 py-4">
              <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-amber-50 dark:bg-amber-500/10 rounded-full flex items-center justify-center flex-shrink-0">
                  <svg class="w-5 h-5 text-amber-500 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path>
                  </svg>
                </div>
                <div>
                  <h3 class="text-base font-medium text-gray-900 dark:text-white">Top Productos</h3>
                  <p class="text-xs text-gray-500 dark:text-zinc-400">Más vendidos del período</p>
                </div>
              </div>
            </div>
            <div class="px-5 pb-5 space-y-2">
              <div v-for="(product, index) in overviewData.data.topSellingProducts.slice(0, 5)" 
                   :key="product.id" 
                   class="flex items-center gap-3 p-3 bg-white dark:bg-zinc-800 hover:bg-gray-100 dark:hover:bg-zinc-700 rounded-md transition-all duration-200">
                <!-- Ranking Badge -->
                <div class="w-8 h-8 bg-blue-50 dark:bg-blue-500/10 rounded-full flex items-center justify-center text-blue-600 dark:text-blue-400 font-semibold text-sm flex-shrink-0">
                  {{ index + 1 }}
                </div>
                
                <!-- Imagen del producto -->
                <div class="w-10 h-10 rounded-full overflow-hidden bg-gray-100 dark:bg-zinc-700 flex items-center justify-center flex-shrink-0">
                  <img v-if="product.image_url" :src="product.image_url" :alt="product.name" class="w-full h-full object-cover" />
                  <svg v-else class="w-5 h-5 text-gray-500 dark:text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                  </svg>
                </div>
                
                <!-- Info del producto -->
                <div class="flex-1 min-w-0">
                  <p class="font-medium text-gray-900 dark:text-white text-sm truncate">{{ product.name }}</p>
                  <p class="text-xs text-gray-500 dark:text-zinc-400 mt-0.5">
                    {{ product.total_quantity_sold }} unidades vendidas
                  </p>
                </div>
                
                <!-- Revenue -->
                <div class="text-right flex-shrink-0">
                  <p class="font-semibold text-emerald-600 dark:text-emerald-400 text-sm">{{ formatCurrency(product.total_revenue) }}</p>
                  <p class="text-xs text-gray-500 dark:text-zinc-400">ingresos</p>
                </div>
              </div>
            </div>
          </div>

          <!-- Stock Bajo -->
          <div v-if="overviewData.data.lowStockProductsList?.length > 0" class="bg-white dark:bg-zinc-900 rounded-md border border-gray-200 dark:border-zinc-800 overflow-hidden">
            <div class="px-5 py-4">
              <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-red-50 dark:bg-red-500/10 rounded-full flex items-center justify-center flex-shrink-0">
                  <svg class="w-5 h-5 text-red-500 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                  </svg>
                </div>
                <div>
                  <h3 class="text-base font-medium text-gray-900 dark:text-white">Stock Bajo</h3>
                  <p class="text-xs text-gray-500 dark:text-zinc-400">Productos que necesitan reposición</p>
                </div>
              </div>
            </div>
            <div class="px-5 pb-5 space-y-2">
              <div v-for="product in overviewData.data.lowStockProductsList.slice(0, 5)" 
                   :key="product.id" 
                   class="flex items-center gap-3 p-3 bg-white dark:bg-zinc-800 hover:bg-gray-100 dark:hover:bg-zinc-700 rounded-md transition-all duration-200">
                <div class="w-10 h-10 rounded-full overflow-hidden bg-gray-100 dark:bg-zinc-700 flex items-center justify-center flex-shrink-0">
                  <img v-if="product.image_url" :src="product.image_url" :alt="product.name" class="w-full h-full object-cover" />
                  <svg v-else class="w-5 h-5 text-gray-500 dark:text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                  </svg>
                </div>
                <div class="flex-1 min-w-0">
                  <p class="font-medium text-gray-900 dark:text-white text-sm truncate">{{ product.name }}</p>
                  <p class="text-xs text-gray-500 dark:text-zinc-400 mt-0.5">{{ product.category?.name || 'Sin categoría' }}</p>
                </div>
                <div class="text-right flex-shrink-0">
                  <div class="flex items-center gap-1.5">
                    <div class="w-1.5 h-1.5 rounded-full animate-pulse" :class="product.current_stock === 0 ? 'bg-red-500' : 'bg-amber-500'"></div>
                    <p class="font-semibold text-sm" :class="product.current_stock === 0 ? 'text-red-500 dark:text-red-400' : 'text-amber-500 dark:text-amber-400'">
                      {{ product.current_stock }}<span class="text-zinc-400">/</span>{{ product.min_stock }}
                    </p>
                  </div>
                  <p class="text-xs text-gray-500 dark:text-zinc-400">{{ product.current_stock === 0 ? 'Agotado' : 'Reponer' }}</p>
                </div>
              </div>
            </div>
          </div>

          <!-- Movimientos Recientes -->
          <div v-if="overviewData.data.recentMovements?.length > 0" class="bg-white dark:bg-zinc-900 rounded-md border border-gray-200 dark:border-zinc-800 overflow-hidden">
              <div class="px-5 py-4">
                <div class="flex items-center gap-3 mb-4">
                  <div class="w-10 h-10 bg-blue-50 dark:bg-blue-500/10 rounded-full flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"></path>
                    </svg>
                  </div>
                  <div>
                    <h3 class="text-base font-medium text-gray-900 dark:text-white">Movimientos</h3>
                    <p class="text-xs text-gray-500 dark:text-zinc-400">Actividad reciente</p>
                  </div>
                </div>
                <div class="space-y-2">
                  <div v-for="movement in overviewData.data.recentMovements.slice(0, 5)" 
                       :key="movement.id" 
                       class="flex items-center gap-3 p-3 bg-white dark:bg-zinc-800 hover:bg-gray-100 dark:hover:bg-zinc-700 transition-all duration-200 rounded-md">
                    <div class="w-8 h-8 rounded-full flex items-center justify-center flex-shrink-0" :class="movement.quantity > 0 ? 'bg-emerald-50 dark:bg-emerald-500/10' : 'bg-red-50 dark:bg-red-500/10'">
                      <svg v-if="movement.quantity > 0" class="w-4 h-4 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 14l-7 7m0 0l-7-7m7 7V3" />
                      </svg>
                      <svg v-else class="w-4 h-4 text-red-500 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 10l7-7m0 0l7 7m-7-7v18" />
                      </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                      <p class="font-medium text-gray-900 dark:text-white text-sm truncate">{{ movement.product_name || movement.product?.name }}</p>
                      <p class="text-xs text-gray-500 dark:text-zinc-400 mt-0.5">{{ formatMovementType(movement.type) }}</p>
                    </div>
                    <div class="text-right flex-shrink-0">
                      <p class="font-semibold text-sm" :class="movement.quantity > 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-500 dark:text-red-400'">
                        {{ movement.quantity > 0 ? '+' : '' }}{{ movement.quantity }}
                      </p>
                      <p class="text-xs text-gray-500 dark:text-zinc-400 mt-0.5">unidades</p>
                    </div>
                  </div>
                </div>
              </div>
          </div>
        </div>
      </div>

      <!-- Vista por Productos -->
      <div v-if="activeSection === 'products'" class="space-y-4">
        
        <!-- Filtros Compactos - Gemini -->
        <div class="bg-white dark:bg-zinc-900 rounded-md border border-gray-200 dark:border-zinc-800 p-4">
          <div class="flex flex-wrap items-center gap-3">
            <!-- Búsqueda -->
            <div class="flex-1 min-w-48 relative">
              <svg class="absolute left-4 top-1/2 transform -translate-y-1/2 w-4 h-4 text-gray-500 dark:text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
              </svg>
              <input
                v-model="filters.search"
                @input="debounceSearch"
                type="text"
                placeholder="Buscar productos..."
                class="w-full pl-10 pr-4 py-2.5 text-sm rounded-full bg-white dark:bg-zinc-800 text-gray-900 dark:text-white placeholder-[#9aa0a6] focus:outline-none focus:ring-2 focus:ring-[#1a73e8] dark:focus:ring-[#8ab4f8]"
              >
            </div>
            
            <!-- Selector de Período -->
            <select 
              v-model="selectedPeriod"
              @change="handlePeriodChange"
              class="px-4 py-2.5 text-sm rounded-full bg-white dark:bg-zinc-800 text-gray-900 dark:text-white font-medium focus:outline-none focus:ring-2 focus:ring-[#1a73e8] dark:focus:ring-[#8ab4f8] min-w-36"
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
                  class="px-4 py-2.5 text-sm rounded-full bg-white dark:bg-zinc-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-[#1a73e8] dark:focus:ring-[#8ab4f8] focus:outline-none min-w-[150px]"
                >
              </div>
            </template>

            <!-- Filtro por Categoría -->
            <select 
              v-model="filters.category"
              @change="loadProductsData"
              class="px-4 py-2.5 text-sm rounded-full bg-white dark:bg-zinc-800 text-gray-900 dark:text-white font-medium focus:outline-none focus:ring-2 focus:ring-[#1a73e8] dark:focus:ring-[#8ab4f8] min-w-44"
            >
              <option value="">Todas las categorías</option>
              <option v-for="cat in availableCategories" :key="cat.id" :value="cat.id">
                {{ cat.name }}
              </option>
            </select>

            <!-- Selector de Bodega/Tienda (solo para Premium/Enterprise con múltiples bodegas) -->
            <select 
              v-if="showWarehouseSelector"
              v-model="selectedWarehouse"
              @change="loadProductsData"
              class="px-4 py-2.5 text-sm rounded-full bg-violet-50 dark:bg-violet-500/10 text-violet-600 dark:text-violet-400 font-medium focus:outline-none focus:ring-2 focus:ring-[#7c3aed] dark:focus:ring-[#a78bfa] min-w-48"
            >
              <option value="">Todas las Sedes</option>
              <option v-for="warehouse in warehouses" :key="warehouse.id" :value="warehouse.id">
                {{ warehouse.is_default ? '' : '' }}{{ warehouse.name }}
              </option>
            </select>

            <!-- Botón Limpiar Filtros -->
            <button 
              @click="clearFilters" 
              class="p-2.5 text-gray-500 dark:text-zinc-400 hover:text-red-600 dark:hover:text-red-400 hover:bg-red-50 dark:hover:bg-red-500/20 rounded-full transition-all"
              title="Limpiar filtros">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
              </svg>
            </button>
          </div>
        </div>

        <!-- KPIs de Productos - Metrics Ribbon -->
        <div class="bg-white dark:bg-zinc-900 rounded-md border border-gray-200 dark:border-zinc-800 grid grid-cols-2 lg:grid-cols-4 divide-x divide-gray-100 dark:divide-zinc-800">
          <div class="flex flex-col gap-1 px-5 py-4">
            <div class="flex items-center justify-between">
              <p class="text-xs text-gray-400 dark:text-zinc-500 uppercase tracking-wider font-medium">Total Productos</p>
              <svg class="w-4 h-4 text-violet-500 dark:text-violet-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
            </div>
            <p class="text-2xl font-black text-gray-900 dark:text-white tabular-nums">{{ productsMetrics.total }}</p>
            <p class="text-xs text-gray-400 dark:text-zinc-500">en el sistema</p>
          </div>
          <div class="flex flex-col gap-1 px-5 py-4">
            <div class="flex items-center justify-between">
              <p class="text-xs text-gray-400 dark:text-zinc-500 uppercase tracking-wider font-medium">Activos</p>
              <svg class="w-4 h-4 text-emerald-500 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <p class="text-2xl font-black text-gray-900 dark:text-white tabular-nums">{{ productsMetrics.active }}</p>
            <p class="text-xs text-emerald-500 dark:text-emerald-400">disponibles</p>
          </div>
          <div class="flex flex-col gap-1 px-5 py-4">
            <div class="flex items-center justify-between">
              <p class="text-xs text-gray-400 dark:text-zinc-500 uppercase tracking-wider font-medium">Sin Stock</p>
              <svg class="w-4 h-4 text-amber-500 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg>
            </div>
            <p class="text-2xl font-black text-gray-900 dark:text-white tabular-nums">{{ productsMetrics.outOfStock }}</p>
            <p class="text-xs text-amber-500 dark:text-amber-400">agotados</p>
          </div>
          <div class="flex flex-col gap-1 px-5 py-4">
            <div class="flex items-center justify-between">
              <p class="text-xs text-gray-400 dark:text-zinc-500 uppercase tracking-wider font-medium">Valor Potencial</p>
              <svg class="w-4 h-4 text-emerald-500 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <p class="text-2xl font-black text-gray-900 dark:text-white tabular-nums">{{ formatCurrency(productsMetrics.totalValueSale) }}</p>
            <p class="text-xs text-gray-400 dark:text-zinc-500">Invertido: {{ formatCurrency(productsMetrics.totalValueCost) }}</p>
          </div>
        </div>



        <!-- Tabla de Productos - Gemini -->
        <div v-if="productsData && productsData.products && productsData.products.length > 0" class="bg-white dark:bg-zinc-900 rounded-md border border-gray-200 dark:border-zinc-800 overflow-hidden">
          <div class="px-5 py-4">
            <div class="flex items-center justify-between">
              <div>
                <h3 class="text-base font-medium text-gray-900 dark:text-white">Lista de Productos</h3>
                <p class="text-xs text-gray-500 dark:text-zinc-400 mt-1">{{ totalProductItems }} productos encontrados</p>
              </div>
            </div>
          </div>
          
          <div class="overflow-x-auto">
            <table class="min-w-full">
              <thead class="bg-white dark:bg-zinc-800">
                <tr>
                  <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-zinc-400 uppercase tracking-wide">Producto</th>
                  <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-zinc-400 uppercase tracking-wide">Categoría</th>
                  <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-zinc-400 uppercase tracking-wide">Stock</th>
                  <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-zinc-400 uppercase tracking-wide">Precio</th>
                  <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-zinc-400 uppercase tracking-wide">Rotación</th>
                  <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-zinc-400 uppercase tracking-wide">Rentabilidad</th>
                </tr>
              </thead>
              <tbody class="bg-white dark:bg-zinc-900">
                <tr v-for="product in paginatedProductsList" :key="product.id"
                    class="hover:bg-gray-50 dark:hover:bg-zinc-800 transition-colors border-b border-gray-200 dark:border-zinc-800 last:border-b-0">
                  <td class="px-4 py-3">
                    <div class="text-sm font-medium text-gray-900 dark:text-white">{{ product.name }}</div>
                    <div class="text-xs text-gray-500 dark:text-zinc-400 mt-0.5">SKU: {{ product.sku || 'N/A' }}</div>
                  </td>
                  <td class="px-4 py-3">
                    <span class="inline-flex items-center bg-blue-50 dark:bg-blue-500/10 text-blue-600 dark:text-blue-400 rounded-full px-3 py-0.5 text-xs font-medium">
                      {{ product.category_name || product.category || 'Sin categoría' }}
                    </span>
                  </td>
                  <td class="px-4 py-3 text-center">
                    <div class="font-mono text-base font-semibold" :class="[
                      product.current_stock <= 0 ? 'text-red-500 dark:text-red-400' :
                      product.current_stock <= product.min_stock ? 'text-amber-500 dark:text-amber-400' :
                      'text-gray-900 dark:text-white'
                    ]">
                      {{ product.current_stock }}
                    </div>
                    <div class="text-xs text-gray-500 dark:text-zinc-400 mt-0.5">
                      {{ getMeasurementUnitLabel(product.measurement_unit || product.unit) }}
                    </div>
                  </td>
                  <td class="px-4 py-3 text-center">
                    <div class="font-mono text-base font-semibold text-gray-900 dark:text-white">{{ formatCurrency(product.sale_price) }}</div>
                    <div class="text-xs text-gray-500 dark:text-zinc-400 mt-0.5">Costo: {{ formatCurrency(product.cost_price) }}</div>
                  </td>
                  <td class="px-4 py-3 text-center">
                    <span 
                      :class="[
                        'px-3 py-0.5 text-xs font-medium rounded-full cursor-help',
                        product.rotation_class === 'A' ? 'bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400' :
                        product.rotation_class === 'B' ? 'bg-blue-50 dark:bg-blue-500/10 text-blue-600 dark:text-blue-400' :
                        'bg-amber-50 dark:bg-amber-500/10 text-amber-500 dark:text-amber-400'
                      ]"
                      :title="getRotationTooltip(product.rotation_class || 'C')"
                    >
                      Clase {{ product.rotation_class || 'C' }}
                    </span>
                    <div class="text-xs text-gray-500 dark:text-zinc-400 mt-1">{{ product.units_sold || 0 }} unidades vendidas</div>
                  </td>
                  <td class="px-4 py-3 text-center">
                    <div class="font-mono text-lg font-semibold" :class="[
                      parseFloat(product.margin_percentage || 0) >= 40 ? 'text-emerald-600 dark:text-emerald-400' :
                      parseFloat(product.margin_percentage || 0) >= 20 ? 'text-blue-600 dark:text-blue-400' :
                      'text-amber-500 dark:text-amber-400'
                    ]">
                      {{ parseFloat(product.margin_percentage || 0).toFixed(1) }}%
                    </div>
                    <div class="text-xs text-gray-500 dark:text-zinc-400 mt-0.5">margen</div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
          
          <!-- Paginador usando componente estándar -->
          <TablePaginator
            v-if="totalProductItems > 10"
            v-model:currentPage="filters.currentPage"
            v-model:itemsPerPage="filters.itemsPerPage"
            :totalPages="totalProductPages"
            :totalItems="totalProductItems"
            label="productos"
          />
        </div>
        
        <!-- Estado Vacío - Gemini -->
        <div v-else-if="productsData && productsData.products && productsData.products.length === 0" class="bg-white dark:bg-zinc-900 rounded-md border border-gray-200 dark:border-zinc-800 p-12">
          <div class="text-center">
            <svg class="w-16 h-16 mx-auto text-gray-200 dark:text-zinc-700 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
            </svg>
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">No hay productos</h3>
            <p class="text-sm text-gray-500 dark:text-zinc-400">No se encontraron productos con los filtros seleccionados.</p>
          </div>
        </div>
        
        <!-- Cargando - Gemini -->
        <div v-else class="bg-white dark:bg-zinc-900 rounded-md border border-gray-200 dark:border-zinc-800 p-12">
          <div class="text-center">
            <svg class="w-16 h-16 mx-auto text-blue-600 dark:text-blue-400 animate-spin mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
            </svg>
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">Cargando productos...</h3>
            <p class="text-sm text-gray-500 dark:text-zinc-400">Por favor espera un momento.</p>
          </div>
        </div>
      </div>

      <div v-if="activeSection === 'movements'" class="space-y-6 animate-fade-in">
        <!-- Header y Filtros - Gemini -->
        <div class="bg-white dark:bg-zinc-900 rounded-md border border-gray-200 dark:border-zinc-800 px-5 py-4">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h2 class="text-lg font-medium text-gray-900 dark:text-white">Movimientos de Inventario</h2>
                    <p class="text-sm text-gray-500 dark:text-zinc-400">Registro detallado de entradas y salidas</p>
                </div>
                
                <div class="flex flex-wrap items-center gap-3">
                    <!-- Selector de Período -->
                    <select 
                      v-model="selectedPeriod"
                      @change="handlePeriodChange"
                      class="px-4 py-2.5 text-sm rounded-full bg-white dark:bg-zinc-800 text-gray-900 dark:text-white font-medium focus:outline-none focus:ring-2 focus:ring-[#1a73e8] dark:focus:ring-[#8ab4f8]"
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
                          class="px-4 py-2.5 text-sm rounded-full bg-white dark:bg-zinc-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-[#1a73e8] focus:outline-none min-w-[150px]"
                        >
                      </div>
                    </template>
        
                    <!-- Filtro por Tipo -->
                    <select 
                      v-model="movementsFilters.type"
                      @change="loadMovementsData"
                      class="px-4 py-2.5 text-sm rounded-full bg-white dark:bg-zinc-800 text-gray-900 dark:text-white font-medium focus:outline-none focus:ring-2 focus:ring-[#1a73e8] dark:focus:ring-[#8ab4f8]"
                    >
                      <option value="">Todos los tipos</option>
                      <option value="entry">Solo Entradas</option>
                      <option value="exit">Solo Salidas</option>
                    </select>
        
                    <!-- Botón Exportar - Gemini Green -->
                    <button 
                      @click="exportMovements"
                      class="px-5 py-2.5 text-sm font-medium text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-500/10 hover:bg-emerald-100 dark:hover:bg-emerald-600/30 rounded-full transition-all inline-flex items-center"
                    >
                      <i class="fas fa-file-excel mr-2"></i>
                      Exportar
                    </button>
                </div>
            </div>
        </div>

        <!-- 4 KPIs principales - Metrics Ribbon -->
        <div class="bg-white dark:bg-zinc-900 rounded-md border border-gray-200 dark:border-zinc-800 grid grid-cols-2 lg:grid-cols-4 divide-x divide-gray-100 dark:divide-zinc-800">
          <div class="flex flex-col gap-1 px-5 py-4">
            <div class="flex items-center justify-between">
              <p class="text-xs text-gray-400 dark:text-zinc-500 uppercase tracking-wider font-medium">Movimientos</p>
              <svg class="w-4 h-4 text-blue-500 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
            </div>
            <p class="text-2xl font-black text-gray-900 dark:text-white tabular-nums">{{ movementsData?.summary?.total_movements || 0 }}</p>
            <p class="text-xs text-gray-400 dark:text-zinc-500">registros totales</p>
          </div>
          <div class="flex flex-col gap-1 px-5 py-4">
            <div class="flex items-center justify-between">
              <p class="text-xs text-gray-400 dark:text-zinc-500 uppercase tracking-wider font-medium">Entradas</p>
              <svg class="w-4 h-4 text-emerald-500 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19 14l-7 7m0 0l-7-7m7 7V3"/></svg>
            </div>
            <p class="text-2xl font-black text-gray-900 dark:text-white tabular-nums">{{ movementsData?.summary?.total_entries || 0 }}</p>
            <p class="text-xs text-emerald-500 dark:text-emerald-400">{{ formatCurrency(movementsData?.summary?.total_entry_value || 0) }}</p>
          </div>
          <div class="flex flex-col gap-1 px-5 py-4">
            <div class="flex items-center justify-between">
              <p class="text-xs text-gray-400 dark:text-zinc-500 uppercase tracking-wider font-medium">Salidas</p>
              <svg class="w-4 h-4 text-red-500 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 10l7-7m0 0l7 7m-7-7v18"/></svg>
            </div>
            <p class="text-2xl font-black text-gray-900 dark:text-white tabular-nums">{{ movementsData?.summary?.total_exits || 0 }}</p>
            <p class="text-xs text-red-500 dark:text-red-400">{{ formatCurrency(movementsData?.summary?.total_exit_value || 0) }}</p>
          </div>
          <div class="flex flex-col gap-1 px-5 py-4">
            <div class="flex items-center justify-between">
              <p class="text-xs text-gray-400 dark:text-zinc-500 uppercase tracking-wider font-medium">Balance</p>
              <svg class="w-4 h-4 text-violet-500 dark:text-violet-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"/></svg>
            </div>
            <p class="text-2xl font-black tabular-nums" :class="(movementsData?.summary?.net_movement || 0) >= 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-500 dark:text-red-400'">
              {{ formatCurrency(movementsData?.summary?.net_movement || 0) }}
            </p>
            <p class="text-xs text-gray-400 dark:text-zinc-500">neto del período</p>
          </div>
        </div>


        <!-- Tabla de Movimientos - Gemini -->
        <div class="bg-white dark:bg-zinc-900 rounded-md border border-gray-200 dark:border-zinc-800 overflow-hidden">
          <div class="px-6 py-4 flex items-center justify-between">
            <div>
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Historial de Movimientos</h3>
                <p class="text-xs text-gray-500 dark:text-zinc-400 mt-1">Registro cronológico de operaciones</p>
            </div>
            <div class="flex items-center gap-2">
                <span class="px-3 py-1.5 bg-blue-50 dark:bg-blue-500/10 text-blue-600 dark:text-blue-400 text-xs font-medium rounded-full">
                    {{ movementsData?.movements?.length || 0 }} registros
                </span>
            </div>
          </div>
          <div class="overflow-x-auto">
            <table class="min-w-full">
              <thead class="bg-gray-50 dark:bg-zinc-800">
                <tr>
                  <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-zinc-400 uppercase tracking-wide">Fecha</th>
                  <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-zinc-400 uppercase tracking-wide">Flujo</th>
                  <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-zinc-400 uppercase tracking-wide">Tipo Movimiento</th>
                  <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-zinc-400 uppercase tracking-wide">Producto</th>
                  <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-zinc-400 uppercase tracking-wide">Cantidad</th>
                  <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-zinc-400 uppercase tracking-wide">Precio Unit.</th>
                  <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-zinc-400 uppercase tracking-wide">Total</th>
                  <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-zinc-400 uppercase tracking-wide">Fuente</th>
                </tr>
              </thead>
              <tbody>
                <!-- Movimientos con iconografía de flujo -->
                <tr v-for="movement in movementsData?.movements || []" :key="movement.movement_id" 
                    class="hover:bg-gray-50 dark:hover:bg-zinc-800 transition-colors duration-150 border-b border-gray-200 dark:border-zinc-800 last:border-b-0"
                    :class="movement.movement_type === 'entry' ? 'border-l-4 border-l-emerald-500 dark:border-l-emerald-400' : 'border-l-4 border-l-red-500 dark:border-l-red-400'">
                  
                  <!-- Fecha -->
                  <td class="px-4 py-3 whitespace-nowrap">
                    <div class="flex items-center gap-2">
                      <i class="fas fa-calendar-day text-zinc-400 text-xs"></i>
                      <span class="text-sm text-gray-900 dark:text-white font-medium">{{ formatDate(movement.movement_date) }}</span>
                    </div>
                  </td>
                  
                  <!-- FLUJO - Flechas SVG Minimalistas -->
                  <td class="px-4 py-3 whitespace-nowrap">
                    <!-- Entrada: Flecha hacia abajo (Verde Gemini) -->
                    <svg v-if="movement.movement_type === 'entry'" 
                         class="w-6 h-6 text-emerald-600 dark:text-emerald-400" 
                         fill="none" 
                         stroke="currentColor" 
                         viewBox="0 0 24 24"
                         title="Entrada">
                      <path stroke-linecap="round" 
                            stroke-linejoin="round" 
                            stroke-width="2.5" 
                            d="M19 14l-7 7m0 0l-7-7m7 7V3"></path>
                    </svg>
                    <!-- Salida: Flecha hacia arriba (Rojo Gemini) -->
                    <svg v-else-if="movement.movement_type === 'exit'" 
                         class="w-6 h-6 text-red-500 dark:text-red-400" 
                         fill="none" 
                         stroke="currentColor" 
                         viewBox="0 0 24 24"
                         title="Salida">
                      <path stroke-linecap="round" 
                            stroke-linejoin="round" 
                            stroke-width="2.5" 
                            d="M5 10l7-7m0 0l7 7m-7-7v18"></path>
                    </svg>
                    <!-- Ajuste: Flecha bidireccional (Azul Gemini) -->
                    <svg v-else 
                         class="w-6 h-6 text-blue-600 dark:text-blue-400" 
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
                    <span class="text-sm font-medium text-gray-900 dark:text-white">{{ movement.movement_reason }}</span>
                  </td>
                  
                  <!-- Producto - Solo Nombre -->
                  <td class="px-4 py-3">
                    <span class="text-sm font-semibold text-gray-900 dark:text-white truncate max-w-xs">{{ movement.product_name }}</span>
                  </td>
                  
                  <!-- CANTIDAD - Con formato de color Gemini -->
                  <td class="px-4 py-3 whitespace-nowrap">
                    <div class="flex items-center gap-2">
                      <!-- Signo visual -->
                      <span v-if="movement.movement_type === 'entry'" 
                            class="inline-flex items-center justify-center w-6 h-6 bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 rounded-full text-xs font-semibold">
                        +
                      </span>
                      <span v-else-if="movement.movement_type === 'exit'" 
                            class="inline-flex items-center justify-center w-6 h-6 bg-red-50 dark:bg-red-500/10 text-red-500 dark:text-red-400 rounded-full text-xs font-semibold">
                        -
                      </span>
                      <span v-else 
                            class="inline-flex items-center justify-center w-6 h-6 bg-blue-50 dark:bg-blue-500/10 text-blue-600 dark:text-blue-400 rounded-full text-xs font-semibold">
                        =
                      </span>
                      
                      <!-- Número con color -->
                      <span class="text-base font-semibold" 
                            :class="movement.movement_type === 'entry' 
                              ? 'text-emerald-600 dark:text-emerald-400' 
                              : movement.movement_type === 'exit' 
                                ? 'text-red-500 dark:text-red-400' 
                                : 'text-blue-600 dark:text-blue-400'">
                        {{ Math.abs(movement.quantity) }}
                      </span>
                    </div>
                  </td>
                  
                  <!-- Precio Unitario -->
                  <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500 dark:text-zinc-400 font-medium">
                    {{ formatCurrency(movement.unit_price) }}
                  </td>
                  
                  <!-- Total con color -->
                  <td class="px-4 py-3 whitespace-nowrap">
                    <span class="text-sm font-semibold" 
                          :class="movement.movement_type === 'entry' 
                            ? 'text-emerald-600 dark:text-emerald-400' 
                            : movement.movement_type === 'exit' 
                              ? 'text-red-500 dark:text-red-400' 
                              : 'text-blue-600 dark:text-blue-400'">
                      {{ formatCurrency(Math.abs(movement.total_value)) }}
                    </span>
                  </td>
                  
                  <!-- FUENTE - Enlace Sutil -->
                  <td class="px-4 py-3 whitespace-nowrap">
                    <button v-if="movement.document_number && movement.document_number !== 'N/A'"
                            @click="viewMovementDocument(movement)"
                            class="text-sm font-mono text-blue-600 dark:text-blue-400 hover:underline decoration-1 underline-offset-2 transition-all duration-150 cursor-pointer">
                      {{ movement.document_number }}
                    </button>
                    <span v-else class="text-sm text-zinc-400 italic">
                      —
                    </span>
                  </td>
                </tr>
                
                <!-- Estado sin movimientos -->
                <tr v-if="!movementsData?.movements || movementsData.movements.length === 0">
                  <td colspan="7" class="px-6 py-12 text-center">
                    <div class="flex flex-col items-center justify-center py-8">
                      <div class="w-16 h-16 bg-gray-50 dark:bg-zinc-800 rounded-full flex items-center justify-center mb-4">
                        <i class="fas fa-box-open text-zinc-400 text-2xl"></i>
                      </div>
                      <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-1">No hay movimientos</h3>
                      <p class="text-sm text-gray-500 dark:text-zinc-400">No se encontraron registros con los filtros seleccionados</p>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
          
          <!-- Controles de Paginación para Movimientos - Gemini -->
          <div v-if="movementsData?.movements && movementsData.movements.length > 0" class="bg-gray-50 dark:bg-zinc-800 px-6 py-3 border-t border-gray-200 dark:border-zinc-800">
            <div class="flex items-center justify-between">
              <!-- Items por página y información -->
              <div class="flex items-center gap-4">
                <div class="flex items-center gap-2">
                  <label class="text-sm text-gray-500 dark:text-zinc-400">Mostrar:</label>
                  <select v-model="movementsFilters.itemsPerPage" 
                          @change="movementsFilters.currentPage = 1; loadMovementsData()"
                          class="text-sm px-3 py-1.5 rounded-full bg-white dark:bg-zinc-900 text-gray-900 dark:text-white focus:ring-2 focus:ring-[#1a73e8] focus:outline-none">
                    <option value="10">10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                  </select>
                  <span class="text-sm text-gray-500 dark:text-zinc-400">por página</span>
                </div>
                
                <div class="text-sm text-gray-500 dark:text-zinc-400">
                  Mostrando {{ movementsPaginationInfo.start }} a {{ movementsPaginationInfo.end }} de {{ movementsPaginationInfo.total }}
                </div>
              </div>
              
              <div class="flex items-center gap-1">
                <button @click="movementsFilters.currentPage--; loadMovementsData()" 
                        :disabled="movementsFilters.currentPage === 1"
                        class="p-2 text-gray-500 dark:text-zinc-400 bg-white dark:bg-zinc-900 rounded-full hover:bg-gray-200 dark:hover:bg-zinc-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                  </svg>
                </button>
                <span class="px-3 py-1 text-sm font-medium text-gray-900 dark:text-white">
                  {{ movementsFilters.currentPage }} / {{ movementsTotalPages }}
                </span>
                <button @click="movementsFilters.currentPage++; loadMovementsData()" 
                        :disabled="movementsFilters.currentPage >= movementsTotalPages"
                        class="p-2 text-gray-500 dark:text-zinc-400 bg-white dark:bg-zinc-900 rounded-full hover:bg-gray-200 dark:hover:bg-zinc-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
        <!-- Header y Filtros - Gemini -->
        <div class="bg-white dark:bg-zinc-900 rounded-md border border-gray-200 dark:border-zinc-800 px-5 py-4">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h2 class="text-lg font-medium text-gray-900 dark:text-white">Análisis por Cliente</h2>
                    <p class="text-sm text-gray-500 dark:text-zinc-400">Comportamiento y valor de clientes</p>
                </div>
                
                <div class="flex flex-wrap items-center gap-3">
                    <!-- Selector de Período -->
                    <select 
                      v-model="selectedPeriod"
                      @change="handlePeriodChange"
                      class="px-4 py-2.5 text-sm rounded-full bg-white dark:bg-zinc-800 text-gray-900 dark:text-white font-medium focus:outline-none focus:ring-2 focus:ring-[#1a73e8] dark:focus:ring-[#8ab4f8]"
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
                          class="px-4 py-2.5 text-sm rounded-full bg-white dark:bg-zinc-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-[#1a73e8] focus:outline-none min-w-[150px]"
                        >
                      </div>
                    </template>
        
                    <!-- Ordenar por -->
                    <select 
                      v-model="customersFilters.sortBy"
                      @change="loadCustomersData"
                      class="px-4 py-2.5 text-sm rounded-full bg-white dark:bg-zinc-800 text-gray-900 dark:text-white font-medium focus:outline-none focus:ring-2 focus:ring-[#1a73e8] dark:focus:ring-[#8ab4f8]"
                    >
                      <option value="total_spent">Valor Total</option>
                      <option value="total_purchases">Más Compras</option>
                      <option value="unique_products_bought">Más Productos</option>
                    </select>
        
                    <!-- Botón Exportar - Gemini Green -->
                    <button 
                      @click="exportCustomers"
                      class="px-5 py-2.5 text-sm font-medium text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-500/10 hover:bg-emerald-100 dark:hover:bg-emerald-600/30 rounded-full transition-all inline-flex items-center"
                    >
                      <i class="fas fa-file-excel mr-2"></i>
                      Exportar
                    </button>
                </div>
            </div>
        </div>

        <!-- 6 KPIs de Clientes - Metrics Ribbon -->
        <div class="bg-white dark:bg-zinc-900 rounded-md border border-gray-200 dark:border-zinc-800 grid grid-cols-2 lg:grid-cols-6 divide-x divide-gray-100 dark:divide-zinc-800" v-if="customersData">
          <div class="flex flex-col gap-1 px-5 py-4">
            <div class="flex items-center justify-between">
              <p class="text-xs text-gray-400 dark:text-zinc-500 uppercase tracking-wider font-medium">Clientes</p>
              <svg class="w-4 h-4 text-blue-500 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            </div>
            <p class="text-2xl font-black text-gray-900 dark:text-white tabular-nums">{{ customersData.summary.total_customers }}</p>
            <p class="text-xs text-gray-400 dark:text-zinc-500">registrados</p>
          </div>
          <div class="flex flex-col gap-1 px-5 py-4">
            <div class="flex items-center justify-between">
              <p class="text-xs text-gray-400 dark:text-zinc-500 uppercase tracking-wider font-medium">Ingresos</p>
              <svg class="w-4 h-4 text-emerald-500 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <p class="text-2xl font-black text-gray-900 dark:text-white tabular-nums">{{ formatCurrency(customersData.summary.total_revenue) }}</p>
            <p class="text-xs text-emerald-500 dark:text-emerald-400">total facturado</p>
          </div>
          <div class="flex flex-col gap-1 px-5 py-4">
            <div class="flex items-center justify-between">
              <p class="text-xs text-gray-400 dark:text-zinc-500 uppercase tracking-wider font-medium">Ganancia</p>
              <svg class="w-4 h-4 text-emerald-500 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"/></svg>
            </div>
            <p class="text-2xl font-black text-emerald-600 dark:text-emerald-400 tabular-nums">{{ formatCurrency(customersData.summary.total_profit) }}</p>
            <p class="text-xs text-gray-400 dark:text-zinc-500">neta del período</p>
          </div>
          <div class="flex flex-col gap-1 px-5 py-4">
            <div class="flex items-center justify-between">
              <p class="text-xs text-gray-400 dark:text-zinc-500 uppercase tracking-wider font-medium">Promedio</p>
              <svg class="w-4 h-4 text-violet-500 dark:text-violet-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/></svg>
            </div>
            <p class="text-2xl font-black text-gray-900 dark:text-white tabular-nums">{{ formatCurrency(customersData.summary.avg_customer_value) }}</p>
            <p class="text-xs text-gray-400 dark:text-zinc-500">por cliente</p>
          </div>
          <div class="flex flex-col gap-1 px-5 py-4">
            <div class="flex items-center justify-between">
              <p class="text-xs text-gray-400 dark:text-zinc-500 uppercase tracking-wider font-medium">Descuento</p>
              <svg class="w-4 h-4 text-amber-500 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z"/></svg>
            </div>
            <p class="text-2xl font-black text-gray-900 dark:text-white tabular-nums">{{ formatPercentage(customersData.summary.avg_discount) }}</p>
            <p class="text-xs text-amber-500 dark:text-amber-400">promedio</p>
          </div>
          <div class="flex flex-col gap-1 px-5 py-4">
            <div class="flex items-center justify-between">
              <p class="text-xs text-gray-400 dark:text-zinc-500 uppercase tracking-wider font-medium">Top Cliente</p>
              <svg class="w-4 h-4 text-violet-500 dark:text-violet-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/></svg>
            </div>
            <p class="text-lg font-black text-gray-900 dark:text-white truncate" v-if="customersData.summary.top_customer">{{ customersData.summary.top_customer.name }}</p>
            <p class="text-lg font-black text-gray-900 dark:text-white" v-else>N/A</p>
            <p class="text-xs text-gray-400 dark:text-zinc-500">mejor comprador</p>
          </div>
        </div>


        <!-- Tabla de Clientes - Gemini -->
        <div class="bg-white dark:bg-zinc-900 rounded-md border border-gray-200 dark:border-zinc-800 overflow-hidden">
          <div class="px-6 py-4 flex items-center justify-between">
            <div>
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Análisis de Clientes</h3>
                <p class="text-xs text-gray-500 dark:text-zinc-400 mt-1">Ranking y comportamiento de compra</p>
            </div>
            <div class="flex items-center gap-2">
                <span class="px-3 py-1.5 bg-blue-50 dark:bg-blue-500/10 text-blue-600 dark:text-blue-400 text-xs font-medium rounded-full">
                    {{ customersData?.customers?.length || 0 }} clientes
                </span>
            </div>
          </div>
          <div class="overflow-x-auto">
            <table class="min-w-full">
              <thead class="bg-gray-50 dark:bg-zinc-800">
                <tr>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-zinc-400 uppercase tracking-wider">Cliente</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-zinc-400 uppercase tracking-wider">Total Compras</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-zinc-400 uppercase tracking-wider">Total Gastado</th>
                  <!-- Columna Crédito: Solo si es Premium/Enterprise -->
                  <th v-if="isPremiumOrEnterprise" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-zinc-400 uppercase tracking-wider">Crédito</th>
                  <!-- Columna Puntos: Solo si es Premium/Enterprise -->
                  <th v-if="isPremiumOrEnterprise" class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-zinc-400 uppercase tracking-wider">Puntos</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-zinc-400 uppercase tracking-wider">Productos Únicos</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-zinc-400 uppercase tracking-wider">Items Totales</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-zinc-400 uppercase tracking-wider">Frecuencia</th>
                </tr>
              </thead>
              <tbody v-if="customersData && customersData.customers">
                <tr v-for="customer in customersData.customers" :key="customer.customer_id" class="hover:bg-gray-50 dark:hover:bg-zinc-800 border-b border-gray-200 dark:border-zinc-800 transition-colors">
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center">
                      <!-- AVATAR CON INICIALES - Gemini -->
                      <div class="w-10 h-10 rounded-full flex items-center justify-center bg-blue-50 dark:bg-blue-500/10">
                        <span class="text-sm font-medium text-blue-600 dark:text-blue-400">
                          {{ getInitials(customer.customer_name) }}
                        </span>
                      </div>
                      <div class="ml-3">
                        <div class="text-sm font-semibold text-gray-900 dark:text-white">{{ customer.customer_name }}</div>
                        <div class="text-xs text-gray-500 dark:text-zinc-400" v-if="customer.email">{{ customer.email }}</div>
                      </div>
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-50 dark:bg-blue-500/10 text-blue-600 dark:text-blue-400">
                      {{ customer.total_purchases }} compras
                    </span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-emerald-600 dark:text-emerald-400">
                    {{ formatCurrency(customer.total_spent) }}
                  </td>
                  
                  <!-- COLUMNA CRÉDITO: Solo si es Premium/Enterprise -->
                  <td v-if="isPremiumOrEnterprise" class="px-6 py-4 whitespace-nowrap">
                    <div class="space-y-1">
                      <!-- Límite de crédito -->
                      <div class="text-xs font-medium text-gray-500 dark:text-zinc-400">
                        Límite: <span class="text-gray-900 dark:text-white font-semibold">{{ formatCurrency(customer.credit_limit || 0) }}</span>
                      </div>
                      <!-- Deuda actual -->
                      <div class="text-xs font-medium text-gray-500 dark:text-zinc-400">
                        Debe: <span class="text-red-500 dark:text-red-400 font-semibold">{{ formatCurrency(customer.current_debt || 0) }}</span>
                      </div>
                    </div>
                  </td>
                  
                  <!-- COLUMNA PUNTOS: Solo si es Premium/Enterprise -->
                  <td v-if="isPremiumOrEnterprise" class="px-6 py-4 whitespace-nowrap text-center">
                    <div class="inline-flex items-center px-3 py-1.5 rounded-full bg-amber-50 dark:bg-amber-500/10">
                      <svg class="w-4 h-4 text-amber-500 dark:text-amber-400 mr-1.5" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                      </svg>
                      <span class="text-sm font-semibold text-amber-500 dark:text-amber-400">
                        {{ customer.loyalty_points || 0 }}
                      </span>
                    </div>
                  </td>
                  
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-zinc-400">
                    {{ customer.unique_products_bought }} productos
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-zinc-400">
                    {{ customer.total_items_bought }} items
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center">
                        <div class="text-sm font-medium text-gray-900 dark:text-white mr-2">
                          {{ Math.round((customer.total_items_bought / customer.total_purchases) * 10) / 10 }}
                        </div>
                        <span class="text-xs text-gray-500 dark:text-zinc-400">items/compra</span>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
          
          <!-- Controles de Paginación para Clientes - Gemini -->
          <div v-if="customersData?.customers && customersData.customers.length > 0" class="bg-gray-50 dark:bg-zinc-800 px-6 py-3 border-t border-gray-200 dark:border-zinc-800 rounded-b-2xl">
            <div class="flex items-center justify-between">
              <!-- Items por página y información -->
              <div class="flex items-center gap-4">
                <div class="flex items-center gap-2">
                  <label class="text-sm text-gray-500 dark:text-zinc-400">Mostrar:</label>
                  <select v-model="customersFilters.itemsPerPage" @change="customersFilters.currentPage = 1; loadCustomersData()" 
                          class="text-sm px-3 py-1.5 rounded-full bg-white dark:bg-zinc-900 text-gray-900 dark:text-white focus:ring-2 focus:ring-[#1a73e8] focus:outline-none">
                    <option value="10">10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                  </select>
                  <span class="text-sm text-gray-500 dark:text-zinc-400">por página</span>
                </div>
                
                <!-- Información de paginación -->
                <div class="text-sm text-gray-500 dark:text-zinc-400">
                  Mostrando {{ customersPaginationInfo.start }} a {{ customersPaginationInfo.end }} de {{ customersPaginationInfo.total }}
                </div>
              </div>
              
              <!-- Controles de paginación -->
              <div class="flex items-center gap-2">
                <!-- Botón Primera página -->
                <button @click="customersFilters.currentPage = 1; loadCustomersData()" 
                        :disabled="customersFilters.currentPage === 1"
                        class="p-2 text-gray-500 dark:text-zinc-400 bg-white dark:bg-zinc-900 rounded-full hover:bg-gray-200 dark:hover:bg-zinc-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"></path>
                  </svg>
                </button>
                
                <!-- Botón Anterior -->
                <button @click="customersFilters.currentPage--; loadCustomersData()" 
                        :disabled="customersFilters.currentPage === 1"
                        class="p-2 text-gray-500 dark:text-zinc-400 bg-white dark:bg-zinc-900 rounded-full hover:bg-gray-200 dark:hover:bg-zinc-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                  </svg>
                </button>
                
                <!-- Números de página -->
                <div class="flex items-center gap-1">
                  <template v-for="page in customersTotalPages" :key="page">
                    <button v-if="page === 1 || page === customersTotalPages || Math.abs(page - customersFilters.currentPage) <= 2"
                            @click="customersFilters.currentPage = page; loadCustomersData()"
                            :class="[
                              'px-3 py-1.5 text-sm font-medium rounded-full transition-colors',
                              page === customersFilters.currentPage 
                                ? 'bg-blue-600 dark:bg-[#8ab4f8] text-white dark:text-gray-900' 
                                : 'text-gray-500 dark:text-zinc-400 bg-white dark:bg-zinc-900 hover:bg-gray-200 dark:hover:bg-zinc-700'
                            ]">
                      {{ page }}
                    </button>
                    <span v-else-if="Math.abs(page - customersFilters.currentPage) === 3" class="px-2 text-zinc-400">...</span>
                  </template>
                </div>
                
                <!-- Botón Siguiente -->
                <button @click="customersFilters.currentPage++; loadCustomersData()" 
                        :disabled="customersFilters.currentPage >= customersTotalPages"
                        class="p-2 text-gray-500 dark:text-zinc-400 bg-white dark:bg-zinc-900 rounded-full hover:bg-gray-200 dark:hover:bg-zinc-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                  </svg>
                </button>
                
                <!-- Botón Última página -->
                <button @click="customersFilters.currentPage = customersTotalPages; loadCustomersData()" 
                        :disabled="customersFilters.currentPage === customersTotalPages"
                        class="p-2 text-gray-500 dark:text-zinc-400 bg-white dark:bg-zinc-900 rounded-full hover:bg-gray-200 dark:hover:bg-zinc-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7"></path>
                  </svg>
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Vista de Proveedores - Gemini Style -->
      <div v-if="activeSection === 'suppliers'" class="space-y-6 animate-fade-in">
        
        <!-- 4 KPIs Proveedores - Metrics Ribbon -->
        <div class="bg-white dark:bg-zinc-900 rounded-md border border-gray-200 dark:border-zinc-800 grid grid-cols-2 lg:grid-cols-4 divide-x divide-gray-100 dark:divide-zinc-800">
          <div class="flex flex-col gap-1 px-5 py-4">
            <div class="flex items-center justify-between">
              <p class="text-xs text-gray-400 dark:text-zinc-500 uppercase tracking-wider font-medium">Proveedores</p>
              <svg class="w-4 h-4 text-blue-500 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
            </div>
            <p class="text-2xl font-black text-gray-900 dark:text-white tabular-nums">{{ suppliersData?.summary?.total_suppliers || 0 }}</p>
            <p class="text-xs text-emerald-500 dark:text-emerald-400">{{ suppliersData?.summary?.active_suppliers || 0 }} activos</p>
          </div>
          <div class="flex flex-col gap-1 px-5 py-4">
            <div class="flex items-center justify-between">
              <p class="text-xs text-gray-400 dark:text-zinc-500 uppercase tracking-wider font-medium">Órdenes Pendientes</p>
              <svg class="w-4 h-4 text-amber-500 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
            </div>
            <p class="text-2xl font-black text-gray-900 dark:text-white tabular-nums">{{ suppliersData?.summary?.total_pending_orders || 0 }}</p>
            <p class="text-xs text-amber-500 dark:text-amber-400">por recibir</p>
          </div>
          <div class="flex flex-col gap-1 px-5 py-4">
            <div class="flex items-center justify-between">
              <p class="text-xs text-gray-400 dark:text-zinc-500 uppercase tracking-wider font-medium">Por Pagar</p>
              <svg class="w-4 h-4 text-red-500 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
            </div>
            <p class="text-2xl font-black text-gray-900 dark:text-white tabular-nums">{{ formatCurrency(suppliersData?.summary?.total_debt || 0) }}</p>
            <p class="text-xs text-red-500 dark:text-red-400">deuda total</p>
          </div>
          <div class="flex flex-col gap-1 px-5 py-4">
            <div class="flex items-center justify-between">
              <p class="text-xs text-gray-400 dark:text-zinc-500 uppercase tracking-wider font-medium">Top Proveedor</p>
              <svg class="w-4 h-4 text-emerald-500 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/></svg>
            </div>
            <p class="text-lg font-black text-gray-900 dark:text-white truncate">{{ suppliersData?.summary?.best_supplier?.name || 'N/A' }}</p>
            <p class="text-xs text-emerald-500 dark:text-emerald-400" v-if="suppliersData?.summary?.best_supplier?.total_purchases">{{ formatCurrency(suppliersData.summary.best_supplier.total_purchases) }} comprado</p>
            <p class="text-xs text-gray-400 dark:text-zinc-500" v-else>sin datos</p>
          </div>
        </div>

        <!-- Tabla de Proveedores - Gemini -->
        <div class="bg-white dark:bg-zinc-900 rounded-md border border-gray-200 dark:border-zinc-800 overflow-hidden">
          <div class="px-5 py-4 flex items-center justify-between">
            <div>
              <h2 class="text-base font-medium text-gray-900 dark:text-white">Lista de Proveedores</h2>
              <p class="text-xs text-gray-500 dark:text-zinc-400 mt-0.5">{{ suppliersData?.suppliers?.length || 0 }} proveedores registrados</p>
            </div>
            <button 
              @click="navigateToSuppliers()"
              class="px-4 py-2.5 text-sm font-medium text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-500/10 hover:bg-blue-100 dark:hover:bg-blue-600/30 rounded-full transition-colors"
            >
              Gestionar Proveedores
            </button>
          </div>

          <div v-if="!suppliersData" class="p-12 text-center">
            <div class="w-12 h-12 bg-gray-50 dark:bg-zinc-800 rounded-full flex items-center justify-center mx-auto mb-3 animate-pulse">
              <svg class="w-6 h-6 text-gray-500 dark:text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
              </svg>
            </div>
            <p class="text-sm font-medium text-gray-500 dark:text-zinc-400">Cargando proveedores...</p>
          </div>

          <div v-else-if="suppliersData?.suppliers?.length === 0" class="text-center py-12">
            <div class="w-16 h-16 bg-gray-50 dark:bg-zinc-800 rounded-full flex items-center justify-center mx-auto mb-4">
              <svg class="w-8 h-8 text-gray-500 dark:text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
              </svg>
            </div>
            <p class="text-sm font-medium text-gray-900 dark:text-white">No hay proveedores registrados</p>
            <p class="text-xs text-gray-500 dark:text-zinc-400 mt-1">Ve a Gestión de Proveedores para agregar uno</p>
          </div>

          <div v-else class="overflow-x-auto">
            <table class="min-w-full">
              <thead class="bg-white/50 dark:bg-zinc-800/50">
                <tr>
                  <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 dark:text-zinc-400 uppercase tracking-wide">Proveedor</th>
                  <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-zinc-400 uppercase tracking-wide">Productos</th>
                  <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-zinc-400 uppercase tracking-wide">Órdenes</th>
                  <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-zinc-400 uppercase tracking-wide">Última Orden</th>
                  <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-zinc-400 uppercase tracking-wide">Total Comprado</th>
                  <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-zinc-400 uppercase tracking-wide">Estado</th>
                  <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-zinc-400 uppercase tracking-wide">Acciones</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-[#e8eaed] dark:divide-[#3a3a3f]">
                <tr v-for="supplier in paginatedSuppliers" :key="supplier.id" class="hover:bg-white dark:hover:bg-zinc-800 transition-colors">
                  <td class="px-5 py-4">
                    <div class="flex items-center gap-3">
                      <div class="w-10 h-10 rounded-full flex items-center justify-center text-white font-medium text-sm"
                           :style="{ backgroundColor: getSupplierColor(supplier.name) }">
                        {{ getInitials(supplier.name) }}
                      </div>
                      <div>
                        <div class="text-sm font-medium text-gray-900 dark:text-white">{{ supplier.name }}</div>
                        <div class="text-xs text-gray-500 dark:text-zinc-400 mt-0.5">
                          {{ supplier.contact_person || supplier.phone || supplier.email || 'Sin contacto' }}
                        </div>
                      </div>
                    </div>
                  </td>
                  <td class="px-4 py-4 text-center">
                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-blue-50 dark:bg-blue-500/10 text-blue-600 dark:text-blue-400">
                      {{ supplier.products_count || 0 }}
                    </span>
                  </td>
                  <td class="px-4 py-4 text-center">
                    <div class="text-sm font-medium text-gray-900 dark:text-white">{{ supplier.total_orders || 0 }}</div>
                    <div v-if="supplier.pending_orders > 0" class="text-xs text-amber-500 dark:text-amber-400">
                      {{ supplier.pending_orders }} pendiente{{ supplier.pending_orders > 1 ? 's' : '' }}
                    </div>
                  </td>
                  <td class="px-4 py-4 text-center">
                    <div v-if="supplier.last_order_date">
                      <div class="text-sm text-gray-900 dark:text-white">{{ formatDate(supplier.last_order_date) }}</div>
                      <div class="text-xs text-gray-500 dark:text-zinc-400">#{{ supplier.last_order_number }}</div>
                    </div>
                    <div v-else class="text-xs text-zinc-400">Sin órdenes</div>
                  </td>
                  <td class="px-4 py-4 text-right">
                    <div class="text-sm font-medium text-gray-900 dark:text-white">{{ formatCurrency(supplier.total_purchased || 0) }}</div>
                    <div v-if="supplier.current_debt > 0" class="text-xs text-red-500 dark:text-red-400">
                      Debe: {{ formatCurrency(supplier.current_debt) }}
                    </div>
                  </td>
                  <td class="px-4 py-4 text-center">
                    <span :class="[
                      'px-2.5 py-1 text-xs font-medium rounded-full',
                      supplier.active 
                        ? 'bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400' 
                        : 'bg-gray-100 dark:bg-zinc-700 text-gray-500 dark:text-zinc-400'
                    ]">
                      {{ supplier.active ? 'Activo' : 'Inactivo' }}
                    </span>
                  </td>
                  <td class="px-4 py-4">
                    <div class="flex items-center justify-center gap-2">
                      <button 
                        @click="viewSupplierProducts(supplier)"
                        class="px-3 py-1.5 text-xs font-medium text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-500/10 hover:bg-blue-100 dark:hover:bg-blue-600/30 rounded-full transition-colors"
                        title="Ver productos de este proveedor"
                      >
                        Ver Productos
                      </button>
                      <button 
                        @click="createOrderForSupplier(supplier)"
                        class="px-3 py-1.5 text-xs font-medium text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-500/10 hover:bg-emerald-100 dark:hover:bg-emerald-600/30 rounded-full transition-colors"
                        title="Crear nueva orden de compra"
                      >
                        Nueva Orden
                      </button>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- Paginador -->
          <TablePaginator
            v-if="suppliersData?.suppliers && suppliersData.suppliers.length > 10"
            v-model:currentPage="suppliersFilters.currentPage"
            v-model:itemsPerPage="suppliersFilters.itemsPerPage"
            :totalPages="suppliersTotalPages"
            :totalItems="suppliersData.suppliers.length"
            label="proveedores"
            @update:itemsPerPage="savePaginatorPreference('suppliers', $event)"
          />
        </div>
      </div>

      <!-- Vista de Alertas -->
      <div v-if="activeSection === 'alerts'" class="space-y-6 animate-fade-in">
        
        <!-- Header con Filtros -->
        <!-- Header Alertas - Gemini -->
        <div class="bg-white dark:bg-zinc-900 rounded-md border border-gray-200 dark:border-zinc-800 px-5 py-4">
          <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h2 class="text-lg font-medium text-gray-900 dark:text-white">Centro de Alertas</h2>
                <p class="text-sm text-gray-500 dark:text-zinc-400">Monitoreo de salud del inventario</p>
            </div>
            
            <div class="flex items-center gap-3">
              <select 
                v-model="alertsFilters.severity"
                @change="loadAlertsData"
                class="px-4 py-2.5 text-sm rounded-full bg-white dark:bg-zinc-800 text-gray-900 dark:text-white font-medium focus:outline-none focus:ring-2 focus:ring-[#1a73e8] dark:focus:ring-[#8ab4f8]"
              >
                <option value="">Todas las alertas</option>
                <option value="critical">Críticas</option>
                <option value="warning">Advertencias</option>
                <option value="info">Información</option>
              </select>
              
              <button 
                @click="loadAlertsData"
                class="p-2.5 bg-white dark:bg-zinc-800 hover:bg-gray-100 dark:hover:bg-zinc-700 text-gray-500 dark:text-zinc-400 rounded-full transition-all"
                title="Actualizar"
              >
                <i class="fas fa-sync-alt"></i>
              </button>
            </div>
          </div>
        </div>

        <!-- 4 KPIs Alertas - Metrics Ribbon -->
        <div v-if="alertsData" class="bg-white dark:bg-zinc-900 rounded-md border border-gray-200 dark:border-zinc-800 grid grid-cols-2 lg:grid-cols-4 divide-x divide-gray-100 dark:divide-zinc-800">
          <div class="flex flex-col gap-1 px-5 py-4">
            <div class="flex items-center justify-between">
              <p class="text-xs text-gray-400 dark:text-zinc-500 uppercase tracking-wider font-medium">Críticas</p>
              <svg class="w-4 h-4 text-red-500 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            </div>
            <p class="text-2xl font-black text-red-500 dark:text-red-400 tabular-nums">{{ alertsData.summary.critical || 0 }}</p>
            <p class="text-xs text-red-400 dark:text-red-500">requieren acción</p>
          </div>
          <div class="flex flex-col gap-1 px-5 py-4">
            <div class="flex items-center justify-between">
              <p class="text-xs text-gray-400 dark:text-zinc-500 uppercase tracking-wider font-medium">Advertencias</p>
              <svg class="w-4 h-4 text-amber-500 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            </div>
            <p class="text-2xl font-black text-amber-500 dark:text-amber-400 tabular-nums">{{ alertsData.summary.warning || 0 }}</p>
            <p class="text-xs text-amber-400 dark:text-amber-500">preventivas</p>
          </div>
          <div class="flex flex-col gap-1 px-5 py-4">
            <div class="flex items-center justify-between">
              <p class="text-xs text-gray-400 dark:text-zinc-500 uppercase tracking-wider font-medium">Información</p>
              <svg class="w-4 h-4 text-blue-500 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <p class="text-2xl font-black text-gray-900 dark:text-white tabular-nums">{{ alertsData.summary.info || 0 }}</p>
            <p class="text-xs text-gray-400 dark:text-zinc-500">informativas</p>
          </div>
          <div class="flex flex-col gap-1 px-5 py-4">
            <div class="flex items-center justify-between">
              <p class="text-xs text-gray-400 dark:text-zinc-500 uppercase tracking-wider font-medium">Total Alertas</p>
              <svg class="w-4 h-4 text-violet-500 dark:text-violet-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
            </div>
            <p class="text-2xl font-black text-gray-900 dark:text-white tabular-nums">{{ alertsData.summary.total_alerts || 0 }}</p>
            <p class="text-xs text-gray-400 dark:text-zinc-500">en el sistema</p>
          </div>
        </div>

        <!-- Alertas del Sistema - Gemini -->
        <div v-if="alertsData && alertsData.alerts?.length > 0" class="bg-white dark:bg-zinc-900 rounded-md border border-gray-200 dark:border-zinc-800 overflow-hidden">
          <div class="px-6 py-3.5 flex items-center justify-between">
            <div>
              <h3 class="text-base font-semibold text-gray-900 dark:text-white">Centro de Alertas</h3>
              <p class="text-xs text-gray-500 dark:text-zinc-400 mt-0.5">{{ alertsData.alerts.length }} notificaciones agrupadas</p>
            </div>
            <div class="flex items-center gap-4">
              <button
                @click="markAllAlertsAsReviewed"
                class="text-xs font-medium text-gray-500 dark:text-zinc-400 hover:text-gray-900 dark:hover:text-white transition-colors duration-150"
              >
                Marcar todas leídas
              </button>
            </div>
          </div>
          
          <!-- Lista de Alertas Agrupadas - Gemini -->
          <div class="divide-y divide-[#e8eaed] dark:divide-[#3a3a3f]">
            <div v-for="group in groupedAlerts" :key="group.category + group.severity" 
                 class="transition-colors duration-150">
              
              <!-- Header del Grupo (Acordeón Limpio) -->
              <div class="px-6 py-3.5 flex items-center justify-between hover:bg-gray-50 dark:hover:bg-zinc-800 transition-colors duration-150">
                <div 
                  @click="toggleAlertGroup(group.category, group.severity)"
                  class="flex items-center gap-3 flex-1 cursor-pointer"
                >
                  <!-- Icono SVG Lineal 16px (sin fondo) -->
                  <svg :class="[
                    'w-4 h-4 flex-shrink-0',
                    group.severity === 'critical' ? 'text-red-500 dark:text-red-400' :
                    group.severity === 'warning' ? 'text-amber-500 dark:text-amber-400' :
                    'text-blue-600 dark:text-blue-400'
                  ]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path v-if="group.severity === 'critical'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    <path v-else-if="group.severity === 'warning'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                  </svg>
                  
                  <!-- Información del grupo -->
                  <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2">
                      <h4 class="text-sm font-medium text-gray-900 dark:text-white">
                        {{ getCategoryTitle(group.category) }}
                      </h4>
                      <span class="text-xs text-gray-500 dark:text-zinc-400">
                        ({{ group.alerts.length }})
                      </span>
                    </div>
                    <p class="text-xs text-gray-500 dark:text-zinc-400 mt-0.5">
                      {{ group.alerts.length > 1 
                          ? `${group.alerts.length} ${getCategoryDescription(group.category)}` 
                          : group.alerts[0].message.substring(0, 60) + '...' }}
                    </p>
                  </div>
                  
                  <!-- Chevron gris sutil -->
                  <svg :class="[
                    'w-3.5 h-3.5 text-zinc-400 dark:text-gray-500 transition-transform duration-200 flex-shrink-0',
                    expandedGroups.includes(group.category + group.severity) ? 'rotate-90' : ''
                  ]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                  </svg>
                </div>
                
                <!-- Acciones de texto sutiles -->
                <div class="ml-4 flex items-center gap-3 flex-shrink-0">
                  <button
                    @click.stop="resolveAlertGroup(group)"
                    class="text-xs font-medium text-gray-500 dark:text-zinc-400 hover:text-gray-900 dark:hover:text-white transition-colors duration-150"
                  >
                    Marcar leídas
                  </button>
                  <button
                    @click.stop="toggleAlertGroup(group.category, group.severity)"
                    class="text-xs font-medium text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 transition-colors duration-150"
                  >
                    Ver detalles
                  </button>
                </div>
              </div>
              
              <!-- Detalles del Grupo (Expandible) -->
              <transition name="expand">
                <div v-if="expandedGroups.includes(group.category + group.severity)" 
                     class="bg-gray-50 dark:bg-zinc-800">
                  <div class="pl-12 pr-6 py-3 space-y-2">
                    <!-- Cada alerta individual -->
                    <div v-for="alert in group.alerts" :key="alert.id"
                         class="flex items-start justify-between py-2 border-b border-gray-200 dark:border-zinc-800 last:border-0 hover:bg-white/50 dark:hover:bg-gray-900/50 px-3 -mx-3 rounded transition-colors duration-150">
                      
                      <!-- Contenido de la alerta -->
                      <div class="flex-1 min-w-0 pr-4">
                        <h5 class="text-sm font-medium text-gray-900 dark:text-white mb-0.5">{{ alert.title }}</h5>
                        <p class="text-xs text-gray-500 dark:text-zinc-400 leading-relaxed mb-1.5">{{ alert.message }}</p>
                        
                        <!-- Metadata inline -->
                        <div class="flex flex-wrap items-center gap-x-3 gap-y-1 text-xs text-gray-500 dark:text-zinc-400">
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
                          <span v-if="alert.discount_value" class="flex items-center font-medium text-red-600">
                            {{ alert.discount_value }}
                          </span>
                        </div>
                      </div>
                      
                      <!-- Acciones de texto -->
                      <div class="flex items-center gap-3 flex-shrink-0">
                        <button
                          v-if="alert.action_url || alert.invoice_id"
                          @click="viewAlertDetails(alert)"
                          class="text-xs font-medium text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 transition-colors duration-150"
                        >
                          Ver detalles
                        </button>
                        
                        <button
                          @click="markAlertAsReviewed(alert)"
                          class="text-xs font-medium text-gray-500 dark:text-zinc-400 hover:text-gray-900 dark:hover:text-white transition-colors duration-150"
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
          
          <!-- Paginación (si hay muchos grupos) - Gemini -->
          <div v-if="groupedAlerts.length > 10" class="bg-white dark:bg-zinc-800 px-6 py-3">
            <div class="flex items-center justify-between">
              <div class="text-sm text-gray-500 dark:text-zinc-400">
                {{ groupedAlerts.length }} grupos de alertas
              </div>
              <button
                @click="loadAlertsData"
                class="px-4 py-1.5 bg-white dark:bg-zinc-700 hover:bg-gray-100 dark:hover:bg-zinc-600 text-gray-900 dark:text-white text-xs font-medium rounded-full transition-all duration-200"
              >
                <i class="fas fa-sync-alt mr-1.5"></i>
                Actualizar
              </button>
            </div>
          </div>
        </div>

        <!-- Sin alertas - Gemini -->
        <div v-else-if="alertsData && alertsData.alerts?.length === 0" 
             class="bg-white dark:bg-zinc-900 rounded-md border border-gray-200 dark:border-zinc-800 p-12 text-center">
          <div class="w-16 h-16 bg-emerald-600 dark:bg-[#81c995] rounded-full flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8 text-white dark:text-gray-900" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
          </div>
          <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">¡Todo en orden!</h3>
          <p class="text-gray-500 dark:text-zinc-400">No hay alertas activas en este momento.</p>
        </div>
      </div>

      <!-- Vista de Predicciones - Gemini -->
      <div v-if="activeSection === 'predictions'" class="space-y-4">
        
        <!-- Filtros Ejecutivos de Predicciones - Gemini -->
        <div class="bg-white dark:bg-zinc-900 rounded-md border border-gray-200 dark:border-zinc-800 px-5 py-4">
          <div class="flex flex-wrap items-center gap-4 justify-between">
            <div class="flex items-center gap-4">
              <div class="flex items-center gap-2">
                <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
                <span class="text-xs font-medium text-gray-500 dark:text-zinc-400">Horizonte de Pronóstico:</span>
              </div>
              <select 
                v-model="predictionsFilters.forecastDays"
                @change="loadPredictionsData"
                class="px-4 py-2.5 text-sm rounded-full bg-white dark:bg-zinc-800 text-gray-900 dark:text-white font-medium focus:outline-none focus:ring-2 focus:ring-[#1a73e8] dark:focus:ring-[#8ab4f8]"
              >
                <option :value="7">7 días</option>
                <option :value="14">14 días</option>
                <option :value="30">30 días</option>
                <option :value="60">60 días</option>
                <option :value="90">90 días</option>
              </select>
              
              <!-- Toggle para productos saludables - Gemini -->
              <div class="flex items-center gap-2 px-4 py-2.5 bg-white dark:bg-zinc-800 rounded-full">
                <input 
                  type="checkbox" 
                  id="showHealthy"
                  v-model="predictionsFilters.showHealthy"
                  class="w-4 h-4 text-blue-600 rounded focus:ring-2 focus:ring-[#1a73e8]"
                >
                <label for="showHealthy" class="text-xs font-medium text-gray-500 dark:text-zinc-400 cursor-pointer">
                  <svg class="w-3 h-3 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                  </svg>
                  Mostrar Stock Seguro
                </label>
              </div>
            </div>
            
            <button 
              @click="loadPredictionsData"
              class="px-5 py-2.5 bg-gray-900 dark:bg-zinc-200 hover:bg-black dark:hover:bg-white text-white dark:text-gray-900 text-sm font-medium rounded-full transition-all duration-200 flex items-center gap-2"
            >
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
              </svg>
              <span>Actualizar</span>
            </button>
          </div>
        </div>

        <!-- Análisis de Tendencias - Gemini -->
        <div v-if="predictionsData" class="bg-white dark:bg-zinc-900 rounded-md border border-gray-200 dark:border-zinc-800 overflow-hidden">
          <div class="px-6 py-4">
            <div class="flex items-center justify-between">
              <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-blue-50 dark:bg-blue-500/10 rounded-full flex items-center justify-center">
                  <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"></path>
                  </svg>
                </div>
                <div>
                  <h3 class="text-lg font-medium text-gray-900 dark:text-white">Análisis de Tendencias IA</h3>
                  <p class="text-xs text-gray-500 dark:text-zinc-400 mt-0.5">Predicciones basadas en Machine Learning</p>
                </div>
              </div>
              <span class="px-3 py-1.5 bg-violet-50 dark:bg-violet-500/10 text-violet-600 dark:text-violet-400 text-xs font-medium rounded-full">
                <svg class="w-3 h-3 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                </svg>
                IA
              </span>
            </div>
          </div>
          
          <!-- Tarjetas de Tendencias - Grid 3 Columnas - Gemini -->
          <div class="grid grid-cols-1 md:grid-cols-3 gap-4 p-6">
            
            <!-- Tendencia Ventas -->
            <div class="bg-white dark:bg-zinc-800 rounded-md p-5 hover:bg-gray-100 dark:hover:bg-zinc-700 transition-all duration-200">
              <div class="flex items-center justify-between mb-3">
                <div class="flex items-center gap-3">
                  <div class="w-10 h-10 bg-emerald-50 dark:bg-emerald-500/10 rounded-full flex items-center justify-center">
                    <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                  </div>
                  <h3 class="text-sm font-medium text-gray-900 dark:text-white">Ventas</h3>
                </div>
                <div class="p-2 bg-gray-100 dark:bg-zinc-900 rounded-full">
                  <svg v-if="predictionsData.trend_analysis.sales.trend === 'positive'" class="w-4 h-4 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                  </svg>
                  <svg v-else class="w-4 h-4 text-red-500 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0v-8m0 8l-8-8-4 4-6-6"></path>
                  </svg>
                </div>
              </div>
              <div class="space-y-2 text-xs">
                <div class="flex justify-between items-center">
                  <span class="text-gray-500 dark:text-zinc-400 font-medium">Actual:</span>
                  <span class="font-semibold text-gray-900 dark:text-white">{{ formatCurrency(predictionsData.trend_analysis.sales.current) }}</span>
                </div>
                <div class="flex justify-between items-center">
                  <span class="text-gray-500 dark:text-zinc-400">Anterior:</span>
                  <span class="font-medium text-gray-500 dark:text-zinc-400">{{ formatCurrency(predictionsData.trend_analysis.sales.previous) }}</span>
                </div>
                <div class="pt-2 mt-2 border-t border-gray-200 dark:border-zinc-800 flex justify-between items-center">
                  <span class="text-gray-900 dark:text-white font-medium">Variación:</span>
                  <span :class="[
                    'text-sm font-medium px-2.5 py-1 rounded-full',
                    predictionsData.trend_analysis.sales.growth_percentage >= 0 
                      ? 'bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400' 
                      : 'bg-red-50 dark:bg-red-500/10 text-red-500 dark:text-red-400'
                  ]">
                    {{ predictionsData.trend_analysis.sales.growth_percentage >= 0 ? '+' : '' }}{{ predictionsData.trend_analysis.sales.growth_percentage }}%
                  </span>
                </div>
              </div>
            </div>

          <!-- Tendencia Transacciones -->
          <div class="bg-white dark:bg-zinc-800 rounded-md p-5 hover:bg-gray-100 dark:hover:bg-zinc-700 transition-all duration-200">
            <div class="flex items-center justify-between mb-3">
              <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-blue-50 dark:bg-blue-500/10 rounded-full flex items-center justify-center">
                  <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                  </svg>
                </div>
                <h3 class="text-sm font-medium text-gray-900 dark:text-white">Transacciones</h3>
              </div>
              <div class="p-2 bg-gray-100 dark:bg-zinc-900 rounded-full">
                <svg v-if="predictionsData.trend_analysis.transactions.trend === 'positive'" class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                </svg>
                <svg v-else class="w-4 h-4 text-red-500 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0v-8m0 8l-8-8-4 4-6-6"></path>
                </svg>
              </div>
            </div>
            <div class="space-y-2 text-xs">
              <div class="flex justify-between items-center">
                <span class="text-gray-500 dark:text-zinc-400 font-medium">Actual:</span>
                <span class="font-semibold text-gray-900 dark:text-white">{{ formatNumber(predictionsData.trend_analysis.transactions.current) }}</span>
              </div>
              <div class="flex justify-between items-center">
                <span class="text-gray-500 dark:text-zinc-400">Anterior:</span>
                <span class="font-medium text-gray-500 dark:text-zinc-400">{{ formatNumber(predictionsData.trend_analysis.transactions.previous) }}</span>
              </div>
              <div class="pt-2 mt-2 border-t border-gray-200 dark:border-zinc-800 flex justify-between items-center">
                <span class="text-gray-900 dark:text-white font-medium">Variación:</span>
                <span :class="[
                  'text-sm font-medium px-2.5 py-1 rounded-full',
                  predictionsData.trend_analysis.transactions.growth_percentage >= 0 
                    ? 'bg-blue-50 dark:bg-blue-500/10 text-blue-600 dark:text-blue-400' 
                    : 'bg-red-50 dark:bg-red-500/10 text-red-500 dark:text-red-400'
                ]">
                  {{ predictionsData.trend_analysis.transactions.growth_percentage >= 0 ? '+' : '' }}{{ predictionsData.trend_analysis.transactions.growth_percentage }}%
                </span>
              </div>
            </div>
          </div>

          <!-- Tendencia Ticket Promedio - Gemini -->
          <div class="bg-white dark:bg-zinc-800 rounded-md p-5 hover:bg-gray-100 dark:hover:bg-zinc-700 transition-all duration-200">
            <div class="flex items-center justify-between mb-3">
              <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-violet-50 dark:bg-violet-500/10 rounded-full flex items-center justify-center">
                  <svg class="w-5 h-5 text-violet-600 dark:text-violet-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"></path>
                  </svg>
                </div>
                <h3 class="text-sm font-medium text-gray-900 dark:text-white">Ticket Promedio</h3>
              </div>
              <div class="p-2 bg-gray-100 dark:bg-zinc-900 rounded-full">
                <svg v-if="predictionsData.trend_analysis.average_ticket.trend === 'positive'" class="w-4 h-4 text-violet-600 dark:text-violet-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                </svg>
                <svg v-else class="w-4 h-4 text-red-500 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0v-8m0 8l-8-8-4 4-6-6"></path>
                </svg>
              </div>
            </div>
            <div class="space-y-2 text-xs">
              <div class="flex justify-between items-center">
                <span class="text-gray-500 dark:text-zinc-400 font-medium">Actual:</span>
                <span class="font-semibold text-gray-900 dark:text-white">{{ formatCurrency(predictionsData.trend_analysis.average_ticket.current) }}</span>
              </div>
              <div class="flex justify-between items-center">
                <span class="text-gray-500 dark:text-zinc-400">Anterior:</span>
                <span class="font-medium text-gray-500 dark:text-zinc-400">{{ formatCurrency(predictionsData.trend_analysis.average_ticket.previous) }}</span>
              </div>
              <div class="pt-2 mt-2 border-t border-gray-200 dark:border-zinc-800 flex justify-between items-center">
                <span class="text-gray-900 dark:text-white font-medium">Variación:</span>
                <span :class="[
                  'text-sm font-medium px-2.5 py-1 rounded-full',
                  predictionsData.trend_analysis.average_ticket.growth_percentage >= 0 
                    ? 'bg-violet-50 dark:bg-violet-500/10 text-violet-600 dark:text-violet-400' 
                    : 'bg-red-50 dark:bg-red-500/10 text-red-500 dark:text-red-400'
                ]">
                  {{ predictionsData.trend_analysis.average_ticket.growth_percentage >= 0 ? '+' : '' }}{{ predictionsData.trend_analysis.average_ticket.growth_percentage }}%
                </span>
              </div>
            </div>
          </div>
          </div>
        </div>

        <!-- Predicción de Agotamiento de Stock con IA - Gemini -->
        <div v-if="predictionsData && predictionsData.stock_depletion?.length > 0" 
             class="bg-white dark:bg-zinc-900 rounded-md border border-gray-200 dark:border-zinc-800 overflow-hidden">
          <div class="px-6 py-4">
            <div class="flex items-center justify-between">
              <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-gray-50 dark:bg-zinc-800 rounded-full flex items-center justify-center">
                  <svg class="w-6 h-6 text-gray-500 dark:text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                  </svg>
                </div>
                <div>
                  <h3 class="text-lg font-medium text-gray-900 dark:text-white">Análisis de Inventario</h3>
                  <p class="text-xs text-gray-500 dark:text-zinc-400 mt-0.5">Proyección de agotamiento de stock</p>
                </div>
              </div>
              <div class="flex items-center gap-3">
                <div class="flex items-center gap-2 text-xs text-gray-500 dark:text-zinc-400">
                  <span class="font-mono font-semibold text-red-500 dark:text-red-400">{{ criticalProductsCount }}</span>
                  <span>Críticos</span>
                  <span class="mx-2 text-gray-200 dark:text-zinc-700">•</span>
                  <span class="font-mono font-semibold text-amber-500 dark:text-amber-400">{{ warningProductsCount }}</span>
                  <span>Atención</span>
                  <span v-if="predictionsFilters.showHealthy" class="mx-2 text-gray-200 dark:text-zinc-700">•</span>
                  <span v-if="predictionsFilters.showHealthy" class="font-mono font-semibold text-emerald-600 dark:text-emerald-400">{{ healthyProductsCount }}</span>
                  <span v-if="predictionsFilters.showHealthy">Saludables</span>
                </div>
              </div>
            </div>
          </div>
          <div class="overflow-x-auto">
            <table class="min-w-full">
              <thead class="bg-gray-50 dark:bg-zinc-800">
                <tr>
                  <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-zinc-400 uppercase tracking-wider">Producto</th>
                  <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-zinc-400 uppercase tracking-wider">Stock Actual</th>
                  <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-zinc-400 uppercase tracking-wider">Consumo Diario</th>
                  <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-zinc-400 uppercase tracking-wider">Agotamiento</th>
                  <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-zinc-400 uppercase tracking-wider">Acción</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="item in paginatedStockDepletion" :key="item.product_id"
                    class="border-b border-gray-200 dark:border-zinc-800 hover:bg-gray-50 dark:hover:bg-zinc-800 transition-colors duration-150">
                  <td class="px-4 py-4">
                    <div>
                      <div class="text-sm font-medium text-gray-900 dark:text-white mb-1.5">{{ item.product_name }}</div>
                      <!-- Barra de urgencia fina (4px) debajo del nombre -->
                      <div class="w-full h-1 bg-[#e8eaed] dark:bg-zinc-700 rounded-full overflow-hidden">
                        <div 
                          :class="[
                            'h-full transition-all duration-500',
                            item.days_until_depletion < 7 ? 'bg-red-500' :
                            item.days_until_depletion < 30 ? 'bg-amber-500' :
                            'bg-emerald-600'
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
                    <div class="font-mono text-base font-semibold text-gray-900 dark:text-white">{{ item.current_stock }}</div>
                    <div class="text-xs text-gray-500 dark:text-zinc-400 mt-0.5">unidades</div>
                  </td>
                  <td class="px-4 py-4 text-center">
                    <div class="font-mono text-sm font-medium text-gray-900 dark:text-white">{{ item.daily_average_sales }}</div>
                    <div class="text-xs text-gray-500 dark:text-zinc-400 mt-0.5">un/día</div>
                  </td>
                  <td class="px-4 py-4 text-center">
                    <!-- Fecha relativa limpia -->
                    <div v-if="item.days_until_depletion >= 30">
                      <div class="text-sm font-medium text-emerald-600 dark:text-emerald-400">{{ getRelativeTimeText(item.days_until_depletion) }}</div>
                      <div class="text-xs text-gray-500 dark:text-zinc-400 mt-0.5">Stock amplio</div>
                    </div>
                    <div v-else>
                      <div :class="[
                        'text-sm font-semibold mb-0.5',
                        item.days_until_depletion < 7 ? 'text-red-500 dark:text-red-400' : 'text-amber-500 dark:text-amber-400'
                      ]">
                        {{ getRelativeTimeText(item.days_until_depletion) }}
                      </div>
                      <div class="text-xs text-gray-500 dark:text-zinc-400 mt-0.5">
                        {{ formatDate(item.estimated_depletion_date) }}
                      </div>
                    </div>
                  </td>
                  <td class="px-4 py-4 text-center">
                    <!-- Botón outline 'Reponer' solo para críticos/advertencias - Gemini -->
                    <button
                      v-if="item.days_until_depletion < 30"
                      @click="createPurchaseOrder(item)"
                      :class="[
                        'px-4 py-1.5 text-xs font-medium rounded-full transition-all duration-150',
                        item.days_until_depletion < 7 
                          ? 'bg-red-50 dark:bg-red-500/10 text-red-500 dark:text-red-400 hover:bg-red-100 dark:hover:bg-red-500/30' 
                          : 'bg-amber-50 dark:bg-amber-500/10 text-amber-500 dark:text-amber-400 hover:bg-amber-100 dark:hover:bg-amber-500/30'
                      ]"
                    >
                      Reponer
                    </button>
                    <!-- Sin acción para productos saludables -->
                    <span v-else class="text-xs text-zinc-400">—</span>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
          
          <!-- Paginación Stock Depletion - Gemini -->
          <div v-if="predictionsData.stock_depletion.length > 10" class="bg-gray-50 dark:bg-zinc-800 px-6 py-3 border-t border-gray-200 dark:border-zinc-800">
            <div class="flex items-center justify-between">
              <div class="text-sm text-gray-500 dark:text-zinc-400">
                Mostrando {{ ((predictionsCurrentPage - 1) * 10) + 1 }} - {{ Math.min(predictionsCurrentPage * 10, predictionsData.stock_depletion.length) }} de {{ predictionsData.stock_depletion.length }} productos
              </div>
              <div class="flex items-center gap-2">
                <button @click="predictionsCurrentPage--" 
                        :disabled="predictionsCurrentPage <= 1"
                        class="px-4 py-1.5 text-sm font-medium bg-white dark:bg-zinc-900 text-gray-500 dark:text-zinc-400 rounded-full hover:bg-gray-200 dark:hover:bg-zinc-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
                  Anterior
                </button>
                <span class="px-3 py-1 text-sm font-medium text-gray-900 dark:text-white">
                  {{ predictionsCurrentPage }} / {{ Math.ceil(predictionsData.stock_depletion.length / 10) }}
                </span>
                <button @click="predictionsCurrentPage++" 
                        :disabled="predictionsCurrentPage >= Math.ceil(predictionsData.stock_depletion.length / 10)"
                        class="px-4 py-1.5 text-sm font-medium bg-white dark:bg-zinc-900 text-gray-500 dark:text-zinc-400 rounded-full hover:bg-gray-200 dark:hover:bg-zinc-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
                  Siguiente
                </button>
              </div>
            </div>
          </div>
        </div>
        
        <!-- Mensaje "¡Todo bajo control!" cuando NO hay productos críticos ni de atención - Gemini -->
        <div v-else-if="predictionsData && criticalProductsCount === 0 && warningProductsCount === 0"
             class="bg-emerald-50 dark:bg-emerald-500/10 rounded-md p-12 text-center">
          <div class="flex flex-col items-center gap-4">
            <div class="w-20 h-20 bg-emerald-100 dark:bg-emerald-600/30 rounded-full flex items-center justify-center">
              <svg class="w-10 h-10 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
              </svg>
            </div>
            <div>
              <h3 class="text-2xl font-semibold text-emerald-600 dark:text-emerald-400 mb-2">¡Todo bajo control!</h3>
              <p class="text-emerald-600/80 dark:text-emerald-400/80 text-sm max-w-md mx-auto">
                No hay productos críticos ni en estado de atención. Tu inventario está en excelente estado.
              </p>
            </div>
            <div class="flex items-center gap-4 mt-4 text-sm">
              <div class="flex items-center gap-2 text-emerald-600 dark:text-emerald-400">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                </svg>
                <span class="font-medium">{{ healthyProductsCount }} productos saludables</span>
              </div>
              <button
                @click="predictionsFilters.showHealthy = true"
                class="px-5 py-2 bg-emerald-600 dark:bg-[#81c995] hover:bg-[#168936] dark:hover:bg-[#72b888] text-white dark:text-gray-900 text-xs font-medium rounded-full transition-all duration-200"
              >
                <svg class="w-3 h-3 inline mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                </svg>
                Ver Inventario Completo
              </button>
            </div>
          </div>
        </div>

        <!-- Recomendaciones de Compra con IA - Gemini -->
        <div v-if="predictionsData && predictionsData.purchase_recommendations?.length > 0" 
             class="bg-white dark:bg-zinc-900 rounded-md border border-gray-200 dark:border-zinc-800 overflow-hidden">
          <!-- Header urgente pero profesional - Gemini -->
          <div class="px-6 py-4 bg-red-50 dark:bg-red-500/10">
            <div class="flex items-center justify-between">
              <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-red-500 dark:bg-[#f28b82] rounded-full flex items-center justify-center">
                  <svg class="w-6 h-6 text-white dark:text-gray-900" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                  </svg>
                </div>
                <div>
                  <h3 class="text-base font-semibold text-red-500 dark:text-red-400">Recomendaciones de Compra</h3>
                  <p class="text-xs text-gray-500 dark:text-zinc-400 mt-0.5">Productos que requieren reabastecimiento</p>
                </div>
              </div>
              <div class="flex items-center gap-2">
                <span class="px-3 py-1 bg-red-500 dark:bg-[#f28b82] text-white dark:text-gray-900 text-xs font-semibold rounded-full">
                  {{ predictionsData.purchase_recommendations.length }}
                </span>
                <span class="text-xs text-red-500 dark:text-red-400 font-medium">urgentes</span>
              </div>
            </div>
          </div>
          
          <div class="overflow-x-auto">
            <table class="min-w-full">
              <thead class="bg-white dark:bg-zinc-800">
                <tr>
                  <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-zinc-400 uppercase tracking-wider">Producto</th>
                  <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-zinc-400 uppercase tracking-wider">Stock</th>
                  <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-zinc-400 uppercase tracking-wider">Comprar</th>
                  <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-zinc-400 uppercase tracking-wider">Inversión</th>
                  <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-zinc-400 uppercase tracking-wider">Prioridad</th>
                  <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-zinc-400 uppercase tracking-wider">Acción</th>
                </tr>
              </thead>
              <tbody class="bg-white dark:bg-zinc-900">
                <tr v-for="item in paginatedPurchaseRecommendations" :key="item.product_id"
                    class="border-b border-gray-200 dark:border-zinc-800 hover:bg-red-50/30 dark:hover:bg-red-500/10 transition-colors duration-150">
                  <td class="px-4 py-4">
                    <div>
                      <div class="text-sm font-medium text-gray-900 dark:text-white">{{ item.product_name }}</div>
                      <div class="text-xs text-gray-500 dark:text-zinc-400 mt-1">
                        <span class="font-mono">{{ item.daily_demand }}</span> un/día
                      </div>
                    </div>
                  </td>
                  <td class="px-4 py-4 text-center">
                    <div class="font-mono text-base font-semibold" :class="[
                      item.current_stock <= 10 ? 'text-red-500 dark:text-red-400' : 'text-gray-900 dark:text-white'
                    ]">{{ item.current_stock }}</div>
                    <div class="text-xs text-gray-500 dark:text-zinc-400 mt-0.5">unidades</div>
                  </td>
                  <td class="px-4 py-4 text-center">
                    <div class="font-mono text-lg font-semibold text-red-500 dark:text-red-400">{{ item.recommended_purchase }}</div>
                    <div class="text-xs text-gray-500 dark:text-zinc-400 mt-0.5">unidades</div>
                  </td>
                  <td class="px-4 py-4 text-center">
                    <div class="font-mono text-base font-semibold text-gray-900 dark:text-white">{{ formatCurrency(item.estimated_cost) }}</div>
                    <div class="text-xs text-gray-500 dark:text-zinc-400 mt-0.5">estimado</div>
                  </td>
                  <td class="px-4 py-4 text-center">
                    <span class="text-xs font-medium" :class="[
                      item.priority === 'critical' ? 'text-red-500 dark:text-red-400' :
                      item.priority === 'high' ? 'text-amber-500 dark:text-amber-400' :
                      'text-gray-500 dark:text-zinc-400'
                    ]">
                      {{ item.priority === 'critical' ? 'Urgente' : 
                         item.priority === 'high' ? 'Alta' : 
                         item.priority === 'medium' ? 'Media' : 'Baja' }}
                    </span>
                  </td>
                  <td class="px-4 py-4 text-center">
                    <button
                      @click="createPurchaseOrder(item)"
                      class="px-4 py-1.5 bg-red-500 hover:bg-[#c5221f] dark:bg-[#f28b82] dark:hover:bg-[#ee675c] text-white dark:text-gray-900 text-xs font-medium rounded-full transition-all duration-150"
                    >
                      Reponer
                    </button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
          
          <!-- Paginación Recomendaciones - Gemini -->
          <div v-if="predictionsData.purchase_recommendations.length > 10" class="bg-white dark:bg-zinc-800 px-6 py-3">
            <div class="flex items-center justify-between">
              <div class="text-sm text-gray-500 dark:text-zinc-400">
                Mostrando {{ ((predictionsCurrentPage - 1) * 10) + 1 }} - {{ Math.min(predictionsCurrentPage * 10, predictionsData.purchase_recommendations.length) }} de {{ predictionsData.purchase_recommendations.length }} recomendaciones
              </div>
              <div class="flex items-center gap-2">
                <button @click="predictionsCurrentPage--" 
                        :disabled="predictionsCurrentPage <= 1"
                        class="px-4 py-1.5 text-sm bg-white dark:bg-zinc-700 text-gray-900 dark:text-white rounded-full hover:bg-gray-100 dark:hover:bg-zinc-600 disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
                  Anterior
                </button>
                <span class="px-3 py-1 text-sm font-medium text-gray-900 dark:text-white">
                  {{ predictionsCurrentPage }} / {{ Math.ceil(predictionsData.purchase_recommendations.length / 10) }}
                </span>
                <button @click="predictionsCurrentPage++" 
                        :disabled="predictionsCurrentPage >= Math.ceil(predictionsData.purchase_recommendations.length / 10)"
                        class="px-4 py-1.5 text-sm bg-white dark:bg-zinc-700 text-gray-900 dark:text-white rounded-full hover:bg-gray-100 dark:hover:bg-zinc-600 disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
                  Siguiente
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Pronóstico de Ventas con Machine Learning - Gemini -->
        <div v-if="predictionsData && predictionsData.sales_forecast?.length > 0" 
             class="bg-white dark:bg-zinc-900 rounded-md border border-gray-200 dark:border-zinc-800 overflow-hidden">
          <div class="px-6 py-4 bg-emerald-50 dark:bg-emerald-500/10">
            <div class="flex items-center justify-between">
              <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-emerald-600 dark:bg-[#81c995] rounded-full flex items-center justify-center">
                  <svg class="w-5 h-5 text-white dark:text-gray-900" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"></path>
                  </svg>
                </div>
                <div>
                  <h3 class="text-base font-semibold text-gray-900 dark:text-white">Pronóstico de Ventas con Machine Learning</h3>
                  <p class="text-xs text-gray-500 dark:text-zinc-400 mt-0.5">Proyección para los próximos {{ predictionsFilters.forecastDays }} días</p>
                </div>
              </div>
              <span class="px-3 py-1 bg-emerald-600 dark:bg-[#81c995] text-white dark:text-gray-900 text-xs font-medium rounded-full">
                {{ predictionsFilters.forecastDays }} días
              </span>
            </div>
          </div>
          <div class="overflow-x-auto">
            <table class="min-w-full">
              <thead class="bg-white dark:bg-zinc-800">
                <tr>
                  <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-zinc-400 uppercase tracking-wide">Producto</th>
                  <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-zinc-400 uppercase tracking-wide">Ventas Históricas</th>
                  <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-zinc-400 uppercase tracking-wide">Pronóstico IA</th>
                  <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-zinc-400 uppercase tracking-wide">Tendencia</th>
                  <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-zinc-400 uppercase tracking-wide">Confianza</th>
                </tr>
              </thead>
              <tbody class="bg-white dark:bg-zinc-900">
                <tr v-for="item in paginatedSalesForecast" :key="item.product_id"
                    class="border-b border-gray-200 dark:border-zinc-800 hover:bg-gray-50 dark:hover:bg-zinc-800 transition-colors">
                  <td class="px-4 py-3">
                    <div class="text-sm font-medium text-gray-900 dark:text-white">{{ item.product_name }}</div>
                    <div class="text-xs text-gray-500 dark:text-zinc-400 mt-0.5">
                      <svg class="w-3 h-3 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                      </svg>{{ item.transactions }} transacciones
                    </div>
                  </td>
                  <td class="px-4 py-3">
                    <span class="text-sm font-medium text-gray-900 dark:text-white">{{ item.historical_sales }}</span>
                    <span class="text-xs text-gray-500 dark:text-zinc-400 ml-1">unidades</span>
                  </td>
                  <td class="px-4 py-3">
                    <span class="inline-flex items-center px-3 py-1 bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 text-xs font-medium rounded-full">
                      <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                      </svg>{{ Math.abs(item.forecast_sales) }} unidades
                    </span>
                  </td>
                  <td class="px-4 py-3">
                    <span :class="[
                      'inline-flex items-center px-3 py-1 rounded-full text-xs font-medium',
                      item.trend === 'growing' ? 'bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400' :
                      item.trend === 'declining' ? 'bg-red-50 dark:bg-red-500/10 text-red-500 dark:text-red-400' :
                      'bg-gray-100 dark:bg-zinc-700 text-gray-500 dark:text-zinc-400'
                    ]">
                      <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path v-if="item.trend === 'growing'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                        <path v-else-if="item.trend === 'declining'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"></path>
                        <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14"></path>
                      </svg>
                      {{ item.trend === 'growing' ? 'Creciendo' : item.trend === 'declining' ? 'Decreciendo' : 'Estable' }}
                    </span>
                  </td>
                  <td class="px-4 py-3">
                    <span :class="[
                      'px-3 py-1 text-xs font-medium rounded-full',
                      item.confidence === 'high' ? 'bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400' :
                      'bg-amber-50 dark:bg-amber-500/10 text-amber-500 dark:text-amber-400'
                    ]">
                      {{ item.confidence === 'high' ? 'ALTA' : 'MEDIA' }}
                    </span>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
          
          <!-- Paginación Pronóstico - Gemini -->
          <div v-if="predictionsData.sales_forecast.length > 10" class="bg-white dark:bg-zinc-800 px-6 py-3">
            <div class="flex items-center justify-between">
              <div class="text-sm text-gray-500 dark:text-zinc-400">
                Mostrando {{ ((predictionsCurrentPage - 1) * 10) + 1 }} - {{ Math.min(predictionsCurrentPage * 10, predictionsData.sales_forecast.length) }} de {{ predictionsData.sales_forecast.length }} pronósticos
              </div>
              <div class="flex items-center gap-2">
                <button @click="predictionsCurrentPage--" 
                        :disabled="predictionsCurrentPage <= 1"
                        class="px-4 py-1.5 text-sm bg-white dark:bg-zinc-700 text-gray-900 dark:text-white rounded-full hover:bg-gray-100 dark:hover:bg-zinc-600 disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
                  Anterior
                </button>
                <span class="px-3 py-1 text-sm font-medium text-gray-900 dark:text-white">
                  {{ predictionsCurrentPage }} / {{ Math.ceil(predictionsData.sales_forecast.length / 10) }}
                </span>
                <button @click="predictionsCurrentPage++" 
                        :disabled="predictionsCurrentPage >= Math.ceil(predictionsData.sales_forecast.length / 10)"
                        class="px-4 py-1.5 text-sm bg-white dark:bg-zinc-700 text-gray-900 dark:text-white rounded-full hover:bg-gray-100 dark:hover:bg-zinc-600 disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
                  Siguiente
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Otras secciones (placeholder para las restantes) - Gemini -->
      <div v-if="!['overview', 'products', 'movements', 'customers', 'suppliers', 'alerts', 'predictions'].includes(activeSection)" class="bg-white dark:bg-zinc-900 rounded-md border border-gray-200 dark:border-zinc-800 p-6">
        <div class="text-center py-12 text-gray-500 dark:text-zinc-400">
          <i :class="getCurrentSectionIcon()" class="text-4xl mb-4 text-gray-200 dark:text-zinc-700"></i>
          <p class="text-lg font-medium text-gray-900 dark:text-white">{{ getCurrentSectionName() }}</p>
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
import { ref, reactive, onMounted, computed, watch } from 'vue'
import ToastNotifications from './ToastNotifications.vue'
import TablePaginator from './TablePaginator.vue'
import { API_CONFIG, apiCall } from '../services/api.js'
import { getInitials } from '../utils/avatarUtils.js'
import { appStore } from '../store/appStore'
import { useModuleNavigation } from '../composables/useModuleNavigation'
import { useUIContextStore } from '../store/uiContextStore.js'

export default {
  name: 'IntelligentInventoryView',
  components: {
    ToastNotifications,
    TablePaginator
  },
  setup() {
    const API_BASE_URL = API_CONFIG.BASE_URL
    
    // Composable para navegación entre módulos
    const { navigateToModule } = useModuleNavigation()

    // Estado reactivo
    const activeSection = ref('overview')
    const connectionStatus = ref('')
    const error = ref('')
    const overviewData = ref(null)
    const selectedPeriod = ref('month')
    const toastRef = ref(null)
    
    // Computed para verificar si el plan es Premium o Enterprise
    // appStore es un objeto reactivo importado directamente
    const isPremiumOrEnterprise = computed(() => {
      const plan = appStore.tenantPlan
      return plan === 'premium' || plan === 'enterprise'
    })
    
    // Rango de fechas personalizado
    const customDateRange = reactive({
      start: '',
      end: ''
    })
    
    // Datos para vista de productos
    const productsData = ref(null)
    const availableCategories = ref([])
    const availableSuppliers = ref([])
    const warehouses = ref([]) // Lista de bodegas/tiendas
    const selectedWarehouse = ref('') // Bodega seleccionada ('' = todas)
    const filters = reactive({
      category: '',
      supplier: '',
      search: '',
      currentPage: 1,
      itemsPerPage: 25
    })
    
    // Computed para mostrar/ocultar selector de bodega
    const showWarehouseSelector = computed(() => {
      // Solo mostrar si:
      // 1. El plan es premium o enterprise
      const isPremiumOrEnterprise = appStore.tenantPlan === 'premium' || appStore.tenantPlan === 'enterprise'
      // 2. Hay más de 1 bodega activa
      const hasMultipleWarehouses = warehouses.value.length > 1
      
      return isPremiumOrEnterprise && hasMultipleWarehouses
    })
    
    // Computed para paginación de productos
    const totalProductItems = computed(() => {
      return productsData.value?.products?.length || 0
    })
    
    const totalProductPages = computed(() => {
      const total = totalProductItems.value
      const perPage = filters.itemsPerPage || 25
      return Math.ceil(total / perPage) || 1
    })
    
    const paginatedProductsList = computed(() => {
      const products = productsData.value?.products || []
      const perPage = filters.itemsPerPage || 25
      const page = filters.currentPage || 1
      const start = (page - 1) * perPage
      const end = start + perPage
      return products.slice(start, end)
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
      averageProfitMargin: 0,
      totalExpenses: 0,
      netProfit: 0
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

    // Función para formatear unidades de medida
    const getMeasurementUnitLabel = (unit) => {
      const units = {
        'unit': 'unidades',
        'kg': 'kg',
        'g': 'gramos',
        'lb': 'libras',
        'oz': 'onzas',
        'l': 'litros',
        'ml': 'ml',
        'gal': 'galones',
        'm': 'metros',
        'cm': 'cm',
        'mm': 'mm',
        'in': 'pulgadas',
        'ft': 'pies',
        'box': 'cajas',
        'pack': 'paquetes',
        'dozen': 'docenas',
        'pair': 'pares',
        'unidad': 'unidades'
      }
      return units[unit] || unit || 'unidades'
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
      if (change > 0) return 'text-emerald-600 dark:text-emerald-400'
      if (change < 0) return 'text-red-500 dark:text-red-400'
      return 'text-gray-500 dark:text-zinc-400'
    }

    const getRotationTooltip = (rotationClass) => {
      const tooltips = {
        'A': '⭐ Alta rotación: Se agota en menos de 30 días. Producto de alta demanda.',
        'B': 'Rotación media: Se agota entre 31-90 días. Velocidad normal.',
        'C': 'Rotación lenta: Tardaría más de 90 días en agotarse. Revisar inventario.'
      }
      return tooltips[rotationClass] || tooltips['C']
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
        'purchase': 'bg-emerald-600',
        'adjustment': 'bg-blue-600',
        'return': 'bg-amber-500',
        'transfer': 'bg-violet-600'
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
        case 'suppliers':
          loadSuppliersData()
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
      // Navegación según tipo de alerta
      if (alert.invoice_id) {
        navigateToModule('invoices', { search: alert.invoice_id })
        return
      }
      if (alert.product_id) {
        activeSection.value = 'products'
        return
      }
      if (alert.category === 'stock') {
        activeSection.value = 'products'
        return
      }
      
      if (toastRef.value) {
        toastRef.value.show({
          title: 'Detalle de alerta',
          message: alert.title,
          type: 'info',
          autoClose: true,
          duration: 2000
        })
      }
    }
    
    // Función para marcar alerta como revisada
    const markAlertAsReviewed = async (alert) => {
      try {
        await apiCall('/inventory/test/alerts/dismiss', {
          method: 'POST',
          body: JSON.stringify({ alert_id: alert.id })
        })
        
        if (toastRef.value) {
          toastRef.value.show({
            title: 'Alerta marcada como revisada',
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
      const justification = prompt(`Justifica la acción para: ${alert.title}`)
      
      if (!justification) return
      
      try {
        await apiCall('/inventory/test/alerts/dismiss', {
          method: 'POST',
          body: JSON.stringify({ alert_id: alert.id, justification })
        })
        
        if (toastRef.value) {
          toastRef.value.show({
            title: 'Alerta justificada',
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
        const alertIds = (alertsData.value?.alerts || []).map(a => a.id)
        await apiCall('/inventory/test/alerts/dismiss', {
          method: 'POST',
          body: JSON.stringify({ alert_ids: alertIds, dismiss_all: true })
        })
        
        if (toastRef.value) {
          toastRef.value.show({
            title: 'Todas las alertas revisadas',
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
        const alertIds = group.alerts.map(a => a.id)
        await apiCall('/inventory/test/alerts/dismiss', {
          method: 'POST',
          body: JSON.stringify({
            alert_ids: alertIds,
            category: group.category,
            severity: group.severity
          })
        })
        
        if (toastRef.value) {
          toastRef.value.show({
            title: `Grupo Resuelto`,
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
      // Navegar según tipo de movimiento
      if (movement.movement_reason && movement.movement_reason.toLowerCase().includes('venta')) {
        navigateToModule('invoices', { search: movement.document_number })
        return
      }
      if (movement.movement_reason && movement.movement_reason.toLowerCase().includes('compra')) {
        navigateToModule('purchase-orders', { search: movement.document_number })
        return
      }
      
      if (toastRef.value) {
        toastRef.value.show({
          title: 'Documento',
          message: `${movement.document_number} - ${movement.movement_reason}`,
          type: 'info',
          autoClose: true,
          duration: 2500
        })
      }
    }

    // Renombrar testConnection a loadDashboardData para mayor claridad
    // Funciones para alertas automáticas
    const loadAndShowAlerts = async () => {
      // Solo mostrar alertas automáticas si estamos en la vista overview
      if (activeSection.value !== 'overview') {
        return
      }
      
      try {
        const data = await apiCall(`/inventory/test/alerts?period=${selectedPeriod.value}`)
        
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
          break
      }
    }

    const handleAlertAction = (alert) => {
      if (alert.action_url) {
        // Aquí podrías navegar a una página específica o abrir un modal
      }
    }

    const handleDismissForever = async (alert) => {
      try {
        const data = await apiCall('/inventory/test/alerts/dismiss', {
          method: 'POST',
          body: JSON.stringify({
            alert_key: alert.alert_key || alert.id,
            alert_type: alert.category,
            product_id: alert.product_id,
            user_id: 1 // Default user para pruebas
          })
        })
        
        if (data.success) {
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
        let endpoint = `/inventory/test/dashboard?period=${selectedPeriod.value}`
        
        // Si es rango personalizado, agregar fechas
        if (selectedPeriod.value === 'custom' && customDateRange.start) {
          endpoint += `&start_date=${customDateRange.start}`
          // Si no hay end_date, usar la misma fecha que start_date (mismo día)
          const endDate = customDateRange.end || customDateRange.start
          endpoint += `&end_date=${endDate}`
        }
        
        // Cargar datos del dashboard
        const inventoryData = await apiCall(endpoint)
        
        if (inventoryData) {
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
            
            // Cargar gastos del período
            await loadExpensesData()
            
            connectionStatus.value = 'Dashboard actualizado correctamente'
            
            // Las alertas automáticas ya no se cargan aquí
            // Solo se mostrarán en el dashboard principal del POS
          } else {
            console.warn('Respuesta sin success o data:', inventoryData)
          }
        } else {
          // const errorText = await inventoryResponse.text()
          // throw new Error(`Error ${inventoryResponse.status}: ${errorText}`)
          throw new Error('Error al cargar datos del dashboard')
        }
      } catch (err) {
        error.value = `Error cargando datos: ${err.message}`
        connectionStatus.value = 'Error al cargar'
      }
    }

    // Método para cargar gastos del período
    const loadExpensesData = async () => {
      try {
        // Construir parámetros según el período seleccionado
        let startDate, endDate
        const now = new Date()
        
        if (selectedPeriod.value === 'custom' && customDateRange.start) {
          startDate = customDateRange.start
          endDate = customDateRange.end || customDateRange.start
        } else if (selectedPeriod.value === 'week') {
          const weekAgo = new Date(now)
          weekAgo.setDate(weekAgo.getDate() - 7)
          startDate = weekAgo.toISOString().split('T')[0]
          endDate = now.toISOString().split('T')[0]
        } else if (selectedPeriod.value === 'month') {
          const monthAgo = new Date(now.getFullYear(), now.getMonth(), 1)
          startDate = monthAgo.toISOString().split('T')[0]
          endDate = now.toISOString().split('T')[0]
        } else if (selectedPeriod.value === 'year') {
          const yearStart = new Date(now.getFullYear(), 0, 1)
          startDate = yearStart.toISOString().split('T')[0]
          endDate = now.toISOString().split('T')[0]
        }
        
        // Llamar al endpoint de gastos con filtros de fecha
        const params = new URLSearchParams()
        if (startDate) params.append('start_date', startDate)
        if (endDate) params.append('end_date', endDate)
        
        const expensesData = await apiCall(`/expenses?${params.toString()}`)
        
        if (expensesData && expensesData.success && expensesData.data) {
          // Calcular total de gastos
          const expenses = expensesData.data.data || expensesData.data
          const totalExpenses = expenses.reduce((sum, expense) => sum + parseFloat(expense.amount || 0), 0)
          
          metrics.totalExpenses = totalExpenses
          metrics.netProfit = (metrics.monthlySales || 0) - totalExpenses
        }
      } catch (err) {
        console.error('Error cargando gastos:', err)
        // No mostrar error al usuario, solo usar 0 como default
        metrics.totalExpenses = 0
        metrics.netProfit = metrics.monthlySales || 0
      }
    }

    // Método para refrescar solo los datos de overview
    const refreshOverviewData = async () => {
      await loadDashboardData()
    }

    // Cargar bodegas activas
    const loadWarehouses = async () => {
      try {
        const data = await apiCall('/warehouses/active', { silent: true })
        
        if (data && data.success) {
          warehouses.value = data.data || []
        }
      } catch (error) {
        // Silencioso: si falla, simplemente no muestra el selector
        warehouses.value = []
      }
    }

    // Cambiar items por página
    const changeItemsPerPage = (event) => {
      const newValue = parseInt(event.target.value, 10)
      filters.itemsPerPage = newValue
      filters.currentPage = 1
      loadProductsData()
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
        
        // Agregar bodega si está seleccionada
        if (selectedWarehouse.value) {
          params.append('warehouse_id', selectedWarehouse.value)
        }
        
        // Si es rango personalizado, agregar fechas
        if (selectedPeriod.value === 'custom' && customDateRange.start) {
          params.append('start_date', customDateRange.start)
          const endDate = customDateRange.end || customDateRange.start
          params.append('end_date', endDate)
        }
        
        const data = await apiCall(`/inventory/test/products?${params}`)
        
        if (data) {
          
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
        const params = new URLSearchParams()
        
        params.append('period', selectedPeriod.value)
        if (movementsFilters.type) params.append('type', movementsFilters.type)
        if (movementsFilters.user) params.append('user', movementsFilters.user)
        if (movementsFilters.product) params.append('product', movementsFilters.product)
        
        // Si es rango personalizado, agregar fechas
        if (selectedPeriod.value === 'custom' && customDateRange.start) {
          params.append('start_date', customDateRange.start)
          const endDate = customDateRange.end || customDateRange.start
          params.append('end_date', endDate)
        }
        
        const endpoint = `/inventory/test/movements?${params}`
        const data = await apiCall(endpoint)
        
        if (data && data.success) {
          movementsData.value = data.data
        } else {
          movementsData.value = { movements: [], summary: {} }
        }
      } catch (error) {
        movementsData.value = { movements: [], summary: {} }
      }
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
        
        const data = await apiCall(`/inventory/test/customers?${params}`)
        
        if (data) {
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
        const response = await apiCall('/suppliers/analytics')
        
        if (response.success) {
          suppliersData.value = {
            suppliers: response.data.suppliers || [],
            summary: response.data.summary || {
              total_suppliers: 0,
              active_suppliers: 0,
              total_debt: 0,
              total_pending_orders: 0
            },
            best_supplier: response.data.summary?.best_supplier || null
          }
        }
      } catch (error) {
        console.error('Error cargando proveedores:', error)
        // Establecer datos vacíos en caso de error
        suppliersData.value = {
          suppliers: [],
          summary: {
            total_suppliers: 0,
            active_suppliers: 0,
            total_debt: 0,
            total_pending_orders: 0
          },
          best_supplier: null
        }
      }
    }

    // Generar color consistente para avatar de proveedor
    const getSupplierColor = (name) => {
      const colors = [
        '#3B82F6', '#8B5CF6', '#EC4899', '#EF4444', '#F97316', 
        '#EAB308', '#22C55E', '#14B8A6', '#06B6D4', '#6366F1'
      ]
      let hash = 0
      for (let i = 0; i < (name || '').length; i++) {
        hash = name.charCodeAt(i) + ((hash << 5) - hash)
      }
      return colors[Math.abs(hash) % colors.length]
    }

    // Navegar a Gestión de Proveedores (va al tab de proveedores en purchase-orders)
    const navigateToSuppliers = () => {
      navigateToModule('purchase-orders', { 
        activeTab: 'suppliers' 
      })
    }

    // Ver productos de un proveedor (abre en master detail)
    const viewSupplierProducts = (supplier) => {
      // Navegar al módulo de órdenes de compra con el proveedor seleccionado
      navigateToModule('purchase-orders', { 
        activeTab: 'suppliers',
        action: 'view', 
        supplierId: supplier.id,
        supplierName: supplier.name 
      })
    }

    // Crear orden de compra para un proveedor
    const createOrderForSupplier = (supplier) => {
      // Navegar al módulo de órdenes de compra con el proveedor preseleccionado
      navigateToModule('purchase-orders', { 
        activeTab: 'orders',
        action: 'create', 
        supplierId: supplier.id,
        supplierName: supplier.name 
      })
    }

    // Guardar preferencia de paginador en localStorage
    const savePaginatorPreference = (key, value) => {
      try {
        const prefs = JSON.parse(localStorage.getItem('pos_paginator_prefs') || '{}')
        prefs[key] = value
        localStorage.setItem('pos_paginator_prefs', JSON.stringify(prefs))
      } catch (e) {
        console.warn('Error guardando preferencia de paginador:', e)
      }
    }

    // Cargar preferencias de paginador desde localStorage
    const loadPaginatorPreferences = () => {
      try {
        const prefs = JSON.parse(localStorage.getItem('pos_paginator_prefs') || '{}')
        if (prefs.products) filters.itemsPerPage = prefs.products
        if (prefs.suppliers) suppliersFilters.itemsPerPage = prefs.suppliers
        if (prefs.movements) movementsFilters.itemsPerPage = prefs.movements
        if (prefs.customers) customersFilters.itemsPerPage = prefs.customers
      } catch (e) {
        console.warn('Error cargando preferencias de paginador:', e)
      }
    }

    // Cargar datos de alertas
    const loadAlertsData = async () => {
      try {
        const params = new URLSearchParams()
        
        params.append('period', selectedPeriod.value)
        if (alertsFilters.severity) params.append('severity', alertsFilters.severity)
        if (alertsFilters.category) params.append('category', alertsFilters.category)
        
        const data = await apiCall(`/inventory/test/alerts?${params}`)
        
        if (data) {
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
        
        const data = await apiCall(`/inventory/test/predictions?${params}`)
        
        if (data) {
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
          // Cargar bodegas primero, luego productos
          loadWarehouses().then(() => loadProductsData())
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

    // Métodos de utilidad para productos - Gemini colors
    const getStockStatusClass = (status) => {
      switch (status) {
        case 'out': return 'bg-red-50 dark:bg-red-500/10 text-red-500 dark:text-red-400'
        case 'low': return 'bg-amber-50 dark:bg-amber-500/10 text-amber-500 dark:text-amber-400'
        default: return 'bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400'
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
      if (days === 999) return 'bg-gray-100 dark:bg-zinc-700 text-gray-500 dark:text-zinc-400'
      if (days > 60) return 'bg-red-50 dark:bg-red-500/10 text-red-500 dark:text-red-400'
      if (days > 30) return 'bg-amber-50 dark:bg-amber-500/10 text-amber-500 dark:text-amber-400'
      return 'bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400'
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
    const createPurchaseOrder = async (item) => {
      const quantity = item.recommended_purchase || item.current_stock * 2
      const estimatedCost = item.estimated_cost || 0
      
      try {
        await apiCall('/purchase-orders', {
          method: 'POST',
          body: JSON.stringify({
            product_id: item.product_id,
            quantity: quantity,
            estimated_cost: estimatedCost,
            priority: item.priority || item.urgency,
            notes: `Orden generada desde predicción de agotamiento`
          })
        })
        
        if (toastRef.value) {
          toastRef.value.show({
            title: 'Orden de Compra Creada',
            message: `Producto: ${item.product_name}\nCantidad: ${quantity} unidades\nCosto estimado: ${formatCurrency(estimatedCost)}`,
            type: 'success',
            autoClose: true,
            duration: 5000,
            actions: [
              {
                label: 'Ver Ordenes',
                onClick: () => navigateToModule('purchase-orders')
              }
            ]
          })
        }
      } catch (error) {
        // Fallback: navegar al módulo de órdenes de compra
        navigateToModule('purchase-orders', {
          action: 'new',
          product_id: item.product_id,
          quantity: quantity
        })
      }
    }

    // CONCIENCIA DE PANTALLA PARA IA - Inventario Inteligente
    const updateScreenContextForAI = () => {
      const uiContext = useUIContextStore()
      
      // Obtener nombres de sección legibles
      const seccionesNombres = {
        'overview': 'Vista General',
        'products': 'Productos',
        'movements': 'Movimientos',
        'customers': 'Clientes',
        'suppliers': 'Proveedores',
        'alerts': 'Alertas',
        'predictions': 'Predicciones'
      }
      
      // Top productos vendidos
      const topProductos = (overviewData.value?.data?.topSellingProducts || []).slice(0, 5).map(p => ({
        nombre: p.name,
        unidadesVendidas: p.total_quantity_sold,
        ingresos: formatCurrency(p.total_revenue)
      }))
      
      // Productos con stock bajo
      const productosStockBajo = (overviewData.value?.data?.lowStockProductsList || []).slice(0, 5).map(p => ({
        nombre: p.name,
        stockActual: p.current_stock,
        stockMinimo: p.min_stock,
        categoria: p.category?.name || 'Sin categoría'
      }))
      
      // Movimientos recientes
      const movimientosRecientes = (overviewData.value?.data?.recentMovements || []).slice(0, 5).map(m => ({
        producto: m.product_name || m.product?.name,
        tipo: m.quantity > 0 ? 'entrada' : 'salida',
        cantidad: Math.abs(m.quantity),
        fuente: m.type
      }))
      
      // Productos de la tabla (si estamos en pestaña productos)
      const productosTabla = (paginatedProductsList.value || []).slice(0, 10).map(p => ({
        nombre: p.name,
        sku: p.sku,
        categoria: p.category?.name || 'Sin categoría',
        stock: p.current_stock,
        precio: formatCurrency(p.sale_price),
        costo: formatCurrency(p.cost_price),
        rotacion: p.rotation_class || 'N/A',
        margen: p.profit_margin ? `${p.profit_margin.toFixed(1)}%` : 'N/A'
      }))
      
      // Datos de movimientos para pestaña movimientos
      const movimientosDataLocal = movementsData.value || {}
      const movimientosSummary = movimientosDataLocal.summary || {}
      
      // Datos del contexto
      const contextData = {
        // Identificador del módulo y sección actual
        modulo: 'intelligent_inventory',
        seccionActiva: activeSection.value, // Nombre interno: overview, products, movements, etc.
        seccionNombre: seccionesNombres[activeSection.value] || activeSection.value, // Nombre legible
        periodo: selectedPeriod.value === 'day' ? 'Hoy' :
                 selectedPeriod.value === 'week' ? 'Esta Semana' :
                 selectedPeriod.value === 'month' ? 'Este Mes' :
                 selectedPeriod.value === 'year' ? 'Este Año' : 'Personalizado',
        
        // Métricas principales para que la IA siempre las tenga
        metrics: {
          activeProducts: metrics.activeProducts,
          totalProducts: metrics.totalProducts,
          totalInventoryValue: metrics.totalInventoryValue,
          totalSaleValue: metrics.totalSaleValue || 0,
          lowStockProducts: metrics.lowStockProducts,
          outOfStockProducts: metrics.outOfStockProducts
        },
        
        // KPIs principales (Vista General)
        kpis: {
          productosActivos: metrics.activeProducts,
          productosTotal: metrics.totalProducts,
          valorInvertido: formatCurrency(metrics.totalInventoryValue),
          valorPotencial: formatCurrency(metrics.totalSaleValue || 0),
          gananciaEstimada: formatCurrency((metrics.totalSaleValue || 0) - (metrics.totalInventoryValue || 0)),
          ventas: formatCurrency(metrics.monthlySales),
          transacciones: monthlyTransactions.value,
          stockBajo: metrics.lowStockProducts,
          sinStock: metrics.outOfStockProducts,
          ganancias: formatCurrency(metrics.monthlySales),
          gastos: formatCurrency(metrics.totalExpenses),
          gananciaNeta: formatCurrency(metrics.netProfit)
        },
        
        // Datos de movimientos (para pestaña Movimientos)
        resumenMovimientos: {
          totalMovimientos: movimientosSummary.total_movements || 0,
          entradas: movimientosSummary.total_entries || 0,
          salidas: movimientosSummary.total_exits || 0,
          valorEntradas: formatCurrency(movimientosSummary.total_entry_value || 0),
          valorSalidas: formatCurrency(movimientosSummary.total_exit_value || 0),
          balance: formatCurrency((movimientosSummary.total_entry_value || 0) - (movimientosSummary.total_exit_value || 0))
        },
        
        // Listas de datos según la sección (expuestas para que uiContextStore las lea)
        topProductos: topProductos,
        topProductosVendidos: topProductos, // Alias para que uiContextStore lo encuentre
        productosStockBajo: productosStockBajo,
        movimientosRecientes: movimientosRecientes,
        productosEnTabla: activeSection.value === 'products' ? productosTabla : [],
        
        // Filtros activos
        filtrosActivos: {
          busqueda: filters.search || null,
          categoria: filters.category || null,
          proveedor: filters.supplier || null
        },
        
        // Datos de Clientes (pestaña Clientes)
        clientes: activeSection.value === 'customers' && customersData.value ? {
          totalClientes: customersData.value.summary?.total_customers || 0,
          ingresos: formatCurrency(customersData.value.summary?.total_revenue || 0),
          ganancia: formatCurrency(customersData.value.summary?.total_profit || 0),
          promedioCliente: formatCurrency(customersData.value.summary?.avg_customer_value || 0),
          descuentoPromedio: `${(customersData.value.summary?.avg_discount || 0).toFixed(1)}%`,
          topCliente: customersData.value.summary?.top_customer?.name || 'N/A',
          listaClientes: (customersData.value.customers || []).slice(0, 10).map(c => ({
            nombre: c.name || c.customer_name || 'Sin nombre',
            compras: c.total_purchases || 0,
            gastado: formatCurrency(c.total_spent || 0),
            productosUnicos: c.unique_products || 0,
            items: c.total_items || 0,
            frecuencia: c.avg_items_per_purchase ? `${c.avg_items_per_purchase.toFixed(1)} items/compra` : 'N/A'
          }))
        } : null,
        
        // Datos de Proveedores (pestaña Proveedores)
        proveedores: activeSection.value === 'suppliers' && suppliersData.value ? {
          totalProveedores: suppliersData.value.summary?.total_suppliers || 0,
          activos: suppliersData.value.summary?.active_suppliers || 0,
          ordenesPendientes: suppliersData.value.summary?.total_pending_orders || 0,
          deudaTotal: formatCurrency(suppliersData.value.summary?.total_debt || 0),
          topProveedor: suppliersData.value.summary?.best_supplier?.name || 'N/A',
          listaProveedores: (suppliersData.value.suppliers || []).slice(0, 10).map(s => ({
            nombre: s.name,
            productos: s.products_count || 0,
            ordenes: s.orders_count || 0,
            totalComprado: formatCurrency(s.total_purchased || 0),
            estado: s.active !== false ? 'Activo' : 'Inactivo'
          }))
        } : null,
        
        // Datos de Alertas (pestaña Alertas)
        alertas: activeSection.value === 'alerts' && alertsData.value ? {
          criticas: alertsData.value.summary?.critical || 0,
          advertencias: alertsData.value.summary?.warning || 0,
          informativas: alertsData.value.summary?.info || 0,
          totalAlertas: alertsData.value.summary?.total_alerts || 0,
          listaAlertas: (alertsData.value.alerts || []).slice(0, 10).map(a => ({
            tipo: a.type || 'info',
            mensaje: a.message || a.title || 'Alerta',
            cantidad: a.count || 1,
            categoria: a.category || 'general'
          }))
        } : null,
        
        // Datos de Predicciones (pestaña Predicciones) - MUY IMPORTANTE PARA LA IA
        predicciones: activeSection.value === 'predictions' && predictionsData.value ? {
          // Tendencias actuales vs anteriores
          tendencias: {
            ventas: {
              actual: formatCurrency(predictionsData.value.trend_analysis?.sales?.current || 0),
              anterior: formatCurrency(predictionsData.value.trend_analysis?.sales?.previous || 0),
              variacion: `${predictionsData.value.trend_analysis?.sales?.growth_percentage || 0}%`,
              tendencia: predictionsData.value.trend_analysis?.sales?.trend || 'stable'
            },
            transacciones: {
              actual: predictionsData.value.trend_analysis?.transactions?.current || 0,
              anterior: predictionsData.value.trend_analysis?.transactions?.previous || 0,
              variacion: `${predictionsData.value.trend_analysis?.transactions?.growth_percentage || 0}%`
            },
            ticketPromedio: {
              actual: formatCurrency(predictionsData.value.trend_analysis?.average_ticket?.current || 0),
              anterior: formatCurrency(predictionsData.value.trend_analysis?.average_ticket?.previous || 0),
              variacion: `${predictionsData.value.trend_analysis?.average_ticket?.growth_percentage || 0}%`
            }
          },
          // Productos que se van a agotar pronto
          productosAgotamiento: (predictionsData.value.stock_depletion || []).slice(0, 10).map(p => ({
            nombre: p.product_name || p.name,
            stockActual: p.current_stock,
            consumoDiario: p.daily_consumption?.toFixed(2) || '0',
            diasAgotamiento: p.days_until_depletion,
            urgencia: p.days_until_depletion < 7 ? 'CRÍTICO' : p.days_until_depletion < 30 ? 'ATENCIÓN' : 'OK'
          })),
          // Pronóstico de ventas por producto (los más vendidos en el futuro)
          pronosticoVentas: (predictionsData.value.sales_forecast || []).slice(0, 10).map(p => ({
            nombre: p.product_name || p.name,
            ventasHistoricas: p.historical_sales || 0,
            pronosticoIA: p.predicted_sales || 0,
            tendencia: p.trend || 'Estable',
            confianza: p.confidence || 'MEDIA'
          })),
          // Resumen rápido para preguntas
          resumenRapido: {
            productoMasVendidoFuturo: (predictionsData.value.sales_forecast || [])[0]?.product_name || 'N/A',
            productoMenosVendidoFuturo: (predictionsData.value.sales_forecast || []).slice(-1)[0]?.product_name || 'N/A',
            productosAgotarsePronto: (predictionsData.value.stock_depletion || []).filter(p => p.days_until_depletion < 7).length,
            horizontePronostico: predictionsFilters.forecastDays + ' días'
          }
        } : null,
        
        // Instrucciones para la IA
        instrucciones: {
          secciones: `Estoy en ${seccionesNombres[activeSection.value]}. Puedo ver: Vista General, Productos, Movimientos, Clientes, Proveedores, Alertas, Predicciones`,
          vistaGeneral: 'En Vista General veo los KPIs principales, top productos vendidos, stock bajo y movimientos recientes',
          productos: 'En Productos veo la tabla detallada con stock, precio, rotación y rentabilidad de cada producto',
          movimientos: 'En Movimientos veo el historial de entradas y salidas con fechas, cantidades y fuentes',
          clientes: 'En Clientes veo ranking de clientes con sus compras, gastos, productos únicos y frecuencia',
          proveedores: 'En Proveedores veo lista de proveedores con sus productos, órdenes y total comprado',
          alertas: 'En Alertas veo KPIs de alertas (críticas, advertencias, informativas) y lista de notificaciones agrupadas por tipo',
          predicciones: 'En Predicciones veo tendencias de ventas, productos que se van a agotar, y pronóstico ML de qué productos se venderán más'
        }
      }
      
      // Registrar acciones disponibles
      uiContext.registerAction('cambiarSeccionInventarioInteligente', (params) => {
        const seccion = params?.seccion?.toLowerCase() || 'overview'
        const mapeo = {
          'general': 'overview', 'vista general': 'overview', 'overview': 'overview',
          'productos': 'products', 'products': 'products',
          'movimientos': 'movements', 'movements': 'movements',
          'clientes': 'customers', 'customers': 'customers',
          'proveedores': 'suppliers', 'suppliers': 'suppliers',
          'alertas': 'alerts', 'alerts': 'alerts',
          'predicciones': 'predictions', 'predictions': 'predictions'
        }
        const seccionId = mapeo[seccion] || 'overview'
        switchToSection(seccionId)
        return { 
          success: true, 
          message: `Cambiando a ${seccionesNombres[seccionId] || seccionId}`
        }
      })
      
      uiContext.registerAction('buscarProductoInventarioInteligente', (params) => {
        const texto = params?.texto || ''
        filters.search = texto
        if (activeSection.value !== 'products') {
          switchToSection('products')
        }
        return { 
          success: true, 
          message: `Buscando "${texto}" en productos`,
          resultados: totalProductItems.value
        }
      })
      
      uiContext.registerAction('cambiarPeriodoInventarioInteligente', (params) => {
        const periodo = params?.periodo?.toLowerCase() || 'month'
        const mapeo = {
          'hoy': 'day', 'day': 'day',
          'semana': 'week', 'week': 'week',
          'mes': 'month', 'month': 'month',
          'año': 'year', 'year': 'year'
        }
        selectedPeriod.value = mapeo[periodo] || 'month'
        handlePeriodChange()
        return { 
          success: true, 
          message: `Período cambiado a ${periodo}`
        }
      })
      
      uiContext.registerAction('verAlertasInventarioInteligente', () => {
        switchToSection('alerts')
        return { 
          success: true, 
          message: `Mostrando alertas. Hay ${metrics.lowStockProducts} productos con stock bajo y ${metrics.outOfStockProducts} sin stock.`
        }
      })
      
      uiContext.registerAction('verPrediccionesInventarioInteligente', () => {
        switchToSection('predictions')
        return { 
          success: true, 
          message: 'Mostrando predicciones de inventario'
        }
      })
      
      // Acción para buscar cliente en Inventario Inteligente
      uiContext.registerAction('buscarClienteInventarioInteligente', (params) => {
        const texto = params?.texto || ''
        if (activeSection.value !== 'customers') {
          switchToSection('customers')
        }
        // Aquí se podría agregar filtro de búsqueda si la vista lo soporta
        return { 
          success: true, 
          message: `Mostrando clientes. ${customersData.value?.summary?.total_customers || 0} clientes en total.`,
          datos: customersData.value?.summary || {}
        }
      })
      
      // Acción para buscar proveedor en Inventario Inteligente
      uiContext.registerAction('buscarProveedorInventarioInteligente', (params) => {
        const texto = params?.texto || ''
        if (activeSection.value !== 'suppliers') {
          switchToSection('suppliers')
        }
        // Aquí se podría agregar filtro de búsqueda si la vista lo soporta
        return { 
          success: true, 
          message: `Mostrando proveedores. ${suppliersData.value?.summary?.total_suppliers || 0} proveedores registrados.`,
          datos: suppliersData.value?.summary || {}
        }
      })
      
      // ACTUALIZAR DATOS GLOBALES DEL NEGOCIO
      // Estos datos estarán disponibles para la IA desde CUALQUIER módulo
      uiContext.updateGlobalBusinessSection('inventario', {
        productosActivos: metrics.activeProducts,
        productosTotal: metrics.totalProducts,
        valorInvertido: metrics.totalInventoryValue,
        valorPotencial: metrics.totalSaleValue || 0,
        gananciaEstimada: (metrics.totalSaleValue || 0) - (metrics.totalInventoryValue || 0),
        stockBajo: metrics.lowStockProducts,
        sinStock: metrics.outOfStockProducts
      })
      
      uiContext.updateGlobalBusinessSection('ganancias', {
        gananciaBrutaMes: metrics.monthlySales,
        gananciaNeta: metrics.netProfit,
        margenPromedio: Math.round(metrics.averageProfitMargin || 0)
      })
      
      uiContext.updateGlobalBusinessSection('gastos', {
        gastosMes: metrics.totalExpenses
      })
      
      uiContext.updateGlobalBusinessSection('alertas', {
        productosStockBajo: productosStockBajo.map(p => ({
          nombre: p.nombre,
          stock: p.stockActual
        })),
        productosSinStock: []
      })
      
      // Actualizar el store de contexto
      uiContext.setScreenData(contextData)
    }

    // Cargar datos automáticamente al montar el componente
    onMounted(() => {
      // Primero cargar preferencias de paginador
      loadPaginatorPreferences()
      
      loadDashboardData()
      loadProductsData()
      loadMovementsData()
      loadCustomersData()
      loadSuppliersData() // Cargar proveedores
      
      // Inicializar contexto para IA
      setTimeout(() => updateScreenContextForAI(), 1000)
    })

    // Watcher para actualizar contexto cuando cambian los datos
    watch([activeSection, overviewData, productsData, movementsData, customersData, suppliersData, alertsData, predictionsData], () => {
      updateScreenContextForAI()
    }, { deep: true })

    // Watchers para guardar preferencias de paginador automáticamente
    watch(() => filters.itemsPerPage, (newValue) => {
      savePaginatorPreference('products', newValue)
    })
    
    watch(() => suppliersFilters.itemsPerPage, (newValue) => {
      savePaginatorPreference('suppliers', newValue)
    })
    
    watch(() => movementsFilters.itemsPerPage, (newValue) => {
      savePaginatorPreference('movements', newValue)
    })
    
    watch(() => customersFilters.itemsPerPage, (newValue) => {
      savePaginatorPreference('customers', newValue)
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
          outOfStock: 0,
          lowStock: 0,
          totalValueSale: 0,
          totalValueCost: 0
        }
      }
      
      // Usar summary del backend que ya viene filtrado por bodega
      const summary = productsData.value.summary || {}
      
      return {
        total: summary.total_products || 0,
        active: summary.products_in_stock || 0, // Productos con stock > 0
        outOfStock: summary.out_of_stock || 0, // Productos sin stock
        lowStock: summary.low_stock || 0,
        totalValueSale: summary.total_value_sale || 0,
        totalValueCost: summary.total_value_cost || 0
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
      warehouses, // Lista de bodegas
      selectedWarehouse, // Bodega seleccionada
      showWarehouseSelector, // Mostrar selector (computed)
      filters,
      // Paginación de productos
      totalProductItems,
      totalProductPages,
      paginatedProductsList,
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
      isPremiumOrEnterprise, // Plan detection para features premium
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
      getMeasurementUnitLabel, // Formatear unidades de medida
      getRotationTooltip, // Tooltips para rotación ABC
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
      loadWarehouses, // Cargar bodegas
      loadProductsData,
      changeItemsPerPage,
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
      viewMovementDocument,
      
      // 🆕 Funciones de proveedores
      getSupplierColor,
      navigateToSuppliers,
      viewSupplierProducts,
      createOrderForSupplier,
      
      // Funciones de persistencia de paginador
      savePaginatorPreference,
      loadPaginatorPreferences
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