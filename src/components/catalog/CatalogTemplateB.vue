<template>
  <!-- PLANTILLA B: "SPEED MARKET" - E-commerce Profesional -->
  <div class="catalog-speed-market min-h-screen h-full bg-gradient-to-b from-slate-50 via-gray-50 to-white">
    
    <!-- HEADER ELEGANTE: Logo + Buscador -->
    <header class="sticky top-0 z-50 bg-white shadow-sm border-b border-gray-200/80">
      <div class="container mx-auto px-4 md:px-6 py-3 md:py-4 flex items-center gap-3 md:gap-6 max-w-7xl">
        <!-- Logo con Nombre de Tienda -->
        <div class="flex items-center gap-2 md:gap-3 flex-shrink-0">
          <img 
            :src="storeConfig.logo_url || 'https://via.placeholder.com/60'"
            alt="Logo"
            class="h-10 w-10 md:h-14 md:w-14 object-contain rounded-xl shadow-sm"
          />
          <div class="hidden md:block">
            <h1 class="text-lg font-bold text-gray-900 leading-tight">{{ storeName }}</h1>
            <p class="text-xs text-gray-500">Catálogo de Productos</p>
          </div>
        </div>
        
        <!-- Buscador Premium -->
        <div class="flex-1 relative max-w-2xl">
          <input 
            type="text"
            v-model="searchQuery"
            placeholder="Buscar productos..."
            class="w-full h-10 md:h-12 pl-10 md:pl-12 pr-4 rounded-xl border-2 border-gray-200 bg-gray-50 focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 outline-none transition-colors duration-150 text-sm font-medium text-gray-900 placeholder-gray-400"
          />
          <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 md:h-5 md:w-5 text-gray-400 absolute left-3 md:left-4 top-1/2 -translate-y-1/2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
          </svg>
        </div>

        <!-- Cart Icon Mejorado -->
        <button 
          @click="openCartWithExplosion"
          class="cart-icon-button relative p-2 md:p-3 hover:bg-gray-100 active:bg-gray-200 rounded-xl transition-transform duration-150 active:scale-95 flex-shrink-0 touch-manipulation"
        >
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 md:h-6 md:w-6 text-gray-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
          </svg>
          <!-- Badge cuando hay items -->
          <Transition name="badge-pop">
            <span 
              v-if="cartItems.length > 0" 
              class="absolute -top-0.5 -right-0.5 text-white text-xs font-black w-5 h-5 rounded-full flex items-center justify-center shadow-lg" 
              :style="{ backgroundColor: primaryColor }"
            >
              {{ cartItems.length }}
            </span>
          </Transition>
        </button>
      </div>
    </header>

    <!-- NAVEGACIÓN POR CATEGORÍAS: Pills Modernas STICKY -->
    <nav class="sticky top-[64px] md:top-[84px] z-40 bg-white shadow-sm border-b border-gray-100/80 py-3 md:py-4 px-3 md:px-6 overflow-x-auto scrollbar-hide">
      <div class="container mx-auto max-w-7xl">
        <div class="flex gap-2 md:gap-3 min-w-max pb-1">
          <button
            @click="selectedCategory = null"
            class="px-4 md:px-6 py-2 md:py-2.5 rounded-xl text-xs md:text-sm font-bold whitespace-nowrap transition-colors duration-150 flex-shrink-0 border-2 touch-manipulation active:scale-95"
            :class="selectedCategory === null 
              ? 'text-white shadow-lg shadow-blue-500/25 border-transparent' 
              : 'bg-white text-gray-600 hover:bg-gray-50 active:bg-gray-100 border-gray-200 hover:border-gray-300'"
            :style="selectedCategory === null ? { backgroundColor: primaryColor } : {}"
          >
            📦 Todos
          </button>
          <button
            v-for="cat in categories"
            :key="cat.id"
            @click="selectedCategory = cat.id"
            class="px-4 md:px-6 py-2 md:py-2.5 rounded-xl text-xs md:text-sm font-bold whitespace-nowrap transition-colors duration-150 flex-shrink-0 border-2 touch-manipulation active:scale-95"
            :class="selectedCategory === cat.id 
              ? 'text-white shadow-lg shadow-blue-500/25 border-transparent' 
              : 'bg-white text-gray-600 hover:bg-gray-50 active:bg-gray-100 border-gray-200 hover:border-gray-300'"
            :style="selectedCategory === cat.id ? { backgroundColor: primaryColor } : {}"
          >
            {{ cat.name }}
          </button>
        </div>
      </div>
    </nav>

    <!-- LISTADO DE PRODUCTOS: Grid Premium Desktop -->
    <main class="container mx-auto px-6 py-8 pb-12 max-w-7xl transition-opacity duration-200" :class="showCheckout ? 'mr-[35%] blur-[2px] opacity-60' : ''">
      <!-- Grid de Cards para Desktop -->
      <div class="hidden md:grid md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-5">
        <TransitionGroup name="list">
          <div 
            v-for="product in filteredProducts" 
            :key="product.id"
            class="product-card bg-white rounded-2xl shadow-md hover:shadow-lg transition-shadow duration-150 border border-gray-100 overflow-hidden group cursor-pointer relative"
            :ref="el => { if (el) productRefs[product.id] = el }"
          >
            <!-- Imagen Premium -->
            <div class="relative aspect-square bg-gradient-to-br from-gray-50 to-gray-100 overflow-hidden">
              <!-- Imagen del Producto -->
              <img 
                v-if="(product.images && product.images.length > 0) || (product.image_url && product.image_url !== 'https://via.placeholder.com/400' && !imageErrors[product.id])"
                :src="product.images && product.images.length > 0 ? product.images[0] : product.image_url"
                :alt="product.name"
                @error="handleImageError(product.id)"
                class="w-full h-full object-cover"
              />
              
              <!-- Placeholder Mejorado -->
              <div v-else class="w-full h-full flex items-center justify-center">
                <div class="text-center">
                  <svg class="w-20 h-20 text-gray-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                  </svg>
                  <p class="text-xs text-gray-400 font-semibold">Sin imagen</p>
                </div>
              </div>

              <!-- Badge Agotado con Glassmorphism -->
              <div v-if="product.stock === 0" class="absolute inset-0 bg-black/75 flex items-center justify-center">
                <div class="bg-white/20 px-4 py-2 rounded-full border border-white/30">
                  <span class="text-white text-sm font-black tracking-wide">AGOTADO</span>
                </div>
              </div>
              
              <!-- Badge Stock Bajo -->
              <div v-else-if="product.stock < 5" class="absolute top-3 right-3 bg-gradient-to-r from-amber-500 to-orange-500 text-white text-xs font-black px-3 py-1.5 rounded-full shadow-lg">
                ⚡ Solo {{ product.stock }}
              </div>
            </div>

            <!-- Info Card Mejorada -->
            <div class="p-4">
              <h3 class="font-bold text-gray-900 text-sm line-clamp-2 mb-2 min-h-[2.5rem] leading-snug">
                {{ product.name }}
              </h3>
              <p class="text-xs text-gray-500 line-clamp-1 mb-3">
                {{ product.description || 'Producto disponible' }}
              </p>
              
              <!-- Precio Premium y Botón -->
              <div class="flex items-center justify-between gap-3">
                <div>
                  <p class="text-2xl font-black text-gray-900 leading-none">
                    {{ storeConfig.currency_symbol }}{{ formatPrice(product.price) }}
                  </p>
                </div>
                <button
                  @click="addToCartWithAnimation(product, $event)"
                  :disabled="product.stock === 0"
                  class="p-3 rounded-xl text-white active:scale-95 transition-transform duration-150 disabled:bg-gray-300 disabled:cursor-not-allowed shadow-lg flex-shrink-0"
                  :style="product.stock > 0 ? { backgroundColor: primaryColor } : {}"
                >
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                  </svg>
                </button>
              </div>
            </div>
          </div>
        </TransitionGroup>
      </div>

      <!-- Lista Horizontal para Móvil -->
      <TransitionGroup name="list" tag="div" class="space-y-3 md:hidden">
        <div 
          v-for="product in filteredProducts" 
          :key="product.id"
          :ref="el => { if (el) productRefs[product.id] = el }"
          @click="getProductQuantity(product.id) === 0 && product.stock > 0 && addToCartWithAnimation(product, $event)"
          class="bg-white rounded-xl shadow-sm hover:shadow-md transition-shadow duration-100 border border-gray-100 overflow-hidden group touch-manipulation"
          :class="[
            product.stock === 0 ? 'opacity-50' : (getProductQuantity(product.id) === 0 ? 'cursor-pointer active:scale-[0.98] active:shadow-lg' : '')
          ]"
        >
          <div class="flex items-center gap-3 p-3">
            <!-- Foto Pequeña (Izquierda) con animación de vuelo -->
            <div 
              class="relative w-20 h-20 flex-shrink-0 rounded-lg overflow-hidden bg-gray-50"
              @click.stop="getProductQuantity(product.id) === 0 && product.stock > 0 && addToCartWithAnimation(product, $event)"
            >
              <!-- Imagen del Producto -->
              <img 
                v-if="(product.images && product.images.length > 0) || (product.image_url && product.image_url !== 'https://via.placeholder.com/400' && !imageErrors[product.id])"
                :src="product.images && product.images.length > 0 ? product.images[0] : product.image_url"
                :alt="product.name"
                @error="handleImageError(product.id)"
                class="w-full h-full object-cover"
              />
              
              <!-- Placeholder cuando no hay imagen -->
              <div v-else class="w-full h-full flex items-center justify-center bg-gradient-to-br from-gray-50 to-gray-100">
                <div class="text-center">
                  <svg class="w-8 h-8 text-gray-300 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                  </svg>
                  <p class="text-[10px] text-gray-400 font-medium mt-0.5">Sin imagen</p>
                </div>
              </div>
              <!-- Badge Stock -->
              <div v-if="product.stock === 0" class="absolute inset-0 bg-black/60 flex items-center justify-center">
                <span class="text-white text-xs font-bold">Agotado</span>
              </div>
            </div>

            <!-- Info Centro -->
            <div class="flex-1 min-w-0">
              <h3 class="font-bold text-gray-900 text-sm line-clamp-1 mb-1">{{ product.name }}</h3>
              <p class="text-xs text-gray-500 line-clamp-1 mb-2">{{ product.description || 'Sin descripción' }}</p>
              <div class="flex items-center gap-3">
                <p class="text-lg font-black text-gray-900">
                  {{ storeConfig.currency_symbol }}{{ formatPrice(product.price) }}
                </p>
                <span v-if="product.stock < 5 && product.stock > 0" class="text-xs font-bold text-amber-600 bg-amber-50 px-2 py-0.5 rounded-full">
                  Solo {{ product.stock }}
                </span>
              </div>
            </div>

            <!-- Indicador de cantidad agregada -->
            <div class="flex items-center gap-2 flex-shrink-0">
              <!-- Badge VERDE grande cuando está agregado -->
              <div 
                v-if="getProductQuantity(product.id) > 0"
                class="relative"
              >
                <!-- Badge principal VERDE -->
                <div 
                  class="relative px-4 py-2.5 rounded-full bg-gradient-to-r from-emerald-500 to-emerald-600 text-white text-base font-black flex items-center gap-2 shadow-lg border-2 border-emerald-400"
                >
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                  </svg>
                  <span class="text-lg">{{ getProductQuantity(product.id) }}</span>
                </div>
              </div>
              
              <!-- Icono de + cuando NO está agregado -->
              <div 
                v-else
                class="w-11 h-11 rounded-full flex items-center justify-center border-2"
                :style="{ borderColor: primaryColor, color: primaryColor }"
              >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 font-bold" viewBox="0 0 20 20" fill="currentColor">
                  <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                </svg>
              </div>
            </div>
          </div>
        </div>
      </TransitionGroup>

      <!-- Empty State -->
      <div v-if="filteredProducts.length === 0" class="col-span-full text-center py-20">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-24 w-24 mx-auto text-gray-300 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        <p class="text-gray-500 text-lg font-medium">No encontramos productos</p>
        <p class="text-gray-400 text-sm mt-2">Intenta con otra búsqueda</p>
      </div>
    </main>

    <!-- MINIATURAS VOLANDO (Animación) -->
    <Teleport to="body">
      <TransitionGroup name="fly">
        <div
          v-for="item in flyingItems"
          :key="item.id + '-' + item.timestamp"
          class="fixed z-[200] pointer-events-none"
          :style="{
            left: item.startX + 'px',
            top: item.startY + 'px',
            '--deltaX': (item.endX - item.startX) + 'px',
            '--deltaY': (item.endY - item.startY) + 'px'
          }"
        >
          <div :class="item.isExplosion ? 'explosion-animation' : 'flying-product-animation'" :style="item.isExplosion ? getExplosionStyle(item.timestamp) : {}">
            <img 
              :src="item.image_url" 
              class="w-16 h-16 rounded-xl shadow-2xl object-cover ring-4 ring-white"
              :style="{ boxShadow: `0 0 30px ${primaryColor}` }"
            />
          </div>
        </div>
      </TransitionGroup>
    </Teleport>

    <!-- PANEL LATERAL DERECHO (SIDE DRAWER) -->
    <Teleport to="body">
      <!-- Overlay (solo desktop) -->
      <Transition name="fade">
        <div 
          v-if="showCheckout" 
          class="hidden md:block fixed inset-0 bg-black/60 z-[90]" 
          @click="showCheckout = false"
        ></div>
      </Transition>
      
      <!-- Panel Deslizante (Full screen móvil, lateral desktop) -->
      <Transition name="slide-right">
        <div 
          v-if="showCheckout" 
          class="fixed inset-0 md:top-0 md:right-0 md:left-auto md:h-full md:w-[420px] lg:w-[450px] bg-gradient-to-b from-gray-50 to-white dark:from-zinc-900 dark:to-zinc-950 shadow-2xl z-[100] flex flex-col"
        >
          <!-- Header Premium del Panel con Glassmorphism -->
          <div class="p-6 border-b border-gray-200/80 dark:border-zinc-800/80 backdrop-blur-xl bg-white/80 dark:bg-zinc-900/80">
            <div class="flex items-center justify-between">
              <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-2xl flex items-center justify-center shadow-lg bg-gradient-to-br" :style="{ backgroundImage: `linear-gradient(135deg, ${primaryColor}, ${primaryColor}dd)` }">
                  <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                  </svg>
                </div>
                <div>
                  <h3 class="text-2xl font-black text-gray-900 dark:text-white">Tu Pedido</h3>
                  <p class="text-sm font-medium text-gray-500 dark:text-zinc-400 mt-0.5">
                    <span class="inline-flex items-center gap-1.5">
                      <span class="w-1.5 h-1.5 rounded-full" :style="{ backgroundColor: primaryColor }"></span>
                      {{ cartItems.length }} producto{{ cartItems.length !== 1 ? 's' : '' }}
                    </span>
                  </p>
                </div>
              </div>
              <button 
                @click="showCheckout = false" 
                class="w-11 h-11 rounded-xl hover:bg-gray-100 dark:hover:bg-zinc-800 active:bg-gray-200 dark:active:bg-zinc-700 flex items-center justify-center text-gray-400 dark:text-zinc-500 hover:text-gray-600 dark:hover:text-zinc-300 transition-all active:scale-95 touch-manipulation shadow-sm"
              >
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
              </button>
            </div>
          </div>


          <!-- VISTA DEL CARRITO (cuando no está en checkout) -->
          <template v-if="!showCheckoutForm">
            <!-- Contenido Scrollable del Panel -->
            <div class="flex-1 overflow-y-auto p-6 space-y-4">
              <!-- Lista de Productos en el Carrito con Animación Secuencial -->
              <TransitionGroup name="slide-in-right" tag="div" class="space-y-3">
                <div 
                  v-for="item in visibleCartItems" 
                  :key="item.id"
                  class="bg-white dark:bg-zinc-900 rounded-2xl p-4 border border-gray-200 dark:border-zinc-800 hover:border-gray-300 dark:hover:border-zinc-700 hover:shadow-lg dark:shadow-black/30 transition-all duration-200 group"
                >
                  <div class="flex gap-4">
                    <!-- Imagen del Producto con Badge -->
                    <div class="relative flex-shrink-0">
                      <!-- Imagen si existe -->
                      <img 
                        v-if="item.image_url && item.image_url !== 'https://via.placeholder.com/400'"
                        :src="item.image_url" 
                        @error="(e) => e.target.style.display = 'none'"
                        class="w-20 h-20 object-cover rounded-xl shadow-md border border-gray-100 dark:border-zinc-800" 
                      />
                      <!-- Placeholder si no hay imagen -->
                      <div 
                        v-if="!item.image_url || item.image_url === 'https://via.placeholder.com/400'"
                        class="w-20 h-20 bg-gradient-to-br from-gray-50 to-gray-100 dark:from-zinc-800 dark:to-zinc-900 rounded-xl flex items-center justify-center border border-gray-200 dark:border-zinc-700"
                      >
                        <div class="text-center">
                          <svg class="w-9 h-9 text-gray-300 dark:text-zinc-600 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                          </svg>
                          <p class="text-[9px] text-gray-400 dark:text-zinc-500 font-semibold mt-1 uppercase tracking-wide">Sin foto</p>
                        </div>
                      </div>
                      <!-- Badge de Cantidad con Gradiente -->
                      <div class="absolute -top-2 -right-2 w-7 h-7 rounded-full text-white text-xs font-black flex items-center justify-center shadow-lg border-2 border-white dark:border-zinc-900" :style="{ background: `linear-gradient(135deg, ${primaryColor}, ${primaryColor}dd)` }">
                        {{ item.quantity }}
                      </div>
                    </div>

                    <!-- Info del Producto -->
                    <div class="flex-1 min-w-0">
                      <h4 class="font-bold text-gray-900 dark:text-white text-base line-clamp-2 mb-1.5 leading-tight">
                        {{ item.name }}
                      </h4>
                      <p class="text-sm text-gray-500 dark:text-zinc-400 font-medium mb-3">
                        {{ storeConfig.currency_symbol }}{{ formatPrice(item.price) }} <span class="text-xs">c/u</span>
                      </p>
                      
                      <!-- Controles de Cantidad con Diseño Pro -->
                      <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2 bg-gray-50 dark:bg-zinc-800 rounded-xl px-2 py-2 border border-gray-200 dark:border-zinc-700">
                          <button 
                            @click="decreaseQuantity(item.id)" 
                            class="w-8 h-8 rounded-lg hover:bg-gray-200 dark:hover:bg-zinc-700 active:bg-gray-300 dark:active:bg-zinc-600 flex items-center justify-center text-gray-600 dark:text-zinc-300 active:scale-90 transition-all touch-manipulation"
                          >
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                              <path fill-rule="evenodd" d="M3 10a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd" />
                            </svg>
                          </button>
                          <span class="text-base font-black text-gray-900 dark:text-white w-8 text-center">
                            {{ item.quantity }}
                          </span>
                          <button 
                            @click="increaseQuantity(item.id, item)" 
                            class="w-8 h-8 rounded-lg flex items-center justify-center text-white active:scale-90 transition-all touch-manipulation shadow-md"
                            :style="{ background: `linear-gradient(135deg, ${primaryColor}, ${primaryColor}dd)` }"
                          >
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                              <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                            </svg>
                          </button>
                        </div>
                        
                        <!-- Precio Total del Item con Estilo Pro -->
                        <p class="text-lg font-black text-gray-900 dark:text-white">
                          {{ storeConfig.currency_symbol }}{{ formatPrice(item.price * item.quantity) }}
                        </p>
                      </div>
                    </div>

                    <!-- Botón Eliminar Mejorado -->
                    <button 
                      @click="removeFromCart(item.id)"
                      class="flex-shrink-0 w-10 h-10 rounded-xl hover:bg-red-50 dark:hover:bg-red-950/30 text-gray-400 dark:text-zinc-500 hover:text-red-600 dark:hover:text-red-400 flex items-center justify-center transition-all active:scale-90 touch-manipulation border border-transparent hover:border-red-100 dark:hover:border-red-900/30"
                    >
                      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                      </svg>
                    </button>
                  </div>
                </div>
              </TransitionGroup>

              <!-- Empty State del Carrito Mejorado -->
              <div v-if="cartItems.length === 0" class="text-center py-16">
                <div class="w-24 h-24 mx-auto rounded-3xl bg-gradient-to-br from-gray-100 to-gray-50 dark:from-zinc-800 dark:to-zinc-900 flex items-center justify-center mb-6 shadow-lg border-2 border-gray-200 dark:border-zinc-700">
                  <svg class="w-12 h-12 text-gray-400 dark:text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                  </svg>
                </div>
                <p class="text-xl font-black text-gray-900 dark:text-white mb-2">Tu carrito está vacío</p>
                <p class="text-sm text-gray-500 dark:text-zinc-400 font-medium">Agrega productos para comenzar tu pedido</p>
              </div>
            </div>

            <!-- Footer Fijo del Panel (Resumen y Botón) - Mejorado con Glassmorphism -->
            <div class="border-t border-gray-200 dark:border-zinc-800 p-6 bg-gradient-to-t from-white to-gray-50 dark:from-zinc-900 dark:to-zinc-900/50 backdrop-blur-xl space-y-4">
              <!-- Resumen de Totales con Diseño Pro -->
              <div class="space-y-3">
                <div class="flex justify-between items-center text-sm">
                  <span class="text-gray-600 dark:text-zinc-400 font-semibold">Subtotal</span>
                  <span class="font-black text-gray-900 dark:text-white text-base">{{ storeConfig.currency_symbol }}{{ formatPrice(cartTotal) }}</span>
                </div>
                
                <!-- Separador Elegante -->
                <div class="relative py-2">
                  <div class="absolute inset-0 flex items-center">
                    <div class="w-full border-t-2 border-dashed border-gray-300 dark:border-zinc-700"></div>
                  </div>
                </div>
                
                <!-- Total con Diseño Destacado -->
                <div class="flex justify-between items-center p-4 rounded-2xl bg-gradient-to-br from-gray-100 to-gray-50 dark:from-zinc-800 dark:to-zinc-900 border-2 border-gray-200 dark:border-zinc-700">
                  <span class="text-xl font-black text-gray-900 dark:text-white">Total</span>
                  <div class="text-right">
                    <p class="text-3xl font-black text-gray-900 dark:text-white" :style="{ color: primaryColor }">
                      {{ storeConfig.currency_symbol }}{{ formatPrice(cartTotal) }}
                    </p>
                    <p class="text-xs text-gray-500 dark:text-zinc-400 font-medium mt-0.5">
                      {{ cartItems.length }} producto{{ cartItems.length !== 1 ? 's' : '' }}
                    </p>
                  </div>
                </div>
              </div>

              <!-- Alerta de Pedido Mínimo Mejorada -->
              <div 
                v-if="cartTotal < storeConfig.min_order_value && cartItems.length > 0" 
                class="bg-gradient-to-r from-amber-50 to-orange-50 dark:from-amber-950/30 dark:to-orange-950/30 border-2 border-amber-300 dark:border-amber-800 rounded-2xl p-4 flex items-start gap-3 shadow-sm"
              >
                <div class="w-10 h-10 rounded-xl bg-amber-100 dark:bg-amber-900/50 flex items-center justify-center flex-shrink-0">
                  <svg class="w-5 h-5 text-amber-600 dark:text-amber-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                  </svg>
                </div>
                <div class="flex-1">
                  <p class="font-bold text-amber-900 dark:text-amber-300 text-sm mb-1">Pedido mínimo: {{ storeConfig.currency_symbol }}{{ formatPrice(storeConfig.min_order_value) }}</p>
                  <p class="text-amber-700 dark:text-amber-400 text-xs font-medium">Faltan {{ storeConfig.currency_symbol }}{{ formatPrice(storeConfig.min_order_value - cartTotal) }} para completar tu pedido</p>
                </div>
              </div>

              <!-- Botón Principal - Completar Pedido -->
              <button 
                @click="showCheckoutForm = true"
                :disabled="cartTotal < storeConfig.min_order_value || cartItems.length === 0"
                class="w-full bg-gradient-to-r from-[#25D366] to-[#1ebe57] hover:from-[#1ebe57] hover:to-[#128C7E] disabled:from-gray-300 disabled:to-gray-400 text-white py-4 rounded-2xl font-black text-base flex items-center justify-center gap-3 shadow-2xl hover:shadow-3xl transition-all disabled:cursor-not-allowed active:scale-[0.98] touch-manipulation"
              >
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>Completar Pedido</span>
              </button>
            </div>
          </template>

          <!-- FORMULARIO DE CHECKOUT (cuando está en modo checkout) -->
          <template v-else>
            <!-- Botón Volver al Carrito -->
            <div class="px-6 pt-6 pb-4">
              <button 
                @click="showCheckoutForm = false" 
                class="flex items-center gap-2 text-gray-600 dark:text-zinc-400 hover:text-gray-900 dark:hover:text-white font-semibold text-sm transition-colors"
              >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                Volver al carrito
              </button>
            </div>

            <!-- Formulario Scrollable -->
            <div class="flex-1 overflow-y-auto px-6 pb-6 space-y-5">
              <div>
                <h3 class="text-lg font-black text-gray-900 dark:text-white mb-1">Datos del Cliente</h3>
                <p class="text-sm text-gray-500 dark:text-zinc-400">Completa tu información para procesar el pedido</p>
              </div>

              <!-- Cédula/Documento - PRIMERO para autocompletar -->
              <div>
                <label class="block text-sm font-bold text-gray-700 dark:text-zinc-300 mb-2">
                  Cédula / Documento <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                  <input 
                    v-model="formData.customer_document"
                    @blur="searchCustomerByDocument"
                    type="text"
                    required
                    minlength="6"
                    placeholder="1234567890"
                    :disabled="searchingCustomer"
                    class="w-full px-4 py-3 border-2 border-gray-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-gray-900 dark:text-zinc-200 rounded-xl text-sm font-medium focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 placeholder-gray-400 dark:placeholder-zinc-500 transition-all disabled:opacity-50"
                  />
                  <!-- Indicador de búsqueda -->
                  <div v-if="searchingCustomer" class="absolute right-3 top-1/2 -translate-y-1/2">
                    <svg class="animate-spin h-5 w-5 text-blue-500" fill="none" viewBox="0 0 24 24">
                      <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                      <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                  </div>
                </div>
              </div>

              <!-- Nombre Completo -->
              <div>
                <label class="block text-sm font-bold text-gray-700 dark:text-zinc-300 mb-2">
                  Nombre Completo <span class="text-red-500">*</span>
                </label>
                <input 
                  v-model="formData.customer_name"
                  type="text"
                  required
                  placeholder="Juan Pérez"
                  class="w-full px-4 py-3 border-2 border-gray-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-gray-900 dark:text-zinc-200 rounded-xl text-sm font-medium focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 placeholder-gray-400 dark:placeholder-zinc-500 transition-all"
                />
              </div>

              <!-- Teléfono -->
              <div>
                <label class="block text-sm font-bold text-gray-700 dark:text-zinc-300 mb-2">
                  Teléfono <span class="text-red-500">*</span>
                </label>
                <input 
                  v-model="formData.customer_phone"
                  type="tel"
                  required
                  placeholder="3001234567"
                  class="w-full px-4 py-3 border-2 border-gray-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-gray-900 dark:text-zinc-200 rounded-xl text-sm font-medium focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 placeholder-gray-400 dark:placeholder-zinc-500 transition-all"
                />
              </div>

              <!-- Email (opcional) -->
              <div>
                <label class="block text-sm font-bold text-gray-700 dark:text-zinc-300 mb-2">
                  Correo Electrónico <span class="text-gray-400 text-xs">(Opcional)</span>
                </label>
                <input 
                  v-model="formData.customer_email"
                  type="email"
                  placeholder="correo@ejemplo.com"
                  class="w-full px-4 py-3 border-2 border-gray-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-gray-900 dark:text-zinc-200 rounded-xl text-sm font-medium focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 placeholder-gray-400 dark:placeholder-zinc-500 transition-all"
                />
              </div>

              <!-- Tipo de Entrega -->
              <div>
                <label class="block text-sm font-bold text-gray-700 dark:text-zinc-300 mb-3">
                  Tipo de Entrega <span class="text-red-500">*</span>
                </label>
                <div class="grid grid-cols-2 gap-3">
                  <button 
                    @click="formData.delivery_type = 'delivery'"
                    type="button"
                    class="px-4 py-4 rounded-xl border-2 font-bold text-sm flex flex-col items-center gap-2 transition-all"
                    :class="formData.delivery_type === 'delivery' 
                      ? 'border-blue-500 dark:border-blue-400 bg-blue-50 dark:bg-blue-950/30 text-blue-700 dark:text-blue-400' 
                      : 'border-gray-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 text-gray-600 dark:text-zinc-400 hover:border-gray-300 dark:hover:border-zinc-600'"
                  >
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0" />
                    </svg>
                    Envío a Domicilio
                  </button>
                  <button 
                    @click="formData.delivery_type = 'pickup'"
                    type="button"
                    class="px-4 py-4 rounded-xl border-2 font-bold text-sm flex flex-col items-center gap-2 transition-all"
                    :class="formData.delivery_type === 'pickup' 
                      ? 'border-blue-500 dark:border-blue-400 bg-blue-50 dark:bg-blue-950/30 text-blue-700 dark:text-blue-400' 
                      : 'border-gray-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 text-gray-600 dark:text-zinc-400 hover:border-gray-300 dark:hover:border-zinc-600'"
                  >
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                    Recoger en Tienda
                  </button>
                </div>
              </div>

              <!-- Dirección (solo si es delivery) -->
              <div v-if="formData.delivery_type === 'delivery'">
                <label class="block text-sm font-bold text-gray-700 dark:text-zinc-300 mb-2">
                  Dirección de Entrega <span class="text-red-500">*</span>
                </label>
                <textarea 
                  v-model="formData.customer_address"
                  required
                  rows="3"
                  placeholder="Calle 123 #45-67, Apartamento 801, Torre B"
                  class="w-full px-4 py-3 border-2 border-gray-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-gray-900 dark:text-zinc-200 rounded-xl text-sm font-medium focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 placeholder-gray-400 dark:placeholder-zinc-500 transition-all resize-none"
                ></textarea>
              </div>

              <!-- Notas Especiales (opcional) -->
              <div>
                <label class="block text-sm font-bold text-gray-700 dark:text-zinc-300 mb-2">
                  Notas Especiales <span class="text-gray-400 text-xs">(Opcional)</span>
                </label>
                <textarea 
                  v-model="formData.note"
                  rows="2"
                  placeholder="Ej: Tocar el timbre, dejar en portería, etc."
                  class="w-full px-4 py-3 border-2 border-gray-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-gray-900 dark:text-zinc-200 rounded-xl text-sm font-medium focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 placeholder-gray-400 dark:placeholder-zinc-500 transition-all resize-none"
                ></textarea>
              </div>
            </div>

            <!-- Footer con botón de confirmar -->
            <div class="border-t border-gray-200 dark:border-zinc-800 p-6 bg-gradient-to-t from-white to-gray-50 dark:from-zinc-900 dark:to-zinc-900/50 space-y-3">
              <!-- Resumen rápido -->
              <div class="p-4 rounded-xl bg-gray-50 dark:bg-zinc-800 border border-gray-200 dark:border-zinc-700">
                <div class="flex justify-between items-center mb-2">
                  <span class="text-sm font-semibold text-gray-600 dark:text-zinc-400">Total a Pagar:</span>
                  <span class="text-2xl font-black" :style="{ color: primaryColor }">
                    {{ storeConfig.currency_symbol }}{{ formatPrice(cartTotal + (formData.delivery_type === 'delivery' ? storeConfig.delivery_cost : 0)) }}
                  </span>
                </div>
                <p class="text-xs text-gray-500 dark:text-zinc-500">
                  {{ cartItems.length }} producto{{ cartItems.length !== 1 ? 's' : '' }} • {{ formData.delivery_type === 'delivery' ? 'Envío a domicilio' : 'Recoger en tienda' }}
                </p>
              </div>

              <button 
                @click="handleCheckoutSubmit"
                :disabled="submittingOrder || !formData.customer_name || !formData.customer_phone || !formData.customer_document || formData.customer_document.length < 6 || (formData.delivery_type === 'delivery' && !formData.customer_address)"
                class="w-full bg-gradient-to-r from-[#25D366] to-[#1ebe57] hover:from-[#1ebe57] hover:to-[#128C7E] disabled:from-gray-300 disabled:to-gray-400 text-white py-4 rounded-2xl font-black text-base flex items-center justify-center gap-3 shadow-2xl hover:shadow-3xl transition-all disabled:cursor-not-allowed active:scale-[0.98]"
              >
                <!-- Icono WhatsApp -->
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
    </Teleport>

    <!-- 👗 Modal de Selección de Variantes (Fashion) -->
    <POSVariantSelector
      :show="showVariantModal"
      :product="selectedProduct"
      @close="showVariantModal = false"
      @confirm="handleVariantConfirmed"
    />

    <!-- ⚖️ Modal de Cantidad (Productos por peso/medida) -->
    <QuantityModal
      :show="showQuantityModal"
      :product="selectedProductForQuantity"
      @close="showQuantityModal = false"
      @confirm="handleQuantityConfirmed"
    />

  </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import axios from 'axios'
