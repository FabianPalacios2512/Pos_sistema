<template>
  <div class="min-h-screen font-sans bg-gradient-to-b from-gray-50 via-gray-100 to-gray-200 dark:bg-gradient-to-b dark:from-[#141417] dark:via-slate-900 dark:to-[#0a0a0c] transition-colors duration-300 px-8">
    <div class="p-4 lg:p-6 space-y-6 pb-8 animate-fade-in">
      
      <!-- Header con Navegación Integrada -->
      <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 pb-2">
        <div>
          <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Inventario Inteligente</h1>
          <p class="text-sm text-gray-600 dark:text-zinc-400 mt-1">Sistema de análisis predictivo y gestión avanzada</p>
        </div>
        
        <div class="flex items-center gap-3">
          <!-- Navegación de Secciones - Estilo Tabs Profesional -->
          <nav class="flex items-center bg-white/60 dark:bg-zinc-800/40  rounded-xl p-1">
            <button
              v-for="section in sections"
              :key="section.id"
              @click="switchToSection(section.id)"
              :class="[
                'px-4 py-2 text-sm font-medium rounded-lg transition-all duration-200',
                activeSection === section.id
                  ? 'bg-white dark:bg-zinc-900 text-gray-900 dark:text-white shadow-sm'
                  : 'text-gray-500 dark:text-zinc-400 hover:text-gray-900 dark:hover:text-white hover:bg-white/50 dark:hover:bg-zinc-800/50'
              ]"
            >
              {{ section.name }}
            </button>
          </nav>

          <!-- Botón Actualizar -->
          <button 
            @click="refreshCurrentSection"
            class="px-5 py-2.5 bg-slate-900 dark:bg-slate-700 hover:bg-black dark:hover:bg-slate-600 text-white text-sm font-bold rounded-xl shadow-lg shadow-slate-400/40 dark:shadow-slate-900/50 transition-all duration-300 flex items-center gap-2"
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
        
        <!-- Solo mostrar errores -->
        <div v-if="error" class="bg-rose-50 dark:bg-rose-950/30 rounded-xl p-3 border border-rose-200 dark:border-rose-900">
          <div class="flex items-center gap-2 text-rose-600 dark:text-rose-400">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <span class="text-sm font-semibold">{{ error }}</span>
          </div>
        </div>
        
        <!-- Filtros de Período (Compactos) - Sin bordes, integrado -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 bg-white/60 dark:bg-zinc-900/40  rounded-xl px-4 py-3">
          <span class="text-sm font-medium text-gray-600 dark:text-zinc-400">Período:</span>
          <div class="flex items-center gap-2 flex-wrap">
            <select 
              v-model="selectedPeriod"
              @change="handlePeriodChange"
              class="bg-white dark:bg-zinc-800 border border-gray-200 dark:border-zinc-700 rounded-lg px-3 py-2 text-sm text-gray-900 dark:text-zinc-200 focus:ring-2 focus:ring-slate-500 dark:focus:ring-slate-400 focus:border-transparent min-w-[140px]"
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
                class="bg-white dark:bg-zinc-800 border border-gray-200 dark:border-zinc-700 rounded-lg px-3 py-2 text-sm text-gray-900 dark:text-zinc-200 focus:ring-2 focus:ring-slate-500 min-w-[150px]"
              >
            </template>
          </div>
        </div>

        <!-- Dashboard Principal - KPIs con Glassmorphism SIN BORDES -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
          <!-- PRODUCTOS ACTIVOS -->
          <div class="bg-white/80 dark:bg-zinc-900/80  rounded-xl px-4 py-4 shadow-lg shadow-gray-200/50 dark:shadow-black/30 hover:shadow-xl dark:hover:shadow-black/50 transition-all duration-200">
            <div class="flex items-center gap-3">
              <div class="w-12 h-12 bg-blue-50 dark:bg-zinc-800/50 rounded-xl flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                </svg>
              </div>
              <div class="flex-1 min-w-0">
                <p class="text-xs font-medium text-gray-500 dark:text-zinc-400 uppercase tracking-wide">Productos Activos</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white mt-0.5">{{ metrics.activeProducts || 0 }}</p>
                <p class="text-xs text-gray-600 dark:text-zinc-400 mt-0.5">de {{ metrics.totalProducts || 0 }} totales</p>
              </div>
            </div>
          </div>

          <!-- VALOR INVERTIDO -->
          <div class="bg-white/80 dark:bg-zinc-900/80  rounded-xl px-4 py-4 shadow-lg shadow-gray-200/50 dark:shadow-black/30 hover:shadow-xl dark:hover:shadow-black/50 transition-all duration-200">
            <div class="flex items-center gap-3">
              <div class="w-12 h-12 bg-emerald-50 dark:bg-zinc-800/50 rounded-xl flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
              </div>
              <div class="flex-1 min-w-0">
                <p class="text-xs font-medium text-gray-500 dark:text-zinc-400 uppercase tracking-wide">Valor Invertido</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white mt-0.5">{{ formatCurrency(metrics.totalInventoryValue) }}</p>
                <p class="text-xs text-gray-500 dark:text-zinc-500 mt-0.5">costo × stock</p>
              </div>
            </div>
          </div>

          <!-- VALOR POTENCIAL -->
          <div class="bg-white/80 dark:bg-zinc-900/80  rounded-xl px-4 py-4 shadow-lg shadow-gray-200/50 dark:shadow-black/30 hover:shadow-xl dark:hover:shadow-black/50 transition-all duration-200">
            <div class="flex items-center gap-3">
              <div class="w-12 h-12 bg-indigo-50 dark:bg-zinc-800/50 rounded-xl flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                </svg>
              </div>
              <div class="flex-1 min-w-0">
                <p class="text-xs font-medium text-gray-500 dark:text-zinc-400 uppercase tracking-wide">Valor Potencial</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white mt-0.5">{{ formatCurrency(metrics.totalSaleValue || 0) }}</p>
                <p class="text-xs text-gray-500 dark:text-zinc-500 mt-0.5">precio × stock</p>
              </div>
            </div>
          </div>

          <!-- GANANCIA ESTIMADA -->
          <div class="bg-white/80 dark:bg-zinc-900/80  rounded-xl px-4 py-4 shadow-lg shadow-gray-200/50 dark:shadow-black/30 hover:shadow-xl dark:hover:shadow-black/50 transition-all duration-200">
            <div class="flex items-center gap-3">
              <div class="w-12 h-12 bg-purple-50 dark:bg-zinc-800/50 rounded-xl flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
              </div>
              <div class="flex-1 min-w-0">
                <p class="text-xs font-medium text-gray-500 dark:text-zinc-400 uppercase tracking-wide">Ganancia Est.</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white mt-0.5">{{ formatCurrency((metrics.totalSaleValue || 0) - (metrics.totalInventoryValue || 0)) }}</p>
                <p class="text-xs text-gray-500 dark:text-zinc-500 mt-0.5">venta - costo</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Métricas Secundarias Compactas (Una línea) - Sin bordes -->
        <div class="bg-white/60 dark:bg-zinc-900/60  rounded-xl px-6 py-5">
          <div class="grid grid-cols-2 md:grid-cols-5 gap-6">
            <!-- Ventas -->
            <div>
              <p class="text-xs font-medium text-gray-500 dark:text-zinc-400 uppercase tracking-wide mb-1">Ventas</p>
              <p class="text-xl font-bold text-gray-900 dark:text-white">{{ formatCurrency(metrics.monthlySales || 0) }}</p>
              <p class="text-xs text-gray-500 dark:text-zinc-500">{{ monthlyTransactions || 0 }} transacciones</p>
            </div>

            <!-- Alertas Stock -->
            <div>
              <p class="text-xs font-medium text-gray-500 dark:text-zinc-400 uppercase tracking-wide mb-1">Alertas Stock</p>
              <div class="flex items-baseline gap-1">
                <span class="text-xl font-bold text-amber-600 dark:text-amber-400">{{ metrics.lowStockProducts || 0 }}</span>
                <span class="text-xs text-gray-400 dark:text-zinc-500">/</span>
                <span class="text-xl font-bold text-rose-600 dark:text-rose-400">{{ metrics.outOfStockProducts || 0 }}</span>
              </div>
              <p class="text-xs text-gray-500 dark:text-zinc-500">bajo / sin stock</p>
            </div>

            <!-- Ganancias -->
            <div>
              <p class="text-xs font-medium text-gray-500 dark:text-zinc-400 uppercase tracking-wide mb-1">Ganancias</p>
              <p class="text-xl font-bold text-emerald-600 dark:text-emerald-400">{{ formatCurrency(metrics.monthlySales || 0) }}</p>
              <p class="text-xs text-gray-500 dark:text-zinc-500">{{ getPeriodLabel() }}</p>
            </div>

            <!-- Gastos -->
            <div>
              <p class="text-xs font-medium text-gray-500 dark:text-zinc-400 uppercase tracking-wide mb-1">Gastos</p>
              <p class="text-xl font-bold text-gray-900 dark:text-white">{{ formatCurrency(metrics.totalExpenses || 0) }}</p>
              <p class="text-xs text-gray-500 dark:text-zinc-500">{{ getPeriodLabel() }}</p>
            </div>

            <!-- Ganancia Neta -->
            <div>
              <p class="text-xs font-medium text-gray-500 dark:text-zinc-400 uppercase tracking-wide mb-1">Ganancia Neta</p>
              <p class="text-xl font-bold text-emerald-600 dark:text-emerald-400">{{ formatCurrency(metrics.netProfit || 0) }}</p>
              <p class="text-xs text-gray-500 dark:text-zinc-500">ventas - gastos</p>
            </div>
          </div>
        </div>

        <!-- Sección de Productos y Movimientos - Cards sin bordes pesados -->
        <div v-if="overviewData" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
          <!-- Top Productos -->
          <div v-if="overviewData.data.topSellingProducts?.length > 0" class="bg-white/80 dark:bg-zinc-900/80  rounded-xl shadow-lg shadow-gray-200/50 dark:shadow-black/30 overflow-hidden">
            <div class="px-5 py-4">
              <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-amber-50 dark:bg-amber-950/30 rounded-xl flex items-center justify-center flex-shrink-0">
                  <svg class="w-5 h-5 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path>
                  </svg>
                </div>
                <div>
                  <h3 class="text-base font-bold text-gray-900 dark:text-white">Top Productos</h3>
                  <p class="text-xs text-gray-500 dark:text-zinc-500">Más vendidos del período</p>
                </div>
              </div>
            </div>
            <div class="px-5 pb-5 space-y-2">
              <div v-for="(product, index) in overviewData.data.topSellingProducts.slice(0, 5)" 
                   :key="product.id" 
                   class="flex items-center gap-3 p-3 bg-gray-50/50 dark:bg-zinc-800/30 hover:bg-gray-100/50 dark:hover:bg-zinc-800/50 rounded-xl transition-all duration-200">
                <!-- Ranking Badge -->
                <div class="w-8 h-8 bg-white dark:bg-zinc-800 rounded-lg flex items-center justify-center text-gray-700 dark:text-zinc-300 font-bold text-sm flex-shrink-0 shadow-sm">
                  {{ index + 1 }}
                </div>
                
                <!-- Imagen del producto -->
                <div class="w-10 h-10 rounded-lg overflow-hidden bg-gray-100 dark:bg-zinc-800 flex items-center justify-center flex-shrink-0">
                  <img v-if="product.image_url" :src="product.image_url" :alt="product.name" class="w-full h-full object-cover" />
                  <svg v-else class="w-5 h-5 text-gray-400 dark:text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                  </svg>
                </div>
                
                <!-- Info del producto -->
                <div class="flex-1 min-w-0">
                  <p class="font-semibold text-gray-900 dark:text-white text-sm truncate">{{ product.name }}</p>
                  <p class="text-xs text-gray-500 dark:text-zinc-500 mt-0.5">
                    {{ product.total_quantity_sold }} unidades vendidas
                  </p>
                </div>
                
                <!-- Revenue -->
                <div class="text-right flex-shrink-0">
                  <p class="font-bold text-emerald-600 dark:text-emerald-400 text-sm">{{ formatCurrency(product.total_revenue) }}</p>
                  <p class="text-xs text-gray-500 dark:text-zinc-500">ingresos</p>
                </div>
              </div>
            </div>
          </div>

          <!-- Stock Bajo -->
          <div v-if="overviewData.data.lowStockProductsList?.length > 0" class="bg-white/80 dark:bg-zinc-900/80  rounded-xl shadow-lg shadow-gray-200/50 dark:shadow-black/30 overflow-hidden">
            <div class="px-5 py-4">
              <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-rose-50 dark:bg-rose-950/30 rounded-xl flex items-center justify-center flex-shrink-0">
                  <svg class="w-5 h-5 text-rose-600 dark:text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                  </svg>
                </div>
                <div>
                  <h3 class="text-base font-bold text-gray-900 dark:text-white">Stock Bajo</h3>
                  <p class="text-xs text-gray-500 dark:text-zinc-500">Productos que necesitan reposición</p>
                </div>
              </div>
            </div>
            <div class="px-5 pb-5 space-y-2">
              <div v-for="product in overviewData.data.lowStockProductsList.slice(0, 5)" 
                   :key="product.id" 
                   class="flex items-center gap-3 p-3 bg-gray-50/50 dark:bg-zinc-800/30 hover:bg-gray-100/50 dark:hover:bg-zinc-800/50 rounded-xl transition-all duration-200">
                <div class="w-10 h-10 rounded-lg overflow-hidden bg-gray-100 dark:bg-zinc-800 flex items-center justify-center flex-shrink-0">
                  <img v-if="product.image_url" :src="product.image_url" :alt="product.name" class="w-full h-full object-cover" />
                  <svg v-else class="w-5 h-5 text-gray-400 dark:text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                  </svg>
                </div>
                <div class="flex-1 min-w-0">
                  <p class="font-semibold text-gray-900 dark:text-white text-sm truncate">{{ product.name }}</p>
                  <p class="text-xs text-gray-500 dark:text-zinc-500 mt-0.5">{{ product.category?.name || 'Sin categoría' }}</p>
                </div>
                <div class="text-right flex-shrink-0">
                  <div class="flex items-center gap-1.5">
                    <div class="w-1.5 h-1.5 rounded-full animate-pulse" :class="product.current_stock === 0 ? 'bg-rose-500' : 'bg-amber-500'"></div>
                    <p class="font-bold text-sm" :class="product.current_stock === 0 ? 'text-rose-600 dark:text-rose-400' : 'text-amber-600 dark:text-amber-400'">
                      {{ product.current_stock }}<span class="text-gray-400 dark:text-zinc-600">/</span>{{ product.min_stock }}
                    </p>
                  </div>
                  <p class="text-xs text-gray-500 dark:text-zinc-500">{{ product.current_stock === 0 ? 'Agotado' : 'Reponer' }}</p>
                </div>
              </div>
            </div>
          </div>

          <!-- Movimientos Recientes -->
          <div v-if="overviewData.data.recentMovements?.length > 0" class="bg-white/80 dark:bg-zinc-900/80  rounded-xl shadow-lg shadow-gray-200/50 dark:shadow-black/30 overflow-hidden">
              <div class="px-5 py-4">
                <div class="flex items-center gap-3 mb-4">
                  <div class="w-10 h-10 bg-blue-50 dark:bg-blue-950/50 rounded-xl flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"></path>
                    </svg>
                  </div>
                  <div>
                    <h3 class="text-base font-bold text-gray-900 dark:text-white">Movimientos</h3>
                    <p class="text-xs text-gray-500 dark:text-zinc-400">Actividad reciente</p>
                  </div>
                </div>
                <div class="space-y-2">
                  <div v-for="movement in overviewData.data.recentMovements.slice(0, 5)" 
                       :key="movement.id" 
                       class="flex items-center gap-3 p-3 bg-gray-50/50 dark:bg-zinc-800/30 hover:bg-gray-100/50 dark:hover:bg-zinc-800/50 transition-all duration-200 rounded-xl">
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0" :class="movement.quantity > 0 ? 'bg-emerald-100 dark:bg-emerald-950/50' : 'bg-rose-100 dark:bg-rose-950/50'">
                      <svg v-if="movement.quantity > 0" class="w-4 h-4 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 14l-7 7m0 0l-7-7m7 7V3" />
                      </svg>
                      <svg v-else class="w-4 h-4 text-rose-600 dark:text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 10l7-7m0 0l7 7m-7-7v18" />
                      </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                      <p class="font-semibold text-gray-900 dark:text-white text-sm truncate">{{ movement.product_name || movement.product?.name }}</p>
                      <p class="text-xs text-gray-600 dark:text-zinc-400 mt-0.5">{{ formatMovementType(movement.type) }}</p>
                    </div>
                    <div class="text-right flex-shrink-0">
                      <p class="font-bold text-sm" :class="movement.quantity > 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400'">
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
        
        <!-- Filtros Compactos -->
        <div class="bg-white/80 dark:bg-zinc-900/60  rounded-xl shadow-lg shadow-gray-200/50 dark:shadow-black/30 p-4">
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
                class="w-full pl-9 pr-3 py-3 text-sm border-2 border-gray-200 dark:border-zinc-700 rounded-xl bg-gray-50 dark:bg-zinc-800 text-gray-900 dark:text-zinc-200 placeholder-gray-400 dark:placeholder-zinc-500 focus:outline-none focus:ring-2 focus:ring-slate-500 dark:focus:ring-slate-400 focus:border-transparent"
              >
            </div>
            
            <!-- Selector de Período -->
            <select 
              v-model="selectedPeriod"
              @change="handlePeriodChange"
              class="px-3 py-3 text-sm rounded-xl border border-gray-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-gray-700 dark:text-zinc-300 font-medium focus:outline-none focus:ring-2 focus:ring-slate-500 dark:focus:ring-slate-400 min-w-36"
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
                  class="px-3 py-3 text-sm border-2 border-gray-200 dark:border-zinc-700 rounded-xl bg-white dark:bg-zinc-800 text-gray-900 dark:text-zinc-200 focus:ring-2 focus:ring-slate-500 dark:focus:ring-slate-400 focus:border-transparent min-w-[150px]"
                >
              </div>
            </template>

            <!-- Filtro por Categoría -->
            <select 
              v-model="filters.category"
              @change="loadProductsData"
              class="px-3 py-3 text-sm rounded-xl border border-gray-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-gray-700 dark:text-zinc-300 font-medium focus:outline-none focus:ring-2 focus:ring-slate-500 dark:focus:ring-slate-400 min-w-44"
            >
              <option value="">Todas las categorías</option>
              <option v-for="cat in availableCategories" :key="cat.id" :value="cat.id">
                {{ cat.name }}
              </option>
            </select>

            <!-- 🏪 Selector de Bodega/Tienda (solo para Premium/Enterprise con múltiples bodegas) -->
            <select 
              v-if="showWarehouseSelector"
              v-model="selectedWarehouse"
              @change="loadProductsData"
              class="px-3 py-3 text-sm rounded-xl border-2 border-indigo-200 dark:border-indigo-700 bg-indigo-50 dark:bg-indigo-950 text-indigo-700 dark:text-indigo-300 font-bold focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400 min-w-48"
            >
              <option value="">📍 Todas las Sedes</option>
              <option v-for="warehouse in warehouses" :key="warehouse.id" :value="warehouse.id">
                {{ warehouse.is_default ? '🏢 ' : '🏪 ' }}{{ warehouse.name }}
              </option>
            </select>

            <!-- Botón Limpiar Filtros -->
            <button 
              @click="clearFilters" 
              class="p-3 text-gray-500 dark:text-zinc-400 hover:text-red-600 dark:hover:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-xl border border-transparent hover:border-red-100 dark:hover:border-red-900/30 transition-all"
              title="Limpiar filtros">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
              </svg>
            </button>
          </div>
        </div>

        <!-- KPIs de Productos con Glassmorphism -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
          <!-- TOTAL PRODUCTOS -->
          <div class="bg-white/80 dark:bg-zinc-900/80  rounded-xl px-4 py-3 shadow-lg shadow-gray-200/50 dark:shadow-black/30 hover:shadow-xl transition-all duration-200">
            <div class="flex items-center space-x-4">
              <div class="bg-indigo-50 dark:bg-indigo-950/50 rounded-xl p-3">
                <svg class="w-6 h-6 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                </svg>
              </div>
              <div class="flex-1 min-w-0">
                <div class="flex items-center justify-between mb-2">
                  <h3 class="text-sm font-semibold text-gray-700 dark:text-zinc-400 uppercase tracking-wide">TOTAL PRODUCTOS</h3>
                  <span class="text-xs font-medium text-gray-500 dark:text-zinc-500 bg-gray-100 dark:bg-zinc-800 px-2 py-1 rounded-lg">
                    en el sistema
                  </span>
                </div>
                <p class="text-2xl font-bold text-gray-900 dark:text-white mb-1">{{ productsMetrics.total }}</p>
              </div>
            </div>
          </div>

          <!-- ACTIVOS -->
          <div class="bg-white/80 dark:bg-zinc-900/80  rounded-xl px-4 py-3 shadow-lg shadow-gray-200/50 dark:shadow-black/30 hover:shadow-xl transition-all duration-200">
            <div class="flex items-center space-x-4">
              <div class="bg-emerald-50 dark:bg-emerald-950/50 rounded-xl p-3">
                <svg class="w-6 h-6 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
              </div>
              <div class="flex-1 min-w-0">
                <div class="flex items-center justify-between mb-2">
                  <h3 class="text-sm font-semibold text-gray-700 dark:text-zinc-400 uppercase tracking-wide">ACTIVOS</h3>
                  <span class="text-xs font-medium text-emerald-700 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-950 px-2 py-1 rounded-full">
                    disponibles
                  </span>
                </div>
                <p class="text-2xl font-bold text-gray-900 dark:text-white mb-1">{{ productsMetrics.active }}</p>
              </div>
            </div>
          </div>

          <!-- SIN STOCK -->
          <div class="bg-white/80 dark:bg-zinc-900/80  rounded-xl px-4 py-3 shadow-lg shadow-gray-200/50 dark:shadow-black/30 hover:shadow-xl transition-all duration-200">
            <div class="flex items-center space-x-4">
              <div class="bg-gray-100 dark:bg-zinc-800/50 rounded-xl p-3">
                <svg class="w-6 h-6 text-gray-500 dark:text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                </svg>
              </div>
              <div class="flex-1 min-w-0">
                <div class="flex items-center justify-between mb-2">
                  <h3 class="text-sm font-semibold text-gray-700 dark:text-zinc-400 uppercase tracking-wide">SIN STOCK</h3>
                  <span class="text-xs font-medium text-gray-600 dark:text-zinc-500 bg-gray-100 dark:bg-zinc-800 px-2 py-1 rounded-full">
                    agotados
                  </span>
                </div>
                <p class="text-2xl font-bold text-gray-900 dark:text-white mb-1">{{ productsMetrics.outOfStock }}</p>
              </div>
            </div>
          </div>

          <!-- VALOR TOTAL -->
          <div class="bg-white/80 dark:bg-zinc-900/80  rounded-xl px-4 py-3 shadow-lg shadow-gray-200/50 dark:shadow-black/30 hover:shadow-xl transition-all duration-200">
            <div class="flex items-center space-x-4">
              <div class="bg-purple-50 dark:bg-purple-950/50 rounded-xl p-3">
                <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
              </div>
              <div class="flex-1 min-w-0">
                <div class="flex items-center justify-between mb-2">
                  <h3 class="text-sm font-semibold text-gray-700 dark:text-zinc-400 uppercase tracking-wide">VALOR TOTAL</h3>
                  <span class="text-xs font-medium text-gray-500 dark:text-zinc-500 bg-gray-100 dark:bg-zinc-800 px-2 py-1 rounded-lg">
                    Inventario
                  </span>
                </div>
                <p class="text-2xl font-bold text-gray-900 dark:text-white mb-1">{{ formatCurrency(productsMetrics.totalValueSale) }}</p>
                <p class="text-sm text-gray-500 dark:text-zinc-400">Costo: {{ formatCurrency(productsMetrics.totalValueCost) }}</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Tabla de Productos -->
        <div v-if="productsData && productsData.products && productsData.products.length > 0" class="bg-white/90 dark:bg-zinc-900/90  rounded-xl shadow-lg shadow-gray-200/50 dark:shadow-black/30 overflow-hidden">
          <div class="px-6 py-4 bg-transparent">
            <div class="flex items-center justify-between">
              <div>
                <h3 class="text-base font-bold text-gray-900 dark:text-white">Lista de Productos</h3>
                <p class="text-xs text-gray-500 dark:text-zinc-400 mt-1">{{ productsData.products.length }} productos encontrados</p>
              </div>
            </div>
          </div>
          
          <div class="overflow-x-auto">
            <table class="min-w-full">
              <thead class="bg-gray-50/80 dark:bg-zinc-800/50">
                <tr>
                  <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 dark:text-zinc-400 uppercase tracking-wide">Producto</th>
                  <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 dark:text-zinc-400 uppercase tracking-wide">Categoría</th>
                  <th class="px-4 py-3 text-center text-xs font-bold text-gray-600 dark:text-zinc-400 uppercase tracking-wide">Stock</th>
                  <th class="px-4 py-3 text-center text-xs font-bold text-gray-600 dark:text-zinc-400 uppercase tracking-wide">Precio</th>
                  <th class="px-4 py-3 text-center text-xs font-bold text-gray-600 dark:text-zinc-400 uppercase tracking-wide">Rotación</th>
                  <th class="px-4 py-3 text-center text-xs font-bold text-gray-600 dark:text-zinc-400 uppercase tracking-wide">Rentabilidad</th>
                </tr>
              </thead>
              <tbody class="bg-transparent">
                <tr v-for="product in productsData.products" :key="product.id"
                    class="hover:bg-gray-50/50 dark:hover:bg-zinc-800/30 transition-colors border-b border-gray-100 dark:border-zinc-800/30 last:border-b-0">
                  <td class="px-4 py-3">
                    <div class="text-sm font-semibold text-gray-900 dark:text-white">{{ product.name }}</div>
                    <div class="text-xs text-gray-500 dark:text-zinc-400 mt-0.5">SKU: {{ product.sku || 'N/A' }}</div>
                  </td>
                  <td class="px-4 py-3">
                    <span class="inline-flex items-center bg-indigo-50 dark:bg-indigo-500/10 text-indigo-700 dark:text-indigo-400 rounded-md px-2.5 py-0.5 text-xs font-medium">
                      {{ product.category_name || product.category || 'Sin categoría' }}
                    </span>
                  </td>
                  <td class="px-4 py-3 text-center">
                    <div class="font-mono text-base font-bold" :class="[
                      product.current_stock <= 0 ? 'text-red-600 dark:text-red-400' :
                      product.current_stock <= product.min_stock ? 'text-amber-600 dark:text-amber-400' :
                      'text-gray-900 dark:text-white'
                    ]">
                      {{ product.current_stock }}
                    </div>
                    <div class="text-xs text-gray-500 dark:text-zinc-400 mt-0.5">
                      {{ getMeasurementUnitLabel(product.measurement_unit || product.unit) }}
                    </div>
                  </td>
                  <td class="px-4 py-3 text-center">
                    <div class="font-mono text-base font-bold text-gray-900 dark:text-white">{{ formatCurrency(product.sale_price) }}</div>
                    <div class="text-xs text-gray-500 dark:text-zinc-400 mt-0.5">Costo: {{ formatCurrency(product.cost_price) }}</div>
                  </td>
                  <td class="px-4 py-3 text-center">
                    <span 
                      :class="[
                        'px-2.5 py-0.5 text-xs font-medium rounded-md border cursor-help',
                        product.rotation_class === 'A' ? 'bg-emerald-50 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 border-emerald-200 dark:border-emerald-500/20' :
                        product.rotation_class === 'B' ? 'bg-indigo-50 dark:bg-indigo-500/10 text-indigo-700 dark:text-indigo-400 border-indigo-200 dark:border-indigo-500/20' :
                        'bg-amber-50 dark:bg-amber-500/10 text-amber-700 dark:text-amber-400 border-amber-200 dark:border-amber-500/20'
                      ]"
                      :title="getRotationTooltip(product.rotation_class || 'C')"
                    >
                      Clase {{ product.rotation_class || 'C' }}
                    </span>
                    <div class="text-xs text-gray-500 dark:text-zinc-400 mt-1">{{ product.units_sold || 0 }} unidades vendidas</div>
                  </td>
                  <td class="px-4 py-3 text-center">
                    <div class="font-mono text-lg font-bold" :class="[
                      parseFloat(product.margin_percentage || 0) >= 40 ? 'text-green-600 dark:text-green-400' :
                      parseFloat(product.margin_percentage || 0) >= 20 ? 'text-blue-600 dark:text-blue-400' :
                      'text-amber-600 dark:text-amber-400'
                    ]">
                      {{ parseFloat(product.margin_percentage || 0).toFixed(1) }}%
                    </div>
                    <div class="text-xs text-gray-500 dark:text-zinc-400 mt-0.5">margen</div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
          
          <!-- Paginación - Siempre visible cuando hay productos -->
          <div v-if="productsData.products && productsData.products.length > 0" class="bg-white dark:bg-zinc-900 border-t-2 border-gray-200 dark:border-zinc-700 px-6 py-4">
            <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
              <!-- Info de paginación -->
              <div class="text-sm font-medium text-gray-700 dark:text-zinc-300">
                <span v-if="productsData.pagination && productsData.pagination.total">
                  Mostrando <span class="font-bold text-gray-900 dark:text-white">{{ ((filters.currentPage - 1) * filters.itemsPerPage) + 1 }}</span> - 
                  <span class="font-bold text-gray-900 dark:text-white">{{ Math.min(filters.currentPage * filters.itemsPerPage, productsData.pagination.total) }}</span> 
                  de <span class="font-bold text-blue-600 dark:text-blue-400">{{ productsData.pagination.total }}</span> productos
                </span>
                <span v-else>
                  <span class="font-bold text-blue-600 dark:text-blue-400">{{ productsData.products.length }}</span> productos encontrados
                </span>
              </div>
              
              <!-- Controles de paginación -->
              <div v-if="productsData.pagination && productsData.pagination.total > filters.itemsPerPage" class="flex items-center space-x-3">
                <button 
                  @click="filters.currentPage--; loadProductsData()" 
                  :disabled="filters.currentPage <= 1"
                  class="px-4 py-2.5 text-sm font-bold bg-white dark:bg-zinc-800 text-gray-700 dark:text-zinc-200 border-2 border-gray-300 dark:border-zinc-600 rounded-xl hover:bg-gray-100 dark:hover:bg-zinc-700 hover:border-blue-400 dark:hover:border-blue-500 disabled:opacity-40 disabled:cursor-not-allowed transition-all duration-200 flex items-center gap-2 shadow-sm"
                >
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/>
                  </svg>
                  Anterior
                </button>
                
                <div class="px-5 py-2.5 text-sm font-bold text-white bg-blue-600 dark:bg-blue-500 rounded-xl shadow-lg shadow-blue-500/30">
                  {{ filters.currentPage }} / {{ Math.ceil(productsData.pagination.total / filters.itemsPerPage) }}
                </div>
                
                <button 
                  @click="filters.currentPage++; loadProductsData()" 
                  :disabled="filters.currentPage >= Math.ceil(productsData.pagination.total / filters.itemsPerPage)"
                  class="px-4 py-2.5 text-sm font-bold bg-white dark:bg-zinc-800 text-gray-700 dark:text-zinc-200 border-2 border-gray-300 dark:border-zinc-600 rounded-xl hover:bg-gray-100 dark:hover:bg-zinc-700 hover:border-blue-400 dark:hover:border-blue-500 disabled:opacity-40 disabled:cursor-not-allowed transition-all duration-200 flex items-center gap-2 shadow-sm"
                >
                  Siguiente
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
                  </svg>
                </button>
              </div>
              
              <!-- Selector de items por página -->
              <div class="flex items-center gap-2">
                <span class="text-xs text-gray-500 dark:text-zinc-400">Por página:</span>
                <select 
                  v-model="filters.itemsPerPage" 
                  @change="filters.currentPage = 1; loadProductsData()"
                  class="px-3 py-2 text-sm font-medium bg-white dark:bg-zinc-800 border-2 border-gray-300 dark:border-zinc-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-gray-700 dark:text-zinc-200"
                >
                  <option :value="10">10</option>
                  <option :value="25">25</option>
                  <option :value="50">50</option>
                  <option :value="100">100</option>
                </select>
              </div>
            </div>
          </div>
        </div>
        
        <!-- Estado Vacío -->
        <div v-else-if="productsData && productsData.products && productsData.products.length === 0" class="bg-white/90 dark:bg-zinc-900/90  rounded-2xl shadow-lg shadow-gray-200/50 dark:shadow-black/30 p-12">
          <div class="text-center">
            <svg class="w-16 h-16 mx-auto text-gray-400 dark:text-zinc-500 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
            </svg>
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">No hay productos</h3>
            <p class="text-sm text-gray-500 dark:text-zinc-400">No se encontraron productos con los filtros seleccionados.</p>
          </div>
        </div>
        
        <!-- Cargando -->
        <div v-else class="bg-white/90 dark:bg-zinc-900/90  rounded-2xl shadow-lg shadow-gray-200/50 dark:shadow-black/30 p-12">
          <div class="text-center">
            <svg class="w-16 h-16 mx-auto text-blue-500 dark:text-blue-400 animate-spin mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
            </svg>
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Cargando productos...</h3>
            <p class="text-sm text-gray-500 dark:text-zinc-400">Por favor espera un momento.</p>
          </div>
        </div>
      </div>

      <div v-if="activeSection === 'movements'" class="space-y-6 animate-fade-in">
        <!-- Header y Filtros -->
        <div class="bg-white/80 dark:bg-zinc-900/60  rounded-2xl p-5 shadow-lg shadow-gray-200/50 dark:shadow-black/30">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h2 class="text-lg font-bold text-gray-900 dark:text-white">Movimientos de Inventario</h2>
                    <p class="text-sm text-gray-500 dark:text-zinc-400">Registro detallado de entradas y salidas</p>
                </div>
                
                <div class="flex flex-wrap items-center gap-3">
                    <!-- Selector de Período -->
                    <select 
                      v-model="selectedPeriod"
                      @change="handlePeriodChange"
                      class="bg-gray-50 dark:bg-zinc-800 border border-gray-200 dark:border-zinc-700 text-gray-700 dark:text-zinc-200 text-sm rounded-xl focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 block p-2.5"
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
                          class="bg-gray-50 dark:bg-zinc-800 border border-gray-200 dark:border-zinc-700 text-gray-700 dark:text-zinc-200 text-sm rounded-xl focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 block p-2.5 min-w-[150px]"
                        >
                      </div>
                    </template>
        
                    <!-- Filtro por Tipo -->
                    <select 
                      v-model="movementsFilters.type"
                      @change="loadMovementsData"
                      class="bg-gray-50 dark:bg-zinc-800 border border-gray-200 dark:border-zinc-700 text-gray-700 dark:text-zinc-200 text-sm rounded-xl focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 block p-2.5"
                    >
                      <option value="">Todos los tipos</option>
                      <option value="entry">Solo Entradas</option>
                      <option value="exit">Solo Salidas</option>
                    </select>
        
                    <!-- Botón Exportar -->
                    <button 
                      @click="exportMovements"
                      class="text-white bg-emerald-600 dark:bg-emerald-700 hover:bg-emerald-700 dark:hover:bg-emerald-600 font-medium rounded-xl text-sm px-5 py-2.5 text-center inline-flex items-center transition-all duration-200 shadow-lg shadow-emerald-400/40 dark:shadow-emerald-900/50"
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
          <div class="bg-white/80 dark:bg-zinc-900/80  rounded-2xl p-4 shadow-lg shadow-gray-200/50 dark:shadow-black/30 hover:shadow-xl transition-all duration-200">
            <div class="flex items-center space-x-3">
                <div class="bg-indigo-50 dark:bg-indigo-950/50 rounded-xl p-3">
                    <svg class="w-5 h-5 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                    </svg>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-xs font-medium text-gray-500 dark:text-zinc-400 uppercase tracking-wide">Movimientos</p>
                    <p class="text-xl font-bold text-gray-900 dark:text-white">{{ movementsData?.summary?.total_movements || 0 }}</p>
                </div>
            </div>
          </div>

          <!-- Entradas -->
          <div class="bg-white/80 dark:bg-zinc-900/80  rounded-2xl p-4 shadow-lg shadow-gray-200/50 dark:shadow-black/30 hover:shadow-xl transition-all duration-200">
            <div class="flex items-center space-x-3">
                <div class="bg-emerald-50 dark:bg-emerald-950/50 rounded-xl p-3">
                    <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path>
                    </svg>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-xs font-medium text-gray-500 dark:text-zinc-400 uppercase tracking-wide">Entradas</p>
                    <p class="text-xl font-bold text-gray-900 dark:text-white">{{ movementsData?.summary?.total_entries || 0 }}</p>
                </div>
            </div>
          </div>

          <!-- Salidas -->
          <div class="bg-white/80 dark:bg-zinc-900/80  rounded-2xl p-4 shadow-lg shadow-gray-200/50 dark:shadow-black/30 hover:shadow-xl transition-all duration-200">
            <div class="flex items-center space-x-3">
                <div class="bg-rose-50 dark:bg-rose-950/50 rounded-xl p-3">
                    <svg class="w-5 h-5 text-rose-600 dark:text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"></path>
                    </svg>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-xs font-medium text-gray-500 dark:text-zinc-400 uppercase tracking-wide">Salidas</p>
                    <p class="text-xl font-bold text-gray-900 dark:text-white">{{ movementsData?.summary?.total_exits || 0 }}</p>
                </div>
            </div>
          </div>

          <!-- Valor Entradas -->
          <div class="bg-white/80 dark:bg-zinc-900/80  rounded-2xl p-4 shadow-lg shadow-gray-200/50 dark:shadow-black/30 hover:shadow-xl transition-all duration-200">
            <div class="flex items-center space-x-3">
                <div class="bg-teal-50 dark:bg-teal-950/50 rounded-xl p-3">
                    <svg class="w-5 h-5 text-teal-600 dark:text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-xs font-medium text-gray-500 dark:text-zinc-400 uppercase tracking-wide">V. Entradas</p>
                    <p class="text-lg font-bold text-gray-900 dark:text-white">{{ formatCurrency(movementsData?.summary?.total_entry_value || 0) }}</p>
                </div>
            </div>
          </div>

          <!-- Valor Salidas -->
          <div class="bg-white/80 dark:bg-zinc-900/80  rounded-2xl p-4 shadow-lg shadow-gray-200/50 dark:shadow-black/30 hover:shadow-xl transition-all duration-200">
            <div class="flex items-center space-x-3">
                <div class="bg-orange-50 dark:bg-orange-950/50 rounded-xl p-3">
                    <svg class="w-5 h-5 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-xs font-medium text-gray-500 dark:text-zinc-400 uppercase tracking-wide">V. Salidas</p>
                    <p class="text-lg font-bold text-gray-900 dark:text-white">{{ formatCurrency(movementsData?.summary?.total_exit_value || 0) }}</p>
                </div>
            </div>
          </div>

          <!-- Movimiento Neto -->
          <div class="bg-white/80 dark:bg-zinc-900/80  rounded-2xl p-4 shadow-lg shadow-gray-200/50 dark:shadow-black/30 hover:shadow-xl transition-all duration-200">
            <div class="flex items-center space-x-3">
                <div class="bg-purple-50 dark:bg-purple-950/50 rounded-xl p-3">
                    <svg class="w-5 h-5 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"></path>
                    </svg>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-xs font-medium text-gray-500 dark:text-zinc-400 uppercase tracking-wide">Balance</p>
                    <p class="text-lg font-bold" :class="(movementsData?.summary?.net_movement || 0) >= 0 ? 'text-gray-900 dark:text-white' : 'text-red-600 dark:text-red-400'">
                      {{ formatCurrency(movementsData?.summary?.net_movement || 0) }}
                    </p>
                </div>
            </div>
          </div>
        </div>

        <!-- Tabla de Movimientos -->
        <div class="bg-white/90 dark:bg-zinc-900/90  rounded-2xl shadow-lg shadow-gray-200/50 dark:shadow-black/30 overflow-hidden">
          <div class="px-6 py-4 bg-transparent flex items-center justify-between">
            <div>
                <h3 class="text-lg font-bold text-gray-900 dark:text-white">Historial de Movimientos</h3>
                <p class="text-xs text-gray-500 dark:text-zinc-400 mt-1">Registro cronológico de operaciones</p>
            </div>
            <div class="flex items-center space-x-2">
                <span class="px-2 py-1 bg-indigo-50 dark:bg-indigo-500/10 text-indigo-700 dark:text-indigo-400 text-xs font-semibold rounded-xl">
                    {{ movementsData?.movements?.length || 0 }} registros
                </span>
            </div>
          </div>
          <div class="overflow-x-auto">
            <table class="min-w-full">
              <thead class="bg-gray-50/80 dark:bg-zinc-800/50">
                <tr>
                  <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 dark:text-zinc-400 uppercase tracking-wide">Fecha</th>
                  <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 dark:text-zinc-400 uppercase tracking-wide">Flujo</th>
                  <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 dark:text-zinc-400 uppercase tracking-wide">Tipo Movimiento</th>
                  <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 dark:text-zinc-400 uppercase tracking-wide">Producto</th>
                  <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 dark:text-zinc-400 uppercase tracking-wide">Cantidad</th>
                  <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 dark:text-zinc-400 uppercase tracking-wide">Precio Unit.</th>
                  <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 dark:text-zinc-400 uppercase tracking-wide">Total</th>
                  <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 dark:text-zinc-400 uppercase tracking-wide">Fuente</th>
                </tr>
              </thead>
              <tbody class="bg-transparent">
                <!-- Movimientos con iconografía de flujo -->
                <tr v-for="movement in movementsData?.movements || []" :key="movement.movement_id" 
                    class="hover:bg-gray-50/50 dark:hover:bg-zinc-800/30 transition-colors duration-150 border-b border-gray-100 dark:border-zinc-800/30 last:border-b-0"
                    :class="movement.movement_type === 'entry' ? 'border-l-4 border-l-emerald-500 dark:border-l-emerald-400' : 'border-l-4 border-l-rose-500 dark:border-l-rose-400'">
                  
                  <!-- Fecha -->
                  <td class="px-4 py-3 whitespace-nowrap">
                    <div class="flex items-center space-x-2">
                      <i class="fas fa-calendar-day text-gray-400 dark:text-zinc-500 text-xs"></i>
                      <span class="text-sm text-gray-700 dark:text-zinc-300 font-medium">{{ formatDate(movement.movement_date) }}</span>
                    </div>
                  </td>
                  
                  <!-- FLUJO - Flechas SVG Minimalistas -->
                  <td class="px-4 py-3 whitespace-nowrap">
                    <!-- Entrada: Flecha hacia abajo-izquierda (Verde Esmeralda) -->
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
                    <!-- Salida: Flecha hacia arriba-derecha (Rojo Suave) -->
                    <svg v-else-if="movement.movement_type === 'exit'" 
                         class="w-6 h-6 text-rose-500 dark:text-rose-400" 
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
                         class="w-6 h-6 text-blue-500 dark:text-blue-400" 
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
                    <span class="text-sm font-medium text-gray-700 dark:text-zinc-300">{{ movement.movement_reason }}</span>
                  </td>
                  
                  <!-- Producto - Solo Nombre -->
                  <td class="px-4 py-3">
                    <span class="text-sm font-semibold text-gray-900 dark:text-white truncate max-w-xs">{{ movement.product_name }}</span>
                  </td>
                  
                  <!-- CANTIDAD - Con formato de color -->
                  <td class="px-4 py-3 whitespace-nowrap">
                    <div class="flex items-center space-x-2">
                      <!-- Signo visual -->
                      <span v-if="movement.movement_type === 'entry'" 
                            class="inline-flex items-center justify-center w-6 h-6 bg-emerald-100 dark:bg-emerald-500/20 text-emerald-700 dark:text-emerald-400 rounded-full text-xs font-bold">
                        +
                      </span>
                      <span v-else-if="movement.movement_type === 'exit'" 
                            class="inline-flex items-center justify-center w-6 h-6 bg-rose-100 dark:bg-rose-500/20 text-rose-700 dark:text-rose-400 rounded-full text-xs font-bold">
                        -
                      </span>
                      <span v-else 
                            class="inline-flex items-center justify-center w-6 h-6 bg-blue-100 dark:bg-blue-500/20 text-blue-700 dark:text-blue-400 rounded-full text-xs font-bold">
                        =
                      </span>
                      
                      <!-- Número con color -->
                      <span class="text-base font-bold" 
                            :class="movement.movement_type === 'entry' 
                              ? 'text-emerald-600 dark:text-emerald-400' 
                              : movement.movement_type === 'exit' 
                                ? 'text-rose-600 dark:text-rose-400' 
                                : 'text-blue-600 dark:text-blue-400'">
                        {{ Math.abs(movement.quantity) }}
                      </span>
                    </div>
                  </td>
                  
                  <!-- Precio Unitario -->
                  <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600 dark:text-zinc-400 font-medium">
                    {{ formatCurrency(movement.unit_price) }}
                  </td>
                  
                  <!-- Total con color -->
                  <td class="px-4 py-3 whitespace-nowrap">
                    <span class="text-sm font-bold" 
                          :class="movement.movement_type === 'entry' 
                            ? 'text-emerald-600 dark:text-emerald-400' 
                            : movement.movement_type === 'exit' 
                              ? 'text-rose-600 dark:text-rose-400' 
                              : 'text-blue-600 dark:text-blue-400'">
                      {{ formatCurrency(Math.abs(movement.total_value)) }}
                    </span>
                  </td>
                  
                  <!-- FUENTE - Enlace Sutil -->
                  <td class="px-4 py-3 whitespace-nowrap">
                    <button v-if="movement.document_number && movement.document_number !== 'N/A'"
                            @click="viewMovementDocument(movement)"
                            class="text-sm font-mono text-slate-600 dark:text-zinc-400 hover:text-slate-900 dark:hover:text-white hover:underline decoration-slate-400 dark:decoration-zinc-500 decoration-1 underline-offset-2 transition-all duration-150 cursor-pointer">
                      {{ movement.document_number }}
                    </button>
                    <span v-else class="text-sm text-gray-400 dark:text-zinc-600 italic">
                      —
                    </span>
                  </td>
                </tr>
                
                <!-- Estado sin movimientos -->
                <tr v-if="!movementsData?.movements || movementsData.movements.length === 0">
                  <td colspan="7" class="px-6 py-12 text-center">
                    <div class="flex flex-col items-center justify-center py-8">
                      <div class="w-16 h-16 bg-gray-50 dark:bg-zinc-800 rounded-full flex items-center justify-center mb-4">
                        <i class="fas fa-box-open text-gray-400 dark:text-zinc-500 text-2xl"></i>
                      </div>
                      <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-1">No hay movimientos</h3>
                      <p class="text-sm text-gray-500 dark:text-zinc-400">No se encontraron registros con los filtros seleccionados</p>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
          
          <!-- Controles de Paginación para Movimientos -->
          <div v-if="movementsData?.movements && movementsData.movements.length > 0" class="bg-gray-50 dark:bg-transparent px-6 py-3 border-t border-gray-200 dark:border-zinc-800">
            <div class="flex items-center justify-between">
              <!-- Items por página y información -->
              <div class="flex items-center space-x-4">
                <div class="flex items-center space-x-2">
                  <label class="text-sm text-gray-700 dark:text-zinc-300">Mostrar:</label>
                  <select v-model="movementsFilters.itemsPerPage" 
                          @change="movementsFilters.currentPage = 1; loadMovementsData()"
                          class="text-sm bg-white dark:bg-zinc-800 border border-gray-300 dark:border-zinc-700 text-gray-700 dark:text-zinc-200 rounded-lg focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400 focus:border-indigo-500 px-3 py-1.5">
                    <option value="10">10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                  </select>
                  <span class="text-sm text-gray-700 dark:text-zinc-300">por página</span>
                </div>
                
                <div class="text-sm text-gray-700 dark:text-zinc-300">
                  Mostrando {{ movementsPaginationInfo.start }} a {{ movementsPaginationInfo.end }} de {{ movementsPaginationInfo.total }}
                </div>
              </div>
              
              <div class="flex items-center space-x-1">
                <button @click="movementsFilters.currentPage--; loadMovementsData()" 
                        :disabled="movementsFilters.currentPage === 1"
                        class="p-1.5 text-gray-500 dark:text-zinc-400 bg-white dark:bg-zinc-800 border border-gray-300 dark:border-zinc-700 rounded-lg hover:bg-gray-50 dark:hover:bg-zinc-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
                  <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                  </svg>
                </button>
                <span class="px-2.5 py-1 text-xs font-medium text-gray-900 dark:text-white">
                  {{ movementsFilters.currentPage }} / {{ movementsTotalPages }}
                </span>
                <button @click="movementsFilters.currentPage++; loadMovementsData()" 
                        :disabled="movementsFilters.currentPage >= movementsTotalPages"
                        class="p-1.5 text-gray-500 dark:text-zinc-400 bg-white dark:bg-zinc-800 border border-gray-300 dark:border-zinc-700 rounded-lg hover:bg-gray-50 dark:hover:bg-zinc-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
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
        <div class="bg-white/80 dark:bg-zinc-900/60  rounded-2xl p-5 shadow-lg shadow-gray-200/50 dark:shadow-black/30">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h2 class="text-lg font-bold text-gray-900 dark:text-white">Análisis por Cliente</h2>
                    <p class="text-sm text-gray-500 dark:text-zinc-400">Comportamiento y valor de clientes</p>
                </div>
                
                <div class="flex flex-wrap items-center gap-3">
                    <!-- Selector de Período -->
                    <select 
                      v-model="selectedPeriod"
                      @change="handlePeriodChange"
                      class="bg-gray-50 dark:bg-zinc-800 border border-gray-200 dark:border-zinc-700 text-gray-700 dark:text-zinc-200 text-sm rounded-xl focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 block p-2.5"
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
                          class="bg-gray-50 dark:bg-zinc-800 border border-gray-200 dark:border-zinc-700 text-gray-700 dark:text-zinc-200 text-sm rounded-xl focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 block p-2.5 min-w-[150px]"
                        >
                      </div>
                    </template>
        
                    <!-- Ordenar por -->
                    <select 
                      v-model="customersFilters.sortBy"
                      @change="loadCustomersData"
                      class="bg-gray-50 dark:bg-zinc-800 border border-gray-200 dark:border-zinc-700 text-gray-700 dark:text-zinc-200 text-sm rounded-xl focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 block p-2.5"
                    >
                      <option value="total_spent">Valor Total</option>
                      <option value="total_purchases">Más Compras</option>
                      <option value="unique_products_bought">Más Productos</option>
                    </select>
        
                    <!-- Botón Exportar -->
                    <button 
                      @click="exportCustomers"
                      class="text-white bg-emerald-600 dark:bg-emerald-700 hover:bg-emerald-700 dark:hover:bg-emerald-600 focus:ring-4 focus:ring-emerald-300 dark:focus:ring-emerald-900/50 font-medium rounded-xl text-sm px-5 py-2.5 text-center inline-flex items-center transition-all duration-200 shadow-lg shadow-emerald-400/40 dark:shadow-emerald-900/50"
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
          <div class="bg-white/80 dark:bg-zinc-900/80  rounded-2xl p-4 shadow-lg shadow-gray-200/50 dark:shadow-black/30 hover:shadow-xl transition-all duration-200">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-blue-50 dark:bg-blue-950/50 rounded-lg flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-xs font-medium text-gray-500 dark:text-zinc-400 uppercase tracking-wide">Clientes</p>
                    <p class="text-xl font-bold text-gray-900 dark:text-white">{{ customersData.summary.total_customers }}</p>
                </div>
            </div>
          </div>

          <!-- Ingresos Totales -->
          <div class="bg-white/80 dark:bg-zinc-900/80  rounded-2xl p-4 shadow-lg shadow-gray-200/50 dark:shadow-black/30 hover:shadow-xl transition-all duration-200">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-emerald-50 dark:bg-emerald-950/50 rounded-lg flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-xs font-medium text-gray-500 dark:text-zinc-400 uppercase tracking-wide">Ingresos</p>
                    <p class="text-lg font-bold text-gray-900 dark:text-white">{{ formatCurrency(customersData.summary.total_revenue) }}</p>
                </div>
            </div>
          </div>

          <!-- Ganancia Total -->
          <div class="bg-white/80 dark:bg-zinc-900/80  rounded-2xl p-4 shadow-lg shadow-gray-200/50 dark:shadow-black/30 hover:shadow-xl transition-all duration-200">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-teal-50 dark:bg-teal-950/50 rounded-lg flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-teal-600 dark:text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"></path>
                    </svg>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-xs font-medium text-gray-500 dark:text-zinc-400 uppercase tracking-wide">Ganancia</p>
                    <p class="text-lg font-bold text-gray-900 dark:text-white">{{ formatCurrency(customersData.summary.total_profit) }}</p>
                </div>
            </div>
          </div>

          <!-- Valor Promedio -->
          <div class="bg-white/80 dark:bg-zinc-900/80  rounded-2xl p-4 shadow-lg shadow-gray-200/50 dark:shadow-black/30 hover:shadow-xl transition-all duration-200">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-purple-50 dark:bg-purple-950/50 rounded-lg flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path>
                    </svg>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-xs font-medium text-gray-500 dark:text-zinc-400 uppercase tracking-wide">Promedio</p>
                    <p class="text-lg font-bold text-gray-900 dark:text-white">{{ formatCurrency(customersData.summary.avg_customer_value) }}</p>
                </div>
            </div>
          </div>

          <!-- Descuento Prom. -->
          <div class="bg-white/80 dark:bg-zinc-900/80  rounded-2xl p-4 shadow-lg shadow-gray-200/50 dark:shadow-black/30 hover:shadow-xl transition-all duration-200">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-orange-50 dark:bg-orange-950/50 rounded-lg flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-percentage text-orange-600 dark:text-orange-400"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-xs font-medium text-gray-500 dark:text-zinc-400 uppercase tracking-wide">Descuento</p>
                    <p class="text-lg font-bold text-gray-900 dark:text-white">{{ formatPercentage(customersData.summary.avg_discount) }}</p>
                </div>
            </div>
          </div>

          <!-- Cliente Top -->
          <div class="bg-white/80 dark:bg-zinc-900/80  rounded-2xl p-4 shadow-lg shadow-gray-200/50 dark:shadow-black/30 hover:shadow-xl transition-all duration-200">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-indigo-50 dark:bg-indigo-950/50 rounded-lg flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path>
                    </svg>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-xs font-medium text-gray-500 dark:text-zinc-400 uppercase tracking-wide">Top Cliente</p>
                    <p class="text-sm font-bold text-gray-900 dark:text-white truncate" v-if="customersData.summary.top_customer">
                      {{ customersData.summary.top_customer.name }}
                    </p>
                    <p class="text-sm font-bold text-gray-900 dark:text-white" v-else>N/A</p>
                </div>
            </div>
          </div>
        </div>

        <!-- Tabla de Clientes -->
        <div class="bg-white/90 dark:bg-zinc-900/90  rounded-2xl shadow-lg shadow-gray-200/50 dark:shadow-black/30 overflow-hidden">
          <div class="px-6 py-4 bg-transparent flex items-center justify-between">
            <div>
                <h3 class="text-lg font-bold text-gray-900 dark:text-white">Análisis de Clientes</h3>
                <p class="text-xs text-gray-500 dark:text-zinc-400 mt-1">Ranking y comportamiento de compra</p>
            </div>
            <div class="flex items-center space-x-2">
                <span class="px-2 py-1 bg-indigo-50 dark:bg-indigo-500/10 text-indigo-700 dark:text-indigo-400 text-xs font-semibold rounded-xl">
                    {{ customersData?.customers?.length || 0 }} clientes
                </span>
            </div>
          </div>
          <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-zinc-800">
              <thead class="bg-gray-50 dark:bg-transparent border-b border-gray-200 dark:border-zinc-800">
                <tr>
                  <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-zinc-400 uppercase tracking-wider">Cliente</th>
                  <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-zinc-400 uppercase tracking-wider">Total Compras</th>
                  <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-zinc-400 uppercase tracking-wider">Total Gastado</th>
                  <!-- Columna Crédito: Solo si es Premium/Enterprise -->
                  <th v-if="isPremiumOrEnterprise" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-zinc-400 uppercase tracking-wider">Crédito</th>
                  <!-- Columna Puntos: Solo si es Premium/Enterprise -->
                  <th v-if="isPremiumOrEnterprise" class="px-6 py-3 text-center text-xs font-semibold text-gray-500 dark:text-zinc-400 uppercase tracking-wider">Puntos</th>
                  <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-zinc-400 uppercase tracking-wider">Productos Únicos</th>
                  <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-zinc-400 uppercase tracking-wider">Items Totales</th>
                  <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-zinc-400 uppercase tracking-wider">Frecuencia</th>
                </tr>
              </thead>
              <tbody class="bg-white dark:bg-zinc-900 divide-y divide-gray-200 dark:divide-zinc-800" v-if="customersData && customersData.customers">
                <tr v-for="customer in customersData.customers" :key="customer.customer_id" class="hover:bg-gray-50/80 dark:hover:bg-zinc-800/30 border-b border-gray-100 dark:border-zinc-800/50 transition-colors">
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center">
                      <!-- AVATAR CON INICIALES (Visual Polish - Clientes) -->
                      <div class="w-9 h-9 rounded-full flex items-center justify-center shadow-sm bg-gradient-to-br from-slate-100 to-slate-200 dark:from-slate-700 dark:to-slate-800">
                        <span class="text-xs font-bold text-slate-600 dark:text-slate-300">
                          {{ getInitials(customer.customer_name) }}
                        </span>
                      </div>
                      <div class="ml-3">
                        <div class="text-sm font-bold text-gray-900 dark:text-white">{{ customer.customer_name }}</div>
                        <div class="text-xs text-gray-500 dark:text-zinc-400" v-if="customer.email">{{ customer.email }}</div>
                      </div>
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-50 dark:bg-blue-500/10 text-blue-700 dark:text-blue-400 border border-blue-100 dark:border-blue-500/20">
                      {{ customer.total_purchases }} compras
                    </span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-emerald-600 dark:text-emerald-400">
                    {{ formatCurrency(customer.total_spent) }}
                  </td>
                  
                  <!-- COLUMNA CRÉDITO: Solo si es Premium/Enterprise -->
                  <td v-if="isPremiumOrEnterprise" class="px-6 py-4 whitespace-nowrap">
                    <div class="space-y-1">
                      <!-- Límite de crédito -->
                      <div class="text-xs font-medium text-gray-500 dark:text-zinc-400">
                        Límite: <span class="text-gray-900 dark:text-white font-bold">{{ formatCurrency(customer.credit_limit || 0) }}</span>
                      </div>
                      <!-- Deuda actual -->
                      <div class="text-xs font-medium text-gray-500 dark:text-zinc-400">
                        Debe: <span class="text-rose-600 dark:text-rose-400 font-bold">{{ formatCurrency(customer.current_debt || 0) }}</span>
                      </div>
                    </div>
                  </td>
                  
                  <!-- COLUMNA PUNTOS: Solo si es Premium/Enterprise -->
                  <td v-if="isPremiumOrEnterprise" class="px-6 py-4 whitespace-nowrap text-center">
                    <div class="inline-flex items-center px-3 py-1.5 rounded-lg bg-amber-50 dark:bg-amber-500/10 border border-amber-200 dark:border-amber-500/20">
                      <svg class="w-4 h-4 text-amber-500 dark:text-amber-400 mr-1.5" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                      </svg>
                      <span class="text-sm font-bold text-amber-700 dark:text-amber-400">
                        {{ customer.loyalty_points || 0 }}
                      </span>
                    </div>
                  </td>
                  
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-zinc-400">
                    {{ customer.unique_products_bought }} productos
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-zinc-400">
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
          
          <!-- Controles de Paginación para Clientes -->
          <div v-if="customersData?.customers && customersData.customers.length > 0" class="bg-gray-50 dark:bg-transparent px-6 py-3 border-t border-gray-200 dark:border-zinc-800 rounded-b-lg">
            <div class="flex items-center justify-between">
              <!-- Items por página y información -->
              <div class="flex items-center space-x-4">
                <div class="flex items-center space-x-2">
                  <label class="text-sm text-gray-700 dark:text-zinc-300">Mostrar:</label>
                  <select v-model="customersFilters.itemsPerPage" @change="customersFilters.currentPage = 1; loadCustomersData()" 
                          class="text-sm bg-white dark:bg-zinc-800 border-gray-300 dark:border-zinc-700 text-gray-900 dark:text-zinc-200 rounded-lg focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400 focus:border-indigo-500">
                    <option value="10">10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                  </select>
                  <span class="text-sm text-gray-700 dark:text-zinc-300">por página</span>
                </div>
                
                <!-- Información de paginación -->
                <div class="text-sm text-gray-700 dark:text-zinc-300">
                  Mostrando {{ customersPaginationInfo.start }} a {{ customersPaginationInfo.end }} de {{ customersPaginationInfo.total }}
                </div>
              </div>
              
              <!-- Controles de paginación -->
              <div class="flex items-center space-x-2">
                <!-- Botón Primera página -->
                <button @click="customersFilters.currentPage = 1; loadCustomersData()" 
                        :disabled="customersFilters.currentPage === 1"
                        class="px-3 py-2 text-sm font-medium text-gray-500 dark:text-zinc-400 bg-white dark:bg-zinc-800 border border-gray-300 dark:border-zinc-700 rounded-lg hover:bg-gray-50 dark:hover:bg-zinc-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"></path>
                  </svg>
                </button>
                
                <!-- Botón Anterior -->
                <button @click="customersFilters.currentPage--; loadCustomersData()" 
                        :disabled="customersFilters.currentPage === 1"
                        class="px-3 py-2 text-sm font-medium text-gray-500 dark:text-zinc-400 bg-white dark:bg-zinc-800 border border-gray-300 dark:border-zinc-700 rounded-lg hover:bg-gray-50 dark:hover:bg-zinc-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
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
                                ? 'bg-indigo-600 dark:bg-indigo-500 text-white border border-indigo-600 dark:border-indigo-500' 
                                : 'text-gray-500 dark:text-zinc-400 bg-white dark:bg-zinc-800 border border-gray-300 dark:border-zinc-700 hover:bg-gray-50 dark:hover:bg-zinc-700'
                            ]">
                      {{ page }}
                    </button>
                    <span v-else-if="Math.abs(page - customersFilters.currentPage) === 3" class="px-2 text-gray-400 dark:text-zinc-500">...</span>
                  </template>
                </div>
                
                <!-- Botón Siguiente -->
                <button @click="customersFilters.currentPage++; loadCustomersData()" 
                        :disabled="customersFilters.currentPage >= customersTotalPages"
                        class="px-3 py-2 text-sm font-medium text-gray-500 dark:text-zinc-400 bg-white dark:bg-zinc-800 border border-gray-300 dark:border-zinc-700 rounded-lg hover:bg-gray-50 dark:hover:bg-zinc-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                  </svg>
                </button>
                
                <!-- Botón Última página -->
                <button @click="customersFilters.currentPage = customersTotalPages; loadCustomersData()" 
                        :disabled="customersFilters.currentPage === customersTotalPages"
                        class="px-3 py-2 text-sm font-medium text-gray-500 dark:text-zinc-400 bg-white dark:bg-zinc-800 border border-gray-300 dark:border-zinc-700 rounded-lg hover:bg-gray-50 dark:hover:bg-zinc-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
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
          <div class="bg-white/80 dark:bg-zinc-900/80  rounded-2xl p-5 shadow-lg shadow-gray-200/50 dark:shadow-black/30 hover:shadow-xl transition-all duration-200">
            <div class="flex items-center space-x-4">
              <div class="w-12 h-12 bg-slate-50 dark:bg-slate-950/50 rounded-xl flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6 text-slate-600 dark:text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
              </div>
              <div class="flex-1 min-w-0">
                <div class="flex items-center justify-between mb-2">
                  <h3 class="text-sm font-semibold text-gray-700 dark:text-zinc-300">Total Proveedores</h3>
                </div>
                <p class="text-2xl font-bold text-gray-900 dark:text-white mb-1">{{ suppliersData?.summary?.total_suppliers || 0 }}</p>
                <p class="text-sm text-gray-500 dark:text-zinc-400">{{ suppliersData?.summary?.active_suppliers || 0 }} activos</p>
              </div>
            </div>
          </div>

          <!-- Cuentas por Pagar -->
          <div class="bg-white/80 dark:bg-zinc-900/80  rounded-2xl p-5 shadow-lg shadow-gray-200/50 dark:shadow-black/30 hover:shadow-xl transition-all duration-200">
            <div class="flex items-center space-x-4">
              <div class="w-12 h-12 bg-rose-50 dark:bg-rose-950/50 rounded-xl flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6 text-rose-600 dark:text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
              </div>
              <div class="flex-1 min-w-0">
                <div class="flex items-center justify-between mb-2">
                  <h3 class="text-sm font-semibold text-gray-700 dark:text-zinc-300">Cuentas por Pagar</h3>
                </div>
                <p class="text-2xl font-bold text-gray-900 dark:text-white mb-1">{{ formatCurrency(suppliersData?.summary?.total_debt || 0) }}</p>
                <p class="text-sm text-gray-500 dark:text-zinc-400">Dinero que debo a proveedores</p>
              </div>
            </div>
          </div>

          <!-- Mejor Proveedor -->
          <div class="bg-white/80 dark:bg-zinc-900/80  rounded-2xl p-5 shadow-lg shadow-gray-200/50 dark:shadow-black/30 hover:shadow-xl transition-all duration-200">
            <div class="flex items-center space-x-4">
              <div class="w-12 h-12 bg-amber-50 dark:bg-amber-950/50 rounded-xl flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                </svg>
              </div>
              <div class="flex-1 min-w-0">
                <div class="flex items-center justify-between mb-2">
                  <h3 class="text-sm font-semibold text-gray-700 dark:text-zinc-300">Mejor Proveedor</h3>
                </div>
                <p class="text-lg font-bold text-gray-900 dark:text-white mb-1 truncate">
                  {{ suppliersData?.best_supplier?.name || 'N/A' }}
                </p>
                <p class="text-sm text-gray-500 dark:text-zinc-400">Al que más le compro</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Tabla de Proveedores -->
        <div class="bg-white/90 dark:bg-zinc-900/90  rounded-2xl shadow-lg shadow-gray-200/50 dark:shadow-black/30 overflow-hidden">
          <div class="bg-transparent px-4 py-3 flex items-center justify-between">
            <div>
              <h2 class="text-base font-bold text-gray-900 dark:text-white">Lista de Proveedores</h2>
              <p class="text-xs text-gray-500 dark:text-zinc-400 mt-0.5">{{ suppliersData?.suppliers?.length || 0 }} proveedores registrados</p>
            </div>
          </div>

          <div v-if="!suppliersData" class="p-12 text-center">
            <div class="w-12 h-12 bg-gray-100 dark:bg-zinc-800 rounded-full flex items-center justify-center mx-auto mb-3 animate-pulse">
              <svg class="w-6 h-6 text-gray-400 dark:text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
              </svg>
            </div>
            <p class="text-sm font-semibold text-gray-700 dark:text-zinc-300">Cargando proveedores...</p>
          </div>

          <div v-else-if="suppliersData?.suppliers?.length === 0" class="text-center py-12">
            <div class="w-16 h-16 bg-slate-100 dark:bg-slate-800/50 rounded-full flex items-center justify-center mx-auto mb-4">
              <svg class="w-8 h-8 text-slate-400 dark:text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
              </svg>
            </div>
            <p class="text-sm font-semibold text-gray-700 dark:text-white">No hay proveedores registrados</p>
            <p class="text-xs text-gray-500 dark:text-zinc-400 mt-1">Ve a Gestión de Proveedores para agregar uno</p>
          </div>

          <div v-else class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-zinc-800">
              <thead class="bg-gray-50 dark:bg-transparent border-b border-gray-200 dark:border-zinc-800">
                <tr>
                  <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 dark:text-zinc-400 uppercase tracking-wide">Proveedor</th>
                  <th class="px-4 py-3 text-center text-xs font-bold text-gray-600 dark:text-zinc-400 uppercase tracking-wide">Tiempo Entrega</th>
                  <th class="px-4 py-3 text-center text-xs font-bold text-gray-600 dark:text-zinc-400 uppercase tracking-wide">Última Compra</th>
                  <th class="px-4 py-3 text-center text-xs font-bold text-gray-600 dark:text-zinc-400 uppercase tracking-wide">Estado</th>
                  <th class="px-4 py-3 text-center text-xs font-bold text-gray-600 dark:text-zinc-400 uppercase tracking-wide">Acciones</th>
                </tr>
              </thead>
              <tbody class="bg-white dark:bg-zinc-900 divide-y divide-gray-200 dark:divide-zinc-800">
                <tr v-for="supplier in paginatedSuppliers" :key="supplier.id" class="hover:bg-gray-50 dark:hover:bg-zinc-800/30 border-b border-gray-200 dark:border-zinc-800 transition-colors">
                  <td class="px-4 py-4">
                    <div>
                      <div class="text-sm font-semibold text-gray-900 dark:text-white">{{ supplier.name }}</div>
                      <div class="text-xs text-gray-500 dark:text-zinc-400 mt-0.5">
                        {{ supplier.contact_name || 'Sin contacto' }}
                      </div>
                      <div class="text-xs text-gray-400 dark:text-zinc-500 mt-0.5 flex items-center space-x-2">
                        <span v-if="supplier.phone">📞 {{ supplier.phone }}</span>
                        <span v-if="supplier.city">📍 {{ supplier.city }}</span>
                      </div>
                    </div>
                  </td>
                  <td class="px-4 py-4 text-center">
                    <div class="text-sm font-medium text-gray-900 dark:text-white">
                      {{ supplier.delivery_time || '2-3 días' }}
                    </div>
                  </td>
                  <td class="px-4 py-4 text-center">
                    <div class="text-sm text-gray-900 dark:text-white" v-if="supplier.last_purchase_date">
                      {{ formatDate(supplier.last_purchase_date) }}
                    </div>
                    <div class="text-xs text-gray-400 dark:text-zinc-500" v-else>Sin compras</div>
                  </td>
                  <td class="px-4 py-4 text-center">
                    <span :class="[
                      'px-2 py-1 text-xs font-semibold rounded-full border',
                      supplier.status === 'active' 
                        ? 'bg-emerald-50 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 border-emerald-100 dark:border-emerald-500/20' 
                        : 'bg-slate-50 dark:bg-slate-800 text-slate-700 dark:text-slate-400 border-slate-100 dark:border-slate-700'
                    ]">
                      {{ supplier.status === 'active' ? 'Activo' : 'Inactivo' }}
                    </span>
                  </td>
                  <td class="px-4 py-4 text-center">
                    <div class="flex items-center justify-center space-x-2">
                      <button class="px-3 py-1.5 text-xs font-medium text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-500/10 hover:bg-blue-100 dark:hover:bg-blue-500/20 border border-blue-100 dark:border-blue-500/20 rounded-lg transition-colors">
                        Ver Productos
                      </button>
                      <button class="px-3 py-1.5 text-xs font-medium text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-500/10 hover:bg-emerald-100 dark:hover:bg-emerald-500/20 border border-emerald-100 dark:border-emerald-500/20 rounded-lg transition-colors">
                        Nuevo Pedido
                      </button>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- Paginador -->
          <div v-if="suppliersData?.suppliers && suppliersData.suppliers.length > 0" class="bg-gray-50 dark:bg-transparent border-t border-gray-200 dark:border-zinc-800 px-4 py-3 flex items-center justify-between">
            <div class="flex items-center space-x-3">
              <div class="flex items-center space-x-2">
                <span class="text-xs font-medium text-gray-700 dark:text-zinc-300">Mostrar:</span>
                <select v-model="suppliersFilters.itemsPerPage" @change="suppliersFilters.currentPage = 1"
                        class="bg-white dark:bg-zinc-800 border border-gray-300 dark:border-zinc-700 text-gray-900 dark:text-zinc-200 rounded-lg px-2 py-1 text-xs focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400">
                  <option value="10">10</option>
                  <option value="25">25</option>
                  <option value="50">50</option>
                  <option value="100">100</option>
                </select>
                <span class="text-xs text-gray-700 dark:text-zinc-300">por página</span>
              </div>
              <div class="text-xs text-gray-700 dark:text-zinc-300">
                Mostrando {{ suppliersPaginationInfo.start }} a {{ suppliersPaginationInfo.end }} de {{ suppliersPaginationInfo.total }}
              </div>
            </div>
            
            <div class="flex items-center space-x-1">
              <button @click="suppliersFilters.currentPage = 1" :disabled="suppliersFilters.currentPage === 1"
                      class="p-1.5 text-gray-500 dark:text-zinc-400 bg-white dark:bg-zinc-800 border border-gray-300 dark:border-zinc-700 rounded-lg hover:bg-gray-50 dark:hover:bg-zinc-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"></path>
                </svg>
              </button>
              <button @click="suppliersFilters.currentPage--" :disabled="suppliersFilters.currentPage === 1"
                      class="p-1.5 text-gray-500 dark:text-zinc-400 bg-white dark:bg-zinc-800 border border-gray-300 dark:border-zinc-700 rounded-lg hover:bg-gray-50 dark:hover:bg-zinc-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
              </button>
              
              <div class="flex items-center space-x-1">
                <button v-for="page in suppliersTotalPages" :key="page" @click="suppliersFilters.currentPage = page"
                        :class="[
                          'px-2.5 py-1 text-xs font-medium rounded-lg transition-colors',
                          page === suppliersFilters.currentPage 
                            ? 'bg-blue-600 dark:bg-blue-500 text-white border border-blue-600 dark:border-blue-500' 
                            : 'text-gray-500 dark:text-zinc-400 bg-white dark:bg-zinc-800 border border-gray-300 dark:border-zinc-700 hover:bg-gray-50 dark:hover:bg-zinc-700'
                        ]">
                  {{ page }}
                </button>
              </div>
              
              <button @click="suppliersFilters.currentPage++" :disabled="suppliersFilters.currentPage === suppliersTotalPages"
                      class="p-1.5 text-gray-500 dark:text-zinc-400 bg-white dark:bg-zinc-800 border border-gray-300 dark:border-zinc-700 rounded-lg hover:bg-gray-50 dark:hover:bg-zinc-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
              </button>
              <button @click="suppliersFilters.currentPage = suppliersTotalPages" :disabled="suppliersFilters.currentPage === suppliersTotalPages"
                      class="p-1.5 text-gray-500 dark:text-zinc-400 bg-white dark:bg-zinc-800 border border-gray-300 dark:border-zinc-700 rounded-lg hover:bg-gray-50 dark:hover:bg-zinc-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
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
        <div class="bg-white/80 dark:bg-zinc-900/60  rounded-2xl p-5 shadow-lg shadow-gray-200/50 dark:shadow-black/30">
          <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h2 class="text-lg font-bold text-gray-900 dark:text-white">Centro de Alertas</h2>
                <p class="text-sm text-gray-500 dark:text-zinc-400">Monitoreo de salud del inventario</p>
            </div>
            
            <div class="flex items-center gap-3">
              <select 
                v-model="alertsFilters.severity"
                @change="loadAlertsData"
                class="px-3 py-2.5 text-sm rounded-xl border border-gray-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-gray-700 dark:text-zinc-200 font-medium focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400"
              >
                <option value="">Todas las alertas</option>
                <option value="critical">Críticas</option>
                <option value="warning">Advertencias</option>
                <option value="info">Información</option>
              </select>
              
              <button 
                @click="loadAlertsData"
                class="p-2.5 bg-white dark:bg-zinc-900 hover:bg-gray-50 dark:hover:bg-zinc-800 text-gray-600 dark:text-zinc-300 rounded-xl border border-gray-200 dark:border-zinc-700 shadow-sm transition-all hover:shadow-md"
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
          <div class="bg-white/80 dark:bg-zinc-900/80  rounded-2xl p-4 shadow-lg shadow-gray-200/50 dark:shadow-black/30 hover:shadow-xl transition-all duration-200">
            <div class="flex items-center space-x-3">
                <div class="w-11 h-11 bg-rose-50 dark:bg-rose-950/50 rounded-lg flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-rose-600 dark:text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-xs font-medium text-gray-500 dark:text-zinc-400 uppercase tracking-wide">Críticas</p>
                    <p class="text-xl font-bold text-gray-900 dark:text-white">{{ alertsData.summary.critical || 0 }}</p>
                </div>
            </div>
          </div>

          <!-- Advertencias -->
          <div class="bg-white/80 dark:bg-zinc-900/80  rounded-2xl p-4 shadow-lg shadow-gray-200/50 dark:shadow-black/30 hover:shadow-xl transition-all duration-200">
            <div class="flex items-center space-x-3">
                <div class="w-11 h-11 bg-amber-50 dark:bg-amber-950/50 rounded-lg flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-xs font-medium text-gray-500 dark:text-zinc-400 uppercase tracking-wide">Advertencias</p>
                    <p class="text-xl font-bold text-gray-900 dark:text-white">{{ alertsData.summary.warning || 0 }}</p>
                </div>
            </div>
          </div>

          <!-- Información -->
          <div class="bg-white/80 dark:bg-zinc-900/80  rounded-2xl p-4 shadow-lg shadow-gray-200/50 dark:shadow-black/30 hover:shadow-xl transition-all duration-200">
            <div class="flex items-center space-x-3">
                <div class="w-11 h-11 bg-sky-50 dark:bg-sky-950/50 rounded-lg flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-sky-600 dark:text-sky-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-xs font-medium text-gray-500 dark:text-zinc-400 uppercase tracking-wide">Información</p>
                    <p class="text-xl font-bold text-gray-900 dark:text-white">{{ alertsData.summary.info || 0 }}</p>
                </div>
            </div>
          </div>

          <!-- Total -->
          <div class="bg-white/80 dark:bg-zinc-900/80  rounded-2xl p-4 shadow-lg shadow-gray-200/50 dark:shadow-black/30 hover:shadow-xl transition-all duration-200">
            <div class="flex items-center space-x-3">
                <div class="w-11 h-11 bg-slate-50 dark:bg-slate-950/50 rounded-lg flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-slate-600 dark:text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                    </svg>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-xs font-medium text-gray-500 dark:text-zinc-400 uppercase tracking-wide">Total Alertas</p>
                    <p class="text-xl font-bold text-gray-900 dark:text-white">{{ alertsData.summary.total_alerts || 0 }}</p>
                </div>
            </div>
          </div>
        </div>

        <!-- Alertas del Sistema - DISEÑO TECHNICAL LIST -->
        <div v-if="alertsData && alertsData.alerts?.length > 0" class="bg-white/90 dark:bg-zinc-900/90  rounded-2xl shadow-lg shadow-gray-200/50 dark:shadow-black/30 overflow-hidden">
          <div class="px-6 py-3.5 flex items-center justify-between">
            <div>
              <h3 class="text-base font-bold text-gray-900 dark:text-white">Centro de Alertas</h3>
              <p class="text-xs text-gray-500 dark:text-zinc-400 mt-0.5">{{ alertsData.alerts.length }} notificaciones agrupadas</p>
            </div>
            <div class="flex items-center space-x-4">
              <button
                @click="markAllAlertsAsReviewed"
                class="text-xs font-medium text-gray-500 dark:text-zinc-400 hover:text-gray-900 dark:hover:text-white transition-colors duration-150"
              >
                Marcar todas leídas
              </button>
            </div>
          </div>
          
          <!-- Lista de Alertas Agrupadas - Technical List -->
          <div class="divide-y divide-gray-100 dark:divide-zinc-800/30">
            <div v-for="group in groupedAlerts" :key="group.category + group.severity" 
                 class="transition-colors duration-150">
              
              <!-- Header del Grupo (Acordeón Limpio) -->
              <div class="px-6 py-3.5 flex items-center justify-between hover:bg-gray-50/50 dark:hover:bg-zinc-800/30 transition-colors duration-150">
                <div 
                  @click="toggleAlertGroup(group.category, group.severity)"
                  class="flex items-center space-x-3 flex-1 cursor-pointer"
                >
                  <!-- Icono SVG Lineal 16px (sin fondo) -->
                  <svg :class="[
                    'w-4 h-4 flex-shrink-0',
                    group.severity === 'critical' ? 'text-red-600 dark:text-red-400' :
                    group.severity === 'warning' ? 'text-amber-600 dark:text-amber-400' :
                    'text-blue-600 dark:text-blue-400'
                  ]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path v-if="group.severity === 'critical'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    <path v-else-if="group.severity === 'warning'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                  </svg>
                  
                  <!-- Información del grupo -->
                  <div class="flex-1 min-w-0">
                    <div class="flex items-center space-x-2">
                      <h4 class="text-sm font-semibold text-gray-900 dark:text-white">
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
                    'w-3.5 h-3.5 text-gray-400 dark:text-zinc-500 transition-transform duration-200 flex-shrink-0',
                    expandedGroups.includes(group.category + group.severity) ? 'rotate-90' : ''
                  ]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                  </svg>
                </div>
                
                <!-- Acciones de texto sutiles -->
                <div class="ml-4 flex items-center space-x-3 flex-shrink-0">
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
                     class="bg-gray-50 dark:bg-zinc-800/50">
                  <div class="pl-12 pr-6 py-3 space-y-2">
                    <!-- Cada alerta individual -->
                    <div v-for="alert in group.alerts" :key="alert.id"
                         class="flex items-start justify-between py-2 border-b border-gray-100 dark:border-zinc-700 last:border-0 hover:bg-white/50 dark:hover:bg-zinc-900/50 px-3 -mx-3 rounded transition-colors duration-150">
                      
                      <!-- Contenido de la alerta -->
                      <div class="flex-1 min-w-0 pr-4">
                        <h5 class="text-sm font-medium text-gray-900 dark:text-white mb-0.5">{{ alert.title }}</h5>
                        <p class="text-xs text-gray-600 dark:text-zinc-400 leading-relaxed mb-1.5">{{ alert.message }}</p>
                        
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
          
          <!-- Paginación (si hay muchos grupos) -->
          <div v-if="groupedAlerts.length > 10" class="bg-gray-50 dark:bg-zinc-800/50 px-6 py-3 border-t border-gray-200 dark:border-zinc-800">
            <div class="flex items-center justify-between">
              <div class="text-sm text-gray-700 dark:text-zinc-300">
                {{ groupedAlerts.length }} grupos de alertas
              </div>
              <button
                @click="loadAlertsData"
                class="px-3 py-1.5 bg-white dark:bg-zinc-900 hover:bg-gray-50 dark:hover:bg-zinc-800 text-gray-700 dark:text-zinc-200 text-xs font-semibold rounded-xl border border-gray-300 dark:border-zinc-700 transition-all duration-200"
              >
                <i class="fas fa-sync-alt mr-1.5"></i>
                Actualizar
              </button>
            </div>
          </div>
        </div>

        <!-- Sin alertas -->
        <div v-else-if="alertsData && alertsData.alerts?.length === 0" 
             class="bg-white/80 dark:bg-zinc-900/80  rounded-lg shadow-lg shadow-gray-200/50 dark:shadow-black/30 p-12 text-center">
          <div class="w-16 h-16 bg-green-100 dark:bg-green-950 rounded-full flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
          </div>
          <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">¡Todo en orden!</h3>
          <p class="text-gray-600 dark:text-zinc-400">No hay alertas activas en este momento.</p>
        </div>
      </div>

      <!-- Vista de Predicciones -->
      <div v-if="activeSection === 'predictions'" class="space-y-4">
        
        <!-- Filtros Ejecutivos de Predicciones -->
        <div class="bg-white/80 dark:bg-zinc-900/60  rounded-2xl shadow-lg shadow-gray-200/50 dark:shadow-black/30 p-4">
          <div class="flex flex-wrap items-center gap-3 justify-between">
            <div class="flex items-center gap-3">
              <div class="flex items-center gap-2">
                <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
                <span class="text-xs font-semibold text-gray-700 dark:text-zinc-300">Horizonte de Pronóstico:</span>
              </div>
              <select 
                v-model="predictionsFilters.forecastDays"
                @change="loadPredictionsData"
                class="pl-3 pr-8 py-2 text-sm border border-gray-300 dark:border-zinc-700 rounded-xl focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent bg-white dark:bg-zinc-800 text-gray-700 dark:text-zinc-200 font-medium"
              >
                <option :value="7">7 días</option>
                <option :value="14">14 días</option>
                <option :value="30">30 días</option>
                <option :value="60">60 días</option>
                <option :value="90">90 días</option>
              </select>
              
              <!-- Toggle para productos saludables -->
              <div class="ml-4 flex items-center space-x-2 px-3 py-2 bg-gray-50 dark:bg-zinc-800 rounded-xl border border-gray-200 dark:border-zinc-700">
                <input 
                  type="checkbox" 
                  id="showHealthy"
                  v-model="predictionsFilters.showHealthy"
                  class="w-4 h-4 text-slate-600 dark:text-slate-400 rounded focus:ring-2 focus:ring-slate-500 dark:focus:ring-slate-400"
                >
                <label for="showHealthy" class="text-xs font-semibold text-gray-700 dark:text-zinc-300 cursor-pointer">
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
              class="px-4 py-2 bg-slate-900 dark:bg-slate-700 hover:bg-black dark:hover:bg-slate-600 text-white text-sm font-semibold rounded-xl shadow-lg shadow-slate-400/40 dark:shadow-slate-900/50 transition-all duration-200 flex items-center space-x-2"
            >
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
              </svg>
              <span>Actualizar</span>
            </button>
          </div>
        </div>

        <!-- Análisis de Tendencias - Ejecutivo Minimalista -->
        <div v-if="predictionsData" class="bg-white/90 dark:bg-zinc-900/90  rounded-2xl shadow-lg shadow-gray-200/50 dark:shadow-black/30 overflow-hidden">
          <div class="px-6 py-4 bg-gradient-to-r from-slate-50/80 to-blue-50/30 dark:from-zinc-800/50 dark:to-blue-950/30">
            <div class="flex items-center justify-between">
              <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-gradient-to-br from-blue-600 to-indigo-600 dark:from-blue-500 dark:to-indigo-500 rounded-xl flex items-center justify-center shadow-sm">
                  <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"></path>
                  </svg>
                </div>
                <div>
                  <h3 class="text-base font-bold text-gray-900 dark:text-white">Análisis de Tendencias IA</h3>
                  <p class="text-xs text-gray-600 dark:text-zinc-400 mt-0.5">Predicciones basadas en Machine Learning</p>
                </div>
              </div>
              <span class="px-2.5 py-1 bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-950 dark:to-indigo-950 text-blue-700 dark:text-blue-400 text-xs font-semibold rounded-xl border border-blue-100 dark:border-blue-900">
                <svg class="w-3 h-3 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                </svg>
                IA
              </span>
            </div>
          </div>
          
          <!-- Tarjetas de Tendencias - Grid 3 Columnas -->
          <div class="grid grid-cols-1 md:grid-cols-3 gap-4 p-6">
            
            <!-- Tendencia Ventas -->
            <div class="bg-white dark:bg-zinc-900/80 rounded-xl p-4 border border-gray-200 dark:border-white/5 hover:border-gray-300 dark:hover:border-white/10 hover:shadow-lg dark:shadow-black/50 transition-all duration-200">
              <div class="flex items-center justify-between mb-3">
                <div class="flex items-center space-x-2">
                  <div class="w-8 h-8 bg-gray-100 dark:bg-zinc-800/50 border border-gray-200 dark:border-white/5 rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                  </div>
                  <h3 class="text-sm font-bold text-gray-900 dark:text-white">Ventas</h3>
                </div>
                <div class="p-1.5 bg-gray-50 dark:bg-zinc-800 rounded-lg">
                  <svg v-if="predictionsData.trend_analysis.sales.trend === 'positive'" class="w-4 h-4 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                  </svg>
                  <svg v-else class="w-4 h-4 text-rose-600 dark:text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0v-8m0 8l-8-8-4 4-6-6"></path>
                  </svg>
                </div>
              </div>
              <div class="space-y-2 text-xs">
                <div class="flex justify-between items-center">
                  <span class="text-gray-600 dark:text-zinc-400 font-medium">Actual:</span>
                  <span class="font-bold text-gray-900 dark:text-white">{{ formatCurrency(predictionsData.trend_analysis.sales.current) }}</span>
                </div>
                <div class="flex justify-between items-center">
                  <span class="text-gray-500 dark:text-zinc-500">Anterior:</span>
                  <span class="font-semibold text-gray-600 dark:text-zinc-400">{{ formatCurrency(predictionsData.trend_analysis.sales.previous) }}</span>
                </div>
                <div class="pt-2 mt-2 border-t border-gray-200 dark:border-zinc-700 flex justify-between items-center">
                  <span class="text-gray-700 dark:text-zinc-300 font-semibold">Variación:</span>
                  <span :class="[
                    'text-sm font-bold px-2 py-0.5 rounded-lg',
                    predictionsData.trend_analysis.sales.growth_percentage >= 0 
                      ? 'bg-emerald-50 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-400' 
                      : 'bg-rose-50 dark:bg-rose-950 text-rose-700 dark:text-rose-400'
                  ]">
                    {{ predictionsData.trend_analysis.sales.growth_percentage >= 0 ? '+' : '' }}{{ predictionsData.trend_analysis.sales.growth_percentage }}%
                  </span>
                </div>
              </div>
            </div>

          <!-- Tendencia Transacciones -->
          <div class="bg-white dark:bg-zinc-900/80 rounded-xl p-4 border border-gray-200 dark:border-white/5 hover:border-gray-300 dark:hover:border-white/10 hover:shadow-lg dark:shadow-black/50 transition-all duration-200">
            <div class="flex items-center justify-between mb-3">
              <div class="flex items-center space-x-2">
                <div class="w-8 h-8 bg-sky-50 dark:bg-sky-950 rounded-lg flex items-center justify-center">
                  <svg class="w-4 h-4 text-sky-600 dark:text-sky-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                  </svg>
                </div>
                <h3 class="text-sm font-bold text-gray-900 dark:text-white">Transacciones</h3>
              </div>
              <div class="p-1.5 bg-gray-50 dark:bg-zinc-800 rounded-lg">
                <svg v-if="predictionsData.trend_analysis.transactions.trend === 'positive'" class="w-4 h-4 text-sky-600 dark:text-sky-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                </svg>
                <svg v-else class="w-4 h-4 text-rose-600 dark:text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0v-8m0 8l-8-8-4 4-6-6"></path>
                </svg>
              </div>
            </div>
            <div class="space-y-2 text-xs">
              <div class="flex justify-between items-center">
                <span class="text-gray-600 dark:text-zinc-400 font-medium">Actual:</span>
                <span class="font-bold text-gray-900 dark:text-white">{{ formatNumber(predictionsData.trend_analysis.transactions.current) }}</span>
              </div>
              <div class="flex justify-between items-center">
                <span class="text-gray-500 dark:text-zinc-500">Anterior:</span>
                <span class="font-semibold text-gray-600 dark:text-zinc-400">{{ formatNumber(predictionsData.trend_analysis.transactions.previous) }}</span>
              </div>
              <div class="pt-2 mt-2 border-t border-gray-200 dark:border-zinc-700 flex justify-between items-center">
                <span class="text-gray-700 dark:text-zinc-300 font-semibold">Variación:</span>
                <span :class="[
                  'text-sm font-bold px-2 py-0.5 rounded-lg',
                  predictionsData.trend_analysis.transactions.growth_percentage >= 0 
                    ? 'bg-sky-50 dark:bg-sky-950 text-sky-700 dark:text-sky-400' 
                    : 'bg-rose-50 dark:bg-rose-950 text-rose-700 dark:text-rose-400'
                ]">
                  {{ predictionsData.trend_analysis.transactions.growth_percentage >= 0 ? '+' : '' }}{{ predictionsData.trend_analysis.transactions.growth_percentage }}%
                </span>
              </div>
            </div>
          </div>

          <!-- Tendencia Ticket Promedio -->
          <div class="bg-white dark:bg-zinc-900/80 rounded-xl p-4 border border-gray-200 dark:border-white/5 hover:border-gray-300 dark:hover:border-white/10 hover:shadow-lg dark:shadow-black/50 transition-all duration-200">
            <div class="flex items-center justify-between mb-3">
              <div class="flex items-center space-x-2">
                <div class="w-8 h-8 bg-purple-50 dark:bg-purple-950 rounded-lg flex items-center justify-center">
                  <svg class="w-4 h-4 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"></path>
                  </svg>
                </div>
                <h3 class="text-sm font-bold text-gray-900 dark:text-white">Ticket Promedio</h3>
              </div>
              <div class="p-1.5 bg-gray-50 dark:bg-zinc-800 rounded-lg">
                <svg v-if="predictionsData.trend_analysis.average_ticket.trend === 'positive'" class="w-4 h-4 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                </svg>
                <svg v-else class="w-4 h-4 text-rose-600 dark:text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0v-8m0 8l-8-8-4 4-6-6"></path>
                </svg>
              </div>
            </div>
            <div class="space-y-2 text-xs">
              <div class="flex justify-between items-center">
                <span class="text-gray-600 dark:text-zinc-400 font-medium">Actual:</span>
                <span class="font-bold text-gray-900 dark:text-white">{{ formatCurrency(predictionsData.trend_analysis.average_ticket.current) }}</span>
              </div>
              <div class="flex justify-between items-center">
                <span class="text-gray-500 dark:text-zinc-500">Anterior:</span>
                <span class="font-semibold text-gray-600 dark:text-zinc-400">{{ formatCurrency(predictionsData.trend_analysis.average_ticket.previous) }}</span>
              </div>
              <div class="pt-2 mt-2 border-t border-gray-200 dark:border-zinc-700 flex justify-between items-center">
                <span class="text-gray-700 dark:text-zinc-300 font-semibold">Variación:</span>
                <span :class="[
                  'text-sm font-bold px-2 py-0.5 rounded-lg',
                  predictionsData.trend_analysis.average_ticket.growth_percentage >= 0 
                    ? 'bg-purple-50 dark:bg-purple-950 text-purple-700 dark:text-purple-400' 
                    : 'bg-rose-50 dark:bg-rose-950 text-rose-700 dark:text-rose-400'
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
             class="bg-white/90 dark:bg-zinc-900/90  rounded-2xl shadow-lg shadow-gray-200/50 dark:shadow-black/30 overflow-hidden">
          <div class="px-6 py-4 bg-transparent">
            <div class="flex items-center justify-between">
              <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-gradient-to-br from-slate-600 to-slate-700 dark:from-slate-500 dark:to-slate-600 rounded-xl flex items-center justify-center shadow-sm">
                  <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                  </svg>
                </div>
                <div>
                  <h3 class="text-base font-bold text-gray-900 dark:text-white">Análisis de Inventario</h3>
                  <p class="text-xs text-gray-600 dark:text-zinc-400 mt-0.5">Proyección de agotamiento de stock</p>
                </div>
              </div>
              <div class="flex items-center space-x-3">
                <div class="flex items-center space-x-2 text-xs text-gray-600 dark:text-zinc-400">
                  <span class="font-mono font-semibold text-red-600 dark:text-red-400">{{ criticalProductsCount }}</span>
                  <span>Críticos</span>
                  <span class="mx-2 text-gray-300 dark:text-zinc-600">•</span>
                  <span class="font-mono font-semibold text-amber-600 dark:text-amber-400">{{ warningProductsCount }}</span>
                  <span>Atención</span>
                  <span v-if="predictionsFilters.showHealthy" class="mx-2 text-gray-300 dark:text-zinc-600">•</span>
                  <span v-if="predictionsFilters.showHealthy" class="font-mono font-semibold text-green-600 dark:text-green-400">{{ healthyProductsCount }}</span>
                  <span v-if="predictionsFilters.showHealthy">Saludables</span>
                </div>
              </div>
            </div>
          </div>
          <div class="overflow-x-auto">
            <table class="min-w-full">
              <thead class="bg-white dark:bg-transparent border-b border-gray-200 dark:border-zinc-800">
                <tr>
                  <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 dark:text-zinc-400 uppercase tracking-wider">Producto</th>
                  <th class="px-4 py-3 text-center text-xs font-semibold text-gray-700 dark:text-zinc-400 uppercase tracking-wider">Stock Actual</th>
                  <th class="px-4 py-3 text-center text-xs font-semibold text-gray-700 dark:text-zinc-400 uppercase tracking-wider">Consumo Diario</th>
                  <th class="px-4 py-3 text-center text-xs font-semibold text-gray-700 dark:text-zinc-400 uppercase tracking-wider">Agotamiento</th>
                  <th class="px-4 py-3 text-center text-xs font-semibold text-gray-700 dark:text-zinc-400 uppercase tracking-wider">Acción</th>
                </tr>
              </thead>
              <tbody class="bg-white dark:bg-zinc-900">
                <tr v-for="item in paginatedStockDepletion" :key="item.product_id"
                    class="border-b border-gray-100 dark:border-zinc-800 hover:bg-gray-50 dark:hover:bg-zinc-800/30 transition-colors duration-150">
                  <td class="px-4 py-4">
                    <div>
                      <div class="text-sm font-semibold text-gray-900 dark:text-white mb-1.5">{{ item.product_name }}</div>
                      <!-- Barra de urgencia fina (4px) debajo del nombre -->
                      <div class="w-full h-1 bg-gray-100 dark:bg-zinc-800 rounded-full overflow-hidden">
                        <div 
                          :class="[
                            'h-full transition-all duration-500',
                            item.days_until_depletion < 7 ? 'bg-red-500 dark:bg-red-400' :
                            item.days_until_depletion < 30 ? 'bg-amber-500 dark:bg-amber-400' :
                            'bg-green-500 dark:bg-green-400'
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
                    <div class="font-mono text-base font-bold text-gray-900 dark:text-white">{{ item.current_stock }}</div>
                    <div class="text-xs text-gray-500 dark:text-zinc-400 mt-0.5">unidades</div>
                  </td>
                  <td class="px-4 py-4 text-center">
                    <div class="font-mono text-sm font-semibold text-gray-700 dark:text-zinc-300">{{ item.daily_average_sales }}</div>
                    <div class="text-xs text-gray-500 dark:text-zinc-400 mt-0.5">un/día</div>
                  </td>
                  <td class="px-4 py-4 text-center">
                    <!-- Fecha relativa limpia -->
                    <div v-if="item.days_until_depletion >= 30">
                      <div class="text-sm font-semibold text-green-700 dark:text-green-400">{{ getRelativeTimeText(item.days_until_depletion) }}</div>
                      <div class="text-xs text-gray-500 dark:text-zinc-400 mt-0.5">Stock amplio</div>
                    </div>
                    <div v-else>
                      <div :class="[
                        'text-sm font-bold mb-0.5',
                        item.days_until_depletion < 7 ? 'text-red-600 dark:text-red-400' : 'text-amber-600 dark:text-amber-400'
                      ]">
                        {{ getRelativeTimeText(item.days_until_depletion) }}
                      </div>
                      <div class="text-xs text-gray-500 dark:text-zinc-400 mt-0.5">
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
                        'px-3 py-1.5 text-xs font-semibold rounded-xl border-2 transition-all duration-150',
                        item.days_until_depletion < 7 
                          ? 'border-red-500 dark:border-red-400 text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-950' 
                          : 'border-amber-500 dark:border-amber-400 text-amber-600 dark:text-amber-400 hover:bg-amber-50 dark:hover:bg-amber-950'
                      ]"
                    >
                      Reponer
                    </button>
                    <!-- Sin acción para productos saludables -->
                    <span v-else class="text-xs text-gray-400 dark:text-zinc-500">—</span>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
          
          <!-- Paginación Stock Depletion -->
          <div v-if="predictionsData.stock_depletion.length > 10" class="bg-gray-50 dark:bg-zinc-800/50 px-6 py-3 border-t border-gray-200 dark:border-zinc-800">
            <div class="flex items-center justify-between">
              <div class="text-sm text-gray-700 dark:text-zinc-300">
                Mostrando {{ ((predictionsCurrentPage - 1) * 10) + 1 }} - {{ Math.min(predictionsCurrentPage * 10, predictionsData.stock_depletion.length) }} de {{ predictionsData.stock_depletion.length }} productos
              </div>
              <div class="flex items-center space-x-2">
                <button @click="predictionsCurrentPage--" 
                        :disabled="predictionsCurrentPage <= 1"
                        class="px-3 py-1 text-sm bg-white dark:bg-zinc-900 border border-gray-300 dark:border-zinc-700 text-gray-700 dark:text-zinc-200 rounded hover:bg-gray-50 dark:hover:bg-zinc-800 disabled:opacity-50 disabled:cursor-not-allowed">
                  Anterior
                </button>
                <span class="px-3 py-1 text-sm font-medium text-gray-900 dark:text-white">
                  {{ predictionsCurrentPage }} / {{ Math.ceil(predictionsData.stock_depletion.length / 10) }}
                </span>
                <button @click="predictionsCurrentPage++" 
                        :disabled="predictionsCurrentPage >= Math.ceil(predictionsData.stock_depletion.length / 10)"
                        class="px-3 py-1 text-sm bg-white dark:bg-zinc-900 border border-gray-300 dark:border-zinc-700 text-gray-700 dark:text-zinc-200 rounded hover:bg-gray-50 dark:hover:bg-zinc-800 disabled:opacity-50 disabled:cursor-not-allowed">
                  Siguiente
                </button>
              </div>
            </div>
          </div>
        </div>
        
        <!-- Mensaje "¡Todo bajo control!" cuando NO hay productos críticos ni de atención -->
        <div v-else-if="predictionsData && criticalProductsCount === 0 && warningProductsCount === 0"
             class="bg-gradient-to-br from-green-50 to-emerald-50 dark:from-green-950/30 dark:to-emerald-950/30 rounded-2xl shadow-sm dark:shadow-black/50 border-2 border-green-200 dark:border-green-900 p-12 text-center">
          <div class="flex flex-col items-center space-y-4">
            <div class="w-20 h-20 bg-green-100 dark:bg-green-950 rounded-full flex items-center justify-center animate-bounce">
              <svg class="w-10 h-10 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
              </svg>
            </div>
            <div>
              <h3 class="text-2xl font-bold text-green-900 dark:text-green-400 mb-2">¡Todo bajo control!</h3>
              <p class="text-green-700 dark:text-green-400/80 text-sm max-w-md mx-auto">
                No hay productos críticos ni en estado de atención. Tu inventario está en excelente estado.
              </p>
            </div>
            <div class="flex items-center space-x-4 mt-4 text-sm">
              <div class="flex items-center space-x-2 text-green-700 dark:text-green-400">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                </svg>
                <span class="font-semibold">{{ healthyProductsCount }} productos saludables</span>
              </div>
              <button
                @click="predictionsFilters.showHealthy = true"
                class="px-4 py-2 bg-green-600 dark:bg-green-700 hover:bg-green-700 dark:hover:bg-green-600 text-white text-xs font-bold rounded-lg transition-all duration-200 shadow-md hover:shadow-lg"
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

        <!-- Recomendaciones de Compra con IA - DISEÑO URGENTE PROFESIONAL -->
        <div v-if="predictionsData && predictionsData.purchase_recommendations?.length > 0" 
             class="bg-white dark:bg-zinc-900 rounded-2xl shadow-sm dark:shadow-black/50 border-2 border-red-200 dark:border-red-900 overflow-hidden">
          <!-- Header urgente pero profesional -->
          <div class="px-6 py-4 border-b border-red-200 dark:border-red-900 bg-gradient-to-r from-red-50 to-rose-50 dark:from-red-950/30 dark:to-rose-950/30">
            <div class="flex items-center justify-between">
              <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-red-600 dark:bg-red-700 rounded-xl flex items-center justify-center shadow-sm">
                  <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                  </svg>
                </div>
                <div>
                  <h3 class="text-base font-bold text-red-900 dark:text-red-400">Recomendaciones de Compra</h3>
                  <p class="text-xs text-red-700 dark:text-red-400/80 mt-0.5">Productos que requieren reabastecimiento</p>
                </div>
              </div>
              <div class="flex items-center space-x-2">
                <span class="px-2.5 py-1 bg-red-600 dark:bg-red-700 text-white text-xs font-bold rounded-lg">
                  {{ predictionsData.purchase_recommendations.length }}
                </span>
                <span class="text-xs text-red-700 dark:text-red-400 font-semibold">urgentes</span>
              </div>
            </div>
          </div>
          
          <div class="overflow-x-auto">
            <table class="min-w-full">
              <thead class="bg-white dark:bg-transparent border-b border-red-100 dark:border-red-900">
                <tr>
                  <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 dark:text-zinc-400 uppercase tracking-wider">Producto</th>
                  <th class="px-4 py-3 text-center text-xs font-semibold text-gray-700 dark:text-zinc-400 uppercase tracking-wider">Stock</th>
                  <th class="px-4 py-3 text-center text-xs font-semibold text-gray-700 dark:text-zinc-400 uppercase tracking-wider">Comprar</th>
                  <th class="px-4 py-3 text-center text-xs font-semibold text-gray-700 dark:text-zinc-400 uppercase tracking-wider">Inversión</th>
                  <th class="px-4 py-3 text-center text-xs font-semibold text-gray-700 dark:text-zinc-400 uppercase tracking-wider">Prioridad</th>
                  <th class="px-4 py-3 text-center text-xs font-semibold text-gray-700 dark:text-zinc-400 uppercase tracking-wider">Acción</th>
                </tr>
              </thead>
              <tbody class="bg-white dark:bg-zinc-900">
                <tr v-for="item in paginatedPurchaseRecommendations" :key="item.product_id"
                    class="border-b border-gray-100 dark:border-zinc-800 hover:bg-red-50/30 dark:hover:bg-red-950/20 transition-colors duration-150">
                  <td class="px-4 py-4">
                    <div>
                      <div class="text-sm font-semibold text-gray-900 dark:text-white">{{ item.product_name }}</div>
                      <div class="text-xs text-gray-500 dark:text-zinc-400 mt-1">
                        <span class="font-mono">{{ item.daily_demand }}</span> un/día
                      </div>
                    </div>
                  </td>
                  <td class="px-4 py-4 text-center">
                    <div class="font-mono text-base font-bold" :class="[
                      item.current_stock <= 10 ? 'text-red-600 dark:text-red-400' : 'text-gray-900 dark:text-white'
                    ]">{{ item.current_stock }}</div>
                    <div class="text-xs text-gray-500 dark:text-zinc-400 mt-0.5">unidades</div>
                  </td>
                  <td class="px-4 py-4 text-center">
                    <div class="font-mono text-lg font-bold text-red-600 dark:text-red-400">{{ item.recommended_purchase }}</div>
                    <div class="text-xs text-gray-500 dark:text-zinc-400 mt-0.5">unidades</div>
                  </td>
                  <td class="px-4 py-4 text-center">
                    <div class="font-mono text-base font-bold text-gray-900 dark:text-white">{{ formatCurrency(item.estimated_cost) }}</div>
                    <div class="text-xs text-gray-500 dark:text-zinc-400 mt-0.5">estimado</div>
                  </td>
                  <td class="px-4 py-4 text-center">
                    <span class="text-xs font-semibold" :class="[
                      item.priority === 'critical' ? 'text-red-600 dark:text-red-400' :
                      item.priority === 'high' ? 'text-amber-600 dark:text-amber-400' :
                      'text-gray-600 dark:text-zinc-400'
                    ]">
                      {{ item.priority === 'critical' ? 'Urgente' : 
                         item.priority === 'high' ? 'Alta' : 
                         item.priority === 'medium' ? 'Media' : 'Baja' }}
                    </span>
                  </td>
                  <td class="px-4 py-4 text-center">
                    <button
                      @click="createPurchaseOrder(item)"
                      class="px-3 py-1.5 bg-red-600 dark:bg-red-700 hover:bg-red-700 dark:hover:bg-red-600 text-white text-xs font-semibold rounded-lg transition-all duration-150"
                    >
                      Reponer
                    </button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
          
          <!-- Paginación Recomendaciones -->
          <div v-if="predictionsData.purchase_recommendations.length > 10" class="bg-gray-50 dark:bg-zinc-800/50 px-6 py-3 border-t border-gray-200 dark:border-zinc-800">
            <div class="flex items-center justify-between">
              <div class="text-sm text-gray-700 dark:text-zinc-300">
                Mostrando {{ ((predictionsCurrentPage - 1) * 10) + 1 }} - {{ Math.min(predictionsCurrentPage * 10, predictionsData.purchase_recommendations.length) }} de {{ predictionsData.purchase_recommendations.length }} recomendaciones
              </div>
              <div class="flex items-center space-x-2">
                <button @click="predictionsCurrentPage--" 
                        :disabled="predictionsCurrentPage <= 1"
                        class="px-3 py-1 text-sm bg-white dark:bg-zinc-900 border border-gray-300 dark:border-zinc-700 text-gray-700 dark:text-zinc-200 rounded hover:bg-gray-50 dark:hover:bg-zinc-800 disabled:opacity-50 disabled:cursor-not-allowed">
                  Anterior
                </button>
                <span class="px-3 py-1 text-sm font-medium text-gray-900 dark:text-white">
                  {{ predictionsCurrentPage }} / {{ Math.ceil(predictionsData.purchase_recommendations.length / 10) }}
                </span>
                <button @click="predictionsCurrentPage++" 
                        :disabled="predictionsCurrentPage >= Math.ceil(predictionsData.purchase_recommendations.length / 10)"
                        class="px-3 py-1 text-sm bg-white dark:bg-zinc-900 border border-gray-300 dark:border-zinc-700 text-gray-700 dark:text-zinc-200 rounded hover:bg-gray-50 dark:hover:bg-zinc-800 disabled:opacity-50 disabled:cursor-not-allowed">
                  Siguiente
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Pronóstico de Ventas con Machine Learning - Ejecutivo -->
        <div v-if="predictionsData && predictionsData.sales_forecast?.length > 0" 
             class="bg-white/90 dark:bg-zinc-900/90  rounded-2xl shadow-lg shadow-gray-200/50 dark:shadow-black/30 overflow-hidden">
          <div class="px-6 py-4 bg-gradient-to-r from-emerald-50/80 to-green-50/50 dark:from-emerald-950/30 dark:to-green-950/30">
            <div class="flex items-center justify-between">
              <div class="flex items-center space-x-3">
                <div class="p-2.5 bg-white/60 dark:bg-emerald-900/50  rounded-xl shadow-sm">
                  <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"></path>
                  </svg>
                </div>
                <div>
                  <h3 class="text-base font-bold text-gray-900 dark:text-white">Pronóstico de Ventas con Machine Learning</h3>
                  <p class="text-xs text-gray-600 dark:text-zinc-400 mt-0.5">Proyección para los próximos {{ predictionsFilters.forecastDays }} días</p>
                </div>
              </div>
              <span class="px-2.5 py-1 bg-emerald-100/70 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-400 text-xs font-semibold rounded-xl">
                {{ predictionsFilters.forecastDays }} días
              </span>
            </div>
          </div>
          <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200/50 dark:divide-zinc-800/50">
              <thead class="bg-gray-50/50 dark:bg-transparent">
                <tr>
                  <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 dark:text-zinc-400 uppercase tracking-wide">Producto</th>
                  <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 dark:text-zinc-400 uppercase tracking-wide">Ventas Históricas</th>
                  <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 dark:text-zinc-400 uppercase tracking-wide">Pronóstico IA</th>
                  <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 dark:text-zinc-400 uppercase tracking-wide">Tendencia</th>
                  <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 dark:text-zinc-400 uppercase tracking-wide">Confianza</th>
                </tr>
              </thead>
              <tbody class="bg-white dark:bg-zinc-900 divide-y divide-gray-200 dark:divide-zinc-800">
                <tr v-for="item in paginatedSalesForecast" :key="item.product_id"
                    class="hover:bg-gray-50 dark:hover:bg-zinc-800/30 transition-colors">
                  <td class="px-4 py-3">
                    <div class="text-sm font-semibold text-gray-900 dark:text-white">{{ item.product_name }}</div>
                    <div class="text-xs text-gray-500 dark:text-zinc-400 mt-0.5">
                      <svg class="w-3 h-3 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                      </svg>{{ item.transactions }} transacciones
                    </div>
                  </td>
                  <td class="px-4 py-3">
                    <span class="text-sm font-semibold text-gray-700 dark:text-zinc-200">{{ item.historical_sales }}</span>
                    <span class="text-xs text-gray-500 dark:text-zinc-400 ml-1">unidades</span>
                  </td>
                  <td class="px-4 py-3">
                    <span class="inline-flex items-center px-2.5 py-1 bg-emerald-100 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-400 text-xs font-bold rounded-lg">
                      <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                      </svg>{{ Math.abs(item.forecast_sales) }} unidades
                    </span>
                  </td>
                  <td class="px-4 py-3">
                    <span :class="[
                      'inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-semibold',
                      item.trend === 'growing' ? 'bg-green-100 dark:bg-green-950 text-green-700 dark:text-green-400' :
                      item.trend === 'declining' ? 'bg-rose-100 dark:bg-rose-950 text-rose-700 dark:text-rose-400' :
                      'bg-gray-100 dark:bg-zinc-800 text-gray-700 dark:text-zinc-400'
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
                      'px-2.5 py-1 text-xs font-semibold rounded-full',
                      item.confidence === 'high' ? 'bg-emerald-100 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-400' :
                      'bg-amber-100 dark:bg-amber-950 text-amber-700 dark:text-amber-400'
                    ]">
                      {{ item.confidence === 'high' ? 'ALTA' : 'MEDIA' }}
                    </span>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
          
          <!-- Paginación Pronóstico -->
          <div v-if="predictionsData.sales_forecast.length > 10" class="bg-gray-50 dark:bg-zinc-800/50 px-6 py-3 border-t border-gray-200 dark:border-zinc-800">
            <div class="flex items-center justify-between">
              <div class="text-sm text-gray-700 dark:text-zinc-300">
                Mostrando {{ ((predictionsCurrentPage - 1) * 10) + 1 }} - {{ Math.min(predictionsCurrentPage * 10, predictionsData.sales_forecast.length) }} de {{ predictionsData.sales_forecast.length }} pronósticos
              </div>
              <div class="flex items-center space-x-2">
                <button @click="predictionsCurrentPage--" 
                        :disabled="predictionsCurrentPage <= 1"
                        class="px-3 py-1 text-sm bg-white dark:bg-zinc-900 border border-gray-300 dark:border-zinc-700 text-gray-700 dark:text-zinc-200 rounded hover:bg-gray-50 dark:hover:bg-zinc-800 disabled:opacity-50 disabled:cursor-not-allowed">
                  Anterior
                </button>
                <span class="px-3 py-1 text-sm font-medium text-gray-900 dark:text-white">
                  {{ predictionsCurrentPage }} / {{ Math.ceil(predictionsData.sales_forecast.length / 10) }}
                </span>
                <button @click="predictionsCurrentPage++" 
                        :disabled="predictionsCurrentPage >= Math.ceil(predictionsData.sales_forecast.length / 10)"
                        class="px-3 py-1 text-sm bg-white dark:bg-zinc-900 border border-gray-300 dark:border-zinc-700 text-gray-700 dark:text-zinc-200 rounded hover:bg-gray-50 dark:hover:bg-zinc-800 disabled:opacity-50 disabled:cursor-not-allowed">
                  Siguiente
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Otras secciones (placeholder para las restantes) -->
      <div v-if="!['overview', 'products', 'movements', 'customers', 'suppliers', 'alerts', 'predictions'].includes(activeSection)" class="bg-white rounded-lg shadow p-6">
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
import { API_CONFIG, apiCall } from '../services/api.js'
import { getInitials } from '../utils/avatarUtils.js'
import { appStore } from '../store/appStore'

