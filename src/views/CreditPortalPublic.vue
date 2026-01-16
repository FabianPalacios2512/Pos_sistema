<template>
  <!-- 🎯 PORTAL DE CRÉDITO - Diseño Mobile-First App Style -->
  <!-- Compensar zoom del sistema con transform -->
  <div class="credit-portal-wrapper min-h-screen bg-slate-100 dark:bg-[#0f0f12]">
    
    <!-- ==================== PANTALLA DE LOGIN ==================== -->
    <div v-if="!isAuthenticated" class="min-h-screen flex flex-col">
      
      <!-- Header con gradiente -->
      <div class="bg-gradient-to-br from-emerald-500 via-teal-500 to-emerald-600 px-6 pt-12 pb-20 text-center relative overflow-hidden">
        <!-- Decoración -->
        <div class="absolute top-0 left-0 w-full h-full opacity-10">
          <div class="absolute top-10 left-10 w-32 h-32 rounded-full bg-white"></div>
          <div class="absolute bottom-0 right-0 w-48 h-48 rounded-full bg-white translate-x-1/2 translate-y-1/2"></div>
        </div>
        
        <div class="relative z-10">
          <div class="w-16 h-16 rounded-2xl bg-white/20 backdrop-blur-sm mx-auto flex items-center justify-center mb-4 shadow-lg">
            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
            </svg>
          </div>
          <h1 class="text-2xl font-bold text-white mb-1">Mi Crédito</h1>
          <p class="text-emerald-100 text-sm">{{ businessName }}</p>
        </div>
      </div>
      
      <!-- Card de Login -->
      <div class="flex-1 -mt-10 px-4 pb-8">
        <div class="bg-white dark:bg-zinc-900 rounded-3xl shadow-2xl max-w-md mx-auto overflow-hidden border border-gray-100 dark:border-zinc-800">
          
          <form @submit.prevent="handleLogin" class="p-6 space-y-5">
            <h2 class="text-lg font-bold text-gray-900 dark:text-white text-center mb-2">Ingresa tus datos</h2>
            
            <!-- ID de Crédito -->
            <div>
              <label class="block text-sm font-medium text-gray-600 dark:text-zinc-400 mb-1.5">ID de Crédito</label>
              <input
                v-model="loginForm.creditId"
                type="text"
                placeholder="CRD-000001"
                class="w-full px-4 py-3.5 bg-gray-50 dark:bg-zinc-800 border border-gray-200 dark:border-zinc-700 rounded-xl text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-zinc-500 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent text-center text-lg font-mono uppercase tracking-wider"
                :disabled="loading"
              />
            </div>
            
            <!-- Apellido -->
            <div>
              <label class="block text-sm font-medium text-gray-600 dark:text-zinc-400 mb-1.5">Tu Apellido</label>
              <input
                v-model="loginForm.lastName"
                type="text"
                placeholder="Ej: García"
                class="w-full px-4 py-3.5 bg-gray-50 dark:bg-zinc-800 border border-gray-200 dark:border-zinc-700 rounded-xl text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-zinc-500 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent text-center"
                :disabled="loading"
              />
            </div>
            
            <!-- Error -->
            <div v-if="error" class="bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800 rounded-xl p-3">
              <p class="text-sm text-red-600 dark:text-red-400 text-center">{{ error }}</p>
            </div>
            
            <!-- Botón -->
            <button
              type="submit"
              :disabled="loading || !loginForm.creditId || !loginForm.lastName"
              class="w-full py-4 bg-gradient-to-r from-emerald-500 to-teal-600 text-white font-bold rounded-xl shadow-lg shadow-emerald-500/30 disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2 active:scale-[0.98] transition-transform"
            >
              <svg v-if="loading" class="animate-spin w-5 h-5" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
              </svg>
              <span>{{ loading ? 'Verificando...' : 'Ver mi crédito' }}</span>
            </button>
          </form>
          
          <div class="bg-gray-50 dark:bg-zinc-800/50 px-6 py-4 border-t border-gray-100 dark:border-zinc-800">
            <p class="text-xs text-center text-gray-500 dark:text-zinc-500">🔒 Tu información está protegida</p>
          </div>
        </div>
        
        <p class="text-center mt-6 text-sm text-gray-500 dark:text-zinc-500">
          ¿No tienes tu ID? Contacta a la tienda
        </p>
      </div>
    </div>

    <!-- ==================== DASHBOARD (AUTENTICADO) ==================== -->
    <div v-else class="pb-8">
      
      <!-- Header con estado y curva inferior -->
      <div :class="[
        'px-4 pt-6 pb-12 text-center relative',
        creditData.credit?.balance > 0 ? 'bg-gradient-to-br from-amber-500 via-orange-500 to-amber-600' : 'bg-gradient-to-br from-emerald-500 via-teal-500 to-emerald-600'
      ]">
        <!-- Curva decorativa inferior -->
        <div class="absolute bottom-0 left-0 right-0 h-6 overflow-hidden">
          <div :class="[
            'absolute -bottom-3 left-1/2 -translate-x-1/2 w-[120%] h-12 rounded-[50%]',
            'bg-slate-100 dark:bg-[#0f0f12]'
          ]"></div>
        </div>
        
        <!-- Botón salir -->
        <button @click="logout" class="absolute top-4 right-4 p-2 text-white/80 hover:text-white hover:bg-white/10 rounded-lg transition-colors">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
          </svg>
        </button>
        
        <!-- Nombre y ID -->
        <div class="w-14 h-14 rounded-full bg-white/20 backdrop-blur-sm mx-auto flex items-center justify-center mb-3 shadow-lg">
          <span class="text-xl font-bold text-white">{{ creditData.customer?.name?.charAt(0)?.toUpperCase() }}</span>
        </div>
        <h2 class="text-lg font-bold text-white mb-0.5">{{ creditData.customer?.name }}</h2>
        <p class="text-white/70 text-xs mb-3">{{ creditData.customer?.document }}</p>
        
        <!-- Saldo grande -->
        <div class="mt-2">
          <p class="text-white/70 text-xs uppercase tracking-wider mb-1">
            {{ creditData.credit?.balance > 0 ? 'Debes' : 'Estado' }}
          </p>
          <p class="text-4xl font-black text-white tracking-tight">
            {{ creditData.credit?.balance > 0 ? '$' + formatNumber(creditData.credit?.balance) : '✓ Al día' }}
          </p>
        </div>
      </div>
      
      <!-- Cards de métricas (superpuestas sobre la curva) -->
      <div class="px-4 -mt-2 relative z-10">
        <div class="grid grid-cols-2 gap-3">
          <!-- Cupo -->
          <div class="bg-white dark:bg-zinc-900 rounded-2xl p-4 shadow-lg border border-gray-100 dark:border-zinc-800">
            <div class="w-10 h-10 rounded-xl bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center mb-2">
              <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
              </svg>
            </div>
            <p class="text-[10px] font-semibold text-gray-500 dark:text-zinc-400 uppercase tracking-wider">Cupo</p>
            <p class="text-xl font-bold text-gray-900 dark:text-white">${{ formatNumber(creditData.credit?.limit || 0) }}</p>
          </div>
          
          <!-- Disponible -->
          <div class="bg-white dark:bg-zinc-900 rounded-2xl p-4 shadow-lg border border-gray-100 dark:border-zinc-800">
            <div :class="[
              'w-10 h-10 rounded-xl flex items-center justify-center mb-2',
              creditData.credit?.available > 0 ? 'bg-emerald-100 dark:bg-emerald-900/30' : 'bg-rose-100 dark:bg-rose-900/30'
            ]">
              <svg :class="['w-5 h-5', creditData.credit?.available > 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400']" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
              </svg>
            </div>
            <p class="text-[10px] font-semibold text-gray-500 dark:text-zinc-400 uppercase tracking-wider">Disponible</p>
            <p class="text-xl font-bold text-gray-900 dark:text-white">${{ formatNumber(creditData.credit?.available || 0) }}</p>
          </div>
        </div>
      </div>
      
      <!-- Desglose de deuda (si hay) -->
      <div v-if="creditData.credit?.balance > 0" class="px-4 mt-4">
        <div class="bg-amber-50 dark:bg-amber-900/20 rounded-2xl p-4 border border-amber-200 dark:border-amber-800/50">
          <h3 class="text-sm font-bold text-amber-800 dark:text-amber-300 mb-3 flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
            </svg>
            Desglose de tu deuda
          </h3>
          <div class="space-y-2">
            <div class="flex justify-between items-center">
              <span class="text-sm text-amber-700 dark:text-amber-400">Productos</span>
              <span class="text-sm font-semibold text-amber-900 dark:text-amber-200">${{ formatNumber(creditData.credit?.balance_breakdown?.products || 0) }}</span>
            </div>
            <div class="flex justify-between items-center">
              <span class="text-sm text-amber-700 dark:text-amber-400">Recargo financiero (10%)</span>
              <span class="text-sm font-semibold text-amber-900 dark:text-amber-200">+${{ formatNumber(creditData.credit?.balance_breakdown?.surcharge || 0) }}</span>
            </div>
            <div class="border-t border-amber-300 dark:border-amber-700 pt-2 mt-2">
              <div class="flex justify-between items-center">
                <span class="text-sm font-bold text-amber-800 dark:text-amber-300">Total a pagar</span>
                <span class="text-lg font-bold text-amber-900 dark:text-amber-100">${{ formatNumber(creditData.credit?.balance || 0) }}</span>
              </div>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Tabs de navegación -->
      <div class="px-4 mt-6">
        <div class="flex gap-2 bg-gray-100 dark:bg-zinc-800 rounded-xl p-1">
          <button 
            @click="activeTab = 'compras'"
            :class="[
              'flex-1 py-2.5 text-sm font-semibold rounded-lg transition-all',
              activeTab === 'compras' ? 'bg-white dark:bg-zinc-700 text-gray-900 dark:text-white shadow' : 'text-gray-500 dark:text-zinc-400'
            ]"
          >
            📦 Compras
          </button>
          <button 
            @click="activeTab = 'pagos'"
            :class="[
              'flex-1 py-2.5 text-sm font-semibold rounded-lg transition-all',
              activeTab === 'pagos' ? 'bg-white dark:bg-zinc-700 text-gray-900 dark:text-white shadow' : 'text-gray-500 dark:text-zinc-400'
            ]"
          >
            💵 Abonos
          </button>
        </div>
      </div>
      
      <!-- Tab Compras -->
      <div v-if="activeTab === 'compras'" class="px-4 mt-4 space-y-3">
        <!-- Solo mostrar facturas del crédito actual (pendientes) -->
        <div v-if="pendingInvoices.length > 0">
          <p class="text-xs font-semibold text-gray-500 dark:text-zinc-400 uppercase tracking-wider mb-2 px-1">
            Crédito Actual ({{ pendingInvoices.length }})
          </p>
          <div v-for="invoice in pendingInvoices" :key="invoice.number" 
               @click="openInvoiceModal(invoice)"
               class="bg-white dark:bg-zinc-900 rounded-xl p-4 shadow border border-gray-100 dark:border-zinc-800 cursor-pointer active:scale-[0.98] transition-transform">
            <div class="flex justify-between items-start mb-2">
              <div>
                <p class="font-semibold text-gray-900 dark:text-white text-sm">{{ invoice.number }}</p>
                <p class="text-xs text-gray-500 dark:text-zinc-500">{{ formatDate(invoice.date) }}</p>
              </div>
              <span :class="[
                'px-2 py-1 rounded-lg text-[10px] font-bold uppercase',
                invoice.status === 'paid' ? 'bg-emerald-100 dark:bg-emerald-900/50 text-emerald-700 dark:text-emerald-400' :
                invoice.status === 'overdue' ? 'bg-rose-100 dark:bg-rose-900/50 text-rose-700 dark:text-rose-400' :
                'bg-blue-100 dark:bg-blue-900/50 text-blue-700 dark:text-blue-400'
              ]">
                {{ invoice.status === 'paid' ? 'Pagada' : invoice.status === 'overdue' ? 'Vencida' : 'Pendiente' }}
              </span>
            </div>
            <div class="flex justify-between items-center text-sm">
              <span class="text-gray-500 dark:text-zinc-400">Productos: ${{ formatNumber(invoice.subtotal) }}</span>
              <span class="text-amber-600 dark:text-amber-400 text-xs">+${{ formatNumber(invoice.surcharge) }}</span>
            </div>
            <div class="flex justify-between items-center mt-2 pt-2 border-t border-gray-100 dark:border-zinc-800">
              <span class="text-gray-600 dark:text-zinc-300 font-medium">Total</span>
              <span class="font-bold text-gray-900 dark:text-white">${{ formatNumber(invoice.total) }}</span>
            </div>
            <!-- Indicador de clic -->
            <p class="text-[10px] text-center text-gray-400 dark:text-zinc-600 mt-2">Toca para ver productos</p>
          </div>
        </div>
        
        <!-- Historial (facturas pagadas) -->
        <div v-if="paidInvoices.length > 0" class="mt-6">
          <button @click="showHistory = !showHistory" class="w-full flex items-center justify-between px-1 py-2 text-xs font-semibold text-gray-500 dark:text-zinc-400 uppercase tracking-wider">
            <span>📜 Historial ({{ paidInvoices.length }} pagadas)</span>
            <svg :class="['w-4 h-4 transition-transform', showHistory ? 'rotate-180' : '']" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
          </button>
          
          <div v-if="showHistory" class="space-y-2 mt-2">
            <div v-for="invoice in paidInvoices" :key="invoice.number" 
                 @click="openInvoiceModal(invoice)"
                 class="bg-gray-50 dark:bg-zinc-800/50 rounded-xl p-3 border border-gray-100 dark:border-zinc-800/50 cursor-pointer active:scale-[0.98] transition-transform">
              <div class="flex justify-between items-center">
                <div>
                  <p class="font-medium text-gray-700 dark:text-zinc-300 text-sm">{{ invoice.number }}</p>
                  <p class="text-xs text-gray-400 dark:text-zinc-500">{{ formatDate(invoice.date) }}</p>
                </div>
                <div class="text-right">
                  <p class="font-semibold text-gray-900 dark:text-white text-sm">${{ formatNumber(invoice.total) }}</p>
                  <span class="text-[10px] text-emerald-600 dark:text-emerald-400 font-medium">✓ Pagada</span>
                </div>
              </div>
            </div>
          </div>
        </div>
        
        <!-- Vacío -->
        <div v-if="!creditData.invoices?.length" class="text-center py-12">
          <div class="w-16 h-16 rounded-full bg-gray-100 dark:bg-zinc-800 mx-auto flex items-center justify-center mb-3">
            <svg class="w-8 h-8 text-gray-400 dark:text-zinc-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
          </div>
          <p class="text-gray-500 dark:text-zinc-500 text-sm">No hay compras registradas</p>
        </div>
      </div>
      
      <!-- Tab Pagos -->
      <div v-if="activeTab === 'pagos'" class="px-4 mt-4 space-y-3">
        <div v-if="creditData.payments?.length > 0">
          <div v-for="(payment, index) in creditData.payments" :key="index" 
               class="bg-white dark:bg-zinc-900 rounded-xl p-4 shadow border border-gray-100 dark:border-zinc-800">
            <div class="flex justify-between items-center">
              <div>
                <p class="text-xs text-gray-500 dark:text-zinc-500">{{ formatDate(payment.date) }}</p>
                <p class="text-sm text-gray-600 dark:text-zinc-400 mt-1 capitalize">{{ payment.method || 'Efectivo' }}</p>
              </div>
              <div class="text-right">
                <p class="text-xl font-bold text-emerald-600 dark:text-emerald-400">${{ formatNumber(payment.amount) }}</p>
                <p v-if="payment.reference" class="text-xs text-gray-400 dark:text-zinc-500">Ref: {{ payment.reference }}</p>
              </div>
            </div>
          </div>
        </div>
        
        <!-- Vacío -->
        <div v-else class="text-center py-12">
          <div class="w-16 h-16 rounded-full bg-gray-100 dark:bg-zinc-800 mx-auto flex items-center justify-center mb-3">
            <svg class="w-8 h-8 text-gray-400 dark:text-zinc-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
            </svg>
          </div>
          <p class="text-gray-500 dark:text-zinc-500 text-sm">No hay abonos registrados</p>
        </div>
      </div>
      
      <!-- Info footer -->
      <div class="px-4 mt-6">
        <div class="bg-blue-50 dark:bg-blue-900/20 rounded-xl p-4 border border-blue-100 dark:border-blue-800/50">
          <p class="text-xs text-blue-700 dark:text-blue-400 leading-relaxed">
            💡 Las compras a crédito incluyen un recargo del 10%. Paga a tiempo para mantener tu crédito activo.
          </p>
        </div>
      </div>
      
      <!-- ID y última actualización -->
      <div class="px-4 mt-4 text-center">
        <p class="text-xs text-gray-400 dark:text-zinc-600">
          ID: {{ creditData.customer?.credit_id }} • {{ businessName }}
        </p>
      </div>
      
    </div>
    
    <!-- ==================== MODAL DETALLE FACTURA ==================== -->
    <div v-if="showInvoiceModal" class="fixed inset-0 z-50 flex items-end justify-center sm:items-center">
      <!-- Backdrop -->
      <div @click="closeInvoiceModal" class="absolute inset-0 bg-black/50"></div>
      
      <!-- Modal Content -->
      <div class="relative bg-white dark:bg-zinc-900 w-full sm:max-w-md sm:rounded-2xl rounded-t-3xl max-h-[85vh] overflow-hidden shadow-2xl animate-slide-up">
        <!-- Header -->
        <div class="sticky top-0 bg-gradient-to-r from-blue-500 to-blue-600 px-5 py-4 flex items-center justify-between">
          <div>
            <h3 class="text-lg font-bold text-white">{{ invoiceDetail?.number }}</h3>
            <p class="text-blue-100 text-xs">{{ invoiceDetail?.date }}</p>
          </div>
          <button @click="closeInvoiceModal" class="p-2 text-white/80 hover:text-white hover:bg-white/10 rounded-lg">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
          </button>
        </div>
        
        <!-- Loading -->
        <div v-if="loadingInvoice" class="p-8 text-center">
          <svg class="animate-spin w-8 h-8 mx-auto text-blue-500" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
          </svg>
          <p class="text-gray-500 dark:text-zinc-400 mt-2 text-sm">Cargando...</p>
        </div>
        
        <!-- Contenido -->
        <div v-else-if="invoiceDetail" class="overflow-y-auto max-h-[calc(85vh-80px)]">
          <!-- Productos -->
          <div class="p-4">
            <h4 class="text-xs font-semibold text-gray-500 dark:text-zinc-400 uppercase tracking-wider mb-3">Productos</h4>
            
            <!-- Sin productos -->
            <div v-if="!invoiceDetail.items?.length" class="text-center py-6">
              <svg class="w-10 h-10 mx-auto text-gray-300 dark:text-zinc-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
              </svg>
              <p class="text-gray-400 dark:text-zinc-500 text-sm mt-2">Detalle no disponible</p>
            </div>
            
            <!-- Lista de productos -->
            <div v-else class="space-y-3">
              <div v-for="(item, idx) in invoiceDetail.items" :key="idx" 
                   class="flex justify-between items-start bg-gray-50 dark:bg-zinc-800 rounded-lg p-3">
                <div class="flex-1">
                  <p class="font-medium text-gray-900 dark:text-white text-sm">{{ item.product_name }}</p>
                  <p class="text-xs text-gray-500 dark:text-zinc-500">{{ item.quantity }} x ${{ formatNumber(item.unit_price) }}</p>
                </div>
                <p class="font-semibold text-gray-900 dark:text-white text-sm">${{ formatNumber(item.total) }}</p>
              </div>
            </div>
          </div>
          
          <!-- Totales -->
          <div class="border-t border-gray-100 dark:border-zinc-800 p-4 space-y-2">
            <div class="flex justify-between text-sm">
              <span class="text-gray-500 dark:text-zinc-400">Subtotal</span>
              <span class="text-gray-700 dark:text-zinc-300">${{ formatNumber(invoiceDetail.subtotal) }}</span>
            </div>
            <div class="flex justify-between text-sm">
              <span class="text-amber-600 dark:text-amber-400">Recargo (10%)</span>
              <span class="text-amber-600 dark:text-amber-400">+${{ formatNumber(invoiceDetail.surcharge) }}</span>
            </div>
            <div class="flex justify-between text-base font-bold pt-2 border-t border-gray-200 dark:border-zinc-700">
              <span class="text-gray-900 dark:text-white">Total</span>
              <span class="text-gray-900 dark:text-white">${{ formatNumber(invoiceDetail.total) }}</span>
            </div>
          </div>
          
          <!-- Estado -->
          <div class="p-4 border-t border-gray-100 dark:border-zinc-800">
            <div :class="[
              'text-center py-3 rounded-xl font-semibold text-sm',
              invoiceDetail.status === 'paid' ? 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400' :
              invoiceDetail.status === 'overdue' ? 'bg-rose-100 dark:bg-rose-900/30 text-rose-700 dark:text-rose-400' :
              'bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400'
            ]">
              {{ invoiceDetail.status === 'paid' ? '✓ Pagada' : invoiceDetail.status === 'overdue' ? '⚠ Vencida' : '⏳ Pendiente de pago' }}
            </div>
          </div>
        </div>
      </div>
    </div>
    
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import axios from 'axios'

