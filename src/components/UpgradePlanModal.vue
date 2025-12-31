<template>
  <!-- Modal Overlay -->
  <Teleport to="body">
    <Transition
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
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-md px-4 py-6 overflow-y-auto"
      >
        <Transition
          enter-active-class="transition ease-out duration-300"
          enter-from-class="scale-95 opacity-0 translate-y-4"
          enter-to-class="scale-100 opacity-100 translate-y-0"
          leave-active-class="transition ease-in duration-200"
          leave-from-class="scale-100 opacity-100 translate-y-0"
          leave-to-class="scale-95 opacity-0 translate-y-4"
        >
          <div 
            v-if="isOpen"
            @click.stop 
            class="bg-white dark:bg-[#0f0f12] rounded-3xl shadow-2xl w-full max-w-5xl my-auto overflow-hidden border border-gray-200/50 dark:border-zinc-800/50"
          >
            
            <!-- Header con gradiente sutil -->
            <div class="relative px-8 py-6 border-b border-gray-100 dark:border-zinc-800/50">
              <!-- Gradiente decorativo -->
              <div class="absolute inset-0 bg-gradient-to-r from-emerald-500/5 via-teal-500/5 to-cyan-500/5 dark:from-emerald-500/10 dark:via-teal-500/5 dark:to-transparent"></div>
              
              <div class="relative flex items-center justify-between">
                <div class="flex items-center gap-4">
                  <!-- Icono animado -->
                  <div class="relative">
                    <div class="absolute inset-0 bg-emerald-400/20 rounded-2xl blur-xl animate-pulse"></div>
                    <div class="relative w-14 h-14 bg-gradient-to-br from-emerald-500 to-teal-600 rounded-2xl flex items-center justify-center shadow-lg shadow-emerald-500/25">
                      <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z" />
                      </svg>
                    </div>
                  </div>
                  <div>
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Potencia tu negocio</h2>
                    <p class="text-sm text-gray-500 dark:text-zinc-400 mt-0.5">
                      Elige el plan perfecto para tu crecimiento
                    </p>
                  </div>
                </div>
                
                <button 
                  @click="closeModal"
                  class="p-2.5 text-gray-400 hover:text-gray-600 dark:text-zinc-500 dark:hover:text-zinc-300 rounded-xl hover:bg-gray-100 dark:hover:bg-zinc-800 transition-all duration-200"
                >
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path>
                  </svg>
                </button>
              </div>
            </div>

            <!-- Content -->
            <div class="p-6 space-y-6 max-h-[70vh] overflow-y-auto">
              
              <!-- Plan Actual Badge -->
              <div v-if="currentPlan" class="flex items-center gap-3 p-3 bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-950/30 dark:to-indigo-950/30 border border-blue-100 dark:border-blue-900/50 rounded-2xl">
                <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/50 rounded-xl flex items-center justify-center">
                  <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                  </svg>
                </div>
                <div class="flex-1">
                  <p class="text-sm font-semibold text-blue-900 dark:text-blue-100">
                    Plan actual: <span class="capitalize bg-blue-100 dark:bg-blue-900/50 px-2 py-0.5 rounded-lg">{{ currentPlan }}</span>
                  </p>
                  <p v-if="subscriptionEndsAt" class="text-xs text-blue-600 dark:text-blue-400 mt-0.5">
                    Vence: {{ formatDate(subscriptionEndsAt) }}
                  </p>
                </div>
              </div>

              <!-- Selector de Frecuencia - Estilo Toggle Moderno -->
              <div class="flex justify-center">
                <div class="inline-flex p-1.5 bg-gray-100 dark:bg-zinc-800/80 rounded-2xl gap-1">
                  <button
                    @click="paymentFrequency = 'monthly'"
                    :class="[
                      'px-5 py-2.5 text-sm font-semibold rounded-xl transition-all duration-200',
                      paymentFrequency === 'monthly' 
                        ? 'bg-white dark:bg-zinc-700 text-gray-900 dark:text-white shadow-sm' 
                        : 'text-gray-500 dark:text-zinc-400 hover:text-gray-700 dark:hover:text-zinc-200'
                    ]"
                  >
                    Mensual
                  </button>
                  <button
                    @click="paymentFrequency = 'yearly'"
                    :class="[
                      'px-5 py-2.5 text-sm font-semibold rounded-xl transition-all duration-200 flex items-center gap-2',
                      paymentFrequency === 'yearly' 
                        ? 'bg-white dark:bg-zinc-700 text-gray-900 dark:text-white shadow-sm' 
                        : 'text-gray-500 dark:text-zinc-400 hover:text-gray-700 dark:hover:text-zinc-200'
                    ]"
                  >
                    Anual
                    <span class="text-[10px] font-bold bg-emerald-100 dark:bg-emerald-900/50 text-emerald-700 dark:text-emerald-400 px-1.5 py-0.5 rounded-md">-20%</span>
                  </button>
                  <button
                    @click="paymentFrequency = '24months'"
                    :class="[
                      'px-5 py-2.5 text-sm font-semibold rounded-xl transition-all duration-200 flex items-center gap-2',
                      paymentFrequency === '24months' 
                        ? 'bg-white dark:bg-zinc-700 text-gray-900 dark:text-white shadow-sm' 
                        : 'text-gray-500 dark:text-zinc-400 hover:text-gray-700 dark:hover:text-zinc-200'
                    ]"
                  >
                    24 Meses
                    <span class="text-[10px] font-bold bg-amber-100 dark:bg-amber-900/50 text-amber-700 dark:text-amber-400 px-1.5 py-0.5 rounded-md">+2 GRATIS</span>
                  </button>
                </div>
              </div>

              <!-- Plans Grid -->
              <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                
                <!-- Basic Plan -->
                <div 
                  v-if="canUpgradeTo('basic')"
                  @click="selectedPlan = 'basic'"
                  :class="[
                    'relative rounded-2xl border-2 transition-all duration-300 cursor-pointer overflow-hidden group',
                    selectedPlan === 'basic' 
                      ? 'border-emerald-500 bg-emerald-50/50 dark:bg-emerald-950/20 ring-4 ring-emerald-500/10' 
                      : 'border-gray-200 dark:border-zinc-800 bg-white dark:bg-zinc-900/50 hover:border-gray-300 dark:hover:border-zinc-700'
                  ]"
                >
                  <!-- Selection indicator -->
                  <div v-if="selectedPlan === 'basic'" class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-emerald-500 to-teal-500"></div>
                  
                  <div class="p-5">
                    <!-- Header -->
                    <div class="flex items-start justify-between mb-4">
                      <div>
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">Basic</h3>
                        <p class="text-xs text-gray-500 dark:text-zinc-400 mt-0.5">Para negocios que inician</p>
                      </div>
                      <!-- Radio -->
                      <div :class="[
                        'w-5 h-5 rounded-full border-2 flex items-center justify-center transition-all duration-200',
                        selectedPlan === 'basic' ? 'border-emerald-500 bg-emerald-500' : 'border-gray-300 dark:border-zinc-600'
                      ]">
                        <svg v-if="selectedPlan === 'basic'" class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path>
                        </svg>
                      </div>
                    </div>

                    <!-- Price -->
                    <div class="mb-4">
                      <div class="flex items-baseline gap-1">
                        <span class="text-3xl font-black text-gray-900 dark:text-white">
                          {{ formatPrice(getPlanPrice('basic')) }}
                        </span>
                        <span class="text-sm text-gray-400 dark:text-zinc-500">/mes</span>
                      </div>
                      <p class="text-[11px] text-emerald-600 dark:text-emerald-400 font-medium mt-1">
                        {{ getPaymentLabel('basic') }}
                      </p>
                    </div>

                    <!-- Features -->
                    <div class="space-y-2.5 pt-4 border-t border-gray-100 dark:border-zinc-800">
                      <div class="flex items-center gap-2 text-sm text-gray-600 dark:text-zinc-300">
                        <svg class="w-4 h-4 text-emerald-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                          <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                        </svg>
                        <span>1 Usuario Administrador</span>
                      </div>
                      <div class="flex items-center gap-2 text-sm text-gray-600 dark:text-zinc-300">
                        <svg class="w-4 h-4 text-emerald-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                          <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                        </svg>
                        <span>POS Web + Inventario</span>
                      </div>
                      <div class="flex items-center gap-2 text-sm text-gray-600 dark:text-zinc-300">
                        <svg class="w-4 h-4 text-emerald-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                          <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                        </svg>
                        <span>Soporte por Email</span>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Premium Plan (Popular) -->
                <div 
                  v-if="canUpgradeTo('premium')"
                  @click="selectedPlan = 'premium'"
                  :class="[
                    'relative rounded-2xl border-2 transition-all duration-300 cursor-pointer overflow-hidden',
                    selectedPlan === 'premium' 
                      ? 'border-emerald-500 bg-emerald-50/50 dark:bg-emerald-950/20 ring-4 ring-emerald-500/10' 
                      : 'border-emerald-200 dark:border-emerald-800/50 bg-gradient-to-b from-emerald-50/80 to-white dark:from-emerald-950/30 dark:to-zinc-900/50'
                  ]"
                >
                  <!-- Popular Badge -->
                  <div class="absolute -top-px left-1/2 -translate-x-1/2">
                    <div class="px-4 py-1 bg-gradient-to-r from-emerald-500 to-teal-500 text-white text-[10px] font-bold uppercase tracking-wider rounded-b-xl shadow-lg shadow-emerald-500/25">
                      ⭐ Más Popular
                    </div>
                  </div>
                  
                  <!-- Selection indicator -->
                  <div v-if="selectedPlan === 'premium'" class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-emerald-500 to-teal-500"></div>
                  
                  <div class="p-5 pt-8">
                    <!-- Header -->
                    <div class="flex items-start justify-between mb-4">
                      <div>
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">Premium</h3>
                        <p class="text-xs text-gray-500 dark:text-zinc-400 mt-0.5">IA + Automatización total</p>
                      </div>
                      <!-- Radio -->
                      <div :class="[
                        'w-5 h-5 rounded-full border-2 flex items-center justify-center transition-all duration-200',
                        selectedPlan === 'premium' ? 'border-emerald-500 bg-emerald-500' : 'border-gray-300 dark:border-zinc-600'
                      ]">
                        <svg v-if="selectedPlan === 'premium'" class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path>
                        </svg>
                      </div>
                    </div>

                    <!-- Price -->
                    <div class="mb-4">
                      <div class="flex items-baseline gap-1">
                        <span class="text-3xl font-black text-gray-900 dark:text-white">
                          {{ formatPrice(getPlanPrice('premium')) }}
                        </span>
                        <span class="text-sm text-gray-400 dark:text-zinc-500">/mes</span>
                      </div>
                      <p class="text-[11px] text-emerald-600 dark:text-emerald-400 font-medium mt-1">
                        {{ getPaymentLabel('premium') }}
                      </p>
                    </div>

                    <!-- DIAN Option -->
                    <div class="mb-4 p-3 bg-white/60 dark:bg-zinc-800/60 border border-gray-200 dark:border-zinc-700 rounded-xl">
                      <label class="flex items-center gap-3 cursor-pointer" @click.stop>
                        <input 
                          type="checkbox" 
                          v-model="includeDianInvoicing"
                          class="w-4 h-4 text-emerald-600 border-gray-300 dark:border-zinc-600 rounded focus:ring-emerald-500"
                        >
                        <div class="flex-1 flex items-center justify-between">
                          <span class="text-sm font-medium text-gray-700 dark:text-zinc-200">Facturación DIAN</span>
                          <span class="text-xs font-bold text-emerald-600 dark:text-emerald-400 bg-emerald-100 dark:bg-emerald-900/50 px-2 py-0.5 rounded-lg">+$30k</span>
                        </div>
                      </label>
                    </div>

                    <!-- Features -->
                    <div class="space-y-2.5 pt-4 border-t border-gray-100 dark:border-zinc-800">
                      <p class="text-xs font-bold text-gray-500 dark:text-zinc-400 uppercase tracking-wide">Todo de Basic +</p>
                      <div class="flex items-center gap-2 text-sm text-gray-600 dark:text-zinc-300">
                        <svg class="w-4 h-4 text-emerald-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                          <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                        </svg>
                        <span>3 Usuarios / 2 Bodegas</span>
                      </div>
                      <div class="flex items-center gap-2 text-sm text-gray-600 dark:text-zinc-300">
                        <svg class="w-4 h-4 text-emerald-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                          <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                        </svg>
                        <span>Agente IA (Crea Promos)</span>
                      </div>
                      <div class="flex items-center gap-2 text-sm text-gray-600 dark:text-zinc-300">
                        <svg class="w-4 h-4 text-emerald-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                          <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                        </svg>
                        <span>Sistema de Fidelización</span>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Enterprise Plan -->
                <div 
                  v-if="canUpgradeTo('enterprise')"
                  @click="selectedPlan = 'enterprise'"
                  :class="[
                    'relative rounded-2xl border-2 transition-all duration-300 cursor-pointer overflow-hidden',
                    selectedPlan === 'enterprise' 
                      ? 'border-emerald-500 bg-emerald-50/50 dark:bg-emerald-950/20 ring-4 ring-emerald-500/10' 
                      : 'border-gray-200 dark:border-zinc-800 bg-white dark:bg-zinc-900/50 hover:border-gray-300 dark:hover:border-zinc-700'
                  ]"
                >
                  <!-- Selection indicator -->
                  <div v-if="selectedPlan === 'enterprise'" class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-emerald-500 to-teal-500"></div>
                  
                  <div class="p-5">
                    <!-- Header -->
                    <div class="flex items-start justify-between mb-4">
                      <div>
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">Enterprise</h3>
                        <p class="text-xs text-gray-500 dark:text-zinc-400 mt-0.5">Solución empresarial</p>
                      </div>
                      <!-- Radio -->
                      <div :class="[
                        'w-5 h-5 rounded-full border-2 flex items-center justify-center transition-all duration-200',
                        selectedPlan === 'enterprise' ? 'border-emerald-500 bg-emerald-500' : 'border-gray-300 dark:border-zinc-600'
                      ]">
                        <svg v-if="selectedPlan === 'enterprise'" class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path>
                        </svg>
                      </div>
                    </div>

                    <!-- Price -->
                    <div class="mb-4">
                      <div class="flex items-baseline gap-1">
                        <span class="text-3xl font-black text-gray-900 dark:text-white">
                          {{ formatPrice(getPlanPrice('enterprise')) }}
                        </span>
                        <span class="text-sm text-gray-400 dark:text-zinc-500">/mes</span>
                      </div>
                      <p class="text-[11px] text-emerald-600 dark:text-emerald-400 font-medium mt-1">
                        {{ getPaymentLabel('enterprise') }}
                      </p>
                    </div>

                    <!-- Features -->
                    <div class="space-y-2.5 pt-4 border-t border-gray-100 dark:border-zinc-800">
                      <p class="text-xs font-bold text-gray-500 dark:text-zinc-400 uppercase tracking-wide">Todo de Premium +</p>
                      <div class="flex items-center gap-2 text-sm text-gray-600 dark:text-zinc-300">
                        <svg class="w-4 h-4 text-emerald-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                          <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                        </svg>
                        <span>Usuarios ilimitados</span>
                      </div>
                      <div class="flex items-center gap-2 text-sm text-gray-600 dark:text-zinc-300">
                        <svg class="w-4 h-4 text-emerald-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                          <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                        </svg>
                        <span>Multi-Sede / Multi-Caja</span>
                      </div>
                      <div class="flex items-center gap-2 text-sm text-gray-600 dark:text-zinc-300">
                        <svg class="w-4 h-4 text-emerald-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                          <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                        </svg>
                        <span>SLA 99.9% garantizado</span>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- No plans available message -->
              <div v-if="!canUpgradeTo('basic') && !canUpgradeTo('premium') && !canUpgradeTo('enterprise')" class="text-center py-8">
                <div class="w-16 h-16 bg-emerald-100 dark:bg-emerald-900/30 rounded-full flex items-center justify-center mx-auto mb-4">
                  <svg class="w-8 h-8 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                  </svg>
                </div>
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">¡Ya tienes el mejor plan!</h3>
                <p class="text-sm text-gray-500 dark:text-zinc-400">Estás disfrutando de todas las funcionalidades disponibles.</p>
              </div>
            </div>

            <!-- Footer con resumen y botones -->
            <div class="px-6 py-5 bg-gray-50 dark:bg-zinc-900/80 border-t border-gray-100 dark:border-zinc-800">
              <!-- Terms -->
              <div v-if="selectedPlan" class="mb-4 p-3 bg-white dark:bg-zinc-800/50 border border-gray-200 dark:border-zinc-700 rounded-xl">
                <label class="flex items-start gap-3 cursor-pointer">
                  <input 
                    type="checkbox" 
                    v-model="agreedToTerms"
                    class="w-4 h-4 text-emerald-600 border-gray-300 dark:border-zinc-600 rounded focus:ring-emerald-500 mt-0.5"
                  >
                  <span class="text-sm text-gray-600 dark:text-zinc-300 leading-relaxed">
                    Autorizo el pago de <strong class="text-gray-900 dark:text-white">{{ selectedPlanTotalPrice }}</strong> ahora. 
                    Entiendo que puedo cancelar en cualquier momento.
                  </span>
                </label>
              </div>

              <!-- Action Buttons -->
              <div class="flex gap-3">
                <button
                  @click="closeModal"
                  :disabled="isProcessing"
                  class="flex-1 h-12 px-6 text-sm font-semibold rounded-xl transition-all duration-200 bg-white dark:bg-zinc-800 hover:bg-gray-50 dark:hover:bg-zinc-700 text-gray-600 dark:text-zinc-300 border border-gray-200 dark:border-zinc-700 disabled:opacity-50"
                >
                  Cancelar
                </button>
                <button
                  @click="processPayment"
                  :disabled="!selectedPlan || !agreedToTerms || isProcessing"
                  :class="[
                    'flex-1 h-12 px-6 text-sm font-bold rounded-xl transition-all duration-200 flex items-center justify-center gap-2',
                    (!selectedPlan || !agreedToTerms || isProcessing)
                      ? 'bg-gray-300 dark:bg-zinc-700 text-gray-500 dark:text-zinc-500 cursor-not-allowed'
                      : 'bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-600 hover:to-teal-700 text-white shadow-lg shadow-emerald-500/25 hover:shadow-emerald-500/40 hover:scale-[1.02] active:scale-[0.98]'
                  ]"
                >
                  <template v-if="isProcessing">
                    <svg class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24">
                      <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                      <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span>Procesando...</span>
                  </template>
                  <template v-else>
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                    </svg>
                    <span>Pagar con ePayco</span>
                  </template>
                </button>
              </div>
              
              <!-- Payment Methods -->
              <div class="flex items-center justify-center gap-4 mt-4 pt-4 border-t border-gray-200 dark:border-zinc-800">
                <span class="text-xs text-gray-400 dark:text-zinc-500">Pago seguro con</span>
                <div class="flex items-center gap-3">
                  <!-- Visa -->
                  <div class="px-2 py-1 bg-white dark:bg-zinc-800 rounded border border-gray-200 dark:border-zinc-700">
                    <span class="text-[10px] font-bold text-blue-600">VISA</span>
                  </div>
                  <!-- Mastercard -->
                  <div class="px-2 py-1 bg-white dark:bg-zinc-800 rounded border border-gray-200 dark:border-zinc-700">
                    <span class="text-[10px] font-bold text-orange-500">MC</span>
                  </div>
                  <!-- PSE -->
                  <div class="px-2 py-1 bg-white dark:bg-zinc-800 rounded border border-gray-200 dark:border-zinc-700">
                    <span class="text-[10px] font-bold text-green-600">PSE</span>
                  </div>
                  <!-- Nequi -->
                  <div class="px-2 py-1 bg-white dark:bg-zinc-800 rounded border border-gray-200 dark:border-zinc-700">
                    <span class="text-[10px] font-bold text-pink-500">Nequi</span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </Transition>
      </div>
    </Transition>
  </Teleport>
