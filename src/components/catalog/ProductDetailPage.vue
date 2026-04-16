<template>
  <div class="min-h-screen bg-white relative pdp-immersive" :style="themeVars">
    
    <!-- Loading State -->
    <div v-if="loading" class="min-h-screen flex items-center justify-center">
      <div class="w-8 h-8 border-2 border-gray-200 border-t-gray-900 rounded-full animate-spin"></div>
    </div>

    <!-- Error State -->
    <div v-else-if="error" class="min-h-screen flex flex-col items-center justify-center px-6 text-center">
      <svg class="w-16 h-16 text-gray-200 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1">
        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
      </svg>
      <p class="text-gray-500 text-sm mb-4">No se pudo cargar el producto</p>
      <button @click="$router.push('/catalog')" class="text-sm font-medium text-gray-900 underline underline-offset-4">
        Volver al catálogo
      </button>
    </div>

    <!-- Product Content -->
    <template v-else-if="product">
      
      <!-- STICKY HEADER: Volver + Compartir + Carrito -->
      <header class="sticky top-0 z-50 backdrop-blur-sm" :style="{ backgroundColor: aiPalette.background, boxShadow: '0 1px 0 rgba(0,0,0,0.06)' }">
        <div class="flex items-center px-3 h-12 gap-2">
          <!-- Back -->
          <button 
            @click="goBack"
            class="w-9 h-9 flex items-center justify-center text-gray-700 active:text-gray-900 transition-colors flex-shrink-0 -ml-1"
          >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
              <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
            </svg>
          </button>

          <!-- Store name — centered -->
          <div class="flex-1 flex flex-col items-center justify-center min-w-0 px-1">
            <span
              class="text-[14px] leading-tight text-gray-900 truncate max-w-full"
              :style="{ fontFamily: aiFonts.heading + ', Georgia, serif', fontWeight: 600, letterSpacing: '0.02em' }"
            >
              {{ storeConfig.store_name || '' }}
            </span>
          </div>

          <!-- Actions -->
          <div class="flex items-center flex-shrink-0">
            <!-- Share -->
            <button 
              @click="shareProduct" 
              class="w-9 h-9 flex items-center justify-center text-gray-500 hover:text-gray-900 transition-colors"
            >
              <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M7.217 10.907a2.25 2.25 0 100 2.186m0-2.186c.18.324.283.696.283 1.093s-.103.77-.283 1.093m0-2.186l9.566-5.314m-9.566 7.5l9.566 5.314m0 0a2.25 2.25 0 103.935 2.186 2.25 2.25 0 00-3.935-2.186zm0-12.814a2.25 2.25 0 103.933-2.185 2.25 2.25 0 00-3.933 2.185z" />
              </svg>
            </button>
            <!-- Cart with counter -->
            <button 
              @click="$router.push('/catalog/bolsa')"
              class="relative w-9 h-9 flex items-center justify-center text-gray-700"
            >
              <svg class="w-[20px] h-[20px]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.3">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993l1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 01-1.12-1.243l1.264-12A1.125 1.125 0 015.513 7.5h12.974c.576 0 1.059.435 1.119 1.007zM8.625 10.5a.375.375 0 11-.75 0 .375.375 0 01.75 0zm7.5 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
              </svg>
              <span 
                v-if="cartCount > 0"
                class="absolute -top-0.5 -right-0.5 bg-gray-900 text-white text-[8px] font-bold min-w-[16px] h-[16px] rounded-full flex items-center justify-center"
              >
                {{ cartCount }}
              </span>
            </button>
          </div>
        </div>
      </header>

      <!-- HERO GALLERY: Smart-Cropped Container -->
      <!-- PRODUCT TRANSITION OVERLAY -->
      <Transition name="product-swap">
        <div
          v-if="isTransitioning"
          class="fixed inset-0 z-[55] flex flex-col items-center justify-center gap-4 pointer-events-none"
          :style="{ backgroundColor: aiPalette.background }"
        >
          <div class="w-10 h-10 rounded-full border-2 border-gray-200 flex items-center justify-center" :style="{ borderTopColor: aiPalette.primary }">
            <svg class="w-5 h-5 animate-spin" :style="{ color: aiPalette.primary }" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-20" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3"></circle>
              <path class="opacity-90" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
            </svg>
          </div>
          <p class="text-xs text-gray-400 font-medium tracking-wide" :style="{ fontFamily: aiFonts.body + ', sans-serif' }">Cargando producto...</p>
        </div>
      </Transition>

      <div class="relative w-full" :style="{ backgroundColor: aiPalette.background }">
        <div 
          class="relative w-full max-h-[52vh] aspect-[4/5] overflow-hidden touch-pan-y border-b border-gray-100"
          @touchstart="onGalleryTouchStart"
          @touchmove="onGalleryTouchMove"
          @touchend="onGalleryTouchEnd"
        >
          <div 
            class="flex h-full transition-transform duration-300 ease-out"
            :style="{ transform: `translateX(calc(-${imageIndex * 100}% + ${swipeOffset}px))` }"
          >
            <div 
              v-for="(img, idx) in productImages" 
              :key="'img-'+idx"
              class="w-full h-full flex-shrink-0 flex items-center justify-center p-2"
            >
              <img 
                :src="img"
                :alt="product.name + ' - foto ' + (idx + 1)"
                class="max-w-full max-h-full object-contain"
                @error="(e) => e.target.style.display = 'none'"
              />
            </div>
          </div>
          
          <!-- Placeholder if no images -->
          <div v-if="productImages.length === 0" class="absolute inset-0 flex items-center justify-center">
            <svg class="w-16 h-16 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="0.8">
              <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909M3.75 21h16.5A2.25 2.25 0 0022.5 18.75V5.25A2.25 2.25 0 0020.25 3H3.75A2.25 2.25 0 001.5 5.25v13.5A2.25 2.25 0 003.75 21z" />
            </svg>
          </div>
        </div>
        
        <!-- Progress bars -->
        <div v-if="productImages.length > 1" class="absolute bottom-0 left-0 right-0 flex gap-[3px] px-3 pb-3">
          <div 
            v-for="(img, idx) in productImages" 
            :key="'bar-'+idx"
            class="h-[2.5px] flex-1 rounded-full transition-all duration-300"
            :class="imageIndex === idx ? 'bg-gray-900' : 'bg-gray-900/20'"
          ></div>
        </div>

        <!-- Counter pill -->
        <div v-if="productImages.length > 1" class="absolute bottom-3 right-3 bg-gray-900/70 text-white text-[10px] font-medium px-2 py-0.5 rounded-full">
          {{ imageIndex + 1 }}/{{ productImages.length }}
        </div>
      </div>

      <!-- PRODUCT INFO -->
      <div class="bg-white">
        
        <!-- Category + Title + Price -->
        <div class="px-5 pt-5 pb-4">
          <p class="text-[10px] text-gray-400 uppercase tracking-[0.2em] font-medium mb-1">
            {{ product.category_name || product.category || 'Producto' }}
          </p>
          <h1 class="text-[26px] font-normal text-gray-900 leading-tight mb-2" :style="{ fontFamily: aiFonts.heading + ', serif' }">
            {{ product.name }}
          </h1>
          <p class="text-sm text-gray-600 mb-3" :style="{ fontFamily: aiFonts.body + ', sans-serif' }">
            {{ productHook }}
          </p>
          <div class="flex items-baseline gap-3">
            <span class="text-2xl font-bold text-gray-900 tracking-tight">
              {{ currencySymbol }}{{ formatPrice(currentPrice) }}
            </span>
            <span v-if="currentStock > 0 && currentStock <= 5" class="text-[11px] text-amber-600 font-semibold uppercase tracking-wide bg-amber-50 px-2 py-0.5">
              Últimas {{ currentStock }} uds
            </span>
            <span v-else-if="currentStock === 0" class="text-[11px] text-red-500 font-semibold uppercase tracking-wide bg-red-50 px-2 py-0.5">
              Agotado
            </span>
          </div>

          <div v-if="storeConfig.ai_value_messages && storeConfig.ai_value_messages.length > 0" class="mt-3 flex flex-wrap gap-2">
            <span
              v-for="(message, idx) in storeConfig.ai_value_messages.slice(0, 2)"
              :key="'pdp-chip-' + idx"
              class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-[10px] font-semibold uppercase tracking-[0.1em] border"
              :style="{ borderColor: `${aiPalette.primary}33`, color: aiPalette.primary, backgroundColor: `${aiPalette.primary}08` }"
            >
              <svg class="w-2.5 h-2.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
              </svg>
              {{ message }}
            </span>
          </div>
        </div>

        <div class="h-px bg-gray-100"></div>

        <!-- VARIANT SELECTORS -->
        <div v-if="product.options && product.options.length > 0" class="px-5 py-4 space-y-4">
          <div v-for="option in product.options" :key="'opt-'+option.id">
            
            <div class="flex items-center justify-between mb-2">
              <span class="text-xs font-bold text-gray-900 uppercase tracking-[0.08em]">{{ option.name }}</span>
              <span v-if="selectedOptions[option.id]" class="text-xs text-gray-400 font-medium">
                {{ getSelectedOptionLabel(option) }}
              </span>
            </div>

            <!-- COLOR circles -->
            <div v-if="option.name.toUpperCase() === 'COLOR'" class="flex flex-wrap gap-3">
              <button 
                v-for="val in option.values" 
                :key="`color-${val.id}`"
                @click="selectedOptions[option.id] = val.id"
                class="relative w-10 h-10 rounded-full transition-all duration-200"
                :class="selectedOptions[option.id] === val.id ? 'ring-2 ring-offset-2 ring-gray-900' : 'ring-1 ring-gray-200 hover:ring-gray-400'"
                :title="val.value"
              >
                <span class="block w-full h-full rounded-full" :style="{ backgroundColor: val.value }"></span>
              </button>
            </div>

            <!-- SIZE / OTHER: rectangular boxes -->
            <div v-else class="flex flex-wrap gap-2">
              <button 
                v-for="val in option.values" 
                :key="`size-${val.id}`"
                @click="selectedOptions[option.id] = val.id"
                class="min-w-[48px] h-11 px-4 text-sm font-semibold tracking-wide border-2 rounded-md transition-all duration-200"
                :class="selectedOptions[option.id] === val.id
                  ? 'bg-gray-50 text-gray-900 border-gray-900' 
                  : 'bg-white text-gray-500 border-gray-200 hover:border-gray-400 active:bg-gray-50'"
              >
                {{ val.value }}
              </button>
            </div>
          </div>
        </div>

        <div class="h-px bg-gray-100"></div>

        <!-- �️ TRUST ZONE: Logística + Pagos + Seguridad -->
        <div class="px-5 py-4 flex flex-col gap-3 border-t border-b border-gray-100" :style="{ backgroundColor: '#fbfbfb' }">

          <!-- Logística: Envío + Retiro -->
          <div class="flex items-center gap-4 flex-wrap">
            <div class="flex items-center gap-1.5">
              <svg class="w-[15px] h-[15px] text-gray-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 01-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 00-3.213-9.193 2.056 2.056 0 00-1.58-.86H14.25M16.5 18.75h-2.25m0-11.177v-.958c0-.568-.422-1.048-.987-1.106a48.554 48.554 0 00-10.026 0 1.106 1.106 0 00-.987 1.106v7.635m12-6.677v6.677m0 4.5v-4.5m0 0h-12" />
              </svg>
              <span class="text-[13px] text-gray-700">Envío nacional</span>
            </div>
            <div class="w-1 h-1 rounded-full bg-gray-300"></div>
            <div class="flex items-center gap-1.5">
              <svg class="w-[15px] h-[15px] text-gray-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 21v-7.5a.75.75 0 01.75-.75h3a.75.75 0 01.75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349m-16.5 11.65V9.35m0 0a3.001 3.001 0 003.75-.615A2.993 2.993 0 009.75 9.75c.896 0 1.7-.393 2.25-1.016a2.993 2.993 0 002.25 1.016c.896 0 1.7-.393 2.25-1.016a3.001 3.001 0 003.75.614m-16.5 0a3.004 3.004 0 01-.621-4.72L4.318 3.44A1.5 1.5 0 015.378 3h13.243a1.5 1.5 0 011.06.44l1.19 1.189a3 3 0 01-.621 4.72m-13.5 8.65h3.75a.75.75 0 00.75-.75V13.5a.75.75 0 00-.75-.75H6.75a.75.75 0 00-.75.75v3.75c0 .415.336.75.75.75z" />
              </svg>
              <span class="text-[13px] text-gray-700">Retiro en local</span>
            </div>
          </div>

          <!-- Métodos de Pago (tarjetas estandarizadas) -->
          <div class="flex flex-wrap items-center gap-2">
            <!-- Visa -->
            <span class="inline-flex items-center justify-center h-7 px-2 bg-white border border-gray-200 rounded-md">
              <svg class="h-4 w-auto" viewBox="0 0 48 16" fill="none">
                <path d="M19.5 13h-2.7l1.7-10.5h2.7L19.5 13zm11.4-10.2c-.5-.2-1.4-.4-2.4-.4-2.6 0-4.5 1.4-4.5 3.4 0 1.5 1.3 2.3 2.3 2.8 1 .5 1.4.8 1.4 1.3 0 .7-.8 1-1.6 1-.8 0-1.7-.2-2.4-.5l-.3-.2-.4 2.2c.7.3 1.9.5 3.2.5 2.8 0 4.6-1.4 4.6-3.5 0-1.2-.7-2.1-2.2-2.8-.9-.5-1.5-.8-1.5-1.3 0-.4.5-.9 1.5-.9.9 0 1.5.2 2 .4l.2.1.4-2.1zm6.8-.3h-2c-.6 0-1.1.2-1.4.8L30 13h2.8l.6-1.6h3.4l.3 1.6h2.5l-2.2-10.5h-2.4zm-2 6.8l1.1-3 .4-1.2.2 1.2.7 3h-2.4zM16 2.5l-2.6 7.2-.3-1.4-.9-4.6c-.2-.7-.6-.9-1.3-.9H7.1l-.1.3c.9.2 1.9.5 2.5.9l2.1 8h2.8l4.3-10.5H16z" fill="#1A1F71"/>
              </svg>
            </span>
            <!-- Mastercard -->
            <span class="inline-flex items-center justify-center h-7 px-2 bg-white border border-gray-200 rounded-md">
              <svg class="h-4 w-auto" viewBox="0 0 36 22" fill="none">
                <circle cx="13" cy="11" r="8" fill="#EB001B"/>
                <circle cx="23" cy="11" r="8" fill="#F79E1B"/>
                <path d="M18 5.3a8 8 0 010 11.4 8 8 0 000-11.4z" fill="#FF5F00"/>
              </svg>
            </span>
            <!-- Nequi -->
            <span class="inline-flex items-center justify-center h-7 px-2.5 bg-white border border-gray-200 rounded-md">
              <span class="text-[11px] font-bold tracking-tight" style="color: #E6007E;">Nequi</span>
            </span>
            <!-- Bancolombia -->
            <span class="inline-flex items-center justify-center h-7 px-2.5 bg-white border border-gray-200 rounded-md">
              <span class="text-[11px] font-bold tracking-tight" style="color: #00543C;">Bancolombia</span>
            </span>
          </div>

          <!-- Seguridad -->
          <div class="flex items-center gap-1.5">
            <svg class="w-3.5 h-3.5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/>
            </svg>
            <span class="text-xs text-gray-500">Pago 100% seguro · Datos encriptados</span>
          </div>
        </div>

        <!-- Description accordion -->
        <div v-if="product.description" class="px-5 py-3">
          <button 
            @click="showDescription = !showDescription"
            class="w-full flex items-center justify-between py-1"
          >
            <span class="text-xs font-bold text-gray-900 uppercase tracking-[0.08em]">Descripción</span>
            <svg 
              class="w-4 h-4 text-gray-400 transition-transform duration-200" 
              :class="{ 'rotate-180': showDescription }"
              fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"
            >
              <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
            </svg>
          </button>
          <Transition name="slide-down">
            <div v-if="showDescription" class="mt-3">
              <p class="text-sm text-gray-500 leading-relaxed">{{ product.description }}</p>
            </div>
          </Transition>
        </div>

        <div class="h-px bg-gray-100"></div>

        <div v-if="productStorySnippet" class="mx-5 mt-4 mb-1 p-4 rounded-2xl border" :style="{ borderColor: aiPalette.secondary, backgroundColor: aiPalette.background }">
          <p class="text-[10px] uppercase tracking-[0.18em] font-semibold mb-2" :style="{ color: aiPalette.primary }">Inspiración de marca</p>
          <p class="text-sm leading-relaxed text-gray-600" :style="{ fontFamily: aiFonts.body + ', sans-serif' }">{{ productStorySnippet }}</p>
        </div>

        <!-- CROSS-SELLING: "También te podría interesar" -->
        <!-- CROSS-SELLING: También te podría interesar -->
        <div v-if="relatedProducts.length > 0" class="pt-5 pb-3">
          <div class="px-5 flex items-center justify-between mb-4">
            <h3 class="text-xs font-bold uppercase tracking-[0.14em]" :style="{ color: aiPalette.text_dark }">
              {{ crossSellHeading }}
            </h3>
            <span class="text-[10px] text-gray-400">{{ relatedProducts.length }} productos</span>
          </div>
          <div class="flex gap-3 overflow-x-auto px-5 pb-3 scrollbar-hide snap-x snap-mandatory">
            <div 
              v-for="related in relatedProducts" 
              :key="'cross-'+related.id"
              @click="navigateToProduct(related)"
              class="flex-shrink-0 w-[140px] snap-start cursor-pointer group relative"
              :class="{ 'pointer-events-none': isTransitioning }"
            >
              <!-- Card image -->
              <div
                class="relative aspect-[3/4] overflow-hidden mb-2.5 rounded-2xl border transition-all duration-200"
                :class="transitioningToId === related.id ? 'border-transparent shadow-lg' : 'border-gray-100 group-active:scale-[0.97]'"
                :style="transitioningToId === related.id ? { borderColor: aiPalette.primary, boxShadow: `0 4px 20px ${aiPalette.primary}33` } : {}"
              >
                <img 
                  :src="related.images && related.images.length > 0 ? related.images[0] : related.image_url"
                  :alt="related.name"
                  class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105"
                  @error="(e) => e.target.style.display = 'none'"
                />
                <!-- Loading spinner overlay on the card being navigated to -->
                <div
                  v-if="transitioningToId === related.id"
                  class="absolute inset-0 flex items-center justify-center rounded-2xl"
                  :style="{ backgroundColor: `${aiPalette.background}CC` }"
                >
                  <div class="w-8 h-8 rounded-full border-2 border-gray-200 flex items-center justify-center" :style="{ borderTopColor: aiPalette.primary }">
                    <svg class="w-4 h-4 animate-spin" :style="{ color: aiPalette.primary }" fill="none" viewBox="0 0 24 24">
                      <circle class="opacity-20" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3"></circle>
                      <path class="opacity-90" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                    </svg>
                  </div>
                </div>
                <!-- Category badge -->
                <div class="absolute top-2 left-2">
                  <span
                    v-if="related.category_name || related.category"
                    class="px-2 py-0.5 rounded-full text-[8px] font-bold uppercase tracking-wide bg-white/90 text-gray-600 backdrop-blur-sm"
                  >
                    {{ (related.category_name || related.category || '').slice(0, 12) }}
                  </span>
                </div>
              </div>
              <!-- Info -->
              <p
                class="text-[12px] leading-snug font-semibold truncate mb-0.5 transition-colors duration-200"
                :style="{ color: transitioningToId === related.id ? aiPalette.primary : aiPalette.text_dark, fontFamily: aiFonts.heading + ', serif' }"
              >{{ related.name }}</p>
              <p class="text-[12px] font-bold" :style="{ color: aiPalette.text_dark }">
                {{ currencySymbol }}{{ formatPrice(related.price) }}
              </p>
            </div>
          </div>
        </div>

        <!-- Spacer for sticky CTA -->
        <div class="h-24"></div>
      </div>

      <!-- STICKY CTA BAR -->
      <div class="fixed bottom-0 left-0 right-0 z-[60] bg-white border-t border-gray-100" style="box-shadow: 0 -2px 10px rgba(0,0,0,0.06);">
        <div class="px-4 py-3 flex items-center gap-3">
          <div class="flex-shrink-0">
            <p class="text-[18px] font-bold text-gray-900 leading-none">
              {{ currencySymbol }}{{ formatPrice(currentPrice) }}
            </p>
            <p v-if="currentStock > 0" class="text-[10px] text-emerald-600 font-medium mt-0.5">En stock</p>
            <p v-else class="text-[10px] text-red-500 font-medium mt-0.5">Sin stock</p>
          </div>
          <button 
            @click="handleAddToCart"
            :disabled="currentStock === 0 || !isVariantSelected || addingToCart"
            class="flex-1 h-[48px] text-white text-[13px] font-bold uppercase tracking-[0.12em] rounded-lg transition-all disabled:bg-gray-200 disabled:text-gray-400 disabled:cursor-not-allowed flex items-center justify-center gap-2 active:scale-[0.98]"
            :style="{ backgroundColor: aiPalette.primary }"
          >
            <!-- Spinner state -->
            <svg v-if="addingToCart" class="animate-spin w-[18px] h-[18px]" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <!-- Normal state -->
            <svg v-else-if="currentStock > 0 && isVariantSelected" class="w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
              <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993l1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 01-1.12-1.243l1.264-12A1.125 1.125 0 015.513 7.5h12.974c.576 0 1.059.435 1.119 1.007zM8.625 10.5a.375.375 0 11-.75 0 .375.375 0 01.75 0zm7.5 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
            </svg>
            {{ addingToCart ? 'AGREGANDO...' : (currentStock === 0 ? 'AGOTADO' : (isVariantSelected ? 'AÑADIR AL CARRITO' : 'SELECCIONA OPCIONES')) }}
          </button>
        </div>
      </div>
    </template>

    <!-- TOAST NOTIFICATION - Minimal pill -->
    <Transition name="toast">
      <div 
        v-if="toast.show" 
        class="fixed top-14 left-4 right-4 z-[100] bg-white px-4 py-3 rounded-2xl flex items-center gap-3"
        style="box-shadow: 0 4px 24px rgba(0,0,0,0.10), 0 1px 3px rgba(0,0,0,0.06);"
      >
        <div class="w-6 h-6 rounded-full bg-emerald-50 flex items-center justify-center flex-shrink-0">
          <svg class="w-3.5 h-3.5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
          </svg>
        </div>
        <span class="text-sm font-medium text-gray-800 flex-1 leading-snug">{{ toast.message }}</span>
        <button 
          @click="$router.push('/catalog/bolsa')" 
          class="text-[11px] font-semibold uppercase tracking-wide text-gray-600 bg-gray-100 hover:bg-gray-200 px-3 py-1.5 rounded-lg transition-colors flex-shrink-0"
        >
          Ver bolsa
        </button>
      </div>
    </Transition>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import apiClient from '../../services/apiClient.js'
