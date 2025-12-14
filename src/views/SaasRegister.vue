<template>
  <!-- 🎨 SPLIT SCREEN ENTERPRISE LAYOUT -->
  <div class="h-screen flex font-sans bg-white selection:bg-emerald-500/30 overflow-hidden relative">
    
    <!-- 🔔 TOAST NOTIFICATION: Google Success (Esquina Superior Derecha) -->
    <transition
      enter-active-class="transition ease-out duration-300"
      enter-from-class="translate-x-full opacity-0"
      enter-to-class="translate-x-0 opacity-100"
      leave-active-class="transition ease-in duration-200"
      leave-from-class="translate-x-0 opacity-100"
      leave-to-class="translate-x-full opacity-0"
    >
      <div 
        v-if="showGoogleToast" 
        class="fixed top-6 right-6 z-50 max-w-md bg-white rounded-xl shadow-2xl border-2 border-emerald-200 overflow-hidden"
      >
        <div class="flex items-start gap-3 p-4">
          <!-- Icono -->
          <div class="w-10 h-10 rounded-full bg-emerald-100 flex items-center justify-center flex-shrink-0">
            <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
          </div>
          
          <!-- Contenido -->
          <div class="flex-1 min-w-0">
            <h3 class="text-sm font-bold text-gray-900">✅ Cuenta de Google conectada</h3>
            <p class="text-xs text-gray-600 mt-1">Completa los datos de tu negocio para finalizar</p>
          </div>
          
          <!-- Botón Cerrar -->
          <button 
            @click="showGoogleToast = false" 
            class="text-gray-400 hover:text-gray-600 transition-colors flex-shrink-0"
          >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
          </button>
        </div>
        
        <!-- Barra de progreso -->
        <div class="h-1 bg-emerald-100">
          <div class="h-full bg-emerald-600 animate-toast-progress"></div>
        </div>
      </div>
    </transition>

    <!-- 🚨 MODAL: Email Ya Registrado -->
    <transition
      enter-active-class="transition ease-out duration-300"
      enter-from-class="opacity-0"
      enter-to-class="opacity-100"
      leave-active-class="transition ease-in duration-200"
      leave-from-class="opacity-100"
      leave-to-class="opacity-0"
    >
      <div 
        v-if="showEmailExistsModal" 
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm px-4"
        @click.self="closeEmailModal"
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
            <div class="bg-gradient-to-r from-amber-500 to-orange-500 p-6 text-center">
              <div class="w-16 h-16 bg-white rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
              </div>
              <h3 class="text-2xl font-bold text-white">Email Ya Registrado</h3>
            </div>

            <!-- Content -->
            <div class="p-6">
              <p class="text-gray-700 text-center mb-2">
                El correo electrónico
              </p>
              <p class="text-gray-900 font-bold text-center text-lg mb-4">
                {{ duplicateEmail }}
              </p>
              <p class="text-gray-600 text-center mb-6">
                ya está registrado en nuestro sistema.
              </p>

              <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                <p class="text-sm text-blue-900 text-center">
                  💡 <strong>¿Olvidaste tu contraseña?</strong><br/>
                  Puedes recuperarla usando nuestra función de recuperación de contraseña.
                </p>
              </div>

              <!-- Botones -->
              <div class="flex flex-col gap-3">
                <button
                  @click="goToRecovery"
                  class="w-full px-6 py-3 bg-gradient-to-r from-emerald-600 to-emerald-700 hover:from-emerald-700 hover:to-emerald-800 text-white font-bold rounded-xl shadow-lg transition-all duration-200 transform hover:scale-[1.02]"
                >
                  🔐 Recuperar Contraseña
                </button>
                <button
                  @click="closeEmailModal"
                  class="w-full px-6 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold rounded-xl transition-colors"
                >
                  Cancelar
                </button>
              </div>
            </div>
          </div>
        </transition>
      </div>
    </transition>
    
    <!-- 📸 LEFT PANEL: Branding Visual (40%) -->
    <div class="hidden lg:flex lg:w-[40%] bg-gradient-to-br from-emerald-600 via-emerald-700 to-emerald-900 relative overflow-hidden flex-col justify-between p-12 text-white">
      <!-- Imagen de fondo real -->
      <img 
        src="/login.png" 
        alt="105 POS Pro" 
        class="absolute inset-0 w-full h-full object-cover opacity-20"
        style="object-position: center 70%;"
      />
      
      <!-- Overlay verde -->
      <div class="absolute inset-0 bg-gradient-to-br from-emerald-900/60 via-emerald-800/50 to-black/60"></div>

      <!-- Header con logo -->
      <div class="relative z-10">
        <h1 class="text-5xl font-bold mb-3">105 POS Pro</h1>
        <p class="text-emerald-100 text-xl">Gestiona tu negocio al siguiente nivel</p>
      </div>

      <!-- Testimonial flotante -->
      <div class="relative z-10 mt-auto">
        <div class="bg-white/10 backdrop-blur-md border border-white/20 p-6 rounded-2xl">
          <div class="flex text-amber-400 mb-3">
            <svg v-for="i in 5" :key="i" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
          </div>
          <p class="text-white text-lg italic mb-4">"Desde que uso 105 POS, mis ventas son 30% más rápidas."</p>
          <div class="flex items-center">
            <div class="w-12 h-12 rounded-full bg-emerald-500/30 flex items-center justify-center text-sm font-bold text-white mr-3 border-2 border-white/30">CR</div>
            <div>
              <p class="text-white font-semibold">Carlos R.</p>
              <p class="text-emerald-100 text-sm">Dueño de Minimarket</p>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- 📝 RIGHT PANEL: Formulario Amplio (60%) -->
    <div class="flex-1 flex items-center justify-center bg-white overflow-y-auto">
      <!-- Mobile Header -->
      <!-- 📱 MOBILE HEADER: Solo en móviles -->
      <div class="lg:hidden p-5 flex items-center border-b border-gray-100 bg-white/80 backdrop-blur-sm sticky top-0 z-20">
        <div class="w-9 h-9 bg-emerald-600 rounded-xl flex items-center justify-center mr-3 shadow-md shadow-emerald-600/30">
           <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
              <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z" />
           </svg>
        </div>
        <span class="font-bold text-gray-900 text-xl">105 POS Pro</span>
      </div>

      <!-- 🔥 CONTENEDOR PRINCIPAL AMPLIO: max-w-2xl -->
      <div class="flex-1 overflow-y-auto py-6">
        <div class="w-full max-w-2xl mx-auto px-10 py-8">
          
          <!-- 🎨 STEPPER: Barra horizontal moderna (solo step 0 y 1) -->
          <div v-if="step === 0 || step === 1" class="mb-10">
             <div class="flex items-center space-x-3">
                <!-- Step 0: Cuenta -->
                <div class="flex-1 flex items-center space-x-2">
                   <div 
                     :class="[
                       'w-9 h-9 rounded-lg flex items-center justify-center text-sm font-bold transition-all duration-300',
                       step >= 0 
                         ? 'bg-emerald-600 text-white shadow-md shadow-emerald-600/40' 
                         : 'bg-gray-100 text-gray-400'
                     ]"
                   >
                      <svg v-if="step > 0" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                      </svg>
                      <span v-else>1</span>
                   </div>
                   <span class="text-sm font-semibold" :class="step >= 0 ? 'text-gray-900' : 'text-gray-400'">Cuenta</span>
                </div>

                <!-- Línea conectora -->
                <div 
                  :class="[
                    'flex-1 h-1 rounded-full transition-all duration-500',
                    step >= 1 ? 'bg-emerald-600' : 'bg-gray-200'
                  ]"
                ></div>

                <!-- Step 1: Negocio -->
                <div class="flex-1 flex items-center space-x-2">
                   <div 
                     :class="[
                       'w-9 h-9 rounded-lg flex items-center justify-center text-sm font-bold transition-all duration-300',
                       step >= 1 
                         ? 'bg-emerald-600 text-white shadow-md shadow-emerald-600/40' 
                         : 'bg-gray-100 text-gray-400'
                     ]"
                   >
                      2
                   </div>
                   <span class="text-sm font-semibold" :class="step >= 1 ? 'text-gray-900' : 'text-gray-400'">Negocio</span>
                </div>
             </div>
          </div>

          <!-- 📝 STEP 0: Formulario de Cuenta -->
          <div v-if="step === 0" class="animate-fade-in">
            <!-- Header -->
            <div class="mb-10">
              <h2 class="text-4xl font-bold text-gray-900 mb-2">
                Crea tu cuenta
              </h2>
              <p class="text-gray-500 text-base">
                Comienza tu prueba gratuita de 30 días
              </p>
            </div>

            <!-- 🔥 Google Button: Chunky & Destacado -->
            <button 
              type="button"
              @click="signInWithGoogle"
              :disabled="isGoogleLoading"
              class="w-full h-14 px-6 bg-white border-2 border-gray-200 hover:border-gray-300 text-gray-700 font-semibold rounded-xl transition-all duration-200 flex items-center justify-center gap-3 hover:bg-gray-50 hover:shadow-md mb-8 disabled:opacity-50 disabled:cursor-not-allowed"
            >
              <svg v-if="!isGoogleLoading" class="w-5 h-5" viewBox="0 0 24 24">
                <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
              </svg>
              <svg v-else class="animate-spin w-5 h-5 text-gray-400" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              <span>{{ isGoogleLoading ? 'Conectando...' : 'Continuar con Google' }}</span>
            </button>

            <!-- Divisor "o" -->
            <div class="relative mb-8">
              <div class="absolute inset-0 flex items-center">
                <div class="w-full border-t border-gray-200"></div>
              </div>
              <div class="relative flex justify-center text-sm">
                <span class="px-5 bg-white text-gray-400 font-medium">o continúa con email</span>
              </div>
            </div>

            <!-- 📝 FORMULARIO: Inputs Chunky -->
            <form @submit.prevent="step = 1" class="space-y-6">
              <!-- Nombre del Negocio -->
              <div>
                <label class="text-sm font-semibold text-gray-700 mb-2 block">Nombre del Negocio</label>
                <div class="relative">
                  <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                  </div>
                  <input 
                    v-model="form.company_name"
                    type="text" 
                    required 
                    class="w-full h-14 pl-12 pr-4 bg-gray-50 border-2 border-transparent rounded-xl focus:bg-white focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition-all outline-none text-gray-900 placeholder-gray-400 font-medium" 
                    placeholder="Ej. Cafetería Central"
                    @input="generateSubdomain"
                  >
                </div>
              </div>

              <!-- Email -->
              <div>
                <label class="text-sm font-semibold text-gray-700 mb-2 block">Correo Electrónico</label>
                <div class="relative">
                  <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                  </div>
                  <input 
                    v-model="form.email"
                    type="email" 
                    required 
                    class="w-full h-14 pl-12 pr-4 bg-gray-50 border-2 border-transparent rounded-xl focus:bg-white focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition-all outline-none text-gray-900 placeholder-gray-400 font-medium" 
                    placeholder="tu@empresa.com"
                  >
                </div>
              </div>

              <!-- Contraseña -->
              <div>
                <label class="text-sm font-semibold text-gray-700 mb-2 block">Contraseña</label>
                <div class="relative">
                  <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                  </div>
                  <input 
                    v-model="form.password"
                    :type="showPassword ? 'text' : 'password'"
                    required 
                    minlength="8"
                    class="w-full h-14 pl-12 pr-14 bg-gray-50 border-2 border-transparent rounded-xl focus:bg-white focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition-all outline-none text-gray-900 placeholder-gray-400 font-medium" 
                    placeholder="Mínimo 8 caracteres"
                  >
                  <button
                    type="button"
                    @click="showPassword = !showPassword"
                    class="absolute inset-y-0 right-0 pr-4 flex items-center text-gray-400 hover:text-gray-600 transition-colors"
                  >
                    <svg v-if="!showPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                    <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                    </svg>
                  </button>
                </div>
              </div>

              <!-- 🔥 Botón Crear Cuenta: Emerald Bold -->
              <button 
                type="submit"
                class="w-full h-14 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl shadow-lg shadow-emerald-600/30 hover:shadow-emerald-700/40 transition-all duration-200 text-base"
              >
                Continuar →
              </button>
            </form>
            
            <!-- Login Link -->
            <p class="text-center mt-8 text-sm text-gray-500">
              ¿Ya tienes cuenta? <a href="/login" class="text-emerald-600 font-semibold hover:text-emerald-700 transition-colors">Inicia Sesión</a>
            </p>
          </div>

          <!-- 📝 STEP 1: Información del Negocio -->
          <div v-if="step === 1" class="animate-fade-in">
            <!-- Header (más compacto sin banner) -->
            <div class="mb-6">
              <h2 class="text-3xl font-bold text-gray-900 mb-1">Información del negocio</h2>
              <p class="text-gray-500 text-sm">Cuéntanos sobre tu empresa</p>
            </div>

            <form @submit.prevent="validateStep1" class="space-y-5">
              <!-- 🎯 GRID 2 COLUMNAS: Nombre y Email (o Cédula si no hay Google) -->
              <div class="grid md:grid-cols-2 gap-4">
                <!-- Tu Nombre -->
                <div>
                  <label class="text-sm font-semibold text-gray-700 mb-2 flex items-center gap-2">
                    Tu Nombre Completo
                    <span v-if="googleUserData" class="text-xs font-normal text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-full">De Google</span>
                  </label>
                  <input 
                    v-model="form.owner_name" 
                    type="text" 
                    required
                    :readonly="!!googleUserData"
                    class="w-full h-14 px-4 rounded-xl border-2 transition-all outline-none font-medium placeholder-gray-400" 
                    :class="googleUserData ? 'bg-gray-100 text-gray-700 border-gray-200 cursor-not-allowed' : 'bg-gray-50 border-transparent focus:bg-white focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 text-gray-900'"
                    placeholder="Ej. Juan Pérez"
                  >
                </div>

                <!-- Email (si viene de Google) O Cédula -->
                <div v-if="googleUserData">
                  <label class="text-sm font-semibold text-gray-700 mb-2 flex items-center gap-2">
                    Email
                    <span class="text-xs font-normal text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-full">De Google</span>
                  </label>
                  <input 
                    v-model="form.email" 
                    type="email" 
                    required
                    readonly
                    class="w-full h-14 px-4 rounded-xl border-2 border-gray-200 bg-gray-100 text-gray-700 cursor-not-allowed font-medium" 
                    placeholder="tu@email.com"
                  >
                </div>
                <div v-else>
                  <label class="text-sm font-semibold text-gray-700 mb-2 block">Cédula / NIT</label>
                  <input 
                    v-model="form.cedula" 
                    type="text" 
                    required 
                    class="w-full h-14 px-4 bg-gray-50 border-2 border-transparent rounded-xl focus:bg-white focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition-all outline-none text-gray-900 font-medium placeholder-gray-400" 
                    placeholder="Ej. 123456789"
                  >
                </div>
              </div>

              <!-- Nombre del Negocio (ancho completo) -->
              <div>
                <label class="text-sm font-semibold text-gray-700 mb-2 block">Nombre del Negocio</label>
                <input 
                  v-model="form.company_name" 
                  type="text" 
                  required 
                  class="w-full h-14 px-4 bg-gray-50 border-2 border-transparent rounded-xl focus:bg-white focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition-all outline-none text-gray-900 font-medium placeholder-gray-400" 
                  placeholder="Ej. Cafetería Central" 
                  @input="generateSubdomain"
                >
              </div>

              <!-- Cédula / NIT (solo si viene de Google, porque ya se mostró arriba en el grid) -->
              <div v-if="googleUserData">
                <label class="text-sm font-semibold text-gray-700 mb-2 block">Cédula / NIT</label>
                <input 
                  v-model="form.cedula" 
                  type="text" 
                  required 
                  class="w-full h-14 px-4 bg-gray-50 border-2 border-transparent rounded-xl focus:bg-white focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition-all outline-none text-gray-900 font-medium placeholder-gray-400" 
                  placeholder="Ej. 123456789"
                >
              </div>

              <!-- Dirección Web (Subdomain) -->
              <div>
                <label class="text-sm font-semibold text-gray-700 mb-2 flex justify-between items-center">
                  <span>Dirección Web</span>
                  <span class="text-xs font-normal text-gray-400">Tu enlace único</span>
                </label>
                <div class="relative group">
                  <input 
                    v-model="form.subdomain" 
                    type="text" 
                    required 
                    class="w-full h-14 pl-4 pr-44 bg-gray-50 border-2 border-transparent rounded-xl focus:bg-white focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition-all outline-none text-gray-900 font-mono font-medium placeholder-gray-400" 
                    placeholder="mi-negocio" 
                    @input="checkAvailability"
                    @blur="checkAvailability"
                  >
                  <div class="absolute right-0 top-0 h-full flex items-center pr-4 pointer-events-none">
                    <span class="text-gray-500 font-medium bg-gray-100 px-3 py-1.5 rounded-lg text-sm group-focus-within:text-emerald-600 group-focus-within:bg-emerald-50 transition-colors">.105pos.pro</span>
                  </div>
                </div>
                
                <!-- Estado de Disponibilidad -->
                <div class="min-h-6 mt-2">
                   <transition name="fade">
                      <p v-if="availabilityStatus === 'checking'" class="text-xs text-gray-500 flex items-center">
                         <svg class="animate-spin w-3.5 h-3.5 mr-1.5" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                         Verificando disponibilidad...
                      </p>
                      <p v-else-if="availabilityStatus === 'available'" class="text-xs text-emerald-600 flex items-center font-bold">
                         <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                         ✓ Disponible: {{ form.subdomain }}.105pos.pro
                      </p>
                      <p v-else-if="availabilityStatus === 'taken'" class="text-xs text-rose-600 flex items-center font-bold">
                         <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                         ✗ No disponible - Prueba otro nombre
                      </p>
                      <p v-else-if="availabilityStatus === 'invalid'" class="text-xs text-amber-600 flex items-center font-bold">
                         <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                         Solo letras minúsculas, números y guiones (-)
                      </p>
                      <p v-else-if="availabilityStatus === 'timeout'" class="text-xs text-orange-600 flex items-center font-bold">
                         <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                         Tiempo de espera agotado - Intenta nuevamente
                      </p>
                      <p v-else-if="availabilityStatus === 'network'" class="text-xs text-red-600 flex items-center font-bold">
                         <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636a9 9 0 010 12.728m0 0l-2.829-2.829m2.829 2.829L21 21M15.536 8.464a5 5 0 010 7.072m0 0l-2.829-2.829m-4.243 2.829a4.978 4.978 0 01-1.414-2.83m-1.414 5.658a9 9 0 01-2.167-9.238m7.824 2.167a1 1 0 111.414 1.414m-1.414-1.414L3 3m8.293 8.293l1.414 1.414"></path></svg>
                         Sin conexión al servidor - Verifica tu internet
                      </p>
                      <p v-else-if="availabilityStatus === 'error'" class="text-xs text-red-600 flex items-start font-bold">
                         <svg class="w-4 h-4 mr-1.5 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                         <span>Error del servidor - Presiona F12 y revisa la pestaña Console para más detalles</span>
                      </p>
                   </transition>
                </div>
              </div>

              <!-- ✅ Términos y Condiciones -->
              <div class="pt-6 pb-2">
                <label class="flex items-start gap-3 cursor-pointer group">
                  <input 
                    type="checkbox" 
                    v-model="acceptedTerms"
                    class="w-5 h-5 text-emerald-600 border-gray-300 rounded focus:ring-emerald-500 focus:ring-2 mt-0.5 cursor-pointer"
                  >
                  <span class="text-sm text-gray-600 leading-relaxed">
                    Acepto los 
                    <a 
                      href="/terminos-condiciones" 
                      target="_blank"
                      class="text-emerald-600 hover:text-emerald-700 font-semibold underline"
                    >
                      Términos y Condiciones
                    </a>
                    y la 
                    <a 
                      href="/politica-privacidad" 
                      target="_blank"
                      class="text-emerald-600 hover:text-emerald-700 font-semibold underline"
                    >
                      Política de Privacidad
                    </a>
                  </span>
                </label>
              </div>

              <!-- 🔥 Botones de Acción -->
              <div class="pt-4 flex items-center gap-4">
                 <button 
                   type="button" 
                   @click="step = 0" 
                   class="px-8 h-12 text-gray-600 font-semibold hover:bg-gray-100 rounded-xl transition-all border border-gray-200"
                 >
                    ← Atrás
                 </button>
                 <button 
                   type="submit" 
                   :disabled="availabilityStatus !== 'available' || !form.owner_name || !form.company_name || !form.cedula || !acceptedTerms || isSubmitting" 
                   class="flex-1 h-14 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl shadow-lg shadow-emerald-600/30 hover:shadow-emerald-700/40 transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center text-base"
                 >
                   <svg v-if="isSubmitting" class="animate-spin -ml-1 mr-2 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                      <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                      <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                   </svg>
                   <span v-if="!isSubmitting">Crear mi Cuenta →</span>
                   <span v-else>Creando cuenta...</span>
                 </button>
              </div>
            </form>
          </div>

          <!-- 🔄 STEP 2: Procesando (Loading State) -->
          <div v-if="step === 2" class="animate-fade-in text-center py-16">
             <div class="relative w-24 h-24 mx-auto mb-8">
                <div class="absolute inset-0 border-4 border-gray-100 rounded-full"></div>
                <div class="absolute inset-0 border-4 border-emerald-600 rounded-full border-t-transparent animate-spin"></div>
             </div>
             <h2 class="text-3xl font-bold text-gray-900 mb-4">Configurando tu sistema</h2>
             
             <div class="h-12 overflow-hidden relative">
                <transition name="slide-up" mode="out-in">
                   <p :key="currentMessageIndex" class="text-gray-500 font-medium text-lg">
                      {{ marketingMessages[currentMessageIndex] }}
                   </p>
                </transition>
             </div>
          </div>

          <!-- ✅ STEP 3: Registro Exitoso -->
          <div v-if="step === 3" class="animate-fade-in py-16">
            <div class="max-w-xl mx-auto">
              <!-- Card de Éxito -->
              <div class="bg-white rounded-2xl shadow-xl border-2 border-emerald-100 p-10 text-center">
                <!-- Icono de Éxito -->
                <div class="w-20 h-20 bg-emerald-100 rounded-full flex items-center justify-center mx-auto mb-6">
                  <svg class="w-10 h-10 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                  </svg>
                </div>

                <!-- Título -->
                <h2 class="text-4xl font-bold text-gray-900 mb-4">
                  ¡Cuenta Creada! 🎉
                </h2>

                <!-- Info de la Empresa -->
                <div class="mb-8">
                  <p class="text-lg text-gray-600 mb-4">
                    Tu empresa <span class="font-bold text-gray-900">{{ form.company_name }}</span> ha sido registrada exitosamente.
                  </p>
                  <div class="inline-flex items-center gap-2 px-5 py-3 bg-emerald-50 rounded-xl border border-emerald-200">
                    <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"></path>
                    </svg>
                    <span class="text-sm font-bold text-emerald-800">
                      {{ form.subdomain }}.105pos.pro
                    </span>
                  </div>
                </div>

                <!-- Mensaje Informativo -->
                <div class="bg-blue-50 rounded-xl p-6 mb-8 border border-blue-200">
                  <div class="flex items-start gap-3 text-left">
                    <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0">
                      <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                      </svg>
                    </div>
                    <div class="flex-1">
                      <h4 class="font-bold text-gray-900 mb-2">Siguiente Paso: Elige Tu Plan</h4>
                      <p class="text-sm text-gray-600 leading-relaxed">
                        Para activar tu cuenta y comenzar a usar el sistema, selecciona el plan que mejor se adapte a tu negocio.
                      </p>
                    </div>
                  </div>
                </div>

                <!-- Botón Principal -->
                <button
                  @click="goToPlanSelection"
                  class="w-full h-14 bg-emerald-600 hover:bg-emerald-700 text-white text-lg font-bold rounded-xl shadow-lg shadow-emerald-600/30 hover:shadow-emerald-700/40 transition-all duration-200 flex items-center justify-center gap-2"
                >
                  Elegir Mi Plan
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                  </svg>
                </button>

                <!-- Badges de Confianza -->
                <div class="flex items-center justify-center flex-wrap gap-4 mt-8 pt-6 border-t border-gray-200">
                  <div class="flex items-center gap-2 text-xs text-gray-500 font-medium">
                    <svg class="w-4 h-4 text-emerald-500" fill="currentColor" viewBox="0 0 20 20">
                      <path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    <span>Pago Seguro</span>
                  </div>
                  <div class="flex items-center gap-2 text-xs text-gray-500 font-medium">
                    <svg class="w-4 h-4 text-emerald-500" fill="currentColor" viewBox="0 0 20 20">
                      <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                    </svg>
                    <span>30 días de prueba</span>
                  </div>
                  <div class="flex items-center gap-2 text-xs text-gray-500 font-medium">
                    <svg class="w-4 h-4 text-emerald-500" fill="currentColor" viewBox="0 0 20 20">
                      <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-8.707l-3-3a1 1 0 00-1.414 0l-3 3a1 1 0 001.414 1.414L9 9.414V13a1 1 0 102 0V9.414l1.293 1.293a1 1 0 001.414-1.414z" clip-rule="evenodd"/>
                    </svg>
                    <span>Cancela cuando quieras</span>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- 🔄 STEP 4: Redirigiendo -->
          <div v-if="step === 4" class="animate-fade-in text-center py-20">
             <div>
                <div class="w-20 h-20 bg-emerald-100 rounded-full flex items-center justify-center mx-auto mb-6 text-emerald-600">
                   <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                </div>
                <h2 class="text-3xl font-bold text-gray-900 mb-3">¡Todo listo!</h2>
                <p class="text-gray-500 text-lg mb-8">Tu sistema ha sido configurado exitosamente</p>
                
                <div class="inline-flex items-center px-6 py-3 bg-emerald-50 text-emerald-700 rounded-xl text-sm font-semibold animate-pulse border border-emerald-200">
                   Redirigiendo a tu panel...
                </div>
             </div>
          </div>

        </div>
      </div>
      
      <!-- 📱 Mobile Footer -->
      <div class="lg:hidden p-6 text-center text-xs text-gray-400 border-t border-gray-100 bg-gray-50">
         <p class="font-medium">© 2024 105 POS Pro</p>
         <p class="mt-1">Todos los derechos reservados</p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted, onUnmounted } from 'vue'
