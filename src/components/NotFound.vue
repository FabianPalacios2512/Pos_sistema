<template>
  <div class="min-h-screen bg-gray-50 flex flex-col justify-center py-12 sm:px-6 lg:px-8">
    <div class="sm:mx-auto sm:w-full sm:max-w-md">
      <div class="text-center">
        <!-- Icono 404 -->
        <div class="mx-auto w-24 h-24 bg-red-100 rounded-full flex items-center justify-center mb-6">
          <svg class="w-12 h-12 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.5 0L4.314 16.5c-.77.833.192 2.5 1.732 2.5z"/>
          </svg>
        </div>
        
        <!-- Título -->
        <h1 class="text-6xl font-bold text-gray-900 mb-4">404</h1>
        <h2 class="text-2xl font-semibold text-gray-700 mb-2">Página no encontrada</h2>
        <p class="text-gray-500 mb-8">
          La página que buscas no existe o ha sido movida.
        </p>
        
        <!-- Botones de acción -->
        <div class="space-y-4">
          <button @click="goHome" 
                  class="w-full bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-medium transition-colors">
            Ir al Inicio
          </button>
          
          <button @click="goBack" 
                  class="w-full bg-gray-100 hover:bg-gray-200 text-gray-700 px-6 py-3 rounded-lg font-medium transition-colors">
            Volver Atrás
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { useRouter } from 'vue-router'
import authService from '../services/authService.js'

const router = useRouter()

const goHome = () => {
  if (authService.isAuthenticated()) {
    const user = authService.getUser()
    if (user.role === 'admin') {
      router.push('/dashboard')
    } else {
      router.push('/pos')
    }
  } else {
    router.push('/login')
  }
}

const goBack = () => {
  router.go(-1)
}
</script>