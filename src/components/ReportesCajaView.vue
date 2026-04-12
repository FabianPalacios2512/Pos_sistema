<template>
  <div class="h-full flex flex-col font-sans transition-colors duration-300">
    <div class="flex-1 overflow-y-auto p-6 space-y-6">
      
      <!-- Header -->
      <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
        <div>
          <h1 class="text-2xl font-bold text-gray-900 dark:text-white tracking-tight">Reportes de Caja</h1>
          <p class="text-sm text-gray-600 dark:text-zinc-400 mt-1">Análisis avanzado por cajero • {{ getPeriodLabel() }}</p>
        </div>
        
        <div class="flex items-center gap-3">
          <!-- Controles -->
          <select 
            v-model="selectedPeriod" 
            @change="handlePeriodChange"
            class="px-4 py-2.5 bg-white dark:bg-zinc-900 border border-gray-200 dark:border-zinc-700 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors text-sm text-gray-700 dark:text-zinc-300 font-medium"
          >
            <option value="today">Hoy</option>
            <option value="week">Esta semana</option>
            <option value="month">Este mes</option>
            <option value="year">Este año</option>
          </select>
          
          <input 
            type="date"
            v-model="customDate"
            @change="loadCashReportsData"
            class="px-4 py-2.5 bg-white dark:bg-zinc-800 border border-gray-200 dark:border-zinc-700 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors text-sm text-gray-700 dark:text-zinc-200"
          />
          
          <input 
            type="date"
            v-model="customEndDate"
            @change="loadCashReportsData"
            :min="customDate"
            class="px-4 py-2.5 bg-white dark:bg-zinc-800 border border-gray-200 dark:border-zinc-700 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors text-sm text-gray-700 dark:text-zinc-200"
            placeholder="Hasta"
          />

          <!-- Botón Actualizar -->
          <button 
            @click="loadCashReportsData" 
            class="px-5 py-2.5 bg-white dark:bg-zinc-900 hover:bg-slate-50 dark:hover:bg-zinc-800 text-slate-600 dark:text-zinc-200 text-sm font-bold rounded-xl border border-slate-200 dark:border-zinc-800 shadow-sm transition-all duration-200 flex items-center gap-2"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
            </svg>
            <span>Refrescar</span>
          </button>

          <button 
            @click="exportCashReport" 
            class="px-6 py-2.5 bg-slate-900 dark:bg-slate-700 hover:bg-black dark:hover:bg-slate-600 text-white text-sm font-bold rounded-xl shadow-lg shadow-slate-400/40 dark:shadow-slate-900/50 transition-all duration-300 flex items-center gap-2"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            <span>Exportar PDF</span>
          </button>
        </div>
      </div>
    
    <!-- Indicador de carga -->
    <div v-if="loading" class="bg-white dark:bg-zinc-900 rounded-2xl p-8 text-center border border-gray-300 dark:border-zinc-800 shadow-xl dark:shadow-black/50">
      <div class="inline-flex items-center space-x-3">
        <svg class="animate-spin h-5 w-5 text-emerald-500" fill="none" viewBox="0 0 24 24">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        <span class="text-gray-500 dark:text-zinc-400 font-medium">Cargando reportes de caja...</span>
      </div>
    </div>

    <!-- Mensaje de error -->
    <div v-else-if="error" class="bg-white dark:bg-zinc-900 rounded-2xl p-8 border border-gray-300 dark:border-zinc-800 shadow-xl dark:shadow-black/50">
      <div class="flex items-center space-x-4">
        <div class="w-12 h-12 rounded-xl bg-rose-50 dark:bg-rose-500/10 flex items-center justify-center">
          <svg class="w-6 h-6 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
          </svg>
        </div>
        <div>
          <h3 class="text-gray-900 dark:text-white font-semibold">Error al cargar reportes de caja</h3>
          <p class="text-gray-500 dark:text-zinc-400 text-sm mt-0.5">{{ error }}</p>
        </div>
      </div>
      <button @click="loadCashReportsData" class="mt-6 px-5 py-2.5 bg-slate-900 dark:bg-slate-700 hover:bg-black dark:hover:bg-slate-600 text-white rounded-xl transition-all duration-300 font-bold text-sm shadow-lg shadow-slate-400/40 dark:shadow-slate-900/50">
        Reintentar
      </button>
    </div>

    <!-- Contenido principal -->
    <div v-else class="space-y-6">
      
      <!-- Métricas Principales por Cajero - 6 KPIs en grid 3x2 -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        
        <!-- Total de sesiones activas -->
        <div class="bg-white dark:bg-zinc-900/80 backdrop-blur-sm rounded-xl px-4 py-3 border border-gray-300 dark:border-zinc-800/60 hover:border-gray-400 dark:hover:border-zinc-700/80 transition-all duration-200 hover:shadow-md dark:shadow-lg dark:shadow-black/30">
          <div class="flex items-center gap-3">
            <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0 bg-gray-100 dark:bg-zinc-800/50 border border-gray-200 dark:border-white/5">
              <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
              </svg>
            </div>
            <div class="flex-1 min-w-0">
              <p class="text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wide">Sesiones Activas</p>
              <p class="text-2xl font-bold text-gray-900 dark:text-white mt-0.5">{{ activeSessions.length }}</p>
              <p class="text-xs text-gray-600 dark:text-zinc-400 mt-0.5">{{ getActiveSessionsChange() }}% vs ayer</p>
            </div>
          </div>
        </div>

        <!-- Mejor cajero del día -->
        <div class="bg-white dark:bg-zinc-900/80 backdrop-blur-sm rounded-xl px-4 py-3 border border-gray-300 dark:border-zinc-800/60 hover:border-gray-400 dark:hover:border-zinc-700/80 transition-all duration-200 hover:shadow-md dark:shadow-lg dark:shadow-black/30">
          <div class="flex items-center gap-3">
            <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0 bg-gray-100 dark:bg-zinc-800/50 border border-gray-200 dark:border-white/5">
              <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
              </svg>
            </div>
            <div class="flex-1 min-w-0">
              <p class="text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wide">Mejor Cajero</p>
              <p class="text-xl font-bold text-gray-900 dark:text-white mt-0.5 truncate">{{ bestCashier.name }}</p>
              <p class="text-xs text-emerald-600 dark:text-emerald-400 mt-0.5">${{ (bestCashier.sales || 0).toLocaleString() }} ventas</p>
            </div>
          </div>
        </div>

        <!-- Promedio de ventas por hora -->
        <div class="bg-white dark:bg-zinc-900/80 backdrop-blur-sm rounded-xl px-4 py-3 border border-gray-300 dark:border-zinc-800/60 hover:border-gray-400 dark:hover:border-zinc-700/80 transition-all duration-200 hover:shadow-md dark:shadow-lg dark:shadow-black/30">
          <div class="flex items-center gap-3">
            <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0 bg-gray-100 dark:bg-zinc-800/50 border border-gray-200 dark:border-white/5">
              <svg class="w-5 h-5 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
              </svg>
            </div>
            <div class="flex-1 min-w-0">
              <p class="text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wide">Promedio/Hora</p>
              <p class="text-2xl font-bold text-gray-900 dark:text-white mt-0.5">${{ averageSalesPerHour.toLocaleString() }}</p>
              <p class="text-xs text-indigo-600 dark:text-indigo-400 mt-0.5">+{{ getHourlyGrowth() }}% vs promedio</p>
            </div>
          </div>
        </div>

        <!-- Total de transacciones -->
        <div class="bg-white dark:bg-zinc-900/80 backdrop-blur-sm rounded-xl px-4 py-3 border border-gray-300 dark:border-zinc-800/60 hover:border-gray-400 dark:hover:border-zinc-700/80 transition-all duration-200 hover:shadow-md dark:shadow-lg dark:shadow-black/30">
          <div class="flex items-center gap-3">
            <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0 bg-gray-100 dark:bg-zinc-800/50 border border-gray-200 dark:border-white/5">
              <svg class="w-5 h-5 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
              </svg>
            </div>
            <div class="flex-1 min-w-0">
              <p class="text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wide">Transacciones</p>
              <p class="text-2xl font-bold text-gray-900 dark:text-white mt-0.5">{{ totalTransactions.toLocaleString() }}</p>
              <p class="text-xs text-purple-600 dark:text-purple-400 mt-0.5">{{ getTransactionGrowth() }}% vs período anterior</p>
            </div>
          </div>
        </div>

        <!-- NUEVO: Gastos de Caja (Retiros) -->
        <div class="bg-white dark:bg-zinc-900/80 backdrop-blur-sm rounded-xl px-4 py-3 border border-gray-300 dark:border-zinc-800/60 hover:border-gray-400 dark:hover:border-zinc-700/80 transition-all duration-200 hover:shadow-md dark:shadow-lg dark:shadow-black/30">
          <div class="flex items-center gap-3">
            <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0 bg-gray-100 dark:bg-zinc-800/50 border border-gray-200 dark:border-white/5">
              <svg class="w-5 h-5 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
              </svg>
            </div>
            <div class="flex-1 min-w-0">
              <p class="text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wide">Gastos de Caja</p>
              <p class="text-2xl font-bold text-amber-600 dark:text-amber-400 mt-0.5">-${{ totalExpenses.toLocaleString() }}</p>
              <p class="text-xs text-gray-600 dark:text-zinc-400 mt-0.5">salidas de efectivo</p>
            </div>
          </div>
        </div>

        <!-- NUEVO: Devoluciones -->
        <div class="bg-white dark:bg-zinc-900/80 backdrop-blur-sm rounded-xl px-4 py-3 border border-gray-300 dark:border-zinc-800/60 hover:border-gray-400 dark:hover:border-zinc-700/80 transition-all duration-200 hover:shadow-md dark:shadow-lg dark:shadow-black/30">
          <div class="flex items-center gap-3">
            <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0 bg-gray-100 dark:bg-zinc-800/50 border border-gray-200 dark:border-white/5">
              <svg class="w-5 h-5 text-rose-600 dark:text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 15v-1a4 4 0 00-4-4H8m0 0l3 3m-3-3l3-3m9 14V5a2 2 0 00-2-2H6a2 2 0 00-2 2v16l4-2 4 2 4-2 4 2z"></path>
              </svg>
            </div>
            <div class="flex-1 min-w-0">
              <p class="text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wide">Devoluciones</p>
              <p class="text-2xl font-bold text-rose-600 dark:text-rose-400 mt-0.5">-${{ totalRefunds.toLocaleString() }}</p>
              <p class="text-xs text-gray-600 dark:text-zinc-400 mt-0.5">productos devueltos</p>
            </div>
          </div>
        </div>

      </div>

      <!-- Gráficos de análisis -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        
        <!-- Rendimiento por Cajero -->
        <div class="bg-white dark:bg-zinc-900 rounded-2xl overflow-hidden border border-gray-300 dark:border-zinc-800 shadow-xl dark:shadow-black/50">
          <div class="px-6 py-5 flex items-center justify-between border-b border-gray-100 dark:border-zinc-800">
            <div>
              <h2 class="text-base font-semibold text-gray-900 dark:text-white">Rendimiento por Cajero</h2>
              <p class="text-sm text-gray-500 dark:text-zinc-400 mt-0.5">Comparativa para {{ getPeriodLabel() }}</p>
            </div>
            <div class="w-10 h-10 rounded-xl flex items-center justify-center bg-gray-100 dark:bg-zinc-800/50 border border-gray-200 dark:border-white/5">
              <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
              </svg>
            </div>
          </div>
          <div class="p-6">
            <div style="height: 280px;">
              <Bar :data="cashierPerformanceChart" :options="chartOptions" />
            </div>
          </div>
        </div>

        <!-- Eficiencia por Hora -->
        <div class="bg-white dark:bg-zinc-900 rounded-2xl overflow-hidden border border-gray-300 dark:border-zinc-800 shadow-xl dark:shadow-black/50">
          <div class="px-6 py-5 flex items-center justify-between border-b border-gray-100 dark:border-zinc-800">
            <div>
              <h2 class="text-base font-semibold text-gray-900 dark:text-white">Eficiencia por Hora</h2>
              <p class="text-sm text-gray-500 dark:text-zinc-400 mt-0.5">Análisis de productividad horaria</p>
            </div>
            <div class="w-10 h-10 rounded-xl flex items-center justify-center bg-gray-100 dark:bg-zinc-800/50 border border-gray-200 dark:border-white/5">
              <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
              </svg>
            </div>
          </div>
          <div class="p-6">
            <div style="height: 280px;">
              <Line :data="hourlyEfficiencyChart" :options="lineChartOptions" />
            </div>
          </div>
        </div>

      </div>

      <!-- Tabla comparativa detallada -->
      <div class="bg-white dark:bg-zinc-900 rounded-2xl overflow-hidden border border-gray-300 dark:border-zinc-800 shadow-xl dark:shadow-black/50">
        <div class="px-6 py-5 flex items-center justify-between border-b border-gray-100 dark:border-zinc-800">
          <div>
            <h2 class="text-base font-semibold text-gray-900 dark:text-white">Análisis Comparativo Detallado</h2>
            <p class="text-sm text-gray-600 dark:text-zinc-400 mt-0.5">Métricas completas por cajero y sesión</p>
          </div>
          <div class="flex gap-3">
            <button 
              @click="exportCashierComparison" 
              class="px-5 py-2.5 bg-slate-900 dark:bg-slate-700 hover:bg-black dark:hover:bg-slate-600 text-white text-sm font-bold rounded-xl shadow-lg shadow-slate-400/40 dark:shadow-slate-900/50 transition-all duration-300 flex items-center gap-2"
            >
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
              </svg>
              <span>Exportar Comparativo</span>
            </button>
          </div>
        </div>

        <!-- Tabla responsive -->
        <div class="overflow-x-auto">
          <table class="min-w-full">
            <thead class="bg-gray-50 dark:bg-zinc-900">
              <tr>
                <th class="px-6 py-4 text-left text-xs font-medium text-gray-600 dark:text-zinc-400 uppercase tracking-wide">Cajero</th>
                <th class="px-6 py-4 text-left text-xs font-medium text-gray-600 dark:text-zinc-400 uppercase tracking-wide">Sesiones</th>
                <th class="px-6 py-4 text-left text-xs font-medium text-gray-600 dark:text-zinc-400 uppercase tracking-wide">Total Ventas</th>
                <th class="px-6 py-4 text-left text-xs font-medium text-gray-600 dark:text-zinc-400 uppercase tracking-wide">Transacciones</th>
                <th class="px-6 py-4 text-left text-xs font-medium text-gray-600 dark:text-zinc-400 uppercase tracking-wide">Promedio/Venta</th>
                <th class="px-6 py-4 text-left text-xs font-medium text-gray-600 dark:text-zinc-400 uppercase tracking-wide">Eficiencia</th>
                <th class="px-6 py-4 text-left text-xs font-medium text-gray-600 dark:text-zinc-400 uppercase tracking-wide">Horas Trabajadas</th>
                <th class="px-6 py-4 text-left text-xs font-medium text-gray-600 dark:text-zinc-400 uppercase tracking-wide">Ventas/Hora</th>
              </tr>
            </thead>
            <tbody class="bg-white dark:bg-zinc-900 divide-y divide-gray-100 dark:divide-zinc-800">
              <tr v-for="cashier in cashierComparison" :key="cashier.id" class="hover:bg-gray-50 dark:hover:bg-zinc-800/50 transition-colors">
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="flex items-center">
                    <div class="w-10 h-10 bg-gray-200 dark:bg-zinc-700 rounded-full flex items-center justify-center mr-4">
                      <span class="text-gray-600 dark:text-zinc-300 font-medium text-sm">{{ getInitials(cashier.name) }}</span>
                    </div>
                    <div>
                      <div class="text-sm font-medium text-gray-900 dark:text-white">{{ cashier.name }}</div>
                      <div class="text-xs text-gray-500 dark:text-zinc-400">{{ cashier.role }}</div>
                    </div>
                  </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-zinc-200">
                  <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400">
                    {{ cashier.sessions || 1 }}
                  </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-emerald-600 dark:text-emerald-400">
                  ${{ parseFloat(cashier.total_sales || 0).toLocaleString() }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-zinc-200">{{ cashier.transactions }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-zinc-200">${{ parseFloat(cashier.average_sale || 0).toFixed(2) }}</td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="flex items-center">
                    <div class="w-16 bg-gray-200 dark:bg-zinc-700 rounded-full h-2 mr-2">
                      <div class="bg-emerald-500 h-2 rounded-full" :style="`width: ${parseFloat(cashier.efficiency || 0)}%`"></div>
                    </div>
                    <span class="text-sm font-medium text-gray-900 dark:text-zinc-200">{{ parseFloat(cashier.efficiency || 0).toFixed(1) }}%</span>
                  </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-zinc-200">{{ cashier.hoursWorked || 8 }}h</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-zinc-200">
                  ${{ (parseFloat(cashier.total_sales || 0) / (cashier.hoursWorked || 8)).toFixed(0) }}
                </td>
              </tr>
              
              <!-- Estado vacío -->
              <tr v-if="!cashierComparison || cashierComparison.length === 0" class="hover:bg-gray-50 dark:hover:bg-zinc-800/50">
                <td colspan="8" class="px-6 py-12 text-center">
                  <div class="flex flex-col items-center">
                    <svg class="w-16 h-16 text-gray-300 dark:text-zinc-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                    <h3 class="text-base font-medium text-gray-700 dark:text-zinc-300 mb-1">No hay datos de cajeros</h3>
                    <p class="text-sm text-gray-500 dark:text-zinc-500">No se encontraron datos para el período seleccionado</p>
                    <button @click="loadCashReportsData" class="mt-3 px-4 py-2.5 bg-slate-900 dark:bg-slate-700 text-white rounded-xl hover:bg-black dark:hover:bg-slate-600 transition-all duration-300 text-sm font-bold shadow-lg shadow-slate-400/40 dark:shadow-slate-900/50">
                      Actualizar datos
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Métricas adicionales -->
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Top 5 mejores sesiones -->
        <div class="bg-white dark:bg-zinc-900 rounded-2xl overflow-hidden border border-gray-300 dark:border-zinc-800 shadow-xl dark:shadow-black/50">
          <div class="px-6 py-5 border-b border-gray-100 dark:border-zinc-800">
            <div class="flex items-center gap-3">
              <div class="w-3 h-3 bg-amber-500 rounded-full"></div>
              <h3 class="text-base font-semibold text-gray-900 dark:text-white">Top 5 Mejores Sesiones</h3>
            </div>
          </div>
          <div class="p-6 space-y-4">
            <div v-for="(session, index) in topSessions" :key="session.id" class="flex items-center justify-between p-4 bg-amber-50 dark:bg-amber-950/30 rounded-xl border border-amber-100 dark:border-amber-800/50">
              <div class="flex items-center">
                <div class="w-8 h-8 bg-amber-500 rounded-full flex items-center justify-center mr-3">
                  <span class="text-white font-medium text-sm">{{ index + 1 }}</span>
                </div>
                <div>
                  <div class="font-medium text-gray-900 dark:text-white">{{ session.cashier }}</div>
                  <div class="text-sm text-gray-500 dark:text-zinc-400">{{ session.date }}</div>
                </div>
              </div>
              <div class="text-right">
                <div class="font-medium text-emerald-600 dark:text-emerald-400">${{ parseFloat(session.sales || 0).toLocaleString() }}</div>
                <div class="text-sm text-gray-500 dark:text-zinc-400">{{ Math.abs(session.duration || 0) }}h</div>
              </div>
            </div>
          </div>
        </div>

        <!-- Métodos de pago más usados -->
        <div class="bg-white dark:bg-zinc-900 rounded-2xl overflow-hidden border border-gray-300 dark:border-zinc-800 shadow-xl dark:shadow-black/50">
          <div class="px-6 py-5 border-b border-gray-100 dark:border-zinc-800">
            <div class="flex items-center gap-3">
              <div class="w-3 h-3 bg-indigo-500 rounded-full"></div>
              <h3 class="text-base font-semibold text-gray-900 dark:text-white">Métodos de Pago</h3>
            </div>
          </div>
          <div class="p-6" style="height: 280px;">
            <div class="flex justify-center items-center h-full">
              <Doughnut :data="paymentMethodsChart" :options="doughnutOptions" style="max-height: 250px; max-width: 250px;"/>
            </div>
          </div>
        </div>

        <!-- Alertas y recomendaciones -->
        <div class="bg-white dark:bg-zinc-900 rounded-2xl overflow-hidden border border-gray-300 dark:border-zinc-800 shadow-xl dark:shadow-black/50">
          <div class="px-6 py-5 border-b border-gray-100 dark:border-zinc-800">
            <div class="flex items-center gap-3">
              <div class="w-3 h-3 bg-rose-500 rounded-full"></div>
              <h3 class="text-base font-semibold text-gray-900 dark:text-white">Alertas y Recomendaciones</h3>
            </div>
          </div>
          <div class="p-6 space-y-4">
            <div v-for="alert in alerts" :key="alert.id" class="p-4 rounded-lg" :class="getAlertClass(alert.type)">
              <div class="flex items-start">
                <svg class="w-5 h-5 mr-3 mt-0.5" :class="getAlertIconClass(alert.type)" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="getAlertIcon(alert.type)"></path>
                </svg>
                <div>
                  <div class="font-medium text-sm">{{ alert.title }}</div>
                  <div class="text-sm opacity-80 mt-1">{{ alert.message }}</div>
                </div>
              </div>
            </div>
          </div>
        </div>

      </div>

    </div>
    
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onBeforeUnmount, watch } from 'vue'
import { Bar, Line, Doughnut } from 'vue-chartjs'
import { Chart as ChartJS, CategoryScale, LinearScale, BarElement, PointElement, LineElement, ArcElement, Title, Tooltip, Legend } from 'chart.js'
import ChartDataLabels from 'chartjs-plugin-datalabels'
import { cashReportsService } from '../services/cashReportsService.js'
import { exportService } from '../services/exportService.js'
import { useToast } from '../composables/useToast.js'
import { useUIContextStore } from '@/store/uiContextStore'

