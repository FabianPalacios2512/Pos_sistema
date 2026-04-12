<template>
  <!-- Vista de Selección de Tipo de Negocio - Onboarding -->
  <div class="min-h-screen font-sans bg-gradient-to-b from-gray-50 via-gray-100 to-gray-200 dark:bg-gradient-to-b dark:from-[#141417] dark:via-slate-900 dark:to-[#0a0a0c] transition-colors duration-300">
    
    <div class="flex flex-col items-center justify-center min-h-screen px-4 py-12">
      
      <!-- Logo y Header -->
      <div class="text-center mb-10">
        <div class="w-20 h-20 mx-auto mb-6 bg-white dark:bg-zinc-900 rounded-3xl shadow-xl dark:shadow-black/50 flex items-center justify-center border border-gray-200 dark:border-zinc-800">
          <svg class="w-10 h-10 text-slate-900 dark:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
          </svg>
        </div>
        
        <h1 class="text-3xl lg:text-4xl font-black text-gray-900 dark:text-white tracking-tight mb-3">
          ¿Qué tipo de negocio tienes?
        </h1>
        <p class="text-base text-gray-600 dark:text-zinc-400 max-w-lg mx-auto">
          Selecciona el tipo de negocio para personalizar tu experiencia de venta. 
          Podrás cambiarlo después en <span class="font-semibold">Configuración</span>.
        </p>
      </div>

      <!-- Grid de Opciones de Negocio -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6 max-w-5xl w-full mb-10">
        
        <!-- Comercio General -->
        <button
          @click="selectBusinessType('general')"
          class="group relative bg-white dark:bg-zinc-900 rounded-3xl p-6 lg:p-8 border-2 transition-all duration-300 hover:shadow-2xl hover:-translate-y-1"
          :class="selectedType === 'general' 
            ? 'border-slate-900 dark:border-slate-500 shadow-xl ring-4 ring-slate-900/10 dark:ring-slate-500/20' 
            : 'border-gray-200 dark:border-zinc-800 hover:border-slate-400 dark:hover:border-zinc-600'"
        >
          <!-- Badge de selección -->
          <div 
            v-if="selectedType === 'general'"
            class="absolute -top-3 -right-3 w-8 h-8 bg-slate-900 dark:bg-slate-600 rounded-full flex items-center justify-center shadow-lg animate-scale-in"
          >
            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3">
              <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
            </svg>
          </div>

          <!-- Icono Grande -->
          <div class="w-20 h-20 mx-auto mb-5 bg-blue-50 dark:bg-blue-950/50 rounded-2xl flex items-center justify-center border border-blue-100 dark:border-blue-800/50 group-hover:scale-110 transition-transform duration-300">
            <svg class="w-10 h-10 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
            </svg>
          </div>

          <!-- Contenido -->
          <div class="text-center">
            <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">
              Comercio General
            </h3>
            <p class="text-sm text-gray-600 dark:text-zinc-400 leading-relaxed mb-4">
              Ferreterías, minimarkets, repuestos, papelerías y tiendas variadas.
            </p>
            
            <!-- Características -->
            <div class="flex flex-wrap gap-2 justify-center">
              <span class="px-2.5 py-1 bg-gray-100 dark:bg-zinc-800 text-gray-700 dark:text-zinc-300 text-xs font-medium rounded-full">
                Vista 3 columnas
              </span>
              <span class="px-2.5 py-1 bg-gray-100 dark:bg-zinc-800 text-gray-700 dark:text-zinc-300 text-xs font-medium rounded-full">
                Alta densidad
              </span>
              <span class="px-2.5 py-1 bg-gray-100 dark:bg-zinc-800 text-gray-700 dark:text-zinc-300 text-xs font-medium rounded-full">
                Búsqueda rápida
              </span>
            </div>
          </div>
        </button>

        <!-- Moda y Boutique -->
        <button
          @click="selectBusinessType('fashion')"
          class="group relative bg-white dark:bg-zinc-900 rounded-3xl p-6 lg:p-8 border-2 transition-all duration-300 hover:shadow-2xl hover:-translate-y-1"
          :class="selectedType === 'fashion' 
            ? 'border-slate-900 dark:border-slate-500 shadow-xl ring-4 ring-slate-900/10 dark:ring-slate-500/20' 
            : 'border-gray-200 dark:border-zinc-800 hover:border-slate-400 dark:hover:border-zinc-600'"
        >
          <!-- Badge de selección -->
          <div 
            v-if="selectedType === 'fashion'"
            class="absolute -top-3 -right-3 w-8 h-8 bg-slate-900 dark:bg-slate-600 rounded-full flex items-center justify-center shadow-lg animate-scale-in"
          >
            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3">
              <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
            </svg>
          </div>

          <!-- Icono Grande -->
          <div class="w-20 h-20 mx-auto mb-5 bg-pink-50 dark:bg-pink-950/50 rounded-2xl flex items-center justify-center border border-pink-100 dark:border-pink-800/50 group-hover:scale-110 transition-transform duration-300">
            <svg class="w-10 h-10 text-pink-600 dark:text-pink-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
            </svg>
          </div>

          <!-- Contenido -->
          <div class="text-center">
            <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">
              Moda y Boutique
            </h3>
            <p class="text-sm text-gray-600 dark:text-zinc-400 leading-relaxed mb-4">
              Tiendas de ropa, calzado, accesorios, joyería y productos de moda.
            </p>
            
            <!-- Características -->
            <div class="flex flex-wrap gap-2 justify-center">
              <span class="px-2.5 py-1 bg-gray-100 dark:bg-zinc-800 text-gray-700 dark:text-zinc-300 text-xs font-medium rounded-full">
                Vista 2 columnas
              </span>
              <span class="px-2.5 py-1 bg-gray-100 dark:bg-zinc-800 text-gray-700 dark:text-zinc-300 text-xs font-medium rounded-full">
                Fotos grandes
              </span>
              <span class="px-2.5 py-1 bg-gray-100 dark:bg-zinc-800 text-gray-700 dark:text-zinc-300 text-xs font-medium rounded-full">
                Variantes (talla/color)
              </span>
            </div>
          </div>
        </button>

        <!-- Comida Rápida -->
        <button
          @click="selectBusinessType('fast_food')"
          class="group relative bg-white dark:bg-zinc-900 rounded-3xl p-6 lg:p-8 border-2 transition-all duration-300 hover:shadow-2xl hover:-translate-y-1"
          :class="selectedType === 'fast_food' 
            ? 'border-slate-900 dark:border-slate-500 shadow-xl ring-4 ring-slate-900/10 dark:ring-slate-500/20' 
            : 'border-gray-200 dark:border-zinc-800 hover:border-slate-400 dark:hover:border-zinc-600'"
        >
          <!-- Badge de selección -->
          <div 
            v-if="selectedType === 'fast_food'"
            class="absolute -top-3 -right-3 w-8 h-8 bg-slate-900 dark:bg-slate-600 rounded-full flex items-center justify-center shadow-lg animate-scale-in"
          >
            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3">
              <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
            </svg>
          </div>

          <!-- Badge Popular -->
          <div class="absolute -top-3 left-4 px-3 py-1 bg-gradient-to-r from-orange-500 to-red-500 text-white text-xs font-bold rounded-full shadow-lg">
            Popular
          </div>

          <!-- Icono Grande -->
          <div class="w-20 h-20 mx-auto mb-5 bg-orange-50 dark:bg-orange-950/50 rounded-2xl flex items-center justify-center border border-orange-100 dark:border-orange-800/50 group-hover:scale-110 transition-transform duration-300">
            <svg class="w-10 h-10 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
            </svg>
          </div>

          <!-- Contenido -->
          <div class="text-center">
            <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">
              Comida Rápida
            </h3>
            <p class="text-sm text-gray-600 dark:text-zinc-400 leading-relaxed mb-4">
              Restaurantes, fritos, cafeterías, pizzerías, taquerías y comida rápida.
            </p>
            
            <!-- Características -->
            <div class="flex flex-wrap gap-2 justify-center">
              <span class="px-2.5 py-1 bg-gray-100 dark:bg-zinc-800 text-gray-700 dark:text-zinc-300 text-xs font-medium rounded-full">
                Categorías por tabs
              </span>
              <span class="px-2.5 py-1 bg-gray-100 dark:bg-zinc-800 text-gray-700 dark:text-zinc-300 text-xs font-medium rounded-full">
                Un clic = agregar
              </span>
              <span class="px-2.5 py-1 bg-gray-100 dark:bg-zinc-800 text-gray-700 dark:text-zinc-300 text-xs font-medium rounded-full">
                Mesa/Llevar
              </span>
            </div>
          </div>
        </button>
      </div>

      <!-- Preview Dinámico -->
      <div v-if="selectedType" class="w-full max-w-4xl mb-8 animate-fade-in">
        <div class="bg-white dark:bg-zinc-900 rounded-2xl shadow-xl dark:shadow-black/50 border border-gray-200 dark:border-zinc-800 overflow-hidden">
          <div class="p-4 border-b border-gray-100 dark:border-zinc-800">
            <div class="flex items-center gap-2">
              <div class="w-3 h-3 rounded-full bg-red-400"></div>
              <div class="w-3 h-3 rounded-full bg-yellow-400"></div>
              <div class="w-3 h-3 rounded-full bg-green-400"></div>
              <span class="ml-3 text-sm font-medium text-gray-500 dark:text-zinc-400">
                Vista previa - {{ layoutPreviewTitle }}
              </span>
            </div>
          </div>
          
          <!-- Preview según tipo -->
          <div class="p-4 h-48 overflow-hidden relative">
            <!-- General Preview -->
            <div v-if="selectedType === 'general'" class="flex gap-3 h-full">
              <div class="w-1/3 bg-gray-50 dark:bg-zinc-800 rounded-xl p-3">
                <div class="text-xs font-bold text-gray-500 dark:text-zinc-400 mb-2">Categorías</div>
                <div class="space-y-1.5">
                  <div class="h-6 bg-gray-200 dark:bg-zinc-700 rounded-lg animate-pulse"></div>
                  <div class="h-6 bg-gray-200 dark:bg-zinc-700 rounded-lg animate-pulse"></div>
                  <div class="h-6 bg-gray-200 dark:bg-zinc-700 rounded-lg animate-pulse"></div>
                </div>
              </div>
              <div class="w-1/3 bg-gray-50 dark:bg-zinc-800 rounded-xl p-3">
                <div class="text-xs font-bold text-gray-500 dark:text-zinc-400 mb-2">Productos</div>
                <div class="grid grid-cols-3 gap-1.5">
                  <div v-for="i in 9" :key="i" class="aspect-square bg-gray-200 dark:bg-zinc-700 rounded-lg animate-pulse"></div>
                </div>
              </div>
              <div class="w-1/3 bg-gray-50 dark:bg-zinc-800 rounded-xl p-3">
                <div class="text-xs font-bold text-gray-500 dark:text-zinc-400 mb-2">Ticket</div>
                <div class="space-y-1.5">
                  <div class="h-4 bg-gray-200 dark:bg-zinc-700 rounded animate-pulse w-full"></div>
                  <div class="h-4 bg-gray-200 dark:bg-zinc-700 rounded animate-pulse w-3/4"></div>
                  <div class="h-8 mt-4 bg-emerald-200 dark:bg-emerald-800 rounded-lg animate-pulse"></div>
                </div>
              </div>
            </div>

            <!-- Fashion Preview -->
            <div v-else-if="selectedType === 'fashion'" class="flex gap-3 h-full">
              <div class="flex-1 bg-gray-50 dark:bg-zinc-800 rounded-xl p-3">
                <div class="text-xs font-bold text-gray-500 dark:text-zinc-400 mb-2">Catálogo Visual</div>
                <div class="grid grid-cols-4 gap-2">
                  <div v-for="i in 4" :key="i" class="aspect-[3/4] bg-gradient-to-b from-pink-100 to-pink-200 dark:from-pink-900/50 dark:to-pink-800/50 rounded-xl"></div>
                </div>
              </div>
              <div class="w-64 bg-gray-50 dark:bg-zinc-800 rounded-xl p-3">
                <div class="text-xs font-bold text-gray-500 dark:text-zinc-400 mb-2">Carrito Elegante</div>
                <div class="space-y-2">
                  <div class="h-4 bg-gray-200 dark:bg-zinc-700 rounded animate-pulse"></div>
                  <div class="h-4 bg-gray-200 dark:bg-zinc-700 rounded animate-pulse w-3/4"></div>
                  <div class="h-10 mt-4 bg-slate-800 dark:bg-slate-600 rounded-xl"></div>
                </div>
              </div>
            </div>

            <!-- Fast Food Preview -->
            <div v-else-if="selectedType === 'fast_food'" class="flex gap-3 h-full">
              <div class="flex-1 bg-gray-50 dark:bg-zinc-800 rounded-xl p-3">
                <div class="flex gap-2 mb-3">
                  <div class="px-3 py-1.5 bg-orange-500 text-white text-xs font-bold rounded-full">Fritos</div>
                  <div class="px-3 py-1.5 bg-gray-200 dark:bg-zinc-700 text-xs font-medium rounded-full">Bebidas</div>
                  <div class="px-3 py-1.5 bg-gray-200 dark:bg-zinc-700 text-xs font-medium rounded-full">Combos</div>
                </div>
                <div class="grid grid-cols-4 gap-2">
                  <div v-for="i in 4" :key="i" class="aspect-square bg-gradient-to-b from-orange-100 to-orange-200 dark:from-orange-900/50 dark:to-orange-800/50 rounded-xl flex items-center justify-center">
                    <span class="text-2xl"></span>
                  </div>
                </div>
              </div>
              <div class="w-64 bg-gray-50 dark:bg-zinc-800 rounded-xl p-3">
                <div class="flex gap-1 mb-3">
                  <div class="flex-1 py-1.5 bg-emerald-500 text-white text-[10px] font-bold rounded-lg text-center">AQUÍ</div>
                  <div class="flex-1 py-1.5 bg-gray-200 dark:bg-zinc-700 text-[10px] font-medium rounded-lg text-center">LLEVAR</div>
                </div>
                <div class="space-y-1.5">
                  <div class="h-4 bg-gray-200 dark:bg-zinc-700 rounded animate-pulse"></div>
                  <div class="h-4 bg-gray-200 dark:bg-zinc-700 rounded animate-pulse w-2/3"></div>
                </div>
                <div class="mt-3 grid grid-cols-2 gap-2">
                  <div class="h-10 bg-emerald-500 rounded-xl"></div>
                  <div class="h-10 bg-blue-500 rounded-xl"></div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Botón de Continuar -->
      <div class="flex flex-col items-center gap-4">
        <button
          @click="confirmSelection"
          :disabled="!selectedType || isLoading"
          class="px-12 py-4 bg-slate-900 dark:bg-slate-700 hover:bg-black dark:hover:bg-slate-600 text-white text-lg font-bold rounded-2xl shadow-xl shadow-slate-400/40 dark:shadow-slate-900/50 transition-all duration-300 disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:bg-slate-900 flex items-center gap-3"
        >
          <svg v-if="isLoading" class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
          </svg>
          <span>{{ isLoading ? 'Configurando...' : 'Continuar a mi POS' }}</span>
          <svg v-if="!isLoading" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
          </svg>
        </button>
        
        <p class="text-xs text-gray-500 dark:text-zinc-500">
          Puedes cambiar esto después en <span class="font-semibold">Configuración > Tipo de Negocio</span>
        </p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useRouter } from 'vue-router'
