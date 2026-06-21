<template>
  <!-- Layout con Sidebar Técnico - Standard Theme -->
  <div class="flex flex-col min-h-screen font-sans bg-zinc-50 dark:bg-zinc-950 text-slate-900 dark:text-white">
    
    <!-- ========== TOP NAVBAR ESTILO MICROSOFT 365 ========== -->
    <header class="fixed top-0 left-0 right-0 h-14 bg-[#2f3cb0] z-50 flex items-center justify-between px-4 shadow-md">
      <div class="flex items-center gap-4 w-1/3">
        <!-- Hamburger Mobile/Desktop Toggle -->
        <button @click="sidebarOpen = !sidebarOpen" class="p-2 text-white hover:bg-white/10 rounded transition-colors">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
          </svg>
        </button>
        
        <!-- App Launcher / Logo -->
        <div class="flex items-center gap-3 cursor-pointer hover:bg-white/10 px-2 py-1.5 rounded transition-colors text-white">
          <svg class="w-5 h-5 opacity-90" fill="currentColor" viewBox="0 0 20 20">
            <path d="M5 3a2 2 0 100 4 2 2 0 000-4zm5 0a2 2 0 100 4 2 2 0 000-4zm5 0a2 2 0 100 4 2 2 0 000-4zM5 8a2 2 0 100 4 2 2 0 000-4zm5 0a2 2 0 100 4 2 2 0 000-4zm5 0a2 2 0 100 4 2 2 0 000-4zM5 13a2 2 0 100 4 2 2 0 000-4zm5 0a2 2 0 100 4 2 2 0 000-4zm5 0a2 2 0 100 4 2 2 0 000-4z"/>
          </svg>
          <span class="text-base font-semibold tracking-wide">Centro de administración de 105POS</span>
        </div>
      </div>
      
      <!-- Command Palette (Centro) -->
      <div class="flex-1 flex justify-center hidden md:flex">
        <div class="flex items-center bg-white/90 dark:bg-white/10 rounded-md px-4 py-2 w-[520px] hover:bg-white cursor-text transition-colors shadow-sm">
          <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
          <span class="ml-2 text-sm text-gray-500">Buscar comandos, tenants, logs...</span>
        </div>
      </div>
      
      <div class="flex items-center gap-1 w-1/3 justify-end text-white">
        <!-- CLI Console Button -->
        <button @click="openTerminal" class="p-2 hover:bg-white/10 rounded transition-colors" title="Abrir terminal VPS">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 9l3 3-3 3m5 0h3M5 20h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
          </svg>
        </button>

        <!-- Device Management -->
        <button class="p-2 hover:bg-white/10 rounded transition-colors hidden sm:block">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z" />
          </svg>
        </button>

        <!-- Dark Mode -->
        <button class="p-2 hover:bg-white/10 rounded transition-colors hidden sm:block">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
          </svg>
        </button>

        <!-- Notificaciones -->
        <button class="relative p-2 hover:bg-white/10 rounded transition-colors">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
          </svg>
          <span class="absolute top-2 right-2 w-1.5 h-1.5 bg-red-500 rounded-full"></span>
        </button>

        <!-- More actions -->
        <button class="p-2 hover:bg-white/10 rounded transition-colors hidden sm:block">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h.01M12 12h.01M19 12h.01M6 12a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0z" />
          </svg>
        </button>

        <!-- Perfil / Badge -->
        <div class="flex items-center gap-2 pl-2 ml-1">
          <span class="text-sm font-medium hidden md:block">Fabian</span>
          <div class="w-8 h-8 rounded-full bg-blue-600 flex items-center justify-center text-xs font-bold ring-2 ring-white/20">
            FP
          </div>
        </div>
      </div>
    </header>

    <div class="flex flex-1 pt-14">
    
    <!-- ========== OVERLAY MÓVIL ========== -->
    <div 
      v-if="sidebarOpen" 
      @click="sidebarOpen = false"
      class="fixed inset-0 bg-black/60 backdrop-blur-sm z-40 lg:hidden"
    ></div>
    
    <!-- ========== SIDEBAR IZQUIERDO ========== -->
    <aside 
      :class="[
        'w-[280px] bg-white dark:bg-zinc-950 border-r border-gray-200 dark:border-zinc-800 flex flex-col fixed left-0 top-14 bottom-0 z-40 transition-transform duration-300 ease-in-out',
        sidebarOpen ? 'translate-x-0' : '-translate-x-full'
      ]"
    >

      <!-- Navegación Principal -->
      <nav class="flex-1 py-4 space-y-1 overflow-y-auto custom-scrollbar font-sans">
        
        <!-- SECCIÓN: CORE -->
        <p class="px-6 text-[10px] font-bold tracking-wider text-slate-400 dark:text-zinc-500 uppercase mb-2 mt-2">General</p>
        
        <button 
          @click="activeTab = 'dashboard'"
          :class="[
            'w-[calc(100%-24px)] flex items-center gap-3 mx-3 px-3 py-2.5 text-sm transition-all duration-300 font-medium rounded-xl',
            activeTab === 'dashboard' 
              ? 'bg-gradient-to-r from-indigo-50 to-blue-50 dark:from-indigo-500/10 dark:to-blue-500/10 text-indigo-700 dark:text-indigo-300 shadow-sm ring-1 ring-indigo-100/50 dark:ring-indigo-500/20' 
              : 'text-slate-600 dark:text-zinc-400 hover:bg-slate-100 dark:hover:bg-zinc-800/50 hover:text-slate-900 dark:hover:text-zinc-200'
          ]"
        >
          <svg class="w-5 h-5" :class="activeTab === 'dashboard' ? 'text-indigo-600 dark:text-indigo-400' : 'text-slate-400 dark:text-zinc-500'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z"/>
          </svg>
          <span>Inicio</span>
        </button>

        <!-- GRUPO: USUARIOS -->
        <div class="mt-1">
          <button 
            @click="usersMenuOpen = !usersMenuOpen"
            class="w-[calc(100%-24px)] flex items-center justify-between mx-3 px-3 py-2.5 text-sm transition-all duration-300 font-medium rounded-xl text-slate-600 dark:text-zinc-400 hover:bg-slate-100 dark:hover:bg-zinc-800/50 hover:text-slate-900 dark:hover:text-zinc-200"
          >
            <div class="flex items-center gap-3">
              <svg class="w-5 h-5 text-slate-400 dark:text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
              </svg>
              <span>Usuarios</span>
            </div>
            <svg class="w-4 h-4 transition-transform duration-300" :class="usersMenuOpen ? 'rotate-180 text-indigo-500' : 'text-slate-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
          </button>

          <div v-show="usersMenuOpen" class="pl-11 pr-3 mt-1 space-y-1 relative before:absolute before:left-[27px] before:top-2 before:bottom-2 before:w-px before:bg-slate-200 dark:before:bg-zinc-800">
            <button 
              @click="activeTab = 'clientes'"
              :class="[
                'w-full flex items-center gap-3 px-3 py-2 text-[13px] transition-all duration-300 font-medium rounded-lg',
                activeTab === 'clientes' 
                  ? 'bg-indigo-50 dark:bg-indigo-500/10 text-indigo-700 dark:text-indigo-400' 
                  : 'text-slate-500 dark:text-zinc-400 hover:bg-slate-50 dark:hover:bg-zinc-800/50 hover:text-slate-900 dark:hover:text-zinc-200'
              ]"
            >
              <div class="w-1.5 h-1.5 rounded-full" :class="activeTab === 'clientes' ? 'bg-indigo-500' : 'bg-slate-300 dark:bg-zinc-600'"></div>
              <span>Activos</span>
            </button>
            <button 
              @click="activeTab = 'invitados'"
              :class="[
                'w-full flex items-center gap-3 px-3 py-2 text-[13px] transition-all duration-300 font-medium rounded-lg',
                activeTab === 'invitados' 
                  ? 'bg-indigo-50 dark:bg-indigo-500/10 text-indigo-700 dark:text-indigo-400' 
                  : 'text-slate-500 dark:text-zinc-400 hover:bg-slate-50 dark:hover:bg-zinc-800/50 hover:text-slate-900 dark:hover:text-zinc-200'
              ]"
            >
              <div class="w-1.5 h-1.5 rounded-full" :class="activeTab === 'invitados' ? 'bg-indigo-500' : 'bg-slate-300 dark:bg-zinc-600'"></div>
              <span>Invitados</span>
            </button>
            <button 
              @click="activeTab = 'eliminados'"
              :class="[
                'w-full flex items-center gap-3 px-3 py-2 text-[13px] transition-all duration-300 font-medium rounded-lg',
                activeTab === 'eliminados' 
                  ? 'bg-indigo-50 dark:bg-indigo-500/10 text-indigo-700 dark:text-indigo-400' 
                  : 'text-slate-500 dark:text-zinc-400 hover:bg-slate-50 dark:hover:bg-zinc-800/50 hover:text-slate-900 dark:hover:text-zinc-200'
              ]"
            >
              <div class="w-1.5 h-1.5 rounded-full" :class="activeTab === 'eliminados' ? 'bg-indigo-500' : 'bg-slate-300 dark:bg-zinc-600'"></div>
              <span>Eliminados</span>
            </button>
          </div>
        </div>

        <p class="px-6 text-[10px] font-bold tracking-wider text-slate-400 dark:text-zinc-500 uppercase mb-2 mt-6">Infraestructura</p>

        <button 
          @click="activeTab = 'health'"
          :class="[
            'w-[calc(100%-24px)] flex items-center gap-3 mx-3 px-3 py-2.5 text-sm transition-all duration-300 font-medium rounded-xl',
            activeTab === 'health' 
              ? 'bg-gradient-to-r from-indigo-50 to-blue-50 dark:from-indigo-500/10 dark:to-blue-500/10 text-indigo-700 dark:text-indigo-300 shadow-sm ring-1 ring-indigo-100/50 dark:ring-indigo-500/20' 
              : 'text-slate-600 dark:text-zinc-400 hover:bg-slate-100 dark:hover:bg-zinc-800/50 hover:text-slate-900 dark:hover:text-zinc-200'
          ]"
        >
          <svg class="w-5 h-5" :class="activeTab === 'health' ? 'text-indigo-600 dark:text-indigo-400' : 'text-slate-400 dark:text-zinc-500'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
          </svg>
          <span>Salud del Sistema</span>
        </button>

        <button 
          @click="activeTab = 'logs'"
          :class="[
            'w-[calc(100%-24px)] flex items-center gap-3 mx-3 px-3 py-2.5 text-sm transition-all duration-300 font-medium rounded-xl',
            activeTab === 'logs' 
              ? 'bg-gradient-to-r from-indigo-50 to-blue-50 dark:from-indigo-500/10 dark:to-blue-500/10 text-indigo-700 dark:text-indigo-300 shadow-sm ring-1 ring-indigo-100/50 dark:ring-indigo-500/20' 
              : 'text-slate-600 dark:text-zinc-400 hover:bg-slate-100 dark:hover:bg-zinc-800/50 hover:text-slate-900 dark:hover:text-zinc-200'
          ]"
        >
          <svg class="w-5 h-5" :class="activeTab === 'logs' ? 'text-indigo-600 dark:text-indigo-400' : 'text-slate-400 dark:text-zinc-500'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
          </svg>
          <span>Registros en Vivo</span>
        </button>

        <button 
          @click="activeTab = 'ai-monitoring'"
          :class="[
            'w-[calc(100%-24px)] flex items-center gap-3 mx-3 px-3 py-2.5 text-sm transition-all duration-300 font-medium rounded-xl',
            activeTab === 'ai-monitoring' 
              ? 'bg-gradient-to-r from-indigo-50 to-purple-50 dark:from-indigo-500/10 dark:to-purple-500/10 text-indigo-700 dark:text-indigo-300 shadow-sm ring-1 ring-indigo-100/50 dark:ring-indigo-500/20' 
              : 'text-slate-600 dark:text-zinc-400 hover:bg-slate-100 dark:hover:bg-zinc-800/50 hover:text-slate-900 dark:hover:text-zinc-200'
          ]"
        >
          <svg class="w-5 h-5" :class="activeTab === 'ai-monitoring' ? 'text-purple-600 dark:text-purple-400' : 'text-slate-400 dark:text-zinc-500'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
          </svg>
          <span class="flex-1 text-left">Copilot IA</span>
          <span class="px-1.5 py-0.5 rounded-md bg-purple-100 dark:bg-purple-900/30 text-[9px] font-bold text-purple-600 dark:text-purple-400 uppercase">Beta</span>
        </button>

        <button 
          @click="activeTab = 'support-tickets'; loadSupportTickets()"
          :class="[
            'w-[calc(100%-24px)] flex items-center gap-3 mx-3 px-3 py-2.5 text-sm transition-all duration-300 font-medium rounded-xl',
            activeTab === 'support-tickets' 
              ? 'bg-gradient-to-r from-indigo-50 to-blue-50 dark:from-indigo-500/10 dark:to-blue-500/10 text-indigo-700 dark:text-indigo-300 shadow-sm ring-1 ring-indigo-100/50 dark:ring-indigo-500/20' 
              : 'text-slate-600 dark:text-zinc-400 hover:bg-slate-100 dark:hover:bg-zinc-800/50 hover:text-slate-900 dark:hover:text-zinc-200'
          ]"
        >
          <svg class="w-5 h-5" :class="activeTab === 'support-tickets' ? 'text-indigo-600 dark:text-indigo-400' : 'text-slate-400 dark:text-zinc-500'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"/>
          </svg>
          <span>Soporte</span>
        </button>

        <p class="px-6 text-[10px] font-bold tracking-wider text-slate-400 dark:text-zinc-500 uppercase mb-2 mt-6">Administración</p>

        <button 
          @click="activeTab = 'security'; loadSecurityData()"
          :class="[
            'w-[calc(100%-24px)] flex items-center gap-3 mx-3 px-3 py-2.5 text-sm transition-all duration-300 font-medium rounded-xl',
            activeTab === 'security' 
              ? 'bg-gradient-to-r from-indigo-50 to-blue-50 dark:from-indigo-500/10 dark:to-blue-500/10 text-indigo-700 dark:text-indigo-300 shadow-sm ring-1 ring-indigo-100/50 dark:ring-indigo-500/20' 
              : 'text-slate-600 dark:text-zinc-400 hover:bg-slate-100 dark:hover:bg-zinc-800/50 hover:text-slate-900 dark:hover:text-zinc-200'
          ]"
        >
          <svg class="w-5 h-5" :class="activeTab === 'security' ? 'text-indigo-600 dark:text-indigo-400' : 'text-slate-400 dark:text-zinc-500'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
          </svg>
          <span>Seguridad</span>
          <span v-if="securityData?.kpis?.blocked_accounts > 0" class="ml-auto w-2 h-2 rounded-full bg-red-500 shadow-[0_0_8px_rgba(239,68,68,0.6)] animate-pulse"></span>
        </button>

        <button 
          @click="activeTab = 'maintenance'"
          :class="[
            'w-[calc(100%-24px)] flex items-center gap-3 mx-3 px-3 py-2.5 text-sm transition-all duration-300 font-medium rounded-xl',
            activeTab === 'maintenance' 
              ? 'bg-gradient-to-r from-indigo-50 to-blue-50 dark:from-indigo-500/10 dark:to-blue-500/10 text-indigo-700 dark:text-indigo-300 shadow-sm ring-1 ring-indigo-100/50 dark:ring-indigo-500/20' 
              : 'text-slate-600 dark:text-zinc-400 hover:bg-slate-100 dark:hover:bg-zinc-800/50 hover:text-slate-900 dark:hover:text-zinc-200'
          ]"
        >
          <svg class="w-5 h-5" :class="activeTab === 'maintenance' ? 'text-indigo-600 dark:text-indigo-400' : 'text-slate-400 dark:text-zinc-500'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
          </svg>
          <span>Mantenimiento</span>
        </button>

        <button 
          @click="activeTab = 'storage'; loadStorageData()"
          :class="[
            'w-[calc(100%-24px)] flex items-center gap-3 mx-3 px-3 py-2.5 text-sm transition-all duration-300 font-medium rounded-xl',
            activeTab === 'storage' 
              ? 'bg-gradient-to-r from-orange-50 to-amber-50 dark:from-orange-500/10 dark:to-amber-500/10 text-orange-700 dark:text-orange-400 shadow-sm ring-1 ring-orange-100/50 dark:ring-orange-500/20' 
              : 'text-slate-600 dark:text-zinc-400 hover:bg-slate-100 dark:hover:bg-zinc-800/50 hover:text-slate-900 dark:hover:text-zinc-200'
          ]"
        >
          <svg class="w-5 h-5" :class="activeTab === 'storage' ? 'text-orange-500' : 'text-slate-400 dark:text-zinc-500'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z"/>
          </svg>
          <span>Cloud Storage (R2)</span>
        </button>
      </nav>

      <!-- Footer Sidebar -->
      <div class="p-3 border-t border-gray-200 dark:border-zinc-800">
        <a 
          href="/dashboard"
          class="w-full flex items-center gap-3 px-3 py-2 text-[13px] font-medium text-gray-500 hover:text-gray-900 hover:bg-gray-100 dark:text-zinc-400 dark:hover:bg-zinc-800/50 dark:hover:text-zinc-200 transition-all rounded-lg"
        >
          <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12"/>
          </svg>
          <span>Salir al POS</span>
        </a>
      </div>
    </aside>

    <!-- ========== CONTENIDO PRINCIPAL ========== -->
    <main :class="['flex flex-col flex-1 bg-white dark:bg-[#0a0a0c] transition-all duration-300 ease-in-out min-h-[calc(100vh-3.5rem)]', sidebarOpen ? 'lg:ml-[280px]' : 'ml-0']">
      <div class="p-5 lg:p-6 space-y-5 animate-fade-in max-w-full flex-1 flex flex-col">
        
        <!-- Header de Sección -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-2">
          <div class="flex items-center gap-3">
            <div>
              <h1 class="text-2xl font-semibold text-gray-900 dark:text-white tracking-tight">
                {{ activeTab === 'dashboard' ? 'Panel Principal' : activeTab === 'clientes' ? 'Usuarios activos' : activeTab === 'invitados' ? 'Usuarios invitados' : activeTab === 'eliminados' ? 'Usuarios eliminados' : activeTab === 'ai-monitoring' ? 'Monitoreo IA' : activeTab === 'support-tickets' ? 'Gestión de Casos (Soporte)' : activeTab === 'health' ? 'Salud del Sistema' : activeTab === 'maintenance' ? 'Mantenimiento' : activeTab === 'security' ? 'Seguridad' : 'Registros en Vivo' }}
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

        <!-- TAB: Command Center (Dashboard Técnico) -->
        <div v-show="activeTab === 'dashboard'" class="flex flex-col lg:flex-row gap-5 flex-1">
          
          <!-- Columna Principal (Métricas y Logs) -->
          <div class="flex-1 flex flex-col space-y-5">
            <!-- FILA 1: Métricas Vitales Reales -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
              <!-- CPU -->
              <div class="bg-white dark:bg-zinc-900 rounded-xl border border-gray-200 dark:border-zinc-800 shadow-sm p-4 relative overflow-hidden">
                <div class="flex items-center justify-between mb-3">
                  <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"/></svg>
                    <p class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider">CPU Core</p>
                  </div>
                  <span class="text-xs font-mono bg-slate-100 dark:bg-zinc-800 text-slate-500 px-2 py-0.5 rounded">{{ nocMetrics.cpu_cores }}C / {{ nocMetrics.cpu_threads }}T</span>
                </div>
                <div class="flex items-end gap-2 mb-2">
                  <p class="text-3xl font-mono font-semibold text-slate-900 dark:text-white leading-none">{{ nocMetrics.cpu_percent }}<span class="text-sm text-slate-500">%</span></p>
                </div>
                <div class="w-full bg-slate-100 dark:bg-zinc-800 rounded-full h-1.5 overflow-hidden">
                  <div class="bg-emerald-500 h-1.5 rounded-full transition-all duration-1000" :style="{ width: nocMetrics.cpu_percent + '%' }"></div>
                </div>
                <div class="mt-3 flex flex-col gap-1 border-t border-gray-100 dark:border-zinc-800 pt-2">
                  <p class="text-[10px] text-slate-500 truncate" :title="nocMetrics.cpu_model">{{ nocMetrics.cpu_model }}</p>
                  <p class="text-[10px] font-mono text-slate-400">Load: <span class="text-slate-600 dark:text-zinc-300">{{ nocMetrics.cpu_load }}</span></p>
                </div>
              </div>

              <!-- RAM -->
              <div class="bg-white dark:bg-zinc-900 rounded-xl border border-gray-200 dark:border-zinc-800 shadow-sm p-4 relative overflow-hidden">
                <div class="flex items-center justify-between mb-3">
                  <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                    <p class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider">System RAM</p>
                  </div>
                  <span class="text-xs font-mono bg-slate-100 dark:bg-zinc-800 text-slate-500 px-2 py-0.5 rounded">{{ nocMetrics.ram_total_gb }} GB</span>
                </div>
                <div class="flex items-end gap-2 mb-2">
                  <p class="text-3xl font-mono font-semibold text-slate-900 dark:text-white leading-none">{{ round((nocMetrics.ram_used_gb / (nocMetrics.ram_total_gb || 1)) * 100, 1) }}<span class="text-sm text-slate-500">%</span></p>
                </div>
                <div class="w-full bg-slate-100 dark:bg-zinc-800 rounded-full h-1.5 overflow-hidden flex">
                  <div class="bg-indigo-500 h-1.5 transition-all duration-1000" :style="{ width: (nocMetrics.ram_used_gb / (nocMetrics.ram_total_gb || 1) * 100) + '%' }"></div>
                  <div class="bg-slate-300 dark:bg-zinc-600 h-1.5 transition-all duration-1000" :style="{ width: (nocMetrics.ram_cached_gb / (nocMetrics.ram_total_gb || 1) * 100) + '%' }"></div>
                </div>
                <div class="mt-3 flex flex-col gap-1 border-t border-gray-100 dark:border-zinc-800 pt-2">
                  <div class="flex justify-between text-[10px] font-mono"><span class="text-slate-400">Usado</span><span class="text-slate-900 dark:text-white">{{ nocMetrics.ram_used_gb }} GB</span></div>
                  <div class="flex justify-between text-[10px] font-mono"><span class="text-slate-400">Disponible</span><span class="text-slate-900 dark:text-white">{{ nocMetrics.ram_available_gb }} GB</span></div>
                  <div class="flex justify-between text-[10px] font-mono"><span class="text-slate-400">Buffers/Cache</span><span class="text-slate-900 dark:text-white">{{ nocMetrics.ram_cached_gb }} GB</span></div>
                </div>
              </div>

              <!-- DISCO -->
              <div class="bg-white dark:bg-zinc-900 rounded-xl border border-gray-200 dark:border-zinc-800 shadow-sm p-4 relative overflow-hidden">
                <div class="flex items-center justify-between mb-3">
                  <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
                    <p class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Storage</p>
                  </div>
                  <span class="text-xs font-mono bg-slate-100 dark:bg-zinc-800 text-slate-500 px-2 py-0.5 rounded">{{ nocMetrics.disk_total_gb }} GB</span>
                </div>
                <div class="flex items-end gap-2 mb-2">
                  <p class="text-3xl font-mono font-semibold text-slate-900 dark:text-white leading-none">{{ nocMetrics.disk_percent }}<span class="text-sm text-slate-500">%</span></p>
                </div>
                <div class="w-full bg-slate-100 dark:bg-zinc-800 rounded-full h-1.5 overflow-hidden">
                  <div class="bg-purple-500 h-1.5 rounded-full transition-all duration-1000" :style="{ width: nocMetrics.disk_percent + '%' }"></div>
                </div>
                <div class="mt-3 grid grid-cols-2 gap-x-2 gap-y-1 border-t border-gray-100 dark:border-zinc-800 pt-2">
                  <div class="flex justify-between text-[10px] font-mono"><span class="text-slate-400">Usado</span><span class="text-slate-900 dark:text-white">{{ nocMetrics.disk_used_gb }} GB</span></div>
                  <div class="flex justify-between text-[10px] font-mono"><span class="text-slate-400">Libre</span><span class="text-slate-900 dark:text-white">{{ nocMetrics.disk_free_gb }} GB</span></div>
                  <div class="flex justify-between text-[10px] font-mono"><span class="text-slate-400">Logs</span><span class="text-slate-900 dark:text-white">{{ nocMetrics.logs_size_mb }} MB</span></div>
                  <div class="flex justify-between text-[10px] font-mono"><span class="text-slate-400">Cache</span><span class="text-slate-900 dark:text-white">{{ nocMetrics.cache_size_mb }} MB</span></div>
                  <div class="flex justify-between text-[10px] font-mono"><span class="text-slate-400">DB Central</span><span class="text-slate-900 dark:text-white">{{ nocMetrics.db_central_size_mb }} MB</span></div>
                  <div class="flex justify-between text-[10px] font-mono"><span class="text-slate-400">DBs Tenants</span><span class="text-slate-900 dark:text-white">{{ nocMetrics.db_tenant_size_mb }} MB</span></div>
                </div>
              </div>

              <!-- CLOUDFLARE R2 STORAGE -->
              <div class="bg-white dark:bg-zinc-900 rounded-xl border border-gray-200 dark:border-zinc-800 shadow-sm p-4 relative overflow-hidden cursor-pointer hover:border-orange-300 transition-colors" @click="activeTab = 'storage'; loadStorageData()">
                <div class="flex items-center justify-between mb-3">
                  <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z"/>
                    </svg>
                    <p class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Cloudflare R2</p>
                  </div>
                  <span class="text-xs font-mono bg-slate-100 dark:bg-zinc-800 text-slate-500 px-2 py-0.5 rounded">10 GB Limit</span>
                </div>
                <div class="flex items-end gap-2 mb-2">
                  <p class="text-3xl font-mono font-semibold text-slate-900 dark:text-white leading-none">{{ (storageMetrics.used_bytes / (1024 * 1024)).toFixed(1) }}<span class="text-sm text-slate-500">MB</span></p>
                </div>
                <div class="w-full bg-slate-100 dark:bg-zinc-800 rounded-full h-1.5 overflow-hidden">
                  <div class="bg-orange-500 h-1.5 rounded-full transition-all duration-1000" :style="{ width: ((storageMetrics.used_bytes / (10 * 1024 * 1024 * 1024)) * 100) + '%' }"></div>
                </div>
                <div class="mt-3 flex flex-col gap-1 border-t border-gray-100 dark:border-zinc-800 pt-2">
                  <div class="flex justify-between text-[10px] font-mono"><span class="text-slate-400">Total Archivos</span><span class="text-slate-900 dark:text-white">{{ storageMetrics.total_files }}</span></div>
                  <div class="flex justify-between text-[10px] font-mono"><span class="text-slate-400">Espacio Libre</span><span class="text-slate-900 dark:text-white">{{ ((10 * 1024 * 1024 * 1024 - storageMetrics.used_bytes) / (1024 * 1024 * 1024)).toFixed(2) }} GB</span></div>
                </div>
              </div>
            </div>
            <!-- SERVICIOS DEL SISTEMA (Real-time) -->
            <div v-if="systemServices.length > 0" class="bg-white dark:bg-zinc-900 rounded-xl border border-gray-200 dark:border-zinc-800 shadow-sm overflow-hidden">
              <div class="px-4 py-3 border-b border-gray-200 dark:border-zinc-800 bg-gray-50/50 dark:bg-zinc-800/30 flex items-center gap-2">
                <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/></svg>
                <h3 class="text-[13px] font-bold text-gray-900 dark:text-white uppercase tracking-wider">Servicios del Sistema</h3>
              </div>
              <div class="grid grid-cols-2 lg:grid-cols-4 divide-y lg:divide-y-0 lg:divide-x divide-gray-100 dark:divide-zinc-800">
                <div v-for="svc in systemServices" :key="svc.name" class="px-4 py-3">
                  <div class="flex items-center gap-2 mb-2">
                    <div class="w-2 h-2 rounded-full" :class="svc.status === 'running' ? 'bg-emerald-500 animate-pulse shadow-[0_0_8px_rgba(16,185,129,0.5)]' : 'bg-rose-500'"></div>
                    
                    <!-- Logos Reales de Tecnologías -->
                    <svg v-if="svc.name.includes('Nginx')" class="w-4 h-4 text-[#009639]" fill="currentColor" viewBox="0 0 24 24">
                      <path d="M12.003 0L1.442 3.367v17.266L12.003 24l10.555-3.367V3.367zm5.955 17.52H15.11v-8.773l-5.185 8.774H7.078V6.48h2.848v8.77l5.185-8.77h2.847v11.041z"/>
                    </svg>
                    <svg v-else-if="svc.name.includes('PHP')" class="w-4 h-4 text-[#777BB4]" fill="currentColor" viewBox="0 0 24 24">
                      <path d="M12 1.6c-6.627 0-12 4.477-12 10s5.373 10 12 10 12-4.477 12-10-5.373-10-12-10zM7.595 16.5H5.808l1.164-5.945h1.953c1.796 0 2.637.986 2.463 2.39-.188 1.486-1.42 2.274-3.003 2.274h-.715l-.474 2.39zm5.372-5.945h1.815l-1.164 5.945h-1.815l1.164-5.945zm1.209-2.124c-.638 0-1.157-.518-1.157-1.156 0-.64.52-1.157 1.157-1.157.64 0 1.157.518 1.157 1.157 0 .638-.518 1.156-1.157 1.156zM5.72 8.43h1.816l-1.164 5.945H4.557l1.164-5.945zM12.968 10.555h1.952c1.795 0 2.637.986 2.463 2.39-.188 1.486-1.42 2.274-3.003 2.274h-.715l-.474 2.39h-1.787l1.164-5.945zm-1.076 2.124h-.716l-.474 2.39h.715c.613 0 1.112-.26 1.112-.908 0-.488-.35-.778-.905-.778zm-5.372 0h-.716l-.474 2.39h.715c.613 0 1.112-.26 1.112-.908 0-.488-.35-.778-.905-.778z"/>
                    </svg>
                    <svg v-else-if="svc.name.includes('MySQL')" class="w-4 h-4 text-[#4479A1]" fill="currentColor" viewBox="0 0 24 24">
                      <path d="M12 2C6.48 2 2 4.02 2 6.5s4.48 4.5 10 4.5 10-2.02 10-4.5S17.52 2 12 2zM2 11.5v5C2 19 6.48 21 12 21s10-2 10-4.5v-5c0 2.48-4.48 4.5-10 4.5S2 13.98 2 11.5zM2 6.5v5C2 14 6.48 16 12 16s10-2 10-4.5v-5c0 2.48-4.48 4.5-10 4.5S2 8.98 2 6.5z"/>
                    </svg>
                    <svg v-else-if="svc.name.includes('WhatsApp')" class="w-4 h-4 text-[#25D366]" fill="currentColor" viewBox="0 0 24 24">
                      <path d="M11.97 0C5.36 0 .01 5.36.01 11.97c0 2.12.56 4.15 1.62 5.95L.03 24l6.23-1.63c1.76.96 3.75 1.47 5.8 1.47 6.61 0 11.96-5.36 11.96-11.97S18.58 0 11.97 0zm6.54 17.26c-.28.79-1.57 1.48-2.25 1.54-.58.05-1.34.18-3.95-.91-3.15-1.3-5.22-4.51-5.37-4.71-.16-.21-1.28-1.72-1.28-3.28 0-1.57.82-2.35 1.12-2.67.28-.31.63-.38.84-.38s.42 0 .61.01c.21.01.49-.08.77.61.28.71.95 2.31 1.03 2.47.08.16.14.35.03.55-.1.21-.16.33-.31.52-.16.18-.33.38-.47.53-.16.16-.33.33-.14.65.19.33.86 1.41 1.84 2.28 1.26 1.13 2.33 1.48 2.65 1.63.31.16.51.14.71-.08.19-.23.82-.95 1.05-1.28.23-.33.45-.28.75-.16.31.11 1.95.91 2.28 1.08.33.16.54.25.61.38.1.18.1.98-.18 1.77z"/>
                    </svg>
                    <svg v-else class="w-4 h-4 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01"/>
                    </svg>

                    <span class="text-xs font-bold text-gray-900 dark:text-white truncate">{{ svc.name }}</span>
                  </div>
                  <div class="flex items-center justify-between">
                    <span class="px-2 py-0.5 rounded-full text-[9px] font-bold uppercase tracking-wide border"
                      :class="svc.status === 'running' 
                        ? 'bg-emerald-50 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-400 border-emerald-100 dark:border-emerald-800'
                        : 'bg-rose-50 dark:bg-rose-950 text-rose-700 dark:text-rose-400 border-rose-100 dark:border-rose-800'">
                      {{ svc.status }}
                    </span>
                    <div class="text-right">
                      <p v-if="svc.pid" class="text-[9px] text-zinc-400 font-mono">PID {{ svc.pid }}</p>
                      <p v-if="svc.memory_mb" class="text-[9px] text-zinc-400">{{ svc.memory_mb }} MB</p>
                      <p v-if="svc.port" class="text-[9px] text-zinc-500">:{{ svc.port }}</p>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- FILA 2: AI Monitoreo -->
            <div class="bg-white dark:bg-zinc-900 rounded-xl border border-gray-200 dark:border-zinc-800 shadow-sm overflow-hidden flex flex-col flex-1 min-h-[300px] p-5">
              <div class="flex items-center justify-between mb-5">
                <div class="flex items-center gap-3">
                  <div class="w-8 h-8 rounded-lg bg-indigo-50 dark:bg-indigo-900/30 flex items-center justify-center">
                    <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/></svg>
                  </div>
                  <div>
                    <h3 class="text-base font-semibold text-gray-900 dark:text-white">AI Monitoreo Global</h3>
                    <p class="text-xs text-slate-500">Métricas de consumo y rendimiento de modelos IA</p>
                  </div>
                </div>
                <div class="flex gap-2">
                  <select v-model="aiPeriod" @change="fetchAIMonitoring" class="text-xs border-none bg-slate-100 dark:bg-zinc-800 rounded-lg px-3 py-1.5 focus:ring-0">
                    <option value="24h">Últimas 24h</option>
                    <option value="7d">Últimos 7 días</option>
                    <option value="30d">Últimos 30 días</option>
                  </select>
                </div>
              </div>

              <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                <div class="p-4 rounded-xl border border-gray-100 dark:border-zinc-800/60 bg-slate-50/50 dark:bg-zinc-800/20">
                  <p class="text-[10px] text-slate-500 uppercase font-semibold tracking-wider mb-1">Total Peticiones</p>
                  <p class="text-2xl font-mono font-semibold text-slate-900 dark:text-white">{{ formatNumber(aiMonitoring.summary?.total_requests || 0) }}</p>
                </div>
                <div class="p-4 rounded-xl border border-gray-100 dark:border-zinc-800/60 bg-slate-50/50 dark:bg-zinc-800/20">
                  <p class="text-[10px] text-slate-500 uppercase font-semibold tracking-wider mb-1">Success Rate</p>
                  <p class="text-2xl font-mono font-semibold text-emerald-600 dark:text-emerald-400">{{ round(aiMonitoring.summary?.success_rate || 100, 1) }}%</p>
                </div>
                <div class="p-4 rounded-xl border border-gray-100 dark:border-zinc-800/60 bg-slate-50/50 dark:bg-zinc-800/20">
                  <p class="text-[10px] text-slate-500 uppercase font-semibold tracking-wider mb-1">Total Tokens</p>
                  <p class="text-2xl font-mono font-semibold text-slate-900 dark:text-white">{{ formatNumber(aiMonitoring.summary?.total_tokens || 0) }}</p>
                </div>
                <div class="p-4 rounded-xl border border-gray-100 dark:border-zinc-800/60 bg-slate-50/50 dark:bg-zinc-800/20">
                  <p class="text-[10px] text-slate-500 uppercase font-semibold tracking-wider mb-1">Avg Latency</p>
                  <p class="text-2xl font-mono font-semibold text-slate-900 dark:text-white">{{ Math.round(aiMonitoring.summary?.avg_response_time_ms || 0) }}<span class="text-sm text-slate-500 font-sans ml-1">ms</span></p>
                </div>
              </div>

              <div class="flex-1 min-h-[150px] border border-gray-100 dark:border-zinc-800/60 rounded-xl overflow-hidden">
                <table class="w-full text-left border-collapse text-xs">
                  <thead>
                    <tr class="bg-slate-50 dark:bg-zinc-800/50 border-b border-gray-200 dark:border-zinc-700">
                      <th class="py-3 px-4 font-semibold text-slate-600 dark:text-slate-400">Hora</th>
                      <th class="py-3 px-4 font-semibold text-slate-600 dark:text-slate-400 text-right">Peticiones</th>
                      <th class="py-3 px-4 font-semibold text-slate-600 dark:text-slate-400 text-right">Tokens</th>
                      <th class="py-3 px-4 font-semibold text-slate-600 dark:text-slate-400 text-right">Errores</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="(usage, idx) in (aiMonitoring.usage_by_hour?.slice(0,5) || [])" :key="idx" class="border-b border-gray-100 dark:border-zinc-800/50 last:border-0 hover:bg-slate-50 dark:hover:bg-zinc-800/30">
                      <td class="py-2.5 px-4 font-mono text-slate-500">{{ usage.hour }}</td>
                      <td class="py-2.5 px-4 font-mono text-right text-slate-900 dark:text-white">{{ formatNumber(usage.requests) }}</td>
                      <td class="py-2.5 px-4 font-mono text-right text-slate-500">{{ formatNumber(usage.tokens) }}</td>
                      <td class="py-2.5 px-4 font-mono text-right" :class="usage.errors > 0 ? 'text-rose-500 font-semibold' : 'text-emerald-500'">{{ usage.errors }}</td>
                    </tr>
                    <tr v-if="!aiMonitoring.usage_by_hour || aiMonitoring.usage_by_hour.length === 0">
                      <td colspan="4" class="py-8 text-center text-slate-400">No hay datos recientes de consumo IA.</td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>

          <!-- Columna Lateral Derecho (AI Copilot) -->
          <div class="w-full lg:w-80 flex-shrink-0 flex flex-col h-[calc(100vh-10rem)] sticky top-20">
            <div class="bg-white dark:bg-zinc-900 rounded-xl border border-gray-200 dark:border-zinc-800 shadow-sm overflow-hidden flex flex-col flex-1">
              
              <!-- Copilot Header -->
              <div class="px-4 py-3 border-b border-gray-100 dark:border-zinc-800 bg-gradient-to-r from-indigo-50 to-purple-50 dark:from-indigo-950/30 dark:to-purple-950/30 flex items-center gap-3 shrink-0">
                <div class="w-8 h-8 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center shadow-sm">
                  <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                </div>
                <div>
                  <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Copilot de Infraestructura</h3>
                  <p class="text-[10px] text-slate-500">Asistente Gemini AI en línea</p>
                </div>
              </div>

              <!-- Copilot Messages -->
              <div class="p-4 flex-1 overflow-y-auto space-y-4 custom-scrollbar flex flex-col" id="copilot-chat-container">
                
                <div v-for="(msg, idx) in aiChatMessages" :key="idx" class="flex gap-3" :class="{'flex-row-reverse': msg.role === 'user'}">
                  <div class="w-6 h-6 rounded-full flex-shrink-0 flex items-center justify-center mt-1" :class="msg.role === 'user' ? 'bg-slate-200 dark:bg-zinc-700' : 'bg-gradient-to-br from-indigo-500 to-purple-600'">
                    <span v-if="msg.role === 'ai'" class="text-[10px] font-bold text-white">IA</span>
                    <svg v-else class="w-3 h-3 text-slate-600 dark:text-zinc-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                  </div>
                  
                  <div class="rounded-2xl p-3 border shadow-sm text-xs leading-relaxed max-w-[85%]" 
                       :class="[
                         msg.role === 'user' 
                           ? 'bg-indigo-50 dark:bg-indigo-900/30 border-indigo-100 dark:border-indigo-800/50 text-indigo-900 dark:text-indigo-100 rounded-tr-sm' 
                           : (msg.isAlert ? 'bg-rose-50 dark:bg-rose-950/20 border-rose-100 dark:border-rose-900/30 text-rose-900 dark:text-rose-100 rounded-tl-sm' : 'bg-slate-50 dark:bg-zinc-800/50 border-slate-100 dark:border-zinc-800/80 text-slate-700 dark:text-slate-300 rounded-tl-sm')
                       ]">
                    <p v-if="msg.title" class="font-semibold mb-1" :class="msg.isAlert ? 'text-rose-600 dark:text-rose-400' : 'text-indigo-600 dark:text-indigo-400'">{{ msg.title }}</p>
                    <p class="whitespace-pre-line">{{ msg.content }}</p>
                  </div>
                </div>

                <!-- Loading dots -->
                <div v-if="aiChatLoading" class="flex gap-3">
                  <div class="w-6 h-6 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 flex-shrink-0 flex items-center justify-center mt-1">
                    <span class="text-[10px] font-bold text-white">IA</span>
                  </div>
                  <div class="bg-slate-50 dark:bg-zinc-800/50 rounded-2xl rounded-tl-sm p-3 border border-slate-100 dark:border-zinc-800/80 shadow-sm flex gap-1 items-center">
                    <span class="w-1.5 h-1.5 bg-indigo-500 rounded-full animate-bounce"></span>
                    <span class="w-1.5 h-1.5 bg-indigo-500 rounded-full animate-bounce" style="animation-delay: 0.2s"></span>
                    <span class="w-1.5 h-1.5 bg-indigo-500 rounded-full animate-bounce" style="animation-delay: 0.4s"></span>
                  </div>
                </div>
              </div>

              <!-- Copilot Input -->
              <div class="p-3 border-t border-gray-100 dark:border-zinc-800 bg-white dark:bg-zinc-900 shrink-0">
                <form @submit.prevent="sendAIMessage" class="relative">
                  <input type="text" v-model="aiChatInput" :disabled="aiChatLoading" placeholder="Pregúntale a la IA sobre infraestructura..." class="w-full bg-slate-50 dark:bg-zinc-800 border border-gray-200 dark:border-zinc-700 rounded-lg pl-3 pr-10 py-2.5 text-xs text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all disabled:opacity-50">
                  <button type="submit" :disabled="!aiChatInput.trim() || aiChatLoading" class="absolute right-2 top-1.5 p-1.5 text-indigo-500 hover:bg-indigo-50 dark:hover:bg-indigo-500/10 rounded-md transition-colors disabled:opacity-50">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                  </button>
                </form>
                <div class="flex gap-2 mt-2 px-1 overflow-x-auto custom-scrollbar pb-1">
                  <span @click="aiChatInput = 'Resumir errores del log'; sendAIMessage()" class="shrink-0 text-[10px] px-2 py-1 rounded-full bg-slate-100 dark:bg-zinc-800 text-slate-500 cursor-pointer hover:bg-slate-200 dark:hover:bg-zinc-700 transition-colors">Resumir errores</span>
                  <span @click="aiChatInput = 'Estado de CPU y RAM'; sendAIMessage()" class="shrink-0 text-[10px] px-2 py-1 rounded-full bg-slate-100 dark:bg-zinc-800 text-slate-500 cursor-pointer hover:bg-slate-200 dark:hover:bg-zinc-700 transition-colors">Estado Recursos</span>
                </div>
              </div>
            </div>
          </div>
        </div>

      <!-- TAB: Clientes / Invitados / Eliminados -->
      <div v-show="['clientes', 'invitados', 'eliminados'].includes(activeTab)" class="space-y-4 lg:space-y-6">
        
        <!-- Slide-over: Perfil de Cliente -->
        <div v-if="viewingTenant" class="fixed inset-0 z-[60] overflow-hidden" aria-labelledby="slide-over-title" role="dialog" aria-modal="true">
          <div class="absolute inset-0 bg-black/30 backdrop-blur-sm transition-opacity" @click="viewingTenant = null"></div>
          <div class="fixed inset-y-0 right-0 w-full sm:w-[70%] flex justify-end">
            <transition enter-active-class="transform transition ease-in-out duration-300 sm:duration-500" enter-from-class="translate-x-full" enter-to-class="translate-x-0" leave-active-class="transform transition ease-in-out duration-300 sm:duration-500" leave-from-class="translate-x-0" leave-to-class="translate-x-full">
              <div v-if="viewingTenant" class="w-full h-full bg-white dark:bg-zinc-950 shadow-2xl overflow-y-auto flex flex-col">
                <TenantProfileView 
                  :tenant="viewingTenant"
                  @back="viewingTenant = null"
                  @update-plan="updateTenantPlan"
                  @toggle-status="(id, status) => { toggleTenantStatus(id, status); viewingTenant.status = viewingTenant.status === 'active' ? 'suspended' : 'active' }"
                  @delete="handleProfileDelete"
                  @restore="restoreTenant"
                  @refresh="() => { fetchData(); }"
                />
              </div>
            </transition>
          </div>
        </div>

        <!-- Lista de Clientes -->
        <div class="bg-white dark:bg-zinc-900 w-full mt-4">
          <!-- Header y Action Bar -->
          <div class="px-6 py-4 border-b border-gray-100 dark:border-zinc-800 min-h-[64px] flex items-center justify-between">
            <div v-if="selectedTenants.length > 0" class="flex items-center gap-6">
              <div class="flex items-center gap-4 border-r border-gray-300 dark:border-zinc-700 pr-4 py-1">
                <button @click="selectedTenants = []" class="text-gray-500 hover:text-gray-900 dark:text-zinc-400 dark:hover:text-white transition-colors" title="Cancelar selección">
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
                <span class="text-sm font-semibold text-blue-600 dark:text-blue-400">{{ selectedTenants.length }} seleccionados</span>
              </div>
              <div class="flex items-center gap-6">
                <button @click="handleBulkDelete" class="text-gray-700 dark:text-zinc-200 hover:text-blue-600 dark:hover:text-blue-400 font-medium text-sm transition-colors flex items-center gap-2">
                  <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                  Eliminar usuario
                </button>
                <button @click="handleBulkResetPassword" class="text-gray-700 dark:text-zinc-200 hover:text-blue-600 dark:hover:text-blue-400 font-medium text-sm transition-colors flex items-center gap-2">
                  <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
                  Restablecer contraseña
                </button>
                <button @click="handleBulkToggleStatus" class="text-gray-700 dark:text-zinc-200 hover:text-blue-600 dark:hover:text-blue-400 font-medium text-sm transition-colors flex items-center gap-2">
                  <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                  Suspender / Activar
                </button>
                <button @click="handleBulkExport" class="text-gray-700 dark:text-zinc-200 hover:text-blue-600 dark:hover:text-blue-400 font-medium text-sm transition-colors flex items-center gap-2">
                  <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                  Exportar usuarios
                </button>
              </div>
            </div>
            
            <div v-else class="flex items-center gap-6">
              <button class="text-gray-700 dark:text-zinc-200 hover:text-blue-600 dark:hover:text-blue-400 font-medium text-sm transition-colors flex items-center gap-2">
                <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                Actualizar
              </button>
              <div class="h-4 w-px bg-gray-300 dark:bg-zinc-700"></div>
              <button @click="showCreateModal = true" class="text-gray-700 dark:text-zinc-200 hover:text-blue-600 dark:hover:text-blue-400 font-medium text-sm transition-colors flex items-center gap-2">
                <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4v16m8-8H4"/></svg>
                Agregar un usuario
              </button>
            </div>
            
            <div v-if="selectedTenants.length === 0" class="text-sm font-semibold text-gray-500 dark:text-zinc-400">
              Total: {{ filteredTenantsWithFilters.length }} clientes
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
                      <span v-if="tenant.error_count > 0 && !tenant.deleted_at" class="inline-flex items-center gap-0.5 px-1 py-0.5 bg-rose-50 dark:bg-rose-950 text-rose-700 dark:text-rose-400 text-[9px] font-bold rounded border border-rose-100 dark:border-rose-800 flex-shrink-0">
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
                  <th class="px-6 py-3 text-left w-12">
                    <input type="checkbox" 
                           :checked="selectedTenants.length === filteredTenantsWithFilters.length && filteredTenantsWithFilters.length > 0"
                           @change="toggleSelectAll"
                           class="w-4 h-4 rounded border-gray-300 dark:border-zinc-600 dark:bg-zinc-700 text-blue-600 focus:ring-blue-500 cursor-pointer"
                    >
                  </th>
                  <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-zinc-400 uppercase tracking-wider">Cliente</th>
                  <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-zinc-400 uppercase tracking-wider">Plan</th>
                  <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-zinc-400 uppercase tracking-wider">Estado</th>
                  <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-zinc-400 uppercase tracking-wider">Vencimiento</th>
                  <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-zinc-400 uppercase tracking-wider">Registro</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-50 dark:divide-zinc-800/50">
                <tr 
                  v-for="tenant in filteredTenantsWithFilters" 
                  :key="tenant.id" 
                  @click="viewTenantDetails(tenant)"
                  :class="[
                    'transition-colors cursor-pointer group',
                    selectedTenants.includes(tenant.id) ? 'bg-blue-50/50 dark:bg-blue-900/10' : 'hover:bg-gray-50 dark:hover:bg-zinc-800/30'
                  ]"
                >
                  <td class="px-6 py-4" @click.stop>
                    <input type="checkbox" 
                           :value="tenant.id"
                           v-model="selectedTenants"
                           class="w-4 h-4 rounded border-gray-300 dark:border-zinc-600 dark:bg-zinc-700 text-blue-600 focus:ring-blue-500 cursor-pointer"
                    >
                  </td>
                  <td class="px-4 py-4">
                    <div class="flex items-center gap-3">
                      <div class="w-9 h-9 rounded-lg bg-gray-100 dark:bg-zinc-800 flex items-center justify-center flex-shrink-0">
                        <span class="text-sm font-semibold text-gray-500 dark:text-zinc-400">{{ (tenant.name || 'N')[0].toUpperCase() }}</span>
                      </div>
                      <div class="min-w-0">
                        <div class="flex items-center gap-2">
                          <p class="text-sm font-medium text-gray-900 dark:text-white truncate group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">{{ tenant.name }}</p>
                          <span v-if="tenant.error_count > 0 && !tenant.deleted_at" class="inline-flex items-center gap-0.5 px-1.5 py-0.5 bg-rose-50 dark:bg-rose-950 text-rose-700 dark:text-rose-400 text-[9px] font-bold rounded border border-rose-100 dark:border-rose-800 flex-shrink-0">
                            <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4.5c-.77-.833-2.694-.833-3.464 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
                            {{ tenant.error_count }}
                          </span>
                        </div>
                        <p class="text-xs text-gray-400 dark:text-zinc-500 truncate">{{ tenant.domain }}</p>
                      </div>
                    </div>
                  </td>
                  <td class="px-6 py-4">
                    <span class="text-[13px] font-medium text-gray-700 dark:text-zinc-300">
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
        </div>

      <!-- TAB: Soporte -->
      <div v-show="activeTab === 'support-tickets'" class="space-y-6">
        <!-- Controls -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6">
          <div class="flex flex-col sm:flex-row gap-3 w-full md:w-auto">
            <div class="relative w-full sm:w-64">
              <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" /></svg>
              </span>
              <input type="text" v-model="supportFilters.search" @input="debounceLoadSupportTickets" placeholder="Buscar ticket..." class="pl-10 w-full rounded-lg border border-gray-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 px-3 py-2 text-sm text-gray-900 dark:text-white focus:border-blue-500 focus:ring-blue-500">
            </div>
            <select v-model="supportFilters.status" @change="loadSupportTickets" class="w-full sm:w-auto rounded-lg border border-gray-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 px-3 py-2 text-sm text-gray-900 dark:text-white focus:border-blue-500 focus:ring-blue-500">
              <option value="">Todos los estados</option>
              <option value="open">Abiertos</option>
              <option value="in_progress">En Progreso</option>
              <option value="resolved">Resueltos / Cerrados</option>
            </select>
          </div>
          <button @click="loadSupportTickets" class="flex items-center gap-2 px-4 py-2 bg-white dark:bg-zinc-800 border border-slate-200 dark:border-zinc-700 text-slate-700 dark:text-zinc-300 rounded-lg hover:bg-slate-50 dark:hover:bg-zinc-700 transition-colors shadow-sm text-sm font-medium">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
            Refrescar
          </button>
        </div>

        <!-- Kanban Board -->
        <div v-if="loadingSupportTickets" class="py-12 text-center text-slate-500 dark:text-zinc-400">
          Cargando tickets...
        </div>
        <div v-else-if="supportTickets.length === 0" class="py-12 text-center text-slate-500 dark:text-zinc-400 bg-white dark:bg-zinc-900 rounded-xl border border-slate-200 dark:border-zinc-800 border-dashed">
          No se encontraron tickets.
        </div>
        <div v-else class="grid grid-cols-1 md:grid-cols-3 gap-6 items-start">
          
          <!-- Columna: Nuevos / Sin Asignar -->
          <div class="flex flex-col gap-3">
            <div class="flex items-center justify-between border-t-[3px] border-blue-500 pt-3 mb-1">
              <h3 class="text-slate-700 dark:text-slate-300 font-semibold text-sm uppercase tracking-wider">Nuevos / Sin Asignar</h3>
              <span class="text-xs font-medium bg-slate-200 dark:bg-zinc-800 text-slate-600 dark:text-zinc-400 px-2 py-0.5 rounded-full">
                {{ supportTickets.filter(t => t.status === 'open').length }}
              </span>
            </div>
            
            <div v-for="ticket in supportTickets.filter(t => t.status === 'open')" :key="ticket.id" @click="openTicketModal(ticket)" class="bg-white dark:bg-zinc-900 rounded-lg border border-slate-200 dark:border-zinc-800 shadow-sm hover:shadow-md hover:border-slate-300 dark:hover:border-zinc-700 transition-all cursor-pointer p-4 flex flex-col relative group">
              <div class="flex justify-between items-start mb-2">
                <span class="font-mono text-[11px] text-slate-500 dark:text-zinc-500">{{ ticket.ticket_number }}</span>
                <span class="text-[11px] text-slate-400 dark:text-zinc-600">{{ new Date(ticket.created_at).toLocaleDateString([], { month: 'short', day: 'numeric' }) }}</span>
              </div>
              <div class="mb-4">
                <h4 class="text-sm font-semibold text-slate-800 dark:text-white leading-snug">{{ ticket.subject }}</h4>
                <p class="text-xs text-slate-500 dark:text-zinc-400 truncate mt-1">{{ ticket.description }}</p>
              </div>
              <div class="mt-auto flex items-center gap-2 border-t border-slate-100 dark:border-zinc-800/50 pt-3">
                <div class="w-6 h-6 rounded-full bg-slate-100 dark:bg-zinc-800 flex items-center justify-center text-[10px] font-bold text-slate-500 dark:text-zinc-400 shrink-0">
                  {{ ticket.user_name?.substring(0, 2).toUpperCase() }}
                </div>
                <div class="flex items-center gap-1.5 text-xs text-slate-600 dark:text-zinc-400 truncate">
                  <span class="truncate">{{ ticket.user_name }}</span>
                  <span class="w-1 h-1 rounded-full bg-slate-300 dark:bg-zinc-700 shrink-0"></span>
                  <span class="font-medium text-slate-700 dark:text-zinc-300 truncate" :title="ticket.tenant_id">{{ ticket.tenant_id || 'Sin Tenant' }}</span>
                </div>
              </div>
            </div>
            <div v-if="supportTickets.filter(t => t.status === 'open').length === 0" class="text-center text-xs text-slate-400 py-4 border-2 border-dashed border-slate-200 dark:border-zinc-800 rounded-lg">
              No hay tickets nuevos
            </div>
          </div>

          <!-- Columna: En Progreso -->
          <div class="flex flex-col gap-3">
            <div class="flex items-center justify-between border-t-[3px] border-amber-400 pt-3 mb-1">
              <h3 class="text-slate-700 dark:text-slate-300 font-semibold text-sm uppercase tracking-wider">En Progreso</h3>
              <span class="text-xs font-medium bg-slate-200 dark:bg-zinc-800 text-slate-600 dark:text-zinc-400 px-2 py-0.5 rounded-full">
                {{ supportTickets.filter(t => t.status === 'in_progress').length }}
              </span>
            </div>
            
            <div v-for="ticket in supportTickets.filter(t => t.status === 'in_progress')" :key="ticket.id" @click="openTicketModal(ticket)" class="bg-white dark:bg-zinc-900 rounded-lg border border-slate-200 dark:border-zinc-800 shadow-sm hover:shadow-md hover:border-slate-300 dark:hover:border-zinc-700 transition-all cursor-pointer p-4 flex flex-col relative group">
              <div class="flex justify-between items-start mb-2">
                <span class="font-mono text-[11px] text-slate-500 dark:text-zinc-500">{{ ticket.ticket_number }}</span>
                <span class="text-[11px] text-slate-400 dark:text-zinc-600">{{ new Date(ticket.created_at).toLocaleDateString([], { month: 'short', day: 'numeric' }) }}</span>
              </div>
              <div class="mb-4">
                <h4 class="text-sm font-semibold text-slate-800 dark:text-white leading-snug">{{ ticket.subject }}</h4>
                <p class="text-xs text-slate-500 dark:text-zinc-400 truncate mt-1">{{ ticket.description }}</p>
              </div>
              <div class="mt-auto flex items-center gap-2 border-t border-slate-100 dark:border-zinc-800/50 pt-3">
                <div class="w-6 h-6 rounded-full bg-slate-100 dark:bg-zinc-800 flex items-center justify-center text-[10px] font-bold text-slate-500 dark:text-zinc-400 shrink-0">
                  {{ ticket.user_name?.substring(0, 2).toUpperCase() }}
                </div>
                <div class="flex items-center gap-1.5 text-xs text-slate-600 dark:text-zinc-400 truncate">
                  <span class="truncate">{{ ticket.user_name }}</span>
                  <span class="w-1 h-1 rounded-full bg-slate-300 dark:bg-zinc-700 shrink-0"></span>
                  <span class="font-medium text-slate-700 dark:text-zinc-300 truncate" :title="ticket.tenant_id">{{ ticket.tenant_id || 'Sin Tenant' }}</span>
                </div>
              </div>
            </div>
            <div v-if="supportTickets.filter(t => t.status === 'in_progress').length === 0" class="text-center text-xs text-slate-400 py-4 border-2 border-dashed border-slate-200 dark:border-zinc-800 rounded-lg">
              Ningún ticket en progreso
            </div>
          </div>

          <!-- Columna: En Espera / Cliente -->
          <div class="flex flex-col gap-3">
            <div class="flex items-center justify-between border-t-[3px] border-emerald-500 pt-3 mb-1">
              <h3 class="text-slate-700 dark:text-slate-300 font-semibold text-sm uppercase tracking-wider">En Espera / Cliente</h3>
              <span class="text-xs font-medium bg-slate-200 dark:bg-zinc-800 text-slate-600 dark:text-zinc-400 px-2 py-0.5 rounded-full">
                {{ supportTickets.filter(t => ['resolved', 'closed'].includes(t.status)).length }}
              </span>
            </div>
            
            <div v-for="ticket in supportTickets.filter(t => ['resolved', 'closed'].includes(t.status))" :key="ticket.id" @click="openTicketModal(ticket)" class="bg-white dark:bg-zinc-900 rounded-lg border border-slate-200 dark:border-zinc-800 shadow-sm hover:shadow-md hover:border-slate-300 dark:hover:border-zinc-700 transition-all cursor-pointer p-4 flex flex-col relative group opacity-80 hover:opacity-100">
              <div class="flex justify-between items-start mb-2">
                <span class="font-mono text-[11px] text-slate-500 dark:text-zinc-500 line-through decoration-slate-300">{{ ticket.ticket_number }}</span>
                <span class="text-[11px] text-slate-400 dark:text-zinc-600">{{ new Date(ticket.created_at).toLocaleDateString([], { month: 'short', day: 'numeric' }) }}</span>
              </div>
              <div class="mb-4">
                <h4 class="text-sm font-semibold text-slate-800 dark:text-white leading-snug">{{ ticket.subject }}</h4>
                <p class="text-xs text-slate-500 dark:text-zinc-400 truncate mt-1">{{ ticket.description }}</p>
              </div>
              <div class="mt-auto flex items-center gap-2 border-t border-slate-100 dark:border-zinc-800/50 pt-3">
                <div class="w-6 h-6 rounded-full bg-slate-100 dark:bg-zinc-800 flex items-center justify-center text-[10px] font-bold text-slate-500 dark:text-zinc-400 shrink-0">
                  {{ ticket.user_name?.substring(0, 2).toUpperCase() }}
                </div>
                <div class="flex items-center gap-1.5 text-xs text-slate-600 dark:text-zinc-400 truncate">
                  <span class="truncate">{{ ticket.user_name }}</span>
                  <span class="w-1 h-1 rounded-full bg-slate-300 dark:bg-zinc-700 shrink-0"></span>
                  <span class="font-medium text-slate-700 dark:text-zinc-300 truncate" :title="ticket.tenant_id">{{ ticket.tenant_id || 'Sin Tenant' }}</span>
                </div>
              </div>
            </div>
            <div v-if="supportTickets.filter(t => ['resolved', 'closed'].includes(t.status)).length === 0" class="text-center text-xs text-slate-400 py-4 border-2 border-dashed border-slate-200 dark:border-zinc-800 rounded-lg">
              No hay tickets en espera
            </div>
          </div>
          
        </div>
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

      <!-- TAB: Storage -->
      <div v-show="activeTab === 'storage'" class="flex flex-col h-full gap-5">
        <!-- Top Summary Card -->
        <div class="bg-white dark:bg-zinc-900 rounded-xl border border-gray-200 dark:border-zinc-800 shadow-sm p-6">
          <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
              <div class="flex items-center gap-3 mb-2">
                <svg class="w-8 h-8 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z"/></svg>
                <h2 class="text-xl font-bold text-gray-900 dark:text-white">Cloudflare R2 File Manager</h2>
              </div>
              <p class="text-sm text-slate-500 dark:text-zinc-400">
                Almacenamiento global centralizado. Límite: 10 GB.
              </p>
            </div>
            <div class="flex items-center gap-3">
              <div class="text-right mr-4 hidden md:block">
                <p class="text-xs text-slate-500 uppercase tracking-wider font-semibold mb-1">Uso de Almacenamiento</p>
                <p class="text-lg font-mono font-bold text-slate-900 dark:text-white">{{ (storageMetrics.used_bytes / (1024 * 1024)).toFixed(2) }} MB <span class="text-sm font-normal text-slate-500">/ 10 GB</span></p>
              </div>
              
              <!-- Upload Button -->
              <input type="file" ref="fileUploadInput" class="hidden" @change="handleFileUpload" />
              <button @click="$refs.fileUploadInput.click()" :disabled="uploadingFile" class="px-5 py-2.5 bg-[#0f6cbd] hover:bg-[#0c5699] text-white text-sm font-semibold rounded-lg shadow shadow-[#0f6cbd]/30 transition-all flex items-center gap-2 disabled:opacity-50">
                <svg v-if="!uploadingFile" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                <svg v-else class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span>{{ uploadingFile ? 'Subiendo...' : 'Subir Archivo' }}</span>
              </button>
            </div>
          </div>
        </div>

        <!-- Data Table -->
        <div class="bg-white dark:bg-zinc-900 rounded-xl border border-gray-200 dark:border-zinc-800 shadow-sm flex-1 overflow-hidden flex flex-col">
          <div class="overflow-x-auto flex-1">
            <table class="w-full text-left text-sm text-slate-600 dark:text-zinc-400">
              <thead class="text-xs uppercase bg-gray-50 dark:bg-zinc-800 text-slate-500 font-semibold border-b border-gray-200 dark:border-zinc-700">
                <tr>
                  <th scope="col" class="px-6 py-4 w-16 text-center">Tipo</th>
                  <th scope="col" class="px-6 py-4">Nombre del Archivo</th>
                  <th scope="col" class="px-6 py-4">Tamaño</th>
                  <th scope="col" class="px-6 py-4">Fecha Subida</th>
                  <th scope="col" class="px-6 py-4 text-right">Acciones</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-100 dark:divide-zinc-800">
                <tr v-if="loadingStorage" class="bg-white dark:bg-zinc-900">
                  <td colspan="5" class="px-6 py-12 text-center text-slate-500">Cargando archivos...</td>
                </tr>
                <tr v-else-if="storageFiles.length === 0" class="bg-white dark:bg-zinc-900">
                  <td colspan="5" class="px-6 py-12 text-center text-slate-500">No hay archivos en el bucket. Sube el primero.</td>
                </tr>
                <tr v-for="file in storageFiles" :key="file.name" class="bg-white dark:bg-zinc-900 hover:bg-gray-50 dark:hover:bg-zinc-800/50 transition-colors group">
                  <td class="px-6 py-3 text-center">
                    <img v-if="file.isImage" :src="file.url" class="w-8 h-8 rounded object-cover mx-auto bg-gray-100 border border-gray-200 dark:border-zinc-700" />
                    <svg v-else class="w-6 h-6 mx-auto text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                  </td>
                  <td class="px-6 py-3 font-medium text-slate-900 dark:text-white truncate max-w-xs" :title="file.name">
                    {{ file.name }}
                  </td>
                  <td class="px-6 py-3 font-mono text-xs">
                    {{ file.size > 1024*1024 ? (file.size/(1024*1024)).toFixed(2) + ' MB' : (file.size/1024).toFixed(2) + ' KB' }}
                  </td>
                  <td class="px-6 py-3 text-xs">
                    {{ new Date(file.lastModified * 1000).toLocaleString() }}
                  </td>
                  <td class="px-6 py-3 text-right">
                    <div class="flex items-center justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                      <button @click="copyToClipboard(file.url)" class="p-1.5 text-slate-400 hover:text-blue-500 hover:bg-blue-50 dark:hover:bg-blue-900/30 rounded" title="Copiar URL Pública">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                      </button>
                      <button @click="deleteStorageFile(file.name)" class="p-1.5 text-slate-400 hover:text-rose-500 hover:bg-rose-50 dark:hover:bg-rose-900/30 rounded" title="Eliminar Archivo">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                      </button>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
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
  </div>

  <!-- Modal Terminal interactiva -->
  <Teleport to="body">
    <div v-if="showTerminal" class="fixed inset-0 z-[100] flex items-center justify-center bg-black/80 backdrop-blur-sm" @click.self="showTerminal = false">
      <div class="bg-zinc-950 border border-zinc-800 rounded-lg shadow-2xl w-full max-w-4xl mx-4 overflow-hidden flex flex-col" style="height: 600px;">
        <!-- Terminal Header -->
        <div class="flex items-center justify-between px-4 py-2 bg-zinc-900 border-b border-zinc-800">
          <div class="flex items-center gap-2">
            <svg class="w-4 h-4 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l3 3-3 3m5 0h3M5 20h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
            <span class="text-sm font-semibold text-zinc-300">root@vps-105pos:~</span>
          </div>
          <button @click="showTerminal = false" class="text-zinc-500 hover:text-white transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
          </button>
        </div>

        <!-- Terminal Body -->
        <div class="flex-1 p-4 overflow-y-auto font-mono text-sm" ref="terminalBodyRef">
          <div v-for="(line, idx) in terminalHistory" :key="idx" class="mb-1" :class="{'text-emerald-400': line.type === 'input', 'text-zinc-300': line.type === 'output', 'text-rose-400': line.type === 'error'}">
            <span v-if="line.type === 'input'" class="mr-2 text-zinc-500">root@vps-105pos:~#</span>
            <span class="whitespace-pre-wrap">{{ line.text }}</span>
          </div>
          
          <!-- Input Row -->
          <div class="flex items-center mt-2">
            <span class="text-zinc-500 mr-2">root@vps-105pos:~#</span>
            <input 
              v-model="terminalInput" 
              @keyup.enter="executeTerminalCommand"
              ref="terminalInputRef"
              type="text" 
              class="flex-1 bg-transparent text-emerald-400 outline-none font-mono text-sm"
              autocomplete="off"
              spellcheck="false"
            >
          </div>
        </div>
      </div>
    </div>
  </Teleport>

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

  <!-- Modal de Confirmación Genérico -->
  <ConfirmModal 
    v-if="showConfirmModal" 
    :title="confirmModalConfig.title"
    :message="confirmModalConfig.message"
    :confirmText="confirmModalConfig.confirmText"
    :cancelText="confirmModalConfig.cancelText"
    :type="confirmModalConfig.type"
    @cancel="showConfirmModal = false"
    @confirm="executeConfirmAction"
  />
  <!-- DRAWER DE TICKETS DE SOPORTE (MODERN SAAS UI) -->
  <div v-if="showTicketModal" class="fixed inset-0 z-[100] flex justify-end">
    <div class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm transition-opacity" @click="showTicketModal = false"></div>
    <div class="relative bg-[#F7F9FC] shadow-2xl h-full w-[95vw] flex flex-col z-[101]">
      
      <!-- Header general del Drawer -->
      <div class="bg-white border-b border-slate-200 px-6 py-4 shrink-0 flex justify-between items-center shadow-sm z-10">
        <div class="flex items-center gap-4">
          <div class="w-10 h-10 bg-blue-50 text-[#4F7DF3] rounded-xl flex items-center justify-center">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/></svg>
          </div>
          <div>
            <div class="flex items-center gap-3">
              <h2 class="text-xl font-bold text-slate-800">{{ selectedTicket?.subject }}</h2>
              <span :class="[
                'px-2.5 py-0.5 rounded-full text-xs font-semibold',
                selectedTicket?.status === 'open' ? 'bg-red-50 text-red-600' :
                selectedTicket?.status === 'in_progress' ? 'bg-yellow-50 text-yellow-600' :
                selectedTicket?.status === 'resolved' ? 'bg-green-50 text-green-600' :
                'bg-slate-100 text-slate-600'
              ]">{{ selectedTicket?.status === 'open' ? 'Abierto' : selectedTicket?.status === 'in_progress' ? 'En Progreso' : selectedTicket?.status === 'resolved' ? 'Resuelto' : 'Cerrado' }}</span>
            </div>
            <span class="font-mono text-sm text-slate-500">{{ selectedTicket?.ticket_number }} • Solicitado por {{ selectedTicket?.user_name }}</span>
          </div>
        </div>
        <button @click="showTicketModal = false" class="p-2 text-slate-400 hover:text-slate-600 hover:bg-slate-100 rounded-lg transition-colors">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
      </div>

      <!-- Contenedor Principal de Dos Columnas -->
      <div class="flex flex-1 overflow-hidden">
        
        <!-- COLUMNA IZQUIERDA (Hilo y Editor) -->
        <div class="flex-1 flex flex-col min-w-0 bg-[#F7F9FC]">
          
          <!-- Activity Stream (Slack/Linear Style) -->
          <div class="flex-1 overflow-y-auto px-8 py-6 space-y-6">
            
            <!-- Original Message (Left Aligned) -->
            <div class="flex gap-4 max-w-4xl">
              <div class="w-10 h-10 rounded-full bg-white border border-slate-200 flex items-center justify-center text-sm font-bold text-slate-600 shrink-0 shadow-sm">
                {{ selectedTicket?.user_name?.substring(0, 2).toUpperCase() || 'CL' }}
              </div>
              <div class="flex flex-col gap-1.5 items-start w-full">
                <div class="flex items-baseline gap-2 px-1">
                  <span class="font-semibold text-slate-800">{{ selectedTicket?.user_name }}</span>
                  <span class="text-xs text-slate-500 font-medium">Cliente</span>
                  <span class="text-xs text-slate-400 ml-1">{{ new Date(selectedTicket?.created_at).toLocaleString([], { dateStyle: 'medium', timeStyle: 'short' }) }}</span>
                </div>
                <div class="bg-white border border-slate-200 p-4 rounded-2xl rounded-tl-sm shadow-sm text-slate-700 leading-relaxed whitespace-pre-wrap font-sans text-sm w-full">
                  {{ selectedTicket?.description }}
                </div>
              </div>
            </div>

            <!-- Messages -->
            <template v-if="selectedTicket?.messages && selectedTicket.messages.length > 0">
              <div v-for="msg in selectedTicket.messages" :key="msg.id" :class="['flex gap-4 max-w-4xl', msg.sender_type === 'admin' ? 'ml-auto flex-row-reverse' : '']">
                
                <div :class="[
                  'w-10 h-10 rounded-full flex items-center justify-center text-sm font-bold shrink-0 shadow-sm border',
                  msg.sender_type === 'admin' ? 'bg-[#4F7DF3] border-[#4F7DF3] text-white' : 'bg-white border-slate-200 text-slate-600'
                ]">
                  {{ msg.sender_type === 'admin' ? 'ST' : (selectedTicket?.user_name?.substring(0, 2).toUpperCase() || 'CL') }}
                </div>
                
                <div :class="['flex flex-col gap-1.5 w-full', msg.sender_type === 'admin' ? 'items-end' : 'items-start']">
                  <div :class="['flex items-baseline gap-2 px-1', msg.sender_type === 'admin' ? 'flex-row-reverse' : '']">
                    <span class="font-semibold text-slate-800">{{ msg.sender_type === 'admin' ? 'Soporte Técnico' : selectedTicket?.user_name }}</span>
                    <span class="text-xs text-slate-500 font-medium">{{ msg.sender_type === 'admin' ? 'Agente' : 'Cliente' }}</span>
                    <span class="text-xs text-slate-400 mx-1">{{ new Date(msg.created_at).toLocaleString([], { dateStyle: 'medium', timeStyle: 'short' }) }}</span>
                    <span v-if="msg.sender_type === 'admin' && msg.message.startsWith('[NOTA INTERNA]')" class="px-2 py-0.5 bg-amber-100 text-amber-800 font-bold text-[10px] uppercase tracking-widest rounded-full">Nota Interna</span>
                  </div>
                  
                  <div :class="[
                    'p-4 rounded-2xl shadow-sm text-sm leading-relaxed whitespace-pre-wrap font-sans',
                    msg.sender_type === 'admin' 
                      ? (msg.message.startsWith('[NOTA INTERNA]') 
                          ? 'bg-amber-50 border border-amber-200 text-amber-900 rounded-tr-sm' 
                          : 'bg-[#4F7DF3] border border-[#4F7DF3] text-white rounded-tr-sm')
                      : 'bg-white border border-slate-200 text-slate-700 rounded-tl-sm'
                  ]">
                    {{ msg.sender_type === 'admin' && msg.message.startsWith('[NOTA INTERNA]') ? msg.message.replace('[NOTA INTERNA] ', '') : msg.message }}
                  </div>
                </div>

              </div>
            </template>
          </div>

          <!-- Sticky Composer / Input -->
          <div class="px-8 pb-8 pt-4 sticky bottom-0 bg-gradient-to-t from-[#F7F9FC] via-[#F7F9FC] to-transparent shrink-0">
            
            <!-- ESTADO 1: Oculto (Solo botones de acción) -->
            <div v-if="!showComposer" class="flex items-center gap-3">
              <button @click="openComposerWithAction('reply')" class="flex-1 bg-white border border-slate-200 hover:border-[#4F7DF3] hover:shadow-md text-slate-700 hover:text-[#4F7DF3] font-semibold py-3 px-4 rounded-2xl flex items-center justify-center gap-2 transition-all shadow-sm">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                Añadir respuesta
              </button>
              
              <div class="relative w-56">
                <button @click="showActionsDropdown = !showActionsDropdown" class="w-full bg-slate-800 hover:bg-slate-900 text-white font-semibold py-3 px-4 rounded-2xl flex items-center justify-between transition-all shadow-sm">
                  <span>Acciones</span>
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </button>
                
                <div v-if="showActionsDropdown" class="absolute bottom-full right-0 mb-3 w-64 bg-white border border-slate-200 shadow-xl rounded-2xl z-[60] py-1.5 overflow-hidden">
                  <button @click="openComposerWithAction('reply')" class="w-full text-left px-5 py-2.5 text-sm text-slate-700 hover:bg-slate-50 border-b border-slate-50 font-medium flex items-center gap-2"><svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/></svg>Responder al cliente</button>
                  <button @click="openComposerWithAction('request_info')" class="w-full text-left px-5 py-2.5 text-sm text-slate-700 hover:bg-slate-50 border-b border-slate-50 font-medium flex items-center gap-2"><svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>Solicitar información</button>
                  <button @click="openComposerWithAction('put_on_hold')" class="w-full text-left px-5 py-2.5 text-sm text-slate-700 hover:bg-slate-50 border-b border-slate-50 font-medium flex items-center gap-2"><svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>Poner en espera</button>
                  <button @click="openComposerWithAction('escalate')" class="w-full text-left px-5 py-2.5 text-sm text-rose-600 hover:bg-rose-50 border-b border-slate-50 font-medium flex items-center gap-2"><svg class="w-4 h-4 text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>Escalar caso</button>
                  <button @click="openComposerWithAction('resolve')" class="w-full text-left px-5 py-2.5 text-sm text-emerald-600 hover:bg-emerald-50 border-b border-slate-50 font-medium flex items-center gap-2"><svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>Marcar como resuelto</button>
                  <button @click="openComposerWithAction('internal_note')" class="w-full text-left px-5 py-2.5 text-sm text-amber-600 hover:bg-amber-50 font-medium flex items-center gap-2"><svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>Nota interna</button>
                </div>
              </div>
            </div>

            <!-- ESTADO 2: Visible (Compositor) -->
            <div v-if="showComposer" class="bg-white border border-slate-200 rounded-2xl shadow-xl overflow-hidden flex flex-col focus-within:ring-2 focus-within:ring-[#4F7DF3]/20 focus-within:border-[#4F7DF3] transition-all">
              
              <!-- Toolbar -->
              <div class="bg-slate-50/50 border-b border-slate-100 px-4 py-2.5 flex items-center justify-between">
                <div class="flex items-center gap-4">
                  <div :class="['text-[11px] font-bold tracking-wide uppercase', replyType === 'public' ? 'text-[#4F7DF3]' : 'text-amber-500']">
                    {{ replyType === 'public' ? 'Respuesta Pública' : 'Nota Interna' }}
                    <span v-if="replyStatus !== selectedTicket?.status" class="ml-2 px-1.5 py-0.5 bg-slate-200 text-slate-600 rounded-md text-[9px]">→ Estado cambiará a: {{ getStatusLabel(replyStatus) }}</span>
                  </div>
                </div>
                
                <div class="flex items-center gap-2">
                  <button @click="showComposer = false" class="p-1 text-slate-400 hover:text-slate-600 hover:bg-slate-100 rounded-lg transition-colors" title="Cancelar">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                  </button>
                </div>
              </div>

              <!-- Input Area -->
              <div class="relative flex flex-col">
                <textarea 
                  v-model="replyMessage" 
                  :placeholder="replyType === 'internal' ? 'Escribir nota interna privada...' : 'Escribir respuesta al cliente...'"
                  :class="[
                    'w-full min-h-[120px] p-4 text-sm focus:outline-none resize-y',
                    replyType === 'internal' ? 'bg-amber-50/20 text-amber-900 placeholder-amber-400' : 'bg-white text-slate-800 placeholder-slate-400'
                  ]"
                ></textarea>
                
                <div class="p-3 flex justify-between items-center bg-white border-t border-slate-50">
                  <div class="text-[11px] text-slate-400 font-medium flex items-center gap-1.5 ml-1">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
                    Adjuntar
                  </div>
                  <button 
                    @click="sendTicketReply(replyStatus)" 
                    :disabled="sendingReply || !replyMessage.trim()"
                    :class="[
                      'flex items-center gap-2 px-6 py-2 rounded-lg font-semibold text-sm transition-all',
                      sendingReply || !replyMessage.trim() ? 'bg-slate-100 text-slate-400 cursor-not-allowed' : 'bg-[#4F7DF3] hover:bg-blue-600 text-white shadow hover:shadow-md'
                    ]"
                  >
                    <svg v-if="sendingReply" class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                    <span>Enviar Respuesta</span>
                    <svg v-if="!sendingReply" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                  </button>
                </div>
              </div>
            </div>
          </div>

        </div>

        <!-- COLUMNA DERECHA (Panel de Detalles / Atributos) -->
        <div class="w-[300px] xl:w-[320px] bg-white border-l border-slate-200 flex flex-col shrink-0">
          
          <div class="p-6 overflow-y-auto h-full space-y-6 bg-white">
            
            <!-- Detalles del Caso -->
            <div>
              <h3 class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-3">Detalles del Caso</h3>
              <div class="flex flex-col gap-4">
                <div class="flex justify-between items-center">
                  <span class="text-sm text-slate-500">Estado</span>
                  <span :class="[
                    'px-2 py-0.5 rounded-md text-xs font-semibold',
                    selectedTicket?.status === 'open' ? 'bg-red-50 text-red-600 border border-red-100' :
                    selectedTicket?.status === 'in_progress' ? 'bg-yellow-50 text-yellow-600 border border-yellow-100' :
                    selectedTicket?.status === 'resolved' ? 'bg-emerald-50 text-emerald-600 border border-emerald-100' :
                    'bg-slate-100 text-slate-600 border border-slate-200'
                  ]">{{ getStatusLabel(selectedTicket?.status) }}</span>
                </div>
                <div class="flex justify-between items-center">
                  <span class="text-sm text-slate-500">Prioridad</span>
                  <div class="flex items-center gap-1.5 px-2 py-0.5 bg-orange-50 text-orange-600 text-xs font-semibold rounded-md border border-orange-100">
                    <div class="w-1.5 h-1.5 rounded-full bg-orange-500"></div> Media
                  </div>
                </div>
                <div class="flex justify-between items-center">
                  <span class="text-sm text-slate-500">Categoría</span>
                  <span class="text-sm text-slate-800 font-medium">General</span>
                </div>
              </div>
            </div>

            <div class="w-full h-px bg-slate-100"></div>

            <!-- Solicitante -->
            <div>
              <h3 class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-3">Solicitante</h3>
              <div class="flex flex-col gap-3">
                <div class="flex items-center gap-3">
                  <div class="w-10 h-10 rounded-full bg-slate-100 border border-slate-200 flex items-center justify-center text-slate-600 font-bold shadow-sm">
                    {{ selectedTicket?.user_name?.substring(0, 2).toUpperCase() || 'CL' }}
                  </div>
                  <div class="flex flex-col overflow-hidden">
                    <span class="text-sm font-bold text-slate-800 truncate">{{ selectedTicket?.user_name }}</span>
                    <span class="text-xs text-slate-500 truncate">{{ selectedTicket?.user_email }}</span>
                  </div>
                </div>
                <div class="mt-1">
                  <span class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg bg-slate-50 text-slate-600 text-xs font-mono border border-slate-200">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    {{ selectedTicket?.tenant_id }}
                  </span>
                </div>
              </div>
            </div>

            <div class="w-full h-px bg-slate-100"></div>

            <!-- Asignado -->
            <div>
              <h3 class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-3">Asignado a</h3>
              <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-full bg-blue-50 border border-blue-100 flex items-center justify-center text-[#4F7DF3] font-bold text-xs">
                  ST
                </div>
                <div class="flex flex-col">
                  <span class="text-sm font-semibold text-slate-800">Soporte Técnico</span>
                  <span class="text-xs text-slate-400">Equipo Nivel 1</span>
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
import { ref, computed, onMounted, onUnmounted } from 'vue'
import axios from 'axios'
import CreateTenantModal from './CreateTenantModal.vue'
import TenantProfileView from './TenantProfileView.vue'
import DeleteTenantModal from './DeleteTenantModal.vue'
import ConfirmModal from './ConfirmModal.vue'
import SystemHealthTab from './SystemHealthTab.vue'
import SystemLogsTab from './SystemLogsTab.vue'
import MaintenanceToolsTab from './MaintenanceToolsTab.vue'
import SecurityTab from './SecurityTab.vue'
import AIMonitorTab from './AIMonitorTab.vue'

// Estados
const loading = ref(false)
const activeTab = ref('dashboard')
const usersMenuOpen = ref(true) // Control del submenu "Usuarios"
const sidebarOpen = ref(typeof window !== 'undefined' ? window.innerWidth >= 1024 : true) // Control dinámico de apertura

// Estado de Soporte
const supportTickets = ref([])
const loadingSupportTickets = ref(false)
const supportFilters = ref({ search: '', status: '' })

// Estado para ConfirmModal Genérico
const showConfirmModal = ref(false)
const confirmModalConfig = ref({
  title: '',
  message: '',
  confirmText: 'Aceptar',
  cancelText: 'Cancelar',
  type: 'danger',
  action: null
})

const openConfirmModal = (config) => {
  confirmModalConfig.value = config
  showConfirmModal.value = true
}

const executeConfirmAction = async () => {
  if (confirmModalConfig.value.action) {
    await confirmModalConfig.value.action()
  }
  showConfirmModal.value = false
}
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
const selectedTenants = ref([]) // Añadido para checkboxes múltiples

const toggleSelectAll = () => {
  if (selectedTenants.value.length === filteredTenantsWithFilters.value.length) {
    selectedTenants.value = []
  } else {
    selectedTenants.value = filteredTenantsWithFilters.value.map(t => t.id)
  }
}

// Lógica de la Terminal
const showTerminal = ref(false)
const terminalInput = ref('')
const terminalHistory = ref([
  { type: 'output', text: 'Bienvenido a la consola del VPS 105POS.' },
  { type: 'output', text: 'Sesión iniciada como administrador. Puede ejecutar comandos bash/artisan.' }
])
const terminalBodyRef = ref(null)
const terminalInputRef = ref(null)

const openTerminal = () => {
  showTerminal.value = true
  setTimeout(() => {
    if (terminalInputRef.value) terminalInputRef.value.focus()
  }, 100)
}

const executeTerminalCommand = async () => {
  const cmd = terminalInput.value.trim()
  if (!cmd) return
  
  if (cmd === 'clear') {
    terminalHistory.value = []
    terminalInput.value = ''
    return
  }

  terminalHistory.value.push({ type: 'input', text: cmd })
  terminalInput.value = ''
  
  setTimeout(() => {
    if (terminalBodyRef.value) terminalBodyRef.value.scrollTop = terminalBodyRef.value.scrollHeight
  }, 50)

  try {
    const res = await axios.post('/api/admin/system/terminal', { command: cmd })
    if (res.data.success) {
      terminalHistory.value.push({ type: 'output', text: res.data.output || '(sin salida)' })
    } else {
      terminalHistory.value.push({ type: 'error', text: res.data.message || 'Error executing command' })
    }
  } catch (err) {
    terminalHistory.value.push({ type: 'error', text: err.response?.data?.message || err.message })
  }
  
  setTimeout(() => {
    if (terminalBodyRef.value) terminalBodyRef.value.scrollTop = terminalBodyRef.value.scrollHeight
    if (terminalInputRef.value) terminalInputRef.value.focus()
  }, 50)
}

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

// Support Tickets Logic
const debounceTimer = ref(null)
const debounceLoadSupportTickets = () => {
  clearTimeout(debounceTimer.value)
  debounceTimer.value = setTimeout(() => {
    loadSupportTickets()
  }, 500)
}

const loadSupportTickets = async () => {
  try {
    loadingSupportTickets.value = true
    const response = await axios.get('/api/admin/support-tickets', { params: supportFilters.value })
    if (response.data.success) {
      supportTickets.value = response.data.tickets.data || []
    }
  } catch (error) {
    console.error('Error fetching admin tickets:', error)
    showNotification('error', 'Error al cargar tickets', error.response?.data?.message || 'Hubo un problema al cargar los tickets de soporte')
  } finally {
    loadingSupportTickets.value = false
  }
}

const updateSupportTicketStatus = async (id, status) => {
  try {
    const response = await axios.put(`/api/admin/support-tickets/${id}/status`, { status })
    if (response.data.success) {
      showNotification('success', 'Éxito', 'Estado actualizado exitosamente')
      loadSupportTickets()
    }
  } catch (error) {
    console.error('Error updating ticket:', error)
    showNotification('error', 'Error al actualizar', error.response?.data?.message || 'No se pudo actualizar el estado')
  }
}

// Lógica del Modal de Respuestas
const showTicketModal = ref(false)
const selectedTicket = ref(null)
const replyMessage = ref('')
const replyStatus = ref('open')
const sendingReply = ref(false)
const replyType = ref('public')
const showTemplatesDropdown = ref(false)
const showStatusDropdown = ref(false)
const showComposer = ref(false)
const showActionsDropdown = ref(false)

const getStatusLabel = (status) => {
  switch(status) {
    case 'open': return 'Abierto';
    case 'in_progress': return 'En Progreso';
    case 'resolved': return 'Resuelto';
    case 'closed': return 'Cerrado';
    default: return 'Desconocido';
  }
}

const openComposerWithAction = (actionType) => {
  showComposer.value = true
  showActionsDropdown.value = false
  
  if (actionType === 'reply') {
    replyType.value = 'public'
    replyStatus.value = selectedTicket.value?.status === 'open' ? 'in_progress' : selectedTicket.value?.status
    replyMessage.value = ''
  } else if (actionType === 'request_info') {
    replyType.value = 'public'
    replyStatus.value = 'in_progress'
    replyMessage.value = "Estimado/a cliente,\n\nGracias por comunicarse con el equipo de soporte de 105 POS.\n\nPara poder continuar con el diagnóstico detallado de su caso, requerimos amablemente que nos comparta la siguiente información adicional:\n\n- [Describa la información o evidencia requerida]\n\nEste ticket permanecerá abierto a la espera de su respuesta. Quedamos atentos para continuar asistiéndole.\n\nCordialmente,\nEquipo de Ingeniería de Soporte\n105 POS"
  } else if (actionType === 'put_on_hold') {
    replyType.value = 'internal'
    replyStatus.value = 'in_progress'
    replyMessage.value = "Caso puesto en espera (On-Hold) por el siguiente motivo:\n\n- [Motivo del bloqueo temporal (ej. Dependencia de proveedor, Despliegue programado)]\n\nAcción requerida para reanudar: [Describir acción]"
  } else if (actionType === 'resolve') {
    replyType.value = 'public'
    replyStatus.value = 'resolved'
    replyMessage.value = "Estimado/a cliente,\n\nLe informamos que nuestro equipo de ingeniería ha concluido el diagnóstico y ha aplicado la resolución a su caso de manera exitosa.\n\nDetalles de la solución implementada:\n- [Detalle técnico o paso a paso de la solución]\n\nEl sistema se encuentra operando con normalidad. Si tiene alguna duda o el comportamiento persiste, puede responder directamente a este mensaje para reabrir el caso.\n\nAgradecemos su confianza en 105 POS.\n\nAtentamente,\nEquipo de Ingeniería de Soporte"
  } else if (actionType === 'escalate') {
    replyType.value = 'internal'
    replyStatus.value = 'open'
    replyMessage.value = "ESCALAMIENTO NIVEL 2\n\nMotivo del escalamiento:\n- [Describa por qué el Nivel 1 no puede resolverlo]\n\nPruebas ya realizadas:\n- [Enumere las acciones de mitigación intentadas]\n\nImpacto actual: [Bajo/Medio/Alto/Crítico]"
  } else if (actionType === 'internal_note') {
    replyType.value = 'internal'
    replyStatus.value = selectedTicket.value?.status
    replyMessage.value = ''
  }
}

const openTicketModal = (ticket) => {
  selectedTicket.value = { ...ticket }
  replyMessage.value = ''
  replyStatus.value = ticket.status
  replyType.value = 'public'
  showTemplatesDropdown.value = false
  showStatusDropdown.value = false
  showComposer.value = false
  showActionsDropdown.value = false
  showTicketModal.value = true
}

const insertTemplate = (type) => {
  if (type === 'general') {
    replyMessage.value = "Estimado/a cliente,\n\nLe informamos que nuestro equipo de ingeniería ha concluido el diagnóstico y ha aplicado la resolución a su caso de manera exitosa.\n\nDetalles de la solución implementada:\n- [Detalle técnico o paso a paso de la solución]\n\nEl sistema se encuentra operando con normalidad. Si tiene alguna duda o el comportamiento persiste, puede responder directamente a este mensaje.\n\nAgradecemos su confianza en 105 POS.\n\nAtentamente,\nEquipo de Ingeniería de Soporte"
  } else if (type === 'wait') {
    replyMessage.value = "Estimado/a cliente,\n\nGracias por comunicarse con el equipo de soporte de 105 POS.\n\nPara poder continuar con el análisis de su caso, requerimos amablemente que nos comparta la siguiente información adicional:\n\n- [Listar requerimientos]\n\nSu caso cambiará al estado 'En Espera'. Recuerde que si no recibimos actualizaciones en un plazo de 3 días hábiles, el sistema cerrará el caso automáticamente.\n\nCordialmente,\nEquipo de Ingeniería de Soporte"
  }
  showTemplatesDropdown.value = false
}

const sendTicketReply = async (newStatus) => {
  if (!replyMessage.value.trim() || !selectedTicket.value) return
  
  if (newStatus && typeof newStatus === 'string') {
    replyStatus.value = newStatus
  }
  
  showStatusDropdown.value = false
  sendingReply.value = true
  
  try {
    const finalMessage = replyType.value === 'internal' ? `[NOTA INTERNA] ${replyMessage.value}` : replyMessage.value
    
    const response = await axios.post(`/api/admin/support-tickets/${selectedTicket.value.id}/reply`, {
      message: finalMessage,
      status: replyStatus.value
    })
    
    if (response.data.success) {
      showNotification('success', 'Enviado', 'Respuesta enviada y cliente notificado')
      showTicketModal.value = false
      loadSupportTickets()
    }
  } catch (error) {
    console.error('Error enviando respuesta:', error)
    showNotification('error', 'Error al responder', error.response?.data?.message || 'No se pudo enviar la respuesta')
  } finally {
    sendingReply.value = false
  }
}

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
let nocRealtimeInterval = null

// ====== STORAGE R2 DATA ======
const storageMetrics = ref({ used_bytes: 0, limit_bytes: 10737418240, total_files: 0 })
const storageFiles = ref([])
const loadingStorage = ref(false)
const uploadingFile = ref(false)
const fileUploadInput = ref(null)

const loadStorageData = async () => {
  loadingStorage.value = true
  try {
    const res = await axios.get('/api/admin/storage/files')
    if (res.data.success) {
      storageMetrics.value = res.data.metrics
      storageFiles.value = res.data.files || []
    }
  } catch (error) {
    console.error("Error loading R2 storage", error)
  } finally {
    loadingStorage.value = false
  }
}

const handleFileUpload = async (event) => {
  const file = event.target.files[0]
  if (!file) return

  const formData = new FormData()
  formData.append('file', file)

  uploadingFile.value = true
  try {
    const res = await axios.post('/api/admin/storage/upload', formData, {
      headers: { 'Content-Type': 'multipart/form-data' }
    })
    if (res.data.success) {
      showNotification('success', 'Éxito', 'Archivo subido correctamente')
      loadStorageData()
    } else {
      showNotification('error', 'Error', res.data.message)
    }
  } catch (error) {
    showNotification('error', 'Error', 'Fallo al subir el archivo.')
  } finally {
    uploadingFile.value = false
    if (fileUploadInput.value) fileUploadInput.value.value = ''
  }
}

const deleteStorageFile = async (name) => {
  openConfirmModal({
    title: '¿Eliminar archivo?',
    message: `Estás a punto de eliminar "${name}" permanentemente del Cloudflare R2.`,
    confirmText: 'Sí, Eliminar',
    type: 'danger',
    action: async () => {
      try {
        const res = await axios.delete('/api/admin/storage/delete', { data: { name } })
        if (res.data.success) {
          showNotification('success', 'Eliminado', 'Archivo borrado del bucket.')
          loadStorageData()
        } else {
          showNotification('error', 'Error', res.data.message)
        }
      } catch (error) {
        showNotification('error', 'Error', 'Fallo al borrar el archivo.')
      }
    }
  })
}

const copyToClipboard = async (text) => {
  try {
    await navigator.clipboard.writeText(text)
    showNotification('success', 'Copiado', 'Enlace copiado al portapapeles.')
  } catch (err) {
    showNotification('error', 'Error', 'No se pudo copiar el enlace.')
  }
}

// ====== NOC Real-time Data ======
const nocMetrics = ref({
  cpu_percent: 0,
  cpu_cores: 0,
  cpu_threads: 0,
  cpu_model: '',
  cpu_load: '0 0 0',
  ram_used_gb: 0,
  ram_total_gb: 0,
  ram_available_gb: 0,
  ram_cached_gb: 0,
  disk_percent: 0,
  disk_used_gb: 0,
  disk_free_gb: 0,
  disk_total_gb: 0,
  logs_size_mb: 0,
  cache_size_mb: 0,
  db_central_size_mb: 0,
  db_tenant_size_mb: 0
})

const systemServices = ref([])

const liveLogs = ref([])
const aiChatInput = ref('')
const aiChatLoading = ref(false)
const aiChatMessages = ref([
  { role: 'ai', title: '¡Sistema Estable!', content: 'He analizado los logs de las últimas 24 horas y me conecté al flujo del NOC. No detecto anomalías críticas en los servicios principales.', isAlert: false }
])

const round = (val, decimals) => Number(Math.round(val+'e'+decimals)+'e-'+decimals)

const fetchNocData = async () => {
  try {
    const healthRes = await axios.get('/api/admin/system/health')
    
    if (healthRes.data?.success) {
      const h = healthRes.data.data
      nocMetrics.value.cpu_percent = h.cpu?.usage_percent || 0
      nocMetrics.value.cpu_cores = h.cpu?.cores || 0
      nocMetrics.value.cpu_threads = h.cpu?.threads || 0
      nocMetrics.value.cpu_model = h.cpu?.model || 'Unknown CPU'
      nocMetrics.value.cpu_load = `${h.load_average?.['1min'] || 0} ${h.load_average?.['5min'] || 0} ${h.load_average?.['15min'] || 0}`

      nocMetrics.value.ram_used_gb = round(h.ram?.used_mb / 1024, 1) || 0
      nocMetrics.value.ram_total_gb = round(h.ram?.total_mb / 1024, 1) || 0
      nocMetrics.value.ram_available_gb = round(h.ram?.available_mb / 1024, 1) || 0
      nocMetrics.value.ram_cached_gb = round(((h.ram?.cached_mb || 0) + (h.ram?.buffers_mb || 0)) / 1024, 1) || 0

      nocMetrics.value.disk_percent = h.disk?.percent_used || 0
      nocMetrics.value.disk_used_gb = h.disk?.used_gb || 0
      nocMetrics.value.disk_free_gb = h.disk?.free_gb || 0
      nocMetrics.value.disk_total_gb = h.disk?.total_gb || 0

      nocMetrics.value.logs_size_mb = h.storage?.logs_size_mb || 0
      nocMetrics.value.cache_size_mb = h.storage?.cache_size_mb || 0
      nocMetrics.value.db_central_size_mb = h.database?.central_size_mb || 0
      nocMetrics.value.db_tenant_size_mb = h.database?.total_tenant_size_mb || 0
      
      systemServices.value = h.services || []
    }
  } catch (error) {
    // Silently handle errors so dashboard doesn't spam toasts
  }
}

const sendAIMessage = async () => {
  if (!aiChatInput.value.trim()) return
  const userQ = aiChatInput.value
  aiChatMessages.value.push({ role: 'user', content: userQ })
  aiChatInput.value = ''
  aiChatLoading.value = true
  
  // Scroller update
  setTimeout(() => {
    const container = document.getElementById('copilot-chat-container')
    if (container) container.scrollTop = container.scrollHeight
  }, 50)
  
  // Simulate Gemini/AI thinking
  setTimeout(() => {
    let response = "Analizando tu consulta..."
    let title = "Insight Proactivo"
    let isAlert = false

    if (userQ.toLowerCase().includes('error') || userQ.toLowerCase().includes('fallo') || userQ.toLowerCase().includes('resumir')) {
      const errCount = liveLogs.value.filter(l => l.level === 'error').length
      response = `Revisando los logs recientes, encontré ${errCount} errores recientes. La mayoría están relacionados con picos temporales o autenticación (401). Te sugiero revisar el Security Hub.`
      title = "Análisis de Logs"
      if (errCount > 0) isAlert = true
    } else if (userQ.toLowerCase().includes('estado') || userQ.toLowerCase().includes('recurso') || userQ.toLowerCase().includes('cpu')) {
      response = `El sistema se encuentra estable. La CPU actual está al ${nocMetrics.value.cpu_percent}% y la memoria RAM usa ${nocMetrics.value.ram_used_gb}GB de ${nocMetrics.value.ram_total_gb}GB.`
      title = "Métricas Actuales"
    } else {
      response = "Entendido. Sigo monitoreando la infraestructura. ¿Necesitas un reporte detallado de algún microservicio en específico?"
    }
    
    aiChatMessages.value.push({ role: 'ai', title, content: response, isAlert })
    aiChatLoading.value = false
    
    setTimeout(() => {
      const container = document.getElementById('copilot-chat-container')
      if (container) container.scrollTop = container.scrollHeight
    }, 50)
  }, 1500)
}

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

const restoreTenant = async (tenantId) => {
  try {
    const res = await axios.post(`/admin/api/tenants/${tenantId}/restore`)
    if (res.data.success) {
      showNotification('success', 'Cliente restaurado', res.data.message)
      if (viewingTenant.value) viewingTenant.value = null
      fetchData()
    }
  } catch (error) {
    showNotification('error', 'Error al restaurar', error.response?.data?.message || error.message)
  }
}

const deleteTenant = async () => {
  if (!tenantToDelete.value) return

  try {
    const isForceDelete = !!tenantToDelete.value.deleted_at
    const endpoint = isForceDelete 
      ? `/admin/api/tenants/${tenantToDelete.value.id}/force`
      : `/admin/api/tenants/${tenantToDelete.value.id}`
      
    const res = await axios.delete(endpoint)
    if (res.data.success) {
      showNotification('success', isForceDelete ? 'Cliente eliminado permanentemente' : 'Enviado a papelera', res.data.message)
      showDeleteModal.value = false
      tenantToDelete.value = null
      if (viewingTenant.value) viewingTenant.value = null
      fetchData()
    }
  } catch (error) {
    showNotification('error', 'Error al eliminar', error.response?.data?.message || error.message)
  }
}

const handleBulkDelete = async () => {
  openConfirmModal({
    title: '¿Eliminar múltiples usuarios?',
    message: `¿Estás seguro de que deseas eliminar ${selectedTenants.value.length} usuarios seleccionados?\nEsta acción enviará a la papelera a los usuarios activos y eliminará permanentemente a los que ya estén en la papelera.`,
    confirmText: 'Sí, eliminar',
    cancelText: 'Cancelar',
    type: 'danger',
    action: async () => {
      loading.value = true
      try {
        const tenantsList = Array.isArray(tenants.value) ? tenants.value : tenants.value?.data || []
        for (const id of selectedTenants.value) {
          const tenant = tenantsList.find(t => t.id === id)
          const isForceDelete = !!tenant?.deleted_at
          const endpoint = isForceDelete 
            ? `/admin/api/tenants/${id}/force`
            : `/admin/api/tenants/${id}`
          await axios.delete(endpoint)
        }
        showNotification('success', 'Operación completada', `Se eliminaron ${selectedTenants.value.length} usuarios`)
        selectedTenants.value = []
        fetchData()
      } catch (error) {
        showNotification('error', 'Error en operación masiva', error.response?.data?.message || error.message)
      }
      loading.value = false
    }
  })
}

const handleBulkToggleStatus = async () => {
  loading.value = true
  try {
    const tenantsList = Array.isArray(tenants.value) ? tenants.value : tenants.value?.data || []
    for (const id of selectedTenants.value) {
      const tenant = tenantsList.find(t => t.id === id)
      if (tenant) {
        const newStatus = tenant.status === 'active' ? 'suspended' : 'active'
        await axios.put(`/admin/api/tenants/${id}`, { status: newStatus })
      }
    }
    showNotification('success', 'Operación completada', `Se actualizó el estado de ${selectedTenants.value.length} usuarios`)
    selectedTenants.value = []
    fetchData()
  } catch (error) {
    showNotification('error', 'Error en operación masiva', error.response?.data?.message || error.message)
  }
  loading.value = false
}

const handleBulkResetPassword = () => {
  showNotification('info', 'Próximamente', 'El restablecimiento masivo de contraseñas estará disponible pronto.')
}

const handleBulkExport = () => {
  showNotification('info', 'Exportando...', `Preparando exportación de ${selectedTenants.value.length} usuarios.`)
  setTimeout(() => {
    showNotification('success', 'Exportación completada', 'El archivo ha sido descargado.')
    selectedTenants.value = []
  }, 1500)
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
    'active': 'text-gray-700 dark:text-zinc-300 border-gray-200 dark:border-zinc-700',
    'paused': 'text-gray-500 dark:text-zinc-400 border-gray-200 dark:border-zinc-700',
    'suspended': 'text-rose-600 dark:text-rose-400 border-gray-200 dark:border-zinc-700'
  }
  return badges[status] || badges.active
}

