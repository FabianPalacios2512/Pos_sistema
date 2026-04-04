<template>
  <!-- 📊 REPORTES DE INVENTARIO - NIVEL ENTERPRISE SaaS -->
  <!-- Análisis profesional de inventario: KPIs, Rotación ABC, Distribución por Categoría, Stock Crítico -->
  <div class="space-y-6 animate-fade-in">
    
    <!-- ═══════════════════════════════════════════════════════════════ -->
    <!-- Header con Controles -->
    <!-- ═══════════════════════════════════════════════════════════════ -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
      <div>
        <h2 class="text-xl font-bold text-gray-900 dark:text-white">Análisis de Inventario</h2>
        <p class="text-sm text-gray-600 dark:text-zinc-400 mt-1">Métricas de stock, rotación y valorización</p>
      </div>
      
      <div class="flex items-center gap-3">
        <!-- Filtro de Categoría -->
        <select 
          v-model="selectedCategory"
          @change="loadData"
          class="px-4 py-2.5 text-sm rounded-xl bg-white dark:bg-zinc-900 text-gray-700 dark:text-zinc-300 border border-gray-200 dark:border-zinc-700 font-medium focus:outline-none focus:ring-2 focus:ring-amber-500 dark:focus:ring-amber-400"
        >
          <option value="">Todas las categorías</option>
          <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
        </select>
        
        <!-- Botón Refrescar -->
        <button 
          @click="loadData" 
          :disabled="loading"
          class="px-5 py-2.5 bg-white dark:bg-zinc-900 hover:bg-slate-50 dark:hover:bg-zinc-800 text-slate-600 dark:text-zinc-200 text-sm font-bold rounded-xl border border-slate-200 dark:border-zinc-800 shadow-sm transition-all duration-200 flex items-center gap-2"
        >
          <svg :class="['w-4 h-4', loading ? 'animate-spin' : '']" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
          </svg>
          <span>Refrescar</span>
        </button>
        
        <!-- Botón Exportar -->
        <button 
          @click="exportReport"
          class="px-6 py-2.5 bg-slate-900 dark:bg-slate-700 hover:bg-black dark:hover:bg-slate-600 text-white text-sm font-bold rounded-xl shadow-lg shadow-slate-400/40 dark:shadow-slate-900/50 transition-all duration-300 flex items-center gap-2"
        >
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
          </svg>
          <span>Exportar PDF</span>
        </button>
      </div>
    </div>

    <!-- ═══════════════════════════════════════════════════════════════ -->
    <!-- KPIs Principales - Grid 3x2 -->
    <!-- ═══════════════════════════════════════════════════════════════ -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
      
      <!-- KPI: Total Productos -->
      <div class="bg-white dark:bg-zinc-900/80 backdrop-blur-sm rounded-xl px-4 py-3 border border-gray-300 dark:border-zinc-800/60 hover:border-gray-400 dark:hover:border-zinc-700/80 transition-all duration-200 hover:shadow-md dark:shadow-lg dark:shadow-black/30">
        <div class="flex items-center gap-3">
          <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0 bg-gray-100 dark:bg-zinc-800/50 border border-gray-200 dark:border-white/5">
            <svg class="w-5 h-5 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
            </svg>
          </div>
          <div class="flex-1 min-w-0">
            <p class="text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wide">Total Productos</p>
            <p class="text-2xl font-bold text-gray-900 dark:text-white mt-0.5">
              <span v-if="loading" class="inline-block w-16 h-7 bg-gray-200 dark:bg-zinc-700 rounded animate-pulse"></span>
              <span v-else>{{ formatNumber(metrics.totalProducts) }}</span>
            </p>
            <p class="text-xs text-gray-600 dark:text-zinc-400 mt-0.5">{{ formatNumber(metrics.activeProducts) }} activos</p>
          </div>
        </div>
      </div>

      <!-- KPI: Valor de Inventario (Costo) -->
      <div class="bg-white dark:bg-zinc-900/80 backdrop-blur-sm rounded-xl px-4 py-3 border border-gray-300 dark:border-zinc-800/60 hover:border-gray-400 dark:hover:border-zinc-700/80 transition-all duration-200 hover:shadow-md dark:shadow-lg dark:shadow-black/30">
        <div class="flex items-center gap-3">
          <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0 bg-gray-100 dark:bg-zinc-800/50 border border-gray-200 dark:border-white/5">
            <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
          </div>
          <div class="flex-1 min-w-0">
            <p class="text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wide">Valor Inventario</p>
            <p class="text-2xl font-bold text-gray-900 dark:text-white mt-0.5">
              <span v-if="loading" class="inline-block w-24 h-7 bg-gray-200 dark:bg-zinc-700 rounded animate-pulse"></span>
              <span v-else>{{ formatCurrency(metrics.totalInventoryValue) }}</span>
            </p>
            <p class="text-xs text-gray-600 dark:text-zinc-400 mt-0.5">A precio de costo</p>
          </div>
        </div>
      </div>

      <!-- KPI: Valor Potencial de Venta -->
      <div class="bg-white dark:bg-zinc-900/80 backdrop-blur-sm rounded-xl px-4 py-3 border border-gray-300 dark:border-zinc-800/60 hover:border-gray-400 dark:hover:border-zinc-700/80 transition-all duration-200 hover:shadow-md dark:shadow-lg dark:shadow-black/30">
        <div class="flex items-center gap-3">
          <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0 bg-gray-100 dark:bg-zinc-800/50 border border-gray-200 dark:border-white/5">
            <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
            </svg>
          </div>
          <div class="flex-1 min-w-0">
            <p class="text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wide">Valor de Venta</p>
            <p class="text-2xl font-bold text-emerald-600 dark:text-emerald-400 mt-0.5">
              <span v-if="loading" class="inline-block w-24 h-7 bg-gray-200 dark:bg-zinc-700 rounded animate-pulse"></span>
              <span v-else>{{ formatCurrency(metrics.totalSaleValue) }}</span>
            </p>
            <p class="text-xs text-gray-600 dark:text-zinc-400 mt-0.5">Potencial a precio venta</p>
          </div>
        </div>
      </div>

      <!-- KPI: Margen Potencial -->
      <div class="bg-white dark:bg-zinc-900/80 backdrop-blur-sm rounded-xl px-4 py-3 border border-gray-300 dark:border-zinc-800/60 hover:border-gray-400 dark:hover:border-zinc-700/80 transition-all duration-200 hover:shadow-md dark:shadow-lg dark:shadow-black/30">
        <div class="flex items-center gap-3">
          <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0 bg-gray-100 dark:bg-zinc-800/50 border border-gray-200 dark:border-white/5">
            <svg class="w-5 h-5 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
            </svg>
          </div>
          <div class="flex-1 min-w-0">
            <p class="text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wide">Margen Potencial</p>
            <p class="text-2xl font-bold text-gray-900 dark:text-white mt-0.5">
              <span v-if="loading" class="inline-block w-20 h-7 bg-gray-200 dark:bg-zinc-700 rounded animate-pulse"></span>
              <span v-else>{{ formatCurrency(metrics.potentialMargin) }}</span>
            </p>
            <p class="text-xs text-gray-600 dark:text-zinc-400 mt-0.5">{{ formatPercent(metrics.marginPercent) }} del valor venta</p>
          </div>
        </div>
      </div>

      <!-- KPI: Stock Bajo -->
      <div class="bg-white dark:bg-zinc-900/80 backdrop-blur-sm rounded-xl px-4 py-3 border border-gray-300 dark:border-zinc-800/60 hover:border-gray-400 dark:hover:border-zinc-700/80 transition-all duration-200 hover:shadow-md dark:shadow-lg dark:shadow-black/30">
        <div class="flex items-center gap-3">
          <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0 bg-gray-100 dark:bg-zinc-800/50 border border-gray-200 dark:border-white/5">
            <svg class="w-5 h-5 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
            </svg>
          </div>
          <div class="flex-1 min-w-0">
            <p class="text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wide">Stock Bajo</p>
            <p class="text-2xl font-bold text-amber-600 dark:text-amber-400 mt-0.5">
              <span v-if="loading" class="inline-block w-12 h-7 bg-gray-200 dark:bg-zinc-700 rounded animate-pulse"></span>
              <span v-else>{{ formatNumber(metrics.lowStockProducts) }}</span>
            </p>
            <p class="text-xs text-gray-600 dark:text-zinc-400 mt-0.5">Productos que reponer</p>
          </div>
        </div>
      </div>

      <!-- KPI: Sin Stock -->
      <div class="bg-white dark:bg-zinc-900/80 backdrop-blur-sm rounded-xl px-4 py-3 border border-gray-300 dark:border-zinc-800/60 hover:border-gray-400 dark:hover:border-zinc-700/80 transition-all duration-200 hover:shadow-md dark:shadow-lg dark:shadow-black/30">
        <div class="flex items-center gap-3">
          <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0 bg-gray-100 dark:bg-zinc-800/50 border border-gray-200 dark:border-white/5">
            <svg class="w-5 h-5 text-rose-600 dark:text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path>
            </svg>
          </div>
          <div class="flex-1 min-w-0">
            <p class="text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wide">Sin Stock</p>
            <p class="text-2xl font-bold text-rose-600 dark:text-rose-400 mt-0.5">
              <span v-if="loading" class="inline-block w-12 h-7 bg-gray-200 dark:bg-zinc-700 rounded animate-pulse"></span>
              <span v-else>{{ formatNumber(metrics.outOfStockProducts) }}</span>
            </p>
            <p class="text-xs text-gray-600 dark:text-zinc-400 mt-0.5">Productos agotados</p>
          </div>
        </div>
      </div>
    </div>

    <!-- ═══════════════════════════════════════════════════════════════ -->
    <!-- Gráficos Principales - Grid 2 columnas -->
    <!-- ═══════════════════════════════════════════════════════════════ -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
      
      <!-- Gráfico: Distribución por Categoría (Doughnut) -->
      <div class="bg-white dark:bg-zinc-900 rounded-2xl shadow-xl dark:shadow-black/50 border border-gray-300 dark:border-zinc-800 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-200 dark:border-zinc-800">
          <h3 class="text-base font-bold text-gray-900 dark:text-white">Distribución por Categoría</h3>
          <p class="text-xs text-gray-600 dark:text-zinc-400 mt-1">Valor de inventario por categoría</p>
        </div>
        <div class="p-5">
          <div v-if="loading" class="h-[300px] flex items-center justify-center">
            <div class="w-48 h-48 rounded-full border-8 border-gray-200 dark:border-zinc-700 border-t-amber-500 animate-spin"></div>
          </div>
          <div v-else-if="categoryDistribution.labels.length > 0" class="h-[300px] flex items-center justify-center">
            <Doughnut :data="categoryChartData" :options="doughnutOptions" />
          </div>
          <div v-else class="h-[300px] flex items-center justify-center text-gray-500 dark:text-zinc-400">
            <p>No hay datos de categorías</p>
          </div>
        </div>
      </div>

      <!-- Gráfico: Análisis ABC (Bar) -->
      <div class="bg-white dark:bg-zinc-900 rounded-2xl shadow-xl dark:shadow-black/50 border border-gray-300 dark:border-zinc-800 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-200 dark:border-zinc-800">
          <h3 class="text-base font-bold text-gray-900 dark:text-white">Análisis ABC de Rotación</h3>
          <p class="text-xs text-gray-600 dark:text-zinc-400 mt-1">Clasificación por velocidad de venta</p>
        </div>
        <div class="p-5">
          <div v-if="loading" class="h-[300px] flex items-center justify-center">
            <div class="space-y-3 w-full">
              <div class="h-8 bg-gray-200 dark:bg-zinc-700 rounded animate-pulse"></div>
              <div class="h-8 bg-gray-200 dark:bg-zinc-700 rounded animate-pulse w-3/4"></div>
              <div class="h-8 bg-gray-200 dark:bg-zinc-700 rounded animate-pulse w-1/2"></div>
            </div>
          </div>
          <div v-else-if="abcAnalysis.A > 0 || abcAnalysis.B > 0 || abcAnalysis.C > 0" class="h-[300px]">
            <Bar :data="abcChartData" :options="barOptions" />
          </div>
          <div v-else class="h-[300px] flex items-center justify-center text-gray-500 dark:text-zinc-400">
            <p>No hay datos de rotación</p>
          </div>
        </div>
        <!-- Leyenda ABC -->
        <div class="px-5 pb-4 grid grid-cols-3 gap-3">
          <div class="text-center p-2 bg-emerald-50 dark:bg-emerald-950 rounded-lg border border-emerald-100 dark:border-emerald-800">
            <p class="text-lg font-bold text-emerald-700 dark:text-emerald-400">{{ formatNumber(abcAnalysis.A) }}</p>
            <p class="text-xs text-emerald-600 dark:text-emerald-400">Clase A - Alta rotación</p>
          </div>
          <div class="text-center p-2 bg-blue-50 dark:bg-blue-950 rounded-lg border border-blue-100 dark:border-blue-800">
            <p class="text-lg font-bold text-blue-700 dark:text-blue-400">{{ formatNumber(abcAnalysis.B) }}</p>
            <p class="text-xs text-blue-600 dark:text-blue-400">Clase B - Media rotación</p>
          </div>
          <div class="text-center p-2 bg-amber-50 dark:bg-amber-950 rounded-lg border border-amber-100 dark:border-amber-800">
            <p class="text-lg font-bold text-amber-700 dark:text-amber-400">{{ formatNumber(abcAnalysis.C) }}</p>
            <p class="text-xs text-amber-600 dark:text-amber-400">Clase C - Baja rotación</p>
          </div>
        </div>
      </div>
    </div>

    <!-- ═══════════════════════════════════════════════════════════════ -->
    <!-- Gráfico: Tendencia de Movimientos (Línea) -->
    <!-- ═══════════════════════════════════════════════════════════════ -->
    <div class="bg-white dark:bg-zinc-900 rounded-2xl shadow-xl dark:shadow-black/50 border border-gray-300 dark:border-zinc-800 overflow-hidden">
      <div class="px-5 py-4 border-b border-gray-200 dark:border-zinc-800">
        <h3 class="text-base font-bold text-gray-900 dark:text-white">Movimientos de Inventario</h3>
        <p class="text-xs text-gray-600 dark:text-zinc-400 mt-1">Entradas vs salidas - últimos 30 días</p>
      </div>
      <div class="p-5">
        <div v-if="loading" class="h-[250px] flex items-center justify-center">
          <div class="w-full h-full bg-gray-100 dark:bg-zinc-800 rounded-lg animate-pulse"></div>
        </div>
        <div v-else-if="movementsTrend.labels.length > 0" class="h-[250px]">
          <Line :data="movementsChartData" :options="lineOptions" />
        </div>
        <div v-else class="h-[250px] flex items-center justify-center text-gray-500 dark:text-zinc-400">
          <p>No hay movimientos registrados</p>
        </div>
      </div>
    </div>

    <!-- ═══════════════════════════════════════════════════════════════ -->
    <!-- Tablas: Top Vendidos + Productos con Stock Bajo -->
    <!-- ═══════════════════════════════════════════════════════════════ -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
      
      <!-- Top Productos Más Vendidos -->
      <div class="bg-white dark:bg-zinc-900 rounded-2xl shadow-xl dark:shadow-black/50 border border-gray-300 dark:border-zinc-800 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-200 dark:border-zinc-800">
          <div class="flex items-center justify-between">
            <div>
              <h3 class="text-base font-bold text-gray-900 dark:text-white">Top Productos Más Vendidos</h3>
              <p class="text-xs text-gray-600 dark:text-zinc-400 mt-1">Últimos 30 días</p>
            </div>
            <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-50 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-400 border border-emerald-100 dark:border-emerald-800 uppercase tracking-wide">
              TOP 10
            </span>
          </div>
        </div>
        <div class="overflow-x-auto">
          <table class="min-w-full">
            <thead class="bg-gray-50 dark:bg-zinc-900">
              <tr>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-600 dark:text-zinc-400 uppercase tracking-wide">#</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-600 dark:text-zinc-400 uppercase tracking-wide">Producto</th>
                <th class="px-4 py-3 text-right text-xs font-medium text-gray-600 dark:text-zinc-400 uppercase tracking-wide">Vendidos</th>
                <th class="px-4 py-3 text-right text-xs font-medium text-gray-600 dark:text-zinc-400 uppercase tracking-wide">Ingresos</th>
              </tr>
            </thead>
            <tbody class="bg-white dark:bg-zinc-900">
              <template v-if="loading">
                <tr v-for="n in 5" :key="n" class="border-b border-gray-100 dark:border-zinc-800">
                  <td class="px-4 py-3"><div class="w-6 h-6 bg-gray-200 dark:bg-zinc-700 rounded-full animate-pulse"></div></td>
                  <td class="px-4 py-3"><div class="w-32 h-4 bg-gray-200 dark:bg-zinc-700 rounded animate-pulse"></div></td>
                  <td class="px-4 py-3"><div class="w-12 h-4 bg-gray-200 dark:bg-zinc-700 rounded animate-pulse ml-auto"></div></td>
                  <td class="px-4 py-3"><div class="w-20 h-4 bg-gray-200 dark:bg-zinc-700 rounded animate-pulse ml-auto"></div></td>
                </tr>
              </template>
              <template v-else-if="topSellingProducts.length > 0">
                <tr v-for="(product, index) in topSellingProducts" :key="product.id" 
                    class="hover:bg-gray-50 dark:hover:bg-zinc-800/50 transition-colors duration-200 border-b border-gray-100 dark:border-zinc-800 last:border-b-0">
                  <td class="px-4 py-3">
                    <div class="w-7 h-7 rounded-full flex items-center justify-center text-xs font-bold"
                         :class="[
                           index === 0 ? 'bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400' :
                           index === 1 ? 'bg-gray-200 dark:bg-zinc-700 text-gray-700 dark:text-zinc-300' :
                           index === 2 ? 'bg-orange-100 dark:bg-orange-900/30 text-orange-700 dark:text-orange-400' :
                           'bg-gray-100 dark:bg-zinc-800 text-gray-600 dark:text-zinc-400'
                         ]">
                      {{ index + 1 }}
                    </div>
                  </td>
                  <td class="px-4 py-3">
                    <p class="text-sm font-medium text-gray-900 dark:text-white truncate max-w-[200px]">{{ product.name }}</p>
                    <p class="text-xs text-gray-500 dark:text-zinc-500">{{ product.sku || 'Sin SKU' }}</p>
                  </td>
                  <td class="px-4 py-3 text-right">
                    <span class="text-sm font-bold text-gray-900 dark:text-white">{{ formatNumber(product.total_quantity_sold || product.total_sold || 0) }}</span>
                    <span class="text-xs text-gray-500 dark:text-zinc-500 ml-1">uds</span>
                  </td>
                  <td class="px-4 py-3 text-right">
                    <span class="text-sm font-bold text-emerald-600 dark:text-emerald-400">{{ formatCurrency(product.total_revenue || 0) }}</span>
                  </td>
                </tr>
              </template>
              <tr v-else>
                <td colspan="4" class="px-4 py-8 text-center text-gray-500 dark:text-zinc-400">
                  No hay datos de ventas disponibles
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Productos con Stock Bajo -->
      <div class="bg-white dark:bg-zinc-900 rounded-2xl shadow-xl dark:shadow-black/50 border border-gray-300 dark:border-zinc-800 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-200 dark:border-zinc-800">
          <div class="flex items-center justify-between">
            <div>
              <h3 class="text-base font-bold text-gray-900 dark:text-white">Productos con Stock Bajo</h3>
              <p class="text-xs text-gray-600 dark:text-zinc-400 mt-1">Requieren reposición inmediata</p>
            </div>
            <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-rose-50 dark:bg-rose-950 text-rose-700 dark:text-rose-400 border border-rose-100 dark:border-rose-800 uppercase tracking-wide">
              ALERTA
            </span>
          </div>
        </div>
        <div class="overflow-x-auto">
          <table class="min-w-full">
            <thead class="bg-gray-50 dark:bg-zinc-900">
              <tr>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-600 dark:text-zinc-400 uppercase tracking-wide">Producto</th>
                <th class="px-4 py-3 text-center text-xs font-medium text-gray-600 dark:text-zinc-400 uppercase tracking-wide">Stock</th>
                <th class="px-4 py-3 text-center text-xs font-medium text-gray-600 dark:text-zinc-400 uppercase tracking-wide">Mínimo</th>
                <th class="px-4 py-3 text-center text-xs font-medium text-gray-600 dark:text-zinc-400 uppercase tracking-wide">Estado</th>
              </tr>
            </thead>
            <tbody class="bg-white dark:bg-zinc-900">
              <template v-if="loading">
                <tr v-for="n in 5" :key="n" class="border-b border-gray-100 dark:border-zinc-800">
                  <td class="px-4 py-3"><div class="w-32 h-4 bg-gray-200 dark:bg-zinc-700 rounded animate-pulse"></div></td>
                  <td class="px-4 py-3"><div class="w-12 h-4 bg-gray-200 dark:bg-zinc-700 rounded animate-pulse mx-auto"></div></td>
                  <td class="px-4 py-3"><div class="w-12 h-4 bg-gray-200 dark:bg-zinc-700 rounded animate-pulse mx-auto"></div></td>
                  <td class="px-4 py-3"><div class="w-16 h-6 bg-gray-200 dark:bg-zinc-700 rounded-full animate-pulse mx-auto"></div></td>
                </tr>
              </template>
              <template v-else-if="lowStockProducts.length > 0">
                <tr v-for="product in lowStockProducts" :key="product.id" 
                    class="hover:bg-gray-50 dark:hover:bg-zinc-800/50 transition-colors duration-200 border-b border-gray-100 dark:border-zinc-800 last:border-b-0">
                  <td class="px-4 py-3">
                    <p class="text-sm font-medium text-gray-900 dark:text-white truncate max-w-[200px]">{{ product.name }}</p>
                    <p class="text-xs text-gray-500 dark:text-zinc-500">{{ product.category?.name || 'Sin categoría' }}</p>
                  </td>
                  <td class="px-4 py-3 text-center">
                    <span class="text-lg font-bold" 
                          :class="product.current_stock <= 0 ? 'text-rose-600 dark:text-rose-400' : 'text-amber-600 dark:text-amber-400'">
                      {{ product.current_stock }}
                    </span>
                  </td>
                  <td class="px-4 py-3 text-center">
                    <span class="text-sm text-gray-600 dark:text-zinc-400">{{ product.min_stock }}</span>
                  </td>
                  <td class="px-4 py-3 text-center">
                    <span v-if="product.current_stock <= 0" 
                          class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-rose-50 dark:bg-rose-950 text-rose-700 dark:text-rose-400 border border-rose-100 dark:border-rose-800 uppercase tracking-wide">
                      Agotado
                    </span>
                    <span v-else 
                          class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-50 dark:bg-amber-950 text-amber-700 dark:text-amber-400 border border-amber-100 dark:border-amber-800 uppercase tracking-wide">
                      Reponer
                    </span>
                  </td>
                </tr>
              </template>
              <tr v-else>
                <td colspan="4" class="px-4 py-8 text-center text-emerald-600 dark:text-emerald-400">
                  <svg class="w-8 h-8 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                  </svg>
                  Todos los productos tienen stock adecuado
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- ═══════════════════════════════════════════════════════════════ -->
    <!-- Tabla: Productos con Menor Rotación (Dead Stock) -->
    <!-- ═══════════════════════════════════════════════════════════════ -->
    <div class="bg-white dark:bg-zinc-900 rounded-2xl shadow-xl dark:shadow-black/50 border border-gray-300 dark:border-zinc-800 overflow-hidden">
      <div class="px-5 py-4 border-b border-gray-200 dark:border-zinc-800">
        <div class="flex items-center justify-between">
          <div>
            <h3 class="text-base font-bold text-gray-900 dark:text-white">Inventario sin Rotación</h3>
            <p class="text-xs text-gray-600 dark:text-zinc-400 mt-1">Productos sin ventas en los últimos 30 días - Capital inmovilizado</p>
          </div>
          <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-purple-50 dark:bg-purple-950 text-purple-700 dark:text-purple-400 border border-purple-100 dark:border-purple-800 uppercase tracking-wide">
            DEAD STOCK
          </span>
        </div>
      </div>
      <div class="overflow-x-auto">
        <table class="min-w-full">
          <thead class="bg-gray-50 dark:bg-zinc-900">
            <tr>
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-600 dark:text-zinc-400 uppercase tracking-wide">Producto</th>
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-600 dark:text-zinc-400 uppercase tracking-wide">Categoría</th>
              <th class="px-4 py-3 text-center text-xs font-medium text-gray-600 dark:text-zinc-400 uppercase tracking-wide">Stock</th>
              <th class="px-4 py-3 text-right text-xs font-medium text-gray-600 dark:text-zinc-400 uppercase tracking-wide">Valor Inmovilizado</th>
              <th class="px-4 py-3 text-center text-xs font-medium text-gray-600 dark:text-zinc-400 uppercase tracking-wide">Días sin Venta</th>
            </tr>
          </thead>
          <tbody class="bg-white dark:bg-zinc-900">
            <template v-if="loading">
              <tr v-for="n in 5" :key="n" class="border-b border-gray-100 dark:border-zinc-800">
                <td class="px-4 py-3"><div class="w-32 h-4 bg-gray-200 dark:bg-zinc-700 rounded animate-pulse"></div></td>
                <td class="px-4 py-3"><div class="w-20 h-6 bg-gray-200 dark:bg-zinc-700 rounded-full animate-pulse"></div></td>
                <td class="px-4 py-3"><div class="w-12 h-4 bg-gray-200 dark:bg-zinc-700 rounded animate-pulse mx-auto"></div></td>
                <td class="px-4 py-3"><div class="w-24 h-4 bg-gray-200 dark:bg-zinc-700 rounded animate-pulse ml-auto"></div></td>
                <td class="px-4 py-3"><div class="w-16 h-6 bg-gray-200 dark:bg-zinc-700 rounded-full animate-pulse mx-auto"></div></td>
              </tr>
            </template>
            <template v-else-if="deadStockProducts.length > 0">
              <tr v-for="product in deadStockProducts" :key="product.id" 
                  class="hover:bg-gray-50 dark:hover:bg-zinc-800/50 transition-colors duration-200 border-b border-gray-100 dark:border-zinc-800 last:border-b-0">
                <td class="px-4 py-3">
                  <p class="text-sm font-medium text-gray-900 dark:text-white">{{ product.name }}</p>
                  <p class="text-xs text-gray-500 dark:text-zinc-500">SKU: {{ product.sku || 'N/A' }}</p>
                </td>
                <td class="px-4 py-3">
                  <span class="inline-flex items-center bg-blue-50 dark:bg-blue-950 text-blue-700 dark:text-blue-400 rounded-full px-2.5 py-1 text-xs font-medium border border-blue-100 dark:border-blue-800">
                    {{ product.category_name || product.category?.name || 'Sin categoría' }}
                  </span>
                </td>
                <td class="px-4 py-3 text-center">
                  <span class="text-sm font-bold text-gray-900 dark:text-white">{{ product.current_stock }}</span>
                </td>
                <td class="px-4 py-3 text-right">
                  <span class="text-sm font-bold text-purple-600 dark:text-purple-400">
                    {{ formatCurrency((parseInt(product.current_stock) || 0) * (parseFloat(product.cost_price) || 0)) }}
                  </span>
                </td>
                <td class="px-4 py-3 text-center">
                  <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-gray-100 dark:bg-zinc-800 text-gray-700 dark:text-zinc-300 border border-gray-200 dark:border-zinc-700">
                    +30 días
                  </span>
                </td>
              </tr>
            </template>
            <tr v-else>
              <td colspan="5" class="px-4 py-8 text-center text-emerald-600 dark:text-emerald-400">
                <svg class="w-8 h-8 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Excelente! Todos los productos tienen movimiento de ventas
              </td>
            </tr>
          </tbody>
        </table>
      </div>
      <!-- Total Inmovilizado -->
      <div v-if="!loading && deadStockProducts.length > 0" class="px-5 py-4 bg-gray-50 dark:bg-zinc-800/50 border-t border-gray-200 dark:border-zinc-800">
        <div class="flex items-center justify-between">
          <span class="text-sm font-medium text-gray-600 dark:text-zinc-400">Capital Total Inmovilizado:</span>
          <span class="text-lg font-bold text-purple-600 dark:text-purple-400">{{ formatCurrency(totalDeadStockValue) }}</span>
        </div>
      </div>
    </div>

  </div>
