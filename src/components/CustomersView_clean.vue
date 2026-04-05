<template>
  <!-- Layout Full Height estilo WhatsApp Web - Sin doble scroll -->
  <div class="h-full font-sans bg-gray-50 dark:bg-[#131314] transition-colors duration-300 overflow-hidden flex flex-col">
    <div class="flex-none px-4 lg:px-6 pt-4 pb-2.5 space-y-3 animate-fade-in">
      
      <!-- NIVEL 1: Header Minimalista -->
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-2xl font-black text-gray-900 dark:text-white tracking-tight">Clientes</h1>
          <p class="text-sm text-gray-500 dark:text-zinc-400 mt-0.5">Gestiona tu base de clientes y su historial</p>
        </div>
        
        <div class="flex items-center gap-2">
          <button @click="fetchCustomers" 
                  class="px-5 py-2.5 bg-white dark:bg-zinc-900 hover:bg-gray-50 dark:hover:bg-zinc-800 text-gray-700 dark:text-zinc-200 text-[13px] font-semibold rounded-md border border-gray-200 dark:border-zinc-800 shadow-sm transition-all duration-200 flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
            </svg>
            <span>Refrescar</span>
          </button>
          
          <button @click="openCreateModal" 
                  class="px-6 py-2.5 bg-gray-900 dark:bg-white hover:bg-gray-800 dark:hover:bg-gray-100 text-white dark:text-gray-900 text-[13px] font-semibold rounded-md  transition-all duration-300 flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            <span>Nuevo Cliente</span>
          </button>
        </div>
      </div>
    </div>
    
    <!-- Master-Detail Layout WhatsApp Web Style - Ocupa todo el espacio restante -->
    <div class="flex-1 mx-3 lg:mx-4 mb-3 rounded-md overflow-hidden border border-gray-200 dark:border-zinc-800 shadow-sm transition-colors duration-300">
      <div class="grid grid-cols-1 lg:grid-cols-10 h-full">
        
        <!-- PANEL IZQUIERDO: Lista de Clientes (30%) -->
        <div class="lg:col-span-3 overflow-hidden flex flex-col bg-white dark:bg-zinc-900 border-r border-gray-200 dark:border-zinc-800 transition-colors duration-300">
          
          <!-- Toolbar: búsqueda y filtros -->
          <div class="px-4 pt-5 pb-4 bg-white dark:bg-zinc-900 space-y-3">
            <!-- Búsqueda -->
            <div class="relative">
              <svg class="absolute left-3.5 top-1/2 transform -translate-y-1/2 w-4 h-4 text-gray-400 dark:text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
              </svg>
              <input
                v-model="searchTerm"
                type="text"
                placeholder="Buscar cliente..."
                class="w-full pl-10 pr-4 py-3 text-sm rounded-xl bg-gray-50 dark:bg-zinc-800 border-2 border-gray-200 dark:border-zinc-700 text-gray-900 dark:text-zinc-200 placeholder-gray-400 dark:placeholder-zinc-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent shadow-sm transition-all duration-200">
            </div>
            <!-- Filtro -->
            <div class="flex items-center gap-2">
              <select
                v-model="statusFilter"
                class="flex-1 px-3 py-2.5 text-[13px] rounded-xl bg-gray-50 dark:bg-zinc-800 border border-gray-200 dark:border-zinc-700 text-gray-700 dark:text-zinc-300 font-semibold focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 shadow-sm transition-colors duration-200 cursor-pointer">
                <option value="">Estado</option>
                <option value="active">Activos</option>
                <option value="inactive">Inactivos</option>
              </select>
            </div>
          </div>

          <!-- Separador sutil -->
          <div class="h-px bg-gray-200 dark:bg-zinc-800 mx-4"></div>
          
          <!-- Lista con scroll independiente - Fondo diferenciado -->
          <div class="flex-1 overflow-y-auto bg-white dark:bg-zinc-900 px-2 pt-2 pb-2">
            
            <!-- Loading state -->
            <div v-if="loading" class="flex items-center justify-center py-12">
              <div class="w-6 h-6 border-2 border-gray-300 dark:border-zinc-600 border-t-gray-600 dark:border-t-zinc-300 rounded-full animate-spin"></div>
              <span class="ml-3 text-xs text-gray-500 dark:text-zinc-500">Cargando...</span>
            </div>
            
            <!-- Empty state -->
            <div v-else-if="filteredCustomers.length === 0" class="flex flex-col items-center justify-center py-8 text-center">
              <svg class="w-8 h-8 text-gray-300 dark:text-zinc-600 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
              </svg>
              <p class="text-xs font-medium text-gray-600 dark:text-zinc-300">Sin resultados</p>
              <p class="text-xs text-gray-500 dark:text-zinc-500 mt-1">Intenta con otros filtros</p>
            </div>
            
            <!-- Lista de clientes -->
            <div
              v-else
              v-for="customer in filteredCustomers"
              :key="customer.id"
              @click="selectCustomer(customer)"
              class="px-3 py-2.5 mb-0.5 cursor-pointer transition-all duration-150 rounded-lg group relative"
              :class="[
                selectedCustomer?.id === customer.id 
                  ? 'bg-emerald-50 dark:bg-emerald-950/30 border-l-4 border-emerald-600 dark:border-emerald-500' 
                  : 'hover:bg-gray-50 dark:hover:bg-zinc-800/40 border-l-4 border-transparent'
              ]"
            >
              <!-- Borde izquierdo de selección (ya inline via border-l-4) -->
              
              <div class="flex items-center gap-3">
                <!-- Avatar con foto o inicial -->
                <div 
                  class="w-11 h-11 rounded-full flex items-center justify-center flex-shrink-0 overflow-hidden transition-transform duration-200"
                  :class="{ 'cursor-pointer hover:scale-110 hover:ring-2 hover:ring-blue-600 dark:hover:ring-blue-400': customer.credit_photo }"
                  :style="{ backgroundColor: customer.credit_photo ? 'transparent' : getCustomerColor(customer.name) + '20', color: getCustomerColor(customer.name) }"
                  @click.stop="customer.credit_photo && openPhotoPreview(customer.credit_photo, customer.name)">
                  <img v-if="customer.credit_photo" 
                       :src="customer.credit_photo" 
                       :alt="customer.name"
                       class="w-full h-full object-cover"
                       @error="$event.target.style.display='none'; $event.target.parentElement.innerHTML=`<span class='text-sm font-medium'>${customer.name.charAt(0).toUpperCase()}</span>`">
                  <span v-else class="text-sm font-medium">{{ customer.name.charAt(0).toUpperCase() }}</span>
                </div>
                
                <!-- Info del cliente -->
                <div class="flex-1 min-w-0">
                  <div class="flex items-center gap-2">
                    <p class="text-sm font-bold text-gray-900 dark:text-zinc-100 truncate leading-tight">
                      {{ customer.name }}
                    </p>
                    <span class="text-[9px] font-bold px-1.5 py-[2px] rounded flex-shrink-0 uppercase tracking-wider leading-none border"
                          :class="customer.active ? 'bg-emerald-50 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-400 border-emerald-200 dark:border-emerald-800' : 'bg-rose-50 dark:bg-rose-950 text-rose-700 dark:text-rose-400 border-rose-200 dark:border-rose-800'">>
                      {{ customer.active ? 'ACTIVO' : 'INACTIVO' }}
                    </span>
                  </div>
                  <div class="flex items-center gap-2 mt-1">
                    <p class="text-xs text-gray-600 dark:text-zinc-400 truncate font-medium">
                      {{ customer.document_type }}: {{ customer.document_number }}
                    </p>
                  </div>
                </div>
                
                <!-- Compras a la derecha -->
                <div class="text-right flex-shrink-0">
                  <span class="text-sm font-extrabold text-gray-900 dark:text-white tabular-nums leading-tight">
                    ${{ formatCurrency(customer.total_purchases || 0) }}
                  </span>
                  <p class="text-[10px] text-gray-400 dark:text-zinc-500">Compras</p>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- PANEL DERECHO: Detalle del Cliente (70%) -->
        <div class="lg:col-span-7 overflow-hidden flex flex-col bg-[#fafbfc] dark:bg-[#1a1a1d] transition-colors duration-300">
          
          <!-- Estado: No seleccionado - Empty State estilo WhatsApp Web -->
          <div v-if="!selectedCustomer" class="flex-1 flex flex-col items-center justify-center p-8 lg:p-12 text-center bg-[#fafbfc] dark:bg-[#1a1a1d] relative overflow-y-auto">
            
            <!-- Ilustración SVG profesional tipo documento de cliente -->
            <div class="mb-6 relative">
              <!-- Efecto glow suave de fondo -->
              <div class="absolute inset-0 bg-gradient-to-br from-blue-200/20 via-transparent to-purple-200/20 dark:from-blue-500/5 dark:to-purple-500/5 rounded-3xl blur-3xl scale-150"></div>
              
              <!-- Ilustración principal -->
              <svg class="w-36 h-36 relative z-10" viewBox="0 0 180 180" fill="none" xmlns="http://www.w3.org/2000/svg">
                <!-- Sombra del documento -->
                <rect x="48" y="38" width="88" height="110" rx="6" class="fill-gray-200/50 dark:fill-zinc-700/30"/>
                
                <!-- Documento principal -->
                <rect x="44" y="32" width="88" height="110" rx="6" class="fill-white dark:fill-zinc-800" stroke-width="0"/>
                <rect x="44" y="32" width="88" height="110" rx="6" class="fill-none stroke-gray-200 dark:stroke-zinc-700" stroke-width="1.5"/>
                
                <!-- Avatar circular del cliente -->
                <circle cx="88" cy="58" r="14" class="fill-gray-100 dark:fill-zinc-700"/>
                <circle cx="88" cy="55" r="5" class="fill-gray-300 dark:fill-zinc-500"/>
                <path d="M80 65 C80 60, 84 58, 88 58 C92 58, 96 60, 96 65" class="fill-gray-300 dark:fill-zinc-500"/>
                
                <!-- Líneas de información -->
                <rect x="54" y="80" width="68" height="3" rx="1.5" class="fill-gray-100 dark:fill-zinc-700"/>
                <rect x="54" y="88" width="55" height="3" rx="1.5" class="fill-gray-100 dark:fill-zinc-700"/>
                <rect x="54" y="96" width="62" height="3" rx="1.5" class="fill-gray-100 dark:fill-zinc-700"/>
                
                <!-- Línea separadora -->
                <line x1="54" y1="106" x2="122" y2="106" class="stroke-gray-200 dark:stroke-zinc-700" stroke-width="1"/>
                
                <!-- Badge de estado -->
                <rect x="54" y="114" width="35" height="8" rx="4" class="fill-emerald-100 dark:fill-emerald-500/20"/>
                <rect x="58" y="117" width="27" height="2" rx="1" class="fill-emerald-500 dark:fill-emerald-400"/>
                
                <!-- Total de compras -->
                <rect x="94" y="114" width="28" height="8" rx="4" class="fill-blue-100 dark:fill-blue-500/20"/>
                <rect x="98" y="117" width="20" height="2" rx="1" class="fill-blue-500 dark:fill-blue-400"/>
                
                <!-- Icono de usuario verificado -->
                <circle cx="120" cy="48" r="14" class="fill-blue-100 dark:fill-blue-500/20"/>
                <circle cx="120" cy="48" r="10" class="fill-blue-500 dark:fill-blue-400"/>
                <path d="M116 48L118.5 50.5L125 44" class="stroke-white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                
                <!-- Tarjeta de crédito pequeña -->
                <rect x="138" y="100" width="28" height="20" rx="3" class="fill-purple-100 dark:fill-purple-500/20"/>
                <rect x="142" y="106" width="12" height="2" rx="1" class="fill-purple-400 dark:fill-purple-400"/>
                <rect x="142" y="112" width="20" height="2" rx="1" class="fill-purple-300 dark:fill-purple-500"/>
              </svg>
            </div>
            
            <!-- Texto de bienvenida profesional -->
            <div class="relative z-10 max-w-sm mb-8">
              <h3 class="text-2xl font-black text-gray-900 dark:text-white mb-2">
                Gestión de Clientes
              </h3>
              <p class="text-sm text-gray-600 dark:text-zinc-400 leading-relaxed">
                Selecciona un cliente del panel izquierdo para visualizar su información completa, historial de compras y puntos de fidelidad.
              </p>
            </div>

            <!-- Hint inferior -->
            <div class="relative z-10 mt-8">
              <p class="text-[11px] text-gray-500 dark:text-zinc-500 flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Haz clic en cualquier cliente de la lista para comenzar
              </p>
            </div>
          </div>

          <!-- Estado: Cliente Seleccionado - Vista de Detalle -->
          <div v-else class="flex-1 flex flex-col overflow-hidden">
            
            <!-- Header del cliente seleccionado -->
            <div class="bg-white dark:bg-zinc-900 border-b border-gray-200 dark:border-zinc-800 p-5 flex-shrink-0">
              <div class="flex items-start justify-between">
                <!-- Info del cliente -->
                <div class="flex items-center gap-4">
                  <!-- Avatar grande con foto o inicial -->
                  <div 
                    class="w-14 h-14 rounded-xl flex items-center justify-center text-xl font-medium overflow-hidden transition-all duration-200"
                    :class="{ 'cursor-pointer hover:scale-105': selectedCustomer.credit_photo }"
                    :style="{ backgroundColor: selectedCustomer.credit_photo ? 'transparent' : getCustomerColor(selectedCustomer.name) + '20', color: getCustomerColor(selectedCustomer.name) }"
                    @click="selectedCustomer.credit_photo && openPhotoPreview(selectedCustomer.credit_photo, selectedCustomer.name)">
                    <img v-if="selectedCustomer.credit_photo" 
                         :src="selectedCustomer.credit_photo" 
                         :alt="selectedCustomer.name"
                         class="w-full h-full object-cover">
                    <span v-else class="text-xl">{{ selectedCustomer.name.charAt(0).toUpperCase() }}</span>
                  </div>
                  
                  <div>
                    <h2 class="text-lg font-semibold text-gray-800 dark:text-white">
                      {{ selectedCustomer.name }}
                    </h2>
                    <div class="flex items-center gap-3 mt-1">
                      <span class="flex items-center gap-1 text-sm text-gray-500 dark:text-zinc-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                        </svg>
                        {{ selectedCustomer.phone || 'Sin teléfono' }}
                      </span>
                      <span v-if="selectedCustomer.email" class="flex items-center gap-1 text-sm text-gray-500 dark:text-zinc-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                        {{ selectedCustomer.email }}
                      </span>
                    </div>
                  </div>
                </div>
                
                <!-- Botones de Acción -->
                <div class="flex items-center gap-2">
                  <button
                    @click="editCustomer(selectedCustomer)"
                    class="px-4 py-2 rounded-full text-[13px] font-medium transition-all duration-200 flex items-center gap-2 bg-gray-50 dark:bg-zinc-800 hover:bg-gray-100 dark:hover:bg-zinc-700 text-gray-700 dark:text-zinc-300 border-none"
                  >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    Editar
                  </button>
                  
                  <!-- No mostrar botón eliminar para Consumidor Final (NIT DIAN) -->
                  <button
                    v-if="selectedCustomer?.document_number !== '222222222222'"
                    @click="deleteCustomer(selectedCustomer)"
                    class="px-4 py-2 rounded-full text-[13px] font-medium transition-all duration-200 flex items-center gap-2 bg-gray-50 dark:bg-zinc-800 hover:bg-rose-50 dark:hover:bg-rose-950 text-gray-700 dark:text-zinc-300 hover:text-rose-600 dark:hover:text-rose-400 border-none"
                  >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                    Eliminar
                  </button>
                </div>
              </div>

              <!-- Tabs de Navegación -->
              <div class="flex items-center gap-6 mt-6 border-b border-gray-200 dark:border-zinc-800">
                <button 
                  @click="activeTab = 'info'"
                  class="pb-3 text-[13px] font-medium border-b-2 transition-all duration-200"
                  :class="activeTab === 'info' ? 'text-gray-900 dark:text-white border-gray-900 dark:border-white' : 'text-gray-500 dark:text-zinc-400 border-transparent hover:text-gray-900 dark:hover:text-white'">
                  Información General
                </button>
                <button 
                  @click="activeTab = 'history'"
                  class="pb-3 text-sm font-medium border-b-2 transition-all duration-200"
                  :class="activeTab === 'history' ? 'text-blue-600 dark:text-blue-400 border-blue-600 dark:border-blue-400' : 'text-gray-600 dark:text-zinc-400 border-transparent hover:text-gray-900 dark:hover:text-white'">
                  Historial de Compras
                </button>
                <button 
                  v-if="isLoyaltyEnabled"
                  @click="activeTab = 'loyalty'"
                  class="pb-3 text-sm font-medium border-b-2 transition-all duration-200"
                  :class="activeTab === 'loyalty' ? 'text-amber-500 dark:text-amber-300 border-amber-500 dark:border-amber-300' : 'text-gray-600 dark:text-zinc-400 border-transparent hover:text-gray-900 dark:hover:text-white'">
                  Puntos de Fidelidad
                </button>
              </div>
            </div>

            <!-- Contenido de Tabs -->
            <div class="flex-1 overflow-y-auto p-6">
              
              <!-- TAB 1: Información General -->
              <div v-if="activeTab === 'info'" class="space-y-6 animate-fade-in">
                <!-- Stats Rápidos - Calculados desde facturas reales -->
                <div class="grid grid-cols-3 gap-4">
                  <div class="bg-white dark:bg-zinc-800 p-4 rounded-xl border border-gray-200 dark:border-zinc-700">
                    <p class="text-xs text-gray-600 dark:text-zinc-400 uppercase tracking-wide font-medium">Total Compras</p>
                    <p class="text-xl font-medium text-gray-900 dark:text-white mt-1">${{ formatCurrency(customerHistorySummary.totalSpent) }}</p>
                  </div>
                  <div class="bg-white dark:bg-zinc-800 p-4 rounded-xl border border-gray-200 dark:border-zinc-700">
                    <p class="text-xs text-gray-600 dark:text-zinc-400 uppercase tracking-wide font-medium">Total Órdenes</p>
                    <p class="text-xl font-medium text-gray-900 dark:text-white mt-1">{{ customerInvoices.length }}</p>
                  </div>
                  <div class="bg-white dark:bg-zinc-800 p-4 rounded-xl border border-gray-200 dark:border-zinc-700">
                    <p class="text-xs text-gray-600 dark:text-zinc-400 uppercase tracking-wide font-medium">Ticket Promedio</p>
                    <p class="text-xl font-medium text-gray-900 dark:text-white mt-1">
                      ${{ formatCurrency(customerHistorySummary.average) }}
                    </p>
                  </div>
                </div>

                <!-- Datos Detallados -->
                <div class="bg-white dark:bg-zinc-800 rounded-xl border border-gray-200 dark:border-zinc-700 overflow-hidden">
                  <div class="px-6 py-4 border-b border-gray-200 dark:border-zinc-700 bg-gray-50 dark:bg-zinc-800">
                    <h3 class="font-medium text-gray-900 dark:text-white">Datos Personales</h3>
                  </div>
                  <div class="p-6 grid grid-cols-2 gap-6">
                    <div>
                      <p class="text-xs text-gray-600 dark:text-zinc-400 uppercase tracking-wide font-medium mb-1">Documento</p>
                      <p class="text-sm font-medium text-gray-900 dark:text-white">{{ selectedCustomer.document_type }} {{ selectedCustomer.document_number }}</p>
                    </div>
                    <div>
                      <p class="text-xs text-gray-600 dark:text-zinc-400 uppercase tracking-wide font-medium mb-1">Ciudad</p>
                      <p class="text-sm font-medium text-gray-900 dark:text-white">{{ selectedCustomer.city || 'No registrada' }}</p>
                    </div>
                    <div>
                      <p class="text-xs text-gray-600 dark:text-zinc-400 uppercase tracking-wide font-medium mb-1">Dirección</p>
                      <p class="text-sm font-medium text-gray-900 dark:text-white">{{ selectedCustomer.address || 'No registrada' }}</p>
                    </div>
                    <div>
                      <p class="text-xs text-gray-600 dark:text-zinc-400 uppercase tracking-wide font-medium mb-1">Fecha Nacimiento</p>
                      <p class="text-sm font-medium text-gray-900 dark:text-white">{{ selectedCustomer.birth_date || 'No registrada' }}</p>
                    </div>
                  </div>
                </div>

                <!-- Sección Crédito (si aplica) -->
                <div v-if="isCreditiendaEnabled" class="bg-white dark:bg-zinc-800 rounded-xl border border-gray-200 dark:border-zinc-700 overflow-hidden">
                  <div class="px-6 py-4 border-b border-gray-200 dark:border-zinc-700 bg-gray-50 dark:bg-zinc-800 flex justify-between items-center">
                    <h3 class="font-medium text-gray-900 dark:text-white">Estado de Crédito</h3>
                    <span :class="selectedCustomer.credit_active ? 'bg-emerald-50 dark:bg-emerald-900/20 text-emerald-600 dark:text-emerald-400' : 'bg-gray-100 dark:bg-zinc-700 text-gray-600 dark:text-zinc-400'" class="px-2 py-1 rounded-full text-xs font-medium uppercase">
                      {{ selectedCustomer.credit_active ? 'Habilitado' : 'Deshabilitado' }}
                    </span>
                  </div>
                  <div class="p-6 grid grid-cols-2 gap-6">
                    <div>
                      <p class="text-xs text-gray-600 dark:text-zinc-400 uppercase tracking-wide font-medium mb-1">Cupo Total</p>
                      <p class="text-lg font-medium text-gray-900 dark:text-white">${{ formatCurrency(selectedCustomer.credit_limit) }}</p>
                    </div>
                    <div>
                      <p class="text-xs text-gray-600 dark:text-zinc-400 uppercase tracking-wide font-medium mb-1">Deuda Actual</p>
                      <p class="text-lg font-medium" :class="selectedCustomer.current_debt > 0 ? 'text-rose-600 dark:text-rose-400' : 'text-emerald-600 dark:text-emerald-400'">
                        ${{ formatCurrency(selectedCustomer.current_debt) }}
                      </p>
                    </div>
                    <div class="col-span-2">
                      <div class="w-full bg-gray-200 dark:bg-zinc-700 rounded-full h-2.5">
                        <div class="bg-blue-600 dark:bg-blue-400 h-2.5 rounded-full" :style="{ width: Math.min((selectedCustomer.current_debt / selectedCustomer.credit_limit) * 100, 100) + '%' }"></div>
                      </div>
                      <p class="text-xs text-right mt-1 text-gray-600 dark:text-zinc-400">
                        {{ Math.round((selectedCustomer.current_debt / selectedCustomer.credit_limit) * 100) || 0 }}% utilizado
                      </p>
                    </div>
                  </div>
                </div>
              </div>

              <!-- TAB 2: Historial -->
              <div v-if="activeTab === 'history'" class="animate-fade-in h-full flex flex-col">
                <!-- Loading State -->
                <div v-if="historyLoading" class="flex-1 flex items-center justify-center">
                  <div class="text-center">
                    <div class="animate-spin rounded-full h-10 w-10 border-b-2 border-blue-600 mx-auto mb-3"></div>
                    <p class="text-sm text-gray-600 dark:text-zinc-400">Cargando historial...</p>
                  </div>
                </div>

                <!-- Sin facturas -->
                <div v-else-if="customerInvoices.length === 0" class="flex-1 flex items-center justify-center">
                  <div class="text-center text-gray-600 dark:text-zinc-400">
                    <svg class="w-12 h-12 mx-auto mb-3 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <p class="text-sm font-medium">No hay compras registradas</p>
                  </div>
                </div>

                <!-- Historial Completo -->
                <div v-else class="flex flex-col h-full space-y-4">
                  <!-- Resumen rápido -->
                  <div class="grid grid-cols-3 gap-3">
                    <div class="bg-blue-50 dark:bg-blue-900/15 rounded-xl p-3 border border-blue-200 dark:border-blue-600/30">
                      <p class="text-xs text-blue-600 dark:text-blue-400 font-medium">Facturas</p>
                      <p class="text-lg font-medium text-gray-900 dark:text-white">{{ customerInvoices.length }}</p>
                    </div>
                    <div class="bg-emerald-50 dark:bg-emerald-900/15 rounded-xl p-3 border border-emerald-200 dark:border-emerald-800/30">
                      <p class="text-xs text-emerald-600 dark:text-emerald-400 font-medium">Total Gastado</p>
                      <p class="text-lg font-medium text-gray-900 dark:text-white">${{ formatCurrency(customerHistorySummary.totalSpent) }}</p>
                    </div>
                    <div class="bg-purple-50 dark:bg-purple-900/15 rounded-xl p-3 border border-purple-200 dark:border-purple-800/30">
                      <p class="text-xs text-purple-600 dark:text-purple-400 font-medium">Promedio</p>
                      <p class="text-lg font-medium text-gray-900 dark:text-white">${{ formatCurrency(customerHistorySummary.average) }}</p>
                    </div>
                  </div>

                  <!-- Tabla de Facturas -->
                  <div class="flex-1 overflow-hidden bg-white dark:bg-zinc-800 rounded-xl border border-gray-200 dark:border-zinc-700">
                    <div class="overflow-y-auto h-full">
                      <table class="w-full">
                        <thead class="bg-gray-50 dark:bg-zinc-800 sticky top-0">
                          <tr>
                            <th class="px-4 py-2.5 text-left text-xs font-medium text-gray-600 dark:text-zinc-400 uppercase">Factura</th>
                            <th class="px-4 py-2.5 text-left text-xs font-medium text-gray-600 dark:text-zinc-400 uppercase">Fecha</th>
                            <th class="px-4 py-2.5 text-left text-xs font-medium text-gray-600 dark:text-zinc-400 uppercase">Productos</th>
                            <th class="px-4 py-2.5 text-right text-xs font-medium text-gray-600 dark:text-zinc-400 uppercase">Total</th>
                            <th class="px-4 py-2.5 text-center text-xs font-medium text-gray-600 dark:text-zinc-400 uppercase">Estado</th>
                            <th class="px-4 py-2.5 text-center text-xs font-medium text-gray-600 dark:text-zinc-400 uppercase">Acciones</th>
                          </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-zinc-700">
                          <tr v-for="invoice in customerInvoices" :key="invoice.id" class="hover:bg-gray-100 dark:hover:bg-zinc-700 transition-colors">
                            <td class="px-4 py-3">
                              <span class="text-sm font-medium text-gray-900 dark:text-white">{{ invoice.custom_number || invoice.number || `#${invoice.id}` }}</span>
                            </td>
                            <td class="px-4 py-3">
                              <span class="text-sm text-gray-600 dark:text-zinc-400">{{ formatInvoiceDate(invoice.date) }}</span>
                            </td>
                            <td class="px-4 py-3">
                              <span class="text-sm text-gray-900 dark:text-white">{{ invoice.items?.length || 0 }} artículos</span>
                            </td>
                            <td class="px-4 py-3 text-right">
                              <span class="text-sm font-medium text-gray-900 dark:text-white">${{ formatCurrency(invoice.total) }}</span>
                            </td>
                            <td class="px-4 py-3 text-center">
                              <span :class="invoice.status === 'paid' 
                                ? 'bg-emerald-50 dark:bg-emerald-900/20 text-emerald-600 dark:text-emerald-400 border-emerald-200 dark:border-emerald-800/30' 
                                : 'bg-amber-50 dark:bg-amber-500/20 text-amber-600 dark:text-amber-300 border-amber-300 dark:border-amber-700/30'"
                                class="px-2 py-0.5 rounded-full text-[10px] font-medium border uppercase">
                                {{ invoice.status === 'paid' ? 'Pagada' : 'Pendiente' }}
                              </span>
                            </td>
                            <td class="px-4 py-3 text-center">
                              <button @click="viewInvoiceDetails(invoice)" 
                                class="p-1.5 text-gray-600 dark:text-zinc-400 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-md transition-all"
                                title="Ver detalles">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                              </button>
                            </td>
                          </tr>
                        </tbody>
                      </table>
                    </div>
                  </div>
                </div>
              </div>

              <!-- TAB 3: Fidelidad -->
              <div v-if="activeTab === 'loyalty'" class="animate-fade-in space-y-6">
                <div class="bg-gradient-to-br from-amber-500 to-amber-600 rounded-2xl p-6 text-white relative overflow-hidden">
                  <div class="absolute top-0 right-0 -mt-4 -mr-4 w-32 h-32 bg-white opacity-10 rounded-full blur-2xl"></div>
                  
                  <div class="relative z-10">
                    <div class="flex justify-between items-start">
                      <div>
                        <p class="text-amber-100 font-medium uppercase tracking-wider text-sm">Puntos Disponibles</p>
                        <h3 class="text-4xl font-medium mt-1">{{ selectedCustomer.loyalty_points || 0 }}</h3>
                      </div>
                      <div class="bg-white/20 p-3 rounded-2xl ">
                        <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 20 20">
                          <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                        </svg>
                      </div>
                    </div>
                    
                    <div class="mt-6 pt-6 border-t border-white/20 flex justify-between items-center">
                      <p class="text-sm text-amber-100">Nivel: <span class="font-medium text-white">Bronce</span></p>
                      <p class="text-sm text-amber-100">Próximo nivel: <span class="font-medium text-white">500 pts</span></p>
                    </div>
                    
                    <div class="w-full bg-black/20 rounded-full h-1.5 mt-2">
                      <div class="bg-white h-1.5 rounded-full" style="width: 45%"></div>
                    </div>
                  </div>
                </div>

                <div class="bg-white dark:bg-zinc-800 rounded-xl border border-gray-200 dark:border-zinc-700 overflow-hidden">
                  <div class="px-6 py-4 border-b border-gray-200 dark:border-zinc-700 bg-gray-50 dark:bg-zinc-800">
                    <h3 class="font-medium text-gray-900 dark:text-white">Historial de Puntos</h3>
                  </div>
                  <div class="p-8 text-center text-gray-600 dark:text-zinc-400">
                    <p>No hay movimientos de puntos recientes</p>
                  </div>
                </div>
              </div>

            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- 🎨 MODAL CREAR/EDITAR CLIENTE - Teleport al Body (evita conflictos z-index) -->
  <Teleport to="body">
    <Transition name="modal">
      <div v-if="showCustomerModal" 
           class="fixed inset-0 bg-black/50 dark:bg-black/60 flex items-center justify-center p-4 z-[9999] animate-fade-in"
           @click.self="closeCustomerModal">
        <div class="bg-gray-50 dark:bg-zinc-900 rounded-2xl max-w-4xl w-full max-h-[92vh] overflow-hidden flex flex-col border border-gray-300 dark:border-zinc-700"
             @click.stop>
          
          <!-- 🎯 Header Profesional -->
          <div class="px-8 py-6 bg-gray-50 dark:bg-zinc-900 border-b border-gray-200 dark:border-zinc-700">
            <div class="flex items-center gap-4">
              <div class="w-14 h-14 bg-blue-600 dark:bg-blue-400/20 rounded-2xl flex items-center justify-center">
                <svg class="w-7 h-7 text-white dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
              </div>
              <div>
                <h3 class="text-2xl font-medium text-gray-900 dark:text-white">
                  {{ isEditing ? 'Editar Cliente' : 'Nuevo Cliente' }}
                </h3>
                <p class="text-sm text-gray-600 dark:text-zinc-400 mt-0.5">
                  {{ isEditing ? 'Actualiza la información del cliente' : 'Completa los datos del nuevo cliente' }}
                </p>
              </div>
            </div>
          </div>
          
          <!-- 📋 Contenido con Grid Inteligente -->
          <div class="flex-1 overflow-y-auto px-8 py-6 bg-gray-50 dark:bg-zinc-950">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-x-8 gap-y-6">
              
              <!-- 🧑 COLUMNA 1: Información Personal -->
              <div class="space-y-5">
                <div class="pb-3 border-b-2 border-blue-600/20 dark:border-blue-400/20">
                  <h4 class="text-base font-medium text-gray-900 dark:text-white uppercase tracking-wide">Información Personal</h4>
                </div>
                
                <!-- Nombre Completo -->
                <div>
                  <label class="block text-sm font-medium text-gray-700 dark:text-zinc-300 mb-2">Nombre Completo *</label>
                  <input v-model="customerForm.name" 
                         type="text" 
                         :class="[
                           'w-full h-12 px-4 border rounded-xl font-medium text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-zinc-500 transition-all duration-200',
                           formErrors.name 
                             ? 'bg-rose-50 dark:bg-rose-900/20 border-rose-600 dark:border-rose-400 focus:ring-2 focus:ring-rose-600 dark:focus:ring-rose-400' 
                             : 'bg-white dark:bg-zinc-800 border-gray-300 dark:border-zinc-700 focus:border-blue-600 dark:focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20 dark:focus:ring-blue-400/20'
                         ]"
                         placeholder="Ej: Juan Carlos Pérez Gómez">
                  <p v-if="formErrors.name" class="text-rose-600 dark:text-rose-400 text-xs font-medium mt-2 flex items-center gap-1">
                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                      <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                    {{ formErrors.name }}
                  </p>
                </div>
                
                <!-- Tipo y Número de Documento -->
                <div class="grid grid-cols-5 gap-3">
                  <div class="col-span-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-zinc-300 mb-2">Tipo Doc.</label>
                    <select v-model="customerForm.document_type" 
                            class="w-full h-12 px-3 border border-gray-300 dark:border-zinc-700 rounded-xl bg-white dark:bg-zinc-800 text-gray-900 dark:text-white font-medium focus:border-blue-600 dark:focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20 dark:focus:ring-blue-400/20 transition-all duration-200">
                      <option value="CC">CC</option>
                      <option value="TI">TI</option>
                      <option value="CE">CE</option>
                      <option value="NIT">NIT</option>
                      <option value="PP">Pasaporte</option>
                    </select>
                  </div>
                  <div class="col-span-3">
                    <label class="block text-sm font-medium text-gray-700 dark:text-zinc-300 mb-2">Número *</label>
                    <input v-model="customerForm.document_number" 
                           type="text" 
                           :class="[
                             'w-full h-12 px-4 border rounded-xl font-medium text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-zinc-500 transition-all duration-200',
                             formErrors.document_number 
                               ? 'bg-rose-50 dark:bg-rose-900/20 border-rose-600 dark:border-rose-400 focus:ring-2 focus:ring-rose-600 dark:focus:ring-rose-400' 
                               : 'bg-white dark:bg-zinc-800 border-gray-300 dark:border-zinc-700 focus:border-blue-600 dark:focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20 dark:focus:ring-blue-400/20'
                           ]"
                           placeholder="1234567890">
                    <p v-if="formErrors.document_number" class="text-rose-600 dark:text-rose-400 text-xs font-medium mt-2">{{ formErrors.document_number }}</p>
                  </div>
                </div>
                
                <!-- Fecha Nacimiento y Género -->
                <div class="grid grid-cols-2 gap-4">
                  <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-zinc-300 mb-2">Fecha Nacimiento</label>
                    <input v-model="customerForm.birth_date" 
                           type="date" 
                           class="w-full h-12 px-4 border border-gray-300 dark:border-zinc-700 rounded-xl bg-white dark:bg-zinc-800 text-gray-900 dark:text-white font-medium focus:border-blue-600 dark:focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20 dark:focus:ring-blue-400/20 transition-all duration-200">
                  </div>
                  <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-zinc-300 mb-2">Género</label>
                    <select v-model="customerForm.gender" 
                            class="w-full h-12 px-4 border border-gray-300 dark:border-zinc-700 rounded-xl bg-white dark:bg-zinc-800 text-gray-900 dark:text-white font-medium focus:border-blue-600 dark:focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20 dark:focus:ring-blue-400/20 transition-all duration-200">
                      <option value="">Seleccionar</option>
                      <option value="M">Masculino</option>
                      <option value="F">Femenino</option>
                      <option value="O">Otro</option>
                    </select>
                  </div>
                </div>
              </div>

              <!-- 📞 COLUMNA 2: Contacto y Financiero -->
              <div class="space-y-5">
                <div class="pb-3 border-b-2 border-blue-600/20 dark:border-blue-400/20">
                  <h4 class="text-base font-medium text-gray-900 dark:text-white uppercase tracking-wide">Contacto</h4>
                </div>
                
                <!-- Teléfono -->
                <div>
                  <label class="block text-sm font-medium text-gray-700 dark:text-zinc-300 mb-2">Teléfono *</label>
                  <input v-model="customerForm.phone" 
                         type="text" 
                         :class="[
                           'w-full h-12 px-4 border rounded-xl font-medium text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-zinc-500 transition-all duration-200',
                           formErrors.phone 
                             ? 'bg-rose-50 dark:bg-rose-900/20 border-rose-600 dark:border-rose-400 focus:ring-2 focus:ring-rose-600 dark:focus:ring-rose-400' 
                             : 'bg-white dark:bg-zinc-800 border-gray-300 dark:border-zinc-700 focus:border-blue-600 dark:focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20 dark:focus:ring-blue-400/20'
                         ]"
                         placeholder="+57 300 123 4567">
                  <p v-if="formErrors.phone" class="text-rose-600 dark:text-rose-400 text-xs font-medium mt-2">{{ formErrors.phone }}</p>
                </div>
                
                <!-- Email -->
                <div>
                  <label class="block text-sm font-medium text-gray-700 dark:text-zinc-300 mb-2">Email *</label>
                  <input v-model="customerForm.email" 
                         type="email" 
                         :class="[
                           'w-full h-12 px-4 border rounded-xl font-medium text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-zinc-500 transition-all duration-200',
                           formErrors.email 
                             ? 'bg-rose-50 dark:bg-rose-900/20 border-rose-600 dark:border-rose-400 focus:ring-2 focus:ring-rose-600 dark:focus:ring-rose-400' 
                             : 'bg-white dark:bg-zinc-800 border-gray-300 dark:border-zinc-700 focus:border-blue-600 dark:focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20 dark:focus:ring-blue-400/20'
                         ]"
                         placeholder="correo@ejemplo.com">
                  <p v-if="formErrors.email" class="text-rose-600 dark:text-rose-400 text-xs font-medium mt-2">{{ formErrors.email }}</p>
                </div>
                
                <!-- Dirección -->
                <div>
                  <label class="block text-sm font-medium text-gray-700 dark:text-zinc-300 mb-2">Dirección</label>
                  <input v-model="customerForm.address" 
                         type="text" 
                         class="w-full h-12 px-4 border border-gray-300 dark:border-zinc-700 rounded-xl bg-white dark:bg-zinc-800 text-gray-900 dark:text-white font-medium placeholder-gray-400 dark:placeholder-zinc-500 focus:border-blue-600 dark:focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20 dark:focus:ring-blue-400/20 transition-all duration-200"
                         placeholder="Calle 123 #45-67">
                </div>
                
                <!-- Ciudad -->
                <div>
                  <label class="block text-sm font-medium text-gray-700 dark:text-zinc-300 mb-2">Ciudad</label>
                  <input v-model="customerForm.city" 
                         type="text" 
                         class="w-full h-12 px-4 border border-gray-300 dark:border-zinc-700 rounded-xl bg-white dark:bg-zinc-800 text-gray-900 dark:text-white font-medium placeholder-gray-400 dark:placeholder-zinc-500 focus:border-blue-600 dark:focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20 dark:focus:ring-blue-400/20 transition-all duration-200"
                         placeholder="Bogotá, Colombia">
                </div>
                
                <!-- 💳 Crédito (si está habilitado) -->
                <div v-if="isCreditiendaEnabled" class="pt-2 border-t border-gray-200 dark:border-zinc-700">
                  <label class="block text-sm font-medium text-gray-700 dark:text-zinc-300 mb-2">Límite de Crédito</label>
                  <div class="relative">
                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-600 dark:text-zinc-400 font-medium">$</span>
                    <input v-model="customerForm.credit_limit" 
                           type="number" 
                           min="0" 
                           step="10000"
                           :class="[
                             'w-full h-12 pl-8 pr-4 border rounded-xl font-medium text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-zinc-500 transition-all duration-200',
                             formErrors.credit_limit 
                               ? 'bg-rose-50 dark:bg-rose-900/20 border-rose-600 dark:border-rose-400 focus:ring-2 focus:ring-rose-600 dark:focus:ring-rose-400' 
                               : 'bg-amber-50 dark:bg-amber-500/10 border-amber-300 dark:border-amber-700/30 focus:border-amber-500 dark:focus:border-amber-300 focus:ring-2 focus:ring-amber-500/20 dark:focus:ring-amber-300/20'
                           ]"
                           placeholder="500000">
                  </div>
                  <p v-if="formErrors.credit_limit" class="text-rose-600 dark:text-rose-400 text-xs font-medium mt-2">{{ formErrors.credit_limit }}</p>
                </div>
              </div>
            </div>

            <!-- 📷 Foto del Cliente -->
            <div class="mt-6 pt-6 border-t border-gray-200 dark:border-zinc-700">
              <div class="pb-3">
                <h4 class="text-base font-medium text-gray-900 dark:text-white uppercase tracking-wide">Foto del Cliente</h4>
                <p class="text-xs text-gray-600 dark:text-zinc-400 mt-1">Sube una foto para identificar fácilmente al cliente</p>
              </div>
              
              <div class="flex items-center gap-6">
                <!-- Preview de la foto -->
                <div class="relative">
                  <div class="w-24 h-24 rounded-md overflow-hidden border-2 border-dashed border-gray-300 dark:border-zinc-700 bg-gray-100 dark:bg-zinc-800 flex items-center justify-center">
                    <img v-if="customerForm.credit_photo" 
                         :src="customerForm.credit_photo" 
                         alt="Foto del cliente"
                         class="w-full h-full object-cover">
                    <div v-else class="text-center p-2">
                      <svg class="w-8 h-8 mx-auto text-gray-600 dark:text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                      </svg>
                      <p class="text-[10px] text-gray-600 dark:text-zinc-400 mt-1">Sin foto</p>
                    </div>
                  </div>
                  <!-- Botón eliminar foto -->
                  <button v-if="customerForm.credit_photo" 
                          @click="customerForm.credit_photo = ''"
                          class="absolute -top-2 -right-2 w-6 h-6 bg-rose-600 hover:bg-rose-700 text-white rounded-full flex items-center justify-center transition-colors">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                  </button>
                </div>
                
                <!-- Botones de acción -->
                <div class="flex flex-col gap-2">
                  <button type="button"
                          @click="triggerPhotoUpload"
                          class="px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-md transition-colors flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <span>Subir Foto</span>
                  </button>
                  <input type="file" 
                         ref="photoInput"
                         accept="image/*" 
                         class="hidden" 
                         @change="handlePhotoUpload">
                  <button type="button"
                          @click="openCamera"
                          class="px-4 py-2.5 bg-gray-100 dark:bg-zinc-700 hover:bg-gray-200 dark:hover:bg-zinc-600 text-gray-700 dark:text-zinc-300 text-sm font-medium rounded-md transition-colors flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    <span>Usar Cámara</span>
                  </button>
                </div>
              </div>
            </div>

            <!-- ✅ Checkboxes de Estado -->
            <div class="mt-8 pt-6 border-t border-gray-200 dark:border-zinc-700 flex flex-wrap items-center gap-6">
              <label class="flex items-center gap-3 cursor-pointer group">
                <div class="relative">
                  <input v-model="customerForm.active" 
                         type="checkbox" 
                         class="peer w-6 h-6 cursor-pointer appearance-none rounded-lg border-2 border-gray-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 checked:bg-[#1e8e3e] dark:checked:bg-[#81c995] checked:border-[#1e8e3e] dark:checked:border-[#81c995] transition-all duration-200">
                  <svg class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-4 h-4 text-white pointer-events-none opacity-0 peer-checked:opacity-100 transition-opacity" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                  </svg>
                </div>
                <span class="text-sm font-medium text-gray-700 dark:text-zinc-300 group-hover:text-emerald-600 dark:group-hover:text-[#81c995] transition-colors">Cliente Activo</span>
              </label>

              <label class="flex items-center gap-3 cursor-pointer group" :class="{'opacity-50 cursor-not-allowed': !isCreditiendaEnabled}">
                <div class="relative">
                  <input v-model="customerForm.credit_active" 
                         type="checkbox" 
                         :disabled="!isCreditiendaEnabled"
                         @click="handleCreditCheckboxClick"
                         class="peer w-6 h-6 cursor-pointer appearance-none rounded-lg border-2 border-gray-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 checked:bg-[#f9ab00] dark:checked:bg-[#fdd663] checked:border-amber-500 dark:checked:border-amber-300 disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-200">
                  <svg class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-4 h-4 text-white pointer-events-none opacity-0 peer-checked:opacity-100 transition-opacity" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                  </svg>
                </div>
                <span class="text-sm font-medium text-gray-700 dark:text-zinc-300 group-hover:text-amber-500 dark:group-hover:text-[#fdd663] transition-colors">
                  Habilitar Crédito
                  <span v-if="!isCreditiendaEnabled" class="ml-2 px-2 py-0.5 bg-amber-50 dark:bg-amber-500/20 text-amber-600 dark:text-amber-300 border border-amber-300 dark:border-amber-700/30 text-xs font-medium rounded-full uppercase tracking-wider">Premium</span>
                </span>
              </label>
            </div>
          </div>
          
          <!-- 🎯 Footer con Botones Profesionales -->
          <div class="px-8 py-5 bg-gray-50 dark:bg-zinc-900 border-t border-gray-200 dark:border-zinc-700 flex items-center justify-between">
            <p class="text-xs text-gray-600 dark:text-zinc-400">
              <span class="text-rose-600">*</span> Campos requeridos
            </p>
            <div class="flex items-center gap-3">
              <button @click="closeCustomerModal" 
                      class="px-6 py-3 text-gray-600 dark:text-zinc-400 hover:text-gray-900 dark:hover:text-white text-sm font-medium rounded-full hover:bg-gray-100 dark:hover:bg-zinc-800 transition-all duration-200">
                Cancelar
              </button>
              <button @click="saveCustomer" 
                      :disabled="loading || !customerForm.name || !customerForm.document_number"
                      class="px-8 py-3 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-full disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-300 flex items-center gap-2">
                <svg v-if="loading" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span>{{ loading ? 'Guardando...' : (isEditing ? 'Actualizar Cliente' : 'Crear Cliente') }}</span>
              </button>
            </div>
          </div>
        </div>
      </div>
    </Transition>
  </Teleport>

  <!-- 🎨 MODAL DETALLES DE FACTURA - Diseño Profesional -->
  <Teleport to="body">
    <Transition name="modal">
      <div v-if="showInvoiceDetailModal && selectedInvoice"
           class="fixed inset-0 bg-black/50 dark:bg-black/60  flex items-center justify-center p-4 z-[9999] animate-fade-in"
           @click.self="showInvoiceDetailModal = false">
        <div class="bg-gray-50 dark:bg-zinc-900 rounded-2xl w-full max-w-4xl max-h-[90vh] overflow-hidden border border-gray-300 dark:border-zinc-700">
          
          <!-- Header -->
          <div class="px-6 py-4 border-b border-gray-200 dark:border-zinc-700 bg-gray-50 dark:bg-zinc-900 flex items-center justify-between">
            <div class="flex items-center gap-3">
              <div class="w-12 h-12 bg-blue-50 dark:bg-blue-900/20 rounded-2xl flex items-center justify-center">
                <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
              </div>
              <div>
                <h2 class="text-lg font-medium text-gray-900 dark:text-white">Detalles de Factura</h2>
                <p class="text-sm text-gray-600 dark:text-zinc-400">{{ selectedInvoice.custom_number || selectedInvoice.number || `#${selectedInvoice.id}` }}</p>
              </div>
            </div>
            <button @click="showInvoiceDetailModal = false"
                    class="w-10 h-10 rounded-full hover:bg-gray-100 dark:hover:bg-zinc-800 flex items-center justify-center text-gray-600 hover:text-gray-900 dark:hover:text-white transition-colors">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
              </svg>
            </button>
          </div>

          <!-- Contenido -->
          <div class="p-6 overflow-y-auto max-h-[calc(90vh-140px)] space-y-4">
            <!-- Info General y Totales -->
            <div class="grid grid-cols-2 gap-4">
              <div class="bg-white dark:bg-zinc-800 rounded-xl p-4 border border-gray-200 dark:border-zinc-700">
                <h3 class="text-sm font-medium text-gray-900 dark:text-white mb-3">Información General</h3>
                <div class="space-y-2 text-sm">
                  <div class="flex justify-between">
                    <span class="text-gray-600 dark:text-zinc-400">Número:</span>
                    <span class="font-medium text-gray-900 dark:text-white">{{ selectedInvoice.custom_number || selectedInvoice.number || `#${selectedInvoice.id}` }}</span>
                  </div>
                  <div class="flex justify-between">
                    <span class="text-gray-600 dark:text-zinc-400">Fecha:</span>
                    <span class="font-medium text-gray-900 dark:text-white">{{ formatInvoiceDate(selectedInvoice.date) }}</span>
                  </div>
                  <div class="flex justify-between">
                    <span class="text-gray-600 dark:text-zinc-400">Vencimiento:</span>
                    <span class="font-medium text-gray-900 dark:text-white">{{ formatInvoiceDate(selectedInvoice.due_date) || 'N/A' }}</span>
                  </div>
                  <div class="flex justify-between items-center">
                    <span class="text-gray-600 dark:text-zinc-400">Estado:</span>
                    <span :class="selectedInvoice.status === 'paid' 
                      ? 'bg-emerald-50 dark:bg-emerald-900/20 text-emerald-600 dark:text-emerald-400 border-emerald-200 dark:border-emerald-800/30' 
                      : 'bg-amber-50 dark:bg-amber-500/20 text-amber-600 dark:text-amber-300 border-amber-300 dark:border-amber-700/30'"
                      class="px-2 py-0.5 rounded-full text-xs font-medium border">
                      {{ selectedInvoice.status === 'paid' ? 'Pagada' : 'Pendiente' }}
                    </span>
                  </div>
                </div>
              </div>

              <div class="bg-white dark:bg-zinc-800 rounded-xl p-4 border border-gray-200 dark:border-zinc-700">
                <h3 class="text-sm font-medium text-gray-900 dark:text-white mb-3">Totales</h3>
                <div class="space-y-2 text-sm">
                  <div class="flex justify-between">
                    <span class="text-gray-600 dark:text-zinc-400">Subtotal:</span>
                    <span class="font-medium text-gray-900 dark:text-white">${{ formatCurrency(selectedInvoice.subtotal || selectedInvoice.total) }}</span>
                  </div>
                  <div class="flex justify-between">
                    <span class="text-gray-600 dark:text-zinc-400">Descuento:</span>
                    <span class="font-medium text-gray-900 dark:text-white">-${{ formatCurrency(selectedInvoice.discount || 0) }}</span>
                  </div>
                  <div class="flex justify-between">
                    <span class="text-gray-600 dark:text-zinc-400">Impuestos:</span>
                    <span class="font-medium text-gray-900 dark:text-white">${{ formatCurrency(selectedInvoice.tax || 0) }}</span>
                  </div>
                  <div class="flex justify-between border-t border-gray-200 dark:border-zinc-700 pt-2 mt-2">
                    <span class="font-medium text-gray-900 dark:text-white">Total:</span>
                    <span class="font-medium text-lg text-blue-600 dark:text-blue-400">${{ formatCurrency(selectedInvoice.total) }}</span>
                  </div>
                </div>
              </div>
            </div>

            <!-- Productos con tabla profesional -->
            <div class="bg-white dark:bg-zinc-800 rounded-xl border border-gray-200 dark:border-zinc-700 overflow-hidden">
              <div class="px-4 py-3 bg-gray-50 dark:bg-zinc-800 border-b border-gray-200 dark:border-zinc-700">
                <h3 class="text-sm font-medium text-gray-900 dark:text-white">Productos ({{ selectedInvoice.items?.length || 0 }})</h3>
              </div>
              <div class="overflow-x-auto">
                <table class="w-full">
                  <thead class="bg-gray-50 dark:bg-zinc-800 border-b border-gray-200 dark:border-zinc-700">
                    <tr>
                      <th class="px-4 py-3 text-left text-xs font-medium text-gray-600 dark:text-zinc-400 uppercase">Producto</th>
                      <th class="px-4 py-3 text-center text-xs font-medium text-gray-600 dark:text-zinc-400 uppercase">Cantidad</th>
                      <th class="px-4 py-3 text-right text-xs font-medium text-gray-600 dark:text-zinc-400 uppercase">Precio Unit.</th>
                      <th class="px-4 py-3 text-right text-xs font-medium text-gray-600 dark:text-zinc-400 uppercase">Total</th>
                    </tr>
                  </thead>
                  <tbody class="divide-y divide-gray-200 dark:divide-zinc-700">
                    <tr v-for="item in (selectedInvoice.items || [])" :key="item.id" class="hover:bg-gray-100 dark:hover:bg-zinc-700">
                      <td class="px-4 py-3">
                        <div class="flex items-center gap-3">
                          <div class="w-10 h-10 rounded-xl bg-gray-100 dark:bg-zinc-700 flex items-center justify-center overflow-hidden flex-shrink-0">
                            <img v-if="item.product?.image_url || item.image_url" 
                                 :src="item.product?.image_url || item.image_url" 
                                 :alt="item.product?.name || item.product_name"
                                 class="w-full h-full object-cover"
                                 @error="$event.target.style.display='none'">
                            <svg v-else class="w-5 h-5 text-gray-600 dark:text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                            </svg>
                          </div>
                          <div>
                            <p class="text-sm font-medium text-gray-900 dark:text-white">{{ item.product?.name || item.product_name || item.name || 'Producto' }}</p>
                            <p class="text-xs text-gray-600 dark:text-zinc-400">SKU: {{ item.product?.sku || item.sku || 'N/A' }}</p>
                          </div>
                        </div>
                      </td>
                      <td class="px-4 py-3 text-center text-sm text-gray-900 dark:text-white">{{ Math.abs(item.quantity || 1) }}</td>
                      <td class="px-4 py-3 text-right text-sm text-gray-900 dark:text-white">${{ formatCurrency(item.unit_price || item.price) }}</td>
                      <td class="px-4 py-3 text-right text-sm font-medium text-gray-900 dark:text-white">${{ formatCurrency(item.total_price || (Math.abs(item.quantity || 1) * (item.unit_price || item.price || 0))) }}</td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>

          <!-- Footer -->
          <div class="px-6 py-4 bg-gray-50 dark:bg-zinc-900 border-t border-gray-200 dark:border-zinc-700 flex justify-end">
            <button @click="showInvoiceDetailModal = false"
                    class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-md transition-colors">
              Cerrar
            </button>
          </div>
        </div>
      </div>
    </Transition>
  </Teleport>

  <!-- Modal Premium Feature -->
  <Teleport to="body">
    <div v-if="showPremiumModal" class="fixed inset-0 bg-black/50 dark:bg-black/60 flex items-center justify-center z-[9999] p-4 animate-fade-in">
      <div class="bg-gray-50 dark:bg-zinc-900 rounded-2xl max-w-md w-full border border-gray-300 dark:border-zinc-700 animate-scale-in">
        
        <!-- Contenido -->
        <div class="p-8 text-center">
          <!-- Icono Premium -->
          <div class="w-20 h-20 bg-blue-600 dark:bg-blue-400/20 rounded-full flex items-center justify-center mx-auto mb-6">
            <svg class="w-10 h-10 text-white dark:text-blue-400" fill="currentColor" viewBox="0 0 24 24">
              <path d="M12 2L15.09 8.26L22 9.27L17 14.14L18.18 21.02L12 17.77L5.82 21.02L7 14.14L2 9.27L8.91 8.26L12 2Z"/>
            </svg>
          </div>

          <!-- Título -->
          <h3 class="text-2xl font-medium text-gray-900 dark:text-white mb-3">¡Mejora tu Plan!</h3>
          
          <!-- Mensaje -->
          <p class="text-base text-gray-600 dark:text-zinc-400 mb-6 leading-relaxed">
            <span class="font-medium text-blue-600 dark:text-blue-400">{{ premiumFeatureName }}</span> está disponible en nuestros planes premium.
          </p>
          
          <p class="text-sm text-gray-600 dark:text-zinc-400 mb-8">
            💡 Desbloquea todas las funciones premium para potenciar tu negocio
          </p>

          <!-- Botones -->
          <div class="flex gap-3">
            <button
              @click="showPremiumModal = false"
              class="flex-1 py-3 bg-white dark:bg-zinc-700 text-gray-700 dark:text-zinc-300 text-base font-medium rounded-full border border-gray-300 dark:border-zinc-700 hover:bg-gray-100 dark:hover:bg-zinc-600 transition-colors"
            >
              Cerrar
            </button>
            <button
              @click="navigateToPlans"
              class="flex-1 py-3 bg-blue-600 hover:bg-blue-700 text-white text-base font-medium rounded-md transition-colors duration-200"
            >
              Ver Planes
            </button>
          </div>
        </div>
      </div>
    </div>
  </Teleport>

  <!-- 🖼️ MODAL PREVIEW DE FOTO -->
  <Teleport to="body">
    <Transition
      enter-active-class="transition-opacity duration-300 ease-out"
      leave-active-class="transition-opacity duration-200 ease-in"
      enter-from-class="opacity-0"
      enter-to-class="opacity-100"
      leave-from-class="opacity-100"
      leave-to-class="opacity-0"
    >
      <div v-if="showPhotoPreviewModal" @click="closePhotoPreview" class="fixed inset-0 z-[9999] flex items-center justify-center p-4 bg-black/80">
        <Transition
          enter-active-class="transition-all duration-300 ease-out"
          leave-active-class="transition-all duration-200 ease-in"
          enter-from-class="opacity-0 scale-90"
          enter-to-class="opacity-100 scale-100"
          leave-from-class="opacity-100 scale-100"
          leave-to-class="opacity-0 scale-90"
        >
          <div v-if="showPhotoPreviewModal" @click.stop class="relative max-w-4xl w-full">
            <!-- Header con nombre del cliente -->
            <div class="bg-gray-50 dark:bg-zinc-900 rounded-t-2xl px-6 py-4 border-b border-gray-200 dark:border-zinc-700">
              <div>
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">{{ previewPhotoName }}</h3>
                <p class="text-sm text-gray-600 dark:text-zinc-400">Foto del cliente</p>
              </div>
            </div>

            <!-- Imagen grande -->
            <div class="bg-gray-100 dark:bg-zinc-950 rounded-b-2xl p-8 flex items-center justify-center">
              <img 
                :src="previewPhotoUrl" 
                :alt="previewPhotoName"
                class="max-w-full max-h-[70vh] object-contain rounded-xl"
              />
            </div>

            <!-- Botón de cerrar flotante -->
            <button @click="closePhotoPreview" class="absolute -top-4 -right-4 w-12 h-12 bg-white dark:bg-zinc-800 hover:bg-gray-100 dark:hover:bg-zinc-700 text-gray-700 dark:text-zinc-300 rounded-full shadow-2xl transition-all duration-200 hover:scale-110 flex items-center justify-center">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path>
              </svg>
            </button>
          </div>
        </Transition>
      </div>
    </Transition>
  </Teleport>
  
  <!-- Modal: Confirmar Eliminación (cuando NO tiene facturas) -->
  <Teleport to="body">
    <Transition name="modal">
      <div v-if="showDeleteConfirmModal" class="fixed inset-0 bg-black/70  flex items-center justify-center z-[9999] p-4 animate-fade-in">
        <div class="bg-white dark:bg-zinc-900 rounded-2xl shadow-2xl max-w-md w-full border border-gray-200 dark:border-zinc-800 animate-scale-in">
          
          <!-- Contenido -->
          <div class="p-8 text-center">
            <!-- Icono Advertencia -->
            <div class="w-20 h-20 bg-amber-50 dark:bg-amber-950/50 rounded-full flex items-center justify-center mx-auto mb-6 border border-amber-200 dark:border-amber-900/50">
              <svg class="w-10 h-10 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
              </svg>
            </div>

            <!-- Título -->
            <h3 class="text-2xl font-black text-gray-900 dark:text-white mb-3">¿Eliminar Cliente?</h3>
            
            <!-- Mensaje -->
            <p class="text-base text-gray-600 dark:text-zinc-400 mb-2 leading-relaxed">
              ¿Estás seguro de eliminar el cliente <span class="font-semibold text-gray-900 dark:text-white">"{{ customerToDelete?.name }}"</span>?
            </p>
            
            <p class="text-sm text-red-600 dark:text-red-400 mb-8">
              Esta acción no se puede deshacer.
            </p>

            <!-- Botones -->
            <div class="flex gap-3">
              <button
                @click="cancelDeleteCustomer"
                class="flex-1 py-3 bg-white dark:bg-zinc-800 text-gray-700 dark:text-zinc-300 text-base font-semibold rounded-xl border border-gray-300 dark:border-zinc-700 hover:bg-gray-50 dark:hover:bg-zinc-700 transition-colors"
              >
                Cancelar
              </button>
              <button
                @click="confirmDeleteCustomer"
                class="flex-1 py-3 bg-gradient-to-r from-red-600 to-rose-600 hover:from-red-700 hover:to-rose-700 text-white text-base font-semibold rounded-xl transition-colors duration-200 shadow-lg"
              >
                Eliminar
              </button>
            </div>
          </div>
        </div>
      </div>
    </Transition>
  </Teleport>
  
  <!-- Modal: No se puede eliminar (tiene facturas asociadas) -->
  <Teleport to="body">
    <Transition name="modal">
      <div v-if="showCannotDeleteModal" class="fixed inset-0 bg-black/50 dark:bg-black/60 flex items-center justify-center z-[9999] p-4 animate-fade-in">
        <div class="bg-gray-50 dark:bg-zinc-900 rounded-2xl max-w-md w-full border border-gray-300 dark:border-zinc-700 animate-scale-in">
          
          <!-- Contenido -->
          <div class="p-8 text-center">
            <!-- Icono Error -->
            <div class="w-20 h-20 bg-rose-600/10 dark:bg-rose-900/10 rounded-full flex items-center justify-center mx-auto mb-6 border border-rose-600/20 dark:border-rose-400/20">
              <svg class="w-10 h-10 text-rose-600 dark:text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
              </svg>
            </div>

            <!-- Título -->
            <h3 class="text-2xl font-medium text-gray-900 dark:text-white mb-3">No se puede eliminar</h3>
            
            <!-- Mensaje -->
            <p class="text-base text-gray-600 dark:text-zinc-400 mb-2 leading-relaxed">
              El cliente <span class="font-medium text-gray-900 dark:text-white">"{{ customerToDelete?.name }}"</span> tiene facturas asociadas.
            </p>
            
            <p class="text-sm text-gray-600 dark:text-zinc-400 mb-8">
              Para mantener la integridad de tus registros contables, no es posible eliminar clientes con historial de compras.
            </p>

            <!-- Botón -->
            <button
              @click="cancelDeleteCustomer"
              class="w-full py-3 bg-blue-600 hover:bg-blue-700 text-white text-base font-medium rounded-md transition-colors duration-200"
            >
              Entendido
            </button>
          </div>
        </div>
      </div>
    </Transition>
  </Teleport>
