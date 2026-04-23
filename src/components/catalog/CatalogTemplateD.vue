<template>
  <!-- PLANTILLA D: "URBAN STREETWEAR" – Estilo Stüssy / Carhartt WIP / ZARA Streetwear -->
  <div class="catalog-urban relative overflow-x-hidden min-h-screen bg-[#fafafa]" :style="themeVars">

    <!-- ═══════════════════════════════════════════════════════════
         HEADER: Barra superior negra con nav limpio
         ═══════════════════════════════════════════════════════════ -->
    <header class="fixed top-0 left-0 right-0 z-50 bg-[#0a0a0a] transition-all duration-300" style="box-shadow: 0 1px 0 rgba(255,255,255,0.06);">
      <!-- Top micro-bar: envío / promos -->
      <div class="h-7 flex items-center justify-center overflow-hidden border-b border-white/[0.06]">
        <TransitionGroup name="ticker-urban" tag="div" class="relative w-full h-full">
          <span
            :key="currentAnnouncement"
            class="absolute inset-0 flex items-center justify-center text-[10px] font-medium tracking-[0.14em] uppercase text-neutral-400 whitespace-nowrap"
          >
            {{ currentAnnouncementText }}
          </span>
        </TransitionGroup>
      </div>

      <!-- Main header row -->
      <div class="w-full px-4 lg:px-10 h-14 lg:h-16 flex items-center justify-between">
        <!-- Left: Hamburger mobile -->
        <div class="flex items-center w-[60px]">
          <button @click="showMobileMenu = !showMobileMenu" class="lg:hidden w-10 h-10 flex items-center justify-center text-white/80 hover:text-white transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
              <path stroke-linecap="round" d="M4 7h16M4 12h10M4 17h16" />
            </svg>
          </button>
        </div>

        <!-- Center: Store Name — Sans-serif bold, uppercase, tracked -->
        <div class="flex-1 flex items-center justify-center lg:justify-start lg:flex-none lg:absolute lg:left-1/2 lg:-translate-x-1/2">
          <h1
            class="text-[18px] lg:text-[22px] text-white font-bold uppercase leading-none"
            style="font-family: 'Inter', 'Helvetica Neue', Arial, sans-serif; letter-spacing: 0.12em;"
          >
            {{ storeName }}
          </h1>
        </div>

        <!-- Right: Search + Cart -->
        <div class="flex items-center gap-1 w-[60px] justify-end">
          <button @click="showMobileSearch = !showMobileSearch" class="w-10 h-10 flex items-center justify-center text-white/70 hover:text-white transition-colors">
            <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
              <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
            </svg>
          </button>
          <button @click="router.push('/catalog/bolsa')" class="relative w-10 h-10 flex items-center justify-center text-white/70 hover:text-white transition-colors">
            <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
              <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993l1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 01-1.12-1.243l1.264-12A1.125 1.125 0 015.513 7.5h12.974c.576 0 1.059.435 1.119 1.007zM8.625 10.5a.375.375 0 11-.75 0 .375.375 0 01.75 0zm7.5 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
            </svg>
            <span
              v-if="cartCount > 0"
              class="absolute -top-0.5 -right-0.5 bg-white text-[#0a0a0a] text-[8px] font-bold min-w-[16px] h-[16px] rounded-full flex items-center justify-center"
            >
              {{ cartCount }}
            </span>
          </button>
        </div>
      </div>

      <!-- Desktop search bar (inline) -->
      <div class="hidden lg:flex justify-center border-t border-white/[0.06] py-2.5 px-10">
        <div class="relative w-full max-w-md">
          <input
            v-model="searchQuery"
            type="text"
            placeholder="Buscar en el catálogo..."
            class="w-full h-9 pl-9 pr-4 bg-white/[0.06] border border-white/[0.08] rounded text-[13px] text-white placeholder-neutral-500 outline-none transition-all focus:border-white/20 focus:bg-white/[0.08]"
          />
          <svg class="w-3.5 h-3.5 text-neutral-500 absolute left-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
          </svg>
        </div>
      </div>

      <!-- Mobile search expandible -->
      <Transition name="slide-down">
        <div v-if="showMobileSearch" class="lg:hidden px-4 py-3 bg-[#0a0a0a] border-t border-white/[0.06]">
          <div class="relative">
            <input
              v-model="searchQuery"
              type="text"
              placeholder="¿Qué buscas hoy?"
              class="w-full h-11 pl-10 pr-10 bg-white/[0.08] border border-white/[0.1] rounded-lg text-sm text-white placeholder-neutral-500 outline-none"
              autofocus
            />
            <svg class="w-4 h-4 text-neutral-500 absolute left-3.5 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
              <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
            <button @click="showMobileSearch = false" class="absolute right-3 top-1/2 -translate-y-1/2 text-neutral-500 hover:text-white transition-colors">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
          </div>
        </div>
      </Transition>
    </header>

    <!-- ═══════════════════════════════════════════════════════════
         HERO: Full-bleed campaign imagery, brutalist typography
         ═══════════════════════════════════════════════════════════ -->
    <section class="relative w-full overflow-hidden mt-[77px] lg:mt-[109px] h-[75vh] min-h-[420px] md:h-[80vh] md:min-h-[540px]">
      <!-- Carousel -->
      <div class="absolute inset-0">
        <TransitionGroup name="fade-slide">
          <div
            v-for="(image, index) in carouselImages"
            :key="index"
            v-show="currentSlide === index"
            class="absolute inset-0"
          >
            <img
              :src="image"
              alt="Campaign"
              class="w-full h-full object-cover will-change-transform transition-transform duration-[12000ms] ease-out"
              :class="currentSlide === index ? 'scale-105' : 'scale-100'"
            />
            <!-- Dark overlay: editorial contrast -->
            <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/30 to-black/10"></div>
          </div>
        </TransitionGroup>
      </div>

      <!-- Hero content: left-aligned brutalist -->
      <div class="absolute inset-0 z-10 flex flex-col items-start justify-end px-6 md:px-12 lg:px-20 pb-12 md:pb-20">
        <div class="inline-flex items-center gap-2 mb-4">
          <span class="w-8 h-[1px] bg-white/50"></span>
          <span class="text-[10px] text-white/70 font-medium uppercase tracking-[0.25em]">Nueva temporada</span>
        </div>
        <h2
          class="text-white text-[42px] md:text-[64px] lg:text-[80px] font-black uppercase leading-[0.92] max-w-[10ch] md:max-w-[12ch]"
          style="font-family: 'Inter', 'Helvetica Neue', Arial, sans-serif; letter-spacing: -0.03em;"
        >
          {{ heroHeadlineDisplay }}
        </h2>
        <p
          class="text-white/60 text-[11px] md:text-sm font-normal tracking-[0.06em] mt-4 max-w-[360px]"
          style="font-family: 'Inter', sans-serif;"
        >
          {{ heroSubheadlineDisplay }}
        </p>
        <button
          @click="scrollToProducts"
          class="mt-6 md:mt-8 px-8 py-3.5 bg-white text-[#0a0a0a] text-[11px] font-bold uppercase tracking-[0.18em] transition-all duration-300 hover:bg-neutral-200 hover:tracking-[0.22em]"
        >
          {{ bannerCtaText || 'COMPRAR AHORA' }}
        </button>
      </div>

      <!-- Slide indicators: minimal lines -->
      <div class="absolute bottom-5 right-6 md:right-12 z-20 flex gap-1.5">
        <button
          v-for="(img, index) in carouselImages.length || 3"
          :key="index"
          @click="currentSlide = index"
          class="h-[2px] rounded-full transition-all duration-500"
          :class="currentSlide === index ? 'bg-white w-10' : 'bg-white/30 w-4'"
        ></button>
      </div>
    </section>

    <!-- ═══════════════════════════════════════════════════════════
         TRUST BAR: Clean horizontal strip
         ═══════════════════════════════════════════════════════════ -->
    <div class="bg-white border-b border-neutral-100">
      <div class="flex items-center justify-center gap-8 md:gap-16 px-4 py-4 overflow-x-auto scrollbar-hide">
        <template v-if="storeConfig.ai_value_messages && storeConfig.ai_value_messages.length > 0">
          <div v-for="(msg, i) in storeConfig.ai_value_messages.slice(0, 3)" :key="'trust-'+i" class="flex items-center gap-2.5 flex-shrink-0">
            <svg class="w-4 h-4 text-neutral-400 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
              <path v-if="i === 0" stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
              <path v-else-if="i === 1" stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 01-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 00-3.213-9.193 2.056 2.056 0 00-1.58-.86H14.25M16.5 18.75h-2.25m0-11.177v-.958c0-.568-.422-1.048-.987-1.106a48.554 48.554 0 00-10.026 0 1.106 1.106 0 00-.987 1.106v7.635m12-6.677v6.677m0 4.5v-4.5m0 0h-12" />
              <path v-else stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
            </svg>
            <span class="text-[11px] text-neutral-500 font-medium whitespace-nowrap tracking-wide">{{ msg }}</span>
          </div>
        </template>
        <template v-else>
          <div class="flex items-center gap-2.5 flex-shrink-0">
            <svg class="w-4 h-4 text-neutral-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 01-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 00-3.213-9.193 2.056 2.056 0 00-1.58-.86H14.25M16.5 18.75h-2.25m0-11.177v-.958c0-.568-.422-1.048-.987-1.106a48.554 48.554 0 00-10.026 0 1.106 1.106 0 00-.987 1.106v7.635m12-6.677v6.677m0 4.5v-4.5m0 0h-12" /></svg>
            <span class="text-[11px] text-neutral-500 font-medium whitespace-nowrap">Envío a toda Colombia</span>
          </div>
          <div class="flex items-center gap-2.5 flex-shrink-0">
            <svg class="w-4 h-4 text-neutral-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            <span class="text-[11px] text-neutral-500 font-medium whitespace-nowrap">Calidad garantizada</span>
          </div>
          <div class="flex items-center gap-2.5 flex-shrink-0">
            <svg class="w-4 h-4 text-neutral-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" /></svg>
            <span class="text-[11px] text-neutral-500 font-medium whitespace-nowrap">Pago seguro</span>
          </div>
        </template>
      </div>
    </div>

    <!-- ═══════════════════════════════════════════════════════════
         CATEGORY PILLS: Horizontal scroll, pill-shaped filters
         ═══════════════════════════════════════════════════════════ -->
    <div class="bg-white border-b border-neutral-100 sticky top-[77px] lg:top-[109px] z-40">
      <div class="flex items-center gap-2 px-4 lg:px-10 py-3 overflow-x-auto scrollbar-hide">
        <button
          @click="selectedCategory = null"
          class="flex-shrink-0 px-4 py-2 text-[11px] font-semibold uppercase tracking-[0.1em] rounded-full border transition-all duration-200"
          :class="selectedCategory === null
            ? 'bg-[#0a0a0a] text-white border-[#0a0a0a]'
            : 'bg-transparent text-neutral-500 border-neutral-200 hover:border-neutral-400 hover:text-neutral-800'"
        >
          Todo
        </button>
        <button
          v-for="cat in categories"
          :key="cat.id"
          @click="selectedCategory = cat.id"
          class="flex-shrink-0 px-4 py-2 text-[11px] font-semibold uppercase tracking-[0.1em] rounded-full border transition-all duration-200"
          :class="selectedCategory === cat.id
            ? 'bg-[#0a0a0a] text-white border-[#0a0a0a]'
            : 'bg-transparent text-neutral-500 border-neutral-200 hover:border-neutral-400 hover:text-neutral-800'"
        >
          {{ cat.name }}
        </button>

        <!-- Separator -->
        <div class="w-px h-5 bg-neutral-200 flex-shrink-0 mx-1"></div>

        <!-- Mobile filter + sort buttons -->
        <button
          @click="showMobileFilters = true"
          class="lg:hidden flex-shrink-0 px-3 py-2 text-[11px] font-semibold uppercase tracking-[0.08em] text-neutral-500 border border-neutral-200 rounded-full flex items-center gap-1.5 hover:border-neutral-400 transition-colors"
        >
          <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6h9.75M10.5 6a1.5 1.5 0 11-3 0m3 0a1.5 1.5 0 10-3 0M3.75 6H7.5m3 12h9.75m-9.75 0a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m-3.75 0H7.5m9-6h3.75m-3.75 0a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m-9.75 0h9.75" />
          </svg>
          Filtros
        </button>
        <button
          @click="showSortModal = true"
          class="lg:hidden flex-shrink-0 px-3 py-2 text-[11px] font-semibold uppercase tracking-[0.08em] text-neutral-500 border border-neutral-200 rounded-full flex items-center gap-1.5 hover:border-neutral-400 transition-colors"
        >
          <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3 7.5L7.5 3m0 0L12 7.5M7.5 3v13.5m13.5 0L16.5 21m0 0L12 16.5m4.5 4.5V7.5" />
          </svg>
          Ordenar
        </button>
      </div>
    </div>

    <!-- ═══════════════════════════════════════════════════════════
         MAIN CONTENT: Sidebar (desktop) + Product Grid
         ═══════════════════════════════════════════════════════════ -->
    <div class="flex">
      <!-- Desktop Sidebar -->
      <aside v-if="!isMobilePreview" class="hidden lg:block w-[240px] flex-shrink-0 px-6 py-8 border-r border-neutral-100 bg-white sticky top-[145px] self-start h-[calc(100vh-145px)] overflow-y-auto">
        <!-- Price filter -->
        <div class="mb-8">
          <h3 class="text-[10px] font-bold text-neutral-400 mb-4 uppercase tracking-[0.2em]">Precio</h3>
          <div class="space-y-3">
            <div class="relative h-1 bg-neutral-200 rounded-full">
              <div
                class="absolute left-0 h-full bg-[#0a0a0a] rounded-full transition-all"
                :style="{ width: ((priceRange.max - minProductPrice) / (maxProductPrice - minProductPrice)) * 100 + '%' }"
              ></div>
              <input
                type="range"
                :min="minProductPrice"
                :max="maxProductPrice"
                v-model.number="priceRange.max"
                class="absolute w-full h-1 appearance-none bg-transparent cursor-pointer"
              />
            </div>
            <div class="flex items-center justify-between text-xs">
              <span class="text-neutral-400">{{ storeConfig.currency_symbol }}0</span>
              <span class="font-semibold text-neutral-800">Hasta {{ storeConfig.currency_symbol }}{{ formatPrice(priceRange.max) }}</span>
            </div>
          </div>
        </div>

        <!-- Sort -->
        <div class="mb-8">
          <h3 class="text-[10px] font-bold text-neutral-400 mb-4 uppercase tracking-[0.2em]">Ordenar</h3>
          <div class="space-y-2">
            <button
              v-for="opt in sortOptions"
              :key="opt.value"
              @click="sortOrder = opt.value"
              class="w-full text-left py-2 text-[13px] transition-colors flex items-center gap-2"
              :class="sortOrder === opt.value ? 'text-[#0a0a0a] font-semibold' : 'text-neutral-400 hover:text-neutral-700'"
            >
              <span class="w-1 h-1 rounded-full transition-colors" :class="sortOrder === opt.value ? 'bg-[#0a0a0a]' : 'bg-transparent'"></span>
              {{ opt.label }}
            </button>
          </div>
        </div>

        <!-- Stock filter -->
        <div class="mb-8">
          <label class="flex items-center gap-2.5 cursor-pointer group">
            <div class="relative">
              <input
                type="checkbox"
                v-model="showOnlyAvailable"
                class="peer h-4 w-4 cursor-pointer appearance-none rounded border border-neutral-300 transition-all checked:bg-[#0a0a0a] checked:border-[#0a0a0a]"
              />
              <svg class="absolute w-3 h-3 text-white opacity-0 peer-checked:opacity-100 top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 pointer-events-none" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
              </svg>
            </div>
            <span class="text-[13px] text-neutral-500 group-hover:text-neutral-800 transition-colors">Solo con stock</span>
          </label>
        </div>

        <!-- Clear filters -->
        <button
          v-if="hasActiveFilters"
          @click="clearFilters"
          class="w-full py-2.5 text-[11px] font-bold uppercase tracking-[0.12em] text-neutral-500 border border-neutral-200 rounded hover:bg-neutral-50 hover:text-neutral-800 transition-all"
        >
          Limpiar filtros
        </button>
      </aside>

      <!-- Product area -->
      <section ref="productsSection" class="flex-1 min-w-0 px-4 lg:px-8 py-6 lg:py-8">
        <!-- Products header -->
        <div class="flex items-end justify-between mb-6">
          <div>
            <h3
              class="text-[22px] md:text-[28px] font-bold text-[#0a0a0a] uppercase leading-tight"
              style="font-family: 'Inter', sans-serif; letter-spacing: -0.01em;"
            >
              {{ selectedCategory !== null ? activeCategoryName : 'Catálogo' }}
            </h3>
            <p class="text-[12px] text-neutral-400 mt-1 tracking-wide">
              {{ filteredProducts.length }} producto{{ filteredProducts.length !== 1 ? 's' : '' }}
              <span v-if="hasActiveFilters" class="text-[#0a0a0a] font-semibold"> · filtrado</span>
            </p>
          </div>
          <button
            v-if="hasActiveFilters"
            @click="clearFilters"
            class="text-[11px] font-semibold uppercase tracking-[0.1em] text-neutral-500 hover:text-[#0a0a0a] transition-colors underline underline-offset-4 decoration-neutral-300"
          >
            Reset
          </button>
        </div>

        <!-- ═══ PRODUCT GRID: 2 cols mobile, 3 tablet, 4 desktop ═══ -->
        <div :class="gridClasses">
          <TransitionGroup name="list">
            <div
              v-for="product in filteredProducts"
              :key="product.id"
              class="group"
            >
              <div
                class="bg-white overflow-hidden h-full flex flex-col cursor-pointer transition-all duration-300 hover:shadow-[0_8px_30px_rgba(0,0,0,0.08)]"
                @click="openProductDetails(product)"
              >
                <!-- Image: 3:4 ratio -->
                <div class="relative aspect-[3/4] overflow-hidden bg-neutral-100">
                  <img
                    v-if="getProductImage(product) && !imageErrors[product.id]"
                    :src="getProductImage(product)"
                    :alt="product.name"
                    @error="handleImageError(product.id)"
                    class="w-full h-full object-cover transition-transform duration-700 ease-out group-hover:scale-[1.04]"
                  />
                  <div v-else class="w-full h-full flex items-center justify-center bg-neutral-100">
                    <svg class="w-10 h-10 text-neutral-300" viewBox="0 0 24 24" fill="none">
                      <path d="M5 9h14l1 12H4L5 9z" fill="currentColor" opacity="0.2"/>
                      <path d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round" fill="none"/>
                    </svg>
                  </div>

                  <!-- Quick add button -->
                  <button
                    @click.stop="addToCart(product)"
                    :disabled="product.stock === 0"
                    class="absolute bottom-3 right-3 w-9 h-9 rounded-full flex items-center justify-center transition-all duration-300 opacity-0 translate-y-2 group-hover:opacity-100 group-hover:translate-y-0 z-10"
                    :class="product.stock > 0
                      ? 'bg-[#0a0a0a] text-white hover:bg-neutral-700 shadow-lg'
                      : 'bg-neutral-300 text-neutral-500 cursor-not-allowed'"
                  >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                  </button>

                  <!-- Badges -->
                  <div class="absolute top-0 left-0 flex flex-col">
                    <span v-if="product.stock <= 5 && product.stock > 0" class="px-2.5 py-1 bg-[#0a0a0a] text-white text-[9px] font-bold uppercase tracking-[0.1em]">
                      Últimas {{ product.stock }}
                    </span>
                    <span v-else-if="product.stock === 0" class="px-2.5 py-1 bg-neutral-600 text-white text-[9px] font-bold uppercase tracking-[0.1em]">
                      Agotado
                    </span>
                    <span v-if="product.is_new" class="px-2.5 py-1 bg-white text-[#0a0a0a] text-[9px] font-bold uppercase tracking-[0.1em]">
                      New
                    </span>
                  </div>
                </div>

                <!-- Product info -->
                <div class="px-3 pt-3 pb-4">
                  <p class="text-[9px] uppercase tracking-[0.2em] text-neutral-400 mb-1 truncate">{{ product.category || 'Colección' }}</p>
                  <h3
                    class="text-[13px] font-medium text-neutral-800 truncate leading-snug mb-1.5"
                    style="font-family: 'Inter', sans-serif;"
                  >
                    {{ product.name }}
                  </h3>
                  <p class="text-[14px] font-bold text-[#0a0a0a]">
                    {{ storeConfig.currency_symbol }}{{ formatPrice(product.price) }}
                  </p>
                </div>
              </div>
            </div>
          </TransitionGroup>
        </div>

        <!-- Empty state -->
        <div v-if="filteredProducts.length === 0" class="text-center py-24">
          <div class="w-16 h-16 mx-auto mb-4 border-2 border-neutral-200 rounded-full flex items-center justify-center">
            <svg class="w-7 h-7 text-neutral-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993l1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 01-1.12-1.243l1.264-12A1.125 1.125 0 015.513 7.5h12.974c.576 0 1.059.435 1.119 1.007z" />
            </svg>
          </div>
          <p class="text-neutral-400 text-sm tracking-wide">No hay productos disponibles</p>
          <button v-if="hasActiveFilters" @click="clearFilters" class="mt-3 text-[11px] font-semibold uppercase tracking-[0.12em] text-[#0a0a0a] underline underline-offset-4">
            Limpiar filtros
          </button>
        </div>
      </section>
    </div>

    <!-- ═══════════════════════════════════════════════════════════
         RECOMMENDATIONS: "También te podría interesar" cross-sell
         ═══════════════════════════════════════════════════════════ -->
    <section v-if="recommendedProducts.length > 0" class="bg-white border-t border-neutral-100 px-4 lg:px-10 py-10 lg:py-14">
      <div class="flex items-center justify-between mb-6">
        <div>
          <p class="text-[10px] text-neutral-400 uppercase tracking-[0.25em] font-semibold mb-1">Curado para ti</p>
          <h3
            class="text-[20px] md:text-[24px] font-bold text-[#0a0a0a] uppercase"
            style="font-family: 'Inter', sans-serif; letter-spacing: -0.01em;"
          >
            También te podría interesar
          </h3>
        </div>
      </div>
      <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3 lg:gap-4">
        <div
          v-for="product in recommendedProducts"
          :key="'rec-' + product.id"
          @click="openProductDetails(product)"
          class="group cursor-pointer"
        >
          <div class="relative aspect-[3/4] overflow-hidden bg-neutral-100 mb-2.5">
            <img
              v-if="getProductImage(product)"
              :src="getProductImage(product)"
              :alt="product.name"
              class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-[1.03]"
            />
            <div v-else class="w-full h-full flex items-center justify-center">
              <svg class="w-8 h-8 text-neutral-300" viewBox="0 0 24 24" fill="none"><path d="M5 9h14l1 12H4L5 9z" fill="currentColor" opacity="0.15"/><path d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" stroke="currentColor" stroke-width="1" fill="none"/></svg>
            </div>
          </div>
          <p class="text-[9px] uppercase tracking-[0.2em] text-neutral-400 mb-0.5 truncate">{{ product.category || 'Colección' }}</p>
          <h4 class="text-[12px] font-medium text-neutral-700 truncate" style="font-family: 'Inter', sans-serif;">{{ product.name }}</h4>
          <p class="text-[13px] font-bold text-[#0a0a0a] mt-0.5">{{ storeConfig.currency_symbol }}{{ formatPrice(product.price) }}</p>
        </div>
      </div>
    </section>

    <!-- ═══════════════════════════════════════════════════════════
         ABOUT / BRAND STORY (if AI generated)
         ═══════════════════════════════════════════════════════════ -->
    <section v-if="storeConfig.ai_about_us" class="bg-[#0a0a0a] text-white px-4 lg:px-10 py-12 lg:py-16">
      <div class="max-w-3xl mx-auto text-center">
        <span class="text-[10px] uppercase tracking-[0.3em] text-neutral-500 font-semibold">Nuestra esencia</span>
        <h3 class="text-[24px] md:text-[32px] font-bold uppercase mt-3 mb-5" style="font-family: 'Inter', sans-serif; letter-spacing: -0.01em;">
          {{ storeName }}
        </h3>
        <p class="text-[14px] md:text-[15px] text-neutral-400 leading-relaxed" style="font-family: 'Inter', sans-serif;">
          {{ storeConfig.ai_about_us }}
        </p>
        <div v-if="storeConfig.ai_value_messages && storeConfig.ai_value_messages.length > 0" class="flex flex-wrap justify-center gap-4 mt-8">
          <div
            v-for="(msg, i) in storeConfig.ai_value_messages"
            :key="'brand-val-' + i"
            class="flex items-center gap-2 text-[12px] text-neutral-500"
          >
            <span class="w-1 h-1 rounded-full bg-neutral-600"></span>
            {{ msg }}
          </div>
        </div>
      </div>
    </section>

    <!-- ═══════════════════════════════════════════════════════════
         FOOTER
         ═══════════════════════════════════════════════════════════ -->
    <footer class="bg-white border-t border-neutral-100 py-8 text-center" :class="{ 'mb-16': cartCount > 0 }">
      <p class="text-[11px] text-neutral-400 tracking-wide">
        Tecnología por
        <a href="https://105pos.pro/register" target="_blank" rel="noopener noreferrer" class="font-semibold text-neutral-600 hover:text-[#0a0a0a] transition-colors">105 POS</a>
      </p>
    </footer>

    <!-- ═══════════════════════════════════════════════════════════
         WHATSAPP FLOATING BUTTON
         ═══════════════════════════════════════════════════════════ -->
    <a
      v-if="storeConfig.whatsapp_number"
      :href="`https://wa.me/${storeConfig.whatsapp_number.replace(/[^0-9]/g, '')}?text=Hola, me interesa hacer un pedido`"
      target="_blank"
      class="fixed right-4 lg:right-6 z-[60] w-12 h-12 bg-[#25D366] hover:bg-[#1ebe57] text-white rounded-full flex items-center justify-center hover:scale-105 transition-all duration-300 bottom-6"
      :class="{ 'bottom-[76px]': cartCount > 0 }"
      style="box-shadow: 0 4px 16px rgba(37, 211, 102, 0.3);"
      title="Contactar por WhatsApp"
    >
      <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893A11.821 11.821 0 0020.885 3.515"/>
      </svg>
    </a>

    <!-- ═══════════════════════════════════════════════════════════
         STICKY BOTTOM BAR (Mobile cart)
         ═══════════════════════════════════════════════════════════ -->
    <Transition name="slide-up">
      <div
        v-if="cartCount > 0"
        class="fixed bottom-0 left-0 right-0 z-[55] px-4 py-3 flex items-center justify-between bg-[#0a0a0a] lg:hidden"
        style="box-shadow: 0 -2px 20px rgba(0,0,0,0.15);"
      >
        <div>
          <p class="text-[10px] text-neutral-500 uppercase tracking-wide font-medium">Total</p>
          <p class="text-lg font-bold text-white">{{ storeConfig.currency_symbol }}{{ formatPrice(cartTotal) }}</p>
        </div>
        <button
          @click="router.push('/catalog/bolsa')"
          class="bg-white text-[#0a0a0a] px-6 py-3 text-[11px] font-bold uppercase tracking-[0.14em] transition-all hover:bg-neutral-200 flex items-center gap-2"
        >
          <span class="bg-[#0a0a0a] text-white text-[9px] font-bold w-5 h-5 rounded-full flex items-center justify-center">{{ cartCount }}</span>
          VER BOLSA
        </button>
      </div>
    </Transition>

    <!-- ═══════════════════════════════════════════════════════════
         OFF-CANVAS: Mobile menu drawer
         ═══════════════════════════════════════════════════════════ -->
    <Transition name="fade">
      <div v-if="showMobileMenu" class="fixed inset-0 bg-black/60 z-[160]" @click="showMobileMenu = false"></div>
    </Transition>
    <Transition name="drawer-left">
      <div v-if="showMobileMenu" class="fixed top-0 left-0 bottom-0 w-[280px] bg-[#0a0a0a] z-[161] flex flex-col shadow-2xl">
        <div class="flex items-center justify-between px-5 h-14 border-b border-white/[0.06]">
          <h2 class="text-[14px] text-white font-bold uppercase tracking-[0.12em]" style="font-family: 'Inter', sans-serif;">
            {{ storeName }}
          </h2>
          <button @click="showMobileMenu = false" class="w-9 h-9 flex items-center justify-center text-neutral-500 hover:text-white transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
              <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>
        <nav class="flex-1 overflow-y-auto py-2">
          <button
            @click="selectedCategory = null; showMobileMenu = false"
            class="w-full text-left px-5 py-4 text-[13px] font-medium border-b border-white/[0.04] transition-colors flex items-center justify-between"
            :class="selectedCategory === null ? 'text-white' : 'text-neutral-500 hover:text-white'"
          >
            <span>Todas las categorías</span>
            <span v-if="selectedCategory === null" class="w-1.5 h-1.5 bg-white rounded-full"></span>
          </button>
          <button
            v-for="cat in categories"
            :key="'drawer-cat-' + cat.id"
            @click="selectedCategory = cat.id; showMobileMenu = false"
            class="w-full text-left px-5 py-4 text-[13px] font-medium border-b border-white/[0.04] transition-colors flex items-center justify-between"
            :class="selectedCategory === cat.id ? 'text-white' : 'text-neutral-500 hover:text-white'"
          >
            <span>{{ cat.name }}</span>
            <span v-if="selectedCategory === cat.id" class="w-1.5 h-1.5 bg-white rounded-full"></span>
          </button>
        </nav>
        <div class="px-5 py-4 border-t border-white/[0.06]">
          <p class="text-[10px] text-neutral-600 text-center tracking-wide">
            Tecnología por <a href="https://105pos.pro/register" target="_blank" rel="noopener noreferrer" class="font-semibold text-neutral-500">105 POS</a>
          </p>
        </div>
      </div>
    </Transition>

    <!-- ═══════════════════════════════════════════════════════════
         MOBILE FILTER SHEET (bottom drawer)
         ═══════════════════════════════════════════════════════════ -->
    <Transition name="fade">
      <div v-if="showMobileFilters" class="lg:hidden fixed inset-0 bg-black/60 z-[150]" @click="showMobileFilters = false"></div>
    </Transition>
    <Transition name="slide-up">
      <div v-if="showMobileFilters" class="lg:hidden fixed bottom-0 left-0 right-0 bg-white z-[151] rounded-t-2xl max-h-[80vh] overflow-hidden flex flex-col">
        <div class="flex items-center justify-between px-6 py-4 border-b border-neutral-100">
          <h3 class="text-[14px] font-bold text-[#0a0a0a] uppercase tracking-[0.1em]">Filtros</h3>
          <button @click="showMobileFilters = false" class="w-8 h-8 flex items-center justify-center text-neutral-400 hover:text-[#0a0a0a]">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
          </button>
        </div>
        <div class="flex-1 overflow-y-auto px-6 py-5 space-y-6">
          <!-- Price -->
          <div>
            <h4 class="text-[10px] font-bold text-neutral-400 mb-4 uppercase tracking-[0.2em]">Precio máximo</h4>
            <div class="space-y-3">
              <div class="relative h-1 bg-neutral-200 rounded-full">
                <div class="absolute left-0 h-full bg-[#0a0a0a] rounded-full" :style="{ width: ((priceRange.max - minProductPrice) / (maxProductPrice - minProductPrice) * 100) + '%' }"></div>
                <input type="range" :min="minProductPrice" :max="maxProductPrice" v-model.number="priceRange.max" class="absolute w-full h-1 appearance-none bg-transparent cursor-pointer" />
              </div>
              <p class="text-center text-[13px] font-semibold text-[#0a0a0a]">Hasta {{ storeConfig.currency_symbol }}{{ formatPrice(priceRange.max) }}</p>
            </div>
          </div>
          <!-- Stock -->
          <label class="flex items-center gap-2.5 cursor-pointer">
            <input type="checkbox" v-model="showOnlyAvailable" class="w-4 h-4 rounded border-neutral-300 text-[#0a0a0a] accent-[#0a0a0a]" />
            <span class="text-[13px] text-neutral-600">Solo productos con stock</span>
          </label>
        </div>
        <div class="px-6 py-4 border-t border-neutral-100">
          <button @click="showMobileFilters = false" class="w-full py-3.5 bg-[#0a0a0a] text-white text-[12px] font-bold uppercase tracking-[0.12em] transition-colors hover:bg-neutral-800">
            Ver {{ filteredProducts.length }} productos
          </button>
        </div>
      </div>
    </Transition>

    <!-- ═══════════════════════════════════════════════════════════
         MOBILE SORT SHEET
         ═══════════════════════════════════════════════════════════ -->
    <Transition name="fade">
      <div v-if="showSortModal" class="lg:hidden fixed inset-0 bg-black/60 z-[150]" @click="showSortModal = false"></div>
    </Transition>
    <Transition name="slide-up">
      <div v-if="showSortModal" class="lg:hidden fixed bottom-0 left-0 right-0 bg-white z-[151] rounded-t-2xl overflow-hidden">
        <div class="flex items-center justify-between px-6 py-4 border-b border-neutral-100">
          <h3 class="text-[14px] font-bold text-[#0a0a0a] uppercase tracking-[0.1em]">Ordenar por</h3>
          <button @click="showSortModal = false" class="w-8 h-8 flex items-center justify-center text-neutral-400 hover:text-[#0a0a0a]">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
          </button>
        </div>
        <div class="px-6 py-4 space-y-1">
          <button
            v-for="opt in sortOptions"
            :key="'mob-sort-' + opt.value"
            @click="sortOrder = opt.value; showSortModal = false"
            class="w-full text-left py-3.5 text-[13px] transition-all flex items-center gap-3"
            :class="sortOrder === opt.value ? 'text-[#0a0a0a] font-semibold' : 'text-neutral-400'"
          >
            <span
              class="w-5 h-5 border-2 rounded-full flex items-center justify-center transition-colors"
              :class="sortOrder === opt.value ? 'border-[#0a0a0a] bg-[#0a0a0a]' : 'border-neutral-300'"
            >
              <svg v-if="sortOrder === opt.value" class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
              </svg>
            </span>
            {{ opt.label }}
          </button>
        </div>
        <div class="h-6"></div>
      </div>
    </Transition>

    <!-- ═══════════════════════════════════════════════════════════
         MODALS: QuantityModal + POSVariantSelector (inherited logic)
         ═══════════════════════════════════════════════════════════ -->
    <QuantityModal
      :show="showQuantityModal"
      :product="selectedProductForQuantity"
      @close="showQuantityModal = false"
      @confirm="handleQuantityConfirmed"
    />
    <POSVariantSelector
      :show="showVariantModal"
      :product="selectedProductForVariants"
      @close="showVariantModal = false"
      @confirm="handleVariantConfirmed"
    />

    <!-- Toast notification -->
    <Transition name="slide-up">
      <div
        v-if="toast.show"
        class="fixed bottom-24 left-4 right-4 z-[200] bg-[#0a0a0a] text-white px-4 py-3 rounded-xl flex items-center gap-3 lg:left-auto lg:right-6 lg:max-w-sm"
        style="box-shadow: 0 8px 32px rgba(0,0,0,0.2);"
      >
        <div class="w-6 h-6 rounded-full bg-white/10 flex items-center justify-center flex-shrink-0">
          <svg class="w-3.5 h-3.5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
          </svg>
        </div>
        <span class="text-[13px] font-medium flex-1">{{ toast.message }}</span>
        <button
          @click="router.push('/catalog/bolsa')"
          class="text-[10px] font-bold uppercase tracking-[0.1em] text-white/60 hover:text-white bg-white/10 px-3 py-1.5 rounded-lg transition-colors whitespace-nowrap"
        >
          Ver bolsa
        </button>
      </div>
    </Transition>

  </div>
