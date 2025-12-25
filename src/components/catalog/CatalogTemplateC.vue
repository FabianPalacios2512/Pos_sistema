<template>
  <!-- PLANTILLA C: "MODERN GRID" - Estilo Original Lime/Green -->
  <div class="catalog-modern-grid min-h-screen bg-white font-sans selection:bg-brand selection:text-white overflow-x-hidden">
    
    <!-- Background Blobs -->
    <div class="fixed inset-0 z-0 pointer-events-none overflow-hidden opacity-30">
      <div class="absolute top-[-10%] left-[-10%] w-[60%] h-[60%] bg-brand/15 rounded-full blur-[120px] animate-blob"></div>
      <div class="absolute bottom-[-10%] right-[-10%] w-[60%] h-[60%] bg-blue-50 rounded-full blur-[120px] animate-blob animation-delay-2000"></div>
      <div class="absolute top-[40%] left-[40%] w-[40%] h-[40%] bg-purple-50 rounded-full blur-[120px] animate-blob animation-delay-4000"></div>
    </div>

    <!-- Floating Glass Header -->
    <header 
      class="fixed top-0 inset-x-0 z-50 transition-all duration-300"
      :class="isScrolled ? 'bg-white/95 backdrop-blur-xl shadow-md py-2.5' : 'bg-white/80 backdrop-blur-xl py-3 shadow-sm'"
    >
      <div class="max-w-7xl mx-auto px-4 md:px-6 lg:px-8">
        <div class="flex items-center justify-between gap-4 md:gap-6">
          
          <!-- Logo -->
          <div class="flex items-center gap-2.5 cursor-pointer group" @click="clearFilters">
            <div v-if="storeConfig.logo_url" class="w-9 h-9 md:w-10 md:h-10 rounded-lg overflow-hidden shadow-md group-hover:scale-105 transition-transform duration-300">
              <img :src="storeConfig.logo_url" class="w-full h-full object-cover" />
            </div>
            <div v-else class="w-9 h-9 md:w-10 md:h-10 bg-gradient-to-br from-brand to-brand-dark rounded-lg flex items-center justify-center shadow-md group-hover:rotate-6 transition-transform duration-300">
              <svg class="w-5 h-5 md:w-6 md:h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
              </svg>
            </div>
            <div class="hidden sm:block">
              <h1 class="text-base md:text-lg font-black text-slate-800 tracking-tight leading-tight">{{ storeConfig.store_name }}</h1>
            </div>
          </div>

          <!-- Search (Desktop & Mobile) -->
          <div class="flex-1 max-w-xs md:max-w-xl mx-2 md:mx-6">
            <div class="relative group">
              <div class="absolute inset-y-0 left-0 pl-3 md:pl-3.5 flex items-center pointer-events-none">
                <svg class="w-4 h-4 md:w-5 md:h-5 text-slate-400 group-focus-within:text-brand transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
              </div>
              <input 
                v-model="searchQuery"
                type="text"
                placeholder="Buscar..."
                class="w-full pl-9 md:pl-11 pr-3 md:pr-4 py-2 md:py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-700 text-sm placeholder-slate-400 focus:bg-white focus:border-brand focus:ring-2 focus:ring-brand/20 transition-all"
              />
            </div>
          </div>

          <!-- Cart Button -->
          <button 
            @click="showCheckout = true"
            class="hidden md:flex items-center gap-2.5 px-5 py-2.5 bg-slate-900 hover:bg-slate-800 text-white rounded-xl transition-all shadow-md hover:shadow-lg group"
          >
            <div class="relative">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
              </svg>
              <span v-if="cartItems.length > 0" class="absolute -top-2 -right-2 min-w-[18px] h-[18px] bg-brand text-slate-900 text-[10px] font-bold rounded-full flex items-center justify-center px-1">
                {{ cartItems.length }}
              </span>
            </div>
            <span class="font-bold text-sm">{{ storeConfig.currency_symbol }}{{ formatPrice(cartTotal) }}</span>
          </button>
        </div>
      </div>
    </header>

    <!-- Hero Section - Mejorado con mejor contraste y sombras -->
    <div class="relative pt-24 pb-8 md:pt-32 md:pb-12 px-4 bg-gradient-to-b from-white via-gray-50/30 to-white">
      <div class="max-w-7xl mx-auto text-center relative z-10">
        <h2 class="text-4xl md:text-5xl lg:text-6xl font-black text-slate-900 mb-4 tracking-tight leading-none drop-shadow-sm">
          Calidad que <span class="text-transparent bg-clip-text bg-gradient-to-r from-emerald-600 via-green-500 to-lime-500 drop-shadow-lg">Inspira</span>
        </h2>
        <p class="text-lg md:text-xl text-slate-600 max-w-2xl mx-auto leading-relaxed font-medium">
          Descubre productos seleccionados con el mejor diseño y calidad garantizada.
        </p>
      </div>
    </div>

    <!-- Categories Pills -->
    <div class="sticky top-[52px] md:top-[58px] z-40 mb-6 md:mb-8 bg-white/80 backdrop-blur-md border-b border-slate-200/50 py-3 md:py-4 shadow-sm">
      <div class="max-w-7xl mx-auto px-4 md:px-6 lg:px-8">
        <div class="flex justify-start md:justify-center overflow-x-auto scrollbar-hide">
          <div class="flex space-x-2 md:space-x-3">
            <button 
              @click="selectedCategory = null"
              class="px-4 md:px-5 py-2 md:py-2.5 rounded-lg text-sm md:text-base font-bold transition-all duration-200 whitespace-nowrap"
              :class="selectedCategory === null ? 'bg-slate-900 text-white shadow-md' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100'"
            >
              Todos
            </button>
            <button 
              v-for="cat in categories"
              :key="cat.id"
              @click="selectedCategory = cat.id"
              class="px-4 md:px-5 py-2 md:py-2.5 rounded-lg text-sm md:text-base font-bold transition-all duration-200 whitespace-nowrap"
              :class="selectedCategory === cat.id ? 'bg-slate-900 text-white shadow-md' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100'"
            >
              {{ cat.name }}
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Products Grid -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-32 md:pb-12 relative z-10">
      <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3 sm:gap-4">
        <TransitionGroup name="list">
          <div 
            v-for="product in filteredProducts"
            :key="product.id"
            class="bg-white rounded-2xl p-3 md:p-4 border border-slate-100 hover:shadow-xl transition-all duration-300 hover:-translate-y-1 group"
          >
            <!-- Product Image -->
            <div class="relative aspect-square rounded-xl overflow-hidden mb-3 bg-gradient-to-br from-gray-100 to-gray-200">
              
              <!-- Imagen del Producto -->
              <img 
                v-if="hasValidImage(product)"
                :src="getProductImage(product)"
                :alt="product.name"
                @error="handleImageError(product.id)"
                class="w-full h-full object-cover"
              />
              
              <!-- Placeholder cuando no hay imagen -->
              <div v-else class="w-full h-full flex items-center justify-center">
                <div class="text-center">
                  <svg class="w-16 h-16 md:w-20 md:h-20 text-gray-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                  </svg>
                  <p class="text-xs text-gray-400 font-medium">Sin imagen</p>
                </div>
              </div>
              <!-- Stock Badge -->
              <div v-if="product.stock === 0" class="absolute inset-0 bg-black/60 flex items-center justify-center">
                <span class="text-white text-xs font-bold bg-red-500 px-3 py-1 rounded-full">Agotado</span>
              </div>
              <div v-else-if="product.stock < 5" class="absolute top-2 right-2 bg-amber-500 text-white text-xs font-bold px-2 py-1 rounded-full">
                Solo {{ product.stock }}
              </div>
            </div>

            <!-- Product Info -->
            <div class="space-y-2">
              <h3 class="font-bold text-slate-900 text-sm md:text-base line-clamp-2 min-h-[40px] md:min-h-[48px]">{{ product.name }}</h3>
              <div class="flex items-center justify-between">
                <p class="text-lg md:text-xl font-black text-brand">
                  {{ storeConfig.currency_symbol }}{{ formatPrice(product.price) }}
                </p>
              </div>
              
              <!-- Add to Cart -->
              <button
                v-if="getProductQuantity(product.id) === 0"
                @click="addToCart(product)"
                :disabled="product.stock === 0"
                class="w-full py-2 md:py-2.5 bg-slate-900 hover:bg-slate-800 disabled:bg-gray-300 text-white font-bold rounded-lg transition-colors text-sm md:text-base disabled:cursor-not-allowed"
              >
                Agregar
              </button>
              <div v-else class="flex items-center gap-2">
                <button @click="decreaseQuantity(product.id)" class="flex-1 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold rounded-lg transition-colors">
                  -
                </button>
                <span class="px-3 font-bold text-slate-900">{{ getProductQuantity(product.id) }}</span>
                <button @click="increaseQuantity(product.id, product)" class="flex-1 py-2 bg-slate-900 hover:bg-slate-800 text-white font-bold rounded-lg transition-colors">
                  +
                </button>
              </div>
            </div>
          </div>
        </TransitionGroup>
      </div>

      <!-- Empty State -->
      <div v-if="filteredProducts.length === 0" class="text-center py-20">
        <div class="w-20 h-20 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-4">
          <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
          </svg>
        </div>
        <h3 class="text-xl font-bold text-slate-900 mb-2">Sin resultados</h3>
        <button @click="clearFilters" class="px-6 py-2 bg-slate-900 text-white font-bold rounded-lg hover:bg-slate-800 transition-colors">
          Ver Todo
        </button>
      </div>
    </main>

    <!-- Footer -->
    <footer class="border-t border-slate-200 bg-white pt-12 pb-24 md:pb-8 relative z-10">
      <div class="max-w-7xl mx-auto px-4 text-center">
        <p class="text-slate-400 text-sm">© 2025 {{ storeConfig.store_name }}</p>
      </div>
    </footer>

    <!-- Floating Cart FAB (Mobile) -->
    <Transition name="scale">
      <button 
        v-if="cartItems.length > 0"
        @click="showCheckout = true"
        class="md:hidden fixed bottom-6 right-6 bg-slate-900 text-white pl-6 pr-5 py-4 rounded-full shadow-2xl flex items-center gap-3 hover:scale-105 transition-all z-50"
      >
        <div class="relative">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
          </svg>
          <span class="absolute -top-2 -right-2 bg-brand text-slate-900 text-xs font-bold w-5 h-5 rounded-full flex items-center justify-center">
            {{ cartItems.length }}
          </span>
        </div>
        <span class="font-black">{{ storeConfig.currency_symbol }}{{ formatPrice(cartTotal) }}</span>
      </button>
    </Transition>

    <!-- Checkout Drawer (Desde la derecha como Template B) -->
    <Teleport to="body">
      <!-- Overlay -->
      <Transition name="fade">
        <div v-if="showCheckout" class="fixed inset-0 bg-black/40 backdrop-blur-sm z-[90]" @click="showCheckout = false"></div>
      </Transition>
      
      <!-- Panel Deslizante desde la Derecha -->
      <Transition name="slide-right">
        <div v-if="showCheckout" class="fixed inset-y-0 right-0 w-full md:w-[420px] lg:w-[450px] bg-gradient-to-b from-gray-50 to-white dark:from-zinc-900 dark:to-zinc-950 shadow-2xl z-[100] flex flex-col">
          <!-- Header del Panel -->
          <div class="p-6 border-b border-gray-200/80 dark:border-zinc-800/80 backdrop-blur-xl bg-white/80 dark:bg-zinc-900/80">
            <div class="flex items-center justify-between">
              <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-2xl flex items-center justify-center shadow-lg bg-gradient-to-br from-slate-900 to-slate-700">
                  <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                  </svg>
                </div>
                <div>
                  <h3 class="text-2xl font-black text-gray-900 dark:text-white">{{ showCheckoutForm ? 'Completar Pedido' : 'Tu Pedido' }}</h3>
                  <p class="text-sm font-medium text-gray-500 dark:text-zinc-400 mt-0.5">
                    <span class="inline-flex items-center gap-1.5">
                      <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
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

          <!-- Contenido Scrollable -->
          <div class="flex-1 overflow-y-auto p-6 space-y-3">
            
            <!-- Vista del Carrito -->
            <template v-if="!showCheckoutForm">
              <!-- Cart Items -->
              <div v-for="item in groupedCartItems" :key="item.id" class="bg-white dark:bg-zinc-900 rounded-2xl p-4 border border-gray-200 dark:border-zinc-800 hover:border-gray-300 dark:hover:border-zinc-700 hover:shadow-lg dark:shadow-black/30 transition-all duration-200">
              <div class="flex gap-4">
                <!-- Imagen -->
                <div class="relative flex-shrink-0">
                  <div class="w-20 h-20 rounded-xl overflow-hidden shadow-md border border-gray-100 dark:border-zinc-800">
                    <img 
                      v-if="item.image_url && item.image_url !== 'https://via.placeholder.com/400'"
                      :src="item.image_url" 
                      class="w-full h-full object-cover"
                      @error="(e) => e.target.style.display = 'none'"
                    />
                    <div 
                      v-if="!item.image_url || item.image_url === 'https://via.placeholder.com/400'"
                      class="w-full h-full bg-gradient-to-br from-gray-50 to-gray-100 dark:from-zinc-800 dark:to-zinc-900 flex items-center justify-center"
                    >
                      <svg class="w-9 h-9 text-gray-300 dark:text-zinc-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                      </svg>
                    </div>
                  </div>
                  <div class="absolute -top-2 -right-2 w-7 h-7 rounded-full text-white text-xs font-black flex items-center justify-center shadow-lg border-2 border-white dark:border-zinc-900 bg-gradient-to-br from-slate-900 to-slate-700">
                    {{ item.quantity }}
                  </div>
                </div>

                <!-- Info -->
                <div class="flex-1 min-w-0">
                  <h4 class="font-bold text-gray-900 dark:text-white text-base line-clamp-2 mb-1.5 leading-tight">{{ item.name }}</h4>
                  <p class="text-sm text-gray-500 dark:text-zinc-400 font-medium mb-3">
                    {{ storeConfig.currency_symbol }}{{ formatPrice(item.price) }} <span class="text-xs">c/u</span>
                  </p>
                  
                  <!-- Controles -->
                  <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2 bg-gray-50 dark:bg-zinc-800 rounded-xl px-2 py-2 border border-gray-200 dark:border-zinc-700">
                      <button 
                        @click="decreaseQuantity(item.id)" 
                        class="w-8 h-8 rounded-lg hover:bg-gray-200 dark:hover:bg-zinc-700 active:bg-gray-300 dark:active:bg-zinc-600 flex items-center justify-center text-gray-600 dark:text-zinc-300 active:scale-90 transition-all"
                      >
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                          <path fill-rule="evenodd" d="M3 10a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd" />
                        </svg>
                      </button>
                      <span class="text-base font-black text-gray-900 dark:text-white w-8 text-center">{{ item.quantity }}</span>
                      <button 
                        @click="increaseQuantity(item.id, item)" 
                        class="w-8 h-8 rounded-lg flex items-center justify-center text-white active:scale-90 transition-all shadow-md bg-gradient-to-br from-slate-900 to-slate-700"
                      >
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                          <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                        </svg>
                      </button>
                    </div>
                    <p class="text-lg font-black text-gray-900 dark:text-white">
                      {{ storeConfig.currency_symbol }}{{ formatPrice(item.price * item.quantity) }}
                    </p>
                  </div>
                </div>

                <!-- Eliminar -->
                <button 
                  @click="removeFromCart(item.id)"
                  class="flex-shrink-0 w-10 h-10 rounded-xl hover:bg-red-50 dark:hover:bg-red-950/30 text-gray-400 dark:text-zinc-500 hover:text-red-600 dark:hover:text-red-400 flex items-center justify-center transition-all active:scale-90 border border-transparent hover:border-red-100 dark:hover:border-red-900/30"
                >
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                  </svg>
                </button>
              </div>
            </div>

            <!-- Empty State -->
            <div v-if="cartItems.length === 0" class="text-center py-16">
              <div class="w-24 h-24 mx-auto rounded-3xl bg-gradient-to-br from-gray-100 to-gray-50 dark:from-zinc-800 dark:to-zinc-900 flex items-center justify-center mb-6 shadow-lg border-2 border-gray-200 dark:border-zinc-700">
                <svg class="w-12 h-12 text-gray-400 dark:text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                </svg>
              </div>
              <p class="text-xl font-black text-gray-900 dark:text-white mb-2">Tu carrito está vacío</p>
              <p class="text-sm text-gray-500 dark:text-zinc-400 font-medium">Agrega productos para comenzar tu pedido</p>
            </div>
            </template>

            <!-- Vista del Formulario -->
            <template v-else>
              
              <!-- Botón Volver -->
              <button 
                @click="showCheckoutForm = false"
                class="flex items-center gap-2 text-gray-600 dark:text-zinc-400 hover:text-gray-900 dark:hover:text-white transition-colors duration-200 mb-6 group">
                <svg class="w-5 h-5 transform group-hover:-translate-x-1 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                <span class="font-semibold text-sm">Volver al carrito</span>
              </button>

              <!-- Formulario -->
              <div class="space-y-5">
                
                <!-- Cédula con Autocomplete -->
                <div>
                  <label class="block text-sm font-bold text-gray-700 dark:text-zinc-300 mb-2">
                    Cédula <span class="text-red-500">*</span>
                  </label>
                  <div class="relative">
                    <input 
                      v-model="formData.customer_document"
                      @blur="searchCustomerByDocument"
                      type="text"
                      required
                      placeholder="Ej: 12345678"
                      class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-gray-900 dark:text-zinc-200 placeholder-gray-400 dark:placeholder-zinc-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent transition-all duration-200 font-medium"
                    />
                    <div v-if="searchingCustomer" class="absolute right-3 top-1/2 -translate-y-1/2">
                      <svg class="animate-spin h-5 w-5 text-blue-600 dark:text-blue-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                      </svg>
                    </div>
                  </div>
                  <p class="text-xs text-gray-500 dark:text-zinc-500 mt-1.5 font-medium">
                    Ingresa la cédula para autocompletar los datos
                  </p>
                </div>

                <!-- Nombre -->
                <div>
                  <label class="block text-sm font-bold text-gray-700 dark:text-zinc-300 mb-2">
                    Nombre Completo <span class="text-red-500">*</span>
                  </label>
                  <input 
                    v-model="formData.customer_name"
                    type="text"
                    required
                    placeholder="Nombre y apellido"
                    class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-gray-900 dark:text-zinc-200 placeholder-gray-400 dark:placeholder-zinc-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent transition-all duration-200 font-medium"
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
                    placeholder="+591 ..."
                    class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-gray-900 dark:text-zinc-200 placeholder-gray-400 dark:placeholder-zinc-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent transition-all duration-200 font-medium"
                  />
                </div>

                <!-- Email -->
                <div>
                  <label class="block text-sm font-bold text-gray-700 dark:text-zinc-300 mb-2">
                    Email (opcional)
                  </label>
                  <input 
                    v-model="formData.customer_email"
                    type="email"
                    placeholder="email@ejemplo.com"
                    class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-gray-900 dark:text-zinc-200 placeholder-gray-400 dark:placeholder-zinc-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent transition-all duration-200 font-medium"
                  />
                </div>

                <!-- Tipo de Entrega -->
                <div>
                  <label class="block text-sm font-bold text-gray-700 dark:text-zinc-300 mb-3">
                    Tipo de Entrega <span class="text-red-500">*</span>
                  </label>
                  <div class="grid grid-cols-2 gap-3">
                    <button 
                      type="button"
                      @click="formData.delivery_type = 'delivery'"
                      :class="[
                        'px-4 py-3 rounded-xl border-2 font-bold transition-all duration-200 flex items-center justify-center gap-2',
                        formData.delivery_type === 'delivery'
                          ? 'bg-gradient-to-br from-slate-900 to-slate-700 dark:from-slate-700 dark:to-slate-600 border-slate-900 dark:border-slate-600 text-white shadow-lg'
                          : 'bg-white dark:bg-zinc-900 border-gray-200 dark:border-zinc-700 text-gray-700 dark:text-zinc-300 hover:border-gray-300 dark:hover:border-zinc-600'
                      ]">
                      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0" />
                      </svg>
                      Delivery
                    </button>
                    <button 
                      type="button"
                      @click="formData.delivery_type = 'pickup'"
                      :class="[
                        'px-4 py-3 rounded-xl border-2 font-bold transition-all duration-200 flex items-center justify-center gap-2',
                        formData.delivery_type === 'pickup'
                          ? 'bg-gradient-to-br from-slate-900 to-slate-700 dark:from-slate-700 dark:to-slate-600 border-slate-900 dark:border-slate-600 text-white shadow-lg'
                          : 'bg-white dark:bg-zinc-900 border-gray-200 dark:border-zinc-700 text-gray-700 dark:text-zinc-300 hover:border-gray-300 dark:hover:border-zinc-600'
                      ]">
                      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                      </svg>
                      Recoger
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
                    :required="formData.delivery_type === 'delivery'"
                    rows="3"
                    placeholder="Calle, número, zona, referencias..."
                    class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-gray-900 dark:text-zinc-200 placeholder-gray-400 dark:placeholder-zinc-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent transition-all duration-200 resize-none font-medium"
                  ></textarea>
                </div>

                <!-- Notas -->
                <div>
                  <label class="block text-sm font-bold text-gray-700 dark:text-zinc-300 mb-2">
                    Notas adicionales (opcional)
                  </label>
                  <textarea 
                    v-model="formData.note"
                    rows="3"
                    placeholder="Observaciones sobre el pedido..."
                    class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-gray-900 dark:text-zinc-200 placeholder-gray-400 dark:placeholder-zinc-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent transition-all duration-200 resize-none font-medium"
                  ></textarea>
                </div>

              </div>
            </template>
          </div>

          <!-- Footer -->
          <div class="border-t border-gray-200 dark:border-zinc-800 p-6 bg-gradient-to-t from-white to-gray-50 dark:from-zinc-900 dark:to-zinc-900/50 backdrop-blur-xl space-y-4">
            
            <!-- Vista Carrito: Totales + Botón Completar -->
            <template v-if="!showCheckoutForm">
            <!-- Totales -->
            <div class="space-y-3">
              <div class="flex justify-between items-center text-sm">
                <span class="text-gray-600 dark:text-zinc-400 font-semibold">Subtotal</span>
                <span class="font-black text-gray-900 dark:text-white text-base">{{ storeConfig.currency_symbol }}{{ formatPrice(cartTotal) }}</span>
              </div>
              
              <div class="relative py-2">
                <div class="absolute inset-0 flex items-center">
                  <div class="w-full border-t-2 border-dashed border-gray-300 dark:border-zinc-700"></div>
                </div>
              </div>
              
              <div class="flex justify-between items-center p-4 rounded-2xl bg-gradient-to-br from-gray-100 to-gray-50 dark:from-zinc-800 dark:to-zinc-900 border-2 border-gray-200 dark:border-zinc-700">
                <span class="text-xl font-black text-gray-900 dark:text-white">Total</span>
                <div class="text-right">
                  <p class="text-3xl font-black text-slate-900 dark:text-white">
                    {{ storeConfig.currency_symbol }}{{ formatPrice(cartTotal) }}
                  </p>
                  <p class="text-xs text-gray-500 dark:text-zinc-400 font-medium mt-0.5">
                    {{ cartItems.length }} producto{{ cartItems.length !== 1 ? 's' : '' }}
                  </p>
                </div>
              </div>
              <p class="text-xs text-gray-500 dark:text-zinc-400 text-center font-medium">El costo de envío se calculará en el siguiente paso</p>
            </div>              <!-- Alerta -->
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

              <!-- Botón Completar Pedido -->
              <button 
                @click="showCheckoutForm = true"
                :disabled="cartTotal < storeConfig.min_order_value || cartItems.length === 0"
                class="w-full bg-gradient-to-r from-[#25D366] to-[#1ebe57] hover:from-[#1ebe57] hover:to-[#128C7E] disabled:from-gray-300 disabled:to-gray-400 text-white py-4 rounded-2xl font-black text-base flex items-center justify-center gap-3 shadow-2xl hover:shadow-3xl transition-all disabled:cursor-not-allowed active:scale-[0.98]"
              >
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>Completar Pedido</span>
              </button>
            </template>

            <!-- Vista Formulario: Total + Botón Enviar -->
            <template v-else>
              <!-- Total -->
              <div class="flex justify-between items-center p-4 rounded-2xl bg-gradient-to-br from-gray-100 to-gray-50 dark:from-zinc-800 dark:to-zinc-900 border-2 border-gray-200 dark:border-zinc-700">
                <span class="text-xl font-black text-gray-900 dark:text-white">Total</span>
                <div class="text-right">
                  <p class="text-3xl font-black text-slate-900 dark:text-white">
                    {{ storeConfig.currency_symbol }}{{ formatPrice(cartTotal + (formData.delivery_type === 'delivery' ? storeConfig.delivery_cost : 0)) }}
                  </p>
                  <p class="text-xs text-gray-500 dark:text-zinc-400 font-medium mt-0.5">
                    {{ cartItems.length }} producto{{ cartItems.length !== 1 ? 's' : '' }}
                    {{ formData.delivery_type === 'delivery' ? '+ envío' : '' }}
                  </p>
                </div>
              </div>

              <!-- Botón Enviar Pedido -->
              <button 
                @click="handleCheckoutSubmit"
                :disabled="submittingOrder"
                class="w-full bg-gradient-to-r from-[#25D366] to-[#1ebe57] hover:from-[#1ebe57] hover:to-[#128C7E] disabled:from-gray-300 disabled:to-gray-400 text-white py-4 rounded-2xl font-black text-base flex items-center justify-center gap-3 shadow-2xl hover:shadow-3xl transition-all disabled:cursor-not-allowed active:scale-[0.98]"
              >
                <svg v-if="!submittingOrder" class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                  <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/>
                </svg>
                <svg v-else class="animate-spin h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span>{{ submittingOrder ? 'Enviando...' : 'Enviar Pedido por WhatsApp' }}</span>
              </button>
            </template>
          </div>
        </div>
      </Transition>
    </Teleport>

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
import { ref, computed, onMounted, onUnmounted } from 'vue'
import axios from 'axios'
import QuantityModal from './QuantityModal.vue'

