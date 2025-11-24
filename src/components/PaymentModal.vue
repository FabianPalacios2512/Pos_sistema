<template>
  <div class="fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center z-50 p-4 animate-fade-in">
    <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-2xl max-w-lg w-full animate-scale-in border border-gray-100 dark:border-gray-700 overflow-hidden">
      
      <!-- Header Mejorado -->
      <div class="bg-gradient-to-r from-green-500 to-emerald-600 p-6 text-white">
        <div class="flex items-center justify-between">
          <div class="flex items-center space-x-4">
            <div class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center backdrop-blur-sm">
              <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
              </svg>
            </div>
            <div>
              <h3 class="text-2xl font-bold">Procesar Pago</h3>
              <p class="text-green-100">Total a pagar: <span class="font-bold text-xl">${{ total.toFixed(2) }}</span></p>
            </div>
          </div>
          <button 
            @click="$emit('close')" 
            class="text-white/80 hover:text-white hover:bg-white/20 rounded-lg p-2 transition-all min-w-[40px] min-h-[40px] flex items-center justify-center"
          >
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
          </button>
        </div>
      </div>

      <!-- Métodos de Pago Mejorados -->
      <div class="p-6">
        <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
          <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
          </svg>
          Selecciona el método de pago
        </h4>
        
        <div class="grid grid-cols-2 gap-3 mb-6">
          <div 
            v-for="method in paymentMethods" 
            :key="method.id"
            @click="selectedMethod = method.id"
            :class="[
              'flex flex-col items-center p-4 rounded-2xl border-2 cursor-pointer transition-all duration-300 hover:shadow-lg transform hover:-translate-y-1',
              selectedMethod === method.id 
                ? 'border-green-500 bg-green-50 dark:bg-green-900/20 shadow-lg scale-105' 
                : 'border-gray-200 dark:border-gray-700 hover:border-gray-300 dark:hover:border-gray-600'
            ]"
          >
            <div :class="[
              'w-12 h-12 rounded-full flex items-center justify-center mb-3 transition-all duration-300',
              method.color,
              selectedMethod === method.id ? 'shadow-lg transform scale-110' : ''
            ]">
              <component :is="method.icon" class="w-6 h-6 text-white" />
            </div>
            <div class="text-center">
              <h5 class="font-semibold text-gray-900 dark:text-white">{{ method.name }}</h5>
              <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ method.description }}</p>
            </div>
            <div v-if="selectedMethod === method.id" class="mt-2">
              <div class="w-6 h-6 bg-green-500 rounded-full flex items-center justify-center animate-bounce">
                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                </svg>
              </div>
            </div>
          </div>
        </div>

        <!-- Campo de Monto Mejorado (para efectivo) -->
        <div v-if="selectedMethod === 'cash'" class="mb-6 animate-slide-down">
          <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3 flex items-center">
            <svg class="w-4 h-4 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
            </svg>
            Monto Recibido
          </label>
          <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
              <span class="text-gray-500 dark:text-gray-400 text-xl font-bold">$</span>
            </div>
            <input
              v-model.number="amountReceived"
              type="number"
              step="0.01"
              min="0"
              placeholder="0.00"
              class="block w-full pl-10 pr-4 py-4 border border-gray-300 dark:border-gray-600 rounded-2xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 text-xl font-semibold shadow-sm transition-all duration-200"
              @focus="$event.target.select()"
            />
          </div>
          
          <!-- Cambio Mejorado -->
          <div v-if="change !== null" class="mt-4 p-4 rounded-2xl transition-all duration-300" :class="change >= 0 ? 'bg-green-50 dark:bg-green-900/20 border border-green-200' : 'bg-red-50 dark:bg-red-900/20 border border-red-200'">
            <div class="flex items-center justify-between">
              <span class="text-lg font-semibold flex items-center" :class="change >= 0 ? 'text-green-800 dark:text-green-200' : 'text-red-800 dark:text-red-200'">
                <svg v-if="change >= 0" class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <svg v-else class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                {{ change >= 0 ? 'Cambio a devolver:' : 'Falta por pagar:' }}
              </span>
              <span class="text-2xl font-bold" :class="change >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400'">
                ${{ Math.abs(change).toFixed(2) }}
              </span>
            </div>
          </div>

          <!-- Botones de Monto Rápido Mejorados -->
          <div class="mt-6">
            <p class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3 flex items-center">
              <svg class="w-4 h-4 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
              </svg>
              Accesos rápidos:
            </p>
            <div class="grid grid-cols-4 gap-2">
              <button
                @click="setExactAmount"
                class="px-3 py-3 text-sm font-semibold bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white rounded-xl transition-all duration-200 shadow-md hover:shadow-lg transform hover:-translate-y-0.5"
              >
                Exacto
              </button>
              <button
                @click="amountReceived = Math.ceil(total)"
                class="px-3 py-3 text-sm font-semibold bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white rounded-xl transition-all duration-200 shadow-md hover:shadow-lg transform hover:-translate-y-0.5"
              >
                Redondear
              </button>
              <button
                @click="amountReceived = total + 5"
                class="px-3 py-3 text-sm font-semibold bg-gradient-to-r from-purple-500 to-purple-600 hover:from-purple-600 hover:to-purple-700 text-white rounded-xl transition-all duration-200 shadow-md hover:shadow-lg transform hover:-translate-y-0.5"
              >
                +$5
              </button>
              <button
                @click="amountReceived = total + 10"
                class="px-3 py-3 text-sm font-semibold bg-gradient-to-r from-orange-500 to-orange-600 hover:from-orange-600 hover:to-orange-700 text-white rounded-xl transition-all duration-200 shadow-md hover:shadow-lg transform hover:-translate-y-0.5"
              >
                +$10
              </button>
            </div>
          </div>
        </div>

        <!-- Información adicional mejorada para otros métodos -->
        <div v-else-if="selectedMethod && selectedMethod !== 'cash'" class="mb-6 animate-slide-down">
          <div class="bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 border border-blue-200 dark:border-blue-800 rounded-2xl p-6">
            <div class="flex items-start space-x-4">
              <div class="w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center animate-pulse">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
              </div>
              <div class="flex-1">
                <h4 class="text-lg font-semibold text-blue-800 dark:text-blue-200 mb-2">Preparando pago...</h4>
                <p class="text-sm text-blue-600 dark:text-blue-300 leading-relaxed">
                  {{ getPaymentInstructions(selectedMethod) }}
                </p>
                <div class="mt-3 flex items-center text-xs text-blue-500">
                  <div class="w-2 h-2 bg-blue-500 rounded-full animate-bounce mr-2"></div>
                  <div class="w-2 h-2 bg-blue-500 rounded-full animate-bounce mr-2" style="animation-delay: 0.1s"></div>
                  <div class="w-2 h-2 bg-blue-500 rounded-full animate-bounce" style="animation-delay: 0.2s"></div>
                  <span class="ml-2">Esperando confirmación...</span>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Resumen Mejorado -->
        <div class="bg-gradient-to-r from-gray-50 to-gray-100 dark:from-gray-800 dark:to-gray-900 rounded-2xl p-6 mb-6 border border-gray-200 dark:border-gray-700">
          <h5 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
            <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
            Resumen de la transacción
          </h5>
          <div class="space-y-3">
            <div class="flex justify-between items-center text-base">
              <span class="text-gray-600 dark:text-gray-400">Subtotal:</span>
              <span class="font-semibold text-gray-900 dark:text-white">${{ (total / 1.19).toFixed(2) }}</span>
            </div>
            <div class="flex justify-between items-center text-base">
              <span class="text-gray-600 dark:text-gray-400">IVA (19%):</span>
              <span class="font-semibold text-gray-900 dark:text-white">${{ (total - (total / 1.19)).toFixed(2) }}</span>
            </div>
            <div class="border-t border-gray-300 dark:border-gray-600 pt-3">
              <div class="flex justify-between items-center text-xl font-bold">
                <span class="text-gray-900 dark:text-white">Total a pagar:</span>
                <span class="text-green-600 dark:text-green-400">${{ total.toFixed(2) }}</span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Footer Mejorado -->
      <div class="p-6 bg-gray-50 dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700">
        <div class="flex space-x-4">
          <button
            @click="$emit('close')"
            class="flex-1 px-6 py-4 bg-gradient-to-r from-gray-400 to-gray-500 hover:from-gray-500 hover:to-gray-600 text-white font-semibold rounded-2xl transition-all duration-200 shadow-md hover:shadow-lg transform hover:-translate-y-0.5 flex items-center justify-center"
          >
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
            Cancelar
          </button>
          <button
            @click="processPayment"
            :disabled="!canProcessPayment || processing"
            :class="[
              'flex-1 px-6 py-4 font-semibold rounded-2xl transition-all duration-200 shadow-md hover:shadow-lg transform flex items-center justify-center',
              canProcessPayment && !processing
                ? 'bg-gradient-to-r from-green-600 to-emerald-600 hover:from-green-700 hover:to-emerald-700 text-white hover:-translate-y-0.5'
                : 'bg-gray-300 dark:bg-gray-700 text-gray-500 dark:text-gray-400 cursor-not-allowed'
            ]"
          >
            <svg v-if="processing" class="w-5 h-5 mr-2 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
            </svg>
            <svg v-else class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            {{ processing ? 'Procesando pago...' : 'Confirmar Pago' }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>
<script setup>
import { ref, computed, watch } from 'vue'

// Props
const props = defineProps({
  total: {
    type: Number,
    required: true
  }
})

// Emits
const emit = defineEmits(['close', 'payment-completed'])

// Estado
const selectedMethod = ref('cash')
const amountReceived = ref(0)
const processing = ref(false)

// Métodos de pago
const paymentMethods = [
  {
    id: 'cash',
    name: 'Efectivo',
    description: 'Pago en efectivo',
    color: 'bg-green-500',
    icon: 'CashIcon'
  },
  {
    id: 'card',
    name: 'Tarjeta',
    description: 'Débito o crédito',
    color: 'bg-blue-500',
    icon: 'CreditCardIcon'
  },
  {
    id: 'transfer',
    name: 'Transferencia',
    description: 'Transferencia bancaria',
    color: 'bg-purple-500',
    icon: 'BankIcon'
  },
  {
    id: 'check',
    name: 'Cheque',
    description: 'Pago con cheque',
    color: 'bg-orange-500',
    icon: 'DocumentIcon'
  }
]

// Computed
const change = computed(() => {
  if (selectedMethod.value !== 'cash' || amountReceived.value === 0) return null
  return amountReceived.value - props.total
})

const canProcessPayment = computed(() => {
  if (selectedMethod.value === 'cash') {
    return amountReceived.value >= props.total
  }
  return selectedMethod.value !== null
})

// Watchers
watch(() => props.total, (newTotal) => {
  if (selectedMethod.value === 'cash') {
    amountReceived.value = newTotal
  }
}, { immediate: true })

// Métodos
const setExactAmount = () => {
  amountReceived.value = props.total
}

const getPaymentInstructions = (method) => {
  const instructions = {
    'card': 'Inserte o deslice la tarjeta en el dispositivo POS y siga las instrucciones en pantalla.',
    'transfer': 'Proporcione los datos bancarios al cliente para realizar la transferencia.',
    'check': 'Verifique que el cheque esté correctamente llenado con fecha, monto y firma.'
  }
  return instructions[method] || ''
}

const processPayment = async () => {
  if (!canProcessPayment.value) return
  
  processing.value = true
  
  // Simular procesamiento
  await new Promise(resolve => setTimeout(resolve, 2000))
  
  const paymentData = {
    method: selectedMethod.value,
    amount: props.total,
    amountReceived: selectedMethod.value === 'cash' ? amountReceived.value : props.total,
    change: selectedMethod.value === 'cash' ? change.value : 0,
    timestamp: new Date().toISOString()
  }
  
  processing.value = false
  emit('payment-completed', paymentData)
}

// Iconos como componentes simples
const CashIcon = {
  template: `
    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
    </svg>
  `
}

const CreditCardIcon = {
  template: `
    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
    </svg>
  `
}

const BankIcon = {
  template: `
    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M12 3l9 7H3l9-7z"></path>
    </svg>
  `
}

const DocumentIcon = {
  template: `
    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
    </svg>
  `
}
</script>

<style scoped>
@keyframes fadeIn {
  from {
    opacity: 0;
  }
  to {
    opacity: 1;
  }
}

@keyframes scaleIn {
  from {
    opacity: 0;
    transform: scale(0.9) translateY(20px);
  }
  to {
    opacity: 1;
    transform: scale(1) translateY(0);
  }
}

@keyframes slideDown {
  from {
    opacity: 0;
    transform: translateY(-10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.animate-fade-in {
  animation: fadeIn 0.3s ease-out;
}

.animate-scale-in {
  animation: scaleIn 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
}

.animate-slide-down {
  animation: slideDown 0.3s ease-out;
}
</style>