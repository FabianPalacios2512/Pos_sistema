<template>
  <!-- Executive Dashboard - Estilo Premium inspirado en el AI Chat -->
  <div class="min-h-screen bg-gradient-to-b from-gray-50 via-gray-100 to-gray-200 dark:bg-gradient-to-b dark:from-[#141417] dark:via-slate-900 dark:to-[#0a0a0c] font-sans antialiased">
    
    <!-- Container Principal -->
    <div class="max-w-[1600px] mx-auto px-6 lg:px-10 py-8 space-y-8">
      
      <!-- Header Premium - Sin iconos, tipografía clara -->
      <div class="flex items-center justify-between">
        <div class="space-y-1.5">
          <h1 class="text-2xl lg:text-3xl font-semibold text-slate-900 dark:text-white tracking-tight">
            {{ isVendedor ? 'Mi Panel' : 'Panel de Control' }}
          </h1>
          <p class="text-sm text-gray-500 dark:text-zinc-500 font-normal flex items-center gap-2">
            <span class="inline-flex h-2 w-2 rounded-full bg-emerald-500 animate-pulse"></span>
            {{ isVendedor ? 'Resumen personal' : 'Sistema Operativo' }} • {{ currentDate }}
          </p>
        </div>
        
        <div class="flex items-center gap-3">
          <button 
            @click="refreshData" 
            :disabled="loading"
            class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-medium text-gray-600 dark:text-zinc-300 bg-white dark:bg-zinc-900 border border-gray-200 dark:border-zinc-800 rounded-xl hover:bg-gray-50 dark:hover:bg-zinc-800 hover:border-gray-300 dark:hover:border-zinc-700 transition-all duration-200"
          >
            <svg :class="['w-4 h-4', loading ? 'animate-spin' : '']" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
            </svg>
            Refrescar
          </button>
          
          <button 
            @click="handleNewSale"
            class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-semibold text-white bg-emerald-600 hover:bg-emerald-700 dark:bg-emerald-600 dark:hover:bg-emerald-500 rounded-xl transition-all duration-200 shadow-lg shadow-emerald-500/25 dark:shadow-emerald-900/30"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Nueva Venta
          </button>
        </div>
      </div>

      <!-- Grid de KPIs Premium (4 columnas) -->
      <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4">
        
        <!-- 1. Estado de Caja -->
        <div class="group relative bg-white dark:bg-zinc-900/80 backdrop-blur-sm rounded-2xl p-5 border border-gray-200/80 dark:border-zinc-800/60 hover:border-gray-300 dark:hover:border-zinc-700 transition-all duration-300 hover:shadow-lg dark:hover:shadow-black/20">
          <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0"
                 :class="hasOpenSession 
                   ? 'bg-emerald-100 dark:bg-emerald-950/60 border border-emerald-200/50 dark:border-emerald-800/30' 
                   : 'bg-gray-100 dark:bg-zinc-800/60 border border-gray-200/50 dark:border-zinc-700/30'">
              <svg class="w-5 h-5" :class="hasOpenSession ? 'text-emerald-600 dark:text-emerald-400' : 'text-gray-400 dark:text-zinc-500'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path v-if="hasOpenSession" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
              </svg>
            </div>
            <div class="flex-1 min-w-0">
              <div class="flex items-center gap-2 mb-1">
                <p class="text-xs font-medium text-gray-500 dark:text-zinc-500 uppercase tracking-wide">Estado de Caja</p>
                <span class="px-1.5 py-0.5 text-[10px] font-semibold rounded-md"
                      :class="hasOpenSession 
                        ? 'bg-emerald-100 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-400' 
                        : 'bg-gray-100 dark:bg-zinc-800 text-gray-600 dark:text-zinc-400'">
                  {{ hasOpenSession ? 'Abierta' : 'Cerrada' }}
                </span>
              </div>
              <p class="text-2xl font-bold text-slate-900 dark:text-white tracking-tight">
                ${{ formatCurrency(cashAmount) }}
              </p>
              <p v-if="hasOpenSession" class="text-xs text-gray-500 dark:text-zinc-500 mt-0.5">
                Sesión #{{ currentSession?.id }}
              </p>
            </div>
          </div>
        </div>

        <!-- 2. Ventas Hoy -->
        <div class="group relative bg-white dark:bg-zinc-900/80 backdrop-blur-sm rounded-2xl p-5 border border-gray-200/80 dark:border-zinc-800/60 hover:border-gray-300 dark:hover:border-zinc-700 transition-all duration-300 hover:shadow-lg dark:hover:shadow-black/20">
          <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-blue-100 dark:bg-blue-950/60 border border-blue-200/50 dark:border-blue-800/30 flex items-center justify-center flex-shrink-0">
              <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
              </svg>
            </div>
            <div class="flex-1 min-w-0">
              <div class="flex items-center justify-between mb-1">
                <p class="text-xs font-medium text-gray-500 dark:text-zinc-500 uppercase tracking-wide">{{ isVendedor ? 'Mis Ventas Hoy' : 'Ventas Hoy' }}</p>
                <span 
                  class="inline-flex items-center gap-0.5 px-1.5 py-0.5 text-[10px] font-semibold rounded-md"
                  :class="growthPercentage >= 0 
                    ? 'bg-emerald-100 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-400' 
                    : 'bg-rose-100 dark:bg-rose-950 text-rose-700 dark:text-rose-400'"
                >
                  <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" :d="growthPercentage >= 0 ? 'M5 10l7-7m0 0l7 7m-7-7v18' : 'M19 14l-7 7m0 0l-7-7m7 7V3'"></path>
                  </svg>
                  {{ Math.abs(growthPercentage) }}%
                </span>
              </div>
              <p class="text-2xl font-bold text-slate-900 dark:text-white tracking-tight">
                ${{ formatCurrency(todaySalesData.revenue) }}
              </p>
              <p class="text-xs text-gray-500 dark:text-zinc-500 mt-0.5">
                {{ todaySalesData.sales }} transacciones
              </p>
            </div>
          </div>
        </div>

        <!-- 3. Transacciones -->
        <div class="group relative bg-white dark:bg-zinc-900/80 backdrop-blur-sm rounded-2xl p-5 border border-gray-200/80 dark:border-zinc-800/60 hover:border-gray-300 dark:hover:border-zinc-700 transition-all duration-300 hover:shadow-lg dark:hover:shadow-black/20">
          <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-violet-100 dark:bg-violet-950/60 border border-violet-200/50 dark:border-violet-800/30 flex items-center justify-center flex-shrink-0">
              <svg class="w-5 h-5 text-violet-600 dark:text-violet-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
              </svg>
            </div>
            <div class="flex-1 min-w-0">
              <p class="text-xs font-medium text-gray-500 dark:text-zinc-500 uppercase tracking-wide mb-1">{{ isVendedor ? 'Mis Transacciones' : 'Transacciones' }}</p>
              <p class="text-2xl font-bold text-slate-900 dark:text-white tracking-tight">
                {{ todaySalesData.sales }}
              </p>
              <p class="text-xs text-gray-500 dark:text-zinc-500 mt-0.5">
                Ticket promedio: <span class="font-semibold text-slate-700 dark:text-zinc-300">${{ formatCurrency(averageTicket) }}</span>
              </p>
            </div>
          </div>
        </div>

        <!-- 4. Alertas/Stock -->
        <div class="group relative bg-white dark:bg-zinc-900/80 backdrop-blur-sm rounded-2xl p-5 border border-gray-200/80 dark:border-zinc-800/60 hover:border-gray-300 dark:hover:border-zinc-700 transition-all duration-300 hover:shadow-lg dark:hover:shadow-black/20">
          <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0"
                 :class="lowStockCount > 0 
                   ? 'bg-amber-100 dark:bg-amber-950/60 border border-amber-200/50 dark:border-amber-800/30' 
                   : 'bg-gray-100 dark:bg-zinc-800/60 border border-gray-200/50 dark:border-zinc-700/30'">
              <svg class="w-5 h-5" :class="lowStockCount > 0 ? 'text-amber-600 dark:text-amber-400' : 'text-gray-400 dark:text-zinc-500'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
              </svg>
            </div>
            <div class="flex-1 min-w-0">
              <p class="text-xs font-medium text-gray-500 dark:text-zinc-500 uppercase tracking-wide mb-1">Alertas Stock</p>
              <p class="text-2xl font-bold tracking-tight"
                 :class="lowStockCount > 0 ? 'text-amber-600 dark:text-amber-400' : 'text-slate-900 dark:text-white'">
                {{ lowStockCount }}
              </p>
              <p class="text-xs text-gray-500 dark:text-zinc-500 mt-0.5">
                {{ lowStockCount === 0 ? 'Todo en orden' : 'Productos críticos' }}
              </p>
            </div>
          </div>
          <!-- Link sutil -->
          <button 
            @click="handleGoToInventory"
            class="absolute bottom-4 right-4 text-xs font-medium text-gray-400 dark:text-zinc-600 hover:text-gray-600 dark:hover:text-zinc-400 transition-colors"
          >
            Ver Inventario →
          </button>
        </div>
      </div>

      <!-- Grid de Gráficos (2 columnas) -->
      <div class="grid grid-cols-1 xl:grid-cols-2 gap-5">
        
        <!-- Gráfico de Ingresos -->
        <div class="bg-white dark:bg-zinc-900/80 backdrop-blur-sm rounded-2xl p-6 border border-gray-200/80 dark:border-zinc-800/60">
          <div class="flex items-center justify-between mb-6">
            <div>
              <h3 class="text-base font-semibold text-slate-900 dark:text-white">Análisis de Ingresos</h3>
              <p class="text-xs text-gray-500 dark:text-zinc-500 mt-0.5">Tendencia de ventas en el tiempo</p>
            </div>
            
            <!-- Tabs de período - Estilo pill más sutil -->
            <div class="inline-flex items-center gap-0.5 p-1 bg-gray-100 dark:bg-zinc-800/80 rounded-lg border border-gray-200/50 dark:border-zinc-700/30">
              <button 
                v-for="period in periods" 
                :key="period.value"
                @click="selectedPeriod = period.value"
                class="px-3 py-1.5 text-xs font-medium rounded-md transition-all duration-200"
                :class="selectedPeriod === period.value 
                  ? 'bg-white dark:bg-zinc-700 text-gray-900 dark:text-white shadow-sm' 
                  : 'text-gray-500 dark:text-zinc-400 hover:text-gray-700 dark:hover:text-zinc-300'"
              >
                {{ period.label }}
              </button>
            </div>
          </div>
          
          <div class="h-[280px] flex items-center justify-center" :class="loading ? 'opacity-50' : 'opacity-100'">
            <Line :data="chartData" :options="chartOptions" />
          </div>
        </div>

        <!-- Gráfico de Top Productos -->
        <div class="bg-white dark:bg-zinc-900/80 backdrop-blur-sm rounded-2xl p-6 border border-gray-200/80 dark:border-zinc-800/60">
          <div class="mb-5">
            <h3 class="text-base font-semibold text-slate-900 dark:text-white">Top Productos</h3>
            <p class="text-xs text-gray-500 dark:text-zinc-500 mt-0.5">Distribución de ingresos</p>
          </div>
          
          <div class="flex items-center gap-6">
            <!-- Gráfico circular -->
            <div class="w-44 h-44 relative flex-shrink-0">
              <Doughnut v-if="!loading && topProducts.length > 0" :data="productChartData" :options="productChartOptions" />
              <div v-else class="w-full h-full rounded-full bg-gray-100 dark:bg-zinc-800/50 flex items-center justify-center">
                <span class="text-xs text-gray-400 dark:text-zinc-600">Sin datos</span>
              </div>
              <div v-if="topProducts.length > 0" class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none">
                <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ totalItems }}</p>
                <p class="text-[10px] text-gray-500 dark:text-zinc-500 uppercase tracking-wider font-medium">Items</p>
              </div>
            </div>
            
            <!-- Leyenda -->
            <div class="flex-1 min-w-0 overflow-hidden space-y-2">
              <div v-for="(product, index) in topProducts.slice(0, 5)" :key="index" 
                   class="flex items-center justify-between py-2 px-2.5 rounded-lg hover:bg-gray-50 dark:hover:bg-zinc-800/50 transition-colors group cursor-default">
                <div class="flex items-center gap-2.5 min-w-0 flex-1 overflow-hidden">
                  <div class="w-2 h-2 rounded-full flex-shrink-0" :style="{ backgroundColor: productColors[index] }"></div>
                  <span class="text-sm text-gray-700 dark:text-zinc-300 truncate group-hover:text-gray-900 dark:group-hover:text-white transition-colors" :title="product.name">{{ product.name }}</span>
                </div>
                <span class="text-sm font-semibold text-gray-900 dark:text-white ml-3 flex-shrink-0 tabular-nums">${{ formatCurrency(product.revenue) }}</span>
              </div>
              
              <!-- Estado vacío -->
              <div v-if="topProducts.length === 0" class="text-center py-8">
                <p class="text-sm text-gray-400 dark:text-zinc-600">Sin productos vendidos</p>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Últimas Transacciones -->
      <div class="bg-white dark:bg-zinc-900/80 backdrop-blur-sm rounded-2xl border border-gray-200/80 dark:border-zinc-800/60 overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-100 dark:border-zinc-800/60">
          <h3 class="text-base font-semibold text-gray-900 dark:text-white">Últimas Transacciones</h3>
          <p class="text-xs text-gray-500 dark:text-zinc-500 mt-0.5">Actividad reciente del sistema</p>
        </div>
        
        <div class="divide-y divide-gray-100 dark:divide-zinc-800/60">
          <template v-if="recentSalesComputed && recentSalesComputed.length > 0">
            <div v-for="sale in recentSalesComputed.slice(0, 5)" :key="sale.id" 
                 class="flex items-center justify-between px-6 py-4 hover:bg-gray-50/50 dark:hover:bg-zinc-800/30 transition-colors">
              <div class="flex items-center gap-4 min-w-0 flex-1">
                <div class="w-10 h-10 rounded-xl bg-emerald-100 dark:bg-emerald-950/50 border border-emerald-200/50 dark:border-emerald-800/30 flex items-center justify-center flex-shrink-0">
                  <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                  </svg>
                </div>
                <div class="min-w-0 flex-1">
                  <p class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ sale.customer?.name || 'Cliente General' }}</p>
                  <p class="text-xs text-gray-500 dark:text-zinc-500">{{ formatDate(sale.created_at) }}</p>
                </div>
              </div>
              <div class="text-right ml-4 flex-shrink-0">
                <p class="text-sm font-semibold text-gray-900 dark:text-white tabular-nums">${{ formatCurrency(sale.total) }}</p>
                <span class="inline-flex items-center gap-1 text-xs text-emerald-600 dark:text-emerald-400 font-medium">
                  <span class="w-1 h-1 bg-emerald-500 rounded-full"></span>
                  Pagado
                </span>
              </div>
            </div>
          </template>
          
          <!-- Estado vacío -->
          <div v-else class="flex flex-col items-center justify-center py-16 text-center">
            <div class="w-14 h-14 rounded-2xl bg-gray-100 dark:bg-zinc-800/60 border border-gray-200/50 dark:border-zinc-700/30 flex items-center justify-center mb-4">
              <svg class="w-6 h-6 text-gray-400 dark:text-zinc-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
              </svg>
            </div>
            <p class="text-sm font-medium text-gray-700 dark:text-zinc-300 mb-0.5">No hay transacciones recientes</p>
            <p class="text-xs text-gray-500 dark:text-zinc-500">Las ventas aparecerán aquí</p>
          </div>
        </div>
      </div>

    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted, watch } from 'vue'
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
import { useScreenContext, formatDashboardContext } from '@/composables/useScreenContext'
import { useUIContextStore } from '@/store/uiContextStore'
import { useModuleNavigation } from '@/composables/useModuleNavigation'
import { useAuth } from '../store/auth.js'

