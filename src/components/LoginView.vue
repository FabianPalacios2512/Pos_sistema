<template>
  <!-- 🎨 PREMIUM SPLIT SCREEN - Diseño SaaS Empresarial 2025 -->
  <div class="min-h-screen flex bg-slate-50">
    
    <!-- 📸 LADO IZQUIERDO: Panel de Marca Premium -->
    <div class="hidden lg:flex lg:w-1/2 xl:w-[55%] relative overflow-hidden">
      <!-- Gradiente base oscuro elegante -->
      <div class="absolute inset-0 bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900"></div>
      
      <!-- Imagen con overlay profesional -->
      <div class="absolute inset-0">
        <img 
          src="/login.png" 
          alt="105 POS Pro - Sistema de Punto de Venta" 
          class="w-full h-full object-cover transition-opacity duration-1000 ease-out"
          :class="imageLoaded ? 'opacity-100' : 'opacity-0'"
          @load="imageLoaded = true"
          loading="eager"
          decoding="async"
        />
        <!-- Overlay con gradiente premium -->
        <div class="absolute inset-0 bg-gradient-to-t from-slate-900 via-slate-900/60 to-slate-900/30"></div>
        <div class="absolute inset-0 bg-gradient-to-r from-slate-900/80 via-transparent to-transparent"></div>
      </div>
      
      <!-- Skeleton mientras carga -->
      <div 
        v-if="!imageLoaded" 
        class="absolute inset-0 bg-gradient-to-br from-slate-800 to-slate-900"
      >
        <div class="absolute inset-0 animate-pulse bg-gradient-to-r from-transparent via-slate-700/20 to-transparent"></div>
      </div>

      <!-- Contenido superpuesto -->
      <div class="relative z-10 flex flex-col justify-between w-full h-full p-10 xl:p-14">
        
        <!-- Logo y marca superior -->
        <div class="flex items-center gap-3">
          <div class="w-12 h-12 rounded-2xl bg-white/10 backdrop-blur-md border border-white/20 flex items-center justify-center">
            <svg class="w-7 h-7 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
            </svg>
          </div>
          <span class="text-white/90 font-bold text-xl tracking-tight">105 POS</span>
        </div>

        <!-- Contenido central con estadísticas -->
        <div class="flex-1 flex flex-col justify-center max-w-lg">
          <transition
            enter-active-class="transition ease-out duration-700 delay-200"
            enter-from-class="translate-y-8 opacity-0"
            enter-to-class="translate-y-0 opacity-100"
          >
            <div v-if="imageLoaded" class="space-y-8">
              <div>
                <h2 class="text-4xl xl:text-5xl font-bold text-white leading-tight">
                  Potencia tu negocio con
                  <span class="text-transparent bg-clip-text bg-gradient-to-r from-emerald-400 to-teal-300">
                    tecnología inteligente
                  </span>
                </h2>
                <p class="mt-4 text-lg text-slate-300 leading-relaxed">
                  El sistema POS más completo para empresas que buscan crecer. 
                  Controla ventas, inventario y clientes desde cualquier lugar.
                </p>
              </div>

              <!-- Stats Grid -->
              <div class="grid grid-cols-3 gap-4">
                <div class="bg-white/5 backdrop-blur-sm rounded-2xl p-4 border border-white/10">
                  <div class="text-3xl font-bold text-white">99.9%</div>
                  <div class="text-sm text-slate-400 mt-1">Uptime garantizado</div>
                </div>
                <div class="bg-white/5 backdrop-blur-sm rounded-2xl p-4 border border-white/10">
                  <div class="text-3xl font-bold text-white">+2K</div>
                  <div class="text-sm text-slate-400 mt-1">Negocios activos</div>
                </div>
                <div class="bg-white/5 backdrop-blur-sm rounded-2xl p-4 border border-white/10">
                  <div class="text-3xl font-bold text-white">24/7</div>
                  <div class="text-sm text-slate-400 mt-1">Soporte técnico</div>
                </div>
              </div>
            </div>
          </transition>
        </div>

        <!-- Testimonial Card inferior -->
        <transition
          enter-active-class="transition ease-out duration-700 delay-500"
          enter-from-class="translate-y-6 opacity-0"
          enter-to-class="translate-y-0 opacity-100"
        >
          <div v-if="imageLoaded" class="bg-white/10 backdrop-blur-xl rounded-2xl p-6 border border-white/20">
            <div class="flex items-start gap-4">
              <div class="w-12 h-12 rounded-full bg-gradient-to-br from-emerald-400 to-teal-500 flex items-center justify-center flex-shrink-0">
                <span class="text-white font-bold text-lg">CP</span>
              </div>
              <div class="flex-1">
                <div class="flex items-center gap-2 mb-2">
                  <div class="flex gap-0.5">
                    <svg v-for="i in 5" :key="i" class="w-4 h-4 text-amber-400" fill="currentColor" viewBox="0 0 20 20">
                      <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                    </svg>
                  </div>
                </div>
                <p class="text-white/90 text-sm leading-relaxed italic">
                  "Desde que implementamos 105 POS, nuestras ventas aumentaron un 40% y el control de inventario es impecable."
                </p>
                <p class="text-slate-400 text-sm mt-3 font-medium">Carlos Pérez — CEO, Supermercados del Valle</p>
              </div>
            </div>
          </div>
        </transition>
      </div>
    </div>

    <!-- 📝 LADO DERECHO: Formulario de Login Premium -->
    <div class="flex-1 flex items-center justify-center px-6 sm:px-8 lg:px-12 xl:px-16 bg-white relative">
      <!-- Patrón de fondo sutil -->
      <div class="absolute inset-0 bg-[radial-gradient(#e5e7eb_1px,transparent_1px)] [background-size:20px_20px] opacity-50"></div>
      
      <div class="w-full max-w-[420px] relative z-10">
        
        <!-- Badge versión móvil -->
        <div class="lg:hidden flex items-center gap-2.5 mb-10">
          <div class="w-11 h-11 rounded-xl bg-gradient-to-br from-slate-800 to-slate-900 flex items-center justify-center shadow-lg shadow-slate-900/20">
            <svg class="w-6 h-6 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
            </svg>
          </div>
          <span class="text-slate-900 font-bold text-xl tracking-tight">105 POS Pro</span>
        </div>

        <!-- Header del formulario -->
        <div class="mb-8">
          <div class="hidden lg:block">
            <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-emerald-50 text-emerald-700 text-xs font-semibold mb-4 border border-emerald-100">
              <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
              Plataforma activa
            </span>
          </div>
          <h1 class="text-3xl font-bold text-slate-900 tracking-tight">
            Bienvenido de nuevo
          </h1>
          <p class="mt-2 text-slate-500">
            Ingresa tus credenciales para acceder al sistema
          </p>
        </div>

        <!-- Mensajes de Error/Éxito mejorados -->
        <transition
          enter-active-class="transition ease-out duration-300"
          enter-from-class="-translate-y-2 opacity-0"
          enter-to-class="translate-y-0 opacity-100"
          leave-active-class="transition ease-in duration-200"
          leave-from-class="translate-y-0 opacity-100"
          leave-to-class="-translate-y-2 opacity-0"
        >
          <div v-if="message.text" 
               :class="[
                 'mb-6 px-4 py-3.5 rounded-xl text-sm font-medium flex items-center gap-3',
                 message.type === 'error' 
                   ? 'bg-red-50 text-red-700 border border-red-100' 
                   : message.type === 'info'
                     ? 'bg-blue-50 text-blue-700 border border-blue-100'
                     : 'bg-emerald-50 text-emerald-700 border border-emerald-100'
               ]">
            <svg v-if="message.type === 'error'" class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <svg v-else-if="message.type === 'info'" class="w-5 h-5 flex-shrink-0 animate-spin" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <svg v-else class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span>{{ message.text }}</span>
          </div>
        </transition>

        <form @submit.prevent="handleLogin" class="space-y-5">
          
          <!-- Campo Email -->
          <div class="space-y-2">
            <label for="email" class="block text-sm font-semibold text-slate-700">
              Correo Electrónico
            </label>
            <div class="relative">
              <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
              </div>
              <input
                id="email"
                v-model="credentials.email"
                type="email"
                autocomplete="email"
                required
                placeholder="tu@email.com"
                class="block w-full pl-12 pr-4 py-3.5 bg-slate-50 border-2 border-slate-200 rounded-xl text-slate-900 placeholder-slate-400 focus:outline-none focus:bg-white focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 transition-all duration-200"
                :class="{ 'border-red-300 bg-red-50 focus:border-red-500 focus:ring-red-500/10': errors.email }"
              />
            </div>
            <p v-if="errors.email" class="text-sm text-red-600 flex items-center gap-1.5">
              <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
              </svg>
              {{ errors.email }}
            </p>
          </div>

          <!-- Campo Contraseña -->
          <div class="space-y-2">
            <div class="flex items-center justify-between">
              <label for="password" class="block text-sm font-semibold text-slate-700">
                Contraseña
              </label>
              <router-link 
                to="/forgot-password" 
                class="text-sm font-medium text-emerald-600 hover:text-emerald-700 transition-colors"
              >
                ¿Olvidaste tu contraseña?
              </router-link>
            </div>
            <div class="relative">
              <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                </svg>
              </div>
              <input
                id="password"
                v-model="credentials.password"
                :type="showPassword ? 'text' : 'password'"
                autocomplete="current-password"
                required
                placeholder="••••••••"
                class="block w-full pl-12 pr-12 py-3.5 bg-slate-50 border-2 border-slate-200 rounded-xl text-slate-900 placeholder-slate-400 focus:outline-none focus:bg-white focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 transition-all duration-200"
                :class="{ 'border-red-300 bg-red-50 focus:border-red-500 focus:ring-red-500/10': errors.password }"
              />
              <button
                type="button"
                @click="showPassword = !showPassword"
                class="absolute inset-y-0 right-0 pr-4 flex items-center text-slate-400 hover:text-slate-600 transition-colors"
              >
                <svg v-if="showPassword" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                </svg>
                <svg v-else class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21"/>
                </svg>
              </button>
            </div>
            <p v-if="errors.password" class="text-sm text-red-600 flex items-center gap-1.5">
              <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
              </svg>
              {{ errors.password }}
            </p>
          </div>

          <!-- Botón Principal Premium -->
          <div class="pt-2">
            <button
              type="submit"
              :disabled="loading"
              class="group w-full flex items-center justify-center gap-2 py-4 px-6 text-base font-semibold text-white bg-gradient-to-r from-slate-800 to-slate-900 hover:from-slate-900 hover:to-black rounded-xl shadow-lg shadow-slate-900/25 focus:outline-none focus:ring-4 focus:ring-slate-900/20 disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-300"
            >
              <svg v-if="loading" class="animate-spin -ml-1 mr-2 h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              <span>{{ loading ? 'Verificando...' : 'Iniciar Sesión' }}</span>
              <svg v-if="!loading" class="w-5 h-5 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
              </svg>
            </button>
          </div>
        </form>

        <!-- Separador Premium -->
        <div class="relative my-8">
          <div class="absolute inset-0 flex items-center">
            <div class="w-full border-t border-slate-200"></div>
          </div>
          <div class="relative flex justify-center">
            <span class="px-4 text-sm text-slate-400 bg-white">o continúa con</span>
          </div>
        </div>

        <!-- Botón Google Premium -->
        <button
          type="button"
          @click="loginWithGoogle"
          :disabled="isGoogleLoading"
          class="w-full flex items-center justify-center gap-3 px-4 py-3.5 bg-white border-2 border-slate-200 rounded-xl text-sm font-semibold text-slate-700 hover:bg-slate-50 hover:border-slate-300 focus:outline-none focus:ring-4 focus:ring-slate-100 transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed"
        >
          <svg v-if="!isGoogleLoading" class="w-5 h-5" viewBox="0 0 24 24">
            <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
            <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
            <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
            <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
          </svg>
          <svg v-else class="w-5 h-5 animate-spin text-slate-400" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
          </svg>
          <span>{{ isGoogleLoading ? 'Conectando...' : 'Continuar con Google' }}</span>
        </button>

        <!-- Link de Registro Premium -->
        <div class="mt-8 text-center">
          <p class="text-sm text-slate-500">
            ¿Nuevo en 105 POS? 
            <a 
              href="https://105pos.pro/register" 
              class="font-semibold text-emerald-600 hover:text-emerald-700 transition-colors hover:underline underline-offset-2"
            >
              Crea tu cuenta gratis
            </a>
          </p>
        </div>

        <!-- Footer con features -->
        <div class="mt-10 pt-8 border-t border-slate-100">
          <div class="flex items-center justify-center gap-6 text-xs text-slate-400">
            <div class="flex items-center gap-1.5">
              <svg class="w-4 h-4 text-emerald-500" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
              </svg>
              <span>Conexión segura</span>
            </div>
            <div class="flex items-center gap-1.5">
              <svg class="w-4 h-4 text-emerald-500" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/>
              </svg>
              <span>Datos encriptados</span>
            </div>
          </div>
          <p class="text-center text-xs text-slate-400 mt-4">© 2025 105 POS Pro. Todos los derechos reservados.</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import axios from 'axios'
