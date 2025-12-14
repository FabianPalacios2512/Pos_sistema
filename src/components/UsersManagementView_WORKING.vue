<template>
  <div class="min-h-screen bg-gradient-to-br from-slate-50 to-blue-50/30 font-sans">
    <div class="p-4 lg:p-6 space-y-6 animate-fade-in">
      
      <!-- 🎯 Header Elegante y Profesional OBLIGATORIO -->
      <div class="flex items-center justify-between pb-4 border-b border-gray-300">
        <div class="flex items-center space-x-4">
          <div class="w-12 h-12 bg-black rounded-xl flex items-center justify-center shadow-lg">
            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
            </svg>
          </div>
          <div>
            <h1 class="text-2xl font-bold text-gray-900">Usuarios</h1>
            <p class="text-sm text-gray-600">Administración de empleados y control de roles del sistema</p>
          </div>
        </div>
        
        <div class="flex items-center space-x-3">
          <!-- Botón Secundario (Neutro Slate) -->
          <button @click="refreshData"
                  class="px-4 py-2.5 bg-slate-50 hover:bg-slate-100 text-slate-600 text-sm font-semibold rounded-xl border border-slate-200 shadow-sm transition-all duration-200 flex items-center space-x-2 hover:shadow-md hover:border-slate-300">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
            </svg>
            <span>Actualizar</span>
          </button>
          
          <!-- Botón Principal Dinámico (Negro, consistente con otras vistas) -->
          <button @click="activeTab === 'users' ? openCreateUserModal() : openCreateRoleModal()"
                  class="px-5 py-2.5 bg-black hover:bg-gray-900 text-white text-sm font-bold rounded-xl shadow-lg transition-all duration-300 transform hover:scale-105 flex items-center space-x-2">
            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            <span>{{ activeTab === 'users' ? 'Nuevo Usuario' : 'Nuevo Rol' }}</span>
          </button>
        </div>
      </div>
      
      <!-- Tabs Navigation Compacto y Profesional -->
      <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-1 inline-flex">
        <button @click="activeTab = 'users'"
                :class="[
                  'px-4 py-2 text-sm font-semibold rounded-lg transition-all duration-200 flex items-center space-x-2',
                  activeTab === 'users' 
                    ? 'bg-blue-600 text-white shadow-sm' 
                    : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50'
                ]">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
          </svg>
          <span>Usuarios</span>
          <span :class="[
            'px-2 py-0.5 text-xs font-semibold rounded-full',
            activeTab === 'users' ? 'bg-blue-500 text-white' : 'bg-gray-200 text-gray-700'
          ]">{{ users.length }}</span>
        </button>
        
        <button @click="activeTab = 'roles'"
                :class="[
                  'px-4 py-2 text-sm font-semibold rounded-lg transition-all duration-200 flex items-center space-x-2',
                  activeTab === 'roles' 
                    ? 'bg-purple-600 text-white shadow-sm' 
                    : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50'
                ]">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
          </svg>
          <span>Roles</span>
          <span :class="[
            'px-2 py-0.5 text-xs font-semibold rounded-full',
            activeTab === 'roles' ? 'bg-purple-500 text-white' : 'bg-gray-200 text-gray-700'
          ]">{{ roles.length }}</span>
        </button>
      </div>
      
      <!-- Métricas Principales Empresariales según Tab Activo -->
      <div v-if="activeTab === 'users'" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        
        <!-- Total Usuarios (Azul Empresarial) -->
        <div class="bg-white rounded-2xl p-5 border border-gray-300 hover:border-gray-400 transition-all duration-200 hover:shadow-lg">
          <div class="flex items-center space-x-4">
            <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center flex-shrink-0">
              <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
              </svg>
            </div>
            <div class="flex-1 min-w-0">
              <div class="flex items-center justify-between mb-2">
                <h3 class="text-sm font-semibold text-gray-700">Total Usuarios</h3>
                <span class="text-xs font-semibold text-blue-700 bg-blue-100 px-2 py-1 rounded-full border border-blue-200">
                  TOTAL
                </span>
              </div>
              <p class="text-2xl font-bold text-gray-900 mb-1">{{ users.length }}</p>
              <p class="text-sm text-gray-500">Empleados registrados</p>
            </div>
          </div>
        </div>
        
        <!-- Usuarios Activos (Verde Empresarial) -->
        <div class="bg-white rounded-2xl p-5 border border-gray-300 hover:border-gray-400 transition-all duration-200 hover:shadow-lg">
          <div class="flex items-center space-x-4">
            <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center flex-shrink-0">
              <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
              </svg>
            </div>
            <div class="flex-1 min-w-0">
              <div class="flex items-center justify-between mb-2">
                <h3 class="text-sm font-semibold text-gray-700">Usuarios Activos</h3>
                <span class="text-xs font-semibold text-green-700 bg-green-100 px-2 py-1 rounded-full border border-green-200">
                  ACTIVOS
                </span>
              </div>
              <p class="text-2xl font-bold text-gray-900 mb-1">{{ activeUsersCount }}</p>
              <p class="text-sm text-gray-500">Con acceso habilitado</p>
            </div>
          </div>
        </div>
        
        <!-- Usuarios Inactivos (Rosa Empresarial) -->
        <div class="bg-white rounded-2xl p-5 border border-gray-300 hover:border-gray-400 transition-all duration-200 hover:shadow-lg">
          <div class="flex items-center space-x-4">
            <div class="w-12 h-12 bg-rose-100 rounded-xl flex items-center justify-center flex-shrink-0">
              <svg class="w-6 h-6 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636m12.728 12.728L18.364 5.636M5.636 18.364l12.728-12.728"/>
              </svg>
            </div>
            <div class="flex-1 min-w-0">
              <div class="flex items-center justify-between mb-2">
                <h3 class="text-sm font-semibold text-gray-700">Inactivos</h3>
                <span class="text-xs font-semibold text-rose-700 bg-rose-100 px-2 py-1 rounded-full border border-rose-200">
                  BLOQUEADOS
                </span>
              </div>
              <p class="text-2xl font-bold text-gray-900 mb-1">{{ inactiveUsersCount }}</p>
              <p class="text-sm text-gray-500">Sin acceso al sistema</p>
            </div>
          </div>
        </div>
        
        <!-- Roles Asignados (Púrpura Empresarial) -->
        <div class="bg-white rounded-2xl p-5 border border-gray-300 hover:border-gray-400 transition-all duration-200 hover:shadow-lg">
          <div class="flex items-center space-x-4">
            <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center flex-shrink-0">
              <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
              </svg>
            </div>
            <div class="flex-1 min-w-0">
              <div class="flex items-center justify-between mb-2">
                <h3 class="text-sm font-semibold text-gray-700">Roles Activos</h3>
                <span class="text-xs font-semibold text-purple-700 bg-purple-100 px-2 py-1 rounded-full border border-purple-200">
                  ROLES
                </span>
              </div>
              <p class="text-2xl font-bold text-gray-900 mb-1">{{ roles.length }}</p>
              <p class="text-sm text-gray-500">Permisos configurados</p>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Métricas de Roles -->
      <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        
        <!-- Total Roles (Púrpura Empresarial) -->
        <div class="bg-white rounded-2xl p-5 border border-gray-300 hover:border-gray-400 transition-all duration-200 hover:shadow-lg">
          <div class="flex items-center space-x-4">
            <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center flex-shrink-0">
              <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
              </svg>
            </div>
            <div class="flex-1 min-w-0">
              <div class="flex items-center justify-between mb-2">
                <h3 class="text-sm font-semibold text-gray-700">Total Roles</h3>
                <span class="text-xs font-semibold text-purple-700 bg-purple-100 px-2 py-1 rounded-full border border-purple-200">
                  TOTAL
                </span>
              </div>
              <p class="text-2xl font-bold text-gray-900 mb-1">{{ roles.length }}</p>
              <p class="text-sm text-gray-500">Roles configurados</p>
            </div>
          </div>
        </div>
        
        <!-- Permisos Únicos (Azul Empresarial) -->
        <div class="bg-white rounded-2xl p-5 border border-gray-300 hover:border-gray-400 transition-all duration-200 hover:shadow-lg">
          <div class="flex items-center space-x-4">
            <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center flex-shrink-0">
              <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
              </svg>
            </div>
            <div class="flex-1 min-w-0">
              <div class="flex items-center justify-between mb-2">
                <h3 class="text-sm font-semibold text-gray-700">Permisos</h3>
                <span class="text-xs font-semibold text-blue-700 bg-blue-100 px-2 py-1 rounded-full border border-blue-200">
                  ÚNICOS
                </span>
              </div>
              <p class="text-2xl font-bold text-gray-900 mb-1">{{ uniquePermissions.length }}</p>
              <p class="text-sm text-gray-500">Accesos disponibles</p>
            </div>
          </div>
        </div>
        
        <!-- Usuarios con Rol Admin (Ámbar Empresarial) -->
        <div class="bg-white rounded-2xl p-5 border border-gray-300 hover:border-gray-400 transition-all duration-200 hover:shadow-lg">
          <div class="flex items-center space-x-4">
            <div class="w-12 h-12 bg-amber-100 rounded-xl flex items-center justify-center flex-shrink-0">
              <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
              </svg>
            </div>
            <div class="flex-1 min-w-0">
              <div class="flex items-center justify-between mb-2">
                <h3 class="text-sm font-semibold text-gray-700">Administradores</h3>
                <span class="text-xs font-semibold text-amber-700 bg-amber-100 px-2 py-1 rounded-full border border-amber-200">
                  ADMIN
                </span>
              </div>
              <p class="text-2xl font-bold text-gray-900 mb-1">{{ adminUsersCount }}</p>
              <p class="text-sm text-gray-500">Control total</p>
            </div>
          </div>
        </div>
        
        <!-- Roles Personalizados (Verde Empresarial) -->
        <div class="bg-white rounded-2xl p-5 border border-gray-300 hover:border-gray-400 transition-all duration-200 hover:shadow-lg">
          <div class="flex items-center space-x-4">
            <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center flex-shrink-0">
              <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4"/>
              </svg>
            </div>
            <div class="flex-1 min-w-0">
              <div class="flex items-center justify-between mb-2">
                <h3 class="text-sm font-semibold text-gray-700">Personalizados</h3>
                <span class="text-xs font-semibold text-green-700 bg-green-100 px-2 py-1 rounded-full border border-green-200">
                  CUSTOM
                </span>
              </div>
              <p class="text-2xl font-bold text-gray-900 mb-1">{{ customRoles.length }}</p>
              <p class="text-sm text-gray-500">Roles específicos</p>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Contenido según Tab -->
      <div v-show="activeTab === 'users'">
        <!-- TAB DE USUARIOS -->
        
        <!-- Panel de Filtros Empresarial -->
        <div class="bg-white rounded-xl shadow-sm p-4 border border-gray-200">
          <div class="flex flex-wrap items-center gap-4">
            
            <!-- Búsqueda -->
            <div class="flex-1 min-w-64 relative">
              <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
              </svg>
              <input v-model="searchUsers"
                     type="text"
                     placeholder="Buscar usuarios..." 
                     class="w-full pl-10 pr-3 py-2.5 text-sm border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
              />
            </div>
            
            <!-- Filtro Rol -->
            <select v-model="filterRole"
                    class="pl-3 pr-8 py-2.5 text-sm border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 min-w-40 bg-white">
              <option value="">Todos los Roles</option>
              <option v-for="role in roles" :key="role.id" :value="role.id">{{ role.name }}</option>
            </select>
            
            <!-- Filtro Estado -->
            <select v-model="filterStatus"
                    class="pl-3 pr-8 py-2.5 text-sm border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 min-w-36 bg-white">
              <option value="">Todos los Estados</option>
              <option value="active">Activos</option>
              <option value="inactive">Inactivos</option>
            </select>
            
            <!-- Botón Limpiar Filtros -->
            <button @click="clearUsersFilters"
                    class="p-2.5 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-xl transition-colors duration-200"
                    title="Limpiar filtros">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
            </svg>
            </button>
          </div>
        </div>
        
        <!-- Tabla de Usuarios Empresarial -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
          <!-- Header de tabla -->
          <div class="bg-gray-50 border-b border-gray-200 px-4 py-3 flex items-center justify-between">
            <div>
              <h2 class="text-base font-bold text-gray-900">Lista de Usuarios</h2>
              <p class="text-xs text-gray-500 mt-0.5">{{ filteredUsers.length }} usuarios encontrados</p>
            </div>
          </div>
          
          <!-- Tabla -->
          <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
              <thead class="bg-gray-50">
                <tr>
                  <th class="px-3 py-2 text-left text-xs font-bold text-gray-600 uppercase tracking-wide">Usuario</th>
                  <th class="px-3 py-2 text-left text-xs font-bold text-gray-600 uppercase tracking-wide">Contacto</th>
                  <th class="px-3 py-2 text-left text-xs font-bold text-gray-600 uppercase tracking-wide">Rol</th>
                  <th class="px-3 py-2 text-left text-xs font-bold text-gray-600 uppercase tracking-wide">Estado</th>
                  <th class="px-3 py-2 text-left text-xs font-bold text-gray-600 uppercase tracking-wide">Último Acceso</th>
                  <th class="px-3 py-2 text-left text-xs font-bold text-gray-600 uppercase tracking-wide">Acciones</th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200">
                <tr v-for="user in filteredUsers" :key="user.id" class="hover:bg-gray-50 transition-colors">
                  <!-- Usuario -->
                  <td class="px-3 py-3">
                    <div class="flex items-center gap-3">
                      <div class="w-10 h-10 rounded-lg flex items-center justify-center flex-shrink-0"
                           :style="{ backgroundColor: getUserColor(user.name) + '20' }">
                        <span class="font-bold text-sm" :style="{ color: getUserColor(user.name) }">
                          {{ user.name.charAt(0).toUpperCase() }}
                        </span>
                      </div>
                      <div class="min-w-0">
                        <div class="text-sm font-semibold text-gray-900 truncate">{{ user.name }}</div>
                        <div class="text-xs text-gray-500">CC: {{ user.cc }}</div>
                      </div>
                    </div>
                  </td>
                  
                  <!-- Contacto -->
                  <td class="px-3 py-3">
                    <div class="text-sm text-gray-900">{{ user.email }}</div>
                    <div class="text-xs text-gray-500" v-if="user.phone">{{ user.phone }}</div>
                  </td>
                  
                  <!-- Rol -->
                  <td class="px-3 py-3">
                    <span v-if="user.role" class="px-2 py-1 rounded-lg text-xs font-semibold"
                          :class="getRoleBadgeClass(user.role.name)">
                      {{ user.role.name }}
                    </span>
                    <span v-else class="px-2 py-1 bg-gray-100 text-gray-600 rounded-lg text-xs font-semibold">
                      Sin Rol
                    </span>
                  </td>
                  
                  <!-- Estado -->
                  <td class="px-3 py-3">
                    <button @click="toggleUserStatus(user)"
                            :class="user.active ? 'bg-green-100 text-green-700 hover:bg-green-200' : 'bg-red-100 text-red-700 hover:bg-red-200'"
                            class="px-2 py-0.5 rounded-full text-xs font-semibold inline-flex items-center transition-colors">
                      <span class="w-1.5 h-1.5 rounded-full mr-1.5" 
                            :class="user.active ? 'bg-green-500' : 'bg-red-500'"></span>
                      {{ user.active ? 'Activo' : 'Inactivo' }}
                    </button>
                  </td>
                  
                  <!-- Último Acceso -->
                  <td class="px-3 py-3">
                    <div class="text-xs text-gray-600" v-if="user.last_login">
                      {{ formatDate(user.last_login) }}
                    </div>
                    <div class="text-xs text-gray-400" v-else>Nunca</div>
                  </td>
                  
                  <!-- Acciones -->
                  <td class="px-3 py-3">
                    <div class="flex items-center gap-1.5">
                      <button @click="editUser(user)"
                              class="p-1.5 bg-amber-50 hover:bg-amber-100 text-amber-600 border border-amber-200 rounded-lg transition-all hover:shadow-sm"
                              title="Editar">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                      </button>
                      <button @click="changePassword(user)"
                              class="p-1.5 bg-blue-50 hover:bg-blue-100 text-blue-600 border border-blue-200 rounded-lg transition-all hover:shadow-sm"
                              title="Cambiar contraseña">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                        </svg>
                      </button>
                      <button @click="deleteUser(user)"
                              class="p-1.5 bg-red-50 hover:bg-red-100 text-red-600 border border-red-200 rounded-lg transition-all hover:shadow-sm"
                              title="Eliminar">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                      </button>
                    </div>
                  </td>
                </tr>
                
                <!-- Estado vacío -->
                <tr v-if="filteredUsers.length === 0">
                  <td colspan="6" class="px-4 py-12 text-center">
                    <div class="flex flex-col items-center space-y-3">
                      <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center">
                        <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
                        </svg>
                      </div>
                      <div>
                        <p class="text-sm font-semibold text-gray-700">No hay usuarios</p>
                        <p class="text-xs text-gray-500 mt-1">No se encontraron usuarios con los filtros aplicados</p>
                      </div>
                      <button @click="clearUsersFilters" class="px-3 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg">
                        Limpiar Filtros
                      </button>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
      
      <div v-show="activeTab === 'roles'">
        <!-- TAB DE ROLES -->
        
        <!-- Panel de Filtros Empresarial -->
        <div class="bg-white rounded-xl shadow-sm p-4 border border-gray-200">
          <div class="flex flex-wrap items-center gap-4">
            
            <!-- Búsqueda -->
            <div class="flex-1 min-w-64 relative">
              <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
              </svg>
              <input v-model="searchRoles"
                     type="text"
                     placeholder="Buscar roles..." 
                     class="w-full pl-10 pr-3 py-2.5 text-sm border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-200"
              />
            </div>
            
            <!-- Filtro Estado -->
            <select v-model="filterRoleStatus"
                    class="pl-3 pr-8 py-2.5 text-sm border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 min-w-36 bg-white">
              <option value="">Todos los Estados</option>
              <option value="active">Activos</option>
              <option value="inactive">Inactivos</option>
            </select>
            
            <!-- Botón Limpiar Filtros -->
            <button @click="clearRolesFilters"
                    class="p-2.5 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-xl transition-colors duration-200"
                    title="Limpiar filtros">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
              </svg>
            </button>
          </div>
        </div>
        
        <!-- Tabla de Roles Empresarial -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
          <!-- Header de tabla -->
          <div class="bg-gray-50 border-b border-gray-200 px-4 py-3 flex items-center justify-between">
            <div>
              <h2 class="text-base font-bold text-gray-900">Roles y Permisos</h2>
              <p class="text-xs text-gray-500 mt-0.5">{{ filteredRoles.length }} roles configurados</p>
            </div>
          </div>
          
          <!-- Tabla -->
          <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
              <thead class="bg-gray-50">
                <tr>
                  <th class="px-3 py-2 text-left text-xs font-bold text-gray-600 uppercase tracking-wide">Rol</th>
                  <th class="px-3 py-2 text-left text-xs font-bold text-gray-600 uppercase tracking-wide">Descripción</th>
                  <th class="px-3 py-2 text-left text-xs font-bold text-gray-600 uppercase tracking-wide">Usuarios</th>
                  <th class="px-3 py-2 text-left text-xs font-bold text-gray-600 uppercase tracking-wide">Permisos</th>
                  <th class="px-3 py-2 text-left text-xs font-bold text-gray-600 uppercase tracking-wide">Estado</th>
                  <th class="px-3 py-2 text-left text-xs font-bold text-gray-600 uppercase tracking-wide">Acciones</th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200">
                <tr v-for="role in filteredRoles" :key="role.id" class="hover:bg-gray-50 transition-colors">
                  <!-- Rol -->
                  <td class="px-3 py-3">
                    <div class="flex items-center gap-3">
                      <div class="w-10 h-10 rounded-lg flex items-center justify-center flex-shrink-0"
                           :style="{ backgroundColor: getRoleColor(role.name) + '20' }">
                        <svg class="w-5 h-5" :style="{ color: getRoleColor(role.name) }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                      </div>
                      <div class="min-w-0">
                        <div class="text-sm font-semibold text-gray-900">{{ role.name }}</div>
                      </div>
                    </div>
                  </td>
                  
                  <!-- Descripción -->
                  <td class="px-3 py-3">
                    <div class="text-sm text-gray-600 max-w-xs truncate" :title="role.description">
                      {{ role.description || 'Sin descripción' }}
                    </div>
                  </td>
                  
                  <!-- Usuarios -->
                  <td class="px-3 py-3">
                    <div class="flex items-center gap-2">
                      <span class="text-sm font-semibold text-gray-900">{{ role.users_count || 0 }}</span>
                      <button v-if="role.users_count > 0"
                              @click="viewRoleUsers(role)"
                              class="text-xs text-blue-600 hover:text-blue-700 font-medium">
                        Ver usuarios
                      </button>
                    </div>
                  </td>
                  
                  <!-- Permisos -->
                  <td class="px-3 py-3">
                    <span class="px-2 py-1 rounded-lg text-xs font-semibold"
                          :class="getPermissionsBadgeClass(role.permissions)">
                      {{ countPermissions(role.permissions) }} permisos
                    </span>
                  </td>
                  
                  <!-- Estado -->
                  <td class="px-3 py-3">
                    <span :class="role.active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'"
                          class="px-2 py-0.5 rounded-full text-xs font-semibold inline-flex items-center">
                      <span class="w-1.5 h-1.5 rounded-full mr-1.5" 
                            :class="role.active ? 'bg-green-500' : 'bg-red-500'"></span>
                      {{ role.active ? 'Activo' : 'Inactivo' }}
                    </span>
                  </td>
                  
                  <!-- Acciones -->
                  <td class="px-3 py-3">
                    <div class="flex items-center gap-1.5">
                      <button @click="viewRolePermissions(role)"
                              class="p-1.5 bg-blue-50 hover:bg-blue-100 text-blue-600 border border-blue-200 rounded-lg transition-all hover:shadow-sm"
                              title="Ver permisos">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                      </button>
                      <button @click="editRole(role)"
                              class="p-1.5 bg-amber-50 hover:bg-amber-100 text-amber-600 border border-amber-200 rounded-lg transition-all hover:shadow-sm"
                              title="Editar">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                      </button>
                      <button @click="deleteRole(role)"
                              class="p-1.5 bg-red-50 hover:bg-red-100 text-red-600 border border-red-200 rounded-lg transition-all hover:shadow-sm"
                              title="Eliminar">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                      </button>
                    </div>
                  </td>
                </tr>
                
                <!-- Estado vacío -->
                <tr v-if="filteredRoles.length === 0">
                  <td colspan="6" class="px-4 py-12 text-center">
                    <div class="flex flex-col items-center space-y-3">
                      <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center">
                        <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                      </div>
                      <div>
                        <p class="text-sm font-semibold text-gray-700">No hay roles</p>
                        <p class="text-xs text-gray-500 mt-1">No se encontraron roles con los filtros aplicados</p>
                      </div>
                      <button @click="clearRolesFilters" class="px-3 py-2 bg-purple-600 hover:bg-purple-700 text-white text-sm font-medium rounded-lg">
                        Limpiar Filtros
                      </button>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
      
    </div>
    
    <!-- MODAL CREAR/EDITAR USUARIO -->
    <div v-if="showUserModal" 
         class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50"
         @click.self="closeUserModal">
      <div class="bg-white rounded-lg p-6 max-w-2xl w-full max-h-[90vh] overflow-y-auto">
        <h3 class="text-lg font-bold text-gray-900 mb-4">
          {{ isEditing ? 'Editar Usuario' : 'Nuevo Usuario' }}
        </h3>
        
        <div class="space-y-4">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Nombre Completo *</label>
              <input v-model="userForm.name" 
                     type="text" 
                     class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                     placeholder="Juan Pérez">
            </div>
            
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Cédula *</label>
              <input v-model="userForm.cc" 
                     type="text" 
                     class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                     placeholder="1234567890">
            </div>
          </div>
          
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Email *</label>
              <input v-model="userForm.email" 
                     type="email" 
                     class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                     placeholder="juan@ejemplo.com">
            </div>
            
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Teléfono</label>
              <input v-model="userForm.phone" 
                     type="tel" 
                     class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                     placeholder="3001234567">
            </div>
          </div>
          
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Rol *</label>
              <select v-model="userForm.role_id"
                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                <option value="">Seleccionar rol...</option>
                <option v-for="role in roles" :key="role.id" :value="role.id">{{ role.name }}</option>
              </select>
            </div>
            
            <div v-if="!isEditing">
              <label class="block text-sm font-medium text-gray-700 mb-1">Contraseña *</label>
              <input v-model="userForm.password" 
                     type="password" 
                     class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                     placeholder="Mínimo 6 caracteres">
            </div>
          </div>
          
          <div class="flex items-center">
            <input v-model="userForm.active" 
                   type="checkbox" 
                   class="w-4 h-4 text-blue-600 rounded focus:ring-blue-500">
            <label class="ml-2 text-sm text-gray-700">Usuario activo</label>
          </div>
        </div>
        
        <div class="flex justify-end gap-3 mt-6 pt-4 border-t">
          <button @click="closeUserModal"
                  class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg font-medium transition-colors">
            Cancelar
          </button>
          <button @click="saveUser"
                  :disabled="loading"
                  class="px-4 py-2 bg-blue-600 hover:bg-blue-700 disabled:bg-gray-300 text-white rounded-lg font-medium transition-colors">
            {{ loading ? 'Guardando...' : (isEditing ? 'Actualizar' : 'Crear') }} Usuario
          </button>
        </div>
      </div>
    </div>
    
    <!-- MODAL CAMBIAR CONTRASEÑA -->
    <div v-if="showPasswordModal" 
         class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50"
         @click.self="closePasswordModal">
      <div class="bg-white rounded-lg p-6 max-w-md w-full">
        <h3 class="text-lg font-bold text-gray-900 mb-4">
          Cambiar Contraseña - {{ selectedUser?.name }}
        </h3>
        
        <div class="space-y-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Nueva Contraseña</label>
            <input v-model="passwordForm.password" 
                   type="password" 
                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                   placeholder="Mínimo 6 caracteres">
          </div>
          
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Confirmar Contraseña</label>
            <input v-model="passwordForm.password_confirmation" 
                   type="password" 
                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                   placeholder="Repite la contraseña">
          </div>
        </div>
        
        <div class="flex justify-end gap-3 mt-6 pt-4 border-t">
          <button @click="closePasswordModal"
                  class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg font-medium transition-colors">
            Cancelar
          </button>
          <button @click="savePassword"
                  :disabled="loading"
                  class="px-4 py-2 bg-blue-600 hover:bg-blue-700 disabled:bg-gray-300 text-white rounded-lg font-medium transition-colors">
            {{ loading ? 'Guardando...' : 'Cambiar Contraseña' }}
          </button>
        </div>
      </div>
    </div>
    
    <!-- MODAL CREAR/EDITAR ROL -->
    <div v-if="showRoleModal" 
         class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50"
         @click.self="closeRoleModal">
      <div class="bg-white rounded-lg p-6 max-w-5xl w-full max-h-[90vh] overflow-y-auto">
        <h3 class="text-lg font-bold text-gray-900 mb-4">
          {{ isEditingRole ? 'Editar Rol' : 'Nuevo Rol' }}
        </h3>
        
        <div class="space-y-6">
          <!-- Información Básica -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Nombre del Rol *</label>
              <input v-model="roleForm.name" 
                     type="text" 
                     class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500"
                     placeholder="Ej: Cajero, Gerente, Vendedor">
            </div>
            
            <div class="flex items-center">
              <input v-model="roleForm.active" 
                     type="checkbox" 
                     class="w-4 h-4 text-purple-600 rounded focus:ring-purple-500">
              <label class="ml-2 text-sm text-gray-700">Rol activo</label>
            </div>
          </div>
          
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Descripción</label>
            <textarea v-model="roleForm.description"
                      rows="2"
                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500"
                      placeholder="Descripción breve del rol y sus responsabilidades"></textarea>
          </div>
          
          <!-- Editor de Permisos -->
          <div class="border-t pt-4">
            <div class="flex items-center justify-between mb-4">
              <h4 class="text-sm font-bold text-gray-900">Permisos del Rol</h4>
              <div class="flex gap-2">
                <button @click="selectAllPermissions"
                        class="px-3 py-1.5 bg-green-100 hover:bg-green-200 text-green-700 text-xs font-semibold rounded-lg transition-colors">
                  Seleccionar Todos
                </button>
                <button @click="deselectAllPermissions"
                        class="px-3 py-1.5 bg-red-100 hover:bg-red-200 text-red-700 text-xs font-semibold rounded-lg transition-colors">
                  Deseleccionar Todos
                </button>
              </div>
            </div>
            
            <!-- Grid de módulos de permisos -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div v-for="module in permissionsModules" :key="module.id"
                   class="border border-gray-200 rounded-lg p-4 hover:border-purple-300 transition-colors">
                <!-- Header del módulo -->
                <div class="flex items-start justify-between mb-3">
                  <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center"
                         :style="{ backgroundColor: module.color + '20' }">
                      <svg class="w-4 h-4" :style="{ color: module.color }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="module.icon"/>
                      </svg>
                    </div>
                    <div>
                      <h5 class="text-sm font-bold text-gray-900">{{ module.name }}</h5>
                      <p class="text-xs text-gray-500">{{ module.permissions.length }} permisos</p>
                    </div>
                  </div>
                  <button @click="toggleModulePermissions(module.id)"
                          class="px-2 py-1 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-semibold rounded">
                    {{ isModuleSelected(module.id) ? 'Desmarcar' : 'Marcar' }} Todos
                  </button>
                </div>
                
                <!-- Lista de permisos del módulo -->
                <div class="space-y-2">
                  <label v-for="permission in module.permissions" :key="permission.id"
                         class="flex items-start gap-2 text-sm text-gray-700 hover:bg-gray-50 p-1.5 rounded cursor-pointer">
                    <input type="checkbox"
                           v-model="roleForm.permissions"
                           :value="permission.id"
                           class="mt-0.5 w-4 h-4 text-purple-600 rounded focus:ring-purple-500">
                    <div class="flex-1 min-w-0">
                      <div class="font-medium">{{ permission.name }}</div>
                      <div class="text-xs text-gray-500">{{ permission.description }}</div>
                    </div>
                  </label>
                </div>
              </div>
            </div>
            
            <!-- Resumen de permisos seleccionados -->
            <div v-if="roleForm.permissions.length > 0" class="mt-4 p-3 bg-purple-50 border border-purple-200 rounded-lg">
              <div class="flex items-center justify-between">
                <div class="text-sm">
                  <span class="font-semibold text-purple-900">{{ roleForm.permissions.length }}</span>
                  <span class="text-purple-700"> permisos seleccionados</span>
                </div>
                <button @click="roleForm.permissions = []"
                        class="text-xs text-purple-600 hover:text-purple-700 font-medium">
                  Limpiar selección
                </button>
              </div>
            </div>
          </div>
        </div>
        
        <div class="flex justify-end gap-3 mt-6 pt-4 border-t">
          <button @click="closeRoleModal"
                  class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg font-medium transition-colors">
            Cancelar
          </button>
          <button @click="saveRole"
                  :disabled="loading || !roleForm.name"
                  class="px-4 py-2 bg-purple-600 hover:bg-purple-700 disabled:bg-gray-300 text-white rounded-lg font-medium transition-colors">
            {{ loading ? 'Guardando...' : (isEditingRole ? 'Actualizar' : 'Crear') }} Rol
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import usersService from '../services/usersService.js'
import rolesService from '../services/rolesService.js'