import { useCatalogCart } from '../../stores/catalogCart.js'
import { productUrl, findProductBySlug } from '../../utils/slugify.js'

const router = useRouter()
const route = useRoute()
const { cartCount, addItem, toast } = useCatalogCart()

// State
const loading = ref(true)
const error = ref(false)
const product = ref(null)
const allProducts = ref([])
const storeConfig = ref({})
const selectedOptions = ref({})
const imageIndex = ref(0)
const swipeOffset = ref(0)
const showDescription = ref(false)
const addingToCart = ref(false)
const isTransitioning = ref(false)
const transitioningToId = ref(null)

// Swipe tracking
let touchStartX = 0
let touchStartY = 0
let isSwiping = false

const currencySymbol = computed(() => storeConfig.value.currency_symbol || '$')

const aiPalette = computed(() => {
  const palette = storeConfig.value.ai_color_palette || {}
  return {
    primary: palette.primary || '#0f172a',
    secondary: palette.secondary || '#d1d5db',
    accent: palette.accent || '#111827',
    background: palette.background || '#ffffff',
    text_dark: palette.text_dark || '#111827',
    text_light: palette.text_light || '#f8fafc'
  }
})

const aiFonts = computed(() => {
  const fonts = storeConfig.value.ai_fonts || {}
  return {
    heading: fonts.heading || 'Playfair Display',
    body: fonts.body || 'Montserrat'
  }
})

