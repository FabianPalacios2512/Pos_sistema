<template>
  <div class="bg-[#f8f9fa] dark:bg-[#131314] font-sans transition-colors duration-300 px-8" style="height: 100%; display: flex; flex-direction: column;">
    <div class="p-4 lg:p-6 space-y-6 pb-8 animate-fade-in" style="flex: 1; display: flex; flex-direction: column; min-height: 0;">
      
      <!-- 🎨 Header Gemini -->
      <div class="flex items-center justify-between pb-4">
        <div class="flex items-center space-x-4">
          <div>
            <h1 class="text-2xl font-semibold text-gray-900 dark:text-white tracking-tight">Control de Cajas</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1 font-normal">Administración y supervisión de sesiones de caja</p>
          </div>
        </div>
        
        <div class="flex items-center gap-3">
          <!-- Botón Secundario -->
          <button @click="refreshSessions"
                  :disabled="loading"
                  class="px-5 py-2.5 bg-[#f8f9fa] dark:bg-[#1e1f20] hover:bg-gray-100 dark:hover:bg-[#2a2a2d] text-gray-700 dark:text-gray-200 text-sm font-medium rounded-full border border-gray-200 dark:border-gray-700 transition-all duration-200 flex items-center space-x-2">
            <svg class="w-4 h-4" :class="{'animate-spin': loading}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
            </svg>
            <span>{{ loading ? 'Actualizando...' : 'Refrescar' }}</span>
          </button>
          
          <!-- Botón Principal -->
          <button @click="showNewSessionModal = true"
                  class="px-6 py-2.5 bg-gray-900 dark:bg-white hover:bg-black dark:hover:bg-gray-100 text-white dark:text-gray-900 text-sm font-medium rounded-full transition-all duration-300 flex items-center space-x-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            <span>Nueva Sesión</span>
          </button>
        </div>
      </div>

      <!-- Métricas Principales Gemini -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        
        <!-- Sesiones Activas -->
        <div class="bg-white dark:bg-[#131314] rounded-xl px-4 py-3 border border-gray-200 dark:border-gray-800 hover:border-gray-300 dark:hover:border-gray-700 transition-all duration-200">
          <div class="flex items-center gap-3">
            <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0 bg-emerald-50 dark:bg-emerald-900/30 border border-emerald-200 dark:border-emerald-800/50">
              <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
              </svg>
            </div>
            <div class="flex-1 min-w-0">
              <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Sesiones Activas</p>
              <p class="text-2xl font-semibold text-gray-900 dark:text-white mt-0.5">{{ activeSessions.length }}</p>
            </div>
          </div>
        </div>
        
        <!-- Total en Cajas -->
        <div class="bg-white dark:bg-[#131314] rounded-xl px-4 py-3 border border-gray-200 dark:border-gray-800 hover:border-gray-300 dark:hover:border-gray-700 transition-all duration-200">
          <div class="flex items-center gap-3">
            <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0 bg-blue-50 dark:bg-blue-900/30 border border-blue-200 dark:border-blue-800/50">
              <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
              </svg>
            </div>
            <div class="flex-1 min-w-0">
              <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Total en Cajas</p>
              <p class="text-2xl font-semibold text-gray-900 dark:text-white mt-0.5">${{ totalCashAmount.toLocaleString() }}</p>
            </div>
          </div>
        </div>
        
        <!-- Ventas del Día -->
        <div class="bg-white dark:bg-[#131314] rounded-xl px-4 py-3 border border-gray-200 dark:border-gray-800 hover:border-gray-300 dark:hover:border-gray-700 transition-all duration-200">
          <div class="flex items-center gap-3">
            <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0 bg-amber-50 dark:bg-amber-900/30 border border-amber-200 dark:border-amber-800/50">
              <svg class="w-5 h-5 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
              </svg>
            </div>
            <div class="flex-1 min-w-0">
              <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Ventas Hoy</p>
              <p class="text-2xl font-semibold text-gray-900 dark:text-white mt-0.5">${{ totalSalesToday.toLocaleString() }}</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Panel de Filtros Gemini -->
      <div class="bg-white dark:bg-[#131314] rounded-xl shadow-lg p-4 border border-gray-200 dark:border-gray-800">
        <div class="flex flex-wrap items-center gap-4">
          
          <!-- Búsqueda -->
          <div class="flex-1 min-w-64 relative">
            <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <input
              v-model="searchQuery"
              type="text"
              placeholder="Buscar usuario..."
              class="w-full pl-10 pr-4 py-3 text-sm rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-[#1e1f20] text-gray-900 dark:text-gray-200 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent"
            />
          </div>
          
          <!-- Filtro Estado -->
          <select
            v-model="statusFilter"
            class="px-3 py-3 text-sm rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-[#1e1f20] text-gray-700 dark:text-gray-300 font-medium focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400"
          >
            <option value="">Todos los estados</option>
            <option value="open">🟢 Sesiones Activas</option>
            <option value="closed">🔴 Sesiones Cerradas</option>
          </select>
          
          <!-- Filtro Fecha -->
          <input
            v-model="dateFilter"
            type="date"
            class="px-3 py-3 text-sm rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-[#1e1f20] text-gray-700 dark:text-gray-300 font-medium focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400"
          />
          
          <!-- Botón Exportar -->
          <button
            @click="exportData"
            class="px-4 py-2.5 bg-emerald-600 dark:bg-emerald-600 hover:bg-emerald-700 dark:hover:bg-emerald-500 text-white text-sm font-medium rounded-full transition-all duration-200 flex items-center space-x-2"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3M3 17V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v10a2 2 0 01-2 2H5a2 2 0 01-2-2z"/>
            </svg>
            <span>Exportar Excel</span>
          </button>
          
          <!-- Botón Limpiar Filtros -->
          <button
            @click="clearFilters"
            class="p-3 text-gray-500 dark:text-gray-400 hover:text-red-600 dark:hover:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg border border-transparent hover:border-red-200 dark:hover:border-red-700/50 transition-colors duration-200"
            title="Limpiar filtros"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
            </svg>
          </button>
        </div>
      </div>

      <!-- Sessions Table -->
      <div class="bg-white dark:bg-[#131314] rounded-xl shadow-lg overflow-hidden border border-gray-200 dark:border-gray-800" style="flex: 1; display: flex; flex-direction: column; min-height: 0;">
        <div class="bg-[#f8f9fa] dark:bg-[#1e1f20] border-b border-gray-200 dark:border-gray-800 px-4 py-3" style="flex-shrink: 0;">
          <div class="flex items-center justify-between">
            <div class="flex items-center space-x-3">
              <div class="w-8 h-8 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center">
                <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                </svg>
              </div>
              <div>
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Sesiones de Caja</h2>
                <p class="text-gray-600 dark:text-gray-400 text-sm">{{ filteredSessions.length }} registros</p>
              </div>
            </div>
            <div class="flex items-center space-x-2">
              <div class="px-3 py-1 bg-blue-50 dark:bg-blue-900/30 border border-blue-200 dark:border-blue-700/50 rounded-lg">
                <span class="text-blue-700 dark:text-blue-400 text-xs font-medium">
                  {{ new Date().toLocaleTimeString('es-ES') }}
                </span>
              </div>
            </div>
          </div>
        </div>
        
        <div class="overflow-x-auto flex-1 overflow-y-auto">
          <table class="min-w-full">
            <thead class="border-b border-gray-200 dark:border-gray-800">
              <tr>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-600 dark:text-gray-400 uppercase tracking-wider">
                  <div class="flex items-center space-x-1">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    <span>Usuario</span>
                  </div>
                </th>
                <th class="px-3 py-2 text-left text-xs font-medium text-gray-600 dark:text-gray-400 uppercase tracking-wider">
                  <span>Estado</span>
                </th>
                <th class="px-3 py-2 text-left text-xs font-medium text-gray-600 dark:text-gray-400 uppercase tracking-wider">
                  <span>Apertura</span>
                </th>
                <th class="px-3 py-2 text-left text-xs font-medium text-gray-600 dark:text-gray-400 uppercase tracking-wider">
                  <span>Cierre</span>
                </th>
                <th class="px-3 py-2 text-left text-xs font-medium text-gray-600 dark:text-gray-400 uppercase tracking-wider">
                  <span>Inicial</span>
                </th>
                <th class="px-3 py-2 text-left text-xs font-medium text-gray-600 dark:text-gray-400 uppercase tracking-wider">
                  <span>Ventas</span>
                </th>
                <th class="px-3 py-2 text-left text-xs font-medium text-gray-600 dark:text-gray-400 uppercase tracking-wider">
                  <span>Estado Cierre</span>
                </th>
                <th class="px-3 py-2 text-left text-xs font-medium text-gray-600 dark:text-gray-400 uppercase tracking-wider">
                  <span>Duración</span>
                </th>
                <th class="px-3 py-2 text-left text-xs font-medium text-gray-600 dark:text-gray-400 uppercase tracking-wider">
                  <span>Acciones</span>
                </th>
              </tr>
            </thead>
            <tbody class="bg-transparent">
              <tr v-for="session in paginatedSessions" :key="session.id" 
                  class="hover:bg-gray-50 dark:hover:bg-[#1e1f20] transition-all duration-200 border-b border-gray-200 dark:border-gray-800">
                <td class="px-4 py-3 whitespace-nowrap">
                  <div class="flex items-center">
                    <div class="relative">
                      <div class="h-8 w-8 rounded-lg bg-emerald-100 dark:bg-emerald-900/30 flex items-center justify-center text-emerald-600 dark:text-emerald-400 font-medium text-sm">
                        {{ getUserInitials(session.user?.name || 'Usuario') }}
                      </div>
                      <div v-if="session.status === 'open'" class="absolute -top-0.5 -right-0.5 w-3 h-3 bg-emerald-500 rounded-full border-2 border-white dark:border-[#131314]"></div>
                    </div>
                    <div class="ml-2">
                      <div class="text-sm font-medium text-gray-900 dark:text-white">
                        {{ session.user?.name || 'Usuario desconocido' }}
                      </div>
                      <div class="text-xs text-gray-500 dark:text-gray-500">
                        ID: #{{ session.id }}
                      </div>
                    </div>
                  </div>
                </td>
                <td class="px-3 py-3 whitespace-nowrap">
                  <span :class="getStatusBadgeClass(session.status)" class="inline-flex items-center px-2 py-1 text-xs font-medium rounded-full">
                    <div :class="[
                      'w-1.5 h-1.5 rounded-full mr-1',
                      session.status === 'open' ? 'bg-emerald-400' : 'bg-gray-400'
                    ]"></div>
                    {{ session.status === 'open' ? 'ACTIVA' : 'CERRADA' }}
                  </span>
                </td>
                <td class="px-3 py-3 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                  <div class="font-medium">{{ formatDate(session.opened_at) }}</div>
                  <div class="text-gray-500 dark:text-gray-500 text-xs">{{ formatTime(session.opened_at) }}</div>
                </td>
                <td class="px-3 py-3 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                  <div v-if="session.status === 'closed' && session.closed_at" class="font-medium">{{ formatDate(session.closed_at) }}</div>
                  <div v-if="session.status === 'closed' && session.closed_at" class="text-gray-500 dark:text-gray-500 text-xs">{{ formatTime(session.closed_at) }}</div>
                  <span v-if="session.status === 'open'" class="text-amber-600 dark:text-amber-400 text-xs font-medium">En curso...</span>
                </td>
                <td class="px-3 py-3 whitespace-nowrap text-sm font-semibold text-gray-900 dark:text-white">
                  ${{ parseFloat(session.opening_amount || 0).toLocaleString() }}
                </td>
                <td class="px-3 py-3 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                  <span class="font-medium">
                    ${{ parseFloat(session.total_sales || 0).toLocaleString() }}
                  </span>
                </td>
                <td class="px-3 py-3 whitespace-nowrap text-sm">
                  <span v-if="session.status === 'closed'" :class="getClosingStatusClass(session.closing_status)" 
                        class="inline-flex items-center px-2 py-1 text-xs font-medium rounded-full">
                    {{ getClosingStatusText(session.closing_status) }}
                  </span>
                  <span v-else class="text-gray-400 dark:text-gray-500 text-xs">En curso</span>
                </td>
                <td class="px-3 py-3 whitespace-nowrap text-sm text-gray-500 dark:text-gray-500">
                  {{ getSessionDuration(session) }}
                </td>
                <td class="px-3 py-3 whitespace-nowrap text-sm font-medium">
                  <div class="flex items-center space-x-1.5">
                    <button
                      @click="viewSessionDetails(session)"
                      class="p-2 text-gray-400 dark:text-gray-500 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-lg border border-transparent hover:border-blue-200 dark:hover:border-blue-700/50 transition-all duration-200"
                      title="Ver detalles"
                    >
                      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                      </svg>
                    </button>
                    <button
                      @click="viewSessionAudit(session)"
                      class="p-2 text-gray-400 dark:text-gray-500 hover:text-gray-600 dark:hover:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-900/20 rounded-lg border border-transparent hover:border-gray-200 dark:hover:border-gray-700/50 transition-all duration-200"
                      title="Ver auditoría completa"
                    >
                      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                      </svg>
                    </button>
                    <button
                      v-if="session.status === 'open'"
                      @click="showCloseModal(session)"
                      class="p-2 text-gray-400 dark:text-gray-500 hover:text-rose-600 dark:hover:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-900/20 rounded-lg border border-transparent hover:border-rose-200 dark:hover:border-rose-700/50 transition-all duration-200"
                      title="Cerrar sesión"
                    >
                      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                      </svg>
                    </button>
                    <button
                      @click="generateReport(session)"
                      class="p-2 text-gray-400 dark:text-gray-500 hover:text-emerald-600 dark:hover:text-emerald-400 hover:bg-emerald-50 dark:hover:bg-emerald-900/20 rounded-lg border border-transparent hover:border-emerald-200 dark:hover:border-emerald-700/50 transition-all duration-200"
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
        <div v-if="totalPages > 1" class="border-t border-gray-200 dark:border-gray-800 bg-white dark:bg-[#131314] px-4 py-3 flex items-center justify-between sm:px-6">
          <div class="flex-1 flex justify-between sm:hidden">
            <button
              @click="prevPage"
              :disabled="currentPage === 1"
              class="relative inline-flex items-center px-4 py-2 border border-gray-200 dark:border-gray-700 text-sm font-medium rounded-lg text-gray-700 dark:text-gray-300 bg-white dark:bg-[#1e1f20] hover:bg-gray-50 dark:hover:bg-[#2a2a2d] disabled:opacity-50 disabled:cursor-not-allowed"
            >
              Anterior
            </button>
            <button
              @click="nextPage"
              :disabled="currentPage === totalPages"
              class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-200 dark:border-gray-700 text-sm font-medium rounded-lg text-gray-700 dark:text-gray-300 bg-white dark:bg-[#1e1f20] hover:bg-gray-50 dark:hover:bg-[#2a2a2d] disabled:opacity-50 disabled:cursor-not-allowed"
            >
              Siguiente
            </button>
          </div>
          <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
            <div class="flex items-center space-x-2">
              <p class="text-sm text-gray-700 dark:text-gray-400">
                Mostrando
                <span class="font-medium dark:text-gray-200">{{ paginationInfo.start }}</span>
                a
                <span class="font-medium dark:text-gray-200">{{ paginationInfo.end }}</span>
                de
                <span class="font-medium dark:text-gray-200">{{ paginationInfo.total }}</span>
                resultados
              </p>
              <select
                v-model="itemsPerPage"
                @change="changeItemsPerPage(itemsPerPage)"
                class="ml-4 px-3 py-1 border border-gray-200 dark:border-gray-700 bg-white dark:bg-[#1e1f20] text-gray-700 dark:text-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400"
              >
                <option v-for="option in itemsPerPageOptions" :key="option" :value="option">
                  {{ option }} por página
                </option>
              </select>
            </div>
            <div>
              <nav class="relative z-0 inline-flex rounded-lg shadow-sm -space-x-px" aria-label="Pagination">
                <!-- Previous button -->
                <button
                  @click="prevPage"
                  :disabled="currentPage === 1"
                  class="relative inline-flex items-center px-2 py-2 rounded-l-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-[#1e1f20] text-sm font-medium text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-[#2a2a2d] disabled:opacity-50 disabled:cursor-not-allowed"
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
                        ? 'z-10 bg-blue-50 dark:bg-blue-900/30 border-blue-500 dark:border-blue-700 text-blue-600 dark:text-blue-400'
                        : 'bg-white dark:bg-[#1e1f20] border-gray-200 dark:border-gray-700 text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-[#2a2a2d]',
                      'relative inline-flex items-center px-4 py-2 border text-sm font-medium'
                    ]"
                  >
                    {{ page }}
                  </button>
                  <span
                    v-else-if="(page === 2 && currentPage > 4) || (page === totalPages - 1 && currentPage < totalPages - 3)"
                    class="relative inline-flex items-center px-4 py-2 border border-gray-200 dark:border-gray-700 bg-white dark:bg-[#1e1f20] text-sm font-medium text-gray-700 dark:text-gray-400"
                  >
                    ...
                  </span>
                </template>

                <!-- Next button -->
                <button
                  @click="nextPage"
                  :disabled="currentPage === totalPages"
                  class="relative inline-flex items-center px-2 py-2 rounded-r-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-[#1e1f20] text-sm font-medium text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-[#2a2a2d] disabled:opacity-50 disabled:cursor-not-allowed"
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
          <div class="w-16 h-16 mx-auto bg-gray-100 dark:bg-gray-800/50 rounded-full flex items-center justify-center mb-4">
            <svg class="w-8 h-8 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
            </svg>
          </div>
          <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">No hay sesiones</h3>
          <p class="text-gray-500 dark:text-gray-400 mb-4">Ajusta los filtros o crea una nueva sesión</p>
          <button
            @click="clearFilters"
            class="px-4 py-2 bg-gray-900 dark:bg-white text-white dark:text-gray-900 rounded-full hover:bg-black dark:hover:bg-gray-100 transition-all duration-300 text-sm font-medium"
          >
            Limpiar filtros
          </button>
        </div>
      </div>
    </div>

    <!-- Session Details Modal Gemini -->
    <div v-if="selectedSession" class="fixed inset-0 bg-black/75 dark:bg-black/85 flex items-center justify-center z-50 p-4" @click.self="selectedSession = null">
      <div class="bg-white dark:bg-[#131314] rounded-xl max-w-3xl w-full max-h-[90vh] overflow-y-auto shadow-2xl border border-gray-200 dark:border-gray-800">
        <!-- Header Gemini -->
        <div class="bg-white dark:bg-[#131314] border-b border-gray-200 dark:border-gray-800 px-6 py-4 rounded-t-xl">
          <div class="flex justify-between items-center">
            <div class="flex items-center space-x-3">
              <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center">
                <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
              </div>
              <div>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Detalles de Sesión</h3>
                <p class="text-sm text-gray-600 dark:text-gray-400">ID: #{{ selectedSession.id }} - {{ selectedSession.user?.name }}</p>
              </div>
            </div>
            <button 
              @click="selectedSession = null" 
              class="text-gray-400 dark:text-gray-500 hover:text-gray-600 dark:hover:text-gray-300 transition-colors"
            >
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
              </svg>
            </button>
          </div>
        </div>
        
        <div class="p-6">
          <!-- User Info -->
          <div class="bg-[#f8f9fa] dark:bg-[#1e1f20] rounded-lg p-4 mb-6 border border-gray-200 dark:border-gray-800">
            <div class="flex items-center space-x-4">
              <div class="relative">
                <div class="w-14 h-14 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center text-blue-600 dark:text-blue-400 font-semibold text-lg">
                  {{ getUserInitials(selectedSession.user?.name || 'Usuario') }}
                </div>
                <div v-if="selectedSession.status === 'open'" class="absolute -top-1 -right-1 w-4 h-4 bg-green-500 rounded-full border-2 border-white dark:border-[#1e1f20]"></div>
              </div>
              <div class="flex-1">
                <h4 class="text-lg font-semibold text-gray-900 dark:text-white">{{ selectedSession.user?.name }}</h4>
                <p class="text-sm text-gray-600 dark:text-gray-400">{{ selectedSession.user?.email }}</p>
                <div class="flex items-center mt-1 space-x-3">
                  <span :class="getStatusBadgeClass(selectedSession.status)" class="px-2 py-1 text-xs font-medium rounded-full">
                    {{ selectedSession.status === 'open' ? 'ACTIVA' : 'CERRADA' }}
                  </span>
                  <span class="text-xs text-gray-500 dark:text-gray-500">
                    CC: {{ selectedSession.user?.cc }}
                  </span>
                </div>
              </div>
            </div>
          </div>

          <!-- Session Info Grid -->
          <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-6">
            <div class="bg-[#f8f9fa] dark:bg-[#1e1f20] p-3 rounded-xl border border-gray-200 dark:border-gray-800">
              <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Fecha Apertura</label>
              <p class="text-sm font-medium text-gray-900 dark:text-white">{{ formatDate(selectedSession.opening_date) }}</p>
            </div>
            <div class="bg-[#f8f9fa] dark:bg-[#1e1f20] p-3 rounded-xl border border-gray-200 dark:border-gray-800">
              <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Hora Apertura</label>
              <p class="text-sm font-medium text-gray-900 dark:text-white">{{ selectedSession.opening_time }}</p>
            </div>
            <div class="bg-[#f8f9fa] dark:bg-[#1e1f20] p-3 rounded-xl border border-gray-200 dark:border-gray-800">
              <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Duración</label>
              <p class="text-sm font-medium text-gray-900 dark:text-white">{{ getSessionDuration(selectedSession) }}</p>
            </div>
            <div class="bg-[#f8f9fa] dark:bg-[#1e1f20] p-3 rounded-xl border border-gray-200 dark:border-gray-800">
              <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Actualización</label>
              <p class="text-sm font-medium text-gray-900 dark:text-white">{{ formatDate(selectedSession.updated_at) }}</p>
            </div>
          </div>

          <!-- Financial Summary -->
          <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <div class="bg-gradient-to-br from-emerald-50 to-emerald-100/50 dark:from-emerald-900/20 dark:to-emerald-800/10 p-4 rounded-xl border border-emerald-200/60 dark:border-emerald-800/30">
              <div class="flex items-center justify-between">
                <div>
                  <p class="text-xs font-medium text-emerald-700 dark:text-emerald-400">Monto Inicial</p>
                  <p class="text-xl font-semibold text-emerald-900 dark:text-emerald-300">${{ parseFloat(selectedSession.opening_amount || 0).toLocaleString() }}</p>
                </div>
                <div class="w-10 h-10 bg-emerald-100 dark:bg-emerald-900/30 rounded-lg flex items-center justify-center">
                  <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                  </svg>
                </div>
              </div>
            </div>
            
            <div class="bg-gradient-to-br from-blue-50 to-blue-100/50 dark:from-blue-900/20 dark:to-blue-800/10 p-4 rounded-xl border border-blue-200/60 dark:border-blue-800/30">
              <div class="flex items-center justify-between">
                <div>
                  <p class="text-xs font-medium text-blue-700 dark:text-blue-400">Total Ventas</p>
                  <p class="text-xl font-semibold text-blue-900 dark:text-blue-300">${{ parseFloat(selectedSession.total_sales || 0).toLocaleString() }}</p>
                </div>
                <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center">
                  <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                  </svg>
                </div>
              </div>
            </div>
            
            <div class="bg-gradient-to-br from-teal-50 to-teal-100/50 dark:from-teal-900/20 dark:to-teal-800/10 p-4 rounded-xl border border-teal-200/60 dark:border-teal-800/30">
              <div class="flex items-center justify-between">
                <div>
                  <p class="text-xs font-medium text-teal-700 dark:text-teal-400">Efectivo</p>
                  <p class="text-xl font-semibold text-teal-900 dark:text-teal-300">${{ parseFloat(selectedSession.cash_sales || 0).toLocaleString() }}</p>
                </div>
                <div class="w-10 h-10 bg-teal-100 dark:bg-teal-900/30 rounded-lg flex items-center justify-center">
                  <svg class="w-5 h-5 text-teal-600 dark:text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v2a2 2 0 002 2z"/>
                  </svg>
                </div>
              </div>
            </div>
          </div>

          <!-- Notes -->
          <div v-if="selectedSession.opening_notes" class="mb-6">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Notas de Apertura</label>
            <div class="bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-700/50 rounded-lg p-3">
              <p class="text-sm text-gray-800 dark:text-amber-300">{{ selectedSession.opening_notes }}</p>
            </div>
          </div>

          <!-- Actions -->
          <div class="flex justify-end space-x-3 pt-4 border-t border-gray-200 dark:border-gray-800">
            <button
              @click="selectedSession = null"
              class="px-4 py-2 border border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-300 bg-white dark:bg-[#1e1f20] rounded-full hover:bg-gray-50 dark:hover:bg-[#2a2a2d] transition-colors text-sm font-medium"
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
          </div>
        </div>
      </div>
    </div>

    <!-- Modal de Cierre Empresarial -->
    <div v-if="showCloseSessionModal" class="fixed inset-0 bg-black/60 dark:bg-black/80 flex items-center justify-center z-[100] p-4" @click.self="showCloseSessionModal = false">
      <div class="bg-white dark:bg-[#131314] rounded-xl w-full max-w-2xl max-h-[90vh] overflow-y-auto shadow-2xl border border-gray-200 dark:border-gray-800">
        <!-- Header -->
        <div class="bg-white dark:bg-[#131314] border-b border-gray-200 dark:border-gray-800 px-6 py-4 rounded-t-xl">
          <div class="flex items-center justify-between">
            <div class="flex items-center space-x-3">
              <div class="w-10 h-10 bg-rose-50 dark:bg-rose-900/30 rounded-lg flex items-center justify-center">
                <svg class="w-5 h-5 text-rose-600 dark:text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                </svg>
              </div>
              <div>
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Cerrar Caja</h2>
                <p class="text-sm text-gray-600 dark:text-gray-400">{{ sessionToClose?.user?.name }} - #{{ sessionToClose?.id }}</p>
              </div>
            </div>
            <button @click="showCloseSessionModal = false" class="text-gray-400 dark:text-gray-500 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
              </svg>
            </button>
          </div>
        </div>

        <div class="p-6">
          <!-- Resumen de la sesión -->
          <div class="grid grid-cols-2 gap-3 mb-4">
            <div class="bg-gradient-to-br from-emerald-50 to-emerald-100/50 dark:from-emerald-900/20 dark:to-emerald-800/10 p-3 rounded-xl border border-emerald-200/60 dark:border-emerald-800/30">
              <label class="text-xs font-medium text-emerald-700 dark:text-emerald-400">Monto Inicial</label>
              <p class="text-xl font-semibold text-emerald-900 dark:text-emerald-300">${{ parseFloat(sessionToClose?.opening_amount || 0).toLocaleString() }}</p>
            </div>
            <div class="bg-gradient-to-br from-blue-50 to-blue-100/50 dark:from-blue-900/20 dark:to-blue-800/10 p-3 rounded-xl border border-blue-200/60 dark:border-blue-800/30">
              <label class="text-xs font-medium text-blue-700 dark:text-blue-400">Ventas Totales</label>
              <p class="text-xl font-semibold text-blue-900 dark:text-blue-300">${{ parseFloat(sessionToClose?.total_sales || 0).toLocaleString() }}</p>
            </div>
          </div>

          <!-- Desglose por método de pago -->
          <div class="grid grid-cols-3 gap-3 mb-4">
            <div class="bg-gradient-to-br from-amber-50 to-amber-100/50 dark:from-amber-900/20 dark:to-amber-800/10 p-3 rounded-xl border border-amber-200/60 dark:border-amber-800/30">
              <label class="text-xs font-medium text-amber-700 dark:text-amber-400">Efectivo</label>
              <p class="text-base font-semibold text-amber-900 dark:text-amber-300">${{ parseFloat(sessionToClose?.cash_sales || 0).toLocaleString() }}</p>
            </div>
            <div class="bg-gradient-to-br from-sky-50 to-sky-100/50 dark:from-sky-900/20 dark:to-sky-800/10 p-3 rounded-xl border border-sky-200/60 dark:border-sky-800/30">
              <label class="text-xs font-medium text-sky-700 dark:text-sky-400">Tarjetas</label>
              <p class="text-base font-semibold text-sky-900 dark:text-sky-300">${{ parseFloat(sessionToClose?.card_sales || 0).toLocaleString() }}</p>
            </div>
            <div class="bg-gradient-to-br from-teal-50 to-teal-100/50 dark:from-teal-900/20 dark:to-teal-800/10 p-3 rounded-xl border border-teal-200/60 dark:border-teal-800/30">
              <label class="text-xs font-medium text-teal-700 dark:text-teal-400">Transferencias</label>
              <p class="text-base font-semibold text-teal-900 dark:text-teal-300">${{ parseFloat(sessionToClose?.transfer_sales || 0).toLocaleString() }}</p>
            </div>
          </div>

          <!-- Formulario de cierre -->
          <div class="space-y-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Monto Real en Caja ($) *</label>
              <input
                v-model="closeForm.actual_amount"
                type="text"
                inputmode="decimal"
                class="w-full px-4 py-3 border border-gray-200 dark:border-gray-700 rounded-lg bg-white dark:bg-[#1e1f20] text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-lg font-semibold"
                placeholder="0.00"
                @input="closeForm.actual_amount = closeForm.actual_amount.replace(/[^0-9.]/g, '')"
              >
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Notas de Cierre</label>
              <textarea
                v-model="closeForm.closing_notes"
                rows="3"
                class="w-full px-4 py-3 border border-gray-200 dark:border-gray-700 rounded-lg bg-white dark:bg-[#1e1f20] text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                placeholder="Observaciones sobre el cierre..."
              ></textarea>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Gastos y Salidas (Opcional)</label>
              <textarea
                v-model="closeForm.expenses_detail"
                rows="2"
                class="w-full px-4 py-3 border border-gray-200 dark:border-gray-700 rounded-lg bg-white dark:bg-[#1e1f20] text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                placeholder="Detallar gastos realizados durante la sesión..."
              ></textarea>
            </div>
          </div>

          <!-- Cálculo de diferencia -->
          <div v-if="closeForm.actual_amount" class="mt-4 p-3 rounded-lg" 
               :class="getDifferenceClass()">
            <div class="text-center">
              <p class="text-xs font-medium opacity-80">Diferencia</p>
              <p class="text-xl font-semibold">
                ${{ Math.abs(getDifference()).toLocaleString() }}
                <span class="text-sm">{{ getDifferenceText() }}</span>
              </p>
            </div>
          </div>

          <!-- Acciones -->
          <div class="flex space-x-3 mt-6 pt-4 border-t border-gray-200 dark:border-gray-800">
            <button
              @click="showCloseSessionModal = false"
              class="flex-1 px-4 py-2.5 border border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-300 bg-[#f8f9fa] dark:bg-[#1e1f20] rounded-full hover:bg-gray-100 dark:hover:bg-[#2a2b2e] font-medium text-sm transition-colors"
            >
              Cancelar
            </button>
            <button
              @click="confirmCloseSession"
              :disabled="!closeForm.actual_amount || closingSession"
              class="flex-1 px-4 py-2.5 bg-rose-600 dark:bg-rose-500 text-white rounded-full hover:bg-rose-700 dark:hover:bg-rose-600 disabled:opacity-50 disabled:cursor-not-allowed font-medium text-sm transition-colors"
            >
              {{ closingSession ? 'Cerrando...' : 'Cerrar Caja' }}
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal de Auditoría Empresarial -->
    <div v-if="showAuditModal" class="fixed inset-0 bg-black/60 dark:bg-black/80 flex items-center justify-center z-[100] p-4" @click.self="showAuditModal = false">
      <div class="bg-white dark:bg-[#131314] rounded-xl w-full max-w-6xl max-h-[90vh] overflow-y-auto shadow-2xl border border-gray-200 dark:border-gray-800">
        <!-- Header -->
        <div class="bg-white dark:bg-[#131314] border-b border-gray-200 dark:border-gray-800 px-6 py-4 rounded-t-xl">
          <div class="flex items-center justify-between">
            <div class="flex items-center space-x-3">
              <div class="w-10 h-10 bg-indigo-50 dark:bg-indigo-900/30 rounded-lg flex items-center justify-center">
                <svg class="w-5 h-5 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                </svg>
              </div>
              <div>
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Auditoría de Sesión</h2>
                <p class="text-sm text-gray-600 dark:text-gray-400">{{ auditData?.session?.user?.name }} - #{{ auditData?.session?.id }}</p>
              </div>
            </div>
            <button @click="showAuditModal = false" class="text-gray-400 dark:text-gray-500 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
              </svg>
            </button>
          </div>
        </div>

        <div class="p-6">
          <!-- Estadísticas principales -->
          <div v-if="auditData?.statistics" class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-4">
            <div class="bg-gradient-to-br from-blue-50 to-blue-100/50 dark:from-blue-900/20 dark:to-blue-800/10 p-3 rounded-xl border border-blue-200/60 dark:border-blue-800/30 text-center">
              <p class="text-xl font-semibold text-blue-900 dark:text-blue-300">{{ auditData.statistics.total_transactions }}</p>
              <p class="text-xs font-medium text-blue-700 dark:text-blue-400">Ventas</p>
            </div>
            <div class="bg-gradient-to-br from-emerald-50 to-emerald-100/50 dark:from-emerald-900/20 dark:to-emerald-800/10 p-3 rounded-xl border border-emerald-200/60 dark:border-emerald-800/30 text-center">
              <p class="text-xl font-semibold text-emerald-900 dark:text-emerald-300">${{ parseFloat(auditData.statistics.average_sale || 0).toLocaleString() }}</p>
              <p class="text-xs font-medium text-emerald-700 dark:text-emerald-400">Venta Promedio</p>
            </div>
            <div class="bg-gradient-to-br from-cyan-50 to-cyan-100/50 dark:from-cyan-900/20 dark:to-cyan-800/10 p-3 rounded-xl border border-cyan-200/60 dark:border-cyan-800/30 text-center">
              <p class="text-xl font-semibold text-cyan-900 dark:text-cyan-300">${{ parseFloat(auditData.statistics.largest_sale || 0).toLocaleString() }}</p>
              <p class="text-xs font-medium text-cyan-700 dark:text-cyan-400">Venta Mayor</p>
            </div>
            <div class="bg-gradient-to-br from-orange-50 to-orange-100/50 dark:from-orange-900/20 dark:to-orange-800/10 p-3 rounded-xl border border-orange-200/60 dark:border-orange-800/30 text-center">
              <p class="text-xl font-semibold text-orange-900 dark:text-orange-300">{{ Math.floor(auditData.statistics.session_duration / 60) }}h</p>
              <p class="text-xs font-medium text-orange-700 dark:text-orange-400">Duración</p>
            </div>
          </div>

          <!-- Estadísticas de devoluciones y gastos (si existen) -->
          <div v-if="auditData?.statistics && (auditData.statistics.total_returns > 0 || auditData.statistics.total_expenses > 0)" class="grid grid-cols-2 gap-3 mb-6">
            <div v-if="auditData.statistics.total_returns > 0" class="bg-gradient-to-br from-amber-50 to-amber-100/50 dark:from-amber-900/20 dark:to-amber-800/10 p-3 rounded-xl border border-amber-200/60 dark:border-amber-800/30 text-center">
              <p class="text-xl font-semibold text-amber-900 dark:text-amber-300">{{ auditData.statistics.total_returns }}</p>
              <p class="text-xs font-medium text-amber-700 dark:text-amber-400">Devoluciones</p>
              <p class="text-xs text-amber-600 dark:text-amber-500">-${{ parseFloat(auditData.statistics.total_returns_amount || 0).toLocaleString() }}</p>
            </div>
            <div v-if="auditData.statistics.total_expenses > 0" class="bg-gradient-to-br from-rose-50 to-rose-100/50 dark:from-rose-900/20 dark:to-rose-800/10 p-3 rounded-xl border border-rose-200/60 dark:border-rose-800/30 text-center">
              <p class="text-xl font-semibold text-rose-900 dark:text-rose-300">{{ auditData.statistics.total_expenses }}</p>
              <p class="text-xs font-medium text-rose-700 dark:text-rose-400">Gastos</p>
              <p class="text-xs text-rose-600 dark:text-rose-500">-${{ parseFloat(auditData.statistics.total_expenses_amount || 0).toLocaleString() }}</p>
            </div>
          </div>

          <!-- Timeline -->
          <div v-if="auditData?.timeline" class="mb-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Timeline de Eventos</h3>
            <div class="space-y-3 max-h-96 overflow-y-auto">
              <div v-for="(event, index) in auditData.timeline" :key="index" 
                   class="flex items-start space-x-3 p-3 bg-[#f8f9fa] dark:bg-[#1e1f20] rounded-xl border border-gray-200 dark:border-gray-700/50">
                <div :class="getEventIconClass(event.type)" class="p-2 rounded-full">
                  <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path v-if="event.type === 'opening'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    <path v-else-if="event.type === 'sale'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                    <path v-else-if="event.type === 'return'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/>
                    <path v-else-if="event.type === 'expense'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                    <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                  </svg>
                </div>
                <div class="flex-1">
                  <div class="flex items-center justify-between">
                    <p class="font-medium text-gray-900 dark:text-white">{{ event.description }}</p>
                    <span class="text-sm text-gray-500 dark:text-gray-500">{{ formatTimestamp(event.timestamp) }}</span>
                  </div>
                  <p class="text-lg font-semibold" :class="event.amount < 0 ? 'text-orange-600 dark:text-orange-400' : 'text-green-600 dark:text-green-400'">
                    ${{ parseFloat(event.amount || 0).toLocaleString() }}
                  </p>
                  <div v-if="event.details.customer" class="text-sm text-gray-600 dark:text-gray-400">
                    Cliente: {{ event.details.customer }}
                  </div>
                  <div v-if="event.type === 'return' && event.details.original_invoice" class="text-sm text-gray-600 dark:text-gray-400">
                    Factura original: {{ event.details.original_invoice }}
                  </div>
                  <div v-if="event.type === 'return' && event.details.reason" class="text-sm text-gray-500 dark:text-gray-500 italic">
                    Razón: {{ event.details.reason }}
                  </div>
                  <!-- Detalles de gastos -->
                  <div v-if="event.type === 'expense' && event.details.category" class="text-sm text-gray-600 dark:text-gray-400">
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium" :style="{ backgroundColor: event.details.category_color + '20', color: event.details.category_color }">
                      {{ event.details.category }}
                    </span>
                  </div>
                  <div v-if="event.type === 'expense' && event.details.supplier" class="text-sm text-gray-500 dark:text-gray-500">
                    Proveedor: {{ event.details.supplier }}
                  </div>
                  <div v-if="event.type === 'expense' && event.details.payment_method" class="text-xs text-gray-400 dark:text-gray-600">
                    Método: {{ event.details.payment_method }}
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Botón cerrar -->
          <div class="flex justify-end pt-4 border-t border-gray-200 dark:border-gray-800">
            <button
              @click="showAuditModal = false"
              class="px-6 py-2 bg-gray-900 dark:bg-white text-white dark:text-gray-900 rounded-full hover:bg-black dark:hover:bg-gray-100 font-medium text-sm transition-all duration-300"
            >
              Cerrar
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- 🎯 Modal de Confirmación Profesional (reemplaza confirm() nativo) -->
  <div v-if="showConfirmModal" class="fixed inset-0 bg-black/60 dark:bg-black/80 flex items-center justify-center z-[200]" @click.self="showConfirmModal = false">
    <div class="bg-white dark:bg-[#131314] rounded-xl shadow-2xl w-full max-w-md mx-4 overflow-hidden border border-gray-200 dark:border-gray-800 animate-fade-in">
      <!-- Header con icono -->
      <div class="p-6 text-center">
        <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-amber-100 dark:bg-amber-900/30 flex items-center justify-center">
          <svg class="w-8 h-8 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
          </svg>
        </div>
        <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">Confirmar Cierre de Caja</h3>
        <p class="text-gray-600 dark:text-gray-400 mb-4">
          ¿Estás seguro que deseas cerrar la caja de <span class="font-semibold text-gray-900 dark:text-white">{{ sessionToClose?.user?.name }}</span>?
        </p>
        
        <!-- Resumen del monto -->
        <div class="bg-[#f8f9fa] dark:bg-[#1e1f20] rounded-xl p-4 border border-gray-200 dark:border-gray-700">
          <div class="flex items-center justify-between">
            <span class="text-sm text-gray-600 dark:text-gray-400">Monto a cerrar:</span>
            <span class="text-2xl font-semibold text-emerald-600 dark:text-emerald-400">${{ parseFloat(closeForm.actual_amount || 0).toLocaleString() }}</span>
          </div>
        </div>
      </div>
      
      <!-- Botones -->
      <div class="flex border-t border-gray-200 dark:border-gray-800">
        <button 
          @click="showConfirmModal = false"
          class="flex-1 px-6 py-4 text-gray-700 dark:text-gray-300 font-medium hover:bg-[#f8f9fa] dark:hover:bg-[#1e1f20] transition-colors duration-200"
        >
          Cancelar
        </button>
        <button 
          @click="confirmCloseSessionFinal"
          class="flex-1 px-6 py-4 bg-emerald-600 text-white font-medium hover:bg-emerald-700 transition-colors duration-200"
        >
          Confirmar Cierre
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted, watch } from 'vue'
import { apiCall } from '../services/api.js'
import { useToast } from '../composables/useToast.js'
import { appStore } from '../store/appStore.js'
import { useUIContextStore } from '../store/uiContextStore.js'
import jsPDF from 'jspdf'
import 'jspdf-autotable'

