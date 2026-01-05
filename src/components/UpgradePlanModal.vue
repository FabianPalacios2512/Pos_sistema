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
            class="bg-white dark:bg-[#0f0f12] rounded-3xl shadow-2xl w-full max-w-6xl my-auto overflow-hidden border border-gray-200/50 dark:border-zinc-800/50"
          >
            
            <!-- Header Sobrio -->
            <div class="relative px-6 py-4 border-b border-gray-200 dark:border-zinc-800">
              <div class="relative flex items-center justify-between">
                <div>
                  <h2 class="text-xl font-bold text-gray-900 dark:text-white">
                    {{ isRenewal ? 'Renovar suscripción' : 'Gestión de planes' }}
                  </h2>
                  <p class="text-xs text-gray-500 dark:text-zinc-400 mt-0.5">
                    {{ isRenewal ? 'Extiende tu suscripción actual' : 'Selecciona el plan que mejor se adapte a tu negocio' }}
                  </p>
                </div>
                
                <button 
                  @click="closeModal"
                  class="p-2 text-gray-400 hover:text-gray-600 dark:text-zinc-500 dark:hover:text-zinc-300 rounded-lg hover:bg-gray-100 dark:hover:bg-zinc-800 transition-all"
                >
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path>
                  </svg>
                </button>
              </div>
            </div>

            <!-- Content: 2 columnas -->
            <div class="flex flex-col lg:flex-row max-h-[75vh]">
              
              <!-- LEFT: Planes -->
              <div class="flex-1 p-6 overflow-y-auto border-r-0 lg:border-r border-gray-100 dark:border-zinc-800">
                
                <!-- Plan actual badge + Selector de frecuencia -->
                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6">
                  <!-- Plan actual -->
                  <div v-if="currentPlan" class="flex items-center gap-2 px-3 py-2 bg-blue-50 dark:bg-blue-950/30 border border-blue-100 dark:border-blue-900/50 rounded-xl">
                    <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                    </svg>
                    <span class="text-sm font-medium text-blue-900 dark:text-blue-100">
                      Plan actual: <span class="capitalize font-bold">{{ getPlanDisplayName(currentPlan) }}</span>
                    </span>
                    <span v-if="daysRemaining > 0" class="text-xs text-blue-600 dark:text-blue-400 ml-1">
                      ({{ daysRemaining }} días restantes)
                    </span>
                  </div>

                  <!-- Selector de Frecuencia -->
                  <div class="inline-flex p-1 bg-gray-100 dark:bg-zinc-800/80 rounded-xl gap-0.5">
                    <button
                      @click="selectFrequency('monthly')"
                      :disabled="!canSelectMonthly"
                      :title="!canSelectMonthly ? 'Disponible cuando te queden 7 días o menos' : ''"
                      :class="[
                        'px-3 py-1.5 text-xs font-semibold rounded-lg transition-all duration-200',
                        paymentFrequency === 'monthly' 
                          ? 'bg-white dark:bg-zinc-700 text-gray-900 dark:text-white shadow-sm' 
                          : 'text-gray-500 dark:text-zinc-400 hover:text-gray-700 dark:hover:text-zinc-200',
                        !canSelectMonthly ? 'opacity-40 cursor-not-allowed' : ''
                      ]"
                    >
                      Mensual
                    </button>
                    <button
                      @click="selectFrequency('yearly')"
                      :class="[
                        'px-3 py-1.5 text-xs font-semibold rounded-lg transition-all duration-200 flex items-center gap-1',
                        paymentFrequency === 'yearly' 
                          ? 'bg-white dark:bg-zinc-700 text-gray-900 dark:text-white shadow-sm' 
                          : 'text-gray-500 dark:text-zinc-400 hover:text-gray-700 dark:hover:text-zinc-200'
                      ]"
                    >
                      Anual
                      <span class="text-[9px] font-bold bg-gray-200 dark:bg-zinc-600 text-gray-600 dark:text-zinc-300 px-1 py-0.5 rounded">-20%</span>
                    </button>
                    <button
                      @click="selectFrequency('24months')"
                      :class="[
                        'px-3 py-1.5 text-xs font-semibold rounded-lg transition-all duration-200 flex items-center gap-1',
                        paymentFrequency === '24months' 
                          ? 'bg-white dark:bg-zinc-700 text-gray-900 dark:text-white shadow-sm' 
                          : 'text-gray-500 dark:text-zinc-400 hover:text-gray-700 dark:hover:text-zinc-200'
                      ]"
                    >
                      24 Meses
                      <span class="text-[9px] font-bold bg-gray-200 dark:bg-zinc-600 text-gray-600 dark:text-zinc-300 px-1 py-0.5 rounded">+2 gratis</span>
                    </button>
                  </div>
                </div>

                <!-- Plans Grid - 3 columnas -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                  
                  <!-- Basic Plan -->
                  <div 
                    v-if="canShowPlan('basic')"
                    @click="selectPlan('basic')"
                    :class="[
                      'relative rounded-xl border transition-all duration-200 cursor-pointer overflow-hidden',
                      selectedPlan === 'basic' 
                        ? 'border-gray-900 dark:border-zinc-400 bg-gray-50 dark:bg-zinc-800/50 ring-1 ring-gray-900/10 dark:ring-zinc-400/20' 
                        : 'border-gray-200 dark:border-zinc-800 bg-white dark:bg-zinc-900/50 hover:border-gray-300 dark:hover:border-zinc-700'
                    ]"
                  >
                    <div v-if="selectedPlan === 'basic'" class="absolute top-0 left-0 right-0 h-0.5 bg-gray-900 dark:bg-zinc-400"></div>
                    
                    <div class="p-4">
                      <div class="flex items-start justify-between mb-3">
                        <div>
                          <h3 class="text-base font-bold text-gray-900 dark:text-white">Basic</h3>
                          <p class="text-[10px] text-gray-500 dark:text-zinc-400">Para empezar</p>
                        </div>
                        <div :class="['w-4 h-4 rounded-full border-2 flex items-center justify-center', selectedPlan === 'basic' ? 'border-gray-900 dark:border-zinc-300 bg-gray-900 dark:bg-zinc-300' : 'border-gray-300 dark:border-zinc-600']">
                          <svg v-if="selectedPlan === 'basic'" class="w-2.5 h-2.5 text-white dark:text-zinc-900" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path>
                          </svg>
                        </div>
                      </div>

                      <div class="mb-3">
                        <div class="flex items-baseline gap-0.5">
                          <span class="text-2xl font-black text-gray-900 dark:text-white">{{ formatPrice(getPlanPrice('basic')) }}</span>
                          <span class="text-xs text-gray-400 dark:text-zinc-500">/mes</span>
                        </div>
                        <p class="text-[10px] text-gray-500 dark:text-zinc-400 font-medium">{{ getFrequencyLabel() }}</p>
                      </div>

                      <div class="space-y-1.5 pt-3 border-t border-gray-100 dark:border-zinc-800">
                        <div class="flex items-center gap-1.5 text-xs text-gray-600 dark:text-zinc-300">
                          <svg class="w-3 h-3 text-gray-400 dark:text-zinc-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                          </svg>
                          <span>1 Usuario</span>
                        </div>
                        <div class="flex items-center gap-1.5 text-xs text-gray-600 dark:text-zinc-300">
                          <svg class="w-3 h-3 text-gray-400 dark:text-zinc-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                          </svg>
                          <span>POS + Inventario</span>
                        </div>
                        <div class="flex items-center gap-1.5 text-xs text-gray-600 dark:text-zinc-300">
                          <svg class="w-3 h-3 text-gray-400 dark:text-zinc-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                          </svg>
                          <span>Soporte Email</span>
                        </div>
                      </div>

                      <!-- Renewal Badge -->
                      <div v-if="currentPlan === 'basic'" class="mt-3 px-2 py-1 bg-gray-100 dark:bg-zinc-800 rounded-lg text-center">
                        <span class="text-[10px] font-bold text-gray-600 dark:text-zinc-400">PLAN ACTUAL</span>
                      </div>
                    </div>
                  </div>

                  <!-- Premium Plan (Popular) -->
                  <div 
                    v-if="canShowPlan('premium')"
                    @click="selectPlan('premium')"
                    :class="[
                      'relative rounded-xl border transition-all duration-200 cursor-pointer overflow-hidden',
                      selectedPlan === 'premium' 
                        ? 'border-gray-900 dark:border-zinc-400 bg-gray-50 dark:bg-zinc-800/50 ring-1 ring-gray-900/10 dark:ring-zinc-400/20' 
                        : 'border-gray-200 dark:border-zinc-700 bg-white dark:bg-zinc-900/50 hover:border-gray-300 dark:hover:border-zinc-600'
                    ]"
                  >
                    <div class="absolute -top-px left-1/2 -translate-x-1/2">
                      <div class="px-3 py-0.5 bg-gray-900 dark:bg-zinc-300 text-white dark:text-zinc-900 text-[9px] font-bold uppercase tracking-wider rounded-b-lg">
                        Recomendado
                      </div>
                    </div>
                    
                    <div v-if="selectedPlan === 'premium'" class="absolute top-0 left-0 right-0 h-0.5 bg-gray-900 dark:bg-zinc-400"></div>
                    
                    <div class="p-4 pt-6">
                      <div class="flex items-start justify-between mb-3">
                        <div>
                          <h3 class="text-base font-bold text-gray-900 dark:text-white">Premium</h3>
                          <p class="text-[10px] text-gray-500 dark:text-zinc-400">IA + Automatización</p>
                        </div>
                        <div :class="['w-4 h-4 rounded-full border-2 flex items-center justify-center', selectedPlan === 'premium' ? 'border-gray-900 dark:border-zinc-300 bg-gray-900 dark:bg-zinc-300' : 'border-gray-300 dark:border-zinc-600']">
                          <svg v-if="selectedPlan === 'premium'" class="w-2.5 h-2.5 text-white dark:text-zinc-900" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path>
                          </svg>
                        </div>
                      </div>

                      <div class="mb-3">
                        <div class="flex items-baseline gap-0.5">
                          <span class="text-2xl font-black text-gray-900 dark:text-white">{{ formatPrice(getPlanPrice('premium')) }}</span>
                          <span class="text-xs text-gray-400 dark:text-zinc-500">/mes</span>
                        </div>
                        <p class="text-[10px] text-gray-500 dark:text-zinc-400 font-medium">{{ getFrequencyLabel() }}</p>
                      </div>

                      <div class="space-y-1.5 pt-3 border-t border-gray-100 dark:border-zinc-800">
                        <div class="flex items-center gap-1.5 text-xs text-gray-600 dark:text-zinc-300">
                          <svg class="w-3 h-3 text-gray-400 dark:text-zinc-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                          </svg>
                          <span>20 Usuarios</span>
                        </div>
                        <div class="flex items-center gap-1.5 text-xs text-gray-600 dark:text-zinc-300">
                          <svg class="w-3 h-3 text-gray-400 dark:text-zinc-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                          </svg>
                          <span>IA Predictiva</span>
                        </div>
                        <div class="flex items-center gap-1.5 text-xs text-gray-600 dark:text-zinc-300">
                          <svg class="w-3 h-3 text-gray-400 dark:text-zinc-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                          </svg>
                          <span>Reportes Avanzados</span>
                        </div>
                        <div class="flex items-center gap-1.5 text-xs text-gray-600 dark:text-zinc-300">
                          <svg class="w-3 h-3 text-gray-400 dark:text-zinc-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                          </svg>
                          <span>Soporte Prioritario</span>
                        </div>
                      </div>

                      <!-- Renewal Badge -->
                      <div v-if="currentPlan === 'premium'" class="mt-3 px-2 py-1 bg-gray-100 dark:bg-zinc-800 rounded-lg text-center">
                        <span class="text-[10px] font-bold text-gray-600 dark:text-zinc-400">PLAN ACTUAL</span>
                      </div>
                    </div>
                  </div>

                  <!-- Enterprise Plan -->
                  <div 
                    v-if="canShowPlan('enterprise')"
                    @click="selectPlan('enterprise')"
                    :class="[
                      'relative rounded-xl border transition-all duration-200 cursor-pointer overflow-hidden',
                      selectedPlan === 'enterprise' 
                        ? 'border-gray-900 dark:border-zinc-400 bg-gray-50 dark:bg-zinc-800/50 ring-1 ring-gray-900/10 dark:ring-zinc-400/20' 
                        : 'border-gray-200 dark:border-zinc-800 bg-white dark:bg-zinc-900/50 hover:border-gray-300 dark:hover:border-zinc-700'
                    ]"
                  >
                    <div v-if="selectedPlan === 'enterprise'" class="absolute top-0 left-0 right-0 h-0.5 bg-gray-900 dark:bg-zinc-400"></div>
                    
                    <div class="p-4">
                      <div class="flex items-start justify-between mb-3">
                        <div>
                          <h3 class="text-base font-bold text-gray-900 dark:text-white">Enterprise</h3>
                          <p class="text-[10px] text-gray-500 dark:text-zinc-400">Sin límites</p>
                        </div>
                        <div :class="['w-4 h-4 rounded-full border-2 flex items-center justify-center', selectedPlan === 'enterprise' ? 'border-gray-900 dark:border-zinc-300 bg-gray-900 dark:bg-zinc-300' : 'border-gray-300 dark:border-zinc-600']">
                          <svg v-if="selectedPlan === 'enterprise'" class="w-2.5 h-2.5 text-white dark:text-zinc-900" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path>
                          </svg>
                        </div>
                      </div>

                      <div class="mb-3">
                        <div class="flex items-baseline gap-0.5">
                          <span class="text-2xl font-black text-gray-900 dark:text-white">{{ formatPrice(getPlanPrice('enterprise')) }}</span>
                          <span class="text-xs text-gray-400 dark:text-zinc-500">/mes</span>
                        </div>
                        <p class="text-[10px] text-gray-500 dark:text-zinc-400 font-medium">{{ getFrequencyLabel() }}</p>
                      </div>

                      <div class="space-y-1.5 pt-3 border-t border-gray-100 dark:border-zinc-800">
                        <div class="flex items-center gap-1.5 text-xs text-gray-600 dark:text-zinc-300">
                          <svg class="w-3 h-3 text-gray-400 dark:text-zinc-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                          </svg>
                          <span>Usuarios Ilimitados</span>
                        </div>
                        <div class="flex items-center gap-1.5 text-xs text-gray-600 dark:text-zinc-300">
                          <svg class="w-3 h-3 text-gray-400 dark:text-zinc-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                          </svg>
                          <span>Multi-Sucursal</span>
                        </div>
                        <div class="flex items-center gap-1.5 text-xs text-gray-600 dark:text-zinc-300">
                          <svg class="w-3 h-3 text-gray-400 dark:text-zinc-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                          </svg>
                          <span>API Completa</span>
                        </div>
                        <div class="flex items-center gap-1.5 text-xs text-gray-600 dark:text-zinc-300">
                          <svg class="w-3 h-3 text-gray-400 dark:text-zinc-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                          </svg>
                          <span>Soporte 24/7</span>
                        </div>
                      </div>

                      <!-- Renewal Badge -->
                      <div v-if="currentPlan === 'enterprise'" class="mt-3 px-2 py-1 bg-gray-100 dark:bg-zinc-800 rounded-lg text-center">
                        <span class="text-[10px] font-bold text-gray-600 dark:text-zinc-400">PLAN ACTUAL</span>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- No hay opciones disponibles -->
                <div v-if="!hasAvailableOptions" class="flex flex-col items-center justify-center py-12 text-center">
                  <div class="w-16 h-16 bg-emerald-100 dark:bg-emerald-950/50 rounded-full flex items-center justify-center mb-4">
                    <svg class="w-8 h-8 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                  </div>
                  <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">¡Ya tienes el mejor plan!</h3>
                  <p class="text-sm text-gray-500 dark:text-zinc-400 max-w-xs">
                    Estás disfrutando de todas las funcionalidades disponibles.
                  </p>
                </div>
              </div>

              <!-- RIGHT: Panel de Resumen -->
              <div class="w-full lg:w-96 bg-gray-50 dark:bg-zinc-900/50 p-6 flex flex-col">
                
                <!-- Header del Resumen -->
                <h3 class="text-sm font-bold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                  <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                  </svg>
                  Resumen de tu pedido
                </h3>

                <!-- Contenido del resumen -->
                <div v-if="selectedPlan" class="flex-1 space-y-4">
                  
                  <!-- Plan seleccionado -->
                  <div class="p-3 bg-white dark:bg-zinc-800 rounded-xl border border-gray-200 dark:border-zinc-700">
                    <div class="flex items-center justify-between">
                      <div>
                        <p class="text-xs text-gray-500 dark:text-zinc-400">Plan seleccionado</p>
                        <p class="text-sm font-bold text-gray-900 dark:text-white capitalize">{{ getPlanDisplayName(selectedPlan) }}</p>
                      </div>
                      <span class="text-xs font-medium px-2 py-1 rounded-lg bg-gray-100 dark:bg-zinc-800 text-gray-700 dark:text-zinc-300">
                        {{ getFrequencyLabel() }}
                      </span>
                    </div>
                  </div>

                  <!-- Código de descuento -->
                  <div class="space-y-2">
                    <label class="text-xs font-medium text-gray-600 dark:text-zinc-400">¿Tienes un código de descuento?</label>
                    <div class="flex gap-2">
                      <input 
                        v-model="discountCode"
                        type="text"
                        placeholder="Ingresa tu código"
                        class="flex-1 px-3 py-2 text-sm bg-white dark:bg-zinc-800 border border-gray-200 dark:border-zinc-700 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-zinc-500"
                      >
                      <button 
                        @click="applyDiscountCode"
                        :disabled="!discountCode || isApplyingDiscount"
                        class="px-3 py-2 text-xs font-semibold bg-gray-900 dark:bg-zinc-700 text-white rounded-lg hover:bg-black dark:hover:bg-zinc-600 disabled:opacity-50 disabled:cursor-not-allowed transition-all"
                      >
                        {{ isApplyingDiscount ? '...' : 'Aplicar' }}
                      </button>
                    </div>
                    <p v-if="discountCodeApplied" class="text-xs text-emerald-600 dark:text-emerald-400 flex items-center gap-1">
                      <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                      </svg>
                      Código aplicado: -{{ formatPrice(discountCodeValue) }}
                    </p>
                    <p v-if="discountCodeError" class="text-xs text-red-500">{{ discountCodeError }}</p>
                  </div>

                  <!-- Desglose de precios -->
                  <div class="p-4 bg-white dark:bg-zinc-800 rounded-xl border border-gray-200 dark:border-zinc-700 space-y-3">
                    
                    <!-- Precio base -->
                    <div class="flex justify-between text-sm">
                      <span class="text-gray-600 dark:text-zinc-400">Precio {{ getFrequencyLabel().toLowerCase() }}</span>
                      <span class="font-medium text-gray-900 dark:text-white">{{ formatPrice(getBasePrice(selectedPlan)) }}</span>
                    </div>

                    <!-- Descuento por frecuencia -->
                    <div v-if="paymentFrequency !== 'monthly'" class="flex justify-between text-sm">
                      <span class="text-emerald-600 dark:text-emerald-400">
                        {{ paymentFrequency === 'yearly' ? 'Descuento anual (-20%)' : 'Descuento 24 meses' }}
                      </span>
                      <span class="font-medium text-emerald-600 dark:text-emerald-400">-{{ formatPrice(getFrequencyDiscount(selectedPlan)) }}</span>
                    </div>

                    <!-- Crédito por días restantes -->
                    <div v-if="availableCredit > 0" class="flex justify-between text-sm">
                      <span class="text-blue-600 dark:text-blue-400 flex items-center gap-1">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Crédito ({{ daysRemaining }} días)
                      </span>
                      <span class="font-medium text-blue-600 dark:text-blue-400">-{{ formatPrice(availableCredit) }}</span>
                    </div>

                    <!-- Descuento por código -->
                    <div v-if="discountCodeApplied && discountCodeValue > 0" class="flex justify-between text-sm">
                      <span class="text-purple-600 dark:text-purple-400">Código promocional</span>
                      <span class="font-medium text-purple-600 dark:text-purple-400">-{{ formatPrice(discountCodeValue) }}</span>
                    </div>

                    <!-- Línea divisora -->
                    <div class="border-t border-gray-200 dark:border-zinc-700 pt-3">
                      <div class="flex justify-between items-center">
                        <span class="text-base font-bold text-gray-900 dark:text-white">Total a pagar</span>
                        <span class="text-xl font-black text-gray-900 dark:text-white">{{ formatPrice(getFinalPrice()) }}</span>
                      </div>
                      <p class="text-[10px] text-gray-400 dark:text-zinc-500 mt-1">Impuestos incluidos</p>
                    </div>
                  </div>

                  <!-- Info de crédito -->
                  <div v-if="availableCredit > 0" class="p-3 bg-blue-50 dark:bg-blue-950/30 rounded-xl border border-blue-100 dark:border-blue-900/50">
                    <p class="text-[11px] text-blue-700 dark:text-blue-300 flex items-start gap-2">
                      <svg class="w-4 h-4 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                      </svg>
                      <span>Aplicamos un <strong>crédito proporcional</strong> por los {{ daysRemaining }} días no usados de tu plan actual.</span>
                    </p>
                  </div>

                  <!-- Terms -->
                  <label class="flex items-start gap-3 cursor-pointer p-3 bg-white dark:bg-zinc-800 rounded-xl border border-gray-200 dark:border-zinc-700">
                    <input 
                      type="checkbox" 
                      v-model="agreedToTerms"
                      class="w-4 h-4 text-emerald-600 border-gray-300 dark:border-zinc-600 rounded focus:ring-emerald-500 mt-0.5"
                    >
                    <span class="text-xs text-gray-600 dark:text-zinc-300 leading-relaxed">
                      Autorizo el pago de <strong class="text-gray-900 dark:text-white">{{ formatPrice(getFinalPrice()) }}</strong>. 
                      Puedo cancelar en cualquier momento.
                    </span>
                  </label>
                </div>

                <!-- Estado vacío -->
                <div v-else class="flex-1 flex flex-col items-center justify-center text-center py-8">
                  <div class="w-12 h-12 bg-gray-100 dark:bg-zinc-800 rounded-full flex items-center justify-center mb-3">
                    <svg class="w-6 h-6 text-gray-400 dark:text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993l1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 01-1.12-1.243l1.264-12A1.125 1.125 0 015.513 7.5h12.974c.576 0 1.059.435 1.119 1.007zM8.625 10.5a.375.375 0 11-.75 0 .375.375 0 01.75 0zm7.5 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z"/>
                    </svg>
                  </div>
                  <p class="text-sm font-medium text-gray-600 dark:text-zinc-400">Selecciona un plan</p>
                  <p class="text-xs text-gray-400 dark:text-zinc-500 mt-1">El resumen aparecerá aquí</p>
                </div>

                <!-- Botones de acción -->
                <div class="space-y-3 mt-4">
                  <button
                    @click="processPayment"
                    :disabled="!selectedPlan || !agreedToTerms || isProcessing || getFinalPrice() <= 0"
                    :class="[
                      'w-full h-12 px-6 text-sm font-bold rounded-xl transition-all duration-200 flex items-center justify-center gap-2',
                      (!selectedPlan || !agreedToTerms || isProcessing || getFinalPrice() <= 0)
                        ? 'bg-gray-200 dark:bg-zinc-700 text-gray-400 dark:text-zinc-500 cursor-not-allowed'
                        : 'bg-gray-900 dark:bg-zinc-200 hover:bg-black dark:hover:bg-white text-white dark:text-zinc-900 shadow-lg hover:shadow-xl'
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

                  <button
                    @click="closeModal"
                    :disabled="isProcessing"
                    class="w-full py-2.5 text-sm font-medium text-gray-500 dark:text-zinc-400 hover:text-gray-700 dark:hover:text-zinc-200 transition-colors"
                  >
                    Cancelar
                  </button>
                </div>

                <!-- Payment Methods -->
                <div class="flex items-center justify-center gap-3 mt-4 pt-4 border-t border-gray-200 dark:border-zinc-800">
                  <span class="text-[10px] text-gray-400 dark:text-zinc-500">Pago seguro</span>
                  <div class="flex items-center gap-2">
                    <span class="text-[9px] font-bold text-blue-600 px-1.5 py-0.5 bg-blue-50 dark:bg-blue-950 rounded">VISA</span>
                    <span class="text-[9px] font-bold text-orange-500 px-1.5 py-0.5 bg-orange-50 dark:bg-orange-950 rounded">MC</span>
                    <span class="text-[9px] font-bold text-green-600 px-1.5 py-0.5 bg-green-50 dark:bg-green-950 rounded">PSE</span>
                    <span class="text-[9px] font-bold text-pink-500 px-1.5 py-0.5 bg-pink-50 dark:bg-pink-950 rounded">Nequi</span>
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
import { appStore, subscriptionEndDate } from '../store/appStore.js'
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
const discountCode = ref('')
const discountCodeApplied = ref(false)
const discountCodeValue = ref(0)
const discountCodeError = ref('')
const isApplyingDiscount = ref(false)

