<template>
  <div class="min-h-screen bg-gray-100 font-sans">
    <div class="p-4 lg:p-6 space-y-6 animate-fade-in">
      
      <!-- Header Elegante -->
      <div class="flex items-center justify-between pb-4 border-b border-gray-300">
        <div class="flex items-center space-x-4">
          <div class="w-12 h-12 bg-gradient-to-br from-blue-600 to-indigo-600 rounded-xl flex items-center justify-center shadow-lg">
            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 15v-1a4 4 0 00-4-4H8m0 0l3 3m-3-3l3-3m9 14V5a2 2 0 00-2-2H6a2 2 0 00-2 2v16l4-2 4 2 4-2 4 2z"></path>
            </svg>
          </div>
          <div>
            <h1 class="text-2xl font-bold text-gray-900">Gestión de Devoluciones</h1>
            <p class="text-sm text-gray-600">Sistema de devoluciones y reembolsos</p>
          </div>
        </div>
        
        <div class="flex items-center space-x-3">
          <button
            @click="exportData"
            class="px-4 py-2.5 bg-slate-50 hover:bg-slate-100 text-slate-600 text-sm font-semibold rounded-xl border border-slate-200 shadow-sm transition-all duration-200 flex items-center space-x-2 hover:shadow-md hover:border-slate-300">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            <span>Exportar</span>
          </button>
          
          <button
            @click="loadReturns"
            class="px-4 py-2.5 bg-slate-50 hover:bg-slate-100 text-slate-600 text-sm font-semibold rounded-xl border border-slate-200 shadow-sm transition-all duration-200 flex items-center space-x-2 hover:shadow-md hover:border-slate-300">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
            </svg>
            <span>Actualizar</span>
          </button>
        </div>
      </div>

      <!-- Métricas Principales -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white rounded-2xl p-5 border border-gray-300 hover:border-gray-400 transition-all duration-200 hover:shadow-lg">
          <div class="flex items-center space-x-4">
            <div class="w-12 h-12 bg-gray-100 rounded-xl flex items-center justify-center flex-shrink-0">
              <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 15v-1a4 4 0 00-4-4H8m0 0l3 3m-3-3l3-3m9 14V5a2 2 0 00-2-2H6a2 2 0 00-2 2v16l4-2 4 2 4-2 4 2z"></path>
              </svg>
            </div>
            <div class="flex-1 min-w-0">
              <div class="flex items-center justify-between mb-2">
                <h3 class="text-sm font-semibold text-gray-700">Total Devuelto</h3>
              </div>
              <p class="text-2xl font-bold text-gray-900 mb-1">${{ formatCurrency(stats.totalRefunded) }}</p>
              <p class="text-sm text-gray-500">{{ stats.totalReturns }} devoluciones</p>
            </div>
          </div>
        </div>

        <div class="bg-white rounded-2xl p-5 border border-gray-300 hover:border-gray-400 transition-all duration-200 hover:shadow-lg">
          <div class="flex items-center space-x-4">
            <div class="w-12 h-12 bg-gray-100 rounded-xl flex items-center justify-center flex-shrink-0">
              <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
              </svg>
            </div>
            <div class="flex-1 min-w-0">
              <div class="flex items-center justify-between mb-2">
                <h3 class="text-sm font-semibold text-gray-700">Completadas</h3>
                <span class="text-xs font-medium text-green-700 bg-green-50 px-2 py-1 rounded-lg border border-green-200">
                  {{ stats.completedCount }}
                </span>
              </div>
              <p class="text-2xl font-bold text-gray-900 mb-1">${{ formatCurrency(stats.completedAmount) }}</p>
              <p class="text-sm text-gray-500">Procesadas</p>
            </div>
          </div>
        </div>

        <div class="bg-white rounded-2xl p-5 border border-gray-300 hover:border-gray-400 transition-all duration-200 hover:shadow-lg">
          <div class="flex items-center space-x-4">
            <div class="w-12 h-12 bg-gray-100 rounded-xl flex items-center justify-center flex-shrink-0">
              <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
              </svg>
            </div>
            <div class="flex-1 min-w-0">
              <div class="flex items-center justify-between mb-2">
                <h3 class="text-sm font-semibold text-gray-700">Pendientes</h3>
                <span class="text-xs font-medium text-yellow-700 bg-yellow-50 px-2 py-1 rounded-lg border border-yellow-200">
                  {{ stats.pendingCount }}
                </span>
              </div>
              <p class="text-2xl font-bold text-gray-900 mb-1">${{ formatCurrency(stats.pendingAmount) }}</p>
              <p class="text-sm text-gray-500">Por procesar</p>
            </div>
          </div>
        </div>

        <div class="bg-white rounded-2xl p-5 border border-gray-300 hover:border-gray-400 transition-all duration-200 hover:shadow-lg">
          <div class="flex items-center space-x-4">
            <div class="w-12 h-12 bg-gray-100 rounded-xl flex items-center justify-center flex-shrink-0">
              <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
              </svg>
            </div>
            <div class="flex-1 min-w-0">
              <div class="flex items-center justify-between mb-2">
                <h3 class="text-sm font-semibold text-gray-700">Productos Devueltos</h3>
              </div>
              <p class="text-2xl font-bold text-gray-900 mb-1">{{ stats.totalItemsReturned }}</p>
              <p class="text-sm text-gray-500">Unidades totales</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Filtros Compactos -->
      <div class="bg-white rounded-2xl p-3 border border-gray-300 shadow-sm">
        <div class="flex flex-wrap items-center gap-3">
          <div class="flex-1 min-w-48 relative">
            <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
            <input
              v-model="searchTerm"
              type="text"
              placeholder="Buscar por número o cliente..."
              class="w-full pl-9 pr-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
          </div>
          
          <select
            v-model="statusFilter"
            class="pl-3 pr-8 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 min-w-36">
            <option value="">Todos los Estados</option>
            <option value="pending">Pendientes</option>
            <option value="completed">Completadas</option>
            <option value="cancelled">Canceladas</option>
          </select>
          
          <select
            v-model="refundMethodFilter"
            class="pl-3 pr-8 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 min-w-36">
            <option value="">Todos los Métodos</option>
            <option value="cash">Efectivo</option>
            <option value="card">Tarjeta</option>
            <option value="transfer">Transferencia</option>
            <option value="store_credit">Crédito</option>
          </select>
          
          <button
            @click="clearFilters"
            class="p-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition-colors"
            title="Limpiar filtros">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
            </svg>
          </button>
        </div>
      </div>

      <!-- Master-Detail Layout -->
      <div class="grid grid-cols-1 lg:grid-cols-12 gap-6" style="height: calc(100vh - 450px); min-height: 600px;">
        
        <!-- PANEL IZQUIERDO: Lista de Devoluciones (30%) -->
        <div class="lg:col-span-4 bg-white rounded-2xl shadow-sm border border-gray-300 overflow-hidden flex flex-col">
          
          <!-- Header del panel -->
          <div class="p-4 border-b border-gray-200 bg-gray-50">
            <h3 class="text-sm font-bold text-gray-900">Devoluciones</h3>
            <p class="text-xs text-gray-500 mt-0.5">{{ filteredReturns.length }} registros</p>
          </div>
          
          <!-- Lista scrollable -->
          <div class="flex-1 overflow-y-auto p-3 space-y-2">
            
            <div
              v-for="returnItem in filteredReturns"
              :key="returnItem.id"
              @click="selectReturn(returnItem)"
              :class="[
                'p-3 rounded-xl border-2 transition-all cursor-pointer hover:shadow-md',
                selectedReturn?.id === returnItem.id
                  ? 'bg-blue-50 border-blue-400 shadow-sm'
                  : 'bg-white border-gray-200 hover:border-gray-300'
              ]">
              
              <div class="flex items-start justify-between mb-2">
                <div class="flex-1">
                  <p class="text-sm font-bold text-gray-900">{{ returnItem.number }}</p>
                  <p class="text-xs text-gray-600 mt-0.5">{{ returnItem.customer?.name || 'Cliente General' }}</p>
                </div>
                
                <span
                  :class="[
                    'px-2 py-0.5 rounded-full text-xs font-semibold',
                    returnItem.status === 'completed' ? 'bg-green-100 text-green-700' :
                    returnItem.status === 'pending' ? 'bg-yellow-100 text-yellow-700' :
                    'bg-red-100 text-red-700'
                  ]">
                  {{ getStatusLabel(returnItem.status) }}
                </span>
              </div>
              
              <div class="flex items-center justify-between text-xs">
                <span class="text-gray-500">{{ formatDate(returnItem.return_date) }}</span>
                <span class="font-bold text-red-600">${{ formatCurrency(returnItem.total) }}</span>
              </div>
            </div>
            
            <!-- Estado vacío -->
            <div v-if="filteredReturns.length === 0" class="flex flex-col items-center justify-center py-12 text-center">
              <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center mb-3">
                <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
              </div>
              <p class="text-sm font-semibold text-gray-700">No hay devoluciones</p>
              <p class="text-xs text-gray-500 mt-1">Ajusta los filtros para ver más resultados</p>
            </div>
          </div>
        </div>

        <!-- PANEL DERECHO: Detalle de Devolución (70%) -->
        <div class="lg:col-span-8 bg-white rounded-2xl shadow-sm border border-gray-300 overflow-hidden flex flex-col">
          
          <!-- Estado: No seleccionado -->
          <div v-if="!selectedReturn" class="flex-1 flex flex-col items-center justify-center p-12 text-center">
            <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mb-4">
              <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
              </svg>
            </div>
            <h3 class="text-lg font-bold text-gray-900 mb-2">Selecciona una devolución</h3>
            <p class="text-sm text-gray-500">Haz clic en cualquier devolución de la lista para ver sus detalles completos</p>
          </div>

          <!-- Estado: Devolución seleccionada -->
          <div v-else class="flex-1 flex flex-col overflow-hidden">
            
            <!-- Header del detalle -->
            <div class="p-4 border-b border-gray-200 bg-gray-50">
              <div class="flex items-start justify-between mb-3">
                <div>
                  <h2 class="text-xl font-bold text-gray-900">{{ selectedReturn.number }}</h2>
                  <p class="text-xs text-gray-500 mt-1">{{ formatDate(selectedReturn.return_date) }}</p>
                </div>
                
                <div class="flex items-center gap-2">
                  <span
                    :class="[
                      'px-3 py-1 rounded-full text-xs font-semibold',
                      selectedReturn.status === 'completed' ? 'bg-green-100 text-green-700' :
                      selectedReturn.status === 'pending' ? 'bg-yellow-100 text-yellow-700' :
                      'bg-red-100 text-red-700'
                    ]">
                    {{ getStatusLabel(selectedReturn.status) }}
                  </span>
                </div>
              </div>
              
              <!-- Botones de acción -->
              <div class="flex items-center gap-2">
                <button
                  @click="printReturn"
                  class="px-3 py-2 bg-white border-2 border-blue-300 text-blue-700 text-xs font-semibold rounded-lg hover:bg-blue-50 transition-all flex items-center gap-1.5">
                  <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                  </svg>
                  Imprimir
                </button>
                
                <button
                  @click="sendByEmail"
                  class="px-3 py-2 bg-white border-2 border-gray-300 text-gray-700 text-xs font-semibold rounded-lg hover:bg-gray-50 transition-all flex items-center gap-1.5">
                  <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                  </svg>
                  Enviar por Correo
                </button>
              </div>
            </div>

            <!-- Contenido scrollable -->
            <div class="flex-1 overflow-y-auto p-4 space-y-4">
              
              <!-- Sección: Productos Devueltos -->
              <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
                <div class="bg-gray-50 border-b border-gray-200 px-4 py-3">
                  <h4 class="text-sm font-bold text-gray-900">Productos Devueltos</h4>
                </div>
                
                <div class="p-3">
                  <table class="min-w-full">
                    <thead>
                      <tr class="border-b border-gray-200">
                        <th class="text-left text-xs font-semibold text-gray-600 pb-2">Producto</th>
                        <th class="text-center text-xs font-semibold text-gray-600 pb-2">Cant.</th>
                        <th class="text-right text-xs font-semibold text-gray-600 pb-2">P. Unit.</th>
                        <th class="text-right text-xs font-semibold text-gray-600 pb-2">Subtotal</th>
                      </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                      <tr v-for="item in selectedReturn.return_items" :key="item.id">
                        <td class="py-2">
                          <p class="text-sm font-semibold text-gray-900">{{ item.product?.name || 'N/A' }}</p>
                          <p class="text-xs text-gray-500">{{ item.product?.code || 'N/A' }}</p>
                        </td>
                        <td class="text-center">
                          <span class="text-sm font-semibold text-gray-900">{{ item.quantity }}</span>
                        </td>
                        <td class="text-right">
                          <span class="text-sm text-gray-700">${{ formatCurrency(item.unit_price) }}</span>
                        </td>
                        <td class="text-right">
                          <span class="text-sm font-bold text-gray-900">${{ formatCurrency(item.subtotal) }}</span>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                  
                  <!-- Total -->
                  <div class="mt-3 pt-3 border-t-2 border-gray-300">
                    <div class="flex justify-end">
                      <div class="text-right">
                        <p class="text-xs text-gray-500 mb-1">Total Devuelto</p>
                        <p class="text-2xl font-bold text-red-600">${{ formatCurrency(selectedReturn.total) }}</p>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Sección: Razón de Devolución -->
              <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-4">
                <div class="flex items-start gap-3">
                  <div class="w-8 h-8 bg-yellow-100 rounded-lg flex items-center justify-center flex-shrink-0">
                    <svg class="w-4 h-4 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"></path>
                    </svg>
                  </div>
                  <div class="flex-1">
                    <h4 class="text-sm font-bold text-yellow-900 mb-1">Razón de Devolución</h4>
                    <p class="text-sm text-yellow-800">{{ selectedReturn.reason || 'No especificada' }}</p>
                  </div>
                </div>
              </div>

              <!-- Sección: Información General (Grid 2 Columnas) -->
              <div class="bg-white border border-gray-200 rounded-xl p-4">
                <h4 class="text-sm font-bold text-gray-900 mb-3">Información de la Devolución</h4>
                
                <div class="grid grid-cols-2 gap-x-6 gap-y-3">
                  <div>
                    <p class="text-xs text-gray-500 mb-0.5">Cliente</p>
                    <p class="text-sm font-semibold text-gray-900">{{ selectedReturn.customer?.name || 'Cliente General' }}</p>
                  </div>
                  
                  <div>
                    <p class="text-xs text-gray-500 mb-0.5">Factura Original</p>
                    <p class="text-sm font-semibold text-gray-900">{{ selectedReturn.original_invoice?.number || 'N/A' }}</p>
                  </div>
                  
                  <div>
                    <p class="text-xs text-gray-500 mb-0.5">Método de Reembolso</p>
                    <p class="text-sm font-semibold text-gray-900">{{ getRefundMethodLabel(selectedReturn.refund_method) }}</p>
                  </div>
                  
                  <div>
                    <p class="text-xs text-gray-500 mb-0.5">Usuario</p>
                    <p class="text-sm font-semibold text-gray-900">{{ selectedReturn.user?.name || 'N/A' }}</p>
                  </div>
                  
                  <div>
                    <p class="text-xs text-gray-500 mb-0.5">Caja</p>
                    <p class="text-sm font-semibold text-gray-900">#{{ selectedReturn.cash_session?.id || 'N/A' }}</p>
                  </div>
                  
                  <div>
                    <p class="text-xs text-gray-500 mb-0.5">Fecha de Devolución</p>
                    <p class="text-sm font-semibold text-gray-900">{{ formatDate(selectedReturn.return_date) }}</p>
                  </div>
                </div>
              </div>

            </div>
          </div>
        </div>

      </div>

    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { apiCall } from '../services/api.js';

