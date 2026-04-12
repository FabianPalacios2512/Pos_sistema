<template>
  <div class="min-h-screen bg-gradient-to-br from-emerald-50 via-white to-teal-50 flex items-center justify-center p-4">
    <!-- Estado: Verificando pago -->
    <div v-if="isVerifying" class="max-w-md w-full bg-white rounded-2xl shadow-2xl border border-blue-100 p-8 text-center animate-fade-in">
      <div class="w-24 h-24 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-6">
        <svg class="w-12 h-12 text-blue-600 animate-spin" fill="none" viewBox="0 0 24 24">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
      </div>
      <h1 class="text-2xl font-bold text-gray-900 mb-4">Verificando pago...</h1>
      <p class="text-gray-600">Por favor espera mientras confirmamos tu transacción.</p>
    </div>

    <!-- Estado: Pago NO válido / Cancelado -->
    <div v-else-if="paymentFailed" class="max-w-md w-full bg-white rounded-2xl shadow-2xl border border-red-100 p-8 text-center animate-fade-in">
      <div class="w-24 h-24 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-6">
        <svg class="w-12 h-12 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path>
        </svg>
      </div>
      <h1 class="text-2xl font-bold text-gray-900 mb-4">{{ errorTitle }}</h1>
      <p class="text-gray-600 mb-6">{{ errorMessage }}</p>
      <button 
        @click="goToPlans"
        class="w-full bg-gray-900 hover:bg-black text-white font-semibold py-3.5 rounded-xl transition-all duration-200 shadow-lg hover:shadow-xl"
      >
        Volver a intentar
      </button>
    </div>

    <!-- Estado: Pago Exitoso -->
    <div v-else class="max-w-md w-full bg-white rounded-2xl shadow-2xl border border-emerald-100 p-8 text-center animate-fade-in">
      <div class="w-24 h-24 bg-emerald-100 rounded-full flex items-center justify-center mx-auto mb-6 animate-scale-in">
        <svg class="w-12 h-12 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path>
        </svg>
      </div>

      <h1 class="text-3xl font-bold text-gray-900 mb-4">¡Pago Exitoso! </h1>
      
      <p class="text-gray-600 mb-6 text-lg">
        Tu suscripción ha sido activada correctamente.
      </p>

      <div class="bg-emerald-50 border border-emerald-200 rounded-xl p-5 mb-6">
        <p class="text-sm text-emerald-800 space-y-1">
          <span class="block font-semibold text-base mb-2">Plan Activado</span>
          <span class="block"><strong>Plan:</strong> {{ planName }}</span>
          <span class="block" v-if="companyName"><strong>Empresa:</strong> {{ companyName }}</span>
          <span class="block text-emerald-600 font-semibold mt-2">Estado: Activo</span>
        </p>
      </div>

      <p class="text-sm text-gray-500 mb-6">
        Redirigiendo a tu panel en {{ countdown }} segundos...
      </p>

      <button 
        @click="redirectToDashboard"
        class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-semibold py-3.5 rounded-xl transition-all duration-200 shadow-lg hover:shadow-xl"
      >
        Ir a Mi Panel Ahora
      </button>

      <p class="mt-4 text-xs text-gray-400">
        ID de Pago: {{ paymentId || 'Procesando...' }}
      </p>
    </div>
  </div>
</template>

<script setup>
import { onMounted, ref } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import axios from 'axios'

// Crear instancia de axios con la URL base correcta del backend
// Wompi redirige desde su servidor externo, no desde localhost
// Por eso necesitamos especificar explícitamente dónde está el backend
const backendAPI = axios.create({
  baseURL: (() => {
    // Determinar dónde está el backend según dónde estemos
    if (window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1') {
      // En localhost: el backend está en http://localhost:8000
      return 'http://localhost:8000'
    } else if (window.location.hostname.includes('.localhost')) {
      // Subdominios de localhost (ej: tenant.localhost:3000)
      // El backend sigue siendo http://localhost:8000
      return 'http://localhost:8000'
    } else {
      // En producción: el backend está en el mismo dominio (105pos.pro)
      // pero sin subdominio
      return `https://${window.location.hostname}`
    }
  })(),
  timeout: 15000,
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json'
  },
  // IMPORTANTE: Permitir URLs absolutas para subdominios
  allowAbsoluteUrls: true
})

