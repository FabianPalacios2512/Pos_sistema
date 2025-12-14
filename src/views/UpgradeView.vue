<template>
  <!-- Fondo con gradiente oficial -->
  <div class="min-h-screen font-sans bg-gradient-to-b from-gray-50 via-gray-100 to-gray-200 dark:bg-gradient-to-b dark:from-[#141417] dark:via-slate-900 dark:to-[#0a0a0c] transition-colors duration-300 px-8">
    <div class="p-4 lg:p-6 space-y-6 pb-8 max-w-6xl mx-auto">
      
      <!-- Header -->
      <div class="text-center pt-8 pb-4">
        <div class="inline-flex items-center justify-center w-20 h-20 rounded-2xl bg-gradient-to-br from-rose-500 to-orange-500 mb-4 shadow-xl">
          <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
          </svg>
        </div>
        <h1 class="text-4xl font-black text-gray-900 dark:text-white mb-2">
          Actualiza tu Plan
        </h1>
        <p class="text-lg text-gray-600 dark:text-zinc-400">
          Desbloquea todas las funciones y lleva tu negocio al siguiente nivel
        </p>
      </div>

      <!-- Estado del Trial (si aplica) -->
      <div
        v-if="trialInfo.isTrial && !trialInfo.expired"
        class="bg-gradient-to-r from-amber-50 to-orange-50 dark:from-amber-900/20 dark:to-orange-900/20 rounded-2xl p-6 border-2 border-amber-200 dark:border-amber-800 shadow-lg"
      >
        <div class="flex items-center gap-4">
          <div class="flex-shrink-0 w-12 h-12 rounded-xl bg-amber-500 flex items-center justify-center">
            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
          </div>
          <div class="flex-1">
            <p class="text-sm font-medium text-amber-800 dark:text-amber-300">
              Tu prueba gratis termina en {{ trialInfo.daysRemaining }} {{ trialInfo.daysRemaining === 1 ? 'día' : 'días' }}
            </p>
            <p class="text-xs text-amber-600 dark:text-amber-400 mt-1">
              Actualiza ahora y no pierdas acceso a tu información
            </p>
          </div>
        </div>
      </div>

      <!-- Trial Expirado -->
      <div
        v-else-if="trialInfo.isTrial && trialInfo.expired"
        class="bg-gradient-to-r from-rose-50 to-red-50 dark:from-rose-900/20 dark:to-red-900/20 rounded-2xl p-6 border-2 border-rose-200 dark:border-rose-800 shadow-lg"
      >
        <div class="flex items-center gap-4">
          <div class="flex-shrink-0 w-12 h-12 rounded-xl bg-rose-500 flex items-center justify-center">
            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
          </div>
          <div class="flex-1">
            <p class="text-sm font-bold text-rose-800 dark:text-rose-300">
              Tu periodo de prueba ha finalizado
            </p>
            <p class="text-xs text-rose-600 dark:text-rose-400 mt-1">
              Selecciona un plan para recuperar el acceso completo a tu sistema
            </p>
          </div>
        </div>
      </div>

      <!-- Planes de Precios -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-8">
        
        <!-- Plan Emprendedor -->
        <div class="bg-white dark:bg-zinc-900 rounded-2xl shadow-xl dark:shadow-black/50 border border-gray-300 dark:border-zinc-800 p-8 hover:scale-105 transition-transform duration-300">
          <div class="text-center">
            <h3 class="text-2xl font-black text-gray-900 dark:text-white mb-2">
              Emprendedor
            </h3>
            <div class="flex items-end justify-center gap-1 mb-4">
              <span class="text-5xl font-black text-slate-900 dark:text-white">
                $25,000
              </span>
              <span class="text-sm text-gray-600 dark:text-zinc-400 mb-2">
                /mes
              </span>
            </div>
            <p class="text-sm text-gray-600 dark:text-zinc-400 mb-6">
              Perfecto para iniciar tu negocio
            </p>
          </div>

          <!-- Features -->
          <ul class="space-y-3 mb-8">
            <li class="flex items-start gap-2">
              <svg class="w-5 h-5 text-emerald-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
              </svg>
              <span class="text-sm text-gray-700 dark:text-zinc-300">1 usuario</span>
            </li>
            <li class="flex items-start gap-2">
              <svg class="w-5 h-5 text-emerald-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
              </svg>
              <span class="text-sm text-gray-700 dark:text-zinc-300">POS completo</span>
            </li>
            <li class="flex items-start gap-2">
              <svg class="w-5 h-5 text-emerald-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
              </svg>
              <span class="text-sm text-gray-700 dark:text-zinc-300">Control de inventario</span>
            </li>
            <li class="flex items-start gap-2">
              <svg class="w-5 h-5 text-emerald-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
              </svg>
              <span class="text-sm text-gray-700 dark:text-zinc-300">Reportes básicos</span>
            </li>
          </ul>

          <!-- CTA Button -->
          <button 
            @click="selectPlan('emprendedor')"
            class="w-full px-6 py-3.5 bg-slate-900 dark:bg-slate-700 hover:bg-black dark:hover:bg-slate-600 text-white text-sm font-bold rounded-xl shadow-lg shadow-slate-400/40 dark:shadow-slate-900/50 transition-all duration-300"
          >
            Seleccionar Plan
          </button>
        </div>

        <!-- Plan Negocio Pro -->
        <div class="bg-white dark:bg-zinc-900 rounded-2xl shadow-xl dark:shadow-black/50 border-2 border-blue-500 dark:border-blue-400 p-8 hover:scale-105 transition-transform duration-300 relative">
          <!-- Badge Recomendado -->
          <div class="absolute -top-4 left-1/2 transform -translate-x-1/2">
            <span class="px-4 py-1 bg-gradient-to-r from-blue-500 to-indigo-500 text-white text-xs font-black rounded-full shadow-lg">
              RECOMENDADO
            </span>
          </div>

          <div class="text-center">
            <h3 class="text-2xl font-black text-gray-900 dark:text-white mb-2">
              Negocio Pro
            </h3>
            <div class="flex items-end justify-center gap-1 mb-4">
              <span class="text-5xl font-black text-slate-900 dark:text-white">
                $50,000
              </span>
              <span class="text-sm text-gray-600 dark:text-zinc-400 mb-2">
                /mes
              </span>
            </div>
            <p class="text-sm text-gray-600 dark:text-zinc-400 mb-6">
              Para negocios en crecimiento
            </p>
          </div>

          <!-- Features -->
          <ul class="space-y-3 mb-8">
            <li class="flex items-start gap-2">
              <svg class="w-5 h-5 text-emerald-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
              </svg>
              <span class="text-sm text-gray-700 dark:text-zinc-300">Usuarios ilimitados</span>
            </li>
            <li class="flex items-start gap-2">
              <svg class="w-5 h-5 text-emerald-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
              </svg>
              <span class="text-sm text-gray-700 dark:text-zinc-300">Multi-sede</span>
            </li>
            <li class="flex items-start gap-2">
              <svg class="w-5 h-5 text-emerald-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
              </svg>
              <span class="text-sm text-gray-700 dark:text-zinc-300">IA integrada</span>
            </li>
            <li class="flex items-start gap-2">
              <svg class="w-5 h-5 text-emerald-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
              </svg>
              <span class="text-sm text-gray-700 dark:text-zinc-300">Facturación electrónica (+$15k)</span>
            </li>
          </ul>

          <!-- CTA Button -->
          <button 
            @click="selectPlan('negocio_pro')"
            class="w-full px-6 py-3.5 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white text-sm font-bold rounded-xl shadow-lg shadow-blue-400/40 dark:shadow-blue-900/50 transition-all duration-300"
          >
            Seleccionar Plan
          </button>
        </div>

      </div>

      <!-- Mensaje de ayuda -->
      <div class="text-center mt-8">
        <p class="text-sm text-gray-600 dark:text-zinc-400">
          ¿Tienes preguntas? 
          <a href="mailto:soporte@sistemapos.com" class="text-blue-600 dark:text-blue-400 hover:underline font-medium">
            Contáctanos
          </a>
        </p>
      </div>

    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import apiClient from '@/services/apiClient.js'

// Estado del trial
const trialInfo = ref({
  isTrial: false,
  expired: false,
  daysRemaining: 0,
})

// Cargar info del trial
const loadTrialStatus = async () => {
  try {
    const response = await apiClient.get('/check-trial-status')
    trialInfo.value = {
      isTrial: response.data.is_trial,
      expired: response.data.trial_expired,
      daysRemaining: response.data.days_remaining ?? 0,
    }
  } catch (error) {
    console.error('Error al cargar estado del trial:', error)
  }
}

// Seleccionar plan (placeholder - aquí iría integración con pasarela de pago)
const selectPlan = (plan) => {
  alert(`Plan seleccionado: ${plan}\n\nIntegración con pasarela de pago pendiente.`)
  // TODO: Integrar con Stripe, PayU, Wompi, etc.
}

// Cargar al montar
onMounted(() => {
  loadTrialStatus()
})
</script>
