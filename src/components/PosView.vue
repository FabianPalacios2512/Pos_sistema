<template>
  <div class="h-full relative bg-gradient-to-br from-gray-50 via-white to-slate-100 dark:bg-gradient-to-b dark:from-[#141417] dark:via-slate-900 dark:to-[#0a0a0c] transition-colors duration-300 pb-4" @click="handlePosClick">
    
    <!-- 🎯 Modal de Bienvenida Primera Vez - DISEÑO LIMPIO CON ELEVACIÓN POR LUMINOSIDAD -->
    <Teleport to="body">
      <div v-if="showWelcomeModal" class="fixed inset-0 z-[100] flex items-center justify-center bg-black/80 backdrop-blur-sm animate-fade-in">
        <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-2xl max-w-md w-full mx-4 overflow-hidden animate-scale-in border border-gray-200 dark:border dark:border-white/10">
          
          <!-- Icono Hero Centrado -->
          <div class="pt-10 pb-6 flex justify-center">
            <div class="w-20 h-20 bg-emerald-50 dark:bg-emerald-950/50 rounded-full flex items-center justify-center border-4 border-emerald-100 dark:border-emerald-800/50">
              <svg class="w-10 h-10 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
              </svg>
            </div>
          </div>
          
          <!-- Contenido -->
          <div class="px-8 pb-8 space-y-6">
            <!-- Título y Subtítulo -->
            <div class="text-center space-y-2">
              <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Te damos la bienvenida a 105 POS Pro</h2>
              <p class="text-gray-600 dark:text-gray-400 text-sm">El corazón de tu negocio empieza a latir hoy</p>
            </div>
            
            <!-- Lista de beneficios -->
            <div class="space-y-3">
              <div class="flex items-start gap-3">
                <div class="w-5 h-5 rounded-full bg-emerald-50 dark:bg-emerald-900/30 flex items-center justify-center flex-shrink-0 mt-0.5 border border-emerald-100 dark:border-emerald-800/50">
                  <svg class="w-3 h-3 text-emerald-600 dark:text-emerald-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                  </svg>
                </div>
                <p class="text-sm text-gray-700 dark:text-gray-300 leading-relaxed">Cómo realizar tu primera venta en segundos</p>
              </div>
              
              <div class="flex items-start gap-3">
                <div class="w-5 h-5 rounded-full bg-emerald-50 dark:bg-emerald-900/30 flex items-center justify-center flex-shrink-0 mt-0.5 border border-emerald-100 dark:border-emerald-800/50">
                  <svg class="w-3 h-3 text-emerald-600 dark:text-emerald-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                  </svg>
                </div>
                <p class="text-sm text-gray-700 dark:text-gray-300 leading-relaxed">Gestionar tu inventario sin complicaciones</p>
              </div>
              
              <div class="flex items-start gap-3">
                <div class="w-5 h-5 rounded-full bg-emerald-50 dark:bg-emerald-900/30 flex items-center justify-center flex-shrink-0 mt-0.5 border border-emerald-100 dark:border-emerald-800/50">
                  <svg class="w-3 h-3 text-emerald-600 dark:text-emerald-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                  </svg>
                </div>
                <p class="text-sm text-gray-700 dark:text-gray-300 leading-relaxed">Cerrar caja y ver tus ganancias del día</p>
              </div>
            </div>
            
            <!-- Badge de tiempo -->
            <div class="flex items-center justify-center gap-2 py-2">
              <svg class="w-4 h-4 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
              </svg>
              <span class="text-xs font-medium text-gray-500 dark:text-gray-500">Tiempo estimado: 3 minutos</span>
            </div>
            
            <!-- Botón Principal (Verde con texto oscuro en dark mode para mejor contraste) -->
            <button 
              @click="handleWelcomeStart"
              class="w-full px-6 py-3.5 bg-emerald-600 dark:bg-emerald-600 hover:bg-emerald-700 dark:hover:bg-emerald-500 text-white dark:text-slate-900 font-bold text-sm rounded-xl shadow-lg shadow-emerald-600/30 dark:shadow-emerald-500/30 transition-all duration-200 transform active:scale-95"
            >
              Iniciar Recorrido Rápido
            </button>
            
            <!-- Botón Secundario (texto simple) -->
            <div class="text-center">
              <button 
                @click="handleWelcomeSkip"
                class="text-sm text-gray-500 dark:text-gray-500 hover:text-gray-700 dark:hover:text-gray-400 font-medium transition-colors"
              >
                Saltar por ahora
              </button>
            </div>
          </div>
          
        </div>
      </div>
    </Teleport>
    
    <!-- 🎯 Tour Educativo del POS -->
    <ContextualTour 
      ref="posTourRef"
      module-name="pos"
      :steps="posTourSteps"
      :auto-start="false"
      @complete="handlePosTourComplete"
      @skip="handlePosTourSkip"
    />
    
    <!-- BARRA DE HERRAMIENTAS EMPRESARIAL -->
    <div class="sticky top-0 z-30 backdrop-blur-xl transition-all duration-300">
    
    <div class="px-4 lg:px-6 py-4">
      <div class="flex flex-col md:flex-row items-center justify-between gap-4">
        
        <div class="flex items-center gap-3 flex-1 w-full md:w-auto">
          <div class="flex-1 max-w-2xl relative group z-20">
            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
              <svg class="h-5 w-5 text-slate-400 dark:text-zinc-500 group-focus-within:text-slate-600 dark:group-focus-within:text-slate-400 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
              </svg>
            </div>
            
            <input
              ref="searchInput"
              v-model="searchTerm"
              type="text"
              placeholder="Buscar productos, SKU o escanear..."
              class="block w-full h-10 pl-12 pr-32 text-sm font-semibold bg-white dark:bg-zinc-800 border-2 border-gray-200 dark:border-zinc-700 text-slate-900 dark:text-white placeholder-slate-500 dark:placeholder-zinc-400 rounded-xl shadow-md focus:border-slate-600 dark:focus:border-slate-500 focus:ring-4 focus:ring-slate-500/30 dark:focus:ring-slate-400/30 transition-all duration-200 hover:border-slate-500 dark:hover:border-zinc-600"
              @keydown.escape="clearSearch"
              @keydown.enter.prevent="handleSearchEnter"
              @input="handleBarcodeInput"
            />
            
            <div class="absolute inset-y-0 right-0 flex items-center pr-2 space-x-1">
              <!-- Badge "Listo para escanear" -->
              <div class="hidden sm:flex items-center gap-1 px-2 py-1 bg-emerald-50 dark:bg-emerald-950/30 text-emerald-600 dark:text-emerald-400 rounded-lg border border-emerald-200 dark:border-emerald-800">
                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M5 2a1 1 0 011 1v1h1a1 1 0 010 2H6v1a1 1 0 01-2 0V6H3a1 1 0 010-2h1V3a1 1 0 011-1zm0 10a1 1 0 011 1v1h1a1 1 0 110 2H6v1a1 1 0 11-2 0v-1H3a1 1 0 110-2h1v-1a1 1 0 011-1zM12 2a1 1 0 01.967.744L14.146 7.2 17.5 9.134a1 1 0 010 1.732l-3.354 1.935-1.18 4.455a1 1 0 01-1.933 0L9.854 12.8 6.5 10.866a1 1 0 010-1.732l3.354-1.935 1.18-4.455A1 1 0 0112 2z" clip-rule="evenodd"/>
                </svg>
                <span class="text-[9px] font-bold uppercase tracking-wider">Escanear</span>
              </div>
              
              <button
                @click="startQRScanner"
                class="p-2 text-slate-400 dark:text-zinc-500 hover:text-slate-600 dark:hover:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800/30 rounded-xl transition-all duration-200"
                title="Escanear código QR"
              >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path>
                </svg>
              </button>
              
              <button
                v-if="searchTerm"
                @click="clearSearch"
                class="p-2 text-slate-400 dark:text-zinc-500 hover:text-rose-500 dark:hover:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-950/30 rounded-xl transition-all duration-200"
                title="Limpiar búsqueda"
              >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
              </button>
            </div>
          </div>

          <!-- 🔍 DEBUG: Verificar condición del botón -->
          <!-- shouldShowMultiWarehouseFeatures: {{ shouldShowMultiWarehouseFeatures }} -->
          <!-- warehouses.length: {{ warehouses.length }} -->
          <!-- hasOpenSession: {{ hasOpenSession }} -->
          <!-- plan: {{ appStore.tenantPlan }} -->

          <!-- 🌍 Toggle Búsqueda Global/Local (Siempre visible con sesión) -->
          <button
            v-if="shouldShowMultiWarehouseFeatures"
            @click="toggleGlobalSearch"
            class="flex items-center gap-2 px-3 h-10 rounded-xl border-2 transition-all duration-200 font-bold text-xs shadow-sm hover:shadow-md"
            :class="globalSearch 
              ? 'bg-blue-600 dark:bg-blue-700 border-blue-600 dark:border-blue-700 text-white hover:bg-blue-700 dark:hover:bg-blue-600' 
              : 'bg-emerald-600 dark:bg-emerald-700 border-emerald-600 dark:border-emerald-700 text-white hover:bg-emerald-700 dark:hover:bg-emerald-600'"
            :title="globalSearch ? '🌍 Buscando en TODAS las tiendas' : '🏪 Buscar SOLO en ' + (currentSession?.warehouse?.name || 'tienda actual')"
          >
            <svg v-if="globalSearch" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
            </svg>
            <span>{{ globalSearch ? 'Global' : 'Local' }}</span>
          </button>

        </div>
        
        <div class="flex items-center gap-3 w-full md:w-auto justify-end">

          <!-- Botón Pedido Web -->
          <button 
            @click="showLoadWebOrderModal = true" 
            class="hidden sm:flex items-center gap-2 pl-1 pr-3 h-10 rounded-full border transition-all duration-300 group bg-white dark:bg-zinc-900 border-slate-200 dark:border-zinc-700 hover:border-blue-300 dark:hover:border-blue-700 shadow-sm"
            title="Cargar pedido web"
          >
            <div class="w-8 h-8 rounded-full flex items-center justify-center shadow-sm transition-colors bg-white dark:bg-zinc-800 border border-slate-200 dark:border-zinc-700 text-blue-500 dark:text-blue-400 group-hover:text-blue-600 dark:group-hover:text-blue-300">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
              </svg>
            </div>
            <div class="flex flex-col text-left leading-none">
              <span class="text-xs font-bold text-gray-900 dark:text-zinc-200">Pedido Web</span>
            </div>
          </button>

          <button
            id="tour-pos-returns"
            @click="showReturnsModal = true"
            :disabled="quotationMode"
            class="hidden sm:flex items-center gap-2 pl-1 pr-3 h-10 rounded-full border transition-all duration-300 group"
            :class="quotationMode ? 'bg-slate-100 dark:bg-zinc-800 border-slate-200 dark:border-zinc-700 text-slate-400 dark:text-zinc-600' : 'bg-white dark:bg-zinc-900 border-slate-200 dark:border-zinc-700 hover:border-blue-300 dark:hover:border-blue-700 shadow-sm'"
          >
            <div class="w-8 h-8 rounded-full flex items-center justify-center shadow-sm transition-colors bg-white dark:bg-zinc-800 border border-slate-200 dark:border-zinc-700"
                 :class="quotationMode ? 'text-slate-400 dark:text-zinc-600' : 'text-blue-500 dark:text-blue-400 group-hover:text-blue-600 dark:group-hover:text-blue-300'">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"></path></svg>
            </div>
            <div class="flex flex-col text-left leading-none">
              
              <span class="text-xs font-bold text-gray-900 dark:text-zinc-200">Devoluciones</span>
            </div>
          </button>
          
          <WhatsAppStatus id="tour-pos-whatsapp" ref="whatsappStatus" class="hidden sm:block" />
          
          <button 
            id="tour-pos-cash-btn"
            @click="hasOpenSession ? showCloseCashModal() : showOpenCashModal()"
            class="relative flex items-center gap-2 pl-1 pr-3 h-10 rounded-full border transition-all duration-300 group"
            :class="hasOpenSession 
              ? 'bg-white dark:bg-zinc-900 border-emerald-200 dark:border-emerald-800 hover:border-emerald-400 dark:hover:border-emerald-600 shadow-sm' 
              : 'bg-slate-100 dark:bg-zinc-800 border-slate-200 dark:border-zinc-700 hover:bg-white dark:hover:bg-zinc-900 hover:border-rose-300 dark:hover:border-rose-700'"
          >
            <div class="w-8 h-8 rounded-full flex items-center justify-center shadow-sm transition-colors"
                 :class="hasOpenSession ? 'bg-emerald-500 dark:bg-emerald-600 text-white' : 'bg-white dark:bg-zinc-800 border border-slate-200 dark:border-zinc-700 text-slate-400 dark:text-zinc-600 group-hover:text-rose-500 dark:group-hover:text-rose-400'">
              <svg v-if="hasOpenSession" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
              <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
            </div>
            
            <div class="flex flex-col text-left leading-none">
              <span class="text-[9px] font-black uppercase tracking-widest mb-1"
                    :class="hasOpenSession ? 'text-emerald-600 dark:text-emerald-400' : 'text-slate-400 dark:text-zinc-600 group-hover:text-rose-500 dark:group-hover:text-rose-400'">
                {{ hasOpenSession ? 'Turno Activo' : 'Cerrado' }}
              </span>
              <span class="text-xs font-bold text-gray-900 dark:text-zinc-200">
                {{ hasOpenSession ? 'Cerrar Caja' : 'Abrir Turno' }}
              </span>
            </div>
          </button>

        </div>
      </div>
    </div>

    <div class="px-4 lg:px-6 pb-4 overflow-x-auto scrollbar-hide">
        <div class="flex items-center gap-1.5 min-w-max">
            <button 
                @click="selectedCategory = null"
                class="px-4 py-1.5 rounded-full text-xs font-bold border-2 transition-all duration-200 flex items-center gap-2 hover:scale-105 active:scale-95"
                :class="!selectedCategory 
                    ? 'bg-slate-800 dark:bg-slate-700 text-white border-slate-800 dark:border-slate-700 shadow-lg shadow-slate-500/30' 
                    : 'bg-white dark:bg-zinc-800 text-slate-700 dark:text-zinc-300 border-slate-300 dark:border-zinc-600 hover:border-indigo-400 dark:hover:border-indigo-600 hover:text-indigo-600 dark:hover:text-indigo-400 hover:bg-indigo-50 dark:hover:bg-indigo-950/30 shadow-sm'"
            >
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                Todas
            </button>

            <button 
                v-for="cat in categories" 
                :key="cat.id"
                @click="selectedCategory = cat.id"
                class="px-4 py-1.5 rounded-full text-xs font-bold border-2 transition-all duration-200 whitespace-nowrap hover:scale-105 active:scale-95"
                :class="selectedCategory === cat.id
                    ? 'bg-slate-800 dark:bg-slate-700 text-white border-slate-800 dark:border-slate-700 shadow-lg shadow-slate-500/30' 
                    : 'bg-white dark:bg-zinc-800 text-slate-700 dark:text-zinc-300 border-slate-300 dark:border-zinc-600 hover:border-indigo-400 dark:hover:border-indigo-600 hover:text-indigo-600 dark:hover:text-indigo-400 hover:bg-indigo-50 dark:hover:bg-indigo-950/30 shadow-sm'"
            >
                {{ capitalizeText(cat.name) }}
            </button>
        </div>
    </div>
