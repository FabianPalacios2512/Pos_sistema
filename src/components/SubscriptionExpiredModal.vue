<template>
  <!-- Modal NO se puede cerrar - Bloquea TODO hasta renovar -->
  <!-- 🔒 data-modal-subscription permite detectar si lo eliminan del DOM -->
  <div 
    v-if="showModal"
    data-modal-subscription="active"
    class="fixed inset-0 z-[9999] flex items-center justify-center bg-black/90 backdrop-blur-md animate-fade-in"
    @click.prevent
    @contextmenu.prevent
  >
    <div class="bg-white dark:bg-zinc-900 rounded-2xl shadow-2xl dark:shadow-black/50 max-w-6xl w-full mx-4 overflow-hidden border border-gray-300 dark:border-zinc-800">
      
      <!-- Header Elegante -->
      <div class="bg-gradient-to-br from-rose-50 to-red-50 dark:from-rose-950/30 dark:to-red-950/30 p-10 text-center border-b border-rose-200 dark:border-rose-900">
        <div class="w-20 h-20 bg-rose-100 dark:bg-rose-900/50 rounded-2xl flex items-center justify-center mx-auto mb-5 border border-rose-200 dark:border-rose-800">
          <svg class="w-10 h-10 text-rose-600 dark:text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
          </svg>
        </div>
        <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">Tu Membresía ha Expirado</h2>
        <p class="text-gray-600 dark:text-zinc-400 text-base">Renueva ahora para seguir usando el sistema</p>
      </div>

      <!-- Contenido -->
      <div class="p-8">
        
        <!-- Mensaje de Alerta -->
        <div class="bg-rose-50 dark:bg-rose-950/30 border border-rose-200 dark:border-rose-800 rounded-xl p-5 mb-8">
          <div class="flex items-start gap-3">
            <svg class="w-5 h-5 text-rose-600 dark:text-rose-400 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
            </svg>
            <div class="flex-1">
              <p class="text-sm font-bold text-rose-900 dark:text-rose-300 mb-1">
                Acceso Bloqueado
              </p>
              <p class="text-sm text-rose-700 dark:text-rose-400">
                Para continuar usando el sistema, necesitas renovar tu plan de suscripción.
              </p>
            </div>
          </div>
        </div>

        <!-- Selector de Plan -->
        <div v-if="!showPayment" class="space-y-6">
          <h3 class="text-lg font-bold text-gray-900 dark:text-white text-center">
            Renueva tu Membresía
          </h3>
          
          <!-- NOTA: Mostramos solo el plan que funciona en ePayco -->
          <div class="max-w-md mx-auto">
            <!-- Alerta informativa -->
            <div class="bg-blue-50 dark:bg-blue-950/30 border border-blue-200 dark:border-blue-800 rounded-lg p-4 mb-6 text-center">
              <p class="text-sm text-blue-700 dark:text-blue-300 font-medium">
                Plan disponible para renovación
              </p>
            </div>
          </div>

          <!-- Solo Plan Corporativo (el único que funciona) -->
          <div class="max-w-md mx-auto">
            
            <!-- 🏢 PLAN CORPORATIVO - ÚNICO DISPONIBLE -->
            <button
              @click="selectedPlan = 'corporativo'"
              class="w-full p-8 rounded-xl border-2 border-purple-500 dark:border-purple-400 bg-purple-50 dark:bg-purple-950/30 shadow-xl dark:shadow-black/50 text-left transition-all duration-200"
            >
              <div class="flex items-center justify-between mb-5">
                <h4 class="text-2xl font-bold text-gray-900 dark:text-white">Plan Empresarial</h4>
                <div class="w-8 h-8 bg-purple-600 dark:bg-purple-500 rounded-full flex items-center justify-center">
                  <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                  </svg>
                </div>
              </div>
              <p class="text-sm text-gray-600 dark:text-zinc-400 mb-5">Solución completa para tu negocio.</p>
              <p class="text-5xl font-bold text-purple-600 dark:text-purple-400 mb-6">
                $100.000<span class="text-lg font-normal text-gray-500 dark:text-zinc-400">/mes</span>
              </p>
              <ul class="space-y-3 text-sm text-gray-700 dark:text-zinc-200">
                <li class="flex items-center gap-3">
                  <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                  </svg>
                  <span class="font-medium">Usuarios Ilimitados</span>
                </li>
                <li class="flex items-center gap-3">
                  <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                  </svg>
                  <span class="font-medium">Multi-Sede / Multi-Caja</span>
                </li>
                <li class="flex items-center gap-3">
                  <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                  </svg>
                  <span class="font-medium">Tienda Web + WhatsApp Automático</span>
                </li>
                <li class="flex items-center gap-3">
                  <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                  </svg>
                  <span class="font-medium">Agente IA + Sistema CRM</span>
                </li>
                <li class="flex items-center gap-3">
                  <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                  </svg>
                  <span class="font-medium">Soporte 24/7 Dedicado</span>
                </li>
                <li class="flex items-center gap-3">
                  <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                  </svg>
                  <span class="font-medium">Integraciones Personalizadas</span>
                </li>
              </ul>
            </button>
          </div>

          <!-- Botón Continuar -->
          <button
            @click="proceedToPayment"
            :disabled="!selectedPlan || isProcessing"
            class="w-full py-4 bg-slate-900 dark:bg-slate-700 hover:bg-black dark:hover:bg-slate-600 text-white font-bold text-base rounded-xl shadow-lg shadow-slate-400/40 dark:shadow-slate-900/50 transition-all duration-300 disabled:opacity-50 disabled:cursor-not-allowed"
          >
            {{ isProcessing ? 'Procesando...' : 'Continuar al Pago' }}
          </button>
        </div>

        <!-- Pasarela de Pago (ePayco) -->
        <div v-else id="epayco-container" class="min-h-[400px]">
          <!-- ePayco se monta aquí -->
        </div>

      </div>

      <!-- Footer -->
      <div class="bg-gray-50 dark:bg-zinc-800 px-8 py-4 text-center border-t border-gray-200 dark:border-zinc-700">
        <p class="text-sm text-gray-600 dark:text-zinc-400">
          ¿Necesitas ayuda? <a href="mailto:soporte@105pos.pro" class="text-blue-600 dark:text-blue-400 hover:underline font-medium">Contáctanos</a>
        </p>
      </div>

    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, watch, onUnmounted } from 'vue'
