<template>
  <div class="min-h-screen font-sans bg-gradient-to-br from-gray-50 via-white to-slate-100 dark:bg-gradient-to-b dark:from-[#141417] dark:via-slate-900 dark:to-[#0a0a0c] transition-colors duration-300 px-8">
    <div class="p-4 lg:p-6 space-y-6 pb-8 animate-fade-in">
      <!-- Header -->
      <div class="flex items-center justify-between pb-4">
        <div>
          <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Cuentas por Cobrar</h1>
          <p class="text-sm text-gray-600 dark:text-zinc-400 mt-1">Gestión de créditos y abonos de clientes</p>
        </div>
        
        <div class="relative">
          <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-gray-400 dark:text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
          </svg>
          <input 
            v-model="searchTerm"
            type="text" 
            placeholder="Buscar cliente..."
            class="pl-10 pr-4 py-3 text-sm rounded-xl border-2 border-gray-200 dark:border-zinc-700 bg-gray-50 dark:bg-zinc-800 text-gray-900 dark:text-zinc-200 placeholder-gray-400 dark:placeholder-zinc-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent transition-all duration-200"
          />
        </div>
      </div>

      <!-- Stats Cards con Glassmorphism -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white dark:bg-zinc-900/80 backdrop-blur-sm rounded-xl px-4 py-3 border border-gray-300 dark:border dark:border-white/5 hover:border-gray-400 dark:hover:border-white/10 transition-all duration-200 shadow-md hover:shadow-lg dark:shadow-lg dark:shadow-black/50">
          <div class="flex items-center gap-3">
            <div class="w-11 h-11 bg-red-50 dark:bg-red-950 rounded-lg flex items-center justify-center flex-shrink-0">
              <svg class="w-5 h-5 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
              </svg>
            </div>
            <div class="flex-1 min-w-0">
              <p class="text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wide">Total por Cobrar</p>
              <p class="text-2xl font-bold text-gray-900 dark:text-white mt-0.5">${{ formatCurrency(totalDebt) }}</p>
              <p class="text-xs text-gray-600 dark:text-zinc-400 mt-1">{{ customersWithDebt }} clientes</p>
            </div>
          </div>
        </div>

        <div class="bg-white dark:bg-zinc-900/80 backdrop-blur-sm rounded-xl px-4 py-3 border border-gray-300 dark:border dark:border-white/5 hover:border-gray-400 dark:hover:border-white/10 transition-all duration-200 shadow-md hover:shadow-lg dark:shadow-lg dark:shadow-black/50">
          <div class="flex items-center gap-3">
            <div class="w-11 h-11 bg-gray-100 dark:bg-zinc-800/50 border border-gray-200 dark:border-white/5 rounded-lg flex items-center justify-center flex-shrink-0">
              <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
              </svg>
            </div>
            <div class="flex-1 min-w-0">
              <p class="text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wide">Clientes con Crédito</p>
              <p class="text-2xl font-bold text-gray-900 dark:text-white mt-0.5">{{ customersWithDebt }}</p>
              <p class="text-xs text-gray-600 dark:text-zinc-400 mt-1">{{ filteredCustomers.length }} total</p>
            </div>
          </div>
        </div>

        <div class="bg-white dark:bg-zinc-900/80 backdrop-blur-sm rounded-xl px-4 py-3 border border-gray-300 dark:border dark:border-white/5 hover:border-gray-400 dark:hover:border-white/10 transition-all duration-200 shadow-md hover:shadow-lg dark:shadow-lg dark:shadow-black/50">
          <div class="flex items-center gap-3">
            <div class="w-11 h-11 bg-gray-100 dark:bg-zinc-800/50 border border-gray-200 dark:border-white/5 rounded-lg flex items-center justify-center flex-shrink-0">
              <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
              </svg>
            </div>
            <div class="flex-1 min-w-0">
              <p class="text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wide">Recaudado Hoy</p>
              <p class="text-2xl font-bold text-gray-900 dark:text-white mt-0.5">${{ formatCurrency(todayPayments) }}</p>
              <p class="text-xs text-gray-600 dark:text-zinc-400 mt-1">Abonos registrados</p>
            </div>
          </div>
        </div>

        <div class="bg-white dark:bg-zinc-900/80 backdrop-blur-sm rounded-xl px-4 py-3 border border-gray-300 dark:border dark:border-white/5 hover:border-gray-400 dark:hover:border-white/10 transition-all duration-200 shadow-md hover:shadow-lg dark:shadow-lg dark:shadow-black/50">
          <div class="flex items-center gap-3">
            <div class="w-11 h-11 bg-gray-100 dark:bg-zinc-800/50 border border-gray-200 dark:border-white/5 rounded-lg flex items-center justify-center flex-shrink-0">
              <svg class="w-5 h-5 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
              </svg>
            </div>
            <div class="flex-1 min-w-0">
              <p class="text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wide">Mora Promedio</p>
              <p class="text-2xl font-bold text-gray-900 dark:text-white mt-0.5">{{ averageDaysOverdue }}</p>
              <p class="text-xs text-gray-600 dark:text-zinc-400 mt-1">Días transcurridos</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Customers List -->
      <div class="bg-white dark:bg-zinc-900 rounded-2xl shadow-xl dark:shadow-black/50 border border-gray-300 dark:border-zinc-800 overflow-hidden">
        <div class="bg-gray-50 dark:bg-zinc-900 border-b border-gray-200 dark:border-zinc-800 px-5 py-4 flex items-center justify-between">
          <div>
            <h2 class="text-base font-bold text-gray-900 dark:text-white">Clientes con Crédito</h2>
            <p class="text-xs text-gray-600 dark:text-zinc-400 mt-0.5">{{ filteredCustomers.length }} clientes encontrados</p>
          </div>
        </div>
        <div class="overflow-x-auto">
          <table class="w-full">
            <thead class="bg-gray-50 dark:bg-zinc-900 border-b border-gray-200 dark:border-zinc-800">
              <tr>
                <th class="px-3 py-2 text-left text-xs font-bold text-gray-700 dark:text-zinc-300 uppercase tracking-wide">Cliente</th>
                <th class="px-3 py-2 text-left text-xs font-bold text-gray-700 dark:text-zinc-300 uppercase tracking-wide">Cupo</th>
                <th class="px-3 py-2 text-left text-xs font-bold text-gray-700 dark:text-zinc-300 uppercase tracking-wide">Deuda Actual</th>
                <th class="px-3 py-2 text-left text-xs font-bold text-gray-700 dark:text-zinc-300 uppercase tracking-wide">Disponible</th>
                <th class="px-3 py-2 text-left text-xs font-bold text-gray-700 dark:text-zinc-300 uppercase tracking-wide">Estado</th>
                <th class="px-3 py-2 text-right text-xs font-bold text-gray-700 dark:text-zinc-300 uppercase tracking-wide">Acciones</th>
              </tr>
            </thead>
            <tbody class="bg-white dark:bg-zinc-900 divide-y divide-gray-200 dark:divide-zinc-800">
              <tr v-if="loading" class="animate-pulse">
                <td colspan="6" class="px-3 py-8 text-center text-gray-500 dark:text-zinc-400">
                  <div class="flex items-center justify-center space-x-2">
                    <div class="w-5 h-5 border-2 border-blue-600 border-t-transparent rounded-full animate-spin"></div>
                    <span>Cargando clientes...</span>
                  </div>
                </td>
              </tr>
              <tr v-else-if="filteredCustomers.length === 0">
                <td colspan="6" class="px-3 py-8 text-center text-gray-500 dark:text-zinc-400">
                  <svg class="w-16 h-16 mx-auto mb-4 text-gray-300 dark:text-zinc-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                  </svg>
                  <p class="text-lg font-medium mb-2">No hay clientes con crédito activo</p>
                  <p class="text-sm">Los clientes aparecerán aquí cuando tengan crédito habilitado</p>
                </td>
              </tr>
              <tr v-else v-for="customer in filteredCustomers" :key="customer.id" class="hover:bg-slate-50 dark:hover:bg-zinc-800/50 hover:shadow-sm transition-all duration-200 border-b border-gray-200 dark:border-zinc-800">
                <td class="px-3 py-3">
                  <div class="flex items-center">
                    <div class="w-10 h-10 bg-blue-100 dark:bg-blue-950 rounded-lg flex items-center justify-center mr-3">
                      <span class="text-blue-600 dark:text-blue-400 font-semibold">{{ customer.name.charAt(0).toUpperCase() }}</span>
                    </div>
                    <div>
                      <p class="font-semibold text-gray-900 dark:text-white">{{ customer.name }}</p>
                      <p class="text-sm text-gray-600 dark:text-zinc-400">{{ customer.document_type }}: {{ customer.document_number }}</p>
                    </div>
                  </div>
                </td>
                <td class="px-3 py-3">
                  <p class="font-semibold text-gray-900 dark:text-white">${{ formatCurrency(customer.credit_limit || 0) }}</p>
                </td>
                <td class="px-3 py-3">
                  <p class="font-semibold" :class="customer.current_debt > 0 ? 'text-red-600 dark:text-red-400' : 'text-gray-900 dark:text-white'">
                    ${{ formatCurrency(customer.current_debt || 0) }}
                  </p>
                </td>
                <td class="px-3 py-3">
                  <p class="font-semibold text-emerald-600 dark:text-emerald-400">
                    ${{ formatCurrency((customer.credit_limit || 0) - (customer.current_debt || 0)) }}
                  </p>
                </td>
                <td class="px-3 py-3">
                  <span 
                    :class="getStatusColor(customer)"
                    class="px-2 py-0.5 rounded-full text-xs font-semibold border"
                  >
                    {{ getStatusText(customer) }}
                  </span>
                </td>
                <td class="px-3 py-3 text-right">
                  <div class="flex items-center justify-end space-x-2">
                    <button 
                      @click="sendReminder(customer)"
                      :disabled="!customer.current_debt || customer.current_debt <= 0 || sendingReminder"
                      class="p-2 text-slate-400 dark:text-zinc-500 hover:text-amber-600 dark:hover:text-amber-400 hover:bg-amber-50 dark:hover:bg-amber-900/20 disabled:text-gray-300 dark:disabled:text-zinc-700 disabled:cursor-not-allowed rounded-xl border border-transparent hover:border-amber-100 dark:hover:border-amber-900/30 transition-all duration-200"
                      title="Enviar Recordatorio"
                    >
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                      </svg>
                    </button>
                    <button 
                      @click="openPaymentModal(customer)"
                      :disabled="!customer.current_debt || customer.current_debt <= 0"
                      class="px-3 py-1.5 bg-emerald-600 dark:bg-emerald-700 hover:bg-emerald-700 dark:hover:bg-emerald-600 disabled:bg-gray-300 dark:disabled:bg-zinc-700 disabled:cursor-not-allowed text-white rounded-lg text-xs font-bold transition-all duration-200"
                    >
                      Abono
                    </button>
                    <button 
                      @click="viewCustomerDetail(customer)"
                      class="px-3 py-1.5 bg-blue-600 dark:bg-blue-700 hover:bg-blue-700 dark:hover:bg-blue-600 text-white rounded-lg text-xs font-bold transition-all duration-200"
                    >
                      Detalle
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Detail Modal -->
    <div v-if="showDetailModal" class="fixed inset-0 bg-black/60 dark:bg-black/80 flex items-center justify-center z-50 p-4" @click.self="showDetailModal = false">
      <div class="bg-white dark:bg-zinc-900 rounded-2xl shadow-xl dark:shadow-black/50 border border-gray-300 dark:border-zinc-800 max-w-6xl w-full max-h-[90vh] overflow-auto">
        <!-- Modal Header -->
        <div class="bg-white dark:bg-zinc-900 border-b border-gray-200 dark:border-zinc-800 px-4 py-3 flex items-center justify-between sticky top-0 z-10">
          <div class="flex items-center space-x-3">
            <div class="w-10 h-10 bg-blue-100 dark:bg-blue-950 rounded-lg flex items-center justify-center">
              <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
              </svg>
            </div>
            <div>
              <h3 class="text-base font-bold text-gray-900 dark:text-white">Detalle de Crédito</h3>
              <p class="text-xs text-gray-600 dark:text-zinc-400">{{ selectedCustomer?.name }}</p>
            </div>
          </div>
          <button @click="showDetailModal = false" class="text-gray-400 dark:text-zinc-500 hover:text-gray-600 dark:hover:text-zinc-300">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
          </button>
        </div>

        <!-- Modal Content -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 p-4">
          <!-- Main Content (2/3) -->
          <div class="lg:col-span-2 space-y-4">
            <!-- Credit Invoices -->
            <div class="bg-white dark:bg-zinc-900 border border-gray-300 dark:border-zinc-800 rounded-lg overflow-hidden">
              <div class="bg-gray-50 dark:bg-zinc-900 border-b border-gray-200 dark:border-zinc-800 px-3 py-2">
                <h4 class="text-sm font-bold text-gray-900 dark:text-white">Facturas a Crédito</h4>
              </div>
              <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-zinc-800">
                  <thead class="bg-gray-50 dark:bg-zinc-900">
                    <tr>
                      <th class="px-3 py-2 text-left text-xs font-bold text-gray-700 dark:text-zinc-300 uppercase">Factura</th>
                      <th class="px-3 py-2 text-left text-xs font-bold text-gray-700 dark:text-zinc-300 uppercase">Fecha</th>
                      <th class="px-3 py-2 text-left text-xs font-bold text-gray-700 dark:text-zinc-300 uppercase">Días</th>
                      <th class="px-3 py-2 text-left text-xs font-bold text-gray-700 dark:text-zinc-300 uppercase">Monto</th>
                      <th class="px-3 py-2 text-left text-xs font-bold text-gray-700 dark:text-zinc-300 uppercase">Recargo</th>
                      <th class="px-3 py-2 text-left text-xs font-bold text-gray-700 dark:text-zinc-300 uppercase">Total</th>
                    </tr>
                  </thead>
                  <tbody v-if="loadingDetail" class="bg-white dark:bg-zinc-900">
                    <tr>
                      <td colspan="6" class="px-3 py-8 text-center">
                        <div class="flex items-center justify-center space-x-2">
                          <div class="w-5 h-5 border-2 border-blue-600 dark:border-blue-400 border-t-transparent rounded-full animate-spin"></div>
                          <span class="text-sm text-gray-500 dark:text-zinc-400">Cargando facturas...</span>
                        </div>
                      </td>
                    </tr>
                  </tbody>
                  <tbody v-else-if="creditInvoices.length === 0" class="bg-white dark:bg-zinc-900">
                    <tr>
                      <td colspan="6" class="px-3 py-8 text-center text-gray-500 dark:text-zinc-400">
                        <p class="text-sm">No hay facturas a crédito</p>
                      </td>
                    </tr>
                  </tbody>
                  <tbody v-else class="bg-white dark:bg-zinc-900 divide-y divide-gray-200 dark:divide-zinc-800">
                    <tr v-for="invoice in creditInvoices" :key="invoice.id" class="hover:bg-slate-50 dark:hover:bg-zinc-800/50 hover:shadow-sm transition-all duration-200">
                      <td class="px-3 py-3">
                        <p class="text-sm font-semibold text-gray-900 dark:text-white">#{{ invoice.invoice_number }}</p>
                      </td>
                      <td class="px-3 py-3">
                        <p class="text-sm text-gray-700 dark:text-zinc-300">{{ formatDate(invoice.created_at) }}</p>
                      </td>
                      <td class="px-3 py-3">
                        <span :class="getDaysBadgeColor(calculateDaysSince(invoice.created_at))" class="px-2 py-0.5 rounded-full text-xs font-semibold">
                          {{ calculateDaysSince(invoice.created_at) }} días
                        </span>
                      </td>
                      <td class="px-3 py-3">
                        <p class="text-sm font-semibold text-gray-900 dark:text-white">${{ formatCurrency(invoice.total - (invoice.surcharge_amount || 0)) }}</p>
                      </td>
                      <td class="px-3 py-3">
                        <p class="text-sm font-semibold text-amber-600 dark:text-amber-400">${{ formatCurrency(invoice.surcharge_amount || 0) }}</p>
                      </td>
                      <td class="px-3 py-3">
                        <p class="text-sm font-bold text-gray-900 dark:text-white">${{ formatCurrency(invoice.total) }}</p>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>

            <!-- Payment History -->
            <div class="bg-white dark:bg-zinc-900 border border-gray-300 dark:border-zinc-800 rounded-lg">
              <div class="bg-gray-50 dark:bg-zinc-900 border-b border-gray-200 dark:border-zinc-800 px-3 py-2">
                <h4 class="text-sm font-bold text-gray-900 dark:text-white">Historial de Abonos</h4>
              </div>
              <div class="p-3">
                <div v-if="loadingDetail" class="text-center py-4">
                  <div class="flex items-center justify-center space-x-2">
                    <div class="w-5 h-5 border-2 border-blue-600 dark:border-blue-400 border-t-transparent rounded-full animate-spin"></div>
                    <span class="text-sm text-gray-500 dark:text-zinc-400">Cargando abonos...</span>
                  </div>
                </div>
                <div v-else-if="creditPayments.length === 0" class="text-center py-4">
                  <p class="text-sm text-gray-500 dark:text-zinc-400">No hay abonos registrados</p>
                </div>
                <div v-else class="space-y-2">
                  <div v-for="payment in creditPayments" :key="payment.id" class="flex items-center justify-between p-2 bg-gray-50 dark:bg-zinc-800 rounded-lg">
                    <div class="flex items-center space-x-3">
                      <div class="w-8 h-8 bg-emerald-100 dark:bg-emerald-950 rounded-lg flex items-center justify-center">
                        <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                      </div>
                      <div>
                        <p class="text-sm font-semibold text-gray-900 dark:text-white">${{ formatCurrency(payment.amount) }}</p>
                        <p class="text-xs text-gray-600 dark:text-zinc-400">{{ formatDate(payment.created_at) }} - {{ payment.method }}</p>
                      </div>
                    </div>
                    <span v-if="payment.notes" class="text-xs text-gray-600 dark:text-zinc-400 max-w-xs truncate">{{ payment.notes }}</span>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Sidebar (1/3) -->
          <div class="space-y-4">
            <!-- Customer Info -->
            <div class="bg-white dark:bg-zinc-900 border border-gray-300 dark:border-zinc-800 rounded-lg">
              <div class="bg-gray-50 dark:bg-zinc-900 border-b border-gray-200 dark:border-zinc-800 px-3 py-2">
                <h4 class="text-sm font-bold text-gray-900 dark:text-white">Información del Cliente</h4>
              </div>
              <div class="p-3 space-y-3">
                <div>
                  <p class="text-xs text-gray-600 dark:text-zinc-400">Nombre</p>
                  <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ selectedCustomer?.name }}</p>
                </div>
                <div>
                  <p class="text-xs text-gray-600 dark:text-zinc-400">Documento</p>
                  <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ selectedCustomer?.document_type }}: {{ selectedCustomer?.document_number }}</p>
                </div>
                <div v-if="selectedCustomer?.phone">
                  <p class="text-xs text-gray-600 dark:text-zinc-400">Teléfono</p>
                  <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ selectedCustomer?.phone }}</p>
                </div>
                <div v-if="selectedCustomer?.email">
                  <p class="text-xs text-gray-600 dark:text-zinc-400">Email</p>
                  <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ selectedCustomer?.email }}</p>
                </div>
              </div>
            </div>

            <!-- Credit Summary -->
            <div class="bg-white dark:bg-zinc-900 border border-amber-200 dark:border-amber-900/30 rounded-lg">
              <div class="bg-amber-50 dark:bg-amber-950 border-b border-amber-200 dark:border-amber-900/30 px-3 py-2">
                <h4 class="text-sm font-bold text-gray-900 dark:text-white">Resumen de Crédito</h4>
              </div>
              <div class="p-3 space-y-3">
                <div>
                  <p class="text-xs text-gray-600 dark:text-zinc-400">Cupo de Crédito</p>
                  <p class="text-lg font-bold text-gray-900 dark:text-white">${{ formatCurrency(selectedCustomer?.credit_limit || 0) }}</p>
                </div>
                <div>
                  <p class="text-xs text-gray-600 dark:text-zinc-400">Deuda Actual</p>
                  <p class="text-lg font-bold text-red-600 dark:text-red-400">${{ formatCurrency(selectedCustomer?.current_debt || 0) }}</p>
                </div>
                <div>
                  <p class="text-xs text-gray-600 dark:text-zinc-400">Crédito Disponible</p>
                  <p class="text-lg font-bold text-emerald-600 dark:text-emerald-400">${{ formatCurrency((selectedCustomer?.credit_limit || 0) - (selectedCustomer?.current_debt || 0)) }}</p>
                </div>
                <div class="pt-2 border-t border-gray-200 dark:border-zinc-800">
                  <span :class="getStatusColor(selectedCustomer)" class="px-2 py-1 rounded-full text-xs font-semibold border w-full block text-center">
                    {{ getStatusText(selectedCustomer) }}
                  </span>
                </div>
              </div>
            </div>

            <!-- Actions -->
            <div class="space-y-2">
              <button 
                @click="sendReminder(selectedCustomer)"
                :disabled="!selectedCustomer?.current_debt || selectedCustomer?.current_debt <= 0 || sendingReminder"
                class="w-full px-4 py-2.5 bg-amber-600 dark:bg-amber-700 hover:bg-amber-700 dark:hover:bg-amber-600 disabled:bg-gray-300 dark:disabled:bg-zinc-700 disabled:cursor-not-allowed text-white rounded-lg text-sm font-bold transition-all duration-200 flex items-center justify-center space-x-2"
              >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                </svg>
                <span>{{ sendingReminder ? 'Enviando...' : 'Enviar Recordatorio' }}</span>
              </button>
              <button 
                @click="openPaymentModalFromDetail()"
                :disabled="!selectedCustomer?.current_debt || selectedCustomer?.current_debt <= 0"
                class="w-full px-4 py-2.5 bg-emerald-600 dark:bg-emerald-700 hover:bg-emerald-700 dark:hover:bg-emerald-600 disabled:bg-gray-300 dark:disabled:bg-zinc-700 disabled:cursor-not-allowed text-white rounded-lg text-sm font-bold transition-all duration-200 flex items-center justify-center space-x-2"
              >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                <span>Registrar Abono</span>
              </button>
            </div>
          </div>
        </div>

        <!-- Modal Footer -->
        <div class="bg-gray-50 dark:bg-zinc-900 border-t border-gray-200 dark:border-zinc-800 px-4 py-3 flex justify-end">
          <button 
            @click="showDetailModal = false"
            class="px-5 py-2.5 bg-white dark:bg-zinc-900 hover:bg-slate-50 dark:hover:bg-zinc-800 text-slate-600 dark:text-zinc-200 text-sm font-bold rounded-xl border border-gray-300 dark:border-zinc-800 shadow-sm transition-all duration-200"
          >
            Cerrar
          </button>
        </div>
      </div>
    </div>

    <!-- Payment Modal -->
    <div v-if="showPaymentModal" class="fixed inset-0 bg-black/60 dark:bg-black/80 flex items-center justify-center z-50 p-4" @click.self="showPaymentModal = false">
      <div class="bg-white dark:bg-zinc-900 rounded-2xl shadow-xl dark:shadow-black/50 border border-gray-300 dark:border-zinc-800 max-w-md w-full p-6">
        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-4">Registrar Abono</h3>
        
        <div class="mb-4">
          <p class="text-sm text-gray-600 dark:text-zinc-400">Cliente</p>
          <p class="font-semibold text-gray-900 dark:text-white">{{ selectedCustomer?.name }}</p>
        </div>

        <div class="mb-4">
          <p class="text-sm text-gray-600 dark:text-zinc-400">Deuda Actual</p>
          <p class="text-2xl font-bold text-red-600 dark:text-red-400">${{ formatCurrency(selectedCustomer?.current_debt || 0) }}</p>
        </div>

        <div class="mb-4">
          <label class="block text-sm font-medium text-gray-700 dark:text-zinc-300 mb-2">Monto del Abono</label>
          <input 
            v-model.number="paymentAmount"
            type="number"
            step="0.01"
            min="0"
            :max="selectedCustomer?.current_debt || 0"
            class="w-full px-4 py-2 bg-white dark:bg-zinc-800 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-zinc-500 border border-gray-300 dark:border-zinc-700 rounded-lg focus:ring-2 focus:ring-emerald-500 dark:focus:ring-emerald-400 focus:border-transparent"
            placeholder="0.00"
          />
        </div>

        <div class="mb-4">
          <label class="block text-sm font-medium text-gray-700 dark:text-zinc-300 mb-2">Método de Pago</label>
          <select 
            v-model="paymentMethod"
            class="w-full px-4 py-2 bg-white dark:bg-zinc-800 text-gray-900 dark:text-white border border-gray-300 dark:border-zinc-700 rounded-lg focus:ring-2 focus:ring-emerald-500 dark:focus:ring-emerald-400 focus:border-transparent"
          >
            <option value="cash">Efectivo</option>
            <option value="card">Tarjeta</option>
            <option value="transfer">Transferencia</option>
          </select>
        </div>

        <div class="mb-6">
          <label class="block text-sm font-medium text-gray-700 dark:text-zinc-300 mb-2">Notas (opcional)</label>
          <textarea 
            v-model="paymentNotes"
            rows="3"
            class="w-full px-4 py-2 bg-white dark:bg-zinc-800 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-zinc-500 border border-gray-300 dark:border-zinc-700 rounded-lg focus:ring-2 focus:ring-emerald-500 dark:focus:ring-emerald-400 focus:border-transparent resize-none"
            placeholder="Observaciones..."
          ></textarea>
        </div>

        <div class="flex space-x-3">
          <button 
            @click="showPaymentModal = false"
            class="flex-1 px-5 py-2.5 bg-white dark:bg-zinc-900 hover:bg-slate-50 dark:hover:bg-zinc-800 text-slate-600 dark:text-zinc-200 text-sm font-bold rounded-xl border border-gray-300 dark:border-zinc-800 shadow-sm transition-all duration-200"
          >
            Cancelar
          </button>
          <button 
            @click="registerPayment"
            :disabled="!paymentAmount || paymentAmount <= 0 || processing"
            class="flex-1 px-6 py-2.5 bg-emerald-600 dark:bg-emerald-700 hover:bg-emerald-700 dark:hover:bg-emerald-600 disabled:bg-gray-300 dark:disabled:bg-zinc-700 disabled:cursor-not-allowed text-white text-sm font-bold rounded-xl shadow-lg shadow-emerald-400/40 dark:shadow-emerald-900/50 transition-all duration-300"
          >
            {{ processing ? 'Guardando...' : 'Registrar Abono' }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { customersService } from '../services/customersService.js'
import { useToast } from '../composables/useToast.js'
import axiosInstance from '../services/apiClient.js'

const { showSuccess, showError } = useToast()

// State
const customers = ref([])
const loading = ref(false)
const searchTerm = ref('')
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
  
  return filtered
})

