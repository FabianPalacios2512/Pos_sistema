<template>
  <div class="space-y-6">

    <!-- Encabezado -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
      <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center space-y-4 sm:space-y-0">
        <div>
          <h2 class="text-2xl font-bold text-gray-900 dark:text-white flex items-center">
            <svg class="w-8 h-8 mr-3 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
            </svg>
            Gestión de Roles y Permisos
          </h2>
          <p class="text-gray-600 dark:text-gray-400 mt-1">
            Crear roles personalizados y asignar permisos específicos para cada módulo del sistema
          </p>
        </div>

        <!-- Acciones principales -->
        <div class="flex flex-wrap items-center space-x-3">
          <button
            @click="openCreateModal"
            class="flex items-center space-x-2 px-4 py-2 bg-gradient-to-r from-purple-500 to-purple-600 hover:from-purple-600 hover:to-purple-700 text-white rounded-xl font-semibold shadow-md hover:shadow-lg transform hover:-translate-y-0.5 transition-all duration-200"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            <span>Crear Rol</span>
          </button>
          
          <button
            class="flex items-center space-x-2 px-4 py-2 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white rounded-xl font-semibold shadow-md hover:shadow-lg transform hover:-translate-y-0.5 transition-all duration-200"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            <span>Exportar Roles</span>
          </button>
        </div>
      </div>
    </div>

    <!-- Tarjetas de Resumen -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
      <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl p-6 text-white">
        <div class="flex items-center justify-between">
          <div>
            <h3 class="text-purple-100 text-sm font-medium">Total Roles</h3>
            <p class="text-2xl font-bold">{{ roles.length }}</p>
          </div>
          <div class="bg-white bg-opacity-20 rounded-lg p-3">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
            </svg>
          </div>
        </div>
      </div>

      <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl p-6 text-white">
        <div class="flex items-center justify-between">
          <div>
            <h3 class="text-blue-100 text-sm font-medium">Roles Activos</h3>
            <p class="text-2xl font-bold">{{ activeRoles }}</p>
          </div>
          <div class="bg-white bg-opacity-20 rounded-lg p-3">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
          </div>
        </div>
      </div>

      <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl p-6 text-white">
        <div class="flex items-center justify-between">
          <div>
            <h3 class="text-green-100 text-sm font-medium">Total Usuarios</h3>
            <p class="text-2xl font-bold">{{ totalUsers }}</p>
          </div>
          <div class="bg-white bg-opacity-20 rounded-lg p-3">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
            </svg>
          </div>
        </div>
      </div>

      <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl p-6 text-white">
        <div class="flex items-center justify-between">
          <div>
            <h3 class="text-orange-100 text-sm font-medium">Módulos Sistema</h3>
            <p class="text-2xl font-bold">{{ modules.length }}</p>
          </div>
          <div class="bg-white bg-opacity-20 rounded-lg p-3">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
            </svg>
          </div>
        </div>
      </div>
    </div>

    <!-- Lista de Roles -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
      <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Roles del Sistema</h3>
        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
          Gestiona los roles y sus permisos de acceso a los módulos
        </p>
      </div>

      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
          <thead class="bg-gray-50 dark:bg-gray-900">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                Rol
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                Usuarios Asignados
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                Permisos
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                Estado
              </th>
              <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                Acciones
              </th>
            </tr>
          </thead>
          <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
            <tr
              v-for="role in roles"
              :key="role.id"
              class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
            >
              <!-- Información del Rol -->
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="flex items-center">
                  <div class="w-10 h-10 rounded-lg flex items-center justify-center text-white font-semibold"
                       :class="getRoleColor(role.name)">
                    {{ role.name.charAt(0).toUpperCase() }}
                  </div>
                  <div class="ml-4">
                    <div class="text-sm font-medium text-gray-900 dark:text-white">
                      {{ role.name }}
                    </div>
                    <div class="text-sm text-gray-500 dark:text-gray-400">
                      {{ role.description }}
                    </div>
                  </div>
                </div>
              </td>

              <!-- Usuarios Asignados -->
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="flex items-center">
                  <span class="text-sm font-medium text-gray-900 dark:text-white">
                    {{ role.users_count }}
                  </span>
                  <span class="ml-2 text-xs text-gray-500 dark:text-gray-400">
                    {{ role.users_count === 1 ? 'usuario' : 'usuarios' }}
                  </span>
                </div>
              </td>

              <!-- Permisos -->
              <td class="px-6 py-4">
                <div class="flex flex-wrap gap-1">
                  <template v-if="role.permissions.includes('all')">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-400">
                      Acceso Total
                    </span>
                  </template>
                  <template v-else>
                    <span
                      v-for="permission in role.permissions.slice(0, 3)"
                      :key="permission"
                      class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400"
                    >
                      {{ getPermissionName(permission) }}
                    </span>
                    <span
                      v-if="role.permissions.length > 3"
                      class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-900/30 dark:text-gray-400"
                    >
                      +{{ role.permissions.length - 3 }} más
                    </span>
                  </template>
                </div>
              </td>

              <!-- Estado -->
              <td class="px-6 py-4 whitespace-nowrap">
                <span 
                  :class="[
                    'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium',
                    role.active 
                      ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400'
                      : 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400'
                  ]"
                >
                  {{ role.active ? 'Activo' : 'Inactivo' }}
                </span>
              </td>

              <!-- Acciones -->
              <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                <div class="flex items-center justify-end space-x-2">
                  <button
                    @click="showRoleDetails(role)"
                    class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300 transition-colors"
                    title="Ver permisos"
                  >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                    </svg>
                  </button>
                  
                  <button
                    @click="openEditModal(role)"
                    class="text-purple-600 hover:text-purple-900 dark:text-purple-400 dark:hover:text-purple-300 transition-colors"
                    title="Editar rol"
                  >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                  </button>
                  
                  <button
                    v-if="role.name !== 'Administrador'"
                    @click="toggleRoleStatus(role)"
                    :class="[
                      'transition-colors',
                      role.active 
                        ? 'text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300'
                        : 'text-green-600 hover:text-green-900 dark:text-green-400 dark:hover:text-green-300'
                    ]"
                    :title="role.active ? 'Desactivar' : 'Activar'"
                  >
                    <svg v-if="role.active" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636m12.728 12.728L18.364 5.636M5.636 18.364l12.728-12.728"></path>
                    </svg>
                    <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                  </button>

                  <button
                    v-if="role.name !== 'Administrador'"
                    @click="deleteRole(role)"
                    class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300 transition-colors"
                    title="Eliminar rol"
                  >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Modal de Crear/Editar Rol -->
    <div
      v-if="showRoleModal"
      class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50"
      @click.self="closeRoleModal"
    >
      <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
        <div class="p-4 border-b border-gray-200 dark:border-gray-700">
          <h3 class="text-base font-semibold text-gray-900 dark:text-white">
            {{ editingRole ? 'Editar Rol' : 'Crear Nuevo Rol' }}
          </h3>
          <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">
            Define el nombre, descripción y permisos del rol
          </p>
        </div>

        <div class="p-4 space-y-4">
          <!-- Información Básica -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                Nombre del Rol
              </label>
              <input
                v-model="roleForm.name"
                type="text"
                placeholder="Ej: Cajero, Supervisor, etc."
                class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
              />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                Estado
              </label>
              <select
                v-model="roleForm.active"
                class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
              >
                <option :value="true">Activo</option>
                <option :value="false">Inactivo</option>
              </select>
            </div>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
              Descripción
            </label>
            <textarea
              v-model="roleForm.description"
              rows="2"
              placeholder="Describe las responsabilidades y alcance de este rol..."
              class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
            ></textarea>
          </div>

          <!-- Permisos por Categoría -->
          <div>
            <h4 class="text-base font-medium text-gray-900 dark:text-white mb-3">
              Permisos de Acceso a Módulos
            </h4>

            <div class="space-y-4">
              <!-- Acceso Total -->
              <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4 bg-purple-50 dark:bg-purple-900/20">
                <label class="flex items-center cursor-pointer">
                  <input
                    v-model="hasFullAccess"
                    type="checkbox"
                    @change="toggleFullAccess"
                    class="h-4 w-4 text-purple-600 focus:ring-purple-500 border-gray-300 rounded"
                  />
                  <span class="ml-3 text-sm font-medium text-gray-900 dark:text-white">
                    🔓 Acceso Total al Sistema
                  </span>
                </label>
                <p class="ml-7 text-xs text-gray-600 dark:text-gray-400 mt-1">
                  Este rol tendrá todos los permisos disponibles
                </p>
              </div>

              <!-- Permisos Individuales -->
              <template v-if="!hasFullAccess">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <!-- Módulo Dashboard -->
                  <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                    <h5 class="font-medium text-gray-900 dark:text-white mb-3 text-sm flex items-center">
                      📊 Dashboard
                    </h5>
                    <div class="space-y-2">
                      <label class="flex items-center cursor-pointer">
                        <input
                          v-model="roleForm.permissions"
                          value="dashboard.view"
                          type="checkbox"
                          class="h-3 w-3 text-purple-600 focus:ring-purple-500 border-gray-300 rounded"
                        />
                        <span class="ml-2 text-xs text-gray-700 dark:text-gray-300">Ver Dashboard</span>
                      </label>
                      <label class="flex items-center cursor-pointer">
                        <input
                          v-model="roleForm.permissions"
                          value="dashboard.analytics"
                          type="checkbox"
                          class="h-3 w-3 text-purple-600 focus:ring-purple-500 border-gray-300 rounded"
                        />
                        <span class="ml-2 text-xs text-gray-700 dark:text-gray-300">Ver Analíticas</span>
                      </label>
                    </div>
                  </div>

                  <!-- Módulo Productos -->
                  <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                    <h5 class="font-medium text-gray-900 dark:text-white mb-3 text-sm flex items-center">
                      📦 Productos
                    </h5>
                    <div class="space-y-2">
                      <label class="flex items-center cursor-pointer">
                        <input
                          v-model="roleForm.permissions"
                          value="products.view"
                          type="checkbox"
                          class="h-3 w-3 text-purple-600 focus:ring-purple-500 border-gray-300 rounded"
                        />
                        <span class="ml-2 text-xs text-gray-700 dark:text-gray-300">Ver Productos</span>
                      </label>
                      <label class="flex items-center cursor-pointer">
                        <input
                          v-model="roleForm.permissions"
                          value="products.create"
                          type="checkbox"
                          class="h-3 w-3 text-purple-600 focus:ring-purple-500 border-gray-300 rounded"
                        />
                        <span class="ml-2 text-xs text-gray-700 dark:text-gray-300">Crear Productos</span>
                      </label>
                      <label class="flex items-center cursor-pointer">
                        <input
                          v-model="roleForm.permissions"
                          value="products.edit"
                          type="checkbox"
                          class="h-3 w-3 text-purple-600 focus:ring-purple-500 border-gray-300 rounded"
                        />
                        <span class="ml-2 text-xs text-gray-700 dark:text-gray-300">Editar Productos</span>
                      </label>
                      <label class="flex items-center cursor-pointer">
                        <input
                          v-model="roleForm.permissions"
                          value="products.delete"
                          type="checkbox"
                          class="h-3 w-3 text-purple-600 focus:ring-purple-500 border-gray-300 rounded"
                        />
                        <span class="ml-2 text-xs text-gray-700 dark:text-gray-300">Eliminar Productos</span>
                      </label>
                    </div>
                  </div>

                  <!-- Módulo Ventas -->
                  <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                    <h5 class="font-medium text-gray-900 dark:text-white mb-3 text-sm flex items-center">
                      💰 Ventas
                    </h5>
                    <div class="space-y-2">
                      <label class="flex items-center cursor-pointer">
                        <input
                          v-model="roleForm.permissions"
                          value="sales.view"
                          type="checkbox"
                          class="h-3 w-3 text-purple-600 focus:ring-purple-500 border-gray-300 rounded"
                        />
                        <span class="ml-2 text-xs text-gray-700 dark:text-gray-300">Ver Ventas</span>
                      </label>
                      <label class="flex items-center cursor-pointer">
                        <input
                          v-model="roleForm.permissions"
                          value="sales.create"
                          type="checkbox"
                          class="h-3 w-3 text-purple-600 focus:ring-purple-500 border-gray-300 rounded"
                        />
                        <span class="ml-2 text-xs text-gray-700 dark:text-gray-300">Realizar Ventas</span>
                      </label>
                      <label class="flex items-center cursor-pointer">
                        <input
                          v-model="roleForm.permissions"
                          value="sales.refund"
                          type="checkbox"
                          class="h-3 w-3 text-purple-600 focus:ring-purple-500 border-gray-300 rounded"
                        />
                        <span class="ml-2 text-xs text-gray-700 dark:text-gray-300">Procesar Devoluciones</span>
                      </label>
                    </div>
                  </div>

                  <!-- Módulo Clientes -->
                  <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                    <h5 class="font-medium text-gray-900 dark:text-white mb-3 text-sm flex items-center">
                      👥 Clientes
                    </h5>
                    <div class="space-y-2">
                      <label class="flex items-center cursor-pointer">
                        <input
                          v-model="roleForm.permissions"
                          value="customers.view"
                          type="checkbox"
                          class="h-3 w-3 text-purple-600 focus:ring-purple-500 border-gray-300 rounded"
                        />
                        <span class="ml-2 text-xs text-gray-700 dark:text-gray-300">Ver Clientes</span>
                      </label>
                      <label class="flex items-center cursor-pointer">
                        <input
                          v-model="roleForm.permissions"
                          value="customers.create"
                          type="checkbox"
                          class="h-3 w-3 text-purple-600 focus:ring-purple-500 border-gray-300 rounded"
                        />
                        <span class="ml-2 text-xs text-gray-700 dark:text-gray-300">Crear Clientes</span>
                      </label>
                      <label class="flex items-center cursor-pointer">
                        <input
                          v-model="roleForm.permissions"
                          value="customers.edit"
                          type="checkbox"
                          class="h-3 w-3 text-purple-600 focus:ring-purple-500 border-gray-300 rounded"
                        />
                        <span class="ml-2 text-xs text-gray-700 dark:text-gray-300">Editar Clientes</span>
                      </label>
                    </div>
                  </div>

                  <!-- Módulo Inventario -->
                  <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                    <h5 class="font-medium text-gray-900 dark:text-white mb-3 text-sm flex items-center">
                      📋 Inventario
                    </h5>
                    <div class="space-y-2">
                      <label class="flex items-center cursor-pointer">
                        <input
                          v-model="roleForm.permissions"
                          value="inventory.view"
                          type="checkbox"
                          class="h-3 w-3 text-purple-600 focus:ring-purple-500 border-gray-300 rounded"
                        />
                        <span class="ml-2 text-xs text-gray-700 dark:text-gray-300">Ver Inventario</span>
                      </label>
                      <label class="flex items-center cursor-pointer">
                        <input
                          v-model="roleForm.permissions"
                          value="inventory.adjust"
                          type="checkbox"
                          class="h-3 w-3 text-purple-600 focus:ring-purple-500 border-gray-300 rounded"
                        />
                        <span class="ml-2 text-xs text-gray-700 dark:text-gray-300">Ajustar Inventario</span>
                      </label>
                    </div>
                  </div>

                  <!-- Módulo Reportes -->
                  <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                    <h5 class="font-medium text-gray-900 dark:text-white mb-3 text-sm flex items-center">
                      📈 Reportes
                    </h5>
                    <div class="space-y-2">
                      <label class="flex items-center cursor-pointer">
                        <input
                          v-model="roleForm.permissions"
                          value="reports.view"
                          type="checkbox"
                          class="h-3 w-3 text-purple-600 focus:ring-purple-500 border-gray-300 rounded"
                        />
                        <span class="ml-2 text-xs text-gray-700 dark:text-gray-300">Ver Reportes</span>
                      </label>
                      <label class="flex items-center cursor-pointer">
                        <input
                          v-model="roleForm.permissions"
                          value="reports.sales"
                          type="checkbox"
                          class="h-3 w-3 text-purple-600 focus:ring-purple-500 border-gray-300 rounded"
                        />
                        <span class="ml-2 text-xs text-gray-700 dark:text-gray-300">Reportes de Ventas</span>
                      </label>
                    </div>
                  </div>

                  <!-- Módulo Administración -->
                  <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                    <h5 class="font-medium text-gray-900 dark:text-white mb-3 text-sm flex items-center">
                      ⚙️ Administración
                    </h5>
                    <div class="space-y-2">
                      <label class="flex items-center cursor-pointer">
                        <input
                          v-model="roleForm.permissions"
                          value="users.view"
                          type="checkbox"
                          class="h-3 w-3 text-purple-600 focus:ring-purple-500 border-gray-300 rounded"
                        />
                        <span class="ml-2 text-xs text-gray-700 dark:text-gray-300">Ver Usuarios</span>
                      </label>
                      <label class="flex items-center cursor-pointer">
                        <input
                          v-model="roleForm.permissions"
                          value="users.create"
                          type="checkbox"
                          class="h-3 w-3 text-purple-600 focus:ring-purple-500 border-gray-300 rounded"
                        />
                        <span class="ml-2 text-xs text-gray-700 dark:text-gray-300">Crear Usuarios</span>
                      </label>
                      <label class="flex items-center cursor-pointer">
                        <input
                          v-model="roleForm.permissions"
                          value="roles.view"
                          type="checkbox"
                          class="h-3 w-3 text-purple-600 focus:ring-purple-500 border-gray-300 rounded"
                        />
                        <span class="ml-2 text-xs text-gray-700 dark:text-gray-300">Ver Roles</span>
                      </label>
                      <label class="flex items-center cursor-pointer">
                        <input
                          v-model="roleForm.permissions"
                          value="roles.create"
                          type="checkbox"
                          class="h-3 w-3 text-purple-600 focus:ring-purple-500 border-gray-300 rounded"
                        />
                        <span class="ml-2 text-xs text-gray-700 dark:text-gray-300">Crear Roles</span>
                      </label>
                    </div>
                  </div>
                </div>
              </template>
            </div>
          </div>
        </div>

        <!-- Botones del Modal -->
        <div class="px-4 py-3 border-t border-gray-200 dark:border-gray-700 flex justify-end space-x-3">
          <button
            @click="closeRoleModal"
            class="px-3 py-2 text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-lg transition-colors text-sm"
          >
            Cancelar
          </button>
          <button
            @click="saveRole"
            class="px-3 py-2 bg-gradient-to-r from-purple-500 to-purple-600 hover:from-purple-600 hover:to-purple-700 text-white rounded-lg font-semibold transition-all duration-200 text-sm"
          >
            {{ editingRole ? 'Actualizar Rol' : 'Crear Rol' }}
          </button>
        </div>
      </div>
    </div>

    <!-- Modal de Detalles del Rol -->
    <div
      v-if="showDetailsModal && selectedRole"
      class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50"
      @click.self="showDetailsModal = false"
    >
      <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xl max-w-2xl w-full">
        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
          <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
            Detalles del Rol: {{ selectedRole.name }}
          </h3>
        </div>

        <div class="p-6 space-y-4">
          <div>
            <h4 class="font-medium text-gray-900 dark:text-white mb-2">Descripción</h4>
            <p class="text-gray-600 dark:text-gray-400">{{ selectedRole.description }}</p>
          </div>

          <div>
            <h4 class="font-medium text-gray-900 dark:text-white mb-2">
              Permisos Asignados ({{ getPermissionsCount(selectedRole) }})
            </h4>
            <div class="space-y-3">
              <template v-if="selectedRole.permissions.includes('all')">
                <div class="bg-purple-50 dark:bg-purple-900/20 p-3 rounded-lg">
                  <span class="text-purple-700 dark:text-purple-300 font-medium">
                    🔓 Acceso Total al Sistema
                  </span>
                  <p class="text-xs text-purple-600 dark:text-purple-400 mt-1">
                    Este rol tiene permisos completos para todos los módulos
                  </p>
                </div>
              </template>
              <template v-else>
                <div
                  v-for="category in moduleCategories"
                  :key="category"
                  class="border border-gray-200 dark:border-gray-700 rounded-lg p-3"
                >
                  <h5 class="font-medium text-gray-900 dark:text-white mb-2">{{ category }}</h5>
                  <div class="grid grid-cols-2 gap-2">
                    <div
                      v-for="module in getModulesByCategory(category)"
                      :key="module.id"
                      class="flex items-center space-x-2"
                    >
                      <div
                        :class="[
                          'w-2 h-2 rounded-full',
                          selectedRole.permissions.includes(module.id) 
                            ? 'bg-green-500' 
                            : 'bg-gray-300 dark:bg-gray-600'
                        ]"
                      ></div>
                      <span
                        :class="[
                          'text-xs',
                          selectedRole.permissions.includes(module.id)
                            ? 'text-gray-900 dark:text-white'
                            : 'text-gray-400 dark:text-gray-500'
                        ]"
                      >
                        {{ module.name }}
                      </span>
                    </div>
                  </div>
                </div>
              </template>
            </div>
          </div>
        </div>

        <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 flex justify-end">
          <button
            @click="showDetailsModal = false"
            class="px-4 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-lg transition-colors"
          >
            Cerrar
          </button>
        </div>
      </div>
    </div>

  </div>
