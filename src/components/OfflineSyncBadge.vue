<template>
  <!-- Badge de sincronización flotante -->
  <transition name="slide-up">
    <div 
      v-if="showBadge" 
      class="fixed bottom-6 right-6 z-50 animate-fade-in"
      @click="toggleDetails"
    >
      <!-- Badge principal -->
      <div 
        class="bg-gradient-to-br from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white rounded-2xl shadow-2xl cursor-pointer transition-all duration-300 hover:scale-105"
        :class="syncStatus === 'syncing' ? 'animate-pulse' : ''"
      >
        <div class="px-5 py-3 flex items-center gap-3">
          <!-- Icono -->
          <div class="relative">
            <svg 
              v-if="syncStatus === 'syncing'" 
              class="w-6 h-6 animate-spin" 
              fill="none" 
              viewBox="0 0 24 24"
            >
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            
            <svg 
              v-else-if="syncStatus === 'offline'" 
              class="w-6 h-6" 
              fill="none" 
              stroke="currentColor" 
              viewBox="0 0 24 24"
            >
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636a9 9 0 010 12.728m0 0l-2.829-2.829m2.829 2.829L21 21M15.536 8.464a5 5 0 010 7.072m0 0l-2.829-2.829m-4.243 2.829a4.978 4.978 0 01-1.414-2.83m-1.414 5.658a9 9 0 01-2.167-9.238m7.824 2.167a1 1 0 111.414 1.414m-1.414-1.414L3 3m8.293 8.293l1.414 1.414" />
            </svg>
            
            <svg 
              v-else 
              class="w-6 h-6" 
              fill="none" 
              stroke="currentColor" 
              viewBox="0 0 24 24"
            >
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
            </svg>
            
            <!-- Badge contador -->
            <span 
              v-if="pendingCount > 0" 
              class="absolute -top-2 -right-2 bg-rose-500 text-white text-[10px] font-bold rounded-full w-5 h-5 flex items-center justify-center animate-bounce"
            >
              {{ pendingCount > 99 ? '99+' : pendingCount }}
            </span>
          </div>
          
          <!-- Texto -->
          <div class="flex flex-col">
            <span class="text-xs font-bold uppercase tracking-wider">
              {{ statusText }}
            </span>
            <span class="text-[10px] opacity-90">
              {{ statusSubtext }}
            </span>
          </div>
        </div>
      </div>

      <!-- Panel de detalles -->
      <transition name="scale-up">
        <div 
          v-if="showDetails" 
          class="mt-3 bg-white dark:bg-zinc-900 rounded-2xl shadow-2xl border border-gray-200 dark:border-zinc-800 max-w-sm overflow-hidden"
        >
          <!-- Header del panel -->
          <div class="bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-zinc-800 dark:to-zinc-900 px-4 py-3 border-b border-gray-200 dark:border-zinc-800">
            <div class="flex items-center justify-between">
              <h3 class="text-sm font-bold text-gray-900 dark:text-white">
                Operaciones Pendientes
              </h3>
              <button 
                @click.stop="showDetails = false"
                class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors"
              >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
              </button>
            </div>
          </div>

          <!-- Lista de operaciones -->
          <div class="max-h-80 overflow-y-auto">
            <div v-if="operations.length === 0" class="p-6 text-center text-gray-500 dark:text-zinc-400">
              <svg class="w-12 h-12 mx-auto mb-2 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
              <p class="text-sm font-medium">Todo sincronizado</p>
              <p class="text-xs mt-1">No hay operaciones pendientes</p>
            </div>

            <div 
              v-for="op in operations" 
              :key="op.id"
              class="px-4 py-3 border-b border-gray-100 dark:border-zinc-800 last:border-0 hover:bg-gray-50 dark:hover:bg-zinc-800/50 transition-colors"
            >
              <div class="flex items-start gap-3">
                <!-- Icono de tipo -->
                <div class="w-10 h-10 rounded-lg bg-blue-50 dark:bg-blue-950 flex items-center justify-center flex-shrink-0">
                  <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                  </svg>
                </div>

                <!-- Detalles -->
                <div class="flex-1 min-w-0">
                  <p class="text-sm font-semibold text-gray-900 dark:text-white">
                    {{ op.label || op.type }}
                  </p>
                  <p class="text-xs text-gray-500 dark:text-zinc-400 mt-0.5">
                    {{ formatTime(op.timestamp) }}
                  </p>
                  <p class="text-xs text-gray-400 dark:text-zinc-500 mt-0.5 truncate">
                    {{ op.endpoint }}
                  </p>
                </div>

                <!-- Estado -->
                <div class="flex-shrink-0">
                  <span 
                    class="inline-flex items-center px-2 py-1 rounded-full text-[10px] font-bold uppercase"
                    :class="getStatusClass(op.status)"
                  >
                    {{ op.status === 'pending' ? 'Pendiente' : op.status === 'syncing' ? 'Sincronizando' : 'Fallido' }}
                  </span>
                </div>
              </div>

              <!-- Reintentos -->
              <div v-if="op.retries > 0" class="mt-2 flex items-center gap-1 text-xs text-amber-600 dark:text-amber-400">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                <span>Reintentos: {{ op.retries }}/{{ op.maxRetries }}</span>
              </div>
            </div>
          </div>

          <!-- Footer con acciones -->
          <div class="bg-gray-50 dark:bg-zinc-900 px-4 py-3 border-t border-gray-200 dark:border-zinc-800">
            <div class="flex gap-2">
              <button 
                @click="manualSync"
                :disabled="!isOnline || syncStatus === 'syncing'"
                class="flex-1 px-4 py-2 bg-blue-600 hover:bg-blue-700 disabled:bg-gray-300 dark:disabled:bg-zinc-700 text-white text-xs font-bold rounded-lg transition-colors disabled:cursor-not-allowed"
              >
                {{ syncStatus === 'syncing' ? 'Sincronizando...' : 'Sincronizar ahora' }}
              </button>
              <button 
                @click="refreshData"
                class="px-4 py-2 bg-white dark:bg-zinc-800 hover:bg-gray-50 dark:hover:bg-zinc-700 text-gray-700 dark:text-zinc-300 text-xs font-bold rounded-lg border border-gray-200 dark:border-zinc-700 transition-colors"
              >
                Refrescar
              </button>
            </div>
          </div>
        </div>
      </transition>
    </div>
  </transition>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import offlineSyncManager from '../utils/offlineSync.js'
