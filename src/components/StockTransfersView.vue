<template>
  <!-- Toast Notifications -->
  <ToastContainer />
  
  <div :class="embedded ? 'space-y-6' : 'min-h-screen font-sans bg-gradient-to-b from-gray-50 via-gray-100 to-gray-200 dark:bg-gradient-to-b dark:from-[#141417] dark:via-slate-900 dark:to-[#0a0a0c] transition-colors duration-300 px-8'">
    <div :class="embedded ? 'space-y-6' : 'p-4 lg:p-6 space-y-6 pb-8 animate-fade-in'">
      
      <!-- Header Elegante -->
      <div v-if="!embedded" class="flex items-center justify-between pb-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Traslados de Mercancía</h1>
            <p class="text-sm text-gray-600 dark:text-zinc-400 mt-1">Gestiona movimientos de inventario entre sedes</p>
        </div>
        
        <div class="flex items-center space-x-3">
          <button 
            @click="fetchTransfers"
            class="px-5 py-2.5 bg-white dark:bg-zinc-900 hover:bg-slate-50 dark:hover:bg-zinc-800 text-slate-600 dark:text-zinc-200 text-sm font-bold rounded-xl border border-slate-200 dark:border-zinc-800 shadow-sm transition-all duration-200 flex items-center space-x-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
            </svg>
            <span>Actualizar</span>
          </button>
          
          <button 
            @click="openCreateModal"
            class="px-6 py-2.5 bg-slate-900 dark:bg-slate-700 hover:bg-black dark:hover:bg-slate-600 text-white text-sm font-bold rounded-xl shadow-lg shadow-slate-400/40 dark:shadow-slate-900/50 transition-all duration-300 flex items-center space-x-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            <span>Nuevo Traslado</span>
          </button>
        </div>
      </div>

      <!-- Filtros - Gemini -->
      <div :class="embedded ? 'bg-white dark:bg-zinc-900 rounded-2xl p-4 border border-gray-200 dark:border-zinc-700' : 'bg-white dark:bg-zinc-900 rounded-2xl shadow-xl dark:shadow-black/50 p-4 border border-gray-300 dark:border-zinc-800'">
        <div class="flex flex-wrap items-center gap-3">
          <select 
            v-model="filters.status"
            @change="fetchTransfers"
            class="px-3 py-3 text-sm rounded-lg border border-gray-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 text-gray-700 dark:text-zinc-300 font-medium focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 min-w-36">
            <option value="">Todos los estados</option>
            <option value="pending">Pendientes</option>
            <option value="in_transit">En tránsito</option>
            <option value="completed">Completados</option>
            <option value="cancelled">Cancelados</option>
          </select>

          <select 
            v-model="filters.source_warehouse_id"
            @change="fetchTransfers"
            class="px-3 py-3 text-sm rounded-lg border border-gray-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 text-gray-700 dark:text-zinc-300 font-medium focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 min-w-48">
            <option value="">Todas las sedes origen</option>
            <option v-for="w in warehouses" :key="w.id" :value="w.id">{{ w.name }}</option>
          </select>

          <button 
            v-if="filters.status || filters.source_warehouse_id"
            @click="clearFilters" 
            class="p-3 text-gray-500 dark:text-zinc-400 hover:text-rose-600 dark:hover:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-950 rounded-xl border border-transparent hover:border-rose-100 dark:hover:border-rose-900/30 transition-all duration-200"
            title="Limpiar filtros">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
            </svg>
          </button>
        </div>
      </div>

      <!-- Lista de Traslados - Gemini -->
      <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-gray-300 dark:border-zinc-800 shadow-xl dark:shadow-black/50 overflow-hidden" style="flex: 1; display: flex; flex-direction: column; min-height: 0;">
        <div class="bg-gray-50 dark:bg-zinc-900 border-b border-gray-200 dark:border-zinc-800 px-5 py-4 flex items-center justify-between">
          <div>
            <h2 class="text-base font-bold text-gray-900 dark:text-white">Historial de Traslados</h2>
            <p class="text-xs text-gray-500 dark:text-zinc-400 mt-0.5">{{ transfers.length }} traslados registrados</p>
          </div>
        </div>

        <div v-if="loading" class="py-12 text-center">
          <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 dark:border-blue-400"></div>
          <p class="mt-2 text-sm text-gray-500 dark:text-zinc-400">Cargando traslados...</p>
        </div>

        <div v-else-if="transfers.length === 0" class="py-12 text-center">
          <div class="flex flex-col items-center space-y-3">
            <div class="w-12 h-12 bg-gray-100 dark:bg-zinc-800 rounded-full flex items-center justify-center">
              <svg class="w-6 h-6 text-zinc-400 dark:text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
              </svg>
            </div>
            <div>
              <p class="text-sm font-medium text-gray-900 dark:text-white">No hay traslados</p>
              <p class="text-xs text-gray-500 dark:text-zinc-400 mt-1">Crea tu primer traslado de mercancía</p>
            </div>
          </div>
        </div>

        <div v-else class="divide-y divide-gray-100 dark:divide-zinc-800 overflow-y-auto" style="flex: 1; min-height: 0;">
          <div 
            v-for="transfer in transfers" 
            :key="transfer.id"
            class="p-4 hover:bg-gray-50 dark:hover:bg-zinc-800 transition-all duration-200">
            <div class="flex items-center justify-between">
              <div class="flex items-center space-x-4 flex-1">
                <div :class="[
                  'w-10 h-10 rounded-lg flex items-center justify-center flex-shrink-0',
                  getStatusColor(transfer.status).bg
                ]">
                  <svg class="w-5 h-5" :class="getStatusColor(transfer.status).text" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                  </svg>
                </div>

                <div class="flex-1 min-w-0">
                  <div class="flex items-center space-x-2 mb-1">
                    <h3 class="text-[15px] font-bold text-gray-900 dark:text-white">{{ transfer.reference_number }}</h3>
                    <span :class="[
                      'px-2.5 py-1 text-[10px] font-bold rounded-full border uppercase tracking-wide',
                      getStatusColor(transfer.status).badge
                    ]">
                      {{ getStatusText(transfer.status) }}
                    </span>
                  </div>
                  <div class="flex items-center space-x-4 text-xs text-gray-500 dark:text-zinc-400">
                    <span class="flex items-center space-x-1">
                      <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                      </svg>
                      <span>{{ transfer.source_warehouse?.name }} → {{ transfer.destination_warehouse?.name }}</span>
                    </span>
                    <span class="flex items-center space-x-1">
                      <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                      </svg>
                      <span>{{ transfer.items?.length || 0 }} productos</span>
                    </span>
                    <span>{{ formatDate(transfer.created_at) }}</span>
                  </div>
                  <p v-if="transfer.notes" class="text-xs text-gray-500 dark:text-zinc-400 mt-1">{{ transfer.notes }}</p>
                </div>
              </div>

              <div class="flex items-center space-x-2">
                <button 
                  @click="viewTransfer(transfer)"
                  class="p-2 text-slate-400 dark:text-zinc-400 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-lg border border-transparent hover:border-blue-100 dark:hover:border-blue-900/30 transition-colors"
                  title="Ver detalle">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                  </svg>
                </button>
                <button 
                  v-if="transfer.status === 'pending'"
                  @click="completeTransfer(transfer)"
                  class="p-2 text-slate-400 dark:text-zinc-400 hover:text-emerald-600 dark:hover:text-emerald-400 hover:bg-emerald-50 dark:hover:bg-emerald-900/20 rounded-lg border border-transparent hover:border-emerald-100 dark:hover:border-emerald-900/30 transition-colors"
                  title="Completar traslado">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                  </svg>
                </button>
                <button 
                  v-if="transfer.status === 'pending'"
                  @click="cancelTransfer(transfer)"
                  class="p-2 text-slate-400 dark:text-zinc-500 hover:text-rose-600 dark:hover:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-900/20 rounded-lg border border-transparent hover:border-rose-100 dark:hover:border-rose-900/30 transition-colors"
                  title="Cancelar">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                  </svg>
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>

    </div>

    <!-- Modal Crear Traslado -->
    <StockTransferModal
      v-if="showModal"
      :warehouses="warehouses"
      @close="closeModal"
      @saved="handleSaved"
    />

    <TransferDetailModal
      v-if="showDetailModal && selectedTransfer"
      :transfer="selectedTransfer"
      @close="closeDetailModal"
      @updated="handleSaved"
    />

    <!-- Modal de Confirmación -->
    <ConfirmModal
      v-if="confirmModal.show"
      :title="confirmModal.title"
      :subtitle="confirmModal.subtitle"
      :message="confirmModal.message"
      :description="confirmModal.description"
      :confirmText="confirmModal.confirmText"
      :cancelText="confirmModal.cancelText"
      :loadingText="confirmModal.loadingText"
      :variant="confirmModal.variant"
      :loading="confirmModal.loading"
      @confirm="confirmModal.onConfirm"
      @cancel="closeConfirmModal"
    />
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { stockTransferService } from '@/services/stockTransferService';
import { warehouseService } from '@/services/warehouseService';
import StockTransferModal from '@/components/warehouses/StockTransferModal.vue';
import TransferDetailModal from '@/components/warehouses/TransferDetailModal.vue';
import ConfirmModal from '@/components/ConfirmModal.vue';
import ToastContainer from '@/components/ToastContainer.vue';
import { useToast } from '@/composables/useToast';

