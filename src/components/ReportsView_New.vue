<template>
  <div class="min-h-screen font-sans bg-gradient-to-b from-gray-50 via-gray-100 to-gray-200 dark:bg-gradient-to-b dark:from-[#141417] dark:via-slate-900 dark:to-[#0a0a0c] transition-colors duration-300 px-8">
    <div class="p-4 lg:p-6 space-y-6 pb-8 animate-fade-in">
      
      <!-- Header -->
      <div class="flex items-center justify-between pb-4">
        <div>
          <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Reportes Empresariales</h1>
          <p class="text-sm text-gray-600 dark:text-zinc-400 mt-1">Análisis completo del negocio • {{ getPeriodLabel() }}</p>
        </div>
        
        <div class="flex items-center gap-3">
          <select 
            v-model="selectedPeriod" 
            @change="loadAllReports"
            class="px-3 py-3 text-sm rounded-lg border border-gray-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 text-gray-700 dark:text-zinc-300 font-medium focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400"
          >
            <option value="today">Hoy</option>
            <option value="week">Esta semana</option>
            <option value="month">Este mes</option>
            <option value="year">Este año</option>
          </select>
          
          <button 
            @click="loadAllReports"
            class="px-5 py-2.5 bg-white dark:bg-zinc-900 hover:bg-slate-50 dark:hover:bg-zinc-800 text-slate-600 dark:text-zinc-200 text-sm font-bold rounded-xl border border-slate-200 dark:border-zinc-800 shadow-sm transition-all duration-200"
          >
            Refrescar
          </button>
          
          <button 
            @click="exportAllReports" 
            class="px-6 py-2.5 bg-slate-900 dark:bg-slate-700 hover:bg-black dark:hover:bg-slate-600 text-white text-sm font-bold rounded-xl shadow-lg shadow-slate-400/40 dark:shadow-slate-900/50 transition-all duration-300 flex items-center gap-2"
          >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            Exportar Todo
          </button>
        </div>
      </div>

      <!-- Loading State -->
      <div v-if="loading" class="bg-white dark:bg-zinc-900 rounded-xl shadow-sm border border-gray-300 dark:border-zinc-800 p-8 text-center">
        <div class="inline-flex items-center space-x-3">
          <svg class="animate-spin h-6 w-6 text-blue-600 dark:text-blue-400" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
          </svg>
          <span class="text-gray-600 dark:text-zinc-300">Cargando reportes...</span>
        </div>
      </div>

      <!-- Error State -->
      <div v-else-if="error" class="bg-red-50 dark:bg-red-950 border border-red-200 dark:border-red-800 rounded-xl p-6">
        <div class="flex items-center space-x-3">
          <svg class="w-6 h-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
          </svg>
          <div>
            <h3 class="text-red-800 dark:text-red-400 font-medium">Error al cargar reportes</h3>
            <p class="text-red-600 dark:text-red-300 text-sm">{{ error }}</p>
          </div>
        </div>
        <button @click="loadAllReports" class="mt-4 px-4 py-2 bg-red-600 dark:bg-red-700 text-white rounded-lg hover:bg-red-700 dark:hover:bg-red-600 transition-colors">
          Reintentar
        </button>
      </div>

      <!-- Main Content -->
      <div v-else class="space-y-6">
        
        <!-- 📊 KPIs PRINCIPALES - Mejorado -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5">
          
          <!-- Ventas Totales -->
          <div class="group relative bg-gradient-to-br from-emerald-50 to-emerald-100/50 dark:from-emerald-950/30 dark:to-emerald-900/10 backdrop-blur-sm rounded-2xl p-5 border-2 border-emerald-200/60 dark:border-emerald-800/40 hover:border-emerald-300 dark:hover:border-emerald-700 hover:shadow-2xl hover:shadow-emerald-500/20 dark:shadow-emerald-900/40 transition-all duration-300 transform hover:-translate-y-1">
            <div class="absolute top-0 right-0 w-32 h-32 bg-emerald-400/10 dark:bg-emerald-600/5 rounded-full blur-3xl"></div>
            <div class="relative">
              <div class="flex items-center justify-between mb-3">
                <div class="w-14 h-14 rounded-2xl flex items-center justify-center bg-gradient-to-br from-emerald-500 to-emerald-600 shadow-lg shadow-emerald-500/50 dark:shadow-emerald-900/50 group-hover:scale-110 transition-transform duration-300">
                  <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                  </svg>
                </div>
                <div class="px-3 py-1.5 bg-emerald-200/50 dark:bg-emerald-900/30 rounded-xl">
                  <p class="text-xs font-black text-emerald-700 dark:text-emerald-400">+{{ kpis.salesGrowth }}%</p>
                </div>
              </div>
              <div class="space-y-1">
                <p class="text-xs font-bold text-emerald-700 dark:text-emerald-400 uppercase tracking-wider">Ventas Totales</p>
                <p class="text-3xl font-black text-gray-900 dark:text-white tracking-tight">${{ formatNumber(kpis.totalSales) }}</p>
                <p class="text-xs text-gray-600 dark:text-zinc-400 font-medium">{{ getPeriodLabel() }}</p>
              </div>
            </div>
          </div>

          <!-- Transacciones -->
          <div class="group relative bg-gradient-to-br from-blue-50 to-blue-100/50 dark:from-blue-950/30 dark:to-blue-900/10 backdrop-blur-sm rounded-2xl p-5 border-2 border-blue-200/60 dark:border-blue-800/40 hover:border-blue-300 dark:hover:border-blue-700 hover:shadow-2xl hover:shadow-blue-500/20 dark:shadow-blue-900/40 transition-all duration-300 transform hover:-translate-y-1">
            <div class="absolute top-0 right-0 w-32 h-32 bg-blue-400/10 dark:bg-blue-600/5 rounded-full blur-3xl"></div>
            <div class="relative">
              <div class="flex items-center justify-between mb-3">
                <div class="w-14 h-14 rounded-2xl flex items-center justify-center bg-gradient-to-br from-blue-500 to-blue-600 shadow-lg shadow-blue-500/50 dark:shadow-blue-900/50 group-hover:scale-110 transition-transform duration-300">
                  <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                  </svg>
                </div>
                <div class="px-3 py-1.5 bg-blue-200/50 dark:bg-blue-900/30 rounded-xl">
                  <p class="text-xs font-black text-blue-700 dark:text-blue-400">+{{ kpis.transactionsGrowth }}%</p>
                </div>
              </div>
              <div class="space-y-1">
                <p class="text-xs font-bold text-blue-700 dark:text-blue-400 uppercase tracking-wider">Transacciones</p>
                <p class="text-3xl font-black text-gray-900 dark:text-white tracking-tight">{{ formatNumber(kpis.totalTransactions) }}</p>
                <p class="text-xs text-gray-600 dark:text-zinc-400 font-medium">Operaciones realizadas</p>
              </div>
            </div>
          </div>

          <!-- Ticket Promedio -->
          <div class="group relative bg-gradient-to-br from-amber-50 to-amber-100/50 dark:from-amber-950/30 dark:to-amber-900/10 backdrop-blur-sm rounded-2xl p-5 border-2 border-amber-200/60 dark:border-amber-800/40 hover:border-amber-300 dark:hover:border-amber-700 hover:shadow-2xl hover:shadow-amber-500/20 dark:shadow-amber-900/40 transition-all duration-300 transform hover:-translate-y-1">
            <div class="absolute top-0 right-0 w-32 h-32 bg-amber-400/10 dark:bg-amber-600/5 rounded-full blur-3xl"></div>
            <div class="relative">
              <div class="flex items-center justify-between mb-3">
                <div class="w-14 h-14 rounded-2xl flex items-center justify-center bg-gradient-to-br from-amber-500 to-amber-600 shadow-lg shadow-amber-500/50 dark:shadow-amber-900/50 group-hover:scale-110 transition-transform duration-300">
                  <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"></path>
                  </svg>
                </div>
                <div class="px-3 py-1.5 bg-amber-200/50 dark:bg-amber-900/30 rounded-xl">
                  <p class="text-xs font-black text-amber-700 dark:text-amber-400">+{{ kpis.ticketGrowth }}%</p>
                </div>
              </div>
              <div class="space-y-1">
                <p class="text-xs font-bold text-amber-700 dark:text-amber-400 uppercase tracking-wider">Ticket Promedio</p>
                <p class="text-3xl font-black text-gray-900 dark:text-white tracking-tight">${{ formatNumber(kpis.averageTicket) }}</p>
                <p class="text-xs text-gray-600 dark:text-zinc-400 font-medium">Por transacción</p>
              </div>
            </div>
          </div>

          <!-- Productos Activos -->
          <div class="group relative bg-gradient-to-br from-purple-50 to-purple-100/50 dark:from-purple-950/30 dark:to-purple-900/10 backdrop-blur-sm rounded-2xl p-5 border-2 border-purple-200/60 dark:border-purple-800/40 hover:border-purple-300 dark:hover:border-purple-700 hover:shadow-2xl hover:shadow-purple-500/20 dark:shadow-purple-900/40 transition-all duration-300 transform hover:-translate-y-1">
            <div class="absolute top-0 right-0 w-32 h-32 bg-purple-400/10 dark:bg-purple-600/5 rounded-full blur-3xl"></div>
            <div class="relative">
              <div class="flex items-center justify-between mb-3">
                <div class="w-14 h-14 rounded-2xl flex items-center justify-center bg-gradient-to-br from-purple-500 to-purple-600 shadow-lg shadow-purple-500/50 dark:shadow-purple-900/50 group-hover:scale-110 transition-transform duration-300">
                  <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                  </svg>
                </div>
                <div class="px-3 py-1.5 bg-red-200/50 dark:bg-red-900/30 rounded-xl">
                  <p class="text-xs font-black text-red-700 dark:text-red-400">{{ kpis.lowStock }} ⚠️</p>
                </div>
              </div>
              <div class="space-y-1">
                <p class="text-xs font-bold text-purple-700 dark:text-purple-400 uppercase tracking-wider">Productos</p>
                <p class="text-3xl font-black text-gray-900 dark:text-white tracking-tight">{{ formatNumber(kpis.totalProducts) }}</p>
                <p class="text-xs text-gray-600 dark:text-zinc-400 font-medium">En catálogo activo</p>
              </div>
            </div>
          </div>

        </div>

        <!-- 📈 SECCIÓN DE VENTAS -->
        <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-gray-300 dark:border-zinc-800 shadow-xl dark:shadow-black/50 overflow-hidden">
          <div class="bg-white dark:bg-zinc-900 border-b border-gray-200 dark:border-zinc-800 px-5 py-4">
            <div class="flex items-center justify-between">
              <div>
                <h2 class="text-base font-bold text-gray-900 dark:text-white">Análisis de Ventas</h2>
                <p class="text-xs text-gray-600 dark:text-zinc-400 mt-0.5">Evolución de ingresos y métodos de pago</p>
              </div>
              <span class="px-3 py-1.5 bg-blue-50 dark:bg-blue-950 text-blue-700 dark:text-blue-400 text-xs font-bold rounded-lg">
                Crecimiento
              </span>
            </div>
          </div>
          
          <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 p-6">
            <!-- Tendencia de Ventas -->
            <div class="bg-white dark:bg-zinc-900 rounded-xl border border-gray-200 dark:border-zinc-800 p-5">
              <h3 class="text-sm font-bold text-gray-900 dark:text-white mb-1">Tendencia de Ventas</h3>
              <p class="text-xs text-gray-600 dark:text-zinc-400 mb-4">Últimas {{ selectedPeriod === 'today' ? '24 horas' : '7 días' }}</p>
              <div style="height: 320px;">
                <Line :data="salesTrendChart" :options="chartOptions" />
              </div>
            </div>

            <!-- Ventas por Método de Pago -->
            <div class="bg-white dark:bg-zinc-900 rounded-xl border border-gray-200 dark:border-zinc-800 p-5">
              <h3 class="text-sm font-bold text-gray-900 dark:text-white mb-1">Métodos de Pago</h3>
              <p class="text-xs text-gray-600 dark:text-zinc-400 mb-4">Distribución de transacciones</p>
              <div style="height: 320px;">
                <Doughnut :data="paymentMethodsChart" :options="doughnutOptions" />
              </div>
            </div>
          </div>
        </div>

        <!-- 💰 REPORTE DE CAJA - Mejorado -->
        <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-gray-300 dark:border-zinc-800 shadow-xl dark:shadow-black/50 overflow-hidden">
          <div class="bg-gradient-to-r from-emerald-50 to-teal-50 dark:from-zinc-900 dark:to-zinc-900 border-b border-gray-200 dark:border-zinc-800 px-6 py-5">
            <div class="flex items-center justify-between">
              <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-emerald-600 to-teal-600 flex items-center justify-center shadow-lg">
                  <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                  </svg>
                </div>
                <div>
                  <h2 class="text-lg font-black text-gray-900 dark:text-white">Control de Cajas</h2>
                  <p class="text-xs text-gray-600 dark:text-zinc-400 font-medium mt-0.5">Rendimiento de cajeros y sesiones</p>
                </div>
              </div>
              <button class="px-4 py-2 bg-white dark:bg-zinc-800 rounded-xl text-xs font-bold text-gray-700 dark:text-zinc-300 border border-gray-200 dark:border-zinc-700 hover:bg-gray-50 dark:hover:bg-zinc-700 transition-colors">
                📊 Más detalles
              </button>
            </div>
          </div>
          
          <div class="p-6">
            <!-- KPIs de Caja Mejorados -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-6">
              <div class="group bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-950/50 dark:to-blue-900/30 rounded-2xl p-5 border-2 border-blue-200 dark:border-blue-800/50 hover:shadow-xl hover:shadow-blue-500/20 transition-all duration-300 hover:-translate-y-1">
                <div class="flex items-center gap-3 mb-3">
                  <div class="w-12 h-12 rounded-xl bg-blue-600 dark:bg-blue-500 flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                  </div>
                  <div class="px-3 py-1 bg-blue-200 dark:bg-blue-900/50 rounded-lg">
                    <p class="text-[10px] font-black text-blue-700 dark:text-blue-400 uppercase">Activas</p>
                  </div>
                </div>
                <p class="text-xs text-blue-700 dark:text-blue-400 font-bold uppercase tracking-wider mb-2">Sesiones Activas</p>
                <p class="text-4xl font-black text-blue-800 dark:text-blue-300">{{ cashReport.activeSessions }}</p>
              </div>
              <div class="group bg-gradient-to-br from-emerald-50 to-emerald-100 dark:from-emerald-950/50 dark:to-emerald-900/30 rounded-2xl p-5 border-2 border-emerald-200 dark:border-emerald-800/50 hover:shadow-xl hover:shadow-emerald-500/20 transition-all duration-300 hover:-translate-y-1">
                <div class="flex items-center gap-3 mb-3">
                  <div class="w-12 h-12 rounded-xl bg-emerald-600 dark:bg-emerald-500 flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                    </svg>
                  </div>
                  <div class="px-3 py-1 bg-emerald-200 dark:bg-emerald-900/50 rounded-lg">
                    <p class="text-[10px] font-black text-emerald-700 dark:text-emerald-400 uppercase">Efectivo</p>
                  </div>
                </div>
                <p class="text-xs text-emerald-700 dark:text-emerald-400 font-bold uppercase tracking-wider mb-2">Total en Cajas</p>
                <p class="text-4xl font-black text-emerald-800 dark:text-emerald-300">${{ formatNumber(cashReport.totalInCash) }}</p>
              </div>
              <div class="group bg-gradient-to-br from-amber-50 to-amber-100 dark:from-amber-950/50 dark:to-amber-900/30 rounded-2xl p-5 border-2 border-amber-200 dark:border-amber-800/50 hover:shadow-xl hover:shadow-amber-500/20 transition-all duration-300 hover:-translate-y-1">
                <div class="flex items-center gap-3 mb-3">
                  <div class="w-12 h-12 rounded-xl bg-amber-600 dark:bg-amber-500 flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                  </div>
                  <div class="px-3 py-1 bg-amber-200 dark:bg-amber-900/50 rounded-lg">
                    <p class="text-[10px] font-black text-amber-700 dark:text-amber-400 uppercase">⚡ Alta</p>
                  </div>
                </div>
                <p class="text-xs text-amber-700 dark:text-amber-400 font-bold uppercase tracking-wider mb-2">Eficiencia Promedio</p>
                <p class="text-4xl font-black text-amber-800 dark:text-amber-300">{{ cashReport.averageEfficiency }}%</p>
              </div>
            </div>

            <!-- Tabla de Cajeros Mejorada -->
            <div class="bg-gradient-to-br from-gray-50 to-gray-100/50 dark:from-zinc-800/50 dark:to-zinc-800/30 rounded-2xl border-2 border-gray-200 dark:border-zinc-700 overflow-hidden">
              <div class="px-5 py-4 bg-white dark:bg-zinc-900 border-b-2 border-gray-200 dark:border-zinc-800">
                <h3 class="text-sm font-black text-gray-900 dark:text-white">👥 Rendimiento por Cajero</h3>
                <p class="text-xs text-gray-600 dark:text-zinc-400 mt-1">Comparativa de desempeño individual</p>
              </div>
              <table class="min-w-full">
                <thead class="bg-gradient-to-r from-gray-100 to-gray-50 dark:from-zinc-800 dark:to-zinc-900 border-b-2 border-gray-200 dark:border-zinc-700">
                  <tr>
                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 dark:text-zinc-300 uppercase tracking-wide">Cajero</th>
                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 dark:text-zinc-300 uppercase tracking-wide">Sesiones</th>
                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 dark:text-zinc-300 uppercase tracking-wide">Ventas</th>
                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 dark:text-zinc-300 uppercase tracking-wide">Transacciones</th>
                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 dark:text-zinc-300 uppercase tracking-wide">Eficiencia</th>
                  </tr>
                </thead>
                <tbody class="bg-white dark:bg-zinc-900 divide-y divide-gray-200 dark:divide-zinc-800">
                  <tr v-for="cashier in cashReport.cashiers" :key="cashier.id" class="hover:bg-gray-50 dark:hover:bg-zinc-800/50 transition-colors duration-200">
                    <td class="px-6 py-4 text-sm font-medium text-gray-900 dark:text-white">{{ cashier.name }}</td>
                    <td class="px-6 py-4 text-sm text-gray-700 dark:text-zinc-300">{{ cashier.sessions }}</td>
                    <td class="px-6 py-4 text-sm font-bold text-emerald-600 dark:text-emerald-400">${{ formatNumber(cashier.sales) }}</td>
                    <td class="px-6 py-4 text-sm text-gray-700 dark:text-zinc-300">{{ cashier.transactions }}</td>
                    <td class="px-6 py-4 text-sm">
                      <span :class="getEfficiencyClass(cashier.efficiency)">{{ cashier.efficiency }}%</span>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>

        <!-- 📦 REPORTE DE INVENTARIO - Mejorado -->
        <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-gray-300 dark:border-zinc-800 shadow-xl dark:shadow-black/50 overflow-hidden">
          <div class="bg-gradient-to-r from-purple-50 to-pink-50 dark:from-zinc-900 dark:to-zinc-900 border-b border-gray-200 dark:border-zinc-800 px-6 py-5">
            <div class="flex items-center justify-between">
              <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-purple-600 to-pink-600 flex items-center justify-center shadow-lg">
                  <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                  </svg>
                </div>
                <div>
                  <h2 class="text-lg font-black text-gray-900 dark:text-white">Inventario & Stock</h2>
                  <p class="text-xs text-gray-600 dark:text-zinc-400 font-medium mt-0.5">Productos estrella y alertas de reorden</p>
                </div>
              </div>
              <button class="px-4 py-2 bg-white dark:bg-zinc-800 rounded-xl text-xs font-bold text-gray-700 dark:text-zinc-300 border border-gray-200 dark:border-zinc-700 hover:bg-gray-50 dark:hover:bg-zinc-700 transition-colors">
                📋 Ver inventario
              </button>
            </div>
          </div>
          
          <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 p-6">
            <!-- Top Productos -->
            <div class="bg-gradient-to-br from-gray-50 to-gray-100/50 dark:from-zinc-800/50 dark:to-zinc-800/30 rounded-2xl border-2 border-gray-200 dark:border-zinc-700 p-5 hover:shadow-lg transition-shadow duration-300">
              <div class="flex items-center justify-between mb-4">
                <div>
                  <h3 class="text-sm font-black text-gray-900 dark:text-white flex items-center gap-2">
                    <span>🏆</span> Top 10 Productos
                  </h3>
                  <p class="text-xs text-gray-600 dark:text-zinc-400 mt-0.5">Más vendidos del período</p>
                </div>
                <div class="px-3 py-1.5 bg-purple-100 dark:bg-purple-900/30 rounded-lg">
                  <p class="text-xs font-bold text-purple-700 dark:text-purple-400">Top</p>
                </div>
              </div>
              <div style="height: 340px;">
                <Bar :data="topProductsChart" :options="barChartOptions" />
              </div>
            </div>

            <!-- Stock Bajo -->
            <div class="bg-gradient-to-br from-gray-50 to-gray-100/50 dark:from-zinc-800/50 dark:to-zinc-800/30 rounded-2xl border-2 border-gray-200 dark:border-zinc-700 p-5">
              <div class="flex items-center justify-between mb-4">
                <div>
                  <h3 class="text-sm font-black text-gray-900 dark:text-white flex items-center gap-2">
                    <span>⚠️</span> Alertas de Stock
                  </h3>
                  <p class="text-xs text-gray-600 dark:text-zinc-400 mt-0.5">Productos requieren reorden</p>
                </div>
                <div class="px-3 py-1.5 bg-red-100 dark:bg-red-900/30 rounded-lg">
                  <p class="text-xs font-bold text-red-700 dark:text-red-400">{{ inventoryReport.lowStock.length }} Críticos</p>
                </div>
              </div>
              <div class="space-y-3 max-h-[300px] overflow-y-auto custom-scrollbar">
                <div v-for="product in inventoryReport.lowStock" :key="product.id" class="group flex items-center justify-between p-4 bg-gradient-to-r from-red-50 to-orange-50 dark:from-red-950/50 dark:to-orange-950/30 rounded-xl border-2 border-red-200 dark:border-red-800/50 hover:shadow-md transition-all duration-200 hover:-translate-y-0.5">
                  <div class="flex items-center gap-3 flex-1">
                    <div class="w-10 h-10 rounded-lg bg-red-200 dark:bg-red-900/50 flex items-center justify-center group-hover:scale-110 transition-transform">
                      <svg class="w-5 h-5 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                      </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                      <p class="text-sm font-black text-gray-900 dark:text-white truncate">{{ product.name }}</p>
                      <p class="text-xs text-gray-600 dark:text-zinc-400 font-medium">🏷️ {{ product.category }}</p>
                    </div>
                  </div>
                  <div class="text-right ml-3">
                    <p class="text-2xl font-black text-red-600 dark:text-red-400">{{ product.stock }}</p>
                    <p class="text-[10px] text-red-600 dark:text-red-400 font-bold uppercase">unidades</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- 👥 REPORTE DE CLIENTES - Mejorado -->
        <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-gray-300 dark:border-zinc-800 shadow-xl dark:shadow-black/50 overflow-hidden">
          <div class="bg-gradient-to-r from-indigo-50 to-purple-50 dark:from-zinc-900 dark:to-zinc-900 border-b border-gray-200 dark:border-zinc-800 px-6 py-5">
            <div class="flex items-center justify-between">
              <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-indigo-600 to-purple-600 flex items-center justify-center shadow-lg">
                  <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                  </svg>
                </div>
                <div>
                  <h2 class="text-lg font-black text-gray-900 dark:text-white">Clientes & Fidelización</h2>
                  <p class="text-xs text-gray-600 dark:text-zinc-400 font-medium mt-0.5">Segmentación y comportamiento de compra</p>
                </div>
              </div>
              <button class="px-4 py-2 bg-white dark:bg-zinc-800 rounded-xl text-xs font-bold text-gray-700 dark:text-zinc-300 border border-gray-200 dark:border-zinc-700 hover:bg-gray-50 dark:hover:bg-zinc-700 transition-colors">
                📊 Análisis completo
              </button>
            </div>
          </div>
          
          <div class="grid grid-cols-1 md:grid-cols-3 gap-5 p-6">
            <div class="group bg-gradient-to-br from-purple-50 to-purple-100 dark:from-purple-950/50 dark:to-purple-900/30 rounded-2xl p-5 border-2 border-purple-200 dark:border-purple-800/50 hover:shadow-xl hover:shadow-purple-500/20 transition-all duration-300 hover:-translate-y-1">
              <div class="flex items-center gap-3 mb-3">
                <div class="w-12 h-12 rounded-xl bg-purple-600 dark:bg-purple-500 flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform">
                  <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                  </svg>
                </div>
                <div class="px-3 py-1 bg-purple-200 dark:bg-purple-900/50 rounded-lg">
                  <p class="text-[10px] font-black text-purple-700 dark:text-purple-400 uppercase">Total</p>
                </div>
              </div>
              <p class="text-xs text-purple-700 dark:text-purple-400 font-bold uppercase tracking-wider mb-2">Total Clientes</p>
              <p class="text-4xl font-black text-purple-800 dark:text-purple-300">{{ customersReport.total }}</p>
              <p class="text-xs text-gray-600 dark:text-zinc-400 font-medium mt-2">Base de datos activa</p>
            </div>
            <div class="group bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-950/50 dark:to-blue-900/30 rounded-2xl p-5 border-2 border-blue-200 dark:border-blue-800/50 hover:shadow-xl hover:shadow-blue-500/20 transition-all duration-300 hover:-translate-y-1">
              <div class="flex items-center gap-3 mb-3">
                <div class="w-12 h-12 rounded-xl bg-blue-600 dark:bg-blue-500 flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform">
                  <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                  </svg>
                </div>
                <div class="px-3 py-1 bg-blue-200 dark:bg-blue-900/50 rounded-lg">
                  <p class="text-[10px] font-black text-blue-700 dark:text-blue-400 uppercase">✨ Nuevos</p>
                </div>
              </div>
              <p class="text-xs text-blue-700 dark:text-blue-400 font-bold uppercase tracking-wider mb-2">Clientes Nuevos</p>
              <p class="text-4xl font-black text-blue-800 dark:text-blue-300">{{ customersReport.newCustomers }}</p>
              <p class="text-xs text-gray-600 dark:text-zinc-400 font-medium mt-2">Este período</p>
            </div>
            <div class="group bg-gradient-to-br from-emerald-50 to-emerald-100 dark:from-emerald-950/50 dark:to-emerald-900/30 rounded-2xl p-5 border-2 border-emerald-200 dark:border-emerald-800/50 hover:shadow-xl hover:shadow-emerald-500/20 transition-all duration-300 hover:-translate-y-1">
              <div class="flex items-center gap-3 mb-3">
                <div class="w-12 h-12 rounded-xl bg-emerald-600 dark:bg-emerald-500 flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform">
                  <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                  </svg>
                </div>
                <div class="px-3 py-1 bg-emerald-200 dark:bg-emerald-900/50 rounded-lg">
                  <p class="text-[10px] font-black text-emerald-700 dark:text-emerald-400 uppercase">💚 Leales</p>
                </div>
              </div>
              <p class="text-xs text-emerald-700 dark:text-emerald-400 font-bold uppercase tracking-wider mb-2">Recurrentes</p>
              <p class="text-4xl font-black text-emerald-800 dark:text-emerald-300">{{ customersReport.recurring }}</p>
              <p class="text-xs text-gray-600 dark:text-zinc-400 font-medium mt-2">Clientes fieles</p>
            </div>
          </div>
        </div>

        <!-- 💳 GASTOS Y RENTABILIDAD - Mejorado -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
          
          <!-- Gastos Operativos -->
          <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-gray-300 dark:border-zinc-800 shadow-xl dark:shadow-black/50 overflow-hidden">
            <div class="bg-gradient-to-r from-red-50 to-orange-50 dark:from-zinc-900 dark:to-zinc-900 border-b border-gray-200 dark:border-zinc-800 px-6 py-5">
              <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-red-600 to-orange-600 flex items-center justify-center shadow-lg">
                  <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2zM10 8.5a.5.5 0 11-1 0 .5.5 0 011 0zm5 5a.5.5 0 11-1 0 .5.5 0 011 0z"></path>
                  </svg>
                </div>
                <div>
                  <h2 class="text-lg font-black text-gray-900 dark:text-white">💸 Gastos Operativos</h2>
                  <p class="text-xs text-gray-600 dark:text-zinc-400 font-medium mt-0.5">Distribución de egresos por categoría</p>
                </div>
              </div>
            </div>
            <div class="p-6">
              <div class="mb-6 bg-gradient-to-br from-red-50 to-orange-50 dark:from-red-950/30 dark:to-orange-950/20 rounded-2xl p-5 border-2 border-red-200 dark:border-red-800/50">
                <div class="flex items-center gap-3 mb-2">
                  <div class="w-10 h-10 rounded-lg bg-red-600 flex items-center justify-center shadow-lg">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                    </svg>
                  </div>
                  <div>
                    <p class="text-xs text-red-700 dark:text-red-400 font-bold uppercase tracking-wider">Total Gastos del Período</p>
                    <p class="text-3xl font-black text-red-800 dark:text-red-300">${{ formatNumber(expensesReport.total) }}</p>
                  </div>
                </div>
              </div>
              <div class="bg-gradient-to-br from-gray-50 to-gray-100/50 dark:from-zinc-800/50 dark:to-zinc-800/30 rounded-2xl border-2 border-gray-200 dark:border-zinc-700 p-4">
                <h3 class="text-xs font-bold text-gray-900 dark:text-white mb-3 uppercase tracking-wider">Desglose por Categoría</h3>
                <div style="height: 240px;">
                  <Doughnut :data="expensesCategoryChart" :options="doughnutOptions" />
                </div>
              </div>
            </div>
          </div>

          <!-- Margen y Rentabilidad -->
          <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-gray-300 dark:border-zinc-800 shadow-xl dark:shadow-black/50 overflow-hidden">
            <div class="bg-gradient-to-r from-emerald-50 to-teal-50 dark:from-zinc-900 dark:to-zinc-900 border-b border-gray-200 dark:border-zinc-800 px-6 py-5">
              <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-emerald-600 to-teal-600 flex items-center justify-center shadow-lg">
                  <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                  </svg>
                </div>
                <div>
                  <h2 class="text-lg font-black text-gray-900 dark:text-white">📊 Rentabilidad</h2>
                  <p class="text-xs text-gray-600 dark:text-zinc-400 font-medium mt-0.5">Indicadores financieros clave</p>
                </div>
              </div>
            </div>
            <div class="p-6 space-y-4">
              <div class="group bg-gradient-to-br from-emerald-50 to-emerald-100 dark:from-emerald-950/50 dark:to-emerald-900/30 rounded-2xl p-5 border-2 border-emerald-200 dark:border-emerald-800/50 hover:shadow-xl hover:shadow-emerald-500/20 transition-all duration-300 hover:-translate-y-1">
                <div class="flex items-center gap-3 mb-3">
                  <div class="w-12 h-12 rounded-xl bg-emerald-600 dark:bg-emerald-500 flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                    </svg>
                  </div>
                  <div class="px-3 py-1.5 bg-emerald-200 dark:bg-emerald-900/50 rounded-lg">
                    <p class="text-xs font-black text-emerald-700 dark:text-emerald-400">✅ Positivo</p>
                  </div>
                </div>
                <p class="text-xs text-emerald-700 dark:text-emerald-400 font-bold uppercase tracking-wider mb-2">Utilidad Neta</p>
                <p class="text-4xl font-black text-emerald-800 dark:text-emerald-300">${{ formatNumber(profitReport.netProfit) }}</p>
                <p class="text-xs text-gray-600 dark:text-zinc-400 font-medium mt-2">Ganancias después de gastos</p>
              </div>
              
              <div class="group bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-950/50 dark:to-blue-900/30 rounded-2xl p-5 border-2 border-blue-200 dark:border-blue-800/50 hover:shadow-xl hover:shadow-blue-500/20 transition-all duration-300 hover:-translate-y-1">
                <div class="flex items-center gap-3 mb-3">
                  <div class="w-12 h-12 rounded-xl bg-blue-600 dark:bg-blue-500 flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"></path>
                    </svg>
                  </div>
                  <div class="px-3 py-1.5 bg-blue-200 dark:bg-blue-900/50 rounded-lg">
                    <p class="text-xs font-black text-blue-700 dark:text-blue-400">📈 Margen</p>
                  </div>
                </div>
                <p class="text-xs text-blue-700 dark:text-blue-400 font-bold uppercase tracking-wider mb-2">Margen Bruto</p>
                <p class="text-4xl font-black text-blue-800 dark:text-blue-300">{{ profitReport.grossMargin }}%</p>
                <p class="text-xs text-gray-600 dark:text-zinc-400 font-medium mt-2">Rentabilidad sobre ventas</p>
              </div>
              
              <div class="group bg-gradient-to-br from-purple-50 to-purple-100 dark:from-purple-950/50 dark:to-purple-900/30 rounded-2xl p-5 border-2 border-purple-200 dark:border-purple-800/50 hover:shadow-xl hover:shadow-purple-500/20 transition-all duration-300 hover:-translate-y-1">
                <div class="flex items-center gap-3 mb-3">
                  <div class="w-12 h-12 rounded-xl bg-purple-600 dark:bg-purple-500 flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                    </svg>
                  </div>
                  <div class="px-3 py-1.5 bg-purple-200 dark:bg-purple-900/50 rounded-lg">
                    <p class="text-xs font-black text-purple-700 dark:text-purple-400">🎯 ROI</p>
                  </div>
                </div>
                <p class="text-xs text-purple-700 dark:text-purple-400 font-bold uppercase tracking-wider mb-2">Retorno de Inversión</p>
                <p class="text-4xl font-black text-purple-800 dark:text-purple-300">{{ profitReport.roi }}%</p>
                <p class="text-xs text-gray-600 dark:text-zinc-400 font-medium mt-2">Rendimiento de capital</p>
              </div>
            </div>
          </div>

        </div>

      </div>

    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import { reportsService } from '../services/reportsService.js'
