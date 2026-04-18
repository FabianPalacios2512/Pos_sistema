<template>
  <!-- SPLIT SCREEN ENTERPRISE LAYOUT -->
  <div class="font-['Inter',sans-serif] bg-white selection:bg-cyan-500/20 relative" style="height: 100%; min-height: 100%;">
    
    <!-- TOAST NOTIFICATION: Google Success (Esquina Superior Derecha) -->
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
            <h3 class="text-sm font-bold text-gray-900">Cuenta de Google conectada</h3>
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

    <!-- ðŸš¨ MODAL UNIFICADO: Email o NIT/CC Ya Registrado -->
    <transition
      enter-active-class="transition ease-out duration-300"
      enter-from-class="opacity-0"
      enter-to-class="opacity-100"
      leave-active-class="transition ease-in duration-200"
      leave-from-class="opacity-100"
      leave-to-class="opacity-0"
    >
      <div 
        v-if="showDuplicateModal" 
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/50  px-4"
        @click.self="closeDuplicateModal"
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
            <!-- Header Diní¡mico -->
            <div class="bg-white border-b border-gray-200 p-4 text-center">
              <div 
                class="w-12 h-12 rounded-full flex items-center justify-center mx-auto mb-3"
                :class="duplicateType === 'email' ? 'bg-amber-100' : 'bg-red-100'"
              >
                <svg class="w-6 h-6" :class="duplicateType === 'email' ? 'text-amber-600' : 'text-red-600'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
              </div>
              <h3 class="text-lg font-bold text-gray-900">
                {{ duplicateType === 'email' ? 'Email Ya Registrado...' : 'NIT/Cédula Ya Registrado...' }}
              </h3>
            </div>

            <!-- Content Diní¡mico -->
            <div class="p-6">
              <!-- Contenido para Email -->
              <template v-if="duplicateType === 'email'">
                <p class="text-gray-700 text-center mb-2">
                  El correo Electrónico
                </p>
                <p class="text-gray-900 font-bold text-center text-lg mb-4">
                  {{ duplicateValue }}
                </p>
                <p class="text-gray-600 text-center mb-6">
                  ya est&aacute; registrado en nuestro sistema.
                </p>

                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                  <p class="text-sm text-blue-900 text-center">
                    <strong>¿Olvidaste tu Contraseña?</strong><br/>
                    Puedes recuperarla usando nuestra función de recuperación de Contraseña.
                  </p>
                </div>
              </template>

              <!-- Contenido para NIT/CC -->
              <template v-else-if="duplicateType === 'document'">
                <p class="text-gray-700 text-center mb-2">
                  El NIT/Cédula
                </p>
                <p class="text-gray-900 font-bold text-center text-lg mb-4">
                  {{ duplicateValue }}
                </p>
                <p class="text-gray-600 text-center mb-6">
                  ya se encuentra registrado en nuestro sistema.
                </p>

                <!-- ðŸ”’ SECURITY FIX: No mostrar lista de tiendas para evitar enumeración -->
                <div class="bg-amber-50 border border-amber-200 rounded-lg p-4 mb-6">
                  <p class="text-sm text-amber-900 text-center">
                    <strong>¿Ya tienes una cuenta?</strong><br/>
                    Puedes recuperar tu Contraseña para acceder.
                  </p>
                </div>
              </template>

              <!-- Botones -->
              <div class="flex flex-col gap-3">
                <button 
                  @click="goToRecovery"
                  class="w-full h-12 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold rounded-xl transition-all duration-200"
                >
                  Recuperar Contraseña
                </button>
                <button 
                  @click="closeDuplicateModal"
                  class="w-full h-12 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold rounded-xl transition-all duration-200"
                >
                  {{ duplicateType === 'email' ? 'Cerrar' : 'Usar otro NIT/Cédula' }}
                </button>
              </div>
            </div>
          </div>
        </transition>
      </div>
    </transition>
    
    <!-- LADO IZQUIERDO: Panel de Marca Corporativo Premium (40%) -->
    <div class="hidden lg:flex lg:w-[40%] lg:fixed lg:left-0 lg:top-0 h-full relative overflow-hidden bg-[#0a0b0f] flex-col p-12 text-white shadow-2xl shadow-black/50 z-10" style="min-height: 100vh;">
      <!-- Imagen de fondo corporativa -->
      <div class="absolute inset-0">
        <img src="/login.png" alt="POS Empresarial" class="w-full h-full object-cover object-center" />
      </div>
      <!-- Capa obsidiana pulida con textura -->
      <div class="absolute inset-0 bg-gradient-to-b from-[#0a0b0f]/[0.82] via-[#0d0e14]/[0.75] to-[#060710]/[0.88]"></div>
      <!-- Reflejo sutil de luz -->
      <div class="absolute inset-0 bg-gradient-to-br from-white/[0.04] via-transparent to-transparent"></div>
      <!-- Red de datos global sutil -->
      <div class="absolute inset-0 opacity-[0.04]" style="background-image: radial-gradient(circle at 25% 25%, rgba(0,180,160,0.4) 1px, transparent 1px), radial-gradient(circle at 75% 75%, rgba(0,180,160,0.3) 1px, transparent 1px), radial-gradient(circle at 50% 50%, rgba(0,180,160,0.2) 0.5px, transparent 0.5px); background-size: 60px 60px, 80px 80px, 40px 40px;"></div>

      <!-- Top: Logo + Botón Radio -->
      <div class="relative z-10 flex items-center justify-between mb-8 animate-fade-in-up">
        <div>
          <h1 class="text-2xl font-extrabold tracking-tight text-white font-['Inter',sans-serif]" style="text-shadow: 0 1px 8px rgba(0,0,0,0.5);">105 POS Pro</h1>
          <p class="text-[10px] text-[#00b894] font-bold uppercase tracking-[0.25em] mt-1.5" style="text-shadow: 0 1px 4px rgba(0,0,0,0.4);">Plataforma Empresarial</p>
        </div>

        <!-- Botón Radio -->
        <button
          @click="radioOpen = true"
          title="Abrir Radio 105"
          class="flex items-center gap-3 px-4 py-2 rounded-xl bg-white/[0.06] hover:bg-white/[0.10] backdrop-blur-md border border-white/[0.08] hover:border-white/[0.15] text-white hover:-translate-y-0.5 hover:shadow-lg hover:shadow-black/30 transition-all duration-300 group"
        >
          <svg class="w-[17px] h-[17px] flex-shrink-0 text-white/60 group-hover:text-[#00b894] transition-colors duration-300" fill="currentColor" viewBox="0 0 24 24">
            <path d="M9 3v10.55A4 4 0 1 0 11 17V7h4V3H9Z"/>
          </svg>
          <span class="flex items-center gap-2">
            <span class="text-[13px] font-medium text-white/80 font-['Inter',sans-serif]">Radio</span>
            <span class="w-1.5 h-1.5 rounded-full bg-[#00b894] animate-pulse flex-shrink-0"></span>
          </span>
        </button>
      </div>

      <!-- Centro: Mensaje Corporativo -->
      <div class="relative z-10 flex-1 flex flex-col justify-center max-w-md pr-6 animate-fade-in-up style-delay-150">
        <h2 class="text-[36px] font-extrabold text-white mb-6 tracking-[-0.02em] leading-[1.12] font-['Inter',sans-serif]" style="text-shadow: 0 2px 12px rgba(0,0,0,0.6), 0 1px 3px rgba(0,0,0,0.4);">
          Gestión inteligente para<br/><span class="text-[#00b894]">negocios en expansión.</span>
        </h2>
        <p class="text-white/90 text-[15px] font-normal leading-[1.75] tracking-wide" style="text-shadow: 0 1px 6px rgba(0,0,0,0.5);">
          Sistema integral de Punto de Venta diseñado para la alta dirección. Optimización, control unificado y escalabilidad para operaciones comerciales de alto volumen.
        </p>
      </div>

      <!-- Bottom: Testimonio anclado en la base -->
      <div class="relative z-10 pt-8 mt-auto border-t border-white/[0.10] animate-fade-in-up style-delay-300">
        <p class="text-white/85 text-[15px] mb-6 leading-relaxed font-normal italic" style="text-shadow: 0 1px 6px rgba(0,0,0,0.5);">"Una herramienta sólida que nos permite controlar las ventas de todas nuestras sucursales con total precisión y sin interrupciones."</p>
        <div class="flex items-center gap-4">
           <div class="w-11 h-11 rounded-full bg-gradient-to-br from-slate-600 to-slate-700 flex items-center justify-center text-sm font-bold text-white uppercase border border-white/[0.12] shadow-lg shadow-black/40">
             MJ
           </div>
           <div>
             <p class="text-sm font-semibold text-white tracking-wide" style="text-shadow: 0 1px 4px rgba(0,0,0,0.4);">María José G.B.</p>
             <p class="text-xs text-[#00b894] font-semibold mt-0.5 tracking-wider" style="text-shadow: 0 1px 3px rgba(0,0,0,0.3);">Directora de Operaciones</p>
           </div>
        </div>
      </div>
    </div>


    <!-- ðŸ“ RIGHT PANEL: Formulario Premium (55%) -->
    <div class="w-full lg:ml-[40%] lg:w-[60%] bg-[#f4f5f7] relative min-h-screen" style="display: flex; flex-direction: column; overflow-y: auto;">
      <!-- Superficie de porcelana mate -->
      <div class="absolute inset-0 bg-gradient-to-b from-[#f0f1f3] via-[#f4f5f7] to-[#ecedf0] pointer-events-none"></div>

      <!-- MOBILE HEADER: Solo en móviles -->
      <div class="lg:hidden sticky top-0 z-20 px-5 py-3.5 flex items-center justify-between bg-white/95 backdrop-blur-sm border-b border-slate-100/60 shadow-sm">
        <div>
          <h1 class="text-[19px] font-extrabold text-slate-900 tracking-tight font-['Inter',sans-serif] leading-tight">105 POS Pro</h1>
          <p class="text-[9px] text-[#00b894] font-bold uppercase tracking-[0.25em] mt-0.5">Plataforma Empresarial</p>
        </div>
        <!-- Botón Radio -->
        <button
          @click="radioOpen = true"
          title="Abrir Radio 105"
          class="flex items-center gap-2 px-3 py-2 rounded-xl bg-[#0f172a] hover:bg-[#1e293b] border border-slate-800 text-white transition-all duration-300 group"
        >
          <svg class="w-4 h-4 flex-shrink-0 text-white/80 group-hover:text-[#00b894] transition-colors duration-300" fill="currentColor" viewBox="0 0 24 24">
            <path d="M9 3v10.55A4 4 0 1 0 11 17V7h4V3H9Z"/>
          </svg>
          <span class="flex items-center gap-1.5">
            <span class="text-xs font-medium">Radio</span>
            <span class="w-1.5 h-1.5 rounded-full bg-[#00b894] animate-pulse flex-shrink-0"></span>
          </span>
        </button>
      </div>

      <!-- ðŸ”¥ CONTENEDOR PRINCIPAL -->
      <div class="py-4 sm:py-8" style="flex: 1; display: flex; flex-direction: column; justify-content: center;">
        <div class="w-full max-w-xl mx-auto px-6 sm:px-10 relative z-10">
          
          <!-- STEPPER: Premium Progress Bar -->
          <div v-if="step === 0 || step === 1" class="mb-10">
             <div class="flex items-center gap-4">
                <!-- Step 0: Cuenta -->
                <div class="flex items-center gap-3 min-w-fit">
                   <div 
                     :class="[
                       'w-10 h-10 rounded-2xl flex items-center justify-center text-sm font-bold transition-all duration-500 flex-shrink-0',
                       step >= 0 
                         ? 'bg-[#0f172a] text-white shadow-lg shadow-slate-900/25 ring-4 ring-slate-900/5' 
                         : 'bg-slate-100 text-slate-400'
                     ]"
                   >
                      <svg v-if="step > 0" class="w-5 h-5 text-[#00b894]" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                      </svg>
                      <span v-else class="text-[13px]">1</span>
                   </div>
                   <span class="text-[13px] font-bold uppercase tracking-wider whitespace-nowrap" :class="step >= 0 ? 'text-slate-900' : 'text-slate-400'">Cuenta</span>
                </div>

                <!-- Lí­nea conectora -->
                <div class="h-[2px] rounded-full transition-all duration-700 flex-1" :class="step >= 1 ? 'bg-gradient-to-r from-[#0f172a] via-slate-600 to-slate-400' : 'bg-slate-200/80'"></div>

                <!-- Step 1: Negocio -->
                <div class="flex items-center gap-3 min-w-fit">
                   <div 
                     :class="[
                       'w-10 h-10 rounded-2xl flex items-center justify-center text-sm font-bold transition-all duration-500 flex-shrink-0',
                       step >= 1 
                         ? 'bg-[#0f172a] text-white shadow-lg shadow-slate-900/25 ring-4 ring-slate-900/5' 
                         : 'bg-slate-100/80 text-slate-400'
                     ]"
                   >
                      <span class="text-[13px]">2</span>
                   </div>
                   <span class="text-[13px] font-bold uppercase tracking-wider whitespace-nowrap" :class="step >= 1 ? 'text-slate-900' : 'text-slate-400'">Negocio</span>
                </div>
             </div>
          </div>

          <!-- ðŸ“ STEP 0: Formulario de Cuenta Premium -->
          <div v-if="step === 0" class="animate-fade-in">
            <!-- Header -->
            <div class="mb-8">
              <h2 class="text-[26px] sm:text-[32px] font-extrabold text-slate-900 tracking-[-0.02em] font-['Inter',sans-serif]">
                Crea tu cuenta
              </h2>
              <p class="text-slate-500 mt-2 text-[15px] font-normal tracking-wide">
                Comienza tu prueba gratuita de 3 dí­as
              </p>
            </div>

            <!-- ðŸ”¥ Google Button Premium -->
            <button 
              type="button"
              @click="signInWithGoogle"
              :disabled="isGoogleLoading"
              class="w-full py-4 px-6 bg-white border border-slate-200/80 hover:border-slate-300 text-slate-700 font-semibold rounded-2xl transition-all duration-300 flex items-center justify-center gap-3 hover:bg-white hover:shadow-xl hover:shadow-slate-200/50 mb-8 disabled:opacity-50 disabled:cursor-not-allowed group"
            >
              <svg v-if="!isGoogleLoading" class="w-5 h-5" viewBox="0 0 24 24">
                <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
              </svg>
              <svg v-else class="animate-spin w-5 h-5 text-slate-400" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              <span>{{ isGoogleLoading ? 'Conectando...' : 'Continuar con Google' }}</span>
            </button>

            <!-- Divisor Premium -->
            <div class="relative mb-8">
              <div class="absolute inset-0 flex items-center">
                <div class="w-full border-t border-slate-200/60"></div>
              </div>
              <div class="relative flex justify-center">
                <span class="px-5 bg-[#f4f5f7] text-[13px] text-slate-400 font-medium tracking-wide">o continúa con email</span>
              </div>
            </div>

            <!-- ðŸ“ FORMULARIO Premium -->
            <form @submit.prevent="step = 1" class="space-y-5">
              <!-- Nombre del Negocio -->
              <div class="space-y-2">
                <label class="text-[13px] font-semibold text-slate-600 uppercase tracking-wider">Nombre del Negocio</label>
                <div class="relative group">
                  <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <svg class="w-5 h-5 text-slate-400/70 group-focus-within:text-slate-600 transition-colors duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                  </div>
                  <input 
                    v-model="form.company_name"
                    type="text" 
                    required 
                    class="w-full h-14 pl-12 pr-4 bg-white border border-slate-200/80 rounded-2xl focus:bg-white focus:border-slate-400 focus:ring-4 focus:ring-slate-900/5 focus:shadow-lg focus:shadow-slate-200/40 transition-all duration-300 outline-none text-slate-900 placeholder-slate-400 font-medium" 
                    placeholder="Ej. Cafeterí­a Central"
                    @input="generateSubdomain"
                  >
                </div>
              </div>

              <!-- Email -->
              <div class="space-y-2">
                <label class="text-[13px] font-semibold text-slate-600 uppercase tracking-wider">Correo Electrónico</label>
                <div class="relative group">
                  <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <svg class="w-5 h-5 text-slate-400/70 group-focus-within:text-slate-600 transition-colors duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                  </div>
                  <input 
                    v-model="form.email"
                    type="email" 
                    required 
                    class="w-full h-14 pl-12 pr-4 bg-white border border-slate-200/80 rounded-2xl focus:bg-white focus:border-slate-400 focus:ring-4 focus:ring-slate-900/5 focus:shadow-lg focus:shadow-slate-200/40 transition-all duration-300 outline-none text-slate-900 placeholder-slate-400 font-medium" 
                    placeholder="tu@empresa.com"
                  >
                </div>
              </div>

              <!-- Contraseña -->
              <div class="space-y-2">
                <label class="text-[13px] font-semibold text-slate-600 uppercase tracking-wider">Contraseña</label>
                <div class="relative group security-halo-wrapper">
                  <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <svg class="w-5 h-5 text-slate-400/70 group-focus-within:text-slate-600 transition-colors duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                  </div>
                  <input 
                    v-model="form.password"
                    :type="showPassword ? 'text' : 'password'"
                    required 
                    minlength="8"
                    autocomplete="new-password"
                    class="w-full h-14 pl-12 pr-14 bg-white border border-slate-200/80 rounded-2xl focus:bg-white focus:border-cyan-400/60 focus:ring-4 focus:ring-cyan-400/10 focus:shadow-[0_0_20px_rgba(0,200,180,0.08)] transition-all duration-300 outline-none text-slate-900 placeholder-slate-400 font-medium" 
                    placeholder="Mí­nimo 8 caracteres"
                  >
                  <button
                    type="button"
                    @click="showPassword = !showPassword"
                    class="absolute inset-y-0 right-0 pr-4 flex items-center text-slate-400/60 hover:text-slate-600 transition-colors duration-300"
                  >
                    <svg v-if="!showPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                    <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                    </svg>
                  </button>
                </div>
              </div>

              <!-- Botón Continuar Premium - Záfiro Pulido -->
              <div class="pt-3">
                <button 
                  type="submit"
                  class="group w-full h-14 bg-gradient-to-r from-[#0f172a] via-[#1e293b] to-[#0f172a] hover:from-[#020617] hover:via-[#0f172a] hover:to-[#020617] text-white font-bold rounded-2xl shadow-xl shadow-slate-900/20 hover:shadow-2xl hover:shadow-slate-900/30 transition-all duration-400 flex items-center justify-center gap-2.5 btn-security-glow"
                >
                  <span class="text-[15px] tracking-wide">Continuar</span>
                  <svg class="w-5 h-5 transform group-hover:translate-x-1.5 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                  </svg>
                </button>
              </div>
            </form>
            
            <!-- Login Link -->
            <p class="text-center mt-8 text-[14px] text-slate-500 tracking-wide">
              ¿Ya tienes cuenta? <a href="/login" class="text-slate-900 font-bold hover:text-[#00b894] transition-colors duration-300">Inicia Sesión</a>
            </p>
          </div>

          <!-- ðŸ“ STEP 1: Información del Negocio Premium -->
          <div v-if="step === 1" class="animate-fade-in">
            <!-- Header -->
            <div class="mb-6">
              <h2 class="text-[30px] font-extrabold text-slate-900 tracking-[-0.02em] font-['Inter',sans-serif]">Información del negocio</h2>
              <p class="text-slate-500 mt-1 text-[15px] tracking-wide">Cuéntanos sobre tu empresa</p>
            </div>

            <form @submit.prevent="validateStep1" class="space-y-5">
              <!-- ðŸŽ¯ GRID 2 COLUMNAS -->
              <div class="grid md:grid-cols-2 gap-4">
                <!-- Tu Nombre -->
                <div class="space-y-2">
                  <label class="text-[13px] font-semibold text-slate-600 uppercase tracking-wider flex items-center gap-2">
                    Tu Nombre Completo
                    <span v-if="googleUserData" class="text-[10px] font-bold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-full border border-emerald-100 normal-case tracking-normal">Google</span>
                  </label>
                  <input 
                    v-model="form.owner_name" 
                    type="text" 
                    required
                    class="w-full h-14 px-4 rounded-2xl border border-slate-200/80 transition-all duration-300 outline-none font-medium placeholder-slate-400 bg-white focus:bg-white focus:border-slate-400 focus:ring-4 focus:ring-slate-900/5 focus:shadow-lg focus:shadow-slate-200/40 text-slate-900"
                    placeholder="Ej. Juan Pérez"
                  >
                </div>

                <!-- Email (si viene de Google) O Cédula -->
                <div v-if="googleUserData" class="space-y-2">
                  <label class="text-[13px] font-semibold text-slate-600 uppercase tracking-wider flex items-center gap-2">
                    Email
                    <span class="text-[10px] font-bold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-full border border-emerald-100 normal-case tracking-normal">Google</span>
                  </label>
                  <input 
                    v-model="form.email" 
                    type="email" 
                    required
                    readonly
                    class="w-full h-14 px-4 rounded-2xl border border-slate-200/80 bg-slate-50 text-slate-500 cursor-not-allowed font-medium" 
                    placeholder="tu@email.com"
                  >
                </div>
                <div v-else class="space-y-2">
                  <label class="text-[13px] font-semibold text-slate-600 uppercase tracking-wider">Cédula / NIT</label>
                  <input 
                    v-model="form.cedula" 
                    type="text" 
                    required 
                    @input="form.cedula = form.cedula.replace(/[^0-9]/g, '')"
                    maxlength="15"
                    class="w-full h-14 px-4 bg-white border border-slate-200/80 rounded-2xl focus:bg-white focus:border-slate-400 focus:ring-4 focus:ring-slate-900/5 focus:shadow-lg focus:shadow-slate-200/40 transition-all duration-300 outline-none text-slate-900 font-medium placeholder-slate-400" 
                    placeholder="Ej. 123456789"
                  >
                </div>
              </div>

              <!-- Nombre del Negocio -->
              <div class="space-y-2">
                <label class="text-[13px] font-semibold text-slate-600 uppercase tracking-wider">Nombre del Negocio</label>
                <input 
                  v-model="form.company_name" 
                  type="text" 
                  required 
                  class="w-full h-14 px-4 bg-white border border-slate-200/80 rounded-2xl focus:bg-white focus:border-slate-400 focus:ring-4 focus:ring-slate-900/5 focus:shadow-lg focus:shadow-slate-200/40 transition-all duration-300 outline-none text-slate-900 font-medium placeholder-slate-400" 
                  placeholder="Ej. Cafeterí­a Central" 
                  @input="generateSubdomain"
                >
              </div>

              <!-- Cédula / NIT (solo si viene de Google) -->
              <div v-if="googleUserData" class="space-y-2">
                <label class="text-[13px] font-semibold text-slate-600 uppercase tracking-wider">Cédula / NIT</label>
                <input 
                  v-model="form.cedula" 
                  type="text" 
                  required 
                  class="w-full h-14 px-4 bg-white border border-slate-200/80 rounded-2xl focus:bg-white focus:border-slate-400 focus:ring-4 focus:ring-slate-900/5 focus:shadow-lg focus:shadow-slate-200/40 transition-all duration-300 outline-none text-slate-900 font-medium placeholder-slate-400" 
                  placeholder="Ej. 123456789"
                >
              </div>

              <!-- Dirección Web (Subdomain) -->
              <div class="space-y-2">
                <label class="text-[13px] font-semibold text-slate-600 uppercase tracking-wider flex justify-between items-center">
                  <span>Dirección Web</span>
                  <span class="text-[11px] font-normal text-slate-400 normal-case tracking-normal">Tu enlace íºnico</span>
                </label>
                <div class="relative group">
                  <input 
                    v-model="form.subdomain" 
                    type="text" 
                    required 
                    class="w-full h-14 pl-4 pr-36 bg-white border border-slate-200/80 rounded-2xl focus:bg-white focus:border-slate-400 focus:ring-4 focus:ring-slate-900/5 focus:shadow-lg focus:shadow-slate-200/40 transition-all duration-300 outline-none text-slate-900 font-mono font-medium placeholder-slate-400" 
                    placeholder="mi-negocio" 
                    @input="checkAvailability"
                    @blur="checkAvailability"
                  >
                  <div class="absolute right-0 top-0 h-full flex items-center pr-4 pointer-events-none">
                    <span class="text-slate-500 font-medium bg-slate-100 px-3 py-1.5 rounded-lg text-sm group-focus-within:text-slate-900 group-focus-within:bg-slate-200 transition-colors">.105pos.pro</span>
                  </div>
                </div>
                
                <!-- Estado de Disponibilidad -->
                <div class="min-h-6 mt-2">
                   <transition name="fade">
                      <p v-if="availabilityStatus === 'checking'" class="text-xs text-slate-500 flex items-center">
                         <svg class="animate-spin w-3.5 h-3.5 mr-1.5" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                         Verificando disponibilidad...
                      </p>
                      <p v-else-if="availabilityStatus === 'available'" class="text-xs text-emerald-600 flex items-center font-semibold">
                         <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                         Disponible: {{ form.subdomain }}.105pos.pro
                      </p>
                      <p v-else-if="availabilityStatus === 'taken'" class="text-xs text-rose-600 flex items-center font-semibold">
                         <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                         No disponible - Prueba otro nombre
                      </p>
                      <p v-else-if="availabilityStatus === 'invalid'" class="text-xs text-amber-600 flex items-center font-semibold">
                         <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                         Solo letras miníºsculas, níºmeros y guiones (-)
                      </p>
                      <p v-else-if="availabilityStatus === 'timeout'" class="text-xs text-orange-600 flex items-center font-semibold">
                         <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                         Tiempo de espera agotado - Intenta nuevamente
                      </p>
                      <p v-else-if="availabilityStatus === 'network'" class="text-xs text-red-600 flex items-center font-semibold">
                         <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636a9 9 0 010 12.728m0 0l-2.829-2.829m2.829 2.829L21 21M15.536 8.464a5 5 0 010 7.072m0 0l-2.829-2.829m-4.243 2.829a4.978 4.978 0 01-1.414-2.83m-1.414 5.658a9 9 0 01-2.167-9.238m7.824 2.167a1 1 0 111.414 1.414m-1.414-1.414L3 3m8.293 8.293l1.414 1.414"></path></svg>
                         Sin conexión al servidor - Verifica tu internet
                      </p>
                      <p v-else-if="availabilityStatus === 'error'" class="text-xs text-red-600 flex items-start font-semibold">
                         <svg class="w-4 h-4 mr-1.5 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                         <span>Error del servidor - Presiona F12 y revisa la pestaí±a Console</span>
                      </p>
                   </transition>
                </div>
              </div>

              <!-- âœ… Términos y Condiciones -->
              <div class="pt-4 pb-2">
                <label class="flex items-start gap-3 cursor-pointer group">
                  <input 
                    type="checkbox" 
                    v-model="acceptedTerms"
                    class="w-5 h-5 text-slate-900 border-slate-300 rounded focus:ring-slate-900 focus:ring-2 mt-0.5 cursor-pointer"
                  >
                  <span class="text-sm text-slate-600 leading-relaxed">
                    Acepto los 
                    <a 
                      href="/terminos-condiciones" 
                      @click.prevent="openTermsInSameWindow"
                      class="text-slate-900 hover:underline font-semibold"
                    >
                      Términos y Condiciones
                    </a>
                    y la 
                    <a 
                      href="/politica-privacidad" 
                      @click.prevent="openPrivacyInSameWindow"
                      class="text-slate-900 hover:underline font-semibold"
                    >
                      Polí­tica de Privacidad
                    </a>
                  </span>
                </label>
              </div>

              <!-- ðŸ”¥ Botones de Acción -->
              <div class="pt-4 flex items-center gap-4">
                 <button 
                   type="button" 
                   @click="step = 0" 
                   class="group flex items-center gap-2 px-6 h-12 bg-white border border-slate-200/80 hover:bg-slate-50 hover:border-slate-300 text-slate-600 font-semibold rounded-2xl transition-all duration-300 hover:shadow-md"
                 >
                   <svg class="w-4 h-4 transform group-hover:-translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                     <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
                   </svg>
                   Atrás
                 </button>
                 <button 
                   type="submit" 
                   :disabled="availabilityStatus !== 'available' || !form.owner_name || !form.company_name || !form.cedula || !acceptedTerms || isSubmitting" 
                   class="group flex-1 h-14 bg-gradient-to-r from-[#0f172a] via-[#1e293b] to-[#0f172a] hover:from-[#020617] hover:via-[#0f172a] hover:to-[#020617] text-white font-bold rounded-2xl shadow-xl shadow-slate-900/20 hover:shadow-2xl hover:shadow-slate-900/30 transition-all duration-400 disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2 btn-security-glow"
                 >
                   <svg v-if="isSubmitting" class="animate-spin -ml-1 mr-2 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                      <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                      <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                   </svg>
                   <span v-if="!isSubmitting">Crear mi Cuenta</span>
                   <span v-else-if="step === 2">Creando cuenta...</span>
                   <span v-else>Verificando datos...</span>
                   <svg v-if="!isSubmitting" class="w-5 h-5 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                     <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                   </svg>
                 </button>
              </div>
            </form>
          </div>

          <!-- ðŸ”„ STEP 2: Procesando (Loading State Premium) -->
          <div v-if="step === 2" class="animate-fade-in flex flex-col items-center justify-center py-20">
             <!-- Spinner Premium -->
             <div class="relative w-20 h-20 mb-8">
                <div class="absolute inset-0 border-4 border-slate-100 rounded-full"></div>
                <div class="absolute inset-0 border-4 border-slate-800 rounded-full border-t-transparent animate-spin"></div>
                <div class="absolute inset-2 border-4 border-slate-200 rounded-full border-b-transparent animate-spin" style="animation-direction: reverse; animation-duration: 1.5s;"></div>
             </div>
             
             <h2 class="text-2xl font-bold text-slate-900 mb-3 text-center">Configurando tu sistema</h2>
             
             <div class="h-8 overflow-hidden relative">
                <transition name="slide-up" mode="out-in">
                   <p :key="currentMessageIndex" class="text-slate-500 font-medium text-center">
                      {{ marketingMessages[currentMessageIndex] }}
                   </p>
                </transition>
             </div>

             <!-- Progress dots -->
             <div class="flex items-center gap-2 mt-8">
               <div class="w-2 h-2 rounded-full bg-slate-800 animate-pulse"></div>
               <div class="w-2 h-2 rounded-full bg-slate-400 animate-pulse" style="animation-delay: 0.2s;"></div>
               <div class="w-2 h-2 rounded-full bg-slate-300 animate-pulse" style="animation-delay: 0.4s;"></div>
             </div>
          </div>

          <!-- âœ… STEP 3: Registro Exitoso - PASE DE ACCESO DIGITAL PREMIUM -->
          <div v-if="step === 3" class="animate-fade-in flex items-center justify-center py-8">
            <div class="w-full max-w-md">
              
              <!-- Tí­tulo Alegre y Elegante -->
              <div class="text-center mb-8 flex flex-col items-center">
                <!-- ícono Check Elegante -->
                <div class="w-16 h-16 rounded-full bg-[#009F7A]/10 flex items-center justify-center mb-4">
                  <svg class="w-8 h-8 text-[#009F7A]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path>
                  </svg>
                </div>

                <h2 class="text-3xl font-bold text-slate-900 tracking-tight mb-2">
                  &iexcl;Cuenta creada con &eacute;xito!
                </h2>
                <p class="text-slate-500">
                  Tu infraestructura dedicada ha sido desplegada
                </p>
              </div>

              <!-- Tarjeta de Acceso Digital Unificada -->
              <div class="bg-white rounded-2xl p-8 mb-8 border border-slate-200 shadow-xl shadow-slate-200/50 relative overflow-hidden">
                <div class="relative z-10 text-center">
                  <p class="text-[11px] font-bold text-slate-400 tracking-[0.2em] uppercase mb-3">Espacio Asignado</p>
                  <p class="text-2xl font-bold text-slate-900 mb-6">{{ form.company_name }}</p>
                  
                  <!-- Tag de Subdominio Luminosa -->
                  <div class="bg-slate-100 rounded-xl px-4 py-3 inline-flex items-center justify-center border border-slate-200 mx-auto max-w-full group">
                    <div class="flex items-center justify-center gap-2">
                      <span class="text-slate-700 text-sm md:text-base font-medium truncate">
                        {{ form.subdomain }}.105pos.pro
                      </span>
                      <button class="text-slate-400 hover:text-slate-600 transition-colors flex-shrink-0" title="Copiar enlace">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                        </svg>
                      </button>
                    </div>
                  </div>
                </div>
              </div>

              <!-- CTA Button Premium -->
              <button
                @click="goToPlanSelection"
                class="group w-full h-14 bg-gradient-to-r from-[#0f172a] via-[#1e293b] to-[#0f172a] hover:from-[#020617] hover:via-[#0f172a] hover:to-[#020617] text-white font-bold rounded-2xl shadow-xl shadow-slate-900/20 hover:shadow-2xl hover:shadow-slate-900/30 transition-all duration-400 hover:-translate-y-0.5 flex items-center justify-center gap-2.5 btn-security-glow"
              >
                <span>Elegir Mi Plan</span>
                <svg class="w-5 h-5 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                </svg>
              </button>

              <!-- Trust Badges Premium (Con color) -->
              <div class="flex items-center justify-center gap-6 mt-8 pt-6 border-t border-slate-200">
                <div class="flex items-center gap-1.5 text-xs text-slate-500">
                  <svg class="w-4 h-4 text-[#009F7A]" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                  </svg>
                  <span>Pago Seguro</span>
                </div>
                <div class="flex items-center gap-1.5 text-xs text-slate-500">
                  <svg class="w-4 h-4 text-[#009F7A]" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                  </svg>
                  <span>3 dí­as gratis</span>
                </div>
                <div class="flex items-center gap-1.5 text-xs text-slate-500">
                  <svg class="w-4 h-4 text-[#009F7A]" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                  </svg>
                  <span>Cancela cuando quieras</span>
                </div>
              </div>
            </div>
          </div>

          <!-- ðŸ”„ STEP 4: Redirigiendo - DISEí‘O PREMIUM -->
          <div v-if="step === 4" class="animate-fade-in flex items-center justify-center py-20">
             <div class="text-center">
                <!-- Success Icon with animation -->
                <div class="relative inline-flex items-center justify-center mb-6">
                   <div class="absolute w-24 h-24 rounded-full bg-emerald-100 animate-pulse"></div>
                   <div class="relative w-20 h-20 bg-gradient-to-br from-emerald-400 to-emerald-600 rounded-full flex items-center justify-center shadow-lg shadow-emerald-500/30">
                      <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path>
                      </svg>
                   </div>
                </div>
                
                <h2 class="text-3xl font-bold text-slate-900 mb-2">Â¡Todo listo!</h2>
                <p class="text-slate-500 mb-8">Tu sistema ha sido configurado exitosamente</p>
                
                <!-- Loading redirect badge -->
                <div class="inline-flex items-center gap-3 px-6 py-3.5 bg-slate-900 text-white rounded-xl text-sm font-semibold shadow-lg shadow-slate-900/25">
                   <svg class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24">
                     <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                     <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                   </svg>
                   <span>Redirigiendo a tu panel...</span>
                </div>
             </div>
          </div>

        </div>
      </div>
      
      <!-- ðŸ“± Mobile Footer -->
      <div class="lg:hidden p-6 text-center text-xs text-slate-400 border-t border-slate-100/60 bg-slate-50/50">
         <p class="font-semibold text-slate-500 tracking-wide">© 2024 105 POS Pro</p>
         <p class="mt-1 tracking-wide">Todos los derechos reservados</p>
      </div>
    </div>
  </div>

  <!-- Modal Radio -->
  <RadioPlayerModal :is-open="radioOpen" @close="radioOpen = false" />

