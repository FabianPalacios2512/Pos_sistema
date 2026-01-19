<template>
  <!-- Loading State durante inicialización -->
  <div v-if="isInitializing" class="min-h-screen font-sans bg-gradient-to-br from-gray-50 via-white to-slate-100 dark:bg-gradient-to-b dark:from-[#141417] dark:via-slate-900 dark:to-[#0a0a0c] transition-colors duration-300 px-8">
    <div class="flex items-center justify-center min-h-screen">
      <div class="flex flex-col items-center space-y-4">
        <div class="w-16 h-16 border-4 border-slate-200 dark:border-zinc-700 border-t-slate-900 dark:border-t-slate-500 rounded-full animate-spin"></div>
        <p class="text-sm text-gray-600 dark:text-zinc-400 font-medium">Cargando colección...</p>
      </div>
    </div>
  </div>
  
  <!-- Contenido principal -->
  <div v-else class="min-h-screen font-sans bg-gradient-to-br from-gray-50 via-white to-slate-100 dark:bg-gradient-to-b dark:from-[#141417] dark:via-slate-900 dark:to-[#0a0a0c] transition-colors duration-300 px-8">
    
    <!-- 🔥 INDICADOR VISUAL - ESTE ARCHIVO SE ESTÁ CARGANDO -->
    <div class="bg-black text-white p-8 text-center text-2xl font-bold mb-4">
      ✅ FASHION PRODUCT LIST CARGADO CORRECTAMENTE
    </div>
    
    <div class="p-4 lg:p-6 space-y-8 pb-8 animate-fade-in">
      
      <!-- Header Minimalista (Sin icono, Sin borde) -->
      <div class="flex items-center justify-between pb-6">
        <!-- Título con Tipografía Elegante -->
        <div>
          <h1 class="text-3xl font-light text-gray-900 dark:text-white tracking-tight">Colección</h1>
          <p class="text-sm text-gray-500 dark:text-zinc-400 mt-1 font-light">Explora nuestros productos</p>
        </div>
        
        <div class="flex items-center space-x-3">
          <!-- Botón Secundario (Actualizar) -->
          <button @click="refreshProducts"
                  :disabled="loading"
                  class="px-4 py-2.5 bg-white dark:bg-zinc-900 hover:bg-slate-50 dark:hover:bg-zinc-800 text-slate-600 dark:text-zinc-200 text-sm font-medium rounded-xl border border-slate-200 dark:border-zinc-800 shadow-sm transition-all duration-200"
                  :class="{ 'opacity-50 cursor-not-allowed': loading }">
            <svg class="w-4 h-4" :class="{ 'animate-spin': loading }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
            </svg>
          </button>
          
          <!-- Botón Principal (Nuevo Producto) -->
          <button @click="openCreateModal"
                  class="px-6 py-2.5 bg-slate-900 dark:bg-slate-700 hover:bg-black dark:hover:bg-slate-600 text-white text-sm font-medium rounded-xl shadow-lg shadow-slate-400/40 dark:shadow-slate-900/50 transition-all duration-300 transform active:scale-95 flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path>
            </svg>
            <span>Nuevo Producto</span>
          </button>
        </div>
      </div>

      <!-- KPIs Minimalistas Monocromáticos (Sin iconos gigantes) -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <!-- Total Productos -->
        <div class="bg-white/70 dark:bg-zinc-900/40  rounded-xl px-5 py-4 border border-gray-200 dark:border-zinc-800 hover:border-gray-300 dark:hover:border-zinc-700 transition-all duration-200">
          <p class="text-xs font-medium text-gray-400 dark:text-zinc-500 uppercase tracking-widest mb-1">Total</p>
          <p class="text-3xl font-light text-gray-900 dark:text-white">{{ displayProducts.length }}</p>
        </div>

        <!-- Productos Activos -->
        <div class="bg-white/70 dark:bg-zinc-900/40  rounded-xl px-5 py-4 border border-gray-200 dark:border-zinc-800 hover:border-gray-300 dark:hover:border-zinc-700 transition-all duration-200">
          <p class="text-xs font-medium text-gray-400 dark:text-zinc-500 uppercase tracking-widest mb-1">Activos</p>
          <p class="text-3xl font-light text-gray-900 dark:text-white">{{ activeProducts }}</p>
        </div>

        <!-- Stock Bajo -->
        <div class="bg-white/70 dark:bg-zinc-900/40  rounded-xl px-5 py-4 border border-gray-200 dark:border-zinc-800 hover:border-gray-300 dark:hover:border-zinc-700 transition-all duration-200">
          <p class="text-xs font-medium text-gray-400 dark:text-zinc-500 uppercase tracking-widest mb-1">Stock Bajo</p>
          <p class="text-3xl font-light text-gray-900 dark:text-white">{{ lowStockProducts }}</p>
        </div>

        <!-- Valor Total -->
        <div class="bg-white/70 dark:bg-zinc-900/40  rounded-xl px-5 py-4 border border-gray-200 dark:border-zinc-800 hover:border-gray-300 dark:hover:border-zinc-700 transition-all duration-200">
          <p class="text-xs font-medium text-gray-400 dark:text-zinc-500 uppercase tracking-widest mb-1">Valor Inventario</p>
          <p class="text-3xl font-light text-gray-900 dark:text-white">${{ formatCurrency(totalValue) }}</p>
        </div>
      </div>

      <!-- Filtros Limpios -->
      <div class="bg-white/50 dark:bg-zinc-900/50  rounded-xl p-4 mb-8 transition-colors duration-300">
        <div class="flex flex-wrap items-center gap-4">
          <!-- Búsqueda -->
          <div class="flex-1 min-w-[250px] relative">
            <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400 dark:text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
            <input
              v-model="searchTerm"
              type="text"
              placeholder="Buscar por nombre o SKU..."
              class="w-full pl-10 pr-4 py-3 text-sm rounded-xl border-2 border-gray-200 dark:border-zinc-700 bg-gray-50 dark:bg-zinc-800 text-gray-900 dark:text-zinc-200 placeholder-gray-400 dark:placeholder-zinc-500 focus:outline-none focus:ring-2 focus:ring-slate-500 dark:focus:ring-slate-400 focus:border-transparent transition-all duration-300">
          </div>
          
          <!-- Categoría -->
          <div class="min-w-[160px]">
            <select
              v-model="categoryFilter"
              class="w-full px-3 py-3 text-sm rounded-xl border border-gray-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-gray-700 dark:text-zinc-300 font-medium focus:outline-none focus:ring-2 focus:ring-slate-500 dark:focus:ring-slate-400 transition-colors duration-300">
              <option value="">Todas las categorías</option>
              <option v-for="category in categories" :key="category.id" :value="category.id">{{ category.name }}</option>
            </select>
          </div>
          
          <!-- Estado -->
          <div class="min-w-[140px]">
            <select
              v-model="statusFilter"
              class="w-full px-3 py-3 text-sm rounded-xl border border-gray-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-gray-700 dark:text-zinc-300 font-medium focus:outline-none focus:ring-2 focus:ring-slate-500 dark:focus:ring-slate-400 transition-colors duration-300">
              <option value="">Todos</option>
              <option value="active">Activos</option>
              <option value="inactive">Inactivos</option>
              <option value="low-stock">Stock Bajo</option>
            </select>
          </div>
        </div>
      </div>

      <!-- Loading State -->
      <div v-if="loading" class="flex justify-center items-center py-20">
        <div class="flex flex-col items-center space-y-3">
          <div class="w-12 h-12 border-4 border-slate-200 dark:border-zinc-700 border-t-slate-900 dark:border-t-slate-500 rounded-full animate-spin"></div>
          <p class="text-sm text-gray-500 dark:text-zinc-400 font-light">Cargando colección...</p>
        </div>
      </div>

      <!-- Empty State -->
      <div v-else-if="!loading && !paginatedProducts.length" class="flex justify-center items-center py-32">
        <div class="flex flex-col items-center space-y-4 max-w-md mx-auto">
          <div class="w-20 h-20 bg-slate-100 dark:bg-zinc-800 rounded-full flex items-center justify-center">
            <svg class="w-10 h-10 text-slate-400 dark:text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
            </svg>
          </div>
          <div class="text-center">
            <p class="text-lg font-light text-gray-900 dark:text-white">No hay productos</p>
            <p class="text-sm text-gray-600 dark:text-zinc-400 mt-2 font-light">{{ searchTerm ? 'No se encontraron productos' : 'Comienza agregando tu primer producto' }}</p>
          </div>
          <button v-if="!searchTerm" 
                  @click="openCreateModal" 
                  class="mt-2 px-5 py-2.5 bg-slate-900 dark:bg-slate-700 hover:bg-black dark:hover:bg-slate-600 text-white rounded-xl text-sm font-medium shadow-lg shadow-slate-400/40 dark:shadow-slate-900/50 transition-all inline-flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
            </svg>
            <span>Nuevo Producto</span>
          </button>
        </div>
      </div>

      <!-- Grid de Productos - Fashion Style (Portrait 3:4) -->
      <div v-else class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-8">
        
        <!-- Fashion Product Card -->
        <div v-for="product in paginatedProducts" 
             :key="product.id" 
             class="group cursor-pointer">
          
          <!-- Imagen Principal (70% de la tarjeta, Aspect Ratio 3:4) -->
          <div class="relative aspect-[3/4] bg-gray-100 dark:bg-zinc-800 rounded-2xl overflow-hidden mb-3">
            <img :src="getProductImage(product)" 
                 :alt="product.name" 
                 @error="(e) => handleImageError(e, product)"
                 @click="viewProduct(product)"
                 class="w-full h-full object-cover transform group-hover:scale-105 transition-transform duration-700 ease-out">
            
            <!-- Badges Discretos -->
            <div class="absolute top-3 left-3 flex flex-col gap-2">
              <!-- Badge "Nuevo" (Si producto es nuevo - menos de 7 días) -->
              <span v-if="isNewProduct(product)" 
                    class="px-2 py-1 bg-white/90 dark:bg-black/60  text-gray-900 dark:text-white text-[10px] font-medium uppercase tracking-wider rounded-md">
                Nuevo
              </span>
              
              <!-- Punto Rojo Discreto para Stock Bajo -->
              <div v-if="(product.current_stock || 0) <= (product.min_stock || 0)" 
                   class="w-2 h-2 bg-red-500 rounded-full animate-pulse"
                   title="Stock bajo"></div>
            </div>
            
            <!-- Botón Editar (Solo visible en hover) -->
            <button @click.stop="editProduct(product)"
                    class="absolute bottom-3 right-3 w-10 h-10 bg-white/90 dark:bg-black/60  rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-300 hover:bg-white dark:hover:bg-black/80">
              <svg class="w-4 h-4 text-gray-900 dark:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
              </svg>
            </button>
          </div>

          <!-- Info del Producto (30% restante) -->
          <div class="px-1" @click="viewProduct(product)">
            <!-- Categoría -->
            <p class="text-[10px] font-medium text-gray-400 dark:text-zinc-500 uppercase tracking-widest mb-1">
              {{ product.category?.name || 'Sin categoría' }}
            </p>
            
            <!-- Nombre del Producto (Tipografía Elegante) -->
            <h3 class="text-sm font-light text-gray-900 dark:text-white leading-tight mb-2 line-clamp-2 group-hover:text-slate-600 dark:group-hover:text-slate-300 transition-colors" :title="product.name">
              {{ product.name }}
            </h3>
            
            <!-- Precio -->
            <div class="mb-3">
              <span v-if="hasVariants(product)" class="text-sm font-medium text-gray-900 dark:text-white">
                Desde ${{ formatCurrency(getMinVariantPrice(product)) }}
              </span>
              <span v-else class="text-sm font-medium text-gray-900 dark:text-white">
                ${{ formatCurrency(product.sale_price) }}
              </span>
            </div>
            
            <!-- Visualizador de Variantes -->
            <div v-if="hasVariants(product)" class="space-y-2">
              <!-- Color Swatches (Círculos pequeños) -->
              <div v-if="getColorVariants(product).length > 0" class="flex items-center gap-1.5">
                <div v-for="color in getColorVariants(product).slice(0, 5)" 
                     :key="color"
                     class="w-5 h-5 rounded-full border-2 border-gray-300 dark:border-zinc-700"
                     :style="{ backgroundColor: getColorHex(color) }"
                     :title="color"></div>
                <span v-if="getColorVariants(product).length > 5" 
                      class="text-[10px] text-gray-400 dark:text-zinc-500">
                  +{{ getColorVariants(product).length - 5 }}
                </span>
              </div>
              
              <!-- Tallas disponibles -->
              <div v-if="getSizeVariants(product).length > 0" class="flex items-center gap-1 flex-wrap">
                <span v-for="size in getSizeVariants(product)" 
                      :key="size"
                      class="text-[10px] text-gray-400 dark:text-zinc-500 font-light">
                  {{ size }}<span v-if="size !== getSizeVariants(product)[getSizeVariants(product).length - 1]"> ·</span>
                </span>
              </div>
            </div>
            
            <!-- Stock para productos sin variantes -->
            <div v-else class="text-[10px] text-gray-400 dark:text-zinc-500 font-light">
              Stock: {{ product.current_stock || 0 }}
            </div>
          </div>
        </div>
      </div>

      <!-- Paginador -->
      <div class="mb-6 mt-8">
        <TablePaginator
          v-if="filteredProducts.length > itemsPerPage"
          v-model:currentPage="currentPage"
          v-model:itemsPerPage="itemsPerPage"
          :totalPages="totalPages"
          :totalItems="totalItems"
          label="productos"
        />
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch, onMounted, onUnmounted, nextTick } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useAppStore } from '@/stores/app'
import { productsService } from '@/services/productsService'
import { categoriesService } from '@/services/categoriesService'
import TablePaginator from '@/components/TablePaginator.vue'

