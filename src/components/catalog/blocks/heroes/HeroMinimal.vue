<template>
  <HeroFocus v-bind="$props" @cta="$emit('cta')" />
</template>
<script setup>
import HeroFocus from './HeroFocus.vue'
defineProps({ headline: String, subheadline: String, backgroundImage: String, palette: Object, fonts: Object, ctaText: String, badge: String, productCount: Number, isMobilePreview: Boolean })
defineEmits(['cta'])
</script>