</template>

<script setup>
import { ref, reactive, onMounted, onUnmounted } from 'vue'
import axios from 'axios'
import googleAuthService from '../services/googleAuthService'
import RadioPlayerModal from '../components/RadioPlayerModal.vue'
import { useToast } from '../composables/useToast.js'

const { showError, showWarning, showInfo } = useToast()

// Dominios de correo conocidos
const KNOWN_EMAIL_DOMAINS = [
  'gmail.com', 'hotmail.com', 'outlook.com', 'yahoo.com', 'yahoo.es',
  'aol.com', 'icloud.com', 'mail.com', 'protonmail.com', 'tutanota.com',
  'fastmail.com', 'zoho.com', 'yandex.com', 'rediffmail.com', 'gmx.com',
  'live.com', 'msn.com'
]

// State
const step = ref(0)  // 0: Datos Iniciales | 1: Propietario | 2: Procesando | 3: Selección de Plan | 4: Éxito
const isSubmitting = ref(false)
const radioOpen = ref(false)
const isGoogleLoading = ref(false)
const successData = ref(null)
const availabilityStatus = ref(null) // null, 'checking', 'available', 'taken'
const checkTimeout = ref(null)
const showPassword = ref(false)
const tenantCreated = ref(null) // Datos del tenant recién creado
const googleUserData = ref(null) // ðŸ†• Datos del usuario de Google
const googleCode = ref(null) // ðŸ†• Código de autorización de Google
const showGoogleToast = ref(false) // ðŸ”” Control de Toast de Google
const acceptedTerms = ref(false) // âœ… Aceptación de Términos y Condiciones