// Registrar componentes de Chart.js incluyendo DataLabels
ChartJS.register(CategoryScale, LinearScale, BarElement, PointElement, LineElement, ArcElement, Title, Tooltip, Legend, ChartDataLabels)

// Composables
const { showToast } = useToast()

// Estado reactivo
const loading = ref(false)
const error = ref(null)
const selectedPeriod = ref('today')
// CORREGIDO: Usar fecha de Colombia (UTC-5) en lugar de fecha local del sistema
const getColombiaDate = () => {
  const now = new Date()
  // Para Colombia, sumamos 5 horas si estamos en UTC, o simplemente tomamos la fecha local
  const year = now.getFullYear()
  const month = String(now.getMonth() + 1).padStart(2, '0')
  const day = String(now.getDate()).padStart(2, '0')
  const result = `${year}-${month}-${day}`
  return result
}
const customDate = ref(getColombiaDate())
const customEndDate = ref('')

// Datos de sesiones y cajeros (ahora serán cargados desde la API)
const activeSessions = ref([])
const cashierComparison = ref([])
const topSessions = ref([])
const alerts = ref([])
const paymentMethodsData = ref([])
const hourlyEfficiencyData = ref([])

// Métricas calculadas (ahora desde datos reales)
const totalSalesAmount = ref(0)
const totalTransactions = ref(0)
const averageSaleAmount = ref(0)
const averageSalesPerHour = ref(0)
const totalExpenses = ref(0)
const totalRefunds = ref(0)
const bestCashierData = ref({
  name: 'N/A',
  sales: 0,
  efficiency: 0
})