// Props
const props = defineProps({
  queryParams: {
    type: Object,
    default: () => ({})
  }
})

// Router
const route = useRoute()
const router = useRouter()

// Store
const appStore = useAppStore()

// Estado
const isInitializing = ref(true)
const loading = ref(false)
const products = ref([])
const categories = ref([])
const searchTerm = ref('')
const categoryFilter = ref('')
const statusFilter = ref('')
const sortBy = ref('name')
const currentPage = ref(1)
const itemsPerPage = ref(20)
const notifications = ref([])
let notificationId = 0

// Computed
const displayProducts = computed(() => products.value)

const activeProducts = computed(() => {
  return products.value.filter(p => getProductStatus(p) !== false).length
})

const lowStockProducts = computed(() => {
  return products.value.filter(p => (p.current_stock || 0) <= (p.min_stock || 0)).length
})

const totalValue = computed(() => {
  return products.value.reduce((sum, p) => {
    const stock = p.current_stock || 0
    const price = p.sale_price || p.price || 0
    return sum + (stock * price)
  }, 0)
})

const filteredProducts = computed(() => {
  let result = [...products.value]
  
  // Filtro de búsqueda
  if (searchTerm.value) {
    const search = searchTerm.value.toLowerCase()
    result = result.filter(p => 
      p.name?.toLowerCase().includes(search) ||
      p.sku?.toLowerCase().includes(search) ||
      p.barcode?.toLowerCase().includes(search)
    )
  }
  
  // Filtro de categoría
  if (categoryFilter.value) {
    result = result.filter(p => p.category_id == categoryFilter.value)
  }
  
  // Filtro de estado
  if (statusFilter.value) {
    switch(statusFilter.value) {
      case 'active':
        result = result.filter(p => getProductStatus(p) !== false)
        break
      case 'inactive':
        result = result.filter(p => getProductStatus(p) === false)
        break
      case 'low-stock':
        result = result.filter(p => (p.current_stock || 0) <= (p.min_stock || 0))
        break
    }
  }
  
  // Ordenamiento
  result.sort((a, b) => {
    switch(sortBy.value) {
      case 'name':
        return (a.name || '').localeCompare(b.name || '')
      case 'price':
        return (a.sale_price || 0) - (b.sale_price || 0)
      case 'stock':
        return (a.current_stock || 0) - (b.current_stock || 0)
      case 'created_at':
        return new Date(b.created_at) - new Date(a.created_at)
      default:
        return 0
    }
  })
  
  return result
})

