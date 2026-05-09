// Headers modulares del sistema de catálogo 105POS
// Cada header es 100% aislado — solo estructura visual + Tailwind CSS

export { default as HeaderEditorialCenter } from './HeaderEditorialCenter.vue'
// Alta moda / Boutique — logo centrado serif, hamburger izq, iconos der, borde sutil

export { default as HeaderRetailLeft }      from './HeaderRetailLeft.vue'
// Consumo masivo / Minimarket — logo izquierda, iconos der, borde sólido funcional

export { default as HeaderTransparentGlass } from './HeaderTransparentGlass.vue'
// Cosmética / Heroes visuales — transparente + glassmorphism al scroll (fixed)

export { default as HeaderRetailOverlay }   from './HeaderRetailOverlay.vue'
// Streetwear / Urbano / Premium — transparente sobre hero, sólido al scroll, full-width

export { default as HeaderUtilitySearch }   from './HeaderUtilitySearch.vue'
// Ferreterías / Catálogos grandes — 2 filas, barra de búsqueda full-width integrada

export { default as HeaderCenteredSerif }   from './HeaderCenteredSerif.vue'
// Distribuidoras / Boutiques — nombre centrado serif + subtítulo, Kharis-inspired

// ─── Mapeo para selección por IA ─────────────────────────────────────────────
// 'editorial-center'   → HeaderEditorialCenter  (moda premium, boutique)
// 'retail-left'        → HeaderRetailLeft        (masivo, funcional, minimarket)
// 'transparent-glass'  → HeaderTransparentGlass  (cosmética, lifestyle, foto fuerte)
// 'floating-pill'      → HeaderRetailOverlay      (streetwear, urbano, premium overlay)
// 'utility-search'     → HeaderUtilitySearch      (ferretería, electrónica, catálogo)
// 'centered-serif'     → HeaderCenteredSerif      (distribuidora, boutique, Kharis-style)