// Estado reactivo
const activeTab = ref('users')
const loading = ref(false)
const users = ref([])
const roles = ref([])

// Filtros de usuarios
const searchUsers = ref('')
const filterRole = ref('')
const filterStatus = ref('')

// Filtros de roles
const searchRoles = ref('')
const filterRoleStatus = ref('')

// Modales
const showUserModal = ref(false)
const showPasswordModal = ref(false)
const showRoleModal = ref(false)
const isEditing = ref(false)
const isEditingRole = ref(false)
const selectedUser = ref(null)

// Formularios
const userForm = ref({
  name: '',
  email: '',
  cc: '',
  phone: '',
  password: '',
  role_id: '',
  active: true
})

const passwordForm = ref({
  password: '',
  password_confirmation: ''
})

const roleForm = ref({
  name: '',
  description: '',
  permissions: [],
  active: true
})

// =================================
// COMPUTED PROPERTIES - USUARIOS
// =================================

const filteredUsers = computed(() => {
  let filtered = users.value
  
  // Filtro por búsqueda
  if (searchUsers.value) {
    const term = searchUsers.value.toLowerCase()
    filtered = filtered.filter(u =>
      u.name.toLowerCase().includes(term) ||
      u.email.toLowerCase().includes(term) ||
      u.cc.includes(term)
    )
  }
  
  // Filtro por rol
  if (filterRole.value) {
    filtered = filtered.filter(u => u.role_id == filterRole.value)
  }
  
  // Filtro por estado
  if (filterStatus.value) {
    filtered = filtered.filter(u => {
      if (filterStatus.value === 'active') return u.active
      if (filterStatus.value === 'inactive') return !u.active
      return true
    })
  }
  
  return filtered
})

