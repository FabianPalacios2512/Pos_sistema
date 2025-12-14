<template>
  <div class="overview-section">
    <!-- Loading State -->
    <div v-if="loading" class="flex items-center justify-center py-12">
      <div class="text-center">
        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-600 mx-auto"></div>
        <p class="mt-4 text-gray-600">Cargando vista general...</p>
      </div>
    </div>

    <!-- Content -->
    <div v-else-if="data" class="space-y-6">
      <!-- Métricas principales -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
        <MetricCard
          title="Total Productos"
          :value="data.metrics.totalProducts"
          icon="fas fa-boxes"
          color="blue"
        />
        <MetricCard
          title="Productos Activos"
          :value="data.metrics.activeProducts"
          icon="fas fa-check-circle"
          color="green"
        />
        <MetricCard
          title="Stock Bajo"
          :value="data.metrics.lowStockProducts"
          icon="fas fa-exclamation-triangle"
          color="yellow"
          :alert="data.metrics.lowStockProducts > 0"
        />
        <MetricCard
          title="Sin Stock"
          :value="data.metrics.outOfStockProducts"
          icon="fas fa-times-circle"
          color="red"
          :alert="data.metrics.outOfStockProducts > 0"
        />
        <MetricCard
          title="Valor Total"
          :value="formatCurrency(data.metrics.totalInventoryValue)"
          icon="fas fa-dollar-sign"
          color="purple"
          :is-currency="true"
        />
      </div>

      <!-- Gráficos y análisis -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Gráfico de tendencia de movimientos -->
        <div class="bg-white rounded-lg shadow p-6">
          <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-900">
              Tendencia de Movimientos (30 días)
            </h3>
            <button
              @click="$emit('refresh')"
              class="text-indigo-600 hover:text-indigo-800 text-sm"
            >
              <i class="fas fa-sync-alt mr-1"></i>
              Actualizar
            </button>
          </div>
          <MovementsTrendChart 
            :data="data.movementsTrend" 
            :height="300"
          />
        </div>

        <!-- Top productos más vendidos -->
        <div class="bg-white rounded-lg shadow p-6">
          <h3 class="text-lg font-semibold text-gray-900 mb-4">
            Productos Más Vendidos (Último Mes)
          </h3>
          <div class="space-y-3">
            <div
              v-for="(product, index) in data.topSellingProducts"
              :key="product.id"
              class="flex items-center justify-between p-3 bg-gray-50 rounded-lg"
            >
              <div class="flex items-center space-x-3">
                <div class="flex-shrink-0">
                  <span class="inline-flex items-center justify-center h-8 w-8 rounded-full bg-indigo-100 text-indigo-800 font-medium text-sm">
                    {{ index + 1 }}
                  </span>
                </div>
                <div>
                  <p class="font-medium text-gray-900">{{ product.name }}</p>
                  <p class="text-sm text-gray-500">SKU: {{ product.sku }}</p>
                </div>
              </div>
              <div class="text-right">
                <p class="font-semibold text-gray-900">{{ product.total_sold || 0 }} unidades</p>
                <p class="text-sm text-gray-500">{{ formatCurrency(product.sale_price) }}</p>
              </div>
            </div>
          </div>
          <div v-if="!data.topSellingProducts || data.topSellingProducts.length === 0" class="text-center py-6">
            <i class="fas fa-chart-bar text-gray-400 text-3xl mb-2"></i>
            <p class="text-gray-500">No hay datos de ventas en el último mes</p>
          </div>
        </div>
      </div>

      <!-- Movimientos recientes -->
      <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
          <div class="flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-900">
              Movimientos Recientes (Últimos 7 días)
            </h3>
            <router-link
              to="/inventory/movements"
              class="text-indigo-600 hover:text-indigo-800 text-sm font-medium"
            >
              Ver todos <i class="fas fa-arrow-right ml-1"></i>
            </router-link>
          </div>
        </div>
        <div class="divide-y divide-gray-200">
          <div
            v-for="movement in data.recentMovements"
            :key="movement.id"
            class="px-6 py-4 hover:bg-gray-50"
          >
            <div class="flex items-center justify-between">
              <div class="flex items-center space-x-4">
                <div class="flex-shrink-0">
                  <MovementTypeIcon :type="movement.type" />
                </div>
                <div>
                  <p class="font-medium text-gray-900">{{ movement.product?.name }}</p>
                  <p class="text-sm text-gray-500">
                    {{ movement.type_description }} • 
                    {{ formatDate(movement.movement_date) }} • 
                    {{ movement.user?.name }}
                  </p>
                </div>
              </div>
              <div class="text-right">
                <p class="font-semibold" :class="getQuantityClass(movement.quantity)">
                  {{ movement.quantity > 0 ? '+' : '' }}{{ movement.quantity }}
                </p>
                <p class="text-sm text-gray-500">
                  {{ movement.previous_stock }} → {{ movement.new_stock }}
                </p>
              </div>
            </div>
          </div>
        </div>
        <div v-if="!data.recentMovements || data.recentMovements.length === 0" class="px-6 py-8 text-center">
          <i class="fas fa-exchange-alt text-gray-400 text-3xl mb-2"></i>
          <p class="text-gray-500">No hay movimientos recientes</p>
        </div>
      </div>
    </div>

    <!-- Error State -->
    <div v-else class="text-center py-12">
      <div class="text-red-500">
        <i class="fas fa-exclamation-triangle text-4xl mb-4"></i>
        <p class="text-lg font-medium">Error al cargar los datos</p>
        <button
          @click="$emit('refresh')"
          class="mt-4 px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700"
        >
          Intentar de nuevo
        </button>
      </div>
    </div>
  </div>
</template>

<script>
import { computed } from 'vue'
import MetricCard from '../components/MetricCard.vue'
import MovementsTrendChart from '../components/MovementsTrendChart.vue'
import MovementTypeIcon from '../components/MovementTypeIcon.vue'

export default {
  name: 'OverviewSection',
  components: {
    MetricCard,
    MovementsTrendChart,
    MovementTypeIcon
  },
  props: {
    data: {
      type: Object,
      default: null
    },
    loading: {
      type: Boolean,
      default: false
    }
  },
  emits: ['refresh'],
  setup() {
    // Métodos de formateo
    const formatCurrency = (amount) => {
      if (!amount && amount !== 0) return '$0.00'
      return new Intl.NumberFormat('es-CO', {
        style: 'currency',
        currency: 'COP',
        minimumFractionDigits: 0,
        maximumFractionDigits: 0
      }).format(amount)
    }

    const formatDate = (date) => {
      if (!date) return ''
      return new Date(date).toLocaleDateString('es-ES', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
      })
    }

    const getQuantityClass = (quantity) => {
      if (quantity > 0) return 'text-green-600'
      if (quantity < 0) return 'text-red-600'
      return 'text-gray-600'
    }

    return {
      formatCurrency,
      formatDate,
      getQuantityClass
    }
  }
}
</script>

<style scoped>
.overview-section {
  min-height: 400px;
}

/* Animaciones para las métricas */
.metric-card {
  transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.metric-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
}

/* Responsive design */
@media (max-width: 768px) {
  .grid {
    grid-template-columns: repeat(1, minmax(0, 1fr));
  }
}

@media (min-width: 769px) and (max-width: 1024px) {
  .lg\\:grid-cols-5 {
    grid-template-columns: repeat(3, minmax(0, 1fr));
  }
}
</style>