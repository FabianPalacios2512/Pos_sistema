<template>
  <!-- Fondo Empresarial Limpio - Igual al Dashboard -->
  <div class="min-h-screen flex items-center justify-center px-4 py-8 font-sans bg-gradient-to-br from-slate-50 to-blue-50/30">
    
    <!-- Main Content - Tarjeta Blanca Central Profesional -->
    <div class="w-full max-w-3xl relative z-10 animate-fade-in">

      <!-- Tarjeta Blanca Central con Sombra Suave - Estilo Dashboard -->
      <div class="bg-white rounded-[24px] shadow-[0_2px_20px_-4px_rgba(0,0,0,0.05)] border border-white overflow-hidden">

        <!-- PANTALLA DE BIENVENIDA - Hero Interactivo -->
        <div v-if="step === 0" class="animate-fade-in px-8 py-12 md:px-16 md:py-16">
          <!-- Logo Principal -->
          <div class="flex items-center justify-center mb-8">
            <img src="/logo.png" alt="105 POS Pro" class="h-20 md:h-24 w-auto object-contain">
          </div>

          <!-- Mensaje de Bienvenida -->
          <div class="text-center mb-10">
            <h1 class="text-4xl md:text-5xl font-black text-slate-900 mb-4 tracking-tight leading-tight">
              Bienvenido a <span class="text-emerald-600">105 POS</span>
            </h1>
            <p class="text-xl md:text-2xl text-slate-600 font-semibold mb-6">
              Sistema de Facturación POS con Control de Inventarios Inteligente
            </p>
          </div>

          <!-- Features Grid -->
          <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-10 max-w-4xl mx-auto">
            <!-- Feature 1 -->
            <div class="bg-gradient-to-br from-emerald-50 to-teal-50 rounded-2xl p-5 border border-emerald-100">
              <div class="w-12 h-12 bg-emerald-600 rounded-xl flex items-center justify-center mb-3">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
              </div>
              <h3 class="font-bold text-slate-900 mb-1 text-base">Facturación Rápida</h3>
              <p class="text-sm text-slate-600 leading-snug">Genera facturas profesionales en segundos</p>
            </div>

            <!-- Feature 2 -->
            <div class="bg-gradient-to-br from-sky-50 to-blue-50 rounded-2xl p-5 border border-sky-100">
              <div class="w-12 h-12 bg-sky-600 rounded-xl flex items-center justify-center mb-3">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                </svg>
              </div>
              <h3 class="font-bold text-slate-900 mb-1 text-base">Inventario Inteligente</h3>
              <p class="text-sm text-slate-600 leading-snug">Control automático de stock y alertas</p>
            </div>

            <!-- Feature 3 -->
            <div class="bg-gradient-to-br from-amber-50 to-orange-50 rounded-2xl p-5 border border-amber-100">
              <div class="w-12 h-12 bg-amber-600 rounded-xl flex items-center justify-center mb-3">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                </svg>
              </div>
              <h3 class="font-bold text-slate-900 mb-1 text-base">Reportes en Tiempo Real</h3>
              <p class="text-sm text-slate-600 leading-snug">Analítica avanzada de ventas y caja</p>
            </div>
          </div>

          <!-- CTA Principal -->
          <div class="text-center">
            <button 
              @click="step = 1" 
              type="button"
              class="inline-flex items-center px-10 py-5 bg-slate-900 hover:bg-black text-white font-black rounded-2xl shadow-lg shadow-slate-400/40 hover:shadow-slate-400/60 transition-all duration-300 text-lg md:text-xl active:scale-95 group"
            >
              <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z" />
              </svg>
              <span>Crea tu Tienda en Segundos</span>
              <svg class="w-6 h-6 ml-3 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6" />
              </svg>
            </button>
            <p class="text-sm text-slate-500 mt-4 font-medium">Sin tarjeta de crédito • Configuración en 2 minutos</p>
          </div>
        </div>

        <!-- Paso 1: Información del Negocio - CONVERSACIONAL -->
        <div v-if="step === 1" class="animate-fade-in px-8 py-10 md:px-12">
          <!-- Conversación Interactiva -->
          <div class="max-w-2xl mx-auto">
            <!-- Avatar del Sistema -->
            <div class="flex items-start space-x-4 mb-8">
              <div class="w-12 h-12 bg-slate-900 rounded-2xl flex items-center justify-center flex-shrink-0">
                <img src="/logo.png" alt="105 POS" class="w-8 h-8 object-contain">
              </div>
              <div class="flex-1 bg-slate-50 rounded-2xl rounded-tl-sm p-5 border border-slate-100">
                <p class="text-slate-900 font-semibold text-lg leading-relaxed">
                  ¡Perfecto! Vamos a crear tu tienda. <br>
                  <span class="text-slate-600 font-medium text-base">Primero cuéntame, ¿cómo te llamas y cuál es el nombre de tu negocio?</span>
                </p>
              </div>
            </div>

            <!-- Formulario Limpio -->
            <form @submit.prevent="validateStep1" class="space-y-5">
              <!-- Respuesta del Usuario (Visual) -->
              <div class="bg-white rounded-2xl border-2 border-slate-200 p-6 space-y-4">
                <!-- Campo: Nombre Completo -->
                <div>
                  <label class="block text-sm font-bold text-slate-700 mb-2 flex items-center">
                    <svg class="w-4 h-4 mr-2 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    Tu Nombre Completo
                  </label>
                  <input 
                    v-model="form.owner_name" 
                    type="text" 
                    required 
                    class="w-full px-4 py-4 rounded-xl border-2 border-slate-200 focus:border-slate-900 focus:ring-2 focus:ring-slate-900/10 transition-all outline-none bg-white text-slate-900 placeholder-slate-400 text-lg font-semibold" 
                    placeholder="Ej. Fabián Palacios"
                  >
                </div>
                
                <!-- Campo: Nombre del Negocio -->
                <div>
                  <label class="block text-sm font-bold text-slate-700 mb-2 flex items-center">
                    <svg class="w-4 h-4 mr-2 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                    Nombre de tu Negocio
                  </label>
                  <input 
                    v-model="form.company_name" 
                    type="text" 
                    required 
                    class="w-full px-4 py-4 rounded-xl border-2 border-slate-200 focus:border-slate-900 focus:ring-2 focus:ring-slate-900/10 transition-all outline-none bg-white text-slate-900 placeholder-slate-400 text-lg font-semibold" 
                    placeholder="Ej. Panadería El Buen Sabor" 
                    @input="generateSubdomain"
                  >
                </div>

                <!-- Campo: Subdominio (Cédula) -->
                <div>
                  <label class="block text-sm font-bold text-slate-700 mb-2 flex items-center">
                    <svg class="w-4 h-4 mr-2 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2" />
                    </svg>
                    Tu Cédula / ID
                  </label>
                  <input 
                    v-model="form.cedula" 
                    type="text" 
                    required 
                    class="w-full px-4 py-4 rounded-xl border-2 border-slate-200 focus:border-slate-900 focus:ring-2 focus:ring-slate-900/10 transition-all outline-none bg-white text-slate-900 placeholder-slate-400 text-lg font-semibold" 
                    placeholder="Ej. 1234567890"
                  >
                </div>

                <!-- Campo: Subdominio -->
                <div>
                  <label class="block text-sm font-bold text-slate-700 mb-2 flex items-center">
                    <svg class="w-4 h-4 mr-2 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9" />
                    </svg>
                    Dirección Web de tu Tienda
                  </label>
                  <div class="relative">
                    <input 
                      v-model="form.subdomain" 
                      type="text" 
                      required 
                      class="w-full pl-4 pr-52 py-4 rounded-xl border-2 border-slate-200 focus:border-slate-900 focus:ring-2 focus:ring-slate-900/10 transition-all outline-none bg-white text-slate-900 font-mono text-lg placeholder-slate-400 font-bold" 
                      placeholder="mi-tienda" 
                      @blur="checkAvailability"
                    >
                    <span class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-500 text-sm font-bold tracking-wide pointer-events-none select-none bg-slate-50 px-3 py-2 rounded-lg border border-slate-200">
                      .SISTEMAPOS.SPACE
                    </span>
                  </div>
                  
                  <!-- Estado de Disponibilidad -->
                  <div class="mt-3 min-h-[32px]">
                    <transition name="fade">
                      <div v-if="availabilityStatus === 'checking'" class="text-sm text-slate-600 flex items-center font-semibold">
                        <svg class="animate-spin w-4 h-4 mr-2 text-slate-900" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        Verificando disponibilidad...
                      </div>
                      <div v-else-if="availabilityStatus === 'available'" class="text-sm text-emerald-700 flex items-center font-bold bg-emerald-50 px-4 py-2.5 rounded-xl border border-emerald-200">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path></svg>
                        ¡Genial! Esta dirección está disponible
                      </div>
                      <div v-else-if="availabilityStatus === 'taken'" class="text-sm text-rose-700 flex items-center font-bold bg-rose-50 px-4 py-2.5 rounded-xl border border-rose-200">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path></svg>
                        Esta dirección ya está en uso
                      </div>
                    </transition>
                  </div>
                </div>
              </div>

              <!-- Botón Principal -->
              <div class="flex justify-end mt-6">
                <button 
                  type="submit" 
                  :disabled="availabilityStatus !== 'available' || !form.owner_name || !form.company_name || !form.cedula" 
                  class="px-8 py-4 bg-slate-900 hover:bg-black text-white font-bold rounded-xl shadow-lg shadow-slate-400/40 hover:shadow-slate-400/60 transition-all duration-300 disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:shadow-lg flex items-center group text-base active:scale-95"
                >
                  <span>Continuar</span>
                  <svg class="w-5 h-5 ml-2 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
                </button>
              </div>
            </form>
          </div>
        </div>

        <!-- Paso 2: Credenciales - CONVERSACIONAL CON NOMBRE -->
        <div v-if="step === 2" class="animate-fade-in px-8 py-10 md:px-12">
          <!-- Conversación Interactiva -->
          <div class="max-w-2xl mx-auto">
            <!-- Avatar del Sistema con Mensaje Personalizado -->
            <div class="flex items-start space-x-4 mb-8">
              <div class="w-12 h-12 bg-slate-900 rounded-2xl flex items-center justify-center flex-shrink-0">
                <img src="/logo.png" alt="105 POS" class="w-8 h-8 object-contain">
              </div>
              <div class="flex-1 bg-slate-50 rounded-2xl rounded-tl-sm p-5 border border-slate-100">
                <p class="text-slate-900 font-semibold text-lg leading-relaxed">
                  ¡Perfecto <span class="text-emerald-600">{{ form.owner_name || 'amigo' }}</span>! 🎉<br>
                  <span class="text-slate-600 font-medium text-base">Para terminar, agrega tu correo electrónico y crea una contraseña segura.</span>
                </p>
              </div>
            </div>

            <!-- Botón Volver Discreto -->
            <button @click="step = 1" type="button" class="text-sm text-slate-500 hover:text-slate-900 mb-6 flex items-center font-semibold transition-colors">
              <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"></path></svg>
              Volver atrás
            </button>

          <!-- Formulario Limpio -->
          <form @submit.prevent="registerTenant" class="space-y-5">
              <!-- Respuesta del Usuario (Visual) -->
              <div class="bg-white rounded-2xl border-2 border-slate-200 p-6 space-y-5">
                
                <!-- Campo: Email -->
                <div>
                  <label class="block text-sm font-bold text-slate-700 mb-2 flex items-center">
                    <svg class="w-4 h-4 mr-2 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                    Correo Electrónico
                  </label>
                  <input 
                    v-model="form.email" 
                    type="email" 
                    required 
                    class="w-full px-4 py-4 rounded-xl border-2 border-slate-200 focus:border-slate-900 focus:ring-2 focus:ring-slate-900/10 transition-all outline-none bg-white text-slate-900 placeholder-slate-400 text-lg font-semibold" 
                    placeholder="fabian@ejemplo.com"
                  >
                  <p class="text-xs text-slate-500 mt-2 font-medium flex items-center">
                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Lo usarás para iniciar sesión
                  </p>
                </div>

                <!-- Campo: Contraseña -->
                <div>
                  <label class="block text-sm font-bold text-slate-700 mb-2 flex items-center">
                    <svg class="w-4 h-4 mr-2 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                    Contraseña Segura
                  </label>
                  <input 
                    v-model="form.password" 
                    type="password" 
                    required 
                    minlength="8" 
                    class="w-full px-4 py-4 rounded-xl border-2 border-slate-200 focus:border-slate-900 focus:ring-2 focus:ring-slate-900/10 transition-all outline-none bg-white text-slate-900 placeholder-slate-400 text-lg font-semibold" 
                    placeholder="••••••••"
                  >
                  <p class="text-xs text-slate-500 mt-2 font-medium flex items-center">
                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                    Mínimo 8 caracteres para tu seguridad
                  </p>
                </div>
              </div>

              <!-- Botón Principal Grande -->
              <div class="flex justify-end mt-6">
                <button 
                  type="submit" 
                  :disabled="isSubmitting || !form.email || !form.password" 
                  class="px-10 py-4 bg-slate-900 hover:bg-black text-white font-bold rounded-xl shadow-lg shadow-slate-400/40 hover:shadow-slate-400/60 transition-all duration-300 disabled:opacity-70 disabled:cursor-not-allowed flex items-center text-lg active:scale-95 group"
                >
                  <svg v-if="isSubmitting" class="animate-spin -ml-1 mr-3 h-6 w-6 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                  </svg>
                  <svg v-else class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z" />
                  </svg>
                  <span v-if="!isSubmitting">Crear mi Tienda Ahora</span>
                  <span v-else>Creando tu tienda...</span>
                  <svg v-if="!isSubmitting" class="w-5 h-5 ml-2 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                  </svg>
                </button>
              </div>
            </form>
          </div>
        </div>

        <!-- Paso 3: Procesando / Éxito - ESTILO DASHBOARD -->
        <div v-if="step === 3" class="text-center animate-fade-in px-8 py-16">
          
          <!-- Estado de Éxito -->
          <div v-if="successData" class="animate-scale-in">
            <div class="w-24 h-24 bg-emerald-50 rounded-[24px] flex items-center justify-center mx-auto mb-8 border border-emerald-100">
              <svg class="w-14 h-14 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path>
              </svg>
            </div>
            
            <h2 class="text-4xl font-black text-slate-900 mb-4 tracking-tight">
              ¡Cuenta Creada!
            </h2>
            
            <p class="text-lg text-slate-600 mb-8 font-medium">
              Redirigiendo a tu panel de control...
            </p>

            <div class="flex items-center justify-center space-x-2">
              <div class="w-2 h-2 bg-emerald-500 rounded-full animate-bounce" style="animation-delay: 0s"></div>
              <div class="w-2 h-2 bg-emerald-500 rounded-full animate-bounce" style="animation-delay: 0.15s"></div>
              <div class="w-2 h-2 bg-emerald-500 rounded-full animate-bounce" style="animation-delay: 0.3s"></div>
            </div>
          </div>

          <!-- Estado de Carga -->
          <div v-else class="w-full">
            <!-- Spinner Profesional -->
            <div class="relative w-20 h-20 mx-auto mb-8">
              <div class="absolute inset-0 border-4 border-slate-200 rounded-full"></div>
              <div class="absolute inset-0 border-4 border-slate-900 rounded-full border-t-transparent animate-spin"></div>
            </div>
            
            <!-- Título Profesional -->
            <h2 class="text-4xl font-black text-slate-900 mb-6 tracking-tight">
              Configurando Sistema
            </h2>
            
            <!-- Mensajes Rotativos Profesionales -->
            <div class="h-16 overflow-hidden relative w-full max-w-xl mx-auto mb-8">
              <transition name="slide-up" mode="out-in">
                <p :key="currentMessageIndex" class="text-lg text-slate-600 font-semibold leading-relaxed">
                  {{ marketingMessages[currentMessageIndex] }}
                </p>
              </transition>
            </div>

            <!-- Puntos de Progreso Sutiles -->
            <div class="flex items-center justify-center space-x-2">
              <div class="w-2 h-2 bg-slate-900 rounded-full animate-bounce" style="animation-delay: 0s"></div>
              <div class="w-2 h-2 bg-slate-900 rounded-full animate-bounce" style="animation-delay: 0.15s"></div>
              <div class="w-2 h-2 bg-slate-900 rounded-full animate-bounce" style="animation-delay: 0.3s"></div>
            </div>

            <!-- Mensaje Discreto -->
            <p class="text-sm text-slate-500 mt-8 font-medium">
              Por favor espere unos momentos...
            </p>
          </div>

        </div>

      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted, onUnmounted } from 'vue'
