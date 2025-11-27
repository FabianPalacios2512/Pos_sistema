<template>
  <div v-if="show" class="fixed bottom-4 right-4 z-50 max-w-sm">
    <!-- Widget Compacto -->
    <transition name="fade">
      <div v-if="!expanded" 
           @click="expanded = true"
           class="bg-gradient-to-r from-purple-600 to-blue-600 text-white rounded-lg shadow-2xl p-4 cursor-pointer hover:shadow-purple-500/50 transition-all duration-300">
        <div class="flex items-center justify-between">
          <div class="flex items-center space-x-3">
            <div class="w-10 h-10 bg-white/20 rounded-lg flex items-center justify-center">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
              </svg>
            </div>
            <div>
              <p class="text-xs font-semibold opacity-90">Créditos IA</p>
              <p class="text-lg font-bold">{{ remaining }}</p>
            </div>
          </div>
          <div :class="getStatusColor()" class="w-3 h-3 rounded-full animate-pulse"></div>
        </div>
      </div>
    </transition>

    <!-- Widget Expandido -->
    <transition name="slide-up">
      <div v-if="expanded" class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl border border-gray-200 dark:border-gray-700 overflow-hidden">
        <!-- Header -->
        <div class="bg-gradient-to-r from-purple-600 to-blue-600 text-white p-4">
          <div class="flex items-center justify-between mb-2">
            <div class="flex items-center space-x-2">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
              </svg>
              <h3 class="font-bold">Uso de IA</h3>
            </div>
            <button @click="expanded = false" class="hover:bg-white/20 rounded-lg p-1 transition-colors">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
              </svg>
            </button>
          </div>
          <div class="flex items-center justify-between">
            <span class="text-sm opacity-90">Plan {{ usage?.plan?.toUpperCase() || 'FREE' }}</span>
            <span v-if="!isUnlimited" class="text-xs bg-white/20 px-2 py-1 rounded-full">
              {{ remaining }} restantes
            </span>
            <span v-else class="text-xs bg-white/20 px-2 py-1 rounded-full">∞ Ilimitado</span>
          </div>
        </div>

        <!-- Advertencias -->
        <div v-if="warnings.length > 0" class="p-3 space-y-2">
          <div v-for="warning in warnings" :key="warning.type"
               :class="warning.severity === 'critical' ? 'bg-red-100 border-red-500 text-red-700' : 'bg-yellow-100 border-yellow-500 text-yellow-700'"
               class="p-2 rounded-lg border-l-4 text-xs">
            <p class="font-semibold">⚠️ {{ warning.message }}</p>
          </div>
        </div>

        <!-- Estadísticas -->
        <div class="p-4 space-y-4">
          <!-- Barra de Progreso Última Hora -->
          <div v-if="!isUnlimited">
            <div class="flex items-center justify-between mb-2">
              <span class="text-xs font-semibold text-gray-600 dark:text-gray-400">Última Hora</span>
              <span class="text-xs font-bold text-gray-900 dark:text-white">
                {{ usage?.usage?.last_hour?.requests || 0 }} / {{ limits?.limits?.requests_per_hour || 0 }}
              </span>
            </div>
            <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
              <div :class="getProgressColor(usagePercentageHour)" 
                   :style="{width: usagePercentageHour + '%'}"
                   class="h-2 rounded-full transition-all duration-500"></div>
            </div>
          </div>

          <!-- Barra de Progreso Hoy -->
          <div v-if="!isUnlimited">
            <div class="flex items-center justify-between mb-2">
              <span class="text-xs font-semibold text-gray-600 dark:text-gray-400">Hoy</span>
              <span class="text-xs font-bold text-gray-900 dark:text-white">
                {{ usage?.usage?.today?.requests || 0 }} / {{ limits?.limits?.requests_per_day || 0 }}
              </span>
            </div>
            <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
              <div :class="getProgressColor(usagePercentageDay)" 
                   :style="{width: usagePercentageDay + '%'}"
                   class="h-2 rounded-full transition-all duration-500"></div>
            </div>
          </div>

          <!-- Métricas -->
          <div class="grid grid-cols-3 gap-2">
            <div class="bg-gray-50 dark:bg-gray-700 p-3 rounded-lg text-center">
              <p class="text-xs text-gray-500 dark:text-gray-400">Tokens Hoy</p>
              <p class="text-sm font-bold text-gray-900 dark:text-white">
                {{ formatNumber(usage?.usage?.today?.tokens || 0) }}
              </p>
            </div>
            <div class="bg-gray-50 dark:bg-gray-700 p-3 rounded-lg text-center">
              <p class="text-xs text-gray-500 dark:text-gray-400">Costo</p>
              <p class="text-sm font-bold text-gray-900 dark:text-white">
                ${{ (usage?.usage?.today?.cost || 0).toFixed(4) }}
              </p>
            </div>
            <div class="bg-gray-50 dark:bg-gray-700 p-3 rounded-lg text-center">
              <p class="text-xs text-gray-500 dark:text-gray-400">Total</p>
              <p class="text-sm font-bold text-gray-900 dark:text-white">
                {{ usage?.usage?.total?.requests || 0 }}
              </p>
            </div>
          </div>

          <!-- Actualizar Plan -->
          <button v-if="!isUnlimited && usagePercentageDay > 80" 
                  @click="$emit('upgrade-plan')"
                  class="w-full py-2 bg-gradient-to-r from-purple-600 to-blue-600 text-white rounded-lg font-semibold text-sm hover:from-purple-700 hover:to-blue-700 transition-all">
            Actualizar Plan
          </button>
        </div>
      </div>
    </transition>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import axios from 'axios'