import { cashReportsService } from '../services/cashReportsService.js'
import { Line, Bar, Doughnut } from 'vue-chartjs'
import {
  Chart,
  CategoryScale,
  LinearScale,
  PointElement,
  LineElement,
  BarElement,
  ArcElement,
  Title,
  Tooltip,
  Legend
} from 'chart.js'

Chart.register(
  CategoryScale,
  LinearScale,
  PointElement,
  LineElement,
  BarElement,
  ArcElement,
  Title,
  Tooltip,
  Legend
)

// Estado
const selectedPeriod = ref('today')
const loading = ref(false)
const error = ref(null)

// Datos de reportes
const kpis = ref({
  totalSales: 0,
  salesGrowth: 0,
  totalTransactions: 0,
  transactionsGrowth: 0,
  averageTicket: 0,
  ticketGrowth: 0,
  totalProducts: 0,
  lowStock: 0
})

const cashReport = ref({
  activeSessions: 0,
  totalInCash: 0,
  averageEfficiency: 0,
  cashiers: []
})

const inventoryReport = ref({
  topProducts: [],
  lowStock: []
})

const customersReport = ref({
  total: 0,
  newCustomers: 0,
  recurring: 0
})

const expensesReport = ref({
  total: 0,
  categories: []
})

const profitReport = ref({
  netProfit: 0,
  grossMargin: 0,
  roi: 0
})