// ðŸš¨ Modal Unificado para Duplicados
const showDuplicateModal = ref(false) // Control del modal unificado
const duplicateType = ref('') // 'email' o 'document'
const duplicateValue = ref('') // Valor duplicado (email o NIT/CC)
const duplicateTenants = ref([]) // Array de tenants que usan el NIT/CC

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
  plan: 'pending' // Se actualizarí¡ cuando usuario seleccione (emprendedor, negocio_pro, enterprise)
})

// Methods
// Validadores de inputs
const validateEmail = (email) => {
  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/
  if (!emailRegex.test(email)) return false
  
  const domain = email.split('@')[1].toLowerCase()
  return KNOWN_EMAIL_DOMAINS.includes(domain) || email.includes('.')
}

const validateOwnerName = (name) => {
  // Permite letras, níºmeros, espacios, acentos y guiones
  const nameRegex = /^[a-zA-Z0-9í¡éí­óíºíí‰íí“íší±í‘\s'-]+$/
  return nameRegex.test(name) && name.trim().length >= 3
}

const validateCedula = (cedula) => {
  // Solo validar que no esté vací­o y que sea solo níºmeros
  // El input ya bloquea caracteres no numéricos en tiempo real
  return cedula.trim().length > 0
}

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

  // Limpiar espacios y convertir a miníºsculas
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
      // VITE_API_URL es la URL base, se agrega /api
      const apiUrl = import.meta.env.VITE_API_URL ? `${import.meta.env.VITE_API_URL}/api` : '/api'
      
      
      const response = await axios.post(`${apiUrl}/check-domain`, { 
        subdomain: form.subdomain 
      }, {
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json'
        },
        timeout: 10000 // 10 segundos timeout
      })
      
      if (response.data && typeof response.data.available !== 'undefined') {
        availabilityStatus.value = response.data.available ? 'available' : 'taken'
      } else {
        console.error('Respuesta inesperada:', response.data)
        availabilityStatus.value = 'error'
      }
    } catch (error) {
      console.error('âŒ Error checking domain:', error)
      console.error('Detalles del error:', {
        message: error.message,
        response: error.response?.data,
        status: error.response?.status,
        code: error.code
      })
      
      // Mostrar error especí­fico segíºn el tipo
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
    
    // NO enviar datos de registro - solo iniciar OAuth
    const authUrl = await googleAuthService.initiateGoogleAuth({})
    
    // Redirigir a Google para autenticación
    // Google luego redirigirí¡ a: /api/auth/google/callback
    window.location.href = authUrl
    
  } catch (error) {
    isGoogleLoading.value = false
    console.error('âŒ Error al iniciar OAuth con Google:', error)
    
    let errorMessage = 'No se pudo conectar con Google. Intenta nuevamente.'
    
    if (error.response?.data?.message) {
      errorMessage = error.response.data.message
    }
    
    showError(errorMessage)
  }
}