import axios from 'axios'
import googleAuthService from '../services/googleAuthService'

// State
const step = ref(0)  // 0: Datos Iniciales | 1: Propietario | 2: Procesando | 3: Selección de Plan | 4: Éxito
const isSubmitting = ref(false)
const isGoogleLoading = ref(false)
const successData = ref(null)
const availabilityStatus = ref(null) // null, 'checking', 'available', 'taken'
const checkTimeout = ref(null)
const showPassword = ref(false)
const tenantCreated = ref(null) // Datos del tenant recién creado
const googleUserData = ref(null) // 🆕 Datos del usuario de Google
const googleCode = ref(null) // 🆕 Código de autorización de Google
const showGoogleToast = ref(false) // 🔔 Control de Toast de Google
const acceptedTerms = ref(false) // ✅ Aceptación de Términos y Condiciones
const showEmailExistsModal = ref(false) // 🔔 Modal de email duplicado
const duplicateEmail = ref('') // Email duplicado encontrado
let toastTimeout = null // Timer para auto-cerrar toast

const marketingMessages = [
  "Creando base de datos segura...",
  "Configurando módulos de inventario...",
  "Activando sistema de facturación...",
  "Generando credenciales de acceso...",
  "Finalizando configuración..."
]
const currentMessageIndex = ref(0)
let messageInterval = null