import { getSyncStatus, forceSyncNow } from '../utils/offlineInterceptor.js'
import { useToast } from '../composables/useToast.js'

const { showError } = useToast()

// Estado
const pendingCount = ref(0)
const operations = ref([])
const isOnline = ref(navigator.onLine)
const syncStatus = ref('idle') // idle, syncing, offline
const showDetails = ref(false)

// Computed
const showBadge = computed(() => pendingCount.value > 0 || !isOnline.value || syncStatus.value === 'syncing')

const statusText = computed(() => {
  if (syncStatus.value === 'syncing') return 'Sincronizando'
  if (!isOnline.value) return 'Sin conexión'
  if (pendingCount.value > 0) return 'Pendiente'
  return 'Sincronizado'
})

const statusSubtext = computed(() => {
  if (syncStatus.value === 'syncing') return 'Subiendo al servidor...'
  if (!isOnline.value) return `${pendingCount.value} operaciones guardadas`
  if (pendingCount.value > 0) return `${pendingCount.value} operaciones`
  return 'Todo al día'
})

// Métodos
function toggleDetails() {
  showDetails.value = !showDetails.value
  if (showDetails.value) {
    loadOperations()
  }
}

async function loadOperations() {
  try {
    operations.value = await offlineSyncManager.getPendingOperations()
    pendingCount.value = operations.value.length
  } catch (error) {
    console.error('Error cargando operaciones:', error)
  }
}

async function refreshData() {
  const status = await getSyncStatus()
  pendingCount.value = status.pendingCount
  isOnline.value = status.isOnline
  await loadOperations()
}

async function manualSync() {
  if (!isOnline.value || syncStatus.value === 'syncing') return
  
  try {
    syncStatus.value = 'syncing'
    await forceSyncNow()
    await refreshData()
  } catch (error) {
    console.error('Error en sincronización manual:', error)
    showError('Error al sincronizar. Por favor verifica tu conexión.')
  } finally {
    syncStatus.value = 'idle'
  }
}

function formatTime(timestamp) {
  const date = new Date(timestamp)
  const now = new Date()
  const diff = now - date
  
  const minutes = Math.floor(diff / 60000)
  const hours = Math.floor(diff / 3600000)
  const days = Math.floor(diff / 86400000)
  
  if (minutes < 1) return 'Hace un momento'
  if (minutes < 60) return `Hace ${minutes} min`
  if (hours < 24) return `Hace ${hours} h`
  return `Hace ${days} días`
}

function getStatusClass(status) {
  if (status === 'pending') return 'bg-amber-50 dark:bg-amber-950 text-amber-700 dark:text-amber-400'
  if (status === 'syncing') return 'bg-blue-50 dark:bg-blue-950 text-blue-700 dark:text-blue-400'
  return 'bg-rose-50 dark:bg-rose-950 text-rose-700 dark:text-rose-400'
}

function handleOnlineStatus() {
  isOnline.value = navigator.onLine
  syncStatus.value = isOnline.value ? 'idle' : 'offline'
  refreshData()
}

// Listener de cambios de sincronización
function onSyncStatusChange(data) {
  if (data.type === 'sync_start') {
    syncStatus.value = 'syncing'
  } else if (data.type === 'sync_complete') {
    syncStatus.value = 'idle'
    refreshData()
  } else if (data.type === 'operation_saved') {
    refreshData()
  }
}

// Lifecycle
onMounted(() => {
  refreshData()
  offlineSyncManager.onStatusChange(onSyncStatusChange)
  window.addEventListener('online', handleOnlineStatus)
  window.addEventListener('offline', handleOnlineStatus)
  
  // Refrescar cada 5 segundos
  const interval = setInterval(refreshData, 5000)
  
  onUnmounted(() => {
    clearInterval(interval)
    window.removeEventListener('online', handleOnlineStatus)
    window.removeEventListener('offline', handleOnlineStatus)
  })
})
</script>

<style scoped>
/* Animaciones */
.slide-up-enter-active,
.slide-up-leave-active {
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.slide-up-enter-from {
  transform: translateY(100px);
  opacity: 0;
}

.slide-up-leave-to {
  transform: translateY(100px);
  opacity: 0;
}

.scale-up-enter-active,
.scale-up-leave-active {
  transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
}

.scale-up-enter-from {
  transform: scale(0.9) translateY(-10px);
  opacity: 0;
}

.scale-up-leave-to {
  transform: scale(0.9) translateY(-10px);
  opacity: 0;
}

.animate-fade-in {
  animation: fadeIn 0.3s ease-in-out;
}

@keyframes fadeIn {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}
</style>
