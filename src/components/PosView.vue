<template>
  <div class="h-full relative" style="background-color: #EEEFF2; padding-bottom: 0px;">
    
    <!-- Loading inicial para evitar parpadeo -->
    <div v-if="initializing" class="absolute inset-0 flex items-center justify-center z-50" style="background-color: #EEEFF2;">
      <div class="flex flex-col items-center space-y-4">
        <div class="w-8 h-8 border-4 border-blue-200 border-t-blue-600 rounded-full animate-spin"></div>
        <p class="text-sm text-gray-600 font-medium">Cargando punto de venta...</p>
      </div>
    </div>
    
    <!-- Overlay oscuro cuando caja está cerrada Y no está en modo cotización (SOLO después de inicializar) -->
    <Transition 
      name="overlay-fade"
      enter-active-class="transition-all duration-500 ease-out"
      leave-active-class="transition-all duration-300 ease-in"
      enter-from-class="opacity-0"
      enter-to-class="opacity-100"
      leave-from-class="opacity-100"
      leave-to-class="opacity-0"
    >
      <div v-if="!initializing && !hasOpenSession && !quotationMode" class="absolute inset-0 bg-black/40 backdrop-blur-sm z-50 pointer-events-none"></div>
    </Transition>
    
    <!-- BARRA DE HERRAMIENTAS EMPRESARIAL -->
    <div class="sticky top-0 z-30 bg-white backdrop-blur-xl border-b border-slate-200/60 shadow-[0_4px_20px_-10px_rgba(0,0,0,0.05)] transition-all duration-300">
    
    <div class="px-4 lg:px-6 py-3 bg-white">
      <div class="flex flex-col md:flex-row items-center justify-between gap-4">
        
        <div class="flex items-center gap-3 flex-1 w-full md:w-auto">
          <div class="flex-1 max-w-3xl relative group z-20">
            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
              <svg class="h-5 w-5 text-slate-400 group-focus-within:text-indigo-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
              </svg>
            </div>
            
            <input
              ref="searchInput"
              v-model="searchTerm"
              type="text"
              placeholder="Buscar productos, SKU o escanear..."
              class="block w-full h-12 pl-12 pr-24 text-sm font-medium bg-white border-0 text-slate-900 placeholder-slate-400 rounded-2xl shadow-[0_2px_10px_-3px_rgba(0,0,0,0.05)] ring-1 ring-slate-100 focus:ring-2 focus:ring-indigo-500 transition-all duration-200"
              @keydown.escape="clearSearch"
              @keydown.enter.prevent="handleSearchEnter"
              @input="handleBarcodeInput"
            />
            
            <div class="absolute inset-y-0 right-0 flex items-center pr-2 space-x-1">
              <button
                @click="startQRScanner"
                class="p-2 text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-xl transition-all duration-200"
                title="Escanear código"
              >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path>
                </svg>
              </button>
              
              <button
                v-if="searchTerm"
                @click="clearSearch"
                class="p-2 text-slate-400 hover:text-rose-500 hover:bg-rose-50 rounded-xl transition-all duration-200"
                title="Limpiar búsqueda"
              >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
              </button>
            </div>
          </div>

          <button 
            @click="clearFilters" 
            class="h-12 w-12 flex-shrink-0 flex items-center justify-center bg-white text-slate-500 hover:text-rose-600 hover:bg-rose-50 rounded-2xl shadow-[0_2px_10px_-3px_rgba(0,0,0,0.05)] ring-1 ring-slate-100 transition-all duration-200"
            title="Resetear filtros"
          >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
            </svg>
          </button>
        </div>
        
        <div class="flex items-center gap-3 w-full md:w-auto justify-end">
          
          <button 
            @click="refreshPosData" 
            class="h-10 w-10 flex items-center justify-center text-slate-500 hover:text-indigo-600 bg-white hover:bg-indigo-50 rounded-xl ring-1 ring-slate-200/60 shadow-sm transition-all duration-200"
            title="Sincronizar"
          >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v6h6M20 20v-6h-6M20 4l-5.5 5.5M4 20l5.5-5.5"/></svg>
          </button>

          <button
            @click="showReturnsModal = true"
            :disabled="quotationMode"
            class="hidden sm:flex items-center gap-3 pl-1.5 pr-4 py-1.5 rounded-full border transition-all duration-300 group"
            :class="quotationMode ? 'bg-slate-100 border-slate-200 text-slate-400' : 'bg-white border-slate-200 hover:border-blue-300 shadow-sm'"
          >
            <div class="w-9 h-9 rounded-full flex items-center justify-center shadow-sm transition-colors bg-white border border-slate-200"
                 :class="quotationMode ? 'text-slate-400' : 'text-blue-500 group-hover:text-blue-600'">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"></path></svg>
            </div>
            <div class="flex flex-col text-left leading-none">
              
              <span class="text-xs font-bold text-slate-900">Devoluciones</span>
            </div>
          </button>
          
          <WhatsAppStatus ref="whatsappStatus" class="hidden sm:block" />
          
          <button 
            @click="hasOpenSession ? showCloseCashModal() : showOpenCashModal()"
            class="relative flex items-center gap-3 pl-1.5 pr-4 py-1.5 rounded-full border transition-all duration-300 group"
            :class="hasOpenSession 
              ? 'bg-white border-emerald-200 hover:border-emerald-400 shadow-sm' 
              : 'bg-slate-100 border-slate-200 hover:bg-white hover:border-rose-300'"
          >
            <div class="w-9 h-9 rounded-full flex items-center justify-center shadow-sm transition-colors"
                 :class="hasOpenSession ? 'bg-emerald-500 text-white' : 'bg-white border border-slate-200 text-slate-400 group-hover:text-rose-500'">
              <svg v-if="hasOpenSession" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
              <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
            </div>
            
            <div class="flex flex-col text-left leading-none">
              <span class="text-[9px] font-black uppercase tracking-widest mb-1"
                    :class="hasOpenSession ? 'text-emerald-600' : 'text-slate-400 group-hover:text-rose-500'">
                {{ hasOpenSession ? 'Turno Activo' : 'Cerrado' }}
              </span>
              <span class="text-xs font-bold text-slate-900">
                {{ hasOpenSession ? 'Cerrar Caja' : 'Abrir Turno' }}
              </span>
            </div>
          </button>

        </div>
      </div>
    </div>

    <div class="px-4 lg:px-6 pb-4 bg-white overflow-x-auto scrollbar-hide">
        <div class="flex items-center gap-1.5 min-w-max">
            <button 
                @click="selectedCategory = null"
                class="px-5 py-1 rounded-full text-xs font-bold border transition-all duration-200 flex items-center gap-2"
                :class="!selectedCategory 
                    ? 'bg-slate-900 text-white border-slate-900 shadow-md shadow-slate-900/20' 
                    : 'bg-white text-slate-600 border-slate-200 hover:border-slate-300 hover:bg-slate-50'"
            >
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                Todas
            </button>

            <button 
                v-for="cat in categories" 
                :key="cat.id"
                @click="selectedCategory = cat.id"
                class="px-5 py-1 rounded-full text-xs font-bold border transition-all duration-200 whitespace-nowrap"
                :class="selectedCategory === cat.id
                    ? 'bg-slate-900 text-white border-slate-900 shadow-md' 
                    : 'bg-white text-slate-600 border-slate-200 hover:border-indigo-300 hover:text-indigo-600'"
            >
                {{ cat.name }}
            </button>
        </div>
    </div>
</div>
    <!-- FIN BARRA DE HERRAMIENTAS EMPRESARIAL -->
    
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-4 px-4 lg:px-6 pt-2 pb-4" style="height: calc(100vh - 12rem - 44px);">
  <!-- Panel Izquierdo: Catálogo de Productos - 50% (6/12) -->
  <div class="lg:col-span-6 h-full overflow-hidden">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 h-full flex flex-col overflow-hidden">
      
      <div class="flex-1 p-4 overflow-y-auto bg-gray-50" style="scrollbar-width: thin; scrollbar-color: #cbd5e1 transparent;">
        
        <div v-if="productsLoading" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 xl:grid-cols-5 gap-3">
          <div v-for="n in 10" :key="n" class="bg-white rounded-2xl p-3 border border-slate-100 shadow-sm animate-pulse">
            <div class="aspect-square bg-slate-100 rounded-xl mb-3"></div>
            <div class="h-5 bg-slate-100 rounded-lg w-2/3 mb-2"></div>
          <div class="h-4 bg-slate-100 rounded-lg w-1/3"></div>
        </div>
      </div>
      
      <div v-else-if="filteredProducts.length === 0" class="h-full flex flex-col items-center justify-center text-center py-12">
        <div class="w-24 h-24 bg-white rounded-full flex items-center justify-center mb-4 shadow-[0_4px_20px_-5px_rgba(0,0,0,0.05)] border border-slate-100">
           <svg class="w-10 h-10 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
             <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
           </svg>
        </div>
        <h3 class="text-base font-bold text-slate-900 mb-1">No hay productos aquí</h3>
        <p class="text-sm text-slate-500 max-w-[220px] leading-relaxed">
           No encontramos coincidencias para tu búsqueda.
        </p>
        <button
          v-if="searchTerm || selectedCategory"
          @click="clearSearch(); selectedCategory = null;"
          class="mt-5 px-5 py-2.5 bg-slate-900 text-white text-xs font-bold rounded-xl hover:bg-black transition-all shadow-lg shadow-slate-200"
        >
          Limpiar filtros
        </button>
      </div>
      
      <div v-else class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 xl:grid-cols-5 gap-4 pb-20">
        <div
          v-for="product in filteredProducts"
          :key="product.id"
          class="group bg-white rounded-2xl p-3 border border-transparent hover:border-indigo-200 shadow-[0_2px_8px_-3px_rgba(0,0,0,0.05)] hover:shadow-[0_8px_25px_-8px_rgba(99,102,241,0.15)] transition-all duration-300 cursor-pointer relative flex flex-col overflow-hidden hover:-translate-y-1"
          @click="addToCart(product)"
        >
          
          <div class="aspect-square rounded-xl overflow-hidden bg-slate-50 relative mb-3">
             
             <!-- Badge de cantidad en carrito (solo si tiene items) -->
             <div v-if="getProductQuantityInCart(product.id) > 0" 
                  class="absolute top-2 right-2 z-10 w-7 h-7 rounded-full bg-indigo-600 text-white flex items-center justify-center text-xs font-black shadow-lg border-2 border-white">
               {{ getProductQuantityInCart(product.id) }}
             </div>

             <img
              :src="getProductImage(product)"
              :alt="product.name"
              @error="handleImageError"
              class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110"
              loading="lazy"
            />
            
            <div class="absolute inset-0 bg-indigo-900/0 group-hover:bg-indigo-900/5 transition-colors duration-300"></div>

            <div class="absolute bottom-2 right-2 translate-y-10 opacity-0 group-hover:translate-y-0 group-hover:opacity-100 transition-all duration-300 ease-out">
               <div class="bg-indigo-600 text-white rounded-lg p-1.5 shadow-lg">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
               </div>
            </div>
          </div>

          <div class="flex flex-col gap-1">
            <div class="flex items-center justify-between">
               <span class="text-xl font-black text-slate-900 tracking-tight">
                 ${{ product.price.toLocaleString() }}
               </span>
            </div>
            <h3 class="text-xs font-semibold text-slate-500 leading-snug line-clamp-2 h-[2.5em]" :title="product.name">
              {{ product.name }}
            </h3>
            <!-- Stock discreto abajo -->
            <p class="text-[10px] text-slate-400 mt-0.5">
              <span v-if="product.stock <= 5" class="text-rose-500 font-bold">¡Solo {{ product.stock }}!</span>
              <span v-else>Stock: {{ product.stock }}</span>
            </p>
          </div>
        </div>
      </div>

    </div>
  </div>
</div>
    

<!-- bloque de ventas -->

<div class="lg:col-span-3 h-full overflow-hidden">
  <div class="bg-white rounded-2xl shadow-sm border border-gray-200 h-full flex flex-col overflow-hidden">
    
    <div class="p-4 border-b border-gray-200 bg-white flex-shrink-0">
      <div class="flex items-center justify-between mb-3">
        <h2 class="text-xs font-black text-slate-800 uppercase tracking-widest flex items-center gap-2">
          <span class="w-1.5 h-5 bg-indigo-600 rounded-full"></span>
          {{ loadedQuotation ? 'Cotización #' + loadedQuotation.code : quotationMode ? 'Nueva Cotización' : 'Ticket de Venta' }}
        </h2>
        
        <span v-if="loadedQuotation || quotationMode" class="px-2 py-1 rounded text-[9px] font-bold uppercase bg-amber-50 text-amber-600 border border-amber-100 tracking-wide">
          Modo Edición
        </span>
      </div>

      <button
        @click="showCustomerSelector = true"
        class="w-full flex items-center justify-between p-2.5 bg-slate-50 hover:bg-slate-100 border border-slate-200 rounded-xl transition-all duration-200 group active:scale-[0.99]"
      >
        <div class="flex items-center gap-3 overflow-hidden">
          <div class="w-8 h-8 rounded-full bg-white border border-slate-200 flex items-center justify-center text-blue-500 shadow-sm group-hover:border-blue-300 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
          </div>
          <div class="flex flex-col text-left overflow-hidden">
             <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider">Cliente</span>
             <span class="text-xs font-bold text-slate-900 truncate group-hover:text-blue-600 transition-colors">
               {{ selectedCustomer ? selectedCustomer.name : 'Cliente General' }}
             </span>
          </div>
        </div>
        <svg class="w-4 h-4 text-slate-300 group-hover:text-blue-500 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
      </button>
    </div>

    <div class="flex-1 p-3 overflow-y-auto bg-gray-50 relative" style="scrollbar-width: thin; scrollbar-color: #cbd5e1 transparent;">
      
      <div v-if="cart.items.length === 0" class="absolute inset-0 flex flex-col items-center justify-center text-center opacity-50 pointer-events-none">
        <div class="w-20 h-20 bg-white rounded-full flex items-center justify-center mb-3 border border-gray-200 shadow-sm">
          <svg class="w-8 h-8 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
        </div>
        <h3 class="text-sm font-bold text-slate-500">Carrito Vacío</h3>
        <p class="text-xs text-slate-400 mt-1">Escanea productos para comenzar</p>
      </div>

      <div v-else class="space-y-2.5 pb-4">
        <div
          v-for="item in cart.items"
          :key="item.id"
          class="group relative flex gap-3 p-2.5 bg-white rounded-xl shadow-sm border border-transparent hover:border-indigo-100 transition-all hover:shadow-md"
        >
          <div class="w-14 h-14 rounded-lg bg-slate-50 border border-slate-100 overflow-hidden flex-shrink-0 relative">
             <img :src="getProductImage(item)" class="w-full h-full object-cover" @error="handleImageError" />
          </div>

          <div class="flex-1 min-w-0 flex flex-col justify-between py-0.5">
            <div class="flex justify-between items-start gap-2">
              <h3 class="text-xs font-bold text-slate-700 truncate leading-snug" :title="item.name">
                {{ item.name }}
              </h3>
              <p class="text-sm font-black text-slate-900">
                ${{ (item.price * item.quantity).toLocaleString() }}
              </p>
            </div>
            
            <div class="flex items-center justify-between mt-2">
              <p class="text-[10px] font-medium text-slate-400">${{ item.price.toLocaleString() }} c/u</p>
              
              <div class="flex items-center bg-slate-50 border border-slate-200 rounded-lg h-6 shadow-sm">
                <button 
                  @click="updateQuantity(item.id, item.quantity - 1)" 
                  class="w-7 h-full flex items-center justify-center text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-l-lg transition-colors font-bold text-base leading-none pb-0.5"
                >-</button>
                <span class="w-6 text-center text-xs font-bold text-slate-900 select-none">{{ item.quantity }}</span>
                <button 
                  @click="updateQuantity(item.id, item.quantity + 1)" 
                  class="w-7 h-full flex items-center justify-center text-slate-400 hover:text-emerald-600 hover:bg-emerald-50 rounded-r-lg transition-colors font-bold text-base leading-none pb-0.5"
                >+</button>
              </div>
            </div>
          </div>

          <button
            @click="removeFromCart(item.id)"
            class="absolute -top-2 -right-2 w-6 h-6 bg-white border border-rose-100 text-rose-500 rounded-full flex items-center justify-center shadow-sm opacity-0 group-hover:opacity-100 transition-all hover:bg-rose-500 hover:text-white hover:scale-110 z-20"
            title="Quitar"
          >
            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
          </button>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- fin bloque de ventas -->  

<div class="lg:col-span-3 h-full overflow-hidden">
  
  <div class="bg-white rounded-2xl border border-gray-200 flex flex-col h-full overflow-hidden shadow-[0_0_40px_-10px_rgba(0,0,0,0.15)]">
    
    <!-- Header compacto -->
    <div class="relative bg-gray-50 px-4 py-3 border-b border-gray-200 flex-shrink-0">
      
      <div class="flex items-center justify-between">
        <div>
          <h3 class="text-[10px] font-semibold text-gray-500 uppercase tracking-wide mb-0.5">Total a Pagar</h3>
          <div class="flex items-baseline gap-1">
            <span class="text-2xl font-black text-gray-900">
              ${{ total.toLocaleString() }}
            </span>
          </div>
        </div>
        <div class="text-right">
           <div class="bg-white px-2.5 py-1.5 rounded-lg border border-gray-200">
              <div class="text-[10px] text-gray-500">Items</div>
              <div class="text-lg font-bold text-gray-900">{{ totalItems }}</div>
           </div>
        </div>
      </div>
    </div>
    
    <!-- Área de contenido con scroll limpio -->
    <div class="flex-1 overflow-y-auto p-3 space-y-2.5 bg-gray-50"
         style="scrollbar-width: thin; scrollbar-color: #cbd5e1 transparent;">

        <!-- Tarjeta de resumen -->
        <div class="bg-white rounded-lg border border-gray-200 p-3 shadow-sm">
          <div class="space-y-2"> 
            <div class="flex justify-between items-center text-xs">
              <span class="font-medium text-slate-600">Subtotal</span>
              <span class="font-bold text-slate-900">${{ subtotal.toLocaleString() }}</span>
            </div>
            
            <div v-if="discount > 0" class="flex justify-between items-center text-xs bg-emerald-50 text-emerald-700 p-2 rounded-lg border border-emerald-100">
              <span class="font-bold flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path></svg>
                Descuento
              </span>
              <span class="font-black">-${{ discount.toLocaleString() }}</span>
            </div>
            
            <div class="flex justify-between items-center text-xs">
              <span class="font-medium text-slate-600">
                <span v-if="displayTaxRate !== null">{{ systemSettings.iva_display_name || 'IVA' }} ({{ displayTaxRate }}%)</span>
                <span v-else>{{ systemSettings.iva_display_name || 'IVA' }}</span>
              </span>
              <span class="font-bold text-slate-900">${{ tax.toLocaleString() }}</span>
            </div>
          </div>

          <!-- Cupón -->
          <div class="mt-4 pt-3 border-t border-dashed border-slate-200">
            <button
              v-if="!showPromoCodeInput"
              @click="showPromoCodeInput = true"
              class="text-[10px] font-bold text-slate-700 hover:text-slate-900 flex items-center gap-1.5 transition-colors uppercase tracking-wide group"
            >
              <div class="w-7 h-7 bg-slate-100 rounded-lg flex items-center justify-center group-hover:bg-slate-200 transition-colors">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path></svg>
              </div>
              <span>Agregar Cupón</span>
            </button>

            <div v-else class="animate-fade-in-down">
              <div class="flex gap-2">
                <input
                  v-model="promoCode"
                  type="text"
                  placeholder="CÓDIGO"
                  class="flex-1 px-3 py-2 text-xs font-bold border border-slate-300 rounded-lg uppercase focus:ring-2 focus:ring-slate-500 focus:border-slate-500 outline-none bg-white"
                  @keyup.enter="applyPromoCode"
                />
                <button
                  @click="applyPromoCode"
                  :disabled="!promoCode.trim()"
                  class="px-3 bg-slate-900 text-white text-[10px] font-bold rounded-lg hover:bg-black disabled:opacity-50 transition-colors"
                >
                  APLICAR
                  </button>
                <button 
                  @click="showPromoCodeInput = false; promoCode = ''; promoError = ''" 
                  class="text-slate-400 hover:text-rose-500 transition-colors p-1"
                >
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
              </div>
              <p v-if="promoError" class="text-[10px] text-rose-600 mt-1.5 font-bold flex items-center gap-1">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                {{ promoError }}
              </p>
              <p v-if="discount > 0 && !promoError" class="text-[10px] text-emerald-600 mt-1.5 font-bold flex items-center gap-1">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                Cupón aplicado correctamente
              </p>
            </div>
          </div>
        </div>

        <!-- Métodos de pago -->
        <div>
          <label class="block text-[10px] font-semibold text-gray-600 uppercase tracking-wide mb-1.5">Método de Pago</label>
          <div class="grid grid-cols-2 gap-2" v-if="paymentMethods.length > 0">
             <button 
                v-for="method in paymentMethods" 
                :key="method.id"
                @click="selectedPaymentMethod = method.id"
                class="px-2.5 py-2.5 rounded-lg border text-[10px] font-semibold uppercase transition-all flex items-center justify-center gap-1.5"
                :class="selectedPaymentMethod === method.id 
                  ? 'bg-gray-900 text-white border-gray-900' 
                  : 'bg-white text-gray-600 border-gray-300 hover:border-gray-400'"
             >
                <svg v-if="method.id === 'efectivo'" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                <svg v-else class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                <span>{{ method.name }}</span>
             </button>
          </div>
          <p v-else class="text-xs text-gray-500 italic">Cargando métodos...</p>
        </div>

        <!-- Input de efectivo -->
        <div v-if="selectedPaymentMethod === 'efectivo'" class="animate-fade-in-up bg-white rounded-lg border border-gray-200 p-3 shadow-sm">
           <label class="block text-[10px] font-semibold text-gray-600 uppercase tracking-wide mb-1.5">Dinero Recibido</label>
           <div class="relative">
              <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 font-medium text-lg">$</span>
              <input
                v-model="cashReceived"
                type="number"
                step="1000"
                :min="total"
                class="w-full pl-8 pr-10 py-2 text-lg font-bold border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white placeholder-gray-300"
                :placeholder="total.toString()"
                @focus="$event.target.select()"
              />
              <button @click="showCalculatorModal" class="absolute right-2 top-1/2 -translate-y-1/2 p-1 text-gray-400 hover:text-gray-700 hover:bg-gray-100 rounded transition-colors">
                 <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
              </button>
           </div>
           
           <div v-if="cashReceived" class="mt-2 px-2.5 py-1.5 rounded-lg flex justify-between items-center transition-all"
                :class="cashReceived >= total ? 'bg-emerald-50 border border-emerald-200' : 'bg-rose-50 border border-rose-200'">
              <span class="text-[10px] font-semibold uppercase tracking-wide" :class="cashReceived >= total ? 'text-emerald-700' : 'text-rose-700'">
                 {{ cashReceived >= total ? 'Cambio' : 'Falta' }}
              </span>
              <span class="text-base font-bold" :class="cashReceived >= total ? 'text-emerald-700' : 'text-rose-700'">
                 ${{ Math.abs(cashReceived - total).toLocaleString() }}
              </span>
           </div>
        </div>

    </div>

    <!-- Footer -->
    <div class="p-3 bg-white border-t border-gray-200 flex-shrink-0">
       
       <div class="flex gap-2 mb-2">
          <button @click="holdSale" :disabEled="cart.items.length === 0" 
                  class="flex-1 py-2 text-[10px] font-semibold text-gray-600 bg-transparent border border-gray-300 rounded-lg hover:bg-gray-50 hover:text-gray-900 disabled:opacity-40 disabled:bg-transparent disabled:text-gray-400 disabled:border-gray-200 transition-all flex items-center justify-center gap-1.5">
             <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
             <span>CANCELAR</span>
          </button>
          
          <button @click="printQuote" :disabled="!canCreateQuotation" 
                  class="flex-1 py-2 text-[10px] font-semibold text-gray-600 bg-transparent border border-gray-300 rounded-lg hover:bg-gray-50 hover:text-gray-900 disabled:opacity-40 disabled:bg-transparent disabled:text-gray-400 disabled:border-gray-200 transition-all flex items-center justify-center gap-1.5">
             <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
             <span>COTIZAR</span>
          </button>
       </div>

       <!-- Botón COBRAR -->
       <button
          @click="handleCobrarClick"
          :disabled="!canShowPaymentModal || quotationMode || (selectedPaymentMethod === 'efectivo' && (!cashReceived || cashReceived < total))"
          class="w-full py-3.5 rounded-lg font-bold text-sm shadow-lg transform active:scale-[0.98] transition-all duration-200 flex items-center justify-center gap-2 group"
          :class="[
            (!canShowPaymentModal || quotationMode)
              ? 'bg-gray-100 text-gray-500 cursor-not-allowed shadow-none border border-gray-300'
          : (selectedPaymentMethod === 'efectivo' && (!cashReceived || cashReceived < total))
            ? 'bg-rose-100 text-rose-700 border-2 border-rose-400 cursor-not-allowed shadow-none'
            : 'bg-blue-600 text-white hover:bg-blue-700 shadow-blue-200/50'
          ]"
        >
          <span v-if="cart.items.length === 0" class="text-xs font-bold">
             Agrega productos
          </span>

          <span v-else-if="!selectedPaymentMethod" class="flex items-center gap-2 animate-pulse">
             <span class="text-xs font-bold uppercase tracking-wide">Selecciona Método de Pago</span>
          </span>

          <span v-else-if="selectedPaymentMethod === 'efectivo' && (!cashReceived || cashReceived < total)" class="flex items-center gap-2">
             <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
             <span class="text-sm font-bold uppercase tracking-wide">Monto Insuficiente</span>
          </span>

          <span v-else class="flex items-center justify-between w-full px-4">
             <span class="text-sm font-bold uppercase tracking-wide">COBRAR</span>
             <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
          </span>
        </button>
    </div>

  </div>
