<template>
  <div class="min-h-screen font-sans bg-gradient-to-b from-gray-50 via-gray-100 to-gray-200 dark:bg-gradient-to-b dark:from-[#141417] dark:via-slate-900 dark:to-[#0a0a0c] transition-colors duration-300 px-4 lg:px-6">
    <div class="p-3 lg:p-4 space-y-4 pb-6 animate-fade-in">
      
      <!-- NIVEL 1: Header Minimalista -->
      <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">CrediTienda</h1>
      </div>

      <!-- NIVEL 2: KPIs Ejecutivos - Estilo Fantasma -->
      <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
        
        <!-- KPI: Total por Cobrar -->
        <div class="bg-white/80 dark:bg-zinc-800/40 backdrop-blur-sm rounded-2xl px-5 py-4 border-0 hover:bg-white dark:hover:bg-zinc-800/60 transition-all duration-300 shadow-[0_4px_20px_-4px_rgba(0,0,0,0.08)] hover:shadow-[0_8px_30px_-4px_rgba(0,0,0,0.12)] dark:shadow-[0_4px_20px_-4px_rgba(0,0,0,0.3)] dark:hover:shadow-[0_8px_30px_-4px_rgba(0,0,0,0.4)]">
          <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-rose-50 dark:bg-rose-500/10 rounded-xl flex items-center justify-center flex-shrink-0">
              <svg class="w-6 h-6 text-rose-500 dark:text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
              </svg>
            </div>
            <div class="flex-1 min-w-0">
              <p class="text-[11px] font-semibold text-gray-400 dark:text-zinc-500 uppercase tracking-wider">Total por Cobrar</p>
              <p class="text-2xl font-bold text-gray-800 dark:text-white mt-0.5">${{ formatCurrency(totalDebt) }}</p>
            </div>
          </div>
        </div>

        <!-- KPI: Clientes con Crédito -->
        <div class="bg-white/80 dark:bg-zinc-800/40 backdrop-blur-sm rounded-2xl px-5 py-4 border-0 hover:bg-white dark:hover:bg-zinc-800/60 transition-all duration-300 shadow-[0_4px_20px_-4px_rgba(0,0,0,0.08)] hover:shadow-[0_8px_30px_-4px_rgba(0,0,0,0.12)] dark:shadow-[0_4px_20px_-4px_rgba(0,0,0,0.3)] dark:hover:shadow-[0_8px_30px_-4px_rgba(0,0,0,0.4)]">
          <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-blue-50 dark:bg-blue-500/10 rounded-xl flex items-center justify-center flex-shrink-0">
              <svg class="w-6 h-6 text-blue-500 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
              </svg>
            </div>
            <div class="flex-1 min-w-0">
              <p class="text-[11px] font-semibold text-gray-400 dark:text-zinc-500 uppercase tracking-wider">Clientes Activos</p>
              <p class="text-2xl font-bold text-gray-800 dark:text-white mt-0.5">{{ customersWithDebt }}</p>
            </div>
          </div>
        </div>

        <!-- KPI: Recaudado Hoy -->
        <div class="bg-white/80 dark:bg-zinc-800/40 backdrop-blur-sm rounded-2xl px-5 py-4 border-0 hover:bg-white dark:hover:bg-zinc-800/60 transition-all duration-300 shadow-[0_4px_20px_-4px_rgba(0,0,0,0.08)] hover:shadow-[0_8px_30px_-4px_rgba(0,0,0,0.12)] dark:shadow-[0_4px_20px_-4px_rgba(0,0,0,0.3)] dark:hover:shadow-[0_8px_30px_-4px_rgba(0,0,0,0.4)]">
          <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-emerald-50 dark:bg-emerald-500/10 rounded-xl flex items-center justify-center flex-shrink-0">
              <svg class="w-6 h-6 text-emerald-500 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
              </svg>
            </div>
            <div class="flex-1 min-w-0">
              <p class="text-[11px] font-semibold text-gray-400 dark:text-zinc-500 uppercase tracking-wider">Recaudado Hoy</p>
              <p class="text-2xl font-bold text-gray-800 dark:text-white mt-0.5">${{ formatCurrency(todayPayments) }}</p>
            </div>
          </div>
        </div>

        <!-- KPI: Mora Promedio -->
        <div class="bg-white/80 dark:bg-zinc-800/40 backdrop-blur-sm rounded-2xl px-5 py-4 border-0 hover:bg-white dark:hover:bg-zinc-800/60 transition-all duration-300 shadow-[0_4px_20px_-4px_rgba(0,0,0,0.08)] hover:shadow-[0_8px_30px_-4px_rgba(0,0,0,0.12)] dark:shadow-[0_4px_20px_-4px_rgba(0,0,0,0.3)] dark:hover:shadow-[0_8px_30px_-4px_rgba(0,0,0,0.4)]">
          <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-amber-50 dark:bg-amber-500/10 rounded-xl flex items-center justify-center flex-shrink-0">
              <svg class="w-6 h-6 text-amber-500 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
              </svg>
            </div>
            <div class="flex-1 min-w-0">
              <p class="text-[11px] font-semibold text-gray-400 dark:text-zinc-500 uppercase tracking-wider">Mora Promedio</p>
              <p class="text-2xl font-bold text-gray-800 dark:text-white mt-0.5">{{ averageDaysOverdue }} días</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Master-Detail Layout Enterprise: 30/70 -->
      <div class="bg-white dark:bg-zinc-900 rounded-2xl overflow-hidden border border-gray-300 dark:border-zinc-800 shadow-xl dark:shadow-black/50 transition-colors duration-300" style="height: calc(100vh - 210px); min-height: 550px;">
        <div class="grid grid-cols-1 lg:grid-cols-10 h-full">
        
        <!-- PANEL IZQUIERDO: Lista de Clientes (30%) -->
        <div class="lg:col-span-3 overflow-hidden flex flex-col border-r border-gray-200 dark:border-zinc-800 transition-colors duration-300">
          
          <!-- Header con búsqueda -->
          <div class="p-4 border-b border-gray-200 dark:border-zinc-800 bg-gray-50/50 dark:bg-zinc-900">
            <!-- Búsqueda -->
            <div class="relative mb-3">
              <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400 dark:text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
              </svg>
              <input
                v-model="searchTerm"
                type="text"
                placeholder="Buscar por nombre o documento..."
                class="w-full pl-10 pr-4 py-3 text-sm rounded-xl border-2 border-gray-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-gray-900 dark:text-zinc-200 placeholder-gray-400 dark:placeholder-zinc-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent transition-all duration-300">
            </div>
            
            <!-- Filtro de estado -->
            <select
              v-model="statusFilter"
              class="w-full px-3 py-2 text-sm rounded-xl border border-gray-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-gray-700 dark:text-zinc-300 font-medium focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 transition-colors duration-300">
              <option value="">📊 Todos los estados</option>
              <option value="debt">💰 Con Deuda</option>
              <option value="overdue">⚠️ En Mora</option>
              <option value="critical">🔴 Crítico</option>
            </select>
          </div>
          
          <!-- Lista de clientes estilo WhatsApp -->
          <div class="flex-1 overflow-y-auto bg-white dark:bg-zinc-900 px-2">
            
            <!-- Loading state -->
            <div v-if="loading" class="flex items-center justify-center py-12">
              <div class="w-6 h-6 border-2 border-blue-600 border-t-transparent rounded-full animate-spin"></div>
              <span class="ml-3 text-sm text-gray-500 dark:text-zinc-400">Cargando clientes...</span>
            </div>
            
            <!-- Empty state -->
            <div v-else-if="filteredCustomers.length === 0" class="flex flex-col items-center justify-center py-12 text-center px-4">
              <svg class="w-12 h-12 text-gray-300 dark:text-zinc-600 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
              </svg>
              <p class="text-sm font-semibold text-gray-600 dark:text-zinc-300">Sin clientes</p>
              <p class="text-xs text-gray-500 dark:text-zinc-500 mt-1">No hay clientes con crédito activo</p>
            </div>
            
            <!-- Lista de clientes -->
            <div
              v-else
              v-for="customer in filteredCustomers"
              :key="customer.id"
              @click="selectCustomer(customer)"
              class="px-3 py-3.5 my-1 cursor-pointer transition-all rounded-xl group relative"
              :class="[
                selectedCustomer?.id === customer.id 
                  ? 'bg-indigo-50 dark:bg-indigo-500/10 shadow-sm' 
                  : 'bg-white dark:bg-zinc-900 hover:bg-gray-50 dark:hover:bg-zinc-800/60'
              ]"
            >
              <!-- Borde izquierdo de selección -->
              <div 
                v-if="selectedCustomer?.id === customer.id"
                class="absolute left-0 top-2 bottom-2 w-1 bg-indigo-500 rounded-r-full"
              ></div>
              
              <div class="flex items-center gap-3">
                <!-- Avatar con inicial o foto - CLICKABLE -->
                <div 
                  @click.stop="customer.credit_photo && openPhotoPreview(customer.credit_photo, customer.name)"
                  class="w-11 h-11 rounded-full flex items-center justify-center flex-shrink-0 overflow-hidden transition-transform duration-200"
                  :class="[
                    customer.credit_photo ? 'cursor-pointer hover:scale-110 ring-2 ring-transparent hover:ring-blue-400' : getAvatarColor(customer)
                  ]">
                  <img v-if="customer.credit_photo" 
                       :src="customer.credit_photo" 
                       :alt="customer.name"
                       class="w-full h-full object-cover" />
                  <span v-else class="text-sm font-bold text-white">{{ customer.name.charAt(0).toUpperCase() }}</span>
                </div>
                
                <!-- Info del cliente -->
                <div class="flex-1 min-w-0">
                  <div class="flex items-center gap-2">
                    <p class="text-[13px] font-semibold text-gray-800 dark:text-zinc-200 truncate group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">
                      {{ customer.name }}
                    </p>
                    <span class="text-[9px] font-bold px-2 py-0.5 rounded-full border flex-shrink-0"
                          :class="getStatusColor(customer)">
                      {{ getStatusText(customer) }}
                    </span>
                  </div>
                  <div class="flex items-center gap-2 mt-1">
                    <p class="text-xs text-gray-500 dark:text-zinc-400 truncate">
                      {{ customer.document_type }}: {{ customer.document_number }}
                    </p>
                  </div>
                </div>
                
                <!-- Deuda a la derecha -->
                <div class="text-right flex-shrink-0">
                  <span class="text-sm font-bold" :class="customer.current_debt > 0 ? 'text-rose-600 dark:text-rose-400' : 'text-emerald-600 dark:text-emerald-400'">
                    ${{ formatCurrency(customer.current_debt || 0) }}
                  </span>
                  <p class="text-[10px] text-gray-400 dark:text-zinc-500">Deuda</p>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- PANEL DERECHO: Detalle del Cliente (70%) -->
        <div class="lg:col-span-7 overflow-hidden flex flex-col bg-gray-50/30 dark:bg-zinc-950/30 transition-colors duration-300">
          
          <!-- Estado: No seleccionado - Empty State Profesional -->
          <div v-if="!selectedCustomer" class="flex-1 flex flex-col items-center justify-center p-12 text-center bg-gradient-to-b from-gray-50 via-white to-gray-50 dark:from-zinc-900/50 dark:via-zinc-900/30 dark:to-zinc-900/50 relative">
            
            <!-- Ilustración SVG profesional -->
            <div class="mb-8 relative">
              <div class="absolute inset-0 bg-gradient-to-br from-indigo-200/30 via-transparent to-emerald-200/30 dark:from-indigo-500/10 dark:to-emerald-500/10 rounded-3xl blur-3xl scale-150"></div>
              
              <svg class="w-48 h-48 relative z-10" viewBox="0 0 180 180" fill="none" xmlns="http://www.w3.org/2000/svg">
                <!-- Círculo de fondo -->
                <circle cx="90" cy="90" r="70" class="fill-gray-100 dark:fill-zinc-800/50"/>
                
                <!-- Silueta de persona -->
                <circle cx="90" cy="65" r="25" class="fill-indigo-200 dark:fill-indigo-500/30"/>
                <path d="M50 130 C50 105, 70 95, 90 95 C110 95, 130 105, 130 130" class="fill-indigo-200 dark:fill-indigo-500/30"/>
                
                <!-- Icono de crédito/dinero -->
                <circle cx="120" cy="120" r="25" class="fill-emerald-100 dark:fill-emerald-500/20"/>
                <circle cx="120" cy="120" r="18" class="fill-emerald-500 dark:fill-emerald-400"/>
                <text x="120" y="126" text-anchor="middle" class="fill-white font-bold" style="font-size: 18px; font-family: system-ui;">$</text>
                
                <!-- Badge de verificación -->
                <circle cx="145" cy="55" r="15" class="fill-blue-100 dark:fill-blue-500/20"/>
                <circle cx="145" cy="55" r="10" class="fill-blue-500 dark:fill-blue-400"/>
                <path d="M140 55L143 58L150 51" class="stroke-white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
              </svg>
            </div>
            
            <!-- Texto de bienvenida -->
            <div class="relative z-10 max-w-md">
              <h3 class="text-2xl font-bold text-gray-800 dark:text-white mb-3">
                Portal de Créditos
              </h3>
              <p class="text-sm text-gray-500 dark:text-zinc-400 leading-relaxed mb-2">
                Selecciona un cliente del panel izquierdo para visualizar su información completa, historial de crédito y registrar abonos.
              </p>
              <p class="text-xs text-gray-400 dark:text-zinc-500">
                Gestión segura de créditos para tu negocio.
              </p>
            </div>
            
            <!-- Footer de seguridad -->
            <div class="absolute bottom-6 left-0 right-0 flex items-center justify-center gap-2 text-xs text-gray-400 dark:text-zinc-500">
              <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
              </svg>
              <span>Información financiera protegida y encriptada</span>
            </div>
          </div>

          <!-- Estado: Cliente Seleccionado - Vista de Detalle -->
          <div v-else class="flex-1 flex flex-col overflow-hidden">
            
            <!-- Header del cliente seleccionado -->
            <div class="bg-white dark:bg-zinc-900 border-b border-gray-200 dark:border-zinc-800 p-5 flex-shrink-0">
              <div class="flex items-start justify-between">
                <!-- Info del cliente -->
                <div class="flex items-center gap-4">
                  <!-- Avatar grande - CLICKABLE -->
                  <div 
                    @click="selectedCustomer.credit_photo && openPhotoPreview(selectedCustomer.credit_photo, selectedCustomer.name)"
                    class="w-16 h-16 rounded-xl flex items-center justify-center text-xl font-bold shadow-lg overflow-hidden transition-all duration-200"
                    :class="[
                      selectedCustomer.credit_photo 
                        ? 'bg-gray-100 dark:bg-zinc-800 cursor-pointer hover:scale-105 hover:shadow-xl ring-2 ring-transparent hover:ring-blue-400' 
                        : getAvatarColor(selectedCustomer)
                    ]">
                    <img v-if="selectedCustomer.credit_photo" 
                         :src="selectedCustomer.credit_photo" 
                         :alt="selectedCustomer.name"
                         class="w-full h-full object-cover" />
                    <span v-else class="text-white">{{ selectedCustomer.name?.charAt(0)?.toUpperCase() || '?' }}</span>
                  </div>
                  
                  <div>
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white">
                      {{ selectedCustomer.name }}
                    </h2>
                    <div class="flex items-center gap-3 mt-1">
                      <span class="flex items-center gap-1 text-sm text-gray-500 dark:text-zinc-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                        </svg>
                        {{ selectedCustomer.phone || 'Sin teléfono' }}
                      </span>
                      <span v-if="selectedCustomer.email" class="flex items-center gap-1 text-sm text-gray-500 dark:text-zinc-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                        {{ selectedCustomer.email }}
                      </span>
                    </div>
                    <p v-if="selectedCustomer.address" class="flex items-center gap-1 text-xs text-gray-400 dark:text-zinc-500 mt-1">
                      <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                      </svg>
                      {{ selectedCustomer.address }}
                    </p>
                  </div>
                </div>
                
                <!-- Botones de Acción -->
                <div class="flex items-center gap-2">
                  <!-- Botón Editar Cliente -->
                  <button
                    @click="openEditCustomerModal(selectedCustomer)"
                    class="px-4 py-2.5 rounded-xl text-sm font-bold transition-all duration-300 flex items-center gap-2 bg-blue-100 hover:bg-blue-200 dark:bg-blue-900/30 dark:hover:bg-blue-900/50 text-blue-700 dark:text-blue-400 border border-blue-200 dark:border-blue-800/50"
                    title="Editar información del cliente"
                  >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    Editar
                  </button>
                  
                  <!-- Botón Enviar Recordatorio -->
                  <button
                    @click="sendReminder(selectedCustomer)"
                    :disabled="sendingReminder || selectedCustomer.balance <= 0"
                    class="px-4 py-2.5 rounded-xl text-sm font-bold transition-all duration-300 flex items-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed bg-amber-100 hover:bg-amber-200 dark:bg-amber-900/30 dark:hover:bg-amber-900/50 text-amber-700 dark:text-amber-400 border border-amber-200 dark:border-amber-800/50"
                    title="Enviar recordatorio de pago por WhatsApp"
                  >
                    <svg v-if="sendingReminder" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                      <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                      <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                    </svg>
                    <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                    </svg>
                    {{ sendingReminder ? 'Enviando...' : 'Recordatorio' }}
                  </button>
                  
                  <!-- Botón Registrar Abono -->
                  <button
                    @click="openPaymentModal(selectedCustomer)"
                    :disabled="selectedCustomer.balance <= 0"
                    class="px-5 py-2.5 rounded-xl text-sm font-bold transition-all duration-300 flex items-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed bg-emerald-600 hover:bg-emerald-700 dark:bg-emerald-500 dark:hover:bg-emerald-400 text-white shadow-lg shadow-emerald-500/30"
                  >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    Registrar Abono
                  </button>
                </div>
              </div>
              
              <!-- Cards de resumen financiero -->
              <div class="grid grid-cols-3 gap-4 mt-5">
                <!-- Balance Total -->
                <div class="bg-gradient-to-br from-amber-50 to-orange-50 dark:from-amber-900/30 dark:to-orange-900/20 rounded-xl p-4 border border-amber-100 dark:border-amber-800/50">
                  <p class="text-xs font-medium text-amber-600 dark:text-amber-400 uppercase tracking-wide">Deuda Total</p>
                  <p class="text-2xl font-bold text-amber-700 dark:text-amber-300 mt-1">
                    ${{ formatNumber(selectedCustomer.balance || 0) }}
                  </p>
                </div>
                
                <!-- Límite de crédito -->
                <div class="bg-gradient-to-br from-blue-50 to-indigo-50 dark:from-blue-900/30 dark:to-indigo-900/20 rounded-xl p-4 border border-blue-100 dark:border-blue-800/50">
                  <p class="text-xs font-medium text-blue-600 dark:text-blue-400 uppercase tracking-wide">Límite Crédito</p>
                  <p class="text-2xl font-bold text-blue-700 dark:text-blue-300 mt-1">
                    ${{ formatNumber(selectedCustomer.credit_limit || 0) }}
                  </p>
                </div>
                
                <!-- Disponible -->
                <div :class="[
                  'rounded-xl p-4 border',
                  getAvailableCreditAmount(selectedCustomer) > 0 
                    ? 'bg-gradient-to-br from-emerald-50 to-green-50 dark:from-emerald-900/30 dark:to-green-900/20 border-emerald-100 dark:border-emerald-800/50'
                    : 'bg-gradient-to-br from-rose-50 to-red-50 dark:from-rose-900/30 dark:to-red-900/20 border-rose-100 dark:border-rose-800/50'
                ]">
                  <p :class="[
                    'text-xs font-medium uppercase tracking-wide',
                    getAvailableCreditAmount(selectedCustomer) > 0 
                      ? 'text-emerald-600 dark:text-emerald-400' 
                      : 'text-rose-600 dark:text-rose-400'
                  ]">Disponible</p>
                  <p :class="[
                    'text-2xl font-bold mt-1',
                    getAvailableCreditAmount(selectedCustomer) > 0 
                      ? 'text-emerald-700 dark:text-emerald-300' 
                      : 'text-rose-700 dark:text-rose-300'
                  ]">
                    ${{ formatNumber(getAvailableCreditAmount(selectedCustomer)) }}
                  </p>
                  <p v-if="getAvailableCreditAmount(selectedCustomer) <= 0" class="text-xs text-rose-500 dark:text-rose-400 mt-1">
                    Cupo agotado
                  </p>
                </div>
              </div>
            </div>

            <!-- Área de contenido con scroll -->
            <div class="flex-1 overflow-y-auto p-5 space-y-6">
              
              <!-- Tabla de Facturas a Crédito -->
              <div class="bg-white dark:bg-zinc-900 rounded-xl shadow-lg dark:shadow-black/30 border border-gray-200 dark:border-zinc-800 overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100 dark:border-zinc-800 flex items-center justify-between">
                  <h3 class="text-sm font-bold text-gray-900 dark:text-white uppercase tracking-wide">
                    Facturas a Crédito
                  </h3>
                  <span class="px-2.5 py-1 bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400 rounded-full text-xs font-bold">
                    {{ selectedCustomer.invoices?.length || 0 }} facturas
                  </span>
                </div>
                
                <div class="overflow-x-auto">
                  <table class="w-full">
                    <thead>
                      <tr class="bg-gray-50 dark:bg-zinc-800/50 text-left text-xs font-medium text-gray-500 dark:text-zinc-400 uppercase tracking-wide">
                        <th class="px-5 py-3">Factura</th>
                        <th class="px-5 py-3">Fecha</th>
                        <th class="px-5 py-3 text-right">Subtotal</th>
                        <th class="px-5 py-3 text-right">Recargo</th>
                        <th class="px-5 py-3 text-right">Total Deuda</th>
                      </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-zinc-800">
                      <template v-if="selectedCustomer.invoices?.length">
                        <tr v-for="invoice in selectedCustomer.invoices" :key="invoice.id"
                            class="hover:bg-gray-50 dark:hover:bg-zinc-800/30 transition-colors duration-150">
                          <td class="px-5 py-3">
                            <span class="font-mono text-sm font-medium text-gray-900 dark:text-white">
                              #{{ invoice.invoice_number || invoice.id }}
                            </span>
                          </td>
                          <td class="px-5 py-3 text-sm text-gray-600 dark:text-zinc-400">
                            {{ formatDate(invoice.created_at) }}
                          </td>
                          <td class="px-5 py-3 text-sm text-gray-900 dark:text-white text-right font-medium">
                            ${{ formatNumber(invoice.subtotal || invoice.total || 0) }}
                          </td>
                          <td class="px-5 py-3 text-sm text-amber-600 dark:text-amber-400 text-right font-medium">
                            +${{ formatNumber(invoice.surcharge_amount || 0) }}
                          </td>
                          <td class="px-5 py-3 text-sm text-rose-600 dark:text-rose-400 text-right font-bold">
                            ${{ formatNumber((invoice.subtotal || invoice.total || 0) + (invoice.surcharge_amount || 0)) }}
                          </td>
                        </tr>
                      </template>
                      <tr v-else>
                        <td colspan="5" class="px-5 py-8 text-center text-gray-500 dark:text-zinc-500">
                          <svg class="w-8 h-8 mx-auto mb-2 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                          </svg>
                          <span class="text-sm">No hay facturas registradas</span>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>

              <!-- Historial de Pagos -->
              <div class="bg-white dark:bg-zinc-900 rounded-xl shadow-lg dark:shadow-black/30 border border-gray-200 dark:border-zinc-800 overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100 dark:border-zinc-800 flex items-center justify-between">
                  <h3 class="text-sm font-bold text-gray-900 dark:text-white uppercase tracking-wide">
                    Historial de Abonos
                  </h3>
                  <span class="px-2.5 py-1 bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400 rounded-full text-xs font-bold">
                    {{ selectedCustomer.payments?.length || 0 }} registros
                  </span>
                </div>
                
                <div class="max-h-72 overflow-y-auto">
                  <div v-if="selectedCustomer.payments?.length" class="divide-y divide-gray-100 dark:divide-zinc-800">
                    <div v-for="payment in selectedCustomer.payments" :key="payment.id"
                         class="px-5 py-4 flex items-center justify-between hover:bg-gray-50 dark:hover:bg-zinc-800/30 transition-colors duration-150">
                      <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-lg bg-emerald-100 dark:bg-emerald-900/30 flex items-center justify-center">
                          <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                          </svg>
                        </div>
                        <div>
                          <p class="text-sm font-medium text-gray-900 dark:text-white">
                            {{ getPaymentMethodLabel(payment.payment_method) }}
                          </p>
                          <p class="text-xs text-gray-500 dark:text-zinc-500">
                            {{ formatDate(payment.created_at) }}
                            <span v-if="payment.reference" class="ml-1 text-gray-400">• {{ payment.reference }}</span>
                          </p>
                        </div>
                      </div>
                      <div class="text-right">
                        <p class="text-sm font-bold text-emerald-600 dark:text-emerald-400">
                          +${{ formatNumber(payment.amount || 0) }}
                        </p>
                        <p v-if="payment.invoice_number" class="text-xs text-gray-400 dark:text-zinc-500">
                          Factura #{{ payment.invoice_number }}
                        </p>
                      </div>
                    </div>
                  </div>
                  <div v-else class="px-5 py-8 text-center text-gray-500 dark:text-zinc-500">
                    <svg class="w-8 h-8 mx-auto mb-2 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    <span class="text-sm">No hay abonos registrados</span>
                  </div>
                </div>
              </div>
              
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- MODAL: Registrar Abono -->
    <Teleport to="body">
      <Transition name="modal">
        <div v-if="showPaymentModal" class="fixed inset-0 z-50 flex items-center justify-center p-4">
          <!-- Overlay -->
          <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" @click="closePaymentModal"></div>
          
          <!-- Modal Content -->
          <div class="relative bg-white dark:bg-zinc-900 rounded-2xl shadow-2xl w-full max-w-md border border-gray-200 dark:border-zinc-700 animate-modal-enter">
            <!-- Header -->
            <div class="flex items-center justify-between p-5 border-b border-gray-200 dark:border-zinc-700">
              <div>
                <h3 class="text-lg font-bold text-gray-900 dark:text-white">Registrar Abono</h3>
                <p class="text-xs text-gray-500 dark:text-zinc-400 mt-0.5">Cliente: {{ paymentCustomer?.name }}</p>
              </div>
              <button @click="closePaymentModal" class="p-2 text-gray-400 hover:text-gray-600 dark:hover:text-zinc-200 hover:bg-gray-100 dark:hover:bg-zinc-800 rounded-lg transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
              </button>
            </div>
            
            <!-- Body -->
            <form @submit.prevent="submitPayment" class="p-5 space-y-4">
              <!-- Saldo actual -->
              <div class="bg-amber-50 dark:bg-amber-900/20 rounded-lg p-3 border border-amber-100 dark:border-amber-800/50">
                <div class="flex items-center justify-between">
                  <span class="text-sm text-amber-700 dark:text-amber-400">Saldo Pendiente:</span>
                  <span class="text-lg font-bold text-amber-700 dark:text-amber-400">
                    ${{ formatNumber(paymentCustomer?.balance || 0) }}
                  </span>
                </div>
              </div>
              
              <!-- Monto a abonar -->
              <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-zinc-300 mb-1.5">
                  Monto a Abonar <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                  <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 dark:text-zinc-500 font-medium">$</span>
                  <input
                    v-model.number="paymentForm.amount"
                    type="number"
                    step="0.01"
                    min="0"
                    :max="paymentCustomer?.balance"
                    required
                    class="w-full pl-8 pr-4 py-2.5 border border-gray-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-gray-900 dark:text-zinc-200 rounded-lg text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                    placeholder="0.00"
                  />
                </div>
              </div>
              
              <!-- Método de pago -->
              <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-zinc-300 mb-1.5">
                  Método de Pago <span class="text-red-500">*</span>
                </label>
                <select
                  v-model="paymentForm.payment_method"
                  required
                  class="w-full px-3 py-2.5 border border-gray-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-gray-900 dark:text-zinc-200 rounded-lg text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                >
                  <option value="cash">Efectivo</option>
                  <option value="card">Tarjeta</option>
                  <option value="transfer">Transferencia</option>
                  <option value="other">Otro</option>
                </select>
              </div>
              
              <!-- Referencia (opcional) -->
              <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-zinc-300 mb-1.5">
                  Referencia <span class="text-gray-400 font-normal">(opcional)</span>
                </label>
                <input
                  v-model="paymentForm.reference"
                  type="text"
                  class="w-full px-3 py-2.5 border border-gray-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-gray-900 dark:text-zinc-200 rounded-lg text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                  placeholder="Número de operación, voucher, etc."
                />
              </div>
              
              <!-- Notas -->
              <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-zinc-300 mb-1.5">
                  Notas <span class="text-gray-400 font-normal">(opcional)</span>
                </label>
                <textarea
                  v-model="paymentForm.notes"
                  rows="2"
                  class="w-full px-3 py-2.5 border border-gray-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-gray-900 dark:text-zinc-200 rounded-lg text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 resize-none"
                  placeholder="Observaciones adicionales..."
                ></textarea>
              </div>
              
              <!-- Botones -->
              <div class="flex gap-3 pt-2">
                <button
                  type="button"
                  @click="closePaymentModal"
                  class="flex-1 px-4 py-2.5 bg-gray-100 dark:bg-zinc-800 hover:bg-gray-200 dark:hover:bg-zinc-700 text-gray-700 dark:text-zinc-300 text-sm font-medium rounded-lg transition-colors"
                >
                  Cancelar
                </button>
                <button
                  type="submit"
                  :disabled="isSubmitting || !paymentForm.amount || paymentForm.amount <= 0"
                  class="flex-1 px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 dark:bg-emerald-500 dark:hover:bg-emerald-400 text-white text-sm font-bold rounded-lg transition-colors disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2"
                >
                  <svg v-if="isSubmitting" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                  </svg>
                  {{ isSubmitting ? 'Procesando...' : 'Registrar Abono' }}
                </button>
              </div>
            </form>
          </div>
        </div>
      </Transition>
    </Teleport>

    <!-- MODAL: Crear Nuevo Crédito -->
    <Teleport to="body">
      <Transition name="modal">
        <div v-if="showCreateCreditModal" class="fixed inset-0 z-50 flex items-center justify-center p-4">
          <!-- Overlay -->
          <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" @click="showCreateCreditModal = false"></div>
          
          <!-- Modal Content -->
          <div class="relative bg-white dark:bg-zinc-900 rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-auto border border-gray-200 dark:border-zinc-700 animate-modal-enter">
            <!-- Header -->
            <div class="flex items-center justify-between p-5 border-b border-gray-200 dark:border-zinc-700 sticky top-0 bg-white dark:bg-zinc-900 z-10">
              <div class="flex items-center gap-3">
                <div class="w-11 h-11 bg-indigo-100 dark:bg-indigo-900/30 rounded-xl flex items-center justify-center">
                  <svg class="w-5 h-5 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                  </svg>
                </div>
                <div>
                  <h3 class="text-lg font-bold text-gray-900 dark:text-white">{{ customerExists ? 'Actualizar Crédito' : 'Nuevo Crédito' }}</h3>
                  <p class="text-xs text-gray-500 dark:text-zinc-400 mt-0.5">{{ customerExists ? 'Cliente encontrado en el sistema' : 'Crear cliente con crédito habilitado' }}</p>
                </div>
              </div>
              <button @click="showCreateCreditModal = false" class="p-2 text-gray-400 hover:text-gray-600 dark:hover:text-zinc-200 hover:bg-gray-100 dark:hover:bg-zinc-800 rounded-lg transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
              </button>
            </div>

            <!-- Body -->
            <div class="p-6 space-y-5">
              <!-- Foto del Cliente -->
              <div class="flex items-center gap-5 pb-5 border-b border-gray-100 dark:border-zinc-800">
                <div class="relative">
                  <div class="w-24 h-24 rounded-xl overflow-hidden bg-gray-100 dark:bg-zinc-800 flex items-center justify-center border-2 border-dashed border-gray-300 dark:border-zinc-600">
                    <img v-if="photoPreview || customerForm.credit_photo" 
                         :src="photoPreview || customerForm.credit_photo" 
                         class="w-full h-full object-cover"
                         alt="Foto cliente" />
                    <svg v-else class="w-10 h-10 text-gray-300 dark:text-zinc-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                  </div>
                  <div v-if="uploadingPhoto" class="absolute inset-0 bg-black/50 rounded-xl flex items-center justify-center">
                    <div class="w-6 h-6 border-2 border-white border-t-transparent rounded-full animate-spin"></div>
                  </div>
                </div>
                <div class="flex-1">
                  <p class="text-sm font-medium text-gray-700 dark:text-zinc-300 mb-2">Foto del Cliente</p>
                  <p class="text-xs text-gray-500 dark:text-zinc-500 mb-3">Para identificación en CrediTienda</p>
                  <div class="flex gap-2">
                    <label class="px-3 py-2 bg-indigo-50 dark:bg-indigo-900/20 hover:bg-indigo-100 dark:hover:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 text-xs font-medium rounded-lg cursor-pointer transition-colors">
                      <input type="file" accept="image/*" @change="handlePhotoUpload" class="hidden" />
                      <span class="flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        Subir Foto
                      </span>
                    </label>
                    <button v-if="photoPreview || customerForm.credit_photo" 
                            @click="removePhoto"
                            type="button"
                            class="px-3 py-2 bg-rose-50 dark:bg-rose-900/20 hover:bg-rose-100 dark:hover:bg-rose-900/30 text-rose-600 dark:text-rose-400 text-xs font-medium rounded-lg transition-colors">
                      Eliminar
                    </button>
                  </div>
                </div>
              </div>

              <!-- Documento -->
              <div class="grid grid-cols-3 gap-3">
                <div>
                  <label class="block text-sm font-medium text-gray-700 dark:text-zinc-300 mb-1.5">Tipo</label>
                  <select 
                    v-model="customerForm.document_type"
                    class="w-full px-3 py-2.5 border border-gray-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-gray-900 dark:text-zinc-200 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                  >
                    <option value="CC">CC</option>
                    <option value="NIT">NIT</option>
                    <option value="CE">CE</option>
                    <option value="Pasaporte">Pasaporte</option>
                  </select>
                </div>
                <div class="col-span-2">
                  <label class="block text-sm font-medium text-gray-700 dark:text-zinc-300 mb-1.5">Número de Documento <span class="text-red-500">*</span></label>
                  <div class="relative">
                    <input 
                      v-model="customerForm.document_number"
                      @blur="checkDocumentExists"
                      type="text"
                      placeholder="Ej: 1234567890"
                      class="w-full px-3 py-2.5 pr-10 border border-gray-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-gray-900 dark:text-zinc-200 placeholder-gray-400 dark:placeholder-zinc-500 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                    />
                    <div v-if="checkingDocument" class="absolute right-3 top-1/2 -translate-y-1/2">
                      <div class="w-4 h-4 border-2 border-indigo-600 border-t-transparent rounded-full animate-spin"></div>
                    </div>
                    <svg v-else-if="customerExists" class="absolute right-3 top-1/2 -translate-y-1/2 w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                  </div>
                  <p v-if="customerExists" class="text-xs text-emerald-600 dark:text-emerald-400 mt-1">✓ Cliente existente encontrado</p>
                </div>
              </div>

              <!-- Información Personal -->
              <div class="space-y-3">
                <h4 class="text-sm font-bold text-gray-900 dark:text-white">Información Personal</h4>
                
                <div>
                  <label class="block text-sm font-medium text-gray-700 dark:text-zinc-300 mb-1.5">Nombre Completo <span class="text-red-500">*</span></label>
                  <input 
                    v-model="customerForm.name"
                    type="text"
                    placeholder="Ej: Juan Pérez"
                    class="w-full px-3 py-2.5 border border-gray-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-gray-900 dark:text-zinc-200 placeholder-gray-400 dark:placeholder-zinc-500 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                  />
                </div>

                <div class="grid grid-cols-2 gap-3">
                  <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-zinc-300 mb-1.5">Teléfono</label>
                    <input 
                      v-model="customerForm.phone"
                      type="tel"
                      placeholder="3001234567"
                      class="w-full px-3 py-2.5 border border-gray-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-gray-900 dark:text-zinc-200 placeholder-gray-400 dark:placeholder-zinc-500 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                    />
                  </div>
                  <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-zinc-300 mb-1.5">Email</label>
                    <input 
                      v-model="customerForm.email"
                      type="email"
                      placeholder="correo@ejemplo.com"
                      class="w-full px-3 py-2.5 border border-gray-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-gray-900 dark:text-zinc-200 placeholder-gray-400 dark:placeholder-zinc-500 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                    />
                  </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                  <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-zinc-300 mb-1.5">Dirección</label>
                    <input 
                      v-model="customerForm.address"
                      type="text"
                      placeholder="Calle 123 # 45-67"
                      class="w-full px-3 py-2.5 border border-gray-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-gray-900 dark:text-zinc-200 placeholder-gray-400 dark:placeholder-zinc-500 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                    />
                  </div>
                  <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-zinc-300 mb-1.5">Ciudad</label>
                    <input 
                      v-model="customerForm.city"
                      type="text"
                      placeholder="Bogotá"
                      class="w-full px-3 py-2.5 border border-gray-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-gray-900 dark:text-zinc-200 placeholder-gray-400 dark:placeholder-zinc-500 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                    />
                  </div>
                </div>
              </div>

              <!-- Configuración de Crédito -->
              <div class="space-y-3 pt-4 border-t border-gray-100 dark:border-zinc-800">
                <h4 class="text-sm font-bold text-gray-900 dark:text-white">Configuración de Crédito</h4>
                
                <div>
                  <label class="block text-sm font-medium text-gray-700 dark:text-zinc-300 mb-1.5">Cupo de Crédito <span class="text-red-500">*</span></label>
                  <div class="relative">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500 dark:text-zinc-400 font-bold">$</span>
                    <input 
                      v-model.number="customerForm.credit_limit"
                      type="number"
                      min="0"
                      step="10000"
                      placeholder="0"
                      class="w-full pl-8 pr-3 py-2.5 border border-gray-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-gray-900 dark:text-zinc-200 placeholder-gray-400 dark:placeholder-zinc-500 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                    />
                  </div>
                  <p class="text-xs text-gray-500 dark:text-zinc-500 mt-1">Monto máximo que el cliente puede deber</p>
                </div>
              </div>
            </div>

            <!-- Footer -->
            <div class="flex items-center justify-end gap-3 p-5 border-t border-gray-200 dark:border-zinc-700 bg-gray-50 dark:bg-zinc-900/50">
              <button 
                type="button"
                @click="showCreateCreditModal = false"
                class="px-5 py-2.5 bg-white dark:bg-zinc-800 hover:bg-gray-100 dark:hover:bg-zinc-700 text-gray-700 dark:text-zinc-300 text-sm font-medium rounded-xl border border-gray-300 dark:border-zinc-700 transition-colors"
              >
                Cancelar
              </button>
              <button 
                @click="saveCustomerCredit"
                :disabled="!customerForm.document_number || !customerForm.name || !customerForm.credit_limit || processing"
                class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 dark:bg-indigo-500 dark:hover:bg-indigo-400 disabled:bg-gray-300 dark:disabled:bg-zinc-700 disabled:cursor-not-allowed text-white text-sm font-bold rounded-xl shadow-lg shadow-indigo-500/30 transition-all duration-300 flex items-center gap-2"
              >
                <svg v-if="processing" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                {{ processing ? 'Guardando...' : (customerExists ? 'Actualizar Cliente' : 'Crear Cliente') }}
              </button>
            </div>
          </div>
        </div>
      </Transition>
    </Teleport>

    <!-- Botón FAB Flotante - Nuevo Crédito -->
    <button 
      @click="openCreateCreditModal"
      class="fixed bottom-6 right-6 w-14 h-14 bg-black dark:bg-zinc-800 hover:bg-gray-900 dark:hover:bg-zinc-700 text-white rounded-full shadow-2xl dark:shadow-black/70 hover:shadow-black/40 transition-all duration-300 transform hover:scale-110 active:scale-95 z-50 flex items-center justify-center group">
      <svg class="w-7 h-7 transition-transform duration-300 group-hover:rotate-90" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path>
      </svg>
      <span class="sr-only">Nuevo Crédito</span>
    </button>

    <!-- Modal de Edición de Cliente -->
    <Teleport to="body">
      <Transition
        enter-active-class="transition-all duration-300 ease-out"
        leave-active-class="transition-all duration-200 ease-in"
        enter-from-class="opacity-0"
        enter-to-class="opacity-100"
        leave-from-class="opacity-100"
        leave-to-class="opacity-0"
      >
        <div v-if="showEditCustomerModal" class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm">
          <Transition
            enter-active-class="transition-all duration-300 ease-out"
            leave-active-class="transition-all duration-200 ease-in"
            enter-from-class="opacity-0 scale-95 translate-y-4"
            enter-to-class="opacity-100 scale-100 translate-y-0"
            leave-from-class="opacity-100 scale-100 translate-y-0"
            leave-to-class="opacity-0 scale-95 translate-y-4"
          >
            <div v-if="showEditCustomerModal" @click.stop class="bg-white dark:bg-zinc-900 rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto border border-gray-200 dark:border-zinc-800">
              <!-- Header -->
              <div class="sticky top-0 bg-white dark:bg-zinc-900 border-b border-gray-200 dark:border-zinc-800 px-6 py-4 flex items-center justify-between z-10">
                <div>
                  <h3 class="text-xl font-bold text-gray-900 dark:text-white">Editar Cliente</h3>
                  <p class="text-sm text-gray-500 dark:text-zinc-400 mt-0.5">Actualizar información y foto del cliente</p>
                </div>
                <button @click="showEditCustomerModal = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-zinc-300 transition-colors p-2 hover:bg-gray-100 dark:hover:bg-zinc-800 rounded-lg">
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                  </svg>
                </button>
              </div>

              <!-- Body -->
              <div class="p-6 space-y-5">
                
                <!-- Foto del Cliente -->
                <div class="bg-gray-50 dark:bg-zinc-800/50 rounded-xl p-4 border border-gray-200 dark:border-zinc-700">
                  <label class="block text-sm font-semibold text-gray-700 dark:text-zinc-300 mb-3">Foto del Cliente</label>
                  
                  <div class="flex items-center gap-4">
                    <!-- Preview de la foto -->
                    <div class="w-24 h-24 rounded-xl overflow-hidden bg-gray-200 dark:bg-zinc-700 flex items-center justify-center flex-shrink-0">
                      <img v-if="editPhotoPreview || editForm.credit_photo" 
                           :src="editPhotoPreview || editForm.credit_photo" 
                           class="w-full h-full object-cover" />
                      <svg v-else class="w-10 h-10 text-gray-400 dark:text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                      </svg>
                    </div>
                    
                    <!-- Botones -->
                    <div class="flex-1 space-y-2">
                      <label class="cursor-pointer inline-block">
                        <input type="file" accept="image/*" @change="handleEditPhotoUpload" class="hidden" />
                        <span class="px-4 py-2 bg-blue-600 hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-400 text-white text-sm font-medium rounded-lg transition-colors inline-flex items-center gap-2">
                          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                          </svg>
                          {{ uploadingEditPhoto ? 'Subiendo...' : (editPhotoPreview || editForm.credit_photo ? 'Cambiar Foto' : 'Subir Foto') }}
                        </span>
                      </label>
                      <button v-if="editPhotoPreview || editForm.credit_photo" @click="removeEditPhoto" type="button" class="block px-4 py-2 bg-red-100 hover:bg-red-200 dark:bg-red-900/30 dark:hover:bg-red-900/50 text-red-700 dark:text-red-400 text-sm font-medium rounded-lg transition-colors">
                        Eliminar Foto
                      </button>
                      <p class="text-xs text-gray-500 dark:text-zinc-400">JPG, PNG o GIF. Máximo 2MB.</p>
                    </div>
                  </div>
                </div>

                <!-- Información Personal -->
                <div class="grid grid-cols-2 gap-4">
                  <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-zinc-300 mb-2">Tipo de Documento</label>
                    <select v-model="editForm.document_type" class="w-full px-3 py-2.5 border border-gray-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-gray-900 dark:text-zinc-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                      <option value="CC">Cédula</option>
                      <option value="NIT">NIT</option>
                      <option value="CE">Cédula Extranjería</option>
                      <option value="PAS">Pasaporte</option>
                    </select>
                  </div>
                  
                  <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-zinc-300 mb-2">Número de Documento</label>
                    <input v-model="editForm.document_number" type="text" class="w-full px-3 py-2.5 border border-gray-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-gray-900 dark:text-zinc-200 placeholder-gray-400 dark:placeholder-zinc-500 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="1234567890" />
                  </div>
                </div>

                <div>
                  <label class="block text-sm font-medium text-gray-700 dark:text-zinc-300 mb-2">Nombre Completo *</label>
                  <input v-model="editForm.name" type="text" required class="w-full px-3 py-2.5 border border-gray-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-gray-900 dark:text-zinc-200 placeholder-gray-400 dark:placeholder-zinc-500 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Juan Pérez" />
                </div>

                <div class="grid grid-cols-2 gap-4">
                  <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-zinc-300 mb-2">Teléfono</label>
                    <input v-model="editForm.phone" type="tel" class="w-full px-3 py-2.5 border border-gray-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-gray-900 dark:text-zinc-200 placeholder-gray-400 dark:placeholder-zinc-500 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="3001234567" />
                  </div>
                  
                  <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-zinc-300 mb-2">Email</label>
                    <input v-model="editForm.email" type="email" class="w-full px-3 py-2.5 border border-gray-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-gray-900 dark:text-zinc-200 placeholder-gray-400 dark:placeholder-zinc-500 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="correo@ejemplo.com" />
                  </div>
                </div>

                <div>
                  <label class="block text-sm font-medium text-gray-700 dark:text-zinc-300 mb-2">Dirección</label>
                  <input v-model="editForm.address" type="text" class="w-full px-3 py-2.5 border border-gray-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-gray-900 dark:text-zinc-200 placeholder-gray-400 dark:placeholder-zinc-500 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Calle 123 #45-67" />
                </div>

                <div class="grid grid-cols-2 gap-4">
                  <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-zinc-300 mb-2">Ciudad</label>
                    <input v-model="editForm.city" type="text" class="w-full px-3 py-2.5 border border-gray-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-gray-900 dark:text-zinc-200 placeholder-gray-400 dark:placeholder-zinc-500 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Bogotá" />
                  </div>
                  
                  <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-zinc-300 mb-2">Límite de Crédito</label>
                    <input v-model.number="editForm.credit_limit" type="number" min="0" step="1000" class="w-full px-3 py-2.5 border border-gray-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-gray-900 dark:text-zinc-200 placeholder-gray-400 dark:placeholder-zinc-500 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="1000000" />
                  </div>
                </div>

              </div>

              <!-- Footer -->
              <div class="sticky bottom-0 bg-gray-50 dark:bg-zinc-800/50 border-t border-gray-200 dark:border-zinc-800 px-6 py-4 flex items-center justify-end gap-3">
                <button @click="showEditCustomerModal = false" type="button" class="px-5 py-2.5 bg-white dark:bg-zinc-900 hover:bg-gray-50 dark:hover:bg-zinc-800 text-gray-700 dark:text-zinc-300 text-sm font-bold rounded-xl border border-gray-300 dark:border-zinc-700 transition-all duration-200">
                  Cancelar
                </button>
                <button @click="submitEditCustomer" :disabled="processingEdit || !editForm.name" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-400 text-white text-sm font-bold rounded-xl shadow-lg transition-all duration-300 disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2">
                  <svg v-if="processingEdit" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                  </svg>
                  {{ processingEdit ? 'Actualizando...' : 'Actualizar Cliente' }}
                </button>
              </div>
            </div>
          </Transition>
        </div>
      </Transition>
    </Teleport>

    <!-- Modal de Preview de Foto -->
    <Teleport to="body">
      <Transition
        enter-active-class="transition-all duration-300 ease-out"
        leave-active-class="transition-all duration-200 ease-in"
        enter-from-class="opacity-0"
        enter-to-class="opacity-100"
        leave-from-class="opacity-100"
        leave-to-class="opacity-0"
      >
        <div v-if="showPhotoPreviewModal" @click="closePhotoPreview" class="fixed inset-0 z-[110] flex items-center justify-center p-4 bg-black/80 backdrop-blur-md">
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
              <div class="bg-white dark:bg-zinc-900 rounded-t-2xl px-6 py-4 flex items-center justify-between border-b border-gray-200 dark:border-zinc-800">
                <div>
                  <h3 class="text-lg font-bold text-gray-900 dark:text-white">{{ previewPhotoName }}</h3>
                  <p class="text-sm text-gray-500 dark:text-zinc-400">Foto del cliente</p>
                </div>
                <button @click="closePhotoPreview" class="text-gray-400 hover:text-gray-600 dark:hover:text-zinc-300 transition-colors p-2 hover:bg-gray-100 dark:hover:bg-zinc-800 rounded-lg">
                  <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                  </svg>
                </button>
              </div>

              <!-- Imagen grande -->
              <div class="bg-gray-100 dark:bg-zinc-900 rounded-b-2xl p-8 flex items-center justify-center">
                <img 
                  :src="previewPhotoUrl" 
                  :alt="previewPhotoName"
                  class="max-w-full max-h-[70vh] object-contain rounded-xl shadow-2xl"
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
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { customersService } from '../services/customersService.js'
import { useToast } from '../composables/useToast.js'
import axiosInstance from '../services/apiClient.js'