// Estado
const returns = ref([]);
const selectedReturn = ref(null);
const searchTerm = ref('');
const statusFilter = ref('');
const refundMethodFilter = ref('');
const dateFrom = ref('');
const dateTo = ref('');

// Cargar devoluciones
const loadReturns = async () => {
  try {
    const response = await apiCall('/returns');
    returns.value = response.data || [];
    
    // Seleccionar la primera automáticamente si hay datos
    if (returns.value.length > 0 && !selectedReturn.value) {
      selectedReturn.value = returns.value[0];
    }
  } catch (error) {
    console.error('Error al cargar devoluciones:', error);
  }
};

// Filtrado
const filteredReturns = computed(() => {
  let result = returns.value;
  
  if (searchTerm.value) {
    const search = searchTerm.value.toLowerCase();
    result = result.filter(r =>
      r.number?.toLowerCase().includes(search) ||
      r.customer?.name?.toLowerCase().includes(search)
    );
  }
  
  if (statusFilter.value) {
    result = result.filter(r => r.status === statusFilter.value);
  }
  
  if (refundMethodFilter.value) {
    result = result.filter(r => r.refund_method === refundMethodFilter.value);
  }
  
  return result;
});

// Estadísticas
const stats = computed(() => {
  const all = returns.value;
  const completed = all.filter(r => r.status === 'completed');
  const pending = all.filter(r => r.status === 'pending');
  
  return {
    totalReturns: all.length,
    totalRefunded: all.reduce((sum, r) => sum + parseFloat(r.total || 0), 0),
    completedCount: completed.length,
    completedAmount: completed.reduce((sum, r) => sum + parseFloat(r.total || 0), 0),
    pendingCount: pending.length,
    pendingAmount: pending.reduce((sum, r) => sum + parseFloat(r.total || 0), 0),
    totalItemsReturned: all.reduce((sum, r) => 
      sum + (r.return_items?.reduce((s, i) => s + parseInt(i.quantity || 0), 0) || 0), 0
    )
  };
});

