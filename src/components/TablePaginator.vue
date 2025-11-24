<template>
  <!-- Paginador estándar reutilizable -->
  <div class="bg-white border-t border-gray-200 px-4 py-3 flex items-center justify-between">
    <!-- Información izquierda -->
    <div class="flex items-center space-x-3">
      <!-- Selector items por página -->
      <div class="flex items-center space-x-2">
        <span class="text-xs font-medium text-gray-700">Mostrar:</span>
        <select :value="itemsPerPage" 
                @change="$emit('update:itemsPerPage', parseInt($event.target.value))"
                class="border border-gray-300 rounded-lg px-2 py-1 text-xs focus:outline-none focus:ring-2 focus:ring-blue-500">
          <option v-for="option in itemsPerPageOptions" :key="option" :value="option">
            {{ option }}
          </option>
        </select>
        <span class="text-xs text-gray-700">por página</span>
      </div>
      
      <!-- Info de paginación -->
      <div class="text-xs text-gray-700">
        Mostrando {{ paginationInfo.start }} a {{ paginationInfo.end }} de {{ paginationInfo.total }} {{ label }}
      </div>
    </div>
    
    <!-- Controles derecha -->
    <div class="flex items-center space-x-1">
      <!-- Primera página -->
      <button @click="$emit('update:currentPage', 1)" 
              :disabled="currentPage === 1"
              class="p-1.5 text-gray-500 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
              title="Primera página">
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"></path>
        </svg>
      </button>
      
      <!-- Anterior -->
      <button @click="$emit('update:currentPage', currentPage - 1)" 
              :disabled="currentPage === 1"
              class="p-1.5 text-gray-500 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
              title="Página anterior">
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
        </svg>
      </button>
      
      <!-- Números de página -->
      <div class="flex items-center space-x-1">
        <template v-for="page in totalPages" :key="page">
          <button v-if="shouldShowPage(page)"
                  @click="$emit('update:currentPage', page)"
                  :class="[
                    'px-2.5 py-1 text-xs font-medium rounded-lg transition-colors',
                    page === currentPage 
                      ? 'bg-blue-600 text-white border border-blue-600' 
                      : 'text-gray-500 bg-white border border-gray-300 hover:bg-gray-50'
                  ]">
            {{ page }}
          </button>
          <span v-else-if="shouldShowEllipsis(page)" class="px-1 text-gray-400 text-xs">...</span>
        </template>
      </div>
      
      <!-- Siguiente -->
      <button @click="$emit('update:currentPage', currentPage + 1)" 
              :disabled="currentPage === totalPages"
              class="p-1.5 text-gray-500 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
              title="Página siguiente">
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
        </svg>
      </button>
      
      <!-- Última página -->
      <button @click="$emit('update:currentPage', totalPages)" 
              :disabled="currentPage === totalPages"
              class="p-1.5 text-gray-500 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
              title="Última página">
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7"></path>
        </svg>
      </button>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  // Página actual (v-model:currentPage)
  currentPage: {
    type: Number,
    required: true,
    default: 1
  },
  // Total de páginas
  totalPages: {
    type: Number,
    required: true,
    default: 1
  },
  // Items por página (v-model:itemsPerPage)
  itemsPerPage: {
    type: Number,
    required: true,
    default: 10
  },
  // Total de items
  totalItems: {
    type: Number,
    required: true,
    default: 0
  },
  // Opciones para selector de items por página
  itemsPerPageOptions: {
    type: Array,
    default: () => [10, 25, 50, 100]
  },
  // Label para el texto (ej: "documentos", "productos", "clientes")
  label: {
    type: String,
    default: 'registros'
  }
});

defineEmits(['update:currentPage', 'update:itemsPerPage']);

// Información de paginación
const paginationInfo = computed(() => {
  const start = props.totalItems === 0 ? 0 : (props.currentPage - 1) * props.itemsPerPage + 1;
  const end = Math.min(props.currentPage * props.itemsPerPage, props.totalItems);
  return {
    start,
    end,
    total: props.totalItems
  };
});

// Lógica para mostrar números de página
const shouldShowPage = (page) => {
  // Siempre mostrar primera y última página
  if (page === 1 || page === props.totalPages) return true;
  // Mostrar páginas cercanas a la actual
  return Math.abs(page - props.currentPage) <= 2;
};

const shouldShowEllipsis = (page) => {
  // Mostrar "..." cuando hay salto de números
  return Math.abs(page - props.currentPage) === 3;
};
</script>
