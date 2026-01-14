<template>
  <div class="min-h-screen bg-gradient-to-br from-emerald-50 via-white to-teal-50 flex items-center justify-center p-4">
    <div class="max-w-md w-full bg-white rounded-2xl shadow-2xl border border-emerald-100 p-8 text-center animate-fade-in">
      <!-- Icono de éxito -->
      <div class="w-24 h-24 bg-emerald-100 rounded-full flex items-center justify-center mx-auto mb-6 animate-scale-in">
        <svg class="w-12 h-12 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path>
        </svg>
      </div>

      <h1 class="text-3xl font-bold text-gray-900 mb-4">¡Pago Exitoso! 🎉</h1>
      
      <p class="text-gray-600 mb-6 text-lg">
        Tu suscripción ha sido activada correctamente.
      </p>

      <div class="bg-emerald-50 border border-emerald-200 rounded-xl p-5 mb-6">
        <p class="text-sm text-emerald-800 space-y-1">
          <span class="block font-semibold text-base mb-2">✅ Plan Activado</span>
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
import { useRoute } from 'vue-router'
import axios from 'axios'

// 🔥 Crear instancia de axios con la URL base correcta del backend
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
  // 🔥 IMPORTANTE: Permitir URLs absolutas para subdominios
  allowAbsoluteUrls: true
})

console.log('🚀 PaymentSuccess.vue - COMPONENTE CARGADO')

const route = useRoute()
const planName = ref('')
const companyName = ref('')
const paymentId = ref('')
const countdown = ref(5)

console.log('🔍 PaymentSuccess.vue - route.query:', route.query)

onMounted(async () => {
  console.log('🎯 PaymentSuccess - onMounted EJECUTADO')
  
  // 1. Intentar recuperar datos de localStorage (ePayco a veces limpia params)
  const pendingPayment = localStorage.getItem('pending_payment')
  let localData = {}
  if (pendingPayment) {
    try {
      localData = JSON.parse(pendingPayment)
      console.log('📦 Datos recuperados de localStorage:', localData)
    } catch (e) {
      console.error('Error parsing pending_payment:', e)
    }
  }

  // 2. Obtener información del pago de URL params o localStorage
  // ePayco envía ?ref_payco=...
  const refPayco = route.query.ref_payco
  
  const reference = route.query.reference || route.query.id || localData.reference || ''
  const tenantId = route.query.tenant_id || localData.tenant_id || ''
  const plan = route.query.plan || localData.plan || ''
  const isUpgrade = route.query.is_upgrade === 'true' || localData.is_upgrade === true
  
  console.log('🔍 PaymentSuccess - Params combinados:', { reference, tenantId, plan, isUpgrade, refPayco })
  
  paymentId.value = refPayco || reference
  
  // Si tenemos ref_payco pero no reference, usar ref_payco como ID visual
  if (refPayco && !reference) {
     // Si viene de ePayco Dashboard, es posible que solo tengamos ref_payco
     // En este caso, confiamos en que el Webhook ya procesó el pago en el backend
     // O usamos los datos de localStorage para mostrar la info
  }
  
  // 🔥 SI TENEMOS DATOS (URL o LocalStorage), ACTIVAR O MOSTRAR ÉXITO
  if ((reference || refPayco) && plan) {
    console.log('✅ PaymentSuccess - Datos encontrados, procesando...', { isUpgrade })
    
    try {
      // Limpiar localStorage para no reutilizar
      localStorage.removeItem('pending_payment')

      // 🔥 DETECTAR SI ES UPGRADE O PAGO INICIAL
      if (isUpgrade) {
        // ==================== FLUJO DE UPGRADE ====================
        // Después de pago, Wompi redirige desde su servidor
        // El usuario NO está autenticado, por eso usamos endpoint PÚBLICO
        console.log('📤 PaymentSuccess - UPGRADE: Enviando a /api/process-upgrade (PÚBLICO)')
        
        if (!tenantId) {
          throw new Error('No se encontró el ID del negocio en la URL')
        }
        
        // Recuperar payment_frequency de localStorage
        const pendingUpgrade = localStorage.getItem('pending_upgrade')
        if (!pendingUpgrade) {
          console.warn('⚠️ PaymentSuccess - No hay pending_upgrade en localStorage, usando defaults')
        }
        
        const upgradeData = pendingUpgrade ? JSON.parse(pendingUpgrade) : {}
        
        console.log('📤 PaymentSuccess - Datos de upgrade:', { plan, tenantId, reference })
        
        // Llamar a endpoint PÚBLICO para procesar upgrade
        const upgradeResponse = await backendAPI.post('/api/process-upgrade', {
          tenant_id: tenantId,
          plan: plan,
          reference: reference,
          is_upgrade: true
        })
        
        console.log('✅ PaymentSuccess - Respuesta de upgrade:', upgradeResponse.data)
        
        if (upgradeResponse.data.success) {
          planName.value = plan === 'basic' ? 'Plan Basic' : 
                         plan === 'premium' ? 'Plan Premium' : 
                         plan === 'enterprise' ? 'Plan Enterprise' : 'Plan Seleccionado'
          
          // Limpiar datos pendientes
          localStorage.removeItem('pending_upgrade')
          localStorage.removeItem('pending_payment')
          
          console.log('✅ PaymentSuccess - Plan actualizado correctamente (UPGRADE)')
        } else {
          throw new Error(upgradeResponse.data.message || 'Error al actualizar el plan')
        }
        
      } else {
        // ==================== FLUJO DE PAGO INICIAL ====================
        // Nuevo tenant, sin autenticación requerida
        console.log('📤 PaymentSuccess - PAGO INICIAL: Enviando a /api/update-tenant-plan')
        
        if (!tenantId) {
          throw new Error('No se encontró el ID del negocio')
        }
        
        // Activar plan directamente (Wompi ya verificó el pago)
        const updateResponse = await backendAPI.post('/api/update-tenant-plan', {
          tenant_id: tenantId,
          plan: plan
        })
        
        console.log('✅ PaymentSuccess - Respuesta de activación de plan:', updateResponse.data)
        
        if (updateResponse.data.success) {
          planName.value = plan === 'basic' ? 'Plan Basic' : 
                         plan === 'premium' ? 'Plan Premium' : 
                         plan === 'enterprise' ? 'Plan Enterprise' : 'Plan Seleccionado'
          
          // Limpiar pago pendiente (por si existe)
          localStorage.removeItem('pending_payment')
          
          console.log('✅ PaymentSuccess - Plan activado correctamente (INICIAL)')
        } else {
          throw new Error(updateResponse.data.message || 'Error al activar el plan')
        }
      }
      
    } catch (error) {
      console.error('❌ PaymentSuccess - Error procesando pago:', error)
      console.error('❌ PaymentSuccess - Error response:', error.response?.data)
      console.error('❌ PaymentSuccess - Error message:', error.message)
      console.error('❌ PaymentSuccess - Full error:', JSON.stringify(error, null, 2))
      
      // Mostrar error más específico
      const errorMsg = error.response?.data?.message || error.message || 'Error desconocido'
      alert('❌ Error al activar el plan\n\n' + errorMsg + '\n\nContacta a soporte con el ID: ' + reference)
    }
  } else {
    console.warn('⚠️ PaymentSuccess - Faltan datos en URL:', { reference, plan, isUpgrade })
  }
  
  // Obtener datos de localStorage
  const registrationData = localStorage.getItem('registration_data')
  if (registrationData) {
    const data = JSON.parse(registrationData)
    companyName.value = data.company_name || ''
  }

  // Countdown para redirección automática
  const interval = setInterval(() => {
    countdown.value--
    if (countdown.value <= 0) {
      clearInterval(interval)
      redirectToDashboard()
    }
  }, 1000)
})

