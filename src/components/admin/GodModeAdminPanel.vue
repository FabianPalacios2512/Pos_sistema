<template>
  <!-- Layout con Sidebar Profesional - 100% MOBILE RESPONSIVE -->
  <div class="flex min-h-screen font-sans">
    
    <!-- ========== OVERLAY MÓVIL (cuando sidebar está abierto) ========== -->
    <div 
      v-if="sidebarOpen" 
      @click="sidebarOpen = false"
      class="fixed inset-0 bg-black/60 backdrop-blur-sm z-40 lg:hidden"
    ></div>
    
    <!-- ========== SIDEBAR IZQUIERDO ========== -->
    <aside 
      :class="[
        'w-64 bg-white dark:bg-zinc-900 border-r border-gray-200 dark:border-zinc-800 flex flex-col fixed left-0 top-0 h-screen z-50 transition-transform duration-300',
        sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'
      ]"
    >
      <!-- Logo/Brand -->
      <div class="p-4 lg:p-6 border-b border-gray-200 dark:border-zinc-800">
        <div class="flex items-center justify-between">
          <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-slate-900 to-slate-700 dark:from-slate-700 dark:to-slate-900 flex items-center justify-center shadow-lg">
              <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
              </svg>
            </div>
            <div>
              <h1 class="text-lg font-bold text-gray-900 dark:text-white">105POS</h1>
              <div class="flex items-center gap-1.5">
                <span class="text-xs text-gray-500 dark:text-zinc-400">Admin</span>
                <span class="px-1.5 py-0.5 bg-red-100 dark:bg-red-950 text-red-600 dark:text-red-400 text-[9px] font-bold rounded uppercase animate-pulse">GOD</span>
              </div>
            </div>
          </div>
          <!-- Botón cerrar sidebar en móvil -->
          <button 
            @click="sidebarOpen = false"
            class="lg:hidden p-2 text-gray-500 hover:text-gray-700 dark:text-zinc-400 dark:hover:text-white"
          >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
          </button>
        </div>
      </div>

      <!-- Navegación Principal -->
      <nav class="flex-1 p-4 space-y-1.5 overflow-y-auto">
        <p class="text-[10px] font-bold text-gray-400 dark:text-zinc-500 uppercase tracking-widest px-3 mb-3">Principal</p>
        
        <button 
          @click="activeTab = 'dashboard'; sidebarOpen = false"
          :class="[
            'w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-200',
            activeTab === 'dashboard' 
              ? 'bg-slate-900 dark:bg-slate-700 text-white shadow-lg shadow-slate-500/20' 
              : 'text-gray-600 dark:text-zinc-400 hover:bg-gray-100 dark:hover:bg-zinc-800 hover:text-gray-900 dark:hover:text-white'
          ]"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z"/>
          </svg>
          <span>Dashboard</span>
        </button>

        <button 
          @click="activeTab = 'clientes'; sidebarOpen = false"
          :class="[
            'w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-200',
            activeTab === 'clientes' 
              ? 'bg-slate-900 dark:bg-slate-700 text-white shadow-lg shadow-slate-500/20' 
              : 'text-gray-600 dark:text-zinc-400 hover:bg-gray-100 dark:hover:bg-zinc-800 hover:text-gray-900 dark:hover:text-white'
          ]"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
          </svg>
          <span>Clientes</span>
          <span class="ml-auto bg-blue-100 dark:bg-blue-950 text-blue-700 dark:text-blue-400 text-[10px] font-bold px-2 py-0.5 rounded-full">{{ tenants.length || 0 }}</span>
        </button>

        <p class="text-[10px] font-bold text-gray-400 dark:text-zinc-500 uppercase tracking-widest px-3 mb-3 mt-6">Monitoreo</p>

        <button 
          @click="activeTab = 'ai-monitoring'; sidebarOpen = false"
          :class="[
            'w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-200',
            activeTab === 'ai-monitoring' 
              ? 'bg-slate-900 dark:bg-slate-700 text-white shadow-lg shadow-slate-500/20' 
              : 'text-gray-600 dark:text-zinc-400 hover:bg-gray-100 dark:hover:bg-zinc-800 hover:text-gray-900 dark:hover:text-white'
          ]"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
          </svg>
          <span>AI Monitoring</span>
        </button>

        <button 
          @click="activeTab = 'logs'; sidebarOpen = false"
          :class="[
            'w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-200',
            activeTab === 'logs' 
              ? 'bg-slate-900 dark:bg-slate-700 text-white shadow-lg shadow-slate-500/20' 
              : 'text-gray-600 dark:text-zinc-400 hover:bg-gray-100 dark:hover:bg-zinc-800 hover:text-gray-900 dark:hover:text-white'
          ]"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
          </svg>
          <span>System Logs</span>
        </button>
      </nav>

      <!-- Footer Sidebar -->
      <div class="p-4 border-t border-gray-200 dark:border-zinc-800">
        <a 
          href="/dashboard"
          class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-gray-600 dark:text-zinc-400 hover:bg-gray-100 dark:hover:bg-zinc-800 transition-all"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12"/>
          </svg>
          <span>Volver al POS</span>
        </a>
      </div>
    </aside>

    <!-- ========== CONTENIDO PRINCIPAL ========== -->
    <main class="lg:ml-64 flex-1 bg-gradient-to-b from-gray-50 via-gray-100 to-gray-200 dark:bg-gradient-to-b dark:from-[#141417] dark:via-slate-900 dark:to-[#0a0a0c] transition-colors duration-300 min-h-screen">
      <div class="p-4 lg:p-6 xl:p-8 space-y-4 lg:space-y-6 animate-fade-in">
        
        <!-- Header con Título y Acciones - MOBILE OPTIMIZADO -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
          <div class="flex items-center gap-3">
            <!-- Hamburger Menu para móvil -->
            <button 
              @click="sidebarOpen = true"
              class="lg:hidden p-2.5 bg-white dark:bg-zinc-800 rounded-xl border border-gray-200 dark:border-zinc-700 shadow-sm"
            >
              <svg class="w-5 h-5 text-gray-600 dark:text-zinc-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
              </svg>
            </button>
            <div>
              <h1 class="text-xl lg:text-2xl font-bold text-gray-900 dark:text-white">
                {{ activeTab === 'dashboard' ? 'Dashboard' : activeTab === 'clientes' ? 'Clientes' : activeTab === 'ai-monitoring' ? 'AI Monitor' : 'Logs' }}
              </h1>
              <p class="text-xs lg:text-sm text-gray-600 dark:text-zinc-400 mt-0.5 flex items-center gap-2">
                <span class="inline-flex h-2 w-2 rounded-full bg-emerald-500 animate-pulse"></span>
                <span class="hidden sm:inline">Sistema operativo •</span> {{ currentDateTime }}
              </p>
            </div>
          </div>
          
          <div class="flex items-center gap-2 lg:gap-3">
            <!-- Botón Refrescar -->
            <button 
              @click="fetchData" 
              :disabled="loading"
              class="p-2.5 bg-white dark:bg-zinc-800 hover:bg-gray-50 dark:hover:bg-zinc-700 text-gray-600 dark:text-zinc-300 rounded-xl border border-gray-200 dark:border-zinc-700 shadow-sm transition-all disabled:opacity-50"
            >
              <svg class="w-5 h-5" :class="{'animate-spin': loading}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
              </svg>
            </button>
            
            <!-- Botón Generar Enlace -->
            <button 
              @click="showLinkModal = true"
              class="inline-flex items-center gap-2 px-3 lg:px-5 py-2.5 bg-slate-900 dark:bg-slate-700 hover:bg-black dark:hover:bg-slate-600 text-white text-sm font-bold rounded-xl shadow-lg shadow-slate-400/30 dark:shadow-slate-900/50 transition-all"
            >
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
              </svg>
              <span class="hidden sm:inline">Generar Enlace</span>
            </button>
          </div>
        </div>

        <!-- TAB: Dashboard -->
        <div v-show="activeTab === 'dashboard'" class="space-y-4 lg:space-y-6">
          
          <!-- KPIs Principales - Primera Fila - MOBILE GRID 2x2 -->
          <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 lg:gap-4">
            
            <!-- MRR Total -->
            <div class="bg-white dark:bg-zinc-900/80 rounded-xl lg:rounded-2xl p-3 lg:p-5 border border-gray-200 dark:border-zinc-800/60 shadow-lg shadow-gray-200/50 dark:shadow-black/30 hover:shadow-xl transition-all duration-300 group">
              <div class="flex items-start justify-between mb-2 lg:mb-3">
                <div class="w-10 h-10 lg:w-12 lg:h-12 rounded-lg lg:rounded-xl bg-emerald-50 dark:bg-emerald-950/50 flex items-center justify-center transition-transform group-hover:scale-105">
                  <svg class="w-5 h-5 lg:w-6 lg:h-6 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                  </svg>
                </div>
                <span class="hidden sm:inline-flex items-center gap-1 px-2 py-1 text-xs font-medium rounded-full bg-emerald-50 dark:bg-emerald-900/20 text-emerald-700 dark:text-emerald-400">
                  <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"/>
                  </svg>
                  +{{ mrrGrowthPercent }}%
                </span>
              </div>
              <div>
                <p class="text-[10px] lg:text-xs font-bold text-gray-500 dark:text-zinc-400 uppercase tracking-wider mb-1">MRR</p>
                <p class="text-xl lg:text-3xl font-bold text-gray-900 dark:text-white tracking-tight">${{ formatNumber(kpis.mrr || 0) }}</p>
                <p class="text-[10px] lg:text-xs text-gray-500 dark:text-zinc-500 mt-0.5 lg:mt-1 hidden sm:block">Ingreso mensual</p>
              </div>
            </div>

            <!-- Clientes Activos -->
            <div class="bg-white dark:bg-zinc-900/80 rounded-xl lg:rounded-2xl p-3 lg:p-5 border border-gray-200 dark:border-zinc-800/60 shadow-lg shadow-gray-200/50 dark:shadow-black/30 hover:shadow-xl transition-all duration-300 group">
              <div class="flex items-start justify-between mb-2 lg:mb-3">
                <div class="w-10 h-10 lg:w-12 lg:h-12 rounded-lg lg:rounded-xl bg-blue-50 dark:bg-blue-950/50 flex items-center justify-center transition-transform group-hover:scale-105">
                  <svg class="w-5 h-5 lg:w-6 lg:h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                  </svg>
                </div>
              </div>
              <div>
                <p class="text-[10px] lg:text-xs font-bold text-gray-500 dark:text-zinc-400 uppercase tracking-wider mb-1">Activos</p>
                <p class="text-xl lg:text-3xl font-bold text-gray-900 dark:text-white tracking-tight">{{ kpis.total_active_clients || 0 }}</p>
                <p class="text-[10px] lg:text-xs text-gray-500 dark:text-zinc-500 mt-0.5 lg:mt-1 hidden sm:block">Suscripciones</p>
              </div>
            </div>

            <!-- Nuevos Este Mes -->
            <div class="bg-white dark:bg-zinc-900/80 rounded-xl lg:rounded-2xl p-3 lg:p-5 border border-gray-200 dark:border-zinc-800/60 shadow-lg shadow-gray-200/50 dark:shadow-black/30 hover:shadow-xl transition-all duration-300 group">
              <div class="flex items-start justify-between mb-2 lg:mb-3">
                <div class="w-10 h-10 lg:w-12 lg:h-12 rounded-lg lg:rounded-xl bg-purple-50 dark:bg-purple-950/50 flex items-center justify-center transition-transform group-hover:scale-105">
                  <svg class="w-5 h-5 lg:w-6 lg:h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                  </svg>
                </div>
              </div>
              <div>
                <p class="text-[10px] lg:text-xs font-bold text-gray-500 dark:text-zinc-400 uppercase tracking-wider mb-1">Nuevos</p>
                <p class="text-xl lg:text-3xl font-bold text-gray-900 dark:text-white tracking-tight">{{ kpis.clients_this_month || kpis.clients_created_today || 0 }}</p>
                <p class="text-[10px] lg:text-xs text-gray-500 dark:text-zinc-500 mt-0.5 lg:mt-1 hidden sm:block">{{ kpis.clients_created_today || 0 }} hoy</p>
              </div>
            </div>

            <!-- ARR Proyectado -->
            <div class="bg-white dark:bg-zinc-900/80 rounded-xl lg:rounded-2xl p-3 lg:p-5 border border-gray-200 dark:border-zinc-800/60 shadow-lg shadow-gray-200/50 dark:shadow-black/30 hover:shadow-xl transition-all duration-300 group">
              <div class="flex items-start justify-between mb-2 lg:mb-3">
                <div class="w-10 h-10 lg:w-12 lg:h-12 rounded-lg lg:rounded-xl bg-amber-50 dark:bg-amber-950/50 flex items-center justify-center transition-transform group-hover:scale-105">
                  <svg class="w-5 h-5 lg:w-6 lg:h-6 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                  </svg>
                </div>
              </div>
              <div>
                <p class="text-[10px] lg:text-xs font-bold text-gray-500 dark:text-zinc-400 uppercase tracking-wider mb-1">ARR</p>
                <p class="text-xl lg:text-3xl font-bold text-gray-900 dark:text-white tracking-tight">${{ formatNumber((kpis.mrr || 0) * 12) }}</p>
                <p class="text-[10px] lg:text-xs text-gray-500 dark:text-zinc-500 mt-0.5 lg:mt-1 hidden sm:block">Anual</p>
              </div>
            </div>
          </div>

          <!-- Segunda Fila: Ingresos por Plan + Métricas - MOBILE STACKED -->
          <div class="grid grid-cols-1 xl:grid-cols-3 gap-4 lg:gap-6">
            
            <!-- Ingresos por Plan -->
            <div class="xl:col-span-2 bg-white dark:bg-zinc-900 rounded-xl lg:rounded-2xl shadow-xl dark:shadow-black/50 border border-gray-200 dark:border-zinc-800 overflow-hidden">
              <div class="px-4 lg:px-6 py-3 lg:py-4 border-b border-gray-200 dark:border-zinc-800 flex items-center justify-between">
                <div>
                  <h3 class="text-sm lg:text-base font-bold text-gray-900 dark:text-white">Distribución</h3>
                  <p class="text-[10px] lg:text-xs text-gray-600 dark:text-zinc-400 mt-0.5">MRR por plan</p>
                </div>
              </div>
              <div class="p-3 lg:p-6">
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-2 lg:gap-4">
                  <!-- Free Trial -->
                  <div class="bg-gray-50 dark:bg-zinc-800/50 rounded-lg lg:rounded-xl p-3 lg:p-4 border border-gray-200 dark:border-zinc-700">
                    <div class="flex items-center gap-2 mb-2 lg:mb-3">
                      <span class="w-2 h-2 lg:w-3 lg:h-3 rounded-full bg-gray-400"></span>
                      <span class="text-[10px] lg:text-xs font-bold text-gray-600 dark:text-zinc-400 uppercase">Trial</span>
                    </div>
                    <p class="text-lg lg:text-2xl font-bold text-gray-900 dark:text-white">{{ planStats.free_trial?.count || 0 }}</p>
                    <p class="text-[10px] lg:text-xs text-gray-500 dark:text-zinc-500 mt-1">$0/mes</p>
                  </div>
                  
                  <!-- Basic -->
                  <div class="bg-blue-50 dark:bg-blue-950/30 rounded-lg lg:rounded-xl p-3 lg:p-4 border border-blue-200 dark:border-blue-900">
                    <div class="flex items-center gap-2 mb-2 lg:mb-3">
                      <span class="w-2 h-2 lg:w-3 lg:h-3 rounded-full bg-blue-500"></span>
                      <span class="text-[10px] lg:text-xs font-bold text-blue-700 dark:text-blue-400 uppercase">Basic</span>
                    </div>
                    <p class="text-lg lg:text-2xl font-bold text-gray-900 dark:text-white">{{ planStats.basic?.count || 0 }}</p>
                    <p class="text-[10px] lg:text-xs text-blue-600 dark:text-blue-400 mt-1 font-medium">${{ formatNumber(planStats.basic?.mrr || 0) }}</p>
                  </div>
                  
                  <!-- Premium -->
                  <div class="bg-purple-50 dark:bg-purple-950/30 rounded-lg lg:rounded-xl p-3 lg:p-4 border border-purple-200 dark:border-purple-900">
                    <div class="flex items-center gap-2 mb-2 lg:mb-3">
                      <span class="w-2 h-2 lg:w-3 lg:h-3 rounded-full bg-purple-500"></span>
                      <span class="text-[10px] lg:text-xs font-bold text-purple-700 dark:text-purple-400 uppercase">Premium</span>
                    </div>
                    <p class="text-lg lg:text-2xl font-bold text-gray-900 dark:text-white">{{ planStats.premium?.count || 0 }}</p>
                    <p class="text-[10px] lg:text-xs text-purple-600 dark:text-purple-400 mt-1 font-medium">${{ formatNumber(planStats.premium?.mrr || 0) }}</p>
                  </div>
                  
                  <!-- Enterprise -->
                  <div class="bg-amber-50 dark:bg-amber-950/30 rounded-lg lg:rounded-xl p-3 lg:p-4 border border-amber-200 dark:border-amber-900">
                    <div class="flex items-center gap-2 mb-2 lg:mb-3">
                      <span class="w-2 h-2 lg:w-3 lg:h-3 rounded-full bg-amber-500"></span>
                      <span class="text-[10px] lg:text-xs font-bold text-amber-700 dark:text-amber-400 uppercase">Enterprise</span>
                    </div>
                    <p class="text-lg lg:text-2xl font-bold text-gray-900 dark:text-white">{{ planStats.enterprise?.count || 0 }}</p>
                    <p class="text-[10px] lg:text-xs text-amber-600 dark:text-amber-400 mt-1 font-medium">${{ formatNumber(planStats.enterprise?.mrr || 0) }}</p>
                  </div>
                </div>
              </div>
            </div>

            <!-- Métricas IA Resumen -->
            <div class="bg-white dark:bg-zinc-900 rounded-xl lg:rounded-2xl shadow-xl dark:shadow-black/50 border border-gray-200 dark:border-zinc-800 overflow-hidden">
              <div class="px-4 lg:px-6 py-3 lg:py-4 border-b border-gray-200 dark:border-zinc-800">
                <h3 class="text-sm lg:text-base font-bold text-gray-900 dark:text-white">Uso de IA</h3>
                <p class="text-[10px] lg:text-xs text-gray-600 dark:text-zinc-400 mt-0.5">Este mes</p>
              </div>
              <div class="p-4 lg:p-6 space-y-3 lg:space-y-4">
                <div class="flex items-center justify-between">
                  <div class="flex items-center gap-3">
                    <div class="w-9 h-9 lg:w-10 lg:h-10 rounded-lg bg-purple-100 dark:bg-purple-950/50 flex items-center justify-center">
                      <svg class="w-4 h-4 lg:w-5 lg:h-5 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                      </svg>
                    </div>
                    <div>
                      <p class="text-[10px] lg:text-xs text-gray-500 dark:text-zinc-400">Tokens</p>
                      <p class="text-base lg:text-lg font-bold text-gray-900 dark:text-white">{{ formatNumber(kpis.ai_tokens_this_month || 0) }}</p>
                    </div>
                  </div>
                </div>
                
                <div class="flex items-center justify-between">
                  <div class="flex items-center gap-3">
                    <div class="w-9 h-9 lg:w-10 lg:h-10 rounded-lg bg-emerald-100 dark:bg-emerald-950/50 flex items-center justify-center">
                      <svg class="w-4 h-4 lg:w-5 lg:h-5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                      </svg>
                    </div>
                    <div>
                      <p class="text-[10px] lg:text-xs text-gray-500 dark:text-zinc-400">Costo</p>
                      <p class="text-base lg:text-lg font-bold text-gray-900 dark:text-white">${{ formatNumber(kpis.ai_cost_this_month || 0) }}</p>
                    </div>
                  </div>
                </div>

                <button 
                  @click="activeTab = 'ai-monitoring'"
                  class="w-full mt-2 px-4 py-2 lg:py-2.5 text-xs lg:text-sm font-medium bg-gray-100 dark:bg-zinc-800 text-gray-700 dark:text-zinc-300 rounded-lg lg:rounded-xl hover:bg-gray-200 dark:hover:bg-zinc-700 transition-all"
                >
                  Ver Detalles →
                </button>
              </div>
            </div>
          </div>

          <!-- Tercera Fila: Últimos Clientes -->
          <div class="bg-white dark:bg-zinc-900 rounded-2xl shadow-xl dark:shadow-black/50 border border-gray-200 dark:border-zinc-800 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-zinc-800 flex items-center justify-between">
              <div>
                <h3 class="text-base font-bold text-gray-900 dark:text-white">Últimos Clientes Registrados</h3>
                <p class="text-xs text-gray-600 dark:text-zinc-400 mt-0.5">Actividad reciente del sistema</p>
              </div>
              <button 
                @click="activeTab = 'clientes'"
                class="text-sm font-medium text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300"
              >
                Ver todos →
              </button>
            </div>
            <div class="overflow-x-auto">
              <table class="w-full">
                <thead class="border-b border-gray-200 dark:border-zinc-800">
                  <tr>
                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 dark:text-zinc-400 uppercase tracking-wider">Negocio</th>
                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 dark:text-zinc-400 uppercase tracking-wider">Plan</th>
                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 dark:text-zinc-400 uppercase tracking-wider">Estado</th>
                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 dark:text-zinc-400 uppercase tracking-wider">Registrado</th>
                  </tr>
                </thead>
                <tbody class="bg-white dark:bg-zinc-900">
                  <tr 
                    v-for="tenant in recentTenants" 
                    :key="tenant.id" 
                    class="hover:bg-gray-50 dark:hover:bg-zinc-800/50 transition-colors duration-200 border-b border-gray-100 dark:border-zinc-800"
                  >
                    <td class="px-6 py-4">
                      <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-lg bg-gradient-to-br from-slate-100 to-slate-200 dark:from-zinc-700 dark:to-zinc-800 flex items-center justify-center">
                          <span class="text-sm font-bold text-slate-600 dark:text-zinc-300">{{ (tenant.name || 'N')[0].toUpperCase() }}</span>
                        </div>
                        <div>
                          <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ tenant.name }}</p>
                          <p class="text-xs text-gray-500 dark:text-zinc-500">{{ tenant.domain }}</p>
                        </div>
                      </div>
                    </td>
                    <td class="px-6 py-4">
                      <span :class="getPlanBadge(tenant.plan)" class="px-2.5 py-1 rounded-full text-[10px] font-bold border uppercase tracking-wide">
                        {{ tenant.plan ? tenant.plan.replace('_', ' ') : 'Sin Plan' }}
                      </span>
                    </td>
                    <td class="px-6 py-4">
                      <span :class="getStatusBadge(tenant.status)" class="px-2.5 py-1 rounded-full text-[10px] font-bold border uppercase tracking-wide">
                        {{ tenant.status === 'active' ? 'ACTIVO' : tenant.status === 'paused' ? 'PAUSADO' : 'SUSPENDIDO' }}
                      </span>
                    </td>
                    <td class="px-6 py-4">
                      <p class="text-sm text-gray-600 dark:text-zinc-400">{{ formatRelativeTime(tenant.created_at) }}</p>
                    </td>
                  </tr>
                  <tr v-if="recentTenants.length === 0">
                    <td colspan="4" class="px-6 py-8 text-center text-sm text-gray-500 dark:text-zinc-400">
                      No hay clientes registrados
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>

      <!-- TAB: Clientes - MOBILE OPTIMIZADO -->
      <div v-show="activeTab === 'clientes'" class="space-y-4 lg:space-y-6">
        <!-- Panel de tabla -->
        <div class="bg-white dark:bg-zinc-900 rounded-xl lg:rounded-2xl shadow-xl dark:shadow-black/50 border border-gray-200 dark:border-zinc-800">
          <div class="px-4 lg:px-6 py-3 lg:py-4 border-b border-gray-200 dark:border-zinc-800">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
              <div>
                <h2 class="text-base lg:text-lg font-bold text-gray-900 dark:text-white">Clientes</h2>
                <p class="text-xs lg:text-sm text-gray-600 dark:text-zinc-400 mt-0.5">{{ filteredTenants.length }} tenants</p>
              </div>
              <button @click="showCreateModal = true" class="px-4 lg:px-5 py-2 lg:py-2.5 bg-slate-900 dark:bg-slate-700 hover:bg-black dark:hover:bg-slate-600 text-white text-sm font-bold rounded-xl shadow-lg shadow-slate-400/30 dark:shadow-slate-900/50 transition-all flex items-center justify-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                <span class="sm:inline">Nuevo</span>
              </button>
            </div>
          </div>

          <!-- Filtros - MOBILE STACKED -->
          <div class="px-4 lg:px-6 py-3 lg:py-4 bg-gray-50 dark:bg-zinc-900/50 border-b border-gray-200 dark:border-zinc-800">
            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2 lg:gap-4">
              <div class="flex-1 relative">
                <input 
                  v-model="searchQuery" 
                  type="text" 
                  placeholder="Buscar..." 
                  class="w-full pl-9 lg:pl-10 pr-4 py-2 lg:py-2.5 text-sm rounded-lg lg:rounded-xl border border-gray-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-gray-900 dark:text-zinc-200 placeholder-gray-400 dark:placeholder-zinc-500 focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500"
                >
                <svg class="w-4 h-4 text-gray-400 dark:text-zinc-500 absolute left-3 lg:left-3.5 top-2.5 lg:top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
              </div>
              <div class="flex gap-2">
                <select v-model="filterPlan" class="flex-1 sm:flex-none px-3 lg:px-4 py-2 lg:py-2.5 text-sm rounded-lg lg:rounded-xl border border-gray-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-gray-700 dark:text-zinc-300 focus:outline-none focus:ring-2 focus:ring-blue-500/30">
                  <option value="">Plan</option>
                  <option value="free_trial">Trial</option>
                  <option value="basic">Basic</option>
                  <option value="premium">Premium</option>
                  <option value="enterprise">Enterprise</option>
                </select>
                <select v-model="filterStatus" class="flex-1 sm:flex-none px-3 lg:px-4 py-2 lg:py-2.5 text-sm rounded-lg lg:rounded-xl border border-gray-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-gray-700 dark:text-zinc-300 focus:outline-none focus:ring-2 focus:ring-blue-500/30">
                  <option value="">Estado</option>
                  <option value="active">Activos</option>
                  <option value="paused">Pausados</option>
                  <option value="suspended">Suspendidos</option>
                </select>
              </div>
            </div>
          </div>

          <!-- MÓVIL: Vista de Cards -->
          <div class="lg:hidden p-3 space-y-3">
            <div 
              v-for="tenant in filteredTenantsWithFilters" 
              :key="tenant.id"
              class="bg-gray-50 dark:bg-zinc-800/50 rounded-xl p-4 border border-gray-200 dark:border-zinc-700"
            >
              <div class="flex items-start justify-between mb-3">
                <div class="flex items-center gap-3">
                  <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-slate-100 to-slate-200 dark:from-zinc-700 dark:to-zinc-800 flex items-center justify-center flex-shrink-0">
                    <span class="text-sm font-bold text-slate-600 dark:text-zinc-300">{{ (tenant.name || 'N')[0].toUpperCase() }}</span>
                  </div>
                  <div class="min-w-0">
                    <p class="text-sm font-semibold text-gray-900 dark:text-white truncate">{{ tenant.name }}</p>
                    <p class="text-xs text-blue-600 dark:text-blue-400 truncate">{{ tenant.domain }}</p>
                  </div>
                </div>
                <span 
                  class="w-2.5 h-2.5 rounded-full flex-shrink-0"
                  :class="{
                    'bg-emerald-500': tenant.status === 'active',
                    'bg-amber-500': tenant.status === 'paused',
                    'bg-rose-500': tenant.status === 'suspended'
                  }"
                ></span>
              </div>
              
              <div class="flex items-center justify-between text-xs mb-3">
                <span :class="getPlanBadge(tenant.plan)" class="px-2 py-1 rounded-lg font-bold border">
                  {{ tenant.plan ? tenant.plan.replace('_', ' ').toUpperCase() : 'N/A' }}
                </span>
                <span class="text-gray-500 dark:text-zinc-400">
                  Vence: {{ tenant.subscription_end ? new Date(tenant.subscription_end).toLocaleDateString('es-ES') : 'N/A' }}
                </span>
              </div>
              
              <div class="flex items-center gap-2 pt-3 border-t border-gray-200 dark:border-zinc-700">
                <a 
                  :href="'https://' + tenant.domain + '/login'" 
                  target="_blank" 
                  class="flex-1 flex items-center justify-center gap-2 py-2 text-xs font-medium bg-emerald-50 dark:bg-emerald-900/20 text-emerald-700 dark:text-emerald-400 rounded-lg"
                >
                  <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                  </svg>
                  Acceder
                </a>
                <button 
                  @click="viewTenantDetails(tenant)"
                  class="flex-1 flex items-center justify-center gap-2 py-2 text-xs font-medium bg-blue-50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-400 rounded-lg"
                >
                  <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                  </svg>
                  Ver
                </button>
                <button 
                  @click="openStoreConfig(tenant)"
                  class="flex-1 flex items-center justify-center gap-2 py-2 text-xs font-medium bg-purple-50 dark:bg-purple-900/20 text-purple-700 dark:text-purple-400 rounded-lg"
                >
                  <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                  </svg>
                  Config
                </button>
              </div>
            </div>
            
            <div v-if="filteredTenantsWithFilters.length === 0" class="py-8 text-center">
              <svg class="w-12 h-12 text-gray-300 dark:text-zinc-600 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
              </svg>
              <p class="text-sm text-gray-500 dark:text-zinc-400">Sin clientes</p>
            </div>
          </div>

          <!-- DESKTOP: Tabla -->
          <div class="hidden lg:block overflow-visible">
            <table class="w-full">
              <thead class="bg-gray-50 dark:bg-zinc-800/50 border-b border-gray-200 dark:border-zinc-800">
                <tr>
                  <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 dark:text-zinc-400 uppercase tracking-wider">Cliente</th>
                  <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 dark:text-zinc-400 uppercase tracking-wider">Plan</th>
                  <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 dark:text-zinc-400 uppercase tracking-wider">Estado</th>
                  <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 dark:text-zinc-400 uppercase tracking-wider">Vence</th>
                  <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 dark:text-zinc-400 uppercase tracking-wider">Acciones</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-100 dark:divide-zinc-800">
                <tr 
                  v-for="tenant in filteredTenantsWithFilters" 
                  :key="tenant.id" 
                  class="hover:bg-gray-50 dark:hover:bg-zinc-800/30 transition-colors"
                >
                  <td class="px-6 py-4">
                    <div class="flex items-center gap-3">
                      <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-slate-100 to-slate-200 dark:from-zinc-700 dark:to-zinc-800 flex items-center justify-center flex-shrink-0">
                        <span class="text-sm font-bold text-slate-600 dark:text-zinc-300">{{ (tenant.name || 'N')[0].toUpperCase() }}</span>
                      </div>
                      <div class="min-w-0">
                        <p class="text-sm font-semibold text-gray-900 dark:text-white truncate">{{ tenant.name }}</p>
                        <a :href="'https://' + tenant.domain" target="_blank" class="text-xs text-blue-600 dark:text-blue-400 hover:underline truncate block">
                          {{ tenant.domain }}
                        </a>
                      </div>
                    </div>
                  </td>
                  <td class="px-6 py-4">
                    <span :class="getPlanBadge(tenant.plan)" class="inline-flex px-2.5 py-1 rounded-lg text-[11px] font-bold border">
                      {{ tenant.plan ? tenant.plan.replace('_', ' ').toUpperCase() : 'N/A' }}
                    </span>
                  </td>
                  <td class="px-6 py-4">
                    <div class="flex items-center gap-2">
                      <span 
                        class="w-2 h-2 rounded-full"
                        :class="{
                          'bg-emerald-500': tenant.status === 'active',
                          'bg-amber-500': tenant.status === 'paused',
                          'bg-rose-500': tenant.status === 'suspended'
                        }"
                      ></span>
                      <span class="text-sm text-gray-700 dark:text-zinc-300">
                        {{ tenant.status === 'active' ? 'Activo' : tenant.status === 'paused' ? 'Pausado' : 'Suspendido' }}
                      </span>
                    </div>
                  </td>
                  <td class="px-6 py-4">
                    <p class="text-sm text-gray-600 dark:text-zinc-400">
                      {{ tenant.subscription_end ? new Date(tenant.subscription_end).toLocaleDateString('es-ES') : 'N/A' }}
                    </p>
                  </td>
                  <td class="px-6 py-4">
                    <div class="flex items-center justify-end gap-1">
                      <!-- Botón Acceder -->
                      <a 
                        :href="'https://' + tenant.domain + '/login'" 
                        target="_blank" 
                        class="p-2 text-gray-400 dark:text-zinc-500 hover:text-emerald-600 dark:hover:text-emerald-400 hover:bg-emerald-50 dark:hover:bg-emerald-900/20 rounded-lg transition-all"
                        title="Acceder al sistema"
                      >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                        </svg>
                      </a>
                      
                      <!-- Menú de Acciones -->
                      <div class="relative" @click.stop>
                        <button 
                          @click="toggleActionMenu(tenant.id)"
                          class="p-2 text-gray-400 dark:text-zinc-500 hover:text-gray-700 dark:hover:text-zinc-200 hover:bg-gray-100 dark:hover:bg-zinc-800 rounded-lg transition-all"
                        >
                          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"/>
                          </svg>
                        </button>
                        
                        <!-- Dropdown Menu -->
                        <div 
                          v-if="activeActionMenu === tenant.id"
                          class="absolute right-0 top-full mt-1 w-48 bg-white dark:bg-zinc-800 rounded-xl shadow-xl dark:shadow-black/50 border border-gray-200 dark:border-zinc-700 py-1 z-50"
                        >
                          <button 
                            @click="viewTenantDetails(tenant); activeActionMenu = null"
                            class="w-full flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 dark:text-zinc-300 hover:bg-gray-50 dark:hover:bg-zinc-700 transition-colors"
                          >
                            <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            Ver Detalles
                          </button>
                          <button 
                            @click="openStoreConfig(tenant); activeActionMenu = null"
                            class="w-full flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 dark:text-zinc-300 hover:bg-gray-50 dark:hover:bg-zinc-700 transition-colors"
                          >
                            <svg class="w-4 h-4 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            Configurar
                          </button>
                          <div class="border-t border-gray-100 dark:border-zinc-700 my-1"></div>
                          <button 
                            @click="toggleTenantStatus(tenant.id, tenant.status); activeActionMenu = null"
                            class="w-full flex items-center gap-3 px-4 py-2.5 text-sm transition-colors"
                            :class="tenant.status === 'active' 
                              ? 'text-amber-600 dark:text-amber-400 hover:bg-amber-50 dark:hover:bg-amber-900/20' 
                              : 'text-emerald-600 dark:text-emerald-400 hover:bg-emerald-50 dark:hover:bg-emerald-900/20'"
                          >
                            <svg v-if="tenant.status === 'active'" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/>
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            {{ tenant.status === 'active' ? 'Pausar Cuenta' : 'Activar Cuenta' }}
                          </button>
                          <div class="border-t border-gray-100 dark:border-zinc-700 my-1"></div>
                          <button 
                            @click="confirmDelete(tenant); activeActionMenu = null"
                            class="w-full flex items-center gap-3 px-4 py-2.5 text-sm text-rose-600 dark:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-900/20 transition-colors"
                          >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                            Eliminar
                          </button>
                        </div>
                      </div>
                    </div>
                  </td>
                </tr>
                <tr v-if="filteredTenantsWithFilters.length === 0">
                  <td colspan="5" class="px-6 py-12 text-center">
                    <div class="flex flex-col items-center">
                      <svg class="w-12 h-12 text-gray-300 dark:text-zinc-600 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                      </svg>
                      <p class="text-sm text-gray-500 dark:text-zinc-400">No se encontraron clientes</p>
                      <p class="text-xs text-gray-400 dark:text-zinc-500 mt-1">Prueba ajustando los filtros</p>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <!-- TAB: AI Monitoring - MOBILE OPTIMIZADO -->
      <div v-show="activeTab === 'ai-monitoring'" class="space-y-4 lg:space-y-6">
        <!-- KPIs Resumen IA - Primera fila - GRID 2x2 MOBILE -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 lg:gap-4">
          <div class="bg-white dark:bg-zinc-900/80 rounded-xl px-3 lg:px-4 py-3 border border-gray-300 dark:border-zinc-800/60 hover:border-gray-400 dark:hover:border-zinc-700/80 transition-all duration-200 hover:shadow-md dark:shadow-lg dark:shadow-black/30">
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

          <div class="bg-white dark:bg-zinc-900/80  rounded-xl px-4 py-3 border border-gray-300 dark:border-zinc-800/60 hover:border-gray-400 dark:hover:border-zinc-700/80 transition-all duration-200 hover:shadow-md dark:shadow-lg dark:shadow-black/30">
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

          <div class="bg-white dark:bg-zinc-900/80  rounded-xl px-4 py-3 border border-gray-300 dark:border-zinc-800/60 hover:border-gray-400 dark:hover:border-zinc-700/80 transition-all duration-200 hover:shadow-md dark:shadow-lg dark:shadow-black/30">
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

          <div class="bg-white dark:bg-zinc-900/80  rounded-xl px-4 py-3 border border-gray-300 dark:border-zinc-800/60 hover:border-gray-400 dark:hover:border-zinc-700/80 transition-all duration-200 hover:shadow-md dark:shadow-lg dark:shadow-black/30">
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

        <!-- KPIs Segunda fila - Costos y Voz -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
          <!-- Costo Total USD -->
          <div class="bg-white dark:bg-zinc-900/80 rounded-xl px-4 py-3 border border-gray-300 dark:border-zinc-800/60 hover:border-gray-400 dark:hover:border-zinc-700/80 transition-all duration-200 hover:shadow-md dark:shadow-lg dark:shadow-black/30">
            <div class="flex items-center gap-3">
              <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0 bg-emerald-50 dark:bg-emerald-950 border border-emerald-100 dark:border-emerald-800">
                <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
              </div>
              <div class="flex-1 min-w-0">
                <p class="text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wide">Costo Total USD</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white mt-0.5">${{ (aiMonitoring.summary?.total_cost_usd || 0).toFixed(4) }}</p>
              </div>
            </div>
          </div>

          <!-- Costo Total COP -->
          <div class="bg-white dark:bg-zinc-900/80 rounded-xl px-4 py-3 border border-gray-300 dark:border-zinc-800/60 hover:border-gray-400 dark:hover:border-zinc-700/80 transition-all duration-200 hover:shadow-md dark:shadow-lg dark:shadow-black/30">
            <div class="flex items-center gap-3">
              <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0 bg-blue-50 dark:bg-blue-950 border border-blue-100 dark:border-blue-800">
                <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
              </div>
              <div class="flex-1 min-w-0">
                <p class="text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wide">Costo Total COP</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white mt-0.5">${{ formatNumber(Math.round(aiMonitoring.summary?.total_cost_cop || 0)) }}</p>
              </div>
            </div>
          </div>

          <!-- Chat Requests -->
          <div class="bg-white dark:bg-zinc-900/80 rounded-xl px-4 py-3 border border-gray-300 dark:border-zinc-800/60 hover:border-gray-400 dark:hover:border-zinc-700/80 transition-all duration-200 hover:shadow-md dark:shadow-lg dark:shadow-black/30">
            <div class="flex items-center gap-3">
              <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0 bg-indigo-50 dark:bg-indigo-950 border border-indigo-100 dark:border-indigo-800">
                <svg class="w-5 h-5 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                </svg>
              </div>
              <div class="flex-1 min-w-0">
                <p class="text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wide">Mensajes Chat</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white mt-0.5">{{ formatNumber(aiMonitoring.summary?.chat_requests || 0) }}</p>
              </div>
            </div>
          </div>

          <!-- Voice Minutes -->
          <div class="bg-white dark:bg-zinc-900/80 rounded-xl px-4 py-3 border border-gray-300 dark:border-zinc-800/60 hover:border-gray-400 dark:hover:border-zinc-700/80 transition-all duration-200 hover:shadow-md dark:shadow-lg dark:shadow-black/30">
            <div class="flex items-center gap-3">
              <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0 bg-rose-50 dark:bg-rose-950 border border-rose-100 dark:border-rose-800">
                <svg class="w-5 h-5 text-rose-600 dark:text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z"></path>
                </svg>
              </div>
              <div class="flex-1 min-w-0">
                <p class="text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wide">Minutos de Voz</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white mt-0.5">{{ (aiMonitoring.summary?.voice_minutes || 0).toFixed(1) }} min</p>
                <p class="text-xs text-zinc-400 dark:text-zinc-500">{{ aiMonitoring.summary?.voice_requests || 0 }} llamadas</p>
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
                  <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 dark:text-zinc-300 uppercase tracking-wider">Tipo</th>
                  <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 dark:text-zinc-300 uppercase tracking-wider">Mensaje</th>
                  <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 dark:text-zinc-300 uppercase tracking-wider">Tokens/Duración</th>
                  <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 dark:text-zinc-300 uppercase tracking-wider">Costo</th>
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
                  <td class="px-6 py-4 whitespace-nowrap">
                    <span 
                      :class="[
                        'px-2 py-1 rounded text-xs font-medium',
                        req.type === 'voice' 
                          ? 'bg-rose-50 dark:bg-rose-950 text-rose-700 dark:text-rose-400 border border-rose-100 dark:border-rose-800'
                          : 'bg-indigo-50 dark:bg-indigo-950 text-indigo-700 dark:text-indigo-400 border border-indigo-100 dark:border-indigo-800'
                      ]"
                    >
                      {{ req.type === 'voice' ? '🎤 Voz' : '💬 Chat' }}
                    </span>
                  </td>
                  <td class="px-6 py-4 text-sm text-gray-700 dark:text-zinc-300 max-w-md truncate">
                    {{ req.type === 'voice' ? `Llamada de ${req.voice_seconds || 0}s` : (req.message || 'Sin mensaje') }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <span v-if="req.type === 'voice'" class="px-2.5 py-1 bg-rose-50 dark:bg-rose-950 text-rose-700 dark:text-rose-400 border border-rose-100 dark:border-rose-800 text-xs font-bold rounded-full">
                      {{ req.voice_seconds || 0 }}s
                    </span>
                    <span v-else class="px-2.5 py-1 bg-purple-50 dark:bg-purple-950 text-purple-700 dark:text-purple-400 border border-purple-100 dark:border-purple-800 text-xs font-bold rounded-full">
                      {{ formatNumber(req.tokens || 0) }} tokens
                    </span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <span class="text-xs font-mono text-emerald-600 dark:text-emerald-400">
                      ${{ (parseFloat(req.cost_usd) || 0).toFixed(6) }}
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
                  <td colspan="7" class="px-6 py-8 text-center text-sm text-gray-500 dark:text-zinc-400">
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

      <!-- Modal: Creando Cuenta (Loading) -->
      <div v-if="creatingTenant" class="fixed inset-0 bg-black/60  flex items-center justify-center z-[9999] animate-fade-in">
        <div class="bg-white dark:bg-zinc-900 rounded-2xl shadow-2xl p-8 max-w-md w-full mx-4 border border-gray-300 dark:border-zinc-800">
          <div class="text-center">
            <!-- Spinner animado -->
            <div class="w-16 h-16 mx-auto mb-4">
              <svg class="animate-spin text-blue-600 dark:text-blue-400" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
            </div>
            
            <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Creando cuenta...</h3>
            <p class="text-sm text-gray-600 dark:text-zinc-400 mb-4">
              Estamos configurando la tienda, creando la base de datos y generando las credenciales.
            </p>
            <p class="text-xs text-gray-500 dark:text-zinc-500">
              Esto puede tomar unos segundos, por favor espera...
            </p>
          </div>
        </div>
      </div>

      <!-- Modal: Crear Tienda -->
      <CreateTenantModal 
        v-if="showCreateModal"
        @close="showCreateModal = false"
        @created="onTenantCreated"
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

      <!-- Modal: Generar Enlace de Registro -->
      <div v-if="showLinkModal" class="fixed inset-0 bg-black/60  flex items-center justify-center z-[9999] animate-fade-in" @click.self="showLinkModal = false">
        <div class="bg-white dark:bg-zinc-900 rounded-2xl shadow-2xl max-w-lg w-full mx-4 border border-gray-200 dark:border-zinc-800 overflow-hidden">
          <!-- Header del Modal -->
          <div class="px-6 py-4 border-b border-gray-200 dark:border-zinc-800 flex items-center justify-between">
            <div>
              <h3 class="text-lg font-bold text-gray-900 dark:text-white">Generar Enlace de Registro</h3>
              <p class="text-sm text-gray-600 dark:text-zinc-400 mt-0.5">Crea un enlace personalizado para nuevos clientes</p>
            </div>
            <button @click="showLinkModal = false" class="p-2 text-gray-400 dark:text-zinc-500 hover:text-gray-600 dark:hover:text-zinc-300 hover:bg-gray-100 dark:hover:bg-zinc-800 rounded-lg transition-all">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
              </svg>
            </button>
          </div>

          <!-- Contenido del Modal -->
          <div class="p-6">
            <div v-if="!generatedLink" class="space-y-4">
              <div>
                <label class="block text-sm font-bold text-gray-700 dark:text-zinc-300 mb-2">Seleccionar Plan:</label>
                <select v-model="selectedPlan" class="w-full px-4 py-3 text-sm border border-gray-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-gray-700 dark:text-zinc-300 rounded-xl font-medium focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400">
                  <option value="free_trial">🎁 FREE TRIAL - Prueba Gratis (7 días)</option>
                  <option value="basic">💼 BASIC - Plan Básico ($29/mes)</option>
                  <option value="premium">⭐ PREMIUM - Plan Premium ($79/mes)</option>
                  <option value="enterprise">🏢 ENTERPRISE - Empresarial ($199/mes)</option>
                </select>
              </div>

              <!-- Info del Plan -->
              <div class="bg-gray-50 dark:bg-zinc-800/50 rounded-xl p-4 border border-gray-200 dark:border-zinc-700">
                <div class="flex items-center gap-3">
                  <div class="w-10 h-10 rounded-lg flex items-center justify-center"
                    :class="{
                      'bg-gray-200 dark:bg-zinc-700': selectedPlan === 'free_trial',
                      'bg-blue-100 dark:bg-blue-950': selectedPlan === 'basic',
                      'bg-purple-100 dark:bg-purple-950': selectedPlan === 'premium',
                      'bg-amber-100 dark:bg-amber-950': selectedPlan === 'enterprise'
                    }">
                    <span class="text-xl">{{ selectedPlan === 'free_trial' ? '🎁' : selectedPlan === 'basic' ? '💼' : selectedPlan === 'premium' ? '⭐' : '🏢' }}</span>
                  </div>
                  <div>
                    <p class="text-sm font-bold text-gray-900 dark:text-white">{{ getPlanInfo(selectedPlan).name }}</p>
                    <p class="text-xs text-gray-500 dark:text-zinc-400">{{ getPlanInfo(selectedPlan).description }}</p>
                  </div>
                </div>
              </div>

              <button 
                @click="generateSignupLink" 
                :disabled="generatingLink"
                class="w-full px-6 py-3 bg-slate-900 dark:bg-slate-700 hover:bg-black dark:hover:bg-slate-600 text-white text-sm font-bold rounded-xl shadow-lg shadow-slate-400/30 dark:shadow-slate-900/50 transition-all disabled:opacity-50 flex items-center justify-center gap-2"
              >
                <svg v-if="generatingLink" class="w-5 h-5 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                </svg>
                {{ generatingLink ? 'Generando...' : 'Generar Enlace' }}
              </button>
            </div>

            <!-- Enlace Generado -->
            <div v-else class="space-y-4">
              <div class="bg-emerald-50 dark:bg-emerald-950/50 border border-emerald-200 dark:border-emerald-800 rounded-xl p-4">
                <div class="flex items-start gap-3 mb-3">
                  <div class="w-10 h-10 rounded-lg bg-emerald-600 flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                  </div>
                  <div class="flex-1">
                    <p class="text-sm font-bold text-emerald-700 dark:text-emerald-400">¡Enlace generado exitosamente!</p>
                    <p class="text-xs text-emerald-600 dark:text-emerald-500 mt-1">
                      Plan: {{ generatedLink.plan?.toUpperCase() }} • Expira: {{ formatLinkExpiry(generatedLink.expires_at) }}
                    </p>
                  </div>
                </div>

                <div class="bg-white dark:bg-zinc-900 rounded-lg p-3 border border-emerald-100 dark:border-emerald-900">
                  <p class="text-xs text-gray-500 dark:text-zinc-500 mb-1.5 font-medium">URL de Registro:</p>
                  <p class="text-sm text-blue-600 dark:text-blue-400 font-mono break-all leading-relaxed">{{ generatedLink.url }}</p>
                </div>
              </div>

              <div class="grid grid-cols-2 gap-3">
                <button 
                  @click="generatedLink = null; selectedPlan = 'basic'" 
                  class="px-5 py-2.5 bg-white dark:bg-zinc-800 hover:bg-gray-50 dark:hover:bg-zinc-700 text-gray-700 dark:text-zinc-200 text-sm font-bold rounded-xl border border-gray-200 dark:border-zinc-700 transition-all"
                >
                  Generar Otro
                </button>
                <button 
                  @click="copyLinkToClipboard" 
                  class="px-5 py-2.5 bg-slate-900 dark:bg-slate-700 hover:bg-black dark:hover:bg-slate-600 text-white text-sm font-bold rounded-xl shadow-lg shadow-slate-400/30 dark:shadow-slate-900/50 transition-all flex items-center justify-center gap-2"
                >
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                  </svg>
                  Copiar
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </main>
  </div>

  <!-- Sistema de Notificaciones Toast -->
  <Teleport to="body">
    <Transition name="toast">
      <div 
        v-if="notification.show" 
        class="fixed top-6 right-6 z-[100] max-w-sm"
      >
        <div 
          :class="[
            'flex items-start gap-3 px-4 py-3 rounded-xl shadow-2xl border ',
            notification.type === 'success' 
              ? 'bg-emerald-50 dark:bg-emerald-950/90 border-emerald-200 dark:border-emerald-800' 
              : notification.type === 'error'
                ? 'bg-rose-50 dark:bg-rose-950/90 border-rose-200 dark:border-rose-800'
                : 'bg-blue-50 dark:bg-blue-950/90 border-blue-200 dark:border-blue-800'
          ]"
        >
          <div class="flex-shrink-0 mt-0.5">
            <svg v-if="notification.type === 'success'" class="w-5 h-5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <svg v-else-if="notification.type === 'error'" class="w-5 h-5 text-rose-600 dark:text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <svg v-else class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
          </div>
          <div class="flex-1 min-w-0">
            <p 
              :class="[
                'text-sm font-semibold',
                notification.type === 'success' ? 'text-emerald-800 dark:text-emerald-300' :
                notification.type === 'error' ? 'text-rose-800 dark:text-rose-300' : 'text-blue-800 dark:text-blue-300'
              ]"
            >
              {{ notification.title }}
            </p>
            <p 
              v-if="notification.message"
              :class="[
                'text-xs mt-0.5',
                notification.type === 'success' ? 'text-emerald-600 dark:text-emerald-400' :
                notification.type === 'error' ? 'text-rose-600 dark:text-rose-400' : 'text-blue-600 dark:text-blue-400'
              ]"
            >
              {{ notification.message }}
            </p>
          </div>
          <button 
            @click="notification.show = false" 
            class="flex-shrink-0 p-1 rounded-lg hover:bg-black/5 dark:hover:bg-white/5 transition-colors"
          >
            <svg class="w-4 h-4 text-gray-400 dark:text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
          </button>
        </div>
      </div>
    </Transition>
  </Teleport>
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
const sidebarOpen = ref(false) // 📱 MOBILE: Control del sidebar
const kpis = ref({ 
  total_active_clients: 0, 
  clients_created_today: 0, 
  clients_this_month: 0,
  mrr: 0, 
  ai_tokens_this_month: 0, 
  ai_cost_this_month: 0 
})
const tenants = ref([])
const selectedTenant = ref(null)
const showCreateModal = ref(false)
const creatingTenant = ref(false)
const showDetailsModal = ref(false)
const showDeleteModal = ref(false)
const showConfigModal = ref(false)
const showLinkModal = ref(false)
const generatingLink = ref(false)
const tenantToDelete = ref(null)
const searchQuery = ref('')
const selectedPlan = ref('basic')
const generatedLink = ref(null)
const filterPlan = ref('')
const filterStatus = ref('')
const activeActionMenu = ref(null)

