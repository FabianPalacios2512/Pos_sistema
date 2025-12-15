<template>
  <!-- Modal Overlay -->
  <transition
    enter-active-class="transition ease-out duration-300"
    enter-from-class="opacity-0"
    enter-to-class="opacity-100"
    leave-active-class="transition ease-in duration-200"
    leave-from-class="opacity-100"
    leave-to-class="opacity-0"
  >
    <div 
      v-if="isOpen" 
      @click="closeModal"
      class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm px-4 py-8 overflow-y-auto"
    >
      <transition
        enter-active-class="transition ease-out duration-300"
        enter-from-class="scale-95 opacity-0"
        enter-to-class="scale-100 opacity-100"
        leave-active-class="transition ease-in duration-200"
        leave-from-class="scale-100 opacity-100"
        leave-to-class="scale-95 opacity-0"
      >
        <div @click.stop class="bg-white dark:bg-zinc-900 rounded-2xl shadow-2xl w-full max-w-6xl my-auto max-h-[90vh] overflow-y-auto">
          
          <!-- Header -->
          <div class="flex items-center justify-between px-8 py-6 border-b border-gray-200 dark:border-zinc-800 sticky top-0 bg-white dark:bg-zinc-900">
            <div>
              <h2 class="text-3xl font-bold text-gray-900 dark:text-white">Actualiza tu Plan</h2>
              <p class="text-sm text-gray-600 dark:text-zinc-400 mt-1">
                Desbloquea todas las funciones y lleva tu negocio al siguiente nivel
              </p>
            </div>
            <button 
              @click="closeModal"
              class="p-2 text-gray-500 hover:text-gray-700 dark:text-zinc-400 dark:hover:text-zinc-200 rounded-lg hover:bg-gray-100 dark:hover:bg-zinc-800 transition-colors"
            >
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
              </svg>
            </button>
          </div>

          <!-- Content -->
          <div class="p-8 space-y-8">
            
            <!-- Current Plan Info -->
            <div v-if="currentPlan" class="p-4 bg-blue-50 dark:bg-blue-950 border border-blue-200 dark:border-blue-800 rounded-xl">
              <div class="flex items-center gap-3">
                <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <div>
                  <p class="text-sm font-semibold text-blue-900 dark:text-blue-100">Plan Actual: <span class="capitalize">{{ currentPlan }}</span></p>
                  <p v-if="subscriptionEndsAt" class="text-xs text-blue-700 dark:text-blue-300 mt-1">
                    Vence: {{ formatDate(subscriptionEndsAt) }}
                  </p>
                </div>
              </div>
            </div>

            <!-- Payment Frequency Selector -->
            <div class="flex flex-col items-center gap-4">
              <div class="relative">
                <select 
                  v-model="paymentFrequency"
                  class="px-8 py-3.5 text-base font-semibold bg-white dark:bg-zinc-800 border border-slate-300 dark:border-zinc-700 rounded-xl appearance-none pr-14 cursor-pointer hover:border-slate-400 dark:hover:border-zinc-600 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 text-slate-900 dark:text-zinc-200"
                >
                  <option value="monthly">Mensual</option>
                  <option value="yearly">Anual (Ahorra 20%)</option>
                  <option value="24months">24 Meses (2 meses gratis)</option>
                </select>
                <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none">
                  <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                  </svg>
                </div>
              </div>
            </div>

            <!-- Plans Grid -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
              
              <!-- Basic Plan -->
              <div 
                @click="selectedPlan = 'basic'"
                v-if="canUpgradeTo('basic')"
                class="relative bg-white dark:bg-zinc-800 rounded-2xl border-2 shadow-sm transition-all duration-300 cursor-pointer p-6 hover:shadow-lg hover:-translate-y-1"
                :class="selectedPlan === 'basic' ? 'border-emerald-500 ring-2 ring-emerald-500/20' : 'border-slate-200 dark:border-zinc-700'"
              >
                <!-- Radio Button -->
                <div class="absolute top-4 right-4 w-5 h-5 rounded-full border-2 flex items-center justify-center transition-all"
                     :class="selectedPlan === 'basic' ? 'border-emerald-500 bg-emerald-500' : 'border-slate-300 dark:border-zinc-600'">
                  <svg v-if="selectedPlan === 'basic'" class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path>
                  </svg>
                </div>

                <div>
                  <!-- Title -->
                  <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-2">Basic</h3>
                  <p class="text-sm text-slate-600 dark:text-zinc-400 mb-4">Para negocios que inician y quieren orden.</p>

                  <!-- Price -->
                  <div class="mb-4">
                    <div class="flex items-baseline gap-1 mb-1">
                      <span class="text-4xl font-bold text-slate-900 dark:text-white">
                        {{ paymentFrequency === '24months' ? '$20.000' : (paymentFrequency === 'yearly' ? '$20.000' : '$25.000') }}
                      </span>
                      <span class="text-sm text-slate-500 dark:text-zinc-400">/mes</span>
                    </div>
                    <p class="text-xs text-emerald-600 dark:text-emerald-400 font-medium">
                      {{ paymentFrequency === '24months' ? 'Facturado $480.000 + 2 meses GRATIS' : (paymentFrequency === 'yearly' ? 'Facturado $240.000 (ahorra $60k)' : 'Facturado mensualmente') }}
                    </p>
                  </div>

                  <!-- Button -->
                  <button
                    @click.stop="selectedPlan = 'basic'"
                    class="w-full h-11 px-4 text-sm font-semibold rounded-xl transition-all duration-200 mb-4 bg-emerald-600 hover:bg-emerald-700 text-white shadow-sm"
                  >
                    Seleccionar
                  </button>

                  <!-- Features -->
                  <div class="space-y-3 text-sm">
                    <div class="flex items-start gap-2 text-slate-700 dark:text-zinc-300">
                      <svg class="w-4 h-4 text-emerald-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                      </svg>
                      <span>1 Usuario Administrador</span>
                    </div>
                    <div class="flex items-start gap-2 text-slate-700 dark:text-zinc-300">
                      <svg class="w-4 h-4 text-emerald-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                      </svg>
                      <span>POS Web + Inventario</span>
                    </div>
                    <div class="flex items-start gap-2 text-slate-700 dark:text-zinc-300">
                      <svg class="w-4 h-4 text-emerald-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                      </svg>
                      <span>Soporte por Email</span>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Premium Plan (Most Popular) -->
              <div 
                @click="selectedPlan = 'premium'"
                v-if="canUpgradeTo('premium')"
                class="relative bg-white dark:bg-zinc-800 rounded-2xl border-2 border-emerald-500 shadow-md transition-all duration-300 cursor-pointer p-6 hover:shadow-lg hover:-translate-y-1 ring-2 ring-emerald-500/20"
              >
                <!-- Badge -->
                <div class="absolute -top-3 left-6">
                  <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-emerald-100 dark:bg-emerald-900 text-emerald-700 dark:text-emerald-300 text-xs font-bold uppercase tracking-wide rounded-full border border-emerald-200 dark:border-emerald-800">
                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                      <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                    </svg>
                    Más Vendido
                  </span>
                </div>

                <!-- Radio Button -->
                <div class="absolute top-4 right-4 w-5 h-5 rounded-full border-2 flex items-center justify-center transition-all"
                     :class="selectedPlan === 'premium' ? 'border-emerald-500 bg-emerald-500' : 'border-slate-300 dark:border-zinc-600'">
                  <svg v-if="selectedPlan === 'premium'" class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path>
                  </svg>
                </div>

                <div class="pt-4">
                  <!-- Title -->
                  <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-2">Premium</h3>
                  <p class="text-sm text-slate-600 dark:text-zinc-400 mb-4">Automatización total + IA para crecer.</p>

                  <!-- Price -->
                  <div class="mb-4">
                    <div class="flex items-baseline gap-1 mb-1">
                      <span class="text-4xl font-bold text-slate-900 dark:text-white">
                        {{ includeDianInvoicing 
                          ? (paymentFrequency === '24months' ? '$80.000' : (paymentFrequency === 'yearly' ? '$80.000' : '$90.000'))
                          : (paymentFrequency === '24months' ? '$50.000' : (paymentFrequency === 'yearly' ? '$50.000' : '$60.000')) 
                        }}
                      </span>
                      <span class="text-sm text-slate-500 dark:text-zinc-400">/mes</span>
                    </div>
                    <p class="text-xs text-emerald-600 dark:text-emerald-400 font-medium">
                      {{ paymentFrequency === '24months' 
                        ? (includeDianInvoicing ? 'Facturado $1.920.000 + DIAN + 2 meses GRATIS' : 'Facturado $1.200.000 + 2 meses GRATIS')
                        : (paymentFrequency === 'yearly' 
                          ? (includeDianInvoicing ? 'Facturado $960.000 + DIAN (ahorra $120k)' : 'Facturado $600.000 (ahorra $120k)')
                          : 'Facturado mensualmente'
                        )
                      }}
                    </p>
                  </div>

                  <!-- DIAN Checkbox -->
                  <div class="mb-4 p-3 bg-slate-50 dark:bg-zinc-700 border border-slate-200 dark:border-zinc-600 rounded-lg">
                    <label class="flex items-start gap-2.5 cursor-pointer">
                      <input 
                        type="checkbox" 
                        v-model="includeDianInvoicing"
                        class="w-4 h-4 text-emerald-600 border-slate-300 dark:border-zinc-600 rounded focus:ring-emerald-500 mt-0.5"
                      >
                      <div class="flex-1 text-sm">
                        <span class="font-medium text-slate-900 dark:text-white">Incluir Facturación DIAN</span>
                        <span class="text-emerald-600 dark:text-emerald-400 font-bold ml-1.5">+$30.000</span>
                      </div>
                    </label>
                  </div>

                  <!-- Button -->
                  <button
                    @click.stop="selectedPlan = 'premium'"
                    class="w-full h-11 px-4 text-sm font-semibold rounded-xl transition-all duration-200 mb-4 bg-emerald-600 hover:bg-emerald-700 text-white shadow-sm"
                  >
                    Seleccionar
                  </button>

                  <!-- Features -->
                  <div class="space-y-3 text-sm">
                    <p class="font-bold text-slate-900 dark:text-white">Todo lo de Basic, más:</p>
                    <div class="flex items-start gap-2 text-slate-700 dark:text-zinc-300">
                      <svg class="w-4 h-4 text-emerald-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                      </svg>
                      <span>3 Usuarios / 2 Bodegas</span>
                    </div>
                    <div class="flex items-start gap-2 text-slate-700 dark:text-zinc-300">
                      <svg class="w-4 h-4 text-emerald-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                      </svg>
                      <span>Agente IA (Crea Promos)</span>
                    </div>
                    <div class="flex items-start gap-2 text-slate-700 dark:text-zinc-300">
                      <svg class="w-4 h-4 text-emerald-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                      </svg>
                      <span>Sistema de Puntos (Fidelización)</span>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Enterprise Plan -->
              <div 
                @click="selectedPlan = 'enterprise'"
                v-if="canUpgradeTo('enterprise')"
                class="relative bg-white dark:bg-zinc-800 rounded-2xl border-2 shadow-sm transition-all duration-300 cursor-pointer p-6 hover:shadow-lg hover:-translate-y-1"
                :class="selectedPlan === 'enterprise' ? 'border-emerald-500 ring-2 ring-emerald-500/20' : 'border-slate-200 dark:border-zinc-700'"
              >
                <!-- Radio Button -->
                <div class="absolute top-4 right-4 w-5 h-5 rounded-full border-2 flex items-center justify-center transition-all"
                     :class="selectedPlan === 'enterprise' ? 'border-emerald-500 bg-emerald-500' : 'border-slate-300 dark:border-zinc-600'">
                  <svg v-if="selectedPlan === 'enterprise'" class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path>
                  </svg>
                </div>

                <div>
                  <!-- Title -->
                  <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-2">Enterprise</h3>
                  <p class="text-sm text-slate-600 dark:text-zinc-400 mb-4">Solución empresarial completa.</p>

                  <!-- Price -->
                  <div class="mb-4">
                    <div class="flex items-baseline gap-1 mb-1">
                      <span class="text-4xl font-bold text-slate-900 dark:text-white">
                        {{ paymentFrequency === '24months' ? '$120.000' : (paymentFrequency === 'yearly' ? '$120.000' : '$150.000') }}
                      </span>
                      <span class="text-sm text-slate-500 dark:text-zinc-400">/mes</span>
                    </div>
                    <p class="text-xs text-emerald-600 dark:text-emerald-400 font-medium">
                      {{ paymentFrequency === '24months' ? 'Facturado $2.880.000 + 2 meses GRATIS' : (paymentFrequency === 'yearly' ? 'Facturado $1.440.000 (ahorra $360k)' : 'Facturado mensualmente') }}
                    </p>
                  </div>

                  <!-- Button -->
                  <button
                    @click.stop="selectedPlan = 'enterprise'"
                    class="w-full h-11 px-4 text-sm font-semibold rounded-xl transition-all duration-200 mb-4 bg-emerald-600 hover:bg-emerald-700 text-white shadow-sm"
                  >
                    Seleccionar
                  </button>

                  <!-- Features -->
                  <div class="space-y-3 text-sm">
                    <p class="font-bold text-slate-900 dark:text-white">Todo lo de Premium, más:</p>
                    <div class="flex items-start gap-2 text-slate-700 dark:text-zinc-300">
                      <svg class="w-4 h-4 text-emerald-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                      </svg>
                      <span>Usuarios ilimitados</span>
                    </div>
                    <div class="flex items-start gap-2 text-slate-700 dark:text-zinc-300">
                      <svg class="w-4 h-4 text-emerald-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                      </svg>
                      <span>Multi-Sede / Multi-Caja</span>
                    </div>
                    <div class="flex items-start gap-2 text-slate-700 dark:text-zinc-300">
                      <svg class="w-4 h-4 text-emerald-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                      </svg>
                      <span>SLA garantizado 99.9%</span>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Terms Checkbox -->
            <div class="p-4 bg-slate-50 dark:bg-zinc-800 border border-slate-200 dark:border-zinc-700 rounded-lg">
              <label class="flex items-start gap-3 cursor-pointer">
                <input 
                  type="checkbox" 
                  v-model="agreedToTerms"
                  class="w-4 h-4 text-emerald-600 border-slate-300 dark:border-zinc-600 rounded focus:ring-emerald-500 mt-1"
                >
                <div class="text-sm text-slate-700 dark:text-zinc-300">
                  Autorizo el pago de <strong>${{ selectedPlanPrice }}</strong> ahora, seguidos de pagos mensuales de 
                  <strong>${{ selectedPlanMonthlyPrice }}</strong>. Entiendo que puedo cancelar en cualquier momento.
                </div>
              </label>
            </div>

            <!-- Action Buttons -->
            <div class="flex gap-4">
              <button
                @click="closeModal"
                :disabled="isProcessing"
                class="flex-1 h-12 px-6 text-sm font-semibold rounded-xl transition-all duration-200 bg-white dark:bg-zinc-800 hover:bg-slate-50 dark:hover:bg-zinc-700 text-slate-600 dark:text-zinc-300 border border-slate-200 dark:border-zinc-700 disabled:opacity-50"
              >
                Cancelar
              </button>
              <button
                @click="handlePlanSelection(selectedPlan)"
                :disabled="!selectedPlan || !agreedToTerms || isProcessing"
                class="flex-1 h-12 px-6 text-sm font-semibold rounded-xl transition-all duration-200 bg-emerald-600 hover:bg-emerald-700 text-white shadow-sm disabled:opacity-50"
              >
                {{ isProcessing ? 'Procesando...' : 'Procesar Pago' }}
              </button>
            </div>
          </div>
        </div>
      </transition>
    </div>
  </transition>
