<template>
  <!-- PLANTILLA A: "VISUAL STORY" - Estilo Boutique/Gourmet -->
  <div class="catalog-visual-story bg-gray-50 relative overflow-x-hidden min-h-screen">
    
    <!-- HERO CARRUSEL: Full Height con Transiciones Automáticas -->
    <section 
      class="relative w-full overflow-hidden transition-all duration-500 mt-[60px] h-[60vh] md:mt-[72px] md:h-[calc(100vh-72px)]" 
    >
      <!-- Carrusel de Imágenes con Transición -->
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
            <!-- Gradiente para legibilidad -->
            <div class="absolute inset-0 bg-gradient-to-b from-black/30 via-black/10 to-black/70"></div>
          </div>
        </TransitionGroup>
      </div>

      <!-- Logo en la Parte Superior Izquierda (Solo Desktop si no hay scroll) -->
      <!-- ELIMINADO: Ya está en el header fijo -->

      <!-- Contenido Central -->
      <div class="absolute inset-0 flex flex-col items-center justify-center z-10 px-6 text-center pt-8 md:pt-0">
        <h2 class="text-white text-3xl md:text-6xl font-black mb-2 md:mb-4 drop-shadow-2xl animate-fade-in leading-tight">
          Productos de Calidad
        </h2>
        <p class="text-white/90 text-base md:text-xl font-light max-w-2xl drop-shadow-lg animate-fade-in-delay">
          Descubre nuestra selección exclusiva
        </p>
        
        <!-- Botón Scroll Down -->
        <button 
          @click="scrollToProducts"
          class="mt-8 md:mt-12 animate-bounce bg-white/10 backdrop-blur-md border border-white/30 rounded-full p-3 md:p-4 hover:bg-white/20 transition-all"
        >
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 md:h-6 md:w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3" />
          </svg>
        </button>
      </div>

      <!-- Indicadores del Carrusel -->
      <div class="absolute bottom-8 left-1/2 -translate-x-1/2 z-20 flex gap-2">
        <button 
          v-for="(img, index) in 3" 
          :key="index"
          @click="currentSlide = index"
          class="w-2 h-2 rounded-full transition-all duration-300"
          :class="currentSlide === index ? 'bg-white w-8' : 'bg-white/50 hover:bg-white/80'"
        ></button>
      </div>
    </section>

    <!-- HEADER STICKY: Siempre Sólido y Limpio -->
    <header 
      ref="stickyHeader"
      class="fixed top-0 left-0 right-0 z-50 transition-all duration-300 bg-white shadow-sm py-2 md:py-3"
    >
      <div class="w-full px-4 lg:px-8 xl:px-12 flex items-center justify-between">
        
        <!-- Left: Logo & Title -->
        <div class="flex items-center gap-3">
          <img 
            v-if="storeConfig.logo_url"
            :src="storeConfig.logo_url"
            alt="Logo"
            class="h-10 w-10 md:h-12 md:w-12 object-contain rounded-lg"
          />
          <div class="flex flex-col">
            <h2 class="text-lg md:text-xl font-bold text-gray-900 leading-tight">
              {{ storeName }}
            </h2>
            <p class="text-[10px] md:text-xs text-gray-500 font-medium uppercase tracking-wide">
              Catálogo Web
            </p>
          </div>
        </div>

        <!-- Right: Search & Cart -->
        <div class="flex items-center gap-4 md:gap-6">
           
           <!-- Desktop Search Bar -->
           <div class="hidden md:flex items-center bg-gray-100 rounded-full px-4 py-2 w-64 transition-all focus-within:ring-2 focus-within:ring-emerald-500 focus-within:bg-white">
             <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
               <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
             </svg>
             <input 
               type="text" 
               placeholder="Buscar productos..." 
               class="bg-transparent border-none focus:ring-0 text-sm text-gray-700 w-full ml-2 placeholder-gray-400"
             />
           </div>

           <button 
            @click="showCheckout = true"
            class="relative p-2 rounded-full hover:bg-gray-100 transition-colors text-gray-700"
          >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
            </svg>
            <span 
              v-if="cartItems.length > 0"
              class="absolute top-0 right-0 bg-red-500 text-white text-[10px] font-bold w-4 h-4 rounded-full flex items-center justify-center"
            >
              {{ cartItems.length }}
            </span>
          </button>
        </div>
      </div>
    </header>

    <!-- PRODUCTS SECTION: Layout con Sidebar Lateral - ANCHO COMPLETO -->
    <section class="relative z-10 px-4 lg:px-6 xl:px-8 pt-8 pb-32 md:pb-12">
      <div class="w-full">
        
        <div class="flex gap-6">
          <!-- SIDEBAR IZQUIERDO: Filtros Estilo Menú Elegante -->
          <aside v-if="!isMobilePreview" class="hidden lg:block w-64 flex-shrink-0">
            <div class="sticky top-24 space-y-8">
              
              <!-- Filtro por Categoría -->
              <div>
                <h3 class="text-xs font-bold text-gray-400 mb-4 uppercase tracking-widest">Categorías</h3>
                <div class="space-y-1">
                  <button
                    @click="selectedCategory = null"
                    class="w-full text-left px-4 py-2.5 rounded-lg text-sm transition-all duration-200 flex items-center justify-between group"
                    :class="selectedCategory === null 
                      ? 'bg-gray-900 text-white font-medium shadow-md' 
                      : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900'"
                  >
                    <span>Todas</span>
                    <svg v-if="selectedCategory === null" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                      <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                    </svg>
                  </button>
                  <button
                    v-for="cat in categories"
                    :key="cat.id"
                    @click="selectedCategory = cat.id"
                    class="w-full text-left px-4 py-2.5 rounded-lg text-sm transition-all duration-200 flex items-center justify-between group"
                    :class="selectedCategory === cat.id 
                      ? 'bg-gray-900 text-white font-medium shadow-md' 
                      : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900'"
                  >
                    <span>{{ cat.name }}</span>
                    <svg v-if="selectedCategory === cat.id" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                      <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                    </svg>
                  </button>
                </div>
              </div>

              <!-- Filtro por Precio -->
              <div>
                <h3 class="text-xs font-bold text-gray-400 mb-4 uppercase tracking-widest">Ordenar por</h3>
                <div class="space-y-3">
                  <label class="flex items-center gap-3 cursor-pointer group">
                    <div class="relative flex items-center">
                      <input 
                        type="radio" 
                        name="sort" 
                        value="" 
                        v-model="sortOrder"
                        class="peer h-4 w-4 cursor-pointer appearance-none rounded-full border border-gray-300 checked:border-emerald-500 transition-all"
                      />
                      <span class="absolute bg-emerald-500 w-2 h-2 rounded-full opacity-0 peer-checked:opacity-100 transition-opacity duration-200 top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2"></span>
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
                        class="peer h-4 w-4 cursor-pointer appearance-none rounded-full border border-gray-300 checked:border-emerald-500 transition-all"
                      />
                      <span class="absolute bg-emerald-500 w-2 h-2 rounded-full opacity-0 peer-checked:opacity-100 transition-opacity duration-200 top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2"></span>
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
                        class="peer h-4 w-4 cursor-pointer appearance-none rounded-full border border-gray-300 checked:border-emerald-500 transition-all"
                      />
                      <span class="absolute bg-emerald-500 w-2 h-2 rounded-full opacity-0 peer-checked:opacity-100 transition-opacity duration-200 top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2"></span>
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
                        class="peer h-4 w-4 cursor-pointer appearance-none rounded-full border border-gray-300 checked:border-emerald-500 transition-all"
                      />
                      <span class="absolute bg-emerald-500 w-2 h-2 rounded-full opacity-0 peer-checked:opacity-100 transition-opacity duration-200 top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2"></span>
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
                      class="peer h-5 w-5 cursor-pointer appearance-none rounded border border-gray-300 checked:bg-emerald-500 checked:border-emerald-500 transition-all"
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
            </div>
          </aside>

          <!-- ÁREA PRINCIPAL: Productos -->
          <div class="flex-1 min-w-0">
            
            <!-- Barra Superior: Contador y Filtros Móviles -->
            <div class="flex items-center justify-between mb-6 pb-4 border-b border-gray-200">
              <div class="text-sm text-gray-600 font-medium">
                {{ filteredProducts.length }} productos
                <span v-if="selectedCategory || showOnlyAvailable || sortOrder" class="text-emerald-600 font-semibold">
                  (filtrados)
                </span>
              </div>

              <!-- Filtros Móviles (solo en pantallas pequeñas) -->
              <div class="lg:hidden">
                <select 
                  v-model="sortOrder"
                  class="appearance-none bg-white border border-gray-300 rounded-lg px-3 py-2 pr-8 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-emerald-500"
                >
                  <option value="">Ordenar</option>
                  <option value="price-asc">Menor precio</option>
                  <option value="price-desc">Mayor precio</option>
                  <option value="name-asc">A-Z</option>
                </select>
              </div>
            </div>

            <!-- Grid de Productos: Estilo Mercado Libre - hasta 5 columnas, tarjetas compactas -->
            <div :class="gridClasses">
              <TransitionGroup name="list">
                <div 
                  v-for="product in filteredProducts" 
                  :key="product.id"
                  class="group"
            >
              <div class="bg-white rounded-xl overflow-hidden shadow-md hover:shadow-xl transition-all duration-300 hover:-translate-y-2 border border-gray-100 h-full flex flex-col cursor-pointer" @click="openProductDetails(product)">
                <!-- Product Image - Compacta -->
                <div class="relative aspect-square overflow-hidden bg-gray-50">
                  
                  <!-- Imagen del Producto -->
                  <img 
                    v-if="product.image_url && product.image_url !== 'https://via.placeholder.com/400' && !imageErrors[product.id]"
                    :src="product.image_url"
                    :alt="product.name"
                    @error="handleImageError(product.id)"
                    class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700"
                  />
                  
                  <!-- Placeholder cuando no hay imagen -->
                  <div v-else class="w-full h-full flex items-center justify-center bg-gray-100">
                    <div class="text-center">
                      <svg class="w-12 h-12 text-gray-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                      </svg>
                      <p class="text-[10px] text-gray-400 font-medium uppercase tracking-wider">Sin imagen</p>
                    </div>
                  </div>
                  
                  <!-- Floating Add Button (Círculo verde flotante) -->
                  <button
                    @click.stop="addToCart(product)"
                    :disabled="product.stock === 0"
                    class="absolute bottom-3 right-3 w-10 h-10 rounded-full shadow-lg flex items-center justify-center bg-emerald-500 text-white hover:bg-emerald-600 hover:scale-110 transition-all active:scale-95 z-10 disabled:bg-gray-300 disabled:cursor-not-allowed opacity-90 hover:opacity-100"
                  >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                    </svg>
                  </button>

                  <!-- Badge de Stock Bajo (Mini) -->
                  <div v-if="product.stock < 5 && product.stock > 0" class="absolute top-3 left-3 bg-amber-500/90 backdrop-blur-sm text-white text-[10px] font-bold px-2.5 py-1 rounded-full shadow-sm">
                    Solo {{ product.stock }}
                  </div>
                  <div v-else-if="product.stock === 0" class="absolute top-3 left-3 bg-red-500/90 backdrop-blur-sm text-white text-[10px] font-bold px-2.5 py-1 rounded-full shadow-sm">
                    Agotado
                  </div>
                </div>

                <!-- Product Info -->
                <div class="p-4 flex-1 flex flex-col justify-between">
                  <div>
                    <h3 class="text-sm font-medium text-gray-800 line-clamp-2 mb-2 leading-snug min-h-[2.5em] group-hover:text-emerald-700 transition-colors">
                      {{ product.name }}
                    </h3>
                  </div>
                  <p class="text-lg font-bold text-emerald-600">
                    {{ storeConfig.currency_symbol }}{{ formatPrice(product.price) }}
                  </p>
                </div>
              </div>
            </div>
          </TransitionGroup>
            </div>

            <!-- Empty State -->
            <div v-if="filteredProducts.length === 0" class="text-center py-20">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-24 w-24 mx-auto text-gray-300 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
              </svg>
              <p class="text-gray-500 text-lg font-medium">No hay productos disponibles</p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- PRODUCT DETAIL MODAL (Executive Matte Style) -->
    <Transition name="fade">
      <div v-if="selectedProduct" class="fixed inset-0 z-[200] bg-white overflow-y-auto animate-fade-in">
        <!-- Close Button -->
        <button 
          @click="closeProductDetails"
          class="fixed top-6 right-6 z-[210] p-2 bg-white/80 backdrop-blur-md rounded-full hover:bg-gray-100 transition-colors"
        >
          <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-900" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>

        <div class="grid grid-cols-1 lg:grid-cols-2 min-h-screen">
          <!-- Left: Immersive Gallery (50%) -->
          <div class="lg:col-span-1 bg-gray-50 p-0 lg:p-0 flex flex-col">
            <div class="grid grid-cols-1 gap-1 h-full">
              <!-- Main Image (Adjusted Height) -->
              <div class="relative h-[50vh] lg:h-[65vh] w-full overflow-hidden group">
                <img 
                  :src="selectedProduct.image_url" 
                  class="w-full h-full object-cover object-top transition-transform duration-1000 group-hover:scale-105"
                />
              </div>
              <!-- Secondary Images (Smaller Height) -->
              <div class="hidden lg:grid grid-cols-2 gap-1 h-[25vh] bg-white">
                 <div class="relative overflow-hidden h-full">
                    <img :src="selectedProduct.image_url" class="w-full h-full object-cover object-top grayscale hover:grayscale-0 transition-all duration-500" />
                 </div>
                 <div class="relative overflow-hidden h-full">
                    <img :src="selectedProduct.image_url" class="w-full h-full object-cover object-center scale-150" />
                 </div>
              </div>
            </div>
          </div>

          <!-- Right: Sticky Info Panel (50%) -->
          <div class="lg:col-span-1 p-6 lg:p-12 flex flex-col bg-white">
            <div class="lg:sticky lg:top-12 space-y-6">
              
              <!-- Breadcrumbs -->
              <nav class="text-xs text-gray-400 uppercase tracking-widest font-medium">
                Inicio / Catálogo / <span class="text-gray-900">{{ selectedProduct.category_name || 'Colección' }}</span>
              </nav>

              <!-- Header -->
              <div class="space-y-4">
                <h2 class="text-3xl lg:text-4xl font-serif text-gray-900 leading-tight">
                  {{ selectedProduct.name }}
                </h2>
                <p class="text-2xl font-light text-gray-900">
                  {{ storeConfig.currency_symbol }}{{ formatPrice(selectedProduct.price) }}
                </p>
              </div>

              <!-- Selectors -->
              <div class="space-y-6 py-6 border-t border-gray-100">
                <!-- Color -->
                <div class="space-y-3">
                  <span class="text-sm font-bold text-gray-900 uppercase tracking-wide">Color: Navy Blue</span>
                  <div class="flex gap-3">
                    <button 
                      v-for="(color, idx) in productColors" 
                      :key="idx"
                      class="w-8 h-8 rounded-full ring-2 ring-offset-2 ring-transparent hover:ring-gray-300 transition-all"
                      :class="[color.class, { '!ring-gray-900': idx === 0 }]"
                    ></button>
                  </div>
                </div>

                <!-- Size -->
                <div class="space-y-3">
                  <div class="flex justify-between items-center">
                    <span class="text-sm font-bold text-gray-900 uppercase tracking-wide">Talla</span>
                    <button class="text-xs text-gray-500 underline hover:text-gray-900">Guía de tallas</button>
                  </div>
                  <div class="flex gap-3">
                    <button 
                      v-for="size in productSizes" 
                      :key="size"
                      @click="selectedSize = size"
                      class="w-12 h-12 flex items-center justify-center border transition-all duration-200 text-sm font-medium"
                      :class="selectedSize === size 
                        ? 'bg-gray-900 text-white border-gray-900' 
                        : 'bg-white text-gray-900 border-gray-200 hover:border-gray-900'"
                    >
                      {{ size }}
                    </button>
                  </div>
                </div>
              </div>

              <!-- CTA -->
              <button 
                @click="addToCartFromDetail"
                :disabled="selectedProduct.stock === 0"
                class="w-full bg-gray-900 text-white py-4 px-8 uppercase tracking-widest text-sm font-bold hover:bg-black transition-colors disabled:bg-gray-300 disabled:cursor-not-allowed"
              >
                {{ selectedProduct.stock === 0 ? 'Agotado' : 'Agregar al Carrito' }}
              </button>

              <!-- Accordions -->
              <div class="border-t border-gray-200 mt-8">
                <!-- Description -->
                <div class="border-b border-gray-200">
                  <button @click="toggleAccordion('description')" class="w-full py-4 flex justify-between items-center text-left group">
                    <span class="text-sm font-bold text-gray-900 uppercase tracking-wide">Descripción</span>
                    <span class="text-xl font-light text-gray-400 group-hover:text-gray-900 transition-colors">
                      {{ activeAccordion === 'description' ? '−' : '+' }}
                    </span>
                  </button>
                  <div v-show="activeAccordion === 'description'" class="pb-4 text-sm text-gray-600 leading-relaxed animate-fade-in">
                    <p>{{ selectedProduct.description || 'Una prenda esencial diseñada para el guardarropa moderno. Confeccionada con materiales de primera calidad que garantizan durabilidad y confort excepcional. El corte preciso y los acabados refinados reflejan nuestra dedicación a la excelencia artesanal.' }}</p>
                  </div>
                </div>

                <!-- Composition -->
                <div class="border-b border-gray-200">
                  <button @click="toggleAccordion('composition')" class="w-full py-4 flex justify-between items-center text-left group">
                    <span class="text-sm font-bold text-gray-900 uppercase tracking-wide">Composición y Cuidados</span>
                    <span class="text-xl font-light text-gray-400 group-hover:text-gray-900 transition-colors">
                      {{ activeAccordion === 'composition' ? '−' : '+' }}
                    </span>
                  </button>
                  <div v-show="activeAccordion === 'composition'" class="pb-4 text-sm text-gray-600 leading-relaxed animate-fade-in">
                    <ul class="list-disc pl-4 space-y-1">
                      <li>100% Algodón Premium</li>
                      <li>Lavar a máquina en frío</li>
                      <li>No usar blanqueador</li>
                      <li>Planchar a temperatura media</li>
                    </ul>
                  </div>
                </div>

                <!-- Shipping -->
                <div class="border-b border-gray-200">
                  <button @click="toggleAccordion('shipping')" class="w-full py-4 flex justify-between items-center text-left group">
                    <span class="text-sm font-bold text-gray-900 uppercase tracking-wide">Envíos y Devoluciones</span>
                    <span class="text-xl font-light text-gray-400 group-hover:text-gray-900 transition-colors">
                      {{ activeAccordion === 'shipping' ? '−' : '+' }}
                    </span>
                  </button>
                  <div v-show="activeAccordion === 'shipping'" class="pb-4 text-sm text-gray-600 leading-relaxed animate-fade-in">
                    <p>Envíos gratuitos en pedidos superiores a {{ storeConfig.currency_symbol }}{{ formatPrice(150000) }}. Entrega estimada en 2-4 días hábiles. Devoluciones gratuitas dentro de los 30 días posteriores a la compra.</p>
                  </div>
                </div>
              </div>

            </div>
          </div>
        </div>
      </div>
    </Transition>

    <!-- FLOATING CART BUTTON (FAB) -->
    <Transition name="scale">
      <button 
        v-if="cartItems.length > 0"
        @click="showCheckout = true"
        class="fixed bottom-6 right-6 bg-brand text-white pl-6 pr-5 py-4 rounded-full shadow-2xl hover:shadow-3xl flex items-center gap-3 transform hover:scale-105 transition-all z-50 animate-bounce-slow"
      >
        <div class="relative">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
          </svg>
          <span class="absolute -top-2 -right-2 bg-red-500 text-white text-xs font-bold w-5 h-5 rounded-full flex items-center justify-center">
            {{ cartItems.length }}
          </span>
        </div>
        <div class="text-left">
          <p class="text-xs font-medium opacity-90">Ver Pedido</p>
          <p class="text-sm font-black">{{ storeConfig.currency_symbol }}{{ formatPrice(cartTotal) }}</p>
        </div>
      </button>
    </Transition>

    <!-- CHECKOUT DRAWER (Responsive: Bottom Sheet en Móvil, Slide-out en Desktop) -->
    <Transition name="fade">
      <div v-if="showCheckout" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-[100]" @click="showCheckout = false"></div>
    </Transition>
    
    <Transition :name="isMobilePreview ? 'slide-up' : 'slide-right'">
      <div 
        v-if="showCheckout" 
        class="fixed z-[101] bg-white shadow-2xl flex flex-col"
        :class="[
          isMobilePreview 
            ? 'bottom-0 left-0 right-0 rounded-t-3xl max-h-[85vh]' 
            : 'top-0 right-0 h-full w-full max-w-md rounded-l-2xl'
        ]"
      >
        <!-- Header del Carrito -->
        <div class="flex-shrink-0 bg-white border-b border-gray-100 px-6 py-5 z-10 flex items-center justify-between" :class="{ 'rounded-t-3xl': isMobilePreview }">
          <div>
            <h3 class="text-xl font-black text-gray-900">Tu Pedido</h3>
            <p class="text-sm text-gray-500 mt-0.5">{{ cartItems.length }} productos seleccionados</p>
          </div>
          <button @click="showCheckout = false" class="p-2 -mr-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-full transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>

        <!-- Lista de Productos (Scrollable) -->
        <div class="flex-1 overflow-y-auto px-6 py-4 space-y-4">
          <div v-if="cartItems.length === 0" class="h-full flex flex-col items-center justify-center text-center py-12">
            <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mb-4">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
              </svg>
            </div>
            <h4 class="text-lg font-bold text-gray-900">Tu carrito está vacío</h4>
            <p class="text-gray-500 text-sm mt-1 max-w-[200px]">Agrega productos para comenzar tu pedido</p>
            <button @click="showCheckout = false" class="mt-6 text-emerald-600 font-bold text-sm hover:underline">
              Seguir comprando
            </button>
          </div>

          <div v-else v-for="item in cartItems" :key="item.id" class="flex gap-4 py-3 border-b border-gray-50 last:border-0 animate-fade-in">
            <div class="w-20 h-20 flex-shrink-0 bg-gray-50 rounded-xl overflow-hidden border border-gray-100">
              <img :src="item.image_url" class="w-full h-full object-cover" />
            </div>
            <div class="flex-1 min-w-0 flex flex-col justify-between py-1">
              <div>
                <h4 class="font-bold text-gray-900 text-sm line-clamp-2 leading-snug">{{ item.name }}</h4>
                <p class="text-xs text-gray-500 mt-1">Unidad</p>
              </div>
              <div class="flex items-center justify-between mt-2">
                <p class="text-emerald-600 font-black text-base">{{ storeConfig.currency_symbol }}{{ formatPrice(item.price) }}</p>
                <button @click="removeFromCart(item.id)" class="text-gray-400 hover:text-red-500 transition-colors p-1 hover:bg-red-50 rounded-lg">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                  </svg>
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Footer con Totales y Acción (Fixed at bottom of drawer) -->
        <div v-if="cartItems.length > 0" class="flex-shrink-0 bg-gray-50 px-6 py-6 border-t border-gray-200 space-y-4">
          <!-- Totals -->
          <div class="space-y-2">
            <div class="flex justify-between text-sm">
              <span class="text-gray-600">Subtotal</span>
              <span class="font-bold text-gray-900">{{ storeConfig.currency_symbol }}{{ formatPrice(cartTotal) }}</span>
            </div>
            <div class="flex justify-between text-sm">
              <span class="text-gray-600">Domicilio</span>
              <span class="font-bold text-gray-900">{{ storeConfig.currency_symbol }}{{ formatPrice(storeConfig.delivery_cost) }}</span>
            </div>
            <div class="flex justify-between text-xl pt-3 border-t border-gray-200/60">
              <span class="font-black text-gray-900">Total a Pagar</span>
              <span class="font-black text-emerald-600">{{ storeConfig.currency_symbol }}{{ formatPrice(cartTotal + storeConfig.delivery_cost) }}</span>
            </div>
          </div>

          <!-- Validación Pedido Mínimo -->
          <div v-if="cartTotal < storeConfig.min_order_value" class="bg-amber-50 border border-amber-200 rounded-xl p-3 flex items-start gap-3">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-amber-600 flex-shrink-0 mt-0.5" viewBox="0 0 20 20" fill="currentColor">
              <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
            </svg>
            <div class="text-xs text-amber-800">
              <p class="font-bold">Pedido mínimo no alcanzado</p>
              <p class="mt-0.5">
                Agrega {{ storeConfig.currency_symbol }}{{ formatPrice(storeConfig.min_order_value - cartTotal) }} más.
              </p>
            </div>
          </div>

          <!-- WhatsApp Button -->
          <button 
            @click="sendWhatsAppOrder"
            :disabled="cartTotal < storeConfig.min_order_value"
            class="w-full bg-[#25D366] hover:bg-[#1ebe57] disabled:bg-gray-300 disabled:text-gray-500 text-white py-4 rounded-xl font-black text-lg flex items-center justify-center gap-3 shadow-lg hover:shadow-xl hover:-translate-y-0.5 transition-all disabled:cursor-not-allowed disabled:shadow-none disabled:transform-none"
          >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24">
              <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z" />
            </svg>
            Enviar Pedido por WhatsApp
          </button>
        </div>
      </div>
    </Transition>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted, watch, nextTick } from 'vue'

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
const sortOrder = ref('') // Filtro de ordenamiento
const showOnlyAvailable = ref(false) // Filtro de disponibilidad
const cartItems = ref([])
const showCheckout = ref(false)
const stickyHeader = ref(null)
const productsSection = ref(null)
const currentSlide = ref(0)
const selectedProduct = ref(null)
const selectedSize = ref(null)
const activeAccordion = ref(null)

