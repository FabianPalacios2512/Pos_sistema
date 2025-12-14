<template>
  <!-- PREVIEW EXACTO del ticket térmico MODERN que genera invoiceTemplate.js -->
  <div class="thermal-ticket-modern bg-white shadow-lg font-sans text-xs" style="width: 300px; margin: 0 auto;">
    
    <!-- Header Empresa CON FONDO INDIGO (como el PDF real) -->
    <div class="text-center pb-3 mb-3 px-3 pt-4 text-white" style="background: linear-gradient(135deg, #4F46E5 0%, #7C3AED 100%);">
      <!-- Logo -->
      <div v-if="data.logo" class="flex justify-center mb-2">
        <img :src="data.logo" alt="Logo" class="h-12 w-auto object-contain bg-white rounded px-2 py-1">
      </div>
      
      <h1 class="text-base font-bold uppercase tracking-wide mb-1">
        {{ data.storeName || 'MI EMPRESA' }}
      </h1>
      <p v-if="data.nit" class="text-[10px] opacity-90 mb-0.5">
        NIT: {{ data.nit }}
      </p>
      <p v-if="data.address" class="text-[10px] opacity-90 mb-0.5">
        {{ data.address }}
      </p>
      <p v-if="data.phone" class="text-[10px] opacity-90">
        {{ data.phone }}
      </p>
    </div>

    <!-- Número de Factura CON ESTILO INDIGO -->
    <div class="border-2 rounded mx-3 mb-3 p-2" style="background: linear-gradient(135deg, #EEF2FF 0%, #F5F3FF 100%); border-color: #4F46E5;">
      <p class="text-center text-xs font-bold mb-1" style="color: #4F46E5;">FACTURA DE VENTA</p>
      <p class="text-center text-xs font-bold" style="color: #4F46E5;">No. PRE-001</p>
    </div>

    <!-- Fecha y Cliente -->
    <div class="px-3 mb-3 text-[10px] text-gray-700 space-y-0.5">
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

    <!-- Línea divisoria -->
    <div class="border-t border-dashed my-3" style="border-color: #C7D2FE;"></div>

    <!-- Items -->
    <div class="px-3 mb-3">
      <div v-for="(item, idx) in items" :key="idx" class="mb-2">
        <div class="text-[10px] font-semibold text-gray-900">{{ item.name }}</div>
        <div class="flex justify-between text-[10px] text-gray-600 mt-0.5">
          <span>{{ item.quantity }} x ${{ formatNumber(item.price) }}</span>
          <span class="font-bold text-gray-900">${{ formatNumber(item.total) }}</span>
        </div>
      </div>
    </div>

    <!-- Línea divisoria -->
    <div class="border-t border-dashed my-3" style="border-color: #C7D2FE;"></div>

    <!-- Totales CON FONDO INDIGO CLARO -->
    <div class="mx-3 mb-3 p-2 rounded" style="background: linear-gradient(135deg, #EEF2FF 0%, #F5F3FF 100%); border: 1px solid #C7D2FE;">
      <div class="space-y-1 text-[10px]">
        <div class="flex justify-between text-gray-700">
          <span>Subtotal:</span>
          <span>${{ formatNumber(subtotal) }}</span>
        </div>
        <div class="flex justify-between font-bold text-sm pt-1" style="color: #4F46E5;">
          <span>TOTAL:</span>
          <span>${{ formatNumber(total) }}</span>
        </div>
      </div>
    </div>

    <!-- Mensaje de agradecimiento -->
    <div class="text-center px-3 mb-3 pt-3 border-t border-dashed" style="border-color: #C7D2FE;">
      <p class="text-[10px] font-semibold text-gray-800">
        {{ data.thankYouMessage || '¡Gracias por su compra!' }}
      </p>
    </div>

    <!-- QR Code placeholder -->
    <div class="flex justify-center mb-3">
      <div class="w-16 h-16 rounded flex items-center justify-center" style="background: #EEF2FF;">
        <svg class="w-10 h-10" style="color: #4F46E5;" fill="currentColor" viewBox="0 0 24 24">
          <path d="M3 3h8v8H3V3zm10 0h8v8h-8V3zM3 13h8v8H3v-8zm10 0h8v8h-8v-8z"/>
        </svg>
      </div>
    </div>

    <!-- Footer -->
    <div class="text-center px-3 pb-4 text-[9px] text-gray-500">
      <p>Powered by 105 POS</p>
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
const formatDate = () => new Date().toLocaleDateString('es-CO', { day: '2-digit', month: 'long', year: 'numeric' })
const formatTime = () => new Date().toLocaleTimeString('es-CO', { hour: '2-digit', minute: '2-digit', hour12: true })
</script>