// Get current tenant data
const currentPlan = computed(() => appStore.tenantPlan)
const subscriptionEndsAt = computed(() => subscriptionEndDate.value)

// Es renovación si selecciona el mismo plan
const isRenewal = computed(() => selectedPlan.value && selectedPlan.value === currentPlan.value)

// Plan names for display
const getPlanDisplayName = (plan) => {
  const names = {
    'free_trial': 'Trial',
    'trial_express': 'Trial',
    'basic': 'Basic',
    'premium': 'Premium',
    'enterprise': 'Enterprise'
  }
  return names[plan] || plan
}

// Prices configuration
const planPrices = {
  basic: {
    monthly: { display: 25000, total: 25000 },
    yearly: { display: 20000, total: 240000 },
    '24months': { display: 20000, total: 440000 }
  },
  premium: {
    monthly: { display: 60000, total: 60000 },
    yearly: { display: 50000, total: 600000 },
    '24months': { display: 50000, total: 1100000 }
  },
  enterprise: {
    monthly: { display: 150000, total: 150000 },
    yearly: { display: 120000, total: 1440000 },
    '24months': { display: 120000, total: 2640000 }
  }
}

// Plan hierarchy for validation
const planHierarchy = {
  'trial_express': 0,
  'free_trial': 0,
  'basic': 1,
  'premium': 2,
  'enterprise': 3
}

