<template>
  <Teleport to="body">
    <div 
      class="fixed top-0 left-0 right-0 bottom-0 bg-black/50 dark:bg-black/70 flex items-center justify-center p-4"
      style="z-index: 99999; position: fixed; inset: 0;">
      <div class="bg-white dark:bg-zinc-900 rounded-2xl shadow-2xl dark:shadow-black/50 max-w-md w-full overflow-hidden border border-gray-300 dark:border-zinc-800 animate-scale-in">
        
        <!-- Header con icono dinámico -->
        <div class="bg-white dark:bg-zinc-900 border-b border-gray-200 dark:border-zinc-800 px-6 py-4">
          <div class="flex items-center space-x-3">
            <div 
              :class="[
                'w-12 h-12 rounded-xl flex items-center justify-center',
                variant === 'danger' ? 'bg-red-50 dark:bg-red-950' : 'bg-blue-50 dark:bg-blue-950'
              ]">
              <svg 
                v-if="variant === 'danger'"
                class="w-6 h-6 text-red-600 dark:text-red-400"
                fill="none" 
                stroke="currentColor" 
                viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
              </svg>
              <svg 
                v-else
                class="w-6 h-6 text-blue-600 dark:text-blue-400"
                fill="none" 
                stroke="currentColor" 
                viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
              </svg>
            </div>
            <div class="flex-1">
              <h3 class="text-lg font-bold text-gray-900 dark:text-white">{{ title }}</h3>
              <p v-if="subtitle" class="text-xs text-gray-500 dark:text-zinc-400 mt-0.5">{{ subtitle }}</p>
            </div>
            <button 
              @click="$emit('cancel')"
              class="p-2 hover:bg-gray-100 dark:hover:bg-zinc-800 rounded-lg transition-colors">
              <svg class="w-5 h-5 text-gray-500 dark:text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
              </svg>
            </button>
          </div>
        </div>

        <!-- Body -->
        <div class="px-6 py-5">
          <p class="text-sm text-gray-700 dark:text-zinc-300 leading-relaxed">
            {{ message }}
          </p>
          <p v-if="description" class="text-xs text-gray-500 dark:text-zinc-400 mt-3 bg-gray-50 dark:bg-zinc-800/50 rounded-lg p-3 border border-gray-200 dark:border-zinc-700/50">
            {{ description }}
          </p>
        </div>

        <!-- Footer -->
        <div class="bg-gray-50 dark:bg-zinc-900 border-t border-gray-200 dark:border-zinc-800 px-6 py-4 flex justify-end space-x-3">
          <button 
            @click="$emit('cancel')"
            type="button"
            class="px-5 py-2.5 bg-white dark:bg-zinc-800 border border-gray-300 dark:border-zinc-700 text-gray-700 dark:text-zinc-200 text-sm font-semibold rounded-lg hover:bg-gray-50 dark:hover:bg-zinc-700 transition-colors">
            {{ cancelText }}
          </button>
          <button 
            @click="$emit('confirm')"
            :disabled="loading"
            :class="[
              'px-5 py-2.5 text-white text-sm font-bold rounded-lg shadow-lg transition-all duration-300 disabled:opacity-50 disabled:cursor-not-allowed flex items-center space-x-2',
              variant === 'danger' 
                ? 'bg-red-600 dark:bg-red-700 hover:bg-red-700 dark:hover:bg-red-600 shadow-red-400/40 dark:shadow-red-900/50' 
                : 'bg-emerald-600 dark:bg-emerald-700 hover:bg-emerald-700 dark:hover:bg-emerald-600 shadow-emerald-400/40 dark:shadow-emerald-900/50'
            ]">
            <svg v-if="loading" class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span>{{ loading ? loadingText : confirmText }}</span>
          </button>
        </div>

      </div>
    </div>
  </Teleport>
</template>

<script setup>
import { defineProps, defineEmits } from 'vue';

defineProps({
  title: {
    type: String,
    required: true
  },
  subtitle: {
    type: String,
    default: ''
  },
  message: {
    type: String,
    required: true
  },
  description: {
    type: String,
    default: ''
  },
  confirmText: {
    type: String,
    default: 'Confirmar'
  },
  cancelText: {
    type: String,
    default: 'Cancelar'
  },
  loadingText: {
    type: String,
    default: 'Procesando...'
  },
  variant: {
    type: String,
    default: 'warning', // 'warning' o 'danger'
    validator: (value) => ['warning', 'danger'].includes(value)
  },
  loading: {
    type: Boolean,
    default: false
  }
});

defineEmits(['confirm', 'cancel']);
</script>

<style scoped>
@keyframes scaleIn {
  from {
    opacity: 0;
    transform: scale(0.95);
  }
  to {
    opacity: 1;
    transform: scale(1);
  }
}

.animate-scale-in {
  animation: scaleIn 0.2s ease-out;
}
</style>
