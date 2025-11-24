<template>
  <div v-if="show" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-[9999]">
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4 relative z-[9999]">
      <div class="p-6">
        <!-- Header -->
        <div class="flex items-center justify-between mb-4">
          <h3 class="text-lg font-semibold text-gray-800">
            📱 Número de WhatsApp
          </h3>
          <button 
            @click="cancel" 
            class="text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition-all min-w-[40px] min-h-[40px] flex items-center justify-center"
          >
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
          </button>
        </div>

        <!-- Message -->
        <p class="text-gray-600 mb-4">{{ message }}</p>

        <!-- Phone Input -->
        <div class="mb-4">
          <label class="block text-sm font-medium text-gray-700 mb-2">
            Número de teléfono
          </label>
          <input
            ref="phoneInput"
            v-model="phoneNumber"
            type="tel"
            placeholder="Ej: +57 300 1234567 o 3001234567"
            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            @keyup.enter="confirm"
            @keyup.escape="cancel"
          >
          <p class="text-xs text-gray-500 mt-1">
            Formatos aceptados: +57 300 1234567, 3001234567
          </p>
        </div>

        <!-- Error Message -->
        <div v-if="errorMessage" class="mb-4 p-3 bg-red-50 border border-red-200 rounded-md">
          <p class="text-sm text-red-600">{{ errorMessage }}</p>
        </div>

        <!-- Buttons -->
        <div class="flex gap-3 justify-end">
          <button
            @click="cancel"
            class="px-4 py-2 text-gray-600 bg-gray-100 rounded-md hover:bg-gray-200 transition-colors"
          >
            Cancelar
          </button>
          <button
            @click="confirm"
            :disabled="!phoneNumber?.trim()"
            class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 disabled:bg-gray-300 disabled:cursor-not-allowed transition-colors"
          >
            Confirmar
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, watch, nextTick } from 'vue'

const props = defineProps({
  show: {
    type: Boolean,
    default: false
  },
  message: {
    type: String,
    default: 'Ingrese el número de WhatsApp del cliente:'
  }
})

const emit = defineEmits(['confirm', 'cancel'])

const phoneInput = ref(null)
const phoneNumber = ref('')
const errorMessage = ref('')

// Focus input when modal opens
watch(() => props.show, (newVal) => {
  if (newVal) {
    phoneNumber.value = ''
    errorMessage.value = ''
    nextTick(() => {
      phoneInput.value?.focus()
    })
  }
})

const validatePhone = (phone) => {
  if (!phone?.trim()) {
    return 'El número de teléfono es requerido'
  }
  
  // Clean phone number
  const cleanPhone = phone.replace(/[^\d]/g, '')
  
  // Validate Colombian mobile format
  if (cleanPhone.length === 10 && cleanPhone.startsWith('3')) {
    return null // Valid
  } else if (cleanPhone.length === 12 && cleanPhone.startsWith('573')) {
    return null // Valid with country code
  }
  
  return 'Formato inválido. Use: +57 300 1234567 o 3001234567'
}

const confirm = () => {
  const validation = validatePhone(phoneNumber.value)
  
  if (validation) {
    errorMessage.value = validation
    return
  }
  
  // Clean and format phone
  let cleanPhone = phoneNumber.value.replace(/[^\d]/g, '')
  if (cleanPhone.length === 10 && cleanPhone.startsWith('3')) {
    cleanPhone = `+57${cleanPhone}`
  } else if (cleanPhone.length === 12 && cleanPhone.startsWith('573')) {
    cleanPhone = `+${cleanPhone}`
  }
  
  emit('confirm', cleanPhone)
}

const cancel = () => {
  emit('cancel')
}
</script>

<style scoped>
/* Modal animations */
.modal-enter-active, .modal-leave-active {
  transition: opacity 0.3s ease;
}

.modal-enter-from, .modal-leave-to {
  opacity: 0;
}
</style>