import POSVariantSelector from '../POSVariantSelector.vue'
import QuantityModal from './QuantityModal.vue'
import CheckoutForm from './CheckoutForm.vue'

const props = defineProps({
  storeConfig: {
    type: Object,
    required: true,
    default: () => ({
      primary_color: '#10B981',
      logo_url: '',
      whatsapp_number: '',
      currency_symbol: '$',
      delivery_cost: 0,
      min_order_value: 0,
      custom_message: 'Hola, quiero hacer el siguiente pedido:',
      catalog_products: []
    })
  },
  categories: {
    type: Array,
    default: () => []
  }
})

// Estado
const searchQuery = ref('')
const selectedCategory = ref(null)
const cartItems = ref([])
const showCheckout = ref(false)
const loadingImages = ref({})
const imageErrors = ref({})
const flyingItems = ref([])
const productRefs = ref({})
const visibleCartItems = ref([]) // Items que ya se mostraron con animación

// Estado del modal de variantes
const showVariantModal = ref(false)
const selectedProduct = ref(null)

// Estado del modal de cantidad (peso/medida)
const showQuantityModal = ref(false)
const selectedProductForQuantity = ref(null)

// Estado del formulario de checkout
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

// Computed
const primaryColor = computed(() => {
  return props.storeConfig.primary_color || '#3B82F6' // Blue-500 por defecto
})

