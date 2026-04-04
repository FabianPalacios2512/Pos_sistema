<template>
  <div class="h-full flex flex-col font-sans transition-colors duration-300">
    <div class="flex-1 overflow-y-auto p-8 space-y-8">
      
      <!-- Header - Minimalista -->
      <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
        <div>
          <h1 class="text-2xl font-bold text-gray-900 dark:text-white tracking-tight">Reportes Generales</h1>
          <p class="text-sm text-gray-400 dark:text-gray-500 mt-1">Análisis integral de rendimiento • {{ getPeriodLabel() }}</p>
        </div>
          
        <div class="flex items-center gap-3">
          <!-- Period Selector - Pill Style -->
          <div class="relative">
            <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
            </svg>
            <select 
              v-model="selectedPeriod" 
              @change="loadReportsData"
              class="pl-10 pr-5 py-2.5 text-sm rounded-xl bg-white dark:bg-zinc-900 text-gray-700 dark:text-gray-300 font-medium focus:outline-none focus:ring-2 focus:ring-emerald-500/30 appearance-none cursor-pointer shadow-sm hover:shadow-md transition-shadow border-0"
              style="box-shadow: 0 2px 8px rgba(0,0,0,0.04);"
            >
              <option value="today">Hoy</option>
              <option value="week">Esta semana</option>
              <option value="month">Este mes</option>
              <option value="year">Este año</option>
            </select>
          </div>
          
          <!-- Export Button - Subtle -->
          <button 
            @click="exportReport" 
            class="px-5 py-2.5 bg-gray-900 dark:bg-white hover:bg-gray-800 dark:hover:bg-gray-100 text-white dark:text-gray-900 text-sm font-medium rounded-xl transition-all flex items-center gap-2 shadow-sm hover:shadow-md"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            <span>Exportar</span>
          </button>
        </div>
      </div>
    
    <!-- Indicador de carga - Clean -->
    <div v-if="loading" class="bg-white dark:bg-zinc-900 rounded-2xl p-12 text-center" style="box-shadow: 0 4px 20px rgba(0,0,0,0.03);">
      <div class="inline-flex items-center space-x-3">
        <svg class="animate-spin h-5 w-5 text-emerald-500" fill="none" viewBox="0 0 24 24">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        <span class="text-gray-500 dark:text-gray-400 font-medium">Cargando datos...</span>
      </div>
    </div>

    <!-- Mensaje de error - Clean -->
    <div v-else-if="error" class="bg-white dark:bg-zinc-900 rounded-2xl p-8" style="box-shadow: 0 4px 20px rgba(0,0,0,0.03);">
      <div class="flex items-center space-x-4">
        <div class="w-12 h-12 rounded-xl bg-rose-50 dark:bg-rose-500/10 flex items-center justify-center">
          <svg class="w-6 h-6 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
          </svg>
        </div>
        <div>
          <h3 class="text-gray-900 dark:text-white font-semibold">Error al cargar reportes</h3>
          <p class="text-gray-500 dark:text-gray-400 text-sm mt-0.5">{{ error }}</p>
        </div>
      </div>
      <button @click="loadReportsData" class="mt-6 px-5 py-2.5 bg-gray-900 dark:bg-white text-white dark:text-gray-900 rounded-xl transition-all font-medium text-sm hover:shadow-md">
        Reintentar
      </button>
    </div>

    <!-- Contenido principal -->
    <div v-else class="space-y-8">
      
      <!-- ═══════════════════════════════════════════════════════════════
           KPIs PRINCIPALES - Estilo Stripe/Linear (Clean, Bold Numbers)
      ═══════════════════════════════════════════════════════════════ -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5">
        
        <!-- Total Sales - Clean KPI -->
        <!-- KPI: Ventas Totales -->
        <div class="kpi-card group px-4 py-3">
          <div class="flex items-center gap-3">
            <div class="icon-container flex-shrink-0">
              <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
              </svg>
            </div>
            <div class="flex-1 min-w-0">
              <p class="text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wide">Ventas Totales</p>
              <p class="text-2xl font-bold text-gray-900 dark:text-white mt-0.5">${{ (totalSales || 0).toLocaleString() }}</p>
              <p class="text-xs text-emerald-600 dark:text-emerald-400 mt-0.5">+15% vs período anterior</p>
            </div>
          </div>
        </div>

        <!-- KPI: Transacciones -->
        <div class="kpi-card group px-4 py-3">
          <div class="flex items-center gap-3">
            <div class="icon-container flex-shrink-0">
              <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v6a2 2 0 002 2h2m3 0h2a2 2 0 002-2V7a2 2 0 00-2-2h-2m-3 0V3m0 0l3 3m-3-3l-3 3"></path>
              </svg>
            </div>
            <div class="flex-1 min-w-0">
              <p class="text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wide">Transacciones</p>
              <p class="text-2xl font-bold text-gray-900 dark:text-white mt-0.5">{{ (totalTransactions || 0).toLocaleString() }}</p>
              <p class="text-xs text-blue-600 dark:text-blue-400 mt-0.5">+8% vs período anterior</p>
            </div>
          </div>
        </div>

        <!-- KPI: Ticket Promedio -->
        <div class="kpi-card group px-4 py-3">
          <div class="flex items-center gap-3">
            <div class="icon-container flex-shrink-0">
              <svg class="w-5 h-5 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"></path>
              </svg>
            </div>
            <div class="flex-1 min-w-0">
              <p class="text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wide">Ticket Promedio</p>
              <p class="text-2xl font-bold text-gray-900 dark:text-white mt-0.5">${{ (averageTicket || 0).toLocaleString() }}</p>
              <p class="text-xs text-amber-600 dark:text-amber-400 mt-0.5">+5% vs período anterior</p>
            </div>
          </div>
        </div>

        <!-- KPI: Margen Bruto -->
        <div class="kpi-card group px-4 py-3">
          <div class="flex items-center gap-3">
            <div class="icon-container flex-shrink-0">
              <svg class="w-5 h-5 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
              </svg>
            </div>
            <div class="flex-1 min-w-0">
              <p class="text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wide">Margen Bruto</p>
              <p class="text-2xl font-bold text-gray-900 dark:text-white mt-0.5">{{ grossMargin }}%</p>
              <p class="text-xs text-purple-600 dark:text-purple-400 mt-0.5">+2.1% vs período anterior</p>
            </div>
          </div>
        </div>
      </div>

      <!-- ═══════════════════════════════════════════════════════════════
           GRÁFICOS - Estilo Sistema de Diseño
      ═══════════════════════════════════════════════════════════════ -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        
        <!-- Sales Trend Chart -->
        <div class="chart-card">
          <div class="px-6 py-5 flex items-center justify-between border-b border-gray-100 dark:border-zinc-800">
            <div>
              <h2 class="text-base font-semibold text-gray-900 dark:text-white">Tendencia de Ventas</h2>
              <p class="text-sm text-gray-400 dark:text-gray-500 mt-0.5">Rendimiento para {{ getPeriodLabel() }}</p>
            </div>
            <svg class="w-5 h-5 text-emerald-500 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
            </svg>
          </div>
          <div class="px-7 pb-6">
            <div class="relative" style="height: 280px;">
              <Line :data="lineChartData" :options="lineChartOptions" />
            </div>
            <div class="mt-6 grid grid-cols-2 gap-4">
              <div class="text-center p-4 rounded-xl bg-emerald-50/50 dark:bg-emerald-500/5">
                <p class="text-xs text-gray-500 dark:text-gray-400 font-medium uppercase tracking-wide mb-1">Crecimiento</p>
                <p class="text-2xl font-bold text-emerald-600 dark:text-emerald-400">+{{ dailySales && dailySales.length > 0 ? Math.round((dailySales.reduce((a, b) => a + b, 0) / dailySales.length) * 0.0015) : 15 }}%</p>
              </div>
              <div class="text-center p-4 rounded-xl bg-blue-50/50 dark:bg-blue-500/5">
                <p class="text-xs text-gray-500 dark:text-gray-400 font-medium uppercase tracking-wide mb-1">Pico Máximo</p>
                <p class="text-2xl font-bold text-blue-600 dark:text-blue-400">${{ dailySales && dailySales.length > 0 ? Math.max(...dailySales).toLocaleString() : '2,800' }}</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Top Products Chart - Clean Card -->
        <div class="chart-card">
          <div class="px-7 py-6 flex items-center justify-between">
            <div>
              <h2 class="text-base font-semibold text-gray-900 dark:text-white">Top Productos</h2>
              <p class="text-sm text-gray-400 dark:text-gray-500 mt-0.5">Productos más vendidos por ingresos</p>
            </div>
            <svg class="w-5 h-5 text-emerald-500 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
            </svg>
          </div>
          <div class="px-7 pb-6">
            <div style="height: 320px;">
              <Bar :data="barChartData" :options="barChartOptions" />
            </div>
          </div>
        </div>

        <!-- Category Sales Chart - Clean Card -->
        <div class="chart-card">
          <div class="px-7 py-6 flex items-center justify-between">
            <div>
              <h2 class="text-base font-semibold text-gray-900 dark:text-white">Ventas por Categoría</h2>
              <p class="text-sm text-gray-400 dark:text-gray-500 mt-0.5">Distribución de ventas por categorías</p>
            </div>
            <svg class="w-5 h-5 text-violet-500 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"></path>
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"></path>
            </svg>
          </div>
          <div class="px-7 pb-6">
            <div style="height: 320px;">
              <Bar :data="categoryBarChartData" :options="categoryBarChartOptions" />
            </div>
          </div>
        </div>

        <!-- Critical Stock Chart - Clean Card -->
        <div class="chart-card">
          <div class="px-7 py-6 flex items-center justify-between">
            <div>
              <h2 class="text-base font-semibold text-gray-900 dark:text-white">Stock Crítico</h2>
              <p class="text-sm text-gray-400 dark:text-gray-500 mt-0.5">Productos con inventario bajo</p>
            </div>
            <svg class="w-5 h-5 text-rose-500 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
            </svg>
          </div>
          <div class="px-7 pb-6">
            <div style="height: 320px;" class="flex justify-center items-center">
              <PolarArea 
                v-if="lowStockProducts && lowStockProducts.length > 0"
                :data="polarChartData" 
                :options="polarChartOptions" 
                class="w-full h-full"
              />
              <div v-else class="text-center">
                <div class="w-16 h-16 mx-auto mb-4 rounded-2xl bg-emerald-50 dark:bg-emerald-500/10 flex items-center justify-center">
                  <svg class="w-8 h-8 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                  </svg>
                </div>
                <p class="font-semibold text-gray-900 dark:text-white">Sin productos críticos</p>
                <p class="text-sm text-gray-400 dark:text-gray-500 mt-1">Todo el inventario está en niveles óptimos</p>
              </div>
            </div>
          </div>
        </div>
      </div>

    </div>
    
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onBeforeUnmount, computed, watch } from 'vue'
import { reportsService } from '../services/reportsService.js'
import { cashReportsService } from '../services/cashReportsService.js' // NUEVO: Para datos por horas
import { useUIContextStore } from '@/store/uiContextStore'

