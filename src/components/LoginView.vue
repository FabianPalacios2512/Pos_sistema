<script setup>
import { ref, reactive, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import axios from 'axios'
import authService from '../services/authService.js'
import googleAuthService from '../services/googleAuthService.js'
import RadioPlayerModal from './RadioPlayerModal.vue'

// Router
const router = useRouter()

// Estado reactivo
const loading = ref(false)
const showPassword = ref(false)
const isGoogleLoading = ref(false)
const imageLoaded = ref(false) // Estado de carga de imagen
const showNotFoundModal = ref(false)
const radioOpen = ref(false)
const blockedState = ref(null) // { type: 'account'|'ip', message: '', supportUrl: '' }

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
  // AUTO-LOGIN desde dominio central (SILENCIOSO - sin mostrar pantalla de login)
  const autoLoginCreds = sessionStorage.getItem('auto_login_credentials')
  
  if (autoLoginCreds) {
    try {
      loading.value = true
      message.text = 'Ingresando a tu cuenta...'
      message.type = 'info'
      
      const creds = JSON.parse(autoLoginCreds)
      // Limpiar credenciales temporales ANTES de hacer el login
      sessionStorage.removeItem('auto_login_credentials')
      
      // LOGIN DIRECTO EN EL TENANT (sin redirección)
      const response = await authService.login({
        email: creds.email,
        password: creds.password
      })

      // Verificar rol y redireccionar INMEDIATAMENTE
      const user = response.data?.user || response.user
      
      if (user?.is_super_admin || user?.role?.name === 'superadmin') {
        window.location.href = '/admin/god-mode'
        return
      }
      
      // VERIFICAR SI EL BACKEND INDICA QUE NECESITA SELECCIONAR PLAN
      if (response.needs_plan_selection && response.tenant) {
        const tenant = response.tenant
        const subdomain = tenant.id
        const companyName = tenant.business_name || ''
        
        const isLocalhost = window.location.hostname.includes('localhost') || window.location.hostname === '127.0.0.1'
        const baseUrl = isLocalhost ? `http://localhost:${window.location.port || 3000}` : 'https://105pos.pro'
        
        const params = new URLSearchParams()
        if (subdomain) params.append('tenant_id', subdomain)
        if (subdomain) params.append('subdomain', subdomain)
        if (companyName) params.append('company', companyName)
        
        // CRÍTICO: Pasar el token para que PlanSelection pueda usarlo después
        const currentToken = localStorage.getItem('authToken')
        if (currentToken) {
          params.append('auth_token', encodeURIComponent(currentToken))
        }
        
        window.location.href = `${baseUrl}/select-plan?${params.toString()}`
        return
      }
      
      // Si todo está bien, ir al POS
      window.location.href = '/pos'
      
      return
    } catch (error) {
      sessionStorage.removeItem('auto_login_credentials')
      message.text = 'Error al iniciar sesión. Por favor, intenta manualmente.'
      message.type = 'error'
      loading.value = false
    }
  }
  
  const registrationSuccess = localStorage.getItem('registration_success')
  if (registrationSuccess) {
    const data = JSON.parse(registrationSuccess)
    message.text = `${data.message}`
    message.type = 'success'
    
    // Limpiar el mensaje después de mostrarlo
    localStorage.removeItem('registration_success')
    
    // Limpiar el mensaje después de 8 segundos
    setTimeout(() => {
      message.text = ''
      message.type = ''
    }, 8000)
  }

  // Verificar si hay un token de login con Google en la URL
  const urlParams = new URLSearchParams(window.location.search)
  const googleLoginToken = urlParams.get('google_login_token')
  const centralLoginToken = urlParams.get('central_login_token')
  
  // Verificar si hay un mensaje de error de tenant
  const reason = urlParams.get('reason')
  const errorMsg = urlParams.get('message')
  
  if (reason === 'tenant-error' && errorMsg) {
    message.text = errorMsg
    message.type = 'error'
    // Limpiar URL
    window.history.replaceState({}, document.title, '/login')
  } else if (reason === 'expired') {
    message.text = 'Tu sesión ha expirado. Por favor, inicia sesión nuevamente.'
    message.type = 'error'
    window.history.replaceState({}, document.title, '/login')
  }

  // AUTO-LOGIN DESDE DOMINIO CENTRAL (con token seguro)
  if (centralLoginToken) {
    try {
      loading.value = true
      message.text = 'Ingresando a tu cuenta...'
      message.type = 'info'
      
      // Limpiar URL inmediatamente para evitar reuso del token
      window.history.replaceState({}, document.title, '/login')
      
      // Limpiar cualquier estado de auth previo para evitar conflictos
      localStorage.removeItem('authToken')
      localStorage.removeItem('user')
      localStorage.removeItem('sanctum_token')
      delete axios.defaults.headers.common['Authorization']
      
      // Crear instancia de axios sin interceptores
      const cleanAxios = axios.create({
        baseURL: window.location.origin,
        headers: {
          'Content-Type': 'application/json'
        }
      })
      
      // Obtener sesión usando el token (con retry)
      let response
      try {
        response = await cleanAxios.get(`/api/central/login-session?token=${centralLoginToken}`)
      } catch (firstError) {
        // Retry una vez en caso de timeout o error de red
        if (firstError.code === 'ECONNABORTED' || !firstError.response) {
          await new Promise(r => setTimeout(r, 1000))
          response = await cleanAxios.get(`/api/central/login-session?token=${centralLoginToken}`)
        } else {
          throw firstError
        }
      }
      
      if (response.data.success && response.data.data) {
        const { token, user } = response.data.data
        
        // Guardar token Sanctum y datos de usuario
        localStorage.setItem('authToken', token)
        localStorage.setItem('user', JSON.stringify(user))
        localStorage.setItem('loginTimestamp', Date.now().toString())
        
        // Configurar token en axios global
        axios.defaults.headers.common['Authorization'] = `Bearer ${token}`
        
        message.text = 'Inicio de sesión exitoso. Redirigiendo...'
        message.type = 'success'
        
        // Verificar si necesita seleccionar plan
        if (response.data.needs_plan_selection && response.data.tenant) {
          const tenant = response.data.tenant
          const subdomain = tenant.id
          const companyName = tenant.business_name || ''
          
          const isLocalhost = window.location.hostname.includes('localhost') || window.location.hostname === '127.0.0.1'
          const baseUrl = isLocalhost ? `http://localhost:${window.location.port || 3000}` : 'https://105pos.pro'
          
          const params = new URLSearchParams()
          if (subdomain) params.append('tenant_id', subdomain)
          if (subdomain) params.append('subdomain', subdomain)
          if (companyName) params.append('company', companyName)
          params.append('auth_token', encodeURIComponent(token))
          
          message.text = 'Necesitas seleccionar un plan. Redirigiendo...'
          message.type = 'info'
          
          setTimeout(() => {
            window.location.href = `${baseUrl}/select-plan?${params.toString()}`
          }, 800)
          return
        }
        
        // Verificar si es super admin
        if (user?.is_super_admin || user?.role?.name === 'superadmin') {
          window.location.href = '/pos'
          return
        }
        
        // Navegar directamente con window.location para full reload con auth
        window.location.href = '/pos'
        return
      } else {
        message.text = response.data.message || 'Error al iniciar sesión.'
        message.type = 'error'
      }
    } catch (error) {
      message.text = error.response?.data?.message || 'Error al iniciar sesión. Por favor, intenta nuevamente.'
      message.type = 'error'
    } finally {
      loading.value = false
    }
  }

  if (googleLoginToken) {
    try {
      loading.value = true
      message.text = 'Iniciando sesión con Google...'
      message.type = 'info'
      
      // Limpiar URL inmediatamente para evitar reuso del token
      window.history.replaceState({}, document.title, '/login')
      
      // Limpiar auth previa
      localStorage.removeItem('authToken')
      localStorage.removeItem('user')
      localStorage.removeItem('sanctum_token')
      delete axios.defaults.headers.common['Authorization']
      
      const cleanAxios = axios.create({
        baseURL: window.location.origin,
        headers: { 'Content-Type': 'application/json' }
      })
      
      const response = await cleanAxios.get(`/api/auth/google/login-session?token=${googleLoginToken}`)
      
      if (response.data.success && response.data.data) {
        const { token, user } = response.data.data
        
        localStorage.setItem('authToken', token)
        localStorage.setItem('user', JSON.stringify(user))
        localStorage.setItem('loginTimestamp', Date.now().toString())
        localStorage.setItem('google_login', 'true')
        
        axios.defaults.headers.common['Authorization'] = `Bearer ${token}`
        
        message.text = 'Inicio de sesión exitoso. Redirigiendo...'
        message.type = 'success'
        
        if (response.data.needs_plan_selection && response.data.tenant) {
          const tenant = response.data.tenant
          const subdomain = tenant.id
          const companyName = tenant.business_name || ''
          
          const isLocalhost = window.location.hostname.includes('localhost') || window.location.hostname === '127.0.0.1'
          const baseUrl = isLocalhost ? `http://localhost:${window.location.port || 3000}` : 'https://105pos.pro'
          
          const params = new URLSearchParams()
          if (subdomain) params.append('tenant_id', subdomain)
          if (subdomain) params.append('subdomain', subdomain)
          if (companyName) params.append('company', companyName)
          params.append('auth_token', encodeURIComponent(token))
          
          message.text = 'Necesitas seleccionar un plan. Redirigiendo...'
          message.type = 'info'
          
          setTimeout(() => {
            window.location.href = `${baseUrl}/select-plan?${params.toString()}`
          }, 800)
          return
        }
        
        // Navegar con window.location para full reload con auth correcta
        window.location.href = '/pos'
      } else {
        message.text = response.data.message || 'Error al iniciar sesión con Google.'
        message.type = 'error'
      }
    } catch (error) {
      message.text = error.response?.data?.message || 'Error al iniciar sesión con Google. Intenta nuevamente.'
      message.type = 'error'
    } finally {
      loading.value = false
    }
  }
})