const storeName = computed(() => {
  return props.storeConfig.store_name || 'Tienda Online'
})

const filteredProducts = computed(() => {
  let products = props.storeConfig.catalog_products || []
  
  // Filtrar por categoría usando category_id
  if (selectedCategory.value !== null) {
    products = products.filter(p => p.category_id === selectedCategory.value)
  }
  
  // Filtrar por búsqueda
  if (searchQuery.value.trim()) {
    const query = searchQuery.value.toLowerCase()
    products = products.filter(p => 
      p.name.toLowerCase().includes(query) || 
      p.description?.toLowerCase().includes(query)
    )
  }
  
  return products
})

const groupedCartItems = computed(() => {
  const grouped = {}
  cartItems.value.forEach(item => {
    if (grouped[item.id]) {
      grouped[item.id].quantity++
    } else {
      grouped[item.id] = { ...item, quantity: 1 }
    }
  })
  return Object.values(grouped)
})

const cartTotal = computed(() => {
  return cartItems.value.reduce((sum, item) => sum + parseFloat(item.price), 0)
})

// Métodos
const formatPrice = (price) => {
  return parseFloat(price).toLocaleString('es-CO', { minimumFractionDigits: 0, maximumFractionDigits: 0 })
}

const handleImageError = (productId) => {
  imageErrors.value[productId] = true
}

