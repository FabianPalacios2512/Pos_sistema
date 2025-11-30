<template>
  <!-- PREVIEW EXACTO del ticket térmico MINIMAL que genera invoiceTemplate.js -->
  <div class="thermal-ticket-minimal bg-white shadow-lg font-mono text-xs" style="width: 300px; margin: 0 auto;">
    
    <!-- Header Empresa CON BORDE NEGRO GRUESO -->
    <div class="text-center border-b-4 border-black pb-3 mb-3 px-3 pt-4">
      <!-- Logo -->
      <div v-if="data.logo" class="flex justify-center mb-2">
        <img :src="data.logo" alt="Logo" class="h-12 w-auto object-contain border-2 border-black p-1">
      </div>
      
      <h1 class="text-base font-bold uppercase tracking-widest mb-1 text-black">
        {{ data.storeName || 'MI EMPRESA' }}
      </h1>
      <p v-if="data.nit" class="text-[10px] text-gray-700 mb-0.5">
        NIT: {{ data.nit }}
      </p>
      <p v-if="data.address" class="text-[10px] text-gray-700 mb-0.5">
        {{ data.address }}
      </p>
      <p v-if="data.phone" class="text-[10px] text-gray-700">
        {{ data.phone }}
      </p>
    </div>

    <!-- Número de Factura CON BORDE NEGRO -->
    <div class="border-3 border-black rounded mx-3 mb-3 p-2">
      <p class="text-center text-xs font-bold text-black mb-1 uppercase">FACTURA DE VENTA</p>
      <p class="text-center text-xs font-bold text-black">No. PRE-001</p>
    </div>

    <!-- Fecha y Cliente -->
    <div class="px-3 mb-3 text-[10px] text-gray-800 font-bold space-y-0.5">
      <div class="flex justify-between">
        <span>Fecha:</span>
        <span>{{ formatDate() }}</span>
      </div>
      <div class="flex justify-between">
        <span>Hora:</span>
        <span>{{ formatTime() }}</span>
      </div>
      <div class="flex justify-between">
        <span>Cliente:</span>
        <span>Cliente General</span>
      </div>
    </div>

    <!-- Línea divisoria NEGRA -->
    <div class="border-t-2 border-dashed border-black my-3"></div>

    <!-- Items -->
    <div class="px-3 mb-3">
      <div v-for="(item, idx) in items" :key="idx" class="mb-2">
        <div class="text-[10px] font-bold text-black uppercase">{{ item.name }}</div>
        <div class="flex justify-between text-[10px] text-gray-700 font-bold mt-0.5">
          <span>{{ item.quantity }} x ${{ formatNumber(item.price) }}</span>
          <span class="text-black">${{ formatNumber(item.total) }}</span>
        </div>
      </div>
    </div>

    <!-- Línea divisoria NEGRA -->
    <div class="border-t-2 border-dashed border-black my-3"></div>

    <!-- Totales CON BORDE NEGRO -->
    <div class="border-t-4 border-black mx-3 mb-3 pt-2">
      <div class="space-y-1 text-[10px] font-bold">
        <div class="flex justify-between text-gray-800">
          <span>Subtotal:</span>
          <span>${{ formatNumber(subtotal) }}</span>
        </div>
        <div class="flex justify-between text-sm text-black pt-1 uppercase">
          <span>TOTAL:</span>
          <span>${{ formatNumber(total) }}</span>
        </div>
      </div>
    </div>

    <!-- Mensaje de agradecimiento -->
    <div class="text-center px-3 mb-3 pt-3 border-t-2 border-dashed border-black">
      <p class="text-[10px] font-bold text-black uppercase">
        {{ data.thankYouMessage || 'GRACIAS POR SU COMPRA' }}
      </p>
    </div>

    <!-- QR Code placeholder -->
    <div class="flex justify-center mb-3">
      <div class="w-16 h-16 border-4 border-black flex items-center justify-center bg-white">
        <svg class="w-10 h-10 text-black" fill="currentColor" viewBox="0 0 24 24">
          <path d="M3 3h8v8H3V3zm10 0h8v8h-8V3zM3 13h8v8H3v-8zm10 0h8v8h-8v-8z"/>
        </svg>
      </div>
    </div>

    <!-- Footer -->
    <div class="text-center px-3 pb-4 text-[9px] text-gray-600 uppercase font-bold">
      <p>POWERED BY 105 POS</p>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  data: { type: Object, required: true },
  items: {
    type: Array,
    default: () => [
      { name: 'Producto A', quantity: 2, price: 25000, total: 50000 },
      { name: 'Producto B', quantity: 1, price: 100000, total: 100000 }
    ]
  }
})

const subtotal = computed(() => props.items.reduce((sum, item) => sum + item.total, 0))
const total = computed(() => subtotal.value)

const formatNumber = (value) => value.toLocaleString('es-CO')
const formatDate = () => new Date().toLocaleDateString('es-CO', { day: '2-digit', month: 'short', year: 'numeric' })
const formatTime = () => new Date().toLocaleTimeString('es-CO', { hour: '2-digit', minute: '2-digit', hour12: false })
</script>

<style scoped>
.border-3 {
  border-width: 3px;
}
</style>
