<template>
  <Teleport to="body">
    <div 
      class="fixed top-0 left-0 right-0 bottom-0 bg-black/50 dark:bg-black/70 flex items-center justify-center p-4"
      style="z-index: 99999; position: fixed; inset: 0;">
      <div class="bg-white dark:bg-zinc-900 rounded-2xl shadow-2xl dark:shadow-black/50 max-w-4xl w-full max-h-[90vh] overflow-hidden border border-gray-300 dark:border-zinc-800">
        
        <!-- Header -->
        <div class="bg-white dark:bg-zinc-900 border-b border-gray-200 dark:border-zinc-800 px-6 py-4 flex items-center justify-between">
          <div class="flex items-center space-x-3">
            <div class="w-10 h-10 bg-blue-50 dark:bg-blue-950 rounded-lg flex items-center justify-center">
              <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
              </svg>
            </div>
            <div>
              <h3 class="text-base font-bold text-gray-900 dark:text-white">Detalle del Traslado</h3>
              <p class="text-xs text-gray-500 dark:text-zinc-400">{{ transfer.reference_number }}</p>
            </div>
          </div>
          <button 
            @click="$emit('close')"
            class="p-2 hover:bg-gray-100 dark:hover:bg-zinc-800 rounded-lg transition-colors">
            <svg class="w-5 h-5 text-gray-500 dark:text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
          </button>
        </div>

        <!-- Body -->
        <div class="p-6 overflow-y-auto max-h-[calc(90vh-140px)]">
          
          <!-- Información General -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
          
          <!-- Estado -->
          <div class="bg-gray-50 dark:bg-zinc-800/50 rounded-lg p-4">
            <p class="text-xs font-medium text-gray-600 dark:text-zinc-400 mb-2">Estado</p>
            <span :class="[
              'px-3 py-1 rounded-full text-xs font-semibold',
              getStatusClass(transfer.status)
            ]">
              {{ getStatusText(transfer.status) }}
            </span>
          </div>

          <!-- Fechas -->
          <div class="bg-gray-50 dark:bg-zinc-800/50 rounded-lg p-4">
            <p class="text-xs font-medium text-gray-600 dark:text-zinc-400 mb-2">Fechas</p>
            <div class="space-y-1">
              <p class="text-xs text-gray-700 dark:text-zinc-300">
                <span class="font-semibold">Creado:</span> {{ formatDate(transfer.created_at) }}
              </p>
              <p v-if="transfer.completed_at" class="text-xs text-gray-700 dark:text-zinc-300">
                <span class="font-semibold">Completado:</span> {{ formatDate(transfer.completed_at) }}
              </p>
            </div>
          </div>

        </div>

        <!-- Flujo de Traslado -->
        <div class="bg-gradient-to-r from-blue-50 to-purple-50 dark:from-blue-950/30 dark:to-purple-950/30 rounded-lg p-4 mb-6 border border-blue-100 dark:border-blue-900/30">
          <div class="flex items-center justify-between">
            <div class="flex-1">
              <p class="text-xs font-medium text-gray-600 dark:text-zinc-400 mb-1">Origen</p>
              <p class="text-sm font-bold text-gray-900 dark:text-white">{{ transfer.source_warehouse?.name }}</p>
            </div>
            
            <div class="flex items-center space-x-2 px-4">
              <div class="h-0.5 w-8 bg-blue-400 dark:bg-blue-600"></div>
              <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
              </svg>
              <div class="h-0.5 w-8 bg-purple-400 dark:bg-purple-600"></div>
            </div>
            
            <div class="flex-1 text-right">
              <p class="text-xs font-medium text-gray-600 dark:text-zinc-400 mb-1">Destino</p>
              <p class="text-sm font-bold text-gray-900 dark:text-white">{{ transfer.destination_warehouse?.name }}</p>
            </div>
          </div>
        </div>

        <!-- Notas -->
        <div v-if="transfer.notes" class="bg-yellow-50 dark:bg-yellow-950/30 border border-yellow-200 dark:border-yellow-900/50 rounded-lg p-4 mb-6">
          <p class="text-xs font-semibold text-yellow-800 dark:text-yellow-200 mb-1">Notas</p>
          <p class="text-sm text-yellow-900 dark:text-yellow-100">{{ transfer.notes }}</p>
        </div>

        <!-- Tabla de Productos -->
        <div class="bg-white dark:bg-zinc-900 border border-gray-200 dark:border-zinc-800 rounded-lg overflow-hidden">
          <div class="bg-gray-50 dark:bg-zinc-900 border-b border-gray-200 dark:border-zinc-800 px-4 py-3">
            <h4 class="text-sm font-bold text-gray-900 dark:text-white">Productos</h4>
          </div>
          
          <table class="min-w-full divide-y divide-gray-200 dark:divide-zinc-800">
            <thead class="bg-gray-50 dark:bg-zinc-900">
              <tr>
                <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 dark:text-zinc-400 uppercase tracking-wide">
                  Producto
                </th>
                <th class="px-4 py-3 text-center text-xs font-bold text-gray-600 dark:text-zinc-400 uppercase tracking-wide">
                  Cantidad
                </th>
                <th v-if="transfer.status === 'completed'" class="px-4 py-3 text-center text-xs font-bold text-gray-600 dark:text-zinc-400 uppercase tracking-wide">
                  Recibido
                </th>
              </tr>
            </thead>
            <tbody class="bg-white dark:bg-zinc-900 divide-y divide-gray-200 dark:divide-zinc-800">
              <tr v-for="item in transfer.items" :key="item.id" class="hover:bg-gray-50 dark:hover:bg-zinc-800/50 transition-colors">
                <td class="px-4 py-3">
                  <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ item.product?.name }}</p>
                  <p v-if="item.product?.code" class="text-xs text-gray-500 dark:text-zinc-400">Código: {{ item.product.code }}</p>
                </td>
                <td class="px-4 py-3 text-center">
                  <span class="text-sm font-semibold text-gray-900 dark:text-white">{{ item.quantity }}</span>
                </td>
                <td v-if="transfer.status === 'completed'" class="px-4 py-3 text-center">
                  <span class="px-2 py-1 bg-emerald-50 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-400 text-xs font-semibold rounded-full border border-emerald-100 dark:border-emerald-800">
                    {{ item.received_quantity || item.quantity }}
                  </span>
                </td>
              </tr>
            </tbody>
          </table>

          <!-- Total -->
          <div class="bg-gray-50 dark:bg-zinc-900 border-t border-gray-200 dark:border-zinc-800 px-4 py-3 flex justify-end">
            <div class="text-right">
              <p class="text-xs text-gray-600 dark:text-zinc-400 mb-1">Total de Productos</p>
              <p class="text-lg font-bold text-gray-900 dark:text-white">{{ totalProducts }} unidades</p>
            </div>
          </div>
        </div>

      </div>

      <!-- Footer -->
      <div class="bg-gray-50 dark:bg-zinc-900 border-t border-gray-200 dark:border-zinc-800 px-6 py-4 flex justify-between">
        <div class="flex space-x-2">
          <button 
            v-if="transfer.status === 'pending'"
            @click="handleComplete"
            class="px-4 py-2 bg-emerald-600 dark:bg-emerald-700 hover:bg-emerald-700 dark:hover:bg-emerald-600 text-white text-sm font-semibold rounded-lg transition-colors flex items-center space-x-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
            <span>Completar Traslado</span>
          </button>
          
          <button 
            v-if="transfer.status === 'pending'"
            @click="handleCancel"
            class="px-4 py-2 bg-red-600 dark:bg-red-700 hover:bg-red-700 dark:hover:bg-red-600 text-white text-sm font-semibold rounded-lg transition-colors flex items-center space-x-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
            <span>Cancelar Traslado</span>
          </button>
        </div>

        <button 
          @click="$emit('close')"
          type="button"
          class="px-4 py-2.5 bg-white dark:bg-zinc-800 border border-gray-300 dark:border-zinc-700 text-gray-700 dark:text-zinc-200 text-sm font-semibold rounded-lg hover:bg-gray-50 dark:hover:bg-zinc-700 transition-colors">
          Cerrar
        </button>
      </div>

    </div>
    </div>
  </Teleport>

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
</template>

