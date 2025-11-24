<template>
  <div id="app">
    <router-view />
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
  </div>
</template>

<script setup>
import { onMounted } from 'vue'
import authStore from './store/auth.js'
import { appStore } from './store/appStore.js'
import ToastContainer from './components/ToastContainer.vue'
import SessionTimeoutWarning from './components/SessionTimeoutWarning.vue'
import { useSessionTimeout } from './composables/useSessionTimeout.js'

// Inicializar el sistema de timeout de sesión
const sessionTimeout = useSessionTimeout()

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
  await authStore.actions.initialize()
  
  // ✅ INICIALIZAR APPSTORE DESPUÉS DE LA AUTENTICACIÓN (incluye sesión de caja)
  if (authStore.state.isAuthenticated) {
    console.log('🚀 Usuario autenticado, inicializando appStore...')
    await appStore.initialize()
  }
})
</script>