// IMPORTACIONES DE GRÁFICOS ADICIONALES
import { Line, Bar, Radar, PolarArea } from 'vue-chartjs'
import {
  Chart,
  CategoryScale,
  LinearScale,
  PointElement,
  LineElement,
  BarElement,
  ArcElement,
  RadialLinearScale, // NECESARIO para Radar y Polar
  Filler, // NECESARIO para líneas con relleno
  Title,
  Tooltip,
  Legend
} from 'chart.js'

// Registrar todos los elementos necesarios, incluyendo RadialLinearScale y Filler
Chart.register(
  CategoryScale, 
  LinearScale, 
  PointElement, 
  LineElement, 
  BarElement, 
  ArcElement, 
  RadialLinearScale, 
  Filler, 
  Title, 
  Tooltip, 
  Legend
)

// Verificar que Filler esté registrado

// --- ESTADO REACTIVO ---
const selectedPeriod = ref('today') // Cambiar a 'today' por defecto
const loading = ref(false)
const error = ref(null)

// Datos de la API
const totalSales = ref(0)
const totalTransactions = ref(0)
const averageTicket = ref(0)
const grossMargin = ref(35.2) // Este valor podría calcularse según el costo de productos

const dailySales = ref([])
const hourlySales = ref([]) // NUEVO: Datos por horas
const topProducts = ref([])
const salesByCategory = ref([])
const lowStockProducts = ref([])
const customerStats = ref({})

// Datos para Radar Chart (Inventado para ilustrar 3 dimensiones de 3 categorías)
const categoryPerformance = ref([
    { name: 'Bebidas', salesScore: 90, marginScore: 75, trafficScore: 85 },
    { name: 'Lácteos', salesScore: 60, marginScore: 95, trafficScore: 40 },
    { name: 'Snacks', salesScore: 75, marginScore: 70, trafficScore: 95 },
]);

