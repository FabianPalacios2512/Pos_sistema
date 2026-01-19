<template>
  <!-- 🔐 Pantalla de Recuperación de Contraseña - DISEÑO PREMIUM -->
  <div class="min-h-screen flex">
    
    <!-- 📸 LADO IZQUIERDO: Panel de Marca Premium (45%) -->
    <div class="hidden lg:flex lg:w-[45%] relative overflow-hidden">
      <!-- Fondo con gradiente slate premium -->
      <div class="absolute inset-0 bg-gradient-to-br from-slate-800 via-slate-900 to-black"></div>
      
      <!-- Imagen con overlay -->
      <img 
        src="/login.png" 
        alt="105 POS - Recuperar Contraseña" 
        class="absolute inset-0 w-full h-full object-cover transition-opacity duration-700"
        :class="imageLoaded ? 'opacity-40' : 'opacity-0'"
        @load="imageLoaded = true"
        loading="eager"
      />
      
      <!-- Overlay gradiente -->
      <div class="absolute inset-0 bg-gradient-to-t from-slate-900 via-slate-900/60 to-slate-900/40"></div>
      
      <!-- Contenido del panel izquierdo -->
      <div class="relative z-10 flex flex-col justify-between h-full p-10">
        
        <!-- Logo y Branding -->
        <div>
          <div class="flex items-center gap-3 mb-16">
            <div class="w-12 h-12 rounded-xl bg-white/10  flex items-center justify-center border border-white/20">
              <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
              </svg>
            </div>
            <span class="text-xl font-bold text-white">105 POS</span>
          </div>

          <!-- Título principal -->
          <h1 class="text-4xl xl:text-5xl font-bold text-white leading-tight mb-6">
            Recupera tu
            <span class="block text-transparent bg-clip-text bg-gradient-to-r from-emerald-400 to-cyan-400">
              acceso seguro
            </span>
          </h1>
          
          <p class="text-lg text-slate-300 leading-relaxed max-w-md">
            Te ayudaremos a restablecer tu contraseña de forma rápida y segura.
          </p>
        </div>

        <!-- Características de seguridad -->
        <div class="space-y-4 mb-8">
          <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-lg bg-white/10 flex items-center justify-center">
              <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
              </svg>
            </div>
            <span class="text-slate-300">Proceso 100% seguro y encriptado</span>
          </div>
          <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-lg bg-white/10 flex items-center justify-center">
              <svg class="w-5 h-5 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
              </svg>
            </div>
            <span class="text-slate-300">Código de 6 dígitos por email</span>
          </div>
          <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-lg bg-white/10 flex items-center justify-center">
              <svg class="w-5 h-5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
              </svg>
            </div>
            <span class="text-slate-300">El código expira en 15 minutos</span>
          </div>
        </div>

        <!-- Footer -->
        <div class="text-sm text-slate-500">
          <p>© 2025 105 POS Pro. Todos los derechos reservados.</p>
        </div>
      </div>
    </div>

    <!-- 📝 LADO DERECHO: Formulario Premium (55%) -->
    <div class="flex-1 flex items-center justify-center px-4 sm:px-6 lg:px-12 bg-white relative">
      <!-- Patrón de fondo sutil -->
      <div class="absolute inset-0 bg-[radial-gradient(#e5e7eb_1px,transparent_1px)] [background-size:20px_20px] opacity-50"></div>
      
      <div class="w-full max-w-md relative z-10">
        
        <!-- PASO 1: Solicitar Email -->
        <div v-if="step === 'request'" class="animate-fade-in">
          <div>
            <router-link to="/login" class="group inline-flex items-center text-sm text-slate-500 hover:text-slate-900 transition-colors mb-8">
              <svg class="w-4 h-4 mr-2 transform group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
              </svg>
              Volver al login
            </router-link>
          </div>

          <div class="mt-2">
            <h2 class="text-3xl font-bold text-slate-900 tracking-tight">¿Olvidaste tu contraseña?</h2>
            <p class="mt-3 text-slate-500">
              Te enviaremos un código de 6 dígitos a tu correo electrónico
            </p>
          </div>

          <div v-if="message.text" 
               :class="message.type === 'error' ? 'bg-red-50 border-red-200 text-red-700' : 'bg-emerald-50 border-emerald-200 text-emerald-700'"
               class="mt-6 border px-4 py-3 rounded-xl text-sm font-medium">
            {{ message.text }}
          </div>

          <form @submit.prevent="requestReset" class="mt-8 space-y-6">
            <div>
              <label for="email" class="block text-sm font-semibold text-slate-700 mb-2">
                Correo Electrónico
              </label>
              <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                  <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                  </svg>
                </div>
                <input
                  id="email"
                  v-model="email"
                  type="email"
                  autocomplete="email"
                  required
                  placeholder="tucorreo@ejemplo.com"
                  class="block w-full pl-12 pr-4 py-3.5 border-2 border-slate-200 rounded-xl text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-4 focus:ring-slate-900/10 focus:border-slate-900 transition-all"
                />
              </div>
            </div>

            <button
              type="submit"
              :disabled="loading"
              class="group w-full h-14 bg-gradient-to-r from-slate-800 to-slate-900 hover:from-slate-900 hover:to-black text-white font-bold rounded-xl shadow-lg shadow-slate-900/25 hover:shadow-xl hover:shadow-slate-900/30 transition-all duration-300 flex items-center justify-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed"
            >
              <svg v-if="loading" class="animate-spin h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              <span>{{ loading ? 'Enviando...' : 'Enviar código de recuperación' }}</span>
              <svg v-if="!loading" class="w-5 h-5 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
              </svg>
            </button>
          </form>
        </div>

        <!-- PASO 2: Ingresar Código - PREMIUM -->
        <div v-else-if="step === 'verify-code'" class="animate-fade-in">
          <div>
            <button @click="step = 'request'" class="group inline-flex items-center text-sm text-slate-500 hover:text-slate-900 transition-colors mb-8">
              <svg class="w-4 h-4 mr-2 transform group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
              </svg>
              Volver
            </button>
          </div>

          <div class="text-center mb-8">
            <div class="relative inline-flex items-center justify-center mb-6">
              <div class="absolute w-20 h-20 rounded-full bg-slate-100"></div>
              <div class="relative w-16 h-16 rounded-full bg-gradient-to-br from-slate-700 to-slate-900 flex items-center justify-center shadow-lg shadow-slate-900/20">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
              </div>
            </div>
            <h2 class="text-3xl font-bold text-slate-900 tracking-tight mb-2">Revisa tu correo</h2>
            <p class="text-slate-500">
              Hemos enviado un código de 6 dígitos a<br/>
              <strong class="text-slate-900">{{ email }}</strong>
            </p>
          </div>

          <div v-if="message.text" 
               :class="message.type === 'error' ? 'bg-red-50 border-red-200 text-red-700' : 'bg-emerald-50 border-emerald-200 text-emerald-700'"
               class="mb-6 border px-4 py-3 rounded-xl text-sm font-medium">
            {{ message.text }}
          </div>

          <form @submit.prevent="validateCode" class="space-y-6">
            <div>
              <label for="code" class="block text-sm font-semibold text-slate-700 mb-2 text-center">
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
                placeholder="000000"
                class="block w-full px-4 py-4 text-center text-2xl font-mono font-bold tracking-[0.5em] border-2 border-slate-200 rounded-xl text-slate-900 placeholder-slate-300 focus:outline-none focus:ring-4 focus:ring-slate-900/10 focus:border-slate-900 transition-all"
              />
              <p class="mt-3 text-xs text-slate-400 text-center">El código expira en 15 minutos</p>
            </div>

            <button
              type="submit"
              :disabled="loading || code.length !== 6"
              class="group w-full h-14 bg-gradient-to-r from-slate-800 to-slate-900 hover:from-slate-900 hover:to-black text-white font-bold rounded-xl shadow-lg shadow-slate-900/25 hover:shadow-xl hover:shadow-slate-900/30 transition-all duration-300 flex items-center justify-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed"
            >
              <svg v-if="loading" class="animate-spin h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              <span>{{ loading ? 'Verificando...' : 'Verificar código' }}</span>
            </button>

            <button
              type="button"
              @click="requestReset"
              :disabled="loading"
              class="w-full text-sm text-slate-500 hover:text-slate-900 font-medium transition-colors disabled:opacity-50"
            >
              ¿No recibiste el código? <span class="underline">Reenviar</span>
            </button>
          </form>
        </div>

        <!-- PASO 3: Nueva Contraseña - PREMIUM -->
        <div v-else-if="step === 'reset-password'" class="animate-fade-in">
          <div class="text-center mb-8">
            <div class="relative inline-flex items-center justify-center mb-6">
              <div class="absolute w-20 h-20 rounded-full bg-emerald-100"></div>
              <div class="relative w-16 h-16 rounded-full bg-gradient-to-br from-emerald-500 to-emerald-600 flex items-center justify-center shadow-lg shadow-emerald-500/25">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
              </div>
            </div>
            <h2 class="text-3xl font-bold text-slate-900 tracking-tight mb-2">Crear nueva contraseña</h2>
            <p class="text-slate-500">
              Tu código ha sido verificado. Establece una nueva contraseña segura.
            </p>
          </div>

          <div v-if="message.text" 
               :class="message.type === 'error' ? 'bg-red-50 border-red-200 text-red-700' : 'bg-emerald-50 border-emerald-200 text-emerald-700'"
               class="mb-6 border px-4 py-3 rounded-xl text-sm font-medium">
            {{ message.text }}
          </div>

          <form @submit.prevent="resetPassword" class="space-y-5">
            <div>
              <label for="password" class="block text-sm font-semibold text-slate-700 mb-2">
                Nueva Contraseña
              </label>
              <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                  <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                  </svg>
                </div>
                <input
                  id="password"
                  v-model="password"
                  type="password"
                  autocomplete="new-password"
                  required
                  minlength="8"
                  placeholder="Mínimo 8 caracteres"
                  class="block w-full pl-12 pr-4 py-3.5 border-2 border-slate-200 rounded-xl text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-4 focus:ring-slate-900/10 focus:border-slate-900 transition-all"
                />
              </div>
            </div>

            <div>
              <label for="password_confirmation" class="block text-sm font-semibold text-slate-700 mb-2">
                Confirmar Contraseña
              </label>
              <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                  <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                  </svg>
                </div>
                <input
                  id="password_confirmation"
                  v-model="passwordConfirmation"
                  type="password"
                  autocomplete="new-password"
                  required
                  minlength="8"
                  placeholder="Repite tu contraseña"
                  class="block w-full pl-12 pr-4 py-3.5 border-2 border-slate-200 rounded-xl text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-4 focus:ring-slate-900/10 focus:border-slate-900 transition-all"
                />
              </div>
            </div>

            <button
              type="submit"
              :disabled="loading"
              class="group w-full h-14 bg-gradient-to-r from-slate-800 to-slate-900 hover:from-slate-900 hover:to-black text-white font-bold rounded-xl shadow-lg shadow-slate-900/25 hover:shadow-xl hover:shadow-slate-900/30 transition-all duration-300 flex items-center justify-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed mt-6"
            >
              <svg v-if="loading" class="animate-spin h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              <span>{{ loading ? 'Cambiando contraseña...' : 'Cambiar contraseña' }}</span>
            </button>
          </form>
        </div>

        <!-- PASO 4: Éxito - PREMIUM -->
        <div v-else-if="step === 'success'" class="text-center animate-fade-in">
          <div class="relative inline-flex items-center justify-center mb-6">
            <div class="absolute w-24 h-24 rounded-full bg-emerald-100 animate-ping opacity-30"></div>
            <div class="absolute w-20 h-20 rounded-full bg-emerald-50"></div>
            <div class="relative w-16 h-16 rounded-full bg-gradient-to-br from-emerald-400 to-emerald-600 flex items-center justify-center shadow-lg shadow-emerald-500/30">
              <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
              </svg>
            </div>
          </div>

          <h2 class="text-3xl font-bold text-slate-900 tracking-tight mb-3">
            ¡Contraseña actualizada!
          </h2>
          
          <p class="text-slate-500 mb-8 leading-relaxed">
            Tu contraseña ha sido cambiada exitosamente.<br/>
            Ya puedes iniciar sesión con tu nueva contraseña.
          </p>

          <router-link 
            to="/login"
            class="group inline-flex justify-center items-center gap-2 px-8 py-3.5 bg-gradient-to-r from-slate-800 to-slate-900 hover:from-slate-900 hover:to-black text-white font-bold rounded-xl shadow-lg shadow-slate-900/25 hover:shadow-xl hover:shadow-slate-900/30 transition-all duration-300"
          >
            <span>Ir al Login</span>
            <svg class="w-5 h-5 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
            </svg>
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
const imageLoaded = ref(false) // 🖼️ Estado de carga de imagen

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

<style scoped>
.animate-fade-in {
  animation: fadeIn 0.5s ease-out forwards;
}

@keyframes fadeIn {
  from { 
    opacity: 0; 
    transform: translateY(10px); 
  }
  to { 
    opacity: 1; 
    transform: translateY(0); 
  }
}
</style>