<template>
  <!-- Modal de Verificación de Pago -->
  <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/80 backdrop-blur-sm p-4">
    
    <!-- VERIFICANDO PAGO -->
    <div v-if="paymentStatus === 'loading'" class="max-w-md w-full bg-white dark:bg-zinc-900 rounded-2xl shadow-2xl border border-gray-200 dark:border-zinc-800 p-8 text-center animate-fade-in">
      <div class="w-16 h-16 border-4 border-slate-200 dark:border-zinc-700 border-t-slate-600 dark:border-t-slate-400 rounded-full animate-spin mx-auto mb-6"></div>
      <h1 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">Verificando transacción</h1>
      <p class="text-gray-500 dark:text-zinc-400 text-sm mb-6">Confirmando tu pago con el procesador...</p>
      <div class="bg-slate-50 dark:bg-zinc-800/50 border border-slate-200 dark:border-zinc-700 rounded-lg p-3">
        <p class="text-xs text-slate-600 dark:text-zinc-400">Esto puede tomar unos segundos</p>
      </div>
    </div>

    <!-- PAGO EXITOSO -->
    <div v-else-if="paymentStatus === 'approved'" class="max-w-md w-full bg-white dark:bg-zinc-900 rounded-2xl shadow-2xl border border-gray-200 dark:border-zinc-800 overflow-hidden animate-scale-in">
      <div class="bg-slate-50 dark:bg-zinc-800/50 border-b border-slate-200 dark:border-zinc-700 p-6">
        <div class="flex items-center gap-4">
          <div class="w-12 h-12 bg-emerald-100 dark:bg-emerald-900/30 rounded-full flex items-center justify-center flex-shrink-0">
            <svg class="w-6 h-6 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
              <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path>
            </svg>
          </div>
          <div>
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Pago confirmado</h2>
            <p class="text-sm text-gray-500 dark:text-zinc-400">Tu suscripción ha sido activada</p>
          </div>
        </div>
      </div>
      <div class="p-6">
        <div class="bg-white dark:bg-zinc-800 border border-gray-200 dark:border-zinc-700 rounded-xl p-4 mb-5">
          <div class="flex items-center justify-between mb-3">
            <span class="text-sm text-gray-500 dark:text-zinc-400">Plan adquirido</span>
            <span class="px-2.5 py-1 bg-emerald-50 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400 text-xs font-medium rounded-full border border-emerald-200 dark:border-emerald-800">Activo</span>
          </div>
          <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ planName }} {{ frequencyName }}</p>
        </div>
        <div class="space-y-3 mb-6">
          <div class="flex items-center gap-3">
            <div class="w-5 h-5 rounded-full bg-slate-100 dark:bg-zinc-700 flex items-center justify-center flex-shrink-0">
              <svg class="w-3 h-3 text-slate-600 dark:text-zinc-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path></svg>
            </div>
            <span class="text-sm text-gray-600 dark:text-zinc-300">Acceso completo a funcionalidades</span>
          </div>
          <div class="flex items-center gap-3">
            <div class="w-5 h-5 rounded-full bg-slate-100 dark:bg-zinc-700 flex items-center justify-center flex-shrink-0">
              <svg class="w-3 h-3 text-slate-600 dark:text-zinc-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path></svg>
            </div>
            <span class="text-sm text-gray-600 dark:text-zinc-300">Soporte técnico incluido</span>
          </div>
        </div>
        <button @click="goToHome" class="w-full py-3 bg-slate-900 dark:bg-slate-700 hover:bg-black dark:hover:bg-slate-600 text-white font-medium rounded-xl transition-all duration-200">Continuar</button>
        <p class="text-xs text-center text-gray-400 dark:text-zinc-500 mt-4">Recibirás un comprobante en tu correo electrónico</p>
      </div>
    </div>

    <!-- PAGO RECHAZADO -->
    <div v-else-if="paymentStatus === 'rejected'" class="max-w-md w-full bg-white dark:bg-zinc-900 rounded-2xl shadow-2xl border border-gray-200 dark:border-zinc-800 overflow-hidden animate-scale-in">
      <div class="bg-slate-50 dark:bg-zinc-800/50 border-b border-slate-200 dark:border-zinc-700 p-6">
        <div class="flex items-center gap-4">
          <div class="w-12 h-12 bg-rose-100 dark:bg-rose-900/30 rounded-full flex items-center justify-center flex-shrink-0">
            <svg class="w-6 h-6 text-rose-600 dark:text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
              <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
          </div>
          <div>
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Pago rechazado</h2>
            <p class="text-sm text-gray-500 dark:text-zinc-400">No se pudo procesar la transacción</p>
          </div>
        </div>
      </div>
      <div class="p-6">
        <div class="bg-rose-50 dark:bg-rose-900/20 border border-rose-200 dark:border-rose-800/50 rounded-xl p-4 mb-5">
          <p class="text-sm text-rose-700 dark:text-rose-300">{{ errorMessage || 'Tu banco rechazó la transacción. Verifica los datos de tu tarjeta o intenta con otro método de pago.' }}</p>
        </div>
        <div class="space-y-2 text-sm text-gray-600 dark:text-zinc-400 mb-6">
          <p class="font-medium text-gray-700 dark:text-zinc-300">Posibles causas:</p>
          <ul class="list-disc list-inside space-y-1">
            <li>Fondos insuficientes</li>
            <li>Tarjeta bloqueada o vencida</li>
            <li>Límite de transacciones excedido</li>
          </ul>
        </div>
        <div class="flex gap-3">
          <button @click="goToHome" class="flex-1 py-3 bg-white dark:bg-zinc-800 border border-gray-200 dark:border-zinc-700 text-gray-700 dark:text-zinc-300 font-medium rounded-xl hover:bg-gray-50 dark:hover:bg-zinc-700 transition-all">Volver</button>
          <button @click="retryPayment" class="flex-1 py-3 bg-slate-900 dark:bg-slate-700 hover:bg-black dark:hover:bg-slate-600 text-white font-medium rounded-xl transition-all">Reintentar</button>
        </div>
      </div>
    </div>

    <!-- PAGO PENDIENTE -->
    <div v-else-if="paymentStatus === 'pending'" class="max-w-md w-full bg-white dark:bg-zinc-900 rounded-2xl shadow-2xl border border-gray-200 dark:border-zinc-800 overflow-hidden animate-scale-in">
      <div class="bg-slate-50 dark:bg-zinc-800/50 border-b border-slate-200 dark:border-zinc-700 p-6">
        <div class="flex items-center gap-4">
          <div class="w-12 h-12 bg-amber-100 dark:bg-amber-900/30 rounded-full flex items-center justify-center flex-shrink-0">
            <svg class="w-6 h-6 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
              <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
          </div>
          <div>
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Pago en proceso</h2>
            <p class="text-sm text-gray-500 dark:text-zinc-400">Tu transacción está siendo verificada</p>
          </div>
        </div>
      </div>
      <div class="p-6">
        <div class="bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800/50 rounded-xl p-4 mb-5">
          <p class="text-sm text-amber-700 dark:text-amber-300">Tu pago está siendo procesado. Te notificaremos por correo cuando se confirme.</p>
        </div>
        <button @click="goToHome" class="w-full py-3 bg-slate-900 dark:bg-slate-700 hover:bg-black dark:hover:bg-slate-600 text-white font-medium rounded-xl transition-all">Entendido</button>
      </div>
    </div>

    <!-- PAGO CANCELADO / NO COMPLETADO -->
    <div v-else-if="paymentStatus === 'cancelled'" class="max-w-md w-full bg-white dark:bg-zinc-900 rounded-2xl shadow-2xl border border-gray-200 dark:border-zinc-800 overflow-hidden animate-scale-in">
      <div class="bg-slate-50 dark:bg-zinc-800/50 border-b border-slate-200 dark:border-zinc-700 p-6">
        <div class="flex items-center gap-4">
          <div class="w-12 h-12 bg-slate-100 dark:bg-zinc-700 rounded-full flex items-center justify-center flex-shrink-0">
            <svg class="w-6 h-6 text-slate-600 dark:text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
              <path stroke-linecap="round" stroke-linejoin="round" d="M9 15L3 9m0 0l6-6M3 9h12a6 6 0 010 12h-3"></path>
            </svg>
          </div>
          <div>
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Pago no completado</h2>
            <p class="text-sm text-gray-500 dark:text-zinc-400">La transacción fue cancelada o no se finalizó</p>
          </div>
        </div>
      </div>
      <div class="p-6">
        <div class="bg-slate-50 dark:bg-zinc-800 border border-slate-200 dark:border-zinc-700 rounded-xl p-4 mb-5">
          <p class="text-sm text-slate-700 dark:text-zinc-300">Parece que saliste del proceso de pago antes de completarlo. No se realizó ningún cargo a tu cuenta.</p>
        </div>
        <div class="flex gap-3">
          <button @click="goToHome" class="flex-1 py-3 bg-white dark:bg-zinc-800 border border-gray-200 dark:border-zinc-700 text-gray-700 dark:text-zinc-300 font-medium rounded-xl hover:bg-gray-50 dark:hover:bg-zinc-700 transition-all">Volver al inicio</button>
          <button @click="retryPayment" class="flex-1 py-3 bg-slate-900 dark:bg-slate-700 hover:bg-black dark:hover:bg-slate-600 text-white font-medium rounded-xl transition-all">Intentar de nuevo</button>
        </div>
      </div>
    </div>

    <!-- ERROR -->
    <div v-else-if="paymentStatus === 'error'" class="max-w-md w-full bg-white dark:bg-zinc-900 rounded-2xl shadow-2xl border border-gray-200 dark:border-zinc-800 overflow-hidden animate-scale-in">
      <div class="bg-slate-50 dark:bg-zinc-800/50 border-b border-slate-200 dark:border-zinc-700 p-6">
        <div class="flex items-center gap-4">
          <div class="w-12 h-12 bg-gray-100 dark:bg-zinc-700 rounded-full flex items-center justify-center flex-shrink-0">
            <svg class="w-6 h-6 text-gray-600 dark:text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
              <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
            </svg>
          </div>
          <div>
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Error de verificación</h2>
            <p class="text-sm text-gray-500 dark:text-zinc-400">No pudimos verificar tu pago</p>
          </div>
        </div>
      </div>
      <div class="p-6">
        <div class="bg-gray-50 dark:bg-zinc-800 border border-gray-200 dark:border-zinc-700 rounded-xl p-4 mb-5">
          <p class="text-sm text-gray-700 dark:text-zinc-300">Hubo un problema de conexión. Si realizaste el pago, lo detectaremos automáticamente en unos minutos.</p>
        </div>
        <button @click="goToHome" class="w-full py-3 bg-slate-900 dark:bg-slate-700 hover:bg-black dark:hover:bg-slate-600 text-white font-medium rounded-xl transition-all">Volver al inicio</button>
        <p class="text-xs text-center text-gray-400 dark:text-zinc-500 mt-4">Contacta a soporte@105pos.pro si tienes problemas</p>
      </div>
    </div>

  </div>
