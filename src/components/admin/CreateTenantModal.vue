<template>
  <div class="fixed inset-0 bg-black/70 backdrop-blur-sm flex items-center justify-center z-50 p-4">
    <div class="bg-white dark:bg-zinc-900 rounded-2xl max-w-2xl w-full border border-gray-300 dark:border-zinc-800 shadow-2xl dark:shadow-black/50 max-h-[90vh] overflow-hidden">
      <!-- Header -->
      <div class="bg-gray-50 dark:bg-zinc-900 border-b border-gray-200 dark:border-zinc-800 px-6 py-4 flex items-center justify-between">
        <div>
          <h3 class="text-xl font-bold text-gray-900 dark:text-white">Crear Nueva Tienda</h3>
          <p class="text-sm text-gray-600 dark:text-zinc-400 mt-0.5">Crea una cuenta manualmente para un nuevo cliente</p>
        </div>
        <button @click="$emit('close')" class="p-2 text-gray-400 dark:text-zinc-400 hover:text-gray-600 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-zinc-800 rounded-lg transition-all">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
          </svg>
        </button>
      </div>

      <!-- Form Content -->
      <div class="p-6 overflow-y-auto max-h-[calc(90vh-180px)]">
        <form @submit.prevent="handleCreate" class="space-y-5">
          
          <!-- Row 1: Nombre del dueño y Cédula/NIT -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="space-y-2">
              <label class="block text-sm font-bold text-gray-700 dark:text-zinc-300">
                Nombre del Propietario <span class="text-red-500">*</span>
              </label>
              <input 
                v-model="form.owner_name" 
                type="text" 
                placeholder="Ej. Juan Pérez"
                class="w-full px-4 py-3 bg-gray-50 dark:bg-zinc-800 text-gray-900 dark:text-zinc-200 rounded-xl border-2 border-gray-200 dark:border-zinc-700 focus:border-blue-500 dark:focus:border-blue-400 focus:outline-none focus:ring-2 focus:ring-blue-500/20 transition-all placeholder-gray-400 dark:placeholder-zinc-500" 
                required
              >
            </div>

            <div class="space-y-2">
              <label class="block text-sm font-bold text-gray-700 dark:text-zinc-300">
                Cédula / NIT <span class="text-red-500">*</span>
              </label>
              <div class="relative">
                <input 
                  v-model="form.cedula" 
                  type="text" 
                  placeholder="Ej. 123456789"
                  @input="form.cedula = form.cedula.replace(/[^0-9]/g, ''); checkCedula()"
                  maxlength="15"
                  class="w-full px-4 py-3 bg-gray-50 dark:bg-zinc-800 text-gray-900 dark:text-zinc-200 rounded-xl border-2 transition-all placeholder-gray-400 dark:placeholder-zinc-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20"
                  :class="[
                    cedulaStatus === 'taken' ? 'border-red-500 dark:border-red-500' : 
                    cedulaStatus === 'available' ? 'border-emerald-500 dark:border-emerald-500' : 
                    'border-gray-200 dark:border-zinc-700 focus:border-blue-500 dark:focus:border-blue-400'
                  ]"
                  required
                >
                <div v-if="cedulaStatus === 'checking'" class="absolute right-3 top-1/2 -translate-y-1/2">
                  <svg class="animate-spin w-5 h-5 text-blue-500" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                  </svg>
                </div>
                <div v-else-if="cedulaStatus === 'available'" class="absolute right-3 top-1/2 -translate-y-1/2">
                  <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                  </svg>
                </div>
                <div v-else-if="cedulaStatus === 'taken'" class="absolute right-3 top-1/2 -translate-y-1/2">
                  <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                  </svg>
                </div>
              </div>
              <p v-if="cedulaStatus === 'taken'" class="text-xs text-red-500 font-medium">Esta cédula/NIT ya está registrada</p>
            </div>
          </div>

          <!-- Row 2: Nombre del Negocio -->
          <div class="space-y-2">
            <label class="block text-sm font-bold text-gray-700 dark:text-zinc-300">
              Nombre del Negocio <span class="text-red-500">*</span>
            </label>
            <input 
              v-model="form.business_name" 
              type="text" 
              placeholder="Ej. Cafetería Central"
              @input="generateSubdomain"
              class="w-full px-4 py-3 bg-gray-50 dark:bg-zinc-800 text-gray-900 dark:text-zinc-200 rounded-xl border-2 border-gray-200 dark:border-zinc-700 focus:border-blue-500 dark:focus:border-blue-400 focus:outline-none focus:ring-2 focus:ring-blue-500/20 transition-all placeholder-gray-400 dark:placeholder-zinc-500" 
              required
            >
          </div>

          <!-- Row 3: Email y Contraseña -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="space-y-2">
              <label class="block text-sm font-bold text-gray-700 dark:text-zinc-300">
                Email del Admin <span class="text-red-500">*</span>
              </label>
              <div class="relative">
                <input 
                  v-model="form.admin_email" 
                  type="email" 
                  placeholder="admin@empresa.com"
                  @blur="checkEmail"
                  class="w-full px-4 py-3 bg-gray-50 dark:bg-zinc-800 text-gray-900 dark:text-zinc-200 rounded-xl border-2 transition-all placeholder-gray-400 dark:placeholder-zinc-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20"
                  :class="[
                    emailStatus === 'taken' ? 'border-red-500 dark:border-red-500' : 
                    emailStatus === 'available' ? 'border-emerald-500 dark:border-emerald-500' : 
                    'border-gray-200 dark:border-zinc-700 focus:border-blue-500 dark:focus:border-blue-400'
                  ]"
                  required
                >
                <div v-if="emailStatus === 'checking'" class="absolute right-3 top-1/2 -translate-y-1/2">
                  <svg class="animate-spin w-5 h-5 text-blue-500" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                  </svg>
                </div>
                <div v-else-if="emailStatus === 'available'" class="absolute right-3 top-1/2 -translate-y-1/2">
                  <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                  </svg>
                </div>
                <div v-else-if="emailStatus === 'taken'" class="absolute right-3 top-1/2 -translate-y-1/2">
                  <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                  </svg>
                </div>
              </div>
              <p v-if="emailStatus === 'taken'" class="text-xs text-red-500 font-medium">Este email ya está registrado</p>
            </div>

            <div class="space-y-2">
              <label class="block text-sm font-bold text-gray-700 dark:text-zinc-300">
                Contraseña <span class="text-red-500">*</span>
              </label>
              <div class="relative">
                <input 
                  v-model="form.admin_password" 
                  :type="showPassword ? 'text' : 'password'" 
                  placeholder="Mínimo 6 caracteres"
                  minlength="6"
                  class="w-full px-4 py-3 pr-12 bg-gray-50 dark:bg-zinc-800 text-gray-900 dark:text-zinc-200 rounded-xl border-2 border-gray-200 dark:border-zinc-700 focus:border-blue-500 dark:focus:border-blue-400 focus:outline-none focus:ring-2 focus:ring-blue-500/20 transition-all placeholder-gray-400 dark:placeholder-zinc-500" 
                  required
                >
                <button 
                  type="button" 
                  @click="showPassword = !showPassword"
                  class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 dark:text-zinc-500 hover:text-gray-600 dark:hover:text-zinc-300 transition-colors"
                >
                  <svg v-if="!showPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                  </svg>
                  <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path>
                  </svg>
                </button>
              </div>
            </div>
          </div>

          <!-- Row 4: Subdominio -->
          <div class="space-y-2">
            <label class="block text-sm font-bold text-gray-700 dark:text-zinc-300">
              Subdominio <span class="text-red-500">*</span>
            </label>
            <div class="relative">
              <input 
                v-model="form.subdomain" 
                type="text" 
                placeholder="mi-negocio"
                @input="checkAvailability"
                class="w-full px-4 py-3 pr-32 bg-gray-50 dark:bg-zinc-800 text-gray-900 dark:text-zinc-200 rounded-xl border-2 transition-all placeholder-gray-400 dark:placeholder-zinc-500 font-mono focus:outline-none focus:ring-2 focus:ring-blue-500/20"
                :class="[
                  availabilityStatus === 'taken' ? 'border-red-500 dark:border-red-500' : 
                  availabilityStatus === 'available' ? 'border-emerald-500 dark:border-emerald-500' : 
                  'border-gray-200 dark:border-zinc-700 focus:border-blue-500 dark:focus:border-blue-400'
                ]"
                required
              >
              <div class="absolute right-0 top-0 h-full flex items-center pr-4 pointer-events-none">
                <span class="text-gray-500 dark:text-zinc-400 font-medium bg-gray-100 dark:bg-zinc-700 px-3 py-1.5 rounded-lg text-sm">.105pos.pro</span>
              </div>
            </div>
            
            <!-- Estado de Disponibilidad -->
            <div class="min-h-5 mt-1">
              <p v-if="availabilityStatus === 'checking'" class="text-xs text-gray-500 dark:text-zinc-400 flex items-center">
                <svg class="animate-spin w-3.5 h-3.5 mr-1.5" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Verificando disponibilidad...
              </p>
              <p v-else-if="availabilityStatus === 'available'" class="text-xs text-emerald-600 dark:text-emerald-400 flex items-center font-semibold">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                Disponible: {{ form.subdomain }}.105pos.pro
              </p>
              <p v-else-if="availabilityStatus === 'taken'" class="text-xs text-red-600 dark:text-red-400 flex items-center font-semibold">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
                No disponible - Prueba otro nombre
              </p>
              <p v-else-if="availabilityStatus === 'invalid'" class="text-xs text-amber-600 dark:text-amber-400 flex items-center font-semibold">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>
                Solo letras minúsculas, números y guiones (-)
              </p>
            </div>
          </div>

          <!-- Row 5: Plan -->
          <div class="space-y-2">
            <label class="block text-sm font-bold text-gray-700 dark:text-zinc-300">Plan</label>
            <select 
              v-model="form.plan" 
              class="w-full px-4 py-3 bg-gray-50 dark:bg-zinc-800 text-gray-900 dark:text-zinc-200 rounded-xl border-2 border-gray-200 dark:border-zinc-700 focus:border-blue-500 dark:focus:border-blue-400 focus:outline-none focus:ring-2 focus:ring-blue-500/20 transition-all"
            >
              <option value="free">🎁 Free Trial (7 días)</option>
              <option value="basic">💼 Basic ($29/mes)</option>
              <option value="premium">⭐ Premium ($79/mes)</option>
              <option value="enterprise">🏢 Enterprise ($199/mes)</option>
            </select>
          </div>

          <!-- Error Message -->
          <div v-if="errorMessage" class="bg-red-50 dark:bg-red-950 border border-red-200 dark:border-red-800 rounded-xl p-4">
            <div class="flex items-start gap-3">
              <svg class="w-5 h-5 text-red-600 dark:text-red-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
              </svg>
              <p class="text-sm text-red-700 dark:text-red-400">{{ errorMessage }}</p>
            </div>
          </div>

          <!-- Success Message -->
          <div v-if="successData" class="bg-emerald-50 dark:bg-emerald-950 border border-emerald-200 dark:border-emerald-800 rounded-xl p-4">
            <div class="flex items-start gap-3">
              <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
              </svg>
              <div class="flex-1">
                <p class="text-sm font-bold text-emerald-700 dark:text-emerald-400">¡Tienda creada exitosamente!</p>
                <div class="mt-2 space-y-1 text-xs text-emerald-600 dark:text-emerald-500">
                  <p><strong>Dominio:</strong> {{ successData.domain }}</p>
                  <p><strong>Email:</strong> {{ successData.credentials?.email }}</p>
                  <p><strong>Contraseña:</strong> {{ successData.credentials?.password }}</p>
                </div>
                <a 
                  :href="successData.login_url" 
                  target="_blank" 
                  class="inline-flex items-center gap-1 mt-3 text-xs font-semibold text-emerald-700 dark:text-emerald-400 hover:underline"
                >
                  Ir al login
                  <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                  </svg>
                </a>
              </div>
            </div>
          </div>
        </form>
      </div>

      <!-- Footer Buttons -->
      <div class="bg-gray-50 dark:bg-zinc-900 border-t border-gray-200 dark:border-zinc-800 px-6 py-4 flex items-center justify-end gap-3">
        <button 
          @click="$emit('close')" 
          class="px-5 py-2.5 bg-white dark:bg-zinc-800 hover:bg-gray-50 dark:hover:bg-zinc-700 text-gray-700 dark:text-zinc-200 text-sm font-bold rounded-xl border border-gray-200 dark:border-zinc-700 shadow-sm transition-all duration-200"
        >
          {{ successData ? 'Cerrar' : 'Cancelar' }}
        </button>
        <button 
          v-if="!successData"
          @click="handleCreate" 
          :disabled="!canCreate || isCreating"
          class="px-6 py-2.5 bg-slate-900 dark:bg-slate-700 hover:bg-black dark:hover:bg-slate-600 text-white text-sm font-bold rounded-xl shadow-lg shadow-slate-400/40 dark:shadow-slate-900/50 transition-all duration-300 disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2"
        >
          <svg v-if="isCreating" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
          </svg>
          {{ isCreating ? 'Creando...' : 'Crear Tienda' }}
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue'
import axios from 'axios'

