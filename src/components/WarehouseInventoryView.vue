<template>
  <div class="min-h-screen font-sans mx-8" style="background-color: #F4F6F8;">
    <div class="p-4 lg:p-6 space-y-6 pb-8 animate-fade-in">
      
      <!-- Header -->
      <div class="flex items-center justify-between pb-4 border-b border-gray-300">
        <div class="flex items-center space-x-4">
          <div class="w-12 h-12 bg-gradient-to-br from-blue-600 to-indigo-600 rounded-xl flex items-center justify-center shadow-lg">
            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
            </svg>
          </div>
          <div>
            <h1 class="text-2xl font-bold text-gray-900">{{ warehouse?.name }}</h1>
            <p class="text-sm text-gray-600">Inventario de la sede</p>
          </div>
        </div>
        
        <div class="flex items-center space-x-3">
          <button 
            @click="loadInventory"
            class="px-4 py-2.5 bg-slate-50 hover:bg-slate-100 text-slate-600 text-sm font-semibold rounded-xl border border-slate-200 shadow-sm transition-all duration-200 flex items-center space-x-2 hover:shadow-md hover:border-slate-300">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
            </svg>
            <span>Actualizar</span>
          </button>
        </div>
      </div>

      <!-- Métricas -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        
        <!-- Total Productos -->
        <div class="bg-white rounded-2xl p-5 border border-gray-300 hover:border-gray-400 transition-all duration-200 hover:shadow-lg">
          <div class="flex items-center space-x-4">
            <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center flex-shrink-0">
              <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
              </svg>
            </div>
            <div class="flex-1 min-w-0">
              <p class="text-sm font-semibold text-gray-700">Total Productos</p>
              <p class="text-2xl font-bold text-gray-900">{{ inventory.length }}</p>
            </div>
          </div>
        </div>

        <!-- Stock Total -->
        <div class="bg-white rounded-2xl p-5 border border-gray-300 hover:border-gray-400 transition-all duration-200 hover:shadow-lg">
          <div class="flex items-center space-x-4">
            <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center flex-shrink-0">
              <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
              </svg>
            </div>
            <div class="flex-1 min-w-0">
              <p class="text-sm font-semibold text-gray-700">Stock Total</p>
              <p class="text-2xl font-bold text-gray-900">{{ totalStock }} unidades</p>
            </div>
          </div>
        </div>

        <!-- Stock Bajo -->
        <div class="bg-white rounded-2xl p-5 border border-gray-300 hover:border-gray-400 transition-all duration-200 hover:shadow-lg">
          <div class="flex items-center space-x-4">
            <div class="w-12 h-12 bg-yellow-100 rounded-xl flex items-center justify-center flex-shrink-0">
              <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
              </svg>
            </div>
            <div class="flex-1 min-w-0">
              <p class="text-sm font-semibold text-gray-700">Stock Bajo</p>
              <p class="text-2xl font-bold text-gray-900">{{ lowStockCount }}</p>
            </div>
          </div>
        </div>

        <!-- Sin Stock -->
        <div class="bg-white rounded-2xl p-5 border border-gray-300 hover:border-gray-400 transition-all duration-200 hover:shadow-lg">
          <div class="flex items-center space-x-4">
            <div class="w-12 h-12 bg-red-100 rounded-xl flex items-center justify-center flex-shrink-0">
              <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
              </svg>
            </div>
            <div class="flex-1 min-w-0">
              <p class="text-sm font-semibold text-gray-700">Sin Stock</p>
              <p class="text-2xl font-bold text-gray-900">{{ noStockCount }}</p>
            </div>
          </div>
        </div>

      </div>

      <!-- Filtros -->
      <div class="bg-white rounded-lg shadow-sm p-3 border border-gray-200">
        <div class="flex flex-wrap items-center gap-3">
          <!-- Búsqueda -->
          <div class="flex-1 min-w-48 relative">
            <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
            <input 
              v-model="filters.search"
              type="text" 
              placeholder="Buscar producto..."
              class="w-full pl-9 pr-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
          </div>
          
          <!-- Filtro Stock -->
          <select 
            v-model="filters.stockStatus"
            class="pl-3 pr-8 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 min-w-36">
            <option value="">Todos</option>
            <option value="available">Con Stock</option>
            <option value="low">Stock Bajo</option>
            <option value="none">Sin Stock</option>
          </select>
          
          <!-- Limpiar -->
          <button 
            @click="clearFilters"
            class="p-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition-colors"
            title="Limpiar filtros">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
            </svg>
          </button>
        </div>
      </div>

      <!-- Tabla de Inventario -->
      <div class="bg-white rounded-2xl border border-gray-300 overflow-hidden">
        <div class="bg-gray-50 border-b border-gray-200 px-5 py-4">
          <h2 class="text-base font-bold text-gray-900">Inventario</h2>
          <p class="text-xs text-gray-500 mt-0.5">{{ filteredInventory.length }} productos</p>
        </div>
        
        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wide">
                  Producto
                </th>
                <th class="px-4 py-3 text-center text-xs font-bold text-gray-600 uppercase tracking-wide">
                  Código
                </th>
                <th class="px-4 py-3 text-center text-xs font-bold text-gray-600 uppercase tracking-wide">
                  Stock
                </th>
                <th class="px-4 py-3 text-center text-xs font-bold text-gray-600 uppercase tracking-wide">
                  Estado
                </th>
                <th class="px-4 py-3 text-center text-xs font-bold text-gray-600 uppercase tracking-wide">
                  Precio
                </th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-if="loading">
                <td colspan="5" class="px-4 py-12 text-center">
                  <div class="flex flex-col items-center space-y-3">
                    <div class="w-8 h-8 border-4 border-blue-600 border-t-transparent rounded-full animate-spin"></div>
                    <p class="text-sm text-gray-500">Cargando inventario...</p>
                  </div>
                </td>
              </tr>

              <tr v-else-if="filteredInventory.length === 0">
                <td colspan="5" class="px-4 py-12 text-center">
                  <div class="flex flex-col items-center space-y-3">
                    <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center">
                      <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                      </svg>
                    </div>
                    <div>
                      <p class="text-sm font-semibold text-gray-700">No hay productos</p>
                      <p class="text-xs text-gray-500 mt-1">No se encontraron productos con los filtros aplicados</p>
                    </div>
                  </div>
                </td>
              </tr>

              <tr 
                v-else
                v-for="item in paginatedInventory" 
                :key="item.id"
                class="hover:bg-gray-50 transition-colors">
                <td class="px-4 py-3">
                  <p class="text-sm font-semibold text-gray-900">{{ item.name }}</p>
                  <p v-if="item.category" class="text-xs text-gray-500">{{ item.category.name }}</p>
                </td>
                <td class="px-4 py-3 text-center">
                  <span class="text-xs font-mono text-gray-600">{{ item.code || '-' }}</span>
                </td>
                <td class="px-4 py-3 text-center">
                  <span :class="[
                    'text-sm font-bold',
                    item.stock === 0 ? 'text-red-600' :
                    item.stock <= (item.minimum_stock || 5) ? 'text-yellow-600' :
                    'text-green-600'
                  ]">
                    {{ item.stock }}
                  </span>
                </td>
                <td class="px-4 py-3 text-center">
                  <span :class="[
                    'px-2 py-1 rounded-full text-xs font-semibold',
                    item.stock === 0 ? 'bg-red-100 text-red-700' :
                    item.stock <= (item.minimum_stock || 5) ? 'bg-yellow-100 text-yellow-700' :
                    'bg-green-100 text-green-700'
                  ]">
                    {{ getStockStatus(item) }}
                  </span>
                </td>
                <td class="px-4 py-3 text-center">
                  <span class="text-sm font-semibold text-gray-900">${{ formatPrice(item.price) }}</span>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Paginador -->
        <div class="bg-white border-t border-gray-200 px-4 py-3 flex items-center justify-between">
          <div class="flex items-center space-x-3">
            <div class="flex items-center space-x-2">
              <span class="text-xs font-medium text-gray-700">Mostrar:</span>
              <select 
                v-model="itemsPerPage" 
                @change="currentPage = 1"
                class="border border-gray-300 rounded-lg px-2 py-1 text-xs focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="10">10</option>
                <option value="25">25</option>
                <option value="50">50</option>
                <option value="100">100</option>
              </select>
              <span class="text-xs text-gray-700">por página</span>
            </div>
            <div class="text-xs text-gray-700">
              Mostrando {{ start }} a {{ end }} de {{ filteredInventory.length }} productos
            </div>
          </div>
          
          <div class="flex items-center space-x-1">
            <button 
              @click="currentPage = 1" 
              :disabled="currentPage === 1"
              class="p-1.5 text-gray-500 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed">
              <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"></path>
              </svg>
            </button>
            
            <button 
              @click="currentPage--" 
              :disabled="currentPage === 1"
              class="p-1.5 text-gray-500 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed">
              <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
              </svg>
            </button>
            
            <span class="px-3 py-1 text-xs font-medium text-gray-700 bg-blue-50 border border-blue-200 rounded-lg">
              {{ currentPage }} / {{ totalPages }}
            </span>
            
            <button 
              @click="currentPage++" 
              :disabled="currentPage === totalPages"
              class="p-1.5 text-gray-500 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed">
              <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
              </svg>
            </button>
            
            <button 
              @click="currentPage = totalPages" 
              :disabled="currentPage === totalPages"
              class="p-1.5 text-gray-500 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed">
              <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7"></path>
              </svg>
            </button>
          </div>
        </div>

      </div>

    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useRoute } from 'vue-router';
