<template>
  <div>
  <!-- MODAL DE ÉXITO PARA TRIAL -->
  <transition
    enter-active-class="transition ease-out duration-300"
    enter-from-class="opacity-0"
    enter-to-class="opacity-100"
    leave-active-class="transition ease-in duration-200"
    leave-from-class="opacity-100"
    leave-to-class="opacity-0"
  >
    <div 
      v-if="showTrialSuccessModal" 
      class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm px-4"
    >
      <transition
        enter-active-class="transition ease-out duration-300"
        enter-from-class="scale-95 opacity-0"
        enter-to-class="scale-100 opacity-100"
        leave-active-class="transition ease-in duration-200"
        leave-from-class="scale-100 opacity-100"
        leave-to-class="scale-95 opacity-0"
      >
        <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full overflow-hidden">
          <!-- Header -->
          <div class="bg-emerald-50 p-8 text-center border-b border-emerald-100">
            <div class="w-16 h-16 bg-emerald-100 rounded-full flex items-center justify-center mx-auto mb-4">
              <svg class="w-8 h-8 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
              </svg>
            </div>
            <h3 class="text-2xl font-bold text-gray-900 mb-2">Trial Activado</h3>
            <p class="text-gray-600">Tu prueba de 3 días comienza ahora</p>
          </div>

          <!-- Content -->
          <div class="p-8">
            <!-- Empresa -->
            <div class="mb-6 p-4 bg-gray-50 rounded-xl border border-gray-200">
              <p class="text-xs text-gray-500 uppercase tracking-wide font-semibold mb-1">Empresa</p>
              <p class="text-lg font-bold text-gray-900">{{ companyName }}</p>
            </div>

            <!-- Información importante -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-8">
              <p class="text-sm text-blue-900">
                <strong>Acceso completo</strong> a todas las funciones durante 3 días. No se requiere tarjeta de crédito.
              </p>
            </div>

            <!-- Botón -->
            <button
              @click="redirectToLogin"
              class="w-full h-12 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl shadow-lg shadow-emerald-600/30 transition-all duration-200"
            >
              Ir al Login
            </button>

            <p class="text-xs text-gray-500 text-center mt-4">
              Se te redireccionará en <span class="font-bold">{{ countdownSeconds }}</span> segundos
            </p>
          </div>
        </div>
      </transition>
    </div>
  </transition>

  <!-- 🏦 FONDO PROFESIONAL GRIS -->
  <div class="min-h-screen bg-gradient-to-b from-slate-50 via-slate-100 to-slate-200 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-6xl mx-auto">
      
      <!-- 📋 HEADER: Centrado y Elegante -->
      <div class="text-center mb-12 animate-fade-in">
        <h1 class="text-4xl lg:text-5xl font-bold text-slate-900 mb-4 tracking-tight">
          Elige el plan ideal para tu negocio
        </h1>
        <p v-if="companyName" class="text-lg text-slate-600 max-w-2xl mx-auto mb-3">
          Configura la suscripción perfecta para <span class="font-bold text-slate-900">{{ companyName }}</span>
        </p>
        <p class="text-sm text-slate-500">
          Sin permanencia • Cancela cuando quieras • Soporte incluido
        </p>
      </div>

      <!-- 💰 SELECTOR DE PERÍODO -->
      <div class="flex flex-col items-center mb-10 gap-3">
        <div class="relative">
          <select 
            v-model="paymentFrequency"
            class="px-6 py-3 text-base font-semibold bg-white border border-slate-300 rounded-xl appearance-none pr-12 cursor-pointer hover:border-slate-400 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-slate-400 focus:border-slate-400 text-slate-900 shadow-sm"
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
        
        <!-- Badge Promoción 24 meses -->
        <div v-if="paymentFrequency === '24months'" class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-50 text-emerald-700 rounded-lg text-sm font-semibold border border-emerald-200">
          <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
          </svg>
          2 meses gratis incluidos
        </div>
      </div>

      <!-- 🏢 GRID DE PLANES: Centrado y Alineado -->
      <div class="flex justify-center">
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-5 w-full max-w-6xl items-start">
        
        <!-- 🎁 PLAN TRIAL: 3 Días Gratis (Oculto en renovación) -->
        <div 
          v-if="!isRenewalMode"
          @click="selectedPlan = 'trial_express'"
          class="relative bg-white rounded-2xl border shadow-sm transition-all duration-300 cursor-pointer p-5 hover:shadow-lg flex flex-col min-h-[420px]"
          :class="selectedPlan === 'trial_express' ? 'border-slate-400 ring-2 ring-slate-200 shadow-md' : 'border-slate-200 hover:border-slate-300'"
        >
          <!-- Radio Button -->
          <div class="absolute top-5 right-5 w-5 h-5 rounded-full border-2 flex items-center justify-center transition-all"
               :class="selectedPlan === 'trial_express' ? 'border-slate-900 bg-slate-900' : 'border-slate-300'">
            <svg v-if="selectedPlan === 'trial_express'" class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3">
              <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path>
            </svg>
          </div>

          <div class="pr-6">
            <!-- Título del Plan -->
            <h3 class="text-lg font-bold text-slate-900 mb-1">Prueba 3 Días</h3>
            <p class="text-sm text-slate-500 mb-5">Prueba todo sin compromiso.</p>

            <!-- Precio -->
            <div class="mb-5">
              <div class="flex items-baseline gap-1 mb-1">
                <span class="text-4xl font-bold text-slate-900">$0</span>
              </div>
              <p class="text-xs text-slate-500 font-medium">
                Sin tarjeta de crédito
              </p>
            </div>

            <!-- Botón Secundario -->
            <button
              type="button"
              @click.stop="handlePlanSelection('trial_express')"
              :disabled="isProcessing"
              class="w-full h-11 px-5 text-sm font-semibold rounded-xl transition-all duration-200 mb-6 bg-slate-100 hover:bg-slate-200 text-slate-900 border border-slate-200 disabled:opacity-50"
            >
              {{ isProcessing ? 'Procesando...' : 'Activar Trial' }}
            </button>

            <!-- Lista de Características -->
            <div class="space-y-3 text-sm mt-auto">
              <div class="flex items-center gap-2.5 text-slate-600">
                <svg class="w-4 h-4 text-emerald-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                </svg>
                <span>Todas las funciones</span>
              </div>
              <div class="flex items-center gap-2.5 text-slate-600">
                <svg class="w-4 h-4 text-emerald-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                </svg>
                <span>Sin límites</span>
              </div>
              <div class="flex items-center gap-2.5 text-slate-600">
                <svg class="w-4 h-4 text-emerald-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                </svg>
                <span>Soporte por email</span>
              </div>
              <div class="flex items-center gap-2.5 text-slate-600">
                <svg class="w-4 h-4 text-emerald-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                </svg>
                <span>No requiere pago</span>
              </div>
            </div>
          </div>
        </div>
        
        <!-- 💼 PLAN BASIC -->
        <div 
          @click="selectedPlan = 'basic'"
          class="relative bg-white rounded-2xl border shadow-sm transition-all duration-300 cursor-pointer p-5 flex flex-col min-h-[420px]"
          :class="selectedPlan === 'basic' ? 'border-slate-400 ring-2 ring-slate-200 shadow-md' : 'border-slate-200 hover:border-slate-300 hover:shadow-lg'"
        >
          <!-- Radio Button -->
          <div class="absolute top-5 right-5 w-5 h-5 rounded-full border-2 flex items-center justify-center transition-all"
               :class="selectedPlan === 'basic' ? 'border-slate-900 bg-slate-900' : 'border-slate-300'">
            <svg v-if="selectedPlan === 'basic'" class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3">
              <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path>
            </svg>
          </div>

          <div class="pr-6">
            <!-- Título del Plan -->
            <h3 class="text-lg font-bold text-slate-900 mb-1">Basic</h3>
            <p class="text-sm text-slate-500 mb-5">Para negocios que inician.</p>

            <!-- Precio -->
            <div class="mb-5">
              <div class="flex items-baseline gap-1 mb-1">
                <span class="text-4xl font-bold text-slate-900">
                  {{ paymentFrequency === '24months' ? '$20.000' : (paymentFrequency === 'yearly' ? '$20.000' : '$25.000') }}
                </span>
                <span class="text-sm text-slate-500">/mes</span>
              </div>
              <p v-if="paymentFrequency === '24months'" class="text-xs text-emerald-600 font-medium">
                Facturado $480.000/24 meses
              </p>
              <p v-else-if="paymentFrequency === 'yearly'" class="text-xs text-emerald-600 font-medium">
                Facturado $240.000/año
              </p>
              <p v-else class="text-xs text-slate-500 font-medium">
                Facturado mensualmente
              </p>
            </div>

            <!-- Botón -->
            <button
              type="button"
              @click.stop="handlePlanSelection('basic')"
              :disabled="isProcessing"
              class="w-full h-11 px-5 text-sm font-semibold rounded-xl transition-all duration-200 mb-6 bg-slate-900 hover:bg-black text-white shadow-sm disabled:opacity-50"
            >
              {{ isProcessing ? 'Procesando...' : 'Comprar Plan' }}
            </button>

            <!-- Lista de Características -->
            <div class="space-y-3 text-sm mt-auto">
              <div class="flex items-center gap-2.5 text-slate-600">
                <svg class="w-4 h-4 text-emerald-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                </svg>
                <span>1 Usuario Administrador</span>
              </div>
              <div class="flex items-center gap-2.5 text-slate-600">
                <svg class="w-4 h-4 text-emerald-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                </svg>
                <span>Punto de Venta (POS) Web</span>
              </div>
              <div class="flex items-center gap-2.5 text-slate-600">
                <svg class="w-4 h-4 text-emerald-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                </svg>
                <span>Gestión de Inventario</span>
              </div>
              <div class="flex items-center gap-2.5 text-slate-600">
                <svg class="w-4 h-4 text-emerald-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                </svg>
                <span>Reportes de Venta</span>
              </div>
              <div class="flex items-center gap-2.5 text-slate-600">
                <svg class="w-4 h-4 text-emerald-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                </svg>
                <span>Soporte por Email</span>
              </div>
              <div class="flex items-center gap-2.5 text-slate-600">
                <svg class="w-4 h-4 text-emerald-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                </svg>
                <span>Modo Offline</span>
              </div>
            </div>
          </div>
        </div>

        <!-- ⭐ PLAN PREMIUM: Destacado -->
        <div 
          @click="selectedPlan = 'premium'"
          class="relative bg-white rounded-2xl border-2 border-emerald-500 shadow-lg transition-all duration-300 cursor-pointer p-5 flex flex-col min-h-[420px] ring-1 ring-emerald-500/20"
          :class="selectedPlan === 'premium' ? 'shadow-xl ring-2' : 'hover:shadow-xl'"
        >
          <!-- Badge "Más Vendido" -->
          <div class="absolute -top-3 left-6">
            <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-emerald-500 text-white text-xs font-bold uppercase tracking-wide rounded-full shadow-sm">
              <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
              </svg>
              Más Vendido
            </span>
          </div>

          <!-- Radio Button -->
          <div class="absolute top-5 right-5 w-5 h-5 rounded-full border-2 flex items-center justify-center transition-all"
               :class="selectedPlan === 'premium' ? 'border-emerald-500 bg-emerald-500' : 'border-slate-300'">
            <svg v-if="selectedPlan === 'premium'" class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3">
              <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path>
            </svg>
          </div>

          <div class="pr-6 pt-3">
            <!-- Título del Plan -->
            <h3 class="text-lg font-bold text-slate-900 mb-1">Premium</h3>
            <p class="text-sm text-slate-500 mb-5">Automatización total + IA.</p>

            <!-- Precio -->
            <div class="mb-4">
              <div class="flex items-baseline gap-1 mb-1">
                <span class="text-4xl font-bold text-slate-900">
                  {{ includeDianInvoicing 
                    ? (paymentFrequency === '24months' ? '$80.000' : (paymentFrequency === 'yearly' ? '$80.000' : '$90.000'))
                    : (paymentFrequency === '24months' ? '$50.000' : (paymentFrequency === 'yearly' ? '$50.000' : '$60.000')) 
                  }}
                </span>
                <span class="text-sm text-slate-500">/mes</span>
              </div>
              <p v-if="paymentFrequency === '24months'" class="text-xs text-emerald-600 font-medium">
                Facturado cada 24 meses
              </p>
              <p v-else-if="paymentFrequency === 'yearly'" class="text-xs text-emerald-600 font-medium">
                Facturado anualmente
              </p>
              <p v-else class="text-xs text-slate-500 font-medium">
                Facturado mensualmente
              </p>
            </div>

            <!-- Add-on Facturación DIAN -->
            <div class="mb-4 p-2.5 bg-emerald-50 border border-emerald-200 rounded-lg">
              <label class="flex items-center gap-2.5 cursor-pointer">
                <input 
                  type="checkbox" 
                  v-model="includeDianInvoicing"
                  class="w-4 h-4 text-emerald-600 border-slate-300 rounded focus:ring-emerald-500"
                >
                <div class="flex-1 text-sm">
                  <span class="font-medium text-slate-700">Facturación DIAN</span>
                  <span class="text-emerald-600 font-bold ml-1">+$30.000</span>
                </div>
              </label>
            </div>

            <!-- Botón -->
            <button
              type="button"
              @click.stop="handlePlanSelection('premium')"
              :disabled="isProcessing"
              class="w-full h-11 px-5 text-sm font-semibold rounded-xl transition-all duration-200 mb-6 bg-emerald-600 hover:bg-emerald-700 text-white shadow-md disabled:opacity-50"
            >
              {{ isProcessing ? 'Procesando...' : 'Comprar Ahora' }}
            </button>

            <!-- Lista de Características -->
            <div class="space-y-3 text-sm mt-auto">
              <p class="font-semibold text-slate-700 text-xs uppercase tracking-wide">Todo lo de Basic, más:</p>
              
              <div v-if="includeDianInvoicing" class="flex items-center gap-2.5 text-emerald-700 bg-emerald-50 -mx-1 px-1.5 py-0.5 rounded">
                <svg class="w-4 h-4 text-emerald-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                </svg>
                <span class="font-medium">Facturación DIAN</span>
              </div>
              
              <div class="flex items-center gap-2.5 text-slate-600">
                <svg class="w-4 h-4 text-emerald-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                </svg>
                <span>3 Usuarios / 2 Bodegas</span>
              </div>
              <div class="flex items-center gap-2.5 text-slate-600">
                <svg class="w-4 h-4 text-emerald-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                </svg>
                <span>Tienda Web</span>
              </div>
              <div class="flex items-start gap-3 text-slate-700">
                <svg class="w-5 h-5 text-emerald-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                </svg>
                <span>Envíos WhatsApp</span>
              </div>
              <div class="flex items-center gap-2.5 text-slate-600">
                <svg class="w-4 h-4 text-emerald-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                </svg>
                <span>Agente IA</span>
              </div>
              <div class="flex items-center gap-2.5 text-slate-600">
                <svg class="w-4 h-4 text-emerald-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                </svg>
                <span>Fidelización</span>
              </div>
              <div class="flex items-center gap-2.5 text-slate-600">
                <svg class="w-4 h-4 text-emerald-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                </svg>
                <span>CRM Automático</span>
              </div>
              <div class="flex items-center gap-2.5 text-slate-600">
                <svg class="w-4 h-4 text-emerald-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                </svg>
                <span>Creditienda</span>
              </div>
            </div>
          </div>
        </div>

        <!-- 🏢 PLAN ENTERPRISE -->
        <div 
          @click="selectedPlan = 'enterprise'"
          class="relative bg-white rounded-2xl border shadow-sm transition-all duration-300 cursor-pointer p-5 flex flex-col min-h-[420px]"
          :class="selectedPlan === 'enterprise' ? 'border-slate-400 ring-2 ring-slate-200 shadow-md' : 'border-slate-200 hover:border-slate-300 hover:shadow-lg'"
        >
          <!-- Radio Button -->
          <div class="absolute top-5 right-5 w-5 h-5 rounded-full border-2 flex items-center justify-center transition-all"
               :class="selectedPlan === 'enterprise' ? 'border-slate-900 bg-slate-900' : 'border-slate-300'">
            <svg v-if="selectedPlan === 'enterprise'" class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3">
              <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path>
            </svg>
          </div>

          <div class="pr-6">
            <!-- Título del Plan -->
            <h3 class="text-lg font-bold text-slate-900 mb-1">Corporativo</h3>
            <p class="text-sm text-slate-500 mb-5">Para empresas grandes.</p>

            <!-- Precio -->
            <div class="mb-5">
              <div class="flex items-baseline gap-1 mb-1">
                <span class="text-4xl font-bold text-slate-900">
                  {{ paymentFrequency === '24months' ? '$120.000' : (paymentFrequency === 'yearly' ? '$120.000' : '$150.000') }}
                </span>
                <span class="text-sm text-slate-500">/mes</span>
              </div>
              <p v-if="paymentFrequency === '24months'" class="text-xs text-emerald-600 font-medium">
                Facturado cada 24 meses
              </p>
              <p v-else-if="paymentFrequency === 'yearly'" class="text-xs text-emerald-600 font-medium">
                Facturado anualmente
              </p>
              <p v-else class="text-xs text-slate-500 font-medium">
                Facturado mensualmente
              </p>
            </div>

            <!-- Botón -->
            <button
              type="button"
              @click.stop="handlePlanSelection('enterprise')"
              :disabled="isProcessing"
              class="w-full h-11 px-5 text-sm font-semibold rounded-xl transition-all duration-200 mb-6 bg-slate-900 hover:bg-black text-white shadow-sm disabled:opacity-50"
            >
              {{ isProcessing ? 'Procesando...' : 'Comprar Ahora' }}
            </button>

            <!-- Lista de Características -->
            <div class="space-y-3 text-sm mt-auto">
              <p class="font-semibold text-slate-700 text-xs uppercase tracking-wide">Todo lo de Premium, más:</p>
              
              <div class="flex items-center gap-2.5 text-slate-600">
                <svg class="w-4 h-4 text-emerald-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                </svg>
                <span>Usuarios Ilimitados</span>
              </div>
              <div class="flex items-center gap-2.5 text-slate-600">
                <svg class="w-4 h-4 text-emerald-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                </svg>
                <span>Multi-Sede</span>
              </div>
              <div class="flex items-center gap-2.5 text-slate-600">
                <svg class="w-4 h-4 text-emerald-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                </svg>
                <span>API Integración</span>
              </div>
              <div class="flex items-center gap-2.5 text-slate-600">
                <svg class="w-4 h-4 text-emerald-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                </svg>
                <span>IA Predictiva</span>
              </div>
              <div class="flex items-center gap-2.5 text-slate-600">
                <svg class="w-4 h-4 text-emerald-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                </svg>
                <span>Gerente Dedicado</span>
              </div>
            </div>
          </div>
        </div>

        </div>
      </div>

      <!-- Trust Badges: Discreto y Profesional -->
      <div class="flex flex-wrap items-center justify-center gap-6 mt-10 mb-8 text-xs font-medium text-slate-500">
        <div class="flex items-center gap-2">
          <svg class="w-4 h-4 text-slate-400" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
          </svg>
          <span>Pago Seguro SSL</span>
        </div>
        <span class="text-slate-300">•</span>
        <div class="flex items-center gap-2">
          <svg class="w-4 h-4 text-slate-400" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
          </svg>
          <span>Garantía de 3 días</span>
        </div>
        <span class="text-slate-300">•</span>
        <div class="flex items-center gap-2">
          <svg class="w-4 h-4 text-slate-400" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-8.707l-3-3a1 1 0 00-1.414 0l-3 3a1 1 0 001.414 1.414L9 9.414V13a1 1 0 102 0V9.414l1.293 1.293a1 1 0 001.414-1.414z" clip-rule="evenodd"/>
          </svg>
          <span>Cancela cuando quieras</span>
        </div>
      </div>

    </div> <!-- Cierre max-w-7xl mx-auto -->
  </div> <!-- Cierre min-h-screen bg-slate-50 -->
  </div> <!-- Cierre wrapper principal -->
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'
import authService from '../services/authService'
import { appStore } from '../store/appStore'

