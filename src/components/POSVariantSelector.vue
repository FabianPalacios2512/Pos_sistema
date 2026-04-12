<template>
  <!-- Modal Touch-Friendly para Selección de Variantes -->
  <Teleport to="body">
    <Transition name="modal-fade">
      <div v-if="show" 
           class="fixed inset-0 bg-black/70  flex items-center justify-center z-[9999] p-4"
           @click.self="closeModal">
        
        <div class="bg-white dark:bg-zinc-900 rounded-2xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-hidden border border-gray-200 dark:border-zinc-800 animate-scale-in">
          
          <!-- Header Minimalista Executive -->
          <div class="bg-white dark:bg-zinc-900 px-6 py-5 border-b border-gray-100 dark:border-zinc-800">
            <div class="flex items-center justify-between">
              <div class="flex-1">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white">{{ product?.name }}</h3>
                <p class="text-sm text-gray-500 dark:text-zinc-400 mt-0.5">
                  Precio base <span class="font-semibold text-gray-900 dark:text-white">${{ formatCurrency(product?.price || 0) }}</span>
                </p>
              </div>
              <button @click="closeModal" 
                      class="p-2 hover:bg-gray-100 dark:hover:bg-zinc-800 rounded-lg transition-colors">
                <svg class="w-5 h-5 text-gray-400 dark:text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
              </button>
            </div>
          </div>

          <!-- Body: Selectores de Variantes -->
          <div class="p-6 overflow-y-auto max-h-[calc(90vh-220px)]">
            
            <!-- Iteración por cada grupo de opciones -->
            <div v-for="(optionGroup, groupIndex) in variantOptions" 
                 :key="groupIndex"
                 class="mb-8 last:mb-0">
              
              <!-- Título del Grupo (Minimalista) -->
              <h4 class="text-xs font-bold text-gray-500 dark:text-zinc-400 uppercase tracking-widest mb-3">
                {{ optionGroup.name }}
              </h4>
              
              <!-- Para COLORES: Solo Círculos (sin texto) -->
              <div v-if="optionGroup.name.toLowerCase().includes('color')" class="flex items-center gap-3 flex-wrap">
                <button v-for="value in optionGroup.values" 
                        :key="value"
                        @click="selectOption(optionGroup.name, value)"
                        :disabled="!isOptionAvailable(optionGroup.name, value)"
                        :title="value"
                        :class="[
                          'relative transition-all duration-200 touch-manipulation group',
                          !isOptionAvailable(optionGroup.name, value) && 'opacity-40 cursor-not-allowed'
                        ]">
                  
                  <!-- Ring de selección -->
                  <div v-if="selectedOptions[optionGroup.name] === value"
                       class="absolute -inset-1.5 rounded-full border-3 border-slate-700 dark:border-slate-300 shadow-lg"></div>
                  
                  <!-- Círculo de color (detecta si es HEX o nombre y convierte) -->
                  <div :style="{ backgroundColor: getColorDisplay(value) }"
                       :class="[
                         'w-12 h-12 rounded-full transition-all shadow-md hover:shadow-xl hover:scale-110',
                         (value.toUpperCase() === '#FFFFFF' || getColorDisplay(value).toUpperCase() === '#FFFFFF') && 'border-2 border-gray-300 dark:border-zinc-600',
                         !isOptionAvailable(optionGroup.name, value) && 'opacity-50'
                       ]">
                    <!-- X para sin stock -->
                    <div v-if="!isOptionAvailable(optionGroup.name, value)"
                         class="w-full h-full flex items-center justify-center">
                      <svg class="w-6 h-6 text-white drop-shadow-lg" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"/>
                      </svg>
                    </div>
                  </div>
                </button>
              </div>
              
              <!-- Para TALLAS: Botones Rectangulares Limpios -->
              <div v-else class="grid grid-cols-4 sm:grid-cols-5 gap-2">
                <button v-for="value in optionGroup.values" 
                        :key="value"
                        @click="selectOption(optionGroup.name, value)"
                        :disabled="!isOptionAvailable(optionGroup.name, value)"
                        :class="[
                          'relative px-4 py-3 rounded-lg text-sm font-semibold transition-all duration-150 touch-manipulation',
                          selectedOptions[optionGroup.name] === value
                            ? 'bg-gray-900 dark:bg-white text-white dark:text-zinc-900 border-2 border-gray-900 dark:border-white'
                            : isOptionAvailable(optionGroup.name, value)
                            ? 'bg-white dark:bg-zinc-800 text-gray-700 dark:text-zinc-300 border border-gray-300 dark:border-zinc-700 hover:border-gray-400 dark:hover:border-zinc-600'
                            : 'bg-gray-50 dark:bg-zinc-900/50 text-gray-300 dark:text-zinc-700 border border-gray-200 dark:border-zinc-800 cursor-not-allowed line-through'
                        ]">
                  {{ value }}
                </button>
              </div>
            </div>

            <!-- Resumen de Selección (Invoice Style) -->
            <div v-if="selectedVariant" 
                 class="mt-8 p-5 bg-gray-50 dark:bg-zinc-800/50 rounded-xl border border-gray-200 dark:border-zinc-700">
              <div class="flex items-center justify-between mb-3">
                <div class="flex-1">
                  <p class="text-xs font-medium text-gray-500 dark:text-zinc-400 uppercase tracking-wider mb-1">
                    Variante
                  </p>
                  <p class="text-sm font-semibold text-gray-900 dark:text-white">
                    {{ getVariantSummary() }}
                  </p>
                </div>
                <div class="text-right">
                  <p class="text-2xl font-bold text-gray-900 dark:text-white">
                    ${{ formatCurrency(selectedVariant.price) }}
                  </p>
                </div>
              </div>
              <div class="flex items-center justify-end pt-2 border-t border-gray-200 dark:border-zinc-700">
                <p class="text-xs text-gray-500 dark:text-zinc-400">
                  Stock disponible: <span class="font-semibold text-gray-700 dark:text-zinc-300">{{ selectedVariant.stock }} unidades</span>
                </p>
              </div>
            </div>

            <!-- Advertencia si no están todas las opciones -->
            <div v-if="!allOptionsSelected && Object.keys(selectedOptions).length > 0" 
                 class="mt-4 p-3 bg-amber-50 dark:bg-amber-950/20 rounded-lg border border-amber-200 dark:border-amber-800 flex items-center gap-2">
              <svg class="w-5 h-5 text-amber-600 dark:text-amber-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16c-.77.833.192 2.5 1.732 2.5z"/>
              </svg>
              <p class="text-xs font-medium text-amber-700 dark:text-amber-400">
                Selecciona todas las opciones para continuar
              </p>
            </div>
          </div>

          <!-- Footer Minimalista -->
          <div class="sticky bottom-0 bg-white dark:bg-zinc-900 border-t border-gray-200 dark:border-zinc-800 px-6 py-4">
            <div class="flex items-center gap-3">
              <button @click="closeModal" 
                      class="px-6 py-3 text-gray-600 dark:text-zinc-400 hover:bg-gray-100 dark:hover:bg-zinc-800 rounded-lg text-sm font-medium transition-all touch-manipulation">
                Cancelar
              </button>
              <button @click="confirmSelection" 
                      :disabled="!allOptionsSelected"
                      :class="[
                        'flex-1 px-8 py-3 rounded-lg text-sm font-semibold transition-all touch-manipulation',
                        allOptionsSelected
                          ? 'bg-gray-900 dark:bg-slate-700 hover:bg-black dark:hover:bg-slate-600 text-white'
                          : 'bg-gray-200 dark:bg-zinc-800 text-gray-400 dark:text-zinc-600 cursor-not-allowed'
                      ]">
                Confirmar y Agregar
              </button>
            </div>
          </div>

        </div>
      </div>
    </Transition>
  </Teleport>
