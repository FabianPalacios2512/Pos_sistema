<template>
  <div class="flex items-center">
    <!-- Indicador WhatsApp Discreto - Solo punto verde si conectado -->
    <button 
      @click="showModal = true"
      class="flex items-center gap-2 h-10 px-3 rounded-lg border transition-all duration-200"
      :class="whatsappStatus.connected 
        ? 'bg-white dark:bg-zinc-900 border-gray-200 dark:border-zinc-700 text-gray-700 dark:text-zinc-300 hover:bg-gray-50 dark:hover:bg-zinc-800' 
        : 'bg-amber-50 dark:bg-amber-950/30 border-amber-200 dark:border-amber-800/50 text-amber-700 dark:text-amber-400 hover:bg-amber-100 dark:hover:bg-amber-900/40'"
      :title="whatsappStatus.connected ? 'WhatsApp conectado' : 'WhatsApp desconectado - Haz clic para configurar'"
    >
      <!-- Punto de estado (verde pulsante si conectado, icono advertencia si no) -->
      <template v-if="whatsappStatus.connected">
        <div class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></div>
        <span class="text-[13px] font-medium text-gray-600 dark:text-zinc-400">En línea</span>
      </template>
      <template v-else>
        <svg class="w-4 h-4 text-amber-600 dark:text-amber-400" fill="currentColor" viewBox="0 0 24 24">
          <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893A11.821 11.821 0 0020.885 3.515"/>
        </svg>
        <span class="text-[13px] font-medium">Sin conexión</span>
      </template>
    </button>

    <!-- Modal de WhatsApp con Teleport para pantalla completa -->
    <Teleport to="body">
      <WhatsAppModal 
        :show-modal="showModal" 
        @close="showModal = false"
        @statusChange="updateStatus"
      />
    </Teleport>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import WhatsAppModal from './WhatsAppModal.vue'
import { whatsappService } from '../services/whatsappService.js'

// Estado reactivo
const showModal = ref(false)
const whatsappStatus = ref({ connected: false })
const statusCheckInterval = ref(null)

// Computed properties
const statusText = computed(() => {
  return whatsappStatus.value.connected ? 'Conectado' : 'Sin conexión'
})

const statusContainerClass = computed(() => {
  if (whatsappStatus.value.connected) {
    return 'bg-green-500 hover:bg-green-600 text-white border-green-500 hover:border-green-600'
  }
  return 'bg-amber-500 hover:bg-amber-600 text-white border-amber-500 hover:border-amber-600'
})

const statusIconClass = computed(() => {
  return 'text-white'
})

const statusDotClass = computed(() => {
  return 'bg-white/30 border border-white/50'
})

const statusTextClass = computed(() => {
  return 'text-white'
})

// Métodos
let consecutiveFailures = 0

const checkStatus = async () => {
  try {
    const result = await whatsappService.getStatus()
    if (result.success && result.status) {
      whatsappStatus.value = result.status
      consecutiveFailures = 0
    } else {
      whatsappStatus.value = { connected: false }
      consecutiveFailures++
    }
  } catch (error) {
    whatsappStatus.value = { connected: false }
    consecutiveFailures++
  }
}

const updateStatus = (newStatus) => {
  whatsappStatus.value = newStatus
}

const startStatusCheck = () => {
  const scheduleNext = async () => {
    await checkStatus()
    if (!statusCheckInterval.value) return // fue detenido
    // Backoff: 30s normal, hasta 120s si falla repetidamente
    const delay = consecutiveFailures > 3 ? 120000 : 30000
    statusCheckInterval.value = setTimeout(scheduleNext, delay)
  }
  // Usar setTimeout recursivo en vez de setInterval para aplicar backoff
  statusCheckInterval.value = setTimeout(scheduleNext, 30000)
}

const stopStatusCheck = () => {
  if (statusCheckInterval.value) {
    clearTimeout(statusCheckInterval.value)
    statusCheckInterval.value = null
  }
}

// Lifecycle hooks
onMounted(async () => {
  await checkStatus()
  startStatusCheck()
})

onUnmounted(() => {
  stopStatusCheck()
})

// Exponer métodos para uso externo
defineExpose({
  checkStatus,
  isConnected: computed(() => whatsappStatus.value.connected),
  openModal: () => showModal.value = true
})
</script>