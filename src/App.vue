<template>
  <div id="app" class="h-full min-h-screen flex flex-col">
    <!-- Splash Screen Inicial -->
    <SplashScreen 
      v-if="showSplash"
    />
    
    <!-- Trial Banner (sticky top) - Sin v-show porque usa Teleport -->
    <TrialBanner />
    
    <!-- Aplicación Principal -->
    <div class="flex-1 flex flex-col min-h-0" 
         v-show="!showSplash"
    >
      <router-view />
    </div>
    <ToastContainer />
    
    <!-- Modal de advertencia de timeout de sesión -->
    <SessionTimeoutWarning
      :show="sessionTimeout.showWarning.value"
      :countdown="sessionTimeout.warningCountdown.value"
      :totalWarningTime="sessionTimeout.WARNING_TIME"
      :inactivityHours="sessionTimeout.INACTIVITY_TIME"
      @extend="sessionTimeout.extendSession"
      @logout="handleManualLogout"
    />
    
    <!-- Modal de actualización de Creditienda -->
    <CreditiendaUpgradeModal />
    
    <!-- PWA Install & Update Prompts -->
    <PWAPrompt />
    
    <!-- Modal de límite de tiempo offline -->
    <OfflineTimeLimitModal />
    
    <!-- Asistente IA 105 - Global en todas las vistas -->
    <AI105Chat 
      v-if="showAIChat"
      :header-height="64"
    />
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import { useRoute } from 'vue-router'
import authStore from './store/auth.js'
import { appStore } from './store/appStore.js'
import { aiChatStore } from './store/aiChatStore.js'
import SplashScreen from './components/SplashScreen.vue'
import ToastContainer from './components/ToastContainer.vue'
import SessionTimeoutWarning from './components/SessionTimeoutWarning.vue'
import CreditiendaUpgradeModal from './components/CreditiendaUpgradeModal.vue'
import PWAPrompt from './components/PWAPrompt.vue'
import OfflineTimeLimitModal from './components/OfflineTimeLimitModal.vue'
import TrialBanner from './components/TrialBanner.vue'
import AI105Chat from './components/AI105Chat.vue'
import { useSessionTimeout } from './composables/useSessionTimeout.js'

const route = useRoute()

// Estado del splash screen
const showSplash = ref(true)

// Inicializar el sistema de timeout de sesión
const sessionTimeout = useSessionTimeout()

// Mostrar IA Chat solo en rutas autenticadas
const showAIChat = computed(() => {
  const publicRoutes = ['/login', '/register', '/forgot-password', '/reset-password', '/terminos-condiciones', '/politica-privacidad', '/catalog']
  const isPublicRoute = publicRoutes.some(pr => route.path.startsWith(pr))
  const isPaymentRoute = route.path.startsWith('/payment/')
  return authStore.state.isAuthenticated && !isPublicRoute && !isPaymentRoute
})

// Exponer estado del chat para ajustar el layout
const isAIChatOpen = computed(() => aiChatStore.isOpen.value)

// Manejar logout manual desde el modal
async function handleManualLogout() {
  try {
    await authStore.actions.logout()
    sessionTimeout.cleanup()
  } catch (error) {
    console.error('Error durante logout manual:', error)
  }
}

// Inicializar autenticación al cargar la app
onMounted(async () => {
  const startTime = Date.now()
  
  await authStore.actions.initialize()
  
  // ✅ INICIALIZAR APPSTORE DESPUÉS DE LA AUTENTICACIÓN (incluye sesión de caja)
  if (authStore.state.isAuthenticated) {
    await appStore.initialize()
  }
  
  // 🔧 TODO: Re-habilitar health-check cuando se arregle correctamente con tenancy
  // El problema era que intentaba acceder a la DB del tenant sin middleware de tenancy
  
  // ✅ OCULTAR SPLASH SOLO DESPUÉS DE QUE TODO ESTÉ LISTO
  // Garantizar mínimo 1.2 segundos para que se vea la animación completa
  const elapsedTime = Date.now() - startTime
  const minimumDisplayTime = 1200
  const remainingTime = Math.max(0, minimumDisplayTime - elapsedTime)
  
  setTimeout(() => {
    showSplash.value = false
  }, remainingTime)
})
</script>