<template>
  <div class="fixed inset-0 bg-black/70 flex items-center justify-center z-50 p-4 overflow-y-auto">
    <div class="bg-gray-900 rounded-2xl p-8 max-w-4xl w-full border border-gray-700 shadow-2xl my-8">
      <div class="flex items-center justify-between mb-6">
        <div>
          <h3 class="text-3xl font-bold text-white">{{ tenant.business_name }}</h3>
          <p class="text-sm text-gray-400 mt-1">ID: {{ tenant.id }}</p>
        </div>
        <button @click="$emit('close')" class="text-gray-400 hover:text-white">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
          </svg>
        </button>
      </div>

      <!-- Info General -->
      <div class="grid grid-cols-2 gap-4 mb-6">
        <div class="bg-gray-800 p-4 rounded-lg">
          <p class="text-xs text-gray-400 mb-1">Plan Actual</p>
          <div class="flex items-center justify-between">
            <span :class="getPlanBadge(tenant.plan)" class="px-3 py-1 text-xs font-bold rounded-full">
              {{ tenant.plan.toUpperCase() }}
            </span>
            <select @change="$emit('update-plan', tenant.id, $event.target.value)" :value="tenant.plan" class="px-3 py-1 bg-gray-700 text-white rounded text-xs border border-gray-600">
              <option value="free_trial">Free Trial</option>
              <option value="basic">Basic</option>
              <option value="premium">Premium</option>
              <option value="enterprise">Enterprise</option>
            </select>
          </div>
        </div>
        <div class="bg-gray-800 p-4 rounded-lg">
          <p class="text-xs text-gray-400 mb-1">Estado</p>
          <span :class="getStatusBadge(tenant.status)" class="px-3 py-1 text-xs font-bold rounded-full">
            {{ tenant.status === 'active' ? 'ACTIVO' : tenant.status === 'paused' ? 'PAUSADO' : 'SUSPENDIDO' }}
          </span>
        </div>
        <div class="bg-gray-800 p-4 rounded-lg">
          <p class="text-xs text-gray-400 mb-1">Dominio Principal</p>
          <p class="text-sm text-white font-mono">{{ tenant.primary_domain }}</p>
        </div>
        <div class="bg-gray-800 p-4 rounded-lg">
          <p class="text-xs text-gray-400 mb-1">Creado</p>
          <p class="text-sm text-white">{{ new Date(tenant.created_at).toLocaleString('es-ES') }}</p>
        </div>
      </div>

      <!-- Estadísticas -->
      <div v-if="tenant.stats && !tenant.stats.error" class="mb-6">
        <h4 class="text-lg font-bold text-white mb-4">Estadísticas de la Tienda</h4>
        <div class="grid grid-cols-4 gap-4">
          <div class="bg-gray-800 p-4 rounded-lg text-center">
            <p class="text-2xl font-bold text-emerald-400">{{ tenant.stats.total_sales || 0 }}</p>
            <p class="text-xs text-gray-400 mt-1">Ventas Totales</p>
          </div>
          <div class="bg-gray-800 p-4 rounded-lg text-center">
            <p class="text-2xl font-bold text-blue-400">{{ formatCurrency(tenant.stats.total_revenue || 0) }}</p>
            <p class="text-xs text-gray-400 mt-1">Ingresos</p>
          </div>
          <div class="bg-gray-800 p-4 rounded-lg text-center">
            <p class="text-2xl font-bold text-purple-400">{{ tenant.stats.total_products || 0 }}</p>
            <p class="text-xs text-gray-400 mt-1">Productos</p>
          </div>
          <div class="bg-gray-800 p-4 rounded-lg text-center">
            <p class="text-2xl font-bold text-amber-400">{{ tenant.stats.total_customers || 0 }}</p>
            <p class="text-xs text-gray-400 mt-1">Clientes</p>
          </div>
        </div>
      </div>

      <!-- Uso de IA -->
      <div class="mb-6" v-if="tenant.ai_usage">
        <h4 class="text-lg font-bold text-white mb-4 flex items-center justify-between">
          <span>Consumo de IA</span>
          <span v-if="tenant.ai_usage.limits" class="text-xs font-normal text-gray-400">
            Plan: {{ tenant.ai_usage.plan.toUpperCase() }}
          </span>
        </h4>

        <!-- Advertencias -->
        <div v-if="tenant.ai_usage.warnings && tenant.ai_usage.warnings.length > 0" class="mb-4 space-y-2">
          <div v-for="warning in tenant.ai_usage.warnings" :key="warning.type"
               :class="warning.severity === 'critical' ? 'bg-red-900/30 border-red-500' : 'bg-yellow-900/30 border-yellow-500'"
               class="p-3 rounded-lg border">
            <p class="text-sm font-semibold" :class="warning.severity === 'critical' ? 'text-red-400' : 'text-yellow-400'">
              ⚠️ {{ warning.message }}
            </p>
          </div>
        </div>

        <!-- Límites y Uso Actual -->
        <div v-if="tenant.ai_usage.limits && !tenant.ai_usage.limits.unlimited" class="mb-4 bg-gray-800 rounded-lg p-4">
          <p class="text-xs text-gray-400 mb-3 font-semibold uppercase">Límites del Plan</p>
          <div class="grid grid-cols-2 gap-3">
            <div>
              <p class="text-xs text-gray-500">Peticiones/Hora</p>
              <p class="text-sm font-bold text-white">{{ tenant.ai_usage.limits.limits.requests_per_hour }}</p>
            </div>
            <div>
              <p class="text-xs text-gray-500">Peticiones/Día</p>
              <p class="text-sm font-bold text-white">{{ tenant.ai_usage.limits.limits.requests_per_day }}</p>
            </div>
            <div>
              <p class="text-xs text-gray-500">Tokens/Petición</p>
              <p class="text-sm font-bold text-white">{{ formatNumber(tenant.ai_usage.limits.limits.tokens_per_request) }}</p>
            </div>
            <div>
              <p class="text-xs text-gray-500">Tokens/Día</p>
              <p class="text-sm font-bold text-white">{{ formatNumber(tenant.ai_usage.limits.limits.tokens_per_day) }}</p>
            </div>
          </div>
        </div>

        <!-- Uso en Última Hora -->
        <div class="mb-4" v-if="tenant.ai_usage.usage">
          <p class="text-sm text-gray-400 mb-2 font-semibold">Última Hora</p>
          <div class="grid grid-cols-3 gap-4">
            <div class="bg-gray-800 p-3 rounded-lg text-center">
              <p class="text-xl font-bold text-blue-400">{{ tenant.ai_usage.usage.last_hour.requests }}</p>
              <p class="text-xs text-gray-400 mt-1">Peticiones</p>
            </div>
            <div class="bg-gray-800 p-3 rounded-lg text-center">
              <p class="text-xl font-bold text-purple-400">{{ formatNumber(tenant.ai_usage.usage.last_hour.tokens) }}</p>
              <p class="text-xs text-gray-400 mt-1">Tokens</p>
            </div>
            <div class="bg-gray-800 p-3 rounded-lg text-center">
              <p class="text-xl font-bold" :class="tenant.ai_usage.usage.last_hour.remaining_requests === 0 ? 'text-red-400' : 'text-emerald-400'">
                {{ typeof tenant.ai_usage.usage.last_hour.remaining_requests === 'number' ? tenant.ai_usage.usage.last_hour.remaining_requests : '∞' }}
              </p>
              <p class="text-xs text-gray-400 mt-1">Restantes</p>
            </div>
          </div>
        </div>

        <!-- Uso Hoy -->
        <div class="mb-4" v-if="tenant.ai_usage.usage">
          <p class="text-sm text-gray-400 mb-2 font-semibold">Hoy</p>
          <div class="grid grid-cols-4 gap-4">
            <div class="bg-gray-800 p-3 rounded-lg text-center">
              <p class="text-xl font-bold text-blue-400">{{ tenant.ai_usage.usage.today.requests }}</p>
              <p class="text-xs text-gray-400 mt-1">Peticiones</p>
            </div>
            <div class="bg-gray-800 p-3 rounded-lg text-center">
              <p class="text-xl font-bold text-purple-400">{{ formatNumber(tenant.ai_usage.usage.today.tokens) }}</p>
              <p class="text-xs text-gray-400 mt-1">Tokens</p>
            </div>
            <div class="bg-gray-800 p-3 rounded-lg text-center">
              <p class="text-xl font-bold text-amber-400">${{ tenant.ai_usage.usage.today.cost.toFixed(4) }}</p>
              <p class="text-xs text-gray-400 mt-1">Costo</p>
            </div>
            <div class="bg-gray-800 p-3 rounded-lg text-center">
              <p class="text-xl font-bold" :class="tenant.ai_usage.usage.today.remaining_requests === 0 ? 'text-red-400' : 'text-emerald-400'">
                {{ typeof tenant.ai_usage.usage.today.remaining_requests === 'number' ? tenant.ai_usage.usage.today.remaining_requests : '∞' }}
              </p>
              <p class="text-xs text-gray-400 mt-1">Restantes</p>
            </div>
          </div>
        </div>

        <!-- Total Histórico -->
        <div v-if="tenant.ai_usage.usage">
          <p class="text-sm text-gray-400 mb-2 font-semibold">Total Histórico</p>
          <div class="grid grid-cols-3 gap-4">
            <div class="bg-gray-800 p-4 rounded-lg text-center">
              <p class="text-2xl font-bold text-white">{{ tenant.ai_usage.usage.total.requests }}</p>
              <p class="text-xs text-gray-400 mt-1">Peticiones</p>
            </div>
            <div class="bg-gray-800 p-4 rounded-lg text-center">
              <p class="text-2xl font-bold text-white">{{ formatNumber(tenant.ai_usage.usage.total.tokens) }}</p>
              <p class="text-xs text-gray-400 mt-1">Tokens</p>
            </div>
            <div class="bg-gray-800 p-4 rounded-lg text-center">
              <p class="text-2xl font-bold text-white">${{ tenant.ai_usage.usage.total.cost.toFixed(4) }}</p>
              <p class="text-xs text-gray-400 mt-1">Costo Total</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Acciones -->
      <div class="flex space-x-3 pt-4 border-t border-gray-800">
        <a :href="'http://' + tenant.primary_domain + '/login'" target="_blank" class="flex-1 px-6 py-3 bg-emerald-600 hover:bg-emerald-700 text-white text-center rounded-lg font-bold transition-colors">
          Entrar a la Tienda
        </a>
        <button @click="$emit('toggle-status', tenant.id, tenant.status)" :class="tenant.status === 'active' ? 'bg-amber-600 hover:bg-amber-700' : 'bg-emerald-600 hover:bg-emerald-700'" class="flex-1 px-6 py-3 text-white rounded-lg font-bold transition-colors">
          {{ tenant.status === 'active' ? 'Pausar Tienda' : 'Activar Tienda' }}
        </button>
        <button @click="$emit('close')" class="px-6 py-3 bg-gray-700 hover:bg-gray-600 text-white rounded-lg font-semibold transition-colors">
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

const formatNumber = (num) => new Intl.NumberFormat('es-ES').format(num)
const formatCurrency = (num) => '$' + new Intl.NumberFormat('es-ES', { minimumFractionDigits: 2 }).format(num)

const getStatusBadge = (status) => {
  const badges = {
    'active': 'bg-emerald-500/20 text-emerald-400',
    'paused': 'bg-amber-500/20 text-amber-400',
    'suspended': 'bg-red-500/20 text-red-400'
  }
  return badges[status] || badges.active
}

const getPlanBadge = (plan) => {
  const badges = {
    'free_trial': 'bg-gray-700 text-gray-300',
    'basic': 'bg-blue-600 text-white',
    'premium': 'bg-purple-600 text-white',
    'enterprise': 'bg-amber-600 text-white'
  }
  return badges[plan] || badges.free_trial
}
</script>