const emit = defineEmits(['close', 'created'])

// Form data
const form = ref({
  owner_name: '',
  cedula: '',
  business_name: '',
  admin_email: '',
  admin_password: '',
  subdomain: '',
  plan: 'basic'
})

// UI states
const showPassword = ref(false)
const isCreating = ref(false)
const errorMessage = ref('')
const successData = ref(null)

// Validation states
const availabilityStatus = ref('') // '', 'checking', 'available', 'taken', 'invalid'
const cedulaStatus = ref('') // '', 'checking', 'available', 'taken'
const emailStatus = ref('') // '', 'checking', 'available', 'taken'

// Debounce timers
let availabilityTimer = null
let cedulaTimer = null
let emailTimer = null

// API base URL
const API_URL = import.meta.env.VITE_API_URL || 'https://api.105pos.pro'

// Generate subdomain from business name
const generateSubdomain = () => {
  if (form.value.business_name) {
    const subdomain = form.value.business_name
      .toLowerCase()
      .normalize('NFD')
      .replace(/[\u0300-\u036f]/g, '') // Remove accents
      .replace(/[^a-z0-9\s-]/g, '') // Remove special chars
      .replace(/\s+/g, '-') // Spaces to hyphens
      .replace(/-+/g, '-') // Multiple hyphens to single
      .replace(/^-|-$/g, '') // Trim hyphens
      .substring(0, 30)
    
    form.value.subdomain = subdomain
    checkAvailability()
  }
}

