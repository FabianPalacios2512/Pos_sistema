<template>
  <!-- Splash Screen Minimalista estilo Alegra -->
  <Transition name="splash-fade">
    <div v-if="visible" class="fixed inset-0 z-[200] bg-gradient-to-b from-gray-50 via-gray-100 to-gray-200 dark:bg-gradient-to-b dark:from-[#141417] dark:via-slate-900 dark:to-[#0a0a0c] flex items-center justify-center">
      
      <div class="flex flex-col items-center justify-center space-y-6">
        
        <!-- Logo limpio sin contenedor -->
        <div 
          class="transform transition-all duration-700 ease-out"
          :class="step >= 1 ? 'translate-y-0 opacity-100' : 'translate-y-8 opacity-0'"
        >
          <img src="/logo.png" alt="105 POS" class="w-20 h-20 object-contain">
        </div>

        <!-- Nombre minimalista -->
        <div 
          class="text-center transform transition-all duration-700 ease-out delay-200"
          :class="step >= 2 ? 'translate-y-0 opacity-100' : 'translate-y-6 opacity-0'"
        >
          <h1 class="text-3xl font-bold text-slate-800 dark:text-white tracking-tight">
            105 POS
          </h1>
        </div>

        <!-- Animación de carga elegante -->
        <div 
          class="transform transition-all duration-500 ease-out delay-400"
          :class="step >= 3 ? 'translate-y-0 opacity-100' : 'translate-y-4 opacity-0'"
        >
          <div class="flex gap-1.5">
            <div 
              v-for="i in 3" 
              :key="i"
              class="w-2 h-2 rounded-full bg-slate-700 dark:bg-slate-400 animate-pulse"
              :style="{ animationDelay: `${i * 150}ms` }"
            ></div>
          </div>
        </div>

      </div>
    </div>
  </Transition>
</template>

<script setup>
import { ref, onMounted } from 'vue'

const visible = ref(true)
const step = ref(0)

onMounted(() => {
  // Step 1: Mostrar logo
  setTimeout(() => {
    step.value = 1
  }, 100)

  // Step 2: Mostrar nombre
  setTimeout(() => {
    step.value = 2
  }, 400)

  // Step 3: Mostrar animación de carga
  setTimeout(() => {
    step.value = 3
  }, 700)
})

// Exponer para control externo (App.vue)
defineExpose({
  hide: () => { visible.value = false }
})

</script>

<style scoped>
/* Transiciones limpias */
.splash-fade-enter-active,
.splash-fade-leave-active {
  transition: opacity 0.4s ease;
}

.splash-fade-enter-from,
.splash-fade-leave-to {
  opacity: 0;
}
</style>
