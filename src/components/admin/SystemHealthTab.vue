<template>
  <div class="space-y-5">
    <!-- Header: Estado + Uptime + Auto-refresh -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
      <div class="flex items-center gap-3">
        <div class="flex items-center gap-2">
          <div class="w-2.5 h-2.5 rounded-full animate-pulse" :class="allServicesOk ? 'bg-emerald-500' : 'bg-rose-500'"></div>
          <span class="text-xs font-medium" :class="allServicesOk ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400'">
            {{ allServicesOk ? 'Todos los servicios operativos' : 'Servicio(s) con problemas' }}
          </span>
        </div>
        <span class="text-[10px] text-zinc-400">{{ lastUpdated }}</span>
        <span v-if="h?.uptime" class="text-[10px] px-2 py-0.5 rounded-full bg-slate-100 dark:bg-zinc-800 text-slate-600 dark:text-zinc-400 font-medium border border-slate-200 dark:border-zinc-700">
          Uptime: {{ h.uptime.formatted }}
        </span>
      </div>
      <div class="flex items-center gap-2">
        <label class="flex items-center gap-1.5 text-[10px] text-zinc-400 cursor-pointer select-none">
          <input type="checkbox" v-model="autoRefresh" class="w-3 h-3 rounded border-gray-300 dark:border-zinc-600 text-blue-500 focus:ring-blue-500 dark:bg-zinc-800">
          Auto (15s)
        </label>
        <button @click="fetchHealth" :disabled="loading"
          class="px-4 py-2 bg-white dark:bg-zinc-900 hover:bg-slate-50 dark:hover:bg-zinc-800 text-slate-600 dark:text-zinc-200 text-xs font-bold rounded-lg border border-slate-200 dark:border-zinc-800 shadow-sm transition-all duration-200 disabled:opacity-50">
          <svg v-if="loading" class="w-3.5 h-3.5 animate-spin" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z" />
          </svg>
          <span v-else>Actualizar</span>
        </button>
      </div>
    </div>

    <!-- Loading -->
    <div v-if="loading && !h" class="flex items-center justify-center py-20">
      <div class="text-center">
        <svg class="w-8 h-8 animate-spin text-blue-500 mx-auto mb-2" fill="none" viewBox="0 0 24 24">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" />
        </svg>
        <p class="text-xs text-zinc-400">Cargando mÃ©tricas del sistema...</p>
      </div>
    </div>

    <template v-if="h">
      <!-- ==================== SERVICIOS EN TIEMPO REAL ==================== -->
      <div class="bg-white dark:bg-zinc-900 rounded-2xl shadow-xl dark:shadow-black/50 border border-gray-300 dark:border-zinc-800 overflow-hidden">
        <div class="px-5 py-3 border-b border-gray-200 dark:border-zinc-800">
          <h3 class="text-sm font-bold text-gray-900 dark:text-white">Servicios del Sistema</h3>
        </div>
        <div class="grid grid-cols-2 lg:grid-cols-4 divide-x divide-gray-200 dark:divide-zinc-800">
          <div v-for="svc in h.services" :key="svc.name" class="px-5 py-4 relative">
            <div class="flex items-center gap-2 mb-2">
              <div class="w-2 h-2 rounded-full" :class="svc.status === 'running' ? 'bg-emerald-500 animate-pulse' : 'bg-rose-500'"></div>
              <span class="text-xs font-bold text-gray-900 dark:text-white">{{ svc.name }}</span>
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

      <!-- ==================== CPU + RAM + DISCO (Gauges) ==================== -->
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        <!-- CPU -->
        <div class="bg-white dark:bg-zinc-900 rounded-2xl shadow-xl dark:shadow-black/50 border border-gray-300 dark:border-zinc-800 p-5">
          <div class="flex items-center justify-between mb-3">
            <h3 class="text-sm font-bold text-gray-900 dark:text-white">CPU</h3>
            <span class="text-[10px] text-zinc-400">{{ h.cpu?.cores }}C / {{ h.cpu?.threads }}T</span>
          </div>
          <!-- Gauge ring -->
          <div class="flex items-center justify-center mb-3">
            <div class="relative w-28 h-28">
              <svg class="w-28 h-28 -rotate-90" viewBox="0 0 120 120">
                <circle cx="60" cy="60" r="52" stroke-width="10" fill="none" class="stroke-gray-200 dark:stroke-zinc-800" />
                <circle cx="60" cy="60" r="52" stroke-width="10" fill="none" stroke-linecap="round"
                  :class="cpuColor" :stroke-dasharray="cpuDash" style="transition: stroke-dasharray 0.8s ease" />
              </svg>
              <div class="absolute inset-0 flex flex-col items-center justify-center">
                <span class="text-2xl font-bold text-gray-900 dark:text-white">{{ h.cpu?.usage_percent || 0 }}%</span>
                <span class="text-[9px] text-zinc-400">CPU</span>
              </div>
            </div>
          </div>
          <!-- Per-core bars -->
          <div class="grid grid-cols-4 gap-1" v-if="h.cpu?.per_core?.length > 0">
            <div v-for="(pct, i) in h.cpu.per_core" :key="i" class="text-center">
              <div class="h-8 bg-gray-100 dark:bg-zinc-800 rounded-sm relative overflow-hidden">
                <div class="absolute bottom-0 w-full rounded-sm transition-all duration-700"
                  :class="pct > 80 ? 'bg-rose-500' : pct > 50 ? 'bg-amber-500' : 'bg-blue-500'"
                  :style="{ height: pct + '%' }"></div>
              </div>
              <span class="text-[8px] text-zinc-500">{{ i }}</span>
            </div>
          </div>
          <p class="text-[10px] text-zinc-400 mt-2 truncate" :title="h.cpu?.model">{{ h.cpu?.model }}</p>
          <!-- Load average -->
          <div class="flex items-center gap-3 mt-2 pt-2 border-t border-gray-100 dark:border-zinc-800">
            <span class="text-[10px] text-zinc-400">Load:</span>
            <span class="text-[10px] font-mono" :class="loadColor(h.load_average?.['1min'])">{{ h.load_average?.['1min'] }}</span>
            <span class="text-[10px] font-mono text-zinc-400">{{ h.load_average?.['5min'] }}</span>
            <span class="text-[10px] font-mono text-zinc-500">{{ h.load_average?.['15min'] }}</span>
          </div>
        </div>

        <!-- RAM -->
        <div class="bg-white dark:bg-zinc-900 rounded-2xl shadow-xl dark:shadow-black/50 border border-gray-300 dark:border-zinc-800 p-5">
          <div class="flex items-center justify-between mb-3">
            <h3 class="text-sm font-bold text-gray-900 dark:text-white">Memoria RAM</h3>
            <span class="text-[10px] text-zinc-400">{{ fmtMb(h.ram?.total_mb) }}</span>
          </div>
          <!-- Gauge ring -->
          <div class="flex items-center justify-center mb-3">
            <div class="relative w-28 h-28">
              <svg class="w-28 h-28 -rotate-90" viewBox="0 0 120 120">
                <circle cx="60" cy="60" r="52" stroke-width="10" fill="none" class="stroke-gray-200 dark:stroke-zinc-800" />
                <circle cx="60" cy="60" r="52" stroke-width="10" fill="none" stroke-linecap="round"
                  :class="ramColor" :stroke-dasharray="ramDash" style="transition: stroke-dasharray 0.8s ease" />
              </svg>
              <div class="absolute inset-0 flex flex-col items-center justify-center">
                <span class="text-2xl font-bold text-gray-900 dark:text-white">{{ h.ram?.percent_used || 0 }}%</span>
                <span class="text-[9px] text-zinc-400">RAM</span>
              </div>
            </div>
          </div>
          <!-- RAM breakdown -->
          <div class="space-y-2">
            <div class="flex justify-between text-[11px]">
              <span class="text-zinc-400">Usado</span>
              <span class="font-bold text-gray-900 dark:text-white">{{ fmtMb(h.ram?.used_mb) }}</span>
            </div>
            <div class="flex justify-between text-[11px]">
              <span class="text-zinc-400">Disponible</span>
              <span class="font-medium text-emerald-600 dark:text-emerald-400">{{ fmtMb(h.ram?.available_mb) }}</span>
            </div>
            <div class="flex justify-between text-[11px]">
              <span class="text-zinc-400">Buffers/Cache</span>
              <span class="text-zinc-300">{{ fmtMb((h.ram?.buffers_mb || 0) + (h.ram?.cached_mb || 0)) }}</span>
            </div>
            <div v-if="h.ram?.swap_total_mb > 0" class="flex justify-between text-[11px] pt-1 border-t border-gray-100 dark:border-zinc-800">
              <span class="text-zinc-400">Swap</span>
              <span class="text-zinc-300">{{ fmtMb(h.ram?.swap_used_mb) }} / {{ fmtMb(h.ram?.swap_total_mb) }}</span>
            </div>
          </div>
        </div>

        <!-- DISCO -->
        <div class="bg-white dark:bg-zinc-900 rounded-2xl shadow-xl dark:shadow-black/50 border border-gray-300 dark:border-zinc-800 p-5">
          <div class="flex items-center justify-between mb-3">
            <h3 class="text-sm font-bold text-gray-900 dark:text-white">Disco</h3>
            <span class="text-[10px] text-zinc-400">{{ h.disk?.total_gb }} GB</span>
          </div>
          <!-- Gauge ring -->
          <div class="flex items-center justify-center mb-3">
            <div class="relative w-28 h-28">
              <svg class="w-28 h-28 -rotate-90" viewBox="0 0 120 120">
                <circle cx="60" cy="60" r="52" stroke-width="10" fill="none" class="stroke-gray-200 dark:stroke-zinc-800" />
                <circle cx="60" cy="60" r="52" stroke-width="10" fill="none" stroke-linecap="round"
                  :class="diskColor" :stroke-dasharray="diskDash" style="transition: stroke-dasharray 0.8s ease" />
              </svg>
              <div class="absolute inset-0 flex flex-col items-center justify-center">
                <span class="text-2xl font-bold text-gray-900 dark:text-white">{{ h.disk?.percent_used }}%</span>
                <span class="text-[9px] text-zinc-400">DISCO</span>
              </div>
            </div>
          </div>
          <div class="space-y-2">
            <div class="flex justify-between text-[11px]">
              <span class="text-zinc-400">Usado</span>
              <span class="font-bold text-gray-900 dark:text-white">{{ h.disk?.used_gb }} GB</span>
            </div>
            <div class="flex justify-between text-[11px]">
              <span class="text-zinc-400">Libre</span>
              <span class="font-medium text-emerald-600 dark:text-emerald-400">{{ h.disk?.free_gb }} GB</span>
            </div>
          </div>
          <!-- Storage breakdown -->
          <div class="mt-3 pt-3 border-t border-gray-100 dark:border-zinc-800 space-y-1.5">
            <div class="flex justify-between text-[10px]">
              <span class="text-zinc-400">Logs</span>
              <span class="text-zinc-300">{{ h.storage?.logs_size_mb }} MB</span>
            </div>
            <div class="flex justify-between text-[10px]">
              <span class="text-zinc-400">Cache</span>
              <span class="text-zinc-300">{{ h.storage?.cache_size_mb }} MB</span>
            </div>
            <div class="flex justify-between text-[10px]">
              <span class="text-zinc-400">DB Central</span>
              <span class="text-zinc-300">{{ h.database?.central_size_mb }} MB</span>
            </div>
            <div class="flex justify-between text-[10px]">
              <span class="text-zinc-400">DBs Tenants</span>
              <span class="text-zinc-300">{{ h.database?.total_tenant_size_mb }} MB</span>
            </div>
          </div>
        </div>
      </div>

      <!-- ==================== DATABASE + SERVER INFO (2 cols) ==================== -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        <!-- Database real-time -->
        <div class="bg-white dark:bg-zinc-900 rounded-2xl shadow-xl dark:shadow-black/50 border border-gray-300 dark:border-zinc-800 p-5">
          <div class="flex items-center gap-2 mb-4">
            <div class="w-2 h-2 rounded-full" :class="h.database?.status === 'connected' ? 'bg-emerald-500 animate-pulse' : 'bg-rose-500'"></div>
            <h3 class="text-sm font-bold text-gray-900 dark:text-white">Base de Datos MySQL</h3>
            <span class="text-[10px] text-zinc-400 ml-auto">v{{ h.database?.version?.split('-')[0] }}</span>
          </div>
          <div class="grid grid-cols-2 gap-3">
            <div class="bg-gray-50 dark:bg-zinc-800/50 rounded-xl p-3 text-center">
              <p class="text-[10px] text-zinc-400 uppercase tracking-wide">Uptime</p>
              <p class="text-lg font-bold text-gray-900 dark:text-white mt-0.5">{{ formatDbUptime(h.database?.uptime_seconds) }}</p>
            </div>
            <div class="bg-gray-50 dark:bg-zinc-800/50 rounded-xl p-3 text-center">
              <p class="text-[10px] text-zinc-400 uppercase tracking-wide">Queries</p>
              <p class="text-lg font-bold text-gray-900 dark:text-white mt-0.5">{{ fmtNum(h.database?.total_queries) }}</p>
            </div>
            <div class="bg-gray-50 dark:bg-zinc-800/50 rounded-xl p-3 text-center">
              <p class="text-[10px] text-zinc-400 uppercase tracking-wide">Conexiones</p>
              <p class="text-lg font-bold text-gray-900 dark:text-white mt-0.5">
                {{ h.database?.connections }}
                <span class="text-[10px] text-zinc-400 font-normal">/ {{ h.database?.max_connections }}</span>
              </p>
            </div>
            <div class="bg-gray-50 dark:bg-zinc-800/50 rounded-xl p-3 text-center">
              <p class="text-[10px] text-zinc-400 uppercase tracking-wide">Tenants</p>
              <p class="text-lg font-bold text-gray-900 dark:text-white mt-0.5">{{ h.database?.tenant_count }}</p>
            </div>
          </div>
          <!-- Connection usage bar -->
          <div class="mt-3 pt-3 border-t border-gray-100 dark:border-zinc-800">
            <div class="flex justify-between text-[10px] mb-1">
              <span class="text-zinc-400">Pool de Conexiones</span>
              <span class="text-zinc-300">{{ h.database?.connections }}/{{ h.database?.max_connections }}</span>
            </div>
            <div class="h-1.5 bg-gray-100 dark:bg-zinc-800 rounded-full overflow-hidden">
              <div class="h-full rounded-full transition-all duration-700"
                :class="connPercent < 60 ? 'bg-emerald-500' : connPercent < 85 ? 'bg-amber-500' : 'bg-rose-500'"
                :style="{ width: connPercent + '%' }"></div>
            </div>
          </div>
        </div>

        <!-- Server Info + Network -->
        <div class="bg-white dark:bg-zinc-900 rounded-2xl shadow-xl dark:shadow-black/50 border border-gray-300 dark:border-zinc-800 p-5">
          <h3 class="text-sm font-bold text-gray-900 dark:text-white mb-4">Servidor</h3>
          <div class="space-y-0">
            <div class="flex justify-between text-[11px] py-1.5 border-b border-gray-100 dark:border-zinc-800">
              <span class="text-zinc-400">OS</span>
              <span class="font-medium text-gray-900 dark:text-white">{{ h.server?.os }}</span>
            </div>
            <div class="flex justify-between text-[11px] py-1.5 border-b border-gray-100 dark:border-zinc-800">
              <span class="text-zinc-400">Hostname</span>
              <span class="font-mono text-gray-900 dark:text-white">{{ h.server?.hostname }}</span>
            </div>
            <div class="flex justify-between text-[11px] py-1.5 border-b border-gray-100 dark:border-zinc-800">
              <span class="text-zinc-400">PHP</span>
              <span class="font-medium text-blue-600 dark:text-blue-400">{{ h.server?.php_version }}</span>
            </div>
            <div class="flex justify-between text-[11px] py-1.5 border-b border-gray-100 dark:border-zinc-800">
              <span class="text-zinc-400">Laravel</span>
              <span class="font-medium text-red-600 dark:text-red-400">{{ h.server?.laravel_version }}</span>
            </div>
            <div class="flex justify-between items-center text-[11px] py-1.5 border-b border-gray-100 dark:border-zinc-800">
              <span class="text-zinc-400">Entorno</span>
              <span class="px-2 py-0.5 rounded-full text-[9px] font-bold uppercase border"
                :class="h.server?.environment === 'production' 
                  ? 'bg-emerald-50 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-400 border-emerald-100 dark:border-emerald-800'
                  : 'bg-amber-50 dark:bg-amber-950 text-amber-700 dark:text-amber-400 border-amber-100 dark:border-amber-800'">
                {{ h.server?.environment }}
              </span>
            </div>
            <div class="flex justify-between items-center text-[11px] py-1.5 border-b border-gray-100 dark:border-zinc-800">
              <span class="text-zinc-400">Debug</span>
              <span class="px-2 py-0.5 rounded-full text-[9px] font-bold uppercase border"
                :class="!h.server?.debug_mode 
                  ? 'bg-emerald-50 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-400 border-emerald-100 dark:border-emerald-800'
                  : 'bg-rose-50 dark:bg-rose-950 text-rose-700 dark:text-rose-400 border-rose-100 dark:border-rose-800'">
                {{ h.server?.debug_mode ? 'ON' : 'OFF' }}
              </span>
            </div>
            <div class="flex justify-between text-[11px] py-1.5">
              <span class="text-zinc-400">Timezone</span>
              <span class="text-gray-900 dark:text-white">{{ h.server?.timezone }}</span>
            </div>
          </div>
          <!-- Network -->
          <div v-if="h.network?.length" class="mt-3 pt-3 border-t border-gray-100 dark:border-zinc-800">
            <p class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider mb-2">Red</p>
            <div v-for="iface in h.network" :key="iface.name" class="flex items-center justify-between text-[10px] py-1">
              <span class="font-mono text-zinc-400">{{ iface.name }}</span>
              <span class="text-zinc-300">
                <span class="text-emerald-500">â†“{{ fmtBytes(iface.rx_bytes) }}</span>
                <span class="text-blue-500 ml-2">â†‘{{ fmtBytes(iface.tx_bytes) }}</span>
              </span>
            </div>
          </div>
        </div>
      </div>

      <!-- ==================== PROCESOS TOP ==================== -->
      <div v-if="h.top_processes?.length" class="bg-white dark:bg-zinc-900 rounded-2xl shadow-xl dark:shadow-black/50 border border-gray-300 dark:border-zinc-800 overflow-hidden">
        <div class="px-5 py-3 border-b border-gray-200 dark:border-zinc-800">
          <h3 class="text-sm font-bold text-gray-900 dark:text-white">Procesos (por uso de memoria)</h3>
        </div>
        <div class="overflow-x-auto">
          <table class="w-full">
            <thead class="border-b border-gray-200 dark:border-zinc-800">
              <tr>
                <th class="px-4 py-2.5 text-left text-[10px] font-bold text-gray-600 dark:text-zinc-400 uppercase tracking-wider">PID</th>
                <th class="px-4 py-2.5 text-left text-[10px] font-bold text-gray-600 dark:text-zinc-400 uppercase tracking-wider">Usuario</th>
                <th class="px-4 py-2.5 text-right text-[10px] font-bold text-gray-600 dark:text-zinc-400 uppercase tracking-wider">CPU %</th>
                <th class="px-4 py-2.5 text-right text-[10px] font-bold text-gray-600 dark:text-zinc-400 uppercase tracking-wider">MEM %</th>
                <th class="px-4 py-2.5 text-right text-[10px] font-bold text-gray-600 dark:text-zinc-400 uppercase tracking-wider">RSS</th>
                <th class="px-4 py-2.5 text-left text-[10px] font-bold text-gray-600 dark:text-zinc-400 uppercase tracking-wider">Comando</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="p in h.top_processes" :key="p.pid"
                class="border-b border-gray-100 dark:border-zinc-800 hover:bg-gray-50 dark:hover:bg-zinc-800/50 transition-colors duration-200">
                <td class="px-4 py-2 text-[11px] font-mono text-zinc-500">{{ p.pid }}</td>
                <td class="px-4 py-2 text-[11px] text-zinc-400">{{ p.user }}</td>
                <td class="px-4 py-2 text-[11px] text-right font-bold" :class="p.cpu > 50 ? 'text-rose-500' : p.cpu > 20 ? 'text-amber-500' : 'text-zinc-300'">{{ p.cpu }}%</td>
                <td class="px-4 py-2 text-[11px] text-right font-bold" :class="p.mem > 30 ? 'text-rose-500' : p.mem > 15 ? 'text-amber-500' : 'text-zinc-300'">{{ p.mem }}%</td>
                <td class="px-4 py-2 text-[11px] text-right text-zinc-400">{{ p.rss_mb }} MB</td>
                <td class="px-4 py-2 text-[11px] text-zinc-400 font-mono truncate max-w-xs">{{ p.command }}</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- ==================== EXTENSIONES PHP ==================== -->
      <div class="bg-white dark:bg-zinc-900 rounded-2xl shadow-xl dark:shadow-black/50 border border-gray-300 dark:border-zinc-800 overflow-hidden">
        <div class="px-5 py-3 border-b border-gray-200 dark:border-zinc-800">
          <h3 class="text-sm font-bold text-gray-900 dark:text-white">Extensiones PHP</h3>
        </div>
        <div class="p-5 flex flex-wrap gap-2">
          <span v-for="(loaded, ext) in h.extensions" :key="ext"
            class="px-2.5 py-1 rounded-lg text-[10px] font-bold border"
            :class="loaded 
              ? 'bg-emerald-50 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-400 border-emerald-100 dark:border-emerald-800' 
              : 'bg-rose-50 dark:bg-rose-950 text-rose-700 dark:text-rose-400 border-rose-100 dark:border-rose-800'">
            {{ ext }} {{ loaded ? 'âœ“' : 'âœ—' }}
          </span>
        </div>
      </div>

      <!-- ==================== TENANT DBS ==================== -->
      <div v-if="h.database?.tenants?.length" class="bg-white dark:bg-zinc-900 rounded-2xl shadow-xl dark:shadow-black/50 border border-gray-300 dark:border-zinc-800 overflow-hidden">
        <div class="px-5 py-3 border-b border-gray-200 dark:border-zinc-800">
          <h3 class="text-sm font-bold text-gray-900 dark:text-white">Bases de Datos Tenants</h3>
          <p class="text-[10px] text-zinc-400 mt-0.5">{{ h.database?.tenant_count }} DBs Â· {{ h.database?.total_tenant_size_mb }} MB total</p>
        </div>
        <div class="overflow-x-auto">
          <table class="w-full">
            <thead class="border-b border-gray-200 dark:border-zinc-800">
              <tr>
                <th class="px-4 py-2.5 text-left text-[10px] font-bold text-gray-600 dark:text-zinc-400 uppercase tracking-wider">Negocio</th>
                <th class="px-4 py-2.5 text-left text-[10px] font-bold text-gray-600 dark:text-zinc-400 uppercase tracking-wider">Database</th>
                <th class="px-4 py-2.5 text-right text-[10px] font-bold text-gray-600 dark:text-zinc-400 uppercase tracking-wider">Tablas</th>
                <th class="px-4 py-2.5 text-right text-[10px] font-bold text-gray-600 dark:text-zinc-400 uppercase tracking-wider">TamaÃ±o</th>
                <th class="px-4 py-2.5 text-center text-[10px] font-bold text-gray-600 dark:text-zinc-400 uppercase tracking-wider">Estado</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="db in h.database.tenants" :key="db.id"
                class="border-b border-gray-100 dark:border-zinc-800 hover:bg-gray-50 dark:hover:bg-zinc-800/50 transition-colors duration-200">
                <td class="px-4 py-2.5 text-[11px] font-medium text-gray-900 dark:text-white">{{ db.business_name }}</td>
                <td class="px-4 py-2.5 text-[10px] font-mono text-zinc-400">{{ db.database }}</td>
                <td class="px-4 py-2.5 text-[11px] text-right text-zinc-300">{{ db.tables }}</td>
                <td class="px-4 py-2.5 text-[11px] text-right font-bold text-gray-900 dark:text-white">{{ db.size_mb }} MB</td>
                <td class="px-4 py-2.5 text-center">
                  <span class="px-2 py-0.5 rounded-full text-[9px] font-bold uppercase border"
                    :class="db.status === 'active' 
                      ? 'bg-emerald-50 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-400 border-emerald-100 dark:border-emerald-800'
                      : 'bg-rose-50 dark:bg-rose-950 text-rose-700 dark:text-rose-400 border-rose-100 dark:border-rose-800'">
                    {{ db.status }}
                  </span>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- ==================== PHP MEMORY ==================== -->
      <div class="bg-white dark:bg-zinc-900 rounded-2xl shadow-xl dark:shadow-black/50 border border-gray-300 dark:border-zinc-800 p-5">
        <h3 class="text-sm font-bold text-gray-900 dark:text-white mb-3">Memoria PHP (este request)</h3>
        <div class="grid grid-cols-3 gap-4">
          <div class="bg-gray-50 dark:bg-zinc-800/50 rounded-xl p-3 text-center">
            <p class="text-[10px] text-zinc-400 uppercase tracking-wide">En uso</p>
            <p class="text-lg font-bold text-gray-900 dark:text-white">{{ h.php_memory?.usage_mb }} MB</p>
          </div>
          <div class="bg-gray-50 dark:bg-zinc-800/50 rounded-xl p-3 text-center">
            <p class="text-[10px] text-zinc-400 uppercase tracking-wide">Pico</p>
            <p class="text-lg font-bold text-gray-900 dark:text-white">{{ h.php_memory?.peak_mb }} MB</p>
          </div>
          <div class="bg-gray-50 dark:bg-zinc-800/50 rounded-xl p-3 text-center">
            <p class="text-[10px] text-zinc-400 uppercase tracking-wide">LÃ­mite</p>
            <p class="text-lg font-bold text-gray-900 dark:text-white">{{ h.php_memory?.limit }}</p>
          </div>
        </div>
      </div>
    </template>

    <!-- Error -->
    <div v-if="error" class="bg-rose-50 dark:bg-rose-950/50 border border-rose-200 dark:border-rose-800 rounded-xl p-6 text-center">
      <p class="text-sm font-medium text-rose-700 dark:text-rose-400">{{ error }}</p>
      <button @click="fetchHealth" class="mt-3 px-4 py-2 bg-rose-600 text-white text-xs font-bold rounded-lg hover:bg-rose-700 transition-colors">Reintentar</button>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted, watch } from 'vue'