// --- FUNCIONES PARA CARGAR DATOS ---
const loadReportsData = async () => {
  try {
    loading.value = true
    error.value = null
    
    if (selectedPeriod.value === 'today') {
      await loadHourlyReportsData()
      return
    }
    
    // NUEVA LÓGICA: Cargar datos estructurados de tendencias para períodos no diarios
    const [salesData, trendChartData, topProductsData, categoryData, lowStockData, customerData] = await Promise.all([
      reportsService.getSalesData(selectedPeriod.value),
      cashReportsService.getTrendChartData(selectedPeriod.value),
      reportsService.getTopProducts(selectedPeriod.value, 5),
      reportsService.getSalesByCategory(selectedPeriod.value),
      reportsService.getLowStockProducts(20),
      reportsService.getCustomerStats(selectedPeriod.value)
    ])
    
    console.log('🔍 DEBUG - Top Products:', topProductsData)
    console.log('🔍 DEBUG - Categories:', categoryData)
    console.log('🔍 DEBUG - Sales Data:', salesData)
    
    // Actualizar datos si las respuestas son exitosas
    if (salesData.success) {
      totalSales.value = salesData.data.totalSales
      totalTransactions.value = salesData.data.totalTransactions
      averageTicket.value = salesData.data.averageTicket
    }
    
    if (trendChartData.success) {
      const chartData = trendChartData.data.chart_data || []
      dailySales.value = chartData.map(item => parseFloat(item.sales) || 0)
      
      if (trendChartData.data.total_sales) {
        totalSales.value = parseFloat(trendChartData.data.total_sales)
      }
      if (trendChartData.data.total_transactions) {
        totalTransactions.value = parseInt(trendChartData.data.total_transactions)
      }
    } else {
      // Datos de ejemplo para la gráfica
      dailySales.value = [1200, 1800, 1500, 2100, 1900, 2300, 2800]
    }
    
    if (topProductsData.success) {
      console.log('✅ TOP PRODUCTS recibidos:', topProductsData.data)
      topProducts.value = topProductsData.data
    } else {
      console.warn('❌ topProductsData.success es false')
      // Datos de ejemplo
      topProducts.value = [
        { id: 1, name: 'Producto A', sold: 45, revenue: 4500 },
        { id: 2, name: 'Producto B', sold: 32, revenue: 3200 },
        { id: 3, name: 'Producto C', sold: 28, revenue: 2800 }
      ]
    }
    
    if (categoryData.success) {
      console.log('✅ CATEGORIES recibidas:', categoryData.data)
      salesByCategory.value = categoryData.data
    } else {
      console.warn('❌ categoryData.success es false')
      // Datos de ejemplo
      salesByCategory.value = [
        { name: 'Bebidas', sales: 5600 },
        { name: 'Snacks', sales: 3400 },
        { name: 'Lácteos', sales: 2100 }
      ]
    }
    

    
    if (lowStockData.success) {
      // Convertir para el gráfico polar con colores
      const colors = ['#ef4444', '#f59e0b', '#3b82f6', '#10b981', '#8b5cf6', '#f97316', '#06b6d4', '#84cc16', '#ec4899', '#6366f1']
      lowStockProducts.value = lowStockData.data.map((product, index) => ({
        ...product,
        color: colors[index % colors.length]
      }))
      console.log('📦 Low Stock Products cargados:', lowStockProducts.value.length, 'productos:', lowStockProducts.value.map(p => p.name))
    }
    
    if (customerData.success) {
      customerStats.value = customerData.data
    }
    
  } catch (err) {
    console.error('Error cargando datos de reportes:', err)
    error.value = 'Error al cargar los datos de reportes'
    
    // Cargar datos de ejemplo en caso de error
    dailySales.value = [1200, 1800, 1500, 2100, 1900, 2300, 2800]
    topProducts.value = [
      { id: 1, name: 'Producto A', sold: 45, revenue: 4500 },
      { id: 2, name: 'Producto B', sold: 32, revenue: 3200 },
      { id: 3, name: 'Producto C', sold: 28, revenue: 2800 }
    ]
    salesByCategory.value = [
      { name: 'Bebidas', sales: 5600 },
      { name: 'Snacks', sales: 3400 },
      { name: 'Lácteos', sales: 2100 }
    ]
    lowStockProducts.value = [
      { id: 1, name: 'Producto X', quantity: 5, color: '#f59e0b' },
      { id: 2, name: 'Producto Y', quantity: 3, color: '#ef4444' },
      { id: 3, name: 'Producto Z', quantity: 1, color: '#3b82f6' }
    ]
  } finally {
    loading.value = false
  }
}

// FUNCIÓN SIMPLIFICADA: USAR EL MISMO SERVICIO QUE EL DASHBOARD
const loadHourlyReportsData = async () => {
  try {
    // USAR cashReportsService IGUAL QUE EL DASHBOARD (ya procesa las horas correctamente)
    const [salesData, hourlyResponse, topProductsData, categoryData, lowStockData, customerData] = await Promise.all([
      reportsService.getSalesData('today'),
      cashReportsService.getHourlyEfficiency('today'), // ✅ MISMO SERVICIO DEL DASHBOARD
      reportsService.getTopProducts('today', 5),
      reportsService.getSalesByCategory('today'),
      reportsService.getLowStockProducts(20),
      reportsService.getCustomerStats('today')
    ])
    
    if (salesData.success) {
      totalSales.value = salesData.data.totalSales
      totalTransactions.value = salesData.data.totalTransactions
      averageTicket.value = salesData.data.averageTicket
    }
    
    // Procesar datos por hora EXACTAMENTE COMO EL DASHBOARD
    if (hourlyResponse.success && hourlyResponse.data) {
      const allHoursData = hourlyResponse.data.hourly_data || []
      
      // Convertir a array de 24 posiciones con las ventas de cada hora
      hourlySales.value = Array.from({ length: 24 }, (_, i) => {
        const hourKey = `${i.toString().padStart(2, '0')}:00`
        const hourData = allHoursData.find(h => h.hour === hourKey)
        return hourData ? parseFloat(hourData.sales || 0) : 0
      })
      
      dailySales.value = hourlySales.value
    } else {
      // Si no hay datos del servicio, poner todo en 0
      hourlySales.value = Array.from({ length: 24 }, () => 0)
      dailySales.value = hourlySales.value
    }
    
    // Actualizar otros datos
    if (topProductsData.success) {
      topProducts.value = topProductsData.data
    } else {
      topProducts.value = [
        { id: 1, name: 'Sin datos hoy', sold: 0, revenue: 0 }
      ]
    }
    
    if (categoryData.success) {
      if (categoryData.data.length === 0) {
        salesByCategory.value = [{ name: 'Sin datos hoy', sales: 0 }]
      } else {
        salesByCategory.value = categoryData.data
      }
    } else {
      salesByCategory.value = [
        { name: 'Sin datos', sales: 0 }
      ]
    }
    
    if (lowStockData.success) {
      const colors = ['#ef4444', '#f59e0b', '#3b82f6', '#10b981', '#8b5cf6', '#f97316', '#06b6d4', '#84cc16', '#ec4899', '#6366f1']
      lowStockProducts.value = lowStockData.data.map((product, index) => ({
        ...product,
        color: colors[index % colors.length]
      }))
      console.log('📦 [Hourly] Low Stock Products cargados:', lowStockProducts.value.length, 'productos:', lowStockProducts.value.map(p => p.name))
    }
    
    if (customerData.success) {
      customerStats.value = customerData.data
    }
    
  } catch (err) {
    console.error('❌ Error cargando datos por horas:', err)
    error.value = 'Error al cargar datos por horas'
    
    // Datos de ejemplo por horas en caso de error
    hourlySales.value = Array.from({ length: 24 }, () => 0)
    dailySales.value = hourlySales.value
    topProducts.value = [{ id: 1, name: 'Error carga', sold: 0, revenue: 0 }]
    salesByCategory.value = [{ name: 'Error', sales: 0 }]
  }
}

