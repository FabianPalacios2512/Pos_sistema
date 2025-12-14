<template>
  <!-- Overlay -->
  <div 
    v-if="show"
    class="fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center z-50 p-4"
    @click.self="closeModal"
  >
    <!-- Modal -->
    <div class="bg-white dark:bg-zinc-900 rounded-2xl shadow-2xl dark:shadow-black/50 border border-gray-300 dark:border-zinc-800 w-full max-w-md overflow-hidden animate-fade-in">
      
      <!-- Header -->
      <div class="px-6 py-4 border-b border-gray-200 dark:border-zinc-800 bg-gray-50 dark:bg-zinc-900">
        <div class="flex items-center justify-between">
          <div>
            <h3 class="text-xl font-bold text-gray-900 dark:text-white">
              Cambiar Contraseña
            </h3>
            <p class="text-sm text-gray-600 dark:text-zinc-400 mt-0.5">
              {{ user?.name }}
            </p>
          </div>
          <button
            @click="closeModal"
            class="p-2 rounded-lg text-gray-400 dark:text-zinc-500 hover:text-gray-600 dark:hover:text-zinc-300 hover:bg-gray-100 dark:hover:bg-zinc-800 transition-all"
          >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
          </button>
        </div>
      </div>

      <!-- Body -->
      <div class="p-6">
        <form @submit.prevent="handleSubmit" class="space-y-5">
          
          <!-- Nueva Contraseña -->
          <div>
            <label class="block text-sm font-bold text-gray-700 dark:text-zinc-300 mb-2">
              Nueva Contraseña *
            </label>
            <input
              v-model="form.password"
              type="password"
              required
              class="w-full px-4 py-3 text-sm rounded-xl border-2 border-gray-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-gray-900 dark:text-zinc-200 placeholder-gray-400 dark:placeholder-zinc-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent transition-all"
              placeholder="Mínimo 8 caracteres"
              minlength="8"
            />
          </div>

          <!-- Confirmar Contraseña -->
          <div>
            <label class="block text-sm font-bold text-gray-700 dark:text-zinc-300 mb-2">
              Confirmar Contraseña *
            </label>
            <input
              v-model="form.password_confirmation"
              type="password"
              required
              class="w-full px-4 py-3 text-sm rounded-xl border-2 border-gray-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-gray-900 dark:text-zinc-200 placeholder-gray-400 dark:placeholder-zinc-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent transition-all"
              placeholder="Repite la contraseña"
              minlength="8"
            />
          </div>

          <!-- Mensaje de error -->
          <div v-if="passwordMismatch" class="p-3 bg-rose-50 dark:bg-rose-950 border border-rose-200 dark:border-rose-800 rounded-lg">
            <p class="text-sm font-medium text-rose-700 dark:text-rose-400">
              Las contraseñas no coinciden
            </p>
          </div>

        </form>
      </div>

      <!-- Footer -->
      <div class="px-6 py-4 border-t border-gray-200 dark:border-zinc-800 bg-gray-50 dark:bg-zinc-900 flex items-center justify-end gap-3">
        <button
          @click="closeModal"
          type="button"
          class="px-5 py-2.5 bg-white dark:bg-zinc-900 hover:bg-gray-50 dark:hover:bg-zinc-800 text-gray-600 dark:text-zinc-200 text-sm font-bold rounded-xl border border-gray-200 dark:border-zinc-700 shadow-sm transition-all duration-200"
        >
          Cancelar
        </button>
        <button
          @click="handleSubmit"
          type="submit"
          :disabled="loading || passwordMismatch"
          class="px-6 py-2.5 bg-slate-900 dark:bg-slate-700 hover:bg-black dark:hover:bg-slate-600 text-white text-sm font-bold rounded-xl shadow-lg shadow-slate-400/40 dark:shadow-slate-900/50 transition-all duration-300 disabled:opacity-50 disabled:cursor-not-allowed"
        >
          <span v-if="loading" class="flex items-center gap-2">
            <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            Guardando...
          </span>
          <span v-else>Cambiar Contraseña</span>
        </button>
      </div>

    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue'

const props = defineProps({
  show: {
    type: Boolean,
    required: true
  },
  user: {
    type: Object,
    default: null
  }
})

const emit = defineEmits(['close', 'save'])

const loading = ref(false)
const form = ref({
  password: '',
  password_confirmation: ''
})

const passwordMismatch = computed(() => {
  return form.value.password !== '' && 
         form.value.password_confirmation !== '' && 
         form.value.password !== form.value.password_confirmation
})

watch(() => props.show, (newShow) => {
  if (!newShow) {
    resetForm()
  }
})

const resetForm = () => {
  form.value = {
    password: '',
    password_confirmation: ''
  }
}

const closeModal = () => {
  resetForm()
  emit('close')
}

const handleSubmit = async () => {
  if (passwordMismatch.value) return
  
  loading.value = true
  try {
    emit('save', {
      password: form.value.password
    })
    resetForm()
  } catch (error) {
    console.error('Error al cambiar contraseña:', error)
  } finally {
    loading.value = false
  }
}
</script>

<style scoped>
@keyframes fade-in {
  from {
    opacity: 0;
    transform: scale(0.95);
  }
  to {
    opacity: 1;
    transform: scale(1);
  }
}

.animate-fade-in {
  animation: fade-in 0.2s ease-out;
}
</style>
