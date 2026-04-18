<template>
  <div class="min-h-screen font-sans bg-gray-50 dark:bg-[#0f1117] text-gray-900 dark:text-gray-100">

    <!-- ═══ Global Filters Bar ═══ -->
    <div class="sticky top-0 z-30 bg-white/80 dark:bg-[#161820]/80 backdrop-blur-md border-b border-gray-200 dark:border-zinc-800">
      <div class="max-w-[1920px] mx-auto flex items-center justify-between px-6 py-3">
        <div class="flex items-center gap-2">
          <h1 class="text-lg font-semibold tracking-tight text-gray-900 dark:text-white">Panel de Control</h1>
          <span class="hidden sm:inline-flex h-1.5 w-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
          <span class="hidden sm:inline text-xs text-gray-400 dark:text-zinc-500">{{ currentDateLabel }}</span>
        </div>

        <div class="flex items-center gap-2">
          <!-- Warehouse filter -->
          <select
            v-model="filters.warehouse_id"
            @change="loadData"
            class="h-8 px-3 text-xs font-medium rounded-md border border-gray-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-gray-700 dark:text-zinc-300 focus:outline-none focus:ring-1 focus:ring-blue-500 cursor-pointer"
          >
            <option :value="null">Todas las sedes</option>
            <option v-for="w in warehouses" :key="w.id" :value="w.id">{{ w.name }}</option>
          </select>

          <!-- Date range -->
          <input
            type="date"
            v-model="filters.date_from"
            @change="loadData"
            class="h-8 px-2 text-xs rounded-md border border-gray-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-gray-700 dark:text-zinc-300 focus:outline-none focus:ring-1 focus:ring-blue-500"
          />
          <span class="text-xs text-gray-400">—</span>
          <input
            type="date"
            v-model="filters.date_to"
            @change="loadData"
            class="h-8 px-2 text-xs rounded-md border border-gray-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-gray-700 dark:text-zinc-300 focus:outline-none focus:ring-1 focus:ring-blue-500"
          />

          <!-- Quick ranges -->
          <div class="hidden md:flex items-center border border-gray-200 dark:border-zinc-700 rounded-md overflow-hidden">
            <button v-for="r in quickRanges" :key="r.key"
              @click="applyRange(r.key)"
              class="h-8 px-3 text-xs font-medium transition-colors"
              :class="activeRange === r.key
                ? 'bg-gray-900 dark:bg-white text-white dark:text-gray-900'
                : 'bg-white dark:bg-zinc-800 text-gray-500 dark:text-zinc-400 hover:bg-gray-50 dark:hover:bg-zinc-700'"
            >{{ r.label }}</button>
          </div>

          <!-- Refresh -->
          <button @click="loadData" :disabled="loading"
            class="h-8 w-8 flex items-center justify-center rounded-md border border-gray-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 hover:bg-gray-50 dark:hover:bg-zinc-700 transition-colors">
            <svg :class="['w-3.5 h-3.5 text-gray-500 dark:text-zinc-400', loading && 'animate-spin']" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
            </svg>
          </button>

          <!-- New Sale -->
          <button @click="$emit('change-module', 'pos')"
            class="h-8 px-4 text-xs font-semibold rounded-md bg-gray-900 dark:bg-white text-white dark:text-gray-900 hover:bg-gray-800 dark:hover:bg-gray-100 transition-colors">
            + Nueva Venta
          </button>
        </div>
      </div>
    </div>

    <!-- ═══ Loading overlay ═══ -->
    <div v-if="loading && !hasData" class="flex items-center justify-center py-32">
      <svg class="animate-spin w-6 h-6 text-gray-400" fill="none" viewBox="0 0 24 24">
        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z" />
      </svg>
      <span class="ml-3 text-sm text-gray-500">Cargando métricas…</span>
    </div>

    <!-- ═══ Main Grid ═══ -->
    <div v-else class="max-w-[1920px] mx-auto px-6 py-5 space-y-4">

      <!-- ROW 1: Core Financial KPIs -->
      <div class="grid grid-cols-2 lg:grid-cols-5 gap-3">
        <!-- Revenue -->
        <div class="bg-white dark:bg-[#1a1d27] rounded-lg border border-gray-200 dark:border-zinc-800 p-4">
          <div class="flex items-center justify-between mb-1">
            <span class="text-[11px] font-medium uppercase tracking-wider text-gray-400 dark:text-zinc-500">Ingresos</span>
            <TrendBadge :value="kpis.revenue_growth" />
          </div>
          <p class="text-2xl font-bold tabular-nums tracking-tight">${{ fmt(kpis.total_revenue) }}</p>
          <p class="text-[11px] text-gray-400 dark:text-zinc-500 mt-1">{{ kpis.tx_count }} transacciones</p>
        </div>

        <!-- Gross Margin -->
        <div class="bg-white dark:bg-[#1a1d27] rounded-lg border border-gray-200 dark:border-zinc-800 p-4">
          <div class="flex items-center justify-between mb-1">
            <span class="text-[11px] font-medium uppercase tracking-wider text-gray-400 dark:text-zinc-500">Margen Bruto</span>
          </div>
          <p class="text-2xl font-bold tabular-nums tracking-tight">{{ kpis.gross_margin }}%</p>
          <p class="text-[11px] text-gray-400 dark:text-zinc-500 mt-1">{{ kpis.total_cost > 0 ? `Costo: $${fmt(kpis.total_cost)}` : 'Sin costos registrados' }}</p>
        </div>

        <!-- Average Ticket -->
        <div class="bg-white dark:bg-[#1a1d27] rounded-lg border border-gray-200 dark:border-zinc-800 p-4">
          <div class="flex items-center justify-between mb-1">
            <span class="text-[11px] font-medium uppercase tracking-wider text-gray-400 dark:text-zinc-500">Ticket Promedio</span>
            <TrendBadge :value="kpis.ticket_growth" />
          </div>
          <p class="text-2xl font-bold tabular-nums tracking-tight">${{ fmt(kpis.avg_ticket) }}</p>
          <p class="text-[11px] text-gray-400 dark:text-zinc-500 mt-1">vs periodo anterior</p>
        </div>

        <!-- Inventory Value -->
        <div class="bg-white dark:bg-[#1a1d27] rounded-lg border border-gray-200 dark:border-zinc-800 p-4">
          <div class="flex items-center justify-between mb-1">
            <span class="text-[11px] font-medium uppercase tracking-wider text-gray-400 dark:text-zinc-500">Capital Invertido</span>
          </div>
          <p class="text-2xl font-bold tabular-nums tracking-tight">${{ fmt(kpis.inventory_value) }}</p>
          <p class="text-[11px] text-gray-400 dark:text-zinc-500 mt-1">costo × stock</p>
        </div>

        <!-- Net Profit -->
        <div class="bg-white dark:bg-[#1a1d27] rounded-lg border border-gray-200 dark:border-zinc-800 p-4">
          <div class="flex items-center justify-between mb-1">
            <span class="text-[11px] font-medium uppercase tracking-wider text-gray-400 dark:text-zinc-500">Utilidad Neta</span>
          </div>
          <p class="text-2xl font-bold tabular-nums tracking-tight" :class="kpis.net_profit >= 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400'">
            ${{ fmt(kpis.net_profit) }}
          </p>
          <p class="text-[11px] text-gray-400 dark:text-zinc-500 mt-1">Gastos: ${{ fmt(kpis.total_expenses) }}</p>
        </div>
      </div>

      <!-- ROW 2: Main Charts -->
      <div class="grid grid-cols-1 xl:grid-cols-3 gap-3">
        <!-- Sales vs Expenses Chart (spans 2 cols) -->
        <div class="xl:col-span-2 bg-white dark:bg-[#1a1d27] rounded-lg border border-gray-200 dark:border-zinc-800 p-5">
          <div class="flex items-center justify-between mb-4">
            <div>
              <h3 class="text-sm font-semibold">Ventas vs Gastos</h3>
              <p class="text-[11px] text-gray-400 dark:text-zinc-500">Evolución diaria del periodo seleccionado</p>
            </div>
          </div>
          <div class="h-[260px]">
            <Bar v-if="salesExpensesChartData.labels.length" :data="salesExpensesChartData" :options="barChartOptions" />
            <div v-else class="h-full flex items-center justify-center text-xs text-gray-400">Sin datos en el periodo</div>
          </div>
        </div>

        <!-- Warehouse Ranking -->
        <div class="bg-white dark:bg-[#1a1d27] rounded-lg border border-gray-200 dark:border-zinc-800 p-5">
          <div class="mb-4">
            <h3 class="text-sm font-semibold">Rendimiento por Sede</h3>
            <p class="text-[11px] text-gray-400 dark:text-zinc-500">Ingresos del periodo</p>
          </div>
          <div v-if="warehouseRanking.length" class="space-y-3">
            <div v-for="(wh, i) in warehouseRanking" :key="wh.id" class="flex items-center gap-3">
              <span class="text-xs font-mono text-gray-400 dark:text-zinc-500 w-4 text-right">{{ i + 1 }}</span>
              <div class="flex-1 min-w-0">
                <div class="flex items-center justify-between mb-1">
                  <span class="text-xs font-medium truncate">{{ wh.name }}</span>
                  <span class="text-xs font-semibold tabular-nums">${{ fmt(wh.revenue) }}</span>
                </div>
                <div class="h-1.5 bg-gray-100 dark:bg-zinc-800 rounded-full overflow-hidden">
                  <div class="h-full rounded-full transition-all duration-500"
                    :class="i === 0 ? 'bg-blue-500' : i === 1 ? 'bg-blue-400' : 'bg-blue-300 dark:bg-blue-600'"
                    :style="{ width: maxWarehouseRevenue > 0 ? (wh.revenue / maxWarehouseRevenue * 100) + '%' : '0%' }">
                  </div>
                </div>
                <span class="text-[10px] text-gray-400 dark:text-zinc-500">{{ wh.tx_count }} transacciones</span>
              </div>
            </div>
          </div>
          <div v-else class="flex items-center justify-center h-32 text-xs text-gray-400">Sin sedes activas</div>
        </div>
      </div>

      <!-- ROW 3: Top Products + Payment Breakdown + Recent Transactions -->
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-3">

        <!-- Top Products -->
        <div class="bg-white dark:bg-[#1a1d27] rounded-lg border border-gray-200 dark:border-zinc-800 p-5">
          <div class="mb-4">
            <h3 class="text-sm font-semibold">Top Productos</h3>
            <p class="text-[11px] text-gray-400 dark:text-zinc-500">Mayor ingreso en el periodo</p>
          </div>
          <div v-if="topProducts.length" class="space-y-2.5">
            <div v-for="(prod, i) in topProducts" :key="prod.id"
              class="flex items-center gap-3 py-2 px-2 rounded-md hover:bg-gray-50 dark:hover:bg-zinc-800/50 transition-colors">
              <span class="text-xs font-mono w-4 text-right" :class="i < 3 ? 'text-blue-500 font-bold' : 'text-gray-400'">{{ i + 1 }}</span>
              <div class="flex-1 min-w-0">
                <p class="text-xs font-medium truncate">{{ prod.name }}</p>
                <p class="text-[10px] text-gray-400 dark:text-zinc-500">{{ prod.units_sold }} uds · {{ prod.margin }}% margen</p>
              </div>
              <span class="text-xs font-semibold tabular-nums">${{ fmt(prod.revenue) }}</span>
            </div>
          </div>
          <div v-else class="flex items-center justify-center h-32 text-xs text-gray-400">Sin ventas</div>
        </div>

        <!-- Payment Methods -->
        <div class="bg-white dark:bg-[#1a1d27] rounded-lg border border-gray-200 dark:border-zinc-800 p-5">
          <div class="mb-4">
            <h3 class="text-sm font-semibold">Métodos de Pago</h3>
            <p class="text-[11px] text-gray-400 dark:text-zinc-500">Distribución de ingresos</p>
          </div>
          <div class="h-[180px] flex items-center justify-center mb-3">
            <Doughnut v-if="paymentChartData.labels.length" :data="paymentChartData" :options="doughnutOptions" />
            <span v-else class="text-xs text-gray-400">Sin datos</span>
          </div>
          <div v-if="paymentBreakdown.length" class="space-y-1.5">
            <div v-for="(pm, i) in paymentBreakdown" :key="pm.method" class="flex items-center justify-between text-xs">
              <div class="flex items-center gap-2">
                <span class="w-2 h-2 rounded-full" :style="{ backgroundColor: paymentColors[i] || '#6b7280' }"></span>
                <span class="text-gray-600 dark:text-zinc-400 capitalize">{{ paymentLabel(pm.method) }}</span>
              </div>
              <span class="font-semibold tabular-nums">${{ fmt(pm.total) }}</span>
            </div>
          </div>
        </div>

        <!-- Recent Transactions -->
        <div class="bg-white dark:bg-[#1a1d27] rounded-lg border border-gray-200 dark:border-zinc-800 p-5">
          <div class="mb-4">
            <h3 class="text-sm font-semibold">Últimas Transacciones</h3>
            <p class="text-[11px] text-gray-400 dark:text-zinc-500">Actividad reciente</p>
          </div>
          <div v-if="recentTx.length" class="space-y-1">
            <div v-for="tx in recentTx" :key="tx.id"
              class="flex items-center justify-between py-2 border-b border-gray-100 dark:border-zinc-800 last:border-0">
              <div class="min-w-0 flex-1">
                <p class="text-xs font-medium truncate">{{ tx.customer_name }}</p>
                <p class="text-[10px] text-gray-400 dark:text-zinc-500">{{ tx.number }} · {{ formatTxDate(tx.date) }}</p>
              </div>
              <div class="text-right ml-3">
                <p class="text-xs font-semibold tabular-nums">${{ fmt(tx.total) }}</p>
                <p class="text-[10px] text-gray-400 dark:text-zinc-500 capitalize">{{ paymentLabel(tx.payment_method) }}</p>
              </div>
            </div>
          </div>
          <div v-else class="flex items-center justify-center h-32 text-xs text-gray-400">Sin transacciones</div>
        </div>
      </div>

      <!-- ROW 4: Intelligence Panel (Alerts) -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-3">

        <!-- Dead Stock -->
        <div class="bg-white dark:bg-[#1a1d27] rounded-lg border border-gray-200 dark:border-zinc-800 p-5">
          <div class="flex items-center gap-2 mb-4">
            <div class="w-1.5 h-4 rounded-full bg-amber-500"></div>
            <div>
              <h3 class="text-sm font-semibold">Stock Estancado</h3>
              <p class="text-[11px] text-gray-400 dark:text-zinc-500">Productos sin movimiento (30+ días)</p>
            </div>
          </div>
          <div v-if="deadStock.length" class="space-y-2">
            <div v-for="item in deadStock" :key="item.id"
              class="flex items-center gap-3 py-2 px-2 rounded-md hover:bg-amber-50 dark:hover:bg-amber-900/10 transition-colors">
              <div class="w-8 h-8 rounded bg-gray-100 dark:bg-zinc-800 flex items-center justify-center flex-shrink-0 overflow-hidden">
                <img v-if="item.image_url" :src="getImgUrl(item.image_url)" class="w-full h-full object-cover" />
                <svg v-else class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.75 7.5h16.5" />
                </svg>
              </div>
              <div class="flex-1 min-w-0">
                <p class="text-xs font-medium truncate">{{ item.name }}</p>
                <p class="text-[10px] text-gray-400 dark:text-zinc-500">
                  {{ item.current_stock }} uds · 
                  <span class="text-amber-600 dark:text-amber-400">{{ item.days_without_sale != null ? item.days_without_sale + ' días sin venta' : 'Sin ventas registradas' }}</span>
                </p>
              </div>
              <span class="text-xs font-semibold text-amber-600 dark:text-amber-400 tabular-nums flex-shrink-0">${{ fmt(item.capital_locked) }}</span>
            </div>
          </div>
          <div v-else class="flex flex-col items-center justify-center py-8 text-center">
            <svg class="w-8 h-8 text-emerald-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
              <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <p class="text-xs text-gray-500">Sin stock estancado</p>
          </div>
        </div>

        <!-- Critical Stock -->
        <div class="bg-white dark:bg-[#1a1d27] rounded-lg border border-gray-200 dark:border-zinc-800 p-5">
          <div class="flex items-center gap-2 mb-4">
            <div class="w-1.5 h-4 rounded-full bg-rose-500"></div>
            <div>
              <h3 class="text-sm font-semibold">Quiebre de Stock Crítico</h3>
              <p class="text-[11px] text-gray-400 dark:text-zinc-500">Productos y variantes bajo mínimo</p>
            </div>
          </div>
          <div v-if="criticalStock.length" class="space-y-2">
            <div v-for="item in criticalStock" :key="item.sku || item.id"
              class="flex items-center gap-3 py-2 px-2 rounded-md hover:bg-rose-50 dark:hover:bg-rose-900/10 transition-colors">
              <div class="w-8 h-8 rounded bg-gray-100 dark:bg-zinc-800 flex items-center justify-center flex-shrink-0 overflow-hidden">
                <img v-if="item.image_url" :src="getImgUrl(item.image_url)" class="w-full h-full object-cover" />
                <svg v-else class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
                </svg>
              </div>
              <div class="flex-1 min-w-0">
                <p class="text-xs font-medium truncate">{{ item.name }}</p>
                <p v-if="item.variant_label" class="text-[10px] text-purple-500 dark:text-purple-400 font-medium">{{ item.variant_label }}</p>
              </div>
              <div class="text-right flex-shrink-0">
                <span class="text-xs font-bold tabular-nums" :class="item.stock === 0 ? 'text-rose-600 dark:text-rose-400' : 'text-amber-600 dark:text-amber-400'">
                  {{ item.stock }}
                </span>
                <span class="text-[10px] text-gray-400"> / {{ item.min_stock }}</span>
              </div>
            </div>
          </div>
          <div v-else class="flex flex-col items-center justify-center py-8 text-center">
            <svg class="w-8 h-8 text-emerald-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
              <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <p class="text-xs text-gray-500">Todo abastecido</p>
          </div>
        </div>
      </div>

    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import { Bar, Doughnut } from 'vue-chartjs'