</template>

<script setup>
import { ref, computed, onMounted, onBeforeUnmount, watch } from 'vue'
import { Chart as ChartJS, ArcElement, CategoryScale, LinearScale, BarElement, PointElement, LineElement, Title, Tooltip, Legend, Filler } from 'chart.js'
import ChartDataLabels from 'chartjs-plugin-datalabels'
import { Doughnut, Bar, Line } from 'vue-chartjs'
import api from '@/services/api.js'
import { useUIContextStore } from '@/store/uiContextStore'

// Registrar componentes de Chart.js
ChartJS.register(ArcElement, CategoryScale, LinearScale, BarElement, PointElement, LineElement, Title, Tooltip, Legend, Filler, ChartDataLabels)

// ═══════════════════════════════════════════════════════════════
// Estados Reactivos
// ═══════════════════════════════════════════════════════════════

const loading = ref(true)
const selectedCategory = ref('')
const categories = ref([])

// Métricas principales
const metrics = ref({
  totalProducts: 0,
  activeProducts: 0,
  totalInventoryValue: 0,
  totalSaleValue: 0,
  potentialMargin: 0,
  marginPercent: 0,
  lowStockProducts: 0,
  outOfStockProducts: 0
})

// Datos para gráficos
const categoryDistribution = ref({ labels: [], data: [], colors: [] })
const abcAnalysis = ref({ A: 0, B: 0, C: 0 })
const movementsTrend = ref({ labels: [], entries: [], exits: [] })

