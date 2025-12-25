<template>
  <Teleport to="body">
    <!-- Overlay -->
    <Transition name="fade">
      <div 
        v-if="show" 
        class="fixed inset-0 bg-black/50 backdrop-blur-sm z-[110]" 
        @click="$emit('close')"
      ></div>
    </Transition>
    
    <!-- Modal de Formulario -->
    <Transition name="scale-modal">
      <div 
        v-if="show" 
        class="fixed inset-0 z-[120] flex items-center justify-center p-4 overflow-y-auto"
      >
        <div class="bg-white dark:bg-zinc-900 rounded-3xl shadow-2xl max-w-lg w-full my-8 max-h-[90vh] overflow-y-auto">
          <!-- Header -->
          <div class="sticky top-0 bg-gradient-to-r from-slate-900 to-slate-700 dark:from-zinc-800 dark:to-zinc-900 p-6 rounded-t-3xl z-10">
            <div class="flex items-center justify-between">
              <div>
                <h3 class="text-2xl font-black text-white mb-1">Completa tu Pedido</h3>
                <p class="text-sm text-white/70">Necesitamos algunos datos para procesar tu orden</p>
              </div>
              <button 
                @click="$emit('close')" 
                class="w-10 h-10 rounded-xl hover:bg-white/10 active:bg-white/20 flex items-center justify-center text-white transition-all"
              >
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" />
                </svg>
              </button>
            </div>
          </div>

          <!-- Form -->
          <div class="p-6 space-y-5">
            <!-- Nombre -->
            <div>
              <label class="block text-sm font-bold text-gray-700 dark:text-zinc-300 mb-2">
                Nombre Completo <span class="text-red-500">*</span>
              </label>
              <input 
                v-model="formData.customer_name"
                type="text"
                placeholder="Juan Pérez"
                class="w-full px-4 py-3 border-2 border-gray-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-gray-900 dark:text-white rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
                :class="{ 'border-red-500': errors.customer_name }"
              />
              <p v-if="errors.customer_name" class="text-red-500 text-xs mt-1 font-medium">{{ errors.customer_name }}</p>
            </div>

            <!-- Teléfono -->
            <div>
              <label class="block text-sm font-bold text-gray-700 dark:text-zinc-300 mb-2">
                Teléfono <span class="text-red-500">*</span>
              </label>
              <input 
                v-model="formData.customer_phone"
                type="tel"
                placeholder="3001234567"
                class="w-full px-4 py-3 border-2 border-gray-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-gray-900 dark:text-white rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
                :class="{ 'border-red-500': errors.customer_phone }"
              />
              <p v-if="errors.customer_phone" class="text-red-500 text-xs mt-1 font-medium">{{ errors.customer_phone }}</p>
            </div>

            <!-- Documento -->
            <div>
              <label class="block text-sm font-bold text-gray-700 dark:text-zinc-300 mb-2">
                Cédula / Documento <span class="text-red-500">*</span>
              </label>
              <input 
                v-model="formData.customer_document"
                type="text"
                placeholder="1234567890"
                class="w-full px-4 py-3 border-2 border-gray-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-gray-900 dark:text-white rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
                :class="{ 'border-red-500': errors.customer_document }"
              />
              <p v-if="errors.customer_document" class="text-red-500 text-xs mt-1 font-medium">{{ errors.customer_document }}</p>
            </div>

            <!-- Email (Opcional) -->
            <div>
              <label class="block text-sm font-bold text-gray-700 dark:text-zinc-300 mb-2">
                Correo Electrónico <span class="text-xs text-gray-500">(Opcional)</span>
              </label>
              <input 
                v-model="formData.customer_email"
                type="email"
                placeholder="correo@ejemplo.com"
                class="w-full px-4 py-3 border-2 border-gray-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-gray-900 dark:text-white rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
              />
            </div>

            <!-- Tipo de Entrega -->
            <div>
              <label class="block text-sm font-bold text-gray-700 dark:text-zinc-300 mb-3">
                Método de Entrega <span class="text-red-500">*</span>
              </label>
              <div class="grid grid-cols-2 gap-3">
                <button
                  type="button"
                  @click="formData.delivery_type = 'delivery'"
                  class="p-4 rounded-2xl border-2 transition-all"
                  :class="formData.delivery_type === 'delivery' 
                    ? 'border-blue-500 bg-blue-50 dark:bg-blue-950/30' 
                    : 'border-gray-200 dark:border-zinc-700 hover:border-gray-300 dark:hover:border-zinc-600'"
                >
                  <svg class="w-8 h-8 mx-auto mb-2" :class="formData.delivery_type === 'delivery' ? 'text-blue-600 dark:text-blue-400' : 'text-gray-400 dark:text-zinc-500'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0" />
                  </svg>
                  <p class="text-sm font-bold" :class="formData.delivery_type === 'delivery' ? 'text-blue-900 dark:text-blue-300' : 'text-gray-700 dark:text-zinc-300'">Envío a Domicilio</p>
                </button>

                <button
                  type="button"
                  @click="formData.delivery_type = 'pickup'"
                  class="p-4 rounded-2xl border-2 transition-all"
                  :class="formData.delivery_type === 'pickup' 
                    ? 'border-emerald-500 bg-emerald-50 dark:bg-emerald-950/30' 
                    : 'border-gray-200 dark:border-zinc-700 hover:border-gray-300 dark:hover:border-zinc-600'"
                >
                  <svg class="w-8 h-8 mx-auto mb-2" :class="formData.delivery_type === 'pickup' ? 'text-emerald-600 dark:text-emerald-400' : 'text-gray-400 dark:text-zinc-500'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                  </svg>
                  <p class="text-sm font-bold" :class="formData.delivery_type === 'pickup' ? 'text-emerald-900 dark:text-emerald-300' : 'text-gray-700 dark:text-zinc-300'">Recoger en Tienda</p>
                </button>
              </div>
              <p v-if="errors.delivery_type" class="text-red-500 text-xs mt-2 font-medium">{{ errors.delivery_type }}</p>
            </div>

            <!-- Dirección (si es delivery) -->
            <div v-if="formData.delivery_type === 'delivery'">
              <label class="block text-sm font-bold text-gray-700 dark:text-zinc-300 mb-2">
                Dirección de Entrega
              </label>
              <textarea 
                v-model="formData.customer_address"
                rows="2"
                placeholder="Calle 123 #45-67, Barrio, Ciudad"
                class="w-full px-4 py-3 border-2 border-gray-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-gray-900 dark:text-white rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all resize-none"
              ></textarea>
            </div>

            <!-- Nota -->
            <div>
              <label class="block text-sm font-bold text-gray-700 dark:text-zinc-300 mb-2">
                Nota o Instrucciones Especiales <span class="text-xs text-gray-500">(Opcional)</span>
              </label>
              <textarea 
                v-model="formData.note"
                rows="2"
                placeholder="Alguna indicación especial para tu pedido..."
                class="w-full px-4 py-3 border-2 border-gray-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-gray-900 dark:text-white rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all resize-none"
              ></textarea>
            </div>
          </div>

          <!-- Footer -->
          <div class="p-6 bg-gray-50 dark:bg-zinc-800/50 rounded-b-3xl border-t border-gray-200 dark:border-zinc-700">
            <button
              @click="handleSubmit"
              :disabled="submitting"
              class="w-full bg-gradient-to-r from-slate-900 to-slate-700 hover:from-slate-800 hover:to-slate-600 disabled:from-gray-300 disabled:to-gray-400 text-white py-4 rounded-2xl font-black text-base flex items-center justify-center gap-3 shadow-xl transition-all disabled:cursor-not-allowed active:scale-[0.98]"
            >
              <svg v-if="!submitting" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
              <svg v-else class="w-5 h-5 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
              </svg>
              <span>{{ submitting ? 'Procesando...' : 'Confirmar Pedido' }}</span>
            </button>
          </div>
        </div>
      </div>
    </Transition>
  </Teleport>