// Sistema de notificaciones
const notification = ref({
  show: false,
  type: 'success',
  title: '',
  message: ''
})

const showNotification = (type, title, message = '') => {
  notification.value = { show: true, type, title, message }
  setTimeout(() => {
    notification.value.show = false
  }, 4000)
}

// Cerrar menú de acciones al hacer click fuera
const closeActionMenus = () => {
  activeActionMenu.value = null
}

const toggleActionMenu = (tenantId) => {
  activeActionMenu.value = activeActionMenu.value === tenantId ? null : tenantId
}

// Listener para cerrar menus
if (typeof window !== 'undefined') {
  window.addEventListener('click', closeActionMenus)
}

// Fecha y hora actual
const currentDateTime = computed(() => {
  return new Date().toLocaleDateString('es-ES', { 
    weekday: 'long', 
    year: 'numeric', 
    month: 'long', 
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
})

// MRR Growth (simulado - en producción vendría del backend)
const mrrGrowthPercent = computed(() => {
  return 12 // Placeholder - calcular desde histórico
})

// Estadísticas por plan
const planStats = computed(() => {
  const stats = {
    free_trial: { count: 0, mrr: 0 },
    basic: { count: 0, mrr: 0 },
    premium: { count: 0, mrr: 0 },
    enterprise: { count: 0, mrr: 0 }
  }
  
  const planPrices = {
    free_trial: 0,
    basic: 29,
    premium: 79,
    enterprise: 199
  }
  
  const tenantsArray = Array.isArray(tenants.value) ? tenants.value : tenants.value?.data || []
  
  tenantsArray.forEach(tenant => {
    const plan = tenant.plan || 'free_trial'
    if (stats[plan]) {
      stats[plan].count++
      if (tenant.status === 'active') {
        stats[plan].mrr += planPrices[plan] || 0
      }
    }
  })
  
  return stats
})

// Últimos tenants registrados (5 más recientes)
const recentTenants = computed(() => {
  const tenantsArray = Array.isArray(tenants.value) ? tenants.value : tenants.value?.data || []
  return [...tenantsArray]
    .sort((a, b) => new Date(b.created_at) - new Date(a.created_at))
    .slice(0, 5)
})

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
  owner_name: '',
  cedula: '',
  business_name: '',
  subdomain: '',
  plan: 'basic',
  admin_email: '',
  admin_password: ''
})