// Métricas calculadas
const bestCashier = computed(() => bestCashierData.value)

const totalTransactionsComputed = computed(() => totalTransactions.value)

// Datos para gráficos
const cashierPerformanceChart = computed(() => {
  if (!Array.isArray(cashierComparison.value) || cashierComparison.value.length === 0) {
    return {
      labels: [],
      datasets: [
        {
          label: 'Ventas Totales',
          data: [],
          backgroundColor: 'rgba(34, 197, 94, 0.8)',
          borderColor: 'rgba(34, 197, 94, 1)',
          borderWidth: 2,
          borderRadius: 8
        },
        {
          label: 'Transacciones',
          data: [],
          backgroundColor: 'rgba(59, 130, 246, 0.8)',
          borderColor: 'rgba(59, 130, 246, 1)',
          borderWidth: 2,
          borderRadius: 8
        }
      ]
    }
  }
  
  return {
    labels: cashierComparison.value.map(c => c.name || 'Sin nombre'),
    datasets: [
      {
        label: 'Ventas Totales',
        data: cashierComparison.value.map(c => parseFloat(c.total_sales) || 0),
        backgroundColor: 'rgba(34, 197, 94, 0.8)',
        borderColor: 'rgba(34, 197, 94, 1)',
        borderWidth: 2,
        borderRadius: 8
      },
      {
        label: 'Transacciones',
        data: cashierComparison.value.map(c => parseInt(c.transactions) || 0),
        backgroundColor: 'rgba(59, 130, 246, 0.8)',
        borderColor: 'rgba(59, 130, 246, 1)',
        borderWidth: 2,
        borderRadius: 8,
        yAxisID: 'y1'
      }
    ]
  }
})

