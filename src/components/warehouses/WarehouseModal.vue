<template>
  <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-2xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-hidden">
      
      <!-- Header -->
      <div class="bg-white border-b border-gray-200 px-6 py-4 flex items-center justify-between">
        <div class="flex items-center space-x-3">
          <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
            </svg>
          </div>
          <div>
            <h3 class="text-base font-bold text-gray-900">
              {{ isEditing ? 'Editar Sede' : 'Nueva Sede' }}
            </h3>
            <p class="text-xs text-gray-500">Complete la información de la sede</p>
          </div>
        </div>
        <button 
          @click="$emit('close')"
          class="p-2 hover:bg-gray-100 rounded-lg transition-colors">
          <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
          </svg>
        </button>
      </div>

      <!-- Body -->
      <div class="p-6 overflow-y-auto max-h-[calc(90vh-140px)]">
        <form @submit.prevent="handleSubmit" class="space-y-4">
          
          <!-- Nombre de la Sede -->
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">
              Nombre de la Sede <span class="text-red-500">*</span>
            </label>
            <input 
              v-model="form.name"
              type="text" 
              required
              placeholder="Ej: Sede Centro, Sucursal Norte"
              class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm"
            />
          </div>

          <!-- Dirección -->
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">
              Dirección
            </label>
            <input 
              v-model="form.address"
              type="text" 
              placeholder="Calle 123 #45-67"
              class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm"
            />
          </div>

          <!-- Teléfono -->
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">
              Teléfono
            </label>
            <input 
              v-model="form.phone"
              type="tel" 
              placeholder="3001234567"
              class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm"
            />
          </div>

          <!-- Checkboxes -->
          <div class="space-y-3 pt-2">
            <label class="flex items-center space-x-3 cursor-pointer p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
              <input 
                v-model="form.is_default"
                type="checkbox"
                class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
              />
              <div class="flex-1">
                <span class="text-sm font-semibold text-gray-900">Marcar como sede principal</span>
                <p class="text-xs text-gray-500 mt-0.5">La sede principal se usa por defecto al abrir caja</p>
              </div>
            </label>

            <label class="flex items-center space-x-3 cursor-pointer p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
              <input 
                v-model="form.active"
                type="checkbox"
                class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
              />
              <div class="flex-1">
                <span class="text-sm font-semibold text-gray-900">Sede activa</span>
                <p class="text-xs text-gray-500 mt-0.5">Las sedes inactivas no pueden operar</p>
              </div>
            </label>
          </div>

          <!-- Alerta Informativa -->
          <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 flex space-x-3">
            <svg class="w-5 h-5 text-blue-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <div class="text-sm text-blue-800">
              <p class="font-semibold mb-1">Importante</p>
              <p class="text-xs">Al crear una nueva sede, todos los productos se agregarán automáticamente con stock 0. Deberás hacer un traslado inicial desde otra sede o ajustar el inventario manualmente.</p>
            </div>
          </div>

        </form>
      </div>

      <!-- Footer -->
      <div class="bg-gray-50 border-t border-gray-200 px-6 py-4 flex justify-end space-x-3">
        <button 
          @click="$emit('close')"
          type="button"
          class="px-4 py-2.5 bg-white border border-gray-300 text-gray-700 text-sm font-semibold rounded-lg hover:bg-gray-50 transition-colors">
          Cancelar
        </button>
        <button 
          @click="handleSubmit"
          :disabled="saving || !form.name"
          class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold rounded-lg shadow-sm transition-colors disabled:opacity-50 disabled:cursor-not-allowed flex items-center space-x-2">
          <svg v-if="saving" class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
          </svg>
          <span>{{ saving ? 'Guardando...' : isEditing ? 'Actualizar' : 'Crear Sede' }}</span>
        </button>
      </div>

    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue';
import { warehouseService } from '@/services/warehouseService';

const props = defineProps({
  warehouse: {
    type: Object,
    default: null
  }
});

const emit = defineEmits(['close', 'saved']);

const saving = ref(false);
const form = ref({
  name: '',
  address: '',
  phone: '',
  is_default: false,
  active: true
});

const isEditing = computed(() => !!props.warehouse);

// Cargar datos si es edición
watch(() => props.warehouse, (newWarehouse) => {
  if (newWarehouse) {
    form.value = {
      name: newWarehouse.name || '',
      address: newWarehouse.address || '',
      phone: newWarehouse.phone || '',
      is_default: newWarehouse.is_default || false,
      active: newWarehouse.active ?? true
    };
  }
}, { immediate: true });

const handleSubmit = async () => {
  if (!form.value.name) {
    alert('El nombre de la sede es obligatorio');
    return;
  }

  saving.value = true;
  try {
    if (isEditing.value) {
      await warehouseService.update(props.warehouse.id, form.value);
      console.log('✅ Sede actualizada exitosamente');
      alert('Sede actualizada exitosamente');
    } else {
      await warehouseService.create(form.value);
      console.log('✅ Sede creada exitosamente');
      alert('Sede creada exitosamente');
    }
    console.log('📤 Emitiendo evento "saved"...');
    emit('saved');
  } catch (error) {
    console.error('❌ Error al guardar sede:', error);
    alert(error.response?.data?.message || 'Error al guardar la sede');
  } finally {
    saving.value = false;
  }
};
</script>