// Mock Data para Detalle
const productSizes = ['S', 'M', 'L', 'XL']
const productColors = [
  { name: 'Navy Blue', class: 'bg-slate-900' },
  { name: 'Matte Black', class: 'bg-gray-900' },
  { name: 'Pure White', class: 'bg-white border border-gray-200' }
]

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

const filteredProducts = computed(() => {
  let products = props.storeConfig.catalog_products || []
  
  // Filtro por categoría seleccionada
  if (selectedCategory.value !== null) {
    products = products.filter(p => p.category_id === selectedCategory.value)
  }
  
  // Filtro por disponibilidad (stock > 0)
  if (showOnlyAvailable.value) {
    products = products.filter(p => p.stock && p.stock > 0)
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
    return 'grid grid-cols-2 gap-2'
  }
  return 'grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-2 sm:gap-3'
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
  cartItems.value.push({ ...product })
  // Animación visual
  const event = new CustomEvent('cart-updated', { detail: { action: 'add' } })
  window.dispatchEvent(event)
}

const removeFromCart = (productId) => {
  const index = cartItems.value.findIndex(item => item.id === productId)
  if (index > -1) cartItems.value.splice(index, 1)
}

const sendWhatsAppOrder = () => {
  if (cartTotal.value < props.storeConfig.min_order_value) return

  const total = cartTotal.value + props.storeConfig.delivery_cost
  let message = `¡Hola! 👋 Quiero hacer un pedido:\n\n`
  
  cartItems.value.forEach((item, index) => {
    message += `${index + 1}. ${item.name} - ${props.storeConfig.currency_symbol}${formatPrice(item.price)}\n`
  })
  
  message += `\n📦 Subtotal: ${props.storeConfig.currency_symbol}${formatPrice(cartTotal.value)}`
  message += `\n🚚 Domicilio: ${props.storeConfig.currency_symbol}${formatPrice(props.storeConfig.delivery_cost)}`
  message += `\n💰 *Total: ${props.storeConfig.currency_symbol}${formatPrice(total)}*`

  const whatsappUrl = `https://wa.me/${props.storeConfig.whatsapp_number}?text=${encodeURIComponent(message)}`
  window.open(whatsappUrl, '_blank')
}

const handleScroll = () => {
  isScrolled.value = window.scrollY > 100
}

const scrollToProducts = () => {
  productsSection.value?.scrollIntoView({ behavior: 'smooth' })
}

const openProductDetails = (product) => {
  selectedProduct.value = product
  selectedSize.value = null
  activeAccordion.value = 'description'
  document.body.style.overflow = 'hidden'
}

const closeProductDetails = () => {
  selectedProduct.value = null
  document.body.style.overflow = ''
}

const toggleAccordion = (section) => {
  activeAccordion.value = activeAccordion.value === section ? null : section
}

const addToCartFromDetail = () => {
  if (selectedProduct.value) {
    addToCart(selectedProduct.value)
    closeProductDetails()
    showCheckout.value = true
  }
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

// Lifecycle
onMounted(() => {
  window.addEventListener('scroll', handleScroll)
  startCarousel()
  // Inicializar solo imageErrors
  props.storeConfig.catalog_products?.forEach(p => {
    imageErrors.value[p.id] = false
  })
})

onUnmounted(() => {
  window.removeEventListener('scroll', handleScroll)
  stopCarousel()
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
</style>
