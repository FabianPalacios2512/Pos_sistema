<template>
  <div class="showcase-root min-h-screen bg-[#F3F0E9]">

    <!-- ═══ HEADER — Transparent + Blur ═══ -->
    <header class="header-blur fixed top-0 left-0 right-0 z-50">
      <div class="max-w-[1280px] mx-auto px-6 lg:px-12">
        <div class="h-[64px] flex items-center justify-between">
          <!-- Logo -->
          <router-link to="/catalog/plantillas" class="flex items-center gap-2.5">
            <img src="/logo.png" alt="105 POS" class="h-7 w-auto" />
            <span class="text-[#111111] text-[13px] font-medium tracking-[0.02em]">105 POS</span>
          </router-link>

          <!-- Nav — desktop -->
          <nav class="hidden md:flex items-center gap-10">
            <a href="https://105pos.pro" target="_blank" class="text-[11px] text-[#777777] uppercase tracking-[0.1em] hover:text-[#111111] transition-colors duration-300">Inicio</a>
            <a href="#" class="text-[11px] text-[#111111] font-semibold uppercase tracking-[0.1em] nav-active">Plantillas</a>
            <a href="https://105pos.pro" target="_blank" class="text-[11px] text-[#777777] uppercase tracking-[0.1em] hover:text-[#111111] transition-colors duration-300">Precios</a>
            <a href="https://wa.me/573001234567" target="_blank" class="text-[11px] text-[#777777] uppercase tracking-[0.1em] hover:text-[#111111] transition-colors duration-300">Soporte</a>
          </nav>

          <!-- CTA -->
          <div class="flex items-center gap-5">
            <a
              href="https://105pos.pro/login"
              target="_blank"
              class="hidden sm:inline-flex text-[11px] text-[#777777] hover:text-[#111111] transition-colors duration-300"
            >
              Iniciar sesión
            </a>
            <a
              href="https://105pos.pro/register"
              target="_blank"
              class="px-5 py-2 bg-[#111111] text-white text-[10px] uppercase tracking-[0.1em] hover:bg-[#000000] transition-colors duration-300"
            >
              Crear tienda gratis
            </a>
          </div>
        </div>
      </div>
    </header>

    <!-- ═══ HERO CAROUSEL ═══ -->
    <section class="pt-[64px] relative overflow-hidden">
      <div class="relative w-full">
        <div class="relative overflow-hidden">
          <div
            class="flex transition-transform duration-700 ease-[cubic-bezier(0.25,0.46,0.45,0.94)]"
            :style="{ transform: `translateX(-${currentSlide * 100}%)` }"
          >
            <div v-for="(slide, i) in heroSlides" :key="i" class="w-full flex-shrink-0">
              <img
                :src="slide.src"
                :alt="slide.alt"
                class="w-full h-[300px] sm:h-[400px] md:h-[520px] lg:h-[600px] object-cover object-center"
              />
            </div>
          </div>
          <!-- Fade-out -->
          <div class="hero-fade-bottom"></div>
        </div>

        <!-- Arrows -->
        <button
          @click="prevSlide"
          class="absolute left-5 md:left-10 top-1/2 -translate-y-1/2 z-10 w-10 h-10 bg-white/60 backdrop-blur-sm flex items-center justify-center shadow-[0_2px_16px_rgba(0,0,0,0.08)] hover:bg-white/90 transition-all duration-300 group"
        >
          <svg class="w-4 h-4 text-[#111111] group-hover:-translate-x-0.5 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5"/></svg>
        </button>
        <button
          @click="nextSlide"
          class="absolute right-5 md:right-10 top-1/2 -translate-y-1/2 z-10 w-10 h-10 bg-white/60 backdrop-blur-sm flex items-center justify-center shadow-[0_2px_16px_rgba(0,0,0,0.08)] hover:bg-white/90 transition-all duration-300 group"
        >
          <svg class="w-4 h-4 text-[#111111] group-hover:translate-x-0.5 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
        </button>

        <!-- Dots -->
        <div class="absolute bottom-6 left-1/2 -translate-x-1/2 flex items-center gap-2 z-10">
          <button
            v-for="(slide, i) in heroSlides"
            :key="'dot-' + i"
            @click="goToSlide(i)"
            class="transition-all duration-300"
            :class="currentSlide === i
              ? 'w-6 h-[3px] bg-white'
              : 'w-[3px] h-[3px] bg-white/40 hover:bg-white/70'"
          ></button>
        </div>
      </div>
    </section>

    <!-- ═══ HERO TEXT ═══ -->
    <section class="pt-16 md:pt-24 pb-16 md:pb-24 px-6 lg:px-12 max-w-[1280px] mx-auto">
      <div class="max-w-2xl">
        <p class="text-[10px] text-[#999999] uppercase tracking-[0.25em] mb-6">Portafolio de plantillas</p>
        <h1 class="font-serif text-[#111111] text-[32px] md:text-[48px] lg:text-[58px] font-normal leading-[1.08] tracking-[-0.02em]">
          Plantillas de E-commerce<br class="hidden md:block"> Diseñadas para Retail de Moda
        </h1>
        <p class="text-[#555555] text-[14px] md:text-[16px] mt-6 md:mt-8 max-w-md leading-[1.6]">
          Sincronización instantánea con tu POS. Diseños editoriales que convierten, listos para tu marca.
        </p>
      </div>
    </section>

    <!-- ═══ FILTER BAR — Texto minimalista ═══ -->
    <section class="px-6 lg:px-12 max-w-[1280px] mx-auto pb-12 md:pb-16">
      <nav class="flex items-center gap-7 md:gap-9 border-b border-[#E0DBD4] pb-4">
        <button
          v-for="filter in filters"
          :key="filter.label"
          @click="activeFilter = filter.value"
          class="filter-text relative text-[11px] uppercase tracking-[0.1em] pb-1 transition-colors duration-300"
          :class="activeFilter === filter.value
            ? 'text-[#111111] font-semibold'
            : 'text-[#777777] hover:text-[#111111]'"
        >
          {{ filter.label }}
          <span
            v-if="activeFilter === filter.value"
            class="absolute left-0 right-0 -bottom-[17px] h-[2px] bg-[#111111]"
          ></span>
        </button>
      </nav>
    </section>

    <!-- ═══ TEMPLATE CARDS ═══ -->
    <section class="px-6 lg:px-12 pb-28 md:pb-40 max-w-[1280px] mx-auto">
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-x-6 gap-y-12 md:gap-x-8 md:gap-y-16">

        <article
          v-for="tmpl in filteredTemplates"
          :key="tmpl.id"
          class="group"
        >
          <!-- ── A. VISUAL (70%) ── -->
          <div class="card-visual relative overflow-hidden rounded-sm bg-[#EEEAE3]">

            <!-- Phone mockup -->
            <div class="flex items-center justify-center py-10 px-8">
              <div class="relative w-[130px] md:w-[150px] flex-shrink-0">
                <div
                  class="relative rounded-[1.6rem] overflow-hidden border-[5px] border-[#111111]"
                  :class="tmpl.phoneShadow"
                >
                  <div class="absolute top-0 left-1/2 -translate-x-1/2 w-[56px] h-[16px] bg-[#111111] rounded-b-lg z-10"></div>
                  <div class="aspect-[9/19.5] overflow-hidden bg-white">
                    <img
                      :src="tmpl.screenshot"
                      :alt="tmpl.name"
                      class="w-full h-full object-cover object-top transition-transform duration-700 ease-out group-hover:scale-[1.06]"
                      loading="lazy"
                    />
                  </div>
                </div>
              </div>
            </div>

            <!-- Badge -->
            <div class="absolute top-4 left-4">
              <span class="badge-micro" :class="tmpl.badgeClass">{{ tmpl.badge }}</span>
            </div>

            <!-- Hover overlay — "Ver demo" -->
            <div class="card-overlay absolute inset-0 flex items-end justify-center pb-7">
              <router-link
                :to="'/catalog/preview/' + tmpl.id"
                class="btn-demo"
              >
                Ver demo
              </router-link>
            </div>
          </div>

          <!-- ── B. INFO (minimal) ── -->
          <div class="pt-4">
            <h3 class="font-serif text-[#111111] text-[18px] md:text-[20px] font-normal leading-tight tracking-[-0.01em]">{{ tmpl.name }}</h3>
            <p class="text-[#888888] text-[12px] mt-1 leading-snug">{{ tmpl.tagline }}</p>

            <!-- Tags -->
            <div class="flex flex-wrap gap-1.5 mt-3">
              <span v-for="tag in tmpl.tags" :key="tag" class="tag-micro">{{ tag }}</span>
            </div>

            <!-- ── C. ACCIONES ── -->
            <div class="flex items-center gap-2.5 mt-5">
              <router-link
                :to="'/catalog/preview/' + tmpl.id"
                class="btn-primary flex-1 text-center"
              >
                Ver demo
              </router-link>
              <a
                href="https://105pos.pro/register"
                target="_blank"
                class="btn-outline flex-1 text-center"
              >
                Usar plantilla
              </a>
            </div>
          </div>
        </article>

      </div>
    </section>

    <!-- ═══ CTA ═══ -->
    <section class="px-6 lg:px-12 py-20 md:py-28 bg-[#111111] text-center">
      <p class="text-[10px] text-white/30 uppercase tracking-[0.25em] mb-5">Empieza hoy</p>
      <h3 class="font-serif text-white text-[26px] md:text-[40px] font-normal leading-[1.1]">¿Listo para lanzar tu tienda?</h3>
      <p class="text-white/40 text-[14px] mt-5 max-w-sm mx-auto leading-[1.6]">
        Crea tu cuenta gratis, carga tus productos y elige tu plantilla. Tu e-commerce estará listo en minutos.
      </p>
      <a
        href="https://105pos.pro/register"
        target="_blank"
        class="inline-block mt-10 px-8 py-3 bg-white text-[#111111] text-[10px] uppercase tracking-[0.1em] font-medium hover:bg-[#F3F0E9] transition-colors duration-300"
      >
        Empezar gratis
      </a>
    </section>

    <!-- ═══ FOOTER ═══ -->
    <footer class="bg-[#F3F0E9] py-10 text-center">
      <p class="text-[10px] text-[#AAAAAA] tracking-[0.08em]">© {{ new Date().getFullYear() }} 105 POS — Todos los derechos reservados</p>
    </footer>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'

