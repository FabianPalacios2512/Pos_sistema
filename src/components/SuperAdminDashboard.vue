<template>
  <!-- ============================================ -->
  <!-- SUPER ADMIN DASHBOARD - GOD MODE -->
  <!-- Dark Theme Technical Panel -->
  <!-- ============================================ -->
  <div class="min-h-screen bg-gradient-to-br from-gray-900 via-slate-900 to-gray-800">
    
    <!-- Header / TopBar -->
    <header class="sticky top-0 z-50 bg-gray-900/95 backdrop-blur-sm border-b border-red-500/30 shadow-lg">
      <div class="px-6 py-4">
        <div class="flex items-center justify-between">
          <!-- Logo & Title -->
          <div class="flex items-center space-x-4">
            <div class="w-12 h-12 bg-gradient-to-br from-red-600 to-orange-600 rounded-lg flex items-center justify-center shadow-lg">
              <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
              </svg>
            </div>
            <div>
              <h1 class="text-2xl font-bold text-white flex items-center space-x-2">
                <span>GOD MODE</span>
                <span class="px-2 py-0.5 bg-red-600 text-xs font-bold rounded-full animate-pulse">ADMIN</span>
              </h1>
              <p class="text-sm text-gray-400">Panel de Administración Central</p>
            </div>
          </div>

          <!-- Acciones Rápidas -->
          <div class="flex items-center space-x-3">
            <button @click="refreshData" 
                    class="px-4 py-2 bg-gray-800 hover:bg-gray-700 text-gray-300 rounded-lg border border-gray-700 transition-all duration-200 flex items-center space-x-2">
              <svg class="w-4 h-4" :class="{ 'animate-spin': loading }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
              </svg>
              <span class="text-sm font-semibold">Actualizar</span>
            </button>
            
            <div class="px-3 py-2 bg-gray-800 rounded-lg border border-gray-700">
              <span class="text-xs text-gray-400">Última actualización:</span>
              <span class="text-xs text-white font-mono ml-2">{{ lastUpdate }}</span>
            </div>
          </div>
        </div>
      </div>
    </header>

    <!-- Main Content -->
    <main class="p-6 space-y-6">
      
      <!-- ============================================ -->
      <!-- SECCIÓN 1: KPIs PRINCIPALES -->
      <!-- ============================================ -->
      <section>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
          
          <!-- KPI: Total Clientes Activos -->
          <div class="bg-gradient-to-br from-gray-800 to-gray-900 rounded-xl p-5 border border-emerald-500/30 hover:border-emerald-500/60 transition-all duration-200 shadow-lg">
            <div class="flex items-center justify-between mb-3">
              <div class="w-12 h-12 bg-emerald-500/20 rounded-lg flex items-center justify-center">
                <svg class="w-6 h-6 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
              </div>
              <span class="px-2 py-1 bg-emerald-500/20 text-emerald-400 text-xs font-bold rounded-full">ACTIVO</span>
            </div>
            <div>
              <p class="text-xs text-gray-400 uppercase tracking-wider font-semibold mb-1">Clientes Activos</p>
              <p class="text-3xl font-bold text-white mb-2">{{ kpis.total_active_clients }}</p>
              <div class="flex items-center space-x-2 text-xs">
                <span class="text-emerald-400 font-semibold">+{{ kpis.clients_created_today }}</span>
                <span class="text-gray-500">hoy</span>
              </div>
            </div>
          </div>

          <!-- KPI: Ingresos Mensuales (MRR) -->
          <div class="bg-gradient-to-br from-gray-800 to-gray-900 rounded-xl p-5 border border-blue-500/30 hover:border-blue-500/60 transition-all duration-200 shadow-lg">
            <div class="flex items-center justify-between mb-3">
              <div class="w-12 h-12 bg-blue-500/20 rounded-lg flex items-center justify-center">
                <svg class="w-6 h-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
              </div>
              <span class="px-2 py-1 bg-blue-500/20 text-blue-400 text-xs font-bold rounded-full">MRR</span>
            </div>
            <div>
              <p class="text-xs text-gray-400 uppercase tracking-wider font-semibold mb-1">Ingresos Mensuales</p>
              <p class="text-3xl font-bold text-white mb-2">${{ formatNumber(kpis.mrr) }}</p>
              <div class="flex items-center space-x-2 text-xs">
                <span class="text-blue-400 font-semibold">USD/mes</span>
              </div>
            </div>
          </div>

          <!-- KPI: Tokens IA Consumidos -->
          <div class="bg-gradient-to-br from-gray-800 to-gray-900 rounded-xl p-5 border border-purple-500/30 hover:border-purple-500/60 transition-all duration-200 shadow-lg">
            <div class="flex items-center justify-between mb-3">
              <div class="w-12 h-12 bg-purple-500/20 rounded-lg flex items-center justify-center">
                <svg class="w-6 h-6 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                </svg>
              </div>
              <span class="px-2 py-1 bg-purple-500/20 text-purple-400 text-xs font-bold rounded-full">IA</span>
            </div>
            <div>
              <p class="text-xs text-gray-400 uppercase tracking-wider font-semibold mb-1">Tokens IA (Mes)</p>
              <p class="text-3xl font-bold text-white mb-2">{{ formatNumber(kpis.ai_tokens_this_month) }}</p>
              <div class="flex items-center space-x-2 text-xs">
                <span class="text-purple-400 font-semibold">${{ kpis.ai_cost_this_month.toFixed(2) }}</span>
                <span class="text-gray-500">costo</span>
              </div>
            </div>
          </div>

          <!-- KPI: Tiendas Hoy -->
          <div class="bg-gradient-to-br from-gray-800 to-gray-900 rounded-xl p-5 border border-orange-500/30 hover:border-orange-500/60 transition-all duration-200 shadow-lg">
            <div class="flex items-center justify-between mb-3">
              <div class="w-12 h-12 bg-orange-500/20 rounded-lg flex items-center justify-center">
                <svg class="w-6 h-6 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                </svg>
              </div>
              <span class="px-2 py-1 bg-orange-500/20 text-orange-400 text-xs font-bold rounded-full">HOY</span>
            </div>
            <div>
              <p class="text-xs text-gray-400 uppercase tracking-wider font-semibold mb-1">Tiendas Creadas Hoy</p>
              <p class="text-3xl font-bold text-white mb-2">{{ kpis.clients_created_today }}</p>
              <div class="flex items-center space-x-2 text-xs">
                <span class="text-orange-400 font-semibold">{{ getCurrentDate() }}</span>
              </div>
            </div>
          </div>

        </div>
      </section>

      <!-- ============================================ -->
      <!-- SECCIÓN 2: GESTIÓN DE TENANTS -->
      <!-- ============================================ -->
      <section>
        <div class="bg-gray-800 rounded-xl border border-gray-700 shadow-lg overflow-hidden">
          
          <!-- Header de la Tabla -->
          <div class="bg-gray-900 border-b border-gray-700 px-6 py-4">
            <div class="flex items-center justify-between">
              <div>
                <h2 class="text-lg font-bold text-white flex items-center space-x-2">
                  <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                  </svg>
                  <span>Gestión de Tenants</span>
                </h2>
                <p class="text-xs text-gray-400 mt-1">{{ tenants.total }} inquilinos registrados</p>
              </div>
              
              <!-- Búsqueda -->
              <div class="flex items-center space-x-3">
                <div class="relative">
                  <input v-model="searchTenant" 
                         @input="searchTenants"
                         type="text" 
                         placeholder="Buscar tenant..." 
                         class="w-64 pl-10 pr-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white text-sm focus:ring-2 focus:ring-emerald-500 focus:border-transparent">
                  <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                  </svg>
                </div>
              </div>
            </div>
          </div>

          <!-- Tabla de Tenants -->
          <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-700">
              <thead class="bg-gray-900">
                <tr>
                  <th class="px-6 py-3 text-left text-xs font-bold text-gray-300 uppercase tracking-wider">Negocio</th>
                  <th class="px-6 py-3 text-left text-xs font-bold text-gray-300 uppercase tracking-wider">Subdominio</th>
                  <th class="px-6 py-3 text-left text-xs font-bold text-gray-300 uppercase tracking-wider">Plan</th>
                  <th class="px-6 py-3 text-left text-xs font-bold text-gray-300 uppercase tracking-wider">Estado</th>
                  <th class="px-6 py-3 text-left text-xs font-bold text-gray-300 uppercase tracking-wider">Creado</th>
                  <th class="px-6 py-3 text-left text-xs font-bold text-gray-300 uppercase tracking-wider">Acciones</th>
                </tr>
              </thead>
              <tbody class="bg-gray-800 divide-y divide-gray-700">
                <tr v-for="tenant in tenants.data" 
                    :key="tenant.id" 
                    class="hover:bg-gray-750 transition-colors">
                  
                  <!-- Nombre del Negocio -->
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center">
                      <div class="w-10 h-10 bg-gradient-to-br from-blue-600 to-cyan-600 rounded-lg flex items-center justify-center mr-3">
                        <span class="text-white font-bold text-sm">{{ tenant.business_name.charAt(0).toUpperCase() }}</span>
                      </div>
                      <div>
                        <p class="text-sm font-semibold text-white">{{ tenant.business_name }}</p>
                        <p class="text-xs text-gray-400 font-mono">ID: {{ tenant.id.substring(0, 8) }}...</p>
                      </div>
                    </div>
                  </td>

                  <!-- Subdominio (Clickeable) -->
                  <td class="px-6 py-4 whitespace-nowrap">
                    <a :href="`http://${tenant.primary_domain}`" 
                       target="_blank"
                       class="text-sm text-blue-400 hover:text-blue-300 font-mono flex items-center space-x-1 hover:underline">
                      <span>{{ tenant.primary_domain }}</span>
                      <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                      </svg>
                    </a>
                  </td>

                  <!-- Plan -->
                  <td class="px-6 py-4 whitespace-nowrap">
                    <span :class="getPlanBadgeClass(tenant.plan)" 
                          class="px-2 py-1 text-xs font-bold rounded-full uppercase">
                      {{ tenant.plan }}
                    </span>
                  </td>

                  <!-- Estado -->
                  <td class="px-6 py-4 whitespace-nowrap">
                    <span :class="getStatusBadgeClass(tenant.status)" 
                          class="px-2 py-1 text-xs font-bold rounded-full flex items-center space-x-1 w-fit">
                      <span class="w-2 h-2 rounded-full" :class="tenant.status === 'active' ? 'bg-emerald-400' : 'bg-red-400'"></span>
                      <span>{{ tenant.status === 'active' ? 'Activo' : 'Expirado' }}</span>
                    </span>
                  </td>

                  <!-- Fecha de Creación -->
                  <td class="px-6 py-4 whitespace-nowrap">
                    <p class="text-sm text-gray-300">{{ formatDate(tenant.created_at) }}</p>
                    <p class="text-xs text-gray-500">{{ getRelativeTime(tenant.created_at) }}</p>
                  </td>

                  <!-- Acciones -->
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center space-x-2">
                      <!-- Botón: Entrar al Panel -->
                      <button @click="loginToTenant(tenant)" 
                              class="px-3 py-1.5 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 text-white rounded-lg text-xs font-bold transition-all duration-200 flex items-center space-x-1 shadow-sm">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                        </svg>
                        <span>Entrar</span>
                      </button>
                      
                      <!-- Botón: Ver Detalles -->
                      <button @click="viewTenantDetails(tenant)" 
                              class="p-1.5 bg-gray-700 hover:bg-gray-600 text-gray-300 rounded-lg transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                      </button>
                    </div>
                  </td>
                </tr>

                <!-- Estado Vacío -->
                <tr v-if="tenants.data.length === 0">
                  <td colspan="6" class="px-6 py-12 text-center">
                    <div class="flex flex-col items-center space-y-3">
                      <div class="w-16 h-16 bg-gray-700 rounded-full flex items-center justify-center">
                        <svg class="w-8 h-8 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                        </svg>
                      </div>
                      <div>
                        <p class="text-sm font-semibold text-gray-300">No hay tenants</p>
                        <p class="text-xs text-gray-500 mt-1">Aún no se han registrado clientes</p>
                      </div>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- Paginación -->
          <div v-if="tenants.last_page > 1" 
               class="bg-gray-900 border-t border-gray-700 px-6 py-4 flex items-center justify-between">
            <div class="text-sm text-gray-400">
              Mostrando {{ tenants.from }} a {{ tenants.to }} de {{ tenants.total }} tenants
            </div>
            <div class="flex items-center space-x-2">
              <button @click="changePage(tenants.current_page - 1)" 
                      :disabled="tenants.current_page === 1"
                      class="px-3 py-1 bg-gray-700 text-gray-300 rounded-lg text-sm disabled:opacity-50 disabled:cursor-not-allowed hover:bg-gray-600 transition-colors">
                Anterior
              </button>
              <span class="text-sm text-gray-300 font-mono">
                Página {{ tenants.current_page }} de {{ tenants.last_page }}
              </span>
              <button @click="changePage(tenants.current_page + 1)" 
                      :disabled="tenants.current_page === tenants.last_page"
                      class="px-3 py-1 bg-gray-700 text-gray-300 rounded-lg text-sm disabled:opacity-50 disabled:cursor-not-allowed hover:bg-gray-600 transition-colors">
                Siguiente
              </button>
            </div>
          </div>

        </div>
      </section>

      <!-- ============================================ -->
      <!-- SECCIÓN 3: MONITOR DE IA (AI WATCHTOWER) -->
      <!-- ============================================ -->
      <section>
        <div class="bg-gray-800 rounded-xl border border-gray-700 shadow-lg overflow-hidden">
          
          <!-- Header -->
          <div class="bg-gradient-to-r from-purple-900 to-indigo-900 border-b border-purple-700/50 px-6 py-4">
            <div class="flex items-center justify-between">
              <div>
                <h2 class="text-lg font-bold text-white flex items-center space-x-2">
                  <svg class="w-5 h-5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                  </svg>
                  <span>AI Watchtower - Monitor de Consumo</span>
                  <span class="px-2 py-0.5 bg-purple-600 text-xs font-bold rounded-full">BETA</span>
                </h2>
                <p class="text-xs text-purple-200 mt-1">
                  {{ aiUsageStats.total_tenants_using_ai }} tenants usando IA • 
                  ${{ aiUsageStats.total_cost_period }} costo total
                </p>
              </div>

              <!-- Filtro de Período -->
              <select v-model="aiPeriod" 
                      @change="fetchAiUsage"
                      class="px-3 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white text-sm focus:ring-2 focus:ring-purple-500">
                <option value="day">Hoy</option>
                <option value="week">Esta Semana</option>
                <option value="month">Este Mes</option>
              </select>
            </div>
          </div>

          <!-- Tabla de Consumo de IA -->
          <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-700">
              <thead class="bg-gray-900">
                <tr>
                  <th class="px-6 py-3 text-left text-xs font-bold text-gray-300 uppercase tracking-wider">Tenant</th>
                  <th class="px-6 py-3 text-left text-xs font-bold text-gray-300 uppercase tracking-wider">Requests</th>
                  <th class="px-6 py-3 text-left text-xs font-bold text-gray-300 uppercase tracking-wider">Tokens</th>
                  <th class="px-6 py-3 text-left text-xs font-bold text-gray-300 uppercase tracking-wider">Avg/Request</th>
                  <th class="px-6 py-3 text-left text-xs font-bold text-gray-300 uppercase tracking-wider">Costo</th>
                  <th class="px-6 py-3 text-left text-xs font-bold text-gray-300 uppercase tracking-wider">Alerta</th>
                </tr>
              </thead>
              <tbody class="bg-gray-800 divide-y divide-gray-700">
                <tr v-for="usage in aiUsageData" 
                    :key="usage.tenant_id"
                    :class="usage.is_anomalous ? 'bg-red-900/20' : ''"
                    class="hover:bg-gray-750 transition-colors">
                  
                  <!-- Tenant -->
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div>
                      <p class="text-sm font-semibold text-white">{{ usage.business_name }}</p>
                      <p class="text-xs text-gray-400 font-mono">{{ usage.primary_domain }}</p>
                    </div>
                  </td>

                  <!-- Total Requests -->
                  <td class="px-6 py-4 whitespace-nowrap">
                    <p class="text-sm text-gray-300 font-mono">{{ usage.total_requests }}</p>
                  </td>

                  <!-- Total Tokens -->
                  <td class="px-6 py-4 whitespace-nowrap">
                    <p class="text-sm font-semibold text-white font-mono">{{ formatNumber(usage.total_tokens) }}</p>
                  </td>

                  <!-- Promedio por Request -->
                  <td class="px-6 py-4 whitespace-nowrap">
                    <p class="text-sm text-gray-300 font-mono">{{ formatNumber(usage.avg_tokens_per_request) }}</p>
                  </td>

                  <!-- Costo -->
                  <td class="px-6 py-4 whitespace-nowrap">
                    <p class="text-sm font-bold text-purple-400 font-mono">${{ usage.total_cost }}</p>
                  </td>

                  <!-- Nivel de Alerta -->
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center space-x-2">
                      <!-- Badge de Alerta -->
                      <span :class="getAlertBadgeClass(usage.alert_level)" 
                            class="px-2 py-1 text-xs font-bold rounded-full uppercase">
                        {{ getAlertLabel(usage.alert_level) }}
                      </span>
                      
                      <!-- Icono de Anomalía -->
                      <svg v-if="usage.is_anomalous" 
                           class="w-5 h-5 text-red-500 animate-pulse" 
                           fill="currentColor" 
                           viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                      </svg>
                    </div>
                  </td>
                </tr>

                <!-- Estado Vacío -->
                <tr v-if="aiUsageData.length === 0">
                  <td colspan="6" class="px-6 py-12 text-center">
                    <div class="flex flex-col items-center space-y-3">
                      <div class="w-16 h-16 bg-gray-700 rounded-full flex items-center justify-center">
                        <svg class="w-8 h-8 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                        </svg>
                      </div>
                      <div>
                        <p class="text-sm font-semibold text-gray-300">Sin actividad de IA</p>
                        <p class="text-xs text-gray-500 mt-1">No hay consumo de IA en el período seleccionado</p>
                      </div>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

        </div>
      </section>

    </main>

  </div>
