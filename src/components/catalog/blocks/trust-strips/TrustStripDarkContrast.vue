<template>
  <!-- TRUST STRIP: DARK CONTRAST — Kharis-inspired, alto impacto -->
  <!-- Fondo oscuro pegado al hero. Fila horizontal scrollable en móvil. -->
  <div
    class="w-full overflow-x-auto scrollbar-none"
    :style="{ backgroundColor: bgColor, color: textColor }"
  >
    <div class="flex items-stretch min-w-max lg:min-w-0 lg:grid lg:grid-cols-4 divide-x"
      :style="{ borderColor: textColor + '18' }"
    >
      <div
        v-for="(item, i) in items"
        :key="i"
        class="flex items-center gap-2.5 px-5 py-3 flex-shrink-0 lg:flex-shrink lg:justify-center"
        :style="{ borderColor: textColor + '18' }"
      >
        <!-- Ícono SVG dinámico -->
        <div class="flex-shrink-0 w-5 h-5" :style="{ color: iconColor }">
          <svg v-if="item.icon === 'truck'" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.3">
            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 01-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 00-3.213-9.193 2.056 2.056 0 00-1.58-.86H14.25M16.5 18.75h-2.25m0-11.177v-.958c0-.568-.422-1.048-.987-1.106a48.554 48.554 0 00-10.026 0 1.106 1.106 0 00-.987 1.106v7.635m12-6.677v6.677m0 4.5v-4.5m0 0h-12" />
          </svg>
          <svg v-else-if="item.icon === 'shield'" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.3">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.955 11.955 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" />
          </svg>
          <svg v-else-if="item.icon === 'star'" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.3">
            <path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 00-.182.557l1.285 5.385a.562.562 0 01-.84.61l-4.725-2.885a.563.563 0 00-.586 0L6.982 20.54a.562.562 0 01-.84-.61l1.285-5.386a.562.562 0 00-.182-.557l-4.204-3.602a.562.562 0 01.321-.988l5.518-.442a.563.563 0 00.475-.345L11.48 3.5z" />
          </svg>
          <svg v-else class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.3">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15.182 15.182a4.5 4.5 0 01-6.364 0M21 12a9 9 0 11-18 0 9 9 0 0118 0zM9.75 9.75c0 .414-.168.75-.375.75S9 10.164 9 9.75 9.168 9 9.375 9s.375.336.375.75zm-.375 0h.008v.015h-.008V9.75zm5.625 0c0 .414-.168.75-.375.75s-.375-.336-.375-.75.168-.75.375-.75.375.336.375.75zm-.375 0h.008v.015h-.008V9.75z" />
          </svg>
        </div>

        <!-- Texto -->
        <div class="flex flex-col">
          <span
            class="text-[10px] uppercase font-bold tracking-widest leading-none"
            :style="{ color: textColor }"
          >{{ item.label }}</span>
          <span
            v-if="item.sublabel"
            class="text-[9px] mt-0.5 leading-none"
            :style="{ color: textColor + 'aa' }"
          >{{ item.sublabel }}</span>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
defineProps({
  items: {
    type: Array,
    default: () => [
      { icon: 'truck',  label: 'Envío a todo el país',    sublabel: 'Rápido y seguro' },
      { icon: 'shield', label: 'Pago 100% seguro',         sublabel: 'Encriptado SSL' },
      { icon: 'star',   label: 'Calidad garantizada',      sublabel: 'Productos originales' },
      { icon: 'chat',   label: 'Atención personalizada',   sublabel: 'Lun–Sáb 8am–6pm' },
    ]
  },
  bgColor:   { type: String, default: '#111111' },
  textColor: { type: String, default: '#ffffff' },
  iconColor: { type: String, default: '#ffffff' },
})
</script>

<style scoped>
.scrollbar-none::-webkit-scrollbar { display: none; }
.scrollbar-none { -ms-overflow-style: none; scrollbar-width: none; }
</style>
