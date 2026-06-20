<template>
  <Teleport to="body">
    <Transition
      enter-active-class="transition-opacity duration-200 ease-out"
      enter-from-class="opacity-0"
      enter-to-class="opacity-100"
      leave-active-class="transition-opacity duration-150 ease-in"
      leave-from-class="opacity-100"
      leave-to-class="opacity-0"
    >
      <div 
        v-if="isOpen"
        @click="handleCancel"
        class="fixed inset-0 bg-black/60 backdrop-blur-sm z-[110] flex items-center justify-center p-4"
      >
        <Transition
          enter-active-class="transition-all duration-200 ease-out"
          enter-from-class="opacity-0 translate-y-4 scale-95"
          enter-to-class="opacity-100 translate-y-0 scale-100"
          leave-active-class="transition-all duration-150 ease-in"
          leave-from-class="opacity-100 translate-y-0 scale-100"
          leave-to-class="opacity-0 translate-y-2 scale-95"
        >
          <div 
            v-if="isOpen"
            @click.stop
            class="bg-white dark:bg-[#09090b] rounded-xl shadow-2xl max-w-sm w-full overflow-hidden border border-gray-200 dark:border-zinc-800"
          >
            <!-- Header -->
            <div class="px-5 py-4 flex items-center justify-between border-b border-gray-100 dark:border-zinc-800">
              <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-lg bg-emerald-50 dark:bg-emerald-900/30 flex items-center justify-center text-emerald-600 dark:text-emerald-400">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                  </svg>
                </div>
                <div>
                  <h3 class="text-[15px] font-bold text-gray-900 dark:text-white leading-tight">Cliente no registrado</h3>
                  <p class="text-[11px] text-gray-500 dark:text-zinc-400">Guardarlo agilizará futuras compras</p>
                </div>
              </div>
              <button 
                @click="handleCancel"
                class="w-7 h-7 flex items-center justify-center rounded-md hover:bg-gray-100 dark:hover:bg-zinc-800 text-gray-400 hover:text-gray-600 dark:hover:text-zinc-300 transition-colors"
              >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
              </button>
            </div>

            <!-- Content -->
            <div class="p-5">
              <div class="bg-gray-50 dark:bg-zinc-900/50 rounded-lg border border-gray-200 dark:border-zinc-800 p-4 space-y-3">
                <div class="flex flex-col">
                  <span class="text-[10px] font-bold text-gray-400 dark:text-zinc-500 uppercase tracking-wider mb-0.5">Nombre Completo</span>
                  <span class="text-[14px] font-semibold text-gray-900 dark:text-white">{{ customerData.name }}</span>
                </div>

                <div class="grid grid-cols-2 gap-4 pt-3 border-t border-gray-200/60 dark:border-zinc-800">
                  <div class="flex flex-col">
                    <span class="text-[10px] font-bold text-gray-400 dark:text-zinc-500 uppercase tracking-wider mb-0.5">Documento</span>
                    <span class="text-[13px] font-medium text-gray-800 dark:text-zinc-200">
                      {{ customerData.document || 'No provisto' }}
                    </span>
                  </div>

                  <div class="flex flex-col">
                    <span class="text-[10px] font-bold text-gray-400 dark:text-zinc-500 uppercase tracking-wider mb-0.5">Contacto</span>
                    <span class="text-[13px] font-medium text-gray-800 dark:text-zinc-200 flex items-center gap-1.5">
                      <svg class="w-3.5 h-3.5 text-green-500" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51a12.8 12.8 0 00-.57-.01c-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                      {{ customerData.phone || 'No provisto' }}
                    </span>
                  </div>
                </div>

                <div v-if="customerData.address" class="pt-3 border-t border-gray-200/60 dark:border-zinc-800">
                  <span class="text-[10px] font-bold text-gray-400 dark:text-zinc-500 uppercase tracking-wider mb-0.5 block">Dirección de Entrega</span>
                  <p class="text-[13px] font-medium text-gray-800 dark:text-zinc-200">{{ customerData.address }}</p>
                </div>
              </div>
            </div>

            <!-- Footer -->
            <div class="px-5 pb-5 flex items-center gap-3">
              <button
                @click="handleCancel"
                class="flex-1 py-2.5 bg-white dark:bg-zinc-900 hover:bg-gray-50 dark:hover:bg-zinc-800 text-gray-700 dark:text-zinc-300 text-[13px] font-semibold rounded-lg border border-gray-300 dark:border-zinc-700 transition-colors"
              >
                No registrar
              </button>
              <button
                @click="handleConfirm"
                class="flex-1 py-2.5 bg-gray-900 hover:bg-black dark:bg-white dark:hover:bg-gray-100 text-white dark:text-gray-900 text-[13px] font-semibold rounded-lg shadow-sm transition-colors flex justify-center items-center gap-2"
              >
                Registrar Cliente
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
