// Headers modulares del sistema de catálogo 105POS
// Cada header es 100% aislado — solo estructura visual + Tailwind CSS

export { default as HeaderEditorialCenter } from './HeaderEditorialCenter.vue'
// Alta moda / Boutique — logo centrado serif, hamburger izq, iconos der, borde sutil

export { default as HeaderRetailLeft }      from './HeaderRetailLeft.vue'
// Consumo masivo / Minimarket — logo izquierda, iconos der, borde sólido funcional

export { default as HeaderTransparentGlass } from './HeaderTransparentGlass.vue'
// Cosmética / Heroes visuales — transparente + glassmorphism al scroll (fixed)

export { default as HeaderFloatingPill }    from './HeaderFloatingPill.vue'
// Streetwear / Urbano / Tech — píldora flotante, sombra fuerte, no toca bordes

export { default as HeaderUtilitySearch }   from './HeaderUtilitySearch.vue'
// Ferreterías / Catálogos grandes — 2 filas, barra de búsqueda full-width integrada

// ─── Mapeo para selección por IA ─────────────────────────────────────────────
// 'editorial-center'   → HeaderEditorialCenter  (moda premium, boutique)
// 'retail-left'        → HeaderRetailLeft        (masivo, funcional, minimarket)
// 'transparent-glass'  → HeaderTransparentGlass  (cosmética, lifestyle, foto fuerte)
// 'floating-pill'      → HeaderFloatingPill       (streetwear, tech, urbano)
// 'utility-search'     → HeaderUtilitySearch      (ferretería, electrónica, catálogo)
