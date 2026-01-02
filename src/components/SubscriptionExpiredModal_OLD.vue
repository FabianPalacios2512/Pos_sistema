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
            Selecciona tu Plan
          </h3>

          <!-- Grid de 3 Planes -->
          <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
            
            <!-- ✨ PLAN BASIC -->
            <button
              @click="selectedPlan = 'basic'"
              class="p-6 rounded-xl border-2 transition-all duration-200 text-left hover:shadow-md dark:hover:shadow-black/30"
              :class="selectedPlan === 'basic' 
                ? 'border-blue-500 dark:border-blue-400 bg-blue-50 dark:bg-blue-950/30 shadow-md dark:shadow-black/30' 
                : 'border-gray-200 dark:border-zinc-700 bg-white dark:bg-zinc-800/50 hover:border-blue-300 dark:hover:border-blue-600'"
            >
              <div class="flex items-center justify-between mb-4">
                <h4 class="text-xl font-bold text-gray-900 dark:text-white">Basic</h4>
                <div v-if="selectedPlan === 'basic'" class="w-6 h-6 bg-blue-600 dark:bg-blue-500 rounded-full flex items-center justify-center">
                  <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                  </svg>
                </div>
              </div>
              <p class="text-sm text-gray-600 dark:text-zinc-400 mb-4">Para negocios que inician y quieren orden.</p>
              <p class="text-4xl font-bold text-blue-600 dark:text-blue-400 mb-4">
                $25.000<span class="text-base font-normal text-gray-500 dark:text-zinc-400">/mes</span>
              </p>
              <ul class="space-y-2.5 text-sm text-gray-600 dark:text-zinc-300">
                <li class="flex items-center gap-2">
                  <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                  </svg>
                  1 Usuario Administrador
                </li>
                <li class="flex items-center gap-2">
                  <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                  </svg>
                  Punto de Venta (POS) Web
                </li>
                <li class="flex items-center gap-2">
                  <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                  </svg>
                  Gestión de Inventario
                </li>
                <li class="flex items-center gap-2">
                  <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                  </svg>
                  Reportes Básicos
                </li>
                <li class="flex items-center gap-2">
                  <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                  </svg>
                  Soporte por Email
                </li>
              </ul>
            </button>

            <!-- ⭐ PLAN PREMIUM (MÁS POPULAR) -->
            <button
              @click="selectedPlan = 'premium'"
              class="p-6 rounded-xl border-2 transition-all duration-200 text-left hover:shadow-md dark:hover:shadow-black/30 relative"
              :class="selectedPlan === 'premium' 
                ? 'border-emerald-500 dark:border-emerald-400 bg-emerald-50 dark:bg-emerald-950/30 shadow-lg dark:shadow-black/40 ring-2 ring-emerald-500/20' 
                : 'border-gray-200 dark:border-zinc-700 bg-white dark:bg-zinc-800/50 hover:border-emerald-300 dark:hover:border-emerald-600'"
            >
              <!-- Badge Más Vendido -->
              <div class="absolute -top-3 left-6">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-emerald-100 dark:bg-emerald-900/50 text-emerald-700 dark:text-emerald-300 text-xs font-bold uppercase tracking-wide rounded-full border border-emerald-200 dark:border-emerald-800">
                  <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                  </svg>
                  Más Vendido
                </span>
              </div>
              
              <div class="flex items-center justify-between mb-4 mt-4">
                <h4 class="text-xl font-bold text-gray-900 dark:text-white">Premium</h4>
                <div v-if="selectedPlan === 'premium'" class="w-6 h-6 bg-emerald-600 dark:bg-emerald-500 rounded-full flex items-center justify-center">
                  <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                  </svg>
                </div>
              </div>
              <p class="text-sm text-gray-600 dark:text-zinc-400 mb-4">Automatización total + IA para crecer.</p>
              <p class="text-4xl font-bold text-emerald-600 dark:text-emerald-400 mb-4">
                $60.000<span class="text-base font-normal text-gray-500 dark:text-zinc-400">/mes</span>
              </p>
              <ul class="space-y-2.5 text-sm text-gray-600 dark:text-zinc-300">
                <li class="flex items-center gap-2 font-medium text-emerald-700 dark:text-emerald-300">
                  Todo lo de Basic, más:
                </li>
                <li class="flex items-center gap-2">
                  <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                  </svg>
                  3 Usuarios / 2 Bodegas
                </li>
                <li class="flex items-center gap-2">
                  <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                  </svg>
                  Tienda Web
                </li>
                <li class="flex items-center gap-2">
                  <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                  </svg>
                  WhatsApp Automático
                </li>
                <li class="flex items-center gap-2">
                  <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                  </svg>
                  Agente IA (Promos)
                </li>
                <li class="flex items-center gap-2">
                  <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                  </svg>
                  Sistema de Puntos (CRM)
                </li>
              </ul>
            </button>

            <!-- 🏢 PLAN ENTERPRISE -->
            <button
              @click="selectedPlan = 'enterprise'"
              class="p-6 rounded-xl border-2 transition-all duration-200 text-left hover:shadow-md dark:hover:shadow-black/30"
              :class="selectedPlan === 'enterprise' 
                ? 'border-purple-500 dark:border-purple-400 bg-purple-50 dark:bg-purple-950/30 shadow-md dark:shadow-black/30' 
                : 'border-gray-200 dark:border-zinc-700 bg-white dark:bg-zinc-800/50 hover:border-purple-300 dark:hover:border-purple-600'"
            >
              <div class="flex items-center justify-between mb-4">
                <h4 class="text-xl font-bold text-gray-900 dark:text-white">Corporativo</h4>
                <div v-if="selectedPlan === 'enterprise'" class="w-6 h-6 bg-purple-600 dark:bg-purple-500 rounded-full flex items-center justify-center">
                  <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                  </svg>
                </div>
              </div>
              <p class="text-sm text-gray-600 dark:text-zinc-400 mb-4">Potencia ilimitada para empresas.</p>
              <p class="text-4xl font-bold text-purple-600 dark:text-purple-400 mb-4">
                $150.000<span class="text-base font-normal text-gray-500 dark:text-zinc-400">/mes</span>
              </p>
              <ul class="space-y-2.5 text-sm text-gray-600 dark:text-zinc-300">
                <li class="flex items-center gap-2 font-medium text-purple-700 dark:text-purple-300">
                  Todo lo de Premium, más:
                </li>
                <li class="flex items-center gap-2">
                  <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                  </svg>
                  Usuarios Ilimitados
                </li>
                <li class="flex items-center gap-2">
                  <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                  </svg>
                  Multi-Sede / Multi-Caja
                </li>
                <li class="flex items-center gap-2">
                  <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                  </svg>
                  Integraciones Custom
                </li>
                <li class="flex items-center gap-2">
                  <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                  </svg>
                  Soporte 24/7 Dedicado
                </li>
                <li class="flex items-center gap-2">
                  <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                  </svg>
                  Capacitación Personalizada
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
            Selecciona tu Plan
          </h3>

          <!-- Grid de Planes -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            
            <!-- Plan Premium -->
            <button
              @click="selectedPlan = 'premium'"
              class="p-6 rounded-xl border-2 transition-all duration-200 text-left hover:shadow-md dark:hover:shadow-black/30"
              :class="selectedPlan === 'premium' 
                ? 'border-blue-500 dark:border-blue-400 bg-blue-50 dark:bg-blue-950/30 shadow-md dark:shadow-black/30' 
                : 'border-gray-200 dark:border-zinc-700 bg-white dark:bg-zinc-800/50 hover:border-blue-300 dark:hover:border-blue-600'"
            >
              <div class="flex items-center justify-between mb-4">
                <h4 class="text-xl font-bold text-gray-900 dark:text-white">Premium</h4>
                <div v-if="selectedPlan === 'premium'" class="w-6 h-6 bg-blue-600 dark:bg-blue-500 rounded-full flex items-center justify-center">
                  <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                  </svg>
                </div>
              </div>
              <p class="text-3xl font-bold text-blue-600 dark:text-blue-400 mb-4">
                $60,000<span class="text-base font-normal text-gray-500 dark:text-zinc-400">/mes</span>
              </p>
              <ul class="space-y-2 text-sm text-gray-600 dark:text-zinc-300">
                <li class="flex items-center gap-2">
                  <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                  </svg>
                  Usuarios ilimitados
                </li>
                <li class="flex items-center gap-2">
                  <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                  </svg>
                  Productos ilimitados
                </li>
                <li class="flex items-center gap-2">
                  <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                  </svg>
                  Reportes avanzados
                </li>
                <li class="flex items-center gap-2">
                  <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                  </svg>
                  Soporte prioritario
                </li>
              </ul>
            </button>

            <!-- Plan Enterprise -->
            <button
              @click="selectedPlan = 'enterprise'"
              class="p-6 rounded-xl border-2 transition-all duration-200 text-left hover:shadow-md dark:hover:shadow-black/30"
              :class="selectedPlan === 'enterprise' 
                ? 'border-purple-500 dark:border-purple-400 bg-purple-50 dark:bg-purple-950/30 shadow-md dark:shadow-black/30' 
                : 'border-gray-200 dark:border-zinc-700 bg-white dark:bg-zinc-800/50 hover:border-purple-300 dark:hover:border-purple-600'"
            >
              <div class="flex items-center justify-between mb-4">
                <h4 class="text-xl font-bold text-gray-900 dark:text-white">Enterprise</h4>
                <div v-if="selectedPlan === 'enterprise'" class="w-6 h-6 bg-purple-600 dark:bg-purple-500 rounded-full flex items-center justify-center">
                  <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                  </svg>
                </div>
              </div>
              <p class="text-3xl font-bold text-purple-600 dark:text-purple-400 mb-4">
                $90,000<span class="text-base font-normal text-gray-500 dark:text-zinc-400">/mes</span>
              </p>
              <ul class="space-y-2 text-sm text-gray-600 dark:text-zinc-300">
                <li class="flex items-center gap-2">
                  <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                  </svg>
                  Todo de Premium
                </li>
                <li class="flex items-center gap-2">
                  <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                  </svg>
                  Multi-sede
                </li>
                <li class="flex items-center gap-2">
                  <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                  </svg>
                  API personalizada
                </li>
                <li class="flex items-center gap-2">
                  <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                  </svg>
                  Soporte 24/7
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
const selectedPlan = ref('premium') // Premium por defecto (más popular)
const showPayment = ref(false)
const isProcessing = ref(false)
const tenantId = ref(null)