// Listas
const topSellingProducts = ref([])
const lowStockProducts = ref([])
const deadStockProducts = ref([])

// ═══════════════════════════════════════════════════════════════
// Computed: Datos de Gráficos
// ═══════════════════════════════════════════════════════════════

const categoryChartData = computed(() => ({
  labels: categoryDistribution.value.labels,
  datasets: [{
    data: categoryDistribution.value.data,
    backgroundColor: categoryDistribution.value.colors,
    borderWidth: 0,
    hoverOffset: 8
  }]
}))

const abcChartData = computed(() => ({
  labels: ['Clase A', 'Clase B', 'Clase C'],
  datasets: [{
    label: 'Productos',
    data: [abcAnalysis.value.A, abcAnalysis.value.B, abcAnalysis.value.C],
    backgroundColor: [
      'rgba(16, 185, 129, 0.8)',  // Emerald
      'rgba(59, 130, 246, 0.8)',  // Blue
      'rgba(245, 158, 11, 0.8)'   // Amber
    ],
    borderRadius: 8,
    borderSkipped: false
  }]
}))

const movementsChartData = computed(() => ({
  labels: movementsTrend.value.labels,
  datasets: [
    {
      label: 'Entradas',
      data: movementsTrend.value.entries,
      borderColor: 'rgba(16, 185, 129, 1)',
      backgroundColor: 'rgba(16, 185, 129, 0.1)',
      fill: true,
      tension: 0.4,
      pointRadius: 3,
      pointHoverRadius: 6
    },
    {
      label: 'Salidas',
      data: movementsTrend.value.exits,
      borderColor: 'rgba(239, 68, 68, 1)',
      backgroundColor: 'rgba(239, 68, 68, 0.1)',
      fill: true,
      tension: 0.4,
      pointRadius: 3,
      pointHoverRadius: 6
    }
  ]
}))

