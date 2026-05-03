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
        'w-64 bg-[#fafbfc] dark:bg-[#111114] border-r border-gray-200 dark:border-zinc-800/70 flex flex-col fixed left-0 top-0 h-screen z-50 transition-transform duration-300',
        sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'
      ]"
    >
      <!-- Logo/Brand -->
      <div class="p-4 lg:p-5 border-b border-gray-200 dark:border-zinc-800/70">
        <div class="flex items-center justify-between">
          <div class="flex items-center gap-3">
            <div class="w-8 h-8 rounded-lg bg-gray-900 dark:bg-white flex items-center justify-center">
              <span class="text-sm font-black text-white dark:text-gray-900">105</span>
            </div>
            <div>
              <h1 class="text-sm font-bold text-gray-900 dark:text-white tracking-tight">105POS</h1>
              <span class="text-[10px] font-medium text-gray-400 dark:text-zinc-500">Super Admin</span>
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
      <nav class="flex-1 p-3 space-y-0.5 overflow-y-auto">
        <p class="text-[10px] font-semibold text-gray-400 dark:text-zinc-600 uppercase tracking-wider px-3 mb-2 mt-1">Principal</p>
        
        <button 
          @click="activeTab = 'dashboard'; sidebarOpen = false"
          :class="[
            'w-full flex items-center gap-3 px-3 py-2 rounded-lg text-[13px] font-medium transition-all duration-150',
            activeTab === 'dashboard' 
              ? 'bg-gray-100 dark:bg-zinc-800/80 text-gray-900 dark:text-white' 
              : 'text-gray-500 dark:text-zinc-500 hover:bg-gray-50 dark:hover:bg-zinc-800/40 hover:text-gray-700 dark:hover:text-zinc-300'
          ]"
        >
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z"/>
          </svg>
          <span>Dashboard</span>
        </button>

        <button 
          @click="activeTab = 'clientes'; sidebarOpen = false"
          :class="[
            'w-full flex items-center gap-3 px-3 py-2 rounded-lg text-[13px] font-medium transition-all duration-150',
            activeTab === 'clientes' 
              ? 'bg-gray-100 dark:bg-zinc-800/80 text-gray-900 dark:text-white' 
              : 'text-gray-500 dark:text-zinc-500 hover:bg-gray-50 dark:hover:bg-zinc-800/40 hover:text-gray-700 dark:hover:text-zinc-300'
          ]"
        >
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
          </svg>
          <span>Clientes</span>
          <span class="ml-auto text-[10px] font-semibold text-gray-400 dark:text-zinc-500 bg-gray-100 dark:bg-zinc-800 px-1.5 py-0.5 rounded">{{ tenants.length || 0 }}</span>
        </button>

        <p class="text-[10px] font-semibold text-gray-400 dark:text-zinc-600 uppercase tracking-wider px-3 mb-2 mt-5">Monitoreo</p>

        <button 
          @click="activeTab = 'ai-monitoring'; sidebarOpen = false"
          :class="[
            'w-full flex items-center gap-3 px-3 py-2 rounded-lg text-[13px] font-medium transition-all duration-150',
            activeTab === 'ai-monitoring' 
              ? 'bg-gray-100 dark:bg-zinc-800/80 text-gray-900 dark:text-white' 
              : 'text-gray-500 dark:text-zinc-500 hover:bg-gray-50 dark:hover:bg-zinc-800/40 hover:text-gray-700 dark:hover:text-zinc-300'
          ]"
        >
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
          </svg>
          <span>AI Monitoring</span>
        </button>

        <button 
          @click="activeTab = 'logs'; sidebarOpen = false"
          :class="[
            'w-full flex items-center gap-3 px-3 py-2 rounded-lg text-[13px] font-medium transition-all duration-150',
            activeTab === 'logs' 
              ? 'bg-gray-100 dark:bg-zinc-800/80 text-gray-900 dark:text-white' 
              : 'text-gray-500 dark:text-zinc-500 hover:bg-gray-50 dark:hover:bg-zinc-800/40 hover:text-gray-700 dark:hover:text-zinc-300'
          ]"
        >
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
          </svg>
          <span>System Logs</span>
        </button>

        <p class="text-[10px] font-semibold text-gray-400 dark:text-zinc-600 uppercase tracking-wider px-3 mb-2 mt-5">Sistema</p>

        <button 
          @click="activeTab = 'health'; sidebarOpen = false"
          :class="[
            'w-full flex items-center gap-3 px-3 py-2 rounded-lg text-[13px] font-medium transition-all duration-150',
            activeTab === 'health' 
              ? 'bg-gray-100 dark:bg-zinc-800/80 text-gray-900 dark:text-white' 
              : 'text-gray-500 dark:text-zinc-500 hover:bg-gray-50 dark:hover:bg-zinc-800/40 hover:text-gray-700 dark:hover:text-zinc-300'
          ]"
        >
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
          </svg>
          <span>System Health</span>
        </button>

        <button 
          @click="activeTab = 'maintenance'; sidebarOpen = false"
          :class="[
            'w-full flex items-center gap-3 px-3 py-2 rounded-lg text-[13px] font-medium transition-all duration-150',
            activeTab === 'maintenance' 
              ? 'bg-gray-100 dark:bg-zinc-800/80 text-gray-900 dark:text-white' 
              : 'text-gray-500 dark:text-zinc-500 hover:bg-gray-50 dark:hover:bg-zinc-800/40 hover:text-gray-700 dark:hover:text-zinc-300'
          ]"
        >
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
          </svg>
          <span>Mantenimiento</span>
        </button>

        <button 
          @click="activeTab = 'security'; loadSecurityData(); sidebarOpen = false"
          :class="[
            'w-full flex items-center gap-3 px-3 py-2 rounded-lg text-[13px] font-medium transition-all duration-150',
            activeTab === 'security' 
              ? 'bg-gray-100 dark:bg-zinc-800/80 text-gray-900 dark:text-white' 
              : 'text-gray-500 dark:text-zinc-500 hover:bg-gray-50 dark:hover:bg-zinc-800/40 hover:text-gray-700 dark:hover:text-zinc-300'
          ]"
        >
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
          </svg>
          <span>Seguridad</span>
          <span v-if="securityData?.kpis?.blocked_accounts > 0" class="ml-auto w-2 h-2 rounded-full bg-rose-500"></span>
        </button>
      </nav>

      <!-- Footer Sidebar -->
      <div class="p-3 border-t border-gray-200 dark:border-zinc-800/70">
        <a 
          href="/dashboard"
          class="w-full flex items-center gap-3 px-3 py-2 rounded-lg text-[13px] font-medium text-gray-400 dark:text-zinc-500 hover:bg-gray-50 dark:hover:bg-zinc-800/40 hover:text-gray-600 dark:hover:text-zinc-300 transition-all"
        >
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12"/>
          </svg>
          <span>Volver al POS</span>
        </a>
      </div>
    </aside>

    <!-- ========== CONTENIDO PRINCIPAL ========== -->
    <main class="lg:ml-64 flex-1 bg-[#f9fafb] dark:bg-gradient-to-b dark:from-[#141417] dark:via-slate-900 dark:to-[#0a0a0c] transition-colors duration-300 min-h-screen">
      <div class="p-4 lg:p-6 xl:p-8 space-y-4 lg:space-y-5 animate-fade-in max-w-full">
        
        <!-- Header Ejecutivo -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
          <div class="flex items-center gap-3">
            <!-- Hamburger Menu para móvil -->
            <button 
              @click="sidebarOpen = true"
              class="lg:hidden p-2 bg-white dark:bg-zinc-800 rounded-lg border border-gray-200 dark:border-zinc-700"
            >
              <svg class="w-5 h-5 text-gray-500 dark:text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
              </svg>
            </button>
            <div>
              <h1 class="text-lg lg:text-xl font-semibold text-gray-900 dark:text-white tracking-tight">
                {{ activeTab === 'dashboard' ? 'Dashboard' : activeTab === 'clientes' ? 'Clientes' : activeTab === 'ai-monitoring' ? 'AI Monitor' : activeTab === 'health' ? 'System Health' : activeTab === 'maintenance' ? 'Mantenimiento' : activeTab === 'security' ? 'Seguridad' : 'System Logs' }}
              </h1>
              <p class="text-xs text-gray-400 dark:text-zinc-500 mt-0.5 flex items-center gap-1.5">
                <span class="inline-flex h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                <span class="hidden sm:inline">En línea</span> · {{ currentDateTime }}
              </p>
            </div>
          </div>
          
          <div class="flex items-center gap-2">
            <button 
              @click="fetchData" 
              :disabled="loading"
              class="p-2 text-gray-400 dark:text-zinc-500 hover:text-gray-600 dark:hover:text-zinc-300 hover:bg-gray-100 dark:hover:bg-zinc-800 rounded-lg transition-all disabled:opacity-50"
            >
              <svg class="w-4 h-4" :class="{'animate-spin': loading}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
              </svg>
            </button>
            
            <button 
              @click="showLinkModal = true"
              class="inline-flex items-center gap-2 px-3 lg:px-4 py-2 bg-gray-900 dark:bg-zinc-700 hover:bg-black dark:hover:bg-zinc-600 text-white text-xs font-medium rounded-lg transition-all"
            >
              <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
              </svg>
              <span class="hidden sm:inline">Generar Enlace</span>
            </button>
          </div>
        </div>

        <!-- TAB: Dashboard - EXECUTIVE REDESIGN -->
        <div v-show="activeTab === 'dashboard'" class="space-y-5">
          
          <!-- KPI Strip - One solid horizontal block -->
          <div class="bg-white dark:bg-zinc-900 rounded-xl border border-gray-200 dark:border-zinc-800 shadow-sm">
            <div class="grid grid-cols-2 lg:grid-cols-4 divide-x divide-gray-100 dark:divide-zinc-800">
              <!-- MRR -->
              <div class="p-4 lg:px-6 lg:py-5">
                <p class="text-[11px] font-medium text-gray-400 dark:text-zinc-500 uppercase tracking-wider">MRR</p>
                <p class="text-2xl lg:text-3xl font-semibold text-gray-900 dark:text-white mt-1 tracking-tight">${{ formatNumber(kpis.mrr || 0) }}</p>
                <div class="flex items-center gap-1 mt-1.5">
                  <svg v-if="mrrGrowthPercent > 0" class="w-3 h-3 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 10l7-7m0 0l7 7m-7-7v18"/></svg>
                  <svg v-else class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M20 12H4"/></svg>
                  <span :class="mrrGrowthPercent > 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-gray-400 dark:text-zinc-500'" class="text-xs font-medium">{{ mrrGrowthPercent > 0 ? '+' : '' }}{{ mrrGrowthPercent }}%</span>
                  <span class="text-[10px] text-gray-400 dark:text-zinc-600">este mes</span>
                </div>
              </div>
              <!-- ARR -->
              <div class="p-4 lg:px-6 lg:py-5">
                <p class="text-[11px] font-medium text-gray-400 dark:text-zinc-500 uppercase tracking-wider">ARR</p>
                <p class="text-2xl lg:text-3xl font-semibold text-gray-900 dark:text-white mt-1 tracking-tight">${{ formatNumber((kpis.mrr || 0) * 12) }}</p>
                <p class="text-[10px] text-gray-400 dark:text-zinc-600 mt-1.5">Proyección anual</p>
              </div>
              <!-- Active Clients -->
              <div class="p-4 lg:px-6 lg:py-5">
                <p class="text-[11px] font-medium text-gray-400 dark:text-zinc-500 uppercase tracking-wider">Clientes Activos</p>
                <p class="text-2xl lg:text-3xl font-semibold text-gray-900 dark:text-white mt-1 tracking-tight">{{ kpis.total_active_clients || 0 }}</p>
                <p class="text-[10px] text-gray-400 dark:text-zinc-600 mt-1.5">Suscripciones activas</p>
              </div>
              <!-- New This Month -->
              <div class="p-4 lg:px-6 lg:py-5">
                <p class="text-[11px] font-medium text-gray-400 dark:text-zinc-500 uppercase tracking-wider">Nuevos</p>
                <p class="text-2xl lg:text-3xl font-semibold text-gray-900 dark:text-white mt-1 tracking-tight">{{ kpis.clients_this_month || kpis.clients_created_today || 0 }}</p>
                <div class="flex items-center gap-1 mt-1.5">
                  <span class="inline-flex h-1.5 w-1.5 rounded-full bg-blue-500"></span>
                  <span class="text-[10px] text-gray-400 dark:text-zinc-600">{{ kpis.clients_created_today || 0 }} hoy</span>
                </div>
              </div>
            </div>
          </div>

          <!-- Secondary KPIs: Security + Errors -->
          <div class="bg-white dark:bg-zinc-900 rounded-xl border border-gray-200 dark:border-zinc-800 shadow-sm">
            <div class="grid grid-cols-2 lg:grid-cols-4 divide-x divide-gray-100 dark:divide-zinc-800">
              <!-- Logins Today -->
              <div class="p-4 lg:px-6 lg:py-4">
                <div class="flex items-center gap-2">
                  <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                  <p class="text-[11px] font-medium text-gray-400 dark:text-zinc-500 uppercase tracking-wider">Logins hoy</p>
                </div>
                <p class="text-xl font-semibold text-gray-900 dark:text-white mt-1">{{ kpis.logins_today || securityData.kpis?.success_today || 0 }}</p>
              </div>
              <!-- Login Fails -->
              <div class="p-4 lg:px-6 lg:py-4">
                <div class="flex items-center gap-2">
                  <span class="w-2 h-2 rounded-full" :class="(kpis.login_fails_today || securityData.kpis?.failed_today || 0) > 0 ? 'bg-rose-500' : 'bg-gray-300 dark:bg-zinc-600'"></span>
                  <p class="text-[11px] font-medium text-gray-400 dark:text-zinc-500 uppercase tracking-wider">Fallos login</p>
                </div>
                <p class="text-xl font-semibold" :class="(kpis.login_fails_today || securityData.kpis?.failed_today || 0) > 0 ? 'text-rose-600 dark:text-rose-400' : 'text-gray-900 dark:text-white'" >{{ kpis.login_fails_today || securityData.kpis?.failed_today || 0 }}</p>
              </div>
              <!-- System Errors -->
              <div class="p-4 lg:px-6 lg:py-4 cursor-pointer hover:bg-gray-50 dark:hover:bg-zinc-800/30 transition-colors" @click="activeTab = 'clientes'">
                <div class="flex items-center gap-2">
                  <span class="w-2 h-2 rounded-full" :class="(kpis.total_errors || 0) > 0 ? 'bg-amber-500' : 'bg-gray-300 dark:bg-zinc-600'"></span>
                  <p class="text-[11px] font-medium text-gray-400 dark:text-zinc-500 uppercase tracking-wider">Errores sistema</p>
                </div>
                <p class="text-xl font-semibold" :class="(kpis.total_errors || 0) > 0 ? 'text-amber-600 dark:text-amber-400' : 'text-gray-900 dark:text-white'">{{ kpis.total_errors || 0 }}</p>
              </div>
              <!-- Blocked -->
              <div class="p-4 lg:px-6 lg:py-4 cursor-pointer hover:bg-gray-50 dark:hover:bg-zinc-800/30 transition-colors" @click="activeTab = 'security'; loadSecurityData()">
                <div class="flex items-center gap-2">
                  <span class="w-2 h-2 rounded-full" :class="(securityData.kpis?.blocked_accounts || 0) > 0 ? 'bg-red-500 animate-pulse' : 'bg-gray-300 dark:bg-zinc-600'"></span>
                  <p class="text-[11px] font-medium text-gray-400 dark:text-zinc-500 uppercase tracking-wider">Bloqueados</p>
                </div>
                <p class="text-xl font-semibold" :class="(securityData.kpis?.blocked_accounts || 0) > 0 ? 'text-red-600 dark:text-red-400' : 'text-gray-900 dark:text-white'">{{ (securityData.kpis?.blocked_accounts || 0) + (securityData.kpis?.blocked_ips || 0) }}</p>
              </div>
            </div>
          </div>

          <!-- Main Content: Chart + Distribution -->
          <div class="grid grid-cols-1 xl:grid-cols-3 gap-5">
            
            <!-- MRR Breakdown - Real Data -->
            <div class="xl:col-span-2 bg-white dark:bg-zinc-900 rounded-xl border border-gray-200 dark:border-zinc-800 shadow-sm overflow-hidden">
              <div class="px-5 py-4 flex items-center justify-between border-b border-gray-100 dark:border-zinc-800">
                <div>
                  <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Desglose MRR</h3>
                  <p class="text-xs text-gray-400 dark:text-zinc-500 mt-0.5">Ingresos mensuales por plan</p>
                </div>
                <span class="text-lg font-semibold text-gray-900 dark:text-white">${{ formatNumber(kpis.mrr || 0) }}</span>
              </div>
              <div class="p-5 space-y-5">
                <!-- Bar visual -->
                <div class="h-4 rounded-full bg-gray-100 dark:bg-zinc-800 overflow-hidden flex">
                  <div class="bg-blue-500 transition-all duration-700 rounded-l-full" :style="{width: mrrPerPlan.basic.pct + '%'}"></div>
                  <div class="bg-violet-500 transition-all duration-700" :style="{width: mrrPerPlan.premium.pct + '%'}"></div>
                  <div class="bg-amber-500 transition-all duration-700 rounded-r-full" :style="{width: mrrPerPlan.enterprise.pct + '%'}"></div>
                </div>
                
                <!-- Detail rows -->
                <div class="space-y-4">
                  <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                      <span class="w-3 h-3 rounded-sm bg-blue-500"></span>
                      <div>
                        <p class="text-sm font-medium text-gray-900 dark:text-white">Basic</p>
                        <p class="text-xs text-gray-400 dark:text-zinc-500">{{ planStats.basic?.count || 0 }} clientes · $29/mes</p>
                      </div>
                    </div>
                    <div class="text-right">
                      <p class="text-sm font-semibold text-gray-900 dark:text-white">${{ formatNumber(mrrPerPlan.basic.mrr) }}</p>
                      <p class="text-[10px] text-gray-400 dark:text-zinc-500">{{ mrrPerPlan.basic.pct }}%</p>
                    </div>
                  </div>
                  <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                      <span class="w-3 h-3 rounded-sm bg-violet-500"></span>
                      <div>
                        <p class="text-sm font-medium text-gray-900 dark:text-white">Premium</p>
                        <p class="text-xs text-gray-400 dark:text-zinc-500">{{ planStats.premium?.count || 0 }} clientes · $79/mes</p>
                      </div>
                    </div>
                    <div class="text-right">
                      <p class="text-sm font-semibold text-gray-900 dark:text-white">${{ formatNumber(mrrPerPlan.premium.mrr) }}</p>
                      <p class="text-[10px] text-gray-400 dark:text-zinc-500">{{ mrrPerPlan.premium.pct }}%</p>
                    </div>
                  </div>
                  <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                      <span class="w-3 h-3 rounded-sm bg-amber-500"></span>
                      <div>
                        <p class="text-sm font-medium text-gray-900 dark:text-white">Enterprise</p>
                        <p class="text-xs text-gray-400 dark:text-zinc-500">{{ planStats.enterprise?.count || 0 }} clientes · $199/mes</p>
                      </div>
                    </div>
                    <div class="text-right">
                      <p class="text-sm font-semibold text-gray-900 dark:text-white">${{ formatNumber(mrrPerPlan.enterprise.mrr) }}</p>
                      <p class="text-[10px] text-gray-400 dark:text-zinc-500">{{ mrrPerPlan.enterprise.pct }}%</p>
                    </div>
                  </div>
                  <div class="flex items-center justify-between pt-3 border-t border-gray-100 dark:border-zinc-800">
                    <div class="flex items-center gap-3">
                      <span class="w-3 h-3 rounded-sm bg-gray-300 dark:bg-zinc-600"></span>
                      <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-zinc-400">Trial</p>
                        <p class="text-xs text-gray-400 dark:text-zinc-500">{{ planStats.free_trial?.count || 0 }} clientes · $0/mes</p>
                      </div>
                    </div>
                    <div class="text-right">
                      <p class="text-sm font-semibold text-gray-400 dark:text-zinc-500">$0</p>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Plan Distribution + AI Usage -->
            <div class="space-y-5">
              <!-- Plan Distribution -->
              <div class="bg-white dark:bg-zinc-900 rounded-xl border border-gray-200 dark:border-zinc-800 shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100 dark:border-zinc-800">
                  <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Distribución de Planes</h3>
                </div>
                <div class="p-5 space-y-3">
                  <!-- Stacked bar -->
                  <div class="h-3 rounded-full bg-gray-100 dark:bg-zinc-800 overflow-hidden flex">
                    <div class="bg-gray-400 dark:bg-zinc-500 transition-all duration-500" :style="{width: ((planStats.free_trial?.count || 0) / totalTenants * 100) + '%'}"></div>
                    <div class="bg-blue-500 transition-all duration-500" :style="{width: ((planStats.basic?.count || 0) / totalTenants * 100) + '%'}"></div>
                    <div class="bg-violet-500 transition-all duration-500" :style="{width: ((planStats.premium?.count || 0) / totalTenants * 100) + '%'}"></div>
                    <div class="bg-amber-500 transition-all duration-500" :style="{width: ((planStats.enterprise?.count || 0) / totalTenants * 100) + '%'}"></div>
                  </div>
                  <!-- Legend -->
                  <div class="space-y-2.5 mt-4">
                    <div class="flex items-center justify-between">
                      <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-sm bg-gray-400 dark:bg-zinc-500"></span>
                        <span class="text-xs text-gray-600 dark:text-zinc-400">Trial</span>
                      </div>
                      <div class="text-right">
                        <span class="text-xs font-semibold text-gray-900 dark:text-white">{{ planStats.free_trial?.count || 0 }}</span>
                        <span class="text-[10px] text-gray-400 dark:text-zinc-500 ml-1">· $0</span>
                      </div>
                    </div>
                    <div class="flex items-center justify-between">
                      <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-sm bg-blue-500"></span>
                        <span class="text-xs text-gray-600 dark:text-zinc-400">Basic</span>
                      </div>
                      <div class="text-right">
                        <span class="text-xs font-semibold text-gray-900 dark:text-white">{{ planStats.basic?.count || 0 }}</span>
                        <span class="text-[10px] text-gray-400 dark:text-zinc-500 ml-1">· ${{ formatNumber(planStats.basic?.mrr || 0) }}</span>
                      </div>
                    </div>
                    <div class="flex items-center justify-between">
                      <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-sm bg-violet-500"></span>
                        <span class="text-xs text-gray-600 dark:text-zinc-400">Premium</span>
                      </div>
                      <div class="text-right">
                        <span class="text-xs font-semibold text-gray-900 dark:text-white">{{ planStats.premium?.count || 0 }}</span>
                        <span class="text-[10px] text-gray-400 dark:text-zinc-500 ml-1">· ${{ formatNumber(planStats.premium?.mrr || 0) }}</span>
                      </div>
                    </div>
                    <div class="flex items-center justify-between">
                      <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-sm bg-amber-500"></span>
                        <span class="text-xs text-gray-600 dark:text-zinc-400">Enterprise</span>
                      </div>
                      <div class="text-right">
                        <span class="text-xs font-semibold text-gray-900 dark:text-white">{{ planStats.enterprise?.count || 0 }}</span>
                        <span class="text-[10px] text-gray-400 dark:text-zinc-500 ml-1">· ${{ formatNumber(planStats.enterprise?.mrr || 0) }}</span>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- AI Usage - Secondary -->
              <div class="bg-white dark:bg-zinc-900 rounded-xl border border-gray-200 dark:border-zinc-800 shadow-sm overflow-hidden">
                <div class="px-5 py-3 border-b border-gray-100 dark:border-zinc-800 flex items-center justify-between">
                  <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Uso de IA</h3>
                  <span class="text-[10px] text-gray-400 dark:text-zinc-500">Este mes</span>
                </div>
                <div class="p-5">
                  <div class="flex items-center justify-between mb-3">
                    <span class="text-xs text-gray-500 dark:text-zinc-400">Tokens</span>
                    <span class="text-sm font-semibold text-gray-900 dark:text-white">{{ formatNumber(kpis.ai_tokens_this_month || 0) }}</span>
                  </div>
                  <div class="flex items-center justify-between mb-4">
                    <span class="text-xs text-gray-500 dark:text-zinc-400">Costo</span>
                    <span class="text-sm font-semibold text-gray-900 dark:text-white">${{ formatNumber(kpis.ai_cost_this_month || 0) }}</span>
                  </div>
                  <button 
                    @click="activeTab = 'ai-monitoring'"
                    class="w-full text-xs font-medium text-gray-500 dark:text-zinc-400 hover:text-gray-700 dark:hover:text-zinc-300 bg-gray-50 dark:bg-zinc-800 hover:bg-gray-100 dark:hover:bg-zinc-700 py-2 rounded-lg transition-all"
                  >
                    Ver detalles
                  </button>
                </div>
              </div>

              <!-- Security Quick Stats -->
              <div class="bg-white dark:bg-zinc-900 rounded-xl border border-gray-200 dark:border-zinc-800 shadow-sm overflow-hidden">
                <div class="px-5 py-3 border-b border-gray-100 dark:border-zinc-800 flex items-center justify-between">
                  <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Seguridad</h3>
                  <span class="text-[10px] text-gray-400 dark:text-zinc-500">Hoy</span>
                </div>
                <div class="p-5 space-y-3">
                  <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                      <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                      <span class="text-xs text-gray-500 dark:text-zinc-400">Fallos login</span>
                    </div>
                    <span class="text-sm font-semibold" :class="(securityData.kpis?.failed_today || 0) > 0 ? 'text-rose-600 dark:text-rose-400' : 'text-gray-900 dark:text-white'">{{ securityData.kpis?.failed_today || 0 }}</span>
                  </div>
                  <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                      <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                      <span class="text-xs text-gray-500 dark:text-zinc-400">Logins exitosos</span>
                    </div>
                    <span class="text-sm font-semibold text-gray-900 dark:text-white">{{ securityData.kpis?.success_today || 0 }}</span>
                  </div>
                  <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                      <span class="w-2 h-2 rounded-full" :class="(securityData.kpis?.blocked_accounts || 0) > 0 ? 'bg-red-500 animate-pulse' : 'bg-gray-300 dark:bg-zinc-600'"></span>
                      <span class="text-xs text-gray-500 dark:text-zinc-400">Cuentas bloqueadas</span>
                    </div>
                    <span class="text-sm font-semibold" :class="(securityData.kpis?.blocked_accounts || 0) > 0 ? 'text-red-600 dark:text-red-400' : 'text-gray-900 dark:text-white'">{{ securityData.kpis?.blocked_accounts || 0 }}</span>
                  </div>
                  <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                      <span class="w-2 h-2 rounded-full" :class="(securityData.kpis?.blocked_ips || 0) > 0 ? 'bg-amber-500' : 'bg-gray-300 dark:bg-zinc-600'"></span>
                      <span class="text-xs text-gray-500 dark:text-zinc-400">IPs bloqueadas</span>
                    </div>
                    <span class="text-sm font-semibold" :class="(securityData.kpis?.blocked_ips || 0) > 0 ? 'text-amber-600 dark:text-amber-400' : 'text-gray-900 dark:text-white'">{{ securityData.kpis?.blocked_ips || 0 }}</span>
                  </div>
                  <button 
                    @click="activeTab = 'security'; loadSecurityData()"
                    class="w-full text-xs font-medium text-gray-500 dark:text-zinc-400 hover:text-gray-700 dark:hover:text-zinc-300 bg-gray-50 dark:bg-zinc-800 hover:bg-gray-100 dark:hover:bg-zinc-700 py-2 rounded-lg transition-all mt-1"
                  >
                    Ver detalles
                  </button>
                </div>
              </div>
            </div>
          </div>

          <!-- Bottom Row: Alerts + Recent Activity -->
          <div class="grid grid-cols-1 xl:grid-cols-3 gap-5">
            
            <!-- Alerts Section -->
            <div class="bg-white dark:bg-zinc-900 rounded-xl border border-gray-200 dark:border-zinc-800 shadow-sm overflow-hidden">
              <div class="px-5 py-4 border-b border-gray-100 dark:border-zinc-800 flex items-center justify-between">
                <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Requiere atención</h3>
                <span v-if="alertTenants.length > 0" class="text-[10px] font-semibold text-amber-600 dark:text-amber-400 bg-amber-50 dark:bg-amber-950 px-2 py-0.5 rounded-full">{{ alertTenants.length }}</span>
              </div>
              <div class="divide-y divide-gray-100 dark:divide-zinc-800">
                <div 
                  v-for="alert in alertTenants" 
                  :key="'alert-' + alert.id"
                  class="px-5 py-3 flex items-center justify-between hover:bg-gray-50 dark:hover:bg-zinc-800/50 transition-colors cursor-pointer"
                  @click="viewTenantDetails(alert)"
                >
                  <div class="flex items-center gap-3 min-w-0">
                    <span 
                      class="w-2 h-2 rounded-full flex-shrink-0"
                      :class="{
                        'bg-rose-500': alert.alertColor === 'rose',
                        'bg-amber-500': alert.alertColor === 'amber'
                      }"
                    ></span>
                    <div class="min-w-0">
                      <p class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ alert.name }}</p>
                      <p class="text-[10px] text-gray-400 dark:text-zinc-500">{{ alert.plan ? alert.plan.replace('_', ' ') : 'Sin plan' }}</p>
                    </div>
                  </div>
                  <span 
                    class="text-[10px] font-semibold px-2 py-0.5 rounded-full flex-shrink-0"
                    :class="{
                      'text-rose-600 dark:text-rose-400 bg-rose-50 dark:bg-rose-950': alert.alertColor === 'rose',
                      'text-amber-600 dark:text-amber-400 bg-amber-50 dark:bg-amber-950': alert.alertColor === 'amber'
                    }"
                  >{{ alert.alertLabel }}</span>
                </div>
                <div v-if="alertTenants.length === 0" class="px-5 py-8 text-center">
                  <svg class="w-8 h-8 text-gray-200 dark:text-zinc-700 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                  </svg>
                  <p class="text-xs text-gray-400 dark:text-zinc-500">Todo en orden</p>
                </div>
              </div>
            </div>

            <!-- Recent Clients -->
            <div class="xl:col-span-2 bg-white dark:bg-zinc-900 rounded-xl border border-gray-200 dark:border-zinc-800 shadow-sm overflow-hidden">
              <div class="px-5 py-4 border-b border-gray-100 dark:border-zinc-800 flex items-center justify-between">
                <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Actividad reciente</h3>
                <button 
                  @click="activeTab = 'clientes'"
                  class="text-xs font-medium text-gray-400 dark:text-zinc-500 hover:text-gray-600 dark:hover:text-zinc-300 transition-colors"
                >
                  Ver todos
                </button>
              </div>
              <div class="overflow-x-auto">
                <table class="w-full">
                  <thead>
                    <tr class="border-b border-gray-100 dark:border-zinc-800">
                      <th class="px-5 py-2.5 text-left text-[10px] font-semibold text-gray-400 dark:text-zinc-500 uppercase tracking-wider">Negocio</th>
                      <th class="px-5 py-2.5 text-left text-[10px] font-semibold text-gray-400 dark:text-zinc-500 uppercase tracking-wider">Plan</th>
                      <th class="px-5 py-2.5 text-left text-[10px] font-semibold text-gray-400 dark:text-zinc-500 uppercase tracking-wider">Estado</th>
                      <th class="px-5 py-2.5 text-right text-[10px] font-semibold text-gray-400 dark:text-zinc-500 uppercase tracking-wider">Registro</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr 
                      v-for="tenant in recentTenants" 
                      :key="'recent-' + tenant.id" 
                      class="border-b border-gray-50 dark:border-zinc-800/50 hover:bg-gray-50 dark:hover:bg-zinc-800/30 transition-colors"
                    >
                      <td class="px-5 py-3">
                        <div class="flex items-center gap-2.5">
                          <div class="w-7 h-7 rounded-md bg-gray-100 dark:bg-zinc-800 flex items-center justify-center flex-shrink-0">
                            <span class="text-[11px] font-semibold text-gray-500 dark:text-zinc-400">{{ (tenant.name || 'N')[0].toUpperCase() }}</span>
                          </div>
                          <div class="min-w-0">
                            <p class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ tenant.name }}</p>
                            <p class="text-[10px] text-gray-400 dark:text-zinc-500 truncate">{{ tenant.domain }}</p>
                          </div>
                        </div>
                      </td>
                      <td class="px-5 py-3">
                        <span class="text-xs font-medium text-gray-600 dark:text-zinc-400">{{ tenant.plan ? tenant.plan.replace('_', ' ') : 'N/A' }}</span>
                      </td>
                      <td class="px-5 py-3">
                        <div class="flex items-center gap-1.5">
                          <span 
                            class="w-1.5 h-1.5 rounded-full"
                            :class="{
                              'bg-emerald-500': tenant.status === 'active',
                              'bg-amber-500': tenant.status === 'paused',
                              'bg-rose-500': tenant.status === 'suspended'
                            }"
                          ></span>
                          <span class="text-xs text-gray-500 dark:text-zinc-400">
                            {{ tenant.status === 'active' ? 'Activo' : tenant.status === 'paused' ? 'Pausado' : 'Suspendido' }}
                          </span>
                        </div>
                      </td>
                      <td class="px-5 py-3 text-right">
                        <span class="text-xs text-gray-400 dark:text-zinc-500">{{ formatRelativeTime(tenant.created_at) }}</span>
                      </td>
                    </tr>
                    <tr v-if="recentTenants.length === 0">
                      <td colspan="4" class="px-5 py-8 text-center text-xs text-gray-400 dark:text-zinc-500">
                        No hay clientes registrados
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>

      <!-- TAB: Clientes -->
      <div v-show="activeTab === 'clientes'" class="space-y-4 lg:space-y-6">
        
        <!-- Sub-view: Perfil de Cliente -->
        <TenantProfileView 
          v-if="viewingTenant"
          :tenant="viewingTenant"
          @back="handleProfileBack"
          @update-plan="updateTenantPlan"
          @toggle-status="(id, status) => { toggleTenantStatus(id, status); if(viewingTenant) viewingTenant.status = viewingTenant.status === 'active' ? 'suspended' : 'active' }"
          @delete="handleProfileDelete"
          @refresh="() => { fetchData(); viewTenantDetails(viewingTenant) }"
        />

        <!-- Lista de Clientes -->
        <template v-else>
        <div class="bg-white dark:bg-zinc-900 rounded-2xl shadow-sm border border-gray-200 dark:border-zinc-800">
          <!-- Header -->
          <div class="px-6 py-4 border-b border-gray-100 dark:border-zinc-800">
            <div class="flex items-center justify-between">
              <div>
                <h2 class="text-base font-semibold text-gray-900 dark:text-white">Clientes</h2>
                <p class="text-sm text-gray-500 dark:text-zinc-400 mt-0.5">{{ filteredTenantsWithFilters.length }} de {{ filteredTenants.length }} tenants</p>
              </div>
              <button @click="showCreateModal = true" class="px-5 py-2.5 bg-slate-900 dark:bg-slate-700 hover:bg-black dark:hover:bg-slate-600 text-white text-sm font-semibold rounded-xl shadow-lg shadow-slate-400/30 dark:shadow-slate-900/50 transition-all flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Nuevo
              </button>
            </div>
          </div>

          <!-- Filtros -->
          <div class="px-6 py-3 border-b border-gray-100 dark:border-zinc-800 bg-gray-50/50 dark:bg-zinc-900/50">
            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
              <div class="flex-1 relative">
                <input 
                  v-model="searchQuery" 
                  type="text" 
                  placeholder="Buscar por nombre, dominio..." 
                  class="w-full pl-10 pr-4 py-2.5 text-sm rounded-xl border border-gray-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-gray-900 dark:text-zinc-200 placeholder-gray-400 dark:placeholder-zinc-500 focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500"
                >
                <svg class="w-4 h-4 text-gray-400 dark:text-zinc-500 absolute left-3.5 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
              </div>
              <div class="flex gap-2">
                <select v-model="filterPlan" class="px-4 py-2.5 text-sm rounded-xl border border-gray-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-gray-700 dark:text-zinc-300 focus:outline-none focus:ring-2 focus:ring-blue-500/30">
                  <option value="">Todos los planes</option>
                  <option value="free_trial">Trial</option>
                  <option value="basic">Basic</option>
                  <option value="premium">Premium</option>
                  <option value="enterprise">Enterprise</option>
                </select>
                <select v-model="filterStatus" class="px-4 py-2.5 text-sm rounded-xl border border-gray-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-gray-700 dark:text-zinc-300 focus:outline-none focus:ring-2 focus:ring-blue-500/30">
                  <option value="">Todos los estados</option>
                  <option value="active">Activos</option>
                  <option value="paused">Pausados</option>
                  <option value="suspended">Suspendidos</option>
                </select>
              </div>
            </div>
          </div>

          <!-- MÓVIL: Cards -->
          <div class="lg:hidden p-3 space-y-2">
            <div 
              v-for="tenant in filteredTenantsWithFilters" 
              :key="tenant.id"
              @click="viewTenantDetails(tenant)"
              class="p-4 rounded-xl border border-gray-100 dark:border-zinc-800 hover:bg-gray-50 dark:hover:bg-zinc-800/40 transition-colors cursor-pointer"
            >
              <div class="flex items-center justify-between">
                <div class="flex items-center gap-3 min-w-0">
                  <div class="w-10 h-10 rounded-xl bg-gray-100 dark:bg-zinc-800 flex items-center justify-center flex-shrink-0">
                    <span class="text-sm font-bold text-gray-500 dark:text-zinc-400">{{ (tenant.name || 'N')[0].toUpperCase() }}</span>
                  </div>
                  <div class="min-w-0">
                    <div class="flex items-center gap-1.5">
                      <p class="text-sm font-semibold text-gray-900 dark:text-white truncate">{{ tenant.name }}</p>
                      <span v-if="tenant.error_count > 0" class="inline-flex items-center gap-0.5 px-1 py-0.5 bg-rose-50 dark:bg-rose-950 text-rose-700 dark:text-rose-400 text-[9px] font-bold rounded border border-rose-100 dark:border-rose-800 flex-shrink-0">
                        {{ tenant.error_count }}
                      </span>
                    </div>
                    <p class="text-xs text-gray-400 dark:text-zinc-500 truncate">{{ tenant.domain }}</p>
                  </div>
                </div>
                <div class="flex items-center gap-2 flex-shrink-0">
                  <span :class="getPlanBadge(tenant.plan)" class="px-2 py-0.5 rounded text-[10px] font-bold border">
                    {{ tenant.plan ? tenant.plan.replace('_', ' ').toUpperCase() : 'N/A' }}
                  </span>
                  <span 
                    class="w-2 h-2 rounded-full"
                    :class="{
                      'bg-emerald-500': tenant.status === 'active',
                      'bg-amber-500': tenant.status === 'paused',
                      'bg-rose-500': tenant.status === 'suspended'
                    }"
                  ></span>
                </div>
              </div>
            </div>
            <div v-if="filteredTenantsWithFilters.length === 0" class="py-10 text-center">
              <p class="text-sm text-gray-400 dark:text-zinc-500">No se encontraron clientes</p>
            </div>
          </div>

          <!-- DESKTOP: Tabla -->
          <div class="hidden lg:block">
            <table class="w-full">
              <thead>
                <tr class="border-b border-gray-100 dark:border-zinc-800">
                  <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-zinc-400 uppercase tracking-wider">Cliente</th>
                  <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-zinc-400 uppercase tracking-wider">Plan</th>
                  <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-zinc-400 uppercase tracking-wider">Estado</th>
                  <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-zinc-400 uppercase tracking-wider">Vencimiento</th>
                  <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-zinc-400 uppercase tracking-wider">Registro</th>
                  <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 dark:text-zinc-400 uppercase tracking-wider">Acciones</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-50 dark:divide-zinc-800/50">
                <tr 
                  v-for="tenant in filteredTenantsWithFilters" 
                  :key="tenant.id" 
                  @click="viewTenantDetails(tenant)"
                  class="hover:bg-gray-50 dark:hover:bg-zinc-800/30 transition-colors cursor-pointer group"
                >
                  <td class="px-6 py-4">
                    <div class="flex items-center gap-3">
                      <div class="w-9 h-9 rounded-lg bg-gray-100 dark:bg-zinc-800 flex items-center justify-center flex-shrink-0">
                        <span class="text-sm font-semibold text-gray-500 dark:text-zinc-400">{{ (tenant.name || 'N')[0].toUpperCase() }}</span>
                      </div>
                      <div class="min-w-0">
                        <div class="flex items-center gap-2">
                          <p class="text-sm font-medium text-gray-900 dark:text-white truncate group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">{{ tenant.name }}</p>
                          <span v-if="tenant.error_count > 0" class="inline-flex items-center gap-0.5 px-1.5 py-0.5 bg-rose-50 dark:bg-rose-950 text-rose-700 dark:text-rose-400 text-[9px] font-bold rounded border border-rose-100 dark:border-rose-800 flex-shrink-0">
                            <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4.5c-.77-.833-2.694-.833-3.464 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
                            {{ tenant.error_count }}
                          </span>
                        </div>
                        <p class="text-xs text-gray-400 dark:text-zinc-500 truncate">{{ tenant.domain }}</p>
                      </div>
                    </div>
                  </td>
                  <td class="px-6 py-4">
                    <span :class="getPlanBadge(tenant.plan)" class="inline-flex px-2 py-0.5 rounded text-[11px] font-bold border">
                      {{ tenant.plan ? tenant.plan.replace('_', ' ').toUpperCase() : 'N/A' }}
                    </span>
                  </td>
                  <td class="px-6 py-4">
                    <div class="flex items-center gap-2">
                      <span 
                        class="w-1.5 h-1.5 rounded-full"
                        :class="{
                          'bg-emerald-500': tenant.status === 'active',
                          'bg-amber-500': tenant.status === 'paused',
                          'bg-rose-500': tenant.status === 'suspended'
                        }"
                      ></span>
                      <span class="text-sm text-gray-600 dark:text-zinc-400">
                        {{ tenant.status === 'active' ? 'Activo' : tenant.status === 'paused' ? 'Pausado' : 'Suspendido' }}
                      </span>
                    </div>
                  </td>
                  <td class="px-6 py-4">
                    <span class="text-sm text-gray-600 dark:text-zinc-400">
                      {{ tenant.subscription_end ? new Date(tenant.subscription_end).toLocaleDateString('es-ES') : '—' }}
                    </span>
                  </td>
                  <td class="px-6 py-4">
                    <span class="text-sm text-gray-400 dark:text-zinc-500">{{ formatRelativeTime(tenant.created_at) }}</span>
                  </td>
                  <td class="px-6 py-4" @click.stop>
                    <div class="flex items-center justify-end gap-1">
                      <a 
                        :href="'https://' + tenant.domain + '/login'" 
                        target="_blank" 
                        class="p-2 text-gray-400 dark:text-zinc-500 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-lg transition-all"
                        title="Acceder al sistema"
                      >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                        </svg>
                      </a>
                      <button
                        @click="toggleTenantStatus(tenant.id, tenant.status)"
                        class="p-2 rounded-lg transition-all"
                        :class="tenant.status === 'active'
                          ? 'text-gray-400 dark:text-zinc-500 hover:text-amber-600 dark:hover:text-amber-400 hover:bg-amber-50 dark:hover:bg-amber-900/20'
                          : 'text-gray-400 dark:text-zinc-500 hover:text-emerald-600 dark:hover:text-emerald-400 hover:bg-emerald-50 dark:hover:bg-emerald-900/20'"
                        :title="tenant.status === 'active' ? 'Suspender' : 'Activar'"
                      >
                        <svg v-if="tenant.status === 'active'" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/>
                        </svg>
                      </button>
                      <button
                        @click="confirmDelete(tenant)"
                        class="p-2 text-gray-400 dark:text-zinc-500 hover:text-rose-600 dark:hover:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-900/20 rounded-lg transition-all"
                        title="Eliminar"
                      >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                      </button>
                    </div>
                  </td>
                </tr>
                <tr v-if="filteredTenantsWithFilters.length === 0">
                  <td colspan="6" class="px-6 py-12 text-center">
                    <p class="text-sm text-gray-400 dark:text-zinc-500">No se encontraron clientes</p>
                    <p class="text-xs text-gray-400 dark:text-zinc-500 mt-1">Prueba ajustando los filtros</p>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
        </template>
      </div>

      <!-- TAB: AI Monitoring -->
      <div v-show="activeTab === 'ai-monitoring'" class="space-y-4 lg:space-y-6">
        <AIMonitorTab @notify="showNotification" />
      </div>

      <!-- TAB: System Logs -->
      <div v-show="activeTab === 'logs'" class="space-y-6">
        <SystemLogsTab @notify="showNotification" />
      </div>

      <!-- TAB: System Health -->
      <div v-show="activeTab === 'health'" class="space-y-6">
        <SystemHealthTab />
      </div>

      <!-- TAB: Maintenance -->
      <div v-show="activeTab === 'maintenance'" class="space-y-6">
        <MaintenanceToolsTab @notify="showNotification" />
      </div>

      <!-- TAB: Security -->
      <div v-show="activeTab === 'security'" class="space-y-6">
        <SecurityTab ref="securityTabRef" @notify="showNotification" />
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

      <!-- Modal: Confirmar Eliminación -->
      <DeleteTenantModal
        v-if="showDeleteModal && tenantToDelete"
        :tenant="tenantToDelete"
        @close="showDeleteModal = false; tenantToDelete = null"
        @confirm="deleteTenant"
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
                  <option value="free_trial">FREE TRIAL - Prueba Gratis (7 días)</option>
                  <option value="basic">BASIC - Plan Básico ($29/mes)</option>
                  <option value="premium">⭐ PREMIUM - Plan Premium ($79/mes)</option>
                  <option value="enterprise">ENTERPRISE - Empresarial ($199/mes)</option>
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
                    <span class="text-xl">{{ selectedPlan === 'free_trial' ? '' : selectedPlan === 'basic' ? '' : selectedPlan === 'premium' ? '⭐' : '' }}</span>
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
import TenantProfileView from './TenantProfileView.vue'
import DeleteTenantModal from './DeleteTenantModal.vue'
import SystemHealthTab from './SystemHealthTab.vue'
import SystemLogsTab from './SystemLogsTab.vue'
import MaintenanceToolsTab from './MaintenanceToolsTab.vue'
import SecurityTab from './SecurityTab.vue'
import AIMonitorTab from './AIMonitorTab.vue'