import { appStore } from '../store/appStore'
import apiClient from '../services/apiClient'

const showModal = ref(false)
const selectedPlan = ref('corporativo') // Único plan disponible
const showPayment = ref(false)
const isProcessing = ref(false)
const tenantId = ref(null)

// 🔐 Sistema de verificación seguro
let verificationToken = null
let paymentReference = null
let verificationInterval = null

let modalCheckInterval = null

const preventContextMenu = (e) => {
  if (showModal.value) {
    e.preventDefault()
    return false
  }
}

const preventKeyboardShortcuts = (e) => {
  if (showModal.value) {
    if (e.keyCode === 123 || (e.ctrlKey && e.shiftKey && e.keyCode === 73) || 
        (e.ctrlKey && e.shiftKey && e.keyCode === 74) || (e.ctrlKey && e.shiftKey && e.keyCode === 67) || 
        (e.ctrlKey && e.keyCode === 85) || e.keyCode === 27) {
      e.preventDefault()
      return false
    }
  }
}

const checkSubscriptionStatus = async () => {
  try {
    const response = await apiClient.get('/check-subscription')
    if (response.data._subscription_expired === true || response.data.is_expired === true) {
      appStore.isSubscriptionExpired = true
      showModal.value = true
      activateAntiBypass()
      tenantId.value = response.data._tenant_id || response.data.tenant_id
    }
  } catch (error) {
    if (appStore.isSubscriptionExpired) {
      showModal.value = true
      activateAntiBypass()
    }
  }
}

const activateAntiBypass = () => {
  modalCheckInterval = setInterval(() => {
    if (appStore.isSubscriptionExpired && !showModal.value) {
      showModal.value = true
    }
    const modalElement = document.querySelector('[data-modal-subscription]')
    if (appStore.isSubscriptionExpired && !modalElement) {
      window.location.reload()
    }
  }, 100)
  
  document.addEventListener('contextmenu', preventContextMenu)
  document.addEventListener('keydown', preventKeyboardShortcuts)
  document.body.style.userSelect = 'none'
}

onMounted(() => {
  checkSubscriptionStatus()
})

onUnmounted(() => {
  if (modalCheckInterval) clearInterval(modalCheckInterval)
  if (verificationInterval) clearInterval(verificationInterval)
  document.removeEventListener('contextmenu', preventContextMenu)
  document.removeEventListener('keydown', preventKeyboardShortcuts)
  document.body.style.userSelect = ''
})

watch(() => appStore.isSubscriptionExpired, async (newVal) => {
  if (newVal) {
    showModal.value = true
    activateAntiBypass()
  }
})