const getProductQuantity = (productId) => {
  return cartItems.value.filter(item => item.id === productId).length
}

const addToCart = (product) => {
  if (product.stock === 0) return
  cartItems.value.push({ ...product })
}

const addToCartWithAnimation = (product, event) => {
  if (product.stock === 0) return
  
  // Debug: Ver qué variantes tiene el producto
  console.log('🛒 Producto clickeado:', product.name)
  console.log('📦 Variantes disponibles:', product.variants)
  console.log('📊 Cantidad de variantes:', product.variants ? product.variants.length : 0)
  console.log('⚖️ Unidad de medida (unit):', product.unit)
  console.log('⚖️ Measurement unit:', product.measurement_unit)
  console.log('⚖️ Allow decimal:', product.allow_decimal)
  
  // 🚨 DETECCIÓN IGUAL QUE EL POS
  // Si el producto usa measurement_unit diferente de 'unit', requiere modal de cantidad
  const requiresQuantityInput = product.measurement_unit && product.measurement_unit !== 'unit'
  
  console.log('⚖️ Requiere input de cantidad:', requiresQuantityInput)
  
  if (requiresQuantityInput) {
    console.log('✅ Abriendo modal de cantidad (peso/medida)')
    selectedProductForQuantity.value = product
    showQuantityModal.value = true
    return
  }
  
  // 2️⃣ Verificar si el producto tiene variantes (Color, Talla, etc.)
  const hasVariants = product.variants && product.variants.length > 0
  
  if (hasVariants) {
    console.log('✅ Abriendo modal de variantes')
    // Abrir modal de selección de variante
    openVariantModal(product)
    return
  }
  
  console.log('⚠️ Producto sin variantes y sin medida especial, agregando directamente')
  
  // 3️⃣ Si NO tiene variantes ni requiere cantidad, proceder con animación
  
  // Obtener el elemento del producto (funciona para desktop Y mobile)
  let productElement = null
  
  // Si event tiene target (click desde template), buscar el elemento
  if (event && event.target) {
    productElement = event.target.closest('.product-card')
  }
  
  // Si no encontramos el elemento por click, buscar por ref (desktop)
  if (!productElement) {
    productElement = productRefs.value[product.id]
  }
  
  if (productElement) {
    const rect = productElement.getBoundingClientRect()
    const cartIcon = document.querySelector('.cart-badge-icon')
    const cartRect = cartIcon ? cartIcon.getBoundingClientRect() : { left: window.innerWidth - 50, top: 20, width: 40, height: 40 }
    
    // Crear item volador hacia el icono del carrito
    const flyingItem = {
      id: product.id,
      image_url: product.images && product.images.length > 0 ? product.images[0] : product.image_url,
      startX: rect.left + rect.width / 2 - 32,
      startY: rect.top + rect.height / 2 - 32,
      endX: cartRect.left + cartRect.width / 2 - 32,
      endY: cartRect.top + cartRect.height / 2 - 32,
      timestamp: Date.now()
    }
    
    flyingItems.value.push(flyingItem)
    
    // Agregar al carrito después de un delay MÁS CORTO (optimizado)
    setTimeout(() => {
      cartItems.value.push({ ...product })
      
      // Remover el item volador después de la animación
      setTimeout(() => {
        flyingItems.value = flyingItems.value.filter(item => item.timestamp !== flyingItem.timestamp)
      }, 100)
    }, 200)
  } else {
    // Fallback si no hay elemento
    addToCart(product)
  }
}