</div>
    </div>


    

    <!-- Modal de Cliente -->
    <CustomerSelectorModal
      v-if="showCustomerSelector"
      :customers="customers"
      @select="selectCustomer"
      @view-history="handleCustomerHistory"
      @close="showCustomerSelector = false"
    />

    <!-- Modal de Historial de Cliente -->
    <CustomerHistoryModal
      v-if="showCustomerHistory"
      :customer="selectedCustomerForHistory"
      @add-to-cart="handleAddToCartFromHistory"
      @close="showCustomerHistory = false"
    />

    <!-- Modal de Confirmación de Pago -->
    <ConfirmPaymentModal
      v-if="showPaymentModal"
      :total="total"
      :subtotal="subtotal"
      :tax="tax"
      :taxRate="displayTaxRate"
      :discount="discount"
      :totalItems="totalItems"
      :paymentMethod="paymentMethods.find(m => m.id === selectedPaymentMethod) || { id: 'efectivo', name: 'Efectivo' }"
      :paymentAmount="selectedPaymentMethod === 'efectivo' ? cashReceived : total"
      :change="selectedPaymentMethod === 'efectivo' ? Math.max(0, cashReceived - total) : 0"
      :customer="selectedCustomer"
      :systemSettings="systemSettings"
      :invoiceNumber="getNextInvoiceNumber()"
      @payment-confirmed="handlePaymentConfirmed"
      @close="showPaymentModal = false"
    />

    <!-- Modal Post-Pago -->
    <AfterPaymentModal
      v-if="showAfterPaymentModal"
      :saleTotal="lastSale?.total || 0"
      :sale="lastSale"
      @print-invoice="handlePrintInvoice"
      @send-whatsapp="handleSendWhatsApp"
      @view-invoice="handleViewInvoice"
      @new-sale="startNewSale"
      @close="showAfterPaymentModal = false"
    />

    <!-- Modal de Recibo -->
    <ReceiptModal
      v-if="showReceiptModal"
      :sale="lastSale"
      @close="showReceiptModal = false"
      @new-sale="startNewSale"
      @send-whatsapp="handleSendWhatsApp"
    />

    <!-- Modal de Confirmación de Cotización -->
    <div v-if="showQuotationConfirmModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white dark:bg-gray-800 rounded-xl p-6 max-w-lg w-full mx-4 shadow-2xl">
        <div class="text-center">
          <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-blue-100 dark:bg-blue-900 mb-4">
            <svg class="h-6 w-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
          </div>
          <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">Confirmar Cotización</h3>
          <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">
            ¿Está seguro que desea crear una cotización con estos datos?
          </p>
          
          <!-- Advertencia si se requiere cliente específico -->
          <div v-if="systemSettings.require_customer_quotations && (!selectedCustomer || !selectedCustomer.id)" 
               class="bg-red-50 border border-red-200 rounded-lg p-3 mb-4">
            <div class="flex items-center justify-between">
              <div class="flex items-center">
                <svg class="h-5 w-5 text-red-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.996-.833-2.464 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                </svg>
                <span class="text-sm font-medium text-red-800">Debe seleccionar un cliente específico</span>
              </div>
              <button
                @click="showQuotationConfirmModal = false; showCustomerSelector = true"
                class="px-3 py-1 bg-red-600 text-white text-xs rounded-md hover:bg-red-700 transition-colors"
              >
                Seleccionar Cliente
              </button>
            </div>
          </div>
          
          <!-- Resumen de la cotización -->
          <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 mb-6 text-left">
            <div class="flex justify-between items-center mb-2">
              <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Cliente:</span>
              <span class="text-sm text-gray-900 dark:text-white">{{ selectedCustomer?.name || 'Cliente General' }}</span>
            </div>
            <div class="flex justify-between items-center mb-2">
              <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Productos:</span>
              <span class="text-sm text-gray-900 dark:text-white">{{ cart.items.length }} artículo(s)</span>
            </div>
            <div class="flex justify-between items-center">
              <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Total:</span>
              <span class="text-sm font-semibold text-gray-900 dark:text-white">${{ total.toFixed(2) }}</span>
            </div>
          </div>
          
          <div class="flex space-x-3">
            <button
              @click="showQuotationConfirmModal = false"
              class="flex-1 bg-gray-300 dark:bg-gray-600 text-gray-700 dark:text-gray-200 px-4 py-2 rounded-lg font-medium hover:bg-gray-400 dark:hover:bg-gray-500 transition-colors"
            >
              Cancelar
            </button>
            <button
              @click="confirmQuotation"
              :disabled="systemSettings.require_customer_quotations && (!selectedCustomer || !selectedCustomer.id)"
              :class="[
                'flex-1 px-4 py-2 rounded-lg font-medium transition-colors',
                systemSettings.require_customer_quotations && (!selectedCustomer || !selectedCustomer.id)
                  ? 'bg-gray-400 text-gray-200 cursor-not-allowed'
                  : 'bg-blue-600 text-white hover:bg-blue-700'
              ]"
            >
              Crear Cotización
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal de Cotizaciones -->
    <QuotationModal
      :show="showQuotationModal"
      :type="quotationModalType"
      :quotation-code="quotationModalData.code"
      :quotation-data="quotationModalData"
      :message="quotationModalData.message"
      :unavailable-items="quotationModalData.unavailableItems"
      :valid-items="quotationModalData.validItems"
      @close="handleCloseQuotationModal"
      @load="handleLoadQuotation"
      @print="handlePrintQuotation"
      @send-whatsapp="handleSendQuotationWhatsApp"
      @load-available="handleLoadAvailableProducts"
      @add-partial-stock="handleAddPartialStock"
    />

    <!-- Modales de Control de Caja -->
    <CashOpenModal
      :show="showCashOpenModal"
      :user-info="currentUser"
      :force-open="!hasOpenSession"
      @close="handleCloseCashOpenModal"
      @success="handleOpenCashSession"
      @quotation-mode="handleQuotationMode"
    />

    <CashCloseModal
      :show="showCashCloseModal"
      :session-data="currentSession"
      @close="showCashCloseModal = false"
      @success="handleCloseCashSession"
    />

    <!-- Modal para solicitar número de WhatsApp -->
    <PhoneInputModal
      :show="showPhoneModal"
      :message="phoneModalMessage"
      @confirm="handlePhoneConfirm"
      @cancel="handlePhoneCancel"
    />

    <!-- Modal de Escáner QR -->
    <div v-if="showQRScanner" class="fixed inset-0 bg-black/80 flex items-center justify-center z-50 p-4">
      <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl max-w-md w-full">
        <!-- Header -->
        <div class="bg-blue-600 dark:bg-blue-700 p-4 rounded-t-xl">
          <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
              <div class="w-8 h-8 bg-white/20 rounded-lg flex items-center justify-center">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path>
                </svg>
              </div>
              <div>
                <h3 class="text-white font-semibold">Escanear QR de Cotización</h3>
                <p class="text-blue-100 text-sm">Apunta la cámara al código QR</p>
              </div>
            </div>
            <button @click="stopQRScanner" class="text-blue-100 hover:text-white hover:bg-white/20 rounded-lg p-2 transition-all min-w-[40px] min-h-[40px] flex items-center justify-center">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
              </svg>
            </button>
          </div>
        </div>
        
        <!-- QR Scanner Area -->
        <div class="p-6">
          <div class="relative">
            <video ref="qrVideo" class="w-full h-64 bg-black rounded-lg"></video>
            
            <!-- Overlay de escaneo -->
            <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
              <div class="w-48 h-48 border-2 border-blue-500 rounded-lg relative">
                <!-- Corners -->
                <div class="absolute top-0 left-0 w-6 h-6 border-t-4 border-l-4 border-blue-500"></div>
                <div class="absolute top-0 right-0 w-6 h-6 border-t-4 border-r-4 border-blue-500"></div>
                <div class="absolute bottom-0 left-0 w-6 h-6 border-b-4 border-l-4 border-blue-500"></div>
                <div class="absolute bottom-0 right-0 w-6 h-6 border-b-4 border-r-4 border-blue-500"></div>
              </div>
            </div>
          </div>
          
          <!-- Status -->
          <div class="mt-4 text-center">
            <p class="text-sm text-gray-600 dark:text-gray-400">
              📱 Escaneando... Muestre el código QR de la cotización
            </p>
          </div>
          
          <!-- Acciones -->
          <div class="flex justify-center mt-6">
            <button
              @click="stopQRScanner"
              class="px-6 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors"
            >
              Cancelar
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal de Calculadora POS -->
    <div v-if="showCalculator" class="fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4">
      <div class="bg-slate-900 rounded-xl shadow-2xl w-96 max-w-md border border-slate-700">
        <!-- Header -->
        <div class="bg-slate-800 border-b border-slate-700 p-4 rounded-t-xl">
          <div class="flex items-center justify-between">
            <h3 class="text-slate-200 font-semibold text-lg">Calculadora POS</h3>
            <button @click="showCalculator = false" class="text-slate-400 hover:text-slate-200 transition-colors">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
              </svg>
            </button>
          </div>
        </div>
        
        <!-- Display -->
        <div class="p-6 bg-slate-900">
          <!-- Operación anterior -->
          <div class="text-slate-400 text-sm font-mono text-right min-h-[20px] mb-1">
            {{ calculatorOperation }}
          </div>
          <!-- Display principal -->
          <div class="bg-slate-800 border border-slate-600 text-slate-100 font-mono text-right text-3xl p-4 rounded-lg min-h-[70px] flex items-center justify-end shadow-inner">
            {{ calculatorDisplay }}
          </div>
        </div>
        </div>
        
        <!-- Buttons -->
        <div class="p-6 pt-0 grid grid-cols-4 gap-3">
          <!-- Row 1 -->
          <button @click="clearCalculator" class="col-span-2 bg-red-600 hover:bg-red-700 active:bg-red-800 text-white font-semibold py-4 rounded-lg transition-colors shadow-lg">
            AC
          </button>
          <button @click="calculatorDisplay = calculatorDisplay.slice(0, -1) || '0'" class="bg-slate-600 hover:bg-slate-700 active:bg-slate-800 text-white font-semibold py-4 rounded-lg transition-colors shadow-lg">
            ⌫
          </button>
          <button @click="performCalculation('/')" class="bg-amber-600 hover:bg-amber-700 active:bg-amber-800 text-white font-semibold py-4 rounded-lg transition-colors shadow-lg">
            ÷
          </button>
          
          <!-- Row 2 -->
          <button @click="inputNumber(7)" class="bg-slate-700 hover:bg-slate-600 active:bg-slate-800 text-white font-semibold py-4 rounded-lg transition-colors shadow-lg">7</button>
          <button @click="inputNumber(8)" class="bg-slate-700 hover:bg-slate-600 active:bg-slate-800 text-white font-semibold py-4 rounded-lg transition-colors shadow-lg">8</button>
          <button @click="inputNumber(9)" class="bg-slate-700 hover:bg-slate-600 active:bg-slate-800 text-white font-semibold py-4 rounded-lg transition-colors shadow-lg">9</button>
          <button @click="performCalculation('*')" class="bg-amber-600 hover:bg-amber-700 active:bg-amber-800 text-white font-semibold py-4 rounded-lg transition-colors shadow-lg">×</button>
          
          <!-- Row 3 -->
          <button @click="inputNumber(4)" class="bg-slate-700 hover:bg-slate-600 active:bg-slate-800 text-white font-semibold py-4 rounded-lg transition-colors shadow-lg">4</button>
          <button @click="inputNumber(5)" class="bg-slate-700 hover:bg-slate-600 active:bg-slate-800 text-white font-semibold py-4 rounded-lg transition-colors shadow-lg">5</button>
          <button @click="inputNumber(6)" class="bg-slate-700 hover:bg-slate-600 active:bg-slate-800 text-white font-semibold py-4 rounded-lg transition-colors shadow-lg">6</button>
          <button @click="performCalculation('-')" class="bg-amber-600 hover:bg-amber-700 active:bg-amber-800 text-white font-semibold py-4 rounded-lg transition-colors shadow-lg">−</button>
          
          <!-- Row 4 -->
          <button @click="inputNumber(1)" class="bg-slate-700 hover:bg-slate-600 active:bg-slate-800 text-white font-semibold py-4 rounded-lg transition-colors shadow-lg">1</button>
          <button @click="inputNumber(2)" class="bg-slate-700 hover:bg-slate-600 active:bg-slate-800 text-white font-semibold py-4 rounded-lg transition-colors shadow-lg">2</button>
          <button @click="inputNumber(3)" class="bg-slate-700 hover:bg-slate-600 active:bg-slate-800 text-white font-semibold py-4 rounded-lg transition-colors shadow-lg">3</button>
          <button @click="performCalculation('+')" class="bg-amber-600 hover:bg-amber-700 active:bg-amber-800 text-white font-semibold py-4 rounded-lg transition-colors shadow-lg">+</button>
          
          <!-- Row 5 -->
          <button @click="inputNumber(0)" class="col-span-2 bg-slate-700 hover:bg-slate-600 active:bg-slate-800 text-white font-semibold py-4 rounded-lg transition-colors shadow-lg">0</button>
          <button @click="inputDecimal" class="bg-slate-700 hover:bg-slate-600 active:bg-slate-800 text-white font-semibold py-4 rounded-lg transition-colors shadow-lg">.</button>
          <button @click="performCalculation('=')" class="bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white font-semibold py-4 rounded-lg transition-colors shadow-lg">=</button>
        </div>
        
        <!-- Actions -->
        <div class="p-6 pt-0 border-t border-slate-700 flex gap-3">
          <button @click="useCalculatorResult" class="flex-1 bg-blue-600 hover:bg-blue-700 active:bg-blue-800 text-white font-semibold py-3 rounded-lg transition-colors shadow-lg">
            Usar Resultado
          </button>
          <button @click="showCalculator = false" class="flex-1 bg-slate-600 hover:bg-slate-700 active:bg-slate-800 text-white font-semibold py-3 rounded-lg transition-colors shadow-lg">
            Cancelar
          </button>
        </div>
      </div>
    </div>
  

  <!-- Modal de Error de Cotización -->
  <div v-if="showQuotationErrorModal" class="fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4">
    <div class="bg-white dark:bg-gray-900 rounded-3xl shadow-2xl max-w-md w-full transform transition-all duration-300 scale-100 border border-red-200 dark:border-red-800">
      <!-- Header -->
      <div class="bg-gradient-to-r from-red-600 via-red-500 to-red-700 dark:from-red-900 dark:via-red-800 dark:to-red-900 p-6 rounded-t-3xl border-b border-red-300 dark:border-red-700">
        <div class="flex items-center justify-between">
          <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-white/20 dark:bg-red-900 rounded-xl flex items-center justify-center">
              <svg class="w-7 h-7 text-white opacity-90" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16c-.77.833.192 2.5 1.732 2.5z" />
              </svg>
            </div>
            <div>
              <h3 class="text-2xl font-extrabold text-white tracking-tight drop-shadow">Error</h3>
              <p class="text-red-100 text-sm mt-1">Cotización no encontrada</p>
            </div>
          </div>
          <button @click="showQuotationErrorModal = false" class="text-red-100 hover:text-white transition-colors p-2 rounded-full hover:bg-red-700/30 focus:outline-none focus:ring-2 focus:ring-red-200">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>
      </div>

      <!-- Content -->
      <div class="p-8 text-center space-y-6">
        <div class="bg-red-50 dark:bg-red-950 rounded-2xl p-6 border border-red-100 dark:border-red-900">
          <svg class="w-16 h-16 text-red-500 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
          </svg>
          <h4 class="text-lg font-bold text-red-900 dark:text-red-200 mb-2">
            No se encontró la cotización
          </h4>
          <p class="text-red-700 dark:text-red-300 mb-4">
            <span class="font-mono font-bold">{{ quotationErrorCode }}</span>
          </p>
          <div class="text-sm text-red-600 dark:text-red-400 space-y-2">
            <p>La cotización que buscas puede estar:</p>
            <ul class="text-left space-y-1 mt-3">
              <li>• Ya convertida a factura</li>
              <li>• Eliminada del sistema</li>
              <li>• El código es incorrecto</li>
            </ul>
          </div>
        </div>

        <div class="flex gap-3">
          <button @click="showQuotationErrorModal = false" 
            class="flex-1 bg-gradient-to-r from-gray-500 to-gray-600 text-white px-6 py-3 rounded-2xl font-bold hover:from-gray-600 hover:to-gray-700 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-1 focus:outline-none focus:ring-2 focus:ring-gray-400">
            Entendido
          </button>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal de Devoluciones -->
  <ReturnsView
    :show="showReturnsModal"
    @close="showReturnsModal = false"
    @success="handleReturnSuccess"
  />

  <!-- 🎫 BARRA DE MULTI-TABS INFERIOR (Footer Fijo) -->
  <div class="fixed bottom-0 left-0 right-0" style="background-color: #FFFFFF; border-top: 1px solid #E2E8F0; box-shadow: 0 -1px 3px rgba(0, 0, 0, 0.04); z-index: 50; height: 44px;">
    <div class="flex items-center h-full px-1 max-w-screen-2xl mx-auto">
      
      <!-- Pestañas de ventas -->
      <div class="flex items-center gap-0.5 flex-1 overflow-x-auto scrollbar-hide">
        <div 
          v-for="tab in salesTabs" 
          :key="tab.id"
          @click="switchTab(tab.id)"
          class="group relative flex items-center gap-2 px-3 py-1.5 cursor-pointer transition-all duration-150 min-w-[110px] max-w-[170px]"
          :class="[
            activeTabId === tab.id 
              ? 'bg-white' 
              : 'bg-transparent hover:bg-gray-50'
          ]"
          :style="{
            borderTop: activeTabId === tab.id ? '2px solid #10B981' : '2px solid transparent',
            borderLeft: '1px solid #E5E7EB',
            borderRight: '1px solid #E5E7EB',
            marginBottom: '-1px'
          }"
        >
          <!-- Indicador de items en carrito -->
          <div 
            v-if="tab.cart && tab.cart.length > 0" 
            class="w-1.5 h-1.5 rounded-full flex-shrink-0"
            :class="activeTabId === tab.id ? 'bg-green-500' : 'bg-gray-400'"
            :title="`${tab.cart.length} producto(s)`"
          ></div>
          
          <!-- Nombre de la pestaña -->
          <span 
            class="text-xs font-semibold truncate flex-1"
            :class="activeTabId === tab.id ? 'text-gray-900' : 'text-gray-600'"
            :title="tab.customer?.name || tab.name"
          >
            {{ getTabDisplayName(tab) }}
          </span>
          
          <!-- Botón cerrar (solo si NO es primaria) -->
          <button
            v-if="!tab.isPrimary"
            @click.stop="closeTab(tab.id)"
            class="opacity-0 group-hover:opacity-100 p-0.5 rounded hover:bg-red-100 transition-all"
            :title="'Cerrar ' + tab.name"
          >
            <svg class="w-3 h-3 text-gray-400 hover:text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
          </button>
        </div>
        
        <!-- Botón + Nueva Venta (estilo Excel) -->
        <button
          @click="addNewTab"
          class="flex items-center justify-center w-7 h-7 rounded transition-all duration-150 flex-shrink-0 ml-0.5"
          :disabled="salesTabs.length >= 5"
          :class="salesTabs.length >= 5 ? 'opacity-30 cursor-not-allowed bg-gray-100' : 'hover:bg-gray-100 text-gray-600 hover:text-green-600'"
          :title="salesTabs.length >= 5 ? 'Máximo 5 ventas' : 'Nueva venta'"
        >
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"></path>
          </svg>
        </button>
      </div>
      
    </div>
  </div>

  <!-- Toast Simple -->
  <div v-if="showToastMessage" 
       class="fixed bottom-20 right-6 z-50 bg-green-500 text-white px-6 py-4 rounded-lg shadow-lg transform transition-all duration-500 ease-in-out"
       :class="{ 'translate-y-0 opacity-100': showToastMessage, 'translate-y-full opacity-0': !showToastMessage }">
    <div class="flex items-center space-x-3">
      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
      </svg>
      <span class="font-medium">{{ toastMessage }}</span>
    </div>
  </div>
  
