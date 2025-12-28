<template>
  <!-- 🎨 SPLIT SCREEN LAYOUT - Diseño Corporativo SaaS -->
  <div class="min-h-screen flex">
    
    <!-- 📸 LADO IZQUIERDO: Imagen de Marca (Oculto en móviles) -->
    <div class="hidden lg:flex lg:w-1/2 xl:w-[45%] relative overflow-hidden bg-gradient-to-br from-emerald-600 via-emerald-700 to-emerald-900">
      <!-- Skeleton Loader mientras carga la imagen -->
      <div 
        v-if="!imageLoaded" 
        class="absolute inset-0 bg-gradient-to-br from-emerald-600 via-emerald-700 to-emerald-900 animate-pulse"
      >
        <div class="absolute inset-0 bg-gradient-to-t from-black/20 to-transparent"></div>
      </div>
      
      <!-- Imagen de Login con lazy loading optimizado -->
      <img 
        src="/login.png" 
        alt="105 POS Pro - Sistema de Punto de Venta" 
        class="absolute inset-0 w-full h-full object-cover transition-opacity duration-700 ease-in-out"
        :class="imageLoaded ? 'opacity-100' : 'opacity-0'"
        @load="imageLoaded = true"
        loading="eager"
        decoding="async"
      />
      
      <!-- Overlay oscuro sutil para mejorar contraste -->
      <div class="absolute inset-0 bg-gradient-to-t from-black/40 via-black/10 to-transparent"></div>
      
      <!-- Badge flotante con logo/texto (Con animación de entrada) -->
      <transition
        enter-active-class="transition ease-out duration-500 delay-300"
        enter-from-class="translate-y-4 opacity-0"
        enter-to-class="translate-y-0 opacity-100"
      >
        <div v-if="imageLoaded" class="absolute bottom-8 left-8 right-8 z-10">
          <div class="bg-white/10 backdrop-blur-md rounded-2xl p-6 border border-white/20">
            <h3 class="text-2xl font-bold text-white mb-2">Sistema POS Empresarial</h3>
            <p class="text-white/80 text-sm">Gestiona tu negocio de forma profesional con tecnología de última generación</p>
          </div>
        </div>
      </transition>
    </div>

    <!-- 📝 LADO DERECHO: Formulario de Login -->
    <div class="flex-1 flex items-center justify-center px-4 sm:px-6 lg:px-8 bg-white">
      <div class="w-full max-w-md space-y-8">
        
        <!-- Logo -->
        <div>
          <h1 class="text-3xl font-bold text-gray-900">105 POS Pro</h1>
        </div>

        <!-- Título y Subtítulo -->
        <div class="mt-6">
          <h2 class="text-3xl font-bold text-gray-900">
            ¡Bienvenido de nuevo!
          </h2>
          <p class="mt-2 text-base text-gray-600">
            Ingresa a tu punto de venta.
          </p>
        </div>

        <!-- Mensajes de Error/Éxito -->
        <div v-if="message.text" 
             :class="message.type === 'error' ? 'bg-red-50 border-red-200 text-red-700' : 'bg-emerald-50 border-emerald-200 text-emerald-700'"
             class="mt-6 border px-4 py-3 rounded-lg text-sm">
          {{ message.text }}
        </div>

        <form @submit.prevent="handleLogin" class="mt-8 space-y-6">
          
          <!-- Campo Email -->
          <div>
            <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
              Correo Electrónico
            </label>
            <input
              id="email"
              v-model="credentials.email"
              type="email"
              autocomplete="email"
              required
              placeholder="tucorreo@ejemplo.com"
              class="block w-full px-4 py-3 border border-gray-200 rounded-lg text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all"
              :class="{ 'border-red-300 focus:ring-red-500': errors.email }"
            />
            <p v-if="errors.email" class="mt-2 text-sm text-red-600">{{ errors.email }}</p>
          </div>

          <!-- Campo Contraseña -->
          <div>
            <div class="flex items-center justify-between mb-2">
              <label for="password" class="block text-sm font-medium text-gray-700">
                Contraseña
              </label>
              <router-link to="/forgot-password" class="text-xs font-medium text-emerald-600 hover:text-emerald-500 transition-colors">
                ¿Olvidaste tu contraseña?
              </router-link>
            </div>
            <div class="relative">
              <input
                id="password"
                v-model="credentials.password"
                :type="showPassword ? 'text' : 'password'"
                autocomplete="current-password"
                required
                placeholder="Ingresa tu contraseña"
                class="block w-full px-4 py-3 pr-12 border border-gray-200 rounded-lg text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all"
                :class="{ 'border-red-300 focus:ring-red-500': errors.password }"
              />
              <button
                type="button"
                @click="showPassword = !showPassword"
                class="absolute inset-y-0 right-0 pr-4 flex items-center text-gray-400 hover:text-gray-600 transition-colors"
              >
                <svg v-if="showPassword" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                </svg>
                <svg v-else class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21"/>
                </svg>
              </button>
            </div>
            <p v-if="errors.password" class="mt-2 text-sm text-red-600">{{ errors.password }}</p>
          </div>

          <!-- Botón Principal - Verde Esmeralda -->
          <div class="pt-2">
            <button
              type="submit"
              :disabled="loading"
              class="w-full flex items-center justify-center py-3.5 px-6 text-base font-medium text-white bg-emerald-600 hover:bg-emerald-700 rounded-lg shadow-lg shadow-emerald-600/20 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-200 transform hover:scale-[1.02] active:scale-[0.98]"
            >
              <svg v-if="loading" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              {{ loading ? 'Iniciando sesión...' : 'Iniciar Sesión' }}
            </button>
          </div>
        </form>

        <!-- Separador -->
        <div class="mt-6">
          <div class="relative">
            <div class="absolute inset-0 flex items-center">
              <div class="w-full border-t border-gray-300"></div>
            </div>
            <div class="relative flex justify-center text-sm">
              <span class="px-2 bg-white text-gray-500">O continúa con</span>
            </div>
          </div>
        </div>

        <!-- Botón Google (Secundario) -->
        <div class="mt-6">
          <button
            type="button"
            @click="loginWithGoogle"
            :disabled="isGoogleLoading"
            class="w-full flex items-center justify-center gap-3 px-4 py-3 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-300 transition-all disabled:opacity-50 disabled:cursor-not-allowed"
          >
            <svg v-if="!isGoogleLoading" class="w-5 h-5" viewBox="0 0 24 24">
              <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
              <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
              <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
              <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
            </svg>
            <svg v-else class="w-5 h-5 animate-spin text-gray-400" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span>{{ isGoogleLoading ? 'Conectando...' : 'Continuar con Google' }}</span>
          </button>
        </div>

        <!-- Link de Registro -->
        <div class="mt-6 text-center">
          <p class="text-sm text-gray-600">
            ¿No tienes cuenta? 
            <a href="https://105pos.pro/register" class="font-semibold text-emerald-600 hover:text-emerald-500 transition-colors">
              Regístrate aquí
            </a>
          </p>
        </div>

        <!-- Footer Copyright -->
        <div class="mt-10 pt-6 border-t border-gray-100">
          <p class="text-xs text-center text-gray-400">© 2025 105 POS Pro. Todos los derechos reservados.</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import axios from 'axios'
