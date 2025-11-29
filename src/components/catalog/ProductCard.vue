<template>
  <div class="group relative bg-white border border-slate-100 rounded-2xl overflow-hidden hover:shadow-xl transition-all duration-300 hover:-translate-y-1 flex flex-col h-full">
    
    <!-- 📸 Image Container -->
    <div class="relative aspect-square overflow-hidden bg-slate-50">
      <img 
        v-if="product.image" 
        :src="product.image" 
        :alt="product.name"
        class="w-full h-full object-cover object-center transition-transform duration-700 group-hover:scale-110"
      >
      <div v-else class="w-full h-full flex items-center justify-center bg-slate-100">
        <svg class="w-12 h-12 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
        </svg>
      </div>

      <!-- Badges (Compact) -->
      <div class="absolute top-2 left-2 flex flex-col gap-1">
        <span v-if="product.stock <= 5 && product.stock > 0" class="px-2 py-0.5 bg-orange-500 text-white text-[10px] font-bold uppercase rounded-md shadow-sm">
          ¡Quedan {{ product.stock }}!
        </span>
        <span v-if="product.category" class="px-2 py-0.5 bg-white/90 backdrop-blur-sm text-slate-700 text-[10px] font-bold uppercase rounded-md border border-slate-200">
          {{ product.category }}
        </span>
      </div>

      <!-- Quick Add (Desktop) -->
      <div class="absolute inset-x-0 bottom-0 p-3 z-20 translate-y-full group-hover:translate-y-0 transition-transform duration-300 hidden md:block">
        <button 
          v-if="quantity === 0 && product.stock > 0"
          @click.stop="addToCart"
          class="w-full py-2.5 bg-slate-900 hover:bg-slate-800 text-white font-bold rounded-xl shadow-lg transition-colors flex items-center justify-center gap-2 text-sm"
        >
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
          Agregar
        </button>
      </div>
    </div>

    <!-- 📝 Content (Compact) -->
    <div class="p-3 md:p-4 flex flex-col flex-1">
      <div class="mb-2 flex-1">
        <h3 class="text-sm md:text-base font-bold text-slate-900 mb-1 leading-tight line-clamp-2">
          {{ product.name }}
        </h3>
        <p class="hidden md:block text-xs text-slate-500 line-clamp-2">
          {{ product.description || 'Calidad garantizada.' }}
        </p>
      </div>

      <!-- Price & Mobile Actions -->
      <div class="flex items-end justify-between gap-2 mt-auto pt-2 border-t border-slate-50">
        <div>
          <span class="text-lg md:text-xl font-black text-slate-900">${{ formatPrice(product.price) }}</span>
        </div>

        <!-- Mobile Add / Quantity -->
        <div class="flex-shrink-0">
          <!-- Mobile Add Button -->
          <button 
            v-if="quantity === 0 && product.stock > 0"
            @click="addToCart"
            class="md:hidden w-8 h-8 bg-lime-500 text-slate-900 rounded-lg flex items-center justify-center shadow-md active:scale-95 transition-transform"
          >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
            </svg>
          </button>

          <!-- Quantity Control -->
          <div v-else-if="quantity > 0" class="flex items-center bg-slate-100 rounded-lg p-0.5">
            <button 
              @click="decreaseQuantity"
              class="w-7 h-7 flex items-center justify-center bg-white rounded-md text-slate-600 shadow-sm"
            >
              -
            </button>
            <span class="w-6 text-center font-bold text-slate-900 text-xs">{{ quantity }}</span>
            <button 
              @click="increaseQuantity"
              :disabled="quantity >= product.stock"
              class="w-7 h-7 flex items-center justify-center bg-white rounded-md text-slate-600 shadow-sm"
            >
              +
            </button>
          </div>
          
          <!-- Sold Out -->
          <div v-if="product.stock === 0" class="px-2 py-1 bg-red-50 text-red-500 text-[10px] font-bold rounded uppercase">
            Agotado
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, watch } from 'vue'

const props = defineProps({
  product: {
    type: Object,
    required: true
  },
  initialQuantity: {
    type: Number,
    default: 0
  }
})

const emit = defineEmits(['update-quantity'])

const quantity = ref(props.initialQuantity)

watch(() => props.initialQuantity, (newVal) => {
  quantity.value = newVal
})

const formatPrice = (price) => {
  return new Intl.NumberFormat('es-CO', {
    minimumFractionDigits: 0,
    maximumFractionDigits: 0
  }).format(price)
}

const addToCart = () => {
  quantity.value = 1
  emit('update-quantity', {
    productId: props.product.id,
    quantity: quantity.value
  })
}

const increaseQuantity = () => {
  if (quantity.value < props.product.stock) {
    quantity.value++
    emit('update-quantity', {
      productId: props.product.id,
      quantity: quantity.value
    })
  }
}

const decreaseQuantity = () => {
  if (quantity.value > 0) {
    quantity.value--
    emit('update-quantity', {
      productId: props.product.id,
      quantity: quantity.value
    })
  }
}
</script>