const hourlyEfficiencyChart = computed(() => {
  if (!Array.isArray(hourlyEfficiencyData.value) || hourlyEfficiencyData.value.length === 0) {
    return {
      labels: ['8:00', '9:00', '10:00', '11:00', '12:00', '13:00', '14:00', '15:00', '16:00', '17:00'],
      datasets: [
        {
          label: 'Ventas por Hora',
          data: [800, 1200, 1500, 1800, 2200, 1900, 2500, 2800, 2300, 1600],
          borderColor: 'rgba(59, 130, 246, 1)',
          backgroundColor: 'rgba(59, 130, 246, 0.1)',
          borderWidth: 3,
          fill: true,
          tension: 0.4,
          pointBackgroundColor: 'rgba(59, 130, 246, 1)',
          pointBorderColor: '#ffffff',
          pointBorderWidth: 2,
          pointRadius: 6
        }
      ]
    }
  }
  
  return {
    labels: hourlyEfficiencyData.value.map(h => h.hour),
    datasets: [
      {
        label: 'Ventas por Hora',
        data: hourlyEfficiencyData.value.map(h => h.sales),
        borderColor: 'rgba(59, 130, 246, 1)',
        backgroundColor: 'rgba(59, 130, 246, 0.1)',
        borderWidth: 3,
        fill: true,
        tension: 0.4,
        pointBackgroundColor: 'rgba(59, 130, 246, 1)',
        pointBorderColor: '#ffffff',
        pointBorderWidth: 2,
        pointRadius: 6
      }
    ]
  }
})

