<template>
  <HeroSplit v-bind="$props" @cta="$emit('cta')" />
</template>
<script setup>
import HeroSplit from './HeroSplit.vue'
defineProps({ headline: String, subheadline: String, backgroundImage: String, palette: Object, fonts: Object, ctaText: String, isMobilePreview: Boolean, mood: String })
defineEmits(['cta'])
</script>
