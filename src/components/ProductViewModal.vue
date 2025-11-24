<template>
  <div class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50 p-4" @click.self="$emit('close')">
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl max-w-4xl w-full max-h-[90vh] overflow-y-auto animate-scale-in">
      
      <!-- Header -->
      <div class="p-6 border-b border-gray-200 dark:border-gray-700">
        <div class="flex items-center justify-between">
          <div class="flex items-center space-x-4">
            <div class="w-16 h-16 rounded-lg overflow-hidden border border-gray-200 dark:border-gray-600 bg-gray-100 dark:bg-gray-700">
              <img 
                v-if="product.image"
                :src="product.image" 
                :alt="product.name"
                class="w-full h-full object-cover"
              />
              <div v-else class="w-full h-full flex items-center justify-center">
                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
              </div>
            </div>
            <div>
              <h3 class="text-xl font-semibold text-gray-900 dark:text-white">{{ product.name }}</h3>
              <p class="text-sm text-gray-500 dark:text-gray-400">{{ product.barcode }}</p>
            </div>
          </div>
          
          <div class="flex items-center space-x-2">
            <button
              @click="$emit('edit', product)"
              class="px-3 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors"
            >
              <svg class="w-4 h-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
              </svg>
              Editar
            </button>
            
            <button @click="$emit('close')" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
              </svg>
            </button>
          </div>
        </div>
      </div>

      <!-- Contenido -->
      <div class="p-6">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
          
          <!-- Columna Principal: Información del Producto -->
          <div class="lg:col-span-2 space-y-6">
            
            <!-- Información General -->
            <div class="bg-gray-50 dark:bg-gray-900 rounded-xl p-6">
              <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Información General
              </h4>
              
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                  <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nombre</label>
                  <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ product.name }}</p>
                </div>
                
                <div>
                  <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Categoría</label>
                  <span class="mt-1 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium" 
                        :style="{ backgroundColor: product.category_color + '20', color: product.category_color }">
                    {{ product.category_name }}
                  </span>
                </div>
                
                <div>
                  <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Código de Barras</label>
                  <p class="mt-1 text-sm text-gray-900 dark:text-white font-mono">{{ product.barcode || 'No asignado' }}</p>
                </div>
                
                <div>
                  <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">SKU</label>
                  <p class="mt-1 text-sm text-gray-900 dark:text-white font-mono">{{ product.sku || 'No asignado' }}</p>
                </div>
                
                <div>
                  <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Unidad de Medida</label>
                  <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ product.unit || 'unidad' }}</p>
                </div>
                
                <div>
                  <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Estado</label>
                  <span :class="[
                    'mt-1 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium',
                    product.active 
                      ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400'
                      : 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400'
                  ]">
                    {{ product.active ? 'Activo' : 'Inactivo' }}
                  </span>
                </div>
              </div>
              
              <div v-if="product.description" class="mt-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Descripción</label>
                <p class="mt-1 text-sm text-gray-900 dark:text-white leading-relaxed">{{ product.description }}</p>
              </div>
            </div>

            <!-- Precios -->
            <div class="bg-gray-50 dark:bg-gray-900 rounded-xl p-6">
              <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                </svg>
                Información de Precios
              </h4>
              
              <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="text-center p-4 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
                  <div class="text-2xl font-bold text-gray-900 dark:text-white">
                    ${{ product.cost?.toLocaleString() || '0' }}
                  </div>
                  <div class="text-sm text-gray-500 dark:text-gray-400">Precio de Costo</div>
                </div>
                
                <div class="text-center p-4 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
                  <div class="text-2xl font-bold text-primary-600 dark:text-primary-400">
                    ${{ product.price.toLocaleString() }}
                  </div>
                  <div class="text-sm text-gray-500 dark:text-gray-400">Precio de Venta</div>
                </div>
                
                <div class="text-center p-4 bg-green-50 dark:bg-green-900/20 rounded-lg border border-green-200 dark:border-green-800">
                  <div class="text-2xl font-bold text-green-600 dark:text-green-400">
                    {{ marginPercentage }}%
                  </div>
                  <div class="text-sm text-green-600 dark:text-green-400">Margen de Ganancia</div>
                  <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                    ${{ profitAmount.toLocaleString() }}
                  </div>
                </div>
              </div>
            </div>

            <!-- Historial de Movimientos (simulado) -->
            <div class="bg-gray-50 dark:bg-gray-900 rounded-xl p-6">
              <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Movimientos Recientes
              </h4>
              
              <div class="space-y-3">
                <div v-for="movement in recentMovements" :key="movement.id" 
                     class="flex items-center justify-between p-3 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
                  <div class="flex items-center space-x-3">
                    <div :class="[
                      'w-8 h-8 rounded-full flex items-center justify-center',
                      movement.type === 'sale' ? 'bg-red-100 dark:bg-red-900/30' :
                      movement.type === 'purchase' ? 'bg-green-100 dark:bg-green-900/30' :
                      'bg-blue-100 dark:bg-blue-900/30'
                    ]">
                      <svg class="w-4 h-4" :class="[
                        movement.type === 'sale' ? 'text-red-600 dark:text-red-400' :
                        movement.type === 'purchase' ? 'text-green-600 dark:text-green-400' :
                        'text-blue-600 dark:text-blue-400'
                      ]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path v-if="movement.type === 'sale'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        <path v-else-if="movement.type === 'purchase'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                      </svg>
                    </div>
                    <div>
                      <div class="text-sm font-medium text-gray-900 dark:text-white">{{ movement.description }}</div>
                      <div class="text-xs text-gray-500 dark:text-gray-400">{{ movement.date }}</div>
                    </div>
                  </div>
                  <div class="text-right">
                    <div class="text-sm font-medium text-gray-900 dark:text-white">
                      {{ movement.type === 'sale' ? '-' : '+' }}{{ movement.quantity }}
                    </div>
                    <div class="text-xs text-gray-500 dark:text-gray-400">unidades</div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Columna Lateral: Estadísticas e Inventario -->
          <div class="space-y-6">
            
            <!-- Stock e Inventario -->
            <div class="bg-gray-50 dark:bg-gray-900 rounded-xl p-6">
              <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                </svg>
                Inventario
              </h4>
              
              <div class="space-y-4">
                <!-- Stock Actual -->
                <div class="text-center p-4 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
                  <div class="text-3xl font-bold text-gray-900 dark:text-white">{{ product.stock }}</div>
                  <div class="text-sm text-gray-500 dark:text-gray-400">Stock Actual</div>
                </div>
                
                <!-- Stock Mínimo -->
                <div class="flex items-center justify-between p-3 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
                  <div>
                    <div class="text-sm font-medium text-gray-900 dark:text-white">Stock Mínimo</div>
                    <div class="text-lg font-semibold text-orange-600 dark:text-orange-400">{{ product.min_stock }}</div>
                  </div>
                  <div v-if="product.stock <= product.min_stock" class="w-3 h-3 bg-red-500 rounded-full animate-pulse"></div>
                </div>
                
                <!-- Alerta de Stock -->
                <div v-if="product.stock <= product.min_stock" 
                     class="p-3 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg">
                  <div class="flex items-start space-x-3">
                    <svg class="w-5 h-5 text-red-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                    </svg>
                    <div>
                      <div class="text-sm font-medium text-red-800 dark:text-red-200">Stock Bajo</div>
                      <div class="text-xs text-red-600 dark:text-red-300 mt-1">
                        {{ product.stock === 0 ? 'Sin stock disponible' : 'Reabastecer pronto' }}
                      </div>
                    </div>
                  </div>
                </div>
                
                <!-- Valorización del Inventario -->
                <div class="p-3 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg">
                  <div class="text-sm font-medium text-blue-800 dark:text-blue-200 mb-2">Valor del Inventario</div>
                  <div class="space-y-1">
                    <div class="flex justify-between text-xs">
                      <span class="text-blue-600 dark:text-blue-300">Al costo:</span>
                      <span class="font-medium text-blue-800 dark:text-blue-200">
                        ${{ ((product.cost || 0) * product.stock).toLocaleString() }}
                      </span>
                    </div>
                    <div class="flex justify-between text-xs">
                      <span class="text-blue-600 dark:text-blue-300">Al precio venta:</span>
                      <span class="font-medium text-blue-800 dark:text-blue-200">
                        ${{ (product.price * product.stock).toLocaleString() }}
                      </span>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Estadísticas de Ventas -->
            <div class="bg-gray-50 dark:bg-gray-900 rounded-xl p-6">
              <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
                Estadísticas
              </h4>
              
              <div class="space-y-3">
                <div class="flex justify-between items-center p-3 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
                  <span class="text-sm text-gray-600 dark:text-gray-400">Ventas este mes</span>
                  <span class="font-semibold text-gray-900 dark:text-white">23 unidades</span>
                </div>
                
                <div class="flex justify-between items-center p-3 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
                  <span class="text-sm text-gray-600 dark:text-gray-400">Ingresos generados</span>
                  <span class="font-semibold text-green-600 dark:text-green-400">${{ (product.price * 23).toLocaleString() }}</span>
                </div>
                
                <div class="flex justify-between items-center p-3 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
                  <span class="text-sm text-gray-600 dark:text-gray-400">Rotación promedio</span>
                  <span class="font-semibold text-gray-900 dark:text-white">5.2 días</span>
                </div>
                
                <div class="flex justify-between items-center p-3 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
                  <span class="text-sm text-gray-600 dark:text-gray-400">Fecha creación</span>
                  <span class="font-semibold text-gray-900 dark:text-white">
                    {{ formatDate(product.created_at) }}
                  </span>
                </div>
              </div>
            </div>

            <!-- Acciones Rápidas -->
            <div class="bg-gray-50 dark:bg-gray-900 rounded-xl p-6">
              <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Acciones Rápidas</h4>
              
              <div class="space-y-2">
                <button class="w-full flex items-center space-x-2 px-3 py-2 bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 rounded-lg hover:bg-blue-200 dark:hover:bg-blue-900/50 transition-colors">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                  </svg>
                  <span class="text-sm">Ajustar Stock</span>
                </button>
                
                <button class="w-full flex items-center space-x-2 px-3 py-2 bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300 rounded-lg hover:bg-green-200 dark:hover:bg-green-900/50 transition-colors">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                  </svg>
                  <span class="text-sm">Actualizar Precio</span>
                </button>
                
                <button class="w-full flex items-center space-x-2 px-3 py-2 bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300 rounded-lg hover:bg-purple-200 dark:hover:bg-purple-900/50 transition-colors">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                  </svg>
                  <span class="text-sm">Generar Reporte</span>
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'

