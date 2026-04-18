<template>
  <div class="min-h-screen bg-gradient-to-b from-white via-slate-50 to-slate-100 flex items-center justify-center p-4 font-sans overflow-hidden relative">
    
    <!-- Patrón de cuadrícula sutil -->
    <div class="absolute inset-0 bg-[linear-gradient(rgba(148,163,184,0.03)_1px,transparent_1px),linear-gradient(90deg,rgba(148,163,184,0.03)_1px,transparent_1px)] bg-[size:4rem_4rem]"></div>
    
    <!-- Contenedor centrado absoluto para evitar desplazamientos -->
    <div class="absolute inset-0 flex items-center justify-center p-4">
      
      <!-- FASE 0: Selección del tipo de tienda -->
      <Transition name="smooth-fade" mode="out-in">
        <div v-if="loadingPhase === 0" key="store-type" class="text-center space-y-8 max-w-4xl w-full relative z-10">
          <!-- Título -->
          <div class="space-y-3">
            <div class="inline-flex items-center gap-2 px-4 py-2 bg-slate-100 rounded-full border border-slate-200 mb-4">
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
          <div class="grid grid-cols-1 md:grid-cols-3 gap-5 max-w-5xl mx-auto mt-8">
            
            <!-- Tarjeta 1: Retail General -->
            <button 
              @click="selectStoreType('general')"
              :disabled="savingStoreType"
              class="relative block w-full text-left p-6 bg-white border border-slate-200 rounded-xl cursor-pointer hover:border-[#009F7A]/40 hover:bg-slate-50 transition-all group focus:outline-none focus:ring-2 focus:ring-[#009F7A]/50 disabled:opacity-50 disabled:cursor-not-allowed"
            >
              <!-- Icono Corporativo (Storefront) -->
              <div class="mb-4 text-slate-600 group-hover:text-slate-800 transition-colors">
                <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 21v-7.5a.75.75 0 01.75-.75h3a.75.75 0 01.75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349m-16.5 11.65V9.35m0 0a3.001 3.001 0 003.75-.615A2.993 2.993 0 009.75 9.75c.896 0 1.7-.393 2.25-1.016a2.993 2.993 0 002.25 1.016c.896 0 1.7-.393 2.25-1.016a3.001 3.001 0 003.75.614m-16.5 0a3.004 3.004 0 01-.621-4.72L4.318 3.44A1.5 1.5 0 015.378 3h13.243a1.5 1.5 0 011.06.44l1.19 1.189a3 3 0 01-.621 4.72m-13.5 8.65h3.75a.75.75 0 00.75-.75V13.5a.75.75 0 00-.75-.75H6.75a.75.75 0 00-.75.75v3.75c0 .415.336.75.75.75z" />
                </svg>
              </div>

              <div class="flex items-center gap-2 mb-2">
                <h3 class="text-lg font-bold text-slate-900 leading-tight">Retail General</h3>
                <!-- Badge Minimalista -->
                <span class="bg-emerald-50 text-[#009F7A] text-[10px] font-bold px-2 py-0.5 rounded-full uppercase tracking-wide">Más usado</span>
              </div>
              <p class="text-sm text-slate-600 leading-relaxed">Mini Market, Ferretería, Papelería, Farmacia</p>
            </button>

            <!-- Tarjeta 2: Moda & Boutique -->
            <button 
              @click="selectStoreType('fashion')"
              :disabled="savingStoreType"
              class="relative block w-full text-left p-6 bg-white border border-slate-200 rounded-xl cursor-pointer hover:border-[#009F7A]/40 hover:bg-slate-50 transition-all group focus:outline-none focus:ring-2 focus:ring-[#009F7A]/50 disabled:opacity-50 disabled:cursor-not-allowed"
            >
              <!-- Icono Corporativo (Shopping Bag) -->
              <div class="mb-4 text-slate-600 group-hover:text-slate-800 transition-colors">
                <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993l1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 01-1.12-1.243l1.264-12A1.125 1.125 0 015.513 7.5h12.974c.576 0 1.059.435 1.119 1.007zM8.625 10.5a.375.375 0 11-.75 0 .375.375 0 01.75 0zm7.5 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
                </svg>
              </div>

              <div class="flex items-center gap-2 mb-2">
                <h3 class="text-lg font-bold text-slate-900 leading-tight">Moda & Boutique</h3>
              </div>
              <p class="text-sm text-slate-500 leading-relaxed">Ropa, Calzado, Accesorios, Joyería</p>
            </button>

            <!-- Tarjeta 3: Restaurante -->
            <button 
              @click="selectStoreType('food')"
              :disabled="savingStoreType"
              class="relative block w-full text-left p-6 bg-white border border-slate-200 rounded-xl cursor-pointer hover:border-[#009F7A]/40 hover:bg-slate-50 transition-all group focus:outline-none focus:ring-2 focus:ring-[#009F7A]/50 disabled:opacity-50 disabled:cursor-not-allowed"
            >
              <!-- Icono Corporativo (Plato Campana Cloche) -->
              <div class="mb-4 text-slate-600 group-hover:text-slate-800 transition-colors">
                <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2m0 0a7.5 7.5 0 0 0-7.5 7.5c0 .414.336.75.75.75h13.5a.75.75 0 0 0 .75-.75A7.5 7.5 0 0 0 12 5ZM3 15h18" />
                </svg>
              </div>

              <div class="flex items-center gap-2 mb-2">
                <h3 class="text-lg font-bold text-slate-900 leading-tight">Restaurante</h3>
                <!-- Badge Minimalista Neutro -->
                <span class="bg-slate-100 text-slate-600 text-[10px] font-bold px-2 py-0.5 rounded-full uppercase tracking-wide">Nuevo</span>
              </div>
              <p class="text-sm text-slate-500 leading-relaxed">Comidas rápidas, Cafetería, Heladería</p>
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
          <!-- Emoji saludando -->
          <div class="flex justify-center mb-4">
            <div class="text-8xl animate-wave-slow"></div>
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
              <div class="absolute -inset-4 bg-gradient-to-r from-blue-500/10 via-purple-500/10 to-pink-500/10 rounded-3xl blur-xl animate-pulse"></div>
              <div class="relative w-28 h-28 bg-white rounded-3xl flex items-center justify-center shadow-xl border border-slate-200">
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
          <div class="bg-white rounded-3xl p-10 shadow-xl border border-slate-200">
            
            <!-- Icono de éxito animado -->
            <div class="flex justify-center mb-6">
              <div class="relative">
                <div class="absolute -inset-2 bg-gradient-to-r from-emerald-400 to-cyan-400 rounded-2xl blur-lg opacity-20 animate-pulse"></div>
                <div class="relative w-20 h-20 bg-gradient-to-br from-emerald-500 to-cyan-500 rounded-2xl flex items-center justify-center shadow-xl">
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
              <p class="text-sm text-slate-500 max-w-sm mx-auto">
                Configuremos tu tienda en <strong class="text-slate-700">3 pasos sencillos</strong>. 
                En menos de 2 minutos estarás listo para vender.
              </p>
            </div>

            <!-- CTA Button premium -->
            <button 
              @click="startOnboarding"
              class="group relative inline-flex items-center justify-center px-10 py-4 text-base font-bold text-white bg-slate-900 hover:bg-slate-800 rounded-xl shadow-lg shadow-slate-900/20 hover:shadow-xl transition-all duration-300 transform hover:scale-105 active:scale-95"
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
import { useToast } from '@/composables/useToast.js'