const openVariantModal = (product) => {
  selectedProduct.value = product
  showVariantModal.value = true
}

// 👗 Manejar confirmación del modal de variantes (Fashion)
const handleVariantConfirmed = ({ variant, selectedOptions }) => {
  if (!variant || !selectedProduct.value) return
  
  // Validar stock de la variante
  if (variant.stock <= 0) {
    console.warn('No hay stock disponible para esta variante')
    return
  }
  
  // Crear resumen de opciones para mostrar (ej: "Talla: M / Color: Rojo")
  const optionsSummary = Object.entries(selectedOptions)
    .map(([key, value]) => `${key}: ${value}`)
    .join(' / ')
  
  // Crear producto con variante para el carrito
  const productWithVariant = {
    ...selectedProduct.value,
    id: `${selectedProduct.value.id}-${variant.id}`, // ID compuesto único
    variant_id: variant.id,
    name: `${selectedProduct.value.name} (${optionsSummary})`,
    price: variant.price,
    stock: variant.stock,
    image_url: selectedProduct.value.image_url || selectedProduct.value.image,
    variant_options: optionsSummary
  }
  
  // Agregar al carrito
  cartItems.value.push(productWithVariant)
  
  // Resetear
  selectedProduct.value = null
  showVariantModal.value = false
}

