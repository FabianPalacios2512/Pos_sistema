<template>
  <!-- 🔓 Pantalla de Restablecer Contraseña -->
  <div class="min-h-screen flex">
    
    <!-- 📸 LADO IZQUIERDO: Imagen de Marca -->
    <div class="hidden lg:flex lg:w-1/2 xl:w-[45%] relative overflow-hidden bg-gradient-to-br from-emerald-600 via-emerald-700 to-emerald-900">
      <!-- Skeleton Loader -->
      <div 
        v-if="!imageLoaded" 
        class="absolute inset-0 bg-gradient-to-br from-emerald-600 via-emerald-700 to-emerald-900 animate-pulse"
      ></div>
      
      <img 
        src="/login.png" 
        alt="105 POS - Nueva Contraseña" 
        class="absolute inset-0 w-full h-full object-cover opacity-70 transition-opacity duration-700 ease-in-out"
        :class="imageLoaded ? 'opacity-70' : 'opacity-0'"
        @load="imageLoaded = true"
        loading="eager"
        decoding="async"
      />
      <div class="absolute inset-0 bg-gradient-to-t from-black/50 via-black/20 to-transparent"></div>
      
      <transition
        enter-active-class="transition ease-out duration-500 delay-300"
        enter-from-class="translate-y-4 opacity-0"
        enter-to-class="translate-y-0 opacity-100"
      >
        <div v-if="imageLoaded" class="absolute bottom-8 left-8 right-8 z-10">
          <div class="bg-white/10  rounded-2xl p-6 border border-white/20">
            <h3 class="text-2xl font-bold text-white mb-2">🔓 Nueva Contraseña</h3>
            <p class="text-white/80 text-sm">Crea una contraseña segura y única para tu cuenta</p>
          </div>
        </div>
      </transition>
    </div>

    <!-- 📝 LADO DERECHO: Formulario -->
    <div class="flex-1 flex items-center justify-center px-4 sm:px-6 lg:px-8 bg-white">
      <div class="w-full max-w-md space-y-8">
        
        <!-- Validando token... -->
        <div v-if="validating" class="text-center">
          <div class="inline-block animate-spin rounded-full h-12 w-12 border-b-2 border-emerald-600"></div>
          <p class="mt-4 text-gray-600">Validando enlace...</p>
        </div>

        <!-- Token inválido o expirado -->
        <div v-else-if="tokenError" class="text-center">
          <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-red-100">
            <svg class="h-8 w-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
          </div>
          <h2 class="mt-6 text-3xl font-bold text-gray-900">Enlace inválido</h2>
          <p class="mt-2 text-base text-gray-600">{{ tokenError }}</p>
          <router-link
            to="/forgot-password"
            class="mt-6 inline-block px-6 py-3 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition-all"
          >
            Solicitar nuevo enlace
          </router-link>
        </div>

        <!-- Formulario de nueva contraseña -->
        <div v-else-if="!success">
          <div>
            <h2 class="text-3xl font-bold text-gray-900">
              Nueva Contraseña
            </h2>
            <p class="mt-2 text-base text-gray-600">
              Ingresa tu nueva contraseña (mínimo 8 caracteres)
            </p>
          </div>

          <!-- Mensajes -->
          <div v-if="message.text" 
               :class="message.type === 'error' ? 'bg-red-50 border-red-200 text-red-700' : 'bg-emerald-50 border-emerald-200 text-emerald-700'"
               class="mt-6 border px-4 py-3 rounded-lg text-sm">
            {{ message.text }}
          </div>

          <form @submit.prevent="resetPassword" class="mt-8 space-y-6">
            <!-- Nueva Contraseña -->
            <div>
              <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                Nueva Contraseña
              </label>
              <div class="relative">
                <input
                  id="password"
                  v-model="password"
                  :type="showPassword ? 'text' : 'password'"
                  required
                  minlength="8"
                  placeholder="Mínimo 8 caracteres"
                  class="block w-full px-4 py-3 pr-12 border border-gray-200 rounded-lg text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all"
                />
                <button
                  type="button"
                  @click="showPassword = !showPassword"
                  class="absolute inset-y-0 right-0 pr-4 flex items-center text-gray-400 hover:text-gray-600"
                >
                  <svg v-if="showPassword" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                  </svg>
                  <svg v-else class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                  </svg>
                </button>
              </div>
              <p class="mt-2 text-xs text-gray-500">
                💡 Usa una combinación de letras, números y símbolos
              </p>
            </div>

            <!-- Confirmar Contraseña -->
            <div>
              <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">
                Confirmar Contraseña
              </label>
              <div class="relative">
                <input
                  id="password_confirmation"
                  v-model="passwordConfirmation"
                  :type="showPasswordConfirm ? 'text' : 'password'"
                  required
                  minlength="8"
                  placeholder="Repite tu contraseña"
                  class="block w-full px-4 py-3 pr-12 border border-gray-200 rounded-lg text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all"
                  :class="{ 'border-red-300': password && passwordConfirmation && password !== passwordConfirmation }"
                />
                <button
                  type="button"
                  @click="showPasswordConfirm = !showPasswordConfirm"
                  class="absolute inset-y-0 right-0 pr-4 flex items-center text-gray-400 hover:text-gray-600"
                >
                  <svg v-if="showPasswordConfirm" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                  </svg>
                  <svg v-else class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                  </svg>
                </button>
              </div>
              <p v-if="password && passwordConfirmation && password !== passwordConfirmation" class="mt-2 text-sm text-red-600">
                Las contraseñas no coinciden
              </p>
            </div>

            <button
              type="submit"
              :disabled="loading || password !== passwordConfirmation || !password || !passwordConfirmation"
              class="w-full flex justify-center items-center px-6 py-3 border border-transparent rounded-lg text-base font-medium text-white bg-gradient-to-r from-emerald-600 to-green-600 hover:from-emerald-700 hover:to-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition-all disabled:opacity-50 disabled:cursor-not-allowed"
            >
              <svg v-if="loading" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              {{ loading ? 'Actualizando...' : 'Cambiar contraseña' }}
            </button>
          </form>
        </div>

        <!-- Éxito -->
        <div v-else class="text-center">
          <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-emerald-100">
            <svg class="h-8 w-8 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
          </div>
          <h2 class="mt-6 text-3xl font-bold text-gray-900">¡Contraseña actualizada!</h2>
          <p class="mt-2 text-base text-gray-600">
            Tu contraseña se cambió exitosamente. Ya puedes iniciar sesión.
          </p>
          <router-link
            to="/login"
            class="mt-8 inline-block w-full px-6 py-3 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition-all font-medium"
          >
            Ir al login
          </router-link>
        </div>

      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import axios from 'axios'

