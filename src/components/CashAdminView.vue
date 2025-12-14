<template>
  <div class="min-h-screen bg-gradient-to-br from-gray-50 via-white to-slate-100 dark:bg-gradient-to-b dark:from-[#141417] dark:via-slate-900 dark:to-[#0a0a0c] font-sans transition-colors duration-300 px-8">
    <div class="p-4 lg:p-6 space-y-6 pb-8 animate-fade-in">
      
      <!-- 🎯 Header Elegante y Profesional OBLIGATORIO -->
      <div class="flex items-center justify-between pb-4">
        <div class="flex items-center space-x-4">
          <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Control de Cajas</h1>
            <p class="text-sm text-gray-600 dark:text-zinc-400 mt-1">Administración y supervisión de sesiones de caja</p>
          </div>
        </div>
        
        <div class="flex items-center gap-3">
          <!-- Botón Secundario -->
          <button @click="refreshSessions"
                  :disabled="loading"
                  class="px-5 py-2.5 bg-white dark:bg-[#252530] hover:bg-slate-50 dark:hover:bg-[#2a2a35] text-slate-600 dark:text-zinc-200 text-sm font-bold rounded-xl border border-gray-300 dark:border-zinc-800 shadow-sm transition-all duration-200 flex items-center space-x-2">
            <svg class="w-4 h-4" :class="{'animate-spin': loading}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
            </svg>
            <span>{{ loading ? 'Actualizando...' : 'Refrescar' }}</span>
          </button>
          
          <!-- Botón Principal -->
          <button @click="showNewSessionModal = true"
                  class="px-6 py-2.5 bg-slate-900 dark:bg-slate-700 hover:bg-black dark:hover:bg-slate-600 text-white text-sm font-bold rounded-xl shadow-lg shadow-slate-400/40 dark:shadow-slate-900/50 transition-all duration-300 flex items-center space-x-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            <span>Nueva Sesión</span>
          </button>
        </div>
      </div>

      <!-- Métricas Principales EMPRESARIALES -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        
        <!-- Sesiones Activas (Verde Empresarial) -->
        <div class="bg-white dark:bg-zinc-900/80 backdrop-blur-sm rounded-xl px-4 py-3 border border-gray-300 dark:border-zinc-800 hover:border-gray-400 dark:hover:border-zinc-700/80 transition-all duration-200 shadow-md hover:shadow-lg dark:shadow-lg dark:shadow-black/50">
          <div class="flex items-center gap-3">
            <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0 bg-gray-100 dark:bg-zinc-800/50 border border-gray-200 dark:border-white/5">
              <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
              </svg>
            </div>
            <div class="flex-1 min-w-0">
              <p class="text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wide">Sesiones Activas</p>
              <p class="text-2xl font-bold text-gray-900 dark:text-white mt-0.5">{{ activeSessions.length }}</p>
            </div>
          </div>
        </div>
        
        <!-- Total en Cajas (Ámbar Empresarial) -->
        <div class="bg-white dark:bg-zinc-900/80 backdrop-blur-sm rounded-xl px-4 py-3 border border-gray-300 dark:border-zinc-800 hover:border-gray-400 dark:hover:border-zinc-700/80 transition-all duration-200 shadow-md hover:shadow-lg dark:shadow-lg dark:shadow-black/50">
          <div class="flex items-center gap-3">
            <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0 bg-gray-100 dark:bg-zinc-800/50 border border-gray-200 dark:border-white/5">
              <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
              </svg>
            </div>
            <div class="flex-1 min-w-0">
              <p class="text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wide">Total en Cajas</p>
              <p class="text-2xl font-bold text-gray-900 dark:text-white mt-0.5">${{ totalCashAmount.toLocaleString() }}</p>
            </div>
          </div>
        </div>
        
        <!-- Ventas del Día (Púrpura Empresarial) -->
        <div class="bg-white dark:bg-zinc-900/80 backdrop-blur-sm rounded-xl px-4 py-3 border border-gray-300 dark:border-zinc-800 hover:border-gray-400 dark:hover:border-zinc-700/80 transition-all duration-200 shadow-md hover:shadow-lg dark:shadow-lg dark:shadow-black/50">
          <div class="flex items-center gap-3">
            <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0 bg-gray-100 dark:bg-zinc-800/50 border border-gray-200 dark:border-white/5">
              <svg class="w-5 h-5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
              </svg>
            </div>
            <div class="flex-1 min-w-0">
              <p class="text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wide">Ventas Hoy</p>
              <p class="text-2xl font-bold text-gray-900 dark:text-white mt-0.5">${{ totalSalesToday.toLocaleString() }}</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Panel de Filtros Empresarial -->
      <div class="bg-white dark:bg-zinc-900 rounded-xl shadow-sm dark:shadow-black/50 p-4 border border-gray-300 dark:border-zinc-800">
        <div class="flex flex-wrap items-center gap-4">
          
          <!-- Búsqueda -->
          <div class="flex-1 min-w-64 relative">
            <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-gray-400 dark:text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <input
              v-model="searchQuery"
              type="text"
              placeholder="Buscar usuario..."
              class="w-full pl-10 pr-4 py-3 text-sm rounded-xl border-2 border-gray-200 dark:border-zinc-800 bg-gray-50 dark:bg-[#252530] text-gray-900 dark:text-zinc-200 placeholder-gray-400 dark:placeholder-zinc-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent"
            />
          </div>
          
          <!-- Filtro Estado -->
          <select
            v-model="statusFilter"
            class="px-3 py-3 text-sm rounded-xl border border-gray-300 dark:border-zinc-800 bg-white dark:bg-[#252530] text-gray-700 dark:text-zinc-300 font-medium focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400"
          >
            <option value="">Todos los estados</option>
            <option value="open">🟢 Sesiones Activas</option>
            <option value="closed">🔴 Sesiones Cerradas</option>
          </select>
          
          <!-- Filtro Fecha -->
          <input
            v-model="dateFilter"
            type="date"
            class="px-3 py-3 text-sm rounded-xl border border-gray-300 dark:border-zinc-800 bg-white dark:bg-[#252530] text-gray-700 dark:text-zinc-300 font-medium focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400"
          />
          
          <!-- Botón Exportar -->
          <button
            @click="exportData"
            class="px-4 py-2.5 bg-emerald-600 dark:bg-emerald-700 hover:bg-emerald-700 dark:hover:bg-emerald-600 text-white text-sm font-semibold rounded-xl shadow-sm transition-all duration-200 flex items-center space-x-2"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3M3 17V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v10a2 2 0 01-2 2H5a2 2 0 01-2-2z"/>
            </svg>
            <span>Exportar Excel</span>
          </button>
          
          <!-- Botón Limpiar Filtros -->
          <button
            @click="clearFilters"
            class="p-3 text-gray-500 dark:text-zinc-400 hover:text-red-600 dark:hover:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-xl border border-transparent hover:border-red-100 dark:hover:border-red-900/30 transition-colors duration-200"
            title="Limpiar filtros"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
            </svg>
          </button>
        </div>
      </div>

      <!-- Sessions Table Compacta -->
      <div class="bg-white dark:bg-zinc-900 rounded-2xl shadow-xl dark:shadow-black/50 overflow-hidden border border-gray-300 dark:border-zinc-800">
        <div class="bg-gray-50 dark:bg-zinc-900 border-b border-gray-200 dark:border-zinc-800 px-4 py-3">
          <div class="flex items-center justify-between">
            <div class="flex items-center space-x-3">
              <div class="w-8 h-8 bg-blue-100 dark:bg-blue-950 rounded-lg flex items-center justify-center">
                <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                </svg>
              </div>
              <div>
                <h2 class="text-lg font-bold text-gray-900 dark:text-white">Sesiones de Caja</h2>
                <p class="text-gray-600 dark:text-zinc-400 text-sm">{{ filteredSessions.length }} registros</p>
              </div>
            </div>
            <div class="flex items-center space-x-2">
              <div class="px-3 py-1 bg-blue-50 dark:bg-blue-950 border border-blue-100 dark:border-blue-800 rounded-lg">
                <span class="text-blue-700 dark:text-blue-400 text-xs font-medium">
                  {{ new Date().toLocaleTimeString('es-ES') }}
                </span>
              </div>
            </div>
          </div>
        </div>
        
        <div class="overflow-x-auto">
          <table class="min-w-full">
            <thead class="border-b border-gray-200 dark:border-zinc-800">
              <tr>
                <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 dark:text-zinc-400 uppercase tracking-wider">
                  <div class="flex items-center space-x-1">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    <span>Usuario</span>
                  </div>
                </th>
                <th class="px-3 py-2 text-left text-xs font-bold text-gray-700 dark:text-zinc-400 uppercase tracking-wider">
                  <span>Estado</span>
                </th>
                <th class="px-3 py-2 text-left text-xs font-bold text-gray-700 dark:text-zinc-400 uppercase tracking-wider">
                  <span>Apertura</span>
                </th>
                <th class="px-3 py-2 text-left text-xs font-bold text-gray-700 dark:text-zinc-400 uppercase tracking-wider">
                  <span>Cierre</span>
                </th>
                <th class="px-3 py-2 text-left text-xs font-bold text-gray-700 dark:text-zinc-400 uppercase tracking-wider">
                  <span>Inicial</span>
                </th>
                <th class="px-3 py-2 text-left text-xs font-bold text-gray-700 dark:text-zinc-400 uppercase tracking-wider">
                  <span>Ventas</span>
                </th>
                <th class="px-3 py-2 text-left text-xs font-bold text-gray-700 dark:text-zinc-400 uppercase tracking-wider">
                  <span>Estado Cierre</span>
                </th>
                <th class="px-3 py-2 text-left text-xs font-bold text-gray-700 dark:text-zinc-400 uppercase tracking-wider">
                  <span>Duración</span>
                </th>
                <th class="px-3 py-2 text-left text-xs font-bold text-gray-700 dark:text-zinc-400 uppercase tracking-wider">
                  <span>Acciones</span>
                </th>
              </tr>
            </thead>
            <tbody class="bg-transparent">
              <tr v-for="session in paginatedSessions" :key="session.id" 
                  class="hover:bg-gray-50 dark:hover:bg-[#2d2d38]/50 transition-all duration-200 border-b border-gray-200 dark:border-zinc-800">
                <td class="px-4 py-3 whitespace-nowrap">
                  <div class="flex items-center">
                    <div class="relative">
                      <div class="h-8 w-8 rounded-lg bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center text-white font-bold text-sm shadow-md">
                        {{ getUserInitials(session.user?.name || 'Usuario') }}
                      </div>
                      <div v-if="session.status === 'open'" class="absolute -top-0.5 -right-0.5 w-3 h-3 bg-emerald-500 rounded-full border-2 border-white dark:border-[#1e1e24]"></div>
                    </div>
                    <div class="ml-2">
                      <div class="text-sm font-bold text-gray-900 dark:text-white">
                        {{ session.user?.name || 'Usuario desconocido' }}
                      </div>
                      <div class="text-xs text-gray-500 dark:text-zinc-400">
                        ID: #{{ session.id }}
                      </div>
                    </div>
                  </div>
                </td>
                <td class="px-3 py-3 whitespace-nowrap">
                  <span :class="getStatusBadgeClass(session.status)" class="inline-flex items-center px-2 py-1 text-xs font-bold rounded-full">
                    <div :class="[
                      'w-1.5 h-1.5 rounded-full mr-1',
                      session.status === 'open' ? 'bg-emerald-400' : 'bg-gray-400'
                    ]"></div>
                    {{ session.status === 'open' ? 'ACTIVA' : 'CERRADA' }}
                  </span>
                </td>
                <td class="px-3 py-3 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                  <div class="font-semibold">{{ formatDate(session.opened_at) }}</div>
                  <div class="text-gray-500 dark:text-zinc-400 text-xs">{{ formatTime(session.opened_at) }}</div>
                </td>
                <td class="px-3 py-3 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                  <div v-if="session.status === 'closed' && session.closed_at" class="font-semibold">{{ formatDate(session.closed_at) }}</div>
                  <div v-if="session.status === 'closed' && session.closed_at" class="text-gray-500 dark:text-zinc-400 text-xs">{{ formatTime(session.closed_at) }}</div>
                  <span v-if="session.status === 'open'" class="text-amber-600 dark:text-amber-400 text-xs font-medium">En curso...</span>
                </td>
                <td class="px-3 py-3 whitespace-nowrap text-sm font-bold text-gray-900 dark:text-white">
                  ${{ parseFloat(session.opening_amount || 0).toLocaleString() }}
                </td>
                <td class="px-3 py-3 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                  <span class="font-semibold">
                    ${{ parseFloat(session.total_sales || 0).toLocaleString() }}
                  </span>
                </td>
                <td class="px-3 py-3 whitespace-nowrap text-sm">
                  <span v-if="session.status === 'closed'" :class="getClosingStatusClass(session.closing_status)" 
                        class="inline-flex items-center px-2 py-1 text-xs font-bold rounded-full">
                    {{ getClosingStatusText(session.closing_status) }}
                  </span>
                  <span v-else class="text-gray-400 dark:text-zinc-500 text-xs">En curso</span>
                </td>
                <td class="px-3 py-3 whitespace-nowrap text-sm text-gray-500 dark:text-zinc-400">
                  {{ getSessionDuration(session) }}
                </td>
                <td class="px-3 py-3 whitespace-nowrap text-sm font-medium">
                  <div class="flex items-center space-x-1.5">
                    <button
                      @click="viewSessionDetails(session)"
                      class="p-2 text-slate-400 dark:text-zinc-500 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-xl border border-transparent hover:border-blue-100 dark:hover:border-blue-900/30 transition-all duration-200"
                      title="Ver detalles"
                    >
                      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                      </svg>
                    </button>
                    <button
                      @click="viewSessionAudit(session)"
                      class="p-2 text-slate-400 dark:text-zinc-500 hover:text-purple-600 dark:hover:text-purple-400 hover:bg-purple-50 dark:hover:bg-purple-900/20 rounded-xl border border-transparent hover:border-purple-100 dark:hover:border-purple-900/30 transition-all duration-200"
                      title="Ver auditoría completa"
                    >
                      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                      </svg>
                    </button>
                    <button
                      v-if="session.status === 'open'"
                      @click="showCloseModal(session)"
                      class="p-2 text-slate-400 dark:text-zinc-500 hover:text-rose-600 dark:hover:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-900/20 rounded-xl border border-transparent hover:border-rose-100 dark:hover:border-rose-900/30 transition-all duration-200"
                      title="Cerrar sesión"
                    >
                      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                      </svg>
                    </button>
                    <button
                      @click="generateReport(session)"
                      class="p-2 text-slate-400 dark:text-zinc-500 hover:text-emerald-600 dark:hover:text-emerald-400 hover:bg-emerald-50 dark:hover:bg-emerald-900/20 rounded-xl border border-transparent hover:border-emerald-100 dark:hover:border-emerald-900/30 transition-all duration-200"
                      title="Generar reporte"
                    >
                      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                      </svg>
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Pagination -->
        <div v-if="totalPages > 1" class="border-t border-gray-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 px-4 py-3 flex items-center justify-between sm:px-6">
          <div class="flex-1 flex justify-between sm:hidden">
            <button
              @click="prevPage"
              :disabled="currentPage === 1"
              class="relative inline-flex items-center px-4 py-2 border border-gray-300 dark:border-zinc-800 text-sm font-medium rounded-md text-gray-700 dark:text-zinc-300 bg-white dark:bg-[#252530] hover:bg-gray-50 dark:hover:bg-[#35354a] disabled:opacity-50 disabled:cursor-not-allowed"
            >
              Anterior
            </button>
            <button
              @click="nextPage"
              :disabled="currentPage === totalPages"
              class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 dark:border-zinc-800 text-sm font-medium rounded-md text-gray-700 dark:text-zinc-300 bg-white dark:bg-[#252530] hover:bg-gray-50 dark:hover:bg-[#2a2a35] disabled:opacity-50 disabled:cursor-not-allowed"
            >
              Siguiente
            </button>
          </div>
          <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
            <div class="flex items-center space-x-2">
              <p class="text-sm text-gray-700 dark:text-zinc-400">
                Mostrando
                <span class="font-medium dark:text-zinc-200">{{ paginationInfo.start }}</span>
                a
                <span class="font-medium dark:text-zinc-200">{{ paginationInfo.end }}</span>
                de
                <span class="font-medium dark:text-zinc-200">{{ paginationInfo.total }}</span>
                resultados
              </p>
              <select
                v-model="itemsPerPage"
                @change="changeItemsPerPage(itemsPerPage)"
                class="ml-4 px-3 py-1 border border-gray-300 dark:border-zinc-800 bg-white dark:bg-[#252530] text-gray-700 dark:text-zinc-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400"
              >
                <option v-for="option in itemsPerPageOptions" :key="option" :value="option">
                  {{ option }} por página
                </option>
              </select>
            </div>
            <div>
              <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                <!-- Previous button -->
                <button
                  @click="prevPage"
                  :disabled="currentPage === 1"
                  class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 dark:border-zinc-800 bg-white dark:bg-[#252530] text-sm font-medium text-gray-500 dark:text-zinc-400 hover:bg-gray-50 dark:hover:bg-[#2a2a35] disabled:opacity-50 disabled:cursor-not-allowed"
                >
                  <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                  </svg>
                </button>

                <!-- Page numbers -->
                <template v-for="page in Math.min(totalPages, 7)" :key="page">
                  <button
                    v-if="page === 1 || page === totalPages || Math.abs(page - currentPage) <= 2"
                    @click="changePage(page)"
                    :class="[
                      page === currentPage
                        ? 'z-10 bg-blue-50 dark:bg-blue-950 border-blue-500 dark:border-blue-700 text-blue-600 dark:text-blue-400'
                        : 'bg-white dark:bg-[#252530] border-gray-300 dark:border-zinc-800 text-gray-500 dark:text-zinc-400 hover:bg-gray-50 dark:hover:bg-[#2a2a35]',
                      'relative inline-flex items-center px-4 py-2 border text-sm font-medium'
                    ]"
                  >
                    {{ page }}
                  </button>
                  <span
                    v-else-if="(page === 2 && currentPage > 4) || (page === totalPages - 1 && currentPage < totalPages - 3)"
                    class="relative inline-flex items-center px-4 py-2 border border-gray-300 dark:border-zinc-800 bg-white dark:bg-[#252530] text-sm font-medium text-gray-700 dark:text-zinc-400"
                  >
                    ...
                  </span>
                </template>

                <!-- Next button -->
                <button
                  @click="nextPage"
                  :disabled="currentPage === totalPages"
                  class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 dark:border-zinc-800 bg-white dark:bg-[#252530] text-sm font-medium text-gray-500 dark:text-zinc-400 hover:bg-gray-50 dark:hover:bg-[#2a2a35] disabled:opacity-50 disabled:cursor-not-allowed"
                >
                  <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                  </svg>
                </button>
              </nav>
            </div>
          </div>
        </div>

        <!-- Empty State -->
        <div v-if="filteredSessions.length === 0" class="text-center py-12">
          <div class="w-16 h-16 mx-auto bg-gray-100 dark:bg-zinc-800/50 rounded-full flex items-center justify-center mb-4">
            <svg class="w-8 h-8 text-gray-300 dark:text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
            </svg>
          </div>
          <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">No hay sesiones</h3>
          <p class="text-gray-500 dark:text-zinc-400 mb-4">Ajusta los filtros o crea una nueva sesión</p>
          <button
            @click="clearFilters"
            class="px-4 py-2 bg-slate-900 dark:bg-slate-700 text-white rounded-lg hover:bg-black dark:hover:bg-slate-600 transition-all duration-300 text-sm font-medium"
          >
            Limpiar filtros
          </button>
        </div>
      </div>
    </div>

    <!-- Session Details Modal Empresarial -->
    <div v-if="selectedSession" class="fixed inset-0 bg-black/75 dark:bg-black/85 backdrop-blur-sm flex items-center justify-center z-50 p-4" @click.self="selectedSession = null">
      <div class="bg-white dark:bg-zinc-900 rounded-xl max-w-3xl w-full max-h-[90vh] overflow-y-auto shadow-2xl dark:shadow-black/40 border border-gray-300 dark:border-zinc-800">
        <!-- Header Empresarial -->
        <div class="bg-white dark:bg-zinc-900 border-b border-gray-200 dark:border-zinc-800 px-6 py-4 rounded-t-xl">
          <div class="flex justify-between items-center">
            <div class="flex items-center space-x-3">
              <div class="w-10 h-10 bg-blue-100 dark:bg-blue-950 rounded-lg flex items-center justify-center">
                <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
              </div>
              <div>
                <h3 class="text-lg font-bold text-gray-900 dark:text-white">Detalles de Sesión</h3>
                <p class="text-sm text-gray-600 dark:text-zinc-400">ID: #{{ selectedSession.id }} - {{ selectedSession.user?.name }}</p>
              </div>
            </div>
            <button 
              @click="selectedSession = null" 
              class="text-gray-400 dark:text-zinc-500 hover:text-gray-600 dark:hover:text-zinc-300 transition-colors"
            >
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
              </svg>
            </button>
          </div>
        </div>
        
        <div class="p-6">
          <!-- User Info -->
          <div class="bg-gray-50 dark:bg-[#252530] rounded-lg p-4 mb-6 border border-gray-300 dark:border-zinc-800/40">
            <div class="flex items-center space-x-4">
              <div class="relative">
                <div class="w-14 h-14 bg-blue-100 dark:bg-blue-950 rounded-lg flex items-center justify-center text-blue-600 dark:text-blue-400 font-bold text-lg">
                  {{ getUserInitials(selectedSession.user?.name || 'Usuario') }}
                </div>
                <div v-if="selectedSession.status === 'open'" class="absolute -top-1 -right-1 w-4 h-4 bg-green-500 rounded-full border-2 border-white dark:border-[#252530]"></div>
              </div>
              <div class="flex-1">
                <h4 class="text-lg font-bold text-gray-900 dark:text-white">{{ selectedSession.user?.name }}</h4>
                <p class="text-sm text-gray-600 dark:text-zinc-400">{{ selectedSession.user?.email }}</p>
                <div class="flex items-center mt-1 space-x-3">
                  <span :class="getStatusBadgeClass(selectedSession.status)" class="px-2 py-1 text-xs font-semibold rounded-full">
                    {{ selectedSession.status === 'open' ? 'ACTIVA' : 'CERRADA' }}
                  </span>
                  <span class="text-xs text-gray-500 dark:text-zinc-500">
                    CC: {{ selectedSession.user?.cc }}
                  </span>
                </div>
              </div>
            </div>
          </div>

          <!-- Session Info Grid -->
          <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-6">
            <div class="bg-gray-50 dark:bg-[#252530] p-3 rounded-xl border border-gray-300 dark:border-zinc-800/40">
              <label class="block text-xs font-medium text-gray-600 dark:text-zinc-400 mb-1">Fecha Apertura</label>
              <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ formatDate(selectedSession.opening_date) }}</p>
            </div>
            <div class="bg-gray-50 dark:bg-[#252530] p-3 rounded-xl border border-gray-300 dark:border-zinc-800/40">
              <label class="block text-xs font-medium text-gray-600 dark:text-zinc-400 mb-1">Hora Apertura</label>
              <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ selectedSession.opening_time }}</p>
            </div>
            <div class="bg-gray-50 dark:bg-[#252530] p-3 rounded-xl border border-gray-300 dark:border-zinc-800/40">
              <label class="block text-xs font-medium text-gray-600 dark:text-zinc-400 mb-1">Duración</label>
              <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ getSessionDuration(selectedSession) }}</p>
            </div>
            <div class="bg-gray-50 dark:bg-[#252530] p-3 rounded-xl border border-gray-300 dark:border-zinc-800/40">
              <label class="block text-xs font-medium text-gray-600 dark:text-zinc-400 mb-1">Actualización</label>
              <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ formatDate(selectedSession.updated_at) }}</p>
            </div>
          </div>

          <!-- Financial Summary -->
          <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <div class="bg-emerald-50 dark:bg-emerald-950 p-4 rounded-xl border border-emerald-200 dark:border-emerald-800">
              <div class="flex items-center justify-between">
                <div>
                  <p class="text-xs font-medium text-emerald-700 dark:text-emerald-400">Monto Inicial</p>
                  <p class="text-xl font-bold text-emerald-900 dark:text-emerald-300">${{ parseFloat(selectedSession.opening_amount || 0).toLocaleString() }}</p>
                </div>
                <div class="w-10 h-10 bg-emerald-100 dark:bg-emerald-950 rounded-lg flex items-center justify-center">
                  <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                  </svg>
                </div>
              </div>
            </div>
            
            <div class="bg-blue-50 dark:bg-blue-950 p-4 rounded-xl border border-blue-200 dark:border-blue-800">
              <div class="flex items-center justify-between">
                <div>
                  <p class="text-xs font-medium text-blue-700 dark:text-blue-400">Total Ventas</p>
                  <p class="text-xl font-bold text-blue-900 dark:text-blue-300">${{ parseFloat(selectedSession.total_sales || 0).toLocaleString() }}</p>
                </div>
                <div class="w-10 h-10 bg-blue-100 dark:bg-blue-950 rounded-lg flex items-center justify-center">
                  <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                  </svg>
                </div>
              </div>
            </div>
            
            <div class="bg-purple-50 dark:bg-purple-950 p-4 rounded-xl border border-purple-200 dark:border-purple-800">
              <div class="flex items-center justify-between">
                <div>
                  <p class="text-xs font-medium text-purple-700 dark:text-purple-400">Efectivo</p>
                  <p class="text-xl font-bold text-purple-900 dark:text-purple-300">${{ parseFloat(selectedSession.cash_sales || 0).toLocaleString() }}</p>
                </div>
                <div class="w-10 h-10 bg-purple-100 dark:bg-purple-950 rounded-lg flex items-center justify-center">
                  <svg class="w-5 h-5 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v2a2 2 0 002 2z"/>
                  </svg>
                </div>
              </div>
            </div>
          </div>

          <!-- Notes -->
          <div v-if="selectedSession.opening_notes" class="mb-6">
            <label class="block text-sm font-semibold text-gray-700 dark:text-zinc-300 mb-2">Notas de Apertura</label>
            <div class="bg-amber-50 dark:bg-amber-950 border border-amber-200 dark:border-amber-800 rounded-lg p-3">
              <p class="text-sm text-gray-800 dark:text-amber-300">{{ selectedSession.opening_notes }}</p>
            </div>
          </div>

          <!-- Actions -->
          <div class="flex justify-end space-x-3 pt-4 border-t border-gray-200 dark:border-zinc-800">
            <button
              @click="selectedSession = null"
              class="px-4 py-2 border border-gray-300 dark:border-zinc-800 text-gray-700 dark:text-zinc-300 bg-white dark:bg-[#252530] rounded-lg hover:bg-gray-50 dark:hover:bg-[#2a2a35] transition-colors text-sm font-medium"
            >
              Cerrar
            </button>
            <button
              v-if="selectedSession.status === 'open'"
              @click="showCloseModal(selectedSession)"
              class="px-4 py-2 bg-rose-600 dark:bg-rose-700 text-white rounded-lg hover:bg-rose-700 dark:hover:bg-rose-600 transition-colors text-sm font-medium"
            >
              Cerrar Sesión
            </button>
            <button
              @click="generateReport(selectedSession)"
              class="px-4 py-2 bg-emerald-600 dark:bg-emerald-700 text-white rounded-lg hover:bg-emerald-700 dark:hover:bg-emerald-600 transition-colors text-sm font-medium"
            >
              Generar Reporte
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal de Cierre Empresarial -->
    <div v-if="showCloseSessionModal" class="fixed inset-0 bg-black/75 dark:bg-black/85 backdrop-blur-sm flex items-center justify-center z-[100] p-4" @click.self="showCloseSessionModal = false">
      <div class="bg-white dark:bg-zinc-900 rounded-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto shadow-xl dark:shadow-black/50 border border-gray-300 dark:border-zinc-800">
        <!-- Header -->
        <div class="bg-white dark:bg-zinc-900 border-b border-gray-200 dark:border-zinc-800 px-6 py-4 rounded-t-2xl">
          <div class="flex items-center justify-between">
            <div class="flex items-center space-x-3">
              <div class="w-10 h-10 bg-rose-50 dark:bg-rose-950 rounded-lg flex items-center justify-center">
                <svg class="w-5 h-5 text-rose-600 dark:text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                </svg>
              </div>
              <div>
                <h2 class="text-lg font-bold text-gray-900 dark:text-white">Cerrar Caja</h2>
                <p class="text-sm text-gray-600 dark:text-zinc-400">{{ sessionToClose?.user?.name }} - #{{ sessionToClose?.id }}</p>
              </div>
            </div>
            <button @click="showCloseSessionModal = false" class="text-gray-400 dark:text-zinc-500 hover:text-gray-600 dark:hover:text-zinc-300 transition-colors">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
              </svg>
            </button>
          </div>
        </div>

        <div class="p-6">
          <!-- Resumen de la sesión -->
          <div class="grid grid-cols-2 gap-3 mb-4">
            <div class="bg-emerald-50 dark:bg-emerald-950 p-3 rounded-xl border border-emerald-100 dark:border-emerald-800">
              <label class="text-xs font-medium text-emerald-700 dark:text-emerald-400">Monto Inicial</label>
              <p class="text-xl font-bold text-emerald-900 dark:text-emerald-300">${{ parseFloat(sessionToClose?.opening_amount || 0).toLocaleString() }}</p>
            </div>
            <div class="bg-blue-50 dark:bg-blue-950 p-3 rounded-xl border border-blue-100 dark:border-blue-800">
              <label class="text-xs font-medium text-blue-700 dark:text-blue-400">Ventas Totales</label>
              <p class="text-xl font-bold text-blue-900 dark:text-blue-300">${{ parseFloat(sessionToClose?.total_sales || 0).toLocaleString() }}</p>
            </div>
          </div>

          <!-- Desglose por método de pago -->
          <div class="grid grid-cols-3 gap-3 mb-4">
            <div class="bg-amber-50 dark:bg-amber-950 p-3 rounded-xl border border-amber-100 dark:border-amber-800">
              <label class="text-xs font-medium text-amber-700 dark:text-amber-400">Efectivo</label>
              <p class="text-base font-bold text-amber-900 dark:text-amber-300">${{ parseFloat(sessionToClose?.cash_sales || 0).toLocaleString() }}</p>
            </div>
            <div class="bg-purple-50 dark:bg-purple-950 p-3 rounded-xl border border-purple-100 dark:border-purple-800">
              <label class="text-xs font-medium text-purple-700 dark:text-purple-400">Tarjetas</label>
              <p class="text-base font-bold text-purple-900 dark:text-purple-300">${{ parseFloat(sessionToClose?.card_sales || 0).toLocaleString() }}</p>
            </div>
            <div class="bg-indigo-50 dark:bg-indigo-950 p-3 rounded-xl border border-indigo-100 dark:border-indigo-800">
              <label class="text-xs font-medium text-indigo-700 dark:text-indigo-400">Transferencias</label>
              <p class="text-base font-bold text-indigo-900 dark:text-indigo-300">${{ parseFloat(sessionToClose?.transfer_sales || 0).toLocaleString() }}</p>
            </div>
          </div>

          <!-- Formulario de cierre -->
          <div class="space-y-4">
            <div>
              <label class="block text-sm font-bold text-gray-700 dark:text-zinc-300 mb-2">Monto Real en Caja ($) *</label>
              <input
                v-model="closeForm.actual_amount"
                type="text"
                inputmode="decimal"
                class="w-full px-4 py-3 border-2 border-gray-300 dark:border-zinc-800 rounded-xl bg-white dark:bg-[#2a2a35] text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-zinc-500 focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent text-lg font-semibold"
                placeholder="0.00"
                @input="closeForm.actual_amount = closeForm.actual_amount.replace(/[^0-9.]/g, '')"
              >
            </div>

            <div>
              <label class="block text-sm font-bold text-gray-700 dark:text-zinc-300 mb-2">Notas de Cierre</label>
              <textarea
                v-model="closeForm.closing_notes"
                rows="3"
                class="w-full px-4 py-3 border-2 border-gray-300 dark:border-zinc-800 rounded-xl bg-white dark:bg-[#2a2a35] text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-zinc-500 focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent"
                placeholder="Observaciones sobre el cierre..."
              ></textarea>
            </div>

            <div>
              <label class="block text-sm font-bold text-gray-700 dark:text-zinc-300 mb-2">Gastos y Salidas (Opcional)</label>
              <textarea
                v-model="closeForm.expenses_detail"
                rows="2"
                class="w-full px-4 py-3 border-2 border-gray-300 dark:border-zinc-800 rounded-xl bg-white dark:bg-[#2a2a35] text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-zinc-500 focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent"
                placeholder="Detallar gastos realizados durante la sesión..."
              ></textarea>
            </div>
          </div>

          <!-- Cálculo de diferencia -->
          <div v-if="closeForm.actual_amount" class="mt-4 p-3 rounded-lg" 
               :class="getDifferenceClass()">
            <div class="text-center">
              <p class="text-xs font-medium opacity-80">Diferencia</p>
              <p class="text-xl font-bold">
                ${{ Math.abs(getDifference()).toLocaleString() }}
                <span class="text-sm">{{ getDifferenceText() }}</span>
              </p>
            </div>
          </div>

          <!-- Acciones -->
          <div class="flex space-x-3 mt-6 pt-4 border-t border-gray-200 dark:border-zinc-800">
            <button
              @click="showCloseSessionModal = false"
              class="flex-1 px-4 py-2.5 border border-gray-300 dark:border-zinc-800 text-gray-700 dark:text-zinc-300 bg-white dark:bg-[#252530] rounded-lg hover:bg-gray-50 dark:hover:bg-[#2a2a35] font-medium text-sm transition-colors"
            >
              Cancelar
            </button>
            <button
              @click="confirmCloseSession"
              :disabled="!closeForm.actual_amount || closingSession"
              class="flex-1 px-4 py-2.5 bg-rose-600 dark:bg-rose-700 text-white rounded-lg hover:bg-rose-700 dark:hover:bg-rose-600 disabled:opacity-50 disabled:cursor-not-allowed font-medium text-sm transition-colors"
            >
              {{ closingSession ? 'Cerrando...' : 'Cerrar Caja' }}
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal de Auditoría Empresarial -->
    <div v-if="showAuditModal" class="fixed inset-0 bg-black/75 dark:bg-black/85 backdrop-blur-sm flex items-center justify-center z-[100] p-4" @click.self="showAuditModal = false">
      <div class="bg-white dark:bg-zinc-900 rounded-xl w-full max-w-6xl max-h-[90vh] overflow-y-auto shadow-xl dark:shadow-black/50 border border-gray-300 dark:border-zinc-800">
        <!-- Header -->
        <div class="bg-white dark:bg-zinc-900 border-b border-gray-200 dark:border-zinc-800 px-6 py-4 rounded-t-xl">
          <div class="flex items-center justify-between">
            <div class="flex items-center space-x-3">
              <div class="w-10 h-10 bg-indigo-100 dark:bg-indigo-950 rounded-lg flex items-center justify-center">
                <svg class="w-5 h-5 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                </svg>
              </div>
              <div>
                <h2 class="text-lg font-bold text-gray-900 dark:text-white">Auditoría de Sesión</h2>
                <p class="text-sm text-gray-600 dark:text-zinc-400">{{ auditData?.session?.user?.name }} - #{{ auditData?.session?.id }}</p>
              </div>
            </div>
            <button @click="showAuditModal = false" class="text-gray-400 dark:text-zinc-500 hover:text-gray-600 dark:hover:text-zinc-300 transition-colors">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
              </svg>
            </button>
          </div>
        </div>

        <div class="p-6">
          <!-- Estadísticas -->
          <div v-if="auditData?.statistics" class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-6">
            <div class="bg-blue-50 dark:bg-blue-950 p-3 rounded-xl border border-blue-200 dark:border-blue-800 text-center">
              <p class="text-xl font-bold text-blue-900 dark:text-blue-300">{{ auditData.statistics.total_transactions }}</p>
              <p class="text-xs font-medium text-blue-700 dark:text-blue-400">Transacciones</p>
            </div>
            <div class="bg-emerald-50 dark:bg-emerald-950 p-3 rounded-xl border border-emerald-200 dark:border-emerald-800 text-center">
              <p class="text-xl font-bold text-emerald-900 dark:text-emerald-300">${{ parseFloat(auditData.statistics.average_sale || 0).toLocaleString() }}</p>
              <p class="text-xs font-medium text-emerald-700 dark:text-emerald-400">Venta Promedio</p>
            </div>
            <div class="bg-purple-50 dark:bg-purple-950 p-3 rounded-xl border border-purple-200 dark:border-purple-800 text-center">
              <p class="text-xl font-bold text-purple-900 dark:text-purple-300">${{ parseFloat(auditData.statistics.largest_sale || 0).toLocaleString() }}</p>
              <p class="text-xs font-medium text-purple-700 dark:text-purple-400">Venta Mayor</p>
            </div>
            <div class="bg-orange-50 dark:bg-orange-950 p-3 rounded-xl border border-orange-200 dark:border-orange-800 text-center">
              <p class="text-xl font-bold text-orange-900 dark:text-orange-300">{{ Math.floor(auditData.statistics.session_duration / 60) }}h</p>
              <p class="text-xs font-medium text-orange-700 dark:text-orange-400">Duración</p>
            </div>
          </div>

          <!-- Timeline -->
          <div v-if="auditData?.timeline" class="mb-6">
            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Timeline de Eventos</h3>
            <div class="space-y-3 max-h-96 overflow-y-auto">
              <div v-for="(event, index) in auditData.timeline" :key="index" 
                   class="flex items-start space-x-3 p-3 bg-gray-50 dark:bg-[#252530] rounded-xl border border-gray-300 dark:border-zinc-800/40">
                <div :class="getEventIconClass(event.type)" class="p-2 rounded-full">
                  <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path v-if="event.type === 'opening'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    <path v-else-if="event.type === 'sale'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                    <path v-else-if="event.type === 'return'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/>
                    <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                  </svg>
                </div>
                <div class="flex-1">
                  <div class="flex items-center justify-between">
                    <p class="font-semibold text-gray-900 dark:text-white">{{ event.description }}</p>
                    <span class="text-sm text-gray-500 dark:text-zinc-500">{{ formatTimestamp(event.timestamp) }}</span>
                  </div>
                  <p class="text-lg font-bold" :class="event.amount < 0 ? 'text-orange-600 dark:text-orange-400' : 'text-green-600 dark:text-green-400'">
                    ${{ parseFloat(event.amount || 0).toLocaleString() }}
                  </p>
                  <div v-if="event.details.customer" class="text-sm text-gray-600 dark:text-zinc-400">
                    Cliente: {{ event.details.customer }}
                  </div>
                  <div v-if="event.type === 'return' && event.details.original_invoice" class="text-sm text-gray-600 dark:text-zinc-400">
                    Factura original: {{ event.details.original_invoice }}
                  </div>
                  <div v-if="event.type === 'return' && event.details.reason" class="text-sm text-gray-500 dark:text-zinc-500 italic">
                    Razón: {{ event.details.reason }}
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Botón cerrar -->
          <div class="flex justify-end pt-4 border-t border-gray-200 dark:border-zinc-800">
            <button
              @click="showAuditModal = false"
              class="px-4 py-2 bg-slate-900 dark:bg-slate-700 text-white rounded-lg hover:bg-black dark:hover:bg-slate-600 font-medium text-sm transition-all duration-300"
            >
              Cerrar
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { apiCall } from '../services/api.js'
import { useToast } from '../composables/useToast.js'