</template>

<script setup>
import { ref, computed, watch } from 'vue'
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

// Prices configuration - Precios reales de producción
const planPrices = {
  basic: {
    monthly: { display: 25000, total: 25000 },
    yearly: { display: 20000, total: 240000 },
    '24months': { display: 20000, total: 480000 }
  },
  premium: {
    monthly: { display: 60000, total: 60000, withDian: 90000, totalWithDian: 90000 },
    yearly: { display: 50000, total: 600000, withDian: 80000, totalWithDian: 960000 },
    '24months': { display: 50000, total: 1200000, withDian: 80000, totalWithDian: 1920000 }
  },
  enterprise: {
    monthly: { display: 150000, total: 150000 },
    yearly: { display: 120000, total: 1440000 },
    '24months': { display: 120000, total: 2880000 }
  }
}

// Get plan display price (monthly equivalent)
const getPlanPrice = (plan) => {
  const prices = planPrices[plan]?.[paymentFrequency.value]
  if (!prices) return 0
  
  if (plan === 'premium' && includeDianInvoicing.value) {
    return prices.withDian || prices.display
  }
  return prices.display
}

// Get total price to pay
const getTotalPrice = (plan) => {
  const prices = planPrices[plan]?.[paymentFrequency.value]
  if (!prices) return 0
  
  if (plan === 'premium' && includeDianInvoicing.value) {
    return prices.totalWithDian || prices.total
  }
  return prices.total
}

