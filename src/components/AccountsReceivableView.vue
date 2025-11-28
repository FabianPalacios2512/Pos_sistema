<template>
  <div class="min-h-screen bg-gray-100 font-sans">
    <div class="p-4 lg:p-6 space-y-6 animate-fade-in">
      <!-- Header Empresarial -->
      <div class="flex items-center justify-between pb-4 border-b border-gray-300">
        <div class="flex items-center space-x-4">
          <div class="w-12 h-12 bg-gradient-to-br from-blue-600 to-indigo-600 rounded-xl flex items-center justify-center shadow-lg">
            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
          </div>
          <div>
            <h1 class="text-2xl font-bold text-gray-900">Cuentas por Cobrar</h1>
            <p class="text-sm text-gray-600">Gestión de créditos y abonos de clientes</p>
          </div>
        </div>
        
        <div class="flex items-center space-x-3">
          <div class="relative">
            <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <input 
              v-model="searchTerm"
              type="text" 
              placeholder="Buscar cliente..."
              class="pl-9 pr-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            />
          </div>
        </div>
      </div>

      <!-- Stats Cards Empresariales -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white rounded-2xl p-5 border border-gray-300 hover:border-gray-400 transition-all duration-200 hover:shadow-lg">
          <div class="flex items-center space-x-4">
            <div class="w-12 h-12 bg-gray-100 rounded-xl flex items-center justify-center flex-shrink-0">
              <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
              </svg>
            </div>
            <div class="flex-1 min-w-0">
              <div class="flex items-center justify-between mb-2">
                <h3 class="text-sm font-semibold text-gray-700">Total por Cobrar</h3>
                <span class="text-xs font-medium text-red-700 bg-red-50 px-2 py-1 rounded-lg border border-red-200">
                  Pendiente
                </span>
              </div>
              <p class="text-2xl font-bold text-gray-900 mb-1">${{ formatCurrency(totalDebt) }}</p>
              <p class="text-sm text-gray-500">{{ customersWithDebt }} clientes</p>
            </div>
          </div>
        </div>

        <div class="bg-white rounded-2xl p-5 border border-gray-300 hover:border-gray-400 transition-all duration-200 hover:shadow-lg">
          <div class="flex items-center space-x-4">
            <div class="w-12 h-12 bg-gray-100 rounded-xl flex items-center justify-center flex-shrink-0">
              <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
              </svg>
            </div>
            <div class="flex-1 min-w-0">
              <div class="flex items-center justify-between mb-2">
                <h3 class="text-sm font-semibold text-gray-700">Clientes con Crédito</h3>
                <span class="text-xs font-medium text-blue-700 bg-blue-50 px-2 py-1 rounded-lg border border-blue-200">
                  Activos
                </span>
              </div>
              <p class="text-2xl font-bold text-gray-900 mb-1">{{ customersWithDebt }}</p>
              <p class="text-sm text-gray-500">{{ filteredCustomers.length }} total</p>
            </div>
          </div>
        </div>

        <div class="bg-white rounded-2xl p-5 border border-gray-300 hover:border-gray-400 transition-all duration-200 hover:shadow-lg">
          <div class="flex items-center space-x-4">
            <div class="w-12 h-12 bg-gray-100 rounded-xl flex items-center justify-center flex-shrink-0">
              <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
              </svg>
            </div>
            <div class="flex-1 min-w-0">
              <div class="flex items-center justify-between mb-2">
                <h3 class="text-sm font-semibold text-gray-700">Recaudado Hoy</h3>
                <span class="text-xs font-medium text-green-700 bg-green-50 px-2 py-1 rounded-lg border border-green-200">
                  Hoy
                </span>
              </div>
              <p class="text-2xl font-bold text-gray-900 mb-1">${{ formatCurrency(todayPayments) }}</p>
              <p class="text-sm text-gray-500">Abonos registrados</p>
            </div>
          </div>
        </div>

        <div class="bg-white rounded-2xl p-5 border border-gray-300 hover:border-gray-400 transition-all duration-200 hover:shadow-lg">
          <div class="flex items-center space-x-4">
            <div class="w-12 h-12 bg-gray-100 rounded-xl flex items-center justify-center flex-shrink-0">
              <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
              </svg>
            </div>
            <div class="flex-1 min-w-0">
              <div class="flex items-center justify-between mb-2">
                <h3 class="text-sm font-semibold text-gray-700">Mora Promedio</h3>
                <span class="text-xs font-medium text-amber-700 bg-amber-50 px-2 py-1 rounded-lg border border-amber-200">
                  Días
                </span>
              </div>
              <p class="text-2xl font-bold text-gray-900 mb-1">{{ averageDaysOverdue }}</p>
              <p class="text-sm text-gray-500">Días transcurridos</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Customers List -->
      <div class="bg-white rounded-2xl border border-gray-300">
        <div class="bg-gray-50 border-b border-gray-200 px-5 py-4 flex items-center justify-between">
          <div>
            <h2 class="text-base font-bold text-gray-900">Clientes con Crédito</h2>
            <p class="text-xs text-gray-500 mt-0.5">{{ filteredCustomers.length }} clientes encontrados</p>
          </div>
        </div>
        <div class="overflow-x-auto">
          <table class="w-full">
            <thead class="bg-gray-50 border-b border-gray-200">
              <tr>
                <th class="px-3 py-2 text-left text-xs font-bold text-gray-600 uppercase tracking-wide">Cliente</th>
                <th class="px-3 py-2 text-left text-xs font-bold text-gray-600 uppercase tracking-wide">Cupo</th>
                <th class="px-3 py-2 text-left text-xs font-bold text-gray-600 uppercase tracking-wide">Deuda Actual</th>
                <th class="px-3 py-2 text-left text-xs font-bold text-gray-600 uppercase tracking-wide">Disponible</th>
                <th class="px-3 py-2 text-left text-xs font-bold text-gray-600 uppercase tracking-wide">Estado</th>
                <th class="px-3 py-2 text-right text-xs font-bold text-gray-600 uppercase tracking-wide">Acciones</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-if="loading" class="animate-pulse">
                <td colspan="6" class="px-3 py-8 text-center text-gray-500">
                  <div class="flex items-center justify-center space-x-2">
                    <div class="w-5 h-5 border-2 border-blue-600 border-t-transparent rounded-full animate-spin"></div>
                    <span>Cargando clientes...</span>
                  </div>
                </td>
              </tr>
              <tr v-else-if="filteredCustomers.length === 0">
                <td colspan="6" class="px-3 py-8 text-center text-gray-500">
                  <svg class="w-16 h-16 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                  </svg>
                  <p class="text-lg font-medium mb-2">No hay clientes con crédito activo</p>
                  <p class="text-sm">Los clientes aparecerán aquí cuando tengan crédito habilitado</p>
                </td>
              </tr>
              <tr v-else v-for="customer in filteredCustomers" :key="customer.id" class="hover:bg-gray-50 transition-colors">
                <td class="px-3 py-3">
                  <div class="flex items-center">
                    <div class="w-10 h-10 bg-blue-600 rounded-full flex items-center justify-center text-white font-semibold mr-3">
                      {{ customer.name.charAt(0).toUpperCase() }}
                    </div>
                    <div>
                      <p class="font-semibold text-gray-900">{{ customer.name }}</p>
                      <p class="text-sm text-gray-500">{{ customer.document_type }}: {{ customer.document_number }}</p>
                    </div>
                  </div>
                </td>
                <td class="px-3 py-3">
                  <p class="font-semibold text-gray-900">${{ formatCurrency(customer.credit_limit || 0) }}</p>
                </td>
                <td class="px-3 py-3">
                  <p class="font-semibold" :class="customer.current_debt > 0 ? 'text-red-600' : 'text-gray-900'">
                    ${{ formatCurrency(customer.current_debt || 0) }}
                  </p>
                </td>
                <td class="px-3 py-3">
                  <p class="font-semibold text-green-600">
                    ${{ formatCurrency((customer.credit_limit || 0) - (customer.current_debt || 0)) }}
                  </p>
                </td>
                <td class="px-3 py-3">
                  <span 
                    :class="getStatusColor(customer)"
                    class="px-2 py-0.5 rounded-full text-xs font-semibold"
                  >
                    {{ getStatusText(customer) }}
                  </span>
                </td>
                <td class="px-3 py-3 text-right">
                  <div class="flex items-center justify-end space-x-2">
                    <button 
                      @click="sendReminder(customer)"
                      :disabled="!customer.current_debt || customer.current_debt <= 0 || sendingReminder"
                      class="p-1.5 bg-amber-100 hover:bg-amber-200 disabled:bg-gray-100 disabled:cursor-not-allowed text-amber-600 disabled:text-gray-400 rounded-lg transition-colors"
                      title="Enviar Recordatorio"
                    >
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                      </svg>
                    </button>
                    <button 
                      @click="openPaymentModal(customer)"
                      :disabled="!customer.current_debt || customer.current_debt <= 0"
                      class="px-3 py-1.5 bg-green-600 hover:bg-green-700 disabled:bg-gray-300 disabled:cursor-not-allowed text-white rounded-lg text-xs font-medium transition-colors"
                    >
                      Abono
                    </button>
                    <button 
                      @click="viewCustomerDetail(customer)"
                      class="px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-xs font-medium transition-colors"
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
    <div v-if="showDetailModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4" @click.self="showDetailModal = false">
      <div class="bg-white rounded-2xl shadow-2xl max-w-6xl w-full max-h-[90vh] overflow-auto">
        <!-- Modal Header -->
        <div class="bg-white border-b px-4 py-3 flex items-center justify-between sticky top-0 z-10">
          <div class="flex items-center space-x-3">
            <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
              <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
              </svg>
            </div>
            <div>
              <h3 class="text-base font-bold text-gray-900">Detalle de Crédito</h3>
              <p class="text-xs text-gray-500">{{ selectedCustomer?.name }}</p>
            </div>
          </div>
          <button @click="showDetailModal = false" class="text-gray-400 hover:text-gray-600">
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
            <div class="bg-white border rounded-lg">
              <div class="bg-gray-50 border-b px-3 py-2">
                <h4 class="text-sm font-bold text-gray-900">Facturas a Crédito</h4>
              </div>
              <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                  <thead class="bg-gray-50">
                    <tr>
                      <th class="px-3 py-2 text-left text-xs font-bold text-gray-600 uppercase">Factura</th>
                      <th class="px-3 py-2 text-left text-xs font-bold text-gray-600 uppercase">Fecha</th>
                      <th class="px-3 py-2 text-left text-xs font-bold text-gray-600 uppercase">Días</th>
                      <th class="px-3 py-2 text-left text-xs font-bold text-gray-600 uppercase">Monto</th>
                      <th class="px-3 py-2 text-left text-xs font-bold text-gray-600 uppercase">Recargo</th>
                      <th class="px-3 py-2 text-left text-xs font-bold text-gray-600 uppercase">Total</th>
                    </tr>
                  </thead>
                  <tbody v-if="loadingDetail" class="bg-white">
                    <tr>
                      <td colspan="6" class="px-3 py-8 text-center">
                        <div class="flex items-center justify-center space-x-2">
                          <div class="w-5 h-5 border-2 border-blue-600 border-t-transparent rounded-full animate-spin"></div>
                          <span class="text-sm text-gray-500">Cargando facturas...</span>
                        </div>
                      </td>
                    </tr>
                  </tbody>
                  <tbody v-else-if="creditInvoices.length === 0" class="bg-white">
                    <tr>
                      <td colspan="6" class="px-3 py-8 text-center text-gray-500">
                        <p class="text-sm">No hay facturas a crédito</p>
                      </td>
                    </tr>
                  </tbody>
                  <tbody v-else class="bg-white divide-y divide-gray-200">
                    <tr v-for="invoice in creditInvoices" :key="invoice.id" class="hover:bg-gray-50">
                      <td class="px-3 py-3">
                        <p class="text-sm font-semibold text-gray-900">#{{ invoice.invoice_number }}</p>
                      </td>
                      <td class="px-3 py-3">
                        <p class="text-sm text-gray-700">{{ formatDate(invoice.created_at) }}</p>
                      </td>
                      <td class="px-3 py-3">
                        <span :class="getDaysBadgeColor(calculateDaysSince(invoice.created_at))" class="px-2 py-0.5 rounded-full text-xs font-semibold">
                          {{ calculateDaysSince(invoice.created_at) }} días
                        </span>
                      </td>
                      <td class="px-3 py-3">
                        <p class="text-sm font-semibold text-gray-900">${{ formatCurrency(invoice.total - (invoice.surcharge_amount || 0)) }}</p>
                      </td>
                      <td class="px-3 py-3">
                        <p class="text-sm font-semibold text-amber-600">${{ formatCurrency(invoice.surcharge_amount || 0) }}</p>
                      </td>
                      <td class="px-3 py-3">
                        <p class="text-sm font-bold text-gray-900">${{ formatCurrency(invoice.total) }}</p>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>

            <!-- Payment History -->
            <div class="bg-white border rounded-lg">
              <div class="bg-gray-50 border-b px-3 py-2">
                <h4 class="text-sm font-bold text-gray-900">Historial de Abonos</h4>
              </div>
              <div class="p-3">
                <div v-if="loadingDetail" class="text-center py-4">
                  <div class="flex items-center justify-center space-x-2">
                    <div class="w-5 h-5 border-2 border-blue-600 border-t-transparent rounded-full animate-spin"></div>
                    <span class="text-sm text-gray-500">Cargando abonos...</span>
                  </div>
                </div>
                <div v-else-if="creditPayments.length === 0" class="text-center py-4">
                  <p class="text-sm text-gray-500">No hay abonos registrados</p>
                </div>
                <div v-else class="space-y-2">
                  <div v-for="payment in creditPayments" :key="payment.id" class="flex items-center justify-between p-2 bg-gray-50 rounded-lg">
                    <div class="flex items-center space-x-3">
                      <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center">
                        <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                      </div>
                      <div>
                        <p class="text-sm font-semibold text-gray-900">${{ formatCurrency(payment.amount) }}</p>
                        <p class="text-xs text-gray-500">{{ formatDate(payment.created_at) }} - {{ payment.method }}</p>
                      </div>
                    </div>
                    <span v-if="payment.notes" class="text-xs text-gray-500 max-w-xs truncate">{{ payment.notes }}</span>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Sidebar (1/3) -->
          <div class="space-y-4">
            <!-- Customer Info -->
            <div class="bg-white border rounded-lg">
              <div class="bg-gray-50 border-b px-3 py-2">
                <h4 class="text-sm font-bold text-gray-900">Información del Cliente</h4>
              </div>
              <div class="p-3 space-y-3">
                <div>
                  <p class="text-xs text-gray-500">Nombre</p>
                  <p class="text-sm font-semibold text-gray-900">{{ selectedCustomer?.name }}</p>
                </div>
                <div>
                  <p class="text-xs text-gray-500">Documento</p>
                  <p class="text-sm font-semibold text-gray-900">{{ selectedCustomer?.document_type }}: {{ selectedCustomer?.document_number }}</p>
                </div>
                <div v-if="selectedCustomer?.phone">
                  <p class="text-xs text-gray-500">Teléfono</p>
                  <p class="text-sm font-semibold text-gray-900">{{ selectedCustomer?.phone }}</p>
                </div>
                <div v-if="selectedCustomer?.email">
                  <p class="text-xs text-gray-500">Email</p>
                  <p class="text-sm font-semibold text-gray-900">{{ selectedCustomer?.email }}</p>
                </div>
              </div>
            </div>

            <!-- Credit Summary -->
            <div class="bg-white border border-orange-200 rounded-lg">
              <div class="bg-orange-50 border-b border-orange-200 px-3 py-2">
                <h4 class="text-sm font-bold text-gray-900">Resumen de Crédito</h4>
              </div>
              <div class="p-3 space-y-3">
                <div>
                  <p class="text-xs text-gray-500">Cupo de Crédito</p>
                  <p class="text-lg font-bold text-gray-900">${{ formatCurrency(selectedCustomer?.credit_limit || 0) }}</p>
                </div>
                <div>
                  <p class="text-xs text-gray-500">Deuda Actual</p>
                  <p class="text-lg font-bold text-red-600">${{ formatCurrency(selectedCustomer?.current_debt || 0) }}</p>
                </div>
                <div>
                  <p class="text-xs text-gray-500">Crédito Disponible</p>
                  <p class="text-lg font-bold text-green-600">${{ formatCurrency((selectedCustomer?.credit_limit || 0) - (selectedCustomer?.current_debt || 0)) }}</p>
                </div>
                <div class="pt-2 border-t">
                  <span :class="getStatusColor(selectedCustomer)" class="px-2 py-1 rounded-full text-xs font-semibold w-full block text-center">
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
                class="w-full px-4 py-2.5 bg-gradient-to-r from-amber-400 to-orange-400 hover:from-amber-500 hover:to-orange-500 disabled:from-gray-300 disabled:to-gray-400 text-white rounded-lg text-sm font-semibold transition-all duration-200 flex items-center justify-center space-x-2"
              >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                </svg>
                <span>{{ sendingReminder ? 'Enviando...' : 'Enviar Recordatorio' }}</span>
              </button>
              <button 
                @click="openPaymentModalFromDetail()"
                :disabled="!selectedCustomer?.current_debt || selectedCustomer?.current_debt <= 0"
                class="w-full px-4 py-2.5 bg-gradient-to-r from-green-400 to-emerald-400 hover:from-green-500 hover:to-emerald-500 disabled:from-gray-300 disabled:to-gray-400 text-white rounded-lg text-sm font-semibold transition-all duration-200 flex items-center justify-center space-x-2"
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
        <div class="bg-gray-50 border-t px-4 py-3 flex justify-end">
          <button 
            @click="showDetailModal = false"
            class="px-4 py-2 bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 rounded-lg text-sm font-medium transition-colors"
          >
            Cerrar
          </button>
        </div>
      </div>
    </div>

    <!-- Payment Modal -->
    <div v-if="showPaymentModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4" @click.self="showPaymentModal = false">
      <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full p-6">
        <h3 class="text-xl font-bold text-gray-900 mb-4">Registrar Abono</h3>
        
        <div class="mb-4">
          <p class="text-sm text-gray-600">Cliente</p>
          <p class="font-semibold text-gray-900">{{ selectedCustomer?.name }}</p>
        </div>

        <div class="mb-4">
          <p class="text-sm text-gray-600">Deuda Actual</p>
          <p class="text-2xl font-bold text-red-600">${{ formatCurrency(selectedCustomer?.current_debt || 0) }}</p>
        </div>

        <div class="mb-4">
          <label class="block text-sm font-medium text-gray-700 mb-2">Monto del Abono</label>
          <input 
            v-model.number="paymentAmount"
            type="number"
            step="0.01"
            min="0"
            :max="selectedCustomer?.current_debt || 0"
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
            placeholder="0.00"
          />
        </div>

        <div class="mb-4">
          <label class="block text-sm font-medium text-gray-700 mb-2">Método de Pago</label>
          <select 
            v-model="paymentMethod"
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
          >
            <option value="cash">Efectivo</option>
            <option value="card">Tarjeta</option>
            <option value="transfer">Transferencia</option>
          </select>
        </div>

        <div class="mb-6">
          <label class="block text-sm font-medium text-gray-700 mb-2">Notas (opcional)</label>
          <textarea 
            v-model="paymentNotes"
            rows="3"
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent resize-none"
            placeholder="Observaciones..."
          ></textarea>
        </div>

        <div class="flex space-x-3">
          <button 
            @click="showPaymentModal = false"
            class="flex-1 px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg font-medium transition-colors"
          >
            Cancelar
          </button>
          <button 
            @click="registerPayment"
            :disabled="!paymentAmount || paymentAmount <= 0 || processing"
            class="flex-1 px-4 py-2 bg-green-600 hover:bg-green-700 disabled:bg-gray-300 disabled:cursor-not-allowed text-white rounded-lg font-medium transition-colors"
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
  
  if (debt === 0) return 'bg-green-100 text-green-800'
  if (percentage >= 90) return 'bg-red-100 text-red-800'
  if (percentage >= 70) return 'bg-yellow-100 text-yellow-800'
  return 'bg-blue-100 text-blue-800'
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
  if (days <= 7) return 'bg-green-100 text-green-700'
  if (days <= 15) return 'bg-blue-100 text-blue-700'
  if (days <= 30) return 'bg-amber-100 text-amber-700'
  return 'bg-red-100 text-red-700'
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
