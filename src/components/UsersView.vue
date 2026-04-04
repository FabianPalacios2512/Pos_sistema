<template>
  <!-- Gradiente oficial del sistema -->
  <div class="min-h-screen font-sans bg-gradient-to-b from-gray-50 via-gray-100 to-gray-200 dark:bg-gradient-to-b dark:from-[#141417] dark:via-slate-900 dark:to-[#0a0a0c] transition-colors duration-300 px-8">
    <div class="p-4 lg:p-6 space-y-6 pb-8 animate-fade-in">

      <!-- ═══════════════════════════════════════════════════ -->
      <!-- HEADER -->
      <!-- ═══════════════════════════════════════════════════ -->
      <div class="flex items-center justify-between pb-4">
        <div>
          <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Usuarios y Roles</h1>
          <p class="text-sm text-gray-600 dark:text-zinc-400 mt-1">Panel de gestión, rendimiento y auditoría de empleados</p>
        </div>
        <div class="flex items-center gap-3">
          <!-- Plan badge -->
          <span v-if="planLimits" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-bold border uppercase tracking-wide"
                :class="planBadgeClass">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            {{ totalUsers }}/{{ planLimits?.max_users ?? '∞' }} usuarios
          </span>
          <!-- Refresh -->
          <button @click="refreshAll"
                  class="p-2.5 bg-white dark:bg-zinc-900 hover:bg-slate-50 dark:hover:bg-zinc-800 text-slate-600 dark:text-zinc-200 rounded-xl border border-slate-200 dark:border-zinc-800 shadow-sm transition-all duration-200"
                  :class="{ 'animate-spin': loading }">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
          </button>
          <!-- New User -->
          <button @click="openCreateModal"
                  class="px-6 py-2.5 bg-slate-900 dark:bg-slate-700 hover:bg-black dark:hover:bg-slate-600 text-white text-sm font-bold rounded-xl shadow-lg shadow-slate-400/40 dark:shadow-slate-900/50 transition-all duration-300 flex items-center gap-2 hover:-translate-y-0.5">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
            Nuevo Usuario
          </button>
        </div>
      </div>

      <!-- ═══════════════════════════════════════════════════ -->
      <!-- TABS: Usuarios | Roles -->
      <!-- ═══════════════════════════════════════════════════ -->
      <div class="flex items-center gap-1 bg-gray-50 dark:bg-[#252530] rounded-xl p-1 border border-gray-200 dark:border-zinc-700/60 w-fit">
        <button @click="activeTab = 'users'"
                :class="activeTab === 'users'
                  ? 'bg-white dark:bg-[#2a2a35] text-gray-900 dark:text-white shadow-sm'
                  : 'text-gray-500 dark:text-zinc-400 hover:text-gray-900 dark:hover:text-white'"
                class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold transition-all duration-200">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
          Usuarios
          <span class="px-1.5 py-0.5 rounded-md text-xs font-bold bg-gray-200 dark:bg-zinc-700 text-gray-700 dark:text-zinc-300">{{ totalUsers }}</span>
        </button>
        <button @click="activeTab = 'roles'"
                :class="activeTab === 'roles'
                  ? 'bg-white dark:bg-[#2a2a35] text-gray-900 dark:text-white shadow-sm'
                  : 'text-gray-500 dark:text-zinc-400 hover:text-gray-900 dark:hover:text-white'"
                class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold transition-all duration-200">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
          Roles
          <span class="px-1.5 py-0.5 rounded-md text-xs font-bold bg-gray-200 dark:bg-zinc-700 text-gray-700 dark:text-zinc-300">{{ availableRoles.length }}</span>
        </button>
        <button @click="activeTab = 'attendance'"
                :class="activeTab === 'attendance'
                  ? 'bg-white dark:bg-[#2a2a35] text-gray-900 dark:text-white shadow-sm'
                  : 'text-gray-500 dark:text-zinc-400 hover:text-gray-900 dark:hover:text-white'"
                class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold transition-all duration-200">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
          Punteo
        </button>
      </div>

      <!-- ═══════════════════════════════════════════════════ -->
      <!-- TAB: USUARIOS -->
      <!-- ═══════════════════════════════════════════════════ -->
      <template v-if="activeTab === 'users'">

        <!-- KPIs Operativos -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
          <!-- Personal Activo Ahora -->
          <div class="bg-white dark:bg-zinc-900/80 backdrop-blur-sm rounded-xl px-4 py-3 border border-gray-300 dark:border-zinc-800/60 hover:border-gray-400 dark:hover:border-zinc-700/80 transition-all duration-200 hover:shadow-md dark:shadow-lg dark:shadow-black/30">
            <div class="flex items-center gap-3">
              <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0 bg-gray-100 dark:bg-zinc-800/50 border border-gray-200 dark:border-white/5">
                <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
              </div>
              <div class="flex-1 min-w-0">
                <p class="text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wide">Personal Activo Ahora</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white mt-0.5">{{ kpis.active_now_count ?? 0 }}</p>
              </div>
              <!-- Active users indicator dots -->
              <div v-if="kpis.active_now?.length" class="flex -space-x-1">
                <div v-for="(u, i) in kpis.active_now.slice(0, 3)" :key="i"
                     class="w-6 h-6 rounded-full bg-emerald-100 dark:bg-emerald-900/50 border-2 border-white dark:border-zinc-900 flex items-center justify-center text-[9px] font-bold text-emerald-700 dark:text-emerald-400">
                  {{ u.user_name?.charAt(0) }}
                </div>
              </div>
            </div>
          </div>

          <!-- Ventas Totales Hoy -->
          <div class="bg-white dark:bg-zinc-900/80 backdrop-blur-sm rounded-xl px-4 py-3 border border-gray-300 dark:border-zinc-800/60 hover:border-gray-400 dark:hover:border-zinc-700/80 transition-all duration-200 hover:shadow-md dark:shadow-lg dark:shadow-black/30">
            <div class="flex items-center gap-3">
              <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0 bg-gray-100 dark:bg-zinc-800/50 border border-gray-200 dark:border-white/5">
                <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1H9m3 0h3"/></svg>
              </div>
              <div class="flex-1 min-w-0">
                <p class="text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wide">Ventas Hoy (equipo)</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white mt-0.5">${{ formatMoney(kpis.sales_today ?? 0) }}</p>
                <p class="text-xs text-gray-500 dark:text-zinc-500">{{ kpis.sales_count_today ?? 0 }} facturas</p>
              </div>
            </div>
          </div>

          <!-- Alertas Devoluciones -->
          <div class="bg-white dark:bg-zinc-900/80 backdrop-blur-sm rounded-xl px-4 py-3 border border-gray-300 dark:border-zinc-800/60 hover:border-gray-400 dark:hover:border-zinc-700/80 transition-all duration-200 hover:shadow-md dark:shadow-lg dark:shadow-black/30"
               :class="{ 'border-amber-300 dark:border-amber-800/60': kpis.return_alerts?.length > 0 }">
            <div class="flex items-center gap-3">
              <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0 bg-gray-100 dark:bg-zinc-800/50 border border-gray-200 dark:border-white/5"
                   :class="{ 'bg-amber-50 dark:bg-amber-950 border-amber-200 dark:border-amber-800': kpis.return_alerts?.length > 0 }">
                <svg class="w-5 h-5 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
              </div>
              <div class="flex-1 min-w-0">
                <p class="text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wide">Alertas Devoluciones</p>
                <p class="text-2xl font-bold mt-0.5" :class="kpis.return_alerts?.length > 0 ? 'text-amber-600 dark:text-amber-400' : 'text-gray-900 dark:text-white'">
                  {{ kpis.returns_today_count ?? 0 }}
                </p>
                <p v-if="kpis.return_alerts?.length > 0" class="text-xs text-amber-600 dark:text-amber-400 font-medium">
                  {{ kpis.return_alerts.length }} usuario(s) con exceso
                </p>
                <p v-else class="text-xs text-gray-500 dark:text-zinc-500">${{ formatMoney(kpis.returns_today_amount ?? 0) }}</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Filtros -->
        <div class="flex flex-wrap items-center gap-3">
          <div class="flex-1 min-w-[280px] relative">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 dark:text-zinc-500" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path stroke-linecap="round" d="m21 21-4.35-4.35"/></svg>
            <input v-model="searchTerm" type="text" placeholder="Buscar por nombre, email, cédula..."
                   class="w-full pl-10 pr-10 py-3 text-sm rounded-xl border-2 border-gray-200 dark:border-zinc-700 bg-gray-50 dark:bg-zinc-800 text-gray-900 dark:text-zinc-200 placeholder-gray-400 dark:placeholder-zinc-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent">
            <button v-if="searchTerm" @click="searchTerm = ''"
                    class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 dark:text-zinc-500 dark:hover:text-zinc-300">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
          </div>
          <select v-model="roleFilter" class="px-3 py-3 text-sm rounded-lg border border-gray-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 text-gray-700 dark:text-zinc-300 font-medium focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400">
            <option value="">Todos los roles</option>
            <option v-for="role in availableRoles" :key="role.id" :value="role.id">{{ role.name }}</option>
          </select>
          <select v-model="statusFilter" class="px-3 py-3 text-sm rounded-lg border border-gray-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 text-gray-700 dark:text-zinc-300 font-medium focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400">
            <option value="">Todos los estados</option>
            <option value="active">Activos</option>
            <option value="inactive">Inactivos</option>
          </select>
          <button v-if="searchTerm || roleFilter || statusFilter" @click="clearFilters"
                  class="p-3 text-gray-500 dark:text-zinc-400 hover:text-red-600 dark:hover:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-xl border border-transparent hover:border-red-100 dark:hover:border-red-900/30 transition-all">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
          </button>
        </div>

        <!-- Tabla Principal de Usuarios -->
        <div class="bg-white dark:bg-zinc-900 rounded-2xl shadow-xl dark:shadow-black/50 border border-gray-300 dark:border-zinc-800 overflow-hidden">
          <!-- Loading -->
          <div v-if="loading" class="flex items-center justify-center py-16">
            <svg class="animate-spin w-8 h-8 text-slate-400" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
          </div>

          <div v-else class="overflow-x-auto">
            <table class="w-full">
              <thead>
                <tr class="border-b border-gray-200 dark:border-zinc-800 bg-gray-50 dark:bg-zinc-900">
                  <th class="px-6 py-3.5 text-left text-[11px] font-semibold text-gray-600 dark:text-zinc-400 uppercase tracking-wider">Usuario</th>
                  <th class="px-4 py-3.5 text-left text-[11px] font-semibold text-gray-600 dark:text-zinc-400 uppercase tracking-wider">Rol</th>
                  <th class="px-4 py-3.5 text-left text-[11px] font-semibold text-gray-600 dark:text-zinc-400 uppercase tracking-wider">
                    <span class="flex items-center gap-1">
                      Sede Actual
                      <svg v-if="!planLimits.multi_sede" class="w-3 h-3 text-amber-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/></svg>
                    </span>
                  </th>
                  <th class="px-4 py-3.5 text-left text-[11px] font-semibold text-gray-600 dark:text-zinc-400 uppercase tracking-wider">Último Ingreso</th>
                  <th class="px-4 py-3.5 text-right text-[11px] font-semibold text-gray-600 dark:text-zinc-400 uppercase tracking-wider">Ventas Hoy</th>
                  <th class="px-4 py-3.5 text-center text-[11px] font-semibold text-gray-600 dark:text-zinc-400 uppercase tracking-wider">Estado Caja</th>
                  <th class="px-4 py-3.5 text-center text-[11px] font-semibold text-gray-600 dark:text-zinc-400 uppercase tracking-wider">Estado</th>
                  <th class="px-4 py-3.5 text-right text-[11px] font-semibold text-gray-600 dark:text-zinc-400 uppercase tracking-wider">Acciones</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-100 dark:divide-zinc-800">
                <tr v-for="user in filteredUsers" :key="user.id"
                    class="group hover:bg-gray-50 dark:hover:bg-zinc-800/50 transition-colors duration-200 cursor-pointer"
                    @click="openUserDetail(user)">
                  <!-- Usuario -->
                  <td class="px-6 py-4">
                    <div class="flex items-center gap-3">
                      <div class="w-10 h-10 rounded-lg flex items-center justify-center text-white font-bold text-sm flex-shrink-0"
                           :class="getAvatarBg(user.role?.name)">
                        {{ user.name?.charAt(0).toUpperCase() }}
                      </div>
                      <div class="min-w-0">
                        <p class="text-sm font-semibold text-gray-900 dark:text-white truncate">{{ user.name }}</p>
                        <p class="text-xs text-gray-500 dark:text-zinc-400 truncate">{{ user.email }}</p>
                      </div>
                    </div>
                  </td>
                  <!-- Rol -->
                  <td class="px-4 py-4">
                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold border uppercase tracking-wide"
                          :class="getRoleBadge(user.role?.name)">
                      {{ user.role?.name || 'Sin rol' }}
                    </span>
                  </td>
                  <!-- Sede Actual -->
                  <td class="px-4 py-4" @click.stop>
                    <template v-if="planLimits.multi_sede">
                      <span v-if="user.current_warehouse" class="text-sm text-gray-700 dark:text-zinc-300">{{ user.current_warehouse }}</span>
                      <span v-else class="text-xs text-gray-400 dark:text-zinc-500">—</span>
                    </template>
                    <template v-else>
                      <span class="inline-flex items-center gap-1 text-xs text-amber-600 dark:text-amber-400 font-medium opacity-80"
                            title="Función Multi-Sede disponible en Plan Premium">
                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/></svg>
                        Plan Pro
                      </span>
                    </template>
                  </td>
                  <!-- Último Ingreso -->
                  <td class="px-4 py-4">
                    <div v-if="user.last_login">
                      <p class="text-sm text-gray-700 dark:text-zinc-300">{{ formatDate(user.last_login) }}</p>
                      <p class="text-xs text-gray-400 dark:text-zinc-500">{{ formatTime(user.last_login) }}</p>
                    </div>
                    <span v-else class="text-xs text-gray-400 dark:text-zinc-500">Nunca</span>
                  </td>
                  <!-- Ventas Hoy -->
                  <td class="px-4 py-4 text-right">
                    <p class="text-sm font-semibold" :class="user.sales_today > 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-gray-300 dark:text-zinc-600'">
                      ${{ formatMoney(user.sales_today ?? 0) }}
                    </p>
                    <p v-if="user.sales_count_today > 0" class="text-xs text-gray-400 dark:text-zinc-500">{{ user.sales_count_today }} ventas</p>
                  </td>
                  <!-- Estado Caja -->
                  <td class="px-4 py-4 text-center">
                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold border uppercase tracking-wide"
                          :class="user.cash_status === 'open'
                            ? 'bg-emerald-50 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-400 border-emerald-100 dark:border-emerald-800'
                            : 'bg-gray-50 dark:bg-zinc-800 text-gray-500 dark:text-zinc-500 border-gray-200 dark:border-zinc-700'">
                      {{ user.cash_status === 'open' ? 'Abierta' : 'Cerrada' }}
                    </span>
                  </td>
                  <!-- Estado -->
                  <td class="px-4 py-4 text-center">
                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold border uppercase tracking-wide"
                          :class="user.active
                            ? 'bg-emerald-50 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-400 border-emerald-100 dark:border-emerald-800'
                            : 'bg-rose-50 dark:bg-rose-950 text-rose-700 dark:text-rose-400 border-rose-100 dark:border-rose-800'">
                      {{ user.active ? 'Activo' : 'Inactivo' }}
                    </span>
                  </td>
                  <!-- Acciones -->
                  <td class="px-4 py-4 text-right" @click.stop>
                    <div class="flex items-center justify-end gap-1">
                      <!-- Ver perfil -->
                      <button @click.stop="openUserDetail(user)"
                              class="p-2 rounded-lg border border-transparent text-slate-400 dark:text-zinc-400 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/20 hover:border-blue-100 dark:hover:border-blue-900/30 transition-all"
                              title="Ver expediente">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                      </button>
                      <!-- Editar -->
                      <button @click.stop="editUser(user)"
                              class="p-2 rounded-lg border border-transparent text-slate-400 dark:text-zinc-400 hover:text-amber-600 dark:hover:text-amber-400 hover:bg-amber-50 dark:hover:bg-amber-900/20 hover:border-amber-100 dark:hover:border-amber-900/30 transition-all"
                              title="Editar">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                      </button>
                      <!-- Enrolar biométrico -->
                      <button @click.stop="openEnrollModal(user)"
                              class="p-2 rounded-lg border border-transparent text-slate-400 dark:text-zinc-400 hover:text-indigo-600 dark:hover:text-indigo-400 hover:bg-indigo-50 dark:hover:bg-indigo-900/20 hover:border-indigo-100 dark:hover:border-indigo-900/30 transition-all"
                              title="Enrolamiento biométrico">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                      </button>
                      <!-- Toggle active -->
                      <button @click.stop="toggleUserStatus(user)"
                              class="p-2 rounded-lg border border-transparent transition-all"
                              :class="user.active
                                ? 'text-slate-400 dark:text-zinc-500 hover:text-rose-600 dark:hover:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-900/20 hover:border-rose-100 dark:hover:border-rose-900/30'
                                : 'text-slate-400 dark:text-zinc-500 hover:text-emerald-600 dark:hover:text-emerald-400 hover:bg-emerald-50 dark:hover:bg-emerald-900/20 hover:border-emerald-100 dark:hover:border-emerald-900/30'"
                              :title="user.active ? 'Desactivar' : 'Activar'">
                        <svg v-if="user.active" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                        <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                      </button>
                    </div>
                  </td>
                </tr>
                <!-- Empty state -->
                <tr v-if="filteredUsers.length === 0">
                  <td colspan="8" class="px-6 py-16 text-center">
                    <p class="text-gray-400 dark:text-zinc-500 text-sm">No se encontraron usuarios</p>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </template>

      <!-- ═══════════════════════════════════════════════════ -->
      <!-- TAB: ROLES -->
      <!-- ═══════════════════════════════════════════════════ -->
      <template v-if="activeTab === 'roles'">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
          <div v-for="role in availableRoles" :key="role.id"
               class="bg-white dark:bg-zinc-900 rounded-2xl shadow-xl dark:shadow-black/50 border border-gray-300 dark:border-zinc-800 p-5 hover:border-gray-400 dark:hover:border-zinc-700 transition-all">
            <div class="flex items-center gap-3 mb-4">
              <div class="w-10 h-10 rounded-xl flex items-center justify-center bg-gray-100 dark:bg-zinc-800/50 border border-gray-200 dark:border-white/5">
                <svg class="w-5 h-5 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
              </div>
              <div>
                <h4 class="font-bold text-gray-900 dark:text-white text-sm">{{ role.name }}</h4>
                <p class="text-xs text-gray-500 dark:text-zinc-400">{{ role.description || 'Sin descripción' }}</p>
              </div>
            </div>
            <div class="flex flex-wrap gap-1">
              <span v-for="perm in (role.permissions || []).slice(0, 5)" :key="perm"
                    class="px-2 py-0.5 rounded-md text-[10px] font-medium bg-indigo-50 dark:bg-indigo-950 text-indigo-700 dark:text-indigo-400 border border-indigo-100 dark:border-indigo-800">
                {{ perm }}
              </span>
              <span v-if="role.permissions?.length > 5"
                    class="px-2 py-0.5 rounded-md text-[10px] font-medium bg-gray-50 dark:bg-zinc-800 text-gray-500 dark:text-zinc-400 border border-gray-200 dark:border-zinc-700">
                +{{ role.permissions.length - 5 }}
              </span>
            </div>
          </div>
        </div>
      </template>

      <!-- ═══════════════════════════════════════════════════ -->
      <!-- TAB: PUNTEO DE JORNADA -->
      <!-- ═══════════════════════════════════════════════════ -->
      <template v-if="activeTab === 'attendance'">
        <AttendanceCheckView />
      </template>

    </div>

    <!-- ═══════════════════════════════════════════════════════════ -->
    <!-- MODAL: Enrolamiento Biométrico -->
    <!-- ═══════════════════════════════════════════════════════════ -->
    <BiometricEnrollModal
      :visible="showEnrollModal"
      :userId="enrollUserId"
      :userName="enrollUserName"
      @close="closeEnrollModal"
      @enrolled="onEnrolled"
    />

    <!-- ═══════════════════════════════════════════════════════════ -->
    <!-- PANEL LATERAL: Expediente del Trabajador -->
    <!-- ═══════════════════════════════════════════════════════════ -->
    <Transition name="slide">
      <div v-if="selectedUser" class="fixed inset-0 z-50 flex justify-end">
        <!-- Overlay -->
        <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" @click="closeUserDetail"></div>
        <!-- Panel -->
        <div class="relative w-full max-w-2xl bg-white dark:bg-[#18181c] shadow-2xl overflow-y-auto">
          <div class="p-6 space-y-6">

            <!-- Panel Header -->
            <div class="flex items-start justify-between">
              <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-xl flex items-center justify-center text-white font-bold text-xl"
                     :class="getAvatarBg(selectedUser.role?.name)">
                  {{ selectedUser.name?.charAt(0).toUpperCase() }}
                </div>
                <div>
                  <h2 class="text-xl font-bold text-gray-900 dark:text-white">{{ selectedUser.name }}</h2>
                  <p class="text-sm text-gray-500 dark:text-zinc-400">{{ selectedUser.email }}</p>
                  <div class="flex items-center gap-2 mt-1">
                    <span class="px-2 py-0.5 rounded-md text-[10px] font-bold border uppercase tracking-wide"
                          :class="getRoleBadge(selectedUser.role?.name)">
                      {{ selectedUser.role?.name || 'Sin rol' }}
                    </span>
                    <span class="px-2 py-0.5 rounded-md text-[10px] font-bold border uppercase tracking-wide"
                          :class="selectedUser.active
                            ? 'bg-emerald-50 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-400 border-emerald-100 dark:border-emerald-800'
                            : 'bg-rose-50 dark:bg-rose-950 text-rose-700 dark:text-rose-400 border-rose-100 dark:border-rose-800'">
                      {{ selectedUser.active ? 'Activo' : 'Inactivo' }}
                    </span>
                  </div>
                </div>
              </div>
              <button @click="closeUserDetail"
                      class="p-2 rounded-lg text-gray-400 dark:text-zinc-500 hover:text-gray-600 dark:hover:text-zinc-300 hover:bg-gray-100 dark:hover:bg-zinc-800 transition-all">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" d="M6 18L18 6M6 6l12 12"/></svg>
              </button>
            </div>

            <!-- Info rápida -->
            <div class="grid grid-cols-2 gap-3">
              <div class="bg-gray-50 dark:bg-zinc-900 rounded-xl p-3 border border-gray-200 dark:border-zinc-800">
                <p class="text-[10px] font-medium text-gray-500 dark:text-zinc-500 uppercase tracking-wide">Cédula</p>
                <p class="text-sm font-semibold text-gray-900 dark:text-white mt-0.5">{{ selectedUser.cc || '—' }}</p>
              </div>
              <div class="bg-gray-50 dark:bg-zinc-900 rounded-xl p-3 border border-gray-200 dark:border-zinc-800">
                <p class="text-[10px] font-medium text-gray-500 dark:text-zinc-500 uppercase tracking-wide">Teléfono</p>
                <p class="text-sm font-semibold text-gray-900 dark:text-white mt-0.5">{{ selectedUser.phone || '—' }}</p>
              </div>
            </div>

            <!-- ═══════════════════ Rendimiento del Mes ═══════════════════ -->
            <div>
              <h3 class="text-sm font-bold text-gray-900 dark:text-white mb-3 flex items-center gap-2">
                <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                Rendimiento del Mes
              </h3>

              <template v-if="planLimits.performance_metrics">
                <div v-if="profileLoading" class="flex items-center justify-center py-8">
                  <svg class="animate-spin w-6 h-6 text-slate-400" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                </div>
                <div v-else class="grid grid-cols-2 gap-3">
                  <!-- Total vendido -->
                  <div class="bg-white dark:bg-zinc-900/80 rounded-xl p-3 border border-gray-200 dark:border-zinc-800">
                    <p class="text-[10px] font-medium text-gray-500 dark:text-zinc-500 uppercase tracking-wide">Total Vendido</p>
                    <p class="text-lg font-bold text-emerald-600 dark:text-emerald-400 mt-0.5">${{ formatMoney(userProfile?.performance?.total_sold_month ?? 0) }}</p>
                  </div>
                  <!-- Ticket Promedio -->
                  <div class="bg-white dark:bg-zinc-900/80 rounded-xl p-3 border border-gray-200 dark:border-zinc-800">
                    <p class="text-[10px] font-medium text-gray-500 dark:text-zinc-500 uppercase tracking-wide">Ticket Promedio</p>
                    <p class="text-lg font-bold text-blue-600 dark:text-blue-400 mt-0.5">${{ formatMoney(userProfile?.performance?.avg_ticket ?? 0) }}</p>
                  </div>
                  <!-- Facturas Emitidas -->
                  <div class="bg-white dark:bg-zinc-900/80 rounded-xl p-3 border border-gray-200 dark:border-zinc-800">
                    <p class="text-[10px] font-medium text-gray-500 dark:text-zinc-500 uppercase tracking-wide">Facturas Emitidas</p>
                    <p class="text-lg font-bold text-gray-900 dark:text-white mt-0.5">{{ userProfile?.performance?.invoices_count ?? 0 }}</p>
                  </div>
                  <!-- Devoluciones -->
                  <div class="bg-white dark:bg-zinc-900/80 rounded-xl p-3 border border-gray-200 dark:border-zinc-800">
                    <p class="text-[10px] font-medium text-gray-500 dark:text-zinc-500 uppercase tracking-wide">Devoluciones</p>
                    <p class="text-lg font-bold mt-0.5" :class="(userProfile?.performance?.returns_count ?? 0) > 0 ? 'text-amber-600 dark:text-amber-400' : 'text-gray-300 dark:text-zinc-600'">
                      {{ userProfile?.performance?.returns_count ?? 0 }}
                    </p>
                    <p v-if="userProfile?.performance?.returns_amount > 0" class="text-xs text-amber-500 dark:text-amber-400">${{ formatMoney(userProfile?.performance?.returns_amount) }}</p>
                  </div>
                  <!-- Sesiones / Descuadres -->
                  <div class="bg-white dark:bg-zinc-900/80 rounded-xl p-3 border border-gray-200 dark:border-zinc-800 col-span-2">
                    <div class="flex items-center justify-between">
                      <div>
                        <p class="text-[10px] font-medium text-gray-500 dark:text-zinc-500 uppercase tracking-wide">Sesiones de Caja / Descuadres</p>
                        <p class="text-lg font-bold text-gray-900 dark:text-white mt-0.5">
                          {{ userProfile?.performance?.cash_sessions_count ?? 0 }}
                          <span v-if="userProfile?.performance?.discrepancies > 0" class="text-sm font-bold text-rose-500 dark:text-rose-400 ml-2">
                            {{ userProfile?.performance?.discrepancies }} descuadre(s)
                          </span>
                        </p>
                      </div>
                    </div>
                  </div>
                </div>
              </template>

              <!-- SaaS Lock: Performance Metrics -->
              <template v-else>
                <div class="bg-gray-50 dark:bg-zinc-900 rounded-xl p-6 border border-gray-200 dark:border-zinc-800 text-center">
                  <svg class="w-8 h-8 text-amber-400 mx-auto mb-2" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/></svg>
                  <p class="text-sm font-semibold text-gray-700 dark:text-zinc-300">Métricas de Rendimiento</p>
                  <p class="text-xs text-gray-500 dark:text-zinc-500 mt-1">Disponible en el Plan Básico o superior</p>
                  <button class="mt-3 px-4 py-1.5 bg-amber-50 dark:bg-amber-950 text-amber-700 dark:text-amber-400 text-xs font-bold rounded-lg border border-amber-200 dark:border-amber-800 hover:bg-amber-100 dark:hover:bg-amber-900 transition-all">
                    Mejorar Plan
                  </button>
                </div>
              </template>
            </div>

            <!-- ═══════════════════ Gestión Multi-Sede ═══════════════════ -->
            <div v-if="planLimits.multi_sede && userProfile?.current_session">
              <h3 class="text-sm font-bold text-gray-900 dark:text-white mb-3 flex items-center gap-2">
                <svg class="w-4 h-4 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                Traslado de Sede
              </h3>
              <div class="bg-white dark:bg-zinc-900/80 rounded-xl p-4 border border-gray-200 dark:border-zinc-800">
                <div class="flex items-center gap-3">
                  <div class="flex-1">
                    <p class="text-xs text-gray-500 dark:text-zinc-500 mb-1">Sede actual: <span class="font-semibold text-gray-700 dark:text-zinc-300">{{ userProfile?.current_session?.warehouse ?? 'Principal' }}</span></p>
                    <select v-model="transferWarehouseId"
                            class="w-full px-3 py-2 text-sm border border-gray-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-gray-700 dark:text-zinc-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                      <option value="">Seleccionar nueva sede...</option>
                      <option v-for="wh in userProfile?.all_warehouses" :key="wh.id" :value="wh.id"
                              :disabled="wh.id === userProfile?.current_session?.warehouse_id">
                        {{ wh.name }} {{ wh.address ? '- ' + wh.address : '' }}
                      </option>
                    </select>
                  </div>
                  <button @click="transferSede"
                          :disabled="!transferWarehouseId || transferring"
                          class="px-4 py-2 bg-indigo-600 dark:bg-indigo-700 hover:bg-indigo-700 dark:hover:bg-indigo-600 disabled:opacity-40 disabled:cursor-not-allowed text-white text-sm font-bold rounded-lg transition-all">
                    {{ transferring ? 'Transfiriendo...' : 'Transferir' }}
                  </button>
                </div>
              </div>
            </div>

            <!-- SaaS Lock: Multi-Sede -->
            <div v-if="!planLimits.multi_sede">
              <h3 class="text-sm font-bold text-gray-900 dark:text-white mb-3 flex items-center gap-2">
                <svg class="w-4 h-4 text-gray-400 dark:text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                Gestión Multi-Sede
              </h3>
              <div class="bg-gray-50 dark:bg-zinc-900 rounded-xl p-6 border border-gray-200 dark:border-zinc-800 text-center">
                <svg class="w-8 h-8 text-amber-400 mx-auto mb-2" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/></svg>
                <p class="text-sm font-semibold text-gray-700 dark:text-zinc-300">Función Multi-Sede</p>
                <p class="text-xs text-gray-500 dark:text-zinc-500 mt-1">Traslada empleados entre sedes en tiempo real</p>
                <button class="mt-3 px-4 py-1.5 bg-amber-50 dark:bg-amber-950 text-amber-700 dark:text-amber-400 text-xs font-bold rounded-lg border border-amber-200 dark:border-amber-800 hover:bg-amber-100 dark:hover:bg-amber-900 transition-all">
                  Disponible en Plan Premium
                </button>
              </div>
            </div>

            <!-- ═══════════════════ Historial de Auditoría ═══════════════════ -->
            <div>
              <h3 class="text-sm font-bold text-gray-900 dark:text-white mb-3 flex items-center gap-2">
                <svg class="w-4 h-4 text-slate-600 dark:text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Historial de Auditoría
                <input type="date" v-model="timelineDate" @change="loadTimeline"
                       class="ml-auto px-2 py-1 text-xs border border-gray-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-gray-700 dark:text-zinc-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
              </h3>

              <template v-if="planLimits.audit_timeline">
                <div v-if="timelineLoading" class="flex items-center justify-center py-8">
                  <svg class="animate-spin w-6 h-6 text-slate-400" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                </div>
                <div v-else-if="timeline.length === 0" class="bg-gray-50 dark:bg-zinc-900 rounded-xl p-6 border border-gray-200 dark:border-zinc-800 text-center">
                  <p class="text-sm text-gray-400 dark:text-zinc-500">Sin actividad registrada para esta fecha</p>
                </div>
                <div v-else class="relative pl-6 space-y-0">
                  <!-- Timeline line -->
                  <div class="absolute left-[11px] top-2 bottom-2 w-px bg-gray-200 dark:bg-zinc-700"></div>

                  <div v-for="(event, index) in timeline" :key="index" class="relative pb-4 last:pb-0">
                    <!-- Dot -->
                    <div class="absolute -left-6 top-1 w-[10px] h-[10px] rounded-full border-2"
                         :class="getTimelineDotClass(event.color)"></div>
                    <!-- Content -->
                    <div class="bg-white dark:bg-zinc-900/80 rounded-lg p-3 border border-gray-100 dark:border-zinc-800 ml-2">
                      <div class="flex items-center justify-between">
                        <p class="text-sm font-semibold" :class="getTimelineTextClass(event.color)">{{ event.title }}</p>
                        <span class="text-[10px] font-mono text-gray-400 dark:text-zinc-500">{{ formatTimelineTime(event.timestamp) }}</span>
                      </div>
                      <p class="text-xs text-gray-500 dark:text-zinc-400 mt-0.5">{{ event.description }}</p>
                    </div>
                  </div>
                </div>
              </template>

              <!-- SaaS Lock: Audit Timeline -->
              <template v-else>
                <div class="bg-gray-50 dark:bg-zinc-900 rounded-xl p-6 border border-gray-200 dark:border-zinc-800 text-center">
                  <svg class="w-8 h-8 text-amber-400 mx-auto mb-2" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/></svg>
                  <p class="text-sm font-semibold text-gray-700 dark:text-zinc-300">Historial de Auditoría</p>
                  <p class="text-xs text-gray-500 dark:text-zinc-500 mt-1">Seguimiento detallado de actividad por empleado</p>
                  <button class="mt-3 px-4 py-1.5 bg-amber-50 dark:bg-amber-950 text-amber-700 dark:text-amber-400 text-xs font-bold rounded-lg border border-amber-200 dark:border-amber-800 hover:bg-amber-100 dark:hover:bg-amber-900 transition-all">
                    Disponible en Plan Básico
                  </button>
                </div>
              </template>
            </div>

          </div>
        </div>
      </div>
    </Transition>

    <!-- ═══════════════════════════════════════════════════════════ -->
    <!-- MODAL: Crear/Editar Usuario -->
    <!-- ═══════════════════════════════════════════════════════════ -->
    <Transition name="fade">
      <div v-if="showUserModal" class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" @click="closeUserModal"></div>
        <div class="relative bg-white dark:bg-[#1e1e24] rounded-2xl p-6 max-w-lg w-full shadow-2xl dark:shadow-black/50 border border-gray-200 dark:border-zinc-800">
          <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-5">
            {{ editingUser ? 'Editar Usuario' : 'Nuevo Usuario' }}
          </h3>

          <div class="space-y-4">
            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="block text-xs font-semibold text-gray-600 dark:text-zinc-400 mb-1.5">Nombre Completo *</label>
                <input v-model="userForm.name" type="text" placeholder="Nombre completo"
                       class="w-full px-3 py-2.5 border border-gray-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-gray-900 dark:text-zinc-200 placeholder-gray-400 dark:placeholder-zinc-500 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
              </div>
              <div>
                <label class="block text-xs font-semibold text-gray-600 dark:text-zinc-400 mb-1.5">Cédula *</label>
                <input v-model="userForm.cc" type="text" placeholder="Número de cédula"
                       class="w-full px-3 py-2.5 border border-gray-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-gray-900 dark:text-zinc-200 placeholder-gray-400 dark:placeholder-zinc-500 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
              </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="block text-xs font-semibold text-gray-600 dark:text-zinc-400 mb-1.5">Email *</label>
                <input v-model="userForm.email" type="email" placeholder="correo@ejemplo.com"
                       class="w-full px-3 py-2.5 border border-gray-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-gray-900 dark:text-zinc-200 placeholder-gray-400 dark:placeholder-zinc-500 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
              </div>
              <div>
                <label class="block text-xs font-semibold text-gray-600 dark:text-zinc-400 mb-1.5">Teléfono</label>
                <input v-model="userForm.phone" type="text" placeholder="+57 300 000 0000"
                       class="w-full px-3 py-2.5 border border-gray-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-gray-900 dark:text-zinc-200 placeholder-gray-400 dark:placeholder-zinc-500 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
              </div>
            </div>
            <div>
              <label class="block text-xs font-semibold text-gray-600 dark:text-zinc-400 mb-1.5">Rol *</label>
              <select v-model="userForm.role_id"
                      class="w-full px-3 py-2.5 border border-gray-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-gray-900 dark:text-zinc-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                <option value="">Seleccionar rol...</option>
                <option v-for="role in availableRoles" :key="role.id" :value="role.id">{{ role.name }}</option>
              </select>
            </div>
            <div v-if="!editingUser">
              <label class="block text-xs font-semibold text-gray-600 dark:text-zinc-400 mb-1.5">Contraseña *</label>
              <input v-model="userForm.password" type="password" placeholder="Mínimo 6 caracteres"
                     class="w-full px-3 py-2.5 border border-gray-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-gray-900 dark:text-zinc-200 placeholder-gray-400 dark:placeholder-zinc-500 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>
            <!-- Active toggle -->
            <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-zinc-900 rounded-lg border border-gray-200 dark:border-zinc-800">
              <span class="text-sm font-medium text-gray-700 dark:text-zinc-300">Usuario activo</span>
              <button @click="userForm.active = !userForm.active" type="button"
                      :class="userForm.active ? 'bg-emerald-500' : 'bg-gray-300 dark:bg-zinc-600'"
                      class="relative w-10 h-5 rounded-full transition-colors duration-200 flex-shrink-0">
                <span :class="userForm.active ? 'translate-x-5' : 'translate-x-0.5'"
                      class="absolute top-0.5 w-4 h-4 bg-white rounded-full shadow transition-transform duration-200"></span>
              </button>
            </div>
          </div>

          <div class="flex items-center gap-3 mt-6">
            <button @click="closeUserModal"
                    class="flex-1 px-4 py-2.5 bg-white dark:bg-zinc-900 hover:bg-slate-50 dark:hover:bg-zinc-800 text-slate-600 dark:text-zinc-200 text-sm font-bold rounded-xl border border-slate-200 dark:border-zinc-800 transition-all">
              Cancelar
            </button>
            <button @click="saveUser" :disabled="saving"
                    class="flex-1 px-4 py-2.5 bg-slate-900 dark:bg-slate-700 hover:bg-black dark:hover:bg-slate-600 disabled:opacity-50 text-white text-sm font-bold rounded-xl shadow-lg shadow-slate-400/40 dark:shadow-slate-900/50 transition-all">
              {{ saving ? 'Guardando...' : (editingUser ? 'Actualizar' : 'Crear') }}
            </button>
          </div>
        </div>
      </div>
    </Transition>

    <!-- Toast notification -->
    <Transition name="fade">
      <div v-if="toast.show" class="fixed bottom-6 right-6 z-[60] max-w-sm">
        <div class="rounded-xl p-4 shadow-xl border" :class="toast.type === 'success'
              ? 'bg-emerald-50 dark:bg-emerald-950 border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-300'
              : 'bg-rose-50 dark:bg-rose-950 border-rose-200 dark:border-rose-800 text-rose-800 dark:text-rose-300'">
          <p class="text-sm font-medium">{{ toast.message }}</p>
        </div>
      </div>
    </Transition>

  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import usersService from '../services/usersService.js'