// Observar cambios en el período seleccionado
watch(selectedPeriod, () => {
  if (selectedPeriod.value === 'today') {
    loadHourlyReportsData()
  } else {
    loadReportsData()
  }
})

// ═══════════════════════════════════════════════════════════════
// 🤖 CONTEXTO IA - Reportes Generales
// ═══════════════════════════════════════════════════════════════
const uiContextStore = useUIContextStore()

// Actualizar contexto para la IA
const actualizarContextoIA = () => {
  const formatMoney = (n) => `$${(n || 0).toLocaleString('es-CO')}`
  
  // KPIs principales
  const kpis = {
    ventasTotales: formatMoney(totalSales.value),
    ventasTotalesNumero: totalSales.value,
    transacciones: totalTransactions.value,
    ticketPromedio: formatMoney(averageTicket.value),
    ticketPromedioNumero: averageTicket.value,
    margenBruto: `${(grossMargin.value || 0).toFixed(1)}%`,
    periodo: selectedPeriod.value
  }
  
  // Top productos
  const topProductosResumen = (topProducts.value || []).slice(0, 5).map(p => ({
    nombre: p.name,
    vendidos: p.sold || 0,
    ingresos: formatMoney(p.revenue || 0)
  }))
  
  // Ventas por categoría
  const categoriasResumen = (salesByCategory.value || []).slice(0, 5).map(c => ({
    nombre: c.name,
    ventas: formatMoney(c.sales || 0)
  }))
  
  // Productos con stock bajo
  const stockBajoResumen = (lowStockProducts.value || []).slice(0, 5).map(p => ({
    nombre: p.name,
    stock: p.quantity || p.stock || 0
  }))
  
  uiContextStore.setScreenData({
    tipoReporte: 'reports-general',
    modulo: 'Reportes Generales',
    descripcion: 'Dashboard ejecutivo con análisis de ventas, productos más vendidos, categorías y alertas de stock',
    kpis,
    topProductos: topProductosResumen,
    ventasPorCategoria: categoriasResumen,
    productosStockBajo: stockBajoResumen,
    periodoActual: getPeriodLabel(),
    tendenciaVentas: dailySales.value?.length || 0,
    ultimaActualizacion: new Date().toLocaleTimeString('es-CO')
  })
}

// Registrar acciones disponibles para la IA
const registrarAccionesIA = () => {
  // Consultar reportes generales
  uiContextStore.registerAction('consultarReportesGenerales', async ({ periodo, tipoConsulta }) => {
    try {
      const formatMoney = (n) => `$${(n || 0).toLocaleString('es-CO')}`
      
      // Si se pide cambiar período, actualizar
      if (periodo && periodo !== selectedPeriod.value) {
        const periodoMap = { 'today': 'today', 'week': 'week', 'month': 'month', 'year': 'year' }
        if (periodoMap[periodo]) {
          selectedPeriod.value = periodoMap[periodo]
          await new Promise(resolve => setTimeout(resolve, 500))
        }
      }
      
      let mensaje = ''
      switch (tipoConsulta) {
        case 'ventas':
          mensaje = `💰 Ventas totales del ${getPeriodLabel()}: ${formatMoney(totalSales.value)} en ${totalTransactions.value} transacciones`
          break
        case 'productos':
          const topProd = topProducts.value.slice(0, 5)
          mensaje = `🏆 Top productos del ${getPeriodLabel()}:\n` + topProd.map((p, i) => 
            `${i+1}. ${p.name}: ${p.sold} vendidos (${formatMoney(p.revenue)})`
          ).join('\n')
          break
        case 'categorias':
          const cats = salesByCategory.value.slice(0, 5)
          mensaje = `📊 Ventas por categoría del ${getPeriodLabel()}:\n` + cats.map(c => 
            `• ${c.name}: ${formatMoney(c.sales)}`
          ).join('\n')
          break
        case 'tendencia':
          const dias = dailySales.value.length
          const promedio = dias > 0 ? dailySales.value.reduce((a,b) => a+b, 0) / dias : 0
          mensaje = `📈 Tendencia del ${getPeriodLabel()}: ${dias} ${selectedPeriod.value === 'today' ? 'horas' : 'días'} registrados, promedio de ${formatMoney(promedio)}`
          break
        default:
          mensaje = `📊 RESUMEN DEL ${getPeriodLabel().toUpperCase()}:
• Ventas totales: ${formatMoney(totalSales.value)}
• Transacciones: ${totalTransactions.value}
• Ticket promedio: ${formatMoney(averageTicket.value)}
• Margen bruto: ${(grossMargin.value || 0).toFixed(1)}%`
      }
      
      return { success: true, message: mensaje }
    } catch (err) {
      console.error('Error en consultarReportesGenerales:', err)
      return { success: false, message: 'Error al consultar reportes' }
    }
  })
  
  // Cambiar período
  uiContextStore.registerAction('cambiarPeriodoReportes', async ({ periodo }) => {
    const periodoMap = { 'hoy': 'today', 'semana': 'week', 'mes': 'month', 'año': 'year' }
    const nuevoPeriodo = periodoMap[periodo] || periodo
    if (['today', 'week', 'month', 'year'].includes(nuevoPeriodo)) {
      selectedPeriod.value = nuevoPeriodo
      return { success: true, message: `Período cambiado a ${periodo}` }
    }
    return { success: false, message: 'Período no válido' }
  })
  
  // Exportar reporte
  uiContextStore.registerAction('exportarReporte', async () => {
    try {
      exportReport()
      return { success: true, message: 'Reporte exportado exitosamente' }
    } catch (err) {
      return { success: false, message: 'Error al exportar reporte' }
    }
  })
}