Chart.register(CategoryScale, LinearScale, PointElement, LineElement, ArcElement, Title, Tooltip, Legend, Filler)

const auth = useAuth()
const isVendedor = computed(() => auth.hasRole('Vendedor'))
const vendedorUserId = computed(() => isVendedor.value ? auth.user.value?.id : null)

// 🧠 Composable para contexto de pantalla (IA Chat de texto)
const { setContext, updateData } = useScreenContext()

// 🧠 Store para contexto de IA de voz
const uiContextStore = useUIContextStore()

// 🧠 Navegación de módulos (para escuchar cambios de filtro)
const { onModuleChange } = useModuleNavigation()

// Props
const props = defineProps({
  moduleName: String,
  queryParams: Object,
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
const emit = defineEmits(['navigate', 'changeModule', 'change-module', 'openQuotationInPos', 'refresh'])

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
    const ventasHoyColombia = await reportsService.getVentasHoyColombia(vendedorUserId.value)
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
    
    // 🌐 ACTUALIZAR DATOS GLOBALES DEL NEGOCIO
    // Estos datos estarán disponibles para la IA desde CUALQUIER módulo
    uiContextStore.updateGlobalBusinessSection('ventas', {
      ventasHoy: dashboardData.value.summary.today_sales.amount,
      transaccionesHoy: dashboardData.value.summary.today_sales.count,
      ventasMes: dashboardData.value.summary.month_sales.amount,
      transaccionesMes: dashboardData.value.summary.month_sales.count,
      ticketPromedio: averageTicket.value
    })
    
    uiContextStore.updateGlobalBusinessSection('caja', {
      estado: hasOpenSession.value ? 'abierta' : 'cerrada',
      montoActual: cashAmount.value
    })
    
    uiContextStore.updateGlobalBusinessSection('alertas', {
      productosStockBajo: (dashboardData.value.charts.low_stock_products || []).map(p => ({
        nombre: p.name,
        stock: p.current_stock || p.stock
      }))
    })
    
    uiContextStore.updateGlobalBusinessSection('rankings', {
      topProductosHoy: (dashboardData.value.charts.top_products || []).slice(0, 5).map(p => ({
        nombre: p.name || p.nombre,
        ingresos: p.revenue || p.ingresos || 0
      }))
    })
    
  } catch (err) {
    console.error('❌ Error cargando datos del dashboard:', err)
  } finally {
    loading.value = false
  }
}

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

