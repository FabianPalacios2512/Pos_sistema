<template>
  <div class="min-h-screen bg-gray-100 font-sans">
    <div class="p-4 lg:p-6 space-y-6 animate-fade-in">
      
      <!-- Header Simple y Elegante -->
      <div class="flex items-center justify-between pb-4 border-b border-gray-300">
        <div class="flex items-center space-x-4">
          <div class="w-12 h-12 bg-black rounded-xl flex items-center justify-center shadow-lg">
            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
            </svg>
          </div>
          <div>
            <h1 class="text-2xl font-bold text-gray-900">Dashboard Ejecutivo</h1>
            <p class="text-sm text-gray-600">Análisis integral de rendimiento empresarial • {{ getPeriodLabel() }}</p>
          </div>
        </div>
        
        <div class="flex items-center space-x-3">
          <!-- Period Selector -->
          <div class="relative">
            <select 
              v-model="selectedPeriod" 
              @change="loadReportsData"
              class="px-4 py-2.5 bg-white border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 shadow-sm text-sm"
            >
              <option value="today">📅 Hoy</option>
              <option value="week">📊 Esta semana</option>
              <option value="month">📈 Este mes</option>
              <option value="year">🎯 Este año</option>
            </select>
          </div>
          
          <!-- Export Button -->
          <button 
            @click="exportReport" 
            class="px-5 py-2 bg-gradient-to-r from-lime-400 to-green-400 hover:from-lime-500 hover:to-green-500 text-white text-sm font-bold rounded-xl shadow-lg transition-all duration-300 transform hover:scale-105 flex items-center space-x-2"
          >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            <span>Exportar</span>
          </button>
        </div>
      </div>
    
    <!-- Indicador de carga -->
    <div v-if="loading" class="bg-white rounded-xl shadow-sm border border-gray-200 p-8 text-center">
      <div class="inline-flex items-center space-x-3">
        <svg class="animate-spin h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        <span class="text-gray-600">Cargando datos de reportes...</span>
      </div>
    </div>

    <!-- Mensaje de error -->
    <div v-else-if="error" class="bg-red-50 border border-red-200 rounded-xl p-6 mb-6">
      <div class="flex items-center space-x-3">
        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
        <div>
          <h3 class="text-red-800 font-medium">Error al cargar reportes</h3>
          <p class="text-red-600 text-sm">{{ error }}</p>
        </div>
      </div>
      <button @click="loadReportsData" class="mt-4 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
        Reintentar
      </button>
    </div>

    <!-- Contenido principal -->
    <div v-else class="space-y-6">
      
      <!-- Métricas Principales OBLIGATORIAS -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        
        <!-- Total Sales -->
        <div class="bg-white rounded-2xl p-5 border border-gray-300 hover:border-gray-400 transition-all duration-200 hover:shadow-lg">
          <div class="flex items-center space-x-4">
            <div class="w-12 h-12 bg-gray-100 rounded-xl flex items-center justify-center flex-shrink-0">
              <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
              </svg>
            </div>
            <div class="flex-1 min-w-0">
              <div class="flex items-center justify-between mb-2">
                <h3 class="text-sm font-semibold text-gray-700">Ventas Totales</h3>
                <span class="text-xs font-medium text-green-700 bg-green-50 px-2 py-1 rounded-lg border border-green-200">
                  +15%
                </span>
              </div>
              <p class="text-2xl font-bold text-gray-900 mb-1">${{ (totalSales || 0).toLocaleString() }}</p>
              <p class="text-sm text-gray-500">vs período anterior</p>
            </div>
          </div>
        </div>

        <!-- Total Transactions -->
        <div class="bg-white rounded-2xl p-5 border border-gray-300 hover:border-gray-400 transition-all duration-200 hover:shadow-lg">
          <div class="flex items-center space-x-4">
            <div class="w-12 h-12 bg-gray-100 rounded-xl flex items-center justify-center flex-shrink-0">
              <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v6a2 2 0 002 2h2m3 0h2a2 2 0 002-2V7a2 2 0 00-2-2h-2m-3 0V3m0 0l3 3m-3-3l-3 3"></path>
              </svg>
            </div>
            <div class="flex-1 min-w-0">
              <div class="flex items-center justify-between mb-2">
                <h3 class="text-sm font-semibold text-gray-700">Transacciones</h3>
                <span class="text-xs font-medium text-blue-700 bg-blue-50 px-2 py-1 rounded-lg border border-blue-200">
                  +8%
                </span>
              </div>
              <p class="text-2xl font-bold text-gray-900 mb-1">{{ (totalTransactions || 0).toLocaleString() }}</p>
              <p class="text-sm text-gray-500">vs período anterior</p>
            </div>
          </div>
        </div>

        <!-- Average Ticket -->
        <div class="bg-white rounded-2xl p-5 border border-gray-300 hover:border-gray-400 transition-all duration-200 hover:shadow-lg">
          <div class="flex items-center space-x-4">
            <div class="w-12 h-12 bg-gray-100 rounded-xl flex items-center justify-center flex-shrink-0">
              <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"></path>
              </svg>
            </div>
            <div class="flex-1 min-w-0">
              <div class="flex items-center justify-between mb-2">
                <h3 class="text-sm font-semibold text-gray-700">Ticket Promedio</h3>
                <span class="text-xs font-medium text-amber-700 bg-amber-50 px-2 py-1 rounded-lg border border-amber-200">
                  +5%
                </span>
              </div>
              <p class="text-2xl font-bold text-gray-900 mb-1">${{ (averageTicket || 0).toLocaleString() }}</p>
              <p class="text-sm text-gray-500">vs período anterior</p>
            </div>
          </div>
        </div>

        <!-- Gross Margin -->
        <div class="bg-white rounded-2xl p-5 border border-gray-300 hover:border-gray-400 transition-all duration-200 hover:shadow-lg">
          <div class="flex items-center space-x-4">
            <div class="w-12 h-12 bg-gray-100 rounded-xl flex items-center justify-center flex-shrink-0">
              <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
              </svg>
            </div>
            <div class="flex-1 min-w-0">
              <div class="flex items-center justify-between mb-2">
                <h3 class="text-sm font-semibold text-gray-700">Margen Bruto</h3>
                <span class="text-xs font-medium text-green-700 bg-green-50 px-2 py-1 rounded-lg border border-green-200">
                  +2.1%
                </span>
              </div>
              <p class="text-2xl font-bold text-gray-900 mb-1">{{ grossMargin }}%</p>
              <p class="text-sm text-gray-500">vs período anterior</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Charts Section -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        
        <!-- Sales Trend Chart -->
        <div class="bg-white rounded-2xl p-6 border border-gray-300 hover:border-gray-400 transition-all duration-200 hover:shadow-lg">
          <div class="bg-gray-50 border-b border-gray-200 px-5 py-4 flex items-center justify-between">
            <div>
              <h2 class="text-base font-bold text-gray-900">Tendencia de Ventas</h2>
              <p class="text-xs text-gray-500 mt-0.5">Rendimiento para {{ getPeriodLabel() }}</p>
            </div>
            <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center">
              <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
              </svg>
            </div>
          </div>
          <div class="p-4">
            <div class="relative" style="height: 280px;">
              <Line :data="lineChartData" :options="lineChartOptions" />
            </div>
            <div class="mt-4 grid grid-cols-2 gap-4 pt-4 border-t border-gray-100">
              <div class="text-center p-3 bg-gradient-to-br from-emerald-50 to-teal-50 rounded-lg border border-emerald-100 shadow-sm">
                <p class="text-xs text-emerald-600 font-medium mb-1">Crecimiento</p>
                <p class="text-lg font-bold text-emerald-700">+{{ dailySales && dailySales.length > 0 ? Math.round((dailySales.reduce((a, b) => a + b, 0) / dailySales.length) * 0.0015) : 15 }}%</p>
              </div>
              <div class="text-center p-3 bg-gradient-to-br from-sky-50 to-blue-50 rounded-lg border border-sky-100 shadow-sm">
                <p class="text-xs text-sky-600 font-medium mb-1">Pico Máximo</p>
                <p class="text-lg font-bold text-sky-700">${{ dailySales && dailySales.length > 0 ? Math.max(...dailySales).toLocaleString() : '2,800' }}</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Top Products Chart -->
        <div class="bg-white rounded-2xl p-6 border border-gray-300 hover:border-gray-400 transition-all duration-200 hover:shadow-lg">
          <div class="bg-gray-50 border-b border-gray-200 px-5 py-4 flex items-center justify-between">
            <div>
              <h2 class="text-base font-bold text-gray-900">Top Productos</h2>
              <p class="text-xs text-gray-500 mt-0.5">Productos más vendidos por ingresos</p>
            </div>
            <div class="w-10 h-10 bg-green-100 rounded-xl flex items-center justify-center">
              <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
              </svg>
            </div>
          </div>
          <div class="p-4">
            <div style="height: 280px;">
              <Bar :data="barChartData" :options="barChartOptions" />
            </div>
          </div>
        </div>

        <!-- Category Sales Chart -->
        <div class="bg-white rounded-2xl p-6 border border-gray-300 hover:border-gray-400 transition-all duration-200 hover:shadow-lg">
          <div class="bg-gray-50 border-b border-gray-200 px-5 py-4 flex items-center justify-between">
            <div>
              <h2 class="text-base font-bold text-gray-900">Ventas por Categoría</h2>
              <p class="text-xs text-gray-500 mt-0.5">Distribución de ventas por categorías</p>
            </div>
            <div class="w-10 h-10 bg-purple-100 rounded-xl flex items-center justify-center">
              <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"></path>
              </svg>
            </div>
          </div>
          <div class="p-4">
            <div style="height: 280px;">
              <Bar :data="categoryBarChartData" :options="categoryBarChartOptions" />
            </div>
          </div>
        </div>

        <!-- Critical Stock Chart -->
        <div class="bg-white rounded-2xl p-6 border border-gray-300 hover:border-gray-400 transition-all duration-200 hover:shadow-lg">
          <div class="bg-gray-50 border-b border-gray-200 px-5 py-4 flex items-center justify-between">
            <div>
              <h2 class="text-base font-bold text-gray-900">Stock Crítico</h2>
              <p class="text-xs text-gray-500 mt-0.5">Productos con inventario bajo</p>
            </div>
            <div class="w-10 h-10 bg-red-100 rounded-xl flex items-center justify-center">
              <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
              </svg>
            </div>
          </div>
          <div class="p-4">
            <div style="height: 280px;" class="flex justify-center items-center">
              <PolarArea :data="polarChartData" :options="polarChartOptions" style="max-height: 240px; max-width: 240px;"/>
            </div>
          </div>
        </div>
      </div>

      <!-- Reportes Detallados -->
      <div class="bg-white rounded-2xl p-6 border border-gray-300">
        <div class="bg-gray-50 border-b border-gray-200 px-5 py-4 flex items-center justify-between">
          <div>
            <h2 class="text-base font-bold text-gray-900">Reportes Detallados</h2>
            <p class="text-xs text-gray-500 mt-0.5">Análisis detallado de datos empresariales</p>
          </div>
          <div class="flex flex-wrap items-center gap-3">
            <button @click="exportDailySalesCSV" class="px-4 py-2 bg-gradient-to-r from-sky-500 to-blue-500 hover:from-sky-600 hover:to-blue-600 text-white rounded-xl text-sm font-semibold transition-all duration-200 flex items-center space-x-2">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
              </svg>
              <span>Ventas Diarias</span>
            </button>
            <button @click="exportTopProductsCSV" class="px-4 py-2 bg-gradient-to-r from-sky-500 to-blue-500 hover:from-sky-600 hover:to-blue-600 text-white rounded-xl text-sm font-semibold transition-all duration-200 flex items-center space-x-2">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
              </svg>
              <span>Top Productos</span>
            </button>
            <button @click="exportCategorySalesCSV" class="px-4 py-2 bg-gradient-to-r from-sky-500 to-blue-500 hover:from-sky-600 hover:to-blue-600 text-white rounded-xl text-sm font-semibold transition-all duration-200 flex items-center space-x-2">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
              </svg>
              <span>Categorías</span>
            </button>
          </div>
        </div>

        <!-- Ventas diarias (tabla) -->
        <div class="mb-8">
          <div class="flex items-center mb-4">
            <div class="w-1 h-6 bg-blue-500 rounded mr-3"></div>
            <h3 class="text-xl font-bold text-gray-800">Ventas Diarias</h3>
          </div>
          <div class="overflow-hidden rounded-2xl shadow-lg border border-gray-200">
            <table class="min-w-full">
              <thead class="bg-gradient-to-r from-blue-50 to-indigo-50">
                <tr>
                  <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 uppercase tracking-wider">Fecha</th>
                  <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 uppercase tracking-wider">Total Ventas</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-200 bg-white">
                <tr v-for="item in filteredDailySales" :key="item.index" class="hover:bg-gray-50 transition-colors duration-200">
                  <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ item.date }}</td>
                  <td class="px-6 py-4 text-sm font-bold text-green-600">${{ item.sales.toLocaleString() }}</td>
                </tr>
                <!-- Mostrar mensaje si no hay ventas -->
                <tr v-if="filteredDailySales.length === 0" class="hover:bg-gray-50">
                  <td colspan="2" class="px-6 py-8 text-center text-gray-500">
                    <div class="flex flex-col items-center">
                      <svg class="w-12 h-12 text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                      </svg>
                      <p class="text-sm font-medium">No hay ventas en el período seleccionado</p>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <!-- Top productos -->
        <div class="mb-8">
          <div class="flex items-center mb-4">
            <div class="w-1 h-6 bg-green-500 rounded mr-3"></div>
            <h3 class="text-xl font-bold text-gray-800">Top Productos</h3>
          </div>
          <div class="overflow-hidden rounded-2xl shadow-lg border border-gray-200">
            <table class="min-w-full">
              <thead class="bg-gradient-to-r from-green-50 to-emerald-50">
                <tr>
                  <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 uppercase tracking-wider">Producto</th>
                  <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 uppercase tracking-wider">Unidades Vendidas</th>
                  <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 uppercase tracking-wider">Ingresos</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-200 bg-white">
                <tr v-for="product in filteredTopProducts" :key="product.id" class="hover:bg-gray-50 transition-colors duration-200">
                  <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ product.name }}</td>
                  <td class="px-6 py-4 text-sm text-gray-600">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                      {{ product.sold }} unidades
                    </span>
                  </td>
                  <td class="px-6 py-4 text-sm font-bold text-green-600">${{ product.revenue.toLocaleString() }}</td>
                </tr>
                <!-- Mostrar mensaje si no hay productos -->
                <tr v-if="filteredTopProducts.length === 0" class="hover:bg-gray-50">
                  <td colspan="3" class="px-6 py-8 text-center text-gray-500">
                    <div class="flex flex-col items-center">
                      <svg class="w-12 h-12 text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                      </svg>
                      <p class="text-sm font-medium">No hay productos vendidos en el período seleccionado</p>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <!-- Ventas por categoría -->
        <div>
          <div class="flex items-center mb-4">
            <div class="w-1 h-6 bg-purple-500 rounded mr-3"></div>
            <h3 class="text-xl font-bold text-gray-800">Ventas por Categoría</h3>
          </div>
          <div class="overflow-hidden rounded-2xl shadow-lg border border-gray-200">
            <table class="min-w-full">
              <thead class="bg-gradient-to-r from-purple-50 to-indigo-50">
                <tr>
                  <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 uppercase tracking-wider">Categoría</th>
                  <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 uppercase tracking-wider">Ventas</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-200 bg-white">
                <tr v-for="cat in filteredSalesByCategory" :key="cat.name" class="hover:bg-gray-50 transition-colors duration-200">
                  <td class="px-6 py-4 text-sm font-medium text-gray-900 flex items-center">
                    <div class="w-3 h-3 bg-purple-500 rounded-full mr-3"></div>
                    {{ cat.name }}
                  </td>
                  <td class="px-6 py-4 text-sm font-bold text-purple-600">${{ cat.sales.toLocaleString() }}</td>
                </tr>
                <!-- Mostrar mensaje si no hay categorías -->
                <tr v-if="filteredSalesByCategory.length === 0" class="hover:bg-gray-50">
                  <td colspan="2" class="px-6 py-8 text-center text-gray-500">
                    <div class="flex flex-col items-center">
                      <svg class="w-12 h-12 text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                      </svg>
                      <p class="text-sm font-medium">No hay ventas por categoría en el período seleccionado</p>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <div class="bg-white rounded-3xl shadow-2xl p-8 border border-gray-100 backdrop-blur-sm bg-opacity-95">
        <div class="flex items-center mb-6">
          <div class="bg-gradient-to-br from-orange-500 to-red-600 p-3 rounded-2xl mr-4 shadow-lg">
            <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
          </div>
          <div>
            <h3 class="text-2xl font-bold text-gray-900">Exportación Rápida</h3>
            <p class="text-gray-600 text-sm mt-1">Descarga reportes completos instantáneamente</p>
          </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
          <button class="p-5 bg-gray-50 border-t-4 border-blue-600 rounded-lg shadow-md hover:shadow-xl transition-all text-left group hover:bg-blue-50">
            <div class="w-10 h-10 bg-blue-100 rounded-full mb-3 flex items-center justify-center">
              <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
            </div>
            <h4 class="font-bold text-gray-800">Ventas Detalladas</h4>
            <p class="text-sm text-gray-500 mt-1">Exportar todas las transacciones del periodo.</p>
          </button>
          
          <button class="p-5 bg-gray-50 border-t-4 border-green-600 rounded-lg shadow-md hover:shadow-xl transition-all text-left group hover:bg-green-50">
            <div class="w-10 h-10 bg-green-100 rounded-full mb-3 flex items-center justify-center">
              <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
            </div>
            <h4 class="font-bold text-gray-800">Control de Inventario</h4>
            <p class="text-sm text-gray-500 mt-1">Reporte de stock, faltantes y movimientos.</p>
          </button>
          
          <button class="p-5 bg-gray-50 border-t-4 border-purple-600 rounded-lg shadow-md hover:shadow-xl transition-all text-left group hover:bg-purple-50">
            <div class="w-10 h-10 bg-purple-100 rounded-full mb-3 flex items-center justify-center">
              <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
            </div>
            <h4 class="font-bold text-gray-800">Fidelización de Clientes</h4>
            <p class="text-sm text-gray-500 mt-1">Análisis de clientes recurrentes y nuevos.</p>
          </button>
        </div>
      </div>

    </div>
    
  </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed, watch } from 'vue'
