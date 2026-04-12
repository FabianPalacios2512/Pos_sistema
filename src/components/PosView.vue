<template>
  <div class="relative bg-[#F9FAFB] dark:bg-gradient-to-b dark:from-[#141417] dark:via-slate-900 dark:to-[#0a0a0c] transition-colors duration-300" style="height: 100%; display: flex; flex-direction: column; overflow: hidden;" @click="handlePosClick">
    
    <!-- Modal de Bienvenida Primera Vez -->
    <Teleport to="body">
      <div v-if="showWelcomeModal" class="fixed inset-0 z-[100] flex items-end sm:items-center justify-center bg-black/70 animate-fade-in">
        <div class="bg-white dark:bg-zinc-900 sm:rounded-2xl rounded-t-2xl shadow-2xl max-w-md w-full sm:mx-4 overflow-hidden animate-scale-in border border-gray-200 dark:border-zinc-800">
          
          <!-- Icono Hero -->
          <div class="pt-8 sm:pt-10 pb-4 sm:pb-6 flex justify-center">
            <div class="w-16 h-16 sm:w-20 sm:h-20 bg-emerald-50 dark:bg-emerald-950/50 rounded-full flex items-center justify-center border-4 border-emerald-100 dark:border-emerald-800/50">
              <svg class="w-8 h-8 sm:w-10 sm:h-10 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
              </svg>
            </div>
          </div>
          
          <!-- Contenido -->
          <div class="px-6 sm:px-8 pb-6 sm:pb-8 space-y-5">
            <!-- Título y Subtítulo -->
            <div class="text-center space-y-1.5">
              <h2 class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-white">Te damos la bienvenida a 105 POS Pro</h2>
              <p class="text-gray-500 dark:text-zinc-400 text-sm">El corazón de tu negocio empieza a latir hoy</p>
            </div>
            
            <!-- Lista de beneficios -->
            <div class="space-y-3">
              <div class="flex items-start gap-3">
                <div class="w-5 h-5 rounded-full bg-emerald-50 dark:bg-emerald-900/30 flex items-center justify-center flex-shrink-0 mt-0.5 border border-emerald-100 dark:border-emerald-800/50">
                  <svg class="w-3 h-3 text-emerald-600 dark:text-emerald-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                  </svg>
                </div>
                <p class="text-sm text-gray-700 dark:text-zinc-300 leading-relaxed">Cómo realizar tu primera venta en segundos</p>
              </div>
              
              <div class="flex items-start gap-3">
                <div class="w-5 h-5 rounded-full bg-emerald-50 dark:bg-emerald-900/30 flex items-center justify-center flex-shrink-0 mt-0.5 border border-emerald-100 dark:border-emerald-800/50">
                  <svg class="w-3 h-3 text-emerald-600 dark:text-emerald-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                  </svg>
                </div>
                <p class="text-sm text-gray-700 dark:text-zinc-300 leading-relaxed">Gestionar tu inventario sin complicaciones</p>
              </div>
              
              <div class="flex items-start gap-3">
                <div class="w-5 h-5 rounded-full bg-emerald-50 dark:bg-emerald-900/30 flex items-center justify-center flex-shrink-0 mt-0.5 border border-emerald-100 dark:border-emerald-800/50">
                  <svg class="w-3 h-3 text-emerald-600 dark:text-emerald-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                  </svg>
                </div>
                <p class="text-sm text-gray-700 dark:text-zinc-300 leading-relaxed">Cerrar caja y ver tus ganancias del día</p>
              </div>
            </div>
            
            <!-- Badge de tiempo -->
            <div class="flex items-center justify-center gap-2 py-1">
              <svg class="w-4 h-4 text-gray-400 dark:text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
              </svg>
              <span class="text-xs font-medium text-gray-500 dark:text-zinc-500">Tiempo estimado: 3 minutos</span>
            </div>
            
            <!-- Botón Principal -->
            <button 
              @click="handleWelcomeStart"
              class="w-full px-6 py-3.5 bg-slate-900 dark:bg-slate-700 hover:bg-black dark:hover:bg-slate-600 text-white font-bold text-sm rounded-xl shadow-lg shadow-slate-900/20 dark:shadow-slate-900/50 transition-all duration-200 active:scale-[0.98]"
            >
              Iniciar Recorrido Rápido
            </button>
            
            <!-- Botón Secundario -->
            <div class="text-center pb-2 sm:pb-0">
              <button 
                @click="handleWelcomeSkip"
                class="text-sm text-gray-500 dark:text-zinc-500 hover:text-gray-700 dark:hover:text-zinc-300 font-medium transition-colors"
              >
                Saltar por ahora
              </button>
            </div>
          </div>
          
        </div>
      </div>
    </Teleport>
    
    <!-- Tour Educativo del POS -->
    <ContextualTour 
      ref="posTourRef"
      module-name="pos"
      :steps="posTourSteps"
      :auto-start="false"
      @complete="handlePosTourComplete"
      @skip="handlePosTourSkip"
    />
    
    <!-- BARRA DE HERRAMIENTAS EMPRESARIAL -->
    <div class="sticky top-0 z-30 bg-white dark:bg-[#121214] border-b border-gray-200/60 dark:border-zinc-800/60 shadow-[0_1px_3px_rgba(0,0,0,0.04)] dark:shadow-none">
    
    <div class="px-4 lg:px-6 py-4">
      <div class="flex flex-col md:flex-row items-center justify-between gap-5">
        
        <div class="flex items-center gap-3 flex-1 w-full md:w-auto">
          <!-- BOTÓN CAJA MÓVIL (Solo visible en móvil, al lado del buscador) -->
          <button 
            @click="hasOpenSession ? showCloseCashModal() : showOpenCashModal()"
            class="lg:hidden flex-shrink-0 w-10 h-10 rounded-xl flex items-center justify-center transition-all duration-200"
            :class="hasOpenSession 
              ? 'bg-emerald-500 text-white' 
              : 'bg-[#f8f9fa] dark:bg-[#1e1f20] text-rose-500 dark:text-rose-400'"
            :title="hasOpenSession ? 'Cerrar Caja' : 'Abrir Caja para vender'"
          >
            <svg v-if="hasOpenSession" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
            </svg>
            <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
            </svg>
          </button>
          
          <div class="flex-1 max-w-2xl relative group z-20">
            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
              <svg class="h-5 w-5 group-focus-within:text-slate-600 dark:group-focus-within:text-slate-400 transition-colors"
                   :class="isFastFoodStore ? 'text-orange-400 dark:text-orange-500' : 'text-slate-400 dark:text-zinc-500'"
                   fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
              </svg>
            </div>
            
            <input
              ref="searchInput"
              v-model="searchTerm"
              type="text"
              placeholder="Buscar productos, SKU o escanear..."
              class="block w-full h-12 pl-12 pr-32 text-[15px] font-medium text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-zinc-500 rounded-xl focus:outline-none transition-all duration-200"
              :class="isFastFoodStore 
                ? 'bg-gray-50 dark:bg-zinc-800/80 border border-gray-200 dark:border-zinc-700 focus:bg-white dark:focus:bg-zinc-800 focus:border-orange-300 dark:focus:border-orange-700 focus:ring-2 focus:ring-orange-100 dark:focus:ring-orange-900/30'
                : 'bg-gray-50 dark:bg-zinc-900 border border-gray-200 dark:border-zinc-700/80 focus:bg-white dark:focus:bg-zinc-800 focus:border-slate-400 dark:focus:border-zinc-500 focus:ring-2 focus:ring-slate-100 dark:focus:ring-zinc-800'"
              :tabindex="showCustomerSelector ? -1 : 0"
              @keydown.escape="clearSearch"
              @keydown.enter.prevent="handleSearchEnter"
              @input="handleBarcodeInput"
              @focus="handleSearchInputFocus"
            />
            
            <div class="absolute inset-y-0 right-0 flex items-center pr-2 space-x-1">
              
              <button
                @click="startQRScanner"
                class="p-2 text-slate-400 dark:text-zinc-500 hover:text-slate-600 dark:hover:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800/30 rounded-xl transition-colors"
                title="Escanear código QR"
              >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path>
                </svg>
              </button>
              
              <button
                v-if="searchTerm"
                @click="clearSearch"
                class="p-2 text-slate-400 dark:text-zinc-500 hover:text-rose-500 dark:hover:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-950/30 rounded-xl transition-colors"
                title="Limpiar búsqueda"
              >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
              </button>
            </div>
          </div>

          <!-- DEBUG: Verificar condición del botón -->
          <!-- shouldShowMultiWarehouseFeatures: {{ shouldShowMultiWarehouseFeatures }} -->
          <!-- warehouses.length: {{ warehouses.length }} -->
          <!-- hasOpenSession: {{ hasOpenSession }} -->
          <!-- plan: {{ appStore.tenantPlan }} -->

          <!-- Toggle Búsqueda Global/Local (Siempre visible con sesión) -->
          <button
            v-if="shouldShowMultiWarehouseFeatures"
            @click="toggleGlobalSearch"
            class="flex items-center gap-2 px-4 h-10 rounded-2xl text-[13px] font-medium transition-all duration-200"
            :class="globalSearch 
              ? 'bg-blue-50 dark:bg-blue-950/30 text-blue-700 dark:text-blue-400 hover:bg-blue-100 dark:hover:bg-blue-900/40' 
              : 'bg-emerald-50 dark:bg-emerald-950/30 text-emerald-700 dark:text-emerald-400 hover:bg-emerald-100 dark:hover:bg-emerald-900/40'"
            :title="globalSearch ? 'Buscando en TODAS las tiendas' : 'Buscar SOLO en ' + (currentSession?.warehouse?.name || 'tienda actual')"
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
        
        <div class="flex items-center gap-3.5 w-full md:w-auto justify-end">

          <!-- Botón Pedido Web - Elevated Toolbar Element -->
          <button
            v-if="canUseWebOrders"
            @click="showLoadWebOrderModal = true" 
            class="hidden sm:flex items-center gap-2 px-4 h-10 rounded-lg border transition-all duration-200 bg-blue-50/60 dark:bg-blue-950/20 border-blue-200/80 dark:border-blue-800/40 text-gray-800 dark:text-zinc-200 hover:bg-blue-100/80 dark:hover:bg-blue-900/30 hover:border-blue-300 dark:hover:border-blue-700/60 hover:shadow-sm"
            title="Cargar pedido web"
          >
            <svg class="w-4 h-4 text-blue-500 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
            </svg>
            <span class="text-sm font-semibold">Pedido Web</span>
          </button>

          <!-- Devoluciones - Elevated Toolbar Element -->
          <button
            id="tour-pos-returns"
            @click="showReturnsModal = true"
            :disabled="quotationMode"
            class="hidden sm:flex items-center gap-2 px-4 h-10 rounded-lg transition-all duration-200 border"
            :class="quotationMode 
              ? 'bg-gray-100 dark:bg-zinc-800 border-gray-200 dark:border-zinc-700 text-gray-400 dark:text-zinc-600 cursor-not-allowed' 
              : 'bg-slate-50/80 dark:bg-zinc-800/60 border-slate-200 dark:border-zinc-700/80 text-gray-800 dark:text-zinc-200 hover:bg-slate-100 dark:hover:bg-zinc-700/60 hover:border-slate-300 dark:hover:border-zinc-600 hover:shadow-sm'"
          >
            <svg class="w-4 h-4" :class="quotationMode ? 'text-gray-400 dark:text-zinc-600' : 'text-slate-500 dark:text-zinc-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"></path></svg>
            <span class="text-sm font-semibold">Devoluciones</span>
          </button>

          <!-- Abonos CrediTienda - Solo visible si hay clientes con deuda activa y caja abierta -->
          <button
            v-if="customersWithDebt.length > 0 && hasOpenSession"
            @click="openAbonoModal"
            :disabled="quotationMode"
            class="hidden sm:flex items-center gap-2 px-4 h-10 rounded-lg transition-all duration-200 border"
            :class="quotationMode 
              ? 'bg-gray-100 dark:bg-zinc-800 border-gray-200 dark:border-zinc-700 text-gray-400 dark:text-zinc-600 cursor-not-allowed' 
              : 'bg-emerald-50/60 dark:bg-emerald-950/20 border-emerald-200/80 dark:border-emerald-800/40 text-gray-800 dark:text-zinc-200 hover:bg-emerald-100/80 dark:hover:bg-emerald-900/30 hover:border-emerald-300 dark:hover:border-emerald-700/60 hover:shadow-sm'"
          >
            <svg class="w-4 h-4" :class="quotationMode ? 'text-gray-400 dark:text-zinc-600' : 'text-emerald-500 dark:text-emerald-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span class="text-sm font-semibold">Abonos</span>
            <span class="px-1.5 py-0.5 rounded text-[10px] font-bold bg-emerald-100 dark:bg-emerald-900/40 text-emerald-700 dark:text-emerald-400">{{ customersWithDebt.length }}</span>
          </button>
          
          <!-- DIAN - Toggle Switch Profesional (TEMPORALMENTE DESHABILITADO) -->
          <!-- TODO: Reactivar cuando esté listo el módulo de facturación electrónica
          <button
            @click="toggleElectronicInvoicing"
            class="hidden sm:flex items-center gap-3 px-4 h-10 rounded-lg border transition-all duration-200 bg-white dark:bg-zinc-900 border-gray-200 dark:border-zinc-700 hover:bg-gray-50 dark:hover:bg-zinc-800 hover:border-gray-300 dark:hover:border-zinc-600"
            :title="electronicInvoicingEnabled ? 'DIAN activa: Las facturas se validan con la DIAN (toma ~15s)' : 'DIAN inactiva: Facturas locales instantáneas'"
          >
            <span class="text-[13px] font-medium text-gray-700 dark:text-zinc-300">Facturación DIAN</span>
            <div 
              class="relative w-9 h-5 rounded-full transition-colors duration-200"
              :class="electronicInvoicingEnabled 
                ? 'bg-gray-900 dark:bg-emerald-500' 
                : 'bg-gray-300 dark:bg-zinc-600'"
            >
              <div 
                class="absolute top-0.5 w-4 h-4 bg-white rounded-full shadow transition-transform duration-200"
                :class="electronicInvoicingEnabled ? 'translate-x-4' : 'translate-x-0.5'"
              ></div>
            </div>
          </button>
          -->
          
          <!-- WhatsApp: Solo para Premium y Enterprise -->
          <WhatsAppStatus v-if="canUseWhatsApp" id="tour-pos-whatsapp" ref="whatsappStatus" class="hidden sm:block" />
          
          <!-- Cerrar Caja - Primary Elevated Toolbar Button -->
          <button 
            id="tour-pos-cash-btn"
            @click="hasOpenSession ? showCloseCashModal() : showOpenCashModal()"
            class="hidden sm:flex items-center gap-2 px-5 h-10 rounded-lg transition-all duration-200"
            :class="hasOpenSession 
              ? 'bg-slate-900 dark:bg-slate-700 text-white hover:bg-black dark:hover:bg-slate-600 shadow-md shadow-slate-400/20 dark:shadow-slate-900/40' 
              : 'bg-white dark:bg-zinc-900 border border-gray-200 dark:border-zinc-700/80 text-gray-700 dark:text-zinc-300 hover:bg-gray-50 dark:hover:bg-zinc-800 hover:border-gray-300 dark:hover:border-zinc-600'"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path v-if="hasOpenSession" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
              <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
            </svg>
            <span class="text-sm font-semibold">{{ hasOpenSession ? 'Cerrar Caja' : 'Abrir Turno' }}</span>
          </button>

        </div>
      </div>
    </div>
