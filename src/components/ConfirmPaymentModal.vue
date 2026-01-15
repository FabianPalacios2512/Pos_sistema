<template>
  <div class="fixed inset-0 bg-black/85 flex items-center justify-center z-50 p-4">
    <div class="bg-white dark:bg-zinc-900 rounded-2xl shadow-2xl max-w-md w-full border border-gray-200 dark:border-zinc-700 max-h-[90vh] flex flex-col overflow-hidden relative">
      
      <template v-if="currentState === 'confirm'">
        <div class="bg-white dark:bg-zinc-900 border-b border-gray-200 dark:border-zinc-800 px-5 py-4 rounded-t-2xl flex-shrink-0">
          <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
              <div class="w-10 h-10 bg-blue-50 dark:bg-blue-950/50 rounded-xl flex items-center justify-center border border-blue-100 dark:border-blue-900/50">
                <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
              </div>
              <div>
                <h3 class="text-base font-bold text-gray-900 dark:text-white">Confirmar Pago</h3>
                <p class="text-xs text-gray-600 dark:text-zinc-400">Verifique los datos antes de procesar</p>
              </div>
            </div>
            <button @click="$emit('close')" class="text-gray-500 hover:text-gray-700 dark:text-zinc-400 dark:hover:text-zinc-200 hover:bg-gray-100 dark:hover:bg-zinc-800 rounded-lg p-1.5 transition-colors">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path>
              </svg>
            </button>
          </div>
        </div>

        <div class="p-5 space-y-3 overflow-y-auto flex-1">
          <div v-if="systemSettings" class="rounded-xl p-3 border border-gray-200 dark:border-zinc-700 bg-gray-50 dark:bg-zinc-800">
            <div class="text-center">
              <h4 class="font-bold text-sm text-gray-900 dark:text-white">{{ systemSettings.company_name }}</h4>
              <p v-if="systemSettings.company_address" class="text-xs text-gray-600 dark:text-zinc-400 mt-0.5">{{ systemSettings.company_address }}</p>
              <p v-if="systemSettings.company_phone" class="text-xs text-gray-600 dark:text-zinc-400">Tel: {{ systemSettings.company_phone }}</p>
              <div class="mt-3 pt-3 border-t border-gray-200 dark:border-zinc-700">
                <p class="text-xs font-medium text-gray-500 dark:text-zinc-500 uppercase tracking-wider">Factura N°</p>
                <p class="text-base font-bold text-slate-900 dark:text-white mt-1">{{ invoiceNumber }}</p>
              </div>
            </div>
          </div>

          <div class="bg-white dark:bg-zinc-800 rounded-xl p-3 border border-gray-200 dark:border-zinc-700">
            <h4 class="text-sm font-bold text-gray-900 dark:text-white mb-2">Resumen de la Venta</h4>
            
            <div class="space-y-1.5">
              <div class="flex justify-between text-xs">
                <span class="text-gray-600 dark:text-zinc-400">Productos ({{ totalItems }} items):</span>
                <span class="text-gray-900 dark:text-white font-semibold">${{ subtotal.toLocaleString() }}</span>
              </div>
              
              <div v-if="discount > 0" class="flex justify-between text-xs">
                <span class="text-gray-600 dark:text-zinc-400">Descuento:</span>
                <span class="text-green-600 dark:text-green-400 font-semibold">-${{ discount.toLocaleString() }}</span>
              </div>
              
              <div class="flex justify-between text-xs">
                <span class="text-gray-600 dark:text-zinc-400">IVA ({{ taxRate }}%):</span>
                <span class="text-gray-900 dark:text-white font-semibold">${{ tax.toLocaleString() }}</span>
              </div>

              <div v-if="paymentFee > 0" class="flex justify-between text-xs">
                <span class="text-gray-600 dark:text-zinc-400">Comisión {{ paymentMethod.name }}:</span>
                <span class="text-orange-600 dark:text-orange-400 font-semibold">+${{ paymentFee.toLocaleString() }}</span>
              </div>
              
              <div class="border-t border-gray-200 dark:border-zinc-700 pt-2 mt-2">
                <div class="flex justify-between font-bold text-sm">
                  <span class="text-gray-900 dark:text-white">Total a Pagar:</span>
                  <span class="text-blue-600 dark:text-blue-400 text-lg">${{ total.toLocaleString() }}</span>
                </div>
              </div>
            </div>

            <div class="bg-white dark:bg-zinc-900 rounded-xl p-3 border border-gray-200 dark:border-zinc-700 mt-3">
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

          <div v-if="customer" class="bg-gray-50 dark:bg-zinc-800 rounded-xl p-3 border border-gray-200 dark:border-zinc-700">
            <div class="flex items-center gap-2">
              <div class="w-8 h-8 bg-gray-200 dark:bg-zinc-700 rounded-lg flex items-center justify-center">
                <svg class="w-4 h-4 text-gray-600 dark:text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
              </div>
              <div>
                <p class="text-sm font-bold text-gray-900 dark:text-white">{{ customer.name }}</p>
                <p v-if="customer.email || customer.phone" class="text-xs text-gray-500 dark:text-zinc-400">
                  {{ customer.email || customer.phone }}
                </p>
              </div>
            </div>
          </div>

          <div class="bg-amber-50 dark:bg-amber-950/20 rounded-lg p-2 border border-amber-200 dark:border-amber-900/50">
            <div class="flex items-start gap-2">
              <svg class="w-4 h-4 text-amber-600 dark:text-amber-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
              </svg>
              <p class="text-xs text-amber-800 dark:text-amber-400">
                Esta operación procesará el pago y generará la factura.
              </p>
            </div>
          </div>
        </div>

        <div class="p-4 bg-gray-50 dark:bg-zinc-800 border-t border-gray-200 dark:border-zinc-700 rounded-b-2xl flex-shrink-0">
          <div class="flex gap-2">
            <button
              @click="$emit('close')"
              class="flex-1 px-4 py-2.5 bg-white dark:bg-zinc-900 hover:bg-slate-50 dark:hover:bg-zinc-800 text-slate-600 dark:text-zinc-200 border border-slate-200 dark:border-zinc-800 text-sm font-semibold rounded-xl transition-colors"
            >
              Cancelar
            </button>
            <button
              @click="confirmPayment"
              :disabled="processing"
              class="flex-1 px-4 py-2.5 bg-slate-900 dark:bg-slate-700 hover:bg-black dark:hover:bg-slate-600 disabled:bg-gray-300 disabled:dark:bg-zinc-800 disabled:cursor-not-allowed text-white text-sm font-bold rounded-xl shadow-lg transition-colors flex items-center justify-center gap-2"
            >
              <svg v-if="processing" class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
              </svg>
              {{ processing ? 'Procesando...' : 'Confirmar Pago' }}
            </button>
          </div>
        </div>
      </template>

      <template v-else-if="currentState === 'success'">
        <div class="rounded-t-2xl p-6 text-center bg-gradient-to-br from-green-50 to-emerald-50 dark:from-green-950/30 dark:to-emerald-950/30 border-b border-green-200 dark:border-green-900">
          <button 
            @click="handleClose"
            class="absolute top-3 right-3 w-10 h-10 flex items-center justify-center rounded-xl bg-white/80 dark:bg-zinc-800/80 hover:bg-white dark:hover:bg-zinc-700 transition-colors"
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
          <p class="text-2xl font-bold text-green-600 dark:text-green-400">${{ total.toLocaleString() }}</p>
        </div>

        <div class="p-5">
          <h4 class="text-sm font-bold text-gray-900 dark:text-white mb-4 text-center">¿Qué deseas hacer ahora?</h4>
          
          <div class="space-y-3">
            <button
              @click="handleAction('print')"
              class="w-full flex items-center justify-center gap-3 p-4 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white text-sm rounded-xl transition-colors shadow-md font-bold"
            >
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
              </svg>
              <span>Imprimir Factura</span>
            </button>

            <button
              @click="handleAction('whatsapp')"
              class="w-full flex items-center justify-center gap-3 p-4 bg-gradient-to-r from-green-600 to-emerald-600 hover:from-green-700 hover:to-emerald-700 text-white text-sm rounded-xl transition-colors shadow-md font-bold"
            >
              <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/>
              </svg>
              <span>Enviar por WhatsApp</span>
            </button>

            <button
              @click="handleAction('view')"
              class="w-full flex items-center justify-center gap-3 p-3 bg-white dark:bg-zinc-800 hover:bg-gray-50 dark:hover:bg-zinc-700 text-gray-700 dark:text-zinc-300 text-sm rounded-xl transition-colors border border-gray-200 dark:border-zinc-700 font-semibold"
            >
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
              </svg>
              <span>Ver Recibo</span>
            </button>
          </div>

          <button
            @click="handleAction('new-sale')"
            class="w-full mt-4 py-3 bg-slate-900 dark:bg-slate-700 hover:bg-black dark:hover:bg-slate-600 text-white text-sm font-bold rounded-xl shadow-lg transition-colors"
          >
            Nueva Venta
          </button>
        </div>
      </template>

    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'

