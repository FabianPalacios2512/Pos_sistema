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
        <div class="flex items-center gap-2 md:gap-6 flex-1 justify-end">
           
           <!-- Search Bar (Desktop & Mobile) -->
           <div class="flex-1 max-w-xs md:max-w-md">
             <div class="relative">
               <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 md:h-5 md:w-5 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
               </svg>
               <input 
                 v-model="searchQuery"
                 type="text" 
                 placeholder="Buscar..."
                 class="w-full h-9 md:h-10 pl-9 md:pl-10 pr-3 md:pr-4 bg-gray-100 border border-gray-200 rounded-full text-sm text-gray-700 placeholder-gray-400 focus:bg-white focus:border-gray-400 focus:ring-2 focus:ring-gray-200 transition-all outline-none"
               />
             </div>
           </div>

           <!-- Cart Button -->
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
            </div>
          </aside>

          <!-- ÁREA PRINCIPAL: Productos -->
          <div class="flex-1 min-w-0">
            
            <!-- Barra Superior: Contador y Filtros Móviles -->
            <div class="flex items-center justify-between mb-6 pb-4 border-b border-gray-200">
              <div class="text-sm text-gray-600 font-medium">
                {{ filteredProducts.length }} productos
                <span v-if="selectedCategory || showOnlyAvailable || sortOrder" class="font-semibold" :style="{ color: primaryColor }">
                  (filtrados)
                </span>
              </div>

              <!-- Filtros Móviles (solo en pantallas pequeñas) -->
              <div class="lg:hidden">
                <select 
                  v-model="sortOrder"
                  class="appearance-none bg-white border border-gray-300 rounded-lg px-3 py-2 pr-8 text-sm text-gray-700 focus:outline-none focus:ring-2"
                  :style="{ '--focus-ring-color': primaryColor }"
                  style="--tw-ring-color: var(--focus-ring-color);"
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
                    v-if="(product.images && product.images.length > 0) || (product.image_url && product.image_url !== 'https://via.placeholder.com/400' && !imageErrors[product.id])"
                    :src="product.images && product.images.length > 0 ? product.images[0] : product.image_url"
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
                  
                  <!-- Floating Add Button (Círculo con color primario flotante) -->
                  <button
                    @click.stop="addToCart(product)"
                    :disabled="product.stock === 0"
                    :style="{ backgroundColor: product.stock > 0 ? primaryColor : undefined }"
                    class="absolute bottom-3 right-3 w-10 h-10 rounded-full shadow-lg flex items-center justify-center text-white hover:scale-110 transition-all active:scale-95 z-10 disabled:bg-gray-300 disabled:cursor-not-allowed opacity-90 hover:opacity-100"
                    @mouseenter="(e) => e.target.style.filter = product.stock > 0 ? 'brightness(0.9)' : ''"
                    @mouseleave="(e) => e.target.style.filter = ''"
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
                    <h3 class="text-sm font-medium text-gray-800 line-clamp-2 mb-2 leading-snug min-h-[2.5em] transition-colors">
                      {{ product.name }}
                    </h3>
                  </div>
                  <p class="text-lg font-bold" :style="{ color: primaryColor }">
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

    <!-- PRODUCT DETAIL MODAL (Modern E-commerce Style) -->
    <Transition name="fade">
      <div v-if="selectedProduct" class="fixed inset-0 z-[200] bg-black/50 backdrop-blur-sm flex items-center justify-center p-4 lg:p-8 animate-fade-in">
        
        <!-- Modal Container -->
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-5xl max-h-[90vh] overflow-hidden flex flex-col lg:flex-row">
          
          <!-- Close Button -->
          <button 
            @click="closeProductDetails"
            class="absolute top-4 right-4 z-[210] p-2 bg-white/90 backdrop-blur-md rounded-full hover:bg-gray-100 transition-colors shadow-lg"
          >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>

          <!-- Left: Gallery -->
          <div class="lg:w-1/2 bg-gray-50 flex flex-col">
            <!-- Main Image -->
            <div class="relative aspect-square lg:aspect-auto lg:h-[400px] overflow-hidden">
              <img 
                :src="selectedProduct.images && selectedProduct.images.length > 0 ? selectedProduct.images[selectedImageIndex || 0] : selectedProduct.image_url" 
                class="w-full h-full object-contain bg-white"
              />
              <!-- Navigation Arrows -->
              <button 
                v-if="selectedProduct.images && selectedProduct.images.length > 1"
                @click="selectedImageIndex = (selectedImageIndex - 1 + selectedProduct.images.length) % selectedProduct.images.length"
                class="absolute left-3 top-1/2 -translate-y-1/2 w-10 h-10 bg-white/90 rounded-full shadow-lg flex items-center justify-center hover:bg-white transition-colors"
              >
                <svg class="w-5 h-5 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
              </button>
              <button 
                v-if="selectedProduct.images && selectedProduct.images.length > 1"
                @click="selectedImageIndex = (selectedImageIndex + 1) % selectedProduct.images.length"
                class="absolute right-3 top-1/2 -translate-y-1/2 w-10 h-10 bg-white/90 rounded-full shadow-lg flex items-center justify-center hover:bg-white transition-colors"
              >
                <svg class="w-5 h-5 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
              </button>
            </div>
            <!-- Thumbnails -->
            <div v-if="selectedProduct.images && selectedProduct.images.length > 1" class="flex gap-2 p-4 overflow-x-auto bg-white border-t border-gray-100">
              <button 
                v-for="(img, idx) in selectedProduct.images" 
                :key="idx"
                @click="selectedImageIndex = idx"
                class="w-16 h-16 flex-shrink-0 rounded-lg overflow-hidden border-2 transition-all"
                :class="selectedImageIndex === idx ? 'border-gray-900 ring-2 ring-gray-900/20' : 'border-gray-200 hover:border-gray-400'"
              >
                <img :src="img" class="w-full h-full object-cover" />
              </button>
            </div>
          </div>

          <!-- Right: Product Info -->
          <div class="lg:w-1/2 flex flex-col max-h-[50vh] lg:max-h-[90vh] overflow-y-auto">
            <div class="p-6 lg:p-8 space-y-5">
              
              <!-- Category Badge -->
              <span class="inline-block px-3 py-1 bg-gray-100 text-gray-600 text-xs font-semibold rounded-full uppercase tracking-wide">
                {{ selectedProduct.category_name || 'Producto' }}
              </span>

              <!-- Product Name -->
              <h2 class="text-2xl lg:text-3xl font-bold text-gray-900 leading-tight">
                {{ selectedProduct.name }}
              </h2>

              <!-- Price -->
              <div class="flex items-baseline gap-3">
                <span class="text-3xl font-bold" :style="{ color: primaryColor }">
                  {{ storeConfig.currency_symbol }}{{ formatPrice(currentPrice) }}
                </span>
                <span v-if="currentStock > 0 && currentStock <= 5" class="text-sm text-amber-600 font-medium">
                  ¡Solo quedan {{ currentStock }}!
                </span>
              </div>

              <!-- Options Selector -->
              <div v-if="selectedProduct.options && selectedProduct.options.length > 0" class="space-y-4 pt-4 border-t border-gray-100">
                <div v-for="option in selectedProduct.options" :key="option.id" class="space-y-2">
                  <div class="flex items-center justify-between">
                    <span class="text-sm font-semibold text-gray-800">{{ option.name }}</span>
                    <span v-if="selectedOptions[option.id]" class="text-xs text-gray-500">
                      {{ getSelectedOptionValue(option) }}
                    </span>
                  </div>
                  <div class="flex flex-wrap gap-2">
                    <!-- Colors -->
                    <template v-if="option.name.toUpperCase() === 'COLOR'">
                      <button 
                        v-for="val in option.values" 
                        :key="`color-${val.id}`"
                        @click="selectedOptions[option.id] = val.id"
                        class="w-9 h-9 rounded-full border-2 transition-all duration-200 relative overflow-hidden"
                        :class="selectedOptions[option.id] === val.id
                          ? 'border-gray-900 scale-110 shadow-md' 
                          : 'border-gray-200 hover:border-gray-400 hover:scale-105'"
                        :title="val.value"
                      >
                        <div :style="{ backgroundColor: val.value }" class="w-full h-full"></div>
                        <svg v-if="selectedOptions[option.id] === val.id" class="absolute inset-0 m-auto w-4 h-4 text-white drop-shadow" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                        </svg>
                      </button>
                    </template>
                    
                    <!-- Size/Other Options -->
                    <template v-else>
                      <button 
                        v-for="val in option.values" 
                        :key="`text-${val.id}`"
                        @click="selectedOptions[option.id] = val.id"
                        class="min-w-[2.5rem] h-9 px-3 rounded-lg border text-sm font-medium transition-all duration-200"
                        :class="selectedOptions[option.id] === val.id
                          ? 'bg-gray-900 text-white border-gray-900 shadow-md' 
                          : 'bg-white text-gray-700 border-gray-200 hover:border-gray-400 hover:bg-gray-50'"
                      >
                        {{ val.value }}
                      </button>
                    </template>
                  </div>
                </div>
              </div>

              <!-- Description -->
              <div v-if="selectedProduct.description" class="pt-4 border-t border-gray-100">
                <p class="text-sm text-gray-600 leading-relaxed">{{ selectedProduct.description }}</p>
              </div>

              <!-- Add to Cart Button -->
              <div class="pt-4 space-y-3">
                <button 
                  @click="addToCartFromDetail"
                  :disabled="currentStock === 0 || !isVariantSelected"
                  :style="{ backgroundColor: currentStock > 0 && isVariantSelected ? primaryColor : undefined }"
                  class="w-full py-3.5 px-6 rounded-xl text-white text-sm font-bold uppercase tracking-wide transition-all hover:shadow-lg hover:scale-[1.02] active:scale-[0.98] disabled:bg-gray-300 disabled:cursor-not-allowed disabled:hover:scale-100 disabled:hover:shadow-none flex items-center justify-center gap-2"
                >
                  <svg v-if="currentStock > 0" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                  </svg>
                  {{ currentStock === 0 ? 'Agotado' : (isVariantSelected ? 'Agregar al Carrito' : 'Selecciona opciones') }}
                </button>
                
                <!-- Stock Info -->
                <div v-if="currentStock > 0" class="flex items-center justify-center gap-2 text-xs text-gray-500">
                  <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                  </svg>
                  <span>Disponible para envío inmediato</span>
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
            <h3 class="text-xl font-black text-gray-900">{{ showCheckoutForm ? 'Completar Pedido' : 'Tu Pedido' }}</h3>
            <p class="text-sm text-gray-500 mt-0.5">{{ showCheckoutForm ? 'Ingresa tus datos' : `${cartItems.length} productos seleccionados` }}</p>
          </div>
          <button @click="showCheckout = false; showCheckoutForm = false" class="p-2 -mr-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-full transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>

        <!-- VISTA DEL CARRITO (cuando showCheckoutForm === false) -->
        <template v-if="!showCheckoutForm">
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
            <button @click="showCheckout = false" class="mt-6 font-bold text-sm hover:underline" :style="{ color: primaryColor }">
              Seguir comprando
            </button>
          </div>

          <div v-else v-for="item in cartItems" :key="item.id" class="flex gap-4 py-3 border-b border-gray-50 last:border-0 animate-fade-in">
            <div class="w-20 h-20 flex-shrink-0 bg-gray-50 rounded-xl overflow-hidden border border-gray-100">
              <img 
                v-if="item.image_url" 
                :src="item.image_url" 
                class="w-full h-full object-cover"
                @error="(e) => e.target.src = ''"
              />
              <div v-else class="w-full h-full flex items-center justify-center bg-gray-100">
                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
              </div>
            </div>
            <div class="flex-1 min-w-0 flex flex-col justify-between py-1">
              <div>
                <h4 class="font-bold text-gray-900 text-sm line-clamp-2 leading-snug">{{ item.name }}</h4>
                <p class="text-xs text-gray-500 mt-1">Unidad</p>
              </div>
              <div class="flex items-center justify-between mt-2">
                <p class="font-black text-base" :style="{ color: primaryColor }">{{ storeConfig.currency_symbol }}{{ formatPrice(item.price) }}</p>
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
            <div class="flex justify-between text-xl pt-3 border-t border-gray-200/60">
              <span class="font-black text-gray-900">Total</span>
              <span class="font-black" :style="{ color: primaryColor }">{{ storeConfig.currency_symbol }}{{ formatPrice(cartTotal) }}</span>
            </div>
            <p class="text-xs text-gray-500 text-center">El costo de envío se calculará en el siguiente paso</p>
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

          <!-- Botón Completar Pedido -->
          <button 
            @click="showCheckoutForm = true"
            :disabled="cartTotal < storeConfig.min_order_value"
            class="w-full bg-[#25D366] hover:bg-[#1ebe57] disabled:bg-gray-300 disabled:text-gray-500 text-white py-4 rounded-xl font-black text-lg flex items-center justify-center gap-3 shadow-lg hover:shadow-xl hover:-translate-y-0.5 transition-all disabled:cursor-not-allowed disabled:shadow-none disabled:transform-none"
          >
            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            Completar Pedido
          </button>
        </div>
        </template>

        <!-- VISTA DEL FORMULARIO (cuando showCheckoutForm === true) -->
        <template v-else>
          <!-- Botón Volver -->
          <div class="px-6 pt-6 pb-4">
            <button 
              @click="showCheckoutForm = false" 
              class="flex items-center gap-2 text-gray-600 hover:text-gray-900 font-semibold text-sm transition-colors"
            >
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
              </svg>
              Volver al carrito
            </button>
          </div>

          <!-- Formulario -->
          <div class="flex-1 overflow-y-auto px-6 pb-6 space-y-4">
            <div>
              <h3 class="text-lg font-black text-gray-900 mb-1">Datos del Cliente</h3>
              <p class="text-sm text-gray-500">Completa tu información para procesar el pedido</p>
            </div>

            <!-- Cédula -->
            <div>
              <label class="block text-sm font-bold text-gray-700 mb-2">Cédula / Documento <span class="text-red-500">*</span></label>
              <div class="relative">
                <input 
                  v-model="formData.customer_document"
                  @blur="searchCustomerByDocument"
                  type="text"
                  required
                  minlength="6"
                  placeholder="1234567890"
                  :disabled="searchingCustomer"
                  class="w-full px-4 py-3 border-2 border-gray-200 bg-white text-gray-900 rounded-xl text-sm font-medium focus:ring-2 focus:ring-blue-500 focus:border-blue-500 placeholder-gray-400 transition-all disabled:opacity-50"
                />
                <div v-if="searchingCustomer" class="absolute right-3 top-1/2 -translate-y-1/2">
                  <svg class="animate-spin h-5 w-5 text-blue-500" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                  </svg>
                </div>
              </div>
            </div>

            <!-- Nombre -->
            <div>
              <label class="block text-sm font-bold text-gray-700 mb-2">Nombre Completo <span class="text-red-500">*</span></label>
              <input 
                v-model="formData.customer_name"
                type="text"
                required
                placeholder="Juan Pérez"
                class="w-full px-4 py-3 border-2 border-gray-200 bg-white text-gray-900 rounded-xl text-sm font-medium focus:ring-2 focus:ring-blue-500 focus:border-blue-500 placeholder-gray-400"
              />
            </div>

            <!-- Teléfono -->
            <div>
              <label class="block text-sm font-bold text-gray-700 mb-2">Teléfono <span class="text-red-500">*</span></label>
              <input 
                v-model="formData.customer_phone"
                type="tel"
                required
                placeholder="3001234567"
                class="w-full px-4 py-3 border-2 border-gray-200 bg-white text-gray-900 rounded-xl text-sm font-medium focus:ring-2 focus:ring-blue-500 focus:border-blue-500 placeholder-gray-400"
              />
            </div>

            <!-- Email -->
            <div>
              <label class="block text-sm font-bold text-gray-700 mb-2">Correo Electrónico <span class="text-gray-400 text-xs">(Opcional)</span></label>
              <input 
                v-model="formData.customer_email"
                type="email"
                placeholder="correo@ejemplo.com"
                class="w-full px-4 py-3 border-2 border-gray-200 bg-white text-gray-900 rounded-xl text-sm font-medium focus:ring-2 focus:ring-blue-500 focus:border-blue-500 placeholder-gray-400"
              />
            </div>

            <!-- Tipo de Entrega -->
            <div>
              <label class="block text-sm font-bold text-gray-700 mb-3">Tipo de Entrega <span class="text-red-500">*</span></label>
              <div class="grid grid-cols-2 gap-3">
                <button 
                  @click="formData.delivery_type = 'delivery'"
                  type="button"
                  class="px-4 py-4 rounded-xl border-2 font-bold text-sm flex flex-col items-center gap-2 transition-all"
                  :class="formData.delivery_type === 'delivery' ? 'border-blue-500 bg-blue-50 text-blue-700' : 'border-gray-200 bg-white text-gray-600 hover:border-gray-300'"
                >
                  <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0" /></svg>
                  Envío a Domicilio
                </button>
                <button 
                  @click="formData.delivery_type = 'pickup'"
                  type="button"
                  class="px-4 py-4 rounded-xl border-2 font-bold text-sm flex flex-col items-center gap-2 transition-all"
                  :class="formData.delivery_type === 'pickup' ? 'border-blue-500 bg-blue-50 text-blue-700' : 'border-gray-200 bg-white text-gray-600 hover:border-gray-300'"
                >
                  <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>
                  Recoger en Tienda
                </button>
              </div>
            </div>

            <!-- Dirección (si es delivery) -->
            <div v-if="formData.delivery_type === 'delivery'">
              <label class="block text-sm font-bold text-gray-700 mb-2">Dirección de Entrega <span class="text-red-500">*</span></label>
              <textarea 
                v-model="formData.customer_address"
                required
                rows="3"
                placeholder="Calle 123 #45-67"
                class="w-full px-4 py-3 border-2 border-gray-200 bg-white text-gray-900 rounded-xl text-sm font-medium focus:ring-2 focus:ring-blue-500 focus:border-blue-500 placeholder-gray-400 resize-none"
              ></textarea>
            </div>

            <!-- Notas -->
            <div>
              <label class="block text-sm font-bold text-gray-700 mb-2">Notas Especiales <span class="text-gray-400 text-xs">(Opcional)</span></label>
              <textarea 
                v-model="formData.note"
                rows="2"
                placeholder="Ej: Tocar el timbre"
                class="w-full px-4 py-3 border-2 border-gray-200 bg-white text-gray-900 rounded-xl text-sm font-medium focus:ring-2 focus:ring-blue-500 focus:border-blue-500 placeholder-gray-400 resize-none"
              ></textarea>
            </div>
          </div>

          <!-- Footer con botón -->
          <div class="border-t border-gray-200 p-6 bg-gray-50 space-y-3">
            <div class="p-4 rounded-xl bg-white border border-gray-200">
              <div class="flex justify-between items-center mb-2">
                <span class="text-sm font-semibold text-gray-600">Total a Pagar:</span>
                <span class="text-2xl font-black text-gray-900">
                  {{ storeConfig.currency_symbol }}{{ formatPrice(cartTotal + (formData.delivery_type === 'delivery' ? storeConfig.delivery_cost : 0)) }}
                </span>
              </div>
              <p class="text-xs text-gray-500">
                {{ cartItems.length }} producto{{ cartItems.length !== 1 ? 's' : '' }} • {{ formData.delivery_type === 'delivery' ? 'Envío a domicilio' : 'Recoger en tienda' }}
              </p>
            </div>

            <button 
              @click="handleCheckoutSubmit"
              :disabled="submittingOrder || !formData.customer_name || !formData.customer_phone || !formData.customer_document || formData.customer_document.length < 6 || (formData.delivery_type === 'delivery' && !formData.customer_address)"
              class="w-full bg-gradient-to-r from-[#25D366] to-[#1ebe57] hover:from-[#1ebe57] hover:to-[#128C7E] disabled:from-gray-300 disabled:to-gray-400 text-white py-4 rounded-2xl font-black text-base flex items-center justify-center gap-3 shadow-2xl hover:shadow-3xl transition-all disabled:cursor-not-allowed active:scale-[0.98]"
            >
              <svg v-if="!submittingOrder" class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/>
              </svg>
              <svg v-else class="animate-spin w-6 h-6" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              <span>{{ submittingOrder ? 'Procesando...' : 'Enviar Pedido' }}</span>
            </button>
          </div>
        </template>
      </div>
    </Transition>

    <!-- ⚖️ Modal de Cantidad (Productos por peso/medida) -->
    <QuantityModal
      :show="showQuantityModal"
      :product="selectedProductForQuantity"
      @close="showQuantityModal = false"
      @confirm="handleQuantityConfirmed"
    />

    <!-- 👗 Modal de Selección de Variantes (Fashion) -->
    <POSVariantSelector
      :show="showVariantModal"
      :product="selectedProductForVariants"
      @close="showVariantModal = false"
      @confirm="handleVariantConfirmed"
    />

  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted, watch, nextTick } from 'vue'