import authService from '../services/authService.js'
import googleAuthService from '../services/googleAuthService.js'

// Router
const router = useRouter()

// Estado reactivo
const loading = ref(false)
const showPassword = ref(false)
const isGoogleLoading = ref(false)
const imageLoaded = ref(false) // 🖼️ Estado de carga de imagen

// Credenciales del formulario
const credentials = reactive({
  email: '',
  password: '',
  remember: false
})

// Mensajes y errores
const message = reactive({
  text: '',
  type: ''
})

const errors = reactive({
  email: '',
  password: ''
})

// Login con Google
const loginWithGoogle = async () => {
  try {
    isGoogleLoading.value = true
    
    // Generar URL de Google OAuth para login
    const authUrl = await googleAuthService.initiateGoogleAuth({
      mode: 'login' // Indicar que es login, no registro
    })
    
    // Redirigir a Google
    window.location.href = authUrl
    
  } catch (error) {
    console.error('Error iniciando Google Auth:', error)
    message.text = 'No se pudo conectar con Google. Intenta nuevamente.'
    message.type = 'error'
    isGoogleLoading.value = false
  }
}

// Verificar mensaje de registro exitoso
onMounted(async () => {
  // 🎯 AUTO-LOGIN desde dominio central
  const autoLoginCreds = sessionStorage.getItem('auto_login_credentials')
  if (autoLoginCreds) {
    try {
      loading.value = true
      message.text = 'Iniciando sesión automáticamente...'
      message.type = 'info'
      
      const creds = JSON.parse(autoLoginCreds)
      
      // Limpiar credenciales temporales
      sessionStorage.removeItem('auto_login_credentials')
      
      // 🔐 LOGIN DIRECTO EN EL TENANT (sin redirección)
      const response = await authService.login({
        email: creds.email,
        password: creds.password
      })

      console.log('✅ Auto-login exitoso')
      message.text = '✅ Inicio de sesión exitoso. Redirigiendo...'
      message.type = 'success'

      // Verificar rol y redireccionar al POS
      const user = response.data?.user || response.user
      
      setTimeout(() => {
        if (user?.is_super_admin || user?.role?.name === 'superadmin') {
          router.push('/admin/god-mode')
        } else {
          router.push('/pos')
        }
      }, 500)
      
      return
    } catch (error) {
      console.error('Error en auto-login:', error)
      sessionStorage.removeItem('auto_login_credentials')
      message.text = 'Error al iniciar sesión. Por favor, intenta manualmente.'
      message.type = 'error'
      loading.value = false
    }
  }
  
  const registrationSuccess = localStorage.getItem('registration_success')
  if (registrationSuccess) {
    const data = JSON.parse(registrationSuccess)
    message.text = `🎉 ${data.message}`
    message.type = 'success'
    
    // Limpiar el mensaje después de mostrarlo
    localStorage.removeItem('registration_success')
    
    // Limpiar el mensaje después de 8 segundos
    setTimeout(() => {
      message.text = ''
      message.type = ''
    }, 8000)
  }

  // 🎯 Verificar si hay un token de login con Google en la URL
  const urlParams = new URLSearchParams(window.location.search)
  const googleLoginToken = urlParams.get('google_login_token')

  if (googleLoginToken) {
    console.log('🔑 Token de Google detectado en URL, procesando autenticación...')
    
    try {
      loading.value = true
      message.text = 'Iniciando sesión con Google...'
      message.type = 'info'
      
      // 🔥 Crear instancia de axios sin interceptores para evitar redirección
      const cleanAxios = axios.create({
        baseURL: window.location.origin,
        headers: {
          'Content-Type': 'application/json'
        }
      })
      
      console.log('📡 Obteniendo datos de sesión de Google desde:', `${window.location.origin}/api/auth/google/login-session?token=${googleLoginToken}`)
      
      // Obtener sesión usando el token
      const response = await cleanAxios.get(`/api/auth/google/login-session?token=${googleLoginToken}`)
      
      console.log('📦 Respuesta del servidor:', response.data)
      
      if (response.data.success && response.data.data) {
        const { token, user } = response.data.data
        
        console.log('✅ Datos de usuario recibidos:', user)
        console.log('✅ Rol del usuario:', user.role)
        console.log('✅ Permisos del rol:', user.role?.permissions)
        console.log('✅ Token Sanctum recibido:', token.substring(0, 20) + '...')
        
        // 🔥 IMPORTANTE: Guardar token Sanctum real y datos de usuario
        localStorage.setItem('authToken', token)
        localStorage.setItem('user', JSON.stringify(user))
        
        // Configurar token en axios global
        axios.defaults.headers.common['Authorization'] = `Bearer ${token}`
        
        console.log('✅ Token Sanctum guardado en localStorage y axios configurado')
        console.log('✅ Usuario autenticado con Google:', user.name, '(', user.email, ')')
        console.log('✅ Usuario tiene', user.role?.permissions?.length || 0, 'permisos')
        
        message.text = '✅ Inicio de sesión exitoso. Redirigiendo...'
        message.type = 'success'
        
        // Pequeño delay para que el usuario vea el mensaje de éxito
        setTimeout(() => {
          console.log('🔄 Redirigiendo a /pos')
          window.location.href = '/pos'
        }, 500)
      } else {
        console.error('❌ Error en respuesta del servidor:', response.data.message)
        message.text = response.data.message || 'Error al iniciar sesión con Google.'
        message.type = 'error'
        
        // Limpiar URL
        window.history.replaceState({}, document.title, '/login')
      }
    } catch (error) {
      console.error('❌ Error obteniendo sesión de Google:', error)
      console.error('Detalles del error:', {
        message: error.message,
        response: error.response?.data,
        status: error.response?.status
      })
      
      message.text = error.response?.data?.message || 'Error al iniciar sesión con Google. Intenta nuevamente.'
      message.type = 'error'
      
      // Limpiar URL
      window.history.replaceState({}, document.title, '/login')
    } finally {
      loading.value = false
    }
  }
})