</template>




<script setup>
import { ref, reactive, computed, onMounted, onBeforeUnmount, onUnmounted, watch, nextTick } from 'vue'
import { productsService } from '../services/productsService.js'
import { categoriesService } from '../services/categoriesService.js'
import { customersService } from '../services/customersService.js'
import { invoicesService } from '../services/invoicesService.js'
import jsPDF from 'jspdf'
import html2canvas from 'html2canvas'
import QRCode from 'qrcode'
import { useCashSession } from '../services/cashSessionService.js'
import { appStore } from '../store/appStore.js' // Importar store global
import axiosInstance from '../services/apiClient.js'
import { useToast } from '../composables/useToast.js'
import { useAuth } from '../store/auth.js'
import authService from '../services/authService.js'
import { whatsappService } from '../services/whatsappService.js'
import CustomerSelectorModal from './CustomerSelectorModal.vue'
import CustomerHistoryModal from './CustomerHistoryModal.vue'
import ConfirmPaymentModal from './ConfirmPaymentModal.vue'
import AfterPaymentModal from './AfterPaymentModal.vue'
import ReceiptModal from './ReceiptModal.vue'
import QuotationModal from './QuotationModal.vue'
import { toMySQLDateTime, formatInvoiceDate } from '@/utils/dateFormatter.js'
import CashOpenModal from './CashOpenModal.vue'
import CashCloseModal from './CashCloseModal.vue'
import WhatsAppStatus from './WhatsAppStatus.vue'
import PhoneInputModal from './PhoneInputModal.vue'
import ReturnsView from './ReturnsView.vue'
import QrScanner from 'qr-scanner'

// Switch para tipo de método de pago
const paymentType = ref('contado')

// Composables
const { showSuccess, showError, showWarning, showInfo } = useToast()
const { user } = useAuth()

// Obtener nombre del vendedor actual
const getCurrentSeller = () => {
  if (user.value && user.value.name) {
    return user.value.name
  }
  return 'Vendedor'
}

// Composable de cash session
const {
  currentSession: composableCurrentSession,
  hasOpenSession: composableHasOpenSession,
  isLoading: cashLoading,
  loadCurrentSession,
  checkSessionStatus,
  openSession,
  closeSession,
  canProcessSale,
  formatCurrency
} = useCashSession()

// Variables locales que se sincronizan con appStore (evitar readonly del composable)
const currentSession = ref(null)
const hasOpenSession = ref(false)

// Current user info
const currentUser = ref({
  name: 'Usuario Actual' // TODO: obtener del store de auth
})

// Loading state para WhatsApp y Email
const isLoading = ref(false)

// Estado inicial para evitar parpadeo del overlay
const initializing = ref(true)

// Toast simple local (deprecated - usar el sistema global)
const showToast = (message, type = 'success') => {
  // Mapear al sistema global
  if (type === 'error') {
    showError(message)
  } else if (type === 'warning') {
    showWarning(message)
  } else if (type === 'info') {
    showInfo(message)
  } else {
    showSuccess(message)
  }
}

// 🇨🇴 Función utilitaria para fechas locales de Colombia (GMT-5)
const getLocalColombiaDate = (date = new Date()) => {
  // Crear una nueva fecha ajustada a la zona horaria local
  const offset = date.getTimezoneOffset() * 60000 // offset en milisegundos
  const localDate = new Date(date.getTime() - offset)
  return localDate.toISOString()
}

// 🖼️ Función utilitaria para manejo inteligente de imágenes
const getProductImage = (product) => {
  // Intentar múltiples propiedades de imagen
  const imageUrl = product.image_url || product.image || product.img || product.photo
  
  // Si hay imagen, devolverla; si no, usar placeholder SVG minimalista con icono de cámara
  return imageUrl || 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjAwIiBoZWlnaHQ9IjIwMCIgdmlld0JveD0iMCAwIDIwMCAyMDAiIGZpbGw9Im5vbmUiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+CjxyZWN0IHdpZHRoPSIyMDAiIGhlaWdodD0iMjAwIiBmaWxsPSIjRkFGQUZCIi8+CjxwYXRoIGQ9Ik0xMDAgMTA1QzEwMi43NjEgMTA1IDEwNSAxMDIuNzYxIDEwNSAxMDBDMTA1IDk3LjIzODYgMTAyLjc2MSA5NSAxMDAgOTVDOTcuMjM4NiA5NSA5NSA5Ny4yMzg2IDk1IDEwMEM5NSAxMDIuNzYxIDk3LjIzODYgMTA1IDEwMCAxMDVaIiBmaWxsPSIjRTVFN0VCIi8+CjxwYXRoIGQ9Ik05MCA4MEMOOC44OTU0IDgwIDg4IDgwLjg5NTQgODggODJWMTE4Qzg4IDExOS4xMDUgODguODk1NCAxMjAgOTAgMTIwSDExMEMxMTEuMTA1IDEyMCAxMTIgMTE5LjEwNSAxMTIgMTE4VjgyQzExMiA4MC44OTU0IDExMS4xMDUgODAgMTEwIDgwSDkwWk0xMDAgODVDMTA1LjUyMyA4NSAxMTAgODkuNDc3MiAxMTAgOTVDMTEwIDEwMC41MjMgMTA1LjUyMyAxMDUgMTAwIDEwNUM5NC40NzcyIDEwNSA5MCAxMDAuNTIzIDkwIDk1QzkwIDg5LjQ3NzIgOTQuNDc3MiA4NSAxMDAgODVaIiBmaWxsPSIjRTVFN0VCIi8+Cjwvc3ZnPg=='
}

// 🔐 Función para generar identificador de usuario consistente para descuentos
const getUserIdentifier = () => {
  // Prioridad 1: Si hay cliente seleccionado, usar su ID + email
  if (selectedCustomer.value?.id) {
    return `customer_${selectedCustomer.value.id}_${selectedCustomer.value.email || 'no-email'}`
  }
  
  // Prioridad 2: Generar o recuperar un identificador de sesión persistente del navegador
  let sessionId = localStorage.getItem('pos_session_id')
  if (!sessionId) {
    // Generar nuevo ID de sesión basado en timestamp + número aleatorio
    sessionId = `browser_session_${Date.now()}_${Math.random().toString(36).substr(2, 9)}`
    localStorage.setItem('pos_session_id', sessionId)
  }
  
  return sessionId
}

// 🚨 Manejar errores de carga de imagen
const handleImageError = (event) => {
  event.target.src = 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjAwIiBoZWlnaHQ9IjIwMCIgdmlld0JveD0iMCAwIDIwMCAyMDAiIGZpbGw9Im5vbmUiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+CjxyZWN0IHdpZHRoPSIyMDAiIGhlaWdodD0iMjAwIiBmaWxsPSIjRjNGNEY2Ii8+CjxwYXRoIGQ9Ik04MCA5MEg4MC4wMUg4MFpNNzAgMTAwSDcwLjAxSDcwWk05NiA3MEw4MC41OSA4NS40MVY4NS40MUw3NiA5MEw4MCA5NEw4NCA5MEw5MCA4NEwxMDQgNzBIOTZaTTYwIDQwSDE0MEM2NC40MTgzIDQwIDY4IDQzLjU4MTcgNjggNDhWNTJIMTMyVjQ4QzEzMiA0My41ODE3IDEzNS41ODIgNDAgMTQwIDQwVjQwQzE0NC40MTggNDAgMTQ4IDQzLjU4MTcgMTQ4IDQ4VjE1MkMxNDggMTU2LjQxOCAxNDQuNDE4IDE2MCAxNDAgMTYwSDYwQzU1LjU4MTcgMTYwIDUyIDE1Ni40MTggNTIgMTUyVjQ4QzUyIDQzLjU4MTcgNTUuNTgxNyA0MCA2MCA0MFpNNjAgNTJIMTQwVjE1Mkg2MFY1MloiIGZpbGw9IiM5Q0E0QUYiLz4KPC9zdmc+'
}

// 🔄 Función para refrescar datos después de una venta
const refreshPosData = async () => {
  
  // Refrescar productos para actualizar stock
  await appStore.refresh('products')
  
  // Opcional: refrescar todo si es necesario
  // await appStore.refresh('all')
  
}

const loadDiscounts = async () => {
  try {
    const response = await axiosInstance.get('/discounts')
    if (response.data.success) {
      // Manejar estructura de paginación de Laravel
      let discountsArray = []
      // Si response.data.data es un objeto con paginación, extraer el array real
      if (response.data.data && typeof response.data.data === 'object' && response.data.data.data) {
        discountsArray = response.data.data.data // Estructura paginada: data.data.data
      } 
      // Si response.data.data es directamente un array
      else if (Array.isArray(response.data.data)) {
        discountsArray = response.data.data
      } 
      // Si response.data es directamente un array
      else if (Array.isArray(response.data)) {
        discountsArray = response.data
      } 
      else {
        discountsArray = []
      }
      discountsFromApi.value = discountsArray.filter(d => d && d.active)
    }
  } catch (error) {
    // Silenciar error en producción
  }
}

// Las funciones de carga de productos, categorías y clientes
// han sido movidas al store global para precarga después del login

// Función para refrescar datos específicos cuando sea necesario
const refreshData = (dataType = 'all') => {
  appStore.refresh(dataType)
}

// Emits
const emit = defineEmits(['sale-completed', 'create-invoice', 'search-quote', 'cart-status-changed'])

// Estado reactivo para datos de la API (usando store global)
const products = computed(() => appStore.products)
const customers = computed(() => appStore.customers)
const loading = ref(false)
const productsLoading = computed(() => appStore.loading.products)
const categoriesLoading = computed(() => appStore.loading.categories)
const error = ref(null)

// Configuraciones del sistema - usando store global
const systemSettings = computed(() => appStore.systemSettings)
const paymentMethodsFromApi = computed(() => appStore.paymentMethods)
const discountsFromApi = ref([])

// Estado reactivo del POS
const searchTerm = ref('')
const selectedCategory = ref(null)
const selectedCustomer = ref(null)
const selectedPaymentMethod = ref('efectivo') // 💰 Por defecto EFECTIVO como solicitado
const cashReceived = ref(0)
const processing = ref(false)
const searchingQuotation = ref(false) // Evitar múltiples búsquedas simultáneas

// Toast simple
const showToastMessage = ref(false)
const toastMessage = ref('')

// Variables para la calculadora POS
const showCalculator = ref(false)
const calculatorDisplay = ref('0')
const calculatorOperator = ref('')
const calculatorPreviousValue = ref('')
const calculatorWaitingForOperand = ref(false)
const calculatorOperation = ref('') // Para mostrar la operación actual

// Referencia al componente WhatsAppStatus
const whatsappStatus = ref(null)

const showCustomerSelector = ref(false)
const showCustomerHistory = ref(false)
const selectedCustomerForHistory = ref(null)
const showPaymentModal = ref(false)
const showAfterPaymentModal = ref(false)
const showReceiptModal = ref(false)

// Estados del modal de cotizaciones
const showQuotationModal = ref(false)
const quotationModalType = ref('success') // 'success' | 'found'
const quotationModalData = ref({
  code: '',
  data: null,
  message: '',
  unavailableItems: [],
  validItems: []
})

// Estados del modal de error de cotización
const showQuotationErrorModal = ref(false)
const quotationErrorCode = ref('')

// Estado del modal de confirmación de cotización
const showQuotationConfirmModal = ref(false)

// Estados de control de caja
const showCashOpenModal = ref(false)
const showCashCloseModal = ref(false)
const quotationMode = ref(false)

// Estado del modal de devoluciones
const showReturnsModal = ref(false)



// Estado del escáner QR
const showQRScanner = ref(false)

// Estados del modal de teléfono para WhatsApp
const showPhoneModal = ref(false)
const phoneModalMessage = ref('')
const phoneModalResolve = ref(null)
const qrVideo = ref(null)
const qrScanner = ref(null)

// Nueva variable para trackear cotización cargada
const loadedQuotation = ref(null)
// Variable para trackear si los productos vienen de una cotización cargada
const productsFromQuotation = ref(false)
// Array para guardar los productos originales de la cotización
const originalQuotationProducts = ref([])

// Variable para mostrar/ocultar input de código promocional
const showPromoCodeInput = ref(false)

const taxRate = computed(() => { // Usar computed del store
  const settings = appStore.systemSettings
  if (settings.iva_enabled && settings.iva_percentage) {
    return parseFloat(settings.iva_percentage)
  }
  return 19 // Valor por defecto
})

// Computed para determinar si se puede cotizar
const canCreateQuotation = computed(() => {
  // Si no hay productos, no se puede cotizar
  if (cart.items.length === 0) return false
  
  // Si no hay productos de cotización, se puede cotizar normalmente
  if (!productsFromQuotation.value || originalQuotationProducts.value.length === 0) return true
  
  // Verificar si el carrito actual es diferente a los productos originales
  const currentCartSignature = cart.items
    .map(item => `${item.id}-${item.quantity}`)
    .sort()
    .join('|')
  
  const originalSignature = originalQuotationProducts.value
    .map(item => `${item.id}-${item.quantity}`)
    .sort()
    .join('|')
  
  // Si son diferentes, se puede cotizar (hay cambios)
  return currentCartSignature !== originalSignature
})
const discount = ref(0)

// Variables para manejo de descuentos y códigos promocionales
const promoCode = ref('')
const appliedDiscount = ref(null)
const showPromoSection = ref(false)
const promoError = ref('')
// Flag para controlar si se intentó aplicar un código (solo mostrar errores tras intento)
const promoApplyAttempted = ref(false)

// Carrito de venta
const cart = reactive({
  items: []
})

// Última venta
const lastSale = ref(null)

// 🎫 SISTEMA DE MULTI-TABS para ventas simultáneas
const salesTabs = ref([
  {
    id: 'tab-main',
    name: 'Venta Principal',
    customer: null,
    cart: [],
    discount: 0,
    promoCode: '',
    appliedDiscount: null,
    notes: '',
    createdAt: new Date(),
    isPrimary: true
  }
])
const activeTabId = ref('tab-main')
const usedTabNumbers = ref([]) // Para reutilizar números

// Computed para obtener la pestaña activa
const activeTab = computed(() => {
  return salesTabs.value.find(tab => tab.id === activeTabId.value) || salesTabs.value[0]
})

// Computed para nombre de tab (cliente o "Venta X")
const getTabDisplayName = (tab) => {
  if (tab.customer && tab.customer.name) {
    return tab.customer.name.length > 12 
      ? tab.customer.name.substring(0, 12) + '...' 
      : tab.customer.name
  }
  return tab.name
}

// Métodos de pago por defecto - ELIMINADOS para evitar doble carga
// Ahora solo se mostrarán los métodos de pago reales de la API

// 🎨 Lista de iconos de categorías (95 iconos) - sincronizada con CategoriesView
const categoryIcons = [
  // General
  { id: 'shopping-bag', emoji: '🛍️' }, { id: 'gift', emoji: '🎁' }, { id: 'package', emoji: '📦' }, { id: 'money', emoji: '💰' },
  // Comida y Bebidas
  { id: 'food', emoji: '🍽️' }, { id: 'drink', emoji: '🥤' }, { id: 'coffee', emoji: '☕' }, { id: 'wine', emoji: '🍷' },
  { id: 'beer', emoji: '🍺' }, { id: 'bread', emoji: '🍞' }, { id: 'meat', emoji: '🥩' }, { id: 'fruit', emoji: '🍎' },
  { id: 'vegetable', emoji: '🥬' }, { id: 'candy', emoji: '🍬' }, { id: 'ice-cream', emoji: '🍦' }, { id: 'pizza', emoji: '🍕' },
  { id: 'burger', emoji: '🍔' }, { id: 'chicken', emoji: '🍗' }, { id: 'fish', emoji: '🐟' }, { id: 'cheese', emoji: '🧀' },
  // Belleza
  { id: 'perfume', emoji: '💐' }, { id: 'cosmetics', emoji: '💄' }, { id: 'nail', emoji: '💅' }, { id: 'haircut', emoji: '💇' }, { id: 'mirror', emoji: '🪞' },
  // Limpieza
  { id: 'soap', emoji: '🧼' }, { id: 'cleaning', emoji: '🧹' }, { id: 'toilet', emoji: '🚽' },
  // Papelería
  { id: 'book', emoji: '📚' }, { id: 'pencil', emoji: '✏️' }, { id: 'scissors', emoji: '✂️' }, { id: 'printer', emoji: '🖨️' }, { id: 'folder', emoji: '📁' },
  // Moda
  { id: 'tshirt', emoji: '👕' }, { id: 'dress', emoji: '👗' }, { id: 'jeans', emoji: '👖' }, { id: 'shoe', emoji: '👟' },
  { id: 'heels', emoji: '👠' }, { id: 'hat', emoji: '🎩' }, { id: 'watch', emoji: '⌚' }, { id: 'glasses', emoji: '👓' },
  { id: 'bag', emoji: '👜' }, { id: 'jewelry', emoji: '💍' }, { id: 'necktie', emoji: '👔' },
  // Salud
  { id: 'pill', emoji: '💊' }, { id: 'medical', emoji: '⚕️' }, { id: 'syringe', emoji: '💉' }, { id: 'thermometer', emoji: '🌡️' },
  // Niños
  { id: 'toy', emoji: '🧸' }, { id: 'baby', emoji: '👶' }, { id: 'bottle', emoji: '🍼' }, { id: 'stroller', emoji: '🍼' },
  // Tecnología
  { id: 'electronics', emoji: '📱' }, { id: 'computer', emoji: '💻' }, { id: 'camera', emoji: '📷' }, { id: 'headphones', emoji: '🎧' },
  { id: 'keyboard', emoji: '⌨️' }, { id: 'mouse', emoji: '🖱️' }, { id: 'tv', emoji: '📺' }, { id: 'game', emoji: '🎮' },
  // Ferretería
  { id: 'tools', emoji: '🔧' }, { id: 'hammer', emoji: '🔨' }, { id: 'saw', emoji: '🪚' }, { id: 'wrench', emoji: '🔩' }, { id: 'paint', emoji: '🎨' },
  // Mascotas
  { id: 'pet', emoji: '🐾' }, { id: 'dog', emoji: '🐕' }, { id: 'cat', emoji: '🐈' }, { id: 'fish-pet', emoji: '🐠' }, { id: 'bird', emoji: '🦜' },
  // Jardín
  { id: 'plant', emoji: '🌱' }, { id: 'flower', emoji: '🌸' }, { id: 'tree', emoji: '🌳' }, { id: 'garden-tools', emoji: '🌿' },
  // Deportes
  { id: 'sport', emoji: '⚽' }, { id: 'basketball', emoji: '🏀' }, { id: 'tennis', emoji: '🎾' }, { id: 'gym', emoji: '💪' }, { id: 'bike', emoji: '🚴' },
  // Automotriz
  { id: 'car', emoji: '🚗' }, { id: 'motorcycle', emoji: '🏍️' }, { id: 'tire', emoji: '🛞' }, { id: 'gas', emoji: '⛽' },
  // Hogar
  { id: 'home', emoji: '🏠' }, { id: 'furniture', emoji: '🛋️' }, { id: 'bed', emoji: '🛏️' }, { id: 'lamp', emoji: '💡' },
  { id: 'kitchen', emoji: '🍳' }, { id: 'decoration', emoji: '🖼️' }, { id: 'door', emoji: '🚪' }, { id: 'key', emoji: '🔑' }
]

// 🔍 Función helper para obtener emoji de icono de categoría
const getCategoryIcon = (iconId) => {
  if (!iconId) {
    console.warn('⚠️ Category without icon ID')
    return '🛍️'
  }
  const icon = categoryIcons.find(i => i.id === iconId)
  if (!icon) {
    console.warn(`⚠️ Icon not found for ID: "${iconId}"`)
    return '🛍️'
  }
  return icon.emoji
}

// Función para mapear iconos de métodos de pago
const mapPaymentIcon = (iconType, methodName) => {
  const name = methodName.toLowerCase()
  
  // Si hay un tipo específico, usarlo
  if (iconType) {
    switch (iconType.toLowerCase()) {
      case 'cash':
      case 'efectivo':
        return 'cash'
      case 'card':
      case 'tarjeta':
      case 'credit':
      case 'debit':
        return 'card'
      case 'bank':
      case 'transfer':
      case 'transferencia':
      case 'pse':
        return 'bank'
      case 'qr':
      case 'mobile':
      case 'movil':
        return 'qr'
      default:
        return iconType
    }
  }
  
  // Si no hay tipo, inferir del nombre
  if (name.includes('efectivo') || name.includes('cash')) return 'cash'
  if (name.includes('tarjeta') || name.includes('card') || name.includes('débito') || name.includes('crédito')) return 'card'
  if (name.includes('transfer') || name.includes('banco') || name.includes('pse')) return 'bank'
  if (name.includes('qr') || name.includes('móvil') || name.includes('movil')) return 'qr'
  
  return 'cash' // Por defecto
}

// Computed properties
const paymentMethods = computed(() => {
  if (Array.isArray(paymentMethodsFromApi.value) && paymentMethodsFromApi.value.length > 0) {
    let filtered = paymentMethodsFromApi.value.filter(method => method.active)
    
    // ACEPTAR TODOS LOS MÉTODOS ACTIVOS INDEPENDIENTEMENTE DEL TIPO
    // Si es 'contado', incluir todos los métodos disponibles (efectivo, tarjeta, transferencia, nequi)
    if (paymentType.value === 'contado') {
      // Incluir TODOS los métodos activos - no filtrar por type
      // Esto permite usar efectivo, tarjeta de crédito/débito, transferencia, nequi, etc.
      filtered = filtered
    } else if (paymentType.value === 'financiado') {
      filtered = filtered.filter(m => m.type === 'financiado')
    }
    
    return filtered.map(method => ({
      id: method.code || method.slug || method.name.toLowerCase().replace(/\s+/g, '_'),
      name: method.name,
      icon: mapPaymentIcon(method.icon, method.name),
      description: method.description,
      fee: method.fee || 0,
      requires_reference: method.requires_reference || false
    }))
  }
  return []
})

const onlyStock = ref(false)