const salesData = ref([])
const paymentMethodsData = ref([])

// Funciones
const getPeriodLabel = () => {
  const labels = {
    today: 'Últimas 24 horas',
    week: 'Últimos 7 días',
    month: 'Últimos 30 días',
    year: 'Últimos 12 meses'
  }
  return labels[selectedPeriod.value] || 'Período seleccionado'
}

const formatNumber = (num) => {
  return Math.round(num || 0).toLocaleString('es-CO')
}

const getEfficiencyClass = (efficiency) => {
  if (efficiency >= 90) return 'px-2 py-0.5 rounded-md text-xs font-bold bg-emerald-50 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-400 border border-emerald-100 dark:border-emerald-800'
  if (efficiency >= 70) return 'px-2 py-0.5 rounded-md text-xs font-bold bg-blue-50 dark:bg-blue-950 text-blue-700 dark:text-blue-400 border border-blue-100 dark:border-blue-800'
  return 'px-2 py-0.5 rounded-md text-xs font-bold bg-amber-50 dark:bg-amber-950 text-amber-700 dark:text-amber-400 border border-amber-100 dark:border-amber-800'
}

const loadAllReports = async () => {
  loading.value = true
  error.value = null
  
  try {
    // Cargar datos de ventas
    const [salesResponse, cashMetrics, cashierComparison, topProducts, paymentMethods] = await Promise.all([
      reportsService.getSalesData(selectedPeriod.value),
      cashReportsService.getCashMetrics(selectedPeriod.value),
      cashReportsService.getCashierComparison(selectedPeriod.value),
      reportsService.getTopProducts(selectedPeriod.value, 10),
      cashReportsService.getPaymentMethods(selectedPeriod.value)
    ])

    // KPIs principales
    if (salesResponse.success) {
      kpis.value.totalSales = salesResponse.data.totalSales || 0
      kpis.value.totalTransactions = salesResponse.data.totalTransactions || 0
      kpis.value.averageTicket = salesResponse.data.averageTicket || 0
      kpis.value.salesGrowth = 15
      kpis.value.transactionsGrowth = 8
      kpis.value.ticketGrowth = 5
    }

    // Reportes de caja
    if (cashMetrics.success) {
      cashReport.value.activeSessions = cashMetrics.active_sessions || 0
      cashReport.value.totalInCash = cashMetrics.total_sales || 0
      cashReport.value.averageEfficiency = Math.round(cashMetrics.average_efficiency || 85)
    }

    if (cashierComparison.success && cashierComparison.data) {
      cashReport.value.cashiers = cashierComparison.data.map(c => ({
        id: c.cashier_id,
        name: c.cashier_name,
        sessions: c.total_sessions,
        sales: c.total_sales,
        transactions: c.total_transactions,
        efficiency: Math.round(c.efficiency || 85)
      }))
    }

    // Top productos
    if (topProducts.success) {
      inventoryReport.value.topProducts = topProducts.data
      salesData.value = topProducts.data.map(p => p.revenue || 0)
    }

    // Métodos de pago
    if (paymentMethods.success && paymentMethods.data) {
      paymentMethodsData.value = paymentMethods.data
    }

    // Datos de ejemplo para inventario bajo stock
    inventoryReport.value.lowStock = [
      { id: 1, name: 'Producto A', category: 'Bebidas', stock: 5 },
      { id: 2, name: 'Producto B', category: 'Snacks', stock: 3 },
      { id: 3, name: 'Producto C', category: 'Lácteos', stock: 2 }
    ]

    // Datos de ejemplo para clientes
    customersReport.value = {
      total: 1250,
      newCustomers: 85,
      recurring: 420
    }

    // Datos de ejemplo para gastos
    expensesReport.value = {
      total: 15000,
      categories: [
        { name: 'Salarios', amount: 8000 },
        { name: 'Servicios', amount: 3500 },
        { name: 'Inventario', amount: 2500 },
        { name: 'Otros', amount: 1000 }
      ]
    }

    // Datos de ejemplo para rentabilidad
    profitReport.value = {
      netProfit: kpis.value.totalSales - expensesReport.value.total,
      grossMargin: 35.2,
      roi: 28.5
    }

    kpis.value.totalProducts = inventoryReport.value.topProducts.length
    kpis.value.lowStock = inventoryReport.value.lowStock.length

  } catch (err) {
    console.error('Error cargando reportes:', err)
    error.value = 'Error al cargar los reportes'
  } finally {
    loading.value = false
  }
}