const props = defineProps({
  storeConfig: {
    type: Object,
    required: true
  },
  categories: {
    type: Array,
    default: () => []
  }
})

// Estado
const isScrolled = ref(false)
const searchQuery = ref('')
const selectedCategory = ref(null)
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
const loadingImages = ref({})
const imageErrors = ref({})
const showQuantityModal = ref(false)
const selectedProductForQuantity = ref(null)

// Computed
const filteredProducts = computed(() => {
  let products = props.storeConfig.catalog_products || []
  
  if (selectedCategory.value !== null) {
    products = products.filter(p => p.category_id === selectedCategory.value)
  }
  
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

// Verificar si el producto tiene una imagen válida
const hasValidImage = (product) => {
  // Si hay error previo, no mostrar imagen
  if (imageErrors.value[product.id]) return false
  
  // Verificar si tiene imágenes en el array
  if (product.images && product.images.length > 0) return true
  
  // Verificar image_url (incluyendo base64)
  if (product.image_url) {
    // Excluir placeholders
    if (product.image_url === 'https://via.placeholder.com/400') return false
    // Aceptar base64, http, https, o rutas relativas
    return true
  }
  
  return false
}

// Obtener la URL/base64 de la imagen del producto
const getProductImage = (product) => {
  if (product.images && product.images.length > 0) {
    return product.images[0]
  }
  return product.image_url || ''
}

const getProductQuantity = (productId) => {
  return cartItems.value.filter(item => item.id === productId).length
}

const addToCart = (product) => {
  if (product.stock === 0) return
  
  console.log('🛒 Producto clickeado:', product.name)
  console.log('⚖️ measurement_unit:', product.measurement_unit)
  console.log('📊 unit:', product.unit)
  console.log('📦 Variantes:', product.variants)
  
  // 🚨 DETECCIÓN IGUAL QUE EL POS
  // Si el producto usa measurement_unit diferente de 'unit', requiere modal de cantidad
  const requiresQuantityInput = product.measurement_unit && product.measurement_unit !== 'unit'
  
  console.log('✅ Requiere input de cantidad:', requiresQuantityInput)
  
  if (requiresQuantityInput) {
    // Mostrar modal de cantidad
    console.log('🚨 Abriendo modal de cantidad')
    selectedProductForQuantity.value = product
    showQuantityModal.value = true
    return
  }
  
  // Si no requiere cantidad especial, agregar directamente
  console.log('➡️ Agregando directamente al carrito')
  cartItems.value.push({ ...product })
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

const removeFromCart = (productId) => {
  cartItems.value = cartItems.value.filter(item => item.id !== productId)
}

const clearFilters = () => {
  searchQuery.value = ''
  selectedCategory.value = null
}

const handleQuantityConfirmed = ({ product, quantity }) => {
  console.log(`⚖️ Agregando ${quantity} ${product.measurement_unit || product.unit} de ${product.name}`)
  
  // Crear producto con cantidad específica para el carrito
  const productWithQuantity = {
    ...product,
    id: `${product.id}-${Date.now()}`, // ID único con timestamp
    quantity_value: quantity,
    name: `${product.name} (${quantity} ${product.measurement_unit || product.unit || 'kg'})`,
    price: product.price * quantity, // Precio total según cantidad
    original_price: product.price, // Guardar precio unitario
    display_quantity: quantity
  }
  
  cartItems.value.push(productWithQuantity)
  showQuantityModal.value = false
  selectedProductForQuantity.value = null
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

    const items = groupedCartItems.value.map(item => ({
      product_id: item.id,
      quantity: item.quantity,
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
      const orderItems = [...groupedCartItems.value]
      
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
        message += `${index + 1}. ${item.name} x${item.quantity}\n`
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

// Lifecycle
onMounted(() => {
  window.addEventListener('scroll', handleScroll)
  // Inicializar solo imageErrors
  props.storeConfig.catalog_products?.forEach(p => {
    imageErrors.value[p.id] = false
  })
})

onUnmounted(() => {
  window.removeEventListener('scroll', handleScroll)
})
</script>

<style scoped>
.catalog-modern-grid {
  --brand-color: v-bind('storeConfig.primary_color');
  --brand-dark: color-mix(in srgb, v-bind('storeConfig.primary_color') 85%, black);
}

.bg-brand {
  background-color: var(--brand-color);
}

.bg-brand-dark {
  background-color: var(--brand-dark);
}

.text-brand {
  color: var(--brand-color);
}

.border-brand {
  border-color: var(--brand-color);
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

/* Blob Animations */
@keyframes blob {
  0% { transform: translate(0px, 0px) scale(1); }
  33% { transform: translate(30px, -50px) scale(1.1); }
  66% { transform: translate(-20px, 20px) scale(0.9); }
  100% { transform: translate(0px, 0px) scale(1); }
}

.animate-blob {
  animation: blob 7s infinite;
}

.animation-delay-2000 {
  animation-delay: 2s;
}

.animation-delay-4000 {
  animation-delay: 4s;
}

/* Fade In Up */
@keyframes fadeInUp {
  from { opacity: 0; transform: translateY(20px); }
  to { opacity: 1; transform: translateY(0); }
}

.animate-fade-in-up {
  animation: fadeInUp 0.8s cubic-bezier(0.2, 0.8, 0.2, 1) forwards;
  opacity: 0;
}

.delay-100 { animation-delay: 100ms; }
.delay-200 { animation-delay: 200ms; }

/* Transitions */
.list-move,
.list-enter-active,
.list-leave-active {
  transition: all 0.4s cubic-bezier(0.55, 0, 0.1, 1);
}

.list-enter-from {
  opacity: 0;
  transform: translateX(-30px);
}

.list-leave-to {
  opacity: 0;
  transform: translateX(30px);
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

.slide-right-enter-active, .slide-right-leave-active {
  transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
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
</style>
