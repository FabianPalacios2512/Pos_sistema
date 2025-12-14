<template>
  <!-- Executive Dashboard - Estilo Linear/Raycast/Shadcn -->
  <div class="min-h-screen bg-slate-50 dark:bg-[#0a0a0c] font-sans antialiased">
    
    <!-- Container Principal con más whitespace -->
    <div class="max-w-[1600px] mx-auto px-6 lg:px-12 py-8 space-y-8">
      
      <!-- Header Executive Clean -->
      <div class="flex items-center justify-between pb-6 border-b border-gray-200/60 dark:border-gray-800">
        <div class="space-y-1">
          <h1 class="text-3xl font-bold text-slate-900 dark:text-white tracking-tight">
            Panel de Control
          </h1>
          <p class="text-sm text-gray-600 dark:text-gray-400 font-medium flex items-center gap-2">
            <span class="inline-flex h-2 w-2 rounded-full bg-emerald-500"></span>
            Sistema Operativo • {{ currentDate }}
          </p>
        </div>
        
        <div class="flex items-center gap-3">
          <button 
            @click="refreshData" 
            :disabled="loading"
            class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-750 transition-colors shadow-sm"
          >
            <svg :class="['w-4 h-4', loading ? 'animate-spin' : '']" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
            </svg>
            Refrescar
          </button>
          
          <button 
            @click="handleNewSale"
            class="inline-flex items-center gap-2 px-5 py-2 text-sm font-medium text-white bg-gray-900 dark:bg-white dark:text-gray-900 rounded-lg hover:bg-gray-800 dark:hover:bg-gray-100 transition-colors shadow-sm"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Nueva Venta
          </button>
        </div>
      </div>

      <!-- Grid de KPIs Executive (4 columnas con más padding y whitespace) -->
      <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6">
        
        <!-- 1. Estado de Caja -->
        <div class="bg-white dark:bg-gray-900 border border-gray-200/60 dark:border-gray-800 rounded-xl p-6 shadow hover:shadow-lg transition-all duration-200">
          <div class="flex items-start justify-between mb-4">
            <div class="flex items-center gap-3">
              <div class="w-10 h-10 rounded-lg flex items-center justify-center"
                   :class="hasOpenSession ? 'bg-emerald-50 dark:bg-emerald-900/20' : 'bg-gray-100 dark:bg-gray-800'">
                <svg class="w-5 h-5" :class="hasOpenSession ? 'text-emerald-600 dark:text-emerald-400' : 'text-gray-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path v-if="hasOpenSession" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                  <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                </svg>
              </div>
              <div>
                <p class="text-xs font-medium text-gray-600 dark:text-gray-400 uppercase tracking-wider">Estado de Caja</p>
                <p class="text-sm font-semibold mt-0.5" :class="hasOpenSession ? 'text-emerald-600 dark:text-emerald-400' : 'text-gray-700 dark:text-gray-300'">
                  {{ hasOpenSession ? 'Abierta' : 'Cerrada' }}
                </p>
              </div>
            </div>
          </div>
          
          <div class="space-y-3">
            <div>
              <p class="text-2xl font-bold text-slate-900 dark:text-white tracking-tight">
                ${{ formatCurrency(cashAmount) }}
              </p>
              <p v-if="hasOpenSession" class="text-xs text-gray-600 dark:text-gray-400 mt-1">
                Sesión #{{ currentSession?.id }}
              </p>
            </div>
            
            <button 
              @click="hasOpenSession ? handleCloseCash() : handleOpenCash()"
              :disabled="cashLoading"
              class="w-full py-2 px-4 text-sm font-medium rounded-lg border transition-colors"
              :class="hasOpenSession 
                ? 'bg-white dark:bg-gray-800 text-rose-600 dark:text-rose-400 border-rose-200 dark:border-rose-900/30 hover:bg-rose-50 dark:hover:bg-rose-900/10' 
                : 'bg-emerald-50 dark:bg-emerald-900/20 text-emerald-700 dark:text-emerald-400 border-emerald-200 dark:border-emerald-800 hover:bg-emerald-100 dark:hover:bg-emerald-900/30'"
            >
              {{ cashLoading ? 'Procesando...' : (hasOpenSession ? 'Cerrar Turno' : 'Abrir Caja') }}
            </button>
          </div>
        </div>

        <!-- 2. Ventas Hoy -->
        <div class="bg-white dark:bg-gray-900 border border-gray-200/60 dark:border-gray-800 rounded-xl p-6 shadow hover:shadow-lg transition-all duration-200">
          <div class="flex items-start justify-between mb-4">
            <div class="flex items-center gap-3">
              <div class="w-10 h-10 rounded-lg bg-blue-50 dark:bg-blue-900/20 flex items-center justify-center">
                <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                </svg>
              </div>
              <div>
                <p class="text-xs font-medium text-gray-600 dark:text-gray-400 uppercase tracking-wider">Ventas Hoy</p>
              </div>
            </div>
            <span 
              class="inline-flex items-center gap-1 px-2 py-1 text-xs font-medium rounded-full"
              :class="growthPercentage >= 0 
                ? 'bg-emerald-50 dark:bg-emerald-900/20 text-emerald-700 dark:text-emerald-400' 
                : 'bg-rose-50 dark:bg-rose-900/20 text-rose-700 dark:text-rose-400'"
            >
              <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="growthPercentage >= 0 ? 'M5 10l7-7m0 0l7 7m-7-7v18' : 'M19 14l-7 7m0 0l-7-7m7 7V3'"></path>
              </svg>
              {{ Math.abs(growthPercentage) }}%
            </span>
          </div>
          
          <div class="space-y-1">
            <p class="text-2xl font-bold text-slate-900 dark:text-white tracking-tight">
              ${{ formatCurrency(todaySalesData.revenue) }}
            </p>
            <p class="text-sm text-gray-600 dark:text-gray-400">
              {{ todaySalesData.sales }} transacciones
            </p>
          </div>
        </div>

        <!-- 3. Transacciones -->
        <div class="bg-white dark:bg-gray-900 border border-gray-200/60 dark:border-gray-800 rounded-xl p-6 shadow hover:shadow-lg transition-all duration-200">
          <div class="flex items-start justify-between mb-4">
            <div class="flex items-center gap-3">
              <div class="w-10 h-10 rounded-lg bg-purple-50 dark:bg-purple-900/20 flex items-center justify-center">
                <svg class="w-5 h-5 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                </svg>
              </div>
              <div>
                <p class="text-xs font-medium text-gray-600 dark:text-gray-400 uppercase tracking-wider">Transacciones</p>
              </div>
            </div>
          </div>
          
          <div class="space-y-1">
            <p class="text-2xl font-bold text-slate-900 dark:text-white tracking-tight">
              {{ todaySalesData.sales }}
            </p>
            <p class="text-sm text-gray-600 dark:text-gray-400">
              Ticket promedio: <span class="font-semibold text-slate-700 dark:text-gray-300">${{ formatCurrency(averageTicket) }}</span>
            </p>
          </div>
        </div>

        <!-- 4. Alertas/Stock -->
        <div class="bg-white dark:bg-gray-900 border border-gray-200/60 dark:border-gray-800 rounded-xl p-6 shadow hover:shadow-lg transition-all duration-200">
          <div class="flex items-start justify-between mb-4">
            <div class="flex items-center gap-3">
              <div class="w-10 h-10 rounded-lg flex items-center justify-center"
                   :class="lowStockCount > 0 ? 'bg-amber-50 dark:bg-amber-900/20' : 'bg-gray-100 dark:bg-gray-800'">
                <svg class="w-5 h-5" :class="lowStockCount > 0 ? 'text-amber-600 dark:text-amber-400' : 'text-gray-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>
              </div>
              <div>
                <p class="text-xs font-medium text-gray-600 dark:text-gray-400 uppercase tracking-wider">Alertas Stock</p>
              </div>
            </div>
          </div>
          
          <div class="space-y-3">
            <div>
              <p class="text-2xl font-semibold tracking-tight"
                 :class="lowStockCount > 0 ? 'text-amber-600 dark:text-amber-400' : 'text-gray-900 dark:text-white'">
                {{ lowStockCount }}
              </p>
              <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                {{ lowStockCount === 0 ? 'Todo en orden' : 'Productos críticos' }}
              </p>
            </div>
            
            <button 
              @click="handleGoToInventory"
              class="w-full py-2 px-4 text-sm font-medium bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 border border-gray-200 dark:border-gray-700 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-750 transition-colors"
            >
              Ver Inventario
            </button>
          </div>
        </div>
      </div>

      <!-- Grid de Gráficos (2 columnas) -->
      <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
        
        <!-- Gráfico de Ingresos (Area Chart Mejorado) -->
        <div class="bg-white dark:bg-gray-900 border border-gray-200/60 dark:border-gray-800 rounded-xl p-6 shadow hover:shadow-lg transition-all duration-200">
          <div class="flex items-center justify-between mb-6">
            <div>
              <h3 class="text-base font-bold text-slate-900 dark:text-white">Análisis de Ingresos</h3>
              <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Tendencia de ventas en el tiempo</p>
            </div>
            
            <!-- Tabs de período -->
            <div class="inline-flex items-center gap-1 p-1 bg-gray-100 dark:bg-gray-800 rounded-lg">
              <button 
                v-for="period in periods" 
                :key="period.value"
                @click="selectedPeriod = period.value"
                class="px-3 py-1.5 text-xs font-medium rounded-md transition-colors"
                :class="selectedPeriod === period.value 
                  ? 'bg-white dark:bg-gray-700 text-gray-900 dark:text-white shadow-sm' 
                  : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200'"
              >
                {{ period.label }}
              </button>
            </div>
          </div>
          
          <div class="h-[300px] flex items-center justify-center">
            <Line v-if="!loading" :data="chartData" :options="chartOptions" />
            <div v-else class="text-gray-400">Cargando datos...</div>
          </div>
        </div>

        <!-- Gráfico de Top Productos (Doughnut Mejorado) -->
        <div class="bg-white dark:bg-gray-900 border border-gray-200/60 dark:border-gray-800 rounded-xl p-6 shadow hover:shadow-lg transition-all duration-200">
          <div class="mb-6">
            <h3 class="text-base font-bold text-slate-900 dark:text-white">Top Productos</h3>
            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Distribución de ingresos</p>
          </div>
          
          <div class="flex items-center gap-8">
            <!-- Gráfico circular más delgado -->
            <div class="w-48 h-48 relative flex-shrink-0">
              <Doughnut v-if="!loading" :data="productChartData" :options="productChartOptions" />
              <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none">
                <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ totalItems }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider">Items</p>
              </div>
            </div>
            
            <!-- Leyenda mejorada -->
            <div class="flex-1 space-y-3">
              <div v-for="(product, index) in topProducts.slice(0, 5)" :key="index" class="flex items-center justify-between">
                <div class="flex items-center gap-2 min-w-0 flex-1">
                  <div class="w-2 h-2 rounded-full flex-shrink-0" :style="{ backgroundColor: productColors[index] }"></div>
                  <span class="text-sm text-gray-700 dark:text-gray-300 truncate">{{ product.name }}</span>
                </div>
                <span class="text-sm font-medium text-gray-900 dark:text-white ml-2 flex-shrink-0">${{ formatCurrency(product.revenue) }}</span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Últimas Transacciones -->
      <div class="bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 rounded-xl p-6 shadow-sm">
        <div class="mb-6">
          <h3 class="text-base font-semibold text-gray-900 dark:text-white">Últimas Transacciones</h3>
          <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Actividad reciente del sistema</p>
        </div>
        
        <div class="space-y-1">
          <div v-if="recentSalesComputed && recentSalesComputed.length > 0">
            <div v-for="sale in recentSalesComputed.slice(0, 5)" :key="sale.id" 
                 class="flex items-center justify-between p-3 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
              <div class="flex items-center gap-4 min-w-0 flex-1">
                <div class="w-8 h-8 rounded-lg bg-emerald-50 dark:bg-emerald-900/20 flex items-center justify-center flex-shrink-0">
                  <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                  </svg>
                </div>
                <div class="min-w-0 flex-1">
                  <p class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ sale.customer?.name || 'Cliente General' }}</p>
                  <p class="text-xs text-gray-500 dark:text-gray-400">{{ formatDate(sale.created_at) }}</p>
                </div>
              </div>
              <div class="text-right ml-4 flex-shrink-0">
                <p class="text-sm font-semibold text-gray-900 dark:text-white">${{ formatCurrency(sale.total) }}</p>
                <span class="inline-flex items-center gap-1 text-xs text-emerald-600 dark:text-emerald-400">
                  <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full"></span>
                  Pagado
                </span>
              </div>
            </div>
          </div>
          
          <!-- Estado vacío -->
          <div v-else class="flex flex-col items-center justify-center py-12 text-center">
            <div class="w-16 h-16 rounded-full bg-gray-100 dark:bg-gray-800 flex items-center justify-center mb-4">
              <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
              </svg>
            </div>
            <p class="text-sm font-medium text-gray-900 dark:text-white mb-1">No hay transacciones recientes</p>
            <p class="text-xs text-gray-500 dark:text-gray-400">Las ventas aparecerán aquí</p>
          </div>
        </div>
      </div>

    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import { Line, Doughnut } from 'vue-chartjs'