// ⚖️ Manejar confirmación del modal de cantidad (peso/medida)
const handleQuantityConfirmed = ({ product, quantity }) => {
  console.log(`⚖️ Agregando ${quantity} ${product.unit} de ${product.name}`)
  
  // Crear producto con cantidad específica para el carrito
  const productWithQuantity = {
    ...product,
    id: `${product.id}-${Date.now()}`, // ID único con timestamp
    quantity_value: quantity,
    name: `${product.name} (${quantity} ${product.unit})`,
    price: product.price * quantity, // Precio total según cantidad
    original_price: product.price, // Guardar precio unitario
    display_quantity: quantity
  }
  
  // Agregar al carrito
  cartItems.value.push(productWithQuantity)
  
  // Resetear
  selectedProductForQuantity.value = null
  showQuantityModal.value = false
}

const openCartWithExplosion = () => {
  // Limpiar items visibles previos
  visibleCartItems.value = []
  
  // Abrir el panel
  showCheckout.value = true
  
  // Agregar items uno por uno con delay
  const items = groupedCartItems.value
  items.forEach((item, index) => {
    setTimeout(() => {
      visibleCartItems.value.push(item)
    }, index * 150) // 150ms entre cada producto
  })
  
  // Si hay productos, crear efecto de "explosión" desde el icono del carrito (opcional, puedes desactivar si prefieres)
  if (cartItems.value.length > 0 && items.length <= 3) {
    // Solo explosión si hay pocos productos (3 o menos)
    const uniqueProducts = items.slice(0, 3)
    
    uniqueProducts.forEach((product, index) => {
      setTimeout(() => {
        const cartIcon = document.querySelector('.cart-icon-button')
        if (cartIcon) {
          const rect = cartIcon.getBoundingClientRect()
          
          const explosionItem = {
            id: product.id + '-explosion',
            image_url: product.image_url,
            startX: rect.left + rect.width / 2 - 32,
            startY: rect.top + rect.height / 2 - 32,
            timestamp: Date.now() + index,
            isExplosion: true
          }
          
          flyingItems.value.push(explosionItem)
          
          // Remover después de la animación
          setTimeout(() => {
            flyingItems.value = flyingItems.value.filter(item => item.timestamp !== explosionItem.timestamp)
          }, 800)
        }
      }, index * 80)
    })
  }
}

const getExplosionStyle = (timestamp) => {
  // Generar valores aleatorios para cada explosión basados en el timestamp
  const seed = timestamp % 1000
  const angle = (seed / 1000) * 360 // Ángulo aleatorio
  const distance = 150 + (seed % 100) // Distancia aleatoria
  
  const tx = Math.cos(angle * Math.PI / 180) * distance
  const ty = Math.sin(angle * Math.PI / 180) * distance
  const rotation = -45 + (seed % 90)
  
  return {
    '--tx': `${tx}px`,
    '--ty': `${ty}px`,
    '--rotation': `${rotation}deg`,
    animation: 'explodeFromCart 0.8s cubic-bezier(0.4, 0, 0.2, 1) forwards'
  }
}

