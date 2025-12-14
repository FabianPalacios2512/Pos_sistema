<template>
  <div v-if="product" class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center p-4 z-50">
    <div class="bg-white rounded-3xl shadow-2xl w-full max-w-6xl max-h-[90vh] overflow-hidden">
      
      <!-- Header del Modal -->
      <div class="p-6 border-b border-gray-200 bg-gradient-to-r from-blue-50 to-indigo-50">
        <div class="flex items-center justify-between">
          <div class="flex items-center space-x-4">
            <div class="w-16 h-16 rounded-2xl overflow-hidden shadow-lg bg-gray-100">
              <img 
                :src="getProductImage(product)" 
                :alt="product.name || 'Producto'"
                class="w-full h-full object-cover"
                @error="handleImageError"
              >
            </div>
            <div>
              <h2 class="text-2xl font-bold text-gray-900">{{ product.name || 'Sin nombre' }}</h2>
              <p class="text-gray-600">{{ product.category?.name || 'Sin categoría' }}</p>
              <p class="text-sm text-gray-500">Código: {{ product.code || 'N/A' }}</p>
            </div>
          </div>
          
          <button 
            @click="$emit('close')"
            class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-xl transition-colors"
          >
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
          </button>
        </div>
      </div>

      <!-- Contenido del Modal -->
      <div class="flex flex-col lg:flex-row h-full">
        
        <!-- Panel Izquierdo - Información del Producto -->
        <div class="lg:w-1/3 p-6 border-r border-gray-200 bg-gray-50">
          
          <!-- Estadísticas Principales -->
          <div class="space-y-6">
            
            <!-- Stock Actual -->
            <div class="bg-white rounded-2xl p-4 shadow-sm border">
              <div class="flex items-center justify-between mb-3">
                <h3 class="font-semibold text-gray-700">Stock Actual</h3>
                <div :class="[
                  'inline-flex items-center px-3 py-1 rounded-full text-sm font-bold',
                  getStockStatusClass(product.current_stock)
                ]">
                  {{ getStockStatus(product.current_stock) }}
                </div>
              </div>
              <div class="text-center">
                <p class="text-3xl font-bold text-gray-900">{{ product.current_stock || 0 }}</p>
                <p class="text-sm text-gray-500">unidades disponibles</p>
              </div>
              
              <!-- Barra de Stock -->
              <div class="mt-4">
                <div class="w-full bg-gray-200 rounded-full h-2">
                  <div 
                    :class="[
                      'h-2 rounded-full transition-all duration-500',
                      getStockBarClass(product.current_stock)
                    ]"
                    :style="{ width: `${Math.min((product.current_stock / 50) * 100, 100)}%` }"
                  ></div>
                </div>
                <div class="flex justify-between text-xs text-gray-500 mt-1">
                  <span>0</span>
                  <span>Stock óptimo: 50+</span>
                </div>
              </div>

              <!-- Botón de Actualizar Stock -->
              <button 
                @click="showStockUpdate = true"
                class="w-full mt-4 py-2 px-4 bg-blue-500 hover:bg-blue-600 text-white font-semibold rounded-xl transition-colors"
              >
                Actualizar Stock
              </button>
            </div>

            <!-- Información de Precios -->
            <div class="bg-white rounded-2xl p-4 shadow-sm border">
              <h3 class="font-semibold text-gray-700 mb-3">Información de Precios</h3>
              
              <div class="space-y-3">
                <div class="flex justify-between">
                  <span class="text-gray-600">Precio de Venta:</span>
                  <span class="font-bold text-green-600">${{ formatCurrency(product.price) }}</span>
                </div>
                
                <div class="flex justify-between">
                  <span class="text-gray-600">Precio de Costo:</span>
                  <span class="font-semibold text-gray-900">${{ formatCurrency(product.cost_price || 0) }}</span>
                </div>
                
                <div class="flex justify-between border-t pt-2">
                  <span class="text-gray-600">Margen de Ganancia:</span>
                  <span class="font-bold text-blue-600">{{ getMarginPercentage(product) }}%</span>
                </div>
                
                <div class="flex justify-between">
                  <span class="text-gray-600">Ganancia por Unidad:</span>
                  <span class="font-bold text-emerald-600">${{ formatCurrency(getUnitProfit(product)) }}</span>
                </div>
              </div>
            </div>

            <!-- Estadísticas de Ventas -->
            <div class="bg-white rounded-2xl p-4 shadow-sm border">
              <h3 class="font-semibold text-gray-700 mb-3">Estadísticas de Ventas</h3>
              
              <div class="space-y-3">
                <div class="text-center p-3 bg-blue-50 rounded-xl">
                  <p class="text-2xl font-bold text-blue-600">{{ totalUnitsSold }}</p>
                  <p class="text-sm text-gray-600">Total vendidas</p>
                </div>
                
                <div class="text-center p-3 bg-emerald-50 rounded-xl">
                  <p class="text-2xl font-bold text-emerald-600">${{ formatCurrency(totalRevenue) }}</p>
                  <p class="text-sm text-gray-600">Ingresos totales</p>
                </div>
                
                <div class="text-center p-3 bg-purple-50 rounded-xl">
                  <p class="text-2xl font-bold text-purple-600">{{ salesSummary.totalSales }}</p>
                  <p class="text-sm text-gray-600">Transacciones</p>
                </div>
              </div>
            </div>

          </div>
        </div>

        <!-- Panel Derecho - Historial de Ventas -->
        <div class="lg:w-2/3 flex flex-col">
          
          <!-- Header del Historial -->
          <div class="p-6 border-b border-gray-200">
            <div class="flex items-center justify-between">
              <h3 class="text-xl font-bold text-gray-800">Historial de Ventas Detallado</h3>
              
              <!-- Filtros de Fecha -->
              <div class="flex items-center space-x-3">
                <select v-model="periodFilter" class="px-3 py-2 bg-gray-100 border border-gray-200 rounded-lg text-sm">
                  <option value="all">Todo el historial</option>
                  <option value="today">Hoy</option>
                  <option value="week">Última semana</option>
                  <option value="month">Último mes</option>
                </select>
                
                <button 
                  @click="exportSalesHistory"
                  class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium rounded-lg transition-colors flex items-center space-x-2"
                >
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                  </svg>
                  <span>Exportar</span>
                </button>
              </div>
            </div>
          </div>

          <!-- Lista del Historial -->
          <div class="flex-1 overflow-y-auto p-6">
            <!-- Estado de carga -->
            <div v-if="isLoadingSales" class="text-center py-12">
              <div class="animate-spin rounded-full h-16 w-16 border-b-2 border-blue-600 mx-auto mb-4"></div>
              <p class="text-gray-500">Cargando historial de ventas...</p>
            </div>
            
            <!-- Sin datos -->
            <div v-else-if="filteredSalesHistory.length === 0" class="text-center py-12">
              <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
              </svg>
              <p class="text-gray-500">No hay ventas registradas para este producto</p>
              <p class="text-gray-400 text-sm mt-2">Los datos se cargan en tiempo real desde la base de datos</p>
            </div>

            <div v-else class="space-y-4">
              <div 
                v-for="sale in filteredSalesHistory" 
                :key="sale.id"
                class="bg-white border border-gray-200 rounded-2xl p-4 hover:shadow-lg transition-shadow duration-200"
              >
                <!-- Header de la Venta -->
                <div class="flex items-center justify-between mb-3">
                  <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center">
                      <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                      </svg>
                    </div>
                    <div>
                      <h4 class="font-semibold text-gray-900">{{ sale.number || `Venta #${sale.id}` }}</h4>
                      <p class="text-sm text-gray-500">{{ sale.customer_name || 'Cliente General' }}</p>
                    </div>
                  </div>
                  
                  <div class="text-right">
                    <p class="text-sm text-gray-500">{{ formatDateTime(sale.date || sale.created_at) }}</p>
                    <span :class="[
                      'inline-flex items-center px-2 py-1 rounded-full text-xs font-medium',
                      sale.status === 'paid' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'
                    ]">
                      {{ sale.status === 'paid' ? 'Pagada' : 'Pendiente' }}
                    </span>
                  </div>
                </div>

                <!-- Detalles de la Venta del Producto -->
                <div class="bg-gray-50 rounded-xl p-3">
                  <div class="grid grid-cols-3 gap-4 text-center">
                    <div>
                      <p class="text-lg font-bold text-blue-600">{{ sale.productQuantity }}</p>
                      <p class="text-xs text-gray-500">Cantidad vendida</p>
                    </div>
                    
                    <div>
                      <p class="text-lg font-bold text-green-600">${{ formatCurrency(sale.productTotal) }}</p>
                      <p class="text-xs text-gray-500">Subtotal producto</p>
                    </div>
                    
                    <div>
                      <p class="text-lg font-bold text-purple-600">${{ formatCurrency(parseFloat(sale.total_amount || 0)) }}</p>
                      <p class="text-xs text-gray-500">Total factura</p>
                    </div>
                  </div>
                </div>

                <!-- Información Adicional -->
                <div class="mt-3 pt-3 border-t border-gray-200">
                  <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                      <span class="text-gray-500">Método de pago:</span>
                      <span class="ml-1 font-medium">{{ getPaymentMethodLabel(sale.payment_method) }}</span>
                    </div>
                    
                    <div>
                      <span class="text-gray-500">Ganancia estimada:</span>
                      <span class="ml-1 font-medium text-emerald-600">
                        ${{ formatCurrency(sale.productQuantity * getUnitProfit(product)) }}
                      </span>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

        </div>
      </div>

      <!-- Modal de Actualización de Stock -->
      <div v-if="showStockUpdate" class="absolute inset-0 bg-black/50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl p-6 w-full max-w-md">
          <h3 class="text-xl font-bold text-gray-900 mb-4">Actualizar Stock</h3>
          
          <div class="space-y-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Stock Actual</label>
              <input 
                type="number" 
                v-model="newStockValue"
                :placeholder="product.current_stock"
                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500"
              >
            </div>
            
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Motivo del Cambio</label>
              <select v-model="stockUpdateReason" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">Seleccionar motivo</option>
                <option value="Reposición de inventario">Reposición de inventario</option>
                <option value="Corrección de inventario">Corrección de inventario</option>
                <option value="Producto dañado">Producto dañado</option>
                <option value="Pérdida/Robo">Pérdida/Robo</option>
                <option value="Devolución">Devolución</option>
                <option value="Ajuste manual de inventario">Ajuste manual de inventario</option>
                <option value="Otro">Otro</option>
              </select>
            </div>
            
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Notas (opcional)</label>
              <textarea 
                v-model="stockUpdateNotes"
                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500"
                rows="3"
                placeholder="Notas adicionales sobre este cambio..."
              ></textarea>
            </div>
          </div>
          
          <div class="flex items-center justify-end space-x-3 mt-6">
            <button 
              @click="showStockUpdate = false"
              class="px-4 py-2 text-gray-600 hover:bg-gray-100 rounded-xl transition-colors"
            >
              Cancelar
            </button>
            
            <button 
              @click="updateStock"
              :disabled="!newStockValue || !stockUpdateReason"
              class="px-6 py-2 bg-blue-500 hover:bg-blue-600 text-white font-semibold rounded-xl transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
            >
              Actualizar Stock
            </button>
          </div>
        </div>
      </div>

    </div>
  </div>