</div>
    <!-- FIN BARRA DE HERRAMIENTAS EMPRESARIAL -->
    
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-3 px-4 lg:px-6 py-2.5" style="height: calc(100vh - 11rem - 54px); margin-bottom: 24px;">
  <!-- Panel Izquierdo: Catálogo de Productos - 70% Fashion (8/12) | 50% General (6/12) -->
  <div :class="isFashionStore ? 'lg:col-span-8' : 'lg:col-span-6'" class="h-full overflow-hidden transition-all duration-300">
    <div class="bg-white/80 dark:bg-zinc-900/90 backdrop-blur-sm rounded-2xl shadow-lg dark:shadow-2xl dark:shadow-black/40 border border-gray-200/50 dark:border-zinc-800/60 h-full flex flex-col overflow-hidden transition-all duration-300">
      
      <div class="flex-1 p-3 overflow-y-auto bg-slate-100/80 dark:bg-zinc-950/50 backdrop-blur-sm" style="scrollbar-width: thin; scrollbar-color: #cbd5e1 transparent;">
        
        <!-- Loading skeleton -->
      <div v-if="productsLoading" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 xl:grid-cols-5 gap-3">
          <div v-for="n in 10" :key="n" class="bg-white rounded-2xl p-3 border border-slate-100 shadow-sm animate-pulse">
            <div class="aspect-square bg-slate-100 rounded-xl mb-3"></div>
            <div class="h-5 bg-slate-100 rounded-lg w-2/3 mb-2"></div>
          <div class="h-4 bg-slate-100 rounded-lg w-1/3"></div>
        </div>
      </div>
      
      <!-- Primera vez: NO hay productos en la base de datos -->
      <div v-else-if="isFirstTimeNoProducts" class="h-full flex flex-col items-center justify-center text-center py-12 min-h-[600px]">
        <!-- Icono de carrito vacío mejorado -->
        <div class="w-28 h-28 bg-slate-100 dark:bg-zinc-800/50 border border-slate-200 dark:border-zinc-700 rounded-2xl flex items-center justify-center mb-6 shadow-lg dark:shadow-black/30">
           <svg class="w-16 h-16 text-slate-400 dark:text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
             <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
           </svg>
        </div>
        <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">¿Quieres crear tu primer producto?</h3>
        <p class="text-sm text-gray-600 dark:text-zinc-400 max-w-xs leading-relaxed mb-6">
           Aún no tienes productos registrados. Crea tu primer producto para comenzar a vender.
        </p>
        <button
          @click="emit('change-module', 'products')"
          class="px-6 py-3 bg-slate-900 dark:bg-slate-700 hover:bg-black dark:hover:bg-slate-600 text-white text-sm font-bold rounded-xl shadow-lg shadow-slate-400/40 dark:shadow-slate-900/50 hover:shadow-slate-400/60 dark:hover:shadow-slate-900/70 transition-all duration-300 transform active:scale-95 flex items-center gap-2"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
          </svg>
          <span>Crear Primer Producto</span>
        </button>
      </div>
      
      <!-- Filtros/búsqueda sin resultados -->
      <div v-else-if="isEmptyByFilters" class="h-full flex flex-col items-center justify-center text-center py-12 min-h-[600px]">
        <div class="w-24 h-24 bg-slate-100 dark:bg-zinc-800/50 border border-slate-200 dark:border-zinc-700 rounded-full flex items-center justify-center mb-4 shadow-lg dark:shadow-black/30">
           <svg class="w-10 h-10 text-slate-400 dark:text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
             <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
           </svg>
        </div>
        <h3 class="text-base font-bold text-gray-900 dark:text-white mb-1">No hay productos aquí</h3>
        <p class="text-sm text-gray-600 dark:text-zinc-400 max-w-[220px] leading-relaxed">
           No encontramos coincidencias para tu búsqueda.
        </p>
        <button
          v-if="searchTerm || selectedCategory"
          @click="clearSearch(); selectedCategory = null;"
          class="mt-5 px-5 py-2.5 bg-slate-900 dark:bg-slate-700 hover:bg-black dark:hover:bg-slate-600 text-white text-xs font-bold rounded-xl shadow-lg shadow-slate-400/40 dark:shadow-slate-900/50 transition-all duration-300"
        >
          Limpiar filtros
        </button>
      </div>
      
      <!-- 👗 MODO FASHION - Diseño tipo Boutique/E-commerce Premium -->
      <template v-if="isFashionStore">
        <!-- Header de Colección Fashion -->
        <div class="mb-6 pb-4 border-b border-gray-200 dark:border-zinc-800">
          <div class="flex items-center justify-between">
            <div>
              <h2 class="text-xl font-bold text-gray-900 dark:text-white tracking-tight">Colección</h2>
              <p class="text-sm text-gray-500 dark:text-zinc-400 mt-0.5">{{ filteredProducts.length }} prendas disponibles</p>
            </div>
            <!-- Selector de vista -->
            <div class="flex items-center gap-2 bg-gray-100 dark:bg-zinc-800 p-1 rounded-lg">
              <button 
                @click="fashionViewMode = 'grid'"
                class="p-2 rounded-md transition-all"
                :class="fashionViewMode === 'grid' ? 'bg-white dark:bg-zinc-700 shadow-sm text-gray-900 dark:text-white' : 'text-gray-500 dark:text-zinc-400 hover:text-gray-700'"
              >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                </svg>
              </button>
              <button 
                @click="fashionViewMode = 'list'"
                class="p-2 rounded-md transition-all"
                :class="fashionViewMode === 'list' ? 'bg-white dark:bg-zinc-700 shadow-sm text-gray-900 dark:text-white' : 'text-gray-500 dark:text-zinc-400 hover:text-gray-700'"
              >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
              </button>
            </div>
          </div>
        </div>

        <!-- 🎨 GRID FASHION - Estilo Lookbook (más columnas gracias al 70% de espacio) -->
        <div v-if="fashionViewMode === 'grid'" class="grid grid-cols-3 md:grid-cols-4 xl:grid-cols-5 gap-3 pb-20 content-start">
          <div
            v-for="(product, index) in filteredProducts"
            :key="product.id"
            class="group cursor-pointer"
            @click="addToCart(product)"
          >
            <!-- Card Minimalista Fashion - SIN fondo en contenedor principal -->
            <div class="relative overflow-hidden rounded-xl mb-2">
              
              <!-- Aspect ratio para fotos de moda - Fondo solo cuando NO hay imagen -->
              <div class="aspect-[4/5] relative" :class="(product.image_url || product.image) ? '' : 'bg-gray-100 dark:bg-zinc-900'">
                
                <!-- Badge NUEVO (para productos recientes) -->
                <div v-if="isNewProduct(product)" class="absolute top-2 left-2 z-20 px-2 py-0.5 bg-black dark:bg-white text-white dark:text-black text-[8px] font-bold uppercase tracking-wider rounded-full shadow-lg">
                  Nuevo
                </div>
                
                <!-- Badge cantidad en carrito - Estilo Fashion -->
                <div v-if="getProductQuantityInCart(product.id) > 0" 
                     class="absolute top-2 right-2 z-20 w-6 h-6 rounded-full bg-black dark:bg-white text-white dark:text-black text-xs font-bold flex items-center justify-center shadow-lg">
                  {{ getProductQuantityInCart(product.id) }}
                </div>
                
                <!-- Imagen principal - LLENA TODO EL ESPACIO -->
                <img
                  v-if="product.image_url || product.image"
                  :src="getProductImage(product)"
                  :alt="product.name"
                  class="w-full h-full object-cover rounded-xl transition-transform duration-700 ease-out group-hover:scale-105"
                  loading="lazy"
                  @error="(e) => handleImageError(e, product)"
                />
                
                <!-- Placeholder FASHION MEJORADO - Icono de prenda más visible -->
                <div v-else class="w-full h-full flex items-center justify-center bg-gradient-to-br from-gray-100 via-gray-50 to-white dark:from-zinc-800 dark:via-zinc-850 dark:to-zinc-900">
                  <div class="text-center">
                    <svg class="w-20 h-20 mx-auto text-gray-300 dark:text-zinc-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                    </svg>
                  </div>
                </div>
                
                <!-- Overlay con botón de agregar (aparece en hover) -->
                <div class="absolute inset-0 bg-black/0 group-hover:bg-black/20 transition-all duration-300 flex items-end justify-center pb-3 opacity-0 group-hover:opacity-100">
                  <button class="px-4 py-2 bg-white dark:bg-black text-black dark:text-white text-[10px] font-bold uppercase tracking-wider rounded-full shadow-xl transform translate-y-4 group-hover:translate-y-0 transition-transform duration-300 hover:bg-gray-100 dark:hover:bg-zinc-800">
                    + Agregar
                  </button>
                </div>
              </div>
            </div>
            
            <!-- Info del producto - Estilo minimalista -->
            <div class="space-y-0.5 px-0.5">
              <!-- Categoría -->
              <p class="text-[8px] font-semibold uppercase tracking-wider text-gray-400 dark:text-zinc-500 truncate">
                {{ product.category_name || 'Sin categoría' }}
              </p>
              
              <!-- Nombre -->
              <h3 class="text-xs font-medium text-gray-900 dark:text-white leading-tight line-clamp-1">
                {{ product.name }}
              </h3>
              
              <!-- Precio y Stock -->
              <div class="flex items-center justify-between">
                <span class="text-sm font-bold text-gray-900 dark:text-white">
                  ${{ product.price.toLocaleString() }}
                </span>
                <span v-if="getTotalStock(product) <= 5" class="text-[8px] font-semibold text-rose-500 dark:text-rose-400">
                  ¡{{ getTotalStock(product) }}!
                </span>
              </div>
            </div>
          </div>
        </div>
        
        <!-- 📋 LISTA FASHION - Vista detallada -->
        <div v-else class="space-y-3 pb-20">
          <div
            v-for="product in filteredProducts"
            :key="product.id"
            class="group flex gap-4 p-3 bg-white dark:bg-zinc-800/50 rounded-2xl border border-gray-200 dark:border-zinc-700/50 hover:border-gray-300 dark:hover:border-zinc-600 hover:shadow-lg transition-all duration-300 cursor-pointer"
            @click="addToCart(product)"
          >
            <!-- Imagen -->
            <div class="w-24 h-32 flex-shrink-0 rounded-xl overflow-hidden" :class="(product.image_url || product.image) ? '' : 'bg-gradient-to-br from-gray-100 via-gray-50 to-white dark:from-zinc-800 dark:via-zinc-850 dark:to-zinc-900'">
              <img
                v-if="product.image_url || product.image"
                :src="getProductImage(product)"
                :alt="product.name"
                class="w-full h-full object-cover"
                loading="lazy"
              />
              <div v-else class="w-full h-full flex items-center justify-center">
                <svg class="w-10 h-10 text-gray-300 dark:text-zinc-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                </svg>
              </div>
            </div>
            
            <!-- Info -->
            <div class="flex-1 flex flex-col justify-between py-1">
              <div>
                <p class="text-[10px] font-semibold uppercase tracking-wider text-gray-400 dark:text-zinc-500 mb-1">
                  {{ product.category_name || 'Sin categoría' }}
                </p>
                <h3 class="text-sm font-semibold text-gray-900 dark:text-white leading-snug">
                  {{ product.name }}
                </h3>
              </div>
              
              <div class="flex items-center justify-between">
                <span class="text-lg font-bold text-gray-900 dark:text-white">
                  ${{ product.price.toLocaleString() }}
                </span>
                <div class="flex items-center gap-2">
                  <span class="text-xs text-gray-500 dark:text-zinc-400">
                    Stock: {{ getTotalStock(product) }}
                  </span>
                  <!-- Badge si está en carrito -->
                  <span v-if="getProductQuantityInCart(product.id) > 0" class="px-2 py-1 bg-black dark:bg-white text-white dark:text-black text-[10px] font-bold rounded-full">
                    {{ getProductQuantityInCart(product.id) }} en carrito
                  </span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </template>
      
      <!-- 📦 GRID MODO GENERAL - Compacto y funcional (DISEÑO ACTUAL) -->
      <div v-else class="grid grid-cols-[repeat(auto-fill,minmax(150px,1fr))] gap-3 pb-20 min-h-[600px] content-start">
        <div
          v-for="product in filteredProducts"
          :key="product.id"
          class="group bg-white dark:bg-zinc-800 rounded-xl overflow-hidden cursor-pointer transition-all duration-200 active:scale-[0.98] border border-gray-200 dark:border-zinc-700 hover:ring-2 hover:ring-emerald-500/70 dark:hover:ring-emerald-400/70 hover:border-transparent shadow-sm hover:shadow-lg dark:shadow-black/20"
          @click="addToCart(product)"
        >
          
          <!-- 🖼️ CONTENEDOR DE IMAGEN - OPTIMIZADO: Mayor área visual (p-2 → p-1) -->
          <div class="aspect-square bg-gray-50 dark:bg-zinc-900/50 relative flex items-center justify-center p-1 border-b border-gray-100 dark:border-zinc-700">
             
             <!-- Badge de cantidad en carrito (gris oscuro profesional - menos ruido visual) -->
             <div v-if="getProductQuantityInCart(product.id) > 0" 
                  class="absolute -top-0.5 -right-0.5 z-10 w-6 h-6 rounded-full bg-slate-800 dark:bg-zinc-200 text-white dark:text-zinc-900 text-[11px] font-bold shadow-md ring-2 ring-white dark:ring-zinc-800 flex items-center justify-center">
               {{ getProductQuantityInCart(product.id) }}
             </div>
             
             <!-- Badge discreto de tienda (esquina superior izquierda, solo para productos remotos) -->
             <div v-if="product.is_remote && product.alternative_warehouses && product.alternative_warehouses.length > 0" 
                  class="absolute top-2 left-2 z-10 px-1.5 py-0.5 rounded bg-amber-500 text-white text-[8px] font-bold shadow-sm">
               {{ product.alternative_warehouses[0].name }}
             </div>

             <!-- Imagen AMPLIADA - Ocupa más espacio del card -->
             <img
              v-if="product.image_url || product.image"
              :src="getProductImage(product)"
              :alt="product.name"
              class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105"
              loading="lazy"
              @error="(e) => handleImageError(e, product)"
            />
            <!-- Placeholder FASHION - Silueta de prenda en lugar de caja genérica -->
            <div v-else class="w-full h-full flex items-center justify-center bg-gradient-to-br from-gray-100 to-gray-50 dark:from-zinc-800 dark:to-zinc-900">
              <!-- Icono de prenda (camiseta/vestido) -->
              <svg class="w-16 h-16 text-gray-300 dark:text-zinc-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1">
                <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
              </svg>
            </div>
          </div>

          <!-- 📝 INFORMACIÓN - Altura fija para uniformidad -->
          <div class="p-2.5 h-[72px] flex flex-col justify-between bg-white dark:bg-zinc-800">
            
            <!-- PRECIO PROTAGONISTA -->
            <span class="text-lg font-bold text-slate-900 dark:text-white leading-none">
              ${{ product.price.toLocaleString() }}
            </span>
            
            <!-- Nombre del producto (2 líneas máximo con line-clamp) -->
            <h3 class="text-sm font-medium text-gray-700 dark:text-zinc-300 leading-tight line-clamp-2" :title="product.name">
              {{ product.name }}
            </h3>
            
            <!-- Stock discreto -->
            <span class="text-xs" :class="getTotalStock(product) <= 5 ? 'text-rose-500 dark:text-rose-400 font-medium' : 'text-gray-400 dark:text-zinc-500'">
              {{ getTotalStock(product) <= 5 ? '¡Solo ' + getTotalStock(product) + '!' : 'Stock: ' + getTotalStock(product) }}
            </span>
          </div>
        </div>
      </div>

    </div>
  </div>
</div>
    

<!-- bloque de ventas - 30% Fashion (4/12) | Panel Central General (3/12) -->