// Toast system
const { showSuccess, showError, showInfo, showWarning } = useToast()

// Data
const sessions = ref([])
const loading = ref(false)
const selectedSession = ref(null)

// Pagination
const currentPage = ref(1)
const itemsPerPage = ref(10)
const itemsPerPageOptions = [5, 10, 25, 50]
const showNewSessionModal = ref(false)

// Nuevos estados para modales
const showCloseSessionModal = ref(false)
const showAuditModal = ref(false)
const sessionToClose = ref(null)
const closingSession = ref(false)
const auditData = ref(null)

// Formulario de cierre
const closeForm = ref({
  actual_amount: '',
  closing_notes: '',
  expenses_detail: ''
})

// Filters
const searchQuery = ref('')
const statusFilter = ref('open') // Filtro por defecto: sesiones activas
const dateFilter = ref('')

// Computed
const activeSessions = computed(() => sessions.value.filter(s => s.status === 'open'))
const uniqueUsers = computed(() => {
  const userIds = new Set(activeSessions.value.map(s => s.user_id))
  return Array.from(userIds)
})

const totalCashAmount = computed(() => {
  return activeSessions.value.reduce((total, session) => {
    return total + parseFloat(session.opening_amount || 0) + parseFloat(session.total_sales || 0) - parseFloat(session.total_expenses || 0)
  }, 0)
})

