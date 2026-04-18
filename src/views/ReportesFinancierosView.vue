<template>
  <div class="h-full flex flex-col font-sans">
    
    <!-- Header - Estilo consistente con otros reportes -->
    <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4 mb-6">
      <div>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white tracking-tight">Reportes Financieros</h1>
        <p class="text-sm text-gray-600 dark:text-zinc-400 mt-1">Análisis completo de ingresos, gastos y rentabilidad • {{ getPeriodLabel() }}</p>
      </div>
        
      <div class="flex items-center gap-3">
        <!-- Period Selector -->
        <div class="relative">
          <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
          </svg>
          <select 
            v-model="selectedPeriod" 
            @change="loadFinancialData"
            class="pl-10 pr-5 py-2.5 text-sm rounded-xl bg-white dark:bg-zinc-900 text-gray-700 dark:text-zinc-300 font-medium focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent appearance-none cursor-pointer border border-gray-200 dark:border-zinc-700 shadow-sm hover:shadow-md transition-all duration-200"
          >
            <option value="today">Hoy</option>
            <option value="week">Esta semana</option>
            <option value="month">Este mes</option>
            <option value="quarter">Este trimestre</option>
            <option value="year">Este año</option>
          </select>
        </div>
        
        <!-- Export Button -->
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

    <!-- Loading State -->
    <div v-if="loading" class="bg-white dark:bg-zinc-900 rounded-2xl p-12 text-center border border-gray-300 dark:border-zinc-800 shadow-xl dark:shadow-black/50">
      <div class="inline-flex items-center space-x-3">
        <svg class="animate-spin h-5 w-5 text-emerald-500" fill="none" viewBox="0 0 24 24">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        <span class="text-gray-500 dark:text-zinc-400 font-medium">Cargando datos financieros...</span>
      </div>
    </div>

    <!-- Error State -->
    <div v-else-if="error" class="bg-white dark:bg-zinc-900 rounded-2xl p-8 border border-gray-300 dark:border-zinc-800 shadow-xl dark:shadow-black/50">
      <div class="flex items-center space-x-4">
        <div class="w-12 h-12 rounded-xl bg-rose-50 dark:bg-rose-500/10 flex items-center justify-center">
          <svg class="w-6 h-6 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
          </svg>
        </div>
        <div>
          <h3 class="text-gray-900 dark:text-white font-semibold">Error al cargar datos financieros</h3>
          <p class="text-gray-500 dark:text-zinc-400 text-sm mt-0.5">{{ error }}</p>
        </div>
      </div>
      <button @click="loadFinancialData" class="mt-6 px-5 py-2.5 bg-slate-900 dark:bg-slate-700 hover:bg-black dark:hover:bg-slate-600 text-white rounded-xl transition-all duration-300 font-bold text-sm shadow-lg shadow-slate-400/40 dark:shadow-slate-900/50">
        Reintentar
      </button>
    </div>

    <!-- Main Content -->
    <div v-else class="space-y-6 overflow-y-auto flex-1">
      
      <!-- ═══════════════════════════════════════════════════════════════
           RESUMEN FINANCIERO PRINCIPAL - 4 KPIs Grandes
      ═══════════════════════════════════════════════════════════════ -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        
        <!-- Ingresos Totales -->
        <div class="bg-white dark:bg-zinc-900/80 backdrop-blur-sm rounded-xl px-4 py-3 border border-gray-300 dark:border-zinc-800/60 hover:border-gray-400 dark:hover:border-zinc-700/80 transition-all duration-200 hover:shadow-md dark:shadow-lg dark:shadow-black/30">
          <div class="flex items-center gap-3">
            <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0 bg-gray-100 dark:bg-zinc-800/50 border border-gray-200 dark:border-white/5">
              <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
              </svg>
            </div>
            <div class="flex-1 min-w-0">
              <p class="text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wide">Ingresos Totales</p>
              <p class="text-2xl font-bold text-gray-900 dark:text-white mt-0.5">${{ formatCurrency(financialData.totalRevenue) }}</p>
              <p class="text-xs text-emerald-600 dark:text-emerald-400 mt-0.5 flex items-center gap-1">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"/></svg>
                {{ financialData.revenueGrowth }}% vs período anterior
              </p>
            </div>
          </div>
        </div>

        <!-- Gastos Totales -->
        <div class="bg-white dark:bg-zinc-900/80 backdrop-blur-sm rounded-xl px-4 py-3 border border-gray-300 dark:border-zinc-800/60 hover:border-gray-400 dark:hover:border-zinc-700/80 transition-all duration-200 hover:shadow-md dark:shadow-lg dark:shadow-black/30">
          <div class="flex items-center gap-3">
            <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0 bg-gray-100 dark:bg-zinc-800/50 border border-gray-200 dark:border-white/5">
              <svg class="w-5 h-5 text-rose-600 dark:text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
              </svg>
            </div>
            <div class="flex-1 min-w-0">
              <p class="text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wide">Gastos Totales</p>
              <p class="text-2xl font-bold text-gray-900 dark:text-white mt-0.5">${{ formatCurrency(financialData.totalExpenses) }}</p>
              <p class="text-xs text-rose-600 dark:text-rose-400 mt-0.5 flex items-center gap-1">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/></svg>
                {{ financialData.expensesGrowth }}% vs período anterior
              </p>
            </div>
          </div>
        </div>

        <!-- Ganancia Neta -->
        <div class="bg-white dark:bg-zinc-900/80 backdrop-blur-sm rounded-xl px-4 py-3 border border-gray-300 dark:border-zinc-800/60 hover:border-gray-400 dark:hover:border-zinc-700/80 transition-all duration-200 hover:shadow-md dark:shadow-lg dark:shadow-black/30">
          <div class="flex items-center gap-3">
            <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0 bg-gray-100 dark:bg-zinc-800/50 border border-gray-200 dark:border-white/5">
              <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
              </svg>
            </div>
            <div class="flex-1 min-w-0">
              <p class="text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wide">Ganancia Neta</p>
              <p class="text-2xl font-bold mt-0.5" :class="financialData.netProfit >= 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400'">
                ${{ formatCurrency(financialData.netProfit) }}
              </p>
              <p class="text-xs text-gray-600 dark:text-zinc-400 mt-0.5">
                Margen: {{ financialData.profitMargin }}%
              </p>
            </div>
          </div>
        </div>

        <!-- ROI / Rentabilidad -->
        <div class="bg-white dark:bg-zinc-900/80 backdrop-blur-sm rounded-xl px-4 py-3 border border-gray-300 dark:border-zinc-800/60 hover:border-gray-400 dark:hover:border-zinc-700/80 transition-all duration-200 hover:shadow-md dark:shadow-lg dark:shadow-black/30">
          <div class="flex items-center gap-3">
            <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0 bg-gray-100 dark:bg-zinc-800/50 border border-gray-200 dark:border-white/5">
              <svg class="w-5 h-5 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
              </svg>
            </div>
            <div class="flex-1 min-w-0">
              <p class="text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wide">ROI Inventario</p>
              <p class="text-2xl font-bold text-gray-900 dark:text-white mt-0.5">{{ financialData.inventoryROI }}%</p>
              <p class="text-xs text-gray-600 dark:text-zinc-400 mt-0.5">
                Rotación: {{ financialData.inventoryTurnover }}x
              </p>
            </div>
          </div>
        </div>
      </div>

      <!-- ═══════════════════════════════════════════════════════════════
           DESGLOSE DE INGRESOS Y GASTOS - 2 Columnas
      ═══════════════════════════════════════════════════════════════ -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        
        <!-- Panel Ingresos -->
        <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-gray-300 dark:border-zinc-800 overflow-hidden shadow-xl dark:shadow-black/50">
          <div class="px-5 py-4 border-b border-gray-100 dark:border-zinc-800 flex items-center justify-between">
            <div class="flex items-center gap-3">
              <div class="w-10 h-10 bg-emerald-50 dark:bg-emerald-900/20 rounded-xl flex items-center justify-center">
                <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
              </div>
              <div>
                <h3 class="text-sm font-bold text-gray-900 dark:text-white">Desglose de Ingresos</h3>
                <p class="text-xs text-gray-600 dark:text-zinc-400">Por tipo de ingreso</p>
              </div>
            </div>
          </div>
          <div class="p-5 space-y-3">
            <!-- Ventas en Efectivo -->
            <div class="flex items-center justify-between py-2">
              <div class="flex items-center gap-3">
                <div class="w-3 h-3 bg-emerald-500 rounded-full"></div>
                <span class="text-sm text-gray-700 dark:text-zinc-300">Ventas en Efectivo</span>
              </div>
              <div class="text-right">
                <p class="text-sm font-semibold text-gray-900 dark:text-white">${{ formatCurrency(financialData.cashSales) }}</p>
                <p class="text-xs text-gray-400">{{ getPercentage(financialData.cashSales, financialData.totalRevenue) }}%</p>
              </div>
            </div>
            <!-- Ventas con Tarjeta -->
            <div class="flex items-center justify-between py-2">
              <div class="flex items-center gap-3">
                <div class="w-3 h-3 bg-blue-500 rounded-full"></div>
                <span class="text-sm text-gray-700 dark:text-zinc-300">Ventas con Tarjeta</span>
              </div>
              <div class="text-right">
                <p class="text-sm font-semibold text-gray-900 dark:text-white">${{ formatCurrency(financialData.cardSales) }}</p>
                <p class="text-xs text-gray-400">{{ getPercentage(financialData.cardSales, financialData.totalRevenue) }}%</p>
              </div>
            </div>
            <!-- Ventas por Transferencia -->
            <div class="flex items-center justify-between py-2">
              <div class="flex items-center gap-3">
                <div class="w-3 h-3 bg-purple-500 rounded-full"></div>
                <span class="text-sm text-gray-700 dark:text-zinc-300">Transferencias</span>
              </div>
              <div class="text-right">
                <p class="text-sm font-semibold text-gray-900 dark:text-white">${{ formatCurrency(financialData.transferSales) }}</p>
                <p class="text-xs text-gray-400">{{ getPercentage(financialData.transferSales, financialData.totalRevenue) }}%</p>
              </div>
            </div>
            <!-- Abonos Creditienda -->
            <div class="flex items-center justify-between py-2">
              <div class="flex items-center gap-3">
                <div class="w-3 h-3 bg-amber-500 rounded-full"></div>
                <span class="text-sm text-gray-700 dark:text-zinc-300">Abonos CrediTienda</span>
              </div>
              <div class="text-right">
                <p class="text-sm font-semibold text-gray-900 dark:text-white">${{ formatCurrency(financialData.creditPayments) }}</p>
                <p class="text-xs text-gray-400">{{ getPercentage(financialData.creditPayments, financialData.totalRevenue) }}%</p>
              </div>
            </div>
            <!-- Separador -->
            <div class="border-t border-gray-100 dark:border-zinc-800 my-2"></div>
            <!-- Devoluciones (resta) -->
            <div class="flex items-center justify-between py-2">
              <div class="flex items-center gap-3">
                <div class="w-3 h-3 bg-rose-500 rounded-full"></div>
                <span class="text-sm text-gray-700 dark:text-zinc-300">Devoluciones</span>
              </div>
              <div class="text-right">
                <p class="text-sm font-semibold text-rose-600 dark:text-rose-400">-${{ formatCurrency(financialData.returns) }}</p>
                <p class="text-xs text-gray-400">{{ financialData.returnsCount }} devoluciones</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Panel Gastos -->
        <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-gray-300 dark:border-zinc-800 overflow-hidden shadow-xl dark:shadow-black/50">
          <div class="px-5 py-4 border-b border-gray-100 dark:border-zinc-800 flex items-center justify-between">
            <div class="flex items-center gap-3">
              <div class="w-10 h-10 bg-rose-50 dark:bg-rose-900/20 rounded-xl flex items-center justify-center">
                <svg class="w-5 h-5 text-rose-600 dark:text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                </svg>
              </div>
              <div>
                <h3 class="text-sm font-bold text-gray-900 dark:text-white">Desglose de Gastos</h3>
                <p class="text-xs text-gray-600 dark:text-zinc-400">Por categoría</p>
              </div>
            </div>
          </div>
          <div class="p-5 space-y-3">
            <div v-for="(category, index) in financialData.expensesByCategory" :key="index" class="flex items-center justify-between py-2">
              <div class="flex items-center gap-3">
                <div class="w-3 h-3 rounded-full" :style="{ backgroundColor: category.color }"></div>
                <span class="text-sm text-gray-700 dark:text-zinc-300">{{ category.name }}</span>
              </div>
              <div class="text-right">
                <p class="text-sm font-semibold text-gray-900 dark:text-white">${{ formatCurrency(category.amount) }}</p>
                <p class="text-xs text-gray-400">{{ category.count }} gastos</p>
              </div>
            </div>
            <div v-if="financialData.expensesByCategory.length === 0" class="text-center py-4">
              <p class="text-sm text-gray-400 dark:text-zinc-500">No hay gastos registrados en este período</p>
            </div>
          </div>
        </div>
      </div>

      <!-- ═══════════════════════════════════════════════════════════════
           FLUJO DE CAJA Y MÉTRICAS ADICIONALES
      ═══════════════════════════════════════════════════════════════ -->
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Flujo de Caja -->
        <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-gray-300 dark:border-zinc-800 overflow-hidden shadow-xl dark:shadow-black/50">
          <div class="px-5 py-4 border-b border-gray-100 dark:border-zinc-800">
            <h3 class="text-sm font-bold text-gray-900 dark:text-white">Flujo de Caja</h3>
            <p class="text-xs text-gray-600 dark:text-zinc-400">Movimiento de efectivo</p>
          </div>
          <div class="p-5 space-y-4">
            <div class="flex items-center justify-between">
              <span class="text-sm text-gray-600 dark:text-zinc-400">Efectivo Entrante</span>
              <span class="text-sm font-semibold text-emerald-600 dark:text-emerald-400">+${{ formatCurrency(financialData.cashInflow) }}</span>
            </div>
            <div class="flex items-center justify-between">
              <span class="text-sm text-gray-600 dark:text-zinc-400">Efectivo Saliente</span>
              <span class="text-sm font-semibold text-rose-600 dark:text-rose-400">-${{ formatCurrency(financialData.cashOutflow) }}</span>
            </div>
            <div class="border-t border-gray-100 dark:border-zinc-800 pt-3">
              <div class="flex items-center justify-between">
                <span class="text-sm font-medium text-gray-900 dark:text-white">Flujo Neto</span>
                <span class="text-lg font-bold" :class="financialData.netCashFlow >= 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400'">
                  ${{ formatCurrency(financialData.netCashFlow) }}
                </span>
              </div>
            </div>
          </div>
        </div>

        <!-- Cuentas por Cobrar -->
        <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-gray-300 dark:border-zinc-800 overflow-hidden shadow-xl dark:shadow-black/50">
          <div class="px-5 py-4 border-b border-gray-100 dark:border-zinc-800">
            <h3 class="text-sm font-bold text-gray-900 dark:text-white">Cuentas por Cobrar</h3>
            <p class="text-xs text-gray-600 dark:text-zinc-400">Cartera CrediTienda</p>
          </div>
          <div class="p-5 space-y-4">
            <div class="flex items-center justify-between">
              <span class="text-sm text-gray-600 dark:text-zinc-400">Total Cartera</span>
              <span class="text-sm font-semibold text-gray-900 dark:text-white">${{ formatCurrency(financialData.totalReceivables) }}</span>
            </div>
            <div class="flex items-center justify-between">
              <span class="text-sm text-gray-600 dark:text-zinc-400">Clientes con Deuda</span>
              <span class="text-sm font-semibold text-gray-900 dark:text-white">{{ financialData.customersWithDebt }}</span>
            </div>
            <div class="flex items-center justify-between">
              <span class="text-sm text-gray-600 dark:text-zinc-400">Mora Promedio</span>
              <span class="text-sm font-semibold text-amber-600 dark:text-amber-400">{{ financialData.avgDaysOverdue }} días</span>
            </div>
            <div class="border-t border-gray-100 dark:border-zinc-800 pt-3">
              <div class="flex items-center justify-between">
                <span class="text-sm font-medium text-gray-900 dark:text-white">Recaudado Hoy</span>
                <span class="text-lg font-bold text-emerald-600 dark:text-emerald-400">
                  ${{ formatCurrency(financialData.todayCreditPayments) }}
                </span>
              </div>
            </div>
          </div>
        </div>

        <!-- Valor del Inventario -->
        <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-gray-300 dark:border-zinc-800 overflow-hidden shadow-xl dark:shadow-black/50">
          <div class="px-5 py-4 border-b border-gray-100 dark:border-zinc-800">
            <h3 class="text-sm font-bold text-gray-900 dark:text-white">Valor del Inventario</h3>
            <p class="text-xs text-gray-600 dark:text-zinc-400">Activo corriente</p>
          </div>
          <div class="p-5 space-y-4">
            <div class="flex items-center justify-between">
              <span class="text-sm text-gray-600 dark:text-zinc-400">Valor a Costo</span>
              <span class="text-sm font-semibold text-gray-900 dark:text-white">${{ formatCurrency(financialData.inventoryCostValue) }}</span>
            </div>
            <div class="flex items-center justify-between">
              <span class="text-sm text-gray-600 dark:text-zinc-400">Valor a Precio Venta</span>
              <span class="text-sm font-semibold text-gray-900 dark:text-white">${{ formatCurrency(financialData.inventorySaleValue) }}</span>
            </div>
            <div class="flex items-center justify-between">
              <span class="text-sm text-gray-600 dark:text-zinc-400">Productos Activos</span>
              <span class="text-sm font-semibold text-gray-900 dark:text-white">{{ financialData.activeProducts }}</span>
            </div>
            <div class="border-t border-gray-100 dark:border-zinc-800 pt-3">
              <div class="flex items-center justify-between">
                <span class="text-sm font-medium text-gray-900 dark:text-white">Ganancia Potencial</span>
                <span class="text-lg font-bold text-emerald-600 dark:text-emerald-400">
                  ${{ formatCurrency(financialData.inventorySaleValue - financialData.inventoryCostValue) }}
                </span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- ═══════════════════════════════════════════════════════════════
           TABLA DE TRANSACCIONES RECIENTES
      ═══════════════════════════════════════════════════════════════ -->
      <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-gray-300 dark:border-zinc-800 overflow-hidden shadow-xl dark:shadow-black/50">
        <div class="px-5 py-4 border-b border-gray-100 dark:border-zinc-800 flex items-center justify-between">
          <div>
            <h3 class="text-sm font-bold text-gray-900 dark:text-white">Movimientos Financieros Recientes</h3>
            <p class="text-xs text-gray-600 dark:text-zinc-400">Últimas transacciones de ingresos y gastos</p>
          </div>
          <button @click="loadMoreTransactions" class="text-sm text-blue-600 dark:text-blue-400 hover:underline font-medium">
            Ver todos
          </button>
        </div>
        <div class="overflow-x-auto">
          <table class="w-full">
            <thead class="bg-gray-50 dark:bg-zinc-900/50">
              <tr>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 dark:text-zinc-400 uppercase tracking-wider">Fecha</th>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 dark:text-zinc-400 uppercase tracking-wider">Tipo</th>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 dark:text-zinc-400 uppercase tracking-wider">Descripción</th>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 dark:text-zinc-400 uppercase tracking-wider">Método</th>
                <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500 dark:text-zinc-400 uppercase tracking-wider">Monto</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-zinc-800">
              <tr v-for="(tx, index) in financialData.recentTransactions" :key="index" class="hover:bg-gray-50 dark:hover:bg-zinc-800/50 transition-colors">
                <td class="px-5 py-3 text-sm text-gray-700 dark:text-zinc-300">{{ formatDate(tx.date) }}</td>
                <td class="px-5 py-3">
                  <span :class="tx.type === 'income' ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400' : 'bg-rose-50 text-rose-700 dark:bg-rose-900/30 dark:text-rose-400'" class="px-2 py-1 rounded-md text-xs font-medium">
                    {{ tx.type === 'income' ? 'Ingreso' : 'Gasto' }}
                  </span>
                </td>
                <td class="px-5 py-3 text-sm text-gray-700 dark:text-zinc-300">{{ tx.description }}</td>
                <td class="px-5 py-3 text-sm text-gray-500 dark:text-zinc-400">{{ tx.paymentMethod }}</td>
                <td class="px-5 py-3 text-sm font-semibold text-right" :class="tx.type === 'income' ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400'">
                  {{ tx.type === 'income' ? '+' : '-' }}${{ formatCurrency(tx.amount) }}
                </td>
              </tr>
              <tr v-if="financialData.recentTransactions.length === 0">
                <td colspan="5" class="px-5 py-8 text-center text-sm text-gray-400 dark:text-zinc-500">
                  No hay transacciones en este período
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import api from '@/services/api'
import { useToast } from '@/composables/useToast.js'