import {
  Chart,
  CategoryScale,
  LinearScale,
  PointElement,
  LineElement,
  ArcElement,
  Title,
  Tooltip,
  Legend,
  Filler
} from 'chart.js'
import { useCashSession } from '../services/cashSessionService.js'
import { reportsService } from '../services/reportsService.js'
import { cashReportsService } from '../services/cashReportsService.js'

Chart.register(CategoryScale, LinearScale, PointElement, LineElement, ArcElement, Title, Tooltip, Legend, Filler)

// Props
const props = defineProps({
  salesData: {
    type: Object,
    default: () => ({
      today: { revenue: 0, sales: 0, items_sold: 0, average_ticket: 0 },
      yesterday: { revenue: 0, sales: 0 }
    })
  },
  productsCount: {
    type: Number,
    default: 0
  },
  lowStock: {
    type: Array,
    default: () => []
  },
  recentSales: {
    type: Array,
    default: () => []
  },
  notifications: {
    type: Array,
    default: () => []
  }
})

// Emits
const emit = defineEmits(['change-module'])

// Composables
const { 
  currentSession, 
  hasOpenSession, 
  isLoading: cashLoading,
  openSession,
  closeSession,
  loadCurrentSession
} = useCashSession()

// Estado
const loading = ref(false)
const selectedPeriod = ref('24H')

