// ─── HOOKS / SPOTLIGHTS: Bloques pre-catálogo de alta gama ─────────────────────
// Reemplazan la tarjeta genérica "Brand Story".
// Son secciones editoriales que enamoran al cliente ANTES de la grilla de productos.
// Sin tarjetas con borde, sin rounded-2xl en contenedores grandes. Fondo que se funde con la página.

export { default as HookEditorialStory } from './HookEditorialStory.vue'
// Boutique / Lencería / Alta Costura
// → Texto serif elegante + imagen corte recto, lado a lado en desktop
// → CTA: enlace minimalista border-b, sin botón

export { default as HookUrbanLookbook }  from './HookUrbanLookbook.vue'
// Streetwear / Moda Juvenil / Sneakers
// → Full-bleed imagen + texto superpuesto fondo oscuro
// → Títulos font-black enormous + botón sólido color primario

export { default as HookDynamicBento }   from './HookDynamicBento.vue'
// Deportivo / Tecnología / Consumo
// → Bento grid: imagen grande + celda color sólido + celda foto detalle
// → Estética técnica, sans-serif gruesa, rounded-lg máximo

export { default as HookDarkNoir }       from './HookDarkNoir.vue'
// Alta Costura / Luxury / Tech Premium — Dark Mode nativo
// → Fondo negro puro, imagen asimétrica 60/40, texto blanco + gray-400
// → CTA outline sutil (border-white/20), sin tarjetas ni fondos visibles

export { default as HookTestimonials }   from './HookTestimonials.vue'
// Social Proof / Cualquier vertical — carrusel de reseñas con avatares
// → Tarjetas horizontales scroll, stars + quote + autor, build confianza

export { default as HookCollectionGrid } from './HookCollectionGrid.vue'
// Multi-categoría / Moda / Retail — grid visual de colecciones
// → 2 tall + 2 short asymétrico, hover arrows, collection labels

export { default as HookBrandManifesto } from './HookBrandManifesto.vue'
// Editorial / Premium / Marcas con filosofía — statement tipográfico full-width
// → Headline serif grande, texto manifiesto, firma de marca

// ─── Mapeo para selección por IA ──────────────────────────────────────────────
// 'editorial-story'   → HookEditorialStory   (boutique, costura, belleza premium)
// 'urban-lookbook'    → HookUrbanLookbook    (streetwear, sneakers, moda juvenil)
// 'dynamic-bento'     → HookDynamicBento     (deportivo, tech, consumo masivo)
// 'dark-noir'         → HookDarkNoir         (alta costura noir, lujo oscuro)
// 'testimonials'      → HookTestimonials     (cualquier vertical, social proof)
// 'collection-grid'   → HookCollectionGrid   (multi-categoría, moda, retail)
// 'brand-manifesto'   → HookBrandManifesto   (editorial, premium, marcas con historia)