const form = reactive({
  owner_name: '',
  cedula: '',
  company_name: '',
  subdomain: '',
  email: '',
  password: '',
  token: '', // Token del link generado
  plan: 'pending' // Se actualizará cuando usuario seleccione (emprendedor, negocio_pro, enterprise)
})

// Methods
const generateSubdomain = () => {
  if (!form.company_name) return
  
  const slug = form.company_name
    .toLowerCase()
    .normalize("NFD").replace(/[\u0300-\u036f]/g, "")
    .replace(/[^a-z0-9\s-]/g, '')
    .trim()
    .replace(/\s+/g, '-')
    .replace(/-+/g, '-')
    .substring(0, 20) // Limit length
  
  form.subdomain = slug
  checkAvailability()
}

const checkAvailability = async () => {
  if (!form.subdomain) {
    availabilityStatus.value = null
    return
  }

  // Limpiar espacios y convertir a minúsculas
  form.subdomain = form.subdomain.trim().toLowerCase()

  // Validar formato del subdomain
  const subdomainRegex = /^[a-z0-9]+(?:-[a-z0-9]+)*$/
  if (!subdomainRegex.test(form.subdomain)) {
    availabilityStatus.value = 'invalid'
    return
  }

  // Debounce
  if (checkTimeout.value) clearTimeout(checkTimeout.value)
  
  availabilityStatus.value = 'checking'
  
  checkTimeout.value = setTimeout(async () => {
    try {
      const apiUrl = import.meta.env.VITE_API_URL ? `${import.meta.env.VITE_API_URL}/api` : '/api'
      
      console.log('🔍 Verificando disponibilidad:', {
        subdomain: form.subdomain,
        url: `${apiUrl}/check-domain`
      })
      
      const response = await axios.post(`${apiUrl}/check-domain`, { 
        subdomain: form.subdomain 
      }, {
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json'
        },
        timeout: 10000 // 10 segundos timeout
      })
      
      console.log('✅ Respuesta del servidor:', response.data)
      
      if (response.data && typeof response.data.available !== 'undefined') {
        availabilityStatus.value = response.data.available ? 'available' : 'taken'
      } else {
        console.error('Respuesta inesperada:', response.data)
        availabilityStatus.value = 'error'
      }
    } catch (error) {
      console.error('❌ Error checking domain:', error)
      console.error('Detalles del error:', {
        message: error.message,
        response: error.response?.data,
        status: error.response?.status,
        code: error.code
      })
      
      // Mostrar error específico según el tipo
      if (error.code === 'ECONNABORTED' || error.message.includes('timeout')) {
        availabilityStatus.value = 'timeout'
      } else if (!error.response) {
        availabilityStatus.value = 'network'
      } else {
        availabilityStatus.value = 'error'
      }
    }
  }, 500)
}