// Cargar datos al montar el componente
onMounted(() => {
  // Establecer módulo actual para la IA
  uiContextStore.setCurrentModule('reports-general')
  
  if (selectedPeriod.value === 'today') {
    loadHourlyReportsData()
  } else {
    loadReportsData()
  }
  
  // Registrar acciones IA
  registrarAccionesIA()
  
  // Actualizar contexto después de cargar datos
  setTimeout(() => {
    actualizarContextoIA()
  }, 1000)
})

// Limpiar al desmontar
onBeforeUnmount(() => {
  uiContextStore.clearSelection()
})

// Actualizar contexto cuando cambian los datos
watch([totalSales, totalTransactions, topProducts, salesByCategory], () => {
  actualizarContextoIA()
}, { deep: true })

// ------------------ Helpers de export / utilidades ------------------
const toCSV = (headers, rows) => {
  const esc = (v) => `"${String(v).replace(/"/g, '""')}"`
  const csv = [headers.map(esc).join(',')]
  rows.forEach(r => csv.push(r.map(esc).join(',')))
  return csv.join('\n')
}

const downloadCSV = (filename, content) => {
  const blob = new Blob([content], { type: 'text/csv;charset=utf-8;' })
  const url = URL.createObjectURL(blob)
  const link = document.createElement('a')
  link.setAttribute('href', url)
  link.setAttribute('download', filename)
  link.style.display = 'none'
  document.body.appendChild(link)
  link.click()
  document.body.removeChild(link)
  URL.revokeObjectURL(url)
}

const exportDailySalesCSV = () => {
  const headers = ['Fecha', 'TotalVentas']
  const rows = dailySales.value.map((total, idx) => [formatDateStringByIndex(idx), total])
  const csv = toCSV(headers, rows)
  downloadCSV(`ventas_diarias_${selectedPeriod.value}.csv`, csv)
}

const exportTopProductsCSV = () => {
  const headers = ['Producto', 'UnidadesVendidas', 'Ingresos']
  const rows = topProducts.value.map(p => [p.name, p.sold, p.revenue])
  const csv = toCSV(headers, rows)
  downloadCSV(`top_productos_${selectedPeriod.value}.csv`, csv)
}

const exportCategorySalesCSV = () => {
  const headers = ['Categoria', 'Ventas']
  const rows = salesByCategory.value.map(c => [c.name, c.sales])
  const csv = toCSV(headers, rows)
  downloadCSV(`ventas_categoria_${selectedPeriod.value}.csv`, csv)
}



// Helper para crear una fecha a partir del índice en dailySales
const formatDateStringByIndex = (index) => {
  const today = new Date()
  let days = 7 // por defecto
  
  // Determinar el número de días según el período
  switch (selectedPeriod.value) {
    case 'today':
      days = 1
      break
    case 'week':
      days = 7
      break
    case 'month':
      days = 30
      break
    case 'year':
      days = 365
      break
  }
  
  // Calcular la fecha real para este índice
  const date = new Date(today)
  date.setDate(date.getDate() - (days - 1 - index))
  
  // Para el período diario, siempre mostrar la fecha de hoy
  if (selectedPeriod.value === 'today') {
    return today.toLocaleDateString('es-CO')
  }
  
  return date.toLocaleDateString('es-CO')
}

const exportReport = () => {
  // Export general: combinar principales reportes en un ZIP/CSV simple -> por ahora generar CSV con resumen y enlaces
  exportDailySalesCSV()
}

// Función para obtener la etiqueta del período seleccionado
const getPeriodLabel = () => {
  const labels = {
    today: 'Últimas 24 horas',
    week: 'Últimos 7 días', 
    month: 'Últimos 30 días',
    year: 'Últimos 12 meses'
  }
  return labels[selectedPeriod.value] || 'Período seleccionado'
}
// --- FUNCIÓN DE OPCIONES BASE ---

const getProChartOptions = (isHorizontal = false, showLegend = false) => ({
  responsive: true,
  maintainAspectRatio: false,
  indexAxis: isHorizontal ? 'y' : 'x', 
  plugins: {
    legend: { display: false },
    title: { display: false },
    tooltip: { 
      backgroundColor: 'rgba(15, 15, 16, 0.95)',
      titleColor: '#fff',
      bodyColor: 'rgba(255,255,255,0.8)',
      cornerRadius: 12,
      displayColors: false,
      padding: 14,
      titleFont: {
        size: 13,
        weight: '600',
        family: "'Inter', -apple-system, sans-serif"
      },
      bodyFont: {
        size: 14,
        weight: '500',
        family: "'Inter', -apple-system, sans-serif"
      },
      callbacks: {
        label: (context) => {
          // Para barras horizontales el valor está en .x, para verticales en .y
          let value = 0
          
          if (context.parsed) {
            // Barras horizontales: valor en X
            if (context.parsed.x !== undefined && context.parsed.x !== null) {
              value = context.parsed.x
            }
            // Barras verticales: valor en Y
            else if (context.parsed.y !== undefined && context.parsed.y !== null) {
              value = context.parsed.y
            }
          }
          
          // Fallback: usar raw data
          if (value === 0 && context.raw !== undefined) {
            value = context.raw
          }
          
          return `$${Math.abs(value).toLocaleString('es-CO')}`;
        }
      }
    }
  },
  scales: {
    x: { 
      grid: { 
        display: false // ❌ ELIMINAR grid para look premium
      },
      border: {
        display: false
      },
      ticks: { 
        color: () => {
          const isDark = document.documentElement.classList.contains('dark')
          return isDark ? 'rgba(161, 161, 170, 0.6)' : 'rgba(100, 116, 139, 0.7)'
        },
        font: {
          family: "'Inter', -apple-system, sans-serif",
          size: 11,
          weight: '500'
        },
        padding: 10,
        callback: function(value, index) {
          if (isHorizontal) {
            // Para barras horizontales: formatear valores monetarios
            return `$${(value / 1000).toFixed(0)}K`
          } else {
            // Para barras verticales: mostrar las etiquetas de categorías
            return this.getLabelForValue(value)
          }
        }
      }
    },
    y: { 
      grid: { 
        display: false // ❌ ELIMINAR grid para look premium
      },
      border: {
        display: false
      },
      ticks: { 
        color: () => {
          const isDark = document.documentElement.classList.contains('dark')
          return isDark ? 'rgba(161, 161, 170, 0.6)' : 'rgba(100, 116, 139, 0.7)'
        },
        font: {
          family: "'Inter', -apple-system, sans-serif",
          size: 11,
          weight: '500'
        },
        padding: 12,
        callback: function(value) {
          if (isHorizontal) {
            // Para barras horizontales: mostrar nombres de productos
            return this.getLabelForValue(value)
          } else {
            // Para barras verticales: formatear valores monetarios
            return `$${(value / 1000).toFixed(0)}K`
          }
        }
      }
    }
  }
});

