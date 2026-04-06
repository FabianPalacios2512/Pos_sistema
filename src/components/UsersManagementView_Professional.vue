<template>
  <!-- Terminal de Acceso Corporativa - Flat Enterprise -->
  <div class="min-h-screen font-sans bg-gray-50 dark:bg-[#131314] transition-colors duration-300">
    <div class="px-4 lg:px-6 py-4 space-y-4 pb-6 animate-fade-in">
      
      <!-- Header -->
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-xl font-bold text-gray-900 dark:text-white tracking-tight">Centro de Control de Personal</h1>
          <p class="text-xs text-gray-500 dark:text-zinc-500 mt-0.5">Rendimiento operativo · Auditoría en tiempo real</p>
        </div>
        
        <div class="flex items-center gap-3">
          <!-- Indicador de Límite de Usuarios -->
          <div v-if="maxUsersAllowed !== null" 
               class="hidden md:flex items-center gap-2 px-3 py-1.5 text-xs font-medium"
               :class="canCreateMoreUsers 
                 ? 'text-emerald-700 dark:text-emerald-400 border border-gray-200 dark:border-zinc-700 bg-white dark:bg-zinc-800' 
                 : 'text-amber-700 dark:text-amber-400 border border-gray-200 dark:border-zinc-700 bg-white dark:bg-zinc-800'">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
            </svg>
            <span>{{ currentUsersCount }}/{{ maxUsersAllowed }} usuarios</span>
          </div>
          
          <!-- Badge Plan Enterprise -->
          <div v-if="maxUsersAllowed === null" 
               class="hidden md:flex items-center gap-2 px-3 py-1.5 text-xs font-medium bg-white dark:bg-zinc-800 text-gray-600 dark:text-zinc-400 border border-gray-200 dark:border-zinc-700">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
            </svg>
            <span>Usuarios ilimitados</span>
          </div>
          
          <!-- Botón Refrescar -->
          <button @click="refreshData"
                  :disabled="loading"
                  class="p-2 bg-white dark:bg-zinc-800 hover:bg-gray-50 dark:hover:bg-zinc-700 text-gray-500 dark:text-zinc-400 border border-gray-200 dark:border-zinc-700 transition-colors duration-150"
                  :class="{ 'opacity-50 cursor-not-allowed': loading }">
            <svg class="w-4 h-4" :class="{ 'animate-spin': loading }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
            </svg>
          </button>
          
          <!-- Botón Principal -->
          <button @click="openCreateUserModal()"
                  :disabled="!canCreateMoreUsers"
                  class="px-4 py-2 bg-gray-900 dark:bg-white hover:bg-gray-800 dark:hover:bg-gray-100 disabled:opacity-50 disabled:cursor-not-allowed text-white dark:text-gray-900 text-sm font-semibold transition-colors duration-150 flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path>
            </svg>
            <span>Nuevo Usuario</span>
          </button>
        </div>
      </div>

      <!-- ============================================================ -->
      <!-- BARRA DE FILTROS TEMPORALES (Time Travel) -->
      <!-- ============================================================ -->
      <div class="bg-white dark:bg-zinc-900 border border-gray-200 dark:border-zinc-800 py-2 px-4 flex items-center gap-3 flex-wrap">
        <!-- Quick presets: Hoy / Ayer -->
        <div class="flex items-center gap-1">
          <button v-for="preset in datePresets" :key="preset.key"
            @click="selectDatePreset(preset.key)"
            class="px-3 py-1.5 text-xs font-semibold border transition-colors duration-150 uppercase tracking-wide"
            :class="activeDatePreset === preset.key
              ? 'bg-gray-900 dark:bg-zinc-200 text-white dark:text-gray-900 border-gray-900 dark:border-zinc-200'
              : 'bg-white dark:bg-zinc-800 text-gray-600 dark:text-zinc-400 border-gray-200 dark:border-zinc-700 hover:bg-gray-50 dark:hover:bg-zinc-700'">
            {{ preset.label }}
          </button>
        </div>

        <!-- Separator -->
        <div class="h-6 w-px bg-gray-200 dark:bg-zinc-700 hidden sm:block"></div>

        <!-- Single date picker (visible when 'custom' is active) -->
        <div v-if="activeDatePreset === 'custom'" class="flex items-center gap-2">
          <label class="text-xs font-medium text-gray-500 dark:text-zinc-400">Fecha</label>
          <input type="date" v-model="filterDate"
            @change="onCustomDateChange"
            class="px-2.5 py-1.5 text-xs border border-gray-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-gray-700 dark:text-zinc-200 focus:ring-1 focus:ring-gray-400 focus:border-gray-400">
        </div>

        <!-- Active filter indicator -->
        <div v-if="activeDatePreset !== 'today'" class="ml-auto flex items-center gap-2">
          <span class="text-[10px] font-medium text-gray-400 dark:text-zinc-500">
            Mostrando: {{ filterDateLabel }}
          </span>
          <button @click="selectDatePreset('today')" class="p-1 rounded hover:bg-red-50 dark:hover:bg-red-900/20 text-gray-400 hover:text-red-500 dark:hover:text-red-400 transition-colors" title="Volver a Hoy">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
          </button>
        </div>
      </div>

      <!-- ============================================================ -->
      <!-- KPIs — Metrics Ribbon (Vercel/Linear) -->
      <!-- ============================================================ -->
      <div class="bg-white dark:bg-zinc-900 border border-gray-200 dark:border-zinc-800 grid grid-cols-1 md:grid-cols-3 divide-x divide-gray-200 dark:divide-zinc-800">
        
        <!-- KPI 1: Personal Activo Ahora -->
        <div class="flex flex-col gap-0.5 px-5 py-3">
          <div class="flex items-center justify-between">
            <p class="text-[11px] text-gray-500 dark:text-zinc-500 uppercase tracking-wider font-medium">Personal Activo Ahora</p>
            <span class="flex h-2.5 w-2.5">
              <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-emerald-500"></span>
            </span>
          </div>
          <p class="text-2xl font-bold text-gray-900 dark:text-white tabular-nums">{{ dashboardKpis.active_now_count }}</p>
          <p class="text-[11px] text-emerald-600 dark:text-emerald-400">en línea</p>
        </div>

        <!-- KPI 2: Ventas del Equipo (Hoy) -->
        <div class="flex flex-col gap-0.5 px-5 py-3">
          <div class="flex items-center justify-between">
            <p class="text-[11px] text-gray-500 dark:text-zinc-500 uppercase tracking-wider font-medium">Ventas del Equipo (Hoy)</p>
            <svg class="w-4 h-4 text-emerald-500 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
          </div>
          <p class="text-2xl font-bold text-gray-900 dark:text-white tabular-nums">{{ formatCurrency(dashboardKpis.sales_today) }}</p>
          <p class="text-[11px] text-gray-500 dark:text-zinc-500">{{ dashboardKpis.sales_count_today }} ventas</p>
        </div>

        <!-- KPI 3: Alertas Operativas (Clickable) -->
        <div @click="alertasTotal > 0 && (showAlertDetails = !showAlertDetails)"
          :class="[
            'flex flex-col gap-0.5 px-5 py-3 transition-colors duration-150',
            alertasTotal > 0
              ? 'cursor-pointer hover:bg-gray-50 dark:hover:bg-zinc-800/50'
              : ''
          ]">
          <div class="flex items-center justify-between">
            <p class="text-[11px] text-gray-500 dark:text-zinc-500 uppercase tracking-wider font-medium">Alertas Operativas</p>
            <div class="flex items-center gap-1.5">
              <svg :class="alertasTotal > 0 ? 'text-amber-500 dark:text-amber-400' : 'text-emerald-500 dark:text-emerald-400'" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                <path v-if="alertasTotal > 0" stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                <path v-else stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
              </svg>
              <svg v-if="alertasTotal > 0" class="w-3.5 h-3.5 text-amber-500 dark:text-amber-400 transition-transform duration-200" :class="showAlertDetails ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
              </svg>
            </div>
          </div>
          <p class="text-2xl font-semibold tabular-nums" :class="alertasTotal > 0 ? 'text-amber-700 dark:text-amber-400' : 'text-gray-900 dark:text-white'">{{ alertasTotal }}</p>
          <div class="flex items-center gap-2">
            <span v-if="dashboardKpis.discrepancies_today > 0" class="px-1.5 py-0.5 text-[10px] font-semibold bg-rose-50 dark:bg-rose-950 text-rose-700 dark:text-rose-400 border border-rose-200 dark:border-rose-800">
              {{ dashboardKpis.discrepancies_today }} descuadre{{ dashboardKpis.discrepancies_today > 1 ? 's' : '' }}
            </span>
            <span v-if="dashboardKpis.returns_today_count > 0" class="px-1.5 py-0.5 text-[10px] font-semibold bg-amber-50 dark:bg-amber-950 text-amber-700 dark:text-amber-400 border border-amber-200 dark:border-amber-800">
              {{ dashboardKpis.returns_today_count }} devolución{{ dashboardKpis.returns_today_count > 1 ? 'es' : '' }}
            </span>
            <span v-if="alertasTotal === 0" class="text-xs text-emerald-500 dark:text-emerald-400">Todo en orden</span>
          </div>
        </div>
      </div>

      <!-- ============================================================ -->
      <!-- PANEL DE DETALLE DE ALERTAS (Expandible) -->
      <!-- ============================================================ -->
      <Transition enter-active-class="transition-all duration-300 ease-out" enter-from-class="opacity-0 -translate-y-2 max-h-0" enter-to-class="opacity-100 translate-y-0 max-h-[600px]" leave-active-class="transition-all duration-200 ease-in" leave-from-class="opacity-100 translate-y-0 max-h-[600px]" leave-to-class="opacity-0 -translate-y-2 max-h-0">
        <div v-if="showAlertDetails && alertasTotal > 0" class="bg-white dark:bg-zinc-900 border border-amber-200 dark:border-amber-800/40 overflow-hidden">
          <div class="px-5 py-3 border-b border-amber-100 dark:border-amber-900/30 bg-amber-50/40 dark:bg-amber-950/10">
            <div class="flex items-center justify-between">
              <div class="flex items-center gap-2">
                <svg class="w-4 h-4 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4.5c-.77-.833-2.694-.833-3.464 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                </svg>
                <h3 class="text-sm font-bold text-gray-900 dark:text-white">Detalle de Alertas de Hoy</h3>
              </div>
              <button @click="showAlertDetails = false" class="p-1 hover:bg-amber-100 dark:hover:bg-amber-900/30 text-gray-400 dark:text-zinc-500 hover:text-gray-600 dark:hover:text-zinc-300 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
              </button>
            </div>
          </div>
          <div class="p-5 space-y-4">
            <!-- Devoluciones -->
            <div v-if="dashboardKpis.returns_today_details?.length > 0">
              <h4 class="text-xs font-bold text-gray-500 dark:text-zinc-500 uppercase tracking-wider mb-3 flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/></svg>
                Devoluciones ({{ dashboardKpis.returns_today_details.length }})
              </h4>
              <div class="space-y-2">
                <div v-for="ret in dashboardKpis.returns_today_details" :key="ret.id" class="flex items-center justify-between p-3 bg-amber-50/50 dark:bg-amber-950/10 border border-amber-100 dark:border-amber-900/30">
                  <div class="flex items-center gap-3">
                    <div class="w-7 h-7 bg-amber-100 dark:bg-amber-900/40 flex items-center justify-center">
                      <svg class="w-4 h-4 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/></svg>
                    </div>
                    <div>
                      <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ ret.number }}</p>
                      <p class="text-[11px] text-gray-500 dark:text-zinc-400">
                        {{ ret.user_name }}
                        <span v-if="ret.invoice_number" class="text-gray-400 dark:text-zinc-500"> &middot; Factura {{ ret.invoice_number }}</span>
                      </p>
                      <p v-if="ret.reason" class="text-[11px] text-amber-600 dark:text-amber-400 mt-0.5">Motivo: {{ ret.reason }}</p>
                    </div>
                  </div>
                  <div class="text-right">
                    <p class="text-sm font-bold text-amber-700 dark:text-amber-300">{{ formatCurrency(ret.total) }}</p>
                    <p class="text-[10px] text-gray-400 dark:text-zinc-500">{{ formatAlertTime(ret.created_at) }}</p>
                  </div>
                </div>
              </div>
            </div>

            <!-- Descuadres -->
            <div v-if="dashboardKpis.discrepancies_today_details?.length > 0">
              <h4 class="text-xs font-bold text-gray-500 dark:text-zinc-500 uppercase tracking-wider mb-3 flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Descuadres de Caja ({{ dashboardKpis.discrepancies_today_details.length }})
              </h4>
              <div class="space-y-2">
                <div v-for="(disc, idx) in dashboardKpis.discrepancies_today_details" :key="idx" class="flex items-center justify-between p-3 bg-rose-50/50 dark:bg-rose-950/10 border border-rose-100 dark:border-rose-900/30">
                  <div class="flex items-center gap-3">
                    <div class="w-7 h-7 bg-rose-100 dark:bg-rose-900/40 flex items-center justify-center">
                      <svg class="w-4 h-4 text-rose-600 dark:text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <div>
                      <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ disc.user_name }}</p>
                      <p class="text-[11px] text-gray-500 dark:text-zinc-400">
                        {{ disc.warehouse }}
                        <span class="text-gray-400 dark:text-zinc-500"> &middot; Apertura: {{ formatCurrency(disc.opening_amount) }} → Cierre: {{ formatCurrency(disc.closing_amount) }}</span>
                      </p>
                    </div>
                  </div>
                  <div class="text-right">
                    <p class="text-sm font-bold text-rose-700 dark:text-rose-300">{{ disc.difference > 0 ? '+' : '' }}{{ formatCurrency(disc.difference) }}</p>
                    <p class="text-[10px] text-gray-400 dark:text-zinc-500">{{ formatAlertTime(disc.closed_at) }}</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </Transition>

      <!-- ============================================================ -->
      <!-- DATA GRID DE ALTO RENDIMIENTO - Flat Enterprise -->
      <!-- ============================================================ -->
      <div class="bg-white dark:bg-zinc-900 border border-gray-200 dark:border-zinc-800 overflow-hidden">
        <!-- Grid Header -->
        <div class="px-4 py-2.5 border-b border-gray-200 dark:border-zinc-800 flex items-center justify-between">
          <div>
            <h3 class="text-sm font-bold text-gray-900 dark:text-white uppercase tracking-wide">Panel de Rendimiento</h3>
            <p class="text-[11px] text-gray-500 dark:text-zinc-500 mt-0.5">Actividad y ventas del equipo en tiempo real</p>
          </div>
          <div class="flex items-center gap-2">
            <span class="flex items-center gap-1.5 text-[11px] font-medium text-emerald-600 dark:text-emerald-400">
              <span class="inline-flex h-2 w-2 rounded-full bg-emerald-500"></span>
              En vivo
            </span>
          </div>
        </div>

        <!-- Table with solid dark header -->
        <div class="overflow-x-auto">
          <table class="w-full">
            <thead>
              <tr class="bg-gray-100 dark:bg-zinc-800 border-b border-gray-200 dark:border-zinc-700">
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-600 dark:text-zinc-400 uppercase tracking-wider">Empleado</th>
                <th class="px-5 py-3 text-center text-xs font-semibold text-gray-600 dark:text-zinc-400 uppercase tracking-wider">Inicio Jornada</th>
                <th class="px-5 py-3 text-center text-xs font-semibold text-gray-600 dark:text-zinc-400 uppercase tracking-wider">Fin Jornada</th>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-600 dark:text-zinc-400 uppercase tracking-wider">Sede Asignada</th>
                <th class="px-5 py-3 text-right text-xs font-semibold text-gray-600 dark:text-zinc-400 uppercase tracking-wider">Ventas Hoy</th>
                <th class="px-5 py-3 text-center text-xs font-semibold text-gray-600 dark:text-zinc-400 uppercase tracking-wider">Auditoría de Turno</th>
                <th class="px-5 py-3 text-right text-xs font-semibold text-gray-600 dark:text-zinc-400 uppercase tracking-wider">Descuadre</th>
                <th class="px-5 py-3 text-right text-xs font-semibold text-gray-600 dark:text-zinc-400 uppercase tracking-wider">Acciones</th>
              </tr>
            </thead>
            <tbody class="bg-white dark:bg-zinc-900 divide-y divide-gray-100 dark:divide-zinc-800">
              <tr 
                v-for="emp in employeeGridData" 
                :key="emp.id"
                class="hover:bg-gray-50 dark:hover:bg-zinc-800/50 transition-colors duration-100"
              >
                <!-- EMPLEADO: Avatar + Nombre + Rol -->
                <td class="px-5 py-3 whitespace-nowrap">
                  <div class="flex items-center gap-3">
                    <div class="relative">
                      <div class="w-9 h-9 rounded-lg flex items-center justify-center text-sm font-bold flex-shrink-0"
                           :class="emp.active
                             ? 'bg-gray-100 dark:bg-zinc-800 text-gray-700 dark:text-zinc-300 border border-gray-200 dark:border-zinc-700'
                             : 'bg-gray-50 dark:bg-zinc-800 text-gray-400 dark:text-zinc-600 border border-gray-200 dark:border-zinc-700'">
                        {{ getInitials(emp.name) }}
                      </div>
                      <span v-if="emp.isOnline" class="absolute -bottom-0.5 -right-0.5 block h-2.5 w-2.5 rounded-full bg-emerald-500 ring-2 ring-white dark:ring-zinc-900"></span>
                    </div>
                    <div>
                      <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ emp.name }}</p>
                      <p class="text-xs text-gray-500 dark:text-zinc-500">{{ emp.roleName }}</p>
                    </div>
                  </div>
                </td>

                <!-- INICIO DE JORNADA -->
                <td class="px-5 py-3 whitespace-nowrap text-center">
                  <div v-if="emp.entryTime">
                    <p class="text-sm font-semibold text-gray-800 dark:text-zinc-200 tabular-nums">{{ emp.entryTime }}</p>
                  </div>
                  <span v-else class="text-xs text-gray-400 dark:text-zinc-600">—</span>
                </td>

                <!-- FIN DE JORNADA -->
                <td class="px-5 py-3 whitespace-nowrap text-center">
                  <div v-if="emp.exitTime">
                    <p class="text-sm font-semibold text-gray-800 dark:text-zinc-200 tabular-nums">{{ emp.exitTime }}</p>
                    <span v-if="emp.exitIsAutoClose"
                      class="inline-flex items-center px-1.5 py-0.5 mt-0.5 text-[9px] font-bold uppercase bg-amber-50 dark:bg-amber-950 text-amber-700 dark:text-amber-400 border border-amber-200 dark:border-amber-800 rounded">
                      Auto-cierre
                    </span>
                  </div>
                  <span v-else class="text-xs text-gray-400 dark:text-zinc-600">—</span>
                </td>

                <!-- SEDE ASIGNADA -->
                <td class="px-5 py-3 whitespace-nowrap">
                  <span class="px-2.5 py-1 text-xs font-semibold uppercase tracking-wide bg-gray-50 dark:bg-zinc-800 text-gray-700 dark:text-zinc-300 border border-gray-200 dark:border-zinc-700 rounded">
                    {{ emp.sedeActual }}
                  </span>
                </td>

                <!-- VENTAS HOY -->
                <td class="px-5 py-3 whitespace-nowrap text-right">
                  <p class="text-sm font-bold tabular-nums" :class="emp.ventasHoy > 0 ? 'text-gray-900 dark:text-white' : 'text-gray-300 dark:text-zinc-600'">
                    {{ formatCurrency(emp.ventasHoy) }}
                  </p>
                </td>

                <!-- AUDITORÍA DE TURNO -->
                <td class="px-5 py-3 whitespace-nowrap text-center">
                  <span v-if="emp.cajaForzada"
                    class="inline-flex items-center gap-1 px-2.5 py-1 text-[11px] font-semibold uppercase tracking-wide bg-orange-50 dark:bg-orange-950 text-orange-700 dark:text-orange-400 border border-orange-200 dark:border-orange-800 rounded">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126z"/></svg>
                    Pendiente Arqueo
                  </span>
                  <span v-else-if="emp.cajaAbierta"
                    class="inline-flex items-center gap-1 px-2.5 py-1 text-[11px] font-semibold uppercase tracking-wide bg-emerald-50 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800 rounded">
                    Turno Activo
                  </span>
                  <span v-else
                    class="inline-flex items-center px-2.5 py-1 text-[11px] font-semibold uppercase tracking-wide bg-gray-50 dark:bg-zinc-800 text-gray-500 dark:text-zinc-500 border border-gray-200 dark:border-zinc-700 rounded">
                    Caja Cerrada
                  </span>
                </td>

                <!-- DESCUADRE -->
                <td class="px-5 py-3 whitespace-nowrap text-right">
                  <template v-if="emp.cajaForzada">
                    <p class="text-xs text-gray-400 dark:text-zinc-600 italic">Esperando arqueo</p>
                  </template>
                  <template v-else-if="emp.cashDiscrepancy !== null">
                    <p class="text-sm font-bold tabular-nums" 
                       :class="emp.cashDiscrepancy < 0 
                         ? 'text-rose-600 dark:text-rose-400' 
                         : emp.cashDiscrepancy > 0 
                           ? 'text-amber-600 dark:text-amber-400' 
                           : 'text-emerald-600 dark:text-emerald-400'">
                      {{ emp.cashDiscrepancy > 0 ? '+' : '' }}{{ formatCurrency(emp.cashDiscrepancy) }}
                    </p>
                  </template>
                  <span v-else class="text-xs text-gray-400 dark:text-zinc-600">$0</span>
                </td>

                <!-- ACCIONES -->
                <td class="px-5 py-3 whitespace-nowrap text-right">
                  <div class="flex items-center justify-end gap-1.5">
                    <button
                      @click="openAttendanceModal(emp)"
                      class="p-1.5 border border-transparent text-gray-400 dark:text-zinc-500 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/20 hover:border-blue-200 dark:hover:border-blue-800 transition-colors duration-150"
                      title="Gestionar asistencia"
                    >
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                      </svg>
                    </button>
                    <button
                      @click="openAuditPanel(emp)"
                      class="px-3 py-1.5 text-[11px] font-semibold border transition-colors duration-150 bg-white dark:bg-zinc-800 text-gray-700 dark:text-zinc-300 border-gray-200 dark:border-zinc-700 hover:bg-gray-50 dark:hover:bg-zinc-700"
                    >
                      Auditar Perfil
                    </button>
                    <button
                      @click="openEditUserModal(findUserById(emp.id))"
                      class="p-1.5 border border-transparent text-gray-400 dark:text-zinc-500 hover:text-amber-600 dark:hover:text-amber-400 hover:bg-amber-50 dark:hover:bg-amber-900/20 hover:border-amber-200 dark:hover:border-amber-800 transition-colors duration-150"
                      title="Editar usuario"
                    >
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                      </svg>
                    </button>
                  </div>
                </td>
              </tr>

              <!-- Empty State -->
              <tr v-if="employeeGridData.length === 0">
                <td colspan="9" class="px-6 py-12 text-center">
                  <div class="flex flex-col items-center justify-center">
                    <svg class="w-12 h-12 text-gray-300 dark:text-zinc-600 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                    <p class="text-sm font-medium text-gray-500 dark:text-zinc-400">No hay empleados registrados</p>
                    <p class="text-[11px] text-gray-400 dark:text-zinc-500 mt-1">Crea tu primer usuario para comenzar</p>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Footer -->
        <div class="px-4 py-2 border-t border-gray-200 dark:border-zinc-800 bg-gray-50 dark:bg-zinc-900/80 flex items-center justify-between">
          <p class="text-[10px] text-gray-400 dark:text-zinc-600 tracking-wide">© 2026 105 POS Pro. Todos los derechos reservados. | Sistema Auditado de Alto Rendimiento.</p>
          <p class="text-[10px] text-gray-300 dark:text-zinc-700 font-mono tracking-widest">105 POS PRO</p>
        </div>
      </div>

    </div>

    <!-- ============================================================ -->
    <!-- SLIDE-OVER: Perfil Ejecutivo del Empleado -->
    <!-- ============================================================ -->
    <Teleport to="body">
      <Transition
        enter-active-class="transition-opacity duration-300 ease-out"
        enter-from-class="opacity-0"
        enter-to-class="opacity-100"
        leave-active-class="transition-opacity duration-200 ease-in"
        leave-from-class="opacity-100"
        leave-to-class="opacity-0"
      >
        <div v-if="auditPanelOpen" class="fixed inset-0 z-50">
          <!-- Backdrop -->
          <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" @click="closeAuditPanel"></div>
          
          <!-- Panel -->
          <Transition
            enter-active-class="transition-transform duration-300 ease-out"
            enter-from-class="translate-x-full"
            enter-to-class="translate-x-0"
            leave-active-class="transition-transform duration-200 ease-in"
            leave-from-class="translate-x-0"
            leave-to-class="translate-x-full"
          >
            <div v-if="auditPanelOpen" class="absolute right-0 top-0 h-full w-full max-w-2xl bg-white dark:bg-zinc-900 shadow-xl dark:shadow-black/60 border-l border-gray-200 dark:border-zinc-800 flex flex-col">
              
              <!-- Panel Header: Identidad del Empleado -->
              <div class="px-6 py-5 border-b border-gray-200 dark:border-zinc-800 bg-gray-50 dark:bg-zinc-900">
                <div class="flex items-center justify-between">
                  <div class="flex items-center gap-4">
                    <!-- Avatar grande -->
                    <div class="w-14 h-14 flex items-center justify-center bg-slate-100 dark:bg-zinc-800 text-slate-700 dark:text-zinc-300 font-bold text-lg border border-slate-200 dark:border-zinc-700 relative">
                      {{ auditTarget ? getInitials(auditTarget.name) : '?' }}
                      <span v-if="auditTarget?.isOnline" class="absolute -bottom-1 -right-1 block h-4 w-4 rounded-full bg-emerald-500 ring-2 ring-white dark:ring-zinc-900"></span>
                    </div>
                    <div>
                      <h2 class="text-lg font-bold text-gray-900 dark:text-white">{{ auditTarget?.name || 'Empleado' }}</h2>
                      <div class="flex items-center gap-2 mt-1">
                        <span class="px-2 py-0.5 text-xs font-medium border bg-purple-50 dark:bg-purple-950 text-purple-700 dark:text-purple-400 border-purple-100 dark:border-purple-800">
                          {{ auditTarget?.roleName || 'Sin rol' }}
                        </span>
                        <span class="px-2 py-0.5 text-xs font-medium border bg-blue-50 dark:bg-blue-950 text-blue-700 dark:text-blue-400 border-blue-100 dark:border-blue-800">
                          {{ auditTarget?.sedeActual || 'Sin sede' }}
                        </span>
                        <span 
                          :class="auditTarget?.cajaAbierta
                            ? 'bg-emerald-50 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-400 border-emerald-100 dark:border-emerald-800'
                            : 'bg-gray-100 dark:bg-zinc-800 text-gray-500 dark:text-zinc-500 border-gray-200 dark:border-zinc-700'"
                          class="px-2 py-0.5 text-xs font-medium border">
                          Caja {{ auditTarget?.cajaAbierta ? 'Abierta' : 'Cerrada' }}
                        </span>
                      </div>
                    </div>
                  </div>
                  <button 
                    @click="closeAuditPanel"
                    class="p-2 border border-transparent text-slate-400 dark:text-zinc-400 hover:text-rose-600 dark:hover:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-900/20 hover:border-rose-100 dark:hover:border-rose-900/30 transition-colors duration-150"
                  >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                  </button>
                </div>
              </div>

              <!-- Panel Body -->
              <div class="flex-1 overflow-y-auto p-6">
                <!-- Loading -->
                <div v-if="auditLoading" class="flex items-center justify-center py-16">
                  <svg class="animate-spin h-8 w-8 text-blue-500" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                  </svg>
                </div>

                <div v-else-if="auditTarget" class="space-y-6">

                  <!-- ===== MÉTRICAS DEL MES ===== -->
                  <div v-if="auditProfile?.performance">
                    <h3 class="text-xs font-bold text-gray-500 dark:text-zinc-500 uppercase tracking-wider mb-3">Rendimiento del Mes</h3>
                    <div class="grid grid-cols-2 gap-3">
                      <!-- Ventas del Mes -->
                      <div class="bg-gray-50 dark:bg-zinc-800/50 p-4 border border-gray-200 dark:border-zinc-700/50">
                        <div class="flex items-center justify-between mb-1">
                          <p class="text-[10px] font-bold text-gray-400 dark:text-zinc-500 uppercase tracking-wider">Total Vendido</p>
                          <svg class="w-4 h-4 text-emerald-500 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                          </svg>
                        </div>
                        <p class="text-xl font-bold text-gray-900 dark:text-white">{{ formatCurrency(auditProfile.performance.total_sold_month) }}</p>
                        <p class="text-[10px] text-gray-400 dark:text-zinc-500 mt-0.5">{{ auditProfile.performance.invoices_count }} facturas emitidas</p>
                      </div>
                      <!-- Ticket Promedio -->
                      <div class="bg-gray-50 dark:bg-zinc-800/50 p-4 border border-gray-200 dark:border-zinc-700/50">
                        <div class="flex items-center justify-between mb-1">
                          <p class="text-[10px] font-bold text-gray-400 dark:text-zinc-500 uppercase tracking-wider">Ticket Promedio</p>
                          <svg class="w-4 h-4 text-blue-500 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                          </svg>
                        </div>
                        <p class="text-xl font-bold text-gray-900 dark:text-white">{{ formatCurrency(auditProfile.performance.avg_ticket) }}</p>
                        <p class="text-[10px] text-gray-400 dark:text-zinc-500 mt-0.5">por transacción</p>
                      </div>
                      <!-- Devoluciones -->
                      <div class="bg-gray-50 dark:bg-zinc-800/50 p-4 border border-gray-200 dark:border-zinc-700/50" :class="auditProfile.performance.returns_count > 0 ? 'ring-1 ring-amber-200 dark:ring-amber-800/50' : ''">
                        <div class="flex items-center justify-between mb-1">
                          <p class="text-[10px] font-bold text-gray-400 dark:text-zinc-500 uppercase tracking-wider">Devoluciones</p>
                          <svg class="w-4 h-4" :class="auditProfile.performance.returns_count > 0 ? 'text-amber-500 dark:text-amber-400' : 'text-gray-300 dark:text-zinc-600'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 15v-1a4 4 0 00-4-4H8m0 0l3 3m-3-3l3-3"/>
                          </svg>
                        </div>
                        <p class="text-xl font-bold" :class="auditProfile.performance.returns_count > 0 ? 'text-amber-600 dark:text-amber-400' : 'text-gray-900 dark:text-white'">
                          {{ auditProfile.performance.returns_count }}
                        </p>
                        <p class="text-[10px] text-gray-400 dark:text-zinc-500 mt-0.5">{{ formatCurrency(auditProfile.performance.returns_amount) }} en valor</p>
                      </div>
                      <!-- Descuadres -->
                      <div class="bg-gray-50 dark:bg-zinc-800/50 p-4 border border-gray-200 dark:border-zinc-700/50" :class="auditProfile.performance.discrepancies > 0 ? 'ring-1 ring-rose-200 dark:ring-rose-800/50' : ''">
                        <div class="flex items-center justify-between mb-1">
                          <p class="text-[10px] font-bold text-gray-400 dark:text-zinc-500 uppercase tracking-wider">Descuadres</p>
                          <svg class="w-4 h-4" :class="auditProfile.performance.discrepancies > 0 ? 'text-rose-500 dark:text-rose-400' : 'text-gray-300 dark:text-zinc-600'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4.5c-.77-.833-2.694-.833-3.464 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                          </svg>
                        </div>
                        <p class="text-xl font-bold" :class="auditProfile.performance.discrepancies > 0 ? 'text-rose-600 dark:text-rose-400' : 'text-gray-900 dark:text-white'">
                          {{ auditProfile.performance.discrepancies }}
                        </p>
                        <p class="text-[10px] text-gray-400 dark:text-zinc-500 mt-0.5">{{ auditProfile.performance.cash_sessions_count }} sesiones de caja</p>
                      </div>
                    </div>
                  </div>

                  <!-- ===== RESUMEN RÁPIDO HOY ===== -->
                  <div class="bg-gray-50 dark:bg-zinc-800/30 border border-gray-200 dark:border-zinc-700/50 p-4">
                    <h3 class="text-xs font-semibold text-gray-500 dark:text-zinc-500 uppercase tracking-wider mb-3">Resumen de Hoy</h3>
                    <div class="grid grid-cols-3 gap-4">
                      <div class="text-center">
                        <p class="text-lg font-bold text-gray-900 dark:text-white">{{ formatCurrency(auditTarget.ventasHoy) }}</p>
                        <p class="text-[10px] text-gray-400 dark:text-zinc-500 uppercase tracking-wide">Ventas</p>
                      </div>
                      <div class="text-center border-x border-gray-200 dark:border-zinc-700/50">
                        <p class="text-lg font-bold text-gray-900 dark:text-white">{{ auditTarget.salesCount || 0 }}</p>
                        <p class="text-[10px] text-gray-400 dark:text-zinc-500 uppercase tracking-wide">Transacciones</p>
                      </div>
                      <div class="text-center">
                        <p class="text-lg font-bold" :class="auditTarget.returnsToday > 0 ? 'text-amber-600 dark:text-amber-400' : 'text-gray-900 dark:text-white'">{{ auditTarget.returnsToday || 0 }}</p>
                        <p class="text-[10px] text-gray-400 dark:text-zinc-500 uppercase tracking-wide">Devoluciones</p>
                      </div>
                    </div>
                  </div>

                  <!-- ===== TIMELINE DE ACTIVIDAD ===== -->
                  <div>
                    <h3 class="text-xs font-bold text-gray-500 dark:text-zinc-500 uppercase tracking-wider mb-3">Registro de Actividad</h3>
                    <div v-if="auditTimeline.length > 0" class="relative">
                      <!-- Línea vertical -->
                      <div class="absolute left-[7px] top-2 bottom-2 w-px bg-gray-200 dark:bg-zinc-700/60"></div>
                      
                      <div v-for="(event, idx) in auditTimeline" :key="idx" class="relative flex items-start gap-3 pb-4 last:pb-0">
                        <!-- Punto con color -->
                        <div class="relative z-10 flex-shrink-0 w-[15px] h-[15px] rounded-full border-2 mt-0.5"
                             :class="{
                               'bg-emerald-500 border-emerald-200 dark:border-emerald-800': event.color === 'emerald',
                               'bg-blue-500 border-blue-200 dark:border-blue-800': event.color === 'blue',
                               'bg-amber-500 border-amber-200 dark:border-amber-800': event.color === 'amber',
                               'bg-rose-500 border-rose-200 dark:border-rose-800': event.color === 'rose',
                               'bg-slate-400 border-slate-200 dark:border-slate-700': event.color === 'slate'
                             }"></div>
                        <div class="flex-1 min-w-0 -mt-0.5">
                          <div class="flex items-center justify-between gap-2">
                            <p class="text-sm font-semibold text-gray-900 dark:text-white truncate">{{ event.title }}</p>
                            <span class="text-[11px] font-mono text-gray-400 dark:text-zinc-500 flex-shrink-0">
                              {{ new Date(event.timestamp).toLocaleTimeString('es-CO', { hour: '2-digit', minute: '2-digit', hour12: false }) }}
                            </span>
                          </div>
                          <p class="text-xs text-gray-500 dark:text-zinc-400 mt-0.5">{{ event.description }}</p>
                        </div>
                      </div>
                    </div>
                    <div v-else class="text-center py-8 bg-gray-50 dark:bg-zinc-800/30 border border-gray-200 dark:border-zinc-700/50">
                      <svg class="w-10 h-10 text-gray-300 dark:text-zinc-600 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                      </svg>
                      <p class="text-sm text-gray-400 dark:text-zinc-500">Sin actividad registrada hoy</p>
                    </div>
                  </div>

                  <!-- ===== SEDES TRABAJADAS ===== -->
                  <div v-if="auditProfile?.warehouses_worked?.length">
                    <h3 class="text-xs font-bold text-gray-500 dark:text-zinc-500 uppercase tracking-wider mb-3">Sedes Trabajadas</h3>
                    <div class="flex flex-wrap gap-2">
                      <span v-for="wh in auditProfile.warehouses_worked" :key="wh.id"
                            class="px-2.5 py-1 text-[10px] font-semibold border uppercase tracking-wide bg-blue-50 dark:bg-blue-950 text-blue-700 dark:text-blue-400 border-blue-100 dark:border-blue-800">
                        {{ wh.name }}
                      </span>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </Transition>
        </div>
      </Transition>
    </Teleport>

    <!-- ============================================================ -->
    <!-- MODAL: Gestión de Registros de Asistencia - Flat Enterprise -->
    <!-- ============================================================ -->
    <Teleport to="body">
      <Transition
        enter-active-class="transition-opacity duration-200 ease-out"
        enter-from-class="opacity-0"
        enter-to-class="opacity-100"
        leave-active-class="transition-opacity duration-150 ease-in"
        leave-from-class="opacity-100"
        leave-to-class="opacity-0"
      >
        <div v-if="attendanceModalOpen" class="fixed inset-0 z-[60] flex items-center justify-center p-4">
          <div class="absolute inset-0 bg-black/50" @click="closeAttendanceModal"></div>

          <div class="relative w-full max-w-5xl bg-white dark:bg-zinc-900 border border-gray-200 dark:border-zinc-800 shadow-xl dark:shadow-black/60 overflow-hidden">

            <!-- Header corporativo -->
            <div class="flex items-center justify-between px-8 py-5 border-b border-gray-200 dark:border-zinc-800">
              <div>
                <h2 class="text-xl font-black text-gray-900 dark:text-white uppercase tracking-tight">Gestión de Asistencia</h2>
                <p class="text-xs text-gray-500 dark:text-zinc-500 mt-1">Registros de entrada y salida · {{ attendanceTarget?.name }}</p>
              </div>
              <button @click="closeAttendanceModal" class="p-2 border border-transparent text-gray-400 dark:text-zinc-500 hover:text-rose-600 dark:hover:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-900/20 hover:border-rose-100 dark:hover:border-rose-900/30 transition-colors duration-150">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
              </button>
            </div>

            <!-- Body: dos paneles -->
            <div class="flex divide-x divide-gray-200 dark:divide-zinc-800">

              <!-- Panel izquierdo: Info empleado -->
              <div class="w-[38%] bg-gray-50 dark:bg-zinc-900/50 p-6">
                <div class="flex items-center gap-3 mb-6">
                  <div class="w-12 h-12 flex items-center justify-center bg-blue-50 dark:bg-blue-950 text-blue-700 dark:text-blue-400 font-bold text-sm border border-blue-100 dark:border-blue-800">
                    {{ attendanceTarget ? getInitials(attendanceTarget.name) : '?' }}
                  </div>
                  <div>
                    <p class="text-sm font-bold text-gray-900 dark:text-white">{{ attendanceTarget?.name }}</p>
                    <p class="text-xs text-gray-500 dark:text-zinc-500">{{ attendanceTarget?.roleName }}</p>
                  </div>
                </div>

                <!-- Registros actuales -->
                <div class="border border-gray-200 dark:border-zinc-800 overflow-hidden">
                  <div class="bg-gray-100 dark:bg-zinc-800/50 px-4 py-2.5">
                    <p class="text-[11px] font-bold text-gray-500 dark:text-zinc-400 uppercase tracking-widest">Registros del {{ filterDateLabel }}</p>
                  </div>
                  <div class="divide-y divide-gray-100 dark:divide-zinc-800">
                    <div class="px-4 py-3 flex items-center justify-between">
                      <div class="flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                        <span class="text-xs font-medium text-gray-600 dark:text-zinc-400">Entrada</span>
                      </div>
                      <span class="text-sm font-semibold text-gray-900 dark:text-white tabular-nums">{{ attendanceTarget?.entryTime || '—' }}</span>
                    </div>
                    <div class="px-4 py-3 flex items-center justify-between">
                      <div class="flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                        <span class="text-xs font-medium text-gray-600 dark:text-zinc-400">Salida</span>
                      </div>
                      <div class="flex items-center gap-1.5">
                        <span class="text-sm font-semibold text-gray-900 dark:text-white tabular-nums">{{ attendanceTarget?.exitTime || '—' }}</span>
                        <span v-if="attendanceTarget?.exitIsAutoClose" class="px-1.5 py-0.5 rounded text-[8px] font-bold uppercase bg-amber-100 dark:bg-amber-950 text-amber-700 dark:text-amber-400 border border-amber-200 dark:border-amber-800">SIS</span>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Info nota -->
                <div class="mt-4 p-3 bg-amber-50 dark:bg-amber-950/30 border border-amber-200 dark:border-amber-800/50">
                  <div class="flex items-start gap-2">
                    <svg class="w-4 h-4 text-amber-600 dark:text-amber-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <p class="text-[11px] text-amber-700 dark:text-amber-400 leading-relaxed">
                      Los cambios en los registros de asistencia son permanentes y quedarán reflejados en el historial.
                    </p>
                  </div>
                </div>
              </div>

              <!-- Panel derecho: Tabla de registros + acciones -->
              <div class="w-[62%] p-6">

                <!-- Loading -->
                <div v-if="attendanceLoading" class="flex items-center justify-center py-16">
                  <svg class="animate-spin h-7 w-7 text-gray-400 dark:text-zinc-500" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                  </svg>
                </div>

                <div v-else>
                  <!-- Tabla de registros -->
                  <div class="border border-gray-200 dark:border-zinc-800 overflow-hidden">
                    <table class="w-full">
                      <thead>
                        <tr class="bg-gray-50 dark:bg-zinc-800/50 border-b border-gray-200 dark:border-zinc-800">
                          <th class="px-4 py-2.5 text-left text-[11px] font-bold text-gray-500 dark:text-zinc-400 uppercase tracking-widest">Evento</th>
                          <th class="px-4 py-2.5 text-left text-[11px] font-bold text-gray-500 dark:text-zinc-400 uppercase tracking-widest">Fecha y Hora</th>
                          <th class="px-4 py-2.5 text-center text-[11px] font-bold text-gray-500 dark:text-zinc-400 uppercase tracking-widest">Origen</th>
                          <th class="px-4 py-2.5 text-right text-[11px] font-bold text-gray-500 dark:text-zinc-400 uppercase tracking-widest">Acciones</th>
                        </tr>
                      </thead>
                      <tbody class="divide-y divide-gray-100 dark:divide-zinc-800">
                        <tr v-for="log in attendanceLogs" :key="log.id" class="hover:bg-gray-50 dark:hover:bg-zinc-800/50 transition-colors duration-150">
                          <!-- Tipo -->
                          <td class="px-4 py-3">
                            <div class="flex items-center gap-2">
                              <span class="w-2 h-2 rounded-full" :class="{'bg-emerald-500': log.event_type === 'entry', 'bg-blue-500': log.event_type === 'exit', 'bg-amber-500': log.event_type === 'break_start', 'bg-violet-500': log.event_type === 'break_end'}[true] || 'bg-gray-400'"></span>
                              <span class="text-sm font-medium text-gray-900 dark:text-white">{{ {entry: 'Entrada', exit: 'Salida', break_start: 'Inicio Break', break_end: 'Fin Break'}[log.event_type] || log.event_type }}</span>
                            </div>
                          </td>
                          <!-- Hora -->
                          <td class="px-4 py-3">
                            <div v-if="editingLogId === log.id" class="flex items-center gap-2">
                              <input
                                v-model="editLogDateTime"
                                type="datetime-local"
                                class="px-2 py-1.5 text-sm border border-gray-300 dark:border-zinc-600 bg-white dark:bg-zinc-800 text-gray-900 dark:text-zinc-200 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                              />
                              <button @click="saveEditLog(log.id)" class="p-1.5 text-emerald-600 dark:text-emerald-400 hover:bg-emerald-50 dark:hover:bg-emerald-900/20 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                              </button>
                              <button @click="cancelEditLog" class="p-1.5 text-gray-400 dark:text-zinc-500 hover:bg-gray-100 dark:hover:bg-zinc-800 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                              </button>
                            </div>
                            <span v-else class="text-sm text-gray-700 dark:text-zinc-300 tabular-nums">
                              {{ formatLogDateTime(log.event_at) }}
                            </span>
                          </td>
                          <!-- Origen -->
                          <td class="px-4 py-3 text-center">
                            <span v-if="log.is_auto_closed" class="px-2 py-0.5 text-[10px] font-semibold border uppercase tracking-wide bg-amber-50 dark:bg-amber-950 text-amber-700 dark:text-amber-400 border-amber-100 dark:border-amber-800">
                              Sistema
                            </span>
                            <span v-else class="px-2 py-0.5 text-[10px] font-semibold border uppercase tracking-wide bg-gray-100 dark:bg-zinc-800 text-gray-500 dark:text-zinc-500 border-gray-200 dark:border-zinc-700">
                              Biométrico
                            </span>
                          </td>
                          <!-- Acciones -->
                          <td class="px-4 py-3 text-right">
                            <div class="flex items-center justify-end gap-1">
                              <button
                                @click="startEditLog(log)"
                                class="p-1.5 border border-transparent text-slate-400 dark:text-zinc-400 hover:text-amber-600 dark:hover:text-amber-400 hover:bg-amber-50 dark:hover:bg-amber-900/20 hover:border-amber-100 dark:hover:border-amber-900/30 transition-colors duration-150"
                                title="Editar hora"
                              >
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                              </button>
                              <button
                                @click="confirmDeleteLog(log)"
                                class="p-1.5 border border-transparent text-slate-400 dark:text-zinc-500 hover:text-rose-600 dark:hover:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-900/20 hover:border-rose-100 dark:hover:border-rose-900/30 transition-colors duration-150"
                                title="Eliminar registro"
                              >
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                              </button>
                            </div>
                          </td>
                        </tr>
                      </tbody>
                    </table>
                    <!-- Empty state -->
                    <div v-if="attendanceLogs.length === 0" class="px-4 py-10 text-center">
                      <svg class="w-10 h-10 text-gray-300 dark:text-zinc-600 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                      </svg>
                      <p class="text-sm text-gray-400 dark:text-zinc-500">Sin registros de asistencia hoy</p>
                    </div>
                  </div>

                  <!-- Confirmación de eliminación inline -->
                  <Transition
                    enter-active-class="transition-all duration-200 ease-out"
                    enter-from-class="opacity-0 -translate-y-2"
                    enter-to-class="opacity-100 translate-y-0"
                    leave-active-class="transition-all duration-150 ease-in"
                    leave-from-class="opacity-100 translate-y-0"
                    leave-to-class="opacity-0 -translate-y-2"
                  >
                    <div v-if="deleteConfirmLog" class="mt-4 p-4 bg-rose-50 dark:bg-rose-950/30 border border-rose-200 dark:border-rose-800/50">
                      <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                          <svg class="w-5 h-5 text-rose-600 dark:text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4.5c-.77-.833-2.694-.833-3.464 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                          </svg>
                          <p class="text-sm font-medium text-rose-800 dark:text-rose-300">
                            ¿Eliminar registro de <strong>{{ {entry: 'entrada', exit: 'salida', break_start: 'inicio break', break_end: 'fin break'}[deleteConfirmLog.event_type] || deleteConfirmLog.event_type }}</strong> ({{ formatLogDateTime(deleteConfirmLog.event_at) }})?
                          </p>
                        </div>
                        <div class="flex items-center gap-2">
                          <button @click="deleteConfirmLog = null" class="px-3 py-1.5 text-xs font-bold border border-gray-300 dark:border-zinc-600 text-gray-600 dark:text-zinc-300 hover:bg-white dark:hover:bg-zinc-800 transition-colors">
                            Cancelar
                          </button>
                          <button @click="executeDeleteLog" :disabled="attendanceSaving" class="px-3 py-1.5 text-xs font-bold bg-rose-600 hover:bg-rose-700 text-white transition-colors disabled:opacity-50">
                            Confirmar
                          </button>
                        </div>
                      </div>
                    </div>
                  </Transition>

                  <!-- Botón Eliminar Todos -->
                  <div v-if="attendanceLogs.length > 0" class="mt-4 flex items-center justify-between">
                    <button
                      @click="confirmDeleteAllLogs"
                      class="px-4 py-2 text-xs font-bold border border-rose-200 dark:border-rose-800/50 text-rose-600 dark:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-900/20 transition-colors duration-150"
                    >
                      Eliminar todos los registros del {{ filterDateLabel.toLowerCase() }}
                    </button>
                    <p v-if="attendanceMessage" class="text-xs font-medium" :class="attendanceMessageType === 'success' ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400'">
                      {{ attendanceMessage }}
                    </p>
                  </div>

                  <!-- Confirmación eliminar todos -->
                  <Transition
                    enter-active-class="transition-all duration-200 ease-out"
                    enter-from-class="opacity-0 -translate-y-2"
                    enter-to-class="opacity-100 translate-y-0"
                    leave-active-class="transition-all duration-150 ease-in"
                    leave-from-class="opacity-100 translate-y-0"
                    leave-to-class="opacity-0 -translate-y-2"
                  >
                    <div v-if="deleteAllConfirm" class="mt-3 p-4 bg-rose-50 dark:bg-rose-950/30 border border-rose-200 dark:border-rose-800/50">
                      <div class="flex items-center justify-between">
                        <p class="text-sm font-medium text-rose-800 dark:text-rose-300">
                          ¿Eliminar <strong>todos los registros</strong> de {{ attendanceTarget?.name }} del día de hoy?
                        </p>
                        <div class="flex items-center gap-2">
                          <button @click="deleteAllConfirm = false" class="px-3 py-1.5 text-xs font-bold border border-gray-300 dark:border-zinc-600 text-gray-600 dark:text-zinc-300 hover:bg-white dark:hover:bg-zinc-800 transition-colors">
                            Cancelar
                          </button>
                          <button @click="executeDeleteAllLogs" :disabled="attendanceSaving" class="px-3 py-1.5 text-xs font-bold bg-rose-600 hover:bg-rose-700 text-white transition-colors disabled:opacity-50">
                            Sí, Eliminar Todo
                          </button>
                        </div>
                      </div>
                    </div>
                  </Transition>
                </div>
              </div>
            </div>

            <!-- Footer corporativo -->
            <div class="flex items-center justify-end px-8 py-4 border-t border-gray-200 dark:border-zinc-800 bg-gray-50 dark:bg-zinc-900/50">
              <button @click="closeAttendanceModal" class="px-5 py-2.5 text-sm font-bold border border-gray-300 dark:border-zinc-700 text-gray-700 dark:text-zinc-300 hover:bg-gray-100 dark:hover:bg-zinc-800 transition-colors">
                Cerrar
              </button>
            </div>
          </div>
        </div>
      </Transition>
    </Teleport>

    <!-- MODAL: Crear/Editar Usuario -->
    <UserModal 
      ref="userModalRef"
      :show="showUserModal"
      :user="selectedUser"
      :roles="roles"
      :warehouses="warehouses"
      @close="closeUserModal"
      @save="saveUser"
    />

    <!-- MODAL: Cambiar Contraseña -->
    <PasswordModal 
      :show="showPasswordModal"
      :user="selectedUser"
      @close="closePasswordModal"
      @save="savePassword"
    />

  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted, watch } from 'vue'
