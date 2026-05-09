<template>
  <!-- PLANTILLA A: "MODA PREMIUM" - Estilo V�lez/Zara -->
  <div class="catalog-visual-story relative overflow-x-hidden min-h-screen" :style="themeVars">
    
    <!-- TOP BAR: Ticker s�lido y elegante -->
    <div
      class="fixed top-0 left-0 right-0 z-[60] h-8 flex items-center justify-center overflow-hidden border-b"
      :style="tickerBarStyle"
    >
      <div class="relative w-full h-full flex items-center justify-center">
        <TransitionGroup :name="tickerTransitionName" tag="div" class="relative w-full h-full">
          <span 
            :key="currentAnnouncement"
            class="absolute inset-0 flex items-center justify-center gap-2 text-[11px] font-medium tracking-[0.08em] whitespace-nowrap px-4"
            :style="{ color: tickerTextColor }"
          >
            <span class="w-1.5 h-1.5 rounded-full flex-shrink-0" :style="{ backgroundColor: aiPalette.primary }"></span>
            <span class="truncate max-w-[92vw]">{{ currentAnnouncementText }}</span>
          </span>
        </TransitionGroup>
      </div>
    </div>

    <!-- HEADER MODULAR: Switch entre los 5 estilos seg�n headerConfig -->

    <!-- editorial-center: Alta moda, logo centrado serif -->
    <HeaderEditorialCenter
      v-if="headerConfig.style === 'editorial-center'"
      :storeName="storeName"
      :logoUrl="storeConfig.logo_url"
      :cartCount="cartCount"
      v-model="searchQuery"
      :palette="aiPalette"
      :fonts="aiFonts"
      class="fixed top-8 left-0 right-0 z-50"
      @menu="showMobileMenu = true"
      @cart="router.push('/catalog/bolsa')"
    />

    <!-- retail-left: Consumo masivo, logo izquierda -->
    <HeaderRetailLeft
      v-else-if="headerConfig.style === 'retail-left'"
      :storeName="storeName"
      :logoUrl="storeConfig.logo_url"
      :cartCount="cartCount"
      v-model="searchQuery"
      :palette="aiPalette"
      :fonts="aiFonts"
      class="fixed top-8 left-0 right-0 z-50"
      @menu="showMobileMenu = true"
      @cart="router.push('/catalog/bolsa')"
    />

    <!-- transparent-glass: ? Pairing perfecto con portrait o editorial -->
    <!-- El header maneja su propio top-8 internamente -->
    <HeaderTransparentGlass
      v-else-if="headerConfig.style === 'transparent-glass'"
      :storeName="storeName"
      :logoUrl="storeConfig.logo_url"
      :cartCount="cartCount"
      v-model="searchQuery"
      :palette="aiPalette"
      :fonts="aiFonts"
      @menu="showMobileMenu = true"
      @cart="router.push('/catalog/bolsa')"
    />

    <!-- floating-pill ? retail-overlay: Urbano/Premium, transparente sobre hero -->
    <HeaderRetailOverlay
      v-else-if="headerConfig.style === 'floating-pill'"
      :storeName="storeName"
      :logoUrl="storeConfig.logo_url"
      :cartCount="cartCount"
      v-model="searchQuery"
      :palette="aiPalette"
      :fonts="aiFonts"
      @menu="showMobileMenu = true"
      @cart="router.push('/catalog/bolsa')"
    />

    <!-- utility-search: Ferreter�a/cat�logo grande, barra b�squeda 2 filas -->
    <HeaderUtilitySearch
      v-else-if="headerConfig.style === 'utility-search'"
      :storeName="storeName"
      :logoUrl="storeConfig.logo_url"
      :cartCount="cartCount"
      v-model="searchQuery"
      :palette="aiPalette"
      :fonts="aiFonts"
      class="fixed top-8 left-0 right-0 z-50"
      @menu="showMobileMenu = true"
      @cart="router.push('/catalog/bolsa')"
    />

    <!-- DEFAULT (null): Header original del sistema � Retail Fashion / Kharis-inspired -->
    <!-- centered-serif: Distribuidora / Boutique � Kharis-style (componente externo) -->
    <HeaderCenteredSerif
      v-else-if="headerConfig.style === 'centered-serif'"
      :storeName="storeName"
      :storeSubtitle="storeConfig.ai_brand?.tagline || storeConfig.category || ''"
      :logoUrl="storeConfig.logo_url"
      :cartCount="cartCount"
      v-model="searchQuery"
      :palette="aiPalette"
      :fonts="aiFonts"
      class="fixed top-8 left-0 right-0 z-50"
      @menu="showMobileMenu = true"
      @cart="router.push('/catalog/bolsa')"
    />

    <!-- DEFAULT (null): Header original del sistema � Retail Fashion / Kharis-inspired -->
    <header
      v-else
      ref="stickyHeader"
      class="fixed top-8 left-0 right-0 z-50 transition-all duration-300"
      :style="{ backgroundColor: aiPalette.background || '#ffffff' }"
      style="box-shadow: 0 1px 3px rgba(0,0,0,0.08);"
    >
      <!-- L�nea decorativa superior sutil -->
      <div class="h-[1px] w-full bg-gradient-to-r from-transparent via-gray-200 to-transparent"></div>
      
      <div class="w-full px-4 lg:px-8 h-14 lg:h-[72px] flex items-center justify-between">
        
        <!-- Left: Men� Hamburguesa (L�neas finas elegantes) -->
        <div class="flex items-center gap-2 w-[72px]">
          <button 
            @click="showMobileMenu = !showMobileMenu"
            class="lg:hidden w-10 h-10 flex items-center justify-center -ml-1"
          >
            <svg class="w-[22px] h-[22px]" :style="{ color: aiPalette.text_dark }" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.2">
              <path stroke-linecap="round" d="M4 7h16M4 12h16M4 17h16" />
            </svg>
          </button>
        </div>

        <!-- CENTER: Nombre del Comercio - Tipograf�a Serif Elegante -->
        <div class="flex-1 flex flex-col items-center justify-center lg:justify-start lg:flex-none lg:absolute lg:left-1/2 lg:-translate-x-1/2">
          <h1 
            class="text-[22px] lg:text-[30px] text-gray-900 leading-none"
            :style="{ fontFamily: aiFonts.heading + ', Georgia, Times New Roman, serif', fontWeight: 600, letterSpacing: layoutConfig.editorial_mood === 'luxury' ? '0.08em' : '0.02em' }"
          >
            {{ storeName }}
          </h1>
        </div>

        <!-- Right: Lupa + Bolsa (Elegantes, negro) -->
        <div class="flex items-center gap-0 w-[72px] justify-end">
          <!-- Lupa M�vil -->
          <button 
            @click="showMobileSearch = !showMobileSearch"
            class="lg:hidden w-10 h-10 flex items-center justify-center"
          >
            <svg class="w-[20px] h-[20px]" :style="{ color: aiPalette.text_dark }" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
            </svg>
          </button>

          <!-- Bolsa/Carrito -->
          <button 
            @click="router.push('/catalog/bolsa')"
            class="relative w-10 h-10 flex items-center justify-center"
          >
            <svg class="w-[20px] h-[20px]" :style="{ color: aiPalette.text_dark }" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993l1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 01-1.12-1.243l1.264-12A1.125 1.125 0 015.513 7.5h12.974c.576 0 1.059.435 1.119 1.007zM8.625 10.5a.375.375 0 11-.75 0 .375.375 0 01.75 0zm7.5 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
            </svg>
            <span 
              v-if="cartCount > 0"
              class="absolute -top-0.5 -right-0.5 text-white text-[8px] font-bold min-w-[16px] h-[16px] rounded-full flex items-center justify-center tracking-tight"
              :style="{ backgroundColor: aiPalette.primary }"
            >
              {{ cartCount }}
            </span>
          </button>
        </div>
      </div>

      <!-- Desktop: Barra de B�squeda + Navegaci�n -->
      <div class="hidden lg:flex justify-center border-t border-gray-100 py-2 px-8">
        <div class="relative w-full max-w-lg">
          <input 
            v-model="searchQuery"
            type="text" 
            placeholder="Buscar productos..."
            class="w-full h-10 pl-10 pr-4 border rounded-lg text-sm placeholder-gray-400 transition-all outline-none"
            :style="{ backgroundColor: aiPalette.background, borderColor: aiPalette.secondary, color: aiPalette.text_dark }"
          />
          <svg class="w-4 h-4 text-gray-400 absolute left-3.5 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
          </svg>
        </div>
      </div>

      <!-- L�nea decorativa inferior -->
      <div class="h-[1px] w-full bg-gray-100"></div>

      <!-- Barra de B�squeda M�vil Expandible -->
      <Transition name="slide-down">
        <div v-if="showMobileSearch" class="lg:hidden px-4 py-3 bg-white border-b border-gray-100">
          <div class="relative">
            <input 
              v-model="searchQuery"
              type="text" 
              placeholder="�Qu� est�s buscando?"
              class="w-full h-11 pl-10 pr-10 border rounded-full text-sm placeholder-gray-500 transition-all outline-none"
              :style="{ backgroundColor: aiPalette.background, borderColor: aiPalette.secondary, color: aiPalette.text_dark }"
              autofocus
            />
            <svg class="w-4 h-4 text-gray-400 absolute left-3.5 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
            <button @click="showMobileSearch = false" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 transition-colors">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
          </div>
        </div>
      </Transition>
    </header>

    <!-- HERO BANNER: Componente modular seg�n hero_style del layout AI -->
    <div :class="heroTopMargin">
      <!-- EDITORIAL: Alta moda, full-bleed fotograf�a, tipograf�a serif -->
      <HeroEditorial
        v-if="layoutConfig.hero_style === 'editorial' || layoutConfig.hero_style === 'full-bleed'"
        :headline="heroHeadlineDisplay"
        :subheadline="heroSubheadlineDisplay"
        :backgroundImage="currentHeroImage"
        :palette="aiPalette"
        :fonts="aiFonts"
        :ctaText="bannerCtaText"
        :isMobilePreview="isMobilePreview"
        :mood="layoutConfig.editorial_mood"
        :textPosition="layoutConfig.hero_text_position"
        @cta="scrollToProducts"
      />

      <!-- SPLIT: Minimalismo, 50/50 color + foto -->
      <HeroSplit
        v-else-if="layoutConfig.hero_style === 'split-portrait' || layoutConfig.hero_style === 'split'"
        :headline="heroHeadlineDisplay"
        :subheadline="heroSubheadlineDisplay"
        :backgroundImage="currentHeroImage"
        :palette="aiPalette"
        :fonts="aiFonts"
        :ctaText="bannerCtaText"
        :isMobilePreview="isMobilePreview"
        :mood="layoutConfig.editorial_mood"
        @cta="scrollToProducts"
      />

      <!-- STREETWEAR: Urbano, asim�trico, texto sobre imagen -->
      <HeroStreetwear
        v-else-if="layoutConfig.hero_style === 'streetwear' || layoutConfig.hero_style === 'urban'"
        :headline="heroHeadlineDisplay"
        :subheadline="heroSubheadlineDisplay"
        :backgroundImage="currentHeroImage"
        :palette="aiPalette"
        :fonts="aiFonts"
        :ctaText="bannerCtaText"
        :isMobilePreview="isMobilePreview"
        @cta="scrollToProducts"
      />

      <!-- FOCUS: Conversi�n directa, copy + producto destacado -->
      <HeroFocus
        v-else-if="layoutConfig.hero_style === 'centered-minimal' || layoutConfig.hero_style === 'focus'"
        :headline="heroHeadlineDisplay"
        :subheadline="heroSubheadlineDisplay"
        :backgroundImage="currentHeroImage"
        :palette="aiPalette"
        :fonts="aiFonts"
        :ctaText="bannerCtaText"
        :badge="bannerCtaText ? 'Novedad' : 'Nueva Colección'"
        :productCount="catalogProducts.length"
        :isMobilePreview="isMobilePreview"
        @cta="scrollToProducts"
      />

      <!-- PORTRAIT: Retrato full-bleed, tipografía mixta sans+serif, doble CTA rectangular + trust strip -->
      <HeroPortrait
        v-else-if="layoutConfig.hero_style === 'portrait'"
        :headline="heroHeadlineDisplay"
        :subheadline="heroSubheadlineDisplay"
        :backgroundImage="currentHeroImage"
        :palette="aiPalette"
        :fonts="aiFonts"
        :ctaText="bannerCtaText"
        :ctaSecondary="bannerCtaSecondary"
        :trustMessages="storeConfig.ai_value_messages || []"
        :isMobilePreview="isMobilePreview"
        @cta="scrollToProducts"
        @ctaSecondary="scrollToProducts"
      />

      <!-- FALLBACK: Editorial por defecto si hero_style no reconocido -->
      <HeroEditorial
        v-else
        :headline="heroHeadlineDisplay"
        :subheadline="heroSubheadlineDisplay"
        :backgroundImage="currentHeroImage"
        :palette="aiPalette"
        :fonts="aiFonts"
        :ctaText="bannerCtaText"
        :isMobilePreview="isMobilePreview"
        :mood="layoutConfig.editorial_mood"
        :textPosition="layoutConfig.hero_text_position"
        @cta="scrollToProducts"
      />
    </div>

    <!-- HERO PRODUCT SPOTLIGHT: Productos visibles desde el primer scroll -->
    <!-- Oculto en portrait: la trust strip integrada reemplaza este bloque -->
    <section v-if="heroProducts.length > 0 && layoutConfig.hero_style !== 'portrait'" class="relative z-20 -mt-6 sm:-mt-10 md:-mt-14 lg:ml-64 px-3 lg:px-8">
      <div class="grid grid-cols-3 gap-2 sm:gap-3 lg:gap-4">
        <article
          v-for="product in heroProducts"
          :key="'hero-product-' + product.id"
          @click="openProductDetails(product)"
          class="group cursor-pointer rounded-xl sm:rounded-2xl overflow-hidden border shadow-lg shadow-black/15"
          :style="{ backgroundColor: aiPalette.background, borderColor: aiPalette.secondary + '66' }"
        >
          <div class="relative h-[80px] sm:h-28 md:h-32 overflow-hidden">
            <img
              v-if="getProductImage(product)"
              :src="getProductImage(product)"
              :alt="product.name"
              class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105"
            />
            <div v-else class="w-full h-full bg-gray-100 flex items-center justify-center">
              <svg class="w-6 h-6 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993l1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 01-1.12-1.243l1.264-12A1.125 1.125 0 015.513 7.5h12.974c.576 0 1.059.435 1.119 1.007z" /></svg>
            </div>
          </div>
          <div class="px-2 sm:px-3 py-2 sm:py-2.5">
            <p class="text-[8px] sm:text-[10px] uppercase tracking-[0.08em] mb-0.5 truncate" :style="{ color: aiPalette.primary + '99' }">{{ product.category || 'Colección' }}</p>
            <h3 class="text-[11px] sm:text-sm leading-snug truncate" :style="{ fontFamily: aiFonts.heading + ', serif', fontWeight: 500, color: aiPalette.text_dark }">{{ product.name }}</h3>
            <p class="text-[11px] sm:text-sm font-bold mt-0.5" :style="{ color: aiPalette.text_dark }">{{ storeConfig.currency_symbol }}{{ formatPrice(product.price) }}</p>
          </div>
        </article>
      </div>
    </section>

    <!-- TRUST STRIP: Sistema modular � aplica a todos los estilos de hero -->
    <div class="lg:ml-64">
      <TrustStripMarquee
        v-if="trustStripStyle === 'marquee'"
        :items="trustStripItems"
        :bgColor="aiPalette.text_dark"
        :textColor="'#ffffff'"
        :accentColor="aiPalette.primary"
        :animate="true"
      />
      <TrustStripDarkContrast
        v-else-if="trustStripStyle === 'dark-contrast'"
        :items="trustStripItems"
        :bgColor="aiPalette.text_dark"
        :textColor="'#ffffff'"
        :iconColor="aiPalette.primary || '#ffffff'"
      />
      <TrustStripDivided
        v-else-if="trustStripStyle === 'divided'"
        :items="trustStripItems"
        :bgColor="aiPalette.background"
        :textColor="aiPalette.text_dark"
        :iconColor="aiPalette.primary"
        :iconBgColor="aiPalette.primary + '18'"
        :dividerColor="aiPalette.secondary + '55'"
      />
      <TrustStripSoftPills
        v-else-if="trustStripStyle === 'soft-pills'"
        :items="trustStripItems"
        :bgColor="aiPalette.background"
        :pillBgColor="'#ffffff'"
        :textColor="aiPalette.text_dark"
        :iconColor="aiPalette.primary"
      />
      <!-- Default / minimal-border -->
      <TrustStripMinimalBorder
        v-else
        :items="trustStripItems"
        :bgColor="aiPalette.background"
        :textColor="aiPalette.text_dark"
        :iconColor="aiPalette.text_dark"
        :borderColor="aiPalette.secondary + '55'"
      />
    </div>

    <!-- HOOK / SPOTLIGHT: Bloque pre-cat�logo modular (reemplaza Brand Story gen�rico) -->
    <div class="lg:ml-64">
      <!-- editorial-story: Boutique / Lencer�a / Alta Costura -->
      <HookEditorialStory
        v-if="hookStyle === 'editorial-story'"
        :headline="bannerHeadline"
        :body="hookBodyText"
        :label="storeConfig.category || 'Brand Story'"
        ctaText="Ver Colecci�n"
        :image="storyImageSlots[0]"
        :palette="aiPalette"
        :fonts="aiFonts"
        @cta="scrollToProducts"
      />
      <!-- urban-lookbook: Streetwear / Sneakers / Drop -->
      <HookUrbanLookbook
        v-else-if="hookStyle === 'urban-lookbook'"
        :headline="bannerHeadline"
        :subheadline="storySnippet.slice(0, 60)"
        :dropLabel="storeConfig.category || 'Nueva Colecci�n'"
        ctaText="Shop Now"
        :images="storyImageSlots"
        :video="storeConfig.catalog_media?.lookbook_video || ''"
        :image="storyImageSlots[0]"
        :palette="aiPalette"
        :fonts="aiFonts"
        @cta="scrollToProducts"
      />
      <!-- dynamic-bento: Deportivo / Tech / Consumo -->
      <HookDynamicBento
        v-else-if="hookStyle === 'dynamic-bento'"
        :headline="bannerHeadline"
        :subheadline="hookBodyText.slice(0, 55)"
        :benefit="storeConfig.ai_banner_texts?.cta_secondary || 'Alto\nRendimiento'"
        :label="storeConfig.category || 'Colecci�n 2026'"
        ctaText="Ver Colecci�n"
        :image="storyImageSlots[0]"
        :detailImage="storyImageSlots[1]"
        :palette="aiPalette"
        :fonts="aiFonts"
        @cta="scrollToProducts"
      />
    </div>

    <!-- BARRA STICKY: Filtrar + Ordenar (M�vil) - S�lida -->
    <div 
      class="lg:hidden sticky top-[90px] z-40 border-t border-b border-gray-200"
      :style="{ backgroundColor: aiPalette.background || '#ffffff', boxShadow: '0 2px 12px rgba(0,0,0,0.07)' }"
    >
      <div class="flex">
        <!-- Bot�n FILTRAR (50%) -->
        <button 
          @click="showMobileFilters = true"
          class="flex-1 h-11 flex items-center justify-center gap-2 text-[11px] font-semibold text-gray-700 uppercase tracking-[0.1em] transition-colors hover:bg-gray-50 active:bg-gray-100"
        >
          <svg class="w-[15px] h-[15px]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.3">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6h9.75M10.5 6a1.5 1.5 0 11-3 0m3 0a1.5 1.5 0 10-3 0M3.75 6H7.5m3 12h9.75m-9.75 0a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m-3.75 0H7.5m9-6h3.75m-3.75 0a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m-9.75 0h9.75" />
          </svg>
          FILTRAR
        </button>
        <!-- Separador central fino -->
        <div class="w-px h-6 bg-gray-200 self-center"></div>
        <!-- Bot�n ORDENAR (50%) -->
        <button 
          @click="showSortModal = true"
          class="flex-1 h-11 flex items-center justify-center gap-2 text-[11px] font-semibold text-gray-700 uppercase tracking-[0.1em] transition-colors hover:bg-gray-50 active:bg-gray-100"
        >
          <svg class="w-[15px] h-[15px]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.3">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3 7.5L7.5 3m0 0L12 7.5M7.5 3v13.5m13.5 0L16.5 21m0 0L12 16.5m4.5 4.5V7.5" />
          </svg>
          ORDENAR
        </button>
      </div>
    </div>

    <!-- SIDEBAR LATERAL (Desktop) - Premium Minimalista -->
    <aside v-if="!isMobilePreview" class="hidden lg:block fixed left-0 top-[154px] bottom-0 w-64 bg-white border-r border-gray-100 overflow-y-auto z-30 px-6 py-8">
      <!-- Filtro por Categor�a - Minimalista con l�nea vertical -->
      <div class="mb-8">
        <h3 class="text-xs font-bold text-gray-400 mb-5 uppercase tracking-widest">Categor�as</h3>
        <div class="space-y-0.5">
          <button
            @click="selectedCategory = null"
            class="w-full text-left py-2.5 text-sm transition-all duration-200 flex items-center gap-3 relative"
            :class="selectedCategory === null 
                ? 'font-semibold' 
              : 'text-gray-500 hover:text-gray-900'"
              :style="selectedCategory === null ? { color: aiPalette.text_dark } : {}"
          >
            <span 
              class="absolute left-0 top-1/2 -translate-y-1/2 w-0.5 h-5 transition-opacity duration-200"
              :style="{ backgroundColor: aiPalette.primary }"
              :class="selectedCategory === null ? 'opacity-100' : 'opacity-0'"
            ></span>
            <span class="pl-4">Todas</span>
          </button>
          <button
            v-for="cat in categories"
            :key="cat.id"
            @click="selectedCategory = cat.id"
            class="w-full text-left py-2.5 text-sm transition-all duration-200 flex items-center gap-3 relative"
            :class="selectedCategory === cat.id 
                ? 'font-semibold' 
              : 'text-gray-500 hover:text-gray-900'"
              :style="selectedCategory === cat.id ? { color: aiPalette.text_dark } : {}"
          >
            <span 
              class="absolute left-0 top-1/2 -translate-y-1/2 w-0.5 h-5 transition-opacity duration-200"
              :style="{ backgroundColor: aiPalette.primary }"
              :class="selectedCategory === cat.id ? 'opacity-100' : 'opacity-0'"
            ></span>
            <span class="pl-4">{{ cat.name }}</span>
          </button>
        </div>
      </div>

      <!-- Filtro por Rango de Precio - Slider Simple (Solo M�ximo) -->
      <div class="mb-8">
        <h3 class="text-xs font-bold text-gray-400 mb-5 uppercase tracking-widest">Precio</h3>
        <div class="space-y-4">
          <!-- Single Range Slider -->
          <div class="relative h-1.5 bg-gray-200 rounded-full">
            <div 
              class="absolute left-0 h-full rounded-full transition-all"
              :style="{ backgroundColor: aiPalette.primary, width: ((priceRange.max - minProductPrice) / (maxProductPrice - minProductPrice)) * 100 + '%' }"
            ></div>
            <input 
              type="range" 
              :min="minProductPrice" 
              :max="maxProductPrice" 
              v-model.number="priceRange.max"
              class="absolute w-full h-1.5 appearance-none bg-transparent cursor-pointer"
              style="appearance: none; -webkit-appearance: none;"
            />
          </div>
          <!-- Valor del rango -->
          <div class="flex items-center justify-between text-sm">
            <span class="text-gray-400">{{ storeConfig.currency_symbol }}0</span>
            <span class="font-semibold" :style="{ color: aiPalette.text_dark }">Hasta {{ storeConfig.currency_symbol }}{{ formatPrice(priceRange.max) }}</span>
          </div>
        </div>
      </div>

      <!-- Ordenar por -->
      <div class="mb-8">
        <h3 class="text-xs font-bold text-gray-400 mb-5 uppercase tracking-widest">Ordenar por</h3>
        <div class="space-y-3">
          <label class="flex items-center gap-3 cursor-pointer group">
            <div class="relative flex items-center">
              <input 
                type="radio" 
                name="sort" 
                value="" 
                v-model="sortOrder"
                class="peer h-4 w-4 cursor-pointer appearance-none rounded-full border border-gray-300 transition-all"
                :style="{ '--primary-color': primaryColor }"
                style="accent-color: var(--primary-color);"
              />
              <span class="absolute w-2 h-2 rounded-full opacity-0 peer-checked:opacity-100 transition-opacity duration-200 top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2" :style="{ backgroundColor: primaryColor }"></span>
            </div>
            <span class="text-sm text-gray-600 group-hover:text-gray-900 transition-colors">Relevancia</span>
          </label>
          
          <label class="flex items-center gap-3 cursor-pointer group">
            <div class="relative flex items-center">
              <input 
                type="radio" 
                name="sort" 
                value="price-asc" 
                v-model="sortOrder"
                class="peer h-4 w-4 cursor-pointer appearance-none rounded-full border border-gray-300 transition-all"
              />
              <span class="absolute w-2 h-2 rounded-full opacity-0 peer-checked:opacity-100 transition-opacity duration-200 top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2" :style="{ backgroundColor: primaryColor }"></span>
            </div>
            <span class="text-sm text-gray-600 group-hover:text-gray-900 transition-colors">Menor precio</span>
          </label>

          <label class="flex items-center gap-3 cursor-pointer group">
            <div class="relative flex items-center">
              <input 
                type="radio" 
                name="sort" 
                value="price-desc" 
                v-model="sortOrder"
                class="peer h-4 w-4 cursor-pointer appearance-none rounded-full border border-gray-300 transition-all"
              />
              <span class="absolute w-2 h-2 rounded-full opacity-0 peer-checked:opacity-100 transition-opacity duration-200 top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2" :style="{ backgroundColor: primaryColor }"></span>
            </div>
            <span class="text-sm text-gray-600 group-hover:text-gray-900 transition-colors">Mayor precio</span>
          </label>

          <label class="flex items-center gap-3 cursor-pointer group">
            <div class="relative flex items-center">
              <input 
                type="radio" 
                name="sort" 
                value="name-asc" 
                v-model="sortOrder"
                class="peer h-4 w-4 cursor-pointer appearance-none rounded-full border border-gray-300 transition-all"
              />
              <span class="absolute w-2 h-2 rounded-full opacity-0 peer-checked:opacity-100 transition-opacity duration-200 top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2" :style="{ backgroundColor: primaryColor }"></span>
            </div>
            <span class="text-sm text-gray-600 group-hover:text-gray-900 transition-colors">Nombre (A-Z)</span>
          </label>
        </div>
      </div>

      <!-- Filtro de Disponibilidad -->
      <div>
        <h3 class="text-xs font-bold text-gray-400 mb-4 uppercase tracking-widest">Filtros</h3>
        <label class="flex items-center gap-3 cursor-pointer group">
          <div class="relative flex items-center">
            <input 
              type="checkbox" 
              v-model="showOnlyAvailable"
              class="peer h-5 w-5 cursor-pointer appearance-none rounded border border-gray-300 transition-all"
              :style="{ backgroundColor: showOnlyAvailable ? primaryColor : 'transparent', borderColor: showOnlyAvailable ? primaryColor : undefined }"
            />
            <svg class="absolute w-3.5 h-3.5 text-white opacity-0 peer-checked:opacity-100 transition-opacity duration-200 top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 pointer-events-none" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
              <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
            </svg>
          </div>
          <span class="text-sm text-gray-600 group-hover:text-gray-900 transition-colors">Solo con stock</span>
        </label>
      </div>

      <!-- Bot�n Limpiar Filtros -->
      <button
        v-if="selectedCategory || showOnlyAvailable || sortOrder"
        @click="clearFilters"
        class="w-full px-4 py-2.5 text-xs font-bold uppercase tracking-wide text-red-500 hover:text-white hover:bg-red-500 rounded-lg transition-all border border-red-200 hover:border-red-500"
      >
        Limpiar filtros
      </button>
    </aside>

    <!-- �REA PRINCIPAL: Productos -->
    <section ref="productsSection" class="lg:ml-64 pt-3 px-4 lg:px-8 pb-8">
      
      <!-- Header de Cat�logo -->
      <div class="mb-5 pb-4 border-b border-gray-200 flex flex-col gap-3 lg:flex-row lg:items-end lg:justify-between">
        <div>
          <p class="text-[10px] uppercase tracking-[0.22em] font-semibold" :style="{ color: aiPalette.primary }">Cat�logo Curado</p>
          <h3 class="mt-1 text-2xl md:text-3xl text-gray-900" :style="{ fontFamily: aiFonts.heading + ', serif', fontWeight: 500 }">
            {{ selectedCategory !== null ? activeCategoryName : (bannerCtaText || 'Compra por estilo') }}
          </h3>
          <p class="mt-1 text-sm text-gray-500">
            {{ filteredProducts.length }} productos
            <span v-if="selectedCategory !== null || showOnlyAvailable || sortOrder" class="font-semibold" :style="{ color: aiPalette.primary }">
              � filtrados
            </span>
          </p>
        </div>

        <button
          v-if="selectedCategory !== null || showOnlyAvailable || sortOrder || searchQuery"
          @click="clearFilters"
          class="px-4 py-2 rounded-xl text-xs font-semibold uppercase tracking-[0.14em] border transition-colors"
          :style="{ borderColor: aiPalette.secondary, color: aiPalette.text_dark }"
        >
          Reiniciar vista
        </button>
      </div>

      <!-- GRID DE PRODUCTOS: E-commerce Premium con Hover Effects -->
      <div :class="gridClassesPremium">
        <TransitionGroup name="list">
          <div 
            v-for="product in filteredProducts" 
            :key="product.id"
            class="group"
          >
            <div 
              class="bg-white rounded-2xl border border-gray-100 overflow-hidden h-full flex flex-col cursor-pointer transition-all duration-300 hover:-translate-y-1 hover:shadow-xl hover:shadow-black/10" 
              @click="openProductDetails(product)"
            >
              
              <!-- Product Image - Aspect 3:4 Uniforme -->
              <div class="relative aspect-[3/4] overflow-hidden bg-gray-50">
                
                <!-- Imagen del Producto -->
                <img 
                  v-if="(product.images && product.images.length > 0) || (product.image_url && product.image_url !== 'https://via.placeholder.com/400' && !imageErrors[product.id])"
                  :src="product.images && product.images.length > 0 ? product.images[0] : product.image_url"
                  :alt="product.name"
                  @error="handleImageError(product.id)"
                  class="w-full h-full object-cover transition-transform duration-700 ease-out group-hover:scale-[1.06]"
                />
                
                <!-- Placeholder Elegante -->
                <div v-else class="w-full h-full flex items-center justify-center bg-gray-100">
                  <svg class="w-10 h-10 text-gray-300" viewBox="0 0 24 24" fill="none">
                    <path d="M5 9h14l1 12H4L5 9z" fill="currentColor" opacity="0.3"/>
                    <path d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" fill="none"/>
                  </svg>
                </div>
                
                <!-- Bot�n Agregar - Minimal -->
                <button
                  @click.stop="addToCart(product)"
                  :disabled="product.stock === 0"
                  class="absolute bottom-2.5 right-2.5 w-9 h-9 rounded-full flex items-center justify-center transition-all duration-300 z-10"
                  :class="product.stock > 0 
                    ? 'bg-white text-gray-600 hover:text-white shadow-sm hover:shadow-md hover:scale-110' 
                    : 'bg-gray-200 text-gray-400 cursor-not-allowed'"
                  :style="product.stock > 0 ? { '--tw-bg-opacity': '1', backgroundColor: 'white' } : {}"
                  @mouseenter="product.stock > 0 ? $event.currentTarget.style.backgroundColor = aiPalette.primary : null"
                  @mouseleave="product.stock > 0 ? $event.currentTarget.style.backgroundColor = 'white' : null"
                >
                  <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                  </svg>
                </button>

                <span class="absolute top-2.5 right-2.5 text-[9px] uppercase tracking-[0.12em] bg-white/90 text-gray-800 px-2 py-1 rounded-full font-semibold">
                  Ver
                </span>

                <!-- Badges (Esquina Superior Izquierda) - Discretos -->
                <div class="absolute top-0 left-0 flex flex-col">
                  <span v-if="product.stock <= 5 && product.stock > 0" class="px-2 py-1 bg-gray-900/85 text-white text-[8px] font-semibold uppercase tracking-wider">
                    Solo {{ product.stock }}
                  </span>
                  <span v-else-if="product.stock === 0" class="px-2 py-1 bg-gray-900/85 text-white text-[8px] font-semibold uppercase tracking-wider">
                    Agotado
                  </span>
                  <span v-if="product.is_new" class="px-2 py-1 bg-gray-900/85 text-white text-[8px] font-semibold uppercase tracking-wider">
                    Nuevo
                  </span>
                </div>
              </div>

              <!-- Product Info - Compacto y Alineado -->
              <div class="pt-3 pb-4 px-3">
                <p class="text-[10px] uppercase tracking-[0.18em] text-gray-400 mb-1 truncate">{{ product.category || 'Colecci�n' }}</p>
                <h3 class="text-[13px] font-normal text-gray-700 truncate leading-snug mb-1" :style="{ fontFamily: aiFonts.heading + ', serif' }">
                  {{ product.name }}
                </h3>
                <div class="flex items-center justify-between gap-2">
                  <p class="text-sm font-bold text-gray-900">
                  {{ storeConfig.currency_symbol }}{{ formatPrice(product.price) }}
                  </p>
                  <span class="text-[11px] font-medium" :style="{ color: aiPalette.primary }">Comprar</span>
                </div>
              </div>
            </div>
          </div>
        </TransitionGroup>
      </div>

      <!-- Empty State Elegante -->
      <div v-if="filteredProducts.length === 0" class="text-center py-20">
        <svg class="w-16 h-16 mx-auto text-gray-200 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1">
          <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993l1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 01-1.12-1.243l1.264-12A1.125 1.125 0 015.513 7.5h12.974c.576 0 1.059.435 1.119 1.007z" />
        </svg>
        <p class="text-gray-400 text-sm font-light tracking-wide">No hay productos disponibles</p>
      </div>
    </section>

    <!-- ABOUT US SECTION (AI Generated) -->
    <section v-if="storeConfig.ai_about_us" class="border-t py-12" :style="{ backgroundColor: aiPalette.background, borderColor: aiPalette.secondary }">
      <div class="lg:ml-64 px-4 lg:px-8">
        <div class="rounded-3xl border border-gray-200 overflow-hidden" :style="{ backgroundColor: '#ffffff' }">
          <div class="grid md:grid-cols-[1.2fr,1fr] gap-0">
            <div class="p-6 md:p-8">
              <p class="text-[10px] uppercase tracking-[0.24em] font-semibold" :style="{ color: aiPalette.primary }">Nuestra esencia</p>
              <h3 
                class="text-2xl md:text-3xl text-gray-900 mt-2 mb-4"
                :style="{ fontFamily: aiFonts.heading + ', serif', fontWeight: 500 }"
              >
                Nuestra Historia
              </h3>
              <p 
                class="text-sm md:text-base text-gray-600 leading-relaxed whitespace-pre-line"
                :style="{ fontFamily: aiFonts.body + ', sans-serif' }"
              >
                {{ storeConfig.ai_about_us }}
              </p>

              <div v-if="storeConfig.ai_value_messages && storeConfig.ai_value_messages.length > 0" class="mt-6 space-y-2.5">
                <div
                  v-for="(msg, i) in storeConfig.ai_value_messages"
                  :key="'history-value-' + i"
                  class="flex items-start gap-2.5 text-sm text-gray-700"
                >
                  <span class="mt-1 w-1.5 h-1.5 rounded-full" :style="{ backgroundColor: aiPalette.primary }"></span>
                  <span :style="{ fontFamily: aiFonts.body + ', sans-serif' }">{{ msg }}</span>
                </div>
              </div>
            </div>

            <div class="relative min-h-[260px] md:min-h-full">
              <img v-if="storyImageSlots[0]" :src="storyImageSlots[0]" alt="Colecci�n principal" class="w-full h-full object-cover" />
              <div v-else class="w-full h-full bg-gray-100"></div>
              <div class="absolute inset-0 bg-gradient-to-t from-black/40 via-black/10 to-transparent"></div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- FOOTER: Powered by 105 POS -->
    <footer class="border-t py-8 text-center" :class="{ 'mb-16': cartCount > 0 }" :style="{ backgroundColor: aiPalette.background, borderColor: aiPalette.secondary }">
      <p class="text-xs text-gray-400 tracking-wide">
        Tecnología por
        <a 
          href="https://105pos.pro/register" 
          target="_blank" 
          rel="noopener noreferrer"
          class="font-semibold text-gray-600 hover:text-gray-900 transition-colors"
        >105 POS</a>
      </p>
    </footer>

    <!-- WHATSAPP BUTTON - En móvil sube con carrito, en PC fijo -->
    <a 
      v-if="storeConfig.whatsapp_number"
      :href="`https://wa.me/${storeConfig.whatsapp_number.replace(/[^0-9]/g, '')}?text=Hola, me interesa hacer un pedido`"
      target="_blank"
      class="fixed right-4 lg:right-[30px] z-[60] w-12 h-12 bg-[#25D366] hover:bg-[#1ebe57] text-white rounded-full flex items-center justify-center transform hover:scale-105 transition-all duration-300 bottom-6 lg:bottom-[30px]"
      :class="{ 'bottom-[76px]': cartCount > 0, 'lg:bottom-[30px]': true }"
      style="box-shadow: 0 4px 16px rgba(37, 211, 102, 0.25), 0 8px 32px rgba(37, 211, 102, 0.12);"
      title="Contactar por WhatsApp"
    >
      <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893A11.821 11.821 0 0020.885 3.515"/>
      </svg>
    </a>

    <!-- STICKY BOTTOM ACTION BAR - Solo Móvil (Desktop usa Mini-Cart en Header) -->
    <Transition name="slide-up">
      <div 
        v-if="cartCount > 0"
        class="fixed bottom-0 left-0 right-0 z-[55] px-4 py-3 flex items-center justify-between lg:hidden"
        :style="{ backgroundColor: aiPalette.background }"
        style="box-shadow: 0 -4px 16px rgba(0,0,0,0.08);"
      >
        <!-- Izquierda: Total -->
        <div>
          <p class="text-[10px] text-gray-500 uppercase tracking-wide font-medium">Total</p>
          <p class="text-lg font-bold text-gray-900">{{ storeConfig.currency_symbol }}{{ formatPrice(cartTotal) }}</p>
        </div>
        
        <!-- Derecha: Botón VER BOLSA -->
        <button 
          @click="router.push('/catalog/bolsa')"
          class="text-white px-6 py-3 text-sm font-semibold uppercase tracking-wide transition-colors flex items-center gap-2"
          :style="{ backgroundColor: aiPalette.primary }"
        >
          <span class="bg-white text-gray-900 text-[10px] font-bold w-5 h-5 rounded-full flex items-center justify-center">
            {{ cartCount }}
          </span>
          VER BOLSA
        </button>
      </div>
    </Transition>

    <!-- OFF-CANVAS DRAWER MENU (Menú Lateral de Categorías) -->
    <Transition name="fade">
      <div v-if="showMobileMenu" class="fixed inset-0 bg-black/50 z-[160]" @click="showMobileMenu = false"></div>
    </Transition>
    <Transition name="drawer-left">
      <div v-if="showMobileMenu" class="fixed top-0 left-0 bottom-0 w-[280px] bg-white z-[161] flex flex-col shadow-2xl">
        <!-- Header -->
        <div class="flex items-center justify-between px-5 h-14 border-b border-gray-100 flex-shrink-0">
          <h2 
            class="text-[15px] text-gray-900 tracking-[0.06em] uppercase"
            style="font-family: 'Playfair Display', 'Georgia', serif; font-weight: 600;"
          >
            {{ storeConfig.store_name || 'Menú' }}
          </h2>
          <button 
            @click="showMobileMenu = false"
            class="w-9 h-9 flex items-center justify-center text-gray-400 hover:text-gray-900 transition-colors"
          >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
              <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>

        <!-- Categorías -->
        <nav class="flex-1 overflow-y-auto">
          <ul>
            <li>
              <button 
                @click="selectedCategory = null; showMobileMenu = false"
                class="w-full text-left px-5 py-4 text-sm font-medium transition-colors border-b border-gray-100 flex items-center justify-between"
                :class="selectedCategory === null ? 'text-gray-900 bg-gray-50' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'"
              >
                <span>Todas las categorías</span>
                <span v-if="selectedCategory === null" class="w-1.5 h-1.5 bg-gray-900 rounded-full"></span>
              </button>
            </li>
            <li v-for="cat in categories" :key="'drawer-cat-' + cat.id">
              <button 
                @click="selectedCategory = cat.id; showMobileMenu = false"
                class="w-full text-left px-5 py-4 text-sm font-medium transition-colors border-b border-gray-100 flex items-center justify-between"
                :class="selectedCategory === cat.id ? 'text-gray-900 bg-gray-50' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'"
              >
                <span>{{ cat.name }}</span>
                <span v-if="selectedCategory === cat.id" class="w-1.5 h-1.5 bg-gray-900 rounded-full"></span>
              </button>
            </li>
          </ul>
        </nav>

        <!-- Footer del drawer -->
        <div class="flex-shrink-0 px-5 py-4 border-t border-gray-100">
          <p class="text-[10px] text-gray-400 text-center tracking-wide">
            Tecnología por
            <a href="https://105pos.pro/register" target="_blank" rel="noopener noreferrer" class="font-semibold text-gray-500">105 POS</a>
          </p>
        </div>
      </div>
    </Transition>

    <!-- MOBILE FILTERS DRAWER (Solo M�vil) -->
    <Transition name="fade">
      <div v-if="showMobileFilters" class="lg:hidden fixed inset-0 bg-black/50 z-[150]" @click="showMobileFilters = false"></div>
    </Transition>
    <Transition name="slide-up">
      <div v-if="showMobileFilters" class="lg:hidden fixed bottom-0 left-0 right-0 bg-white z-[151] rounded-t-3xl max-h-[85vh] overflow-hidden flex flex-col">
        <!-- Header -->
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
          <h3 class="text-base font-semibold text-gray-900 uppercase tracking-wide">Filtros</h3>
          <button @click="showMobileFilters = false" class="w-8 h-8 flex items-center justify-center text-gray-400 hover:text-gray-900">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
              <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>
        
        <!-- Contenido de Filtros -->
        <div class="flex-1 overflow-y-auto px-6 py-5 space-y-6">
          <!-- Categorías -->
          <div>
            <h4 class="text-xs font-bold text-gray-400 mb-4 uppercase tracking-widest">Categorías</h4>
            <div class="space-y-1">
              <button
                @click="selectedCategory = null; showMobileFilters = false"
                class="w-full text-left py-3 text-sm transition-all flex items-center gap-3"
                :class="selectedCategory === null ? 'text-gray-900 font-semibold' : 'text-gray-500'"
              >
                <span class="w-5 h-5 border-2 rounded-full flex items-center justify-center" :class="selectedCategory === null ? 'border-gray-900' : 'border-gray-300'">
                  <span v-if="selectedCategory === null" class="w-2.5 h-2.5 bg-gray-900 rounded-full"></span>
                </span>
                Todas
              </button>
              <button
                v-for="cat in categories"
                :key="'mob-cat-'+cat.id"
                @click="selectedCategory = cat.id; showMobileFilters = false"
                class="w-full text-left py-3 text-sm transition-all flex items-center gap-3"
                :class="selectedCategory === cat.id ? 'text-gray-900 font-semibold' : 'text-gray-500'"
              >
                <span class="w-5 h-5 border-2 rounded-full flex items-center justify-center" :class="selectedCategory === cat.id ? 'border-gray-900' : 'border-gray-300'">
                  <span v-if="selectedCategory === cat.id" class="w-2.5 h-2.5 bg-gray-900 rounded-full"></span>
                </span>
                {{ cat.name }}
              </button>
            </div>
          </div>
          
          <!-- Precio - Slider Simple -->
          <div>
            <h4 class="text-xs font-bold text-gray-400 mb-4 uppercase tracking-widest">Precio máximo</h4>
            <div class="space-y-3">
              <div class="relative h-1.5 bg-gray-200 rounded-full">
                <div 
                  class="absolute left-0 h-full bg-gray-900 rounded-full"
                  :style="{ width: ((priceRange.max - minProductPrice) / (maxProductPrice - minProductPrice) * 100) + '%' }"
                ></div>
                <input 
                  type="range" 
                  :min="minProductPrice" 
                  :max="maxProductPrice" 
                  v-model.number="priceRange.max"
                  class="absolute w-full h-1.5 appearance-none bg-transparent cursor-pointer"
                  style="appearance: none; -webkit-appearance: none;"
                />
              </div>
              <div class="text-center">
                <span class="text-sm font-semibold text-gray-900">Hasta {{ storeConfig.currency_symbol }}{{ formatPrice(priceRange.max) }}</span>
              </div>
            </div>
          </div>
          
          <!-- Solo con stock -->
          <div>
            <label class="flex items-center gap-3 cursor-pointer">
              <input 
                type="checkbox" 
                v-model="showOnlyAvailable"
                class="w-5 h-5 rounded border-gray-300 text-gray-900 focus:ring-gray-900"
              />
              <span class="text-sm text-gray-700">Solo productos con stock</span>
            </label>
          </div>
        </div>
        
        <!-- Footer con botón -->
        <div class="flex-shrink-0 px-6 py-4 border-t border-gray-100 bg-gray-50">
          <button 
            @click="showMobileFilters = false"
            class="w-full py-3.5 bg-gray-900 hover:bg-black text-white text-sm font-medium uppercase tracking-wide transition-colors"
          >
            Ver {{ filteredProducts.length }} productos
          </button>
        </div>
      </div>
    </Transition>

    <!-- MOBILE SORT MODAL (Solo Móvil) -->
    <Transition name="fade">
      <div v-if="showSortModal" class="lg:hidden fixed inset-0 bg-black/50 z-[150]" @click="showSortModal = false"></div>
    </Transition>
    <Transition name="slide-up">
      <div v-if="showSortModal" class="lg:hidden fixed bottom-0 left-0 right-0 bg-white z-[151] rounded-t-3xl overflow-hidden">
        <!-- Header -->
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
          <h3 class="text-base font-semibold text-gray-900 uppercase tracking-wide">Ordenar por</h3>
          <button @click="showSortModal = false" class="w-8 h-8 flex items-center justify-center text-gray-400 hover:text-gray-900">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
              <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>
        
        <!-- Opciones de Ordenar -->
        <div class="px-6 py-4 space-y-1">
          <button
            @click="sortOrder = ''; showSortModal = false"
            class="w-full text-left py-3.5 text-sm transition-all flex items-center gap-3"
            :class="sortOrder === '' ? 'text-gray-900 font-semibold' : 'text-gray-500'"
          >
            <span class="w-5 h-5 border-2 rounded-full flex items-center justify-center" :class="sortOrder === '' ? 'border-gray-900 bg-gray-900' : 'border-gray-300'">
              <svg v-if="sortOrder === ''" class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
              </svg>
            </span>
            Relevancia
          </button>
          <button
            @click="sortOrder = 'price-asc'; showSortModal = false"
            class="w-full text-left py-3.5 text-sm transition-all flex items-center gap-3"
            :class="sortOrder === 'price-asc' ? 'text-gray-900 font-semibold' : 'text-gray-500'"
          >
            <span class="w-5 h-5 border-2 rounded-full flex items-center justify-center" :class="sortOrder === 'price-asc' ? 'border-gray-900 bg-gray-900' : 'border-gray-300'">
              <svg v-if="sortOrder === 'price-asc'" class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
              </svg>
            </span>
            Menor precio
          </button>
          <button
            @click="sortOrder = 'price-desc'; showSortModal = false"
            class="w-full text-left py-3.5 text-sm transition-all flex items-center gap-3"
            :class="sortOrder === 'price-desc' ? 'text-gray-900 font-semibold' : 'text-gray-500'"
          >
            <span class="w-5 h-5 border-2 rounded-full flex items-center justify-center" :class="sortOrder === 'price-desc' ? 'border-gray-900 bg-gray-900' : 'border-gray-300'">
              <svg v-if="sortOrder === 'price-desc'" class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
              </svg>
            </span>
            Mayor precio
          </button>
          <button
            @click="sortOrder = 'name-asc'; showSortModal = false"
            class="w-full text-left py-3.5 text-sm transition-all flex items-center gap-3"
            :class="sortOrder === 'name-asc' ? 'text-gray-900 font-semibold' : 'text-gray-500'"
          >
            <span class="w-5 h-5 border-2 rounded-full flex items-center justify-center" :class="sortOrder === 'name-asc' ? 'border-gray-900 bg-gray-900' : 'border-gray-300'">
              <svg v-if="sortOrder === 'name-asc'" class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
              </svg>
            </span>
            Nombre (A-Z)
          </button>
        </div>
        
        <!-- Espacio inferior seguro -->
        <div class="h-6"></div>
      </div>
    </Transition>

    <!-- Modal de Cantidad (Productos por peso/medida) -->
    <QuantityModal
      :show="showQuantityModal"
      :product="selectedProductForQuantity"
      @close="showQuantityModal = false"
      @confirm="handleQuantityConfirmed"
    />

    <!-- Modal de Selecci�n de Variantes (Fashion) -->
    <POSVariantSelector
      :show="showVariantModal"
      :product="selectedProductForVariants"
      @close="showVariantModal = false"
      @confirm="handleVariantConfirmed"
    />

    <!-- Toast Notification - Minimal Pill -->
    <Transition name="slide-up">
      <div 
        v-if="toast.show"
        class="fixed bottom-24 left-4 right-4 z-[200] bg-white px-4 py-3 rounded-2xl flex items-center gap-3 lg:left-auto lg:right-6 lg:max-w-sm"
        style="box-shadow: 0 4px 24px rgba(0,0,0,0.10), 0 1px 3px rgba(0,0,0,0.06);"
      >
        <div class="w-6 h-6 rounded-full bg-emerald-50 flex items-center justify-center flex-shrink-0">
          <svg class="w-3.5 h-3.5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
          </svg>
        </div>
        <span class="text-sm font-medium text-gray-800 flex-1">{{ toast.message }}</span>
        <button
          @click="router.push('/catalog/bolsa')"
          class="text-[11px] font-semibold uppercase tracking-wide text-gray-600 bg-gray-100 hover:bg-gray-200 px-3 py-1.5 rounded-lg transition-colors whitespace-nowrap"
        >
          Ver bolsa
        </button>
      </div>
    </Transition>

  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted, watch, nextTick, inject } from 'vue'