</template>

<script setup>
import { ref, computed, onMounted, watch, onActivated } from 'vue'
import { customersService } from '../services/customersService.js'
import { invoicesService } from '../services/invoicesService.js'
import { useToast } from '../composables/useToast.js'
import { useCreditienda } from '../composables/useCreditienda.js'
import { useAutoRefresh } from '../composables/useRouteState.js'
import { appStore } from '../store/appStore.js'
import { useModuleNavigation } from '../composables/useModuleNavigation.js'
import { useUIContextStore } from '../store/uiContextStore.js'

const { navigateToModule } = useModuleNavigation()
const uiContextStore = useUIContextStore()

// ✅ Definir props y emits para heredar correctamente desde el padre
const props = defineProps({
  moduleName: String,
  queryParams: Object,
  customers: Array
})

const emit = defineEmits(['navigate', 'changeModule', 'openQuotationInPos', 'openReturnInPos', 'refresh'])

// Sistema de toasts
const { showSuccess, showError, showWarning, showInfo } = useToast()

// Sistema de Creditienda
const { isCreditiendaEnabled, showCreditiendaUpgradeModal } = useCreditienda()

// Sistema de Fidelización (Loyalty Points)
const isLoyaltyEnabled = computed(() => {
  return appStore.systemSettings?.enable_loyalty_system || false
})

