<template>
  <div class="min-h-screen bg-gradient-to-b from-gray-50 via-gray-100 to-gray-200 dark:bg-gradient-to-b dark:from-[#141417] dark:via-slate-900 dark:to-[#0a0a0c] flex items-center justify-center p-4">
    <div class="bg-white dark:bg-zinc-900 rounded-2xl shadow-xl p-8 max-w-2xl w-full">
      <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">🔍 DEBUG: Parámetros de Wompi</h1>
      
      <div class="space-y-4 mb-6">
        <div class="bg-gray-50 dark:bg-zinc-800 p-4 rounded-lg">
          <p class="text-xs font-mono text-gray-500 dark:text-zinc-400 uppercase tracking-wide mb-2">URL Completa</p>
          <p class="text-sm font-mono text-gray-900 dark:text-white break-all">{{ fullUrl }}</p>
        </div>

        <div class="bg-gray-50 dark:bg-zinc-800 p-4 rounded-lg">
          <p class="text-xs font-mono text-gray-500 dark:text-zinc-400 uppercase tracking-wide mb-2">Parámetros Detectados</p>
          <pre class="text-xs font-mono text-gray-900 dark:text-white whitespace-pre-wrap">{{ JSON.stringify(params, null, 2) }}</pre>
        </div>

        <div class="bg-blue-50 dark:bg-blue-950 border border-blue-200 dark:border-blue-900 p-4 rounded-lg">
          <p class="text-xs font-mono text-blue-600 dark:text-blue-400 uppercase tracking-wide mb-2">ℹ️ Información</p>
          <ul class="text-xs text-blue-700 dark:text-blue-300 space-y-1">
            <li>✓ Route: {{ $route.path }}</li>
            <li>✓ Route Name: {{ $route.name }}</li>
            <li>✓ Router Instance: {{ !!$router }}</li>
            <li>✓ Timestamp: {{ new Date().toISOString() }}</li>
          </ul>
        </div>
      </div>

      <div class="flex gap-3">
        <button
          @click="copyToClipboard"
          class="flex-1 px-4 py-2 bg-blue-600 dark:bg-blue-700 hover:bg-blue-700 dark:hover:bg-blue-600 text-white text-sm font-bold rounded-lg transition-all duration-200"
        >
          📋 Copiar JSON
        </button>
        <button
          @click="goBack"
          class="flex-1 px-4 py-2 bg-gray-600 dark:bg-gray-700 hover:bg-gray-700 dark:hover:bg-gray-600 text-white text-sm font-bold rounded-lg transition-all duration-200"
        >
          ← Volver
        </button>
      </div>

      <p class="text-xs text-gray-500 dark:text-zinc-400 mt-6">
        Esta página es temporal para DEBUG. Muestra exactamente qué parámetros está enviando Wompi.
      </p>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { useRoute, useRouter } from 'vue-router'

const route = useRoute()
const router = useRouter()

// Capturar URL completa
const fullUrl = computed(() => {
  if (typeof window !== 'undefined') {
    return window.location.href
  }
  return 'N/A'
})

// Todos los parámetros (query + path)
const params = computed(() => ({
  query: { ...route.query },
  params: { ...route.params },
  hash: route.hash,
  fullPath: route.fullPath,
}))

const copyToClipboard = () => {
  const text = JSON.stringify(params.value, null, 2)
  navigator.clipboard.writeText(text).then(() => {
    alert('✅ Copiado al portapapeles')
  })
}

const goBack = () => {
  router.go(-1)
}

// Log agresivo
console.log('🎯 PaymentDebug Componente Cargado')
console.log('📍 URL Completa:', fullUrl.value)
console.log('📦 Query Params:', route.query)
console.log('📦 Route Params:', route.params)
console.log('🔗 Full Path:', route.fullPath)
</script>