// Auto-refresh interval
let refreshInterval = null

// Métodos
const fetchData = async () => {
  loading.value = true
  try {
    const [kpisRes, tenantsRes] = await Promise.all([
      axios.get('/api/admin/kpis'),
      axios.get('/api/admin/tenants')
    ])
    if (kpisRes.data.success) kpis.value = kpisRes.data.data
    if (tenantsRes.data.success) tenants.value = tenantsRes.data.data
  } catch (error) {
    console.error('Error:', error)
    showNotification('error', 'Error al cargar datos', 'No se pudo conectar con el servidor')
  }
  loading.value = false
}

const fetchAIMonitoring = async () => {
  loading.value = true
  try {
    // Super admin usa /admin/api/, tenant usa /api/admin/
    const endpoint = `/admin/api/ai-monitoring/dashboard?period=${aiPeriod.value}`
    console.log('📊 [GodMode] Fetching AI Monitoring:', endpoint)
    
    const res = await axios.get(endpoint)
    if (res.data) {
      aiMonitoring.value = res.data
      console.log('✅ [GodMode] AI Monitoring cargado:', res.data)
    }
  } catch (error) {
    console.error('❌ [GodMode] Error al cargar AI Monitoring:', error)
    // No mostrar error al usuario, simplemente dejar vacío
    aiMonitoring.value = {
      summary: {
        total_requests: 0,
        successful: 0,
        rate_limited: 0,
        errors: 0,
        total_tokens: 0,
        total_cost_usd: 0,
        total_cost_cop: 0,
        chat_requests: 0,
        voice_requests: 0,
        voice_minutes: 0
      },
      recent_requests: []
    }
  }
  loading.value = false
}