// 🧠 Función para actualizar el contexto de pantalla para la IA
const updateScreenContextForAI = () => {
  // Analizar tendencia de la gráfica
  const chartData = weeklyDataComputed.value
  let chartTrend = 'Sin datos de tendencia'
  
  if (chartData.length > 0) {
    // Encontrar el día con más ventas
    const maxSalesDay = chartData.reduce((prev, current) => 
      (prev.sales > current.sales) ? prev : current
    )
    
    if (maxSalesDay.sales > 0) {
      chartTrend = `Pico de ventas el ${maxSalesDay.label} con $${formatCurrency(maxSalesDay.sales)}`
    } else {
      chartTrend = 'Sin ventas registradas en el período seleccionado'
    }
  }

  // Formatear datos usando el helper
  const contextData = formatDashboardContext({
    hasOpenSession: hasOpenSession.value,
    cashAmount: cashAmount.value,
    todaySales: todaySalesData.value.revenue,
    transactionsCount: todaySalesData.value.sales,
    averageTicket: averageTicket.value,
    lowStockCount: lowStockCount.value,
    topProducts: topProductsComputed.value,
    chartTrend,
    recentTransactions: recentSalesComputed.value
  })
  
  // Agregar período actual del gráfico al contexto
  const periodoLabels = {
    '24H': 'últimas 24 horas',
    '7D': 'últimos 7 días',
    '30D': 'últimos 30 días'
  }
  contextData.periodoGrafico = {
    valor: selectedPeriod.value,
    descripcion: periodoLabels[selectedPeriod.value] || selectedPeriod.value,
    opcionesDisponibles: ['24H (hoy)', '7D (semana)', '30D (mes)']
  }

  // Establecer el contexto para IA de texto
  setContext({
    screen: 'Panel de Control',
    description: 'Dashboard principal con métricas del negocio: estado de caja, ventas del día, alertas de stock, productos más vendidos y tendencias.',
    data: contextData
  })
  
  // 🧠 Actualizar también el store de UI para IA de voz
  uiContextStore.setCurrentModule('dashboard')
  uiContextStore.setScreenData(contextData)
}