import {
  Chart,
  CategoryScale,
  LinearScale,
  BarElement,
  ArcElement,
  PointElement,
  LineElement,
  Title,
  Tooltip,
  Legend,
  Filler
} from 'chart.js'
import apiClient from '../services/apiClient.js'
import { useCashSession } from '../services/cashSessionService.js'
import { useScreenContext } from '@/composables/useScreenContext'
import { useUIContextStore } from '@/store/uiContextStore'
import { useAuth } from '../store/auth.js'

Chart.register(CategoryScale, LinearScale, BarElement, ArcElement, PointElement, LineElement, Title, Tooltip, Legend, Filler)

// ─── Props / Emits ───
defineProps({
  salesData: { type: Object, default: () => ({}) },
  productsCount: { type: Number, default: 0 },
  lowStock: { type: Array, default: () => [] },
  recentSales: { type: Array, default: () => [] },
  notifications: { type: Array, default: () => [] }
})
const emit = defineEmits(['change-module', 'navigate', 'changeModule', 'openQuotationInPos', 'refresh'])

// ─── Composables ───
const auth = useAuth()
const { setContext } = useScreenContext()
const uiContextStore = useUIContextStore()
const { currentSession, hasOpenSession, loadCurrentSession } = useCashSession()

// ─── State ───
const loading = ref(false)
const hasData = ref(false)