// ── Hero Carousel ──
const heroSlides = [
  { src: '/img-plantillas/hero1.png', alt: 'Plantillas e-commerce de moda – Vista multi-dispositivo' },
  { src: '/img-plantillas/hero2.png', alt: 'Plantillas e-commerce streetwear – Vista multi-dispositivo' }
]
const currentSlide = ref(0)
let slideInterval = null

const nextSlide = () => { currentSlide.value = (currentSlide.value + 1) % heroSlides.length }
const prevSlide = () => { currentSlide.value = (currentSlide.value - 1 + heroSlides.length) % heroSlides.length }
const goToSlide = (i) => { currentSlide.value = i; resetAutoplay() }

function resetAutoplay() {
  clearInterval(slideInterval)
  slideInterval = setInterval(nextSlide, 5000)
}

onMounted(() => { slideInterval = setInterval(nextSlide, 5000) })
onUnmounted(() => { clearInterval(slideInterval) })

// ── Filters ──
const activeFilter = ref('all')

const filters = [
  { label: 'Todas', value: 'all' },
  { label: 'Premium', value: 'premium' },
  { label: 'Urbano', value: 'urbano' },
  { label: 'Editorial', value: 'editorial' },
  { label: 'Novedad', value: 'nuevo' },
]

const templates = [
  {
    id: 'visual-story',
    name: 'Luxe Editorial',
    tagline: 'Moda editorial con alto impacto visual',
    badge: 'Premium',
    badgeClass: 'bg-[#C5955A]/10 text-[#B08545] border border-[#C5955A]/20',
    tags: ['Editorial', 'Serif', 'Premium'],
    categories: ['premium', 'editorial'],
    phoneShadow: 'shadow-[0_20px_60px_-15px_rgba(0,0,0,0.25)]',
    screenshot: 'https://images.unsplash.com/photo-1469334031218-e382a71b716b?w=600&h=1300&fit=crop&crop=top&q=85'
  },
  {
    id: 'urban-street',
    name: 'Urban Drop',
    tagline: 'Streetwear bold con estética dark urbana',
    badge: 'Nuevo',
    badgeClass: 'bg-[#8B6FC0]/10 text-[#7A5FB0] border border-[#8B6FC0]/20',
    tags: ['Urbano', 'Dark', 'Bold'],
    categories: ['urbano', 'nuevo'],
    phoneShadow: 'shadow-[0_20px_60px_-15px_rgba(0,0,0,0.35)]',
    screenshot: 'https://images.unsplash.com/photo-1523398002811-999ca8dec234?w=600&h=1300&fit=crop&crop=top&q=85'
  },
  {
    id: 'minimal-studio',
    name: 'Minimal Studio',
    tagline: 'Minimalismo limpio para marcas de autor',
    badge: 'Editorial',
    badgeClass: 'bg-[#4A7C6F]/10 text-[#3D6B5F] border border-[#4A7C6F]/20',
    tags: ['Minimal', 'Clean', 'Neutro'],
    categories: ['editorial'],
    phoneShadow: 'shadow-[0_20px_60px_-15px_rgba(0,0,0,0.20)]',
    screenshot: 'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=600&h=1300&fit=crop&crop=top&q=85'
  },
  {
    id: 'street-capsule',
    name: 'Street Capsule',
    tagline: 'Colecciones cápsula con grilla de impacto',
    badge: 'Nuevo',
    badgeClass: 'bg-[#8B6FC0]/10 text-[#7A5FB0] border border-[#8B6FC0]/20',
    tags: ['Cápsula', 'Grid', 'Urbano'],
    categories: ['urbano', 'nuevo'],
    phoneShadow: 'shadow-[0_20px_60px_-15px_rgba(0,0,0,0.30)]',
    screenshot: 'https://images.unsplash.com/photo-1515886657613-9f3515b0c78f?w=600&h=1300&fit=crop&crop=top&q=85'
  }
]