const createTenant = async () => {
  creatingTenant.value = true
  loading.value = true
  try {
    const res = await axios.post('/api/admin/tenants', newTenant.value)
    if (res.data.success) {
      const data = res.data.data
      
      // Generar PDF profesional con credenciales
      await generateCredentialsPDF(data)
      
      // Cerrar modal de creación
      creatingTenant.value = false
      showCreateModal.value = false
      
      // Mostrar notificación de éxito
      showNotification('success', '¡Cuenta creada exitosamente!', `PDF descargado • ${data.login_url}`)
      
      newTenant.value = { 
        owner_name: '',
        cedula: '',
        business_name: '', 
        subdomain: '', 
        plan: 'basic',
        admin_email: '',
        admin_password: ''
      }
      fetchData()
    }
  } catch (error) {
    const errorMsg = error.response?.data?.message || error.message
    const errors = error.response?.data?.errors
    
    if (errors) {
      const errorList = Object.entries(errors)
        .map(([field, messages]) => `${field}: ${messages.join(', ')}`)
        .join(', ')
      showNotification('error', 'Errores de validación', errorList)
    } else {
      showNotification('error', 'Error', errorMsg)
    }
    creatingTenant.value = false
  }
  loading.value = false
}

// Cuando se crea un tenant desde el nuevo modal
const onTenantCreated = async (data) => {
  // Generar PDF profesional con credenciales
  await generateCredentialsPDF(data)
  
  // Refrescar datos
  fetchData()
}