// 🎯 Props y Emits para evitar warnings de Vue
defineProps({
  moduleName: { type: String, default: '' },
  queryParams: { type: Object, default: () => ({}) }
})

defineEmits(['navigate', 'changeModule', 'openQuotationInPos', 'openReturnInPos', 'refresh'])

// Toast system
const { showSuccess, showError, showInfo, showWarning } = useToast()

// 🧠 IA Context Store
const uiContext = useUIContextStore()

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
const showConfirmModal = ref(false) // Modal de confirmación bonito
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
  showInfo('📥 Exportación en desarrollo - Esta función estará disponible pronto')
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
    ? 'bg-emerald-50 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800' 
    : 'bg-gray-50 dark:bg-gray-800 text-gray-700 dark:text-gray-300 border border-gray-200 dark:border-gray-700'
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
  // Mostrar detalles sin mensaje informativo en UI/console
  selectedSession.value = session
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

  // Mostrar modal de confirmación en lugar de confirm()
  showConfirmModal.value = true
}

// 🎯 Función que hace el cierre real después de confirmar en el modal bonito
const confirmCloseSessionFinal = async () => {
  showConfirmModal.value = false
  const actualAmount = parseFloat(closeForm.value.actual_amount)
  
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
      showSuccess('✅ Caja cerrada correctamente')
      showCloseSessionModal.value = false
      sessionToClose.value = null
      
      // 🔄 Actualizar appStore para que el POS detecte el cambio
      await appStore.loadCashSession(true) // force = true
      
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
    expense: 'bg-rose-500',
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
    exact: 'bg-emerald-50 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800',
    surplus: 'bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400 border border-blue-200 dark:border-blue-800',
    deficit: 'bg-rose-50 dark:bg-rose-900/30 text-rose-700 dark:text-rose-400 border border-rose-200 dark:border-rose-800',
    with_expenses: 'bg-amber-50 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400 border border-amber-200 dark:border-amber-800'
  }
  return classes[status] || 'bg-gray-50 dark:bg-gray-800 text-gray-700 dark:text-gray-300 border border-gray-200 dark:border-gray-700'
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
      
      // 🔄 Actualizar appStore para que el POS detecte el cambio
      await appStore.loadCashSession(true) // force = true
      
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