<div :class="isFashionStore ? 'lg:col-span-4' : 'lg:col-span-3'" class="h-full overflow-hidden transition-all duration-300">
  <div class="bg-white/80 dark:bg-zinc-900/90 backdrop-blur-sm rounded-2xl shadow-lg dark:shadow-2xl dark:shadow-black/40 border border-gray-200/50 dark:border-zinc-800/60 h-full flex flex-col overflow-hidden transition-all duration-300">
    
    <div class="p-4 flex-shrink-0">
      <div class="flex items-center justify-between mb-3">
        <h2 class="text-xs font-black text-slate-800 dark:text-zinc-200 uppercase tracking-widest flex items-center gap-2">
          <span class="w-1.5 h-5 bg-indigo-600 dark:bg-indigo-500 rounded-full"></span>
          {{ loadedQuotation ? 'Cotización #' + loadedQuotation.code : quotationMode ? 'Nueva Cotización' : 'Ticket de Venta' }}
        </h2>
        
        <span v-if="loadedQuotation || quotationMode" class="px-2 py-1 rounded text-[9px] font-bold uppercase bg-amber-50 dark:bg-amber-950/30 text-amber-600 dark:text-amber-400 border border-amber-100 dark:border-amber-800 tracking-wide">
          Modo Edición
        </span>
      </div>

      <!-- Botón Cliente (COMPLETO - Modo General) -->
      <button
        id="tour-customer-btn"
        @click="showCustomerSelector = true"
        class="w-full flex items-center justify-between p-3 rounded-xl transition-all duration-200 group active:scale-[0.99] border-2 border-dashed"
        :class="selectedCustomer 
          ? 'bg-white dark:bg-zinc-800 border-indigo-100 dark:border-indigo-800 hover:border-indigo-300 dark:hover:border-indigo-600 shadow-sm border-solid' 
          : 'bg-slate-50 dark:bg-zinc-800/50 border-slate-300 dark:border-zinc-700 hover:border-indigo-400 dark:hover:border-indigo-600 hover:bg-indigo-50/30 dark:hover:bg-indigo-950/30'"
      >
        <div class="flex items-center gap-3 overflow-hidden">
          <!-- Icono Dinámico -->
          <div class="w-10 h-10 rounded-full flex items-center justify-center transition-colors shadow-sm"
               :class="selectedCustomer 
                 ? 'bg-indigo-50 dark:bg-indigo-950/50 text-indigo-600 dark:text-indigo-400 border border-indigo-100 dark:border-indigo-800' 
                 : 'bg-white dark:bg-zinc-800 text-slate-400 dark:text-zinc-500 border border-slate-300 dark:border-zinc-700 group-hover:text-indigo-500 dark:group-hover:text-indigo-400 group-hover:border-indigo-300 dark:group-hover:border-indigo-700'">
            
            <!-- Icono Usuario (Selected) -->
            <svg v-if="selectedCustomer" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
            
            <!-- Icono Agregar (Empty) -->
            <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
          </div>

          <div class="flex flex-col text-left overflow-hidden flex-1">
             <span class="text-[10px] font-bold uppercase tracking-wider"
                   :class="selectedCustomer ? 'text-indigo-500 dark:text-indigo-400' : 'text-slate-500 dark:text-zinc-500'">
               {{ selectedCustomer ? 'Cliente Asignado' : 'Asignar Cliente' }}
             </span>
             <span class="text-sm font-bold truncate transition-colors"
                   :class="selectedCustomer ? 'text-slate-900 dark:text-zinc-100' : 'text-slate-400 dark:text-zinc-500 group-hover:text-indigo-600 dark:group-hover:text-indigo-400'">
               {{ selectedCustomer ? selectedCustomer.name : 'Seleccionar o Crear...' }}
             </span>
             
             <!-- 🎁 Badge de Puntos de Fidelización -->
             <div v-if="selectedCustomer && loyaltyEnabled && (selectedCustomer.loyalty_points || 0) > 0" 
                  class="flex items-center gap-1.5 mt-1">
               <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-gradient-to-r from-amber-50 to-yellow-50 text-amber-700 text-[10px] font-bold rounded-full border border-amber-200">
                 <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                   <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                 </svg>
                 {{ selectedCustomer.loyalty_points }}
               </span>
               <span class="text-[9px] text-amber-600 font-semibold">
                 {{ formatPointsAsMoney(selectedCustomer.loyalty_points) }}
               </span>
             </div>
          </div>
        </div>
        
        <!-- Flecha o Plus -->
        <div class="text-slate-300 dark:text-zinc-500 group-hover:text-indigo-500 dark:group-hover:text-indigo-400 transition-transform group-hover:translate-x-1">
           <svg v-if="selectedCustomer" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
           <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
        </div>
      </button>
    </div>

    <div class="flex-1 overflow-y-auto bg-white dark:bg-zinc-900 relative" style="scrollbar-width: thin; scrollbar-color: #cbd5e1 transparent;">
      
      <div v-if="cart.items.length === 0" class="absolute inset-0 flex flex-col items-center justify-center text-center opacity-50 pointer-events-none">
        <div class="w-20 h-20 bg-gray-50 dark:bg-zinc-800 rounded-full flex items-center justify-center mb-3 border border-gray-200 dark:border-zinc-700">
          <svg class="w-8 h-8 text-slate-400 dark:text-zinc-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
        </div>
        <h3 class="text-sm font-bold text-slate-600 dark:text-zinc-500">Carrito Vacío</h3>
        <p class="text-xs text-slate-500 dark:text-zinc-600 mt-1">Escanea productos para comenzar</p>
      </div>

      <!-- 🛒 LISTA DE ITEMS REDISEÑADA - Compacta para Alto Tráfico -->
      <div v-else class="divide-y divide-gray-100 dark:divide-zinc-800">
        <div
          v-for="(item, index) in cart.items"
          :key="item.id"
          class="group relative flex items-center gap-2.5 px-3 py-2 hover:bg-gray-50/50 dark:hover:bg-zinc-800/50 transition-colors"
        >
          <!-- 🖼️ Miniatura compacta -->
          <div class="w-10 h-10 rounded-lg bg-gray-50 dark:bg-zinc-800 border border-gray-200 dark:border-zinc-700 overflow-hidden flex-shrink-0 flex items-center justify-center p-1">
             <img 
               :src="getCartItemImage(item)" 
               class="max-w-full max-h-full object-contain"
               @error="(e) => handleImageError(e, item)"
               :alt="item.name"
             />
          </div>

          <!-- 📝 Info del producto -->
          <div class="flex-1 min-w-0">
            <h3 class="text-sm font-semibold text-slate-800 dark:text-zinc-200 truncate leading-tight" :title="item.name">
              {{ item.name.split(' (')[0] }}
            </h3>
            <!-- Variantes con círculos de color -->
            <div v-if="item.variant_options" class="flex items-center gap-1.5 mt-0.5">
              <template v-for="(option, idx) in parseVariantOptions(item.variant_options)" :key="idx">
                <!-- Si es COLOR, mostrar círculo -->
                <div v-if="option.type === 'COLOR'" 
                     class="w-4 h-4 rounded-full border-2 border-white dark:border-zinc-700 shadow-sm ring-1 ring-gray-300 dark:ring-zinc-600" 
                     :style="{ backgroundColor: option.value }"
                     :title="option.label">
                </div>
                <!-- Si es TALLA u otro, mostrar texto -->
                <span v-else class="text-[10px] font-bold text-gray-600 dark:text-zinc-400 uppercase px-1.5 py-0.5 bg-gray-100 dark:bg-zinc-800 rounded border border-gray-200 dark:border-zinc-700">
                  {{ option.value }}
                </span>
              </template>
            </div>
            <p v-else class="text-xs text-gray-400 dark:text-zinc-500 mt-0.5 tabular-nums">
              ${{ item.price.toLocaleString() }}/{{ getUnitText(item.measurement_unit || 'unit') }}
            </p>
          </div>

          <!-- 💰 Precios alineados a la derecha -->
          <div class="text-right flex-shrink-0 mr-1">
            <p class="text-base font-bold text-slate-900 dark:text-white tabular-nums leading-tight">
              ${{ (item.price * item.quantity).toLocaleString() }}
            </p>
          </div>

          <!-- 🎛️ STEPPER - Control de cantidad táctil -->
          <div class="flex items-center border border-gray-200 dark:border-zinc-700 rounded-lg overflow-hidden bg-white dark:bg-zinc-800 shadow-sm flex-shrink-0">
            <button 
              @click.stop="updateQuantity(item.id, item.quantity - 1)" 
              class="w-8 h-8 flex items-center justify-center text-gray-500 dark:text-zinc-400 hover:bg-gray-100 dark:hover:bg-zinc-700 hover:text-rose-600 dark:hover:text-rose-400 transition-colors font-medium text-lg"
            >−</button>
            <span class="w-8 h-8 flex items-center justify-center text-sm font-bold text-slate-900 dark:text-white bg-gray-50 dark:bg-zinc-900 tabular-nums select-none border-x border-gray-200 dark:border-zinc-700">
              {{ item.quantity % 1 === 0 ? item.quantity : item.quantity.toFixed(1) }}
            </span>
            <button 
              @click.stop="updateQuantity(item.id, item.quantity + 1)" 
              class="w-8 h-8 flex items-center justify-center text-gray-500 dark:text-zinc-400 hover:bg-emerald-50 dark:hover:bg-emerald-950/30 hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors font-medium text-lg"
            >+</button>
          </div>

          <!-- ❌ Botón eliminar (aparece en hover) -->
          <button
            @click.stop="removeFromCart(item.id)"
            class="absolute top-2 right-2 w-5 h-5 bg-white dark:bg-zinc-700 border border-gray-200 dark:border-zinc-600 text-gray-400 dark:text-zinc-500 rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition-all hover:bg-rose-500 hover:border-rose-500 hover:text-white z-10"
            title="Quitar"
          >
            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path></svg>
          </button>
        </div>
      </div>
    </div>
    
    <!-- 👗 SECCIÓN DE PAGO INTEGRADA - Solo en modo Fashion -->
    <div v-if="isFashionStore" class="flex-shrink-0 border-t border-gray-200 dark:border-zinc-800 bg-gray-50 dark:bg-zinc-900/50">
      
      <!-- Total compacto -->
      <div class="px-2.5 py-2 border-b border-gray-100 dark:border-zinc-800">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-[8px] font-semibold text-slate-500 dark:text-zinc-500 uppercase tracking-wide">Total</p>
            <h3 class="text-xl font-black text-slate-900 dark:text-white">${{ total.toLocaleString() }}</h3>
            <p class="text-[9px] text-slate-400 dark:text-zinc-500">
              Sub: ${{ subtotal.toLocaleString() }} • {{ systemSettings.iva_display_name || 'IVA' }}: ${{ tax.toLocaleString() }}
            </p>
          </div>
          <div class="text-right">
            <span class="text-2xl font-black text-slate-300 dark:text-zinc-600">{{ totalItems }}</span>
            <p class="text-[8px] text-slate-400 dark:text-zinc-500 uppercase">items</p>
          </div>
        </div>
      </div>
      
      <!-- Métodos de pago compactos -->
      <div class="px-2.5 py-2 space-y-1.5">
        
        <!-- Descuento Aplicado -->
        <div v-if="discount > 0" class="bg-emerald-50 dark:bg-emerald-950/30 rounded-md px-2 py-1.5 border border-emerald-200 dark:border-emerald-800 flex items-center justify-between">
          <span class="text-[10px] font-bold text-emerald-700 dark:text-emerald-400 flex items-center gap-1">
            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
            Descuento
          </span>
          <span class="text-xs font-black text-emerald-700 dark:text-emerald-400">-${{ discount.toLocaleString() }}</span>
        </div>
        
        <!-- Métodos de Pago -->
        <div class="grid grid-cols-3 gap-1">
          <button 
            v-for="method in paymentMethods.slice(0, 3)" 
            :key="method.id"
            @click="selectedPaymentMethod = method.id"
            class="px-1.5 py-2 rounded-md text-[9px] font-bold uppercase transition-all flex flex-col items-center justify-center gap-0.5 border"
            :class="selectedPaymentMethod === method.id 
              ? 'bg-black dark:bg-white text-white dark:text-black border-black dark:border-white shadow-md' 
              : 'bg-white dark:bg-zinc-800 text-gray-600 dark:text-zinc-300 border-gray-200 dark:border-zinc-700 hover:border-gray-400 dark:hover:border-zinc-500'"
          >
            <svg v-if="method.id === 'efectivo'" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
            <svg v-else-if="method.id === 'tarjeta' || method.id === 'card'" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
            <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
            <span class="leading-none">{{ method.name.split(' ')[0] }}</span>
          </button>
        </div>
        
        <!-- Puntos de Fidelización -->
        <div v-if="canUseLoyaltyPoints" class="mb-1.5">
          <button
            @click="usePoints = !usePoints"
            class="w-full text-xs font-bold flex items-center justify-center gap-2 px-3 py-2.5 rounded-lg border transition-all"
            :class="usePoints ? 'bg-amber-500 text-white border-amber-500 shadow-md' : 'bg-white dark:bg-zinc-800 border-gray-200 dark:border-zinc-700 text-gray-600 dark:text-zinc-400 hover:border-amber-400 dark:hover:border-amber-600'"
          >
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
              <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
            </svg>
            <span>Usar Puntos de Fidelización</span>
          </button>
        </div>
        
        <!-- Botones de acción -->
        <div class="flex gap-2 pt-0.5">
          <button 
            @click="showConfirmClearCart" 
            :disabled="cart.items.length === 0"
            class="px-4 py-2.5 text-sm font-bold bg-white dark:bg-zinc-800 text-gray-700 dark:text-zinc-300 rounded-lg border border-gray-300 dark:border-zinc-700 hover:bg-gray-50 dark:hover:bg-zinc-700 hover:border-gray-400 dark:hover:border-zinc-600 disabled:opacity-40 disabled:cursor-not-allowed transition-all shadow-sm"
          >
            Cancelar
          </button>
          
          <button 
            @click="printQuote" 
            :disabled="!canCreateQuotation"
            class="px-4 py-2.5 text-sm font-bold bg-white dark:bg-zinc-800 text-gray-700 dark:text-zinc-300 rounded-lg border border-gray-300 dark:border-zinc-700 hover:bg-gray-50 dark:hover:bg-zinc-700 hover:border-gray-400 dark:hover:border-zinc-600 disabled:opacity-40 disabled:cursor-not-allowed transition-all flex items-center justify-center gap-1.5 shadow-sm"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            <span>Cotizar</span>
          </button>
          
          <button
            @click="handleCobrarClick"
            :disabled="!canShowPaymentModal || quotationMode"
            class="flex-1 py-4 rounded-xl font-black text-lg transition-all flex items-center justify-center gap-2.5 shadow-lg"
            :class="canShowPaymentModal && !quotationMode
              ? 'bg-emerald-600 dark:bg-emerald-500 text-white hover:bg-emerald-700 dark:hover:bg-emerald-400 shadow-emerald-600/40 hover:shadow-xl transform hover:scale-[1.02] active:scale-[0.98]'
              : 'bg-gray-200 dark:bg-zinc-700 text-gray-400 dark:text-zinc-500 cursor-not-allowed shadow-none'"
          >
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <span class="font-black">Cobrar ${{ total.toLocaleString() }}</span>
          </button>
        </div>
      </div>
    </div>
    
  </div>
</div>

<!-- fin bloque de ventas -->  