import axios from 'axios'
import QuantityModal from './QuantityModal.vue'
import POSVariantSelector from '../POSVariantSelector.vue'

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
const cartItems = ref([])
const showCheckout = ref(false)
const showCheckoutForm = ref(false)
const submittingOrder = ref(false)
const searchingCustomer = ref(false)
const formData = ref({
  customer_name: '',
  customer_phone: '',
  customer_document: '',
  customer_email: '',
  delivery_type: 'delivery',
  customer_address: '',
  note: ''
})
const stickyHeader = ref(null)
const productsSection = ref(null)
const currentSlide = ref(0)
const selectedProduct = ref(null)
const selectedOptions = ref({}) // { 'Color': 'Rojo', 'Talla': 'M' } (Stores Value IDs)
const activeAccordion = ref(null)
const selectedImageIndex = ref(0) // Para navegación de galería de imágenes

// 🆕 Estados para modales nuevos
const showQuantityModal = ref(false)
const selectedProductForQuantity = ref(null)
const showVariantModal = ref(false)
const selectedProductForVariants = ref(null)

// Color primario dinámico del storeConfig
const primaryColor = computed(() => props.storeConfig.primary_color || '#10B981')

// Computed for Variants
const currentVariant = computed(() => {
  if (!selectedProduct.value || !selectedProduct.value.variants || selectedProduct.value.variants.length === 0) return null
  
  // Find variant that matches all selected options
  return selectedProduct.value.variants.find(variant => {
    // Check if every selected option matches the variant's option values
    return Object.entries(selectedOptions.value).every(([optionId, valueId]) => {
      // Find the option value in the variant's option_values list
      return variant.option_values.some(ov => ov.option_id == optionId && ov.value_id == valueId)
    })
  })
})

