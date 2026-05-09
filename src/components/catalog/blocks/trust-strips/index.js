// Trust Strips modulares del sistema de catálogo 105POS
// Cada strip es 100% aislado — estructura visual + Tailwind + props de color inyectables por IA

export { default as TrustStripDarkContrast }  from './TrustStripDarkContrast.vue'
// Kharis-inspired — fondo oscuro, texto blanco, fila horizontal scrollable

export { default as TrustStripMinimalBorder } from './TrustStripMinimalBorder.vue'
// Boutique / Zara — blanco puro, borde-y fino, ícono arriba + texto centrado abajo

export { default as TrustStripDivided }       from './TrustStripDivided.vue'
// Catálogo / Cosmética — bg gris sutil, separadores verticales, estructura corporativa

export { default as TrustStripMarquee }       from './TrustStripMarquee.vue'
// Urbano / Streetwear — solo texto corrido, separadores ✦, animación marquee infinita

export { default as TrustStripSoftPills }     from './TrustStripSoftPills.vue'
// Tech / Consumo rápido — pastillas horizontales blancas sobre fondo gris claro

// ─── Mapeo para selección por IA ──────────────────────────────────────────────
// 'dark-contrast'   → TrustStripDarkContrast  (moda premium, Kharis, distribuidoras)
// 'minimal-border'  → TrustStripMinimalBorder  (boutique, zara-style, lujo moderno)
// 'divided'         → TrustStripDivided        (cosmética, catálogos, corporativo)
// 'marquee'         → TrustStripMarquee        (streetwear, urbano, jóvenes)
// 'soft-pills'      → TrustStripSoftPills      (tech, electrónica, consumo rápido)
