<template>
  <!-- PANEL DE PEDIDO - Diseño Profesional Premium -->
  <div class="flex flex-col overflow-hidden rounded-2xl border shadow-lg dark:shadow-xl dark:shadow-black/40 transition-all duration-300"
       :class="cartItems.length > 0 
         ? 'bg-white dark:bg-zinc-900 border-gray-200 dark:border-zinc-700' 
         : 'bg-gray-50 dark:bg-zinc-900/80 border-gray-100 dark:border-zinc-800'"
       style="height: 100%; max-height: 100%;">
    
    <!-- HEADER -->
    <div class="flex-shrink-0 px-4 pt-4 pb-3 border-b"
         :class="cartItems.length > 0 
           ? 'bg-white dark:bg-zinc-900 border-gray-100 dark:border-zinc-800' 
           : 'bg-gray-50 dark:bg-zinc-900/80 border-gray-100 dark:border-zinc-800/50'">
      
      <!-- Título del pedido -->
      <div class="flex items-center justify-between mb-3">
        <div class="flex items-center gap-2.5">
          <div class="w-8 h-8 rounded-lg flex items-center justify-center"
               :class="cartItems.length > 0 
                 ? 'bg-orange-100 dark:bg-orange-900/30' 
                 : 'bg-gray-100 dark:bg-zinc-800'">
            <svg class="w-4 h-4" :class="cartItems.length > 0 ? 'text-orange-500 dark:text-orange-400' : 'text-gray-400 dark:text-zinc-500'" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
          </div>
          <div>
            <h2 class="text-sm font-bold text-gray-900 dark:text-white leading-none">Pedido Actual</h2>
            <p v-if="cartItems.length > 0" class="text-xs text-orange-500 dark:text-orange-400 font-medium mt-0.5">{{ totalItemsCount }} {{ totalItemsCount === 1 ? 'producto' : 'productos' }}</p>
            <p v-else class="text-xs text-gray-400 dark:text-zinc-500 mt-0.5">Vacío</p>
          </div>
        </div>
      </div>
      
      <!-- Botones de acción -->
      <div class="flex gap-2">
        <!-- Botón Cliente -->
        <button
          @click="$emit('show-customer-selector')"
          class="flex-1 flex items-center gap-2 px-3 py-2 rounded-lg transition-all duration-150 border"
          :class="selectedCustomer 
            ? 'bg-orange-50 dark:bg-orange-900/20 border-orange-200 dark:border-orange-800/50' 
            : 'bg-white dark:bg-zinc-800 border-gray-200 dark:border-zinc-700 hover:border-gray-300 dark:hover:border-zinc-600'"
        >
          <div class="w-7 h-7 rounded-md flex items-center justify-center flex-shrink-0"
               :class="selectedCustomer ? 'bg-orange-100 dark:bg-orange-900/40' : 'bg-gray-100 dark:bg-zinc-700'">
            <svg class="w-3.5 h-3.5" :class="selectedCustomer ? 'text-orange-500 dark:text-orange-400' : 'text-gray-400 dark:text-zinc-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
              <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
            </svg>
          </div>
          <div class="flex-1 min-w-0 text-left">
            <span v-if="selectedCustomer" class="text-xs font-semibold text-orange-600 dark:text-orange-300 truncate block">{{ selectedCustomer.name }}</span>
            <span v-else class="text-xs font-medium text-gray-500 dark:text-zinc-400">Cliente</span>
          </div>
        </button>
        
        <!-- Botón Descuento -->
        <button
          @click="$emit('toggle-promo-input')"
          class="flex items-center gap-1.5 px-3 py-2 rounded-lg transition-all duration-150 border"
          :class="discount > 0 
            ? 'bg-emerald-50 dark:bg-emerald-900/20 border-emerald-200 dark:border-emerald-800/50 text-emerald-600 dark:text-emerald-400' 
            : (showPromoInput 
                ? 'bg-orange-50 dark:bg-orange-900/20 border-orange-200 dark:border-orange-800/50 text-orange-500 dark:text-orange-400'
                : 'bg-white dark:bg-zinc-800 border-gray-200 dark:border-zinc-700 text-gray-400 dark:text-zinc-400 hover:border-gray-300 dark:hover:border-zinc-600 hover:text-gray-600')"
        >
          <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9.568 3H5.25A2.25 2.25 0 003 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 005.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 009.568 3z" />
          </svg>
          <span class="text-xs font-semibold">{{ discount > 0 ? '-$' + discount.toLocaleString() : '%' }}</span>
        </button>
      </div>

      <!-- Input Cupón -->
      <div v-if="showPromoInput" class="mt-2.5 animate-fade-in">
        <div class="flex gap-2">
          <input
            :value="promoCode"
            @input="$emit('update:promoCode', $event.target.value)"
            type="text"
            placeholder="Código de descuento"
            class="flex-1 px-3 py-2 text-xs bg-white dark:bg-zinc-800 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-zinc-500 rounded-lg border border-gray-200 dark:border-zinc-700 focus:ring-2 focus:ring-orange-500/20 focus:border-orange-400 outline-none transition-all"
            @keyup.enter="$emit('apply-promo')"
          />
          <button
            @click="$emit('apply-promo')"
            :disabled="!promoCode?.trim()"
            class="px-4 py-2 bg-orange-500 hover:bg-orange-600 text-white text-xs font-semibold rounded-lg disabled:opacity-40 disabled:cursor-not-allowed transition-colors"
          >Aplicar</button>
        </div>
      </div>
      
      <!-- Descuento Aplicado -->
      <div v-if="discount > 0 && !showPromoInput" class="mt-2 flex items-center gap-1.5 text-xs text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-900/20 px-2.5 py-1.5 rounded-lg">
        <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
        <span class="font-semibold">Descuento aplicado: -${{ discount.toLocaleString() }}</span>
      </div>
    </div>

    <!-- LISTA DE ITEMS -->
    <div class="flex-1 overflow-y-auto relative" 
         :class="cartItems.length > 0 ? 'bg-gray-50/50 dark:bg-zinc-900/50' : 'bg-gray-50 dark:bg-zinc-900/30'" 
         style="scrollbar-width: thin;">
      
      <!-- Carrito Vacío -->
      <div v-if="cartItems.length === 0" class="absolute inset-0 flex flex-col items-center justify-center text-center pointer-events-none px-8">
        <div class="w-14 h-14 rounded-xl bg-gray-100 dark:bg-zinc-800 flex items-center justify-center mb-3">
          <svg class="w-6 h-6 text-gray-300 dark:text-zinc-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993l1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 01-1.12-1.243l1.264-12A1.125 1.125 0 015.513 7.5h12.974c.576 0 1.059.435 1.119 1.007zM8.625 10.5a.375.375 0 11-.75 0 .375.375 0 01.75 0zm7.5 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
          </svg>
        </div>
        <p class="text-sm font-medium text-gray-400 dark:text-zinc-500">Sin productos</p>
        <p class="text-xs text-gray-300 dark:text-zinc-600 mt-1">Selecciona productos del menú</p>
      </div>

      <!-- Lista de Items -->
      <div v-else class="p-3 space-y-1.5">
        <div
          v-for="item in cartItems"
          :key="item.id"
          class="flex items-center gap-3 py-2.5 px-3 bg-white dark:bg-zinc-800/80 rounded-xl border border-gray-100 dark:border-zinc-700/50 hover:border-gray-200 dark:hover:border-zinc-600 transition-colors"
        >
          <!-- Imagen pequeña -->
          <div class="w-9 h-9 rounded-lg bg-gray-100 dark:bg-zinc-700 overflow-hidden flex-shrink-0 flex items-center justify-center">
            <img 
              v-if="isRealImage(item.image_url)"
              :src="item.image_url" 
              :alt="item.name"
              class="w-full h-full object-cover"
            />
            <svg v-else class="w-4 h-4 text-gray-300 dark:text-zinc-500" viewBox="0 0 24 24" fill="currentColor">
              <path d="M8.1 13.34l2.83-2.83L3.91 3.5c-1.56 1.56-1.56 4.09 0 5.66l4.19 4.18zm6.78-1.81c1.53.71 3.68.21 5.27-1.38 1.91-1.91 2.28-4.65.81-6.12-1.46-1.46-4.20-1.10-6.12.81-1.59 1.59-2.09 3.74-1.38 5.27L3.7 19.87l1.41 1.41L12 14.41l6.88 6.88 1.41-1.41L13.41 13l1.47-1.47z"/>
            </svg>
          </div>

          <!-- Nombre y precio unitario -->
          <div class="flex-1 min-w-0">
            <p class="font-medium text-gray-800 dark:text-zinc-100 truncate text-sm leading-tight">
              {{ item.name.split(' (')[0] }}
            </p>
            <p class="text-xs text-gray-400 dark:text-zinc-500 mt-0.5">${{ item.price.toLocaleString() }} c/u</p>
          </div>

          <!-- Controles cantidad -->
          <div class="flex items-center bg-gray-50 dark:bg-zinc-700/50 rounded-lg border border-gray-200 dark:border-zinc-600">
            <button 
              @click.stop="$emit('update-quantity', item.id, item.quantity - 1)" 
              class="w-8 h-8 flex items-center justify-center text-gray-500 dark:text-zinc-400 hover:text-orange-500 dark:hover:text-orange-400 hover:bg-white dark:hover:bg-zinc-700 rounded-l-lg transition-colors"
            >
              <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 12h-15"/></svg>
            </button>
            <span class="w-8 text-center font-bold text-gray-800 dark:text-zinc-100 text-sm tabular-nums">
              {{ item.quantity }}
            </span>
            <button 
              @click.stop="$emit('update-quantity', item.id, item.quantity + 1)" 
              class="w-8 h-8 flex items-center justify-center text-gray-500 dark:text-zinc-400 hover:text-orange-500 dark:hover:text-orange-400 hover:bg-white dark:hover:bg-zinc-700 rounded-r-lg transition-colors"
            >
              <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
            </button>
          </div>

          <!-- Precio total item -->
          <span class="font-bold text-gray-900 dark:text-white text-sm w-[72px] tabular-nums text-right">
            ${{ (item.price * item.quantity).toLocaleString() }}
          </span>
        </div>
      </div>
    </div>
    
    <!-- SECCION DE PAGO -->
    <div class="flex-shrink-0 border-t"
         :class="cartItems.length > 0 
           ? 'bg-white dark:bg-zinc-900 border-gray-100 dark:border-zinc-800' 
           : 'bg-gray-50 dark:bg-zinc-900/80 border-gray-100 dark:border-zinc-800/50'">
      <div class="p-4">
        
        <!-- Métodos de pago -->
        <div class="flex gap-1.5 mb-4">
          <!-- Efectivo -->
          <button 
            @click="$emit('select-payment-method', 'efectivo')"
            :disabled="!canPay"
            class="flex-1 flex items-center justify-center gap-1.5 py-2 px-2 rounded-lg transition-all duration-150 border text-xs font-semibold"
            :class="canPay 
              ? (selectedPaymentMethod === 'efectivo'
                  ? 'bg-orange-50 dark:bg-orange-900/30 text-orange-600 dark:text-orange-400 border-orange-200 dark:border-orange-700/50'
                  : 'bg-white dark:bg-zinc-800 text-gray-500 dark:text-zinc-400 border-gray-200 dark:border-zinc-700 hover:border-gray-300 dark:hover:border-zinc-600')
              : 'bg-gray-50 dark:bg-zinc-800/50 text-gray-300 dark:text-zinc-600 border-gray-100 dark:border-zinc-800 cursor-not-allowed'"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
            <span class="hidden sm:inline">Efectivo</span>
          </button>
          
          <!-- Crédito - Ahora al lado de Efectivo -->
          <button 
            v-if="creditEnabled"
            @click="$emit('select-payment-method', 'credit')"
            :disabled="!canPay"
            class="flex-1 flex items-center justify-center gap-1.5 py-2 px-2 rounded-lg transition-all duration-150 border text-xs font-semibold"
            :class="canPay 
              ? (selectedPaymentMethod === 'credit'
                  ? 'bg-orange-50 dark:bg-orange-900/30 text-orange-600 dark:text-orange-400 border-orange-200 dark:border-orange-700/50'
                  : 'bg-white dark:bg-zinc-800 text-gray-500 dark:text-zinc-400 border-gray-200 dark:border-zinc-700 hover:border-gray-300 dark:hover:border-zinc-600')
              : 'bg-gray-50 dark:bg-zinc-800/50 text-gray-300 dark:text-zinc-600 border-gray-100 dark:border-zinc-800 cursor-not-allowed'"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <span class="hidden sm:inline">Crédito</span>
          </button>
          
          <!-- Tarjeta -->
          <button 
            @click="$emit('select-payment-method', 'tarjeta')"
            :disabled="!canPay"
            class="flex-1 flex items-center justify-center gap-1.5 py-2 px-2 rounded-lg transition-all duration-150 border text-xs font-semibold"
            :class="canPay 
              ? (selectedPaymentMethod === 'tarjeta'
                  ? 'bg-orange-50 dark:bg-orange-900/30 text-orange-600 dark:text-orange-400 border-orange-200 dark:border-orange-700/50'
                  : 'bg-white dark:bg-zinc-800 text-gray-500 dark:text-zinc-400 border-gray-200 dark:border-zinc-700 hover:border-gray-300 dark:hover:border-zinc-600')
              : 'bg-gray-50 dark:bg-zinc-800/50 text-gray-300 dark:text-zinc-600 border-gray-100 dark:border-zinc-800 cursor-not-allowed'"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
            <span class="hidden sm:inline">Tarjeta</span>
          </button>
          
          <!-- Transferencia -->
          <button 
            @click="$emit('select-payment-method', 'transferencia')"
            :disabled="!canPay"
            class="flex-1 flex items-center justify-center gap-1.5 py-2 px-2 rounded-lg transition-all duration-150 border text-xs font-semibold"
            :class="canPay 
              ? (selectedPaymentMethod === 'transferencia'
                  ? 'bg-orange-50 dark:bg-orange-900/30 text-orange-600 dark:text-orange-400 border-orange-200 dark:border-orange-700/50'
                  : 'bg-white dark:bg-zinc-800 text-gray-500 dark:text-zinc-400 border-gray-200 dark:border-zinc-700 hover:border-gray-300 dark:hover:border-zinc-600')
              : 'bg-gray-50 dark:bg-zinc-800/50 text-gray-300 dark:text-zinc-600 border-gray-100 dark:border-zinc-800 cursor-not-allowed'"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
            <span class="hidden sm:inline">Transfer</span>
          </button>
        </div>
        
        <!-- Botón COBRAR - Principal -->
        <button
          @click="$emit('checkout')"
          :disabled="!canPay"
          class="w-full py-4 rounded-2xl font-bold text-base transition-all duration-200 flex items-center justify-center gap-3"
          :class="canPay
            ? 'bg-orange-500 hover:bg-orange-600 active:bg-orange-700 text-white shadow-lg shadow-orange-500/25 hover:shadow-xl hover:shadow-orange-500/30 active:scale-[0.98]'
            : 'bg-gray-100 dark:bg-zinc-800 text-gray-400 dark:text-zinc-500 cursor-not-allowed'"
        >
          <svg v-if="canPay" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
          </svg>
          <span>{{ canPay ? 'Cobrar · $' + total.toLocaleString() : 'Agregar productos' }}</span>
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { defineProps, defineEmits, computed } from 'vue'