const totalSalesToday = computed(() => {
  const today = new Date().toISOString().split('T')[0]
  return sessions.value
    .filter(s => s.opening_date?.startsWith(today))
    .reduce((total, session) => total + parseFloat(session.total_sales || 0), 0)
})

const filteredSessions = computed(() => {
  let filtered = sessions.value

  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase()
    filtered = filtered.filter(session => 
      session.user?.name?.toLowerCase().includes(query) ||
      session.user?.email?.toLowerCase().includes(query)
    )
  }

  if (statusFilter.value) {
    filtered = filtered.filter(session => session.status === statusFilter.value)
  }

  if (dateFilter.value) {
    filtered = filtered.filter(session => 
      session.opening_date?.startsWith(dateFilter.value)
    )
  }

  return filtered.sort((a, b) => new Date(b.created_at) - new Date(a.created_at))
})

const paginatedSessions = computed(() => {
  const startIndex = (currentPage.value - 1) * itemsPerPage.value
  const endIndex = startIndex + itemsPerPage.value
  return filteredSessions.value.slice(startIndex, endIndex)
})

const totalPages = computed(() => {
  return Math.ceil(filteredSessions.value.length / itemsPerPage.value)
})

const paginationInfo = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage.value + 1
  const end = Math.min(currentPage.value * itemsPerPage.value, filteredSessions.value.length)
  return {
    start,
    end,
    total: filteredSessions.value.length
  }
})

