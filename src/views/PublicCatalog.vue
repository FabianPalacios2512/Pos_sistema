<template>
  <!-- Dynamic Template Selector -->
  <CatalogTemplateSelector 
    :template="catalogConfig.template"
    :storeConfig="storeConfigForTemplate"
    :categories="visibleCategories"
  />
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import CatalogTemplateSelector from '../components/catalog/CatalogTemplateSelector.vue'
import apiClient from '../services/apiClient.js'

// Estado
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
  catalog_products: products.value
}))

// Cargar configuración del catálogo
const loadCatalogConfig = async () => {
  try {
    const response = await apiClient.get('/public/catalog/config')
    
    if (response.data.success && response.data.data) {
      const data = response.data.data
      catalogConfig.value = {
        template: data.template || 'speed-market', // Plantilla por defecto segura
        primary_color: data.primary_color || '#10B981',
        logo_url: data.logo_url || '',
        banner_url: data.banner_url || '',
        whatsapp_number: data.whatsapp_number || '',
        currency_symbol: '$',
        delivery_cost: parseFloat(data.delivery_cost || 0),
        min_order_value: parseFloat(data.minimum_order || 0),
        custom_message: data.custom_message || 'Hola, quiero hacer el siguiente pedido:',
        store_name: data.store_name || 'Mi Tienda'
      }
    }
  } catch (error) {
    console.error('Error loading catalog config:', error)
    // Usar valores por defecto
  }
}
// Helper para corregir URLs de imágenes
const getImageUrl = (path) => {
  if (!path) return ''
  // No modificar base64 ni URLs absolutas
  if (path.startsWith('http') || path.startsWith('data:')) return path
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
  await loadCatalogConfig()
  await Promise.all([loadProducts(), loadVisibleCategories()])
})
</script>