const route = useRoute()
const router = useRouter()

// Estados de UI
const isVerifying = ref(true)
const paymentFailed = ref(false)
const errorTitle = ref('Pago no completado')
const errorMessage = ref('El pago fue cancelado o no se pudo procesar.')

// Datos del pago
const planName = ref('')
const companyName = ref('')
const paymentId = ref('')
const countdown = ref(5)

// Función para ir a selección de planes con mensaje de error
const goToPlans = () => {
  // Obtener datos de la URL
  const tenantId = route.query.tenant_id || route.query.subdomain || ''
  const company = route.query.company || ''
  const subdomain = route.query.subdomain || tenantId || ''
  
  // Obtener datos de localStorage si existen
  let registrationData = {}
  try {
    const savedData = localStorage.getItem('registration_data')
    if (savedData) {
      registrationData = JSON.parse(savedData)
    }
  } catch (e) {
    // Ignorar error
  }
  
  // Construir URL de regreso a select-plan en el dominio CENTRAL
  const params = new URLSearchParams()
  if (tenantId) params.append('tenant_id', tenantId)
  if (subdomain) params.append('subdomain', subdomain)
  if (company || registrationData.company_name) {
    params.append('company', company || registrationData.company_name)
  }
  
  // Siempre redirigir al dominio central (105pos.pro), no al subdominio
  const isLocalhost = window.location.hostname.includes('localhost') || window.location.hostname === '127.0.0.1'
  const baseUrl = isLocalhost ? `http://localhost:${window.location.port || 3000}` : 'https://105pos.pro'
  
  window.location.href = `${baseUrl}/select-plan?${params.toString()}`
}

