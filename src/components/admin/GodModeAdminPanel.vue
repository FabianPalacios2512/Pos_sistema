<template>
  <!-- Gradiente oficial del sistema -->
  <div class="min-h-screen font-sans bg-gradient-to-b from-gray-50 via-gray-100 to-gray-200 dark:bg-gradient-to-b dark:from-[#141417] dark:via-slate-900 dark:to-[#0a0a0c] transition-colors duration-300 px-8">
    <div class="p-4 lg:p-6 space-y-6 pb-8 animate-fade-in">
      
      <!-- Header profesional sin icono -->
      <div class="flex items-center justify-between pb-4">
        <div>
          <h1 class="text-2xl font-bold text-gray-900 dark:text-white flex items-center gap-3">
            Super Admin Panel
            <span class="px-2.5 py-1 bg-red-50 dark:bg-red-950 text-red-700 dark:text-red-400 border border-red-100 dark:border-red-800 text-[10px] font-bold rounded-full uppercase tracking-wide animate-pulse">GOD MODE</span>
          </h1>
          <p class="text-sm text-gray-600 dark:text-zinc-400 mt-1">Gestión centralizada de todos los tenants del sistema</p>
        </div>
        
        <div class="flex items-center gap-3">
          <button @click="fetchData" :disabled="loading" class="px-5 py-2.5 bg-white dark:bg-zinc-900 hover:bg-slate-50 dark:hover:bg-zinc-800 text-slate-600 dark:text-zinc-200 text-sm font-bold rounded-xl border border-slate-200 dark:border-zinc-800 shadow-sm transition-all duration-200 disabled:opacity-50">
            <svg class="w-4 h-4" :class="{'animate-spin': loading}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
            </svg>
          </button>
        </div>
      </div>

      <!-- Sistema de Tabs -->
      <div class="bg-white dark:bg-zinc-900/80 backdrop-blur-sm rounded-xl border border-gray-300 dark:border-zinc-800/60 shadow-xl dark:shadow-black/50 p-1 mb-6">
        <div class="grid grid-cols-4 gap-1">
          <button 
            @click="activeTab = 'dashboard'"
            :class="[
              'px-4 py-3 rounded-lg text-sm font-bold transition-all duration-200',
              activeTab === 'dashboard' 
                ? 'bg-slate-900 dark:bg-slate-700 text-white shadow-lg' 
                : 'text-gray-600 dark:text-zinc-400 hover:text-gray-900 dark:hover:text-white hover:bg-gray-50 dark:hover:bg-zinc-800/50'
            ]"
          >
            <svg class="w-5 h-5 inline-block mr-2 -mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
            </svg>
            Dashboard
          </button>
          
          <button 
            @click="activeTab = 'clientes'"
            :class="[
              'px-4 py-3 rounded-lg text-sm font-bold transition-all duration-200',
              activeTab === 'clientes' 
                ? 'bg-slate-900 dark:bg-slate-700 text-white shadow-lg' 
                : 'text-gray-600 dark:text-zinc-400 hover:text-gray-900 dark:hover:text-white hover:bg-gray-50 dark:hover:bg-zinc-800/50'
            ]"
          >
            <svg class="w-5 h-5 inline-block mr-2 -mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
            </svg>
            Clientes
          </button>
          
          <button 
            @click="activeTab = 'ai-monitoring'"
            :class="[
              'px-4 py-3 rounded-lg text-sm font-bold transition-all duration-200',
              activeTab === 'ai-monitoring' 
                ? 'bg-slate-900 dark:bg-slate-700 text-white shadow-lg' 
                : 'text-gray-600 dark:text-zinc-400 hover:text-gray-900 dark:hover:text-white hover:bg-gray-50 dark:hover:bg-zinc-800/50'
            ]"
          >
            <svg class="w-5 h-5 inline-block mr-2 -mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
            </svg>
            AI Monitoring
          </button>
          
          <button 
            @click="activeTab = 'logs'"
            :class="[
              'px-4 py-3 rounded-lg text-sm font-bold transition-all duration-200',
              activeTab === 'logs' 
                ? 'bg-slate-900 dark:bg-slate-700 text-white shadow-lg' 
                : 'text-gray-600 dark:text-zinc-400 hover:text-gray-900 dark:hover:text-white hover:bg-gray-50 dark:hover:bg-zinc-800/50'
            ]"
          >
            <svg class="w-5 h-5 inline-block mr-2 -mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            System Logs
          </button>
        </div>
      </div>

      <!-- TAB: Dashboard -->
      <div v-show="activeTab === 'dashboard'" class="space-y-6">
        <!-- KPIs con glassmorphism -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
          <div class="bg-white dark:bg-zinc-900/80 backdrop-blur-sm rounded-xl px-4 py-3 border border-gray-300 dark:border-zinc-800/60 hover:border-gray-400 dark:hover:border-zinc-700/80 transition-all duration-200 hover:shadow-md dark:shadow-lg dark:shadow-black/30">
            <div class="flex items-center gap-3">
              <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0 bg-gray-100 dark:bg-zinc-800/50 border border-gray-200 dark:border-white/5">
                <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
              </div>
              <div class="flex-1 min-w-0">
                <p class="text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wide">Clientes Activos</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white mt-0.5">{{ kpis.total_active_clients }}</p>
              </div>
            </div>
          </div>

          <div class="bg-white dark:bg-zinc-900/80 backdrop-blur-sm rounded-xl px-4 py-3 border border-gray-300 dark:border-zinc-800/60 hover:border-gray-400 dark:hover:border-zinc-700/80 transition-all duration-200 hover:shadow-md dark:shadow-lg dark:shadow-black/30">
            <div class="flex items-center gap-3">
              <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0 bg-gray-100 dark:bg-zinc-800/50 border border-gray-200 dark:border-white/5">
                <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
              </div>
              <div class="flex-1 min-w-0">
                <p class="text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wide">MRR Total</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white mt-0.5">${{ formatNumber(kpis.mrr) }}</p>
              </div>
            </div>
          </div>

          <div class="bg-white dark:bg-zinc-900/80 backdrop-blur-sm rounded-xl px-4 py-3 border border-gray-300 dark:border-zinc-800/60 hover:border-gray-400 dark:hover:border-zinc-700/80 transition-all duration-200 hover:shadow-md dark:shadow-lg dark:shadow-black/30">
            <div class="flex items-center gap-3">
              <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0 bg-gray-100 dark:bg-zinc-800/50 border border-gray-200 dark:border-white/5">
                <svg class="w-5 h-5 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                </svg>
              </div>
              <div class="flex-1 min-w-0">
                <p class="text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wide">Tokens IA (Mes)</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white mt-0.5">{{ formatNumber(kpis.ai_tokens_this_month) }}</p>
              </div>
            </div>
          </div>

          <div class="bg-white dark:bg-zinc-900/80 backdrop-blur-sm rounded-xl px-4 py-3 border border-gray-300 dark:border-zinc-800/60 hover:border-gray-400 dark:hover:border-zinc-700/80 transition-all duration-200 hover:shadow-md dark:shadow-lg dark:shadow-black/30">
            <div class="flex items-center gap-3">
              <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0 bg-gray-100 dark:bg-zinc-800/50 border border-gray-200 dark:border-white/5">
                <svg class="w-5 h-5 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                </svg>
              </div>
              <div class="flex-1 min-w-0">
                <p class="text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wide">Nuevas Hoy</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white mt-0.5">{{ kpis.clients_created_today }}</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Generador de Links -->
        <div class="bg-white dark:bg-zinc-900 rounded-2xl shadow-xl dark:shadow-black/50 border border-gray-300 dark:border-zinc-800 overflow-hidden">
          <div class="bg-gray-50 dark:bg-zinc-900 border-b border-gray-200 dark:border-zinc-800 px-6 py-4">
            <h3 class="text-lg font-bold text-gray-900 dark:text-white">Generador de Enlaces de Registro</h3>
            <p class="text-sm text-gray-600 dark:text-zinc-400 mt-1">Crea enlaces personalizados para nuevos clientes con planes preseleccionados</p>
          </div>
          
          <div class="p-6">
            <div v-if="!generatedLink" class="grid grid-cols-1 lg:grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-bold text-gray-700 dark:text-zinc-300 mb-2">Seleccionar Plan:</label>
                <select v-model="selectedPlan" class="w-full px-3 py-3 text-sm border border-gray-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 text-gray-700 dark:text-zinc-300 rounded-lg font-medium focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400">
                  <option value="free_trial">🎁 FREE TRIAL - Prueba Gratis (7 días)</option>
                  <option value="basic">💼 BASIC - Plan Básico ($29/mes)</option>
                  <option value="premium">⭐ PREMIUM - Plan Premium ($79/mes)</option>
                  <option value="enterprise">🏢 ENTERPRISE - Empresarial ($199/mes)</option>
                </select>
              </div>
              
              <div class="flex items-end">
                <button @click="generateSignupLink" class="w-full px-6 py-3 bg-slate-900 dark:bg-slate-700 hover:bg-black dark:hover:bg-slate-600 text-white text-sm font-bold rounded-xl shadow-lg shadow-slate-400/40 dark:shadow-slate-900/50 transition-all duration-300">
                  <svg class="w-5 h-5 inline-block mr-2 -mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path>
                  </svg>
                  Generar Enlace
                </button>
              </div>
            </div>

            <div v-else class="space-y-4">
              <div class="bg-emerald-50 dark:bg-emerald-950 border border-emerald-100 dark:border-emerald-800 rounded-xl p-4">
                <div class="flex items-start gap-3 mb-3">
                  <div class="w-10 h-10 rounded-lg bg-emerald-600 dark:bg-emerald-500 flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                  </div>
                  <div class="flex-1">
                    <p class="text-sm font-bold text-emerald-700 dark:text-emerald-400">Enlace generado exitosamente</p>
                    <p class="text-xs text-emerald-600 dark:text-emerald-500 mt-1">Plan: {{ generatedLink.plan.toUpperCase() }} • Expira: {{ new Date(generatedLink.expires_at).toLocaleString('es-ES') }}</p>
                  </div>
                </div>
                
                <div class="bg-white dark:bg-zinc-900 rounded-lg p-3 border border-emerald-100 dark:border-emerald-900">
                  <p class="text-xs text-gray-500 dark:text-zinc-500 mb-1 font-medium">URL de Registro:</p>
                  <p class="text-sm text-blue-600 dark:text-blue-400 font-mono break-all">{{ generatedLink.url }}</p>
                </div>
              </div>

              <div class="flex gap-3">
                <button @click="generatedLink = null; selectedPlan = 'basic'" class="flex-1 px-5 py-2.5 bg-white dark:bg-zinc-900 hover:bg-slate-50 dark:hover:bg-zinc-800 text-slate-600 dark:text-zinc-200 text-sm font-bold rounded-xl border border-slate-200 dark:border-zinc-800 shadow-sm transition-all duration-200">
                  Generar Otro
                </button>
                <button @click="copyLinkToClipboard" class="flex-1 px-6 py-2.5 bg-slate-900 dark:bg-slate-700 hover:bg-black dark:hover:bg-slate-600 text-white text-sm font-bold rounded-xl shadow-lg shadow-slate-400/40 dark:shadow-slate-900/50 transition-all duration-300">
                  <svg class="w-4 h-4 inline-block mr-2 -mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                  </svg>
                  Copiar Enlace
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- TAB: Clientes -->
      <div v-show="activeTab === 'clientes'" class="space-y-6">
        <!-- Panel de tabla -->
        <div class="bg-white dark:bg-zinc-900 rounded-2xl shadow-xl dark:shadow-black/50 border border-gray-300 dark:border-zinc-800">
          <div class="bg-gray-50 dark:bg-zinc-900 border-b border-gray-200 dark:border-zinc-800 px-6 py-4">
            <div class="flex items-center justify-between">
              <div>
                <h2 class="text-lg font-bold text-gray-900 dark:text-white">Gestión de Clientes</h2>
                <p class="text-sm text-gray-600 dark:text-zinc-400 mt-1">{{ tenants.total }} tenants registrados en el sistema</p>
              </div>
              <button @click="showCreateModal = true" class="px-6 py-2.5 bg-slate-900 dark:bg-slate-700 hover:bg-black dark:hover:bg-slate-600 text-white text-sm font-bold rounded-xl shadow-lg shadow-slate-400/40 dark:shadow-slate-900/50 transition-all duration-300">
                <svg class="w-4 h-4 inline-block mr-2 -mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Nueva Tienda
              </button>
            </div>
          </div>

          <!-- Filtros -->
          <div class="px-6 py-4 bg-white dark:bg-zinc-900 border-b border-gray-200 dark:border-zinc-800">
            <div class="relative">
              <input 
                v-model="searchQuery" 
                type="text" 
                placeholder="Buscar por nombre, ID o dominio..." 
                class="pl-10 pr-4 py-3 text-sm rounded-xl border-2 border-gray-200 dark:border-zinc-700 bg-gray-50 dark:bg-zinc-800 text-gray-900 dark:text-zinc-200 placeholder-gray-400 dark:placeholder-zinc-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent w-full"
              >
              <svg class="w-5 h-5 text-gray-400 dark:text-zinc-500 absolute left-3 top-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
              </svg>
            </div>
          </div>

          <!-- Tabla -->
          <div class="overflow-x-auto">
            <table class="w-full">
              <thead class="border-b border-gray-200 dark:border-zinc-800">
                <tr>
                  <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 dark:text-zinc-400 uppercase tracking-wider">Negocio</th>
                  <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 dark:text-zinc-400 uppercase tracking-wider">Dominio</th>
                  <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 dark:text-zinc-400 uppercase tracking-wider">Plan</th>
                  <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 dark:text-zinc-400 uppercase tracking-wider">Estado</th>
                  <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 dark:text-zinc-400 uppercase tracking-wider">Creación</th>
                  <th class="px-6 py-4 text-center text-xs font-bold text-gray-600 dark:text-zinc-400 uppercase tracking-wider">Acciones</th>
                </tr>
              </thead>
              <tbody class="bg-white dark:bg-zinc-900">
                <tr v-for="tenant in filteredTenants" :key="tenant.id" class="hover:bg-gray-50 dark:hover:bg-zinc-800/50 transition-colors duration-200 border-b border-gray-100 dark:border-zinc-800">
                  <td class="px-6 py-4">
                    <div>
                      <p class="text-sm font-bold text-gray-900 dark:text-white">{{ tenant.business_name }}</p>
                      <p class="text-xs text-gray-500 dark:text-zinc-500 font-mono">{{ tenant.id }}</p>
                    </div>
                  </td>
                  <td class="px-6 py-4">
                    <a :href="'http://' + tenant.primary_domain" target="_blank" class="text-sm text-blue-600 dark:text-blue-400 hover:underline">
                      {{ tenant.primary_domain }}
                    </a>
                  </td>
                  <td class="px-6 py-4">
                    <span :class="getPlanBadge(tenant.plan)" class="px-2.5 py-1 rounded-full text-[10px] font-bold border uppercase tracking-wide">
                      {{ tenant.plan.replace('_', ' ') }}
                    </span>
                  </td>
                  <td class="px-6 py-4">
                    <span :class="getStatusBadge(tenant.status)" class="px-2.5 py-1 rounded-full text-[10px] font-bold border uppercase tracking-wide">
                      {{ tenant.status === 'active' ? 'ACTIVO' : tenant.status === 'paused' ? 'PAUSADO' : 'SUSPENDIDO' }}
                    </span>
                  </td>
                  <td class="px-6 py-4">
                    <p class="text-sm text-gray-600 dark:text-zinc-400">{{ new Date(tenant.created_at).toLocaleDateString('es-ES') }}</p>
                  </td>
                  <td class="px-6 py-4">
                    <div class="flex items-center justify-center gap-2">
                      <button @click="openStoreConfig(tenant)" class="p-2 text-slate-400 dark:text-zinc-400 hover:text-purple-600 dark:hover:text-purple-400 hover:bg-purple-50 dark:hover:bg-purple-900/20 rounded-lg border border-transparent hover:border-purple-100 dark:hover:border-purple-900/30 transition-all" title="Configurar">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                      </button>
                      <button @click="viewTenantDetails(tenant)" class="p-2 text-slate-400 dark:text-zinc-400 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-lg border border-transparent hover:border-blue-100 dark:hover:border-blue-900/30 transition-all" title="Ver detalles">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                        </svg>
                      </button>
                      <button @click="toggleTenantStatus(tenant.id, tenant.status)" :class="tenant.status === 'active' ? 'hover:text-amber-600 dark:hover:text-amber-400 hover:bg-amber-50 dark:hover:bg-amber-900/20 hover:border-amber-100 dark:hover:border-amber-900/30' : 'hover:text-emerald-600 dark:hover:text-emerald-400 hover:bg-emerald-50 dark:hover:bg-emerald-900/20 hover:border-emerald-100 dark:hover:border-emerald-900/30'" class="p-2 text-slate-400 dark:text-zinc-400 rounded-lg border border-transparent transition-all" :title="tenant.status === 'active' ? 'Pausar' : 'Activar'">
                        <svg v-if="tenant.status === 'active'" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path>
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                      </button>
                      <a :href="'http://' + tenant.primary_domain + '/login'" target="_blank" class="p-2 text-slate-400 dark:text-zinc-400 hover:text-emerald-600 dark:hover:text-emerald-400 hover:bg-emerald-50 dark:hover:bg-emerald-900/20 rounded-lg border border-transparent hover:border-emerald-100 dark:hover:border-emerald-900/30 transition-all" title="Acceder">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                        </svg>
                      </a>
                      <button @click="confirmDelete(tenant)" class="p-2 text-slate-400 dark:text-zinc-500 hover:text-rose-600 dark:hover:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-900/20 rounded-lg border border-transparent hover:border-rose-100 dark:hover:border-rose-900/30 transition-all" title="Eliminar">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                      </button>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <!-- TAB: AI Monitoring -->
      <div v-show="activeTab === 'ai-monitoring'" class="space-y-6">
        <!-- KPIs Resumen IA -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
          <div class="bg-white dark:bg-zinc-900/80 backdrop-blur-sm rounded-xl px-4 py-3 border border-gray-300 dark:border-zinc-800/60 hover:border-gray-400 dark:hover:border-zinc-700/80 transition-all duration-200 hover:shadow-md dark:shadow-lg dark:shadow-black/30">
            <div class="flex items-center gap-3">
              <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0 bg-gray-100 dark:bg-zinc-800/50 border border-gray-200 dark:border-white/5">
                <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path>
                </svg>
              </div>
              <div class="flex-1 min-w-0">
                <p class="text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wide">Total Requests</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white mt-0.5">{{ formatNumber(aiMonitoring.summary?.total_requests || 0) }}</p>
              </div>
            </div>
          </div>

          <div class="bg-white dark:bg-zinc-900/80 backdrop-blur-sm rounded-xl px-4 py-3 border border-gray-300 dark:border-zinc-800/60 hover:border-gray-400 dark:hover:border-zinc-700/80 transition-all duration-200 hover:shadow-md dark:shadow-lg dark:shadow-black/30">
            <div class="flex items-center gap-3">
              <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0 bg-gray-100 dark:bg-zinc-800/50 border border-gray-200 dark:border-white/5">
                <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
              </div>
              <div class="flex-1 min-w-0">
                <p class="text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wide">Success Rate</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white mt-0.5">{{ aiMonitoring.summary?.success_rate || 0 }}%</p>
              </div>
            </div>
          </div>

          <div class="bg-white dark:bg-zinc-900/80 backdrop-blur-sm rounded-xl px-4 py-3 border border-gray-300 dark:border-zinc-800/60 hover:border-gray-400 dark:hover:border-zinc-700/80 transition-all duration-200 hover:shadow-md dark:shadow-lg dark:shadow-black/30">
            <div class="flex items-center gap-3">
              <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0 bg-gray-100 dark:bg-zinc-800/50 border border-gray-200 dark:border-white/5">
                <svg class="w-5 h-5 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                </svg>
              </div>
              <div class="flex-1 min-w-0">
                <p class="text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wide">Total Tokens</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white mt-0.5">{{ formatNumber(aiMonitoring.summary?.total_tokens || 0) }}</p>
              </div>
            </div>
          </div>

          <div class="bg-white dark:bg-zinc-900/80 backdrop-blur-sm rounded-xl px-4 py-3 border border-gray-300 dark:border-zinc-800/60 hover:border-gray-400 dark:hover:border-zinc-700/80 transition-all duration-200 hover:shadow-md dark:shadow-lg dark:shadow-black/30">
            <div class="flex items-center gap-3">
              <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0 bg-gray-100 dark:bg-zinc-800/50 border border-gray-200 dark:border-white/5">
                <svg class="w-5 h-5 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
              </div>
              <div class="flex-1 min-w-0">
                <p class="text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wide">Avg Response</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white mt-0.5">{{ aiMonitoring.summary?.avg_response_time_ms || 0 }}ms</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Tabla de API Keys -->
        <div class="bg-white dark:bg-zinc-900 rounded-2xl shadow-xl dark:shadow-black/50 border border-gray-300 dark:border-zinc-800 overflow-hidden">
          <div class="bg-gray-50 dark:bg-zinc-900 px-6 py-4 border-b border-gray-200 dark:border-zinc-800 flex items-center justify-between">
            <div>
              <h3 class="text-base font-bold text-gray-900 dark:text-white">API Keys Status</h3>
              <p class="text-xs text-gray-600 dark:text-zinc-400 mt-0.5">Monitoreo de uso por clave de API (Groq)</p>
            </div>
            <div class="flex items-center gap-2">
              <select 
                v-model="aiPeriod" 
                @change="fetchAIMonitoring"
                class="px-3 py-2 text-xs border border-gray-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 text-gray-700 dark:text-zinc-300 rounded-lg font-medium focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400"
              >
                <option value="24h">Últimas 24h</option>
                <option value="7d">Últimos 7 días</option>
                <option value="30d">Últimos 30 días</option>
                <option value="all">Todo</option>
              </select>
            </div>
          </div>

          <div class="overflow-x-auto">
            <table class="w-full">
              <thead class="border-b border-gray-200 dark:border-zinc-800">
                <tr class="bg-white dark:bg-zinc-900">
                  <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 dark:text-zinc-300 uppercase tracking-wider">Key #</th>
                  <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 dark:text-zinc-300 uppercase tracking-wider">Last 4</th>
                  <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 dark:text-zinc-300 uppercase tracking-wider">Total Requests</th>
                  <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 dark:text-zinc-300 uppercase tracking-wider">Success</th>
                  <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 dark:text-zinc-300 uppercase tracking-wider">Rate Limited</th>
                  <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 dark:text-zinc-300 uppercase tracking-wider">Errors</th>
                  <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 dark:text-zinc-300 uppercase tracking-wider">Total Tokens</th>
                  <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 dark:text-zinc-300 uppercase tracking-wider">Avg Time</th>
                  <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 dark:text-zinc-300 uppercase tracking-wider">Status</th>
                </tr>
              </thead>
              <tbody class="bg-white dark:bg-zinc-900">
                <tr 
                  v-for="key in aiMonitoring.keys_status" 
                  :key="key.key_index"
                  class="border-b border-gray-100 dark:border-zinc-800 hover:bg-gray-50 dark:hover:bg-zinc-800/50 transition-colors duration-200"
                >
                  <td class="px-6 py-4 whitespace-nowrap">
                    <span class="px-2 py-1 bg-slate-900 dark:bg-slate-700 text-white text-xs font-bold rounded">Key #{{ key.key_index }}</span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm font-mono text-gray-700 dark:text-zinc-300">...{{ key.key_last_4 }}</td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900 dark:text-white">{{ formatNumber(key.total_requests) }}</td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-emerald-600 dark:text-emerald-400 font-semibold">{{ formatNumber(key.successful) }}</td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-amber-600 dark:text-amber-400 font-semibold">{{ formatNumber(key.rate_limited) }}</td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-rose-600 dark:text-rose-400 font-semibold">{{ formatNumber(key.errors) }}</td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-purple-600 dark:text-purple-400">{{ formatNumber(key.total_tokens) }}</td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-zinc-300">{{ key.avg_response_time }}ms</td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <span 
                      :class="[
                        'px-2.5 py-1 rounded-full text-[10px] font-bold border uppercase tracking-wide',
                        key.status === 'active' 
                          ? 'bg-emerald-50 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-400 border-emerald-100 dark:border-emerald-800'
                          : 'bg-rose-50 dark:bg-rose-950 text-rose-700 dark:text-rose-400 border-rose-100 dark:border-rose-800'
                      ]"
                    >
                      {{ key.status }}
                    </span>
                  </td>
                </tr>
                <tr v-if="aiMonitoring.keys_status?.length === 0">
                  <td colspan="9" class="px-6 py-8 text-center text-sm text-gray-500 dark:text-zinc-400">
                    No hay datos de API keys configuradas
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <!-- Tabla de Peticiones Recientes -->
        <div class="bg-white dark:bg-zinc-900 rounded-2xl shadow-xl dark:shadow-black/50 border border-gray-300 dark:border-zinc-800 overflow-hidden">
          <div class="bg-gray-50 dark:bg-zinc-900 px-6 py-4 border-b border-gray-200 dark:border-zinc-800">
            <h3 class="text-base font-bold text-gray-900 dark:text-white">Peticiones Recientes</h3>
            <p class="text-xs text-gray-600 dark:text-zinc-400 mt-0.5">Últimas {{ aiMonitoring.recent_requests?.length || 0 }} peticiones a la IA</p>
          </div>

          <div class="overflow-x-auto">
            <table class="w-full">
              <thead class="border-b border-gray-200 dark:border-zinc-800">
                <tr class="bg-white dark:bg-zinc-900">
                  <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 dark:text-zinc-300 uppercase tracking-wider">Fecha/Hora</th>
                  <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 dark:text-zinc-300 uppercase tracking-wider">Tenant</th>
                  <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 dark:text-zinc-300 uppercase tracking-wider">Mensaje</th>
                  <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 dark:text-zinc-300 uppercase tracking-wider">Tokens</th>
                  <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 dark:text-zinc-300 uppercase tracking-wider">Estado</th>
                </tr>
              </thead>
              <tbody class="bg-white dark:bg-zinc-900">
                <tr 
                  v-for="(req, index) in aiMonitoring.recent_requests" 
                  :key="index"
                  class="border-b border-gray-100 dark:border-zinc-800 hover:bg-gray-50 dark:hover:bg-zinc-800/50 transition-colors duration-200"
                >
                  <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-600 dark:text-zinc-400 font-mono">
                    {{ formatDateTime(req.created_at) }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <span class="px-2 py-1 bg-blue-50 dark:bg-blue-950 text-blue-700 dark:text-blue-400 border border-blue-100 dark:border-blue-800 text-xs font-medium rounded">
                      {{ req.tenant?.replace('tenant', '') || 'N/A' }}
                    </span>
                  </td>
                  <td class="px-6 py-4 text-sm text-gray-700 dark:text-zinc-300 max-w-md truncate">
                    {{ req.message || 'Sin mensaje' }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <span class="px-2.5 py-1 bg-purple-50 dark:bg-purple-950 text-purple-700 dark:text-purple-400 border border-purple-100 dark:border-purple-800 text-xs font-bold rounded-full">
                      {{ formatNumber(req.tokens || 0) }}
                    </span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <span 
                      :class="[
                        'px-2.5 py-1 rounded-full text-[10px] font-bold border uppercase tracking-wide',
                        req.status === 'success' 
                          ? 'bg-emerald-50 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-400 border-emerald-100 dark:border-emerald-800'
                          : req.status === 'rate_limited'
                          ? 'bg-amber-50 dark:bg-amber-950 text-amber-700 dark:text-amber-400 border-amber-100 dark:border-amber-800'
                          : 'bg-rose-50 dark:bg-rose-950 text-rose-700 dark:text-rose-400 border-rose-100 dark:border-rose-800'
                      ]"
                    >
                      {{ req.status }}
                    </span>
                  </td>
                </tr>
                <tr v-if="!aiMonitoring.recent_requests || aiMonitoring.recent_requests.length === 0">
                  <td colspan="5" class="px-6 py-8 text-center text-sm text-gray-500 dark:text-zinc-400">
                    No hay peticiones recientes en el período seleccionado
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <!-- TAB: System Logs -->
      <div v-show="activeTab === 'logs'" class="space-y-6">
        <div class="bg-white dark:bg-zinc-900 rounded-2xl shadow-xl dark:shadow-black/50 border border-gray-300 dark:border-zinc-800 p-8 text-center">
          <div class="w-16 h-16 bg-gray-100 dark:bg-zinc-800 rounded-xl flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8 text-gray-600 dark:text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
          </div>
          <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Logs del Sistema</h3>
          <p class="text-sm text-gray-600 dark:text-zinc-400 mb-6">Visor de logs de Laravel con filtros por nivel (error, warning, info), fecha y tenant específico.</p>
          <span class="px-3 py-1.5 bg-amber-50 dark:bg-amber-950 text-amber-700 dark:text-amber-400 border border-amber-100 dark:border-amber-800 text-xs font-bold rounded-lg uppercase tracking-wide">En desarrollo</span>
        </div>
      </div>

      <!-- Modal: Crear Tienda -->
      <CreateTenantModal 
        v-if="showCreateModal"
        :newTenant="newTenant"
        @close="showCreateModal = false"
        @create="createTenant"
      />

      <!-- Modal: Detalles de Tienda -->
      <TenantDetailsModal
        v-if="showDetailsModal && selectedTenant"
        :tenant="selectedTenant"
        @close="showDetailsModal = false"
        @update-plan="updateTenantPlan"
        @toggle-status="toggleTenantStatus"
      />

      <!-- Modal: Confirmar Eliminación -->
      <DeleteTenantModal
        v-if="showDeleteModal && tenantToDelete"
        :tenant="tenantToDelete"
        @close="showDeleteModal = false; tenantToDelete = null"
        @confirm="deleteTenant"
      />

      <!-- Modal: Configurar Tienda -->
      <StoreConfigModal
        v-if="showConfigModal && selectedTenant"
        :tenant="selectedTenant"
        @close="showConfigModal = false"
        @refresh="fetchData"
      />
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import axios from 'axios'
import CreateTenantModal from './CreateTenantModal.vue'
import TenantDetailsModal from './TenantDetailsModal.vue'
import DeleteTenantModal from './DeleteTenantModal.vue'
import StoreConfigModal from './StoreConfigModal.vue'

// Estados
const loading = ref(false)
const activeTab = ref('dashboard')
const kpis = ref({ 
  total_active_clients: 0, 
  clients_created_today: 0, 
  mrr: 0, 
  ai_tokens_this_month: 0, 
  ai_cost_this_month: 0 
})
const tenants = ref({ data: [], total: 0 })
const selectedTenant = ref(null)
const showCreateModal = ref(false)
const showDetailsModal = ref(false)
const showDeleteModal = ref(false)
const showConfigModal = ref(false)
const tenantToDelete = ref(null)
const searchQuery = ref('')
const selectedPlan = ref('basic')
const generatedLink = ref(null)

// AI Monitoring
const aiMonitoring = ref({
  summary: {
    total_requests: 0,
    successful: 0,
    rate_limited: 0,
    errors: 0,
    success_rate: 0,
    total_tokens: 0,
    avg_response_time_ms: 0
  },
  keys_status: [],
  usage_by_hour: [],
  recent_requests: [],
  top_users: []
})
const aiPeriod = ref('24h')

// Formulario de creación
const newTenant = ref({
  tenant_id: '',
  business_name: '',
  domain: '',
  plan: 'free_trial'
})

// Auto-refresh interval
let refreshInterval = null

// Métodos
const fetchData = async () => {
  loading.value = true
  try {
    const [kpisRes, tenantsRes] = await Promise.all([
      axios.get('/admin/api/kpis'),
      axios.get('/admin/api/tenants')
    ])
    if (kpisRes.data.success) kpis.value = kpisRes.data.data
    if (tenantsRes.data.success) tenants.value = tenantsRes.data.data
  } catch (error) {
    console.error('Error:', error)
    alert('Error al cargar datos')
  }
  loading.value = false
}

const fetchAIMonitoring = async () => {
  loading.value = true
  try {
    const res = await axios.get(`/admin/api/ai-monitoring/dashboard?period=${aiPeriod.value}`)
    if (res.data) {
      aiMonitoring.value = res.data
    }
  } catch (error) {
    console.error('Error al cargar AI Monitoring:', error)
  }
  loading.value = false
}

const createTenant = async () => {
  try {
    const res = await axios.post('/admin/api/tenants', newTenant.value)
    if (res.data.success) {
      alert('✅ ' + res.data.message)
      showCreateModal.value = false
      newTenant.value = { tenant_id: '', business_name: '', domain: '', plan: 'free_trial' }
      fetchData()
    }
  } catch (error) {
    alert('❌ Error: ' + (error.response?.data?.message || error.message))
  }
}

const openStoreConfig = (tenant) => {
  selectedTenant.value = tenant
  showConfigModal.value = true
}

const viewTenantDetails = async (tenant) => {
  try {
    const res = await axios.get(`/admin/api/tenants/${tenant.id}`)
    if (res.data.success) {
      selectedTenant.value = res.data.data
      showDetailsModal.value = true
    }
  } catch (error) {
    alert('❌ Error al cargar detalles: ' + error.message)
  }
}

const updateTenantPlan = async (tenantId, newPlan) => {
  try {
    const res = await axios.put(`/admin/api/tenants/${tenantId}`, { plan: newPlan })
    if (res.data.success) {
      alert('✅ Plan actualizado exitosamente')
      fetchData()
      if (selectedTenant.value?.id === tenantId) {
        selectedTenant.value.plan = newPlan
      }
    }
  } catch (error) {
    alert('❌ Error: ' + (error.response?.data?.message || error.message))
  }
}

const toggleTenantStatus = async (tenantId, currentStatus) => {
  const newStatus = currentStatus === 'active' ? 'paused' : 'active'
  try {
    const res = await axios.put(`/admin/api/tenants/${tenantId}`, { status: newStatus })
    if (res.data.success) {
      alert(`✅ Tienda ${newStatus === 'paused' ? 'pausada' : 'activada'} exitosamente`)
      fetchData()
      if (selectedTenant.value?.id === tenantId) {
        selectedTenant.value.status = newStatus
      }
    }
  } catch (error) {
    alert('❌ Error: ' + (error.response?.data?.message || error.message))
  }
}

const confirmDelete = (tenant) => {
  tenantToDelete.value = tenant
  showDeleteModal.value = true
}

const deleteTenant = async () => {
  if (!tenantToDelete.value) return

  try {
    const res = await axios.delete(`/admin/api/tenants/${tenantToDelete.value.id}`)
    if (res.data.success) {
      alert('✅ ' + res.data.message)
      showDeleteModal.value = false
      tenantToDelete.value = null
      fetchData()
    }
  } catch (error) {
    alert('❌ Error: ' + (error.response?.data?.message || error.message))
  }
}

const generateSignupLink = async () => {
  try {
    const res = await axios.post('/admin/api/generate-signup-link', {
      plan: selectedPlan.value
    })
    if (res.data.success) {
      generatedLink.value = res.data.data
    }
  } catch (error) {
    alert('❌ Error al generar link: ' + (error.response?.data?.message || error.message))
  }
}

const copyLinkToClipboard = async () => {
  try {
    await navigator.clipboard.writeText(generatedLink.value.url)
    alert('✅ Link copiado al portapapeles!')
  } catch (error) {
    alert('❌ Error al copiar: ' + error.message)
  }
}

const formatNumber = (num) => new Intl.NumberFormat('es-ES').format(num)
const formatCurrency = (num) => '$' + new Intl.NumberFormat('es-ES', { minimumFractionDigits: 2 }).format(num)
const formatDateTime = (dateString) => {
  if (!dateString) return 'N/A'
  const date = new Date(dateString)
  return date.toLocaleString('es-ES', { 
    year: 'numeric', 
    month: '2-digit', 
    day: '2-digit', 
    hour: '2-digit', 
    minute: '2-digit',
    second: '2-digit'
  })
}

const getStatusBadge = (status) => {
  const badges = {
    'active': 'bg-emerald-50 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-400 border-emerald-100 dark:border-emerald-800',
    'paused': 'bg-amber-50 dark:bg-amber-950 text-amber-700 dark:text-amber-400 border-amber-100 dark:border-amber-800',
    'suspended': 'bg-rose-50 dark:bg-rose-950 text-rose-700 dark:text-rose-400 border-rose-100 dark:border-rose-800'
  }
  return badges[status] || badges.active
}

const getPlanBadge = (plan) => {
  const badges = {
    'free_trial': 'bg-gray-50 dark:bg-gray-900 text-gray-700 dark:text-gray-300 border-gray-100 dark:border-gray-800',
    'basic': 'bg-blue-50 dark:bg-blue-950 text-blue-700 dark:text-blue-400 border-blue-100 dark:border-blue-800',
    'premium': 'bg-purple-50 dark:bg-purple-950 text-purple-700 dark:text-purple-400 border-purple-100 dark:border-purple-800',
    'enterprise': 'bg-amber-50 dark:bg-amber-950 text-amber-700 dark:text-amber-400 border-amber-100 dark:border-amber-800'
  }
  return badges[plan] || badges.free_trial
}

const filteredTenants = computed(() => {
  if (!searchQuery.value) return tenants.value.data
  return tenants.value.data.filter(t =>
    t.business_name.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
    t.id.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
    t.primary_domain.toLowerCase().includes(searchQuery.value.toLowerCase())
  )
})

onMounted(() => {
  fetchData()
  fetchAIMonitoring() // Cargar datos de AI
  refreshInterval = setInterval(() => {
    fetchData()
    if (activeTab.value === 'ai-monitoring') {
      fetchAIMonitoring()
    }
  }, 60000) // Auto-refresh cada minuto
})

onUnmounted(() => {
  if (refreshInterval) {
    clearInterval(refreshInterval)
  }
})
</script>

<style scoped>
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
</style>
