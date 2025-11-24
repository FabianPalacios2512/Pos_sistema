<template>
  <div class="suppliers-section">
    <div class="bg-white rounded-lg shadow p-6">
      <div class="flex items-center justify-between mb-6">
        <h2 class="text-xl font-semibold text-gray-900">Análisis de Proveedores</h2>
        <button @click="$emit('refresh')" class="text-indigo-600 hover:text-indigo-800">
          <i class="fas fa-sync-alt mr-2"></i>
          Actualizar
        </button>
      </div>
      
      <div v-if="loading" class="flex items-center justify-center py-12">
        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-indigo-600"></div>
      </div>
      
      <div v-else class="text-center py-12 text-gray-500">
        <i class="fas fa-truck text-4xl mb-4 text-gray-300"></i>
        <p class="text-lg font-medium">Análisis de Proveedores</p>
        <p class="text-sm">Esta sección estará disponible próximamente</p>
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