<!-- Panel de Pagos - Solo visible en modo General (3/12) - Se oculta en Fashion -->
<div v-if="!isFashionStore" id="tour-pos-cart" class="lg:col-span-3 h-full overflow-hidden">
  
  <div class="bg-white/80 dark:bg-zinc-900/90 backdrop-blur-sm rounded-2xl border border-gray-200/50 dark:border-zinc-800/60 h-full flex flex-col justify-between shadow-lg dark:shadow-2xl dark:shadow-black/40 transition-all duration-300">
    
    <!-- 💰 HEADER COMPACTO: Total Protagonista -->
    <div class="p-3 flex-shrink-0 border-b border-gray-100 dark:border-zinc-800">
      <div class="flex items-end justify-between">
        <div>
          <p class="text-[9px] font-semibold text-slate-500 dark:text-zinc-500 uppercase tracking-wide">Total a Pagar</p>
          <h2 class="text-4xl font-black text-slate-900 dark:text-white tracking-tight leading-none mt-0.5">${{ total.toLocaleString() }}</h2>
          <!-- Subtotal e IVA en una línea -->
          <p class="text-[10px] text-slate-500 dark:text-zinc-400 mt-1">
            Sub: ${{ subtotal.toLocaleString() }} 
            <span class="mx-1">•</span> 
            <span v-if="displayTaxRate !== null">{{ systemSettings.iva_display_name || 'IVA' }} ({{ displayTaxRate }}%): ${{ tax.toLocaleString() }}</span>
            <span v-else>{{ systemSettings.iva_display_name || 'IVA' }}: ${{ tax.toLocaleString() }}</span>
          </p>
        </div>
        <div class="text-right">
          <p class="text-[9px] font-semibold text-slate-500 dark:text-zinc-400 uppercase tracking-wide">Items</p>
          <p class="text-xl font-black text-slate-900 dark:text-white">{{ totalItems }}</p>
        </div>
      </div>
    </div>
    
    <!-- 📦 ÁREA DE CONTENIDO CENTRAL con scroll optimizado -->
    <div class="flex-1 overflow-y-auto px-3 py-2 space-y-2 bg-gray-50/50 dark:bg-zinc-900/30"
         style="scrollbar-width: thin; scrollbar-color: #cbd5e1 transparent;">

        <!-- 🎯 CONFIGURACIÓN DE PAGO - Solo visible cuando hay items en el carrito -->
        <div v-if="cart.items.length > 0" class="space-y-2">
        
        <!-- Descuento Aplicado (Solo si existe) -->
        <div v-if="discount > 0" class="bg-gradient-to-r from-emerald-50 to-green-50 dark:from-emerald-950/20 dark:to-green-950/20 rounded-lg px-2.5 py-1.5 border border-emerald-200 dark:border-emerald-800 flex items-center justify-between">
          <span class="text-[10px] font-bold text-emerald-700 dark:text-emerald-400 flex items-center gap-1">
            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path></svg>
            Descuento
          </span>
          <span class="text-xs font-black text-emerald-700 dark:text-emerald-400">-${{ discount.toLocaleString() }}</span>
        </div>

        <!-- MÉTODOS DE PAGO -->
        <div class="bg-gray-100 dark:bg-zinc-800 rounded-lg p-1">
          <div v-if="paymentMethods.length > 0" class="flex gap-1">
             <button 
                v-for="method in paymentMethods" 
                :key="method.id"
                @click="selectedPaymentMethod = method.id"
                class="flex-1 px-2 py-2 rounded-md text-[10px] font-bold uppercase transition-all flex flex-col items-center justify-center gap-1"
                :class="selectedPaymentMethod === method.id 
                  ? 'bg-slate-800 dark:bg-slate-700 text-white shadow-sm' 
                  : 'bg-white dark:bg-zinc-700 text-gray-600 dark:text-zinc-300 hover:bg-gray-50 dark:hover:bg-zinc-600'"
             >
                <svg v-if="method.id === 'efectivo'" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                <span class="leading-none tracking-wide">{{ method.name.replace(/\s*(crédito|débito|bancaria|de|\/)\s*/gi, ' ').trim() }}</span>
             </button>
          </div>
          <p v-else class="text-[10px] text-gray-500 italic p-2">Cargando métodos...</p>
        </div>

        <!-- PUNTOS Y CUPONES -->
        <div class="bg-white dark:bg-zinc-800 rounded-lg border border-gray-200 dark:border-zinc-700 p-2">
          <div class="flex gap-1.5">
            <!-- Botón Usar Puntos -->
            <button
              v-if="canUseLoyaltyPoints"
              @click="usePoints = !usePoints"
              class="flex-1 text-[9px] font-bold flex items-center justify-center gap-1 transition-all uppercase tracking-wide px-2 py-1.5 rounded-lg border"
              :class="usePoints ? 'bg-slate-700 dark:bg-slate-600 text-white border-slate-700' : 'bg-gray-50 dark:bg-zinc-700 border-gray-200 dark:border-zinc-600 text-gray-600 dark:text-zinc-300 hover:bg-gray-100 dark:hover:bg-zinc-600'"
            >
              <svg class="w-3 h-3" :class="usePoints ? 'text-amber-300' : 'text-gray-400 dark:text-zinc-500'" fill="currentColor" viewBox="0 0 20 20">
                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
              </svg>
              Puntos
            </button>

            <!-- Botón Cupón -->
            <button
              id="tour-discount-btn"
              @click="showPromoCodeInput = !showPromoCodeInput"
              class="flex-1 text-[9px] font-bold flex items-center justify-center gap-1 transition-all uppercase tracking-wide px-2 py-1.5 rounded-lg border"
              :class="showPromoCodeInput ? 'bg-slate-700 dark:bg-slate-600 text-white border-slate-700' : 'bg-gray-50 dark:bg-zinc-700 border-gray-200 dark:border-zinc-600 text-gray-600 dark:text-zinc-300 hover:bg-gray-100 dark:hover:bg-zinc-600'"
            >
              <svg class="w-3 h-3 text-gray-400 dark:text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
              </svg>
              Cupón
            </button>
          </div>

          <!-- Inputs Expandibles -->
          <div v-if="usePoints && canUseLoyaltyPoints" class="mt-1.5 animate-fade-in">
            <div class="flex gap-1">
              <input 
                type="number" 
                v-model.number="pointsToRedeem"
                :max="maxPointsToUse"
                min="0"
                class="flex-1 px-2 py-1 border rounded-lg text-[10px] font-bold bg-white dark:bg-zinc-900 border-gray-200 dark:border-zinc-700 text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-zinc-500 focus:ring-1 focus:ring-yellow-400"
                placeholder="Puntos"
              />
              <button 
                @click="pointsToRedeem = maxPointsToUse"
                class="px-2 py-1 bg-slate-700 dark:bg-zinc-700 hover:bg-slate-800 text-white text-[9px] font-bold rounded-lg"
              >
                Máx
              </button>
              <button 
                @click="usePoints = false; pointsToRedeem = 0" 
                class="px-1.5 text-slate-500 dark:text-zinc-400 hover:text-slate-700 dark:hover:text-zinc-300 rounded-lg"
              >
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path></svg>
              </button>
            </div>
            <p v-if="pointsDiscount > 0" class="text-[9px] text-emerald-600 dark:text-emerald-400 mt-1 font-bold">✓ Descuento: -${{ pointsDiscount.toLocaleString() }}</p>
          </div>

          <div v-if="showPromoCodeInput" class="mt-1.5 animate-fade-in">
            <div class="flex gap-1.5">
              <input
                v-model="promoCode"
                type="text"
                placeholder="CÓDIGO PROMOCIONAL"
                class="flex-1 px-3 py-2.5 text-sm font-bold border rounded-lg uppercase bg-white dark:bg-zinc-900 border-gray-200 dark:border-zinc-700 text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-zinc-500 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none tracking-wide"
                @keyup.enter="applyPromoCode"
              />
              <button
                @click="applyPromoCode"
                :disabled="!promoCode.trim()"
                class="px-4 py-2.5 bg-slate-700 dark:bg-zinc-700 hover:bg-slate-800 disabled:bg-slate-300 dark:disabled:bg-zinc-700 disabled:cursor-not-allowed text-white text-xs font-bold rounded-lg transition-all"
              >
                Aplicar
              </button>
              <button 
                @click="showPromoCodeInput = false; promoCode = ''; promoError = ''" 
                class="px-2 text-slate-500 dark:text-zinc-400 hover:text-slate-700 dark:hover:text-zinc-300 rounded-lg transition-colors"
              >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path></svg>
              </button>
            </div>
            <p v-if="promoError" class="text-xs text-rose-600 dark:text-rose-400 mt-1.5 font-semibold">⚠️ {{ promoError }}</p>
            <p v-if="discount > 0 && !promoError" class="text-xs text-emerald-600 dark:text-emerald-400 mt-1.5 font-semibold">✓ Código aplicado: -${{ discount.toLocaleString() }}</p>
          </div>
        </div>

        <!-- INPUT DE EFECTIVO -->
        <div v-if="selectedPaymentMethod === 'efectivo'" class="animate-fade-in-up bg-white dark:bg-zinc-800 rounded-lg border border-gray-200 dark:border-zinc-700 p-3">
           <label class="block text-[10px] font-bold text-gray-500 dark:text-zinc-400 uppercase tracking-wider mb-2">Dinero Recibido</label>
           
           <!-- Input Principal -->
           <div class="relative mb-3">
              <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 dark:text-zinc-500 font-bold text-xl">$</span>
              <input
                v-model="cashReceived"
                type="number"
                step="1000"
                :min="total"
                class="w-full pl-10 pr-12 py-2.5 text-2xl font-black rounded-lg bg-gray-50 dark:bg-zinc-900 text-slate-900 dark:text-white placeholder-gray-300 dark:placeholder-zinc-600 border border-gray-200 dark:border-zinc-700 focus:border-slate-400 dark:focus:border-zinc-500 focus:ring-2 focus:ring-slate-200 dark:focus:ring-zinc-700 transition-all tabular-nums"
                :placeholder="total.toString()"
                @focus="$event.target.select()"
              />
              <button @click="showCalculatorModal" class="absolute right-2 top-1/2 -translate-y-1/2 p-2 text-gray-400 dark:text-zinc-500 hover:text-gray-600 dark:hover:text-zinc-300 hover:bg-gray-100 dark:hover:bg-zinc-800 rounded-md transition-colors">
                 <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
              </button>
           </div>
           
           <!-- BOTONES DE MONTO RÁPIDO - Neutros y limpios -->
           <div class="grid grid-cols-4 gap-1.5 mb-2">
              <button 
                 v-for="amount in [2000, 5000, 10000, 20000, 50000, 100000]" 
                 :key="amount"
                 @click="cashReceived = amount"
                 class="h-9 text-xs font-semibold rounded-md border transition-all active:scale-95"
                 :class="cashReceived === amount 
                   ? 'bg-slate-800 dark:bg-slate-600 text-white border-slate-800 dark:border-slate-600' 
                   : 'bg-white dark:bg-zinc-700 text-gray-700 dark:text-zinc-300 border-gray-200 dark:border-zinc-600 hover:bg-gray-50 dark:hover:bg-zinc-600'"
              >
                 ${{ (amount / 1000).toFixed(0) }}k
              </button>
              
              <!-- Botón EXACTO - Neutro -->
              <button 
                 @click="cashReceived = total"
                 class="h-9 text-xs font-semibold rounded-md border transition-all active:scale-95"
                 :class="cashReceived === total 
                   ? 'bg-slate-800 dark:bg-slate-600 text-white border-slate-800 dark:border-slate-600' 
                   : 'bg-gray-100 dark:bg-zinc-700 text-gray-700 dark:text-zinc-300 border-gray-200 dark:border-zinc-600 hover:bg-gray-200 dark:hover:bg-zinc-600'"
              >
                 Exacto
              </button>
              
              <!-- Botón BORRAR - Sutil -->
              <button 
                 @click="cashReceived = 0"
                 class="h-9 text-xs font-semibold rounded-md border border-gray-200 dark:border-zinc-600 bg-white dark:bg-zinc-700 text-gray-500 dark:text-zinc-400 hover:bg-gray-50 dark:hover:bg-zinc-600 hover:text-red-500 dark:hover:text-red-400 transition-all active:scale-95 flex items-center justify-center gap-1"
              >
                 <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2M3 12l6.414 6.414a2 2 0 001.414.586H19a2 2 0 002-2V7a2 2 0 00-2-2h-8.172a2 2 0 00-1.414.586L3 12z"/></svg>
                 <span>C</span>
              </button>
           </div>
           
           <!-- INDICADOR DE CAMBIO/FALTA - Limpio y sutil -->
           <div v-if="cashReceived" class="rounded-md px-3 py-2 transition-all bg-gray-50 dark:bg-zinc-900 border border-gray-100 dark:border-zinc-700">
              <div class="flex items-center justify-between">
                 <span class="text-[10px] font-medium uppercase tracking-wide flex items-center gap-1"
                       :class="cashReceived >= total ? 'text-gray-500 dark:text-zinc-400' : 'text-gray-500 dark:text-zinc-400'">
                    <svg v-if="cashReceived < total" class="w-3 h-3 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    {{ cashReceived >= total ? 'Su cambio' : 'Falta' }}
                 </span>
                 <span 
                    class="font-black tabular-nums"
                    :class="cashReceived >= total 
                      ? 'text-2xl text-emerald-600 dark:text-emerald-400' 
                      : 'text-lg text-red-600 dark:text-red-400'"
                 >
                    ${{ Math.abs(cashReceived - total).toLocaleString() }}
                 </span>
              </div>
           </div>
        </div>
        
        </div>
        <!-- FIN CONFIGURACIÓN DE PAGO -->

    </div>

    <!-- FOOTER DE ACCIÓN -->
    <div class="p-2.5 bg-gray-50 dark:bg-zinc-800 border-t border-gray-100 dark:border-zinc-700 flex-shrink-0 space-y-2 mt-auto">
       
       <!-- Botones Secundarios - NEUTROS -->
       <div class="flex gap-2">
          <button @click="holdSale" :disabled="cart.items.length === 0" 
                  class="flex-1 h-9 text-[10px] font-semibold bg-white dark:bg-zinc-700 text-gray-600 dark:text-zinc-300 hover:bg-gray-50 dark:hover:bg-zinc-600 rounded-lg transition-all flex items-center justify-center gap-1.5 disabled:opacity-40 border border-gray-200 dark:border-zinc-600">
             <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
             <span>Cancelar</span>
          </button>
          
          <button @click="printQuote" :disabled="!canCreateQuotation" 
                  class="flex-1 h-9 text-[10px] font-semibold bg-gray-100 dark:bg-zinc-700 text-gray-700 dark:text-zinc-300 hover:bg-gray-200 dark:hover:bg-zinc-600 rounded-lg transition-all flex items-center justify-center gap-1.5 disabled:opacity-40 border border-gray-200 dark:border-zinc-600">
             <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
             <span>Cotizar</span>
          </button>
       </div>

       <!-- BOTÓN COBRAR - Verde SOLO cuando está listo (MÁS GRANDE Y DESTACADO) -->
       <button
          @click="handleCobrarClick"
          :disabled="!canShowPaymentModal || quotationMode || (selectedPaymentMethod === 'efectivo' && total > 0 && (!cashReceived || cashReceived < total))"
          class="w-full h-16 rounded-2xl font-black text-lg shadow-2xl transform active:scale-[0.98] transition-all duration-200 flex items-center justify-center gap-3 group relative overflow-hidden"
          :class="[
            (!canShowPaymentModal || quotationMode)
              ? 'bg-slate-300 dark:bg-zinc-700 text-slate-500 dark:text-zinc-400 cursor-not-allowed shadow-none'
            : (selectedPaymentMethod === 'efectivo' && total > 0 && (!cashReceived || cashReceived < total))
              ? 'bg-slate-400 dark:bg-zinc-600 text-slate-600 dark:text-zinc-300 cursor-not-allowed shadow-none'
              : 'bg-emerald-600 dark:bg-emerald-500 text-white hover:bg-emerald-700 dark:hover:bg-emerald-400 shadow-emerald-600/40 hover:shadow-emerald-600/60 hover:shadow-2xl cobrar-ready-pulse'
          ]"
        >
          <!-- Efecto de brillo animado cuando está listo -->
          <div 
            v-if="canShowPaymentModal && !quotationMode && !(selectedPaymentMethod === 'efectivo' && total > 0 && (!cashReceived || cashReceived < total))" 
            class="absolute inset-0 bg-gradient-to-r from-transparent via-white/20 to-transparent -translate-x-full animate-shimmer"
          ></div>

          <span v-if="cart.items.length === 0" class="text-sm font-bold opacity-80">
             Agrega productos
          </span>

          <span v-else-if="!selectedPaymentMethod" class="text-sm font-bold uppercase tracking-wide">
             Selecciona método de pago
          </span>

          <span v-else-if="selectedPaymentMethod === 'efectivo' && (!cashReceived || cashReceived < total)" class="flex items-center gap-2">
             <svg class="w-5 h-5 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
             <span class="text-sm font-bold uppercase tracking-wide">Ingresa efectivo</span>
          </span>

          <span v-else class="flex items-center justify-between w-full px-4">
             <span class="flex items-center gap-3">
               <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
               <span class="text-xl font-black tracking-wide uppercase">Cobrar Ahora</span>
             </span>
             <span class="flex items-center gap-2 text-2xl font-black tabular-nums">
               ${{ total.toLocaleString() }}
               <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
             </span>
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
      :start-creating="startCustomerCreation"
      @select="selectCustomer"
      @view-history="handleCustomerHistory"
      @close="showCustomerSelector = false; startCustomerCreation = false"
    />

    <!-- Modal de Cantidad con Unidades de Medida -->
    <div v-if="showQuantityModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center z-50 p-4">
      <div class="bg-white dark:bg-zinc-800 rounded-2xl shadow-2xl max-w-md w-full">
        <!-- Header -->
        <div class="bg-white dark:bg-zinc-800 border-b border-gray-200 dark:border-zinc-700 px-6 py-4">
          <div class="flex items-center justify-between">
            <div>
              <h3 class="text-lg font-bold text-gray-900 dark:text-white">Ingresa la Cantidad</h3>
              <p class="text-sm text-gray-500 dark:text-zinc-400 mt-0.5">{{ selectedProductForQuantity?.name }}</p>
            </div>
            <button @click="showQuantityModal = false" 
                    class="p-2 hover:bg-gray-100 dark:hover:bg-zinc-700 rounded-lg transition-colors">
              <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
              </svg>
            </button>
          </div>
        </div>

        <!-- Body -->
        <div class="p-6 space-y-4">
          <!-- Toggle de unidad (solo para kg/g y L/ml) -->
          <div v-if="['kg', 'g', 'l', 'ml'].includes(selectedProductForQuantity?.measurement_unit)" 
               class="flex items-center justify-between p-3 bg-gray-50 dark:bg-zinc-700 rounded-lg">
            <span class="text-sm font-medium text-gray-700 dark:text-zinc-300">
              Ingresar en {{ getUnitText(getAlternativeUnit(selectedProductForQuantity?.measurement_unit)) }}
            </span>
            <button 
              @click="useAlternativeUnit = !useAlternativeUnit"
              :class="[
                'relative inline-flex h-6 w-11 items-center rounded-full transition-colors',
                useAlternativeUnit ? 'bg-blue-600' : 'bg-gray-300 dark:bg-zinc-600'
              ]">
              <span :class="[
                'inline-block h-4 w-4 transform rounded-full bg-white transition-transform',
                useAlternativeUnit ? 'translate-x-6' : 'translate-x-1'
              ]" />
            </button>
          </div>

          <div>
            <label class="block text-sm font-bold text-gray-700 dark:text-zinc-300 mb-2">
              Cantidad en {{ getUnitText(getInputUnit(selectedProductForQuantity?.measurement_unit)) }}
            </label>
            <div class="relative">
              <input 
                v-model="customQuantity" 
                type="number" 
                step="0.01"
                min="0.01"
                @keyup.enter="addToCartWithQuantity"
                class="w-full px-4 py-3 border border-gray-300 dark:border-zinc-600 dark:bg-zinc-700 dark:text-white rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-lg font-semibold"
                :placeholder="useAlternativeUnit && selectedProductForQuantity?.measurement_unit === 'kg' ? 'Ej: 450, 500, 1000' : 'Ej: 0.5, 1.25, 2.75'"
                autofocus>
              <span class="absolute right-4 top-1/2 transform -translate-y-1/2 text-sm font-semibold text-gray-500 dark:text-zinc-400">
                {{ getUnitText(getInputUnit(selectedProductForQuantity?.measurement_unit)) }}
              </span>
            </div>
            <p class="text-xs text-gray-500 dark:text-zinc-400 mt-2">
              Stock disponible: {{ getTotalStock(selectedProductForQuantity) }} {{ getUnitText(selectedProductForQuantity?.measurement_unit) }}
            </p>
          </div>

          <!-- Botones rápidos (ajustados según la unidad) -->
          <div class="grid grid-cols-4 gap-2">
            <button 
              v-for="quick in (useAlternativeUnit && ['kg', 'l'].includes(selectedProductForQuantity?.measurement_unit) ? [250, 500, 1000, 2000] : [0.25, 0.5, 1, 2])" 
              :key="quick"
              @click="customQuantity = quick"
              class="px-3 py-2 bg-gray-100 dark:bg-zinc-700 hover:bg-gray-200 dark:hover:bg-zinc-600 text-gray-700 dark:text-zinc-300 rounded-lg text-sm font-semibold transition-colors">
              {{ quick }}
            </button>
          </div>
        </div>

        <!-- Footer -->
        <div class="bg-gray-50 dark:bg-zinc-900 border-t border-gray-200 dark:border-zinc-700 px-6 py-4 flex gap-3">
          <button @click="showQuantityModal = false" 
                  class="flex-1 px-4 py-2.5 bg-white dark:bg-zinc-700 hover:bg-gray-50 dark:hover:bg-zinc-600 text-gray-700 dark:text-zinc-300 rounded-lg font-semibold transition-colors border border-gray-300 dark:border-zinc-600">
            Cancelar
          </button>
          <button @click="addToCartWithQuantity" 
                  :disabled="!customQuantity || parseFloat(customQuantity) <= 0"
                  class="flex-1 px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-bold transition-colors disabled:opacity-50 disabled:cursor-not-allowed shadow-md">
            Agregar al Carrito
          </button>
        </div>
      </div>
    </div>

    <!-- Modal de Historial de Cliente -->
    <CustomerHistoryModal
      v-if="showCustomerHistory"
      :customer="selectedCustomerForHistory"
      @add-to-cart="handleAddToCartFromHistory"
      @close="showCustomerHistory = false"
    />

    <!-- 👗 Modal de Selección de Variantes (Fashion) -->
    <POSVariantSelector
      :show="showVariantSelector"
      :product="selectedProductForVariants"
      @close="showVariantSelector = false"
      @confirm="handleVariantConfirmed"
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

    <!-- Modal: Se requiere cliente para vender -->
    <Teleport to="body">
      <Transition name="modal">
        <div v-if="showRequireCustomerModal" class="fixed inset-0 bg-black/70 backdrop-blur-sm flex items-center justify-center z-[9999] p-4">
          <div class="bg-white dark:bg-zinc-900 rounded-2xl shadow-2xl max-w-md w-full border border-gray-300 dark:border-zinc-800">
            <!-- Header con icono -->
            <div class="p-6 pb-4">
              <div class="text-center">
                <div class="mx-auto flex items-center justify-center h-14 w-14 rounded-xl bg-red-50 dark:bg-red-950/50 mb-4 border border-red-100 dark:border-red-900/50">
                  <svg class="h-7 w-7 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                  </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Cliente Requerido</h3>
                <p class="text-sm text-gray-600 dark:text-zinc-400 leading-relaxed">
                  La configuración del sistema requiere seleccionar un <span class="font-semibold text-gray-900 dark:text-white">cliente específico</span> para procesar ventas.
                </p>
              </div>
            </div>

            <!-- Body -->
            <div class="px-6 pb-6">
              <div class="bg-red-50 dark:bg-red-950/30 border border-red-200 dark:border-red-900/50 rounded-xl p-4 mb-4">
                <p class="text-sm text-red-800 dark:text-red-400 text-center font-medium">
                  ⚠️ No se puede usar "Cliente Final"
                </p>
              </div>
              <p class="text-center text-gray-600 dark:text-zinc-400 text-sm">
                Por favor, seleccione o agregue un cliente para continuar.
              </p>
            </div>

            <!-- Footer -->
            <div class="px-6 pb-6 flex gap-3">
              <button 
                @click="showRequireCustomerModal = false"
                class="flex-1 px-4 py-2.5 bg-white dark:bg-zinc-800 hover:bg-gray-50 dark:hover:bg-zinc-700 text-gray-700 dark:text-zinc-200 rounded-xl font-semibold border border-gray-200 dark:border-zinc-700 transition-all duration-200"
              >
                Cancelar
              </button>
              <button 
                @click="showRequireCustomerModal = false; showCustomerSelector = true"
                class="flex-1 px-4 py-2.5 bg-slate-900 dark:bg-slate-700 hover:bg-black dark:hover:bg-slate-600 text-white rounded-xl font-bold shadow-lg shadow-slate-400/40 dark:shadow-slate-900/50 transition-all duration-300 flex items-center justify-center gap-2"
              >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Agregar Cliente
              </button>
            </div>
          </div>
        </div>
      </Transition>
    </Teleport>

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
              <span class="text-sm text-gray-900 dark:text-white">{{ selectedCustomer?.name || 'Cliente Final' }}</span>
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
      :force-open="!hasOpenSession && !isFirstVisit"
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

  <!-- Modal de Cargar Pedido Web -->
  <LoadWebOrderModal
    :is-open="showLoadWebOrderModal"
    @close="showLoadWebOrderModal = false"
    @order-loaded="handleWebOrderLoaded"
  />

  <!-- Modal de Confirmación de Cliente Nuevo -->
  <ConfirmCustomerModal
    :is-open="showConfirmCustomerModal"
    :customer-data="pendingCustomerData || {}"
    @confirm="handleConfirmNewCustomer"
    @cancel="handleCancelNewCustomer"
    @close="showConfirmCustomerModal = false"
  />

  <!-- 🎫 BARRA DE MULTI-TABS INFERIOR (Footer Fijo) -->
  <div id="tour-pos-multisales" class="fixed bottom-0 left-0 right-0 bg-white dark:bg-zinc-900 border-t border-gray-200 dark:border-zinc-800 shadow-sm z-50 h-11">
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
              ? 'bg-white dark:bg-zinc-900' 
              : 'bg-transparent hover:bg-gray-50 dark:hover:bg-zinc-800'
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
            :class="activeTabId === tab.id ? 'text-gray-900 dark:text-zinc-200' : 'text-gray-600 dark:text-zinc-400'"
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
          :class="salesTabs.length >= 5 ? 'opacity-30 cursor-not-allowed bg-gray-100 dark:bg-zinc-800' : 'hover:bg-gray-100 dark:hover:bg-zinc-800 text-gray-600 dark:text-zinc-400 hover:text-green-600 dark:hover:text-green-500'"
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
import { apiCall } from '../services/api.js'
import { ref, reactive, computed, onMounted, onBeforeUnmount, onUnmounted, watch, nextTick } from 'vue'
import { productsService } from '../services/productsService.js'
import { categoriesService } from '../services/categoriesService.js'
import { customersService } from '../services/customersService.js'
import { invoicesService } from '../services/invoicesService.js'
import { warehouseService } from '../services/warehouseService.js'
import { generateInvoicePDF as generateInvoicePDFTemplate, generateQuotationPDF as generateQuotationPDFTemplate, getPDFBlob } from '../utils/pdfTemplates/pdfGenerator.js'
import { useCashSession } from '../services/cashSessionService.js'
import { appStore } from '../store/appStore.js' // Importar store global
import axiosInstance from '../services/apiClient.js'
import { useToast } from '../composables/useToast.js'
import { useLoyaltyPoints } from '../composables/useLoyaltyPoints.js'
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
import ContextualTour from './ContextualTour.vue'
import LoadWebOrderModal from './pos/LoadWebOrderModal.vue'
import ConfirmCustomerModal from './pos/ConfirmCustomerModal.vue'
import POSVariantSelector from './POSVariantSelector.vue'