</template>

<script setup>
import { ref, computed, watch } from 'vue'

// Props
const props = defineProps({
  show: {
    type: Boolean,
    default: false
  },
  product: {
    type: Object,
    default: null
  }
})

// Emits
const emit = defineEmits(['close', 'confirm'])

// Estado local
const selectedOptions = ref({})
const selectedVariant = ref(null)

// Computed: Extraer opciones de variantes
const variantOptions = computed(() => {
  if (!props.product?.variants || props.product.variants.length === 0) return []
  
  const optionsMap = {}
  
  props.product.variants.forEach(variant => {
    if (variant.options_summary) {
      const options = typeof variant.options_summary === 'string' 
        ? JSON.parse(variant.options_summary) 
        : variant.options_summary
      
      options.forEach(opt => {
        if (!optionsMap[opt.name]) {
          optionsMap[opt.name] = new Set()
        }
        optionsMap[opt.name].add(opt.value)
      })
    }
  })
  
  return Object.entries(optionsMap).map(([name, valuesSet]) => ({
    name,
    values: Array.from(valuesSet).sort()
  }))
})

// Computed: Todas las opciones seleccionadas
const allOptionsSelected = computed(() => {
  return variantOptions.value.length > 0 && 
         variantOptions.value.every(group => selectedOptions.value[group.name])
})

// Métodos
const closeModal = () => {
  selectedOptions.value = {}
  selectedVariant.value = null
  emit('close')
}

const selectOption = (optionName, value) => {
  selectedOptions.value[optionName] = value
  updateSelectedVariant()
}