// 📄 Generar PDF profesional con credenciales
const generateCredentialsPDF = async (data) => {
  // Importación dinámica para evitar problemas de módulos en producción
  const { default: jsPDF } = await import('jspdf')
  
  const pdf = new jsPDF({
    orientation: 'portrait',
    unit: 'mm',
    format: 'letter'
  })

  const pageWidth = pdf.internal.pageSize.getWidth()
  const pageHeight = pdf.internal.pageSize.getHeight()
  const margin = 20
  const contentWidth = pageWidth - (margin * 2)
  const colMid = pageWidth / 2
  
  // 🎨 PALETA CORPORATIVA ENTERPRISE
  const brandNavy = [15, 23, 42]       // Azul oscuro principal
  const brandGold = [180, 145, 70]     // Dorado elegante (líneas decorativas)
  const textBlack = [17, 24, 39]       // Negro para datos importantes
  const textGray = [75, 85, 99]        // Gris oscuro para etiquetas
  const textMuted = [107, 114, 128]    // Gris medio
  const bgLight = [249, 250, 251]      // Fondo gris pálido
  const accentBlue = [37, 99, 235]     // Azul para links
  const borderCard = [59, 130, 246]    // Borde azul tarjeta
  const successGreen = [22, 163, 74]   // Verde estado activo
  const white = [255, 255, 255]
  
  // ==================== HEADER CORPORATIVO ====================
  // Franja azul oscuro
  pdf.setFillColor(...brandNavy)
  pdf.rect(0, 0, pageWidth, 38, 'F')
  
  // Línea dorada decorativa superior
  pdf.setFillColor(...brandGold)
  pdf.rect(0, 38, pageWidth, 2, 'F')
  
  // Logo a la izquierda
  pdf.setTextColor(...white)
  pdf.setFontSize(28)
  pdf.setFont('helvetica', 'bold')
  pdf.text('105POS', margin, 22)
  
  pdf.setFontSize(8)
  pdf.setFont('helvetica', 'normal')
  pdf.setTextColor(180, 190, 210)
  pdf.text('Plataforma Empresarial de Punto de Venta', margin, 30)
  
  // "Documento Confidencial" a la derecha
  pdf.setFontSize(8)
  pdf.setTextColor(150, 160, 180)
  pdf.text('DOCUMENTO CONFIDENCIAL', pageWidth - margin, 25, { align: 'right' })
  
  let yPos = 52
  
  // ==================== SECCIÓN: INFORMACIÓN DEL CLIENTE (2 Columnas) ====================
  // Fondo gris pálido
  pdf.setFillColor(...bgLight)
  pdf.roundedRect(margin, yPos, contentWidth, 42, 3, 3, 'F')
  
  yPos += 10
  
  // COLUMNA IZQUIERDA: Datos del Titular
  const colLeft = margin + 8
  const colRight = colMid + 5
  
  pdf.setFontSize(8)
  pdf.setFont('helvetica', 'bold')
  pdf.setTextColor(...textGray)
  pdf.text('TITULAR DE LA CUENTA', colLeft, yPos)
  
  yPos += 8
  pdf.setFontSize(13)
  pdf.setFont('helvetica', 'bold')
  pdf.setTextColor(...textBlack)
  pdf.text(data.owner_name || 'N/A', colLeft, yPos)
  
  yPos += 7
  pdf.setFontSize(10)
  pdf.setFont('helvetica', 'normal')
  pdf.setTextColor(...textGray)
  pdf.text('Negocio:', colLeft, yPos)
  pdf.setFont('helvetica', 'bold')
  pdf.setTextColor(...textBlack)
  pdf.text(data.business_name || data.tenant_id, colLeft + 18, yPos)
  
  yPos += 6
  pdf.setFont('helvetica', 'normal')
  pdf.setTextColor(...textGray)
  pdf.text('CC:', colLeft, yPos)
  pdf.setTextColor(...textBlack)
  pdf.text(data.cedula || 'N/A', colLeft + 8, yPos)
  
  // COLUMNA DERECHA: Datos del Servicio
  let yPosRight = 62
  
  pdf.setFontSize(8)
  pdf.setFont('helvetica', 'bold')
  pdf.setTextColor(...textGray)
  pdf.text('SERVICIO CONTRATADO', colRight, yPosRight)
  
  yPosRight += 8
  const planLabels = {
    free: 'Plan Gratuito',
    free_trial: 'Prueba Gratuita',
    basic: 'Plan Básico',
    premium: 'Plan Premium',
    enterprise: 'Plan Empresarial'
  }
  pdf.setFontSize(13)
  pdf.setFont('helvetica', 'bold')
  pdf.setTextColor(...textBlack)
  pdf.text(planLabels[data.plan] || data.plan, colRight, yPosRight)
  
  yPosRight += 7
  const fechaVence = new Date(data.subscription_end).toLocaleDateString('es-ES', { 
    year: 'numeric', 
    month: 'long', 
    day: 'numeric' 
  })
  pdf.setFontSize(10)
  pdf.setFont('helvetica', 'normal')
  pdf.setTextColor(...textGray)
  pdf.text('Vigente hasta:', colRight, yPosRight)
  pdf.setTextColor(...textBlack)
  pdf.text(fechaVence, colRight + 26, yPosRight)
  
  yPosRight += 6
  // Estado: Activo (con color verde)
  pdf.setTextColor(...textGray)
  pdf.text('Estado:', colRight, yPosRight)
  pdf.setTextColor(...successGreen)
  pdf.setFont('helvetica', 'bold')
  pdf.text('Activo', colRight + 14, yPosRight)
  
  yPos = 105
  
  // ==================== TARJETA DE ACCESO (Hero Section) ====================
  // Borde azul con fondo blanco - altura aumentada para que no se corte
  pdf.setDrawColor(...borderCard)
  pdf.setLineWidth(1.5)
  pdf.setFillColor(...white)
  pdf.roundedRect(margin, yPos, contentWidth, 70, 4, 4, 'FD')
  
  // Línea dorada superior dentro de la tarjeta (más corta para no pegar al borde)
  pdf.setFillColor(...brandGold)
  pdf.rect(margin + 4, yPos + 1, contentWidth - 8, 3, 'F')
  
  yPos += 15
  
  // Título de la tarjeta
  pdf.setFontSize(11)
  pdf.setFont('helvetica', 'bold')
  pdf.setTextColor(...brandNavy)
  pdf.text('CREDENCIALES DE ACCESO', pageWidth / 2, yPos, { align: 'center' })
  
  yPos += 12
  
  // URL (grande, azul, clickeable)
  pdf.setFontSize(9)
  pdf.setFont('helvetica', 'normal')
  pdf.setTextColor(...textGray)
  pdf.text('Dirección de acceso:', margin + 10, yPos)
  
  yPos += 7
  const loginUrl = data.login_url || `https://${data.domain || data.tenant_id + '.105pos.pro'}/login`
  pdf.setFontSize(12)
  pdf.setFont('helvetica', 'bold')
  pdf.setTextColor(...accentBlue)
  pdf.textWithLink(loginUrl, margin + 10, yPos, { url: loginUrl })
  // Subrayado del link
  const urlWidth = pdf.getTextWidth(loginUrl)
  pdf.setDrawColor(...accentBlue)
  pdf.setLineWidth(0.3)
  pdf.line(margin + 10, yPos + 1, margin + 10 + urlWidth, yPos + 1)
  
  yPos += 12
  
  // Caja interna para Usuario y Contraseña
  pdf.setFillColor(248, 250, 252)
  pdf.roundedRect(margin + 10, yPos - 4, contentWidth - 20, 22, 2, 2, 'F')
  
  // Usuario
  pdf.setFontSize(10)
  pdf.setFont('helvetica', 'normal')
  pdf.setTextColor(...textGray)
  pdf.text('Usuario:', margin + 15, yPos + 4)
  pdf.setFont('helvetica', 'bold')
  pdf.setTextColor(...textBlack)
  pdf.text(data.credentials.email, margin + 35, yPos + 4)
  
  // Contraseña (tipografía monoespaciada)
  pdf.setFont('helvetica', 'normal')
  pdf.setTextColor(...textGray)
  pdf.text('Contraseña:', margin + 15, yPos + 12)
  pdf.setFont('courier', 'bold')  // Monoespaciada para distinguir 0 de O
  pdf.setFontSize(11)
  pdf.setTextColor(...textBlack)
  pdf.text(data.credentials.password, margin + 40, yPos + 12)
  
  yPos = 188
  
  // ==================== PRIMEROS PASOS (3 Columnas Horizontales) ====================
  pdf.setFontSize(10)
  pdf.setFont('helvetica', 'bold')
  pdf.setTextColor(...brandNavy)
  pdf.text('PRIMEROS PASOS', pageWidth / 2, yPos, { align: 'center' })
  
  yPos += 12
  
  // 3 columnas con pasos
  const stepWidth = contentWidth / 3
  const stepY = yPos
  const steps = [
    { num: '1', icon: '🌐', title: 'INGRESE', desc: 'Abra la URL en\nsu navegador' },
    { num: '2', icon: '🔑', title: 'ACCEDA', desc: 'Use sus credenciales\npara iniciar sesión' },
    { num: '3', icon: '🏪', title: 'VENDA', desc: 'Configure su negocio\ny comience a vender' }
  ]
  
  steps.forEach((step, index) => {
    const stepX = margin + (stepWidth * index) + (stepWidth / 2)
    
    // Número del paso en círculo
    pdf.setFillColor(...brandNavy)
    pdf.circle(stepX, stepY, 6, 'F')
    pdf.setFontSize(10)
    pdf.setFont('helvetica', 'bold')
    pdf.setTextColor(...white)
    pdf.text(step.num, stepX, stepY + 2.2, { align: 'center' })
    
    // Título
    pdf.setFontSize(9)
    pdf.setFont('helvetica', 'bold')
    pdf.setTextColor(...textBlack)
    pdf.text(step.title, stepX, stepY + 15, { align: 'center' })
    
    // Descripción
    pdf.setFontSize(8)
    pdf.setFont('helvetica', 'normal')
    pdf.setTextColor(...textMuted)
    const descLines = step.desc.split('\n')
    descLines.forEach((line, lineIdx) => {
      pdf.text(line, stepX, stepY + 21 + (lineIdx * 4), { align: 'center' })
    })
  })
  
  // ==================== FOOTER CORPORATIVO (Franja azul) ====================
  const footerHeight = 28
  const footerY = pageHeight - footerHeight
  
  // Franja azul oscuro
  pdf.setFillColor(...brandNavy)
  pdf.rect(0, footerY, pageWidth, footerHeight, 'F')
  
  // Línea dorada superior
  pdf.setFillColor(...brandGold)
  pdf.rect(0, footerY, pageWidth, 1.5, 'F')
  
  // Información de soporte centrada
  pdf.setFontSize(9)
  pdf.setFont('helvetica', 'bold')
  pdf.setTextColor(...white)
  pdf.text('SOPORTE TÉCNICO', pageWidth / 2, footerY + 10, { align: 'center' })
  
  pdf.setFontSize(8)
  pdf.setFont('helvetica', 'normal')
  pdf.setTextColor(180, 190, 210)
  pdf.text('soporte@105pos.pro   •   www.105pos.pro/ayuda   •   WhatsApp: +57 312 738 8130', pageWidth / 2, footerY + 17, { align: 'center' })
  
  // ID y fecha pequeños
  pdf.setFontSize(6)
  pdf.setTextColor(120, 130, 150)
  pdf.text(`ID: ${data.tenant_id}  |  Generado: ${new Date().toLocaleDateString('es-ES')}`, pageWidth / 2, footerY + 24, { align: 'center' })
  
  // Descargar
  const businessSlug = (data.business_name || 'cliente').replace(/\s+/g, '-').toLowerCase()
  pdf.save(`105POS_Credenciales_${businessSlug}.pdf`)
}