const { showWarning } = useToast()

// Estado
const loading = ref(true)
const error = ref(null)
const selectedPeriod = ref('month')

// Datos financieros
const financialData = ref({
  // KPIs Principales
  totalRevenue: 0,
  totalExpenses: 0,
  netProfit: 0,
  profitMargin: 0,
  revenueGrowth: 0,
  expensesGrowth: 0,
  inventoryROI: 0,
  inventoryTurnover: 0,
  
  // Desglose Ingresos
  cashSales: 0,
  cardSales: 0,
  transferSales: 0,
  creditPayments: 0,
  returns: 0,
  returnsCount: 0,
  
  // Gastos por Categoría
  expensesByCategory: [],
  
  // Flujo de Caja
  cashInflow: 0,
  cashOutflow: 0,
  netCashFlow: 0,
  
  // Cuentas por Cobrar
  totalReceivables: 0,
  customersWithDebt: 0,
  avgDaysOverdue: 0,
  todayCreditPayments: 0,
  
  // Inventario
  inventoryCostValue: 0,
  inventorySaleValue: 0,
  activeProducts: 0,
  
  // Transacciones Recientes
  recentTransactions: []
})

// Métodos
const formatCurrency = (value) => {
  const num = parseFloat(value) || 0
  return Math.round(num).toLocaleString('es-CO')
}

