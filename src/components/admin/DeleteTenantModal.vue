<template>
  <div class="fixed inset-0 bg-black/70 flex items-center justify-center z-50 p-4">
    <div class="bg-gray-900 rounded-2xl p-8 max-w-md w-full border border-red-500/30 shadow-2xl">
      <div class="text-center mb-6">
        <div class="w-16 h-16 bg-red-600/20 rounded-full flex items-center justify-center mx-auto mb-4">
          <svg class="w-8 h-8 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
          </svg>
        </div>
        <h3 class="text-2xl font-bold text-white mb-2">¿Eliminar Tienda?</h3>
        <p class="text-gray-400 mb-1">Esta acción eliminará permanentemente:</p>
        <p class="text-white font-bold">{{ tenant.business_name }}</p>
        <p class="text-sm text-gray-500 mt-2">ID: {{ tenant.id }}</p>
        <p class="text-sm text-red-400 mt-4 font-semibold">⚠️ Esta acción NO se puede deshacer</p>
      </div>
      <div class="flex space-x-3">
        <button @click="$emit('close')" class="flex-1 px-6 py-3 bg-gray-700 hover:bg-gray-600 text-white rounded-lg font-semibold transition-colors">
          Cancelar
        </button>
        <button @click="$emit('confirm')" class="flex-1 px-6 py-3 bg-red-600 hover:bg-red-700 text-white rounded-lg font-bold shadow-lg transition-all">
          Eliminar
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
defineProps({
  tenant: {
    type: Object,
    required: true
  }
})

defineEmits(['close', 'confirm'])
</script>