/**
 * Inicia el flujo de autenticación con Google OAuth 2.0
 */
const signInWithGoogle = async () => {
  try {
    isGoogleLoading.value = true
    
    console.log('🔐 Iniciando autenticación con Google...')
    
    // NO enviar datos de registro - solo iniciar OAuth
    const authUrl = await googleAuthService.initiateGoogleAuth({})
    
    console.log('✅ URL de Google OAuth recibida:', authUrl)
    
    // Redirigir a Google para autenticación
    // Google luego redirigirá a: /api/auth/google/callback
    window.location.href = authUrl
    
  } catch (error) {
    isGoogleLoading.value = false
    console.error('❌ Error al iniciar OAuth con Google:', error)
    
    let errorMessage = 'No se pudo conectar con Google. Intenta nuevamente.'
    
    if (error.response?.data?.message) {
      errorMessage = error.response.data.message
    }
    
    alert(errorMessage)
  }
}

/**
 * Verifica si el correo ya está registrado en el sistema
 * @returns {Promise<boolean>} true si el correo existe, false si está disponible
 */
// 🔄 Cerrar modal y redirigir a recuperación
const goToRecovery = () => {
  showEmailExistsModal.value = false
  window.location.href = '/forgot-password'
}

// ❌ Cerrar modal sin hacer nada
const closeEmailModal = () => {
  showEmailExistsModal.value = false
}

