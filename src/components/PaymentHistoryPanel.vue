<template>
  <div class="bg-white dark:bg-zinc-900 rounded-lg border border-gray-200 dark:border-zinc-800 shadow-sm overflow-hidden">
    <div class="px-6 py-5 border-b border-gray-200 dark:border-zinc-800">
      <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Historial de Pagos</h3>
      <p class="text-sm text-gray-500 dark:text-zinc-400 mt-0.5">Tus upgrades y transacciones</p>
    </div>

    <div class="divide-y divide-gray-200 dark:divide-zinc-800">
      <!-- Estado de Carga -->
      <div v-if="loading" class="px-6 py-8 flex items-center justify-center">
        <div class="flex items-center gap-3">
          <div class="w-5 h-5 border-2 border-slate-300 dark:border-slate-600 border-t-slate-900 dark:border-t-white rounded-full animate-spin"></div>
          <span class="text-sm text-gray-600 dark:text-zinc-400">Cargando historial...</span>
        </div>
      </div>

      <!-- Lista de Pagos -->
      <template v-else-if="payments.length > 0">
        <div v-for="payment in payments" :key="payment.id" class="px-6 py-4 flex items-center justify-between hover:bg-gray-50 dark:hover:bg-zinc-800/50 transition-colors">
          
          <!-- Info del Pago -->
          <div class="flex-1 min-w-0">
            <div class="flex items-center gap-3">
              
              <!-- Icono según estado -->
              <div :class="[
                'w-11 h-11 rounded-lg flex items-center justify-center flex-shrink-0',
                (payment.status === 'completed' || payment.status === 'approved')
                  ? 'bg-emerald-50 dark:bg-emerald-950'
                  : payment.status === 'pending'
                  ? 'bg-amber-50 dark:bg-amber-950'
                  : 'bg-red-50 dark:bg-red-950'
              ]">
                <svg v-if="payment.status === 'completed' || payment.status === 'approved'" class="w-5 h-5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                <svg v-else-if="payment.status === 'pending'" class="w-5 h-5 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <svg v-else class="w-5 h-5 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
              </div>

              <!-- Detalles -->
              <div class="flex-1 min-w-0">
                <p class="text-sm font-semibold text-gray-900 dark:text-white">
                  Plan {{ capitalizeFirstLetter(payment.plan) }}
                </p>
                <p class="text-xs text-gray-500 dark:text-zinc-400 mt-0.5">
                  {{ formatDate(payment.created_at) }} • {{ formatFrequency(payment.payment_frequency) }}
                </p>
              </div>
            </div>
          </div>

          <!-- Monto y Estado -->
          <div class="ml-4 flex items-end flex-col">
            <p class="text-sm font-bold text-gray-900 dark:text-white">
              {{ formatCurrency(payment.amount_in_cents / 100) }}
            </p>
            <span :class="[
              'text-xs font-medium mt-1 px-2 py-1 rounded-full',
              (payment.status === 'completed' || payment.status === 'approved')
                ? 'bg-emerald-50 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-400'
                : payment.status === 'pending'
                ? 'bg-amber-50 dark:bg-amber-950 text-amber-700 dark:text-amber-400'
                : 'bg-red-50 dark:bg-red-950 text-red-700 dark:text-red-400'
            ]">
              {{ payment.status === 'approved' ? 'Aprobado' : capitalizeFirstLetter(payment.status) }}
            </span>
          </div>
        </div>
      </template>

      <!-- Sin Pagos -->
      <div v-else class="px-6 py-8 text-center">
        <svg class="w-12 h-12 text-gray-300 dark:text-zinc-700 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <p class="text-sm text-gray-500 dark:text-zinc-400">No hay pagos registrados</p>
      </div>
    </div>

    <!-- Nota Informativa -->
    <div class="px-6 py-4 bg-blue-50 dark:bg-blue-950/30 border-t border-gray-200 dark:border-zinc-800">
      <p class="text-xs text-blue-700 dark:text-blue-400">
        ℹ️ El historial muestra todos tus pagos de plan. Para más información, contacta a soporte.
      </p>
    </div>
  </div>
</template>

<script setup>
import { onMounted, ref } from 'vue'
import { appStore } from '../store/appStore'
import apiClient from '../services/apiClient'

const payments = ref([])
const loading = ref(true)

onMounted(async () => {
  try {
    if (!appStore.tenant?.id) {
      console.warn('No tenant ID found')
      return
    }

    // Obtener historial de pagos del backend usando apiClient configurado
    const response = await apiClient.get(`/payment-history/${appStore.tenant.id}`)
    
    if (response.data.success) {
      payments.value = response.data.data || []
    }
  } catch (error) {
    console.error('Error cargando historial de pagos:', error)
    payments.value = []
  } finally {
    loading.value = false
  }
})

// Utilidades
const formatDate = (dateString) => {
  const date = new Date(dateString)
  return new Intl.DateTimeFormat('es-ES', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  }).format(date)
}

const formatCurrency = (value) => {
  return new Intl.NumberFormat('es-CO', {
    style: 'currency',
    currency: 'COP',
    minimumFractionDigits: 0,
    maximumFractionDigits: 0
  }).format(value)
}

const formatFrequency = (frequency) => {
  const map = {
    'monthly': 'Mensual',
    'yearly': 'Anual',
    '24months': '24 Meses'
  }
  return map[frequency] || frequency
}

const capitalizeFirstLetter = (str) => {
  if (!str) return ''
  return str.charAt(0).toUpperCase() + str.slice(1)
}
</script>

<style scoped>
</style>