import axios from 'axios'

const loading = ref(false)
const h = ref(null)
const error = ref('')
const lastUpdated = ref('')
const autoRefresh = ref(true)
let refreshInterval = null

const allServicesOk = computed(() => {
  if (!h.value?.services) return false
  return h.value.services.every(s => s.status === 'running')
})

const circumference = 2 * Math.PI * 52

const cpuDash = computed(() => {
  const pct = h.value?.cpu?.usage_percent || 0
  const filled = (pct / 100) * circumference
  return `${filled} ${circumference - filled}`
})
const cpuColor = computed(() => {
  const pct = h.value?.cpu?.usage_percent || 0
  if (pct > 85) return 'stroke-rose-500'
  if (pct > 60) return 'stroke-amber-500'
  return 'stroke-blue-500'
})

const ramDash = computed(() => {
  const pct = h.value?.ram?.percent_used || 0
  const filled = (pct / 100) * circumference
  return `${filled} ${circumference - filled}`
})
const ramColor = computed(() => {
  const pct = h.value?.ram?.percent_used || 0
  if (pct > 85) return 'stroke-rose-500'
  if (pct > 60) return 'stroke-amber-500'
  return 'stroke-emerald-500'
})

const diskDash = computed(() => {
  const pct = h.value?.disk?.percent_used || 0
  const filled = (pct / 100) * circumference
  return `${filled} ${circumference - filled}`
})
const diskColor = computed(() => {
  const pct = h.value?.disk?.percent_used || 0
  if (pct > 90) return 'stroke-rose-500'
  if (pct > 75) return 'stroke-amber-500'
  return 'stroke-emerald-500'
})