</template>

<script setup>
/**
 * ═══════════════════════════════════════════════════════════════════
 * PLANTILLA D: "URBAN STREETWEAR" — PlantillaUrbana01
 * ─────────────────────────────────────────────────────────────────
 * Lógica de integración 100% heredada de CatalogTemplateA.
 * Solo el diseño visual es nuevo.
 * ═══════════════════════════════════════════════════════════════════
 */
import { ref, computed, onMounted, onUnmounted, watch, nextTick, inject } from 'vue'
import { useRouter } from 'vue-router'
import QuantityModal from './QuantityModal.vue'
import POSVariantSelector from '../POSVariantSelector.vue'
import { useCatalogCart } from '../../stores/catalogCart.js'
import { productUrl } from '../../utils/slugify.js'

const router = useRouter()
const isPreviewMode = inject('isPreviewMode', false)
const { cartItems, cartCount, addItem, removeItem, toast } = useCatalogCart()

// ─── MOCK DATA (10 productos urbanos de muestra) ───
// Usados cuando catalog_products está vacío (modo desarrollo).
// En producción se alimenta de los productos reales del POS.
const MOCK_URBAN_PRODUCTS = [
  { id: 'mock-1', name: 'Hoodie Oversize Essential', price: 189000, stock: 12, category: 'Hoodies', category_id: 'cat-hoodies', image_url: 'https://images.unsplash.com/photo-1556821840-3a63f95609a7?w=600&q=80', is_new: true, description: 'Hoodie oversize de algodón premium con corte urbano relajado.' },
  { id: 'mock-2', name: 'Cargo Pants Tactical', price: 165000, stock: 8, category: 'Pantalones', category_id: 'cat-pants', image_url: 'https://images.unsplash.com/photo-1624378439575-d8705ad7ae80?w=600&q=80', is_new: false, description: 'Pantalón cargo con múltiples bolsillos y tela resistente.' },
  { id: 'mock-3', name: 'Tee Graphic "No Signal"', price: 79000, stock: 25, category: 'Camisetas', category_id: 'cat-tees', image_url: 'https://images.unsplash.com/photo-1576566588028-4147f3842f27?w=600&q=80', is_new: true, description: 'Camiseta de algodón 100% con estampado gráfico urbano.' },
  { id: 'mock-4', name: 'Bomber Jacket Black', price: 285000, stock: 4, category: 'Chaquetas', category_id: 'cat-jackets', image_url: 'https://images.unsplash.com/photo-1591047139829-d91aecb6caea?w=600&q=80', is_new: false, description: 'Bomber jacket en nylon con forro interior satinado.' },
  { id: 'mock-5', name: 'Jogger Slim Fit', price: 129000, stock: 15, category: 'Pantalones', category_id: 'cat-pants', image_url: 'https://images.unsplash.com/photo-1552902865-b72c031ac5ea?w=600&q=80', is_new: false, description: 'Jogger de corte slim en french terry de algodón.' },
  { id: 'mock-6', name: 'Bucket Hat Washed', price: 55000, stock: 20, category: 'Accesorios', category_id: 'cat-accessories', image_url: 'https://images.unsplash.com/photo-1588850561407-ed78c334e67a?w=600&q=80', is_new: true, description: 'Bucket hat de algodón lavado con fit relajado.' },
  { id: 'mock-7', name: 'Crossbody Bag Urban', price: 95000, stock: 10, category: 'Accesorios', category_id: 'cat-accessories', image_url: 'https://images.unsplash.com/photo-1553062407-98eeb64c6a62?w=600&q=80', is_new: false, description: 'Bolso cruzado en nylon con cierre frontal y correa ajustable.' },
  { id: 'mock-8', name: 'Crewneck Heavyweight', price: 145000, stock: 7, category: 'Hoodies', category_id: 'cat-hoodies', image_url: 'https://images.unsplash.com/photo-1618354691373-d851c5c3a990?w=600&q=80', is_new: false, description: 'Crewneck de algodón heavyweight 400gsm.' },
  { id: 'mock-9', name: 'Track Pants Retro', price: 139000, stock: 0, category: 'Pantalones', category_id: 'cat-pants', image_url: 'https://images.unsplash.com/photo-1515886657613-9f3515b0c78f?w=600&q=80', is_new: false, description: 'Pantalón track retro con franjas laterales contrastantes.' },
  { id: 'mock-10', name: 'Windbreaker Reflective', price: 225000, stock: 3, category: 'Chaquetas', category_id: 'cat-jackets', image_url: 'https://images.unsplash.com/photo-1544966503-7cc5ac882d5f?w=600&q=80', is_new: true, description: 'Rompevientos con detalles reflectivos y capucha oculta.' },
]