</div>
    <!-- FIN BARRA DE HERRAMIENTAS EMPRESARIAL -->
    
    <!-- BOTÓN FLOTANTE CARRITO MÓVIL -->
    <button 
      @click="showMobileCart = true"
      class="lg:hidden fixed bottom-6 right-6 z-40 flex items-center gap-2 px-5 py-3.5 rounded-2xl font-medium text-sm transition-all duration-300 active:scale-95"
      :class="cart.items.length > 0 
        ? 'bg-gray-900 dark:bg-white text-white dark:text-zinc-900 shadow-xl' 
        : 'bg-white dark:bg-[#1e1f20] text-gray-600 dark:text-zinc-300 shadow-lg'"
    >
      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
      </svg>
      <span v-if="cart.items.length > 0">${{ subtotal.toLocaleString() }}</span>
      <span v-else>Carrito</span>
      <!-- Badge cantidad -->
      <span v-if="cart.items.length > 0" class="absolute -top-2 -right-2 w-6 h-6 bg-red-500 text-white text-xs font-bold rounded-full flex items-center justify-center shadow-lg">
        {{ cart.items.length }}
      </span>
    </button>

    <!-- MODAL CARRITO MÓVIL (Slide from bottom) -->
    <Teleport to="body">
      <Transition
        enter-active-class="transition ease-out duration-300"
        enter-from-class="opacity-0"
        enter-to-class="opacity-100"
        leave-active-class="transition ease-in duration-200"
        leave-from-class="opacity-100"
        leave-to-class="opacity-0"
      >
        <div v-if="showMobileCart" class="lg:hidden fixed inset-0 z-50 bg-black/70" @click="showMobileCart = false"></div>
      </Transition>
      
      <Transition
        enter-active-class="transition ease-out duration-300"
        enter-from-class="translate-y-full"
        enter-to-class="translate-y-0"
        leave-active-class="transition ease-in duration-200"
        leave-from-class="translate-y-0"
        leave-to-class="translate-y-full"
      >
        <div v-if="showMobileCart" class="lg:hidden fixed inset-x-0 bottom-0 z-50 bg-white dark:bg-zinc-900 rounded-t-3xl shadow-2xl max-h-[90vh] flex flex-col border-t border-gray-200 dark:border-zinc-700">
          <!-- Handle para arrastrar -->
          <div class="flex justify-center pt-3 pb-2">
            <div class="w-12 h-1.5 bg-gray-300 dark:bg-zinc-600 rounded-full"></div>
          </div>
          
          <!-- Alerta Caja Cerrada (Solo si no hay sesión) -->
          <div v-if="!hasOpenSession" class="mx-5 mb-3 p-3 bg-amber-50 dark:bg-amber-950/30 border border-amber-200 dark:border-amber-800 rounded-xl flex items-center gap-3">
            <div class="w-10 h-10 rounded-full bg-amber-100 dark:bg-amber-900/50 flex items-center justify-center flex-shrink-0">
              <svg class="w-5 h-5 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
              </svg>
            </div>
            <div class="flex-1">
              <p class="text-sm font-bold text-amber-800 dark:text-amber-300">Caja Cerrada</p>
              <p class="text-xs text-amber-600 dark:text-amber-500">Abre la caja para procesar ventas</p>
            </div>
            <button 
              @click="showOpenCashModal(); showMobileCart = false;"
              class="px-3 py-2 bg-amber-500 hover:bg-amber-600 text-white text-xs font-bold rounded-lg transition-colors"
            >
              Abrir
            </button>
          </div>
          
          <!-- Header del carrito móvil -->
          <div class="px-5 pb-4 flex items-center justify-between">
            <div>
              <h2 class="text-xl font-bold text-gray-900 dark:text-white">Ticket de Venta</h2>
              <p class="text-xs text-gray-500 dark:text-zinc-500 mt-0.5">{{ cart.items.length }} productos</p>
            </div>
            <button @click="showMobileCart = false" class="p-2.5 hover:bg-gray-100 dark:hover:bg-zinc-800 rounded-xl transition-colors">
              <svg class="w-5 h-5 text-gray-500 dark:text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
              </svg>
            </button>
          </div>
          
          <!-- Cliente -->
          <div class="px-5 pb-4">
            <button
              @click="showCustomerSelector = true; showMobileCart = false;"
              class="w-full flex items-center justify-between p-3.5 rounded-2xl transition-all duration-200 border"
              :class="selectedCustomer 
                ? 'bg-indigo-50 dark:bg-indigo-950/30 border-indigo-200 dark:border-indigo-800' 
                : 'bg-gray-50 dark:bg-zinc-800/50 border-gray-200 dark:border-zinc-700'"
            >
              <div class="flex items-center gap-3">
                <div class="w-11 h-11 rounded-xl flex items-center justify-center"
                     :class="selectedCustomer ? 'bg-indigo-100 dark:bg-indigo-900/50' : 'bg-white dark:bg-zinc-700'">
                  <svg class="w-5 h-5" :class="selectedCustomer ? 'text-indigo-600 dark:text-indigo-400' : 'text-gray-400 dark:text-zinc-500'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                  </svg>
                </div>
                <div class="text-left">
                  <p class="text-[10px] font-bold uppercase tracking-wider" :class="selectedCustomer ? 'text-indigo-600 dark:text-indigo-400' : 'text-gray-400 dark:text-zinc-500'">
                    {{ selectedCustomer ? 'Cliente Asignado' : 'Sin Cliente' }}
                  </p>
                  <p class="text-sm font-semibold" :class="selectedCustomer ? 'text-gray-900 dark:text-white' : 'text-gray-500 dark:text-zinc-400'">
                    {{ selectedCustomer ? selectedCustomer.name : 'Tocar para seleccionar...' }}
                  </p>
                </div>
              </div>
              <svg class="w-5 h-5 text-gray-400 dark:text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
              </svg>
            </button>
          </div>
          
          <!-- Items del carrito móvil -->
          <div class="flex-1 overflow-y-auto px-5" style="max-height: 40vh;">
            <div v-if="cart.items.length === 0" class="flex flex-col items-center justify-center py-12 text-center">
              <!-- SVG bolsa de compras elegante -->
              <svg class="w-24 h-24 text-gray-200 dark:text-zinc-700 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
              </svg>
              <p class="text-base font-semibold text-gray-400 dark:text-zinc-500">Tu carrito está vacío</p>
              <p class="text-sm text-gray-400 dark:text-zinc-600 mt-1">Agrega productos para comenzar</p>
            </div>
            
            <div v-else class="space-y-2">
              <div v-for="item in cart.items" :key="item.id" class="flex items-center gap-3 p-3 bg-gray-50 dark:bg-zinc-800/50 rounded-xl border border-gray-100 dark:border-zinc-800">
                <div class="w-14 h-14 rounded-xl overflow-hidden flex items-center justify-center flex-shrink-0 border"
                     :class="isRealProductImage(item.image_url) ? 'bg-white dark:bg-zinc-700 border-gray-200 dark:border-zinc-600' : 'bg-[#F3F4F6] dark:bg-zinc-800 border-[#E5E7EB] dark:border-zinc-700'">
                  <img v-if="isRealProductImage(item.image_url)" :src="getCartItemImage(item)" class="max-w-full max-h-full object-contain" :alt="item.name"/>
                  <div v-else-if="isFastFoodStore" class="w-8 h-8 rounded-full bg-gradient-to-br from-orange-100 to-amber-100 dark:from-zinc-600 dark:to-zinc-600/80 flex items-center justify-center">
                    <svg class="w-4 h-4 text-orange-300 dark:text-orange-500/60" viewBox="0 0 24 24" fill="currentColor">
                      <path d="M8.1 13.34l2.83-2.83L3.91 3.5c-1.56 1.56-1.56 4.09 0 5.66l4.19 4.18zm6.78-1.81c1.53.71 3.68.21 5.27-1.38 1.91-1.91 2.28-4.65.81-6.12-1.46-1.46-4.20-1.10-6.12.81-1.59 1.59-2.09 3.74-1.38 5.27L3.7 19.87l1.41 1.41L12 14.41l6.88 6.88 1.41-1.41L13.41 13l1.47-1.47z"/>
                    </svg>
                  </div>
                  <svg v-else class="w-6 h-6" viewBox="0 0 24 24" fill="none">
                    <path d="M5 9h14l1 12H4L5 9z" fill="#CBD5E1" class="dark:fill-zinc-700"/>
                    <path d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" stroke="#94A3B8" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" fill="none" class="dark:stroke-zinc-500"/>
                  </svg>
                </div>
                <div class="flex-1 min-w-0">
                  <p class="text-sm font-semibold text-gray-800 dark:text-zinc-200 truncate">{{ item.name.split(' (')[0] }}</p>
                  <p class="text-sm font-bold text-emerald-600 dark:text-emerald-400 mt-0.5">${{ (item.price * item.quantity).toLocaleString() }}</p>
                </div>
                <div class="flex items-center gap-1">
                  <button @click="updateQuantity(item.id, item.quantity - 1)" class="w-8 h-8 rounded-lg bg-white dark:bg-zinc-700 border border-gray-200 dark:border-zinc-600 flex items-center justify-center text-gray-600 dark:text-zinc-300 active:scale-95 transition-transform">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/></svg>
                  </button>
                  <span class="text-sm font-bold text-gray-800 dark:text-white w-8 text-center">{{ item.quantity }}</span>
                  <button @click="updateQuantity(item.id, item.quantity + 1)" class="w-8 h-8 rounded-lg bg-white dark:bg-zinc-700 border border-gray-200 dark:border-zinc-600 flex items-center justify-center text-gray-600 dark:text-zinc-300 active:scale-95 transition-transform">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                  </button>
                </div>
              </div>
            </div>
          </div>
          
          <!-- Totales y botones móvil -->
          <div class="px-5 py-5 border-t border-gray-100 dark:border-zinc-800 bg-gray-50/50 dark:bg-zinc-800/30 safe-area-bottom">
            <!-- Total -->
            <div class="flex items-center justify-between mb-5">
              <div>
                <p class="text-xs font-bold text-gray-400 dark:text-zinc-500 uppercase tracking-wider">Total a Pagar</p>
                <p class="text-3xl font-black text-gray-900 dark:text-white">${{ subtotal.toLocaleString() }}</p>
              </div>
              <!-- Botón limpiar carrito -->
              <button 
                v-if="cart.items.length > 0"
                @click="clearCart()"
                class="p-3 text-gray-400 dark:text-zinc-500 hover:text-rose-500 dark:hover:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-950/30 rounded-xl transition-colors"
                title="Vaciar carrito"
              >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
              </button>
            </div>
            
            <!-- Botones de acción -->
            <div class="grid grid-cols-2 gap-3 mb-3">
              <button 
                @click="openQuoteModal(); showMobileCart = false;"
                :disabled="cart.items.length === 0"
                class="px-4 py-3.5 bg-white dark:bg-zinc-800 border border-gray-200 dark:border-zinc-700 text-gray-700 dark:text-zinc-200 font-bold text-sm rounded-xl flex items-center justify-center gap-2 disabled:opacity-40 active:scale-95 transition-all"
              >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Cotizar
              </button>
              <button 
                @click="showMobilePaymentSelector = true; showMobileCart = false;"
                :disabled="cart.items.length === 0"
                class="px-4 py-3.5 bg-white dark:bg-zinc-800 border border-gray-200 dark:border-zinc-700 text-gray-700 dark:text-zinc-200 font-bold text-sm rounded-xl flex items-center justify-center gap-2 disabled:opacity-40 active:scale-95 transition-all"
              >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                Otros Pagos
              </button>
            </div>
            
            <!-- Botón principal pagar -->
            <button 
              @click="handleCobrarClick(); showMobileCart = false;"
              :disabled="cart.items.length === 0 || !hasOpenSession"
              class="w-full px-6 py-4 bg-emerald-600 hover:bg-emerald-700 disabled:bg-gray-300 dark:disabled:bg-zinc-700 text-white disabled:text-gray-500 dark:disabled:text-zinc-500 font-bold text-base rounded-xl shadow-lg shadow-emerald-600/30 disabled:shadow-none transition-all duration-300 flex items-center justify-center gap-3 active:scale-[0.98]"
            >
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
              </svg>
              Pagar en Efectivo
            </button>
          </div>
        </div>
      </Transition>
    </Teleport>
    
    <!-- MODAL SELECTOR DE MÉTODOS DE PAGO MÓVIL -->
    <Teleport to="body">
      <Transition
        enter-active-class="transition ease-out duration-300"
        enter-from-class="opacity-0"
        enter-to-class="opacity-100"
        leave-active-class="transition ease-in duration-200"
        leave-from-class="opacity-100"
        leave-to-class="opacity-0"
      >
        <div v-if="showMobilePaymentSelector" class="lg:hidden fixed inset-0 z-50 bg-black/70" @click="showMobilePaymentSelector = false"></div>
      </Transition>
      
      <Transition
        enter-active-class="transition ease-out duration-300"
        enter-from-class="translate-y-full"
        enter-to-class="translate-y-0"
        leave-active-class="transition ease-in duration-200"
        leave-from-class="translate-y-0"
        leave-to-class="translate-y-full"
      >
        <div v-if="showMobilePaymentSelector" class="lg:hidden fixed inset-x-0 bottom-0 z-50 bg-white dark:bg-zinc-900 rounded-t-3xl shadow-2xl max-h-[70vh] flex flex-col border-t border-gray-200 dark:border-zinc-700">
          <!-- Handle -->
          <div class="flex justify-center pt-3 pb-2">
            <div class="w-12 h-1.5 bg-gray-300 dark:bg-zinc-600 rounded-full"></div>
          </div>
          
          <!-- Header -->
          <div class="px-5 pb-4 flex items-center justify-between border-b border-gray-100 dark:border-zinc-800">
            <div>
              <h2 class="text-xl font-bold text-gray-900 dark:text-white">Método de Pago</h2>
              <p class="text-xs text-gray-500 dark:text-zinc-500 mt-0.5">Selecciona cómo deseas cobrar</p>
            </div>
            <button @click="showMobilePaymentSelector = false" class="p-2.5 hover:bg-gray-100 dark:hover:bg-zinc-800 rounded-xl transition-colors">
              <svg class="w-5 h-5 text-gray-500 dark:text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
              </svg>
            </button>
          </div>
          
          <!-- Métodos de Pago -->
          <div class="flex-1 overflow-y-auto px-5 py-5">
            <div v-if="paymentMethods.length > 0" class="grid grid-cols-2 gap-3">
              <button 
                v-for="method in paymentMethods" 
                :key="method.id"
                @click="handleMobilePaymentMethodSelect(method)"
                class="flex flex-col items-center justify-center gap-3 p-5 rounded-2xl border-2 transition-all duration-200 active:scale-95"
                :class="selectedPaymentMethod === method.id 
                  ? 'bg-slate-800 dark:bg-slate-700 border-slate-800 dark:border-slate-700 text-white shadow-lg' 
                  : 'bg-white dark:bg-zinc-800 border-gray-200 dark:border-zinc-700 text-gray-700 dark:text-zinc-300 hover:border-slate-400 dark:hover:border-slate-600'"
              >
                <div class="w-14 h-14 rounded-2xl flex items-center justify-center"
                     :class="selectedPaymentMethod === method.id 
                       ? 'bg-white/10' 
                       : 'bg-gray-50 dark:bg-zinc-700/50'">
                  <svg v-if="method.id === 'efectivo'" class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                  </svg>
                  <svg v-else class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                  </svg>
                </div>
                <div class="text-center">
                  <p class="text-sm font-bold">{{ method.name }}</p>
                  <p v-if="method.fee > 0" class="text-xs opacity-70 mt-1">+{{ method.fee_type === 'percent' ? method.fee + '%' : '$' + method.fee.toLocaleString() }}</p>
                </div>
              </button>
            </div>
            <p v-else class="text-center text-gray-500 dark:text-zinc-400 py-8">Cargando métodos de pago...</p>
          </div>
        </div>
      </Transition>
    </Teleport>
    
    <!-- MODAL INPUT EFECTIVO MÓVIL -->
    <Teleport to="body">
      <Transition
        enter-active-class="transition ease-out duration-300"
        enter-from-class="opacity-0"
        enter-to-class="opacity-100"
        leave-active-class="transition ease-in duration-200"
        leave-from-class="opacity-100"
        leave-to-class="opacity-0"
      >
        <div v-if="showMobileCashInput" class="lg:hidden fixed inset-0 z-50 bg-black/70" @click="showMobileCashInput = false"></div>
      </Transition>
      
      <Transition
        enter-active-class="transition ease-out duration-300"
        enter-from-class="translate-y-full"
        enter-to-class="translate-y-0"
        leave-active-class="transition ease-in duration-200"
        leave-from-class="translate-y-0"
        leave-to-class="translate-y-full"
      >
        <div v-if="showMobileCashInput" class="lg:hidden fixed inset-x-0 bottom-0 z-50 bg-white dark:bg-zinc-900 rounded-t-3xl shadow-2xl flex flex-col border-t border-gray-200 dark:border-zinc-700 pb-safe">
          <!-- Handle -->
          <div class="flex justify-center pt-3 pb-2">
            <div class="w-12 h-1.5 bg-gray-300 dark:bg-zinc-600 rounded-full"></div>
          </div>
          
          <!-- Header -->
          <div class="px-5 pb-4 border-b border-gray-100 dark:border-zinc-800">
            <h2 class="text-xl font-bold text-gray-900 dark:text-white">Pago en Efectivo</h2>
            <p class="text-xs text-gray-500 dark:text-zinc-500 mt-0.5">Total a pagar: ${{ total.toLocaleString() }}</p>
          </div>
          
          <!-- Input de efectivo -->
          <div class="px-5 py-5">
            <label class="block text-sm font-bold text-gray-700 dark:text-zinc-300 mb-2">¿Cuánto dinero recibe?</label>
            <div class="relative">
              <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 dark:text-zinc-500 font-bold text-2xl">$</span>
              <input
                ref="mobileCashInput"
                v-model.number="cashReceived"
                type="number"
                :min="total"
                step="1000"
                class="w-full pl-12 pr-4 py-4 text-3xl font-black rounded-2xl bg-gray-50 dark:bg-zinc-800 text-slate-900 dark:text-white placeholder-gray-300 dark:placeholder-zinc-600 border-2 border-gray-200 dark:border-zinc-700 focus:border-emerald-500 dark:focus:border-emerald-500 focus:ring-4 focus:ring-emerald-200 dark:focus:ring-emerald-900/30 transition-all"
                :placeholder="total.toString()"
                @focus="$event.target.select()"
              />
            </div>
            
            <!-- Cambio -->
            <div v-if="cashReceived >= total" class="mt-4 p-4 bg-emerald-50 dark:bg-emerald-950/30 rounded-xl border border-emerald-200 dark:border-emerald-800">
              <p class="text-xs font-bold text-emerald-700 dark:text-emerald-400 uppercase tracking-wide">Cambio</p>
              <p class="text-3xl font-black text-emerald-600 dark:text-emerald-400 mt-1">${{ (cashReceived - total).toLocaleString() }}</p>
            </div>
            
            <!-- Botones rápidos -->
            <div class="grid grid-cols-4 gap-2 mt-4">
              <button 
                v-for="amount in [5000, 10000, 20000, 50000]" 
                :key="amount"
                @click="cashReceived = total + amount"
                class="px-3 py-2.5 bg-gray-100 dark:bg-zinc-800 hover:bg-gray-200 dark:hover:bg-zinc-700 text-gray-700 dark:text-zinc-300 font-bold text-xs rounded-lg border border-gray-200 dark:border-zinc-700 transition-colors"
              >
                +${{ (amount / 1000).toFixed(0) }}k
              </button>
            </div>
          </div>
          
          <!-- Botones de acción -->
          <div class="px-5 pb-5 pt-3 border-t border-gray-100 dark:border-zinc-800 flex gap-3">
            <button 
              @click="showMobileCashInput = false; selectedPaymentMethod = null;"
              class="flex-1 px-4 py-3.5 bg-white dark:bg-zinc-800 border border-gray-200 dark:border-zinc-700 text-gray-700 dark:text-zinc-200 font-bold text-sm rounded-xl transition-all"
            >
              Cancelar
            </button>
            <button 
              @click="showMobileCashInput = false; showPaymentModal = true;"
              :disabled="!cashReceived || cashReceived < total"
              class="flex-1 px-4 py-3.5 bg-emerald-600 hover:bg-emerald-700 disabled:bg-gray-300 dark:disabled:bg-zinc-700 text-white disabled:text-gray-500 dark:disabled:text-zinc-500 font-bold text-sm rounded-xl shadow-lg disabled:shadow-none transition-all"
            >
              Continuar
            </button>
          </div>
        </div>
      </Transition>
    </Teleport>
    
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-2.5 px-4 lg:px-5 xl:px-6 py-3 pb-14 lg:pb-[56px]" style="flex: 1; min-height: 0; height: 100%; max-height: 100%; overflow: hidden;">
  <!-- Panel Izquierdo: Catálogo de Productos - Full en móvil, 66% Fashion/FastFood (8/12) | 58% General (7/12) en desktop -->
  <div :class="(isFashionStore || isFastFoodStore) ? 'lg:col-span-8' : 'lg:col-span-7 xl:col-span-6'" class="col-span-1 lg:col-span-auto overflow-hidden" style="height: 100%; max-height: 100%;">
    <div class="rounded-xl shadow-[0_1px_4px_rgba(0,0,0,0.08)] dark:shadow-2xl dark:shadow-black/50 border flex flex-col overflow-hidden" 
         :class="isFastFoodStore 
           ? 'bg-gradient-to-b from-[#FFFCF9] to-white dark:from-zinc-900 dark:to-zinc-900 border-amber-100/60 dark:border-zinc-800' 
           : 'bg-white dark:bg-zinc-900 border-gray-200 dark:border-zinc-700'"
         style="height: 100%; max-height: 100%;">
      
      <!-- Categorías integradas en panel de productos -->
      <div class="flex-shrink-0 px-4 pt-3 pb-2 overflow-x-auto scrollbar-hide border-b border-gray-100 dark:border-zinc-800/40">
        <div class="flex items-center gap-1.5 min-w-max">
            <button 
                @click="selectedCategory = null"
                class="px-4 py-1.5 rounded-lg text-sm transition-all duration-150 flex items-center gap-1.5"
                :class="!selectedCategory 
                    ? (isFastFoodStore 
                        ? 'bg-orange-50 dark:bg-orange-900/20 text-orange-600 dark:text-orange-400 border border-orange-200 dark:border-orange-800 font-semibold' 
                        : 'bg-slate-900 dark:bg-white text-white dark:text-zinc-900 font-semibold shadow-sm')
                    : (isFastFoodStore
                        ? 'text-gray-500 dark:text-zinc-400 hover:text-orange-600 dark:hover:text-orange-400 hover:bg-orange-50 dark:hover:bg-orange-900/10 font-medium'
                        : 'text-gray-500 dark:text-zinc-400 hover:bg-gray-100 dark:hover:bg-zinc-800 hover:text-gray-700 dark:hover:text-zinc-200 font-medium')"
            >
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                Todas
            </button>
            <button 
                v-for="cat in visibleCategories" 
                :key="cat.id"
                @click="selectedCategory = cat.id"
                class="px-4 py-1.5 rounded-lg text-sm transition-all duration-150 whitespace-nowrap"
                :class="selectedCategory === cat.id
                    ? (isFastFoodStore 
                        ? 'bg-orange-50 dark:bg-orange-900/20 text-orange-600 dark:text-orange-400 border border-orange-200 dark:border-orange-800 font-semibold' 
                        : 'bg-slate-900 dark:bg-white text-white dark:text-zinc-900 font-semibold shadow-sm')
                    : (isFastFoodStore
                        ? 'text-gray-500 dark:text-zinc-400 hover:text-orange-600 dark:hover:text-orange-400 hover:bg-orange-50 dark:hover:bg-orange-900/10 font-medium'
                        : 'text-gray-500 dark:text-zinc-400 hover:bg-gray-100 dark:hover:bg-zinc-800 hover:text-gray-700 dark:hover:text-zinc-200 font-medium')"
            >
                {{ capitalizeText(cat.name) }}
            </button>
            <button 
                v-if="hiddenCategoriesCount > 0"
                @click="showAllCategories = !showAllCategories"
                class="px-4 py-1.5 rounded-lg text-sm font-medium transition-all duration-150 whitespace-nowrap"
                :class="isFastFoodStore
                    ? 'text-gray-500 dark:text-zinc-400 hover:text-orange-600 dark:hover:text-orange-400 hover:bg-orange-50 dark:hover:bg-orange-900/10'
                    : 'text-gray-500 dark:text-zinc-400 hover:bg-gray-100 dark:hover:bg-zinc-800 hover:text-gray-700 dark:hover:text-zinc-200'"
            >
                <span v-if="!showAllCategories" class="flex items-center gap-1">
                    +{{ hiddenCategoriesCount }} más
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </span>
                <span v-else class="flex items-center gap-1">
                    Menos
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/></svg>
                </span>
            </button>
        </div>
      </div>

      <!-- RESTAURANTE - Header con estilo cálido y apetitoso -->
      <div v-if="isFastFoodStore" class="flex-shrink-0 bg-gradient-to-r from-white to-amber-50/50 dark:from-zinc-900 dark:to-zinc-900 border-b border-amber-100/50 dark:border-zinc-800">
        <div class="px-5 py-4 flex items-center justify-between">
          <div class="flex items-center gap-4">
            <!-- Icono de comida -->
            <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-orange-100 to-amber-100 dark:from-orange-900/30 dark:to-amber-900/20 flex items-center justify-center border border-orange-200/50 dark:border-orange-800/30 shadow-sm">
              <svg class="w-6 h-6 text-orange-500 dark:text-orange-400" viewBox="0 0 24 24" fill="currentColor">
                <path d="M8.1 13.34l2.83-2.83L3.91 3.5c-1.56 1.56-1.56 4.09 0 5.66l4.19 4.18zm6.78-1.81c1.53.71 3.68.21 5.27-1.38 1.91-1.91 2.28-4.65.81-6.12-1.46-1.46-4.20-1.10-6.12.81-1.59 1.59-2.09 3.74-1.38 5.27L3.7 19.87l1.41 1.41L12 14.41l6.88 6.88 1.41-1.41L13.41 13l1.47-1.47z"/>
              </svg>
            </div>
            <div>
              <h3 class="text-lg font-bold text-gray-900 dark:text-white">Menú</h3>
              <p class="text-sm text-orange-500/70 dark:text-orange-400/60 font-medium">{{ paginatedProducts.length }} productos disponibles</p>
            </div>
          </div>
          <!-- Toggle Vista Grid/Lista con estilo cálido -->
          <div class="flex items-center gap-1.5 bg-amber-50 dark:bg-zinc-800 rounded-xl p-1.5 border border-amber-100/50 dark:border-zinc-700">
            <button 
              @click="fashionViewMode = 'grid'"
              class="p-2 rounded-lg transition-all"
              :class="fashionViewMode === 'grid' ? 'bg-white dark:bg-zinc-700 shadow-sm text-orange-600 dark:text-orange-400' : 'text-gray-400 dark:text-zinc-500 hover:text-orange-500'"
            >
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
              </svg>
            </button>
            <button 
              @click="fashionViewMode = 'list'"
              class="p-2 rounded-lg transition-all"
              :class="fashionViewMode === 'list' ? 'bg-white dark:bg-zinc-700 shadow-sm text-orange-600 dark:text-orange-400' : 'text-gray-400 dark:text-zinc-500 hover:text-orange-500'"
            >
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
              </svg>
            </button>
          </div>
        </div>
      </div>
      
      <div class="p-4 overflow-y-auto" 
           :class="isFastFoodStore ? 'bg-gradient-to-b from-amber-50/30 to-white dark:from-zinc-800/20 dark:to-zinc-900/50' : 'bg-white dark:bg-zinc-800/40'"
           style="flex: 1 1 0; min-height: 0; scrollbar-width: thin; scrollbar-color: #cbd5e1 transparent;">
        
        <!-- Loading spinner elegante - SOLO si está cargando Y no hay productos previos Y pasó el delay -->
        <!-- Evita flash al cargar rápido, muestra loader limpio en vez de skeletons feos -->
      <div v-if="showLoadingSpinner" class="h-full flex flex-col items-center justify-center text-center py-12 min-h-[400px]">
        <div class="relative">
          <!-- Spinner animado -->
          <div class="w-12 h-12 border-4 rounded-full animate-spin"
               :class="isFastFoodStore ? 'border-orange-200 dark:border-zinc-700 border-t-orange-500 dark:border-t-orange-400' : 'border-slate-200 dark:border-zinc-700 border-t-slate-600 dark:border-t-zinc-400'"></div>
        </div>
        <p class="mt-4 text-sm" :class="isFastFoodStore ? 'text-orange-500/70 dark:text-orange-400/60' : 'text-gray-500 dark:text-zinc-400'">Cargando menú...</p>
      </div>
      
      <!-- Primera vez: NO hay productos en la base de datos - Icono Limpio y Profesional -->
      <div v-else-if="isFirstTimeNoProducts" class="h-full flex flex-col items-center justify-center text-center py-8 min-h-[400px]">
        
        <!-- Icono de Carrito Profesional -->
        <div class="w-20 h-20 mb-5 rounded-xl bg-gray-50 dark:from-zinc-800 dark:to-zinc-800/50 border border-gray-200 dark:border-zinc-700 flex items-center justify-center shadow-sm">
          <svg class="w-10 h-10 text-gray-300 dark:text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
          </svg>
        </div>
        
        <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">¿Listo para empezar a vender?</h3>
        <p class="text-sm text-gray-500 dark:text-zinc-400 max-w-[280px] leading-relaxed mb-1">
           Tu catálogo está vacío. Agrega productos para comenzar a realizar ventas.
        </p>
        <p class="text-xs text-gray-400 dark:text-zinc-500 mb-5">Solo toma unos minutos configurar tu inventario</p>
        
        <button
          @click="emit('change-module', 'products')"
          class="px-6 py-3 bg-slate-900 dark:bg-slate-700 hover:bg-black dark:hover:bg-slate-600 text-white text-sm font-bold rounded-xl shadow-lg shadow-slate-400/40 dark:shadow-slate-900/50 transition-all duration-300 transform active:scale-95 flex items-center gap-2"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
          </svg>
          <span>Ir a Productos</span>
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
      
      <!-- MODO FASHION - Diseño tipo Boutique/E-commerce Premium -->
      <template v-if="isFashionStore">
        <!-- Header de Colección Fashion - Minimalista -->
        <div class="mb-5 flex items-center justify-between">
          <div>
            <h2 class="text-lg font-medium text-gray-900 dark:text-white tracking-tight">Colección</h2>
            <p class="text-xs text-gray-400 dark:text-zinc-500 mt-0.5">{{ filteredProducts.length }} prendas</p>
          </div>
          <!-- Selector de vista - Más sutil -->
          <div class="flex items-center gap-1 bg-gray-50 dark:bg-zinc-800/50 p-0.5 rounded-lg">
            <button 
              @click="fashionViewMode = 'grid'"
              class="p-1.5 rounded transition-all"
              :class="fashionViewMode === 'grid' ? 'bg-white dark:bg-zinc-700 shadow-sm text-gray-900 dark:text-white' : 'text-gray-400 dark:text-zinc-500 hover:text-gray-600'"
            >
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
              </svg>
            </button>
            <button 
              @click="fashionViewMode = 'list'"
              class="p-1.5 rounded transition-all"
              :class="fashionViewMode === 'list' ? 'bg-white dark:bg-zinc-700 shadow-sm text-gray-900 dark:text-white' : 'text-gray-400 dark:text-zinc-500 hover:text-gray-600'"
            >
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 6h16M4 12h16M4 18h16"/>
              </svg>
            </button>
          </div>
        </div>

        <!-- GRID FASHION - Estilo Lookbook Premium -->
        <div v-if="fashionViewMode === 'grid'" class="grid grid-cols-3 md:grid-cols-4 xl:grid-cols-5 gap-4 pb-20 content-start">
          <div
            v-for="(product, index) in paginatedProducts"
            :key="product.id"
            class="group cursor-pointer"
            @click="addToCart(product)"
          >
            <!-- Card Fashion Premium - Hover con elevación -->
            <div class="relative overflow-hidden rounded-lg bg-white dark:bg-zinc-800/30 transition-all duration-300 hover:-translate-y-1 hover:shadow-xl dark:hover:shadow-black/50"
                 :class="!(product.image_url || product.image) ? 'border border-[#E5E7EB] dark:border-zinc-700/50' : 'border border-transparent'">
              
              
              <!-- Aspect ratio UNIFORME 3:4 para todas las imágenes -->
              <div class="aspect-[3/4] relative overflow-hidden">
                
                <!-- Badge cantidad en carrito -->
                <div v-if="getProductQuantityInCart(product.id) > 0" 
                     class="absolute top-2 right-2 z-20 w-5 h-5 rounded-full bg-gray-900 dark:bg-white text-white dark:text-zinc-900 text-[10px] font-semibold flex items-center justify-center">
                  {{ getProductQuantityInCart(product.id) }}
                </div>
                
                <!-- Imagen - object-cover para llenar uniforme -->
                <img
                  v-if="product.image_url || product.image"
                  :src="getProductImage(product)"
                  :alt="product.name"
                  class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105"
                  :loading="index < 12 ? 'eager' : 'lazy'"
                  @error="(e) => handleImageError(e, product)"
                />
                
                <!-- Placeholder Premium - Icono solid con más cuerpo -->
                <div v-else class="w-full h-full flex items-center justify-center bg-[#F3F4F6] dark:bg-zinc-800">
                  <svg class="w-10 h-10" viewBox="0 0 24 24" fill="none">
                    <path d="M5 9h14l1 12H4L5 9z" fill="#CBD5E1" class="dark:fill-zinc-700"/>
                    <path d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" stroke="#94A3B8" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" fill="none" class="dark:stroke-zinc-500"/>
                  </svg>
                </div>
              </div>
              
              <!-- Info del producto - Jerarquía tipográfica premium -->
              <div class="p-2.5 bg-white dark:bg-zinc-900/50 border-t border-gray-50 dark:border-zinc-800/30">
                <!-- Categoría - Muy sutil y pequeña -->
                <p class="text-[10px] font-semibold uppercase tracking-wider text-gray-400 dark:text-zinc-500 truncate mb-1">
                  {{ product.category_name || 'Moda' }}
                </p>
                
                <!-- Nombre -->
                <h3 class="text-sm font-medium text-gray-700 dark:text-zinc-300 leading-tight line-clamp-1 mb-1">
                  {{ product.name }}
                </h3>
                
                <!-- Precio -->
                <span class="text-[15px] font-semibold text-gray-900 dark:text-white">
                  ${{ product.price.toLocaleString() }}
                </span>
                <span v-if="getTotalStock(product) <= 3" class="ml-2 text-[9px] font-medium text-rose-500 dark:text-rose-400">
                  ¡Solo {{ getTotalStock(product) }}!
                </span>
              </div>
            </div>
          </div>
        </div>
        
        <!-- LISTA FASHION - Vista detallada -->
        <div v-else class="space-y-3 pb-20">
          <div
            v-for="product in paginatedProducts"
            :key="product.id"
            class="group flex gap-4 p-3 bg-white dark:bg-zinc-800/50 rounded-2xl border border-gray-200 dark:border-zinc-700/50 hover:border-gray-300 dark:hover:border-zinc-600 hover:shadow-md dark:hover:shadow-black/30 cursor-pointer transition-all duration-300"
            @click="addToCart(product)"
          >
            <!-- Imagen con borde sutil -->
            <div class="w-24 h-32 flex-shrink-0 rounded-xl overflow-hidden flex items-center justify-center border"
                 :class="(product.image_url || product.image) ? 'border-gray-100 dark:border-zinc-700' : 'bg-[#F3F4F6] dark:bg-zinc-800 border-[#E5E7EB] dark:border-zinc-700'">
              <img
                v-if="product.image_url || product.image"
                :src="getProductImage(product)"
                :alt="product.name"
                class="w-full h-full object-cover"
                loading="lazy"
              />
              <svg v-else class="w-8 h-8" viewBox="0 0 24 24" fill="none">
                <path d="M5 9h14l1 12H4L5 9z" fill="#CBD5E1" class="dark:fill-zinc-700"/>
                <path d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" stroke="#94A3B8" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" fill="none" class="dark:stroke-zinc-500"/>
              </svg>
            </div>
            
            <!-- Info con jerarquía tipográfica -->
            <div class="flex-1 flex flex-col justify-between py-1">
              <div>
                <p class="text-[10px] font-semibold uppercase tracking-wider text-gray-400 dark:text-zinc-500 mb-1">
                  {{ product.category_name || 'Sin categoría' }}
                </p>
                <h3 class="text-sm font-medium text-gray-700 dark:text-zinc-300 leading-snug">
                  {{ product.name }}
                </h3>
              </div>
              
              <div class="flex items-center justify-between">
                <span class="text-lg font-bold text-gray-900 dark:text-white">
                  ${{ product.price.toLocaleString() }}
                </span>
                <div class="flex items-center gap-2">
                  <span class="text-[11px] font-medium text-gray-400 dark:text-zinc-500">
                    {{ getTotalStock(product) }}
                  </span>
                  <!-- Badge si está en carrito -->
                  <span v-if="getProductQuantityInCart(product.id) > 0" class="px-2 py-1 bg-gray-900 dark:bg-white text-white dark:text-zinc-900 text-[10px] font-bold rounded-full">
                    {{ getProductQuantityInCart(product.id) }}
                  </span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </template>
      
      <!-- MODO RESTAURANTE / COMIDA RÁPIDA - Componente Externo -->
      <template v-else-if="isFastFoodStore">
        <PosFastFoodGrid 
          :products="paginatedProducts"
          :cart-items="cart.items"
          @add-to-cart="addToCartFastFood"
        />
      </template>
      
      <!-- GRID MODO GENERAL - Compacto y funcional (DISEÑO PREMIUM) -->
      <div v-else class="grid grid-cols-[repeat(auto-fill,minmax(120px,1fr))] xl:grid-cols-[repeat(auto-fill,minmax(150px,1fr))] gap-3 xl:gap-3.5 pb-20 min-h-[600px] content-start">
        <div
          v-for="product in paginatedProducts"
          :key="product.id"
          class="group bg-white dark:bg-[#1e1f20] rounded-xl overflow-hidden cursor-pointer border transition-all duration-300 hover:-translate-y-0.5 hover:shadow-lg dark:hover:shadow-black/50 shadow-[0_1px_3px_rgba(0,0,0,0.06)]"
          :class="(product.image_url || product.image) ? 'border-gray-200 dark:border-zinc-800/30 hover:border-gray-300 dark:hover:border-[#009F7A]/30' : 'border-gray-200 dark:border-zinc-700/60 hover:border-gray-300 dark:hover:border-[#009F7A]/30'"
          @click="addToCart(product)"
        >
          
          <!-- CONTENEDOR DE IMAGEN -->
          <div class="aspect-square relative flex items-center justify-center"
               :class="(product.image_url || product.image) ? 'bg-white dark:bg-[#282a2c]' : 'bg-[#F3F4F6] dark:bg-zinc-800'"
          >
             <!-- Badge de cantidad en carrito (gris oscuro profesional - menos ruido visual) -->
             <div v-if="getProductQuantityInCart(product.id) > 0" 
                  class="absolute top-1.5 right-1.5 z-10 w-5 h-5 rounded-full bg-gray-900 dark:bg-white text-white dark:text-zinc-900 text-[10px] font-medium flex items-center justify-center">
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
            <!-- Placeholder - Bolsa outline limpia sin relleno -->
            <div v-else class="w-full h-full flex items-center justify-center">
              <svg class="w-10 h-10" viewBox="0 0 24 24" fill="none">
                <path d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" stroke="#94A3B8" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="dark:stroke-zinc-500"/>
              </svg>
            </div>
          </div>

          <!-- INFORMACIÓN - Jerarquía tipográfica premium -->
          <div class="p-3 h-[80px] flex flex-col justify-between bg-white dark:bg-[#1e1f20] border-t border-gray-100 dark:border-zinc-800/50">
            
            <!-- Categoría pequeña y sutil -->
            <span class="text-[10px] font-semibold uppercase tracking-wider text-gray-400 dark:text-zinc-500 truncate">
              {{ product.category_name || 'General' }}
            </span>
            
            <!-- Nombre del producto -->
            <h3 class="text-sm font-medium text-gray-700 dark:text-zinc-300 leading-tight line-clamp-1" :title="product.name">
              {{ product.name }}
            </h3>
            
            <!-- PRECIO PROTAGONISTA + Stock -->
            <div class="flex items-center justify-between">
              <span class="text-[17px] font-bold text-gray-900 dark:text-white leading-none">
                ${{ product.price.toLocaleString() }}
              </span>
              <span class="text-[10px] font-medium" :class="getTotalStock(product) <= 5 ? 'text-rose-500 dark:text-rose-400' : 'text-gray-400 dark:text-zinc-500'">
                {{ getTotalStock(product) <= 5 ? '¡' + getTotalStock(product) + '!' : getTotalStock(product) }}
              </span>
            </div>
          </div>
        </div>
      </div>

      <!-- BOTÓN CARGAR MÁS PRODUCTOS -->
      <div v-if="remainingProductsCount > 0" class="flex justify-center pb-20 pt-4">
        <button 
          @click="loadMoreProducts"
          class="flex items-center gap-2 px-5 py-2.5 bg-white dark:bg-[#1e1f20] text-gray-700 dark:text-zinc-300 rounded-full text-[13px] font-semibold hover:bg-gray-50 dark:hover:bg-[#282a2c] transition-all duration-200 border border-gray-200 dark:border-zinc-700/60 shadow-sm hover:shadow"
        >
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
          </svg>
          Cargar más productos
          <span class="text-gray-400 dark:text-zinc-500">({{ remainingProductsCount }})</span>
        </button>
      </div>

    </div>
  </div>
</div>
    

<!-- bloque de ventas - 33% Fashion/FastFood (4/12) | Panel Central General (3/12) -->