</template>

<script setup>
import { ref, computed, reactive, onMounted } from 'vue'
import rolesService from '../services/rolesService.js'

// Props
const props = defineProps({
  modules: {
    type: Array,
    default: () => []
  }
})

// Estado reactivo
const showRoleModal = ref(false)
const showDetailsModal = ref(false)
const editingRole = ref(null)
const selectedRole = ref(null)
const hasFullAccess = ref(false)
const loading = ref(false)
const roles = ref([])
const availablePermissions = ref([])

// Formulario del rol
const roleForm = reactive({
  name: '',
  description: '',
  permissions: [],
  active: true
})

// Computadas
const activeRoles = computed(() => 
  (Array.isArray(roles.value) ? roles.value : []).filter(role => role.active).length
)

const totalUsers = computed(() => 
  (Array.isArray(roles.value) ? roles.value : []).reduce((sum, role) => sum + (role.users_count || 0), 0)
)

const moduleCategories = computed(() => 
  [...new Set((Array.isArray(availablePermissions.value) ? availablePermissions.value : []).map(module => module.category))]
)

// Métodos para cargar datos
const loadRoles = async () => {
  loading.value = true
  try {
    const response = await rolesService.getAllRoles()
    // Asegurar que siempre sea un array
    if (response && response.data) {
      roles.value = Array.isArray(response.data) ? response.data : []
    } else if (Array.isArray(response)) {
      roles.value = response
    } else {
      roles.value = []
    }
  } catch (error) {
    console.error('Error loading roles:', error)
    // Fallback con roles básicos si falla la API
    roles.value = [
      {
        id: 1,
        name: 'Administrador',
        description: 'Acceso completo a todas las funciones',
        permissions: ['all'],
        users_count: 1,
        active: true
      },
      {
        id: 2,
        name: 'Cajero',
        description: 'Acceso al punto de venta',
        permissions: ['pos', 'sales'],
        users_count: 0,
        active: true
      },
      {
        id: 3,
        name: 'Vendedor',
        description: 'Acceso a ventas y clientes',
        permissions: ['sales', 'customers'],
        users_count: 0,
        active: true
      }
    ]
  } finally {
    loading.value = false
  }
}

