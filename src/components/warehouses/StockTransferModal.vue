<template>
  <Teleport to="body">
    <div 
      class="fixed top-0 left-0 right-0 bottom-0 bg-black/50 dark:bg-black/70 flex items-center justify-center p-4"
      style="z-index: 99999; position: fixed; inset: 0;">
      <div class="bg-white dark:bg-[#1e1f20] rounded-2xl max-w-4xl w-full max-h-[90vh] overflow-hidden border border-[#e8eaed] dark:border-[#3a3a3f]">
        
        <!-- Header - Gemini -->
        <div class="bg-white dark:bg-[#1e1f20] border-b border-[#e8eaed] dark:border-[#3a3a3f] px-6 py-4 flex items-center justify-between">
          <div class="flex items-center space-x-3">
            <div class="w-10 h-10 bg-[#e8f0fe] dark:bg-[#1a73e8]/15 rounded-2xl flex items-center justify-center">
              <svg class="w-5 h-5 text-[#1a73e8] dark:text-[#8ab4f8]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
              </svg>
            </div>
            <div>
              <h3 class="text-base font-medium text-[#1e1f20] dark:text-[#e3e3e3]">Nuevo Traslado</h3>
              <p class="text-xs text-[#5f6368] dark:text-[#9aa0a6]">Transfiere productos entre sedes</p>
            </div>
          </div>
          <button 
            @click="$emit('close')"
            class="p-2 hover:bg-[#f1f3f4] dark:hover:bg-[#282a2c] rounded-full transition-colors">
            <svg class="w-5 h-5 text-[#5f6368] dark:text-[#9aa0a6]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
          </button>
        </div>

        <!-- Body - Gemini -->
        <div class="p-6 overflow-y-auto max-h-[calc(90vh-140px)]">
          <form @submit.prevent="handleSubmit" class="space-y-6">
          
          <!-- Sedes Origen y Destino -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-[#1e1f20] dark:text-[#e3e3e3] mb-2">
                Sede Origen <span class="text-[#d93025]">*</span>
              </label>
              <select 
                v-model="form.source_warehouse_id"
                required
                class="w-full px-4 py-2.5 border border-[#dadce0] dark:border-[#3a3a3f] bg-white dark:bg-[#282a2c] text-[#1e1f20] dark:text-[#e3e3e3] rounded-xl focus:ring-2 focus:ring-[#1a73e8] dark:focus:ring-[#8ab4f8] focus:border-transparent text-sm transition-all duration-200">
                <option value="">Seleccionar sede origen</option>
                <option v-for="w in availableSourceWarehouses" :key="w.id" :value="w.id">
                  {{ w.name }}
                </option>
              </select>
            </div>

            <div>
              <label class="block text-sm font-medium text-[#1e1f20] dark:text-[#e3e3e3] mb-2">
                Sede Destino <span class="text-[#d93025]">*</span>
              </label>
              <select 
                v-model="form.destination_warehouse_id"
                required
                :disabled="!form.source_warehouse_id"
                class="w-full px-4 py-2.5 border border-[#dadce0] dark:border-[#3a3a3f] bg-white dark:bg-[#282a2c] text-[#1e1f20] dark:text-[#e3e3e3] rounded-xl focus:ring-2 focus:ring-[#1a73e8] dark:focus:ring-[#8ab4f8] focus:border-transparent text-sm transition-all duration-200 disabled:bg-[#f1f3f4] dark:disabled:bg-[#131314] disabled:opacity-60">
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
            <label class="block text-sm font-medium text-[#1e1f20] dark:text-[#e3e3e3] mb-2">
              Notas (opcional)
            </label>
            <textarea 
              v-model="form.notes"
              rows="2"
              placeholder="Motivo del traslado, observaciones, etc."
              class="w-full px-4 py-2.5 border border-[#dadce0] dark:border-[#3a3a3f] bg-white dark:bg-[#282a2c] text-[#1e1f20] dark:text-[#e3e3e3] placeholder-[#5f6368] dark:placeholder-[#9aa0a6] rounded-xl focus:ring-2 focus:ring-[#1a73e8] dark:focus:ring-[#8ab4f8] focus:border-transparent text-sm transition-all duration-200"
            ></textarea>
          </div>

          <!-- Agregar Productos - Gemini -->
          <div class="border border-[#e8eaed] dark:border-[#3a3a3f] rounded-2xl p-4">
            <div class="flex items-center justify-between mb-4">
              <h4 class="text-sm font-medium text-[#1e1f20] dark:text-[#e3e3e3]">Productos a Trasladar</h4>
              <button 
                type="button"
                @click="addProduct"
                class="px-3 py-1.5 bg-[#1a73e8] dark:bg-[#8ab4f8] hover:bg-[#1557b0] dark:hover:bg-[#aecbfa] text-white dark:text-[#1e1f20] text-xs font-medium rounded-full flex items-center space-x-1 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                <span>Agregar</span>
              </button>
            </div>

            <div v-if="form.items.length === 0" class="text-center py-6 text-sm text-[#5f6368] dark:text-[#9aa0a6]">
              No hay productos agregados
            </div>

            <div v-else class="space-y-3">
              <div 
                v-for="(item, index) in form.items" 
                :key="index"
                class="flex items-center space-x-3 bg-[#f8f9fa] dark:bg-[#282a2c] p-3 rounded-xl border border-[#e8eaed] dark:border-[#3a3a3f]">
                <div class="flex-1">
                  <select 
                    v-model="item.product_id"
                    required
                    @change="updateProductStock(index)"
                    class="w-full px-3 py-2 border border-[#dadce0] dark:border-[#3a3a3f] bg-white dark:bg-[#1e1f20] text-[#1e1f20] dark:text-[#e3e3e3] rounded-xl text-sm focus:ring-2 focus:ring-[#1a73e8] dark:focus:ring-[#8ab4f8]">
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
                    class="w-full px-3 py-2 border border-[#dadce0] dark:border-[#3a3a3f] bg-white dark:bg-[#1e1f20] text-[#1e1f20] dark:text-[#e3e3e3] placeholder-[#5f6368] dark:placeholder-[#9aa0a6] rounded-xl text-sm focus:ring-2 focus:ring-[#1a73e8] dark:focus:ring-[#8ab4f8]">
                </div>
                <button 
                  type="button"
                  @click="removeProduct(index)"
                  class="p-2 text-[#d93025] dark:text-[#f28b82] hover:bg-[#fce8e6] dark:hover:bg-[#d93025]/15 rounded-full transition-colors">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                  </svg>
                </button>
              </div>
            </div>
          </div>

          <!-- Alerta Informativa - Gemini -->
          <div class="bg-[#fef7e0] dark:bg-[#f9ab00]/15 border border-[#fdd663]/50 dark:border-[#f9ab00]/30 rounded-xl p-4 flex space-x-3">
            <svg class="w-5 h-5 text-[#f9ab00] dark:text-[#fdd663] flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
            </svg>
            <div class="text-sm text-[#e37400] dark:text-[#fdd663]">
              <p class="font-medium mb-1">Importante</p>
              <p class="text-xs text-[#b06000] dark:text-[#feefc3]">El traslado se creará como "Pendiente". Deberás completarlo manualmente para que el stock se mueva físicamente entre las sedes.</p>
            </div>
          </div>

        </form>
      </div>

      <!-- Footer - Gemini -->
      <div class="bg-[#f8f9fa] dark:bg-[#282a2c] border-t border-[#e8eaed] dark:border-[#3a3a3f] px-6 py-4 flex justify-end space-x-3">
        <button 
          @click="$emit('close')"
          type="button"
          class="px-5 py-2.5 bg-white dark:bg-[#1e1f20] border border-[#dadce0] dark:border-[#3a3a3f] text-[#1e1f20] dark:text-[#e3e3e3] text-sm font-medium rounded-full hover:bg-[#f1f3f4] dark:hover:bg-[#3a3a3f] transition-colors">
          Cancelar
        </button>
        <button 
          @click="handleSubmit"
          :disabled="saving || !canSubmit"
          class="px-5 py-2.5 bg-[#1a73e8] dark:bg-[#8ab4f8] hover:bg-[#1557b0] dark:hover:bg-[#aecbfa] text-white dark:text-[#1e1f20] text-sm font-medium rounded-full transition-colors disabled:opacity-50 disabled:cursor-not-allowed flex items-center space-x-2">
          <svg v-if="saving" class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
          </svg>
          <span>{{ saving ? 'Creando...' : 'Crear Traslado' }}</span>
        </button>
      </div>

    </div>
    </div>
  </Teleport>
</template>

<script setup>
import { ref, computed, watch } from 'vue';
import { stockTransferService } from '@/services/stockTransferService';
import { warehouseService } from '@/services/warehouseService';
import api from '@/services/api';
import { useToast } from '@/composables/useToast';

const props = defineProps({
  warehouses: {
    type: Array,
    required: true
  }
});

const emit = defineEmits(['close', 'saved']);

const { showSuccess, showError, showWarning } = useToast();
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
      const response = await warehouseService.getInventory(warehouseId);
      // El backend devuelve {success: true, data: {warehouse, summary, products}}
      const products = response.data?.products || response.products || [];
      availableProducts.value = products.filter(p => p.stock > 0); // Solo productos con stock
      // Mapear stock por producto
      warehouseInventory.value = {};
      availableProducts.value.forEach(p => {
        warehouseInventory.value[p.id] = p.stock;
      });
      // Limpiar items si cambia la bodega
      form.value.items = [];
    } catch (error) {
      console.error('Error al cargar inventario:', error);
      showError('Error al cargar productos de la sede. Por favor intenta de nuevo.');
    }
  } else {
    // Si no hay sede seleccionada, limpiar todo
    availableProducts.value = [];
    warehouseInventory.value = {};
    form.value.items = [];
  }
});

const getProductStock = (productId) => {
  const stock = warehouseInventory.value[productId] || 0;
  return stock;
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
    showWarning('Por favor completa todos los campos requeridos');
    return;
  }

  // Validar stock disponible
  for (const item of form.value.items) {
    const availableStock = getProductStock(item.product_id);
    
    if (item.quantity > availableStock) {
      const product = availableProducts.value.find(p => p.id === item.product_id);
      showError(`Stock insuficiente para ${product?.name}. Disponible: ${availableStock}`);
      return;
    }
  }

  saving.value = true;
  try {
    const response = await stockTransferService.create(form.value);
    showSuccess('Traslado creado exitosamente');
    emit('saved');
  } catch (error) {
    console.error('Error al crear traslado:', error);
    showError(error.response?.data?.message || 'Error al crear el traslado');
  } finally {
    saving.value = false;
  }
};
</script>
