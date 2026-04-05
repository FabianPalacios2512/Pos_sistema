<template>
  <div class="h-full flex flex-col bg-gray-50 dark:bg-[#131314]">
    <div class="flex-1 flex flex-col p-6 space-y-5 overflow-hidden">
      
      <!-- NIVEL 1: Header Minimalista con Botones -->
      <div class="flex-none flex items-center justify-between">
        <div>
          <h1 class="text-2xl font-black text-gray-900 dark:text-white tracking-tight">CrediTienda</h1>
          <p class="text-sm text-gray-500 dark:text-zinc-400 mt-1">Gestión de créditos y cobros a clientes</p>
        </div>
        
        <!-- Botones de acción -->
        <div class="flex items-center gap-3">
          <!-- Botón Configuración -->
          <button 
            @click="showReminderSettingsModal = true"
            class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-zinc-200 bg-white dark:bg-zinc-900 hover:bg-gray-50 dark:hover:bg-zinc-800 rounded-md border border-gray-300 dark:border-zinc-700 transition-colors flex items-center gap-2"
            title="Configuración de recordatorios">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
            </svg>
            <span>Configuración</span>
          </button>
          
          <!-- Botón Nuevo Crédito -->
          <button 
            @click="openCreateCreditModal"
            class="px-4 py-2 text-sm font-semibold text-white dark:text-gray-900 bg-gray-900 dark:bg-white hover:bg-gray-800 dark:hover:bg-gray-100 rounded-md transition-colors flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            <span>Nuevo Crédito</span>
          </button>
        </div>
      </div>

      <!-- KPIs — Metrics Ribbon (Vercel/Linear) -->
      <div class="bg-white dark:bg-zinc-900 rounded-md border border-gray-200 dark:border-zinc-800 grid grid-cols-2 lg:grid-cols-4 divide-x divide-gray-100 dark:divide-zinc-800">
        <div class="flex flex-col gap-1 px-5 py-4">
          <div class="flex items-center justify-between">
            <p class="text-xs text-gray-400 dark:text-zinc-500 uppercase tracking-wider font-medium">Total por Cobrar</p>
            <svg class="w-4 h-4 text-rose-500 dark:text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
          </div>
          <p class="text-2xl font-black text-gray-900 dark:text-white tabular-nums">${{ formatCurrency(totalDebt) }}</p>
          <p class="text-xs text-rose-500 dark:text-rose-400">Cartera pendiente</p>
        </div>
        <div class="flex flex-col gap-1 px-5 py-4">
          <div class="flex items-center justify-between">
            <p class="text-xs text-gray-400 dark:text-zinc-500 uppercase tracking-wider font-medium">Clientes Activos</p>
            <svg class="w-4 h-4 text-blue-500 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
          </div>
          <p class="text-2xl font-black text-gray-900 dark:text-white tabular-nums">{{ customersWithDebt }}</p>
          <p class="text-xs text-gray-400 dark:text-zinc-500">Con crédito vigente</p>
        </div>
        <div class="flex flex-col gap-1 px-5 py-4">
          <div class="flex items-center justify-between">
            <p class="text-xs text-gray-400 dark:text-zinc-500 uppercase tracking-wider font-medium">Recaudado Hoy</p>
            <svg class="w-4 h-4 text-emerald-500 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
          </div>
          <p class="text-2xl font-black text-gray-900 dark:text-white tabular-nums">${{ formatCurrency(todayPayments) }}</p>
          <p class="text-xs text-emerald-500 dark:text-emerald-400">Cobrado hoy</p>
        </div>
        <div class="flex flex-col gap-1 px-5 py-4">
          <div class="flex items-center justify-between">
            <p class="text-xs text-gray-400 dark:text-zinc-500 uppercase tracking-wider font-medium">Mora Promedio</p>
            <svg class="w-4 h-4 text-amber-500 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
          </div>
          <p class="text-2xl font-black text-gray-900 dark:text-white tabular-nums">{{ averageDaysOverdue }}<span class="text-base font-normal text-gray-400 dark:text-zinc-600"> días</span></p>
          <p class="text-xs text-amber-500 dark:text-amber-400">Promedio de atraso</p>
        </div>
      </div>

      <!-- Master-Detail Layout Enterprise: 30/70 -->
      <div class="flex-1 flex rounded-md overflow-hidden shadow-sm border border-gray-200 dark:border-zinc-800 min-h-0">
        <div class="grid grid-cols-1 lg:grid-cols-10 h-full w-full">
        
        <!-- PANEL IZQUIERDO: Lista de Clientes (30%) -->
        <div class="lg:col-span-3 overflow-hidden flex flex-col bg-white dark:bg-zinc-900 relative">
          <!-- Sombra lateral para dar profundidad -->
          <div class="absolute inset-y-0 right-0 w-px bg-gray-200 dark:bg-zinc-800"></div>
          
          <!-- Header con búsqueda -->
          <div class="p-4 border-b border-gray-100 dark:border-zinc-800 bg-gray-50 dark:bg-zinc-900">
            <!-- Búsqueda -->
            <div class="relative mb-3">
              <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
              </svg>
              <input
                v-model="searchTerm"
                type="text"
                placeholder="Buscar por nombre o documento..."
                class="w-full pl-10 pr-4 py-2.5 text-sm rounded-lg border border-gray-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-zinc-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
            </div>
            
            <!-- Filtro de estado -->
            <select
              v-model="statusFilter"
              class="w-full px-3 py-2.5 text-sm rounded-lg border border-gray-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-gray-700 dark:text-zinc-300 font-medium focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors">
              <option value=""> Todos los estados</option>
              <option value="debt"> Con Deuda</option>
              <option value="overdue"> En Mora</option>
              <option value="critical"> Crítico</option>
            </select>
          </div>
          
          <!-- Lista de clientes -->
          <div class="flex-1 overflow-y-auto bg-gray-50/50 dark:bg-zinc-900 px-3 py-2">
            
            <!-- Loading state -->
            <div v-if="loading" class="flex items-center justify-center py-12">
              <div class="w-6 h-6 border-2 border-gray-300 dark:border-zinc-600 border-t-gray-900 dark:border-t-white rounded-full animate-spin"></div>
              <span class="ml-3 text-sm text-gray-500 dark:text-zinc-400">Cargando clientes...</span>
            </div>
            
            <!-- Empty state -->
            <div v-else-if="filteredCustomers.length === 0" class="flex flex-col items-center justify-center py-12 text-center px-4">
              <div class="w-14 h-14 rounded-full bg-gray-100 dark:bg-gray-800/50 flex items-center justify-center mb-3">
                <svg class="w-7 h-7 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                </svg>
              </div>
              <p class="text-sm font-medium text-gray-900 dark:text-white">Sin clientes</p>
              <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">No hay clientes con crédito activo</p>
            </div>
            
            <!-- Lista de clientes con tarjetas -->
            <div v-else class="space-y-2">
              <div
                v-for="customer in filteredCustomers"
                :key="customer.id"
                @click="selectCustomer(customer)"
                :class="[
                  'p-3.5 cursor-pointer transition-all duration-200 rounded-md border-l-4',
                  selectedCustomer?.id === customer.id
                    ? 'bg-emerald-50 dark:bg-emerald-950/30 border-l-emerald-600 dark:border-l-emerald-500'
                    : 'bg-white dark:bg-zinc-900 border-l-transparent hover:bg-gray-50 dark:hover:bg-zinc-800/40'
                ]">
              
                <div class="flex items-center gap-3">
                  <!-- Avatar con inicial o foto -->
                  <div 
                    @click.stop="customer.credit_photo && openPhotoPreview(customer.credit_photo, customer.name)"
                    class="w-10 h-10 rounded-full flex items-center justify-center flex-shrink-0 overflow-hidden transition-transform duration-200"
                    :class="[
                      customer.credit_photo ? 'cursor-pointer hover:scale-110 ring-2 ring-transparent hover:ring-blue-400' : 'text-white',
                      !customer.credit_photo && 'dark:brightness-110'
                    ]"
                    :style="!customer.credit_photo ? getAvatarStyle(customer) : {}">
                    <img v-if="customer.credit_photo" 
                       :src="customer.credit_photo" 
                       :alt="customer.name"
                       class="w-full h-full object-cover" />
                  <span v-else class="text-sm font-medium">{{ customer.name.charAt(0).toUpperCase() }}</span>
                </div>
                
                <!-- Info del cliente -->
                <div class="flex-1 min-w-0">
                  <div class="flex items-center gap-2">
                    <p class="text-sm font-semibold text-gray-900 dark:text-white truncate">
                      {{ customer.name }}
                    </p>
                    <span class="text-[9px] font-semibold px-2 py-0.5 rounded-full border flex-shrink-0 uppercase tracking-wide"
                          :class="getStatusColor(customer)">
                      {{ getStatusText(customer) }}
                    </span>
                  </div>
                  <div class="flex items-center gap-2 mt-1">
                    <p class="text-xs text-gray-500 dark:text-gray-400 truncate">
                      {{ customer.document_type }}: {{ customer.document_number }}
                    </p>
                  </div>
                </div>
                
                <!-- Deuda a la derecha -->
                <div class="text-right flex-shrink-0">
                  <span class="text-sm font-bold" :class="customer.current_debt > 0 ? 'text-rose-600 dark:text-rose-400' : 'text-emerald-600 dark:text-emerald-400'">
                    ${{ formatCurrency(customer.current_debt || 0) }}
                  </span>
                  <p class="text-[10px] text-gray-400 dark:text-gray-500">Deuda</p>
                </div>
                
                <!-- Indicador de selección -->
                <svg v-if="selectedCustomer?.id === customer.id" class="w-4 h-4 text-gray-900 dark:text-white flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
              </div>
            </div>
          </div>
        </div>
        </div>

        <!-- PANEL DERECHO: Detalle del Cliente (70%) -->
        <div class="lg:col-span-7 overflow-hidden flex flex-col bg-white dark:bg-zinc-900">
          
          <!-- Estado: No seleccionado - Empty State mejorado -->
          <div v-if="!selectedCustomer" class="flex-1 flex flex-col items-center justify-center p-12 text-center relative">
            
            <!-- Fondo decorativo sutil -->
            <div class="absolute inset-0 bg-[radial-gradient(circle_at_50%_30%,rgba(59,130,246,0.03),transparent_50%)] dark:bg-[radial-gradient(circle_at_50%_30%,rgba(59,130,246,0.05),transparent_50%)]"></div>
            
            <!-- Contenedor con glassmorphism -->
            <div class="relative z-10 bg-white/50 dark:bg-zinc-800/50 backdrop-blur-sm rounded-md p-10 border border-gray-200 dark:border-zinc-700 shadow-sm max-w-md">
              <!-- Ilustración SVG profesional más compacta -->
              <div class="mb-6 flex justify-center">
                <div class="relative">
                  <!-- Círculo principal con avatar -->
                  <div class="w-20 h-20 rounded-md bg-gradient-to-br from-gray-100 to-gray-200 dark:from-gray-800 dark:to-gray-700 flex items-center justify-center shadow-inner">
                    <svg class="w-10 h-10 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                  </div>
                  <!-- Badge de verificación -->
                  <div class="absolute -top-1 -right-1 w-6 h-6 bg-blue-500 rounded-full flex items-center justify-center shadow-sm">
                    <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                  </div>
                  <!-- Badge de dinero -->
                  <div class="absolute -bottom-1 -right-1 w-7 h-7 bg-emerald-500 rounded-full flex items-center justify-center shadow-sm">
                    <span class="text-white text-xs font-bold">$</span>
                  </div>
                </div>
              </div>
              
              <!-- Texto de bienvenida profesional -->
              <h3 class="text-xl font-semibold text-gray-800 dark:text-white mb-3">
                Portal de Créditos
              </h3>
              <p class="text-sm text-gray-500 dark:text-gray-400 leading-relaxed mb-2">
                Selecciona un cliente del panel izquierdo para visualizar su información completa, historial de crédito y registrar abonos.
              </p>
              <p class="text-xs text-gray-400 dark:text-gray-500">
                Gestión segura de créditos para tu negocio.
              </p>
              
              <!-- Indicador visual -->
              <div class="mt-6 flex items-center justify-center gap-2 text-xs text-gray-400 dark:text-gray-500">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                <span>Selecciona un cliente de la lista</span>
              </div>
            </div>
            
            <!-- Footer de seguridad -->
            <div class="absolute bottom-6 left-0 right-0 flex items-center justify-center gap-2 text-xs text-gray-400 dark:text-gray-500">
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
                    class="w-16 h-16 rounded-md flex items-center justify-center text-xl font-bold shadow-sm overflow-hidden transition-all duration-200"
                    :class="[
                      selectedCustomer.credit_photo 
                        ? 'bg-gray-100 dark:bg-zinc-800 cursor-pointer hover:scale-105 hover:shadow-xl ring-2 ring-transparent hover:ring-blue-400' 
                        : 'text-white dark:brightness-110'
                    ]"
                    :style="!selectedCustomer.credit_photo ? getAvatarStyle(selectedCustomer) : {}">
                    <img v-if="selectedCustomer.credit_photo" 
                         :src="selectedCustomer.credit_photo" 
                         :alt="selectedCustomer.name"
                         class="w-full h-full object-cover" />
                    <span v-else>{{ selectedCustomer.name?.charAt(0)?.toUpperCase() || '?' }}</span>
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
                    class="px-4 py-2.5 rounded-md text-sm font-bold transition-all duration-300 flex items-center gap-2 bg-blue-100 hover:bg-blue-200 dark:bg-blue-900/30 dark:hover:bg-blue-900/50 text-blue-700 dark:text-blue-400 border border-blue-200 dark:border-blue-800/50"
                    title="Editar información del cliente"
                  >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    Editar
                  </button>
                  
                  <!-- Botón Eliminar Crédito (solo si está al día) -->
                  <button
                    v-if="selectedCustomer.balance <= 0"
                    @click="confirmDeleteCredit(selectedCustomer)"
                    class="px-4 py-2.5 rounded-md text-sm font-bold transition-all duration-300 flex items-center gap-2 bg-rose-100 hover:bg-rose-200 dark:bg-rose-900/30 dark:hover:bg-rose-900/50 text-rose-700 dark:text-rose-400 border border-rose-200 dark:border-rose-800/50"
                    title="Eliminar crédito del cliente"
                  >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                    Eliminar
                  </button>
                  
                  <!-- Botón Enviar Recordatorio -->
                  <button
                    @click="sendReminder(selectedCustomer)"
                    :disabled="sendingReminder || selectedCustomer.balance <= 0"
                    class="px-4 py-2.5 rounded-md text-sm font-bold transition-all duration-300 flex items-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed bg-amber-100 hover:bg-amber-200 dark:bg-amber-900/30 dark:hover:bg-amber-900/50 text-amber-700 dark:text-amber-400 border border-amber-200 dark:border-amber-800/50"
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
                    class="px-5 py-2.5 rounded-md text-sm font-bold transition-all duration-300 flex items-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed bg-emerald-600 hover:bg-emerald-700 dark:bg-emerald-500 dark:hover:bg-emerald-400 text-white shadow-sm"
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
                <!-- Balance Total con desglose -->
                <div class="bg-gradient-to-br from-amber-50 to-orange-50 dark:from-amber-900/30 dark:to-orange-900/20 rounded-md p-4 border border-amber-100 dark:border-amber-800/50">
                  <p class="text-xs font-medium text-amber-600 dark:text-amber-400 uppercase tracking-wide">Deuda Total</p>
                  <p class="text-2xl font-bold text-amber-700 dark:text-amber-300 mt-1">
                    ${{ formatNumber(selectedCustomer.balance || 0) }}
                  </p>
                  <!-- 📊 Desglose de deuda: Productos + Recargo (usa porcentaje dinámico del sistema) -->
                  <div v-if="selectedCustomer.balance > 0" class="mt-3 pt-3 border-t border-amber-200 dark:border-amber-800/50 space-y-1">
                    <div class="flex justify-between items-center text-xs">
                      <span class="text-amber-600 dark:text-amber-400">Productos</span>
                      <span class="text-amber-700 dark:text-amber-300 font-medium">
                        ${{ formatNumber(Math.round(selectedCustomer.balance / (1 + (parseFloat(systemSettings.credit_surcharge_percentage) || 10) / 100))) }}
                      </span>
                    </div>
                    <div class="flex justify-between items-center text-xs">
                      <span class="text-amber-600 dark:text-amber-400">Recargo ({{ parseFloat(systemSettings.credit_surcharge_percentage) || 10 }}%)</span>
                      <span class="text-amber-700 dark:text-amber-300 font-medium">
                        +${{ formatNumber(Math.round(selectedCustomer.balance - (selectedCustomer.balance / (1 + (parseFloat(systemSettings.credit_surcharge_percentage) || 10) / 100)))) }}
                      </span>
                    </div>
                  </div>
                </div>
                
                <!-- Límite de crédito -->
                <div class="bg-gradient-to-br from-blue-50 to-indigo-50 dark:from-blue-900/30 dark:to-indigo-900/20 rounded-md p-4 border border-blue-100 dark:border-blue-800/50">
                  <p class="text-xs font-medium text-blue-600 dark:text-blue-400 uppercase tracking-wide">Límite Crédito</p>
                  <p class="text-2xl font-bold text-blue-700 dark:text-blue-300 mt-1">
                    ${{ formatNumber(selectedCustomer.credit_limit || 0) }}
                  </p>
                </div>
                
                <!-- Disponible -->
                <div :class="[
                  'rounded-md p-4 border',
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
              <div class="bg-white dark:bg-zinc-900 rounded-md shadow-sm border border-gray-200 dark:border-zinc-800 overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100 dark:border-zinc-800 flex items-center justify-between">
                  <h3 class="text-sm font-bold text-gray-900 dark:text-white uppercase tracking-wide">
                    Facturas a Crédito
                  </h3>
                  <span class="px-2.5 py-1 bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400 rounded-md text-xs font-bold">
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
              <div class="bg-white dark:bg-zinc-900 rounded-md shadow-sm border border-gray-200 dark:border-zinc-800 overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100 dark:border-zinc-800 flex items-center justify-between">
                  <h3 class="text-sm font-bold text-gray-900 dark:text-white uppercase tracking-wide">
                    Historial de Abonos
                  </h3>
                  <span class="px-2.5 py-1 bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400 rounded-md text-xs font-bold">
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
          <div class="absolute inset-0 bg-black/70 dark:bg-black/85" @click="closePaymentModal"></div>
          
          <!-- Modal Content -->
          <div class="relative bg-white dark:bg-zinc-900 rounded-md shadow-sm w-full max-w-md border border-gray-200 dark:border-zinc-700 animate-modal-enter">
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
          <div class="absolute inset-0 bg-black/70 dark:bg-black/85" @click="showCreateCreditModal = false"></div>
          
          <!-- Modal Content -->
          <div class="relative bg-white dark:bg-zinc-900 rounded-md shadow-sm w-full max-w-2xl max-h-[90vh] overflow-auto border border-gray-200 dark:border-zinc-700 animate-modal-enter">
            <!-- Header -->
            <div class="flex items-center justify-between p-5 border-b border-gray-200 dark:border-zinc-700 sticky top-0 bg-white dark:bg-zinc-900 z-10">
              <div class="flex items-center gap-3">
                <div class="w-11 h-11 bg-indigo-100 dark:bg-indigo-900/30 rounded-md flex items-center justify-center">
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
                  <div class="w-24 h-24 rounded-md overflow-hidden bg-gray-100 dark:bg-zinc-800 flex items-center justify-center border-2 border-dashed border-gray-300 dark:border-zinc-600">
                    <img v-if="photoPreview || customerForm.credit_photo" 
                         :src="photoPreview || customerForm.credit_photo" 
                         class="w-full h-full object-cover"
                         alt="Foto cliente" />
                    <svg v-else class="w-10 h-10 text-gray-300 dark:text-zinc-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                  </div>
                  <div v-if="uploadingPhoto" class="absolute inset-0 bg-black/50 rounded-md flex items-center justify-center">
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
                      @input="checkDocumentExists"
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
                class="px-5 py-2.5 bg-white dark:bg-zinc-800 hover:bg-gray-100 dark:hover:bg-zinc-700 text-gray-700 dark:text-zinc-300 text-sm font-medium rounded-md border border-gray-300 dark:border-zinc-700 transition-colors"
              >
                Cancelar
              </button>
              <button 
                @click="saveCustomerCredit"
                :disabled="!customerForm.document_number || !customerForm.name || !customerForm.credit_limit || processing"
                class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 dark:bg-indigo-500 dark:hover:bg-indigo-400 disabled:bg-gray-300 dark:disabled:bg-zinc-700 disabled:cursor-not-allowed text-white dark:text-gray-900 text-sm font-semibold rounded-md shadow-sm transition-all duration-300 flex items-center gap-2"
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

    <!-- MODAL: Configuración de Recordatorios Automáticos -->
    <Teleport to="body">
      <Transition name="modal">
        <div v-if="showReminderSettingsModal" class="fixed inset-0 z-50 flex items-center justify-center p-4">
          <!-- Overlay -->
          <div class="absolute inset-0 bg-black/70 dark:bg-black/85" @click="showReminderSettingsModal = false"></div>
          
          <!-- Modal Content -->
          <div class="relative bg-white dark:bg-zinc-900 rounded-md shadow-sm w-full max-w-md border border-gray-200 dark:border-zinc-700 animate-modal-enter">
            <!-- Header -->
            <div class="flex items-center justify-between p-5 border-b border-gray-200 dark:border-zinc-700">
              <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-md flex items-center justify-center">
                  <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                  </svg>
                </div>
                <div>
                  <h3 class="text-lg font-bold text-gray-900 dark:text-white">CRM Inteligente</h3>
                  <p class="text-xs text-gray-500 dark:text-zinc-400">Gestión automática de clientes</p>
                </div>
              </div>
              <button @click="showReminderSettingsModal = false" class="p-2 text-gray-400 hover:text-gray-600 dark:hover:text-zinc-200 hover:bg-gray-100 dark:hover:bg-zinc-800 rounded-lg transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
              </button>
            </div>
            
            <!-- Body -->
            <div class="p-5 space-y-5">
              <!-- Estado WhatsApp -->
              <div class="bg-gray-50 dark:bg-zinc-800/50 rounded-md p-4 border border-gray-200 dark:border-zinc-700">
                <div class="flex items-center justify-between">
                  <div class="flex items-center gap-3">
                    <div class="w-9 h-9 bg-green-100 dark:bg-green-500/10 rounded-lg flex items-center justify-center">
                      <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893A11.821 11.821 0 0020.885 3.515"/>
                      </svg>
                    </div>
                    <div>
                      <p class="text-sm font-medium text-gray-900 dark:text-white">WhatsApp</p>
                      <p class="text-xs" :class="whatsappConnected ? 'text-green-600 dark:text-green-400' : 'text-red-500 dark:text-red-400'">
                        {{ whatsappConnected ? '● Conectado' : '○ Desconectado' }}
                      </p>
                    </div>
                  </div>
                  <button 
                    v-if="!whatsappConnected"
                    @click="openWhatsAppConfig"
                    class="px-3 py-1.5 bg-green-600 hover:bg-green-700 text-white text-xs font-medium rounded-lg transition-colors">
                    Conectar
                  </button>
                </div>
              </div>

              <!-- Funciones CRM automáticas -->
              <div class="space-y-3">
                <h4 class="text-sm font-semibold text-gray-700 dark:text-zinc-300">Funciones automáticas activas:</h4>
                
                <!-- Confirmación post-compra -->
                <div class="flex items-start gap-3 p-3 bg-emerald-50 dark:bg-emerald-900/20 rounded-md border border-emerald-100 dark:border-emerald-800/30">
                  <div class="w-8 h-8 bg-emerald-100 dark:bg-emerald-800/50 rounded-lg flex items-center justify-center flex-shrink-0 mt-0.5">
                    <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                  </div>
                  <div>
                    <p class="text-sm font-medium text-emerald-800 dark:text-emerald-300">Confirmación de compra</p>
                    <p class="text-xs text-emerald-600 dark:text-emerald-400">Después de cada compra a crédito, el cliente recibe un mensaje con el resumen y su saldo disponible.</p>
                  </div>
                </div>
              </div>
              
              <!-- Modo de recordatorios -->
              <div>
                <label class="block text-sm font-semibold text-gray-700 dark:text-zinc-300 mb-3">
                  Modo de recordatorios
                </label>
                <div class="grid grid-cols-1 gap-2">
                  <!-- Modo Manual -->
                  <label 
                    class="flex items-center gap-3 p-3 rounded-md border cursor-pointer transition-all duration-200"
                    :class="reminderSettings.frequency === 'manual' 
                      ? 'bg-gray-100 dark:bg-zinc-800 border-gray-300 dark:border-zinc-600' 
                      : 'bg-white dark:bg-zinc-800/50 border-gray-200 dark:border-zinc-700 hover:border-gray-300 dark:hover:border-zinc-600'"
                  >
                    <input 
                      type="radio" 
                      name="frequency" 
                      value="manual" 
                      v-model="reminderSettings.frequency"
                      class="w-4 h-4 text-blue-600 border-gray-300 focus:ring-blue-500"
                    />
                    <div class="flex-1">
                      <p class="text-sm font-medium text-gray-900 dark:text-white">Manual</p>
                      <p class="text-xs text-gray-500 dark:text-zinc-400">Envío manual desde el botón "Recordatorio"</p>
                    </div>
                  </label>
                  
                  <!-- Modo Inteligente -->
                  <label 
                    class="flex items-center gap-3 p-3 rounded-md border cursor-pointer transition-all duration-200"
                    :class="reminderSettings.frequency === 'smart' 
                      ? 'bg-blue-50 dark:bg-blue-500/10 border-blue-200 dark:border-blue-500/30' 
                      : 'bg-white dark:bg-zinc-800/50 border-gray-200 dark:border-zinc-700 hover:border-gray-300 dark:hover:border-zinc-600'"
                  >
                    <input 
                      type="radio" 
                      name="frequency" 
                      value="smart" 
                      v-model="reminderSettings.frequency"
                      class="w-4 h-4 text-blue-600 border-gray-300 focus:ring-blue-500"
                    />
                    <div class="flex-1">
                      <div class="flex items-center gap-2">
                        <p class="text-sm font-medium text-gray-900 dark:text-white">Modo Inteligente</p>
                        <span class="px-1.5 py-0.5 bg-blue-100 dark:bg-blue-900/50 text-blue-700 dark:text-blue-400 text-[10px] font-bold rounded">RECOMENDADO</span>
                      </div>
                      <p class="text-xs text-gray-500 dark:text-zinc-400">
                        Envía recordatorios automáticamente después de 20 días de mora, cada 3 días para no saturar al cliente
                      </p>
                    </div>
                    <div class="w-8 h-8 bg-blue-100 dark:bg-blue-800/50 rounded-lg flex items-center justify-center">
                      <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                      </svg>
                    </div>
                  </label>
                </div>
              </div>
              
              <!-- Info del modo inteligente -->
              <div v-if="reminderSettings.frequency === 'smart'" class="bg-blue-50 dark:bg-blue-900/20 rounded-md p-4 border border-blue-100 dark:border-blue-800/30">
                <h4 class="text-sm font-medium text-blue-800 dark:text-blue-300 mb-2 flex items-center gap-2">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                  </svg>
                  Cómo funciona el modo inteligente
                </h4>
                <ul class="text-xs text-blue-700 dark:text-blue-400 space-y-1.5">
                  <li class="flex items-start gap-2">
                    <span class="text-blue-500 mt-0.5">•</span>
                    <span>Espera 20 días después de la compra antes del primer recordatorio</span>
                  </li>
                  <li class="flex items-start gap-2">
                    <span class="text-blue-500 mt-0.5">•</span>
                    <span>Envía recordatorios cada 3 días (máximo 5 por mes)</span>
                  </li>
                  <li class="flex items-start gap-2">
                    <span class="text-blue-500 mt-0.5">•</span>
                    <span>Los mensajes son amigables y no agresivos</span>
                  </li>
                  <li class="flex items-start gap-2">
                    <span class="text-blue-500 mt-0.5">•</span>
                    <span>Se detiene automáticamente cuando el cliente paga</span>
                  </li>
                </ul>
              </div>
            </div>
            
            <!-- Footer -->
            <div class="flex gap-3 p-5 border-t border-gray-200 dark:border-zinc-700">
              <button
                @click="showReminderSettingsModal = false"
                class="flex-1 px-4 py-2.5 bg-gray-100 dark:bg-zinc-800 hover:bg-gray-200 dark:hover:bg-zinc-700 text-gray-700 dark:text-zinc-300 font-medium rounded-md transition-colors">
                Cancelar
              </button>
              <button
                @click="saveReminderSettings"
                :disabled="savingSettings"
                class="flex-1 px-4 py-2.5 bg-blue-600 hover:bg-blue-700 disabled:bg-blue-400 text-white font-medium rounded-md transition-colors flex items-center justify-center gap-2">
                <svg v-if="savingSettings" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                {{ savingSettings ? 'Guardando...' : 'Guardar' }}
              </button>
            </div>
          </div>
        </div>
      </Transition>
    </Teleport>

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
        <div v-if="showEditCustomerModal" class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-black/70 dark:bg-black/85">
          <Transition
            enter-active-class="transition-all duration-300 ease-out"
            leave-active-class="transition-all duration-200 ease-in"
            enter-from-class="opacity-0 scale-95 translate-y-4"
            enter-to-class="opacity-100 scale-100 translate-y-0"
            leave-from-class="opacity-100 scale-100 translate-y-0"
            leave-to-class="opacity-0 scale-95 translate-y-4"
          >
            <div v-if="showEditCustomerModal" @click.stop class="bg-white dark:bg-zinc-900 rounded-md shadow-sm w-full max-w-2xl max-h-[90vh] overflow-y-auto border border-gray-200 dark:border-zinc-800">
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
                <div class="bg-gray-50 dark:bg-zinc-800/50 rounded-md p-4 border border-gray-200 dark:border-zinc-700">
                  <label class="block text-sm font-semibold text-gray-700 dark:text-zinc-300 mb-3">Foto del Cliente</label>
                  
                  <div class="flex items-center gap-4">
                    <!-- Preview de la foto -->
                    <div class="w-24 h-24 rounded-md overflow-hidden bg-gray-200 dark:bg-zinc-700 flex items-center justify-center flex-shrink-0">
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
                <button @click="showEditCustomerModal = false" type="button" class="px-5 py-2.5 bg-white dark:bg-zinc-900 hover:bg-gray-50 dark:hover:bg-zinc-800 text-gray-700 dark:text-zinc-300 text-sm font-bold rounded-md border border-gray-300 dark:border-zinc-700 transition-all duration-200">
                  Cancelar
                </button>
                <button @click="submitEditCustomer" :disabled="processingEdit || !editForm.name" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-400 text-white dark:text-gray-900 text-sm font-semibold rounded-md shadow-sm transition-all duration-300 disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2">
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
        <div v-if="showPhotoPreviewModal" @click="closePhotoPreview" class="fixed inset-0 z-[110] flex items-center justify-center p-4 bg-black/90 dark:bg-black/95">
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
                  class="max-w-full max-h-[70vh] object-contain rounded-md shadow-sm"
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

    <!-- 🗑️ MODAL: Confirmar Eliminación de Crédito -->
    <Teleport to="body">
      <Transition
        enter-active-class="transition-opacity duration-200 ease-out"
        leave-active-class="transition-opacity duration-150 ease-in"
        enter-from-class="opacity-0"
        enter-to-class="opacity-100"
        leave-from-class="opacity-100"
        leave-to-class="opacity-0"
      >
        <div v-if="showDeleteModal" @click="showDeleteModal = false" class="fixed inset-0 bg-black/60 z-[100] flex items-center justify-center p-4">
          <Transition
            enter-active-class="transition-all duration-200 ease-out"
            leave-active-class="transition-all duration-150 ease-in"
            enter-from-class="opacity-0 scale-95"
            enter-to-class="opacity-100 scale-100"
            leave-from-class="opacity-100 scale-100"
            leave-to-class="opacity-0 scale-95"
          >
            <div v-if="showDeleteModal" @click.stop class="bg-white dark:bg-zinc-900 rounded-md shadow-sm max-w-md w-full overflow-hidden border border-gray-200 dark:border-zinc-800">
              
              <!-- Icono de advertencia -->
              <div class="p-6 text-center">
                <div class="mx-auto w-16 h-16 rounded-full bg-amber-100 dark:bg-amber-900/30 flex items-center justify-center mb-4">
                  <svg class="w-8 h-8 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                  </svg>
                </div>
                
                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">
                  ¿Eliminar Cliente?
                </h3>
                
                <p class="text-gray-600 dark:text-zinc-400 mb-1">
                  ¿Estás seguro de eliminar el cliente <strong class="text-gray-900 dark:text-white">"{{ customerToDelete?.name }}"</strong>?
                </p>
                
                <p class="text-sm text-rose-600 dark:text-rose-400 font-medium mb-4">
                  Esta acción no se puede deshacer.
                </p>
                
                <!-- Lista de lo que se eliminará -->
                <div class="bg-gray-50 dark:bg-zinc-800/50 rounded-md p-4 text-left mb-6">
                  <p class="text-xs font-semibold text-gray-700 dark:text-zinc-300 uppercase tracking-wide mb-3">Se eliminará:</p>
                  <ul class="space-y-2 text-sm text-gray-600 dark:text-zinc-400">
                    <li class="flex items-center gap-2">
                      <svg class="w-4 h-4 text-rose-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                      </svg>
                      Historial de crédito
                    </li>
                    <li class="flex items-center gap-2">
                      <svg class="w-4 h-4 text-rose-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                      </svg>
                      Pagos registrados
                    </li>
                    <li class="flex items-center gap-2">
                      <svg class="w-4 h-4 text-rose-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                      </svg>
                      Foto del cliente (si existe)
                    </li>
                    <li class="flex items-center gap-2">
                      <svg class="w-4 h-4 text-rose-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                      </svg>
                      Acceso a crédito del cliente
                    </li>
                  </ul>
                </div>
              </div>
              
              <!-- Botones de acción -->
              <div class="bg-gray-50 dark:bg-zinc-800/50 px-6 py-4 flex gap-3">
                <button
                  @click="showDeleteModal = false"
                  class="flex-1 px-4 py-2.5 bg-white dark:bg-zinc-800 hover:bg-gray-100 dark:hover:bg-zinc-700 text-gray-700 dark:text-zinc-300 font-medium rounded-md border border-gray-300 dark:border-zinc-700 transition-all duration-200"
                >
                  Cancelar
                </button>
                <button
                  @click="executeDeleteCredit"
                  :disabled="deletingCredit"
                  class="flex-1 px-4 py-2.5 bg-rose-600 hover:bg-rose-700 disabled:bg-rose-400 text-white font-bold rounded-md transition-all duration-200 disabled:cursor-not-allowed flex items-center justify-center gap-2"
                >
                  <svg v-if="deletingCredit" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                  </svg>
                  <span>{{ deletingCredit ? 'Eliminando...' : 'Eliminar' }}</span>
                </button>
              </div>
              
            </div>
          </Transition>
        </div>
      </Transition>
    </Teleport>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted, watch } from 'vue'