const themeVars = computed(() => ({
  '--pdp-primary': aiPalette.value.primary,
  '--pdp-secondary': aiPalette.value.secondary,
  '--pdp-background': aiPalette.value.background,
  '--pdp-text-dark': aiPalette.value.text_dark,
  '--pdp-text-light': aiPalette.value.text_light,
  '--pdp-font-heading': aiFonts.value.heading,
  '--pdp-font-body': aiFonts.value.body
}))

const productHook = computed(() => {
  const values = storeConfig.value.ai_value_messages
  if (values && Array.isArray(values) && values.length > 0) {
    return values[0]
  }
  return storeConfig.value.ai_banner_texts?.subheadline || 'Pieza elegida para tu estilo diario'
})

const productStorySnippet = computed(() => {
  const about = (storeConfig.value.ai_about_us || '').trim()
  if (!about) return ''
  return about.length > 180 ? `${about.slice(0, 180).trim()}...` : about
})

// AI-generated cross-sell heading
const crossSellHeading = computed(() => {
  const messages = storeConfig.value.ai_cross_sell_messages
  if (messages && Array.isArray(messages) && messages.length > 0) {
    return messages[Math.floor(Math.random() * messages.length)]
  }
  return 'También te podría interesar'
})

// Images
const productImages = computed(() => {
  if (!product.value) return []
  const imgs = []
  if (product.value.images && product.value.images.length > 0) {
    imgs.push(...product.value.images)
  } else if (product.value.image_url) {
    imgs.push(product.value.image_url)
  }
  return imgs
})