const proceedToPayment = async () => {
  if (!selectedPlan.value || !tenantId.value) {
    alert('Error: No se pudo identificar tu cuenta.')
    return
  }

  isProcessing.value = true

  try {
    // Mapeo de planes: corporativo → enterprise (el que funciona en ePayco)
    const planMapping = {
      basic: { amount: 25000, epaycoName: 'basic' },
      premium: { amount: 60000, epaycoName: 'premium' },
      corporativo: { amount: 100000, epaycoName: 'enterprise' }
    }
    
    const planData = planMapping[selectedPlan.value]
    const amount = planData.amount
    const epaycoPlan = planData.epaycoName
    const reference = `renewal_${tenantId.value}_${Date.now()}`
    
    // 🔐 PASO 1: Inicializar transacción en backend y obtener token de verificación
    const initResponse = await apiClient.post('/epayco/init-transaction', {
      reference,
      plan: epaycoPlan,
      tenant_id: tenantId.value,
      amount,
      payment_frequency: 'monthly',
      customer_email: appStore.user?.email || 'cliente@105pos.pro'
    })

    if (!initResponse.data.success) {
      throw new Error('No se pudo inicializar la transacción')
    }

    // Guardar token y referencia para verificación
    verificationToken = initResponse.data.verification_token
    paymentReference = reference

    // Configurar ePayco con la API Key correcta
    const handler = window.ePayco.checkout.configure({
      key: '2943652c673afffaa5b7b67829f00a0c', // API Key de producción
      test: true // Mantener en TRUE para usar tarjeta de prueba
    })

    // Obtener URL de respuesta correcta
    const currentUrl = window.location.origin
    const responseUrl = `${currentUrl}/payment/success?tenant_id=${tenantId.value}&plan=${selectedPlan.value}&reference=${reference}&renewal=true`

    handler.open({
      name: `Plan ${selectedPlan.value.toUpperCase()} - Renovación`,
      description: `Renovación suscripción - Plan ${epaycoPlan}`,
      invoice: reference,
      currency: 'cop',
      amount: amount,
      tax_base: '0',
      tax: '0',
      country: 'co',
      lang: 'es',
      external: 'true', // Standard Checkout (página de ePayco)
      response: responseUrl,
      confirmation: 'https://105pos.pro/api/epayco/webhook',
      name_billing: appStore.businessName || 'Cliente 105POS',
      address_billing: 'Calle 123 # 45-67',
      type_doc_billing: 'cc',
      mobilephone_billing: '3000000000',
      number_doc_billing: '1000000000',
      email_billing: 'cliente@105pos.pro',
      extra1: tenantId.value,
      extra2: epaycoPlan, // Enviar 'enterprise' en vez de 'corporativo'
      extra3: 'renewal'
    })
    
    showPayment.value = true

    // 🔎 PASO 2: Iniciar verificación periódica del estado del pago
    startPaymentVerification()
    
  } catch (error) {
    console.error('Error al abrir pasarela de pago:', error)
    alert('Error al procesar el pago. Por favor intenta de nuevo.')
  } finally {
    isProcessing.value = false
  }
}

/**
 * 🔎 Verificar periódicamente el estado del pago con token seguro
 * Funciona tanto en localhost como en producción
 */
const startPaymentVerification = () => {
  if (verificationInterval) {
    clearInterval(verificationInterval)
  }

  let attempts = 0
  const maxAttempts = 120 // 10 minutos (cada 5 segundos)

  verificationInterval = setInterval(async () => {
    attempts++

    try {
      const response = await apiClient.post('/epayco/verify-payment', {
        reference: paymentReference,
        verification_token: verificationToken
      })

      if (response.data.success) {
        const status = response.data.status

        if (status === 'approved') {
          // ✅ Pago aprobado
          clearInterval(verificationInterval)
          appStore.isSubscriptionExpired = false
          showModal.value = false
          alert('✅ Pago aprobado correctamente. Tu suscripción ha sido renovada.')
          window.location.reload()
        } else if (status === 'rejected' || status === 'failed') {
          // ❌ Pago rechazado
          clearInterval(verificationInterval)
          alert('❌ El pago fue rechazado. Por favor intenta de nuevo con otro método de pago.')
        }
        // Si está 'pending', seguimos esperando
      }
    } catch (error) {
      console.error('Error verificando pago:', error)
    }

    // Detener después de max intentos
    if (attempts >= maxAttempts) {
      clearInterval(verificationInterval)
      alert('⏱️ El tiempo de verificación expiró. Por favor contacta a soporte si ya realizaste el pago.')
    }
  }, 5000) // Verificar cada 5 segundos
}
</script>

<style scoped>
@keyframes fade-in {
  from { opacity: 0; }
  to { opacity: 1; }
}

.animate-fade-in {
  animation: fade-in 0.3s ease-in;
}
</style>