import { customersService } from '../services/customersService.js'
import { useToast } from '../composables/useToast.js'
import { useUIContextStore } from '../store/uiContextStore.js'
import axiosInstance from '../services/apiClient.js'
import axios from 'axios'

const { showSuccess, showError } = useToast()
const uiContextStore = useUIContextStore()

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
let documentCheckTimeout = null // Para debounce de búsqueda

// 🗑️ Modal de confirmación para eliminar crédito
const showDeleteModal = ref(false)
const customerToDelete = ref(null)
const deletingCredit = ref(false)
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

// 🔔 Modal de configuración de recordatorios
const showReminderSettingsModal = ref(false)
const savingSettings = ref(false)
const showWhatsAppModal = ref(false)
const whatsappConnected = ref(false)
const reminderSettings = ref({
  frequency: 'smart', // Por defecto modo inteligente
  sendHour: '9',
  minDaysOverdue: 20 // 20 días por defecto para modo inteligente
})

// Función para abrir WhatsApp desde CRM
const openWhatsAppConfig = () => {
  showReminderSettingsModal.value = false
  // Emitir evento para abrir modal de WhatsApp global
  window.dispatchEvent(new CustomEvent('open-whatsapp-modal'))
}

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

// 🔧 System settings para obtener el porcentaje de recargo dinámicamente
const systemSettings = ref({
  credit_surcharge_percentage: 10,
  company_name: ''
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

// Cargar configuración del sistema (porcentaje de recargo, nombre de empresa)
const loadSystemSettings = async () => {
  try {
    const response = await axiosInstance.get('/system-settings')
    if (response.data && response.data.data) {
      systemSettings.value = response.data.data
    }
  } catch (error) {
    console.warn('No se pudieron cargar los system settings:', error.message)
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
  // 🎯 El disponible se calcula contra el SUBTOTAL_DEBT (productos sin recargo)
  // El recargo es ganancia del negocio, NO cuenta contra el cupo del cliente
  // Usamos subtotal_debt que viene directamente de la BD
  const subtotalDebt = customer.subtotal_debt || 0
  return Math.max(0, (customer.credit_limit || 0) - subtotalDebt)
}

// Función helper para obtener el subtotal de productos pendientes (sin recargo)
const getSubtotalPendiente = (customer) => {
  // 🎯 USAR subtotal_debt del cliente (calculado correctamente en el backend)
  // NO calcular desde las facturas porque su status='paid' (la venta se hizo)
  // El crédito es independiente del status de la factura
  if (customer.subtotal_debt !== undefined && customer.subtotal_debt !== null) {
    return parseFloat(customer.subtotal_debt) || 0
  }
  
  // Fallback: Si no hay subtotal_debt, usar current_debt / factor
  const debt = customer.balance || customer.current_debt || 0
  const surchargePercent = parseFloat(systemSettings.value?.credit_surcharge_percentage) || 10
  const factor = 1 + (surchargePercent / 100)
  return debt > 0 ? Math.round(debt / factor) : 0
}

// Función para calcular el crédito disponible (puede ser negativo para mostrar cuánto sobrepasó)
const getAvailableCreditAmount = (customer) => {
  // 🎯 El disponible es: Límite - subtotal_debt (sin recargo)
  // El recargo NO cuenta contra el cupo (es ganancia del negocio)
  const limit = customer.credit_limit || 0
  const subtotalDebt = customer.subtotal_debt || getSubtotalPendiente(customer)
  return Math.max(0, limit - subtotalDebt)
}

// Nueva función para seleccionar cliente en el panel izquierdo
const selectCustomer = async (customer) => {
  selectedCustomer.value = customer
  await loadCustomerCreditDetail(customer.id)
}

// Función para obtener color del avatar basado en el ID o customer
const getAvatarColor = (customerOrId) => {
  // Colores profesionales sutiles con mejor legibilidad usando HSL
  const colors = [
    { bg: 'hsl(210, 70%, 45%)', dark: 'hsl(210, 80%, 55%)' },  // Azul profesional
    { bg: 'hsl(260, 65%, 50%)', dark: 'hsl(260, 75%, 60%)' },  // Índigo
    { bg: 'hsl(280, 60%, 50%)', dark: 'hsl(280, 70%, 60%)' },  // Púrpura
    { bg: 'hsl(340, 60%, 50%)', dark: 'hsl(340, 70%, 60%)' },  // Rosa moderado
    { bg: 'hsl(160, 65%, 42%)', dark: 'hsl(160, 75%, 52%)' },  // Esmeralda
    { bg: 'hsl(180, 65%, 45%)', dark: 'hsl(180, 75%, 55%)' },  // Teal
    { bg: 'hsl(200, 70%, 48%)', dark: 'hsl(200, 80%, 58%)' },  // Cian
    { bg: 'hsl(45, 75%, 50%)', dark: 'hsl(45, 85%, 60%)' },    // Ámbar
    { bg: 'hsl(25, 70%, 52%)', dark: 'hsl(25, 80%, 62%)' },    // Naranja
    { bg: 'hsl(230, 65%, 50%)', dark: 'hsl(230, 75%, 60%)' }   // Azul oscuro
  ]
  
  // Acepta tanto un objeto customer como un ID directo
  const id = typeof customerOrId === 'object' ? customerOrId?.id : customerOrId
  const index = (id || 0) % colors.length
  const color = colors[index]
  
  // Retornar estilo con CSS variables para modo claro/oscuro
  return `text-white`
}

// Función auxiliar para obtener el color de fondo del avatar
const getAvatarStyle = (customerOrId) => {
  const colors = [
    { bg: 'hsl(210, 70%, 45%)', dark: 'hsl(210, 80%, 55%)' },
    { bg: 'hsl(260, 65%, 50%)', dark: 'hsl(260, 75%, 60%)' },
    { bg: 'hsl(280, 60%, 50%)', dark: 'hsl(280, 70%, 60%)' },
    { bg: 'hsl(340, 60%, 50%)', dark: 'hsl(340, 70%, 60%)' },
    { bg: 'hsl(160, 65%, 42%)', dark: 'hsl(160, 75%, 52%)' },
    { bg: 'hsl(180, 65%, 45%)', dark: 'hsl(180, 75%, 55%)' },
    { bg: 'hsl(200, 70%, 48%)', dark: 'hsl(200, 80%, 58%)' },
    { bg: 'hsl(45, 75%, 50%)', dark: 'hsl(45, 85%, 60%)' },
    { bg: 'hsl(25, 70%, 52%)', dark: 'hsl(25, 80%, 62%)' },
    { bg: 'hsl(230, 65%, 50%)', dark: 'hsl(230, 75%, 60%)' }
  ]
  
  const id = typeof customerOrId === 'object' ? customerOrId?.id : customerOrId
  const index = (id || 0) % colors.length
  const color = colors[index]
  
  return {
    backgroundColor: color.bg,
    '--avatar-dark-bg': color.dark
  }
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

  if (!customer.phone) {
    showError('El cliente no tiene número de teléfono registrado')
    return
  }

  sendingReminder.value = true
  try {
    const response = await axiosInstance.post('/credit-reminders', {
      customer_id: customer.id
    })

    if (response.data.success) {
      const channels = response.data.data?.sent_channels || []
      if (channels.length > 0) {
        showSuccess(`Recordatorio enviado por ${channels.join(' y ')}`)
      } else {
        showSuccess('Recordatorio enviado')
      }
    } else {
      // Mensaje de error amigable
      const message = response.data.message || 'No se pudo enviar el recordatorio'
      if (response.data.requires_whatsapp) {
        showError('WhatsApp no está conectado. Conéctalo desde el botón de configuración ⚙️')
      } else {
        showError(message)
      }
    }
  } catch (error) {
    console.error('Error sending reminder:', error)
    // Mensaje amigable sin detalles técnicos
    showError('No se pudo enviar el recordatorio. Verifica que WhatsApp esté conectado.')
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

// 🗑️ Función para confirmar y eliminar crédito
// 🗑️ Función para abrir modal de confirmación de eliminación
const confirmDeleteCredit = (customer) => {
  // Verificar que el cliente esté al día (sin deuda)
  if (customer.balance > 0 || customer.current_debt > 0) {
    showError('No se puede eliminar el crédito. El cliente tiene una deuda pendiente.')
    return
  }
  
  // Abrir modal de confirmación
  customerToDelete.value = customer
  showDeleteModal.value = true
}

// 🗑️ Función para ejecutar la eliminación del crédito
const executeDeleteCredit = async () => {
  if (!customerToDelete.value) return
  
  deletingCredit.value = true
  try {
    const response = await axiosInstance.delete(`/customers/${customerToDelete.value.id}/credit`)
    
    if (response.data.success) {
      showSuccess('Crédito eliminado correctamente')
      showDeleteModal.value = false
      customerToDelete.value = null
      selectedCustomer.value = null
      await loadCustomers()
    } else {
      showError(response.data.message || 'Error al eliminar el crédito')
    }
  } catch (error) {
    console.error('Error eliminando crédito:', error)
    showError(error.response?.data?.message || 'Error al eliminar el crédito')
  } finally {
    deletingCredit.value = false
  }
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
  // Limpiar timeout anterior
  if (documentCheckTimeout) {
    clearTimeout(documentCheckTimeout)
  }
  
  // Si el documento es muy corto, resetear estado
  if (!customerForm.value.document_number || customerForm.value.document_number.length < 5) {
    customerExists.value = false
    checkingDocument.value = false
    return
  }

  // Mostrar indicador de carga
  checkingDocument.value = true
  
  // Esperar 500ms antes de buscar (debounce)
  documentCheckTimeout = setTimeout(async () => {
    try {
      const response = await axiosInstance.post('/customers/check-document', {
        document_type: customerForm.value.document_type,
        document_number: customerForm.value.document_number
      })

      if (response.data.exists) {
        customerExists.value = true
        const existingCustomer = response.data.data
        
        // Auto-llenar todos los campos del cliente encontrado
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
        
        console.log('✅ Cliente encontrado y campos auto-llenados:', existingCustomer.name)
      } else {
        customerExists.value = false
        console.log('📝 Cliente no encontrado, listo para crear nuevo')
      }
    } catch (error) {
      // Búsqueda opcional: No mostrar error si no encuentra el cliente
      console.log('📝 No se encontró cliente con ese documento')
      customerExists.value = false
    } finally {
      checkingDocument.value = false
    }
  }, 500) // Esperar 500ms después de dejar de escribir
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
      const isNewCustomer = !customerExists.value
      const createdCustomer = response.data.data // 📦 Cliente con credit_id y token generados
      
      showSuccess(customerExists.value ? 'Crédito actualizado exitosamente' : 'Crédito creado exitosamente')
      showCreateCreditModal.value = false
      await loadCustomers()
      
      // 🎉 Enviar mensajes de bienvenida para CUALQUIER nuevo crédito
      // (tanto clientes nuevos como clientes existentes que reciben crédito por primera vez o de nuevo)
      if (customerForm.value.email || customerForm.value.phone) {
        await sendWelcomeMessages(createdCustomer)
      }
    }
  } catch (error) {
    console.error('❌ Error al crear/actualizar cliente:', error)
    console.error('📊 Respuesta del servidor:', error.response?.data)
    console.error('📝 Datos enviados:', customerForm.value)
    
    if (error.response?.data?.errors) {
      const errors = error.response.data.errors
      const firstError = Object.values(errors)[0]
      
      if (errors.document_number) {
        showError('❌ Este número de documento ya está registrado')
      } else if (errors.email) {
        showError('❌ Este correo electrónico ya está registrado')
      } else if (errors.name) {
        showError('❌ El nombre del cliente es requerido')
      } else {
        showError(`❌ Error de validación: ${Array.isArray(firstError) ? firstError[0] : firstError}`)
      }
    } else if (error.response?.data?.message) {
      showError(`❌ Error: ${error.response.data.message}`)
    } else if (error.response?.data?.error) {
      showError(`❌ Error del servidor: ${error.response.data.error}`)
    } else if (error.message) {
      showError(`❌ Error al crear cliente: ${error.message}`)
    } else {
      showError('❌ Error desconocido al crear el cliente. Verifica la consola para más detalles.')
    }
  } finally {
    processing.value = false
  }
}

// 🎉 FUNCIÓN CRM: Enviar mensajes de bienvenida a nuevo cliente con crédito
const sendWelcomeMessages = async (createdCustomer = null) => {
  try {
    const customerData = createdCustomer || customerForm.value
    // 🔧 Usar nombre de empresa de systemSettings o fallback
    const companyName = systemSettings.value.company_name || 'MATIMAA'
    // 🔧 Obtener porcentaje de recargo del sistema (dinámico)
    const surchargePercent = parseFloat(systemSettings.value.credit_surcharge_percentage) || 10
    
    // 🔗 Construir URL del portal de crédito
    const baseUrl = window.location.origin
    const portalUrl = customerData.credit_access_token 
      ? `${baseUrl}/mi-credito?token=${customerData.credit_access_token}`
      : `${baseUrl}/mi-credito`
    
    // Preparar datos del crédito
    const creditInfo = {
      customerName: customerData.name,
      creditLimit: new Intl.NumberFormat('es-CO', { style: 'currency', currency: 'COP', minimumFractionDigits: 0 }).format(customerData.credit_limit),
      phone: customerData.phone,
      email: customerData.email,
      creditId: customerData.credit_id || 'Pendiente'
    }

    // Calcular ejemplo de recargo dinámicamente
    const ejemploProductos = 100000
    const ejemploRecargo = Math.round(ejemploProductos * surchargePercent / 100)
    const ejemploTotal = ejemploProductos + ejemploRecargo

    // 📧 Enviar email de bienvenida si tiene correo
    if (customerData.email && customerData.email.includes('@')) {
      try {
        const currentDate = new Date().toLocaleDateString('es-CO', { 
          year: 'numeric', 
          month: 'long', 
          day: 'numeric' 
        })
        
        await axiosInstance.post('/send-email', {
          to: customerData.email,
          subject: `Crédito Aprobado - ${companyName}`,
          html: `
            <div style="font-family: 'Segoe UI', Arial, sans-serif; max-width: 600px; margin: 0 auto; background: #f4f4f5;">
              
              <!-- Header con gradiente sutil -->
              <div style="background: linear-gradient(135deg, #0f766e 0%, #134e4a 100%); padding: 35px 40px; text-align: center;">
                <h1 style="color: #ffffff; margin: 0; font-size: 26px; font-weight: 600; letter-spacing: 0.5px;">${companyName}</h1>
                <p style="color: #5eead4; margin: 8px 0 0 0; font-size: 13px; letter-spacing: 1px;">NOTIFICACIÓN DE CRÉDITO</p>
              </div>
              
              <!-- Contenido principal -->
              <div style="background: #ffffff; padding: 40px;">
                
                <!-- Saludo con icono visual -->
                <div style="text-align: center; margin-bottom: 30px;">
                  <div style="display: inline-block; background: linear-gradient(135deg, #10b981 0%, #059669 100%); width: 60px; height: 60px; border-radius: 50%; line-height: 60px; margin-bottom: 15px;">
                    <span style="color: white; font-size: 28px;">✓</span>
                  </div>
                  <h2 style="color: #0f766e; margin: 0; font-size: 22px; font-weight: 600;">Crédito Aprobado</h2>
                </div>
                
                <p style="color: #374151; font-size: 15px; margin: 0 0 20px 0; line-height: 1.6;">
                  Estimado(a) <strong>${customerData.name}</strong>,
                </p>
                
                <p style="color: #6b7280; font-size: 14px; line-height: 1.7; margin: 0 0 30px 0;">
                  Nos complace informarle que su solicitud de crédito ha sido evaluada y 
                  <strong style="color: #059669;">aprobada satisfactoriamente</strong>. 
                  A continuación encontrará los detalles de su línea de crédito:
                </p>
                
                <!-- Cupo de crédito destacado -->
                <div style="background: linear-gradient(135deg, #0f766e 0%, #134e4a 100%); border-radius: 12px; padding: 30px; margin: 25px 0; text-align: center;">
                  <p style="color: #99f6e4; margin: 0 0 8px 0; font-size: 11px; text-transform: uppercase; letter-spacing: 2px; font-weight: 500;">Su Cupo de Crédito</p>
                  <p style="color: #ffffff; margin: 0; font-size: 38px; font-weight: 700;">${creditInfo.creditLimit}</p>
                  <p style="color: #5eead4; margin: 10px 0 0 0; font-size: 12px;">Disponible desde hoy</p>
                </div>
                
                <!-- Información de la cuenta con mejor diseño -->
                <div style="background: #f9fafb; border-radius: 8px; padding: 20px; margin: 25px 0;">
                  <p style="color: #0f766e; font-size: 13px; font-weight: 600; margin: 0 0 15px 0; text-transform: uppercase; letter-spacing: 0.5px;">Detalles de su Cuenta</p>
                  <table style="width: 100%; border-collapse: collapse;">
                    <tr>
                      <td style="padding: 10px 0; color: #6b7280; font-size: 13px; border-bottom: 1px solid #e5e7eb;">Titular</td>
                      <td style="padding: 10px 0; color: #111827; font-size: 13px; font-weight: 500; text-align: right; border-bottom: 1px solid #e5e7eb;">${customerData.name}</td>
                    </tr>
                    <tr>
                      <td style="padding: 10px 0; color: #6b7280; font-size: 13px; border-bottom: 1px solid #e5e7eb;">Documento</td>
                      <td style="padding: 10px 0; color: #111827; font-size: 13px; font-weight: 500; text-align: right; border-bottom: 1px solid #e5e7eb;">${customerData.document_type} ${customerData.document_number}</td>
                    </tr>
                    <tr>
                      <td style="padding: 10px 0; color: #6b7280; font-size: 13px; border-bottom: 1px solid #e5e7eb;">Cupo Aprobado</td>
                      <td style="padding: 10px 0; color: #059669; font-size: 13px; font-weight: 600; text-align: right; border-bottom: 1px solid #e5e7eb;">${creditInfo.creditLimit}</td>
                    </tr>
                    <tr>
                      <td style="padding: 10px 0; color: #6b7280; font-size: 13px;">Fecha de Activación</td>
                      <td style="padding: 10px 0; color: #111827; font-size: 13px; font-weight: 500; text-align: right;">${currentDate}</td>
                    </tr>
                  </table>
                </div>
                
                <!-- Nota importante con diseño amigable -->
                <div style="background: #fef3c7; border-left: 4px solid #f59e0b; padding: 15px 20px; margin: 25px 0; border-radius: 0 8px 8px 0;">
                  <p style="color: #92400e; margin: 0; font-size: 13px; line-height: 1.6;">
                    <strong>Nota:</strong> Recuerde realizar sus pagos de manera oportuna para mantener 
                    su crédito activo y acceder a futuros incrementos de cupo.
                  </p>
                </div>
                
                <!-- 📊 Información sobre recargo financiero (transparencia) -->
                <div style="background: #f0f9ff; border: 1px solid #bae6fd; padding: 18px 20px; margin: 20px 0; border-radius: 8px;">
                  <p style="color: #0c4a6e; margin: 0 0 10px 0; font-size: 13px; font-weight: 600;">
                    📊 Información de su Crédito
                  </p>
                  <p style="color: #0369a1; margin: 0; font-size: 13px; line-height: 1.7;">
                    Las compras realizadas a crédito incluyen un <strong>recargo financiero del ${surchargePercent}%</strong> 
                    sobre el valor de los productos, el cual se aplica al momento de la compra.
                  </p>
                  <div style="background: #e0f2fe; padding: 12px; margin: 12px 0 0 0; border-radius: 6px;">
                    <p style="color: #075985; margin: 0 0 8px 0; font-size: 12px; font-weight: 600;">Ejemplo:</p>
                    <table style="width: 100%; font-size: 12px; color: #0c4a6e;">
                      <tr>
                        <td style="padding: 3px 0;">Productos comprados:</td>
                        <td style="text-align: right; font-weight: 500;">$${ejemploProductos.toLocaleString('es-CO')}</td>
                      </tr>
                      <tr>
                        <td style="padding: 3px 0;">Recargo financiero (${surchargePercent}%):</td>
                        <td style="text-align: right; font-weight: 500;">+$${ejemploRecargo.toLocaleString('es-CO')}</td>
                      </tr>
                      <tr style="border-top: 2px solid #0891b2;">
                        <td style="padding: 6px 0 3px 0; font-weight: 600;">Total a pagar:</td>
                        <td style="text-align: right; font-weight: 700; color: #0369a1;">$${ejemploTotal.toLocaleString('es-CO')}</td>
                      </tr>
                    </table>
                  </div>
                </div>
                
                <!-- 🔗 Botón de acceso al portal de crédito -->
                <div style="text-align: center; margin: 30px 0;">
                  <p style="color: #374151; font-size: 14px; margin: 0 0 15px 0;">
                    Consulte su crédito en línea en cualquier momento:
                  </p>
                  <a href="${portalUrl}" 
                     style="display: inline-block; background: linear-gradient(135deg, #0f766e 0%, #134e4a 100%); color: #ffffff; padding: 14px 32px; font-size: 14px; font-weight: 600; text-decoration: none; border-radius: 8px; letter-spacing: 0.5px;">
                    📊 Ver Mi Crédito
                  </a>
                  <p style="color: #9ca3af; font-size: 11px; margin: 12px 0 0 0;">
                    Su ID de Crédito: <strong style="color: #0f766e;">${creditInfo.creditId}</strong>
                  </p>
                </div>
                
                <p style="color: #6b7280; font-size: 14px; line-height: 1.7; margin: 25px 0 0 0;">
                  Si tiene alguna pregunta sobre su crédito, no dude en contactarnos. 
                  Estamos aquí para ayudarle.
                </p>
                
                <p style="color: #374151; font-size: 14px; margin: 30px 0 0 0;">
                  Cordialmente,<br>
                  <strong style="color: #0f766e;">${companyName}</strong>
                </p>
                
              </div>
              
              <!-- Footer -->
              <div style="background: #134e4a; padding: 25px 40px; text-align: center;">
                <p style="color: #5eead4; font-size: 13px; margin: 0 0 5px 0;">¿Tiene preguntas?</p>
                <p style="color: #99f6e4; font-size: 11px; margin: 0;">Contáctenos y con gusto le atenderemos</p>
                <div style="border-top: 1px solid #0f766e; margin-top: 20px; padding-top: 15px;">
                  <p style="color: #6b7280; font-size: 10px; margin: 0;">
                    Este es un mensaje automático. Por favor no responda a este correo.
                  </p>
                </div>
              </div>
              
            </div>
          `
        })
        console.log('✅ Email de bienvenida enviado correctamente')
      } catch (emailError) {
        console.warn('⚠️ No se pudo enviar el email de bienvenida:', emailError.message)
      }
    }

    // 📱 Enviar mensaje de WhatsApp si tiene teléfono
    if (customerData.phone && customerData.phone.length >= 10) {
      try {
        // Formatear número de teléfono
        let phone = customerData.phone.replace(/[\s\-\(\)]/g, '')
        if (!phone.startsWith('+')) {
          if (phone.startsWith('57')) {
            phone = '+' + phone
          } else if (phone.startsWith('3')) {
            phone = '+57' + phone
          }
        }
        
        const welcomeMessage = `🎉 *¡Bienvenido a ${companyName}!*

Hola *${customerData.name}*,

Tu crédito ha sido *activado exitosamente*. 

💳 *Tu Cupo:* ${creditInfo.creditLimit}

📋 *Tu cuenta:*
• ID: *${creditInfo.creditId}*
• Documento: ${customerData.document_type} ${customerData.document_number}

📊 *Nota:* Las compras a crédito incluyen un recargo del ${surchargePercent}%.

💡 Paga a tiempo para mantener tu crédito activo.

¡Gracias por confiar en nosotros! 🙌

---
🔗 *Consulta tu crédito aquí:*
${portalUrl}

_${companyName}_`

        // Enviar mensaje directo al backend de WhatsApp
        const rawTenantId = window.location.hostname.split('.')[0] || 'default'
        const tenantId = rawTenantId.replace(/-/g, '_') // Normalizar: guiones a guiones bajos
        
        // En producción usar proxy Nginx, en desarrollo usar localhost
        const isLocalhost = window.location.hostname === 'localhost' || 
                           window.location.hostname === '127.0.0.1'
        const whatsappBaseURL = isLocalhost 
          ? 'http://localhost:3002' 
          : `https://${rawTenantId}.105pos.pro/api/whatsapp`
        
        await axios.post(`${whatsappBaseURL}/send`, {
          phone: phone,
          message: welcomeMessage
        }, {
          headers: {
            'X-Tenant-Id': tenantId
          }
        })
        
        console.log('✅ WhatsApp de bienvenida enviado correctamente')
      } catch (whatsappError) {
        console.warn('⚠️ No se pudo enviar el WhatsApp de bienvenida:', whatsappError.message)
      }
    }

    // Mostrar notificación de que se enviaron los mensajes
    if (customerData.email || customerData.phone) {
      showSuccess('🎉 Mensajes de bienvenida enviados al cliente')
    }

  } catch (error) {
    console.error('❌ Error al enviar mensajes de bienvenida:', error)
    // No mostrar error al usuario, ya que el crédito sí se creó correctamente
  }
}

// 🔔 Funciones de configuración de recordatorios
const loadReminderSettings = async () => {
  try {
    const response = await axiosInstance.get('/credit-reminder-settings')
    if (response.data.success && response.data.data) {
      reminderSettings.value = {
        frequency: response.data.data.frequency || 'manual',
        sendHour: response.data.data.send_hour || '9',
        minDaysOverdue: response.data.data.min_days_overdue || 1
      }
    }
  } catch (error) {
    // Si no existe configuración, usar valores por defecto
    console.warn('No se encontró configuración de recordatorios, usando valores por defecto')
  }
}

const saveReminderSettings = async () => {
  savingSettings.value = true
  try {
    const response = await axiosInstance.post('/credit-reminder-settings', {
      frequency: reminderSettings.value.frequency,
      send_hour: reminderSettings.value.sendHour,
      min_days_overdue: reminderSettings.value.minDaysOverdue
    })
    
    if (response.data.success) {
      showSuccess('Configuración guardada exitosamente')
      showReminderSettingsModal.value = false
    } else {
      showError(response.data.message || 'Error al guardar la configuración')
    }
  } catch (error) {
    console.error('Error saving reminder settings:', error)
    showError(error.response?.data?.message || 'Error al guardar la configuración')
  } finally {
    savingSettings.value = false
  }
}

const checkWhatsAppStatus = async () => {
  try {
    const { whatsappService } = await import('../services/whatsappService.js')
    const result = await whatsappService.getStatus()
    whatsappConnected.value = result?.status?.connected || false
  } catch (error) {
    whatsappConnected.value = false
  }
}

// ========== CONTEXTO DE IA - CONCIENCIA DE PANTALLA ==========
const instruccionesCrediTienda = {
  modulo: 'Módulo CrediTienda: Sistema de gestión de créditos a clientes. Vista Master-Detail con lista de clientes con crédito activo a la izquierda y detalles del crédito a la derecha.',
  panelIzquierdo: 'Panel izquierdo: Lista de clientes con crédito activo, mostrando nombre, documento, deuda actual y días de mora.',
  panelDerecho: 'Panel derecho: Portal de Créditos con información del cliente seleccionado, historial de compras a crédito, historial de abonos y opción de registrar pagos.',
  kpis: 'KPIs disponibles: Total por Cobrar, Clientes Activos con crédito, Recaudado Hoy, Mora Promedio en días.',
  acciones: 'Acciones disponibles: Nuevo Crédito (habilitar crédito a cliente), Registrar Abono, Ver historial, Enviar recordatorio de pago.',
  camposNuevoCredito: 'Para habilitar crédito: El usuario proporciona la CÉDULA/CC y el sistema busca automáticamente si el cliente existe. Si existe, auto-llena todos los datos. Solo falta definir el cupo de crédito.',
  flujoCreacion: 'Flujo para nuevo crédito: 1) Abrir modal con crearNuevoCredito, 2) Pedir la cédula/CC al usuario, 3) Usar buscarClientePorDocumento para auto-llenar, 4) Si no existe, pedir los datos, 5) Definir cupo de crédito, 6) Guardar con guardarCredito.',
  flujoAbono: 'Para registrar un abono: 1) Seleccionar cliente, 2) Abrir modal de abono, 3) Ingresar monto y método de pago, 4) Confirmar.'
}

// Watcher para actualizar contexto de IA cuando cambian los datos
watch(
  [customers, selectedCustomer, loading, showCreateCreditModal, showPaymentModal, customerForm],
  () => {
    // Datos base de CrediTienda
    const crediTiendaData = {
      // KPIs principales
      totalPorCobrar: totalDebt.value,
      clientesConCredito: customersWithDebt.value,
      recaudadoHoy: todayPayments.value,
      moraPromedio: averageDaysOverdue.value,
      cargando: loading.value,
      
      // Estado de filtros y búsqueda
      filtroActivo: statusFilter.value || 'todos',
      terminoBusqueda: searchTerm.value || '',
      
      // Estado del modal de nuevo crédito
      modalNuevoCreditoAbierto: showCreateCreditModal.value,
      modalAbonoAbierto: showPaymentModal.value,
      
      // Si el modal está abierto, mostrar datos del formulario
      formularioCredito: showCreateCreditModal.value ? {
        documento: customerForm.value.document_number || '(vacío)',
        tipoDocumento: customerForm.value.document_type || 'CC',
        nombre: customerForm.value.name || '(vacío)',
        email: customerForm.value.email || '(vacío)',
        telefono: customerForm.value.phone || '(vacío)',
        direccion: customerForm.value.address || '(vacío)',
        ciudad: customerForm.value.city || '(vacío)',
        cupoCredito: customerForm.value.credit_limit || 0,
        clienteExistente: customerExists.value,
        buscandoDocumento: checkingDocument.value,
        mensajeEstado: customerExists.value 
          ? 'Cliente encontrado, datos auto-llenados. Solo falta definir el cupo de crédito.'
          : 'Ingresa la cédula para buscar el cliente o crear uno nuevo.'
      } : null,
      
      // Lista de clientes con crédito (primeros 15)
      listaClientesCredito: filteredCustomers.value.slice(0, 15).map(c => ({
        id: c.id,
        nombre: c.name,
        documento: `${c.document_type || 'CC'}: ${c.document_number}`,
        deudaActual: parseFloat(c.balance || c.current_debt || 0),
        cupoCredito: parseFloat(c.credit_limit || 0),
        diasMora: c.days_overdue || 0,
        estado: c.balance > 0 ? 'Con deuda' : 'Al día'
      })),
      
      instrucciones: instruccionesCrediTienda
    }
    
    // Si hay un cliente seleccionado, agregar sus detalles completos
    if (selectedCustomer.value) {
      const cliente = selectedCustomer.value
      crediTiendaData.clienteSeleccionado = {
        id: cliente.id,
        nombre: cliente.name,
        documento: `${cliente.document_type || 'CC'}: ${cliente.document_number}`,
        telefono: cliente.phone || 'Sin teléfono',
        email: cliente.email || 'Sin email',
        direccion: cliente.address || 'Sin dirección',
        
        // Información de crédito
        cupoCredito: parseFloat(cliente.credit_limit || 0),
        deudaActual: parseFloat(cliente.balance || cliente.current_debt || 0),
        disponible: parseFloat(cliente.credit_limit || 0) - parseFloat(cliente.balance || cliente.current_debt || 0),
        diasMora: cliente.days_overdue || 0,
        
        // Historial de compras a crédito
        comprasCredito: creditInvoices.value.slice(0, 10).map(inv => ({
          numero: inv.invoice_number || inv.id,
          fecha: inv.created_at,
          total: parseFloat(inv.total || 0),
          estado: inv.payment_status
        })),
        
        // Historial de abonos
        abonos: creditPayments.value.slice(0, 10).map(pay => ({
          fecha: pay.created_at,
          monto: parseFloat(pay.amount || 0),
          metodo: pay.method
        }))
      }
    }
    
    // Resumen rápido para respuestas comunes
    crediTiendaData.resumenRapido = {
      cuantoMeDeben: `El total por cobrar es de $${formatCurrency(totalDebt.value)}. Hay ${customersWithDebt.value} cliente(s) con crédito activo.`,
      recaudadoHoy: `Hoy se han recaudado $${formatCurrency(todayPayments.value)} en abonos.`,
      moraPromedio: `La mora promedio es de ${averageDaysOverdue.value} días.`,
      clienteSeleccionado: selectedCustomer.value 
        ? `Cliente: ${selectedCustomer.value.name}. Deuda: $${formatCurrency(selectedCustomer.value.balance || 0)}. Cupo disponible: $${formatCurrency((selectedCustomer.value.credit_limit || 0) - (selectedCustomer.value.balance || 0))}.`
        : 'No hay cliente seleccionado. Selecciona uno para ver sus detalles de crédito.',
      comoHabilitarCredito: 'Para habilitar crédito: dime la cédula del cliente. Si ya existe, auto-lleno sus datos. Solo necesitamos definir el cupo.',
      comoRegistrarAbono: 'Para registrar un abono: primero selecciona el cliente, luego indica el monto a abonar.'
    }

    uiContextStore.setScreenData(crediTiendaData)
    
    // Registrar acciones disponibles para la IA
    uiContextStore.registerAction('seleccionarClienteCredito', async ({ nombre }) => {
      const cliente = filteredCustomers.value.find(c => 
        c.name.toLowerCase().includes(nombre.toLowerCase())
      )
      if (cliente) {
        selectCustomer(cliente)
        return { success: true, message: `Cliente "${cliente.name}" seleccionado. Deuda: $${formatCurrency(cliente.balance || 0)}` }
      }
      return { success: false, message: `No se encontró cliente con nombre "${nombre}" en CrediTienda` }
    })
    
    uiContextStore.registerAction('buscarClienteCredito', ({ texto }) => {
      searchTerm.value = texto
      return { success: true, message: `Buscando clientes con crédito: "${texto}"` }
    })
    
    uiContextStore.registerAction('crearNuevoCredito', () => {
      openCreateCreditModal()
      return { success: true, message: 'Modal abierto. Proporciona la CÉDULA del cliente para buscar si ya existe o crear uno nuevo.' }
    })
    
    // Acción para llenar el documento y activar la búsqueda automática
    uiContextStore.registerAction('buscarClientePorDocumento', async ({ documento }) => {
      if (!showCreateCreditModal.value) {
        openCreateCreditModal()
        await new Promise(resolve => setTimeout(resolve, 300))
      }
      
      customerForm.value.document_number = documento
      
      // Ejecutar la búsqueda
      await checkDocumentExists()
      
      // Esperar un poco para que complete la búsqueda
      await new Promise(resolve => setTimeout(resolve, 700))
      
      if (customerExists.value) {
        return { 
          success: true, 
          message: `¡Cliente encontrado! ${customerForm.value.name}. Los datos se han auto-llenado. Solo falta definir el cupo de crédito.`,
          clienteEncontrado: {
            nombre: customerForm.value.name,
            telefono: customerForm.value.phone,
            email: customerForm.value.email
          }
        }
      } else {
        return { 
          success: true, 
          message: `Cliente no encontrado con documento ${documento}. Necesito los datos para crear uno nuevo: nombre completo, teléfono y email.` 
        }
      }
    })
    
    // Acción para llenar campos del formulario
    uiContextStore.registerAction('llenarCampoCredito', ({ campo, valor }) => {
      if (!showCreateCreditModal.value) {
        return { success: false, message: 'Primero abre el modal con crearNuevoCredito' }
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
        'cupo': 'credit_limit',
        'cupo_credito': 'credit_limit',
        'limite': 'credit_limit',
        'credit_limit': 'credit_limit'
      }
      
      const campoReal = camposValidos[campo.toLowerCase().trim()]
      
      if (!campoReal) {
        return { success: false, message: `Campo "${campo}" no reconocido. Campos válidos: nombre, documento, email, telefono, direccion, ciudad, cupo/limite` }
      }
      
      customerForm.value[campoReal] = campoReal === 'credit_limit' ? parseFloat(valor) || 0 : valor
      
      return { 
        success: true, 
        message: `Campo "${campo}" actualizado a "${valor}"`,
        formularioActual: {
          nombre: customerForm.value.name,
          documento: customerForm.value.document_number,
          cupo: customerForm.value.credit_limit
        }
      }
    })
    
    uiContextStore.registerAction('guardarCredito', async () => {
      if (!showCreateCreditModal.value) {
        return { success: false, message: 'No hay formulario de crédito abierto' }
      }
      
      // Validar campos obligatorios
      if (!customerForm.value.document_number?.trim()) {
        return { success: false, message: 'Falta el número de documento (cédula)' }
      }
      if (!customerForm.value.name?.trim()) {
        return { success: false, message: 'Falta el nombre del cliente' }
      }
      
      try {
        await saveCustomerCredit()
        return { success: true, message: `Crédito habilitado para ${customerForm.value.name}. Cupo: $${formatCurrency(customerForm.value.credit_limit)}` }
      } catch (error) {
        return { success: false, message: `Error al guardar: ${error.message}` }
      }
    })
    
    uiContextStore.registerAction('registrarAbono', async ({ monto, metodo }) => {
      if (!selectedCustomer.value) {
        return { success: false, message: 'Primero selecciona un cliente para registrar el abono' }
      }
      
      const montoNum = parseFloat(monto) || 0
      if (montoNum <= 0) {
        return { success: false, message: 'El monto debe ser mayor a 0' }
      }
      
      if (montoNum > (selectedCustomer.value.balance || 0)) {
        return { success: false, message: `El monto ($${formatCurrency(montoNum)}) no puede ser mayor a la deuda ($${formatCurrency(selectedCustomer.value.balance || 0)})` }
      }
      
      // Abrir modal de pago con los datos
      openPaymentModal(selectedCustomer.value)
      paymentForm.value.amount = montoNum
      paymentForm.value.payment_method = metodo || 'cash'
      
      return { 
        success: true, 
        message: `Modal de abono abierto para ${selectedCustomer.value.name}. Monto: $${formatCurrency(montoNum)}. Confirma para procesar el pago.` 
      }
    })
    
    uiContextStore.registerAction('confirmarAbono', async () => {
      if (!showPaymentModal.value || !paymentCustomer.value) {
        return { success: false, message: 'No hay un abono pendiente de confirmar' }
      }
      
      try {
        await submitPayment()
        return { success: true, message: 'Abono registrado exitosamente' }
      } catch (error) {
        return { success: false, message: `Error: ${error.message}` }
      }
    })
    
    uiContextStore.registerAction('cerrarModalCredito', () => {
      // Lista de modales que estaban abiertos antes de cerrar
      const modalesCerrados = []
      
      // Cerrar TODOS los modales posibles de CrediTienda
      if (showCreateCreditModal.value) {
        showCreateCreditModal.value = false
        modalesCerrados.push('crear crédito')
      }
      if (showPaymentModal.value) {
        closePaymentModal()
        modalesCerrados.push('registrar abono')
      }
      if (showDetailModal.value) {
        showDetailModal.value = false
        modalesCerrados.push('detalles')
      }
      if (showDeleteModal.value) {
        showDeleteModal.value = false
        modalesCerrados.push('confirmación eliminación')
      }
      if (showEditCustomerModal.value) {
        showEditCustomerModal.value = false
        modalesCerrados.push('editar cliente')
      }
      if (showReminderSettingsModal.value) {
        showReminderSettingsModal.value = false
        modalesCerrados.push('configuración recordatorios')
      }
      if (showWhatsAppModal.value) {
        showWhatsAppModal.value = false
        modalesCerrados.push('WhatsApp')
      }
      if (showPhotoPreviewModal.value) {
        showPhotoPreviewModal.value = false
        modalesCerrados.push('previsualización foto')
      }
      
      if (modalesCerrados.length > 0) {
        return { success: true, message: `Modal cerrado: ${modalesCerrados.join(', ')}` }
      }
      return { success: true, message: 'No había modales abiertos' }
    })
  },
  { immediate: true, deep: true }
)

// Initialization
onMounted(() => {
  loadCustomers()
  loadSystemSettings()
  loadReminderSettings()
  checkWhatsAppStatus()
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

/* Avatar styles - modo oscuro usa colores más brillantes */
@media (prefers-color-scheme: dark) {
  [style*="--avatar-dark-bg"] {
    background-color: var(--avatar-dark-bg) !important;
  }
}

html.dark [style*="--avatar-dark-bg"] {
  background-color: var(--avatar-dark-bg) !important;
}
</style>