const checkEmailExists = async () => {
  try {
    const apiUrl = import.meta.env.VITE_API_URL ? `${import.meta.env.VITE_API_URL}/api` : '/api'
    
    const response = await axios.get(`${apiUrl}/central/check-email`, {
      params: { email: form.email }
    })
    
    if (response.data.exists) {
      // Mostrar modal profesional
      duplicateEmail.value = form.email
      showEmailExistsModal.value = true
      return true // Email exists
    }
    
    return false // Email available
    
  } catch (error) {
    console.error('❌ Error al verificar correo:', error)
    
    // Si hay error en la verificación, mostrar mensaje pero permitir continuar
    // (puede ser un error de red temporal)
    const shouldContinue = confirm(
      'No se pudo verificar si el correo está disponible. ' +
      'Esto puede deberse a un problema temporal de conexión.\n\n' +
      '¿Deseas continuar con el registro de todas formas?'
    )
    
    return !shouldContinue // Si dice que SÍ quiere continuar, retornamos false (no existe)
  }
}

const validateStep1 = async () => {
  if (!form.owner_name || !form.company_name || !form.subdomain || !form.cedula) {
    alert('Por favor completa todos los campos')
    return
  }
  
  if (!form.email) {
    alert('Por favor ingresa tu correo electrónico')
    return
  }
  
  if (!acceptedTerms.value) {
    alert('Debes aceptar los Términos y Condiciones y la Política de Privacidad para continuar')
    return
  }
  
  if (availabilityStatus.value === 'checking') {
    alert('Esperando verificación de disponibilidad del sitio web...')
    return
  }
  
  if (availabilityStatus.value === 'taken') {
    alert('El sitio web no está disponible. Por favor elige otro nombre.')
    return
  }
  
  if (availabilityStatus.value === 'invalid') {
    alert('El sitio web solo puede contener letras minúsculas, números y guiones.')
    return
  }
  
  if (availabilityStatus.value === 'error') {
    alert('Hubo un error al verificar la disponibilidad. Por favor verifica tu conexión y la consola del navegador.')
    return
  }
  
  if (availabilityStatus.value !== 'available') {
    alert('Por favor espera a que se verifique la disponibilidad del sitio web')
    return
  }
  
  // ✅ VALIDAR SI EL CORREO YA EXISTE
  const emailExists = await checkEmailExists()
  if (emailExists) {
    return // La función ya muestra el mensaje de error
  }
  
  // En lugar de ir al paso 2 (que ya no existe), crear la cuenta directamente
  registerTenant()
}

