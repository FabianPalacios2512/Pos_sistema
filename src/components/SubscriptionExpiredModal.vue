<template>
  <!-- Paywall - Pantalla completa con overlay -->
  <div 
    v-if="showModal"
    data-modal-subscription="active"
    class="fixed inset-0 z-[9999] overflow-y-auto"
    @click.prevent
    @contextmenu.prevent
  >
    <!-- Fondo con blur sobre la app -->
    <div class="fixed inset-0 bg-gradient-to-br from-gray-900/80 via-gray-900/90 to-black/95 "></div>
    
    <!-- Contenido centrado -->
    <div class="relative min-h-screen flex items-center justify-center p-4 md:p-8">
      <div class="w-full max-w-5xl animate-fade-in">
        
        <!-- Header Section -->
        <div class="text-center mb-8 md:mb-10">
          <!-- Logo 105 POS -->
          <div class="inline-flex items-center justify-center w-20 h-20 bg-white dark:bg-zinc-800 rounded-2xl shadow-xl shadow-black/20 mb-5 p-2">
            <img src="/logo.png" alt="105 POS Pro" class="w-full h-full object-contain" />
          </div>
          
          <h1 class="text-2xl md:text-3xl font-bold text-white mb-3">
            Tu negocio no se detiene
          </h1>
          <p class="text-gray-400 text-base md:text-lg max-w-lg mx-auto">
            Tu suscripción ha expirado. Renueva ahora para continuar disfrutando de todas las funciones de 
            <span class="text-emerald-400 font-semibold">105 POS Pro</span>
          </p>
          
          <!-- Badge de seguridad -->
          <div class="inline-flex items-center gap-2 mt-4 px-4 py-2 bg-white/5 border border-white/10 rounded-full">
            <svg class="w-4 h-4 text-emerald-400" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
            </svg>
            <span class="text-sm text-gray-300">Tus datos están seguros y guardados</span>
          </div>
        </div>

        <!-- Pricing Cards -->
        <div v-if="!showPayment" class="grid grid-cols-1 md:grid-cols-3 gap-4 md:gap-5 mb-8">
          
          <!-- Plan Básico -->
          <button
            @click="selectedPlan = 'basic'"
            :class="[
              'relative bg-white dark:bg-zinc-900 rounded-2xl p-6 text-left transition-all duration-300 group',
              selectedPlan === 'basic' 
                ? 'ring-2 ring-emerald-500 shadow-xl shadow-emerald-500/20' 
                : 'ring-1 ring-gray-200 dark:ring-zinc-700 hover:ring-gray-300 dark:hover:ring-zinc-600 shadow-lg'
            ]"
          >
            <!-- Indicador de selección -->
            <div :class="[
              'absolute top-4 right-4 w-6 h-6 rounded-full border-2 flex items-center justify-center transition-all',
              selectedPlan === 'basic' 
                ? 'border-emerald-500 bg-emerald-500' 
                : 'border-gray-300 dark:border-zinc-600'
            ]">
              <svg v-if="selectedPlan === 'basic'" class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
              </svg>
            </div>
            
            <div class="mb-4">
              <h3 class="text-lg font-bold text-gray-900 dark:text-white">Básico</h3>
              <p class="text-sm text-gray-500 dark:text-zinc-400">Para emprendedores</p>
            </div>
            
            <div class="mb-5">
              <span class="text-3xl font-bold text-gray-900 dark:text-white">$25.000</span>
              <span class="text-gray-500 dark:text-zinc-400">/mes</span>
            </div>
            
            <ul class="space-y-2.5 text-sm">
              <li class="flex items-center gap-2 text-gray-600 dark:text-zinc-300">
                <svg class="w-4 h-4 text-emerald-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                </svg>
                1 Usuario
              </li>
              <li class="flex items-center gap-2 text-gray-600 dark:text-zinc-300">
                <svg class="w-4 h-4 text-emerald-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                </svg>
                1 Caja / 1 Sede
              </li>
              <li class="flex items-center gap-2 text-gray-600 dark:text-zinc-300">
                <svg class="w-4 h-4 text-emerald-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                </svg>
                Inventario básico
              </li>
              <li class="flex items-center gap-2 text-gray-600 dark:text-zinc-300">
                <svg class="w-4 h-4 text-emerald-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                </svg>
                Reportes esenciales
              </li>
            </ul>
          </button>

          <!-- Plan Premium (Destacado) -->
          <button
            @click="selectedPlan = 'premium'"
            :class="[
              'relative bg-white dark:bg-zinc-900 rounded-2xl p-6 text-left transition-all duration-300 group md:scale-105 md:-my-2',
              selectedPlan === 'premium' 
                ? 'ring-2 ring-emerald-500 shadow-2xl shadow-emerald-500/30' 
                : 'ring-1 ring-emerald-200 dark:ring-emerald-900/50 hover:ring-emerald-300 shadow-xl'
            ]"
          >
            <!-- Badge Recomendado -->
            <div class="absolute -top-3 left-1/2 -translate-x-1/2">
              <span class="px-3 py-1 bg-gradient-to-r from-emerald-500 to-emerald-600 text-white text-xs font-bold rounded-full shadow-lg">
                ⭐ RECOMENDADO
              </span>
            </div>
            
            <!-- Indicador de selección -->
            <div :class="[
              'absolute top-4 right-4 w-6 h-6 rounded-full border-2 flex items-center justify-center transition-all',
              selectedPlan === 'premium' 
                ? 'border-emerald-500 bg-emerald-500' 
                : 'border-emerald-300 dark:border-emerald-700'
            ]">
              <svg v-if="selectedPlan === 'premium'" class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
              </svg>
            </div>
            
            <div class="mb-4 mt-2">
              <h3 class="text-lg font-bold text-gray-900 dark:text-white">Premium</h3>
              <p class="text-sm text-emerald-600 dark:text-emerald-400 font-medium">El más elegido</p>
            </div>
            
            <div class="mb-5">
              <span class="text-3xl font-bold text-gray-900 dark:text-white">$60.000</span>
              <span class="text-gray-500 dark:text-zinc-400">/mes</span>
            </div>
            
            <ul class="space-y-2.5 text-sm">
              <li class="flex items-center gap-2 text-gray-600 dark:text-zinc-300">
                <svg class="w-4 h-4 text-emerald-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                </svg>
                3 Usuarios / 3 Bodegas
              </li>
              <li class="flex items-center gap-2 text-gray-600 dark:text-zinc-300">
                <svg class="w-4 h-4 text-emerald-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                </svg>
                Multi-Caja / 2 Sedes
              </li>
              <li class="flex items-center gap-2 text-gray-600 dark:text-zinc-300">
                <svg class="w-4 h-4 text-emerald-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                </svg>
                Tienda Web + Catálogo
              </li>
              <li class="flex items-center gap-2 text-gray-600 dark:text-zinc-300">
                <svg class="w-4 h-4 text-emerald-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                </svg>
                WhatsApp Automático
              </li>
              <li class="flex items-center gap-2 text-gray-600 dark:text-zinc-300">
                <svg class="w-4 h-4 text-emerald-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                </svg>
                Asistente IA
              </li>
            </ul>
          </button>

          <!-- Plan Empresarial -->
          <button
            @click="selectedPlan = 'corporativo'"
            :class="[
              'relative bg-white dark:bg-zinc-900 rounded-2xl p-6 text-left transition-all duration-300 group',
              selectedPlan === 'corporativo' 
                ? 'ring-2 ring-emerald-500 shadow-xl shadow-emerald-500/20' 
                : 'ring-1 ring-gray-200 dark:ring-zinc-700 hover:ring-gray-300 dark:hover:ring-zinc-600 shadow-lg'
            ]"
          >
            <!-- Indicador de selección -->
            <div :class="[
              'absolute top-4 right-4 w-6 h-6 rounded-full border-2 flex items-center justify-center transition-all',
              selectedPlan === 'corporativo' 
                ? 'border-emerald-500 bg-emerald-500' 
                : 'border-gray-300 dark:border-zinc-600'
            ]">
              <svg v-if="selectedPlan === 'corporativo'" class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
              </svg>
            </div>
            
            <div class="mb-4">
              <h3 class="text-lg font-bold text-gray-900 dark:text-white">Empresarial</h3>
              <p class="text-sm text-gray-500 dark:text-zinc-400">Para grandes negocios</p>
            </div>
            
            <div class="mb-5">
              <span class="text-3xl font-bold text-gray-900 dark:text-white">$100.000</span>
              <span class="text-gray-500 dark:text-zinc-400">/mes</span>
            </div>
            
            <ul class="space-y-2.5 text-sm">
              <li class="flex items-center gap-2 text-gray-600 dark:text-zinc-300">
                <svg class="w-4 h-4 text-emerald-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                </svg>
                Usuarios ilimitados
              </li>
              <li class="flex items-center gap-2 text-gray-600 dark:text-zinc-300">
                <svg class="w-4 h-4 text-emerald-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                </svg>
                Multi-Sede ilimitado
              </li>
              <li class="flex items-center gap-2 text-gray-600 dark:text-zinc-300">
                <svg class="w-4 h-4 text-emerald-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                </svg>
                Agente IA Avanzado
              </li>
              <li class="flex items-center gap-2 text-gray-600 dark:text-zinc-300">
                <svg class="w-4 h-4 text-emerald-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                </svg>
                Soporte 24/7 dedicado
              </li>
              <li class="flex items-center gap-2 text-gray-600 dark:text-zinc-300">
                <svg class="w-4 h-4 text-emerald-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                </svg>
                Integraciones API
              </li>
            </ul>
          </button>

        </div>

        <!-- Botón de Acción Principal -->
        <div v-if="!showPayment" class="max-w-md mx-auto">
          <button
            @click="proceedToPayment"
            :disabled="!selectedPlan || isProcessing"
            class="w-full py-4 bg-gradient-to-r from-emerald-500 to-emerald-600 hover:from-emerald-600 hover:to-emerald-700 text-white font-bold text-base rounded-xl shadow-xl shadow-emerald-500/30 transition-all duration-300 disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-3"
          >
            <svg v-if="isProcessing" class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span>{{ isProcessing ? 'Procesando...' : `Renovar Suscripción · ${getPlanPrice()}` }}</span>
            <svg v-if="!isProcessing" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
            </svg>
          </button>
        </div>

        <!-- Estado de Pago -->
        <div v-else class="max-w-md mx-auto bg-white dark:bg-zinc-900 rounded-2xl p-8 text-center">
          <div class="w-12 h-12 border-3 border-emerald-200 border-t-emerald-500 rounded-full animate-spin mx-auto mb-4"></div>
          <p class="text-gray-600 dark:text-zinc-300">Redirigiendo a pasarela de pago segura...</p>
        </div>

        <!-- Footer con badges de confianza -->
        <div class="mt-8 flex flex-col md:flex-row items-center justify-center gap-4 md:gap-8 text-sm text-gray-400">
          <!-- Pago seguro -->
          <div class="flex items-center gap-2">
            <svg class="w-5 h-5 text-emerald-500" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
            </svg>
            <span>Pago 100% seguro</span>
          </div>
          
          <!-- ePayco -->
          <div class="flex items-center gap-2">
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
              <path d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
            </svg>
            <span>Procesado por ePayco</span>
          </div>

          <!-- Soporte -->
          <div class="flex items-center gap-4">
            <a 
              href="https://wa.me/573001234567?text=Hola, necesito ayuda con mi suscripción de 105POS" 
              target="_blank"
              class="hover:text-emerald-400 transition-colors flex items-center gap-1.5"
            >
              <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
              </svg>
              WhatsApp
            </a>
            <span class="text-gray-600">·</span>
            <a 
              href="mailto:soporte@105pos.pro?subject=Ayuda con renovación de suscripción" 
              class="hover:text-blue-400 transition-colors"
            >
              soporte@105pos.pro
            </a>
          </div>
        </div>

      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, watch, onUnmounted } from 'vue'