const activeUsersCount = computed(() => users.value.filter(u => u.active).length)
const inactiveUsersCount = computed(() => users.value.filter(u => !u.active).length)
const adminUsersCount = computed(() => {
  const adminRole = roles.value.find(r => r.name.toLowerCase() === 'administrador')
  return adminRole ? users.value.filter(u => u.role_id === adminRole.id).length : 0
})

// =================================
// COMPUTED PROPERTIES - ROLES
// =================================

const filteredRoles = computed(() => {
  let filtered = roles.value
  
  // Filtro por búsqueda
  if (searchRoles.value) {
    const term = searchRoles.value.toLowerCase()
    filtered = filtered.filter(r =>
      r.name.toLowerCase().includes(term) ||
      (r.description && r.description.toLowerCase().includes(term))
    )
  }
  
  // Filtro por estado
  if (filterRoleStatus.value) {
    filtered = filtered.filter(r => {
      if (filterRoleStatus.value === 'active') return r.active
      if (filterRoleStatus.value === 'inactive') return !r.active
      return true
    })
  }
  
  return filtered
})

const activeRolesCount = computed(() => roles.value.filter(r => r.users_count > 0).length)
const adminRoleUsers = computed(() => {
  const adminRole = roles.value.find(r => r.name.toLowerCase() === 'administrador')
  return adminRole ? adminRole.users_count : 0
})