</template>

<script setup>
import { computed, ref, watch, onMounted } from 'vue'
import { reportsService } from '../services/reportsService.js'
import { productsService } from '../services/productsService.js'

const props = defineProps({
  product: {
    type: Object,
    required: true
  }
})

const emit = defineEmits(['close', 'update-stock'])

// Estado reactivo
const periodFilter = ref('all')
const showStockUpdate = ref(false)
const newStockValue = ref('')
const stockUpdateReason = ref('')
const stockUpdateNotes = ref('')

// Estado de datos reales de la base de datos
const salesHistory = ref([])
const isLoadingSales = ref(false)
const salesSummary = ref({
  totalSales: 0,
  totalQuantitySold: 0,
  totalRevenue: 0
})

// Computeds basados en datos reales de la base de datos
const totalUnitsSold = computed(() => {
  return salesSummary.value.totalQuantitySold || 0
})

const totalRevenue = computed(() => {
  return salesSummary.value.totalRevenue || 0
})

const filteredSalesHistory = computed(() => {
  return salesHistory.value || []
})

// Funciones
const formatCurrency = (amount) => {
  if (amount >= 1000000) {
    return `${(amount / 1000000).toFixed(1)}M`
  } else if (amount >= 1000) {
    return `${(amount / 1000).toFixed(0)}k`
  }
  return amount?.toLocaleString('es-ES') || '0'
}

