<template>
  <div class="space-y-6">
    <!-- KPIs de Seguridad -->
    <div class="grid grid-cols-2 lg:grid-cols-5 gap-4">
      <div class="bg-white dark:bg-zinc-900/80 backdrop-blur-sm rounded-xl px-4 py-3 border border-gray-300 dark:border-zinc-800/60 hover:shadow-md transition-all duration-200">
        <div class="flex items-center gap-3">
          <div class="w-11 h-11 rounded-xl flex items-center justify-center bg-gray-100 dark:bg-zinc-800/50 border border-gray-200 dark:border-white/5">
            <svg class="w-5 h-5 text-rose-600 dark:text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/>
            </svg>
          </div>
          <div>
            <p class="text-[11px] text-gray-500 dark:text-zinc-400 font-medium">Fallos Hoy</p>
            <p class="text-xl font-bold text-gray-900 dark:text-white">{{ kpis.failed_today || 0 }}</p>
          </div>
        </div>
      </div>

      <div class="bg-white dark:bg-zinc-900/80 backdrop-blur-sm rounded-xl px-4 py-3 border border-gray-300 dark:border-zinc-800/60 hover:shadow-md transition-all duration-200">
        <div class="flex items-center gap-3">
          <div class="w-11 h-11 rounded-xl flex items-center justify-center bg-gray-100 dark:bg-zinc-800/50 border border-gray-200 dark:border-white/5">
            <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
          </div>
          <div>
            <p class="text-[11px] text-gray-500 dark:text-zinc-400 font-medium">Exitosos Hoy</p>
            <p class="text-xl font-bold text-gray-900 dark:text-white">{{ kpis.success_today || 0 }}</p>
          </div>
        </div>
      </div>

      <div class="bg-white dark:bg-zinc-900/80 backdrop-blur-sm rounded-xl px-4 py-3 border border-gray-300 dark:border-zinc-800/60 hover:shadow-md transition-all duration-200">
        <div class="flex items-center gap-3">
          <div class="w-11 h-11 rounded-xl flex items-center justify-center bg-gray-100 dark:bg-zinc-800/50 border border-gray-200 dark:border-white/5">
            <svg class="w-5 h-5 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
            </svg>
          </div>
          <div>
            <p class="text-[11px] text-gray-500 dark:text-zinc-400 font-medium">Cuentas Bloqueadas</p>
            <p class="text-xl font-bold text-gray-900 dark:text-white">{{ kpis.blocked_accounts || 0 }}</p>
          </div>
        </div>
      </div>

      <div class="bg-white dark:bg-zinc-900/80 backdrop-blur-sm rounded-xl px-4 py-3 border border-gray-300 dark:border-zinc-800/60 hover:shadow-md transition-all duration-200">
        <div class="flex items-center gap-3">
          <div class="w-11 h-11 rounded-xl flex items-center justify-center bg-gray-100 dark:bg-zinc-800/50 border border-gray-200 dark:border-white/5">
            <svg class="w-5 h-5 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
            </svg>
          </div>
          <div>
            <p class="text-[11px] text-gray-500 dark:text-zinc-400 font-medium">IPs Bloqueadas</p>
            <p class="text-xl font-bold text-gray-900 dark:text-white">{{ kpis.blocked_ips || 0 }}</p>
          </div>
        </div>
      </div>

      <div class="bg-white dark:bg-zinc-900/80 backdrop-blur-sm rounded-xl px-4 py-3 border border-gray-300 dark:border-zinc-800/60 hover:shadow-md transition-all duration-200">
        <div class="flex items-center gap-3">
          <div class="w-11 h-11 rounded-xl flex items-center justify-center bg-gray-100 dark:bg-zinc-800/50 border border-gray-200 dark:border-white/5">
            <svg class="w-5 h-5 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
            </svg>
          </div>
          <div>
            <p class="text-[11px] text-gray-500 dark:text-zinc-400 font-medium">Sospechosas</p>
            <p class="text-xl font-bold text-gray-900 dark:text-white">{{ kpis.suspicious_accounts || 0 }}</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Cuentas Bloqueadas -->
    <div v-if="data.blocked_accounts?.length > 0" class="bg-white dark:bg-zinc-900 rounded-2xl shadow-xl dark:shadow-black/50 border border-gray-300 dark:border-zinc-800 overflow-hidden">
      <div class="px-5 py-4 border-b border-gray-200 dark:border-zinc-800 flex items-center justify-between">
        <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Cuentas Bloqueadas</h3>
      </div>
      <div class="overflow-x-auto">
        <table class="w-full text-sm">
          <thead>
            <tr class="border-b border-gray-200 dark:border-zinc-800 bg-gray-50 dark:bg-zinc-900">
              <th class="text-left px-5 py-3 text-xs font-semibold text-gray-600 dark:text-zinc-400 uppercase tracking-wider">Email</th>
              <th class="text-left px-5 py-3 text-xs font-semibold text-gray-600 dark:text-zinc-400 uppercase tracking-wider">Motivo</th>
              <th class="text-left px-5 py-3 text-xs font-semibold text-gray-600 dark:text-zinc-400 uppercase tracking-wider">Score</th>
              <th class="text-left px-5 py-3 text-xs font-semibold text-gray-600 dark:text-zinc-400 uppercase tracking-wider">Bloqueado</th>
              <th class="text-center px-5 py-3 text-xs font-semibold text-gray-600 dark:text-zinc-400 uppercase tracking-wider">Acción</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="acc in data.blocked_accounts" :key="acc.id" class="border-b border-gray-100 dark:border-zinc-800 hover:bg-gray-50 dark:hover:bg-zinc-800/50 transition-colors">
              <td class="px-5 py-3 text-gray-900 dark:text-white font-medium">{{ acc.email }}</td>
              <td class="px-5 py-3 text-gray-600 dark:text-zinc-400 text-xs max-w-xs truncate">{{ acc.block_reason }}</td>
              <td class="px-5 py-3">
                <span class="px-2 py-0.5 rounded-md text-xs font-semibold border"
                      :class="acc.risk_score >= 10 ? 'bg-rose-50 dark:bg-rose-950 text-rose-700 dark:text-rose-400 border-rose-200 dark:border-rose-800' : 'bg-amber-50 dark:bg-amber-950 text-amber-700 dark:text-amber-400 border-amber-200 dark:border-amber-800'">
                  {{ acc.risk_score }}
                </span>
              </td>
              <td class="px-5 py-3 text-gray-500 dark:text-zinc-500 text-xs">{{ formatDate(acc.blocked_at) }}</td>
              <td class="px-5 py-3 text-center">
                <button @click="unblockUser(acc.email)" 
                        class="px-3 py-1.5 text-xs font-medium rounded-lg border text-emerald-600 dark:text-emerald-400 border-emerald-200 dark:border-emerald-800 hover:bg-emerald-50 dark:hover:bg-emerald-950/30 transition-all">
                  Desbloquear
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- IPs Bloqueadas -->
    <div v-if="data.blocked_ips?.length > 0" class="bg-white dark:bg-zinc-900 rounded-2xl shadow-xl dark:shadow-black/50 border border-gray-300 dark:border-zinc-800 overflow-hidden">
      <div class="px-5 py-4 border-b border-gray-200 dark:border-zinc-800 flex items-center justify-between">
        <h3 class="text-sm font-semibold text-gray-900 dark:text-white">IPs Bloqueadas</h3>
      </div>
      <div class="overflow-x-auto">
        <table class="w-full text-sm">
          <thead>
            <tr class="border-b border-gray-200 dark:border-zinc-800 bg-gray-50 dark:bg-zinc-900">
              <th class="text-left px-5 py-3 text-xs font-semibold text-gray-600 dark:text-zinc-400 uppercase tracking-wider">IP</th>
              <th class="text-left px-5 py-3 text-xs font-semibold text-gray-600 dark:text-zinc-400 uppercase tracking-wider">Motivo</th>
              <th class="text-left px-5 py-3 text-xs font-semibold text-gray-600 dark:text-zinc-400 uppercase tracking-wider">Intentos</th>
              <th class="text-left px-5 py-3 text-xs font-semibold text-gray-600 dark:text-zinc-400 uppercase tracking-wider">Expira</th>
              <th class="text-center px-5 py-3 text-xs font-semibold text-gray-600 dark:text-zinc-400 uppercase tracking-wider">Acción</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="ip in data.blocked_ips" :key="ip.id" class="border-b border-gray-100 dark:border-zinc-800 hover:bg-gray-50 dark:hover:bg-zinc-800/50 transition-colors">
              <td class="px-5 py-3 text-gray-900 dark:text-white font-mono text-xs">{{ ip.ip_address }}</td>
              <td class="px-5 py-3 text-gray-600 dark:text-zinc-400 text-xs max-w-xs truncate">{{ ip.reason }}</td>
              <td class="px-5 py-3 text-gray-800 dark:text-zinc-300 font-semibold">{{ ip.attempt_count }}</td>
              <td class="px-5 py-3 text-gray-500 dark:text-zinc-500 text-xs">
                {{ ip.is_permanent ? 'Permanente' : formatDate(ip.expires_at) }}
              </td>
              <td class="px-5 py-3 text-center">
                <button @click="unblockIp(ip.ip_address)" 
                        class="px-3 py-1.5 text-xs font-medium rounded-lg border text-emerald-600 dark:text-emerald-400 border-emerald-200 dark:border-emerald-800 hover:bg-emerald-50 dark:hover:bg-emerald-950/30 transition-all">
                  Desbloquear
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Top Emails Atacados (24h) -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
      <div class="bg-white dark:bg-zinc-900 rounded-2xl shadow-xl dark:shadow-black/50 border border-gray-300 dark:border-zinc-800 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-200 dark:border-zinc-800">
          <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Emails Más Atacados (24h)</h3>
        </div>
        <div class="divide-y divide-gray-100 dark:divide-zinc-800">
          <div v-if="!data.top_attacked_emails?.length" class="px-5 py-6 text-center text-sm text-gray-400 dark:text-zinc-600">Sin intentos en las últimas 24h</div>
          <div v-for="item in data.top_attacked_emails" :key="item.email" class="px-5 py-3 flex items-center justify-between hover:bg-gray-50 dark:hover:bg-zinc-800/50 transition-colors">
            <span class="text-sm text-gray-700 dark:text-zinc-300 truncate max-w-[70%]">{{ item.email }}</span>
            <span class="px-2.5 py-1 rounded-full text-[10px] font-bold border uppercase bg-rose-50 dark:bg-rose-950 text-rose-700 dark:text-rose-400 border-rose-100 dark:border-rose-800">{{ item.attempts }} intentos</span>
          </div>
        </div>
      </div>

      <div class="bg-white dark:bg-zinc-900 rounded-2xl shadow-xl dark:shadow-black/50 border border-gray-300 dark:border-zinc-800 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-200 dark:border-zinc-800">
          <h3 class="text-sm font-semibold text-gray-900 dark:text-white">IPs con Más Fallos (24h)</h3>
        </div>
        <div class="divide-y divide-gray-100 dark:divide-zinc-800">
          <div v-if="!data.top_attack_ips?.length" class="px-5 py-6 text-center text-sm text-gray-400 dark:text-zinc-600">Sin intentos en las últimas 24h</div>
          <div v-for="item in data.top_attack_ips" :key="item.ip_address" class="px-5 py-3 flex items-center justify-between hover:bg-gray-50 dark:hover:bg-zinc-800/50 transition-colors">
            <span class="text-sm text-gray-700 dark:text-zinc-300 font-mono">{{ item.ip_address }}</span>
            <span class="px-2.5 py-1 rounded-full text-[10px] font-bold border uppercase bg-amber-50 dark:bg-amber-950 text-amber-700 dark:text-amber-400 border-amber-100 dark:border-amber-800">{{ item.attempts }} intentos</span>
          </div>
        </div>
      </div>
    </div>

    <!-- Eventos de Seguridad Recientes -->
    <div class="bg-white dark:bg-zinc-900 rounded-2xl shadow-xl dark:shadow-black/50 border border-gray-300 dark:border-zinc-800 overflow-hidden">
      <div class="px-5 py-4 border-b border-gray-200 dark:border-zinc-800 flex items-center justify-between">
        <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Eventos de Seguridad Recientes</h3>
        <button @click="cleanupLogs"
                class="px-3 py-1.5 text-xs font-medium rounded-lg border text-gray-500 dark:text-zinc-400 border-gray-200 dark:border-zinc-700 hover:bg-gray-50 dark:hover:bg-zinc-800 transition-all">
          Limpiar Antiguos
        </button>
      </div>
      <div class="overflow-x-auto">
        <table class="w-full text-sm">
          <thead>
            <tr class="border-b border-gray-200 dark:border-zinc-800 bg-gray-50 dark:bg-zinc-900">
              <th class="text-left px-5 py-3 text-xs font-semibold text-gray-600 dark:text-zinc-400 uppercase tracking-wider">Tipo</th>
              <th class="text-left px-5 py-3 text-xs font-semibold text-gray-600 dark:text-zinc-400 uppercase tracking-wider">Severidad</th>
              <th class="text-left px-5 py-3 text-xs font-semibold text-gray-600 dark:text-zinc-400 uppercase tracking-wider">Email</th>
              <th class="text-left px-5 py-3 text-xs font-semibold text-gray-600 dark:text-zinc-400 uppercase tracking-wider">IP</th>
              <th class="text-left px-5 py-3 text-xs font-semibold text-gray-600 dark:text-zinc-400 uppercase tracking-wider">Acción</th>
              <th class="text-left px-5 py-3 text-xs font-semibold text-gray-600 dark:text-zinc-400 uppercase tracking-wider">Fecha</th>
              <th class="text-center px-5 py-3 text-xs font-semibold text-gray-600 dark:text-zinc-400 uppercase tracking-wider">Estado</th>
            </tr>
          </thead>
          <tbody>
            <tr v-if="!data.recent_events?.length">
              <td colspan="7" class="px-5 py-8 text-center text-sm text-gray-400 dark:text-zinc-600">No hay eventos de seguridad registrados</td>
            </tr>
            <tr v-for="evt in data.recent_events" :key="evt.id" class="border-b border-gray-100 dark:border-zinc-800 hover:bg-gray-50 dark:hover:bg-zinc-800/50 transition-colors">
              <td class="px-5 py-3">
                <span class="px-2 py-0.5 rounded-md text-[10px] font-bold border uppercase"
                      :class="eventTypeClass(evt.event_type)">
                  {{ eventTypeLabel(evt.event_type) }}
                </span>
              </td>
              <td class="px-5 py-3">
                <span class="px-2 py-0.5 rounded-md text-[10px] font-bold border uppercase"
                      :class="severityClass(evt.severity)">
                  {{ evt.severity }}
                </span>
              </td>
              <td class="px-5 py-3 text-gray-700 dark:text-zinc-300 text-xs truncate max-w-[150px]">{{ evt.email || '—' }}</td>
              <td class="px-5 py-3 text-gray-500 dark:text-zinc-500 font-mono text-xs">{{ evt.ip_address || '—' }}</td>
              <td class="px-5 py-3 text-gray-500 dark:text-zinc-400 text-xs">{{ evt.action_taken || '—' }}</td>
              <td class="px-5 py-3 text-gray-500 dark:text-zinc-500 text-xs whitespace-nowrap">{{ formatDate(evt.created_at) }}</td>
              <td class="px-5 py-3 text-center">
                <button v-if="!evt.resolved" @click="resolveEvent(evt.id)" 
                        class="px-2 py-1 text-[10px] font-medium rounded border text-blue-600 dark:text-blue-400 border-blue-200 dark:border-blue-800 hover:bg-blue-50 dark:hover:bg-blue-950/30 transition-all">
                  Resolver
                </button>
                <span v-else class="text-[10px] font-medium text-emerald-600 dark:text-emerald-400">Resuelto</span>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Loading overlay -->
    <div v-if="loading" class="fixed inset-0 bg-black/20 flex items-center justify-center z-50">
      <div class="bg-white dark:bg-zinc-900 rounded-xl p-6 shadow-xl border border-gray-200 dark:border-zinc-800">
        <div class="flex items-center gap-3">
          <svg class="animate-spin w-5 h-5 text-blue-600" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
          </svg>
          <span class="text-sm text-gray-700 dark:text-zinc-300 font-medium">Procesando...</span>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import axios from 'axios'

