<template>
  <div class="min-h-screen bg-gradient-to-br from-slate-50 via-slate-100 to-slate-200 flex items-center justify-center p-4 font-sans overflow-hidden relative">
    
    <!-- Patrón de fondo premium -->
    <div class="absolute inset-0 bg-[radial-gradient(#94a3b8_0.5px,transparent_0.5px)] [background-size:24px_24px] opacity-30"></div>
    
    <!-- Círculos decorativos flotantes -->
    <div class="absolute top-20 left-20 w-72 h-72 bg-gradient-to-br from-blue-400/20 to-cyan-400/20 rounded-full blur-3xl"></div>
    <div class="absolute bottom-20 right-20 w-96 h-96 bg-gradient-to-br from-purple-400/20 to-pink-400/20 rounded-full blur-3xl"></div>
    
    <!-- Contenedor centrado absoluto para evitar desplazamientos -->
    <div class="absolute inset-0 flex items-center justify-center p-4">
      
      <!-- FASE 0: Selección del tipo de tienda -->
      <Transition name="smooth-fade" mode="out-in">
        <div v-if="loadingPhase === 0" key="store-type" class="text-center space-y-8 max-w-4xl w-full relative z-10">
          <!-- Título -->
          <div class="space-y-3">
            <div class="inline-flex items-center gap-2 px-4 py-2 bg-white/80 backdrop-blur-sm rounded-full border border-slate-200 shadow-sm mb-4">
              <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
              <span class="text-xs font-bold text-slate-600 uppercase tracking-wide">Paso Final</span>
            </div>
            <h1 class="text-3xl md:text-4xl font-black text-slate-900 tracking-tight">
              ¿Qué tipo de negocio tienes?
            </h1>
            <p class="text-base font-medium text-slate-500 max-w-lg mx-auto">
              Selecciona el modelo que mejor describe tu tienda para personalizar tu experiencia
            </p>
          </div>

          <!-- Tarjetas de selección premium -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6 max-w-3xl mx-auto">
            
            <!-- Opción: Retail General -->
            <button 
              @click="selectStoreType('general')"
              :disabled="savingStoreType"
              class="group relative bg-white rounded-2xl p-8 border-2 border-slate-200 hover:border-slate-900 hover:shadow-2xl hover:shadow-slate-900/10 transition-all duration-300 text-left transform hover:-translate-y-1 disabled:opacity-50 disabled:cursor-not-allowed"
            >
              <div class="flex flex-col items-center space-y-4">
                <!-- Icono con gradiente -->
                <div class="w-20 h-20 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-2xl flex items-center justify-center shadow-lg shadow-blue-500/25 group-hover:shadow-xl group-hover:shadow-blue-500/30 transition-all">
                  <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                  </svg>
                </div>
                
                <!-- Texto -->
                <div class="text-center">
                  <h3 class="text-xl font-bold text-slate-900 mb-2">Retail General</h3>
                  <p class="text-sm text-slate-500 font-medium">
                    Mini Market, Ferretería, Papelería, Farmacia, Supermercado
                  </p>
                </div>

                <!-- Badge "Más usado" -->
                <span class="absolute top-4 right-4 px-3 py-1.5 bg-gradient-to-r from-blue-500 to-indigo-600 text-white text-xs font-bold rounded-full shadow-lg">
                  Más usado
                </span>
              </div>
            </button>

            <!-- Opción: Moda & Boutique -->
            <button 
              @click="selectStoreType('fashion')"
              :disabled="savingStoreType"
              class="group relative bg-white rounded-2xl p-8 border-2 border-slate-200 hover:border-slate-900 hover:shadow-2xl hover:shadow-slate-900/10 transition-all duration-300 text-left transform hover:-translate-y-1 disabled:opacity-50 disabled:cursor-not-allowed"
            >
              <div class="flex flex-col items-center space-y-4">
                <!-- Icono con gradiente -->
                <div class="w-20 h-20 bg-gradient-to-br from-purple-500 to-pink-600 rounded-2xl flex items-center justify-center shadow-lg shadow-purple-500/25 group-hover:shadow-xl group-hover:shadow-purple-500/30 transition-all">
                  <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"/>
                  </svg>
                </div>
                
                <!-- Texto -->
                <div class="text-center">
                  <h3 class="text-xl font-bold text-slate-900 mb-2">Moda & Boutique</h3>
                  <p class="text-sm text-slate-500 font-medium">
                    Ropa, Calzado, Accesorios, Joyería, Perfumería
                  </p>
                </div>
              </div>
            </button>
          </div>

          <!-- Loader mientras guarda -->
          <div v-if="savingStoreType" class="flex items-center justify-center gap-3 text-slate-600">
            <div class="w-5 h-5 border-2 border-slate-300 border-t-slate-900 rounded-full animate-spin"></div>
            <span class="text-sm font-bold">Preparando tu configuración...</span>
          </div>
        </div>

        <!-- FASE 1: Animación de carga inicial -->
        <div v-else-if="loadingPhase === 1" key="loading" class="text-center space-y-8 relative z-10">
          <!-- Spinner premium con múltiples anillos -->
          <div class="flex justify-center">
            <div class="relative w-24 h-24">
              <div class="absolute inset-0 border-4 border-slate-100 rounded-full"></div>
              <div class="absolute inset-0 border-4 border-slate-900 rounded-full border-t-transparent animate-spin"></div>
              <div class="absolute inset-3 border-4 border-slate-200 rounded-full border-b-transparent animate-spin" style="animation-direction: reverse; animation-duration: 1.5s;"></div>
              <div class="absolute inset-0 flex items-center justify-center">
                <div class="w-3 h-3 bg-slate-900 rounded-full animate-pulse"></div>
              </div>
            </div>
          </div>
          <div class="space-y-2">
            <p class="text-xl font-bold text-slate-800">Inicializando sistema</p>
            <p class="text-sm text-slate-500">Preparando tu experiencia premium...</p>
          </div>
        </div>

        <!-- FASE 2: Saludo personalizado -->
        <div v-else-if="loadingPhase === 2" key="greeting" class="text-center space-y-6 max-w-lg relative z-10">
          <!-- Icono de bienvenida animado -->
          <div class="flex justify-center mb-2">
            <div class="relative">
              <div class="absolute inset-0 w-24 h-24 bg-gradient-to-br from-slate-900 to-slate-700 rounded-2xl transform rotate-6 opacity-20"></div>
              <div class="relative w-24 h-24 bg-gradient-to-br from-slate-800 to-slate-900 rounded-2xl flex items-center justify-center shadow-2xl shadow-slate-900/30">
                <span class="text-4xl">👋</span>
              </div>
            </div>
          </div>
          <div class="space-y-3">
            <h1 class="text-4xl md:text-5xl font-black text-slate-900 tracking-tight">
              ¡Hola<span v-if="userName" class="text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-cyan-600">, {{ userName }}</span>!
            </h1>
            <p class="text-lg font-semibold text-slate-500">
              Bienvenido a <span class="text-slate-900">105 POS</span>
            </p>
          </div>
        </div>

        <!-- FASE 3: Preparando el sistema -->
        <div v-else-if="loadingPhase === 3" key="preparing" class="text-center space-y-8 max-w-lg relative z-10">
          <div class="flex justify-center">
            <div class="relative">
              <div class="absolute -inset-4 bg-gradient-to-r from-blue-500/20 via-purple-500/20 to-pink-500/20 rounded-3xl blur-xl animate-pulse"></div>
              <div class="relative w-28 h-28 bg-white rounded-3xl flex items-center justify-center shadow-2xl border border-slate-100">
                <svg class="w-14 h-14 text-slate-900 animate-spin-slow" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
              </div>
            </div>
          </div>
          <div class="space-y-3">
            <h2 class="text-2xl font-black text-slate-900 tracking-tight">
              Preparando todo para ti
            </h2>
            <p class="text-base text-slate-500">
              Configurando tu espacio de trabajo personalizado...
            </p>
          </div>
          <!-- Barra de progreso premium -->
          <div class="w-full max-w-xs mx-auto">
            <div class="h-2 bg-slate-200 rounded-full overflow-hidden shadow-inner">
              <div class="h-full bg-gradient-to-r from-slate-700 to-slate-900 rounded-full animate-progress"></div>
            </div>
          </div>
        </div>

        <!-- FASE 4: Contenido final -->
        <div v-else-if="loadingPhase === 4" key="content" class="relative z-10 max-w-xl w-full text-center space-y-8">
          
          <!-- Card principal premium -->
          <div class="bg-white/80 backdrop-blur-xl rounded-3xl p-10 shadow-2xl shadow-slate-900/10 border border-white">
            
            <!-- Icono de éxito animado -->
            <div class="flex justify-center mb-6">
              <div class="relative">
                <div class="absolute -inset-2 bg-gradient-to-r from-emerald-400 to-cyan-400 rounded-2xl blur-lg opacity-30 animate-pulse"></div>
                <div class="relative w-20 h-20 bg-gradient-to-br from-slate-800 to-slate-900 rounded-2xl flex items-center justify-center shadow-xl">
                  <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path>
                  </svg>
                </div>
              </div>
            </div>

            <!-- Título y descripción -->
            <div class="space-y-3 mb-8">
              <h1 class="text-3xl md:text-4xl font-black text-slate-900 tracking-tight">
                ¡Todo listo!
              </h1>
              <p class="text-lg font-semibold text-slate-600">
                Tu sistema POS está preparado
              </p>
              <p class="text-sm text-slate-400 max-w-sm mx-auto">
                Configuremos tu tienda en <strong class="text-slate-600">3 pasos sencillos</strong>. 
                En menos de 2 minutos estarás listo para vender.
              </p>
            </div>

            <!-- CTA Button premium -->
            <button 
              @click="startOnboarding"
              class="group relative inline-flex items-center justify-center px-10 py-4 text-base font-bold text-white bg-gradient-to-r from-slate-800 to-slate-900 hover:from-slate-900 hover:to-black rounded-xl shadow-lg shadow-slate-900/25 hover:shadow-xl hover:shadow-slate-900/30 transition-all duration-300 transform hover:scale-105 active:scale-95"
            >
              <span class="flex items-center gap-3">
                <span>Comenzar Configuración</span>
                <svg class="w-5 h-5 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                </svg>
              </span>
            </button>
            
            <!-- Steps indicators -->
            <div class="flex items-center justify-center gap-6 mt-8 pt-6 border-t border-slate-100">
              <div class="flex items-center gap-2 text-xs text-slate-400">
                <div class="w-6 h-6 rounded-lg bg-slate-100 flex items-center justify-center">
                  <span class="text-slate-600 font-bold">1</span>
                </div>
                <span class="font-semibold">Diseño</span>
              </div>
              <div class="w-4 h-px bg-slate-200"></div>
              <div class="flex items-center gap-2 text-xs text-slate-400">
                <div class="w-6 h-6 rounded-lg bg-slate-100 flex items-center justify-center">
                  <span class="text-slate-600 font-bold">2</span>
                </div>
                <span class="font-semibold">Datos</span>
              </div>
              <div class="w-4 h-px bg-slate-200"></div>
              <div class="flex items-center gap-2 text-xs text-slate-400">
                <div class="w-6 h-6 rounded-lg bg-slate-100 flex items-center justify-center">
                  <span class="text-slate-600 font-bold">3</span>
                </div>
                <span class="font-semibold">WhatsApp</span>
              </div>
            </div>
          </div>

        </div>
      </Transition>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { appStore } from '@/store/appStore'