const MOCK_CATEGORIES = [
  { id: 'cat-hoodies', name: 'Hoodies' },
  { id: 'cat-pants', name: 'Pantalones' },
  { id: 'cat-tees', name: 'Camisetas' },
  { id: 'cat-jackets', name: 'Chaquetas' },
  { id: 'cat-accessories', name: 'Accesorios' },
]

// ─── PROPS (idénticas a CatalogTemplateA) ───
const props = defineProps({
  storeConfig: {
    type: Object,
    required: true,
    default: () => ({
      primary_color: '#0a0a0a',
      logo_url: '',
      banner_url: '',
      whatsapp_number: '',
      currency_symbol: '$',
      delivery_cost: 0,
      min_order_value: 0,
      catalog_products: []
    })
  },
  isMobilePreview: {
    type: Boolean,
    default: false
  },
  categories: {
    type: Array,
    default: () => []
  }
})

// ─── STATE ───
const isScrolled = ref(false)
const imageErrors = ref({})
const selectedCategory = ref(null)
const searchQuery = ref('')
const sortOrder = ref('')
const showOnlyAvailable = ref(false)
const priceRange = ref({ min: 0, max: 1000000 })
const productsSection = ref(null)
const currentSlide = ref(0)

// Modals
const showQuantityModal = ref(false)
const selectedProductForQuantity = ref(null)
const showVariantModal = ref(false)
const selectedProductForVariants = ref(null)