/**
 * Verifica si el correo ya estí¡ registrado en el sistema
 * @returns {Promise<boolean>} true si el correo existe, false si estí¡ disponible
 */

// ðŸš¨ Cerrar modal unificado de duplicados
const closeDuplicateModal = () => {
  showDuplicateModal.value = false
  duplicateType.value = ''
  duplicateValue.value = ''
  duplicateTenants.value = []
}

// ðŸ”„ Redirigir a recuperación de Contraseña
const goToRecovery = () => {
  closeDuplicateModal()
  window.location.href = '/forgot-password'
}

// ðŸ“ Abrir Términos en la misma ventana (guarda datos en sessionStorage)
const openTermsInSameWindow = () => {
  // Guardar datos del formulario antes de navegar
  sessionStorage.setItem('register_form_data', JSON.stringify(form))
  sessionStorage.setItem('register_return_url', window.location.pathname + window.location.search)
  
  // Navegar a Términos
  window.location.href = '/terminos-condiciones#from-register'
}

// ðŸ”’ Abrir privacidad en la misma ventana (guarda datos en sessionStorage)
const openPrivacyInSameWindow = () => {
  // Guardar datos del formulario antes de navegar
  sessionStorage.setItem('register_form_data', JSON.stringify(form))
  sessionStorage.setItem('register_return_url', window.location.pathname + window.location.search)
  
  // Navegar a privacidad
  window.location.href = '/politica-privacidad#from-register'
}