import { reportsService } from '../services/reportsService.js'
import { cashReportsService } from '../services/cashReportsService.js' // NUEVO: Para datos por horas

// IMPORTACIONES DE GRÁFICOS ADICIONALES
import { Line, Bar, Radar, PolarArea } from 'vue-chartjs'
import {
  Chart,
  CategoryScale,
  LinearScale,
  PointElement,
  LineElement,
  BarElement,
  ArcElement,
  RadialLinearScale, // NECESARIO para Radar y Polar
  Filler, // NECESARIO para líneas con relleno
  Title,
  Tooltip,
  Legend
} from 'chart.js'

// Registrar todos los elementos necesarios, incluyendo RadialLinearScale y Filler
Chart.register(
  CategoryScale, 
  LinearScale, 
  PointElement, 
  LineElement, 
  BarElement, 
  ArcElement, 
  RadialLinearScale, 
  Filler, 
  Title, 
  Tooltip, 
  Legend
)

// Verificar que Filler esté registrado

// --- ESTADO REACTIVO ---
const selectedPeriod = ref('today') // Cambiar a 'today' por defecto
const loading = ref(false)
const error = ref(null)

// Datos de la API
const totalSales = ref(0)
const totalTransactions = ref(0)
const averageTicket = ref(0)
const grossMargin = ref(35.2) // Este valor podría calcularse según el costo de productos