// Methods
const refreshSessions = async () => {
  loading.value = true
  try {
    const response = await apiCall('/cash-sessions')
    
    if (response.success) {
      sessions.value = response.sessions || []
    } else {
      throw new Error(response.message || 'Error en la respuesta del servidor')
    }
  } catch (error) {
    showError('❌ No se pudieron cargar las sesiones')
  } finally {
    loading.value = false
  }
}

const clearFilters = () => {
  searchQuery.value = ''
  statusFilter.value = ''
  dateFilter.value = ''
  currentPage.value = 1
}

const exportData = () => {
  // TODO: Implementar exportación real
  alert('Función de exportación en desarrollo')
}

// Pagination methods
const changePage = (page) => {
  if (page >= 1 && page <= totalPages.value) {
    currentPage.value = page
  }
}

const changeItemsPerPage = (newValue) => {
  itemsPerPage.value = newValue
  currentPage.value = 1 // Reset to first page
}

const nextPage = () => {
  if (currentPage.value < totalPages.value) {
    currentPage.value++
  }
}

const prevPage = () => {
  if (currentPage.value > 1) {
    currentPage.value--
  }
}

const getUserInitials = (name) => {
  return name.split(' ').map(n => n[0]).join('').toUpperCase().slice(0, 2)
}

const getStatusBadgeClass = (status) => {
  return status === 'open' 
    ? 'bg-emerald-50 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800' 
    : 'bg-gray-50 dark:bg-zinc-800 text-gray-700 dark:text-zinc-300 border border-gray-300 dark:border-zinc-800'
}