// Computed para contar permisos únicos
const uniquePermissions = computed(() => {
  const allPermissions = permissionsModules.value.flatMap(module => module.permissions)
  return allPermissions
})

// Computed para roles personalizados (no admin)
const customRoles = computed(() => {
  return roles.value.filter(r => r.name.toLowerCase() !== 'administrador')
})

// Estructura de permisos por módulo - ALINEADA CON EL SIDEBAR
const permissionsModules = ref([
  {
    id: 'dashboard',
    name: 'Dashboard',
    color: '#3B82F6',
    icon: 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6',
    permissions: [
      { id: 'dashboard.view', name: 'Ver Dashboard', description: 'Acceder a la vista principal del dashboard' }
    ]
  },
  {
    id: 'pos',
    name: 'Punto de Venta',
    color: '#10B981',
    icon: 'M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z',
    permissions: [
      { id: 'pos.view', name: 'Acceder al POS', description: 'Usar el punto de venta' },
      { id: 'pos.create_sale', name: 'Crear Ventas', description: 'Realizar transacciones de venta' },
      { id: 'pos.apply_discount', name: 'Aplicar Descuentos', description: 'Aplicar descuentos en ventas' },
      { id: 'pos.cancel_sale', name: 'Cancelar Ventas', description: 'Cancelar transacciones' }
    ]
  },
  {
    id: 'invoices',
    name: 'Facturas',
    color: '#F59E0B',
    icon: 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
    permissions: [
      { id: 'invoices.view', name: 'Ver Facturas', description: 'Acceder a la lista de facturas' },
      { id: 'invoices.create', name: 'Crear Facturas', description: 'Generar nuevas facturas' },
      { id: 'invoices.edit', name: 'Editar Facturas', description: 'Modificar facturas existentes' },
      { id: 'invoices.delete', name: 'Eliminar Facturas', description: 'Eliminar facturas' },
      { id: 'invoices.print', name: 'Imprimir Facturas', description: 'Generar impresiones' }
    ]
  },
  {
    id: 'returns',
    name: 'Devoluciones',
    color: '#EF4444',
    icon: 'M16 15v-1a4 4 0 00-4-4H8m0 0l3 3m-3-3l3-3m9 14V5a2 2 0 00-2-2H6a2 2 0 00-2 2v16l4-2 4 2 4-2 4 2z',
    permissions: [
      { id: 'returns.view', name: 'Ver Devoluciones', description: 'Acceder a gestión de devoluciones' },
      { id: 'returns.create', name: 'Crear Devoluciones', description: 'Registrar nuevas devoluciones' },
      { id: 'returns.approve', name: 'Aprobar Devoluciones', description: 'Aprobar solicitudes de devolución' }
    ]
  },
  {
    id: 'products',
    name: 'Productos',
    color: '#8B5CF6',
    icon: 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4',
    permissions: [
      { id: 'products.view', name: 'Ver Productos', description: 'Acceder al catálogo de productos' },
      { id: 'products.create', name: 'Crear Productos', description: 'Agregar nuevos productos' },
      { id: 'products.edit', name: 'Editar Productos', description: 'Modificar información de productos' },
      { id: 'products.delete', name: 'Eliminar Productos', description: 'Eliminar productos' }
    ]
  },
  {
    id: 'categories',
    name: 'Categorías',
    color: '#EC4899',
    icon: 'M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z',
    permissions: [
      { id: 'categories.view', name: 'Ver Categorías', description: 'Acceder a categorías' },
      { id: 'categories.create', name: 'Crear Categorías', description: 'Agregar nuevas categorías' },
      { id: 'categories.edit', name: 'Editar Categorías', description: 'Modificar categorías' },
      { id: 'categories.delete', name: 'Eliminar Categorías', description: 'Eliminar categorías' }
    ]
  },
  {
    id: 'stock',
    name: 'Gestión de Stock',
    color: '#14B8A6',
    icon: 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01',
    permissions: [
      { id: 'stock.view', name: 'Ver Stock', description: 'Acceder al control de inventario' },
      { id: 'stock.adjust', name: 'Ajustar Stock', description: 'Realizar ajustes de inventario' },
      { id: 'stock.transfer', name: 'Transferir Stock', description: 'Transferir productos entre bodegas' }
    ]
  },
  {
    id: 'intelligent_inventory',
    name: 'Inventario IA',
    color: '#A855F7',
    icon: 'M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z',
    permissions: [
      { id: 'intelligent_inventory.view', name: 'Ver Inventario IA', description: 'Acceder a análisis inteligente de inventario' },
      { id: 'intelligent_inventory.predictions', name: 'Ver Predicciones', description: 'Ver predicciones de demanda' }
    ]
  },
  {
    id: 'customers',
    name: 'Clientes',
    color: '#0EA5E9',
    icon: 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z',
    permissions: [
      { id: 'customers.view', name: 'Ver Clientes', description: 'Acceder a la lista de clientes' },
      { id: 'customers.create', name: 'Crear Clientes', description: 'Registrar nuevos clientes' },
      { id: 'customers.edit', name: 'Editar Clientes', description: 'Modificar datos de clientes' },
      { id: 'customers.delete', name: 'Eliminar Clientes', description: 'Eliminar clientes' },
      { id: 'customers.view_history', name: 'Ver Historial', description: 'Ver historial de compras' }
    ]
  },
  {
    id: 'suppliers',
    name: 'Proveedores',
    color: '#F97316',
    icon: 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4',
    permissions: [
      { id: 'suppliers.view', name: 'Ver Proveedores', description: 'Acceder a proveedores' },
      { id: 'suppliers.create', name: 'Crear Proveedores', description: 'Registrar proveedores' },
      { id: 'suppliers.edit', name: 'Editar Proveedores', description: 'Modificar proveedores' },
      { id: 'suppliers.delete', name: 'Eliminar Proveedores', description: 'Eliminar proveedores' }
    ]
  },
  {
    id: 'users',
    name: 'Usuarios',
    color: '#6366F1',
    icon: 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z',
    permissions: [
      { id: 'users.view', name: 'Ver Usuarios', description: 'Acceder a gestión de usuarios' },
      { id: 'users.create', name: 'Crear Usuarios', description: 'Registrar nuevos usuarios' },
      { id: 'users.edit', name: 'Editar Usuarios', description: 'Modificar usuarios' },
      { id: 'users.delete', name: 'Eliminar Usuarios', description: 'Eliminar usuarios' },
      { id: 'users.change_password', name: 'Cambiar Contraseñas', description: 'Resetear contraseñas de usuarios' },
      { id: 'users.manage_roles', name: 'Gestionar Roles', description: 'Crear y editar roles y permisos' }
    ]
  },
  {
    id: 'cash_register',
    name: 'Panel Admin (Caja)',
    color: '#DC2626',
    icon: 'M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z',
    permissions: [
      { id: 'cash_register.view', name: 'Ver Caja', description: 'Acceder al módulo de caja' },
      { id: 'cash_register.open', name: 'Abrir Caja', description: 'Abrir turno de caja' },
      { id: 'cash_register.close', name: 'Cerrar Caja', description: 'Cerrar y cuadrar caja' },
      { id: 'cash_register.movements', name: 'Movimientos', description: 'Registrar ingresos/egresos' }
    ]
  },
  {
    id: 'reports',
    name: 'Reportes',
    color: '#059669',
    icon: 'M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
    permissions: [
      { id: 'reports.view', name: 'Ver Reportes', description: 'Acceder a reportes' },
      { id: 'reports.sales', name: 'Reporte de Ventas', description: 'Ver estadísticas de ventas' },
      { id: 'reports.inventory', name: 'Reporte de Inventario', description: 'Ver reportes de stock' },
      { id: 'reports.financial', name: 'Reportes Financieros', description: 'Ver reportes financieros' },
      { id: 'reports.export', name: 'Exportar Reportes', description: 'Exportar datos a Excel/PDF' }
    ]
  },
  {
    id: 'settings',
    name: 'Configuración',
    color: '#64748B',
    icon: 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z',
    permissions: [
      { id: 'settings.view', name: 'Ver Configuración', description: 'Acceder a configuración del sistema' },
      { id: 'settings.edit', name: 'Editar Configuración', description: 'Modificar configuración del sistema' },
      { id: 'settings.manage_business', name: 'Gestionar Empresa', description: 'Editar datos de la empresa' }
    ]
  }
])