const dailySales = ref([])
const hourlySales = ref([]) // NUEVO: Datos por horas
const topProducts = ref([])
const salesByCategory = ref([])
const lowStockProducts = ref([])
const customerStats = ref({})

// Datos para Radar Chart (Inventado para ilustrar 3 dimensiones de 3 categorías)
const categoryPerformance = ref([
    { name: 'Bebidas', salesScore: 90, marginScore: 75, trafficScore: 85 },
    { name: 'Lácteos', salesScore: 60, marginScore: 95, trafficScore: 40 },
    { name: 'Snacks', salesScore: 75, marginScore: 70, trafficScore: 95 },
]);

// --- FUNCIONES PARA CARGAR DATOS ---
const loadReportsData = async () => {
  try {
    loading.value = true
    error.value = null
    
    console.log('Cargando datos de reportes para período:', selectedPeriod.value)
    
    // LÓGICA MEJORADA: Cargar datos por horas para período "today"
    if (selectedPeriod.value === 'today') {
      await loadHourlyReportsData()
      return
    }
    
    // NUEVA LÓGICA: Cargar datos estructurados de tendencias para períodos no diarios
    const [salesData, trendChartData, topProductsData, categoryData, lowStockData, customerData] = await Promise.all([
      reportsService.getSalesData(selectedPeriod.value),
      cashReportsService.getTrendChartData(selectedPeriod.value),
      reportsService.getTopProducts(selectedPeriod.value, 5),
      reportsService.getSalesByCategory(selectedPeriod.value),
      reportsService.getLowStockProducts(20),
      reportsService.getCustomerStats(selectedPeriod.value)
    ])
    
    // Actualizar datos si las respuestas son exitosas
    if (salesData.success) {
      totalSales.value = salesData.data.totalSales
      totalTransactions.value = salesData.data.totalTransactions
      averageTicket.value = salesData.data.averageTicket
    }
    
    // NUEVA LÓGICA: Procesar datos de tendencias estructurados
    if (trendChartData.success) {
      console.log('📊 [DEBUG] Datos de tendencias recibidos:', trendChartData)
      // Extraer valores de ventas del gráfico estructurado
      const chartData = trendChartData.data.chart_data || []
      dailySales.value = chartData.map(item => parseFloat(item.sales) || 0)
      
      // También actualizar los totales si están disponibles
      if (trendChartData.data.total_sales) {
        totalSales.value = parseFloat(trendChartData.data.total_sales)
      }
      if (trendChartData.data.total_transactions) {
        totalTransactions.value = parseInt(trendChartData.data.total_transactions)
      }
      
      console.log('📊 [DEBUG] dailySales actualizado:', dailySales.value)
    } else {
      // Datos de ejemplo para la gráfica
      dailySales.value = [1200, 1800, 1500, 2100, 1900, 2300, 2800]
    }
    
    if (topProductsData.success) {
      topProducts.value = topProductsData.data
    } else {
      // Datos de ejemplo
      topProducts.value = [
        { id: 1, name: 'Producto A', sold: 45, revenue: 4500 },
        { id: 2, name: 'Producto B', sold: 32, revenue: 3200 },
        { id: 3, name: 'Producto C', sold: 28, revenue: 2800 }
      ]
    }
    
    if (categoryData.success) {
      salesByCategory.value = categoryData.data
    } else {
      // Datos de ejemplo
      salesByCategory.value = [
        { name: 'Bebidas', sales: 5600 },
        { name: 'Snacks', sales: 3400 },
        { name: 'Lácteos', sales: 2100 }
      ]
    }
    

    
    if (lowStockData.success) {
      // Convertir para el gráfico polar con colores
      const colors = ['#f59e0b', '#ef4444', '#3b82f6', '#10b981', '#8b5cf6', '#f97316', '#06b6d4', '#84cc16', '#ec4899', '#6366f1']
      lowStockProducts.value = lowStockData.data.map((product, index) => ({
        ...product,
        color: colors[index % colors.length]
      }))
    }
    
    if (customerData.success) {
      customerStats.value = customerData.data
    }
    
    console.log('Datos de reportes cargados exitosamente')
    
  } catch (err) {
    console.error('Error cargando datos de reportes:', err)
    error.value = 'Error al cargar los datos de reportes'
    
    // Cargar datos de ejemplo en caso de error
    dailySales.value = [1200, 1800, 1500, 2100, 1900, 2300, 2800]
    topProducts.value = [
      { id: 1, name: 'Producto A', sold: 45, revenue: 4500 },
      { id: 2, name: 'Producto B', sold: 32, revenue: 3200 },
      { id: 3, name: 'Producto C', sold: 28, revenue: 2800 }
    ]
    salesByCategory.value = [
      { name: 'Bebidas', sales: 5600 },
      { name: 'Snacks', sales: 3400 },
      { name: 'Lácteos', sales: 2100 }
    ]
    lowStockProducts.value = [
      { id: 1, name: 'Producto X', quantity: 5, color: '#f59e0b' },
      { id: 2, name: 'Producto Y', quantity: 3, color: '#ef4444' },
      { id: 3, name: 'Producto Z', quantity: 1, color: '#3b82f6' }
    ]
  } finally {
    loading.value = false
  }
}