import { appStore } from '../store/appStore'
import apiClient from '../services/apiClient'

const showModal = ref(false)
const selectedPlan = ref('premium') // Plan más popular por defecto
const showPayment = ref(false)
const isProcessing = ref(false)
const tenantId = ref(null)

// Obtener precio del plan seleccionado
const getPlanPrice = () => {
  const prices = {
    basic: '$25.000',
    premium: '$60.000',
    corporativo: '$100.000'
  }
  return prices[selectedPlan.value] || ''
}

// 🔐 Sistema de verificación seguro
let verificationToken = null
let paymentReference = null
let verificationInterval = null

let modalCheckInterval = null
let subscriptionPollingInterval = null

const preventContextMenu = (e) => {
  if (showModal.value) {
    e.preventDefault()
    return false
  }
}

const preventKeyboardShortcuts = (e) => {
  if (showModal.value) {
    if (e.keyCode === 123 || (e.ctrlKey && e.shiftKey && e.keyCode === 73) || 
        (e.ctrlKey && e.shiftKey && e.keyCode === 74) || (e.ctrlKey && e.shiftKey && e.keyCode === 67) || 
        (e.ctrlKey && e.keyCode === 85) || e.keyCode === 27) {
      e.preventDefault()
      return false
    }
  }
}

/**
 * 🔄 LÓGICA SIMPLIFICADA: Consultar backend y decidir
 * - Si active === true → NO bloquear
 * - Si active === false → bloquear
 */