const loadAvailablePermissions = async () => {
  try {
    const permissions = await rolesService.getAvailablePermissions()
    // Asegurar que siempre sea un array
    if (permissions && permissions.data) {
      availablePermissions.value = Array.isArray(permissions.data) ? permissions.data : []
    } else if (Array.isArray(permissions)) {
      availablePermissions.value = permissions
    } else {
      availablePermissions.value = []
    }
  } catch (error) {
    console.error('Error loading permissions:', error)
    // Usar permisos por defecto como fallback
    availablePermissions.value = [
      { key: 'POS_ACCESS', name: 'Acceso al punto de venta', category: 'pos' },
      { key: 'PRODUCTS_VIEW', name: 'Ver productos', category: 'productos' },
      { key: 'USERS_MANAGE', name: 'Gestionar usuarios', category: 'admin' },
      { key: 'ROLES_MANAGE', name: 'Gestionar roles', category: 'admin' }
    ]
  }
}

// Métodos
const getModulesByCategory = (category) => {
  return availablePermissions.value.filter(module => module.category === category)
}

const getRoleColor = (roleName) => {
  const colors = {
    'Administrador': 'bg-purple-500',
    'Cajera': 'bg-blue-500',
    'Supervisor': 'bg-green-500',
    'default': 'bg-gray-500'
  }
  return colors[roleName] || colors.default
}