const generateReport = async (session) => {
  try {
    showInfo(`📊 Generando reporte de sesión #${session.id}...`)
    
    const doc = new jsPDF()
    
    // Título principal
    doc.setFont('helvetica', 'bold')
    doc.setFontSize(18)
    doc.text('REPORTE DE SESIÓN DE CAJA', 105, 20, { align: 'center' })
    
    // ID y Estado
    doc.setFontSize(12)
    doc.setTextColor(100, 100, 100)
    doc.text(`Sesión #${session.id}`, 105, 28, { align: 'center' })
    
    // Resetear color
    doc.setTextColor(0, 0, 0)
    
    let y = 45
    
    // === INFORMACIÓN DEL USUARIO ===
    doc.setFont('helvetica', 'bold')
    doc.setFontSize(11)
    doc.setFillColor(240, 240, 240)
    doc.rect(20, y - 5, 170, 8, 'F')
    doc.text('RESPONSABLE', 22, y)
    y += 10
    
    doc.setFont('helvetica', 'normal')
    doc.setFontSize(10)
    doc.text(`Nombre: ${session.user?.name || 'N/A'}`, 22, y)
    y += 6
    if (session.user?.email) {
      doc.text(`Email: ${session.user.email}`, 22, y)
      y += 6
    }
    if (session.user?.cc) {
      doc.text(`CC: ${session.user.cc}`, 22, y)
      y += 6
    }
    
    y += 5
    
    // === INFORMACIÓN DE LA SESIÓN ===
    doc.setFont('helvetica', 'bold')
    doc.setFontSize(11)
    doc.setFillColor(240, 240, 240)
    doc.rect(20, y - 5, 170, 8, 'F')
    doc.text('DETALLES DE LA SESIÓN', 22, y)
    y += 10
    
    doc.setFont('helvetica', 'normal')
    doc.setFontSize(10)
    
    const estadoTexto = session.status === 'open' ? 'ACTIVA' : 'CERRADA'
    const estadoColor = session.status === 'open' ? [34, 197, 94] : [239, 68, 68]
    doc.setTextColor(...estadoColor)
    doc.text(`Estado: ${estadoTexto}`, 22, y)
    doc.setTextColor(0, 0, 0)
    y += 6
    
    doc.text(`Fecha Apertura: ${formatDate(session.opening_date)}`, 22, y)
    y += 6
    doc.text(`Hora Apertura: ${session.opening_time || 'N/A'}`, 22, y)
    y += 6
    
    if (session.closing_date) {
      doc.text(`Fecha Cierre: ${formatDate(session.closing_date)}`, 22, y)
      y += 6
    }
    
    if (session.duration) {
      doc.text(`Duración: ${session.duration}`, 22, y)
      y += 6
    }
    
    y += 5
    
    // === RESUMEN FINANCIERO ===
    doc.setFont('helvetica', 'bold')
    doc.setFontSize(11)
    doc.setFillColor(240, 240, 240)
    doc.rect(20, y - 5, 170, 8, 'F')
    doc.text('RESUMEN FINANCIERO', 22, y)
    y += 10
    
    const financialData = [
      ['Monto Inicial', `$${parseFloat(session.opening_amount || 0).toLocaleString()}`],
      ['Total Ventas', `$${parseFloat(session.total_sales || 0).toLocaleString()}`],
      ['Ventas en Efectivo', `$${parseFloat(session.cash_sales || 0).toLocaleString()}`],
      ['Efectivo Esperado', `$${(parseFloat(session.opening_amount || 0) + parseFloat(session.cash_sales || 0)).toLocaleString()}`]
    ]
    
    if (session.status === 'closed') {
      financialData.push(['Efectivo Contado', `$${parseFloat(session.closing_amount || 0).toLocaleString()}`])
      const diferencia = parseFloat(session.closing_amount || 0) - (parseFloat(session.opening_amount || 0) + parseFloat(session.cash_sales || 0))
      financialData.push(['Diferencia', `$${diferencia.toLocaleString()}`])
    }
    
    doc.autoTable({
      startY: y,
      body: financialData,
      theme: 'plain',
      styles: { fontSize: 10, cellPadding: 3 },
      columnStyles: {
        0: { fontStyle: 'bold', cellWidth: 80 },
        1: { halign: 'right', cellWidth: 90 }
      }
    })
    
    y = doc.lastAutoTable.finalY + 10
    
    // === NOTAS ===
    if (session.opening_notes) {
      doc.setFont('helvetica', 'bold')
      doc.setFontSize(11)
      doc.setFillColor(255, 250, 200)
      doc.rect(20, y - 5, 170, 8, 'F')
      doc.text('NOTAS DE APERTURA', 22, y)
      y += 10
      
      doc.setFont('helvetica', 'normal')
      doc.setFontSize(9)
      const splitNotes = doc.splitTextToSize(session.opening_notes, 166)
      doc.text(splitNotes, 22, y)
      y += splitNotes.length * 5 + 5
    }
    
    if (session.closing_notes) {
      doc.setFont('helvetica', 'bold')
      doc.setFontSize(11)
      doc.setFillColor(255, 250, 200)
      doc.rect(20, y - 5, 170, 8, 'F')
      doc.text('NOTAS DE CIERRE', 22, y)
      y += 10
      
      doc.setFont('helvetica', 'normal')
      doc.setFontSize(9)
      const splitNotes = doc.splitTextToSize(session.closing_notes, 166)
      doc.text(splitNotes, 22, y)
    }
    
    // Footer
    doc.setFontSize(8)
    doc.setTextColor(150, 150, 150)
    doc.text(`Generado el ${new Date().toLocaleString('es-CO')}`, 105, 280, { align: 'center' })
    
    // Descargar
    doc.save(`Sesion_Caja_${session.id}_${new Date().toISOString().split('T')[0]}.pdf`)
    showSuccess('✅ Reporte generado exitosamente')
    
  } catch (error) {
    console.error('Error generando reporte:', error)
    showError('Error al generar el reporte')
  }
}