// =================================
// MÉTODOS - USUARIOS
// =================================

const loadUsers = async () => {
  try {
    loading.value = true
    const response = await usersService.getAllUsers()
    console.log('✅ Usuarios cargados:', response)
    users.value = response.data || []
  } catch (error) {
    console.error('❌ Error cargando usuarios:', error)
    alert('Error al cargar usuarios')
  } finally {
    loading.value = false
  }
}

// Función para actualizar todos los datos
const refreshData = async () => {
  try {
    loading.value = true
    await Promise.all([
      loadUsers(),
      loadRoles()
    ])
    console.log('✅ Datos actualizados correctamente')
  } catch (error) {
    console.error('❌ Error actualizando datos:', error)
  } finally {
    loading.value = false
  }
}

const loadRoles = async () => {
  try {
    const response = await rolesService.getAllRoles()
    console.log('✅ Roles cargados:', response)
    roles.value = response.data || []
  } catch (error) {
    console.error('❌ Error cargando roles:', error)
  }
}

const openCreateUserModal = () => {
  isEditing.value = false
  userForm.value = {
    name: '',
    email: '',
    cc: '',
    phone: '',
    password: '',
    role_id: '',
    active: true
  }
  showUserModal.value = true
}

const editUser = (user) => {
  isEditing.value = true
  userForm.value = {
    id: user.id,
    name: user.name,
    email: user.email,
    cc: user.cc,
    phone: user.phone || '',
    role_id: user.role_id,
    active: user.active
  }
  showUserModal.value = true
}

