<template>
  <!-- Loading State -->
  <div v-if="isLoading" class="min-h-screen bg-gray-50 flex items-center justify-center">
    <div class="text-center">
      <div class="animate-spin rounded-full h-16 w-16 border-b-2 border-emerald-600 mx-auto mb-4"></div>
      <p class="text-gray-600 font-medium">Cargando tienda...</p>
    </div>
  </div>

  <!-- Dynamic Template Selector -->
  <CatalogTemplateSelector 
    v-else
    :template="catalogConfig.template"
    :storeConfig="storeConfigForTemplate"
  />
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import CatalogTemplateSelector from '../components/catalog/CatalogTemplateSelector.vue'
import apiClient from '../services/apiClient.js'

// Estado
const isLoading = ref(true)
const catalogConfig = ref({
  template: 'visual-story',
  primary_color: '#10B981',
  logo_url: '',
  banner_url: '',
  whatsapp_number: '',
  currency_symbol: '$',
  delivery_cost: 0,
  min_order_value: 0,
  store_name: 'Mi Tienda'
})
const products = ref([])

// Computed: Preparar configuración para las plantillas
const storeConfigForTemplate = computed(() => ({
  primary_color: catalogConfig.value.primary_color,
  logo_url: catalogConfig.value.logo_url,
  banner_url: catalogConfig.value.banner_url,
  whatsapp_number: catalogConfig.value.whatsapp_number,
  currency_symbol: catalogConfig.value.currency_symbol,
  delivery_cost: catalogConfig.value.delivery_cost,
  min_order_value: catalogConfig.value.min_order_value,
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
        template: data.template || 'visual-story',
        primary_color: data.primary_color || '#10B981',
        logo_url: data.logo_url || '',
        banner_url: data.banner_url || '',
        whatsapp_number: data.whatsapp_number || '',
        currency_symbol: '$',
        delivery_cost: parseFloat(data.delivery_cost || 0),
        min_order_value: parseFloat(data.minimum_order || 0),
        store_name: data.store_name || 'Mi Tienda'
      }
    }
  } catch (error) {
    console.error('Error loading catalog config:', error)
    // Usar valores por defecto
  }
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
        image_url: product.image || product.image_url || '',
        stock: product.stock || 0,
        category: product.category || 'Sin categoría'
      }))
    }
  } catch (error) {
    console.error('Error loading products:', error)
    products.value = []
  }
}

// Lifecycle
onMounted(async () => {
  await loadCatalogConfig()
  await loadProducts()
  isLoading.value = false
})
</script>