// Mobile UI
const showMobileMenu = ref(false)
const showMobileSearch = ref(false)
const showMobileFilters = ref(false)
const showSortModal = ref(false)

// Ticker announcements
const defaultAnnouncements = [
  'ENVÍO GRATIS EN COMPRAS + $150.000',
  'NUEVA COLECCIÓN DISPONIBLE',
  'HASTA 3 CUOTAS SIN INTERÉS',
  'DEVOLUCIONES GRATIS — 30 DÍAS'
]
const announcements = computed(() => {
  const ai = props.storeConfig.ai_announcements
  return (ai && Array.isArray(ai) && ai.length > 0) ? ai : defaultAnnouncements
})
const currentAnnouncement = ref(0)
const currentAnnouncementText = computed(() => {
  const text = announcements.value[currentAnnouncement.value] || ''
  return text.length > 64 ? `${text.slice(0, 61).trim()}...` : text
})

// Sort options
const sortOptions = [
  { value: '', label: 'Relevancia' },
  { value: 'price-asc', label: 'Menor precio' },
  { value: 'price-desc', label: 'Mayor precio' },
  { value: 'name-asc', label: 'Nombre (A-Z)' },
]

// ─── THEME (CSS vars for consistency) ───
const themeVars = computed(() => ({
  '--urban-bg': '#fafafa',
  '--urban-dark': '#0a0a0a',
  '--urban-primary': props.storeConfig.primary_color || '#0a0a0a',
}))

