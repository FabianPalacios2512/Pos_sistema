<template>
  <Teleport to="body">
    <!-- Modal de Bienvenida (Pregunta antes de iniciar) -->
    <Transition name="fade">
      <div v-if="showWelcomeModal" class="fixed inset-0 z-[9999] flex items-center justify-center bg-black/50 backdrop-blur-sm p-4">
        <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full overflow-hidden animate-scale-in">
          <div class="p-6 text-center">
            <div class="w-16 h-16 bg-indigo-50 rounded-2xl flex items-center justify-center mx-auto mb-4 text-indigo-600">
              <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
              </svg>
            </div>
            <h2 class="text-xl font-black text-slate-900 mb-2">¡Bienvenido a 105 POS! 👋</h2>
            <p class="text-slate-500 text-sm leading-relaxed mb-6">
              Vemos que es tu primera vez aquí. ¿Te gustaría hacer un recorrido rápido para conocer las funciones principales?
            </p>
            <div class="flex gap-3">
              <button 
                @click="skipTour"
                class="flex-1 px-4 py-3 rounded-xl bg-slate-100 text-slate-600 font-bold text-sm hover:bg-slate-200 transition-colors"
              >
                No, gracias
              </button>
              <button 
                @click="startTourConfirmed"
                class="flex-1 px-4 py-3 rounded-xl bg-indigo-600 text-white font-bold text-sm hover:bg-indigo-700 transition-colors shadow-lg shadow-indigo-200"
              >
                ¡Sí, vamos! 🚀
              </button>
            </div>
          </div>
        </div>
      </div>
    </Transition>

    <!-- Spotlight Overlay -->
    <Transition name="fade">
      <div v-if="isActive" class="fixed inset-0 pointer-events-none z-[99999]">
        <!-- SVG mask para spotlight effect -->
        <svg class="absolute inset-0 w-full h-full">
          <defs>
            <mask :id="`spotlight-mask-${currentStepIndex}`">
              <rect x="0" y="0" width="100%" height="100%" fill="white"/>
              <!-- Recorte para el elemento resaltado -->
              <rect 
                v-if="highlightRect"
                :x="highlightRect.x - 8"
                :y="highlightRect.y - 8"
                :width="highlightRect.width + 16"
                :height="highlightRect.height + 16"
                :rx="12"
                fill="black"
              />
            </mask>
          </defs>
          <rect 
            x="0" y="0" 
            width="100%" 
            height="100%" 
            fill="rgba(0, 0, 0, 0.8)"
            :mask="`url(#spotlight-mask-${currentStepIndex})`"
          />
        </svg>

        <!-- 🎯 Tooltip Flotante Estilo Intercom/Driver.js - DISEÑO LIMPIO -->
        <Transition name="tooltip-slide">
          <div 
            v-if="highlightRect && currentTooltipPosition"
            :style="{
              position: 'fixed',
              left: currentTooltipPosition.x + 'px',
              top: currentTooltipPosition.y + 'px',
              transform: 'translateX(-50%)',
              maxHeight: 'calc(100vh - 100px)'
            }"
            class="pointer-events-auto w-[90vw] max-w-[380px] transition-opacity duration-200"
            :class="{ 'opacity-0': isTransitioning, 'opacity-100': !isTransitioning }"
          >
            <!-- Popover Blanco Profesional con Elevación por Luminosidad -->
            <div class="bg-white dark:bg-slate-800 rounded-xl shadow-2xl border border-gray-200 dark:border dark:border-white/10 overflow-hidden mx-2 sm:mx-0">
              
              <!-- Header Limpio con Contador -->
              <div class="px-5 pt-4 pb-3 flex items-start justify-between border-b border-gray-100 dark:border-white/10">
                <div class="flex-1 min-w-0">
                  <h3 class="text-gray-900 dark:text-gray-100 font-bold text-base leading-tight">{{ currentStep.title }}</h3>
                </div>
                <div class="ml-3 flex-shrink-0">
                  <span class="inline-flex items-center px-2.5 py-1 rounded-lg bg-gray-100 dark:bg-slate-700 text-gray-600 dark:text-gray-300 text-xs font-semibold">
                    {{ currentStepIndex + 1 }}/{{ steps.length }}
                  </span>
                </div>
              </div>

              <!-- Contenido Limpio (Sin cajas de colores) -->
              <div class="px-5 py-4 max-h-[45vh] overflow-y-auto">
                <div class="text-gray-600 dark:text-gray-400 leading-relaxed text-sm space-y-3" v-html="currentStep.content"></div>
              </div>

              <!-- Footer con Botones -->
              <div class="px-5 py-3 bg-gray-50 dark:bg-slate-900 border-t border-gray-100 dark:border-white/10 flex items-center justify-between gap-3">
                <!-- Lado izquierdo: Omitir (solo primer paso) -->
                <button 
                  v-if="currentStepIndex === 0"
                  @click="skipTour"
                  class="text-xs text-gray-500 dark:text-gray-500 hover:text-gray-700 dark:hover:text-gray-400 font-medium transition-colors"
                >
                  Omitir Tour
                </button>
                
                <!-- Botón Anterior (resto de pasos) -->
                <button 
                  v-if="currentStepIndex > 0"
                  @click="previousStep"
                  class="px-4 py-2 rounded-lg text-gray-600 dark:text-gray-300 font-semibold text-sm hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors border border-gray-200 dark:border-white/10"
                >
                  Anterior
                </button>

                <!-- Espaciador flexible -->
                <div class="flex-1"></div>

                <!-- Botón Siguiente (Verde Esmeralda Optimizado) -->
                <button 
                  @click="nextStep"
                  class="px-5 py-2 rounded-lg bg-emerald-600 dark:bg-emerald-600 hover:bg-emerald-700 dark:hover:bg-emerald-500 text-white dark:text-slate-900 font-bold text-sm transition-all shadow-lg shadow-emerald-600/20 dark:shadow-emerald-500/20 flex items-center gap-2"
                >
                  <span>{{ currentStepIndex === steps.length - 1 ? 'Finalizar' : 'Siguiente' }}</span>
                  <svg v-if="currentStepIndex < steps.length - 1" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                  </svg>
                  <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                  </svg>
                </button>
              </div>
            </div>

            <!-- Flecha/Arrow apuntando al elemento con bordes definidos -->
            <div 
              class="absolute w-3 h-3 bg-white dark:bg-slate-800 transform rotate-45 hidden sm:block"
              :class="{
                'border-l border-t border-gray-200 dark:border-white/10': currentTooltipPosition.arrowSide === 'bottom',
                'border-r border-b border-gray-200 dark:border-white/10': currentTooltipPosition.arrowSide === 'top',
                'border-t border-r border-gray-200 dark:border-white/10': currentTooltipPosition.arrowSide === 'left',
                'border-l border-b border-gray-200 dark:border-white/10': currentTooltipPosition.arrowSide === 'right'
              }"
              :style="{
                left: '50%',
                marginLeft: '-6px',
                [currentTooltipPosition.arrowSide]: '-6px'
              }"
            ></div>
          </div>
        </Transition>
      </div>
    </Transition>
  </Teleport>
