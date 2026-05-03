<template>
  <div class="space-y-6">
    <!-- Header compacto con estado y controles -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
      <div class="flex items-center gap-3">
        <div class="flex items-center gap-2">
          <div class="w-2.5 h-2.5 rounded-full animate-pulse" :class="isOnline ? 'bg-emerald-500' : 'bg-rose-500'"></div>
          <span class="text-xs font-medium" :class="isOnline ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400'">
            {{ isOnline ? 'En línea' : 'Sin datos' }}
          </span>
        </div>
        <span class="text-xs text-zinc-400 dark:text-zinc-500">{{ currentDateTime }}</span>
        <span class="text-[10px] px-2 py-0.5 rounded-full bg-slate-100 dark:bg-zinc-800 text-slate-600 dark:text-zinc-400 font-medium border border-slate-200 dark:border-zinc-700">
          {{ data.tenants_analyzed || 0 }} tenants · {{ data.keys_total || 0 }} keys
        </span>
      </div>
      <div class="flex items-center gap-2">
        <select v-model="period" @change="fetchData"
          class="px-3 py-2 text-xs border border-gray-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 text-gray-700 dark:text-zinc-300 rounded-lg font-medium focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400">
          <option value="24h">Últimas 24h</option>
          <option value="7d">7 días</option>
          <option value="30d">30 días</option>
          <option value="all">Todo</option>
        </select>
        <button @click="fetchData" :disabled="loading"
          class="px-4 py-2 bg-white dark:bg-zinc-900 hover:bg-slate-50 dark:hover:bg-zinc-800 text-slate-600 dark:text-zinc-200 text-xs font-bold rounded-lg border border-slate-200 dark:border-zinc-800 shadow-sm transition-all duration-200 disabled:opacity-50">
          <svg v-if="loading" class="w-3.5 h-3.5 animate-spin" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z" />
          </svg>
          <span v-else>Actualizar</span>
        </button>
      </div>
    </div>

    <!-- Resumen compacto: 4 métricas principales en una barra -->
    <div class="bg-white dark:bg-zinc-900 rounded-2xl shadow-xl dark:shadow-black/50 border border-gray-300 dark:border-zinc-800 overflow-hidden">
      <div class="grid grid-cols-2 lg:grid-cols-5 divide-x divide-gray-200 dark:divide-zinc-800">
        <div class="px-5 py-4">
          <p class="text-[10px] font-semibold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider">Requests</p>
          <p class="text-xl font-bold text-gray-900 dark:text-white mt-1">{{ fmt(summary.total_requests) }}</p>
          <div class="flex items-center gap-2 mt-1">
            <span class="text-[10px] font-medium text-emerald-600 dark:text-emerald-400">{{ fmt(summary.successful) }} ok</span>
            <span v-if="summary.errors > 0" class="text-[10px] font-medium text-rose-500">{{ summary.errors }} err</span>
            <span v-if="summary.rate_limited > 0" class="text-[10px] font-medium text-amber-500">{{ summary.rate_limited }} limit</span>
          </div>
        </div>
        <div class="px-5 py-4">
          <p class="text-[10px] font-semibold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider">Tokens</p>
          <p class="text-xl font-bold text-gray-900 dark:text-white mt-1">{{ fmtTokens(summary.total_tokens) }}</p>
          <div class="flex items-center gap-2 mt-1">
            <span class="text-[10px] text-zinc-400">↑{{ fmtTokens(summary.total_input_tokens) }}</span>
            <span class="text-[10px] text-zinc-400">↓{{ fmtTokens(summary.total_output_tokens) }}</span>
          </div>
        </div>
        <div class="px-5 py-4">
          <p class="text-[10px] font-semibold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider">Costo USD</p>
          <p class="text-xl font-bold text-gray-900 dark:text-white mt-1">${{ summary.total_cost_usd?.toFixed(4) || '0.00' }}</p>
          <p class="text-[10px] text-zinc-400 mt-1">~${{ fmt(summary.total_cost_cop || 0) }} COP</p>
        </div>
        <div class="px-5 py-4">
          <p class="text-[10px] font-semibold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider">Latencia</p>
          <p class="text-xl font-bold text-gray-900 dark:text-white mt-1">{{ summary.avg_response_time_ms || 0 }}ms</p>
          <p class="text-[10px] text-zinc-400 mt-1">{{ summary.success_rate || 0 }}% éxito</p>
        </div>
        <div class="px-5 py-4 col-span-2 lg:col-span-1 bg-gradient-to-r from-transparent to-indigo-50/50 dark:to-indigo-950/20">
          <p class="text-[10px] font-semibold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider">Proyección/Mes</p>
          <p class="text-xl font-bold text-indigo-600 dark:text-indigo-400 mt-1">${{ data.cost_projection_monthly_usd?.toFixed(2) || '0.00' }}</p>
          <p class="text-[10px] text-zinc-400 mt-1">~${{ fmt(data.cost_projection_monthly_cop || 0) }} COP</p>
        </div>
      </div>
    </div>

    <!-- Gráficos: Uso por día + Distribución -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
      <!-- Gráfico de barras: Uso diario (30 días) -->
      <div class="lg:col-span-2 bg-white dark:bg-zinc-900 rounded-2xl shadow-xl dark:shadow-black/50 border border-gray-300 dark:border-zinc-800 p-5">
        <div class="flex items-center justify-between mb-4">
          <div>
            <h3 class="text-sm font-bold text-gray-900 dark:text-white">Actividad Diaria</h3>
            <p class="text-[10px] text-zinc-400 mt-0.5">Últimos 30 días</p>
          </div>
          <div class="flex items-center gap-3 text-[10px]">
            <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-sm bg-blue-500"></span> Requests</span>
            <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-sm bg-rose-400"></span> Errores</span>
          </div>
        </div>
        <!-- Bar chart con CSS puro -->
        <div class="flex items-end gap-[2px] h-36" v-if="dailyChartData.length > 0">
          <div v-for="(d, i) in dailyChartData" :key="i" class="flex-1 flex flex-col items-center gap-[1px] group relative">
            <!-- Error bar -->
            <div class="w-full rounded-t-sm bg-rose-400/80 dark:bg-rose-500/60 transition-all duration-300"
              :style="{ height: d.errorHeight + 'px', minHeight: d.errors > 0 ? '2px' : '0' }"></div>
            <!-- Success bar -->
            <div class="w-full rounded-t-sm bg-blue-500/80 dark:bg-blue-400/60 hover:bg-blue-500 dark:hover:bg-blue-400 transition-all duration-300 cursor-pointer"
              :style="{ height: d.height + 'px', minHeight: d.requests > 0 ? '2px' : '0' }"></div>
            <!-- Tooltip -->
            <div class="absolute bottom-full mb-2 hidden group-hover:block z-10 pointer-events-none">
              <div class="bg-zinc-900 dark:bg-zinc-700 text-white text-[10px] rounded-lg px-2.5 py-1.5 whitespace-nowrap shadow-lg">
                <p class="font-bold">{{ d.label }}</p>
                <p>{{ d.requests }} requests · {{ d.errors }} errores</p>
                <p>${{ d.cost.toFixed(4) }} USD · {{ fmtTokens(d.tokens) }} tokens</p>
              </div>
            </div>
          </div>
        </div>
        <div v-else class="h-36 flex items-center justify-center text-sm text-zinc-400">
          Sin datos en los últimos 30 días
        </div>
        <!-- Eje X labels -->
        <div class="flex items-center justify-between mt-2 text-[9px] text-zinc-400" v-if="dailyChartData.length > 0">
          <span>{{ dailyChartData[0]?.label }}</span>
          <span>{{ dailyChartData[Math.floor(dailyChartData.length / 2)]?.label }}</span>
          <span>{{ dailyChartData[dailyChartData.length - 1]?.label }}</span>
        </div>
      </div>

      <!-- Panel lateral: distribución de tipo + modelos -->
      <div class="space-y-4">
        <!-- Distribución Chat vs Voz -->
        <div class="bg-white dark:bg-zinc-900 rounded-2xl shadow-xl dark:shadow-black/50 border border-gray-300 dark:border-zinc-800 p-5">
          <h3 class="text-sm font-bold text-gray-900 dark:text-white mb-3">Tipo de Uso</h3>
          <div class="space-y-3">
            <div>
              <div class="flex justify-between text-xs mb-1">
                <span class="text-zinc-500 dark:text-zinc-400">Chat</span>
                <span class="font-bold text-gray-900 dark:text-white">{{ fmt(summary.chat_requests) }}</span>
              </div>
              <div class="h-2 bg-gray-100 dark:bg-zinc-800 rounded-full overflow-hidden">
                <div class="h-full bg-blue-500 rounded-full transition-all duration-500" :style="{ width: chatPercent + '%' }"></div>
              </div>
            </div>
            <div>
              <div class="flex justify-between text-xs mb-1">
                <span class="text-zinc-500 dark:text-zinc-400">Voz</span>
                <span class="font-bold text-gray-900 dark:text-white">{{ fmt(summary.voice_requests) }} <span class="text-[10px] text-zinc-400 font-normal">({{ (summary.voice_minutes || 0).toFixed(1) }} min)</span></span>
              </div>
              <div class="h-2 bg-gray-100 dark:bg-zinc-800 rounded-full overflow-hidden">
                <div class="h-full bg-rose-500 rounded-full transition-all duration-500" :style="{ width: voicePercent + '%' }"></div>
              </div>
            </div>
          </div>
        </div>

        <!-- Modelos/Proveedores -->
        <div class="bg-white dark:bg-zinc-900 rounded-2xl shadow-xl dark:shadow-black/50 border border-gray-300 dark:border-zinc-800 p-5">
          <h3 class="text-sm font-bold text-gray-900 dark:text-white mb-3">Modelos</h3>
          <div class="space-y-2" v-if="data.model_breakdown?.length > 0">
            <div v-for="m in data.model_breakdown" :key="m.provider + m.model" 
              class="flex items-center justify-between py-1.5 border-b border-gray-100 dark:border-zinc-800 last:border-0">
              <div class="min-w-0">
                <p class="text-xs font-semibold text-gray-800 dark:text-zinc-200 truncate">{{ m.model }}</p>
                <p class="text-[10px] text-zinc-400">{{ m.provider }} · {{ fmtTokens(m.tokens) }} tok</p>
              </div>
              <div class="text-right flex-shrink-0 ml-2">
                <p class="text-xs font-bold text-gray-900 dark:text-white">{{ m.requests }}</p>
                <p class="text-[10px] text-emerald-500">${{ m.cost?.toFixed(4) }}</p>
              </div>
            </div>
          </div>
          <p v-else class="text-xs text-zinc-400 text-center py-2">Sin datos</p>
        </div>
      </div>
    </div>

    <!-- API Keys Grid -->
    <div class="bg-white dark:bg-zinc-900 rounded-2xl shadow-xl dark:shadow-black/50 border border-gray-300 dark:border-zinc-800 overflow-hidden">
      <div class="px-5 py-4 border-b border-gray-200 dark:border-zinc-800 flex items-center justify-between">
        <div>
          <h3 class="text-sm font-bold text-gray-900 dark:text-white">Groq API Keys</h3>
          <p class="text-[10px] text-zinc-400 mt-0.5">{{ data.keys_total || 0 }} keys configuradas · Estado y uso por key</p>
        </div>
        <button @click="testAllKeys" :disabled="testingKeys"
          class="px-3 py-1.5 text-[10px] font-bold rounded-lg border transition-all duration-200"
          :class="testingKeys 
            ? 'bg-amber-50 dark:bg-amber-950 border-amber-200 dark:border-amber-800 text-amber-600 dark:text-amber-400'
            : 'bg-white dark:bg-zinc-800 border-gray-200 dark:border-zinc-700 text-gray-700 dark:text-zinc-300 hover:bg-slate-50 dark:hover:bg-zinc-700'">
          {{ testingKeys ? `Testeando ${testProgress}/${data.keys_status?.length || 0}...` : 'Test All Keys' }}
        </button>
      </div>
      
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-[1px] bg-gray-200 dark:bg-zinc-800">
        <div v-for="key in data.keys_status" :key="key.key_index"
          class="bg-white dark:bg-zinc-900 px-4 py-3 hover:bg-gray-50 dark:hover:bg-zinc-800/50 transition-colors duration-200">
          <div class="flex items-center justify-between mb-2">
            <div class="flex items-center gap-2">
              <span class="text-[10px] font-bold px-1.5 py-0.5 rounded bg-slate-900 dark:bg-slate-700 text-white">#{{ key.key_index }}</span>
              <span class="text-[10px] font-mono text-zinc-400">...{{ key.key_last_4 }}</span>
            </div>
            <span class="px-2 py-0.5 rounded-full text-[9px] font-bold uppercase tracking-wide border"
              :class="keyStatusClass(key)">
              {{ keyStatusLabel(key) }}
            </span>
          </div>
          <div class="grid grid-cols-3 gap-2 text-center">
            <div>
              <p class="text-sm font-bold text-gray-900 dark:text-white">{{ fmt(key.total_requests) }}</p>
              <p class="text-[9px] text-zinc-400">requests</p>
            </div>
            <div>
              <p class="text-sm font-bold text-emerald-600 dark:text-emerald-400">{{ key.successful }}</p>
              <p class="text-[9px] text-zinc-400">success</p>
            </div>
            <div>
              <p class="text-sm font-bold" :class="key.errors > 0 ? 'text-rose-500' : 'text-gray-400 dark:text-zinc-500'">{{ key.errors }}</p>
              <p class="text-[9px] text-zinc-400">errors</p>
            </div>
          </div>
          <!-- Usage bar -->
          <div class="mt-2 h-1 bg-gray-100 dark:bg-zinc-800 rounded-full overflow-hidden" v-if="maxKeyRequests > 0">
            <div class="h-full rounded-full transition-all duration-500"
              :class="key.status === 'degraded' ? 'bg-rose-400' : key.status === 'idle' ? 'bg-zinc-300 dark:bg-zinc-600' : 'bg-blue-500'"
              :style="{ width: (key.total_requests / maxKeyRequests * 100) + '%' }"></div>
          </div>
          <!-- Rate limit info from test -->
          <div v-if="keyTestResults[key.key_index]" class="mt-2 pt-2 border-t border-gray-100 dark:border-zinc-800">
            <div class="flex items-center justify-between text-[10px]">
              <span class="text-zinc-400">Respuesta:</span>
              <span class="font-mono font-medium" :class="keyTestResults[key.key_index].status === 'active' ? 'text-emerald-500' : 'text-rose-500'">
                {{ keyTestResults[key.key_index].response_time_ms }}ms
              </span>
            </div>
            <div v-if="keyTestResults[key.key_index].remaining" class="mt-1">
              <div class="flex justify-between text-[10px] mb-0.5">
                <span class="text-zinc-400">Límite req:</span>
                <span class="text-zinc-300">{{ keyTestResults[key.key_index].remaining.requests }}/{{ keyTestResults[key.key_index].limits?.requests }}</span>
              </div>
              <div class="h-1 bg-gray-100 dark:bg-zinc-800 rounded-full overflow-hidden">
                <div class="h-full bg-emerald-500 rounded-full"
                  :style="{ width: rateLimitPercent(keyTestResults[key.key_index]) + '%' }"></div>
              </div>
            </div>
          </div>
          <p v-if="key.last_used" class="text-[9px] text-zinc-500 mt-2">Último uso: {{ relativeTime(key.last_used) }}</p>
        </div>
      </div>
    </div>

    <!-- Uso por Tenant -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4" v-if="data.tenant_breakdown?.length > 0">
      <div class="bg-white dark:bg-zinc-900 rounded-2xl shadow-xl dark:shadow-black/50 border border-gray-300 dark:border-zinc-800 p-5">
        <h3 class="text-sm font-bold text-gray-900 dark:text-white mb-3">Uso por Tenant</h3>
        <div class="space-y-2">
          <div v-for="t in data.tenant_breakdown?.slice(0, 10)" :key="t.tenant_id"
            class="flex items-center gap-3">
            <span class="text-xs font-mono font-medium text-blue-600 dark:text-blue-400 w-24 truncate">{{ t.tenant_id }}</span>
            <div class="flex-1 h-3 bg-gray-100 dark:bg-zinc-800 rounded-full overflow-hidden">
              <div class="h-full bg-blue-500/70 rounded-full transition-all duration-500"
                :style="{ width: tenantBarWidth(t.total_requests) + '%' }"></div>
            </div>
            <div class="text-right w-20">
              <span class="text-xs font-bold text-gray-900 dark:text-white">{{ fmt(t.total_requests) }}</span>
              <span class="text-[10px] text-zinc-400 ml-1">${{ t.cost_usd?.toFixed(4) }}</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Gráfico de costos por hora (últimas 48h) -->
      <div class="bg-white dark:bg-zinc-900 rounded-2xl shadow-xl dark:shadow-black/50 border border-gray-300 dark:border-zinc-800 p-5">
        <div class="flex items-center justify-between mb-3">
          <div>
            <h3 class="text-sm font-bold text-gray-900 dark:text-white">Tráfico por Hora</h3>
            <p class="text-[10px] text-zinc-400">Últimas 48 horas</p>
          </div>
        </div>
        <div class="flex items-end gap-[1px] h-28" v-if="hourlyChartData.length > 0">
          <div v-for="(h, i) in hourlyChartData" :key="i" class="flex-1 group relative">
            <div class="w-full rounded-t-sm bg-indigo-400/60 dark:bg-indigo-500/40 hover:bg-indigo-500 dark:hover:bg-indigo-400/60 transition-all duration-200 cursor-pointer"
              :style="{ height: h.height + 'px', minHeight: h.requests > 0 ? '2px' : '0' }"></div>
            <div class="absolute bottom-full mb-2 hidden group-hover:block z-10 pointer-events-none left-1/2 -translate-x-1/2">
              <div class="bg-zinc-900 dark:bg-zinc-700 text-white text-[10px] rounded-lg px-2 py-1 whitespace-nowrap shadow-lg">
                <p class="font-bold">{{ h.label }}</p>
                <p>{{ h.requests }} req · {{ fmtTokens(h.tokens) }} tok</p>
              </div>
            </div>
          </div>
        </div>
        <div v-else class="h-28 flex items-center justify-center text-sm text-zinc-400">
          Sin datos
        </div>
      </div>
    </div>

    <!-- Peticiones Recientes -->
    <div class="bg-white dark:bg-zinc-900 rounded-2xl shadow-xl dark:shadow-black/50 border border-gray-300 dark:border-zinc-800 overflow-hidden">
      <div class="px-5 py-4 border-b border-gray-200 dark:border-zinc-800 flex items-center justify-between">
        <div>
          <h3 class="text-sm font-bold text-gray-900 dark:text-white">Peticiones Recientes</h3>
          <p class="text-[10px] text-zinc-400 mt-0.5">Últimas {{ data.recent_requests?.length || 0 }} peticiones</p>
        </div>
      </div>
      <div class="overflow-x-auto">
        <table class="w-full">
          <thead class="border-b border-gray-200 dark:border-zinc-800">
            <tr class="bg-white dark:bg-zinc-900">
              <th class="px-4 py-2.5 text-left text-[10px] font-bold text-gray-600 dark:text-zinc-400 uppercase tracking-wider">Fecha</th>
              <th class="px-4 py-2.5 text-left text-[10px] font-bold text-gray-600 dark:text-zinc-400 uppercase tracking-wider">Tenant</th>
              <th class="px-4 py-2.5 text-left text-[10px] font-bold text-gray-600 dark:text-zinc-400 uppercase tracking-wider">Tipo</th>
              <th class="px-4 py-2.5 text-left text-[10px] font-bold text-gray-600 dark:text-zinc-400 uppercase tracking-wider">Mensaje</th>
              <th class="px-4 py-2.5 text-left text-[10px] font-bold text-gray-600 dark:text-zinc-400 uppercase tracking-wider">Tokens</th>
              <th class="px-4 py-2.5 text-left text-[10px] font-bold text-gray-600 dark:text-zinc-400 uppercase tracking-wider">Latencia</th>
              <th class="px-4 py-2.5 text-left text-[10px] font-bold text-gray-600 dark:text-zinc-400 uppercase tracking-wider">Costo</th>
              <th class="px-4 py-2.5 text-left text-[10px] font-bold text-gray-600 dark:text-zinc-400 uppercase tracking-wider">Estado</th>
            </tr>
          </thead>
          <tbody class="bg-white dark:bg-zinc-900">
            <tr v-for="(req, i) in data.recent_requests" :key="i"
              class="border-b border-gray-100 dark:border-zinc-800 hover:bg-gray-50 dark:hover:bg-zinc-800/50 transition-colors duration-200">
              <td class="px-4 py-2.5 whitespace-nowrap text-[11px] text-zinc-500 dark:text-zinc-400 font-mono">{{ formatDateTime(req.created_at) }}</td>
              <td class="px-4 py-2.5 whitespace-nowrap">
                <span class="px-1.5 py-0.5 bg-blue-50 dark:bg-blue-950 text-blue-700 dark:text-blue-400 border border-blue-100 dark:border-blue-800 text-[10px] font-medium rounded">{{ req.tenant }}</span>
              </td>
              <td class="px-4 py-2.5 whitespace-nowrap">
                <span class="px-1.5 py-0.5 rounded text-[10px] font-medium border"
                  :class="req.type === 'voice' ? 'bg-rose-50 dark:bg-rose-950 text-rose-700 dark:text-rose-400 border-rose-100 dark:border-rose-800' : 'bg-indigo-50 dark:bg-indigo-950 text-indigo-700 dark:text-indigo-400 border-indigo-100 dark:border-indigo-800'">
                  {{ req.type === 'voice' ? 'Voz' : 'Chat' }}
                </span>
              </td>
              <td class="px-4 py-2.5 text-[11px] text-gray-700 dark:text-zinc-300 max-w-xs truncate">{{ req.type === 'voice' ? `Llamada ${req.voice_seconds}s` : (req.message || '—') }}</td>
              <td class="px-4 py-2.5 whitespace-nowrap text-[11px]">
                <span class="font-bold text-purple-600 dark:text-purple-400">{{ fmtTokens(req.tokens) }}</span>
              </td>
              <td class="px-4 py-2.5 whitespace-nowrap text-[11px] font-mono text-zinc-500">{{ req.response_time_ms }}ms</td>
              <td class="px-4 py-2.5 whitespace-nowrap text-[11px] font-mono text-emerald-600 dark:text-emerald-400">${{ req.cost_usd?.toFixed(6) }}</td>
              <td class="px-4 py-2.5 whitespace-nowrap">
                <span class="px-2 py-0.5 rounded-full text-[9px] font-bold border uppercase tracking-wide"
                  :class="{
                    'bg-emerald-50 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-400 border-emerald-100 dark:border-emerald-800': req.status === 'success',
                    'bg-amber-50 dark:bg-amber-950 text-amber-700 dark:text-amber-400 border-amber-100 dark:border-amber-800': req.status === 'rate_limited',
                    'bg-rose-50 dark:bg-rose-950 text-rose-700 dark:text-rose-400 border-rose-100 dark:border-rose-800': req.status === 'error',
                  }">{{ req.status }}</span>
              </td>
            </tr>
            <tr v-if="!data.recent_requests || data.recent_requests.length === 0">
              <td colspan="8" class="px-4 py-8 text-center text-sm text-zinc-400">No hay peticiones en el período seleccionado</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import axios from 'axios'