const filteredProducts = computed(() => {
  let filtered = products.value

  // Filtrar por categoría
  if (selectedCategory.value) {
    filtered = filtered.filter(product => product.category_id === selectedCategory.value)
  }

  // Filtrar por término de búsqueda (incluye código de barras)
  if (searchTerm.value) {
    const term = searchTerm.value.toLowerCase()
    filtered = filtered.filter(product => 
      product.name.toLowerCase().includes(term) ||
      product.sku?.toLowerCase().includes(term) ||
      product.barcode?.toLowerCase().includes(term) ||
      product.category_name.toLowerCase().includes(term)
    )
  }

  // Filtrar por stock si está activo
  if (onlyStock.value) {
    filtered = filtered.filter(p => (p.stock ?? 0) > 0)
  }
  return filtered
})

// Computed de categorías: mostrar solo categorías que tengan productos visibles
const categories = computed(() => {
  const cats = appStore.categories || []
  if (!Array.isArray(cats)) return []
  // Mantener solo las categorías que tienen al menos un producto en filteredProducts
  try {
    const ids = new Set(filteredProducts.value.map(p => p.category_id))
    return cats.filter(c => ids.has(c.id))
  } catch (e) {
    return cats
  }
})

const subtotal = computed(() => {
  const total = cart.items.reduce((total, item) => total + (item.price * item.quantity), 0)
  // Recalcular descuento cuando cambie el subtotal
  if (appliedDiscount.value) {
    calculateDiscount()
  }
  return total
})

// Toggle solo stock
const toggleOnlyStock = () => {
  onlyStock.value = !onlyStock.value
}

const tax = computed(() => {
  // Usar solo la configuración del store global
  const settings = appStore.systemSettings
  
  // No calcular impuesto hasta tener configuración válida
  if (!settings || Object.keys(settings).length === 0) {
    return 0
  }
  
  // Solo aplicar IVA si está habilitado en configuración
  if (settings.iva_enabled && settings.iva_percentage) {
    const percentage = parseFloat(settings.iva_percentage)
    return Math.round((subtotal.value - discount.value) * (percentage / 100))
  }
  return 0
})

const displayTaxRate = computed(() => {
  // Usar solo la configuración del store global
  const settings = appStore.systemSettings
  
  // No mostrar ningún valor hasta que se cargue la configuración real
  if (!settings || Object.keys(settings).length === 0) {
    return null
  }
  
  // Solo mostrar si está habilitado
  if (settings.iva_enabled && settings.iva_percentage) {
    return parseFloat(settings.iva_percentage)
  }
  return null
})

const total = computed(() => {
  return subtotal.value - discount.value + tax.value
})

const totalItems = computed(() => {
  return cart.items.reduce((total, item) => total + item.quantity, 0)
})

const canProcessPayment = computed(() => {
  // Para todos los métodos de pago - siempre permitir
  // El efectivo recibido ya no es obligatorio, se puede manejar después
  return true
})

// Validación antes de mostrar modal de pago
const canShowPaymentModal = computed(() => {
  return cart.items.length > 0 && selectedPaymentMethod.value && canProcessPayment.value && paymentMethods.value.length > 0
})

// Función para generar número de factura
const getNextInvoiceNumber = () => {
  if (systemSettings.invoice_prefix) {
    const current = systemSettings.invoice_current_number || 1
    return `${systemSettings.invoice_prefix}${String(current).padStart(4, '0')}`
  }
  return `FACT-${String(Date.now()).slice(-6)}`
}

// Funciones para manejo de códigos promocionales
// Alternar la sección de promo y resetear flag de intento
const togglePromoSection = () => {
  showPromoSection.value = !showPromoSection.value
  // Resetear el intento de aplicar cuando se abre la sección
  if (!showPromoSection.value) promoApplyAttempted.value = false
}

// Mantener método de pago seleccionado después de cada venta
const resetToDefaultPaymentMethod = () => {
  const cashMethod = paymentMethods.value.find(method => 
    method.code === 'cash' || method.name?.toLowerCase().includes('efectivo')
  )
  if (cashMethod) {
    selectedPaymentMethod.value = cashMethod.id
  } else if (paymentMethods.value.length > 0) {
    selectedPaymentMethod.value = paymentMethods.value[0].id
  }
}

// Watcher para mantener consistencia en métodos de pago
watch(paymentMethods, (newMethods) => {
  if (!selectedPaymentMethod.value && newMethods.length > 0) {
    resetToDefaultPaymentMethod()
  }
}, { immediate: true })

const applyPromoCode = async () => {
  if (!promoCode.value.trim()) return

  // Marcar que el usuario intentó aplicar un código (para mostrar errores solo en ese momento)
  promoApplyAttempted.value = true

  // Validar que hay un cliente seleccionado y no es "Cliente General"
  if (!selectedCustomer.value || selectedCustomer.value.name === 'Cliente General') {
    promoError.value = 'Debes elegir primero al cliente para aplicar descuentos'
    return
  }

  promoError.value = ''
  
  try {
    // Validar código promocional usando la API del backend (incluye validación de uso por usuario)
    const baseUrl = import.meta.env.VITE_API_URL || 'http://localhost:8000'
    const apiUrl = `${baseUrl}/api/discounts/validate-code`
    console.log('🔍 URL API completa:', apiUrl)
    console.log('🔍 VITE_API_URL:', import.meta.env.VITE_API_URL)
    
    const validationResponse = await fetch(apiUrl, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json'
      },
      body: JSON.stringify({
        code: promoCode.value.trim(),
        customer_id: selectedCustomer.value?.id,
        cart_total: subtotal.value,
        customer_email: selectedCustomer.value?.email,
        customer_phone: selectedCustomer.value?.phone,
        user_identifier: getUserIdentifier() // Identificador consistente de usuario
      })
    })

    // Verificar si la respuesta es válida
    if (!validationResponse.ok) {
      try {
        const errorData = await validationResponse.json()
        console.error('❌ Error en respuesta de API:', validationResponse.status, errorData)
        
        // Mostrar el mensaje específico del backend si está disponible
        if (errorData.message) {
          promoError.value = errorData.message
        } else {
          // Mensajes específicos según el código de error HTTP
          switch (validationResponse.status) {
            case 404:
              promoError.value = 'Código de descuento no encontrado'
              break
            case 422:
              promoError.value = 'Datos de validación incorrectos'
              break
            case 429:
              promoError.value = 'Demasiados intentos, espera un momento'
              break
            default:
              promoError.value = 'Error al validar el código de descuento'
          }
        }
      } catch (parseError) {
        console.error('❌ Error al parsear respuesta de error:', parseError)
        promoError.value = 'Error al conectar con el servidor'
      }
      return
    }

    const validationResult = await validationResponse.json()

    if (!validationResult.success) {
      promoError.value = validationResult.message
      return
    }

    // Si la validación es exitosa, aplicar el descuento
    const foundDiscount = validationResult.data.discount
    const applicableAmount = validationResult.data.applicable_amount

    // Aplicar descuento con la información validada del backend
    appliedDiscount.value = {
      ...foundDiscount,
      applicable_amount: applicableAmount
    }
    
    // Usar el monto calculado por el backend
    discount.value = applicableAmount
    
    showPromoSection.value = false
    promoCode.value = ''
    // Resetear flag de intento tras aplicar correctamente
    promoApplyAttempted.value = false

    console.log('✅ Código promocional aplicado:', {
      code: foundDiscount.code,
      discount_amount: applicableAmount,
      usages_remaining: validationResult.data.usages_remaining,
      user_usage_count: validationResult.data.user_usage_count
    })
    
  } catch (error) {
    console.error('❌ Error al validar código promocional:', error)
    promoError.value = 'Error al aplicar el código promocional'
  }
}

const calculateDiscount = () => {
  if (!appliedDiscount.value) {
    discount.value = 0
    return
  }
  
  const disc = appliedDiscount.value
  if (disc.type === 'percentage') {
    discount.value = Math.round(subtotal.value * (disc.value / 100))
  } else if (disc.type === 'fixed') {
    discount.value = Math.min(disc.value, subtotal.value) // No puede ser mayor al subtotal
  }
}

const removeDiscount = () => {
  appliedDiscount.value = null
  discount.value = 0
  promoError.value = ''
  showPromoSection.value = false
  promoCode.value = ''
  promoApplyAttempted.value = false
  showPromoCodeInput.value = false
}

// Watcher para limpiar descuento cuando el carrito queda vacío
watch(() => cart.items.length, (newLength) => {
  if (newLength === 0) {
    removeDiscount()
  }
})

// Helper para obtener cantidad de producto en carrito
const getProductQuantityInCart = (productId) => {
  const item = cart.items.find(item => item.id === productId)
  return item ? item.quantity : 0
}

// Métodos
const addToCart = (product) => {
  
  const existingItem = cart.items.find(item => item.id === product.id)
  
  if (existingItem) {
    if (existingItem.quantity < product.stock) {
      existingItem.quantity += 1
    }
  } else {
    cart.items.push({
      id: product.id,
      name: product.name,
      price: product.price,
      quantity: 1,
      image: getProductImage(product),
      barcode: product.barcode,
      category: product.category,
      max_stock: product.stock
    })
  }
  
  // Emitir cambio en el estado del carrito
  emit('cart-status-changed', cart.items.length > 0)
}

const updateQuantity = (productId, newQuantity) => {
  if (newQuantity <= 0) {
    removeFromCart(productId)
    return
  }
  
  const item = cart.items.find(item => item.id === productId)
  if (item && newQuantity <= item.max_stock) {
    item.quantity = newQuantity
  }
  
  // Emitir cambio en el estado del carrito
  emit('cart-status-changed', cart.items.length > 0)
}

const removeFromCart = (productId) => {
  const index = cart.items.findIndex(item => item.id === productId)
  if (index > -1) {
    cart.items.splice(index, 1)
  }
  
  // Emitir cambio en el estado del carrito
  emit('cart-status-changed', cart.items.length > 0)
}

const clearCart = async () => {
  // Eliminar draft de la pestaña activa si existe
  if (activeTab.value && activeTab.value.cart.length > 0) {
    try {
      const response = await invoicesService.getAll()
      const draft = response.data?.find(inv => inv.tab_id === activeTab.value.id && inv.status === 'draft')
      if (draft) {
        await invoicesService.delete(draft.id)
      }
    } catch (error) {
      console.error('Error eliminando draft al limpiar carrito:', error)
    }
  }
  
  cart.items = []
  selectedCustomer.value = null
  cashReceived.value = 0
  // Limpiar descuentos aplicados
  removeDiscount()
  // Limpiar cotización cargada
  loadedQuotation.value = null
  // Resetear el flag de productos de cotización
  productsFromQuotation.value = false
  // Limpiar productos originales
  originalQuotationProducts.value = []
  // MANTENER selectedPaymentMethod.value para que persista entre ventas
  // Esto evita que el usuario tenga que reseleccionar el método cada vez
  
  // Limpiar datos de la pestaña activa
  if (activeTab.value) {
    activeTab.value.cart = []
    activeTab.value.customer = null
    activeTab.value.discount = 0
    activeTab.value.promoCode = ''
    activeTab.value.appliedDiscount = null
  }
  
  // Emitir cambio en el estado del carrito
  emit('cart-status-changed', false)
}

// 🎯 FUNCIONES DE MULTI-TABS

// Agregar nueva pestaña
const addNewTab = () => {
  if (salesTabs.value.length >= 5) {
    showWarning('Máximo 5 ventas simultáneas permitidas')
    return
  }
  
  // Buscar el número más bajo disponible
  let newNumber = 1
  while (usedTabNumbers.value.includes(newNumber)) {
    newNumber++
  }
  
  const newTab = {
    id: `tab-${newNumber}`,
    name: `Venta ${newNumber}`,
    customer: null,
    cart: [],
    discount: 0,
    promoCode: '',
    appliedDiscount: null,
    notes: '',
    createdAt: new Date(),
    isPrimary: false
  }
  
  salesTabs.value.push(newTab)
  usedTabNumbers.value.push(newNumber)
  
  // Cambiar a la nueva pestaña
  switchTab(newTab.id)
  
  showSuccess(`Nueva venta creada: ${newTab.name}`)
}

// Cambiar de pestaña
const switchTab = async (tabId) => {
  if (activeTabId.value === tabId) return
  
  // Guardar estado de la pestaña actual
  await saveCurrentTabState()
  
  // Cambiar a la nueva pestaña
  activeTabId.value = tabId
  
  // Cargar estado de la nueva pestaña
  loadTabState(tabId)
}

// Guardar estado de la pestaña actual
const saveCurrentTabState = async () => {
  const currentTab = activeTab.value
  if (!currentTab) return
  
  // Guardar datos en la pestaña
  currentTab.cart = [...cart.items]
  currentTab.customer = selectedCustomer.value
  currentTab.discount = discount.value
  currentTab.promoCode = promoCode.value
  currentTab.appliedDiscount = appliedDiscount.value
  
  // Si hay items en el carrito, guardar en BD como draft
  if (currentTab.cart.length > 0) {
    try {
      const draftData = {
        tab_id: currentTab.id,
        type: 'invoice',
        status: 'draft',
        customer_id: currentTab.customer?.id || null,
        items: JSON.stringify(currentTab.cart),
        subtotal: calculateSubtotal(currentTab.cart),
        total: calculateTotal(currentTab.cart, currentTab.discount),
        notes: currentTab.notes
      }
      
      // Buscar si ya existe un draft para este tab_id
      const response = await invoicesService.getAll()
      const existingDraft = response.data?.find(inv => inv.tab_id === currentTab.id && inv.status === 'draft')
      
      if (existingDraft) {
        // Actualizar draft existente
        await invoicesService.update(existingDraft.id, draftData)
      } else {
        // Crear nuevo draft
        await invoicesService.create(draftData)
      }
    } catch (error) {
      console.error('Error guardando draft:', error)
    }
  }
}

// Cargar estado de una pestaña
const loadTabState = (tabId) => {
  const tab = salesTabs.value.find(t => t.id === tabId)
  if (!tab) return
  
  // Restaurar datos de la pestaña
  cart.items = [...(tab.cart || [])]
  selectedCustomer.value = tab.customer
  discount.value = tab.discount || 0
  promoCode.value = tab.promoCode || ''
  appliedDiscount.value = tab.appliedDiscount
  
  // Emitir evento de cambio
  emit('cart-status-changed', cart.items.length > 0)
}

// Cerrar pestaña
const closeTab = async (tabId) => {
  const tab = salesTabs.value.find(t => t.id === tabId)
  
  // No permitir cerrar la pestaña principal
  if (tab && tab.isPrimary) {
    showWarning('No puede cerrar la Venta Principal')
    return
  }
  
  if (salesTabs.value.length === 1) {
    showWarning('Debe mantener al menos una venta abierta')
    return
  }
  
  // Si tiene items, confirmar
  if (tab && tab.cart && tab.cart.length > 0) {
    if (!confirm(`¿Cerrar "${getTabDisplayName(tab)}"? Los productos se perderán.`)) {
      return
    }
    
    // Eliminar draft de BD si existe
    try {
      const response = await invoicesService.getAll()
      const draft = response.data?.find(inv => inv.tab_id === tabId && inv.status === 'draft')
      if (draft) {
        await invoicesService.delete(draft.id)
      }
    } catch (error) {
      console.error('Error eliminando draft:', error)
    }
  }
  
  // Liberar el número para reutilización
  if (tab && !tab.isPrimary) {
    const tabNumber = parseInt(tab.id.replace('tab-', ''))
    if (!isNaN(tabNumber)) {
      const index = usedTabNumbers.value.indexOf(tabNumber)
      if (index > -1) {
        usedTabNumbers.value.splice(index, 1)
      }
    }
  }
  
  // Eliminar pestaña
  const index = salesTabs.value.findIndex(t => t.id === tabId)
  if (index > -1) {
    salesTabs.value.splice(index, 1)
  }
  
  // Si se cerró la pestaña activa, cambiar a la primera
  if (activeTabId.value === tabId) {
    activeTabId.value = salesTabs.value[0].id
    loadTabState(activeTabId.value)
  }
  
  showSuccess('Venta cerrada')
}

// Helper para calcular subtotal
const calculateSubtotal = (items) => {
  return items.reduce((sum, item) => sum + (item.price * item.quantity), 0)
}

// Helper para calcular total con descuento
const calculateTotal = (items, discountValue) => {
  const subtotal = calculateSubtotal(items)
  return subtotal - discountValue
}

// Cargar drafts existentes desde la BD
const loadSalesDrafts = async () => {
  try {
    const response = await invoicesService.getInvoices()
    const drafts = response.data?.filter(inv => inv.status === 'draft' && inv.tab_id) || []
    
    if (drafts.length > 0) {
      // Limpiar tabs por defecto
      salesTabs.value = []
      usedTabNumbers.value = []
      
      // Crear tabs desde drafts
      drafts.forEach((draft, index) => {
        const isPrimary = draft.tab_id === 'tab-main'
        let tabName = 'Venta Principal'
        let tabNumber = null
        
        if (!isPrimary) {
          tabNumber = parseInt(draft.tab_id.replace('tab-', ''))
          if (!isNaN(tabNumber)) {
            tabName = `Venta ${tabNumber}`
            usedTabNumbers.value.push(tabNumber)
          }
        }
        
        let items = []
        try {
          items = draft.items ? JSON.parse(draft.items) : []
        } catch (e) {
          console.error('Error parsing items:', e)
        }
        
        const tab = {
          id: draft.tab_id,
          name: tabName,
          customer: draft.customer || null,
          cart: items,
          discount: parseFloat(draft.total - draft.subtotal) || 0,
          promoCode: '',
          appliedDiscount: null,
          notes: draft.notes || '',
          createdAt: new Date(draft.created_at || Date.now()),
          isPrimary: isPrimary
        }
        
        salesTabs.value.push(tab)
      })
      
      // Ordenar: pestaña principal primero
      salesTabs.value.sort((a, b) => {
        if (a.isPrimary) return -1
        if (b.isPrimary) return 1
        return 0
      })
      
      // Cargar el primer tab (principal)
      if (salesTabs.value.length > 0) {
        activeTabId.value = salesTabs.value[0].id
        loadTabState(activeTabId.value)
      }
    } else {
      // Si no hay drafts, mantener la pestaña principal por defecto
      console.log('No hay drafts guardados, iniciando con Venta Principal')
    }
  } catch (error) {
    console.error('Error cargando drafts:', error)
    // En caso de error, mantener pestaña principal por defecto
  }
}

const selectCustomer = (customer) => {
  selectedCustomer.value = customer
  showCustomerSelector.value = false
  
  // Actualizar nombre de la pestaña activa con el nombre del cliente
  if (activeTab.value && customer && customer.name) {
    activeTab.value.customer = customer
  }
  
  // Si se selecciona Cliente General, limpiar descuentos aplicados
  if (!customer || customer.name === 'Cliente General') {
    removeDiscount()
  }
}

const handleCustomerHistory = (customer) => {
  selectedCustomerForHistory.value = customer
  showCustomerSelector.value = false
  showCustomerHistory.value = true
}

const handleAddToCartFromHistory = (product) => {
  // Buscar el producto en la lista para obtener datos completos
  const fullProduct = products.value.find(p => p.id === product.id)
  if (fullProduct && fullProduct.stock > 0) {
    addToCart(fullProduct)
  } else {
    // Si no está en stock o no existe, mostrar mensaje
    showError('Este producto no está disponible en este momento')
  }
}

// Nuevo flujo de pago mejorado
const handleCobrarClick = async () => {
  // VERIFICACIÓN OBLIGATORIA: Consultar base de datos antes de procesar venta
  try {
    await loadCurrentSession()
    
    // Si no hay sesión abierta después de verificar, bloquear operación
    if (!hasOpenSession.value && !quotationMode.value) {
      showError('No hay caja abierta. Debe abrir caja para procesar ventas.')
      showOpenCashModal()
      return
    }
    
    // Si está en modo cotización, mostrar mensaje para usar el botón de cotización existente
    if (quotationMode.value) {
      showWarning('Está en modo cotización. Use el botón "Cotizar" para generar cotizaciones.')
      return
    }
    
  } catch (error) {
    console.error('Error verificando sesión de caja:', error)
    showError('Error verificando estado de caja. Intente nuevamente.')
    return
  }
  
  if (!canShowPaymentModal.value) return
  showPaymentModal.value = true
}