const redirectToDashboard = async () => {
  // Verificar si es renovación
  const isRenewal = route.query.renewal === 'true'
  
  if (isRenewal) {
    // Limpiar datos de pago
    localStorage.removeItem('pending_payment')
    
    // 🔥 Redirigir al dashboard SIN parámetros de pago en la URL
    // Usar replace() para no dejar params en la URL
    window.location.replace('/dashboard')
    return
  }

  const registrationData = localStorage.getItem('registration_data')
  
  if (registrationData) {
    const data = JSON.parse(registrationData)
    
    // Limpiar datos de pago
    localStorage.removeItem('pending_payment')
    
    // 🔐 Hacer auto-login en lugar de redirigir al login
    await performAutoLogin()
  } else {
    window.location.href = '/login'
  }
}

// 🔑 Función para hacer auto-login después del pago exitoso
const performAutoLogin = async () => {
  try {
    console.log('🔐 Iniciando auto-login después del pago...')
    
    // Obtener credenciales guardadas temporalmente
    const registrationData = localStorage.getItem('registration_data')
    if (!registrationData) {
      console.warn('⚠️ No hay datos de registro - redirigiendo al login manual')
      window.location.href = '/login'
      return
    }

    const data = JSON.parse(registrationData)
    const { email, temp_password, is_google, subdomain } = data

    // Si es registro con Google, redirigir al welcome directamente
    if (is_google) {
      console.log('✅ Usuario registrado con Google - redirigiendo a welcome')
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
      console.warn('⚠️ No hay password temporal - redirigiendo al login manual')
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

    console.log('📤 Haciendo login automático en:', apiUrl)

    // Hacer login
    const response = await axios.post(`${apiUrl}/login`, {
      email: email,
      password: temp_password
    })

    console.log('✅ Login exitoso:', response.data)

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
      
      console.log('🚀 Redirigiendo a:', targetUrl)
      window.location.href = targetUrl
    } else {
      throw new Error('No se recibió token de autenticación')
    }

  } catch (error) {
    console.error('❌ Error en auto-login:', error)
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