// Variant resolution
const currentVariant = computed(() => {
  if (!product.value || !product.value.variants || product.value.variants.length === 0) return null
  return product.value.variants.find(variant => {
    return Object.entries(selectedOptions.value).every(([optionId, valueId]) => {
      return variant.option_values.some(ov => ov.option_id == optionId && ov.value_id == valueId)
    })
  })
})

const currentPrice = computed(() => {
  if (currentVariant.value) return currentVariant.value.price
  return product.value ? product.value.price : 0
})

const currentStock = computed(() => {
  if (currentVariant.value) return currentVariant.value.stock
  return product.value ? product.value.stock : 0
})

const isVariantSelected = computed(() => {
  if (!product.value || !product.value.options || product.value.options.length === 0) return true
  return product.value.options.every(opt => selectedOptions.value[opt.id] !== undefined)
})

// Related products (same category, different product)
const relatedProducts = computed(() => {
  if (!product.value || allProducts.value.length === 0) return []
  const sameCategory = allProducts.value.filter(p => 
    p.id !== product.value.id && p.category_id === product.value.category_id
  )
  const others = allProducts.value.filter(p => 
    p.id !== product.value.id && p.category_id !== product.value.category_id
  )
  return [...sameCategory, ...others].slice(0, 8)
})

// Helper to get URL for images
const getImageUrl = (path) => {
  if (!path) return ''
  if (path.startsWith('http') || path.startsWith('data:')) return path
  return path.startsWith('/') ? path : `/${path}`
}

