<template>
  <div class="fixed inset-0 bg-black/50 dark:bg-black/70 flex items-center justify-center z-50 p-4 overflow-y-auto ">
    <div class="bg-white dark:bg-zinc-900 rounded-2xl w-full max-w-4xl border border-gray-200 dark:border-zinc-800 shadow-2xl dark:shadow-black/50 my-8">
      <!-- Header -->
      <div class="px-6 py-5 border-b border-gray-200 dark:border-zinc-800 flex items-start justify-between">
        <div class="flex items-center gap-4">
          <div class="w-14 h-14 rounded-xl bg-gradient-to-br from-slate-100 to-slate-200 dark:from-zinc-700 dark:to-zinc-800 flex items-center justify-center flex-shrink-0 border border-gray-200 dark:border-zinc-700">
            <span class="text-xl font-bold text-slate-600 dark:text-zinc-300">{{ (tenant.business_name || tenant.name || 'N')[0].toUpperCase() }}</span>
          </div>
          <div>
            <h3 class="text-xl font-bold text-gray-900 dark:text-white">{{ tenant.business_name || tenant.name }}</h3>
            <p class="text-sm text-gray-500 dark:text-zinc-400 font-mono mt-0.5">{{ tenant.id }}</p>
          </div>
        </div>
        <button @click="$emit('close')" class="p-2 text-gray-400 dark:text-zinc-500 hover:text-gray-600 dark:hover:text-zinc-300 hover:bg-gray-100 dark:hover:bg-zinc-800 rounded-lg transition-all">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
          </svg>
        </button>
      </div>

      <div class="p-6 space-y-6 max-h-[70vh] overflow-y-auto">
        <!-- Info General -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
          <div class="bg-gray-50 dark:bg-zinc-800/50 p-4 rounded-xl border border-gray-100 dark:border-zinc-700/50">
            <p class="text-xs text-gray-500 dark:text-zinc-400 font-medium mb-2">Plan Actual</p>
            <div class="flex items-center gap-2">
              <span :class="getPlanBadge(tenant.plan)" class="px-2.5 py-1 text-xs font-bold rounded-lg border">
                {{ (tenant.plan || 'N/A').toUpperCase().replace('_', ' ') }}
              </span>
            </div>
            <select @change="$emit('update-plan', tenant.id, $event.target.value)" :value="tenant.plan" class="mt-3 w-full px-3 py-2 bg-white dark:bg-zinc-800 text-gray-700 dark:text-zinc-300 rounded-lg text-xs border border-gray-200 dark:border-zinc-700 focus:ring-2 focus:ring-blue-500/30">
              <option value="free_trial">Free Trial</option>
              <option value="basic">Basic</option>
              <option value="premium">Premium</option>
              <option value="enterprise">Enterprise</option>
            </select>
          </div>
          <div class="bg-gray-50 dark:bg-zinc-800/50 p-4 rounded-xl border border-gray-100 dark:border-zinc-700/50">
            <p class="text-xs text-gray-500 dark:text-zinc-400 font-medium mb-2">Estado</p>
            <div class="flex items-center gap-2">
              <span 
                class="w-2.5 h-2.5 rounded-full"
                :class="{
                  'bg-emerald-500': tenant.status === 'active',
                  'bg-amber-500': tenant.status === 'paused',
                  'bg-rose-500': tenant.status === 'suspended'
                }"
              ></span>
              <span :class="getStatusBadge(tenant.status)" class="px-2.5 py-1 text-xs font-bold rounded-lg border">
                {{ tenant.status === 'active' ? 'ACTIVO' : tenant.status === 'paused' ? 'PAUSADO' : 'SUSPENDIDO' }}
              </span>
            </div>
          </div>
          <div class="bg-gray-50 dark:bg-zinc-800/50 p-4 rounded-xl border border-gray-100 dark:border-zinc-700/50">
            <p class="text-xs text-gray-500 dark:text-zinc-400 font-medium mb-2">Dominio</p>
            <a :href="'https://' + tenant.primary_domain" target="_blank" class="text-sm text-blue-600 dark:text-blue-400 hover:underline font-medium break-all">
              {{ tenant.primary_domain }}
            </a>
          </div>
          <div class="bg-gray-50 dark:bg-zinc-800/50 p-4 rounded-xl border border-gray-100 dark:border-zinc-700/50">
            <p class="text-xs text-gray-500 dark:text-zinc-400 font-medium mb-2">Creado</p>
            <p class="text-sm text-gray-700 dark:text-zinc-300 font-medium">{{ new Date(tenant.created_at).toLocaleDateString('es-ES') }}</p>
            <p class="text-xs text-gray-400 dark:text-zinc-500 mt-0.5">{{ new Date(tenant.created_at).toLocaleTimeString('es-ES', { hour: '2-digit', minute: '2-digit' }) }}</p>
          </div>
        </div>

        <!-- Estadísticas -->
        <div v-if="tenant.stats && !tenant.stats.error">
          <h4 class="text-sm font-bold text-gray-900 dark:text-white mb-3 flex items-center gap-2">
            <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
            </svg>
            Estadísticas de la Tienda
          </h4>
          <div class="grid grid-cols-4 gap-3">
            <div class="bg-emerald-50 dark:bg-emerald-950/30 p-4 rounded-xl text-center border border-emerald-100 dark:border-emerald-900/50">
              <p class="text-2xl font-bold text-emerald-600 dark:text-emerald-400">{{ tenant.stats.total_sales || 0 }}</p>
              <p class="text-xs text-emerald-600/70 dark:text-emerald-400/70 mt-1 font-medium">Ventas Totales</p>
            </div>
            <div class="bg-blue-50 dark:bg-blue-950/30 p-4 rounded-xl text-center border border-blue-100 dark:border-blue-900/50">
              <p class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ formatCurrency(tenant.stats.total_revenue || 0) }}</p>
              <p class="text-xs text-blue-600/70 dark:text-blue-400/70 mt-1 font-medium">Ingresos</p>
            </div>
            <div class="bg-purple-50 dark:bg-purple-950/30 p-4 rounded-xl text-center border border-purple-100 dark:border-purple-900/50">
              <p class="text-2xl font-bold text-purple-600 dark:text-purple-400">{{ tenant.stats.total_products || 0 }}</p>
              <p class="text-xs text-purple-600/70 dark:text-purple-400/70 mt-1 font-medium">Productos</p>
            </div>
            <div class="bg-amber-50 dark:bg-amber-950/30 p-4 rounded-xl text-center border border-amber-100 dark:border-amber-900/50">
              <p class="text-2xl font-bold text-amber-600 dark:text-amber-400">{{ tenant.stats.total_customers || 0 }}</p>
              <p class="text-xs text-amber-600/70 dark:text-amber-400/70 mt-1 font-medium">Clientes</p>
            </div>
          </div>
        </div>

        <!-- Uso de IA -->
        <div v-if="tenant.ai_usage">
          <h4 class="text-sm font-bold text-gray-900 dark:text-white mb-3 flex items-center justify-between">
            <span class="flex items-center gap-2">
              <svg class="w-4 h-4 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
              </svg>
              Consumo de IA
            </span>
            <span v-if="tenant.ai_usage.limits" class="px-2 py-0.5 bg-gray-100 dark:bg-zinc-800 text-xs font-medium text-gray-600 dark:text-zinc-400 rounded-md">
              Plan: {{ (tenant.ai_usage.plan || '').toUpperCase() }}
            </span>
          </h4>

          <!-- Advertencias -->
          <div v-if="tenant.ai_usage.warnings && tenant.ai_usage.warnings.length > 0" class="mb-4 space-y-2">
            <div v-for="warning in tenant.ai_usage.warnings" :key="warning.type"
                 :class="warning.severity === 'critical' ? 'bg-rose-50 dark:bg-rose-950/30 border-rose-200 dark:border-rose-900/50' : 'bg-amber-50 dark:bg-amber-950/30 border-amber-200 dark:border-amber-900/50'"
                 class="p-3 rounded-xl border">
              <p class="text-sm font-semibold" :class="warning.severity === 'critical' ? 'text-rose-600 dark:text-rose-400' : 'text-amber-600 dark:text-amber-400'">
                ⚠️ {{ warning.message }}
              </p>
            </div>
          </div>

          <!-- Límites y Uso Actual -->
          <div v-if="tenant.ai_usage.limits && !tenant.ai_usage.limits.unlimited" class="mb-4 bg-gray-50 dark:bg-zinc-800/50 rounded-xl p-4 border border-gray-100 dark:border-zinc-700/50">
            <p class="text-xs text-gray-500 dark:text-zinc-400 mb-3 font-bold uppercase tracking-wide">Límites del Plan</p>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
              <div>
                <p class="text-xs text-gray-400 dark:text-zinc-500">Peticiones/Hora</p>
                <p class="text-sm font-bold text-gray-700 dark:text-zinc-300">{{ tenant.ai_usage.limits.limits.requests_per_hour }}</p>
              </div>
              <div>
                <p class="text-xs text-gray-400 dark:text-zinc-500">Peticiones/Día</p>
                <p class="text-sm font-bold text-gray-700 dark:text-zinc-300">{{ tenant.ai_usage.limits.limits.requests_per_day }}</p>
              </div>
              <div>
                <p class="text-xs text-gray-400 dark:text-zinc-500">Tokens/Petición</p>
                <p class="text-sm font-bold text-gray-700 dark:text-zinc-300">{{ formatNumber(tenant.ai_usage.limits.limits.tokens_per_request) }}</p>
              </div>
              <div>
                <p class="text-xs text-gray-400 dark:text-zinc-500">Tokens/Día</p>
                <p class="text-sm font-bold text-gray-700 dark:text-zinc-300">{{ formatNumber(tenant.ai_usage.limits.limits.tokens_per_day) }}</p>
              </div>
            </div>
          </div>

          <!-- Uso en Última Hora -->
          <div class="mb-4" v-if="tenant.ai_usage.usage">
            <p class="text-xs text-gray-500 dark:text-zinc-400 mb-2 font-bold uppercase tracking-wide">Última Hora</p>
            <div class="grid grid-cols-3 gap-3">
              <div class="bg-blue-50 dark:bg-blue-950/20 p-3 rounded-xl text-center border border-blue-100 dark:border-blue-900/30">
                <p class="text-xl font-bold text-blue-600 dark:text-blue-400">{{ tenant.ai_usage.usage.last_hour.requests }}</p>
                <p class="text-xs text-blue-600/70 dark:text-blue-400/70 mt-1">Peticiones</p>
              </div>
              <div class="bg-purple-50 dark:bg-purple-950/20 p-3 rounded-xl text-center border border-purple-100 dark:border-purple-900/30">
                <p class="text-xl font-bold text-purple-600 dark:text-purple-400">{{ formatNumber(tenant.ai_usage.usage.last_hour.tokens) }}</p>
                <p class="text-xs text-purple-600/70 dark:text-purple-400/70 mt-1">Tokens</p>
              </div>
              <div class="p-3 rounded-xl text-center border"
                   :class="tenant.ai_usage.usage.last_hour.remaining_requests === 0 
                     ? 'bg-rose-50 dark:bg-rose-950/20 border-rose-100 dark:border-rose-900/30' 
                     : 'bg-emerald-50 dark:bg-emerald-950/20 border-emerald-100 dark:border-emerald-900/30'">
                <p class="text-xl font-bold" :class="tenant.ai_usage.usage.last_hour.remaining_requests === 0 ? 'text-rose-600 dark:text-rose-400' : 'text-emerald-600 dark:text-emerald-400'">
                  {{ typeof tenant.ai_usage.usage.last_hour.remaining_requests === 'number' ? tenant.ai_usage.usage.last_hour.remaining_requests : '∞' }}
                </p>
                <p class="text-xs mt-1" :class="tenant.ai_usage.usage.last_hour.remaining_requests === 0 ? 'text-rose-600/70 dark:text-rose-400/70' : 'text-emerald-600/70 dark:text-emerald-400/70'">Restantes</p>
              </div>
            </div>
          </div>

          <!-- Uso Hoy -->
          <div class="mb-4" v-if="tenant.ai_usage.usage">
            <p class="text-xs text-gray-500 dark:text-zinc-400 mb-2 font-bold uppercase tracking-wide">Hoy</p>
            <div class="grid grid-cols-4 gap-3">
              <div class="bg-gray-50 dark:bg-zinc-800/50 p-3 rounded-xl text-center border border-gray-100 dark:border-zinc-700/50">
                <p class="text-xl font-bold text-gray-700 dark:text-zinc-300">{{ tenant.ai_usage.usage.today.requests }}</p>
                <p class="text-xs text-gray-500 dark:text-zinc-500 mt-1">Peticiones</p>
              </div>
              <div class="bg-gray-50 dark:bg-zinc-800/50 p-3 rounded-xl text-center border border-gray-100 dark:border-zinc-700/50">
                <p class="text-xl font-bold text-gray-700 dark:text-zinc-300">{{ formatNumber(tenant.ai_usage.usage.today.tokens) }}</p>
                <p class="text-xs text-gray-500 dark:text-zinc-500 mt-1">Tokens</p>
              </div>
              <div class="bg-gray-50 dark:bg-zinc-800/50 p-3 rounded-xl text-center border border-gray-100 dark:border-zinc-700/50">
                <p class="text-xl font-bold text-amber-600 dark:text-amber-400">${{ tenant.ai_usage.usage.today.cost.toFixed(4) }}</p>
                <p class="text-xs text-gray-500 dark:text-zinc-500 mt-1">Costo</p>
              </div>
              <div class="p-3 rounded-xl text-center border"
                   :class="tenant.ai_usage.usage.today.remaining_requests === 0 
                     ? 'bg-rose-50 dark:bg-rose-950/20 border-rose-100 dark:border-rose-900/30' 
                     : 'bg-emerald-50 dark:bg-emerald-950/20 border-emerald-100 dark:border-emerald-900/30'">
                <p class="text-xl font-bold" :class="tenant.ai_usage.usage.today.remaining_requests === 0 ? 'text-rose-600 dark:text-rose-400' : 'text-emerald-600 dark:text-emerald-400'">
                  {{ typeof tenant.ai_usage.usage.today.remaining_requests === 'number' ? tenant.ai_usage.usage.today.remaining_requests : '∞' }}
                </p>
                <p class="text-xs mt-1" :class="tenant.ai_usage.usage.today.remaining_requests === 0 ? 'text-rose-600/70 dark:text-rose-400/70' : 'text-emerald-600/70 dark:text-emerald-400/70'">Restantes</p>
              </div>
            </div>
          </div>

          <!-- Total Histórico -->
          <div v-if="tenant.ai_usage.usage">
            <p class="text-xs text-gray-500 dark:text-zinc-400 mb-2 font-bold uppercase tracking-wide">Total Histórico</p>
            <div class="grid grid-cols-3 gap-3">
              <div class="bg-slate-100 dark:bg-zinc-800 p-4 rounded-xl text-center border border-slate-200 dark:border-zinc-700">
                <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ tenant.ai_usage.usage.total.requests }}</p>
                <p class="text-xs text-gray-500 dark:text-zinc-400 mt-1">Peticiones</p>
              </div>
              <div class="bg-slate-100 dark:bg-zinc-800 p-4 rounded-xl text-center border border-slate-200 dark:border-zinc-700">
                <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ formatNumber(tenant.ai_usage.usage.total.tokens) }}</p>
                <p class="text-xs text-gray-500 dark:text-zinc-400 mt-1">Tokens</p>
              </div>
              <div class="bg-slate-100 dark:bg-zinc-800 p-4 rounded-xl text-center border border-slate-200 dark:border-zinc-700">
                <p class="text-2xl font-bold text-gray-900 dark:text-white">${{ tenant.ai_usage.usage.total.cost.toFixed(4) }}</p>
                <p class="text-xs text-gray-500 dark:text-zinc-400 mt-1">Costo Total</p>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Acciones -->
      <div class="px-6 py-4 border-t border-gray-200 dark:border-zinc-800 flex gap-3">
        <a :href="'https://' + tenant.primary_domain + '/login'" target="_blank" class="flex-1 px-6 py-3 bg-emerald-600 hover:bg-emerald-700 text-white text-center rounded-xl font-bold transition-colors flex items-center justify-center gap-2">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
          </svg>
          Entrar a la Tienda
        </a>
        <button @click="$emit('toggle-status', tenant.id, tenant.status)" :class="tenant.status === 'active' ? 'bg-amber-500 hover:bg-amber-600' : 'bg-emerald-600 hover:bg-emerald-700'" class="flex-1 px-6 py-3 text-white rounded-xl font-bold transition-colors">
          {{ tenant.status === 'active' ? 'Pausar Tienda' : 'Activar Tienda' }}
        </button>
        <button @click="$emit('close')" class="px-6 py-3 bg-white dark:bg-zinc-800 hover:bg-gray-50 dark:hover:bg-zinc-700 text-gray-700 dark:text-zinc-300 rounded-xl font-semibold transition-colors border border-gray-200 dark:border-zinc-700">
          Cerrar
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
defineProps({
  tenant: {
    type: Object,
    required: true
  }
})

defineEmits(['close', 'update-plan', 'toggle-status'])

const formatNumber = (num) => {
  if (num == null || isNaN(num)) return '0'
  return new Intl.NumberFormat('es-ES').format(num)
}
const formatCurrency = (num) => {
  if (num == null || isNaN(num)) return '$0.00'
  return '$' + new Intl.NumberFormat('es-ES', { minimumFractionDigits: 2 }).format(num)
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
    'free_trial': 'bg-gray-100 dark:bg-zinc-800 text-gray-700 dark:text-zinc-300 border-gray-200 dark:border-zinc-700',
    'basic': 'bg-blue-50 dark:bg-blue-950 text-blue-700 dark:text-blue-400 border-blue-100 dark:border-blue-800',
    'premium': 'bg-purple-50 dark:bg-purple-950 text-purple-700 dark:text-purple-400 border-purple-100 dark:border-purple-800',
    'enterprise': 'bg-amber-50 dark:bg-amber-950 text-amber-700 dark:text-amber-400 border-amber-100 dark:border-amber-800'
  }
  return badges[plan] || badges.free_trial
}
</script>