// Check if plan should be shown (upgrade OR renewal)
const canShowPlan = (plan) => {
  const currentLevel = planHierarchy[currentPlan.value] || 0
  const targetLevel = planHierarchy[plan] || 0
  
  // Puede subir de plan
  if (targetLevel > currentLevel) return true
  
  // Puede renovar el mismo plan
  if (plan === currentPlan.value) return true
  
  return false
}

// Check if there are available options
const hasAvailableOptions = computed(() => {
  return canShowPlan('basic') || canShowPlan('premium') || canShowPlan('enterprise')
})

// Días restantes del plan actual
const daysRemaining = computed(() => {
  if (!subscriptionEndsAt.value) return 0
  const now = new Date()
  const endDate = new Date(subscriptionEndsAt.value)
  const days = Math.ceil((endDate - now) / (1000 * 60 * 60 * 24))
  return days > 0 ? days : 0
})

// ¿Puede seleccionar mensual? Solo si le quedan 7 días o menos, o si es un UPGRADE (plan diferente)
const canSelectMonthly = computed(() => {
  // Si no hay plan actual o es trial, puede mensual
  if (!currentPlan.value || currentPlan.value === 'trial_express' || currentPlan.value === 'free_trial') {
    return true
  }
  
  // Si está viendo un plan DIFERENTE (upgrade), puede mensual
  if (selectedPlan.value && selectedPlan.value !== currentPlan.value) {
    return true
  }
  
  // Si es renovación del MISMO plan, solo puede mensual si le quedan 7 días o menos
  return daysRemaining.value <= 7
})

