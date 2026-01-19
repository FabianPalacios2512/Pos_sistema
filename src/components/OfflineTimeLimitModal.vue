<template>
  <!-- Modal de bloqueo por tiempo offline excedido -->
  <transition name="fade">
    <div 
      v-if="showModal" 
      class="fixed inset-0 z-[9999] flex items-center justify-center bg-black/80 "
    >
      <div class="max-w-md w-full mx-4 animate-fade-in">
        <!-- Card principal - Diseño limpio y profesional -->
        <div class="bg-white dark:bg-zinc-900 rounded-2xl shadow-2xl dark:shadow-black/50 border border-gray-300 dark:border-zinc-800 overflow-hidden">
          
          <!-- Header minimalista -->
          <div class="p-8 text-center border-b border-gray-200 dark:border-zinc-800">
            <!-- Icono simple sin degradados -->
            <div class="w-20 h-20 mx-auto mb-4 bg-red-50 dark:bg-red-950/30 rounded-2xl flex items-center justify-center border border-red-100 dark:border-red-900/30">
              <svg class="w-10 h-10 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636a9 9 0 010 12.728m0 0l-2.829-2.829m2.829 2.829L21 21M15.536 8.464a5 5 0 010 7.072m0 0l-2.829-2.829m-4.243 2.829a4.978 4.978 0 01-1.414-2.83m-1.414 5.658a9 9 0 01-2.167-9.238m7.824 2.167a1 1 0 111.414 1.414m-1.414-1.414L3 3m8.293 8.293l1.414 1.414" />
              </svg>
            </div>
            
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">
              Conexión Requerida
            </h2>
            <p class="text-sm text-red-600 dark:text-red-400 font-medium">
              Tiempo sin conexión excedido
            </p>
          </div>

          <!-- Contenido -->
          <div class="p-8 space-y-6">
            <!-- Mensaje principal - limpio -->
            <div class="bg-red-50 dark:bg-red-950/20 border border-red-200 dark:border-red-900/30 rounded-xl p-4">
              <p class="text-sm text-gray-700 dark:text-gray-300 text-center leading-relaxed">
                Has estado trabajando <strong class="text-red-600 dark:text-red-400">{{ formatOfflineTime }}</strong> sin conexión a internet.
              </p>
              <p class="text-sm text-gray-700 dark:text-gray-300 text-center mt-2 leading-relaxed">
                Por seguridad, necesitas <strong>reconectar a internet</strong> para continuar usando el sistema.
              </p>
            </div>

            <!-- Estadísticas - diseño limpio -->
            <div class="grid grid-cols-2 gap-4">
              <div class="bg-gray-50 dark:bg-zinc-800 rounded-xl p-4 text-center border border-gray-200 dark:border-zinc-700">
                <p class="text-xs text-gray-500 dark:text-zinc-400 uppercase tracking-wider font-semibold mb-1">
                  Tiempo Offline
                </p>
                <p class="text-xl font-bold text-gray-900 dark:text-white">
                  {{ formatOfflineTime }}
                </p>
              </div>
              
              <div class="bg-gray-50 dark:bg-zinc-800 rounded-xl p-4 text-center border border-gray-200 dark:border-zinc-700">
                <p class="text-xs text-gray-500 dark:text-zinc-400 uppercase tracking-wider font-semibold mb-1">
                  Límite Permitido
                </p>
                <p class="text-xl font-bold text-gray-900 dark:text-white">
                  {{ formatTimeLimit }}
                </p>
              </div>
            </div>

            <!-- Barra de progreso - simple y limpia -->
            <div class="space-y-2">
              <div class="flex items-center justify-between text-xs text-gray-600 dark:text-zinc-400">
                <span class="font-medium">Tiempo sin conexión</span>
                <span class="font-bold text-red-600 dark:text-red-400">{{ percentage }}%</span>
              </div>
              <div class="h-2 bg-gray-200 dark:bg-zinc-800 rounded-full overflow-hidden border border-gray-300 dark:border-zinc-700">
                <div 
                  class="h-full bg-red-600 dark:bg-red-500 transition-all duration-500"
                  :style="{ width: `${Math.min(percentage, 100)}%` }"
                ></div>
              </div>
            </div>

            <!-- Instrucciones - minimalistas -->
            <div class="bg-blue-50 dark:bg-blue-950/20 border border-blue-200 dark:border-blue-900/30 rounded-xl p-4">
              <p class="text-xs font-bold text-blue-700 dark:text-blue-400 mb-3 uppercase tracking-wider flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Instrucciones
              </p>
              <ol class="text-sm text-gray-700 dark:text-gray-300 space-y-2 list-decimal list-inside">
                <li>Activa tu conexión WiFi o datos móviles</li>
                <li>Espera a que se restablezca la conexión</li>
                <li>Haz clic en "Verificar Conexión"</li>
              </ol>
            </div>

            <!-- Estado de conexión - simple -->
            <div class="flex items-center justify-center gap-2 py-2">
              <div 
                class="w-2.5 h-2.5 rounded-full"
                :class="isOnline ? 'bg-green-500 animate-pulse' : 'bg-red-500 animate-pulse'"
              ></div>
              <span class="text-sm font-semibold" :class="isOnline ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400'">
                {{ isOnline ? 'Sin conexión' : 'Sin conexión' }}
              </span>
            </div>
          </div>

          <!-- Footer con botón - limpio -->
          <div class="bg-gray-50 dark:bg-zinc-900 p-6 border-t border-gray-200 dark:border-zinc-800 space-y-3">
            <button
              @click="checkConnection"
              :disabled="checking"
              class="w-full px-6 py-3.5 bg-slate-900 dark:bg-slate-700 hover:bg-black dark:hover:bg-slate-600 disabled:bg-gray-400 dark:disabled:bg-zinc-700 text-white font-bold rounded-xl shadow-lg shadow-slate-400/40 dark:shadow-slate-900/50 transition-all duration-300 disabled:cursor-not-allowed flex items-center justify-center gap-2"
            >
              <svg 
                v-if="checking" 
                class="w-5 h-5 animate-spin" 
                fill="none" 
                viewBox="0 0 24 24"
              >
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              <svg 
                v-else 
                class="w-5 h-5" 
                fill="none" 
                stroke="currentColor" 
                viewBox="0 0 24 24"
              >
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
              </svg>
              <span>{{ checking ? 'Verificando...' : 'Verificar Conexión' }}</span>
            </button>

            <p class="text-xs text-center text-gray-500 dark:text-zinc-500">
              No puedes continuar sin reconectar a internet
            </p>
          </div>
        </div>

        <!-- Hint adicional -->
        <div class="mt-4 text-center">
          <p class="text-xs text-gray-500 dark:text-zinc-500">
            Esta verificación previene el uso indebido del sistema
          </p>
        </div>
      </div>
    </div>
  </transition>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import offlineTimeValidator from '../utils/offlineTimeValidator.js'

