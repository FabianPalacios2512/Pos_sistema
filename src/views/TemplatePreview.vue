<template>
  <div>
    <CatalogTemplateSelector
      :template="templateId"
      :storeConfig="mockStoreConfig"
      :categories="mockCategories"
    />
  </div>
</template>

<script setup>
import { computed, provide } from 'vue'
import { useRoute } from 'vue-router'
import CatalogTemplateSelector from '../components/catalog/CatalogTemplateSelector.vue'

provide('isPreviewMode', true)

const route = useRoute()
const templateId = computed(() => route.params.templateId || 'urban-street')

// ─── MOCK: 10 productos urbanos con estructura real del POS ───
const mockProducts = [
  { id: 1, name: 'Hoodie Oversize Essential', price: 189000, stock: 12, category: 'Hoodies', category_id: 1, image_url: 'https://images.unsplash.com/photo-1556821840-3a63f95609a7?w=600&q=80', images: ['https://images.unsplash.com/photo-1556821840-3a63f95609a7?w=600&q=80'], is_new: true, description: 'Hoodie oversize de algodón premium con corte urbano relajado.', measurement_unit: 'unit', variants: [] },
  { id: 2, name: 'Cargo Pants Tactical', price: 165000, stock: 8, category: 'Pantalones', category_id: 2, image_url: 'https://images.unsplash.com/photo-1624378439575-d8705ad7ae80?w=600&q=80', images: ['https://images.unsplash.com/photo-1624378439575-d8705ad7ae80?w=600&q=80'], is_new: false, description: 'Pantalón cargo con múltiples bolsillos y tela resistente.', measurement_unit: 'unit', variants: [] },
  { id: 3, name: 'Tee Graphic "No Signal"', price: 79000, stock: 25, category: 'Camisetas', category_id: 3, image_url: 'https://images.unsplash.com/photo-1576566588028-4147f3842f27?w=600&q=80', images: ['https://images.unsplash.com/photo-1576566588028-4147f3842f27?w=600&q=80'], is_new: true, description: 'Camiseta de algodón 100% con estampado gráfico urbano.', measurement_unit: 'unit', variants: [] },
  { id: 4, name: 'Bomber Jacket Black', price: 285000, stock: 4, category: 'Chaquetas', category_id: 4, image_url: 'https://images.unsplash.com/photo-1591047139829-d91aecb6caea?w=600&q=80', images: ['https://images.unsplash.com/photo-1591047139829-d91aecb6caea?w=600&q=80'], is_new: false, description: 'Bomber jacket en nylon con forro interior satinado.', measurement_unit: 'unit', variants: [] },
  { id: 5, name: 'Jogger Slim Fit', price: 129000, stock: 15, category: 'Pantalones', category_id: 2, image_url: 'https://images.unsplash.com/photo-1552902865-b72c031ac5ea?w=600&q=80', images: ['https://images.unsplash.com/photo-1552902865-b72c031ac5ea?w=600&q=80'], is_new: false, description: 'Jogger de corte slim en french terry de algodón.', measurement_unit: 'unit', variants: [] },
  { id: 6, name: 'Bucket Hat Washed', price: 55000, stock: 20, category: 'Accesorios', category_id: 5, image_url: 'https://images.unsplash.com/photo-1588850561407-ed78c334e67a?w=600&q=80', images: ['https://images.unsplash.com/photo-1588850561407-ed78c334e67a?w=600&q=80'], is_new: true, description: 'Bucket hat de algodón lavado con fit relajado.', measurement_unit: 'unit', variants: [] },
  { id: 7, name: 'Crossbody Bag Urban', price: 95000, stock: 10, category: 'Accesorios', category_id: 5, image_url: 'https://images.unsplash.com/photo-1553062407-98eeb64c6a62?w=600&q=80', images: ['https://images.unsplash.com/photo-1553062407-98eeb64c6a62?w=600&q=80'], is_new: false, description: 'Bolso cruzado en nylon con cierre frontal y correa ajustable.', measurement_unit: 'unit', variants: [] },
  { id: 8, name: 'Crewneck Heavyweight', price: 145000, stock: 7, category: 'Hoodies', category_id: 1, image_url: 'https://images.unsplash.com/photo-1618354691373-d851c5c3a990?w=600&q=80', images: ['https://images.unsplash.com/photo-1618354691373-d851c5c3a990?w=600&q=80'], is_new: false, description: 'Crewneck de algodón heavyweight 400gsm.', measurement_unit: 'unit', variants: [] },
  { id: 9, name: 'Track Pants Retro', price: 139000, stock: 0, category: 'Pantalones', category_id: 2, image_url: 'https://images.unsplash.com/photo-1515886657613-9f3515b0c78f?w=600&q=80', images: ['https://images.unsplash.com/photo-1515886657613-9f3515b0c78f?w=600&q=80'], is_new: false, description: 'Pantalón track retro con franjas laterales.', measurement_unit: 'unit', variants: [] },
  { id: 10, name: 'Windbreaker Reflective', price: 225000, stock: 3, category: 'Chaquetas', category_id: 4, image_url: 'https://images.unsplash.com/photo-1544966503-7cc5ac882d5f?w=600&q=80', images: ['https://images.unsplash.com/photo-1544966503-7cc5ac882d5f?w=600&q=80'], is_new: true, description: 'Rompevientos con detalles reflectivos y capucha oculta.', measurement_unit: 'unit', variants: [] },
]

const mockCategories = [
  { id: 1, name: 'Hoodies' },
  { id: 2, name: 'Pantalones' },
  { id: 3, name: 'Camisetas' },
  { id: 4, name: 'Chaquetas' },
  { id: 5, name: 'Accesorios' },
]

const mockStoreConfig = computed(() => ({
  primary_color: '#0a0a0a',
  logo_url: '',
  banner_url: 'https://images.unsplash.com/photo-1523398002811-999ca8dec234?w=1200&q=80',
  whatsapp_number: '',
  currency_symbol: '$',
  delivery_cost: 0,
  min_order_value: 0,
  custom_message: '',
  store_name: 'URBAN DEMO STORE',
  catalog_products: mockProducts,
  ai_color_palette: null,
  ai_fonts: null,
  ai_banner_texts: {
    headline: 'DROP 01',
    subheadline: 'La nueva temporada ya está aquí. Piezas esenciales para tu día a día urbano.',
    cta_text: 'COMPRAR AHORA'
  },
  ai_about_us: 'Somos una marca urbana que fusiona el streetwear contemporáneo con la funcionalidad del día a día. Cada pieza está diseñada para expresar tu identidad sin comprometer la comodidad.',
  ai_value_messages: ['Envío gratis +$150.000', 'Calidad garantizada', 'Devoluciones en 30 días'],
  ai_announcements: ['ENVÍO GRATIS EN COMPRAS + $150.000', 'NUEVA COLECCIÓN DISPONIBLE', 'HASTA 3 CUOTAS SIN INTERÉS'],
  ai_cross_sell_messages: null,
  ai_layout_config: null
}))
</script>
