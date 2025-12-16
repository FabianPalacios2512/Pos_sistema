<template>
  <component 
    :is="currentTemplate" 
    :storeConfig="storeConfig"
    :isMobilePreview="isMobilePreview"
  />
</template>

<script setup>
import { computed } from 'vue'
import CatalogTemplateA from './CatalogTemplateA.vue'
import CatalogTemplateB from './CatalogTemplateB.vue'
import CatalogTemplateC from './CatalogTemplateC.vue'

const props = defineProps({
  template: {
    type: String,
    default: 'visual-story',
    validator: (value) => ['visual-story', 'speed-market', 'modern-grid'].includes(value)
  },
  storeConfig: {
    type: Object,
    required: true
  },
  isMobilePreview: {
    type: Boolean,
    default: false
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
    default:
      return CatalogTemplateA
  }
})
</script>