// Estados
const loading = ref(false)
const activeTab = ref('dashboard')
const sidebarOpen = ref(false) // MOBILE: Control del sidebar
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

// MRR Growth - calculated from new clients this month vs total
const mrrGrowthPercent = computed(() => {
  const totalActive = kpis.value.total_active_clients || 0
  const newThisMonth = kpis.value.clients_this_month || kpis.value.clients_created_today || 0
  if (totalActive <= 0 || newThisMonth <= 0) return 0
  const previousCount = totalActive - newThisMonth
  if (previousCount <= 0) return 100
  return Math.round((newThisMonth / previousCount) * 100)
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

// MRR per plan breakdown for visual bar
const mrrPerPlan = computed(() => {
  const totalMrr = kpis.value.mrr || 1
  return {
    basic: { pct: Math.round(((planStats.value.basic?.mrr || 0) / totalMrr) * 100), mrr: planStats.value.basic?.mrr || 0 },
    premium: { pct: Math.round(((planStats.value.premium?.mrr || 0) / totalMrr) * 100), mrr: planStats.value.premium?.mrr || 0 },
    enterprise: { pct: Math.round(((planStats.value.enterprise?.mrr || 0) / totalMrr) * 100), mrr: planStats.value.enterprise?.mrr || 0 }
  }
})

// Alerts - tenants that need attention
const alertTenants = computed(() => {
  const tenantsArray = Array.isArray(tenants.value) ? tenants.value : tenants.value?.data || []
  const now = new Date()
  const alerts = []
  
  tenantsArray.forEach(t => {
    if (t.status === 'suspended') {
      alerts.push({ ...t, alertType: 'suspended', alertLabel: 'Suspendido', alertColor: 'rose' })
    } else if (t.status === 'paused') {
      alerts.push({ ...t, alertType: 'paused', alertLabel: 'Pausado', alertColor: 'amber' })
    } else if (t.subscription_end) {
      const end = new Date(t.subscription_end)
      const daysLeft = Math.ceil((end - now) / 86400000)
      if (daysLeft <= 7 && daysLeft >= 0) {
        alerts.push({ ...t, alertType: 'expiring', alertLabel: `Vence en ${daysLeft}d`, alertColor: 'amber' })
      } else if (daysLeft < 0) {
        alerts.push({ ...t, alertType: 'expired', alertLabel: 'Vencido', alertColor: 'rose' })
      }
    }
  })
  
  return alerts.slice(0, 5)
})

// Total clients (for distribution bar)
const totalTenants = computed(() => {
  const tenantsArray = Array.isArray(tenants.value) ? tenants.value : tenants.value?.data || []
  return tenantsArray.length || 1
})

// Security
const securityTabRef = ref(null)
const securityData = ref({ kpis: {} })
const fetchSecuritySummary = async () => {
  try {
    const res = await axios.get('/api/admin/security/dashboard')
    if (res.data.success) securityData.value = res.data.data
  } catch (e) { /* silent - security tables may not exist yet */ }
}
const loadSecurityData = () => {
  securityTabRef.value?.loadData()
  fetchSecuritySummary()
}

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
      axios.get('/api/admin/tenants'),
      fetchSecuritySummary()
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
    // Super admin usa /api/admin/ prefix
    const endpoint = `/api/admin/api/ai-monitoring/dashboard?period=${aiPeriod.value}`
    
    const res = await axios.get(endpoint)
    if (res.data) {
      aiMonitoring.value = res.data
    }
  } catch (error) {
    console.error('[GodMode] Error al cargar AI Monitoring:', error)
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

// Generar PDF profesional con credenciales
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
  
  // PALETA CORPORATIVA ENTERPRISE
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
    { num: '1', icon: '', title: 'INGRESE', desc: 'Abra la URL en\nsu navegador' },
    { num: '2', icon: '', title: 'ACCEDA', desc: 'Use sus credenciales\npara iniciar sesión' },
    { num: '3', icon: '', title: 'VENDA', desc: 'Configure su negocio\ny comience a vender' }
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

const viewingTenant = ref(null)

const viewTenantDetails = async (tenant) => {
  try {
    const res = await axios.get(`/api/admin/tenants/${tenant.id}`)
    if (res.data.success) {
      selectedTenant.value = res.data.data
      viewingTenant.value = res.data.data
    }
  } catch (error) {
    showNotification('error', 'Error al cargar detalles', error.message)
  }
}

const handleProfileDelete = (tenant) => {
  tenantToDelete.value = tenant
  showDeleteModal.value = true
}

const handleProfileBack = () => {
  viewingTenant.value = null
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
    'free_trial': 'bg-gray-50 dark:bg-zinc-900 text-gray-700 dark:text-zinc-300 border-gray-100 dark:border-zinc-800',
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
  fetchAIMonitoring()
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
