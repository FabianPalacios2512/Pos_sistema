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

console.log('🚀 PaymentSuccess.vue - COMPONENTE CARGADO')

const route = useRoute()
const planName = ref('')
const companyName = ref('')
const paymentId = ref('')
const countdown = ref(5)

console.log('🔍 PaymentSuccess.vue - route.query:', route.query)

onMounted(async () => {
  console.log('🎯 PaymentSuccess - onMounted EJECUTADO')
  // Obtener información del pago de URL params
  const reference = route.query.reference || route.query.id || ''
  const tenantId = route.query.tenant_id || ''
  const plan = route.query.plan || ''
  
  console.log('🔍 PaymentSuccess - URL params:', { reference, tenantId, plan })
  
  paymentId.value = reference
  
  // 🔥 SI TENEMOS DATOS EN LA URL, ACTIVAR EL PLAN DIRECTAMENTE
  // (Wompi solo redirige a /success si el pago fue exitoso)
  if (reference && tenantId && plan) {
    console.log('✅ PaymentSuccess - Datos completos en URL, activando plan...')
    
    try {
      console.log('📤 PaymentSuccess - Enviando a /api/update-tenant-plan:', { tenant_id: tenantId, plan: plan })
      
      // Activar plan directamente (Wompi ya verificó el pago)
      const updateResponse = await axios.post('/api/update-tenant-plan', {
        tenant_id: tenantId,
        plan: plan
      })
      
      console.log('✅ PaymentSuccess - Respuesta de actualización de plan:', updateResponse.data)
      
      if (updateResponse.data.success) {
        planName.value = plan === 'basic' ? 'Plan Basic' : 
                       plan === 'premium' ? 'Plan Premium' : 
                       plan === 'enterprise' ? 'Plan Enterprise' : 'Plan Seleccionado'
        
        // Limpiar pago pendiente (por si existe)
        localStorage.removeItem('pending_payment')
        console.log('✅ PaymentSuccess - Plan activado correctamente')
      } else {
        console.error('❌ PaymentSuccess - Error al activar plan:', updateResponse.data)
        alert('Error al activar el plan. Contacta a soporte con el ID: ' + reference)
      }
      
    } catch (error) {
      console.error('❌ PaymentSuccess - Error actualizando plan:', error)
      console.error('❌ PaymentSuccess - Error response:', error.response?.data)
      alert('Error al activar el plan. Contacta a soporte con el ID: ' + reference)
    }
  } else {
    console.warn('⚠️ PaymentSuccess - Faltan datos en URL:', { reference, tenantId, plan })
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

const redirectToDashboard = () => {
  const registrationData = localStorage.getItem('registration_data')
  
  if (registrationData) {
    const data = JSON.parse(registrationData)
    const tenantSubdomain = data.subdomain || ''
    
    // Limpiar datos de registro y pago
    localStorage.removeItem('registration_data')
    localStorage.removeItem('pending_payment')
    
    // Guardar mensaje de éxito
    localStorage.setItem('payment_success', JSON.stringify({
      message: '¡Pago exitoso! Inicia sesión para acceder.',
      plan: planName.value
    }))
    
    // 🔥 Redirigir al subdominio correcto del tenant
    if (window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1') {
      // Local: redirigir a subdomain.localhost:3000/login
      window.location.href = `http://${tenantSubdomain}.localhost:3000/login`
    } else {
      // Producción: redirigir a subdomain.105pos.pro/login
      window.location.href = `https://${tenantSubdomain}.105pos.pro/login`
    }
  } else {
    window.location.href = '/login'
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