// Estado
const selectedPlan = ref(null)
const paymentFrequency = ref('monthly')
const includeDianInvoicing = ref(false)
const isProcessing = ref(false)
const companyName = ref('')
const tenantId = ref(null)
const redirectUrl = ref('')
const showTrialSuccessModal = ref(false)
const countdownSeconds = ref(5)
const isRenewalMode = ref(false)

// Detectar ambiente de desarrollo
const isDevelopment = ref(
  window.location.hostname === 'localhost' || 
  window.location.hostname === '127.0.0.1'
)

// Cargar datos del registro desde URL params (cross-domain) o localStorage (fallback)
onMounted(async () => {
  // Limpiar flag de redirección al montar
  if (window.__redirecting_to_expired) {
    delete window.__redirecting_to_expired
  }

  // 🔍 DEBUG: Verificar estado de autenticación
  const token = localStorage.getItem('authToken')
  const userStr = localStorage.getItem('user')
  console.log('🔍 PlanSelection Debug:', { 
    token: !!token, 
    user: !!userStr, 
    isAuthenticated: authService.isAuthenticated() 
  })

  // 🚨 PRIORIDAD MÁXIMA: Leer URL params una sola vez
  const urlParams = new URLSearchParams(window.location.search)
  
  // Detectar parámetro ?renewal=true (indica renovación explícita)
  const isRenewalParam = urlParams.get('renewal') === 'true'
  if (isRenewalParam) {
    console.log('✅ Parámetro ?renewal=true detectado - MODO RENOVACIÓN ACTIVADO')
    isRenewalMode.value = true
  }
  
  // Leer parámetros de registro (cross-domain)
  const tenantIdParam = urlParams.get('tenant_id')
  const companyParam = urlParams.get('company')
  const subdomainParam = urlParams.get('subdomain')
  const redirectParam = urlParams.get('redirect_url')

  // 🔑 PRIORIDAD 0: Verificar si es usuario autenticado (Renovación)
  // Usar verificación directa de token como fallback si authService falla
  const isAuthenticatedUser = authService.isAuthenticated() || !!token
  
  if (isAuthenticatedUser) {
    console.log('🔄 Modo Renovación Detectado')
    isRenewalMode.value = true
    
    // Intentar obtener datos del store o authService
    const user = authService.getUser() || (userStr ? JSON.parse(userStr) : null)
    
    // Si tenemos el tenant en el store
    if (appStore.tenant && appStore.tenant.id) {
      tenantId.value = appStore.tenant.id
      companyName.value = appStore.businessName || 'Mi Negocio'
    } else if (user && user.tenant_id) {
      // Fallback al usuario
      tenantId.value = user.tenant_id
      companyName.value = user.company_name || 'Mi Negocio'
    }
    
    // Si no tenemos tenantId, intentar cargar settings
    if (!tenantId.value) {
      try {
        await appStore.loadSystemSettings(true)
        if (appStore.tenant && appStore.tenant.id) {
          tenantId.value = appStore.tenant.id
          companyName.value = appStore.businessName
        }
      } catch (e) {
        console.error('Error cargando datos para renovación', e)
        // Ignorar errores 403 (suscripción expirada) - es esperado en modo renovación
        if (e.response?.status !== 403) {
          console.error('Error no relacionado con suscripción:', e)
        }
      }
    }
    
    // Si aún no tenemos tenantId pero tenemos usuario, intentar usar el del usuario
    if (!tenantId.value && user && user.tenant_id) {
       tenantId.value = user.tenant_id
    }

    if (tenantId.value) {
      console.log('✅ Modo Renovación configurado con Tenant ID:', tenantId.value)
      return // Ya tenemos los datos necesarios
    } else {
      console.warn('⚠️ Modo renovación activo pero no se encontró tenantId')
      // Intentar recuperar del localStorage como último recurso
      const storedUser = localStorage.getItem('user')
      if (storedUser) {
        try {
          const u = JSON.parse(storedUser)
          if (u.tenant_id) {
            tenantId.value = u.tenant_id
            console.log('✅ TenantId recuperado de localStorage (fallback final):', tenantId.value)
            return
          }
        } catch (e) { console.error(e) }
      }
      
      // ⚠️ Si llegamos aquí en modo renovación, es un error crítico
      console.error('❌ Error crítico: Modo renovación activo pero sin Tenant ID')
      alert('Error: No se pudo identificar tu cuenta. Por favor contacta a soporte.')
      return // NO redirigir a /register
    }
  }

  // 🔑 PRIORIDAD 1: Si hay tenant_id en URL params (cuando vienes del registro en otro dominio)
  if (tenantIdParam) {
    // Datos vienen de URL params (cross-domain desde registro)
    console.log('📦 Cargando datos desde URL params')
    companyName.value = companyParam || ''
    tenantId.value = tenantIdParam
    redirectUrl.value = redirectParam || ''
    
    // Guardar en localStorage del subdominio para futuros usos
    const registrationData = {
      company_name: companyParam,
      tenant_id: tenantIdParam,
      subdomain: subdomainParam || tenantIdParam, // 🔥 Usar subdomain si existe, sino tenant_id
      redirect_url: redirectParam
    }
    localStorage.setItem('registration_data', JSON.stringify(registrationData))
    return
  }
  
  // 🔑 PRIORIDAD 2: Leer desde localStorage (mismo dominio)
  const registrationData = localStorage.getItem('registration_data')
  if (registrationData) {
    console.log('📦 Cargando datos desde localStorage')
    const data = JSON.parse(registrationData)
    companyName.value = data.company_name || ''
    tenantId.value = data.tenant_id || null
    redirectUrl.value = data.redirect_url || ''
    
    // ✅ Si no tiene subdomain pero tiene tenant_id, usar tenant_id como subdomain
    if (!data.subdomain && data.tenant_id) {
      data.subdomain = data.tenant_id
      localStorage.setItem('registration_data', JSON.stringify(data))
    }
    return
  }
  
  // ⚠️ VALIDACIÓN FINAL: Solo redirigir si NO es renovación Y NO está autenticado
  if (isRenewalMode.value) {
    // ✅ MODO RENOVACIÓN: No hacer validaciones adicionales
    console.log('✅ Modo Renovación - Saltando validaciones de registro')
    // Si aún no tenemos tenantId, no es problema - se obtendrá al seleccionar el plan
    if (!tenantId.value) {
      console.warn('⚠️ Renovación sin TenantId - se obtendrá después')
    }
    return // NO ejecutar más validaciones
  }
  
  // ✅ FIX CRÍTICO: NUNCA redirigir si el usuario está autenticado
  if (!isAuthenticatedUser) {
    console.warn('⚠️ No hay datos de registro y no está autenticado - redirigiendo a /register')
    window.location.href = '/register'
  } else if (isAuthenticatedUser && !tenantId.value) {
    // Usuario autenticado pero sin datos - error crítico
    console.error('❌ Error: Usuario autenticado pero no se pudo obtener Tenant ID')
    alert('Error: No se pudo identificar tu cuenta. Por favor intenta cerrar sesión y volver a entrar.')
  }
})

