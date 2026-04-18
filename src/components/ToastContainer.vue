<template>
  <Teleport to="body">
    <div class="fixed top-4 right-4 space-y-3 pointer-events-none max-w-sm w-full" style="z-index: 2147483647;">
      <TransitionGroup name="toast" tag="div" class="space-y-3">
        <div
          v-for="toast in toasts"
          :key="toast.id"
          :class="[
            'w-full bg-white dark:bg-zinc-900 shadow-xl dark:shadow-black/50 rounded-xl pointer-events-auto ring-1 ring-black/10 dark:ring-white/10 overflow-hidden toast-container',
            'transform transition-all duration-300 ease-in-out border border-gray-200 dark:border-zinc-800'
          ]"
        >
          <div class="p-4">
            <div class="flex items-start space-x-3">
              <!-- Icono basado en el tipo -->
              <div class="flex-shrink-0 mt-0.5">
                <!-- Success Icon -->
                <div v-if="toast.type === 'success'" class="w-6 h-6 bg-emerald-100 dark:bg-emerald-950 rounded-full flex items-center justify-center">
                  <svg class="h-4 w-4 text-emerald-600 dark:text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                  </svg>
                </div>
                
                <!-- Error Icon -->
                <div v-else-if="toast.type === 'error'" class="w-6 h-6 bg-rose-100 dark:bg-rose-950 rounded-full flex items-center justify-center">
                  <svg class="h-4 w-4 text-rose-600 dark:text-rose-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                  </svg>
                </div>
                
                <!-- Warning Icon -->
                <div v-else-if="toast.type === 'warning'" class="w-6 h-6 bg-amber-100 dark:bg-amber-950 rounded-full flex items-center justify-center">
                  <svg class="h-4 w-4 text-amber-600 dark:text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01" />
                  </svg>
                </div>
                
                <!-- Info Icon -->
                <div v-else class="w-6 h-6 bg-blue-100 dark:bg-blue-950 rounded-full flex items-center justify-center">
                  <svg class="h-4 w-4 text-blue-600 dark:text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01" />
                  </svg>
                </div>
              </div>
              
              <!-- Contenido del toast -->
              <div class="flex-1 min-w-0">
                <p :class="[
                  'text-sm font-medium leading-5 break-words',
                  toast.type === 'success' ? 'text-emerald-800 dark:text-emerald-300' :
                  toast.type === 'error' ? 'text-rose-800 dark:text-rose-300' :
                  toast.type === 'warning' ? 'text-amber-800 dark:text-amber-300' :
                  'text-blue-800 dark:text-blue-300'
                ]">
                  {{ toast.message }}
                </p>
              </div>
              
              <!-- Botón cerrar -->
              <div class="flex-shrink-0">
                <button
                  @click="removeToast(toast.id)"
                  class="inline-flex text-gray-400 dark:text-zinc-500 hover:text-gray-600 dark:hover:text-zinc-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 rounded-full p-1"
                >
                  <span class="sr-only">Cerrar</span>
                  <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                  </svg>
                </button>
              </div>
            </div>
          </div>
          
          <!-- Barra de progreso -->
          <div 
            v-if="toast.type !== 'error'"
            :class="[
              'h-1 bg-gradient-to-r',
              toast.type === 'success' ? 'from-emerald-400 to-emerald-600' :
              toast.type === 'warning' ? 'from-amber-400 to-amber-600' :
              'from-blue-400 to-blue-600'
            ]"
            :style="{ 
              animation: `toast-progress ${toast.type === 'warning' ? '4s' : '3s'} linear forwards` 
            }"
          ></div>
        </div>
      </TransitionGroup>
    </div>
  </Teleport>
</template>

<script setup>
import { useToast } from '../composables/useToast.js'

const { toasts, removeToast } = useToast()
</script>

<style scoped>
/* Animaciones para los toasts */
.toast-enter-active {
  transition: all 0.4s ease-out;
}

.toast-leave-active {
  transition: all 0.3s ease-in;
}

.toast-enter-from {
  transform: translateX(100%) scale(0.95);
  opacity: 0;
}

.toast-leave-to {
  transform: translateX(100%) scale(0.95);
  opacity: 0;
}

.toast-move {
  transition: transform 0.4s ease;
}

/* Animación de la barra de progreso */
@keyframes toast-progress {
  from {
    width: 100%;
  }
  to {
    width: 0%;
  }
}

/* Responsividad */
@media (max-width: 640px) {
  .fixed.top-4.right-4 {
    top: 1rem;
    right: 1rem;
    left: 1rem;
    max-width: none;
  }
}

/* Asegurar que el contenido no se corte */
.toast-container {
  min-height: 60px;
  width: 100%;
}
</style>