// Estado
const isAuthenticated = ref(false)
const loading = ref(false)
const error = ref('')
const businessName = ref('Mi Negocio')
const creditData = ref({})
const activeTab = ref('compras')
const showHistory = ref(false)

// Modal de factura
const showInvoiceModal = ref(false)
const loadingInvoice = ref(false)
const invoiceDetail = ref(null)
const currentToken = ref('')

// Guardar zoom original para restaurar
let originalZoom = ''

const loginForm = ref({
  creditId: '',
  lastName: ''
})

// Computed: Facturas pendientes vs pagadas
const pendingInvoices = computed(() => {
  return creditData.value.invoices?.filter(inv => inv.status !== 'paid') || []
})

const paidInvoices = computed(() => {
  return creditData.value.invoices?.filter(inv => inv.status === 'paid') || []
})

// Obtener parámetros de URL
onMounted(async () => {
  // 🎯 Resetear zoom del sistema para esta vista pública
  const html = document.documentElement
  originalZoom = html.style.zoom || ''
  html.style.zoom = '1'
  
  const urlParams = new URLSearchParams(window.location.search)
  const token = urlParams.get('token')
  
  // Obtener nombre del negocio desde el subdominio
  const hostname = window.location.hostname
  const subdomain = hostname.split('.')[0]
  if (subdomain && subdomain !== 'localhost' && subdomain !== 'www') {
    // Capitalizar primera letra del subdominio como nombre del negocio
    businessName.value = subdomain.charAt(0).toUpperCase() + subdomain.slice(1)
  }
  
  // Si hay token en URL, acceder automáticamente
  if (token) {
    await accessByToken(token)
  }
})