// Manejo de selección de plan
const handlePlanSelection = async (plan) => {
  if (isProcessing.value) return
  
  selectedPlan.value = plan
  
  // Si es trial, activar directamente
  if (plan === 'trial_express') {
    try {
      isProcessing.value = true
      
      // 🔥 VALIDACIÓN: Trial NO está disponible en modo renovación
      if (isRenewalMode.value) {
        alert('❌ El plan de prueba no está disponible para renovación.\n\nPor favor selecciona uno de los planes de pago.')
        return
      }
      
      await updateTenantPlan(plan)
    } catch (error) {
      console.error('Error activating trial:', error)
      
      // Mostrar error detallado
      const errorMessage = error.response?.data?.message || 'Error al activar el trial'
      const errorDetails = error.response?.data?.errors ? JSON.stringify(error.response.data.errors) : ''
      
      alert(`${errorMessage}${errorDetails ? '\n\n' + errorDetails : ''}. Por favor, intenta nuevamente.`)
    } finally {
      isProcessing.value = false
    }
    return
  }
  
  // Para planes de pago, procesar pago con ePayco
  try {
    isProcessing.value = true
    
    // 🔥 VALIDACIÓN CRÍTICA: Asegurar que tenemos tenantId en modo renovación
    if (isRenewalMode.value && !tenantId.value) {
      console.warn('⚠️ Modo renovación sin tenantId - obteniendo desde múltiples fuentes...')
      
      // PRIORIDAD 1: TenantId guardado específicamente para renovación
      tenantId.value = localStorage.getItem('expiredTenantId')
      if (tenantId.value) {
        console.log('✅ TenantId recuperado desde expiredTenantId:', tenantId.value)
      } else {
        // PRIORIDAD 2: Usuario en localStorage
        let user = authService.getUser()
        console.log('👤 Usuario desde localStorage:', user)
        
        // PRIORIDAD 3: Si no hay usuario en localStorage, intentar obtenerlo del backend
        if (!user) {
          console.log('🔄 Usuario no en localStorage, consultando backend /me...')
          try {
            const response = await authService.getCurrentUser()
            if (response.success && response.data?.user) {
              user = response.data.user
              console.log('✅ Usuario obtenido del backend:', user)
              localStorage.setItem('user', JSON.stringify(user))
            }
          } catch (error) {
            console.error('❌ Error obteniendo usuario del backend:', error)
          }
        }
        
        // PRIORIDAD 4: Intentar extraer tenant_id del usuario
        if (user) {
          tenantId.value = user.tenant_id || user.tenantId || localStorage.getItem('tenantId')
        }
        
        // PRIORIDAD 5: Si aún no tenemos tenant_id, intentar obtenerlo del subdominio
        if (!tenantId.value) {
          const subdomain = window.location.hostname.split('.')[0]
          if (subdomain && subdomain !== 'localhost' && subdomain !== '127') {
            console.log('🌐 Obteniendo tenant desde subdominio:', subdomain)
            tenantId.value = subdomain
          }
        }
        
        if (tenantId.value) {
          console.log('✅ TenantId obtenido:', tenantId.value)
        } else {
          throw new Error('No se pudo identificar tu cuenta. Por favor intenta cerrar sesión y volver a entrar.')
        }
      }
    }
    
    // Validar que tenemos tenantId antes de continuar
    if (!tenantId.value) {
      throw new Error('Error: No se pudo identificar el tenant. Por favor recarga la página.')
    }
    
    // Calcular precio final
    const basePrice = calculatePlanPrice(plan)
    
    // Calcular adicional DIAN según período
    let dianExtra = 0
    if (includeDianInvoicing.value && plan === 'premium') {
      if (paymentFrequency.value === 'monthly') {
        dianExtra = 30000 // $30k/mes
      } else if (paymentFrequency.value === 'yearly') {
        dianExtra = 30000 * 12 // $30k × 12 meses = $360k
      } else if (paymentFrequency.value === '24months') {
        dianExtra = 30000 * 24 // $30k × 24 meses = $720k
      }
    }
    
    const finalPrice = basePrice + dianExtra
    
    // Generar referencia única
    const reference = `plan_${tenantId.value}_${Date.now()}`
    
    // 🔥 Obtener subdomain del tenant desde localStorage
    const registrationData = localStorage.getItem('registration_data')
    let tenantSubdomain = ''
    
    if (isRenewalMode.value) {
      // En modo renovación, el subdominio es el actual o el tenant_id
      tenantSubdomain = window.location.hostname.split('.')[0]
      if (tenantSubdomain === 'localhost' || tenantSubdomain === 'www') {
        tenantSubdomain = tenantId.value
      }
    } else if (registrationData) {
      const data = JSON.parse(registrationData)
      tenantSubdomain = data.subdomain || ''
    }
    // 🔥 Determinar URL de redirección correcta basada en el entorno
    const getRedirectUrl = () => {
      // 🎯 SIEMPRE redirigir a /payment/verify para verificar el estado real
      // ePayco puede decir "rechazada" antes de que llegue el webhook
      const baseParams = `tenant_id=${encodeURIComponent(tenantId.value)}&plan=${encodeURIComponent(plan)}&reference=${encodeURIComponent(reference)}&subdomain=${encodeURIComponent(tenantSubdomain)}`
      
      // Si es renovación, agregar parámetro adicional
      if (isRenewalMode.value) {
         const protocol = window.location.protocol
         const host = window.location.host
         return `${protocol}//${host}/payment/verify?${baseParams}&renewal=true`
      }

      if (window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1') {
        return `http://localhost:3000/payment/verify?${baseParams}`
      }
      return `https://105pos.pro/payment/verify?${baseParams}`
    }

    // Inicializar transacción en backend (guardar pending payment)
    const initResponse = await axios.post('/api/epayco/init-transaction', {
      amount: finalPrice,
      reference: reference,
      customer_email: localStorage.getItem('user_email') || 'cliente@105pos.pro',
      payment_frequency: paymentFrequency.value,
      plan: plan,
      tenant_id: tenantId.value
    })

    console.log('✅ Transacción inicializada:', initResponse.data)

    // 🚀 Construir URL de ePayco directamente (más rápido que SDK)
    const epaycoParams = new URLSearchParams({
      'p_cust_id_cliente': '1569644',
      'p_key': 'bbc93c88d4780f0898bbe4e9ed29e6bc8e33ca72',
      'p_id_invoice': reference,
      'p_description': `Plan ${plan} - ${paymentFrequency.value}`,
      'p_amount': finalPrice.toString(),
      'p_amount_base': finalPrice.toString(),
      'p_tax': '0',
      'p_currency_code': 'COP',
      'p_test_request': 'true',
      'p_extra1': tenantId.value,
      'p_extra2': plan,
      'p_extra3': paymentFrequency.value,
      'p_url_response': getRedirectUrl(),
      'p_url_confirmation': window.location.hostname === 'localhost' ? 'http://localhost:3000/api/epayco/webhook' : 'https://105pos.pro/api/epayco/webhook',
      'p_name_billing': (companyName.value || 'Cliente 105POS').replace(/[^a-zA-Z0-9\s]/g, '').substring(0, 50),
      'p_address_billing': 'Calle 123',
      'p_type_doc_billing': 'cc',
      'p_number_doc_billing': '1234567890',
      'p_email_billing': localStorage.getItem('user_email') || 'cliente@105pos.pro',
      'p_mobilephone_billing': '3000000000'
    })

    const epaycoUrl = `https://checkout.epayco.co/checkout.php?${epaycoParams.toString()}`
    
    console.log('🌐 URL ePayco generada:', epaycoUrl)
    console.log('📍 URL de respuesta:', getRedirectUrl())
    
    // Guardar referencia en localStorage ANTES de redirigir
    localStorage.setItem('pending_payment', JSON.stringify({
      reference: reference,
      plan: plan,
      tenant_id: tenantId.value,
      amount: finalPrice
    }))
    
    // Redirigir después de un pequeño delay para asegurar que el localStorage se guarde
    setTimeout(() => {
      window.location.href = epaycoUrl
    }, 100)

  } catch (error) {
    console.error('Error processing payment:', error)
    
    // Mostrar error del servidor o mensaje genérico
    const errorMessage = error.response?.data?.error || error.message || 'Por favor, intenta nuevamente.'
    alert('❌ Error al procesar el pago\n\n' + errorMessage)
  } finally {
    isProcessing.value = false
  }
}