import apiClient from '../services/apiClient.js'

const router = useRouter()

// Estado
const selectedType = ref(null)
const isLoading = ref(false)

// Computed
const layoutPreviewTitle = computed(() => {
  const titles = {
    'general': 'Comercio General - 3 columnas, alta densidad',
    'fashion': 'Moda y Boutique - 2 columnas, fotos grandes',
    'fast_food': 'Comida Rápida - Tabs por categoría, pagos rápidos'
  }
  return titles[selectedType.value] || ''
})

// Métodos
const selectBusinessType = (type) => {
  selectedType.value = type
}

const confirmSelection = async () => {
  if (!selectedType.value) return
  
  isLoading.value = true
  
  try {
    // Guardar en backend
    await apiClient.post('/settings/business-layout', {
      business_layout_type: selectedType.value,
      business_layout_selected: true
    })
    
    // Guardar en localStorage para carga inmediata
    localStorage.setItem('business_layout_type', selectedType.value)
    localStorage.setItem('business_layout_selected', 'true')
    
    // Redirigir al POS
    router.push('/pos')
    
  } catch (error) {
    console.error('Error guardando tipo de negocio:', error)
    // Aún así continuar al POS con el valor seleccionado localmente
    localStorage.setItem('business_layout_type', selectedType.value)
    router.push('/pos')
  } finally {
    isLoading.value = false
  }
}
</script>

<style scoped>
.animate-fade-in {
  animation: fadeIn 0.3s ease-out;
}

.animate-scale-in {
  animation: scaleIn 0.2s ease-out;
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

@keyframes scaleIn {
  from {
    opacity: 0;
    transform: scale(0.8);
  }
  to {
    opacity: 1;
    transform: scale(1);
  }
}
</style>
