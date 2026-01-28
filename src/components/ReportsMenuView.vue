<template>
  <div class="min-h-screen font-sans bg-gradient-to-b from-gray-50 via-gray-100 to-gray-200 dark:bg-gradient-to-b dark:from-[#141417] dark:via-slate-900 dark:to-[#0a0a0c] transition-colors duration-300">
    <!-- Contenedor con ancho expandido -->
    <div class="max-w-[95%] mx-auto px-4 lg:px-8">
      <div class="py-4 lg:py-6 space-y-6 pb-8 animate-fade-in">
        
        <!-- Navegación de Reportes como Submenú -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
          
          <!-- Reportes Generales -->
          <button 
            @click="setActiveReport('general')"
            :class="[
              'group relative px-5 py-4 rounded-2xl border transition-all duration-300 text-left',
              activeReport === 'general' 
                ? 'bg-white dark:bg-zinc-900 border-blue-300 dark:border-blue-700 shadow-lg dark:shadow-black/50' 
                : 'bg-white dark:bg-zinc-900 border-gray-300 dark:border-zinc-800 hover:border-blue-300 dark:hover:border-blue-700 hover:shadow-lg dark:hover:shadow-black/50'
            ]"
          >
            <div class="flex items-center space-x-3">
              <div class="w-11 h-11 rounded-xl flex items-center justify-center transition-colors bg-gray-100 dark:bg-zinc-800/50 border border-gray-200 dark:border-white/5">
                <svg class="w-5 h-5 text-blue-600 dark:text-blue-400 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
              </div>
              <div class="flex-1">
                <h3 class="text-sm font-bold text-gray-900 dark:text-white transition-colors">Reportes Generales</h3>
                <p class="text-xs text-gray-600 dark:text-zinc-400 mt-0.5 transition-colors">Dashboard ejecutivo</p>
              </div>
              <div v-if="activeReport === 'general'" class="w-2 h-2 bg-blue-500 rounded-full animate-pulse"></div>
            </div>
            <span class="absolute top-2 right-3 text-[10px] font-bold text-blue-700 dark:text-blue-400 bg-blue-50 dark:bg-blue-950 px-2 py-0.5 rounded-md border border-blue-100 dark:border-blue-800 uppercase tracking-wide">
              Ejecutivo
            </span>
          </button>

          <!-- Reportes de Caja -->
          <button 
            @click="setActiveReport('caja')"
            :class="[
              'group relative px-5 py-4 rounded-2xl border transition-all duration-300 text-left',
              activeReport === 'caja' 
                ? 'bg-white dark:bg-zinc-900 border-emerald-300 dark:border-emerald-700 shadow-lg dark:shadow-black/50' 
                : 'bg-white dark:bg-zinc-900 border-gray-300 dark:border-zinc-800 hover:border-emerald-300 dark:hover:border-emerald-700 hover:shadow-lg dark:hover:shadow-black/50'
            ]"
          >
            <div class="flex items-center space-x-3">
              <div class="w-11 h-11 rounded-xl flex items-center justify-center transition-colors bg-gray-100 dark:bg-zinc-800/50 border border-gray-200 dark:border-white/5">
                <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
              </div>
              <div class="flex-1">
                <h3 class="text-sm font-bold text-gray-900 dark:text-white transition-colors">Reportes de Caja</h3>
                <p class="text-xs text-gray-600 dark:text-zinc-400 mt-0.5 transition-colors">Análisis por cajero</p>
              </div>
              <div v-if="activeReport === 'caja'" class="w-2 h-2 bg-emerald-500 rounded-full animate-pulse"></div>
            </div>
            <span class="absolute top-2 right-3 text-[10px] font-bold text-emerald-700 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-950 px-2 py-0.5 rounded-md border border-emerald-100 dark:border-emerald-800 uppercase tracking-wide">
              Activo
            </span>
          </button>

          <!-- Reportes de Inventario -->
          <button 
            @click="setActiveReport('inventario')"
            class="group relative px-5 py-4 rounded-2xl border transition-all duration-300 text-left opacity-60 cursor-not-allowed bg-white dark:bg-zinc-900 border-gray-300 dark:border-zinc-800"
            disabled
          >
            <div class="flex items-center space-x-3">
              <div class="w-11 h-11 rounded-xl flex items-center justify-center bg-gray-100 dark:bg-zinc-800/50 border border-gray-200 dark:border-white/5">
                <svg class="w-5 h-5 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                </svg>
              </div>
              <div class="flex-1">
                <h3 class="text-sm font-bold text-gray-700 dark:text-zinc-400">Reportes de Inventario</h3>
                <p class="text-xs text-gray-500 dark:text-zinc-500 mt-0.5">Control de stock</p>
              </div>
            </div>
            <span class="absolute top-2 right-3 text-[10px] font-bold text-amber-700 dark:text-amber-400 bg-amber-50 dark:bg-amber-950 px-2 py-0.5 rounded-md border border-amber-100 dark:border-amber-800 uppercase tracking-wide">
              Próximo
            </span>
          </button>

          <!-- Reportes Financieros -->
          <button 
            @click="setActiveReport('financiero')"
            class="group relative px-5 py-4 rounded-2xl border transition-all duration-300 text-left opacity-60 cursor-not-allowed bg-white dark:bg-zinc-900 border-gray-300 dark:border-zinc-800"
            disabled
          >
            <div class="flex items-center space-x-3">
              <div class="w-11 h-11 rounded-xl flex items-center justify-center bg-gray-100 dark:bg-zinc-800/50 border border-gray-200 dark:border-white/5">
                <svg class="w-5 h-5 text-rose-600 dark:text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
              </div>
              <div class="flex-1">
                <h3 class="text-sm font-bold text-gray-700 dark:text-zinc-400">Reportes Financieros</h3>
                <p class="text-xs text-gray-500 dark:text-zinc-500 mt-0.5">Análisis económico</p>
              </div>
            </div>
            <span class="absolute top-2 right-3 text-[10px] font-bold text-rose-700 dark:text-rose-400 bg-rose-50 dark:bg-rose-950 px-2 py-0.5 rounded-md border border-rose-100 dark:border-rose-800 uppercase tracking-wide">
              Próximo
            </span>
          </button>

        </div>

      <!-- Contenido del reporte activo -->
      <div class="space-y-6">
        
        <!-- Reportes Generales -->
        <div v-if="activeReport === 'general'">
          <ReportsView />
        </div>

        <!-- Reportes de Caja (Nueva funcionalidad) -->
        <div v-if="activeReport === 'caja'">
          <ReportesCajaView />
        </div>

        <!-- Reportes de Inventario -->
        <div v-if="activeReport === 'inventario'">
          <div class="bg-white rounded-2xl p-6 border border-gray-300">
            <div class="text-center py-12">
              <div class="w-20 h-20 bg-purple-100 rounded-2xl mx-auto mb-6 flex items-center justify-center">
                <svg class="w-10 h-10 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                </svg>
              </div>
              <h3 class="text-xl font-bold text-gray-900 mb-4">Reportes de Inventario</h3>
              <p class="text-gray-600 mb-6 max-w-md mx-auto">Los reportes detallados de inventario estarán disponibles en la próxima actualización.</p>
              <button class="px-5 py-2 bg-gradient-to-r from-lime-400 to-green-400 hover:from-lime-500 hover:to-green-500 text-white text-sm font-bold rounded-xl shadow-lg transition-all duration-300 transform hover:scale-105">
                Notificarme cuando esté listo
              </button>
            </div>
          </div>
        </div>

        <!-- Reportes Financieros -->
        <div v-if="activeReport === 'financiero'">
          <div class="bg-white rounded-2xl p-6 border border-gray-300">
            <div class="text-center py-12">
              <div class="w-20 h-20 bg-orange-100 rounded-2xl mx-auto mb-6 flex items-center justify-center">
                <svg class="w-10 h-10 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
              </div>
              <h3 class="text-xl font-bold text-gray-900 mb-4">Reportes Financieros</h3>
              <p class="text-gray-600 mb-6 max-w-md mx-auto">Los reportes financieros avanzados estarán disponibles en la próxima actualización.</p>
              <button class="px-5 py-2 bg-gradient-to-r from-lime-400 to-green-400 hover:from-lime-500 hover:to-green-500 text-white text-sm font-bold rounded-xl shadow-lg transition-all duration-300 transform hover:scale-105">
                Notificarme cuando esté listo
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
import { ref, defineAsyncComponent, onMounted, onBeforeUnmount, watch } from 'vue'
import { useUIContextStore } from '@/store/uiContextStore'