const checkSubscriptionFromBackend = async () => {
  try {
    // Usar el endpoint que SIEMPRE consulta la base de datos
    const response = await apiClient.get('/subscription/status')
    
    if (response.data.success) {
      if (response.data.active === true) {
        // ✅ Suscripción ACTIVA - no bloquear
        showModal.value = false
        appStore.isSubscriptionExpired = false
        stopAllIntervals()
        return false // No expirada
      } else {
        // ⛔ Suscripción EXPIRADA - bloquear
        appStore.isSubscriptionExpired = true
        showModal.value = true
        tenantId.value = response.data.tenant?.id || ''
        activateAntiBypass()
        return true // Expirada
      }
    }
  } catch (error) {
    console.error('Error verificando suscripción:', error)
    // En caso de error, no cambiar el estado actual
  }
  return appStore.isSubscriptionExpired
}

/**
 * 🔄 Iniciar polling para detectar cuando se paga
 */
const startSubscriptionPolling = () => {
  if (subscriptionPollingInterval) return
  
  console.log('🔄 [Subscription] Iniciando polling cada 8 segundos...')
  
  subscriptionPollingInterval = setInterval(async () => {
    const isExpired = await checkSubscriptionFromBackend()
    
    if (!isExpired) {
      // ✅ Ya no está expirada - recargar página
      console.log('✅ [Subscription] Suscripción reactivada! Recargando...')
      stopAllIntervals()
      setTimeout(() => window.location.reload(), 1000)
    }
  }, 8000) // Cada 8 segundos
}