const exportAllReports = () => {
  console.log('Exportando todos los reportes...')
  alert('Función de exportación en desarrollo')
}

// Charts
const salesTrendChart = computed(() => {
  const length = selectedPeriod.value === 'today' ? 24 : 7
  const labels = Array.from({ length }, (_, i) => 
    selectedPeriod.value === 'today' ? `${i}:00` : `Día ${i + 1}`
  )
  
  // Generar datos de ejemplo si no hay datos reales
  const data = salesData.value.length > 0 ? salesData.value : Array.from({ length }, () => Math.floor(Math.random() * 500000) + 100000)
  
  return {
    labels,
    datasets: [{
      label: 'Ventas',
      data,
      borderColor: '#3b82f6',
      backgroundColor: 'rgba(59, 130, 246, 0.1)',
      borderWidth: 2.5,
      tension: 0.4,
      fill: true,
      pointRadius: 3,
      pointHoverRadius: 5,
      pointBackgroundColor: '#3b82f6',
      pointBorderColor: '#fff',
      pointBorderWidth: 2
    }]
  }
})

const paymentMethodsChart = computed(() => {
  // Si no hay datos, usar datos de ejemplo
  const hasData = paymentMethodsData.value.length > 0
  const labels = hasData 
    ? paymentMethodsData.value.map(pm => pm.method_name || 'Efectivo')
    : ['Efectivo', 'Tarjeta', 'Transferencia', 'Otro']
  const data = hasData
    ? paymentMethodsData.value.map(pm => pm.total || 0)
    : [450000, 350000, 200000, 50000]
  
  return {
    labels,
    datasets: [{
      data,
      backgroundColor: ['#3b82f6', '#10b981', '#f59e0b', '#8b5cf6', '#ec4899'],
      borderWidth: 0,
      hoverOffset: 10
    }]
  }
})