const getPermissionName = (permission) => {
  const module = availablePermissions.value.find(m => m.id === permission)
  return module ? module.name : permission
}

const getPermissionsCount = (role) => {
  if (role.permissions.includes('all')) {
    return `${availablePermissions.value.length} (Total)`
  }
  return role.permissions.length
}

// Crear o actualizar rol
const saveRole = async () => {
  loading.value = true
  try {
    if (editingRole.value) {
      // Actualizar rol existente
      await rolesService.updateRole(editingRole.value.id, roleForm)
    } else {
      // Crear nuevo rol
      await rolesService.createRole(roleForm)
    }
    
    // Recargar roles
    await loadRoles()
    
    // Cerrar modal y limpiar formulario
    closeRoleModal()
    
    // Mostrar mensaje de éxito
    alert(editingRole.value ? 'Rol actualizado exitosamente' : 'Rol creado exitosamente')
    
  } catch (error) {
    console.error('Error saving role:', error)
    alert('Error al guardar el rol: ' + (error.response?.data?.message || error.message))
  } finally {
    loading.value = false
  }
}

// Eliminar rol
const deleteRole = async (role) => {
  if (!confirm(`¿Está seguro de eliminar el rol "${role.name}"?`)) {
    return
  }
  
  loading.value = true
  try {
    await rolesService.deleteRole(role.id)
    await loadRoles()
    alert('Rol eliminado exitosamente')
  } catch (error) {
    console.error('Error deleting role:', error)
    alert('Error al eliminar el rol: ' + (error.response?.data?.message || error.message))
  } finally {
    loading.value = false
  }
}

