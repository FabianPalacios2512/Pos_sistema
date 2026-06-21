<template>
  <div class="fixed inset-0 bg-black/70 flex items-center justify-center z-[100] p-4">
    <div class="bg-white dark:bg-zinc-900 rounded-2xl p-8 max-w-md w-full border border-gray-200 dark:border-zinc-800 shadow-2xl">
      <div class="text-center mb-6">
        <div 
          class="w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4"
          :class="type === 'danger' ? 'bg-red-100 dark:bg-red-900/30' : 'bg-amber-100 dark:bg-amber-900/30'"
        >
          <svg v-if="type === 'danger'" class="w-8 h-8 text-red-600 dark:text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
          </svg>
          <svg v-else class="w-8 h-8 text-amber-600 dark:text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
          </svg>
        </div>
        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">{{ title }}</h3>
        <p class="text-sm text-gray-600 dark:text-zinc-400 mb-1 whitespace-pre-line">{{ message }}</p>
      </div>
      <div class="flex space-x-3">
        <button @click="$emit('cancel')" class="flex-1 px-4 py-2.5 bg-gray-100 hover:bg-gray-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 text-gray-900 dark:text-white rounded-xl font-semibold transition-colors">
          {{ cancelText || 'Cancelar' }}
        </button>
        <button @click="$emit('confirm')" 
          class="flex-1 px-4 py-2.5 text-white rounded-xl font-bold shadow-lg transition-all"
          :class="type === 'danger' ? 'bg-red-600 hover:bg-red-700' : 'bg-amber-600 hover:bg-amber-700'"
        >
          {{ confirmText || 'Aceptar' }}
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
defineProps({
  title: String,
  message: String,
  confirmText: String,
  cancelText: String,
  type: {
    type: String,
    default: 'danger'
  }
})

defineEmits(['confirm', 'cancel'])
</script>