// ðŸ†” Verificar si un NIT/CC ya existe en el sistema
const checkDocumentExists = async (cedula) => {
  try {
    // VITE_API_URL es la URL base, se agrega /api
    const apiUrl = import.meta.env.VITE_API_URL ? `${import.meta.env.VITE_API_URL}/api` : '/api'
    
    const response = await axios.get(`${apiUrl}/central/check-document`, {
      params: { cedula }
    })
    
    if (response.data.exists) {
      // Mostrar modal unificado con info de los tenants existentes
      duplicateType.value = 'document'
      duplicateValue.value = cedula
      duplicateTenants.value = response.data.tenants || []
      showDuplicateModal.value = true
      return true // NIT/CC exists
    }
    
    return false // NIT/CC available
    
  } catch (error) {
    console.error('âŒ Error al verificar NIT/CC:', error)
    
    // Si hay error en la verificación, mostrar mensaje pero permitir continuar
    const shouldContinue = confirm(
      'No se pudo verificar si el NIT/C\u00e9dula est\u00e1 disponible. ' +
      'Esto puede deberse a un problema temporal de conexión.\n\n' +
      '¿Deseas continuar con el registro de todas formas?'
    )
    
    return !shouldContinue // Si dice que Sí quiere continuar, retornamos false (no existe)
  }
}