const { showSuccess, showError } = useToast()

// 🎯 Escape Key Handler - Limpiar filtros
const handleEscape = (e) => {
  if (e.key === 'Escape') {
    // Cerrar modales primero (por prioridad)
    if (showPhotoPreviewModal.value) {
      closePhotoPreview()
      return
    }
    if (showEditCustomerModal.value) {
      showEditCustomerModal.value = false
      return
    }
    if (showPaymentModal.value) {
      closePaymentModal()
      return
    }
    if (showCreateCreditModal.value) {
      showCreateCreditModal.value = false
      return
    }
    // Limpiar filtros y deseleccionar
    searchTerm.value = ''
    statusFilter.value = ''
    selectedCustomer.value = null
  }
}

// State
const customers = ref([])
const loading = ref(false)
const searchTerm = ref('')
const statusFilter = ref('')
const showPaymentModal = ref(false)
const showDetailModal = ref(false)
const selectedCustomer = ref(null)
const paymentAmount = ref(0)
const paymentMethod = ref('cash')
const paymentNotes = ref('')
const processing = ref(false)
const loadingDetail = ref(false)
const sendingReminder = ref(false)
const creditInvoices = ref([])
const creditPayments = ref([])

// Modal state
const paymentCustomer = ref(null)
const isSubmitting = ref(false)
const paymentForm = ref({
  amount: 0,
  payment_method: 'cash',
  reference: '',
  notes: ''
})