const removeFromCart = (productId) => {
  // Eliminar TODAS las instancias del producto
  cartItems.value = cartItems.value.filter(item => item.id !== productId)
}

const increaseQuantity = (productId, product) => {
  const currentQty = getProductQuantity(productId)
  if (currentQty < product.stock) {
    cartItems.value.push({ ...product })
  }
}

const decreaseQuantity = (productId) => {
  const index = cartItems.value.findIndex(item => item.id === productId)
  if (index > -1) {
    cartItems.value.splice(index, 1)
  }
}

// Buscar cliente por cédula (autocomplete inteligente)
const searchCustomerByDocument = async () => {
  // Validar que haya cédula y tenga al menos 6 caracteres
  if (!formData.value.customer_document || formData.value.customer_document.length < 6) {
    return
  }
  
  try {
    searchingCustomer.value = true
    
    const response = await axios.post('/api/public/customers/find-by-document', {
      document: formData.value.customer_document
    })
    
    if (response.data.success && response.data.found) {
      // Auto-llenar campos con los datos encontrados
      formData.value.customer_name = response.data.customer.name
      formData.value.customer_phone = response.data.customer.phone
      formData.value.customer_email = response.data.customer.email || ''
      formData.value.customer_address = response.data.customer.address || ''
      
      // Feedback visual sutil (opcional: puedes agregar un toast o animación)
      console.log('✅ Cliente encontrado:', response.data.customer.name)
    } else {
      // Cliente no encontrado - el usuario puede llenar manualmente
      console.log('ℹ️ Cliente no encontrado, permitir llenado manual')
    }
  } catch (error) {
    // Error de red o servidor - permitir llenado manual sin mostrar error al usuario
    console.error('⚠️ Error buscando cliente:', error)
    // Fallar silenciosamente - el usuario puede seguir llenando el formulario manualmente
  } finally {
    searchingCustomer.value = false
  }
}

const handleCheckoutSubmit = async () => {
  try {
    submittingOrder.value = true
    
    // Preparar items del pedido
    const items = groupedCartItems.value.map(item => ({
      product_id: item.id,
      quantity: item.quantity,
      special_instructions: item.special_instructions || null
    }))
    
    // Enviar al backend
    const response = await axios.post('/api/public/orders', {
      ...formData.value,
      items
    })
    
    if (response.data.success) {
      const order = response.data.order
      
      // Guardar datos ANTES de resetear para usar en el mensaje de WhatsApp
      const customerData = { ...formData.value }
      const orderItems = [...groupedCartItems.value]
      
      // Cerrar modales
      showCheckoutForm.value = false
      showCheckout.value = false
      
      // Vaciar carrito
      cartItems.value = []
      visibleCartItems.value = []
      
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
      
      // Crear mensaje simple para WhatsApp usando el mensaje personalizado de configuración
      const greeting = props.storeConfig.custom_message || 'Hola, quiero hacer el siguiente pedido:'
      let message = `${greeting}\n\n`
      message += `📋 *Código: ${order.order_number}*\n\n`
      message += `👤 ${customerData.customer_name}\n`
      message += `📱 ${customerData.customer_phone}\n\n`
      
      // Tipo de entrega
      if (customerData.delivery_type === 'delivery') {
        message += `🚚 Envío a: ${customerData.customer_address}\n\n`
      } else {
        message += `🏪 Recoger en tienda\n\n`
      }
      
      // Lista simple de productos
      message += `📦 *Productos:*\n`
      orderItems.forEach((item, index) => {
        message += `${index + 1}. ${item.name} x${item.quantity}\n`
      })
      
      // Calcular total correcto con delivery_cost
      const deliveryCost = customerData.delivery_type === 'delivery' ? parseFloat(props.storeConfig.delivery_cost || 0) : 0
      const finalTotal = parseFloat(order.total) + deliveryCost
      
      message += `\n💰 Total: ${props.storeConfig.currency_symbol}${formatPrice(finalTotal)}`
      
      // Notas si existen
      if (customerData.note) {
        message += `\n\n📝 ${customerData.note}`
      }
      
      const whatsappUrl = `https://wa.me/${props.storeConfig.whatsapp_number}?text=${encodeURIComponent(message)}`
      
      // Abrir WhatsApp directamente (sin alerta)
      window.open(whatsappUrl, '_blank')
    }
  } catch (error) {
    console.error('Error al crear pedido:', error)
    const errorMsg = error.response?.data?.message || error.response?.data?.errors || 'Error al procesar tu pedido. Por favor intenta nuevamente.'
    alert(`❌ Error: ${typeof errorMsg === 'object' ? JSON.stringify(errorMsg) : errorMsg}`)
  } finally {
    submittingOrder.value = false
  }
}

// Lifecycle
onMounted(() => {
  // Inicializar solo imageErrors
  props.storeConfig.catalog_products?.forEach(p => {
    imageErrors.value[p.id] = false
  })
  
  // 🔍 Debug: Ver todos los productos y sus variantes
  console.log('📦 Total productos cargados:', props.storeConfig.catalog_products?.length)
  props.storeConfig.catalog_products?.forEach(p => {
    if (p.variants && p.variants.length > 0) {
      console.log(`✅ Producto "${p.name}" tiene ${p.variants.length} variantes:`, p.variants)
    } else {
      console.log(`⚠️ Producto "${p.name}" NO tiene variantes`)
    }
  })
})

// Watchers
watch(groupedCartItems, (newItems) => {
  // Si el panel está abierto, actualizar las cantidades de los items visibles
  if (showCheckout.value) {
    visibleCartItems.value = visibleCartItems.value.map(visibleItem => {
      const updated = newItems.find(item => item.id === visibleItem.id)
      return updated ? { ...updated } : visibleItem
    }).filter(item => newItems.some(newItem => newItem.id === item.id))
  }
}, { deep: true })
</script>

<style scoped>
.catalog-speed-market {
  --brand-color: v-bind('storeConfig.primary_color');
  --brand-dark: color-mix(in srgb, v-bind('storeConfig.primary_color') 85%, black);
}

.bg-brand {
  background-color: var(--brand-color);
}

.bg-brand-dark {
  background-color: var(--brand-dark);
}

.hover\:bg-brand-dark:hover {
  background-color: var(--brand-dark);
}

.text-brand {
  color: var(--brand-color);
}

.border-brand {
  border-color: var(--brand-color);
}

.focus\:border-brand:focus {
  border-color: var(--brand-color);
}

.focus\:ring-brand\/20:focus {
  --tw-ring-color: color-mix(in srgb, v-bind('storeConfig.primary_color') 20%, transparent);
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
  background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
  background-size: 200% 100%;
  animation: shimmer 1.5s infinite;
}

@keyframes shimmer {
  0% { background-position: 200% 0; }
  100% { background-position: -200% 0; }
}

/* ========================================
   ANIMACIONES DE VUELO DE PRODUCTOS
   ======================================== */
.flying-product-animation {
  animation: flyToCart 0.3s ease-out forwards;
}

/* Animación Desktop - simple y rápida */
@keyframes flyToCart {
  0% {
    transform: scale(1);
    opacity: 1;
  }
  100% {
    transform: scale(0.3) translateY(-30px);
    opacity: 0;
  }
}