import usersService from '../services/usersService.js'
import rolesService from '../services/rolesService.js'
import { warehouseService } from '../services/warehouseService.js'
import { appStore } from '../store/appStore.js'
import { useUIContextStore } from '../store/uiContextStore.js'
import UserModal from './UserModal.vue'
import PasswordModal from './PasswordModal.vue'
import biometricService from '../services/biometricService.js'

// ===== CONTEXTO AI =====
const uiContextStore = useUIContextStore()

// ===== REFS A MODALES =====
const userModalRef = ref(null)

// ===== ESTADO REACTIVO =====
const loading = ref(false)

// Usuarios
const users = ref([])
const showUserModal = ref(false)
const selectedUser = ref(null)

// Roles (se mantiene para mapeo interno)
const roles = ref([])

// Warehouses / Sedes
const warehouses = ref([])

// ===== ALERTAS OPERATIVAS =====
const showAlertDetails = ref(false)

// ===== FILTROS DE FECHA (Single Date Selection) =====
const activeDatePreset = ref('today')
const filterDate = ref(new Date().toISOString().split('T')[0])

const datePresets = [
  { key: 'today', label: 'Hoy' },
  { key: 'yesterday', label: 'Ayer' },
  { key: 'custom', label: 'Personalizado' },
]

const filterDateLabel = computed(() => {
  if (activeDatePreset.value === 'today') return 'Hoy'
  if (activeDatePreset.value === 'yesterday') return 'Ayer'
  return filterDate.value
})

