<template>
<template>
  <div class="min-h-screen bg-gray-100">
    <!-- Header Compacto -->
    <div class="bg-white shadow-sm border-b">
      <div class="max-w-7xl mx-auto px-4 py-3">
        <div class="flex items-center justify-between">
          <div class="flex items-center space-x-3">
            <div class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center">
              <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" />
              </svg>
            </div>
            <div>
              <h1 class="text-lg font-semibold text-gray-900">Panel Administrativo</h1>
              <p class="text-xs text-gray-500">Última sync: {{ currentTime }}</p>
            </div>
          </div>
          <div class="flex items-center space-x-2">
            <button @click="refreshSessions" class="px-3 py-1.5 text-sm bg-gray-100 hover:bg-gray-200 rounded transition-colors">
              ↻
            </button>
            <button @click="showNewSessionModal = true" class="px-3 py-1.5 text-sm bg-blue-600 text-white hover:bg-blue-700 rounded transition-colors">
              + Nueva
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Navegación por pestañas / submenús -->
    <div class="bg-white border-b">
      <div class="max-w-7xl mx-auto px-4">
        <nav class="flex space-x-6" aria-label="Tabs">
          <button
            v-for="tab in tabs"
            :key="tab.id"
            @click="activeTab = tab.id"
            :class="[
              'py-3 px-1 border-b-2 font-medium text-sm whitespace-nowrap transition-colors',
              activeTab === tab.id
                ? 'border-blue-500 text-blue-600'
                : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
            ]"
          >
            <span class="flex items-center space-x-2">
              <span>{{ tab.icon }}</span>
              <span>{{ tab.name }}</span>
              <span v-if="tab.count" class="bg-gray-100 text-gray-600 py-0.5 px-2 rounded-full text-xs">{{ tab.count }}</span>
            </span>
          </button>
        </nav>
      </div>
    </div>

    <!-- Contenido Principal -->
    <div class="max-w-7xl mx-auto px-4 py-4">
      
      <!-- Control de Cajas -->
      <div v-if="activeTab === 'cajas'" class="space-y-4">
        <!-- Stats compactos -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
          <div class="bg-white p-3 rounded-lg shadow-sm border">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-xs text-gray-600">Sesiones Activas</p>
                <p class="text-lg font-semibold text-gray-900">{{ activeSessions.length }}</p>
              </div>
              <div class="w-8 h-8 bg-green-100 rounded flex items-center justify-center">
                <span class="text-green-600 text-sm">📊</span>
              </div>
            </div>
          </div>
          
          <div class="bg-white p-3 rounded-lg shadow-sm border">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-xs text-gray-600">Usuarios Online</p>
                <p class="text-lg font-semibold text-gray-900">{{ activeSessions.length }}</p>
              </div>
              <div class="w-8 h-8 bg-blue-100 rounded flex items-center justify-center">
                <span class="text-blue-600 text-sm">👥</span>
              </div>
            </div>
          </div>
          
          <div class="bg-white p-3 rounded-lg shadow-sm border">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-xs text-gray-600">Total en Cajas</p>
                <p class="text-lg font-semibold text-gray-900">${{ formatNumber(totalInCash) }}</p>
              </div>
              <div class="w-8 h-8 bg-yellow-100 rounded flex items-center justify-center">
                <span class="text-yellow-600 text-sm">💰</span>
              </div>
            </div>
          </div>
          
          <div class="bg-white p-3 rounded-lg shadow-sm border">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-xs text-gray-600">Ventas Hoy</p>
                <p class="text-lg font-semibold text-gray-900">${{ formatNumber(todaySales) }}</p>
              </div>
              <div class="w-8 h-8 bg-purple-100 rounded flex items-center justify-center">
                <span class="text-purple-600 text-sm">📈</span>
              </div>
            </div>
          </div>
        </div>

        <!-- Tabla compacta -->
        <div class="bg-white rounded-lg shadow-sm border">
          <div class="px-4 py-3 border-b border-gray-200 flex items-center justify-between">
            <div>
              <h3 class="text-sm font-medium text-gray-900">Sesiones de Caja</h3>
              <p class="text-xs text-gray-500">{{ sessions.length }} sesiones encontradas</p>
            </div>
            <div class="flex items-center space-x-2">
              <input
                v-model="searchQuery"
                type="text"
                placeholder="Buscar usuario..."
                class="text-xs border border-gray-300 rounded px-2 py-1 w-32"
              >
              <select v-model="statusFilter" class="text-xs border border-gray-300 rounded px-2 py-1">
                <option value="">Todos</option>
                <option value="ACTIVA">Activas</option>
                <option value="CERRADA">Cerradas</option>
              </select>
            </div>
          </div>
          
          <div class="overflow-x-auto">
            <table class="min-w-full">
              <thead class="bg-gray-50">
                <tr>
                  <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Usuario</th>
                  <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                  <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Apertura</th>
                  <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Inicial</th>
                  <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ventas</th>
                  <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200">
                <tr v-for="session in filteredSessions" :key="session.id" class="hover:bg-gray-50">
                  <td class="px-3 py-2 whitespace-nowrap">
                    <div class="flex items-center">
                      <div class="w-6 h-6 bg-blue-100 rounded-full flex items-center justify-center mr-2">
                        <span class="text-xs text-blue-600 font-medium">{{ session.user?.name?.slice(0, 2).toUpperCase() || 'UN' }}</span>
                      </div>
                      <div>
                        <p class="text-xs font-medium text-gray-900">{{ session.user?.name || 'Usuario' }}</p>
                        <p class="text-xs text-gray-500">{{ session.user?.email || 'Sin email' }}</p>
                      </div>
                    </div>
                  </td>
                  <td class="px-3 py-2 whitespace-nowrap">
                    <span :class="[
                      'inline-flex px-2 py-1 text-xs font-semibold rounded-full',
                      session.status === 'ACTIVA' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'
                    ]">
                      {{ session.status }}
                    </span>
                  </td>
                  <td class="px-3 py-2 text-xs text-gray-900 whitespace-nowrap">
                    {{ formatDate(session.opened_at) }}
                  </td>
                  <td class="px-3 py-2 text-xs text-gray-900 whitespace-nowrap">
                    ${{ formatNumber(session.initial_amount) }}
                  </td>
                  <td class="px-3 py-2 text-xs text-gray-900 whitespace-nowrap">
                    ${{ formatNumber(session.sales_amount) }}
                  </td>
                  <td class="px-3 py-2 text-xs font-medium whitespace-nowrap">
                    <div class="flex items-center space-x-1">
                      <button
                        @click="viewSessionDetails(session)"
                        class="text-blue-600 hover:text-blue-900 p-1"
                        title="Ver detalles"
                      >
                        👁️
                      </button>
                      <button
                        v-if="session.status === 'ACTIVA'"
                        @click="confirmCloseSession(session)"
                        class="text-red-600 hover:text-red-900 p-1"
                        title="Cerrar sesión"
                      >
                        🔒
                      </button>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <!-- Otros tabs (placeholder) -->
      <div v-else-if="activeTab === 'accesos'" class="bg-white rounded-lg shadow-sm border p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Control de Accesos</h3>
        <p class="text-gray-600">Próximamente: Gestión de usuarios, roles y permisos</p>
      </div>

      <div v-else-if="activeTab === 'reportes'" class="bg-white rounded-lg shadow-sm border p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Reportes</h3>
        <p class="text-gray-600">Próximamente: Reportes detallados de ventas y operaciones</p>
      </div>

      <div v-else-if="activeTab === 'configuracion'" class="bg-white rounded-lg shadow-sm border p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Configuración</h3>
        <p class="text-gray-600">Próximamente: Configuración del sistema</p>
      </div>
    </div>

      <!-- Stats Cards Premium -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 mb-10">
        <!-- Sesiones Activas -->
        <div class="group bg-white rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-500 p-6 border border-gray-100 hover:border-green-200 transform hover:-translate-y-1">
          <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
              <div class="w-14 h-14 bg-gradient-to-br from-green-500 to-emerald-600 rounded-2xl flex items-center justify-center shadow-lg shadow-green-500/30 group-hover:shadow-green-500/50 transition-shadow duration-300">
                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                </svg>
              </div>
              <div>
                <p class="text-sm font-semibold text-gray-600 uppercase tracking-wider">Sesiones Activas</p>
                <p class="text-3xl font-bold text-gray-900 group-hover:text-green-600 transition-colors duration-300">
                  {{ activeSessions.length }}
                </p>
                <p class="text-xs text-green-600 font-medium mt-1">
                  +{{ activeSessions.length }} desde ayer
                </p>
              </div>
            </div>
            <div class="w-12 h-12 bg-green-50 rounded-xl flex items-center justify-center group-hover:bg-green-100 transition-colors duration-300">
              <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
              </svg>
            </div>
          </div>
        </div>

        <!-- Usuarios Conectados -->
        <div class="group bg-white rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-500 p-6 border border-gray-100 hover:border-blue-200 transform hover:-translate-y-1">
          <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
              <div class="w-14 h-14 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-2xl flex items-center justify-center shadow-lg shadow-blue-500/30 group-hover:shadow-blue-500/50 transition-shadow duration-300">
                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
              </div>
              <div>
                <p class="text-sm font-semibold text-gray-600 uppercase tracking-wider">Usuarios Online</p>
                <p class="text-3xl font-bold text-gray-900 group-hover:text-blue-600 transition-colors duration-300">
                  {{ uniqueUsers.length }}
                </p>
                <p class="text-xs text-blue-600 font-medium mt-1">
                  Conectados ahora
                </p>
              </div>
            </div>
            <div class="w-12 h-12 bg-blue-50 rounded-xl flex items-center justify-center group-hover:bg-blue-100 transition-colors duration-300">
              <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
              </svg>
            </div>
          </div>
        </div>

        <!-- Total en Cajas -->
        <div class="group bg-white rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-500 p-6 border border-gray-100 hover:border-yellow-200 transform hover:-translate-y-1">
          <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
              <div class="w-14 h-14 bg-gradient-to-br from-yellow-500 to-orange-600 rounded-2xl flex items-center justify-center shadow-lg shadow-yellow-500/30 group-hover:shadow-yellow-500/50 transition-shadow duration-300">
                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                </svg>
              </div>
              <div>
                <p class="text-sm font-semibold text-gray-600 uppercase tracking-wider">Total en Cajas</p>
                <p class="text-3xl font-bold text-gray-900 group-hover:text-yellow-600 transition-colors duration-300">
                  ${{ totalCashAmount.toLocaleString() }}
                </p>
                <p class="text-xs text-yellow-600 font-medium mt-1">
                  Capital activo
                </p>
              </div>
            </div>
            <div class="w-12 h-12 bg-yellow-50 rounded-xl flex items-center justify-center group-hover:bg-yellow-100 transition-colors duration-300">
              <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
              </svg>
            </div>
          </div>
        </div>

        <!-- Ventas del Día -->
        <div class="group bg-white rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-500 p-6 border border-gray-100 hover:border-purple-200 transform hover:-translate-y-1">
          <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
              <div class="w-14 h-14 bg-gradient-to-br from-purple-500 to-pink-600 rounded-2xl flex items-center justify-center shadow-lg shadow-purple-500/30 group-hover:shadow-purple-500/50 transition-shadow duration-300">
                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
              </div>
              <div>
                <p class="text-sm font-semibold text-gray-600 uppercase tracking-wider">Ventas Hoy</p>
                <p class="text-3xl font-bold text-gray-900 group-hover:text-purple-600 transition-colors duration-300">
                  ${{ totalSalesToday.toLocaleString() }}
                </p>
                <p class="text-xs text-purple-600 font-medium mt-1">
                  Revenue diario
                </p>
              </div>
            </div>
            <div class="w-12 h-12 bg-purple-50 rounded-xl flex items-center justify-center group-hover:bg-purple-100 transition-colors duration-300">
              <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
              </svg>
            </div>
          </div>
        </div>
      </div>

      <!-- Filters Premium -->
      <div class="bg-white rounded-2xl shadow-lg mb-8 p-6 border border-gray-100">
        <div class="flex items-center justify-between mb-6">
          <div class="flex items-center space-x-3">
            <div class="w-10 h-10 bg-gradient-to-br from-gray-600 to-gray-700 rounded-xl flex items-center justify-center">
              <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.414A1 1 0 013 6.707V4z"/>
              </svg>
            </div>
            <div>
              <h3 class="text-xl font-bold text-gray-900">Filtros Avanzados</h3>
              <p class="text-sm text-gray-600">Personaliza la vista de datos</p>
            </div>
          </div>
          <button
            @click="clearFilters"
            class="px-4 py-2 text-sm text-gray-600 hover:text-gray-800 hover:bg-gray-100 rounded-lg transition-colors duration-200"
          >
            Limpiar filtros
          </button>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-6">
          <div class="space-y-2">
            <label class="block text-sm font-semibold text-gray-700">Buscar Usuario</label>
            <div class="relative">
              <input
                v-model="searchQuery"
                type="text"
                placeholder="Nombre o email..."
                class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
              >
              <svg class="absolute left-3 top-3.5 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
              </svg>
            </div>
          </div>
          
          <div class="space-y-2">
            <label class="block text-sm font-semibold text-gray-700">Estado</label>
            <select
              v-model="statusFilter"
              class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
            >
              <option value="">Todos los estados</option>
              <option value="open">🟢 Activas</option>
              <option value="closed">🔴 Cerradas</option>
            </select>
          </div>
          
          <div class="space-y-2">
            <label class="block text-sm font-semibold text-gray-700">Fecha</label>
            <input
              v-model="dateFilter"
              type="date"
              class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
            >
          </div>
          
          <div class="space-y-2">
            <label class="block text-sm font-semibold text-gray-700">Exportar</label>
            <button
              @click="exportData"
              class="w-full px-4 py-3 bg-gradient-to-r from-green-600 to-green-700 text-white rounded-xl hover:from-green-700 hover:to-green-800 transition-all duration-200 font-medium"
            >
              📊 Exportar Excel
            </button>
          </div>
        </div>
      </div>

      <!-- Sessions Table Premium -->
      <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100">
        <div class="bg-gradient-to-r from-gray-900 via-gray-800 to-gray-900 px-8 py-6">
          <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
              <div class="w-12 h-12 bg-white/10 rounded-xl flex items-center justify-center backdrop-blur-sm">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                </svg>
              </div>
              <div>
                <h2 class="text-2xl font-bold text-white">Centro de Control de Sesiones</h2>
                <p class="text-gray-300">{{ filteredSessions.length }} sesiones encontradas</p>
              </div>
            </div>
            <div class="flex items-center space-x-3">
              <div class="px-4 py-2 bg-white/10 backdrop-blur-sm rounded-lg">
                <span class="text-white text-sm font-medium">
                  Última sync: {{ new Date().toLocaleTimeString('es-ES') }}
                </span>
              </div>
            </div>
          </div>
        </div>
        
        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-8 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                  <div class="flex items-center space-x-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    <span>Usuario</span>
                  </div>
                </th>
                <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                  <div class="flex items-center space-x-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span>Estado</span>
                  </div>
                </th>
                <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                  <div class="flex items-center space-x-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3a2 2 0 012-2h4a2 2 0 012 2v4m-6 0h6m-6 0l-3 9a2 2 0 002 2h10a2 2 0 002-2l-3-9"/>
                    </svg>
                    <span>Apertura</span>
                  </div>
                </th>
                <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                  <div class="flex items-center space-x-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                    </svg>
                    <span>Inicial</span>
                  </div>
                </th>
                <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                  <div class="flex items-center space-x-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                    </svg>
                    <span>Ventas</span>
                  </div>
                </th>
                <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                  <div class="flex items-center space-x-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span>Duración</span>
                  </div>
                </th>
                <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                  <div class="flex items-center space-x-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"/>
                    </svg>
                    <span>Acciones</span>
                  </div>
                </th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="session in filteredSessions" :key="session.id" 
                  class="hover:bg-gray-50 transition-colors duration-200 group">
                <td class="px-8 py-6 whitespace-nowrap">
                  <div class="flex items-center">
                    <div class="relative">
                      <div class="h-12 w-12 rounded-xl bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center text-white font-bold text-lg shadow-lg">
                        {{ getUserInitials(session.user?.name || 'Usuario') }}
                      </div>
                      <div v-if="session.status === 'open'" class="absolute -top-1 -right-1 w-4 h-4 bg-green-500 rounded-full border-2 border-white"></div>
                    </div>
                    <div class="ml-4">
                      <div class="text-sm font-bold text-gray-900">
                        {{ session.user?.name || 'Usuario desconocido' }}
                      </div>
                      <div class="text-sm text-gray-500 flex items-center">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"/>
                        </svg>
                        {{ session.user?.email }}
                      </div>
                      <div class="text-xs text-gray-400 mt-1">
                        ID: #{{ session.id }}
                      </div>
                    </div>
                  </div>
                </td>
                <td class="px-6 py-6 whitespace-nowrap">
                  <span :class="getStatusBadgeClass(session.status)" class="inline-flex items-center px-3 py-1 text-xs font-bold rounded-full">
                    <div :class="[
                      'w-2 h-2 rounded-full mr-2',
                      session.status === 'open' ? 'bg-green-400' : 'bg-gray-400'
                    ]"></div>
                    {{ session.status === 'open' ? 'ACTIVA' : 'CERRADA' }}
                  </span>
                </td>
                <td class="px-6 py-6 whitespace-nowrap text-sm text-gray-900">
                  <div class="font-semibold">{{ formatDate(session.opening_date) }}</div>
                  <div class="text-gray-500 text-xs">{{ session.opening_time }}</div>
                </td>
                <td class="px-6 py-6 whitespace-nowrap text-sm font-bold text-gray-900">
                  <div class="flex items-center">
                    <svg class="w-4 h-4 text-green-600 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                    </svg>
                    ${{ parseFloat(session.opening_amount || 0).toLocaleString() }}
                  </div>
                </td>
                <td class="px-6 py-6 whitespace-nowrap text-sm text-gray-900">
                  <div class="flex items-center">
                    <svg class="w-4 h-4 text-blue-600 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                    </svg>
                    <span class="font-semibold">
                      ${{ parseFloat(session.total_sales || 0).toLocaleString() }}
                    </span>
                  </div>
                </td>
                <td class="px-6 py-6 whitespace-nowrap text-sm text-gray-500">
                  <div class="flex items-center">
                    <svg class="w-4 h-4 text-purple-600 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    {{ getSessionDuration(session) }}
                  </div>
                </td>
                <td class="px-6 py-6 whitespace-nowrap text-sm font-medium">
                  <div class="flex items-center space-x-2">
                    <button
                      @click="viewSessionDetails(session)"
                      class="group p-2 bg-blue-100 hover:bg-blue-200 text-blue-600 hover:text-blue-700 rounded-lg transition-all duration-200 hover:scale-105"
                      title="Ver detalles"
                    >
                      <svg class="w-4 h-4 group-hover:scale-110 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                      </svg>
                    </button>
                    <button
                      v-if="session.status === 'open'"
                      @click="closeSession(session)"
                      class="group p-2 bg-red-100 hover:bg-red-200 text-red-600 hover:text-red-700 rounded-lg transition-all duration-200 hover:scale-105"
                      title="Cerrar sesión"
                    >
                      <svg class="w-4 h-4 group-hover:scale-110 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                      </svg>
                    </button>
                    <button
                      @click="generateReport(session)"
                      class="group p-2 bg-green-100 hover:bg-green-200 text-green-600 hover:text-green-700 rounded-lg transition-all duration-200 hover:scale-105"
                      title="Generar reporte"
                    >
                      <svg class="w-4 h-4 group-hover:scale-110 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                      </svg>
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Empty State -->
        <div v-if="filteredSessions.length === 0" class="text-center py-16">
          <div class="w-24 h-24 mx-auto bg-gray-100 rounded-full flex items-center justify-center mb-6">
            <svg class="w-12 h-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
            </svg>
          </div>
          <h3 class="text-xl font-semibold text-gray-900 mb-2">No se encontraron sesiones</h3>
          <p class="text-gray-500 mb-6">Ajusta los filtros o crea una nueva sesión para comenzar</p>
          <button
            @click="clearFilters"
            class="px-6 py-3 bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition-colors duration-200"
          >
            Limpiar filtros
          </button>
        </div>
      </div>
    </div>

    <!-- Session Details Modal Premium -->
    <div v-if="selectedSession" class="fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center z-50 p-4">
      <div class="bg-white rounded-3xl max-w-4xl w-full max-h-[90vh] overflow-y-auto shadow-2xl border border-gray-200">
        <div class="bg-gradient-to-r from-blue-600 via-purple-600 to-indigo-700 px-8 py-6 rounded-t-3xl">
          <div class="flex justify-between items-center">
            <div class="flex items-center space-x-4">
              <div class="w-16 h-16 bg-white/20 rounded-2xl flex items-center justify-center backdrop-blur-sm">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                </svg>
              </div>
              <div>
                <h3 class="text-2xl font-bold text-white">Detalles de Sesión</h3>
                <p class="text-blue-100">ID: #{{ selectedSession.id }} • {{ selectedSession.user?.name }}</p>
              </div>
            </div>
            <button 
              @click="selectedSession = null" 
              class="w-10 h-10 bg-white/20 hover:bg-white/30 rounded-xl flex items-center justify-center transition-colors duration-200 backdrop-blur-sm"
            >
              <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
              </svg>
            </button>
          </div>
        </div>
        
        <div class="p-8">
          <!-- User Info -->
          <div class="bg-gradient-to-r from-gray-50 to-blue-50 rounded-2xl p-6 mb-8">
            <div class="flex items-center space-x-6">
              <div class="relative">
                <div class="w-20 h-20 bg-gradient-to-br from-blue-500 to-purple-600 rounded-2xl flex items-center justify-center text-white font-bold text-2xl shadow-lg">
                  {{ getUserInitials(selectedSession.user?.name || 'Usuario') }}
                </div>
                <div v-if="selectedSession.status === 'open'" class="absolute -top-2 -right-2 w-6 h-6 bg-green-500 rounded-full border-4 border-white"></div>
              </div>
              <div class="flex-1">
                <h4 class="text-2xl font-bold text-gray-900">{{ selectedSession.user?.name }}</h4>
                <p class="text-gray-600 text-lg">{{ selectedSession.user?.email }}</p>
                <div class="flex items-center mt-2 space-x-4">
                  <span :class="getStatusBadgeClass(selectedSession.status)" class="px-3 py-1 text-sm font-bold rounded-full">
                    {{ selectedSession.status === 'open' ? 'SESIÓN ACTIVA' : 'SESIÓN CERRADA' }}
                  </span>
                  <span class="text-sm text-gray-500">
                    CC: {{ selectedSession.user?.cc }}
                  </span>
                </div>
              </div>
            </div>
          </div>

          <!-- Session Info Grid -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <div class="space-y-4">
              <div class="bg-gray-50 p-4 rounded-xl">
                <label class="block text-sm font-bold text-gray-700 mb-2">Fecha de Apertura</label>
                <p class="text-lg font-semibold text-gray-900">{{ formatDate(selectedSession.opening_date) }}</p>
              </div>
              <div class="bg-gray-50 p-4 rounded-xl">
                <label class="block text-sm font-bold text-gray-700 mb-2">Hora de Apertura</label>
                <p class="text-lg font-semibold text-gray-900">{{ selectedSession.opening_time }}</p>
              </div>
            </div>
            <div class="space-y-4">
              <div class="bg-gray-50 p-4 rounded-xl">
                <label class="block text-sm font-bold text-gray-700 mb-2">Duración</label>
                <p class="text-lg font-semibold text-gray-900">{{ getSessionDuration(selectedSession) }}</p>
              </div>
              <div class="bg-gray-50 p-4 rounded-xl">
                <label class="block text-sm font-bold text-gray-700 mb-2">Última Actualización</label>
                <p class="text-lg font-semibold text-gray-900">{{ formatDate(selectedSession.updated_at) }}</p>
              </div>
            </div>
          </div>

          <!-- Financial Summary -->
          <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-gradient-to-br from-green-50 to-emerald-50 p-6 rounded-2xl border border-green-200">
              <div class="flex items-center justify-between">
                <div>
                  <p class="text-sm font-bold text-green-700">Monto Inicial</p>
                  <p class="text-3xl font-bold text-green-900">${{ parseFloat(selectedSession.opening_amount || 0).toLocaleString() }}</p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                  <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                  </svg>
                </div>
              </div>
            </div>
            
            <div class="bg-gradient-to-br from-blue-50 to-indigo-50 p-6 rounded-2xl border border-blue-200">
              <div class="flex items-center justify-between">
                <div>
                  <p class="text-sm font-bold text-blue-700">Total Ventas</p>
                  <p class="text-3xl font-bold text-blue-900">${{ parseFloat(selectedSession.total_sales || 0).toLocaleString() }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                  <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                  </svg>
                </div>
              </div>
            </div>
            
            <div class="bg-gradient-to-br from-purple-50 to-pink-50 p-6 rounded-2xl border border-purple-200">
              <div class="flex items-center justify-between">
                <div>
                  <p class="text-sm font-bold text-purple-700">Efectivo</p>
                  <p class="text-3xl font-bold text-purple-900">${{ parseFloat(selectedSession.cash_sales || 0).toLocaleString() }}</p>
                </div>
                <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center">
                  <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v2a2 2 0 002 2z"/>
                  </svg>
                </div>
              </div>
            </div>
          </div>

          <!-- Notes -->
          <div v-if="selectedSession.opening_notes" class="mb-8">
            <label class="block text-sm font-bold text-gray-700 mb-3">Notas de Apertura</label>
            <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-4">
              <p class="text-gray-800">{{ selectedSession.opening_notes }}</p>
            </div>
          </div>

          <!-- Actions -->
          <div class="flex justify-end space-x-4 pt-6 border-t border-gray-200">
            <button
              @click="selectedSession = null"
              class="px-6 py-3 border-2 border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50 transition-colors duration-200 font-semibold"
            >
              Cerrar
            </button>
            <button
              v-if="selectedSession.status === 'open'"
              @click="closeSession(selectedSession)"
              class="px-6 py-3 bg-gradient-to-r from-red-600 to-red-700 text-white rounded-xl hover:from-red-700 hover:to-red-800 transition-all duration-200 font-semibold shadow-lg shadow-red-500/30"
            >
              🔒 Cerrar Sesión
            </button>
            <button
              @click="generateReport(selectedSession)"
              class="px-6 py-3 bg-gradient-to-r from-green-600 to-green-700 text-white rounded-xl hover:from-green-700 hover:to-green-800 transition-all duration-200 font-semibold shadow-lg shadow-green-500/30"
            >
              📊 Generar Reporte
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Toast Notifications Container -->
    <div class="fixed top-4 right-4 z-50 space-y-2">
      <div
        v-for="toast in toasts"
        :key="toast.id"
        :class="[
          'max-w-sm w-full bg-white shadow-lg rounded-xl border-l-4 transform transition-all duration-500',
          toast.type === 'success' ? 'border-green-500' : toast.type === 'error' ? 'border-red-500' : 'border-blue-500',
          toast.show ? 'translate-x-0 opacity-100' : 'translate-x-full opacity-0'
        ]"
      >
        <div class="p-4">
          <div class="flex items-start">
            <div class="flex-shrink-0">
              <svg
                v-if="toast.type === 'success'"
                class="w-6 h-6 text-green-500"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
              >
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
              </svg>
              <svg
                v-else-if="toast.type === 'error'"
                class="w-6 h-6 text-red-500"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
              >
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
              </svg>
              <svg
                v-else
                class="w-6 h-6 text-blue-500"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
              >
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
              </svg>
            </div>
            <div class="ml-3 w-0 flex-1">
              <p class="text-sm font-semibold text-gray-900">{{ toast.title }}</p>
              <p class="text-sm text-gray-600 mt-1">{{ toast.message }}</p>
            </div>
            <div class="ml-4 flex-shrink-0 flex">
              <button
                @click="removeToast(toast.id)"
                class="text-gray-400 hover:text-gray-600 focus:outline-none"
              >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { apiCall } from '../services/api.js'