// Props
const props = defineProps({
  embedded: {
    type: Boolean,
    default: false
  }
});

const { showSuccess, showError } = useToast();
const transfers = ref([]);
const warehouses = ref([]);
const loading = ref(false);
const showModal = ref(false);
const showDetailModal = ref(false);
const selectedTransfer = ref(null);
const filters = ref({
  status: '',
  source_warehouse_id: ''
});

// Estado del modal de confirmación
const confirmModal = ref({
  show: false,
  title: '',
  subtitle: '',
  message: '',
  description: '',
  confirmText: 'Confirmar',
  cancelText: 'Cancelar',
  loadingText: 'Procesando...',
  variant: 'warning',
  loading: false,
  onConfirm: () => {}
});

const fetchTransfers = async () => {
  loading.value = true;
  try {
    const response = await stockTransferService.getAll(filters.value);
    
    // Laravel devuelve paginación: { data: [...], current_page: 1, total: X }
    // Axios devuelve: { data: { data: [...], ... } }
    const data = response.data || response;
    
    if (Array.isArray(data)) {
      // Si es un array directo
      transfers.value = data;
    } else if (data && Array.isArray(data.data)) {
      // Si es un objeto paginado de Laravel
      transfers.value = data.data;
    } else {
      transfers.value = [];
    }
  } catch (error) {
    console.error('Error al cargar traslados:', error);
    transfers.value = [];
    showError('Error al cargar los traslados');
  } finally {
    loading.value = false;
  }
};