const formatDate = (dateString) => {
  if (!dateString) return 'N/A'
  return new Date(dateString).toLocaleDateString('es-ES', {
    year: 'numeric',
    month: 'short',
    day: 'numeric'
  })
}

const formatTime = (dateString) => {
  if (!dateString) return 'N/A'
  return new Date(dateString).toLocaleTimeString('es-ES', {
    hour: '2-digit',
    minute: '2-digit',
    hour12: true
  })
}

const getSessionDuration = (session) => {
  if (!session) return 'N/A'
  
  try {
    // Intentar crear fecha desde created_at primero, luego opening_date
    let openingDateTime
    
    if (session.created_at) {
      openingDateTime = new Date(session.created_at)
    } else if (session.opening_date && session.opening_time) {
      openingDateTime = new Date(`${session.opening_date}T${session.opening_time}`)
    } else if (session.opening_date) {
      openingDateTime = new Date(session.opening_date)
    } else {
      return 'N/A'
    }
    
    // Validar que la fecha es válida
    if (isNaN(openingDateTime.getTime())) {
      return 'N/A'
    }
    
    const now = new Date()
    const diff = now - openingDateTime
    
    // Si la diferencia es negativa o muy grande (más de 1 año), retornar N/A
    if (diff < 0 || diff > (365 * 24 * 60 * 60 * 1000)) {
      return 'N/A'
    }
    
    const hours = Math.floor(diff / (1000 * 60 * 60))
    const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60))
    
    return `${hours}h ${minutes}m`
  } catch (error) {
    console.error('Error calculating session duration:', error)
    return 'N/A'
  }
}

