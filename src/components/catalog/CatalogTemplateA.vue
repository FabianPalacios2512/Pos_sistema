<template>
  <!-- PLANTILLA A: "MODA PREMIUM" - Estilo Vélez/Zara -->
  <div class="catalog-visual-story bg-white relative overflow-x-hidden min-h-screen">
    
    <!-- � TOP BAR: Ticker tipo Tren - Entra, se detiene, sale -->
    <div class="fixed top-0 left-0 right-0 z-[60] bg-[#1a1a1a] h-9 flex items-center justify-center overflow-hidden">
      <div class="relative w-full h-full flex items-center justify-center">
        <TransitionGroup name="ticker-train" tag="div" class="relative w-full h-full">
          <span 
            :key="currentAnnouncement"
            class="absolute inset-0 flex items-center justify-center text-[11px] text-white font-medium tracking-[0.15em] uppercase whitespace-nowrap px-4"
          >
            {{ announcements[currentAnnouncement] }}
          </span>
        </TransitionGroup>
      </div>
    </div>

    <!-- HEADER PREMIUM: Retail Fashion Store Style (KHARIS-inspired) -->
    <header 
      ref="stickyHeader"
      class="fixed top-9 left-0 right-0 z-50 bg-white transition-all duration-300"
      style="box-shadow: 0 1px 0 rgba(0,0,0,0.06);"
    >
      <!-- Línea decorativa superior sutil -->
      <div class="h-[1px] w-full bg-gradient-to-r from-transparent via-gray-200 to-transparent"></div>
      
      <div class="w-full px-4 lg:px-8 h-14 lg:h-[72px] flex items-center justify-between">
        
        <!-- Left: Menú Hamburguesa (Líneas finas elegantes) -->
        <div class="flex items-center gap-2 w-[72px]">
          <button 
            @click="showMobileMenu = !showMobileMenu"
            class="lg:hidden w-10 h-10 flex items-center justify-center -ml-1"
          >
            <svg class="w-[22px] h-[22px] text-gray-800" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.2">
              <path stroke-linecap="round" d="M4 7h16M4 12h16M4 17h16" />
            </svg>
          </button>
        </div>

        <!-- CENTER: Nombre del Comercio - Tipografía Serif Elegante -->
        <div class="flex-1 flex flex-col items-center justify-center lg:justify-start lg:flex-none lg:absolute lg:left-1/2 lg:-translate-x-1/2">
          <h1 
            class="text-[22px] lg:text-[28px] text-gray-900 tracking-[0.08em] uppercase leading-none"
            style="font-family: 'Playfair Display', 'Georgia', 'Times New Roman', serif; font-weight: 600;"
          >
            {{ storeNameFirst }}
          </h1>
          <span 
            v-if="storeNameSecond"
            class="text-[9px] lg:text-[11px] text-gray-500 tracking-[0.25em] uppercase mt-0.5 font-medium"
            style="font-family: 'Inter', 'Helvetica Neue', sans-serif;"
          >
            {{ storeNameSecond }}
          </span>
        </div>

        <!-- Right: Lupa + Bolsa (Elegantes, negro) -->
        <div class="flex items-center gap-0 w-[72px] justify-end">
          <!-- Lupa Móvil -->
          <button 
            @click="showMobileSearch = !showMobileSearch"
            class="lg:hidden w-10 h-10 flex items-center justify-center"
          >
            <svg class="w-[20px] h-[20px] text-gray-800" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
            </svg>
          </button>

          <!-- Bolsa/Carrito -->
          <button 
            @click="router.push('/catalog/bolsa')"
            class="relative w-10 h-10 flex items-center justify-center"
          >
            <svg class="w-[20px] h-[20px] text-gray-800" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993l1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 01-1.12-1.243l1.264-12A1.125 1.125 0 015.513 7.5h12.974c.576 0 1.059.435 1.119 1.007zM8.625 10.5a.375.375 0 11-.75 0 .375.375 0 01.75 0zm7.5 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
            </svg>
            <span 
              v-if="cartCount > 0"
              class="absolute -top-0.5 -right-0.5 bg-gray-900 text-white text-[8px] font-bold min-w-[16px] h-[16px] rounded-full flex items-center justify-center tracking-tight"
            >
              {{ cartCount }}
            </span>
          </button>
        </div>
      </div>

      <!-- Desktop: Barra de Búsqueda + Navegación -->
      <div class="hidden lg:flex justify-center border-t border-gray-100 py-2 px-8">
        <div class="relative w-full max-w-lg">
          <input 
            v-model="searchQuery"
            type="text" 
            placeholder="Buscar productos..."
            class="w-full h-10 pl-10 pr-4 bg-gray-50 border border-gray-200 rounded-lg text-sm text-gray-900 placeholder-gray-400 focus:bg-white focus:border-gray-400 focus:ring-1 focus:ring-gray-200 transition-all outline-none"
          />
          <svg class="w-4 h-4 text-gray-400 absolute left-3.5 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
          </svg>
        </div>
      </div>

      <!-- Línea decorativa inferior -->
      <div class="h-[1px] w-full bg-gray-100"></div>

      <!-- Barra de Búsqueda Móvil Expandible -->
      <Transition name="slide-down">
        <div v-if="showMobileSearch" class="lg:hidden px-4 py-3 bg-white border-b border-gray-100">
          <div class="relative">
            <input 
              v-model="searchQuery"
              type="text" 
              placeholder="¿Qué estás buscando?"
              class="w-full h-11 pl-10 pr-10 bg-gray-50 border border-gray-200 rounded-full text-sm text-gray-900 placeholder-gray-500 focus:bg-white focus:border-gray-300 transition-all outline-none"
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

    <!-- HERO BANNER: Compacto y Elegante -->
    <section 
      class="relative w-full overflow-hidden mt-[94px] lg:mt-[154px]" 
      :class="isMobilePreview ? 'h-[280px]' : 'h-[300px] md:h-[450px]'"
    >
      <!-- Carrusel de Imágenes -->
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
              alt="Banner"
              class="w-full h-full object-cover"
            />
            <!-- Gradiente fuerte inferior para legibilidad -->
            <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-black/10 to-transparent"></div>
          </div>
        </TransitionGroup>
      </div>

      <!-- Contenido sobre el Banner -->
      <div class="absolute inset-0 flex flex-col items-center justify-end z-10 px-6 pb-10 text-center">
        <h2 
          class="text-white text-2xl md:text-5xl font-light mb-2 tracking-wide"
          style="font-family: 'Montserrat', 'Inter', sans-serif; font-weight: 300; text-shadow: 0 2px 12px rgba(0,0,0,0.3);"
        >
          Nueva Colección
        </h2>
        <p 
          class="text-white/90 text-xs md:text-lg font-medium tracking-[0.2em] uppercase"
          style="font-family: 'Montserrat', 'Inter', sans-serif; text-shadow: 0 1px 8px rgba(0,0,0,0.25);"
        >
          Descubre lo nuevo
        </p>
      </div>

      <!-- Indicadores Minimalistas -->
      <div class="absolute bottom-4 left-1/2 -translate-x-1/2 z-20 flex gap-1.5">
        <button 
          v-for="(img, index) in carouselImages.length || 3" 
          :key="index"
          @click="currentSlide = index"
          class="w-1.5 h-1.5 rounded-full transition-all duration-300"
          :class="currentSlide === index ? 'bg-white w-6' : 'bg-white/50'"
        ></button>
      </div>
    </section>

    <!-- BARRA STICKY: Filtrar + Ordenar (Móvil) - Línea fina inferior -->
    <div 
      class="lg:hidden sticky top-[93px] z-40 bg-white/95 backdrop-blur-sm border-b border-gray-200"
    >
      <div class="flex">
        <!-- Botón FILTRAR (50%) -->
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
        <!-- Botón ORDENAR (50%) -->
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
      <!-- Filtro por Categoría - Minimalista con línea vertical -->
      <div class="mb-8">
        <h3 class="text-xs font-bold text-gray-400 mb-5 uppercase tracking-widest">Categorías</h3>
        <div class="space-y-0.5">
          <button
            @click="selectedCategory = null"
            class="w-full text-left py-2.5 text-sm transition-all duration-200 flex items-center gap-3 relative"
            :class="selectedCategory === null 
              ? 'text-gray-900 font-semibold' 
              : 'text-gray-500 hover:text-gray-900'"
          >
            <span 
              class="absolute left-0 top-1/2 -translate-y-1/2 w-0.5 h-5 bg-gray-900 transition-opacity duration-200"
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
              ? 'text-gray-900 font-semibold' 
              : 'text-gray-500 hover:text-gray-900'"
          >
            <span 
              class="absolute left-0 top-1/2 -translate-y-1/2 w-0.5 h-5 bg-gray-900 transition-opacity duration-200"
              :class="selectedCategory === cat.id ? 'opacity-100' : 'opacity-0'"
            ></span>
            <span class="pl-4">{{ cat.name }}</span>
          </button>
        </div>
      </div>

      <!-- Filtro por Rango de Precio - Slider Simple (Solo Máximo) -->
      <div class="mb-8">
        <h3 class="text-xs font-bold text-gray-400 mb-5 uppercase tracking-widest">Precio</h3>
        <div class="space-y-4">
          <!-- Single Range Slider -->
          <div class="relative h-1.5 bg-gray-200 rounded-full">
            <div 
              class="absolute left-0 h-full bg-gray-900 rounded-full transition-all"
              :style="{ width: ((priceRange.max - minProductPrice) / (maxProductPrice - minProductPrice)) * 100 + '%' }"
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
            <span class="font-semibold text-gray-900">Hasta {{ storeConfig.currency_symbol }}{{ formatPrice(priceRange.max) }}</span>
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

      <!-- Botón Limpiar Filtros -->
      <button
        v-if="selectedCategory || showOnlyAvailable || sortOrder"
        @click="clearFilters"
        class="w-full px-4 py-2.5 text-xs font-bold uppercase tracking-wide text-red-500 hover:text-white hover:bg-red-500 rounded-lg transition-all border border-red-200 hover:border-red-500"
      >
        Limpiar filtros
      </button>
    </aside>

    <!-- ÁREA PRINCIPAL: Productos -->
    <section class="lg:ml-64 pt-4 px-4 lg:px-8">
      
      <!-- Barra Superior: Contador (Solo Desktop) -->
      <div class="hidden lg:flex items-center justify-between mb-6 pb-4 border-b border-gray-200">
        <div class="text-sm text-gray-600 font-medium">
          {{ filteredProducts.length }} productos
          <span v-if="selectedCategory || showOnlyAvailable || sortOrder" class="font-semibold" :style="{ color: primaryColor }">
            (filtrados)
          </span>
        </div>
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
              class="bg-white overflow-hidden h-full flex flex-col cursor-pointer transition-all duration-300 hover:-translate-y-1 hover:shadow-xl" 
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
                
                <!-- Botón Agregar - Minimal -->
                <button
                  @click.stop="addToCart(product)"
                  :disabled="product.stock === 0"
                  class="absolute bottom-2.5 right-2.5 w-9 h-9 rounded-full flex items-center justify-center transition-all duration-300 z-10"
                  :class="product.stock > 0 
                    ? 'bg-white text-gray-600 hover:bg-gray-900 hover:text-white shadow-sm hover:shadow-md hover:scale-110' 
                    : 'bg-gray-200 text-gray-400 cursor-not-allowed'"
                >
                  <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                  </svg>
                </button>

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
              <div class="pt-3 pb-4 px-0.5">
                <h3 class="text-[13px] font-normal text-gray-700 truncate leading-snug mb-1">
                  {{ product.name }}
                </h3>
                <p class="text-sm font-bold text-gray-900">
                  {{ storeConfig.currency_symbol }}{{ formatPrice(product.price) }}
                </p>
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

    <!-- FOOTER: Powered by 105 POS -->
    <footer class="bg-white border-t border-gray-100 py-8 text-center" :class="{ 'mb-16': cartCount > 0 }">
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
        class="fixed bottom-0 left-0 right-0 z-[55] bg-white px-4 py-3 flex items-center justify-between lg:hidden"
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
          class="bg-gray-900 hover:bg-black text-white px-6 py-3 text-sm font-semibold uppercase tracking-wide transition-colors flex items-center gap-2"
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

    <!-- MOBILE FILTERS DRAWER (Solo Móvil) -->
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

    <!-- Modal de Selección de Variantes (Fashion) -->
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
import { ref, computed, onMounted, onUnmounted, watch, nextTick } from 'vue'
import { useRouter } from 'vue-router'
import axios from 'axios'
import QuantityModal from './QuantityModal.vue'
import POSVariantSelector from '../POSVariantSelector.vue'
import { useCatalogCart } from '../../stores/catalogCart.js'
import { productUrl } from '../../utils/slugify.js'