// 🎯 CreditiTenda: Modal de creación de cliente con crédito
const showCreateCreditModal = ref(false)
const checkingDocument = ref(false)
const customerExists = ref(false)
const uploadingPhoto = ref(false)
const photoPreview = ref(null)
const customerForm = ref({
  document_type: 'CC',
  document_number: '',
  name: '',
  email: '',
  phone: '',
  address: '',
  city: '',
  credit_limit: 0,
  credit_photo: '',
  credit_active: true,
  active: true
})

// 🎯 Modal de edición de cliente
const showEditCustomerModal = ref(false)
const editForm = ref({})
const editPhotoPreview = ref(null)
const uploadingEditPhoto = ref(false)
const processingEdit = ref(false)

// 🎯 Modal de preview de foto
const showPhotoPreviewModal = ref(false)
const previewPhotoUrl = ref('')
const previewPhotoName = ref('')

// Computed
const filteredCustomers = computed(() => {
  let filtered = customers.value.filter(c => c.credit_active)
  
  if (searchTerm.value) {
    const term = searchTerm.value.toLowerCase()
    filtered = filtered.filter(c => 
      c.name.toLowerCase().includes(term) ||
      c.document_number?.includes(term)
    )
  }
  
  // Filtro por estado
  if (statusFilter.value === 'debt') {
    filtered = filtered.filter(c => c.current_debt > 0)
  } else if (statusFilter.value === 'overdue') {
    filtered = filtered.filter(c => {
      if (!c.debt_since) return false
      const days = Math.floor((new Date() - new Date(c.debt_since)) / (1000 * 60 * 60 * 24))
      return days >= 30
    })
  } else if (statusFilter.value === 'critical') {
    filtered = filtered.filter(c => c.current_debt > (c.credit_limit || 0))
  }
  
  return filtered
})

