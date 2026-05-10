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

// ─── Mapeo para selección por IA ──────────────────────────────────────────────
// 'editorial-story' → HookEditorialStory  (boutique, costura, belleza premium)
// 'urban-lookbook'  → HookUrbanLookbook   (streetwear, sneakers, moda juvenil)
// 'dynamic-bento'   → HookDynamicBento    (deportivo, tech, consumo masivo)