const checkEmailExists = async () => {
  try {
    // VITE_API_URL es la URL base, se agrega /api
    const apiUrl = import.meta.env.VITE_API_URL ? `${import.meta.env.VITE_API_URL}/api` : '/api'
    
    const response = await axios.get(`${apiUrl}/central/check-email`, {
      params: { email: form.email }
    })
    
    if (response.data.exists) {
      // Mostrar modal unificado para email duplicado
      duplicateType.value = 'email'
      duplicateValue.value = form.email
      duplicateTenants.value = [] // Email no necesita lista de tenants
      showDuplicateModal.value = true
      return true // Email exists
    }
    
    return false // Email available
    
  } catch (error) {
    console.error('âŒ Error al verificar correo:', error)
    
    // Si hay error en la verificación, mostrar mensaje pero permitir continuar
    // (puede ser un error de red temporal)
    const shouldContinue = confirm(
      'No se pudo verificar si el correo est\u00e1 disponible. ' +
      'Esto puede deberse a un problema temporal de conexión.\n\n' +
      '¿Deseas continuar con el registro de todas formas?'
    )
    
    return !shouldContinue // Si dice que Sí quiere continuar, retornamos false (no existe)
  }
}

const validateStep1 = async () => {
  // Guardia anti doble-clic: si ya estamos procesando, ignorar
  if (isSubmitting.value) return
  // Bloquear INMEDIATAMENTE para prevenir doble-clic
  isSubmitting.value = true

  if (!form.owner_name || !form.company_name || !form.subdomain || !form.cedula) {
    showWarning('Por favor completa todos los campos')
    isSubmitting.value = false
    return
  }
  
  // Validar nombre del propietario
  if (!validateOwnerName(form.owner_name)) {
    showWarning('El nombre debe contener al menos 3 caracteres (se permiten letras, números, espacios y guiones)')
    isSubmitting.value = false
    return
  }
  
  if (!form.email) {
    showWarning('Por favor ingresa tu correo electrónico')
    isSubmitting.value = false
    return
  }
  
  // Validar formato y dominio del correo
  if (!validateEmail(form.email)) {
    showWarning('Por favor ingresa un correo v\u00e1lido (gmail.com, hotmail.com, yahoo.com, etc.)')
    isSubmitting.value = false
    return
  }
  
  // Validar NIT/Cédula
  if (!validateCedula(form.cedula)) {
    showWarning('Por favor completa el NIT/Cédula')
    isSubmitting.value = false
    return
  }
  
  if (!acceptedTerms.value) {
    showWarning('Debes aceptar los Términos y Condiciones y la Polí­tica de Privacidad para continuar')
    isSubmitting.value = false
    return
  }
  
  if (availabilityStatus.value === 'checking') {
    showInfo('Esperando verificación de disponibilidad del sitio web...')
    isSubmitting.value = false
    return
  }
  
  if (availabilityStatus.value === 'taken') {
    showWarning('El sitio web no est\u00e1 disponible. Por favor elige otro nombre.')
    isSubmitting.value = false
    return
  }
  
  if (availabilityStatus.value === 'invalid') {
    showWarning('El sitio web solo puede contener letras miníºsculas, níºmeros y guiones.')
    isSubmitting.value = false
    return
  }
  
  if (availabilityStatus.value === 'error') {
    showError('Hubo un error al verificar la disponibilidad. Por favor verifica tu conexión y la consola del navegador.')
    isSubmitting.value = false
    return
  }
  
  if (availabilityStatus.value !== 'available') {
    showInfo('Por favor espera a que se verifique la disponibilidad del sitio web')
    isSubmitting.value = false
    return
  }

  try {
    // Validar si el correo ya existe
    const emailExists = await checkEmailExists()
    if (emailExists) {
      isSubmitting.value = false
      return
    }

    // Validar si el NIT/CC ya existe
    const documentExists = await checkDocumentExists(form.cedula)
    if (documentExists) {
      isSubmitting.value = false
      return
    }

    // Crear la cuenta directamente
    registerTenant()
  } catch {
    isSubmitting.value = false
  }
}