// Acciones
const selectReturn = (returnItem) => {
  selectedReturn.value = returnItem;
};

const clearFilters = () => {
  searchTerm.value = '';
  statusFilter.value = '';
  refundMethodFilter.value = '';
  dateFrom.value = '';
  dateTo.value = '';
};

const exportData = () => {
  console.log('Exportando datos...');
};

const printReturn = () => {
  console.log('Imprimir devolución:', selectedReturn.value?.number);
};

const sendByEmail = () => {
  console.log('Enviar por email:', selectedReturn.value?.number);
};

// Formateo
const formatCurrency = (value) => {
  return parseFloat(value || 0).toFixed(2);
};

const formatDate = (date) => {
  if (!date) return 'N/A';
  return new Date(date).toLocaleDateString('es-ES');
};

const getStatusLabel = (status) => {
  const labels = {
    pending: 'Pendiente',
    completed: 'Completada',
    cancelled: 'Cancelada'
  };
  return labels[status] || status;
};

const getRefundMethodLabel = (method) => {
  const labels = {
    cash: 'Efectivo',
    card: 'Tarjeta',
    transfer: 'Transferencia',
    store_credit: 'Crédito en Tienda'
  };
  return labels[method] || method;
};

onMounted(() => {
  loadReturns();
});
</script>

<style scoped>
.animate-fade-in {
  animation: fadeIn 0.3s ease-in;
}

@keyframes fadeIn {
  from { opacity: 0; transform: translateY(10px); }
  to { opacity: 1; transform: translateY(0); }
}
</style>