// 🧠 ================================
// IA CONTEXT - Control de Cajas
// ================================

// Función para calcular alertas de empleados
const calcularAlertasEmpleados = () => {
  const alertas = []
  const now = new Date()
  
  activeSessions.value.forEach(session => {
    const userName = session.user?.name || 'Usuario desconocido'
    const ventas = parseFloat(session.total_sales || 0)
    
    // Calcular duración de la sesión
    let horasSesion = 0
    if (session.created_at || session.opened_at) {
      const openedAt = new Date(session.created_at || session.opened_at)
      horasSesion = (now - openedAt) / (1000 * 60 * 60)
    }
    
    // Alerta: Más de 2 horas sin ventas (si tiene sesión abierta)
    if (horasSesion > 2 && ventas === 0) {
      alertas.push({
        tipo: 'sin_ventas',
        usuario: userName,
        mensaje: `${userName} lleva ${Math.floor(horasSesion)} horas sin registrar ventas`,
        severidad: 'alta'
      })
    }
    
    // Alerta: Sesión muy larga (más de 10 horas)
    if (horasSesion > 10) {
      alertas.push({
        tipo: 'sesion_larga',
        usuario: userName,
        mensaje: `${userName} tiene sesión abierta por más de ${Math.floor(horasSesion)} horas`,
        severidad: 'media'
      })
    }
  })
  
  return alertas
}