import rolesService from '../services/rolesService.js'
import { appStore } from '../store/appStore.js'
import BiometricEnrollModal from './BiometricEnrollModal.vue'
import AttendanceCheckView from './AttendanceCheckView.vue'

// ═══════════════════════════════════════
// State
// ═══════════════════════════════════════
const activeTab = ref('users')
const loading = ref(false)
const saving = ref(false)
const profileLoading = ref(false)
const timelineLoading = ref(false)
const transferring = ref(false)

const users = ref([])
const availableRoles = ref([])
const kpis = ref({})
const planLimits = ref({
  max_users: 2,
  max_warehouses: 1,
  multi_sede: false,
  audit_timeline: false,
  performance_metrics: false,
})

// Filters
const searchTerm = ref('')
const roleFilter = ref('')
const statusFilter = ref('')

// User Detail Panel
const selectedUser = ref(null)
const userProfile = ref(null)
const timeline = ref([])
const timelineDate = ref(new Date().toISOString().split('T')[0])
const transferWarehouseId = ref('')

// Create/Edit Modal
const showUserModal = ref(false)
const editingUser = ref(null)
const userForm = ref({
  name: '',
  email: '',
  cc: '',
  phone: '',
  password: '',
  role_id: '',
  active: true,
})

// Toast
const toast = ref({ show: false, message: '', type: 'success' })