// 🔒 BLOQUEO ANTI-BYPASS: Prevenir que eliminen el modal desde inspector
let modalCheckInterval = null
let keyboardBlocker = null

const blockDevTools = () => {
  // Detectar apertura de DevTools
  const threshold = 160
  const widthThreshold = window.outerWidth - window.innerWidth > threshold
  const heightThreshold = window.outerHeight - window.innerHeight > threshold
  
  if (widthThreshold || heightThreshold) {
    console.clear()
    console.log('%c⛔ ACCESO BLOQUEADO', 'color: red; font-size: 20px; font-weight: bold')
    console.log('%cTu suscripción ha expirado. Renueva para continuar.', 'color: white; font-size: 14px')
  }
}

const preventContextMenu = (e) => {
  if (showModal.value) {
    e.preventDefault()
    return false
  }
}

const preventKeyboardShortcuts = (e) => {
  if (showModal.value) {
    // Bloquear F12, Ctrl+Shift+I, Ctrl+Shift+J, Ctrl+Shift+C, Ctrl+U
    if (e.keyCode === 123 || // F12
        (e.ctrlKey && e.shiftKey && e.keyCode === 73) || // Ctrl+Shift+I
        (e.ctrlKey && e.shiftKey && e.keyCode === 74) || // Ctrl+Shift+J
        (e.ctrlKey && e.shiftKey && e.keyCode === 67) || // Ctrl+Shift+C
        (e.ctrlKey && e.keyCode === 85)) { // Ctrl+U
      e.preventDefault()
      return false
    }
    
    // Bloquear ESC
    if (e.keyCode === 27) {
      e.preventDefault()
      return false
    }
  }
}

