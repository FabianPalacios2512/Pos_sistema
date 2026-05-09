<template>
  <!-- TRUST STRIP: MINIMAL BORDER — Boutique / Zara-style -->
  <!-- Fondo blanco/transparente, borde superior e inferior. Ícono arriba, texto centrado abajo. -->
  <div
    class="w-full border-y"
    :style="{ backgroundColor: bgColor, borderColor: borderColor }"
  >
    <div class="flex items-stretch justify-between overflow-x-auto scrollbar-none">
      <div
        v-for="(item, i) in items"
        :key="i"
        class="flex flex-col items-center justify-center gap-1.5 flex-1 min-w-[80px] px-3 py-3.5"
      >
        <!-- Ícono -->
        <div class="w-5 h-5 flex items-center justify-center" :style="{ color: iconColor }">
          <svg v-if="item.icon === 'truck'" class="w-[18px] h-[18px]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.3">
            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 01-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 00-3.213-9.193 2.056 2.056 0 00-1.58-.86H14.25M16.5 18.75h-2.25m0-11.177v-.958c0-.568-.422-1.048-.987-1.106a48.554 48.554 0 00-10.026 0 1.106 1.106 0 00-.987 1.106v7.635m12-6.677v6.677m0 4.5v-4.5m0 0h-12" />
          </svg>
          <svg v-else-if="item.icon === 'shield'" class="w-[18px] h-[18px]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.3">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.955 11.955 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" />
          </svg>
          <svg v-else-if="item.icon === 'star'" class="w-[18px] h-[18px]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.3">
            <path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 00-.182.557l1.285 5.385a.562.562 0 01-.84.61l-4.725-2.885a.563.563 0 00-.586 0L6.982 20.54a.562.562 0 01-.84-.61l1.285-5.386a.562.562 0 00-.182-.557l-4.204-3.602a.562.562 0 01.321-.988l5.518-.442a.563.563 0 00.475-.345L11.48 3.5z" />
          </svg>
          <svg v-else class="w-[18px] h-[18px]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.3">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15.182 15.182a4.5 4.5 0 01-6.364 0M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
        </div>

        <!-- Texto centrado -->
        <span
          class="text-[9px] uppercase font-bold tracking-widest text-center leading-tight"
          :style="{ color: textColor }"
        >{{ item.label }}</span>
      </div>
    </div>
  </div>
</template>

<script setup>
defineProps({
  items: {
    type: Array,
    default: () => [
      { icon: 'truck',  label: 'Envío gratis' },
      { icon: 'shield', label: 'Pago seguro' },
      { icon: 'star',   label: 'Garantía' },
      { icon: 'chat',   label: 'Soporte' },
    ]
  },
  bgColor:     { type: String, default: '#ffffff' },
  textColor:   { type: String, default: '#111111' },
  iconColor:   { type: String, default: '#111111' },
  borderColor: { type: String, default: '#e5e7eb' },
})
</script>

<style scoped>
.scrollbar-none::-webkit-scrollbar { display: none; }
.scrollbar-none { -ms-overflow-style: none; scrollbar-width: none; }
</style>