// Biometric Enrollment Modal
const showEnrollModal = ref(false)
const enrollUserId = ref(null)
const enrollUserName = ref('')

// ═══════════════════════════════════════
// Computed
// ═══════════════════════════════════════
const totalUsers = computed(() => users.value.length)

const filteredUsers = computed(() => {
  let filtered = users.value

  if (searchTerm.value) {
    const term = searchTerm.value.toLowerCase()
    filtered = filtered.filter(u =>
      u.name?.toLowerCase().includes(term) ||
      u.email?.toLowerCase().includes(term) ||
      (u.cc || '').includes(term)
    )
  }

  if (roleFilter.value) {
    filtered = filtered.filter(u => u.role?.id == roleFilter.value)
  }

  if (statusFilter.value) {
    const isActive = statusFilter.value === 'active'
    filtered = filtered.filter(u => u.active === isActive)
  }

  return filtered
})

const planBadgeClass = computed(() => {
  const plan = appStore.tenantPlan || 'free_trial'
  if (plan === 'enterprise') return 'bg-purple-50 dark:bg-purple-950 text-purple-700 dark:text-purple-400 border-purple-100 dark:border-purple-800'
  if (plan === 'premium') return 'bg-blue-50 dark:bg-blue-950 text-blue-700 dark:text-blue-400 border-blue-100 dark:border-blue-800'
  if (plan === 'basic') return 'bg-emerald-50 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-400 border-emerald-100 dark:border-emerald-800'
  return 'bg-gray-50 dark:bg-zinc-800 text-gray-600 dark:text-zinc-400 border-gray-200 dark:border-zinc-700'
})