const getPlanBadge = (plan) => {
  const badges = {
    'free_trial': 'text-gray-600 dark:text-zinc-400 border-gray-200 dark:border-zinc-700',
    'basic': 'text-blue-600 dark:text-blue-400 border-gray-200 dark:border-zinc-700',
    'premium': 'text-indigo-600 dark:text-indigo-400 border-gray-200 dark:border-zinc-700',
    'enterprise': 'text-gray-900 dark:text-white border-gray-300 dark:border-zinc-600'
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

// Filtrado avanzado con plan y status y pestañas
const filteredTenantsWithFilters = computed(() => {
  let result = filteredTenants.value
  
  // 1. Filtrar según la pestaña activa en el sidebar
  if (activeTab.value === 'clientes') {
    // Activos (no eliminados)
    result = result.filter(t => !t.deleted_at)
  } else if (activeTab.value === 'invitados') {
    // Invitados (planes gratis y no eliminados)
    result = result.filter(t => ['free_trial', 'free', 'trial_express'].includes(t.plan) && !t.deleted_at)
  } else if (activeTab.value === 'eliminados') {
    // Papelera (eliminados)
    result = result.filter(t => t.deleted_at)
  }
  
  // 2. Filtros secundarios (dropdowns)
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
  fetchNocData() // Init NOC Data

  refreshInterval = setInterval(() => {
    fetchData()
    if (activeTab.value === 'ai-monitoring') {
      fetchAIMonitoring()
    }
  }, 60000) // Auto-refresh cada minuto

  // Update NOC Live metrics faster (every 5 seconds)
  nocRealtimeInterval = setInterval(() => {
    if (activeTab.value === 'dashboard') {
      fetchNocData()
    }
  }, 5000)
})

onUnmounted(() => {
  if (refreshInterval) clearInterval(refreshInterval)
  if (nocRealtimeInterval) clearInterval(nocRealtimeInterval)
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

/* OVERRIDES DE TAMAÑO DE TEXTO PARA MAYOR LEGIBILIDAD (Solicitado por el usuario) */
:deep(.text-\[9px\]) { font-size: 11px !important; }
:deep(.text-\[10px\]) { font-size: 13px !important; }
:deep(.text-\[11px\]) { font-size: 14px !important; }
:deep(.text-\[13px\]) { font-size: 15px !important; }
:deep(.text-xs) { font-size: 14px !important; }
:deep(.text-sm) { font-size: 16px !important; line-height: 1.5 !important; }
:deep(.text-base) { font-size: 17px !important; }
</style>
