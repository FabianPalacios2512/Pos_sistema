<template>
  <!-- 🔐 Pantalla de Recuperación de Contraseña con Código -->
  <div class="min-h-screen flex">
    
    <!-- 📸 LADO IZQUIERDO: Imagen de Marca (Oculto en móviles) -->
    <div class="hidden lg:flex lg:w-1/2 xl:w-[45%] relative overflow-hidden bg-gradient-to-br from-red-600 via-rose-700 to-pink-900">
      <img 
        src="/login.png" 
        alt="105 POS - Recuperar Contraseña" 
        class="absolute inset-0 w-full h-full object-cover opacity-70"
      />
      <div class="absolute inset-0 bg-gradient-to-t from-black/50 via-black/20 to-transparent"></div>
      <div class="absolute bottom-8 left-8 right-8 z-10">
        <div class="bg-white/10 backdrop-blur-md rounded-2xl p-6 border border-white/20">
          <h3 class="text-2xl font-bold text-white mb-2">🔐 Recupera tu Acceso</h3>
          <p class="text-white/80 text-sm">Te enviaremos un código de 6 dígitos a tu correo</p>
        </div>
      </div>
    </div>

    <!-- 📝 LADO DERECHO: Formulario -->
    <div class="flex-1 flex items-center justify-center px-4 sm:px-6 lg:px-8 bg-white">
      <div class="w-full max-w-md space-y-8">
        
        <!-- PASO 1: Solicitar Email -->
        <div v-if="step === 'request'">
          <div>
            <router-link to="/login" class="inline-flex items-center text-sm text-gray-600 hover:text-gray-900 transition-colors mb-6">
              <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
              </svg>
              Volver al login
            </router-link>
          </div>

          <div class="mt-6">
            <h2 class="text-3xl font-bold text-gray-900">¿Olvidaste tu contraseña?</h2>
            <p class="mt-2 text-base text-gray-600">
              Te enviaremos un código de 6 dígitos a tu correo electrónico
            </p>
          </div>

          <div v-if="message.text" 
               :class="message.type === 'error' ? 'bg-red-50 border-red-200 text-red-700' : 'bg-emerald-50 border-emerald-200 text-emerald-700'"
               class="mt-6 border px-4 py-3 rounded-lg text-sm">
            {{ message.text }}
          </div>

          <form @submit.prevent="requestReset" class="mt-8 space-y-6">
            <div>
              <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                Correo Electrónico
              </label>
              <input
                id="email"
                v-model="email"
                type="email"
                autocomplete="email"
                required
                placeholder="tucorreo@ejemplo.com"
                class="block w-full px-4 py-3 border border-gray-200 rounded-lg text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent transition-all"
              />
            </div>

            <button
              type="submit"
              :disabled="loading"
              class="w-full flex justify-center items-center px-6 py-3 border border-transparent rounded-lg text-base font-medium text-white bg-gradient-to-r from-red-600 to-pink-600 hover:from-red-700 hover:to-pink-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-all disabled:opacity-50 disabled:cursor-not-allowed"
            >
              <svg v-if="loading" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              {{ loading ? 'Enviando...' : 'Enviar código de recuperación' }}
            </button>
          </form>
        </div>

        <!-- PASO 2: Ingresar Código -->
        <div v-else-if="step === 'verify-code'">
          <div>
            <button @click="step = 'request'" class="inline-flex items-center text-sm text-gray-600 hover:text-gray-900 transition-colors mb-6">
              <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
              </svg>
              Volver
            </button>
          </div>

          <div class="text-center mb-8">
            <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-red-100 mb-4">
              <svg class="h-8 w-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
              </svg>
            </div>
            <h2 class="text-3xl font-bold text-gray-900 mb-2">Revisa tu correo</h2>
            <p class="text-base text-gray-600">
              Hemos enviado un código de 6 dígitos a<br/>
              <strong class="text-gray-900">{{ email }}</strong>
            </p>
          </div>

          <div v-if="message.text" 
               :class="message.type === 'error' ? 'bg-red-50 border-red-200 text-red-700' : 'bg-emerald-50 border-emerald-200 text-emerald-700'"
               class="mb-6 border px-4 py-3 rounded-lg text-sm">
            {{ message.text }}
          </div>

          <form @submit.prevent="validateCode" class="space-y-6">
            <div>
              <label for="code" class="block text-sm font-medium text-gray-700 mb-2 text-center">
                Ingresa el código de 6 dígitos
              </label>
              <input
                id="code"
                v-model="code"
                type="text"
                inputmode="numeric"
                maxlength="6"
                pattern="[0-9]{6}"
                required
                placeholder="123456"
                class="block w-full px-4 py-4 text-center text-2xl font-mono font-bold tracking-[0.5em] border-2 border-gray-300 rounded-lg text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent transition-all"
              />
              <p class="mt-2 text-xs text-gray-500 text-center">El código expira en 15 minutos</p>
            </div>

            <button
              type="submit"
              :disabled="loading || code.length !== 6"
              class="w-full flex justify-center items-center px-6 py-3 border border-transparent rounded-lg text-base font-medium text-white bg-gradient-to-r from-red-600 to-pink-600 hover:from-red-700 hover:to-pink-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-all disabled:opacity-50 disabled:cursor-not-allowed"
            >
              <svg v-if="loading" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              {{ loading ? 'Verificando...' : 'Verificar código' }}
            </button>

            <button
              type="button"
              @click="requestReset"
              :disabled="loading"
              class="w-full text-sm text-gray-600 hover:text-gray-900 transition-colors disabled:opacity-50"
            >
              ¿No recibiste el código? Reenviar
            </button>
          </form>
        </div>

        <!-- PASO 3: Nueva Contraseña -->
        <div v-else-if="step === 'reset-password'">
          <div class="text-center mb-8">
            <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-emerald-100 mb-4">
              <svg class="h-8 w-8 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
              </svg>
            </div>
            <h2 class="text-3xl font-bold text-gray-900 mb-2">Crear nueva contraseña</h2>
            <p class="text-base text-gray-600">
              Tu código ha sido verificado. Ahora puedes establecer una nueva contraseña.
            </p>
          </div>

          <div v-if="message.text" 
               :class="message.type === 'error' ? 'bg-red-50 border-red-200 text-red-700' : 'bg-emerald-50 border-emerald-200 text-emerald-700'"
               class="mb-6 border px-4 py-3 rounded-lg text-sm">
            {{ message.text }}
          </div>

          <form @submit.prevent="resetPassword" class="space-y-6">
            <div>
              <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                Nueva Contraseña
              </label>
              <input
                id="password"
                v-model="password"
                type="password"
                autocomplete="new-password"
                required
                minlength="8"
                placeholder="Mínimo 8 caracteres"
                class="block w-full px-4 py-3 border border-gray-200 rounded-lg text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent transition-all"
              />
            </div>

            <div>
              <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">
                Confirmar Contraseña
              </label>
              <input
                id="password_confirmation"
                v-model="passwordConfirmation"
                type="password"
                autocomplete="new-password"
                required
                minlength="8"
                placeholder="Repite tu contraseña"
                class="block w-full px-4 py-3 border border-gray-200 rounded-lg text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent transition-all"
              />
            </div>

            <button
              type="submit"
              :disabled="loading"
              class="w-full flex justify-center items-center px-6 py-3 border border-transparent rounded-lg text-base font-medium text-white bg-gradient-to-r from-red-600 to-pink-600 hover:from-red-700 hover:to-pink-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-all disabled:opacity-50 disabled:cursor-not-allowed"
            >
              <svg v-if="loading" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              {{ loading ? 'Cambiando contraseña...' : 'Cambiar contraseña' }}
            </button>
          </form>
        </div>

        <!-- PASO 4: Éxito -->
        <div v-else-if="step === 'success'" class="text-center">
          <div class="mx-auto flex items-center justify-center h-20 w-20 rounded-full bg-emerald-100 mb-6">
            <svg class="h-10 w-10 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
          </div>

          <h2 class="text-3xl font-bold text-gray-900 mb-4">
            ¡Contraseña actualizada!
          </h2>
          
          <p class="text-base text-gray-600 mb-8">
            Tu contraseña ha sido cambiada exitosamente.<br/>
            Ya puedes iniciar sesión con tu nueva contraseña.
          </p>

          <router-link 
            to="/login"
            class="inline-flex justify-center items-center px-8 py-3 border border-transparent rounded-lg text-base font-medium text-white bg-gradient-to-r from-red-600 to-pink-600 hover:from-red-700 hover:to-pink-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-all"
          >
            Ir al Login
          </router-link>
        </div>

      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import axios from 'axios'

