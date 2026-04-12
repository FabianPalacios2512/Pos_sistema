<template>
  <div class="overview-section">
    <!-- Loading State -->
    <div v-if="loading" class="flex items-center justify-center py-12">
      <div class="text-center">
        <svg class="animate-spin w-12 h-12 text-[#1a73e8] dark:text-[#8ab4f8] mx-auto" fill="none" viewBox="0 0 24 24">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        <p class="mt-4 text-[#5f6368] dark:text-[#9aa0a6]">Cargando vista general...</p>
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
        <div class="bg-white dark:bg-[#1e1f20] rounded-2xl p-6">
          <div class="flex items-center justify-between mb-4">
            <h3 class="text-base font-semibold text-[#1e1f20] dark:text-[#e3e3e3]">
              Tendencia de Movimientos (30 días)
            </h3>
            <button
              @click="$emit('refresh')"
              class="inline-flex items-center gap-2 px-3 py-1.5 text-sm font-medium text-[#1a73e8] dark:text-[#8ab4f8] hover:bg-[#e8f0fe] dark:hover:bg-[#1a73e8]/20 rounded-full transition-colors"
            >
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
              </svg>
              Actualizar
            </button>
          </div>
          <MovementsTrendChart 
            :data="data.movementsTrend" 
            :height="300"
          />
        </div>

        <!-- Top productos más vendidos -->
        <div class="bg-white dark:bg-[#1e1f20] rounded-2xl p-6">
          <h3 class="text-base font-semibold text-[#1e1f20] dark:text-[#e3e3e3] mb-4">
            Productos Más Vendidos (Último Mes)
          </h3>
          <div class="space-y-3">
            <div
              v-for="(product, index) in data.topSellingProducts"
              :key="product.id"
              class="flex items-center justify-between p-3 bg-[#f8f9fa] dark:bg-[#282a2c] rounded-2xl"
            >
              <div class="flex items-center space-x-3">
                <div class="flex-shrink-0">
                  <span class="inline-flex items-center justify-center h-8 w-8 rounded-full bg-[#e8f0fe] dark:bg-[#1a73e8]/20 text-[#1a73e8] dark:text-[#8ab4f8] font-medium text-sm">
                    {{ index + 1 }}
                  </span>
                </div>
                <div>
                  <p class="font-medium text-[#1e1f20] dark:text-[#e3e3e3]">{{ product.name }}</p>
                  <p class="text-sm text-[#5f6368] dark:text-[#9aa0a6]">SKU: {{ product.sku }}</p>
                </div>
              </div>
              <div class="text-right">
                <p class="font-semibold text-[#1e1f20] dark:text-[#e3e3e3]">{{ product.total_sold || 0 }} unidades</p>
                <p class="text-sm text-[#5f6368] dark:text-[#9aa0a6]">{{ formatCurrency(product.sale_price) }}</p>
              </div>
            </div>
          </div>
          <div v-if="!data.topSellingProducts || data.topSellingProducts.length === 0" class="text-center py-6">
            <svg class="w-10 h-10 text-[#5f6368] dark:text-[#9aa0a6] mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
              <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z"></path>
            </svg>
            <p class="text-[#5f6368] dark:text-[#9aa0a6]">No hay datos de ventas en el último mes</p>
          </div>
        </div>
      </div>

      <!-- Movimientos recientes -->
      <div class="bg-white dark:bg-[#1e1f20] rounded-2xl overflow-hidden">
        <div class="px-6 py-4 border-b border-[#e8eaed] dark:border-[#3a3a3f]">
          <div class="flex items-center justify-between">
            <h3 class="text-base font-semibold text-[#1e1f20] dark:text-[#e3e3e3]">
              Movimientos Recientes (Últimos 7 días)
            </h3>
            <router-link
              to="/inventory/movements"
              class="inline-flex items-center gap-1 text-sm font-medium text-[#1a73e8] dark:text-[#8ab4f8] hover:underline"
            >
              Ver todos 
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"></path>
              </svg>
            </router-link>
          </div>
        </div>
        <div class="divide-y divide-[#e8eaed] dark:divide-[#3a3a3f]">
          <div
            v-for="movement in data.recentMovements"
            :key="movement.id"
            class="px-6 py-4 hover:bg-[#f8f9fa] dark:hover:bg-[#282a2c] transition-colors"
          >
            <div class="flex items-center justify-between">
              <div class="flex items-center space-x-4">
                <div class="flex-shrink-0">
                  <MovementTypeIcon :type="movement.type" />
                </div>
                <div>
                  <p class="font-medium text-[#1e1f20] dark:text-[#e3e3e3]">{{ movement.product?.name }}</p>
                  <p class="text-sm text-[#5f6368] dark:text-[#9aa0a6]">
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
                <p class="text-sm text-[#5f6368] dark:text-[#9aa0a6]">
                  {{ movement.previous_stock }} → {{ movement.new_stock }}
                </p>
              </div>
            </div>
          </div>
        </div>
        <div v-if="!data.recentMovements || data.recentMovements.length === 0" class="px-6 py-8 text-center">
          <svg class="w-10 h-10 text-[#5f6368] dark:text-[#9aa0a6] mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 21L3 16.5m0 0L7.5 12M3 16.5h13.5m0-13.5L21 7.5m0 0L16.5 12M21 7.5H7.5"></path>
          </svg>
          <p class="text-[#5f6368] dark:text-[#9aa0a6]">No hay movimientos recientes</p>
        </div>
      </div>
    </div>

    <!-- Error State -->
    <div v-else class="text-center py-12">
      <div>
        <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-[#fce8e6] dark:bg-[#d93025]/20 flex items-center justify-center">
          <svg class="w-8 h-8 text-[#d93025] dark:text-[#f28b82]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"></path>
          </svg>
        </div>
        <p class="text-base font-medium text-[#1e1f20] dark:text-[#e3e3e3]">Error al cargar los datos</p>
        <button
          @click="$emit('refresh')"
          class="mt-4 px-6 py-2.5 bg-[#1a73e8] dark:bg-[#8ab4f8] text-white dark:text-[#131314] text-sm font-medium rounded-full hover:bg-[#1557b0] dark:hover:bg-[#aecbfa] transition-colors"
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
      if (quantity > 0) return 'text-[#1e8e3e] dark:text-[#81c995]'
      if (quantity < 0) return 'text-[#d93025] dark:text-[#f28b82]'
      return 'text-[#5f6368] dark:text-[#9aa0a6]'
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