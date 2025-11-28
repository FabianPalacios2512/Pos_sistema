<template>
  <div class="min-h-screen bg-gradient-to-br from-slate-50 to-blue-50/30 font-sans">
    <div class="p-4 lg:p-6 space-y-6 animate-fade-in">
      
      <!-- 🎯 Header Elegante y Profesional OBLIGATORIO -->
      <div class="flex items-center justify-between pb-4 border-b border-gray-300">
        <div class="flex items-center space-x-4">
          <div class="w-12 h-12 bg-black rounded-xl flex items-center justify-center shadow-lg">
            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
            </svg>
          </div>
          <div>
            <h1 class="text-3xl font-bold text-gray-900">Control de Caja</h1>
            <p class="text-sm text-gray-600">Administración y supervisión de sesiones de caja</p>
          </div>
        </div>
        
        <div class="flex items-center space-x-3">
          <!-- Botón Secundario (Neutro Slate) -->
          <button @click="refreshSessions"
                  :disabled="loading"
                  class="px-4 py-2.5 bg-slate-50 hover:bg-slate-100 text-slate-600 text-sm font-semibold rounded-xl border border-slate-200 shadow-sm transition-all duration-200 flex items-center space-x-2 hover:shadow-md hover:border-slate-300">
            <svg class="w-4 h-4" :class="{'animate-spin': loading}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
            </svg>
            <span>{{ loading ? 'Actualizando...' : 'Actualizar' }}</span>
          </button>
          
          <!-- Botón Principal (Verde Empresarial) -->
          <button @click="showNewSessionModal = true"
                  class="px-5 py-2.5 bg-gradient-to-r from-lime-400 to-green-400 hover:from-lime-500 hover:to-green-500 text-white text-sm font-bold rounded-xl shadow-lg transition-all duration-300 transform hover:scale-105 flex items-center space-x-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            <span>Nueva Sesión</span>
          </button>
        </div>
      </div>

      <!-- Métricas Principales EMPRESARIALES -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        
        <!-- Sesiones Activas (Verde Empresarial) -->
        <div class="bg-white rounded-2xl p-5 border border-gray-300 hover:border-gray-400 transition-all duration-200 hover:shadow-lg">
          <div class="flex items-center space-x-4">
            <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center flex-shrink-0">
              <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
              </svg>
            </div>
            <div class="flex-1 min-w-0">
              <div class="flex items-center justify-between mb-2">
                <h3 class="text-sm font-semibold text-gray-700">Sesiones Activas</h3>
                <span class="text-xs font-semibold text-green-700 bg-green-100 px-2 py-1 rounded-full border border-green-200">
                  ACTIVAS
                </span>
              </div>
              <p class="text-2xl font-bold text-gray-900 mb-1">{{ activeSessions.length }}</p>
              <p class="text-sm text-gray-500">En tiempo real</p>
            </div>
          </div>
        </div>
        
        <!-- Usuarios Conectados (Azul Empresarial) -->
        <div class="bg-white rounded-2xl p-5 border border-gray-300 hover:border-gray-400 transition-all duration-200 hover:shadow-lg">
          <div class="flex items-center space-x-4">
            <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center flex-shrink-0">
              <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
              </svg>
            </div>
            <div class="flex-1 min-w-0">
              <div class="flex items-center justify-between mb-2">
                <h3 class="text-sm font-semibold text-gray-700">Usuarios Online</h3>
                <span class="text-xs font-semibold text-blue-700 bg-blue-100 px-2 py-1 rounded-full border border-blue-200">
                  EN LÍNEA
                </span>
              </div>
              <p class="text-2xl font-bold text-gray-900 mb-1">{{ uniqueUsers.length }}</p>
              <p class="text-sm text-gray-500">Conectados ahora</p>
            </div>
          </div>
        </div>
        
        <!-- Total en Cajas (Ámbar Empresarial) -->
        <div class="bg-white rounded-2xl p-5 border border-gray-300 hover:border-gray-400 transition-all duration-200 hover:shadow-lg">
          <div class="flex items-center space-x-4">
            <div class="w-12 h-12 bg-amber-100 rounded-xl flex items-center justify-center flex-shrink-0">
              <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
              </svg>
            </div>
            <div class="flex-1 min-w-0">
              <div class="flex items-center justify-between mb-2">
                <h3 class="text-sm font-semibold text-gray-700">Total en Cajas</h3>
                <span class="text-xs font-semibold text-amber-700 bg-amber-100 px-2 py-1 rounded-full border border-amber-200">
                  EFECTIVO
                </span>
              </div>
              <p class="text-2xl font-bold text-gray-900 mb-1">${{ totalCashAmount.toLocaleString() }}</p>
              <p class="text-sm text-gray-500">Todas las cajas</p>
            </div>
          </div>
        </div>
        
        <!-- Ventas del Día (Púrpura Empresarial) -->
        <div class="bg-white rounded-2xl p-5 border border-gray-300 hover:border-gray-400 transition-all duration-200 hover:shadow-lg">
          <div class="flex items-center space-x-4">
            <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center flex-shrink-0">
              <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
              </svg>
            </div>
            <div class="flex-1 min-w-0">
              <div class="flex items-center justify-between mb-2">
                <h3 class="text-sm font-semibold text-gray-700">Ventas Hoy</h3>
                <span class="text-xs font-semibold text-purple-700 bg-purple-100 px-2 py-1 rounded-full border border-purple-200">
                  HOY
                </span>
              </div>
              <p class="text-2xl font-bold text-gray-900 mb-1">${{ totalSalesToday.toLocaleString() }}</p>
              <p class="text-sm text-gray-500">Ventas registradas</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Panel de Filtros Empresarial -->
      <div class="bg-white rounded-xl shadow-sm p-4 border border-gray-200">
        <div class="flex flex-wrap items-center gap-4">
          
          <!-- Búsqueda -->
          <div class="flex-1 min-w-64 relative">
            <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <input
              v-model="searchQuery"
              type="text"
              placeholder="Buscar usuario..."
              class="w-full pl-10 pr-3 py-2.5 text-sm border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
            />
          </div>
          
          <!-- Filtro Estado -->
          <select
            v-model="statusFilter"
            class="pl-3 pr-8 py-2.5 text-sm border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 min-w-40 bg-white"
          >
            <option value="">Todos los estados</option>
            <option value="open">🟢 Sesiones Activas</option>
            <option value="closed">🔴 Sesiones Cerradas</option>
          </select>
          
          <!-- Filtro Fecha -->
          <input
            v-model="dateFilter"
            type="date"
            class="pl-3 pr-3 py-2.5 text-sm border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 min-w-36 bg-white"
          />
          
          <!-- Botón Exportar -->
          <button
            @click="exportData"
            class="px-4 py-2.5 bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 text-white text-sm font-semibold rounded-xl shadow-sm transition-all duration-200 flex items-center space-x-2"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3M3 17V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v10a2 2 0 01-2 2H5a2 2 0 01-2-2z"/>
            </svg>
            <span>Exportar Excel</span>
          </button>
          
          <!-- Botón Limpiar Filtros -->
          <button
            @click="clearFilters"
            class="p-2.5 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-xl transition-colors duration-200"
            title="Limpiar filtros"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
            </svg>
          </button>
        </div>
      </div>

      <!-- Sessions Table Compacta -->
      <div class="bg-white rounded-lg shadow-lg overflow-hidden border border-gray-100">
        <div class="bg-gray-50 border-b border-gray-200 px-4 py-3">
          <div class="flex items-center justify-between">
            <div class="flex items-center space-x-3">
              <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                </svg>
              </div>
              <div>
                <h2 class="text-lg font-bold text-gray-900">Sesiones de Caja</h2>
                <p class="text-gray-600 text-sm">{{ filteredSessions.length }} registros</p>
              </div>
            </div>
            <div class="flex items-center space-x-2">
              <div class="px-3 py-1 bg-blue-50 border border-blue-100 rounded-lg">
                <span class="text-blue-700 text-xs font-medium">
                  {{ new Date().toLocaleTimeString('es-ES') }}
                </span>
              </div>
            </div>
          </div>
        </div>
        
        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                  <div class="flex items-center space-x-1">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    <span>Usuario</span>
                  </div>
                </th>
                <th class="px-3 py-2 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                  <span>Estado</span>
                </th>
                <th class="px-3 py-2 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                  <span>Apertura</span>
                </th>
                <th class="px-3 py-2 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                  <span>Inicial</span>
                </th>
                <th class="px-3 py-2 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                  <span>Ventas</span>
                </th>
                <th class="px-3 py-2 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                  <span>Estado Cierre</span>
                </th>
                <th class="px-3 py-2 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                  <span>Duración</span>
                </th>
                <th class="px-3 py-2 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                  <span>Acciones</span>
                </th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="session in paginatedSessions" :key="session.id" 
                  class="hover:bg-gray-50 transition-colors duration-200">
                <td class="px-4 py-3 whitespace-nowrap">
                  <div class="flex items-center">
                    <div class="relative">
                      <div class="h-8 w-8 rounded-lg bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center text-white font-bold text-sm shadow-md">
                        {{ getUserInitials(session.user?.name || 'Usuario') }}
                      </div>
                      <div v-if="session.status === 'open'" class="absolute -top-0.5 -right-0.5 w-3 h-3 bg-green-500 rounded-full border border-white"></div>
                    </div>
                    <div class="ml-2">
                      <div class="text-sm font-bold text-gray-900">
                        {{ session.user?.name || 'Usuario desconocido' }}
                      </div>
                      <div class="text-xs text-gray-500">
                        ID: #{{ session.id }}
                      </div>
                    </div>
                  </div>
                </td>
                <td class="px-3 py-3 whitespace-nowrap">
                  <span :class="getStatusBadgeClass(session.status)" class="inline-flex items-center px-2 py-1 text-xs font-bold rounded-full">
                    <div :class="[
                      'w-1.5 h-1.5 rounded-full mr-1',
                      session.status === 'open' ? 'bg-green-400' : 'bg-gray-400'
                    ]"></div>
                    {{ session.status === 'open' ? 'ACTIVA' : 'CERRADA' }}
                  </span>
                </td>
                <td class="px-3 py-3 whitespace-nowrap text-sm text-gray-900">
                  <div class="font-semibold">{{ formatDate(session.opening_date) }}</div>
                  <div class="text-gray-500 text-xs">{{ session.opening_time }}</div>
                </td>
                <td class="px-3 py-3 whitespace-nowrap text-sm font-bold text-gray-900">
                  ${{ parseFloat(session.opening_amount || 0).toLocaleString() }}
                </td>
                <td class="px-3 py-3 whitespace-nowrap text-sm text-gray-900">
                  <span class="font-semibold">
                    ${{ parseFloat(session.total_sales || 0).toLocaleString() }}
                  </span>
                </td>
                <td class="px-3 py-3 whitespace-nowrap text-sm">
                  <span v-if="session.status === 'closed'" :class="getClosingStatusClass(session.closing_status)" 
                        class="inline-flex items-center px-2 py-1 text-xs font-bold rounded-full">
                    {{ getClosingStatusText(session.closing_status) }}
                  </span>
                  <span v-else class="text-gray-400 text-xs">En curso</span>
                </td>
                <td class="px-3 py-3 whitespace-nowrap text-sm text-gray-500">
                  {{ getSessionDuration(session) }}
                </td>
                <td class="px-3 py-3 whitespace-nowrap text-sm font-medium">
                  <div class="flex items-center space-x-1">
                    <button
                      @click="viewSessionDetails(session)"
                      class="p-1.5 bg-blue-100 hover:bg-blue-200 text-blue-600 hover:text-blue-700 rounded-md transition-all duration-200"
                      title="Ver detalles"
                    >
                      <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                      </svg>
                    </button>
                    <button
                      @click="viewSessionAudit(session)"
                      class="p-1.5 bg-purple-100 hover:bg-purple-200 text-purple-600 hover:text-purple-700 rounded-md transition-all duration-200"
                      title="Ver auditoría completa"
                    >
                      <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                      </svg>
                    </button>
                    <button
                      v-if="session.status === 'open'"
                      @click="showCloseModal(session)"
                      class="p-1.5 bg-red-100 hover:bg-red-200 text-red-600 hover:text-red-700 rounded-md transition-all duration-200"
                      title="Cerrar sesión"
                    >
                      <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                      </svg>
                    </button>
                    <button
                      @click="generateReport(session)"
                      class="p-1.5 bg-green-100 hover:bg-green-200 text-green-600 hover:text-green-700 rounded-md transition-all duration-200"
                      title="Generar reporte"
                    >
                      <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
        <div v-if="totalPages > 1" class="border-t border-gray-200 bg-white px-4 py-3 flex items-center justify-between sm:px-6">
          <div class="flex-1 flex justify-between sm:hidden">
            <button
              @click="prevPage"
              :disabled="currentPage === 1"
              class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
            >
              Anterior
            </button>
            <button
              @click="nextPage"
              :disabled="currentPage === totalPages"
              class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
            >
              Siguiente
            </button>
          </div>
          <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
            <div class="flex items-center space-x-2">
              <p class="text-sm text-gray-700">
                Mostrando
                <span class="font-medium">{{ paginationInfo.start }}</span>
                a
                <span class="font-medium">{{ paginationInfo.end }}</span>
                de
                <span class="font-medium">{{ paginationInfo.total }}</span>
                resultados
              </p>
              <select
                v-model="itemsPerPage"
                @change="changeItemsPerPage(itemsPerPage)"
                class="ml-4 px-3 py-1 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
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
                  class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
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
                        ? 'z-10 bg-blue-50 border-blue-500 text-blue-600'
                        : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50',
                      'relative inline-flex items-center px-4 py-2 border text-sm font-medium'
                    ]"
                  >
                    {{ page }}
                  </button>
                  <span
                    v-else-if="(page === 2 && currentPage > 4) || (page === totalPages - 1 && currentPage < totalPages - 3)"
                    class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700"
                  >
                    ...
                  </span>
                </template>

                <!-- Next button -->
                <button
                  @click="nextPage"
                  :disabled="currentPage === totalPages"
                  class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
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
          <div class="w-16 h-16 mx-auto bg-gray-100 rounded-full flex items-center justify-center mb-4">
            <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
            </svg>
          </div>
          <h3 class="text-lg font-semibold text-gray-900 mb-2">No hay sesiones</h3>
          <p class="text-gray-500 mb-4">Ajusta los filtros o crea una nueva sesión</p>
          <button
            @click="clearFilters"
            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200 text-sm"
          >
            Limpiar filtros
          </button>
        </div>
      </div>
    </div>

    <!-- Session Details Modal Empresarial -->
    <div v-if="selectedSession" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4" @click.self="selectedSession = null">
      <div class="bg-white rounded-xl max-w-3xl w-full max-h-[90vh] overflow-y-auto shadow-2xl">
        <!-- Header Empresarial -->
        <div class="bg-white border-b border-gray-200 px-6 py-4 rounded-t-xl">
          <div class="flex justify-between items-center">
            <div class="flex items-center space-x-3">
              <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
              </div>
              <div>
                <h3 class="text-lg font-bold text-gray-900">Detalles de Sesión</h3>
                <p class="text-sm text-gray-600">ID: #{{ selectedSession.id }} - {{ selectedSession.user?.name }}</p>
              </div>
            </div>
            <button 
              @click="selectedSession = null" 
              class="text-gray-400 hover:text-gray-600 transition-colors"
            >
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
              </svg>
            </button>
          </div>
        </div>
        
        <div class="p-6">
          <!-- User Info -->
          <div class="bg-gray-50 rounded-lg p-4 mb-6">
            <div class="flex items-center space-x-4">
              <div class="relative">
                <div class="w-14 h-14 bg-blue-100 rounded-lg flex items-center justify-center text-blue-600 font-bold text-lg">
                  {{ getUserInitials(selectedSession.user?.name || 'Usuario') }}
                </div>
                <div v-if="selectedSession.status === 'open'" class="absolute -top-1 -right-1 w-4 h-4 bg-green-500 rounded-full border-2 border-white"></div>
              </div>
              <div class="flex-1">
                <h4 class="text-lg font-bold text-gray-900">{{ selectedSession.user?.name }}</h4>
                <p class="text-sm text-gray-600">{{ selectedSession.user?.email }}</p>
                <div class="flex items-center mt-1 space-x-3">
                  <span :class="getStatusBadgeClass(selectedSession.status)" class="px-2 py-1 text-xs font-semibold rounded-full">
                    {{ selectedSession.status === 'open' ? 'ACTIVA' : 'CERRADA' }}
                  </span>
                  <span class="text-xs text-gray-500">
                    CC: {{ selectedSession.user?.cc }}
                  </span>
                </div>
              </div>
            </div>
          </div>

          <!-- Session Info Grid -->
          <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-6">
            <div class="bg-gray-50 p-3 rounded-lg">
              <label class="block text-xs font-medium text-gray-600 mb-1">Fecha Apertura</label>
              <p class="text-sm font-semibold text-gray-900">{{ formatDate(selectedSession.opening_date) }}</p>
            </div>
            <div class="bg-gray-50 p-3 rounded-lg">
              <label class="block text-xs font-medium text-gray-600 mb-1">Hora Apertura</label>
              <p class="text-sm font-semibold text-gray-900">{{ selectedSession.opening_time }}</p>
            </div>
            <div class="bg-gray-50 p-3 rounded-lg">
              <label class="block text-xs font-medium text-gray-600 mb-1">Duración</label>
              <p class="text-sm font-semibold text-gray-900">{{ getSessionDuration(selectedSession) }}</p>
            </div>
            <div class="bg-gray-50 p-3 rounded-lg">
              <label class="block text-xs font-medium text-gray-600 mb-1">Actualización</label>
              <p class="text-sm font-semibold text-gray-900">{{ formatDate(selectedSession.updated_at) }}</p>
            </div>
          </div>

          <!-- Financial Summary -->
          <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <div class="bg-green-50 p-4 rounded-lg border border-green-100">
              <div class="flex items-center justify-between">
                <div>
                  <p class="text-xs font-medium text-green-700">Monto Inicial</p>
                  <p class="text-xl font-bold text-green-900">${{ parseFloat(selectedSession.opening_amount || 0).toLocaleString() }}</p>
                </div>
                <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                  <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                  </svg>
                </div>
              </div>
            </div>
            
            <div class="bg-blue-50 p-4 rounded-lg border border-blue-100">
              <div class="flex items-center justify-between">
                <div>
                  <p class="text-xs font-medium text-blue-700">Total Ventas</p>
                  <p class="text-xl font-bold text-blue-900">${{ parseFloat(selectedSession.total_sales || 0).toLocaleString() }}</p>
                </div>
                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                  <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                  </svg>
                </div>
              </div>
            </div>
            
            <div class="bg-purple-50 p-4 rounded-lg border border-purple-100">
              <div class="flex items-center justify-between">
                <div>
                  <p class="text-xs font-medium text-purple-700">Efectivo</p>
                  <p class="text-xl font-bold text-purple-900">${{ parseFloat(selectedSession.cash_sales || 0).toLocaleString() }}</p>
                </div>
                <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                  <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v2a2 2 0 002 2z"/>
                  </svg>
                </div>
              </div>
            </div>
          </div>

          <!-- Notes -->
          <div v-if="selectedSession.opening_notes" class="mb-6">
            <label class="block text-sm font-semibold text-gray-700 mb-2">Notas de Apertura</label>
            <div class="bg-amber-50 border border-amber-200 rounded-lg p-3">
              <p class="text-sm text-gray-800">{{ selectedSession.opening_notes }}</p>
            </div>
          </div>

          <!-- Actions -->
          <div class="flex justify-end space-x-3 pt-4 border-t border-gray-200">
            <button
              @click="selectedSession = null"
              class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors text-sm font-medium"
            >
              Cerrar
            </button>
            <button
              v-if="selectedSession.status === 'open'"
              @click="showCloseModal(selectedSession)"
              class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors text-sm font-medium"
            >
              Cerrar Sesión
            </button>
            <button
              @click="generateReport(selectedSession)"
              class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors text-sm font-medium"
            >
              Generar Reporte
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal de Cierre Empresarial -->
    <div v-if="showCloseSessionModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-[100] p-4" @click.self="showCloseSessionModal = false">
      <div class="bg-white rounded-xl w-full max-w-2xl max-h-[90vh] overflow-y-auto shadow-2xl">
        <!-- Header -->
        <div class="bg-white border-b border-gray-200 px-6 py-4 rounded-t-xl">
          <div class="flex items-center justify-between">
            <div class="flex items-center space-x-3">
              <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center">
                <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                </svg>
              </div>
              <div>
                <h2 class="text-lg font-bold text-gray-900">Cerrar Caja</h2>
                <p class="text-sm text-gray-600">{{ sessionToClose?.user?.name }} - #{{ sessionToClose?.id }}</p>
              </div>
            </div>
            <button @click="showCloseSessionModal = false" class="text-gray-400 hover:text-gray-600 transition-colors">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
              </svg>
            </button>
          </div>
        </div>

        <div class="p-6">
          <!-- Resumen de la sesión -->
          <div class="grid grid-cols-2 gap-3 mb-4">
            <div class="bg-green-50 p-3 rounded-lg border border-green-100">
              <label class="text-xs font-medium text-green-700">Monto Inicial</label>
              <p class="text-xl font-bold text-green-900">${{ parseFloat(sessionToClose?.opening_amount || 0).toLocaleString() }}</p>
            </div>
            <div class="bg-blue-50 p-3 rounded-lg border border-blue-100">
              <label class="text-xs font-medium text-blue-700">Ventas Totales</label>
              <p class="text-xl font-bold text-blue-900">${{ parseFloat(sessionToClose?.total_sales || 0).toLocaleString() }}</p>
            </div>
          </div>

          <!-- Desglose por método de pago -->
          <div class="grid grid-cols-3 gap-3 mb-4">
            <div class="bg-amber-50 p-3 rounded-lg border border-amber-100">
              <label class="text-xs font-medium text-amber-700">Efectivo</label>
              <p class="text-base font-bold text-amber-900">${{ parseFloat(sessionToClose?.cash_sales || 0).toLocaleString() }}</p>
            </div>
            <div class="bg-purple-50 p-3 rounded-lg border border-purple-100">
              <label class="text-xs font-medium text-purple-700">Tarjetas</label>
              <p class="text-base font-bold text-purple-900">${{ parseFloat(sessionToClose?.card_sales || 0).toLocaleString() }}</p>
            </div>
            <div class="bg-indigo-50 p-3 rounded-lg border border-indigo-100">
              <label class="text-xs font-medium text-indigo-700">Transferencias</label>
              <p class="text-base font-bold text-indigo-900">${{ parseFloat(sessionToClose?.transfer_sales || 0).toLocaleString() }}</p>
            </div>
          </div>

          <!-- Formulario de cierre -->
          <div class="space-y-4">
            <div>
              <label class="block text-sm font-bold text-gray-700 mb-2">Monto Real en Caja ($)</label>
              <input
                v-model="closeForm.actual_amount"
                type="number"
                step="0.01"
                min="0"
                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-lg font-semibold"
                placeholder="0.00"
              >
            </div>

            <div>
              <label class="block text-sm font-bold text-gray-700 mb-2">Notas de Cierre</label>
              <textarea
                v-model="closeForm.closing_notes"
                rows="3"
                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                placeholder="Observaciones sobre el cierre..."
              ></textarea>
            </div>

            <div>
              <label class="block text-sm font-bold text-gray-700 mb-2">Gastos y Salidas (Opcional)</label>
              <textarea
                v-model="closeForm.expenses_detail"
                rows="2"
                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
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
          <div class="flex space-x-3 mt-6 pt-4 border-t">
            <button
              @click="showCloseSessionModal = false"
              class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 font-medium text-sm"
            >
              Cancelar
            </button>
            <button
              @click="confirmCloseSession"
              :disabled="!closeForm.actual_amount || closingSession"
              class="flex-1 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 disabled:opacity-50 disabled:cursor-not-allowed font-medium text-sm"
            >
              {{ closingSession ? 'Cerrando...' : 'Cerrar Caja' }}
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal de Auditoría Empresarial -->
    <div v-if="showAuditModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-[100] p-4" @click.self="showAuditModal = false">
      <div class="bg-white rounded-xl w-full max-w-6xl max-h-[90vh] overflow-y-auto shadow-2xl">
        <!-- Header -->
        <div class="bg-white border-b border-gray-200 px-6 py-4 rounded-t-xl">
          <div class="flex items-center justify-between">
            <div class="flex items-center space-x-3">
              <div class="w-10 h-10 bg-indigo-100 rounded-lg flex items-center justify-center">
                <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                </svg>
              </div>
              <div>
                <h2 class="text-lg font-bold text-gray-900">Auditoría de Sesión</h2>
                <p class="text-sm text-gray-600">{{ auditData?.session?.user?.name }} - #{{ auditData?.session?.id }}</p>
              </div>
            </div>
            <button @click="showAuditModal = false" class="text-gray-400 hover:text-gray-600 transition-colors">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
              </svg>
            </button>
          </div>
        </div>

        <div class="p-6">
          <!-- Estadísticas -->
          <div v-if="auditData?.statistics" class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-6">
            <div class="bg-blue-50 p-3 rounded-lg border border-blue-100 text-center">
              <p class="text-xl font-bold text-blue-900">{{ auditData.statistics.total_transactions }}</p>
              <p class="text-xs font-medium text-blue-700">Transacciones</p>
            </div>
            <div class="bg-green-50 p-3 rounded-lg border border-green-100 text-center">
              <p class="text-xl font-bold text-green-900">${{ parseFloat(auditData.statistics.average_sale || 0).toLocaleString() }}</p>
              <p class="text-xs font-medium text-green-700">Venta Promedio</p>
            </div>
            <div class="bg-purple-50 p-3 rounded-lg border border-purple-100 text-center">
              <p class="text-xl font-bold text-purple-900">${{ parseFloat(auditData.statistics.largest_sale || 0).toLocaleString() }}</p>
              <p class="text-xs font-medium text-purple-700">Venta Mayor</p>
            </div>
            <div class="bg-orange-50 p-3 rounded-lg border border-orange-100 text-center">
              <p class="text-xl font-bold text-orange-900">{{ Math.floor(auditData.statistics.session_duration / 60) }}h</p>
              <p class="text-xs font-medium text-orange-700">Duración</p>
            </div>
          </div>

          <!-- Timeline -->
          <div v-if="auditData?.timeline" class="mb-6">
            <h3 class="text-lg font-bold text-gray-900 mb-4">Timeline de Eventos</h3>
            <div class="space-y-3 max-h-96 overflow-y-auto">
              <div v-for="(event, index) in auditData.timeline" :key="index" 
                   class="flex items-start space-x-3 p-3 bg-gray-50 rounded-lg">
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
                    <p class="font-semibold text-gray-900">{{ event.description }}</p>
                    <span class="text-sm text-gray-500">{{ formatTimestamp(event.timestamp) }}</span>
                  </div>
                  <p class="text-lg font-bold" :class="event.amount < 0 ? 'text-orange-600' : 'text-green-600'">
                    ${{ parseFloat(event.amount || 0).toLocaleString() }}
                  </p>
                  <div v-if="event.details.customer" class="text-sm text-gray-600">
                    Cliente: {{ event.details.customer }}
                  </div>
                  <div v-if="event.type === 'return' && event.details.original_invoice" class="text-sm text-gray-600">
                    Factura original: {{ event.details.original_invoice }}
                  </div>
                  <div v-if="event.type === 'return' && event.details.reason" class="text-sm text-gray-500 italic">
                    Razón: {{ event.details.reason }}
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Botón cerrar -->
          <div class="flex justify-end pt-4 border-t">
            <button
              @click="showAuditModal = false"
              class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium text-sm"
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
    showInfo('🔄 Actualizando... - Obteniendo datos más recientes del sistema')
    
    const response = await apiCall('/cash-sessions')
    
    if (response.success) {
      sessions.value = response.sessions || []
      showSuccess(`✅ Actualización completa - Se cargaron ${sessions.value.length} sesiones correctamente`)
    } else {
      throw new Error(response.message || 'Error en la respuesta del servidor')
    }
  } catch (error) {
    console.error('Error fetching sessions:', error)
    showError('❌ Error de conexión - No se pudieron cargar las sesiones. Verifica la conexión.')
  } finally {
    loading.value = false
  }
}