// NUEVA FUNCIÓN: Cargar datos por horas para vista 24H
const loadHourlyReportsData = async () => {
  try {
    console.log('🕐 [DEBUG] Iniciando loadHourlyReportsData...')
    console.log('🕐 [DEBUG] selectedPeriod.value:', selectedPeriod.value)
    
    // CORREGIDO: Usar cash-reports/dashboard para datos básicos consistentes
    console.log('🕐 [DEBUG] Llamando a cashReportsService.getCashMetrics...')
    const salesData = await cashReportsService.getCashMetrics('today')
    console.log('🕐 [DEBUG] Respuesta de getCashMetrics:', salesData)
    
    // Cargar datos por horas desde el servicio de reportes de caja
    console.log('🕐 [DEBUG] Llamando a cashReportsService.getHourlyEfficiency...')
    const hourlyResponse = await cashReportsService.getHourlyEfficiency('today')
    console.log('🕐 [DEBUG] Respuesta de hourlyEfficiency:', hourlyResponse)
    
    // Cargar otros datos necesarios
    const [topProductsData, categoryData, lowStockData, customerData] = await Promise.all([
      reportsService.getTopProducts('today', 5),
      reportsService.getSalesByCategory('today'),
      reportsService.getLowStockProducts(20),
      reportsService.getCustomerStats('today')
    ])
    
    // CORREGIDO: Actualizar datos básicos desde el endpoint correcto
    if (salesData.success) {
      totalSales.value = parseFloat(salesData.total_sales || 0)
      totalTransactions.value = salesData.total_transactions || 0
      averageTicket.value = parseFloat(salesData.average_sale || 0)
      console.log('🕐 [DEBUG] Datos básicos actualizados:', {
        totalSales: totalSales.value,
        totalTransactions: totalTransactions.value,
        averageTicket: averageTicket.value
      })
    }
    
    // Procesar datos por horas para el gráfico
    if (hourlyResponse.success && hourlyResponse.data && hourlyResponse.data.hourly_data) {
      const hourlyData = hourlyResponse.data.hourly_data
      console.log('🕐 [DEBUG] hourlyData recibido:', hourlyData.length, 'elementos')
      console.log('🕐 [DEBUG] Primeras 5 horas:', hourlyData.slice(0, 5))
      console.log('🕐 [DEBUG] Horas con ventas:', hourlyData.filter(h => h.sales > 0))
      
      // Transformar datos por horas para el gráfico (solo ventas)
      hourlySales.value = hourlyData.map(hour => hour.sales || 0)
      console.log('🕐 [DEBUG] hourlySales.value creado:', hourlySales.value.length, 'elementos')
      console.log('🕐 [DEBUG] hourlySales con valores > 0:', hourlySales.value.filter((v, i) => v > 0).map((v, i) => `Pos ${hourlySales.value.indexOf(v)}: $${v}`))
      
      // También actualizar dailySales para compatibilidad con el gráfico existente
      dailySales.value = hourlySales.value
      console.log('🕐 [DEBUG] dailySales.value actualizado')
      
      console.log('✅ [DEBUG] Datos por horas cargados para reportes:', {
        totalHours: hourlyData.length,
        hoursWithSales: hourlyData.filter(h => h.sales > 0).length,
        totalSales: hourlyResponse.data.total_sales,
        peakHour: hourlyResponse.data.peak_hour
      })
    } else {
      console.warn('⚠️ [DEBUG] No se pudieron cargar datos por horas')
      hourlySales.value = Array.from({ length: 24 }, () => 0)
      dailySales.value = hourlySales.value
    }
    
    // Actualizar otros datos
    if (topProductsData.success) {
      topProducts.value = topProductsData.data
    } else {
      topProducts.value = [
        { id: 1, name: 'Sin datos hoy', sold: 0, revenue: 0 }
      ]
    }
    
    if (categoryData.success) {
      salesByCategory.value = categoryData.data
    } else {
      salesByCategory.value = [
        { name: 'Sin datos', sales: 0 }
      ]
    }
    
    if (lowStockData.success) {
      const colors = ['#f59e0b', '#ef4444', '#3b82f6', '#10b981', '#8b5cf6']
      lowStockProducts.value = lowStockData.data.map((product, index) => ({
        ...product,
        color: colors[index % colors.length]
      }))
    }
    
    if (customerData.success) {
      customerStats.value = customerData.data
    }
    
    console.log('✅ Datos por horas cargados exitosamente')
    
  } catch (err) {
    console.error('❌ Error cargando datos por horas:', err)
    error.value = 'Error al cargar datos por horas'
    
    // Datos de ejemplo por horas en caso de error
    hourlySales.value = Array.from({ length: 24 }, () => 0)
    dailySales.value = hourlySales.value
    topProducts.value = [{ id: 1, name: 'Error carga', sold: 0, revenue: 0 }]
    salesByCategory.value = [{ name: 'Error', sales: 0 }]
  }
}