const formatDateTime = (dateString) => {
  if (!dateString) return 'N/A'
  const date = new Date(dateString)
  return date.toLocaleString('es-ES', {
    year: 'numeric',
    month: '2-digit',
    day: '2-digit',
    hour: '2-digit',
    minute: '2-digit'
  })
}

const getProductImage = (product) => {
  if (!product) return `https://images.unsplash.com/photo-1553062407-98eeb64c6a62?w=100&h=100&fit=crop&crop=center`
  
  const imageUrl = product.image_url || product.image || product.img || product.photo
  return imageUrl || `https://images.unsplash.com/photo-1553062407-98eeb64c6a62?w=100&h=100&fit=crop&crop=center`
}

const handleImageError = (event) => {
  event.target.src = `https://images.unsplash.com/photo-1553062407-98eeb64c6a62?w=100&h=100&fit=crop&crop=center`
}

const getStockStatusClass = (stock) => {
  if (!stock || stock === 0) {
    return 'bg-red-100 text-red-800'
  } else if (stock <= 10) {
    return 'bg-yellow-100 text-yellow-800'
  } else {
    return 'bg-green-100 text-green-800'
  }
}

const getStockStatus = (stock) => {
  if (!stock || stock === 0) {
    return 'AGOTADO'
  } else if (stock <= 10) {
    return 'STOCK BAJO'
  } else {
    return 'DISPONIBLE'
  }
}