import authService from '../services/authService.js'
import googleAuthService from '../services/googleAuthService.js'

// Router
const router = useRouter()

// Estado reactivo
const loading = ref(false)
const showPassword = ref(false)
const isGoogleLoading = ref(false)
const imageLoaded = ref(false) // 🖼️ Estado de carga de imagen

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

// Login con Google
const loginWithGoogle = async () => {
  try {
    isGoogleLoading.value = true
    
    // Generar URL de Google OAuth para login
    const authUrl = await googleAuthService.initiateGoogleAuth({
      mode: 'login' // Indicar que es login, no registro
    })
    
    // Redirigir a Google
    window.location.href = authUrl
    
  } catch (error) {
    console.error('Error iniciando Google Auth:', error)
    message.text = 'No se pudo conectar con Google. Intenta nuevamente.'
    message.type = 'error'
    isGoogleLoading.value = false
  }
}

// Verificar mensaje de registro exitoso
onMounted(async () => {
  // 🎯 AUTO-LOGIN desde dominio central
  const autoLoginCreds = sessionStorage.getItem('auto_login_credentials')
  if (autoLoginCreds) {
    try {
      loading.value = true
      message.text = 'Iniciando sesión automáticamente...'
      message.type = 'info'
      
      const creds = JSON.parse(autoLoginCreds)
      
      // Limpiar credenciales temporales
      sessionStorage.removeItem('auto_login_credentials')
      
      // 🔐 LOGIN DIRECTO EN EL TENANT (sin redirección)
      const response = await authService.login({
        email: creds.email,
        password: creds.password
      })

      console.log('✅ Auto-login exitoso')
      message.text = '✅ Inicio de sesión exitoso. Redirigiendo...'
      message.type = 'success'

      // Verificar rol y redireccionar al POS
      const user = response.data?.user || response.user
      
      setTimeout(() => {
        if (user?.is_super_admin || user?.role?.name === 'superadmin') {
          router.push('/admin/god-mode')
        } else {
          router.push('/pos')
        }
      }, 500)
      
      return
    } catch (error) {
      console.error('Error en auto-login:', error)
      sessionStorage.removeItem('auto_login_credentials')
      message.text = 'Error al iniciar sesión. Por favor, intenta manualmente.'
      message.type = 'error'
      loading.value = false
    }
  }
  
  const registrationSuccess = localStorage.getItem('registration_success')
  if (registrationSuccess) {
    const data = JSON.parse(registrationSuccess)
    message.text = `🎉 ${data.message}`
    message.type = 'success'
    
    // Limpiar el mensaje después de mostrarlo
    localStorage.removeItem('registration_success')
    
    // Limpiar el mensaje después de 8 segundos
    setTimeout(() => {
      message.text = ''
      message.type = ''
    }, 8000)
  }

  // 🎯 Verificar si hay un token de login con Google en la URL
  const urlParams = new URLSearchParams(window.location.search)
  const googleLoginToken = urlParams.get('google_login_token')

  if (googleLoginToken) {
    console.log('🔑 Token de Google detectado en URL, procesando autenticación...')
    
    try {
      loading.value = true
      message.text = 'Iniciando sesión con Google...'
      message.type = 'info'
      
      // 🔥 Crear instancia de axios sin interceptores para evitar redirección
      const cleanAxios = axios.create({
        baseURL: window.location.origin,
        headers: {
          'Content-Type': 'application/json'
        }
      })
      
      console.log('📡 Obteniendo datos de sesión de Google desde:', `${window.location.origin}/api/auth/google/login-session?token=${googleLoginToken}`)
      
      // Obtener sesión usando el token
      const response = await cleanAxios.get(`/api/auth/google/login-session?token=${googleLoginToken}`)
      
      console.log('📦 Respuesta del servidor:', response.data)
      
      if (response.data.success && response.data.data) {
        const { token, user } = response.data.data
        
        console.log('✅ Datos de usuario recibidos:', user)
        console.log('✅ Rol del usuario:', user.role)
        console.log('✅ Permisos del rol:', user.role?.permissions)
        console.log('✅ Token Sanctum recibido:', token.substring(0, 20) + '...')
        
        // 🔥 IMPORTANTE: Guardar token Sanctum real y datos de usuario
        localStorage.setItem('authToken', token)
        localStorage.setItem('user', JSON.stringify(user))
        
        // Configurar token en axios global
        axios.defaults.headers.common['Authorization'] = `Bearer ${token}`
        
        console.log('✅ Token Sanctum guardado en localStorage y axios configurado')
        console.log('✅ Usuario autenticado con Google:', user.name, '(', user.email, ')')
        console.log('✅ Usuario tiene', user.role?.permissions?.length || 0, 'permisos')
        
        message.text = '✅ Inicio de sesión exitoso. Redirigiendo...'
        message.type = 'success'
        
        // Pequeño delay para que el usuario vea el mensaje de éxito
        setTimeout(() => {
          console.log('🔄 Redirigiendo a /pos')
          window.location.href = '/pos'
        }, 500)
      } else {
        console.error('❌ Error en respuesta del servidor:', response.data.message)
        message.text = response.data.message || 'Error al iniciar sesión con Google.'
        message.type = 'error'
        
        // Limpiar URL
        window.history.replaceState({}, document.title, '/login')
      }
    } catch (error) {
      console.error('❌ Error obteniendo sesión de Google:', error)
      console.error('Detalles del error:', {
        message: error.message,
        response: error.response?.data,
        status: error.response?.status
      })
      
      message.text = error.response?.data?.message || 'Error al iniciar sesión con Google. Intenta nuevamente.'
      message.type = 'error'
      
      // Limpiar URL
      window.history.replaceState({}, document.title, '/login')
    } finally {
      loading.value = false
    }
  }
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
    // 🎯 DETECTAR SI ESTAMOS EN DOMINIO PRINCIPAL (login centralizado)
    const hostname = window.location.hostname
    const isMainDomain = ['localhost', '127.0.0.1', '105pos.pro', 'www.105pos.pro'].includes(hostname)
    
    if (isMainDomain) {
      // ============================================
      // 🚀 LOGIN CENTRALIZADO (Smart Login)
      // ============================================
      console.log('🎯 Login centralizado detectado en dominio principal')
      
      const response = await axios.post('/api/central/login', {
        email: credentials.email.trim(),
        password: credentials.password
      })

      if (response.data.success) {
        message.text = '✅ ' + response.data.message
        message.type = 'success'
        
        console.log('✅ Tenant encontrado:', response.data.data.tenant_domain)
        console.log('🔄 Redirigiendo a:', response.data.data.redirect_url)
        
        // Guardar credenciales temporalmente para auto-login en el tenant
        sessionStorage.setItem('auto_login_credentials', JSON.stringify({
          email: response.data.data.credentials.email,
          password: response.data.data.credentials.password
        }))
        
        // Esperar un momento y redirigir al tenant
        setTimeout(() => {
          window.location.href = response.data.data.redirect_url + '/login'
        }, 1000)
        
        return
      }
    }
    
    // ============================================
    // 🔐 LOGIN TRADICIONAL (en subdominio)
    // ============================================
    const response = await authService.login({
      email: credentials.email.trim(),
      password: credentials.password
    })

    message.text = 'Inicio de sesión exitoso'
    message.type = 'success'

    // Aplicar configuración pendiente del onboarding si existe
    const pendingConfig = localStorage.getItem('pending_onboarding_config')
    if (pendingConfig) {
      try {
        const config = JSON.parse(pendingConfig)
        // ✅ IMPORTANTE: Agregar onboarding_completed al guardar config pendiente
        config.onboarding_completed = true
        await axios.put('/api/tenant/system-settings', config)
        localStorage.removeItem('pending_onboarding_config')
        console.log('✅ Configuración de onboarding aplicada exitosamente con onboarding_completed = true')
      } catch (error) {
        console.error('Error aplicando configuración pendiente:', error)
      }
    }

    // Verificar rol y redireccionar
    const user = response.data?.user || response.user // Compatibilidad con ambas estructuras
    console.log('Usuario autenticado:', user)

    // 🎯 VERIFICAR SI HAY SUBDOMAIN PENDIENTE (desde registro trial)
    const registrationSuccess = localStorage.getItem('registration_success')
    if (registrationSuccess) {
      const data = JSON.parse(registrationSuccess)
      if (data.subdomain) {
        console.log('✅ Redirigiendo a tenant:', data.subdomain)
        localStorage.removeItem('registration_success')
        
        // Esperar un momento para mostrar el mensaje
        setTimeout(() => {
          window.location.href = data.subdomain + '/pos'
        }, 1000)
        return
      }
    }

    // Esperar un momento para mostrar el mensaje de éxito
    setTimeout(() => {
      // Si es super admin, ir directo al panel god mode
      if (user?.is_super_admin || user?.role?.name === 'superadmin') {
        router.push('/admin/god-mode')
        return
      }

      // 🎯 REDIRECCIÓN INTELIGENTE BASADA EN PERMISOS
      if (user?.role?.permissions) {
        const permissions = typeof user.role.permissions === 'string' 
          ? JSON.parse(user.role.permissions) 
          : user.role.permissions

        // Mapa de permisos a rutas (en orden de prioridad)
        const permissionRoutes = [
          { permission: 'pos.access', route: '/pos' },
          { permission: 'dashboard.view', route: '/pos' },
          { permission: 'invoices.view', route: '/invoices' },
          { permission: 'products.view', route: '/products' },
          { permission: 'customers.view', route: '/customers' },
          { permission: 'reports.view', route: '/reports' },
          { permission: 'users.view', route: '/users' },
          { permission: 'cash_register.view', route: '/cash-admin' },
          { permission: 'expenses.view', route: '/expenses' },
          { permission: 'settings.view', route: '/settings' }
        ]

        // Buscar la primera ruta para la que el usuario tiene permiso
        for (const { permission, route } of permissionRoutes) {
          if (permissions.includes(permission)) {
            router.push(route)
            return
          }
        }

        // Si no tiene ningún permiso específico, ir al POS por defecto
        router.push('/pos')
      } else {
        // Fallback: Si no tiene rol definido, ir al POS
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
  router.push('/pos')
}
</script>

<style scoped>
/* 🎨 SPLIT SCREEN DESIGN - Estilos Personalizados */

/* Animación de spinner */
.animate-spin {
  animation: spin 1s linear infinite;
}

@keyframes spin {
  from { transform: rotate(0deg); }
  to { transform: rotate(360deg); }
}

/* Focus ring verde esmeralda (Marca) */
input:focus {
  box-shadow: 0 0 0 4px rgba(5, 150, 105, 0.1);
  border-color: #059669;
}

/* Smooth transitions */
input,
button,
a {
  transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
}

/* Efecto hover en botón principal */
button[type="submit"]:hover:not(:disabled) {
  transform: translateY(-1px);
  box-shadow: 0 10px 25px -5px rgba(5, 150, 105, 0.3);
}

/* Animación de entrada suave */
@keyframes fadeInUp {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

/* Aplicar animación al contenedor del formulario */
.space-y-8 {
  animation: fadeInUp 0.6s ease-out;
}
</style>