// Ambiente de desarrollo
const isDevelopment = computed(() => {
  return process.env.NODE_ENV === 'development' || window.location.hostname === 'localhost'
})

// Limpiar mensajes
const clearMessages = () => {
  message.text = ''
  message.type = ''
  errors.email = ''
  errors.password = ''
}

// Validar formulario
const validateForm = () => {
  clearMessages()
  let isValid = true

  if (!credentials.email.trim()) {
    errors.email = 'El correo es requerido'
    isValid = false
  } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(credentials.email.trim())) {
    errors.email = 'Ingrese un correo válido'
    isValid = false
  }

  if (!credentials.password.trim()) {
    errors.password = 'La contraseña es requerida'
    isValid = false
  } else if (credentials.password.length < 6) {
    errors.password = 'La contraseña debe tener al menos 6 caracteres'
    isValid = false
  }

  return isValid
}

// Manejar login
const handleLogin = async () => {
  if (!validateForm()) return

  loading.value = true
  clearMessages()

  try {
    // 🎯 DETECTAR SI ESTAMOS EN DOMINIO PRINCIPAL (login centralizado)
    const hostname = window.location.hostname
    const isMainDomain = ['localhost', '127.0.0.1', '105pos.pro', 'www.105pos.pro'].includes(hostname)
    
    if (isMainDomain) {
      // ============================================
      // 🚀 LOGIN CENTRALIZADO (Smart Login)
      // ============================================
      console.log('🎯 Login centralizado detectado en dominio principal')
      
      const response = await axios.post('/api/central/login', {
        email: credentials.email.trim(),
        password: credentials.password
      })

      if (response.data.success) {
        message.text = '✅ ' + response.data.message
        message.type = 'success'
        
        console.log('✅ Tenant encontrado:', response.data.data.tenant_domain)
        console.log('🔄 Redirigiendo a:', response.data.data.redirect_url)
        
        // Guardar credenciales temporalmente para auto-login en el tenant
        sessionStorage.setItem('auto_login_credentials', JSON.stringify({
          email: response.data.data.credentials.email,
          password: response.data.data.credentials.password
        }))
        
        // Esperar un momento y redirigir al tenant
        setTimeout(() => {
          window.location.href = response.data.data.redirect_url + '/login'
        }, 1000)
        
        return
      }
    }
    
    // ============================================
    // 🔐 LOGIN TRADICIONAL (en subdominio)
    // ============================================
    const response = await authService.login({
      email: credentials.email.trim(),
      password: credentials.password
    })

    message.text = 'Inicio de sesión exitoso'
    message.type = 'success'

    // Aplicar configuración pendiente del onboarding si existe
    const pendingConfig = localStorage.getItem('pending_onboarding_config')
    if (pendingConfig) {
      try {
        const config = JSON.parse(pendingConfig)
        // ✅ IMPORTANTE: Agregar onboarding_completed al guardar config pendiente
        config.onboarding_completed = true
        await axios.put('/api/tenant/system-settings', config)
        localStorage.removeItem('pending_onboarding_config')
        console.log('✅ Configuración de onboarding aplicada exitosamente con onboarding_completed = true')
      } catch (error) {
        console.error('Error aplicando configuración pendiente:', error)
      }
    }

    // Verificar rol y redireccionar
    const user = response.data?.user || response.user // Compatibilidad con ambas estructuras
    console.log('Usuario autenticado:', user)

    // 🎯 VERIFICAR SI HAY SUBDOMAIN PENDIENTE (desde registro trial)
    const registrationSuccess = localStorage.getItem('registration_success')
    if (registrationSuccess) {
      const data = JSON.parse(registrationSuccess)
      if (data.subdomain) {
        console.log('✅ Redirigiendo a tenant:', data.subdomain)
        localStorage.removeItem('registration_success')
        
        // Esperar un momento para mostrar el mensaje
        setTimeout(() => {
          window.location.href = data.subdomain + '/pos'
        }, 1000)
        return
      }
    }

    // Esperar un momento para mostrar el mensaje de éxito
    setTimeout(() => {
      // Si es super admin, ir directo al panel god mode
      if (user?.is_super_admin || user?.role?.name === 'superadmin') {
        router.push('/admin/god-mode')
        return
      }

      // 🎯 REDIRECCIÓN INTELIGENTE BASADA EN PERMISOS
      if (user?.role?.permissions) {
        const permissions = typeof user.role.permissions === 'string' 
          ? JSON.parse(user.role.permissions) 
          : user.role.permissions

        // Mapa de permisos a rutas (en orden de prioridad)
        const permissionRoutes = [
          { permission: 'pos.access', route: '/pos' },
          { permission: 'dashboard.view', route: '/pos' },
          { permission: 'invoices.view', route: '/invoices' },
          { permission: 'products.view', route: '/products' },
          { permission: 'customers.view', route: '/customers' },
          { permission: 'reports.view', route: '/reports' },
          { permission: 'users.view', route: '/users' },
          { permission: 'cash_register.view', route: '/cash-admin' },
          { permission: 'expenses.view', route: '/expenses' },
          { permission: 'settings.view', route: '/settings' }
        ]

        // Buscar la primera ruta para la que el usuario tiene permiso
        for (const { permission, route } of permissionRoutes) {
          if (permissions.includes(permission)) {
            router.push(route)
            return
          }
        }

        // Si no tiene ningún permiso específico, ir al POS por defecto
        router.push('/pos')
      } else {
        // Fallback: Si no tiene rol definido, ir al POS
        router.push('/pos')
      }
    }, 1000)

  } catch (error) {
    console.error('Error en login:', error)
    
    if (error.errors) {
      // Errores de validación del servidor
      if (error.errors.email) {
        errors.email = error.errors.email[0]
      }
      if (error.errors.password) {
        errors.password = error.errors.password[0]
      }
    } else {
      // Error general
      message.text = error.message || 'Credenciales incorrectas'
      message.type = 'error'
    }
  } finally {
    loading.value = false
  }
}