const totalDeadStockValue = computed(() => {
  return deadStockProducts.value.reduce((sum, p) => {
    const stock = parseInt(p.current_stock) || 0
    const cost = parseFloat(p.cost_price) || 0
    return sum + (stock * cost)
  }, 0)
})

// ═══════════════════════════════════════════════════════════════
// Opciones de Gráficos
// ═══════════════════════════════════════════════════════════════

const doughnutOptions = {
  responsive: true,
  maintainAspectRatio: false,
  cutout: '65%',
  plugins: {
    legend: {
      position: 'right',
      labels: {
        usePointStyle: true,
        padding: 16,
        font: { size: 11, weight: '500' },
        color: document.documentElement.classList.contains('dark') ? '#a1a1aa' : '#71717a'
      }
    },
    tooltip: {
      backgroundColor: 'rgba(0, 0, 0, 0.8)',
      titleColor: '#fff',
      bodyColor: '#fff',
      padding: 12,
      cornerRadius: 8,
      callbacks: {
        label: (context) => ` ${formatCurrency(context.raw)}`
      }
    },
    datalabels: {
      display: false
    }
  }
}

const barOptions = {
  responsive: true,
  maintainAspectRatio: false,
  indexAxis: 'y',
  plugins: {
    legend: { display: false },
    tooltip: {
      backgroundColor: 'rgba(0, 0, 0, 0.8)',
      padding: 12,
      cornerRadius: 8
    },
    datalabels: {
      anchor: 'end',
      align: 'end',
      color: document.documentElement.classList.contains('dark') ? '#e4e4e7' : '#3f3f46',
      font: { weight: 'bold', size: 12 },
      formatter: (value) => value
    }
  },
  scales: {
    x: {
      grid: {
        color: document.documentElement.classList.contains('dark') ? 'rgba(63, 63, 70, 0.3)' : 'rgba(0, 0, 0, 0.05)'
      },
      ticks: {
        color: document.documentElement.classList.contains('dark') ? '#a1a1aa' : '#71717a'
      }
    },
    y: {
      grid: { display: false },
      ticks: {
        color: document.documentElement.classList.contains('dark') ? '#a1a1aa' : '#71717a',
        font: { weight: '500' }
      }
    }
  }
}

