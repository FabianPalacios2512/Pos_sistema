<template>
  <div class="min-h-screen flex font-sans bg-white selection:bg-emerald-500/30">
    
    <!-- LEFT PANEL: Branding & Value Proposition (Fixed on Desktop) -->
    <div class="hidden lg:flex lg:w-5/12 bg-slate-900 relative overflow-hidden flex-col justify-between p-12 text-white">
      <!-- Background Effects -->
      <div class="absolute top-0 left-0 w-full h-full overflow-hidden z-0">
        <div class="absolute top-[-10%] right-[-10%] w-[500px] h-[500px] bg-emerald-500/20 rounded-full blur-[100px]"></div>
        <div class="absolute bottom-[-10%] left-[-10%] w-[500px] h-[500px] bg-blue-600/20 rounded-full blur-[100px]"></div>
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-full h-full bg-[url('/grid-pattern.svg')] opacity-[0.03]"></div>
      </div>

      <!-- Header -->
      <div class="relative z-10">
        <div class="flex items-center space-x-3 mb-8">
          <div class="w-10 h-10 bg-gradient-to-br from-emerald-400 to-emerald-600 rounded-xl flex items-center justify-center shadow-lg shadow-emerald-900/50">
             <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z" />
             </svg>
          </div>
          <span class="text-2xl font-bold tracking-tight">105 POS</span>
        </div>
        
        <h1 class="text-4xl xl:text-5xl font-bold leading-tight mb-6">
          Gestiona tu negocio <br>
          <span class="text-transparent bg-clip-text bg-gradient-to-r from-emerald-400 to-teal-300">al siguiente nivel.</span>
        </h1>
        
        <p class="text-lg text-slate-400 max-w-md leading-relaxed">
          Únete a miles de emprendedores que ya controlan su inventario y ventas de forma inteligente.
        </p>
      </div>

      <!-- Testimonial / Social Proof -->
      <div class="relative z-10 mt-auto">
        <div class="bg-slate-800/50 backdrop-blur-sm border border-slate-700/50 p-6 rounded-2xl mb-8">
          <div class="flex text-amber-400 mb-3">
            <svg v-for="i in 5" :key="i" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
          </div>
          <p class="text-slate-300 italic mb-4">"Increíble sistema. Pasé de usar cuadernos a tener todo automatizado en menos de 10 minutos. El soporte es excelente."</p>
          <div class="flex items-center">
            <div class="w-10 h-10 rounded-full bg-slate-700 flex items-center justify-center text-sm font-bold text-slate-300 mr-3">CR</div>
            <div>
              <p class="text-white font-semibold text-sm">Carlos Rodríguez</p>
              <p class="text-slate-500 text-xs">Dueño de Minimarket Express</p>
            </div>
          </div>
        </div>
        
        <div class="flex items-center space-x-6 text-sm text-slate-500 font-medium">
          <span>© 2024 105 POS</span>
          <a href="#" class="hover:text-emerald-400 transition-colors">Términos</a>
          <a href="#" class="hover:text-emerald-400 transition-colors">Privacidad</a>
        </div>
      </div>
    </div>

    <!-- RIGHT PANEL: Form Area -->
    <div class="w-full lg:w-7/12 flex flex-col relative bg-white">
      <!-- Mobile Header -->
      <div class="lg:hidden p-6 flex items-center justify-between border-b border-slate-100 bg-white sticky top-0 z-20">
        <div class="flex items-center space-x-2">
           <div class="w-8 h-8 bg-slate-900 rounded-lg flex items-center justify-center">
             <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z" />
             </svg>
          </div>
          <span class="font-bold text-slate-900 text-lg">105 POS</span>
        </div>
      </div>

      <!-- Main Scrollable Content -->
      <div class="flex-1 overflow-y-auto">
        <div class="min-h-full flex flex-col justify-center px-6 py-12 sm:px-12 lg:px-20 xl:px-32">
          
          <!-- Progress Stepper -->
          <div v-if="step > 0 && step < 3" class="mb-10">
             <div class="flex items-center justify-between relative">
                <div class="absolute left-0 top-1/2 -translate-y-1/2 w-full h-1 bg-slate-100 rounded-full -z-10"></div>
                <div class="absolute left-0 top-1/2 -translate-y-1/2 h-1 bg-emerald-500 rounded-full -z-10 transition-all duration-500" :style="{ width: step === 1 ? '50%' : '100%' }"></div>
                
                <!-- Step 1 Indicator -->
                <div class="flex flex-col items-center">
                   <div class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-bold border-2 transition-all duration-300"
                        :class="step >= 1 ? 'bg-emerald-500 border-emerald-500 text-white' : 'bg-white border-slate-300 text-slate-400'">
                      1
                   </div>
                   <span class="text-xs font-semibold mt-2" :class="step >= 1 ? 'text-emerald-600' : 'text-slate-400'">Negocio</span>
                </div>

                <!-- Step 2 Indicator -->
                <div class="flex flex-col items-center">
                   <div class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-bold border-2 transition-all duration-300"
                        :class="step >= 2 ? 'bg-emerald-500 border-emerald-500 text-white' : 'bg-white border-slate-300 text-slate-400'">
                      2
                   </div>
                   <span class="text-xs font-semibold mt-2" :class="step >= 2 ? 'text-emerald-600' : 'text-slate-400'">Cuenta</span>
                </div>
             </div>
          </div>

          <!-- STEP 0: Welcome / Landing -->
          <div v-if="step === 0" class="animate-fade-in">
            <div class="text-center lg:text-left mb-10">
              <span class="inline-block py-1 px-3 rounded-full bg-emerald-50 text-emerald-600 text-xs font-bold uppercase tracking-wider mb-4 border border-emerald-100">
                Comienza Gratis
              </span>
              <h2 class="text-3xl md:text-4xl font-bold text-slate-900 mb-4 tracking-tight">
                Crea tu cuenta profesional
              </h2>
              <p class="text-lg text-slate-500">
                Configura tu punto de venta en segundos. Sin tarjeta de crédito requerida.
              </p>
            </div>

            <div class="grid gap-4 mb-10">
              <div class="flex items-start p-4 rounded-xl bg-slate-50 border border-slate-100 hover:border-emerald-200 transition-colors group">
                <div class="w-10 h-10 rounded-lg bg-white flex items-center justify-center shadow-sm text-emerald-600 mr-4 group-hover:scale-110 transition-transform">
                  <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                </div>
                <div>
                  <h3 class="font-bold text-slate-900">Todo en uno</h3>
                  <p class="text-sm text-slate-500">Facturación, inventario y reportes en una sola plataforma.</p>
                </div>
              </div>
              <div class="flex items-start p-4 rounded-xl bg-slate-50 border border-slate-100 hover:border-blue-200 transition-colors group">
                <div class="w-10 h-10 rounded-lg bg-white flex items-center justify-center shadow-sm text-blue-600 mr-4 group-hover:scale-110 transition-transform">
                  <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                </div>
                <div>
                  <h3 class="font-bold text-slate-900">Seguro y Confiable</h3>
                  <p class="text-sm text-slate-500">Tus datos están encriptados y respaldados diariamente.</p>
                </div>
              </div>
            </div>

            <button 
              @click="step = 1" 
              class="w-full py-4 bg-slate-900 hover:bg-black text-white font-bold rounded-xl shadow-lg shadow-slate-900/20 hover:shadow-slate-900/40 transition-all duration-300 flex items-center justify-center text-lg group"
            >
              <span>Comenzar Ahora</span>
              <svg class="w-5 h-5 ml-2 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
            </button>
            
            <p class="text-center mt-6 text-sm text-slate-500">
              ¿Ya tienes cuenta? <a href="/login" class="text-emerald-600 font-bold hover:underline">Inicia Sesión</a>
            </p>
          </div>

          <!-- STEP 1: Business Info -->
          <div v-if="step === 1" class="animate-fade-in">
            <div class="mb-8">
              <h2 class="text-2xl font-bold text-slate-900 mb-2">Sobre tu Negocio</h2>
              <p class="text-slate-500">Cuéntanos un poco para personalizar tu experiencia.</p>
            </div>

            <form @submit.prevent="validateStep1" class="space-y-6">
              <!-- Name -->
              <div class="space-y-1.5">
                <label class="text-sm font-bold text-slate-700">Tu Nombre Completo</label>
                <input 
                  v-model="form.owner_name" 
                  type="text" 
                  required 
                  class="w-full px-4 py-3.5 rounded-xl border border-slate-200 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 transition-all outline-none bg-slate-50 focus:bg-white text-slate-900 font-medium placeholder-slate-400" 
                  placeholder="Ej. Juan Pérez"
                >
              </div>

              <!-- Company Name -->
              <div class="space-y-1.5">
                <label class="text-sm font-bold text-slate-700">Nombre del Negocio</label>
                <input 
                  v-model="form.company_name" 
                  type="text" 
                  required 
                  class="w-full px-4 py-3.5 rounded-xl border border-slate-200 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 transition-all outline-none bg-slate-50 focus:bg-white text-slate-900 font-medium placeholder-slate-400" 
                  placeholder="Ej. Cafetería Central" 
                  @input="generateSubdomain"
                >
              </div>

              <!-- ID / Cedula -->
              <div class="space-y-1.5">
                <label class="text-sm font-bold text-slate-700">Cédula / NIT</label>
                <input 
                  v-model="form.cedula" 
                  type="text" 
                  required 
                  class="w-full px-4 py-3.5 rounded-xl border border-slate-200 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 transition-all outline-none bg-slate-50 focus:bg-white text-slate-900 font-medium placeholder-slate-400" 
                  placeholder="Ej. 123456789"
                >
              </div>

              <!-- Subdomain -->
              <div class="space-y-1.5">
                <label class="text-sm font-bold text-slate-700 flex justify-between">
                  <span>Dirección Web</span>
                  <span class="text-xs font-normal text-slate-400">Tu enlace único</span>
                </label>
                <div class="relative group">
                  <input 
                    v-model="form.subdomain" 
                    type="text" 
                    required 
                    class="w-full pl-4 pr-48 py-3.5 rounded-xl border border-slate-200 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 transition-all outline-none bg-slate-50 focus:bg-white text-slate-900 font-mono font-medium placeholder-slate-400" 
                    placeholder="mi-negocio" 
                    @blur="checkAvailability"
                  >
                  <div class="absolute right-0 top-0 h-full flex items-center pr-4 pointer-events-none">
                    <span class="text-slate-400 font-medium bg-slate-100 px-2 py-1 rounded text-sm group-focus-within:text-emerald-600 group-focus-within:bg-emerald-50 transition-colors">.sistemapos.space</span>
                  </div>
                </div>
                
                <!-- Availability Status -->
                <div class="h-6 mt-1">
                   <transition name="fade">
                      <p v-if="availabilityStatus === 'checking'" class="text-xs text-slate-500 flex items-center">
                         <svg class="animate-spin w-3 h-3 mr-1.5" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                         Verificando...
                      </p>
                      <p v-else-if="availabilityStatus === 'available'" class="text-xs text-emerald-600 flex items-center font-bold">
                         <svg class="w-3 h-3 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                         Disponible
                      </p>
                      <p v-else-if="availabilityStatus === 'taken'" class="text-xs text-rose-600 flex items-center font-bold">
                         <svg class="w-3 h-3 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                         No disponible
                      </p>
                   </transition>
                </div>
              </div>

              <div class="pt-4 flex items-center space-x-4">
                 <button type="button" @click="step = 0" class="px-6 py-3.5 text-slate-500 font-bold hover:bg-slate-50 rounded-xl transition-colors">
                    Atrás
                 </button>
                 <button 
                   type="submit" 
                   :disabled="availabilityStatus !== 'available' || !form.owner_name || !form.company_name || !form.cedula" 
                   class="flex-1 py-3.5 bg-slate-900 hover:bg-black text-white font-bold rounded-xl shadow-lg shadow-slate-900/20 hover:shadow-slate-900/40 transition-all duration-300 disabled:opacity-50 disabled:cursor-not-allowed"
                 >
                   Continuar
                 </button>
              </div>
            </form>
          </div>

          <!-- STEP 2: Credentials -->
          <div v-if="step === 2" class="animate-fade-in">
             <div class="mb-8">
              <h2 class="text-2xl font-bold text-slate-900 mb-2">Crea tu Acceso</h2>
              <p class="text-slate-500">Protege tu cuenta con una contraseña segura.</p>
            </div>

            <form @submit.prevent="registerTenant" class="space-y-6">
               <!-- Email -->
               <div class="space-y-1.5">
                <label class="text-sm font-bold text-slate-700">Correo Electrónico</label>
                <input 
                  v-model="form.email" 
                  type="email" 
                  required 
                  class="w-full px-4 py-3.5 rounded-xl border border-slate-200 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 transition-all outline-none bg-slate-50 focus:bg-white text-slate-900 font-medium placeholder-slate-400" 
                  placeholder="nombre@empresa.com"
                >
              </div>

              <!-- Password -->
              <div class="space-y-1.5">
                <label class="text-sm font-bold text-slate-700">Contraseña</label>
                <input 
                  v-model="form.password" 
                  type="password" 
                  required 
                  minlength="8"
                  class="w-full px-4 py-3.5 rounded-xl border border-slate-200 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 transition-all outline-none bg-slate-50 focus:bg-white text-slate-900 font-medium placeholder-slate-400" 
                  placeholder="••••••••"
                >
                <p class="text-xs text-slate-400 mt-1">Mínimo 8 caracteres</p>
              </div>

              <div class="pt-4 flex items-center space-x-4">
                 <button type="button" @click="step = 1" class="px-6 py-3.5 text-slate-500 font-bold hover:bg-slate-50 rounded-xl transition-colors">
                    Atrás
                 </button>
                 <button 
                   type="submit" 
                   :disabled="isSubmitting || !form.email || !form.password" 
                   class="flex-1 py-3.5 bg-slate-900 hover:bg-black text-white font-bold rounded-xl shadow-lg shadow-slate-900/20 hover:shadow-slate-900/40 transition-all duration-300 disabled:opacity-70 disabled:cursor-not-allowed flex items-center justify-center"
                 >
                   <svg v-if="isSubmitting" class="animate-spin -ml-1 mr-2 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                      <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                      <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                   </svg>
                   <span v-if="!isSubmitting">Crear Cuenta</span>
                   <span v-else>Procesando...</span>
                 </button>
              </div>
            </form>
          </div>

          <!-- STEP 3: Success / Loading -->
          <div v-if="step === 3" class="animate-fade-in text-center py-10">
             
             <div v-if="successData" class="animate-scale-in">
                <div class="w-20 h-20 bg-emerald-100 rounded-full flex items-center justify-center mx-auto mb-6 text-emerald-600">
                   <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                </div>
                <h2 class="text-3xl font-bold text-slate-900 mb-2">¡Todo listo!</h2>
                <p class="text-slate-500 mb-8">Tu tienda ha sido creada exitosamente.</p>
                
                <div class="inline-flex items-center px-4 py-2 bg-emerald-50 text-emerald-700 rounded-lg text-sm font-medium animate-pulse">
                   Redirigiendo a tu panel...
                </div>
             </div>

             <div v-else>
                <div class="relative w-20 h-20 mx-auto mb-8">
                   <div class="absolute inset-0 border-4 border-slate-100 rounded-full"></div>
                   <div class="absolute inset-0 border-4 border-slate-900 rounded-full border-t-transparent animate-spin"></div>
                </div>
                <h2 class="text-2xl font-bold text-slate-900 mb-4">Configurando tu tienda</h2>
                
                <div class="h-12 overflow-hidden relative">
                   <transition name="slide-up" mode="out-in">
                      <p :key="currentMessageIndex" class="text-slate-500 font-medium">
                         {{ marketingMessages[currentMessageIndex] }}
                      </p>
                   </transition>
                </div>
             </div>
          </div>

        </div>
      </div>
      
      <!-- Mobile Footer -->
      <div class="lg:hidden p-6 text-center text-xs text-slate-400 border-t border-slate-100">
         © 2024 105 POS. Todos los derechos reservados.
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
    .substring(0, 20) // Limit length
  
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
      availabilityStatus.value = null 
    }
  }, 500)
}