// Switch para tipo de método de pago
const paymentType = ref('contado')

// Composables
const { showSuccess, showError, showWarning, showInfo } = useToast()
const { user } = useAuth()
const { 
  loyaltySettings, 
  isEnabled: loyaltyEnabled, 
  loadSettings: loadLoyaltySettings, 
  calculatePointsValue, 
  formatPointsAsMoney 
} = useLoyaltyPoints()

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
const warehouses = ref([]) // 🏢 Lista de todas las warehouses/sedes

// Current user info
// Current user info
const currentUser = computed(() => user.value || { name: 'Compañero' })

// Loading state para WhatsApp y Email
const isLoading = ref(false)

// 🎯 TOUR DEL POS - Control de bienvenida y primera visita
const DEV_MODE = false // false = Tour solo primera vez | true = Tour siempre disponible
const isFirstVisit = ref(DEV_MODE || !localStorage.getItem('pos_tour_completed'))
const showWelcomeModal = ref(false)
const posTourRef = ref(null)

// 🎯 TOUR EDUCATIVO DEL POS - Pasos amigables y didácticos
const posTourSteps = ref([
  {
    selector: '#tour-pos-cash-btn',
    title: 'Control de Caja',
    content: `
      <p>Antes de vender, el sistema necesita una base. <strong>Abre tu turno</strong> para registrar ingresos y salidas de efectivo automáticamente.</p>
      <p class="mt-2 text-xs text-gray-500">El sistema cuadrará tu caja al final del día sin esfuerzo manual.</p>
    `
  },
  {
    selector: '[placeholder="Buscar productos, SKU o escanear..."]',
    title: 'Buscador Inteligente',
    content: `
      <p>Encuentra todo rápido. Busca por:</p>
      <div class="flex flex-wrap gap-2 mt-3">
        <span class="px-2.5 py-1 bg-gray-100 dark:bg-zinc-800 text-gray-700 dark:text-zinc-300 text-xs font-semibold rounded-lg border border-gray-200 dark:border-zinc-700">Nombre</span>
        <span class="px-2.5 py-1 bg-gray-100 dark:bg-zinc-800 text-gray-700 dark:text-zinc-300 text-xs font-semibold rounded-lg border border-gray-200 dark:border-zinc-700">Escáner</span>
        <span class="px-2.5 py-1 bg-gray-100 dark:bg-zinc-800 text-gray-700 dark:text-zinc-300 text-xs font-semibold rounded-lg border border-gray-200 dark:border-zinc-700">SKU</span>
        <span class="px-2.5 py-1 bg-gray-100 dark:bg-zinc-800 text-gray-700 dark:text-zinc-300 text-xs font-semibold rounded-lg border border-gray-200 dark:border-zinc-700">Cotización</span>
      </div>
      <p class="mt-3 text-xs text-gray-500">Tip: Escribe el número de cotización completo y presiona Enter.</p>
    `
  },
  {
    selector: '.flex.items-center.gap-1\\.5.min-w-max',
    title: 'Filtros por Categorías',
    content: `
      <p>Organiza y filtra productos por categoría para acceso rápido. <strong>Perfecto para tiendas con amplio inventario.</strong></p>
      <p class="mt-2 text-xs text-gray-500">Haz clic en cualquier categoría para ver solo esos productos.</p>
    `
  },
  {
    selector: '#tour-pos-returns',
    title: 'Devoluciones Sin Caos',
    content: `
      <p>Gestiona devoluciones sin complicaciones. El sistema <strong>ajustará el inventario</strong> y generará el comprobante contable automáticamente con un clic.</p>
      <p class="mt-2 text-xs text-gray-500">Todo queda registrado para tu control y auditoría.</p>
    `
  },
  {
    selector: '#tour-pos-whatsapp',
    title: 'WhatsApp Business',
    content: `
      <p><span class="inline-flex items-center px-2 py-0.5 rounded-md bg-emerald-50 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-400 text-xs font-semibold border border-emerald-200 dark:border-emerald-800">Premium</span></p>
      <p class="mt-2">Envía <strong>facturas y cotizaciones</strong> directamente por WhatsApp en formato PDF. Mantén comunicación instantánea con tus clientes.</p>
    `
  },
  {
    selector: '#tour-customer-btn',
    title: 'Base de Clientes',
    content: `
      <p>Gestiona tu <strong>base de clientes</strong> de forma profesional. Registra información, consulta historial de compras y gestiona créditos.</p>
      <p class="mt-2 text-xs text-gray-500">Asigna clientes a ventas para mejor control y seguimiento.</p>
    `
  },
  {
    selector: '#tour-discount-btn',
    title: 'Descuentos Inteligentes',
    content: `
      <p>Aplica descuentos por <strong>porcentaje o monto fijo</strong>, ya sea por producto individual o venta completa.</p>
      <div class="flex flex-wrap gap-2 mt-3">
        <span class="px-2.5 py-1 bg-amber-50 dark:bg-amber-950 text-amber-700 dark:text-amber-400 text-xs font-semibold rounded-lg border border-amber-200 dark:border-amber-800">% Porcentaje</span>
        <span class="px-2.5 py-1 bg-amber-50 dark:bg-amber-950 text-amber-700 dark:text-amber-400 text-xs font-semibold rounded-lg border border-amber-200 dark:border-amber-800">$ Monto Fijo</span>
      </div>
      <p class="mt-3 text-xs text-gray-500">Gestiona todos tus descuentos desde el Panel de Configuración.</p>
    `
  }
])

// 🎉 Función para crear efecto de confeti ÉPICO
const triggerConfetti = () => {
  const duration = 3000
  const animationEnd = Date.now() + duration
  const colors = ['#3b82f6', '#8b5cf6', '#ec4899', '#10b981', '#f59e0b', '#ef4444']

  function randomInRange(min, max) {
    return Math.random() * (max - min) + min
  }

  // Explosión inicial desde el centro
  if (window.confetti) {
    window.confetti({
      particleCount: 150,
      spread: 180,
      origin: { y: 0.5 },
      colors: colors,
      zIndex: 9999
    })

    // Ráfagas continuas desde los lados
    const interval = setInterval(function() {
      const timeLeft = animationEnd - Date.now()

      if (timeLeft <= 0) {
        return clearInterval(interval)
      }

      const particleCount = 30 * (timeLeft / duration)

      // Desde la izquierda
      window.confetti({
        particleCount,
        angle: 60,
        spread: 55,
        origin: { x: 0, y: 0.6 },
        colors: colors,
        zIndex: 9999
      })
      
      // Desde la derecha
      window.confetti({
        particleCount,
        angle: 120,
        spread: 55,
        origin: { x: 1, y: 0.6 },
        colors: colors,
        zIndex: 9999
      })
    }, 250)
  }
}

// Handlers del tour
const handlePosTourComplete = () => {
  if (!DEV_MODE) {
    localStorage.setItem('pos_tour_completed', 'true')
    isFirstVisit.value = false
  }
  
  // 🎉 EFECTO DE CONFETI AL COMPLETAR EL TOUR
  triggerConfetti()
  
  showSuccess('¡Felicidades! Ya dominas el POS como un profesional 🎉')
}

const handlePosTourSkip = () => {
  if (!DEV_MODE) {
    localStorage.setItem('pos_tour_skipped', 'true')
    isFirstVisit.value = false
  }
  showInfo('Puedes volver a ver el tour desde el menú de ayuda')
}

// Handlers del modal de bienvenida
const handleWelcomeStart = () => {
  showWelcomeModal.value = false
  // Iniciar el tour manualmente después de cerrar el modal
  setTimeout(() => {
    if (posTourRef.value) {
      posTourRef.value.startTourConfirmed()
    }
  }, 300)
}

const handleWelcomeSkip = () => {
  showWelcomeModal.value = false
  if (!DEV_MODE) {
    localStorage.setItem('pos_tour_completed', 'true')
    isFirstVisit.value = false
  }
  showInfo('¡Entendido! Puedes explorar por tu cuenta')
}

// Función para manejar clics en el área del POS (permite cerrar dropdowns del header)
const handlePosClick = (event) => {
  // No hacer nada aquí, solo permitir que el evento se propague al documento
  // Esto permite que el AppHeader detecte los clics y cierre sus dropdowns
}

// Mostrar modal de bienvenida si es primera visita
onMounted(() => {
  if (isFirstVisit.value) {
    // Mostrar modal de bienvenida después de que el POS esté listo
    setTimeout(() => {
      showWelcomeModal.value = true
    }, 800) // Delay aumentado para asegurar que todo esté cargado
  }
})

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

// 🎨 Paleta de colores moderna para fallbacks (SaaS Enterprise Style)
const fallbackColors = [
  { bg: '#EEF2FF', text: '#4F46E5' }, // Indigo
  { bg: '#F0F9FF', text: '#0EA5E9' }, // Sky
  { bg: '#ECFDF5', text: '#10B981' }, // Emerald
  { bg: '#FEF2F2', text: '#EF4444' }, // Red
  { bg: '#FFF7ED', text: '#F97316' }, // Orange
  { bg: '#FAF5FF', text: '#8B5CF6' }, // Violet
  { bg: '#FDF4FF', text: '#D946EF' }, // Fuchsia
  { bg: '#FFF1F2', text: '#F43F5E' }  // Rose
]

// 🔤 Obtener iniciales del producto
const getInitials = (name) => {
  if (!name) return '??'
  return name
    .split(' ')
    .map(word => word[0])
    .slice(0, 2)
    .join('')
    .toUpperCase()
}

// 🎨 Obtener color determinista basado en el nombre
const getColorForName = (name) => {
  if (!name) return fallbackColors[0]
  let hash = 0
  for (let i = 0; i < name.length; i++) {
    hash = name.charCodeAt(i) + ((hash << 5) - hash)
  }
  const index = Math.abs(hash) % fallbackColors.length
  return fallbackColors[index]
}

// 🖼️ Generar SVG PROFESIONAL para placeholder (con gradiente elegante)
const generateAvatarSVG = (name) => {
  const initials = getInitials(name)
  
  // Detectar dark mode
  const isDark = document.documentElement.classList.contains('dark')
  
  // Colores MEJORADOS con MEJOR CONTRASTE para modo oscuro
  const bgGradientStart = isDark ? '#18181b' : '#f1f5f9'  // zinc-900 : slate-100
  const bgGradientEnd = isDark ? '#27272a' : '#e2e8f0'    // zinc-800 : slate-200
  const iconColor = isDark ? '#a1a1aa' : '#94a3b8'        // zinc-400 (más claro) : slate-400
  const textColor = isDark ? '#e4e4e7' : '#64748b'        // zinc-200 (mucho más claro) : slate-500
  const accentColor = isDark ? '#818cf8' : '#818cf8'      // indigo-400 : indigo-400
  
  const svg = `
    <svg width="200" height="200" viewBox="0 0 200 200" xmlns="http://www.w3.org/2000/svg">
      <!-- Gradiente de fondo elegante -->
      <defs>
        <linearGradient id="bgGrad${Date.now()}" x1="0%" y1="0%" x2="100%" y2="100%">
          <stop offset="0%" style="stop-color:${bgGradientStart};stop-opacity:1" />
          <stop offset="100%" style="stop-color:${bgGradientEnd};stop-opacity:1" />
        </linearGradient>
        <linearGradient id="iconGrad${Date.now()}" x1="0%" y1="0%" x2="100%" y2="100%">
          <stop offset="0%" style="stop-color:${iconColor};stop-opacity:1" />
          <stop offset="100%" style="stop-color:${accentColor};stop-opacity:0.5" />
        </linearGradient>
      </defs>
      
      <rect width="200" height="200" fill="url(#bgGrad${Date.now()})"/>
      
      <!-- Icono de imagen profesional (cámara + montaña) con MEJOR CONTRASTE -->
      <g transform="translate(100, 75)">
        <!-- Marco de imagen -->
        <rect x="-35" y="-25" width="70" height="50" rx="6" fill="none" stroke="url(#iconGrad${Date.now()})" stroke-width="3"/>
        
        <!-- Montaña estilizada -->
        <path d="M -20 10 L -5 -5 L 10 10 Z" fill="${iconColor}" opacity="0.8"/>
        <path d="M 0 10 L 15 -8 L 30 10 Z" fill="${iconColor}" opacity="0.6"/>
        
        <!-- Sol/círculo -->
        <circle cx="-15" cy="-10" r="6" fill="${accentColor}" opacity="0.7"/>
      </g>
      
      <!-- Iniciales elegantes con MUCHO MEJOR CONTRASTE -->
      <text 
        x="50%" 
        y="150" 
        fill="${textColor}" 
        font-family="Inter, system-ui, -apple-system, sans-serif" 
        font-size="16" 
        font-weight="700" 
        text-anchor="middle" 
        dominant-baseline="middle"
        letter-spacing="2"
      >
        ${initials}
      </text>
    </svg>
  `
  
  // Usar encodeURIComponent en lugar de btoa para mejor compatibilidad
  const encoded = encodeURIComponent(svg)
  return `data:image/svg+xml,${encoded}`
}