const lineOptions = {
  responsive: true,
  maintainAspectRatio: false,
  interaction: {
    intersect: false,
    mode: 'index'
  },
  plugins: {
    legend: {
      position: 'top',
      labels: {
        usePointStyle: true,
        padding: 20,
        font: { size: 11, weight: '500' },
        color: document.documentElement.classList.contains('dark') ? '#a1a1aa' : '#71717a'
      }
    },
    tooltip: {
      backgroundColor: 'rgba(0, 0, 0, 0.8)',
      padding: 12,
      cornerRadius: 8
    },
    datalabels: {
      display: false
    }
  },
  scales: {
    x: {
      grid: { display: false },
      ticks: {
        color: document.documentElement.classList.contains('dark') ? '#a1a1aa' : '#71717a',
        maxRotation: 45
      }
    },
    y: {
      grid: {
        color: document.documentElement.classList.contains('dark') ? 'rgba(63, 63, 70, 0.3)' : 'rgba(0, 0, 0, 0.05)'
      },
      ticks: {
        color: document.documentElement.classList.contains('dark') ? '#a1a1aa' : '#71717a'
      }
    }
  }
}

// ═══════════════════════════════════════════════════════════════
// Funciones de Formato
// ═══════════════════════════════════════════════════════════════

const formatCurrency = (value) => {
  if (value === null || value === undefined) return '$0'
  const num = parseFloat(value)
  if (isNaN(num)) return '$0'
  return new Intl.NumberFormat('es-CO', {
    style: 'currency',
    currency: 'COP',
    minimumFractionDigits: 0,
    maximumFractionDigits: 0
  }).format(num)
}