const saveUser = async () => {
  try {
    loading.value = true
    
    if (isEditing.value) {
      await usersService.updateUser(userForm.value.id, userForm.value)
      alert('✅ Usuario actualizado exitosamente')
    } else {
      await usersService.createUser(userForm.value)
      alert('✅ Usuario creado exitosamente')
    }
    
    await loadUsers()
    closeUserModal()
  } catch (error) {
    console.error('❌ Error guardando usuario:', error)
    alert('❌ Error al guardar el usuario: ' + (error.response?.data?.message || error.message))
  } finally {
    loading.value = false
  }
}

const deleteUser = async (user) => {
  if (!confirm(`¿Estás seguro de eliminar el usuario "${user.name}"?`)) return
  
  try {
    loading.value = true
    await usersService.deleteUser(user.id)
    alert('✅ Usuario eliminado exitosamente')
    await loadUsers()
  } catch (error) {
    console.error('❌ Error eliminando usuario:', error)
    alert('❌ Error al eliminar el usuario')
  } finally {
    loading.value = false
  }
}

const toggleUserStatus = async (user) => {
  try {
    loading.value = true
    await usersService.toggleUserActive(user.id)
    alert(`✅ Usuario ${user.active ? 'desactivado' : 'activado'} exitosamente`)
    await loadUsers()
  } catch (error) {
    console.error('❌ Error cambiando estado:', error)
    alert('❌ Error al cambiar el estado del usuario')
  } finally {
    loading.value = false
  }
}