// Estado reactivo
const loading = ref(false)
const customers = ref([])
const searchTerm = ref('')
const statusFilter = ref('')
const cityFilter = ref('')
const viewMode = ref('table')

// 💾 Sistema de Preferencias del Usuario
const USER_PREFERENCES_KEY = 'customers_user_preferences'

// Paginación
const currentPage = ref(1)
const itemsPerPage = ref(25)

// Modals
const showCustomerModal = ref(false)
const isEditing = ref(false)
const selectedCustomer = ref(null)
const activeTab = ref('info')

// Modal de preview de foto
const showPhotoPreviewModal = ref(false)
const previewPhotoUrl = ref('')
const previewPhotoName = ref('')

// Modal Premium
const showPremiumModal = ref(false)
const premiumFeatureName = ref('')

// Modales de eliminación de cliente
const showDeleteConfirmModal = ref(false)
const showCannotDeleteModal = ref(false)
const customerToDelete = ref(null)

// Historial inline del cliente
const historyLoading = ref(false)
const customerInvoices = ref([])

// Computed para resumen del historial
const customerHistorySummary = computed(() => {
  const total = customerInvoices.value.reduce((sum, inv) => sum + parseFloat(inv.total || 0), 0)
  const count = customerInvoices.value.length
  return {
    totalSpent: total,
    average: count > 0 ? total / count : 0
  }
})