const handlePaymentConfirmed = async (paymentData) => {
  // Cerrar modal de confirmación
  showPaymentModal.value = false
  
  try {
    // Verificar si hay una cotización cargada para convertir
    if (loadedQuotation.value) {
      // Debug logs removed for production
      
      const quotationCode = loadedQuotation.value.code || loadedQuotation.value.invoice_number
      if (!quotationCode) {
        throw new Error('No se encontró el código de cotización para convertir')
      }
      
      // Convertir cotización existente a venta
      const conversionData = {
        status: 'completed',
        payment_method: paymentData.method,
        cash_received: paymentData.amount,
        change_given: paymentData.change || 0,
        // Actualizar items si fueron modificados
        items: cart.items.map(item => ({
          product_id: item.id,
          quantity: item.quantity,
          unit_price: item.price,
          total_price: item.quantity * item.price,
          discount_amount: item.discount || 0
        })),
        // Recalcular totales
        subtotal: subtotal.value,
        tax_amount: tax.value,
        discount_amount: discount.value,
        total: total.value  // ✅ Cambiado de total_amount a total
      }
      
      // Llamar al servicio para convertir la cotización
      const result = await invoicesService.convertQuoteToSale(quotationCode, conversionData)
      
      if (result.success) {
        console.log('✅ Cotización convertida exitosamente a venta')
        console.log('📄 Datos de la nueva factura:', result.data)
        
        // Preparar datos para el recibo usando la nueva factura generada
        lastSale.value = {
          id: result.data.id || loadedQuotation.value.id,
          invoiceNumber: result.data.custom_number || result.data.number || `FV-${result.data.id}`,
          date: toMySQLDateTime(),
          due_date: toMySQLDateTime(new Date(Date.now() + 30 * 24 * 60 * 60 * 1000)),
          customer_id: selectedCustomer.value?.id || null,
          customer: selectedCustomer.value?.name || 'Cliente General',
          cashier: getCurrentSeller(),
          companyInfo: {
            name: systemSettings.company_name || 'Mi Empresa',
            document: systemSettings.company_document || '',
            address: systemSettings.company_address || '',
            phone: systemSettings.company_phone || '',
            email: systemSettings.company_email || ''
          },
          footerMessage: systemSettings.invoice_footer_message || '',
          items: [...cart.items],
          subtotal: subtotal.value,
          discount: discount.value,
          tax: tax.value,
          total: total.value,
          taxRate: displayTaxRate.value,
          payments: [{
            method: paymentData.method,
            amount: paymentData.amount
          }],
          change: paymentData.change || 0,
          convertedFromQuote: true // Flag para identificar que fue convertida
        }
      } else {
        throw new Error(result.message || 'Error al convertir cotización')
      }
      
    } else {
      // Crear nueva venta (flujo normal cuando no hay cotización cargada)
      console.log('💰 Creando nueva venta')
      
      const currentDate = new Date()
      const dueDate = new Date()
      dueDate.setDate(currentDate.getDate() + 30)
      
      lastSale.value = {
        id: Date.now(),
        invoiceNumber: 'Generando...', // Placeholder temporal hasta crear en backend
        date: toMySQLDateTime(currentDate),
        due_date: toMySQLDateTime(dueDate),
        customer_id: selectedCustomer.value?.id || 7, // Cliente General por defecto (ID 7)
        customer: selectedCustomer.value?.name || 'Cliente General',
        cashier: getCurrentSeller(),
        companyInfo: {
          name: systemSettings.company_name || 'Mi Empresa',
          document: systemSettings.company_document || '',
          address: systemSettings.company_address || '',
          phone: systemSettings.company_phone || '',
          email: systemSettings.company_email || ''
        },
        footerMessage: systemSettings.invoice_footer_message || '',
        items: [...cart.items],
        subtotal: subtotal.value,
        discount: discount.value,
        tax: tax.value,
        total: total.value,
        taxRate: displayTaxRate.value,
        payments: [{
          method: paymentData.method,
          methodName: paymentData.methodName || getPaymentMethodName(paymentData.method),
          amount: paymentData.amount || total.value
        }],
        change: paymentData.change || 0
      }
    }
  } catch (error) {
    console.error('❌ Error al procesar pago:', error)
    showError('Error al procesar el pago: ' + error.message)
    return
  }
  
  // 🔄 Refrescar datos inmediatamente después de la venta para actualizar stock
  await refreshPosData()
  
  // Procesar la venta en el backend y actualizar el número de factura
  try {
    let finalInvoiceData = null
    
    if (loadedQuotation.value) {
      // Para cotizaciones convertidas, el número ya está correcto del backend
      finalInvoiceData = lastSale.value
    } else {
      // Para ventas nuevas, crear la factura en el backend y obtener el número real
      console.log('📤 selectedPaymentMethod.value:', selectedPaymentMethod.value)
      
      const invoiceData = {
        type: 'invoice',
        customer_id: lastSale.value.customer_id || 7, // Asegurar que siempre haya un customer_id (Cliente General)
        date: lastSale.value.date.split('T')[0],
        due_date: lastSale.value.due_date.split('T')[0],
        subtotal: parseFloat(lastSale.value.subtotal),
        tax_amount: parseFloat(lastSale.value.tax),
        total: parseFloat(lastSale.value.total),
        payment_method: selectedPaymentMethod.value, // Usar directamente el código del método de pago
        notes: `Venta POS - ${lastSale.value.cashier}`,
        items: lastSale.value.items.map(item => ({
          product_id: item.id,
          product_name: item.name,
          quantity: parseInt(item.quantity),
          unit_price: parseFloat(item.price)
        })),
        // Información del descuento aplicado
        ...(appliedDiscount.value ? {
          applied_discount: {
            code: appliedDiscount.value.code,
            discount_id: appliedDiscount.value.id,
            amount: parseFloat(discount.value)
          },
          user_identifier: getUserIdentifier(), // Identificador consistente de usuario
          customer_email: lastSale.value.customer_email || null,
          customer_phone: lastSale.value.customer_phone || null
        } : {})
      }
      
      console.log('📤 selectedPaymentMethod.value:', selectedPaymentMethod.value)
      console.log('📤 payment_method enviado:', invoiceData.payment_method)
      console.log('📤 Creando factura en backend:', invoiceData)
      const result = await invoicesService.createPosInvoice(invoiceData) // Usar endpoint sin autenticación
      
      if (result && result.data) {
        // Actualizar lastSale con los datos reales del backend
        const oldNumber = lastSale.value.invoiceNumber
        lastSale.value.id = result.data.id
        lastSale.value.invoiceNumber = result.data.number || `FV-${result.data.id}`
        console.log(`✅ Número actualizado: ${oldNumber} → ${lastSale.value.invoiceNumber}`)
        
        finalInvoiceData = lastSale.value
      } else {
        throw new Error('No se pudo crear la factura en el backend')
      }
    }
    
    // Emitir el evento con los datos finales (solo para actualizar listas locales)
    emit('sale-completed', finalInvoiceData)
    
  } catch (error) {
    console.error('Error procesando venta en backend:', error)
    // Si hay error, generar número temporal como fallback
    if (lastSale.value.invoiceNumber === 'Generando...') {
      lastSale.value.invoiceNumber = getNextInvoiceNumber()
    }
    emit('sale-completed', lastSale.value)
  }
  
  // DEBUG: Verificar cliente después del pago
  console.log('🔍 Cliente después del pago:', selectedCustomer.value)
  
  // ⚡ LIMPIAR CARRITO DESPUÉS DE PROCESAR EXITOSAMENTE EN BACKEND
  // Esto garantiza que el descuento se haya enviado antes de limpiarlo
  clearCart()
  
  // Mostrar modal post-pago con opciones
  showAfterPaymentModal.value = true
}

const startNewSale = async () => {
  showReceiptModal.value = false
  showAfterPaymentModal.value = false
  
  // 🔄 Refrescar datos del POS para obtener stock actualizado
  await refreshPosData()
  
  // El carrito ya se limpió después del pago confirmado
  // Solo restablecer estado para nueva venta
  // lastSale.value = null // 🔧 Mantener lastSale para referencias futuras
  
  // 💰 Restablecer método de pago a efectivo por defecto
  const cashMethod = paymentMethods.value.find(method => 
    method.id === 'cash' || 
    method.id === 'efectivo' ||
    method.name.toLowerCase().includes('efectivo') ||
    method.name.toLowerCase().includes('cash')
  )
  
  if (cashMethod) {
    selectedPaymentMethod.value = cashMethod.id
  } else {
    selectedPaymentMethod.value = 'cash'
  }
  
  // Limpiar campos de entrada
  cashReceived.value = 0
  selectedCustomer.value = null
  
  // Mantener focus en búsqueda para agilizar siguiente venta
  setTimeout(() => {
    if (searchInput.value) {
      searchInput.value.focus()
    }
  }, 100)
  
  console.log('🆕 Nueva venta iniciada - POS listo para siguiente transacción')
}

// Handlers para el modal post-pago
const handlePrintInvoice = async () => {
  try {
    // Cerrar el modal post-pago
    showAfterPaymentModal.value = false
    
    // Abrir el modal de recibo brevemente para poder imprimir
    showReceiptModal.value = true
    
    // Esperar a que el modal se renderice
    await nextTick()
    
    // Esperar un momento más para asegurar que todo está listo
    setTimeout(() => {
      // Buscar el contenido imprimible en el modal
      const printContent = document.getElementById('receipt-content')
      if (printContent) {
        // Crear ventana de impresión
        const printWindow = window.open('', '_blank')
        printWindow.document.write(`
          <html>
            <head>
              <title>Factura ${lastSale.value?.invoice_number || 'POS'}</title>
              <style>
                body { 
                  margin: 0; 
                  padding: 10px; 
                  font-family: monospace; 
                  font-size: 12px;
                  line-height: 1.3;
                }
                @media print {
                  body { margin: 0; padding: 0; }
                  @page { size: 58mm auto; margin: 2mm; }
                }
                * { box-sizing: border-box; }
              </style>
            </head>
            <body>
              ${printContent.innerHTML}
            </body>
          </html>
        `)
        
        printWindow.document.close()
        
        // Imprimir automáticamente
        printWindow.onload = () => {
          printWindow.print()
          printWindow.close()
          
          // Cerrar el modal después de imprimir
          showReceiptModal.value = false
          showSuccess('✅ Factura enviada a impresión')
        }
      } else {
        showWarning('⚠️ No se pudo preparar la factura para impresión')
        showReceiptModal.value = false
      }
    }, 500)
    
  } catch (error) {
    console.error('Error al imprimir factura:', error)
    showError('❌ Error al imprimir la factura')
    showReceiptModal.value = false
  }
}

const handleSendEmail = async () => {
  // Funcionalidad de envío por correo
  try {
    showAfterPaymentModal.value = false
    isLoading.value = true
    
    // Obtener email del cliente o solicitar uno
    let email = selectedCustomer.value?.email
    
    if (!email || !isValidEmail(email)) {
      email = await promptForEmail('Ingrese el email para enviar la factura:')
      if (!email) {
        showWarning('Envío cancelado')
        isLoading.value = false
        return
      }
    }
    
    showInfo('Enviando factura por correo...')
    
    // Generar PDF y enviar por email
    const pdfBlob = await generateInvoicePDF()
    await sendEmailWithPDF(email, pdfBlob)
    
    showSuccess(`Factura enviada a: ${email}`)
    
  } catch (error) {
    console.error('Error enviando email:', error)
    showError(error.message || 'Error enviando correo electrónico')
  } finally {
    isLoading.value = false
  }
}

const handleSendWhatsApp = async () => {
  try {
    // Verificar estado de WhatsApp antes de proceder
    if (!whatsappStatus.value?.isConnected) {
      showError('WhatsApp no está conectado. Haga clic en el indicador de WhatsApp para conectar.')
      whatsappStatus.value?.openModal()
      return
    }
    
    // Mostrar indicador de carga
    isLoading.value = true
    
    // Buscar información del cliente desde lastSale si selectedCustomer está vacío
    let customerInfo = selectedCustomer.value
    
    if ((!customerInfo || customerInfo.id === 7) && lastSale.value?.customer_id) {
      try {
        const customerResponse = await fetch(`http://localhost:8000/api/customers/${lastSale.value.customer_id}`, {
          headers: {
            'Authorization': `Bearer ${authService.getToken()}`
          }
        })
        if (customerResponse.ok) {
          const result = await customerResponse.json()
          customerInfo = result.data
        }
      } catch (error) {
        console.warn('Error buscando cliente:', error)
      }
    }
    
    let phoneNumber = null
    let cleanPhone = null
    
    if (customerInfo && customerInfo.id !== 7) {
      // Cliente registrado (no es Cliente General) - intentar usar su teléfono
      phoneNumber = customerInfo.phone
      
      if (phoneNumber && phoneNumber.trim() !== '') {
        // Validar el teléfono registrado
        cleanPhone = cleanPhoneNumber(phoneNumber)
        
        if (isValidPhoneNumber(cleanPhone)) {
          // Teléfono válido - usar automáticamente (sin alerta)
          console.log(`📱 Enviando a: ${customerInfo.name} (${cleanPhone})`)
        } else {
          // Teléfono inválido - solicitar uno nuevo
          phoneNumber = await promptForPhoneNumber(`El teléfono registrado del cliente "${customerInfo.name}" (${phoneNumber}) no es válido. Ingrese un número correcto:`)
          if (!phoneNumber) {
            showError('Envío cancelado: No se proporcionó número de teléfono')
            return
          }
          cleanPhone = cleanPhoneNumber(phoneNumber)
          if (!isValidPhoneNumber(cleanPhone)) {
            showError('Número de teléfono inválido. Use formato: +57 300 1234567 o 3001234567')
            return
          }
        }
      } else {
        // No tiene teléfono registrado - solicitarlo
        phoneNumber = await promptForPhoneNumber(`El cliente "${customerInfo.name}" no tiene teléfono registrado. Ingrese el número:`)
        if (!phoneNumber) {
          showError('Envío cancelado: No se proporcionó número de teléfono')
          return
        }
        cleanPhone = cleanPhoneNumber(phoneNumber)
        if (!isValidPhoneNumber(cleanPhone)) {
          showError('Número de teléfono inválido. Use formato: +57 300 1234567 o 3001234567')
          return
        }
      }
    } else {
      // Cliente general (ID 7) o sin cliente - solicitar teléfono
      phoneNumber = await promptForPhoneNumber('Ingrese el número de WhatsApp del cliente:')
      if (!phoneNumber) {
        showError('Envío cancelado: No se proporcionó número de teléfono')
        return
      }
      cleanPhone = cleanPhoneNumber(phoneNumber)
      if (!isValidPhoneNumber(cleanPhone)) {
        showError('Número de teléfono inválido. Use formato: +57 300 1234567 o 3001234567')
        return
      }
    }
    
    // Generar PDF de la factura exactamente como se muestra en pantalla
    console.log('📄 Generando PDF de la factura...')
    const pdfBlob = await generateInvoicePDF()
    
    // Enviar por WhatsApp usando API
    console.log('📱 Enviando por WhatsApp...')
    await sendWhatsAppMessage(cleanPhone, pdfBlob)
    
    showSuccess(`📱 Factura enviada por WhatsApp a ${cleanPhone}`)
    
  } catch (error) {
    console.error('Error enviando por WhatsApp:', error)
    showError(error.message || 'Error al enviar por WhatsApp. Verifique el número e intente nuevamente.')
  } finally {
    isLoading.value = false
  }
}

const handleViewInvoice = () => {
  // Mostrar modal de recibo
  showAfterPaymentModal.value = false
  showReceiptModal.value = true
}

// FUNCIONES DE WHATSAPP Y PDF
// ========================================

/**
 * Solicitar número de teléfono al usuario mediante modal
 */
const promptForPhoneNumber = (message) => {
  return new Promise((resolve) => {
    phoneModalMessage.value = message
    phoneModalResolve.value = resolve
    showPhoneModal.value = true
  })
}

/**
 * Manejar confirmación del modal de teléfono
 */
const handlePhoneConfirm = (phone) => {
  showPhoneModal.value = false
  if (phoneModalResolve.value) {
    phoneModalResolve.value(phone)
    phoneModalResolve.value = null
  }
}

/**
 * Manejar cancelación del modal de teléfono
 */
const handlePhoneCancel = () => {
  showPhoneModal.value = false
  if (phoneModalResolve.value) {
    phoneModalResolve.value(null)
    phoneModalResolve.value = null
  }
}

/**
 * Limpiar y formatear número de teléfono
 */
const cleanPhoneNumber = (phone) => {
  if (!phone) return ''
  
  // Remover todos los espacios, guiones y paréntesis
  let clean = phone.replace(/[\s\-\(\)]/g, '')
  
  // Si no empieza con +57, agregarlo si es número colombiano
  if (!clean.startsWith('+')) {
    if (clean.startsWith('57')) {
      clean = '+' + clean
    } else if (clean.length === 10 && clean.startsWith('3')) {
      clean = '+57' + clean
    }
  }
  
  return clean
}

/**
 * Validar formato de número de teléfono colombiano
 */
const isValidPhoneNumber = (phone) => {
  // Formato: +573001234567 (13 dígitos total)
  const regex = /^\+57[3][0-9]{9}$/
  return regex.test(phone)
}

/**
 * Generar PDF capturando exactamente la factura que está en pantalla
 */
const generateInvoicePDF = async () => {
  try {
    if (!lastSale.value) {
      throw new Error('No hay datos de venta para generar el PDF')
    }

    console.log('Capturando factura de pantalla...')

    // Mostrar el recibo temporalmente si no está visible
    const wasReceiptVisible = showReceiptModal.value
    if (!wasReceiptVisible) {
      showReceiptModal.value = true
      await nextTick()
      await new Promise(resolve => setTimeout(resolve, 500)) // Esperar renderizado
    }

    // Obtener el elemento del recibo visible
    const receiptElement = document.getElementById('receipt-content')
    if (!receiptElement) {
      throw new Error('No se encontró el recibo en pantalla')
    }

    // Crear canvas del recibo usando html2canvas
    const canvas = await html2canvas(receiptElement, {
      scale: 2,
      useCORS: true,
      backgroundColor: '#ffffff',
      width: receiptElement.scrollWidth,
      height: receiptElement.scrollHeight,
      logging: false
    })

    // Crear PDF con jsPDF
    const imgWidth = 80 // Ancho del ticket en mm
    const imgHeight = (canvas.height * imgWidth) / canvas.width

    const pdf = new jsPDF({
      orientation: 'portrait',
      unit: 'mm',
      format: [imgWidth, Math.max(imgHeight, 100)]
    })

    const imgData = canvas.toDataURL('image/png', 1.0)
    pdf.addImage(imgData, 'PNG', 0, 0, imgWidth, imgHeight, '', 'FAST')

    // Ocultar el recibo si no estaba visible antes
    if (!wasReceiptVisible) {
      showReceiptModal.value = false
    }

    // Convertir a blob
    return pdf.output('blob')

  } catch (error) {
    console.error('Error generando PDF:', error)
    throw new Error('No se pudo generar el PDF de la factura: ' + error.message)
  }
}

/**
 * Enviar mensaje por WhatsApp con PDF adjunto
 */
const sendWhatsAppMessage = async (phoneNumber, pdfBlob) => {
  try {
    // Crear un mensaje más detallado y profesional
    const companyName = systemSettings.company_name || 'Mi Empresa'
    const customerName = lastSale.value.customer_name || selectedCustomer.value?.name || 'Cliente'
    const invoiceNumber = lastSale.value.invoiceNumber || 'N/A'
    const totalAmount = (lastSale.value.total || total.value).toLocaleString()
    const saleDate = new Date().toLocaleDateString('es-CO', {
      year: 'numeric',
      month: '2-digit', 
      day: '2-digit'
    })

    const message = `🧾 *${companyName}*

¡Hola ${customerName}!

Gracias por tu compra. Aquí tienes los detalles de tu factura:

📋 *Factura:* ${invoiceNumber}
📅 *Fecha:* ${saleDate}
💰 *Total:* $${totalAmount}

📄 Adjunto encontrarás el comprobante de tu compra en formato PDF.

¡Esperamos verte pronto! 😊

_Conserva este recibo para cualquier devolución o garantía._`

    const fileName = `factura_${lastSale.value.invoiceNumber || 'temp'}.pdf`
    
    // Usar el nuevo método para enviar PDF generado
    const result = await whatsappService.sendInvoiceWithPDF(phoneNumber, pdfBlob, message, fileName, customerName)
    
    if (!result.success) {
      // Manejar errores específicos
      if (result.error === 'INVALID_PHONE') {
        throw new Error(`Número inválido: ${phoneNumber}. Verifique que esté bien escrito.`)
      } else if (result.error === 'WHATSAPP_API_ERROR') {
        throw new Error(`Error del servicio WhatsApp: ${result.message}`)
      } else if (result.error === 'PHONE_NOT_REGISTERED') {
        throw new Error(`El número ${phoneNumber} no tiene WhatsApp o no puede recibir mensajes.`)
      } else if (result.error === 'WHATSAPP_NOT_CONNECTED') {
        throw new Error('WhatsApp no está conectado. Configure la conexión primero.')
      }
      
      throw new Error(result.message || 'Error enviando mensaje')
    }
    
    return result
    
  } catch (error) {
    console.error('Error en sendWhatsAppMessage:', error)
    throw error
  }
}

// ========================================
// FUNCIONES DE EMAIL
// ========================================

/**
 * Solicitar email al usuario mediante prompt
 */
const promptForEmail = (message) => {
  return new Promise((resolve) => {
    const email = prompt(message + '\n\nEjemplo: cliente@correo.com')
    resolve(email?.trim() || null)
  })
}

/**
 * Validar formato de email
 */
const isValidEmail = (email) => {
  const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/
  return regex.test(email)
}

/**
 * Enviar email con PDF adjunto
 */
const sendEmailWithPDF = async (email, pdfBlob) => {
  try {
    // Preparar FormData con el archivo PDF
    const formData = new FormData()
    formData.append('email', email)
    formData.append('pdf', pdfBlob, `factura_${lastSale.value.invoiceNumber || 'temp'}.pdf`)
    formData.append('subject', `Factura ${lastSale.value.invoiceNumber || 'N/A'} - ${systemSettings.company_name || 'Mi Empresa'}`)
    formData.append('message', `Estimado cliente,

Adjunto encontrará su factura de compra.

Factura: ${lastSale.value.invoiceNumber || 'N/A'}
Fecha: ${new Date().toLocaleDateString()}
Total: $${(lastSale.value.total || total.value).toLocaleString()}

Gracias por confiar en nosotros.

Saludos cordiales,
${systemSettings.company_name || 'Mi Empresa'}`)
    
    // Enviar al endpoint de email
    const response = await fetch('http://localhost:8000/api/email/send-invoice', {
      method: 'POST',
      headers: {
        'Authorization': `Bearer ${authService.getToken()}`
      },
      body: formData
    })
    
    const result = await response.json()
    
    if (!response.ok || !result.success) {
      // Manejar errores específicos
      if (result.error === 'INVALID_EMAIL') {
        throw new Error(`Email inválido: ${email}. Verifique que esté bien escrito.`)
      } else if (result.error === 'SMTP_ERROR') {
        throw new Error(`Error del servidor de correo: ${result.message}`)
      } else if (result.error === 'EMAIL_REJECTED') {
        throw new Error(`El email ${email} fue rechazado. Verifique la dirección.`)
      }
      
      throw new Error(result.message || 'Error enviando email')
    }
    
    return result
    
  } catch (error) {
    console.error('Error en sendEmailWithPDF:', error)
    throw error
  }
}

const getPaymentMethodName = (methodId) => {
  const method = paymentMethods.value.find(m => m.id === methodId)
  return method ? method.name : 'Efectivo'
}

const holdSale = () => {
  // Implementar lógica para suspender venta
  const heldSales = JSON.parse(localStorage.getItem('heldSales') || '[]')
  heldSales.push({
    id: Date.now(),
    items: [...cart.items],
    customer: selectedCustomer.value,
    date: toMySQLDateTime()
  })
  localStorage.setItem('heldSales', JSON.stringify(heldSales))
  
  clearCart()
  showToast('Venta suspendida correctamente', 'success')
}

// Función para mostrar calculadora del POS
const showCalculatorModal = () => {
  showCalculator.value = true
  calculatorDisplay.value = '0'
  calculatorOperator.value = ''
  calculatorPreviousValue.value = ''
  calculatorWaitingForOperand.value = false
  calculatorOperation.value = ''
}

// Funciones de la calculadora
const inputNumber = (num) => {
  if (calculatorWaitingForOperand.value) {
    calculatorDisplay.value = String(num)
    calculatorWaitingForOperand.value = false
  } else {
    calculatorDisplay.value = calculatorDisplay.value === '0' ? String(num) : calculatorDisplay.value + num
  }
}

const inputDecimal = () => {
  if (calculatorWaitingForOperand.value) {
    calculatorDisplay.value = '0.'
    calculatorWaitingForOperand.value = false
  } else if (calculatorDisplay.value.indexOf('.') === -1) {
    calculatorDisplay.value += '.'
  }
}

const clearCalculator = () => {
  calculatorDisplay.value = '0'
  calculatorOperator.value = ''
  calculatorPreviousValue.value = ''
  calculatorWaitingForOperand.value = false
  calculatorOperation.value = ''
}