// Verificar si la suscripción está expirada
const checkSubscriptionStatus = async () => {
  try {
    // Llamar al endpoint de verificación
    const response = await apiClient.get('/check-subscription')
    
    console.log('📊 Estado de suscripción:', response.data)
    
    // Si la respuesta indica que está expirada
    if (response.data._subscription_expired === true || response.data.is_expired === true) {
      console.log('⛔ Suscripción expirada detectada')
      appStore.isSubscriptionExpired = true
      showModal.value = true
      
      // Activar bloqueos anti-bypass
      activateAntiBypass()
      
      // Guardar tenant_id
      if (response.data._tenant_id) {
        tenantId.value = response.data._tenant_id
      } else if (response.data.tenant_id) {
        tenantId.value = response.data.tenant_id
      }
      
      console.log('✅ TenantId para renovación:', tenantId.value)
    }
  } catch (error) {
    console.error('Error verificando suscripción:', error)
    
    // Si ya sabemos que está expirada desde appStore
    if (appStore.isSubscriptionExpired) {
      showModal.value = true
      activateAntiBypass()
      
      // Intentar obtener tenant_id
      try {
        const response = await apiClient.get('/tenant-info')
        if (response.data.tenant_id) {
          tenantId.value = response.data.tenant_id
        }
      } catch (e) {
        // Fallback: usar subdomain
        const subdomain = window.location.hostname.split('.')[0]
        tenantId.value = subdomain
      }
    }
  }
}