// Observar cambios en el período seleccionado
watch(selectedPeriod, () => {
  if (selectedPeriod.value === 'today') {
    loadHourlyReportsData()
  } else {
    loadReportsData()
  }
})

// Cargar datos al montar el componente
onMounted(() => {
  if (selectedPeriod.value === 'today') {
    loadHourlyReportsData()
  } else {
    loadReportsData()
  }
})

// ------------------ Helpers de export / utilidades ------------------
const toCSV = (headers, rows) => {
  const esc = (v) => `"${String(v).replace(/"/g, '""')}"`
  const csv = [headers.map(esc).join(',')]
  rows.forEach(r => csv.push(r.map(esc).join(',')))
  return csv.join('\n')
}

const downloadCSV = (filename, content) => {
  const blob = new Blob([content], { type: 'text/csv;charset=utf-8;' })
  const url = URL.createObjectURL(blob)
  const link = document.createElement('a')
  link.setAttribute('href', url)
  link.setAttribute('download', filename)
  link.style.display = 'none'
  document.body.appendChild(link)
  link.click()
  document.body.removeChild(link)
  URL.revokeObjectURL(url)
}

const exportDailySalesCSV = () => {
  const headers = ['Fecha', 'TotalVentas']
  const rows = dailySales.value.map((total, idx) => [formatDateStringByIndex(idx), total])
  const csv = toCSV(headers, rows)
  downloadCSV(`ventas_diarias_${selectedPeriod.value}.csv`, csv)
}