const updateSelectedVariant = () => {
  if (!allOptionsSelected.value) {
    selectedVariant.value = null
    return
  }
  
  // Buscar la variante que coincida con todas las opciones seleccionadas
  const matchingVariant = props.product.variants.find(variant => {
    if (!variant.options_summary) return false
    
    const options = typeof variant.options_summary === 'string' 
      ? JSON.parse(variant.options_summary) 
      : variant.options_summary
    
    return options.every(opt => selectedOptions.value[opt.name] === opt.value)
  })
  
  selectedVariant.value = matchingVariant || null
}

const isOptionAvailable = (optionName, value) => {
  // Si no hay otras opciones seleccionadas, verificar si existe alguna variante con esta opción
  const otherSelectedOptions = Object.entries(selectedOptions.value)
    .filter(([key]) => key !== optionName)
  
  if (otherSelectedOptions.length === 0) {
    // Verificar si existe al menos una variante con esta opción que tenga stock
    return props.product.variants.some(variant => {
      if (!variant.options_summary) return false
      const options = typeof variant.options_summary === 'string' 
        ? JSON.parse(variant.options_summary) 
        : variant.options_summary
      
      const hasOption = options.some(opt => opt.name === optionName && opt.value === value)
      return hasOption && variant.stock > 0
    })
  }
  
  // Si hay otras opciones seleccionadas, verificar compatibilidad
  return props.product.variants.some(variant => {
    if (!variant.options_summary || variant.stock <= 0) return false
    
    const options = typeof variant.options_summary === 'string' 
      ? JSON.parse(variant.options_summary) 
      : variant.options_summary
    
    const hasThisOption = options.some(opt => opt.name === optionName && opt.value === value)
    const matchesOtherOptions = otherSelectedOptions.every(([key, val]) => 
      options.some(opt => opt.name === key && opt.value === val)
    )
    
    return hasThisOption && matchesOtherOptions
  })
}

const confirmSelection = () => {
  if (!allOptionsSelected.value || !selectedVariant.value) return
  
  emit('confirm', {
    variant: selectedVariant.value,
    selectedOptions: { ...selectedOptions.value }
  })
  
  closeModal()
}

const getVariantSummary = () => {
  if (!selectedVariant.value?.options_summary) return ''
  
  const options = typeof selectedVariant.value.options_summary === 'string' 
    ? JSON.parse(selectedVariant.value.options_summary) 
    : selectedVariant.value.options_summary
  
  return options.map(opt => `${opt.name}: ${opt.value}`).join(' / ')
}

const formatCurrency = (value) => {
  return new Intl.NumberFormat('es-CO', {
    minimumFractionDigits: 0,
    maximumFractionDigits: 0
  }).format(value || 0)
}

// Función helper: Detecta si es HEX o nombre de color y devuelve HEX
const getColorDisplay = (value) => {
  // Si ya es un código HEX, devolverlo directamente
  if (value.startsWith('#')) {
    return value
  }
  
  // Si es un nombre de color, convertirlo usando el diccionario
  return getColorHex(value) || value
}