const openStoreConfig = async (tenant) => {
  try {
    // Cargar detalles completos del tenant incluyendo fechas de suscripción
    const res = await axios.get(`/api/admin/tenants/${tenant.id}`)
    if (res.data.success) {
      selectedTenant.value = res.data.data
      showConfigModal.value = true
    } else {
      // Fallback: usar datos de la lista
      selectedTenant.value = tenant
      showConfigModal.value = true
    }
  } catch (error) {
    console.error('Error loading tenant details:', error)
    // Fallback: usar datos de la lista
    selectedTenant.value = tenant
    showConfigModal.value = true
  }
}

const viewTenantDetails = async (tenant) => {
  try {
    const res = await axios.get(`/api/admin/tenants/${tenant.id}`)
    if (res.data.success) {
      selectedTenant.value = res.data.data
      showDetailsModal.value = true
    }
  } catch (error) {
    showNotification('error', 'Error al cargar detalles', error.message)
  }
}

const updateTenantPlan = async (tenantId, newPlan) => {
  try {
    const res = await axios.put(`/admin/api/tenants/${tenantId}`, { plan: newPlan })
    if (res.data.success) {
      showNotification('success', 'Plan actualizado', `Nuevo plan: ${newPlan.toUpperCase()}`)
      fetchData()
      if (selectedTenant.value?.id === tenantId) {
        selectedTenant.value.plan = newPlan
      }
    }
  } catch (error) {
    showNotification('error', 'Error', error.response?.data?.message || error.message)
  }
}