// ═══════════════════════════════════════
// Data Loading
// ═══════════════════════════════════════
const loadUsers = async () => {
  try {
    const response = await usersService.getUsersWithPerformance()
    users.value = response.data || []
  } catch {
    try {
      const response = await usersService.getAllUsers()
      users.value = response.data || response || []
    } catch {
      users.value = []
    }
  }
}

const loadRoles = async () => {
  try {
    const response = await rolesService.getAllRoles()
    availableRoles.value = response.data || response || []
  } catch {
    availableRoles.value = []
  }
}

const loadKpis = async () => {
  try {
    const response = await usersService.getDashboardKpis()
    kpis.value = response.data || {}
    if (response.data?.plan_limits) {
      planLimits.value = response.data.plan_limits
    }
  } catch {
    kpis.value = {}
  }
}

const refreshAll = async () => {
  loading.value = true
  try {
    await Promise.all([loadUsers(), loadKpis(), loadRoles()])
  } finally {
    loading.value = false
  }
}

// ═══════════════════════════════════════
// User Detail Panel
// ═══════════════════════════════════════
const openUserDetail = async (user) => {
  selectedUser.value = user
  userProfile.value = null
  timeline.value = []
  transferWarehouseId.value = ''
  timelineDate.value = new Date().toISOString().split('T')[0]

  if (planLimits.value.performance_metrics) {
    profileLoading.value = true
    try {
      const response = await usersService.getUserProfile(user.id)
      userProfile.value = response.data || null
    } catch {
      userProfile.value = null
    } finally {
      profileLoading.value = false
    }
  }

  if (planLimits.value.audit_timeline) {
    loadTimeline()
  }
}

