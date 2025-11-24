<template>
  <div class="fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4">
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-sm w-full border border-gray-200 dark:border-gray-700">
      
      <!-- Header -->
      <div class="rounded-t-lg p-4 text-center relative" style="background-color: #E6FFF1;">
        <!-- Botón cerrar -->
        <button 
          @click="$emit('close')"
          class="absolute top-2 right-2 min-w-[40px] min-h-[40px] flex items-center justify-center rounded-lg bg-white/50 hover:bg-white/70 transition-all"
        >
          <svg class="w-5 h-5 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
          </svg>
        </button>
        
        <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center mx-auto mb-2">
          <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
          </svg>
        </div>
        <h3 class="text-base font-bold text-gray-900">¡Pago Realizado!</h3>
        <p class="text-sm text-gray-700">Total: ${{ saleTotal.toLocaleString() }}</p>
      </div>

      <!-- Contenido -->
      <div class="p-4">
        <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-3 text-center">¿Qué deseas hacer ahora?</h4>
        
        <div class="space-y-2">
          <!-- Imprimir Factura -->
          <button
            @click="printInvoice"
            class="w-full flex items-center justify-center gap-2 p-3 bg-blue-600 hover:bg-blue-700 text-white text-sm rounded-lg transition-colors"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
            </svg>
            <span class="font-medium">Imprimir Factura</span>
          </button>



          <!-- Enviar por WhatsApp -->
          <button
            @click="sendWhatsApp"
            onclick="console.log('🔥 BOTÓN WHATSAPP CLICKEADO - AfterPaymentModal')"
            class="w-full flex items-center justify-center gap-2 p-3 bg-green-600 hover:bg-green-700 text-white text-sm rounded-lg transition-colors"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21l4-4 4 4M3 20l6-6M15 3l6 6m-6-6v12.5a.5.5 0 01-.5.5H9a.5.5 0 01-.5-.5V7.5a.5.5 0 01.5-.5h5a.5.5 0 01.5.5V9"></path>
            </svg>
            <span class="font-medium">Enviar por WhatsApp</span>
          </button>

          <!-- Ver Factura (Mostrar modal de recibo) -->
          <button
            @click="viewInvoice"
            class="w-full flex items-center justify-center gap-2 p-3 bg-gray-600 hover:bg-gray-700 text-white text-sm rounded-lg transition-colors"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
            </svg>
            <span class="font-medium">Ver Factura</span>
          </button>
        </div>

        <!-- Separador -->
        <div class="border-t border-gray-200 dark:border-gray-600 my-3"></div>

        <!-- Nueva Venta -->
        <button
          @click="newSale"
          class="w-full flex items-center justify-center gap-2 p-3 bg-orange-600 hover:bg-orange-700 text-white text-sm rounded-lg transition-colors font-medium"
        >
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
          </svg>
          <span>Nueva Venta</span>
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { defineEmits, defineProps } from 'vue'

// Props
const props = defineProps({
  saleTotal: { type: Number, required: true },
  sale: { type: Object, required: true }
})

// Emits
const emit = defineEmits(['close', 'print-invoice', 'send-whatsapp', 'view-invoice', 'new-sale'])

// Métodos
const printInvoice = () => {
  emit('print-invoice')
}



const sendWhatsApp = () => {
  console.log('🚀 AfterPaymentModal sendWhatsApp ejecutado')
  emit('send-whatsapp')
}

const viewInvoice = () => {
  emit('view-invoice')
}

const newSale = () => {
  emit('new-sale')
}


</script>

<style scoped>
@keyframes fadeIn {
  from { opacity: 0; }
  to { opacity: 1; }
}

@keyframes scaleIn {
  from {
    opacity: 0;
    transform: scale(0.95) translateY(10px);
  }
  to {
    opacity: 1;
    transform: scale(1) translateY(0);
  }
}

.animate-fade-in {
  animation: fadeIn 0.3s ease-out;
}

.animate-scale-in {
  animation: scaleIn 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
}
</style>