<template>
  <Teleport to="body">
    <div 
      class="fixed inset-0 bg-gray-900/60 dark:bg-black/70 backdrop-blur-sm flex items-center justify-center p-4"
      style="z-index: 99999;"
      @mousedown.self="$emit('close')">
      <div class="bg-white dark:bg-zinc-900 rounded-lg max-w-5xl w-full max-h-[90vh] overflow-hidden border border-gray-200 dark:border-zinc-800 shadow-xl dark:shadow-black/50">
        
        <!-- Header -->
        <div class="border-b border-gray-200 dark:border-zinc-800 px-5 py-3 flex items-center justify-between">
          <div class="flex items-center gap-3">
            <div class="w-8 h-8 bg-gray-100 dark:bg-zinc-800 rounded-md flex items-center justify-center border border-gray-200 dark:border-zinc-700">
              <svg class="w-4 h-4 text-gray-600 dark:text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
              </svg>
            </div>
            <div>
              <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Detalle del Traslado</h3>
              <p class="text-[11px] text-gray-500 dark:text-zinc-500 font-mono">{{ transfer.reference_number }}</p>
            </div>
          </div>
          <button @click="$emit('close')" class="p-1.5 hover:bg-gray-100 dark:hover:bg-zinc-800 rounded-md transition-colors">
            <svg class="w-4 h-4 text-gray-400 dark:text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
          </button>
        </div>

        <!-- Body -->
        <div class="p-5 overflow-y-auto max-h-[calc(90vh-120px)] space-y-4">
          
          <!-- Info Grid: Status + Dates -->
          <div class="grid grid-cols-2 gap-3">
            <div class="bg-gray-50 dark:bg-zinc-800/50 rounded-md px-4 py-3 border border-gray-200 dark:border-zinc-800">
              <p class="text-[10px] font-medium text-gray-500 dark:text-zinc-500 uppercase tracking-wide mb-1.5">Estado</p>
              <span :class="['inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold border', getStatusClass(transfer.status)]">
                {{ getStatusText(transfer.status) }}
              </span>
            </div>
            <div class="bg-gray-50 dark:bg-zinc-800/50 rounded-md px-4 py-3 border border-gray-200 dark:border-zinc-800">
              <p class="text-[10px] font-medium text-gray-500 dark:text-zinc-500 uppercase tracking-wide mb-1.5">Fechas</p>
              <p class="text-xs text-gray-700 dark:text-zinc-300">
                <span class="font-medium">Creado:</span> {{ formatDate(transfer.created_at) }}
              </p>
              <p v-if="transfer.completed_at" class="text-xs text-gray-700 dark:text-zinc-300 mt-0.5">
                <span class="font-medium">Completado:</span> {{ formatDate(transfer.completed_at) }}
              </p>
            </div>
          </div>

          <!-- Transfer Flow -->
          <div class="bg-blue-50 dark:bg-blue-950/30 rounded-md px-4 py-3 border border-blue-200 dark:border-blue-800/40">
            <div class="flex items-center justify-between">
              <div class="flex-1">
                <p class="text-[10px] font-medium text-gray-500 dark:text-zinc-500 uppercase tracking-wide mb-0.5">Origen</p>
                <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ transfer.source_warehouse?.name }}</p>
              </div>
              <div class="flex items-center gap-1.5 px-4">
                <div class="h-px w-6 bg-blue-400 dark:bg-blue-500"></div>
                <svg class="w-4 h-4 text-blue-500 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                </svg>
                <div class="h-px w-6 bg-purple-400 dark:bg-purple-500"></div>
              </div>
              <div class="flex-1 text-right">
                <p class="text-[10px] font-medium text-gray-500 dark:text-zinc-500 uppercase tracking-wide mb-0.5">Destino</p>
                <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ transfer.destination_warehouse?.name }}</p>
              </div>
            </div>
          </div>

          <!-- Notes -->
          <div v-if="transfer.notes" class="flex items-start gap-2.5 px-3 py-2.5 bg-amber-50 dark:bg-amber-950/30 border border-amber-200 dark:border-amber-800/40 rounded-md">
            <svg class="w-4 h-4 text-amber-600 dark:text-amber-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
              <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 8.25h9m-9 3H12m-9.75 1.51c0 1.6 1.123 2.994 2.707 3.227 1.087.16 2.185.283 3.293.369V21l4.076-4.076a1.526 1.526 0 011.037-.443 48.282 48.282 0 005.68-.494c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0012 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018z"></path>
            </svg>
            <div>
              <p class="text-[10px] font-semibold text-amber-700 dark:text-amber-400 uppercase tracking-wide mb-0.5">Notas</p>
              <p class="text-xs text-amber-800 dark:text-amber-300">{{ transfer.notes }}</p>
            </div>
          </div>

          <!-- Products Table -->
          <div class="border border-gray-200 dark:border-zinc-800 rounded-lg overflow-hidden">
            <div class="bg-gray-50 dark:bg-zinc-800/50 border-b border-gray-200 dark:border-zinc-800 px-4 py-2.5">
              <h4 class="text-xs font-semibold text-gray-700 dark:text-zinc-300 uppercase tracking-wide">Productos</h4>
            </div>
            
            <table class="min-w-full">
              <thead>
                <tr class="border-b border-gray-200 dark:border-zinc-800 bg-gray-50 dark:bg-zinc-800/30">
                  <th class="px-4 py-2 text-left text-[10px] font-semibold text-gray-500 dark:text-zinc-500 uppercase tracking-wider">Producto</th>
                  <th class="px-4 py-2 text-center text-[10px] font-semibold text-gray-500 dark:text-zinc-500 uppercase tracking-wider">Cantidad</th>
                  <th v-if="transfer.status === 'completed'" class="px-4 py-2 text-center text-[10px] font-semibold text-gray-500 dark:text-zinc-500 uppercase tracking-wider">Recibido</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="item in transfer.items" :key="item.id" class="border-b border-gray-100 dark:border-zinc-800 last:border-0 hover:bg-gray-50 dark:hover:bg-zinc-800/50 transition-colors">
                  <td class="px-4 py-2.5">
                    <p class="text-sm font-medium text-gray-900 dark:text-zinc-200">{{ item.product?.name }}</p>
                    <div v-if="item.variant && item.variant.options_summary" class="flex items-center gap-1 mt-1 flex-wrap">
                      <span class="px-1.5 py-0.5 bg-purple-50 dark:bg-purple-950 text-purple-700 dark:text-purple-400 text-[9px] font-bold uppercase tracking-wide rounded border border-purple-100 dark:border-purple-800">Variante</span>
                      <span v-for="(opt, idx) in normalizeOptions(item.variant.options_summary)" :key="idx"
                        class="px-1.5 py-0.5 rounded text-[10px] font-medium bg-gray-100 dark:bg-zinc-700 text-gray-700 dark:text-zinc-300 border border-gray-200 dark:border-zinc-600">
                        {{ opt.name }}: {{ opt.value }}
                      </span>
                    </div>
                    <p v-if="item.product?.code" class="text-[10px] text-gray-400 dark:text-zinc-500 font-mono mt-0.5">{{ item.product.code }}</p>
                  </td>
                  <td class="px-4 py-2.5 text-center">
                    <span class="text-sm font-semibold text-gray-900 dark:text-zinc-200 tabular-nums">{{ item.quantity }}</span>
                  </td>
                  <td v-if="transfer.status === 'completed'" class="px-4 py-2.5 text-center">
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold bg-emerald-50 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-400 border border-emerald-100 dark:border-emerald-800">
                      {{ item.received_quantity || item.quantity }}
                    </span>
                  </td>
                </tr>
              </tbody>
            </table>

            <!-- Total -->
            <div class="bg-gray-50 dark:bg-zinc-800/50 border-t border-gray-200 dark:border-zinc-800 px-4 py-2.5 flex justify-end">
              <div class="text-right">
                <p class="text-[10px] text-gray-500 dark:text-zinc-500 uppercase tracking-wide">Total de Productos</p>
                <p class="text-base font-bold text-gray-900 dark:text-white tabular-nums">{{ totalProducts }} unidades</p>
              </div>
            </div>
          </div>

        </div>

        <!-- Footer -->
        <div class="border-t border-gray-200 dark:border-zinc-800 px-5 py-3 flex items-center justify-between">
          <div class="flex items-center gap-2">
            <button 
              v-if="transfer.status === 'pending'"
              @click="handleComplete"
              class="px-4 py-2 bg-emerald-700 dark:bg-emerald-600 hover:bg-emerald-800 dark:hover:bg-emerald-500 text-white text-sm font-medium rounded-md transition-colors flex items-center gap-1.5">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path>
              </svg>
              <span>Completar</span>
            </button>
            
            <button 
              v-if="transfer.status === 'pending'"
              @click="handleCancel"
              class="px-4 py-2 bg-white dark:bg-zinc-800 border border-rose-300 dark:border-rose-700 text-rose-700 dark:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-900/20 text-sm font-medium rounded-md transition-colors flex items-center gap-1.5">
              <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path>
              </svg>
              <span>Cancelar Traslado</span>
            </button>
          </div>

          <button 
            @click="$emit('close')"
            type="button"
            class="px-4 py-2 bg-white dark:bg-zinc-800 border border-gray-300 dark:border-zinc-700 text-gray-700 dark:text-zinc-300 text-sm font-medium rounded-md hover:bg-gray-50 dark:hover:bg-zinc-700 transition-colors">
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

// Normalize options_summary to [{name, value}] format
function normalizeOptions(optionsSummary) {
  if (!optionsSummary) return []
  if (Array.isArray(optionsSummary)) {
    return optionsSummary.filter(o => o && o.name)
  }
  if (typeof optionsSummary === 'object') {
    return Object.entries(optionsSummary).map(([k, v]) => ({ name: k, value: v }))
  }
  return []
}

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
    'pending': 'bg-amber-50 dark:bg-amber-950 text-amber-700 dark:text-amber-400 border-amber-100 dark:border-amber-800',
    'in_transit': 'bg-blue-50 dark:bg-blue-950 text-blue-700 dark:text-blue-400 border-blue-100 dark:border-blue-800',
    'completed': 'bg-emerald-50 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-400 border-emerald-100 dark:border-emerald-800',
    'cancelled': 'bg-rose-50 dark:bg-rose-950 text-rose-700 dark:text-rose-400 border-rose-100 dark:border-rose-800'
  };
  return classes[status] || 'bg-gray-50 dark:bg-zinc-800 text-gray-600 dark:text-zinc-400 border-gray-200 dark:border-zinc-700';
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
        emit('close');
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
        emit('close');
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
