<template>
  <div class="fixed inset-0 bg-black/70 flex items-center justify-center z-50 p-4">
    <div class="bg-gray-900 rounded-2xl p-8 max-w-lg w-full border border-gray-700 shadow-2xl">
      <div class="flex items-center justify-between mb-6">
        <h3 class="text-2xl font-bold text-white">Crear Nueva Tienda</h3>
        <button @click="$emit('close')" class="text-gray-400 hover:text-white">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
          </svg>
        </button>
      </div>
      <div class="space-y-4">
        <div>
          <label class="block text-sm font-semibold text-gray-300 mb-2">ID de Tenant (slug)</label>
          <input v-model="newTenant.tenant_id" type="text" placeholder="mi-tienda" class="w-full px-4 py-3 bg-gray-800 text-white rounded-lg border border-gray-700 focus:border-blue-500 focus:outline-none" required>
          <p class="text-xs text-gray-500 mt-1">Solo letras minúsculas, números y guiones</p>
        </div>
        <div>
          <label class="block text-sm font-semibold text-gray-300 mb-2">Nombre del Negocio</label>
          <input v-model="newTenant.business_name" type="text" placeholder="Mi Tienda S.A." class="w-full px-4 py-3 bg-gray-800 text-white rounded-lg border border-gray-700 focus:border-blue-500 focus:outline-none" required>
        </div>
        <div>
          <label class="block text-sm font-semibold text-gray-300 mb-2">Dominio</label>
          <input v-model="newTenant.domain" type="text" placeholder="mi-tienda.localhost" class="w-full px-4 py-3 bg-gray-800 text-white rounded-lg border border-gray-700 focus:border-blue-500 focus:outline-none" required>
        </div>
        <div>
          <label class="block text-sm font-semibold text-gray-300 mb-2">Plan</label>
          <select v-model="newTenant.plan" class="w-full px-4 py-3 bg-gray-800 text-white rounded-lg border border-gray-700 focus:border-blue-500 focus:outline-none">
            <option value="free_trial">Free Trial</option>
            <option value="basic">Basic</option>
            <option value="premium">Premium</option>
            <option value="enterprise">Enterprise</option>
          </select>
        </div>
        <div class="flex space-x-3 pt-4">
          <button @click="$emit('close')" class="flex-1 px-6 py-3 bg-gray-700 hover:bg-gray-600 text-white rounded-lg font-semibold transition-colors">
            Cancelar
          </button>
          <button @click="$emit('create')" class="flex-1 px-6 py-3 bg-gradient-to-r from-emerald-600 to-emerald-700 hover:from-emerald-700 hover:to-emerald-800 text-white rounded-lg font-bold shadow-lg transition-all">
            Crear Tienda
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
defineProps({
  newTenant: {
    type: Object,
    required: true
  }
})

defineEmits(['close', 'create'])
</script>