const currentPrice = computed(() => {
  if (currentVariant.value) return currentVariant.value.price
  return selectedProduct.value ? selectedProduct.value.price : 0
})

const currentStock = computed(() => {
  if (currentVariant.value) return currentVariant.value.stock
  return selectedProduct.value ? selectedProduct.value.stock : 0
})

const isVariantSelected = computed(() => {
  if (!selectedProduct.value || !selectedProduct.value.options || selectedProduct.value.options.length === 0) return true
  // Check if all options have a selection
  return selectedProduct.value.options.every(opt => selectedOptions.value[opt.id])
})

// Mock Data para Detalle (Legacy - Removed)

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
  
  console.log('🛒 Producto clickeado:', product.name)
  console.log('⚖️ Measurement unit:', product.measurement_unit)
  console.log('⚖️ Unit:', product.unit)
  console.log('📦 Variantes:', product.variants)
  
  // 🚨 DETECCIÓN IGUAL QUE EL POS
  // Si el producto usa measurement_unit diferente de 'unit', requiere modal de cantidad
  const requiresQuantityInput = product.measurement_unit && product.measurement_unit !== 'unit'
  
  console.log('✅ Requiere input de cantidad:', requiresQuantityInput)
  
  if (requiresQuantityInput) {
    console.log('✅ Abriendo modal de cantidad')
    selectedProductForQuantity.value = product
    showQuantityModal.value = true
    return
  }
  
  // 2️⃣ Verificar si tiene variantes (Fashion)
  const hasVariants = product.variants && product.variants.length > 0
  if (hasVariants) {
    console.log('✅ Abriendo modal de variantes')
    selectedProductForVariants.value = product
    showVariantModal.value = true
    return
  }
  
  // 3️⃣ Producto simple: agregar directamente
  console.log('⚠️ Agregando directamente')
  cartItems.value.push({ ...product })
  // Animación visual
  const event = new CustomEvent('cart-updated', { detail: { action: 'add' } })
  window.dispatchEvent(event)
}

