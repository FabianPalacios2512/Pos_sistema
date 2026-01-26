<template>
  <!-- 🎨 Diseño SaaS Profesional - Sistema de Usuarios y Roles -->
  <div class="min-h-screen font-sans bg-gradient-to-br from-gray-50 via-white to-slate-100 dark:bg-gradient-to-b dark:from-[#141417] dark:via-slate-900 dark:to-[#0a0a0c] transition-colors duration-300 px-8">
    <div class="p-4 lg:p-6 space-y-6 pb-8 animate-fade-in">
      
      <!-- Header sin borde, sin icono -->
      <div class="flex items-center justify-between pb-4">
        <div>
          <h1 class="text-2xl font-semibold text-gray-900 dark:text-white tracking-tight">Usuarios y Roles</h1>
          <p class="text-sm text-gray-500 dark:text-zinc-500 mt-1 font-normal">
            {{ activeTab === 'users' ? 'Administra empleados y su acceso al sistema' : 'Configura roles y permisos granulares' }}
          </p>
        </div>
        
        <div class="flex items-center gap-3">
          <!-- Indicador de Límite de Usuarios (solo en tab usuarios) -->
          <div v-if="activeTab === 'users' && maxUsersAllowed !== null" 
               class="hidden md:flex items-center gap-2 px-3 py-2 rounded-xl text-xs font-medium"
               :class="canCreateMoreUsers 
                 ? 'bg-emerald-50 dark:bg-emerald-950/50 text-emerald-700 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800' 
                 : 'bg-amber-50 dark:bg-amber-950/50 text-amber-700 dark:text-amber-400 border border-amber-200 dark:border-amber-800'">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
            </svg>
            <span>{{ currentUsersCount }}/{{ maxUsersAllowed }} usuarios</span>
          </div>
          
          <!-- Badge Plan Enterprise -->
          <div v-if="activeTab === 'users' && maxUsersAllowed === null" 
               class="hidden md:flex items-center gap-2 px-3 py-2 rounded-xl text-xs font-medium bg-purple-50 dark:bg-purple-950/50 text-purple-700 dark:text-purple-400 border border-purple-200 dark:border-purple-800">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
            </svg>
            <span>Usuarios ilimitados</span>
          </div>
          
          <!-- Botón Secundario -->
          <button @click="refreshData"
                  :disabled="loading"
                  class="px-5 py-2.5 bg-white dark:bg-zinc-900 hover:bg-slate-50 dark:hover:bg-zinc-800 text-slate-600 dark:text-zinc-200 text-sm font-bold rounded-xl border border-gray-300 dark:border-zinc-800 shadow-sm transition-all duration-200"
                  :class="{ 'opacity-50 cursor-not-allowed': loading }">
            <svg class="w-4 h-4" :class="{ 'animate-spin': loading }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
            </svg>
          </button>
          
          <!-- Botón Principal -->
          <button @click="activeTab === 'users' ? openCreateUserModal() : openCreateRoleModal()"
                  :disabled="activeTab === 'users' && !canCreateMoreUsers"
                  class="px-6 py-2.5 bg-slate-900 dark:bg-slate-700 hover:bg-black dark:hover:bg-slate-600 disabled:opacity-50 disabled:cursor-not-allowed text-white text-sm font-bold rounded-xl shadow-lg shadow-slate-400/40 dark:shadow-slate-900/50 transition-all duration-300 flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path>
            </svg>
            <span>{{ activeTab === 'users' ? 'Nuevo Usuario' : 'Nuevo Rol' }}</span>
          </button>
        </div>
      </div>

      <!-- Tabs Navigation -->
      <div class="bg-gray-50 dark:bg-zinc-800 rounded-xl p-1 inline-flex border border-gray-200 dark:border-zinc-700 h-[46px]">
        <button @click="activeTab = 'users'"
                :class="[
                  'px-5 py-2.5 text-sm font-bold rounded-lg transition-all duration-200 flex items-center gap-2',
                  activeTab === 'users'
                    ? 'bg-white dark:bg-zinc-900 text-gray-900 dark:text-white shadow-sm'
                    : 'text-gray-500 dark:text-zinc-400 hover:text-gray-900 dark:hover:text-white'
                ]">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
          </svg>
          <span>Usuarios</span>
          <span class="px-2 py-0.5 text-xs font-bold rounded-full bg-blue-50 dark:bg-blue-950 text-blue-700 dark:text-blue-400">
            {{ users.length }}
          </span>
        </button>
        
        <button @click="activeTab = 'roles'"
                :class="[
                  'px-5 py-2.5 text-sm font-bold rounded-lg transition-all duration-200 flex items-center gap-2',
                  activeTab === 'roles'
                    ? 'bg-white dark:bg-zinc-900 text-gray-900 dark:text-white shadow-sm'
                    : 'text-gray-500 dark:text-zinc-400 hover:text-gray-900 dark:hover:text-white'
                ]">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
          </svg>
          <span>Roles</span>
          <span class="px-2 py-0.5 text-xs font-bold rounded-full bg-purple-50 dark:bg-purple-950 text-purple-700 dark:text-purple-400">
            {{ roles.length }}
          </span>
        </button>
      </div>

      <!-- KPIs con Glassmorphism -->
      <div v-if="activeTab === 'users'" class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <!-- Total Usuarios -->
        <div class="bg-white dark:bg-zinc-900/80  rounded-xl px-4 py-3 border border-gray-300 dark:border dark:border-white/5 hover:border-gray-400 dark:hover:border-white/10 transition-all duration-200 shadow-md hover:shadow-lg dark:shadow-lg dark:shadow-black/50">
          <div class="flex items-center gap-3">
            <div class="w-11 h-11 bg-gray-100 dark:bg-zinc-800/50 border border-gray-200 dark:border-white/5 rounded-lg flex items-center justify-center flex-shrink-0">
              <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
              </svg>
            </div>
            <div class="flex-1 min-w-0">
              <p class="text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wide">Total Usuarios</p>
              <p class="text-2xl font-bold text-gray-900 dark:text-white mt-0.5">{{ users.length }}</p>
            </div>
          </div>
        </div>

        <!-- Usuarios Activos -->
        <div class="bg-white dark:bg-zinc-900/80  rounded-xl px-4 py-3 border border-gray-300 dark:border dark:border-white/5 hover:border-gray-400 dark:hover:border-white/10 transition-all duration-200 shadow-md hover:shadow-lg dark:shadow-lg dark:shadow-black/50">
          <div class="flex items-center gap-3">
            <div class="w-11 h-11 bg-gray-100 dark:bg-zinc-800/50 border border-gray-200 dark:border-white/5 rounded-lg flex items-center justify-center flex-shrink-0">
              <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
              </svg>
            </div>
            <div class="flex-1 min-w-0">
              <p class="text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wide">Activos</p>
              <p class="text-2xl font-bold text-gray-900 dark:text-white mt-0.5">{{ activeUsersCount }}</p>
            </div>
          </div>
        </div>

        <!-- Usuarios Inactivos -->
        <div class="bg-white dark:bg-zinc-900/80  rounded-xl px-4 py-3 border border-gray-300 dark:border dark:border-white/5 hover:border-gray-400 dark:hover:border-white/10 transition-all duration-200 shadow-md hover:shadow-lg dark:shadow-lg dark:shadow-black/50">
          <div class="flex items-center gap-3">
            <div class="w-11 h-11 bg-gray-100 dark:bg-zinc-800/50 border border-gray-200 dark:border-white/5 rounded-lg flex items-center justify-center flex-shrink-0">
              <svg class="w-5 h-5 text-rose-600 dark:text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
              </svg>
            </div>
            <div class="flex-1 min-w-0">
              <p class="text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wide">Inactivos</p>
              <p class="text-2xl font-bold text-gray-900 dark:text-white mt-0.5">{{ inactiveUsersCount }}</p>
            </div>
          </div>
        </div>

        <!-- Roles Configurados -->
        <div class="bg-white dark:bg-zinc-900/80  rounded-xl px-4 py-3 border border-gray-300 dark:border dark:border-white/5 hover:border-gray-400 dark:hover:border-white/10 transition-all duration-200 shadow-md hover:shadow-lg dark:shadow-lg dark:shadow-black/50">
          <div class="flex items-center gap-3">
            <div class="w-11 h-11 bg-gray-100 dark:bg-zinc-800/50 border border-gray-200 dark:border-white/5 rounded-lg flex items-center justify-center flex-shrink-0">
              <svg class="w-5 h-5 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
              </svg>
            </div>
            <div class="flex-1 min-w-0">
              <p class="text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wide">Roles</p>
              <p class="text-2xl font-bold text-gray-900 dark:text-white mt-0.5">{{ roles.length }}</p>
            </div>
          </div>
        </div>
      </div>

      <!-- KPIs para Roles -->
      <div v-else class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <!-- Total Roles -->
        <div class="bg-white dark:bg-zinc-900/80  rounded-xl px-4 py-3 border border-gray-300 dark:border dark:border-white/5 hover:border-gray-400 dark:hover:border-white/10 transition-all duration-200 shadow-md hover:shadow-lg dark:shadow-lg dark:shadow-black/50">
          <div class="flex items-center gap-3">
            <div class="w-11 h-11 bg-gray-100 dark:bg-zinc-800/50 border border-gray-200 dark:border-white/5 rounded-lg flex items-center justify-center flex-shrink-0">
              <svg class="w-5 h-5 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
              </svg>
            </div>
            <div class="flex-1 min-w-0">
              <p class="text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wide">Total Roles</p>
              <p class="text-2xl font-bold text-gray-900 dark:text-white mt-0.5">{{ roles.length }}</p>
            </div>
          </div>
        </div>

        <!-- Permisos Disponibles -->
        <div class="bg-white dark:bg-zinc-900/80  rounded-xl px-4 py-3 border border-gray-300 dark:border dark:border-white/5 hover:border-gray-400 dark:hover:border-white/10 transition-all duration-200 shadow-md hover:shadow-lg dark:shadow-lg dark:shadow-black/50">
          <div class="flex items-center gap-3">
            <div class="w-11 h-11 bg-gray-100 dark:bg-zinc-800/50 border border-gray-200 dark:border-white/5 rounded-lg flex items-center justify-center flex-shrink-0">
              <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
              </svg>
            </div>
            <div class="flex-1 min-w-0">
              <p class="text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wide">Permisos</p>
              <p class="text-2xl font-bold text-gray-900 dark:text-white mt-0.5">{{ totalPermissions }}</p>
            </div>
          </div>
        </div>

        <!-- Módulos del Sistema -->
        <div class="bg-white dark:bg-zinc-900/80  rounded-xl px-4 py-3 border border-gray-300 dark:border dark:border-white/5 hover:border-gray-400 dark:hover:border-white/10 transition-all duration-200 shadow-md hover:shadow-lg dark:shadow-lg dark:shadow-black/50">
          <div class="flex items-center gap-3">
            <div class="w-11 h-11 bg-indigo-50 dark:bg-indigo-950 rounded-lg flex items-center justify-center flex-shrink-0">
              <svg class="w-5 h-5 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
              </svg>
            </div>
            <div class="flex-1 min-w-0">
              <p class="text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wide">Módulos</p>
              <p class="text-2xl font-bold text-gray-900 dark:text-white mt-0.5">{{ permissionsModules.length }}</p>
            </div>
          </div>
        </div>

        <!-- Roles Activos -->
        <div class="bg-white dark:bg-zinc-900/80  rounded-xl px-4 py-3 border border-gray-300 dark:border dark:border-white/5 hover:border-gray-400 dark:hover:border-white/10 transition-all duration-200 shadow-md hover:shadow-lg dark:shadow-lg dark:shadow-black/50">
          <div class="flex items-center gap-3">
            <div class="w-11 h-11 bg-gray-100 dark:bg-zinc-800/50 border border-gray-200 dark:border-white/5 rounded-lg flex items-center justify-center flex-shrink-0">
              <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
              </svg>
            </div>
            <div class="flex-1 min-w-0">
              <p class="text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wide">Roles en Uso</p>
              <p class="text-2xl font-bold text-gray-900 dark:text-white mt-0.5">{{ activeRolesCount }}</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Contenido según Tab Activo -->
      <UsersTable 
        v-if="activeTab === 'users'"
        :users="users"
        @edit="openEditUserModal"
        @delete="deleteUser"
        @toggle-status="toggleUserStatus"
        @change-password="openPasswordModal"
      />
      
      <RolesTable 
        v-if="activeTab === 'roles'"
        :roles="roles"
        @edit="openEditRoleModal"
        @delete="deleteRole"
      />

    </div>

    <!-- MODAL: Crear/Editar Usuario -->
    <UserModal 
      :show="showUserModal"
      :user="selectedUser"
      :roles="roles"
      @close="closeUserModal"
      @save="saveUser"
    />

    <!-- MODAL: Crear/Editar Rol -->
    <RoleModal 
      :show="showRoleModal"
      :role="selectedRole"
      :permissionsModules="permissionsModules"
      @close="closeRoleModal"
      @save="saveRole"
    />

    <!-- MODAL: Cambiar Contraseña -->
    <PasswordModal 
      :show="showPasswordModal"
      :user="selectedUser"
      @close="closePasswordModal"
      @save="savePassword"
    />

  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import usersService from '../services/usersService.js'