const fetchWarehouses = async () => {
  try {
    const data = await warehouseService.getAll();
    
    // El API devuelve { warehouses: [...], plan_info: {...} }
    if (data && data.warehouses && Array.isArray(data.warehouses)) {
      warehouses.value = data.warehouses;
    } else if (data && data.data && Array.isArray(data.data)) {
      warehouses.value = data.data;
    } else if (Array.isArray(data)) {
      warehouses.value = data;
    } else {
      warehouses.value = [];
    }
  } catch (error) {
    console.error('Error al cargar sedes:', error);
    warehouses.value = [];
  }
};

const openCreateModal = () => {
  showModal.value = true;
};

const closeModal = () => {
  showModal.value = false;
};

const viewTransfer = (transfer) => {
  selectedTransfer.value = transfer;
  showDetailModal.value = true;
};

const closeDetailModal = () => {
  showDetailModal.value = false;
  selectedTransfer.value = null;
};

const closeConfirmModal = () => {
  confirmModal.value.show = false;
  confirmModal.value.loading = false;
};

const completeTransfer = async (transfer) => {
  confirmModal.value = {
    show: true,
    title: 'Completar Traslado',
    subtitle: transfer.reference_number,
    message: `¿Estás seguro de que deseas completar el traslado ${transfer.reference_number}?`,
    description: 'Esta acción actualizará el inventario de ambas sedes y no se puede deshacer.',
    confirmText: 'Sí, Completar',
    cancelText: 'Cancelar',
    loadingText: 'Completando...',
    variant: 'warning',
    loading: false,
    onConfirm: async () => {
      confirmModal.value.loading = true;
      try {
        await stockTransferService.complete(transfer.id);
        closeConfirmModal();
        await fetchTransfers();
        showSuccess('Traslado completado exitosamente');
      } catch (error) {
        console.error('Error al completar traslado:', error);
        showError(error.response?.data?.message || 'Error al completar el traslado');
        confirmModal.value.loading = false;
      }
    }
  };
};