const viewSessionDetails = (session) => {
  selectedSession.value = session
  showInfo(`👁️ Detalles cargados - Mostrando información completa de la sesión #${session.id}`)
}

// Métodos para cálculos de cierre
const getDifference = () => {
  if (!closeForm.value.actual_amount || !sessionToClose.value) return 0
  const expected = parseFloat(sessionToClose.value.opening_amount || 0) + parseFloat(sessionToClose.value.cash_sales || 0)
  const actual = parseFloat(closeForm.value.actual_amount)
  return actual - expected
}

const getDifferenceClass = () => {
  const diff = getDifference()
  if (diff > 0) return 'bg-emerald-50 dark:bg-emerald-950 border border-emerald-200 dark:border-emerald-800 text-emerald-900 dark:text-emerald-300'
  if (diff < 0) return 'bg-rose-50 dark:bg-rose-950 border border-rose-200 dark:border-rose-800 text-rose-900 dark:text-rose-300'
  return 'bg-blue-50 dark:bg-blue-950 border border-blue-200 dark:border-blue-800 text-blue-900 dark:text-blue-300'
}

const getDifferenceText = () => {
  const diff = getDifference()
  if (diff > 0) return '(Sobrante)'
  if (diff < 0) return '(Faltante)'
  return '(Exacto)'
}

