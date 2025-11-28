<template>
  <div class="min-h-screen bg-gray-50 flex flex-col justify-center py-12 sm:px-6 lg:px-8">
    <!-- Header del Login -->
    <div class="sm:mx-auto sm:w-full sm:max-w-md">
      <div class="flex justify-center">
        <div class="w-20 h-20 bg-blue-600 rounded-xl flex items-center justify-center shadow-lg">
          <span class="text-white font-bold text-2xl">105</span>
        </div>
      </div>
      <h2 class="mt-6 text-center text-3xl font-bold text-gray-900">
        Iniciar Sesión
      </h2>
      <p class="mt-2 text-center text-sm text-gray-600">
        Sistema POS Empresarial
      </p>
    </div>

    <!-- Formulario de Login -->
    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
      <div class="bg-white py-8 px-4 shadow-xl rounded-xl sm:px-10">
        
        <!-- Mensajes de Error/Éxito -->
        <div v-if="message.text" 
             :class="message.type === 'error' ? 'bg-red-100 border-red-400 text-red-700' : 'bg-green-100 border-green-400 text-green-700'"
             class="mb-4 border px-4 py-3 rounded-lg">
          {{ message.text }}
        </div>

        <form @submit.prevent="handleLogin" class="space-y-6">
          
          <!-- Campo Email -->
          <div>
            <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
              Correo Electrónico
            </label>
            <div class="relative">
              <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207" />
                </svg>
              </div>
              <input
                id="email"
                v-model="credentials.email"
                type="email"
                autocomplete="email"
                required
                placeholder="ejemplo@correo.com"
                class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                :class="{ 'border-red-300 focus:border-red-500 focus:ring-red-500': errors.email }"
              />
            </div>
            <p v-if="errors.email" class="mt-1 text-sm text-red-600">{{ errors.email }}</p>
          </div>

          <!-- Campo Contraseña -->
          <div>
            <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
              Contraseña
            </label>
            <div class="relative">
              <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                </svg>
              </div>
              <input
                id="password"
                v-model="credentials.password"
                :type="showPassword ? 'text' : 'password'"
                autocomplete="current-password"
                required
                placeholder="Ingrese su contraseña"
                class="block w-full pl-10 pr-12 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                :class="{ 'border-red-300 focus:border-red-500 focus:ring-red-500': errors.password }"
              />
              <button
                type="button"
                @click="showPassword = !showPassword"
                class="absolute inset-y-0 right-0 pr-3 flex items-center"
              >
                <svg v-if="showPassword" class="h-5 w-5 text-gray-400 hover:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                </svg>
                <svg v-else class="h-5 w-5 text-gray-400 hover:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21"/>
                </svg>
              </button>
            </div>
            <p v-if="errors.password" class="mt-1 text-sm text-red-600">{{ errors.password }}</p>
          </div>

          <!-- Recordar Sesión -->
          <div class="flex items-center justify-between">
            <div class="flex items-center">
              <input
                id="remember-me"
                v-model="credentials.remember"
                name="remember-me"
                type="checkbox"
                class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
              />
              <label for="remember-me" class="ml-2 block text-sm text-gray-700">
                Recordar sesión
              </label>
            </div>

            <div class="text-sm">
              <a href="#" class="font-medium text-blue-600 hover:text-blue-500 transition-colors">
                ¿Olvidaste tu contraseña?
              </a>
            </div>
          </div>

          <!-- Botón de Login -->
          <div>
            <button
              type="submit"
              :disabled="loading"
              class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-medium rounded-lg text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-200"
            >
              <span v-if="loading" class="absolute left-0 inset-y-0 flex items-center pl-3">
                <svg class="h-5 w-5 text-blue-300 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
              </span>
              {{ loading ? 'Iniciando sesión...' : 'Iniciar Sesión' }}
            </button>
          </div>
        </form>

        <!-- Información adicional -->
        <div class="mt-6 border-t border-gray-200 pt-6">
          <div class="text-center">
            <p class="text-xs text-gray-500">
              Sistema desarrollado por <span class="font-medium">105 CODE</span>
            </p>
            <p class="text-xs text-gray-400 mt-1">
              Versión 2.0 - {{ new Date().getFullYear() }}
            </p>
          </div>
        </div>

        <!-- Demo credentials (solo desarrollo) -->
        <div v-if="isDevelopment" class="mt-4 p-4 bg-blue-50 rounded-lg border border-blue-200">
          <h4 class="text-sm font-medium text-blue-800 mb-2">🔑 Credenciales de Demo:</h4>
          <div class="text-xs text-blue-700 space-y-2">
            <div class="flex justify-between items-center bg-white p-2 rounded border">
              <span class="font-medium">👨‍💼 Admin:</span>
              <span class="font-mono text-gray-800">12345678 / admin123</span>
            </div>
            <div class="flex justify-between items-center bg-white p-2 rounded border">
              <span class="font-medium">🛒 Vendedor:</span>
              <span class="font-mono text-gray-800">11223344 / vendedor123</span>
            </div>
          </div>
          <p class="text-xs text-blue-600 mt-2 italic">
            ✨ Usa estas credenciales para probar el sistema
          </p>
        </div>

      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, computed } from 'vue'
