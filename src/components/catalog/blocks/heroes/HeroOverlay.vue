<template>
  <HeroEditorial v-bind="$props" @cta="$emit('cta')" />
</template>
<script setup>
import HeroEditorial from './HeroEditorial.vue'
defineProps({ headline: String, subheadline: String, backgroundImage: String, palette: Object, fonts: Object, ctaText: String, isMobilePreview: Boolean, mood: String, textPosition: String })
defineEmits(['cta'])
</script>