const filters = ref({
  warehouse_id: null,
  date_from: new Date().toISOString().slice(0, 8) + '01', // 1st of month
  date_to: new Date().toISOString().slice(0, 10)           // today
})
const activeRange = ref('month')

const kpis = ref({
  total_revenue: 0, revenue_growth: 0, total_cost: 0, gross_margin: 0,
  avg_ticket: 0, ticket_growth: 0, tx_count: 0, inventory_value: 0,
  total_expenses: 0, expense_growth: 0, net_profit: 0
})
const salesVsExpenses = ref([])
const warehouseRanking = ref([])
const deadStock = ref([])
const criticalStock = ref([])
const topProducts = ref([])
const paymentBreakdown = ref([])
const recentTx = ref([])
const warehouses = ref([])

// ─── Quick date ranges ───
const quickRanges = [
  { key: 'today', label: 'Hoy' },
  { key: 'week', label: '7D' },
  { key: 'month', label: '30D' },
  { key: 'quarter', label: '90D' }
]

function applyRange(key) {
  activeRange.value = key
  const now = new Date()
  const to = now.toISOString().slice(0, 10)
  let from
  switch (key) {
    case 'today':
      from = to
      break
    case 'week':
      from = new Date(now - 6 * 864e5).toISOString().slice(0, 10)
      break
    case 'month':
      from = new Date(now - 29 * 864e5).toISOString().slice(0, 10)
      break
    case 'quarter':
      from = new Date(now - 89 * 864e5).toISOString().slice(0, 10)
      break
  }
  filters.value.date_from = from
  filters.value.date_to = to
  loadData()
}