// Acceso por token
const accessByToken = async (token) => {
  loading.value = true
  error.value = ''
  
  try {
    const response = await axios.post('/api/credit-portal/access-by-token', { token })
    
    if (response.data.success) {
      creditData.value = response.data.data
      currentToken.value = token // Guardar token para usar en modal
      isAuthenticated.value = true
    } else {
      error.value = response.data.message || 'Error al acceder'
    }
  } catch (e) {
    error.value = e.response?.data?.message || 'Enlace inválido'
  } finally {
    loading.value = false
  }
}

// Acceso por credenciales
const handleLogin = async () => {
  loading.value = true
  error.value = ''
  
  try {
    const response = await axios.post('/api/credit-portal/access-by-credentials', {
      credit_id: loginForm.value.creditId.toUpperCase(),
      last_name: loginForm.value.lastName
    })
    
    if (response.data.success) {
      creditData.value = response.data.data
      isAuthenticated.value = true
    } else {
      error.value = response.data.message || 'Datos incorrectos'
    }
  } catch (e) {
    error.value = e.response?.data?.message || 'Error de conexión'
  } finally {
    loading.value = false
  }
}

// Logout
const logout = () => {
  isAuthenticated.value = false
  creditData.value = {}
  loginForm.value = { creditId: '', lastName: '' }
  currentToken.value = ''
  // Limpiar URL
  window.history.replaceState({}, '', window.location.pathname)
}

