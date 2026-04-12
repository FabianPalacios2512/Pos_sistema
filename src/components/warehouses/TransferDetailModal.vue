<template>
  <Teleport to="body">
    <div 
      class="fixed top-0 left-0 right-0 bottom-0 bg-black/50 dark:bg-black/60 flex items-center justify-center p-4"
      style="z-index: 99999; position: fixed; inset: 0;">
      <div class="bg-[#f8f9fa] dark:bg-[#1e1f20] rounded-2xl max-w-4xl w-full max-h-[90vh] overflow-hidden border border-[#dadce0] dark:border-[#3a3a3f]">
        
        <!-- Header -->
        <div class="bg-[#f8f9fa] dark:bg-[#1e1f20] border-b border-[#e8eaed] dark:border-[#3a3a3f] px-6 py-4 flex items-center justify-between">
          <div class="flex items-center space-x-3">
            <div class="w-10 h-10 bg-[#e8f0fe] dark:bg-[#1a73e8]/20 rounded-2xl flex items-center justify-center">
              <svg class="w-5 h-5 text-[#1a73e8] dark:text-[#8ab4f8]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
              </svg>
            </div>
            <div>
              <h3 class="text-base font-medium text-[#1e1f20] dark:text-[#e3e3e3]">Detalle del Traslado</h3>
              <p class="text-xs text-[#5f6368] dark:text-[#9aa0a6]">{{ transfer.reference_number }}</p>
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

        <!-- Body -->
        <div class="p-6 overflow-y-auto max-h-[calc(90vh-140px)]">
          
          <!-- Información General -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
          
          <!-- Estado -->
          <div class="bg-white dark:bg-[#282a2c] rounded-xl p-4 border border-[#e8eaed] dark:border-[#3a3a3f]">
            <p class="text-xs font-medium text-[#5f6368] dark:text-[#9aa0a6] mb-2">Estado</p>
            <span :class="[
              'px-3 py-1 rounded-full text-xs font-medium',
              getStatusClass(transfer.status)
            ]">
              {{ getStatusText(transfer.status) }}
            </span>
          </div>

          <!-- Fechas -->
          <div class="bg-white dark:bg-[#282a2c] rounded-xl p-4 border border-[#e8eaed] dark:border-[#3a3a3f]">
            <p class="text-xs font-medium text-[#5f6368] dark:text-[#9aa0a6] mb-2">Fechas</p>
            <div class="space-y-1">
              <p class="text-xs text-[#3c4043] dark:text-[#bdc1c6]">
                <span class="font-medium">Creado:</span> {{ formatDate(transfer.created_at) }}
              </p>
              <p v-if="transfer.completed_at" class="text-xs text-[#3c4043] dark:text-[#bdc1c6]">
                <span class="font-medium">Completado:</span> {{ formatDate(transfer.completed_at) }}
              </p>
            </div>
          </div>

        </div>

        <!-- Flujo de Traslado -->
        <div class="bg-[#e8f0fe] dark:bg-[#1a73e8]/10 rounded-xl p-4 mb-6 border border-[#c6dafc] dark:border-[#1a73e8]/30">
          <div class="flex items-center justify-between">
            <div class="flex-1">
              <p class="text-xs font-medium text-[#5f6368] dark:text-[#9aa0a6] mb-1">Origen</p>
              <p class="text-sm font-medium text-[#1e1f20] dark:text-[#e3e3e3]">{{ transfer.source_warehouse?.name }}</p>
            </div>
            
            <div class="flex items-center space-x-2 px-4">
              <div class="h-0.5 w-8 bg-[#1a73e8] dark:bg-[#8ab4f8]"></div>
              <svg class="w-5 h-5 text-[#1a73e8] dark:text-[#8ab4f8]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
              </svg>
              <div class="h-0.5 w-8 bg-[#9334e6] dark:bg-[#c58af9]"></div>
            </div>
            
            <div class="flex-1 text-right">
              <p class="text-xs font-medium text-[#5f6368] dark:text-[#9aa0a6] mb-1">Destino</p>
              <p class="text-sm font-medium text-[#1e1f20] dark:text-[#e3e3e3]">{{ transfer.destination_warehouse?.name }}</p>
            </div>
          </div>
        </div>

        <!-- Notas -->
        <div v-if="transfer.notes" class="bg-[#fef7e0] dark:bg-[#f9ab00]/10 border border-[#fdd663] dark:border-[#f9ab00]/30 rounded-xl p-4 mb-6">
          <p class="text-xs font-medium text-[#e37400] dark:text-[#fdd663] mb-1">Notas</p>
          <p class="text-sm text-[#5f6368] dark:text-[#bdc1c6]">{{ transfer.notes }}</p>
        </div>

        <!-- Tabla de Productos -->
        <div class="bg-white dark:bg-[#282a2c] border border-[#e8eaed] dark:border-[#3a3a3f] rounded-xl overflow-hidden">
          <div class="bg-[#f8f9fa] dark:bg-[#282a2c] border-b border-[#e8eaed] dark:border-[#3a3a3f] px-4 py-3">
            <h4 class="text-sm font-medium text-[#1e1f20] dark:text-[#e3e3e3]">Productos</h4>
          </div>
          
          <table class="min-w-full divide-y divide-[#e8eaed] dark:divide-[#3a3a3f]">
            <thead class="bg-[#f8f9fa] dark:bg-[#282a2c]">
              <tr>
                <th class="px-4 py-3 text-left text-xs font-medium text-[#5f6368] dark:text-[#9aa0a6] uppercase tracking-wide">
                  Producto
                </th>
                <th class="px-4 py-3 text-center text-xs font-medium text-[#5f6368] dark:text-[#9aa0a6] uppercase tracking-wide">
                  Cantidad
                </th>
                <th v-if="transfer.status === 'completed'" class="px-4 py-3 text-center text-xs font-medium text-[#5f6368] dark:text-[#9aa0a6] uppercase tracking-wide">
                  Recibido
                </th>
              </tr>
            </thead>
            <tbody class="bg-white dark:bg-[#282a2c] divide-y divide-[#e8eaed] dark:divide-[#3a3a3f]">
              <tr v-for="item in transfer.items" :key="item.id" class="hover:bg-[#f1f3f4] dark:hover:bg-[#35363a] transition-colors">
                <td class="px-4 py-3">
                  <p class="text-sm font-medium text-[#1e1f20] dark:text-[#e3e3e3]">{{ item.product?.name }}</p>
                  <p v-if="item.product?.code" class="text-xs text-[#5f6368] dark:text-[#9aa0a6]">Código: {{ item.product.code }}</p>
                </td>
                <td class="px-4 py-3 text-center">
                  <span class="text-sm font-medium text-[#1e1f20] dark:text-[#e3e3e3]">{{ item.quantity }}</span>
                </td>
                <td v-if="transfer.status === 'completed'" class="px-4 py-3 text-center">
                  <span class="px-2 py-1 bg-[#e6f4ea] dark:bg-[#1e8e3e]/20 text-[#1e8e3e] dark:text-[#81c995] text-xs font-medium rounded-full border border-[#ceead6] dark:border-[#1e8e3e]/30">
                    {{ item.received_quantity || item.quantity }}
                  </span>
                </td>
              </tr>
            </tbody>
          </table>

          <!-- Total -->
          <div class="bg-[#f8f9fa] dark:bg-[#282a2c] border-t border-[#e8eaed] dark:border-[#3a3a3f] px-4 py-3 flex justify-end">
            <div class="text-right">
              <p class="text-xs text-[#5f6368] dark:text-[#9aa0a6] mb-1">Total de Productos</p>
              <p class="text-lg font-medium text-[#1e1f20] dark:text-[#e3e3e3]">{{ totalProducts }} unidades</p>
            </div>
          </div>
        </div>

      </div>

      <!-- Footer -->
      <div class="bg-[#f8f9fa] dark:bg-[#282a2c] border-t border-[#e8eaed] dark:border-[#3a3a3f] px-6 py-4 flex justify-between">
        <div class="flex space-x-2">
          <button 
            v-if="transfer.status === 'pending'"
            @click="handleComplete"
            class="px-4 py-2 bg-[#1e8e3e] hover:bg-[#188038] text-white text-sm font-medium rounded-full transition-colors flex items-center space-x-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
            <span>Completar Traslado</span>
          </button>
          
          <button 
            v-if="transfer.status === 'pending'"
            @click="handleCancel"
            class="px-4 py-2 bg-[#d93025] hover:bg-[#c5221f] text-white text-sm font-medium rounded-full transition-colors flex items-center space-x-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
            <span>Cancelar Traslado</span>
          </button>
        </div>

        <button 
          @click="$emit('close')"
          type="button"
          class="px-4 py-2.5 bg-white dark:bg-[#35363a] border border-[#dadce0] dark:border-[#3a3a3f] text-[#3c4043] dark:text-[#e3e3e3] text-sm font-medium rounded-full hover:bg-[#f1f3f4] dark:hover:bg-[#44464a] transition-colors">
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
    'pending': 'bg-[#fef7e0] dark:bg-[#f9ab00]/20 text-[#e37400] dark:text-[#fdd663] border border-[#fdd663] dark:border-[#f9ab00]/30',
    'in_transit': 'bg-[#e8f0fe] dark:bg-[#1a73e8]/20 text-[#1a73e8] dark:text-[#8ab4f8] border border-[#c6dafc] dark:border-[#1a73e8]/30',
    'completed': 'bg-[#e6f4ea] dark:bg-[#1e8e3e]/20 text-[#1e8e3e] dark:text-[#81c995] border border-[#ceead6] dark:border-[#1e8e3e]/30',
    'cancelled': 'bg-[#fce8e6] dark:bg-[#d93025]/20 text-[#d93025] dark:text-[#f28b82] border border-[#f5c6cb] dark:border-[#d93025]/30'
  };
  return classes[status] || 'bg-[#f1f3f4] dark:bg-[#35363a] text-[#5f6368] dark:text-[#9aa0a6] border border-[#e8eaed] dark:border-[#3a3a3f]';
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
        showSuccess('Traslado completado exitosamente');
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
        showSuccess('Traslado cancelado exitosamente');
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