</template>

<script setup>
import { onMounted, ref } from 'vue'
import { useRoute } from 'vue-router'
import axios from 'axios'

const route = useRoute()

const paymentStatus = ref('loading') // loading, approved, rejected, pending, error
const planName = ref('')
const frequencyName = ref('')
const errorMessage = ref('')
const attemptNumber = ref(0)
const maxAttempts = 15

const backendAPI = axios.create({
  baseURL: (() => {
    if (window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1') {
      return 'http://localhost:8000'
    } else if (window.location.hostname.includes('.localhost')) {
      return 'http://localhost:8000'
    } else {
      return `https://${window.location.hostname.replace(/^[^.]+\./, '')}`
    }
  })(),
  timeout: 10000,
  headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' }
})

const goToHome = () => {
  window.history.replaceState({}, document.title, '/')
  window.location.href = '/'
}

const retryPayment = () => {
  window.history.replaceState({}, document.title, '/')
  window.location.href = '/#/my-profile'
}

onMounted(async () => {
  const refPayco = route.query.ref_payco
  const reference = route.query.reference || route.query.x_id_invoice || route.query.p_id_invoice
  const tenantId = route.query.tenant_id || route.query.x_extra1
  const plan = route.query.plan || route.query.x_extra2
  const paymentFrequency = route.query.x_extra3 || 'monthly'
  const isUpgrade = route.query.is_upgrade === 'true'

  const planNames = { basic: 'Basic', premium: 'Premium', enterprise: 'Enterprise' }
  const frequencyNames = { monthly: 'Mensual', yearly: 'Anual', '24months': '24 Meses' }
  planName.value = planNames[plan] || plan || 'Premium'
  frequencyName.value = frequencyNames[paymentFrequency] || 'Mensual'

  if (!reference && !refPayco) {
    paymentStatus.value = 'error'
    return
  }

  const verifyPaymentStatus = async () => {
    attemptNumber.value++
    try {
      const response = await backendAPI.get('/api/epayco/check-payment-status', {
        params: { reference, ref_payco: refPayco }
      })
      const status = response.data.status

      if (status === 'approved') {
        if (isUpgrade) {
          try {
            await backendAPI.post('/api/process-upgrade', {
              tenant_id: tenantId, plan, payment_frequency: paymentFrequency, ref_payco: refPayco, reference
            })
          } catch (e) { console.error('Error actualizando plan:', e) }
        }
        paymentStatus.value = 'approved'
      } else if (status === 'rejected' || status === 'failed') {
        errorMessage.value = response.data.message || ''
        paymentStatus.value = 'rejected'
      } else if (status === 'not_found') {
        // Pago no encontrado - usuario canceló o no completó
        paymentStatus.value = 'cancelled'
      } else if (status === 'pending' || status === 'processing') {
        if (attemptNumber.value >= maxAttempts) {
          paymentStatus.value = 'pending'
        } else {
          setTimeout(verifyPaymentStatus, 2000)
        }
      } else {
        if (attemptNumber.value >= maxAttempts) {
          paymentStatus.value = 'error'
        } else {
          setTimeout(verifyPaymentStatus, 2000)
        }
      }
    } catch (error) {
      // Error 404 = pago no encontrado (usuario canceló)
      if (error.response?.status === 404 || error.response?.data?.status === 'not_found') {
        paymentStatus.value = 'cancelled'
        return
      }
      // Otros errores - reintentar hasta maxAttempts
      if (attemptNumber.value >= maxAttempts) {
        paymentStatus.value = 'error'
      } else {
        setTimeout(verifyPaymentStatus, 2000)
      }
    }
  }
  verifyPaymentStatus()
})
</script>

<style scoped>
.animate-fade-in { animation: fadeIn 0.5s ease-out forwards; }
.animate-scale-in { animation: scaleIn 0.4s ease-out forwards; }
@keyframes fadeIn { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
@keyframes scaleIn { from { opacity: 0; transform: scale(0.95); } to { opacity: 1; transform: scale(1); } }
</style>
