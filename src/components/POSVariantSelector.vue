<template>
  <!-- Modal Touch-Friendly para Selección de Variantes -->
  <Teleport to="body">
    <Transition name="modal-fade">
      <div v-if="show" 
           class="fixed inset-0 bg-black/70 backdrop-blur-sm flex items-center justify-center z-[9999] p-4"
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
              
              <!-- Para COLORES: Círculos Reales -->
              <div v-if="optionGroup.name.toLowerCase().includes('color')" class="flex items-center gap-3 flex-wrap">
                <button v-for="value in optionGroup.values" 
                        :key="value"
                        @click="selectOption(optionGroup.name, value)"
                        :disabled="!isOptionAvailable(optionGroup.name, value)"
                        :class="[
                          'relative transition-all duration-200 touch-manipulation group',
                          !isOptionAvailable(optionGroup.name, value) && 'opacity-40 cursor-not-allowed'
                        ]">
                  
                  <!-- Swatch de Color -->
                  <div class="relative">
                    <!-- Ring de selección -->
                    <div v-if="selectedOptions[optionGroup.name] === value"
                         class="absolute -inset-1 rounded-full border-2 border-gray-900 dark:border-white"></div>
                    
                    <!-- Círculo de color -->
                    <div :style="{ backgroundColor: getColorHex(value) || '#9CA3AF' }"
                         :class="[
                           'w-10 h-10 rounded-full transition-all',
                           getColorHex(value) === '#F3F4F6' && 'border-2 border-gray-300 dark:border-zinc-700',
                           !isOptionAvailable(optionGroup.name, value) && 'opacity-50'
                         ]">
                      <!-- X para sin stock -->
                      <div v-if="!isOptionAvailable(optionGroup.name, value)"
                           class="w-full h-full flex items-center justify-center">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                      </div>
                    </div>
                  </div>
                  
                  <!-- Nombre del color debajo -->
                  <p class="text-xs text-center text-gray-600 dark:text-zinc-400 mt-1.5 font-medium">
                    {{ value }}
                  </p>
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
                            ? 'bg-gray-900 dark:bg-white text-white dark:text-gray-900 border-2 border-gray-900 dark:border-white'
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

const getColorHex = (colorName) => {
  const colorMap = {
    'rojo': '#EF4444', 'red': '#EF4444',
    'azul': '#3B82F6', 'blue': '#3B82F6',
    'verde': '#10B981', 'green': '#10B981',
    'amarillo': '#F59E0B', 'yellow': '#F59E0B',
    'negro': '#1F2937', 'black': '#1F2937',
    'blanco': '#F3F4F6', 'white': '#F3F4F6',
    'gris': '#6B7280', 'gray': '#6B7280',
    'rosa': '#EC4899', 'pink': '#EC4899',
    'morado': '#8B5CF6', 'purple': '#8B5CF6',
    'naranja': '#F97316', 'orange': '#F97316',
    'café': '#92400E', 'brown': '#92400E',
    'beige': '#D4A373', 'beige': '#D4A373'
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