// Load product + all products + config
const loadData = async () => {
  loading.value = true
  error.value = false
  try {
    const [configRes, productsRes] = await Promise.all([
      apiClient.get('/public/catalog/config'),
      apiClient.get('/public/catalog')
    ])

    if (configRes.data.success && configRes.data.data) {
      const d = configRes.data.data
      storeConfig.value = {
        currency_symbol: '$',
        delivery_cost: parseFloat(d.delivery_cost || 0),
        min_order_value: parseFloat(d.minimum_order || 0),
        whatsapp_number: d.whatsapp_number || '',
        store_name: d.store_name || 'Mi Tienda',
        custom_message: d.custom_message || '',
        ai_cross_sell_messages: d.ai_cross_sell_messages || null,
        ai_fonts: d.ai_fonts || null,
        ai_color_palette: d.ai_color_palette || null,
        ai_banner_texts: d.ai_banner_texts || null,
        ai_about_us: d.ai_about_us || '',
        ai_value_messages: d.ai_value_messages || null
      }
    }

    if (productsRes.data.success) {
      allProducts.value = productsRes.data.products.map(p => ({
        id: p.id,
        name: p.name,
        description: p.description,
        price: p.price,
        image_url: getImageUrl(p.image || p.image_url),
        images: (p.images || []).map(getImageUrl),
        stock: p.stock || 0,
        category: p.category || 'Sin categoría',
        category_id: p.category_id,
        category_name: p.category || 'Producto',
        unit: p.unit || 'unidad',
        measurement_unit: p.measurement_unit,
        allow_decimal: p.allow_decimal || false,
        type: p.type || 'simple',
        options: p.options || [],
        variants: p.variants || []
      }))

      // Find current product by slug
      product.value = findProductBySlug(route.params.slug, allProducts.value)
      
      if (!product.value) {
        error.value = true
      }
    }
  } catch (e) {
    error.value = true
  } finally {
    loading.value = false
  }
}