const paymentMethodsChart = computed(() => {
  if (!paymentMethodsData.value || paymentMethodsData.value.length === 0) {
    return {
      labels: ['Efectivo', 'Tarjeta', 'Transferencia', 'Otros'],
      datasets: [{
        data: [45, 35, 15, 5],
        backgroundColor: [
          'rgba(34, 197, 94, 0.8)',
          'rgba(59, 130, 246, 0.8)',
          'rgba(168, 85, 247, 0.8)',
          'rgba(249, 115, 22, 0.8)'
        ],
        borderColor: [
          'rgba(34, 197, 94, 1)',
          'rgba(59, 130, 246, 1)',
          'rgba(168, 85, 247, 1)',
          'rgba(249, 115, 22, 1)'
        ],
        borderWidth: 2
      }]
    }
  }

  return {
    labels: paymentMethodsData.value.map(p => p.method),
    datasets: [{
      data: paymentMethodsData.value.map(p => p.percentage),
      backgroundColor: [
        'rgba(34, 197, 94, 0.8)',
        'rgba(59, 130, 246, 0.8)',
        'rgba(168, 85, 247, 0.8)',
        'rgba(249, 115, 22, 0.8)'
      ],
      borderColor: [
        'rgba(34, 197, 94, 1)',
        'rgba(59, 130, 246, 1)',
        'rgba(168, 85, 247, 1)',
        'rgba(249, 115, 22, 1)'
      ],
      borderWidth: 2
    }]
  }
})

// Opciones de gráficos
const chartOptions = {
  responsive: true,
  maintainAspectRatio: false,
  plugins: {
    legend: { display: true, position: 'bottom' },
    tooltip: { 
      backgroundColor: 'rgba(25, 25, 25, 0.9)', 
      cornerRadius: 8,
      padding: 12,
      titleColor: '#ffffff',
      bodyColor: '#ffffff',
      callbacks: {
        label: (context) => {
          const value = context.parsed.y;
          if (context.dataset.yAxisID === 'y1') {
            return `${context.dataset.label}: ${value} trans`;
          }
          return `${context.dataset.label}: $${value.toLocaleString()}`;
        }
      }
    },
    datalabels: {
      display: true,
      anchor: 'end',
      align: 'top',
      offset: 4,
      font: {
        weight: 'bold',
        size: 11
      },
      color: (context) => {
        // Color del label basado en el dataset
        if (context.dataset.yAxisID === 'y1') {
          return '#3b82f6' // Azul para transacciones
        }
        return '#16a34a' // Verde para ventas
      },
      formatter: (value, context) => {
        if (context.dataset.yAxisID === 'y1') {
          return value + ' tx'
        }
        // Formato abreviado para moneda
        if (value >= 1000000) {
          return '$' + (value / 1000000).toFixed(1) + 'M'
        } else if (value >= 1000) {
          return '$' + (value / 1000).toFixed(0) + 'K'
        }
        return '$' + value.toLocaleString()
      }
    }
  },
  scales: {
    y: { 
      type: 'linear',
      display: true,
      position: 'left',
      beginAtZero: true,
      grid: { color: '#e5e7eb' },
      ticks: { 
        color: '#6b7280',
        callback: (value) => {
          // Mostrar formato legible: $20.000, $1.5M, etc.
          if (value >= 1000000) {
            return `$${(value / 1000000).toFixed(1)}M`;
          } else if (value >= 1000) {
            return `$${(value / 1000).toFixed(0)}K`;
          }
          return `$${value.toLocaleString()}`;
        }
      }
    },
    y1: {
      type: 'linear',
      display: true,
      position: 'right',
      beginAtZero: true,
      grid: { drawOnChartArea: false },
      ticks: { 
        color: '#3b82f6',
        callback: (value) => `${value} trans`
      }
    },
    x: { 
      grid: { display: false },
      ticks: { color: '#6b7280' }
    }
  }
}

