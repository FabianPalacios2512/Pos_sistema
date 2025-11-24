<template>
  <div class="min-h-screen bg-gray-100 font-sans">
    <div class="p-4 lg:p-6 space-y-6 animate-fade-in">
      
      <!-- Header Simple y Elegante -->
      <div class="flex items-center justify-between pb-4 border-b border-gray-300">
        <div class="flex items-center space-x-4">
          <div class="w-12 h-12 bg-black rounded-xl flex items-center justify-center shadow-lg">
            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
          </div>
          <div>
            <h1 class="text-2xl font-bold text-gray-900">Reportes de Caja</h1>
            <p class="text-sm text-gray-600">Análisis avanzado por cajero • {{ getPeriodLabel() }}</p>
          </div>
        </div>
        
        <div class="flex items-center space-x-3">
          <!-- Controles compactos -->
          <select 
            v-model="selectedPeriod" 
            @change="handlePeriodChange"
            class="px-4 py-2.5 bg-white border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all duration-200 shadow-sm text-sm"
          >
            <option value="today">📅 Hoy</option>
            <option value="week">📊 Esta semana</option>
            <option value="month">📈 Este mes</option>
            <option value="year">🎯 Este año</option>
          </select>
          
          <input 
            type="date"
            v-model="customDate"
            @change="loadCashReportsData"
            class="px-4 py-2.5 bg-white border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all duration-200 shadow-sm text-sm"
          />
          
          <input 
            type="date"
            v-model="customEndDate"
            @change="loadCashReportsData"
            :min="customDate"
            class="px-4 py-2.5 bg-white border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all duration-200 shadow-sm text-sm"
            placeholder="Hasta"
          />

          <!-- Botón Actualizar -->
          <button 
            @click="loadCashReportsData" 
            class="px-4 py-2 bg-slate-50 hover:bg-slate-100 text-slate-600 text-sm font-semibold rounded-xl border border-slate-200 shadow-sm transition-all duration-200 flex items-center space-x-2 hover:shadow-md hover:border-slate-300"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
            </svg>
            <span>Actualizar</span>
          </button>

          <button 
            @click="exportCashReport" 
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
        <span class="text-gray-600">Cargando reportes de caja...</span>
      </div>
    </div>

    <!-- Mensaje de error -->
    <div v-else-if="error" class="bg-red-50 border border-red-200 rounded-xl p-6 mb-6">
      <div class="flex items-center space-x-3">
        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
        <div>
          <h3 class="text-red-800 font-medium">Error al cargar reportes de caja</h3>
          <p class="text-red-600 text-sm">{{ error }}</p>
        </div>
      </div>
      <button @click="loadCashReportsData" class="mt-4 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
        Reintentar
      </button>
    </div>

    <!-- Contenido principal -->
    <div v-else class="space-y-6">
      
      <!-- Métricas Principales por Cajero -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        
        <!-- Total de sesiones activas -->
        <div class="bg-white rounded-2xl p-5 border border-gray-300 hover:border-gray-400 transition-all duration-200 hover:shadow-lg">
          <div class="flex items-center space-x-4">
            <div class="w-12 h-12 bg-gray-100 rounded-xl flex items-center justify-center flex-shrink-0">
              <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
              </svg>
            </div>
            <div class="flex-1 min-w-0">
              <div class="flex items-center justify-between mb-2">
                <h3 class="text-sm font-semibold text-gray-700">Sesiones Activas</h3>
                <span class="text-xs font-medium text-blue-700 bg-blue-50 px-2 py-1 rounded-lg border border-blue-200">
                  Activo
                </span>
              </div>
              <p class="text-2xl font-bold text-gray-900 mb-1">{{ activeSessions.length }}</p>
              <p class="text-sm text-gray-500">{{ getActiveSessionsChange() }}% vs ayer</p>
            </div>
          </div>
        </div>

        <!-- Mejor cajero del día -->
        <div class="bg-white rounded-2xl p-5 border border-gray-300 hover:border-gray-400 transition-all duration-200 hover:shadow-lg">
          <div class="flex items-center space-x-4">
            <div class="w-12 h-12 bg-gray-100 rounded-xl flex items-center justify-center flex-shrink-0">
              <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
              </svg>
            </div>
            <div class="flex-1 min-w-0">
              <div class="flex items-center justify-between mb-2">
                <h3 class="text-sm font-semibold text-gray-700">Mejor Cajero</h3>
                <span class="text-xs font-medium text-green-700 bg-green-50 px-2 py-1 rounded-lg border border-green-200">
                  TOP
                </span>
              </div>
              <p class="text-xl font-bold text-gray-900 mb-1">{{ bestCashier.name }}</p>
              <p class="text-sm text-gray-500">${{ (bestCashier.sales || 0).toLocaleString() }} ventas</p>
            </div>
          </div>
        </div>

        <!-- Promedio de ventas por hora -->
        <div class="bg-white rounded-2xl p-5 border border-gray-300 hover:border-gray-400 transition-all duration-200 hover:shadow-lg">
          <div class="flex items-center space-x-4">
            <div class="w-12 h-12 bg-gray-100 rounded-xl flex items-center justify-center flex-shrink-0">
              <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
              </svg>
            </div>
            <div class="flex-1 min-w-0">
              <div class="flex items-center justify-between mb-2">
                <h3 class="text-sm font-semibold text-gray-700">Promedio/Hora</h3>
                <span class="text-xs font-medium text-amber-700 bg-amber-50 px-2 py-1 rounded-lg border border-amber-200">
                  +{{ getHourlyGrowth() }}%
                </span>
              </div>
              <p class="text-2xl font-bold text-gray-900 mb-1">${{ averageSalesPerHour.toLocaleString() }}</p>
              <p class="text-sm text-gray-500">vs promedio</p>
            </div>
          </div>
        </div>

        <!-- Total de transacciones -->
        <div class="bg-white rounded-2xl p-5 border border-gray-300 hover:border-gray-400 transition-all duration-200 hover:shadow-lg">
          <div class="flex items-center space-x-4">
            <div class="w-12 h-12 bg-gray-100 rounded-xl flex items-center justify-center flex-shrink-0">
              <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
              </svg>
            </div>
            <div class="flex-1 min-w-0">
              <div class="flex items-center justify-between mb-2">
                <h3 class="text-sm font-semibold text-gray-700">Transacciones</h3>
                <span class="text-xs font-medium text-green-700 bg-green-50 px-2 py-1 rounded-lg border border-green-200">
                  {{ getTransactionGrowth() }}%
                </span>
              </div>
              <p class="text-2xl font-bold text-gray-900 mb-1">{{ totalTransactions.toLocaleString() }}</p>
              <p class="text-sm text-gray-500">vs período anterior</p>
            </div>
          </div>
        </div>

      </div>

      <!-- Gráficos de análisis -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        
        <!-- Rendimiento por Cajero -->
        <div class="bg-white rounded-2xl p-6 border border-gray-300 hover:border-gray-400 transition-all duration-200 hover:shadow-lg">
          <div class="bg-gray-50 border-b border-gray-200 px-5 py-4 flex items-center justify-between">
            <div>
              <h2 class="text-base font-bold text-gray-900">Rendimiento por Cajero</h2>
              <p class="text-xs text-gray-500 mt-0.5">Comparativa para {{ getPeriodLabel() }}</p>
            </div>
            <div class="w-10 h-10 bg-green-100 rounded-xl flex items-center justify-center">
              <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
              </svg>
            </div>
          </div>
          <div class="p-4">
            <div style="height: 280px;">
              <Bar :data="cashierPerformanceChart" :options="chartOptions" />
            </div>
          </div>
        </div>

        <!-- Eficiencia por Hora -->
        <div class="bg-white rounded-2xl p-6 border border-gray-300 hover:border-gray-400 transition-all duration-200 hover:shadow-lg">
          <div class="bg-gray-50 border-b border-gray-200 px-5 py-4 flex items-center justify-between">
            <div>
              <h2 class="text-base font-bold text-gray-900">Eficiencia por Hora</h2>
              <p class="text-xs text-gray-500 mt-0.5">Análisis de productividad horaria</p>
            </div>
            <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center">
              <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
              </svg>
            </div>
          </div>
          <div class="p-4">
            <div style="height: 280px;">
              <Line :data="hourlyEfficiencyChart" :options="lineChartOptions" />
            </div>
          </div>
        </div>

      </div>

      <!-- Tabla comparativa detallada -->
      <div class="bg-white rounded-2xl p-6 border border-gray-300">
        <div class="bg-gray-50 border-b border-gray-200 px-5 py-4 flex items-center justify-between">
          <div>
            <h2 class="text-base font-bold text-gray-900">Análisis Comparativo Detallado</h2>
            <p class="text-xs text-gray-500 mt-0.5">Métricas completas por cajero y sesión</p>
          </div>
          <div class="flex gap-3">
            <button 
              @click="exportCashierComparison" 
              class="px-4 py-2 bg-gradient-to-r from-sky-500 to-blue-500 hover:from-sky-600 hover:to-blue-600 text-white rounded-xl text-sm font-semibold transition-all duration-200 flex items-center space-x-2"
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
            <thead class="bg-gradient-to-r from-purple-50 to-indigo-50">
              <tr>
                <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 uppercase tracking-wider">Cajero</th>
                <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 uppercase tracking-wider">Sesiones</th>
                <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 uppercase tracking-wider">Total Ventas</th>
                <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 uppercase tracking-wider">Transacciones</th>
                <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 uppercase tracking-wider">Promedio/Venta</th>
                <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 uppercase tracking-wider">Eficiencia</th>
                <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 uppercase tracking-wider">Horas Trabajadas</th>
                <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 uppercase tracking-wider">Ventas/Hora</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="cashier in cashierComparison" :key="cashier.id" class="hover:bg-gray-50 transition-colors">
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="flex items-center">
                    <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center mr-4">
                      <span class="text-white font-semibold text-sm">{{ getInitials(cashier.name) }}</span>
                    </div>
                    <div>
                      <div class="text-sm font-medium text-gray-900">{{ cashier.name }}</div>
                      <div class="text-sm text-gray-500">{{ cashier.role }}</div>
                    </div>
                  </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                  <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                    {{ cashier.sessions || 1 }}
                  </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-green-600">
                  ${{ parseFloat(cashier.total_sales || 0).toLocaleString() }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ cashier.transactions }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${{ parseFloat(cashier.average_sale || 0).toFixed(2) }}</td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="flex items-center">
                    <div class="w-16 bg-gray-200 rounded-full h-2 mr-2">
                      <div class="bg-gradient-to-r from-green-400 to-green-600 h-2 rounded-full" :style="`width: ${parseFloat(cashier.efficiency || 0)}%`"></div>
                    </div>
                    <span class="text-sm font-medium text-gray-900">{{ parseFloat(cashier.efficiency || 0).toFixed(1) }}%</span>
                  </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ cashier.hoursWorked || 8 }}h</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                  ${{ (parseFloat(cashier.total_sales || 0) / (cashier.hoursWorked || 8)).toFixed(0) }}
                </td>
              </tr>
              
              <!-- Estado vacío -->
              <tr v-if="!cashierComparison || cashierComparison.length === 0" class="hover:bg-gray-50">
                <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                  <div class="flex flex-col items-center">
                    <svg class="w-16 h-16 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                    <h3 class="text-lg font-medium text-gray-700 mb-1">No hay datos de cajeros</h3>
                    <p class="text-sm text-gray-500">No se encontraron datos para el período seleccionado</p>
                    <button @click="loadCashReportsData" class="mt-3 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-sm">
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
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <!-- Top 5 mejores sesiones -->
        <div class="bg-white rounded-3xl shadow-2xl p-8 border border-gray-100">
          <h3 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
            <div class="w-3 h-3 bg-yellow-500 rounded-full mr-3"></div>
            Top 5 Mejores Sesiones
          </h3>
          <div class="space-y-4">
            <div v-for="(session, index) in topSessions" :key="session.id" class="flex items-center justify-between p-4 bg-gradient-to-r from-yellow-50 to-orange-50 rounded-xl">
              <div class="flex items-center">
                <div class="w-8 h-8 bg-gradient-to-br from-yellow-400 to-orange-500 rounded-full flex items-center justify-center mr-3">
                  <span class="text-white font-bold text-sm">{{ index + 1 }}</span>
                </div>
                <div>
                  <div class="font-medium text-gray-900">{{ session.cashier }}</div>
                  <div class="text-sm text-gray-500">{{ session.date }}</div>
                </div>
              </div>
              <div class="text-right">
                <div class="font-bold text-green-600">${{ parseFloat(session.sales || 0).toLocaleString() }}</div>
                <div class="text-sm text-gray-500">{{ Math.abs(session.duration || 0) }}h</div>
              </div>
            </div>
          </div>
        </div>

        <!-- Métodos de pago más usados -->
        <div class="bg-white rounded-3xl shadow-2xl p-8 border border-gray-100">
          <h3 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
            <div class="w-3 h-3 bg-indigo-500 rounded-full mr-3"></div>
            Métodos de Pago
          </h3>
          <div style="height: 280px;" class="flex justify-center items-center">
            <Doughnut :data="paymentMethodsChart" :options="doughnutOptions" style="max-height: 250px; max-width: 250px;"/>
          </div>
        </div>

        <!-- Alertas y recomendaciones -->
        <div class="bg-white rounded-3xl shadow-2xl p-8 border border-gray-100">
          <h3 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
            <div class="w-3 h-3 bg-red-500 rounded-full mr-3"></div>
            Alertas y Recomendaciones
          </h3>
          <div class="space-y-4">
            <div v-for="alert in alerts" :key="alert.id" class="p-4 rounded-xl" :class="getAlertClass(alert.type)">
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
import { ref, computed, onMounted, watch } from 'vue'
import { Bar, Line, Doughnut } from 'vue-chartjs'
import { Chart as ChartJS, CategoryScale, LinearScale, BarElement, PointElement, LineElement, ArcElement, Title, Tooltip, Legend } from 'chart.js'
import { cashReportsService } from '../services/cashReportsService.js'
import { exportService } from '../services/exportService.js'
import { useToast } from '../composables/useToast.js'

