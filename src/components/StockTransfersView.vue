<template>
  <div class="min-h-screen font-sans mx-8" style="background-color: #F4F6F8;">
    <div class="p-4 lg:p-6 space-y-6 pb-8 animate-fade-in">
      
      <!-- Header Elegante -->
      <div class="flex items-center justify-between pb-4 border-b border-gray-300">
        <div class="flex items-center space-x-4">
          <div class="w-12 h-12 bg-gradient-to-br from-purple-600 to-pink-600 rounded-xl flex items-center justify-center shadow-lg">
            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
            </svg>
          </div>
          <div>
            <h1 class="text-2xl font-bold text-gray-900">Traslados de Mercancía</h1>
            <p class="text-sm text-gray-600">Gestiona movimientos de inventario entre sedes</p>
          </div>
        </div>
        
        <div class="flex items-center space-x-3">
          <button 
            @click="fetchTransfers"
            class="px-4 py-2.5 bg-slate-50 hover:bg-slate-100 text-slate-600 text-sm font-semibold rounded-xl border border-slate-200 shadow-sm transition-all duration-200 flex items-center space-x-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
            </svg>
            <span>Actualizar</span>
          </button>
          
          <button 
            @click="openCreateModal"
            class="px-5 py-2.5 bg-gradient-to-r from-lime-400 to-green-400 hover:from-lime-500 hover:to-green-500 text-white text-sm font-bold rounded-xl shadow-lg transition-all duration-300 transform hover:scale-105 flex items-center space-x-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            <span>Nuevo Traslado</span>
          </button>
        </div>
      </div>

      <!-- Filtros -->
      <div class="bg-white rounded-lg shadow-sm p-3 border border-gray-200">
        <div class="flex flex-wrap items-center gap-3">
          <select 
            v-model="filters.status"
            @change="fetchTransfers"
            class="pl-3 pr-8 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 min-w-36">
            <option value="">Todos los estados</option>
            <option value="pending">Pendientes</option>
            <option value="in_transit">En tránsito</option>
            <option value="completed">Completados</option>
            <option value="cancelled">Cancelados</option>
          </select>

          <select 
            v-model="filters.source_warehouse_id"
            @change="fetchTransfers"
            class="pl-3 pr-8 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 min-w-48">
            <option value="">Todas las sedes origen</option>
            <option v-for="w in warehouses" :key="w.id" :value="w.id">{{ w.name }}</option>
          </select>

          <button 
            v-if="filters.status || filters.source_warehouse_id"
            @click="clearFilters" 
            class="p-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition-colors"
            title="Limpiar filtros">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
            </svg>
          </button>
        </div>
      </div>

      <!-- Lista de Traslados -->
      <div class="bg-white rounded-2xl p-5 border border-gray-300">
        <div class="bg-gray-50 border-b border-gray-200 px-5 py-4 flex items-center justify-between">
          <div>
            <h2 class="text-base font-bold text-gray-900">Historial de Traslados</h2>
            <p class="text-xs text-gray-500 mt-0.5">{{ transfers.length }} traslados registrados</p>
          </div>
        </div>

        <div v-if="loading" class="py-12 text-center">
          <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-purple-600"></div>
          <p class="mt-2 text-sm text-gray-600">Cargando traslados...</p>
        </div>

        <div v-else-if="transfers.length === 0" class="py-12 text-center">
          <div class="flex flex-col items-center space-y-3">
            <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center">
              <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
              </svg>
            </div>
            <div>
              <p class="text-sm font-semibold text-gray-700">No hay traslados</p>
              <p class="text-xs text-gray-500 mt-1">Crea tu primer traslado de mercancía</p>
            </div>
          </div>
        </div>

        <div v-else class="divide-y divide-gray-200">
          <div 
            v-for="transfer in transfers" 
            :key="transfer.id"
            class="p-4 hover:bg-gray-50 transition-colors">
            <div class="flex items-center justify-between">
              <div class="flex items-center space-x-4 flex-1">
                <div :class="[
                  'w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0',
                  getStatusColor(transfer.status).bg
                ]">
                  <svg class="w-6 h-6" :class="getStatusColor(transfer.status).text" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                  </svg>
                </div>

                <div class="flex-1 min-w-0">
                  <div class="flex items-center space-x-2 mb-1">
                    <h3 class="text-base font-bold text-gray-900">{{ transfer.reference_number }}</h3>
                    <span :class="[
                      'px-2 py-0.5 text-xs font-semibold rounded-full',
                      getStatusColor(transfer.status).badge
                    ]">
                      {{ getStatusText(transfer.status) }}
                    </span>
                  </div>
                  <div class="flex items-center space-x-4 text-xs text-gray-500">
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
                  <p v-if="transfer.notes" class="text-xs text-gray-600 mt-1">{{ transfer.notes }}</p>
                </div>
              </div>

              <div class="flex items-center space-x-2">
                <button 
                  @click="viewTransfer(transfer)"
                  class="p-2 bg-blue-50 hover:bg-blue-100 text-blue-600 rounded-lg transition-colors"
                  title="Ver detalle">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                  </svg>
                </button>
                <button 
                  v-if="transfer.status === 'pending'"
                  @click="completeTransfer(transfer)"
                  class="p-2 bg-green-50 hover:bg-green-100 text-green-600 rounded-lg transition-colors"
                  title="Completar traslado">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                  </svg>
                </button>
                <button 
                  v-if="transfer.status === 'pending'"
                  @click="cancelTransfer(transfer)"
                  class="p-2 bg-red-50 hover:bg-red-100 text-red-600 rounded-lg transition-colors"
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

    <!-- Modales -->
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
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { stockTransferService } from '@/services/stockTransferService';
import { warehouseService } from '@/services/warehouseService';
import StockTransferModal from '@/components/warehouses/StockTransferModal.vue';
import TransferDetailModal from '@/components/warehouses/TransferDetailModal.vue';

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