// Auto-completar en desarrollo
const setDemoCredentials = (role) => {
  if (!isDevelopment.value) return
  
  switch (role) {
    case 'admin':
      credentials.cc = '12345678'
      credentials.password = 'admin123'
      break
    case 'cajero':
      credentials.cc = '87654321'
      credentials.password = 'cajero123'
      break
    case 'vendedor':
      credentials.cc = '11223344'
      credentials.password = 'vendedor123'
      break
  }
}

// Verificar si ya está autenticado
if (authService.isAuthenticated()) {
  router.push('/pos')
}
</script>

<style scoped>
/* 🎨 PREMIUM LOGIN DESIGN - Estilos Personalizados 2025 */

/* Animación de spinner */
.animate-spin {
  animation: spin 1s linear infinite;
}

@keyframes spin {
  from { transform: rotate(0deg); }
  to { transform: rotate(360deg); }
}

/* Animación pulse para indicador de estado */
.animate-pulse {
  animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}

@keyframes pulse {
  0%, 100% { opacity: 1; }
  50% { opacity: 0.5; }
}

/* Focus ring premium para inputs */
input:focus {
  box-shadow: 0 0 0 4px rgba(16, 185, 129, 0.1);
}

/* Smooth transitions globales */
input,
button,
a {
  transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
}

