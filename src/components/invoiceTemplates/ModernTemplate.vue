<template>
  <div class="invoice-modern bg-white w-full mx-auto font-sans text-sm shadow-lg rounded-lg overflow-hidden">
    <!-- Header con Gradiente Moderno (Indigo a Purple) -->
    <div class="text-center py-6 px-6 text-white relative overflow-hidden" 
         style="background: linear-gradient(135deg, #4F46E5 0%, #7C3AED 100%);">
      <!-- Glassmorphism overlay -->
      <div class="absolute inset-0 bg-white/5"></div>
      <div class="relative z-10">
        <div v-if="data.logo" class="mb-3 flex justify-center h-12">
          <img :src="data.logo" class="h-full object-contain brightness-0 invert">
        </div>
        <h1 class="text-2xl font-bold tracking-wide mb-1">{{ data.storeName || 'MI EMPRESA' }}</h1>
        <p class="text-xs opacity-90">NIT: {{ data.nit || 'N/A' }}</p>
        <p class="text-xs opacity-90">{{ data.address || 'Calle 123 #45-67, Bogotá' }}</p>
        <p class="text-xs opacity-90">{{ data.phone || '+57 300 123 4567' }}</p>
      </div>
    </div>

    <!-- Content -->
    <div class="px-6 py-4">
      <!-- Invoice Info Badge -->
      <div class="inline-block px-4 py-2 rounded-xl border-2" 
           style="background: linear-gradient(135deg, #EEF2FF 0%, #F5F3FF 100%); border-color: #4F46E5;">
        <div class="flex items-center space-x-4 text-sm">
          <div>
            <span class="font-medium text-[#4F46E5]">Factura:</span>
            <span class="ml-2 font-bold text-[#4F46E5]">{{ invoiceNumber }}</span>
          </div>
          <div>
            <span class="font-medium text-[#4F46E5]">Fecha:</span>
            <span class="ml-2 font-bold text-[#4F46E5]">{{ currentDate }}</span>
          </div>
        </div>
      </div>

      
      <!-- Items Table -->
      <table class="w-full mt-4">
        <thead class="border-b-2" style="background: #EEF2FF; color: #4338CA; border-color: #C7D2FE;">
          <tr class="text-xs font-bold">
            <th class="text-left py-2 px-3">Producto</th>
            <th class="text-center py-2 px-3">Cant</th>
            <th class="text-right py-2 px-3">Precio</th>
            <th class="text-right py-2 px-3">Total</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="(item, idx) in items" :key="idx" class="border-b border-gray-100">
            <td class="py-3 px-3 text-sm text-gray-900">{{ item.name }}</td>
            <td class="py-3 px-3 text-center text-sm text-gray-600">{{ item.quantity }}</td>
            <td class="py-3 px-3 text-right text-sm text-gray-600">{{ formatCurrency(item.price) }}</td>
            <td class="py-3 px-3 text-right text-sm font-semibold text-gray-900">{{ formatCurrency(item.total) }}</td>
          </tr>
        </tbody>
      </table>

      <!-- Total Section -->
      <div class="mt-6 px-3 py-3 rounded-lg" 
           style="background: linear-gradient(135deg, #EEF2FF 0%, #F5F3FF 100%); border: 1px solid #C7D2FE;">
        <div class="flex justify-end">
          <div class="text-right">
            <div class="text-sm mb-1 text-gray-600">
              Subtotal: <span class="font-semibold">{{ formatCurrency(subtotal) }}</span>
            </div>
            <div class="text-xl font-bold text-[#4F46E5]">
              TOTAL: {{ formatCurrency(total) }}</div>
          </div>
        </div>
      </div>

      <!-- Thank You Message -->
      <div class="text-center mt-6 mb-2">
        <p class="font-medium text-sm" :class="{'text-gray-300': !data.thankYouMessage}">
          {{ data.thankYouMessage || '¡Gracias por su compra!' }}
        </p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  data: {
    type: Object,
    required: true
  },
  items: {
    type: Array,
    default: () => [
      { name: 'Producto Ejemplo A', quantity: 1, price: 25000, total: 25000 },
      { name: 'Producto Ejemplo B', quantity: 2, price: 25000, total: 50000 },
      { name: 'Servicio Especial', quantity: 1, price: 100000, total: 100000 }
    ]
  }
})

const invoiceNumber = 'PRE-001'
const clientName = 'Cliente General'

const currentDate = computed(() => new Date().toLocaleDateString('es-CO', {
  day: '2-digit',
  month: 'long',
  year: 'numeric'
}))

const currentTime = computed(() => new Date().toLocaleTimeString('es-CO', { 
  hour: '2-digit', 
  minute: '2-digit',
  hour12: true 
}))

const subtotal = computed(() => props.items.reduce((sum, item) => sum + item.total, 0))
const tax = computed(() => Math.round(subtotal.value * 0.19))
const total = computed(() => subtotal.value + tax.value)

const formatCurrency = (value) => {
  return new Intl.NumberFormat('es-CO', {
    style: 'currency',
    currency: 'COP',
    minimumFractionDigits: 0
  }).format(value)
}
</script>

<style scoped>
.invoice-modern {
  font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
}
</style>