const route = useRoute()
const router = useRouter()

const validating = ref(true)
const tokenError = ref('')
const success = ref(false)
const loading = ref(false)
const imageLoaded = ref(false) // 🖼️ Estado de carga de imagen

const token = ref('')
const email = ref('')
const password = ref('')
const passwordConfirmation = ref('')
const showPassword = ref(false)
const showPasswordConfirm = ref(false)

const message = ref({
  type: '',
  text: ''
})

onMounted(async () => {
  // Obtener token y email de la URL
  token.value = route.query.token
  email.value = route.query.email

  if (!token.value || !email.value) {
    tokenError.value = 'El enlace no es válido. Por favor, solicita uno nuevo.'
    validating.value = false
    return
  }

  // Validar que el token sea válido
  try {
    await axios.post('/api/password/validate-token', {
      token: token.value,
      email: email.value
    })
    validating.value = false
  } catch (error) {
    validating.value = false
    if (error.response?.status === 410) {
      tokenError.value = 'Este enlace ya expiró. Por favor, solicita uno nuevo.'
    } else if (error.response?.status === 404) {
      tokenError.value = 'Este enlace no es válido o ya fue utilizado.'
    } else {
      tokenError.value = 'Error al validar el enlace. Intenta de nuevo.'
    }
  }
})

const resetPassword = async () => {
  if (password.value !== passwordConfirmation.value) {
    message.value = {
      type: 'error',
      text: 'Las contraseñas no coinciden'
    }
    return
  }

  if (password.value.length < 8) {
    message.value = {
      type: 'error',
      text: 'La contraseña debe tener al menos 8 caracteres'
    }
    return
  }

  try {
    loading.value = true
    message.value = { type: '', text: '' }

    await axios.post('/api/password/reset', {
      token: token.value,
      email: email.value,
      password: password.value,
      password_confirmation: passwordConfirmation.value
    })

    success.value = true
    
  } catch (error) {
    console.error('Error al resetear contraseña:', error)
    message.value = {
      type: 'error',
      text: error.response?.data?.message || 'Error al cambiar la contraseña. Intenta de nuevo.'
    }
  } finally {
    loading.value = false
  }
}
</script>