// ─── COMPUTED: Productos & categorías con fallback a mock ───
const catalogProducts = computed(() => {
  const real = props.storeConfig.catalog_products
  return (real && real.length > 0) ? real : MOCK_URBAN_PRODUCTS
})

const effectiveCategories = computed(() => {
  return (props.categories && props.categories.length > 0) ? props.categories : MOCK_CATEGORIES
})

// Re-export categories for template usage (real > mock)
const categories = effectiveCategories

const storeName = computed(() => props.storeConfig.store_name || 'URBAN STORE')

// ─── HERO ───
const bannerHeadline = computed(() => props.storeConfig.ai_banner_texts?.headline || 'DROP 01')
const bannerSubheadline = computed(() => props.storeConfig.ai_banner_texts?.subheadline || 'La nueva temporada ya está aquí. Piezas esenciales para tu día a día urbano.')
const bannerCtaText = computed(() => props.storeConfig.ai_banner_texts?.cta_text || '')

const heroHeadlineDisplay = computed(() => {
  const text = bannerHeadline.value
  return text.length > 40 ? `${text.slice(0, 37).trim()}...` : text
})
const heroSubheadlineDisplay = computed(() => {
  const text = bannerSubheadline.value
  return text.length > 100 ? `${text.slice(0, 97).trim()}...` : text
})