const toggleTenantStatus = async (tenantId, currentStatus) => {
  const newStatus = currentStatus === 'active' ? 'paused' : 'active'
  try {
    const res = await axios.put(`/admin/api/tenants/${tenantId}`, { status: newStatus })
    if (res.data.success) {
      showNotification('success', newStatus === 'paused' ? 'Tienda pausada' : 'Tienda activada', 'Estado actualizado correctamente')
      fetchData()
      if (selectedTenant.value?.id === tenantId) {
        selectedTenant.value.status = newStatus
      }
    }
  } catch (error) {
    showNotification('error', 'Error', error.response?.data?.message || error.message)
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
      showNotification('success', 'Cliente eliminado', res.data.message)
      showDeleteModal.value = false
      tenantToDelete.value = null
      fetchData()
    }
  } catch (error) {
    showNotification('error', 'Error al eliminar', error.response?.data?.message || error.message)
  }
}

const generateSignupLink = async () => {
  generatingLink.value = true
  try {
    const res = await axios.post('/api/admin/generate-signup-link', {
      plan: selectedPlan.value
    })
    if (res.data.success) {
      generatedLink.value = res.data.data
    }
  } catch (error) {
    showNotification('error', 'Error al generar enlace', error.response?.data?.message || error.message)
  }
  generatingLink.value = false
}

