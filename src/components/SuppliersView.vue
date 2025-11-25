<template>
  <div class="min-h-screen bg-gray-100">
    <div class="p-4 lg:p-6 space-y-6">
      
      <!-- Header Elegante -->
      <div class="flex items-center justify-between pb-4 border-b border-gray-300">
        <div class="flex items-center space-x-4">
          <div class="w-12 h-12 bg-gradient-to-br from-blue-600 to-indigo-600 rounded-xl flex items-center justify-center shadow-lg">
            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
            </svg>
          </div>
          <div>
            <h1 class="text-2xl font-bold text-gray-900">Gestión de Proveedores</h1>
            <p class="text-sm text-gray-600">Control de proveedores y compras</p>
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
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div class="bg-white rounded-2xl p-5 border border-gray-300 hover:border-gray-400 transition-all duration-200 hover:shadow-lg">
          <div class="flex items-center space-x-4">
            <div class="w-12 h-12 bg-gray-100 rounded-xl flex items-center justify-center flex-shrink-0">
              <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
              </svg>
            </div>
            <div class="flex-1 min-w-0">
              <div class="flex items-center justify-between mb-2">
                <h3 class="text-sm font-semibold text-gray-700">Total Proveedores</h3>
              </div>
              <p class="text-2xl font-bold text-gray-900 mb-1">{{ summary.total_suppliers || 0 }}</p>
              <p class="text-sm text-gray-500">{{ summary.active_suppliers || 0 }} activos</p>
            </div>
          </div>
        </div>

        <div class="bg-white rounded-2xl p-5 border border-gray-300 hover:border-gray-400 transition-all duration-200 hover:shadow-lg">
          <div class="flex items-center space-x-4">
            <div class="w-12 h-12 bg-red-50 rounded-xl flex items-center justify-center flex-shrink-0">
              <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
            </div>
            <div class="flex-1 min-w-0">
              <div class="flex items-center justify-between mb-2">
                <h3 class="text-sm font-semibold text-gray-700">Cuentas por Pagar</h3>
              </div>
              <p class="text-2xl font-bold text-gray-900 mb-1">${{ formatNumber(summary.total_debt || 0) }}</p>
              <p class="text-sm text-gray-500">Deuda total</p>
            </div>
          </div>
        </div>

        <div class="bg-white rounded-2xl p-5 border border-gray-300 hover:border-gray-400 transition-all duration-200 hover:shadow-lg">
          <div class="flex items-center space-x-4">
            <div class="w-12 h-12 bg-green-50 rounded-xl flex items-center justify-center flex-shrink-0">
              <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
              </svg>
            </div>
            <div class="flex-1 min-w-0">
              <div class="flex items-center justify-between mb-2">
                <h3 class="text-sm font-semibold text-gray-700">Mejor Proveedor</h3>
              </div>
              <p class="text-lg font-bold text-gray-900 mb-1 truncate" :title="summary.best_supplier?.name">
                {{ summary.best_supplier?.name || 'N/A' }}
              </p>
              <p class="text-sm text-gray-500" v-if="summary.best_supplier">
                ${{ formatNumber(summary.best_supplier.total_purchases) }}
              </p>
            </div>
          </div>
        </div>
      </div>

      <!-- Tabla de Proveedores -->
      <div class="bg-white rounded-2xl shadow-sm border border-gray-300 overflow-hidden">
        <div class="bg-gray-50 border-b border-gray-200 px-5 py-4 flex items-center justify-between">
          <div>
            <h2 class="text-base font-bold text-gray-900">Lista de Proveedores</h2>
            <p class="text-xs text-gray-500 mt-0.5">Información de contacto y métricas de compra</p>
          </div>
        </div>

        <div v-if="loading" class="flex items-center justify-center py-12">
          <div class="animate-spin rounded-full h-10 w-10 border-4 border-gray-200 border-t-blue-600"></div>
        </div>

        <div v-else-if="suppliers.length === 0" class="text-center py-12">
          <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
            </svg>
          </div>
          <p class="text-sm font-semibold text-gray-700">No hay proveedores</p>
          <p class="text-xs text-gray-500 mt-1">Agrega proveedores para empezar</p>
        </div>

        <div v-else class="overflow-x-auto">
          <table class="min-w-full">
            <thead class="bg-white border-b border-gray-200">
              <tr>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Proveedor</th>
                <th class="px-4 py-3 text-center text-xs font-semibold text-gray-700 uppercase tracking-wider">Productos</th>
                <th class="px-4 py-3 text-center text-xs font-semibold text-gray-700 uppercase tracking-wider">Última Compra</th>
                <th class="px-4 py-3 text-center text-xs font-semibold text-gray-700 uppercase tracking-wider">Total Compras</th>
                <th class="px-4 py-3 text-center text-xs font-semibold text-gray-700 uppercase tracking-wider">Deuda</th>
                <th class="px-4 py-3 text-center text-xs font-semibold text-gray-700 uppercase tracking-wider">Estado</th>
                <th class="px-4 py-3 text-center text-xs font-semibold text-gray-700 uppercase tracking-wider">Acciones</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-100">
              <tr v-for="supplier in paginatedSuppliers" :key="supplier.id" class="hover:bg-gray-50 transition-colors">
                <td class="px-4 py-4">
                  <div>
                    <div class="text-sm font-semibold text-gray-900">{{ supplier.name }}</div>
                    <div class="text-xs text-gray-500 mt-0.5">
                      {{ supplier.contact_name }}
                    </div>
                    <div class="text-xs text-gray-400 mt-0.5 flex items-center space-x-2">
                      <span v-if="supplier.phone">📞 {{ supplier.phone }}</span>
                      <span v-if="supplier.city">📍 {{ supplier.city }}</span>
                    </div>
                  </div>
                </td>
                <td class="px-4 py-4 text-center">
                  <div class="font-mono text-base font-bold text-gray-900">{{ supplier.products_count }}</div>
                  <div class="text-xs text-gray-500 mt-0.5">productos</div>
                </td>
                <td class="px-4 py-4 text-center">
                  <div class="text-sm text-gray-900" v-if="supplier.last_purchase_date">
                    {{ formatDate(supplier.last_purchase_date) }}
                  </div>
                  <div class="text-xs text-gray-400" v-else>Sin compras</div>
                </td>
                <td class="px-4 py-4 text-center">
                  <div class="font-mono text-base font-bold text-gray-900">${{ formatNumber(supplier.total_purchases_amount) }}</div>
                  <div class="text-xs text-gray-500 mt-0.5">{{ supplier.purchase_orders_count }} órdenes</div>
                </td>
                <td class="px-4 py-4 text-center">
                  <div class="font-mono text-base font-bold" :class="[
                    supplier.current_debt > 0 ? 'text-red-600' : 'text-gray-400'
                  ]">${{ formatNumber(supplier.current_debt) }}</div>
                </td>
                <td class="px-4 py-4 text-center">
                  <span :class="[
                    'px-2.5 py-1 text-xs font-bold rounded-full',
                    supplier.active ? 'bg-green-100 text-green-700 border border-green-300' : 'bg-gray-100 text-gray-700 border border-gray-300'
                  ]">
                    {{ supplier.active ? 'Activo' : 'Inactivo' }}
                  </span>
                </td>
                <td class="px-4 py-4 text-center">
                  <div class="flex items-center justify-center space-x-2">
                    <button @click="viewProducts(supplier)" class="text-xs font-medium text-blue-600 hover:text-blue-700 transition-colors">
                      Ver Productos
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Paginación -->
        <div v-if="suppliers.length > itemsPerPage" class="bg-white border-t border-gray-200 px-4 py-3 flex items-center justify-between">
          <div class="flex items-center space-x-3">
            <div class="flex items-center space-x-2">
              <span class="text-xs font-medium text-gray-700">Mostrar:</span>
              <select v-model="itemsPerPage" @change="currentPage = 1" class="border border-gray-300 rounded-lg px-2 py-1 text-xs focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="10">10</option>
                <option value="25">25</option>
                <option value="50">50</option>
              </select>
              <span class="text-xs text-gray-700">por página</span>
            </div>
            <div class="text-xs text-gray-700">
              Mostrando {{ (currentPage - 1) * itemsPerPage + 1 }} a {{ Math.min(currentPage * itemsPerPage, suppliers.length) }} de {{ suppliers.length }}
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
  name: 'SuppliersManagementView',
  data() {
    return {
      loading: false,
      suppliers: [],
      summary: {},
      currentPage: 1,
      itemsPerPage: 25
    }
  },
  computed: {
    totalPages() {
      return Math.ceil(this.suppliers.length / this.itemsPerPage)
    },
    paginatedSuppliers() {
      const start = (this.currentPage - 1) * this.itemsPerPage
      const end = start + this.itemsPerPage
      return this.suppliers.slice(start, end)
    }
  },
  mounted() {
    this.loadData()
  },
  methods: {
    async loadData() {
      this.loading = true
      try {
        const response = await axios.get('/api/suppliers/analytics')
        if (response.data.success) {
          this.suppliers = response.data.data.suppliers
          this.summary = response.data.data.summary
        }
      } catch (error) {
        console.error('Error cargando proveedores:', error)
      } finally {
        this.loading = false
      }
    },
    formatNumber(value) {
      return new Intl.NumberFormat('es-CO').format(value)
    },
    formatDate(dateString) {
      if (!dateString) return 'N/A'
      const date = new Date(dateString)
      return date.toLocaleDateString('es-CO', { year: 'numeric', month: 'short', day: 'numeric' })
    },
    viewProducts(supplier) {
      this.$router.push({ name: 'products', query: { supplier_id: supplier.id } })
    }
  }
}
</script>