import { useRouter } from 'vue-router'
import axios from 'axios'
import QuantityModal from './QuantityModal.vue'
import POSVariantSelector from '../POSVariantSelector.vue'
import { useCatalogCart } from '../../stores/catalogCart.js'
import { HeroEditorial, HeroSplit, HeroStreetwear, HeroFocus, HeroPortrait } from './blocks/heroes/index.js'
import { HeaderEditorialCenter, HeaderRetailLeft, HeaderTransparentGlass, HeaderRetailOverlay, HeaderUtilitySearch, HeaderCenteredSerif } from './blocks/headers/index.js'
import { TrustStripDarkContrast, TrustStripMinimalBorder, TrustStripDivided, TrustStripMarquee, TrustStripSoftPills } from './blocks/trust-strips/index.js'
import { HookEditorialStory, HookUrbanLookbook, HookDynamicBento } from './blocks/hooks/index.js'
import { productUrl } from '../../utils/slugify.js'

const router = useRouter()
const isPreviewMode = inject('isPreviewMode', false)
const { cartItems, cartCount, addItem, removeItem, toast } = useCatalogCart()

const props = defineProps({
  storeConfig: {
    type: Object,
    required: true,
    default: () => ({
      primary_color: '#10B981',
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

// Estado
const isScrolled = ref(false)
const isLoadingBanner = ref(true)
const loadingImages = ref({})
const imageErrors = ref({})
const selectedCategory = ref(null)
const searchQuery = ref('')
const sortOrder = ref('') // Filtro de ordenamiento
const showOnlyAvailable = ref(false) // Filtro de disponibilidad

// Ticker de anuncios vertical - Use AI-generated or defaults
const defaultAnnouncements = [
  'Env�o Gratis en compras mayores a $150.000',
  'Nuevas Colecciones Disponibles',
  'Hasta 3 cuotas sin inter�s',
  'Devoluciones gratis en 30 d�as'
]
const announcements = computed(() => {
  const aiAnnouncements = props.storeConfig.ai_announcements
  if (aiAnnouncements && Array.isArray(aiAnnouncements) && aiAnnouncements.length > 0) {
    return aiAnnouncements
  }
  return defaultAnnouncements
})
const currentAnnouncement = ref(0)

// Rango de precios para filtro
const priceRange = ref({ min: 0, max: 1000000 })

const stickyHeader = ref(null)
const productsSection = ref(null)
const currentSlide = ref(0)

// ?? Estados para modales nuevos
const showQuantityModal = ref(false)
const selectedProductForQuantity = ref(null)
const showVariantModal = ref(false)
const selectedProductForVariants = ref(null)

// ?? Estados para UI Premium M�vil
const showMobileMenu = ref(false)
const showMobileSearch = ref(false)
const showMobileFilters = ref(false)
const showSortModal = ref(false)

// Color primario din�mico del storeConfig
const primaryColor = computed(() => props.storeConfig.primary_color || '#10B981')

// AI color palette with resilient fallbacks
const aiPalette = computed(() => {
  const palette = props.storeConfig.ai_color_palette || {}
  return {
    primary: palette.primary || primaryColor.value || '#10B981',
    secondary: palette.secondary || '#d1d5db',
    accent: palette.accent || '#111827',
    background: palette.background || '#ffffff',
    text_dark: palette.text_dark || '#111827',
    text_light: palette.text_light || '#f9fafb'
  }
})

// Global theme variables for mobile + desktop layout
const themeVars = computed(() => ({
  '--ai-primary': aiPalette.value.primary,
  '--ai-secondary': aiPalette.value.secondary,
  '--ai-accent': aiPalette.value.accent,
  '--ai-background': aiPalette.value.background,
  '--ai-text-dark': aiPalette.value.text_dark,
  '--ai-text-light': aiPalette.value.text_light,
  '--ai-font-heading': aiFonts.value.heading,
  '--ai-font-body': aiFonts.value.body,
}))

// AI-generated fonts with fallbacks
const aiFonts = computed(() => {
  const fonts = props.storeConfig.ai_fonts
  return {
    heading: fonts?.heading || 'Playfair Display',
    body: fonts?.body || 'Montserrat'
  }
})

// AI layout config - controls hero style, CTA variant, category layout
const layoutConfig = computed(() => {
  const cfg = props.storeConfig.ai_layout_config || {}
  // ?? TEST: descomenta UNA l�nea para probar cada hero
  const TEST_HERO = null               // null = usa el valor real de la BD
  // const TEST_HERO = 'editorial'        // HeroEditorial   � full-bleed moda/lifestyle
  //const TEST_HERO = 'split-portrait'   // HeroSplit        � 50/50 color+foto
  // const TEST_HERO = 'streetwear'       // HeroStreetwear  � urbano bold
  // const TEST_HERO = 'centered-minimal' // HeroFocus        � conversi�n directa
  // const TEST_HERO = 'portrait'         // HeroPortrait     � belleza/cabello, doble CTA + trust strip

  return {
    hero_style: TEST_HERO || cfg.hero_style || 'editorial',
    hero_cta_style: cfg.hero_cta_style || 'single-outline',
    hero_text_position: cfg.hero_text_position || 'bottom-left',
    category_style: cfg.category_style || 'horizontal-pills',
    editorial_mood: cfg.editorial_mood || 'luxury',
    ticker_style: cfg.ticker_style || 'muted-light',
    hero_content_density: cfg.hero_content_density || 'balanced'
  }
})

// AI header config � controla qu� navbar se renderiza + su ticker y animaci�n
const headerConfig = computed(() => {
  const cfg = props.storeConfig.ai_layout_config || {}
  // ?? TEST: descomenta UNA l�nea para probar cada header
   const TEST_HEADER = null                   // null = header original del sistema
  // const TEST_HEADER = 'editorial-center'  // HeaderEditorialCenter � alta moda, logo centrado serif
  // const TEST_HEADER = 'retail-left'       // HeaderRetailLeft      � consumo masivo, logo izquierda
  // const TEST_HEADER = 'transparent-glass' // HeaderTransparentGlass � cosm�tica ? pair con portrait/editorial
   // const TEST_HEADER = 'floating-pill'     // HeaderFloatingPill    � streetwear/tech, p�ldora flotante
  // const TEST_HEADER = 'utility-search'    // HeaderUtilitySearch   � ferreter�a, barra b�squeda 2 filas
  // const TEST_HEADER = 'centered-serif' // HeaderCenteredSerif � elegante, logo centrado serif, sin ticker (usa layoutConfig) 
  const style = TEST_HEADER || cfg.header_style || null

  // Cada header dicta su propio ticker: estilo visual + animaci�n
  const presets = {
    'editorial-center':   { tickerStyle: 'muted-light',  tickerMode: 'slide-left'  },
    'retail-left':        { tickerStyle: 'soft-primary',  tickerMode: 'slide-left'  },
    'transparent-glass':  { tickerStyle: 'contrast-dark', tickerMode: 'fade'        },
    'floating-pill':      { tickerStyle: 'contrast-dark', tickerMode: 'slide-down'  },
    'utility-search':     { tickerStyle: 'muted-light',   tickerMode: 'slide-left'  },
    'centered-serif':     { tickerStyle: 'muted-light',   tickerMode: 'slide-left'  },
  }

  return {
    style,
    tickerStyle: presets[style]?.tickerStyle || null,
    tickerMode:  presets[style]?.tickerMode  || 'slide-left',
  }
})

// Espaciado din�mico encima del hero seg�n el tipo de header activo
// - transparent-glass y floating-pill flotan sobre el hero (sin margen)
// - utility-search tiene 2 filas (ticker 32px + header 92px = 124px)
// - Resto de headers s�lidos: ticker 32px + header 56px = 88px mobile, 104px desktop
const heroTopMargin = computed(() => {
  const style = headerConfig.value.style
  if (style === 'transparent-glass' || style === 'floating-pill') return 'mt-0'
  if (style === 'utility-search') return 'mt-[124px]'
  return 'mt-[88px] lg:mt-[104px]'
})

// Hook / Spotlight: bloque pre-cat�logo que reemplaza el Brand Story gen�rico
const hookStyle = computed(() => {
  const cfg = props.storeConfig.ai_layout_config || {}
  // ?? TEST: descomenta UNA l�nea para probar cada bloque
   const TEST_HOOK = null                    // null = usa el valor real de la BD
  // const TEST_HOOK = 'editorial-story'    // HookEditorialStory  � boutique/lencer�a/alta costura
  // const TEST_HOOK = 'urban-lookbook'     // HookUrbanLookbook   � streetwear/sneakers/drop
  // const TEST_HOOK = 'dynamic-bento'      // HookDynamicBento    � deportivo/tech/consumo
  return TEST_HOOK || cfg.hook_style || 'editorial-story' // editorial-story como default
})

// Texto del cuerpo del Hook (usa ai_about_us como fuente, truncado para el bloque)
const hookBodyText = computed(() => {
  const about = (props.storeConfig.ai_about_us || '').trim()
  if (!about) return 'Cada pieza es pensada con materiales de primera calidad. Tu comodidad y estilo son nuestra obsesi�n desde el primer boceto.'
  return about.length > 200 ? `${about.slice(0, 200).trim()}...` : about
})

// Trust Strip: estilo visual y datos normalizados desde ai_value_messages
const trustStripStyle = computed(() => {
  const cfg = props.storeConfig.ai_layout_config || {}
  // ?? TEST: descomenta UNA l�nea para probar cada estilo
   const TEST_TRUST_STRIP = null              // null = usa el valor real de la BD
  // const TEST_TRUST_STRIP = 'dark-contrast'  // Kharis � fondo negro, texto blanco, scroll
  // const TEST_TRUST_STRIP = 'minimal-border' // Boutique/Zara � blanco, borde fino
  // const TEST_TRUST_STRIP = 'divided'        // Cosm�tica/Corp � gris sutil, divide-x
  // const TEST_TRUST_STRIP = 'marquee'        // Streetwear � texto en loop infinito
  // const TEST_TRUST_STRIP = 'soft-pills'     // Tech/R�pido � pastillas blancas
  return TEST_TRUST_STRIP || cfg.trust_strip_style || null
})

const trustStripItems = computed(() => {
  const msgs = props.storeConfig.ai_value_messages || []
  const iconMap = ['truck', 'shield', 'star', 'chat']
  if (msgs.length > 0) {
    return msgs.slice(0, 4).map((msg, i) => ({
      icon: iconMap[i] || 'star',
      label: msg.length > 22 ? msg.slice(0, 22) : msg,
      sublabel: ''
    }))
  }
  return [
    { icon: 'truck',  label: 'Env�o a todo el pa�s', sublabel: 'R�pido y seguro' },
    { icon: 'shield', label: 'Pago 100% seguro',      sublabel: 'SSL encriptado' },
    { icon: 'star',   label: 'Calidad garantizada',   sublabel: 'Productos originales' },
    { icon: 'chat',   label: 'Atenci�n personalizada', sublabel: 'Lun�S�b 8am�6pm' },
  ]
})

const tickerBarStyle = computed(() => {
  // El header dicta el estilo del ticker (si no, usa layoutConfig)
  const style = headerConfig.value.tickerStyle || layoutConfig.value.ticker_style
  if (style === 'contrast-dark') {
    return {
      backgroundColor: aiPalette.value.text_dark,
      borderColor: aiPalette.value.text_dark
    }
  }
  if (style === 'soft-primary') {
    return {
      backgroundColor: aiPalette.value.background || '#ffffff',
      borderColor: `${aiPalette.value.primary}55`
    }
  }
  return {
    backgroundColor: '#f6f5f3',
    borderColor: '#e6e3de'
  }
})

const tickerTextColor = computed(() => {
  const style = headerConfig.value.tickerStyle || layoutConfig.value.ticker_style
  return style === 'contrast-dark'
    ? aiPalette.value.text_light
    : aiPalette.value.text_dark
})

// Animaci�n del ticker seg�n el header
const tickerTransitionName = computed(() => {
  const mode = headerConfig.value.tickerMode
  if (mode === 'fade')        return 'ticker-fade'
  if (mode === 'slide-down')  return 'ticker-down'
  return 'ticker-train'  // slide-left (default)
})

const currentAnnouncementText = computed(() => {
  const text = announcements.value[currentAnnouncement.value] || ''
  const maxLength = 64
  return text.length > maxLength ? `${text.slice(0, maxLength - 3).trim()}...` : text
})

const heroSectionHeightClass = computed(() => {
  if (layoutConfig.value.hero_style === 'centered-minimal') {
    return props.isMobilePreview
      ? 'h-[58vh] min-h-[320px]'
      : 'h-[60vh] min-h-[360px] md:h-[64vh] md:min-h-[460px]'
  }
  if (layoutConfig.value.hero_style === 'split-portrait') {
    return props.isMobilePreview
      ? 'h-[62vh] min-h-[350px]'
      : 'h-[64vh] min-h-[390px] md:h-[68vh] md:min-h-[500px]'
  }
  return props.isMobilePreview
    ? 'h-[65vh] min-h-[360px]'
    : 'h-[65vh] min-h-[400px] md:h-[70vh] md:min-h-[520px]'
})

const heroOverlayClass = computed(() => {
  if (layoutConfig.value.hero_style === 'centered-minimal') {
    return 'bg-gradient-to-t from-black/35 via-black/20 to-black/10'
  }
  if (layoutConfig.value.hero_style === 'split-portrait') {
    return 'bg-gradient-to-r from-black/55 via-black/20 to-transparent'
  }
  return 'bg-gradient-to-t from-black/55 via-black/20 to-transparent'
})

const heroContentPositionClass = computed(() => {
  const position = layoutConfig.value.hero_text_position
  if (position === 'center') {
    return 'flex flex-col items-center justify-center text-center'
  }
  if (position === 'bottom-center') {
    return 'flex flex-col items-center justify-end text-center'
  }
  return 'flex flex-col items-start justify-end text-left'
})

const heroBadgeStyle = computed(() => ({
  backgroundColor: `${aiPalette.value.background}2E`,
  border: `1px solid ${aiPalette.value.text_light}33`,
  color: aiPalette.value.text_light,
  backdropFilter: 'blur(4px)'
}))

const bannerCtaSecondary = computed(() => {
  return props.storeConfig.ai_banner_texts?.cta_secondary || ''
})

// AI-generated banner texts with fallbacks
const bannerHeadline = computed(() => {
  return props.storeConfig.ai_banner_texts?.headline || 'Nueva Colección'
})

const bannerSubheadline = computed(() => {
  return props.storeConfig.ai_banner_texts?.subheadline || 'Descubre lo nuevo'
})

const bannerCtaText = computed(() => {
  return props.storeConfig.ai_banner_texts?.cta_text || ''
})

const heroHeadlineDisplay = computed(() => {
  const text = bannerHeadline.value || 'Nueva Colección'
  const maxChars = layoutConfig.value.hero_content_density === 'compact' ? 58 : 76
  return text.length > maxChars ? `${text.slice(0, maxChars - 3).trim()}...` : text
})

const heroSubheadlineDisplay = computed(() => {
  const text = bannerSubheadline.value || ''
  const maxChars = layoutConfig.value.hero_content_density === 'compact' ? 62 : 88
  return text.length > maxChars ? `${text.slice(0, maxChars - 3).trim()}...` : text
})

// Im�genes del carrusel � prioriza catalog_media.hero_images, luego banner_url, luego fallbacks
const carouselImages = computed(() => {
  // 1. Im�genes subidas por el comerciante en el admin
  const catalogMedia = props.storeConfig.catalog_media || {}
  const heroImages = Array.isArray(catalogMedia.hero_images)
    ? catalogMedia.hero_images.filter(Boolean)
    : []

  if (heroImages.length > 0) {
    // Completar hasta 3 con el mismo array en loop si hay menos de 3
    const result = [...heroImages]
    while (result.length < 3) result.push(heroImages[result.length % heroImages.length] || heroImages[0])
    return result.slice(0, 3)
  }

  // 2. Fallback: banner_url legacy
  const images = []
  if (props.storeConfig.banner_url) images.push(props.storeConfig.banner_url)

  // 3. Fallback: Unsplash defaults
  const defaultImages = [
    'https://images.unsplash.com/photo-1441986300917-64674bd600d8',
    'https://images.unsplash.com/photo-1483985988355-763728e1935b',
    'https://images.unsplash.com/photo-1490481651871-ab68de25d43d'
  ]
  defaultImages.forEach(img => { if (images.length < 3) images.push(img) })
  return images.slice(0, 3)
})

// Imagen activa del hero según el slide del carrusel
const currentHeroImage = computed(() => carouselImages.value[currentSlide.value] || carouselImages.value[0] || '')

const catalogProducts = computed(() => props.storeConfig.catalog_products || [])

const getProductImage = (product) => {
  if (!product) return ''
  if (product.images && product.images.length > 0) return product.images[0]
  return product.image_url || ''
}

const heroProducts = computed(() => {
  const inStock = catalogProducts.value.filter(product => Number(product.stock || 0) > 0)
  const source = inStock.length > 0 ? inStock : catalogProducts.value
  return source.slice(0, 3)
})

const storyImageSlots = computed(() => {
  const catalogMedia = props.storeConfig.catalog_media || {}
  const hook = hookStyle.value

  // Imágenes contextuales según el diseño activo
  if (hook === 'urban-lookbook') {
    const lookbookImgs = Array.isArray(catalogMedia.lookbook_images)
      ? catalogMedia.lookbook_images.filter(Boolean)
      : []
    // Completar con product images como fallback
    if (lookbookImgs.length > 0) return [...lookbookImgs, ...Array(4).fill('')].slice(0, 4)
  }
  if (hook === 'dynamic-bento') {
    return [
      catalogMedia.bento_main || '',
      catalogMedia.bento_detail || '',
      carouselImages.value[0] || ''
    ]
  }
  if (hook === 'editorial-story') {
    const editImg = catalogMedia.editorial_image || ''
    if (editImg) return [editImg, carouselImages.value[0] || '', carouselImages.value[1] || '']
  }

  // Fallback genérico: product images + carousel
  const productImages = catalogProducts.value
    .map(product => getProductImage(product))
    .filter(Boolean)
  const fallback = [...carouselImages.value]
  const combined = [...productImages, ...fallback]
  return [combined[0] || '', combined[1] || '', combined[2] || '']
})

const storySnippet = computed(() => {
  const about = (props.storeConfig.ai_about_us || '').trim()
  if (!about) {
    return 'Curamos piezas con dise�o, calidad y car�cter para que tu compra se sienta especial desde el primer vistazo.'
  }
  return about.length > 260 ? `${about.slice(0, 260).trim()}...` : about
})

const announcementCards = computed(() => announcements.value.slice(0, 3))

const categoryHighlights = computed(() => {
  const countMap = new Map()

  catalogProducts.value.forEach((product) => {
    const rawId = product.category_id ?? product.category ?? 'sin-categoria'
    const id = String(rawId)
    const name = product.category || product.category_name || 'Sin categor�a'

    if (!countMap.has(id)) {
      countMap.set(id, { id: rawId, name, count: 0 })
    }
    countMap.get(id).count += 1
  })

  return Array.from(countMap.values())
    .sort((a, b) => b.count - a.count)
    .slice(0, 8)
})

const activeCategoryName = computed(() => {
  if (selectedCategory.value === null) return 'Toda la colección' 
  const match = categoryHighlights.value.find(category => String(category.id) === String(selectedCategory.value))
  return match?.name || 'Colección seleccionada'
})

// Computed
const storeName = computed(() => props.storeConfig.store_name || 'Mi Tienda')

// Precios mínimo y máximo de productos
const minProductPrice = computed(() => {
  const products = catalogProducts.value
  if (products.length === 0) return 0
  return Math.floor(Math.min(...products.map(p => parseFloat(p.price || 0))))
})

const maxProductPrice = computed(() => {
  const products = catalogProducts.value
  if (products.length === 0) return 1000000
  return Math.ceil(Math.max(...products.map(p => parseFloat(p.price || 0))))
})

const filteredProducts = computed(() => {
  let products = catalogProducts.value
  
  // Filtro por b�squeda
  if (searchQuery.value.trim()) {
    const query = searchQuery.value.toLowerCase()
    products = products.filter(p => 
      (p.name || '').toLowerCase().includes(query) ||
      (p.description || '').toLowerCase().includes(query)
    )
  }
  
  // Filtro por categor�a seleccionada
  if (selectedCategory.value !== null) {
    products = products.filter(p => p.category_id === selectedCategory.value)
  }
  
  // Filtro por disponibilidad (stock > 0)
  if (showOnlyAvailable.value) {
    products = products.filter(p => p.stock && p.stock > 0)
  }
  
  // Filtro por precio m�ximo
  if (priceRange.value.max < maxProductPrice.value) {
    products = products.filter(p => {
      const price = parseFloat(p.price || 0)
      return price <= priceRange.value.max
    })
  }
  
  // Ordenamiento
  if (sortOrder.value) {
    products = [...products] // Clonar para no mutar el original
    
    switch (sortOrder.value) {
      case 'price-asc':
        products.sort((a, b) => parseFloat(a.price || 0) - parseFloat(b.price || 0))
        break
      case 'price-desc':
        products.sort((a, b) => parseFloat(b.price || 0) - parseFloat(a.price || 0))
        break
      case 'name-asc':
        products.sort((a, b) => (a.name || '').localeCompare(b.name || ''))
        break
      case 'name-desc':
        products.sort((a, b) => (b.name || '').localeCompare(a.name || ''))
        break
    }
  }
  
  return products
})

const cartTotal = computed(() => {
  return cartItems.value.reduce((sum, item) => sum + parseFloat(item.price), 0)
})

// Clases del grid seg�n el modo de vista
const gridClasses = computed(() => {
  if (props.isMobilePreview) {
    return 'grid grid-cols-2 gap-3'
  }
  return 'grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 2xl:grid-cols-6 gap-4 lg:gap-5'
})

// Grid Premium para Moda (2 columnas en m�vil, m�ximo 4 en desktop)
const gridClassesPremium = computed(() => {
  if (props.isMobilePreview) {
    return 'grid grid-cols-2 gap-3 px-0'
  }
  return 'grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4 lg:gap-5'
})


// M�todos
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

const applyCategoryFilter = (categoryId) => {
  selectedCategory.value = categoryId
  showMobileMenu.value = false
  showMobileFilters.value = false
  nextTick(() => {
    scrollToProducts()
  })
}

const handleImageError = (productId) => {
  imageErrors.value[productId] = true
}

const addToCart = (product) => {
  if (product.stock === 0) return
  
  // If product uses measurement_unit different from 'unit', needs quantity modal
  const requiresQuantityInput = product.measurement_unit && product.measurement_unit !== 'unit'
  
  if (requiresQuantityInput) {
    selectedProductForQuantity.value = product
    showQuantityModal.value = true
    return
  }
  
  // Check for variants (Fashion) - navigate to PDP for variant selection
  const hasVariants = product.variants && product.variants.length > 0
  if (hasVariants) {
    router.push(productUrl(product))
    return
  }
  
  // Simple product: add directly via shared cart store
  addItem({ ...product, image_url: product.image_url || (product.images && product.images[0]) })
}

// Handler para modal de cantidad
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

// ?? Handler para modal de variantes
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

const handleScroll = () => {
  isScrolled.value = window.scrollY > 100
}

const scrollToProducts = () => {
  if (!productsSection.value) return
  const top = productsSection.value.getBoundingClientRect().top + window.scrollY - 140
  window.scrollTo({ top: Math.max(top, 0), behavior: 'smooth' })
}

const openProductDetails = (product) => {
  if (isPreviewMode) return
  router.push(productUrl(product))
}

// Autoplay del carrusel
let carouselInterval = null
const startCarousel = () => {
  carouselInterval = setInterval(() => {
    const totalSlides = Math.max(carouselImages.value.length, 1)
    currentSlide.value = (currentSlide.value + 1) % totalSlides
  }, 5000) // Cambia cada 5 segundos
}

const stopCarousel = () => {
  if (carouselInterval) {
    clearInterval(carouselInterval)
  }
}

// Interval para el ticker de anuncios
let announcementInterval = null
const startAnnouncementTicker = () => {
  announcementInterval = setInterval(() => {
    currentAnnouncement.value = (currentAnnouncement.value + 1) % announcements.value.length
  }, 3000)
}

const stopAnnouncementTicker = () => {
  if (announcementInterval) {
    clearInterval(announcementInterval)
  }
}

// Load Google Fonts dynamically for AI-generated font pairs
const loadAiFonts = () => {
  const fonts = props.storeConfig.ai_fonts
  if (!fonts) return
  
  const fontsToLoad = new Set()
  if (fonts.heading) fontsToLoad.add(fonts.heading)
  if (fonts.body) fontsToLoad.add(fonts.body)
  
  // Always load defaults too
  fontsToLoad.add('Playfair Display')
  fontsToLoad.add('Montserrat')
  
  const familyParam = Array.from(fontsToLoad)
    .map(f => 'family=' + f.replace(/\s+/g, '+') + ':wght@300;400;500;600;700')
    .join('&')
  
  // Check if link already exists
  const existingLink = document.querySelector('link[data-ai-fonts]')
  if (existingLink) existingLink.remove()
  
  const link = document.createElement('link')
  link.rel = 'stylesheet'
  link.href = `https://fonts.googleapis.com/css2?${familyParam}&display=swap`
  link.setAttribute('data-ai-fonts', 'true')
  document.head.appendChild(link)
}

// Inicializar rango de precios cuando se cargan los productos
const initPriceRange = () => {
  priceRange.value.min = minProductPrice.value
  priceRange.value.max = maxProductPrice.value
}

// Lifecycle
onMounted(() => {
  window.addEventListener('scroll', handleScroll)
  startCarousel()
  startAnnouncementTicker()
  initPriceRange()
  // Inicializar solo imageErrors
  props.storeConfig.catalog_products?.forEach(p => {
    imageErrors.value[p.id] = false
  })
  // Load AI-generated Google Fonts dynamically
  loadAiFonts()
})

watch(() => props.storeConfig.ai_fonts, () => {
  loadAiFonts()
}, { deep: true })

watch(() => props.storeConfig.catalog_products, () => {
  initPriceRange()
}, { deep: true })

onUnmounted(() => {
  window.removeEventListener('scroll', handleScroll)
  stopCarousel()
  stopAnnouncementTicker()
})
</script>

<style scoped>
.catalog-visual-story {
  --brand-color: v-bind('storeConfig.primary_color');
  background-color: var(--ai-background, #ffffff);
  color: var(--ai-text-dark, #111827);
  font-family: var(--ai-font-body), 'Inter', 'Helvetica Neue', sans-serif;
}

.catalog-visual-story h1,
.catalog-visual-story h2,
.catalog-visual-story h3,
.catalog-visual-story h4 {
  font-family: var(--ai-font-heading), 'Playfair Display', 'Georgia', serif;
}

.bg-brand {
  background-color: var(--brand-color);
}

.text-brand {
  color: var(--brand-color);
}

/* Off-canvas drawer from left */
.drawer-left-enter-active,
.drawer-left-leave-active {
  transition: transform 0.3s ease-in-out;
}
.drawer-left-enter-from,
.drawer-left-leave-to {
  transform: translateX(-100%);
}

/* Animaci�n Carrusel de Anuncios */
@keyframes scroll-left {
  0% { transform: translateX(0); }
  100% { transform: translateX(-50%); }
}

.animate-scroll-left {
  animation: scroll-left 20s linear infinite;
}

/* Transici�n Slide Down para b�squeda m�vil */
.slide-down-enter-active,
.slide-down-leave-active {
  transition: all 0.3s ease;
}

.slide-down-enter-from,
.slide-down-leave-to {
  opacity: 0;
  transform: translateY(-10px);
}

.scrollbar-hide::-webkit-scrollbar {
  display: none;
}

.scrollbar-hide {
  -ms-overflow-style: none;
  scrollbar-width: none;
}

/* Skeleton Loader */
.skeleton-loader {
  position: relative;
  overflow: hidden;
}

.skeleton-loader::after {
  content: '';
  position: absolute;
  top: 0;
  left: -100%;
  width: 100%;
  height: 100%;
  background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
  animation: shimmer 1.5s infinite;
}

@keyframes shimmer {
  to { left: 100%; }
}

/* Transitions */
.list-move,
.list-enter-active,
.list-leave-active {
  transition: all 0.5s cubic-bezier(0.55, 0, 0.1, 1);
}

.list-enter-from {
  opacity: 0;
  transform: scale(0.8) translateY(30px);
}

.list-leave-to {
  opacity: 0;
  transform: scale(0.8);
}

.list-leave-active {
  position: absolute;
}

.fade-enter-active, .fade-leave-active {
  transition: opacity 0.3s ease;
}

.fade-enter-from, .fade-leave-to {
  opacity: 0;
}

/* Transici�n del Carrusel */
.fade-slide-enter-active {
  transition: opacity 1.5s ease;
}

.fade-slide-leave-active {
  transition: opacity 1s ease;
}

.fade-slide-enter-from {
  opacity: 0;
}

.fade-slide-leave-to {
  opacity: 0;
}

/* Animaci�n Fade In */
@keyframes fadeIn {
  from { opacity: 0; transform: translateY(20px); }
  to { opacity: 1; transform: translateY(0); }
}

.animate-fade-in {
  animation: fadeIn 1s ease-out;
}

.animate-fade-in-delay {
  animation: fadeIn 1s ease-out 0.3s both;
}

.slide-up-enter-active, .slide-up-leave-active {
  transition: transform 0.4s cubic-bezier(0.4, 0, 0.2, 1);
}

.slide-up-enter-from, .slide-up-leave-to {
  transform: translateY(100%);
}

.slide-right-enter-active, .slide-right-leave-active {
  transition: transform 0.4s cubic-bezier(0.4, 0, 0.2, 1);
}

.slide-right-enter-from, .slide-right-leave-to {
  transform: translateX(100%);
}

/* PDP Full-Page Slide Transition */
.pdp-slide-enter-active {
  transition: transform 0.35s cubic-bezier(0.4, 0, 0.2, 1);
}

.pdp-slide-leave-active {
  transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.pdp-slide-enter-from {
  transform: translateX(100%);
}

.pdp-slide-leave-to {
  transform: translateX(100%);
}

.scale-enter-active, .scale-leave-active {
  transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
}

.scale-enter-from, .scale-leave-to {
  opacity: 0;
  transform: scale(0.8);
}

@keyframes bounce-slow {
  0%, 100% { transform: translateY(0); }
  50% { transform: translateY(-5px); }
}

.animate-bounce-slow {
  animation: bounce-slow 2s ease-in-out infinite;
}

/* Masonry Responsive */
@media (max-width: 640px) {
  .columns-2 {
    columns: 1;
  }
}

/* Ticker: slide-left (default) � entra desde derecha, sale por izquierda */
.ticker-train-enter-active { transition: all 0.8s cubic-bezier(0.16, 1, 0.3, 1); }
.ticker-train-leave-active  { transition: all 0.5s cubic-bezier(0.7, 0, 0.84, 0); }
.ticker-train-enter-from    { opacity: 0; transform: translateX(100%); }
.ticker-train-leave-to      { opacity: 0; transform: translateX(-100%); }

/* Ticker: fade � aparece y desaparece suavemente (transparent-glass) */
.ticker-fade-enter-active { transition: opacity 0.6s ease; }
.ticker-fade-leave-active  { transition: opacity 0.4s ease; }
.ticker-fade-enter-from    { opacity: 0; }
.ticker-fade-leave-to      { opacity: 0; }

/* Ticker: slide-down � entra desde arriba, sale hacia arriba (floating-pill) */
.ticker-down-enter-active { transition: all 0.5s cubic-bezier(0.16, 1, 0.3, 1); }
.ticker-down-leave-active  { transition: all 0.4s cubic-bezier(0.7, 0, 0.84, 0); }
.ticker-down-enter-from    { opacity: 0; transform: translateY(-100%); }
.ticker-down-leave-to      { opacity: 0; transform: translateY(-100%); }

/* Dual Range Slider - Estilos Premium */
input[type="range"] {
  -webkit-appearance: none;
  appearance: none;
  background: transparent;
  cursor: pointer;
}

input[type="range"]::-webkit-slider-thumb {
  -webkit-appearance: none;
  appearance: none;
  width: 18px;
  height: 18px;
  border-radius: 50%;
  background: #1f2937;
  border: 3px solid white;
  box-shadow: 0 2px 6px rgba(0, 0, 0, 0.2);
  cursor: pointer;
  margin-top: -8px;
  transition: all 0.2s ease;
}

input[type="range"]::-webkit-slider-thumb:hover {
  transform: scale(1.15);
  box-shadow: 0 3px 10px rgba(0, 0, 0, 0.3);
}

input[type="range"]::-moz-range-thumb {
  width: 18px;
  height: 18px;
  border-radius: 50%;
  background: #1f2937;
  border: 3px solid white;
  box-shadow: 0 2px 6px rgba(0, 0, 0, 0.2);
  cursor: pointer;
  transition: all 0.2s ease;
}

input[type="range"]::-moz-range-thumb:hover {
  transform: scale(1.15);
}

input[type="range"]::-webkit-slider-runnable-track {
  height: 4px;
  border-radius: 2px;
}

input[type="range"]::-moz-range-track {
  height: 4px;
  border-radius: 2px;
}
</style>