// Cargar historial cuando cambia el cliente (siempre, para los stats)
const loadCustomerHistory = async () => {
  if (!selectedCustomer.value?.id) return
  
  historyLoading.value = true
  try {
    const response = await invoicesService.getInvoices()
    if (response.success) {
      customerInvoices.value = response.data
        .filter(inv => inv.customer_id === selectedCustomer.value.id || inv.customer_name === selectedCustomer.value.name)
        .sort((a, b) => new Date(b.date) - new Date(a.date))
    }
  } catch (error) {
    console.error('Error cargando historial:', error)
  } finally {
    historyLoading.value = false
  }
}

// Formatear fecha de factura
const formatInvoiceDate = (dateStr) => {
  if (!dateStr) return '-'
  const date = new Date(dateStr)
  return date.toLocaleDateString('es-CO', { day: '2-digit', month: 'short', year: 'numeric' })
}

// Ver detalles de factura (modal simple)
const showInvoiceDetailModal = ref(false)
const selectedInvoice = ref(null)

const viewInvoiceDetails = (invoice) => {
  selectedInvoice.value = invoice
  showInvoiceDetailModal.value = true
}

// Watch para cargar historial cuando cambia el tab (no necesario, pero mantiene compatibilidad)
watch(activeTab, (newTab) => {
  if (newTab === 'history' && customerInvoices.value.length === 0) {
    loadCustomerHistory()
  }
})