const props = defineProps({
  autoRefresh: {
    type: Boolean,
    default: true
  },
  refreshInterval: {
    type: Number,
    default: 30000 // 30 segundos
  }
})

const emit = defineEmits(['upgrade-plan'])

const show = ref(true)
const expanded = ref(false)
const usage = ref(null)
const loading = ref(false)
let refreshTimer = null

const limits = computed(() => usage.value?.limits)
const warnings = computed(() => usage.value?.warnings || [])
const isUnlimited = computed(() => limits.value?.unlimited === true)

const remaining = computed(() => {
  if (isUnlimited.value) return '∞'
  return usage.value?.usage?.today?.remaining_requests || 0
})

const usagePercentageHour = computed(() => {
  if (!usage.value || isUnlimited.value) return 0
  const used = usage.value.usage?.last_hour?.requests || 0
  const limit = limits.value?.limits?.requests_per_hour || 1
  return Math.min(Math.round((used / limit) * 100), 100)
})

const usagePercentageDay = computed(() => {
  if (!usage.value || isUnlimited.value) return 0
  const used = usage.value.usage?.today?.requests || 0
  const limit = limits.value?.limits?.requests_per_day || 1
  return Math.min(Math.round((used / limit) * 100), 100)
})

const fetchUsage = async () => {
  if (loading.value) return
  loading.value = true
  try {
    const response = await axios.get('/api/ai/usage-status')
    if (response.data.success) {
      usage.value = response.data.data
      
      // Auto-expandir si hay advertencias críticas
      const criticalWarnings = warnings.value.filter(w => w.severity === 'critical')
      if (criticalWarnings.length > 0 && !expanded.value) {
        expanded.value = true
      }
    }
  } catch (error) {
    console.error('Error fetching AI usage:', error)
  } finally {
    loading.value = false
  }
}

const getStatusColor = () => {
  if (isUnlimited.value) return 'bg-green-400'
  const percentage = usagePercentageDay.value
  if (percentage >= 90) return 'bg-red-500'
  if (percentage >= 80) return 'bg-yellow-500'
  return 'bg-green-500'
}

const getProgressColor = (percentage) => {
  if (percentage >= 90) return 'bg-red-500'
  if (percentage >= 80) return 'bg-yellow-500'
  return 'bg-gradient-to-r from-purple-500 to-blue-500'
}

const formatNumber = (num) => {
  return new Intl.NumberFormat('es-ES').format(num)
}

const startAutoRefresh = () => {
  if (props.autoRefresh && !refreshTimer) {
    refreshTimer = setInterval(fetchUsage, props.refreshInterval)
  }
}

const stopAutoRefresh = () => {
  if (refreshTimer) {
    clearInterval(refreshTimer)
    refreshTimer = null
  }
}

onMounted(() => {
  fetchUsage()
  startAutoRefresh()
})

onUnmounted(() => {
  stopAutoRefresh()
})

defineExpose({
  refresh: fetchUsage,
  toggle: () => expanded.value = !expanded.value
})
</script>

<style scoped>
.fade-enter-active, .fade-leave-active {
  transition: opacity 0.3s, transform 0.3s;
}

.fade-enter-from, .fade-leave-to {
  opacity: 0;
  transform: scale(0.9);
}

.slide-up-enter-active, .slide-up-leave-active {
  transition: all 0.3s ease;
}

.slide-up-enter-from {
  opacity: 0;
  transform: translateY(20px);
}

.slide-up-leave-to {
  opacity: 0;
  transform: translateY(20px);
}
</style>