// Check subdomain availability
const checkAvailability = () => {
  clearTimeout(availabilityTimer)
  
  const subdomain = form.value.subdomain
  
  if (!subdomain || subdomain.length < 2) {
    availabilityStatus.value = ''
    return
  }
  
  // Validate format
  if (!/^[a-z0-9-]+$/.test(subdomain)) {
    availabilityStatus.value = 'invalid'
    return
  }
  
  availabilityStatus.value = 'checking'
  
  availabilityTimer = setTimeout(async () => {
    try {
      const fullDomain = `${subdomain}.105pos.pro`
      const response = await axios.get(`${API_URL}/api/check-domain/${fullDomain}`)
      availabilityStatus.value = response.data.available ? 'available' : 'taken'
    } catch (error) {
      availabilityStatus.value = 'available' // Assume available on error
    }
  }, 500)
}

// Check cedula availability
const checkCedula = () => {
  clearTimeout(cedulaTimer)
  
  const cedula = form.value.cedula
  
  if (!cedula || cedula.length < 5) {
    cedulaStatus.value = ''
    return
  }
  
  cedulaStatus.value = 'checking'
  
  cedulaTimer = setTimeout(async () => {
    try {
      const response = await axios.get(`${API_URL}/api/check-cedula/${cedula}`)
      cedulaStatus.value = response.data.available ? 'available' : 'taken'
    } catch (error) {
      cedulaStatus.value = 'available' // Assume available on error
    }
  }, 500)
}

