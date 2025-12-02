<template>
  <div class="min-h-screen font-sans mx-8" style="background-color: #F4F6F8;">
    <div class="p-4 lg:p-6 space-y-6 pb-8 animate-fade-in">
      
      <!-- Header Elegante -->
      <div class="flex items-center justify-between pb-4 border-b border-gray-300">
        <div class="flex items-center space-x-4">
          <div class="w-12 h-12 bg-gradient-to-br from-blue-600 to-indigo-600 rounded-xl flex items-center justify-center shadow-lg">
            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
            </svg>
          </div>
          <div>
            <h1 class="text-2xl font-bold text-gray-900">Gestión de Sedes</h1>
            <p class="text-sm text-gray-600">Administra las tiendas y bodegas del negocio</p>
          </div>
        </div>
        
        <div class="flex items-center space-x-3">
          <button 
            @click="fetchWarehouses"
            class="px-4 py-2.5 bg-slate-50 hover:bg-slate-100 text-slate-600 text-sm font-semibold rounded-xl border border-slate-200 shadow-sm transition-all duration-200 flex items-center space-x-2 hover:shadow-md hover:border-slate-300">
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
            <span>Nueva Sede</span>
          </button>
        </div>
      </div>

      <!-- Métricas Principales -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Total de Sedes -->
        <div class="bg-white rounded-2xl p-5 border border-gray-300 hover:border-gray-400 transition-all duration-200 hover:shadow-lg">
          <div class="flex items-center space-x-4">
            <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center flex-shrink-0">
              <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
              </svg>
            </div>
            <div class="flex-1 min-w-0">
              <div class="flex items-center justify-between mb-2">
                <h3 class="text-sm font-semibold text-gray-700">Total Sedes</h3>
              </div>
              <p class="text-2xl font-bold text-gray-900 mb-1">{{ warehouses.length }}</p>
              <p class="text-sm text-gray-500">{{ activeWarehouses }} activas</p>
            </div>
          </div>
        </div>

        <!-- Sede Principal -->
        <div class="bg-white rounded-2xl p-5 border border-gray-300 hover:border-gray-400 transition-all duration-200 hover:shadow-lg">
          <div class="flex items-center space-x-4">
            <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center flex-shrink-0">
              <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
              </svg>
            </div>
            <div class="flex-1 min-w-0">
              <div class="flex items-center justify-between mb-2">
                <h3 class="text-sm font-semibold text-gray-700">Sede Principal</h3>
                <span class="text-xs font-medium text-green-700 bg-green-50 px-2 py-1 rounded-lg border border-green-200">
                  Predeterminada
                </span>
              </div>
              <p class="text-lg font-bold text-gray-900 truncate">{{ defaultWarehouse?.name || '-' }}</p>
            </div>
          </div>
        </div>

        <!-- Stock Total -->
        <div class="bg-white rounded-2xl p-5 border border-gray-300 hover:border-gray-400 transition-all duration-200 hover:shadow-lg">
          <div class="flex items-center space-x-4">
            <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center flex-shrink-0">
              <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
              </svg>
            </div>
            <div class="flex-1 min-w-0">
              <div class="flex items-center justify-between mb-2">
                <h3 class="text-sm font-semibold text-gray-700">Productos Totales</h3>
              </div>
              <p class="text-2xl font-bold text-gray-900 mb-1">{{ totalProducts }}</p>
              <p class="text-sm text-gray-500">En todas las sedes</p>
            </div>
          </div>
        </div>

        <!-- Inventario Global -->
        <div class="bg-white rounded-2xl p-5 border border-gray-300 hover:border-gray-400 transition-all duration-200 hover:shadow-lg">
          <div class="flex items-center space-x-4">
            <div class="w-12 h-12 bg-amber-100 rounded-xl flex items-center justify-center flex-shrink-0">
              <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
              </svg>
            </div>
            <div class="flex-1 min-w-0">
              <div class="flex items-center justify-between mb-2">
                <h3 class="text-sm font-semibold text-gray-700">Stock Global</h3>
              </div>
              <p class="text-2xl font-bold text-gray-900 mb-1">{{ totalStock }}</p>
              <p class="text-sm text-gray-500">Unidades totales</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Lista de Sedes -->
      <div class="bg-white rounded-2xl p-5 border border-gray-300">
        <div class="bg-gray-50 border-b border-gray-200 px-5 py-4 flex items-center justify-between">
          <div>
            <h2 class="text-base font-bold text-gray-900">Listado de Sedes</h2>
            <p class="text-xs text-gray-500 mt-0.5">{{ warehouses.length }} sedes registradas</p>
          </div>
        </div>

        <div v-if="loading" class="py-12 text-center">
          <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
          <p class="mt-2 text-sm text-gray-600">Cargando sedes...</p>
        </div>

        <div v-else-if="warehouses.length === 0" class="py-12 text-center">
          <div class="flex flex-col items-center space-y-3">
            <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center">
              <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
              </svg>
            </div>
            <div>
              <p class="text-sm font-semibold text-gray-700">No hay sedes registradas</p>
              <p class="text-xs text-gray-500 mt-1">Crea tu primera sede para comenzar</p>
            </div>
            <button 
              @click="openCreateModal"
              class="px-3 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg">
              Nueva Sede
            </button>
          </div>
        </div>

        <div v-else class="divide-y divide-gray-200">
          <div 
            v-for="warehouse in warehouses" 
            :key="warehouse.id"
            class="p-4 hover:bg-gray-50 transition-colors">
            <div class="flex items-center justify-between">
              <div class="flex items-center space-x-4 flex-1">
                <div :class="[
                  'w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0',
                  warehouse.is_default ? 'bg-green-100' : 'bg-blue-100'
                ]">
                  <svg class="w-6 h-6" :class="warehouse.is_default ? 'text-green-600' : 'text-blue-600'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                  </svg>
                </div>

                <div class="flex-1 min-w-0">
                  <div class="flex items-center space-x-2 mb-1">
                    <h3 class="text-base font-bold text-gray-900">{{ warehouse.name }}</h3>
                    <span v-if="warehouse.is_default" class="px-2 py-0.5 bg-green-100 text-green-700 text-xs font-semibold rounded-full">
                      Principal
                    </span>
                    <span :class="[
                      'px-2 py-0.5 text-xs font-semibold rounded-full',
                      warehouse.active ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-700'
                    ]">
                      {{ warehouse.active ? 'Activa' : 'Inactiva' }}
                    </span>
                  </div>
                  <div class="flex items-center space-x-4 text-xs text-gray-500">
                    <span v-if="warehouse.address" class="flex items-center space-x-1">
                      <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                      </svg>
                      <span>{{ warehouse.address }}</span>
                    </span>
                    <span v-if="warehouse.phone" class="flex items-center space-x-1">
                      <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                      </svg>
                      <span>{{ warehouse.phone }}</span>
                    </span>
                    <span class="flex items-center space-x-1">
                      <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                      </svg>
                      <span>{{ warehouse.products_count || 0 }} productos</span>
                    </span>
                  </div>
                </div>
              </div>

              <div class="flex items-center space-x-2">
                <button 
                  @click="viewInventory(warehouse)"
                  class="p-2 bg-blue-50 hover:bg-blue-100 text-blue-600 rounded-lg transition-colors"
                  title="Ver inventario">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                  </svg>
                </button>
                <button 
                  @click="editWarehouse(warehouse)"
                  class="p-2 bg-amber-50 hover:bg-amber-100 text-amber-600 rounded-lg transition-colors"
                  title="Editar">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                  </svg>
                </button>
                <button 
                  v-if="!warehouse.is_default"
                  @click="deleteWarehouse(warehouse)"
                  class="p-2 bg-red-50 hover:bg-red-100 text-red-600 rounded-lg transition-colors"
                  title="Eliminar">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                  </svg>
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>

    </div>

    <!-- Modal Crear/Editar Sede -->
    <WarehouseModal
      v-if="showModal"
      :warehouse="selectedWarehouse"
      @close="closeModal"
      @saved="handleSaved"
    />
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { warehouseService } from '@/services/warehouseService';
import WarehouseModal from '@/components/warehouses/WarehouseModal.vue';
const warehouses = ref([]);
const loading = ref(false);
const showModal = ref(false);
const selectedWarehouse = ref(null);

