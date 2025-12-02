<template>
  <div class="fixed inset-0 bg-black/70 dark:bg-black/80 backdrop-blur-sm flex items-center justify-center z-50 p-4 animate-fade-in">
    <div class="bg-white dark:bg-zinc-900 rounded-2xl shadow-2xl max-w-lg w-full border border-gray-200 dark:border-zinc-700 animate-scale-in">
      
      <!-- Header Simple -->
      <div class="bg-gradient-to-r from-blue-600 to-indigo-600 dark:from-blue-700 dark:to-indigo-700 p-5 rounded-t-2xl">
        <div class="flex items-center justify-between">
          <div class="flex items-center gap-3">
            <div class="w-12 h-12 bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center">
              <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
              </svg>
            </div>
            <div>
              <h3 class="text-lg font-bold text-white">Confirmar Pago</h3>
              <p class="text-sm text-blue-100">Verifique los datos antes de procesar</p>
            </div>
          </div>
          <button @click="$emit('close')" class="text-white/80 hover:text-white hover:bg-white/10 rounded-xl p-2 transition-all">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
          </button>
        </div>
      </div>

      <!-- Contenido -->
      <div class="p-5 space-y-4">
        <!-- Información de la empresa -->
        <div v-if="systemSettings" class="rounded-xl p-4 border border-blue-200 dark:border-blue-900 bg-gradient-to-br from-blue-50 to-indigo-50 dark:from-blue-950/30 dark:to-indigo-950/30">
          <div class="text-center">
            <h4 class="font-bold text-base text-gray-900 dark:text-white">{{ systemSettings.company_name }}</h4>
            <p v-if="systemSettings.company_address" class="text-xs text-gray-600 dark:text-zinc-400 mt-1">{{ systemSettings.company_address }}</p>
            <p v-if="systemSettings.company_phone" class="text-xs text-gray-600 dark:text-zinc-400">Tel: {{ systemSettings.company_phone }}</p>
            <p class="text-sm text-blue-600 dark:text-blue-400 mt-2 font-bold">
              Factura N° {{ invoiceNumber }}
            </p>
          </div>
        </div>

        <!-- Resumen de Venta -->
        <div class="bg-gray-50 dark:bg-zinc-800 rounded-xl p-4 border border-gray-200 dark:border-zinc-700">
          <h4 class="text-sm font-bold text-gray-900 dark:text-white mb-3 flex items-center gap-2">
            <div class="w-9 h-9 bg-gradient-to-br from-blue-500 to-indigo-500 rounded-xl flex items-center justify-center shadow-lg">
              <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
              </svg>
            </div>
            <span>Resumen de la Venta</span>
          </h4>
          
          <div class="space-y-2 mb-4">
            <div class="flex justify-between text-sm">
              <span class="text-gray-600 dark:text-zinc-400 font-medium">Productos ({{ totalItems }} items):</span>
              <span class="text-gray-900 dark:text-white font-semibold">${{ subtotal.toLocaleString() }}</span>
            </div>
            
            <!-- Mostrar descuento aplicado -->
            <div v-if="discount > 0" class="flex justify-between text-sm">
              <span class="text-gray-600 dark:text-zinc-400 font-medium">Descuento:</span>
              <span class="text-green-600 dark:text-green-400 font-semibold">-${{ discount.toLocaleString() }}</span>
            </div>
            
            <!-- Mostrar IVA -->
            <div class="flex justify-between text-sm">
              <span class="text-gray-600 dark:text-zinc-400 font-medium">IVA ({{ taxRate }}%):</span>
              <span class="text-gray-900 dark:text-white font-semibold">${{ tax.toLocaleString() }}</span>
            </div>

            <!-- Mostrar comisión del método de pago si aplica -->
            <div v-if="paymentFee > 0" class="flex justify-between text-sm">
              <span class="text-gray-600 dark:text-zinc-400 font-medium">Comisión {{ paymentMethod.name }}:</span>
              <span class="text-orange-600 dark:text-orange-400 font-semibold">+${{ paymentFee.toLocaleString() }}</span>
            </div>
            
            <div class="border-t-2 border-gray-300 dark:border-zinc-600 pt-3 mt-3">
              <div class="flex justify-between font-bold text-base">
                <span class="text-gray-900 dark:text-white">Total a Pagar:</span>
                <span class="text-blue-600 dark:text-blue-400 text-xl">${{ total.toLocaleString() }}</span>
              </div>
            </div>
          </div>

          <!-- Método de Pago -->
          <div class="bg-white dark:bg-zinc-900 rounded-xl p-3 border border-gray-200 dark:border-zinc-700">
            <div class="flex items-center justify-between">
              <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center shadow-md" :style="{ backgroundColor: paymentMethod.icon_color || '#3b82f6' }">
                  <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                  </svg>
                </div>
                <div>
                  <p class="text-sm font-bold text-gray-900 dark:text-white">{{ paymentMethod.name }}</p>
                  <p v-if="paymentMethod.description" class="text-xs text-gray-500 dark:text-zinc-400">{{ paymentMethod.description }}</p>
                </div>
              </div>
              <div class="text-right">
                <p class="text-base font-bold text-gray-900 dark:text-white">${{ paymentAmount.toLocaleString() }}</p>
                <p v-if="change > 0" class="text-sm text-green-600 dark:text-green-400 font-semibold">Cambio: ${{ change.toLocaleString() }}</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Cliente -->
        <div v-if="customer" class="bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-950/30 dark:to-indigo-950/30 rounded-xl p-3 border border-blue-200 dark:border-blue-900">
          <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-blue-500 rounded-xl flex items-center justify-center shadow-md">
              <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
              </svg>
            </div>
            <div>
              <p class="text-sm font-bold text-blue-900 dark:text-blue-300">{{ customer.name }}</p>
              <p v-if="customer.email" class="text-xs text-blue-700 dark:text-blue-400">{{ customer.email }}</p>
              <p v-if="customer.phone" class="text-xs text-blue-700 dark:text-blue-400">{{ customer.phone }}</p>
            </div>
          </div>
        </div>

        <!-- Información adicional -->
        <div class="bg-gradient-to-r from-amber-50 to-orange-50 dark:from-amber-950/30 dark:to-orange-950/30 rounded-xl p-3 border border-amber-200 dark:border-amber-900">
          <div class="flex items-start gap-3">
            <div class="w-9 h-9 bg-amber-500 rounded-xl flex items-center justify-center flex-shrink-0 shadow-md">
              <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
              </svg>
            </div>
            <div>
              <p class="text-xs font-bold text-amber-900 dark:text-amber-300 mb-1">Información importante</p>
              <p class="text-xs text-amber-800 dark:text-amber-400 leading-relaxed">
                Esta operación procesará el pago y generará la factura correspondiente. 
                Verifique que toda la información sea correcta antes de continuar.
              </p>
            </div>
          </div>
        </div>
      </div>

      <!-- Footer -->
      <div class="p-5 bg-gray-50 dark:bg-zinc-800 border-t border-gray-200 dark:border-zinc-700 rounded-b-2xl">
        <div class="flex gap-3">
          <button
            @click="$emit('close')"
            class="flex-1 px-5 py-3 bg-white dark:bg-zinc-700 hover:bg-gray-100 dark:hover:bg-zinc-600 text-gray-700 dark:text-zinc-200 border border-gray-300 dark:border-zinc-600 text-sm font-bold rounded-xl transition-all hover:shadow-md"
          >
            Cancelar
          </button>
          <button
            @click="confirmPayment"
            :disabled="processing"
            class="flex-1 px-5 py-3 bg-gradient-to-r from-green-600 to-emerald-600 hover:from-green-700 hover:to-emerald-700 disabled:from-gray-300 disabled:to-gray-300 disabled:cursor-not-allowed text-white text-sm font-bold rounded-xl shadow-lg transition-all hover:scale-105 disabled:hover:scale-100 flex items-center justify-center gap-2"
          >
            <svg v-if="processing" class="w-5 h-5 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
            </svg>
            {{ processing ? 'Procesando...' : 'Confirmar Pago' }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'

// Props
const props = defineProps({
  total: { type: Number, required: true },
  subtotal: { type: Number, required: true },
  tax: { type: Number, required: true },
  taxRate: { type: Number, required: true },
  discount: { type: Number, default: 0 },
  totalItems: { type: Number, required: true },
  paymentMethod: { type: Object, required: true }, // Ahora es un objeto completo
  paymentAmount: { type: Number, required: true },
  change: { type: Number, default: 0 },
  customer: { type: Object, default: null },
  systemSettings: { type: Object, default: null }
})

// Emits
const emit = defineEmits(['close', 'payment-confirmed'])

// Estado
const processing = ref(false)

// Computed
const invoiceNumber = computed(() => {
  if (props.systemSettings?.next_invoice_number) {
    return props.systemSettings.next_invoice_number
  }
  return `FAC-${String(Date.now()).slice(-6)}`
})

const paymentFee = computed(() => {
  if (!props.paymentMethod.fee_amount) return 0
  
  if (props.paymentMethod.fee_type === 'fixed') {
    return props.paymentMethod.fee_amount
  } else if (props.paymentMethod.fee_type === 'percentage') {
    return Math.round((props.subtotal * props.paymentMethod.fee_amount) / 100)
  }
  
  return 0
})

// Métodos
const confirmPayment = async () => {
  processing.value = true
  
  // Simular procesamiento más rápido
  await new Promise(resolve => setTimeout(resolve, 800))
  
  const paymentData = {
    method: props.paymentMethod.code || props.paymentMethod.id,
    methodName: props.paymentMethod.name,
    amount: props.paymentAmount,
    change: props.change,
    fee: paymentFee.value,
    timestamp: new Date().toISOString(),
    invoiceNumber: invoiceNumber.value
  }
  
  processing.value = false
  emit('payment-confirmed', paymentData)
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

.flex-2 {
  flex: 2;
}
</style>