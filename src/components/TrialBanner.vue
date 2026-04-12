<template>
  <!-- Banner solo visible en trial_express con días restantes -->
  <Teleport to="body">
    <div
      v-if="showBanner"
      class="fixed top-0 left-0 right-0 z-[9999] bg-gradient-to-r from-amber-500 via-orange-500 to-rose-500 text-white shadow-2xl border-b-4 border-rose-600 animate-fade-in"
      role="alert"
    >
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex items-center justify-between py-3 gap-4">
        <!-- Icono + Mensaje -->
        <div class="flex items-center gap-3 flex-1 min-w-0">
          <!-- Icono de reloj animado -->
          <div class="flex-shrink-0">
            <svg 
              class="w-6 h-6 animate-pulse" 
              fill="none" 
              stroke="currentColor" 
              viewBox="0 0 24 24"
            >
              <path 
                stroke-linecap="round" 
                stroke-linejoin="round" 
                stroke-width="2" 
                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"
              />
            </svg>
          </div>

          <!-- Texto del mensaje -->
          <div class="flex-1 min-w-0">
            <p class="text-sm font-bold">
              <template v-if="daysRemaining === 0">
                ⏰ ¡Último día de prueba gratis!
              </template>
              <template v-else-if="daysRemaining === 1">
                ⏰ Te queda <span class="text-xl font-black">1 día</span> de prueba gratis
              </template>
              <template v-else>
                ⏰ Te quedan <span class="text-xl font-black">{{ daysRemaining }} días</span> de prueba gratis
              </template>
            </p>
            <p class="text-xs mt-0.5 opacity-90">
              Actualiza ahora y desbloquea todas las funciones sin límites
            </p>
          </div>
        </div>

        <!-- Barra de Progreso Visual -->
        <div class="hidden md:flex items-center gap-2 flex-shrink-0">
          <div class="w-32 h-2 bg-white/20 rounded-full overflow-hidden">
            <div 
              class="h-full bg-white rounded-full transition-all duration-500"
              :style="{ width: progressPercentage + '%' }"
            />
          </div>
          <span class="text-xs font-bold whitespace-nowrap">{{ progressPercentage }}%</span>
        </div>

        <!-- Botón CTA -->
        <button
          @click="goToUpgrade"
          class="flex-shrink-0 px-6 py-2.5 bg-white text-rose-600 text-sm font-black rounded-xl hover:bg-gray-100 hover:scale-105 transition-all duration-200 shadow-xl hover:shadow-2xl"
        >
          Actualizar Ahora
        </button>

        <!-- Botón Cerrar (opcional) -->
        <button
          @click="dismissBanner"
          class="flex-shrink-0 p-1.5 hover:bg-white/10 rounded-lg transition-colors duration-200"
          aria-label="Cerrar"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>
    </div>
  </div>
  </Teleport>

  <!-- Spacer para evitar que el contenido quede debajo del banner -->
  <div v-if="showBanner" class="h-[60px]" />
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import apiClient from '@/services/apiClient.js'
import authStore from '@/store/auth.js'

const router = useRouter()
const route = useRoute()

// Estado
const showBanner = ref(false)
const daysRemaining = ref(0)
const plan = ref('')
const subscriptionEndsAt = ref(null)

// Progreso visual (invertido: día 3 = 100%, día 0 = 0%)
const progressPercentage = computed(() => {
  const totalDays = 3
  return Math.round((daysRemaining.value / totalDays) * 100)
})

// Verificar si estamos en una ruta pública
const isPublicRoute = computed(() => {
  const publicRoutes = ['/login', '/register', '/catalog']
  return publicRoutes.includes(route.path)
})

// Cargar estado del trial
const loadTrialStatus = async () => {
  // No cargar en rutas públicas o sin autenticación
  if (isPublicRoute.value || !authStore.state.isAuthenticated) {
    showBanner.value = false
    return
  }

  try {
    const response = await apiClient.get('/check-trial-status')
    
    plan.value = response.data.plan
    daysRemaining.value = response.data.days_remaining ?? 0
    subscriptionEndsAt.value = response.data.subscription_ends_at

    // Mostrar banner solo si es trial_express y no expiró
    showBanner.value = response.data.is_trial && !response.data.trial_expired
  } catch (error) {
    // Silenciar error si no estamos en contexto de tenant
    showBanner.value = false
  }
}

// Ir a página de upgrade
const goToUpgrade = () => {
  router.push('/upgrade')
}

// Ocultar banner temporalmente (se volverá a mostrar en próximo refresh)
const dismissBanner = () => {
  showBanner.value = false
}

// Cargar al montar
onMounted(() => {
  loadTrialStatus()

  // Actualizar cada 5 minutos
  setInterval(loadTrialStatus, 5 * 60 * 1000)
})
</script>

<style scoped>
@keyframes fade-in {
  from {
    opacity: 0;
    transform: translateY(-10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.animate-fade-in {
  animation: fade-in 0.3s ease-out;
}
</style>
