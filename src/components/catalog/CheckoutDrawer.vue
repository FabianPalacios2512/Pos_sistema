<template>
  <Teleport to="body">
    <Transition
      enter-active-class="transition-opacity duration-300 ease-out"
      enter-from-class="opacity-0"
      enter-to-class="opacity-100"
      leave-active-class="transition-opacity duration-200 ease-in"
      leave-from-class="opacity-100"
      leave-to-class="opacity-0"
    >
      <div 
        v-if="isOpen"
        @click="$emit('close')"
        class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm z-50"
      ></div>
    </Transition>
    
    <Transition
      enter-active-class="transition-transform duration-300 cubic-bezier(0.16, 1, 0.3, 1)"
      enter-from-class="translate-y-full md:translate-y-0 md:translate-x-full"
      enter-to-class="translate-y-0 md:translate-x-0"
      leave-active-class="transition-transform duration-300 cubic-bezier(0.16, 1, 0.3, 1)"
      leave-from-class="translate-y-0 md:translate-x-0"
      leave-to-class="translate-y-full md:translate-y-0 md:translate-x-full"
    >
      <div 
        v-if="isOpen"
        class="fixed bottom-0 right-0 w-full md:w-[450px] h-[90vh] md:h-screen bg-white z-50 flex flex-col rounded-t-3xl md:rounded-none shadow-2xl overflow-hidden"
      >
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between bg-white/80 backdrop-blur-md sticky top-0 z-10">
          <div>
            <h2 class="text-xl font-bold text-gray-900 tracking-tight">Tu Pedido</h2>
            <p class="text-xs text-gray-500 font-medium">
              {{ cart.length }} {{ cart.length === 1 ? 'producto' : 'productos' }} agregados
            </p>
          </div>
          
          <button 
            @click="$emit('close')"
            class="w-8 h-8 flex items-center justify-center rounded-full bg-gray-100 hover:bg-gray-200 text-gray-500 transition-colors"
          >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
          </button>
        </div>
        
        <div class="flex-1 overflow-y-auto scrollbar-hide">
          
          <div class="p-6 space-y-4">
            <div 
              v-for="item in cart" 
              :key="item.id"
              class="flex gap-4 p-3 bg-white border border-gray-100 rounded-2xl shadow-sm"
            >
              <div class="w-16 h-16 bg-gray-50 rounded-xl overflow-hidden flex-shrink-0 border border-gray-100">
                <img 
                  v-if="item.image" 
                  :src="item.image" 
                  :alt="item.name"
                  class="w-full h-full object-cover"
                >
                <div v-else class="w-full h-full flex items-center justify-center text-gray-300">
                  <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                </div>
              </div>
              
              <div class="flex-1 flex flex-col justify-center">
                <h3 class="text-sm font-bold text-gray-800 line-clamp-1">{{ item.name }}</h3>
                <p class="text-xs text-gray-500 mb-1">
                  {{ item.quantity }} x ${{ formatPrice(item.price) }}
                </p>
                <p class="text-sm font-bold text-green-600">
                  ${{ formatPrice(item.price * item.quantity) }}
                </p>
              </div>
            </div>
          </div>
          
          <div class="h-2 bg-gray-50 border-y border-gray-100"></div>

          <div class="p-6 space-y-6">
            <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wider text-opacity-80">
              Datos de Envío
            </h3>
            
            <div class="space-y-1">
              <label class="text-xs font-bold text-gray-600 ml-1">Tu Nombre</label>
              <input 
                v-model="form.customer_name"
                type="text"
                placeholder="Ej: Juan Pérez"
                class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-green-500 focus:border-transparent outline-none transition-all placeholder-gray-400"
                :class="{ 'border-red-300 bg-red-50': errors.customer_name }"
              >
              <p v-if="errors.customer_name" class="text-red-500 text-[10px] ml-1">{{ errors.customer_name }}</p>
            </div>
            
            <div class="space-y-1">
              <label class="text-xs font-bold text-gray-600 ml-1">WhatsApp / Teléfono</label>
              <input 
                v-model="form.customer_phone"
                type="tel"
                placeholder="Ej: 300 123 4567"
                class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-green-500 focus:border-transparent outline-none transition-all placeholder-gray-400"
                :class="{ 'border-red-300 bg-red-50': errors.customer_phone }"
              >
              <p v-if="errors.customer_phone" class="text-red-500 text-[10px] ml-1">{{ errors.customer_phone }}</p>
            </div>

            <div class="space-y-1">
              <label class="text-xs font-bold text-gray-600 ml-1">Cédula / Documento</label>
              <input 
                v-model="form.customer_document"
                type="text"
                placeholder="Ej: 1234567890"
                maxlength="15"
                class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-green-500 focus:border-transparent outline-none transition-all placeholder-gray-400"
                :class="{ 'border-red-300 bg-red-50': errors.customer_document }"
              >
              <p v-if="errors.customer_document" class="text-red-500 text-[10px] ml-1">{{ errors.customer_document }}</p>
              <p class="text-[10px] text-gray-500 ml-1">Se usa para identificar tu pedido en el sistema</p>
            </div>
            
            <div class="space-y-2">
              <label class="text-xs font-bold text-gray-600 ml-1">Método de Entrega</label>
              <div class="grid grid-cols-2 gap-3">
                <button 
                  @click="form.delivery_type = 'pickup'"
                  class="relative p-4 rounded-xl border transition-all text-left group"
                  :class="form.delivery_type === 'pickup' 
                    ? 'border-green-500 bg-green-50/50 ring-1 ring-green-500' 
                    : 'border-gray-200 hover:border-gray-300 bg-white'"
                >
                  <div class="flex items-center gap-2 mb-1">
                    <svg class="w-5 h-5" :class="form.delivery_type === 'pickup' ? 'text-green-600' : 'text-gray-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                    <span class="text-sm font-bold" :class="form.delivery_type === 'pickup' ? 'text-green-700' : 'text-gray-700'">Recoger</span>
                  </div>
                  <p class="text-[10px] text-gray-500 leading-tight">Pasa por la tienda y recoge tu pedido listo.</p>
                </button>
                
                <button 
                  @click="form.delivery_type = 'delivery'"
                  class="relative p-4 rounded-xl border transition-all text-left group"
                  :class="form.delivery_type === 'delivery' 
                    ? 'border-green-500 bg-green-50/50 ring-1 ring-green-500' 
                    : 'border-gray-200 hover:border-gray-300 bg-white'"
                >
                  <div class="flex items-center gap-2 mb-1">
                    <svg class="w-5 h-5" :class="form.delivery_type === 'delivery' ? 'text-green-600' : 'text-gray-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                    <span class="text-sm font-bold" :class="form.delivery_type === 'delivery' ? 'text-green-700' : 'text-gray-700'">Domicilio</span>
                  </div>
                  <p class="text-[10px] text-gray-500 leading-tight">Te lo llevamos a la puerta de tu casa.</p>
                </button>
              </div>
            </div>
            
            <div v-if="form.delivery_type === 'delivery'" class="space-y-1 animate-fadeIn">
              <label class="text-xs font-bold text-gray-600 ml-1">Dirección Completa</label>
              <textarea 
                v-model="form.customer_address"
                rows="2"
                placeholder="Calle, Barrio, Edificio..."
                class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-green-500 focus:border-transparent outline-none transition-all resize-none placeholder-gray-400"
                :class="{ 'border-red-300 bg-red-50': errors.customer_address }"
              ></textarea>
              <p v-if="errors.customer_address" class="text-red-500 text-[10px] ml-1">{{ errors.customer_address }}</p>
            </div>

            <div class="space-y-1">
              <label class="text-xs font-bold text-gray-600 ml-1">Comentarios (Opcional)</label>
              <textarea 
                v-model="form.note"
                rows="1"
                placeholder="¿Algo más que debamos saber?"
                class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-green-500 focus:border-transparent outline-none transition-all resize-none placeholder-gray-400"
              ></textarea>
            </div>
          </div>
        </div>
        
        <div class="border-t border-gray-100 p-6 bg-white safe-bottom">
          <div class="flex justify-between items-center mb-4">
            <span class="text-gray-500 font-medium">Total a Pagar</span>
            <span class="text-2xl font-black text-gray-900 tracking-tight">${{ formatPrice(total) }}</span>
          </div>
          
          <button 
            @click="submitOrder"
            :disabled="isSubmitting || cart.length === 0"
            class="w-full py-4 bg-[#25D366] hover:bg-[#20bd5a] text-white rounded-xl font-bold text-lg shadow-lg shadow-green-200 transition-all transform active:scale-95 flex items-center justify-center gap-3 disabled:opacity-50 disabled:cursor-not-allowed disabled:shadow-none"
          >
            <svg v-if="!isSubmitting" class="w-6 h-6 fill-current" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/></svg>
            <span>{{ isSubmitting ? 'Enviando...' : 'Enviar Pedido por WhatsApp' }}</span>
          </button>
        </div>
      </div>
    </Transition>
  </Teleport>
