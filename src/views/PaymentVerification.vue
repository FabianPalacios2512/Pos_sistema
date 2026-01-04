<template>
  <div class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-indigo-50 flex items-center justify-center p-4">
    <div class="max-w-md w-full bg-white rounded-2xl shadow-2xl border border-blue-100 p-8 text-center animate-fade-in">
      <!-- Spinner de carga -->
      <div class="w-24 h-24 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-6 animate-pulse">
        <svg class="w-12 h-12 text-blue-600 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
      </div>

      <h1 class="text-2xl font-bold text-gray-900 mb-4">{{ statusMessage }}</h1>
      
      <p class="text-gray-600 mb-6">
        {{ detailMessage }}
      </p>

      <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 mb-6">
        <p class="text-xs text-blue-700 font-medium">
          Verificando tu pago con el banco...<br>
          <span class="text-blue-500 text-[10px]">Esto puede tomar unos segundos</span>
        </p>
      </div>

      <p class="text-xs text-gray-400">
        Intento {{ attemptNumber }} de {{ maxAttempts }}
      </p>
    </div>
  </div>
</template>

<script setup>
import { onMounted, ref } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import axios from 'axios'

const route = useRoute()
const router = useRouter()

const statusMessage = ref('Verificando Pago')
const detailMessage = ref('Estamos confirmando tu transacción con el procesador de pagos...')
const attemptNumber = ref(0)
const maxAttempts = 15 // 15 intentos = 30 segundos máximo

// Backend API con configuración correcta para producción
const backendAPI = axios.create({
  baseURL: (() => {
    if (window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1') {
      return 'http://localhost:8000'
    } else if (window.location.hostname.includes('.localhost')) {
      return 'http://localhost:8000'
    } else {
      return `https://${window.location.hostname.replace(/^[^.]+\./, '')}` // Sin subdominio
    }
  })(),
  timeout: 10000,
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json'
  }
})

onMounted(async () => {
  console.log('🔍 PaymentVerification - Iniciando verificación...')
  console.log('🔍 URL params:', route.query)

  // Obtener datos de la URL o localStorage
  const refPayco = route.query.ref_payco
  const reference = route.query.reference || route.query.x_id_invoice || route.query.p_id_invoice
  const tenantId = route.query.tenant_id || route.query.x_extra1
  const plan = route.query.plan || route.query.x_extra2
  const paymentFrequency = route.query.x_extra3 || 'monthly'
  const isRenewal = route.query.renewal === 'true'

  console.log('📋 Datos extraídos:', { refPayco, reference, tenantId, plan, paymentFrequency, isRenewal })

  if (!reference && !refPayco) {
    console.error('❌ No se encontró referencia de pago')
    detailMessage.value = 'No pudimos identificar tu pago. Contacta a soporte.'
    setTimeout(() => {
      router.push('/select-plan')
    }, 3000)
    return
  }

  // FUNCIÓN DE VERIFICACIÓN CON REINTENTOS
  const verifyPaymentStatus = async () => {
    attemptNumber.value++

    try {
      console.log(`🔄 Intento ${attemptNumber.value}/${maxAttempts} - Verificando pago...`)

      // Consultar el estado del pago en nuestro backend
      const response = await backendAPI.get('/api/epayco/check-payment-status', {
        params: {
          reference: reference,
          ref_payco: refPayco
        }
      })

      console.log('📥 Respuesta del servidor:', response.data)

      const status = response.data.status

      if (status === 'approved') {
        // ✅ PAGO APROBADO
        console.log('✅ Pago aprobado - Redirigiendo a success...')
        
        // Redirigir a PaymentSuccess con los datos necesarios (SIN ref_payco para evitar que se quede en URL)
        const successUrl = `/payment/success?` + new URLSearchParams({
          reference: reference || '',
          tenant_id: tenantId || '',
          plan: plan || '',
          renewal: isRenewal ? 'true' : 'false',
          is_upgrade: route.query.is_upgrade || 'false'
        }).toString()

        window.location.href = successUrl
        return

      } else if (status === 'rejected' || status === 'failed') {
        // ❌ PAGO RECHAZADO
        console.log('❌ Pago rechazado - Redirigiendo a failure...')
        
        const failureUrl = `/payment/failure?` + new URLSearchParams({
          payment_id: refPayco || reference || '',
          status: 'rejected'
        }).toString()

        window.location.href = failureUrl
        return

      } else if (status === 'pending' || status === 'processing') {
        // ⏳ PAGO PENDIENTE - Reintentar
        console.log('⏳ Pago aún pendiente, reintentando...')
        
        if (attemptNumber.value >= maxAttempts) {
          // Máximo de intentos alcanzado
          console.warn('⚠️ Máximo de intentos alcanzado, mostrando pendiente...')
          detailMessage.value = 'Tu pago está siendo procesado. Te notificaremos por email cuando se confirme.'
          
          setTimeout(() => {
            if (isRenewal) {
              window.location.href = '/dashboard'
            } else {
              window.location.href = '/select-plan'
            }
          }, 5000)
          return
        }

        // Reintentar en 2 segundos
        setTimeout(verifyPaymentStatus, 2000)
        return

      } else {
        // Estado desconocido
        console.warn('⚠️ Estado desconocido:', status)
        
        if (attemptNumber.value >= maxAttempts) {
          detailMessage.value = 'No pudimos verificar tu pago. Contacta a soporte si ya realizaste el pago.'
          setTimeout(() => {
            window.location.href = '/select-plan'
          }, 5000)
          return
        }

        setTimeout(verifyPaymentStatus, 2000)
      }

    } catch (error) {
      console.error('❌ Error verificando pago:', error)
      
      if (attemptNumber.value >= maxAttempts) {
        detailMessage.value = 'Error de conexión. Por favor verifica tu conexión a internet.'
        setTimeout(() => {
          window.location.href = '/select-plan'
        }, 5000)
        return
      }

      // Reintentar en 2 segundos
      setTimeout(verifyPaymentStatus, 2000)
    }
  }

  // Iniciar verificación
  verifyPaymentStatus()
})
</script>

<style scoped>
.animate-fade-in {
  animation: fadeIn 0.6s ease-out forwards;
}

@keyframes fadeIn {
  from { opacity: 0; transform: translateY(20px); }
  to { opacity: 1; transform: translateY(0); }
}

@keyframes spin {
  from { transform: rotate(0deg); }
  to { transform: rotate(360deg); }
}
</style>