// Format price with Colombian format
const formatPrice = (price) => {
  return '$' + price.toLocaleString('es-CO')
}

// Get payment label based on frequency
const getPaymentLabel = (plan) => {
  const total = getTotalPrice(plan)
  
  if (paymentFrequency.value === 'monthly') {
    return 'Facturado mensualmente'
  } else if (paymentFrequency.value === 'yearly') {
    return `Pago único de ${formatPrice(total)} (ahorra 20%)`
  } else {
    return `Pago único de ${formatPrice(total)} + 2 meses gratis`
  }
}

// Selected plan total price formatted
const selectedPlanTotalPrice = computed(() => {
  if (!selectedPlan.value) return '$0'
  return formatPrice(getTotalPrice(selectedPlan.value))
})

// Format date
const formatDate = (dateStr) => {
  if (!dateStr) return ''
  const date = new Date(dateStr)
  return date.toLocaleDateString('es-CO', { year: 'numeric', month: 'long', day: 'numeric' })
}

// Reset state when modal closes
watch(() => props.isOpen, (newVal) => {
  if (!newVal) {
    selectedPlan.value = null
    agreedToTerms.value = false
    paymentFrequency.value = 'monthly'
    includeDianInvoicing.value = false
  }
})

// Close modal
const closeModal = () => {
  emit('close')
}

