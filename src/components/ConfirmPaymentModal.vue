<template>
  <div class="fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center z-50 p-4">
    <div class="bg-white dark:bg-[#1a1a1d] rounded-2xl shadow-2xl max-w-md w-full overflow-hidden relative transition-all duration-300">
      
      <!-- ═══════════════════════════════════════════════════════════════════
           ESTADO 1: CONFIRMACIÓN DE PAGO - Estilo Terminal Financiero
      ═══════════════════════════════════════════════════════════════════ -->
      <template v-if="currentState === 'confirm'">
        
        <!-- Hero: El Monto Total (Lo más importante) -->
        <div class="pt-10 pb-8 px-6 text-center">
          <p class="text-5xl font-bold text-gray-900 dark:text-white tracking-tight">
            ${{ total.toLocaleString() }}
          </p>
          <p class="text-sm text-gray-400 dark:text-zinc-500 mt-2 font-medium uppercase tracking-wider">
            Total a Pagar
          </p>
          
          <!-- Detalles secundarios -->
          <div class="mt-4 flex items-center justify-center gap-4 text-xs text-gray-400 dark:text-zinc-500">
            <span>{{ totalItems }} {{ totalItems === 1 ? 'item' : 'items' }}</span>
            <span class="w-1 h-1 bg-gray-300 dark:bg-zinc-600 rounded-full"></span>
            <span>{{ invoiceNumber }}</span>
          </div>
        </div>

        <!-- Lista de Métodos de Pago - Estilo Lista Elegante -->
        <div class="border-t border-gray-100 dark:border-zinc-800">
          <p class="px-6 pt-5 pb-3 text-xs font-semibold text-gray-400 dark:text-zinc-500 uppercase tracking-wider">
            Método de pago seleccionado
          </p>
          
          <!-- Método Seleccionado (como fila de lista) -->
          <div class="mx-4 mb-4 bg-gray-50 dark:bg-zinc-800/50 rounded-xl border border-gray-100 dark:border-zinc-700/50">
            <div class="flex items-center justify-between p-4">
              <div class="flex items-center gap-4">
                <!-- Icono Minimalista de Línea -->
                <div class="w-10 h-10 rounded-xl bg-white dark:bg-zinc-800 border border-gray-200 dark:border-zinc-700 flex items-center justify-center">
                  <svg v-if="paymentMethod.code === 'cash' || paymentMethod.code === 'efectivo'" class="w-5 h-5 text-gray-600 dark:text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 00-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 01-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 003 15h-.75M15 10.5a3 3 0 11-6 0 3 3 0 016 0zm3 0h.008v.008H18V10.5zm-12 0h.008v.008H6V10.5z" />
                  </svg>
                  <svg v-else-if="paymentMethod.code === 'card' || paymentMethod.code === 'tarjeta'" class="w-5 h-5 text-gray-600 dark:text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5z" />
                  </svg>
                  <svg v-else-if="paymentMethod.code === 'transfer' || paymentMethod.code === 'transferencia'" class="w-5 h-5 text-gray-600 dark:text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 21L3 16.5m0 0L7.5 12M3 16.5h13.5m0-13.5L21 7.5m0 0L16.5 12M21 7.5H7.5" />
                  </svg>
                  <svg v-else class="w-5 h-5 text-gray-600 dark:text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                  </svg>
                </div>
                
                <div>
                  <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ paymentMethod.name }}</p>
                  <p v-if="paymentFee > 0" class="text-xs text-gray-400 dark:text-zinc-500 mt-0.5">
                    + ${{ paymentFee.toLocaleString() }} comisión
                  </p>
                </div>
              </div>
              
              <div class="text-right">
                <p class="text-base font-bold text-gray-900 dark:text-white">${{ paymentAmount.toLocaleString() }}</p>
                <p v-if="change > 0" class="text-xs text-emerald-600 dark:text-emerald-400 font-medium mt-0.5">
                  Cambio: ${{ change.toLocaleString() }}
                </p>
              </div>
            </div>
          </div>

          <!-- Información del Cliente (si existe) -->
          <div v-if="customer" class="mx-4 mb-4">
            <div class="flex items-center gap-3 p-3 bg-gray-50 dark:bg-zinc-800/30 rounded-xl">
              <div class="w-8 h-8 bg-gray-200 dark:bg-zinc-700 rounded-lg flex items-center justify-center">
                <svg class="w-4 h-4 text-gray-500 dark:text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                </svg>
              </div>
              <div class="flex-1 min-w-0">
                <p class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ customer.name }}</p>
                <p v-if="customer.email || customer.phone" class="text-xs text-gray-400 dark:text-zinc-500 truncate">
                  {{ customer.email || customer.phone }}
                </p>
              </div>
            </div>
          </div>
        </div>

        <!-- Footer: Botones -->
        <div class="px-4 pb-4 pt-2 space-y-3">
          <button
            @click="confirmPayment"
            :disabled="processing"
            class="w-full py-4 bg-gray-900 dark:bg-white hover:bg-black dark:hover:bg-gray-100 disabled:bg-gray-300 dark:disabled:bg-zinc-700 text-white dark:text-zinc-900 text-sm font-bold rounded-xl transition-all flex items-center justify-center gap-2"
          >
            <svg v-if="processing" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span>{{ processing ? 'Procesando...' : 'Confirmar Pago' }}</span>
          </button>
          
          <button
            @click="$emit('close')"
            class="w-full py-3 text-gray-500 dark:text-zinc-400 hover:text-red-600 dark:hover:text-red-400 text-sm font-medium transition-colors"
          >
            Cancelar Venta
          </button>
        </div>
      </template>

      <!-- ═══════════════════════════════════════════════════════════════════
           ESTADO 1.5: PROCESANDO EN BACKEND - Esperando CUFE/Factus
      ═══════════════════════════════════════════════════════════════════ -->
      <template v-else-if="currentState === 'processing'">
        <div class="py-12 px-6 text-center">
          <!-- Icono de documento animado (más amigable que spinner) -->
          <div class="w-20 h-20 mx-auto mb-5 relative">
            <div class="absolute inset-0 bg-emerald-50 dark:bg-emerald-950/30 rounded-2xl"></div>
            <div class="absolute inset-0 flex items-center justify-center">
              <svg class="w-10 h-10 text-emerald-600 dark:text-emerald-400 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
              </svg>
            </div>
          </div>
          
          <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">
            Registrando en la DIAN
          </h3>
          <p class="text-sm text-gray-500 dark:text-zinc-400 max-w-xs mx-auto mb-4">
            Esto puede tomar unos segundos...
          </p>
          
          <!-- Indicador de puntos animados (menos estresante que barra) -->
          <div class="flex items-center justify-center gap-1">
            <div class="w-2 h-2 bg-emerald-500 rounded-full animate-bounce" style="animation-delay: 0ms"></div>
            <div class="w-2 h-2 bg-emerald-500 rounded-full animate-bounce" style="animation-delay: 150ms"></div>
            <div class="w-2 h-2 bg-emerald-500 rounded-full animate-bounce" style="animation-delay: 300ms"></div>
          </div>
          
          <!-- Tip amigable -->
          <p class="text-xs text-gray-400 dark:text-zinc-500 mt-6">
            Puedes desactivar esto en el toggle de arriba
          </p>
        </div>
      </template>

      <!-- ═══════════════════════════════════════════════════════════════════
           ESTADO 2: PAGO EXITOSO - Estilo Recibo Digital Premium
      ═══════════════════════════════════════════════════════════════════ -->
      <template v-else-if="currentState === 'success'">
        
        <!-- Botón Cerrar -->
        <button 
          @click="handleClose"
          class="absolute top-4 right-4 w-8 h-8 flex items-center justify-center rounded-lg text-gray-400 dark:text-zinc-500 hover:text-gray-600 dark:hover:text-gray-300 hover:bg-gray-100 dark:hover:bg-zinc-800 transition-colors z-10"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path>
          </svg>
        </button>

        <!-- Icono de Éxito Sutil -->
        <div class="pt-10 pb-6 text-center">
          <div class="w-14 h-14 bg-emerald-500 rounded-full flex items-center justify-center mx-auto mb-5 shadow-lg shadow-emerald-500/30">
            <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
              <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"></path>
            </svg>
          </div>
          
          <!-- Cambio/Vueltas - EL DATO MÁS IMPORTANTE -->
          <div v-if="change > 0" class="mb-6">
            <p class="text-xs font-medium text-gray-400 dark:text-zinc-500 uppercase tracking-wider mb-2">
              Cambio a entregar
            </p>
            <p class="text-6xl font-bold text-gray-900 dark:text-white tracking-tight">
              ${{ change.toLocaleString() }}
            </p>
          </div>
          
          <div v-else class="mb-4">
            <p class="text-sm font-medium text-gray-500 dark:text-zinc-400">Pago Exacto</p>
          </div>
        </div>

        <!-- Resumen tipo Recibo -->
        <div class="mx-4 mb-6 p-4 bg-gray-50 dark:bg-zinc-800/50 rounded-xl border border-dashed border-gray-200 dark:border-zinc-700">
          <div class="space-y-2">
            <div class="flex justify-between text-sm">
              <span class="text-gray-500 dark:text-zinc-400">Total Venta</span>
              <span class="font-semibold text-gray-900 dark:text-white">${{ fixedTotal.toLocaleString() }}</span>
            </div>
            <div class="flex justify-between text-sm">
              <span class="text-gray-500 dark:text-zinc-400">Recibido</span>
              <span class="font-semibold text-gray-900 dark:text-white">${{ paymentAmount.toLocaleString() }}</span>
            </div>
            <div class="flex justify-between text-sm pt-2 border-t border-dashed border-gray-200 dark:border-zinc-700">
              <span class="text-gray-500 dark:text-zinc-400">Método</span>
              <span class="font-medium text-gray-700 dark:text-zinc-300">{{ paymentMethod.name }}</span>
            </div>
          </div>
        </div>

        <!-- Acciones -->
        <div class="px-4 pb-6 space-y-3">
          <!-- Botón Principal: Nueva Venta -->
          <button
            @click="handleAction('new-sale')"
            class="w-full py-4 bg-gray-900 dark:bg-white hover:bg-black dark:hover:bg-gray-100 text-white dark:text-zinc-900 text-sm font-bold rounded-xl transition-all uppercase tracking-wide"
          >
            Nueva Venta
          </button>
          
          <!-- Botones Secundarios: Grid 2x2 -->
          <div class="grid grid-cols-2 gap-2">
            <button
              @click="handleAction('print')"
              class="py-3 flex items-center justify-center gap-2 text-gray-600 dark:text-zinc-400 hover:text-gray-900 dark:hover:text-white text-sm font-medium border border-gray-200 dark:border-zinc-700 hover:border-gray-300 dark:hover:border-zinc-600 rounded-xl transition-all"
            >
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0110.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0l.229 2.523a1.125 1.125 0 01-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0021 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 00-1.913-.247M6.34 18H5.25A2.25 2.25 0 013 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 011.913-.247m10.5 0a48.536 48.536 0 00-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.659M18 10.5h.008v.008H18V10.5zm-3 0h.008v.008H15V10.5z" />
              </svg>
              <span>Imprimir</span>
            </button>
            
            <button
              @click="handleAction('whatsapp')"
              class="py-3 flex items-center justify-center gap-2 text-gray-600 dark:text-zinc-400 hover:text-emerald-600 dark:hover:text-emerald-400 text-sm font-medium border border-gray-200 dark:border-zinc-700 hover:border-emerald-200 dark:hover:border-emerald-800 rounded-xl transition-all"
            >
              <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/>
              </svg>
              <span>WhatsApp</span>
            </button>

            <button
              @click="handleAction('download')"
              class="py-3 flex items-center justify-center gap-2 text-gray-600 dark:text-zinc-400 hover:text-blue-600 dark:hover:text-blue-400 text-sm font-medium border border-gray-200 dark:border-zinc-700 hover:border-blue-200 dark:hover:border-blue-800 rounded-xl transition-all"
            >
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
              </svg>
              <span>Descargar</span>
            </button>
            
            <button
              @click="handleAction('email')"
              class="py-3 flex items-center justify-center gap-2 text-gray-600 dark:text-zinc-400 hover:text-violet-600 dark:hover:text-violet-400 text-sm font-medium border border-gray-200 dark:border-zinc-700 hover:border-violet-200 dark:hover:border-violet-800 rounded-xl transition-all"
            >
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" />
              </svg>
              <span>Email</span>
            </button>
          </div>
          
          <button
            @click="handleAction('view')"
            class="w-full py-2.5 text-gray-400 dark:text-zinc-500 hover:text-gray-600 dark:hover:text-gray-300 text-xs font-medium transition-colors"
          >
            Ver recibo completo
          </button>
        </div>
      </template>

    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue'

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
  invoiceNumber: { type: String, default: 'FACT-000000' },
  // Estado de procesamiento del backend (para sincronización)
  backendProcessing: { type: Boolean, default: false },
  backendSuccess: { type: Boolean, default: false }
})