const getStockBarClass = (stock) => {
  if (!stock || stock === 0) {
    return 'bg-red-500'
  } else if (stock <= 10) {
    return 'bg-yellow-500'
  } else {
    return 'bg-green-500'
  }
}

const getMarginPercentage = (product) => {
  const price = parseFloat(product.price || 0)
  const cost = parseFloat(product.cost_price || 0)
  
  if (cost === 0 || price === 0) return 0
  
  return (((price - cost) / price) * 100).toFixed(1)
}

const getUnitProfit = (product) => {
  const price = parseFloat(product.price || 0)
  const cost = parseFloat(product.cost_price || 0)
  return price - cost
}

const getPaymentMethodLabel = (method) => {
  const methods = {
    'cash': 'Efectivo',
    'card': 'Tarjeta',
    'transfer': 'Transferencia',
    'other': 'Otro'
  }
  return methods[method] || 'No especificado'
}

// Cargar historial de ventas de la base de datos
const loadSalesHistory = async (period = 'all') => {
  if (!props.product?.id) return
  
  isLoadingSales.value = true
  
  try {
    
    const response = await reportsService.getProductSalesHistory(props.product.id, period)
    
    if (response.success) {
      salesHistory.value = response.data || []
      salesSummary.value = response.summary || {
        totalSales: 0,
        totalQuantitySold: 0,
        totalRevenue: 0
      }
      
    } else {
      console.error('❌ Error al cargar historial de ventas:', response.error)
      salesHistory.value = []
      salesSummary.value = {
        totalSales: 0,
        totalQuantitySold: 0,
        totalRevenue: 0
      }
    }
  } catch (error) {
    console.error('❌ Error al cargar historial de ventas:', error)
    salesHistory.value = []
    salesSummary.value = {
      totalSales: 0,
      totalQuantitySold: 0,
      totalRevenue: 0
    }
  } finally {
    isLoadingSales.value = false
  }
}

const updateStock = async () => {
  if (!newStockValue.value) {
    alert('❌ Por favor ingresa una cantidad válida')
    return
  }

  const stockValue = parseInt(newStockValue.value)
  if (isNaN(stockValue) || stockValue < 0) {
    alert('❌ Por favor ingresa un número válido')
    return
  }

  try {
    
    // Usar el mismo formato que funciona en InventoryViewComplete
    const response = await productsService.updateStock(props.product.id, {
      current_stock: stockValue,
      reason: 'Ajuste manual de inventario'
    })
    
    if (response.success) {
      
      // Emitir evento para actualizar el componente padre
      emit('update-stock', props.product.id, stockValue)
      
      showStockUpdate.value = false
      
      // Limpiar formulario
      newStockValue.value = ''
      stockUpdateReason.value = ''
      stockUpdateNotes.value = ''
      
      alert(`✅ Stock actualizado: ${props.product.name} ahora tiene ${stockValue} unidades`)
    } else {
      throw new Error(response.message || 'Error al actualizar stock')
    }
  } catch (error) {
    console.error('❌ Error al actualizar stock:', error)
    alert(`❌ Error al actualizar stock: ${error.message}`)
  }
}

const exportSalesHistory = () => {
  // Implementar exportación
  console.log('Exportando historial de ventas...')
}

// Watchers
watch(periodFilter, (newPeriod) => {
  loadSalesHistory(newPeriod)
})

// Cargar datos al montar el componente
onMounted(() => {
  if (props.product?.id) {
    console.log('🚀 ProductDetailsModal montado, cargando datos para producto:', props.product.id)
    loadSalesHistory(periodFilter.value)
  } else {
    console.warn('⚠️ ProductDetailsModal montado sin producto válido')
  }
})
</script>