const getDateParams = () => {
  return { date: filterDate.value }
}

const selectDatePreset = (key) => {
  const today = new Date()
  const fmt = (d) => d.toISOString().split('T')[0]

  if (key === 'today') {
    filterDate.value = fmt(today)
  } else if (key === 'yesterday') {
    const yesterday = new Date(today)
    yesterday.setDate(yesterday.getDate() - 1)
    filterDate.value = fmt(yesterday)
  } else if (key === 'custom') {
    activeDatePreset.value = 'custom'
    return
  }
  activeDatePreset.value = key
  loadDashboardData()
}

const onCustomDateChange = () => {
  if (filterDate.value) {
    loadDashboardData()
  }
}

const formatAlertTime = (dateStr) => {
  if (!dateStr) return ''
  try {
    return new Date(dateStr).toLocaleTimeString('es-CO', { hour: '2-digit', minute: '2-digit', hour12: true })
  } catch { return '' }
}

// ===== PANEL DE AUDITORÍA (Slide-over) =====
const auditPanelOpen = ref(false)
const auditTarget = ref(null)
const auditProfile = ref(null)
const auditTimeline = ref([])
const auditLoading = ref(false)

const openAuditPanel = async (employee) => {
  auditTarget.value = employee
  auditPanelOpen.value = true
  auditLoading.value = true
  try {
    const [profileRes, timelineRes] = await Promise.all([
      usersService.getUserProfile(employee.id),
      usersService.getUserTimeline(employee.id)
    ])
    auditProfile.value = profileRes.data || null
    auditTimeline.value = timelineRes.data?.events || []
  } catch (e) {
    console.error('Error cargando auditoría:', e)
  } finally {
    auditLoading.value = false
  }
}