// Abrir modal para crear rol
const openCreateModal = () => {
  editingRole.value = null
  Object.assign(roleForm, {
    name: '',
    description: '',
    permissions: [],
    active: true
  })
  showRoleModal.value = true
}

// Abrir modal para editar rol
const openEditModal = (role) => {
  editingRole.value = role
  Object.assign(roleForm, {
    name: role.name,
    description: role.description,
    permissions: [...role.permissions],
    active: role.active
  })
  showRoleModal.value = true
}

// Cerrar modal
const closeRoleModal = () => {
  showRoleModal.value = false
  editingRole.value = null
  Object.assign(roleForm, {
    name: '',
    description: '',
    permissions: [],
    active: true
  })
}

const viewRoleDetails = (role) => {
  selectedRole.value = role
  showDetailsModal.value = true
}

const editRole = (role) => {
  editingRole.value = role
  roleForm.name = role.name
  roleForm.description = role.description
  roleForm.permissions = [...role.permissions]
  roleForm.active = role.active
  showRoleModal.value = true
}

const showRoleDetails = (role) => {
  selectedRole.value = role
  showDetailsModal.value = true
}

// Cambiar estado del rol
const toggleRoleStatus = async (role) => {
  loading.value = true
  try {
    await rolesService.updateRole(role.id, {
      ...role,
      active: !role.active
    })
    await loadRoles()
    alert(`Rol ${role.active ? 'desactivado' : 'activado'} exitosamente`)
  } catch (error) {
    console.error('Error toggling role status:', error)
    alert('Error al cambiar el estado del rol')
  } finally {
    loading.value = false
  }
}

