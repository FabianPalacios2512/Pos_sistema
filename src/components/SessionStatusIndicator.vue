<template>
  <div class="flex items-center space-x-2 text-sm">
    <!-- Indicador de estado de sesión -->
    <div class="flex items-center space-x-2">
      <!-- Punto de estado -->
      <div class="flex items-center space-x-1">
        <div 
          :class="[
            'w-2 h-2 rounded-full transition-colors duration-300',
            sessionActive ? 'bg-green-500' : 'bg-red-500'
          ]"
        ></div>
        <span class="text-xs font-medium text-gray-600">
          {{ sessionActive ? 'Activa' : 'Inactiva' }}
        </span>
      </div>
      
      <!-- Tiempo restante (solo si queda poco tiempo) -->
      <div 
        v-if="showTimeRemaining" 
        class="px-2 py-1 bg-amber-100 text-amber-700 text-xs font-semibold rounded-full border border-amber-200"
      >
        ⏱️ {{ formatTime(timeRemaining) }}
      </div>
    </div>
    
    <!-- Botón para extender sesión manualmente -->
    <button
      v-if="sessionActive"
      @click="$emit('extendSession')"
      class="p-1.5 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-lg transition-colors"
      title="Extender sesión"
    >
      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
              d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
      </svg>
    </button>
  </div>
</template>

<script setup>
import { computed } from 'vue'

// Props
const props = defineProps({
  sessionActive: {
    type: Boolean,
    default: true
  },
  timeRemaining: {
    type: Number,
    default: 0
  },
  showTimeRemaining: {
    type: Boolean,
    default: false
  }
})

// Events
const emit = defineEmits(['extendSession'])

// Computed
const showTimeRemaining = computed(() => {
  return props.sessionActive && props.timeRemaining > 0 && props.timeRemaining <= 300 // Mostrar si quedan 5 minutos o menos
})

// Methods
function formatTime(seconds) {
  const minutes = Math.floor(seconds / 60)
  const secs = seconds % 60
  return `${minutes}:${secs.toString().padStart(2, '0')}`
}
</script>