// 🆕 Handler para modal de cantidad
const handleQuantityConfirmed = ({ product, quantity }) => {
  console.log(`⚖️ Agregando ${quantity} ${product.unit} de ${product.name}`)
  
  const productWithQuantity = {
    ...product,
    id: `${product.id}-${Date.now()}`,
    quantity_value: quantity,
    name: `${product.name} (${quantity} ${product.unit || 'kg'})`,
    price: product.price * quantity,
    original_price: product.price,
    display_quantity: quantity
  }
  
  cartItems.value.push(productWithQuantity)
  selectedProductForQuantity.value = null
  showQuantityModal.value = false
}

// 🆕 Handler para modal de variantes
const handleVariantConfirmed = ({ variant, selectedOptions }) => {
  if (!variant || !selectedProductForVariants.value) return
  
  if (variant.stock <= 0) {
    console.warn('No hay stock disponible para esta variante')
    return
  }
  
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
  
  cartItems.value.push(productWithVariant)
  selectedProductForVariants.value = null
  showVariantModal.value = false
}

const removeFromCart = (productId) => {
  const index = cartItems.value.findIndex(item => item.id === productId)
  if (index > -1) cartItems.value.splice(index, 1)
}

// Buscar cliente por cédula (autocomplete inteligente)
const searchCustomerByDocument = async () => {
  if (!formData.value.customer_document || formData.value.customer_document.length < 6) {
    return
  }
  
  try {
    searchingCustomer.value = true
    
    const response = await axios.post('/api/public/customers/find-by-document', {
      document: formData.value.customer_document
    })
    
    if (response.data.success && response.data.found) {
      formData.value.customer_name = response.data.customer.name
      formData.value.customer_phone = response.data.customer.phone
      formData.value.customer_email = response.data.customer.email || ''
      formData.value.customer_address = response.data.customer.address || ''
      
      console.log('✅ Cliente encontrado:', response.data.customer.name)
    } else {
      console.log('ℹ️ Cliente no encontrado, permitir llenado manual')
    }
  } catch (error) {
    console.error('⚠️ Error buscando cliente:', error)
  } finally {
    searchingCustomer.value = false
  }
}