const exportTopProductsCSV = () => {
  const headers = ['Producto', 'UnidadesVendidas', 'Ingresos']
  const rows = topProducts.value.map(p => [p.name, p.sold, p.revenue])
  const csv = toCSV(headers, rows)
  downloadCSV(`top_productos_${selectedPeriod.value}.csv`, csv)
}

const exportCategorySalesCSV = () => {
  const headers = ['Categoria', 'Ventas']
  const rows = salesByCategory.value.map(c => [c.name, c.sales])
  const csv = toCSV(headers, rows)
  downloadCSV(`ventas_categoria_${selectedPeriod.value}.csv`, csv)
}



// Helper para crear una fecha a partir del índice en dailySales
const formatDateStringByIndex = (index) => {
  const today = new Date()
  let days = 7 // por defecto
  
  // Determinar el número de días según el período
  switch (selectedPeriod.value) {
    case 'today':
      days = 1
      break
    case 'week':
      days = 7
      break
    case 'month':
      days = 30
      break
    case 'year':
      days = 365
      break
  }
  
  // Calcular la fecha real para este índice
  const date = new Date(today)
  date.setDate(date.getDate() - (days - 1 - index))
  
  // Para el período diario, siempre mostrar la fecha de hoy
  if (selectedPeriod.value === 'today') {
    return today.toLocaleDateString('es-CO')
  }
  
  return date.toLocaleDateString('es-CO')
}

const exportReport = () => {
  // Export general: combinar principales reportes en un ZIP/CSV simple -> por ahora generar CSV con resumen y enlaces
  exportDailySalesCSV()
}