import axios from 'axios'

// State
const step = ref(0)  // 0: Bienvenida | 1: Negocio | 2: Credenciales | 3: Procesando/Éxito
const isSubmitting = ref(false)
const successData = ref(null)
const availabilityStatus = ref(null) // null, 'checking', 'available', 'taken'
const checkTimeout = ref(null)

const marketingMessages = [
  "🏪 Configurando tu tienda virtual...",
  "📊 Inicializando módulos de facturación inteligente...",
  "📦 Preparando sistema de inventarios en tiempo real...",
  "💰 Configurando panel de reportes avanzados...",
  "🎯 Activando control de caja y ventas...",
  "⚡ Optimizando rendimiento del sistema..."
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
  token: '' // Token del link generado
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
  
  form.subdomain = slug
  checkAvailability()
}

const checkAvailability = async () => {
  if (!form.subdomain) {
    availabilityStatus.value = null
    return
  }

  // Debounce
  if (checkTimeout.value) clearTimeout(checkTimeout.value)
  
  availabilityStatus.value = 'checking'
  
  checkTimeout.value = setTimeout(async () => {
    try {
      const apiUrl = import.meta.env.VITE_API_URL ? `${import.meta.env.VITE_API_URL}/api` : '/api'
      const response = await axios.post(`${apiUrl}/check-domain`, { subdomain: form.subdomain })
      
      availabilityStatus.value = response.data.available ? 'available' : 'taken'
    } catch (error) {
      console.error('Error checking domain:', error)
      availabilityStatus.value = null // Reset on error or handle differently
    }
  }, 500)
}