import rolesService from '../services/rolesService.js'
import { appStore } from '../store/appStore.js'
import UsersTable from './UsersTable.vue'
import RolesTable from './RolesTable.vue'
import UserModal from './UserModal.vue'
import RoleModal from './RoleModal.vue'
import PasswordModal from './PasswordModal.vue'

// ===== ESTADO REACTIVO =====
const loading = ref(false)
const activeTab = ref('users')

// Usuarios
const users = ref([])
const showUserModal = ref(false)
const selectedUser = ref(null)

// Roles
const roles = ref([])
const showRoleModal = ref(false)
const selectedRole = ref(null)

// Contraseña
const showPasswordModal = ref(false)

// ===== VALIDACIÓN DE PLAN =====
// Límites de usuarios según el plan
const planUserLimits = {
  'free_trial': 2,
  'basic': 4,
  'pro': 4,       // premium en el frontend se llama 'pro'
  'premium': 4,   // alias por si acaso
  'enterprise': null // null = ilimitado
}

const currentPlan = computed(() => appStore.tenantPlan || 'free_trial')
const maxUsersAllowed = computed(() => planUserLimits[currentPlan.value] ?? 2)
const currentUsersCount = computed(() => users.value.length)
const canCreateMoreUsers = computed(() => {
  // Si es enterprise (null), siempre puede crear
  if (maxUsersAllowed.value === null) return true
  return currentUsersCount.value < maxUsersAllowed.value
})
const remainingUserSlots = computed(() => {
  if (maxUsersAllowed.value === null) return '∞'
  return Math.max(0, maxUsersAllowed.value - currentUsersCount.value)
})