// Función para seleccionar frecuencia
const selectFrequency = (freq) => {
  if (freq === 'monthly' && !canSelectMonthly.value) {
    return // No permitir mensual si no puede
  }
  paymentFrequency.value = freq
}

// Select a plan
const selectPlan = (plan) => {
  selectedPlan.value = plan
  discountCodeApplied.value = false
  discountCodeValue.value = 0
  discountCodeError.value = ''
  
  // Si selecciona el mismo plan (renovación) y no puede mensual, forzar anual
  if (plan === currentPlan.value && !canSelectMonthly.value && paymentFrequency.value === 'monthly') {
    paymentFrequency.value = 'yearly'
  }
}

// Calcular crédito proporcional
const calculateProportionalCredit = () => {
  if (!currentPlan.value || !subscriptionEndsAt.value || currentPlan.value === 'trial_express' || currentPlan.value === 'free_trial') {
    return 0
  }

  try {
    const now = new Date()
    const endDate = new Date(subscriptionEndsAt.value)
    
    if (endDate <= now) return 0

    const days = Math.ceil((endDate - now) / (1000 * 60 * 60 * 24))
    const currentPlanPrices = planPrices[currentPlan.value]?.monthly
    if (!currentPlanPrices) return 0
    
    const dailyValue = currentPlanPrices.total / 30
    return Math.round(days * dailyValue)
  } catch (error) {
    return 0
  }
}

