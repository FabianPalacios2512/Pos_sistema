<template>
  <!-- Loading: show nothing until config is loaded to prevent template flash -->
  <div v-if="!configLoaded" class="min-h-screen bg-white flex items-center justify-center">
    <div class="w-6 h-6 border-2 border-gray-200 border-t-gray-800 rounded-full animate-spin"></div>
  </div>

  <!-- Dynamic Template Selector -->
  <CatalogTemplateSelector 
    v-else
    :template="catalogConfig.template"
    :storeConfig="storeConfigForTemplate"
    :categories="visibleCategories"
  />
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import CatalogTemplateSelector from '../components/catalog/CatalogTemplateSelector.vue'
import apiClient from '../services/apiClient.js'

// Clave de caché en localStorage para carga instantánea
const CATALOG_CACHE_KEY = 'pos_catalog_config_cache'

// Estado
const configLoaded = ref(false)
const catalogConfig = ref({
  template: 'speed-market', // Plantilla por defecto segura para todas las tiendas
  primary_color: '#10B981',
  logo_url: '',
  banner_url: '',
  whatsapp_number: '',
  currency_symbol: '$',
  delivery_cost: 0,
  min_order_value: 0,
  custom_message: 'Hola, quiero hacer el siguiente pedido:',
  store_name: 'Mi Tienda'
})
const products = ref([])
const visibleCategories = ref([]) // Categorías visibles configuradas

// Computed: Preparar configuración para las plantillas
const storeConfigForTemplate = computed(() => ({
  primary_color: catalogConfig.value.primary_color,
  logo_url: catalogConfig.value.logo_url,
  banner_url: catalogConfig.value.banner_url,
  whatsapp_number: catalogConfig.value.whatsapp_number,
  currency_symbol: catalogConfig.value.currency_symbol,
  delivery_cost: catalogConfig.value.delivery_cost,
  min_order_value: catalogConfig.value.min_order_value,
  custom_message: catalogConfig.value.custom_message,
  store_name: catalogConfig.value.store_name,
  catalog_products: products.value,
  // AI-Generated Brand Identity
  ai_color_palette: catalogConfig.value.ai_color_palette,
  ai_fonts: catalogConfig.value.ai_fonts,
  ai_banner_texts: catalogConfig.value.ai_banner_texts,
  ai_about_us: catalogConfig.value.ai_about_us,
  ai_value_messages: catalogConfig.value.ai_value_messages,
  ai_announcements: catalogConfig.value.ai_announcements,
  ai_cross_sell_messages: catalogConfig.value.ai_cross_sell_messages,
  ai_layout_config: catalogConfig.value.ai_layout_config
}))

// Leer configuración guardada en caché (síncrono, sin esperar API)
const tryLoadFromCache = () => {
  try {
    const raw = localStorage.getItem(CATALOG_CACHE_KEY)
    if (!raw) return false
    const cached = JSON.parse(raw)
    if (cached && cached.template) {
      catalogConfig.value = cached
      configLoaded.value = true
      return true
    }
  } catch (e) {}
  return false
}

// Cargar configuración del catálogo
const loadCatalogConfig = async () => {
  try {
    const response = await apiClient.get('/public/catalog/config')
    
    if (response.data.success && response.data.data) {
      const data = response.data.data
      const newConfig = {
        template: data.template || 'speed-market', // Plantilla por defecto segura
        primary_color: data.primary_color || '#10B981',
        logo_url: data.logo_url || '',
        banner_url: data.banner_url || '',
        whatsapp_number: data.whatsapp_number || '',
        currency_symbol: '$',
        delivery_cost: parseFloat(data.delivery_cost || 0),
        min_order_value: parseFloat(data.minimum_order || 0),
        custom_message: data.custom_message || 'Hola, quiero hacer el siguiente pedido:',
        store_name: data.store_name || 'Mi Tienda',
        // AI-Generated Brand Identity
        ai_color_palette: data.ai_color_palette || null,
        ai_fonts: data.ai_fonts || null,
        ai_banner_texts: data.ai_banner_texts || null,
        ai_about_us: data.ai_about_us || null,
        ai_value_messages: data.ai_value_messages || null,
        ai_announcements: data.ai_announcements || null,
        ai_cross_sell_messages: data.ai_cross_sell_messages || null,
        ai_layout_config: data.ai_layout_config || null
      }
      catalogConfig.value = newConfig
      // Guardar en caché para carga instantánea en la próxima visita
      try { localStorage.setItem(CATALOG_CACHE_KEY, JSON.stringify(newConfig)) } catch (e) {}
    }
  } catch (error) {
    console.error('Error loading catalog config:', error)
    // Usar valores por defecto
  } finally {
    configLoaded.value = true
  }
}
// Helper para corregir URLs de imágenes
const getImageUrl = (path) => {
  if (!path) return ''
  // No modificar base64 ni URLs absolutas
  if (path.startsWith('http') || path.startsWith('data:')) return path
  // Asumir que los nombres de archivo planos están en storage/products/
  if (!path.includes('/')) return `/storage/products/${path}`
  // Asegurar que tenga slash inicial si es relativa
  return path.startsWith('/') ? path : `/${path}`
}

// Cargar productos del catálogo
const loadProducts = async () => {
  try {
    const response = await apiClient.get('/public/catalog')
    
    if (response.data.success) {
      products.value = response.data.products.map(product => ({
        id: product.id,
        name: product.name,
        description: product.description,
        price: product.price,
        image_url: getImageUrl(product.image || product.image_url),
        images: (product.images || []).map(getImageUrl),
        stock: product.stock || 0,
        category: product.category || 'Sin categoría',
        category_id: product.category_id,
        unit: product.unit || 'unidad', // 🆕 Campo para peso/medida
        measurement_unit: product.measurement_unit, // 🆕 Campo especial de medida (kg, g, etc.)
        allow_decimal: product.allow_decimal || false, // 🆕 Si permite decimales
        type: product.type || 'simple',
        options: product.options || [],
        variants: product.variants || []
      }))
    }
  } catch (error) {
    console.error('Error loading products:', error)
    products.value = []
  }
}

// Cargar categorías visibles desde el backend
const loadVisibleCategories = async () => {
  try {
    const response = await apiClient.get('/public/catalog/categories')
    
    if (response.data.success) {
      visibleCategories.value = response.data.categories || []
    }
  } catch (error) {
    console.error('Error loading categories:', error)
    visibleCategories.value = []
  }
}

// Lifecycle
onMounted(async () => {
  const hasCachedConfig = tryLoadFromCache()

  if (hasCachedConfig) {
    // Config ya visible desde caché: cargar todo en paralelo sin bloquear UI
    await Promise.all([
      loadCatalogConfig(), // Refresca caché en background
      loadProducts(),
      loadVisibleCategories()
    ])
  } else {
    // Primera visita: esperar config para evitar flash de plantilla incorrecta
    await loadCatalogConfig()
    await Promise.all([loadProducts(), loadVisibleCategories()])
  }
})
</script>