const emit = defineEmits(['notify'])

const loading = ref(false)
const data = ref({})
const kpis = computed(() => data.value.kpis || {})

const loadData = async () => {
  try {
    const response = await axios.get('/api/admin/security/dashboard')
    if (response.data.success) {
      data.value = response.data.data
    }
  } catch (e) {
    emit('notify', { type: 'error', message: 'Error cargando datos de seguridad' })
  }
}

const unblockUser = async (email) => {
  loading.value = true
  try {
    const res = await axios.post('/api/admin/security/unblock-user', { email })
    emit('notify', { type: res.data.success ? 'success' : 'error', message: res.data.message })
    if (res.data.success) await loadData()
  } catch (e) {
    emit('notify', { type: 'error', message: 'Error desbloqueando cuenta' })
  } finally {
    loading.value = false
  }
}

const unblockIp = async (ip) => {
  loading.value = true
  try {
    const res = await axios.post('/api/admin/security/unblock-ip', { ip })
    emit('notify', { type: res.data.success ? 'success' : 'error', message: res.data.message })
    if (res.data.success) await loadData()
  } catch (e) {
    emit('notify', { type: 'error', message: 'Error desbloqueando IP' })
  } finally {
    loading.value = false
  }
}

const resolveEvent = async (eventId) => {
  try {
    await axios.post('/api/admin/security/resolve-event', { event_id: eventId })
    await loadData()
  } catch (e) {
    emit('notify', { type: 'error', message: 'Error resolviendo evento' })
  }
}