const closeAuditPanel = () => {
  auditPanelOpen.value = false
  auditTarget.value = null
  auditProfile.value = null
  auditTimeline.value = []
}

// ===== GESTIÓN DE ASISTENCIA (Admin) =====
const attendanceModalOpen = ref(false)
const attendanceTarget = ref(null)
const attendanceLogs = ref([])
const attendanceLoading = ref(false)
const attendanceSaving = ref(false)
const attendanceMessage = ref('')
const attendanceMessageType = ref('success')
const editingLogId = ref(null)
const editLogDateTime = ref('')
const deleteConfirmLog = ref(null)
const deleteAllConfirm = ref(false)

const openAttendanceModal = async (emp) => {
  attendanceTarget.value = emp
  attendanceModalOpen.value = true
  attendanceLoading.value = true
  attendanceMessage.value = ''
  deleteConfirmLog.value = null
  deleteAllConfirm.value = false
  editingLogId.value = null
  try {
    const res = await biometricService.getAttendanceHistory({ user_id: emp.id, date: filterDate.value })
    attendanceLogs.value = res.data || []
  } catch (e) {
    attendanceLogs.value = []
  } finally {
    attendanceLoading.value = false
  }
}

const closeAttendanceModal = () => {
  attendanceModalOpen.value = false
  attendanceTarget.value = null
  attendanceLogs.value = []
  editingLogId.value = null
  deleteConfirmLog.value = null
  deleteAllConfirm.value = false
  attendanceMessage.value = ''
}