const performCalculation = (nextOperator) => {
  const inputValue = parseFloat(calculatorDisplay.value)

  if (calculatorPreviousValue.value === '') {
    calculatorPreviousValue.value = inputValue
  } else if (calculatorOperator.value) {
    const currentValue = calculatorPreviousValue.value || 0
    const operatorSymbols = {
      '+': '+',
      '-': '−',
      '*': '×',
      '/': '÷'
    }
    
    // Mostrar la operación anterior
    calculatorOperation.value = `${currentValue} ${operatorSymbols[calculatorOperator.value] || calculatorOperator.value} ${inputValue} =`
    
    const newValue = {
      '+': (prev, next) => prev + next,
      '-': (prev, next) => prev - next,
      '*': (prev, next) => prev * next,
      '/': (prev, next) => prev / next,
      '=': (prev, next) => next
    }[calculatorOperator.value](currentValue, inputValue)

    calculatorDisplay.value = String(newValue)
    calculatorPreviousValue.value = newValue
  }

  calculatorWaitingForOperand.value = true
  calculatorOperator.value = nextOperator
  
  // Si no es igual, actualizar la operación actual
  if (nextOperator !== '=') {
    const operatorSymbols = {
      '+': '+',
      '-': '−',
      '*': '×',
      '/': '÷'
    }
    calculatorOperation.value = `${calculatorDisplay.value} ${operatorSymbols[nextOperator] || nextOperator}`
  }
}

const useCalculatorResult = () => {
  const result = parseFloat(calculatorDisplay.value)
  if (!isNaN(result)) {
    cashReceived.value = result
  }
  showCalculator.value = false
}

const printQuote = () => {
  // Validar que hay productos en el carrito
  if (cart.items.length === 0) {
    alert('No hay productos en el carrito para cotizar')
    return
  }
  
  // Mostrar modal de confirmación
  showQuotationConfirmModal.value = true
}

const confirmQuotation = async () => {
  try {
    // Validar si se requiere cliente específico para cotizaciones
    if (systemSettings.require_customer_quotations && (!selectedCustomer.value || !selectedCustomer.value.id)) {
      alert('⚠️ La configuración del sistema requiere seleccionar un cliente específico para crear cotizaciones.\n\nNo se puede usar "Cliente General".')
      showQuotationConfirmModal.value = false
      return
    }
    
    // Preparar items mapeados con product_name requerido
    const mappedItems = cart.items.map(item => ({
      product_id: item.id,
      product_name: item.name, // Campo requerido por el backend
      quantity: item.quantity,
      unit_price: item.price,
      total_price: item.price * item.quantity
    }))
    
    console.log('📦 Items mapeados:', mappedItems)
    
    // Preparar datos de la cotización
    const quoteData = {
      type: 'quote', // Campo requerido
      customer_id: selectedCustomer.value?.id || 7, // Usar cliente por defecto ID=7 (Cliente General) si no hay seleccionado
      date: new Date().toISOString().split('T')[0], // Campo requerido formato YYYY-MM-DD
      subtotal: subtotal.value,
      tax_amount: tax.value,
      total: total.value,
      notes: `Cliente: ${selectedCustomer.value?.name || 'Cliente General'} - Cotización generada desde POS`,
      items: mappedItems
    }

    console.log('💾 Guardando cotización en BD...', quoteData)
    
    // Crear la cotización usando el servicio directamente
    const result = await invoicesService.createQuote(quoteData)
    
    if (result.success && result.data) {
      // Usar el código real del backend
      const realQuoteCode = result.data.number
      
      // Preparar datos para el modal de cotización exitosa
      const modalQuotationData = {
        id: result.data.id, // ID de la cotización para WhatsApp
        code: realQuoteCode, // Código real del backend (ej: COT-000007)
        customer: selectedCustomer.value || { name: 'Cliente General' }, // Objeto completo del cliente
        total: total.value,
        items: cart.items.map(item => ({
          name: item.name,
          quantity: item.quantity,
          price: item.price,
          subtotal: item.price * item.quantity
        })),
        message: 'El cliente puede usar este código para realizar la compra posteriormente.',
        unavailableItems: []
      }
      
      // Cerrar modal de confirmación
      showQuotationConfirmModal.value = false
      
      // Limpiar carrito
      clearCart()
      
      // Mostrar modal de cotización exitosa
      quotationModalType.value = 'success'
      quotationModalData.value = modalQuotationData
      showQuotationModal.value = true
      
      console.log(`✅ Cotización ${realQuoteCode} creada exitosamente`)
      
    } else {
      throw new Error(result.message || 'Error al crear cotización')
    }
    
  } catch (error) {
    console.error('❌ Error al crear cotización:', error)
    alert('Error al crear la cotización. Inténtelo de nuevo.')
    showQuotationConfirmModal.value = false
  }
}

const printReceipt = () => {
  // Implementar lógica de impresión de factura (no cotización)
  window.print()
}

const printQuotation = () => {
  // Imprimir cotización formal desde los datos del modal
  const data = quotationModalData.value
  const win = window.open('', '', 'width=800,height=900')
  if (!win) return
  win.document.write(`
    <html>
    <head>
      <title>Cotización ${data.code}</title>
      <style>
        body { font-family: Arial, sans-serif; margin: 40px; }
        h2 { color: #2563eb; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #e5e7eb; padding: 8px; text-align: left; }
        th { background: #f1f5f9; }
        .total { font-weight: bold; color: #059669; }
      </style>
    </head>
    <body>
      <h2>Cotización</h2>
      <p><strong>Código:</strong> ${data.code}</p>
      <p><strong>Cliente:</strong> ${data.customer || 'Cliente General'}</p>
      <table>
        <thead>
          <tr><th>Producto</th><th>Cantidad</th><th>Precio Unitario</th><th>Subtotal</th></tr>
        </thead>
        <tbody>
          ${(data.items || []).map(item => `
            <tr>
              <td>${item.name}</td>
              <td>${item.quantity}</td>
              <td>$${item.price.toLocaleString()}</td>
              <td>$${item.subtotal.toLocaleString()}</td>
            </tr>
          `).join('')}
        </tbody>
      </table>
      <p class="total">Total: $${(data.total || 0).toLocaleString()}</p>
      <p style="margin-top:30px; color:#64748b;">${data.message || ''}</p>
    </body>
    </html>
  `)
  win.document.close()
  win.focus()
  setTimeout(() => win.print(), 300)
}

const closeSaleModal = () => {
  showSaleConfirmation.value = false
  lastSale.value = null
}

// Referencias para el DOM
const searchInput = ref(null)

// Nuevas funciones para mejorar la búsqueda
const clearSearch = () => {
  searchTerm.value = ''
  selectedCategory.value = null
  if (searchInput.value) {
    searchInput.value.focus()
  }
}

// Función para limpiar todos los filtros
const clearFilters = () => {
  searchTerm.value = ''
  selectedCategory.value = null
  onlyStock.value = false
  if (searchInput.value) {
    searchInput.value.focus()
  }
}

const focusFirstProduct = () => {
  if (filteredProducts.value.length > 0) {
    addToCart(filteredProducts.value[0])
    clearSearch()
  }
}

// Búsqueda mejorada con código de barras y cotizaciones
const handleSearchEnter = async () => {
  const search = searchTerm.value.trim()
  
  // 1. Verificar si es un código de cotización (formato COT-xxxxxx)
  if (search.startsWith('COT-')) {
    // Evitar búsquedas simultáneas
    if (searchingQuotation.value) {
      console.log('⏸️ Búsqueda en progreso, ignorando nueva solicitud')
      return
    }
    
    searchingQuotation.value = true
    
    try {
      console.log(`🔍 Buscando cotización: ${search}`)
      
      // Llamar al servicio de cotizaciones
      console.log(`� [PosView] Llamando directamente a invoicesService.searchQuote`)
      const result = await invoicesService.searchQuote(search)

      
      if (result.success && result.data && result.data.length > 0) {
        console.log(`✅ [PosView] Datos encontrados, buscando cotización específica...`)
        
        // Encontrar la cotización específica por código
        const quote = result.data.find(invoice => 
          invoice.invoice_number === search && invoice.status === 'quotation'
        )
        
        console.log(`🎯 [PosView] Cotización filtrada:`, quote)
        
        if (quote) {
          console.log(`✅ [PosView] Cotización encontrada exitosamente`)
          console.log('📄 Cotización encontrada, validando disponibilidad de productos...')
        
        // Validar disponibilidad de productos antes de mostrar el modal
        const itemsToLoad = quote.sale_items || quote.saleItems || quote.items || []
        const validItems = []
        const unavailableItems = []
        
        for (const item of itemsToLoad) {
          console.log(`🔍 Validando item:`, item)
          
          // Obtener product_id de diferentes fuentes posibles (compatibilidad con ambas tablas)
          const productId = item.product_id || item.product?.id || null
          
          console.log(`🔍 BÚSQUEDA DE PRODUCTO:`)
          console.log(`   - item.product_id: ${item.product_id}`)
          console.log(`   - item.product?.id: ${item.product?.id}`)
          console.log(`   - productId final: ${productId}`)
          
          if (!productId) {
            console.log(`❌ NO SE ENCONTRÓ product_id en el item`)
            unavailableItems.push({
              name: item.product_name || item.name || 'Producto desconocido',
              requestedQuantity: item.quantity,
              issue: 'not_found',
              message: 'ID de producto no encontrado en cotización'
            })
            continue
          }
          
          // Buscar producto en el store global para verificar stock y existencia
          const product = appStore.products.find(p => p.id === productId)
          
          if (!product) {
            // Producto no existe
            console.log(`❌ PRODUCTO NO ENCONTRADO (handleSearchEnter):`)
            console.log(`   - productId buscado: ${productId}`)
            console.log(`   - Productos disponibles: ${appStore.products.length}`)
            
            unavailableItems.push({
              name: item.product_name || item.product?.name || `Producto ID ${productId}`,
              requestedQuantity: item.quantity,
              issue: 'not_found',
              message: 'Producto no encontrado en el catálogo'
            })
            console.log(`❌ Producto no encontrado: ID ${productId}`)
          } else if (product.manage_stock && product.stock < item.quantity) {
            // Producto existe pero sin stock suficiente
            console.log(`🚨 VALIDACIÓN STOCK CRÍTICA (handleSearchEnter):`)
            console.log(`   - Producto: ${product.name}`)
            console.log(`   - Stock actual: ${product.stock}`)
            console.log(`   - Cantidad solicitada: ${item.quantity}`)
            console.log(`   - manage_stock: ${product.manage_stock}`)
            console.log(`   - Condición evaluada: ${product.manage_stock && product.stock < item.quantity}`)
            
            unavailableItems.push({
              name: product.name,
              quantity: item.quantity,
              availableQuantity: product.stock,
              issue: 'insufficient_stock',
              message: `Stock insuficiente. Disponible: ${product.stock}, Solicitado: ${item.quantity}`
            })
            console.log(`⚠️ Stock insuficiente: ${product.name} - Disponible: ${product.stock}, Solicitado: ${item.quantity}`)
            
            // NUEVO: Si hay stock disponible (mayor a 0), agregarlo a validItems con la cantidad disponible
            if (product.stock > 0) {
              const cartItem = {
                id: productId,
                name: product.name,
                price: parseFloat(item.unit_price),
                quantity: product.stock, // Usar la cantidad disponible en lugar de la solicitada
                discount: parseFloat(item.discount_amount || item.discount || 0),
                category: product.category || 'General',
                currentStock: product.stock || 0,
                manageStock: product.manage_stock || false
              }
              
              validItems.push(cartItem)
              console.log(`✅ Producto con stock parcial agregado a validItems: ${product.name} - Cantidad disponible: ${product.stock}`)
            }
          } else {
            // Producto válido
            console.log(`✅ PRODUCTO VÁLIDO (handleSearchEnter):`)
            console.log(`   - Producto: ${product.name}`)
            console.log(`   - Stock actual: ${product.stock}`)
            console.log(`   - Cantidad solicitada: ${item.quantity}`)
            console.log(`   - manage_stock: ${product.manage_stock}`)
            
            const cartItem = {
              id: productId,
              name: product.name,
              price: parseFloat(item.unit_price),
              quantity: parseInt(item.quantity),
              discount: parseFloat(item.discount_amount || item.discount || 0),
              category: product.category || 'General',
              currentStock: product.current_stock || 0,
              manageStock: product.manage_stock || false
            }
            
            validItems.push(cartItem)
            console.log(`✅ Producto válido agregado: ${product.name} - Cantidad: ${item.quantity}`)
          }
        }
        
        // Configurar modal según los resultados de validación
        if (unavailableItems.length > 0) {
          // Hay productos con problemas
          quotationModalType.value = 'availability_issues'
          quotationModalData.value = {
            code: quote.invoice_number,
            customer: quote.customer_name || (typeof quote.customer === 'object' ? quote.customer?.name : quote.customer) || 'Cliente General',
            total: quote.total || quote.total_amount || 0,
            items: validItems.map(item => ({
              name: item.name,
              quantity: item.quantity,
              price: item.price,
              subtotal: item.price * item.quantity
            })),
            validItems: validItems,
            unavailableItems: unavailableItems,
            data: quote,
            message: `${unavailableItems.length} producto(s) tienen problemas de disponibilidad. ¿Desea continuar con los productos disponibles?`
          }
        } else {
          // Todos los productos están disponibles
          quotationModalType.value = 'found'
          quotationModalData.value = {
            code: quote.invoice_number,
            customer: quote.customer_name || (typeof quote.customer === 'object' ? quote.customer?.name : quote.customer) || 'Cliente General',
            total: quote.total || quote.total_amount || 0,
            items: validItems.map(item => ({
              name: item.name,
              quantity: item.quantity,
              price: item.price,
              subtotal: item.price * item.quantity
            })),
            validItems: validItems,
            unavailableItems: [],
            data: quote,
            message: '¿Desea cargar esta cotización al carrito?',
            quotationToLoad: quote
          }
        }
        
        showQuotationModal.value = true
        clearSearch()
        return
        } else {
          console.log('❌ [PosView] Cotización no encontrada o no es una cotización activa')
          quotationErrorCode.value = search
          showQuotationErrorModal.value = true
          clearSearch()
          return
        }
      } else {
        // Cotización no encontrada - mostrar modal sin logs innecesarios
        quotationErrorCode.value = search
        showQuotationErrorModal.value = true
        clearSearch()
        return
      }
    } catch (error) {
      // Solo loggear errores técnicos, no cuando simplemente no se encuentra
      if (error.message !== 'Cotización no encontrada') {
        console.error('❌ [PosView] Error técnico al buscar cotización:', error)
      }
      quotationErrorCode.value = search
      showQuotationErrorModal.value = true
      clearSearch()
      return
    } finally {
      searchingQuotation.value = false
    }
  }
  
  // 2. Búsqueda normal de productos (código de barras, SKU, etc.)
  const exactMatch = products.value.find(product => 
    product.barcode === search || 
    product.sku === search
  )
  
  if (exactMatch && exactMatch.current_stock > 0 && exactMatch.active) {
    addToCart(exactMatch)
    clearSearch()
  } else if (filteredProducts.value.length > 0) {
    addToCart(filteredProducts.value[0])
    clearSearch()
  }
}

// Detector de entrada de código de barras (lectores rápidos)
let barcodeBuffer = ''
let barcodeTimeout = null

const handleBarcodeInput = () => {
  // Si el input es muy rápido (típico de lectores de código de barras)
  clearTimeout(barcodeTimeout)
  barcodeBuffer += searchTerm.value.slice(-1)
  
  barcodeTimeout = setTimeout(() => {
    // Si se detecta un patrón de código de barras (entrada rápida)
    if (barcodeBuffer.length > 8 && searchTerm.value === barcodeBuffer) {
      handleSearchEnter()
    }
    barcodeBuffer = ''
  }, 100)
}

// Manejo de teclado para la calculadora
const handleCalculatorKeyboard = (event) => {
  event.preventDefault()
  
  // Números 0-9
  if (event.key >= '0' && event.key <= '9') {
    inputNumber(parseInt(event.key))
  }
  // Punto decimal
  else if (event.key === '.' || event.key === ',') {
    inputDecimal()
  }
  // Operadores
  else if (event.key === '+') {
    performCalculation('+')
    calculatorOperator.value = '+'
    calculatorWaitingForOperand.value = true
    calculatorOperation.value = `${calculatorDisplay.value} +`
  }
  else if (event.key === '-') {
    performCalculation('-')
    calculatorOperator.value = '-'
    calculatorWaitingForOperand.value = true
    calculatorOperation.value = `${calculatorDisplay.value} -`
  }
  else if (event.key === '*') {
    performCalculation('*')
    calculatorOperator.value = '*'
    calculatorWaitingForOperand.value = true
    calculatorOperation.value = `${calculatorDisplay.value} ×`
  }
  else if (event.key === '/') {
    performCalculation('/')
    calculatorOperator.value = '/'
    calculatorWaitingForOperand.value = true
    calculatorOperation.value = `${calculatorDisplay.value} ÷`
  }
  // Igual o Enter para calcular
  else if (event.key === '=' || event.key === 'Enter') {
    performCalculation('=')
  }
  // Backspace para borrar último dígito
  else if (event.key === 'Backspace') {
    calculatorDisplay.value = calculatorDisplay.value.slice(0, -1) || '0'
  }
  // Delete o C para limpiar
  else if (event.key === 'Delete' || event.key.toLowerCase() === 'c') {
    clearCalculator()
  }
  // Escape para cerrar calculadora
  else if (event.key === 'Escape') {
    showCalculator.value = false
  }
}

// Manejo de atajos de teclado para búsqueda
const handleKeyboard = (event) => {
  // Si la calculadora está abierta, manejar eventos de teclado para la calculadora
  if (showCalculator.value) {
    handleCalculatorKeyboard(event)
    return
  }
  
  // Ctrl+F para enfocar búsqueda
  if ((event.ctrlKey || event.metaKey) && event.key === 'f') {
    event.preventDefault()
    if (searchInput.value) {
      searchInput.value.focus()
    }
  }
  // F3 para enfocar búsqueda
  if (event.key === 'F3') {
    event.preventDefault()
    if (searchInput.value) {
      searchInput.value.focus()
    }
  }
}

// Función para cargar cotización desde facturas
const loadQuotationFromInvoices = async () => {
  if (window.quotationToLoad) {
    console.log('📄 Cargando cotización desde facturas:', window.quotationToLoad)
    
    const quotationInfo = window.quotationToLoad
    const quotationCode = quotationInfo.code
    
    try {
      // Buscar la cotización completa con sus items
      console.log('🔍 Buscando cotización completa:', quotationCode)
      const result = await invoicesService.searchQuote(quotationCode)
      
      console.log('🔍 Respuesta completa de búsqueda:', result)
      
      if (result.success && result.data && result.data.length > 0) {
        console.log('📋 Documentos encontrados:', result.data.length)
        console.log('📋 Primer documento:', result.data[0])
        
        // Buscar por diferentes campos posibles
        const quotation = result.data.find(invoice => {
          console.log(`🔍 Comparando: ${invoice.invoice_number} === ${quotationCode} && ${invoice.status} === 'quotation'`)
          return (invoice.invoice_number === quotationCode || invoice.custom_number === quotationCode || invoice.number === quotationCode) && 
                 invoice.status === 'quotation'
        })
        
        if (quotation) {
          console.log('📄 Cotización completa encontrada:', quotation)
          
          // Limpiar el carrito actual
          cart.items = []
          
          // Cargar información del cliente si existe
          if (quotation.customer) {
            selectedCustomer.value = {
              id: quotation.customer.id,
              name: quotation.customer.name,
              email: quotation.customer.email || '',
              phone: quotation.customer.phone || ''
            }
          } else if (quotation.customer_name) {
            // Si solo tenemos el nombre del cliente
            selectedCustomer.value = {
              id: null,
              name: quotation.customer_name,
              email: '',
              phone: ''
            }
          }
          
          // Cargar productos del carrito - usar sale_items que es el campo correcto
          const itemsToLoad = quotation.sale_items || quotation.saleItems || quotation.items || []
          console.log('🔍 Items disponibles en cotización:', {
            items: quotation.items,
            saleItems: quotation.saleItems, 
            sale_items: quotation.sale_items,
            itemsToLoad: itemsToLoad
          })
          
          if (itemsToLoad && itemsToLoad.length > 0) {
            console.log('🛒 Validando disponibilidad de productos...')
            
            const validItems = []
            const unavailableItems = []
            
            for (const item of itemsToLoad) {
              console.log(`🔍 Validando item:`, item)
              
              // Obtener product_id de diferentes fuentes posibles (compatibilidad con ambas tablas)
              const productId = item.product_id || item.product?.id || null
              
              console.log(`🔍 BÚSQUEDA DE PRODUCTO (loadQuotationFromInvoices):`)
              console.log(`   - item.product_id: ${item.product_id}`)
              console.log(`   - item.product?.id: ${item.product?.id}`)
              console.log(`   - productId final: ${productId}`)
              
              if (!productId) {
                console.log(`❌ NO SE ENCONTRÓ product_id en el item`)
                unavailableItems.push({
                  name: item.product_name || item.name || 'Producto desconocido',
                  requestedQuantity: item.quantity,
                  issue: 'not_found',
                  message: 'ID de producto no encontrado en cotización'
                })
                continue
              }
              
              // Buscar producto en el store global para verificar stock y existencia
              const product = appStore.products.find(p => p.id === productId)
              
              if (!product) {
                // Producto no existe
                console.log(`❌ PRODUCTO NO ENCONTRADO:`)
                console.log(`   - product_id buscado: ${item.product_id}`)
                console.log(`   - Productos disponibles: ${appStore.products.length}`)
                
                unavailableItems.push({
                  name: item.product_name || item.product?.name || `Producto ID ${item.product_id}`,
                  requestedQuantity: item.quantity,
                  issue: 'not_found',
                  message: 'Producto no encontrado en el catálogo'
                })
                console.log(`❌ Producto no encontrado: ID ${item.product_id}`)
              } else if (product.manage_stock && product.stock < item.quantity) {
                // Producto existe pero sin stock suficiente
                console.log(`🚨 VALIDACIÓN STOCK CRÍTICA:`)
                console.log(`   - Producto: ${product.name}`)
                console.log(`   - Stock actual: ${product.stock}`)
                console.log(`   - Cantidad solicitada: ${item.quantity}`)
                console.log(`   - manage_stock: ${product.manage_stock}`)
                console.log(`   - Condición evaluada: ${product.manage_stock && product.stock < item.quantity}`)
                
                unavailableItems.push({
                  name: product.name,
                  quantity: item.quantity,
                  availableQuantity: product.stock,
                  issue: 'insufficient_stock',
                  message: `Stock insuficiente. Disponible: ${product.stock}, Solicitado: ${item.quantity}`
                })
                console.log(`⚠️ Stock insuficiente: ${product.name} - Disponible: ${product.stock}, Solicitado: ${item.quantity}`)
                
                // NUEVO: Si hay stock disponible (mayor a 0), agregarlo a validItems con la cantidad disponible
                if (product.stock > 0) {
                  const cartItem = {
                    id: productId,
                    name: product.name,
                    price: parseFloat(item.unit_price),
                    quantity: product.stock, // Usar la cantidad disponible en lugar de la solicitada
                    discount: parseFloat(item.discount_amount || item.discount || 0),
                    category: product.category || 'General',
                    currentStock: product.stock || 0,
                    manageStock: product.manage_stock || false
                  }
                  
                  validItems.push(cartItem)
                  console.log(`✅ Producto con stock parcial agregado a validItems: ${product.name} - Cantidad disponible: ${product.stock}`)
                }
              } else {
                // Producto válido
                console.log(`✅ PRODUCTO VÁLIDO:`)
                console.log(`   - Producto: ${product.name}`)
                console.log(`   - Stock actual: ${product.stock}`)
                console.log(`   - Cantidad solicitada: ${item.quantity}`)
                console.log(`   - manage_stock: ${product.manage_stock}`)
                console.log(`   - Condición manage_stock: ${product.manage_stock}`)
                console.log(`   - Condición stock: ${product.stock >= item.quantity}`)
                
                const cartItem = {
                  id: productId,
                  name: product.name,
                  price: parseFloat(item.unit_price),
                  quantity: parseInt(item.quantity),
                  discount: parseFloat(item.discount_amount || item.discount || 0),
                  category: product.category || 'General',
                  currentStock: product.current_stock || 0,
                  manageStock: product.manage_stock || false
                }
                
                validItems.push(cartItem)
                console.log(`✅ Producto válido agregado: ${product.name} - Cantidad: ${item.quantity}`)
              }
            }
            
            if (unavailableItems.length > 0) {
              // Hay productos con problemas, pero puede haber válidos
              console.log('⚠️ Se encontraron productos con problemas:', unavailableItems)
              quotationModalData.value = {
                code: quotation.code,
                customer: quotation.customer_name || quotation.customer || 'Cliente General',
                total: quotation.total_amount || quotation.total || 0,
                items: validItems.map(item => ({
                  name: item.name || item.product_name || '',
                  quantity: item.quantity,
                  price: item.unit_price || item.price,
                  subtotal: (item.unit_price || item.price) * item.quantity
                })),
                validItems: validItems,
                unavailableItems: unavailableItems,
                message: `${unavailableItems.length} producto(s) tienen problemas de disponibilidad. ¿Desea continuar con los productos disponibles?`
              }
              quotationModalType.value = 'availability_issues'
              showQuotationModal.value = true
              // El usuario decide si cargar solo los productos válidos
            } else {
              // Todos los productos están disponibles - cargar normalmente
              console.log('✅ Todos los productos están disponibles')
              
              // Limpiar carrito antes de cargar
              clearCart()
              
              validItems.forEach(cartItem => {
                cart.items.push(cartItem)
              })
              
              // Marcar que los productos vienen de una cotización
              productsFromQuotation.value = true
              
              // Guardar copia de los productos originales para comparación
              originalQuotationProducts.value = validItems.map(item => ({
                id: item.id,
                name: item.name,
                quantity: item.quantity,
                price: item.price
              }))
              
              // Seleccionar cliente de la cotización si existe
              if (quotation.customer_id && customers.value.length > 0) {
                const customer = customers.value.find(c => c.id === quotation.customer_id)
                if (customer) {
                  selectedCustomer.value = customer
                }
              }
              
              console.log('🛒 Carrito cargado exitosamente desde cotización:', cart.items)
            }
          } else {
            console.log('⚠️ No se encontraron items en la cotización')
            console.log('⚠️ Estructura completa de la cotización:', quotation)
          }
          
          // Guardar información de la cotización cargada
          loadedQuotation.value = {
            code: quotation.invoice_number,
            customer: quotation.customer_name,
            total: quotation.total_amount,
            created_at: quotation.created_at
          }
          
          console.log('✅ Cotización cargada exitosamente en POS')
        } else {
          console.error('❌ Cotización no encontrada o no es válida')
        }
      } else {
        console.error('❌ No se pudo cargar la cotización:', result.message)
      }
    } catch (error) {
      console.error('❌ Error al cargar cotización desde facturas:', error)
    }
    
    // Limpiar la variable global
    window.quotationToLoad = null
  }
}