<script setup>
import { computed, ref } from 'vue';
import { stockTransferService } from '@/services/stockTransferService';
import { useToast } from '@/composables/useToast';
import ConfirmModal from '@/components/ConfirmModal.vue';

const props = defineProps({
  transfer: {
    type: Object,
    required: true
  }
});

const emit = defineEmits(['close', 'updated']);

const { showSuccess, showError } = useToast();

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

const totalProducts = computed(() => {
  return props.transfer.items?.reduce((sum, item) => sum + item.quantity, 0) || 0;
});

const getStatusClass = (status) => {
  const classes = {
    'pending': 'bg-yellow-100 text-yellow-700',
    'in_transit': 'bg-blue-100 text-blue-700',
    'completed': 'bg-green-100 text-green-700',
    'cancelled': 'bg-red-100 text-red-700'
  };
  return classes[status] || 'bg-gray-100 text-gray-700';
};

const getStatusText = (status) => {
  const texts = {
    'pending': 'Pendiente',
    'in_transit': 'En Tránsito',
    'completed': 'Completado',
    'cancelled': 'Cancelado'
  };
  return texts[status] || status;
};

const formatDate = (dateString) => {
  if (!dateString) return '-';
  const date = new Date(dateString);
  return new Intl.DateTimeFormat('es-ES', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  }).format(date);
};