const props = defineProps({
  cartItems: {
    type: Array,
    default: () => []
  },
  selectedCustomer: {
    type: Object,
    default: null
  },
  discount: {
    type: Number,
    default: 0
  },
  total: {
    type: Number,
    default: 0
  },
  showPromoInput: {
    type: Boolean,
    default: false
  },
  promoCode: {
    type: String,
    default: ''
  },
  selectedPaymentMethod: {
    type: String,
    default: 'efectivo'
  },
  canPay: {
    type: Boolean,
    default: false
  },
  creditEnabled: {
    type: Boolean,
    default: false
  }
})

defineEmits([
  'show-customer-selector',
  'toggle-promo-input',
  'update:promoCode',
  'apply-promo',
  'update-quantity',
  'select-payment-method',
  'checkout'
])

// Computed: Total de items (suma de cantidades)
const totalItemsCount = computed(() => {
  return props.cartItems.reduce((sum, item) => sum + item.quantity, 0)
})

// Validar si es una imagen real
const isRealImage = (imageUrl) => {
  if (!imageUrl || typeof imageUrl !== 'string') return false
  const url = imageUrl.trim()
  if (!url) return false
  if (url.startsWith('data:image/svg+xml')) return false
  if (url.includes('placeholder') || url.includes('default') || url.includes('no-image')) return false
  return true
}
</script>