// Check email availability
const checkEmail = () => {
  clearTimeout(emailTimer)
  
  const email = form.value.admin_email
  
  if (!email || !email.includes('@')) {
    emailStatus.value = ''
    return
  }
  
  emailStatus.value = 'checking'
  
  emailTimer = setTimeout(async () => {
    try {
      const response = await axios.get(`${API_URL}/api/check-email/${encodeURIComponent(email)}`)
      emailStatus.value = response.data.available ? 'available' : 'taken'
    } catch (error) {
      emailStatus.value = 'available' // Assume available on error
    }
  }, 500)
}

// Check if form is valid
const canCreate = computed(() => {
  return (
    form.value.owner_name.length >= 2 &&
    form.value.cedula.length >= 5 &&
    form.value.business_name.length >= 2 &&
    form.value.admin_email.includes('@') &&
    form.value.admin_password.length >= 6 &&
    form.value.subdomain.length >= 2 &&
    availabilityStatus.value === 'available' &&
    cedulaStatus.value !== 'taken' &&
    emailStatus.value !== 'taken'
  )
})

// Handle create tenant
const handleCreate = async () => {
  if (!canCreate.value || isCreating.value) return
  
  isCreating.value = true
  errorMessage.value = ''
  successData.value = null
  
  try {
    const token = localStorage.getItem('token')
    
    const response = await axios.post(
      `${API_URL}/api/admin/tenants`,
      {
        owner_name: form.value.owner_name,
        cedula: form.value.cedula,
        business_name: form.value.business_name,
        subdomain: form.value.subdomain,
        plan: form.value.plan,
        admin_email: form.value.admin_email,
        admin_password: form.value.admin_password
      },
      {
        headers: {
          Authorization: `Bearer ${token}`,
          'Content-Type': 'application/json'
        }
      }
    )
    
    if (response.data.success) {
      successData.value = response.data.data
      emit('created', response.data.data)
    } else {
      errorMessage.value = response.data.message || 'Error al crear la tienda'
    }
  } catch (error) {
    if (error.response?.data?.message) {
      errorMessage.value = error.response.data.message
    } else if (error.response?.data?.errors) {
      errorMessage.value = Object.values(error.response.data.errors).flat().join('. ')
    } else {
      errorMessage.value = 'Error de conexión. Por favor intenta de nuevo.'
    }
  } finally {
    isCreating.value = false
  }
}
</script>