const totalDebt = computed(() => {
  return filteredCustomers.value.reduce((sum, c) => sum + (c.current_debt || 0), 0)
})

const customersWithDebt = computed(() => {
  return filteredCustomers.value.filter(c => c.current_debt > 0).length
})

const todayPayments = computed(() => {
  return 0
})

const averageDaysOverdue = computed(() => {
  return 0
})

// Methods
const loadCustomers = async () => {
  loading.value = true
  try {
    const response = await customersService.getAll()
    customers.value = response.data || []
  } catch (error) {
    console.error('Error loading customers:', error)
    showError('Error al cargar clientes')
  } finally {
    loading.value = false
  }
}

const formatNumber = (value) => {
  return new Intl.NumberFormat('es-CO').format(parseFloat(value || 0))
}

const formatCurrency = (value) => {
  return new Intl.NumberFormat('es-CO').format(parseFloat(value || 0))
}

const formatDate = (date) => {
  if (!date) return ''
  return new Date(date).toLocaleDateString('es-CO', {
    year: 'numeric',
    month: 'short',
    day: 'numeric'
  })
}

const getStatusColor = (customer) => {
  const debt = customer.current_debt || 0
  const limit = customer.credit_limit || 0
  
  if (debt === 0) return 'bg-emerald-50 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-400 border-emerald-100 dark:border-emerald-800'
  if (debt > limit) return 'bg-gray-900 dark:bg-gray-950 text-white dark:text-gray-100 border-gray-800 dark:border-gray-900'
  
  if (!customer.debt_since) {
    return 'bg-blue-50 dark:bg-blue-950 text-blue-700 dark:text-blue-400 border-blue-100 dark:border-blue-800'
  }
  
  const debtDate = new Date(customer.debt_since)
  const now = new Date()
  const daysDiff = Math.floor((now - debtDate) / (1000 * 60 * 60 * 24))
  
  if (daysDiff >= 90) return 'bg-rose-50 dark:bg-rose-950 text-rose-700 dark:text-rose-400 border-rose-100 dark:border-rose-800'
  if (daysDiff >= 60) return 'bg-orange-50 dark:bg-orange-950 text-orange-700 dark:text-orange-400 border-orange-100 dark:border-orange-800'
  if (daysDiff >= 30) return 'bg-amber-50 dark:bg-amber-950 text-amber-700 dark:text-amber-400 border-amber-100 dark:border-amber-800'
  
  return 'bg-blue-50 dark:bg-blue-950 text-blue-700 dark:text-blue-400 border-blue-100 dark:border-blue-800'
}