</template>

<script setup>
import { ref, computed, watch, nextTick, onMounted, onUnmounted } from 'vue'

const props = defineProps({
  moduleName: {
    type: String,
    required: true
  },
  steps: {
    type: Array,
    default: () => []
  },
  autoStart: {
    type: Boolean,
    default: false
  }
})

const emit = defineEmits(['complete', 'skip', 'step-change'])

const isActive = ref(false)
const currentStepIndex = ref(0)
const highlightRect = ref(null)
const currentTooltipPosition = ref(null)

const currentStep = computed(() => props.steps[currentStepIndex.value] || {})

// 🔧 DEV MODE - desactivado para evitar scroll automático
const DEV_MODE = false

// Calcular posición del elemento resaltado
const updateHighlight = async () => {
  await nextTick()
  
  if (!currentStep.value.selector) {
    highlightRect.value = null
    currentTooltipPosition.value = null
    return
  }

  const element = document.querySelector(currentStep.value.selector)
  if (!element) {
    console.warn(`Elemento no encontrado: ${currentStep.value.selector}`)
    highlightRect.value = null
    
    // 🔥 AUTO-SKIP: Si el elemento no existe, pasar automáticamente al siguiente paso
    // Esto evita que el tour se quede bloqueado
    if (currentStepIndex.value < props.steps.length - 1) {
      console.log('⏭️ Saltando paso automáticamente porque el elemento no existe')
      setTimeout(() => {
        nextStep()
      }, 500) // Pequeño delay para que el usuario vea el mensaje
    } else {
      // Si es el último paso y no existe, completar el tour
      console.log('✅ Completando tour porque el último elemento no existe')
      setTimeout(() => {
        completeTour()
      }, 500)
    }
    return
  }

  // 🎯 SCROLL AUTOMÁTICO SUAVE - Solo si el elemento no está visible
  const rect = element.getBoundingClientRect()
  const isVisible = (
    rect.top >= 0 &&
    rect.left >= 0 &&
    rect.bottom <= window.innerHeight &&
    rect.right <= window.innerWidth
  )

  if (!isVisible) {
    element.scrollIntoView({
      behavior: 'smooth',
      block: 'nearest',  // Cambiar de 'center' a 'nearest' para menos scroll
      inline: 'nearest'
    })
    // Delay para que termine el scroll
    await new Promise(resolve => setTimeout(resolve, 400))
  }

  // Recalcular posición después del scroll
  const finalRect = element.getBoundingClientRect()
  highlightRect.value = {
    x: finalRect.left,
    y: finalRect.top,
    width: finalRect.width,
    height: finalRect.height
  }

  // Calcular posición del tooltip (con ajuste para que se vea completo)
  const tooltipWidth = 350 // max-w-[350px]
  const tooltipHeight = 300 // Estimado conservador
  const spaceBelow = window.innerHeight - finalRect.bottom
  const spaceAbove = finalRect.top
  
  // Calcular posición X centrada, pero ajustada a los límites de la pantalla
  let tooltipX = rect.left + rect.width / 2
  const padding = 16 // Margen mínimo del borde
  
  // Ajustar si el tooltip se sale por la derecha
  if (tooltipX + tooltipWidth / 2 > window.innerWidth - padding) {
    tooltipX = window.innerWidth - tooltipWidth / 2 - padding
  }
  
  // Ajustar si el tooltip se sale por la izquierda
  if (tooltipX - tooltipWidth / 2 < padding) {
    tooltipX = tooltipWidth / 2 + padding
  }

  if (spaceBelow > tooltipHeight || spaceBelow > spaceAbove) {
    // Mostrar abajo (preferido si hay suficiente espacio)
    currentTooltipPosition.value = {
      x: tooltipX,
      y: rect.bottom + 20,
      arrowSide: 'top'
    }
  } else if (spaceAbove > tooltipHeight) {
    // Mostrar arriba
    currentTooltipPosition.value = {
      x: tooltipX,
      y: rect.top - 20,
      arrowSide: 'bottom'
    }
  } else {
    // Mostrar al lado si no cabe arriba ni abajo
    const spaceRight = window.innerWidth - rect.right
    const spaceLeft = rect.left
    
    if (spaceRight > spaceLeft) {
      // Mostrar a la derecha
      currentTooltipPosition.value = {
        x: rect.right + 20,
        y: Math.max(tooltipHeight / 2 + padding, Math.min(window.innerHeight - tooltipHeight / 2 - padding, rect.top + rect.height / 2)),
        arrowSide: 'left'
      }
    } else {
      // Mostrar a la izquierda
      currentTooltipPosition.value = {
        x: rect.left - 20,
        y: Math.max(tooltipHeight / 2 + padding, Math.min(window.innerHeight - tooltipHeight / 2 - padding, rect.top + rect.height / 2)),
        arrowSide: 'right'
      }
    }
  }
}