const topProductsChart = computed(() => ({
  labels: inventoryReport.value.topProducts.map(p => p.name),
  datasets: [{
    label: 'Ventas',
    data: inventoryReport.value.topProducts.map(p => p.revenue || 0),
    backgroundColor: '#3b82f6',
    borderRadius: 6
  }]
}))

const expensesCategoryChart = computed(() => ({
  labels: expensesReport.value.categories.map(c => c.name),
  datasets: [{
    data: expensesReport.value.categories.map(c => c.amount),
    backgroundColor: ['#ef4444', '#f59e0b', '#3b82f6', '#8b5cf6']
  }]
}))

const chartOptions = {
  responsive: true,
  maintainAspectRatio: false,
  plugins: {
    legend: { 
      display: false 
    },
    tooltip: {
      backgroundColor: 'rgba(0, 0, 0, 0.8)',
      padding: 12,
      titleFont: {
        size: 13,
        weight: 'bold'
      },
      bodyFont: {
        size: 12
      },
      callbacks: {
        label: (context) => {
          return `Ventas: $${context.parsed.y.toLocaleString('es-CO')}`
        }
      }
    }
  },
  scales: {
    x: {
      grid: {
        display: false
      },
      ticks: {
        color: '#9ca3af',
        font: {
          size: 11
        }
      }
    },
    y: {
      beginAtZero: true,
      grid: {
        color: 'rgba(156, 163, 175, 0.1)'
      },
      ticks: {
        color: '#9ca3af',
        font: {
          size: 11
        },
        callback: (value) => `$${(value / 1000).toFixed(0)}K`
      }
    }
  }
}