const formatNumber = (value) => {
  if (value === null || value === undefined) return '0'
  return new Intl.NumberFormat('es-CO').format(value)
}

const formatPercent = (value) => {
  if (value === null || value === undefined) return '0%'
  return `${parseFloat(value).toFixed(1)}%`
}

// ═══════════════════════════════════════════════════════════════
// Colores para gráficos de categorías
// ═══════════════════════════════════════════════════════════════

const categoryColors = [
  'rgba(99, 102, 241, 0.8)',   // Indigo
  'rgba(16, 185, 129, 0.8)',  // Emerald
  'rgba(245, 158, 11, 0.8)',  // Amber
  'rgba(236, 72, 153, 0.8)',  // Pink
  'rgba(59, 130, 246, 0.8)',  // Blue
  'rgba(139, 92, 246, 0.8)',  // Purple
  'rgba(20, 184, 166, 0.8)',  // Teal
  'rgba(249, 115, 22, 0.8)',  // Orange
  'rgba(6, 182, 212, 0.8)',   // Cyan
  'rgba(168, 85, 247, 0.8)',  // Violet
]

// ═══════════════════════════════════════════════════════════════
// Cargar Datos
// ═══════════════════════════════════════════════════════════════

const loadData = async () => {
  loading.value = true
  
  try {
    // Cargar categorías (usando ruta sin auth para reportes)
    const categoriesResponse = await api.get('/inventory-reports/categories')
    if (categoriesResponse.data) {
      // Manejar tanto respuesta con success como array directo
      if (categoriesResponse.data.success && categoriesResponse.data.data) {
        categories.value = categoriesResponse.data.data
      } else if (Array.isArray(categoriesResponse.data)) {
        categories.value = categoriesResponse.data
      } else {
        categories.value = categoriesResponse.data.data || []
      }
    }

    // Cargar overview de inventario
    const overviewResponse = await api.get('/inventory-reports/overview')
    
    // Manejar diferentes formatos de respuesta del overview
    let overviewData = null
    if (overviewResponse.data) {
      if (overviewResponse.data.success && overviewResponse.data.data) {
        overviewData = overviewResponse.data.data
      } else if (overviewResponse.data.metrics) {
        // Respuesta directa sin wrapper success
        overviewData = overviewResponse.data
      }
    }
    
    if (overviewData && overviewData.metrics) {
      // Métricas principales
      metrics.value.totalProducts = overviewData.metrics.totalProducts || 0
      metrics.value.activeProducts = overviewData.metrics.activeProducts || 0
      metrics.value.lowStockProducts = overviewData.metrics.lowStockProducts || 0
      metrics.value.outOfStockProducts = overviewData.metrics.outOfStockProducts || 0
      metrics.value.totalInventoryValue = overviewData.metrics.totalInventoryValue || 0
      
      // Top productos vendidos
      topSellingProducts.value = overviewData.topSellingProducts || []
      
      // Productos stock bajo
      lowStockProducts.value = overviewData.lowStockProductsList || []
      
      // Procesar tendencia de movimientos
      if (overviewData.movementsTrend) {
        processMovementsTrend(overviewData.movementsTrend)
      }
    }

    // Cargar productos con métricas para calcular valores adicionales
    const productsResponse = await api.get('/inventory-reports/products', {
      params: { 
        per_page: 1000,
        category_id: selectedCategory.value || undefined
      }
    })
    
    // Manejar diferentes formatos de respuesta
    let products = []
    if (productsResponse.data) {
      if (productsResponse.data.success && productsResponse.data.data) {
        // Formato: { success: true, data: { data: [...] } } o { success: true, data: [...] }
        products = productsResponse.data.data?.data || productsResponse.data.data || []
      } else if (productsResponse.data.data) {
        // Formato paginado directo: { current_page: 1, data: [...] }
        products = productsResponse.data.data
      } else if (Array.isArray(productsResponse.data)) {
        // Formato array directo
        products = productsResponse.data
      }
    }
    
    if (products.length > 0) {
      // Calcular valor de venta potencial y margen
      let totalSaleValue = 0
      let totalCostValue = 0
      
      products.forEach(p => {
        // Parsear precios que vienen como strings
        const salePrice = parseFloat(p.sale_price) || 0
        const costPrice = parseFloat(p.cost_price) || 0
        const stock = parseInt(p.current_stock) || 0
        
        totalSaleValue += stock * salePrice
        totalCostValue += stock * costPrice
      })
      
      metrics.value.totalSaleValue = totalSaleValue
      if (!metrics.value.totalInventoryValue || parseFloat(metrics.value.totalInventoryValue) === 0) {
        metrics.value.totalInventoryValue = totalCostValue
      }
      metrics.value.potentialMargin = totalSaleValue - parseFloat(metrics.value.totalInventoryValue)
      metrics.value.marginPercent = totalSaleValue > 0 
        ? ((metrics.value.potentialMargin / totalSaleValue) * 100) 
        : 0

      // Calcular distribución por categoría
      processCategoriDistribution(products)
      
      // Calcular análisis ABC
      processABCAnalysis(products)
      
      // Identificar dead stock (productos sin ventas)
      deadStockProducts.value = products
        .filter(p => (p.total_sold === 0 || !p.total_sold) && (parseInt(p.current_stock) || 0) > 0)
        .sort((a, b) => ((parseInt(b.current_stock) || 0) * (parseFloat(b.cost_price) || 0)) - ((parseInt(a.current_stock) || 0) * (parseFloat(a.cost_price) || 0)))
        .slice(0, 10)
    }

  } catch (error) {
    console.error('Error cargando datos de inventario:', error)
  } finally {
    loading.value = false
  }
}