const connPercent = computed(() => {
  if (!h.value?.database?.max_connections) return 0
  return Math.min(100, (h.value.database.connections / h.value.database.max_connections) * 100)
})

const loadColor = (val) => {
  const cores = h.value?.cpu?.cores || 1
  if (val > cores * 2) return 'text-rose-500'
  if (val > cores) return 'text-amber-500'
  return 'text-emerald-500'
}

const fmtMb = (mb) => {
  if (!mb && mb !== 0) return 'â€”'
  if (mb >= 1024) return (mb / 1024).toFixed(1) + ' GB'
  return Math.round(mb) + ' MB'
}

const fmtBytes = (bytes) => {
  if (!bytes) return '0'
  if (bytes >= 1073741824) return (bytes / 1073741824).toFixed(1) + ' GB'
  if (bytes >= 1048576) return (bytes / 1048576).toFixed(1) + ' MB'
  if (bytes >= 1024) return (bytes / 1024).toFixed(1) + ' KB'
  return bytes + ' B'
}

const fmtNum = (n) => {
  if (!n && n !== 0) return '0'
  if (n >= 1000000) return (n / 1000000).toFixed(1) + 'M'
  if (n >= 1000) return (n / 1000).toFixed(1) + 'K'
  return n.toLocaleString('es-CO')
}

