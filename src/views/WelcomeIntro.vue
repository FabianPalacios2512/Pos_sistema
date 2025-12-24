<template>
  <div class="min-h-screen bg-[#EEF2F6] flex items-center justify-center p-4 font-sans overflow-hidden">
    
    <!-- Contenedor centrado absoluto para evitar desplazamientos -->
    <div class="absolute inset-0 flex items-center justify-center p-4">
      
      <!-- FASE 0: Selección del tipo de tienda (NUEVO) -->
      <Transition name="smooth-fade" mode="out-in">
        <div v-if="loadingPhase === 0" key="store-type" class="text-center space-y-8 max-w-4xl w-full">
          <!-- Título -->
          <div class="space-y-3">
            <h1 class="text-3xl md:text-4xl font-black text-slate-900 tracking-tight">
              ¿Qué tipo de negocio tienes?
            </h1>
            <p class="text-base font-medium text-slate-600">
              Selecciona el modelo que mejor describe tu tienda
            </p>
          </div>

          <!-- Tarjetas de selección estilo Odoo -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6 max-w-3xl mx-auto">
            
            <!-- Opción: Retail General -->
            <button 
              @click="selectStoreType('general')"
              :disabled="savingStoreType"
              class="group relative bg-white rounded-2xl p-8 border-2 border-gray-200 hover:border-slate-900 hover:shadow-2xl transition-all duration-300 text-left transform hover:-translate-y-1 disabled:opacity-50 disabled:cursor-not-allowed"
            >
              <div class="flex flex-col items-center space-y-4">
                <!-- Icono -->
                <div class="w-20 h-20 bg-blue-50 rounded-2xl flex items-center justify-center group-hover:bg-blue-100 transition-colors">
                  <svg class="w-10 h-10 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                  </svg>
                </div>
                
                <!-- Texto -->
                <div class="text-center">
                  <h3 class="text-xl font-bold text-slate-900 mb-2">Retail General</h3>
                  <p class="text-sm text-slate-600 font-medium">
                    Mini Market, Ferretería, Papelería, Farmacia, Supermercado, Abarrotes
                  </p>
                </div>

                <!-- Badge "Recomendado" -->
                <span class="absolute top-4 right-4 px-3 py-1 bg-blue-100 text-blue-700 text-xs font-bold rounded-full">
                  Más usado
                </span>
              </div>
            </button>

            <!-- Opción: Moda & Boutique -->
            <button 
              @click="selectStoreType('fashion')"
              :disabled="savingStoreType"
              class="group relative bg-white rounded-2xl p-8 border-2 border-gray-200 hover:border-slate-900 hover:shadow-2xl transition-all duration-300 text-left transform hover:-translate-y-1 disabled:opacity-50 disabled:cursor-not-allowed"
            >
              <div class="flex flex-col items-center space-y-4">
                <!-- Icono -->
                <div class="w-20 h-20 bg-purple-50 rounded-2xl flex items-center justify-center group-hover:bg-purple-100 transition-colors">
                  <svg class="w-10 h-10 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"/>
                  </svg>
                </div>
                
                <!-- Texto -->
                <div class="text-center">
                  <h3 class="text-xl font-bold text-slate-900 mb-2">Moda & Boutique</h3>
                  <p class="text-sm text-slate-600 font-medium">
                    Ropa, Calzado, Accesorios, Joyería, Perfumería
                  </p>
                </div>
              </div>
            </button>
          </div>

          <!-- Loader mientras guarda -->
          <div v-if="savingStoreType" class="flex items-center justify-center gap-3 text-slate-600">
            <div class="w-5 h-5 border-2 border-slate-300 border-t-slate-900 rounded-full animate-spin"></div>
            <span class="text-sm font-semibold">Guardando configuración...</span>
          </div>
        </div>

        <!-- FASE 1: Animación de carga inicial (0s - 2s) -->
        <div v-else-if="loadingPhase === 1" key="loading" class="text-center space-y-6">
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
    console.log(`🏪 Guardando tipo de tienda en localStorage: ${storeType}`)
    
    // Guardar temporalmente en localStorage
    localStorage.setItem('pending_store_type', storeType)
    
    // Actualizar el store local si existe
    if (appStore.systemSettings) {
      appStore.systemSettings.store_type = storeType
    }
    
    console.log('✅ Tipo de tienda guardado en localStorage')
    
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