const formatLogDateTime = (dt) => {
  if (!dt) return '—'
  try {
    const d = new Date(dt)
    return d.toLocaleString('es-CO', { day: '2-digit', month: '2-digit', year: 'numeric', hour: '2-digit', minute: '2-digit', hour12: true })
  } catch {
    return dt
  }
}

const toLocalDatetimeValue = (dt) => {
  if (!dt) return ''
  const d = new Date(dt)
  const pad = (n) => String(n).padStart(2, '0')
  return `${d.getFullYear()}-${pad(d.getMonth() + 1)}-${pad(d.getDate())}T${pad(d.getHours())}:${pad(d.getMinutes())}`
}

const startEditLog = (log) => {
  editingLogId.value = log.id
  editLogDateTime.value = toLocalDatetimeValue(log.event_at)
  deleteConfirmLog.value = null
  deleteAllConfirm.value = false
}

const cancelEditLog = () => {
  editingLogId.value = null
  editLogDateTime.value = ''
}

const saveEditLog = async (logId) => {
  if (!editLogDateTime.value) return
  attendanceSaving.value = true
  try {
    await biometricService.updateAttendanceLog(logId, editLogDateTime.value)
    const res = await biometricService.getAttendanceHistory({ user_id: attendanceTarget.value.id, date: filterDate.value })
    attendanceLogs.value = res.data || []
    editingLogId.value = null
    editLogDateTime.value = ''
    showAttendanceMsg('Registro actualizado', 'success')
    loadDashboardData()
  } catch {
    showAttendanceMsg('Error al actualizar', 'error')
  } finally {
    attendanceSaving.value = false
  }
}