// Calcular precio del plan según frecuencia de pago
const calculatePlanPrice = (plan) => {
  const prices = {
    basic: {
      monthly: 25000,
      yearly: 240000, // 20k/mes * 12
      '24months': 480000 // 20k/mes * 24
    },
    premium: {
      monthly: 60000,
      yearly: 600000, // 50k/mes * 12
      '24months': 1200000 // 50k/mes * 24
    },
    enterprise: {
      monthly: 150000,
      yearly: 1440000, // 120k/mes * 12
      '24months': 2880000 // 120k/mes * 24
    }
  }
  
  return prices[plan]?.[paymentFrequency.value] || 0
}

// Activar plan en el tenant
const updateTenantPlan = async (plan) => {
  try {
    const response = await axios.post('/api/update-tenant-plan', {
      tenant_id: tenantId.value,
      plan: plan
    })

    console.log('Plan activado:', response.data)

    // Limpiar localStorage
    localStorage.removeItem('registration_data')

    // Si es trial, mostrar modal de éxito
    if (plan === 'trial_express') {
      showTrialSuccessModal.value = true
      countdownSeconds.value = 5
      
      // Iniciar countdown
      const countdownInterval = setInterval(() => {
        countdownSeconds.value--
        if (countdownSeconds.value <= 0) {
          clearInterval(countdownInterval)
          redirectToLogin()
        }
      }, 1000)
      
      // Guardar mensaje de éxito para después
      localStorage.setItem('registration_success', JSON.stringify({
        message: 'Trial activado. Inicia sesión para comenzar.',
        companyName: companyName.value,
        subdomain: redirectUrl.value
      }))
    } else {
      // Para planes de pago, redirigir al tenant directamente
      if (redirectUrl.value) {
        window.location.href = redirectUrl.value
      } else {
        alert('Plan activado exitosamente')
      }
    }
    
  } catch (error) {
    console.error('Error updating tenant plan:', error)
    console.error('Error details:', error.response?.data)
    throw error
  }
}

// Función para redirigir al login
const redirectToLogin = () => {
  // Obtener subdomain desde URL params o localStorage
  const urlParams = new URLSearchParams(window.location.search)
  let subdomain = urlParams.get('subdomain')

  if (!subdomain) {
    const registrationData = localStorage.getItem('registration_data')
    if (registrationData) {
      const data = JSON.parse(registrationData)
      subdomain = data.subdomain
    }
  }

  // Redirigir al login con subdomain
  if (subdomain) {
    // Si estamos en localhost, usar subdominio
    if (window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1') {
      window.location.href = `http://${subdomain}.localhost:3000/login`
    } else {
      // En producción
      window.location.href = `https://${subdomain}.105pos.pro/login`
    }
  } else {
    // Fallback sin subdominio
    window.location.href = '/login'
  }
}
</script>