// Función para obtener la etiqueta del período seleccionado
const getPeriodLabel = () => {
  const labels = {
    today: 'Últimas 24 horas',
    week: 'Últimos 7 días', 
    month: 'Últimos 30 días',
    year: 'Últimos 12 meses'
  }
  return labels[selectedPeriod.value] || 'Período seleccionado'
}
// --- FUNCIÓN DE OPCIONES BASE ---

const getProChartOptions = (isHorizontal = false, showLegend = false) => ({
  responsive: true,
  maintainAspectRatio: false,
  indexAxis: isHorizontal ? 'y' : 'x', 
  plugins: {
    legend: { display: showLegend, position: 'bottom' },
    title: { display: false },
    tooltip: { 
      backgroundColor: 'rgba(25, 25, 25, 0.9)', 
      cornerRadius: 6,
      padding: 12,
      displayColors: true,
      callbacks: {
        label: (context) => {
          let label = context.dataset.label || '';
          if (label) label += ': ';
          // Manejo de etiquetas para barras y líneas
          if (context.parsed.x !== undefined || context.parsed.y !== undefined) {
             const value = context.parsed.x !== undefined ? context.parsed.x : context.parsed.y;
             label += `$${value.toLocaleString('es-CO')}`;
          }
          // Manejo de etiquetas para Doughnut/Polar
          if (context.chart.config.type !== 'line' && context.chart.config.type !== 'bar') {
              label = `${context.label}: ${context.formattedValue}`;
          }
          return label;
        }
      }
    }
  },
  scales: {
    x: { 
      grid: { color: isHorizontal ? '#e5e7eb' : 'transparent', drawBorder: false, borderDash: [4, 4] },
      ticks: { 
        color: '#6b7280',
        callback: (value) => isHorizontal ? `$${(value / 1000).toFixed(0)}K` : value 
      }
    },
    y: { 
      grid: { color: isHorizontal ? 'transparent' : '#e5e7eb', drawBorder: false, borderDash: [4, 4] },
      ticks: { 
        color: '#6b7280',
        callback: (value) => isHorizontal ? value : `$${(value / 1000).toFixed(0)}K`
      }
    }
  }
});

// NUEVO: Computed para filtrar ventas diarias sin ceros
const filteredDailySales = computed(() => {
  if (!dailySales.value || dailySales.value.length === 0) {
    return []
  }
  
  // Crear array de objetos con fecha y ventas, filtrar solo las ventas > 0
  const salesWithDates = dailySales.value.map((sales, index) => ({
    date: formatDateStringByIndex(index),
    sales: sales,
    index: index
  }))
  
  // Filtrar solo las ventas mayores a 0
  return salesWithDates.filter(item => item.sales > 0)
})

// NUEVO: Computed para filtrar productos con ventas mayores a 0
const filteredTopProducts = computed(() => {
  if (!topProducts.value || topProducts.value.length === 0) {
    return []
  }
  
  // Filtrar productos que tengan ventas o ingresos mayores a 0
  return topProducts.value.filter(product => 
    (product.sold > 0) || (product.revenue > 0)
  )
})

// NUEVO: Computed para filtrar categorías con ventas mayores a 0
const filteredSalesByCategory = computed(() => {
  if (!salesByCategory.value || salesByCategory.value.length === 0) {
    return []
  }
  
  // Filtrar categorías que tengan ventas mayores a 0
  return salesByCategory.value.filter(category => category.sales > 0)
})