const confirmDeleteLog = (log) => {
  deleteConfirmLog.value = log
  deleteAllConfirm.value = false
  editingLogId.value = null
}

const executeDeleteLog = async () => {
  if (!deleteConfirmLog.value) return
  attendanceSaving.value = true
  try {
    await biometricService.deleteAttendanceLog(deleteConfirmLog.value.id)
    const res = await biometricService.getAttendanceHistory({ user_id: attendanceTarget.value.id, date: filterDate.value })
    attendanceLogs.value = res.data || []
    deleteConfirmLog.value = null
    showAttendanceMsg('Registro eliminado', 'success')
    loadDashboardData()
  } catch {
    showAttendanceMsg('Error al eliminar', 'error')
  } finally {
    attendanceSaving.value = false
  }
}

const confirmDeleteAllLogs = () => {
  deleteAllConfirm.value = true
  deleteConfirmLog.value = null
  editingLogId.value = null
}

const executeDeleteAllLogs = async () => {
  if (!attendanceTarget.value) return
  attendanceSaving.value = true
  try {
    await biometricService.deleteUserAttendanceLogs(attendanceTarget.value.id, filterDate.value)
    attendanceLogs.value = []
    deleteAllConfirm.value = false
    showAttendanceMsg('Todos los registros eliminados', 'success')
    loadDashboardData()
  } catch {
    showAttendanceMsg('Error al eliminar registros', 'error')
  } finally {
    attendanceSaving.value = false
  }
}

const showAttendanceMsg = (msg, type) => {
  attendanceMessage.value = msg
  attendanceMessageType.value = type
  setTimeout(() => { attendanceMessage.value = '' }, 3000)
}

// ===== DATOS REALES DEL DASHBOARD ERP =====
const dashboardKpis = ref({
  active_now: [],
  active_now_count: 0,
  sales_today: 0,
  sales_count_today: 0,
  returns_today_amount: 0,
  returns_today_count: 0,
  discrepancies_today: 0,
  return_alerts: []
})

// Usuarios enriquecidos con datos de rendimiento del backend
const usersWithPerformance = ref([])

// ===== GRID DATA: Datos reales del backend =====
const employeeGridData = computed(() => {
  return usersWithPerformance.value.map(user => ({
    id: user.id,
    name: user.name,
    active: user.active,
    roleName: user.role?.name || 'Sin rol',
    ultimoIngreso: formatIngress(user.last_ingress),
    entryTime: user.entry_time ? formatTimeOnly(user.entry_time) : null,
    exitTime: user.exit_time ? formatTimeOnly(user.exit_time) : null,
    exitIsAutoClose: user.exit_is_auto_closed || false,
    exitClosedBy: user.exit_closed_by || 'user',
    sedeActual: user.current_warehouse || 'Sin sede',
    ventasHoy: user.sales_today || 0,
    salesCount: user.sales_count_today || 0,
    cajaAbierta: user.cash_status === 'open',
    cajaForzada: user.cash_status === 'forced_closed',
    cashStatus: user.cash_status || 'closed',
    isOnline: user.cash_status === 'open' && user.active,
    returnsToday: user.returns_today || 0,
    cashDiscrepancy: user.cash_discrepancy ?? null,
  }))
})