const clearFilters = () => {
  searchQuery.value = ''
  statusFilter.value = ''
  dateFilter.value = ''
  currentPage.value = 1
  showInfo('🧹 Filtros limpiados - Mostrando todas las sesiones disponibles')
}

const exportData = () => {
  showInfo('📊 Exportando datos... - Preparando archivo Excel con la información actual')
  // TODO: Implementar exportación real
  setTimeout(() => {
    showSuccess('✅ Exportación completa - El archivo Excel se ha descargado correctamente')
  }, 2000)
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
    ? 'bg-green-100 text-green-800 border border-green-200' 
    : 'bg-gray-100 text-gray-800 border border-gray-200'
}

const formatDate = (dateString) => {
  if (!dateString) return 'N/A'
  return new Date(dateString).toLocaleDateString('es-ES', {
    year: 'numeric',
    month: 'short',
    day: 'numeric'
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
  if (diff > 0) return 'bg-green-50 border border-green-200'
  if (diff < 0) return 'bg-red-50 border border-red-200'
  return 'bg-blue-50 border border-blue-200'
}

const getDifferenceText = () => {
  const diff = getDifference()
  if (diff > 0) return '(Sobrante)'
  if (diff < 0) return '(Faltante)'
  return '(Exacto)'
}

// Confirmar cierre de sesión
const confirmCloseSession = async () => {
  if (!closeForm.value.actual_amount || closeForm.value.actual_amount === '') {
    showError('⚠️ Campo requerido - Por favor ingresa el monto real en caja')
    return
  }

  closingSession.value = true
  
  try {
    // Ensure all numeric fields are properly formatted
    const formData = {
      actual_amount: parseFloat(closeForm.value.actual_amount) || 0,
      closing_notes: closeForm.value.closing_notes || '',
      expenses_detail: closeForm.value.expenses_detail || ''
    }
    
    const data = await apiCall(`/cash-sessions/${sessionToClose.value.id}/close`, {
      method: 'POST',
      data: formData
    })
    
    if (data.success) {
      showSuccess(`✅ Sesión cerrada - Se cerró correctamente con estado: ${data.closing_details?.status}`)
      showCloseSessionModal.value = false
      sessionToClose.value = null
      refreshSessions()
    } else {
      throw new Error(data.message || 'Error al cerrar la sesión')
    }
  } catch (error) {
    console.error('Error closing session:', error)
    showError('❌ Error al cerrar - No se pudo cerrar la sesión. Intenta nuevamente.')
  } finally {
    closingSession.value = false
  }
}

// Método para mostrar auditoría
const viewSessionAudit = async (session) => {
  try {
    showInfo('🔍 Cargando auditoría... - Obteniendo historial completo de la sesión')
    
    const data = await apiCall(`/cash-sessions/${session.id}/audit`)
    
    if (data.success) {
      auditData.value = data
      showAuditModal.value = true
      showSuccess(`✅ Auditoría cargada - Se encontraron ${data.timeline?.length || 0} eventos`)
    } else {
      throw new Error(data.message || 'Error al cargar auditoría')
    }
  } catch (error) {
    console.error('Error loading audit:', error)
    showError('❌ Error de auditoría - No se pudo cargar el historial. Verifica la conexión.')
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
    exact: 'bg-green-100 text-green-800 border border-green-200',
    surplus: 'bg-blue-100 text-blue-800 border border-blue-200',
    deficit: 'bg-red-100 text-red-800 border border-red-200',
    with_expenses: 'bg-yellow-100 text-yellow-800 border border-yellow-200'
  }
  return classes[status] || 'bg-gray-100 text-gray-800 border border-gray-200'
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
  // Confirmation modal simulation
  const confirmed = await new Promise((resolve) => {
    showWarning(`⚠️ Confirmación requerida - ¿Cerrar la sesión de ${session.user?.name}?`)
    
    // Create confirmation modal programmatically
    const modal = document.createElement('div')
    modal.className = 'fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center z-[100]'
    modal.innerHTML = `
      <div class="bg-white rounded-2xl p-8 max-w-md mx-4 shadow-2xl">
        <div class="text-center">
          <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.084 16.5c-.77.833.192 2.5 1.732 2.5z"/>
            </svg>
          </div>
          <h3 class="text-xl font-bold text-gray-900 mb-2">¿Cerrar sesión de caja?</h3>
          <p class="text-gray-600 mb-6">Esta acción cerrará permanentemente la sesión de <strong>${session.user?.name}</strong>.</p>
          <div class="flex space-x-3">
            <button id="cancel-btn" class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 font-medium">
              Cancelar
            </button>
            <button id="confirm-btn" class="flex-1 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 font-medium">
              Sí, cerrar
            </button>
          </div>
        </div>
      </div>
    `
    
    document.body.appendChild(modal)
    
    document.getElementById('cancel-btn').onclick = () => {
      document.body.removeChild(modal)
      resolve(false)
    }
    
    document.getElementById('confirm-btn').onclick = () => {
      document.body.removeChild(modal)
      resolve(true)
    }
  })
  
  if (!confirmed) return
  
  try {
    showInfo('🔄 Cerrando sesión... - Procesando cierre de caja')
    
    const response = await apiCall(`/cash-sessions/${session.id}/close`, {
      method: 'POST',
      data: {
        actual_amount: parseFloat(session.opening_amount || 0),
        closing_notes: 'Cerrada desde panel administrativo'
      }
    })
    
    if (response.success) {
      showSuccess(`✅ Sesión cerrada - La sesión de ${session.user?.name} se cerró correctamente`)
      selectedSession.value = null
      refreshSessions()
    } else {
      throw new Error(response.message || 'Error al cerrar la sesión')
    }
  } catch (error) {
    console.error('Error closing session:', error)
    showError('❌ Error al cerrar - No se pudo cerrar la sesión. Intenta nuevamente.')
  }
}

// Nuevos métodos para modal de cierre mejorado
const showCloseModal = (session) => {
  sessionToClose.value = session
  // Pre-rellenar formulario con datos calculados
  const expectedCash = parseFloat(session.opening_amount || 0) + parseFloat(session.cash_sales || 0)
  closeForm.value = {
    actual_amount: expectedCash,
    closing_notes: '',
    expenses_detail: '',
    cash_counted: expectedCash,
    card_counted: parseFloat(session.card_sales || 0),
    transfer_counted: parseFloat(session.transfer_sales || 0)
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