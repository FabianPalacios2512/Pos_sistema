<template>
  <Teleport to="body">
    <Transition name="tour-fade">
      <div v-if="isActive" class="fixed inset-0 z-[9999] flex items-center justify-center pointer-events-none">
        
        <!-- Backdrop -->
        <div class="absolute inset-0 bg-black/60  pointer-events-auto transition-opacity duration-500" @click="handleBackdropClick"></div>

        <!-- Tour Card -->
        <div class="relative w-full max-w-lg mx-4 pointer-events-auto">
          <div class="bg-white rounded-3xl shadow-2xl overflow-hidden border border-white/20 relative animate-tour-slide-up">
            
            <!-- Decorative Background Elements -->
            <div class="absolute top-0 left-0 w-full h-32 bg-gradient-to-br from-indigo-600 to-purple-700"></div>
            <div class="absolute top-[-50px] right-[-50px] w-40 h-40 bg-white/10 rounded-full blur-2xl"></div>
            <div class="absolute top-[20px] left-[-20px] w-20 h-20 bg-white/10 rounded-full blur-xl"></div>

            <!-- Content -->
            <div class="relative px-8 pt-8 pb-8">
              
              <!-- Icon/Avatar -->
              <div class="w-20 h-20 mx-auto bg-white rounded-2xl shadow-xl flex items-center justify-center mb-6 relative z-10 transform -rotate-3 border-4 border-white/50">
                <span class="text-4xl">{{ currentStepIcon }}</span>
              </div>

              <!-- Text -->
              <div class="text-center space-y-4">
                <h2 class="text-2xl font-black text-slate-900 tracking-tight">
                  {{ currentStepTitle }}
                </h2>
                <p class="text-slate-600 leading-relaxed text-sm font-medium">
                  {{ currentStepDescription }}
                </p>
              </div>

              <!-- Steps Indicator -->
              <div class="flex justify-center gap-2 mt-8 mb-8">
                <div 
                  v-for="i in totalSteps" 
                  :key="i"
                  class="h-1.5 rounded-full transition-all duration-300"
                  :class="i <= currentStep ? 'w-8 bg-indigo-600' : 'w-2 bg-slate-200'"
                ></div>
              </div>

              <!-- Actions -->
              <div class="flex gap-3">
                <button 
                  v-if="currentStep === 1"
                  @click="skipTour"
                  class="flex-1 py-3.5 px-6 rounded-xl text-slate-500 font-bold text-sm hover:bg-slate-50 transition-colors border border-transparent hover:border-slate-200"
                >
                  Omitir
                </button>
                
                <button 
                  @click="nextStep"
                  class="flex-1 py-3.5 px-6 rounded-xl bg-slate-900 text-white font-bold text-sm shadow-lg shadow-slate-900/20 hover:bg-black hover:shadow-xl hover:-translate-y-0.5 transition-all flex items-center justify-center gap-2 group"
                >
                  <span>{{ currentStep === totalSteps ? '¡Empezar!' : 'Siguiente' }}</span>
                  <svg v-if="currentStep < totalSteps" class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                </button>
              </div>

            </div>
          </div>
        </div>

      </div>
    </Transition>
  </Teleport>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted, watch } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { useAuth } from '../store/auth'
import { apiCall } from '../services/api'

const router = useRouter()
const route = useRoute()
const { user, updateUser } = useAuth()

// MODO DESARROLLO: Cambia esto a false cuando estés en producción
const DEV_MODE = false // true = siempre muestra el tour para testing

const isActive = ref(false)
const currentStep = ref(1)
const totalSteps = 4

// Check if tour should start
const checkTourStatus = () => {
  // En modo desarrollo, SIEMPRE mostrar el tour (ignorar BD)
  if (DEV_MODE) {
    const savedStep = localStorage.getItem('tour_step')
    if (savedStep) {
      currentStep.value = parseInt(savedStep)
    }
    isActive.value = true
    return
  }
  
  // Modo normal: activar tour si tour_completed es false, null, o undefined (first time users)
  if (user.value && (user.value.tour_completed === false || user.value.tour_completed == null)) {
    // Check localStorage for current step to resume
    const savedStep = localStorage.getItem('tour_step')
    if (savedStep) {
      currentStep.value = parseInt(savedStep)
    }
    isActive.value = true
  }
}

