<template>
  <!-- Overlay -->
  <div 
    v-if="show"
    class="fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center z-50 p-4"
    @click.self="closeModal"
  >
    <!-- Modal -->
    <div class="bg-white dark:bg-zinc-900 rounded-2xl shadow-2xl dark:shadow-black/50 border border-gray-300 dark:border-zinc-800 w-full max-w-2xl max-h-[90vh] overflow-hidden animate-fade-in">
      
      <!-- Header -->
      <div class="px-6 py-4 border-b border-gray-200 dark:border-zinc-800 bg-gray-50 dark:bg-zinc-900">
        <div class="flex items-center justify-between">
          <div>
            <h3 class="text-xl font-bold text-gray-900 dark:text-white">
              {{ isEdit ? 'Editar Usuario' : 'Nuevo Usuario' }}
            </h3>
            <p class="text-sm text-gray-600 dark:text-zinc-400 mt-0.5">
              {{ isEdit ? 'Modifica los datos del usuario' : 'Completa el formulario para crear un usuario' }}
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
      <div class="p-6 overflow-y-auto max-h-[calc(90vh-200px)]">
        <form @submit.prevent="handleSubmit" class="space-y-5">
          
          <!-- Nombre Completo -->
          <div>
            <label class="block text-sm font-bold text-gray-700 dark:text-zinc-300 mb-2">
              Nombre Completo *
            </label>
            <input
              v-model="form.name"
              type="text"
              required
              class="w-full px-4 py-3 text-sm rounded-xl border-2 border-gray-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-gray-900 dark:text-zinc-200 placeholder-gray-400 dark:placeholder-zinc-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent transition-all"
              placeholder="Ej: Juan Pérez"
            />
          </div>

          <!-- Email -->
          <div>
            <label class="block text-sm font-bold text-gray-700 dark:text-zinc-300 mb-2">
              Email *
            </label>
            <input
              v-model="form.email"
              type="email"
              required
              class="w-full px-4 py-3 text-sm rounded-xl border-2 border-gray-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-gray-900 dark:text-zinc-200 placeholder-gray-400 dark:placeholder-zinc-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent transition-all"
              placeholder="ejemplo@correo.com"
            />
          </div>

          <!-- Cédula y Teléfono (Grid) -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- Cédula -->
            <div>
              <label class="block text-sm font-bold text-gray-700 dark:text-zinc-300 mb-2">
                Cédula
              </label>
              <input
                v-model="form.cc"
                type="text"
                class="w-full px-4 py-3 text-sm rounded-xl border-2 border-gray-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-gray-900 dark:text-zinc-200 placeholder-gray-400 dark:placeholder-zinc-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent transition-all"
                placeholder="1234567890"
              />
            </div>

            <!-- Teléfono -->
            <div>
              <label class="block text-sm font-bold text-gray-700 dark:text-zinc-300 mb-2">
                Teléfono
              </label>
              <input
                v-model="form.phone"
                type="text"
                class="w-full px-4 py-3 text-sm rounded-xl border-2 border-gray-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-gray-900 dark:text-zinc-200 placeholder-gray-400 dark:placeholder-zinc-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent transition-all"
                placeholder="3001234567"
              />
            </div>
          </div>

          <!-- Contraseña (solo para nuevo usuario) -->
          <div v-if="!isEdit">
            <label class="block text-sm font-bold text-gray-700 dark:text-zinc-300 mb-2">
              Contraseña *
            </label>
            <input
              v-model="form.password"
              type="password"
              :required="!isEdit"
              class="w-full px-4 py-3 text-sm rounded-xl border-2 border-gray-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-gray-900 dark:text-zinc-200 placeholder-gray-400 dark:placeholder-zinc-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent transition-all"
              placeholder="Mínimo 8 caracteres"
              minlength="8"
            />
          </div>

          <!-- Rol -->
          <div>
            <label class="block text-sm font-bold text-gray-700 dark:text-zinc-300 mb-2">
              Rol *
            </label>
            <select
              v-model="form.role_id"
              required
              class="w-full px-4 py-3 text-sm rounded-xl border-2 border-gray-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-gray-900 dark:text-zinc-200 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent transition-all"
            >
              <option value="" disabled>Selecciona un rol</option>
              <option 
                v-for="role in roles" 
                :key="role.id" 
                :value="role.id"
              >
                {{ role.name }}
              </option>
            </select>
          </div>

          <!-- Estado Activo -->
          <div class="flex items-center gap-3 p-4 bg-gray-50 dark:bg-zinc-800/50 rounded-xl border border-gray-200 dark:border-zinc-700">
            <input
              v-model="form.active"
              type="checkbox"
              id="user-active"
              class="w-5 h-5 text-blue-600 rounded border-gray-300 dark:border-zinc-600 focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 cursor-pointer"
            />
            <label for="user-active" class="flex-1 text-sm font-medium text-gray-700 dark:text-zinc-300 cursor-pointer">
              Usuario Activo
              <span class="block text-xs text-gray-500 dark:text-zinc-400 mt-0.5">
                Los usuarios inactivos no pueden iniciar sesión
              </span>
            </label>
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
          :disabled="loading"
          class="px-6 py-2.5 bg-slate-900 dark:bg-slate-700 hover:bg-black dark:hover:bg-slate-600 text-white text-sm font-bold rounded-xl shadow-lg shadow-slate-400/40 dark:shadow-slate-900/50 transition-all duration-300 disabled:opacity-50 disabled:cursor-not-allowed"
        >
          <span v-if="loading" class="flex items-center gap-2">
            <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            Guardando...
          </span>
          <span v-else>{{ isEdit ? 'Guardar Cambios' : 'Crear Usuario' }}</span>
        </button>
      </div>

    </div>
  </div>
</template>

<script setup>
import { ref, watch, computed } from 'vue'

const props = defineProps({
  show: {
    type: Boolean,
    required: true
  },
  user: {
    type: Object,
    default: null
  },
  roles: {
    type: Array,
    required: true
  }
})

const emit = defineEmits(['close', 'save'])

const loading = ref(false)
const form = ref({
  name: '',
  email: '',
  cc: '',
  phone: '',
  password: '',
  role_id: '',
  active: true
})

const isEdit = computed(() => !!props.user)

// Definir resetForm ANTES del watch
const resetForm = () => {
  form.value = {
    name: '',
    email: '',
    cc: '',
    phone: '',
    password: '',
    role_id: '',
    active: true
  }
}

// Watch para cargar datos cuando se edita
watch(() => props.user, (newUser) => {
  if (newUser) {
    form.value = {
      name: newUser.name || '',
      email: newUser.email || '',
      cc: newUser.cc || '',
      phone: newUser.phone || '',
      password: '', // No se carga la contraseña por seguridad
      role_id: newUser.role_id || '',
      active: newUser.active !== undefined ? newUser.active : true
    }
  } else {
    resetForm()
  }
}, { immediate: true })

const closeModal = () => {
  resetForm()
  emit('close')
}

const handleSubmit = async () => {
  loading.value = true
  try {
    // Preparar datos para enviar
    const userData = { ...form.value }
    
    // Si es edición, remover password vacío
    if (isEdit.value && !userData.password) {
      delete userData.password
    }

    emit('save', userData)
  } catch (error) {
    console.error('Error al guardar usuario:', error)
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