</template>

<script>
import { ref, onMounted } from 'vue';
import axios from 'axios';

export default {
  name: 'SuperAdminDashboard',
  
  setup() {
    // Estado
    const loading = ref(false);
    const lastUpdate = ref('--:--');
    
    // KPIs
    const kpis = ref({
      total_active_clients: 0,
      clients_created_today: 0,
      mrr: 0,
      ai_tokens_this_month: 0,
      ai_cost_this_month: 0,
    });

    // Tenants
    const tenants = ref({
      data: [],
      current_page: 1,
      last_page: 1,
      total: 0,
      from: 0,
      to: 0,
    });
    const searchTenant = ref('');

    // AI Usage
    const aiUsageData = ref([]);
    const aiUsageStats = ref({
      total_tenants_using_ai: 0,
      avg_tokens_per_tenant: 0,
      total_cost_period: 0,
    });
    const aiPeriod = ref('month');

    // ============================================
    // MÉTODOS: Fetch Data
    // ============================================
    
    const fetchKpis = async () => {
      try {
        const response = await axios.get('/admin/api/kpis');
        if (response.data.success) {
          kpis.value = response.data.data;
        }
      } catch (error) {
        console.error('Error fetching KPIs:', error);
      }
    };

    const fetchTenants = async (page = 1, search = '') => {
      try {
        const response = await axios.get('/admin/api/tenants', {
          params: { page, search, per_page: 10 }
        });
        if (response.data.success) {
          tenants.value = response.data.data;
        }
      } catch (error) {
        console.error('Error fetching tenants:', error);
      }
    };

    const fetchAiUsage = async () => {
      try {
        const response = await axios.get('/admin/api/ai-usage', {
          params: { period: aiPeriod.value }
        });
        if (response.data.success) {
          aiUsageData.value = response.data.data;
          aiUsageStats.value = response.data.stats;
        }
      } catch (error) {
        console.error('Error fetching AI usage:', error);
      }
    };

    const refreshData = async () => {
      loading.value = true;
      await Promise.all([
        fetchKpis(),
        fetchTenants(tenants.value.current_page, searchTenant.value),
        fetchAiUsage(),
      ]);
      lastUpdate.value = new Date().toLocaleTimeString('es-ES');
      loading.value = false;
    };

    // ============================================
    // MÉTODOS: Acciones
    // ============================================
    
    const searchTenants = () => {
      fetchTenants(1, searchTenant.value);
    };

    const changePage = (page) => {
      fetchTenants(page, searchTenant.value);
    };

    const loginToTenant = (tenant) => {
      // Redirigir al login del tenant
      const url = `http://${tenant.primary_domain}/login`;
      window.open(url, '_blank');
    };

    const viewTenantDetails = async (tenant) => {
      try {
        const response = await axios.get(`/admin/api/tenants/${tenant.id}`);
        if (response.data.success) {
          alert(JSON.stringify(response.data.data, null, 2));
          // TODO: Abrir modal con detalles completos
        }
      } catch (error) {
        console.error('Error fetching tenant details:', error);
      }
    };

    // ============================================
    // HELPERS
    // ============================================
    
    const formatNumber = (num) => {
      return new Intl.NumberFormat('es-ES').format(num);
    };

    const formatDate = (date) => {
      return new Date(date).toLocaleDateString('es-ES', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
      });
    };

    const getRelativeTime = (date) => {
      const now = new Date();
      const past = new Date(date);
      const diffDays = Math.floor((now - past) / (1000 * 60 * 60 * 24));
      
      if (diffDays === 0) return 'Hoy';
      if (diffDays === 1) return 'Ayer';
      if (diffDays < 7) return `Hace ${diffDays} días`;
      if (diffDays < 30) return `Hace ${Math.floor(diffDays / 7)} semanas`;
      return `Hace ${Math.floor(diffDays / 30)} meses`;
    };

    const getCurrentDate = () => {
      return new Date().toLocaleDateString('es-ES', {
        month: 'long',
        day: 'numeric',
      });
    };

    const getPlanBadgeClass = (plan) => {
      const classes = {
        free: 'bg-gray-700 text-gray-300',
        premium: 'bg-gradient-to-r from-yellow-600 to-orange-600 text-white',
        enterprise: 'bg-gradient-to-r from-purple-600 to-pink-600 text-white',
      };
      return classes[plan] || classes.free;
    };

    const getStatusBadgeClass = (status) => {
      return status === 'active' 
        ? 'bg-emerald-500/20 text-emerald-400 border border-emerald-500/50' 
        : 'bg-red-500/20 text-red-400 border border-red-500/50';
    };

    const getAlertBadgeClass = (level) => {
      const classes = {
        normal: 'bg-gray-700 text-gray-300',
        moderate: 'bg-blue-600 text-white',
        warning: 'bg-yellow-600 text-white',
        critical: 'bg-red-600 text-white animate-pulse',
      };
      return classes[level] || classes.normal;
    };

    const getAlertLabel = (level) => {
      const labels = {
        normal: 'Normal',
        moderate: 'Moderado',
        warning: 'Alerta',
        critical: 'Crítico',
      };
      return labels[level] || 'N/A';
    };

    // ============================================
    // Lifecycle
    // ============================================
    
    onMounted(() => {
      refreshData();
      
      // Auto-refresh cada 30 segundos
      setInterval(() => {
        refreshData();
      }, 30000);
    });

    return {
      loading,
      lastUpdate,
      kpis,
      tenants,
      searchTenant,
      aiUsageData,
      aiUsageStats,
      aiPeriod,
      refreshData,
      searchTenants,
      changePage,
      loginToTenant,
      viewTenantDetails,
      fetchAiUsage,
      formatNumber,
      formatDate,
      getRelativeTime,
      getCurrentDate,
      getPlanBadgeClass,
      getStatusBadgeClass,
      getAlertBadgeClass,
      getAlertLabel,
    };
  },
};
</script>

<style scoped>
/* Animaciones personalizadas */
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

.animate-fade-in {
  animation: fade-in 0.5s ease-out;
}

/* Hover state para bg-gray-750 (no existe en Tailwind) */
.hover\:bg-gray-750:hover {
  background-color: #2d3748;
}
</style>