const loadTimeline = async () => {
  if (!selectedUser.value || !planLimits.value.audit_timeline) return

  timelineLoading.value = true
  try {
    const response = await usersService.getUserTimeline(selectedUser.value.id, timelineDate.value)
    timeline.value = response.data?.events || []
  } catch {
    timeline.value = []
  } finally {
    timelineLoading.value = false
  }
}

const closeUserDetail = () => {
  selectedUser.value = null
  userProfile.value = null
  timeline.value = []
}

// ═══════════════════════════════════════
// Sede Transfer
// ═══════════════════════════════════════
const transferSede = async () => {
  if (!transferWarehouseId.value || !selectedUser.value) return

  transferring.value = true
  try {
    const response = await usersService.assignUserWarehouse(selectedUser.value.id, transferWarehouseId.value)
    showToast(response.message || 'Sede actualizada', 'success')
    transferWarehouseId.value = ''
    await Promise.all([loadUsers(), loadKpis()])
    if (selectedUser.value) {
      openUserDetail(selectedUser.value)
    }
  } catch (error) {
    const msg = error.response?.data?.message || 'Error al transferir sede'
    showToast(msg, 'error')
  } finally {
    transferring.value = false
  }
}

// ═══════════════════════════════════════
// CRUD
// ═══════════════════════════════════════
const openCreateModal = () => {
  editingUser.value = null
  userForm.value = { name: '', email: '', cc: '', phone: '', password: '', role_id: '', active: true }
  showUserModal.value = true
}