const totalPages = computed(() => Math.ceil(filteredProducts.value.length / itemsPerPage.value))
const totalItems = computed(() => filteredProducts.value.length)

const paginatedProducts = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage.value
  const end = start + itemsPerPage.value
  return filteredProducts.value.slice(start, end)
})

// Métodos auxiliares
const getProductStatus = (product) => {
  if (product.is_active !== undefined) return product.is_active
  if (product.active !== undefined) return product.active
  return true
}

const getProductImage = (product) => {
  return product.image_url || product.image || 'https://via.placeholder.com/400x500?text=Sin+Imagen'
}

const handleImageError = (e, product) => {
  e.target.src = 'https://via.placeholder.com/400x500?text=Sin+Imagen'
}

const formatCurrency = (value) => {
  return new Intl.NumberFormat('es-CL').format(value || 0)
}

const isNewProduct = (product) => {
  if (!product.created_at) return false
  const createdDate = new Date(product.created_at)
  const now = new Date()
  const diffDays = (now - createdDate) / (1000 * 60 * 60 * 24)
  return diffDays <= 7
}

const hasVariants = (product) => {
  if (!product.variants || product.variants.length === 0) return false
  // Producto simple: 1 variante sin opciones
  if (product.variants.length === 1) {
    const variant = product.variants[0]
    return variant.options && variant.options.length > 0
  }
  // Múltiples variantes
  return true
}