// Dashboard data structure
const dashboardData = ref({
  summary: {
    today_sales: { amount: 0, count: 0 },
    month_sales: { amount: 0, count: 0 },
    total_products: 0,
    low_stock_products: 0
  },
  charts: {
    daily_sales: [],
    hourly_sales: [],
    top_products: [],
    low_stock_products: []
  },
  recent_sales: []
})

// Períodos disponibles
const periods = [
  { label: '24H', value: '24H' },
  { label: '7D', value: '7D' },
  { label: '30D', value: '30D' }
]

// Computed
const currentDate = computed(() => {
  return new Date().toLocaleDateString('es-CO', { 
    year: 'numeric', 
    month: 'long', 
    day: 'numeric' 
  })
})

const cashAmount = computed(() => {
  if (!currentSession.value) return 0
  
  // Calcular total real de efectivo disponible
  const initialCash = parseFloat(currentSession.value.opening_amount || 0)
  
  // Leer ventas en efectivo desde closing_breakdown
  let cashSales = 0
  if (currentSession.value.closing_breakdown) {
    try {
      const breakdown = typeof currentSession.value.closing_breakdown === 'string' 
        ? JSON.parse(currentSession.value.closing_breakdown)
        : currentSession.value.closing_breakdown
      
      cashSales = parseFloat(breakdown.sales?.cash || 0)
    } catch (e) {
      cashSales = 0
    }
  }
  
  const expenses = parseFloat(currentSession.value.total_expenses || 0)
  
  return initialCash + cashSales - expenses
})