// Procesar distribución por categoría
const processCategoriDistribution = (products) => {
  const categoryMap = new Map()
  
  products.forEach(p => {
    const catName = p.category_name || p.category?.name || 'Sin categoría'
    const stock = parseInt(p.current_stock) || 0
    const costPrice = parseFloat(p.cost_price) || 0
    const value = stock * costPrice
    
    if (categoryMap.has(catName)) {
      categoryMap.set(catName, categoryMap.get(catName) + value)
    } else {
      categoryMap.set(catName, value)
    }
  })
  
  // Ordenar por valor descendente
  const sorted = Array.from(categoryMap.entries())
    .sort((a, b) => b[1] - a[1])
    .slice(0, 8)  // Top 8 categorías
  
  categoryDistribution.value = {
    labels: sorted.map(([name]) => name),
    data: sorted.map(([, value]) => value),
    colors: sorted.map((_, i) => categoryColors[i % categoryColors.length])
  }
}

// Procesar análisis ABC
const processABCAnalysis = (products) => {
  // Ordenar por ventas
  const sortedBySales = [...products].sort((a, b) => 
    (b.total_sold || 0) - (a.total_sold || 0)
  )
  
  const total = sortedBySales.length
  const aLimit = Math.ceil(total * 0.2)  // Top 20% = Clase A
  const bLimit = Math.ceil(total * 0.5)  // Siguiente 30% = Clase B
  
  let classA = 0, classB = 0, classC = 0
  
  sortedBySales.forEach((p, i) => {
    if (i < aLimit) classA++
    else if (i < bLimit) classB++
    else classC++
  })
  
  abcAnalysis.value = { A: classA, B: classB, C: classC }
}

// Procesar tendencia de movimientos
const processMovementsTrend = (trend) => {
  const labels = []
  const entries = []
  const exits = []
  
  // Generar últimos 14 días
  for (let i = 13; i >= 0; i--) {
    const date = new Date()
    date.setDate(date.getDate() - i)
    const dateStr = date.toISOString().split('T')[0]
    const dayLabel = date.toLocaleDateString('es-CO', { day: '2-digit', month: 'short' })
    
    labels.push(dayLabel)
    
    const dayData = trend[dateStr] || []
    let dayEntries = 0
    let dayExits = 0
    
    if (Array.isArray(dayData)) {
      dayData.forEach(m => {
        if (m.type === 'purchase' || m.type === 'adjustment' || m.type === 'return') {
          dayEntries += Math.abs(m.total || 0)
        } else {
          dayExits += Math.abs(m.total || 0)
        }
      })
    }
    
    entries.push(dayEntries)
    exits.push(dayExits)
  }
  
  movementsTrend.value = { labels, entries, exits }
}

// Exportar reporte
const exportReport = () => {
  // Por ahora solo muestra alerta, se puede implementar jsPDF
  alert('Función de exportar PDF disponible próximamente')
}

// ═══════════════════════════════════════════════════════════════
// Lifecycle
// ═══════════════════════════════════════════════════════════════

// 🤖 Store de contexto para IA de voz
const uiContextStore = useUIContextStore()