// Función para ir a la vista de selección de plan
const goToPlanSelection = () => {
  // Obtener datos del registro para obtener el subdomain del tenant
  const registrationData = localStorage.getItem('registration_data')
  
  if (registrationData) {
    const data = JSON.parse(registrationData)
    
    // 🔑 CRÍTICO: Pasar datos como query params porque localStorage NO se comparte entre dominios
    const params = new URLSearchParams({
      tenant_id: data.tenant_id,
      company: data.company_name || '',
      subdomain: data.subdomain || ''
    })
    
    // En producción sin subdominios DNS, ir directo a /select-plan en el dominio principal
    const targetUrl = `/select-plan?${params.toString()}`
    console.log('✅ Redirigiendo a selección de plan:', targetUrl)
    window.location.href = targetUrl
  } else {
    // Si no hay datos (caso raro), redirigir a la app central como fallback
    console.warn('⚠️ No hay registration_data en localStorage')
    window.location.href = '/select-plan'
  }
}

const startMarketingMessages = () => {
  messageInterval = setInterval(() => {
    currentMessageIndex.value = (currentMessageIndex.value + 1) % marketingMessages.length
  }, 2000)
}

const registerTenant = async () => {
  isSubmitting.value = true
  step.value = 2 // Show processing screen
  startMarketingMessages()

  try {
    const apiUrl = import.meta.env.VITE_API_URL ? `${import.meta.env.VITE_API_URL}/api` : '/api'
    
    console.log('📤 Enviando petición de registro a:', `${apiUrl}/register-tenant`)
    console.log('📦 Datos del formulario:', form)
    
    // 🆕 Si hay datos de Google, incluirlos en el registro
    const registrationPayload = {
      ...form,
      plan: 'pending' // Plan pendiente de selección
    }
    
    if (googleUserData.value) {
      registrationPayload.google_id = googleUserData.value.google_id
      registrationPayload.google_email = googleUserData.value.email
      registrationPayload.google_name = googleUserData.value.name
      registrationPayload.google_picture = googleUserData.value.picture
      
      console.log('🔐 Incluyendo datos de Google en registro:', googleUserData.value)
    }
    
    // Crear tenant SIN plan definido aún (o con plan temporal)
    const response = await axios.post(`${apiUrl}/register-tenant`, registrationPayload)
    
    console.log('📥 Respuesta completa recibida:', response)
    
    if (response.data.success) {
      // Guardar datos del tenant creado
      tenantCreated.value = response.data
      clearInterval(messageInterval)
      isSubmitting.value = false
      
      // ✅ GUARDAR DATOS DEL REGISTRO EN LOCALSTORAGE para proceso de pago
      const registrationData = {
        company_name: form.company_name,
        storeName: form.company_name,
        owner_name: form.owner_name,
        email: form.email,
        subdomain: form.subdomain,
        cedula: form.cedula,
        tenant_id: response.data.tenant_id,
        redirect_url: response.data.redirect_url
      }
      localStorage.setItem('registration_data', JSON.stringify(registrationData))
      
      // Ir a pantalla de éxito (paso 3 - ahora es solo mensaje de éxito)
      step.value = 3
    } else {
      throw new Error('Respuesta sin success=true')
    }

  } catch (error) {
    console.error('Registration error:', error)
    
    step.value = 1 // Go back to step 1 on error
    isSubmitting.value = false
    clearInterval(messageInterval)
    
    let errorMessage = 'Ocurrió un error al crear la cuenta.'
    if (error.response?.data?.message) {
      errorMessage = error.response.data.message
    } else if (error.response?.data?.errors) {
       errorMessage = Object.values(error.response.data.errors)[0][0]
    }
    
    alert(errorMessage)
  }
}