const growthPercentage = computed(() => {
  // Usar datos de dashboardData en lugar de props
  const today = dashboardData.value.summary.today_sales.amount || 0
  const yesterday = props.salesData?.yesterday?.revenue || 0
  
  if (yesterday === 0) return 0
  
  return Number(((today - yesterday) / yesterday * 100).toFixed(1))
})

const averageTicket = computed(() => {
  // Usar datos de dashboardData en lugar de props
  const sales = dashboardData.value.summary.today_sales.count || 0
  const revenue = dashboardData.value.summary.today_sales.amount || 0
  
  if (sales === 0) return 0
  
  return revenue / sales
})

// Computed para ventas de hoy (para usar en template)
const todaySalesData = computed(() => ({
  revenue: dashboardData.value.summary.today_sales.amount || 0,
  sales: dashboardData.value.summary.today_sales.count || 0
}))

const lowStockCount = computed(() => {
  // Usar datos reales de dashboardData, NO props (que pueden estar cacheados)
  return dashboardData.value.charts.low_stock_products?.length || 0
})

// Computed properties para transformar datos de dashboardData
const weeklyDataComputed = computed(() => {
  if (selectedPeriod.value === '24H') {
    return dashboardData.value.charts.hourly_sales || []
  }
  
  const dailySales = dashboardData.value.charts.daily_sales || []
  
  if (!dailySales.length) {
    return [
      { label: 'Lun', sales: 0 },
      { label: 'Mar', sales: 0 },
      { label: 'Mié', sales: 0 },
      { label: 'Jue', sales: 0 },
      { label: 'Vie', sales: 0 },
      { label: 'Sáb', sales: 0 },
      { label: 'Dom', sales: 0 }
    ]
  }
  
  return dailySales
})