// Función para ir a la vista de selección de plan
const goToPlanSelection = () => {
  // Obtener datos del registro para obtener el subdomain del tenant
  const registrationData = localStorage.getItem('registration_data')
  
  if (registrationData) {
    const data = JSON.parse(registrationData)
    
    // ðŸ”‘ CRíTICO: Pasar datos como query params porque localStorage NO se comparte entre dominios
    const params = new URLSearchParams({
      tenant_id: data.tenant_id,
      company: data.company_name || '',
      subdomain: data.subdomain || ''
    })
    
    // En producción sin subdominios DNS, ir directo a /select-plan en el dominio principal
    const targetUrl = `/select-plan?${params.toString()}`
    window.location.href = targetUrl
  } else {
    // Si no hay datos (caso raro), redirigir a la app central como fallback
    console.warn('âš ï¸ No hay registration_data en localStorage')
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
    // VITE_API_URL es la URL base, se agrega /api
    const apiUrl = import.meta.env.VITE_API_URL ? `${import.meta.env.VITE_API_URL}/api` : '/api'
    
    // ðŸ†• Si hay datos de Google, incluirlos en el registro
    const registrationPayload = {
      ...form,
      plan: 'pending' // Plan pendiente de selección
    }
    
    if (googleUserData.value) {
      registrationPayload.google_id = googleUserData.value.google_id
      registrationPayload.google_email = googleUserData.value.email
      registrationPayload.google_name = googleUserData.value.name
      registrationPayload.google_picture = googleUserData.value.picture
      
    }
    
    // Crear tenant SIN plan definido aíºn (o con plan temporal)
    const response = await axios.post(`${apiUrl}/register-tenant`, registrationPayload)
    
    if (response.data.success) {
      // Guardar datos del tenant creado
      tenantCreated.value = response.data
      clearInterval(messageInterval)
      isSubmitting.value = false
      
      // ðŸ”‘ GUARDAR TOKEN DE AUTENTICACIí“N si viene en la respuesta
      if (response.data.data && response.data.data.token) {
        const token = response.data.data.token
        const user = response.data.data.user
        
        // Guardar en localStorage
        localStorage.setItem('authToken', token)
        localStorage.setItem('user', JSON.stringify(user))
        localStorage.setItem('loginTimestamp', Date.now().toString())
        
        // Configurar axios para futuras peticiones
        axios.defaults.headers.common['Authorization'] = `Bearer ${token}`
        
      }
      
      // âœ… GUARDAR DATOS DEL REGISTRO EN LOCALSTORAGE para proceso de pago
      const registrationData = {
        company_name: form.company_name,
        storeName: form.company_name,
        owner_name: form.owner_name,
        email: form.email,
        subdomain: form.subdomain,
        cedula: form.cedula,
        tenant_id: response.data.tenant_id,
        redirect_url: response.data.redirect_url,
        // ðŸ”‘ Ya NO necesitamos temp_password porque el usuario ya estí¡ autenticado
        // temp_password: form.password, // ELIMINADO
        // Si es Google, marcar para auto-login sin password
        is_google: googleUserData.value ? true : false,
        // Marcar que ya tiene token activo
        authenticated: response.data.data && response.data.data.token ? true : false
      }
      localStorage.setItem('registration_data', JSON.stringify(registrationData))
      
      // Ir a pantalla de éxito (paso 3 - ahora es solo mensaje de éxito)
      step.value = 3
    } else {
      throw new Error('Respuesta sin success=true')
    }

  } catch (error) {
    step.value = 1 // Go back to step 1 on error
    isSubmitting.value = false
    clearInterval(messageInterval)
    
    const responseData = error.response?.data || {}
    const errors = responseData.errors || {}
    const message = responseData.message || ''
    
    // ðŸš¨ Verificar si el error es de NIT/CC duplicado (Check robusto)
    // Verificamos si existe el error en 'cedula' O si el mensaje contiene el texto especí­fico
    // Convertimos a miníºsculas para evitar problemas de case-sensitivity
    const isDuplicateDocument = 
      errors.cedula || 
      message.toLowerCase().includes('ya existe una tienda') || 
      message.toLowerCase().includes('identificación') ||
      message.toLowerCase().includes('nit/cédula');

    if (isDuplicateDocument) {
      // Mostrar modal unificado con info de NIT/CC duplicado
      duplicateType.value = 'document'
      duplicateValue.value = form.cedula
      duplicateTenants.value = [] // ðŸ”’ SECURITY FIX: No mostrar lista de tiendas
      showDuplicateModal.value = true
      return
    }
    
    // ðŸš¨ Verificar si el error es de email duplicado
    if (errors.email) {
      duplicateType.value = 'email'
      duplicateValue.value = form.email
      duplicateTenants.value = []
      showDuplicateModal.value = true
      return
    }
    
    // Para otros errores, mostrar alert normal
    let errorMessage = 'Ocurrió un error al crear la cuenta.'
    if (error.response?.data?.message) {
      errorMessage = error.response.data.message
    } else if (error.response?.data?.errors) {
       errorMessage = Object.values(error.response.data.errors)[0][0]
    }
    
    // ðŸ›¡ï¸ íšLTIMA DEFENSA: Si por alguna razón el check de arriba falló pero el mensaje es de duplicado
    if (errorMessage.toLowerCase().includes('ya existe una tienda') || errorMessage.toLowerCase().includes('identificación')) {
      duplicateType.value = 'document'
      duplicateValue.value = form.cedula
      duplicateTenants.value = [] // ðŸ”’ SECURITY FIX: No mostrar lista de tiendas
      showDuplicateModal.value = true
      return
    }

    showError(errorMessage)
  }
}