// ─── Computed ───
const currentDateLabel = computed(() =>
  new Date().toLocaleDateString('es-CO', { year: 'numeric', month: 'long', day: 'numeric' })
)

const maxWarehouseRevenue = computed(() =>
  Math.max(...warehouseRanking.value.map(w => w.revenue), 1)
)

// ─── Chart data ───
const paymentColors = ['#3b82f6', '#10b981', '#f59e0b', '#8b5cf6', '#ef4444', '#6b7280']

const paymentMethodLabels = {
  efectivo: 'Efectivo',
  cash: 'Efectivo',
  tarjeta: 'Tarjeta',
  card: 'Tarjeta',
  transferencia: 'Transferencia',
  transfer: 'Transferencia',
  creditienda: 'Crédito',
  credit: 'Crédito',
  nequi: 'Nequi',
  daviplata: 'Daviplata'
}

function paymentLabel(method) {
  return paymentMethodLabels[method] || method || 'Otro'
}

const isDark = computed(() => document.documentElement.classList.contains('dark'))

const salesExpensesChartData = computed(() => {
  const d = salesVsExpenses.value
  return {
    labels: d.map(x => x.label),
    datasets: [
      {
        label: 'Ventas',
        data: d.map(x => x.sales),
        backgroundColor: '#3b82f6',
        borderRadius: 3,
        barPercentage: 0.7,
        categoryPercentage: 0.8
      },
      {
        label: 'Gastos',
        data: d.map(x => x.expenses),
        backgroundColor: '#ef4444',
        borderRadius: 3,
        barPercentage: 0.7,
        categoryPercentage: 0.8
      }
    ]
  }
})