const copyLinkToClipboard = async () => {
  try {
    await navigator.clipboard.writeText(generatedLink.value.url)
    showNotification('success', 'Link copiado', 'El enlace está en tu portapapeles')
  } catch (error) {
    showNotification('error', 'Error al copiar', error.message)
  }
}

// Helpers adicionales
const getPlanInfo = (plan) => {
  const plans = {
    free_trial: { name: 'Free Trial', description: '7 días de prueba gratis' },
    basic: { name: 'Plan Basic', description: '$29/mes - Ideal para pequeños negocios' },
    premium: { name: 'Plan Premium', description: '$79/mes - Funciones avanzadas' },
    enterprise: { name: 'Plan Enterprise', description: '$199/mes - Para grandes empresas' }
  }
  return plans[plan] || plans.basic
}

const formatLinkExpiry = (date) => {
  if (!date) return 'N/A'
  return new Date(date).toLocaleString('es-ES', {
    day: '2-digit',
    month: 'short',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
}

const formatRelativeTime = (dateString) => {
  if (!dateString) return 'N/A'
  const date = new Date(dateString)
  const now = new Date()
  const diffMs = now - date
  const diffMins = Math.floor(diffMs / 60000)
  const diffHours = Math.floor(diffMs / 3600000)
  const diffDays = Math.floor(diffMs / 86400000)
  
  if (diffMins < 1) return 'Hace un momento'
  if (diffMins < 60) return `Hace ${diffMins} min`
  if (diffHours < 24) return `Hace ${diffHours}h`
  if (diffDays < 7) return `Hace ${diffDays} días`
  return date.toLocaleDateString('es-ES')
}

const formatNumber = (num) => {
  if (num === null || num === undefined || isNaN(num)) return '0'
  return new Intl.NumberFormat('es-ES').format(num)
}
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
  const tenantsArray = Array.isArray(tenants.value) ? tenants.value : tenants.value?.data || []
  if (!searchQuery.value) return tenantsArray
  return tenantsArray.filter(t =>
    (t.name || '').toLowerCase().includes(searchQuery.value.toLowerCase()) ||
    (t.id || '').toLowerCase().includes(searchQuery.value.toLowerCase()) ||
    (t.domain || '').toLowerCase().includes(searchQuery.value.toLowerCase())
  )
})

// Filtrado avanzado con plan y status
const filteredTenantsWithFilters = computed(() => {
  let result = filteredTenants.value
  
  if (filterPlan.value) {
    result = result.filter(t => t.plan === filterPlan.value)
  }
  
  if (filterStatus.value) {
    result = result.filter(t => t.status === filterStatus.value)
  }
  
  return result
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

/* Toast transitions */
.toast-enter-active {
  animation: toast-in 0.3s ease-out;
}
.toast-leave-active {
  animation: toast-out 0.2s ease-in;
}

@keyframes toast-in {
  from {
    opacity: 0;
    transform: translateX(100%);
  }
  to {
    opacity: 1;
    transform: translateX(0);
  }
}

@keyframes toast-out {
  from {
    opacity: 1;
    transform: translateX(0);
  }
  to {
    opacity: 0;
    transform: translateX(100%);
  }
}
</style>