const topProductsComputed = computed(() => {
  const products = dashboardData.value.charts.top_products || []
  
  if (!products.length) {
    return []
  }
  
  return products.slice(0, 5).map(product => ({
    id: product.id,
    name: product.name || 'Producto sin nombre',
    sold: product.sold || 0,
    revenue: product.revenue || 0,
    image: product.image_url || null
  }))
})

const recentSalesComputed = computed(() => {
  return dashboardData.value.recent_sales || props.recentSales || []
})

// Datos para gráfico de línea (usando datos reales)
const chartData = computed(() => {
  const data = weeklyDataComputed.value
  
  return {
    labels: data.map(d => d.label),
    datasets: [{
      label: 'Ingresos',
      data: data.map(d => d.sales || 0),
      borderColor: '#10b981',
      backgroundColor: (context) => {
        const ctx = context.chart.ctx
        const gradient = ctx.createLinearGradient(0, 0, 0, 300)
        gradient.addColorStop(0, 'rgba(16, 185, 129, 0.1)')
        gradient.addColorStop(1, 'rgba(16, 185, 129, 0)')
        return gradient
      },
      borderWidth: 2,
      fill: true,
      tension: 0.4,
      pointRadius: 0,
      pointHoverRadius: 6,
      pointHoverBackgroundColor: '#10b981',
      pointHoverBorderColor: '#fff',
      pointHoverBorderWidth: 2
    }]
  }
})

const chartOptions = computed(() => ({
  responsive: true,
  maintainAspectRatio: false,
  plugins: {
    legend: {
      display: false
    },
    tooltip: {
      backgroundColor: '#1f2937',
      titleColor: '#f9fafb',
      bodyColor: '#f9fafb',
      borderColor: '#374151',
      borderWidth: 1,
      padding: 12,
      displayColors: false,
      callbacks: {
        label: (context) => `$${formatCurrency(context.parsed.y)}`
      }
    }
  },
  scales: {
    x: {
      grid: {
        display: false
      },
      border: {
        display: false
      },
      ticks: {
        color: '#9ca3af',
        font: {
          size: 11,
          weight: 500
        }
      }
    },
    y: {
      grid: {
        color: 'rgba(156, 163, 175, 0.1)',
        drawBorder: false
      },
      border: {
        display: false
      },
      ticks: {
        color: '#9ca3af',
        font: {
          size: 11,
          weight: 500
        },
        callback: (value) => `$${formatCurrency(value / 1000)}k`
      }
    }
  },
  interaction: {
    intersect: false,
    mode: 'index'
  }
}))

