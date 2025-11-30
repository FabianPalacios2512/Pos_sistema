<template>
  <div class="invoice-minimal bg-white shadow-lg rounded-lg overflow-hidden font-mono text-sm">
    <div class="border-3 border-black p-6">
      <!-- Header -->
      <div class="text-center pb-4 border-b-3 border-black mb-4">
        <div v-if="data.logo" class="mb-3 flex justify-center h-16">
          <img :src="data.logo" class="h-full object-contain">
        </div>
        <h1 class="font-bold text-2xl uppercase tracking-wide mb-1 text-black" 
            :class="{'text-gray-300': !data.storeName}">
          {{ data.storeName || 'MI EMPRESA' }}
        </h1>
        <p class="text-xs text-gray-700" :class="{'text-gray-300': !data.nit}">
          NIT: {{ data.nit || 'N/A' }}
        </p>
        <p class="text-xs text-gray-700" :class="{'text-gray-300': !data.address}">
          {{ data.address || 'Calle 123 #45-67, Bogotá' }}
        </p>
        <p class="text-xs text-gray-700" :class="{'text-gray-300': !data.phone}">
          {{ data.phone || '+57 300 123 4567' }}
        </p>
      </div>

      <!-- Invoice Info -->
      <div class="flex justify-between text-sm mb-4 font-bold">
        <div>
          <p class="text-black">Factura: {{ invoiceNumber }}</p>
          <p class="text-black">Fecha: {{ currentDate }}</p>
        </div>
      </div>

      <!-- Items Table -->
      <table class="w-full">
        <thead class="border-b-3 border-black">
          <tr class="text-xs font-bold text-black">
            <th class="text-left pb-2 px-2">PRODUCTO</th>
            <th class="text-center pb-2 px-2">CANT</th>
            <th class="text-right pb-2 px-2">PRECIO</th>
            <th class="text-right pb-2 px-2">TOTAL</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="(item, idx) in items" :key="idx" class="border-b border-gray-200">
            <td class="py-3 px-2 text-sm text-black">{{ item.name }}</td>
            <td class="py-3 px-2 text-center text-sm text-gray-700">{{ item.quantity }}</td>
            <td class="py-3 px-2 text-right text-sm text-gray-700">{{ formatNumber(item.price) }}</td>
            <td class="py-3 px-2 text-right text-sm font-bold text-black">{{ formatNumber(item.total) }}</td>
          </tr>
        </tbody>
      </table>

      <!-- Total -->
      <div class="mt-6 border-t-3 border-black pt-4 px-2">
        <div class="flex justify-end">
          <div class="text-right">
            <div class="text-sm mb-1 text-gray-700">
              Subtotal: <span class="font-bold">{{ formatNumber(subtotal) }}</span>
            </div>
            <div class="text-xl font-bold text-black">
              TOTAL: {{ formatNumber(total) }}
            </div>
          </div>
        </div>
      </div>

      <!-- Thank You Message -->
      <div class="text-center mt-6">
        <p class="text-gray-700 text-sm font-bold" :class="{'text-gray-300': !data.thankYouMessage}">
          {{ data.thankYouMessage || 'GRACIAS POR SU COMPRA' }}
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
      { name: 'Producto A', quantity: 1, total: 25000 },
      { name: 'Producto B', quantity: 2, total: 50000 },
      { name: 'Servicio X', quantity: 1, total: 100000 }
    ]
  }
})

const invoiceNumber = 'PRE-001'
const clientName = 'GENERAL'

const currentDate = computed(() => new Date().toLocaleDateString('es-CO'))

const subtotal = computed(() => props.items.reduce((sum, item) => sum + item.total, 0))
const tax = computed(() => Math.round(subtotal.value * 0.19))
const total = computed(() => subtotal.value + tax.value)

const formatNumber = (value) => {
  return value.toLocaleString('es-CO')
}
</script>

<style scoped>
.invoice-minimal {
  font-family: 'Courier New', Courier, monospace;
}
</style>
