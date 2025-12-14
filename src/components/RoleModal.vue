<template>
  <!-- Overlay -->
  <div 
    v-if="show"
    class="fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center z-50 p-4"
    @click.self="closeModal"
  >
    <!-- Modal -->
    <div class="bg-white dark:bg-zinc-900 rounded-2xl shadow-2xl dark:shadow-black/50 border border-gray-300 dark:border-zinc-800 w-full max-w-5xl max-h-[90vh] overflow-hidden animate-fade-in">
      
      <!-- Header -->
      <div class="px-6 py-4 border-b border-gray-200 dark:border-zinc-800 bg-gray-50 dark:bg-zinc-900">
        <div class="flex items-center justify-between">
          <div>
            <h3 class="text-xl font-bold text-gray-900 dark:text-white">
              {{ isEdit ? 'Editar Rol' : 'Nuevo Rol' }}
            </h3>
            <p class="text-sm text-gray-600 dark:text-zinc-400 mt-0.5">
              {{ isEdit ? 'Modifica los permisos del rol' : 'Configura los permisos para el nuevo rol' }}
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
        <form @submit.prevent="handleSubmit" class="space-y-6">
          
          <!-- Información Básica -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- Nombre del Rol -->
            <div>
              <label class="block text-sm font-bold text-gray-700 dark:text-zinc-300 mb-2">
                Nombre del Rol *
              </label>
              <input
                v-model="form.name"
                type="text"
                required
                class="w-full px-4 py-3 text-sm rounded-xl border-2 border-gray-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-gray-900 dark:text-zinc-200 placeholder-gray-400 dark:placeholder-zinc-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent transition-all"
                placeholder="Ej: Gerente de Ventas"
              />
            </div>

            <!-- Estado Activo -->
            <div class="flex items-center gap-3 p-4 bg-gray-50 dark:bg-zinc-800/50 rounded-xl border border-gray-200 dark:border-zinc-700">
              <input
                v-model="form.active"
                type="checkbox"
                id="role-active"
                class="w-5 h-5 text-blue-600 rounded border-gray-300 dark:border-zinc-600 focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 cursor-pointer"
              />
              <label for="role-active" class="flex-1 text-sm font-medium text-gray-700 dark:text-zinc-300 cursor-pointer">
                Rol Activo
                <span class="block text-xs text-gray-500 dark:text-zinc-400 mt-0.5">
                  Los roles inactivos no pueden asignarse
                </span>
              </label>
            </div>
          </div>

          <!-- Descripción -->
          <div>
            <label class="block text-sm font-bold text-gray-700 dark:text-zinc-300 mb-2">
              Descripción
            </label>
            <textarea
              v-model="form.description"
              rows="2"
              class="w-full px-4 py-3 text-sm rounded-xl border-2 border-gray-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-gray-900 dark:text-zinc-200 placeholder-gray-400 dark:placeholder-zinc-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent transition-all resize-none"
              placeholder="Describe las responsabilidades de este rol"
            ></textarea>
          </div>

          <!-- Permisos Header -->
          <div class="pt-4 border-t border-gray-200 dark:border-zinc-800">
            <div class="flex items-center justify-between mb-4">
              <div>
                <h4 class="text-lg font-bold text-gray-900 dark:text-white">Módulos Permitidos</h4>
                <p class="text-sm text-gray-600 dark:text-zinc-400 mt-0.5">
                  {{ selectedPermissions.length }} de {{ totalPermissions }} módulos seleccionados
                </p>
              </div>
              <div class="flex items-center gap-2">
                <button
                  type="button"
                  @click="selectAll"
                  class="px-4 py-2 bg-blue-50 dark:bg-blue-950 hover:bg-blue-100 dark:hover:bg-blue-900 text-blue-700 dark:text-blue-400 text-xs font-bold rounded-lg border border-blue-200 dark:border-blue-800 transition-all"
                >
                  Seleccionar Todo
                </button>
                <button
                  type="button"
                  @click="deselectAll"
                  class="px-4 py-2 bg-gray-50 dark:bg-zinc-800 hover:bg-gray-100 dark:hover:bg-zinc-700 text-gray-700 dark:text-zinc-300 text-xs font-bold rounded-lg border border-gray-200 dark:border-zinc-700 transition-all"
                >
                  Deseleccionar Todo
                </button>
              </div>
            </div>

            <!-- Módulos de Permisos -->
            <div class="space-y-4">
              <div 
                v-for="module in permissionsModules" 
                :key="module.id"
                class="bg-gray-50 dark:bg-zinc-800/50 rounded-xl p-4 border border-gray-200 dark:border-zinc-700"
              >
                <!-- Module Header -->
                <div class="flex items-center justify-between mb-3">
                  <div class="flex items-center gap-3 flex-1">
                    <div 
                      class="w-10 h-10 rounded-lg flex items-center justify-center flex-shrink-0"
                      :style="{ backgroundColor: module.color + '20', color: module.color }"
                    >
                      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="module.icon"></path>
                      </svg>
                    </div>
                    <div>
                      <h5 class="text-sm font-bold text-gray-900 dark:text-white flex items-center gap-2">
                        {{ module.name }}
                        <span 
                          v-if="module.isPremium" 
                          class="px-2 py-0.5 bg-amber-50 dark:bg-amber-950 text-amber-700 dark:text-amber-400 text-[10px] font-bold rounded-full border border-amber-200 dark:border-amber-800"
                        >
                          PREMIUM
                        </span>
                        <span 
                          v-if="isModuleFullySelected(module.id)"
                          class="px-2 py-0.5 bg-emerald-50 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-400 text-[10px] font-bold rounded-full border border-emerald-200 dark:border-emerald-800"
                        >
                          ✓ PERMITIDO
                        </span>
                      </h5>
                    </div>
                  </div>
                </div>

                <!-- Checkbox único del módulo -->
                <div class="pl-13">
                  <div
                    class="flex items-start gap-3 p-3 bg-white dark:bg-zinc-900 hover:bg-blue-50 dark:hover:bg-blue-950/30 rounded-lg border-2 transition-all cursor-pointer"
                    :class="form.permissions.includes(module.id + '.view') 
                      ? 'border-blue-500 dark:border-blue-600 bg-blue-50 dark:bg-blue-950/20' 
                      : 'border-gray-200 dark:border-zinc-700 hover:border-blue-300 dark:hover:border-blue-800'"
                    @click="togglePermission(module.id + '.view')"
                  >
                    <input
                      type="checkbox"
                      :checked="form.permissions.includes(module.id + '.view')"
                      class="mt-0.5 w-5 h-5 text-blue-600 rounded border-gray-300 dark:border-zinc-600 focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 cursor-pointer"
                      @click.stop="togglePermission(module.id + '.view')"
                    />
                    <div class="flex-1 min-w-0">
                      <p class="text-sm font-bold text-gray-900 dark:text-white leading-tight">
                        Acceso Completo al Módulo
                      </p>
                      <p class="text-xs text-gray-600 dark:text-zinc-400 leading-tight mt-1">
                        {{ module.description }}
                      </p>
                    </div>
                  </div>
                </div>
              </div>
            </div>
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
          :disabled="loading || form.permissions.length === 0"
          class="px-6 py-2.5 bg-slate-900 dark:bg-slate-700 hover:bg-black dark:hover:bg-slate-600 text-white text-sm font-bold rounded-xl shadow-lg shadow-slate-400/40 dark:shadow-slate-900/50 transition-all duration-300 disabled:opacity-50 disabled:cursor-not-allowed"
        >
          <span v-if="loading" class="flex items-center gap-2">
            <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            Guardando...
          </span>
          <span v-else>{{ isEdit ? 'Guardar Cambios' : 'Crear Rol' }}</span>
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
  role: {
    type: Object,
    default: null
  },
  permissionsModules: {
    type: Array,
    required: true
  }
})

