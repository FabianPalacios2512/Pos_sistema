<template>
  <div class="fixed inset-0 bg-black/40 dark:bg-black/60 flex items-start justify-center z-50 p-4 overflow-y-auto backdrop-blur-[2px]">
    <div class="bg-white dark:bg-zinc-900 rounded-xl w-full max-w-3xl border border-gray-200 dark:border-zinc-800 shadow-2xl dark:shadow-black/50 my-8">
      
      <!-- Header - Store Identity -->
      <div class="px-6 py-5 border-b border-gray-100 dark:border-zinc-800">
        <div class="flex items-start justify-between">
          <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-gray-100 dark:bg-zinc-800 flex items-center justify-center flex-shrink-0">
              <span class="text-lg font-bold text-gray-500 dark:text-zinc-400">{{ (tenant.business_name || tenant.name || 'N')[0].toUpperCase() }}</span>
            </div>
            <div>
              <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ tenant.business_name || tenant.name }}</h3>
              <p class="text-xs text-gray-400 dark:text-zinc-500 font-mono mt-0.5">{{ tenant.id }}</p>
            </div>
          </div>
          <div class="flex items-center gap-2">
            <span 
              class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-xs font-semibold border"
              :class="getStatusClasses(tenant.status)"
            >
              <span class="w-1.5 h-1.5 rounded-full" :class="getStatusDot(tenant.status)"></span>
              {{ tenant.status === 'active' ? 'Activo' : tenant.status === 'paused' ? 'Pausado' : 'Suspendido' }}
            </span>
            <button @click="$emit('close')" class="p-1.5 text-gray-400 dark:text-zinc-500 hover:text-gray-600 dark:hover:text-zinc-300 hover:bg-gray-100 dark:hover:bg-zinc-800 rounded-lg transition-all">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
              </svg>
            </button>
          </div>
        </div>
      </div>

      <!-- Content -->
      <div class="max-h-[75vh] overflow-y-auto">
        
        <!-- Section: Titular de la Cuenta -->
        <div class="px-6 py-5 border-b border-gray-100 dark:border-zinc-800">
          <p class="text-[10px] font-semibold text-gray-400 dark:text-zinc-500 uppercase tracking-wider mb-4">Titular de la Cuenta</p>
          <div class="grid grid-cols-2 gap-x-8 gap-y-4">
            <div>
              <p class="text-[11px] text-gray-400 dark:text-zinc-500 mb-0.5">Nombre del Propietario</p>
              <p class="text-sm font-medium text-gray-900 dark:text-white">{{ tenant.owner_name || 'â€”' }}</p>
            </div>
            <div>
              <p class="text-[11px] text-gray-400 dark:text-zinc-500 mb-0.5">CÃ©dula / NIT</p>
              <p class="text-sm font-medium text-gray-900 dark:text-white">{{ tenant.cedula || 'â€”' }}</p>
            </div>
            <div>
              <p class="text-[11px] text-gray-400 dark:text-zinc-500 mb-0.5">Email del Administrador</p>
              <p class="text-sm font-medium text-gray-900 dark:text-white">{{ tenant.admin_email || 'â€”' }}</p>
            </div>
            <div>
              <p class="text-[11px] text-gray-400 dark:text-zinc-500 mb-0.5">TelÃ©fono</p>
              <p class="text-sm font-medium text-gray-900 dark:text-white">{{ tenant.admin_phone || 'â€”' }}</p>
            </div>
          </div>
        </div>

        <!-- Section: SuscripciÃ³n -->
        <div class="px-6 py-5 border-b border-gray-100 dark:border-zinc-800">
          <p class="text-[10px] font-semibold text-gray-400 dark:text-zinc-500 uppercase tracking-wider mb-4">SuscripciÃ³n</p>
          <div class="grid grid-cols-2 lg:grid-cols-4 gap-x-8 gap-y-4">
            <div>
              <p class="text-[11px] text-gray-400 dark:text-zinc-500 mb-0.5">Plan Actual</p>
              <div class="flex items-center gap-2">
                <span 
                  class="px-2 py-0.5 text-[11px] font-semibold rounded border"
                  :class="getPlanClasses(tenant.plan)"
                >{{ (tenant.plan || 'N/A').toUpperCase().replace('_', ' ') }}</span>
              </div>
            </div>
            <div>
              <p class="text-[11px] text-gray-400 dark:text-zinc-500 mb-0.5">Dominio</p>
              <a :href="'https://' + (tenant.primary_domain || tenant.domain)" target="_blank" rel="noopener noreferrer" class="text-sm font-medium text-blue-600 dark:text-blue-400 hover:underline break-all">
                {{ tenant.primary_domain || tenant.domain || 'â€”' }}
              </a>
            </div>
            <div>
              <p class="text-[11px] text-gray-400 dark:text-zinc-500 mb-0.5">Fecha de Registro</p>
              <p class="text-sm font-medium text-gray-900 dark:text-white">{{ formatDate(tenant.created_at) }}</p>
            </div>
            <div>
              <p class="text-[11px] text-gray-400 dark:text-zinc-500 mb-0.5">Vencimiento</p>
              <p class="text-sm font-medium" :class="isExpiringSoon ? 'text-amber-600 dark:text-amber-400' : 'text-gray-900 dark:text-white'">
                {{ formatDate(tenant.subscription_end) }}
              </p>
              <p v-if="daysRemaining !== null" class="text-[10px] mt-0.5" :class="daysRemaining <= 7 ? 'text-amber-500' : 'text-gray-400 dark:text-zinc-500'">
                {{ daysRemaining > 0 ? daysRemaining + ' dÃ­as restantes' : 'Vencido' }}
              </p>
            </div>
          </div>

          <!-- Cambiar Plan -->
          <div class="mt-4 pt-4 border-t border-gray-100 dark:border-zinc-800 flex items-center gap-3">
            <p class="text-[11px] text-gray-400 dark:text-zinc-500 flex-shrink-0">Cambiar plan:</p>
            <select 
              @change="$emit('update-plan', tenant.id, $event.target.value)" 
              :value="tenant.plan" 
              class="px-3 py-1.5 bg-white dark:bg-zinc-800 text-gray-700 dark:text-zinc-300 rounded-lg text-xs border border-gray-200 dark:border-zinc-700 focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500"
            >
              <option value="free_trial">Free Trial</option>
              <option value="basic">Basic</option>
              <option value="premium">Premium</option>
              <option value="enterprise">Enterprise</option>
            </select>
          </div>
        </div>

        <!-- Section: MÃ©tricas de la Tienda -->
        <div class="px-6 py-5 border-b border-gray-100 dark:border-zinc-800" v-if="tenant.stats && !tenant.stats.error">
          <p class="text-[10px] font-semibold text-gray-400 dark:text-zinc-500 uppercase tracking-wider mb-4">MÃ©tricas de la Tienda</p>
          <div class="grid grid-cols-2 lg:grid-cols-5 gap-3">
            <div class="bg-gray-50 dark:bg-zinc-800/50 rounded-lg p-3 text-center">
              <p class="text-xl font-semibold text-gray-900 dark:text-white">{{ tenant.stats.total_sales || 0 }}</p>
              <p class="text-[10px] text-gray-400 dark:text-zinc-500 mt-0.5">Ventas</p>
            </div>
            <div class="bg-gray-50 dark:bg-zinc-800/50 rounded-lg p-3 text-center">
              <p class="text-xl font-semibold text-gray-900 dark:text-white">{{ formatCurrency(tenant.stats.total_revenue || 0) }}</p>
              <p class="text-[10px] text-gray-400 dark:text-zinc-500 mt-0.5">Ingresos</p>
            </div>
            <div class="bg-gray-50 dark:bg-zinc-800/50 rounded-lg p-3 text-center">
              <p class="text-xl font-semibold text-gray-900 dark:text-white">{{ tenant.stats.total_products || 0 }}</p>
              <p class="text-[10px] text-gray-400 dark:text-zinc-500 mt-0.5">Productos</p>
            </div>
            <div class="bg-gray-50 dark:bg-zinc-800/50 rounded-lg p-3 text-center">
              <p class="text-xl font-semibold text-gray-900 dark:text-white">{{ tenant.stats.total_customers || 0 }}</p>
              <p class="text-[10px] text-gray-400 dark:text-zinc-500 mt-0.5">Clientes</p>
            </div>
            <div class="bg-gray-50 dark:bg-zinc-800/50 rounded-lg p-3 text-center">
              <p class="text-xl font-semibold text-gray-900 dark:text-white">{{ tenant.stats.total_users || 0 }}</p>
              <p class="text-[10px] text-gray-400 dark:text-zinc-500 mt-0.5">Usuarios</p>
            </div>
          </div>
        </div>

        <!-- Section: Uso de IA -->
        <div class="px-6 py-5 border-b border-gray-100 dark:border-zinc-800" v-if="tenant.ai_usage">
          <div class="flex items-center justify-between mb-4">
            <p class="text-[10px] font-semibold text-gray-400 dark:text-zinc-500 uppercase tracking-wider">Consumo de IA</p>
            <span v-if="tenant.ai_usage.plan" class="text-[10px] font-medium text-gray-400 dark:text-zinc-500 bg-gray-100 dark:bg-zinc-800 px-2 py-0.5 rounded">
              {{ (tenant.ai_usage.plan || '').toUpperCase() }}
            </span>
          </div>

          <!-- Warnings -->
          <div v-if="tenant.ai_usage.warnings && tenant.ai_usage.warnings.length > 0" class="mb-4 space-y-2">
            <div v-for="warning in tenant.ai_usage.warnings" :key="warning.type"
                 class="px-3 py-2 rounded-lg text-xs font-medium border"
                 :class="warning.severity === 'critical' ? 'bg-rose-50 dark:bg-rose-950/30 border-rose-200 dark:border-rose-900/50 text-rose-600 dark:text-rose-400' : 'bg-amber-50 dark:bg-amber-950/30 border-amber-200 dark:border-amber-900/50 text-amber-600 dark:text-amber-400'"
            >{{ warning.message }}</div>
          </div>

          <!-- Limits -->
          <div v-if="tenant.ai_usage.limits && !tenant.ai_usage.limits.unlimited" class="grid grid-cols-4 gap-3 mb-4">
            <div>
              <p class="text-[10px] text-gray-400 dark:text-zinc-500">Peticiones/Hora</p>
              <p class="text-sm font-medium text-gray-900 dark:text-white">{{ tenant.ai_usage.limits.limits.requests_per_hour }}</p>
            </div>
            <div>
              <p class="text-[10px] text-gray-400 dark:text-zinc-500">Peticiones/DÃ­a</p>
              <p class="text-sm font-medium text-gray-900 dark:text-white">{{ tenant.ai_usage.limits.limits.requests_per_day }}</p>
            </div>
            <div>
              <p class="text-[10px] text-gray-400 dark:text-zinc-500">Tokens/PeticiÃ³n</p>
              <p class="text-sm font-medium text-gray-900 dark:text-white">{{ formatNumber(tenant.ai_usage.limits.limits.tokens_per_request) }}</p>
            </div>
            <div>
              <p class="text-[10px] text-gray-400 dark:text-zinc-500">Tokens/DÃ­a</p>
              <p class="text-sm font-medium text-gray-900 dark:text-white">{{ formatNumber(tenant.ai_usage.limits.limits.tokens_per_day) }}</p>
            </div>
          </div>

          <!-- Usage Stats -->
          <div v-if="tenant.ai_usage.usage" class="grid grid-cols-2 gap-4">
            <div class="bg-gray-50 dark:bg-zinc-800/50 rounded-lg p-3">
              <p class="text-[10px] font-semibold text-gray-400 dark:text-zinc-500 uppercase tracking-wider mb-2">Ãšltima Hora</p>
              <div class="space-y-1.5">
                <div class="flex justify-between"><span class="text-[11px] text-gray-500 dark:text-zinc-400">Peticiones</span><span class="text-xs font-semibold text-gray-900 dark:text-white">{{ tenant.ai_usage.usage.last_hour.requests }}</span></div>
                <div class="flex justify-between"><span class="text-[11px] text-gray-500 dark:text-zinc-400">Tokens</span><span class="text-xs font-semibold text-gray-900 dark:text-white">{{ formatNumber(tenant.ai_usage.usage.last_hour.tokens) }}</span></div>
                <div class="flex justify-between">
                  <span class="text-[11px] text-gray-500 dark:text-zinc-400">Restantes</span>
                  <span class="text-xs font-semibold" :class="tenant.ai_usage.usage.last_hour.remaining_requests === 0 ? 'text-rose-600 dark:text-rose-400' : 'text-emerald-600 dark:text-emerald-400'">
                    {{ typeof tenant.ai_usage.usage.last_hour.remaining_requests === 'number' ? tenant.ai_usage.usage.last_hour.remaining_requests : 'âˆž' }}
                  </span>
                </div>
              </div>
            </div>
            <div class="bg-gray-50 dark:bg-zinc-800/50 rounded-lg p-3">
              <p class="text-[10px] font-semibold text-gray-400 dark:text-zinc-500 uppercase tracking-wider mb-2">Hoy</p>
              <div class="space-y-1.5">
                <div class="flex justify-between"><span class="text-[11px] text-gray-500 dark:text-zinc-400">Peticiones</span><span class="text-xs font-semibold text-gray-900 dark:text-white">{{ tenant.ai_usage.usage.today.requests }}</span></div>
                <div class="flex justify-between"><span class="text-[11px] text-gray-500 dark:text-zinc-400">Tokens</span><span class="text-xs font-semibold text-gray-900 dark:text-white">{{ formatNumber(tenant.ai_usage.usage.today.tokens) }}</span></div>
                <div class="flex justify-between"><span class="text-[11px] text-gray-500 dark:text-zinc-400">Costo</span><span class="text-xs font-semibold text-amber-600 dark:text-amber-400">${{ tenant.ai_usage.usage.today.cost?.toFixed(4) || '0.0000' }}</span></div>
                <div class="flex justify-between">
                  <span class="text-[11px] text-gray-500 dark:text-zinc-400">Restantes</span>
                  <span class="text-xs font-semibold" :class="tenant.ai_usage.usage.today.remaining_requests === 0 ? 'text-rose-600 dark:text-rose-400' : 'text-emerald-600 dark:text-emerald-400'">
                    {{ typeof tenant.ai_usage.usage.today.remaining_requests === 'number' ? tenant.ai_usage.usage.today.remaining_requests : 'âˆž' }}
                  </span>
                </div>
              </div>
            </div>
          </div>

          <!-- Total usage -->
          <div v-if="tenant.ai_usage.usage && tenant.ai_usage.usage.total" class="mt-3 bg-gray-50 dark:bg-zinc-800/50 rounded-lg p-3">
            <p class="text-[10px] font-semibold text-gray-400 dark:text-zinc-500 uppercase tracking-wider mb-2">Acumulado Total</p>
            <div class="grid grid-cols-3 gap-4">
              <div class="flex justify-between"><span class="text-[11px] text-gray-500 dark:text-zinc-400">Peticiones</span><span class="text-xs font-semibold text-gray-900 dark:text-white">{{ formatNumber(tenant.ai_usage.usage.total.requests) }}</span></div>
              <div class="flex justify-between"><span class="text-[11px] text-gray-500 dark:text-zinc-400">Tokens</span><span class="text-xs font-semibold text-gray-900 dark:text-white">{{ formatNumber(tenant.ai_usage.usage.total.tokens) }}</span></div>
              <div class="flex justify-between"><span class="text-[11px] text-gray-500 dark:text-zinc-400">Costo Total</span><span class="text-xs font-semibold text-amber-600 dark:text-amber-400">${{ tenant.ai_usage.usage.total.cost?.toFixed(4) || '0.0000' }}</span></div>
            </div>
          </div>
        </div>
      </div>

      <!-- Footer Actions -->
      <div class="px-6 py-4 border-t border-gray-100 dark:border-zinc-800 flex items-center justify-between gap-3">
        <div class="flex items-center gap-2">
          <a 
            :href="'https://' + (tenant.primary_domain || tenant.domain) + '/login'" 
            target="_blank"
            rel="noopener noreferrer"
            class="inline-flex items-center gap-2 px-4 py-2 bg-gray-900 dark:bg-zinc-700 hover:bg-black dark:hover:bg-zinc-600 text-white text-xs font-medium rounded-lg transition-all"
          >
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
            </svg>
            Entrar a la Tienda
          </a>
          <button 
            @click="$emit('toggle-status', tenant.id, tenant.status)"
            class="inline-flex items-center gap-2 px-4 py-2 text-xs font-medium rounded-lg border transition-all"
            :class="tenant.status === 'active' 
              ? 'text-amber-600 dark:text-amber-400 border-amber-200 dark:border-amber-800 hover:bg-amber-50 dark:hover:bg-amber-950/30' 
              : 'text-emerald-600 dark:text-emerald-400 border-emerald-200 dark:border-emerald-800 hover:bg-emerald-50 dark:hover:bg-emerald-950/30'"
          >
            {{ tenant.status === 'active' ? 'Pausar Tienda' : 'Activar Tienda' }}
          </button>
        </div>
        <button 
          @click="$emit('close')"
          class="px-4 py-2 text-xs font-medium text-gray-500 dark:text-zinc-400 hover:text-gray-700 dark:hover:text-zinc-300 hover:bg-gray-100 dark:hover:bg-zinc-800 rounded-lg transition-all"
        >
          Cerrar
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  tenant: { type: Object, required: true }
})