const formatPrice = (price) => {
  return Number(price).toLocaleString('es-CO', { minimumFractionDigits: 0, maximumFractionDigits: 0 })
}

const getSelectedOptionLabel = (option) => {
  const selectedValueId = selectedOptions.value[option.id]
  if (!selectedValueId) return ''
  const val = option.values.find(v => v.id === selectedValueId)
  return val ? val.value : ''
}

// Add to cart with loading animation
const handleAddToCart = async () => {
  if (!product.value || !isVariantSelected.value || currentStock.value === 0) return
  
  addingToCart.value = true
  
  // Simulate brief loading for UX
  await new Promise(resolve => setTimeout(resolve, 600))
  
  const productToAdd = {
    ...product.value,
    id: currentVariant.value ? `${product.value.id}-${currentVariant.value.id}` : product.value.id,
    price: currentPrice.value,
    stock: currentStock.value,
    variant_id: currentVariant.value ? currentVariant.value.id : null,
    variant_name: product.value.options?.length > 0 
      ? product.value.options.map(opt => getSelectedOptionLabel(opt)).filter(Boolean).join(' / ') 
      : null,
    selected_options: { ...selectedOptions.value },
    image_url: productImages.value[0] || product.value.image_url
  }
  
  addItem(productToAdd)
  addingToCart.value = false
}