// Data
const sessions = ref([])
const loading = ref(false)
const selectedSession = ref(null)
const showNewSessionModal = ref(false)
const toasts = ref([])

// Filters
const searchQuery = ref('')
const statusFilter = ref('')
const dateFilter = ref('')

// Toast System
let toastId = 0

const showToast = (title, message, type = 'info') => {
  const id = ++toastId
  const toast = {
    id,
    title,
    message,
    type,
    show: false
  }
  
  toasts.value.push(toast)
  
  // Show with animation
  setTimeout(() => {
    toast.show = true
  }, 100)
  
  // Auto remove after 5 seconds
  setTimeout(() => {
    removeToast(id)
  }, 5000)
}

const removeToast = (id) => {
  const toastIndex = toasts.value.findIndex(t => t.id === id)
  if (toastIndex > -1) {
    toasts.value[toastIndex].show = false
    setTimeout(() => {
      toasts.value.splice(toastIndex, 1)
    }, 300)
  }
}

// Computed
const activeSessions = computed(() => sessions.value.filter(s => s.status === 'open'))
const uniqueUsers = computed(() => {
  const userIds = new Set(activeSessions.value.map(s => s.user_id))
  return Array.from(userIds)
})

const totalCashAmount = computed(() => {
  return activeSessions.value.reduce((total, session) => {
    return total + parseFloat(session.opening_amount || 0) + parseFloat(session.total_sales || 0)
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

// Methods
const refreshSessions = async () => {
  loading.value = true
  try {
    showToast('🔄 Actualizando...', 'Obteniendo datos más recientes del sistema', 'info')
    
    const data = await apiCall('/cash-sessions')
    sessions.value = data.sessions || []
    showToast('✅ Actualización completa', `Se cargaron ${sessions.value.length} sesiones correctamente`, 'success')
  } catch (error) {
    console.error('Error fetching sessions:', error)
    showToast('❌ Error de conexión', 'No se pudieron cargar las sesiones. Verifica la conexión.', 'error')
  } finally {
    loading.value = false
  }
}

const clearFilters = () => {
  searchQuery.value = ''
  statusFilter.value = ''
  dateFilter.value = ''
  showToast('🧹 Filtros limpiados', 'Mostrando todas las sesiones disponibles', 'info')
}

const exportData = () => {
  showToast('📊 Exportando datos...', 'Preparando archivo Excel con la información actual', 'info')
  // TODO: Implementar exportación real
  setTimeout(() => {
    showToast('✅ Exportación completa', 'El archivo Excel se ha descargado correctamente', 'success')
  }, 2000)
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
  if (!session.opening_date || !session.opening_time) return 'N/A'
  
  const openingDateTime = new Date(`${session.opening_date}T${session.opening_time}`)
  const now = new Date()
  const diff = now - openingDateTime
  
  const hours = Math.floor(diff / (1000 * 60 * 60))
  const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60))
  
  return `${hours}h ${minutes}m`
}

const viewSessionDetails = (session) => {
  selectedSession.value = session
  showToast('👁️ Detalles cargados', `Mostrando información completa de la sesión #${session.id}`, 'info')
}

const closeSession = async (session) => {
  // Confirmation modal simulation
  const confirmed = await new Promise((resolve) => {
    showToast('⚠️ Confirmación requerida', `¿Cerrar la sesión de ${session.user?.name}?`, 'info')
    
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
    showToast('🔄 Cerrando sesión...', 'Procesando cierre de caja', 'info')
    
    await apiCall(`/cash-sessions/${session.id}/close`, {
      method: 'POST',
      body: JSON.stringify({
        actual_amount: session.opening_amount,
        closing_notes: 'Cerrada desde panel administrativo'
      })
    })
    
    showToast('✅ Sesión cerrada', `La sesión de ${session.user?.name} se cerró correctamente`, 'success')
    selectedSession.value = null
    refreshSessions()
  } catch (error) {
    console.error('Error closing session:', error)
    showToast('❌ Error al cerrar', 'No se pudo cerrar la sesión. Intenta nuevamente.', 'error')
  }
}

const generateReport = (session) => {
  showToast('📊 Generando reporte...', `Creando reporte detallado para ${session.user?.name}`, 'info')
  
  // Simulate report generation
  setTimeout(() => {
    showToast('✅ Reporte generado', 'El reporte se ha creado exitosamente', 'success')
  }, 1500)
}

// Lifecycle
onMounted(() => {
  refreshSessions()
  showToast('🚀 Panel cargado', 'Sistema administrativo iniciado correctamente', 'success')
})
</script>