// Estado
const showModal = ref(false)
const isOnline = ref(navigator.onLine)
const checking = ref(false)
const offlineTime = ref(0)
const timeLimit = ref(0)
const percentage = ref(0)

// Computed
const formatOfflineTime = computed(() => {
  return offlineTimeValidator.formatTime(offlineTime.value)
})

const formatTimeLimit = computed(() => {
  return offlineTimeValidator.formatTime(timeLimit.value)
})

// Métodos
async function checkConnection() {
  if (checking.value) return
  
  checking.value = true
  
  // Pequeño delay para UX
  await new Promise(resolve => setTimeout(resolve, 500))
  
  isOnline.value = navigator.onLine
  
  if (isOnline.value) {
    // Verificar con una petición real al servidor
    try {
      const response = await fetch('/api/ping', { 
        method: 'GET',
        cache: 'no-cache'
      })
      
      if (response.ok) {
        console.log('✅ Conexión verificada exitosamente')
        showModal.value = false
        // El validador ya limpiará el tiempo offline
      }
    } catch (error) {
      console.error('❌ Sin conexión real al servidor:', error)
      isOnline.value = false
      alert('No se pudo conectar al servidor. Verifica tu conexión a internet.')
    }
  } else {
    alert('Aún no hay conexión a internet. Por favor activa tu WiFi o datos móviles.')
  }
  
  checking.value = false
}

function updateStatus() {
  const status = offlineTimeValidator.getOfflineStatus()
  offlineTime.value = status.offlineTime
  timeLimit.value = offlineTimeValidator.getConfig().timeLimit
  percentage.value = status.percentage
  isOnline.value = status.isOnline
  
  // Mostrar modal si se excedió el límite
  if (status.hasExceededLimit && !status.isOnline) {
    showModal.value = true
  } else if (status.isOnline) {
    showModal.value = false
  }
}

function handleOnlineStatus() {
  isOnline.value = navigator.onLine
  updateStatus()
}

function handleValidatorEvent(data) {
  if (data.type === 'time_limit_exceeded') {
    console.warn('⚠️ Límite de tiempo offline excedido - Mostrando modal')
    updateStatus()
  } else if (data.type === 'online_restored') {
    console.log('✅ Conexión restaurada - Ocultando modal')
    showModal.value = false
  }
}

// Lifecycle
let statusInterval = null

onMounted(() => {
  // Verificar estado inicial
  updateStatus()
  
  // Actualizar cada segundo cuando el modal está visible
  statusInterval = setInterval(() => {
    if (showModal.value || !navigator.onLine) {
      updateStatus()
    }
  }, 1000)
  
  // Escuchar eventos del validador
  offlineTimeValidator.onStatusChange(handleValidatorEvent)
  
  // Escuchar eventos de conexión del navegador
  window.addEventListener('online', handleOnlineStatus)
  window.addEventListener('offline', handleOnlineStatus)
})

onUnmounted(() => {
  if (statusInterval) {
    clearInterval(statusInterval)
  }
  window.removeEventListener('online', handleOnlineStatus)
  window.removeEventListener('offline', handleOnlineStatus)
})
</script>

<style scoped>
/* Animaciones */
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.3s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}

.animate-fade-in {
  animation: fadeInScale 0.4s cubic-bezier(0.4, 0, 0.2, 1);
}

@keyframes fadeInScale {
  from {
    opacity: 0;
    transform: scale(0.95) translateY(20px);
  }
  to {
    opacity: 1;
    transform: scale(1) translateY(0);
  }
}

.animate-pulse-slow {
  animation: pulse 3s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}

@keyframes pulse {
  0%, 100% {
    opacity: 1;
    transform: scale(1);
  }
  50% {
    opacity: 0.8;
    transform: scale(1.05);
  }
}
</style>
