<template>
  <div class="min-h-screen bg-gradient-to-b from-gray-50 via-white to-gray-50 dark:from-[#141417] dark:via-[#1a1a1f] dark:to-[#141417] flex items-center justify-center p-4 lg:p-8">
    <div class="max-w-3xl w-full">
      
      <!-- Upgrade Card -->
      <div class="bg-white dark:bg-zinc-900 rounded-3xl shadow-2xl border-2 border-gray-200 dark:border-zinc-800 overflow-hidden">
        
        <!-- Header with gradient -->
        <div class="bg-gradient-to-r from-blue-600 via-purple-600 to-pink-600 p-8 text-center">
          <div class="inline-flex items-center justify-center w-20 h-20 rounded-2xl bg-white/20  mb-4">
            <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
            </svg>
          </div>
          <h1 class="text-4xl font-black text-white mb-3">
            ¡Tienda Online Disponible!
          </h1>
          <p class="text-xl text-white/90 font-medium">
            Activa tu catálogo web y vende 24/7
          </p>
        </div>

        <!-- Content -->
        <div class="p-8 lg:p-12 space-y-8">
          
          <!-- Feature Preview -->
          <div class="text-center">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">
              ¿Qué incluye la Tienda Online?
            </h2>
            <p class="text-gray-600 dark:text-zinc-400 max-w-xl mx-auto">
              Crea tu catálogo personalizado sin código y recibe pedidos por WhatsApp automáticamente
            </p>
          </div>

          <!-- Features Grid -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="flex items-start gap-4 p-5 bg-gradient-to-br from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 rounded-xl border border-blue-200 dark:border-blue-800">
              <div class="flex-shrink-0 w-10 h-10 rounded-lg bg-blue-500 flex items-center justify-center">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01" />
                </svg>
              </div>
              <div>
                <h3 class="font-bold text-gray-900 dark:text-white mb-1">Diseño Personalizable</h3>
                <p class="text-sm text-gray-600 dark:text-zinc-400">Logo, colores, banner y plantillas profesionales</p>
              </div>
            </div>

            <div class="flex items-start gap-4 p-5 bg-gradient-to-br from-green-50 to-emerald-50 dark:from-green-900/20 dark:to-emerald-900/20 rounded-xl border border-green-200 dark:border-green-800">
              <div class="flex-shrink-0 w-10 h-10 rounded-lg bg-green-500 flex items-center justify-center">
                <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                  <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                </svg>
              </div>
              <div>
                <h3 class="font-bold text-gray-900 dark:text-white mb-1">Pedidos por WhatsApp</h3>
                <p class="text-sm text-gray-600 dark:text-zinc-400">Integración automática con código único</p>
              </div>
            </div>

            <div class="flex items-start gap-4 p-5 bg-gradient-to-br from-purple-50 to-pink-50 dark:from-purple-900/20 dark:to-pink-900/20 rounded-xl border border-purple-200 dark:border-purple-800">
              <div class="flex-shrink-0 w-10 h-10 rounded-lg bg-purple-500 flex items-center justify-center">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                </svg>
              </div>
              <div>
                <h3 class="font-bold text-gray-900 dark:text-white mb-1">Control de Inventario</h3>
                <p class="text-sm text-gray-600 dark:text-zinc-400">Sincronización en tiempo real con tu POS</p>
              </div>
            </div>

            <div class="flex items-start gap-4 p-5 bg-gradient-to-br from-orange-50 to-amber-50 dark:from-orange-900/20 dark:to-amber-900/20 rounded-xl border border-orange-200 dark:border-orange-800">
              <div class="flex-shrink-0 w-10 h-10 rounded-lg bg-orange-500 flex items-center justify-center">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
              </div>
              <div>
                <h3 class="font-bold text-gray-900 dark:text-white mb-1">Vende 24/7</h3>
                <p class="text-sm text-gray-600 dark:text-zinc-400">Tu tienda abierta incluso cuando estás cerrado</p>
              </div>
            </div>
          </div>

          <!-- Plan Badge -->
          <div class="bg-gradient-to-r from-amber-100 to-orange-100 dark:from-amber-900/30 dark:to-orange-900/30 rounded-2xl p-6 border-2 border-amber-300 dark:border-amber-700">
            <div class="flex items-center justify-center gap-3 mb-3">
              <svg class="w-6 h-6 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
              </svg>
              <h3 class="text-lg font-bold text-amber-900 dark:text-amber-300">
                Función Exclusiva Premium
              </h3>
            </div>
            <p class="text-center text-sm text-amber-800 dark:text-amber-400">
              La Tienda Online está disponible para planes <span class="font-bold">Premium</span> y <span class="font-bold">Enterprise</span>
            </p>
          </div>

          <!-- CTA Buttons -->
          <div class="space-y-4">
            <button
              @click="goToUpgrade"
              class="w-full h-14 bg-gradient-to-r from-blue-600 via-purple-600 to-pink-600 hover:from-blue-700 hover:via-purple-700 hover:to-pink-700 text-white font-bold text-lg rounded-xl shadow-2xl transition-all duration-300 flex items-center justify-center gap-3"
            >
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
              </svg>
              Actualizar a Premium
            </button>

            <button
              @click="goBack"
              class="w-full h-12 bg-gray-200 dark:bg-zinc-800 hover:bg-gray-300 dark:hover:bg-zinc-700 text-gray-700 dark:text-zinc-300 font-semibold rounded-xl transition-all duration-200"
            >
              Volver al Dashboard
            </button>
          </div>

          <!-- Support Link -->
          <div class="text-center pt-4 border-t border-gray-200 dark:border-zinc-800">
            <p class="text-sm text-gray-600 dark:text-zinc-400">
              ¿Tienes preguntas sobre los planes? 
              <a href="mailto:soporte@105pos.pro" class="text-blue-600 dark:text-blue-400 hover:underline font-semibold">
                Contáctanos
              </a>
            </p>
          </div>

        </div>

      </div>

      <!-- Additional Info -->
      <div class="mt-8 text-center">
        <p class="text-sm text-gray-500 dark:text-zinc-500">
          💡 <span class="font-semibold">Tip:</span> Con Premium también desbloqueas Multi-sede, IA integrada y más funciones avanzadas
        </p>
      </div>

    </div>
  </div>
</template>

<script setup>
import { defineEmits } from 'vue'
import { useRouter } from 'vue-router'

const emit = defineEmits(['change-module'])
const router = useRouter()

// Go to upgrade page
const goToUpgrade = () => {
  router.push('/upgrade')
}

// Go back to dashboard
const goBack = () => {
  emit('change-module', 'dashboard')
}
</script>

<style scoped>
/* Add any component-specific styles if needed */
</style>