const getMinVariantPrice = (product) => {
  if (!hasVariants(product)) return product.sale_price || 0
  return Math.min(...product.variants.map(v => v.price || v.sale_price || 0))
}

const getColorVariants = (product) => {
  if (!hasVariants(product)) return []
  const colors = new Set()
  product.variants.forEach(variant => {
    if (variant.options) {
      variant.options.forEach(opt => {
        if (opt.name.toLowerCase() === 'color' || opt.name.toLowerCase() === 'colour') {
          colors.add(opt.value)
        }
      })
    }
  })
  return Array.from(colors)
}

const getSizeVariants = (product) => {
  if (!hasVariants(product)) return []
  const sizes = new Set()
  product.variants.forEach(variant => {
    if (variant.options) {
      variant.options.forEach(opt => {
        if (opt.name.toLowerCase() === 'talla' || opt.name.toLowerCase() === 'size') {
          sizes.add(opt.value)
        }
      })
    }
  })
  return Array.from(sizes)
}

const getColorHex = (colorName) => {
  const colorMap = {
    'rojo': '#EF4444', 'red': '#EF4444',
    'azul': '#3B82F6', 'blue': '#3B82F6',
    'verde': '#10B981', 'green': '#10B981',
    'amarillo': '#F59E0B', 'yellow': '#F59E0B',
    'negro': '#1F2937', 'black': '#1F2937',
    'blanco': '#F9FAFB', 'white': '#F9FAFB',
    'gris': '#6B7280', 'gray': '#6B7280', 'grey': '#6B7280',
    'rosa': '#EC4899', 'pink': '#EC4899',
    'morado': '#8B5CF6', 'purple': '#8B5CF6',
    'naranja': '#F97316', 'orange': '#F97316'
  }
  return colorMap[colorName.toLowerCase()] || '#94A3B8'
}