const handleCheckoutSubmit = async () => {
  if (cartTotal.value < props.storeConfig.min_order_value) return

  try {
    submittingOrder.value = true

    const items = cartItems.value.map(item => ({
      product_id: item.id,
      quantity: item.quantity || 1,
      special_instructions: item.special_instructions || null
    }))

    const response = await axios.post('/api/public/orders', {
      ...formData.value,
      items
    })

    if (response.data.success) {
      const order = response.data.order
      
      // Guardar datos ANTES de resetear
      const customerData = { ...formData.value }
      const orderItems = [...cartItems.value]
      
      // Cerrar modales
      showCheckoutForm.value = false
      showCheckout.value = false
      
      // Vaciar carrito
      cartItems.value = []
      
      // Resetear formulario
      formData.value = {
        customer_name: '',
        customer_phone: '',
        customer_document: '',
        customer_email: '',
        delivery_type: 'delivery',
        customer_address: '',
        note: ''
      }
      
      // Crear mensaje usando configuración personalizada
      const greeting = props.storeConfig.custom_message || 'Hola, quiero hacer el siguiente pedido:'
      let message = `${greeting}\n\n`
      message += `📋 *Código: ${order.order_number}*\n\n`
      message += `👤 ${customerData.customer_name}\n`
      message += `📱 ${customerData.customer_phone}\n\n`
      
      if (customerData.delivery_type === 'delivery') {
        message += `🚚 Envío a: ${customerData.customer_address}\n\n`
      } else {
        message += `🏪 Recoger en tienda\n\n`
      }
      
      message += `📦 *Productos:*\n`
      orderItems.forEach((item, index) => {
        message += `${index + 1}. ${item.name} x${item.quantity || 1}\n`
      })
      
      const deliveryCost = customerData.delivery_type === 'delivery' ? parseFloat(props.storeConfig.delivery_cost || 0) : 0
      const finalTotal = parseFloat(order.total) + deliveryCost
      
      message += `\n💰 Total: ${props.storeConfig.currency_symbol}${formatPrice(finalTotal)}`

      if (customerData.note) {
        message += `\n\n📝 ${customerData.note}`
      }

      const whatsappUrl = `https://wa.me/${props.storeConfig.whatsapp_number}?text=${encodeURIComponent(message)}`
      window.open(whatsappUrl, '_blank')
    }
  } catch (error) {
    console.error('Error al crear pedido:', error)
    alert('❌ Error al crear el pedido. Por favor intenta nuevamente.')
  } finally {
    submittingOrder.value = false
  }
}