const getStatusText = (customer) => {
  const debt = customer.current_debt || 0
  const limit = customer.credit_limit || 0
  
  if (debt === 0) return 'Al Día'
  if (debt > limit) return 'Crítico'
  
  if (!customer.debt_since) return 'Activo'
  
  const debtDate = new Date(customer.debt_since)
  const now = new Date()
  const daysDiff = Math.floor((now - debtDate) / (1000 * 60 * 60 * 24))
  
  if (daysDiff >= 90) return 'Mora'
  if (daysDiff >= 60) return 'Vencido'
  if (daysDiff >= 30) return 'Por Vencer'
  
  return 'Al Día'
}

const getAvailableCredit = (customer) => {
  return Math.max(0, (customer.credit_limit || 0) - (customer.current_debt || 0))
}

// Función para calcular el crédito disponible (puede ser negativo para mostrar cuánto sobrepasó)
const getAvailableCreditAmount = (customer) => {
  // El disponible es: Límite - Deuda (que incluye recargos)
  // Si es negativo, el cliente está en sobre-deuda (compró más de su límite + recargos)
  const limit = customer.credit_limit || 0
  const debt = customer.balance || customer.current_debt || 0
  return Math.max(0, limit - debt)
}

// Nueva función para seleccionar cliente en el panel izquierdo
const selectCustomer = async (customer) => {
  selectedCustomer.value = customer
  await loadCustomerCreditDetail(customer.id)
}

