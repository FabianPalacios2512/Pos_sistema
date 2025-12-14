<template>
  <div class="invoice-classic bg-white shadow-lg rounded-lg overflow-hidden font-sans text-sm">
    <!-- Header -->
    <div class="text-center p-6 border-b-2 border-gray-200">
      <div v-if="data.logo" class="mb-3 flex justify-center h-16">
        <img :src="data.logo" class="h-full object-contain">
      </div>
      <h1 class="text-2xl font-bold tracking-wide mb-1 text-gray-900" 
          :class="{'text-gray-300': !data.storeName}">
        {{ data.storeName || 'MI EMPRESA' }}
      </h1>
      <p class="text-xs text-gray-500" :class="{'text-gray-300': !data.nit}">
        NIT: {{ data.nit || 'N/A' }}
      </p>
      <p class="text-xs text-gray-500" :class="{'text-gray-300': !data.address}">
        {{ data.address || 'Calle 123 #45-67, Bogotá' }}
      </p>
      <p class="text-xs text-gray-500" :class="{'text-gray-300': !data.phone}">
        {{ data.phone || '+57 300 123 4567' }}
      </p>
    </div>

    <!-- Invoice Info -->
    <div class="py-4 px-6 bg-gray-50">
      <div class="flex justify-between text-sm">
        <div>
          <span class="font-semibold text-gray-900">Factura:</span>
          <span class="ml-2 text-gray-600">{{ invoiceNumber }}</span>
        </div>
        <div>
          <span class="font-semibold text-gray-900">Fecha:</span>
          <span class="ml-2 text-gray-600">{{ currentDate }}</span>
        </div>
      </div>
    </div>

    <!-- Items Table -->
    <div class="px-6 py-4">
      <table class="w-full">
        <thead class="border-b border-gray-300">
          <tr class="text-xs font-bold text-gray-900">
            <th class="text-left pb-2 px-2">Producto</th>
            <th class="text-center pb-2 px-2">Cant</th>
            <th class="text-right pb-2 px-2">Precio</th>
            <th class="text-right pb-2 px-2">Total</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="(item, idx) in items" :key="idx" class="border-b border-gray-100">
            <td class="py-3 px-2 text-sm text-gray-900">{{ item.name }}</td>
            <td class="py-3 px-2 text-center text-sm text-gray-600">{{ item.quantity }}</td>
            <td class="py-3 px-2 text-right text-sm text-gray-600">{{ formatCurrency(item.price) }}</td>
            <td class="py-3 px-2 text-right text-sm font-semibold text-gray-900">{{ formatCurrency(item.total) }}</td>
          </tr>
        </tbody>
      </table>

      <!-- Total -->
      <div class="mt-6 border-t-2 pt-4 px-2" style="border-color: #E5E7EB;">
        <div class="flex justify-end">
          <div class="text-right">
            <div class="text-sm mb-1 text-gray-600">
              Subtotal: <span class="font-semibold">{{ formatCurrency(subtotal) }}</span>
            </div>
            <div class="text-xl font-bold text-gray-900">
              TOTAL: {{ formatCurrency(total) }}
            </div>
          </div>
        </div>
      </div>

      <!-- Thank You Message -->
      <div class="text-center mt-6">
        <p class="text-gray-600 italic text-sm" :class="{'text-gray-300': !data.thankYouMessage}">
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
      { name: 'Producto Ejemplo A', quantity: 1, total: 25000 },
      { name: 'Producto Ejemplo B', quantity: 2, total: 50000 }
    ]
  }
})

const invoiceNumber = 'PRE-001'
const clientName = 'Cliente General'
const clientId = 'CC: 222222222'

const currentDate = computed(() => new Date().toLocaleDateString('es-CO'))
const currentTime = computed(() => new Date().toLocaleTimeString('es-CO', { hour: '2-digit', minute: '2-digit' }))

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
.invoice-classic {
  font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
}
</style>