// Watch for user changes (login)
watch(() => user.value, (newUser) => {
  if (newUser) checkTourStatus()
}, { immediate: true })

const currentStepTitle = computed(() => {
  switch (currentStep.value) {
    case 1: return `¡Hola, ${user.value?.name?.split(' ')[0] || 'Usuario'}!`
    case 2: return 'Tu Panel de Control'
    case 3: return 'Gestión de Productos'
    case 4: return '¡Todo Listo!'
    default: return ''
  }
})

const currentStepDescription = computed(() => {
  switch (currentStep.value) {
    case 1: return 'Bienvenido a tu nuevo sistema POS. Hemos preparado un breve recorrido para mostrarte las funciones principales y ayudarte a empezar.'
    case 2: return 'Este es tu Dashboard. Aquí tendrás una vista general de tus ventas, alertas de stock bajo y métricas clave de tu negocio en tiempo real.'
    case 3: return 'En esta sección podrás administrar todo tu inventario. Crea productos, actualiza precios y controla el stock de manera eficiente.'
    case 4: return 'Ya conoces lo básico. Explora las demás funciones y empieza a vender. ¡Estamos aquí para ayudarte a crecer!'
    default: return ''
  }
})

const currentStepIcon = computed(() => {
  switch (currentStep.value) {
    case 1: return ''
    case 2: return ''
    case 3: return ''
    case 4: return ''
    default: return ''
  }
})

const nextStep = async () => {
  if (currentStep.value === 1) {
    // Go to Dashboard step
    if (route.path !== '/dashboard') {
      await router.push('/pos')
    }
    currentStep.value = 2
    localStorage.setItem('tour_step', '2')
  } 
  else if (currentStep.value === 2) {
    // Go to Products step
    await router.push('/products')
    currentStep.value = 3
    localStorage.setItem('tour_step', '3')
  }
  else if (currentStep.value === 3) {
    // Go to Finish step
    currentStep.value = 4
    localStorage.setItem('tour_step', '4')
  }
  else if (currentStep.value === 4) {
    // Finish
    await completeTour()
  }
}

const skipTour = async () => {
  await completeTour()
}

const completeTour = async () => {
  isActive.value = false
  localStorage.removeItem('tour_step')
  
  // En DEV_MODE, no actualizar la BD (para poder probar fácilmente)
  if (DEV_MODE) {
    return
  }
  
  // Update user in backend (solo en producción)
  try {
    if (user.value?.id) {
      await apiCall(`/users/${user.value.id}`, 'PUT', {
        tour_completed: true
      })
      // Update local user state
      await updateUser()
    }
  } catch (error) {
    console.error('Error updating tour status:', error)
  }
}

const handleBackdropClick = () => {
  // Optional: Close on backdrop click? No, better to force interaction or skip.
}

// DESARROLLO: Función para resetear el tour manualmente
const resetTour = () => {
  localStorage.removeItem('tour_step')
  currentStep.value = 1
  isActive.value = false
  // Re-activar después de un pequeño delay
  setTimeout(() => {
    checkTourStatus()
  }, 100)
}

// DESARROLLO: Atajo de teclado Ctrl+Shift+T para resetear tour
const handleKeyPress = (event) => {
  if (DEV_MODE && event.ctrlKey && event.shiftKey && event.key === 'T') {
    resetTour()
  }
}

// Lifecycle
onMounted(() => {
  if (DEV_MODE) {
    window.addEventListener('keydown', handleKeyPress)
  }
})

onUnmounted(() => {
  if (DEV_MODE) {
    window.removeEventListener('keydown', handleKeyPress)
  }
})
</script>

<style scoped>
.tour-fade-enter-active,
.tour-fade-leave-active {
  transition: opacity 0.5s ease;
}

.tour-fade-enter-from,
.tour-fade-leave-to {
  opacity: 0;
}

.animate-tour-slide-up {
  animation: tourSlideUp 0.6s cubic-bezier(0.16, 1, 0.3, 1);
}

@keyframes tourSlideUp {
  from {
    opacity: 0;
    transform: translateY(20px) scale(0.95);
  }
  to {
    opacity: 1;
    transform: translateY(0) scale(1);
  }
}
</style>