// Función para obtener color del avatar basado en el ID o customer
const getAvatarColor = (customerOrId) => {
  const colors = [
    'bg-blue-500 text-white',
    'bg-indigo-500 text-white', 
    'bg-purple-500 text-white',
    'bg-pink-500 text-white',
    'bg-rose-500 text-white',
    'bg-emerald-500 text-white',
    'bg-teal-500 text-white',
    'bg-cyan-500 text-white',
    'bg-amber-500 text-white',
    'bg-orange-500 text-white'
  ]
  // Acepta tanto un objeto customer como un ID directo
  const id = typeof customerOrId === 'object' ? customerOrId?.id : customerOrId
  const index = (id || 0) % colors.length
  return colors[index]
}

// Función para obtener etiqueta de método de pago
const getPaymentMethodLabel = (method) => {
  const labels = {
    'cash': 'Efectivo',
    'card': 'Tarjeta',
    'transfer': 'Transferencia',
    'credit': 'Crédito',
    'other': 'Otro'
  }
  return labels[method] || method
}

const openPaymentModal = (customer) => {
  paymentCustomer.value = customer
  paymentForm.value = {
    amount: 0,
    payment_method: 'cash',
    reference: '',
    notes: ''
  }
  isSubmitting.value = false
  showPaymentModal.value = true
}