const barChartOptions = computed(() => ({
  responsive: true,
  maintainAspectRatio: false,
  plugins: {
    legend: {
      display: true,
      position: 'top',
      align: 'end',
      labels: {
        boxWidth: 8,
        boxHeight: 8,
        borderRadius: 2,
        usePointStyle: true,
        pointStyle: 'rect',
        font: { size: 11 },
        color: isDark.value ? '#a1a1aa' : '#6b7280',
        padding: 16
      }
    },
    tooltip: {
      backgroundColor: isDark.value ? '#27272a' : '#1f2937',
      titleColor: '#f9fafb',
      bodyColor: '#f9fafb',
      borderColor: isDark.value ? '#3f3f46' : '#374151',
      borderWidth: 1,
      padding: 10,
      displayColors: true,
      callbacks: {
        label: (ctx) => `${ctx.dataset.label}: $${fmt(ctx.parsed.y)}`
      }
    }
  },
  scales: {
    x: {
      grid: { display: false },
      border: { display: false },
      ticks: { color: isDark.value ? '#71717a' : '#9ca3af', font: { size: 10 }, maxRotation: 45 }
    },
    y: {
      grid: { color: isDark.value ? 'rgba(63,63,70,0.3)' : 'rgba(0,0,0,0.05)' },
      border: { display: false },
      ticks: {
        color: isDark.value ? '#71717a' : '#9ca3af',
        font: { size: 10 },
        callback: v => `$${fmtShort(v)}`
      }
    }
  }
}))