onMounted(async () => {
  // 1. Obtener parámetros de la URL
  const refPayco = route.query.ref_payco
  const reference = route.query.reference || route.query.id || ''
  const tenantId = route.query.tenant_id || ''
  const plan = route.query.plan || ''
  const isUpgrade = route.query.is_upgrade === 'true'
  
  paymentId.value = refPayco || reference
  
  // VALIDACIÓN CRÍTICA: Si ref_payco es 'undefined' o vacío, el pago NO fue completado
  if (!refPayco || refPayco === 'undefined' || refPayco === 'null') {
    isVerifying.value = false
    paymentFailed.value = true
    errorTitle.value = 'Pago cancelado'
    errorMessage.value = 'El proceso de pago fue cancelado. No se realizó ningún cargo a tu cuenta.'
    return
  }
  
  // VERIFICAR CON EL BACKEND si el pago realmente fue aprobado
  try {
    const verifyResponse = await backendAPI.get('/api/epayco/check-payment-status', {
      params: {
        reference: reference,
        ref_payco: refPayco
      }
    })
    
    const paymentStatus = verifyResponse.data.status
    
    if (paymentStatus !== 'approved' && paymentStatus !== 'Aceptada') {
      // Pago NO fue aprobado
      isVerifying.value = false
      paymentFailed.value = true
      
      if (paymentStatus === 'pending' || paymentStatus === 'Pendiente') {
        errorTitle.value = 'Pago pendiente'
        errorMessage.value = 'Tu pago está siendo procesado. Te notificaremos cuando se confirme.'
      } else if (paymentStatus === 'rejected' || paymentStatus === 'Rechazada') {
        errorTitle.value = 'Pago rechazado'
        errorMessage.value = 'El pago fue rechazado por la entidad financiera. Por favor intenta con otro método de pago.'
      } else {
        errorTitle.value = 'Pago no completado'
        errorMessage.value = `Estado del pago: ${paymentStatus}. Por favor contacta a soporte si crees que es un error.`
      }
      return
    }
    
    // Pago verificado como aprobado - Continuar con activación
    isVerifying.value = false
    
    // Recuperar datos de localStorage si existen
    const pendingPayment = localStorage.getItem('pending_payment')
    let localData = {}
    if (pendingPayment) {
      try {
        localData = JSON.parse(pendingPayment)
      } catch (e) {
        // Ignorar error de parsing
      }
    }
    
    // Usar datos combinados de URL y localStorage
    const finalTenantId = tenantId || localData.tenant_id || ''
    const finalPlan = plan || localData.plan || ''
    const finalReference = reference || localData.reference || ''
  
  // SI TENEMOS DATOS, ACTIVAR O MOSTRAR ÉXITO
  if ((finalReference || refPayco) && finalPlan) {
    
    try {
      // Limpiar localStorage para no reutilizar
      localStorage.removeItem('pending_payment')

      // DETECTAR SI ES UPGRADE O PAGO INICIAL
      if (isUpgrade) {
        // ==================== FLUJO DE UPGRADE ====================
        // Después de pago, Wompi redirige desde su servidor
        // El usuario NO está autenticado, por eso usamos endpoint PÚBLICO
        
        if (!finalTenantId) {
          throw new Error('No se encontró el ID del negocio en la URL')
        }
        
        // Recuperar payment_frequency de localStorage
        const pendingUpgrade = localStorage.getItem('pending_upgrade')
        const upgradeData = pendingUpgrade ? JSON.parse(pendingUpgrade) : {}
        
        // Llamar a endpoint PÚBLICO para procesar upgrade
        const upgradeResponse = await backendAPI.post('/api/process-upgrade', {
          tenant_id: finalTenantId,
          plan: finalPlan,
          reference: finalReference,
          is_upgrade: true
        })
        
        if (upgradeResponse.data.success) {
          planName.value = finalPlan === 'basic' ? 'Plan Basic' : 
                         finalPlan === 'premium' ? 'Plan Premium' : 
                         finalPlan === 'enterprise' ? 'Plan Enterprise' : 'Plan Seleccionado'
          
          // Limpiar datos pendientes
          localStorage.removeItem('pending_upgrade')
          localStorage.removeItem('pending_payment')
        } else {
          throw new Error(upgradeResponse.data.message || 'Error al actualizar el plan')
        }
        
      } else {
        // ==================== FLUJO DE PAGO INICIAL ====================
        // Nuevo tenant, sin autenticación requerida
        
        if (!finalTenantId) {
          throw new Error('No se encontró el ID del negocio')
        }
        
        // Activar plan directamente (ya verificamos que el pago fue aprobado)
        const updateResponse = await backendAPI.post('/api/update-tenant-plan', {
          tenant_id: finalTenantId,
          plan: finalPlan
        })
        
        if (updateResponse.data.success) {
          planName.value = finalPlan === 'basic' ? 'Plan Basic' : 
                         finalPlan === 'premium' ? 'Plan Premium' : 
                         finalPlan === 'enterprise' ? 'Plan Enterprise' : 'Plan Seleccionado'
          
          // Limpiar pago pendiente (por si existe)
          localStorage.removeItem('pending_payment')
        } else {
          throw new Error(updateResponse.data.message || 'Error al activar el plan')
        }
      }
      
    } catch (error) {
      // Mostrar error más específico
      const errorMsg = error.response?.data?.message || error.message || 'Error desconocido'
      paymentFailed.value = true
      errorTitle.value = 'Error al activar plan'
      errorMessage.value = errorMsg + '. Contacta a soporte con el ID: ' + (finalReference || refPayco)
      return
    }
  } else {
    paymentFailed.value = true
    errorTitle.value = 'Datos incompletos'
    errorMessage.value = 'No se encontraron los datos necesarios para activar el plan.'
    return
  }
  
  // Obtener datos de localStorage
  const registrationData = localStorage.getItem('registration_data')
  if (registrationData) {
    const data = JSON.parse(registrationData)
    companyName.value = data.company_name || ''
  }

  // Countdown para redirección automática (solo si no hay error)
  if (!paymentFailed.value) {
    const interval = setInterval(() => {
      countdown.value--
      if (countdown.value <= 0) {
        clearInterval(interval)
        redirectToDashboard()
      }
    }, 1000)
  }
  
  } catch (error) {
    // Error general de verificación
    isVerifying.value = false
    paymentFailed.value = true
    errorTitle.value = 'Error de verificación'
    errorMessage.value = 'No pudimos verificar el estado de tu pago. Contacta a soporte si ya realizaste el pago.'
  }
})

const redirectToDashboard = async () => {
  // Verificar si es renovación
  const isRenewal = route.query.renewal === 'true'
  
  if (isRenewal) {
    // Limpiar datos de pago
    localStorage.removeItem('pending_payment')
    
    // Redirigir al dashboard SIN parámetros de pago en la URL
    // Usar replace() para no dejar params en la URL
    window.location.replace('/dashboard')
    return
  }

  const registrationData = localStorage.getItem('registration_data')
  
  if (registrationData) {
    const data = JSON.parse(registrationData)
    
    // Limpiar datos de pago
    localStorage.removeItem('pending_payment')
    
    // Hacer auto-login en lugar de redirigir al login
    await performAutoLogin()
  } else {
    window.location.href = '/login'
  }
}