<!-- Panel Derecho: Carrito/Ticket - OCULTO EN MÓVIL - 4 columnas (33%) -->
<div :class="(isFashionStore || isFastFoodStore) ? 'lg:col-span-4' : 'lg:col-span-3 xl:col-span-3'" class="hidden lg:block overflow-hidden" style="max-height: 100%;">
  
  <!-- CARRITO FAST FOOD - Componente Externo -->
  <PosFastFoodCart 
    v-if="isFastFoodStore"
    :cart-items="cart.items"
    :selected-customer="selectedCustomer"
    :discount="discount"
    :total="total"
    :show-promo-input="showPromoCodeInput"
    :promo-code="promoCode"
    :selected-payment-method="selectedPaymentMethod"
    :can-pay="canShowPaymentModal && !quotationMode"
    :credit-enabled="systemSettings.creditienda_enabled"
    @show-customer-selector="showCustomerSelector = true"
    @toggle-promo-input="showPromoCodeInput = !showPromoCodeInput"
    @update:promoCode="promoCode = $event"
    @apply-promo="applyPromoCode"
    @update-quantity="updateQuantity"
    @select-payment-method="selectedPaymentMethod = $event"
    @checkout="handleCobrarClick"
  />

  <!-- CARRITO FASHION + GENERAL -->
  <div v-else class="flex flex-col overflow-hidden bg-white dark:bg-[#141416] rounded-xl shadow-[0_1px_4px_rgba(0,0,0,0.08)] dark:shadow-2xl dark:shadow-black/50 border border-gray-200 dark:border-zinc-700 transition-all duration-300"
       style="height: 100%; max-height: 100%;">
    
    <!-- HEADER TICKET -->
    <div class="flex-shrink-0 px-5 pt-4 pb-3 border-b border-gray-100 dark:border-zinc-800/50">
      <!-- Header para Fashion y General -->
      <div class="flex items-center justify-between mb-3">
        <div>
          <h2 class="text-sm font-bold text-gray-600 dark:text-zinc-300 uppercase tracking-[0.14em] leading-none">
            {{ loadedQuotation ? 'Cotización' : quotationMode ? 'Nueva Cotización' : 'Ticket de Venta' }}
          </h2>
          <p v-if="loadedQuotation" class="text-sm font-bold text-gray-900 dark:text-white mt-1 leading-none">#{{ loadedQuotation.code }}</p>
        </div>
        <span v-if="loadedQuotation || quotationMode" class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-[0.12em] bg-amber-50 dark:bg-amber-950/40 text-amber-700 dark:text-amber-400">
          Editando
        </span>
      </div>

      <!-- CLIENTE + CUPÓN - Card visible y clicable -->
      <div class="flex items-stretch gap-2">
        <button
          id="tour-customer-btn-normal"
          @click="showCustomerSelector = true"
          class="flex-1 flex items-center gap-3 px-3 py-2.5 rounded-lg transition-all duration-200 group border"
          :class="selectedCustomer 
            ? 'bg-gray-900 dark:bg-white border-gray-900 dark:border-white' 
            : 'bg-gray-50 dark:bg-zinc-800 border-gray-200 dark:border-zinc-700 hover:bg-gray-100 dark:hover:bg-zinc-700 hover:border-gray-300'"
        >
          <div class="w-8 h-8 rounded-full flex items-center justify-center flex-shrink-0"
               :class="selectedCustomer ? 'bg-white/20 dark:bg-zinc-900/20' : 'bg-white dark:bg-zinc-700'">
            <svg class="w-4 h-4" :class="selectedCustomer ? 'text-white dark:text-zinc-900' : 'text-gray-500 dark:text-zinc-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
              <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
            </svg>
          </div>
          <div class="flex-1 min-w-0 text-left">
            <span class="text-[11px] font-bold block uppercase tracking-[0.12em] leading-none" :class="selectedCustomer ? 'text-white/75 dark:text-zinc-900/75' : 'text-gray-500 dark:text-zinc-400'">
              {{ selectedCustomer ? 'Cliente' : 'Asignar Cliente' }}
            </span>
            <span class="text-[15px] font-semibold block leading-tight truncate mt-1" :class="selectedCustomer ? 'text-white dark:text-zinc-900' : 'text-gray-800 dark:text-zinc-200'">
              {{ selectedCustomer ? selectedCustomer.name : 'Seleccionar...' }}
            </span>
          </div>
          <svg class="w-4 h-4 flex-shrink-0" :class="selectedCustomer ? 'text-white/50 dark:text-zinc-900/50' : 'text-gray-400 dark:text-zinc-600'" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/>
          </svg>
        </button>
        
        <button
          v-if="isFashionStore"
          @click="showPromoCodeInput = !showPromoCodeInput"
          class="group relative w-14 h-14 rounded-xl flex items-center justify-center transition-all duration-200"
          :class="showPromoCodeInput || discount > 0
            ? 'bg-rose-100 dark:bg-rose-950/40 text-rose-600 dark:text-rose-400'
            : 'bg-gray-100 dark:bg-zinc-800 text-gray-500 dark:text-zinc-400 hover:bg-gray-200 dark:hover:bg-zinc-700 hover:text-gray-700'"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9.568 3H5.25A2.25 2.25 0 003 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 005.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 009.568 3z" />
          </svg>
        </button>
      </div>

      <!-- Input Cupón Expandible (solo fashion, en general está en col 3) -->
      <div v-if="showPromoCodeInput && isFashionStore" class="mt-2 animate-fade-in">
        <div class="flex gap-2">
          <input
            v-model="promoCode"
            type="text"
            placeholder="Código descuento"
            class="flex-1 px-4 py-2 text-sm font-medium bg-gray-50 dark:bg-zinc-800 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-zinc-500 rounded-full border-0 focus:ring-2 focus:ring-gray-900 dark:focus:ring-white outline-none uppercase tracking-wide"
            @keyup.enter="applyPromoCode"
          />
          <button
            @click="applyPromoCode"
            :disabled="!promoCode.trim()"
            class="px-4 py-2 bg-gray-900 dark:bg-white text-white dark:text-zinc-900 text-xs font-medium rounded-full disabled:opacity-40 transition-all"
          >OK</button>
        </div>
      </div>
      
      <!-- Descuento Aplicado -->
      <div v-if="discount > 0" class="mt-3 flex items-center justify-between text-sm">
        <span class="text-gray-500 dark:text-zinc-400">Descuento</span>
        <span class="font-medium text-emerald-600 dark:text-emerald-400">-${{ discount.toLocaleString() }}</span>
      </div>
    </div>

    <!-- LISTA DE ITEMS -->
    <div class="flex-1 overflow-y-auto relative bg-white dark:bg-[#141416]" style="scrollbar-width: thin;">
      
      <!-- Carrito Vacío - Empty State Premium -->
      <div v-if="cart.items.length === 0" class="absolute inset-0 flex flex-col items-center justify-center text-center pointer-events-none px-8">
        <!-- Icono animado con pulso sutil -->
        <div class="relative mb-4">
          <div class="w-16 h-16 rounded-2xl bg-gray-50 dark:bg-zinc-800/50 flex items-center justify-center border border-gray-100 dark:border-zinc-700/50">
            <svg class="w-7 h-7 text-gray-300 dark:text-zinc-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
              <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993l1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 01-1.12-1.243l1.264-12A1.125 1.125 0 015.513 7.5h12.974c.576 0 1.059.435 1.119 1.007zM8.625 10.5a.375.375 0 11-.75 0 .375.375 0 01.75 0zm7.5 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
            </svg>
          </div>
          <!-- Indicador de estado -->
          <div class="absolute -top-1 -right-1 w-3.5 h-3.5 rounded-full bg-gray-200 dark:bg-zinc-700 border-2 border-white dark:border-[#141416]"></div>
        </div>
        <p class="text-base font-bold text-gray-600 dark:text-zinc-300 leading-tight">Tu ticket esta vacío</p>
        <p class="text-sm font-medium text-gray-400 dark:text-zinc-500 mt-2 max-w-[220px] leading-relaxed">Busca, escanea o selecciona un producto para comenzar</p>
        
        <!-- Indicador de flujo -->
        <div class="flex items-center gap-2 mt-5 opacity-60">
          <div class="flex items-center gap-1.5 text-[11px] font-semibold text-gray-400 dark:text-zinc-500">
            <div class="w-6 h-6 rounded-full border-2 border-gray-200 dark:border-zinc-700 flex items-center justify-center">
              <span class="text-[8px] font-bold">1</span>
            </div>
            <span>Producto</span>
          </div>
          <svg class="w-3 h-3 text-gray-200 dark:text-zinc-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
          <div class="flex items-center gap-1.5 text-[11px] font-semibold text-gray-400 dark:text-zinc-500">
            <div class="w-6 h-6 rounded-full border-2 border-gray-200 dark:border-zinc-700 flex items-center justify-center">
              <span class="text-[8px] font-bold">2</span>
            </div>
            <span>Pago</span>
          </div>
          <svg class="w-3 h-3 text-gray-200 dark:text-zinc-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
          <div class="flex items-center gap-1.5 text-[11px] font-semibold text-gray-400 dark:text-zinc-500">
            <div class="w-6 h-6 rounded-full border-2 border-gray-200 dark:border-zinc-700 flex items-center justify-center">
              <span class="text-[8px] font-bold">3</span>
            </div>
            <span>Cobrar</span>
          </div>
        </div>
      </div>

      <!-- LISTA DE ITEMS -->
      <div v-else class="px-4 py-3 space-y-1">
        <div
          v-for="item in cart.items"
          :key="item.id"
          class="group flex items-center gap-3 py-2.5 border-b last:border-0 border-gray-50 dark:border-zinc-800/30"
        >
          <!-- Imagen -->
          <div class="overflow-hidden flex-shrink-0 flex items-center justify-center w-11 h-11 rounded-lg border"
               :class="isRealProductImage(item.image_url) ? 'bg-white dark:bg-zinc-800 border-gray-100 dark:border-zinc-700' : 'bg-[#F3F4F6] dark:bg-zinc-800 border-[#E5E7EB] dark:border-zinc-700'">
            <img 
              v-if="isRealProductImage(item.image_url)"
              :src="getCartItemImage(item)" 
              :alt="item.name"
              class="w-full h-full object-cover rounded-lg"
            />
            <!-- Placeholder modo normal - Bolsa de compras -->
            <svg v-else class="w-5 h-5" viewBox="0 0 24 24" fill="none">
              <path d="M5 9h14l1 12H4L5 9z" fill="#CBD5E1" class="dark:fill-zinc-700"/>
              <path d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" stroke="#94A3B8" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" fill="none" class="dark:stroke-zinc-500"/>
            </svg>
          </div>

          <!-- Info del producto -->
          <div class="flex-1 min-w-0">
            <p class="font-semibold text-base text-gray-900 dark:text-zinc-100 truncate leading-snug">
              {{ item.name.split(' (')[0] }}
            </p>
            <p class="text-[13px] font-medium text-gray-500 dark:text-zinc-400 mt-1 leading-none">
              ${{ item.price.toLocaleString() }}
            </p>
          </div>

          <!-- Controles de cantidad -->
          <div class="flex items-center rounded-full border border-gray-200 dark:border-zinc-700">
            <button 
              @click.stop="updateQuantity(item.id, item.quantity - 1)" 
              class="w-7 h-7 flex items-center justify-center text-gray-400 dark:text-zinc-500 hover:text-gray-600 dark:hover:text-zinc-300 transition-all active:scale-90"
            >
              <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 12h-15"/></svg>
            </button>
            <span class="w-8 text-base text-center font-extrabold text-gray-900 dark:text-zinc-100 tabular-nums">
              {{ item.quantity }}
            </span>
            <button 
              @click.stop="updateQuantity(item.id, item.quantity + 1)" 
              class="w-7 h-7 flex items-center justify-center text-gray-400 dark:text-zinc-500 hover:text-gray-600 dark:hover:text-zinc-300 transition-all active:scale-90"
            >
              <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
            </button>
          </div>

          <!-- Precio -->
          <span class="text-base w-24 font-extrabold text-gray-900 dark:text-white tabular-nums text-right leading-none">
            ${{ (item.price * item.quantity).toLocaleString() }}
          </span>
        </div>
      </div>
    </div>
    
    <!-- SECCIÓN DE PAGO - Luxury Retail Minimal -->
    <div v-if="isFashionStore" class="flex-shrink-0 bg-white dark:bg-[#141416]">
      
      <div class="px-5 py-4">
        <!-- TOTAL ENORME - Estilo Apple/Zara -->
        <div class="text-center mb-5">
          <p class="text-[10px] font-medium text-gray-400 dark:text-zinc-500 uppercase tracking-[0.2em] mb-1">Total</p>
          <h2 class="text-5xl font-light text-gray-900 dark:text-white tracking-tight">${{ total.toLocaleString() }}</h2>
          <p class="text-xs text-gray-400 dark:text-zinc-500 mt-2 tabular-nums">
            {{ totalItems }} {{ totalItems === 1 ? 'artículo' : 'artículos' }} · IVA incl.
          </p>
        </div>
      
        <!-- MÉTODOS DE PAGO - Iconos Cuadrados Minimalistas -->
        <div class="flex gap-2 mb-4 justify-center flex-wrap">
          <button 
            v-for="method in paymentMethods" 
            :key="method.id"
            @click="selectedPaymentMethod = method.id"
            class="w-[72px] h-16 rounded-xl flex flex-col items-center justify-center gap-1.5 transition-all duration-200"
            :class="selectedPaymentMethod === method.id 
              ? 'bg-gray-900 dark:bg-white text-white dark:text-zinc-900 shadow-lg' 
              : 'bg-white dark:bg-zinc-800/50 text-gray-500 dark:text-zinc-400 border border-gray-200 dark:border-zinc-700 hover:border-gray-400 dark:hover:border-zinc-500'"
          >
            <svg v-if="method.id === 'efectivo'" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 00-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 01-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 003 15h-.75M15 10.5a3 3 0 11-6 0 3 3 0 016 0zm3 0h.008v.008H18V10.5zm-12 0h.008v.008H6V10.5z"/></svg>
            <svg v-else-if="method.id === 'tarjeta' || method.id === 'card'" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5z"/></svg>
            <svg v-else-if="method.id === 'credit' || method.is_credit" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M7.5 21L3 16.5m0 0L7.5 12M3 16.5h13.5m0-13.5L21 7.5m0 0L16.5 12M21 7.5H7.5"/></svg>
            <span class="text-[9px] font-medium leading-tight text-center">{{ method.name.split(' ')[0] }}</span>
          </button>
        </div>
        
        <!-- Puntos de lealtad (si aplica) -->
        <button
          v-if="canUseLoyaltyPoints"
          @click="usePoints = !usePoints"
          class="w-full py-2 mb-3 text-xs font-medium transition-all flex items-center justify-center gap-1.5 rounded-lg"
          :class="usePoints 
            ? 'bg-amber-50 dark:bg-amber-900/20 text-amber-600 dark:text-amber-400' 
            : 'text-gray-400 dark:text-zinc-500 hover:text-gray-600 dark:hover:text-zinc-400'"
        >
          <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
          {{ usePoints ? 'Usando puntos' : 'Usar puntos de lealtad' }}
        </button>
        
        <!-- Descuento aplicado -->
        <p v-if="discount > 0" class="text-xs text-emerald-600 dark:text-emerald-400 text-center mb-3 font-medium">
          Descuento: -${{ discount.toLocaleString() }}
        </p>
      
        <!-- BOTÓN PAGAR - Grande y Negro -->
        <button
          @click="handleCobrarClick"
          :disabled="!canShowPaymentModal || quotationMode"
          class="w-full py-4 rounded-xl font-medium text-base transition-all duration-200"
          :class="canShowPaymentModal && !quotationMode
            ? 'bg-gray-900 dark:bg-white hover:bg-black dark:hover:bg-gray-100 text-white dark:text-zinc-900 active:scale-[0.99] shadow-lg'
            : 'bg-gray-100 dark:bg-zinc-800 text-gray-400 dark:text-zinc-500 cursor-not-allowed'"
        >
          Completar Compra
        </button>
        
        <!-- Acciones secundarias - Solo texto, sin bordes -->
        <div class="flex gap-4 mt-4 justify-center">
          <button 
            @click="showConfirmClearCart" 
            :disabled="cart.items.length === 0"
            class="text-xs text-gray-400 dark:text-zinc-500 hover:text-gray-600 dark:hover:text-zinc-400 disabled:opacity-40 disabled:cursor-not-allowed transition-colors"
          >Cancelar</button>
          
          <button 
            @click="printQuote" 
            :disabled="!canCreateQuotation"
            class="text-xs text-gray-400 dark:text-zinc-500 hover:text-gray-600 dark:hover:text-zinc-400 disabled:opacity-40 disabled:cursor-not-allowed transition-colors"
          >Cotizar</button>
        </div>
      </div>
    </div>
    
  </div>
</div>

<!-- fin bloque de ventas -->  