const carouselImages = computed(() => {
  const images = []
  if (props.storeConfig.banner_url) images.push(props.storeConfig.banner_url)
  const defaults = [
    'https://images.unsplash.com/photo-1523398002811-999ca8dec234?w=1200&q=80',
    'https://images.unsplash.com/photo-1509631179647-0177331693ae?w=1200&q=80',
    'https://images.unsplash.com/photo-1495121605193-b116b5b9c5fe?w=1200&q=80'
  ]
  defaults.forEach(img => { if (images.length < 3) images.push(img) })
  return images.slice(0, 3)
})

// ─── PRODUCT HELPERS (heredado de TemplateA) ───
const getProductImage = (product) => {
  if (!product) return ''
  if (product.images && product.images.length > 0) return product.images[0]
  return product.image_url || ''
}

// ─── PRICE RANGE ───
const minProductPrice = computed(() => {
  if (catalogProducts.value.length === 0) return 0
  return Math.floor(Math.min(...catalogProducts.value.map(p => parseFloat(p.price || 0))))
})
const maxProductPrice = computed(() => {
  if (catalogProducts.value.length === 0) return 1000000
  return Math.ceil(Math.max(...catalogProducts.value.map(p => parseFloat(p.price || 0))))
})

// ─── FILTERED PRODUCTS (lógica exacta de TemplateA) ───
const filteredProducts = computed(() => {
  let products = catalogProducts.value

  if (searchQuery.value.trim()) {
    const query = searchQuery.value.toLowerCase()
    products = products.filter(p =>
      (p.name || '').toLowerCase().includes(query) ||
      (p.description || '').toLowerCase().includes(query)
    )
  }

  if (selectedCategory.value !== null) {
    products = products.filter(p => p.category_id === selectedCategory.value)
  }

  if (showOnlyAvailable.value) {
    products = products.filter(p => p.stock && p.stock > 0)
  }

  if (priceRange.value.max < maxProductPrice.value) {
    products = products.filter(p => parseFloat(p.price || 0) <= priceRange.value.max)
  }

  if (sortOrder.value) {
    products = [...products]
    switch (sortOrder.value) {
      case 'price-asc': products.sort((a, b) => parseFloat(a.price || 0) - parseFloat(b.price || 0)); break
      case 'price-desc': products.sort((a, b) => parseFloat(b.price || 0) - parseFloat(a.price || 0)); break
      case 'name-asc': products.sort((a, b) => (a.name || '').localeCompare(b.name || '')); break
      case 'name-desc': products.sort((a, b) => (b.name || '').localeCompare(a.name || '')); break
    }
  }

  return products
})