const stopAllIntervals = () => {
  if (modalCheckInterval) {
    clearInterval(modalCheckInterval)
    modalCheckInterval = null
  }
  if (subscriptionPollingInterval) {
    clearInterval(subscriptionPollingInterval)
    subscriptionPollingInterval = null
  }
}

const activateAntiBypass = () => {
  // Solo activar si no está ya activo
  if (modalCheckInterval) return
  
  modalCheckInterval = setInterval(() => {
    // Solo forzar modal si está bloqueado
    if (appStore.isSubscriptionExpired && !showModal.value) {
      showModal.value = true
    }
  }, 500)
  
  document.addEventListener('contextmenu', preventContextMenu)
  document.addEventListener('keydown', preventKeyboardShortcuts)
  document.body.style.userSelect = 'none'
  
  // Iniciar polling para detectar pago
  startSubscriptionPolling()
}

onMounted(async () => {
  // Verificar estado real desde el backend
  await checkSubscriptionFromBackend()
})

onUnmounted(() => {
  stopAllIntervals()
  if (verificationInterval) clearInterval(verificationInterval)
  document.removeEventListener('contextmenu', preventContextMenu)
  document.removeEventListener('keydown', preventKeyboardShortcuts)
  document.body.style.userSelect = ''
})

// Watch simplificado - solo para sincronizar el modal con el estado
watch(() => appStore.isSubscriptionExpired, (newVal) => {
  showModal.value = newVal
  if (newVal) {
    activateAntiBypass()
  } else {
    stopAllIntervals()
  }
})

