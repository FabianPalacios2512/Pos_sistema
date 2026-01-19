<template>
  <div class="min-h-screen font-sans bg-gradient-to-b from-gray-50 via-gray-100 to-gray-200 dark:bg-gradient-to-b dark:from-[#141417] dark:via-slate-900 dark:to-[#0a0a0c] flex items-center justify-center px-4 transition-colors duration-300">
    
    <!-- Card Principal con Glassmorphism -->
    <div class="max-w-lg w-full bg-white dark:bg-zinc-900/80  rounded-2xl shadow-2xl dark:shadow-black/50 border border-gray-200 dark:border-zinc-800 p-8 md:p-10 text-center space-y-8 relative overflow-hidden">
      
      <!-- Decoración de fondo (Glow) -->
      <div class="absolute top-0 left-1/2 -translate-x-1/2 w-full h-2 bg-gradient-to-r from-transparent via-amber-500 to-transparent opacity-50"></div>
      
      <!-- Icono de Alerta Animado -->
      <div class="relative mx-auto w-24 h-24">
        <div class="absolute inset-0 bg-amber-100 dark:bg-amber-900/20 rounded-full animate-pulse"></div>
        <div class="relative w-full h-full bg-amber-50 dark:bg-amber-900/30 rounded-full flex items-center justify-center border border-amber-100 dark:border-amber-900/40 shadow-inner">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-amber-600 dark:text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
          </svg>
        </div>
      </div>

      <!-- Título y Mensaje -->
      <div class="space-y-3">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white tracking-tight">
          Suscripción Finalizada
        </h1>
        <p class="text-gray-600 dark:text-zinc-400 text-lg leading-relaxed">
          Tu periodo de suscripción ha terminado. Para recuperar el acceso completo a tu sistema y datos, por favor renueva tu plan.
        </p>
      </div>

      <!-- Detalles del Estado -->
      <div class="bg-gray-50 dark:bg-zinc-800/50 rounded-xl p-5 border border-gray-200 dark:border-zinc-700/50 flex items-center justify-between">
        <div class="flex items-center gap-3">
          <div class="w-10 h-10 rounded-lg bg-white dark:bg-zinc-800 flex items-center justify-center border border-gray-200 dark:border-zinc-700">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500 dark:text-zinc-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
          </div>
          <div class="text-left">
            <p class="text-xs font-medium text-gray-500 dark:text-zinc-500 uppercase tracking-wider">Estado Actual</p>
            <p class="text-sm font-semibold text-gray-900 dark:text-zinc-200">Plan Expirado</p>
          </div>
        </div>
        <span class="px-3 py-1 rounded-full text-xs font-bold bg-rose-50 dark:bg-rose-900/30 text-rose-700 dark:text-rose-400 border border-rose-100 dark:border-rose-800 uppercase tracking-wide">
          Acceso Restringido
        </span>
      </div>

      <!-- Botones de Acción -->
      <div class="space-y-4 pt-4">
        <button 
          @click="goToUpgrade"
          class="w-full group relative py-3.5 px-6 bg-slate-900 dark:bg-slate-700 hover:bg-black dark:hover:bg-slate-600 text-white font-bold rounded-xl shadow-lg shadow-slate-400/40 dark:shadow-slate-900/50 transition-all duration-300 flex items-center justify-center gap-3 overflow-hidden"
        >
          <div class="absolute inset-0 w-full h-full bg-gradient-to-r from-transparent via-white/10 to-transparent -translate-x-full group-hover:translate-x-full transition-transform duration-700"></div>
          <span class="relative">Renovar Suscripción</span>
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 relative group-hover:translate-x-1 transition-transform duration-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
          </svg>
        </button>

        <button 
          @click="logout"
          class="w-full py-3.5 px-6 bg-white dark:bg-zinc-900 hover:bg-gray-50 dark:hover:bg-zinc-800 text-slate-600 dark:text-zinc-400 font-medium rounded-xl border border-gray-200 dark:border-zinc-800 transition-all duration-200 flex items-center justify-center gap-2"
        >
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
          </svg>
          <span>Cerrar Sesión</span>
        </button>
      </div>

      <!-- Pie de página -->
      <div class="pt-4 border-t border-gray-100 dark:border-zinc-800/50">
        <p class="text-xs text-gray-400 dark:text-zinc-600">
          ¿Necesitas ayuda? <a href="#" class="text-blue-600 dark:text-blue-400 hover:underline font-medium">Contactar a Soporte</a>
        </p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { useRouter } from 'vue-router'
import { onMounted } from 'vue'
import authService from '../services/authService'
import { appStore } from '../store/appStore'
import apiClient from '../services/apiClient'

const router = useRouter()

// Limpiar flag de redirección al montar y guardar tenant_id
onMounted(async () => {
  if (window.__redirecting_to_expired) {
    delete window.__redirecting_to_expired
  }
  
  // 🔥 CRÍTICO: Obtener tenant_id REAL desde el backend
  try {
    console.log('🔍 Obteniendo tenant_id desde /tenant-info...')
    const response = await apiClient.get('/tenant-info')
    
    if (response.data.success && response.data.tenant_id) {
      localStorage.setItem('expiredTenantId', response.data.tenant_id)
      console.log('💾 TenantId guardado desde backend:', response.data.tenant_id)
      
      // También guardar el nombre del negocio si está disponible
      if (response.data.business_name) {
        localStorage.setItem('business_name', response.data.business_name)
      }
    } else {
      console.error('❌ No se pudo obtener tenant_id del backend')
      // Fallback: intentar desde appStore
      if (appStore.tenant?.id && appStore.tenant.id !== 'unknown') {
        localStorage.setItem('expiredTenantId', appStore.tenant.id)
        console.log('💾 TenantId guardado desde appStore (fallback):', appStore.tenant.id)
      }
    }
  } catch (error) {
    console.error('❌ Error obteniendo tenant_id:', error)
    // Último recurso: intentar desde appStore
    if (appStore.tenant?.id && appStore.tenant.id !== 'unknown') {
      localStorage.setItem('expiredTenantId', appStore.tenant.id)
      console.log('💾 TenantId guardado desde appStore (error fallback):', appStore.tenant.id)
    }
  }
})

const goToUpgrade = () => {
  // Redirigir a la vista de selección de planes con parámetro de renovación
  // El parámetro ?renewal=true indica que es un usuario existente renovando su plan
  router.push('/select-plan?renewal=true')
}

const logout = async () => {
  try {
    await authService.logout()
    router.push('/login')
  } catch (error) {
    console.error('Error al cerrar sesión:', error)
    router.push('/login')
  }
}
</script>