// Función para hacer auto-login después del pago exitoso
const performAutoLogin = async () => {
  try {
    // Obtener credenciales guardadas temporalmente
    const registrationData = localStorage.getItem('registration_data')
    if (!registrationData) {
      console.warn('No hay datos de registro - redirigiendo al login manual')
      window.location.href = '/login'
      return
    }

    const data = JSON.parse(registrationData)
    const { email, temp_password, is_google, subdomain } = data

    // Si es registro con Google, redirigir al welcome directamente
    if (is_google) {
      const targetUrl = subdomain 
        ? (window.location.hostname === 'localhost' 
            ? `http://${subdomain}.localhost:3000/welcome` 
            : `https://${subdomain}.105pos.pro/welcome`)
        : '/welcome'
      
      // Limpiar credenciales temporales
      localStorage.removeItem('registration_data')
      
      window.location.href = targetUrl
      return
    }

    // Para registro con email/password, hacer login
    if (!temp_password) {
      console.warn('No hay password temporal - redirigiendo al login manual')
      const targetUrl = subdomain 
        ? (window.location.hostname === 'localhost' 
            ? `http://${subdomain}.localhost:3000/login` 
            : `https://${subdomain}.105pos.pro/login`)
        : '/login'
      window.location.href = targetUrl
      return
    }

    // Determinar API URL según el entorno
    const apiUrl = subdomain
      ? (window.location.hostname === 'localhost'
          ? `http://${subdomain}.localhost:3000/api`
          : `https://${subdomain}.105pos.pro/api`)
      : '/api'

    // Hacer login
    const response = await axios.post(`${apiUrl}/login`, {
      email: email,
      password: temp_password
    })

    // Guardar token y usuario
    if (response.data.success && response.data.data?.token) {
      const { token, user } = response.data.data
      
      localStorage.setItem('authToken', token)
      localStorage.setItem('user', JSON.stringify(user))
      localStorage.setItem('loginTimestamp', Date.now())
      
      // Configurar axios con el token
      axios.defaults.headers.common['Authorization'] = `Bearer ${token}`
      
      // Limpiar credenciales temporales y datos de registro
      localStorage.removeItem('registration_data')
      
      // Redirigir al welcome del tenant
      const targetUrl = subdomain 
        ? (window.location.hostname === 'localhost' 
            ? `http://${subdomain}.localhost:3000/welcome` 
            : `https://${subdomain}.105pos.pro/welcome`)
        : '/welcome'
      
      window.location.href = targetUrl
    } else {
      throw new Error('No se recibió token de autenticación')
    }

  } catch (error) {
    console.error('Error en auto-login:', error)
    console.error('Detalles:', error.response?.data)
    
    // Si falla el auto-login, limpiar credenciales y redirigir al login manual
    const registrationData = localStorage.getItem('registration_data')
    if (registrationData) {
      const data = JSON.parse(registrationData)
      const subdomain = data.subdomain
      localStorage.removeItem('registration_data')
      
      // Guardar mensaje para mostrar en el login
      localStorage.setItem('payment_success', JSON.stringify({
        message: '¡Pago exitoso! Por favor, inicia sesión para continuar.',
        plan: planName.value
      }))
      
      const targetUrl = subdomain 
        ? (window.location.hostname === 'localhost' 
            ? `http://${subdomain}.localhost:3000/login` 
            : `https://${subdomain}.105pos.pro/login`)
        : '/login'
      
      window.location.href = targetUrl
    } else {
      window.location.href = '/login'
    }
  }
}

</script>

<style scoped>
.animate-fade-in {
  animation: fadeIn 0.6s ease-out forwards;
}

@keyframes fadeIn {
  from { opacity: 0; transform: translateY(20px); }
  to { opacity: 1; transform: translateY(0); }
}

.animate-scale-in {
  animation: scaleIn 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275) forwards;
}

@keyframes scaleIn {
  from { opacity: 0; transform: scale(0.8); }
  to { opacity: 1; transform: scale(1); }
}
</style>