const editUser = (user) => {
  editingUser.value = user
  userForm.value = {
    name: user.name,
    email: user.email,
    cc: user.cc || '',
    phone: user.phone || '',
    password: '',
    role_id: user.role_id || user.role?.id || '',
    active: user.active,
  }
  showUserModal.value = true
}

const closeUserModal = () => {
  showUserModal.value = false
  editingUser.value = null
}

const saveUser = async () => {
  if (!userForm.value.name || !userForm.value.email || !userForm.value.cc) {
    showToast('Completa los campos obligatorios: Nombre, Email y Cédula', 'error')
    return
  }
  if (!editingUser.value && (!userForm.value.password || userForm.value.password.length < 6)) {
    showToast('La contraseña es obligatoria y debe tener al menos 6 caracteres', 'error')
    return
  }
  if (!userForm.value.role_id) {
    showToast('Selecciona un rol para el usuario', 'error')
    return
  }

  saving.value = true
  try {
    if (editingUser.value) {
      await usersService.updateUser(editingUser.value.id, userForm.value)
      showToast('Usuario actualizado', 'success')
    } else {
      await usersService.createUser(userForm.value)
      showToast('Usuario creado', 'success')
    }
    closeUserModal()
    await refreshAll()
  } catch (error) {
    if (error.response?.status === 422 && error.response?.data?.errors) {
      const errors = error.response.data.errors
      const msg = Object.values(errors).flat().join(', ')
      showToast(msg, 'error')
    } else {
      showToast(error.response?.data?.message || 'Error al guardar usuario', 'error')
    }
  } finally {
    saving.value = false
  }
}