// Navigation
const goBack = () => {
  if (window.history.length > 1) {
    router.back()
  } else {
    router.push('/catalog')
  }
}

const navigateToProduct = (related) => {
  transitioningToId.value = related.id
  isTransitioning.value = true
  router.push(productUrl(related))
}

// Share
const shareProduct = async () => {
  if (!product.value) return
  const text = `${product.value.name} - ${currencySymbol.value}${formatPrice(product.value.price)}`
  const url = window.location.href
  if (navigator.share) {
    try {
      await navigator.share({ title: product.value.name, text, url })
    } catch (_) {}
  } else {
    try {
      await navigator.clipboard.writeText(`${text}\n${url}`)
    } catch (_) {}
  }
}

// Gallery swipe handlers
const onGalleryTouchStart = (e) => {
  touchStartX = e.touches[0].clientX
  touchStartY = e.touches[0].clientY
  isSwiping = false
}

const onGalleryTouchMove = (e) => {
  const dx = e.touches[0].clientX - touchStartX
  const dy = e.touches[0].clientY - touchStartY
  if (!isSwiping && Math.abs(dx) > Math.abs(dy) && Math.abs(dx) > 10) {
    isSwiping = true
  }
  if (isSwiping) {
    e.preventDefault()
    swipeOffset.value = dx * 0.6
  }
}

