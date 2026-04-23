<template>
  <component 
    :is="currentTemplate" 
    :storeConfig="storeConfig"
    :isMobilePreview="isMobilePreview"
    :categories="categories"
  />
</template>

<script setup>
import { computed } from 'vue'
import CatalogTemplateA from './CatalogTemplateA.vue'
import CatalogTemplateB from './CatalogTemplateB.vue'
import CatalogTemplateC from './CatalogTemplateC.vue'
import CatalogTemplateD from './CatalogTemplateD.vue'

const props = defineProps({
  template: {
    type: String,
    default: 'speed-market', // Default seguro para todas las tiendas
    validator: (value) => ['visual-story', 'speed-market', 'modern-grid', 'urban-street'].includes(value)
  },
  storeConfig: {
    type: Object,
    required: true
  },
  isMobilePreview: {
    type: Boolean,
    default: false
  },
  categories: {
    type: Array,
    default: () => []
  }
})

const currentTemplate = computed(() => {
  switch (props.template) {
    case 'visual-story':
      return CatalogTemplateA
    case 'speed-market':
      return CatalogTemplateB
    case 'modern-grid':
      return CatalogTemplateC
    case 'urban-street':
      return CatalogTemplateD
    default:
      return CatalogTemplateB // Default seguro (speed-market) para todas las tiendas
  }
})
</script>