// Función para obtener resumen de rendimiento por empleado (incluye gastos y devoluciones)
const obtenerRendimientoEmpleado = async (busqueda) => {
  const searchTerm = busqueda?.toLowerCase() || ''
  
  // Buscar en todas las sesiones (activas y cerradas)
  const sesionesEmpleado = sessions.value.filter(s => 
    s.user?.name?.toLowerCase().includes(searchTerm) ||
    s.user?.email?.toLowerCase().includes(searchTerm)
  )
  
  if (sesionesEmpleado.length === 0) {
    // Buscar si al menos existe alguien con ese nombre en las sesiones para sugerir
    const todosLosEmpleados = [...new Set(sessions.value.map(s => s.user?.name).filter(Boolean))]
    
    // Buscar nombres similares (al menos 3 caracteres coinciden)
    const sugerencias = todosLosEmpleados.filter(nombre => {
      const nombreLower = nombre.toLowerCase()
      return nombreLower.includes(searchTerm.substring(0, 3)) || 
             searchTerm.includes(nombreLower.substring(0, 3))
    })
    
    return {
      noEncontrado: true,
      busqueda: busqueda,
      sugerencias: sugerencias.slice(0, 3),
      mensaje: sugerencias.length > 0 
        ? `No encontré sesiones de "${busqueda}". ¿Quisiste decir: ${sugerencias.join(', ')}?`
        : `No encontré sesiones de caja para "${busqueda}". Este empleado no ha abierto caja recientemente.`
    }
  }
  
  const empleado = sesionesEmpleado[0].user
  const sesionesHoy = sesionesEmpleado.filter(s => {
    const today = new Date().toISOString().split('T')[0]
    return s.opening_date?.startsWith(today) || s.created_at?.startsWith(today)
  })
  
  const sesionActiva = sesionesEmpleado.find(s => s.status === 'open')
  const ventasTotalesHoy = sesionesHoy.reduce((sum, s) => sum + parseFloat(s.total_sales || 0), 0)
  const transaccionesHoy = sesionesHoy.length
  
  // Calcular tiempo activo hoy
  let tiempoActivoHoy = 0
  sesionesHoy.forEach(s => {
    const inicio = new Date(s.created_at || s.opened_at)
    const fin = s.status === 'open' ? new Date() : new Date(s.closed_at || s.updated_at)
    tiempoActivoHoy += (fin - inicio) / (1000 * 60 * 60)
  })
  
  // Obtener gastos y devoluciones de las sesiones de hoy
  let gastosHoy = 0
  let cantidadGastos = 0
  let devolucionesHoy = 0
  let cantidadDevoluciones = 0
  
  // Para cada sesión de hoy, obtener los gastos y devoluciones
  for (const sesion of sesionesHoy) {
    try {
      const response = await apiClient.get(`/cash-sessions/${sesion.id}/audit`)
      if (response.data.success) {
        const stats = response.data.statistics
        gastosHoy += parseFloat(stats.total_expenses_amount || 0)
        cantidadGastos += stats.total_expenses || 0
        devolucionesHoy += parseFloat(stats.total_returns_amount || 0)
        cantidadDevoluciones += stats.total_returns || 0
      }
    } catch (err) {
      // Silenciar errores, continuar con siguientes sesiones
    }
  }
  
  return {
    noEncontrado: false,
    nombre: empleado?.name,
    email: empleado?.email,
    sesionActiva: sesionActiva ? {
      id: sesionActiva.id,
      duracion: getSessionDuration(sesionActiva),
      ventas: parseFloat(sesionActiva.total_sales || 0),
      montoInicial: parseFloat(sesionActiva.opening_amount || 0)
    } : null,
    resumenHoy: {
      ventas: ventasTotalesHoy,
      sesiones: transaccionesHoy,
      horasTrabajadas: Math.round(tiempoActivoHoy * 10) / 10,
      // Nuevos campos: gastos y devoluciones
      gastos: gastosHoy,
      cantidadGastos: cantidadGastos,
      devoluciones: devolucionesHoy,
      cantidadDevoluciones: cantidadDevoluciones
    },
    historial: sesionesEmpleado.slice(0, 5).map(s => ({
      id: s.id,
      fecha: formatDate(s.opening_date || s.created_at),
      estado: s.status === 'open' ? 'Activa' : 'Cerrada',
      ventas: parseFloat(s.total_sales || 0),
      estadoCierre: s.closing_status ? getClosingStatusText(s.closing_status) : '-'
    }))
  }
}