/* Animación Móvil - vuela hacia esquina superior derecha (carrito) */
@media (max-width: 768px) {
  .flying-product-animation {
    animation: flyToCartMobile 0.3s ease-out forwards;
  }
  
  @keyframes flyToCartMobile {
    0% {
      transform: scale(1);
      opacity: 1;
    }
    100% {
      transform: scale(0.3) translateY(-30px);
      opacity: 0;
    }
  }
}

/* Animación de EXPLOSIÓN desde el carrito */
.explosion-animation {
  /* Los estilos inline sobrescribirán esto */
}

@keyframes explodeFromCart {
  0% {
    transform: translate(0, 0) scale(0.5) rotate(0deg);
    opacity: 1;
  }
  30% {
    transform: translate(var(--tx), var(--ty)) scale(1.2) rotate(var(--rotation));
    opacity: 0.9;
  }
  60% {
    transform: translate(calc(var(--tx) * 1.5), calc(var(--ty) * 1.5)) scale(0.8) rotate(calc(var(--rotation) * 1.5));
    opacity: 0.7;
  }
  100% {
    transform: translate(calc(var(--tx) * 2), calc(var(--ty) * 2)) scale(0.3) rotate(calc(var(--rotation) * 2));
    opacity: 0;
  }
}

.fly-enter-active {
  transition: all 0.2s ease-out;
}

.fly-leave-active {
  transition: all 0.3s cubic-bezier(0.25, 0.46, 0.45, 0.94);
}

.fly-enter-from {
  opacity: 0;
  transform: scale(0.5);
}

.fly-leave-to {
  opacity: 0;
}

/* ========================================
   ANIMACIÓN DEL BADGE DEL CARRITO (Simplificada)
   ======================================== */
.badge-pop-enter-active {
  animation: badgePop 0.3s cubic-bezier(0.68, -0.55, 0.265, 1.55);
}

.badge-pop-leave-active {
  transition: all 0.2s ease-in;
}

.badge-pop-enter-from,
.badge-pop-leave-to {
  opacity: 0;
  transform: scale(0);
}

@keyframes badgePop {
  0% {
    transform: scale(0);
    opacity: 0;
  }
  50% {
    transform: scale(1.3);
  }
  100% {
    transform: scale(1);
    opacity: 1;
  }
}

/* ========================================
   TRANSICIONES DEL PANEL LATERAL
   ======================================== */
.slide-right-enter-active,
.slide-right-leave-active {
  transition: transform 0.4s cubic-bezier(0.4, 0, 0.2, 1);
}

.slide-right-enter-from,
.slide-right-leave-to {
  transform: translateX(100%);
}

/* ========================================
   TRANSICIONES DE ITEMS DEL CARRITO
   ======================================== */
/* Animación de deslizamiento suave desde la derecha uno por uno */
.slide-in-right-enter-active {
  transition: all 0.5s cubic-bezier(0.34, 1.56, 0.64, 1);
}

.slide-in-right-leave-active {
  transition: all 0.4s ease-in;
}

.slide-in-right-enter-from {
  opacity: 0;
  transform: translateX(100px);
}

.slide-in-right-leave-to {
  opacity: 0;
  transform: translateX(-50px);
}

.slide-in-right-move {
  transition: transform 0.5s ease;
}

/* Transición antigua (por si acaso) */
.cart-item-move,
.cart-item-enter-active,
.cart-item-leave-active {
  transition: all 0.4s cubic-bezier(0.55, 0, 0.1, 1);
}

.cart-item-enter-from {
  opacity: 0;
  transform: translateX(30px);
}

.cart-item-leave-to {
  opacity: 0;
  transform: translateX(-30px);
}

.cart-item-leave-active {
  position: absolute;
  width: 100%;
}

/* Transitions - Simplificadas para mejor rendimiento */
/* Desktop */
@media (min-width: 768px) {
  .list-move,
  .list-enter-active,
  .list-leave-active {
    transition: all 0.25s cubic-bezier(0.55, 0, 0.1, 1);
  }

  .list-enter-from {
    opacity: 0;
    transform: translateX(-30px);
  }

  .list-leave-to {
    opacity: 0;
    transform: translateX(30px);
  }

  .list-leave-active {
    position: absolute;
    width: 100%;
  }
}

/* Móvil - Animaciones optimizadas */
@media (max-width: 767px) {
  .list-enter-active {
    animation: slideInMobile 0.25s cubic-bezier(0.34, 1.56, 0.64, 1);
  }

  .list-leave-active {
    animation: slideOutMobile 0.3s cubic-bezier(0.55, 0, 0.1, 1);
    position: absolute;
    width: 100%;
  }

  @keyframes slideInMobile {
    0% {
      opacity: 0;
      transform: translateX(100px) scale(0.9);
    }
    60% {
      transform: translateX(-10px) scale(1.02);
    }
    100% {
      opacity: 1;
      transform: translateX(0) scale(1);
    }
  }

  @keyframes slideOutMobile {
    0% {
      opacity: 1;
      transform: translateX(0) scale(1);
    }
    100% {
      opacity: 0;
      transform: translateX(-100px) scale(0.8);
    }
  }
}

.fade-enter-active, .fade-leave-active {
  transition: opacity 0.3s ease;
}

.fade-enter-from, .fade-leave-to {
  opacity: 0;
}

.slide-up-enter-active, .slide-up-leave-active {
  transition: transform 0.4s cubic-bezier(0.4, 0, 0.2, 1);
}

.slide-up-enter-from, .slide-up-leave-to {
  transform: translateY(100%);
}

/* ========================================
   OPTIMIZACIONES MÓVILES
   ======================================== */
.touch-manipulation {
  touch-action: manipulation;
  -webkit-tap-highlight-color: transparent;
}

/* Feedback visual brutal al tocar cards en móvil */
@media (max-width: 768px) {
  .cursor-pointer:active {
    transform: scale(0.98) !important;
  }
}

/* Mejora de scroll en móvil */
.overflow-y-auto {
  -webkit-overflow-scrolling: touch;
}

/* Ocultar scrollbar horizontal en navegación */
.scrollbar-hide::-webkit-scrollbar {
  display: none;
}

.scrollbar-hide {
  -ms-overflow-style: none;
  scrollbar-width: none;
}

/* Transición rápida en móvil */
@media (max-width: 768px) {
  .slide-right-enter-active {
    animation: slideInPanel 0.25s cubic-bezier(0.34, 1.56, 0.64, 1);
  }
  
  .slide-right-leave-active {
    animation: slideOutPanel 0.2s cubic-bezier(0.55, 0, 0.1, 1);
  }
  
  @keyframes slideInPanel {
    0% {
      transform: translateX(100%);
      opacity: 0.8;
    }
    100% {
      transform: translateX(0);
      opacity: 1;
    }
  }
  
  @keyframes slideOutPanel {
    0% {
      transform: translateX(0);
      opacity: 1;
    }
    100% {
      transform: translateX(100%);
      opacity: 0.8;
    }
  }
}

/* Transición rápida en desktop */
@media (min-width: 769px) {
  .slide-right-enter-active,
  .slide-right-leave-active {
    transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  }
  
  .slide-right-enter-from,
  .slide-right-leave-to {
    transform: translateX(100%);
  }
}

</style>