// Activar protecciones anti-bypass
const activateAntiBypass = () => {
  // Verificar cada 100ms que el modal sigue visible
  modalCheckInterval = setInterval(() => {
    if (appStore.isSubscriptionExpired && !showModal.value) {
      showModal.value = true
      console.warn('🔒 Intento de bypass detectado - modal restaurado')
    }
    
    // Verificar que el elemento del modal existe en el DOM
    const modalElement = document.querySelector('[data-modal-subscription]')
    if (appStore.isSubscriptionExpired && !modalElement) {
      // Si alguien lo eliminó del DOM, forzar recarga
      window.location.reload()
    }
  }, 100)
  
  // Bloquear click derecho
  document.addEventListener('contextmenu', preventContextMenu)
  
  // Bloquear atajos de teclado
  document.addEventListener('keydown', preventKeyboardShortcuts)
  
  // Detectar DevTools
  setInterval(blockDevTools, 1000)
  
  // Bloquear selección de texto en el modal
  document.body.style.userSelect = 'none'
}

onMounted(() => {
  // Verificar inmediatamente
  checkSubscriptionStatus()
})

onUnmounted(() => {
  // Limpiar interval si el componente se desmonta
  if (modalCheckInterval) {
    clearInterval(modalCheckInterval)
  }
  
  // Remover event listeners
  document.removeEventListener('contextmenu', preventContextMenu)
  document.removeEventListener('keydown', preventKeyboardShortcuts)
  document.body.style.userSelect = ''
})

// Watch para detectar cambios en isSubscriptionExpired
watch(() => appStore.isSubscriptionExpired, async (newVal) => {
  if (newVal) {
    showModal.value = true
    activateAntiBypass()
    
    // Obtener tenant_id si no lo tenemos
    if (!tenantId.value) {
      try {
        const response = await apiClient.get('/tenant-info')
        if (response.data.tenant_id) {
          tenantId.value = response.data.tenant_id
        }
      } catch (error) {
        console.error('Error obteniendo tenant_id:', error)
      }
    }
  }
})

const proceedToPayment = async () => {
  if (!selectedPlan.value || !tenantId.value) {
    alert('Error: No se pudo identificar tu cuenta. Por favor recarga la página.')
    return
  }

  isProcessing.value = true

  try {
    // Calcular precio según plan
    const prices = {
      basic: 25000,
      premium: 60000,
      enterprise: 150000
    }
    const amount = prices[selectedPlan.value]

    // Generar referencia única
    const reference = `renewal_${tenantId.value}_${Date.now()}`

    // Guardar datos del pago pendiente
    localStorage.setItem('pending_payment', JSON.stringify({
      reference,
      plan: selectedPlan.value,
      tenant_id: tenantId.value,
      amount,
      is_renewal: true
    }))

    // Configurar handler de ePayco
    const handler = window.ePayco.checkout.configure({
      key: 'e893ca6c08e3caeab2da3634a25de91c', // Tu API Key de ePayco
      test: true // Cambiar a false en producción
    })

    // Abrir checkout de ePayco
    const data = {
      // Información del comercio
      name: `Plan ${selectedPlan.value.toUpperCase()}`,
      description: `Renovación de suscripción - Plan ${selectedPlan.value}`,
      invoice: reference,
      currency: 'cop',
      amount: amount,
      tax_base: '0',
      tax: '0',
      country: 'co',
      lang: 'es',

      // URLs de respuesta
      response: `${window.location.origin}/payment/success?tenant_id=${tenantId.value}&plan=${selectedPlan.value}&reference=${reference}&renewal=true`,
      confirmation: `http://localhost:8000/api/payment/webhook`,

      // Información del cliente (desde appStore si está disponible)
      name_billing: appStore.businessName || 'Cliente',
      address_billing: 'N/A',
      type_doc_billing: 'cc',
      mobilephone_billing: '3000000000',
      number_doc_billing: '1000000000',

      // Personalización
      methodsDisable: [], // Permitir todos los métodos
      extra1: tenantId.value,
      extra2: selectedPlan.value,
      extra3: 'renewal'
    }

    handler.open(data)
    showPayment.value = true

  } catch (error) {
    console.error('Error al abrir pasarela de pago:', error)
    alert('Error al procesar el pago. Por favor intenta de nuevo.')
  } finally {
    isProcessing.value = false
  }
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
