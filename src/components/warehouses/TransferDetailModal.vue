<template>
  <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-2xl shadow-2xl max-w-4xl w-full max-h-[90vh] overflow-hidden">
      
      <!-- Header -->
      <div class="bg-white border-b border-gray-200 px-6 py-4 flex items-center justify-between">
        <div class="flex items-center space-x-3">
          <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
          </div>
          <div>
            <h3 class="text-base font-bold text-gray-900">Detalle del Traslado</h3>
            <p class="text-xs text-gray-500">{{ transfer.reference_number }}</p>
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
        
        <!-- Información General -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
          
          <!-- Estado -->
          <div class="bg-gray-50 rounded-lg p-4">
            <p class="text-xs font-medium text-gray-600 mb-2">Estado</p>
            <span :class="[
              'px-3 py-1 rounded-full text-xs font-semibold',
              getStatusClass(transfer.status)
            ]">
              {{ getStatusText(transfer.status) }}
            </span>
          </div>

          <!-- Fechas -->
          <div class="bg-gray-50 rounded-lg p-4">
            <p class="text-xs font-medium text-gray-600 mb-2">Fechas</p>
            <div class="space-y-1">
              <p class="text-xs text-gray-700">
                <span class="font-semibold">Creado:</span> {{ formatDate(transfer.created_at) }}
              </p>
              <p v-if="transfer.completed_at" class="text-xs text-gray-700">
                <span class="font-semibold">Completado:</span> {{ formatDate(transfer.completed_at) }}
              </p>
            </div>
          </div>

        </div>

        <!-- Flujo de Traslado -->
        <div class="bg-gradient-to-r from-blue-50 to-purple-50 rounded-lg p-4 mb-6">
          <div class="flex items-center justify-between">
            <div class="flex-1">
              <p class="text-xs font-medium text-gray-600 mb-1">Origen</p>
              <p class="text-sm font-bold text-gray-900">{{ transfer.source_warehouse?.name }}</p>
            </div>
            
            <div class="flex items-center space-x-2 px-4">
              <div class="h-0.5 w-8 bg-blue-400"></div>
              <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
              </svg>
              <div class="h-0.5 w-8 bg-purple-400"></div>
            </div>
            
            <div class="flex-1 text-right">
              <p class="text-xs font-medium text-gray-600 mb-1">Destino</p>
              <p class="text-sm font-bold text-gray-900">{{ transfer.destination_warehouse?.name }}</p>
            </div>
          </div>
        </div>

        <!-- Notas -->
        <div v-if="transfer.notes" class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
          <p class="text-xs font-semibold text-yellow-800 mb-1">Notas</p>
          <p class="text-sm text-yellow-900">{{ transfer.notes }}</p>
        </div>

        <!-- Tabla de Productos -->
        <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
          <div class="bg-gray-50 border-b border-gray-200 px-4 py-3">
            <h4 class="text-sm font-bold text-gray-900">Productos</h4>
          </div>
          
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wide">
                  Producto
                </th>
                <th class="px-4 py-3 text-center text-xs font-bold text-gray-600 uppercase tracking-wide">
                  Cantidad
                </th>
                <th v-if="transfer.status === 'completed'" class="px-4 py-3 text-center text-xs font-bold text-gray-600 uppercase tracking-wide">
                  Recibido
                </th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="item in transfer.items" :key="item.id" class="hover:bg-gray-50">
                <td class="px-4 py-3">
                  <p class="text-sm font-semibold text-gray-900">{{ item.product?.name }}</p>
                  <p v-if="item.product?.code" class="text-xs text-gray-500">Código: {{ item.product.code }}</p>
                </td>
                <td class="px-4 py-3 text-center">
                  <span class="text-sm font-semibold text-gray-900">{{ item.quantity }}</span>
                </td>
                <td v-if="transfer.status === 'completed'" class="px-4 py-3 text-center">
                  <span class="px-2 py-1 bg-green-100 text-green-700 text-xs font-semibold rounded-full">
                    {{ item.received_quantity || item.quantity }}
                  </span>
                </td>
              </tr>
            </tbody>
          </table>

          <!-- Total -->
          <div class="bg-gray-50 border-t border-gray-200 px-4 py-3 flex justify-end">
            <div class="text-right">
              <p class="text-xs text-gray-600 mb-1">Total de Productos</p>
              <p class="text-lg font-bold text-gray-900">{{ totalProducts }} unidades</p>
            </div>
          </div>
        </div>

      </div>

      <!-- Footer -->
      <div class="bg-gray-50 border-t border-gray-200 px-6 py-4 flex justify-between">
        <div class="flex space-x-2">
          <button 
            v-if="transfer.status === 'pending'"
            @click="handleComplete"
            class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-semibold rounded-lg transition-colors flex items-center space-x-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
            <span>Completar Traslado</span>
          </button>
          
          <button 
            v-if="transfer.status === 'pending'"
            @click="handleCancel"
            class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-semibold rounded-lg transition-colors flex items-center space-x-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
            <span>Cancelar Traslado</span>
          </button>
        </div>

        <button 
          @click="$emit('close')"
          type="button"
          class="px-4 py-2.5 bg-white border border-gray-300 text-gray-700 text-sm font-semibold rounded-lg hover:bg-gray-50 transition-colors">
          Cerrar
        </button>
      </div>

    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { stockTransferService } from '@/services/stockTransferService';

const props = defineProps({
  transfer: {
    type: Object,
    required: true
  }
});

const emit = defineEmits(['close', 'updated']);

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

const handleComplete = async () => {
  if (!confirm('¿Estás seguro de completar este traslado? El stock se moverá entre las sedes.')) {
    return;
  }

  try {
    await stockTransferService.complete(props.transfer.id);
    alert('Traslado completado exitosamente');
    emit('updated');
  } catch (error) {
    console.error('Error al completar traslado:', error);
    alert(error.response?.data?.message || 'Error al completar el traslado');
  }
};

const handleCancel = async () => {
  if (!confirm('¿Estás seguro de cancelar este traslado? Esta acción no se puede deshacer.')) {
    return;
  }

  try {
    await stockTransferService.cancel(props.transfer.id);
    alert('Traslado cancelado exitosamente');
    emit('updated');
  } catch (error) {
    console.error('Error al cancelar traslado:', error);
    alert(error.response?.data?.message || 'Error al cancelar el traslado');
  }
};
</script>