// 🖼️ Función utilitaria para manejo inteligente de imágenes
const getProductImage = (product) => {
  // Intentar múltiples propiedades de imagen
  const imageUrl = product.image_url || product.image || product.img || product.photo
  
  // Verificar si la URL es válida Y existe
  if (imageUrl && typeof imageUrl === 'string' && imageUrl.trim()) {
    const url = imageUrl.trim()
    
    // Si es una ruta relativa de Laravel Storage (/storage/...), convertir a URL absoluta
    if (url.startsWith('/storage/')) {
      const backendUrl = import.meta.env.VITE_API_BASE_URL || window.location.origin
      const fullUrl = `${backendUrl}${url}`
      console.log('✅ Imagen convertida a URL completa:', fullUrl)
      return fullUrl
    }
    
    // Si ya es una URL HTTP completa
    if ((url.startsWith('http://') || url.startsWith('https://')) && url.length > 20) {
      // Verificar que no sea un placeholder genérico
      if (!url.includes('placeholder') && !url.includes('default') && !url.includes('no-image')) {
        console.log('✅ Imagen HTTP válida para', product.name)
        return url
      }
    }
    
    // Si es un data URI, devolverlo directamente
    if (url.startsWith('data:image')) {
      console.log('✅ Data URI válido para', product.name)
      return url
    }
  }
  
  // SIEMPRE retornar SVG profesional si no hay imagen válida
  return generateAvatarSVG(product.name || 'Producto')
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
const handleImageError = (event, product) => {
  // Evitar bucle infinito si ya se aplicó el fallback
  if (event.target.getAttribute('data-fallback-applied')) return
  
  event.target.setAttribute('data-fallback-applied', 'true')
  
  // Cambiar a object-fit cover para que el SVG llene el espacio
  event.target.style.objectFit = 'cover'
  event.target.style.padding = '0'
  
  // Generar y aplicar el SVG placeholder
  const name = product?.name || 'Producto'
  event.target.src = generateAvatarSVG(name)
  
  // Forzar recarga de la imagen
  event.target.onerror = null
}

// 🔄 Función para refrescar datos después de una venta
const refreshPosData = async () => {
  
  // 🏢 Mantener el filtro Global/Local al refrescar
  if (currentSession.value?.warehouse_id) {
    const scope = globalSearch.value ? 'global' : 'local'
    await appStore.loadProducts(currentSession.value.warehouse_id, scope)
  } else {
    // Si no hay sesión, refrescar normalmente
    await appStore.refresh('products')
  }
  
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

// 🏢 Cargar todas las warehouses/sedes para validar multi-warehouse features
const loadWarehouses = async () => {
  try {
    const response = await warehouseService.getAll()
    
    // ✅ CORRECCIÓN: warehouseService.getAll() ya devuelve response.data
    // No necesitamos acceder a response.data.data
    if (response.warehouses) {
      warehouses.value = response.warehouses || []
    } else {
      warehouses.value = []
    }
  } catch (error) {
    console.error('❌ [loadWarehouses] Error:', error)
    warehouses.value = []
  }
}

// Las funciones de carga de productos, categorías y clientes
// han sido movidas al store global para precarga después del login

// Función para refrescar datos específicos cuando sea necesario
const refreshData = (dataType = 'all') => {
  appStore.refresh(dataType)
}

// Emits
const emit = defineEmits(['sale-completed', 'create-invoice', 'search-quote', 'cart-status-changed', 'change-module', 'warehouse-changed'])

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
const globalSearch = ref(localStorage.getItem('posGlobalSearch') === 'true') // 🌍 Toggle para búsqueda global de productos

// Persistir el estado de búsqueda global
watch(globalSearch, (newValue) => {
  localStorage.setItem('posGlobalSearch', newValue.toString())
})

// 🎁 Loyalty Points - Variables para redimir puntos
const usePoints = ref(false)
const pointsToRedeem = ref(0)
const pointsDiscount = ref(0)

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
const startCustomerCreation = ref(false)
const showCustomerHistory = ref(false)
const selectedCustomerForHistory = ref(null)
const showPaymentModal = ref(false)
const showAfterPaymentModal = ref(false)
const showReceiptModal = ref(false)
const showRequireCustomerModal = ref(false)

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

// Estado del modal de cargar pedido web
const showLoadWebOrderModal = ref(false)

// Estado del modal de confirmación de cliente nuevo
const showConfirmCustomerModal = ref(false)
const pendingCustomerData = ref(null)
const pendingWebOrder = ref(null)

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

// 👗 MODO FASHION - Variables para el diseño de tienda de ropa
const fashionViewMode = ref('grid') // 'grid' | 'list'

// Función para detectar si un producto es nuevo (menos de 7 días)
const isNewProduct = (product) => {
  if (!product.created_at) return false
  const createdDate = new Date(product.created_at)
  const now = new Date()
  const diffDays = Math.floor((now - createdDate) / (1000 * 60 * 60 * 24))
  return diffDays <= 7
}

// Número de factura real desde el backend
const nextInvoiceNumber = ref('FACT-000001') // Valor inicial por defecto

// Detectar si es primera vez (NO hay productos en la base de datos)
const isFirstTimeNoProducts = computed(() => {
  // Si está cargando, no es primera vez
  if (productsLoading.value) return false
  
  // Si NO hay productos en absoluto (sin filtros ni búsqueda)
  const totalProducts = products.value?.length || 0
  return totalProducts === 0
})

// Detectar si el resultado vacío es por filtros/búsqueda
const isEmptyByFilters = computed(() => {
  if (productsLoading.value || isFirstTimeNoProducts.value) return false
  
  // Hay productos en la BD pero filteredProducts está vacío
  const totalProducts = products.value?.length || 0
  return totalProducts > 0 && filteredProducts.value.length === 0
})

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

// Modal de cantidad con unidades de medida
const showQuantityModal = ref(false)
const selectedProductForQuantity = ref(null)
const customQuantity = ref('')
const useAlternativeUnit = ref(false) // Toggle para usar g en vez de kg, ml en vez de L, etc

// 👗 Modal de Selección de Variantes (Fashion/Moda)
const showVariantSelector = ref(false)
const selectedProductForVariants = ref(null)

// Función para obtener la URL completa de la imagen
const getFullImageUrl = (imageUrl) => {
  if (!imageUrl) return null
  // Si ya es una URL completa (http/https), devolverla tal cual
  if (imageUrl.startsWith('http://') || imageUrl.startsWith('https://')) {
    return imageUrl
  }
  // Si es una ruta relativa, agregar el base URL del backend
  const baseUrl = import.meta.env.VITE_API_URL || 'http://localhost:8000'
  return `${baseUrl}/storage/${imageUrl.replace(/^\/storage\//, '')}`
}

// Función para obtener la imagen del item del carrito (con fallback a avatar)
const getCartItemImage = (item) => {
  // La imagen ya viene procesada desde addToCart con getProductImage()
  // Puede ser una URL completa http:// o un data:image SVG
  if (item.image_url && typeof item.image_url === 'string' && item.image_url.trim().length > 0) {
    return item.image_url
  }
  
  return generateAvatarSVG(item.name)
}

// Función para parsear las opciones de variantes y detectar colores
const parseVariantOptions = (variantOptions) => {
  if (!variantOptions) return []
  
  // Formato: "Talla: 40 / Color: #CB5A5A"
  const options = variantOptions.split(' / ')
  
  return options.map(opt => {
    const [label, value] = opt.split(': ').map(s => s.trim())
    
    // Detectar si es un color (empieza con #)
    const isColor = value && value.startsWith('#')
    
    return {
      label: label,
      value: value,
      type: isColor ? 'COLOR' : label.toUpperCase()
    }
  }).filter(opt => opt.value) // Filtrar vacíos
}

// Función para capitalizar texto - Capitaliza cada palabra (Pantalón, Camisa Hombres, etc.)
const capitalizeText = (text) => {
  if (!text) return ''
  // Capitalizar primera letra de CADA palabra, mantener resto (para tildes como "Pantalón")
  return text
    .split(' ')
    .map(word => word.charAt(0).toUpperCase() + word.slice(1).toLowerCase())
    .join(' ')
}

// Función para obtener el texto de la unidad
const getUnitText = (unit) => {
  const units = {
    'unit': 'und',
    'kg': 'kg',
    'g': 'g',
    'm': 'm',
    'cm': 'cm',
    'l': 'L',
    'ml': 'ml'
  }
  return units[unit] || 'und'
}

// Función para verificar si una unidad permite decimales
const allowsDecimals = (unit) => {
  return unit !== 'unit' // Todas menos 'unit' permiten decimales
}

// Función para obtener la unidad alternativa (g<->kg, ml<->L)
const getAlternativeUnit = (unit) => {
  const alternatives = {
    'kg': 'g',
    'g': 'kg',
    'l': 'ml',
    'ml': 'l'
  }
  return alternatives[unit] || unit
}

// Función para convertir cantidad entre unidades
const convertQuantity = (quantity, fromUnit, toUnit) => {
  if (fromUnit === toUnit) return quantity
  
  // kg -> g: multiplicar por 1000
  if (fromUnit === 'kg' && toUnit === 'g') return quantity * 1000
  // g -> kg: dividir por 1000
  if (fromUnit === 'g' && toUnit === 'kg') return quantity / 1000
  // L -> ml: multiplicar por 1000
  if (fromUnit === 'l' && toUnit === 'ml') return quantity * 1000
  // ml -> L: dividir por 1000
  if (fromUnit === 'ml' && toUnit === 'l') return quantity / 1000
  
  return quantity
}

// Obtener la unidad de entrada según el toggle
const getInputUnit = (baseUnit) => {
  if (useAlternativeUnit.value) {
    return getAlternativeUnit(baseUnit)
  }
  return baseUnit
}

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
    return '🛍️'
  }
  const icon = categoryIcons.find(i => i.id === iconId)
  if (!icon) {
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

// 🏢 Computed para saber si debe mostrar features de múltiples bodegas
const shouldShowMultiWarehouseFeatures = computed(() => {
  // ✅ Solo mostrar en planes premium/enterprise
  const plan = appStore.tenantPlan
  const isPremiumOrEnterprise = plan === 'premium' || plan === 'enterprise'
  
  if (!isPremiumOrEnterprise) {
    return false
  }
  
  // ✅ CRÍTICO: Solo mostrar si hay MÁS DE UNA warehouse/sede registrada
  if (warehouses.value.length <= 1) {
    return false
  }
  
  // Y solo cuando hay sesión abierta con bodega asignada
  return hasOpenSession.value && currentSession.value?.warehouse_id
})

// 👗 Computed para detectar si es una tienda de MODA (Fashion Store)
const isFashionStore = computed(() => {
  // 🛍️ Detección de tienda de moda
  // PRIORIDAD 1: Configuración manual (anula todo lo demás)
  
  const storeType = systemSettings.value?.store_type
  
  // Si hay configuración explícita, respetarla siempre
  if (storeType) {
    const isFashion = storeType === 'fashion'
    console.log(`${isFashion ? '🎨' : '🏪'} Modo ${isFashion ? 'Fashion' : 'General'}: Configurado manualmente como "${storeType}"`)
    return isFashion
  }
  
  // PRIORIDAD 2: Auto-detección (solo si NO hay configuración manual)
  
  const fashionKeywords = [
    'boutique', 'moda', 'fashion', 'ropa', 'clothing', 'vestir',
    'confecciones', 'textil', 'prendas', 'closet', 'wear',
    'style', 'estilo', 'indumentaria', 'vestuario', 'apparel'
  ]
  
  const fashionCategories = [
    'camisas', 'camisas hombre', 'camisas mujer',
    'pantalones', 'jeans', 'vestidos', 'faldas',
    'chaquetas', 'abrigos', 'zapatos', 'calzado',
    'accesorios moda', 'bolsos', 'ropa', 'moda',
    'blusas', 'shorts', 'bermudas', 'camisetas', 't-shirts',
    'sweaters', 'suéteres', 'medias', 'calcetines', 'ropa interior'
  ]
  
  // Verificar nombre del negocio
  const businessName = (systemSettings.value?.business_name || appStore.businessName || '').toLowerCase()
  const hasBusinessNameMatch = fashionKeywords.some(kw => businessName.includes(kw))
  
  if (hasBusinessNameMatch) {
    console.log('🎨 Fashion Mode: Auto-detectado por nombre del negocio:', businessName)
    return true
  }
  
  // Verificar categorías (solo necesita 2+ categorías de moda)
  const cats = appStore.categories || []
  if (!Array.isArray(cats) || cats.length === 0) {
    console.log('🏪 General Mode: Sin categorías')
    return false
  }
  
  const matchedCategories = cats.filter(cat => {
    const catName = (cat.name || '').toLowerCase()
    return fashionCategories.some(f => catName.includes(f))
  })
  
  const fashionCount = matchedCategories.length
  
  if (fashionCount >= 2) {
    console.log('🎨 Fashion Mode: Auto-detectado por categorías:', matchedCategories.map(c => c.name).join(', '))
    return true
  }
  
  console.log('🏪 General Mode: Menos de 2 categorías de moda (' + fashionCount + '/' + cats.length + ')')
  return false
})

const paymentMethods = computed(() => {
  let methods = []

  // 1. Métodos de la API
  if (Array.isArray(paymentMethodsFromApi.value) && paymentMethodsFromApi.value.length > 0) {
    let filtered = paymentMethodsFromApi.value.filter(method => method.active)
    
    if (paymentType.value === 'financiado') {
      filtered = filtered.filter(m => m.type === 'financiado')
    }
    
    methods = filtered.map(method => ({
      id: method.code || method.slug || method.name.toLowerCase().replace(/\s+/g, '_'),
      name: method.name,
      icon: mapPaymentIcon(method.icon, method.name),
      description: method.description,
      fee_amount: method.fee || 0, // Mapear correctamente para ConfirmPaymentModal
      fee_type: method.fee_type || 'fixed',
      requires_reference: method.requires_reference || false
    }))
  }

  // 2. Inyectar Creditienda si está habilitado
  if (systemSettings.value.creditienda_enabled) {
    methods.push({
      id: 'credit',
      name: 'Creditienda',
      icon: 'card', // Reutilizamos icono de tarjeta o definimos uno nuevo
      description: 'Venta a crédito con recargo',
      fee_amount: parseFloat(systemSettings.value.credit_surcharge_percentage) || 0,
      fee_type: 'percentage',
      requires_reference: false,
      is_credit: true // Flag para identificarlo fácilmente
    })
  }

  return methods
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
  const baseTotal = subtotal.value - discount.value + tax.value
  // 🎁 Aplicar descuento por puntos si están activados
  if (usePoints.value && pointsDiscount.value > 0) {
    return Math.max(0, baseTotal - pointsDiscount.value)
  }
  return baseTotal
})

const totalItems = computed(() => {
  return cart.items.reduce((total, item) => total + item.quantity, 0)
})

// 🎁 Loyalty Points - Computed properties
const canUseLoyaltyPoints = computed(() => {
  // 🔥 OPTIMIZACIÓN SaaS: Si loyalty está desactivado, retornar inmediatamente sin procesar
  const enabled = loyaltyEnabled.value
  if (!enabled) return false
  
  const hasCustomer = !!selectedCustomer.value
  const customerPoints = selectedCustomer.value?.loyalty_points || 0
  const hasItems = cart.items.length > 0
  
  return hasCustomer && customerPoints > 0 && hasItems
})

const maxPointsToUse = computed(() => {
  if (!canUseLoyaltyPoints.value) return 0
  
  const customerPoints = selectedCustomer.value.loyalty_points || 0
  const baseTotal = subtotal.value - discount.value + tax.value
  const maxDiscountFromPoints = calculatePointsValue(customerPoints)
  
  // No puede redimir más del total de la compra
  if (maxDiscountFromPoints >= baseTotal) {
    // Calcular cuántos puntos necesita para cubrir el total
    const pointsNeeded = Math.ceil(baseTotal / loyaltySettings.value.point_value)
    return Math.min(pointsNeeded, customerPoints)
  }
  
  return customerPoints
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
  // Retornar el número obtenido del backend
  return nextInvoiceNumber.value
}

// Obtener el próximo número de factura desde el backend
const fetchNextInvoiceNumber = async () => {
  try {
    const nextNumber = await invoicesService.getNextInvoiceNumber('invoice')
    
    if (nextNumber) {
      nextInvoiceNumber.value = nextNumber
    }
  } catch (error) {
    console.error('❌ Error al obtener próximo número de factura:', error)
  }
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

// 🎁 Watchers para loyalty points
watch(pointsToRedeem, (newPoints) => {
  if (!usePoints.value || !canUseLoyaltyPoints.value) {
    pointsDiscount.value = 0
    return
  }
  
  // Validar que no exceda el máximo
  if (newPoints > maxPointsToUse.value) {
    pointsToRedeem.value = maxPointsToUse.value
    return
  }
  
  // Calcular descuento en dinero
  pointsDiscount.value = calculatePointsValue(newPoints)
})

watch(usePoints, (enabled) => {
  if (!enabled) {
    pointsToRedeem.value = 0
    pointsDiscount.value = 0
  }
})

// Resetear puntos cuando cambia el cliente o el carrito se vacía
watch([selectedCustomer, () => cart.items.length], () => {
  usePoints.value = false
  pointsToRedeem.value = 0
  pointsDiscount.value = 0
})

const applyPromoCode = async () => {
  if (!promoCode.value.trim()) return

  // Marcar que el usuario intentó aplicar un código (para mostrar errores solo en ese momento)
  promoApplyAttempted.value = true

  // Validar que hay un cliente seleccionado y no es "Cliente Final"
  if (!selectedCustomer.value || selectedCustomer.value.name === 'Cliente Final') {
    promoError.value = 'Debes elegir primero al cliente para aplicar descuentos'
    return
  }

  promoError.value = ''
  
  try {
    // Validar código promocional usando la API del backend (incluye validación de uso por usuario)
    const validationResult = await apiCall('/discounts/validate-code', {
      method: 'POST',
      body: JSON.stringify({
        code: promoCode.value.trim(),
        customer_id: selectedCustomer.value?.id,
        cart_total: subtotal.value,
        customer_email: selectedCustomer.value?.email,
        customer_phone: selectedCustomer.value?.phone,
        user_identifier: getUserIdentifier() // Identificador consistente de usuario
      })
    })

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
    
  } catch (error) {
    console.error('❌ Error al validar código promocional:', error)
    promoError.value = error.message || 'Error al aplicar el código promocional'
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

// Watcher para recalcular descuento cuando cambia el subtotal (nuevos productos agregados)
watch(() => subtotal.value, (newSubtotal) => {
  if (appliedDiscount.value && newSubtotal > 0) {
    // Recalcular descuento basado en el nuevo subtotal
    if (appliedDiscount.value.type === 'percentage') {
      const calculatedDiscount = Math.round(newSubtotal * (appliedDiscount.value.value / 100))
      // Aplicar límite máximo de descuento si existe
      if (appliedDiscount.value.maximum_discount) {
        discount.value = Math.min(calculatedDiscount, appliedDiscount.value.maximum_discount)
      } else {
        discount.value = calculatedDiscount
      }
    } else if (appliedDiscount.value.type === 'fixed') {
      // Descuento fijo no puede ser mayor al subtotal
      discount.value = Math.min(appliedDiscount.value.value, newSubtotal)
    }
  }
})

// Helper para obtener cantidad de producto en carrito
const getProductQuantityInCart = (productId) => {
  const item = cart.items.find(item => item.id === productId)
  return item ? item.quantity : 0
}

// 🏢 Función helper para calcular stock total de todas las bodegas
const getTotalStock = (product) => {
  // 🔍 En modo LOCAL: Solo mostrar stock de la bodega actual
  if (!globalSearch.value && currentSession.value?.warehouse_id) {
    const currentWarehouseId = currentSession.value.warehouse_id
    
    // Buscar en warehouses
    if (product.warehouses && Array.isArray(product.warehouses)) {
      const currentWh = product.warehouses.find(w => w.warehouse_id === currentWarehouseId)
      return currentWh ? (parseInt(currentWh.stock) || 0) : 0
    }
    
    // Buscar en alternative_warehouses
    if (product.alternative_warehouses && Array.isArray(product.alternative_warehouses)) {
      const currentWh = product.alternative_warehouses.find(w => w.warehouse_id === currentWarehouseId)
      return currentWh ? (parseInt(currentWh.stock) || 0) : 0
    }
    
    // Si no hay info de bodegas, retornar 0 en modo local
    return 0
  }
  
  // 🌍 En modo GLOBAL: Sumar stock de TODAS las bodegas
  if (product.warehouses && Array.isArray(product.warehouses)) {
    return product.warehouses.reduce((sum, w) => sum + (parseInt(w.stock) || 0), 0)
  }
  
  if (product.alternative_warehouses && Array.isArray(product.alternative_warehouses)) {
    return product.alternative_warehouses.reduce((sum, w) => sum + (parseInt(w.stock) || 0), 0)
  }
  
  // Fallback al stock general
  return product.stock || 0
}

// Métodos
const addToCart = (product) => {
  // 👗 INTERCEPTAR: Si el producto tiene variantes (Fashion/Moda), abrir modal de selección
  if (product.variants && product.variants.length > 0) {
    selectedProductForVariants.value = product
    showVariantSelector.value = true
    
    // Notificación visual en el POS
    showInfo(`Abriendo selector de variantes para: ${product.name}`)
    
    return
  }
  
  // 🏢 Calcular stock disponible (ya maneja modo LOCAL vs GLOBAL)
  const totalStock = getTotalStock(product)
  
  // Validar que haya stock disponible
  if (totalStock <= 0) {
    if (!globalSearch.value) {
      showWarning(`⚠️ No hay stock de "${product.name}" en ${currentSession.value?.warehouse?.name || 'esta tienda'}. Cambia a búsqueda GLOBAL para ver productos de otras tiendas.`)
    } else {
      showWarning(`No hay stock disponible de ${product.name} en ninguna tienda`)
    }
    return
  }
  
  // Si el producto usa unidades con decimales, abrir modal para ingresar cantidad
  if (allowsDecimals(product.measurement_unit)) {
    selectedProductForQuantity.value = product
    customQuantity.value = ''
    showQuantityModal.value = true
    return
  }
  
  // Para unidades enteras (unit), agregar normalmente
  const existingItem = cart.items.find(item => item.id === product.id)
  
  if (existingItem) {
    if (existingItem.quantity < totalStock) {
      existingItem.quantity += 1
      existingItem.max_stock = totalStock
    } else {
      showWarning(`No hay más stock disponible de ${product.name}`)
    }
  } else {
    // Usar la misma función que usa el catálogo para obtener la imagen
    const processedImageUrl = getProductImage(product)
    
    cart.items.push({
      id: product.id,
      name: product.name,
      price: product.price,
      quantity: 1,
      image_url: processedImageUrl, // Ahora guarda la URL ya procesada
      barcode: product.barcode,
      category: product.category,
      max_stock: totalStock,
      measurement_unit: product.measurement_unit || 'unit'
    })
  }
  
  // Emitir cambio en el estado del carrito
  emit('cart-status-changed', cart.items.length > 0)
}

// 👗 Manejar confirmación del modal de variantes
const handleVariantConfirmed = ({ variant, selectedOptions }) => {
  if (!variant || !selectedProductForVariants.value) return
  
  // Validar stock de la variante
  if (variant.stock <= 0) {
    showWarning(`No hay stock disponible para esta variante`)
    return
  }
  
  // Buscar si ya existe en el carrito (por variant_id único)
  const existingItem = cart.items.find(item => item.variant_id === variant.id)
  
  if (existingItem) {
    if (existingItem.quantity < variant.stock) {
      existingItem.quantity += 1
      existingItem.max_stock = variant.stock
    } else {
      showWarning(`No hay más stock disponible de esta variante`)
    }
  } else {
    // Crear resumen de opciones para mostrar (ej: "Talla: M / Color: Rojo")
    const optionsSummary = Object.entries(selectedOptions)
      .map(([key, value]) => `${key}: ${value}`)
      .join(' / ')
    
    // Agregar nueva variante al carrito
    cart.items.push({
      id: selectedProductForVariants.value.id,
      variant_id: variant.id, // 🔑 ID único de la variante
      name: `${selectedProductForVariants.value.name} (${optionsSummary})`,
      price: variant.price, // Precio específico de la variante
      quantity: 1,
      image_url: getProductImage(selectedProductForVariants.value),
      barcode: variant.sku || selectedProductForVariants.value.barcode,
      category: selectedProductForVariants.value.category,
      max_stock: variant.stock,
      measurement_unit: selectedProductForVariants.value.measurement_unit || 'unit',
      variant_options: optionsSummary // Para mostrar en el ticket
    })
  }
  
  // Emitir cambio en el estado del carrito
  emit('cart-status-changed', cart.items.length > 0)
  
  // Resetear
  selectedProductForVariants.value = null
  showVariantSelector.value = false
}

// Agregar producto con cantidad personalizada
const addToCartWithQuantity = () => {
  if (!selectedProductForQuantity.value || !customQuantity.value) return
  
  let quantity = parseFloat(customQuantity.value)
  const product = selectedProductForQuantity.value
  const totalStock = getTotalStock(product)
  const inputUnit = getInputUnit(product.measurement_unit)
  
  if (isNaN(quantity) || quantity <= 0) {
    showWarning('Por favor ingresa una cantidad válida')
    return
  }
  
  // Convertir la cantidad ingresada a la unidad base del producto
  const quantityInBaseUnit = convertQuantity(quantity, inputUnit, product.measurement_unit)
  
  if (quantityInBaseUnit > totalStock) {
    showWarning(`Solo hay ${totalStock} ${getUnitText(product.measurement_unit)} disponibles`)
    return
  }
  
  const existingItem = cart.items.find(item => item.id === product.id)
  
  if (existingItem) {
    const newQuantity = existingItem.quantity + quantityInBaseUnit
    if (newQuantity <= totalStock) {
      existingItem.quantity = newQuantity
      existingItem.max_stock = totalStock
    } else {
      showWarning(`No puedes agregar más. Stock disponible: ${totalStock} ${getUnitText(product.measurement_unit)}`)
      return
    }
  } else {
    cart.items.push({
      id: product.id,
      name: product.name,
      price: product.price,
      quantity: quantityInBaseUnit,
      image_url: getProductImage(product), // Usar la misma función del catálogo
      barcode: product.barcode,
      category: product.category,
      max_stock: totalStock,
      measurement_unit: product.measurement_unit || 'unit'
    })
  }
  
  // Cerrar modal y limpiar
  showQuantityModal.value = false
  selectedProductForQuantity.value = null
  customQuantity.value = ''
  useAlternativeUnit.value = false
  
  // Emitir cambio en el estado del carrito
  emit('cart-status-changed', cart.items.length > 0)
}

const updateQuantity = (productId, newQuantity) => {
  if (newQuantity <= 0) {
    removeFromCart(productId)
    return
  }
  
  const item = cart.items.find(item => item.id === productId)
  if (item) {
    // Para unidades con decimales, incrementar/decrementar según un paso apropiado
    const unit = item.measurement_unit || 'unit'
    let step = 1
    
    // Definir pasos según la unidad
    if (unit === 'kg' || unit === 'l') step = 0.5  // 500g o 500ml
    else if (unit === 'g' || unit === 'ml') step = 100  // 100g o 100ml
    else if (unit === 'm') step = 0.5  // 50cm
    else if (unit === 'cm') step = 10  // 10cm
    
    // Redondear a 2 decimales
    const roundedQuantity = Math.round(newQuantity * 100) / 100
    
    if (roundedQuantity <= item.max_stock) {
      item.quantity = roundedQuantity
    } else {
      showWarning(`Stock máximo: ${item.max_stock} ${getUnitText(unit)}`)
    }
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
  
  // Si se selecciona Cliente Final, limpiar descuentos aplicados
  if (!customer || customer.name === 'Cliente Final') {
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
  // ⚠️ VALIDACIÓN CRÍTICA: Si la configuración requiere cliente, validar ANTES de cualquier cosa
  if (systemSettings.value.require_customer && (!selectedCustomer.value || selectedCustomer.value.name === 'Cliente Final')) {
    showRequireCustomerModal.value = true
    return
  }
  
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

  // VALIDACIÓN DE CRÉDITO
  if (selectedPaymentMethod.value === 'credit') {
    // 1. Validar que haya cliente seleccionado
    if (!selectedCustomer.value || !selectedCustomer.value.id || selectedCustomer.value.name === 'Cliente Final') {
      showError('Debe seleccionar un cliente registrado para ventas a crédito')
      return
    }

    // 2. Validar que el cliente tenga crédito activo
    if (!selectedCustomer.value.credit_active) {
      showError('El cliente seleccionado no tiene crédito habilitado')
      return
    }

    // 3. Calcular recargo y total estimado
    const surchargePercent = parseFloat(systemSettings.value.credit_surcharge_percentage) || 0
    const surcharge = Math.round((total.value * surchargePercent) / 100)
    const estimatedTotal = total.value + surcharge

    // 4. Validar cupo disponible (SOLO SOBRE EL SUBTOTAL, NO EL RECARGO)
    // El recargo es ganancia adicional del negocio, no cuenta contra el cupo
    const currentDebt = parseFloat(selectedCustomer.value.current_debt || 0)
    const creditLimit = parseFloat(selectedCustomer.value.credit_limit || 0)
    const availableCredit = creditLimit - currentDebt

    if (total.value > availableCredit) {
      showError(`Crédito insuficiente. Disponible: $${availableCredit.toLocaleString()} - Requerido: $${total.value.toLocaleString()}`)
      return
    }
  }

  showPaymentModal.value = true
}

const handlePaymentConfirmed = async (paymentData) => {
  // ⚠️ VALIDACIÓN: Si la configuración requiere cliente, validar antes de procesar (segunda barrera de seguridad)
  if (systemSettings.value.require_customer && (!selectedCustomer.value || selectedCustomer.value.name === 'Cliente Final')) {
    showPaymentModal.value = false
    showRequireCustomerModal.value = true
    return
  }
  
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
        surcharge_amount: paymentData.method === 'credit' ? paymentData.fee : 0,
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
        
        // Preparar datos para el recibo usando la nueva factura generada
        lastSale.value = {
          id: result.data.id || loadedQuotation.value.id,
          invoiceNumber: result.data.custom_number || result.data.number || `FV-${result.data.id}`,
          date: toMySQLDateTime(),
          due_date: toMySQLDateTime(new Date(Date.now() + 30 * 24 * 60 * 60 * 1000)),
          customer_id: selectedCustomer.value?.id || null,
          customer: selectedCustomer.value?.name || 'Cliente Final',
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
      
      const currentDate = new Date()
      const dueDate = new Date()
      dueDate.setDate(currentDate.getDate() + 30)
      
      lastSale.value = {
        id: Date.now(),
        invoiceNumber: 'Generando...', // Placeholder temporal hasta crear en backend
        date: toMySQLDateTime(currentDate),
        due_date: toMySQLDateTime(dueDate),
        customer_id: selectedCustomer.value?.id || 1, // Cliente Final por defecto (ID 1)
        customer: selectedCustomer.value?.name || 'Cliente Final',
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
        surcharge: paymentData.method === 'credit' ? paymentData.fee : 0,
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
      
      const invoiceData = {
        type: 'invoice',
        customer_id: lastSale.value.customer_id || 1, // Asegurar que siempre haya un customer_id (Cliente Final)
        date: lastSale.value.date.split('T')[0],
        due_date: lastSale.value.due_date.split('T')[0],
        subtotal: parseFloat(lastSale.value.subtotal),
        tax_amount: parseFloat(lastSale.value.tax),
        total: parseFloat(lastSale.value.total),
        payment_method: selectedPaymentMethod.value, // Usar directamente el código del método de pago
        surcharge_amount: selectedPaymentMethod.value === 'credit' ? paymentData.fee : 0,
        notes: `Venta POS - ${lastSale.value.cashier}`,
        items: lastSale.value.items.map(item => ({
          product_id: item.id,
          variant_id: item.variant_id || null, // 👗 ID de variante si es producto fashion
          product_name: item.name,
          quantity: parseFloat(item.quantity), // Cambiar a parseFloat para soportar decimales
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
        } : {}),
        // 🎁 Información de puntos redimidos
        ...(usePoints.value && pointsToRedeem.value > 0 ? {
          loyalty_points_redeemed: pointsToRedeem.value,
          loyalty_discount_amount: pointsDiscount.value
        } : {})
      }
      
      const result = await invoicesService.createPosInvoice(invoiceData) // Usar endpoint sin autenticación
      
      if (result && result.data) {
        // Actualizar lastSale con los datos reales del backend
        const oldNumber = lastSale.value.invoiceNumber
        lastSale.value.id = result.data.id
        lastSale.value.invoiceNumber = result.data.number || `FV-${result.data.id}`
        
        // Obtener el próximo número de factura para la siguiente venta
        await fetchNextInvoiceNumber()
        
        finalInvoiceData = lastSale.value
      } else {
        throw new Error('No se pudo crear la factura en el backend')
      }
    }
    
    // Emitir el evento con los datos finales (solo para actualizar listas locales)
    emit('sale-completed', finalInvoiceData)
    
  } catch (error) {
    console.error('❌ Error procesando venta en backend:', error)
    
    // 🚨 MOSTRAR ERROR AL USUARIO Y NO CONTINUAR
    showError(`No se pudo crear la factura: ${error.message || 'Error desconocido'}`)
    
    // Cerrar modal de confirmación de pago
    showPaymentModal.value = false
    
    // NO limpiar el carrito, el usuario puede corregir e intentar de nuevo
    return // IMPORTANTE: detener ejecución aquí
  }
  
  // DEBUG: Verificar cliente después del pago
  
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
        const result = await apiCall(`/customers/${lastSale.value.customer_id}`)
        if (result.success) {
          customerInfo = result.data
        }
      } catch (error) {
      }
    }
    
    let phoneNumber = null
    let cleanPhone = null
    
    if (customerInfo && customerInfo.id !== 7) {
      // Cliente registrado (no es Cliente Final) - intentar usar su teléfono
      phoneNumber = customerInfo.phone
      
      if (phoneNumber && phoneNumber.trim() !== '') {
        // Validar el teléfono registrado
        cleanPhone = cleanPhoneNumber(phoneNumber)
        
        if (isValidPhoneNumber(cleanPhone)) {
          // Teléfono válido - usar automáticamente (sin alerta)
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
      // Cliente Final (ID 7) o sin cliente - solicitar teléfono
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
    const pdfBlob = await generateInvoicePDF()
    
    // Enviar por WhatsApp usando API
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
 * Generar PDF de factura usando plantilla centralizada
 * SIEMPRE genera el mismo PDF (vectorial, alta calidad) para: imprimir, descargar, WhatsApp
 */
const generateInvoicePDF = async () => {
  try {
    if (!lastSale.value) {
      throw new Error('No hay datos de venta para generar el PDF')
    }

    // Preparar datos de la factura para la plantilla
    const invoiceData = {
      invoice_number: lastSale.value.invoiceNumber || lastSale.value.invoice_number || 'SIN-NUMERO',
      created_at: lastSale.value.date || lastSale.value.created_at || new Date(),
      customer_name: lastSale.value.customer || lastSale.value.customer_name || 'Cliente Final',
      cashier: lastSale.value.cashier || currentUser?.name || 'Vendedor',
      items: lastSale.value.items || [],
      subtotal: parseFloat(lastSale.value.subtotal || 0),
      discount: parseFloat(lastSale.value.discount || 0),
      tax: parseFloat(lastSale.value.tax || 0),
      total: parseFloat(lastSale.value.total || 0),
      payments: lastSale.value.payments || [],
      change: parseFloat(lastSale.value.change || 0),
      notes: lastSale.value.notes || ''
    }


    // Generar PDF usando plantilla centralizada (jsPDF vectorial)
    const pdf = await generateInvoicePDFTemplate(invoiceData, systemSettings)
    
    // Convertir a blob
    return getPDFBlob(pdf)

  } catch (error) {
    console.error('❌ Error generando PDF:', error)
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
    const headers = {
      'Authorization': `Bearer ${authService.getToken()}`,
      'Accept': 'application/json'
    }
    
    const result = await apiCall('/email/send-invoice', {
      method: 'POST',
      headers: headers,
      body: formData
    })
    
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
      alert('⚠️ La configuración del sistema requiere seleccionar un cliente específico para crear cotizaciones.\n\nNo se puede usar "Cliente Final".')
      showQuotationConfirmModal.value = false
      return
    }
    
    // Preparar items mapeados con product_name requerido
    const mappedItems = cart.items.map(item => ({
      product_id: item.id,
      variant_id: item.variant_id || null, // 👗 ID de variante si es producto fashion
      product_name: item.name, // Campo requerido por el backend
      quantity: item.quantity,
      unit_price: item.price,
      total_price: item.price * item.quantity
    }))
    
    
    // Preparar datos de la cotización
    const quoteData = {
      type: 'quote', // Campo requerido
      customer_id: selectedCustomer.value?.id || 1, // Usar cliente por defecto ID=1 (Cliente Final) si no hay seleccionado
      date: new Date().toISOString().split('T')[0], // Campo requerido formato YYYY-MM-DD
      subtotal: subtotal.value,
      tax_amount: tax.value,
      total: total.value,
      notes: `Cliente: ${selectedCustomer.value?.name || 'Cliente Final'} - Cotización generada desde POS`,
      items: mappedItems
    }

    
    // Crear la cotización usando el servicio directamente
    const result = await invoicesService.createQuote(quoteData)
    
    if (result.success && result.data) {
      // Usar el código real del backend
      const realQuoteCode = result.data.number
      
      // Preparar datos para el modal de cotización exitosa
      const modalQuotationData = {
        id: result.data.id, // ID de la cotización para WhatsApp
        code: realQuoteCode, // Código real del backend (ej: COT-000007)
        customer: selectedCustomer.value || { name: 'Cliente Final' }, // Objeto completo del cliente
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

// 🌍 Toggle para búsqueda global de productos
const toggleGlobalSearch = async () => {
  globalSearch.value = !globalSearch.value
  
  const scope = globalSearch.value ? 'global' : 'local'
  const icon = globalSearch.value ? '🌍' : '📍'
  const label = globalSearch.value ? 'GLOBAL' : 'LOCAL'
  
  // Recargar productos con el nuevo scope
  if (currentSession.value?.warehouse_id) {
    await appStore.loadProducts(currentSession.value.warehouse_id, scope)
    
    if (globalSearch.value) {
      showSuccess('🌍 Búsqueda global - Todas las tiendas')
    } else {
      showSuccess(`📍 Búsqueda local - ${currentSession.value.warehouse?.name || 'Tienda actual'}`)
    }
  }
}

// Aplicar el filtro global al cargar productos inicialmente
watch(() => currentSession.value?.warehouse_id, async (warehouseId) => {
  if (warehouseId) {
    const scope = globalSearch.value ? 'global' : 'local'
    await appStore.loadProducts(warehouseId, scope)
  }
}, { immediate: false })

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
      return
    }
    
    searchingQuotation.value = true
    
    try {
      
      // Llamar al servicio de cotizaciones
      const result = await invoicesService.searchQuote(search)

      
      if (result.success && result.data && result.data.length > 0) {
        
        // Encontrar la cotización específica por código
        const quote = result.data.find(invoice => 
          invoice.invoice_number === search && invoice.status === 'quotation'
        )
        
        
        if (quote) {
        
        // Validar disponibilidad de productos antes de mostrar el modal
        const itemsToLoad = quote.sale_items || quote.saleItems || quote.items || []
        const validItems = []
        const unavailableItems = []
        
        for (const item of itemsToLoad) {
          
          // Obtener product_id de diferentes fuentes posibles (compatibilidad con ambas tablas)
          const productId = item.product_id || item.product?.id || null
          
          console.log(`   - item.product?.id: ${item.product?.id}`)
          
          if (!productId) {
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
            
            unavailableItems.push({
              name: item.product_name || item.product?.name || `Producto ID ${productId}`,
              requestedQuantity: item.quantity,
              issue: 'not_found',
              message: 'Producto no encontrado en el catálogo'
            })
          } else if (product.manage_stock && product.stock < item.quantity) {
            // Producto existe pero sin stock suficiente
            
            unavailableItems.push({
              name: product.name,
              quantity: item.quantity,
              availableQuantity: product.stock,
              issue: 'insufficient_stock',
              message: `Stock insuficiente. Disponible: ${product.stock}, Solicitado: ${item.quantity}`
            })
            
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
            }
          } else {
            // Producto válido
            
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
  
  // 🔍 SIMULACIÓN DE LECTOR DE CÓDIGOS DE BARRAS
  // Si el usuario NO está en un input/textarea y presiona una tecla alfanumérica,
  // automáticamente enfocar el buscador y dejar que la tecla se escriba allí
  const isTypingInInput = event.target.tagName === 'INPUT' || 
                          event.target.tagName === 'TEXTAREA' || 
                          event.target.isContentEditable
  
  const isAlphanumeric = /^[a-zA-Z0-9]$/.test(event.key)
  
  // Si está escribiendo fuera de un input Y es una tecla alfanumérica
  if (!isTypingInInput && isAlphanumeric && searchInput.value) {
    event.preventDefault()
    searchInput.value.focus()
    // Agregar la tecla al searchTerm manualmente
    searchTerm.value += event.key
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
    
    const quotationInfo = window.quotationToLoad
    const quotationCode = quotationInfo.code
    
    try {
      // Buscar la cotización completa con sus items
      const result = await invoicesService.searchQuote(quotationCode)
      
      
      if (result.success && result.data && result.data.length > 0) {
        
        // Buscar por diferentes campos posibles
        const quotation = result.data.find(invoice => {
          return (invoice.invoice_number === quotationCode || invoice.custom_number === quotationCode || invoice.number === quotationCode) && 
                 invoice.status === 'quotation'
        })
        
        if (quotation) {
          
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
          
          if (itemsToLoad && itemsToLoad.length > 0) {
            
            const validItems = []
            const unavailableItems = []
            
            for (const item of itemsToLoad) {
              
              // Obtener product_id de diferentes fuentes posibles (compatibilidad con ambas tablas)
              const productId = item.product_id || item.product?.id || null
              
              console.log(`   - item.product?.id: ${item.product?.id}`)
              
              if (!productId) {
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
                
                unavailableItems.push({
                  name: item.product_name || item.product?.name || `Producto ID ${item.product_id}`,
                  requestedQuantity: item.quantity,
                  issue: 'not_found',
                  message: 'Producto no encontrado en el catálogo'
                })
              } else if (product.manage_stock && product.stock < item.quantity) {
                // Producto existe pero sin stock suficiente
                
                unavailableItems.push({
                  name: product.name,
                  quantity: item.quantity,
                  availableQuantity: product.stock,
                  issue: 'insufficient_stock',
                  message: `Stock insuficiente. Disponible: ${product.stock}, Solicitado: ${item.quantity}`
                })
                
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
                    manageStock: product.manage_stock || false,
                    image_url: product.image_url || null
                  }
                  
                  validItems.push(cartItem)
                }
              } else {
                // Producto válido
                
                const cartItem = {
                  id: productId,
                  name: product.name,
                  price: parseFloat(item.unit_price),
                  quantity: parseInt(item.quantity),
                  discount: parseFloat(item.discount_amount || item.discount || 0),
                  category: product.category || 'General',
                  currentStock: product.current_stock || 0,
                  manageStock: product.manage_stock || false,
                  image_url: product.image_url || null
                }
                
                
                validItems.push(cartItem)
              }
            }
            
            if (unavailableItems.length > 0) {
              // Hay productos con problemas, pero puede haber válidos
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
              
              cart.items.forEach(item => {
              })
            }
          } else {
          }
          
          // Guardar información de la cotización cargada
          loadedQuotation.value = {
            code: quotation.invoice_number,
            customer: quotation.customer_name,
            total: quotation.total_amount,
            created_at: quotation.created_at
          }
          
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
    
    // Obtener próximo número de factura desde el backend
    await fetchNextInvoiceNumber()
    
    // Solo cargar descuentos (que aún no están en el store global)
    loadDiscounts()
    
    // 🏢 Cargar warehouses para validar si mostrar botón Global/Local
    await loadWarehouses()
    
    // Cargar configuración de loyalty points
    await loadLoyaltySettings()
    
    // ✅ USAR ESTADO GLOBAL DE SESIÓN DE CAJA (ya inicializado por App.vue)
    // El appStore.initialize() ya se ejecutó en el splash screen
    if (appStore.cashSession.initialized) {
      // Usar el estado ya cargado del appStore
      hasOpenSession.value = appStore.cashSession.hasOpenSession
      currentSession.value = appStore.cashSession.current
      
      // Emitir warehouse actual al parent
      if (currentSession.value?.warehouse) {
        emit('warehouse-changed', currentSession.value.warehouse)
      }
    }
    
    // Verificar si hay una cotización pendiente de cargar
    setTimeout(() => {
      loadQuotationFromInvoices()
    }, 500)
    
    // 🎯 Cargar drafts existentes de la base de datos
    await loadSalesDrafts()
    
    // 🛍️ Debug: Verificar productos con variantes
    // 👗 Debug variantes en consola
    setTimeout(() => {
      const productsWithVariants = products.value.filter(p => p.variants && p.variants.length > 0)
      if (productsWithVariants.length > 0) {
        console.log('✅ Productos con variantes cargados:', productsWithVariants.length)
      } else {
        console.log('⚠️ No hay productos con variantes en el catálogo')
      }
    }, 2000)
    
    // Emitir estado inicial del carrito
    emit('cart-status-changed', cart.items.length > 0)
    
  } catch (error) {
    console.error('Error en inicialización de PosView:', error)
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
    } else {
      // Si no existe efectivo, mantener 'cash' o usar el primer método
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
      } else {
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
    
    // Emitir cambio en el estado del carrito después de cargar productos
    emit('cart-status-changed', cart.items.length > 0)
    
  } catch (error) {
    console.error('Error al cargar productos disponibles:', error)
  }
}

const handleAddPartialStock = (item) => {
  try {
    
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
      } else {
        alert(`⚠️ Ya tienes la cantidad máxima disponible de ${product.name} en el carrito`)
        return
      }
    } else {
      // Agregar nuevo producto al carrito
      cart.items.push(cartItem)
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


    // Generar exactamente el mismo PDF que se usa para imprimir
    const pdfBlob = await generateQuotationPDFBlob(quotationData)

    if (!pdfBlob) {
      throw new Error('Error generando PDF de cotización')
    }

    // Preparar mensaje personalizado para WhatsApp
    const message = `📋 *Cotización ${quotationData.code}*\n\n` +
                   `📅 Fecha: ${new Date().toLocaleDateString('es-CO')}\n` +
                   `👤 Cliente: ${customerName}\n` +
                   `💰 Total: $${parseFloat(quotationData.total || 0).toLocaleString()}\n\n` +
                   `Puede usar este código para realizar su compra posteriormente.\n\n` +
                   `¡Gracias por elegirnos! 😊`

    const fileName = `cotizacion_${quotationData.code}_${Date.now()}.pdf`

    // Enviar cotización por WhatsApp con el PDF generado igual al de impresión
    const result = await whatsappService.sendQuotationWithPDF(
      formattedPhone,
      pdfBlob,
      message,
      fileName,
      customerName
    )

    if (result.success) {
      
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
    
    
  } catch (error) {
    console.error('❌ Error al generar ticket de impresión:', error)
    // Fallback a impresión normal
    window.print()
  }
}

/**
 * Generar PDF Blob de cotización usando plantilla centralizada
 * SIEMPRE genera el mismo PDF (vectorial, alta calidad) para: imprimir, descargar, WhatsApp
 */
const generateQuotationPDFBlob = async (quotationData) => {
  try {
    // Preparar datos de la cotización para la plantilla
    const quotationForTemplate = {
      quotation_number: quotationData.code,
      date: quotationData.created_at || new Date(),
      customer_name: typeof quotationData.customer === 'object' && quotationData.customer !== null 
        ? (quotationData.customer.name || quotationData.customer.customer_name || 'Cliente General')
        : (typeof quotationData.customer === 'string' ? quotationData.customer : 'Cliente General'),
      cashier: currentUser?.name || 'Vendedor',
      items: quotationData.items || [],
      subtotal: parseFloat(quotationData.subtotal || quotationData.total || 0),
      discount: parseFloat(quotationData.discount_amount || 0),
      tax: parseFloat(tax.value || 0),
      total: parseFloat(quotationData.total || 0),
      notes: quotationData.notes || '',
      validity_days: 15
    }

    // Generar PDF usando plantilla centralizada (jsPDF vectorial)
    const pdf = await generateQuotationPDFTemplate(quotationForTemplate, systemSettings)
    
    // Convertir a blob
    return getPDFBlob(pdf)
    
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
}

const handleQRResult = (data) => {
  
  // Detener el scanner
  stopQRScanner()
  
  // Verificar si es un código de cotización válido (formato: COT-XXXXXX)
  if (data.match(/^COT-\d+$/)) {
    
    // Poner el código en el campo de búsqueda y procesarlo
    searchTerm.value = data
    
    // Procesar como búsqueda de cotización
    handleSearchEnter()
    
  } else {
    alert('❌ Código QR no válido. Se esperaba un código de cotización (COT-XXXXXX)')
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
      
      // 🏪 RECARGAR PRODUCTOS DE LA BODEGA SELECCIONADA
      if (currentSession.value?.warehouse_id) {
        await appStore.loadProducts(currentSession.value.warehouse_id)
      }
      
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
 * Permite cerrar si hay sesión abierta O si es primera visita (tour)
 */
const handleCloseCashOpenModal = () => {
  // Permitir cerrar si hay sesión abierta O si es primera visita (para el tour)
  if (hasOpenSession.value || isFirstVisit.value) {
    showCashOpenModal.value = false
  } else {
    // Si no hay sesión y no es primera visita, mostrar advertencia pero mantener modal abierto
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
  
  // Cargar sesión actual por si no está cargada
  try {
    await loadCurrentSession()
  } catch (error) {
    console.error('🎯 Error cargando sesión:', error)
  }
  
  // Verificar nuevamente después de cargar
  if (!currentSession.value) {
    showError('No hay una sesión de caja abierta')
    return
  }
  
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

// ==================== FUNCIONES DE PEDIDOS WEB ====================

/**
 * Manejar cuando se carga un pedido web
 */
const handleWebOrderLoaded = async (order) => {
  try {
    
    // Buscar cliente por documento (prioridad) o teléfono
    let customer = null
    
    // 1. Intentar encontrar el cliente por documento (si está disponible)
    if (order.customer_document) {
      customer = await customersService.findByDocument(order.customer_document)
      if (customer) {
      }
    }
    
    // 2. Si no se encontró por documento, intentar por teléfono
    if (!customer) {
      customer = await customersService.findByPhone(order.customer_phone)
      if (customer) {
      }
    }
    
    // 3. Si el cliente existe, asignarlo directamente
    if (customer) {
      // Asignar al selectedCustomer global
      selectedCustomer.value = customer
      
      // También asignarlo al tab actual
      const currentTab = salesTabs.value.find(t => t.id === activeTabId.value)
      if (currentTab) {
        currentTab.customer = customer
        currentTab.selectedCustomer = customer
      }
      
      // Cargar productos al carrito
      await loadOrderProductsToCart(order)
      
      showSuccess(`Pedido #${order.order_number} cargado. Cliente: ${customer.name}`)
      
    } else {
      // 4. Si el cliente NO existe, pedir confirmación antes de crear
      pendingCustomerData.value = {
        name: order.customer_name,
        document: order.customer_document || 'No proporcionado',
        phone: order.customer_phone,
        address: order.customer_address || ''
      }
      pendingWebOrder.value = order
      showConfirmCustomerModal.value = true
    }
    
  } catch (error) {
    console.error('❌ Error cargando pedido web:', error)
    showError('Error al cargar el pedido web')
  }
}

/**
 * Cargar productos del pedido web al carrito
 */
const loadOrderProductsToCart = async (order) => {
  // Agregar los productos al carrito
  for (const item of order.items) {
    const product = filteredProducts.value.find(p => p.id === item.product_id)
    
    if (product) {
      await addToCart(product, item.quantity)
    } else {
    }
  }
  
  // Agregar nota del pedido si existe
  const currentTab = salesTabs.value.find(t => t.id === activeTabId.value)
  if (currentTab && order.note) {
    currentTab.note = `Pedido Web #${order.order_number}\n${order.note}`
  }
}

/**
 * Confirmar y crear nuevo cliente desde pedido web
 */
const handleConfirmNewCustomer = async () => {
  try {
    if (!pendingWebOrder.value || !pendingCustomerData.value) return
    
    // Crear el cliente
    const newCustomer = await customersService.create({
      name: pendingCustomerData.value.name,
      phone: pendingCustomerData.value.phone,
      address: pendingCustomerData.value.address,
      document_type: 'CC',
      document_number: pendingCustomerData.value.document === 'No proporcionado' ? '' : pendingCustomerData.value.document,
      email: '',
      active: true
    })
    
    
    // Actualizar la lista de clientes en el store para que aparezca de inmediato
    await appStore.refresh('customers')
    
    // Asignar el cliente al carrito actual (selectedCustomer global)
    selectedCustomer.value = newCustomer
    
    // También asignarlo al tab actual
    const currentTab = salesTabs.value.find(t => t.id === activeTabId.value)
    if (currentTab) {
      currentTab.customer = newCustomer
      currentTab.selectedCustomer = newCustomer
    }
    
    // Cargar productos al carrito
    await loadOrderProductsToCart(pendingWebOrder.value)
    
    showSuccess(`Pedido #${pendingWebOrder.value.order_number} cargado. Cliente: ${newCustomer.name}`)
    
    // Limpiar datos pendientes
    pendingCustomerData.value = null
    pendingWebOrder.value = null
    
  } catch (error) {
    console.error('❌ Error creando cliente:', error)
    showError('Error al crear el cliente')
  }
}

/**
 * Cancelar creación de cliente
 */
const handleCancelNewCustomer = () => {
  showInfo('Pedido web cancelado. No se creó el cliente.')
  pendingCustomerData.value = null
  pendingWebOrder.value = null
}

// ==================== FIN FUNCIONES DE PEDIDOS WEB ====================

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
  openOpenCashModal: showOpenCashModal,
  showReturnsModal
})
</script>

<style scoped>
.product-card {
  background-color: white;
  border-radius: 1rem;
  box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
  border: 1px solid #f3f4f6;
  overflow: hidden;
  transition: all 0.3s ease;
}

.dark .product-card {
  background-color: #18181b; /* zinc-900 */
  border-color: #27272a; /* zinc-800 */
}

.product-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px -2px rgba(0, 0, 0, 0.1);
  border-color: #e5e7eb;
}

.dark .product-card:hover {
  border-color: #3f3f46; /* zinc-700 */
}

.cart-item {
  background-color: white;
  border-radius: 0.75rem;
  box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
  border: 1px solid #f3f4f6;
  padding: 1rem;
  transition: all 0.2s ease;
}

.dark .cart-item {
  background-color: #18181b; /* zinc-900 */
  border-color: #27272a; /* zinc-800 */
}

.cart-item:hover {
  box-shadow: 0 2px 8px -2px rgba(0, 0, 0, 0.1);
}

.dark .cart-item:hover {
  border-color: #3f3f46; /* zinc-700 */
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

/* Animaciones del modal de bienvenida */
@keyframes fadeIn {
  from {
    opacity: 0;
  }
  to {
    opacity: 1;
  }
}

@keyframes scaleIn {
  from {
    opacity: 0;
    transform: scale(0.9);
  }
  to {
    opacity: 1;
    transform: scale(1);
  }
}

.animate-fade-in {
  animation: fadeIn 0.3s ease-out;
}

.animate-scale-in {
  animation: scaleIn 0.4s ease-out;
}

/* 🚀 Animación de pulso sutil para botón COBRAR listo */
@keyframes pulse-subtle {
  0%, 100% {
    box-shadow: 0 10px 25px -5px rgba(16, 185, 129, 0.4);
  }
  50% {
    box-shadow: 0 15px 35px -5px rgba(16, 185, 129, 0.6);
  }
}

.animate-pulse-subtle {
  animation: pulse-subtle 2s ease-in-out infinite;
}

/* 💚 Clase para botón COBRAR listo - Pulso suave profesional */
.cobrar-ready-pulse {
  animation: cobrar-glow 2s ease-in-out infinite;
}

@keyframes cobrar-glow {
  0%, 100% {
    box-shadow: 0 4px 20px -2px rgba(16, 185, 129, 0.4), 0 0 0 0 rgba(16, 185, 129, 0.2);
  }
  50% {
    box-shadow: 0 8px 30px -2px rgba(16, 185, 129, 0.6), 0 0 0 4px rgba(16, 185, 129, 0.1);
  }
}

/* ✨ Efecto de brillo que recorre el botón */
@keyframes shimmer {
  0% {
    transform: translateX(-100%);
  }
  100% {
    transform: translateX(100%);
  }
}

.animate-shimmer {
  animation: shimmer 2.5s ease-in-out infinite;
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