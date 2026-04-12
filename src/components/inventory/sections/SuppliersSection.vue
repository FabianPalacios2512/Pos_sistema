<template>
  <div class="suppliers-section">
    <div class="bg-white dark:bg-[#1e1f20] rounded-2xl p-6">
      <div class="flex items-center justify-between mb-6">
        <h2 class="text-lg font-semibold text-[#1e1f20] dark:text-[#e3e3e3]">Análisis de Proveedores</h2>
        <button @click="$emit('refresh')" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-[#1a73e8] dark:text-[#8ab4f8] bg-[#e8f0fe] dark:bg-[#1a73e8]/20 rounded-full hover:bg-[#d2e3fc] dark:hover:bg-[#1a73e8]/30 transition-colors">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
          </svg>
          Actualizar
        </button>
      </div>
      
      <div v-if="loading" class="flex items-center justify-center py-12">
        <svg class="animate-spin w-8 h-8 text-[#1a73e8] dark:text-[#8ab4f8]" fill="none" viewBox="0 0 24 24">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
      </div>
      
      <div v-else class="text-center py-12">
        <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-[#e8f0fe] dark:bg-[#1a73e8]/20 flex items-center justify-center">
          <svg class="w-8 h-8 text-[#1a73e8] dark:text-[#8ab4f8]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 01-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 00-3.213-9.193 2.056 2.056 0 00-1.58-.86H14.25M16.5 18.75h-2.25m0-11.177v-.958c0-.568-.422-1.048-.987-1.106a48.554 48.554 0 00-10.026 0 1.106 1.106 0 00-.987 1.106v7.635m12-6.677v6.677m0 4.5v-4.5m0 0h-12"></path>
          </svg>
        </div>
        <p class="text-base font-medium text-[#1e1f20] dark:text-[#e3e3e3]">Análisis de Proveedores</p>
        <p class="text-sm text-[#5f6368] dark:text-[#9aa0a6] mt-1">Esta sección estará disponible próximamente</p>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'SuppliersSection',
  props: {
    data: Object,
    loading: Boolean
  },
  emits: ['refresh', 'supplier-select']
}
</script>