const findUserById = (id) => users.value.find(u => u.id === id)

const getInitials = (name) => {
  if (!name) return '?'
  const parts = name.trim().split(' ')
  if (parts.length >= 2) return (parts[0][0] + parts[1][0]).toUpperCase()
  return name.substring(0, 2).toUpperCase()
}

const formatCurrency = (value) => {
  if (!value && value !== 0) return '$0'
  return new Intl.NumberFormat('es-CO', { style: 'currency', currency: 'COP', minimumFractionDigits: 0, maximumFractionDigits: 0 }).format(value)
}

const formatIngress = (dateStr) => {
  if (!dateStr) return 'Sin registro'
  try {
    const date = new Date(dateStr)
    const now = new Date()
    const isToday = date.toDateString() === now.toDateString()
    const yesterday = new Date(now)
    yesterday.setDate(yesterday.getDate() - 1)
    const isYesterday = date.toDateString() === yesterday.toDateString()
    
    const timeStr = date.toLocaleTimeString('es-CO', { hour: '2-digit', minute: '2-digit', hour12: true })
    if (isToday) return `Hoy, ${timeStr}`
    if (isYesterday) return `Ayer, ${timeStr}`
    return date.toLocaleDateString('es-CO', { day: '2-digit', month: 'short' }) + `, ${timeStr}`
  } catch {
    return 'Sin registro'
  }
}

const formatTimeOnly = (dateStr) => {
  if (!dateStr) return null
  try {
    const date = new Date(dateStr)
    return date.toLocaleTimeString('es-CO', { hour: '2-digit', minute: '2-digit', hour12: true })
  } catch {
    return null
  }
}

// ===== CARGA DE DATOS REALES =====
const alertasTotal = computed(() => (dashboardKpis.value.discrepancies_today || 0) + (dashboardKpis.value.returns_today_count || 0))

const loadDashboardData = async () => {
  try {
    const dateParams = getDateParams()
    const [kpisRes, perfRes] = await Promise.all([
      usersService.getDashboardKpis(dateParams),
      usersService.getUsersWithPerformance(dateParams)
    ])
    if (kpisRes.success) dashboardKpis.value = kpisRes.data
    if (perfRes.success) usersWithPerformance.value = perfRes.data || []
  } catch (e) {
    console.error('Error cargando dashboard:', e)
  }
}

// Contraseña
const showPasswordModal = ref(false)

// ===== VALIDACIÓN DE PLAN =====
// Límites de usuarios según el plan
const planUserLimits = {
  'free_trial': 2,
  'basic': 4,
  'pro': 4,       // premium en el frontend se llama 'pro'
  'premium': 4,   // alias por si acaso
  'enterprise': null // null = ilimitado
}

const currentPlan = computed(() => appStore.tenantPlan || 'free_trial')
const maxUsersAllowed = computed(() => planUserLimits[currentPlan.value] ?? 2)
const currentUsersCount = computed(() => users.value.length)
const canCreateMoreUsers = computed(() => {
  // Si es enterprise (null), siempre puede crear
  if (maxUsersAllowed.value === null) return true
  return currentUsersCount.value < maxUsersAllowed.value
})
const remainingUserSlots = computed(() => {
  if (maxUsersAllowed.value === null) return '∞'
  return Math.max(0, maxUsersAllowed.value - currentUsersCount.value)
})

// ===== COMPUTED PROPERTIES =====
const activeUsersCount = computed(() => users.value.filter(u => u.active).length)
const inactiveUsersCount = computed(() => users.value.filter(u => !u.active).length)

// ===== PERMISOS ULTRA SIMPLIFICADOS: SOLO MÓDULOS =====
// 17 módulos = 17 permisos
// Si tiene el permiso → Ve el módulo completo
// Si NO tiene el permiso → El módulo no aparece en el menú
const permissionsModules = ref([
  {
    id: 'dashboard',
    name: 'Dashboard',
    description: 'Panel principal con estadísticas y KPIs',
    color: '#3B82F6',
    icon: 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6'
  },
  {
    id: 'pos',
    name: 'Punto de Venta (POS)',
    description: 'Sistema de ventas y cobros',
    color: '#10B981',
    icon: 'M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z'
  },
  {
    id: 'invoices',
    name: 'Facturas',
    description: 'Gestión de facturas y documentos',
    color: '#F59E0B',
    icon: 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'
  },
  {
    id: 'returns',
    name: 'Devoluciones',
    description: 'Gestión de devoluciones y reembolsos',
    color: '#EF4444',
    icon: 'M16 15v-1a4 4 0 00-4-4H8m0 0l3 3m-3-3l3-3m9 14V5a2 2 0 00-2-2H6a2 2 0 00-2 2v16l4-2 4 2 4-2 4 2z'
  },
  {
    id: 'products',
    name: 'Productos',
    description: 'Catálogo de productos y servicios',
    color: '#8B5CF6',
    icon: 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4'
  },
  {
    id: 'categories',
    name: 'Categorías',
    description: 'Organización de productos por categorías',
    color: '#EC4899',
    icon: 'M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z'
  },
  {
    id: 'stock',
    name: 'Gestión de Stock',
    description: 'Control de inventario y movimientos',
    color: '#14B8A6',
    icon: 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01'
  },
  {
    id: 'intelligent_inventory',
    name: 'Inventario IA',
    description: 'Análisis inteligente de inventario con IA',
    color: '#A855F7',
    icon: 'M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z',
    isPremium: true
  },
  {
    id: 'warehouses',
    name: 'Multisede (Bodegas)',
    description: 'Gestión de múltiples sedes y traslados',
    color: '#F97316',
    icon: 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4',
    isPremium: true
  },
  {
    id: 'customers',
    name: 'Clientes',
    description: 'Base de datos de clientes',
    color: '#0EA5E9',
    icon: 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z'
  },
  {
    id: 'accounts_receivable',
    name: 'Cuentas por Cobrar',
    description: 'Gestión de créditos y cobranzas',
    color: '#06B6D4',
    icon: 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
    isPremium: true
  },
  {
    id: 'suppliers',
    name: 'Proveedores',
    description: 'Gestión de proveedores y compras',
    color: '#F97316',
    icon: 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4'
  },
  {
    id: 'users',
    name: 'Usuarios y Roles',
    description: 'Administración de usuarios y permisos',
    color: '#6366F1',
    icon: 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z'
  },
  {
    id: 'cash_register',
    name: 'Caja (Administración)',
    description: 'Control de turnos de caja',
    color: '#DC2626',
    icon: 'M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z'
  },
  {
    id: 'expenses',
    name: 'Gastos Operativos',
    description: 'Registro y control de gastos',
    color: '#EF4444',
    icon: 'M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z'
  },
  {
    id: 'reports',
    name: 'Reportes',
    description: 'Informes y estadísticas del negocio',
    color: '#059669',
    icon: 'M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'
  },
  {
    id: 'settings',
    name: 'Configuración',
    description: 'Configuración general del sistema',
    color: '#64748B',
    icon: 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z'
  }
])

// ===== MÉTODOS =====
const refreshData = async () => {
  try {
    loading.value = true
    await Promise.all([loadUsers(), loadRoles(), loadWarehouses(), loadDashboardData()])
  } catch (error) {
    console.error('Error actualizando datos:', error)
  } finally {
    loading.value = false
  }
}

const loadUsers = async () => {
  try {
    const response = await usersService.getAllUsers()
    users.value = response.data || []
  } catch (error) {
    console.error('Error cargando usuarios:', error)
  }
}

const loadRoles = async () => {
  try {
    const response = await rolesService.getAllRoles()
    roles.value = response.data || []
  } catch (error) {
    console.error('Error cargando roles:', error)
  }
}

const loadWarehouses = async () => {
  try {
    const response = await warehouseService.getAll()
    // api.get returns parsed JSON directly: { warehouses: [...], plan_info: {...} }
    warehouses.value = response.warehouses || response.data || []
  } catch (error) {
    console.error('Error cargando sedes:', error)
  }
}

// Usuarios
const openCreateUserModal = () => {
  // 🔒 VALIDACIÓN DE PLAN: Verificar si puede crear más usuarios
  if (!canCreateMoreUsers.value) {
    const planName = currentPlan.value === 'free_trial' ? 'Prueba Gratuita' : 
                     currentPlan.value === 'basic' ? 'Básico' :
                     currentPlan.value === 'pro' || currentPlan.value === 'premium' ? 'Premium' : 
                     'Enterprise'
    alert(`⚠️ Has alcanzado el límite de ${maxUsersAllowed.value} usuarios para el plan ${planName}.\n\n💎 Actualiza tu plan para agregar más usuarios.`)
    return
  }
  
  selectedUser.value = null
  showUserModal.value = true
}

const openEditUserModal = (user) => {
  // 🔒 PROTECCIÓN: No permitir editar el usuario administrador inicial
  if (user.role_id === 1) {
    alert('⚠️ El usuario Administrador principal no puede ser editado por seguridad del sistema')
    return
  }
  
  selectedUser.value = user
  showUserModal.value = true
}

const closeUserModal = () => {
  showUserModal.value = false
  selectedUser.value = null
}

const saveUser = async (userData) => {
  try {
    loading.value = true
    
    if (selectedUser.value) {
      // Editar usuario existente
      await usersService.updateUser(selectedUser.value.id, userData)
      alert('✅ Usuario actualizado exitosamente')
    } else {
      // Crear nuevo usuario
      await usersService.createUser(userData)
      alert('✅ Usuario creado exitosamente')
    }
    
    await loadUsers()
    closeUserModal()
  } catch (error) {
    console.error('Error guardando usuario:', error)
    alert('❌ Error al guardar el usuario')
  } finally {
    loading.value = false
  }
}