// Detectar token de la URL al cargar
onMounted(async () => {
  // 🔒 PROTECCIÓN: Solo permitir registro en app central (sin subdominio)
  const hostname = window.location.hostname
  const parts = hostname.split('.')
  
  // Dominios principales permitidos
  const mainDomains = ['localhost', '127.0.0.1', '105pos.pro', 'www.105pos.pro']
  const isMainDomain = mainDomains.includes(hostname)
  
  // Si NO es un dominio principal y tiene más de 2 partes, es un subdominio
  if (!isMainDomain && parts.length > 2) {
    console.warn('⚠️ Intento de acceder a /register desde subdominio. Redirigiendo a app central...')
    const protocol = window.location.protocol
    const port = window.location.port ? `:${window.location.port}` : ''
    
    // Determinar dominio principal según el ambiente
    const mainDomain = hostname.includes('105pos.pro') ? '105pos.pro' : 'localhost'
    
    // Redirigir a la app central
    window.location.href = `${protocol}//${mainDomain}${port}/register`
    return
  }
  
  const urlParams = new URLSearchParams(window.location.search)
  const token = urlParams.get('token')
  if (token) {
    form.token = token
  }

  // 🆕 Detectar callback de Google OAuth con token
  const googleToken = urlParams.get('google_token')
  
  if (googleToken) {
    console.log('🔐 Google Auth completado - Cargando datos del usuario...')
    isGoogleLoading.value = true
    
    try {
      // Obtener datos de Google usando el token temporal
      const response = await axios.get('/api/auth/google/user-data', {
        params: { token: googleToken }
      })
      
      if (response.data.success && response.data.user) {
        const userData = response.data.user
        
        console.log('✅ Datos de Google recibidos:', userData)
        
        googleUserData.value = userData
        
        // Prellenar formulario con datos de Google
        form.owner_name = userData.name || ''
        form.email = userData.email || ''
        
        // Generar subdominio sugerido desde el nombre/email
        const suggestedSubdomain = (userData.name || userData.email.split('@')[0])
          .toLowerCase()
          .normalize("NFD").replace(/[\u0300-\u036f]/g, "")
          .replace(/[^a-z0-9\s-]/g, '')
          .trim()
          .replace(/\s+/g, '-')
          .substring(0, 20)
        
        form.company_name = ''
        form.subdomain = suggestedSubdomain
        
        // Ir al paso 1 para que complete datos de tienda
        step.value = 1
        isGoogleLoading.value = false
        
        // 🔔 Mostrar toast de éxito de Google
        showGoogleToast.value = true
        
        // Auto-cerrar toast después de 4 segundos
        toastTimeout = setTimeout(() => {
          showGoogleToast.value = false
        }, 4000)
        
        // Limpiar URL (quitar token de la URL)
        window.history.replaceState({}, document.title, '/register')
        
      } else {
        throw new Error('No se pudieron obtener los datos de Google')
      }
      
    } catch (error) {
      console.error('❌ Error al obtener datos de Google:', error)
      alert('Error al procesar datos de Google. El token pudo haber expirado. Intenta nuevamente.')
      isGoogleLoading.value = false
      window.history.replaceState({}, document.title, '/register')
    }
  }
})