const formatDate = (dateStr) => {
  if (!dateStr) return ''
  const date = new Date(dateStr)
  return date.toLocaleDateString('es-CO', { day: '2-digit', month: 'short', hour: '2-digit', minute: '2-digit' })
}

const getPercentage = (value, total) => {
  if (!total || total === 0) return 0
  return Math.round((value / total) * 100)
}

const getPeriodLabel = () => {
  const labels = {
    today: 'Hoy',
    week: 'Esta semana',
    month: 'Este mes',
    quarter: 'Este trimestre',
    year: 'Este año'
  }
  return labels[selectedPeriod.value] || 'Este mes'
}

const loadFinancialData = async () => {
  loading.value = true
  error.value = null
  
  try {
    // Usar el nuevo endpoint financiero optimizado
    const response = await api.get('/optimized/financial', { 
      params: { period: selectedPeriod.value } 
    })
    
    const data = response.data.data || response.data || {}
    
    // Actualizar estado con todos los datos (parseando strings a números)
    financialData.value = {
      // KPIs Principales
      totalRevenue: parseFloat(data.totalRevenue) || 0,
      totalExpenses: parseFloat(data.totalExpenses) || 0,
      netProfit: parseFloat(data.netProfit) || 0,
      profitMargin: parseFloat(data.profitMargin) || 0,
      revenueGrowth: parseFloat(data.revenueGrowth) || 0,
      expensesGrowth: parseFloat(data.expensesGrowth) || 0,
      inventoryROI: parseFloat(data.inventoryROI) || 0,
      inventoryTurnover: parseFloat(data.inventoryTurnover) || 0,
      
      // Desglose Ingresos
      cashSales: parseFloat(data.cashSales) || 0,
      cardSales: parseFloat(data.cardSales) || 0,
      transferSales: parseFloat(data.transferSales) || 0,
      creditPayments: parseFloat(data.todayCreditPayments) || 0,
      returns: parseFloat(data.returns) || 0,
      returnsCount: parseInt(data.returnsCount) || 0,
      
      // Gastos por Categoría
      expensesByCategory: data.expensesByCategory || [],
      
      // Flujo de Caja
      cashInflow: parseFloat(data.cashInflow) || 0,
      cashOutflow: parseFloat(data.cashOutflow) || 0,
      netCashFlow: parseFloat(data.netCashFlow) || 0,
      
      // Cuentas por Cobrar
      totalReceivables: parseFloat(data.totalReceivables) || 0,
      customersWithDebt: parseInt(data.customersWithDebt) || 0,
      avgDaysOverdue: parseInt(data.avgDaysOverdue) || 0,
      todayCreditPayments: parseFloat(data.todayCreditPayments) || 0,
      
      // Inventario
      inventoryCostValue: parseFloat(data.inventoryCostValue) || 0,
      inventorySaleValue: parseFloat(data.inventorySaleValue) || 0,
      activeProducts: parseInt(data.activeProducts) || 0,
      
      // Transacciones
      recentTransactions: data.recentTransactions || []
    }
    
  } catch (err) {
    console.error('Error cargando datos financieros:', err)
    error.value = err.response?.data?.message || 'Error al cargar los datos financieros'
  } finally {
    loading.value = false
  }
}

const exportReport = () => {
  if (!reportData.value) {
    showWarning('No hay datos para exportar')
    return
  }
  const data = reportData.value
  const lines = [
    `Reporte Financiero - ${selectedPeriod.value}`,
    '',
    `Ingresos Totales,$${data.total_income || 0}`,
    `Gastos Totales,$${data.total_expenses || 0}`,
    `Utilidad Neta,$${data.net_profit || 0}`,
    `Margen,%${data.profit_margin || 0}`
  ]
  const csv = lines.join('\n')
  const blob = new Blob(['\uFEFF' + csv], { type: 'text/csv;charset=utf-8;' })
  const url = URL.createObjectURL(blob)
  const a = document.createElement('a')
  a.href = url
  a.download = `reporte_financiero_${new Date().toISOString().slice(0,10)}.csv`
  a.click()
  URL.revokeObjectURL(url)
}

const loadMoreTransactions = () => {
  // Redirigir a módulo de facturas para ver transacciones completas
  window.dispatchEvent(new CustomEvent('navigate-module', { detail: { module: 'invoices' } }))
}

onMounted(() => {
  loadFinancialData()
})
</script>

<style scoped>
/* Estilos adicionales si son necesarios */
</style>