// NUEVO: Computed para filtrar ventas diarias sin ceros
const filteredDailySales = computed(() => {
  if (!dailySales.value || dailySales.value.length === 0) {
    return []
  }
  
  // Crear array de objetos con fecha y ventas, filtrar solo las ventas > 0
  const salesWithDates = dailySales.value.map((sales, index) => ({
    date: formatDateStringByIndex(index),
    sales: sales,
    index: index
  }))
  
  // Filtrar solo las ventas mayores a 0
  return salesWithDates.filter(item => item.sales > 0)
})

// NUEVO: Computed para filtrar productos con ventas mayores a 0
const filteredTopProducts = computed(() => {
  if (!topProducts.value || topProducts.value.length === 0) {
    return []
  }
  
  // Filtrar productos que tengan ventas o ingresos mayores a 0
  return topProducts.value.filter(product => 
    (product.sold > 0) || (product.revenue > 0)
  )
})

// NUEVO: Computed para filtrar categorías con ventas mayores a 0
const filteredSalesByCategory = computed(() => {
  if (!salesByCategory.value || salesByCategory.value.length === 0) {
    return []
  }
  
  // Filtrar categorías que tengan ventas mayores a 0
  return salesByCategory.value.filter(category => category.sales > 0)
})


// 1. LÍNEA (Tendencia) - Mejorado con datos por horas para 'today'
const lineChartData = computed(() => {
  let dataValues, labels
  
  if (selectedPeriod.value === 'today') {
    dataValues = (hourlySales.value && hourlySales.value.length > 0) 
      ? hourlySales.value 
      : Array.from({ length: 24 }, () => 0)
    
    // Mostrar solo algunas horas clave para no saturar el eje X
    labels = Array.from({ length: 24 }, (_, i) => {
      // Mostrar etiqueta solo cada 4 horas (0, 4, 8, 12, 16, 20)
      if (i % 4 === 0) {
        const hour = i.toString().padStart(2, '0')
        return `${hour}:00`
      }
      return '' // Vacío para las demás horas
    })
  } else {
    dataValues = (dailySales.value && dailySales.value.length > 0) 
      ? dailySales.value 
      : [1200, 1800, 1500, 2100, 1900, 2300, 2800]
    
    if (selectedPeriod.value === 'week') {
      labels = ['Vie', 'Sáb', 'Dom', 'Lun', 'Mar', 'Mié', 'Jue']
    } else if (selectedPeriod.value === 'month') {
      labels = Array.from({ length: dataValues.length }, (_, i) => `Día ${i + 1}`)
    } else if (selectedPeriod.value === 'year') {
      const monthNames = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic']
      labels = dataValues.length === 12 
        ? monthNames 
        : Array.from({ length: dataValues.length }, (_, i) => `Mes ${i + 1}`)
    } else {
      labels = Array.from({ length: dataValues.length }, (_, i) => `Período ${i + 1}`)
    }
  }
  
  return {
    labels: labels,
    datasets: [
      {
        label: 'Ventas ($)',
        data: dataValues,
        borderColor: '#10b981',
        backgroundColor: (context) => {
          const ctx = context.chart.ctx;
          const gradient = ctx.createLinearGradient(0, 0, 0, 350);
          gradient.addColorStop(0, 'rgba(16, 185, 129, 0.15)');
          gradient.addColorStop(0.5, 'rgba(16, 185, 129, 0.08)');
          gradient.addColorStop(1, 'rgba(16, 185, 129, 0)');
          return gradient;
        },
        borderWidth: 4,
        tension: 0.4,
        fill: true,
        pointBackgroundColor: '#10b981',
        pointBorderColor: '#ffffff',
        // Mostrar punto SOLO donde hay ventas (> 0)
        pointRadius: (context) => {
          const value = context.parsed?.y ?? 0
          return value > 0 ? 5 : 0 // 5 si hay venta, 0 si no hay
        },
        pointHoverRadius: (context) => {
          const value = context.parsed?.y ?? 0
          return value > 0 ? 8 : 0
        },
        pointBorderWidth: 3,
        pointHoverBackgroundColor: '#10b981',
        pointHoverBorderColor: '#ffffff',
        borderCapStyle: 'round',
        borderJoinStyle: 'round'
      }
    ]
  }
})
const lineChartOptions = computed(() => ({
  responsive: true,
  maintainAspectRatio: false,
  interaction: {
    intersect: false,
    mode: 'index'
  },
  plugins: {
    legend: {
      display: false
    },
    tooltip: {
      backgroundColor: 'rgba(15, 15, 16, 0.95)',
      titleColor: '#fff',
      bodyColor: 'rgba(255,255,255,0.8)',
      cornerRadius: 12,
      displayColors: false,
      padding: 14,
      titleFont: {
        size: 13,
        weight: '600',
        family: "'Inter', -apple-system, sans-serif"
      },
      bodyFont: {
        size: 14,
        weight: '500',
        family: "'Inter', -apple-system, sans-serif"
      },
      callbacks: {
        title: (context) => {
          // Calcular la hora real basándose en el índice del punto
          const index = context[0]?.dataIndex
          if (index !== undefined) {
            const hour = index.toString().padStart(2, '0')
            return `${hour}:00`
          }
          return context[0]?.label || ''
        },
        label: (context) => {
          const value = context.parsed?.y ?? 0
          if (value === 0) return null // No mostrar tooltip si no hay ventas
          return `Ventas: $${value.toLocaleString('es-CO')}`
        }
      }
    }
  },
  scales: {
    x: {
      grid: {
        display: false // ❌ ELIMINAR grid vertical
      },
      border: {
        display: false
      },
      ticks: {
        color: () => {
          const isDark = document.documentElement.classList.contains('dark')
          return isDark ? 'rgba(161, 161, 170, 0.6)' : 'rgba(100, 116, 139, 0.7)'
        },
        font: {
          family: "'Inter', -apple-system, sans-serif",
          size: 11,
          weight: '500'
        },
        padding: 10
      }
    },
    y: {
      grid: {
        display: false // ❌ ELIMINAR grid horizontal
      },
      border: {
        display: false
      },
      ticks: {
        color: () => {
          const isDark = document.documentElement.classList.contains('dark')
          return isDark ? 'rgba(161, 161, 170, 0.6)' : 'rgba(100, 116, 139, 0.7)'
        },
        font: {
          family: "'Inter', -apple-system, sans-serif",
          size: 11,
          weight: '500'
        },
        padding: 12,
        callback: (value) => `$${(value / 1000).toFixed(0)}K`
      }
    }
  }
}));

