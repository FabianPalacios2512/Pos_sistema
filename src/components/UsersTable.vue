<template>
  <div class="bg-white dark:bg-zinc-900 rounded-2xl shadow-xl dark:shadow-black/50 border border-gray-300 dark:border-zinc-800 overflow-hidden">
    <!-- Table Header -->
    <div class="px-6 py-4 border-b border-gray-200 dark:border-zinc-800 bg-gray-50 dark:bg-zinc-900">
      <div class="flex items-center justify-between">
        <div>
          <h3 class="text-lg font-bold text-gray-900 dark:text-white">Lista de Usuarios</h3>
          <p class="text-sm text-gray-600 dark:text-zinc-400 mt-0.5">Gestiona los usuarios del sistema</p>
        </div>
      </div>
    </div>

    <!-- Table Content -->
    <div class="overflow-x-auto">
      <table class="w-full">
        <thead class="bg-gray-50 dark:bg-zinc-900 border-b border-gray-200 dark:border-zinc-800">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 dark:text-zinc-300 uppercase tracking-wider">Usuario</th>
            <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 dark:text-zinc-300 uppercase tracking-wider">Email</th>
            <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 dark:text-zinc-300 uppercase tracking-wider">Rol</th>
            <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 dark:text-zinc-300 uppercase tracking-wider">Estado</th>
            <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 dark:text-zinc-300 uppercase tracking-wider">Acciones</th>
          </tr>
        </thead>
        <tbody class="bg-white dark:bg-zinc-900 divide-y divide-gray-200 dark:divide-zinc-800">
          <tr 
            v-for="user in users" 
            :key="user.id"
            class="hover:bg-gray-50 dark:hover:bg-zinc-800/50 transition-all duration-200"
          >
            <!-- Usuario -->
            <td class="px-6 py-4 whitespace-nowrap">
              <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white font-bold text-sm flex-shrink-0">
                  {{ getUserInitials(user.name) }}
                </div>
                <div>
                  <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ user.name }}</p>
                  <p class="text-xs text-gray-500 dark:text-zinc-400">{{ user.cc || 'Sin CC' }}</p>
                </div>
              </div>
            </td>

            <!-- Email -->
            <td class="px-6 py-4 whitespace-nowrap">
              <p class="text-sm text-gray-700 dark:text-zinc-300">{{ user.email }}</p>
            </td>

            <!-- Rol -->
            <td class="px-6 py-4 whitespace-nowrap">
              <span class="px-3 py-1 rounded-full text-xs font-bold bg-indigo-50 dark:bg-indigo-950 text-indigo-700 dark:text-indigo-400 border border-indigo-100 dark:border-indigo-800">
                {{ user.role?.name || 'Sin rol' }}
              </span>
            </td>

            <!-- Estado -->
            <td class="px-6 py-4 whitespace-nowrap">
              <span 
                :class="user.active 
                  ? 'bg-emerald-50 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-400 border-emerald-100 dark:border-emerald-800'
                  : 'bg-rose-50 dark:bg-rose-950 text-rose-700 dark:text-rose-400 border-rose-100 dark:border-rose-800'"
                class="px-2.5 py-1 rounded-full text-[10px] font-bold border uppercase tracking-wide"
              >
                {{ user.active ? 'ACTIVO' : 'INACTIVO' }}
              </span>
            </td>

            <!-- Acciones -->
            <td class="px-6 py-4 whitespace-nowrap">
              <div class="flex items-center gap-2">
                <!-- Editar -->
                <button
                  @click="$emit('edit', user)"
                  class="p-2 rounded-lg border border-transparent text-slate-400 dark:text-zinc-500 hover:text-amber-600 dark:hover:text-amber-400 hover:bg-amber-50 dark:hover:bg-amber-900/20 hover:border-amber-100 dark:hover:border-amber-900/30 transition-all duration-200"
                  title="Editar usuario"
                >
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                  </svg>
                </button>

                <!-- Cambiar Contraseña -->
                <button
                  @click="$emit('change-password', user)"
                  class="p-2 rounded-lg border border-transparent text-slate-400 dark:text-zinc-500 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/20 hover:border-blue-100 dark:hover:border-blue-900/30 transition-all duration-200"
                  title="Cambiar contraseña"
                >
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path>
                  </svg>
                </button>

                <!-- Toggle Estado -->
                <button
                  @click="$emit('toggle-status', user)"
                  :class="user.active 
                    ? 'hover:text-rose-600 dark:hover:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-900/20 hover:border-rose-100 dark:hover:border-rose-900/30'
                    : 'hover:text-emerald-600 dark:hover:text-emerald-400 hover:bg-emerald-50 dark:hover:bg-emerald-900/20 hover:border-emerald-100 dark:hover:border-emerald-900/30'"
                  class="p-2 rounded-lg border border-transparent text-slate-400 dark:text-zinc-500 transition-all duration-200"
                  :title="user.active ? 'Desactivar' : 'Activar'"
                >
                  <svg v-if="user.active" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path>
                  </svg>
                  <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                  </svg>
                </button>

                <!-- Eliminar -->
                <button
                  @click="$emit('delete', user)"
                  class="p-2 rounded-lg border border-transparent text-slate-400 dark:text-zinc-500 hover:text-rose-600 dark:hover:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-900/20 hover:border-rose-100 dark:hover:border-rose-900/30 transition-all duration-200"
                  title="Eliminar usuario"
                >
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                  </svg>
                </button>
              </div>
            </td>
          </tr>

          <!-- Empty State -->
          <tr v-if="!users || users.length === 0">
            <td colspan="5" class="px-6 py-12 text-center">
              <div class="flex flex-col items-center justify-center">
                <svg class="w-16 h-16 text-gray-300 dark:text-zinc-700 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                </svg>
                <p class="text-gray-500 dark:text-zinc-400 font-medium">No hay usuarios registrados</p>
                <p class="text-sm text-gray-400 dark:text-zinc-500 mt-1">Crea tu primer usuario para comenzar</p>
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
  users: {
    type: Array,
    required: true
  }
})

defineEmits(['edit', 'delete', 'toggle-status', 'change-password'])

const getUserInitials = (name) => {
  if (!name) return '?'
  const parts = name.trim().split(' ')
  if (parts.length >= 2) {
    return (parts[0][0] + parts[1][0]).toUpperCase()
  }
  return name.substring(0, 2).toUpperCase()
}
</script>