// Registrar componentes de Chart.js
ChartJS.register(CategoryScale, LinearScale, BarElement, PointElement, LineElement, ArcElement, Title, Tooltip, Legend)

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
  console.log('🗓️ Fecha Colombia calculada:', result)
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
      bodyColor: '#ffffff'
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
        callback: (value) => `$${(value / 1000000).toFixed(1)}M`
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
      bodyColor: '#ffffff'
    }
  },
  scales: {
    y: { 
      beginAtZero: true,
      grid: { color: '#e5e7eb' },
      ticks: { 
        color: '#6b7280',
        callback: (value) => `$${value.toLocaleString()}`
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
  return Math.round(Math.random() * 20 + 5) // Simulado
}

const getHourlyGrowth = () => {
  return Math.round(Math.random() * 15 + 5) // Simulado
}

const getTransactionGrowth = () => {
  return Math.round(Math.random() * 25 + 10) // Simulado
}

const getAlertClass = (type) => {
  const classes = {
    warning: 'bg-gradient-to-r from-yellow-50 to-orange-50 border border-yellow-200',
    success: 'bg-gradient-to-r from-green-50 to-emerald-50 border border-green-200',
    info: 'bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200',
    error: 'bg-gradient-to-r from-red-50 to-pink-50 border border-red-200'
  }
  return classes[type] || classes.info
}

const getAlertIconClass = (type) => {
  const classes = {
    warning: 'text-yellow-600',
    success: 'text-green-600',
    info: 'text-blue-600',
    error: 'text-red-600'
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
    console.log('Exportando reporte completo de caja...')
    
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
    console.log('Exportando comparativa de cajeros...')
    
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
      console.log('✅ Datos de eficiencia por hora cargados correctamente:', {
        totalHours: hourlyResponse.data.hourly_data.length,
        timezone: hourlyResponse.data.timezone,
        totalSales: hourlyResponse.data.total_sales,
        peakHour: hourlyResponse.data.peak_hour,
        sampleData: hourlyResponse.data.hourly_data.slice(0, 3)
      })
    } else {
      console.warn('⚠️ No se encontraron datos de eficiencia por hora válidos:', hourlyResponse)
      hourlyEfficiencyData.value = []
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

// Cargar datos al montar
onMounted(() => {
  loadCashReportsData()
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

/* Estilos personalizados */
* {
  transition: all 0.2s ease-in-out;
}
</style>