const filteredTemplates = computed(() => {
  if (activeFilter.value === 'all') return templates
  return templates.filter(t => t.categories.includes(activeFilter.value))
})
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,500;1,400&display=swap');

/* ── Serif ── */
.showcase-root .font-serif {
  font-family: 'Playfair Display', Georgia, serif;
}

/* ── Header — transparent + blur ── */
.header-blur {
  background: rgba(243, 240, 233, 0.6);
  backdrop-filter: blur(10px);
  -webkit-backdrop-filter: blur(10px);
  border-bottom: 1px solid rgba(0, 0, 0, 0.04);
}

/* ── Nav active underline ── */
.nav-active {
  position: relative;
}
.nav-active::after {
  content: '';
  position: absolute;
  left: 0;
  right: 0;
  bottom: -4px;
  height: 1.5px;
  background: #111111;
}

/* ── Hero fade ── */
.hero-fade-bottom {
  position: absolute;
  bottom: 0;
  left: 0;
  right: 0;
  height: 160px;
  background: linear-gradient(to bottom, transparent 0%, #F3F0E9 100%);
  pointer-events: none;
  z-index: 2;
}

/* ── Card visual zone ── */
.card-visual {
  cursor: pointer;
  transition: transform 0.4s ease;
}
.card-visual:hover {
  transform: translateY(-4px);
}

/* ── Hover overlay ── */
.card-overlay {
  background: rgba(17, 17, 17, 0);
  transition: background 0.4s ease;
  pointer-events: none;
}
.group:hover .card-overlay {
  background: rgba(17, 17, 17, 0.18);
  pointer-events: auto;
}

/* ── Demo button (overlay) ── */
.btn-demo {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  padding: 10px 24px;
  background: #FFFFFF;
  color: #111111;
  font-size: 9px;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.12em;
  border: none;
  opacity: 0;
  transform: translateY(8px);
  transition: opacity 0.3s ease, transform 0.3s ease;
  white-space: nowrap;
  text-decoration: none;
}
.group:hover .btn-demo {
  opacity: 1;
  transform: translateY(0);
}

/* ── Badge micro ── */
.badge-micro {
  display: inline-block;
  padding: 4px 9px;
  font-size: 8px;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.12em;
  border-radius: 2px;
}

/* ── Tag micro ── */
.tag-micro {
  display: inline-block;
  padding: 3px 9px;
  font-size: 9px;
  font-weight: 500;
  text-transform: uppercase;
  letter-spacing: 0.1em;
  color: #999999;
  background: transparent;
  border: 1px solid #E0DBD4;
  border-radius: 2px;
}

/* ── Buttons — geometría estricta (rectos) ── */
.btn-primary {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  padding: 10px 20px;
  background: #111111;
  color: #FFFFFF;
  font-size: 9px;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.1em;
  border: none;
  transition: background 0.3s ease;
  text-decoration: none;
  white-space: nowrap;
}
.btn-primary:hover {
  background: #000000;
}

.btn-outline {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  padding: 9px 20px;
  background: transparent;
  color: #111111;
  font-size: 9px;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.1em;
  border: 1px solid #CCCCCC;
  transition: border-color 0.3s ease;
  text-decoration: none;
  white-space: nowrap;
}
.btn-outline:hover {
  border-color: #111111;
}
</style>