import axios from 'axios'

const router = useRouter()
const userName = ref('')
const loadingPhase = ref(1) // Empezar en 1 para mostrar animación primero
const savingStoreType = ref(false)

onMounted(() => {
  // Obtener nombre del usuario del localStorage o store
  const user = JSON.parse(localStorage.getItem('user') || '{}')
  userName.value = user.name || user.username || ''
  
  // Iniciar la secuencia de animación automáticamente
  startWelcomeSequence()
})

// Función para guardar el tipo de tienda seleccionado
const selectStoreType = async (storeType) => {
  try {
    savingStoreType.value = true
    
    // Guardar temporalmente en localStorage
    localStorage.setItem('pending_store_type', storeType)
    
    // ✅ Marcar welcome como visto para evitar bucle de redirección
    localStorage.setItem('welcome_seen', 'true')
    
    // Actualizar el store local si existe
    if (appStore.systemSettings) {
      appStore.systemSettings.store_type = storeType
    }
    
    // Esperar un momento para el feedback visual
    setTimeout(() => {
      savingStoreType.value = false
      // Ir directamente al onboarding
      router.push('/onboarding')
    }, 800)
    
  } catch (error) {
    console.error('❌ Error al guardar tipo de tienda:', error)
    alert('No se pudo guardar la configuración. Por favor intenta nuevamente.')
    savingStoreType.value = false
  }
}

// Secuencia de animación de bienvenida
const startWelcomeSequence = () => {
  // Fase 1: Loading inicial (2.5s)
  loadingPhase.value = 1
  
  setTimeout(() => {
    loadingPhase.value = 2 // Saludo
  }, 2500)

  // Fase 2: Saludo (3.5s de duración)
  setTimeout(() => {
    loadingPhase.value = 3 // Preparando sistema
  }, 6000)

  // Fase 3: Preparando (4s de duración)
  setTimeout(() => {
    loadingPhase.value = 0 // Mostrar selección de tipo de tienda
  }, 10000)
}

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