const { showError } = useToast()

const router = useRouter()
const userName = ref('')
const loadingPhase = ref(1) // Empezar en 1 para mostrar animación primero
const savingStoreType = ref(false)

onMounted(async () => {
  // Obtener nombre del usuario del localStorage o store
  const user = JSON.parse(localStorage.getItem('user') || '{}')
  userName.value = user.name || user.username || ''
  
  // Si no hay nombre, intentar obtenerlo del backend
  if (!userName.value) {
    try {
      const token = localStorage.getItem('authToken')
      if (token) {
        const response = await axios.get('/api/me', {
          headers: { Authorization: `Bearer ${token}` }
        })
        if (response.data?.success) {
          const freshUser = response.data.data?.user || response.data.data
          if (freshUser?.name) {
            userName.value = freshUser.name
            // Actualizar localStorage con datos frescos
            localStorage.setItem('user', JSON.stringify(freshUser))
          }
        }
      }
    } catch (e) {
      // Continuar sin nombre - la animación mostrará "¡Hola!" sin nombre
    }
  }
  
  // Iniciar la secuencia de animación automáticamente
  startWelcomeSequence()
})

// Función para guardar el tipo de tienda seleccionado
const selectStoreType = async (storeType) => {
  try {
    savingStoreType.value = true
    
    // Guardar temporalmente en localStorage
    localStorage.setItem('pending_store_type', storeType)
    
    // Marcar welcome como visto para evitar bucle de redirección
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
    console.error('Error al guardar tipo de tienda:', error)
    showError('No se pudo guardar la configuración. Por favor intenta nuevamente.')
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

  // Fase 2: Saludo (4.5s de duración - +1 segundo más)
  setTimeout(() => {
    loadingPhase.value = 3 // Preparando sistema
  }, 7000)

  // Fase 3: Preparando (4s de duración)
  setTimeout(() => {
    loadingPhase.value = 0 // Mostrar selección de tipo de tienda
  }, 11000)
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

/* Animación de saludo con la mano */
@keyframes wave {
  0%, 100% {
    transform: rotate(0deg);
  }
  25% {
    transform: rotate(-15deg);
  }
  75% {
    transform: rotate(15deg);
  }
}

.animate-wave {
  animation: wave 1s ease-in-out infinite;
}

/* Animación más suave para el emoji */
@keyframes wave-slow {
  0%, 100% {
    transform: rotate(0deg);
  }
  20% {
    transform: rotate(-10deg);
  }
  40% {
    transform: rotate(12deg);
  }
  60% {
    transform: rotate(-10deg);
  }
  80% {
    transform: rotate(8deg);
  }
}

.animate-wave-slow {
  display: inline-block;
  animation: wave-slow 2.5s ease-in-out infinite;
  transform-origin: 70% 70%;
}
</style>