// Props
const props = defineProps({
  product: {
    type: Object,
    required: true
  }
})

// Emits
defineEmits(['edit', 'close'])

// Computed
const marginPercentage = computed(() => {
  if (!props.product.cost || props.product.cost === 0) return 0
  return ((props.product.price - props.product.cost) / props.product.price * 100).toFixed(1)
})

const profitAmount = computed(() => {
  if (!props.product.cost) return props.product.price
  return props.product.price - props.product.cost
})

// Datos simulados para movimientos
const recentMovements = [
  {
    id: 1,
    type: 'sale',
    description: 'Venta #1234',
    quantity: 2,
    date: '2024-01-15 14:30'
  },
  {
    id: 2,
    type: 'purchase',
    description: 'Compra a proveedor',
    quantity: 50,
    date: '2024-01-10 09:15'
  },
  {
    id: 3,
    type: 'adjustment',
    description: 'Ajuste de inventario',
    quantity: -1,
    date: '2024-01-08 16:45'
  }
]

// Métodos
const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString('es-ES', {
    year: 'numeric',
    month: 'short',
    day: 'numeric'
  })
}
</script>

<style scoped>
@keyframes scaleIn {
  from {
    opacity: 0;
    transform: scale(0.95);
  }
  to {
    opacity: 1;
    transform: scale(1);
  }
}

.animate-scale-in {
  animation: scaleIn 0.2s ease-out;
}
</style>