// 🏦 Función para distinguir "mi caja" vs "cajas de empleados"
const obtenerMiCajaVsEmpleados = () => {
  const currentUser = appStore.user
  const currentUserId = currentUser?.id
  
  // Buscar MI caja (del usuario actual)
  const miSesion = activeSessions.value.find(s => s.user_id === currentUserId)
  
  // Cajas de otros empleados
  const cajasEmpleados = activeSessions.value.filter(s => s.user_id !== currentUserId)
  
  return {
    miCaja: miSesion ? {
      id: miSesion.id,
      estado: 'Activa',
      duracion: getSessionDuration(miSesion),
      montoInicial: parseFloat(miSesion.opening_amount || 0),
      ventas: parseFloat(miSesion.total_sales || 0),
      efectivo: parseFloat(miSesion.cash_sales || 0),
      totalEnCaja: parseFloat(miSesion.opening_amount || 0) + parseFloat(miSesion.total_sales || 0)
    } : null,
    cajasEmpleados: cajasEmpleados.map(s => ({
      id: s.id,
      empleado: s.user?.name || 'Desconocido',
      duracion: getSessionDuration(s),
      ventas: parseFloat(s.total_sales || 0),
      montoInicial: parseFloat(s.opening_amount || 0)
    })),
    resumen: {
      tengoCajaAbierta: !!miSesion,
      empleadosActivos: cajasEmpleados.length,
      totalVentasEmpleados: cajasEmpleados.reduce((sum, s) => sum + parseFloat(s.total_sales || 0), 0)
    }
  }
}