// Watch para recargar historial cuando cambia el cliente seleccionado
watch(selectedCustomer, (newCustomer) => {
  customerInvoices.value = []
  if (newCustomer?.id) {
    loadCustomerHistory()
  }
})

const selectCustomer = (customer) => {
  selectedCustomer.value = customer
  activeTab.value = 'info'
}

const customerForm = ref({
  name: '',
  email: '',
  phone: '',
  address: '',
  city: '',
  document_type: 'CC',
  document_number: '',
  birth_date: '',
  gender: '',
  credit_limit: 0,
  current_debt: 0,
  active: true,
  credit_active: false,
  credit_photo: ''
})

// Cargar preferencias del usuario
const loadUserPreferences = () => {
  try {
    const savedPreferences = localStorage.getItem(USER_PREFERENCES_KEY)
    if (savedPreferences) {
      const preferences = JSON.parse(savedPreferences)
      
      // Aplicar preferencias guardadas
      viewMode.value = preferences.viewMode || 'table'
      itemsPerPage.value = preferences.itemsPerPage || 25
      statusFilter.value = preferences.statusFilter || ''
    }
  } catch (error) {
    // Error silencioso al cargar preferencias
  }
}

// Guardar preferencias del usuario
const saveUserPreferences = () => {
  try {
    const preferences = {
      viewMode: viewMode.value,
      itemsPerPage: itemsPerPage.value,
      statusFilter: statusFilter.value,
      lastUpdated: new Date().toISOString()
    }
    
    localStorage.setItem(USER_PREFERENCES_KEY, JSON.stringify(preferences))
  } catch (error) {
    // Error silencioso al guardar preferencias
  }
}