const getColorHex = (colorName) => {
  const colorMap = {
    // ROJOS
    'rojo': '#EF4444', 'red': '#EF4444',
    'rojo oscuro': '#B91C1C', 'rojo claro': '#FCA5A5', 'rojo brillante': '#DC2626',
    'carmesí': '#DC143C', 'carmesi': '#DC143C', 'crimson': '#DC143C',
    'escarlata': '#FF2400', 'granate': '#800000', 'burdeos': '#7C0A02',
    'vino': '#722F37', 'cereza': '#DE3163',
    
    // AZULES
    'azul': '#3B82F6', 'blue': '#3B82F6',
    'azul oscuro': '#1E3A8A', 'azul claro': '#93C5FD', 'azul cielo': '#87CEEB',
    'azul marino': '#000080', 'marino': '#000080', 'navy': '#000080',
    'azul rey': '#4169E1', 'azul eléctrico': '#7DF9FF', 'azul turquesa': '#40E0D0',
    'turquesa': '#40E0D0', 'cian': '#00FFFF', 'celeste': '#B0E0E6',
    'azul pastel': '#AEC6CF', 'añil': '#4B0082', 'anil': '#4B0082',
    
    // VERDES
    'verde': '#10B981', 'green': '#10B981',
    'verde oscuro': '#065F46', 'verde claro': '#86EFAC', 'verde limón': '#32CD32',
    'verde marino': '#2E8B57', 'verde oliva': '#808000', 'oliva': '#808000',
    'verde menta': '#98FF98', 'menta': '#98FF98', 'verde agua': '#7FFFD4',
    'esmeralda': '#50C878', 'jade': '#00A86B', 'lima': '#00FF00',
    'verde militar': '#4B5320', 'verde bosque': '#228B22', 'verde pino': '#01796F',
    
    // AMARILLOS
    'amarillo': '#F59E0B', 'yellow': '#F59E0B',
    'amarillo claro': '#FDE68A', 'amarillo oscuro': '#D97706', 'amarillo brillante': '#FFFF00',
    'oro': '#FFD700', 'dorado': '#FFD700', 'gold': '#FFD700',
    'mostaza': '#FFDB58', 'canario': '#FFFF99',
    
    // NARANJAS
    'naranja': '#F97316', 'orange': '#F97316',
    'naranja claro': '#FDBA74', 'naranja oscuro': '#C2410C', 'naranja brillante': '#FF8C00',
    'coral': '#FF7F50', 'durazno': '#FFDAB9', 'melocotón': '#FFE5B4', 'melocoton': '#FFE5B4',
    'salmón': '#FA8072', 'salmon': '#FA8072',
    
    // ROSAS
    'rosa': '#EC4899', 'pink': '#EC4899',
    'rosa claro': '#FBB6CE', 'rosa oscuro': '#BE185D', 'rosa pastel': '#FFD1DC',
    'rosa fuerte': '#FF1493', 'rosa chicle': '#FF69B4', 'magenta': '#FF00FF',
    'fucsia': '#FF00FF', 'fuchsia': '#FF00FF',
    
    // MORADOS/PÚRPURAS
    'morado': '#8B5CF6', 'purple': '#8B5CF6',
    'morado claro': '#C4B5FD', 'morado oscuro': '#6B21A8', 'púrpura': '#A020F0', 'purpura': '#A020F0',
    'violeta': '#8F00FF', 'lila': '#C8A2C8', 'lavanda': '#E6E6FA',
    'ciruela': '#8E4585', 'berenjena': '#614051',
    
    // MARRONES/CAFÉS
    'café': '#92400E', 'brown': '#92400E', 'marrón': '#A52A2A', 'marron': '#A52A2A',
    'café claro': '#D2691E', 'café oscuro': '#654321', 'chocolate': '#D2691E',
    'caramelo': '#C68E17', 'canela': '#D2691E', 'terracota': '#E2725B',
    'cobre': '#B87333', 'bronce': '#CD7F32',
    
    // BEIGES/CREMAS
    'beige': '#D4A373', 'crema': '#FFFDD0', 'marfil': '#FFFFF0',
    'arena': '#C2B280', 'champán': '#F7E7CE', 'champan': '#F7E7CE',
    'vainilla': '#F3E5AB', 'hueso': '#E3DAC9',
    
    // GRISES
    'gris': '#6B7280', 'gray': '#6B7280', 'grey': '#6B7280',
    'gris claro': '#D1D5DB', 'gris oscuro': '#374151', 'gris medio': '#9CA3AF',
    'plata': '#C0C0C0', 'plateado': '#C0C0C0', 'silver': '#C0C0C0',
    'ceniza': '#B2BEB5', 'pizarra': '#708090', 'carbón': '#36454F', 'carbon': '#36454F',
    
    // NEGROS
    'negro': '#1F2937', 'black': '#1F2937',
    'negro claro': '#374151', 'negro mate': '#28282B', 'negro brillante': '#000000',
    'ébano': '#555D50', 'ebano': '#555D50', 'ónix': '#0F0F0F', 'onix': '#0F0F0F',
    
    // BLANCOS
    'blanco': '#F3F4F6', 'white': '#F3F4F6',
    'blanco roto': '#FAF9F6', 'blanco hueso': '#F5F5DC', 'blanco perla': '#FDEEF4',
    'blanco nieve': '#FFFAFA', 'nieve': '#FFFAFA',
    
    // ESPECIALES
    'transparente': 'transparent', 'neutro': '#E5E7EB',
    'multicolor': 'linear-gradient(90deg, #FF0000, #FF7F00, #FFFF00, #00FF00, #0000FF, #4B0082, #9400D3)'
  }
  
  return colorMap[colorName.toLowerCase()] || null
}

// Watch: Limpiar selección cuando cambia el producto
watch(() => props.product, (newProduct) => {
  selectedOptions.value = {}
  selectedVariant.value = null
})
</script>

<style scoped>
/* Animaciones */
.modal-fade-enter-active,
.modal-fade-leave-active {
  transition: opacity 0.3s ease;
}

.modal-fade-enter-from,
.modal-fade-leave-to {
  opacity: 0;
}

.animate-scale-in {
  animation: scaleIn 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
}

@keyframes scaleIn {
  from {
    transform: scale(0.9);
    opacity: 0;
  }
  to {
    transform: scale(1);
    opacity: 1;
  }
}

/* Touch optimizations */
.touch-manipulation {
  touch-action: manipulation;
  -webkit-tap-highlight-color: transparent;
}

/* Mejora de scroll en móvil */
.overflow-y-auto {
  -webkit-overflow-scrolling: touch;
}
</style>
