<template>
  <!-- 
    ✅ COMPONENTE QUE USA LOS TEMPLATES REALES DEL SISTEMA
    Este componente simplemente renderiza los templates reales que ya existen
    en src/components/invoiceTemplates/ - NO creamos código nuevo
  -->
  <div class="mini-invoice-wrapper">
    <!-- Classic Template REAL -->
    <ClassicTemplate 
      v-if="template === 'classic'" 
      :data="data" 
      :items="items"
    />

    <!-- Modern Template REAL -->
    <ModernTemplate 
      v-if="template === 'modern'" 
      :data="data" 
      :items="items"
    />

    <!-- Minimal Template REAL -->
    <MinimalTemplate 
      v-if="template === 'minimal'" 
      :data="data" 
      :items="items"
    />
  </div>
</template>

<script setup>
// Importamos los componentes REALES que se usan en el POS
import ClassicTemplate from '@/components/invoiceTemplates/ClassicTemplate.vue'
import ModernTemplate from '@/components/invoiceTemplates/ModernTemplate.vue'
import MinimalTemplate from '@/components/invoiceTemplates/MinimalTemplate.vue'

const props = defineProps({
  template: {
    type: String,
    required: true,
    validator: (value) => ['classic', 'modern', 'minimal'].includes(value)
  },
  data: {
    type: Object,
    required: true
  },
  items: {
    type: Array,
    default: () => [
      { name: 'Producto A', quantity: 2, price: 25000, total: 50000 },
      { name: 'Producto B', quantity: 1, price: 100000, total: 100000 }
    ]
  }
})
</script>

<style scoped>
.mini-invoice-wrapper {
  font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
  min-height: 500px;
}
</style>