// Top productos - usar datos reales de topProductsComputed
const topProducts = computed(() => {
  return topProductsComputed.value
})

const totalItems = computed(() => topProducts.value.length)

const productColors = ['#10b981', '#3b82f6', '#8b5cf6', '#f59e0b', '#ef4444']

const productChartData = computed(() => ({
  labels: topProducts.value.map(p => p.name),
  datasets: [{
    data: topProducts.value.map(p => p.revenue),
    backgroundColor: productColors,
    borderWidth: 0,
    spacing: 2
  }]
}))

const productChartOptions = computed(() => ({
  responsive: true,
  maintainAspectRatio: true,
  cutout: '85%', // Anillo muy delgado
  plugins: {
    legend: {
      display: false
    },
    tooltip: {
      backgroundColor: '#1f2937',
      titleColor: '#f9fafb',
      bodyColor: '#f9fafb',
      borderColor: '#374151',
      borderWidth: 1,
      padding: 12,
      displayColors: true,
      callbacks: {
        label: (context) => `$${formatCurrency(context.parsed)}`
      }
    }
  }
}))

// Funciones utilitarias
const formatCurrency = (value) => {
  if (!value) return '0'
  return new Intl.NumberFormat('es-CO', {
    minimumFractionDigits: 0,
    maximumFractionDigits: 2
  }).format(value)
}

