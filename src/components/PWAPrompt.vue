<template>
  <div v-if="showInstallPrompt" class="fixed bottom-4 left-4 right-4 md:left-auto md:right-4 md:w-96 z-[9999]">
    <div class="bg-gradient-to-r from-slate-900 to-slate-800 dark:from-slate-950 dark:to-black rounded-2xl shadow-2xl border-2 border-slate-700 dark:border-slate-800 p-5 ">
      <div class="flex items-start gap-4">
        <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-blue-600 to-indigo-600 flex items-center justify-center flex-shrink-0 shadow-lg">
          <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
          </svg>
        </div>
        
        <div class="flex-1 min-w-0">
          <h3 class="text-base font-bold text-white mb-1">Instalar 105 POS Pro</h3>
          <p class="text-sm text-gray-300 mb-4">Instala la app para acceso rápido y trabajar sin conexión</p>
          
          <div class="flex gap-2">
            <button 
              @click="installPWA"
              class="flex-1 px-4 py-2.5 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white text-sm font-bold rounded-xl shadow-lg transition-all duration-200"
            >
              Instalar Ahora
            </button>
            <button 
              @click="dismissPrompt"
              class="px-4 py-2.5 bg-slate-700 hover:bg-slate-600 text-white text-sm font-bold rounded-xl transition-all duration-200"
            >
              Ahora no
            </button>
          </div>
        </div>
        
        <button 
          @click="dismissPrompt"
          class="text-gray-400 hover:text-white transition-colors"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
          </svg>
        </button>
      </div>
    </div>
  </div>

  <!-- Update Available Notification -->
  <div v-if="showUpdatePrompt" class="fixed top-4 right-4 z-[9999]">
    <div class="bg-emerald-600 dark:bg-emerald-700 rounded-xl shadow-2xl p-4 max-w-sm">
      <div class="flex items-center gap-3">
        <div class="w-10 h-10 rounded-lg bg-white/20 flex items-center justify-center">
          <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
          </svg>
        </div>
        <div class="flex-1">
          <p class="text-white font-bold text-sm">Nueva versión disponible</p>
          <p class="text-white/80 text-xs mt-0.5">Actualiza para obtener las últimas mejoras</p>
        </div>
      </div>
      <button 
        @click="reloadApp"
        class="w-full mt-3 px-4 py-2 bg-white hover:bg-gray-100 text-emerald-700 font-bold text-sm rounded-lg transition-colors"
      >
        Actualizar Ahora
      </button>
    </div>
  </div>

  <!-- Offline Indicator -->
  <div v-if="!isOnline" class="fixed top-4 left-4 z-[9999]">
    <div class="bg-amber-500 dark:bg-amber-600 rounded-xl shadow-xl px-4 py-3 flex items-center gap-3">
      <div class="w-8 h-8 rounded-lg bg-white/20 flex items-center justify-center">
        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636a9 9 0 010 12.728m0 0l-2.829-2.829m2.829 2.829L21 21M15.536 8.464a5 5 0 010 7.072m0 0l-2.829-2.829m-4.243 2.829a4.978 4.978 0 01-1.414-2.83m-1.414 5.658a9 9 0 01-2.167-9.238m7.824 2.167a1 1 0 111.414 1.414m-1.414-1.414L3 3m8.293 8.293l1.414 1.414"></path>
        </svg>
      </div>
      <div>
        <p class="text-white font-bold text-sm">Modo Sin Conexión</p>
        <p class="text-white/80 text-xs">Trabajando con datos locales</p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import { useRegisterSW } from 'virtual:pwa-register/vue'

const { needRefresh, updateServiceWorker } = useRegisterSW()

const showInstallPrompt = ref(false)
const showUpdatePrompt = ref(false)
const isOnline = ref(navigator.onLine)
let deferredPrompt = null

// Detectar cuando la PWA puede instalarse
const handleBeforeInstallPrompt = (e) => {
  e.preventDefault()
  deferredPrompt = e
  
  // No mostrar si ya está instalado o si el usuario ya rechazó
  const dismissed = localStorage.getItem('pwa-install-dismissed')
  const alreadyInstalled = window.matchMedia('(display-mode: standalone)').matches
  // Solo mostrar después de completar el tour del POS
  const tourCompleted = localStorage.getItem('pos_tour_completed') === 'true'
  
  if (!dismissed && !alreadyInstalled && tourCompleted) {
    setTimeout(() => {
      showInstallPrompt.value = true
    }, 5000) // Mostrar después de 5 segundos
  }
}

// Instalar PWA
const installPWA = async () => {
  if (!deferredPrompt) return
  
  deferredPrompt.prompt()
  const { outcome } = await deferredPrompt.userChoice
  
  if (outcome === 'accepted') {
    console.log('✅ PWA instalada correctamente')
  }
  
  deferredPrompt = null
  showInstallPrompt.value = false
}

// Rechazar instalación
const dismissPrompt = () => {
  showInstallPrompt.value = false
  localStorage.setItem('pwa-install-dismissed', Date.now())
  // Permitir mostrar nuevamente después de 7 días
  setTimeout(() => {
    localStorage.removeItem('pwa-install-dismissed')
  }, 7 * 24 * 60 * 60 * 1000)
}

// Actualizar app
const reloadApp = () => {
  updateServiceWorker(true)
}

// Detectar cambios en conexión
const updateOnlineStatus = () => {
  isOnline.value = navigator.onLine
}

// Watch for updates
const checkForUpdates = () => {
  if (needRefresh.value) {
    showUpdatePrompt.value = true
  }
}

onMounted(() => {
  window.addEventListener('beforeinstallprompt', handleBeforeInstallPrompt)
  window.addEventListener('online', updateOnlineStatus)
  window.addEventListener('offline', updateOnlineStatus)
  
  // Check for updates every 30 minutes
  const updateInterval = setInterval(checkForUpdates, 30 * 60 * 1000)
  checkForUpdates()
  
  // Cleanup
  onUnmounted(() => {
    clearInterval(updateInterval)
    window.removeEventListener('beforeinstallprompt', handleBeforeInstallPrompt)
    window.removeEventListener('online', updateOnlineStatus)
    window.removeEventListener('offline', updateOnlineStatus)
  })
})
</script>