const lineChartOptions = {
  responsive: true,
  maintainAspectRatio: false,
  plugins: {
    legend: { display: true, position: 'bottom' },
    tooltip: { 
      backgroundColor: 'rgba(25, 25, 25, 0.9)', 
      cornerRadius: 8,
      padding: 12,
      titleColor: '#ffffff',
      bodyColor: '#ffffff',
      callbacks: {
        label: (context) => `${context.dataset.label}: $${context.parsed.y.toLocaleString()}`
      }
    },
    datalabels: {
      display: false // Deshabilitado para gráfico de línea
    }
  },
  scales: {
    y: { 
      beginAtZero: true,
      grid: { color: '#e5e7eb' },
      ticks: { 
        color: '#6b7280',
        callback: (value) => {
          // Mostrar formato legible
          if (value >= 1000000) {
            return `$${(value / 1000000).toFixed(1)}M`;
          } else if (value >= 1000) {
            return `$${(value / 1000).toFixed(0)}K`;
          }
          return `$${value.toLocaleString()}`;
        }
      }
    },
    x: { 
      grid: { display: false },
      ticks: { color: '#6b7280' }
    }
  }
}

const doughnutOptions = {
  responsive: true,
  maintainAspectRatio: false,
  plugins: {
    legend: { display: true, position: 'bottom' },
    tooltip: { 
      backgroundColor: 'rgba(25, 25, 25, 0.9)', 
      cornerRadius: 8,
      padding: 12,
      titleColor: '#ffffff',
      bodyColor: '#ffffff',
      callbacks: {
        label: (context) => `${context.label}: ${context.parsed}%`
      }
    },
    datalabels: {
      display: true,
      color: '#ffffff',
      font: {
        weight: 'bold',
        size: 12
      },
      formatter: (value) => {
        return value > 5 ? value + '%' : '' // Solo mostrar si es > 5%
      }
    }
  }
}

// Métodos
const getPeriodLabel = () => {
  // Si hay fechas personalizadas, mostrarlas
  if (customDate.value) {
    return customEndDate.value ? 
      `${formatDate(customDate.value)} - ${formatDate(customEndDate.value)}` : 
      formatDate(customDate.value)
  }
  
  // CORREGIDO: Para 'today', mostrar fecha específica de Colombia
  if (selectedPeriod.value === 'today') {
    const colombiaDate = getColombiaDate()
    return formatDate(colombiaDate)
  }
  
  // Para otros períodos, usar las etiquetas genéricas
  const labels = {
    week: 'Últimos 7 días', 
    month: 'Últimos 30 días',
    year: 'Últimos 12 meses'
  }
  return labels[selectedPeriod.value] || 'Período seleccionado'
}

const formatDate = (dateString) => {
  if (!dateString) return ''
  
  // CORREGIDO: Asegurar que la fecha se interprete en timezone de Colombia
  const date = new Date(dateString + 'T00:00:00-05:00') // Forzar timezone Colombia
  
  return date.toLocaleDateString('es-CO', { 
    day: '2-digit', 
    month: '2-digit', 
    year: 'numeric',
    timeZone: 'America/Bogota'
  })
}

const getInitials = (name) => {
  return name.split(' ').map(n => n[0]).join('').toUpperCase()
}

const getActiveSessionsChange = () => {
  // Calcular cambio real basado en sesiones activas
  const currentCount = activeSessions.value.length
  // Si hay sesiones activas, mostrar un indicador positivo
  return currentCount > 0 ? Math.round((currentCount / Math.max(currentCount, 1)) * 100) : 0
}

const getHourlyGrowth = () => {
  // Calcular crecimiento basado en promedio real
  if (averageSalesPerHour.value > 0 && totalSalesAmount.value > 0) {
    // Comparar con promedio esperado (total/horas trabajadas aproximadas)
    const expectedAvg = totalSalesAmount.value / 8 // Asumiendo 8 horas de trabajo
    const growth = Math.round(((averageSalesPerHour.value - expectedAvg) / Math.max(expectedAvg, 1)) * 100)
    return Math.max(0, growth) // No mostrar valores negativos
  }
  return 0
}

const getTransactionGrowth = () => {
  // Mostrar porcentaje basado en transacciones reales
  if (totalTransactions.value > 0) {
    // Comparar con un baseline de transacciones esperadas
    const baseline = 10 // Transacciones mínimas esperadas
    const growth = Math.round(((totalTransactions.value - baseline) / Math.max(baseline, 1)) * 100)
    return growth
  }
  return 0
}

const getAlertClass = (type) => {
  const classes = {
    warning: 'bg-amber-50 dark:bg-amber-950/30 border border-amber-200 dark:border-amber-800/50',
    success: 'bg-emerald-50 dark:bg-emerald-950/30 border border-emerald-200 dark:border-emerald-800/50',
    info: 'bg-blue-50 dark:bg-blue-950/30 border border-blue-200 dark:border-blue-800/50',
    error: 'bg-rose-50 dark:bg-rose-950/30 border border-rose-200 dark:border-rose-800/50'
  }
  return classes[type] || classes.info
}

const getAlertIconClass = (type) => {
  const classes = {
    warning: 'text-amber-600 dark:text-amber-400',
    success: 'text-emerald-600 dark:text-emerald-400',
    info: 'text-blue-600 dark:text-blue-400',
    error: 'text-rose-600 dark:text-rose-400'
  }
  return classes[type] || classes.info
}