const barChartOptions = {
  responsive: true,
  maintainAspectRatio: false,
  indexAxis: 'y',
  plugins: {
    legend: { 
      display: false 
    },
    tooltip: {
      backgroundColor: 'rgba(0, 0, 0, 0.8)',
      padding: 12,
      titleFont: {
        size: 13,
        weight: 'bold'
      },
      bodyFont: {
        size: 12
      }
    }
  },
  scales: {
    x: {
      grid: {
        color: 'rgba(156, 163, 175, 0.1)'
      },
      ticks: {
        color: '#9ca3af',
        font: {
          size: 11
        }
      }
    },
    y: {
      grid: {
        display: false
      },
      ticks: {
        color: '#9ca3af',
        font: {
          size: 11
        }
      }
    }
  }
}

const doughnutOptions = {
  responsive: true,
  maintainAspectRatio: false,
  plugins: {
    legend: { 
      position: 'bottom',
      labels: {
        color: '#9ca3af',
        font: {
          size: 11
        },
        padding: 15,
        usePointStyle: true,
        pointStyle: 'circle'
      }
    },
    tooltip: {
      backgroundColor: 'rgba(0, 0, 0, 0.8)',
      padding: 12,
      titleFont: {
        size: 13,
        weight: 'bold'
      },
      bodyFont: {
        size: 12
      },
      callbacks: {
        label: (context) => {
          const label = context.label || ''
          const value = context.parsed || 0
          const total = context.dataset.data.reduce((a, b) => a + b, 0)
          const percentage = ((value / total) * 100).toFixed(1)
          return `${label}: $${value.toLocaleString('es-CO')} (${percentage}%)`
        }
      }
    }
  }
}

