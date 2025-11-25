<template>
  <div class="min-h-screen bg-gray-100">
    <div class="p-4 lg:p-6 space-y-6">
      
      <!-- Header Elegante -->
      <div class="flex items-center justify-between pb-4 border-b border-gray-300">
        <div class="flex items-center space-x-4">
          <div class="w-12 h-12 bg-gradient-to-br from-blue-600 to-indigo-600 rounded-xl flex items-center justify-center shadow-lg">
            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
            </svg>
          </div>
          <div>
            <h1 class="text-2xl font-bold text-gray-900">Análisis de Productos</h1>
            <p class="text-sm text-gray-600">Rotación ABC y rentabilidad por producto</p>
          </div>
        </div>
        
        <div class="flex items-center space-x-3">
          <button @click="loadData" class="px-4 py-2.5 bg-slate-50 hover:bg-slate-100 text-slate-600 text-sm font-semibold rounded-xl border border-slate-200 shadow-sm transition-all duration-200 flex items-center space-x-2 hover:shadow-md hover:border-slate-300">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
            </svg>
            <span>Actualizar</span>
          </button>
        </div>
      </div>

      <!-- Métricas Principales -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white rounded-2xl p-5 border border-gray-300 hover:border-gray-400 transition-all duration-200 hover:shadow-lg">
          <div class="flex items-center space-x-4">
            <div class="w-12 h-12 bg-gray-100 rounded-xl flex items-center justify-center flex-shrink-0">
              <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
              </svg>
            </div>
            <div class="flex-1 min-w-0">
              <div class="flex items-center justify-between mb-2">
                <h3 class="text-sm font-semibold text-gray-700">Total Productos</h3>
              </div>
              <p class="text-2xl font-bold text-gray-900 mb-1">{{ summary.total_products || 0 }}</p>
              <p class="text-sm text-gray-500">Activos en sistema</p>
            </div>
          </div>
        </div>

        <div class="bg-white rounded-2xl p-5 border border-gray-300 hover:border-gray-400 transition-all duration-200 hover:shadow-lg">
          <div class="flex items-center space-x-4">
            <div class="w-12 h-12 bg-green-50 rounded-xl flex items-center justify-center flex-shrink-0">
              <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
              </svg>
            </div>
            <div class="flex-1 min-w-0">
              <div class="flex items-center justify-between mb-2">
                <h3 class="text-sm font-semibold text-gray-700">Clase A</h3>
                <span class="text-xs font-medium text-green-700 bg-green-50 px-2 py-1 rounded-lg border border-green-200">
                  Alta Rotación
                </span>
              </div>
              <p class="text-2xl font-bold text-gray-900 mb-1">{{ summary.class_a_count || 0 }}</p>
              <p class="text-sm text-gray-500">Top performers</p>
            </div>
          </div>
        </div>

        <div class="bg-white rounded-2xl p-5 border border-gray-300 hover:border-gray-400 transition-all duration-200 hover:shadow-lg">
          <div class="flex items-center space-x-4">
            <div class="w-12 h-12 bg-blue-50 rounded-xl flex items-center justify-center flex-shrink-0">
              <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z" />
              </svg>
            </div>
            <div class="flex-1 min-w-0">
              <div class="flex items-center justify-between mb-2">
                <h3 class="text-sm font-semibold text-gray-700">Clase B</h3>
                <span class="text-xs font-medium text-blue-700 bg-blue-50 px-2 py-1 rounded-lg border border-blue-200">
                  Media
                </span>
              </div>
              <p class="text-2xl font-bold text-gray-900 mb-1">{{ summary.class_b_count || 0 }}</p>
              <p class="text-sm text-gray-500">Rotación moderada</p>
            </div>
          </div>
        </div>

        <div class="bg-white rounded-2xl p-5 border border-gray-300 hover:border-gray-400 transition-all duration-200 hover:shadow-lg">
          <div class="flex items-center space-x-4">
            <div class="w-12 h-12 bg-amber-50 rounded-xl flex items-center justify-center flex-shrink-0">
              <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
            </div>
            <div class="flex-1 min-w-0">
              <div class="flex items-center justify-between mb-2">
                <h3 class="text-sm font-semibold text-gray-700">Margen Promedio</h3>
              </div>
              <p class="text-2xl font-bold text-gray-900 mb-1">{{ summary.avg_margin || 0 }}%</p>
              <p class="text-sm text-gray-500">Rentabilidad general</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Tabla de Productos -->
      <div class="bg-white rounded-2xl shadow-sm border border-gray-300 overflow-hidden">
        <div class="bg-gray-50 border-b border-gray-200 px-5 py-4 flex items-center justify-between">
          <div>
            <h2 class="text-base font-bold text-gray-900">Productos con Métricas de Rendimiento</h2>
            <p class="text-xs text-gray-500 mt-0.5">Rotación ABC y rentabilidad - Últimos {{ summary.analysis_period_days || 90 }} días</p>
          </div>
        </div>

        <div v-if="loading" class="flex items-center justify-center py-12">
          <div class="animate-spin rounded-full h-10 w-10 border-4 border-gray-200 border-t-blue-600"></div>
        </div>

        <div v-else-if="products.length === 0" class="text-center py-12">
          <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
            </svg>
          </div>
          <p class="text-sm font-semibold text-gray-700">No hay productos</p>
          <p class="text-xs text-gray-500 mt-1">Agrega productos para ver análisis</p>
        </div>

        <div v-else class="overflow-x-auto">
          <table class="min-w-full">
            <thead class="bg-white border-b border-gray-200">
              <tr>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Producto</th>
                <th class="px-4 py-3 text-center text-xs font-semibold text-gray-700 uppercase tracking-wider">Stock</th>
                <th class="px-4 py-3 text-center text-xs font-semibold text-gray-700 uppercase tracking-wider">Precio</th>
                <th class="px-4 py-3 text-center text-xs font-semibold text-gray-700 uppercase tracking-wider">Rotación</th>
                <th class="px-4 py-3 text-center text-xs font-semibold text-gray-700 uppercase tracking-wider">Rentabilidad</th>
                <th class="px-4 py-3 text-center text-xs font-semibold text-gray-700 uppercase tracking-wider">Ventas 90d</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-100">
              <tr v-for="product in paginatedProducts" :key="product.id" class="hover:bg-gray-50 transition-colors">
                <td class="px-4 py-4">
                  <div>
                    <div class="text-sm font-semibold text-gray-900">{{ product.name }}</div>
                    <div class="text-xs text-gray-500 mt-0.5">
                      <span class="font-mono">{{ product.sku }}</span>
                      <span v-if="product.category_name" class="ml-2">• {{ product.category_name }}</span>
                    </div>
                  </div>
                </td>
                <td class="px-4 py-4 text-center">
                  <div class="font-mono text-base font-bold" :class="[
                    product.current_stock <= product.min_stock ? 'text-red-600' : 'text-gray-900'
                  ]">{{ product.current_stock }}</div>
                  <div class="text-xs text-gray-500 mt-0.5">{{ product.unit }}</div>
                </td>
                <td class="px-4 py-4 text-center">
                  <div class="font-mono text-base font-bold text-gray-900">${{ formatNumber(product.sale_price) }}</div>
                  <div class="text-xs text-gray-500 mt-0.5">Costo: ${{ formatNumber(product.cost_price) }}</div>
                </td>
                <td class="px-4 py-4 text-center">
                  <span :class="[
                    'px-2.5 py-1 text-xs font-bold rounded-full',
                    product.rotation_class === 'A' ? 'bg-green-100 text-green-700 border border-green-300' :
                    product.rotation_class === 'B' ? 'bg-blue-100 text-blue-700 border border-blue-300' :
                    'bg-amber-100 text-amber-700 border border-amber-300'
                  ]">
                    Clase {{ product.rotation_class }}
                  </span>
                  <div class="text-xs text-gray-500 mt-1">{{ product.units_sold }} unidades</div>
                </td>
                <td class="px-4 py-4 text-center">
                  <div class="font-mono text-lg font-bold" :class="[
                    product.margin_percentage >= 40 ? 'text-green-600' :
                    product.margin_percentage >= 20 ? 'text-blue-600' :
                    'text-amber-600'
                  ]">{{ product.margin_percentage }}%</div>
                  <div class="text-xs text-gray-500 mt-0.5">margen</div>
                </td>
                <td class="px-4 py-4 text-center">
                  <div class="font-mono text-base font-bold text-gray-900">{{ product.sales_count }}</div>
                  <div class="text-xs text-gray-500 mt-0.5">facturas</div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Paginación -->
        <div v-if="products.length > itemsPerPage" class="bg-white border-t border-gray-200 px-4 py-3 flex items-center justify-between">
          <div class="flex items-center space-x-3">
            <div class="flex items-center space-x-2">
              <span class="text-xs font-medium text-gray-700">Mostrar:</span>
              <select v-model="itemsPerPage" @change="currentPage = 1" class="border border-gray-300 rounded-lg px-2 py-1 text-xs focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="10">10</option>
                <option value="25">25</option>
                <option value="50">50</option>
                <option value="100">100</option>
              </select>
              <span class="text-xs text-gray-700">por página</span>
            </div>
            <div class="text-xs text-gray-700">
              Mostrando {{ (currentPage - 1) * itemsPerPage + 1 }} a {{ Math.min(currentPage * itemsPerPage, products.length) }} de {{ products.length }}
            </div>
          </div>
          
          <div class="flex items-center space-x-1">
            <button @click="currentPage = 1" :disabled="currentPage === 1" class="p-1.5 text-gray-500 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed">
              <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"></path>
              </svg>
            </button>
            <button @click="currentPage--" :disabled="currentPage === 1" class="p-1.5 text-gray-500 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed">
              <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
              </svg>
            </button>
            
            <div class="flex items-center space-x-1">
              <button v-for="page in totalPages" :key="page" @click="currentPage = page" :class="[
                'px-2.5 py-1 text-xs font-medium rounded-lg transition-colors',
                page === currentPage 
                  ? 'bg-blue-600 text-white border border-blue-600' 
                  : 'text-gray-500 bg-white border border-gray-300 hover:bg-gray-50'
              ]">
                {{ page }}
              </button>
            </div>
            
            <button @click="currentPage++" :disabled="currentPage === totalPages" class="p-1.5 text-gray-500 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed">
              <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
              </svg>
            </button>
            <button @click="currentPage = totalPages" :disabled="currentPage === totalPages" class="p-1.5 text-gray-500 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed">
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

<script>
import axios from 'axios'

export default {
  name: 'ProductsSection',
  data() {
    return {
      loading: false,
      products: [],
      summary: {},
      currentPage: 1,
      itemsPerPage: 25
    }
  },
  computed: {
    totalPages() {
      return Math.ceil(this.products.length / this.itemsPerPage)
    },
    paginatedProducts() {
      const start = (this.currentPage - 1) * this.itemsPerPage
      const end = start + this.itemsPerPage
      return this.products.slice(start, end)
    }
  },
  mounted() {
    this.loadData()
  },
  methods: {
    async loadData() {
      this.loading = true
      try {
        const response = await axios.get('/api/products/analytics')
        if (response.data.success) {
          this.products = response.data.data.products
          this.summary = response.data.data.summary
        }
      } catch (error) {
        console.error('Error cargando productos:', error)
      } finally {
        this.loading = false
      }
    },
    formatNumber(value) {
      return new Intl.NumberFormat('es-CO').format(value)
    }
  }
}
</script>