const hasActiveFilters = computed(() => {
  return selectedCategory.value !== null || showOnlyAvailable.value || sortOrder.value || searchQuery.value.trim()
})

const activeCategoryName = computed(() => {
  if (selectedCategory.value === null) return 'Catálogo'
  const match = effectiveCategories.value.find(c => String(c.id) === String(selectedCategory.value))
  return match?.name || 'Colección'
})

// ─── RECOMMENDATIONS (cross-sell) ───
const recommendedProducts = computed(() => {
  const inStock = catalogProducts.value.filter(p => Number(p.stock || 0) > 0)
  const source = inStock.length >= 5 ? inStock : catalogProducts.value
  // Shuffle-ish: take last 5 different from current filtered view
  const filtered = filteredProducts.value.map(p => p.id)
  const recs = source.filter(p => !filtered.includes(p.id)).slice(0, 5)
  return recs.length >= 3 ? recs : source.slice(0, 5)
})

// ─── CART (lógica idéntica a TemplateA) ───
const cartTotal = computed(() => {
  return cartItems.value.reduce((sum, item) => sum + parseFloat(item.price), 0)
})

// ─── GRID CLASSES ───
const gridClasses = computed(() => {
  if (props.isMobilePreview) return 'grid grid-cols-2 gap-3'
  return 'grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-3 xl:grid-cols-4 gap-3 lg:gap-4'
})

// ─── METHODS (heredados de TemplateA) ───
const formatPrice = (price) => {
  return parseFloat(price).toLocaleString('es-CO', { minimumFractionDigits: 0, maximumFractionDigits: 0 })
}