const cancelTransfer = async (transfer) => {
  confirmModal.value = {
    show: true,
    title: 'Cancelar Traslado',
    subtitle: transfer.reference_number,
    message: `¿Estás seguro de que deseas cancelar el traslado ${transfer.reference_number}?`,
    description: 'El traslado será marcado como cancelado y no se moverá ningún inventario.',
    confirmText: 'Sí, Cancelar',
    cancelText: 'No',
    loadingText: 'Cancelando...',
    variant: 'danger',
    loading: false,
    onConfirm: async () => {
      confirmModal.value.loading = true;
      try {
        await stockTransferService.cancel(transfer.id);
        closeConfirmModal();
        await fetchTransfers();
        showSuccess('Traslado cancelado exitosamente');
      } catch (error) {
        console.error('Error al cancelar traslado:', error);
        showError(error.response?.data?.message || 'Error al cancelar el traslado');
        confirmModal.value.loading = false;
      }
    }
  };
};

const clearFilters = () => {
  filters.value = { status: '', source_warehouse_id: '' };
  fetchTransfers();
};

const handleSaved = () => {
  closeModal();
  // Pequeño delay para asegurar que la BD se actualice
  setTimeout(() => {
    fetchTransfers();
  }, 100);
};

const getStatusColor = (status) => {
  const colors = {
    pending: { bg: 'bg-amber-50 dark:bg-amber-950', text: 'text-amber-600 dark:text-amber-400', badge: 'bg-amber-50 dark:bg-amber-950 text-amber-700 dark:text-amber-400 border-amber-100 dark:border-amber-800' },
    in_transit: { bg: 'bg-blue-50 dark:bg-blue-950', text: 'text-blue-600 dark:text-blue-400', badge: 'bg-blue-50 dark:bg-blue-950 text-blue-700 dark:text-blue-400 border-blue-100 dark:border-blue-800' },
    completed: { bg: 'bg-emerald-50 dark:bg-emerald-950', text: 'text-emerald-600 dark:text-emerald-400', badge: 'bg-emerald-50 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-400 border-emerald-100 dark:border-emerald-800' },
    cancelled: { bg: 'bg-rose-50 dark:bg-rose-950', text: 'text-rose-600 dark:text-rose-400', badge: 'bg-rose-50 dark:bg-rose-950 text-rose-700 dark:text-rose-400 border-rose-100 dark:border-rose-800' }
  };
  return colors[status] || colors.pending;
};

const getStatusText = (status) => {
  const texts = {
    pending: 'Pendiente',
    in_transit: 'En Tránsito',
    completed: 'Completado',
    cancelled: 'Cancelado'
  };
  return texts[status] || status;
};

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('es-ES', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  });
};

onMounted(() => {
  fetchWarehouses();
  fetchTransfers();
});

// Exponer métodos para que el componente padre pueda llamarlos
defineExpose({
  fetchTransfers,
  openCreateModal,
  fetchWarehouses
});
</script>

<style scoped>
.animate-fade-in {
  animation: fadeIn 0.3s ease-in;
}

@keyframes fadeIn {
  from {
    opacity: 0;
    transform: translateY(-10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}
</style>
