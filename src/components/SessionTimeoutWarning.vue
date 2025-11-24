<template>
  <div v-if="show" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-2xl p-6 max-w-md w-full mx-4 shadow-2xl animate-bounce">
      
      <!-- Header con icono de advertencia -->
      <div class="text-center mb-4">
        <div class="w-16 h-16 bg-gradient-to-br from-amber-400 to-orange-500 rounded-full flex items-center justify-center mx-auto mb-3 shadow-lg">
          <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                  d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.464 0L4.35 16.5c-.77.833.192 2.5 1.732 2.5z"/>
          </svg>
        </div>
        <h3 class="text-xl font-bold text-gray-900 mb-1">
          ⚠️ Sesión por Expirar
        </h3>
        <p class="text-sm text-gray-600">
          Tu sesión se cerrará por inactividad en:
        </p>
      </div>
      
      <!-- Countdown timer -->
      <div class="text-center mb-6">
        <div class="text-4xl font-bold text-red-600 mb-2">
          {{ formatTime(countdown) }}
        </div>
        <div class="w-full bg-gray-200 rounded-full h-2">
          <div 
            class="bg-gradient-to-r from-red-400 to-red-600 h-2 rounded-full transition-all duration-1000"
            :style="{ width: `${(countdown / totalWarningTime) * 100}%` }"
          ></div>
        </div>
      </div>
      
      <!-- Mensaje informativo -->
      <div class="bg-amber-50 border border-amber-200 rounded-lg p-3 mb-4">
        <p class="text-sm text-amber-800">
          <strong>💡 Sistema POS:</strong> Por seguridad, cerramos sesiones inactivas después de {{ inactivityHours }} horas.
        </p>
      </div>
      
      <!-- Botones -->
      <div class="flex space-x-3">
        <!-- Botón Extender Sesión -->
        <button 
          @click="$emit('extend')"
          class="flex-1 px-4 py-3 bg-gradient-to-r from-green-400 to-emerald-500 hover:from-green-500 hover:to-emerald-600 text-white text-sm font-bold rounded-xl shadow-lg transition-all duration-200 transform hover:scale-105 flex items-center justify-center space-x-2"
        >
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                  d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
          </svg>
          <span>Extender Sesión</span>
        </button>
        
        <!-- Botón Cerrar Sesión -->
        <button 
          @click="$emit('logout')"
          class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-xl border border-gray-300 transition-colors flex items-center justify-center space-x-2"
        >
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                  d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
          </svg>
          <span>Cerrar</span>
        </button>
      </div>
      
      <!-- Información adicional -->
      <div class="mt-4 text-center">
        <p class="text-xs text-gray-500">
          También puedes mover el mouse o hacer clic para extender automáticamente
        </p>
      </div>
      
    </div>
  </div>
</template>

<script setup>
// Props
const props = defineProps({
  show: {
    type: Boolean,
    default: false
  },
  countdown: {
    type: Number,
    default: 0
  },
  totalWarningTime: {
    type: Number,
    default: 300 // 5 minutos en segundos
  },
  inactivityHours: {
    type: Number,
    default: 5
  }
})

// Events
const emit = defineEmits(['extend', 'logout'])

// Methods
function formatTime(seconds) {
  const minutes = Math.floor(seconds / 60)
  const secs = seconds % 60
  return `${minutes.toString().padStart(2, '0')}:${secs.toString().padStart(2, '0')}`
}
</script>

<style scoped>
@keyframes bounce {
  0%, 20%, 53%, 80%, 100% {
    transform: translate3d(0,0,0);
  }
  40%, 43% {
    transform: translate3d(0,-5px,0);
  }
  70% {
    transform: translate3d(0,-2px,0);
  }
  90% {
    transform: translate3d(0,-1px,0);
  }
}

.animate-bounce {
  animation: bounce 1s ease-in-out;
}
</style>