// Ambiente de desarrollo
const isDevelopment = computed(() => {
  return process.env.NODE_ENV === 'development' || window.location.hostname === 'localhost'
})

// Modal: Cuenta no encontrada
const closeNotFoundModal = () => {
  showNotFoundModal.value = false
}
const goToRegister = () => {
  showNotFoundModal.value = false
  router.push('/register')
}

// Limpiar mensajes
const clearMessages = () => {
  message.text = ''
  message.type = ''
  errors.email = ''
  errors.password = ''
}

// Validar formulario
const validateForm = () => {
  // SEGURIDAD: Verificar que credentials existe
  if (!credentials) {
    console.error('ERROR CRÍTICO: credentials es undefined')
    return false
  }
  
  clearMessages()
  let isValid = true

  if (!credentials.email || !credentials.email.trim()) {
    errors.email = 'El correo es requerido'
    isValid = false
  } else if (!/^[^\s@]+@[^\s@]+/.test(credentials.email.trim())) {
    // Validación flexible: acepta admin@superadmin, admin@admin, etc.
    errors.email = 'Ingrese un correo válido'
    isValid = false
  }

  if (!credentials.password || !credentials.password.trim()) {
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
    // DETECTAR SI ESTAMOS EN DOMINIO PRINCIPAL (login centralizado)
    const hostname = window.location.hostname
    const isMainDomain = ['localhost', '127.0.0.1', '105pos.pro', 'www.105pos.pro'].includes(hostname)
    
    if (isMainDomain) {
      // ============================================
      // LOGIN CENTRALIZADO (Smart Login)
      // ============================================
      
      const response = await axios.post('/api/central/login', {
        email: credentials.email.trim(),
        password: credentials.password
      })

      // Manejar respuesta del login centralizado
      if (response.data && response.data.success) {
        message.text = 'Accediendo a tu cuenta...'
        message.type = 'success'
        
        // � SUPER ADMIN: Guardar token y usuario ANTES de redirigir
        if (response.data.data.is_super_admin) {
          localStorage.setItem('authToken', response.data.data.token)
          localStorage.setItem('user', JSON.stringify(response.data.data.user))
          axios.defaults.headers.common['Authorization'] = `Bearer ${response.data.data.token}`
          
          setTimeout(() => {
            window.location.href = response.data.data.redirect_url
          }, 500)
          return
        }
        
        // CRÍTICO: Limpiar tokens viejos ANTES de redirigir (para tenants)
        // Los tokens de Sanctum NO funcionan entre dominios
        localStorage.removeItem('authToken')
        localStorage.removeItem('sanctum_token')
        localStorage.removeItem('user')
        
        // La URL ya incluye el token temporal para auto-login
        // redirect_url ahora es: https://tenant.105pos.pro/login?central_login_token=XXX
        setTimeout(() => {
          window.location.href = response.data.data.redirect_url
        }, 500)
        
        return
      }
    }
    
    // ============================================
    // LOGIN TRADICIONAL (en subdominio)
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
        // IMPORTANTE: Agregar onboarding_completed al guardar config pendiente
        config.onboarding_completed = true
        await axios.put('/api/tenant/system-settings', config)
        localStorage.removeItem('pending_onboarding_config')
      } catch (error) {
        console.error('Error aplicando configuración pendiente:', error)
      }
    }

    // Verificar rol y redireccionar
    const user = response.data?.user || response.user // Compatibilidad con ambas estructuras

    // VERIFICAR SI HAY SUBDOMAIN PENDIENTE (desde registro trial)
    const registrationSuccess = localStorage.getItem('registration_success')
    if (registrationSuccess) {
      const data = JSON.parse(registrationSuccess)
      if (data.subdomain) {
        localStorage.removeItem('registration_success')
        
        // FIX: En local (puerto 3000 o 5173), NO redirigir a subdominio
        const isLocal = window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1'
        
        // Esperar un momento para mostrar el mensaje
        setTimeout(() => {
          if (isLocal) {
            // En local, quedarse en localhost sin subdominios
            router.push('/pos')
          } else {
            // En producción, usar el subdominio del tenant
            window.location.href = data.subdomain + '/pos'
          }
        }, 1000)
        return
      }
    }

    // Esperar un momento para mostrar el mensaje de éxito
    setTimeout(async () => {
      // Si es super admin, ir directo al panel god mode
      if (user?.is_super_admin || user?.role?.name === 'superadmin') {
        router.push('/admin/god-mode')
        return
      }

      // � VALIDAR SI EL TENANT TIENE UN PLAN VÁLIDO
      try {
        const tenantResponse = await axios.get('/api/tenant/info')
        const tenant = tenantResponse.data?.tenant || tenantResponse.data
        
        // Verificar si el plan es válido (no pendiente ni trial expirado)
        const validPlans = ['basic', 'premium', 'enterprise', 'free_trial']
        const planStatus = tenant?.subscription_status || 'pending'
        const planType = tenant?.plan_type || 'pending'
        
        // Si el plan es pendiente, redirigir a select-plan
        if (planStatus === 'pending' || planType === 'pending' || !validPlans.includes(planType)) {
          // Obtener subdomain y company name para el redirect
          const subdomain = tenant?.id || tenant?.subdomain || ''
          const companyName = tenant?.company_name || tenant?.name || ''
          
          // Redirigir al dominio central para seleccionar plan
          const isLocalhost = window.location.hostname.includes('localhost') || window.location.hostname === '127.0.0.1'
          const baseUrl = isLocalhost ? `http://localhost:${window.location.port || 3000}` : 'https://105pos.pro'
          
          const params = new URLSearchParams()
          if (subdomain) params.append('tenant_id', subdomain)
          if (subdomain) params.append('subdomain', subdomain)
          if (companyName) params.append('company', companyName)
          
          // CRÍTICO: Pasar el token para que PlanSelection pueda usarlo después
          const currentToken = localStorage.getItem('authToken')
          if (currentToken) {
            params.append('auth_token', encodeURIComponent(currentToken))
          }
          
          window.location.href = `${baseUrl}/select-plan?${params.toString()}`
          return
        }
      } catch (error) {
        console.error('Error verificando plan del tenant:', error)
        // Continuar de todos modos
      }

      // �FIX: Cargar systemSettings ANTES de redireccionar
      // Esto evita que el router guard redirija a welcome/onboarding incorrectamente
      try {
        await appStore.loadSystemSettings()
      } catch (error) {
        // Continuar de todos modos
      }

      // REDIRECCIÓN INTELIGENTE BASADA EN PERMISOS
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

    // Detectar cuenta/IP bloqueada (423 Locked o 429 Too Many Requests)
    if (error.response?.status === 423 || error.response?.status === 429 || error.response?.data?.blocked) {
      const data = error.response?.data || {}
      blockedState.value = {
        type: data.block_type || 'account',
        message: data.message || 'Tu cuenta ha sido bloqueada por seguridad.',
        supportUrl: data.support_url || 'https://wa.me/573217355070?text=' + encodeURIComponent('Hola, mi cuenta fue bloqueada en 105POS, necesito ayuda para recuperar el acceso.')
      }
      loading.value = false
      return
    }

    // Detectar error de "usuario no encontrado" → mostrar modal de conversión
    const responseMsg = (error.response?.data?.message || error.message || '').toLowerCase()
    const responseEmailError = (error.response?.data?.errors?.email?.[0] || '').toLowerCase()
    const notFoundKeywords = ['no encontramos', 'no está registrado', 'not found', 'no existe', 'no encontrado', 'not registered', 'account not found', 'user not found', 'no se encontró']
    const isUserNotFound = error.response?.status === 404 ||
      notFoundKeywords.some(kw => responseMsg.includes(kw) || responseEmailError.includes(kw))

    if (isUserNotFound) {
      showNotFoundModal.value = true
    } else if (error.response?.status === 422) {
      // Manejar respuestas 422 (Unprocessable Entity) del backend
      const errorData = error.response.data

      // Mostrar errores específicos por campo
      if (errorData.errors) {
        if (errorData.errors.email) {
          errors.email = errorData.errors.email[0]
        }
        if (errorData.errors.password) {
          errors.password = errorData.errors.password[0]
        }
      }

      // Mostrar mensaje general
      message.text = errorData.message || 'Credenciales incorrectas'
      message.type = 'error'
    } else if (error.errors) {
      // Errores de validación del servidor (formato antiguo)
      if (error.errors.email) {
        errors.email = error.errors.email[0]
      }
      if (error.errors.password) {
        errors.password = error.errors.password[0]
      }
      message.text = 'Por favor verifica los datos ingresados'
      message.type = 'error'
    } else {
      // Error general
      message.text = error.response?.data?.message || error.message || 'Error al iniciar sesión. Intenta de nuevo.'
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
  // ANTES de redirigir al POS, verificar si tiene plan válido
  const checkPlanAndRedirect = async () => {
    try {
      const response = await axios.get('/api/tenant/info')
      const tenant = response.data?.tenant || response.data
      
      const validPlans = ['basic', 'premium', 'enterprise', 'free_trial']
      const planType = tenant?.plan_type || tenant?.plan || 'pending'
      const subscriptionStatus = tenant?.subscription_status || 'pending'
      
      // Si no tiene plan válido, redirigir a select-plan
      if (subscriptionStatus === 'pending' || planType === 'pending' || !validPlans.includes(planType)) {
        const subdomain = tenant?.id || tenant?.subdomain || ''
        const companyName = tenant?.company_name || tenant?.name || tenant?.business_name || ''
        
        const isLocalhost = window.location.hostname.includes('localhost') || window.location.hostname === '127.0.0.1'
        const baseUrl = isLocalhost ? `http://localhost:${window.location.port || 3000}` : 'https://105pos.pro'
        
        const params = new URLSearchParams()
        if (subdomain) params.append('tenant_id', subdomain)
        if (subdomain) params.append('subdomain', subdomain)
        if (companyName) params.append('company', companyName)
        
        // CRÍTICO: Pasar el token para que PlanSelection pueda usarlo después
        const currentToken = localStorage.getItem('authToken')
        if (currentToken) {
          params.append('auth_token', encodeURIComponent(currentToken))
        }
        
        window.location.href = `${baseUrl}/select-plan?${params.toString()}`
        return
      }
      
      // Si tiene plan válido, ir al POS
      router.push('/pos')
    } catch (error) {
      // CUALQUIER ERROR = limpiar tokens y quedarse en login
      // NO redirigir al POS bajo ninguna circunstancia si hay error
      localStorage.removeItem('authToken')
      localStorage.removeItem('user')
      localStorage.removeItem('sanctum_token')
      delete axios.defaults.headers.common['Authorization']
      // Quedarse en login - el usuario deberá autenticarse nuevamente
    }
  }
  
  checkPlanAndRedirect()
}
</script>
<template>
  <div class="font-['Inter',sans-serif] bg-[#f4f5f7] text-slate-900 min-h-screen">
    
    <!-- LADO IZQUIERDO: Panel de Marca Corporativo Premium (40%) -->
    <div class="hidden lg:flex lg:w-[40%] lg:fixed lg:left-0 lg:top-0 h-full relative overflow-hidden bg-[#0a0b0f] flex-col p-12 text-white shadow-2xl shadow-black/50 z-10" style="min-height: 100vh;">
      <!-- Imagen de fondo corporativa -->
      <div class="absolute inset-0">
        <img src="/login.png" alt="POS Empresarial" class="w-full h-full object-cover object-center" />
      </div>
      <!-- Overlay oscuro con gradiente - deja ver la imagen -->
      <div class="absolute inset-0 bg-gradient-to-b from-[#0a0b0f]/[0.82] via-[#0d0e14]/[0.75] to-[#060710]/[0.88]"></div>
      <!-- Reflejo sutil de luz -->
      <div class="absolute inset-0 bg-gradient-to-br from-white/[0.04] via-transparent to-transparent"></div>
      <!-- Red de datos global sutil -->
      <div class="absolute inset-0 opacity-[0.04]" style="background-image: radial-gradient(circle at 25% 25%, rgba(0,180,160,0.4) 1px, transparent 1px), radial-gradient(circle at 75% 75%, rgba(0,180,160,0.3) 1px, transparent 1px), radial-gradient(circle at 50% 50%, rgba(0,180,160,0.2) 0.5px, transparent 0.5px); background-size: 60px 60px, 80px 80px, 40px 40px;"></div>
      
      <!-- Top: Logo + Botón Radio -->
      <div class="relative z-10 flex items-center justify-between mb-8 animate-fade-in-up">
        <div>
          <h1 class="text-2xl font-extrabold tracking-tight text-white font-['Inter',sans-serif]" style="text-shadow: 0 1px 8px rgba(0,0,0,0.5);">105 POS Pro</h1>
          <p class="text-[10px] text-[#00b894] font-bold uppercase tracking-[0.25em] mt-1.5" style="text-shadow: 0 1px 4px rgba(0,0,0,0.4);">Plataforma Empresarial</p>
        </div>

        <!-- Botón Radio -->
        <button
          @click="radioOpen = true"
          title="Abrir Radio 105"
          class="flex items-center gap-3 px-4 py-2 rounded-xl bg-white/[0.06] hover:bg-white/[0.10] backdrop-blur-md border border-white/[0.08] hover:border-white/[0.15] text-white hover:-translate-y-0.5 hover:shadow-lg hover:shadow-black/30 transition-all duration-300 group"
        >
          <!-- Ícono nota musical -->
          <svg class="w-[17px] h-[17px] flex-shrink-0 text-white/60 group-hover:text-[#00b894] transition-colors duration-300" fill="currentColor" viewBox="0 0 24 24">
            <path d="M9 3v10.55A4 4 0 1 0 11 17V7h4V3H9Z"/>
          </svg>
          <!-- Texto + punto live -->
          <span class="flex items-center gap-2">
            <span class="text-[13px] font-medium text-white/80 font-['Inter',sans-serif]">Radio</span>
            <span class="w-1.5 h-1.5 rounded-full bg-[#00b894] animate-pulse flex-shrink-0"></span>
          </span>
        </button>
      </div>

      <!-- Centro: Mensaje Corporativo -->
      <div class="relative z-10 flex-1 flex flex-col justify-center max-w-md pr-6 animate-fade-in-up style-delay-150">
        <h2 class="text-[36px] font-extrabold text-white mb-6 tracking-[-0.02em] leading-[1.12] font-['Inter',sans-serif]" style="text-shadow: 0 2px 12px rgba(0,0,0,0.6), 0 1px 3px rgba(0,0,0,0.4);">
          Gestión inteligente para<br/><span class="text-[#00b894]">negocios en expansión.</span>
        </h2>
        <p class="text-white/90 text-[15px] font-normal leading-[1.75] tracking-wide" style="text-shadow: 0 1px 6px rgba(0,0,0,0.5);">
          Sistema integral de Punto de Venta diseñado para la alta dirección. Optimización, control unificado y escalabilidad para operaciones comerciales de alto volumen.
        </p>
      </div>

      <!-- Bottom: Testimonio anclado en la base -->
      <div class="relative z-10 pt-8 mt-auto border-t border-white/[0.06] animate-fade-in-up style-delay-300">
        <p class="text-slate-200/90 text-[15px] mb-6 leading-relaxed font-normal italic drop-shadow-[0_1px_2px_rgba(0,0,0,0.3)]">"Una herramienta sólida que nos permite controlar las ventas de todas nuestras sucursales con total precisión y sin interrupciones."</p>
        <div class="flex items-center gap-4">
           <div class="w-11 h-11 rounded-full bg-gradient-to-br from-slate-700 to-slate-800 flex items-center justify-center text-sm font-bold text-white uppercase border border-white/[0.08] shadow-lg shadow-black/30">
             MJ
           </div>
           <div>
             <p class="text-sm font-semibold text-white tracking-wide" style="text-shadow: 0 1px 4px rgba(0,0,0,0.4);">María José G.B.</p>
             <p class="text-xs text-[#00b894] font-semibold mt-0.5 tracking-wider" style="text-shadow: 0 1px 3px rgba(0,0,0,0.3);">Directora de Operaciones</p>
           </div>
        </div>
      </div>
    </div>

    <!-- LADO DERECHO: Formulario (60%) -->
    <div class="w-full lg:ml-[40%] lg:w-[60%] bg-[#f4f5f7] relative min-h-screen" style="display: flex; flex-direction: column;">
      <!-- Superficie de porcelana mate -->
      <div class="absolute inset-0 bg-gradient-to-b from-[#f0f1f3] via-[#f4f5f7] to-[#ecedf0] pointer-events-none"></div>
      <!-- Data-mesh grid sofisticada -->
      <div class="absolute inset-0 pointer-events-none" style="background-image: radial-gradient(circle, #c8cdd3 0.5px, transparent 0.5px), linear-gradient(to right, #e4e7ec 0.5px, transparent 0.5px), linear-gradient(to bottom, #e4e7ec 0.5px, transparent 0.5px); background-size: 32px 32px, 64px 64px, 64px 64px; opacity: 0.35; mask-image: radial-gradient(ellipse 70% 60% at 50% 0%, #000 50%, transparent 100%);"></div>

      <!-- MOBILE STICKY HEADER: Logo + Radio (solo en móviles) -->
      <div class="lg:hidden sticky top-0 z-20 px-5 py-3.5 flex items-center justify-between bg-white/95 backdrop-blur-sm border-b border-slate-100/60 shadow-sm">
        <div>
          <h1 class="text-[19px] font-extrabold text-slate-900 tracking-tight font-['Inter',sans-serif] leading-tight">105 POS Pro</h1>
          <p class="text-[9px] text-[#00b894] font-bold uppercase tracking-[0.25em] mt-0.5">Plataforma Empresarial</p>
        </div>
        <!-- Botón Radio (compacto para móvil) -->
        <button
          @click="radioOpen = true"
          title="Abrir Radio 105"
          class="flex items-center gap-2 px-3 py-2 rounded-xl bg-[#0f172a] hover:bg-[#1e293b] border border-slate-800 text-white transition-all duration-300 group"
        >
          <svg class="w-4 h-4 flex-shrink-0 text-white/80 group-hover:text-[#00b894] transition-colors duration-300" fill="currentColor" viewBox="0 0 24 24">
            <path d="M9 3v10.55A4 4 0 1 0 11 17V7h4V3H9Z"/>
          </svg>
          <span class="flex items-center gap-1.5">
            <span class="text-xs font-medium">Radio</span>
            <span class="w-1.5 h-1.5 rounded-full bg-[#00b894] animate-pulse flex-shrink-0"></span>
          </span>
        </button>
      </div>

      <!-- Área del formulario (centrada verticalmente en desktop) -->
      <div class="py-8" style="flex: 1; display: flex; flex-direction: column; justify-content: center;">
        <!-- Formulario expandido (max-w-xl igual que Register) -->
        <div class="w-full max-w-xl mx-auto px-6 sm:px-10 relative z-10">

        <!-- Estado: Cuenta Bloqueada -->
        <div v-if="blockedState" class="animate-fade-in-up">
          <div class="bg-white rounded-2xl border border-rose-200 shadow-lg p-8 text-center">
            <div class="w-16 h-16 mx-auto mb-5 rounded-full bg-rose-50 flex items-center justify-center">
              <svg class="w-8 h-8 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
              </svg>
            </div>
            <h3 class="text-xl font-bold text-slate-900 mb-3">Cuenta bloqueada</h3>
            <p class="text-slate-600 text-sm leading-relaxed mb-6">
              {{ blockedState.message }}
            </p>
            <p class="text-slate-500 text-sm mb-6">
              Para recuperar el acceso, contacta con el administrador.
            </p>
            <div class="flex flex-col gap-3">
              <a 
                :href="blockedState.supportUrl" 
                target="_blank" 
                rel="noopener noreferrer"
                class="w-full flex items-center justify-center gap-2.5 py-3.5 px-6 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold rounded-xl transition-all duration-200 shadow-sm"
              >
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                  <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/>
                  <path d="M12 2C6.477 2 2 6.477 2 12c0 1.89.525 3.66 1.438 5.168L2 22l4.832-1.438A9.955 9.955 0 0012 22c5.523 0 10-4.477 10-10S17.523 2 12 2zm0 18c-1.69 0-3.27-.464-4.622-1.27l-.332-.197-2.87.852.852-2.87-.197-.332A7.96 7.96 0 014 12c0-4.411 3.589-8 8-8s8 3.589 8 8-3.589 8-8 8z"/>
                </svg>
                Contactar soporte
              </a>
              <button 
                @click="blockedState = null"
                class="w-full py-3 px-6 text-slate-500 hover:text-slate-700 font-medium text-sm rounded-xl hover:bg-slate-50 transition-all duration-200"
              >
                Volver al inicio de sesión
              </button>
            </div>
          </div>
        </div>

        <!-- Estado: Normal (formulario de login) -->
        <template v-else>
        <div class="mb-7 text-center lg:text-left animate-fade-in-up">
          <h2 class="text-[32px] font-extrabold text-slate-900 mb-2 tracking-[-0.02em] font-['Inter',sans-serif]">Accede a tu panel</h2>
          <p class="text-slate-500 text-[15px] font-normal tracking-wide">Ingresa tus credenciales corporativas para continuar.</p>
        </div>

        <!-- Alertas -->
        <div v-if="message.text" 
             :class="message.type === 'error' ? 'bg-rose-50 text-rose-700 border-rose-200' : 'bg-[#00b894]/10 text-[#00b894] border-[#00b894]/20'" 
             class="mb-6 p-4 rounded-2xl border text-[14px] font-medium flex items-start gap-3 shadow-sm animate-fade-in-up">
          <span class="leading-relaxed">{{ message.text }}</span>
        </div>

        <!-- Botón Google Corporativo -->
        <button 
           @click="loginWithGoogle" 
           :disabled="isGoogleLoading"
           class="w-full flex items-center justify-center gap-3 py-4 px-6 bg-white border border-slate-200/80 hover:border-slate-300 rounded-2xl text-slate-700 font-semibold transition-all duration-300 hover:bg-white hover:shadow-lg hover:shadow-slate-200/60 focus:outline-none disabled:opacity-50 disabled:cursor-not-allowed animate-fade-in-up style-delay-75 group"
        >
          <img v-if="!isGoogleLoading" src="https://www.svgrepo.com/show/475656/google-color.svg" alt="Google" class="w-5 h-5 transition-transform group-hover:scale-110" />
          <span class="text-[15px]">{{ isGoogleLoading ? 'Conectando...' : 'Continuar con Google' }}</span>
        </button>

        <div class="relative mb-8 mt-8 animate-fade-in-up style-delay-150">
          <div class="absolute inset-0 flex items-center"><div class="w-full border-t border-slate-200"></div></div>
          <div class="relative flex justify-center"><span class="px-4 bg-[#f4f5f7] text-sm text-slate-400 font-medium tracking-wide">o ingresa con tu correo</span></div>
        </div>

        <form @submit.prevent="handleLogin" class="space-y-5">
          <!-- Input Correo -->
          <div class="space-y-2 animate-fade-in-up style-delay-225">
            <label for="email" class="text-[13px] font-semibold text-slate-600 uppercase tracking-wider">Correo Electrónico</label>
            <div class="relative group">
              <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                <svg class="w-5 h-5 text-slate-300 group-focus-within:text-slate-500 transition-colors duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
              </div>
              <input 
                id="email"
                v-model="credentials.email" 
                type="email" 
                class="w-full h-14 pl-12 pr-4 bg-white border border-slate-200/80 rounded-2xl focus:bg-white focus:border-slate-400 focus:ring-4 focus:ring-slate-900/5 focus:shadow-lg focus:shadow-slate-200/50 transition-all duration-300 outline-none text-slate-900 placeholder-slate-400 font-medium"
                :class="{'border-rose-400 focus:border-rose-400 focus:ring-rose-400/20 bg-rose-50/50': errors.email}"
                placeholder="tu@empresa.com"
              >
            </div>
            <p v-if="errors.email" class="text-[13px] font-medium text-rose-500 mt-1 pl-1">{{ errors.email }}</p>
          </div>

          <!-- Input Contraseña -->
          <div class="space-y-2 animate-fade-in-up style-delay-225">
            <label for="password" class="text-[13px] font-semibold text-slate-600 uppercase tracking-wider">Contraseña</label>
            <div class="relative group security-halo-wrapper">
              <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                <svg class="w-5 h-5 text-slate-300 group-focus-within:text-slate-500 transition-colors duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                </svg>
              </div>
              <input 
                id="password"
                v-model="credentials.password" 
                :type="showPassword ? 'text' : 'password'" 
                class="w-full h-14 pl-12 pr-14 bg-white border border-slate-200/80 rounded-2xl focus:bg-white focus:border-cyan-400/60 focus:ring-4 focus:ring-cyan-400/10 focus:shadow-[0_0_20px_rgba(0,200,180,0.08)] transition-all duration-300 outline-none text-slate-900 placeholder-slate-400 font-medium"
                :class="{'border-rose-400 focus:border-rose-400 focus:ring-rose-400/20 bg-rose-50/50': errors.password}"
                placeholder="••••••••"
              >
              <button 
                type="button" 
                @click="showPassword = !showPassword" 
                class="absolute inset-y-0 right-0 pr-4 flex items-center text-slate-400 hover:text-slate-600 transition-colors"
                aria-label="Toggle password visibility"
              >
                 <svg v-if="!showPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                   <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                   <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                 </svg>
                 <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                   <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88" />
                 </svg>
              </button>
            </div>
            <p v-if="errors.password" class="text-[13px] font-medium text-rose-500 mt-1 pl-1">{{ errors.password }}</p>
          </div>

          <!-- Controles Extra -->
          <div class="flex items-center justify-between pt-1 animate-fade-in-up style-delay-300">
            <div class="flex items-center">
              <input id="remember" type="checkbox" v-model="credentials.remember" class="w-4 h-4 rounded border-slate-300 text-[#00b894] focus:ring-[#00b894] focus:ring-offset-0 transition-colors cursor-pointer bg-white">
              <label for="remember" class="ml-2.5 block text-[13px] font-semibold text-slate-600 cursor-pointer select-none">Mantener sesión</label>
            </div>
            <router-link to="/forgot-password" class="text-[13px] font-bold text-[#00b894] hover:text-[#009d80] transition-colors duration-300">
              ¿Olvidó su contraseña?
            </router-link>
          </div>

          <!-- Submit Botón Principal -->
          <div class="pt-2 animate-fade-in-up style-delay-300">
            <button 
              type="submit" 
              :disabled="loading"
              class="btn-security-glow group w-full h-14 bg-gradient-to-r from-[#0f172a] via-[#1e293b] to-[#0f172a] hover:from-[#1e293b] hover:via-[#334155] hover:to-[#1e293b] text-white font-bold rounded-2xl shadow-lg shadow-slate-900/30 hover:shadow-xl hover:shadow-slate-900/40 transition-all duration-300 flex items-center justify-center gap-2 disabled:opacity-75 disabled:cursor-not-allowed"
            >
              <span>{{ loading ? 'Iniciando Sesión...' : 'Iniciar Sesión' }}</span>
              <svg v-if="!loading" class="w-5 h-5 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
            </button>
          </div>
        </form>
        
        <!-- Enlace de registro -->
        <div class="mt-10 text-center flex flex-col gap-1 animate-fade-in-up style-delay-[400ms]">
          <p class="text-[14px] font-semibold text-slate-500">
            ¿Nuevo en 105 POS? 
          </p>
          <router-link to="/register" class="text-[15px] font-bold text-[#00b894] hover:text-[#009d80] transition-colors duration-300 relative inline-block mx-auto group">
             Crea tu cuenta corporativa
             <span class="absolute -bottom-0.5 left-0 w-0 h-0.5 bg-[#00b894] transition-all duration-300 group-hover:w-full"></span>
          </router-link>
        </div>

        </template>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal Radio -->
  <RadioPlayerModal :is-open="radioOpen" @close="radioOpen = false" />

  <!-- ================================================================
       MODAL: Cuenta no encontrada (Diseño Enterprise - Sin futurismo)
       ================================================================ -->
  <Teleport to="body">
    <Transition name="modal-enterprise">
      <div
        v-if="showNotFoundModal"
        class="fixed inset-0 z-[9999] flex items-center justify-center p-4 bg-slate-800/35 backdrop-blur-[2px]"
        @click.self="closeNotFoundModal"
      >
        <div class="modal-enterprise-content bg-white border border-slate-200/80 rounded-xl shadow-2xl shadow-slate-800/8 w-full max-w-2xl">

          <!-- Cuerpo del modal -->
          <div class="px-10 pt-10 pb-7">
            <h3 class="text-[28px] font-extrabold text-slate-900 tracking-[-0.02em] leading-snug mb-4 font-['Inter',sans-serif]">
              Cuenta no encontrada
            </h3>
            <p class="text-[16px] text-slate-500 leading-[1.75] font-['Inter',sans-serif]">
              No encontramos ninguna cuenta asociada a ese correo en 105 POS.<br/>
              ¿Quieres crear una cuenta y empezar a gestionar tu negocio hoy?
            </p>
          </div>

          <!-- Divisor -->
          <div class="border-t border-slate-100 mx-10"></div>

          <!-- Botones lado a lado -->
          <div class="px-10 py-7 flex flex-col sm:flex-row justify-end gap-3">
            <!-- Botón secundario: Intentar de nuevo -->
            <button
              @click="closeNotFoundModal"
              class="px-7 py-3 bg-white border border-slate-200 hover:bg-slate-50 hover:border-slate-300 text-slate-600 font-semibold text-[15px] rounded-xl transition-all duration-300 focus:outline-none font-['Inter',sans-serif]"
            >
              Intentar de nuevo
            </button>

            <!-- Botón principal: Registrar ahora -->
            <button
              @click="goToRegister"
              class="px-7 py-3 bg-[#00b894] hover:bg-[#009d80] text-white font-bold text-[15px] rounded-xl transition-all duration-300 hover:shadow-lg hover:shadow-[#00b894]/20 focus:outline-none font-['Inter',sans-serif]"
            >
              Registrar ahora →
            </button>
          </div>

        </div>
      </div>
    </Transition>
  </Teleport>

</template>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');

@keyframes fadeInUp {
  from {
    opacity: 0;
    transform: translateY(16px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

@keyframes securityPulse {
  0%, 100% { box-shadow: 0 0 0 0 rgba(0, 200, 180, 0); }
  50% { box-shadow: 0 0 0 4px rgba(0, 200, 180, 0.06); }
}

.animate-fade-in-up {
  animation: fadeInUp 0.7s cubic-bezier(0.16, 1, 0.3, 1) forwards;
  opacity: 0;
}

/* Security Halo - sutil glow en password fields */
.security-halo-wrapper {
  position: relative;
}
.security-halo-wrapper::after {
  content: '';
  position: absolute;
  inset: -2px;
  border-radius: 1rem;
  pointer-events: none;
  opacity: 0;
  transition: opacity 0.4s ease;
}
.security-halo-wrapper:focus-within::after {
  animation: securityPulse 2.5s ease-in-out infinite;
  opacity: 1;
}

/* Button security glow */
.btn-security-glow {
  position: relative;
}
.btn-security-glow::after {
  content: '';
  position: absolute;
  inset: -1px;
  border-radius: 1rem;
  background: linear-gradient(135deg, rgba(0, 200, 180, 0.1), rgba(15, 23, 42, 0.05));
  opacity: 0;
  transition: opacity 0.3s ease;
  pointer-events: none;
  z-index: -1;
}
.btn-security-glow:hover::after {
  opacity: 1;
}

/* Staggered Delays para coreografía */
.style-delay-75 { animation-delay: 75ms; }
.style-delay-150 { animation-delay: 150ms; }
.style-delay-225 { animation-delay: 225ms; }
.style-delay-300 { animation-delay: 300ms; }
.style-delay-\[400ms\] { animation-delay: 400ms; }

/* Refined scrollbar */
::-webkit-scrollbar { width: 4px; }
::-webkit-scrollbar-track { background: transparent; }
::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 2px; }
::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
</style>

<style>
/* Transición del Modal Enterprise */
.modal-enterprise-enter-active,
.modal-enterprise-leave-active {
  transition: opacity 0.2s ease;
}
.modal-enterprise-enter-active .modal-enterprise-content,
.modal-enterprise-leave-active .modal-enterprise-content {
  transition: opacity 0.2s ease, transform 0.2s ease;
}
.modal-enterprise-enter-from,
.modal-enterprise-leave-to {
  opacity: 0;
}
.modal-enterprise-enter-from .modal-enterprise-content,
.modal-enterprise-leave-to .modal-enterprise-content {
  opacity: 0;
  transform: translateY(-10px) scale(0.98);
}
</style>