</template>

<script setup>
import { ref, computed } from 'vue'
import { appStore } from '../store/appStore.js'
import axios from 'axios'

const props = defineProps({
  isOpen: {
    type: Boolean,
    default: false
  }
})

const emit = defineEmits(['close', 'success'])

// State
const selectedPlan = ref(null)
const agreedToTerms = ref(false)
const isProcessing = ref(false)
const paymentFrequency = ref('monthly')
const includeDianInvoicing = ref(false)

// Get current tenant data
const currentPlan = computed(() => appStore.tenantPlan)
const subscriptionEndsAt = computed(() => appStore.subscriptionEndDate)

// Plan hierarchy for validation
const planHierarchy = {
  'trial_express': 0,
  'basic': 1,
  'premium': 2,
  'enterprise': 3
}

// Check if can upgrade to a plan
const canUpgradeTo = (plan) => {
  const currentLevel = planHierarchy[currentPlan.value] || 0
  const targetLevel = planHierarchy[plan] || 0
  return targetLevel > currentLevel
}

// Calculate selected plan price
const selectedPlanPrice = computed(() => {
  if (!selectedPlan.value) return '$0'
  
  const prices = {
    basic: {
      monthly: 25000,
      yearly: 240000,
      '24months': 480000
    },
    premium: {
      monthly: includeDianInvoicing.value ? 90000 : 60000,
      yearly: includeDianInvoicing.value ? 960000 : 600000,
      '24months': includeDianInvoicing.value ? 1920000 : 1200000
    },
    enterprise: {
      monthly: 150000,
      yearly: 1440000,
      '24months': 2880000
    }
  }
  
  const price = prices[selectedPlan.value]?.[paymentFrequency.value] || 0
  return `$${price.toLocaleString('es-CO').replace(/\./g, '.')}`
})