const formatDbUptime = (secs) => {
  if (!secs) return 'â€”'
  const d = Math.floor(secs / 86400)
  const hrs = Math.floor((secs % 86400) / 3600)
  const m = Math.floor((secs % 3600) / 60)
  if (d > 0) return `${d}d ${hrs}h`
  if (hrs > 0) return `${hrs}h ${m}m`
  return `${m}m`
}

const fetchHealth = async () => {
  loading.value = true
  error.value = ''
  try {
    const res = await axios.get('/api/admin/system/health')
    if (res.data.success) {
      h.value = res.data.data
      lastUpdated.value = new Date().toLocaleTimeString('es-CO', { hour: '2-digit', minute: '2-digit', second: '2-digit' })
    } else {
      error.value = res.data.message || 'Error al cargar datos'
    }
  } catch (e) {
    error.value = e.response?.data?.message || e.message || 'Error de conexiÃ³n'
  }
  loading.value = false
}

const startAutoRefresh = () => {
  stopAutoRefresh()
  if (autoRefresh.value) {
    refreshInterval = setInterval(fetchHealth, 15000)
  }
}

const stopAutoRefresh = () => {
  if (refreshInterval) {
    clearInterval(refreshInterval)
    refreshInterval = null
  }
}

watch(autoRefresh, (val) => {
  if (val) startAutoRefresh()
  else stopAutoRefresh()
})

onMounted(() => {
  fetchHealth()
  startAutoRefresh()
})

onUnmounted(() => {
  stopAutoRefresh()
})
</script>
