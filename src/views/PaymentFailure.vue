<template>
  <div class="min-h-screen bg-gradient-to-br from-red-50 via-white to-orange-50 flex items-center justify-center p-4">
    <div class="max-w-md w-full bg-white rounded-2xl shadow-2xl border border-red-100 p-8 text-center animate-fade-in">
      <!-- Icono de error -->
      <div class="w-24 h-24 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-6 animate-scale-in">
        <svg class="w-12 h-12 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path>
        </svg>
      </div>

      <h1 class="text-3xl font-bold text-gray-900 mb-4">Pago No Procesado</h1>
      
      <p class="text-gray-600 mb-6 text-lg">
        No se pudo completar tu pago. Por favor verifica tu información e intenta nuevamente.
      </p>

      <div class="bg-red-50 border border-red-200 rounded-xl p-5 mb-6">
        <p class="text-sm text-red-800">
          <strong>Razón:</strong> {{ errorReason }}
        </p>
      </div>

      <div class="space-y-3">
        <button 
          @click="retryPayment"
          class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3.5 rounded-xl transition-all duration-200 shadow-lg hover:shadow-xl"
        >
          Intentar Nuevamente
        </button>

        <button 
          @click="activateTrial"
          class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-semibold py-3.5 rounded-xl transition-all duration-200 shadow-lg hover:shadow-xl"
        >
          Activar Trial de 3 Días Gratis
        </button>

        <button 
          @click="goToRegister"
          class="w-full bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold py-3.5 rounded-xl transition-all duration-200"
        >
          Volver al Inicio
        </button>
      </div>

      <a 
        href="https://wa.me/573001234567?text=Hola,%20tuve%20un%20problema%20con%20mi%20pago%20en%20105%20POS" 
        target="_blank"
        class="block mt-6 text-sm text-blue-600 hover:text-blue-700 hover:underline font-medium"
      >
        ¿Necesitas ayuda? Contáctanos por WhatsApp
      </a>

      <p class="mt-4 text-xs text-gray-400" v-if="paymentId">
        Referencia: {{ paymentId }}
      </p>
    </div>
  </div>
</template>

<script setup>
import { onMounted, ref } from 'vue'
import { useRoute } from 'vue-router'
import axios from 'axios'
import { useToast } from '../composables/useToast.js'

const { showError } = useToast()

const route = useRoute()
const errorReason = ref('El pago fue rechazado por el procesador de pagos')
const paymentId = ref('')

onMounted(() => {
  paymentId.value = route.query.payment_id || route.query.collection_id || ''
  
  // Determinar razón del error
  const status = route.query.collection_status || route.query.status
  if (status === 'rejected') {
    errorReason.value = 'Pago rechazado. Verifica los datos de tu tarjeta.'
  } else if (status === 'cancelled') {
    errorReason.value = 'Pago cancelado por el usuario.'
  } else if (status === 'in_process') {
    errorReason.value = 'El pago está en proceso de verificación.'
  }
})

const retryPayment = () => {
  // Volver a la selección de plan
  const registrationData = localStorage.getItem('registration_data')
  if (registrationData) {
    const data = JSON.parse(registrationData)
    if (data.redirect_url) {
      window.location.href = data.redirect_url.replace(/\/welcome\/?$/, '') + '/select-plan'
    } else {
      window.location.href = '/select-plan'
    }
  } else {
    window.location.href = '/select-plan'
  }
}

const activateTrial = async () => {
  const registrationData = localStorage.getItem('registration_data')
  if (!registrationData) {
    window.location.href = '/register'
    return
  }

  const data = JSON.parse(registrationData)
  
  try {
    // Activar trial de 3 días
    const apiUrl = import.meta.env.VITE_API_URL || 'http://localhost:8000'
    const response = await axios.post(`${apiUrl}/api/update-tenant-plan`, {
      tenant_id: data.tenant_id,
      plan: 'trial_express'
    })

    if (response.data.success) {
      // Guardar éxito y redirigir a login
      localStorage.setItem('registration_success', JSON.stringify({
        message: '¡Trial activado! Inicia sesión para comenzar.',
        companyName: data.company_name,
        subdomain: data.redirect_url
      }))
      
      window.location.href = 'http://localhost:3000/login'
    }
  } catch (error) {
    console.error('Error activando trial:', error)
    showError('Error al activar trial. Por favor intenta nuevamente.')
  }
}

const goToRegister = () => {
  localStorage.removeItem('registration_data')
  window.location.href = 'http://localhost:3000/register'
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