// Función para actualizar contexto de IA
const actualizarContextoIA = () => {
  const alertas = calcularAlertasEmpleados()
  
  // Construir lista de sesiones para la IA (resumida)
  const sesionesResumidas = paginatedSessions.value.map(s => ({
    id: s.id,
    usuario: s.user?.name || 'Desconocido',
    estado: s.status === 'open' ? 'Activa' : 'Cerrada',
    ventas: `$${parseFloat(s.total_sales || 0).toLocaleString()}`,
    duracion: getSessionDuration(s),
    montoInicial: `$${parseFloat(s.opening_amount || 0).toLocaleString()}`,
    estadoCierre: s.status === 'closed' ? getClosingStatusText(s.closing_status) : 'En curso'
  }))
  
  // Preparar datos de auditoría si está abierta
  let auditoriaActual = null
  if (showAuditModal.value && auditData.value) {
    auditoriaActual = {
      usuario: auditData.value.session?.user?.name || 'Desconocido',
      estadisticas: auditData.value.statistics ? {
        totalTransacciones: auditData.value.statistics.total_transactions || 0,
        totalVentas: `$${parseFloat(auditData.value.statistics.total_sales || 0).toLocaleString()}`,
        ventaPromedio: `$${parseFloat(auditData.value.statistics.average_sale || 0).toLocaleString()}`,
        ventaMayor: `$${parseFloat(auditData.value.statistics.largest_sale || 0).toLocaleString()}`,
        devoluciones: auditData.value.statistics.total_returns || 0,
        montoDevuelto: `$${parseFloat(auditData.value.statistics.total_returns_amount || 0).toLocaleString()}`,
        gastos: auditData.value.statistics.total_expenses || 0,
        montoGastos: `$${parseFloat(auditData.value.statistics.total_expenses_amount || 0).toLocaleString()}`,
        duracion: `${Math.floor((auditData.value.statistics.session_duration || 0) / 60)}h`
      } : null,
      timeline: auditData.value.timeline || []
    }
  }
  
  uiContext.setScreenData({
    tipoReporte: 'cash-admin',
    modulo: 'Control de Cajas',
    descripcion: 'Gestión de sesiones de caja, auditoría de movimientos y control de empleados',
    kpis: {
      sesionesActivas: activeSessions.value.length,
      totalEnCajas: `$${totalCashAmount.value.toLocaleString()}`,
      ventasHoy: `$${totalSalesToday.value.toLocaleString()}`,
      empleadosConCajaAbierta: activeSessions.value.map(s => s.user?.name).filter(Boolean)
    },
    sesiones: {
      lista: sesionesResumidas,
      totalRegistros: filteredSessions.value.length,
      filtroActual: statusFilter.value || 'todos'
    },
    alertasEmpleados: alertas,
    detalleSeleccionado: selectedSession.value ? {
      id: selectedSession.value.id,
      usuario: selectedSession.value.user?.name,
      estado: selectedSession.value.status,
      montoInicial: parseFloat(selectedSession.value.opening_amount || 0),
      ventas: parseFloat(selectedSession.value.total_sales || 0),
      efectivo: parseFloat(selectedSession.value.cash_sales || 0)
    } : null,
    modales: {
      detalleAbierto: !!selectedSession.value,
      cierreAbierto: showCloseSessionModal.value,
      auditoriaAbierta: showAuditModal.value
    },
    auditoriaActual
  })
}