import { useRouter } from 'vue-router'
import authService from '../services/authService.js'

// Router
const router = useRouter()

// Estado reactivo
const loading = ref(false)
const showPassword = ref(false)

// Credenciales del formulario
const credentials = reactive({
  email: '',
  password: '',
  remember: false
})

// Mensajes y errores
const message = reactive({
  text: '',
  type: ''
})

const errors = reactive({
  email: '',
  password: ''
})

// Ambiente de desarrollo
const isDevelopment = computed(() => {
  return process.env.NODE_ENV === 'development' || window.location.hostname === 'localhost'
})

// Limpiar mensajes
const clearMessages = () => {
  message.text = ''
  message.type = ''
  errors.email = ''
  errors.password = ''
}

// Validar formulario
const validateForm = () => {
  clearMessages()
  let isValid = true

  if (!credentials.email.trim()) {
    errors.email = 'El correo es requerido'
    isValid = false
  } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(credentials.email.trim())) {
    errors.email = 'Ingrese un correo válido'
    isValid = false
  }

  if (!credentials.password.trim()) {
    errors.password = 'La contraseña es requerida'
    isValid = false
  } else if (credentials.password.length < 6) {
    errors.password = 'La contraseña debe tener al menos 6 caracteres'
    isValid = false
  }

  return isValid
}

// Manejar login
const handleLogin = async () => {
  if (!validateForm()) return

  loading.value = true
  clearMessages()

  try {
    const response = await authService.login({
      email: credentials.email.trim(),
      password: credentials.password
    })

    message.text = 'Inicio de sesión exitoso'
    message.type = 'success'

    // Verificar rol y redireccionar
    const user = response.data?.user || response.user // Compatibilidad con ambas estructuras
    console.log('Usuario autenticado:', user)

    // Esperar un momento para mostrar el mensaje de éxito
    setTimeout(() => {
      // Si es super admin, ir directo al panel god mode
      if (user?.is_super_admin || user?.role?.name === 'superadmin') {
        router.push('/admin/god-mode')
      } else if (user?.role) {
        const roleName = user.role.name || user.role
        if (roleName === 'Administrador' || roleName === 'admin' || roleName === 'Gerente') {
          router.push('/dashboard')
        } else if (roleName === 'Cajero' || roleName === 'cajero') {
          router.push('/pos')
        } else if (roleName === 'Vendedor' || roleName === 'vendedor') {
          router.push('/pos')
        } else {
          router.push('/dashboard')
        }
      } else {
        // Si no tiene rol definido, ir al POS por defecto
        router.push('/pos')
      }
    }, 1000)

  } catch (error) {
    console.error('Error en login:', error)
    
    if (error.errors) {
      // Errores de validación del servidor
      if (error.errors.email) {
        errors.email = error.errors.email[0]
      }
      if (error.errors.password) {
        errors.password = error.errors.password[0]
      }
    } else {
      // Error general
      message.text = error.message || 'Credenciales incorrectas'
      message.type = 'error'
    }
  } finally {
    loading.value = false
  }
}

// Auto-completar en desarrollo
const setDemoCredentials = (role) => {
  if (!isDevelopment.value) return
  
  switch (role) {
    case 'admin':
      credentials.cc = '12345678'
      credentials.password = 'admin123'
      break
    case 'cajero':
      credentials.cc = '87654321'
      credentials.password = 'cajero123'
      break
    case 'vendedor':
      credentials.cc = '11223344'
      credentials.password = 'vendedor123'
      break
  }
}

// Verificar si ya está autenticado
if (authService.isAuthenticated()) {
  router.push('/dashboard')
}
</script>

<style scoped>
/* Animaciones adicionales */
.animate-spin {
  animation: spin 1s linear infinite;
}

@keyframes spin {
  from { transform: rotate(0deg); }
  to { transform: rotate(360deg); }
}

/* Efectos de focus mejorados */
input:focus {
  box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

/* Hover effects */
button:hover:not(:disabled) {
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(59, 130, 246, 0.15);
}
</style>