// ===== COMPUTED PROPERTIES =====
const activeUsersCount = computed(() => users.value.filter(u => u.active).length)
const inactiveUsersCount = computed(() => users.value.filter(u => !u.active).length)
const activeRolesCount = computed(() => roles.value.filter(r => r.users_count > 0).length)
const totalPermissions = computed(() => {
  // Ahora son 17 módulos = 17 permisos
  return permissionsModules.value.length
})

// ===== PERMISOS ULTRA SIMPLIFICADOS: SOLO MÓDULOS =====
// 17 módulos = 17 permisos
// Si tiene el permiso → Ve el módulo completo
// Si NO tiene el permiso → El módulo no aparece en el menú
const permissionsModules = ref([
  {
    id: 'dashboard',
    name: 'Dashboard',
    description: 'Panel principal con estadísticas y KPIs',
    color: '#3B82F6',
    icon: 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6'
  },
  {
    id: 'pos',
    name: 'Punto de Venta (POS)',
    description: 'Sistema de ventas y cobros',
    color: '#10B981',
    icon: 'M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z'
  },
  {
    id: 'invoices',
    name: 'Facturas',
    description: 'Gestión de facturas y documentos',
    color: '#F59E0B',
    icon: 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'
  },
  {
    id: 'returns',
    name: 'Devoluciones',
    description: 'Gestión de devoluciones y reembolsos',
    color: '#EF4444',
    icon: 'M16 15v-1a4 4 0 00-4-4H8m0 0l3 3m-3-3l3-3m9 14V5a2 2 0 00-2-2H6a2 2 0 00-2 2v16l4-2 4 2 4-2 4 2z'
  },
  {
    id: 'products',
    name: 'Productos',
    description: 'Catálogo de productos y servicios',
    color: '#8B5CF6',
    icon: 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4'
  },
  {
    id: 'categories',
    name: 'Categorías',
    description: 'Organización de productos por categorías',
    color: '#EC4899',
    icon: 'M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z'
  },
  {
    id: 'stock',
    name: 'Gestión de Stock',
    description: 'Control de inventario y movimientos',
    color: '#14B8A6',
    icon: 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01'
  },
  {
    id: 'intelligent_inventory',
    name: 'Inventario IA',
    description: 'Análisis inteligente de inventario con IA',
    color: '#A855F7',
    icon: 'M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z',
    isPremium: true
  },
  {
    id: 'warehouses',
    name: 'Multisede (Bodegas)',
    description: 'Gestión de múltiples sedes y traslados',
    color: '#F97316',
    icon: 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4',
    isPremium: true
  },
  {
    id: 'customers',
    name: 'Clientes',
    description: 'Base de datos de clientes',
    color: '#0EA5E9',
    icon: 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z'
  },
  {
    id: 'accounts_receivable',
    name: 'Cuentas por Cobrar',
    description: 'Gestión de créditos y cobranzas',
    color: '#06B6D4',
    icon: 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
    isPremium: true
  },
  {
    id: 'suppliers',
    name: 'Proveedores',
    description: 'Gestión de proveedores y compras',
    color: '#F97316',
    icon: 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4'
  },
  {
    id: 'users',
    name: 'Usuarios y Roles',
    description: 'Administración de usuarios y permisos',
    color: '#6366F1',
    icon: 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z'
  },
  {
    id: 'cash_register',
    name: 'Caja (Administración)',
    description: 'Control de turnos de caja',
    color: '#DC2626',
    icon: 'M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z'
  },
  {
    id: 'expenses',
    name: 'Gastos Operativos',
    description: 'Registro y control de gastos',
    color: '#EF4444',
    icon: 'M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z'
  },
  {
    id: 'reports',
    name: 'Reportes',
    description: 'Informes y estadísticas del negocio',
    color: '#059669',
    icon: 'M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'
  },
  {
    id: 'settings',
    name: 'Configuración',
    description: 'Configuración general del sistema',
    color: '#64748B',
    icon: 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z'
  }
])

