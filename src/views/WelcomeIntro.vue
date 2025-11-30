<template>
  <div class="min-h-screen bg-[#EEF2F6] flex items-center justify-center p-4 font-sans overflow-hidden">
    
    <!-- Contenedor centrado absoluto para evitar desplazamientos -->
    <div class="absolute inset-0 flex items-center justify-center p-4">
      
      <!-- FASE 1: Animación de carga inicial (0s - 2s) -->
      <Transition name="smooth-fade" mode="out-in">
        <div v-if="loadingPhase === 1" key="loading" class="text-center space-y-6">
          <!-- Spinner elegante con tu estilo -->
          <div class="flex justify-center mb-8">
            <div class="relative">
              <div class="w-16 h-16 border-4 border-slate-200 rounded-full"></div>
              <div class="absolute top-0 left-0 w-16 h-16 border-4 border-slate-900 rounded-full border-t-transparent animate-spin"></div>
            </div>
          </div>
          <p class="text-xl font-bold text-slate-700">
            Inicializando sistema...
          </p>
        </div>

        <!-- FASE 2: Saludo inicial (2s - 5s) -->
        <div v-else-if="loadingPhase === 2" key="greeting" class="text-center space-y-6 max-w-lg">
          <div class="flex justify-center mb-6">
            <div class="w-20 h-20 bg-slate-900 rounded-[20px] flex items-center justify-center shadow-lg">
              <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
              </svg>
            </div>
          </div>
          <h1 class="text-4xl font-black text-slate-900 tracking-tight">
            ¡Hola<span v-if="userName">, {{ userName }}</span>!
          </h1>
          <p class="text-lg font-semibold text-slate-600">
            Bienvenido a 105 POS
          </p>
        </div>

        <!-- FASE 3: Preparando el sistema (5s - 8s) -->
        <div v-else-if="loadingPhase === 3" key="preparing" class="text-center space-y-8 max-w-lg">
          <div class="flex justify-center mb-6">
            <div class="relative">
              <div class="w-24 h-24 bg-white rounded-[24px] flex items-center justify-center shadow-xl border border-slate-200">
                <svg class="w-12 h-12 text-slate-900 animate-spin-slow" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
              </div>
            </div>
          </div>
          <div class="space-y-3">
            <h2 class="text-2xl font-black text-slate-900 tracking-tight">
              Estamos preparando todo para ti
            </h2>
            <p class="text-base font-semibold text-slate-600">
              Configurando tu espacio de trabajo personalizado...
            </p>
          </div>
          <!-- Barra de progreso con tu estilo -->
          <div class="w-full max-w-xs mx-auto">
            <div class="h-2 bg-slate-200 rounded-full overflow-hidden">
              <div class="h-full bg-slate-900 rounded-full animate-progress"></div>
            </div>
          </div>
        </div>

        <!-- FASE 4: Contenido final (después de 8s) -->
        <div v-else-if="loadingPhase === 4" key="content" class="relative z-10 max-w-xl w-full text-center space-y-6">
          
          <!-- Icono principal con tu estilo -->
          <div class="flex justify-center mb-4">
            <div class="w-20 h-20 bg-slate-900 rounded-[20px] flex items-center justify-center shadow-lg border-4 border-white">
              <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
              </svg>
            </div>
          </div>

          <!-- Título principal -->
          <div class="space-y-2">
            <h1 class="text-3xl md:text-4xl font-black text-slate-900 tracking-tight">
              ¡Todo listo para comenzar!
            </h1>
            <p class="text-lg font-semibold text-slate-600">
              Tu sistema POS está preparado
            </p>
          </div>

          <!-- Descripción -->
          <p class="text-base font-medium text-slate-500 max-w-md mx-auto">
            Configuremos tu tienda en 3 pasos sencillos. 
            En menos de 2 minutos estarás listo para vender.
          </p>

          <!-- CTA Button con tu estilo -->
          <div class="pt-4">
            <button 
              @click="startOnboarding"
              class="inline-flex items-center justify-center px-8 py-3 text-base font-bold text-white bg-slate-900 hover:bg-black rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-105 active:scale-95"
            >
              <span class="flex items-center space-x-2">
                <span>Comenzar Configuración</span>
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                </svg>
              </span>
            </button>
            
            <p class="text-xs font-bold text-slate-400 mt-3 uppercase tracking-wide">
              Diseño · Datos · WhatsApp
            </p>
          </div>

        </div>
      </Transition>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'

const router = useRouter()
const userName = ref('')
const loadingPhase = ref(1)

onMounted(() => {
  // Obtener nombre del usuario del localStorage o store
  const user = JSON.parse(localStorage.getItem('user') || '{}')
  userName.value = user.name || user.username || ''

  // Secuencia de animación tipo instalación de Windows (MÁS TIEMPO)
  // Fase 1: Loading inicial (2.5s)
  setTimeout(() => {
    loadingPhase.value = 2 // Saludo
  }, 2500)

  // Fase 2: Saludo (3.5s de duración)
  setTimeout(() => {
    loadingPhase.value = 3 // Preparando sistema
  }, 6000)

  // Fase 3: Preparando (4s de duración)
  setTimeout(() => {
    loadingPhase.value = 4 // Contenido final
  }, 10000)
})

const startOnboarding = () => {
  // Marcar que el usuario ya vio la pantalla de bienvenida
  localStorage.setItem('welcome_seen', 'true')
  
  // Transición suave al wizard
  router.push('/onboarding')
}
</script>

<style scoped>
/* === TRANSICIONES SUAVES Y CENTRADAS (SIN DESPLAZAMIENTOS LATERALES) === */

/* Transición principal: Fade suave sin movimiento */
.smooth-fade-enter-active {
  transition: opacity 1s cubic-bezier(0.4, 0, 0.2, 1);
}

.smooth-fade-leave-active {
  transition: opacity 0.8s cubic-bezier(0.4, 0, 0.2, 1);
}

.smooth-fade-enter-from {
  opacity: 0;
}

.smooth-fade-leave-to {
  opacity: 0;
}

.smooth-fade-enter-to,
.smooth-fade-leave-from {
  opacity: 1;
}

/* Barra de progreso animada */
@keyframes progress {
  0% {
    width: 0%;
  }
  100% {
    width: 100%;
  }
}

.animate-progress {
  animation: progress 2.5s cubic-bezier(0.4, 0, 0.2, 1) forwards;
}

/* Spin suave para el engranaje */
@keyframes spin-slow {
  from {
    transform: rotate(0deg);
  }
  to {
    transform: rotate(360deg);
  }
}

.animate-spin-slow {
  animation: spin-slow 3s linear infinite;
}
</style>