// Watchers (solo uno)
watch(selectedPeriod, async (newPeriod) => {
  await loadDashboardData(newPeriod)
})

// 🧠 Watcher para responder a comandos de la IA (queryParams)
watch(
  () => props.queryParams,
  (newParams) => {
    if (newParams?.filter) {
      // Mapear filtros de texto a valores del período
      const filterMap = {
        '7 días': '7D',
        '7 dias': '7D',
        '7d': '7D',
        'semana': '7D',
        'semanal': '7D',
        '30 días': '30D',
        '30 dias': '30D',
        '30d': '30D',
        'mes': '30D',
        'mensual': '30D',
        '24 horas': '24H',
        '24h': '24H',
        'hoy': '24H',
        'día': '24H',
        'dia': '24H'
      }
      
      const normalizedFilter = newParams.filter.toLowerCase().trim()
      const mappedPeriod = filterMap[normalizedFilter]
      
      if (mappedPeriod) {
        // Forzar el cambio aunque sea el mismo valor (por si el watcher no detectó)
        if (mappedPeriod === selectedPeriod.value) {
          // Forzar recarga de datos
          loadDashboardData(mappedPeriod)
        } else {
          selectedPeriod.value = mappedPeriod
        }
      }
    }
  },
  { immediate: true, deep: true }
)