onMounted(() => {
  loadAllReports()
})
</script>

<style scoped>
.animate-fade-in {
  animation: fadeIn 0.5s ease-in-out;
}

@keyframes fadeIn {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

/* Custom Scrollbar */
.custom-scrollbar::-webkit-scrollbar {
  width: 6px;
}

.custom-scrollbar::-webkit-scrollbar-track {
  background: transparent;
  border-radius: 10px;
}

.custom-scrollbar::-webkit-scrollbar-thumb {
  background: linear-gradient(to bottom, #ef4444, #f97316);
  border-radius: 10px;
}

.custom-scrollbar::-webkit-scrollbar-thumb:hover {
  background: linear-gradient(to bottom, #dc2626, #ea580c);
}

/* Dark mode scrollbar */
:global(.dark) .custom-scrollbar::-webkit-scrollbar-thumb {
  background: linear-gradient(to bottom, #dc2626, #ea580c);
}

/* Animaciones adicionales */
@keyframes pulse-soft {
  0%, 100% {
    opacity: 1;
  }
  50% {
    opacity: 0.8;
  }
}

.group:hover .group-hover\:scale-110 {
  transform: scale(1.1);
}

/* Efectos de glassmorphism mejorados */
.backdrop-blur-sm {
  backdrop-filter: blur(8px);
}

/* Transiciones suaves */
* {
  transition-property: background-color, border-color, color, fill, stroke, opacity, box-shadow, transform;
  transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
}
</style>