const availableCredit = computed(() => calculateProportionalCredit())

// Get frequency label
const getFrequencyLabel = () => {
  const labels = {
    'monthly': 'Mensual',
    'yearly': 'Anual',
    '24months': '24 Meses'
  }
  return labels[paymentFrequency.value] || 'Mensual'
}

// Get plan display price
const getPlanPrice = (plan) => {
  return planPrices[plan]?.[paymentFrequency.value]?.display || 0
}

// Get base price
const getBasePrice = (plan) => {
  const monthlyPrice = planPrices[plan]?.monthly?.total || 0
  const months = paymentFrequency.value === 'monthly' ? 1 : paymentFrequency.value === 'yearly' ? 12 : 24
  return monthlyPrice * months
}

// Get frequency discount
const getFrequencyDiscount = (plan) => {
  const base = getBasePrice(plan)
  const actual = planPrices[plan]?.[paymentFrequency.value]?.total || base
  return base - actual
}

// Get final price
const getFinalPrice = () => {
  if (!selectedPlan.value) return 0
  
  const planTotal = planPrices[selectedPlan.value]?.[paymentFrequency.value]?.total || 0
  const credit = availableCredit.value
  const codeDiscount = discountCodeApplied.value ? discountCodeValue.value : 0
  
  return Math.max(0, planTotal - credit - codeDiscount)
}