// Actualizar contexto para la IA
const actualizarContextoIA = () => {
  const formatMoney = (n) => `$${(parseFloat(n) || 0).toLocaleString('es-CO', { maximumFractionDigits: 0 })}`
  
  // KPIs principales
  const kpis = {
    totalProductos: metrics.value.totalProducts,
    productosActivos: metrics.value.activeProducts,
    valorInventario: formatMoney(metrics.value.totalInventoryValue),
    valorVentaPotencial: formatMoney(metrics.value.totalSaleValue),
    margenPotencial: formatMoney(metrics.value.potentialMargin),
    porcentajeMargen: `${metrics.value.marginPercent.toFixed(1)}%`,
    productosBajoStock: metrics.value.lowStockProducts,
    productosSinStock: metrics.value.outOfStockProducts
  }
  
  // Análisis ABC
  const analisisABC = {
    claseA: abcAnalysis.value.A,
    claseB: abcAnalysis.value.B,
    claseC: abcAnalysis.value.C,
    descripcion: `${abcAnalysis.value.A} productos alta rotación, ${abcAnalysis.value.B} media, ${abcAnalysis.value.C} baja`
  }
  
  // Distribución por categoría
  const categoriasResumen = (categoryDistribution.value.labels || []).map((label, i) => ({
    nombre: label,
    valor: formatMoney(categoryDistribution.value.data[i] || 0)
  }))
  
  // Top productos vendidos
  const topVendidosResumen = (topSellingProducts.value || []).slice(0, 5).map(p => ({
    nombre: p.name,
    vendidos: p.total_quantity_sold || 0,
    ingresos: formatMoney(p.total_revenue || 0)
  }))
  
  // Productos stock bajo
  const stockBajoResumen = (lowStockProducts.value || []).map(p => ({
    nombre: p.name,
    stockActual: p.current_stock,
    stockMinimo: p.min_stock
  }))
  
  // Dead stock (sin movimiento)
  const sinMovimientoResumen = (deadStockProducts.value || []).slice(0, 5).map(p => ({
    nombre: p.name,
    stock: p.current_stock,
    valorInmovilizado: formatMoney((parseInt(p.current_stock) || 0) * (parseFloat(p.cost_price) || 0))
  }))
  
  uiContextStore.setScreenData({
    tipoReporte: 'reports-inventario',
    modulo: 'Reportes de Inventario',
    descripcion: 'Análisis avanzado de inventario con métricas de stock, rotación, valorización y alertas',
    kpis,
    analisisABC,
    categorias: categoriasResumen,
    topVendidos: topVendidosResumen,
    stockBajo: stockBajoResumen,
    sinMovimiento: sinMovimientoResumen,
    ultimaActualizacion: new Date().toLocaleTimeString('es-CO')
  })
}

// Registrar acciones disponibles para la IA
const registrarAccionesIA = () => {
  const formatMoney = (n) => `$${(parseFloat(n) || 0).toLocaleString('es-CO', { maximumFractionDigits: 0 })}`
  
  // Consultar inventario
  uiContextStore.registerAction('consultarInventario', async ({ tipoConsulta }) => {
    try {
      let mensaje = ''
      switch (tipoConsulta) {
        case 'valor_total':
          mensaje = `💰 Valor total del inventario: ${formatMoney(metrics.value.totalInventoryValue)} (costo)\nValor potencial de venta: ${formatMoney(metrics.value.totalSaleValue)}\nMargen potencial: ${formatMoney(metrics.value.potentialMargin)} (${metrics.value.marginPercent.toFixed(1)}%)`
          break
          
        case 'stock_bajo':
          const stockBajo = lowStockProducts.value.slice(0, 5)
          mensaje = `⚠️ Productos con stock bajo (${stockBajo.length}):\n` + 
            stockBajo.map((p, i) => `${i+1}. ${p.name}: ${p.current_stock} unidades (mín: ${p.min_stock})`).join('\n')
          break
          
        case 'top_vendidos':
          const topVendidos = topSellingProducts.value.slice(0, 5)
          mensaje = `🏆 Top productos más vendidos:\n` + 
            topVendidos.map((p, i) => `${i+1}. ${p.name}: ${p.total_quantity_sold || 0} vendidos (${formatMoney(p.total_revenue || 0)})`).join('\n')
          break
          
        case 'sin_movimiento':
          const sinMov = deadStockProducts.value.slice(0, 5)
          mensaje = `📦 Productos sin movimiento (capital inmovilizado):\n` + 
            sinMov.map((p, i) => `${i+1}. ${p.name}: ${p.current_stock} unidades (${formatMoney((p.current_stock || 0) * (parseFloat(p.cost_price) || 0))} inmovilizado)`).join('\n')
          break
          
        case 'abc':
          mensaje = `📊 Análisis ABC de rotación:\n• Clase A (alta rotación): ${abcAnalysis.value.A} productos\n• Clase B (media rotación): ${abcAnalysis.value.B} productos\n• Clase C (baja rotación): ${abcAnalysis.value.C} productos`
          break
          
        default:
          mensaje = `📦 RESUMEN DE INVENTARIO:
• Total productos: ${metrics.value.totalProducts} (${metrics.value.activeProducts} activos)
• Valor inventario: ${formatMoney(metrics.value.totalInventoryValue)}
• Valor venta potencial: ${formatMoney(metrics.value.totalSaleValue)}
• Margen potencial: ${formatMoney(metrics.value.potentialMargin)} (${metrics.value.marginPercent.toFixed(1)}%)
• Stock bajo: ${metrics.value.lowStockProducts} productos
• Sin stock: ${metrics.value.outOfStockProducts} productos
• ABC: ${abcAnalysis.value.A} alta, ${abcAnalysis.value.B} media, ${abcAnalysis.value.C} baja rotación`
      }
      
      return { success: true, message: mensaje }
    } catch (err) {
      console.error('Error en consultarInventario:', err)
      return { success: false, message: 'Error al consultar inventario' }
    }
  })
  
  // Refrescar datos de inventario
  uiContextStore.registerAction('refrescarInventario', async () => {
    await loadData()
    return { success: true, message: 'Datos de inventario actualizados' }
  })
}

onMounted(() => {
  // Establecer módulo actual para la IA
  uiContextStore.setCurrentModule('reports-inventario')
  
  loadData()
  
  // Registrar acciones IA
  registrarAccionesIA()
  
  // Actualizar contexto después de cargar datos
  setTimeout(() => {
    actualizarContextoIA()
  }, 1500)
})

onBeforeUnmount(() => {
  uiContextStore.clearSelection()
})

watch(selectedCategory, () => {
  loadData()
})

// Actualizar contexto cuando cambian los datos
watch([metrics, abcAnalysis, categoryDistribution, topSellingProducts, lowStockProducts, deadStockProducts], () => {
  actualizarContextoIA()
}, { deep: true })
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
</style>