// Función para mostrar detalles de la cotización cargada
const showLoadedQuotationDetails = () => {
  if (loadedQuotation.value) {
    quotationModalType.value = 'loaded'
    quotationModalData.value = {
      id: loadedQuotation.value.id, // ID de la cotización para WhatsApp
      code: loadedQuotation.value.invoice_number || loadedQuotation.value.code,
      customer: loadedQuotation.value.customer_name || loadedQuotation.value.customer || 'Cliente General',
      total: total.value || 0,
      items: cart.items.map(item => ({
        name: item.name,
        quantity: item.quantity,
        price: item.price,
        subtotal: item.price * item.quantity
      })),
      message: 'Esta cotización está cargada en el carrito actual.',
      unavailableItems: [],
      validItems: cart.items,
      data: loadedQuotation.value
    }
    showQuotationModal.value = true
  }
}

// Lifecycle hooks
onMounted(async () => {
  try {
    // Configurar interfaz inmediatamente
    setTimeout(() => {
      if (searchInput.value) {
        searchInput.value.focus()
      }
    }, 100)
    
    // Agregar listeners para atajos de teclado
    document.addEventListener('keydown', handleKeyboard)
    
    // Solo cargar descuentos (que aún no están en el store global)
    loadDiscounts()
    
    // Asegurar que el store esté inicializado
    if (!appStore.initialized) {
      console.log('🔄 Inicializando appStore desde PosView...')
      await appStore.initialize()
    }
    
    // ✅ USAR ESTADO GLOBAL DE SESIÓN DE CAJA (evita parpadeo)
    if (appStore.cashSession.initialized) {
      // Usar el estado ya cargado del appStore
      hasOpenSession.value = appStore.cashSession.hasOpenSession
      currentSession.value = appStore.cashSession.current
      
      console.log('🔄 Usando sesión de caja desde appStore:', {
        hasOpenSession: hasOpenSession.value,
        sessionId: currentSession.value?.id,
        sessionStatus: currentSession.value?.status,
        appStoreState: appStore.cashSession
      })
      
      // ✅ FINALIZAR INICIALIZACIÓN ANTES DE MOSTRAR MODAL
      initializing.value = false
      
      // Esperar un tick para que se actualice el DOM y luego mostrar modal si es necesario
      await nextTick()
      
      if (!hasOpenSession.value) {
        console.log('⚠️ No hay sesión abierta, mostrando modal...')
        setTimeout(() => {
          showOpenCashModal()
          showWarning('Sistema bloqueado: Debe abrir caja para procesar ventas. Solo se permiten cotizaciones.')
        }, 100)
      } else {
        console.log('✅ Sesión de caja activa, POS listo para usar')
      }
    } else {
      // Fallback: cargar sesión individualmente solo si el store no está inicializado
      console.log('⚠️ AppStore no inicializado, cargando sesión individualmente...')
      try {
        await loadCurrentSession()
        
        // Sincronizar nuestras variables locales con el composable
        currentSession.value = composableCurrentSession.value
        hasOpenSession.value = composableHasOpenSession.value
        
        console.log('🔄 Sesión cargada individualmente:', {
          hasOpenSession: hasOpenSession.value,
          sessionId: currentSession.value?.id,
          sessionStatus: currentSession.value?.status
        })
        
        // Sincronizar con appStore después de cargar
        appStore.updateCashSession(currentSession.value, hasOpenSession.value)
        
        // ✅ FINALIZAR INICIALIZACIÓN
        initializing.value = false
        await nextTick()
        
        if (!hasOpenSession.value) {
          console.log('⚠️ No hay sesión abierta, mostrando modal...')
          setTimeout(() => {
            showOpenCashModal()
            showWarning('Sistema bloqueado: Debe abrir caja para procesar ventas. Solo se permiten cotizaciones.')
          }, 100)
        } else {
          console.log('✅ Sesión de caja activa, POS listo para usar')
        }
      } catch (error) {
        console.error('Error cargando sesión de caja:', error)
        initializing.value = false // Finalizar inicialización incluso con error
      }
    }
    
    // Verificar si hay una cotización pendiente de cargar
    setTimeout(() => {
      loadQuotationFromInvoices()
    }, 500) // Pequeña espera para asegurar que el componente esté completamente cargado
    
    // 🎯 Cargar drafts existentes de la base de datos
    await loadSalesDrafts()
    
    // Emitir estado inicial del carrito
    emit('cart-status-changed', cart.items.length > 0)
    
  } catch (error) {
    console.error('Error en inicialización de PosView:', error)
    initializing.value = false // Asegurar que se finalice la inicialización
  }
})

// 💰 Watcher para establecer efectivo por defecto cuando se carguen métodos de pago
watch(() => paymentMethods.value.length, (newLength) => {
  if (newLength > 0 && selectedPaymentMethod.value === 'cash') {
    // Buscar si existe un método "efectivo" real en la API
    const cashMethod = paymentMethods.value.find(method => 
      method.id === 'cash' || 
      method.id === 'efectivo' ||
      method.name.toLowerCase().includes('efectivo') ||
      method.name.toLowerCase().includes('cash')
    )
    
    if (cashMethod) {
      selectedPaymentMethod.value = cashMethod.id
      console.log('💰 Método por defecto establecido a:', cashMethod.name)
    } else {
      // Si no existe efectivo, mantener 'cash' o usar el primer método
      console.log('⚠️ No se encontró método "efectivo", manteniendo cash o usando primer método')
      selectedPaymentMethod.value = paymentMethods.value[0]?.id || 'cash'
    }
  }
}, { immediate: true })

onUnmounted(() => {
  // Limpiar listeners
  document.removeEventListener('keydown', handleKeyboard)
})

// Funciones del modal de cotizaciones
const handleLoadQuotation = () => {
  try {
    // Verificar si hay productos validados para cargar
    const validItems = quotationModalData.value.validItems
    const quote = quotationModalData.value.data || quotationModalData.value.quotationToLoad
    
    if (!validItems || validItems.length === 0) {
      console.log('❌ No hay productos válidos para cargar')
      alert('No hay productos disponibles para cargar de esta cotización')
      return
    }

    // Limpiar carrito actual
    clearCart()
    
    // Cargar solo los productos pre-validados al carrito
    validItems.forEach((cartItem, index) => {
      // Verificar una vez más el stock actual por seguridad
      const product = products.value.find(p => p.id === cartItem.id)
      const availableStock = product?.current_stock ?? product?.stock ?? 0
      if (product && availableStock >= cartItem.quantity) {
        cart.items.push(cartItem)
        console.log(`✅ Producto cargado: ${cartItem.name} - Cantidad: ${cartItem.quantity}`)
      } else {
        console.log(`⚠️ Producto saltado por stock insuficiente: ${cartItem.name} (disponible: ${availableStock}, solicitado: ${cartItem.quantity})`)
      }
    })
    
    // Seleccionar cliente de la cotización si existe
    if (quote && quote.customer_id && customers.value.length > 0) {
      const customer = customers.value.find(c => c.id === quote.customer_id)
      if (customer) {
        selectedCustomer.value = customer
      }
    }
    // Marcar que los productos vienen de una cotización
    productsFromQuotation.value = true
    loadedQuotation.value = quote
    
    // Guardar productos originales (crear desde los items cargados)
    originalQuotationProducts.value = cart.items.map(item => ({
      id: item.id,
      name: item.name,
      quantity: item.quantity,
      price: item.price
    }))

    // Cerrar modal
    showQuotationModal.value = false

    console.log(`✅ ${cart.items.length} productos cargados al carrito desde cotización ${quote?.invoice_number || 'N/A'}`)
    
    // Emitir cambio en el estado del carrito después de cargar productos
    emit('cart-status-changed', cart.items.length > 0)
    
  } catch (error) {
    console.error('❌ Error al cargar cotización:', error)
    alert('❌ Error al cargar la cotización. Inténtelo de nuevo.')
  }
}

const handleLoadAvailableProducts = () => {
  try {
    const validItems = quotationModalData.value.validItems
    if (!validItems || validItems.length === 0) {
      console.log('No hay productos válidos para cargar')
      return
    }

    // Limpiar carrito actual
    clearCart()
    
    // Cargar solo los productos válidos al carrito
    validItems.forEach(cartItem => {
      cart.items.push(cartItem)
    })
    
    // Seleccionar cliente de la cotización si existe
    const quotation = quotationModalData.value.data
    if (quotation && quotation.customer_id && customers.value.length > 0) {
      const customer = customers.value.find(c => c.id === quotation.customer_id)
      if (customer) {
        selectedCustomer.value = customer
      }
    }

    // Marcar que los productos vienen de una cotización
    productsFromQuotation.value = true
    loadedQuotation.value = quotation
    
    // Guardar copia de los productos originales para comparación
    originalQuotationProducts.value = validItems.map(item => ({
      id: item.id,
      name: item.name,
      quantity: item.quantity,
      price: item.price
    }))

    // Cerrar el modal después de un breve delay para que el usuario vea que se cargaron los productos
    setTimeout(() => {
      showQuotationModal.value = false
    }, 1500)
    
    // Mostrar notificación de éxito
    console.log(`✅ ${validItems.length} productos disponibles cargados al carrito (desde cotización ${quotation?.code || 'N/A'})`)
    
    // Emitir cambio en el estado del carrito después de cargar productos
    emit('cart-status-changed', cart.items.length > 0)
    
  } catch (error) {
    console.error('Error al cargar productos disponibles:', error)
  }
}

const handleAddPartialStock = (item) => {
  try {
    console.log('🔍 Agregando producto con stock parcial:', item)
    
    // Buscar el producto en el catálogo para obtener sus datos completos
    const product = appStore.products.find(p => p.name === item.name)
    if (!product) {
      alert('❌ Producto no encontrado en el catálogo')
      return
    }
    
    // Verificar que realmente hay stock disponible
    if (product.current_stock < 1) {
      alert('❌ No hay stock disponible para este producto')
      return
    }
    
    // Crear item del carrito con la cantidad disponible
    const cartItem = {
      id: product.id,
      name: product.name,
      price: parseFloat(product.sale_price),
      quantity: Math.min(item.availableQuantity, product.current_stock),
      discount: 0,
      category: product.category || 'General',
      currentStock: product.current_stock,
      manageStock: product.manage_stock || false
    }
    
    // Verificar si el producto ya está en el carrito
    const existingItemIndex = cart.items.findIndex(cartProduct => cartProduct.id === product.id)
    
    if (existingItemIndex >= 0) {
      // Producto ya está en el carrito, incrementar cantidad hasta el máximo disponible
      const currentQuantity = cart.items[existingItemIndex].quantity
      const maxQuantity = Math.min(item.availableQuantity, product.current_stock)
      
      if (currentQuantity < maxQuantity) {
        cart.items[existingItemIndex].quantity = maxQuantity
        console.log(`✅ Cantidad actualizada para ${product.name}: ${maxQuantity}`)
      } else {
        alert(`⚠️ Ya tienes la cantidad máxima disponible de ${product.name} en el carrito`)
        return
      }
    } else {
      // Agregar nuevo producto al carrito
      cart.items.push(cartItem)
      console.log(`✅ Producto agregado: ${product.name} - Cantidad: ${cartItem.quantity}`)
    }
    
    alert(`✅ Se agregó ${cartItem.quantity} unidad(es) de ${product.name} al carrito`)
    
  } catch (error) {
    console.error('Error al agregar producto con stock parcial:', error)
    alert('❌ Error al agregar el producto. Inténtelo de nuevo.')
  }
}

const handlePrintQuotation = async () => {
  // Generar e imprimir ticket de cotización
  await printQuotationTicket()
  showQuotationModal.value = false
}

// Función para enviar cotización por WhatsApp
const handleSendQuotationWhatsApp = async () => {
  try {
    // Obtener datos de la cotización
    const quotationData = quotationModalData.value
    console.log('📋 Datos de cotización para WhatsApp:', quotationData)
    
    if (!quotationData || !quotationData.id) {
      throw new Error('No se encontraron datos de la cotización')
    }

    // Obtener datos del cliente
    const customer = quotationData.customer
    let customerName = 'Cliente General'
    let customerPhone = ''
    
    // Procesar datos del cliente - mejorado para manejar más casos
    if (typeof customer === 'object' && customer !== null) {
      customerName = customer.name || customer.customer_name || 'Cliente General'
      customerPhone = customer.phone || customer.telefono || customer.celular || ''
    } else if (typeof customer === 'string') {
      customerName = customer
    }

    // Intentar obtener teléfono del cliente seleccionado si no lo tenemos
    if (!customerPhone && selectedCustomer.value) {
      customerPhone = selectedCustomer.value.phone || selectedCustomer.value.telefono || selectedCustomer.value.celular || ''
      if (!customerName || customerName === 'Cliente General') {
        customerName = selectedCustomer.value.name || customerName
      }
    }

    // Si no hay teléfono, solicitar al usuario
    if (!customerPhone) {
      showPhoneModal.value = true
      phoneModalMessage.value = `Ingrese el número de WhatsApp para enviar la cotización ${quotationData.code} a ${customerName}:`
      
      // Esperar a que el usuario ingrese el teléfono usando la estrategia correcta
      const phoneResult = await new Promise((resolve) => {
        phoneModalResolve.value = resolve
      })
      
      if (!phoneResult) {
        console.log('❌ Envío por WhatsApp cancelado por el usuario')
        return
      }
      
      customerPhone = phoneResult
    }

    // Validar número de teléfono
    if (!customerPhone) {
      throw new Error('Número de teléfono requerido')
    }

    // Formatear número de teléfono al formato internacional colombiano
    const formatPhoneNumber = (phone) => {
      // Limpiar el número: quitar espacios, guiones, paréntesis
      let cleanPhone = phone.replace(/[\s\-\(\)]/g, '')
      
      // Si ya tiene el formato internacional (+57...), devolverlo tal como está
      if (cleanPhone.startsWith('+57')) {
        return cleanPhone
      }
      
      // Si empieza con 57, agregar el +
      if (cleanPhone.startsWith('57') && cleanPhone.length === 12) {
        return '+' + cleanPhone
      }
      
      // Si es un número de 10 dígitos que empieza con 3 (celular colombiano)
      if (cleanPhone.length === 10 && cleanPhone.startsWith('3')) {
        return '+57' + cleanPhone
      }
      
      // Si es un número de 7 dígitos (fijo colombiano), agregar código de área por defecto
      if (cleanPhone.length === 7) {
        return '+571' + cleanPhone // Bogotá por defecto
      }
      
      // Si no se puede formatear, devolver tal como está
      return cleanPhone
    }

    // Aplicar el formato
    const formattedPhone = formatPhoneNumber(customerPhone)

    console.log('📱 Generando PDF de cotización igual al de impresión...')

    // Generar exactamente el mismo PDF que se usa para imprimir
    const pdfBlob = await generateQuotationPDFBlob(quotationData)

    if (!pdfBlob) {
      throw new Error('Error generando PDF de cotización')
    }

    console.log('✅ PDF generado exitosamente:', {
      size: pdfBlob.size,
      type: pdfBlob.type,
      isValidPDF: pdfBlob.size > 0 && pdfBlob.type === 'application/pdf'
    })

    // Preparar mensaje personalizado para WhatsApp
    const message = `📋 *Cotización ${quotationData.code}*\n\n` +
                   `📅 Fecha: ${new Date().toLocaleDateString('es-CO')}\n` +
                   `👤 Cliente: ${customerName}\n` +
                   `💰 Total: $${parseFloat(quotationData.total || 0).toLocaleString()}\n\n` +
                   `Puede usar este código para realizar su compra posteriormente.\n\n` +
                   `¡Gracias por elegirnos! 😊`

    const fileName = `cotizacion_${quotationData.code}_${Date.now()}.pdf`

    console.log('📱 Datos para envío por WhatsApp:', {
      quotationCode: quotationData.code,
      customerName,
      formattedPhone: formattedPhone,
      fileName,
      pdfSize: pdfBlob.size,
      messageLength: message.length
    })

    // Enviar cotización por WhatsApp con el PDF generado igual al de impresión
    const result = await whatsappService.sendQuotationWithPDF(
      formattedPhone,
      pdfBlob,
      message,
      fileName,
      customerName
    )

    if (result.success) {
      console.log('✅ Cotización enviada por WhatsApp exitosamente')
      
      // Toast profesional en lugar de alerta
      showSuccess(`📱 Cotización ${quotationData.code} enviada a ${customerName} (${formattedPhone})`)
      
      // Cerrar modal después del envío exitoso
      showQuotationModal.value = false
    } else {
      throw new Error(result.message || 'Error al enviar cotización por WhatsApp')
    }

  } catch (error) {
    console.error('❌ Error enviando cotización por WhatsApp:', error)
    alert(`❌ Error al enviar cotización por WhatsApp\n\n${error.message}`)
  }
}