const paymentChartData = computed(() => ({
  labels: paymentBreakdown.value.map(p => paymentLabel(p.method)),
  datasets: [{
    data: paymentBreakdown.value.map(p => p.total),
    backgroundColor: paymentColors.slice(0, paymentBreakdown.value.length),
    borderWidth: 0,
    spacing: 2
  }]
}))

const doughnutOptions = computed(() => ({
  responsive: true,
  maintainAspectRatio: false,
  cutout: '75%',
  plugins: {
    legend: { display: false },
    tooltip: {
      backgroundColor: isDark.value ? '#27272a' : '#1f2937',
      titleColor: '#f9fafb',
      bodyColor: '#f9fafb',
      borderColor: isDark.value ? '#3f3f46' : '#374151',
      borderWidth: 1,
      padding: 10,
      callbacks: {
        label: (ctx) => `${ctx.label}: $${fmt(ctx.parsed)}`
      }
    }
  }
}))

// ─── Format helpers ───
function fmt(v) {
  if (v == null || isNaN(v)) return '0'
  return new Intl.NumberFormat('es-CO', { minimumFractionDigits: 0, maximumFractionDigits: 0 }).format(v)
}

function fmtShort(v) {
  if (Math.abs(v) >= 1e6) return (v / 1e6).toFixed(1) + 'M'
  if (Math.abs(v) >= 1e3) return (v / 1e3).toFixed(0) + 'k'
  return v.toString()
}