// ===== MÉTODOS =====
const refreshData = async () => {
  try {
    loading.value = true
    await Promise.all([loadUsers(), loadRoles()])
  } catch (error) {
    console.error('Error actualizando datos:', error)
  } finally {
    loading.value = false
  }
}

const loadUsers = async () => {
  try {
    const response = await usersService.getAllUsers()
    users.value = response.data || []
  } catch (error) {
    console.error('Error cargando usuarios:', error)
  }
}

const loadRoles = async () => {
  try {
    const response = await rolesService.getAllRoles()
    roles.value = response.data || []
  } catch (error) {
    console.error('Error cargando roles:', error)
  }
}

// Usuarios
const openCreateUserModal = () => {
  // 🔒 VALIDACIÓN DE PLAN: Verificar si puede crear más usuarios
  if (!canCreateMoreUsers.value) {
    const planName = currentPlan.value === 'free_trial' ? 'Prueba Gratuita' : 
                     currentPlan.value === 'basic' ? 'Básico' :
                     currentPlan.value === 'pro' || currentPlan.value === 'premium' ? 'Premium' : 
                     'Enterprise'
    alert(`⚠️ Has alcanzado el límite de ${maxUsersAllowed.value} usuarios para el plan ${planName}.\n\n💎 Actualiza tu plan para agregar más usuarios.`)
    return
  }
  
  selectedUser.value = null
  showUserModal.value = true
}