const emit = defineEmits(['close', 'payment-confirmed', 'print-invoice', 'send-whatsapp', 'view-invoice', 'new-sale', 'download-invoice', 'send-email'])

const currentState = ref('confirm')
const processing = ref(false)
const paymentData = ref(null)
const fixedTotal = ref(0)

// Observar cuando el backend termine de procesar exitosamente
watch(() => props.backendSuccess, (success) => {
  if (success && currentState.value === 'processing') {
    processing.value = false
    currentState.value = 'success'
  }
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

const confirmPayment = async () => {
  processing.value = true
  
  // Capturar el total ANTES de que se limpie el carrito
  fixedTotal.value = props.total
  
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
  
  // Emitir evento y cambiar a estado "processing" (esperar que el padre confirme éxito)
  emit('payment-confirmed', paymentData.value)
  
  // Cambiar a estado "processing" - el watch de backendSuccess cambiará a "success" cuando termine
  currentState.value = 'processing'
  // processing.value sigue true hasta que backendSuccess sea true
}

const handleAction = (action) => {
  switch (action) {
    case 'print':
      emit('print-invoice')
      break
    case 'whatsapp':
      emit('send-whatsapp')
      break
    case 'download':
      emit('download-invoice')
      break
    case 'email':
      emit('send-email')
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
