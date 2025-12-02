<template>
  <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-2xl shadow-2xl max-w-4xl w-full max-h-[90vh] overflow-hidden">
      
      <!-- Header -->
      <div class="bg-white border-b border-gray-200 px-6 py-4 flex items-center justify-between">
        <div class="flex items-center space-x-3">
          <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
            <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
            </svg>
          </div>
          <div>
            <h3 class="text-base font-bold text-gray-900">Nuevo Traslado</h3>
            <p class="text-xs text-gray-500">Transfiere productos entre sedes</p>
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
        <form @submit.prevent="handleSubmit" class="space-y-6">
          
          <!-- Sedes Origen y Destino -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-2">
                Sede Origen <span class="text-red-500">*</span>
              </label>
              <select 
                v-model="form.source_warehouse_id"
                required
                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent text-sm">
                <option value="">Seleccionar sede origen</option>
                <option v-for="w in availableSourceWarehouses" :key="w.id" :value="w.id">
                  {{ w.name }}
                </option>
              </select>
            </div>

            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-2">
                Sede Destino <span class="text-red-500">*</span>
              </label>
              <select 
                v-model="form.destination_warehouse_id"
                required
                :disabled="!form.source_warehouse_id"
                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent text-sm disabled:bg-gray-100">
                <option value="">Seleccionar sede destino</option>
                <option 
                  v-for="w in availableDestinationWarehouses" 
                  :key="w.id" 
                  :value="w.id">
                  {{ w.name }}
                </option>
              </select>
            </div>
          </div>

          <!-- Notas -->
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">
              Notas (opcional)
            </label>
            <textarea 
              v-model="form.notes"
              rows="2"
              placeholder="Motivo del traslado, observaciones, etc."
              class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent text-sm"
            ></textarea>
          </div>

          <!-- Agregar Productos -->
          <div class="border border-gray-200 rounded-lg p-4">
            <div class="flex items-center justify-between mb-4">
              <h4 class="text-sm font-bold text-gray-900">Productos a Trasladar</h4>
              <button 
                type="button"
                @click="addProduct"
                class="px-3 py-1.5 bg-purple-600 hover:bg-purple-700 text-white text-xs font-semibold rounded-lg flex items-center space-x-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                <span>Agregar</span>
              </button>
            </div>

            <div v-if="form.items.length === 0" class="text-center py-6 text-sm text-gray-500">
              No hay productos agregados
            </div>

            <div v-else class="space-y-3">
              <div 
                v-for="(item, index) in form.items" 
                :key="index"
                class="flex items-center space-x-3 bg-gray-50 p-3 rounded-lg">
                <div class="flex-1">
                  <select 
                    v-model="item.product_id"
                    required
                    @change="updateProductStock(index)"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                    <option value="">Seleccionar producto</option>
                    <option 
                      v-for="p in availableProducts" 
                      :key="p.id" 
                      :value="p.id">
                      {{ p.name }} (Stock: {{ getProductStock(p.id) }})
                    </option>
                  </select>
                </div>
                <div class="w-32">
                  <input 
                    v-model.number="item.quantity"
                    type="number"
                    min="1"
                    :max="getProductStock(item.product_id)"
                    required
                    placeholder="Cantidad"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                </div>
                <button 
                  type="button"
                  @click="removeProduct(index)"
                  class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                  </svg>
                </button>
              </div>
            </div>
          </div>

          <!-- Alerta Informativa -->
          <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 flex space-x-3">
            <svg class="w-5 h-5 text-yellow-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
            </svg>
            <div class="text-sm text-yellow-800">
              <p class="font-semibold mb-1">Importante</p>
              <p class="text-xs">El traslado se creará como "Pendiente". Deberás completarlo manualmente para que el stock se mueva físicamente entre las sedes.</p>
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
          :disabled="saving || !canSubmit"
          class="px-5 py-2.5 bg-purple-600 hover:bg-purple-700 text-white text-sm font-bold rounded-lg shadow-sm transition-colors disabled:opacity-50 disabled:cursor-not-allowed flex items-center space-x-2">
          <svg v-if="saving" class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
          </svg>
          <span>{{ saving ? 'Creando...' : 'Crear Traslado' }}</span>
        </button>
      </div>

    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue';
import { stockTransferService } from '@/services/stockTransferService';
import { warehouseService } from '@/services/warehouseService';
import api from '@/services/api';

const props = defineProps({
  warehouses: {
    type: Array,
    required: true
  }
});

const emit = defineEmits(['close', 'saved']);

const saving = ref(false);
const availableProducts = ref([]);
const warehouseInventory = ref({});

const form = ref({
  source_warehouse_id: '',
  destination_warehouse_id: '',
  notes: '',
  items: []
});

const availableSourceWarehouses = computed(() => {
  return props.warehouses.filter(w => w.active);
});

const availableDestinationWarehouses = computed(() => {
  return props.warehouses.filter(w => 
    w.active && w.id !== form.value.source_warehouse_id
  );
});

const canSubmit = computed(() => {
  return form.value.source_warehouse_id &&
         form.value.destination_warehouse_id &&
         form.value.items.length > 0 &&
         form.value.items.every(item => item.product_id && item.quantity > 0);
});

// Cargar inventario de la bodega origen
watch(() => form.value.source_warehouse_id, async (warehouseId) => {
  if (warehouseId) {
    try {
      const data = await warehouseService.getInventory(warehouseId);
      availableProducts.value = data.products || [];
      
      // Mapear stock por producto
      warehouseInventory.value = {};
      availableProducts.value.forEach(p => {
        warehouseInventory.value[p.id] = p.stock;
      });
      
      // Limpiar items si cambia la bodega
      form.value.items = [];
    } catch (error) {
      console.error('Error al cargar inventario:', error);
    }
  }
});

const getProductStock = (productId) => {
  return warehouseInventory.value[productId] || 0;
};

const addProduct = () => {
  form.value.items.push({
    product_id: '',
    quantity: 1
  });
};

const removeProduct = (index) => {
  form.value.items.splice(index, 1);
};

const updateProductStock = (index) => {
  const item = form.value.items[index];
  const maxStock = getProductStock(item.product_id);
  if (item.quantity > maxStock) {
    item.quantity = maxStock;
  }
};

const handleSubmit = async () => {
  if (!canSubmit.value) {
    alert('Por favor completa todos los campos requeridos');
    return;
  }

  // Validar stock disponible
  for (const item of form.value.items) {
    const availableStock = getProductStock(item.product_id);
    if (item.quantity > availableStock) {
      const product = availableProducts.value.find(p => p.id === item.product_id);
      alert(`Stock insuficiente para ${product?.name}. Disponible: ${availableStock}, Solicitado: ${item.quantity}`);
      return;
    }
  }

  saving.value = true;
  try {
    await stockTransferService.create(form.value);
    alert('Traslado creado exitosamente');
    emit('saved');
  } catch (error) {
    console.error('Error al crear traslado:', error);
    alert(error.response?.data?.message || 'Error al crear el traslado');
  } finally {
    saving.value = false;
  }
};
</script>
