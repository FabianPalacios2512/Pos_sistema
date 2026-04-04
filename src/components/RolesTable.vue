<template>
  <div class="bg-white dark:bg-[#131314] rounded-xl shadow-lg border border-gray-200 dark:border-gray-800 overflow-hidden">
    <!-- Table Header -->
    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-800 bg-[#f8f9fa] dark:bg-[#1e1f20]">
      <div class="flex items-center justify-between">
        <div>
          <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Lista de Roles</h3>
          <p class="text-sm text-gray-600 dark:text-gray-400 mt-0.5">Gestiona los roles y permisos</p>
        </div>
      </div>
    </div>

    <!-- Table Content -->
    <div class="overflow-x-auto">
      <table class="w-full">
        <thead class="bg-[#f8f9fa] dark:bg-[#1e1f20] border-b border-gray-200 dark:border-gray-800">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 dark:text-gray-400 uppercase tracking-wider">Rol</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 dark:text-gray-400 uppercase tracking-wider">Permisos</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 dark:text-gray-400 uppercase tracking-wider">Usuarios</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 dark:text-gray-400 uppercase tracking-wider">Acciones</th>
          </tr>
        </thead>
        <tbody class="bg-white dark:bg-[#131314] divide-y divide-gray-200 dark:divide-gray-800">
          <tr 
            v-for="role in roles" 
            :key="role.id"
            class="hover:bg-gray-50 dark:hover:bg-[#1e1f20] transition-all duration-200"
          >
            <!-- Rol -->
            <td class="px-6 py-4 whitespace-nowrap">
              <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-purple-100 dark:bg-purple-900/30 flex items-center justify-center flex-shrink-0">
                  <svg class="w-5 h-5 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                  </svg>
                </div>
                <div>
                  <p class="text-sm font-medium text-gray-900 dark:text-white">{{ role.name }}</p>
                  <p class="text-xs text-gray-500 dark:text-gray-500">{{ role.description || 'Sin descripción' }}</p>
                </div>
              </div>
            </td>

            <!-- Permisos -->
            <td class="px-6 py-4 whitespace-nowrap">
              <div class="flex items-center gap-2">
                <span class="px-3 py-1 rounded-full text-xs font-medium bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400 border border-blue-200 dark:border-blue-700/50">
                  {{ getPermissionsCount(role) }} permisos
                </span>
              </div>
            </td>

            <!-- Usuarios Asignados -->
            <td class="px-6 py-4 whitespace-nowrap">
              <span class="px-3 py-1 rounded-full text-xs font-medium bg-emerald-50 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-700/50">
                {{ role.users_count || 0 }} usuarios
              </span>
            </td>

            <!-- Acciones -->
            <td class="px-6 py-4 whitespace-nowrap">
              <div class="flex items-center gap-2">
                <!-- Editar -->
                <button
                  @click="$emit('edit', role)"
                  class="p-2 rounded-lg border border-transparent text-gray-400 dark:text-gray-500 hover:text-amber-600 dark:hover:text-amber-400 hover:bg-amber-50 dark:hover:bg-amber-900/20 hover:border-amber-200 dark:hover:border-amber-700/50 transition-all duration-200"
                  title="Editar rol"
                >
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                  </svg>
                </button>

                <!-- Eliminar -->
                <button
                  @click="$emit('delete', role)"
                  :disabled="role.users_count > 0"
                  :class="role.users_count > 0 
                    ? 'opacity-50 cursor-not-allowed' 
                    : 'hover:text-rose-600 dark:hover:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-900/20 hover:border-rose-200 dark:hover:border-rose-700/50'"
                  class="p-2 rounded-lg border border-transparent text-gray-400 dark:text-gray-500 transition-all duration-200"
                  title="Eliminar rol"
                >
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                  </svg>
                </button>
              </div>
            </td>
          </tr>

          <!-- Empty State -->
          <tr v-if="!roles || roles.length === 0">
            <td colspan="4" class="px-6 py-12 text-center">
              <div class="flex flex-col items-center justify-center">
                <svg class="w-16 h-16 text-gray-300 dark:text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                </svg>
                <p class="text-gray-500 dark:text-gray-400 font-medium">No hay roles registrados</p>
                <p class="text-sm text-gray-400 dark:text-gray-500 mt-1">Crea tu primer rol para comenzar</p>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script setup>
defineProps({
  roles: {
    type: Array,
    required: true
  }
})

defineEmits(['edit', 'delete'])

const getPermissionsCount = (role) => {
  if (!role.permissions) return 0
  try {
    const permissions = typeof role.permissions === 'string' 
      ? JSON.parse(role.permissions) 
      : role.permissions
    return Array.isArray(permissions) ? permissions.length : 0
  } catch {
    return 0
  }
}
</script>
