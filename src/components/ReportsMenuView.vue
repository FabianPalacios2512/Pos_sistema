<template>
  <div class="min-h-screen bg-gray-100 font-sans">
    <div class="p-4 lg:p-6 space-y-6 animate-fade-in">
      
      <!-- Navegación de Reportes como Submenú -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        
        <!-- Reportes Generales -->
        <button 
          @click="setActiveReport('general')"
          :class="[
            'group relative px-6 py-4 rounded-xl border-2 transition-all duration-200 text-left',
            activeReport === 'general' 
              ? 'bg-blue-50 border-blue-300 shadow-md' 
              : 'bg-white border-gray-200 hover:border-blue-300 hover:shadow-md hover:bg-blue-25'
          ]"
        >
          <div class="flex items-center space-x-3">
            <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center transition-colors group-hover:bg-blue-200">
              <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
              </svg>
            </div>
            <div class="flex-1">
              <h3 class="text-sm font-bold text-gray-900 group-hover:text-blue-700">Reportes Generales</h3>
              <p class="text-xs text-gray-500 mt-1">Dashboard ejecutivo</p>
            </div>
            <!-- Indicador de selección -->
            <div v-if="activeReport === 'general'" class="w-2 h-2 bg-blue-500 rounded-full"></div>
          </div>
          <span class="absolute top-2 right-3 text-xs font-semibold text-blue-600 bg-blue-100 px-2 py-1 rounded-full">
            Ejecutivo
          </span>
        </button>

        <!-- Reportes de Caja -->
        <button 
          @click="setActiveReport('caja')"
          :class="[
            'group relative px-6 py-4 rounded-xl border-2 transition-all duration-200 text-left',
            activeReport === 'caja' 
              ? 'bg-green-50 border-green-300 shadow-md' 
              : 'bg-white border-gray-200 hover:border-green-300 hover:shadow-md hover:bg-green-25'
          ]"
        >
          <div class="flex items-center space-x-3">
            <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center transition-colors group-hover:bg-green-200">
              <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
              </svg>
            </div>
            <div class="flex-1">
              <h3 class="text-sm font-bold text-gray-900 group-hover:text-green-700">Reportes de Caja</h3>
              <p class="text-xs text-gray-500 mt-1">Análisis por cajero</p>
            </div>
            <!-- Indicador de selección -->
            <div v-if="activeReport === 'caja'" class="w-2 h-2 bg-green-500 rounded-full"></div>
          </div>
          <span class="absolute top-2 right-3 text-xs font-semibold text-green-600 bg-green-100 px-2 py-1 rounded-full">
            Activo
          </span>
        </button>

        <!-- Reportes de Inventario -->
        <button 
          @click="setActiveReport('inventario')"
          :class="[
            'group relative px-6 py-4 rounded-xl border-2 transition-all duration-200 text-left opacity-75 cursor-not-allowed',
            activeReport === 'inventario' 
              ? 'bg-amber-50 border-amber-300 shadow-md' 
              : 'bg-gray-50 border-gray-200'
          ]"
          disabled
        >
          <div class="flex items-center space-x-3">
            <div class="w-10 h-10 bg-amber-100 rounded-lg flex items-center justify-center">
              <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
              </svg>
            </div>
            <div class="flex-1">
              <h3 class="text-sm font-bold text-gray-700">Reportes de Inventario</h3>
              <p class="text-xs text-gray-500 mt-1">Control de stock</p>
            </div>
          </div>
          <span class="absolute top-2 right-3 text-xs font-semibold text-amber-600 bg-amber-100 px-2 py-1 rounded-full">
            Próximo
          </span>
        </button>

        <!-- Reportes Financieros -->
        <button 
          @click="setActiveReport('financiero')"
          :class="[
            'group relative px-6 py-4 rounded-xl border-2 transition-all duration-200 text-left opacity-75 cursor-not-allowed',
            activeReport === 'financiero' 
              ? 'bg-orange-50 border-orange-300 shadow-md' 
              : 'bg-gray-50 border-gray-200'
          ]"
          disabled
        >
          <div class="flex items-center space-x-3">
            <div class="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center">
              <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
              </svg>
            </div>
            <div class="flex-1">
              <h3 class="text-sm font-bold text-gray-700">Reportes Financieros</h3>
              <p class="text-xs text-gray-500 mt-1">Análisis económico</p>
            </div>
          </div>
          <span class="absolute top-2 right-3 text-xs font-semibold text-orange-600 bg-orange-100 px-2 py-1 rounded-full">
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
</template>

<script setup>
import { ref, defineAsyncComponent } from 'vue'

// Componentes lazy loading
const ReportsView = defineAsyncComponent(() => import('./ReportsView.vue'))
const ReportesCajaView = defineAsyncComponent(() => import('./ReportesCajaView.vue'))

// Estado reactivo
const activeReport = ref('general')

// Métodos
const setActiveReport = (report) => {
  activeReport.value = report
}
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