// Calculate monthly price for terms
const selectedPlanMonthlyPrice = computed(() => {
  if (!selectedPlan.value) return '$0'
  
  const prices = {
    basic: 25000,
    premium: includeDianInvoicing.value ? 90000 : 60000,
    enterprise: 150000
  }
  
  const price = prices[selectedPlan.value] || 0
  return `$${price.toLocaleString('es-CO').replace(/\./g, '.')}`
})

// Format date
const formatDate = (dateStr) => {
  if (!dateStr) return ''
  const date = new Date(dateStr)
  return date.toLocaleDateString('es-CO', { year: 'numeric', month: 'long', day: 'numeric' })
}

// Close modal
const closeModal = () => {
  selectedPlan.value = null
  agreedToTerms.value = false
  paymentFrequency.value = 'monthly'
  includeDianInvoicing.value = false
  emit('close')
}

// Handle plan selection and payment
const handlePlanSelection = async (plan) => {
  if (isProcessing.value || !plan || !agreedToTerms.value) return
  
  try {
    isProcessing.value = true
    
    // Get tenant ID from authenticated context
    const tenantId = appStore.tenant?.id
    if (!tenantId) {
      throw new Error('No se pudo obtener el ID del negocio. Por favor, recarga la página.')
    }
    
    // Calculate price
    const prices = {
      basic: {
        monthly: 25000,
        yearly: 240000,
        '24months': 480000
      },
      premium: {
        monthly: includeDianInvoicing.value ? 90000 : 60000,
        yearly: includeDianInvoicing.value ? 960000 : 600000,
        '24months': includeDianInvoicing.value ? 1920000 : 1200000
      },
      enterprise: {
        monthly: 150000,
        yearly: 1440000,
        '24months': 2880000
      }
    }
    
    const finalPrice = prices[plan]?.[paymentFrequency.value] || 0
    const reference = `upgrade_${tenantId}_${Date.now()}`
    
    // 🔥 IMPORTANTE: Pasar tenant_id en la URL para que PaymentSuccess pueda procesarlo
    // Wompi redirige desde su servidor, el navegador pierde el contexto de auth
    const redirectUrl = getRedirectUrl(plan, reference, tenantId)
    
    // Create payment link with Wompi
    const response = await axios.post('/api/create-payment-link', {
      amount_in_cents: finalPrice * 100,
      reference: reference,
      customer_email: appStore.userEmail || 'cliente@105pos.pro',
      description: `Plan upgrade a ${plan}`,
      redirect_url: redirectUrl,
      payment_frequency: paymentFrequency.value,
      plan: plan,
      tenant_id: tenantId,
      is_upgrade: true
    })
    
    if (response.data.success && response.data.payment_link_url) {
      // Store upgrade data in localStorage - INCLUYENDO payment_frequency
      const upgradeData = {
        reference: reference,
        plan: plan,
        tenant_id: tenantId,
        amount: finalPrice,
        payment_frequency: paymentFrequency.value,
        is_upgrade: true,  // Flag importante para diferenciar en PaymentSuccess
        dianInvoicing: includeDianInvoicing.value
      }
      
      localStorage.setItem('pending_upgrade', JSON.stringify(upgradeData))
      
      console.log('UpgradePlanModal - Upgrade pendiente guardado:', upgradeData)
      
      // Redirect to Wompi payment page
      window.location.href = response.data.payment_link_url
    } else {
      throw new Error('No se pudo generar el link de pago')
    }
    
  } catch (error) {
    console.error('Error processing payment:', error)
    const errorMessage = error.response?.data?.error || error.message || 'Por favor, intenta nuevamente.'
    alert('❌ Error al procesar el pago\n\n' + errorMessage)
  } finally {
    isProcessing.value = false
  }
}