onUnmounted(() => {
  if (messageInterval) clearInterval(messageInterval)
  if (toastTimeout) clearTimeout(toastTimeout)
})
</script>

<style scoped>
.animate-fade-in {
  animation: fadeIn 0.5s ease-out forwards;
}

@keyframes fadeIn {
  from { opacity: 0; transform: translateY(10px); }
  to { opacity: 1; transform: translateY(0); }
}

.animate-scale-in {
  animation: scaleIn 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275) forwards;
}

@keyframes scaleIn {
  from { opacity: 0; transform: scale(0.9); }
  to { opacity: 1; transform: scale(1); }
}

.slide-up-enter-active,
.slide-up-leave-active {
  transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
}

.slide-up-enter-from {
  opacity: 0;
  transform: translateY(20px);
}

.slide-up-leave-to {
  opacity: 0;
  transform: translateY(-20px);
}

/* Custom Scrollbar for form area */
::-webkit-scrollbar {
  width: 6px;
}
::-webkit-scrollbar-track {
  background: transparent;
}
::-webkit-scrollbar-thumb {
  background: #cbd5e1;
  border-radius: 3px;
}
::-webkit-scrollbar-thumb:hover {
  background: #94a3b8;
}

/* 🔔 Toast Progress Bar Animation */
@keyframes toast-progress {
  from {
    width: 100%;
  }
  to {
    width: 0%;
  }
}

.animate-toast-progress {
  animation: toast-progress 4s linear forwards;
}
</style>