const closePaymentModal = () => {
  showPaymentModal.value = false
  paymentCustomer.value = null
}

const submitPayment = async () => {
  // Validar que paymentCustomer existe
  if (!paymentCustomer.value) {
    showError('Error: Cliente no seleccionado')
    return
  }

  if (!paymentForm.value.amount || paymentForm.value.amount <= 0) {
    showError('Ingrese un monto válido')
    return
  }

  if (paymentForm.value.amount > paymentCustomer.value.balance) {
    showError('El monto no puede ser mayor a la deuda')
    return
  }

  isSubmitting.value = true
  
  // Guardar ID del cliente antes de hacer el request
  const customerId = paymentCustomer.value.id
  
  try {
    const response = await axiosInstance.post('/credit-payments', {
      customer_id: customerId,
      amount: paymentForm.value.amount,
      method: paymentForm.value.payment_method,
      reference: paymentForm.value.reference,
      notes: paymentForm.value.notes
    })

    if (response.data.success) {
      showSuccess('Abono registrado exitosamente')
      closePaymentModal()
      await loadCustomers()
      // Recargar detalle del cliente seleccionado
      if (selectedCustomer.value?.id === customerId) {
        await loadCustomerCreditDetail(selectedCustomer.value.id)
      }
    }
  } catch (error) {
    console.error('Error registering payment:', error)
    showError('Error al registrar el abono')
  } finally {
    isSubmitting.value = false
  }
}

const loadCustomerCreditDetail = async (customerId) => {
  loadingDetail.value = true
  try {
    // Load credit invoices
    const invoicesResponse = await axiosInstance.get('/invoices', {
      params: {
        customer_id: customerId,
        payment_method: 'credit'
      }
    })
    
    const allInvoices = invoicesResponse.data.data || invoicesResponse.data || []
    creditInvoices.value = allInvoices.filter(invoice => invoice.payment_method === 'credit')
    
    // Actualizar invoices y payments en el selectedCustomer
    if (selectedCustomer.value) {
      selectedCustomer.value.invoices = creditInvoices.value
    }
    
    // Load credit payments
    const paymentsResponse = await axiosInstance.get('/credit-payments', {
      params: {
        customer_id: customerId
      }
    })
    creditPayments.value = paymentsResponse.data.data || paymentsResponse.data || []
    
    if (selectedCustomer.value) {
      selectedCustomer.value.payments = creditPayments.value
      // Calcular balance basado en current_debt
      selectedCustomer.value.balance = selectedCustomer.value.current_debt || 0
    }
  } catch (error) {
    console.error('Error loading customer credit detail:', error)
    showError('Error al cargar el detalle del cliente')
  } finally {
    loadingDetail.value = false
  }
}