</template>

<script setup>
import { ref, computed, watch } from 'vue'

const props = defineProps({
  isOpen: { type: Boolean, default: false },
  cart: { type: Array, default: () => [] }
})

const emit = defineEmits(['close', 'order-submitted'])

const form = ref({
  customer_name: '',
  customer_phone: '',
  customer_document: '',
  customer_address: '',
  delivery_type: 'pickup',
  note: ''
})

const errors = ref({})
const isSubmitting = ref(false)

const total = computed(() => {
  return props.cart.reduce((sum, item) => sum + (item.price * item.quantity), 0)
})

const formatPrice = (price) => {
  return new Intl.NumberFormat('es-CO', {
    minimumFractionDigits: 0,
    maximumFractionDigits: 0
  }).format(price)
}

const validateForm = () => {
  errors.value = {}
  
  if (!form.value.customer_name.trim()) errors.value.customer_name = 'Requerido'
  if (!form.value.customer_phone.trim()) errors.value.customer_phone = 'Requerido'
  if (!form.value.customer_document.trim()) {
    errors.value.customer_document = 'Cédula requerida'
  } else if (form.value.customer_document.trim().length < 6) {
    errors.value.customer_document = 'Mínimo 6 caracteres'
  }
  
  if (form.value.delivery_type === 'delivery' && !form.value.customer_address.trim()) {
    errors.value.customer_address = 'Dirección requerida para domicilios'
  }
  
  return Object.keys(errors.value).length === 0
}

const submitOrder = async () => {
  if (!validateForm()) return
  
  isSubmitting.value = true
  
  // Construir el pedido
  const orderData = {
    ...form.value,
    items: props.cart.map(item => ({
      product_id: item.id,
      quantity: item.quantity,
      price: item.price,
      name: item.name
    })),
    total: total.value
  }
  
  // Emitimos el evento para que el padre lo maneje
  emit('order-submitted', orderData)
}

// Reset al cerrar
watch(() => props.isOpen, (newVal) => {
  if (!newVal) isSubmitting.value = false
})

defineExpose({
  resetSubmitting: () => isSubmitting.value = false
})
</script>

<style scoped>
/* Ocultar scrollbar pero permitir scroll */
.scrollbar-hide::-webkit-scrollbar { display: none; }
.scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }

/* Animación simple */
@keyframes fadeIn {
  from { opacity: 0; transform: translateY(-5px); }
  to { opacity: 1; transform: translateY(0); }
}
.animate-fadeIn { animation: fadeIn 0.3s ease-out; }
</style>