// Modal de factura - Abrir
const openInvoiceModal = async (invoice) => {
  showInvoiceModal.value = true
  loadingInvoice.value = true
  invoiceDetail.value = null
  
  try {
    const response = await axios.post('/api/credit-portal/invoice-detail', {
      invoice_id: invoice.id,
      token: currentToken.value || null,
      credit_id: creditData.value.customer?.credit_id || null
    })
    
    if (response.data.success) {
      invoiceDetail.value = response.data.data
    } else {
      // Si falla, al menos mostrar los datos básicos
      invoiceDetail.value = {
        ...invoice,
        items: [],
        subtotal: invoice.subtotal || invoice.total,
        recargo: 0,
        total: invoice.total
      }
    }
  } catch (e) {
    // Mostrar datos básicos en caso de error
    invoiceDetail.value = {
      ...invoice,
      items: [],
      subtotal: invoice.subtotal || invoice.total,
      recargo: 0,
      total: invoice.total
    }
  } finally {
    loadingInvoice.value = false
  }
}

// Modal de factura - Cerrar
const closeInvoiceModal = () => {
  showInvoiceModal.value = false
  invoiceDetail.value = null
}

// Formateo
const formatNumber = (num) => {
  return new Intl.NumberFormat('es-CO').format(num || 0)
}

const formatDate = (date) => {
  if (!date) return '-'
  return new Date(date).toLocaleDateString('es-CO', {
    day: 'numeric',
    month: 'short',
    year: 'numeric'
  })
}

// Restaurar zoom al salir de la vista
onUnmounted(() => {
  const html = document.documentElement
  if (originalZoom) {
    html.style.zoom = originalZoom
  }
})
</script>

<style scoped>
/* Animación suave */
@keyframes fadeIn {
  from { opacity: 0; transform: translateY(10px); }
  to { opacity: 1; transform: translateY(0); }
}

.animate-fade-in {
  animation: fadeIn 0.3s ease-out;
}

/* Animación slide-up para modal */
@keyframes slideUp {
  from { transform: translateY(100%); opacity: 0; }
  to { transform: translateY(0); opacity: 1; }
}

.animate-slide-up {
  animation: slideUp 0.3s ease-out;
}
</style>