const closeConfirmModal = () => {
  confirmModal.value.show = false;
  confirmModal.value.loading = false;
};

const handleComplete = async () => {
  confirmModal.value = {
    show: true,
    title: 'Completar Traslado',
    subtitle: props.transfer.reference_number,
    message: `¿Estás seguro de completar el traslado ${props.transfer.reference_number}?`,
    description: 'El stock se moverá entre las sedes y esta acción no se puede deshacer.',
    confirmText: 'Sí, Completar',
    cancelText: 'Cancelar',
    loadingText: 'Completando...',
    variant: 'warning',
    loading: false,
    onConfirm: async () => {
      confirmModal.value.loading = true;
      try {
        await stockTransferService.complete(props.transfer.id);
        closeConfirmModal();
        showSuccess('✅ Traslado completado exitosamente');
        emit('updated');
      } catch (error) {
        console.error('Error al completar traslado:', error);
        showError(error.response?.data?.message || 'Error al completar el traslado');
        confirmModal.value.loading = false;
      }
    }
  };
};

const handleCancel = async () => {
  confirmModal.value = {
    show: true,
    title: 'Cancelar Traslado',
    subtitle: props.transfer.reference_number,
    message: `¿Estás seguro de cancelar el traslado ${props.transfer.reference_number}?`,
    description: 'Esta acción no se puede deshacer y el traslado quedará marcado como cancelado.',
    confirmText: 'Sí, Cancelar',
    cancelText: 'No',
    loadingText: 'Cancelando...',
    variant: 'danger',
    loading: false,
    onConfirm: async () => {
      confirmModal.value.loading = true;
      try {
        await stockTransferService.cancel(props.transfer.id);
        closeConfirmModal();
        showSuccess('✅ Traslado cancelado exitosamente');
        emit('updated');
      } catch (error) {
        console.error('Error al cancelar traslado:', error);
        showError(error.response?.data?.message || 'Error al cancelar el traslado');
        confirmModal.value.loading = false;
      }
    }
  };
};
</script>