// Registrar acciones para la IA
const registrarAccionesIA = () => {
  // Ver detalles de sesión
  uiContext.registerAction('verDetalleSesion', async ({ idSesion, busqueda }) => {
    let session = null
    
    if (idSesion) {
      session = sessions.value.find(s => s.id === parseInt(idSesion))
    } else if (busqueda) {
      session = sessions.value.find(s => 
        s.user?.name?.toLowerCase().includes(busqueda.toLowerCase()) ||
        s.id.toString() === busqueda
      )
    }
    
    if (session) {
      viewSessionDetails(session)
      return { 
        success: true, 
        message: `Mostrando detalles de la sesión de ${session.user?.name}`,
        datos: {
          usuario: session.user?.name,
          estado: session.status === 'open' ? 'Activa' : 'Cerrada',
          ventas: parseFloat(session.total_sales || 0),
          montoInicial: parseFloat(session.opening_amount || 0)
        }
      }
    }
    return { success: false, message: 'No encontré esa sesión' }
  })
  
  // Ver auditoría de sesión
  uiContext.registerAction('verAuditoriaSesion', async ({ idSesion, busqueda }) => {
    let session = null
    
    if (idSesion) {
      session = sessions.value.find(s => s.id === parseInt(idSesion))
    } else if (busqueda) {
      session = sessions.value.find(s => 
        s.user?.name?.toLowerCase().includes(busqueda.toLowerCase())
      )
    }
    
    if (session) {
      await viewSessionAudit(session)
      return { success: true, message: `Mostrando auditoría de ${session.user?.name}` }
    }
    return { success: false, message: 'No encontré esa sesión' }
  })
  
  // Buscar sesiones por usuario
  uiContext.registerAction('buscarSesionesPorUsuario', async ({ nombre }) => {
    if (!nombre) {
      return { success: false, message: 'Dime el nombre del usuario a buscar' }
    }
    
    searchQuery.value = nombre
    await new Promise(resolve => setTimeout(resolve, 100))
    actualizarContextoIA()
    
    return { 
      success: true, 
      message: `Filtrando sesiones de "${nombre}". ${filteredSessions.value.length} resultados encontrados.`
    }
  })
  
  // Filtrar por estado
  uiContext.registerAction('filtrarSesionesPorEstado', async ({ estado }) => {
    const estadoMap = {
      'activas': 'open',
      'abiertas': 'open', 
      'open': 'open',
      'cerradas': 'closed',
      'closed': 'closed',
      'todas': '',
      'todos': ''
    }
    
    statusFilter.value = estadoMap[estado?.toLowerCase()] ?? ''
    await new Promise(resolve => setTimeout(resolve, 100))
    actualizarContextoIA()
    
    return { 
      success: true, 
      message: `Mostrando sesiones ${estado || 'todas'}. ${filteredSessions.value.length} resultados.`
    }
  })
  
  // Consultar rendimiento de empleado (GLOBAL - funciona desde cualquier módulo)
  uiContext.registerAction('consultarRendimientoEmpleado', async ({ busqueda }) => {
    if (!busqueda) {
      return { success: false, message: 'Dime el nombre del empleado que quieres consultar' }
    }
    
    // Ahora es async, incluye gastos y devoluciones
    const rendimiento = await obtenerRendimientoEmpleado(busqueda)
    
    // Manejar caso de no encontrado con sugerencias
    if (rendimiento?.noEncontrado) {
      return { 
        success: false, 
        message: rendimiento.mensaje,
        sugerencias: rendimiento.sugerencias
      }
    }
    
    if (!rendimiento) {
      return { success: false, message: `No encontré información de "${busqueda}" en las sesiones de caja` }
    }
    
    return {
      success: true,
      message: `Rendimiento de ${rendimiento.nombre}`,
      datos: rendimiento
    }
  })
  
  // 🏦 Mi caja vs cajas de empleados (GLOBAL)
  uiContext.registerAction('obtenerMiCajaVsEmpleados', async () => {
    const info = obtenerMiCajaVsEmpleados()
    
    let mensaje = ''
    if (info.miCaja) {
      mensaje = `Tu caja está abierta: $${info.miCaja.ventas.toLocaleString()} en ventas, ${info.miCaja.duracion}. `
    } else {
      mensaje = 'No tienes caja abierta. '
    }
    
    if (info.cajasEmpleados.length > 0) {
      mensaje += `Tus empleados (${info.cajasEmpleados.length}): ${info.cajasEmpleados.map(c => `${c.empleado} ($${c.ventas.toLocaleString()})`).join(', ')}`
    } else {
      mensaje += 'No hay empleados con caja abierta.'
    }
    
    return {
      success: true,
      message: mensaje,
      datos: info
    }
  })
  
  // Obtener alertas de empleados
  uiContext.registerAction('obtenerAlertasEmpleados', async () => {
    const alertas = calcularAlertasEmpleados()
    
    if (alertas.length === 0) {
      return { 
        success: true, 
        message: 'Todo bien, no hay alertas de empleados en este momento.',
        alertas: []
      }
    }
    
    return {
      success: true,
      message: `Hay ${alertas.length} alerta(s) de empleados`,
      alertas
    }
  })
  
  // Obtener resumen de cajas (GLOBAL)
  uiContext.registerAction('obtenerResumenCajas', async () => {
    // Incluir info de mi caja vs empleados
    const miCajaInfo = obtenerMiCajaVsEmpleados()
    
    return {
      success: true,
      message: 'Resumen de Control de Cajas',
      datos: {
        sesionesActivas: activeSessions.value.length,
        empleadosActivos: activeSessions.value.map(s => s.user?.name).filter(Boolean),
        totalEnCajas: totalCashAmount.value,
        ventasHoy: totalSalesToday.value,
        alertas: calcularAlertasEmpleados(),
        miCaja: miCajaInfo.miCaja,
        cajasEmpleados: miCajaInfo.cajasEmpleados
      }
    }
  })
  
  // Generar reporte de sesión
  uiContext.registerAction('generarReporteSesion', async ({ idSesion, busqueda }) => {
    let session = null
    
    if (idSesion) {
      session = sessions.value.find(s => s.id === parseInt(idSesion))
    } else if (busqueda) {
      session = sessions.value.find(s => 
        s.user?.name?.toLowerCase().includes(busqueda.toLowerCase())
      )
    }
    
    if (session) {
      await generateReport(session)
      return { success: true, message: `Reporte generado para sesión #${session.id}` }
    }
    return { success: false, message: 'No encontré esa sesión' }
  })
  
  // Refrescar datos
  uiContext.registerAction('refrescarCajas', async () => {
    await refreshSessions()
    actualizarContextoIA()
    return { success: true, message: 'Datos de cajas actualizados' }
  })
}

// Lifecycle
onMounted(() => {
  // Establecer módulo actual para la IA
  uiContext.setCurrentModule('cash-admin')
  
  refreshSessions()
  
  // Registrar acciones para IA
  registrarAccionesIA()
  
  // Actualizar contexto inicial (después de cargar datos)
  setTimeout(() => {
    actualizarContextoIA()
  }, 1000)
})

// Watch para actualizar contexto cuando cambien los datos
watch([sessions, activeSessions, statusFilter, searchQuery, selectedSession], () => {
  actualizarContextoIA()
}, { deep: true })

// Watch para actualizar contexto cuando se abre/cierra el modal de auditoría
watch([showAuditModal, auditData], () => {
  actualizarContextoIA()
}, { deep: true })

// Cleanup al desmontar
onUnmounted(() => {
  uiContext.clearSelection()
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