// Format price
const formatPrice = (price) => {
  return '$' + price.toLocaleString('es-CO')
}

// Apply discount code
const applyDiscountCode = async () => {
  if (!discountCode.value || isApplyingDiscount.value) return
  
  isApplyingDiscount.value = true
  discountCodeError.value = ''
  
  try {
    const code = discountCode.value.toUpperCase()
    
    if (code === '105POS10') {
      discountCodeApplied.value = true
      discountCodeValue.value = Math.round(getFinalPrice() * 0.10)
    } else if (code === '105POS20') {
      discountCodeApplied.value = true
      discountCodeValue.value = Math.round(getFinalPrice() * 0.20)
    } else if (code === 'BIENVENIDO') {
      discountCodeApplied.value = true
      discountCodeValue.value = 10000
    } else {
      discountCodeError.value = 'Código no válido o expirado'
      discountCodeApplied.value = false
      discountCodeValue.value = 0
    }
  } catch (error) {
    discountCodeError.value = 'Error al validar el código'
  } finally {
    isApplyingDiscount.value = false
  }
}

// Reset state when modal closes
watch(() => props.isOpen, (newVal) => {
  if (!newVal) {
    selectedPlan.value = null
    agreedToTerms.value = false
    paymentFrequency.value = 'monthly'
    discountCode.value = ''
    discountCodeApplied.value = false
    discountCodeValue.value = 0
    discountCodeError.value = ''
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
    
    const tenantId = appStore.tenant?.id
    if (!tenantId) {
      throw new Error('No se pudo obtener el ID del negocio. Por favor, recarga la página.')
    }
    
    const finalPrice = getFinalPrice()
    const reference = `upgrade_${tenantId}_${Date.now()}`
    
    const initResponse = await axios.post('/api/epayco/init-transaction', {
      amount: finalPrice,
      reference: reference,
      customer_email: appStore.userEmail || 'cliente@105pos.pro',
      payment_frequency: paymentFrequency.value,
      plan: selectedPlan.value,
      tenant_id: tenantId,
      is_renewal: isRenewal.value,
      applied_credit: availableCredit.value,
      discount_code: discountCodeApplied.value ? discountCode.value : null,
      discount_value: discountCodeValue.value
    })

    if (!initResponse.data.success) {
      throw new Error(initResponse.data.error || 'Error inicializando transacción')
    }

    if (!window.ePayco) {
      throw new Error('El sistema de pagos no está disponible. Por favor recarga la página.')
    }

    const handler = window.ePayco.checkout.configure({
      key: import.meta.env.VITE_EPAYCO_PUBLIC_KEY || '2943652c673afffaa5b7b67829f00a0c',
      test: import.meta.env.VITE_EPAYCO_TEST_MODE === 'true' || true
    })

    const planNames = {
      basic: 'Basic',
      premium: 'Premium',
      enterprise: 'Enterprise'
    }

    const frequencyNames = {
      monthly: 'Mensual',
      yearly: 'Anual',
      '24months': '24 Meses'
    }

    const data = {
      name: `Plan ${planNames[selectedPlan.value]} - ${frequencyNames[paymentFrequency.value]}`,
      description: `${isRenewal.value ? 'Renovación' : 'Upgrade'} ${frequencyNames[paymentFrequency.value]} - 105 POS`,
      invoice: reference,
      currency: 'cop',
      amount: finalPrice,
      tax_base: '0',
      tax: '0',
      country: 'co',
      lang: 'es',
      external: 'false',
      extra1: tenantId,
      extra2: selectedPlan.value,
      extra3: paymentFrequency.value,
      confirmation: `${window.location.origin}/api/epayco/webhook`,
      response: `${window.location.origin}/#/payment/verify?tenant_id=${tenantId}&plan=${selectedPlan.value}&reference=${reference}&is_upgrade=true`,
      email_billing: appStore.userEmail || 'cliente@105pos.pro',
      name_billing: appStore.userName || 'Cliente 105 POS',
    }

    handler.open(data)
    
  } catch (error) {
    console.error('Error processing payment:', error)
    alert('❌ Error al procesar el pago\n\n' + (error.response?.data?.error || error.message || 'Por favor, intenta nuevamente.'))
  } finally {
    isProcessing.value = false
  }
}
</script>