const emit = defineEmits(['close', 'save'])

const loading = ref(false)
const form = ref({
  name: '',
  description: '',
  permissions: [],
  active: true
})

const isEdit = computed(() => !!props.role)

const totalPermissions = computed(() => {
  // Ahora son 17 módulos = 17 permisos totales
  return props.permissionsModules.length
})

const selectedPermissions = computed(() => form.value.permissions)

// Definir resetForm ANTES del watch
const resetForm = () => {
  form.value = {
    name: '',
    description: '',
    permissions: [],
    active: true
  }
}

// Watch para cargar datos cuando se edita
watch(() => props.role, (newRole) => {
  if (newRole) {
    // Parsear permissions si vienen como JSON string
    let permissions = []
    if (newRole.permissions) {
      try {
        permissions = typeof newRole.permissions === 'string' 
          ? JSON.parse(newRole.permissions) 
          : newRole.permissions
      } catch {
        permissions = []
      }
    }

    form.value = {
      name: newRole.name || '',
      description: newRole.description || '',
      permissions: Array.isArray(permissions) ? permissions : [],
      active: newRole.active !== undefined ? newRole.active : true
    }
  } else {
    resetForm()
  }
}, { immediate: true })

const togglePermission = (permissionId) => {
  const index = form.value.permissions.indexOf(permissionId)
  if (index > -1) {
    form.value.permissions.splice(index, 1)
  } else {
    form.value.permissions.push(permissionId)
  }
}

// Ya no se usa toggleModule porque cada módulo es un solo permiso
const toggleModule = (moduleId) => {
  togglePermission(moduleId + '.view')
}

const isModuleFullySelected = (moduleId) => {
  // Ahora solo verifica si tiene el permiso .view
  return form.value.permissions.includes(moduleId + '.view')
}

const getModuleSelectedCount = (moduleId) => {
  // Retorna 1 si está seleccionado, 0 si no
  return form.value.permissions.includes(moduleId + '.view') ? 1 : 0
}

const selectAll = () => {
  // Seleccionar todos los módulos (17 permisos tipo "dashboard.view", "pos.view", etc.)
  const allPermissionIds = props.permissionsModules.map(module => module.id + '.view')
  form.value.permissions = allPermissionIds
}

const deselectAll = () => {
  form.value.permissions = []
}

const closeModal = () => {
  resetForm()
  emit('close')
}

const handleSubmit = async () => {
  if (form.value.permissions.length === 0) {
    alert('Debes seleccionar al menos un permiso')
    return
  }

  loading.value = true
  try {
    emit('save', { ...form.value })
  } catch (error) {
    console.error('Error al guardar rol:', error)
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