const toggleUserStatus = async (user) => {
  try {
    await usersService.toggleUserActive(user.id)
    showToast(`Usuario ${user.active ? 'desactivado' : 'activado'}`, 'success')
    await loadUsers()
  } catch {
    showToast('Error al cambiar estado', 'error')
  }
}

// ═══════════════════════════════════════
// Helpers
// ═══════════════════════════════════════
const clearFilters = () => {
  searchTerm.value = ''
  roleFilter.value = ''
  statusFilter.value = ''
}

// ═══════════════════════════════════════
// Biometric Enrollment
// ═══════════════════════════════════════
const openEnrollModal = (user) => {
  enrollUserId.value = user.id
  enrollUserName.value = user.name
  showEnrollModal.value = true
}

const closeEnrollModal = () => {
  showEnrollModal.value = false
  enrollUserId.value = null
  enrollUserName.value = ''
}

const onEnrolled = (response) => {
  closeEnrollModal()
  showToast(response?.message || 'Perfil biométrico registrado exitosamente', 'success')
}

const getAvatarBg = (roleName) => {
  const map = {
    'Administrador': 'bg-slate-800 dark:bg-slate-700',
    'Gerente': 'bg-indigo-600 dark:bg-indigo-700',
    'Cajero': 'bg-emerald-600 dark:bg-emerald-700',
    'Vendedor': 'bg-blue-600 dark:bg-blue-700',
  }
  return map[roleName] || 'bg-zinc-500 dark:bg-zinc-600'
}