const deleteUser = async (user) => {
  // 🔒 PROTECCIÓN: No permitir eliminar el usuario administrador inicial
  if (user.role_id === 1) {
    alert('⚠️ El usuario Administrador principal no puede ser eliminado por seguridad del sistema')
    return
  }
  
  if (!confirm(`¿Estás seguro de eliminar al usuario "${user.name}"?`)) {
    return
  }
  
  try {
    loading.value = true
    await usersService.deleteUser(user.id)
    alert('✅ Usuario eliminado exitosamente')
    await loadUsers()
  } catch (error) {
    console.error('Error eliminando usuario:', error)
    alert('❌ Error al eliminar el usuario')
  } finally {
    loading.value = false
  }
}

const toggleUserStatus = async (user) => {
  // 🔒 PROTECCIÓN: No permitir desactivar el usuario administrador inicial
  if (user.role_id === 1) {
    alert('⚠️ El usuario Administrador principal no puede ser desactivado por seguridad del sistema')
    return
  }
  
  try {
    loading.value = true
    await usersService.toggleStatus(user.id)
    alert(`✅ Usuario ${user.active ? 'desactivado' : 'activado'} exitosamente`)
    await loadUsers()
  } catch (error) {
    console.error('Error cambiando estado del usuario:', error)
    alert('❌ Error al cambiar el estado')
  } finally {
    loading.value = false
  }
}

const openPasswordModal = (user) => {
  // Permitir cambiar contraseña del administrador (para recuperación)
  selectedUser.value = user
  showPasswordModal.value = true
}

// Contraseña
const closePasswordModal = () => {
  showPasswordModal.value = false
  selectedUser.value = null
}

const savePassword = async (passwordData) => {
  try {
    loading.value = true
    await usersService.changePassword(selectedUser.value.id, passwordData)
    alert('✅ Contraseña actualizada exitosamente')
    closePasswordModal()
  } catch (error) {
    console.error('Error cambiando contraseña:', error)
    alert('❌ Error al cambiar la contraseña')
  } finally {
    loading.value = false
  }
}

// ===== CONTEXTO AI =====
const updateAIContext = () => {
  const contextData = {
    modulo: 'usuarios',
    
    // Información de usuarios
    usuarios: {
      total: users.value.length,
      activos: activeUsersCount.value,
      inactivos: inactiveUsersCount.value,
      limite: maxUsersAllowed.value,
      puedeCrearMas: canCreateMoreUsers.value,
      espaciosDisponibles: remainingUserSlots.value,
      lista: users.value.map(u => ({
        id: u.id,
        nombre: u.name,
        email: u.email,
        cedula: u.document,
        telefono: u.phone,
        rol: u.role?.name || 'Sin rol',
        rolId: u.role_id,
        activo: u.active
      }))
    },
    
    // Sedes disponibles
    sedes: warehouses.value.map(w => ({ id: w.id, nombre: w.name })),
    
    // Estado de modales
    modales: {
      usuarioAbierto: showUserModal.value,
      passwordAbierto: showPasswordModal.value,
      usuarioEditando: selectedUser.value ? {
        id: selectedUser.value.id,
        nombre: selectedUser.value.name
      } : null
    },
    
    // Plan actual
    plan: {
      nombre: currentPlan.value,
      limiteUsuarios: maxUsersAllowed.value
    }
  }
  
  uiContextStore.setScreenData(contextData)
}

const registerAIActions = () => {
  // Listar usuarios
  uiContextStore.registerAction('listarUsuarios', () => {
    const lista = users.value.map(u => ({
      nombre: u.name,
      email: u.email,
      rol: u.role?.name || 'Sin rol',
      activo: u.active ? 'Sí' : 'No'
    }))
    return {
      success: true,
      message: `Hay ${users.value.length} usuarios registrados.`,
      usuarios: lista
    }
  })
  
  // Abrir modal crear usuario
  uiContextStore.registerAction('abrirCrearUsuario', () => {
    if (!canCreateMoreUsers.value) {
      return { 
        success: false, 
        message: `Has alcanzado el límite de ${maxUsersAllowed.value} usuarios. Actualiza tu plan para agregar más.` 
      }
    }
    openCreateUserModal()
    updateAIContext()
    return { 
      success: true, 
      message: 'Modal de nuevo usuario abierto. Campos: nombre, email, cédula, teléfono, contraseña, rol, sede.' 
    }
  })
  
  // Cerrar modales
  uiContextStore.registerAction('cerrarModalUsuario', () => {
    closeUserModal()
    updateAIContext()
    return { success: true, message: 'Modal de usuario cerrado.' }
  })
  
  // Llenar campo de usuario
  uiContextStore.registerAction('llenarCampoUsuario', ({ campo, valor }) => {
    if (!showUserModal.value) {
      return { success: false, message: 'Primero debes abrir el modal de crear usuario.' }
    }
    
    if (!userModalRef.value) {
      return { success: false, message: 'Modal no disponible.' }
    }
    
    // Mapear rol por nombre si es necesario
    if (campo.toLowerCase() === 'rol' || campo.toLowerCase() === 'role_id') {
      const rolEncontrado = roles.value.find(r => 
        r.id.toString() === valor || 
        r.name.toLowerCase() === valor.toLowerCase()
      )
      if (rolEncontrado) {
        userModalRef.value.setFieldValue('role_id', rolEncontrado.id)
        return { success: true, message: `Rol asignado: ${rolEncontrado.name}` }
      } else {
        return { success: false, message: `No encontré el rol "${valor}". Roles disponibles: ${roles.value.map(r => r.name).join(', ')}` }
      }
    }
    
    // Mapear sede por nombre
    if (campo.toLowerCase() === 'sede' || campo.toLowerCase() === 'warehouse_id') {
      const sedeEncontrada = warehouses.value.find(w =>
        w.id.toString() === valor ||
        w.name.toLowerCase().includes(valor.toLowerCase())
      )
      if (sedeEncontrada) {
        userModalRef.value.setFieldValue('warehouse_id', sedeEncontrada.id)
        return { success: true, message: `Sede asignada: ${sedeEncontrada.name}` }
      } else {
        return { success: false, message: `No encontré la sede "${valor}". Sedes disponibles: ${warehouses.value.map(w => w.name).join(', ')}` }
      }
    }
    
    const result = userModalRef.value.setFieldValue(campo, valor)
    if (result) {
      return { success: true, message: `Campo ${campo} establecido: ${campo === 'password' ? '***' : valor}` }
    }
    
    return { success: false, message: `Campo "${campo}" no reconocido. Usa: nombre, email, password, rol, sede, cedula, telefono` }
  })
  
  // Guardar usuario
  uiContextStore.registerAction('guardarUsuario', async () => {
    if (!showUserModal.value) {
      return { success: false, message: 'No hay modal de usuario abierto.' }
    }
    
    if (!userModalRef.value) {
      return { success: false, message: 'Modal no disponible.' }
    }
    
    const form = userModalRef.value.form
    
    // Validar campos requeridos (incluyendo ROL como obligatorio)
    const faltantes = []
    if (!form.name) faltantes.push('nombre')
    if (!form.email) faltantes.push('email')
    if (!form.password && !selectedUser.value) faltantes.push('contraseña') // Solo requerido para nuevos usuarios
    if (!form.role_id) faltantes.push('rol')
    
    if (faltantes.length > 0) {
      return { success: false, message: `Faltan campos obligatorios: ${faltantes.join(', ')}. El ROL es obligatorio.` }
    }
    
    try {
      // Llamar handleSubmit del modal que emite el evento save
      userModalRef.value.handleSubmit()
      return { success: true, message: `Usuario "${form.name}" ${selectedUser.value ? 'actualizándose' : 'creándose'}...` }
    } catch (err) {
      return { success: false, message: `Error al guardar usuario: ${err.message || 'Error desconocido'}` }
    }
  })
  
  // Editar usuario existente
  uiContextStore.registerAction('editarUsuario', ({ busqueda }) => {
    // Buscar usuario por nombre o email
    const usuario = users.value.find(u => 
      u.name?.toLowerCase().includes(busqueda.toLowerCase()) ||
      u.email?.toLowerCase().includes(busqueda.toLowerCase())
    )
    
    if (!usuario) {
      return { success: false, message: `No encontré usuario con "${busqueda}". Usuarios disponibles: ${users.value.slice(0, 5).map(u => u.name).join(', ')}...` }
    }
    
    // Abrir modal de edición
    openEditUserModal(usuario)
    updateAIContext()
    
    return { 
      success: true, 
      message: `Abrí el formulario para editar a "${usuario.name}". Puedes modificar los campos con llenarCampoUsuario.`,
      usuario: {
        nombre: usuario.name,
        email: usuario.email,
        rol: usuario.role?.name,
        activo: usuario.active
      }
    }
  })
  
  // Buscar usuario
  uiContextStore.registerAction('buscarUsuario', ({ texto }) => {
    const encontrados = users.value.filter(u => 
      u.name?.toLowerCase().includes(texto.toLowerCase()) ||
      u.email?.toLowerCase().includes(texto.toLowerCase()) ||
      u.document?.includes(texto)
    )
    
    if (encontrados.length === 0) {
      return { success: true, message: `No encontré usuarios con "${texto}".` }
    }
    
    return {
      success: true,
      message: `Encontré ${encontrados.length} usuario(s):`,
      usuarios: encontrados.map(u => ({
        nombre: u.name,
        email: u.email,
        cedula: u.document,
        rol: u.role?.name,
        activo: u.active
      }))
    }
  })
  
  // Ver permisos disponibles
  uiContextStore.registerAction('verPermisosDisponibles', () => {
    return {
      success: true,
      message: `Hay ${permissionsModules.value.length} módulos/permisos disponibles:`,
      permisos: permissionsModules.value.map(p => ({
        id: p.id,
        nombre: p.name,
        descripcion: p.description
      }))
    }
  })
}

// Observar cambios para actualizar contexto
watch([users, roles, showUserModal], () => {
  updateAIContext()
}, { deep: true })

// ===== LIFECYCLE =====
onMounted(() => {
  refreshData()
  registerAIActions()
  updateAIContext()
})

onUnmounted(() => {
  // Limpiar acciones registradas al salir del módulo
  uiContextStore.clearSelection()
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
  animation: fade-in 0.6s ease-out;
}
</style>