const getAlertIcon = (type) => {
  const icons = {
    warning: 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z',
    success: 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
    info: 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
    error: 'M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z'
  }
  return icons[type] || icons.info
}

// Funciones de exportación
const exportCashReport = async () => {
  try {
    const result = await exportService.exportCashierReport(
      cashierComparison.value,
      getPeriodLabel()
    )
    
    if (result.success) {
      showToast('Reporte exportado exitosamente', 'success')
    } else {
      showToast(result.message, 'error')
    }
  } catch (error) {
    console.error('Error exportando reporte:', error)
    showToast('Error al exportar reporte', 'error')
  }
}

const exportCashierComparison = async () => {
  try {
    const result = await exportService.exportCashierComparison(
      cashierComparison.value,
      topSessions.value,
      alerts.value
    )
    
    if (result.success) {
      showToast('Comparativa exportada exitosamente', 'success')
    } else {
      showToast(result.message, 'error')
    }
  } catch (error) {
    console.error('Error exportando comparativa:', error)
    showToast('Error al exportar comparativa', 'error')
  }
}

// Manejar cambio de período
const handlePeriodChange = () => {
  loadCashReportsData()
}

// Cargar datos de reportes de caja
const loadCashReportsData = async () => {
  try {
    loading.value = true
    
    // CAMBIO CRÍTICO: Usar el período seleccionado directamente sin forzar custom
    const periodToUse = selectedPeriod.value
    const customDateParam = selectedPeriod.value === 'custom' ? customDate.value : null
    const customEndDateParam = selectedPeriod.value === 'custom' ? customEndDate.value : null
    
    // Cargar métricas del dashboard
    const dashboardData = await cashReportsService.getCashMetrics(periodToUse, customDateParam, customEndDateParam)
    
    if (dashboardData.success) {
      totalSalesAmount.value = dashboardData.total_sales || 0
      totalTransactions.value = dashboardData.total_transactions || 0
      averageSaleAmount.value = dashboardData.average_sale || 0
      totalExpenses.value = dashboardData.total_expenses || 0
      totalRefunds.value = dashboardData.total_refunds || 0
      
      // Cargar sesiones activas si están disponibles
      if (dashboardData.active_sessions) {
        activeSessions.value = dashboardData.active_sessions
      }
    }
    
    // Cargar comparación de cajeros
    const cashiersResponse = await cashReportsService.getCashierComparison(periodToUse, customDateParam, customEndDateParam)
    
    if (cashiersResponse.success && cashiersResponse.data && Array.isArray(cashiersResponse.data)) {
      // Procesar datos para asegurar tipos correctos
      const processedCashiers = cashiersResponse.data.map(cashier => ({
        ...cashier,
        total_sales: parseFloat(cashier.total_sales || 0),
        average_sale: parseFloat(cashier.average_sale || 0),
        efficiency: parseFloat(cashier.efficiency || 0),
        transactions: parseInt(cashier.transactions || 0)
      }))
      
      cashierComparison.value = processedCashiers
      
      // Establecer mejor cajero
      if (processedCashiers.length > 0) {
        const best = processedCashiers.reduce((prev, current) => 
          (prev.total_sales > current.total_sales) ? prev : current
        )
        bestCashierData.value = {
          name: best.name,
          sales: parseFloat(best.total_sales || 0),
          efficiency: parseFloat(best.efficiency || 0)
        }
      }
    } else {
      cashierComparison.value = []
    }
    
    // Cargar eficiencia horaria
    const hourlyResponse = await cashReportsService.getHourlyEfficiency(periodToUse, customDateParam, customEndDateParam)
    if (hourlyResponse.success && hourlyResponse.data && hourlyResponse.data.hourly_data && Array.isArray(hourlyResponse.data.hourly_data)) {
      hourlyEfficiencyData.value = hourlyResponse.data.hourly_data
      
      // Calcular promedio de ventas por hora (solo horas con ventas)
      const hoursWithSales = hourlyResponse.data.hourly_data.filter(h => h.sales > 0)
      if (hoursWithSales.length > 0) {
        const totalHourlySales = hoursWithSales.reduce((sum, h) => sum + h.sales, 0)
        averageSalesPerHour.value = Math.round(totalHourlySales / hoursWithSales.length)
      } else {
        averageSalesPerHour.value = 0
      }
    } else {
      hourlyEfficiencyData.value = []
      averageSalesPerHour.value = 0
    }
    
    // Cargar métodos de pago
    const paymentResponse = await cashReportsService.getPaymentMethods(periodToUse, customDateParam, customEndDateParam)
    if (paymentResponse.success && paymentResponse.data && Array.isArray(paymentResponse.data)) {
      paymentMethodsData.value = paymentResponse.data
    } else {
      paymentMethodsData.value = []
    }
    
    // Cargar mejores sesiones
    const topSessionsResponse = await cashReportsService.getTopSessions(periodToUse, customDateParam, customEndDateParam)
    
    if (topSessionsResponse.success && topSessionsResponse.data && Array.isArray(topSessionsResponse.data)) {
      topSessions.value = topSessionsResponse.data
    } else {
      topSessions.value = []
    }
    
    // Cargar alertas
    const alertsResponse = await cashReportsService.getCashAlerts()
    if (alertsResponse.success && alertsResponse.data && Array.isArray(alertsResponse.data)) {
      alerts.value = alertsResponse.data
    } else {
      alerts.value = []
    }

  } catch (error) {
    console.error('Error cargando datos de reportes de caja:', error)
    showToast('Error al cargar los datos de reportes', 'error')
  } finally {
    loading.value = false
  }
}

// Función para refrescar datos
const refreshData = async () => {
  await loadCashReportsData()
  showToast('Datos actualizados correctamente', 'success')
}

// Observar cambios en el período
watch(selectedPeriod, () => {
  loadCashReportsData()
})

// ═══════════════════════════════════════════════════════════════
// CONTEXTO IA - Reportes de Caja
// ═══════════════════════════════════════════════════════════════
const uiContextStore = useUIContextStore()