const openEditUserModal = (user) => {
  // 🔒 PROTECCIÓN: No permitir editar el usuario administrador inicial
  if (user.role_id === 1) {
    alert('⚠️ El usuario Administrador principal no puede ser editado por seguridad del sistema')
    return
  }
  
  selectedUser.value = user
  showUserModal.value = true
}

const closeUserModal = () => {
  showUserModal.value = false
  selectedUser.value = null
}

const saveUser = async (userData) => {
  try {
    loading.value = true
    
    if (selectedUser.value) {
      // Editar usuario existente
      await usersService.updateUser(selectedUser.value.id, userData)
      alert('✅ Usuario actualizado exitosamente')
    } else {
      // Crear nuevo usuario
      await usersService.createUser(userData)
      alert('✅ Usuario creado exitosamente')
    }
    
    await loadUsers()
    closeUserModal()
  } catch (error) {
    console.error('Error guardando usuario:', error)
    alert('❌ Error al guardar el usuario')
  } finally {
    loading.value = false
  }
}

const deleteUser = async (user) => {
  // 🔒 PROTECCIÓN: No permitir eliminar el usuario administrador inicial
  if (user.role_id === 1) {
    alert('⚠️ El usuario Administrador principal no puede ser eliminado por seguridad del sistema')
    return
  }
  
  if (!confirm(`¿Estás seguro de eliminar al usuario "${user.name}"?`)) {
    return
  }
  
  try {
    loading.value = true
    await usersService.deleteUser(user.id)
    alert('✅ Usuario eliminado exitosamente')
    await loadUsers()
  } catch (error) {
    console.error('Error eliminando usuario:', error)
    alert('❌ Error al eliminar el usuario')
  } finally {
    loading.value = false
  }
}