const formatDate = (dateString) => {
  const date = new Date(dateString)
  const today = new Date()
  const isToday = date.toDateString() === today.toDateString()
  
  if (isToday) {
    return date.toLocaleTimeString('es-CO', { hour: '2-digit', minute: '2-digit' })
  }
  
  return date.toLocaleDateString('es-CO', { 
    month: 'short', 
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
}

// Función principal para cargar datos del dashboard
const loadDashboardData = async (period = '24H') => {
  try {
    loading.value = true
    
    // 🧹 Limpiar datos previos al inicio (evitar cache)
    dashboardData.value.summary.low_stock_products = 0
    dashboardData.value.charts.low_stock_products = []
    
    // Mapear períodos del UI a períodos de API
    const periodMap = {
      '24H': 'today',
      '7D': 'week',
      '30D': 'month'
    }
    const apiPeriod = periodMap[period] || 'week'
    
    // Cargar datos en paralelo
    const [todaySalesData, dailySalesData, hourlyData, topProductsData, lowStockData, productStatsData] = await Promise.all([
      reportsService.getSalesData('today'),
      apiPeriod !== 'today' ? reportsService.getDailySales(apiPeriod) : Promise.resolve({ success: true, data: [] }),
      apiPeriod === 'today' ? cashReportsService.getHourlyEfficiency('today') : Promise.resolve({ success: true, data: [] }),
      reportsService.getTopProducts('month', 5),
      reportsService.getLowStockProducts(),
      reportsService.getProductStats()
    ])
    
    // Actualizar ventas de hoy - SIEMPRE llamar a getVentasHoyColombia
    const ventasHoyColombia = await reportsService.getVentasHoyColombia()
    dashboardData.value.summary.today_sales = {
      amount: ventasHoyColombia.total || 0,
      count: ventasHoyColombia.transacciones || 0
    }
    
    // Actualizar datos por día
    if (dailySalesData.success && Array.isArray(dailySalesData.data)) {
      if (dailySalesData.dataWithDates && Array.isArray(dailySalesData.dataWithDates)) {
        dashboardData.value.charts.daily_sales = dailySalesData.dataWithDates.map(item => ({
          date: item.date,
          dateObject: item.dateObject,
          label: item.dateObject.toLocaleDateString('es-ES', { weekday: 'short' }),
          sales: item.total || 0
        }))
      } else {
        dashboardData.value.charts.daily_sales = dailySalesData.data.map((sales, index) => {
          const now = new Date()
          const targetDate = new Date(now.getTime() - (5 * 60 * 60 * 1000))
          targetDate.setDate(targetDate.getDate() - (dailySalesData.data.length - 1 - index))
          
          return {
            date: targetDate.toISOString().split('T')[0],
            label: targetDate.toLocaleDateString('es-ES', { weekday: 'short' }),
            sales: sales || 0,
            dateObject: targetDate
          }
        })
      }
    }
    
    // Actualizar datos por hora
    if (hourlyData.success && hourlyData.data) {
      const allHoursData = hourlyData.data.hourly_data || []
      
      if (allHoursData.length > 0) {
        // Usar directamente los datos del backend (ya vienen con 24 horas completas)
        dashboardData.value.charts.hourly_sales = allHoursData.map(h => ({
          label: h.hour,
          sales: h.sales || 0,
          transactions: h.transactions || 0
        }))
      } else {
        // Si no hay datos, crear array vacío con algunas horas
        const nowColombia = new Date().toLocaleString('es-CO', { 
          timeZone: 'America/Bogota',
          hour: '2-digit',
          hour12: false
        })
        const currentHour = parseInt(nowColombia)
        
        const chartData = []
        for (let h = 0; h <= currentHour; h++) {
          chartData.push({
            label: `${h.toString().padStart(2, '0')}:00`,
            sales: 0,
            transactions: 0
          })
        }
        
        dashboardData.value.charts.hourly_sales = chartData
      }
    }
    
    // Actualizar top productos
    if (topProductsData.success && Array.isArray(topProductsData.data)) {
      dashboardData.value.charts.top_products = topProductsData.data
    }
    
    // Actualizar stock bajo
    if (lowStockData.success && Array.isArray(lowStockData.data)) {
      dashboardData.value.summary.low_stock_products = lowStockData.data.length
      dashboardData.value.charts.low_stock_products = lowStockData.data
    } else {
      // Si falla o no hay datos, limpiar valores
      dashboardData.value.summary.low_stock_products = 0
      dashboardData.value.charts.low_stock_products = []
    }
    
    // Actualizar total de productos
    if (productStatsData.success) {
      dashboardData.value.summary.total_products = productStatsData.data.totalProducts
    }
    
    // Cargar transacciones recientes
    const recentTransactions = await reportsService.getRecentTransactions(4)
    if (recentTransactions.success) {
      dashboardData.value.recent_sales = recentTransactions.data
    }
    
  } catch (err) {
    console.error('❌ Error cargando datos del dashboard:', err)
  } finally {
    loading.value = false
  }
}

// Watchers
watch(selectedPeriod, async (newPeriod) => {
  await loadDashboardData(newPeriod)
})

// Handlers
const refreshData = async () => {
  loading.value = true
  await loadCurrentSession()
  await loadDashboardData(selectedPeriod.value)
  loading.value = false
}

const handleCloseCash = async () => {
  try {
    await closeSession()
  } catch (error) {
    console.error('Error cerrando caja:', error)
  }
}

const handleOpenCash = async () => {
  try {
    await openSession({ initial_cash: 0 })
  } catch (error) {
    console.error('Error abriendo caja:', error)
  }
}

const handleNewSale = () => {
  emit('change-module', 'pos')
}

const handleGoToInventory = () => {
  emit('change-module', 'stock')
}

// Watch para cambios en período
watch(selectedPeriod, async (newPeriod) => {
  await loadDashboardData(newPeriod)
})

// Lifecycle
onMounted(async () => {
  await loadCurrentSession()
  await loadDashboardData(selectedPeriod.value)
})
</script>

<style scoped>
/* Smooth transitions */
* {
  transition-property: background-color, border-color, color, fill, stroke;
  transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
  transition-duration: 150ms;
}
</style>