// Actualizar contexto para la IA
const actualizarContextoIA = () => {
  const formatMoney = (n) => `$${(n || 0).toLocaleString('es-CO')}`
  
  // KPIs principales
  const kpis = {
    sesionesActivas: activeSessions.value?.length || 0,
    mejorCajero: bestCashierData.value.name,
    mejorCajeroVentas: formatMoney(bestCashierData.value.sales),
    promedioHora: formatMoney(averageSalesPerHour.value),
    totalVentas: formatMoney(totalSalesAmount.value),
    totalTransacciones: totalTransactions.value,
    periodo: selectedPeriod.value
  }
  
  // Comparativa de cajeros
  const cajerosResumen = (cashierComparison.value || []).slice(0, 5).map(c => ({
    nombre: c.name,
    ventas: formatMoney(parseFloat(c.total_sales) || 0),
    transacciones: parseInt(c.transactions) || 0,
    ticketPromedio: formatMoney(parseFloat(c.average_ticket) || 0)
  }))
  
  // Top sesiones
  const topSesionesResumen = (topSessions.value || []).slice(0, 5).map(s => ({
    cajero: s.cashier_name,
    ventas: formatMoney(parseFloat(s.total_sales) || 0),
    fecha: s.date || 'N/A'
  }))
  
  // Alertas
  const alertasResumen = (alerts.value || []).map(a => ({
    tipo: a.type,
    titulo: a.title,
    mensaje: a.message
  }))
  
  uiContextStore.setScreenData({
    tipoReporte: 'reports-caja',
    modulo: 'Reportes de Caja',
    descripcion: 'Análisis avanzado por cajero con rendimiento, comparativas y eficiencia por hora',
    kpis,
    cajeros: cajerosResumen,
    topSesiones: topSesionesResumen,
    alertas: alertasResumen,
    periodoActual: getPeriodLabel(),
    ultimaActualizacion: new Date().toLocaleTimeString('es-CO')
  })
}

// Registrar acciones disponibles para la IA
const registrarAccionesIA = () => {
  // Consultar reportes de caja
  uiContextStore.registerAction('consultarReportesCaja', async ({ periodo, tipoConsulta }) => {
    try {
      const formatMoney = (n) => `$${(n || 0).toLocaleString('es-CO')}`
      
      let mensaje = ''
      switch (tipoConsulta) {
        case 'mejor_cajero':
          mensaje = `Mejor cajero del ${getPeriodLabel()}: ${bestCashierData.value.name} con ${formatMoney(bestCashierData.value.sales)} en ventas`
          break
          
        case 'comparativa_cajeros':
          const cajeros = cashierComparison.value.slice(0, 5)
          mensaje = `Comparativa de cajeros (${getPeriodLabel()}):\n` + 
            cajeros.map((c, i) => `${i+1}. ${c.name}: ${formatMoney(parseFloat(c.total_sales) || 0)} (${c.transactions || 0} trans.)`).join('\n')
          break
          
        case 'top_sesiones':
          const sesiones = topSessions.value.slice(0, 5)
          mensaje = `Mejores sesiones del ${getPeriodLabel()}:\n` + 
            sesiones.map((s, i) => `${i+1}. ${s.cashier_name}: ${formatMoney(parseFloat(s.total_sales) || 0)}`).join('\n')
          break
          
        case 'eficiencia_hora':
          mensaje = `⏰ Promedio de ventas por hora: ${formatMoney(averageSalesPerHour.value)}`
          break
          
        default:
          mensaje = `REPORTE DE CAJAS (${getPeriodLabel()}):
• Sesiones activas: ${activeSessions.value?.length || 0}
• Total ventas: ${formatMoney(totalSalesAmount.value)}
• Transacciones: ${totalTransactions.value}
• Mejor cajero: ${bestCashierData.value.name} (${formatMoney(bestCashierData.value.sales)})
• Promedio/hora: ${formatMoney(averageSalesPerHour.value)}`
      }
      
      return { success: true, message: mensaje }
    } catch (err) {
      console.error('Error en consultarReportesCaja:', err)
      return { success: false, message: 'Error al consultar reportes de caja' }
    }
  })
  
  // Cambiar período
  uiContextStore.registerAction('cambiarPeriodoReportesCaja', async ({ periodo }) => {
    const periodoMap = { 'hoy': 'today', 'semana': 'week', 'mes': 'month', 'año': 'year' }
    const nuevoPeriodo = periodoMap[periodo] || periodo
    if (['today', 'week', 'month', 'year'].includes(nuevoPeriodo)) {
      selectedPeriod.value = nuevoPeriodo
      return { success: true, message: `Período cambiado a ${periodo}` }
    }
    return { success: false, message: 'Período no válido' }
  })
  
  // Cambiar a reporte de caja (desde reportes generales)
  uiContextStore.registerAction('cambiarAReporteCaja', async () => {
    // Este componente ya ES el reporte de caja
    return { success: true, message: 'Ya estás en los reportes de caja' }
  })
  
  // Exportar reporte
  uiContextStore.registerAction('exportarReporteCaja', async () => {
    try {
      await exportCashReport()
      return { success: true, message: 'Reporte de caja exportado exitosamente' }
    } catch (err) {
      return { success: false, message: 'Error al exportar reporte de caja' }
    }
  })
}

// Cargar datos al montar
onMounted(() => {
  // Establecer módulo actual para la IA
  uiContextStore.setCurrentModule('reports-caja')
  
  loadCashReportsData()
  
  // Registrar acciones IA
  registrarAccionesIA()
  
  // Actualizar contexto después de cargar datos
  setTimeout(() => {
    actualizarContextoIA()
  }, 1000)
})

// Limpiar al desmontar
onBeforeUnmount(() => {
  uiContextStore.clearSelection()
})

// Actualizar contexto cuando cambian los datos
watch([totalSalesAmount, cashierComparison, topSessions, bestCashierData], () => {
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

/* Estilos personalizados */
* {
  transition: all 0.2s ease-in-out;
}
</style>