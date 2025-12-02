<template>
  <div class="fixed inset-0 bg-black/70 dark:bg-black/80 backdrop-blur-sm flex items-center justify-center z-50 p-4 animate-fade-in">
    <div class="bg-white dark:bg-zinc-900 rounded-2xl shadow-2xl max-w-sm w-full border border-gray-200 dark:border-zinc-700 animate-scale-in">
      
      <!-- Header -->
      <div class="rounded-t-2xl p-6 text-center relative bg-gradient-to-br from-green-50 to-emerald-50 dark:from-green-950/30 dark:to-emerald-950/30 border-b border-green-200 dark:border-green-900">
        <!-- Botón cerrar -->
        <button 
          @click="$emit('close')"
          class="absolute top-3 right-3 w-10 h-10 flex items-center justify-center rounded-xl bg-white/60 dark:bg-zinc-800/60 hover:bg-white dark:hover:bg-zinc-700 transition-all backdrop-blur-sm"
        >
          <svg class="w-5 h-5 text-gray-700 dark:text-zinc-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path>
          </svg>
        </button>
        
        <div class="w-16 h-16 bg-gradient-to-br from-green-500 to-emerald-500 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-lg">
          <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
          </svg>
        </div>
        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">¡Pago Realizado!</h3>
        <p class="text-2xl font-bold text-green-600 dark:text-green-400">${{ saleTotal.toLocaleString() }}</p>
      </div>

      <!-- Contenido -->
      <div class="p-5">
        <h4 class="text-sm font-bold text-gray-900 dark:text-white mb-4 text-center">¿Qué deseas hacer ahora?</h4>
        
        <div class="space-y-3">
          <!-- Imprimir Factura -->
          <button
            @click="printInvoice"
            class="w-full flex items-center justify-center gap-3 p-4 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white text-sm rounded-xl transition-all shadow-md hover:shadow-lg hover:scale-105 font-bold"
          >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
            </svg>
            <span>Imprimir Factura</span>
          </button>



          <!-- Enviar por WhatsApp -->
          <button
            @click="sendWhatsApp"
            onclick="console.log('🔥 BOTÓN WHATSAPP CLICKEADO - AfterPaymentModal')"
            class="w-full flex items-center justify-center gap-3 p-4 bg-gradient-to-r from-green-600 to-emerald-600 hover:from-green-700 hover:to-emerald-700 text-white text-sm rounded-xl transition-all shadow-md hover:shadow-lg hover:scale-105 font-bold"
          >
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
              <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/>
            </svg>
            <span>Enviar por WhatsApp</span>
          </button>

          <!-- Ver Factura (Mostrar modal de recibo) -->
          <button
            @click="viewInvoice"
            class="w-full flex items-center justify-center gap-3 p-4 bg-gradient-to-r from-slate-600 to-gray-600 hover:from-slate-700 hover:to-gray-700 text-white text-sm rounded-xl transition-all shadow-md hover:shadow-lg hover:scale-105 font-bold"
          >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
              <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
            </svg>
            <span>Ver Factura</span>
          </button>
        </div>

        <!-- Separador -->
        <div class="border-t-2 border-gray-200 dark:border-zinc-700 my-4"></div>

        <!-- Nueva Venta -->
        <button
          @click="newSale"
          class="w-full flex items-center justify-center gap-3 p-4 bg-gradient-to-r from-orange-600 to-amber-600 hover:from-orange-700 hover:to-amber-700 text-white text-sm rounded-xl transition-all shadow-md hover:shadow-lg hover:scale-105 font-bold"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
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