const changePassword = (user) => {
  selectedUser.value = user
  passwordForm.value = {
    password: '',
    password_confirmation: ''
  }
  showPasswordModal.value = true
}

const savePassword = async () => {
  if (passwordForm.value.password !== passwordForm.value.password_confirmation) {
    alert('❌ Las contraseñas no coinciden')
    return
  }
  
  if (passwordForm.value.password.length < 6) {
    alert('❌ La contraseña debe tener al menos 6 caracteres')
    return
  }
  
  try {
    loading.value = true
    await usersService.changePassword(selectedUser.value.id, passwordForm.value)
    alert('✅ Contraseña actualizada exitosamente')
    closePasswordModal()
  } catch (error) {
    console.error('❌ Error cambiando contraseña:', error)
    alert('❌ Error al cambiar la contraseña')
  } finally {
    loading.value = false
  }
}

const closeUserModal = () => {
  showUserModal.value = false
  isEditing.value = false
}

const closePasswordModal = () => {
  showPasswordModal.value = false
  selectedUser.value = null
}

const clearUsersFilters = () => {
  searchUsers.value = ''
  filterRole.value = ''
  filterStatus.value = ''
}

// =================================
// MÉTODOS - ROLES
// =================================

const openCreateRoleModal = () => {
  isEditingRole.value = false
  roleForm.value = {
    name: '',
    description: '',
    permissions: [],
    active: true
  }
  showRoleModal.value = true
}

const editRole = (role) => {
  isEditingRole.value = true
  roleForm.value = {
    id: role.id,
    name: role.name,
    description: role.description || '',
    permissions: Array.isArray(role.permissions) ? role.permissions : [],
    active: role.active
  }
  showRoleModal.value = true
}

const saveRole = async () => {
  if (!roleForm.value.name) {
    alert('❌ El nombre del rol es obligatorio')
    return
  }
  
  try {
    loading.value = true
    
    if (isEditingRole.value) {
      await rolesService.updateRole(roleForm.value.id, roleForm.value)
      alert('✅ Rol actualizado exitosamente')
    } else {
      await rolesService.createRole(roleForm.value)
      alert('✅ Rol creado exitosamente')
    }
    
    await loadRoles()
    closeRoleModal()
  } catch (error) {
    console.error('❌ Error guardando rol:', error)
    alert('❌ Error al guardar el rol: ' + (error.response?.data?.message || error.message))
  } finally {
    loading.value = false
  }
}

const deleteRole = async (role) => {
  if (role.users_count > 0) {
    alert(`❌ No se puede eliminar el rol "${role.name}" porque tiene ${role.users_count} usuarios asignados. Primero debes reasignar esos usuarios a otro rol.`)
    return
  }
  
  if (!confirm(`¿Estás seguro de eliminar el rol "${role.name}"?`)) return
  
  try {
    loading.value = true
    await rolesService.deleteRole(role.id)
    alert('✅ Rol eliminado exitosamente')
    await loadRoles()
    await loadUsers() // Recargar usuarios también
  } catch (error) {
    console.error('❌ Error eliminando rol:', error)
    alert('❌ Error al eliminar el rol')
  } finally {
    loading.value = false
  }
}