/* Efecto hover premium en botón principal */
button[type="submit"]:hover:not(:disabled) {
  transform: translateY(-2px);
  box-shadow: 0 20px 40px -10px rgba(15, 23, 42, 0.3);
}

button[type="submit"]:active:not(:disabled) {
  transform: translateY(0);
}

/* Animación de entrada del formulario */
@keyframes fadeInUp {
  from {
    opacity: 0;
    transform: translateY(30px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

/* Contenedor principal con animación */
.max-w-\[420px\] {
  animation: fadeInUp 0.8s ease-out;
}

/* Efecto shimmer para skeleton loader */
@keyframes shimmer {
  0% { transform: translateX(-100%); }
  100% { transform: translateX(100%); }
}

/* Transición suave para imagen de fondo */
img {
  transition: opacity 1s ease-out, transform 0.3s ease;
}

/* Glassmorphism mejorado para cards */
.backdrop-blur-xl {
  backdrop-filter: blur(16px);
  -webkit-backdrop-filter: blur(16px);
}

.backdrop-blur-md {
  backdrop-filter: blur(12px);
  -webkit-backdrop-filter: blur(12px);
}

.backdrop-blur-sm {
  backdrop-filter: blur(8px);
  -webkit-backdrop-filter: blur(8px);
}

/* Hover sutil en stats */
.bg-white\/5:hover {
  background-color: rgba(255, 255, 255, 0.08);
  transform: translateY(-2px);
  transition: all 0.3s ease;
}

/* Grid pattern sutil */
.bg-\[radial-gradient\(\#e5e7eb_1px\,transparent_1px\)\] {
  background-image: radial-gradient(#e5e7eb 1px, transparent 1px);
}

/* Underline offset para links */
.underline-offset-2 {
  text-underline-offset: 2px;
}

/* Scroll suave */
html {
  scroll-behavior: smooth;
}

/* Selección de texto con color de marca */
::selection {
  background-color: rgba(16, 185, 129, 0.2);
  color: inherit;
}

/* Media query para pantallas pequeñas */
@media (max-width: 1024px) {
  .max-w-\[420px\] {
    max-width: 100%;
    padding-left: 1.5rem;
    padding-right: 1.5rem;
  }
}

/* Efecto de focus visible mejorado */
button:focus-visible,
input:focus-visible,
a:focus-visible {
  outline: 2px solid #10b981;
  outline-offset: 2px;
}
</style>