const onGalleryTouchEnd = () => {
  if (isSwiping) {
    const threshold = 50
    if (swipeOffset.value < -threshold && imageIndex.value < productImages.value.length - 1) {
      imageIndex.value++
    } else if (swipeOffset.value > threshold && imageIndex.value > 0) {
      imageIndex.value--
    }
  }
  swipeOffset.value = 0
  isSwiping = false
}

// Watch route changes (for cross-selling navigation)
watch(() => route.params.slug, (newSlug) => {
  if (!newSlug) return
  window.scrollTo({ top: 0, behavior: 'smooth' })
  isTransitioning.value = true
  // Short delay so user sees the loading state before content swaps
  setTimeout(() => {
    const found = findProductBySlug(newSlug, allProducts.value)
    if (found) {
      product.value = found
      selectedOptions.value = {}
      imageIndex.value = 0
      swipeOffset.value = 0
      showDescription.value = false
    } else {
      loadData()
    }
    // Allow content to settle before removing overlay
    setTimeout(() => {
      isTransitioning.value = false
      transitioningToId.value = null
    }, 250)
  }, 350)
})

onMounted(() => {
  loadData()
})
</script>

<style scoped>
.pdp-immersive {
  font-family: var(--pdp-font-body), 'Inter', 'Helvetica Neue', sans-serif;
}

.pdp-immersive h1,
.pdp-immersive h2,
.pdp-immersive h3 {
  font-family: var(--pdp-font-heading), 'Playfair Display', 'Georgia', serif;
}

.scrollbar-hide::-webkit-scrollbar { display: none; }
.scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }

.slide-down-enter-active, .slide-down-leave-active {
  transition: all 0.3s ease;
}
.slide-down-enter-from, .slide-down-leave-to {
  opacity: 0; transform: translateY(-10px);
}

.toast-enter-active {
  transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
}
.toast-leave-active {
  transition: all 0.3s ease;
}
.toast-enter-from {
  opacity: 0; transform: translateY(-20px) scale(0.95);
}
.toast-leave-to {
  opacity: 0; transform: translateY(-10px);
}

.product-swap-enter-active {
  transition: opacity 0.18s ease;
}
.product-swap-leave-active {
  transition: opacity 0.25s ease;
}
.product-swap-enter-from,
.product-swap-leave-to {
  opacity: 0;
}
</style>