// Métodos de toggles
const togglePermission = (permissionId) => {
  const index = roleForm.permissions.indexOf(permissionId)
  if (index > -1) {
    roleForm.permissions.splice(index, 1)
  } else {
    roleForm.permissions.push(permissionId)
  }
}

const toggleFullAccess = () => {
  if (hasFullAccess.value) {
    // Si se activa acceso total, agregar todos los permisos
    roleForm.permissions = [
      'dashboard.view', 'dashboard.analytics',
      'products.view', 'products.create', 'products.edit', 'products.delete', 'products.import', 'products.export',
      'categories.view', 'categories.create', 'categories.edit', 'categories.delete',
      'inventory.view', 'inventory.movements', 'inventory.adjust', 'inventory.reports',
      'sales.view', 'sales.create', 'sales.edit', 'sales.delete', 'sales.refund', 'sales.reports',
      'customers.view', 'customers.create', 'customers.edit', 'customers.delete', 'customers.reports',
      'suppliers.view', 'suppliers.create', 'suppliers.edit', 'suppliers.delete',
      'reports.view', 'reports.sales', 'reports.inventory', 'reports.financial',
      'users.view', 'users.create', 'users.edit', 'users.delete',
      'roles.view', 'roles.create', 'roles.edit', 'roles.delete',
      'settings.view', 'settings.edit', 'settings.backup'
    ]
  } else {
    // Si se desactiva, limpiar permisos
    roleForm.permissions = []
  }
}

// Lifecycle
onMounted(async () => {
  await Promise.all([
    loadRoles(),
    loadAvailablePermissions()
  ])
})
</script>