<!-- Panel de Pagos - Solo visible en modo General (3/12) - Se oculta en Fashion y FastFood - OCULTO EN MÓVIL -->
<div v-if="!isFashionStore && !isFastFoodStore" id="tour-pos-cart" class="hidden lg:block lg:col-span-2 xl:col-span-3 h-full overflow-hidden">
  
  <div class="bg-white dark:bg-zinc-900 rounded-xl border border-gray-200 dark:border-zinc-700 h-full flex flex-col justify-between shadow-[0_1px_4px_rgba(0,0,0,0.08)] dark:shadow-2xl dark:shadow-black/50 transition-all duration-300"
       :class="cart.items.length > 0 ? 'border-l-emerald-300/50 dark:border-l-emerald-800/30' : ''">
    
    <!-- HEADER: Total Protagonista -->
    <div class="px-4 pt-4 pb-3 flex-shrink-0 border-b border-gray-100 dark:border-zinc-800/60">
      <p class="text-xs font-extrabold uppercase tracking-[0.16em] transition-colors duration-300" :class="cart.items.length > 0 ? 'text-gray-600 dark:text-zinc-300' : 'text-gray-400 dark:text-zinc-500'">Total a Pagar</p>
      <div class="flex items-baseline justify-between mt-1">
        <h2 class="text-[40px] font-black tracking-tight leading-none tabular-nums transition-colors duration-300" :class="cart.items.length > 0 ? 'text-gray-900 dark:text-white' : 'text-gray-300 dark:text-zinc-700'">${{ total.toLocaleString() }}</h2>
        <span class="text-[15px] font-bold tabular-nums transition-colors duration-300" :class="cart.items.length > 0 ? 'text-gray-500 dark:text-zinc-400' : 'text-gray-300 dark:text-zinc-600'">{{ totalItems }} <span class="text-[11px] font-semibold">items</span></span>
      </div>
      <p class="text-[13px] font-medium mt-1.5 leading-relaxed transition-colors duration-300" :class="cart.items.length > 0 ? 'text-gray-500 dark:text-zinc-400' : 'text-gray-300 dark:text-zinc-600'">
        Sub: ${{ subtotal.toLocaleString() }} 
        <span class="mx-0.5">·</span> 
            <span v-if="displayTaxRate !== null">{{ systemSettings.iva_display_name || 'IVA' }} ({{ displayTaxRate }}%): ${{ tax.toLocaleString() }}</span>
            <span v-else>{{ systemSettings.iva_display_name || 'IVA' }}: ${{ tax.toLocaleString() }}</span>
          </p>
        </div>
    
    <!-- ÁREA DE CONTENIDO CENTRAL -->
    <div class="flex-1 overflow-y-auto px-3 py-3 pb-32 space-y-3"
         style="scrollbar-width: thin; scrollbar-color: #cbd5e1 transparent;">

        <!-- CONFIGURACIÓN DE PAGO -->
        <div class="space-y-2" :class="{ 'opacity-40 pointer-events-none select-none': cart.items.length === 0 }">
        
        <!-- Descuento Aplicado (Solo si existe) -->
        <div v-if="discount > 0" class="bg-gradient-to-r from-emerald-50 to-green-50 dark:from-emerald-950/20 dark:to-green-950/20 rounded-lg px-2.5 py-1.5 border border-emerald-200 dark:border-emerald-800 flex items-center justify-between">
          <span class="text-[10px] font-bold text-emerald-700 dark:text-emerald-400 flex items-center gap-1">
            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path></svg>
            Descuento
          </span>
          <span class="text-xs font-black text-emerald-700 dark:text-emerald-400">-${{ discount.toLocaleString() }}</span>
        </div>

        <!-- MÉTODOS DE PAGO -->
        <div class="bg-gray-100 dark:bg-zinc-800/80 rounded-xl p-1.5 border border-gray-200/50 dark:border-zinc-700/40">
          <div v-if="paymentMethods.length > 0" class="flex gap-1.5">
             <button 
                v-for="method in paymentMethods" 
                :key="method.id"
                @click="selectedPaymentMethod = method.id"
                class="flex-1 px-2 py-2.5 rounded-lg text-[10px] font-bold uppercase transition-all flex flex-col items-center justify-center gap-1"
                :class="selectedPaymentMethod === method.id 
                  ? 'bg-slate-800 dark:bg-slate-700 text-white shadow-md' 
                  : 'bg-white dark:bg-zinc-700 text-gray-600 dark:text-zinc-300 hover:bg-gray-50 dark:hover:bg-zinc-600 shadow-sm border border-gray-100 dark:border-zinc-600/50'"
             >
                 <svg v-if="method.id === 'efectivo'" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                 <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                 <span class="leading-none tracking-wide">{{ method.name.replace(/\s*(crédito|débito|bancaria|de|\/)\s*/gi, ' ').trim() }}</span>
             </button>
          </div>
          <p v-else class="text-[10px] text-gray-500 italic p-2">Cargando métodos...</p>
        </div>

        <!-- PUNTOS Y CUPONES -->
        <div class="bg-white dark:bg-zinc-800/80 rounded-xl border border-gray-200/80 dark:border-zinc-700/60 p-2 shadow-sm">
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
            <p v-if="pointsDiscount > 0" class="text-[9px] text-emerald-600 dark:text-emerald-400 mt-1 font-bold">Descuento: -${{ pointsDiscount.toLocaleString() }}</p>
          </div>

          <div v-if="showPromoCodeInput" class="mt-1.5 animate-fade-in">
            <div class="flex gap-1.5">
              <input
                v-model="promoCode"
                type="text"
                placeholder="CÓDIGO PROMOCIONAL"
                class="flex-1 px-3 py-2 text-sm font-bold border rounded-lg uppercase bg-white dark:bg-zinc-900 border-gray-200 dark:border-zinc-700 text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-zinc-500 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none tracking-wide"
                @keyup.enter="applyPromoCode"
              />
              <button
                @click="applyPromoCode"
                :disabled="!promoCode.trim()"
                class="px-4 py-2 bg-slate-700 dark:bg-zinc-700 hover:bg-slate-800 disabled:bg-slate-300 dark:disabled:bg-zinc-700 disabled:cursor-not-allowed text-white text-[10px] font-bold rounded-lg transition-all"
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
            <p v-if="promoError" class="text-xs text-rose-600 dark:text-rose-400 mt-1.5 font-semibold">{{ promoError }}</p>
            <p v-if="discount > 0 && !promoError" class="text-xs text-emerald-600 dark:text-emerald-400 mt-1.5 font-semibold">Código aplicado: -${{ discount.toLocaleString() }}</p>
          </div>
        </div>

        <!-- INPUT DE EFECTIVO -->
        <div v-if="selectedPaymentMethod === 'efectivo'" class="animate-fade-in-up bg-white dark:bg-zinc-800/80 rounded-xl border border-gray-200/80 dark:border-zinc-700/60 p-3 shadow-sm">
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
                 <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2-2v14a2 2 0 002 2z"/></svg>
              </button>
           </div>
           
           <!-- BOTONES DE MONTO RÁPIDO -->
           <div class="grid grid-cols-4 gap-1.5 mb-2">
              <button 
                 v-for="amount in [2000, 5000, 10000, 20000, 50000, 100000]" 
                 :key="amount"
                 @click="cashReceived = amount"
                 class="h-8 text-[11px] font-bold rounded-md border transition-all active:scale-95"
                 :class="cashReceived === amount 
                   ? 'bg-slate-700 text-white border-slate-700 shadow-sm' 
                   : 'bg-white dark:bg-zinc-700 text-gray-600 dark:text-zinc-300 border-gray-200 dark:border-zinc-600 hover:bg-gray-50 dark:hover:bg-zinc-600'"
              >
                 ${{ (amount / 1000).toFixed(0) }}k
              </button>
              
              <!-- Botón EXACTO -->
              <button 
                 @click="cashReceived = total"
                 class="h-8 text-[11px] font-bold rounded-md border transition-all active:scale-95"
                 :class="cashReceived === total 
                   ? 'bg-slate-700 text-white border-slate-700 shadow-sm' 
                   : 'bg-gray-100 dark:bg-zinc-700 text-gray-700 dark:text-zinc-300 border-gray-200 dark:border-zinc-600 hover:bg-gray-200 dark:hover:bg-zinc-600'"
              >
                 Exacto
              </button>
              
              <!-- Botón BORRAR -->
              <button 
                 @click="cashReceived = 0"
                 class="h-8 text-[11px] font-bold rounded-md border border-gray-200 dark:border-zinc-600 bg-white dark:bg-zinc-700 text-gray-500 dark:text-zinc-400 hover:bg-gray-50 dark:hover:bg-zinc-600 hover:text-red-500 dark:hover:text-red-400 transition-all flex items-center justify-center gap-1 active:scale-95"
              >
                 <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2M3 12l6.414 6.414a2 2 0 001.414.586H19a2 2 0 002-2V7a2 2 0 00-2-2h-8.172a2 2 0 00-1.414.586L3 12z"/></svg>
                 <span>C</span>
              </button>
           </div>
           
           <!-- INDICADOR DE CAMBIO/FALTA -->
           <div v-if="cashReceived" class="rounded-lg px-2.5 py-1.5 transition-all duration-300"
                :class="cashReceived >= total ? 'bg-emerald-50 outline outline-1 outline-emerald-200' : 'bg-red-50 outline outline-1 outline-red-200'">
              <div class="flex items-center justify-between">
                 <span class="text-[10px] font-bold uppercase tracking-wider flex items-center gap-1"
                       :class="cashReceived >= total ? 'text-emerald-700' : 'text-red-700'">
                    <svg v-if="cashReceived < total" class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    {{ cashReceived >= total ? 'CAMBIO' : 'FALTA' }}
                 </span>
                 <span class="text-base font-black tabular-nums"
                       :class="cashReceived >= total ? 'text-emerald-600' : 'text-red-600'">
                    ${{ Math.abs(cashReceived - total).toLocaleString() }}
                 </span>
              </div>
           </div>
        </div>
        
        </div>
        <!-- FIN CONFIGURACIÓN DE PAGO -->

        <!-- EMPTY STATE HINT - Visible solo cuando no hay productos -->
        <div v-if="cart.items.length === 0" class="flex flex-col items-center justify-center text-center py-6 px-4">
          <div class="w-10 h-10 rounded-xl bg-gray-50 dark:bg-zinc-800/40 flex items-center justify-center mb-2.5 border border-dashed border-gray-200 dark:border-zinc-700/50">
            <svg class="w-5 h-5 text-gray-300 dark:text-zinc-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
              <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
          </div>
          <p class="text-sm font-bold text-gray-500 dark:text-zinc-400">Panel de cobro</p>
          <p class="text-xs font-medium text-gray-400 dark:text-zinc-500 mt-1.5 max-w-[190px] leading-relaxed">Los métodos de pago se habilitarán al agregar productos</p>
        </div>

    </div>

    <!-- FOOTER DE ACCIÓN -->
    <div class="px-3 py-3 bg-white dark:bg-zinc-900 border-t border-gray-100 dark:border-zinc-800/60 flex-shrink-0 space-y-2 mt-auto sticky bottom-0 z-10 shadow-[0_-8px_24px_rgba(0,0,0,0.04)] dark:shadow-[0_-8px_24px_rgba(0,0,0,0.25)]">
       
       <!-- Botones de Acción -->
       <div class="flex gap-1.5">
          <button @click="holdSale" :disabled="cart.items.length === 0" 
                  class="flex-1 h-10 text-sm font-semibold rounded-lg transition-all flex items-center justify-center gap-1.5 disabled:opacity-40"
                  :class="cart.items.length === 0 
                    ? 'text-gray-400 dark:text-zinc-500' 
                    : 'text-gray-500 dark:text-zinc-400 hover:bg-gray-50 dark:hover:bg-zinc-800 hover:text-gray-700 dark:hover:text-zinc-300'">
             <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6 18L18 6M6 6l12 12"/></svg>
             <span>Cancelar</span>
          </button>
          
          <button @click="printQuote" :disabled="!canCreateQuotation" 
                  class="flex-1 h-10 text-sm font-semibold rounded-lg transition-all flex items-center justify-center gap-1.5 disabled:opacity-40"
                  :class="!canCreateQuotation 
                    ? 'text-gray-400 dark:text-zinc-500' 
                    : 'text-gray-500 dark:text-zinc-400 hover:bg-gray-50 dark:hover:bg-zinc-800 hover:text-gray-700 dark:hover:text-zinc-300'">
             <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
             <span>Cotizar</span>
          </button>
       </div>

       <!-- BOTÓN COBRAR - Prominente y limpio -->
       <button
          @click="handleCobrarClick"
          :disabled="!canShowPaymentModal || quotationMode || (selectedPaymentMethod === 'efectivo' && total > 0 && (!cashReceived || cashReceived < total))"
          class="w-full h-[64px] rounded-xl font-bold text-base flex items-center justify-center gap-3 group relative overflow-hidden transition-all duration-200"
          :class="[
            (!canShowPaymentModal || quotationMode)
              ? 'bg-gray-100 dark:bg-zinc-800 text-gray-400 dark:text-zinc-500 cursor-not-allowed border border-gray-200 dark:border-zinc-700'
            : (selectedPaymentMethod === 'efectivo' && total > 0 && (!cashReceived || cashReceived < total))
              ? 'bg-gray-100 dark:bg-zinc-700 text-gray-500 dark:text-zinc-400 cursor-not-allowed border border-gray-200 dark:border-zinc-700'
              : 'bg-emerald-600 dark:bg-emerald-600 text-white hover:bg-emerald-700 dark:hover:bg-emerald-500 shadow-lg shadow-emerald-500/25 dark:shadow-emerald-900/40'
          ]"
        >
          <!-- Efecto de brillo animado cuando está listo -->
          <div 
            v-if="canShowPaymentModal && !quotationMode && !(selectedPaymentMethod === 'efectivo' && total > 0 && (!cashReceived || cashReceived < total))" 
            class="absolute inset-0 bg-gradient-to-r from-transparent via-white/20 to-transparent -translate-x-full animate-shimmer"
          ></div>

           <span v-if="cart.items.length === 0" class="text-base font-bold tracking-[0.02em]">
             Agrega productos
          </span>

           <span v-else-if="!selectedPaymentMethod" class="text-base font-bold uppercase tracking-[0.08em]">
             Selecciona método de pago
          </span>

          <span v-else-if="selectedPaymentMethod === 'efectivo' && (!cashReceived || cashReceived < total)" class="flex items-center gap-2">
             <svg class="w-5 h-5 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
             <span class="text-base font-bold uppercase tracking-[0.08em]">Ingresa efectivo</span>
          </span>

          <span v-else class="flex items-center justify-between w-full px-4">
             <span class="flex items-center gap-2">
               <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
              <span class="text-base font-extrabold">Cobrar Ahora</span>
             </span>
             <span class="flex items-center gap-2 text-xl font-extrabold tabular-nums leading-none">
               ${{ total.toLocaleString() }}
               <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
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

    <!-- Modal de Cantidad - Diseño Limpio y Profesional -->
    <Teleport to="body">
      <Transition
        enter-active-class="transition ease-out duration-200"
        enter-from-class="opacity-0"
        enter-to-class="opacity-100"
        leave-active-class="transition ease-in duration-150"
        leave-from-class="opacity-100"
        leave-to-class="opacity-0"
      >
        <div v-if="showQuantityModal" class="fixed inset-0 bg-black/50 backdrop-blur-[2px] flex items-center justify-center z-[100] p-4" @click.self="showQuantityModal = false">
          <Transition
            enter-active-class="transition ease-out duration-200"
            enter-from-class="opacity-0 scale-[0.97]"
            enter-to-class="opacity-100 scale-100"
            leave-active-class="transition ease-in duration-150"
            leave-from-class="opacity-100 scale-100"
            leave-to-class="opacity-0 scale-[0.97]"
          >
            <div v-if="showQuantityModal" class="bg-white dark:bg-zinc-900 rounded-2xl shadow-2xl dark:shadow-black/60 max-w-[640px] w-full overflow-hidden border border-gray-200 dark:border-zinc-800">
              
              <!-- Header -->
              <div class="px-6 py-4 border-b border-gray-100 dark:border-zinc-800 flex items-center justify-between">
                <div class="min-w-0">
                  <h3 class="text-base font-bold text-gray-900 dark:text-white truncate">{{ selectedProductForQuantity?.name }}</h3>
                  <p class="text-sm text-gray-500 dark:text-zinc-500 mt-0.5">{{ formatCurrency(selectedProductForQuantity?.price || 0) }} / {{ getUnitText(selectedProductForQuantity?.measurement_unit) }}</p>
                </div>
                <button @click="showQuantityModal = false" 
                        class="p-2 hover:bg-gray-100 dark:hover:bg-zinc-800 rounded-lg transition-colors duration-150 ml-4">
                  <svg class="w-5 h-5 text-gray-400 dark:text-zinc-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                  </svg>
                </button>
              </div>

              <!-- Body -->
              <div class="px-6 py-5">
                <!-- Fila principal: Input + Unidad + Quick amounts -->
                <div class="flex items-start gap-5">

                  <!-- Input con unidad pegada -->
                  <div class="flex-1 min-w-0">
                    <div class="flex items-stretch gap-0">
                      <input 
                        ref="quantityModalInputRef"
                        v-model="customQuantity" 
                        type="number" 
                        step="0.01"
                        min="0.01"
                        inputmode="decimal"
                        @keyup.enter="addToCartWithQuantity"
                        class="flex-1 min-w-0 text-center text-[34px] font-bold py-4 px-4 border-2 border-r-0 border-gray-200 dark:border-zinc-700 bg-gray-50/50 dark:bg-zinc-800/50 text-gray-900 dark:text-white rounded-l-xl focus:ring-2 focus:ring-blue-500/40 dark:focus:ring-blue-400/30 focus:border-blue-500 dark:focus:border-blue-400 focus:z-10 relative placeholder-gray-300 dark:placeholder-zinc-700 transition-all duration-200 [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none"
                        placeholder="0">
                      <!-- Selector unidad pegado al input -->
                      <div v-if="['kg', 'g', 'l', 'ml'].includes(selectedProductForQuantity?.measurement_unit)" 
                           class="flex flex-col bg-gray-100 dark:bg-zinc-800 rounded-r-xl border-2 border-l-0 border-gray-200 dark:border-zinc-700 p-1 gap-1">
                        <button 
                          @click="useAlternativeUnit = false"
                          :class="[
                            'px-4 py-2.5 rounded-lg text-xs font-bold transition-all duration-150 flex-1',
                            !useAlternativeUnit 
                              ? 'bg-white dark:bg-zinc-700 text-gray-900 dark:text-white shadow-sm' 
                              : 'text-gray-400 dark:text-zinc-500 hover:text-gray-600 dark:hover:text-zinc-300'
                          ]">
                          {{ getUnitText(selectedProductForQuantity?.measurement_unit).toUpperCase() }}
                        </button>
                        <button 
                          @click="useAlternativeUnit = true"
                          :class="[
                            'px-4 py-2.5 rounded-lg text-xs font-bold transition-all duration-150 flex-1',
                            useAlternativeUnit 
                              ? 'bg-white dark:bg-zinc-700 text-gray-900 dark:text-white shadow-sm' 
                              : 'text-gray-400 dark:text-zinc-500 hover:text-gray-600 dark:hover:text-zinc-300'
                          ]">
                          {{ getUnitText(getAlternativeUnit(selectedProductForQuantity?.measurement_unit)).toUpperCase() }}
                        </button>
                      </div>
                      <div v-else class="flex items-center px-5 bg-gray-100 dark:bg-zinc-800 rounded-r-xl border-2 border-l-0 border-gray-200 dark:border-zinc-700">
                        <span class="text-sm font-bold text-gray-500 dark:text-zinc-400">{{ getUnitText(selectedProductForQuantity?.measurement_unit).toUpperCase() }}</span>
                      </div>
                    </div>
                  </div>

                  <!-- Separador visual -->
                  <div class="w-px self-stretch bg-gray-200 dark:bg-zinc-800"></div>

                  <!-- Quick amounts en grid 2x2 -->
                  <div class="flex-shrink-0">
                    <div class="grid grid-cols-2 gap-2 w-[180px]">
                      <button 
                        v-for="quick in quantityModalQuickAmounts" 
                        :key="quick"
                        @click="customQuantity = quick"
                        :class="[
                          'py-3 rounded-xl text-sm font-bold transition-all duration-100 border select-none tabular-nums',
                          customQuantity == quick 
                            ? 'bg-slate-800 dark:bg-slate-600 border-slate-800 dark:border-slate-600 text-white' 
                            : 'bg-white dark:bg-zinc-800 hover:bg-gray-50 dark:hover:bg-zinc-750 text-gray-600 dark:text-zinc-300 border-gray-200 dark:border-zinc-700 active:scale-95'
                        ]">
                        {{ quick }} {{ getUnitText(getInputUnit(selectedProductForQuantity?.measurement_unit)) }}
                      </button>
                    </div>
                  </div>
                </div>

                <!-- Barra inferior: Stock + Subtotal + Botón -->
                <div class="flex items-center gap-3 mt-5 pt-4 border-t border-gray-100 dark:border-zinc-800/60">
                  <!-- Stock badge -->
                  <div :class="[
                    'flex items-center gap-1.5 px-3 py-2 rounded-lg text-xs font-semibold border',
                    quantityModalStockLevel === 'critical' 
                      ? 'bg-red-50 dark:bg-red-950/20 border-red-100 dark:border-red-900/30 text-red-600 dark:text-red-400' 
                      : quantityModalStockLevel === 'low' 
                        ? 'bg-amber-50 dark:bg-amber-950/20 border-amber-100 dark:border-amber-900/30 text-amber-600 dark:text-amber-400' 
                        : 'bg-emerald-50 dark:bg-emerald-950/20 border-emerald-100 dark:border-emerald-900/30 text-emerald-600 dark:text-emerald-400'
                  ]">
                    Stock: {{ getTotalStock(selectedProductForQuantity) }} {{ getUnitText(selectedProductForQuantity?.measurement_unit) }}
                  </div>
                  <!-- Subtotal -->
                  <div v-if="quantityModalSubtotal > 0" 
                       class="px-3 py-2 rounded-lg text-xs font-semibold border bg-blue-50 dark:bg-blue-950/20 border-blue-100 dark:border-blue-900/30 text-blue-600 dark:text-blue-400">
                    Subtotal: {{ formatCurrency(quantityModalSubtotal) }}
                  </div>
                  <!-- Spacer -->
                  <div class="flex-1"></div>
                  <!-- Cancelar -->
                  <button @click="showQuantityModal = false" 
                          class="px-5 py-2.5 text-sm font-medium text-gray-500 dark:text-zinc-500 hover:text-gray-700 dark:hover:text-zinc-300 hover:bg-gray-100 dark:hover:bg-zinc-800 rounded-xl transition-all duration-150">
                    Cancelar
                  </button>
                  <!-- Agregar -->
                  <button @click="addToCartWithQuantity" 
                          :disabled="!customQuantity || parseFloat(customQuantity) <= 0"
                          class="px-6 py-2.5 bg-slate-900 dark:bg-slate-700 hover:bg-black dark:hover:bg-slate-600 disabled:bg-gray-200 dark:disabled:bg-zinc-800 disabled:text-gray-400 dark:disabled:text-zinc-600 text-white text-sm font-bold rounded-xl shadow-sm disabled:shadow-none transition-all duration-150 active:scale-[0.98]">
                    Agregar al carrito
                  </button>
                </div>
              </div>
              
            </div>
          </Transition>
        </div>
      </Transition>
    </Teleport>

    <!-- Modal de Historial de Cliente -->
    <CustomerHistoryModal
      v-if="showCustomerHistory"
      :customer="selectedCustomerForHistory"
      @add-to-cart="handleAddToCartFromHistory"
      @close="showCustomerHistory = false"
    />

    <!-- Modal de Selección de Variantes (Fashion) -->
    <POSVariantSelector
      :show="showVariantSelector"
      :product="selectedProductForVariants"
      @close="showVariantSelector = false"
      @confirm="handleVariantConfirmed"
    />

    <!-- Modal de Confirmación de Pago (Unificado con Post-Pago) -->
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
      :backendProcessing="backendProcessingPayment"
      :backendSuccess="backendPaymentSuccess"
      @payment-confirmed="handlePaymentConfirmed"
      @print-invoice="handlePrintInvoice"
      @send-whatsapp="handleSendWhatsApp"
      @download-invoice="handleDownloadInvoice"
      @send-email="handleSendEmail"
      @view-invoice="handleViewInvoice"
      @new-sale="startNewSale"
      @close="handleClosePaymentModal"
    />

    <!-- Modal Post-Pago -->
    <AfterPaymentModal
      v-if="showAfterPaymentModal"
      :saleTotal="lastSale?.total || 0"
      :sale="lastSale"
      :userPlan="appStore.tenantPlan || 'free_trial'"
      @print-invoice="handlePrintInvoice"
      @send-whatsapp="handleSendWhatsApp"
      @send-email="handleSendEmail"
      @view-invoice="handleViewInvoice"
      @new-sale="startNewSale"
      @close="showAfterPaymentModal = false"
    />

    <!-- Modal de Recibo -->
    <ReceiptModal
      v-if="showReceiptModal"
      :sale="lastSale"
      :userPlan="appStore.tenantPlan || 'free_trial'"
      @close="showReceiptModal = false"
      @new-sale="startNewSale"
      @send-whatsapp="handleSendWhatsApp"
      @send-email="handleSendEmail"
    />

    <!-- Modal de Email Profesional -->
    <Teleport to="body">
      <div v-if="showEmailModal" class="fixed inset-0 bg-black/70 flex items-center justify-center z-[9999] p-4">
        <div class="bg-white dark:bg-zinc-900 rounded-2xl shadow-2xl max-w-md w-full border border-gray-300 dark:border-zinc-800">
          <!-- Header con icono -->
          <div class="p-6 pb-4">
            <div class="text-center">
              <div class="mx-auto flex items-center justify-center h-14 w-14 rounded-xl bg-blue-50 dark:bg-blue-950/50 mb-4 border border-blue-100 dark:border-blue-900/50">
                <svg class="h-7 w-7 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
              </div>
              <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Email del Cliente</h3>
              <p class="text-sm text-gray-600 dark:text-zinc-400 leading-relaxed">
                El cliente no tiene un email registrado. Por favor ingrese el email donde desea enviar la factura.
              </p>
            </div>
          </div>

          <!-- Body -->
          <div class="px-6 pb-6">
            <div class="mb-4">
              <label class="block text-sm font-medium text-gray-700 dark:text-zinc-300 mb-2">
                Email
              </label>
              <input
                v-model="emailInput"
                type="email"
                placeholder="cliente@ejemplo.com"
                class="w-full px-4 py-3 border border-gray-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-gray-900 dark:text-zinc-200 placeholder-gray-400 dark:placeholder-zinc-500 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                @keyup.enter="confirmEmail"
                autofocus
              >
            </div>
          </div>

          <!-- Footer -->
          <div class="px-6 pb-6 flex gap-3">
            <button 
              @click="cancelEmail"
              class="flex-1 px-4 py-2.5 bg-white dark:bg-zinc-800 hover:bg-gray-50 dark:hover:bg-zinc-700 text-gray-700 dark:text-zinc-200 rounded-xl font-semibold border border-gray-200 dark:border-zinc-700 transition-all duration-200"
            >
              Cancelar
            </button>
            <button 
              @click="confirmEmail"
              :disabled="!emailInput"
              class="flex-1 px-4 py-2.5 bg-blue-600 dark:bg-blue-700 hover:bg-blue-700 dark:hover:bg-blue-800 text-white rounded-xl font-bold shadow-lg shadow-blue-400/40 dark:shadow-blue-900/50 transition-all duration-300 flex items-center justify-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed"
            >
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
              </svg>
              Enviar
            </button>
          </div>
        </div>
      </div>
    </Teleport>

    <!-- Modal Premium -->
    <Teleport to="body">
      <div v-if="showPremiumModalPOS" class="fixed inset-0 bg-black/85 flex items-center justify-center z-[9999] p-4">
        <div class="bg-white dark:bg-zinc-900 rounded-2xl shadow-2xl max-w-md w-full border border-gray-200 dark:border-zinc-800">
          
          <!-- Contenido -->
          <div class="p-8 text-center">
            <!-- Icono Premium -->
            <div class="w-20 h-20 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-full flex items-center justify-center mx-auto mb-6 shadow-lg">
              <svg class="w-10 h-10 text-white" fill="currentColor" viewBox="0 0 24 24">
                <path d="M12 2L15.09 8.26L22 9.27L17 14.14L18.18 21.02L12 17.77L5.82 21.02L7 14.14L2 9.27L8.91 8.26L12 2Z"/>
              </svg>
            </div>

            <!-- Título -->
            <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-3">¡Mejora tu Plan!</h3>
            
            <!-- Mensaje -->
            <p class="text-base text-gray-600 dark:text-zinc-400 mb-6 leading-relaxed">
              <span class="font-semibold text-blue-600 dark:text-blue-400">{{ premiumFeatureNamePOS }}</span> está disponible en nuestros planes premium.
            </p>
            
            <p class="text-sm text-gray-500 dark:text-zinc-500 mb-8">
              Desbloquea esta y muchas más funciones contactando con soporte.
            </p>

            <!-- Botón -->
            <button
              @click="closePremiumModalPOS"
              class="w-full py-3.5 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white text-base font-semibold rounded-xl transition-all shadow-lg hover:shadow-xl"
            >
              Entendido
            </button>
          </div>
        </div>
      </div>
    </Teleport>

    <!-- Modal: Se requiere cliente para vender -->
    <Teleport to="body">
      <Transition name="modal">
        <div v-if="showRequireCustomerModal" class="fixed inset-0 bg-black/70 flex items-center justify-center z-[9999] p-4">
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
                  No se puede usar "Cliente Final"
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

    <!-- Modal: Límite de Facturas del Plan Gratuito Alcanzado -->
    <Teleport to="body">
      <div v-if="showFreePlanLimitModal" class="fixed inset-0 bg-black/85 flex items-center justify-center z-[60] p-4">
        <div class="bg-white dark:bg-zinc-900 rounded-2xl shadow-2xl max-w-md w-full border border-gray-200 dark:border-zinc-800">
          
          <!-- Contenido -->
          <div class="p-8 text-center">
            <!-- Icono -->
            <div class="w-20 h-20 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-full flex items-center justify-center mx-auto mb-6 shadow-lg">
              <svg class="w-10 h-10 text-white" fill="currentColor" viewBox="0 0 24 24">
                <path d="M12 2L15.09 8.26L22 9.27L17 14.14L18.18 21.02L12 17.77L5.82 21.02L7 14.14L2 9.27L8.91 8.26L12 2Z"/>
              </svg>
            </div>

            <!-- Título -->
            <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-3">¡Límite de Prueba Alcanzado!</h3>
            
            <!-- Mensaje -->
            <p class="text-base text-gray-600 dark:text-zinc-400 mb-6 leading-relaxed">
              Has completado <span class="font-semibold text-blue-600 dark:text-blue-400">{{ freePlanLimitData.maxInvoices }} facturas</span> en tu periodo de prueba gratuita.
            </p>
            
            <p class="text-sm text-gray-500 dark:text-zinc-500 mb-8">
              Actualiza tu plan para seguir vendiendo sin límites
            </p>

            <!-- Botones -->
            <div class="flex gap-3">
              <button
                @click="showFreePlanLimitModal = false"
                class="flex-1 py-3 bg-white dark:bg-zinc-800 text-gray-700 dark:text-zinc-300 text-base font-semibold rounded-xl border border-gray-300 dark:border-zinc-700 hover:bg-gray-50 dark:hover:bg-zinc-700 transition-colors"
              >
                Cerrar
              </button>
              <button
                @click="goToSelectPlan"
                class="flex-1 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white text-base font-semibold rounded-xl transition-colors duration-200 shadow-lg"
              >
                Ver Planes
              </button>
            </div>
          </div>
        </div>
      </div>
    </Teleport>

    <!-- Modal de Confirmación de Cierre de Venta -->
    <Teleport to="body">
      <Transition name="modal-fade">
        <div v-if="showCloseTabModal" class="fixed inset-0 bg-black/70 flex items-center justify-center z-[9999] p-4">
          <div class="bg-white dark:bg-zinc-900 rounded-2xl shadow-2xl max-w-md w-full border border-gray-300 dark:border-zinc-800">
            <!-- Header con icono -->
            <div class="p-6 pb-4">
              <div class="text-center">
                <div class="mx-auto flex items-center justify-center h-14 w-14 rounded-xl bg-amber-50 dark:bg-amber-950/50 mb-4 border border-amber-100 dark:border-amber-900/50">
                  <svg class="h-7 w-7 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                  </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">¿Cerrar "{{ closeTabData.tabName }}"?</h3>
                <p class="text-sm text-gray-600 dark:text-zinc-400 leading-relaxed">
                  Esta acción no se puede deshacer. Todos los productos en el carrito se perderán.
                </p>
              </div>
            </div>

            <!-- Body -->
            <div class="px-6 pb-6">
              <div class="bg-amber-50 dark:bg-amber-950/30 border border-amber-200 dark:border-amber-900/50 rounded-xl p-4 mb-4">
                <p class="text-sm text-amber-800 dark:text-amber-400 text-center font-medium">
                  Los productos en el carrito se eliminarán permanentemente
                </p>
              </div>
            </div>

            <!-- Footer -->
            <div class="px-6 pb-6 flex gap-3">
              <button 
                @click="cancelCloseTab"
                class="flex-1 px-4 py-2.5 bg-white dark:bg-zinc-800 hover:bg-gray-50 dark:hover:bg-zinc-700 text-gray-700 dark:text-zinc-200 rounded-xl font-semibold border border-gray-200 dark:border-zinc-700 transition-all duration-200"
              >
                Cancelar
              </button>
              <button 
                @click="confirmCloseTab"
                class="flex-1 px-4 py-2.5 bg-red-600 dark:bg-red-700 hover:bg-red-700 dark:hover:bg-red-800 text-white rounded-xl font-bold shadow-lg shadow-red-400/40 dark:shadow-red-900/50 transition-all duration-300 flex items-center justify-center gap-2"
              >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
                Cerrar Venta
              </button>
            </div>
          </div>
        </div>
      </Transition>
    </Teleport>

    <!-- Modal de Confirmación de Cotización -->
    <div v-if="showQuotationConfirmModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white dark:bg-zinc-800 rounded-xl p-6 max-w-lg w-full mx-4 shadow-2xl">
        <div class="text-center">
          <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-blue-100 dark:bg-blue-900 mb-4">
            <svg class="h-6 w-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
          </div>
          <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">Confirmar Cotización</h3>
          <p class="text-sm text-gray-500 dark:text-zinc-400 mb-6">
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
          <div class="bg-gray-50 dark:bg-zinc-700 rounded-lg p-4 mb-6 text-left">
            <div class="flex justify-between items-center mb-2">
              <span class="text-sm font-medium text-gray-700 dark:text-zinc-300">Cliente:</span>
              <span class="text-sm text-gray-900 dark:text-white">{{ selectedCustomer?.name || 'Cliente Final' }}</span>
            </div>
            <div class="flex justify-between items-center mb-2">
              <span class="text-sm font-medium text-gray-700 dark:text-zinc-300">Productos:</span>
              <span class="text-sm text-gray-900 dark:text-white">{{ cart.items.length }} artículo(s)</span>
            </div>
            <div class="flex justify-between items-center">
              <span class="text-sm font-medium text-gray-700 dark:text-zinc-300">Total:</span>
              <span class="text-sm font-semibold text-gray-900 dark:text-white">${{ total.toFixed(2) }}</span>
            </div>
          </div>
          
          <div class="flex space-x-3">
            <button
              @click="showQuotationConfirmModal = false"
              class="flex-1 bg-gray-300 dark:bg-zinc-600 text-gray-700 dark:text-zinc-200 px-4 py-2 rounded-lg font-medium hover:bg-gray-400 dark:hover:bg-gray-500 transition-colors"
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
      :user-plan="appStore.tenantPlan || 'free_trial'"
      @close="handleCloseQuotationModal"
      @load="handleLoadQuotation"
      @print="handlePrintQuotation"
      @send-whatsapp="handleSendQuotationWhatsApp"
      @send-email="handleSendQuotationEmail"
      @load-available="handleLoadAvailableProducts"
      @add-partial-stock="handleAddPartialStock"
    />

    <!-- ===== MODAL: Cierre Forzado — Flat Enterprise ===== -->
    <Teleport to="body">
      <Transition
        enter-active-class="transition-all duration-200 ease-out"
        enter-from-class="opacity-0"
        enter-to-class="opacity-100"
        leave-active-class="transition-all duration-150 ease-in"
        leave-from-class="opacity-100"
        leave-to-class="opacity-0"
      >
        <div v-if="showForcedCloseModal" class="fixed inset-0 z-[100] flex items-center justify-center p-4">
          <!-- Backdrop — NO dismissible -->
          <div class="absolute inset-0 bg-black/50 dark:bg-black/70"></div>

          <div class="relative bg-white dark:bg-zinc-900 w-full max-w-5xl border border-gray-200 dark:border-zinc-800 shadow-2xl dark:shadow-black/60 overflow-hidden rounded-lg">

            <!-- ══ HEADER CORPORATIVO ══ -->
            <div class="px-8 py-5 border-b border-gray-200 dark:border-zinc-800 flex items-center justify-between">
              <div>
                <h2 class="text-xl font-black text-gray-900 dark:text-white uppercase tracking-tight">Finalizar Jornada y Arqueo de Caja</h2>
                <p class="text-xs text-gray-500 dark:text-zinc-500 font-medium mt-1">Sesión pendiente de cierre · Resolución obligatoria antes de operar</p>
              </div>
              <span class="text-[11px] font-bold text-orange-700 dark:text-orange-400 uppercase tracking-widest bg-orange-50 dark:bg-orange-950/50 border border-orange-200 dark:border-orange-800/50 px-3 py-1.5 rounded">Pendiente</span>
            </div>

            <!-- ══ BODY: DOS PANELES ══ -->
            <div class="flex flex-col lg:flex-row">

              <!-- ▸ PANEL IZQUIERDO (42%): Contexto y Resumen -->
              <div class="lg:w-[42%] bg-gray-50 dark:bg-zinc-950/50 border-b lg:border-b-0 lg:border-r border-gray-200 dark:border-zinc-800 p-7 space-y-6">

                <!-- Info del operario -->
                <div class="space-y-3">
                  <p class="text-[11px] font-bold text-gray-400 dark:text-zinc-600 uppercase tracking-widest">Datos de la Sesión</p>
                  <div class="space-y-2.5">
                    <div class="flex justify-between items-center">
                      <span class="text-sm text-gray-500 dark:text-zinc-500">Operario</span>
                      <span class="text-sm font-bold text-gray-900 dark:text-white">{{ forcedCloseSession?.user_name || currentUser?.name || 'Usuario' }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                      <span class="text-sm text-gray-500 dark:text-zinc-500">Ubicación</span>
                      <span class="text-sm font-semibold text-gray-700 dark:text-zinc-300">{{ forcedCloseSession?.warehouse_name || 'Sin sede' }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                      <span class="text-sm text-gray-500 dark:text-zinc-500">Apertura</span>
                      <span class="text-sm font-semibold text-gray-700 dark:text-zinc-300">{{ forcedCloseRealOpenDate }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                      <span class="text-sm text-gray-500 dark:text-zinc-500">Cierre sistema</span>
                      <span class="text-sm font-semibold text-gray-700 dark:text-zinc-300">{{ forcedCloseSession?.closed_at ? new Date(forcedCloseSession.closed_at).toLocaleString('es-CO', { dateStyle: 'short', timeStyle: 'short' }) : '—' }}</span>
                    </div>
                  </div>
                </div>

                <!-- Resumen operativo en cuadrícula -->
                <div class="space-y-2.5">
                  <p class="text-[11px] font-bold text-gray-400 dark:text-zinc-600 uppercase tracking-widest">Resumen Operativo</p>
                  <div class="border border-gray-200 dark:border-zinc-800 rounded-md bg-white dark:bg-zinc-900 divide-y divide-gray-200 dark:divide-zinc-800">
                    <div class="grid grid-cols-2 divide-x divide-gray-200 dark:divide-zinc-800">
                      <div class="p-3.5">
                        <p class="text-[11px] text-gray-400 dark:text-zinc-500 uppercase tracking-wider font-medium">Total Ventas</p>
                        <p class="text-2xl font-black text-gray-900 dark:text-white tabular-nums mt-1">{{ formatCurrency(forcedCloseSession?.total_sales || 0) }}</p>
                      </div>
                      <div class="p-3.5">
                        <p class="text-[11px] text-gray-400 dark:text-zinc-500 uppercase tracking-wider font-medium">Ventas Efectivo</p>
                        <p class="text-2xl font-black text-gray-900 dark:text-white tabular-nums mt-1">{{ formatCurrency(forcedCloseSession?.cash_sales || 0) }}</p>
                      </div>
                    </div>
                    <div class="grid grid-cols-2 divide-x divide-gray-200 dark:divide-zinc-800">
                      <div class="p-3.5">
                        <p class="text-[11px] text-gray-400 dark:text-zinc-500 uppercase tracking-wider font-medium">Monto Inicial</p>
                        <p class="text-2xl font-black text-gray-900 dark:text-white tabular-nums mt-1">{{ formatCurrency(forcedCloseSession?.opening_amount || 0) }}</p>
                      </div>
                      <div class="p-3.5">
                        <p class="text-[11px] text-gray-400 dark:text-zinc-500 uppercase tracking-wider font-medium">Efectivo Esperado</p>
                        <p class="text-2xl font-black text-gray-900 dark:text-white tabular-nums mt-1">{{ formatCurrency(forcedCloseSession?.expected_amount || 0) }}</p>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Nota del sistema -->
                <div class="space-y-2">
                  <p class="text-[11px] font-bold text-gray-400 dark:text-zinc-600 uppercase tracking-widest">Observaciones del Sistema</p>
                  <div class="bg-gray-100 dark:bg-zinc-800/80 border border-gray-200 dark:border-zinc-700 rounded-md p-3.5">
                    <p class="text-xs text-gray-600 dark:text-zinc-400 leading-relaxed">El sistema cerró esta sesión de caja automáticamente porque permaneció abierta al finalizar el día operativo. Para poder iniciar ventas hoy, debe completar el arqueo de esta sesión.</p>
                  </div>
                </div>
              </div>

              <!-- ▸ PANEL DERECHO (58%): Acción y Arqueo -->
              <div class="lg:w-[58%] bg-white dark:bg-zinc-900 p-7 space-y-5">

                <!-- Ingreso de monto -->
                <div class="space-y-2">
                  <label class="block text-[11px] font-bold text-gray-500 dark:text-zinc-500 uppercase tracking-widest">
                    Ingrese monto real contado en caja
                  </label>
                  <p class="text-xs text-gray-400 dark:text-zinc-600">Ingrese el monto exacto del efectivo físico</p>
                  <input
                    type="number"
                    v-model="forcedCloseAuditAmount"
                    min="0"
                    step="100"
                    placeholder="0"
                    class="w-full py-3.5 px-4 text-2xl font-semibold tabular-nums
                      border-2 border-gray-300 dark:border-zinc-600 rounded-md
                      bg-white dark:bg-zinc-800
                      text-gray-900 dark:text-white
                      placeholder-gray-300 dark:placeholder-zinc-600
                      focus:outline-none focus:ring-2 focus:ring-gray-900 dark:focus:ring-zinc-400 focus:border-transparent"
                    @keydown.enter="submitForcedCloseAudit"
                  />
                </div>

                <!-- Diferencia calculada -->
                <div class="space-y-2">
                  <p class="text-[11px] font-bold text-gray-500 dark:text-zinc-500 uppercase tracking-widest">Diferencia Calculada <span class="font-normal normal-case text-gray-400 dark:text-zinc-600">(Esperado vs Contado)</span></p>
                  <div v-if="forcedCloseDifference !== null" class="flex items-baseline gap-3">
                    <span class="text-3xl font-black tabular-nums"
                      :class="forcedCloseDifference < -0.01
                        ? 'text-red-700 dark:text-red-400'
                        : forcedCloseDifference > 0.01
                          ? 'text-gray-900 dark:text-white'
                          : 'text-gray-900 dark:text-white'">
                      {{ forcedCloseDifference > 0 ? '+' : '' }}{{ formatCurrency(forcedCloseDifference) }}
                    </span>
                    <span class="text-sm font-bold uppercase tracking-wide"
                      :class="forcedCloseDifference < -0.01
                        ? 'text-red-600 dark:text-red-400'
                        : forcedCloseDifference > 0.01
                          ? 'text-orange-600 dark:text-orange-400'
                          : 'text-gray-500 dark:text-zinc-500'">
                      {{ forcedCloseDifference < -0.01 ? 'Faltante' : forcedCloseDifference > 0.01 ? 'Sobrante' : 'Cuadre exacto' }}
                    </span>
                  </div>
                  <div v-else class="text-base text-gray-300 dark:text-zinc-700 font-medium">—</div>

                  <!-- Alerta si diferencia significativa -->
                  <div v-if="forcedCloseDifference !== null && Math.abs(forcedCloseDifference) > 1000" class="flex items-start gap-2 mt-1">
                    <svg class="w-4 h-4 text-orange-600 dark:text-orange-400 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                      <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                    <p class="text-xs text-orange-700 dark:text-orange-400 font-medium">Diferencia significativa detectada. Verifique nuevamente el conteo de efectivo antes de continuar.</p>
                  </div>
                </div>

                <!-- Notas del cajero -->
                <div class="space-y-2">
                  <label class="block text-[11px] font-bold text-gray-500 dark:text-zinc-500 uppercase tracking-widest">Observaciones del Cajero <span class="font-normal normal-case text-gray-400 dark:text-zinc-600">(opcional)</span></label>
                  <input
                    type="text"
                    v-model="forcedCloseNotes"
                    name="cash_close_obs_forced"
                    placeholder="Ej: Faltó cambio de $5.000"
                    autocomplete="one-time-code"
                    data-form-type="other"
                    class="w-full px-4 py-3 text-sm border border-gray-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-gray-900 dark:text-zinc-200 placeholder-gray-400 dark:placeholder-zinc-600 rounded-md focus:ring-2 focus:ring-gray-900 dark:focus:ring-zinc-400 focus:border-transparent"
                  />
                </div>

                <!-- ═══ AUTORIZACIÓN CONDICIONAL ═══ -->

                <!-- Admin: paso directo -->
                <div v-if="isAdminUser" class="flex items-center gap-2 py-2">
                  <div class="w-1.5 h-1.5 rounded-full bg-emerald-500"></div>
                  <span class="text-sm font-semibold text-gray-600 dark:text-zinc-400">Cierre rápido habilitado · Modo Administrador</span>
                </div>

                <!-- Vendedor: requiere firma con contraseña -->
                <div v-else class="space-y-2">
                  <p class="text-[11px] font-bold text-gray-500 dark:text-zinc-500 uppercase tracking-widest">Autorización de Cierre</p>
                  <div class="relative">
                    <div class="absolute left-3 top-1/2 -translate-y-1/2">
                      <svg class="w-4 h-4 text-gray-400 dark:text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/>
                      </svg>
                    </div>
                    <input
                      :type="showForcedClosePassword ? 'text' : 'password'"
                      v-model="forcedClosePassword"
                      name="cash_close_auth_forced"
                      placeholder="Ingresa tu contraseña para firmar"
                      autocomplete="new-password"
                      data-form-type="other"
                      class="w-full pl-10 pr-10 py-3 text-sm border border-gray-300 dark:border-zinc-600 bg-white dark:bg-zinc-800 text-gray-900 dark:text-zinc-200 placeholder-gray-400 dark:placeholder-zinc-600 rounded-md focus:ring-2 focus:ring-gray-900 dark:focus:ring-zinc-400 focus:border-transparent"
                      @keydown.enter="submitForcedCloseAudit"
                    />
                    <button
                      type="button"
                      @click="showForcedClosePassword = !showForcedClosePassword"
                      class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 dark:text-zinc-500 hover:text-gray-600 dark:hover:text-zinc-300 transition-colors"
                    >
                      <svg v-if="!showForcedClosePassword" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                      </svg>
                      <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88"/>
                      </svg>
                    </button>
                  </div>
                  <p v-if="forcedClosePasswordError" class="text-xs text-red-700 dark:text-red-400 font-medium">{{ forcedClosePasswordError }}</p>
                </div>

                <!-- Error general -->
                <p v-if="forcedCloseError" class="text-sm text-red-700 dark:text-red-400 font-semibold">{{ forcedCloseError }}</p>

                <!-- ═══ BOTONES ═══ -->
                <div class="flex items-center justify-end gap-3 pt-3 border-t border-gray-100 dark:border-zinc-800">
                  <button
                    @click="submitForcedCloseAudit"
                    :disabled="forcedCloseSubmitting || !forcedCloseAuditAmount || (!isAdminUser && !forcedClosePassword)"
                    class="px-7 py-3 text-sm font-semibold uppercase tracking-wide rounded-md transition-all duration-150
                      bg-gray-900 dark:bg-zinc-200 text-white dark:text-zinc-900
                      hover:bg-black dark:hover:bg-white
                      disabled:opacity-40 disabled:cursor-not-allowed">
                    <span v-if="forcedCloseSubmitting" class="inline-flex items-center gap-2">
                      <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                      Verificando...
                    </span>
                    <span v-else>Confirmar Datos y Finalizar Arqueo</span>
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </Transition>
    </Teleport>

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
      :is-admin-user="isAdminUser"
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
      <div class="bg-white dark:bg-zinc-800 rounded-xl shadow-2xl max-w-md w-full">
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
            <p class="text-sm text-gray-600 dark:text-zinc-400">
              Escaneando... Muestre el código QR de la cotización
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
    <div class="bg-white dark:bg-zinc-900 rounded-3xl shadow-2xl max-w-md w-full transform transition-all duration-300 scale-100 border border-red-200 dark:border-red-800">
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
    :preload-invoice-number="preloadInvoiceNumber"
    @close="showReturnsModal = false; preloadInvoiceNumber = ''"
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

  <!-- BARRA DE MULTI-TABS INFERIOR (Footer Fijo) - SOLO DESKTOP -->
  <div id="tour-pos-multisales" class="hidden lg:block fixed bottom-0 left-0 right-0 bg-white dark:bg-[#121214] border-t border-gray-300/70 dark:border-zinc-700/80 shadow-[0_-2px_8px_rgba(0,0,0,0.06)] dark:shadow-[0_-2px_12px_rgba(0,0,0,0.35)] z-50 h-[48px]">
    <div class="flex items-center h-full px-2 max-w-screen-2xl mx-auto">
      
      <!-- Pestañas de ventas -->
      <div class="flex items-center gap-0.5 flex-1 overflow-x-auto scrollbar-hide">
        <div 
          v-for="tab in salesTabs" 
          :key="tab.id"
          @click="switchTab(tab.id)"
          class="group relative flex items-center gap-2 px-4 py-2.5 cursor-pointer transition-all duration-150 min-w-[130px] max-w-[190px] rounded-t-lg"
          :class="[
            activeTabId === tab.id 
              ? 'bg-gray-50 dark:bg-zinc-800/80' 
              : 'bg-transparent hover:bg-gray-50/80 dark:hover:bg-zinc-800/40'
          ]"
          :style="{
            borderTop: activeTabId === tab.id ? (isFastFoodStore ? '2px solid #F97316' : '2px solid #10B981') : '2px solid transparent',
          }"
        >
          <!-- Indicador de items en carrito -->
          <div 
            v-if="tab.cart && tab.cart.length > 0" 
            class="w-1.5 h-1.5 rounded-full flex-shrink-0"
            :class="activeTabId === tab.id ? (isFastFoodStore ? 'bg-orange-500' : 'bg-green-500') : 'bg-gray-400'"
            :title="`${tab.cart.length} producto(s)`"
          ></div>
          
          <!-- Nombre de la pestaña -->
          <span 
            class="text-sm font-semibold truncate flex-1"
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
          :class="salesTabs.length >= 5 ? 'opacity-30 cursor-not-allowed bg-gray-100 dark:bg-zinc-800' : (isFastFoodStore ? 'hover:bg-orange-50 dark:hover:bg-orange-900/20 text-gray-600 dark:text-zinc-400 hover:text-orange-600 dark:hover:text-orange-500' : 'hover:bg-gray-100 dark:hover:bg-zinc-800 text-gray-600 dark:text-zinc-400 hover:text-green-600 dark:hover:text-green-500')"
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

  <!-- MODAL: Registrar Abono desde POS -->
  <Teleport to="body">
    <div v-if="showAbonoModal" class="fixed inset-0 z-50 flex items-center justify-center p-4">
      <div class="absolute inset-0 bg-black/60 dark:bg-black/80" @click="closeAbonoModal"></div>
      <div class="relative bg-white dark:bg-zinc-900 rounded-2xl shadow-2xl w-full max-w-3xl border border-gray-200 dark:border-zinc-800">
          
          <!-- Header -->
          <div class="flex items-center justify-between px-7 py-5 border-b border-gray-100 dark:border-zinc-800">
            <div class="flex items-center gap-3">
              <div class="w-10 h-10 rounded-xl bg-emerald-50 dark:bg-emerald-900/30 flex items-center justify-center border border-emerald-100 dark:border-emerald-800/50">
                <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
              </div>
              <div>
                <h3 class="text-lg font-bold text-gray-900 dark:text-white">Registrar Abono</h3>
                <p class="text-xs text-gray-400 dark:text-zinc-500">Pagos a créditos activos</p>
              </div>
            </div>
            <button @click="closeAbonoModal" class="p-2 text-gray-400 hover:text-gray-600 dark:hover:text-zinc-200 hover:bg-gray-100 dark:hover:bg-zinc-800 rounded-lg transition-colors">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
          </div>

          <div class="p-7">
            <!-- PASO 1: Buscar cliente por CC / Documento -->
            <div v-if="!abonoFoundCustomer" class="mb-6">
              <label class="block text-xs font-semibold text-gray-500 dark:text-zinc-400 uppercase tracking-wider mb-2">
                Documento del cliente (CC / NIT)
              </label>
              <div class="flex gap-3">
                <div class="relative flex-1">
                  <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400 dark:text-zinc-500 z-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                  </svg>
                  <input v-model="abonoDocSearch" type="text"
                    @keydown.enter.prevent="searchAbonoCustomer"
                    @focus="showAbonoSuggestions = true"
                    @keydown.escape="showAbonoSuggestions = false"
                    :disabled="abonoSearching"
                    autocomplete="off"
                    class="w-full pl-11 pr-4 py-3 border-2 border-gray-200 dark:border-zinc-700 bg-gray-50 dark:bg-zinc-800 text-gray-900 dark:text-zinc-200 rounded-xl text-base focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors placeholder-gray-400 dark:placeholder-zinc-500"
                    placeholder="Ej: 1234567890"/>
                  
                  <!-- Dropdown de sugerencias -->
                  <div v-if="showAbonoSuggestions && abonoSuggestions.length > 0" 
                    class="absolute left-0 right-0 top-full mt-1 bg-white dark:bg-zinc-800 rounded-xl border border-gray-200 dark:border-zinc-700 shadow-xl dark:shadow-black/50 z-30 overflow-hidden max-h-[240px] overflow-y-auto">
                    <button v-for="s in abonoSuggestions" :key="s.id" type="button"
                      @mousedown.prevent="selectAbonoSuggestion(s)"
                      class="w-full flex items-center gap-3 px-4 py-3 hover:bg-gray-50 dark:hover:bg-zinc-700/60 transition-colors text-left border-b border-gray-100 dark:border-zinc-700/40 last:border-0">
                      <div class="w-9 h-9 rounded-full bg-slate-100 dark:bg-zinc-700 flex items-center justify-center flex-shrink-0">
                        <span class="text-sm font-bold text-slate-500 dark:text-zinc-400">{{ s.name?.charAt(0)?.toUpperCase() }}</span>
                      </div>
                      <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-gray-900 dark:text-zinc-200 truncate">{{ s.name }}</p>
                        <p class="text-xs text-gray-400 dark:text-zinc-500">CC: {{ s.document_number }}</p>
                      </div>
                      <div class="text-right flex-shrink-0">
                        <p class="text-sm font-bold text-amber-600 dark:text-amber-400">{{ formatCurrency(s.current_debt) }}</p>
                        <p class="text-[10px] text-gray-400 dark:text-zinc-500 uppercase">Deuda</p>
                      </div>
                    </button>
                  </div>
                </div>
                <button type="button" @click="searchAbonoCustomer"
                  :disabled="abonoSearching || !abonoDocSearch.trim()"
                  class="px-6 py-3 bg-slate-900 dark:bg-slate-700 hover:bg-black dark:hover:bg-slate-600 text-white text-sm font-bold rounded-xl shadow-lg shadow-slate-400/30 dark:shadow-slate-900/50 transition-all duration-200 disabled:opacity-40 disabled:cursor-not-allowed flex items-center gap-2 flex-shrink-0">
                  <svg v-if="abonoSearching" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                  </svg>
                  {{ abonoSearching ? 'Buscando...' : 'Buscar' }}
                </button>
              </div>
              <!-- Error de búsqueda -->
              <p v-if="abonoSearchError" class="mt-2 text-sm text-red-500 dark:text-red-400 flex items-center gap-1.5">
                <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                {{ abonoSearchError }}
              </p>
            </div>

            <!-- PASO 2: Tarjeta cliente encontrado -->
            <div v-if="abonoFoundCustomer" class="bg-gray-50 dark:bg-zinc-800/60 rounded-xl border border-gray-200 dark:border-zinc-700/60 p-5 mb-6">
              <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-3">
                  <div class="w-11 h-11 rounded-full bg-slate-200 dark:bg-zinc-700 flex items-center justify-center">
                    <span class="text-base font-bold text-slate-600 dark:text-zinc-300">{{ abonoFoundCustomer.name?.charAt(0)?.toUpperCase() }}</span>
                  </div>
                  <div>
                    <p class="text-base font-bold text-gray-900 dark:text-white">{{ abonoFoundCustomer.name }}</p>
                    <p class="text-sm text-gray-500 dark:text-zinc-400">CC: {{ abonoFoundCustomer.document_number }}</p>
                  </div>
                </div>
                <button type="button" @click="clearAbonoSearch" class="p-1.5 text-gray-400 hover:text-red-500 dark:hover:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors" title="Cambiar cliente">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
              </div>
              <div class="grid grid-cols-3 gap-4">
                <div class="bg-white dark:bg-zinc-900/60 rounded-lg px-4 py-3 border border-gray-100 dark:border-zinc-700/40">
                  <p class="text-[11px] font-medium text-gray-400 dark:text-zinc-500 uppercase tracking-wider">Deuda Total</p>
                  <p class="text-xl font-bold text-amber-600 dark:text-amber-400 mt-0.5">{{ formatCurrency(abonoFoundCustomer.current_debt) }}</p>
                </div>
                <div class="bg-white dark:bg-zinc-900/60 rounded-lg px-4 py-3 border border-gray-100 dark:border-zinc-700/40">
                  <p class="text-[11px] font-medium text-gray-400 dark:text-zinc-500 uppercase tracking-wider">Límite Crédito</p>
                  <p class="text-xl font-bold text-gray-700 dark:text-zinc-300 mt-0.5">{{ formatCurrency(abonoFoundCustomer.credit_limit || 0) }}</p>
                </div>
                <div class="bg-white dark:bg-zinc-900/60 rounded-lg px-4 py-3 border border-gray-100 dark:border-zinc-700/40">
                  <p class="text-[11px] font-medium text-gray-400 dark:text-zinc-500 uppercase tracking-wider">Disponible</p>
                  <p class="text-xl font-bold text-emerald-600 dark:text-emerald-400 mt-0.5">{{ formatCurrency(Math.max(0, parseFloat(abonoFoundCustomer.credit_limit || 0) - parseFloat(abonoFoundCustomer.current_debt || 0))) }}</p>
                </div>
              </div>
            </div>

            <!-- PASO 3: Formulario de pago (solo si hay cliente) -->
            <form v-if="abonoFoundCustomer" @submit.prevent="submitAbono">
              <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                <!-- Monto -->
                <div>
                  <label class="block text-xs font-semibold text-gray-500 dark:text-zinc-400 uppercase tracking-wider mb-2">
                    Monto a Abonar
                  </label>
                  <div class="relative">
                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 dark:text-zinc-500 text-base font-semibold">$</span>
                    <input v-model.number="abonoForm.amount" type="number" step="100" min="0" required
                      class="w-full pl-9 pr-4 py-3 border-2 text-base font-semibold rounded-xl transition-colors focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                      :class="abonoAmountError 
                        ? 'border-red-300 dark:border-red-700 bg-red-50 dark:bg-red-950/20 text-red-700 dark:text-red-400' 
                        : 'border-gray-200 dark:border-zinc-700 bg-gray-50 dark:bg-zinc-800 text-gray-900 dark:text-zinc-200'"
                      placeholder="0"/>
                  </div>
                  <p v-if="abonoAmountError" class="mt-1.5 text-xs text-red-500 dark:text-red-400 flex items-center gap-1">
                    <svg class="w-3.5 h-3.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                    {{ abonoAmountError }}
                  </p>
                </div>

                <!-- Método de pago -->
                <div>
                  <label class="block text-xs font-semibold text-gray-500 dark:text-zinc-400 uppercase tracking-wider mb-2">
                    Método de Pago
                  </label>
                  <select v-model="abonoForm.payment_method" required
                    class="w-full px-4 py-3 border-2 border-gray-200 dark:border-zinc-700 bg-gray-50 dark:bg-zinc-800 text-gray-900 dark:text-zinc-200 rounded-xl text-base focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors">
                    <option value="cash">Efectivo</option>
                    <option value="card">Tarjeta</option>
                    <option value="transfer">Transferencia</option>
                  </select>
                </div>

                <!-- Referencia -->
                <div>
                  <label class="block text-xs font-semibold text-gray-500 dark:text-zinc-400 uppercase tracking-wider mb-2">
                    Referencia <span class="text-gray-300 dark:text-zinc-600 font-normal normal-case">(opcional)</span>
                  </label>
                  <input v-model="abonoForm.reference" type="text"
                    class="w-full px-4 py-3 border-2 border-gray-200 dark:border-zinc-700 bg-gray-50 dark:bg-zinc-800 text-gray-900 dark:text-zinc-200 rounded-xl text-base focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors"
                    placeholder="No. operación, voucher..."/>
                </div>
              </div>

              <!-- Notas (ancho completo) -->
              <div class="mt-4">
                <label class="block text-xs font-semibold text-gray-500 dark:text-zinc-400 uppercase tracking-wider mb-2">
                  Notas <span class="text-gray-300 dark:text-zinc-600 font-normal normal-case">(opcional)</span>
                </label>
                <input v-model="abonoForm.notes" type="text"
                  class="w-full px-4 py-3 border-2 border-gray-200 dark:border-zinc-700 bg-gray-50 dark:bg-zinc-800 text-gray-900 dark:text-zinc-200 rounded-xl text-base focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors"
                  placeholder="Observaciones..."/>
              </div>

              <!-- Footer -->
              <div class="flex items-center justify-end gap-3 mt-7 pt-5 border-t border-gray-100 dark:border-zinc-800">
                <button type="button" @click="closeAbonoModal"
                  class="px-6 py-3 bg-red-50 dark:bg-red-950/30 hover:bg-red-100 dark:hover:bg-red-900/40 text-red-600 dark:text-red-400 text-sm font-bold rounded-xl border border-red-200 dark:border-red-800/50 transition-all duration-200 flex items-center gap-2">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                  Cancelar
                </button>
                <button type="submit"
                  :disabled="abonoSubmitting || !!abonoAmountError || !abonoForm.amount || abonoForm.amount <= 0"
                  class="px-6 py-3 bg-emerald-700 dark:bg-emerald-700 hover:bg-emerald-800 dark:hover:bg-emerald-600 text-white text-sm font-bold rounded-xl shadow-lg shadow-emerald-800/25 dark:shadow-emerald-900/40 transition-all duration-200 disabled:opacity-40 disabled:cursor-not-allowed disabled:shadow-none flex items-center gap-2">
                  <svg v-if="abonoSubmitting" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                  </svg>
                  <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                  </svg>
                  {{ abonoSubmitting ? 'Procesando...' : 'Registrar Abono' }}
                </button>
              </div>
            </form>

            <!-- Estado vacío si no hay cliente -->
            <div v-if="!abonoFoundCustomer && !abonoSearchError" class="text-center py-8">
              <svg class="w-12 h-12 mx-auto text-gray-300 dark:text-zinc-600 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
              </svg>
              <p class="text-sm text-gray-400 dark:text-zinc-500">Ingresa el documento del cliente para comenzar</p>
            </div>
          </div>
        </div>
      </div>
  </Teleport>

  <!-- ========== RECIBO / COLILLA DE ABONO ========== -->
  <Teleport to="body">
    <div v-if="showAbonoReceipt && lastAbonoData" class="fixed inset-0 z-[9999] flex items-center justify-center p-4">
      <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" @click="showAbonoReceipt = false"></div>
      <div class="relative bg-white dark:bg-zinc-900 rounded-2xl shadow-2xl dark:shadow-black/60 border border-gray-200 dark:border-zinc-800 w-full max-w-md overflow-hidden animate-fade-in">

        <!-- Header con checkmark animado -->
        <div class="bg-gradient-to-br from-emerald-500 to-emerald-600 dark:from-emerald-600 dark:to-emerald-700 px-6 py-5 text-center">
          <div class="w-16 h-16 rounded-full bg-white/20 flex items-center justify-center mx-auto mb-3">
            <svg class="w-9 h-9 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
            </svg>
          </div>
          <h3 class="text-xl font-bold text-white">Abono Registrado</h3>
          <p class="text-emerald-100 text-sm mt-1">Comprobante de pago</p>
        </div>

        <!-- Contenido del recibo -->
        <div class="px-6 py-5 space-y-4">
          <!-- Nombre del negocio -->
          <div class="text-center pb-3 border-b border-dashed border-gray-200 dark:border-zinc-700">
            <p class="text-base font-bold text-gray-900 dark:text-white">{{ systemSettings?.company_name || systemSettings?.business_name || 'Mi Empresa' }}</p>
            <p class="text-xs text-gray-500 dark:text-zinc-400 mt-0.5">Comprobante de Abono a Crédito</p>
          </div>

          <!-- Datos del cliente -->
          <div class="space-y-2">
            <div class="flex justify-between items-center">
              <span class="text-xs text-gray-500 dark:text-zinc-400">Cliente</span>
              <span class="text-sm font-semibold text-gray-900 dark:text-white">{{ lastAbonoData.customer?.name }}</span>
            </div>
            <div v-if="lastAbonoData.customer?.document_number" class="flex justify-between items-center">
              <span class="text-xs text-gray-500 dark:text-zinc-400">Documento</span>
              <span class="text-sm text-gray-700 dark:text-zinc-300">{{ lastAbonoData.customer.document_number }}</span>
            </div>
          </div>

          <!-- Separador -->
          <div class="border-t border-dashed border-gray-200 dark:border-zinc-700"></div>

          <!-- Detalles del pago -->
          <div class="space-y-2">
            <div class="flex justify-between items-center">
              <span class="text-xs text-gray-500 dark:text-zinc-400">Fecha</span>
              <span class="text-sm text-gray-700 dark:text-zinc-300">{{ new Date(lastAbonoData.date).toLocaleDateString('es-CO', { year: 'numeric', month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' }) }}</span>
            </div>
            <div class="flex justify-between items-center">
              <span class="text-xs text-gray-500 dark:text-zinc-400">Método de pago</span>
              <span class="text-sm text-gray-700 dark:text-zinc-300">{{ { cash: 'Efectivo', card: 'Tarjeta', transfer: 'Transferencia' }[lastAbonoData.method] || lastAbonoData.method }}</span>
            </div>
            <div v-if="lastAbonoData.reference" class="flex justify-between items-center">
              <span class="text-xs text-gray-500 dark:text-zinc-400">Referencia</span>
              <span class="text-sm text-gray-700 dark:text-zinc-300">{{ lastAbonoData.reference }}</span>
            </div>
            <div v-if="lastAbonoData.notes" class="flex justify-between items-start">
              <span class="text-xs text-gray-500 dark:text-zinc-400 mt-0.5">Notas</span>
              <span class="text-sm text-gray-700 dark:text-zinc-300 text-right max-w-[60%]">{{ lastAbonoData.notes }}</span>
            </div>
          </div>

          <!-- Separador -->
          <div class="border-t border-dashed border-gray-200 dark:border-zinc-700"></div>

          <!-- Monto pagado (destacado) -->
          <div class="bg-emerald-50 dark:bg-emerald-950/50 rounded-xl p-4 text-center border border-emerald-100 dark:border-emerald-800/50">
            <p class="text-xs font-medium text-emerald-600 dark:text-emerald-400 uppercase tracking-wide mb-1">Monto Abonado</p>
            <p class="text-3xl font-bold text-emerald-700 dark:text-emerald-400">${{ Number(lastAbonoData.amount).toLocaleString('es-CO') }}</p>
          </div>

          <!-- Saldo restante -->
          <div class="flex justify-between items-center bg-gray-50 dark:bg-zinc-800/50 rounded-lg px-4 py-2.5">
            <span class="text-xs font-medium text-gray-500 dark:text-zinc-400">Saldo pendiente</span>
            <span class="text-sm font-bold" :class="lastAbonoData.remaining_debt <= 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400'">
              {{ lastAbonoData.remaining_debt <= 0 ? 'Pagado' : '$' + Number(lastAbonoData.remaining_debt).toLocaleString('es-CO') }}
            </span>
          </div>

          <!-- ID de transacción -->
          <div v-if="lastAbonoData.id" class="text-center">
            <p class="text-[10px] text-gray-400 dark:text-zinc-500 font-mono">ID: #{{ lastAbonoData.id }}</p>
          </div>
        </div>

        <!-- Acciones -->
        <div class="px-6 pb-5 space-y-2">
          <!-- Fila de acciones de envío -->
          <div class="grid grid-cols-3 gap-2">
            <button @click="handlePrintAbonoReceipt" class="flex flex-col items-center gap-1.5 py-3 px-2 rounded-xl bg-gray-50 dark:bg-zinc-800 hover:bg-gray-100 dark:hover:bg-zinc-700 border border-gray-200 dark:border-zinc-700 transition-all duration-200">
              <svg class="w-5 h-5 text-gray-600 dark:text-zinc-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
              </svg>
              <span class="text-[10px] font-medium text-gray-600 dark:text-zinc-300">Imprimir</span>
            </button>
            <button @click="handleSendAbonoWhatsApp" class="flex flex-col items-center gap-1.5 py-3 px-2 rounded-xl bg-gray-50 dark:bg-zinc-800 hover:bg-emerald-50 dark:hover:bg-emerald-900/20 border border-gray-200 dark:border-zinc-700 hover:border-emerald-200 dark:hover:border-emerald-800/30 transition-all duration-200">
              <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400" fill="currentColor" viewBox="0 0 24 24">
                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/>
                <path d="M12 0C5.373 0 0 5.373 0 12c0 2.625.846 5.059 2.284 7.034L.789 23.492a.5.5 0 00.612.616l4.529-1.468A11.953 11.953 0 0012 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 22c-2.24 0-4.326-.732-6.012-1.97l-.168-.122-3.485 1.129 1.153-3.433-.135-.176A9.935 9.935 0 012 12C2 6.486 6.486 2 12 2s10 4.486 10 10-4.486 10-10 10z"/>
              </svg>
              <span class="text-[10px] font-medium text-emerald-600 dark:text-emerald-400">WhatsApp</span>
            </button>
            <button @click="handleSendAbonoEmail" class="flex flex-col items-center gap-1.5 py-3 px-2 rounded-xl bg-gray-50 dark:bg-zinc-800 hover:bg-blue-50 dark:hover:bg-blue-900/20 border border-gray-200 dark:border-zinc-700 hover:border-blue-200 dark:hover:border-blue-800/30 transition-all duration-200">
              <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
              </svg>
              <span class="text-[10px] font-medium text-blue-600 dark:text-blue-400">Email</span>
            </button>
          </div>

          <!-- Botón cerrar -->
          <button @click="showAbonoReceipt = false; lastAbonoData = null" class="w-full py-2.5 text-sm font-medium text-gray-500 dark:text-zinc-400 hover:text-gray-700 dark:hover:text-zinc-200 rounded-xl hover:bg-gray-50 dark:hover:bg-zinc-800 transition-all duration-200">
            Cerrar
          </button>
        </div>
      </div>
    </div>
  </Teleport>
  
</template>




<script setup>
import { apiCall } from '../services/api.js'
import { ref, reactive, computed, onMounted, onBeforeUnmount, onUnmounted, watch, nextTick, onActivated } from 'vue'
import { productsService } from '../services/productsService.js'
import { categoriesService } from '../services/categoriesService.js'
import { customersService } from '../services/customersService.js'
import { invoicesService } from '../services/invoicesService.js'
import { warehouseService } from '../services/warehouseService.js'
import { generateInvoicePDF as generateInvoicePDFTemplate, generateQuotationPDF as generateQuotationPDFTemplate, getPDFBlob } from '../utils/pdfTemplates/pdfGenerator.js'
import { useCashSession, cashSessionService } from '../services/cashSessionService.js'
import { appStore } from '../store/appStore.js' // Importar store global
import { useAutoRefresh } from '../composables/useRouteState.js'
import { useScreenScaling } from '../composables/useScreenScaling.js'
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
import PosFastFoodGrid from './pos/PosFastFoodGrid.vue'
import PosFastFoodCart from './pos/PosFastFoodCart.vue'
import { useModuleNavigation } from '../composables/useModuleNavigation.js'

// Switch para tipo de método de pago
const paymentType = ref('contado')

// Composables
const { showSuccess, showError, showWarning, showInfo } = useToast()
const { user } = useAuth()
const { navigateToModule } = useModuleNavigation()
const { 
  loyaltySettings, 
  isEnabled: loyaltyEnabled, 
  loadSettings: loadLoyaltySettings, 
  calculatePointsValue, 
  formatPointsAsMoney 
} = useLoyaltyPoints()

// Obtener nombre del vendedor actual
const getCurrentSeller = () => {
  const u = currentUser.value
  if (!u) return 'Vendedor'
  
  if (u.name) return u.name
  if (u.user && u.user.name) return u.user.name
  if (u.data && u.data.name) return u.data.name
  
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
const warehouses = ref([]) // Lista de todas las warehouses/sedes

// Current user info
const currentUser = computed(() => {
  const userData = user.value || authService.getUser()
  return userData || {}
})

// Determinar si el usuario es administrador (cierre rápido sin contraseña)
const isAdminUser = computed(() => {
  const role = currentUser.value?.role?.name || currentUser.value?.role || ''
  return role.toLowerCase() === 'administrador' || role.toLowerCase() === 'admin'
})

// Loading state para WhatsApp y Email
const isLoading = ref(false)

// Validaciones de Plan del Tenant
// WhatsApp solo para Premium y Enterprise
const canUseWhatsApp = computed(() => {
  const tenantPlan = appStore.tenantPlan || 'free_trial'
  const allowedPlans = ['premium', 'enterprise']
  return allowedPlans.includes(tenantPlan)
})

// Pedidos Web solo para Premium y Enterprise
const canUseWebOrders = computed(() => {
  const tenantPlan = appStore.tenantPlan || 'free_trial'
  const allowedPlans = ['premium', 'enterprise']
  return allowedPlans.includes(tenantPlan)
})

// TOUR DEL POS - Control de bienvenida y primera visita
const DEV_MODE = false // false = Tour solo primera vez | true = Tour siempre disponible
const isFirstVisit = ref(DEV_MODE || !localStorage.getItem('pos_tour_completed'))
const showWelcomeModal = ref(false)
const posTourRef = ref(null)

// TOUR EDUCATIVO DEL POS - Pasos amigables y didácticos
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

// Función para crear efecto de confeti ÉPICO
const triggerConfetti = () => {
  const duration = 3000
  const animationEnd = Date.now() + duration
  const colors = ['#3b82f6', '#8b5cf6', '#ec4899', '#10b981', '#f59e0b', '#ef4444']
  
  // Obtener factor de compensación de escalado
  const { appliedZoom, isCompensating } = useScreenScaling()
  const zoomFactor = isCompensating.value ? appliedZoom.value : 1

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

      // Desde la izquierda (no necesita compensación, x:0 siempre es el borde izquierdo)
      window.confetti({
        particleCount,
        angle: 60,
        spread: 55,
        origin: { x: 0, y: 0.6 },
        colors: colors,
        zIndex: 9999
      })
      
      // Desde la derecha (compensar para pantallas escaladas)
      // Si está escalado, ajustamos la posición para que salga del borde real
      window.confetti({
        particleCount,
        angle: 120,
        spread: 55,
        origin: { x: 1 / zoomFactor, y: 0.6 },
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
  
  // EFECTO DE CONFETI AL COMPLETAR EL TOUR
  triggerConfetti()
  
  showSuccess('¡Felicidades! Ya dominas el POS como un profesional ')
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

// Paleta de colores moderna para fallbacks (SaaS Enterprise Style)
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

// Obtener iniciales del producto
const getInitials = (name) => {
  if (!name) return '??'
  return name
    .split(' ')
    .map(word => word[0])
    .slice(0, 2)
    .join('')
    .toUpperCase()
}

// Obtener color determinista basado en el nombre
const getColorForName = (name) => {
  if (!name) return fallbackColors[0]
  let hash = 0
  for (let i = 0; i < name.length; i++) {
    hash = name.charCodeAt(i) + ((hash << 5) - hash)
  }
  const index = Math.abs(hash) % fallbackColors.length
  return fallbackColors[index]
}

// Generar SVG PROFESIONAL para placeholder (con gradiente elegante)
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

// Genera un fondo pastel suave a partir del color hex de la categoría
const getCategoryBg = (hexColor) => {
  if (!hexColor || hexColor === '#6b7280') return '#F3F4F6'
  // Parse hex → RGB, luego mezclar con blanco al 88% para un tono pastel muy suave
  const r = parseInt(hexColor.slice(1,3), 16)
  const g = parseInt(hexColor.slice(3,5), 16)
  const b = parseInt(hexColor.slice(5,7), 16)
  const mix = 0.88
  const pr = Math.round(r + (255 - r) * mix)
  const pg = Math.round(g + (255 - g) * mix)
  const pb = Math.round(b + (255 - b) * mix)
  return `rgb(${pr}, ${pg}, ${pb})`
}

// Función utilitaria para manejo inteligente de imágenes
const getProductImage = (product) => {
  // Intentar múltiples propiedades de imagen
  const imageUrl = product.image_url || product.image || product.img || product.photo
  
  // Verificar si la URL es válida Y existe
  if (imageUrl && typeof imageUrl === 'string' && imageUrl.trim()) {
    const url = imageUrl.trim()
    
    // Si es una ruta relativa de Laravel Storage (/storage/...), convertir a URL absoluta
    if (url.startsWith('/storage/')) {
      const backendUrl = import.meta.env.VITE_API_BASE_URL || window.location.origin
      return `${backendUrl}${url}`
    }
    
    // Si ya es una URL HTTP completa
    if ((url.startsWith('http://') || url.startsWith('https://')) && url.length > 20) {
      // Verificar que no sea un placeholder genérico
      if (!url.includes('placeholder') && !url.includes('default') && !url.includes('no-image')) {
        return url
      }
    }
    
    // Si es un data URI, devolverlo directamente
    if (url.startsWith('data:image')) {
      return url
    }
  }
  
  // SIEMPRE retornar SVG profesional si no hay imagen válida
  return generateAvatarSVG(product.name || 'Producto')
}

// Validar si una imagen es REAL (no placeholder SVG)
const isRealProductImage = (imageUrl) => {
  if (!imageUrl || typeof imageUrl !== 'string') return false
  const url = imageUrl.trim()
  if (!url) return false
  if (url.startsWith('data:image/svg+xml')) return false
  if (url.includes('placeholder') || url.includes('default') || url.includes('no-image')) return false
  return true
}

// Función para generar identificador de usuario consistente para descuentos
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

// Cargar todas las warehouses/sedes para validar multi-warehouse features
const loadWarehouses = async () => {
  try {
    const response = await warehouseService.getAll()
    
    // CORRECCIÓN: warehouseService.getAll() ya devuelve response.data
    // No necesitamos acceder a response.data.data
    if (response.warehouses) {
      warehouses.value = response.warehouses || []
    } else {
      warehouses.value = []
    }
  } catch (error) {
    console.error('[loadWarehouses] Error:', error)
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
const selectedPaymentMethod = ref('efectivo') // Por defecto EFECTIVO como solicitado
const cashReceived = ref(0)
const processing = ref(false)
const searchingQuotation = ref(false) // Evitar múltiples búsquedas simultáneas
const globalSearch = ref(localStorage.getItem('posGlobalSearch') === 'true') // Toggle para búsqueda global de productos

// Persistir el estado de búsqueda global
watch(globalSearch, (newValue) => {
  localStorage.setItem('posGlobalSearch', newValue.toString())
})

// Loyalty Points - Variables para redimir puntos
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

const showMobileCart = ref(false) // Modal carrito para móvil
const showMobilePaymentSelector = ref(false) // Selector de métodos de pago móvil
const showMobileCashInput = ref(false) // Input de efectivo para móvil
const mobileCashInput = ref(null) // Ref del input de efectivo móvil
const showCustomerSelector = ref(false)
const startCustomerCreation = ref(false)
const showCustomerHistory = ref(false)
const selectedCustomerForHistory = ref(null)
const showPaymentModal = ref(false)
const showAfterPaymentModal = ref(false)
const showReceiptModal = ref(false)
const showRequireCustomerModal = ref(false)

// Estados para sincronización con el modal de pago (Factus/CUFE)
const backendProcessingPayment = ref(false)
const backendPaymentSuccess = ref(false)

// Toggle Facturación Electrónica DIAN
const electronicInvoicingEnabled = ref(localStorage.getItem('electronicInvoicingEnabled') !== 'false')

// Función para toggle de facturación electrónica
const toggleElectronicInvoicing = () => {
  electronicInvoicingEnabled.value = !electronicInvoicingEnabled.value
  localStorage.setItem('electronicInvoicingEnabled', electronicInvoicingEnabled.value.toString())
}

// Estado para modal de límite de facturas alcanzado (plan gratuito)
const showFreePlanLimitModal = ref(false)
const freePlanLimitData = ref({
  invoiceCount: 0,
  maxInvoices: 10
})

// Estados para modales de email y premium
const showEmailModal = ref(false)
const emailInput = ref('')
const emailModalResolve = ref(null)
const showPremiumModalPOS = ref(false)
const premiumFeatureNamePOS = ref('')

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

// Forced-close audit state
const showForcedCloseModal = ref(false)
const forcedCloseSession = ref(null)
const forcedCloseAuditAmount = ref('')
const forcedCloseNotes = ref('')
const forcedCloseSubmitting = ref(false)
const forcedCloseError = ref('')
const forcedClosePassword = ref('')
const showForcedClosePassword = ref(false)
const forcedClosePasswordError = ref('')

// Estado del modal de devoluciones
const showReturnsModal = ref(false)
const preloadInvoiceNumber = ref('')

// Estado del modal de abonos (pagos a créditos)
const showAbonoModal = ref(false)
const customersWithDebt = ref([])
const abonoDocSearch = ref('')
const abonoSearching = ref(false)
const abonoSearchError = ref('')
const abonoFoundCustomer = ref(null)
const showAbonoSuggestions = ref(false)
const abonoForm = ref({
  amount: 0,
  payment_method: 'cash',
  reference: '',
  notes: ''
})
const abonoSubmitting = ref(false)
const showAbonoReceipt = ref(false)
const lastAbonoData = ref(null)

const abonoAmountError = computed(() => {
  const amt = abonoForm.value.amount
  if (!amt || amt <= 0) return ''
  const cust = abonoFoundCustomer.value
  if (!cust) return ''
  const debt = parseFloat(cust.current_debt)
  if (amt > debt) return `El monto excede la deuda de ${formatCurrency(debt)}`
  return ''
})

const abonoSuggestions = computed(() => {
  const q = abonoDocSearch.value.trim()
  if (!q || q.length < 2 || abonoFoundCustomer.value) return []
  const normalized = q.replace(/[\s\-\.]/g, '').toLowerCase()
  return customersWithDebt.value.filter(c => {
    const doc = (c.document_number || '').replace(/[\s\-\.]/g, '').toLowerCase()
    const name = (c.name || '').toLowerCase()
    return doc.includes(normalized) || name.includes(normalized)
  }).slice(0, 6)
})

// Estado del modal de confirmación de cierre de venta
const showCloseTabModal = ref(false)
const closeTabData = ref({ tabId: null, tabName: '' })

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

// MODO FASHION - Variables para el diseño de tienda de ropa
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

// FIX: Mostrar spinner de carga SOLO después de un delay para evitar flash
// Esto previene el parpadeo molesto cuando la carga es rápida
const loadingDelayPassed = ref(false)
let loadingDelayTimer = null

// Watcher para manejar el delay de loading
watch(productsLoading, (isLoading) => {
  if (isLoading) {
    // Iniciar timer - solo mostrar spinner si la carga tarda más de 200ms
    loadingDelayTimer = setTimeout(() => {
      loadingDelayPassed.value = true
    }, 200)
  } else {
    // Limpiar timer y resetear
    if (loadingDelayTimer) {
      clearTimeout(loadingDelayTimer)
      loadingDelayTimer = null
    }
    loadingDelayPassed.value = false
  }
})

// Mostrar spinner de carga elegante (reemplaza los skeletons feos)
const showLoadingSpinner = computed(() => {
  // Mostrar spinner SOLO si:
  // 1. Está cargando productos
  // 2. NO hay productos aún
  // 3. Pasó el delay mínimo (200ms) para evitar flash
  return productsLoading.value && (products.value?.length || 0) === 0 && loadingDelayPassed.value
})

// Detectar si es primera vez (NO hay productos en la base de datos)
const isFirstTimeNoProducts = computed(() => {
  // Si estamos mostrando spinner, no mostrar mensaje de "primera vez"
  if (showLoadingSpinner.value || productsLoading.value) return false
  
  // Si NO hay productos en absoluto (sin filtros ni búsqueda)
  const totalProducts = products.value?.length || 0
  return totalProducts === 0 && !productsLoading.value
})

// Detectar si el resultado vacío es por filtros/búsqueda
const isEmptyByFilters = computed(() => {
  // No mostrar si hay spinner o es primera vez
  if (showLoadingSpinner.value || isFirstTimeNoProducts.value) return false
  
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
const quantityModalInputRef = ref(null)

// Computed: subtotal en tiempo real para el modal de cantidad
const quantityModalSubtotal = computed(() => {
  if (!selectedProductForQuantity.value || !customQuantity.value) return 0
  const qty = parseFloat(customQuantity.value)
  if (isNaN(qty) || qty <= 0) return 0
  const product = selectedProductForQuantity.value
  const inputUnit = getInputUnit(product.measurement_unit)
  const qtyBase = convertQuantity(qty, inputUnit, product.measurement_unit)
  return qtyBase * (product.price || 0)
})

// Computed: nivel de stock para color dinámico del badge
const quantityModalStockLevel = computed(() => {
  if (!selectedProductForQuantity.value) return 'good'
  const stock = getTotalStock(selectedProductForQuantity.value)
  if (stock <= 0) return 'critical'
  if (stock <= 5) return 'critical'
  if (stock <= 20) return 'low'
  return 'good'
})

// Computed: cantidades rápidas según unidad activa
const quantityModalQuickAmounts = computed(() => {
  if (!selectedProductForQuantity.value) return [0.25, 0.5, 1, 2]
  const unit = selectedProductForQuantity.value.measurement_unit
  if (useAlternativeUnit.value && ['kg', 'l'].includes(unit)) {
    return [250, 500, 1000, 2000]
  }
  if (!useAlternativeUnit.value && ['g', 'ml'].includes(unit)) {
    return [100, 250, 500, 1000]
  }
  return [0.25, 0.5, 1, 2]
})

// Watch: auto-conversión al cambiar unidad (kg↔g, L↔ml)
watch(useAlternativeUnit, (isAlt, wasAlt) => {
  if (!customQuantity.value || !selectedProductForQuantity.value) return
  const qty = parseFloat(customQuantity.value)
  if (isNaN(qty) || qty <= 0) return
  const baseUnit = selectedProductForQuantity.value.measurement_unit
  const fromUnit = wasAlt ? getAlternativeUnit(baseUnit) : baseUnit
  const toUnit = isAlt ? getAlternativeUnit(baseUnit) : baseUnit
  const converted = convertQuantity(qty, fromUnit, toUnit)
  customQuantity.value = parseFloat(converted.toFixed(4))
})

// Watch: auto-focus al abrir el modal de cantidad
watch(showQuantityModal, (isOpen) => {
  if (isOpen) {
    nextTick(() => {
      if (quantityModalInputRef.value) {
        quantityModalInputRef.value.focus()
        quantityModalInputRef.value.select()
      }
    })
  }
})

// Modal de Selección de Variantes (Fashion/Moda)
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

// SISTEMA DE MULTI-TABS para ventas simultáneas
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

// Lista de iconos de categorías (95 iconos) - sincronizada con CategoriesView
const categoryIcons = [
  // General
  { id: 'shopping-bag', emoji: '' }, { id: 'gift', emoji: '' }, { id: 'package', emoji: '' }, { id: 'money', emoji: '' },
  // Comida y Bebidas
  { id: 'food', emoji: '' }, { id: 'drink', emoji: '' }, { id: 'coffee', emoji: '' }, { id: 'wine', emoji: '' },
  { id: 'beer', emoji: '' }, { id: 'bread', emoji: '' }, { id: 'meat', emoji: '' }, { id: 'fruit', emoji: '' },
  { id: 'vegetable', emoji: '' }, { id: 'candy', emoji: '' }, { id: 'ice-cream', emoji: '' }, { id: 'pizza', emoji: '' },
  { id: 'burger', emoji: '' }, { id: 'chicken', emoji: '' }, { id: 'fish', emoji: '' }, { id: 'cheese', emoji: '' },
  // Belleza
  { id: 'perfume', emoji: '' }, { id: 'cosmetics', emoji: '' }, { id: 'nail', emoji: '' }, { id: 'haircut', emoji: '' }, { id: 'mirror', emoji: '🪞' },
  // Limpieza
  { id: 'soap', emoji: '' }, { id: 'cleaning', emoji: '' }, { id: 'toilet', emoji: '' },
  // Papelería
  { id: 'book', emoji: '' }, { id: 'pencil', emoji: '' }, { id: 'scissors', emoji: '' }, { id: 'printer', emoji: '' }, { id: 'folder', emoji: '' },
  // Moda
  { id: 'tshirt', emoji: '' }, { id: 'dress', emoji: '' }, { id: 'jeans', emoji: '' }, { id: 'shoe', emoji: '' },
  { id: 'heels', emoji: '' }, { id: 'hat', emoji: '' }, { id: 'watch', emoji: '⌚' }, { id: 'glasses', emoji: '' },
  { id: 'bag', emoji: '' }, { id: 'jewelry', emoji: '' }, { id: 'necktie', emoji: '' },
  // Salud
  { id: 'pill', emoji: '' }, { id: 'medical', emoji: '' }, { id: 'syringe', emoji: '' }, { id: 'thermometer', emoji: '' },
  // Niños
  { id: 'toy', emoji: '' }, { id: 'baby', emoji: '' }, { id: 'bottle', emoji: '' }, { id: 'stroller', emoji: '' },
  // Tecnología
  { id: 'electronics', emoji: '' }, { id: 'computer', emoji: '' }, { id: 'camera', emoji: '' }, { id: 'headphones', emoji: '' },
  { id: 'keyboard', emoji: '⌨️' }, { id: 'mouse', emoji: '' }, { id: 'tv', emoji: '' }, { id: 'game', emoji: '' },
  // Ferretería
  { id: 'tools', emoji: '' }, { id: 'hammer', emoji: '' }, { id: 'saw', emoji: '🪚' }, { id: 'wrench', emoji: '' }, { id: 'paint', emoji: '' },
  // Mascotas
  { id: 'pet', emoji: '' }, { id: 'dog', emoji: '' }, { id: 'cat', emoji: '' }, { id: 'fish-pet', emoji: '' }, { id: 'bird', emoji: '' },
  // Jardín
  { id: 'plant', emoji: '' }, { id: 'flower', emoji: '' }, { id: 'tree', emoji: '' }, { id: 'garden-tools', emoji: '' },
  // Deportes
  { id: 'sport', emoji: '' }, { id: 'basketball', emoji: '' }, { id: 'tennis', emoji: '' }, { id: 'gym', emoji: '' }, { id: 'bike', emoji: '' },
  // Automotriz
  { id: 'car', emoji: '' }, { id: 'motorcycle', emoji: '' }, { id: 'tire', emoji: '' }, { id: 'gas', emoji: '' },
  // Hogar
  { id: 'home', emoji: '' }, { id: 'furniture', emoji: '' }, { id: 'bed', emoji: '' }, { id: 'lamp', emoji: '' },
  { id: 'kitchen', emoji: '' }, { id: 'decoration', emoji: '' }, { id: 'door', emoji: '' }, { id: 'key', emoji: '' }
]

// Función helper para obtener emoji de icono de categoría
const getCategoryIcon = (iconId) => {
  if (!iconId) {
    return ''
  }
  const icon = categoryIcons.find(i => i.id === iconId)
  if (!icon) {
    return ''
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

// Computed para saber si debe mostrar features de múltiples bodegas
const shouldShowMultiWarehouseFeatures = computed(() => {
  // Solo mostrar en planes premium/enterprise
  const plan = appStore.tenantPlan
  const isPremiumOrEnterprise = plan === 'premium' || plan === 'enterprise'
  
  if (!isPremiumOrEnterprise) {
    return false
  }
  
  // CRÍTICO: Solo mostrar si hay MÁS DE UNA warehouse/sede registrada
  if (warehouses.value.length <= 1) {
    return false
  }
  
  // Y solo cuando hay sesión abierta con bodega asignada
  return hasOpenSession.value && currentSession.value?.warehouse_id
})

// Computed para detectar si es una tienda de MODA (Fashion Store)
const isFashionStore = computed(() => {
  // Detección de tienda de moda
  // PRIORIDAD 1: Configuración manual (anula todo lo demás)
  
  const storeType = systemSettings.value?.store_type
  
  // Si hay configuración explícita, respetarla siempre
  if (storeType) {
    const isFashion = storeType === 'fashion'
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
    return true
  }
  
  // Verificar categorías (solo necesita 2+ categorías de moda)
  const cats = appStore.categories || []
  if (!Array.isArray(cats) || cats.length === 0) {
    return false
  }
  
  const matchedCategories = cats.filter(cat => {
    const catName = (cat.name || '').toLowerCase()
    return fashionCategories.some(f => catName.includes(f))
  })
  
  const fashionCount = matchedCategories.length
  
  if (fashionCount >= 2) {
    return true
  }
  
  return false
})

// Computed para detectar si es un RESTAURANTE o negocio de comidas
const isFastFoodStore = computed(() => {
  // Detección de restaurante/comida rápida
  // PRIORIDAD 1: Configuración manual (anula todo lo demás)
  
  const storeType = systemSettings.value?.store_type
  
  // Si hay configuración explícita, respetarla siempre
  if (storeType) {
    const isRestaurant = storeType === 'restaurant' || storeType === 'fast_food' || storeType === 'food'
    return isRestaurant
  }
  
  // PRIORIDAD 2: Auto-detección basada en palabras clave
  
  const foodKeywords = [
    'restaurante', 'restaurant', 'comida', 'food', 'fast food',
    'cafeteria', 'cafetería', 'cafe', 'café', 'fritos', 'fritanga',
    'pizzeria', 'pizzería', 'pizza', 'burger', 'hamburguesa',
    'taco', 'tacos', 'taqueria', 'taquería', 'pollo', 'chicken',
    'panaderia', 'panadería', 'bakery', 'heladeria', 'heladería',
    'asadero', 'asados', 'bbq', 'grill', 'sushi', 'comidas rapidas',
    'empanadas', 'arepas', 'cocina', 'kitchen', 'deli', 'delicatessen'
  ]
  
  const foodCategories = [
    'fritos', 'bebidas', 'combos', 'postres', 'entradas',
    'platos fuertes', 'hamburguesas', 'pizzas', 'tacos', 'burritos',
    'pollo', 'carnes', 'ensaladas', 'sopas', 'desayunos',
    'almuerzos', 'cenas', 'snacks', 'adicionales', 'salsas',
    'acompañamientos', 'jugos', 'malteadas', 'helados', 'cafés',
    'gaseosas', 'cervezas', 'licores', 'vinos', 'cocteles'
  ]
  
  // Verificar nombre del negocio
  const businessName = (systemSettings.value?.business_name || appStore.businessName || '').toLowerCase()
  const hasBusinessNameMatch = foodKeywords.some(kw => businessName.includes(kw))
  
  if (hasBusinessNameMatch) {
    return true
  }
  
  // Verificar categorías (necesita 2+ categorías de comida)
  const cats = appStore.categories || []
  if (!Array.isArray(cats) || cats.length === 0) {
    return false
  }
  
  const matchedFoodCategories = cats.filter(cat => {
    const catName = (cat.name || '').toLowerCase()
    return foodCategories.some(f => catName.includes(f))
  })
  
  const foodCount = matchedFoodCategories.length
  
  if (foodCount >= 2) {
    return true
  }
  
  return false
})

// Computed para determinar el tipo de layout actual
const currentLayoutType = computed(() => {
  if (isFastFoodStore.value) return 'restaurant'
  if (isFashionStore.value) return 'fashion'
  return 'general'
})

// MODO FAST FOOD - Variables para el diseño de comida rápida
const fastFoodOrderType = ref('dine_in') // 'dine_in' | 'take_out'
const selectedFastFoodCategory = ref(null) // Categoría seleccionada en tabs

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

// Control de visualización - Límites para mejor rendimiento
const MAX_VISIBLE_CATEGORIES = 10 // Máximo de categorías visibles inicialmente
const PRODUCTS_PER_PAGE = 60 // Productos por página
const showAllCategories = ref(false) // Toggle para mostrar todas las categorías
const currentProductPage = ref(1) // Página actual de productos

const filteredProducts = computed(() => {
  let filtered = products.value

  // Filtrar por categoría (comparar como números para evitar problemas de tipos)
  if (selectedCategory.value) {
    const catId = parseInt(selectedCategory.value)
    filtered = filtered.filter(product => parseInt(product.category_id) === catId)
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

// Categorías visibles (con límite)
const visibleCategories = computed(() => {
  if (showAllCategories.value) {
    return categories.value
  }
  return categories.value.slice(0, MAX_VISIBLE_CATEGORIES)
})

// Cantidad de categorías ocultas
const hiddenCategoriesCount = computed(() => {
  return Math.max(0, categories.value.length - MAX_VISIBLE_CATEGORIES)
})

// Productos paginados (60 por página)
const paginatedProducts = computed(() => {
  const endIndex = currentProductPage.value * PRODUCTS_PER_PAGE
  return filteredProducts.value.slice(0, endIndex)
})

// Total de productos restantes
const remainingProductsCount = computed(() => {
  return Math.max(0, filteredProducts.value.length - paginatedProducts.value.length)
})

// Función para cargar más productos
const loadMoreProducts = () => {
  currentProductPage.value++
}

// Reset de paginación cuando cambia categoría o búsqueda
watch([selectedCategory, searchTerm], () => {
  currentProductPage.value = 1
})

const subtotal = computed(() => {
  // OPTIMIZADO: No llamar calculateDiscount() aquí, causa re-renders infinitos
  // El watcher se encarga de recalcular el descuento cuando cambia el subtotal
  return cart.items.reduce((total, item) => total + (item.price * item.quantity), 0)
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
  // Aplicar descuento por puntos si están activados
  if (usePoints.value && pointsDiscount.value > 0) {
    return Math.max(0, baseTotal - pointsDiscount.value)
  }
  return baseTotal
})

const totalItems = computed(() => {
  return cart.items.reduce((total, item) => total + item.quantity, 0)
})

// Loyalty Points - Computed properties
const canUseLoyaltyPoints = computed(() => {
  // OPTIMIZACIÓN SaaS: Si loyalty está desactivado, retornar inmediatamente sin procesar
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
    console.error('Error al obtener próximo número de factura:', error)
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

// Watchers para loyalty points
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
    console.error('Error al validar código promocional:', error)
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

// Watcher para limpiar descuento cuando el carrito queda vacío - OPTIMIZADO
watch(() => cart.items.length, (newLength) => {
  if (newLength === 0) {
    removeDiscount()
  }
}, { flush: 'post' }) // Ejecutar después del render para evitar bloqueos

// Watcher para recalcular descuento cuando cambia el subtotal - OPTIMIZADO CON DEBOUNCE
let subtotalWatcherTimeout = null
watch(() => subtotal.value, (newSubtotal) => {
  // Debounce para evitar cálculos excesivos
  if (subtotalWatcherTimeout) clearTimeout(subtotalWatcherTimeout)
  
  subtotalWatcherTimeout = setTimeout(() => {
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
  }, 100) // Esperar 100ms antes de recalcular
}, { flush: 'post' })

// Helper para obtener cantidad de producto en carrito
const getProductQuantityInCart = (productId) => {
  const item = cart.items.find(item => item.id === productId)
  return item ? item.quantity : 0
}

// Función helper para calcular stock total de todas las bodegas
const getTotalStock = (product) => {
  // SIEMPRE usar current_stock como fuente de verdad
  // Este campo se actualiza automáticamente en el backend cuando se ajusta stock
  // en cualquier bodega (ver Product::updateStock y Product::updateStockInWarehouse)
  // 
  // IMPORTANTE: No sumar warehouses manualmente, el backend ya lo hace
  return product.current_stock || 0
}

// Obtener colores únicos disponibles de las variantes del producto
const getProductColors = (product) => {
  if (!product.variants || product.variants.length <= 1) return []
  
  const colors = new Set()
  product.variants.forEach(variant => {
    if (variant.options && Array.isArray(variant.options)) {
      variant.options.forEach(opt => {
        // Buscar opciones de tipo COLOR
        if (opt.type === 'COLOR' && opt.value) {
          colors.add(opt.value)
        }
      })
    }
  })
  return Array.from(colors)
}

// Obtener tallas únicas disponibles de las variantes del producto
const getProductSizes = (product) => {
  if (!product.variants || product.variants.length <= 1) return []
  
  const sizes = new Set()
  const sizeOrder = ['XS', 'S', 'M', 'L', 'XL', 'XXL', 'XXXL', '2XL', '3XL']
  
  product.variants.forEach(variant => {
    if (variant.options && Array.isArray(variant.options)) {
      variant.options.forEach(opt => {
        // Buscar opciones de tipo TALLA o SIZE
        if ((opt.type === 'SIZE' || opt.type === 'TALLA') && opt.value) {
          sizes.add(opt.value.toUpperCase())
        }
      })
    }
  })
  
  // Ordenar tallas por orden lógico
  return Array.from(sizes).sort((a, b) => {
    const idxA = sizeOrder.indexOf(a)
    const idxB = sizeOrder.indexOf(b)
    if (idxA >= 0 && idxB >= 0) return idxA - idxB
    if (idxA >= 0) return -1
    if (idxB >= 0) return 1
    return a.localeCompare(b)
  })
}

// Métodos
const addToCart = (product) => {
  // INTERCEPTAR: Si el producto tiene variantes REALES (no productos simples)
  // Un producto simple tiene 1 variante sin options, un producto con variantes tiene múltiples variantes o options
  let hasRealVariants = false
  
  if (product.variants && product.variants.length > 0) {
    if (product.variants.length > 1) {
      // Múltiples variantes = producto con opciones
      hasRealVariants = true
    } else {
      // 1 sola variante - verificar si tiene opciones
      const variant = product.variants[0]
      
      // Verificar options (array)
      if (variant.options && Array.isArray(variant.options) && variant.options.length > 0) {
        hasRealVariants = true
      }
      
      // Verificar options_summary (JSON string)
      if (!hasRealVariants && variant.options_summary) {
        try {
          const optionsSummary = typeof variant.options_summary === 'string' 
            ? JSON.parse(variant.options_summary) 
            : variant.options_summary
          if (Array.isArray(optionsSummary) && optionsSummary.length > 0) {
            hasRealVariants = true
          }
        } catch (e) {
          // Si falla el parse, no tiene opciones válidas
          hasRealVariants = false
        }
      }
    }
  }
  
  if (hasRealVariants) {
    selectedProductForVariants.value = product
    showVariantSelector.value = true
    
    return
  }
  
  // Calcular stock disponible (ya maneja modo LOCAL vs GLOBAL)
  const totalStock = getTotalStock(product)
  
  // Validar que haya stock disponible
  if (totalStock <= 0) {
    if (!globalSearch.value) {
      showWarning(`No hay stock de "${product.name}" en ${currentSession.value?.warehouse?.name || 'esta tienda'}. Cambia a búsqueda GLOBAL para ver productos de otras tiendas.`)
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

// FAST FOOD - Agregar al carrito con animación rápida
const addToCartFastFood = (product) => {
  // Usar la misma lógica que addToCart pero con feedback visual más rápido
  addToCart(product)
}

// FAST FOOD - Pago rápido directo
const handleQuickPayment = (paymentMethod) => {
  if (!canShowPaymentModal.value || quotationMode.value) return
  
  // Establecer el método de pago seleccionado
  selectedPaymentMethod.value = paymentMethod
  
  // Abrir el modal de confirmación de pago
  handleCobrarClick()
}

// FAST FOOD - Obtener emoji según categoría/nombre del producto
const getProductEmoji = (product) => {
  const name = (product.name || '').toLowerCase()
  const category = (product.category_name || product.category || '').toLowerCase()
  
  // Mapeo de palabras clave a emojis
  const emojiMap = {
    // Fritos
    'pollo': '', 'chicken': '', 'alitas': '',
    'papa': '', 'papas': '', 'fries': '', 'patata': '',
    'empanada': '', 'empanadita': '',
    'arepa': '🫓', 'arepita': '🫓',
    'salchipapa': '', 'salchi': '',
    'chorizo': '', 'hot dog': '', 'perro': '',
    
    // Hamburguesas
    'hamburguesa': '', 'burger': '', 'hambur': '',
    'sandwich': '', 'sándwich': '', 'sanduche': '',
    
    // Mexicana
    'taco': '', 'tacos': '',
    'burrito': '', 'wrap': '',
    'nacho': '', 'nachos': '',
    'quesadilla': '🫔',
    
    // Pizza/Italiana
    'pizza': '', 'pizzeta': '',
    'pasta': '', 'espagueti': '', 'lasaña': '',
    
    // Bebidas
    'gaseosa': '', 'soda': '', 'cola': '', 'refresco': '',
    'jugo': '', 'juice': '',
    'agua': '', 'water': '',
    'cerveza': '', 'beer': '',
    'café': '', 'coffee': '', 'cafe': '',
    'malteada': '', 'milkshake': '', 'batido': '',
    'limonada': '', 'naranjada': '',
    
    // Postres
    'helado': '', 'ice cream': '',
    'torta': '', 'cake': '', 'pastel': '',
    'dona': '', 'donut': '',
    'brownie': '', 'chocolate': '',
    'flan': '', 'pudding': '',
    
    // Otros
    'ensalada': '', 'salad': '',
    'sopa': '', 'caldo': '',
    'arroz': '', 'rice': '',
    'carne': '', 'steak': '', 'res': '',
    'pescado': '', 'fish': '',
    'sushi': '',
    'camarón': '', 'camarones': '',
  }
  
  // Buscar en nombre primero, luego en categoría
  for (const [keyword, emoji] of Object.entries(emojiMap)) {
    if (name.includes(keyword) || category.includes(keyword)) {
      return emoji
    }
  }
  
  // Emoji por defecto según categoría general
  if (category.includes('bebida') || category.includes('drink')) return ''
  if (category.includes('postre') || category.includes('dessert')) return ''
  if (category.includes('combo') || category.includes('meal')) return ''
  if (category.includes('frito') || category.includes('fried')) return ''
  if (category.includes('entrada') || category.includes('appetizer')) return ''
  
  return '' // Emoji por defecto
}

// FAST FOOD - Obtener emoji para categoría
const getCategoryEmoji = (categoryName) => {
  const name = (categoryName || '').toLowerCase()
  
  const categoryEmojis = {
    'fritos': '',
    'bebidas': '',
    'combos': '',
    'hamburguesas': '',
    'pizzas': '',
    'postres': '',
    'entradas': '',
    'pollos': '',
    'tacos': '',
    'desayunos': '',
    'almuerzos': '',
    'cenas': '',
    'especiales': '⭐',
    'promociones': '',
    'adicionales': '',
    'salsas': '🫙',
    'ensaladas': '',
    'sopas': '',
    'carnes': '',
    'mariscos': '',
    'pastas': '',
    'wraps': '',
    'sandwiches': '',
    'cafés': '',
    'jugos': '',
    'cervezas': '',
    'helados': '',
  }
  
  for (const [keyword, emoji] of Object.entries(categoryEmojis)) {
    if (name.includes(keyword.slice(0, -1)) || name.includes(keyword)) {
      return emoji
    }
  }
  
  return ''
}

// Manejar confirmación del modal de variantes
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
      variant_id: variant.id, // ID único de la variante
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

// FUNCIONES DE MULTI-TABS

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
    // Mostrar modal de confirmación
    closeTabData.value = {
      tabId: tabId,
      tabName: getTabDisplayName(tab)
    }
    showCloseTabModal.value = true
    return
  }
  
  // Si no tiene items, cerrar directamente
  await performCloseTab(tabId)
}

// Función auxiliar para ejecutar el cierre de la pestaña
const performCloseTab = async (tabId) => {
  const tab = salesTabs.value.find(t => t.id === tabId)
  
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

// Confirmar cierre de venta desde el modal
const confirmCloseTab = async () => {
  const tabId = closeTabData.value.tabId
  showCloseTabModal.value = false
  
  if (tabId) {
    await performCloseTab(tabId)
  }
}

// Cancelar cierre de venta
const cancelCloseTab = () => {
  showCloseTabModal.value = false
  closeTabData.value = { tabId: null, tabName: '' }
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

// Manejador de selección de método de pago móvil
const handleMobilePaymentMethodSelect = (method) => {
  selectedPaymentMethod.value = method.id
  showMobilePaymentSelector.value = false
  
  // Si es efectivo, mostrar input de monto
  if (method.id === 'efectivo') {
    // Pre-llenar con el total si está vacío
    if (!cashReceived.value || cashReceived.value < total.value) {
      cashReceived.value = total.value
    }
    showMobileCashInput.value = true
    // Focus en el input cuando se abra
    nextTick(() => {
      if (mobileCashInput.value) {
        mobileCashInput.value.focus()
        mobileCashInput.value.select()
      }
    })
  } else {
    // Para otros métodos, ir directo al modal de confirmación
    showPaymentModal.value = true
  }
}

// Nuevo flujo de pago mejorado
const handleCobrarClick = async () => {
  // VALIDACIÓN CRÍTICA: Si la configuración requiere cliente, validar ANTES de cualquier cosa
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

    // 4. Validar cupo disponible (basado en SUBTOTAL de productos pendientes, NO el total con recargo)
    // El backend ya calcula available_credit correctamente basado en subtotales
    // Si available_credit está disponible, usarlo; si no, calcular con credit_limit - subtotal_debt
    let availableCredit = parseFloat(selectedCustomer.value.available_credit || 0)
    
    // Fallback: Si no viene available_credit, usar subtotal_debt (sin recargo)
    // Si tampoco hay subtotal_debt, usar current_debt dividido por el factor de recargo
    if (!selectedCustomer.value.available_credit && selectedCustomer.value.available_credit !== 0) {
      const creditLimit = parseFloat(selectedCustomer.value.credit_limit || 0)
      // Usar subtotal_debt si existe, sino estimar desde current_debt
      let subtotalDebt = parseFloat(selectedCustomer.value.subtotal_debt || 0)
      if (!subtotalDebt && selectedCustomer.value.current_debt) {
        // Estimar: current_debt incluye recargo, dividir por factor
        const factor = 1 + (surchargePercent / 100)
        subtotalDebt = parseFloat(selectedCustomer.value.current_debt || 0) / factor
      }
      availableCredit = creditLimit - subtotalDebt
    }

    if (total.value > availableCredit) {
      showError(`Crédito insuficiente. Disponible: $${availableCredit.toLocaleString()} - Requerido: $${total.value.toLocaleString()}`)
      return
    }
  }

  showPaymentModal.value = true
}

// Helper: Obtener ID del Consumidor Final dinámicamente
const getConsumidorFinalId = () => {
  const consumidorFinal = appStore.customers.find(c => c.document_number === '222222222222')
  return consumidorFinal?.id || null
}

const handlePaymentConfirmed = async (paymentData) => {
  // VALIDACIÓN: Si la configuración requiere cliente, validar antes de procesar (segunda barrera de seguridad)
  if (systemSettings.value.require_customer && (!selectedCustomer.value || selectedCustomer.value.name === 'Cliente Final')) {
    showPaymentModal.value = false
    showRequireCustomerModal.value = true
    return
  }
  
  // Iniciar procesamiento del backend (el modal mostrará estado "processing")
  backendProcessingPayment.value = true
  backendPaymentSuccess.value = false
  
  // NO cerrar modal de confirmación - ahora cambia a estado 'success' internamente
  
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
        total: total.value  // Cambiado de total_amount a total
      }
      
      // Llamar al servicio para convertir la cotización
      const result = await invoicesService.convertQuoteToSale(quotationCode, conversionData)
      
      if (result.success) {
        
        // Preparar datos para el recibo usando la nueva factura generada
        // Calcular total con recargo si es crédito
        const surchargeForQuote = paymentData.method === 'credit' ? (paymentData.fee || 0) : 0
        const totalWithSurcharge = total.value + surchargeForQuote
        
        lastSale.value = {
          id: result.data.id || loadedQuotation.value.id,
          invoiceNumber: result.data.custom_number || result.data.number || `FV-${result.data.id}`,
          date: toMySQLDateTime(),
          due_date: toMySQLDateTime(new Date(Date.now() + 30 * 24 * 60 * 60 * 1000)),
          customer_id: selectedCustomer.value?.id || getConsumidorFinalId(),
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
          total: totalWithSurcharge, // Total CON recargo incluido
          taxRate: displayTaxRate.value,
          payments: [{
            method: paymentData.method,
            amount: paymentData.amount
          }],
          change: paymentData.change || 0,
          convertedFromQuote: true, // Flag para identificar que fue convertida
          // Recargo CrediTienda
          surcharge_amount: surchargeForQuote,
          payment_method: paymentData.method
        }
      } else {
        throw new Error(result.message || 'Error al convertir cotización')
      }
      
    } else {
      // Crear nueva venta (flujo normal cuando no hay cotización cargada)
      
      const currentDate = new Date()
      const dueDate = new Date()
      dueDate.setDate(currentDate.getDate() + 30)
      
      // Calcular total con recargo si es crédito
      const surchargeAmount = paymentData.method === 'credit' ? (paymentData.fee || 0) : 0
      const totalWithSurcharge = total.value + surchargeAmount
      
      lastSale.value = {
        id: Date.now(),
        invoiceNumber: 'Generando...', // Placeholder temporal hasta crear en backend
        date: toMySQLDateTime(currentDate),
        due_date: toMySQLDateTime(dueDate),
        customer_id: selectedCustomer.value?.id || getConsumidorFinalId(), // Consumidor Final por defecto
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
        total: totalWithSurcharge, // Total CON recargo incluido
        taxRate: displayTaxRate.value,
        payments: [{
          method: paymentData.method,
          methodName: paymentData.methodName || getPaymentMethodName(paymentData.method),
          amount: paymentData.amount || totalWithSurcharge
        }],
        // Recargo CrediTienda (normalizado a surcharge_amount)
        surcharge: surchargeAmount,
        surcharge_amount: surchargeAmount,
        payment_method: paymentData.method,
        change: paymentData.change || 0
      }
    }
  } catch (error) {
    console.error('Error al procesar pago:', error)
    // Resetear estados de backend en caso de error
    backendProcessingPayment.value = false
    backendPaymentSuccess.value = false
    showError('Error al procesar el pago: ' + error.message)
    return
  }
  
  // Refrescar solo productos para actualizar stock sin borrar todo
  await refreshData('products')
  
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
        // Skip facturación electrónica si está desactivada
        skip_electronic_invoice: !electronicInvoicingEnabled.value,
        items: lastSale.value.items.map(item => ({
          product_id: item.id,
          variant_id: item.variant_id || null, // ID de variante si es producto fashion
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
        // Información de puntos redimidos
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
        
        // Actualizar vendedor con el que retornó el backend (fuente de verdad)
        if (result.data.seller_name) {
          lastSale.value.cashier = result.data.seller_name
        }
        
        // FACTUS: Agregar datos de facturación electrónica DIAN si existen
        if (result.data.cufe) {
          lastSale.value.cufe = result.data.cufe
          lastSale.value.factus_number = result.data.factus_number
          lastSale.value.qr_code = result.data.qr_code
          lastSale.value.qr_image = result.data.qr_image
          lastSale.value.factus_status = result.data.factus_status
        }
        
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
    console.error('Error procesando venta en backend:', error)
    
    // Resetear estados de backend en caso de error
    backendProcessingPayment.value = false
    backendPaymentSuccess.value = false
    
    // � Detectar si es error de límite de facturas del plan gratuito
    if (error.response?.data?.error_code === 'FREE_PLAN_LIMIT_REACHED') {
      // Guardar datos del límite para mostrar en el modal
      freePlanLimitData.value = {
        invoiceCount: error.response.data.invoice_count || 10,
        maxInvoices: error.response.data.max_invoices || 10
      }
      
      // Cerrar modal de pago y mostrar modal de upgrade
      showPaymentModal.value = false
      showFreePlanLimitModal.value = true
      
      return // No continuar
    }
    
    // �MOSTRAR ERROR AL USUARIO Y NO CONTINUAR
    showError(`No se pudo crear la factura: ${error.message || 'Error desconocido'}`)
    
    // Cerrar modal de confirmación de pago
    showPaymentModal.value = false
    
    // NO limpiar el carrito, el usuario puede corregir e intentar de nuevo
    return // IMPORTANTE: detener ejecución aquí
  }
  
  // LIMPIAR CARRITO DESPUÉS DE PROCESAR EXITOSAMENTE EN BACKEND
  // Esto garantiza que el descuento se haya enviado antes de limpiarlo
  clearCart()
  
  // Si fue venta a crédito, refrescar clientes para actualizar available_credit
  if (selectedPaymentMethod.value === 'credit') {
    refreshData('customers')
  }
  
  // Señalar al modal que el backend terminó exitosamente (ahora puede mostrar estado success)
  backendProcessingPayment.value = false
  backendPaymentSuccess.value = true
  
  // El modal de confirmación ahora cambia internamente a estado 'success'
  // Ya no necesitamos abrir AfterPaymentModal
}

// Manejar cierre del modal de pago (resetear estados de sincronización)
const handleClosePaymentModal = () => {
  showPaymentModal.value = false
  backendProcessingPayment.value = false
  backendPaymentSuccess.value = false
}

const startNewSale = async () => {
  showReceiptModal.value = false
  showAfterPaymentModal.value = false
  
  // Resetear estados de sincronización del modal de pago
  backendProcessingPayment.value = false
  backendPaymentSuccess.value = false
  
  // Refrescar solo productos para obtener stock actualizado
  await refreshData('products')
  
  // El carrito ya se limpió después del pago confirmado
  // Solo restablecer estado para nueva venta
  // lastSale.value = null // Mantener lastSale para referencias futuras
  
  // Restablecer método de pago a efectivo por defecto
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
// Verificar si el usuario tiene plan básico (free_trial, free, basic)
const isBasicPlan = () => {
  const plan = (appStore.tenantPlan || 'free_trial').toLowerCase()
  return plan === 'free_trial' || plan === 'free' || plan === 'basic'
}

// Mostrar modal premium
const showPremiumFeaturePOS = (featureName) => {
  premiumFeatureNamePOS.value = featureName
  showPremiumModalPOS.value = true
}

// Cerrar modal premium
const closePremiumModalPOS = () => {
  showPremiumModalPOS.value = false
}

// Ir a selección de plan (desde modal de límite alcanzado)
const goToSelectPlan = () => {
  showFreePlanLimitModal.value = false
  // Navegar a configuración -> sección de planes
  navigateToModule('settings', { section: 'plans' })
}

const handlePrintInvoice = async () => {
  try {
    // Cerrar el modal post-pago
    showAfterPaymentModal.value = false
    
    if (!lastSale.value) {
      showWarning('No hay venta para imprimir')
      return
    }
    
    // Generar PDF con la plantilla usando los mismos datos que WhatsApp
    const invoiceData = {
      invoice_number: lastSale.value.invoiceNumber || lastSale.value.invoice_number || 'SIN-NUMERO',
      created_at: lastSale.value.date || lastSale.value.created_at || new Date(),
      customer_name: lastSale.value.customer || lastSale.value.customer_name || 'Cliente Final',
      cashier: lastSale.value.cashier || lastSale.value.seller_name || currentUser.value?.name || 'Vendedor',
      items: lastSale.value.items || [],
      subtotal: parseFloat(lastSale.value.subtotal || 0),
      discount: parseFloat(lastSale.value.discount || 0),
      tax: parseFloat(lastSale.value.tax || 0),
      surcharge_amount: parseFloat(lastSale.value.surcharge_amount || 0), // Recargo por crédito
      total: parseFloat(lastSale.value.total || 0),
      payments: lastSale.value.payments || [],
      change: parseFloat(lastSale.value.change || 0),
      notes: lastSale.value.notes || '',
      payment_method: lastSale.value.payment_method || '', // Para identificar si es crédito
      // FACTUS: Datos de facturación electrónica DIAN
      cufe: lastSale.value.cufe || null,
      factus_number: lastSale.value.factus_number || null,
      qr_code: lastSale.value.qr_code || null,
      qr_image: lastSale.value.qr_image || null,
      factus_status: lastSale.value.factus_status || null
    }

    // Generar PDF y abrir en ventana nueva inmediatamente
    const pdf = await generateInvoicePDFTemplate(invoiceData, systemSettings.value)
    const pdfBlob = pdf.output('blob')
    const pdfUrl = URL.createObjectURL(pdfBlob)
    
    // Abrir ventana nueva y activar impresión rápida
    const printWindow = window.open(pdfUrl, '_blank')
    if (printWindow) {
      printWindow.onload = () => {
        setTimeout(() => {
          printWindow.print()
        }, 250)
      }
    }
    
  } catch (error) {
    console.error('Error al imprimir factura:', error)
    showError('Error al imprimir la factura')
  }
}

const handleSendEmail = async () => {
  try {
    // PRIMERO: Verificar si tiene plan premium
    if (isBasicPlan()) {
      showPremiumFeaturePOS('Envío por Email')
      return
    }
    
    // Cerrar modales
    showAfterPaymentModal.value = false
    showReceiptModal.value = false
    isLoading.value = true
    
    if (!lastSale.value) {
      showWarning('No hay venta para enviar')
      isLoading.value = false
      return
    }
    
    // Buscar email del cliente desde lastSale
    let email = null
    
    // Intentar obtener email de diferentes fuentes
    if (lastSale.value.customer_email) {
      email = lastSale.value.customer_email
    } else if (lastSale.value.customer && lastSale.value.customer.customer_email) {
      email = lastSale.value.customer.customer_email
    } else if (lastSale.value.customer && lastSale.value.customer.email) {
      email = lastSale.value.customer.email
    } else if (selectedCustomer.value?.email) {
      email = selectedCustomer.value.email
    }
    
    // Si no hay email o no es válido, solicitarlo con modal profesional
    if (!email || !isValidEmail(email)) {
      email = await requestEmail()
      if (!email) {
        showWarning('Envío cancelado')
        isLoading.value = false
        return
      }
      if (!isValidEmail(email)) {
        showError('Email inválido. Use formato: ejemplo@correo.com')
        isLoading.value = false
        return
      }
    }
    
    showInfo('Enviando factura por correo...')
    
    // Generar PDF de la factura
    const pdfBlob = await generateInvoicePDF()
    
    // Enviar por email
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
    // PRIMERO: Verificar si tiene plan premium
    if (isBasicPlan()) {
      showPremiumFeaturePOS('Envío por WhatsApp')
      return
    }
    
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
    
    if (!pdfBlob) {
      throw new Error('No se pudo generar el PDF de la factura')
    }
    
    // Enviar por WhatsApp usando API
    await sendWhatsAppMessage(cleanPhone, pdfBlob)
    
    showSuccess(`Factura enviada por WhatsApp a ${cleanPhone}`)
    
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

const handleDownloadInvoice = async () => {
  try {
    isLoading.value = true
    
    // Generar PDF
    const pdfBlob = await generateInvoicePDF()
    
    if (!pdfBlob) {
      throw new Error('No se pudo generar el PDF de la factura')
    }
    
    // Crear enlace de descarga
    const url = window.URL.createObjectURL(pdfBlob)
    const link = document.createElement('a')
    link.href = url
    link.download = `Factura_${lastSale.value?.invoiceNumber || lastSale.value?.invoice_number || 'SIN-NUMERO'}.pdf`
    document.body.appendChild(link)
    link.click()
    document.body.removeChild(link)
    window.URL.revokeObjectURL(url)
    
    showSuccess('Factura descargada exitosamente')
    
  } catch (error) {
    console.error('Error descargando factura:', error)
    showError(error.message || 'Error descargando la factura')
  } finally {
    isLoading.value = false
  }
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
      // FIX: Usar el nombre del usuario autenticado si no viene en la venta
      cashier: lastSale.value.cashier || lastSale.value.seller_name || currentUser.value?.name || 'Vendedor',
      items: lastSale.value.items || [],
      subtotal: parseFloat(lastSale.value.subtotal || 0),
      discount: parseFloat(lastSale.value.discount || 0),
      tax: parseFloat(lastSale.value.tax || 0),
      total: parseFloat(lastSale.value.total || 0),
      payments: lastSale.value.payments || [],
      change: parseFloat(lastSale.value.change || 0),
      notes: lastSale.value.notes || '',
      // Recargo CrediTienda
      surcharge_amount: parseFloat(lastSale.value.surcharge_amount || lastSale.value.surcharge || 0),
      payment_method: lastSale.value.payment_method || lastSale.value.paymentMethod || ''
    }


    // Generar PDF usando plantilla centralizada (jsPDF vectorial)
    const pdf = await generateInvoicePDFTemplate(invoiceData, systemSettings.value)
    
    // Convertir a blob
    return getPDFBlob(pdf)

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
    const companyName = systemSettings.value?.company_name || 'Mi Empresa'
    const customerName = lastSale.value?.customer || selectedCustomer.value?.name || 'Cliente'
    const invoiceNumber = lastSale.value?.invoiceNumber || 'N/A'
    const totalAmount = (lastSale.value?.total || total.value).toLocaleString()
    const saleDate = new Date().toLocaleDateString('es-CO', {
      year: 'numeric',
      month: '2-digit', 
      day: '2-digit'
    })

    const message = `*${companyName}*

¡Hola ${customerName}!

Gracias por tu compra. Aquí tienes los detalles de tu factura:

*Factura:* ${invoiceNumber}
*Fecha:* ${saleDate}
*Total:* $${totalAmount}

Adjunto encontrarás el comprobante de tu compra en formato PDF.

¡Esperamos verte pronto! _Conserva este recibo para cualquier devolución o garantía._`

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
// Solicitar email mediante modal profesional
const requestEmail = () => {
  return new Promise((resolve) => {
    emailInput.value = ''
    emailModalResolve.value = resolve
    showEmailModal.value = true
  })
}

// Confirmar email del modal
const confirmEmail = () => {
  const email = emailInput.value.trim()
  if (!email) return
  
  showEmailModal.value = false
  if (emailModalResolve.value) {
    emailModalResolve.value(email)
    emailModalResolve.value = null
  }
}

// Cancelar email del modal
const cancelEmail = () => {
  showEmailModal.value = false
  if (emailModalResolve.value) {
    emailModalResolve.value(null)
    emailModalResolve.value = null
  }
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
    
    if (!result.success) {
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
      alert('La configuración del sistema requiere seleccionar un cliente específico para crear cotizaciones.\n\nNo se puede usar "Cliente Final".')
      showQuotationConfirmModal.value = false
      return
    }
    
    // Preparar items mapeados con product_name requerido
    const mappedItems = cart.items.map(item => ({
      product_id: item.id,
      variant_id: item.variant_id || null, // ID de variante si es producto fashion
      product_name: item.name, // Campo requerido por el backend
      quantity: item.quantity,
      unit_price: item.price,
      total_price: item.price * item.quantity
    }))
    
    
    // Preparar datos de la cotización
    const quoteData = {
      type: 'quote', // Campo requerido
      customer_id: selectedCustomer.value?.id || getConsumidorFinalId(), // Usar Consumidor Final si no hay seleccionado
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
      
      // Preparar datos para el modal de cotización exitosa - USAR DATOS COMPLETOS DEL BACKEND
      const modalQuotationData = {
        id: result.data.id, // ID de la cotización para WhatsApp
        code: realQuoteCode, // Código real del backend (ej: COT-000007)
        created_at: result.data.created_at || new Date(), // Fecha de creación del backend
        customer: selectedCustomer.value || { name: 'Cliente Final' }, // Objeto completo del cliente
        cashier: currentUser.value?.name || 'Vendedor', // Nombre del vendedor
        subtotal: result.data.subtotal || subtotal.value,
        discount_amount: result.data.discount_amount || 0,
        total: result.data.total || total.value,
        items: cart.items.map(item => ({
          product_name: item.name, // Campo requerido
          name: item.name,
          quantity: item.quantity,
          unit_price: item.price, // Campo requerido
          price: item.price,
          subtotal: item.price * item.quantity
        })),
        notes: result.data.notes || '',
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
    console.error('Error al crear cotización:', error)
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

// Toggle para búsqueda global de productos
const toggleGlobalSearch = async () => {
  globalSearch.value = !globalSearch.value
  
  const scope = globalSearch.value ? 'global' : 'local'
  const icon = globalSearch.value ? '' : ''
  const label = globalSearch.value ? 'GLOBAL' : 'LOCAL'
  
  // Recargar productos con el nuevo scope
  if (currentSession.value?.warehouse_id) {
    await appStore.loadProducts(currentSession.value.warehouse_id, scope)
    
    if (globalSearch.value) {
      showSuccess('Búsqueda global - Todas las tiendas')
    } else {
      showSuccess(`Búsqueda local - ${currentSession.value.warehouse?.name || 'Tienda actual'}`)
    }
  }
}

// Aplicar el filtro global al cargar productos inicialmente - OPTIMIZADO
watch(() => currentSession.value?.warehouse_id, async (warehouseId) => {
  if (warehouseId) {
    const scope = globalSearch.value ? 'global' : 'local'
    await appStore.loadProducts(warehouseId, scope)
  }
}, { immediate: false, flush: 'post' }) // Post flush para no bloquear UI

const focusFirstProduct = () => {
  if (filteredProducts.value.length > 0) {
    addToCart(filteredProducts.value[0])
    clearSearch()
  }
}

// Manejar foco del input de búsqueda (prevenir escritura cuando modal de clientes está abierto)
const handleSearchInputFocus = () => {
  if (showCustomerSelector.value) {
    // Si el modal de clientes está abierto, quitar el foco del input del POS
    if (searchInput.value) {
      searchInput.value.blur()
    }
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
        console.error('[PosView] Error técnico al buscar cotización:', error)
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
  
  // SIMULACIÓN DE LECTOR DE CÓDIGOS DE BARRAS
  // Si el usuario NO está en un input/textarea y presiona una tecla alfanumérica,
  // automáticamente enfocar el buscador y dejar que la tecla se escriba allí
  const isTypingInInput = event.target.tagName === 'INPUT' || 
                          event.target.tagName === 'TEXTAREA' || 
                          event.target.isContentEditable
  
  const isAlphanumeric = /^[a-zA-Z0-9]$/.test(event.key)
  
  // Si está escribiendo fuera de un input Y es una tecla alfanumérica
  // FIX: Ignorar si se presionan teclas modificadoras (Ctrl, Alt, Meta) para evitar capturar atajos como Ctrl+C, Ctrl+V
  if (!isTypingInInput && isAlphanumeric && searchInput.value && !event.ctrlKey && !event.altKey && !event.metaKey) {
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
          console.error('Cotización no encontrada o no es válida')
        }
      } else {
        console.error('No se pudo cargar la cotización:', result.message)
      }
    } catch (error) {
      console.error('Error al cargar cotización desde facturas:', error)
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
    // Check for forced-closed sessions FIRST (blocks POS until resolved)
    await checkForcedClosedSessions()

    // FIX: SIEMPRE forzar recarga de sesión al montar el componente
    // Esto evita condiciones de carrera y asegura datos frescos
    await appStore.loadCashSession(true) // force = true
    
    // Actualizar refs locales desde appStore
    hasOpenSession.value = appStore.cashSession.hasOpenSession
    currentSession.value = appStore.cashSession.current
    
    // AUTO-REFRESH FORZADO: Recargar productos SIEMPRE al entrar al POS
    const warehouseId = currentSession.value?.warehouse_id
    const scope = globalSearch.value ? 'global' : 'local'
    
    // Ejecutar cargas en paralelo para mayor velocidad
    // IMPORTANTE: Cargar productos incluso sin warehouse_id (usará el default)
    await Promise.all([
      appStore.loadProducts(warehouseId, scope, true), // force = true
      appStore.loadCustomers(true), // force = true
      appStore.loadPaymentMethods(true) // force = true
    ])

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
    
    // Cargar warehouses para validar si mostrar botón Global/Local
    await loadWarehouses()
    
    // Cargar configuración de loyalty points
    await loadLoyaltySettings()
    
    // Emitir warehouse actual al parent (si existe sesión)
    if (currentSession.value?.warehouse) {
      emit('warehouse-changed', currentSession.value.warehouse)
    }
    
    // Verificar si hay una cotización pendiente de cargar
    setTimeout(() => {
      loadQuotationFromInvoices()
    }, 500)
    
    // Cargar drafts existentes de la base de datos
    await loadSalesDrafts()
    
    // Cargar clientes con deuda para botón Abonos (no bloqueante)
    loadCustomersWithDebt()
    
    // Verificar productos con variantes para UI
    
    // Emitir estado inicial del carrito
    emit('cart-status-changed', cart.items.length > 0)
    
  } catch (error) {
    console.error('Error en inicialización de PosView:', error)
  }
})

// Listener para actualización de productos desde otros módulos
const handleProductsUpdate = async (event) => {
  // Recargar productos del store global
  if (currentSession.value?.warehouse_id) {
    const scope = globalSearch.value ? 'global' : 'local'
    await appStore.loadProducts(currentSession.value.warehouse_id, scope, true) // force = true
  }
}

// Registrar listener al montar
window.addEventListener('products-updated', handleProductsUpdate)

// AUTO-REFRESH al reactivar el componente (si se usa KeepAlive)
onActivated(async () => {
  try {
    // FIX: SIEMPRE recargar sesión al reactivar (forzar verificación)
    // Esto garantiza que si abriste/cerraste caja en otro módulo, se refleje aquí
    await appStore.loadCashSession(true) // force = true
    
    // Sincronizar variables locales
    currentSession.value = appStore.cashSession.current
    hasOpenSession.value = appStore.cashSession.hasOpenSession

    // 1️⃣ Recargar productos (respetando filtro Global/Local) - FORZADO
    if (currentSession.value?.warehouse_id) {
      const scope = globalSearch.value ? 'global' : 'local'
      await appStore.loadProducts(currentSession.value.warehouse_id, scope, true) // force = true
    }
    
    // 2️⃣ Recargar clientes (desde appStore global) - FORZADO
    await appStore.loadCustomers(true)
    
    // 3️⃣ Recargar métodos de pago (desde appStore global) - FORZADO
    await appStore.loadPaymentMethods(true)
    
    // 4️⃣ Recargar descuentos (local del POS)
    await loadDiscounts()
    
    // 5️⃣ Actualizar número de factura
    await fetchNextInvoiceNumber()
  } catch (error) {
    console.error('[PosView] Error en auto-refresh:', error)
    // No mostrar error al usuario - es refresh silencioso
  }
})

// Watcher para establecer efectivo por defecto cuando se carguen métodos de pago
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
  window.removeEventListener('products-updated', handleProductsUpdate)
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
    console.error('Error al cargar cotización:', error)
    alert('Error al cargar la cotización. Inténtelo de nuevo.')
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
      alert('Producto no encontrado en el catálogo')
      return
    }
    
    // Verificar que realmente hay stock disponible
    if (product.current_stock < 1) {
      alert('No hay stock disponible para este producto')
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
        alert(`Ya tienes la cantidad máxima disponible de ${product.name} en el carrito`)
        return
      }
    } else {
      // Agregar nuevo producto al carrito
      cart.items.push(cartItem)
    }
    
    alert(`Se agregó ${cartItem.quantity} unidad(es) de ${product.name} al carrito`)
    
  } catch (error) {
    console.error('Error al agregar producto con stock parcial:', error)
    alert('Error al agregar el producto. Inténtelo de nuevo.')
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
    const message = `*Cotización ${quotationData.code}*\n\n` +
                   `Fecha: ${new Date().toLocaleDateString('es-CO')}\n` +
                   `Cliente: ${customerName}\n` +
                   `Total: $${parseFloat(quotationData.total || 0).toLocaleString()}\n\n` +
                   `Puede usar este código para realizar su compra posteriormente.\n\n` +
                   `¡Gracias por elegirnos! `

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
      showSuccess(`Cotización ${quotationData.code} enviada a ${customerName} (${formattedPhone})`)
      
      // Cerrar modal después del envío exitoso
      showQuotationModal.value = false
    } else {
      throw new Error(result.message || 'Error al enviar cotización por WhatsApp')
    }

  } catch (error) {
    console.error('Error enviando cotización por WhatsApp:', error)
    alert(`Error al enviar cotización por WhatsApp\n\n${error.message}`)
  }
}

const handleSendQuotationEmail = async () => {
  try {
    // Verificar plan antes de procesar
    if (isBasicPlan()) {
      premiumFeatureNamePOS.value = 'Envío por Email'
      showPremiumModalPOS.value = true
      return
    }

    // Obtener datos de la cotización
    const quotationData = quotationModalData.value
    
    if (!quotationData || !quotationData.id) {
      throw new Error('No se encontraron datos de la cotización')
    }

    // Obtener datos del cliente
    const customer = quotationData.customer
    let customerName = 'Cliente General'
    let customerEmail = ''
    
    // Procesar datos del cliente
    if (typeof customer === 'object' && customer !== null) {
      customerName = customer.name || customer.customer_name || 'Cliente General'
      customerEmail = customer.email || customer.customer_email || ''
    } else if (typeof customer === 'string') {
      customerName = customer
    }

    // Intentar obtener email del cliente seleccionado
    if (!customerEmail && selectedCustomer.value) {
      customerEmail = selectedCustomer.value.email || selectedCustomer.value.customer_email || ''
      if (!customerName || customerName === 'Cliente General') {
        customerName = selectedCustomer.value.name || customerName
      }
    }

    // Si no hay email, solicitar al usuario
    if (!customerEmail) {
      const email = await requestEmail()
      if (!email) {
        return
      }
      customerEmail = email
    }

    // Validar email
    if (!customerEmail) {
      throw new Error('Email requerido')
    }

    showToast('Generando y enviando cotización por email...', 'info')

    // Generar el mismo PDF que se usa para imprimir
    const pdfBlob = await generateQuotationPDFBlob(quotationData)

    if (!pdfBlob) {
      throw new Error('Error generando PDF de cotización')
    }

    // Enviar email con la cotización (necesitarás implementar este servicio)
    await invoicesService.sendInvoiceEmail(
      quotationData.id,
      customerEmail,
      pdfBlob,
      true, // isQuote
      quotationData.code
    )

    showSuccess(`Cotización ${quotationData.code} enviada a ${customerEmail}`)
    
    // Cerrar modal después del envío exitoso
    showQuotationModal.value = false

  } catch (error) {
    console.error('Error enviando cotización por email:', error)
    showError(`Error al enviar cotización por email: ${error.message}`)
  }
}

// Función para generar e imprimir ticket de cotización
const printQuotationTicket = async () => {
  try {
    // Obtener datos de la cotización
    const quotationData = quotationModalData.value
    
    if (!quotationData) {
      throw new Error('No hay datos de cotización')
    }

    // Generar PDF usando la plantilla unificada (mismo que gestión facturas)
    const pdfBlob = await generateQuotationPDFBlob(quotationData)
    
    if (!pdfBlob) {
      throw new Error('Error generando PDF de cotización')
    }

    // Crear URL del blob
    const blobUrl = URL.createObjectURL(pdfBlob)
    
    // Abrir en nueva ventana con print dialog
    const printWindow = window.open(blobUrl, '_blank')
    
    if (printWindow) {
      printWindow.addEventListener('load', () => {
        // Pequeño delay para que cargue completamente
        setTimeout(() => {
          printWindow.print()
        }, 250)
      })
    } else {
      // Fallback: Descargar el PDF si no se puede abrir ventana
      const link = document.createElement('a')
      link.href = blobUrl
      link.download = `Cotizacion_${quotationData.code}.pdf`
      link.click()
      URL.revokeObjectURL(blobUrl)
    }
    
  } catch (error) {
    console.error('Error al imprimir cotización:', error)
    showError('Error al generar PDF de cotización')
  }
}

/**
 * Generar PDF Blob de cotización usando plantilla centralizada
 * SIEMPRE genera el mismo PDF (vectorial, alta calidad) para: imprimir, descargar, WhatsApp
 * USA EXACTAMENTE EL MISMO FORMATO QUE INVOICESVIEW
 */
const generateQuotationPDFBlob = async (quotationData) => {
  try {
    // Preparar datos de la cotización EXACTAMENTE como InvoicesView
    const quotationForTemplate = {
      quotation_number: quotationData.code,
      date: quotationData.created_at || new Date(),
      created_at: quotationData.created_at || new Date(), // Campo requerido
      customer_name: typeof quotationData.customer === 'object' && quotationData.customer !== null 
        ? (quotationData.customer.name || quotationData.customer.customer_name || 'Cliente General')
        : (typeof quotationData.customer === 'string' ? quotationData.customer : 'Cliente General'),
      customer: typeof quotationData.customer === 'object' && quotationData.customer !== null 
        ? (quotationData.customer.name || quotationData.customer.customer_name || 'Cliente General')
        : (typeof quotationData.customer === 'string' ? quotationData.customer : 'Cliente General'),
      cashier: currentUser.value?.name || 'Vendedor',
      items: quotationData.items || [],
      subtotal: parseFloat(quotationData.subtotal || quotationData.total || 0),
      discount: parseFloat(quotationData.discount_amount || 0),
      tax: parseFloat(tax.value || 0),
      tax_amount: parseFloat(tax.value || 0), // Ambos campos para compatibilidad
      total: parseFloat(quotationData.total || 0),
      notes: quotationData.notes || '',
      validity_days: 15
    }

    // Generar PDF usando plantilla centralizada (jsPDF vectorial)
    // Pasar systemSettings.value (el objeto real, no el computed)
    const pdf = await generateQuotationPDFTemplate(quotationForTemplate, appStore.systemSettings)
    
    // Convertir a blob
    return getPDFBlob(pdf)
    
  } catch (error) {
    console.error('Error al generar PDF Blob:', error)
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
      console.error('Elemento de video no encontrado')
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
    console.error('Error al iniciar scanner QR:', error)
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
    alert('Código QR no válido. Se esperaba un código de cotización (COT-XXXXXX)')
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
      
      // ARREGLO: Desactivar modo cotización cuando se abre una caja
      if (quotationMode.value) {
        quotationMode.value = false
        showSuccess('Modo cotización desactivado. Ahora puedes procesar ventas normalmente.')
      }
      
      // Actualizar estado local y sincronizar con appStore
      await loadCurrentSession()
      
      // Sincronizar nuestras variables locales con el composable
      currentSession.value = composableCurrentSession.value
      hasOpenSession.value = composableHasOpenSession.value
      
      // SINCRONIZAR CON APPSTORE (evitar verificaciones futuras)
      appStore.updateCashSession(currentSession.value, hasOpenSession.value)
      
      // RECARGAR PRODUCTOS DE LA BODEGA SELECCIONADA
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
      
      // SINCRONIZAR CON APPSTORE (evitar verificaciones futuras)
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
    console.error('Error cargando sesión:', error)
  }
  
  // Verificar nuevamente después de cargar
  if (!currentSession.value) {
    showError('No hay una sesión de caja abierta')
    return
  }
  
  showCashCloseModal.value = true
}

/**
 * Check for forced-closed sessions on POS load
 */
const checkForcedClosedSessions = async () => {
  try {
    const response = await cashSessionService.checkForcedClosed()
    if (response.success && response.has_forced_closed) {
      forcedCloseSession.value = response.session
      forcedCloseAuditAmount.value = ''
      forcedCloseNotes.value = ''
      forcedCloseError.value = ''
      showForcedCloseModal.value = true
    }
  } catch {
    // Silent — don't block POS for network errors
  }
}

/**
 * Submit the audit for a forced-closed session
 */
const submitForcedCloseAudit = async () => {
  const amount = parseFloat(forcedCloseAuditAmount.value)
  if (isNaN(amount) || amount < 0) {
    forcedCloseError.value = 'Ingresa un monto válido (mayor o igual a 0)'
    return
  }

  forcedCloseSubmitting.value = true
  forcedCloseError.value = ''
  forcedClosePasswordError.value = ''

  try {
    // Si es vendedor, verificar contraseña primero
    if (!isAdminUser.value) {
      if (!forcedClosePassword.value) {
        forcedClosePasswordError.value = 'Debes ingresar tu contraseña'
        forcedCloseSubmitting.value = false
        return
      }
      const verify = await authService.verifyMyPassword(forcedClosePassword.value)
      if (!verify.valid) {
        forcedClosePasswordError.value = verify.message || 'Contraseña incorrecta'
        forcedCloseSubmitting.value = false
        return
      }
    }

    const response = await cashSessionService.resolveForcedClose({
      session_id: forcedCloseSession.value.id,
      actual_amount: amount,
      closing_notes: forcedCloseNotes.value || null,
    })

    if (response.success) {
      showForcedCloseModal.value = false
      forcedCloseSession.value = null
      forcedClosePassword.value = ''
      forcedClosePasswordError.value = ''
      // Check if there are more forced-closed sessions
      await checkForcedClosedSessions()
    } else {
      forcedCloseError.value = response.message || 'Error al resolver el arqueo'
    }
  } catch (error) {
    forcedCloseError.value = error.response?.data?.message || 'Error de conexión'
  } finally {
    forcedCloseSubmitting.value = false
  }
}

const forcedCloseRealOpenDate = computed(() => {
  if (!forcedCloseSession.value) return '—'
  const s = forcedCloseSession.value
  // Use created_at as the real opening date when opened_at was corrupted (same as closed_at)
  const openedAt = s.opened_at ? new Date(s.opened_at).getTime() : null
  const closedAt = s.closed_at ? new Date(s.closed_at).getTime() : null
  const createdAt = s.created_at ? new Date(s.created_at).getTime() : null
  // If opened_at equals closed_at (or differs by less than 60s), prefer created_at
  const useCreatedAt = createdAt && openedAt && closedAt && Math.abs(openedAt - closedAt) < 60000
  const realDate = useCreatedAt ? new Date(s.created_at) : (s.opened_at ? new Date(s.opened_at) : null)
  if (!realDate) return '—'
  return realDate.toLocaleString('es-CO', { dateStyle: 'short', timeStyle: 'short' })
})

const forcedCloseDifference = computed(() => {
  if (!forcedCloseSession.value || !forcedCloseAuditAmount.value) return null
  const actual = parseFloat(forcedCloseAuditAmount.value)
  if (isNaN(actual)) return null
  return actual - (forcedCloseSession.value.expected_amount || 0)
})

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
    preloadInvoiceNumber.value = ''
    
    // Refrescar solo productos para actualizar inventario
    await refreshData('products')
    
  } catch (error) {
    console.error('Error al procesar el éxito de la devolución:', error)
    showError('Error al actualizar los datos después de la devolución')
  }
}

/**
 * Abrir modal de devoluciones con número de factura precargado
 */
const openReturnsModalWithInvoice = (invoiceNumber) => {
  preloadInvoiceNumber.value = invoiceNumber
  showReturnsModal.value = true
}

// ==================== FIN FUNCIONES DE DEVOLUCIONES ====================

// ==================== FUNCIONES DE ABONOS (PAGOS A CRÉDITO) ====================

const loadCustomersWithDebt = async () => {
  try {
    const customers = await customersService.getAll()
    const list = Array.isArray(customers) ? customers : (customers?.data || customers?.customers || [])
    customersWithDebt.value = list.filter(c => c.credit_active && parseFloat(c.current_debt || 0) > 0)
  } catch {
    customersWithDebt.value = []
  }
}

const openAbonoModal = () => {
  if (!hasOpenSession.value) {
    showError('Debes abrir caja antes de registrar abonos')
    return
  }
  abonoDocSearch.value = ''
  abonoSearchError.value = ''
  abonoFoundCustomer.value = null
  showAbonoSuggestions.value = false
  abonoForm.value = { amount: 0, payment_method: 'cash', reference: '', notes: '' }
  showAbonoModal.value = true
}

const closeAbonoModal = () => {
  showAbonoModal.value = false
}

const searchAbonoCustomer = async () => {
  const doc = abonoDocSearch.value.trim()
  if (!doc) return
  abonoSearching.value = true
  abonoSearchError.value = ''
  abonoFoundCustomer.value = null
  showAbonoSuggestions.value = false
  try {
    const found = await customersService.findByDocument(doc)
    if (!found) {
      abonoSearchError.value = 'No se encontró un cliente con ese documento'
      return
    }
    if (!found.credit_active || parseFloat(found.current_debt || 0) <= 0) {
      abonoSearchError.value = 'Este cliente no tiene deuda de crédito pendiente'
      return
    }
    abonoFoundCustomer.value = found
    abonoForm.value.amount = 0
  } catch {
    abonoSearchError.value = 'Error al buscar cliente'
  } finally {
    abonoSearching.value = false
  }
}

const clearAbonoSearch = () => {
  abonoDocSearch.value = ''
  abonoSearchError.value = ''
  abonoFoundCustomer.value = null
  showAbonoSuggestions.value = false
  abonoForm.value = { amount: 0, payment_method: 'cash', reference: '', notes: '' }
}

const selectAbonoSuggestion = (customer) => {
  showAbonoSuggestions.value = false
  if (!customer.credit_active || parseFloat(customer.current_debt || 0) <= 0) {
    abonoSearchError.value = 'Este cliente no tiene deuda de crédito pendiente'
    return
  }
  abonoDocSearch.value = customer.document_number || ''
  abonoFoundCustomer.value = customer
  abonoForm.value.amount = 0
  abonoSearchError.value = ''
}

const submitAbono = async () => {
  if (abonoSubmitting.value) return
  const cust = abonoFoundCustomer.value
  if (!cust) return showError('Busca un cliente por su documento primero')
  if (!abonoForm.value.amount || abonoForm.value.amount <= 0) return showError('Ingresa un monto válido')
  if (abonoAmountError.value) return showError(abonoAmountError.value)

  abonoSubmitting.value = true
  try {
    const response = await axiosInstance.post('/credit-payments', {
      customer_id: cust.id,
      amount: abonoForm.value.amount,
      method: abonoForm.value.payment_method,
      reference: abonoForm.value.reference || null,
      notes: abonoForm.value.notes || null,
      cash_session_id: currentSession.value?.id || null
    })
    const paymentData = response.data?.data || response.data || response
    closeAbonoModal()
    // Show receipt modal
    lastAbonoData.value = {
      id: paymentData.id,
      amount: abonoForm.value.amount,
      method: abonoForm.value.payment_method,
      reference: abonoForm.value.reference,
      notes: abonoForm.value.notes,
      customer: cust,
      date: new Date().toISOString(),
      remaining_debt: (response.data?.customer?.current_debt ?? parseFloat(cust.current_debt) - abonoForm.value.amount)
    }
    showAbonoReceipt.value = true
    await loadCustomersWithDebt()
  } catch (err) {
    const msg = err.response?.data?.message || 'Error al registrar el abono'
    showError(msg)
  } finally {
    abonoSubmitting.value = false
  }
}

// --- Funciones de recibo de abono ---
const generateAbonoReceiptText = () => {
  if (!lastAbonoData.value) return ''
  const d = lastAbonoData.value
  const companyName = systemSettings.value?.company_name || systemSettings.value?.business_name || 'Mi Empresa'
  const methodLabel = { cash: 'Efectivo', card: 'Tarjeta', transfer: 'Transferencia' }[d.method] || d.method
  const fecha = new Date(d.date).toLocaleDateString('es-CO', { year: 'numeric', month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' })

  let text = `*${companyName}*\n`
  text += `Comprobante de Abono a Crédito\n`
  text += `─────────────────────\n`
  text += `Cliente: ${d.customer?.name || 'Cliente'}\n`
  if (d.customer?.document_number) text += `Documento: ${d.customer.document_number}\n`
  text += `Fecha: ${fecha}\n`
  text += `─────────────────────\n`
  text += `*Monto abonado: $${Number(d.amount).toLocaleString('es-CO')}*\n`
  text += `Método: ${methodLabel}\n`
  if (d.reference) text += `Referencia: ${d.reference}\n`
  if (d.notes) text += `Notas: ${d.notes}\n`
  text += `─────────────────────\n`
  text += `Saldo pendiente: $${d.remaining_debt > 0 ? Number(d.remaining_debt).toLocaleString('es-CO') : '0 (Pagado)'}\n`
  if (d.id) text += `\n#${d.id}\n`
  text += `\n¡Gracias por tu pago! `
  return text
}

const handlePrintAbonoReceipt = () => {
  if (!lastAbonoData.value) return
  const d = lastAbonoData.value
  const companyName = systemSettings.value?.company_name || systemSettings.value?.business_name || 'Mi Empresa'
  const methodLabel = { cash: 'Efectivo', card: 'Tarjeta', transfer: 'Transferencia' }[d.method] || d.method
  const fecha = new Date(d.date).toLocaleDateString('es-CO', { year: 'numeric', month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' })

  const printWindow = window.open('', '_blank', 'width=350,height=600')
  if (!printWindow) { showError('No se pudo abrir la ventana de impresión'); return }

  printWindow.document.write(`<!DOCTYPE html><html><head><meta charset="utf-8"><title>Recibo de Abono</title>
    <style>
      * { margin: 0; padding: 0; box-sizing: border-box; }
      body { font-family: 'Courier New', monospace; font-size: 12px; padding: 10px; width: 280px; color: #000; }
      .center { text-align: center; }
      .bold { font-weight: bold; }
      .line { border-top: 1px dashed #000; margin: 8px 0; }
      .row { display: flex; justify-content: space-between; margin: 3px 0; }
      .big { font-size: 18px; font-weight: bold; }
      h2 { font-size: 14px; margin: 4px 0; }
      @media print { body { width: 72mm; padding: 2mm; } }
    </style></head><body>
    <div class="center">
      <h2 class="bold">${companyName}</h2>
      <p>Comprobante de Abono</p>
    </div>
    <div class="line"></div>
    <div class="row"><span>Cliente:</span><span class="bold">${d.customer?.name || 'Cliente'}</span></div>
    ${d.customer?.document_number ? `<div class="row"><span>Documento:</span><span>${d.customer.document_number}</span></div>` : ''}
    <div class="row"><span>Fecha:</span><span>${fecha}</span></div>
    <div class="line"></div>
    <div class="center" style="margin: 10px 0;">
      <p>Monto Abonado</p>
      <p class="big">$${Number(d.amount).toLocaleString('es-CO')}</p>
    </div>
    <div class="row"><span>Método:</span><span>${methodLabel}</span></div>
    ${d.reference ? `<div class="row"><span>Referencia:</span><span>${d.reference}</span></div>` : ''}
    ${d.notes ? `<div class="row"><span>Notas:</span><span>${d.notes}</span></div>` : ''}
    <div class="line"></div>
    <div class="row"><span>Saldo pendiente:</span><span class="bold">${d.remaining_debt > 0 ? '$' + Number(d.remaining_debt).toLocaleString('es-CO') : '$0 (Pagado)'}</span></div>
    <div class="line"></div>
    <div class="center" style="margin-top: 8px;">
      <p style="font-size: 10px;">${d.id ? '#' + d.id : ''}</p>
      <p style="font-size: 10px; margin-top: 4px;">Gracias por su pago</p>
    </div>
  </body></html>`)
  printWindow.document.close()
  printWindow.focus()
  setTimeout(() => { printWindow.print() }, 300)
}

const handleSendAbonoWhatsApp = async () => {
  if (!lastAbonoData.value) return
  const phone = lastAbonoData.value.customer?.phone || lastAbonoData.value.customer?.phone_number
  if (!phone) {
    showWarning('El cliente no tiene número de teléfono registrado')
    return
  }
  const message = generateAbonoReceiptText().replace(/\*/g, '')
  const cleanPhone = phone.replace(/\D/g, '')
  window.open(`https://wa.me/${cleanPhone}?text=${encodeURIComponent(message)}`, '_blank')
  showInfo('Abriendo WhatsApp...')
}

const handleSendAbonoEmail = async () => {
  if (!lastAbonoData.value) return
  let email = lastAbonoData.value.customer?.email || lastAbonoData.value.customer?.customer_email
  if (!email) {
    showWarning('El cliente no tiene correo electrónico registrado')
    return
  }
  try {
    const d = lastAbonoData.value
    const companyName = systemSettings.value?.company_name || systemSettings.value?.business_name || 'Mi Empresa'
    const methodLabel = { cash: 'Efectivo', card: 'Tarjeta', transfer: 'Transferencia' }[d.method] || d.method
    const fecha = new Date(d.date).toLocaleDateString('es-CO', { year: 'numeric', month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' })

    await axiosInstance.post('/send-abono-receipt', {
      email,
      customer_name: d.customer?.name,
      company_name: companyName,
      amount: d.amount,
      method: methodLabel,
      reference: d.reference,
      notes: d.notes,
      remaining_debt: d.remaining_debt,
      date: fecha,
      payment_id: d.id
    })
    showSuccess(`Comprobante enviado a ${email}`)
  } catch {
    const subject = encodeURIComponent(`Comprobante de Abono - ${systemSettings.value?.company_name || 'Mi Empresa'}`)
    const body = encodeURIComponent(generateAbonoReceiptText().replace(/\*/g, ''))
    window.open(`mailto:${email}?subject=${subject}&body=${body}`)
    showInfo('Abriendo cliente de correo...')
  }
}

// ==================== FIN FUNCIONES DE ABONOS ====================

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
    console.error('Error cargando pedido web:', error)
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
    console.error('Error creando cliente:', error)
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
  showReturnsModal,
  openReturnsModalWithInvoice
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

/* Animación de pulso sutil para botón COBRAR listo */
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

/* Clase para botón COBRAR listo - Pulso suave profesional */
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

/* Efecto de brillo que recorre el botón */
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

/* Animación de entrada para productos */
@keyframes fade-in-up {
  from {
    opacity: 0;
    transform: translateY(10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.animate-fade-in-up {
  animation: fade-in-up 0.4s ease-out forwards;
  opacity: 0;
}

/* Animación de carga de imagen */
@keyframes image-load {
  from {
    opacity: 0;
  }
  to {
    opacity: 1;
  }
}

.animate-image-load {
  animation: image-load 0.3s ease-out forwards;
}
</style>