const showWelcomeModal = ref(false)
const isTransitioning = ref(false)

const nextStep = async () => {
  if (currentStepIndex.value < props.steps.length - 1) {
    isTransitioning.value = true
    // Esperar fade out (más rápido)
    await new Promise(resolve => setTimeout(resolve, 150))
    
    currentStepIndex.value++
    
    // Emitir evento de cambio de paso
    emit('step-change', currentStepIndex.value)
    
    await updateHighlight()
    
    isTransitioning.value = false
  } else {
    completeTour()
  }
}

const previousStep = async () => {
  if (currentStepIndex.value > 0) {
    isTransitioning.value = true
    // Esperar fade out (más rápido)
    await new Promise(resolve => setTimeout(resolve, 150))
    
    currentStepIndex.value--
    
    // Emitir evento de cambio de paso
    emit('step-change', currentStepIndex.value)
    
    await updateHighlight()
    
    isTransitioning.value = false
  }
}

const skipTour = () => {
  isActive.value = false
  showWelcomeModal.value = false
  currentStepIndex.value = 0
  highlightRect.value = null
  currentTooltipPosition.value = null
  
  // 🔓 Restaurar scroll del body
  document.body.style.overflow = ''
  document.body.style.pointerEvents = ''
  
  emit('skip')
}

const completeTour = () => {
  isActive.value = false
  showWelcomeModal.value = false
  currentStepIndex.value = 0
  highlightRect.value = null
  currentTooltipPosition.value = null
  
  // 🔓 Restaurar scroll del body
  document.body.style.overflow = ''
  document.body.style.pointerEvents = ''
  
  emit('complete')
}

const startTour = async () => {
  if (props.steps.length === 0) return
  
  // Mostrar modal de bienvenida primero
  showWelcomeModal.value = true
}

const startTourConfirmed = async () => {
  showWelcomeModal.value = false
  currentStepIndex.value = 0
  isActive.value = true
  
  // 🔒 Bloquear scroll del body
  document.body.style.overflow = 'hidden'
  
  await updateHighlight()
}

// Watch para cambios de tamaño/scroll
let resizeObserver
onMounted(() => {
  window.addEventListener('resize', updateHighlight)
  // ❌ NO escuchar scroll - está bloqueado durante el tour
  
  if (props.autoStart || DEV_MODE) {
    setTimeout(startTour, 500) // Pequeño delay para que el DOM esté listo
  }
})

onUnmounted(() => {
  window.removeEventListener('resize', updateHighlight)
  
  // 🔓 Asegurar que el scroll se restaure al desmontar
  document.body.style.overflow = ''
})

// Exponer función para iniciar manualmente
defineExpose({
  startTour,
  startTourConfirmed,
  isActive
})
</script>

<style scoped>
.fade-enter-active, .fade-leave-active {
  transition: opacity 0.3s;
}
.fade-enter-from, .fade-leave-to {
  opacity: 0;
}

.tooltip-slide-enter-active, .tooltip-slide-leave-active {
  transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
}
.tooltip-slide-enter-from {
  opacity: 0;
  transform: translateX(-50%) translateY(-10px);
}
.tooltip-slide-leave-to {
  opacity: 0;
  transform: translateX(-50%) translateY(10px);
}
</style>