export default {
  name: 'IntelligentInventoryView',
  components: {
    ToastNotifications
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
    const warehouses = ref([]) // 🏪 Lista de bodegas/tiendas
    const selectedWarehouse = ref('') // 🏪 Bodega seleccionada ('' = todas)
    const filters = reactive({
      category: '',
      supplier: '',
      search: '',
      currentPage: 1,
      itemsPerPage: 25
    })
    
    // 🏪 Computed para mostrar/ocultar selector de bodega
    const showWarehouseSelector = computed(() => {
      // Solo mostrar si:
      // 1. El plan es premium o enterprise
      const isPremiumOrEnterprise = appStore.tenantPlan === 'premium' || appStore.tenantPlan === 'enterprise'
      // 2. Hay más de 1 bodega activa
      const hasMultipleWarehouses = warehouses.value.length > 1
      
      return isPremiumOrEnterprise && hasMultipleWarehouses
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

    // 🏭 Función para formatear unidades de medida
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
      if (change > 0) return 'text-green-600'
      if (change < 0) return 'text-red-600'
      return 'text-gray-600'
    }

    const getRotationTooltip = (rotationClass) => {
      const tooltips = {
        'A': '⭐ Alta rotación: Se agota en menos de 30 días. Producto de alta demanda.',
        'B': '🔵 Rotación media: Se agota entre 31-90 días. Velocidad normal.',
        'C': '⚠️ Rotación lenta: Tardaría más de 90 días en agotarse. Revisar inventario.'
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
      console.log('Acción de alerta:', alert)
      if (alert.action_url) {
        // Aquí podrías navegar a una página específica o abrir un modal
        console.log('Navegando a:', alert.action_url)
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
            console.warn('⚠️ Respuesta sin success o data:', inventoryData)
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

    // 🏪 Cargar bodegas activas
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
        
        // 🏪 Agregar bodega si está seleccionada
        if (selectedWarehouse.value) {
          params.append('warehouse_id', selectedWarehouse.value)
          console.log('🏢 [IntelligentInventory] Filtrando por warehouse ID:', selectedWarehouse.value)
        } else {
          console.log('📍 [IntelligentInventory] Mostrando todas las sedes')
        }
        
        console.log('🔍 [IntelligentInventory] Parámetros de búsqueda:', Object.fromEntries(params))
        
        // Si es rango personalizado, agregar fechas
        if (selectedPeriod.value === 'custom' && customDateRange.start) {
          params.append('start_date', customDateRange.start)
          const endDate = customDateRange.end || customDateRange.start
          params.append('end_date', endDate)
        }
        
        const data = await apiCall(`/inventory/test/products?${params}`)
        console.log('📦 [IntelligentInventory] Respuesta del backend:', data)
        console.log('📊 [IntelligentInventory] Pagination recibida:', data?.data?.pagination)
        console.log('📋 [IntelligentInventory] Total productos:', data?.data?.products?.length)
        
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
              total_debt: 0
            },
            best_supplier: response.data.summary?.best_supplier || null
          }
          
          console.log('✅ Proveedores cargados:', suppliersData.value.suppliers.length)
        }
      } catch (error) {
        console.error('❌ Error cargando proveedores:', error)
        // Establecer datos vacíos en caso de error
        suppliersData.value = {
          suppliers: [],
          summary: {
            total_suppliers: 0,
            active_suppliers: 0,
            total_debt: 0
          },
          best_supplier: null
        }
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
          // 🏪 Cargar bodegas primero, luego productos
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

    // Métodos de utilidad para productos
    const getStockStatusClass = (status) => {
      switch (status) {
        case 'out': return 'bg-rose-50 dark:bg-rose-950 text-rose-700 dark:text-rose-400 border-rose-100 dark:border-rose-800'
        case 'low': return 'bg-amber-50 dark:bg-amber-950 text-amber-700 dark:text-amber-400 border-amber-100 dark:border-amber-800'
        default: return 'bg-emerald-50 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-400 border-emerald-100 dark:border-emerald-800'
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
      if (days === 999) return 'bg-gray-50 dark:bg-gray-950 text-gray-700 dark:text-gray-400 border-gray-100 dark:border-gray-800'
      if (days > 60) return 'bg-rose-50 dark:bg-rose-950 text-rose-700 dark:text-rose-400 border-rose-100 dark:border-rose-800'
      if (days > 30) return 'bg-amber-50 dark:bg-amber-950 text-amber-700 dark:text-amber-400 border-amber-100 dark:border-amber-800'
      return 'bg-emerald-50 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-400 border-emerald-100 dark:border-emerald-800'
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
      loadSuppliersData() // 🏭 Cargar proveedores
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
      
      // ✅ Usar summary del backend que ya viene filtrado por bodega
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
      warehouses, // 🏪 Lista de bodegas
      selectedWarehouse, // 🏪 Bodega seleccionada
      showWarehouseSelector, // 🏪 Mostrar selector (computed)
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
      isPremiumOrEnterprise, // 🔒 Plan detection para features premium
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
      getMeasurementUnitLabel, // 🏭 Formatear unidades de medida
      getRotationTooltip, // 💡 Tooltips para rotación ABC
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
      loadWarehouses, // 🏪 Cargar bodegas
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