const getRoleBadge = (roleName) => {
  const map = {
    'Administrador': 'bg-purple-50 dark:bg-purple-950 text-purple-700 dark:text-purple-400 border-purple-100 dark:border-purple-800',
    'Gerente': 'bg-indigo-50 dark:bg-indigo-950 text-indigo-700 dark:text-indigo-400 border-indigo-100 dark:border-indigo-800',
    'Cajero': 'bg-emerald-50 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-400 border-emerald-100 dark:border-emerald-800',
    'Vendedor': 'bg-blue-50 dark:bg-blue-950 text-blue-700 dark:text-blue-400 border-blue-100 dark:border-blue-800',
  }
  return map[roleName] || 'bg-gray-50 dark:bg-zinc-800 text-gray-600 dark:text-zinc-400 border-gray-200 dark:border-zinc-700'
}

const getTimelineDotClass = (color) => {
  const map = {
    emerald: 'bg-emerald-400 border-emerald-200 dark:bg-emerald-500 dark:border-emerald-700',
    blue: 'bg-blue-400 border-blue-200 dark:bg-blue-500 dark:border-blue-700',
    amber: 'bg-amber-400 border-amber-200 dark:bg-amber-500 dark:border-amber-700',
    rose: 'bg-rose-400 border-rose-200 dark:bg-rose-500 dark:border-rose-700',
    slate: 'bg-slate-400 border-slate-200 dark:bg-slate-500 dark:border-slate-700',
  }
  return map[color] || map.slate
}

const getTimelineTextClass = (color) => {
  const map = {
    emerald: 'text-emerald-700 dark:text-emerald-400',
    blue: 'text-blue-700 dark:text-blue-400',
    amber: 'text-amber-700 dark:text-amber-400',
    rose: 'text-rose-700 dark:text-rose-400',
    slate: 'text-gray-700 dark:text-zinc-300',
  }
  return map[color] || map.slate
}

const formatMoney = (amount) => {
  return Number(amount || 0).toLocaleString('es-CO', { minimumFractionDigits: 0, maximumFractionDigits: 0 })
}

const formatDate = (dateString) => {
  if (!dateString) return ''
  const date = new Date(dateString)
  return date.toLocaleDateString('es-ES', { day: '2-digit', month: '2-digit', year: 'numeric' })
}

const formatTime = (dateString) => {
  if (!dateString) return ''
  const date = new Date(dateString)
  return date.toLocaleTimeString('es-ES', { hour: '2-digit', minute: '2-digit' })
}

const formatTimelineTime = (timestamp) => {
  if (!timestamp) return ''
  const date = new Date(timestamp)
  return date.toLocaleTimeString('es-ES', { hour: '2-digit', minute: '2-digit', hour12: true }).toUpperCase()
}

const showToast = (message, type = 'success') => {
  toast.value = { show: true, message, type }
  setTimeout(() => { toast.value.show = false }, 3000)
}

// ═══════════════════════════════════════
// Lifecycle
// ═══════════════════════════════════════
onMounted(async () => {
  loading.value = true
  try {
    await Promise.all([loadUsers(), loadKpis(), loadRoles()])
  } finally {
    loading.value = false
  }
})
</script>

<style scoped>
/* Slide panel transition */
.slide-enter-active,
.slide-leave-active {
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}
.slide-enter-active > div:last-child,
.slide-leave-active > div:last-child {
  transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}
.slide-enter-from > div:last-child,
.slide-leave-to > div:last-child {
  transform: translateX(100%);
}
.slide-enter-from > div:first-child,
.slide-leave-to > div:first-child {
  opacity: 0;
}

/* Fade transition */
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.2s ease;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}

/* Fade in animation */
@keyframes fadeIn {
  from { opacity: 0; transform: translateY(8px); }
  to { opacity: 1; transform: translateY(0); }
}
.animate-fade-in {
  animation: fadeIn 0.4s ease-out;
}
</style>