// Process payment with ePayco
const processPayment = async () => {
  if (isProcessing.value || !selectedPlan.value || !agreedToTerms.value) return
  
  try {
    isProcessing.value = true
    
    // Get tenant ID
    const tenantId = appStore.tenant?.id
    if (!tenantId) {
      throw new Error('No se pudo obtener el ID del negocio. Por favor, recarga la página.')
    }
    
    const finalPrice = getTotalPrice(selectedPlan.value)
    const reference = `upgrade_${tenantId}_${Date.now()}`
    
    // 1. Initialize transaction in backend
    const initResponse = await axios.post('/api/epayco/init-transaction', {
      amount: finalPrice,
      reference: reference,
      customer_email: appStore.userEmail || 'cliente@105pos.pro',
      payment_frequency: paymentFrequency.value,
      plan: selectedPlan.value,
      tenant_id: tenantId,
      include_dian: includeDianInvoicing.value
    })

    if (!initResponse.data.success) {
      throw new Error(initResponse.data.error || 'Error inicializando transacción')
    }

    // 2. Open ePayco Checkout
    if (!window.ePayco) {
      throw new Error('El sistema de pagos no está disponible. Por favor recarga la página.')
    }

    const handler = window.ePayco.checkout.configure({
      key: import.meta.env.VITE_EPAYCO_PUBLIC_KEY || '2943652c673afffaa5b7b67829f00a0c',
      test: import.meta.env.VITE_EPAYCO_TEST_MODE === 'true' || true
    })

    const planNames = {
      basic: 'Basic',
      premium: includeDianInvoicing.value ? 'Premium + DIAN' : 'Premium',
      enterprise: 'Enterprise'
    }

    const frequencyNames = {
      monthly: 'Mensual',
      yearly: 'Anual',
      '24months': '24 Meses'
    }

    const data = {
      name: `Plan ${planNames[selectedPlan.value]}`,
      description: `Suscripción ${frequencyNames[paymentFrequency.value]} - 105 POS`,
      invoice: reference,
      currency: 'cop',
      amount: finalPrice,
      tax_base: '0',
      tax: '0',
      country: 'co',
      lang: 'es',
      external: 'false', // false = Modal dentro de la página (NO redirección)
      extra1: tenantId,
      extra2: selectedPlan.value,
      extra3: paymentFrequency.value,
      confirmation: `${window.location.origin}/api/epayco/webhook`,
      response: `${window.location.origin}/#/dashboard`, // Volver al dashboard después del pago
      email_billing: appStore.userEmail || 'cliente@105pos.pro',
      name_billing: appStore.userName || 'Cliente 105 POS',
      
      // Callbacks para manejar respuesta sin redirigir
      methodsDisable: [],
    }

    // Agregar listeners para capturar la respuesta del pago
    const checkPaymentInterval = setInterval(async () => {
      try {
        // Consultar si el pago fue procesado
        const statusResponse = await axios.get(`/api/epayco/check-payment-status/${reference}`)
        
        if (statusResponse.data.status === 'approved') {
          clearInterval(checkPaymentInterval)
          
          // Actualizar datos del tenant en el store
          await appStore.fetchTenantData()
          
          // Mostrar éxito
          alert('✅ ¡Pago procesado con éxito!\n\nTu plan ha sido actualizado. Recarga la página para ver los cambios.')
          
          // Cerrar modal
          closeModal()
          emit('success')
          
          // Recargar página para aplicar cambios
          setTimeout(() => {
            window.location.reload()
          }, 2000)
        } else if (statusResponse.data.status === 'rejected' || statusResponse.data.status === 'failed') {
          clearInterval(checkPaymentInterval)
          alert('❌ El pago fue rechazado.\n\nPor favor, intenta nuevamente con otro método de pago.')
        }
      } catch (error) {
        // Continuar esperando
      }
    }, 3000) // Consultar cada 3 segundos

    // Detener consultas después de 5 minutos
    setTimeout(() => {
      clearInterval(checkPaymentInterval)
    }, 300000)

    handler.open(data)
    
  } catch (error) {
    console.error('Error processing payment:', error)
    alert('❌ Error al procesar el pago\n\n' + (error.response?.data?.error || error.message || 'Por favor, intenta nuevamente.'))
  } finally {
    isProcessing.value = false
  }
}
</script>
