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
        @click="handleCancel"
        class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm z-[110] flex items-center justify-center p-4"
      >
        <Transition
          enter-active-class="transition-all duration-300 ease-out"
          enter-from-class="opacity-0 scale-95"
          enter-to-class="opacity-100 scale-100"
          leave-active-class="transition-all duration-200 ease-in"
          leave-from-class="opacity-100 scale-100"
          leave-to-class="opacity-0 scale-95"
        >
          <div 
            v-if="isOpen"
            @click.stop
            class="bg-white rounded-2xl shadow-2xl max-w-md w-full overflow-hidden"
          >
            <!-- Header -->
            <div class="bg-white border-b border-orange-200 px-6 py-4">
              <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                  <div class="w-10 h-10 bg-gradient-to-br from-orange-400 to-amber-500 rounded-xl flex items-center justify-center shadow-lg">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                  </div>
                  <div>
                    <h3 class="text-base font-bold text-gray-900">Cliente Nuevo Detectado</h3>
                    <p class="text-xs text-gray-500">¿Deseas registrarlo en el sistema?</p>
                  </div>
                </div>
                <button 
                  @click="handleCancel"
                  class="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-gray-100 text-gray-400 hover:text-gray-600 transition-colors"
                >
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                  </svg>
                </button>
              </div>
            </div>

            <!-- Content -->
            <div class="p-6 space-y-4">
              <!-- Message -->
              <div class="bg-gradient-to-br from-orange-50 to-amber-50 border border-orange-200 rounded-xl p-4">
                <div class="flex items-start space-x-3">
                  <div class="w-8 h-8 bg-orange-100 rounded-lg flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                  </div>
                  <div>
                    <p class="text-sm font-bold text-gray-900 mb-1">
                      Este cliente no existe en el sistema
                    </p>
                    <p class="text-xs text-gray-600 leading-relaxed">
                      Puedes registrarlo ahora para tener su historial de compras y facilitar futuras ventas.
                    </p>
                  </div>
                </div>
              </div>

              <!-- Customer Info -->
              <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
                <div class="bg-gray-50 border-b border-gray-200 px-4 py-2.5">
                  <h4 class="text-xs font-bold text-gray-700 uppercase tracking-wide">Datos del Cliente</h4>
                </div>
                <div class="p-4 space-y-3">
                  <div class="flex justify-between items-center">
                    <span class="text-xs font-medium text-gray-500 uppercase">Nombre</span>
                    <span class="text-sm font-bold text-gray-900">{{ customerData.name }}</span>
                  </div>

                  <div class="flex justify-between items-center">
                    <span class="text-xs font-medium text-gray-500 uppercase">Documento</span>
                    <span class="text-sm font-semibold text-gray-700 bg-gray-50 px-2 py-1 rounded-lg">{{ customerData.document }}</span>
                  </div>

                  <div class="flex justify-between items-center">
                    <span class="text-xs font-medium text-gray-500 uppercase">Teléfono</span>
                    <span class="text-sm font-semibold text-gray-700">{{ customerData.phone }}</span>
                  </div>

                  <div v-if="customerData.address" class="pt-3 border-t border-gray-200">
                    <span class="text-xs font-medium text-gray-500 uppercase block mb-1.5">Dirección</span>
                    <p class="text-sm text-gray-700 leading-relaxed">{{ customerData.address }}</p>
                  </div>
                </div>
              </div>
            </div>

            <!-- Footer -->
            <div class="bg-gray-50 px-6 py-4 flex items-center justify-end space-x-3">
              <button
                @click="handleCancel"
                class="px-4 py-2.5 bg-white hover:bg-gray-100 text-gray-700 text-sm font-semibold rounded-xl border border-gray-300 transition-colors"
              >
                Cancelar
              </button>
              <button
                @click="handleConfirm"
                class="px-5 py-2.5 bg-gradient-to-r from-lime-400 to-green-400 hover:from-lime-500 hover:to-green-500 text-white text-sm font-bold rounded-xl shadow-lg transition-all duration-300 transform hover:scale-105"
              >
                Sí, Agregar Cliente
              </button>
            </div>
          </div>
        </Transition>
      </div>
    </Transition>
  </Teleport>
</template>

<script setup>
import { defineProps, defineEmits } from 'vue'

defineProps({
  isOpen: {
    type: Boolean,
    default: false
  },
  customerData: {
    type: Object,
    required: true
  }
})

const emit = defineEmits(['confirm', 'cancel', 'close'])

const handleConfirm = () => {
  emit('confirm')
  emit('close')
}

const handleCancel = () => {
  emit('cancel')
  emit('close')
}
</script>