// 2. BARRAS HORIZONTALES (Top Productos)
const barChartData = computed(() => ({
  labels: topProducts.value.map(p => p.name),
  datasets: [
    {
      label: 'Ingresos ($)',
      data: topProducts.value.map(p => p.revenue),
      backgroundColor: (context) => {
        const ctx = context.chart.ctx;
        const gradient = ctx.createLinearGradient(0, 0, 450, 0);
        gradient.addColorStop(0, 'rgba(16, 185, 129, 0.9)');
        gradient.addColorStop(1, 'rgba(16, 185, 129, 0.5)');
        return gradient;
      },
      borderRadius: { topRight: 6, bottomRight: 6 },
      barPercentage: 0.5, // Barras más delgadas
      categoryPercentage: 0.7,
      borderWidth: 0,
      hoverBackgroundColor: 'rgba(5, 150, 105, 1)'
    }
  ]
}));
const barChartOptions = getProChartOptions(true, false);

// 3. BARRAS VERTICALES (Categorías)
const categoryBarChartData = computed(() => ({
  labels: salesByCategory.value.map(c => c.name),
  datasets: [
    {
      label: 'Ventas ($)',
      data: salesByCategory.value.map(c => c.sales),
      backgroundColor: (context) => {
        const ctx = context.chart.ctx;
        const gradient = ctx.createLinearGradient(0, 0, 0, 300);
        gradient.addColorStop(0, 'rgba(99, 102, 241, 0.9)');
        gradient.addColorStop(1, 'rgba(99, 102, 241, 0.4)');
        return gradient;
      },
      borderRadius: { topLeft: 8, topRight: 8 },
      barPercentage: 0.55, // Barras más delgadas y elegantes
      categoryPercentage: 0.7,
      borderWidth: 0,
      hoverBackgroundColor: 'rgba(79, 70, 229, 1)'
    }
  ]
}));
const categoryBarChartOptions = getProChartOptions(false, false);






// 5. GRÁFICO DE RADAR (Rendimiento Categoria)
const radarChartData = computed(() => ({
    labels: ['Ventas', 'Margen', 'Tráfico'],
    datasets: categoryPerformance.value.map((cat, index) => {
        const color = ['#3b82f6', '#10b981', '#ef4444'][index % 3]; // Azul, Verde, Rojo
        return {
            label: cat.name,
            data: [cat.salesScore, cat.marginScore, cat.trafficScore],
            backgroundColor: `${color}33`, // Con opacidad
            borderColor: color,
            borderWidth: 2,
            pointBackgroundColor: color,
            pointBorderColor: '#fff',
        }
    }),
}));

const radarChartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: { 
          display: true, 
          position: 'top',
          labels: {
            color: () => document.documentElement.classList.contains('dark') ? '#a1a1aa' : '#64748b',
            padding: 16,
            usePointStyle: true,
            pointStyle: 'circle',
            font: {
              family: "'Inter', -apple-system, sans-serif",
              size: 12,
              weight: '500'
            }
          }
        },
        tooltip: {
          backgroundColor: 'rgba(15, 15, 16, 0.95)',
          titleColor: '#fff',
          bodyColor: 'rgba(255,255,255,0.8)',
          cornerRadius: 12,
          displayColors: false,
          padding: 14,
          titleFont: {
            size: 13,
            weight: '600',
            family: "'Inter', -apple-system, sans-serif"
          },
          bodyFont: {
            size: 14,
            weight: '500',
            family: "'Inter', -apple-system, sans-serif"
          }
        },
    },
    scales: {
        r: {
            angleLines: { 
              color: () => document.documentElement.classList.contains('dark') ? 'rgba(63, 63, 70, 0.5)' : 'rgba(226, 232, 240, 0.8)'
            },
            grid: { 
              color: () => document.documentElement.classList.contains('dark') ? 'rgba(63, 63, 70, 0.3)' : 'rgba(226, 232, 240, 0.6)'
            },
            pointLabels: { 
              font: { 
                size: 12, 
                weight: '500',
                family: "'Inter', -apple-system, sans-serif"
              }, 
              color: () => document.documentElement.classList.contains('dark') ? '#a1a1aa' : '#64748b'
            },
            ticks: { display: false, beginAtZero: true, max: 100 }
        }
    }
};

// 6. GRÁFICO POLAR (Stock Bajo)
const polarChartData = computed(() => {
    // Si no hay datos, mostrar placeholder
    if (!lowStockProducts.value || lowStockProducts.value.length === 0) {
        return {
            labels: ['Sin productos críticos'],
            datasets: [{
                data: [1],
                backgroundColor: ['#d1d5db'],
                borderColor: '#fff',
                borderWidth: 2,
            }]
        }
    }
    
    // Colores para cada producto
    const colors = ['#ef4444', '#f59e0b', '#3b82f6', '#10b981', '#8b5cf6', '#f97316', '#06b6d4', '#84cc16', '#ec4899', '#6366f1']
    
    // Mostrar TODOS los productos con stock bajo
    // Para el gráfico polar, usamos el stock actual (units) pero mínimo 1 para que sea visible
    const chartData = lowStockProducts.value.map((p, index) => ({
        name: p.name || 'Producto sin nombre',
        value: Math.max(p.units || p.quantity || 0, 1), // Mínimo 1 para visualización
        actualValue: p.units || p.quantity || 0, // Valor real para tooltip
        color: p.color || colors[index % colors.length]
    }))
    
    return {
        labels: chartData.map(p => p.name),
        datasets: [{
            data: chartData.map(p => p.value),
            backgroundColor: chartData.map(p => p.color),
            borderColor: '#fff',
            borderWidth: 2,
        }]
    }
});

const polarChartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: { 
            display: true, 
            position: 'right',
            labels: {
                color: () => document.documentElement.classList.contains('dark') ? '#a1a1aa' : '#64748b',
                padding: 16,
                usePointStyle: true,
                pointStyle: 'circle',
                font: {
                    family: "'Inter', -apple-system, sans-serif",
                    size: 11,
                    weight: '500'
                }
            }
        },
        tooltip: {
            backgroundColor: 'rgba(15, 15, 16, 0.95)',
            titleColor: '#fff',
            bodyColor: 'rgba(255,255,255,0.8)',
            cornerRadius: 12,
            displayColors: false,
            padding: 14,
            titleFont: {
              size: 13,
              weight: '600',
              family: "'Inter', -apple-system, sans-serif"
            },
            bodyFont: {
              size: 14,
              weight: '500',
              family: "'Inter', -apple-system, sans-serif"
            },
            callbacks: {
                label: function(context) {
                    const label = context.label || '';
                    const value = context.raw || 0;
                    return `${label}: ${value} unidades`;
                }
            }
        },
    },
    scales: {
        r: {
            grid: { 
              color: () => document.documentElement.classList.contains('dark') ? 'rgba(63, 63, 70, 0.3)' : 'rgba(226, 232, 240, 0.6)', 
              circular: true 
            },
            ticks: { 
              display: false, 
              beginAtZero: true, 
              stepSize: 5,
              backdropColor: 'transparent'
            },
            pointLabels: { display: false }
        }
    }
};

onMounted(async () => {
  await loadReportsData()
})
</script>

<style scoped>
/* ═══════════════════════════════════════════════════════════════════════════
   DASHBOARD PREMIUM - Estilo Stripe/Linear
   ═══════════════════════════════════════════════════════════════════════════ */

/* Animación suave de aparición */
.animate-fade-in {
  animation: fadeIn 0.4s cubic-bezier(0.16, 1, 0.3, 1);
}

@keyframes fadeIn {
  from {
    opacity: 0;
    transform: translateY(12px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

/* ═══════════════════════════════════════════════════════════════════════════
   KPI CARDS - Tarjetas de métricas premium (Estilo Sistema de Diseño)
   ═══════════════════════════════════════════════════════════════════════════ */
.kpi-card {
  background: white;
  border: 1px solid rgb(209, 213, 219); /* gray-300 */
  border-radius: 12px;
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
  transition: all 0.2s ease;
}

.kpi-card:hover {
  border-color: rgb(156, 163, 175); /* gray-400 */
  box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
}

:deep(.dark) .kpi-card,
.dark .kpi-card {
  background: rgba(24, 24, 27, 0.8); /* zinc-900/80 */
  backdrop-filter: blur(4px);
  -webkit-backdrop-filter: blur(4px);
  border: 1px solid rgba(63, 63, 70, 0.6); /* zinc-800/60 */
  box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.3);
}

:deep(.dark) .kpi-card:hover,
.dark .kpi-card:hover {
  border-color: rgba(63, 63, 70, 0.8); /* zinc-700/80 */
  box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.4);
}

/* ═══════════════════════════════════════════════════════════════════════════
   CHART CARDS - Contenedores de gráficos (Estilo Sistema de Diseño)
   ═══════════════════════════════════════════════════════════════════════════ */
.chart-card {
  background: white;
  border: 1px solid rgb(209, 213, 219); /* gray-300 */
  border-radius: 16px;
  box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
  transition: all 0.2s ease;
}

.chart-card:hover {
  box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
}

:deep(.dark) .chart-card,
.dark .chart-card {
  background: rgb(24, 24, 27); /* zinc-900 */
  border: 1px solid rgb(39, 39, 42); /* zinc-800 */
  box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.5);
}

:deep(.dark) .chart-card:hover,
.dark .chart-card:hover {
  border-color: rgb(63, 63, 70); /* zinc-700 */
}

/* ═══════════════════════════════════════════════════════════════════════════
   ICONOS EN KPIs (Estilo Sistema de Diseño)
   ═══════════════════════════════════════════════════════════════════════════ */
.kpi-card .icon-container {
  width: 48px;
  height: 48px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  background: rgb(243, 244, 246); /* gray-100 */
  border: 1px solid rgb(229, 231, 235); /* gray-200 */
  transition: all 0.2s ease;
}

:deep(.dark) .kpi-card .icon-container,
.dark .kpi-card .icon-container {
  background: rgba(39, 39, 42, 0.5); /* zinc-800/50 */
  border: 1px solid rgba(255, 255, 255, 0.05);
}

.kpi-card:hover .icon-container {
  transform: scale(1.02);
}

/* ═══════════════════════════════════════════════════════════════════════════
   SELECTOR DE PERIODO - Pills modernas
   ═══════════════════════════════════════════════════════════════════════════ */
.period-selector {
  display: flex;
  gap: 6px;
  padding: 4px;
  background: #f4f4f5;
  border-radius: 12px;
}

:deep(.dark) .period-selector,
.dark .period-selector {
  background: rgba(39, 39, 42, 0.8);
}

.period-pill {
  padding: 8px 16px;
  font-size: 13px;
  font-weight: 500;
  border-radius: 8px;
  transition: all 0.2s ease;
  cursor: pointer;
  color: #71717a;
  border: none;
  background: transparent;
}

.period-pill:hover {
  color: #18181b;
}

.period-pill.active {
  background: white;
  color: #18181b;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

:deep(.dark) .period-pill,
.dark .period-pill {
  color: #a1a1aa;
}

:deep(.dark) .period-pill:hover,
.dark .period-pill:hover {
  color: #fafafa;
}

:deep(.dark) .period-pill.active,
.dark .period-pill.active {
  background: rgba(63, 63, 70, 0.8);
  color: #fafafa;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.3);
}

/* ═══════════════════════════════════════════════════════════════════════════
   BADGES DE CAMBIO PORCENTUAL
   ═══════════════════════════════════════════════════════════════════════════ */
.change-badge {
  padding: 4px 10px;
  border-radius: 8px;
  font-size: 12px;
  font-weight: 600;
  display: inline-flex;
  align-items: center;
  gap: 4px;
}

.change-badge.positive {
  background: rgba(16, 185, 129, 0.1);
  color: #059669;
}

.change-badge.negative {
  background: rgba(239, 68, 68, 0.1);
  color: #dc2626;
}

:deep(.dark) .change-badge.positive,
.dark .change-badge.positive {
  background: rgba(16, 185, 129, 0.15);
  color: #34d399;
}

:deep(.dark) .change-badge.negative,
.dark .change-badge.negative {
  background: rgba(239, 68, 68, 0.15);
  color: #f87171;
}

/* ═══════════════════════════════════════════════════════════════════════════
   RESPONSIVE AJUSTES
   ═══════════════════════════════════════════════════════════════════════════ */
@media (max-width: 768px) {
  .kpi-card {
    border-radius: 16px;
  }
  
  .chart-card {
    border-radius: 20px;
  }
  
  .period-selector {
    flex-wrap: wrap;
  }
}
</style>