const validateStep1 = () => {
  if (!form.owner_name || !form.company_name || !form.subdomain || !form.cedula) {
    alert('Por favor completa todos los campos')
    return
  }
  
  if (availabilityStatus.value !== 'available') {
    alert('El subdominio no está disponible')
    return
  }
  
  step.value = 2
}

const startMarketingMessages = () => {
  messageInterval = setInterval(() => {
    currentMessageIndex.value = (currentMessageIndex.value + 1) % marketingMessages.length
  }, 2000)
}

const registerTenant = async () => {
  isSubmitting.value = true
  step.value = 3 // Show processing screen
  startMarketingMessages()

  try {
    const apiUrl = import.meta.env.VITE_API_URL ? `${import.meta.env.VITE_API_URL}/api` : '/api'
    
    console.log('📤 Enviando petición de registro a:', `${apiUrl}/register-tenant`)
    console.log('📦 Datos del formulario:', form)
    
    const response = await axios.post(`${apiUrl}/register-tenant`, form)
    
    console.log('📥 Respuesta completa recibida:', response)
    console.log('📥 response.data:', response.data)
    console.log('📥 response.data.success:', response.data.success)
    
    if (response.data.success) {
      successData.value = response.data
      clearInterval(messageInterval)
      
      // ✅ GUARDAR DATOS DEL REGISTRO EN LOCALSTORAGE
      const registrationData = {
        company_name: form.company_name,
        storeName: form.company_name,
        owner_name: form.owner_name,
        phone: form.phone || '',
        email: form.email,
        subdomain: form.subdomain,
        cedula: form.cedula
      }
      localStorage.setItem('registration_data', JSON.stringify(registrationData))
      
      setTimeout(() => {
         window.location.href = response.data.redirect_url
      }, 1500)
    } else {
      throw new Error('Respuesta sin success=true')
    }

  } catch (error) {
    console.error('Registration error:', error)
    
    step.value = 2 // Go back to step 2 on error
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
onMounted(() => {
  const urlParams = new URLSearchParams(window.location.search)
  const token = urlParams.get('token')
  if (token) {
    form.token = token
  }
})

onUnmounted(() => {
  if (messageInterval) clearInterval(messageInterval)
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
</style>