// Watcher para actualizar contexto cuando cambien los datos del dashboard
watch(
  () => ({
    sales: todaySalesData.value,
    cash: cashAmount.value,
    stock: lowStockCount.value,
    products: topProductsComputed.value
  }),
  () => {
    updateScreenContextForAI()
  },
  { deep: true }
)

// Función para aplicar filtro de período
const applyPeriodFilter = (filter) => {
  const filterMap = {
    '7 días': '7D',
    '7 dias': '7D',
    '7d': '7D',
    'semana': '7D',
    'semanal': '7D',
    '30 días': '30D',
    '30 dias': '30D',
    '30d': '30D',
    'mes': '30D',
    'mensual': '30D',
    '24 horas': '24H',
    '24h': '24H',
    'hoy': '24H',
    'día': '24H',
    'dia': '24H'
  }
  
  const normalizedFilter = filter.toLowerCase().trim()
  const mappedPeriod = filterMap[normalizedFilter]
  
  if (mappedPeriod) {
    console.log(`📊 [Dashboard] Aplicando filtro de período: ${filter} → ${mappedPeriod}`)
    selectedPeriod.value = mappedPeriod
    // Forzar recarga de datos
    loadDashboardData(mappedPeriod)
  }
}

// Lifecycle
onMounted(async () => {
  await loadCurrentSession()
  await loadDashboardData(selectedPeriod.value)
  
  // 🧠 Establecer contexto inicial para la IA después de cargar datos
  updateScreenContextForAI()
  
  // 🧠 Escuchar cambios de módulo/filtro desde la IA de voz
  onModuleChange((moduleName, queryParams) => {
    if (moduleName === 'dashboard' && queryParams?.filter) {
      applyPeriodFilter(queryParams.filter)
    }
  })
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