// Detectar token de la URL al cargar
onMounted(async () => {
  // ðŸ”’ SECURITY FIX: Prevenir "Ghost Sessions"
  // Limpiamos tokens residuales para evitar que Vue Router redirija a /welcome 
  // mí¡gicamente al interactuar con el botón Atrí¡s o el link Login si habí­a una Sesión vieja guardada.
  const urlParamsCheck = new URLSearchParams(window.location.search);
  if (!urlParamsCheck.get('google_token') && !urlParamsCheck.get('token')) {
    localStorage.removeItem('authToken');
    localStorage.removeItem('user');
    localStorage.removeItem('welcome_seen');
    localStorage.removeItem('loginTimestamp');
  }

  // ðŸ“ RESTAURAR datos del formulario si el usuario regresa de Términos/privacidad
  const savedFormData = sessionStorage.getItem('register_form_data')
  if (savedFormData) {
    try {
      const parsedData = JSON.parse(savedFormData)
      Object.assign(form, parsedData)
      // Limpiar sessionStorage
      sessionStorage.removeItem('register_form_data')
      sessionStorage.removeItem('register_return_url')
      
      // Ir al paso 1 (formulario completo)
      step.value = 1
      
      // Re-verificar disponibilidad del subdominio
      if (form.subdomain) {
        checkAvailability()
      }
    } catch (error) {
      console.error('âŒ Error al restaurar datos del formulario:', error)
    }
  }

  // ðŸ”’ PROTECCIí“N: Solo permitir registro en app central (sin subdominio)
  const hostname = window.location.hostname
  const parts = hostname.split('.')
  
  // Dominios principales permitidos
  const mainDomains = ['localhost', '127.0.0.1', '105pos.pro', 'www.105pos.pro']
  const isMainDomain = mainDomains.includes(hostname)
  
  // Si NO es un dominio principal y tiene mí¡s de 2 partes, es un subdominio
  if (!isMainDomain && parts.length > 2) {
    console.warn('âš ï¸ Intento de acceder a /register desde subdominio. Redirigiendo a app central...')
    const protocol = window.location.protocol
    const port = window.location.port ? `:${window.location.port}` : ''
    
    // Determinar dominio principal segíºn el ambiente
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

  // ðŸ†• Detectar callback de Google OAuth con token
  const googleToken = urlParams.get('google_token')
  
  if (googleToken) {
    isGoogleLoading.value = true
    
    try {
      // Obtener datos de Google usando el token temporal
      const response = await axios.get('/api/auth/google/user-data', {
        params: { token: googleToken }
      })
      
      if (response.data.success && response.data.user) {
        const userData = response.data.user
        
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
        
        // ðŸ”” Mostrar toast de éxito de Google
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
      console.error('âŒ Error al obtener datos de Google:', error)
      showError('Error al procesar datos de Google. El token pudo haber expirado. Intenta nuevamente.')
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
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');

.animate-fade-in {
  animation: fadeIn 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards;
}

@keyframes fadeIn {
  from { opacity: 0; transform: translateY(12px); }
  to { opacity: 1; transform: translateY(0); }
}

.animate-fade-in-up {
  animation: fadeIn 0.7s cubic-bezier(0.16, 1, 0.3, 1) forwards;
  opacity: 0;
}

.style-delay-150 { animation-delay: 150ms; }
.style-delay-300 { animation-delay: 300ms; }

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

/* Custom Scrollbar */
::-webkit-scrollbar {
  width: 5px;
}
::-webkit-scrollbar-track {
  background: transparent;
}
::-webkit-scrollbar-thumb {
  background: #d1d5db;
  border-radius: 3px;
}
::-webkit-scrollbar-thumb:hover {
  background: #9ca3af;
}

/* Security Halo - Pulsing cyan glow on password field */
.security-halo-wrapper:focus-within {
  position: relative;
}
.security-halo-wrapper:focus-within::after {
  content: '';
  position: absolute;
  inset: -3px;
  border-radius: 1.25rem;
  background: linear-gradient(135deg, rgba(0,200,180,0.08), rgba(0,180,220,0.04), transparent);
  pointer-events: none;
  animation: securityPulse 2.5s ease-in-out infinite;
  z-index: -1;
}

@keyframes securityPulse {
  0%, 100% { opacity: 0.4; }
  50% { opacity: 0.8; }
}

/* Button security glow on hover */
.btn-security-glow {
  position: relative;
}
.btn-security-glow::after {
  content: '';
  position: absolute;
  inset: -2px;
  border-radius: 1.15rem;
  background: linear-gradient(135deg, rgba(0,200,180,0.06), transparent, rgba(0,180,220,0.04));
  pointer-events: none;
  opacity: 0;
  transition: opacity 0.4s ease;
  z-index: -1;
}
.btn-security-glow:hover::after {
  opacity: 1;
  animation: securityPulse 2s ease-in-out infinite;
}

/* ðŸ”” Toast Progress Bar Animation */
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

/* âœ… Bounce Once Animation for Success Icon */
.animate-bounce-once {
  animation: bounceOnce 0.6s cubic-bezier(0.36, 0.07, 0.19, 0.97) both;
}

@keyframes bounceOnce {
  0% { transform: scale(0); }
  50% { transform: scale(1.2); }
  70% { transform: scale(0.9); }
  100% { transform: scale(1); }
}
</style>