// Componentes lazy loading
const ReportsView = defineAsyncComponent(() => import('./ReportsView.vue'))
const ReportesCajaView = defineAsyncComponent(() => import('./ReportesCajaView.vue'))

// Estado reactivo
const activeReport = ref('general')

// Store de contexto IA
const uiContextStore = useUIContextStore()

// Métodos
const setActiveReport = (report) => {
  activeReport.value = report
}

// ═══════════════════════════════════════════════════════════════
// 🤖 CONTEXTO IA - Menú de Reportes
// ═══════════════════════════════════════════════════════════════

// Actualizar contexto para la IA
const actualizarContextoIA = () => {
  uiContextStore.setScreenData('reports-menu', {
    modulo: 'Reportes',
    descripcion: 'Menú principal de reportes con acceso a Reportes Generales y Reportes de Caja',
    reporteActivo: activeReport.value,
    reportesDisponibles: ['general', 'caja'],
    reporteActivoNombre: activeReport.value === 'general' ? 'Reportes Generales' : 
                         activeReport.value === 'caja' ? 'Reportes de Caja' : 'Otro'
  })
}

// Registrar acciones disponibles para la IA
const registrarAccionesIA = () => {
  // Cambiar a reporte de caja
  uiContextStore.registerAction('cambiarAReporteCaja', async () => {
    activeReport.value = 'caja'
    return { success: true, message: 'Cambiado a Reportes de Caja' }
  })
  
  // Cambiar a reporte general
  uiContextStore.registerAction('cambiarAReporteGeneral', async () => {
    activeReport.value = 'general'
    return { success: true, message: 'Cambiado a Reportes Generales' }
  })
  
  // Cambiar tipo de reporte
  uiContextStore.registerAction('cambiarTipoReporte', async ({ tipo }) => {
    if (tipo === 'caja' || tipo === 'cajeros') {
      activeReport.value = 'caja'
      return { success: true, message: 'Cambiado a Reportes de Caja' }
    } else if (tipo === 'general' || tipo === 'ventas') {
      activeReport.value = 'general'
      return { success: true, message: 'Cambiado a Reportes Generales' }
    }
    return { success: false, message: 'Tipo de reporte no válido. Usa "general" o "caja".' }
  })
}

onMounted(() => {
  registrarAccionesIA()
  actualizarContextoIA()
})

onBeforeUnmount(() => {
  uiContextStore.clearSelection()
})

// Actualizar contexto cuando cambia el reporte activo
watch(activeReport, () => {
  actualizarContextoIA()
})
</script>

<style scoped>
/* Animación suave de aparición */
.animate-fade-in {
  animation: fadeIn 0.3s ease-in-out;
}

@keyframes fadeIn {
  from {
    opacity: 0;
    transform: translateY(10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}
</style>