const cleanupLogs = async () => {
  loading.value = true
  try {
    const res = await axios.post('/api/admin/security/cleanup')
    emit('notify', { type: 'success', message: res.data.message })
    await loadData()
  } catch (e) {
    emit('notify', { type: 'error', message: 'Error limpiando logs' })
  } finally {
    loading.value = false
  }
}

const formatDate = (d) => {
  if (!d) return '—'
  return new Date(d).toLocaleString('es-ES', { day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' })
}

const eventTypeLabel = (type) => {
  const map = {
    brute_force: 'Fuerza Bruta',
    ip_sweep: 'Escaneo IP',
    unusual_hour: 'Hora Inusual',
    risk_threshold: 'Riesgo Alto',
    account_blocked: 'Cuenta Bloqueada',
    account_unblocked: 'Desbloqueada',
    ip_blocked: 'IP Bloqueada',
  }
  return map[type] || type
}

const eventTypeClass = (type) => {
  if (['brute_force', 'ip_sweep'].includes(type)) return 'bg-rose-50 dark:bg-rose-950 text-rose-700 dark:text-rose-400 border-rose-100 dark:border-rose-800'
  if (type === 'unusual_hour') return 'bg-amber-50 dark:bg-amber-950 text-amber-700 dark:text-amber-400 border-amber-100 dark:border-amber-800'
  if (type === 'account_unblocked') return 'bg-emerald-50 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-400 border-emerald-100 dark:border-emerald-800'
  return 'bg-blue-50 dark:bg-blue-950 text-blue-700 dark:text-blue-400 border-blue-100 dark:border-blue-800'
}

const severityClass = (sev) => {
  const map = {
    critical: 'bg-rose-50 dark:bg-rose-950 text-rose-700 dark:text-rose-400 border-rose-100 dark:border-rose-800',
    high: 'bg-red-50 dark:bg-red-950 text-red-700 dark:text-red-400 border-red-100 dark:border-red-800',
    medium: 'bg-amber-50 dark:bg-amber-950 text-amber-700 dark:text-amber-400 border-amber-100 dark:border-amber-800',
    low: 'bg-blue-50 dark:bg-blue-950 text-blue-700 dark:text-blue-400 border-blue-100 dark:border-blue-800',
  }
  return map[sev] || map.low
}

// Expose loadData para que el padre pueda llamarlo
defineExpose({ loadData })
</script>