import { warehouseService } from '@/services/warehouseService';

const route = useRoute();
const loading = ref(false);
const warehouse = ref(null);
const inventory = ref([]);

const filters = ref({
  search: '',
  stockStatus: ''
});

const currentPage = ref(1);
const itemsPerPage = ref(25);

onMounted(() => {
  loadInventory();
});

const loadInventory = async () => {
  loading.value = true;
  try {
    const warehouseId = route.params.id;
    const data = await warehouseService.getInventory(warehouseId);
    warehouse.value = data.warehouse;
    inventory.value = data.products || [];
  } catch (error) {
    console.error('Error al cargar inventario:', error);
    alert('Error al cargar el inventario');
  } finally {
    loading.value = false;
  }
};

const totalStock = computed(() => {
  return inventory.value.reduce((sum, item) => sum + (item.stock || 0), 0);
});

const lowStockCount = computed(() => {
  return inventory.value.filter(item => 
    item.stock > 0 && item.stock <= (item.minimum_stock || 5)
  ).length;
});

const noStockCount = computed(() => {
  return inventory.value.filter(item => item.stock === 0).length;
});

const filteredInventory = computed(() => {
  let result = inventory.value;

  // Búsqueda
  if (filters.value.search) {
    const search = filters.value.search.toLowerCase();
    result = result.filter(item =>
      item.name?.toLowerCase().includes(search) ||
      item.code?.toLowerCase().includes(search)
    );
  }

  // Filtro stock
  if (filters.value.stockStatus === 'available') {
    result = result.filter(item => item.stock > (item.minimum_stock || 5));
  } else if (filters.value.stockStatus === 'low') {
    result = result.filter(item => 
      item.stock > 0 && item.stock <= (item.minimum_stock || 5)
    );
  } else if (filters.value.stockStatus === 'none') {
    result = result.filter(item => item.stock === 0);
  }

  return result;
});

const totalPages = computed(() => {
  return Math.ceil(filteredInventory.value.length / itemsPerPage.value) || 1;
});

const start = computed(() => {
  return (currentPage.value - 1) * itemsPerPage.value + 1;
});

const end = computed(() => {
  return Math.min(currentPage.value * itemsPerPage.value, filteredInventory.value.length);
});

const paginatedInventory = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage.value;
  return filteredInventory.value.slice(start, start + itemsPerPage.value);
});

const clearFilters = () => {
  filters.value.search = '';
  filters.value.stockStatus = '';
  currentPage.value = 1;
};

const getStockStatus = (item) => {
  if (item.stock === 0) return 'Sin Stock';
  if (item.stock <= (item.minimum_stock || 5)) return 'Stock Bajo';
  return 'Disponible';
};

const formatPrice = (price) => {
  return new Intl.NumberFormat('es-CO').format(price);
};
</script>