defineEmits(['close', 'update-plan', 'toggle-status'])

const daysRemaining = computed(() => {
  if (!props.tenant.subscription_end) return null
  const end = new Date(props.tenant.subscription_end)
  const now = new Date()
  return Math.ceil((end - now) / 86400000)
})

const isExpiringSoon = computed(() => {
  return daysRemaining.value !== null && daysRemaining.value <= 7
})

const formatDate = (dateString) => {
  if (!dateString) return 'â€”'
  return new Date(dateString).toLocaleDateString('es-ES', { year: 'numeric', month: 'short', day: 'numeric' })
}

const formatNumber = (num) => {
  if (num === null || num === undefined || isNaN(num)) return '0'
  return new Intl.NumberFormat('es-ES').format(num)
}

const formatCurrency = (num) => '$' + new Intl.NumberFormat('es-ES', { minimumFractionDigits: 0 }).format(num)

const getStatusClasses = (status) => ({
  'bg-emerald-50 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-400 border-emerald-200 dark:border-emerald-800': status === 'active',
  'bg-amber-50 dark:bg-amber-950 text-amber-700 dark:text-amber-400 border-amber-200 dark:border-amber-800': status === 'paused',
  'bg-rose-50 dark:bg-rose-950 text-rose-700 dark:text-rose-400 border-rose-200 dark:border-rose-800': status === 'suspended'
})

const getStatusDot = (status) => ({
  'bg-emerald-500': status === 'active',
  'bg-amber-500': status === 'paused',
  'bg-rose-500': status === 'suspended'
})

const getPlanClasses = (plan) => ({
  'bg-gray-50 dark:bg-zinc-800 text-gray-600 dark:text-zinc-400 border-gray-200 dark:border-zinc-700': plan === 'free_trial' || !plan,
  'bg-blue-50 dark:bg-blue-950 text-blue-700 dark:text-blue-400 border-blue-200 dark:border-blue-800': plan === 'basic',
  'bg-violet-50 dark:bg-violet-950 text-violet-700 dark:text-violet-400 border-violet-200 dark:border-violet-800': plan === 'premium',
  'bg-amber-50 dark:bg-amber-950 text-amber-700 dark:text-amber-400 border-amber-200 dark:border-amber-800': plan === 'enterprise'
})
</script>