const emit = defineEmits(['notify'])

const loading = ref(false)
const period = ref('24h')
const testingKeys = ref(false)
const testProgress = ref(0)
const keyTestResults = ref({})

const data = ref({
  summary: {},
  keys_status: [],
  keys_total: 0,
  usage_by_hour: [],
  usage_by_day: [],
  tenant_breakdown: [],
  model_breakdown: [],
  recent_requests: [],
  tenants_analyzed: 0,
  cost_projection_monthly_usd: 0,
  cost_projection_monthly_cop: 0,
})

const summary = computed(() => data.value.summary || {})
const isOnline = computed(() => data.value.tenants_analyzed > 0)

const currentDateTime = computed(() => {
  const now = new Date()
  return now.toLocaleDateString('es-CO', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' }) + ', ' +
    now.toLocaleTimeString('es-CO', { hour: '2-digit', minute: '2-digit' })
})

const chatPercent = computed(() => {
  const total = (summary.value.chat_requests || 0) + (summary.value.voice_requests || 0)
  return total > 0 ? ((summary.value.chat_requests || 0) / total * 100) : 0
})
const voicePercent = computed(() => {
  const total = (summary.value.chat_requests || 0) + (summary.value.voice_requests || 0)
  return total > 0 ? ((summary.value.voice_requests || 0) / total * 100) : 0
})

const maxKeyRequests = computed(() => {
  return Math.max(...(data.value.keys_status || []).map(k => k.total_requests || 0), 1)
})

// Daily chart data (fill 30 days)
const dailyChartData = computed(() => {
  const raw = data.value.usage_by_day || []
  if (raw.length === 0) return []
  const maxReq = Math.max(...raw.map(d => d.requests || 0), 1)
  const maxErr = Math.max(...raw.map(d => d.errors || 0), 1)
  const chartH = 128
  return raw.map(d => ({
    label: formatShortDate(d.day),
    requests: d.requests || 0,
    errors: d.errors || 0,
    tokens: d.tokens || 0,
    cost: d.cost || 0,
    height: Math.max(0, ((d.requests || 0) / maxReq) * chartH * 0.85),
    errorHeight: Math.max(0, ((d.errors || 0) / maxReq) * chartH * 0.15),
  }))
})

// Hourly chart
const hourlyChartData = computed(() => {
  const raw = data.value.usage_by_hour || []
  if (raw.length === 0) return []
  const maxReq = Math.max(...raw.map(h => h.requests || 0), 1)
  return raw.map(h => ({
    label: formatHour(h.hour),
    requests: h.requests || 0,
    tokens: h.tokens || 0,
    height: Math.max(0, ((h.requests || 0) / maxReq) * 100),
  }))
})

const fetchData = async () => {
  loading.value = true
  try {
    const res = await axios.get(`/admin/api/ai-monitoring/dashboard?period=${period.value}`)
    if (res.data?.success) {
      data.value = res.data
    }
  } catch (e) {
    // silently fail
  }
  loading.value = false
}

const testAllKeys = async () => {
  testingKeys.value = true
  testProgress.value = 0
  keyTestResults.value = {}
  const keys = data.value.keys_status || []
  for (const key of keys) {
    try {
      testProgress.value++
      const res = await axios.get(`/api/admin/ai-monitoring/key/${key.key_index}`)
      if (res.data?.success) {
        keyTestResults.value[key.key_index] = res.data
      }
    } catch (e) {
      keyTestResults.value[key.key_index] = { status: 'error', response_time_ms: 0 }
    }
  }
  testingKeys.value = false
}

// Formatting helpers
const fmt = (n) => {
  if (n == null) return '0'
  if (n >= 1000000) return (n / 1000000).toFixed(1) + 'M'
  if (n >= 1000) return (n / 1000).toFixed(1) + 'K'
  return n.toLocaleString('es-CO')
}

const fmtTokens = (n) => {
  if (!n) return '0'
  if (n >= 1000000) return (n / 1000000).toFixed(1) + 'M'
  if (n >= 1000) return (n / 1000).toFixed(1) + 'K'
  return n.toString()
}

const formatDateTime = (d) => {
  if (!d) return '—'
  const dt = new Date(d)
  return dt.toLocaleDateString('es-CO', { month: 'short', day: 'numeric' }) + ' ' +
    dt.toLocaleTimeString('es-CO', { hour: '2-digit', minute: '2-digit' })
}

const formatShortDate = (d) => {
  if (!d) return ''
  const dt = new Date(d + 'T00:00:00')
  return dt.toLocaleDateString('es-CO', { month: 'short', day: 'numeric' })
}

const formatHour = (h) => {
  if (!h) return ''
  const dt = new Date(h)
  return dt.toLocaleTimeString('es-CO', { hour: '2-digit', minute: '2-digit' })
}

const relativeTime = (dateStr) => {
  if (!dateStr) return '—'
  const diff = (Date.now() - new Date(dateStr).getTime()) / 1000
  if (diff < 60) return 'hace ' + Math.round(diff) + 's'
  if (diff < 3600) return 'hace ' + Math.round(diff / 60) + ' min'
  if (diff < 86400) return 'hace ' + Math.round(diff / 3600) + 'h'
  return 'hace ' + Math.round(diff / 86400) + 'd'
}

const keyStatusClass = (key) => {
  const r = keyTestResults.value[key.key_index]
  const status = r?.status || key.status
  switch (status) {
    case 'active': return 'bg-emerald-50 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-400 border-emerald-100 dark:border-emerald-800'
    case 'rate_limited': return 'bg-amber-50 dark:bg-amber-950 text-amber-700 dark:text-amber-400 border-amber-100 dark:border-amber-800'
    case 'degraded': return 'bg-rose-50 dark:bg-rose-950 text-rose-700 dark:text-rose-400 border-rose-100 dark:border-rose-800'
    case 'error':
    case 'unreachable': return 'bg-rose-50 dark:bg-rose-950 text-rose-700 dark:text-rose-400 border-rose-100 dark:border-rose-800'
    default: return 'bg-gray-50 dark:bg-zinc-800 text-gray-500 dark:text-zinc-400 border-gray-200 dark:border-zinc-700'
  }
}

const keyStatusLabel = (key) => {
  const r = keyTestResults.value[key.key_index]
  if (r) return r.status
  return key.status || 'idle'
}

const rateLimitPercent = (result) => {
  if (!result?.remaining?.requests || !result?.limits?.requests) return 0
  return (parseInt(result.remaining.requests) / parseInt(result.limits.requests) * 100)
}

const tenantBarWidth = (requests) => {
  const max = Math.max(...(data.value.tenant_breakdown || []).map(t => t.total_requests), 1)
  return (requests / max * 100)
}

onMounted(() => {
  fetchData()
})
</script>