const activeWarehouses = computed(() => {
  return warehouses.value.filter(w => w.active).length;
});

const defaultWarehouse = computed(() => {
  return warehouses.value.find(w => w.is_default);
});

const totalProducts = computed(() => {
  return warehouses.value.reduce((sum, w) => sum + (w.products_count || 0), 0);
});

const totalStock = computed(() => {
  return warehouses.value.reduce((sum, w) => {
    const warehouseStock = w.products?.reduce((s, p) => s + (p.pivot?.stock || 0), 0) || 0;
    return sum + warehouseStock;
  }, 0);
});

const fetchWarehouses = async () => {
  console.log('🔄 fetchWarehouses iniciado...');
  loading.value = true;
  try {
    const data = await warehouseService.getAll();
    // El servicio ya retorna el array directamente
    warehouses.value = Array.isArray(data) ? data : [];
    console.log('✅ Sedes cargadas:', warehouses.value.length, 'sedes');
  } catch (error) {
    console.error('❌ Error al cargar sedes:', error);
    warehouses.value = [];
    alert('Error al cargar las sedes');
  } finally {
    loading.value = false;
  }
};

const openCreateModal = () => {
  selectedWarehouse.value = null;
  showModal.value = true;
};

const closeModal = () => {
  showModal.value = false;
  selectedWarehouse.value = null;
};

const editWarehouse = (warehouse) => {
  selectedWarehouse.value = warehouse;
  showModal.value = true;
};

const viewInventory = async (warehouse) => {
  try {
    const data = await warehouseService.getInventory(warehouse.id);
    const productsList = data.products || [];
    const totalStock = productsList.reduce((sum, p) => sum + (p.stock || 0), 0);
    alert(`📦 Inventario de ${warehouse.name}\n\n` +
          `Total de productos: ${productsList.length}\n` +
          `Stock total: ${totalStock} unidades\n\n` +
          `(Vista detallada en desarrollo)`);
  } catch (error) {
    console.error('Error al cargar inventario:', error);
    alert('Error al cargar el inventario');
  }
};

const deleteWarehouse = async (warehouse) => {
  if (!confirm(`¿Eliminar la sede "${warehouse.name}"?`)) return;
  
  try {
    await warehouseService.delete(warehouse.id);
    await fetchWarehouses();
    alert('Sede eliminada exitosamente');
  } catch (error) {
    console.error('Error al eliminar sede:', error);
    alert(error.response?.data?.message || 'Error al eliminar la sede');
  }
};

const handleSaved = () => {
  console.log('✅ handleSaved ejecutado - cerrando modal y recargando...');
  closeModal();
  // Pequeño delay para asegurar que la BD se actualice
  setTimeout(() => {
    fetchWarehouses();
  }, 100);
};

onMounted(() => {
  fetchWarehouses();
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