const viewRolePermissions = (role) => {
  const permissionsCount = countPermissions(role.permissions)
  const permissionsList = Array.isArray(role.permissions) ? role.permissions.join(', ') : 'Sin permisos'
  alert(`Permisos de "${role.name}":\n\nTotal: ${permissionsCount} permisos\n\n${permissionsList}`)
}

const viewRoleUsers = async (role) => {
  try {
    const response = await rolesService.getUsersByRole(role.id)
    const users = response.data?.users || []
    if (users.length === 0) {
      alert(`El rol "${role.name}" no tiene usuarios asignados`)
      return
    }
    const usersList = users.map(u => `• ${u.name} (${u.email})`).join('\n')
    alert(`Usuarios con rol "${role.name}":\n\n${usersList}`)
  } catch (error) {
    console.error('❌ Error obteniendo usuarios del rol:', error)
    alert('❌ Error al cargar usuarios del rol')
  }
}

const clearRolesFilters = () => {
  searchRoles.value = ''
  filterRoleStatus.value = ''
}

const selectAllPermissions = () => {
  const allPermissions = []
  permissionsModules.value.forEach(module => {
    module.permissions.forEach(permission => {
      allPermissions.push(permission.id)
    })
  })
  roleForm.value.permissions = allPermissions
}

const deselectAllPermissions = () => {
  roleForm.value.permissions = []
}

const toggleModulePermissions = (moduleId) => {
  const module = permissionsModules.value.find(m => m.id === moduleId)
  if (!module) return
  
  const modulePermissionIds = module.permissions.map(p => p.id)
  const allSelected = modulePermissionIds.every(id => roleForm.value.permissions.includes(id))
  
  if (allSelected) {
    // Desmarcar todos los permisos del módulo
    roleForm.value.permissions = roleForm.value.permissions.filter(p => !modulePermissionIds.includes(p))
  } else {
    // Marcar todos los permisos del módulo
    const newPermissions = [...roleForm.value.permissions]
    modulePermissionIds.forEach(id => {
      if (!newPermissions.includes(id)) {
        newPermissions.push(id)
      }
    })
    roleForm.value.permissions = newPermissions
  }
}

const isModuleSelected = (moduleId) => {
  const module = permissionsModules.value.find(m => m.id === moduleId)
  if (!module) return false
  const modulePermissionIds = module.permissions.map(p => p.id)
  return modulePermissionIds.every(id => roleForm.value.permissions.includes(id))
}

const closeRoleModal = () => {
  showRoleModal.value = false
  isEditingRole.value = false
}

// =================================
// UTILIDADES
// =================================

const getUserColor = (name) => {
  const colors = ['#3B82F6', '#10B981', '#F59E0B', '#EF4444', '#8B5CF6', '#EC4899', '#14B8A6', '#F97316']
  const index = name.charCodeAt(0) % colors.length
  return colors[index]
}

const getRoleBadgeClass = (roleName) => {
  const name = roleName.toLowerCase()
  if (name.includes('admin')) return 'bg-red-100 text-red-700'
  if (name.includes('gerente')) return 'bg-blue-100 text-blue-700'
  if (name.includes('cajero')) return 'bg-green-100 text-green-700'
  if (name.includes('analista')) return 'bg-purple-100 text-purple-700'
  if (name.includes('inventario')) return 'bg-yellow-100 text-yellow-700'
  return 'bg-gray-100 text-gray-700'
}

const formatDate = (date) => {
  if (!date) return 'Nunca'
  const d = new Date(date)
  return d.toLocaleDateString('es-ES', { 
    year: 'numeric', 
    month: 'short', 
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
}

const getRoleColor = (name) => {
  const colors = ['#A855F7', '#8B5CF6', '#6366F1', '#3B82F6', '#0EA5E9', '#14B8A6', '#10B981', '#F59E0B']
  const index = name.charCodeAt(0) % colors.length
  return colors[index]
}

const countPermissions = (permissions) => {
  if (!permissions) return 0
  if (Array.isArray(permissions)) return permissions.length
  if (typeof permissions === 'object') return Object.keys(permissions).length
  return 0
}

const getPermissionsBadgeClass = (permissions) => {
  const count = countPermissions(permissions)
  if (count === 0) return 'bg-gray-100 text-gray-600'
  if (count >= 20) return 'bg-red-100 text-red-700'
  if (count >= 10) return 'bg-orange-100 text-orange-700'
  if (count >= 5) return 'bg-blue-100 text-blue-700'
  return 'bg-green-100 text-green-700'
}

// =================================
// LIFECYCLE
// =================================

onMounted(() => {
  loadUsers()
  loadRoles()
})
</script>

<style scoped>
/* 🎨 Animaciones Empresariales */
@keyframes fade-in {
  from {
    opacity: 0;
    transform: translateY(10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

@keyframes slide-in {
  from {
    opacity: 0;
    transform: translateX(-20px);
  }
  to {
    opacity: 1;
    transform: translateX(0);
  }
}

.animate-fade-in {
  animation: fade-in 0.6s ease-out;
}

.animate-slide-in {
  animation: slide-in 0.5s ease-out;
}

/* 💫 Efectos Hover Empresariales */
.hover-lift:hover {
  transform: translateY(-2px);
  box-shadow: 0 10px 25px -3px rgba(0, 0, 0, 0.1);
}

/* 📊 Scrollbar Empresarial */
::-webkit-scrollbar {
  width: 6px;
  height: 6px;
}

::-webkit-scrollbar-track {
  background: #f1f5f9;
  border-radius: 3px;
}

::-webkit-scrollbar-thumb {
  background: #cbd5e1;
  border-radius: 3px;
}

::-webkit-scrollbar-thumb:hover {
  background: #94a3b8;
}

/* 🎯 Estados de Focus Empresariales */
input:focus, select:focus, textarea:focus {
  outline: none;
  box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

/* ⚡ Transiciones Optimizadas */
button, .transition-all, .transition-colors {
  transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
}

/* 🎨 Badge Hover Empresarial */
.badge-hover:hover {
  transform: scale(1.05);
  transition: transform 0.2s ease;
}

/* 💼 Modal y Overlay Empresarial */
.modal-overlay {
  backdrop-filter: blur(8px);
  background: rgba(15, 23, 42, 0.6);
}

.modal-content {
  box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
}

/* 🔄 Tabs Empresariales */
.tab-transition {
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

/* 📱 Avatar Empresarial */
.user-avatar {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

/* 🎯 Estados de Tabla */
.table-row-hover:hover {
  background: linear-gradient(90deg, rgba(59, 130, 246, 0.02) 0%, rgba(147, 197, 253, 0.02) 100%);
}

/* 🔍 Estado de Focus en Filtros */
.filter-focus:focus-within {
  box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
  border-color: #3b82f6;
}

/* ✨ Animación de Carga */
@keyframes pulse-gentle {
  0%, 100% {
    opacity: 1;
  }
  50% {
    opacity: 0.8;
  }
}

.loading-pulse {
  animation: pulse-gentle 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}
</style>
