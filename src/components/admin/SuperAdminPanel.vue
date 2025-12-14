<template>
  <!-- Panel Super Admin Modular -->
  <div class="min-h-screen bg-gradient-to-b from-gray-50 via-gray-100 to-gray-200 dark:bg-gradient-to-b dark:from-[#141417] dark:via-slate-900 dark:to-[#0a0a0c] transition-colors duration-300">
    
    <!-- Sidebar de Módulos -->
    <div class="flex">
      <!-- Sidebar -->
      <aside class="w-64 min-h-screen bg-white dark:bg-zinc-900 border-r border-gray-300 dark:border-zinc-800 fixed left-0 top-0 z-40">
        <!-- Header -->
        <div class="p-6 border-b border-gray-200 dark:border-zinc-800">
          <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-purple-600 to-blue-600 flex items-center justify-center">
              <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
              </svg>
            </div>
            <div>
              <h1 class="text-lg font-bold text-gray-900 dark:text-white">Super Admin</h1>
              <p class="text-xs text-gray-500 dark:text-zinc-400">Panel de Control</p>
            </div>
          </div>
        </div>

        <!-- Navegación de Módulos -->
        <nav class="p-4 space-y-2">
          <button
            v-for="module in modules"
            :key="module.id"
            @click="activeModule = module.id"
            :class="[
              'w-full flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition-all duration-200',
              activeModule === module.id
                ? 'bg-gradient-to-r from-purple-600 to-blue-600 text-white shadow-lg'
                : 'text-gray-600 dark:text-zinc-400 hover:bg-gray-100 dark:hover:bg-zinc-800'
            ]"
          >
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="module.icon"></path>
            </svg>
            <span>{{ module.name }}</span>
            <span v-if="module.badge" class="ml-auto bg-red-500 text-white text-xs px-2 py-0.5 rounded-full">{{ module.badge }}</span>
          </button>
        </nav>

        <!-- Footer Sidebar -->
        <div class="absolute bottom-0 left-0 right-0 p-4 border-t border-gray-200 dark:border-zinc-800">
          <button
            @click="$emit('navigate', 'dashboard')"
            class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium text-gray-600 dark:text-zinc-400 hover:bg-gray-100 dark:hover:bg-zinc-800 transition-all"
          >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            <span>Volver al Sistema</span>
          </button>
        </div>
      </aside>

      <!-- Contenido Principal -->
      <main class="ml-64 flex-1 p-8">
        <!-- Dashboard -->
        <div v-if="activeModule === 'dashboard'" class="animate-fade-in">
          <SuperAdminDashboard />
        </div>

        <!-- Gestión de Clientes -->
        <div v-if="activeModule === 'clients'" class="animate-fade-in">
          <TenantsManagement />
        </div>

        <!-- Monitoreo IA -->
        <div v-if="activeModule === 'ai-monitoring'" class="animate-fade-in">
          <AdminDashboardView />
        </div>

        <!-- Logs del Sistema -->
        <div v-if="activeModule === 'logs'" class="animate-fade-in">
          <SystemLogs />
        </div>
      </main>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import SuperAdminDashboard from './modules/SuperAdminDashboard.vue'
import TenantsManagement from './modules/TenantsManagement.vue'
import AdminDashboardView from '../../views/AdminDashboardView.vue'
import SystemLogs from './modules/SystemLogs.vue'

const emit = defineEmits(['navigate'])

const activeModule = ref('dashboard')

const modules = ref([
  {
    id: 'dashboard',
    name: 'Dashboard',
    icon: 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6'
  },
  {
    id: 'clients',
    name: 'Gestión de Clientes',
    icon: 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z'
  },
  {
    id: 'ai-monitoring',
    name: 'Monitoreo IA',
    icon: 'M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z',
    badge: null
  },
  {
    id: 'logs',
    name: 'Logs del Sistema',
    icon: 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
    badge: null
  }
])
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
  animation: fade-in 0.3s ease-out;
}
</style>