const props = defineProps({
  total: { type: Number, required: true },
  subtotal: { type: Number, required: true },
  tax: { type: Number, required: true },
  taxRate: { type: Number, required: true },
  discount: { type: Number, default: 0 },
  totalItems: { type: Number, required: true },
  paymentMethod: { type: Object, required: true },
  paymentAmount: { type: Number, required: true },
  change: { type: Number, default: 0 },
  customer: { type: Object, default: null },
  systemSettings: { type: Object, default: null },
  invoiceNumber: { type: String, default: 'FACT-000000' }
})

const emit = defineEmits(['close', 'payment-confirmed', 'print-invoice', 'send-whatsapp', 'view-invoice', 'new-sale'])

const currentState = ref('confirm')
const processing = ref(false)
const paymentData = ref(null)

const paymentFee = computed(() => {
  if (!props.paymentMethod.fee_amount) return 0
  
  if (props.paymentMethod.fee_type === 'fixed') {
    return props.paymentMethod.fee_amount
  } else if (props.paymentMethod.fee_type === 'percentage') {
    return Math.round((props.subtotal * props.paymentMethod.fee_amount) / 100)
  }
  
  return 0
})

const confirmPayment = async () => {
  processing.value = true
  
  await new Promise(resolve => setTimeout(resolve, 300))
  
  paymentData.value = {
    method: props.paymentMethod.code || props.paymentMethod.id,
    methodName: props.paymentMethod.name,
    amount: props.paymentAmount,
    change: props.change,
    fee: paymentFee.value,
    timestamp: new Date().toISOString(),
    invoiceNumber: props.invoiceNumber
  }
  
  emit('payment-confirmed', paymentData.value)
  
  processing.value = false
  currentState.value = 'success'
}

const handleAction = (action) => {
  switch (action) {
    case 'print':
      emit('print-invoice')
      break
    case 'whatsapp':
      emit('send-whatsapp')
      break
    case 'view':
      emit('view-invoice')
      break
    case 'new-sale':
      emit('new-sale')
      emit('close')
      break
  }
}

const handleClose = () => {
  currentState.value = 'confirm'
  emit('close')
}
</script>