const proceedToPayment = async () => {
  if (!selectedPlan.value || !tenantId.value) {
    alert('Error: No se pudo identificar tu cuenta.')
    return
  }

  isProcessing.value = true

  try {
    // Mapeo de planes: corporativo → enterprise (el que funciona en ePayco)
    const planMapping = {
      basic: { amount: 25000, epaycoName: 'basic' },
      premium: { amount: 60000, epaycoName: 'premium' },
      corporativo: { amount: 100000, epaycoName: 'enterprise' }
    }
    
    const planData = planMapping[selectedPlan.value]
    const amount = planData.amount
    const epaycoPlan = planData.epaycoName
    const reference = `renewal_${tenantId.value}_${Date.now()}`
    
    // 🔐 PASO 1: Inicializar transacción en backend y obtener token de verificación
    const initResponse = await apiClient.post('/epayco/init-transaction', {
      reference,
      plan: epaycoPlan,
      tenant_id: tenantId.value,
      amount,
      payment_frequency: 'monthly',
      customer_email: appStore.user?.email || 'cliente@105pos.pro'
    })

    if (!initResponse.data.success) {
      throw new Error('No se pudo inicializar la transacción')
    }

    // Guardar token y referencia para verificación
    verificationToken = initResponse.data.verification_token
    paymentReference = reference

    // Configurar ePayco con la API Key correcta
    const handler = window.ePayco.checkout.configure({
      key: '2943652c673afffaa5b7b67829f00a0c', // API Key de producción
      test: true // Mantener en TRUE para usar tarjeta de prueba
    })

    // Obtener URL de respuesta correcta (usar verificación)
    const currentUrl = window.location.origin
    const responseUrl = `${currentUrl}/payment/verify?tenant_id=${tenantId.value}&plan=${selectedPlan.value}&reference=${reference}&renewal=true`

    handler.open({
      name: `Plan ${selectedPlan.value.toUpperCase()} - Renovación`,
      description: `Renovación suscripción - Plan ${epaycoPlan}`,
      invoice: reference,
      currency: 'cop',
      amount: amount,
      tax_base: '0',
      tax: '0',
      country: 'co',
      lang: 'es',
      external: 'true', // Standard Checkout (página de ePayco)
      response: responseUrl,
      confirmation: 'https://105pos.pro/api/epayco/webhook',
      name_billing: appStore.businessName || 'Cliente 105POS',
      address_billing: 'Calle 123 # 45-67',
      type_doc_billing: 'cc',
      mobilephone_billing: '3000000000',
      number_doc_billing: '1000000000',
      email_billing: 'cliente@105pos.pro',
      extra1: tenantId.value,
      extra2: epaycoPlan, // Enviar 'enterprise' en vez de 'corporativo'
      extra3: 'renewal'
    })
    
    showPayment.value = true

    // 🔎 PASO 2: Iniciar verificación periódica del estado del pago
    startPaymentVerification()
    
  } catch (error) {
    console.error('Error al abrir pasarela de pago:', error)
    alert('Error al procesar el pago. Por favor intenta de nuevo.')
  } finally {
    isProcessing.value = false
  }
}

/**
 * 🔎 Verificar periódicamente el estado del pago con token seguro
 * Funciona tanto en localhost como en producción
 */
const startPaymentVerification = () => {
  if (verificationInterval) {
    clearInterval(verificationInterval)
  }

  let attempts = 0
  const maxAttempts = 120 // 10 minutos (cada 5 segundos)

  verificationInterval = setInterval(async () => {
    attempts++

    try {
      const response = await apiClient.post('/epayco/verify-payment', {
        reference: paymentReference,
        verification_token: verificationToken
      })

      if (response.data.success) {
        const status = response.data.status

        if (status === 'approved') {
          // ✅ Pago aprobado
          clearInterval(verificationInterval)
          appStore.isSubscriptionExpired = false
          showModal.value = false
          alert('✅ Pago aprobado correctamente. Tu suscripción ha sido renovada.')
          window.location.reload()
        } else if (status === 'rejected' || status === 'failed') {
          // ❌ Pago rechazado
          clearInterval(verificationInterval)
          alert('❌ El pago fue rechazado. Por favor intenta de nuevo con otro método de pago.')
        }
        // Si está 'pending', seguimos esperando
      }
    } catch (error) {
      console.error('Error verificando pago:', error)
    }

    // Detener después de max intentos
    if (attempts >= maxAttempts) {
      clearInterval(verificationInterval)
      alert('⏱️ El tiempo de verificación expiró. Por favor contacta a soporte si ya realizaste el pago.')
    }
  }, 5000) // Verificar cada 5 segundos
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