const router = useRouter()
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

// Ticker de anuncios vertical
const announcements = ref([
  'Envío Gratis en compras mayores a $150.000',
  'Nuevas Colecciones Disponibles',
  'Hasta 3 cuotas sin interés',
  'Devoluciones gratis en 30 días'
])
const currentAnnouncement = ref(0)

// Rango de precios para filtro
const priceRange = ref({ min: 0, max: 1000000 })

const stickyHeader = ref(null)
const productsSection = ref(null)
const currentSlide = ref(0)

// 🆕 Estados para modales nuevos
const showQuantityModal = ref(false)
const selectedProductForQuantity = ref(null)
const showVariantModal = ref(false)
const selectedProductForVariants = ref(null)

// 🆕 Estados para UI Premium Móvil
const showMobileMenu = ref(false)
const showMobileSearch = ref(false)
const showMobileFilters = ref(false)
const showSortModal = ref(false)

// Color primario dinámico del storeConfig
const primaryColor = computed(() => props.storeConfig.primary_color || '#10B981')

// Imágenes del carrusel - usar banner y logo si están disponibles
const carouselImages = computed(() => {
  const images = []
  if (props.storeConfig.banner_url) images.push(props.storeConfig.banner_url)
  // Rellenar con imágenes predeterminadas
  const defaultImages = [
    'https://images.unsplash.com/photo-1441986300917-64674bd600d8', // Tienda de ropa/boutique (Clean)
    'https://images.unsplash.com/photo-1483985988355-763728e1935b', // Modelo Fashion / Shopping (Moda)
    'https://images.unsplash.com/photo-1490481651871-ab68de25d43d'  // Ropa en ganchos (Minimalista)
  ]
  defaultImages.forEach(img => {
    if (images.length < 3) images.push(img)
  })
  return images.slice(0, 3)
})