const handleScroll = () => {
  isScrolled.value = window.scrollY > 100
}

const scrollToProducts = () => {
  productsSection.value?.scrollIntoView({ behavior: 'smooth' })
}

const openProductDetails = (product) => {
  selectedProduct.value = product
  selectedOptions.value = {} // Reset options
  selectedImageIndex.value = 0 // Reset image index
  activeAccordion.value = 'description'
  document.body.style.overflow = 'hidden'
}

const closeProductDetails = () => {
  selectedProduct.value = null
  selectedImageIndex.value = 0
  document.body.style.overflow = ''
}

// Obtener el valor de la opción seleccionada para mostrar en el UI
const getSelectedOptionValue = (option) => {
  const selectedValueId = selectedOptions.value[option.id]
  if (!selectedValueId) return ''
  const val = option.values.find(v => v.id === selectedValueId)
  return val ? val.value : ''
}

const toggleAccordion = (section) => {
  activeAccordion.value = activeAccordion.value === section ? null : section
}

const addToCartFromDetail = () => {
  if (selectedProduct.value) {
    // Si es un producto con variantes pero no se ha seleccionado una, no hacer nada
    if (!isVariantSelected.value) return

    // Crear el producto para agregar al carrito con la información ya seleccionada
    const productToAdd = {
      ...selectedProduct.value,
      price: currentPrice.value,
      stock: currentStock.value,
      variant_id: currentVariant.value ? currentVariant.value.id : null,
      selected_options: { ...selectedOptions.value }
    }
    
    // Agregar directamente al carrito sin pasar por addToCart() que abriría otro modal
    cartItems.value.push(productToAdd)
    
    // Animación visual
    const event = new CustomEvent('cart-updated', { detail: { action: 'add' } })
    window.dispatchEvent(event)
    
    // Cerrar modal de detalle y abrir carrito
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