const validateStep1 = () => {
  if (!form.owner_name || !form.company_name || !form.subdomain || !form.cedula) {
    toast.error('Por favor completa todos los campos')
    return
  }
  
  if (availabilityStatus.value !== 'available') {
    toast.error('El subdominio no está disponible')
    return
  }
  
  // Scroll al inicio para la conversación del paso 2
  window.scrollTo({ top: 0, behavior: 'smooth' })
  
  step.value = 2
}

const startMarketingMessages = () => {
  messageInterval = setInterval(() => {
    currentMessageIndex.value = (currentMessageIndex.value + 1) % marketingMessages.length
  }, 1500) // Cambiar mensaje cada 1.5 segundos
}

const registerTenant = async () => {
  isSubmitting.value = true
  step.value = 3 // Show processing screen
  startMarketingMessages()

  try {
    const apiUrl = import.meta.env.VITE_API_URL ? `${import.meta.env.VITE_API_URL}/api` : '/api'
    console.log('Enviando registro a:', `${apiUrl}/register-tenant`)
    console.log('Datos del formulario:', form)
    
    const response = await axios.post(`${apiUrl}/register-tenant`, form)
    
    console.log('Respuesta recibida:', response.data)

    if (response.data.success) {
      successData.value = response.data
      clearInterval(messageInterval)
      
      console.log('Redirigiendo a:', response.data.redirect_url)
      
      // Redirigir INMEDIATAMENTE sin esperar
      window.location.href = response.data.redirect_url
    } else {
      throw new Error('Respuesta sin success=true')
    }

  } catch (error) {
    console.error('Registration error:', error)
    console.error('Error details:', error.response?.data)
    
    step.value = 2 // Go back to step 2 on error
    isSubmitting.value = false
    clearInterval(messageInterval)
    
    let errorMessage = 'Ocurrió un error al crear la cuenta. Por favor intenta de nuevo.'
    
    if (error.response) {
      // Server responded with a status code out of 2xx range
      if (error.response.status === 422) {
        // Validation error
        const errors = error.response.data.errors
        if (errors) {
          // Get the first error message
          const firstError = Object.values(errors)[0]
          if (Array.isArray(firstError)) {
            errorMessage = firstError[0]
          } else {
            errorMessage = firstError
          }
        } else if (error.response.data.message) {
            errorMessage = error.response.data.message
        }
      } else if (error.response.data && error.response.data.message) {
        errorMessage = error.response.data.message
      }
    } else if (error.request) {
      // The request was made but no response was received
      errorMessage = 'No se pudo conectar con el servidor. Por favor verifica tu conexión.'
    }

    alert(errorMessage)
  }
}

// Detectar token de la URL al cargar
onMounted(() => {
  const urlParams = new URLSearchParams(window.location.search)
  const token = urlParams.get('token')
  if (token) {
    form.token = token
    console.log('Token detectado:', token)
  }
})

onUnmounted(() => {
  if (messageInterval) clearInterval(messageInterval)
})
</script>

<style scoped>
/* Animaciones Profesionales */
.animate-fade-in {
  animation: fadeIn 0.4s ease-out forwards;
}

@keyframes fadeIn {
  from { opacity: 0; transform: translateY(8px); }
  to { opacity: 1; transform: translateY(0); }
}

.animate-scale-in {
  animation: scaleIn 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275) forwards;
}

@keyframes scaleIn {
  from { opacity: 0; transform: scale(0.9); }
  to { opacity: 1; transform: scale(1); }
}

.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.3s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
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
</style>