// Computed
const storeName = computed(() => props.storeConfig.store_name || 'Mi Tienda')

// Split store name: first word = main title (serif), rest = subtitle
const storeNameFirst = computed(() => {
  const name = storeName.value.trim()
  const parts = name.split(/\s+/)
  return parts[0] || name
})

const storeNameSecond = computed(() => {
  const name = storeName.value.trim()
  const parts = name.split(/\s+/)
  return parts.length > 1 ? parts.slice(1).join(' ') : ''
})

// Precios mínimo y máximo de productos
const minProductPrice = computed(() => {
  const products = props.storeConfig.catalog_products || []
  if (products.length === 0) return 0
  return Math.floor(Math.min(...products.map(p => parseFloat(p.price || 0))))
})

const maxProductPrice = computed(() => {
  const products = props.storeConfig.catalog_products || []
  if (products.length === 0) return 1000000
  return Math.ceil(Math.max(...products.map(p => parseFloat(p.price || 0))))
})

const filteredProducts = computed(() => {
  let products = props.storeConfig.catalog_products || []
  
  // Filtro por búsqueda
  if (searchQuery.value.trim()) {
    const query = searchQuery.value.toLowerCase()
    products = products.filter(p => 
      (p.name || '').toLowerCase().includes(query) ||
      (p.description || '').toLowerCase().includes(query)
    )
  }
  
  // Filtro por categoría seleccionada
  if (selectedCategory.value !== null) {
    products = products.filter(p => p.category_id === selectedCategory.value)
  }
  
  // Filtro por disponibilidad (stock > 0)
  if (showOnlyAvailable.value) {
    products = products.filter(p => p.stock && p.stock > 0)
  }
  
  // Filtro por precio máximo
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

// Clases del grid según el modo de vista
const gridClasses = computed(() => {
  if (props.isMobilePreview) {
    return 'grid grid-cols-2 gap-3'
  }
  return 'grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 2xl:grid-cols-6 gap-4 lg:gap-5'
})

// Grid Premium para Moda (2 columnas en móvil, máximo 4 en desktop)
const gridClassesPremium = computed(() => {
  if (props.isMobilePreview) {
    return 'grid grid-cols-2 gap-3 px-0'
  }
  return 'grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4 lg:gap-5'
})


// Métodos
const formatPrice = (price) => {
  return parseFloat(price).toLocaleString('es-CO', { minimumFractionDigits: 0, maximumFractionDigits: 0 })
}

const clearFilters = () => {
  selectedCategory.value = null
  sortOrder.value = ''
  showOnlyAvailable.value = false
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

// 🆕 Handler para modal de variantes
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
  productsSection.value?.scrollIntoView({ behavior: 'smooth' })
}

const openProductDetails = (product) => {
  router.push(productUrl(product))
}

// Autoplay del carrusel
let carouselInterval = null
const startCarousel = () => {
  carouselInterval = setInterval(() => {
    currentSlide.value = (currentSlide.value + 1) % 3 // 3 slides
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
})

onUnmounted(() => {
  window.removeEventListener('scroll', handleScroll)
  stopCarousel()
  stopAnnouncementTicker()
})
</script>

<style scoped>
.catalog-visual-story {
  --brand-color: v-bind('storeConfig.primary_color');
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

/* Animación Carrusel de Anuncios */
@keyframes scroll-left {
  0% { transform: translateX(0); }
  100% { transform: translateX(-50%); }
}

.animate-scroll-left {
  animation: scroll-left 20s linear infinite;
}

/* Transición Slide Down para búsqueda móvil */
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

/* Transición del Carrusel */
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

/* Animación Fade In */
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

/* Ticker Tren - Animación horizontal: entra desde derecha, se detiene, sale por izquierda */
.ticker-train-enter-active {
  transition: all 0.8s cubic-bezier(0.16, 1, 0.3, 1);
}
.ticker-train-leave-active {
  transition: all 0.5s cubic-bezier(0.7, 0, 0.84, 0);
}

.ticker-train-enter-from {
  opacity: 0;
  transform: translateX(100%);
}

.ticker-train-leave-to {
  opacity: 0;
  transform: translateX(-100%);
}

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