// Función para generar e imprimir ticket de cotización
const printQuotationTicket = async () => {
  try {
    // Obtener datos de la cotización
    const quotationData = quotationModalData.value
    const quotationCode = quotationData.code
    
    // Generar el QR como imagen base64
    const QRCode = await import('qrcode')
    const qrDataURL = await QRCode.default.toDataURL(quotationCode, {
      width: 150,
      height: 150,
      margin: 1,
      color: {
        dark: '#000000',
        light: '#FFFFFF'
      }
    })
    
    // Función para formatear moneda
    const formatCurrencyForPrint = (amount) => {
      return new Intl.NumberFormat('es-CO').format(amount)
    }
    
    // Crear contenido HTML del ticket
    const ticketContent = `
    <!DOCTYPE html>
    <html>
    <head>
      <meta charset="UTF-8">
      <style>
        @media print {
          body { margin: 0; }
          .no-print { display: none; }
        }
        body {
          font-family: 'Courier New', monospace;
          font-size: 12px;
          line-height: 1.2;
          max-width: 300px;
          margin: 0 auto;
          padding: 10px;
        }
        .header {
          text-align: center;
          border-bottom: 2px solid #000;
          padding-bottom: 10px;
          margin-bottom: 15px;
        }
        .company-name {
          font-size: 16px;
          font-weight: bold;
          margin-bottom: 5px;
        }
        .ticket-type {
          font-size: 14px;
          font-weight: bold;
          margin-bottom: 10px;
        }
        .section {
          margin-bottom: 15px;
        }
        .section-title {
          font-weight: bold;
          border-bottom: 1px solid #000;
          margin-bottom: 5px;
        }
        .code-section {
          text-align: center;
          border: 2px solid #000;
          padding: 10px;
          margin: 15px 0;
        }
        .quotation-code {
          font-size: 16px;
          font-weight: bold;
          margin-bottom: 10px;
        }
        .qr-code {
          margin: 10px 0;
        }
        .qr-instructions {
          font-size: 10px;
          text-align: center;
          margin-top: 5px;
        }
        .products-table {
          width: 100%;
          border-collapse: collapse;
          margin-bottom: 10px;
        }
        .products-table th,
        .products-table td {
          border: 1px solid #000;
          padding: 3px;
          text-align: left;
          font-size: 10px;
        }
        .products-table th {
          background-color: #f0f0f0;
          font-weight: bold;
        }
        .total-section {
          border-top: 2px solid #000;
          padding-top: 10px;
          text-align: right;
        }
        .total-amount {
          font-size: 16px;
          font-weight: bold;
        }
        .footer {
          text-align: center;
          border-top: 1px solid #000;
          padding-top: 10px;
          margin-top: 15px;
          font-size: 10px;
        }
        .print-button {
          text-align: center;
          margin: 20px 0;
        }
        .print-btn {
          background-color: #007bff;
          color: white;
          border: none;
          padding: 10px 20px;
          border-radius: 5px;
          cursor: pointer;
          font-size: 14px;
        }
      </style>
    </head>
    <body>
      <div class="header">
        <div class="company-name">SISTEMA POS</div>
        <div class="ticket-type">COTIZACIÓN</div>
        <div>Fecha: ${new Date().toLocaleDateString('es-CO')}</div>
        <div>Hora: ${new Date().toLocaleTimeString('es-CO')}</div>
      </div>

      <div class="section">
        <div class="section-title">CLIENTE</div>
        <div><strong>${quotationData?.customer || 'Cliente General'}</strong></div>
      </div>

      <div class="code-section">
        <div class="quotation-code">CÓDIGO: ${quotationCode}</div>
        <div class="qr-code">
          <img src="${qrDataURL}" alt="QR Code" style="width: 120px; height: 120px;">
        </div>
        <div class="qr-instructions">
          📱 Escanee para cargar automáticamente
        </div>
      </div>

      <div class="section">
        <div class="section-title">PRODUCTOS COTIZADOS</div>
        <table class="products-table">
          <thead>
            <tr>
              <th>Producto</th>
              <th>Cant.</th>
              <th>Precio</th>
              <th>Subtotal</th>
            </tr>
          </thead>
          <tbody>
            ${quotationData?.items?.map(item => `
              <tr>
                <td>${item.product_name || item.name || 'Producto'}</td>
                <td>${item.quantity || 1}</td>
                <td>$${formatCurrencyForPrint(item.unit_price || item.price || 0)}</td>
                <td>$${formatCurrencyForPrint((item.quantity || 1) * (item.unit_price || item.price || 0))}</td>
              </tr>
            `).join('') || '<tr><td colspan="4">No hay productos</td></tr>'}
          </tbody>
        </table>
      </div>

      <div class="total-section">
        <div class="total-amount">
          TOTAL: $${formatCurrencyForPrint(quotationData?.total || 0)}
        </div>
      </div>

      <div class="footer">
        <div>El cliente puede usar este código para</div>
        <div>realizar la compra posteriormente.</div>
        <div style="margin-top: 10px;">
          <strong>¡Gracias por su preferencia!</strong>
        </div>
      </div>

      <div class="print-button no-print">
        <button class="print-btn" onclick="window.print(); window.close();">
          🖨️ Imprimir Ticket
        </button>
      </div>
    </body>
    </html>
    `
    
    // Abrir ventana de impresión
    const printWindow = window.open('', '_blank', 'width=400,height=600')
    printWindow.document.write(ticketContent)
    printWindow.document.close()
    
    // Auto-enfocar para impresión
    printWindow.focus()
    
    console.log('🖨️ Ticket de cotización generado para impresión')
    
  } catch (error) {
    console.error('❌ Error al generar ticket de impresión:', error)
    // Fallback a impresión normal
    window.print()
  }
}

// Función para generar PDF Blob de cotización (igual al de impresión)
const generateQuotationPDFBlob = async (quotationData) => {
  try {
    console.log('📋 Generando PDF Blob de cotización igual al de impresión...')
    
    // Obtener código de cotización
    const quotationCode = quotationData.code
    
    // Generar el QR como imagen base64
    const qrDataURL = await QRCode.toDataURL(quotationCode, {
      width: 80,
      margin: 1,
      color: {
        dark: '#000000',
        light: '#FFFFFF'
      }
    })
    
    // Calcular altura necesaria dinámicamente
    const baseHeight = 120 // Altura base mínima
    const itemHeight = 4 // Altura por item
    const itemCount = Array.isArray(quotationData.items) ? quotationData.items.length : 0
    const qrSectionHeight = 60 // Altura para QR y footer
    const dynamicHeight = Math.max(200, baseHeight + (itemCount * itemHeight) + qrSectionHeight)
    
    // Crear PDF con formato ticket (altura dinámica)
    const pdf = new jsPDF({
      orientation: 'portrait',
      unit: 'mm',
      format: [80, dynamicHeight] // Altura dinámica según contenido
    })
    
    // Configuración del ticket
    let yPos = 8
    const pageWidth = 80
    const leftMargin = 4
    const rightMargin = pageWidth - 4
    const centerX = pageWidth / 2
    
    // ==================== HEADER EMPRESARIAL ====================
    pdf.setFont('helvetica', 'bold')
    pdf.setFontSize(12)
    pdf.text('SISTEMA POS', centerX, yPos, { align: 'center' })
    yPos += 5
    
    pdf.setFontSize(8)
    pdf.setFont('helvetica', 'normal')
    pdf.text('Soluciones de Punto de Venta', centerX, yPos, { align: 'center' })
    yPos += 4
    pdf.text('Tel: (+57) 1 234-5678', centerX, yPos, { align: 'center' })
    yPos += 4
    pdf.text('ventas@sistemapos.com', centerX, yPos, { align: 'center' })
    yPos += 6
    
    // Línea separadora
    pdf.line(leftMargin, yPos, rightMargin, yPos)
    yPos += 6
    
    // ==================== INFORMACIÓN DE COTIZACIÓN ====================
    pdf.setFont('helvetica', 'bold')
    pdf.setFontSize(10)
    pdf.text('COTIZACIÓN', leftMargin, yPos)
    yPos += 5
    
    pdf.setFont('helvetica', 'normal')
    pdf.setFontSize(8)
    pdf.text(`Código: ${quotationCode}`, leftMargin, yPos)
    yPos += 4
    pdf.text(`Fecha: ${new Date(quotationData.created_at || new Date()).toLocaleDateString('es-CO')}`, leftMargin, yPos)
    yPos += 4
    pdf.text(`Hora: ${new Date(quotationData.created_at || new Date()).toLocaleTimeString('es-CO', { hour: '2-digit', minute: '2-digit' })}`, leftMargin, yPos)
    yPos += 4
    pdf.text('Vendedor: Usuario Sistema', leftMargin, yPos)
    yPos += 6
    
    // Línea punteada
    pdf.setLineDashPattern([1, 1], 0)
    pdf.line(leftMargin, yPos, rightMargin, yPos)
    pdf.setLineDashPattern([], 0)
    yPos += 6
    
    // ==================== INFORMACIÓN DEL CLIENTE ====================
    const customerName = typeof quotationData.customer === 'object' && quotationData.customer !== null 
      ? (quotationData.customer.name || quotationData.customer.customer_name || 'Cliente General')
      : (typeof quotationData.customer === 'string' ? quotationData.customer : 'Cliente General')
    
    pdf.setFont('helvetica', 'bold')
    pdf.text('CLIENTE:', leftMargin, yPos)
    yPos += 4
    pdf.setFont('helvetica', 'normal')
    pdf.text(`${customerName}`, leftMargin, yPos)
    yPos += 6
    
    // Línea punteada
    pdf.setLineDashPattern([1, 1], 0)
    pdf.line(leftMargin, yPos, rightMargin, yPos)
    pdf.setLineDashPattern([], 0)
    yPos += 6
    
    // ==================== TABLA DE PRODUCTOS ====================
    // Headers
    pdf.setFont('helvetica', 'bold')
    pdf.setFontSize(7)
    pdf.text('Descripción', leftMargin, yPos)
    pdf.text('Cant', leftMargin + 40, yPos)
    pdf.text('Precio', leftMargin + 50, yPos)
    pdf.text('Total', leftMargin + 65, yPos)
    yPos += 3
    
    pdf.line(leftMargin, yPos, rightMargin, yPos)
    yPos += 3
    
    // Items
    pdf.setFont('helvetica', 'normal')
    pdf.setFontSize(7)
    
    if (Array.isArray(quotationData.items)) {
      quotationData.items.forEach(item => {
        const itemTotal = (parseFloat(item.price || 0) * parseInt(item.quantity || 1))
        
        // Descripción (máximo 20 caracteres)
        const description = (item.name || item.description || 'Sin descripción').substring(0, 20)
        pdf.text(description, leftMargin, yPos)
        
        // Cantidad
        pdf.text(String(item.quantity || 1), leftMargin + 40, yPos)
        
        // Precio
        pdf.text(`$${parseFloat(item.price || 0).toLocaleString()}`, leftMargin + 50, yPos, { align: 'right' })
        
        // Total
        pdf.text(`$${itemTotal.toLocaleString()}`, leftMargin + 72, yPos, { align: 'right' })
        
        yPos += 4
      })
    }
    
    yPos += 3
    
    // ==================== TOTALES ====================
    pdf.line(leftMargin, yPos, rightMargin, yPos)
    yPos += 4
    
    pdf.setFont('helvetica', 'normal')
    pdf.setFontSize(8)
    
    // Subtotal
    pdf.text('Subtotal:', leftMargin + 35, yPos)
    pdf.text(`$${parseFloat(quotationData.subtotal || quotationData.total || 0).toLocaleString()}`, leftMargin + 72, yPos, { align: 'right' })
    yPos += 4
    
    // Descuento
    pdf.text('Descuento:', leftMargin + 35, yPos)
    pdf.text(`$${parseFloat(quotationData.discount_amount || 0).toLocaleString()}`, leftMargin + 72, yPos, { align: 'right' })
    yPos += 4
    
    // IVA
    pdf.text('IVA (0%):', leftMargin + 35, yPos)
    pdf.text('$0', leftMargin + 72, yPos, { align: 'right' })
    yPos += 4
    
    // Total final
    pdf.line(leftMargin + 35, yPos, rightMargin, yPos)
    yPos += 3
    
    pdf.setFont('helvetica', 'bold')
    pdf.setFontSize(10)
    pdf.text('TOTAL:', leftMargin + 35, yPos)
    pdf.text(`$${parseFloat(quotationData.total || 0).toLocaleString()}`, leftMargin + 72, yPos, { align: 'right' })
    yPos += 10
    
    // ==================== CÓDIGO QR (POSICIÓN FIJA AL FINAL) ====================
    pdf.setLineDashPattern([1, 1], 0)
    pdf.line(leftMargin, yPos, rightMargin, yPos)
    pdf.setLineDashPattern([], 0)
    yPos += 6
    
    pdf.setFont('helvetica', 'bold')
    pdf.setFontSize(9)
    pdf.text('Código de Cotización', centerX, yPos, { align: 'center' })
    yPos += 6
    
    // Añadir QR code - SIEMPRE VISIBLE
    pdf.addImage(qrDataURL, 'PNG', centerX - 15, yPos, 30, 30)
    yPos += 35
    
    pdf.setFont('helvetica', 'bold')
    pdf.setFontSize(8)
    pdf.text(quotationCode, centerX, yPos, { align: 'center' })
    yPos += 10
    
    // ==================== FOOTER ====================
    pdf.setLineDashPattern([1, 1], 0)
    pdf.line(leftMargin, yPos, rightMargin, yPos)
    pdf.setLineDashPattern([], 0)
    yPos += 5
    
    pdf.setFont('helvetica', 'bold')
    pdf.setFontSize(8)
    pdf.text('COTIZACIÓN VÁLIDA', centerX, yPos, { align: 'center' })
    yPos += 4
    
    pdf.setFont('helvetica', 'normal')
    pdf.setFontSize(7)
    pdf.text('El cliente puede usar este código para', centerX, yPos, { align: 'center' })
    yPos += 3
    pdf.text('realizar la compra posteriormente.', centerX, yPos, { align: 'center' })
    yPos += 6
    
    pdf.setFont('helvetica', 'bold')
    pdf.text('¡Gracias por su preferencia!', centerX, yPos, { align: 'center' })
    
    // Convertir a Blob con tipo MIME explícito
    const pdfArrayBuffer = pdf.output('arraybuffer')
    const pdfBlob = new Blob([pdfArrayBuffer], { type: 'application/pdf' })
    
    console.log('✅ PDF Blob igual al de impresión generado exitosamente:', {
      size: pdfBlob.size,
      type: pdfBlob.type,
      sizeKB: Math.round(pdfBlob.size / 1024),
      dynamicHeight: dynamicHeight + 'mm',
      itemCount: itemCount
    })
    return pdfBlob
    
  } catch (error) {
    console.error('❌ Error al generar PDF Blob:', error)
    throw error
  }
}

// Función para manejar el cierre del modal de cotización
const handleCloseQuotationModal = () => {
  // Cerrar el modal
  showQuotationModal.value = false
  
  // Limpiar toda la información de cotización cargada
  loadedQuotation.value = null
  productsFromQuotation.value = false
  originalQuotationProducts.value = []
  quotationModalData.value = {}
  quotationModalType.value = ''
  
  console.log('✅ Modal de cotización cerrado y estado limpiado')
}

// Funciones del QR Scanner
const startQRScanner = async () => {
  try {
    showQRScanner.value = true
    
    // Esperar a que el video esté en el DOM
    await nextTick()
    
    if (!qrVideo.value) {
      console.error('❌ Elemento de video no encontrado')
      return
    }
    
    // Crear el scanner
    qrScanner.value = new QrScanner(
      qrVideo.value,
      (result) => {
        console.log('📱 QR detectado:', result.data)
        handleQRResult(result.data)
      },
      {
        returnDetailedScanResult: true,
        highlightScanRegion: true,
        highlightCodeOutline: true,
      }
    )
    
    // Iniciar el scanner
    await qrScanner.value.start()
    console.log('📷 Scanner QR iniciado')
    
  } catch (error) {
    console.error('❌ Error al iniciar scanner QR:', error)
    alert('Error al acceder a la cámara. Por favor, verifique los permisos.')
    showQRScanner.value = false
  }
}

const stopQRScanner = () => {
  if (qrScanner.value) {
    qrScanner.value.destroy()
    qrScanner.value = null
  }
  showQRScanner.value = false
  console.log('📷 Scanner QR detenido')
}

const handleQRResult = (data) => {
  console.log('🔍 Procesando código QR:', data)
  
  // Detener el scanner
  stopQRScanner()
  
  // Verificar si es un código de cotización válido (formato: COT-XXXXXX)
  if (data.match(/^COT-\d+$/)) {
    console.log('✅ Código de cotización válido detectado:', data)
    
    // Poner el código en el campo de búsqueda y procesarlo
    searchTerm.value = data
    
    // Procesar como búsqueda de cotización
    handleSearchEnter()
    
  } else {
    alert('❌ Código QR no válido. Se esperaba un código de cotización (COT-XXXXXX)')
    console.log('❌ Código QR no válido:', data)
  }
}

// ==================== FUNCIONES DE CONTROL DE CAJA ====================

/**
 * Manejar apertura de sesión de caja
 */
const handleOpenCashSession = async (sessionData) => {
  try {
    const result = await openSession(sessionData)
    
    if (result.success) {
      showSuccess('¡Caja abierta correctamente! Ya puedes procesar ventas.')
      showCashOpenModal.value = false
      
      // 🔧 ARREGLO: Desactivar modo cotización cuando se abre una caja
      if (quotationMode.value) {
        quotationMode.value = false
        showSuccess('Modo cotización desactivado. Ahora puedes procesar ventas normalmente.')
      }
      
      // Actualizar estado local y sincronizar con appStore
      await loadCurrentSession()
      
      // Sincronizar nuestras variables locales con el composable
      currentSession.value = composableCurrentSession.value
      hasOpenSession.value = composableHasOpenSession.value
      
      // ✅ SINCRONIZAR CON APPSTORE (evitar verificaciones futuras)
      appStore.updateCashSession(currentSession.value, hasOpenSession.value)
      
      // Asegurar que no estamos en estado de inicialización
      initializing.value = false
      
      console.log('✅ Sesión abierta y sincronizada con appStore')
    }
  } catch (error) {
    console.error('Error al abrir caja:', error)
    const message = error.response?.data?.message || 'Error al abrir la caja'
    showError(message)
  }
}

/**
 * Manejar cierre de sesión de caja
 */
const handleCloseCashSession = async (closeData) => {
  try {
    const result = await closeSession(closeData)
    
    if (result.success) {
      showSuccess('¡Caja cerrada correctamente!')
      showCashCloseModal.value = false
      
      // Actualizar estado local y sincronizar con appStore
      await loadCurrentSession()
      
      // Sincronizar nuestras variables locales con el composable
      currentSession.value = composableCurrentSession.value
      hasOpenSession.value = composableHasOpenSession.value
      
      // ✅ SINCRONIZAR CON APPSTORE (evitar verificaciones futuras)
      appStore.updateCashSession(currentSession.value, hasOpenSession.value)
      
      // Asegurar que no estamos en estado de inicialización
      initializing.value = false
      
      console.log('✅ Sesión cerrada y sincronizada con appStore')
    }
  } catch (error) {
    console.error('Error al cerrar caja:', error)
    const message = error.response?.data?.message || 'Error al cerrar la caja'
    showError(message)
  }
}

/**
 * Mostrar modal de apertura de caja
 */
const showOpenCashModal = () => {
  showCashOpenModal.value = true
}

/**
 * Manejar cierre del modal de apertura de caja
 * Solo permite cerrar si hay sesión abierta
 */
const handleCloseCashOpenModal = () => {
  // Solo permitir cerrar si hay sesión abierta
  if (hasOpenSession.value) {
    showCashOpenModal.value = false
  } else {
    // Si no hay sesión, mostrar advertencia pero mantener modal abierto
    showWarning('Debe abrir una caja o seleccionar modo cotización para continuar')
  }
}

/**
 * Activar modo cotización
 * Permite usar el POS solo para generar cotizaciones sin procesar ventas
 */
const handleQuotationMode = () => {
  quotationMode.value = true
  showCashOpenModal.value = false
  showSuccess('Modo cotización activado. Solo podrá generar cotizaciones, no procesar ventas.')
}



/**
 * Mostrar modal de cierre de caja
 */
const showCloseCashModal = async () => {
  console.log('🎯 showCloseCashModal llamado')
  console.log('🎯 currentSession actual:', currentSession.value)
  
  // Cargar sesión actual por si no está cargada
  try {
    await loadCurrentSession()
    console.log('🎯 Sesión cargada:', currentSession.value)
  } catch (error) {
    console.error('🎯 Error cargando sesión:', error)
  }
  
  // Verificar nuevamente después de cargar
  if (!currentSession.value) {
    console.log('🎯 No hay sesión abierta después de cargar')
    showError('No hay una sesión de caja abierta')
    return
  }
  
  console.log('🎯 Abriendo modal de cerrar caja')
  showCashCloseModal.value = true
}

/**
 * Validar que se puede procesar una venta
 */
const validateCanProcessSale = () => {
  if (!canProcessSale()) {
    showError('Debes abrir una caja antes de procesar ventas')
    
    // Mostrar modal de apertura automáticamente
    setTimeout(() => {
      showOpenCashModal()
    }, 1000)
    
    return false
  }
  return true
}

// Agregar validación a las funciones de venta existentes
const originalConfirmQuotation = confirmQuotation

// Sobrescribir la función de confirmación de cotización para validar caja
const confirmQuotationWithCashValidation = async () => {
  // Solo validar caja para ventas, no para cotizaciones
  if (paymentType.value === 'contado' && !validateCanProcessSale()) {
    return
  }
  
  return await originalConfirmQuotation()
}

// Reemplazar la función original
// confirmQuotation = confirmQuotationWithCashValidation

// ==================== FIN FUNCIONES DE CONTROL DE CAJA ====================

// ==================== FUNCIONES DE DEVOLUCIONES ====================

/**
 * Manejar el éxito de una devolución
 */
const handleReturnSuccess = async (returnData) => {
  try {
    console.log('✅ Devolución procesada exitosamente:', returnData)
    
    // Mostrar mensaje de éxito
    showToast(`Devolución ${returnData.number} procesada exitosamente`)
    
    // Cerrar el modal
    showReturnsModal.value = false
    
    // Refrescar datos del POS para actualizar inventario
    await refreshPosData()
    
  } catch (error) {
    console.error('Error al procesar el éxito de la devolución:', error)
    showError('Error al actualizar los datos después de la devolución')
  }
}

// ==================== FIN FUNCIONES DE DEVOLUCIONES ====================

// Cleanup del scanner al desmontar el componente
onBeforeUnmount(() => {
  if (qrScanner.value) {
    qrScanner.value.destroy()
  }
})

// Exposer funciones para que puedan ser llamadas desde el componente padre
defineExpose({
  showCloseCashModal,
  openCloseCashModal: showCloseCashModal,
  showOpenCashModal,
  openOpenCashModal: showOpenCashModal
})
</script>

<style scoped>
.product-card {
  background-color: white;
  border-radius: 0.75rem;
  box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
  border: 2px solid #e5e7eb;
  overflow: hidden;
  transition: all 0.3s ease;
}

.dark .product-card {
  background-color: #1f2937;
  border-color: #374151;
}

.product-card:hover {
  transform: translateY(-4px) scale(1.02);
  box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.15);
  border-color: #3b82f6;
}

.cart-item {
  background-color: white;
  border-radius: 0.5rem;
  box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
  border: 1px solid #e5e7eb;
  padding: 1rem;
  transition: all 0.2s ease;
}

.dark .cart-item {
  background-color: #1f2937;
  border-color: #374151;
}

.cart-item:hover {
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
}

@keyframes slideUp {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.animate-slide-up {
  animation: slideUp 0.3s ease-out;
}

/* Scrollbar personalizado */
::-webkit-scrollbar {
  width: 6px;
}

::-webkit-scrollbar-track {
  background: #f1f5f9;
}

::-webkit-scrollbar-thumb {
  background: #cbd5e1;
  border-radius: 3px;
}

::-webkit-scrollbar-thumb:hover {
  background: #94a3b8;
}

.dark ::-webkit-scrollbar-track {
  background: #374151;
}

.dark ::-webkit-scrollbar-thumb {
  background: #6b7280;
}

/* Ocultar scrollbar en tabs */
.scrollbar-hide {
  -ms-overflow-style: none;
  scrollbar-width: none;
}

.scrollbar-hide::-webkit-scrollbar {
  display: none;
}
</style>