const toggleUserStatus = async (user) => {
  // 🔒 PROTECCIÓN: No permitir desactivar el usuario administrador inicial
  if (user.role_id === 1) {
    alert('⚠️ El usuario Administrador principal no puede ser desactivado por seguridad del sistema')
    return
  }
  
  try {
    loading.value = true
    await usersService.toggleStatus(user.id)
    alert(`✅ Usuario ${user.active ? 'desactivado' : 'activado'} exitosamente`)
    await loadUsers()
  } catch (error) {
    console.error('Error cambiando estado del usuario:', error)
    alert('❌ Error al cambiar el estado')
  } finally {
    loading.value = false
  }
}

const openPasswordModal = (user) => {
  // Permitir cambiar contraseña del administrador (para recuperación)
  selectedUser.value = user
  showPasswordModal.value = true
}

// Roles
const openCreateRoleModal = () => {
  selectedRole.value = null
  showRoleModal.value = true
}

const openEditRoleModal = (role) => {
  selectedRole.value = role
  showRoleModal.value = true
}

const closeRoleModal = () => {
  showRoleModal.value = false
  selectedRole.value = null
}

const saveRole = async (roleData) => {
  if (!roleData.name) {
    alert('❌ El nombre del rol es obligatorio')
    return
  }
  
  if (roleData.permissions.length === 0) {
    alert('❌ Debes seleccionar al menos un permiso')
    return
  }
  
  try {
    loading.value = true
    
    if (selectedRole.value) {
      // Editar rol existente
      await rolesService.updateRole(selectedRole.value.id, roleData)
      alert('✅ Rol actualizado exitosamente')
    } else {
      // Crear nuevo rol
      await rolesService.createRole(roleData)
      alert('✅ Rol creado exitosamente')
    }
    
    await loadRoles()
    closeRoleModal()
  } catch (error) {
    console.error('Error guardando rol:', error)
    alert('❌ Error al guardar el rol')
  } finally {
    loading.value = false
  }
}

const deleteRole = async (role) => {
  // Validar que no tenga usuarios asignados
  if (role.users_count > 0) {
    alert(`⚠️ No se puede eliminar el rol "${role.name}" porque tiene ${role.users_count} usuario(s) asignado(s)`)
    return
  }
  
  if (!confirm(`¿Estás seguro de eliminar el rol "${role.name}"?`)) {
    return
  }
  
  try {
    loading.value = true
    await rolesService.deleteRole(role.id)
    alert('✅ Rol eliminado exitosamente')
    await loadRoles()
  } catch (error) {
    console.error('Error eliminando rol:', error)
    alert('❌ Error al eliminar el rol')
  } finally {
    loading.value = false
  }
}

// Contraseña
const closePasswordModal = () => {
  showPasswordModal.value = false
  selectedUser.value = null
}

const savePassword = async (passwordData) => {
  try {
    loading.value = true
    await usersService.changePassword(selectedUser.value.id, passwordData)
    alert('✅ Contraseña actualizada exitosamente')
    closePasswordModal()
  } catch (error) {
    console.error('Error cambiando contraseña:', error)
    alert('❌ Error al cambiar la contraseña')
  } finally {
    loading.value = false
  }
}

// ===== LIFECYCLE =====
onMounted(() => {
  refreshData()
})
</script>

<style scoped>
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

.animate-fade-in {
  animation: fade-in 0.6s ease-out;
}
</style>