// Confirmar cierre de sesión
const confirmCloseSession = async () => {
  const actualAmount = parseFloat(closeForm.value.actual_amount)
  
  // Validar que sea un número válido y mayor a 0
  if (!closeForm.value.actual_amount || closeForm.value.actual_amount.trim() === '' || isNaN(actualAmount) || actualAmount <= 0) {
    showError('⚠️ Monto inválido - Por favor ingresa un monto mayor a $0.00')
    return
  }

  // Confirmación antes de cerrar
  const confirmed = confirm(`¿Estás seguro que deseas cerrar la caja de ${sessionToClose.value?.user?.name}?\n\nMonto: $${actualAmount.toLocaleString()}`)
  if (!confirmed) return

  closingSession.value = true
  
  try {
    const formData = {
      actual_amount: actualAmount,
      closing_notes: closeForm.value.closing_notes || '',
      expenses_detail: closeForm.value.expenses_detail || ''
    }
    
    const data = await apiCall(`/cash-sessions/${sessionToClose.value.id}/close`, {
      method: 'POST',
      body: JSON.stringify(formData)
    })
    
    if (data.success) {
      showCloseSessionModal.value = false
      sessionToClose.value = null
      refreshSessions()
    } else {
      throw new Error(data.message || 'Error al cerrar la sesión')
    }
  } catch (error) {
    showError('❌ No se pudo cerrar la sesión. Intenta nuevamente.')
  } finally {
    closingSession.value = false
  }
}