// Método para cambiar vista
const setViewMode = (mode) => {
  viewMode.value = mode
  saveUserPreferences()
}

// Validaciones del formulario
const formErrors = ref({})

const validateForm = () => {
  formErrors.value = {}
  
  // Validar nombre (requerido)
  if (!customerForm.value.name || !customerForm.value.name.trim()) {
    formErrors.value.name = 'El nombre es requerido'
  }
  
  // Validar email (requerido y formato)
  if (!customerForm.value.email || !customerForm.value.email.trim()) {
    formErrors.value.email = 'El email es requerido'
  } else if (!/\S+@\S+\.\S+/.test(customerForm.value.email)) {
    formErrors.value.email = 'El formato del email no es válido'
  }
  
  // Validar teléfono (requerido)
  if (!customerForm.value.phone || !customerForm.value.phone.trim()) {
    formErrors.value.phone = 'El teléfono es requerido'
  }
  
  // Validar número de documento (requerido)
  if (!customerForm.value.document_number || !customerForm.value.document_number.trim()) {
    formErrors.value.document_number = 'El número de documento es requerido'
  }
  
  // Validar dirección (opcional pero si existe debe tener contenido)
  if (customerForm.value.address && !customerForm.value.address.trim()) {
    formErrors.value.address = 'La dirección no puede estar vacía'
  }
  
  // Validar ciudad (opcional pero si existe debe tener contenido)
  if (customerForm.value.city && !customerForm.value.city.trim()) {
    formErrors.value.city = 'La ciudad no puede estar vacía'
  }
  
  // Validar límite de crédito (no negativo)
  if (customerForm.value.credit_limit < 0) {
    formErrors.value.credit_limit = 'El límite de crédito no puede ser negativo'
  }
  
  return Object.keys(formErrors.value).length === 0
}

// Computed properties
const filteredCustomers = computed(() => {
  // 🛡️ Filtrar el Consumidor Final (cliente del sistema) - No debe mostrarse en la lista
  let filtered = customers.value.filter(c => c.document_number !== '222222222222' && c.document_number !== '00000000000' && c.email !== 'general@sistema.local')

  if (searchTerm.value) {
    const term = searchTerm.value.toLowerCase()
    filtered = filtered.filter(customer => 
      (customer.name || '').toLowerCase().includes(term) ||
      (customer.email || '').toLowerCase().includes(term) ||
      (customer.phone || '').includes(term) ||
      (customer.document_number || '').includes(term)
    )
  }

  if (statusFilter.value) {
    filtered = filtered.filter(customer => {
      if (statusFilter.value === 'active') return customer.active
      if (statusFilter.value === 'inactive') return !customer.active
      return true
    })
  }

  if (cityFilter.value) {
    filtered = filtered.filter(customer => customer.city === cityFilter.value)
  }

  return filtered
})

// Computed properties para paginación
const totalItems = computed(() => filteredCustomers.value.length)
const totalPages = computed(() => Math.ceil(totalItems.value / itemsPerPage.value))
const startItem = computed(() => (currentPage.value - 1) * itemsPerPage.value + 1)
const endItem = computed(() => Math.min(currentPage.value * itemsPerPage.value, totalItems.value))

const paginatedCustomers = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage.value
  const end = start + itemsPerPage.value
  return filteredCustomers.value.slice(start, end)
})

const visiblePages = computed(() => {
  const delta = 2
  const range = []
  const rangeWithDots = []

  for (let i = Math.max(2, currentPage.value - delta); 
       i <= Math.min(totalPages.value - 1, currentPage.value + delta); 
       i++) {
    range.push(i)
  }

  if (currentPage.value - delta > 2) {
    rangeWithDots.push(1, '...')
  } else {
    rangeWithDots.push(1)
  }

  rangeWithDots.push(...range)

  if (currentPage.value + delta < totalPages.value - 1) {
    rangeWithDots.push('...', totalPages.value)
  } else {
    rangeWithDots.push(totalPages.value)
  }

  return rangeWithDots.filter((item, index, array) => array.indexOf(item) === index && item !== '...' && item <= totalPages.value)
})

const activeCustomers = computed(() => customers.value.filter(c => c.active).length)

const totalSales = computed(() => 
  customers.value.reduce((sum, c) => sum + parseFloat(c.total_purchases || 0), 0)
)

const totalDebt = computed(() => 
  customers.value.reduce((sum, c) => sum + parseFloat(c.current_debt || 0), 0)
)

const averagePurchase = computed(() => {
  const totalCustomers = customers.value.length
  return totalCustomers > 0 ? totalSales.value / totalCustomers : 0
})

const uniqueCities = computed(() => {
  const cities = customers.value.map(c => c.city).filter(Boolean)
  return [...new Set(cities)].sort()
})

// Métodos
const loadCustomers = async () => {
  try {
    loading.value = true
    const response = await customersService.getAll()
    customers.value = response.data || []
  } catch (error) {
    console.error('Error cargando clientes:', error)
    alert('Error al cargar los clientes')
  } finally {
    loading.value = false
  }
}

const refreshCustomers = async () => {
  await loadCustomers()
}

const openCreateModal = () => {
  isEditing.value = false
  formErrors.value = {} // Limpiar errores
  customerForm.value = {
    name: '',
    email: '',
    phone: '',
    address: '',
    city: '',
    document_type: 'CC',
    document_number: '',
    birth_date: '',
    gender: '',
    credit_limit: 0,
    current_debt: 0,
    active: true,
    credit_active: false,
    credit_photo: ''
  }
  showCustomerModal.value = true
}

// �️ Funciones para preview de foto del cliente
const openPhotoPreview = (photoUrl, customerName) => {
  previewPhotoUrl.value = photoUrl
  previewPhotoName.value = customerName
  showPhotoPreviewModal.value = true
}