const totalDebt = computed(() => {
  return filteredCustomers.value.reduce((sum, c) => sum + (c.current_debt || 0), 0)
})

const customersWithDebt = computed(() => {
  return filteredCustomers.value.filter(c => c.current_debt > 0).length
})

const todayPayments = computed(() => {
  // TODO: Obtener de la API los pagos del día
  return 0
})

const averageDaysOverdue = computed(() => {
  // TODO: Calcular días de mora promedio
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

const getStatusColor = (customer) => {
  const debt = customer.current_debt || 0
  const limit = customer.credit_limit || 0
  const percentage = limit > 0 ? (debt / limit) * 100 : 0
  
  if (debt === 0) return 'bg-emerald-50 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-400 border-emerald-100 dark:border-emerald-800'
  if (percentage >= 90) return 'bg-rose-50 dark:bg-rose-950 text-rose-700 dark:text-rose-400 border-rose-100 dark:border-rose-800'
  if (percentage >= 70) return 'bg-amber-50 dark:bg-amber-950 text-amber-700 dark:text-amber-400 border-amber-100 dark:border-amber-800'
  return 'bg-blue-50 dark:bg-blue-950 text-blue-700 dark:text-blue-400 border-blue-100 dark:border-blue-800'
}

const getStatusText = (customer) => {
  const debt = customer.current_debt || 0
  const limit = customer.credit_limit || 0
  const percentage = limit > 0 ? (debt / limit) * 100 : 0
  
  if (debt === 0) return 'Al día'
  if (percentage >= 90) return 'Crítico'
  if (percentage >= 70) return 'Alto'
  return 'Normal'
}

const openPaymentModal = (customer) => {
  selectedCustomer.value = customer
  paymentAmount.value = 0
  paymentMethod.value = 'cash'
  paymentNotes.value = ''
  showPaymentModal.value = true
}

const registerPayment = async () => {
  if (!paymentAmount.value || paymentAmount.value <= 0) {
    showError('Ingrese un monto válido')
    return
  }

  if (paymentAmount.value > selectedCustomer.value.current_debt) {
    showError('El monto no puede ser mayor a la deuda')
    return
  }

  processing.value = true
  try {
    const response = await axiosInstance.post('/credit-payments', {
      customer_id: selectedCustomer.value.id,
      amount: paymentAmount.value,
      method: paymentMethod.value,
      notes: paymentNotes.value
    })

    if (response.data.success) {
      showSuccess('Abono registrado exitosamente')
      showPaymentModal.value = false
      await loadCustomers()
    }
  } catch (error) {
    console.error('Error registering payment:', error)
    showError('Error al registrar el abono')
  } finally {
    processing.value = false
  }
}

const viewCustomerDetail = async (customer) => {
  selectedCustomer.value = customer
  showDetailModal.value = true
  
  // Load customer credit invoices and payments
  await loadCustomerCreditDetail(customer.id)
}

const loadCustomerCreditDetail = async (customerId) => {
  loadingDetail.value = true
  try {
    // Load credit invoices - filter by payment_method=credit
    const invoicesResponse = await axiosInstance.get('/invoices', {
      params: {
        customer_id: customerId,
        payment_method: 'credit'
      }
    })
    
    // Filter to ensure only credit invoices are shown
    const allInvoices = invoicesResponse.data.data || invoicesResponse.data || []
    creditInvoices.value = allInvoices.filter(invoice => invoice.payment_method === 'credit')
    
    // Load credit payments
    const paymentsResponse = await axiosInstance.get('/credit-payments', {
      params: {
        customer_id: customerId
      }
    })
    creditPayments.value = paymentsResponse.data.data || paymentsResponse.data || []
  } catch (error) {
    console.error('Error loading customer credit detail:', error)
    showError('Error al cargar el detalle del cliente')
  } finally {
    loadingDetail.value = false
  }
}

const openPaymentModalFromDetail = () => {
  showDetailModal.value = false
  openPaymentModal(selectedCustomer.value)
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

const calculateDaysSince = (date) => {
  if (!date) return 0
  const invoiceDate = new Date(date)
  const today = new Date()
  const diffTime = Math.abs(today - invoiceDate)
  const diffDays = Math.floor(diffTime / (1000 * 60 * 60 * 24))
  return diffDays
}

const getDaysBadgeColor = (days) => {
  if (days <= 7) return 'bg-emerald-50 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-400 border-emerald-100 dark:border-emerald-800'
  if (days <= 15) return 'bg-blue-50 dark:bg-blue-950 text-blue-700 dark:text-blue-400 border-blue-100 dark:border-blue-800'
  if (days <= 30) return 'bg-amber-50 dark:bg-amber-950 text-amber-700 dark:text-amber-400 border-amber-100 dark:border-amber-800'
  return 'bg-rose-50 dark:bg-rose-950 text-rose-700 dark:text-rose-400 border-rose-100 dark:border-rose-800'
}

const formatDate = (date) => {
  if (!date) return ''
  return new Date(date).toLocaleDateString('es-CO', {
    year: 'numeric',
    month: 'short',
    day: 'numeric'
  })
}

const formatCurrency = (value) => {
  return new Intl.NumberFormat('es-CO').format(parseFloat(value || 0))
}

// Initialization
onMounted(() => {
  loadCustomers()
})
</script>