const sendReminder = async (customer) => {
  if (!customer.current_debt || customer.current_debt <= 0) {
    showError('El cliente no tiene deuda pendiente')
    return
  }

  sendingReminder.value = true
  try {
    const response = await axiosInstance.post('/credit-reminders', {
      customer_id: customer.id
    })

    if (response.data.success) {
      showSuccess('Recordatorio enviado exitosamente')
    }
  } catch (error) {
    console.error('Error sending reminder:', error)
    showError(error.response?.data?.message || 'Error al enviar el recordatorio')
  } finally {
    sendingReminder.value = false
  }
}

// 🎯 CreditiTenda: Funciones para creación de cliente con validación de cédula
const openCreateCreditModal = () => {
  customerForm.value = {
    document_type: 'CC',
    document_number: '',
    name: '',
    email: '',
    phone: '',
    address: '',
    city: '',
    credit_limit: 0,
    credit_photo: '',
    credit_active: true,
    active: true
  }
  customerExists.value = false
  photoPreview.value = null
  showCreateCreditModal.value = true
}

// Función para subir foto
const handlePhotoUpload = async (event) => {
  const file = event.target.files[0]
  if (!file) return
  
  // Validar tipo de archivo
  if (!file.type.startsWith('image/')) {
    showError('Por favor selecciona una imagen válida')
    return
  }
  
  // Validar tamaño (max 2MB)
  if (file.size > 2 * 1024 * 1024) {
    showError('La imagen no debe superar 2MB')
    return
  }
  
  uploadingPhoto.value = true
  
  // Preview local y guardar base64
  const reader = new FileReader()
  reader.onload = (e) => {
    photoPreview.value = e.target.result
    customerForm.value.credit_photo = e.target.result
    uploadingPhoto.value = false
    showSuccess('Foto cargada correctamente')
  }
  reader.onerror = () => {
    showError('Error al cargar la imagen')
    uploadingPhoto.value = false
  }
  reader.readAsDataURL(file)
}

// Función para eliminar foto
const removePhoto = () => {
  customerForm.value.credit_photo = ''
  photoPreview.value = null
}

// Funciones para editar cliente
const openEditCustomerModal = (customer) => {
  editForm.value = {
    id: customer.id,
    document_type: customer.document_type,
    document_number: customer.document_number,
    name: customer.name,
    email: customer.email || '',
    phone: customer.phone || '',
    address: customer.address || '',
    city: customer.city || '',
    credit_limit: customer.credit_limit || 0,
    credit_photo: customer.credit_photo || ''
  }
  editPhotoPreview.value = null
  showEditCustomerModal.value = true
}

const handleEditPhotoUpload = async (event) => {
  const file = event.target.files[0]
  if (!file) return
  
  if (!file.type.startsWith('image/')) {
    showError('Por favor selecciona una imagen válida')
    return
  }
  
  if (file.size > 2 * 1024 * 1024) {
    showError('La imagen no debe superar 2MB')
    return
  }
  
  uploadingEditPhoto.value = true
  
  const reader = new FileReader()
  reader.onload = (e) => {
    editPhotoPreview.value = e.target.result
    editForm.value.credit_photo = e.target.result
    uploadingEditPhoto.value = false
    showSuccess('Foto cargada correctamente')
  }
  reader.onerror = () => {
    showError('Error al cargar la imagen')
    uploadingEditPhoto.value = false
  }
  reader.readAsDataURL(file)
}

const removeEditPhoto = () => {
  editForm.value.credit_photo = ''
  editPhotoPreview.value = null
}

const submitEditCustomer = async () => {
  if (!editForm.value.name) {
    showError('El nombre es obligatorio')
    return
  }
  
  processingEdit.value = true
  try {
    const response = await axiosInstance.put(`/customers/${editForm.value.id}`, editForm.value)
    
    if (response.data.success || response.data.customer) {
      showSuccess('Cliente actualizado exitosamente')
      showEditCustomerModal.value = false
      await loadCustomers()
      
      // Actualizar el cliente seleccionado si es el mismo
      if (selectedCustomer.value?.id === editForm.value.id) {
        const updatedCustomer = response.data.customer || response.data.data
        selectedCustomer.value = { ...selectedCustomer.value, ...updatedCustomer }
      }
    }
  } catch (error) {
    console.error('Error updating customer:', error)
    console.error('Response data:', error.response?.data)
    console.error('Validation errors:', error.response?.data?.errors)
    const errorMsg = error.response?.data?.message || 'Error al actualizar el cliente'
    const validationErrors = error.response?.data?.errors
    if (validationErrors) {
      const firstError = Object.values(validationErrors)[0]
      showError(firstError[0] || errorMsg)
    } else {
      showError(errorMsg)
    }
  } finally {
    processingEdit.value = false
  }
}

// Función para abrir preview de foto
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

const checkDocumentExists = async () => {
  if (!customerForm.value.document_number || customerForm.value.document_number.length < 5) {
    return
  }

  checkingDocument.value = true
  try {
    const response = await axiosInstance.post('/customers/check-document', {
      document_type: customerForm.value.document_type,
      document_number: customerForm.value.document_number
    })

    if (response.data.exists) {
      customerExists.value = true
      const existingCustomer = response.data.data
      customerForm.value = {
        ...customerForm.value,
        name: existingCustomer.name,
        email: existingCustomer.email || '',
        phone: existingCustomer.phone || '',
        address: existingCustomer.address || '',
        city: existingCustomer.city || '',
        credit_limit: existingCustomer.credit_limit || 0,
        credit_photo: existingCustomer.credit_photo || '',
        credit_active: true,
        active: existingCustomer.active ?? true
      }
      // Mostrar foto existente si la tiene
      if (existingCustomer.credit_photo) {
        photoPreview.value = existingCustomer.credit_photo
      }
    } else {
      customerExists.value = false
    }
  } catch (error) {
    console.error('Error checking document:', error)
    showError('Error al validar el documento')
  } finally {
    checkingDocument.value = false
  }
}

const saveCustomerCredit = async () => {
  if (!customerForm.value.document_number) {
    showError('El número de documento es obligatorio')
    return
  }
  if (!customerForm.value.name) {
    showError('El nombre es obligatorio')
    return
  }
  if (!customerForm.value.credit_limit || customerForm.value.credit_limit <= 0) {
    showError('El cupo de crédito debe ser mayor a 0')
    return
  }

  processing.value = true
  try {
    let response
    if (customerExists.value) {
      const existingId = customers.value.find(c => 
        c.document_type === customerForm.value.document_type && 
        c.document_number === customerForm.value.document_number
      )?.id
      
      response = await axiosInstance.put(`/customers/${existingId}`, customerForm.value)
    } else {
      response = await axiosInstance.post('/customers', customerForm.value)
    }

    if (response.data.success) {
      showSuccess(customerExists.value ? 'Crédito actualizado exitosamente' : 'Crédito creado exitosamente')
      showCreateCreditModal.value = false
      await loadCustomers()
    }
  } catch (error) {
    console.error('Error saving customer:', error)
    
    if (error.response?.data?.errors) {
      const errors = error.response.data.errors
      if (errors.document_number) {
        showError('Este número de documento ya está registrado')
      } else if (errors.email) {
        showError('Este correo electrónico ya está registrado')
      } else {
        showError(error.response?.data?.message || 'Error al guardar el crédito')
      }
    } else {
      showError(error.response?.data?.message || 'Error al guardar el crédito')
    }
  } finally {
    processing.value = false
  }
}

// Initialization
onMounted(() => {
  loadCustomers()
  window.addEventListener('keydown', handleEscape)
})

onUnmounted(() => {
  window.removeEventListener('keydown', handleEscape)
})
</script>

<style scoped>
.animate-fade-in {
  animation: fadeIn 0.3s ease-out;
}

@keyframes fadeIn {
  from { opacity: 0; transform: translateY(10px); }
  to { opacity: 1; transform: translateY(0); }
}

.animate-modal-enter {
  animation: modalEnter 0.2s ease-out;
}

@keyframes modalEnter {
  from { opacity: 0; transform: scale(0.95); }
  to { opacity: 1; transform: scale(1); }
}

/* Modal transitions */
.modal-enter-active,
.modal-leave-active {
  transition: opacity 0.2s ease;
}

.modal-enter-from,
.modal-leave-to {
  opacity: 0;
}

/* Scrollbar styling */
::-webkit-scrollbar {
  width: 6px;
  height: 6px;
}

::-webkit-scrollbar-track {
  background: transparent;
}

::-webkit-scrollbar-thumb {
  background: rgba(156, 163, 175, 0.4);
  border-radius: 3px;
}

::-webkit-scrollbar-thumb:hover {
  background: rgba(156, 163, 175, 0.6);
}

.dark ::-webkit-scrollbar-thumb {
  background: rgba(82, 82, 91, 0.5);
}

.dark ::-webkit-scrollbar-thumb:hover {
  background: rgba(82, 82, 91, 0.8);
}
</style>