// Get redirect URL based on environment
const getRedirectUrl = (plan, reference, tenantId) => {
  // 🔥 CRÍTICO: Detectar si estamos en localhost o en producción
  // window.location.hostname puede ser: localhost, 127.0.0.1, sdsdsdsdf.localhost, 105pos.pro, subdomain.105pos.pro
  const hostname = window.location.hostname
  const isLocalhost = hostname === 'localhost' || hostname === '127.0.0.1' || hostname.endsWith('.localhost')
  
  let baseUrl
  if (isLocalhost) {
    // En localhost: redirigir a http://localhost:3000
    // (Sin subdominio, porque el backend está en un puerto diferente)
    baseUrl = 'http://localhost:3000'
  } else {
    // En producción: redirigir a https://105pos.pro (sin subdominio)
    baseUrl = 'https://105pos.pro'
  }
  
  console.log('🔗 getRedirectUrl - Detecting environment:', {
    hostname: hostname,
    isLocalhost: isLocalhost,
    baseUrl: baseUrl,
  })
  
  // 🔥 IMPORTANTE: Incluir tenant_id en la URL para que PaymentSuccess pueda usarlo
  // Wompi redirige desde su servidor, el navegador pierde contexto de auth
  return `${baseUrl}/payment/success?plan=${plan}&reference=${reference}&is_upgrade=true&tenant_id=${tenantId}`
}
</script>