</template>

<script setup>
import { ref, watch } from 'vue'

const props = defineProps({
  show: Boolean,
  cartItems: Array,
})

const emit = defineEmits(['close', 'submit'])

const formData = ref({
  customer_name: '',
  customer_phone: '',
  customer_document: '',
  customer_email: '',
  customer_address: '',
  delivery_type: 'delivery',
  note: '',
})

const errors = ref({})
const submitting = ref(false)

// Reset form cuando se cierra
watch(() => props.show, (newVal) => {
  if (!newVal) {
    formData.value = {
      customer_name: '',
      customer_phone: '',
      customer_document: '',
      customer_email: '',
      customer_address: '',
      delivery_type: 'delivery',
      note: '',
    }
    errors.value = {}
    submitting.value = false
  }
})

const validateForm = () => {
  errors.value = {}
  
  if (!formData.value.customer_name.trim()) {
    errors.value.customer_name = 'El nombre es obligatorio'
  }
  
  if (!formData.value.customer_phone.trim()) {
    errors.value.customer_phone = 'El teléfono es obligatorio'
  } else if (formData.value.customer_phone.length < 7) {
    errors.value.customer_phone = 'Teléfono inválido'
  }
  
  if (!formData.value.customer_document.trim()) {
    errors.value.customer_document = 'El documento es obligatorio'
  } else if (formData.value.customer_document.length < 6) {
    errors.value.customer_document = 'El documento debe tener al menos 6 caracteres'
  }
  
  if (!formData.value.delivery_type) {
    errors.value.delivery_type = 'Selecciona un método de entrega'
  }
  
  return Object.keys(errors.value).length === 0
}

const handleSubmit = () => {
  if (!validateForm()) {
    return
  }
  
  submitting.value = true
  emit('submit', formData.value)
}
</script>

<style scoped>
.scale-modal-enter-active,
.scale-modal-leave-active {
  transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
}

.scale-modal-enter-from,
.scale-modal-leave-to {
  opacity: 0;
  transform: scale(0.9);
}
</style>