const closePhotoPreview = () => {
  showPhotoPreviewModal.value = false
  previewPhotoUrl.value = ''
  previewPhotoName.value = ''
}

// Función para verificar si el plan es básico/free
const isBasicPlan = () => {
  const currentPlan = appStore.tenantPlan || 'free_trial'
  return ['free_trial', 'free', 'basic'].includes(currentPlan)
}

// Función para navegar a planes
const navigateToPlans = () => {
  showPremiumModal.value = false
  navigateToModule('settings', { section: 'plan' })
}
// 📷 Funciones para manejo de foto del cliente
const photoInput = ref(null)

const triggerPhotoUpload = () => {
  // Validar plan ANTES de abrir el selector de archivos
  if (isBasicPlan()) {
    premiumFeatureName.value = 'Subir Foto del Cliente'
    showPremiumModal.value = true
    return
  }
  
  // Si el plan es premium, abrir el selector
  photoInput.value?.click()
}

const handlePhotoUpload = (event) => {
  const file = event.target.files[0]
  if (!file) return
  
  // Validar tipo de archivo
  if (!file.type.startsWith('image/')) {
    showError('Por favor selecciona una imagen válida')
    return
  }
  
  // Validar tamaño (max 5MB)
  if (file.size > 5 * 1024 * 1024) {
    showError('La imagen no puede superar 5MB')
    return
  }
  
  // Convertir a base64
  const reader = new FileReader()
  reader.onload = (e) => {
    // Redimensionar imagen si es muy grande
    const img = new Image()
    img.onload = () => {
      const canvas = document.createElement('canvas')
      const MAX_SIZE = 400
      let width = img.width
      let height = img.height
      
      if (width > height) {
        if (width > MAX_SIZE) {
          height *= MAX_SIZE / width
          width = MAX_SIZE
        }
      } else {
        if (height > MAX_SIZE) {
          width *= MAX_SIZE / height
          height = MAX_SIZE
        }
      }
      
      canvas.width = width
      canvas.height = height
      const ctx = canvas.getContext('2d')
      ctx.drawImage(img, 0, 0, width, height)
      
      customerForm.value.credit_photo = canvas.toDataURL('image/jpeg', 0.8)
      showSuccess('Foto agregada correctamente')
    }
    img.src = e.target.result
  }
  reader.readAsDataURL(file)
}

const openCamera = async () => {
  // Validar plan antes de permitir usar cámara
  if (isBasicPlan()) {
    premiumFeatureName.value = 'Usar Cámara'
    showPremiumModal.value = true
    return
  }
  
  try {
    const stream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'user' } })
    
    // Crear modal de cámara
    const modal = document.createElement('div')
    modal.className = 'fixed inset-0 bg-black/90 flex flex-col items-center justify-center z-[99999] p-4'
    modal.innerHTML = `
      <div class="bg-zinc-900 rounded-md overflow-hidden max-w-lg w-full">
        <div class="p-4 border-b border-zinc-700 flex justify-between items-center">
          <h3 class="text-white font-bold">Tomar Foto</h3>
          <button id="closeCamera" class="text-zinc-400 hover:text-white">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
          </button>
        </div>
        <video id="cameraVideo" autoplay playsinline class="w-full"></video>
        <div class="p-4 flex justify-center">
          <button id="capturePhoto" class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
            Capturar
          </button>
        </div>
      </div>
    `
    document.body.appendChild(modal)
    
    const video = modal.querySelector('#cameraVideo')
    video.srcObject = stream
    
    // Cerrar cámara
    modal.querySelector('#closeCamera').onclick = () => {
      stream.getTracks().forEach(track => track.stop())
      modal.remove()
    }
    
    // Capturar foto
    modal.querySelector('#capturePhoto').onclick = () => {
      const canvas = document.createElement('canvas')
      canvas.width = video.videoWidth
      canvas.height = video.videoHeight
      canvas.getContext('2d').drawImage(video, 0, 0)
      
      // Redimensionar
      const MAX_SIZE = 400
      const resizedCanvas = document.createElement('canvas')
      let width = canvas.width
      let height = canvas.height
      
      if (width > height) {
        if (width > MAX_SIZE) {
          height *= MAX_SIZE / width
          width = MAX_SIZE
        }
      } else {
        if (height > MAX_SIZE) {
          width *= MAX_SIZE / height
          height = MAX_SIZE
        }
      }
      
      resizedCanvas.width = width
      resizedCanvas.height = height
      resizedCanvas.getContext('2d').drawImage(canvas, 0, 0, width, height)
      
      customerForm.value.credit_photo = resizedCanvas.toDataURL('image/jpeg', 0.8)
      stream.getTracks().forEach(track => track.stop())
      modal.remove()
      showSuccess('Foto capturada correctamente')
    }
  } catch (error) {
    console.error('Error accediendo a la cámara:', error)
    showError('No se pudo acceder a la cámara. Verifica los permisos.')
  }
}

// Manejar click en checkbox de crédito
const handleCreditCheckboxClick = (event) => {
  if (!isCreditiendaEnabled.value) {
    event.preventDefault()
    showCreditiendaUpgradeModal()
  }
}

const editCustomer = (customer) => {
  isEditing.value = true
  customerForm.value = { ...customer }
  showCustomerModal.value = true
}

const closeCustomerModal = () => {
  showCustomerModal.value = false
  isEditing.value = false
}

const saveCustomer = async () => {
  // Validar formulario antes de guardar
  if (!validateForm()) {
    showError('Por favor corrige los errores en el formulario')
    return
  }
  
  try {
    loading.value = true
    
    // Preparar datos del cliente
    const customerData = { ...customerForm.value }
    
    if (isEditing.value) {
      await customersService.update(customerForm.value.id, customerData)
      showSuccess('Cliente actualizado exitosamente')
    } else {
      await customersService.create(customerData)
      showSuccess('Cliente creado exitosamente')
    }
    
    await loadCustomers()
    closeCustomerModal()
  } catch (error) {
    console.error('Error guardando cliente:', error)
    showError('Error al guardar el cliente: ' + (error.message || 'Error desconocido'))
  } finally {
    loading.value = false
  }
}

const deleteCustomer = async (customer) => {
  customerToDelete.value = customer
  loading.value = true
  
  try {
    // Verificar PRIMERO si tiene facturas antes de mostrar cualquier modal
    const response = await customersService.getCustomerInvoices(customer.id)
    const hasInvoices = response?.data && Array.isArray(response.data) && response.data.length > 0
    
    loading.value = false
    
    if (hasInvoices) {
      // Tiene facturas → Mostrar modal rojo directamente
      showCannotDeleteModal.value = true
    } else {
      // No tiene facturas → Mostrar modal de confirmación amarillo
      showDeleteConfirmModal.value = true
    }
  } catch (error) {
    loading.value = false
    // Si hay error verificando, mostrar modal de confirmación y dejar que el backend valide
    showDeleteConfirmModal.value = true
  }
}

// Confirmar eliminación del cliente
const confirmDeleteCustomer = async () => {
  if (!customerToDelete.value) return
  
  try {
    loading.value = true
    showDeleteConfirmModal.value = false
    
    await customersService.delete(customerToDelete.value.id)
    showSuccess('Cliente eliminado exitosamente')
    await loadCustomers()
    customerToDelete.value = null
  } catch (error) {
    // Si el backend dice que tiene facturas (doble validación por seguridad)
    if (error.response?.status === 400 && error.response?.data?.has_invoices) {
      showCannotDeleteModal.value = true
    } else if (error.message?.includes('facturas asociadas')) {
      showCannotDeleteModal.value = true
    } else {
      showError(error.response?.data?.message || 'Error al eliminar el cliente')
    }
  } finally {
    loading.value = false
  }
}

// Cancelar eliminación
const cancelDeleteCustomer = () => {
  showDeleteConfirmModal.value = false
  showCannotDeleteModal.value = false
  customerToDelete.value = null
}

const getCustomerColor = (name) => {
  const colors = ['#3B82F6', '#10B981', '#F59E0B', '#EF4444', '#8B5CF6', '#EC4899', '#14B8A6', '#F97316']
  const index = name.charCodeAt(0) % colors.length
  return colors[index]
}

const exportCustomers = () => {
  alert('Funcionalidad de exportación próximamente')
}

const formatCurrency = (value) => {
  return new Intl.NumberFormat('es-CO').format(parseFloat(value || 0))
}

// Funciones para el punto de ventas
const createQuickCustomer = async (customerData) => {
  try {
    const response = await customersService.create(customerData)
    await loadCustomers()
    return response
  } catch (error) {
    throw error
  }
}

// Exportar funciones para uso en punto de ventas
defineExpose({
  createQuickCustomer,
  loadCustomers,
  customers
})

// Watchers para guardar preferencias automáticamente
watch(viewMode, (newValue, oldValue) => {
  if (oldValue !== undefined && newValue !== oldValue) {
    saveUserPreferences()
  }
}, { immediate: false })

watch(itemsPerPage, (newValue, oldValue) => {
  if (oldValue !== undefined && newValue !== oldValue) {
    saveUserPreferences()
  }
}, { immediate: false })

watch(statusFilter, (newValue, oldValue) => {
  if (oldValue !== undefined && newValue !== oldValue) {
    saveUserPreferences()
  }
}, { immediate: false })

// ========== CONTEXTO DE IA - CONCIENCIA DE PANTALLA ==========
const instruccionesClientes = {
  modulo: 'Módulo de Clientes: Vista Master-Detail que muestra la lista de clientes a la izquierda y los detalles del cliente seleccionado a la derecha.',
  panelIzquierdo: 'Panel izquierdo: Lista de clientes con búsqueda, filtro por estado (activos/inactivos), nombre, documento, total de compras.',
  panelDerecho: 'Panel derecho: Información del cliente seleccionado con tabs de Información General e Historial de Compras.',
  informacionGeneral: 'Pestaña Información General: Datos personales (documento, dirección, ciudad, fecha nacimiento), Estado de Crédito (cupo, deuda actual, % utilizado).',
  historialCompras: 'Pestaña Historial de Compras: Lista de facturas del cliente ordenadas por fecha, con número de factura, fecha, estado de pago y total.',
  acciones: 'Acciones disponibles: Nuevo Cliente, Editar cliente, Eliminar cliente, Ver historial de compras, Buscar cliente.',
  camposObligatorios: 'CAMPOS OBLIGATORIOS para crear cliente: 1) Nombre completo, 2) Número de documento (CC/cédula), 3) Teléfono, 4) Email. Sin estos 4 campos no se puede crear el cliente.',
  flujoCreacion: 'Para crear un cliente: 1) Ejecutar crearNuevoCliente, 2) Usar llenarCampoCliente para CADA campo (nombre, documento, telefono, email), 3) Ejecutar guardarCliente cuando todos los campos estén llenos.'
}