const router = useRouter()

// Estados
const step = ref('request') // 'request' | 'verify-code' | 'reset-password' | 'success'
const email = ref('')
const code = ref('')
const password = ref('')
const passwordConfirmation = ref('')
const loading = ref(false)
const message = ref({ text: '', type: '' })

// 📧 Paso 1: Solicitar código
const requestReset = async () => {
  loading.value = true
  message.value = { text: '', type: '' }

  try {
    const apiUrl = import.meta.env.VITE_API_URL ? `${import.meta.env.VITE_API_URL}/api` : '/api'
    
    const response = await axios.post(`${apiUrl}/password/forgot`, {
      email: email.value
    })

    if (response.data.success) {
      step.value = 'verify-code'
      message.value = { 
        text: 'Código enviado exitosamente. Revisa tu correo.', 
        type: 'success' 
      }
    }
  } catch (error) {
    console.error('Error:', error)
    message.value = {
      text: error.response?.data?.message || 'Error al enviar el código. Intenta nuevamente.',
      type: 'error'
    }
  } finally {
    loading.value = false
  }
}

// ✅ Paso 2: Validar código
const validateCode = async () => {
  loading.value = true
  message.value = { text: '', type: '' }

  try {
    const apiUrl = import.meta.env.VITE_API_URL ? `${import.meta.env.VITE_API_URL}/api` : '/api'
    
    const response = await axios.post(`${apiUrl}/password/validate-code`, {
      email: email.value,
      code: code.value
    })

    if (response.data.success) {
      step.value = 'reset-password'
      message.value = { 
        text: 'Código verificado correctamente.', 
        type: 'success' 
      }
    }
  } catch (error) {
    console.error('Error:', error)
    message.value = {
      text: error.response?.data?.message || 'Código inválido o expirado.',
      type: 'error'
    }
  } finally {
    loading.value = false
  }
}

// 🔄 Paso 3: Cambiar contraseña
const resetPassword = async () => {
  if (password.value !== passwordConfirmation.value) {
    message.value = {
      text: 'Las contraseñas no coinciden',
      type: 'error'
    }
    return
  }

  loading.value = true
  message.value = { text: '', type: '' }

  try {
    const apiUrl = import.meta.env.VITE_API_URL ? `${import.meta.env.VITE_API_URL}/api` : '/api'
    
    const response = await axios.post(`${apiUrl}/password/reset`, {
      email: email.value,
      code: code.value,
      password: password.value,
      password_confirmation: passwordConfirmation.value
    })

    if (response.data.success) {
      step.value = 'success'
    }
  } catch (error) {
    console.error('Error:', error)
    message.value = {
      text: error.response?.data?.message || 'Error al cambiar la contraseña.',
      type: 'error'
    }
  } finally {
    loading.value = false
  }
}
</script>
