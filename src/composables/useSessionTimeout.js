import { ref, onMounted, onUnmounted } from 'vue'
import { useRouter } from 'vue-router'
import authStore from '../store/auth.js'

/**
 * Composable para manejar timeout de sesión por inactividad
 */
export function useSessionTimeout() {
  const router = useRouter()
  
  // Configuración
  const INACTIVITY_TIME = 6 * 60 * 60 * 1000 // 6 horas en producción
  const WARNING_TIME = 5 * 60 * 1000 // 5 minutos de advertencia
  const WARNING_SHOW_TIME = INACTIVITY_TIME - WARNING_TIME // Mostrar warning 5 min antes
  
  // Estado
  const showWarning = ref(false)
  const warningCountdown = ref(0)
  
  // Timers
  let inactivityTimer = null
  let warningTimer = null
  let countdownInterval = null
  
  // Eventos que reinician el timer de inactividad
  const events = [
    'mousedown',
    'mousemove', 
    'keypress',
    'scroll',
    'touchstart',
    'click'
  ]
  
  /**
   * Reiniciar el timer de inactividad
   */
  function resetInactivityTimer() {
    // Limpiar timers existentes
    clearTimeout(inactivityTimer)
    clearTimeout(warningTimer)
    clearInterval(countdownInterval)
    
    // Ocultar warning si está visible
    showWarning.value = false
    warningCountdown.value = 0
    
    // Solo configurar timers si hay una sesión activa
    if (!authStore.getters.isAuthenticated.value) {
      return
    }
    
    // Timer para mostrar warning
    warningTimer = setTimeout(() => {
      showSessionWarning()
    }, WARNING_SHOW_TIME)
    
    // Timer para logout automático
    inactivityTimer = setTimeout(() => {
      performAutoLogout()
    }, INACTIVITY_TIME)
  }
  
  /**
   * Mostrar advertencia de sesión próxima a expirar
   */
  function showSessionWarning() {
    console.log('⚠️ Mostrando advertencia de inactividad')
    showWarning.value = true
    warningCountdown.value = Math.ceil(WARNING_TIME / 1000) // segundos
    
    // Countdown
    countdownInterval = setInterval(() => {
      warningCountdown.value--
      
      if (warningCountdown.value <= 0) {
        clearInterval(countdownInterval)
        performAutoLogout()
      }
    }, 1000)
  }
  
  /**
   * Extender la sesión (cancelar logout)
   */
  function extendSession() {
    console.log('✅ Sesión extendida por el usuario')
    resetInactivityTimer()
  }
  
  /**
   * Realizar logout automático
   */
  async function performAutoLogout() {
    console.log('🚪 Realizando logout automático por inactividad')
    
    try {
      // Limpiar todos los timers
      cleanup()
      
      // Logout
      await authStore.actions.logout()
      
      // Redirigir al login con mensaje
      router.push({
        path: '/login',
        query: { 
          reason: 'timeout',
          message: 'Sesión cerrada por inactividad'
        }
      })
      
    } catch (error) {
      console.error('Error durante logout automático:', error)
      
      // Forzar redirección al login
      window.location.href = '/login?reason=timeout'
    }
  }
  
  /**
   * Limpiar todos los timers y eventos
   */
  function cleanup() {
    clearTimeout(inactivityTimer)
    clearTimeout(warningTimer)
    clearInterval(countdownInterval)
    
    events.forEach(event => {
      document.removeEventListener(event, resetInactivityTimer, true)
    })
    
    showWarning.value = false
    warningCountdown.value = 0
  }
  
  /**
   * Inicializar el sistema de timeout
   */
  function initialize() {
    // Solo inicializar si hay sesión activa
    if (!authStore.getters.isAuthenticated.value) {
      return
    }
    
    console.log('🚀 Sistema de timeout de sesión activado')
    
    // Añadir listeners para detectar actividad
    events.forEach(event => {
      document.addEventListener(event, resetInactivityTimer, true)
    })
    
    // Iniciar el timer
    resetInactivityTimer()
  }
  
  /**
   * Formatear tiempo en MM:SS
   */
  function formatTime(seconds) {
    const minutes = Math.floor(seconds / 60)
    const secs = seconds % 60
    return `${minutes.toString().padStart(2, '0')}:${secs.toString().padStart(2, '0')}`
  }
  
  // Lifecycle hooks
  onMounted(() => {
    // Delay para asegurar que el auth store esté inicializado
    setTimeout(initialize, 1000)
  })
  
  onUnmounted(() => {
    cleanup()
  })
  
  return {
    // Estado
    showWarning,
    warningCountdown,
    
    // Métodos
    extendSession,
    resetInactivityTimer,
    initialize,
    cleanup,
    formatTime,
    
    // Configuración (readonly)
    INACTIVITY_TIME: INACTIVITY_TIME / 1000 / 60 / 60, // en horas
    WARNING_TIME: WARNING_TIME / 1000 / 60 // en minutos
  }
}