// Métodos de acción
const loadProducts = async () => {
  try {
    loading.value = true
    const response = await productsService.getAll()
    if (response.success) {
      products.value = response.data || []
    }
  } catch (error) {
    console.error('Error cargando productos:', error)
    showNotification('Error', 'No se pudieron cargar los productos', 'error')
  } finally {
    loading.value = false
  }
}

const loadCategories = async () => {
  try {
    const response = await categoriesService.getAll()
    if (response.success) {
      categories.value = response.data || []
    }
  } catch (error) {
    console.error('Error cargando categorías:', error)
  }
}

const refreshProducts = async () => {
  await loadProducts()
  showNotification('Productos actualizados', 'La lista se ha actualizado correctamente', 'success')
}

const openCreateModal = () => {
  // Implementar apertura de modal
  console.log('Abrir modal de creación')
}

const viewProduct = (product) => {
  // Implementar vista de detalle
  console.log('Ver producto:', product)
}

const editProduct = (product) => {
  // Implementar edición
  console.log('Editar producto:', product)
}

const showNotification = (title, message = '', type = 'info', duration = 5000) => {
  const notification = {
    id: ++notificationId,
    title,
    message,
    type
  }
  
  notifications.value.push(notification)
  
  setTimeout(() => {
    removeNotification(notification.id)
  }, duration)
}

const removeNotification = (id) => {
  const index = notifications.value.findIndex(n => n.id === id)
  if (index > -1) {
    notifications.value.splice(index, 1)
  }
}

// Watchers
watch([searchTerm, categoryFilter, statusFilter], () => {
  currentPage.value = 1
})

// Lifecycle
onMounted(async () => {
  console.log('Fashion Product List inicializado')
  
  await loadCategories()
  await loadProducts()
  
  isInitializing.value = false
})

onUnmounted(() => {
  // Limpiar si es necesario
})
</script>

<style scoped>
/* Animación de fade-in */
@keyframes fade-in {
  from {
    opacity: 0;
    transform: translateY(10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.animate-fade-in {
  animation: fade-in 0.5s ease-out;
}

/* Transiciones suaves */
* {
  transition-property: background-color, border-color, color, fill, stroke, opacity, box-shadow, transform;
  transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
  transition-duration: 300ms;
}
</style>