function formatTxDate(d) {
  if (!d) return ''
  const date = new Date(d)
  const today = new Date()
  if (date.toDateString() === today.toDateString()) {
    return date.toLocaleTimeString('es-CO', { hour: '2-digit', minute: '2-digit' })
  }
  return date.toLocaleDateString('es-CO', { month: 'short', day: 'numeric' })
}

function getImgUrl(url) {
  if (!url) return ''
  if (url.startsWith('http')) return url
  return `/storage/${url}`
}

// ─── API ───
async function loadData() {
  loading.value = true
  try {
    const { data: res } = await apiClient.get('/bi/dashboard', {
      params: {
        warehouse_id: filters.value.warehouse_id || undefined,
        date_from: filters.value.date_from,
        date_to: filters.value.date_to
      }
    })

    if (res.success) {
      const d = res.data
      kpis.value = d.kpis || kpis.value
      salesVsExpenses.value = d.sales_vs_expenses || []
      warehouseRanking.value = d.warehouse_ranking || []
      deadStock.value = d.dead_stock || []
      criticalStock.value = d.critical_stock || []
      topProducts.value = d.top_products || []
      paymentBreakdown.value = d.payment_breakdown || []
      recentTx.value = d.recent_transactions || []
      warehouses.value = d.filters?.warehouses || []
      hasData.value = true

      // Update AI context
      updateAIContext()
    }
  } catch (err) {
    console.error('BI Dashboard load error:', err)
  } finally {
    loading.value = false
  }
}

function updateAIContext() {
  const ctx = {
    screen: 'Panel de Control BI',
    description: 'Dashboard ejecutivo con métricas financieras, análisis de ventas vs gastos, rendimiento multisede, stock estancado y alertas críticas.',
    data: {
      ingresos: kpis.value.total_revenue,
      margenBruto: kpis.value.gross_margin + '%',
      ticketPromedio: kpis.value.avg_ticket,
      utilidadNeta: kpis.value.net_profit,
      valorInventario: kpis.value.inventory_value,
      transacciones: kpis.value.tx_count,
      alertasStockCritico: criticalStock.value.length,
      productosSinMovimiento: deadStock.value.length,
      topProductos: topProducts.value.map(p => ({ nombre: p.name, ingresos: p.revenue })),
    }
  }
  setContext(ctx)
  uiContextStore.setCurrentModule('dashboard')
  uiContextStore.setScreenData(ctx.data)
}

// ─── Lifecycle ───
onMounted(async () => {
  await loadCurrentSession()
  await loadData()
})
</script>

<!-- TrendBadge inline component -->
<script>
import { h } from 'vue'

const TrendBadge = {
  props: { value: { type: Number, default: 0 } },
  render() {
    if (this.value === 0) return null
    const up = this.value > 0
    return h('span', {
      class: [
        'inline-flex items-center gap-0.5 px-1.5 py-0.5 text-[10px] font-semibold rounded',
        up ? 'bg-emerald-50 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-400'
           : 'bg-rose-50 dark:bg-rose-950 text-rose-700 dark:text-rose-400'
      ]
    }, [
      h('svg', {
        class: 'w-2.5 h-2.5',
        fill: 'none',
        stroke: 'currentColor',
        viewBox: '0 0 24 24'
      }, [
        h('path', {
          'stroke-linecap': 'round',
          'stroke-linejoin': 'round',
          'stroke-width': '2.5',
          d: up ? 'M5 10l7-7m0 0l7 7m-7-7v18' : 'M19 14l-7 7m0 0l-7-7m7 7V3'
        })
      ]),
      `${Math.abs(this.value)}%`
    ])
  }
}

export default {
  components: { TrendBadge }
}
</script>

<style scoped>
/* Micro transitions */
* { transition-property: background-color, border-color, color; transition-duration: 150ms; }
</style>