const fetchTransfers = async () => {
  console.log('🔄 fetchTransfers iniciado...');
  loading.value = true;
  try {
    const data = await stockTransferService.getAll(filters.value);
    transfers.value = Array.isArray(data) ? data : [];
    console.log('✅ Traslados cargados:', transfers.value.length, 'traslados');
  } catch (error) {
    console.error('❌ Error al cargar traslados:', error);
    transfers.value = [];
    alert('Error al cargar los traslados');
  } finally {
    loading.value = false;
  }
};

const fetchWarehouses = async () => {
  try {
    const data = await warehouseService.getAll();
    warehouses.value = Array.isArray(data) ? data : [];
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

const completeTransfer = async (transfer) => {
  if (!confirm(`¿Completar el traslado ${transfer.reference_number}?`)) return;
  
  try {
    await stockTransferService.complete(transfer.id);
    await fetchTransfers();
    alert('Traslado completado exitosamente');
  } catch (error) {
    console.error('Error al completar traslado:', error);
    alert(error.response?.data?.message || 'Error al completar el traslado');
  }
};

const cancelTransfer = async (transfer) => {
  if (!confirm(`¿Cancelar el traslado ${transfer.reference_number}?`)) return;
  
  try {
    await stockTransferService.cancel(transfer.id);
    await fetchTransfers();
    alert('Traslado cancelado exitosamente');
  } catch (error) {
    console.error('Error al cancelar traslado:', error);
    alert(error.response?.data?.message || 'Error al cancelar el traslado');
  }
};

const clearFilters = () => {
  filters.value = { status: '', source_warehouse_id: '' };
  fetchTransfers();
};

const handleSaved = () => {
  console.log('✅ handleSaved ejecutado - cerrando modal y recargando transferencias...');
  closeModal();
  // Pequeño delay para asegurar que la BD se actualice
  setTimeout(() => {
    fetchTransfers();
  }, 100);
};

const getStatusColor = (status) => {
  const colors = {
    pending: { bg: 'bg-yellow-100', text: 'text-yellow-600', badge: 'bg-yellow-100 text-yellow-700' },
    in_transit: { bg: 'bg-blue-100', text: 'text-blue-600', badge: 'bg-blue-100 text-blue-700' },
    completed: { bg: 'bg-green-100', text: 'text-green-600', badge: 'bg-green-100 text-green-700' },
    cancelled: { bg: 'bg-red-100', text: 'text-red-600', badge: 'bg-red-100 text-red-700' }
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