const clearFilters = () => {
  selectedCategory.value = null
  searchQuery.value = ''
  sortOrder.value = ''
  showOnlyAvailable.value = false
  priceRange.value.max = maxProductPrice.value
}

const handleImageError = (productId) => {
  imageErrors.value[productId] = true
}

const addToCart = (product) => {
  if (product.stock === 0) return

  const requiresQuantityInput = product.measurement_unit && product.measurement_unit !== 'unit'
  if (requiresQuantityInput) {
    selectedProductForQuantity.value = product
    showQuantityModal.value = true
    return
  }

  const hasVariants = product.variants && product.variants.length > 0
  if (hasVariants) {
    router.push(productUrl(product))
    return
  }

  addItem({ ...product, image_url: product.image_url || (product.images && product.images[0]) })
}

const handleQuantityConfirmed = ({ product, quantity }) => {
  const productWithQuantity = {
    ...product,
    id: `${product.id}-${Date.now()}`,
    quantity_value: quantity,
    name: `${product.name} (${quantity} ${product.unit || 'kg'})`,
    price: product.price * quantity,
    original_price: product.price,
    display_quantity: quantity
  }
  addItem(productWithQuantity)
  selectedProductForQuantity.value = null
  showQuantityModal.value = false
}

const handleVariantConfirmed = ({ variant, selectedOptions }) => {
  if (!variant || !selectedProductForVariants.value) return
  if (variant.stock <= 0) return

  const optionsSummary = Object.entries(selectedOptions)
    .map(([key, value]) => `${key}: ${value}`)
    .join(' / ')

  const productWithVariant = {
    ...selectedProductForVariants.value,
    id: `${selectedProductForVariants.value.id}-${variant.id}`,
    variant_id: variant.id,
    name: `${selectedProductForVariants.value.name} (${optionsSummary})`,
    price: variant.price,
    stock: variant.stock,
    image_url: selectedProductForVariants.value.image_url || selectedProductForVariants.value.image,
    variant_options: optionsSummary
  }
  addItem(productWithVariant)
  selectedProductForVariants.value = null
  showVariantModal.value = false
}

const scrollToProducts = () => {
  if (!productsSection.value) return
  const top = productsSection.value.getBoundingClientRect().top + window.scrollY - 160
  window.scrollTo({ top: Math.max(top, 0), behavior: 'smooth' })
}

const openProductDetails = (product) => {
  if (isPreviewMode) return
  router.push(productUrl(product))
}

// ─── CAROUSEL & TICKER INTERVALS ───
let carouselInterval = null
const startCarousel = () => {
  carouselInterval = setInterval(() => {
    currentSlide.value = (currentSlide.value + 1) % Math.max(carouselImages.value.length, 1)
  }, 5000)
}
const stopCarousel = () => { if (carouselInterval) clearInterval(carouselInterval) }

let announcementInterval = null
const startAnnouncementTicker = () => {
  announcementInterval = setInterval(() => {
    currentAnnouncement.value = (currentAnnouncement.value + 1) % announcements.value.length
  }, 3500)
}
const stopAnnouncementTicker = () => { if (announcementInterval) clearInterval(announcementInterval) }

const handleScroll = () => { isScrolled.value = window.scrollY > 100 }

const initPriceRange = () => {
  priceRange.value.min = minProductPrice.value
  priceRange.value.max = maxProductPrice.value
}

// ─── LIFECYCLE ───
onMounted(() => {
  window.addEventListener('scroll', handleScroll)
  startCarousel()
  startAnnouncementTicker()
  initPriceRange()
  catalogProducts.value.forEach(p => { imageErrors.value[p.id] = false })
})

watch(() => props.storeConfig.catalog_products, () => { initPriceRange() }, { deep: true })

onUnmounted(() => {
  window.removeEventListener('scroll', handleScroll)
  stopCarousel()
  stopAnnouncementTicker()
})
</script>

<style scoped>
/* ═══════════════════════════════════════════════════════
   PLANTILLA D: "URBAN STREETWEAR" — Estilos base
   ═══════════════════════════════════════════════════════ */
.catalog-urban {
  font-family: 'Inter', 'Helvetica Neue', Arial, sans-serif;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
}

/* Scrollbar hide */
.scrollbar-hide::-webkit-scrollbar { display: none; }
.scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }

/* Ticker animation */
.ticker-urban-enter-active { transition: all 0.6s cubic-bezier(0.16, 1, 0.3, 1); }
.ticker-urban-leave-active { transition: all 0.4s cubic-bezier(0.7, 0, 0.84, 0); }
.ticker-urban-enter-from { opacity: 0; transform: translateY(100%); }
.ticker-urban-leave-to { opacity: 0; transform: translateY(-100%); }

/* Off-canvas drawer */
.drawer-left-enter-active,
.drawer-left-leave-active { transition: transform 0.3s ease-in-out; }
.drawer-left-enter-from,
.drawer-left-leave-to { transform: translateX(-100%); }

/* Slide down (search) */
.slide-down-enter-active,
.slide-down-leave-active { transition: all 0.3s ease; }
.slide-down-enter-from,
.slide-down-leave-to { opacity: 0; transform: translateY(-10px); }

/* Fade */
.fade-enter-active, .fade-leave-active { transition: opacity 0.3s ease; }
.fade-enter-from, .fade-leave-to { opacity: 0; }

/* Slide up */
.slide-up-enter-active, .slide-up-leave-active { transition: transform 0.4s cubic-bezier(0.4, 0, 0.2, 1); }
.slide-up-enter-from, .slide-up-leave-to { transform: translateY(100%); }

/* Product list transitions */
.list-move,
.list-enter-active,
.list-leave-active { transition: all 0.4s cubic-bezier(0.55, 0, 0.1, 1); }
.list-enter-from { opacity: 0; transform: translateY(20px); }
.list-leave-to { opacity: 0; transform: scale(0.9); }
.list-leave-active { position: absolute; }

/* Hero carousel fade */
.fade-slide-enter-active { transition: opacity 1.5s ease; }
.fade-slide-leave-active { transition: opacity 1s ease; }
.fade-slide-enter-from { opacity: 0; }
.fade-slide-leave-to { opacity: 0; }

/* Range slider */
input[type="range"] {
  -webkit-appearance: none;
  appearance: none;
  background: transparent;
  cursor: pointer;
}
input[type="range"]::-webkit-slider-thumb {
  -webkit-appearance: none;
  width: 16px;
  height: 16px;
  border-radius: 50%;
  background: #0a0a0a;
  border: 2px solid white;
  box-shadow: 0 1px 4px rgba(0,0,0,0.2);
  cursor: pointer;
  margin-top: -7px;
}
input[type="range"]::-moz-range-thumb {
  width: 16px;
  height: 16px;
  border-radius: 50%;
  background: #0a0a0a;
  border: 2px solid white;
  box-shadow: 0 1px 4px rgba(0,0,0,0.2);
  cursor: pointer;
}
input[type="range"]::-webkit-slider-runnable-track { height: 4px; border-radius: 2px; }
input[type="range"]::-moz-range-track { height: 4px; border-radius: 2px; }
</style>