// 1. LÍNEA (Tendencia) - Mejorado con datos por horas para 'today'
const lineChartData = computed(() => {
  console.log('📊 [DEBUG] lineChartData computed ejecutándose...')
  console.log('📊 [DEBUG] selectedPeriod.value:', selectedPeriod.value)
  
  // Elegir fuente de datos según el período
  let dataValues, labels
  
  if (selectedPeriod.value === 'today') {
    // Usar datos por horas para 'today' (24 horas de 00:00 a 23:00)
    dataValues = (hourlySales.value && hourlySales.value.length > 0) 
      ? hourlySales.value 
      : Array.from({ length: 24 }, () => 0) // 24 horas vacías por defecto
    
    // Etiquetas por horas (00:00, 01:00, ..., 23:00)
    labels = Array.from({ length: 24 }, (_, i) => {
      const hour = i.toString().padStart(2, '0')
      return `${hour}:00`
    })
    
    console.log('📊 [DEBUG] Usando hourlySales.value:', dataValues.length, 'elementos')
    console.log('📊 [DEBUG] hourlySales valores > 0:', dataValues.map((v, i) => v > 0 ? `${i}:00 = $${v}` : null).filter(Boolean))
  } else {
    // NUEVA LÓGICA: Usar datos estructurados para otros períodos
    dataValues = (dailySales.value && dailySales.value.length > 0) 
      ? dailySales.value 
      : [1200, 1800, 1500, 2100, 1900, 2300, 2800] // Datos de ejemplo
    
    // Generar etiquetas apropiadas según el período
    if (selectedPeriod.value === 'week') {
      labels = ['Vie', 'Sáb', 'Dom', 'Lun', 'Mar', 'Mié', 'Jue'] // Últimos 7 días
    } else if (selectedPeriod.value === 'month') {
      // Para mes, usar números de día
      labels = Array.from({ length: dataValues.length }, (_, i) => `Día ${i + 1}`)
    } else if (selectedPeriod.value === 'year') {
      // Para año, usar nombres de meses
      const monthNames = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic']
      labels = dataValues.length === 12 
        ? monthNames 
        : Array.from({ length: dataValues.length }, (_, i) => `Mes ${i + 1}`)
    } else {
      labels = Array.from({ length: dataValues.length }, (_, i) => `Período ${i + 1}`)
    }
    
    console.log('📊 [DEBUG] Usando dailySales.value:', dataValues.length, 'elementos')
  }
  
  return {
    labels: labels,
    datasets: [
      {
        label: 'Ventas',
        data: dataValues,
        borderColor: 'rgba(59, 130, 246, 1)', 
        backgroundColor: 'rgba(59, 130, 246, 0.1)',
        borderWidth: 4, 
        tension: 0.4, 
        fill: true,
        pointBackgroundColor: 'rgba(59, 130, 246, 1)',
        pointBorderColor: '#ffffff',
        pointRadius: 6, 
        pointHoverRadius: 8,
        pointBorderWidth: 3,
        pointHoverBackgroundColor: 'rgba(59, 130, 246, 1)',
        pointHoverBorderColor: '#ffffff',
        borderCapStyle: 'round',
        borderJoinStyle: 'round'
      }
    ]
  }
})
const lineChartOptions = computed(() => ({
  responsive: true,
  maintainAspectRatio: false,
  interaction: {
    intersect: false,
    mode: 'index'
  },
  plugins: {
    legend: {
      display: true,
      position: 'top',
      labels: {
        usePointStyle: true,
        padding: 20,
        font: {
          family: "'Inter', sans-serif",
          size: 12,
          weight: '500'
        }
      }
    },
    tooltip: {
      backgroundColor: 'rgba(0, 0, 0, 0.8)',
      titleColor: '#ffffff',
      bodyColor: '#ffffff',
      borderColor: 'rgba(59, 130, 246, 0.8)',
      borderWidth: 1,
      cornerRadius: 8,
      displayColors: false,
      callbacks: {
        label: (context) => `Ventas: $${context.parsed.y.toLocaleString()}`
      }
    }
  },
  scales: {
    x: {
      grid: {
        display: true,
        color: 'rgba(0, 0, 0, 0.05)',
        drawBorder: false
      },
      ticks: {
        color: '#6b7280',
        font: {
          family: "'Inter', sans-serif",
          size: 11
        }
      }
    },
    y: {
      grid: {
        display: true,
        color: 'rgba(0, 0, 0, 0.05)',
        drawBorder: false
      },
      ticks: {
        color: '#6b7280',
        font: {
          family: "'Inter', sans-serif",
          size: 11
        },
        callback: (value) => `$${(value / 1000).toFixed(0)}K`
      }
    }
  }
}));

// 2. BARRAS HORIZONTALES (Top Productos)
const barChartData = computed(() => ({
  labels: topProducts.value.map(p => p.name),
  datasets: [
    {
      label: 'Ingreso',
      data: topProducts.value.map(p => p.revenue),
      backgroundColor: '#3b82f6', // blue-500
      borderRadius: 4, 
      barPercentage: 0.6, 
    }
  ]
}));
const barChartOptions = getProChartOptions(true, false);

// 3. BARRAS VERTICALES (Categorías)
const categoryBarChartData = computed(() => ({
  labels: salesByCategory.value.map(c => c.name),
  datasets: [
    {
      label: 'Ventas',
      data: salesByCategory.value.map(c => c.sales),
      backgroundColor: '#8b5cf6', // indigo-500
      borderRadius: 4,
      barPercentage: 0.7,
      categoryPercentage: 0.8
    }
  ]
}));
const categoryBarChartOptions = getProChartOptions(false, false);






// 5. GRÁFICO DE RADAR (Rendimiento Categoria)
const radarChartData = computed(() => ({
    labels: ['Ventas', 'Margen', 'Tráfico'],
    datasets: categoryPerformance.value.map((cat, index) => {
        const color = ['#3b82f6', '#10b981', '#ef4444'][index % 3]; // Azul, Verde, Rojo
        return {
            label: cat.name,
            data: [cat.salesScore, cat.marginScore, cat.trafficScore],
            backgroundColor: `${color}33`, // Con opacidad
            borderColor: color,
            borderWidth: 2,
            pointBackgroundColor: color,
            pointBorderColor: '#fff',
        }
    }),
}));

const radarChartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: { display: true, position: 'top' }, // La leyenda es útil aquí
        tooltip: getProChartOptions().plugins.tooltip,
    },
    scales: {
        r: {
            angleLines: { color: '#e5e7eb' },
            grid: { color: '#e5e7eb' },
            pointLabels: { font: { size: 14, weight: 'bold' }, color: '#374151' },
            ticks: { display: false, beginAtZero: true, max: 100 }
        }
    }
};

// 6. GRÁFICO POLAR (Stock Bajo)
const polarChartData = computed(() => ({
    labels: lowStockProducts.value.map(p => p.name),
    datasets: [
        {
            data: lowStockProducts.value.map(p => p.units),
            backgroundColor: lowStockProducts.value.map(p => p.color),
            borderColor: '#fff',
            borderWidth: 2,
        }
    ]
}));

const polarChartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: { display: true, position: 'right' }, // La leyenda es necesaria aquí
        tooltip: getProChartOptions().plugins.tooltip,
    },
    scales: {
        r: {
            grid: { color: '#e5e7eb' },
            ticks: { display: false, beginAtZero: true }
        }
    }
};

onMounted(async () => {
  console.log('Módulo de reportes inicializado con 6 gráficos.')
  
  // Cargar datos iniciales
  await loadReportsData()
})
</script>

<style scoped>
/* Animación suave de aparición */
.animate-fade-in {
  animation: fadeIn 0.3s ease-in-out;
}

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

/* Estilos generales */
* {
  transition: all 0.2s ease-in-out;
}
</style>