// Método para mostrar auditoría
const viewSessionAudit = async (session) => {
  try {
    const data = await apiCall(`/cash-sessions/${session.id}/audit`)
    
    if (data.success) {
      auditData.value = data
      showAuditModal.value = true
    } else {
      throw new Error(data.message || 'Error al cargar auditoría')
    }
  } catch (error) {
    showError('❌ No se pudo cargar la auditoría')
  }
}

// Métodos auxiliares para auditoría
const getEventIconClass = (type) => {
  const classes = {
    opening: 'bg-green-500',
    sale: 'bg-blue-500', 
    return: 'bg-orange-500',
    closing: 'bg-red-500'
  }
  return classes[type] || 'bg-gray-500'
}

const formatTimestamp = (timestamp) => {
  return new Date(timestamp).toLocaleString('es-ES', {
    hour: '2-digit',
    minute: '2-digit',
    day: '2-digit',
    month: '2-digit'
  })
}

// Métodos para estado de cierre
const getClosingStatusClass = (status) => {
  const classes = {
    exact: 'bg-emerald-50 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800',
    surplus: 'bg-blue-50 dark:bg-blue-950 text-blue-700 dark:text-blue-400 border border-blue-200 dark:border-blue-800',
    deficit: 'bg-rose-50 dark:bg-rose-950 text-rose-700 dark:text-rose-400 border border-rose-200 dark:border-rose-800',
    with_expenses: 'bg-amber-50 dark:bg-amber-950 text-amber-700 dark:text-amber-400 border border-amber-200 dark:border-amber-800'
  }
  return classes[status] || 'bg-gray-50 dark:bg-zinc-800 text-gray-700 dark:text-zinc-300 border border-gray-300 dark:border-zinc-800'
}

const getClosingStatusText = (status) => {
  const texts = {
    exact: '✅ Exacto',
    surplus: '📈 Sobrante', 
    deficit: '📉 Faltante',
    with_expenses: '💸 Con gastos'
  }
  return texts[status] || 'Desconocido'
}

const closeSession = async (session) => {
  // Open the professional close modal instead of making direct API call
  sessionToClose.value = session
  
  // Pre-fill form with calculated values (como strings)
  const expectedCash = parseFloat(session.opening_amount || 0) + parseFloat(session.cash_sales || 0)
  const finalAmount = expectedCash > 0 ? expectedCash : parseFloat(session.opening_amount || 0)
  closeForm.value = {
    actual_amount: finalAmount.toFixed(2), // String con 2 decimales
    closing_notes: '',
    expenses_detail: ''
  }
  
  showCloseSessionModal.value = true
}

// Nuevos métodos para modal de cierre mejorado
const showCloseModal = (session) => {
  sessionToClose.value = session
  // Pre-rellenar formulario con datos calculados (como strings para el backend)
  const expectedCash = parseFloat(session.opening_amount || 0) + parseFloat(session.cash_sales || 0)
  closeForm.value = {
    actual_amount: expectedCash.toFixed(2), // Convertir a string con 2 decimales
    closing_notes: '',
    expenses_detail: ''
  }
  showCloseSessionModal.value = true
}

const closeSessionWithDetails = async () => {
  if (!sessionToClose.value) return
  
  closingSession.value = true
  try {
    showInfo('🔄 Cerrando sesión... - Procesando cierre de caja con detalles')
    
    const response = await apiCall(`/cash-sessions/${sessionToClose.value.id}/close`, {
      method: 'POST',
      data: closeForm.value
    })
    
    if (response.success) {
      showSuccess('✅ Sesión cerrada - La sesión se cerró correctamente')
      showCloseSessionModal.value = false
      sessionToClose.value = null
      refreshSessions()
    } else {
      throw new Error(response.message || 'Error al cerrar la sesión')
    }
  } catch (error) {
    console.error('Error closing session:', error)
    showError('❌ Error al cerrar - No se pudo cerrar la sesión. Intenta nuevamente.')
  } finally {
    closingSession.value = false
  }
}

const generateReport = (session) => {
  showInfo(`📊 Generando reporte... - Creando reporte detallado para ${session.user?.name}`)
  
  // Simulate report generation
  setTimeout(() => {
    showSuccess('✅ Reporte generado - El reporte se ha creado exitosamente')
  }, 1500)
}

// Lifecycle
onMounted(() => {
  refreshSessions()
  showSuccess('🚀 Panel cargado - Sistema administrativo iniciado correctamente')
})
</script>

<style scoped>
/* 🎨 Animaciones Empresariales */
@keyframes fade-in {
  from {
    opacity: 0;
    transform: translateY(10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

@keyframes slide-in {
  from {
    opacity: 0;
    transform: translateX(-20px);
  }
  to {
    opacity: 1;
    transform: translateX(0);
  }
}

.animate-fade-in {
  animation: fade-in 0.6s ease-out;
}

.animate-slide-in {
  animation: slide-in 0.5s ease-out;
}

/* 💫 Efectos Hover Empresariales */
.hover-lift:hover {
  transform: translateY(-2px);
  box-shadow: 0 10px 25px -3px rgba(0, 0, 0, 0.1);
}

/* 📊 Scrollbar Empresarial */
::-webkit-scrollbar {
  width: 6px;
  height: 6px;
}

::-webkit-scrollbar-track {
  background: #f1f5f9;
  border-radius: 3px;
}

::-webkit-scrollbar-thumb {
  background: #cbd5e1;
  border-radius: 3px;
}

::-webkit-scrollbar-thumb:hover {
  background: #94a3b8;
}

/* 🎯 Estados de Focus Empresariales */
input:focus, select:focus, textarea:focus {
  outline: none;
  box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

/* ⚡ Transiciones Optimizadas */
button, .transition-all, .transition-colors {
  transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
}

/* 🎨 Badge Hover Empresarial */
.badge-hover:hover {
  transform: scale(1.05);
  transition: transform 0.2s ease;
}

/* 💼 Modal y Overlay Empresarial */
.modal-overlay {
  backdrop-filter: blur(8px);
  background: rgba(15, 23, 42, 0.6);
}

.modal-content {
  box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
}
</style>