// Watcher para actualizar contexto de IA cuando cambian los datos
watch(
  [customers, selectedCustomer, activeTab, customerInvoices, loading, showCustomerModal, customerForm],
  () => {
    // Datos base de clientes
    const clientesData = {
      totalClientes: customers.value.length,
      clientesActivos: customers.value.filter(c => c.active).length,
      clientesInactivos: customers.value.filter(c => !c.active).length,
      totalVentas: customers.value.reduce((sum, c) => sum + parseFloat(c.total_purchases || 0), 0),
      totalDeudas: customers.value.reduce((sum, c) => sum + parseFloat(c.current_debt || 0), 0),
      cargando: loading.value,
      filtroActivo: statusFilter.value || 'todos',
      terminoBusqueda: searchTerm.value || '',
      pestanaActiva: activeTab.value === 'info' ? 'Información General' : 'Historial de Compras',
      
      // Estado del modal de creación/edición
      modalAbierto: showCustomerModal.value,
      modoEdicion: isEditing.value,
      
      // Si el modal está abierto, mostrar datos del formulario
      formularioActual: showCustomerModal.value ? {
        nombre: customerForm.value.name || '(vacío)',
        documento: customerForm.value.document_number || '(vacío)',
        tipoDocumento: customerForm.value.document_type || 'CC',
        email: customerForm.value.email || '(vacío)',
        telefono: customerForm.value.phone || '(vacío)',
        direccion: customerForm.value.address || '(vacío)',
        ciudad: customerForm.value.city || '(vacío)',
        camposCompletos: !!(customerForm.value.name && customerForm.value.document_number && customerForm.value.email && customerForm.value.phone),
        camposFaltantes: [
          !customerForm.value.name?.trim() ? 'nombre' : null,
          !customerForm.value.document_number?.trim() ? 'documento/CC' : null,
          !customerForm.value.email?.trim() ? 'email' : null,
          !customerForm.value.phone?.trim() ? 'teléfono' : null
        ].filter(Boolean)
      } : null,
      
      // Lista de clientes (primeros 15 para no sobrecargar)
      listaClientes: filteredCustomers.value.slice(0, 15).map(c => ({
        id: c.id,
        nombre: c.name,
        documento: `${c.document_type}: ${c.document_number}`,
        telefono: c.phone || 'Sin teléfono',
        email: c.email || 'Sin email',
        estado: c.active ? 'Activo' : 'Inactivo',
        totalCompras: parseFloat(c.total_purchases || 0),
        deudaActual: parseFloat(c.current_debt || 0),
        ciudad: c.city || 'Sin ciudad'
      })),
      
      instrucciones: instruccionesClientes
    }
    
    // Si hay un cliente seleccionado, agregar sus detalles completos
    if (selectedCustomer.value) {
      const cliente = selectedCustomer.value
      clientesData.clienteSeleccionado = {
        id: cliente.id,
        nombre: cliente.name,
        documento: `${cliente.document_type}: ${cliente.document_number}`,
        telefono: cliente.phone || 'Sin teléfono',
        email: cliente.email || 'Sin email',
        direccion: cliente.address || 'Sin dirección',
        ciudad: cliente.city || 'Sin ciudad',
        fechaNacimiento: cliente.birth_date || 'No registrada',
        estado: cliente.active ? 'Activo' : 'Inactivo',
        
        // Estadísticas de compras
        totalCompras: parseFloat(cliente.total_purchases || 0),
        totalOrdenes: customerInvoices.value.length,
        ticketPromedio: customerInvoices.value.length > 0 
          ? customerInvoices.value.reduce((sum, inv) => sum + parseFloat(inv.total || 0), 0) / customerInvoices.value.length 
          : 0,
        
        // Estado de crédito
        credito: {
          activo: cliente.credit_active || false,
          cupoTotal: parseFloat(cliente.credit_limit || 0),
          deudaActual: parseFloat(cliente.current_debt || 0),
          porcentajeUsado: cliente.credit_limit > 0 
            ? Math.round((parseFloat(cliente.current_debt || 0) / parseFloat(cliente.credit_limit)) * 100) 
            : 0
        },
        
        // Historial de compras (últimas 10)
        historialCompras: customerInvoices.value.slice(0, 10).map(inv => ({
          numeroFactura: inv.invoice_number || inv.id,
          fecha: inv.date || inv.created_at,
          estado: inv.payment_status || 'pendiente',
          total: parseFloat(inv.total || 0),
          items: inv.items?.length || 0
        }))
      }
    }
    
    // Resumen rápido para respuestas comunes
    clientesData.resumenRapido = {
      cuantosClientes: `Hay ${clientesData.totalClientes} cliente(s) en total, ${clientesData.clientesActivos} activo(s) y ${clientesData.clientesInactivos} inactivo(s).`,
      ventasTotales: `Las ventas totales a clientes suman $${clientesData.totalVentas.toLocaleString()}.`,
      deudasTotales: `La deuda total de clientes es de $${clientesData.totalDeudas.toLocaleString()}.`,
      clienteSeleccionado: selectedCustomer.value 
        ? `Cliente seleccionado: ${selectedCustomer.value.name} con ${customerInvoices.value.length} compras registradas.`
        : 'No hay cliente seleccionado. Selecciona uno de la lista para ver sus detalles.',
      comoCrearCliente: 'Para crear un nuevo cliente: haz clic en el botón "Nuevo Cliente" en la esquina superior derecha.',
      comoEditarCliente: 'Para editar un cliente: primero selecciónalo de la lista, luego haz clic en el botón "Editar" en su ficha de detalles.',
      comoVerHistorial: 'Para ver el historial de compras: selecciona un cliente y haz clic en la pestaña "Historial de Compras".'
    }

    // Actualizar el contexto de pantalla para la IA
    uiContextStore.setScreenData(clientesData)
    
    // Registrar acciones disponibles para la IA
    uiContextStore.registerAction('seleccionarClientePorNombre', async ({ nombre }) => {
      const cliente = customers.value.find(c => 
        c.name.toLowerCase().includes(nombre.toLowerCase())
      )
      if (cliente) {
        selectCustomer(cliente)
        return { success: true, message: `Cliente "${cliente.name}" seleccionado` }
      }
      return { success: false, message: `No se encontró cliente con nombre "${nombre}"` }
    })
    
    uiContextStore.registerAction('buscarCliente', ({ texto }) => {
      searchTerm.value = texto
      return { success: true, message: `Buscando clientes con: "${texto}"` }
    })
    
    uiContextStore.registerAction('filtrarClientesPorEstado', ({ estado }) => {
      if (estado === 'activos') {
        statusFilter.value = 'active'
      } else if (estado === 'inactivos') {
        statusFilter.value = 'inactive'
      } else {
        statusFilter.value = ''
      }
      return { success: true, message: `Filtrando clientes por: ${estado}` }
    })
    
    uiContextStore.registerAction('cambiarPestanaCliente', ({ pestana }) => {
      if (pestana === 'info' || pestana === 'informacion' || pestana === 'general') {
        activeTab.value = 'info'
        return { success: true, message: 'Mostrando información general del cliente' }
      } else if (pestana === 'historial' || pestana === 'compras') {
        activeTab.value = 'history'
        return { success: true, message: 'Mostrando historial de compras del cliente' }
      }
      return { success: false, message: 'Pestaña no reconocida. Usa "info" o "historial"' }
    })
    
    uiContextStore.registerAction('editarClienteSeleccionado', () => {
      if (selectedCustomer.value) {
        editCustomer(selectedCustomer.value)
        return { success: true, message: `Abriendo editor para ${selectedCustomer.value.name}` }
      }
      return { success: false, message: 'Primero selecciona un cliente para editar' }
    })
    
    uiContextStore.registerAction('crearNuevoCliente', () => {
      openCreateModal()
      return { success: true, message: 'Formulario abierto. Ahora necesito los datos: nombre completo, número de documento (CC), teléfono y email son OBLIGATORIOS.' }
    })
    
    // 📝 Acción para llenar campos del formulario visualmente
    uiContextStore.registerAction('llenarCampoCliente', ({ campo, valor }) => {
      if (!showCustomerModal.value) {
        return { success: false, message: 'Primero abre el formulario con crearNuevoCliente' }
      }
      
      const camposValidos = {
        'nombre': 'name',
        'name': 'name',
        'email': 'email',
        'correo': 'email',
        'telefono': 'phone',
        'phone': 'phone',
        'celular': 'phone',
        'direccion': 'address',
        'address': 'address',
        'ciudad': 'city',
        'city': 'city',
        'documento': 'document_number',
        'document_number': 'document_number',
        'cedula': 'document_number',
        'cc': 'document_number',
        'tipo_documento': 'document_type',
        'document_type': 'document_type',
        'fecha_nacimiento': 'birth_date',
        'birth_date': 'birth_date',
        'genero': 'gender',
        'gender': 'gender',
        'limite_credito': 'credit_limit',
        'credit_limit': 'credit_limit'
      }
      
      const campoNormalizado = campo.toLowerCase().trim()
      const campoReal = camposValidos[campoNormalizado]
      
      if (!campoReal) {
        return { success: false, message: `Campo "${campo}" no reconocido. Campos válidos: nombre, documento/cedula/cc, email, telefono, direccion, ciudad, genero, limite_credito` }
      }
      
      // Actualizar el campo del formulario
      customerForm.value[campoReal] = valor
      
      return { 
        success: true, 
        message: `Campo "${campo}" actualizado a "${valor}"`,
        formularioActual: {
          nombre: customerForm.value.name,
          documento: customerForm.value.document_number,
          email: customerForm.value.email,
          telefono: customerForm.value.phone
        }
      }
    })
    
    // 💾 Acción para guardar el cliente
    uiContextStore.registerAction('guardarCliente', async () => {
      if (!showCustomerModal.value) {
        return { success: false, message: 'No hay formulario abierto para guardar' }
      }
      
      // Verificar campos obligatorios
      const faltantes = []
      if (!customerForm.value.name?.trim()) faltantes.push('nombre')
      if (!customerForm.value.document_number?.trim()) faltantes.push('número de documento (CC)')
      if (!customerForm.value.email?.trim()) faltantes.push('email')
      if (!customerForm.value.phone?.trim()) faltantes.push('teléfono')
      
      if (faltantes.length > 0) {
        return { 
          success: false, 
          message: `Faltan campos obligatorios: ${faltantes.join(', ')}. Por favor proporciona estos datos.` 
        }
      }
      
      try {
        await saveCustomer()
        return { 
          success: true, 
          message: `Cliente "${customerForm.value.name}" creado exitosamente. La lista se ha actualizado.` 
        }
      } catch (error) {
        return { success: false, message: `Error al guardar: ${error.message}` }
      }
    })
    
    // ❌ Acción para cerrar el modal
    uiContextStore.registerAction('cerrarModalCliente', () => {
      closeCustomerModal()
      return { success: true, message: 'Modal cerrado' }
    })
  },
  { immediate: true, deep: true }
)

// Inicialización
onMounted(async () => {
  // 🔧 Cargar preferencias del usuario primero
  loadUserPreferences()
  
  await loadCustomers()
})

// 🔄 AUTO-REFRESH al reactivar el componente
onActivated(async () => {
  await loadCustomers()
})
</script>

<style scoped>
/* 🎨 ESTILOS EMPRESARIALES OBLIGATORIOS */

/* Animación de entrada principal */
@keyframes fade-in {
  from { 
    opacity: 0; 
    transform: translateY(10px); 
  }
  to { 
    opacity: 1; 
    transform: translateY(0); 
  }
}

.animate-fade-in {
  animation: fade-in 0.3s ease-out;
}

/* Transiciones suaves globales */
* {
  transition: background-color 0.2s, border-color 0.2s, color 0.2s;
}

/* Hover effects para tarjetas - Gemini sin transform */
.group:hover {
  opacity: 0.98;
}

/* Focus states mejorados - Gemini */
input:focus,
select:focus {
  box-shadow: 0 0 0 2px rgba(26, 115, 232, 0.2);
}

/* Botones sin transform */
button:hover {
  opacity: 0.95;
}

button:active {
  opacity: 1;
}

/* Estados de las métricas sin sombras */
.bg-gradient-to-br:hover {
  opacity: 0.98;
}

/* Animaciones de carga */
.animate-spin {
  animation: spin 1s linear infinite;
}

@keyframes spin {
  from { transform: rotate(0deg); }
  to { transform: rotate(360deg); }
}

/* Grid responsive mejorado */
@media (max-width: 768px) {
  .grid {
    gap: 1rem;
  }
}

/* Scrollbar personalizado - Gemini */
.overflow-x-auto::-webkit-scrollbar {
  height: 6px;
}

.overflow-x-auto::-webkit-scrollbar-track {
  background: #f1f3f4;
  border-radius: 3px;
}

.dark .overflow-x-auto::-webkit-scrollbar-track {
  background: #282a2c;
}

.overflow-x-auto::-webkit-scrollbar-thumb {
  background: #dadce0;
  border-radius: 3px;
}

.dark .overflow-x-auto::-webkit-scrollbar-thumb {
  background: #5f6368;
}

.overflow-x-auto::-webkit-scrollbar-thumb:hover {
  background: #bdc1c6;
}

.dark .overflow-x-auto::-webkit-scrollbar-thumb:hover {
  background: #9aa0a6;
}

/* Estados de formulario mejorados - Gemini */
.border-gray-300:focus-within {
  border-color: #1a73e8;
  box-shadow: 0 0 0 2px rgba(26, 115, 232, 0.15);
}

/* Tipografía optimizada - Gemini */
.font-sans {
  font-family: 'Google Sans', 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
}

/* Gemini no usa sombras */
.shadow-sm,
.shadow-md:hover {
  box-shadow: none;
}

/* Estilos para estados vacíos */
.empty-state {
  min-height: 200px;
  display: flex;
  align-items: center;
  justify-content: center;
}

/* 🎭 Transiciones Modal - Animación suave entrada/salida Gemini */
.modal-enter-active,
.modal-leave-active {
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.modal-enter-from,
.modal-leave-to {
  opacity: 0;
}

.modal-enter-from .bg-\[\#f8f9fa\],
.modal-enter-from .bg-white,
.modal-leave-to .bg-\[\#f8f9fa\],
.modal-leave-to .bg-white {
  transform: scale(0.95) translateY(20px);
}
</style>