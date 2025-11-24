<template>
  <div class="min-h-screen font-sans" style="background-color: #F4F6F8;">
    <div class="p-4 lg:p-6 space-y-4 lg:space-y-5 animate-fade-in">
      
      <!-- Header Minimalista Enterprise con KPIs que ocupan todo el espacio -->
      <div class="flex items-center justify-between gap-4">
        
        <!-- Título -->
        <div class="flex-shrink-0">
          <h1 class="text-2xl font-bold" style="color: #0F172A; font-family: 'Inter', sans-serif;">Devoluciones</h1>
          <p class="text-xs mt-1" style="color: #94A3B8;">Control y seguimiento de devoluciones</p>
        </div>
        
        <!-- KPIs que ocupan todo el espacio disponible -->
        <div class="flex-1 flex items-center justify-start gap-4 px-6">
          
          <!-- KPI: Total Devuelto -->
          <div class="flex items-center gap-3 px-6 py-2.5 bg-white rounded-lg border" style="border-color: #E2E8F0;">
            <div class="w-9 h-9 rounded-lg flex items-center justify-center flex-shrink-0" style="background-color: #FEF2F2;">
              <svg class="w-5 h-5" style="color: #EF4444;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 15v-1a4 4 0 00-4-4H8m0 0l3 3m-3-3l3-3m9 14V5a2 2 0 00-2-2H6a2 2 0 00-2 2v16l4-2 4 2 4-2 4 2z"></path>
              </svg>
            </div>
            <div>
              <p class="text-[10px] font-medium uppercase tracking-wide" style="color: #94A3B8; font-family: 'Inter', sans-serif;">Total Devuelto</p>
              <p class="text-lg font-bold leading-tight mt-0.5" style="color: #0F172A; font-family: 'Inter', sans-serif;">${{ formatCurrency(stats.totalRefunded) }}</p>
            </div>
          </div>

          <!-- KPI: Completadas -->
          <div class="flex items-center gap-3 px-6 py-2.5 bg-white rounded-lg border" style="border-color: #E2E8F0;">
            <div class="w-9 h-9 rounded-lg flex items-center justify-center flex-shrink-0" style="background-color: #D1FAE5;">
              <svg class="w-5 h-5" style="color: #10B981;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
              </svg>
            </div>
            <div>
              <p class="text-[10px] font-medium uppercase tracking-wide" style="color: #94A3B8; font-family: 'Inter', sans-serif;">Completadas</p>
              <p class="text-lg font-bold leading-tight mt-0.5" style="color: #0F172A; font-family: 'Inter', sans-serif;">{{ stats.completedCount }}</p>
            </div>
          </div>

          <!-- KPI: Pendientes -->
          <div class="flex items-center gap-3 px-6 py-2.5 bg-white rounded-lg border" style="border-color: #E2E8F0;">
            <div class="w-9 h-9 rounded-lg flex items-center justify-center flex-shrink-0" style="background-color: #FEF3C7;">
              <svg class="w-5 h-5" style="color: #F59E0B;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
              </svg>
            </div>
            <div>
              <p class="text-[10px] font-medium uppercase tracking-wide" style="color: #94A3B8; font-family: 'Inter', sans-serif;">Pendientes</p>
              <p class="text-lg font-bold leading-tight mt-0.5" style="color: #0F172A; font-family: 'Inter', sans-serif;">{{ stats.pendingCount }}</p>
            </div>
          </div>

          <!-- KPI: Productos -->
          <div class="flex items-center gap-3 px-6 py-2.5 bg-white rounded-lg border" style="border-color: #E2E8F0;">
            <div class="w-9 h-9 rounded-lg flex items-center justify-center flex-shrink-0" style="background-color: #EEF2FF;">
              <svg class="w-5 h-5" style="color: #6366F1;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
              </svg>
            </div>
            <div>
              <p class="text-[10px] font-medium uppercase tracking-wide" style="color: #94A3B8; font-family: 'Inter', sans-serif;">Productos</p>
              <p class="text-lg font-bold leading-tight mt-0.5" style="color: #0F172A; font-family: 'Inter', sans-serif;">{{ stats.totalItemsReturned }}</p>
            </div>
          </div>
          
        </div>
        
        <!-- Botones de acción -->
        <div class="flex items-center gap-2 flex-shrink-0">
          <button
            @click="loadReturns"
            class="p-2 bg-white hover:bg-gray-50 rounded-lg border transition-all"
            style="border-color: #E2E8F0; color: #64748B;"
            title="Actualizar">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
            </svg>
          </button>
          
          <button
            @click="exportData"
            class="px-4 py-2 text-white text-sm font-semibold rounded-lg transition-all flex items-center gap-2"
            style="background-color: #0F172A; font-family: 'Inter', sans-serif; box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            Exportar
          </button>
        </div>
        
      </div>

      <!-- Master-Detail Layout Enterprise: 30/70 -->
      <div class="grid grid-cols-1 lg:grid-cols-10 gap-4" style="height: calc(100vh - 140px); min-height: 650px;">
        
        <!-- PANEL IZQUIERDO: Lista Minimalista (30%) -->
        <div class="lg:col-span-3 bg-white rounded-xl overflow-hidden flex flex-col" style="border: 1px solid #E2E8F0;">
          
          <!-- Header minimalista con búsqueda -->
          <div class="p-3" style="border-bottom: 1px solid #E2E8F0;">
            <!-- Búsqueda limpia -->
            <div class="relative mb-3">
              <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4" style="color: #94A3B8;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
              </svg>
              <input
                v-model="searchTerm"
                type="text"
                placeholder="Buscar..."
                class="w-full pl-9 pr-3 py-2 text-sm rounded-lg focus:outline-none focus:ring-2"
                style="border: 1px solid #E2E8F0; color: #0F172A; font-family: 'Inter', sans-serif;">
            </div>
            
            <!-- Filtro de estado -->
            <select
              v-model="statusFilter"
              class="w-full px-2 py-1.5 text-xs rounded-lg focus:outline-none focus:ring-2"
              style="border: 1px solid #E2E8F0; color: #64748B; font-family: 'Inter', sans-serif;">
              <option value="">Todos los estados</option>
              <option value="pending">Pendientes</option>
              <option value="completed">Completadas</option>
              <option value="cancelled">Canceladas</option>
            </select>
          </div>
          
          <!-- Lista minimalista -->
          <div class="flex-1 overflow-y-auto">
            
            <div
              v-for="returnItem in filteredReturns"
              :key="returnItem.id"
              @click="selectReturn(returnItem)"
              class="px-4 py-4 cursor-pointer transition-all"
              :style="{
                borderBottom: '1px solid #F1F5F9',
                backgroundColor: selectedReturn?.id === returnItem.id ? '#FEF2F2' : '#FFFFFF',
                borderLeft: selectedReturn?.id === returnItem.id ? '4px solid #EF4444' : '4px solid transparent'
              }">
              
              <!-- Contenido ultra minimalista -->
              <div class="flex items-start justify-between mb-1">
                <div class="flex-1 min-w-0">
                  <p class="text-sm font-semibold truncate" style="color: #0F172A; font-family: 'Inter', sans-serif;">
                    {{ returnItem.number }}
                  </p>
                  <p class="text-xs truncate mt-0.5" style="color: #64748B;">{{ returnItem.customer?.name || 'Cliente General' }}</p>
                </div>
                
                <!-- Pill pequeña de estado -->
                <span
                  class="px-2 py-0.5 rounded-full text-[10px] font-semibold flex-shrink-0 ml-2"
                  :style="{
                    backgroundColor: returnItem.status === 'completed' ? '#D1FAE5' : returnItem.status === 'pending' ? '#FEF3C7' : '#FEE2E2',
                    color: returnItem.status === 'completed' ? '#065F46' : returnItem.status === 'pending' ? '#92400E' : '#991B1B'
                  }">
                  {{ returnItem.status === 'completed' ? 'C' : returnItem.status === 'pending' ? 'P' : 'X' }}
                </span>
              </div>
              
              <!-- Info secundaria -->
              <div class="flex items-center justify-between text-[11px] mt-1">
                <span style="color: #94A3B8;">{{ formatDate(returnItem.return_date) }}</span>
                <span class="font-semibold" style="color: #EF4444;">${{ formatCurrency(returnItem.total) }}</span>
              </div>
            </div>
            
            <!-- Estado vacío -->
            <div v-if="filteredReturns.length === 0" class="flex flex-col items-center justify-center py-8 text-center">
              <svg class="w-8 h-8 text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 15v-1a4 4 0 00-4-4H8m0 0l3 3m-3-3l3-3m9 14V5a2 2 0 00-2-2H6a2 2 0 00-2 2v16l4-2 4 2 4-2 4 2z"></path>
              </svg>
              <p class="text-xs font-semibold text-gray-600">Sin resultados</p>
              <p class="text-xs text-gray-500 mt-1">Intenta con otros filtros</p>
            </div>
          </div>
        </div>

        <!-- PANEL DERECHO: Detalle Amplio (70%) -->
        <div class="lg:col-span-7 bg-white rounded-xl overflow-hidden flex flex-col" style="border: 1px solid #E2E8F0;">
          
          <!-- Estado: No seleccionado -->
          <div v-if="!selectedReturn" class="flex-1 flex flex-col items-center justify-center p-8 text-center">
            <svg class="w-16 h-16 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 15v-1a4 4 0 00-4-4H8m0 0l3 3m-3-3l3-3m9 14V5a2 2 0 00-2-2H6a2 2 0 00-2 2v16l4-2 4 2 4-2 4 2z"></path>
            </svg>
            <h3 class="text-base font-bold text-gray-700 mb-1">Selecciona una devolución</h3>
            <p class="text-xs text-gray-500">Haz clic en cualquier devolución de la lista</p>
          </div>

          <!-- Estado: Devolución seleccionada -->
          <div v-else class="flex-1 flex flex-col overflow-hidden">
            
            <!-- Header del detalle con acciones contextuales -->
            <div class="p-4" style="border-bottom: 1px solid #E2E8F0;">
              <div class="flex items-start justify-between">
                <div class="flex-1">
                  <div class="flex items-center gap-3 mb-2">
                    <h2 class="text-2xl font-bold" style="color: #0F172A; font-family: 'Inter', sans-serif;">
                      {{ selectedReturn.number }}
                    </h2>
                    <span
                      class="px-2.5 py-1 rounded-full text-xs font-semibold"
                      :style="{
                        backgroundColor: selectedReturn.status === 'completed' ? '#D1FAE5' : selectedReturn.status === 'pending' ? '#FEF3C7' : '#FEE2E2',
                        color: selectedReturn.status === 'completed' ? '#065F46' : selectedReturn.status === 'pending' ? '#92400E' : '#991B1B'
                      }">
                      {{ getStatusLabel(selectedReturn.status) }}
                    </span>
                  </div>
                  <div class="flex items-center gap-4 text-sm" style="color: #64748B;">
                    <span class="flex items-center gap-1.5">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                      </svg>
                      {{ formatDate(selectedReturn.return_date) }}
                    </span>
                    <span class="flex items-center gap-1.5">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                      </svg>
                      {{ selectedReturn.customer?.name || 'Cliente General' }}
                    </span>
                  </div>
                </div>
                
                <!-- Acciones contextuales con texto -->
                <div class="flex items-center gap-2">
                  <button
                    @click="printReturn"
                    class="px-3 py-2 rounded-lg transition-all flex items-center gap-2 text-sm font-medium"
                    style="color: #64748B; background-color: #F8FAFC; border: 1px solid #E2E8F0;">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                    </svg>
                    Imprimir
                  </button>
                  
                  <button
                    @click="sendByEmail"
                    class="px-3 py-2 rounded-lg transition-all flex items-center gap-2 text-sm font-medium"
                    style="color: #64748B; background-color: #F8FAFC; border: 1px solid #E2E8F0;">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                    </svg>
                    Email
                  </button>
                </div>
              </div>
            </div>

            <!-- Contenido scrollable - Documento digital limpio -->
            <div class="flex-1 overflow-y-auto p-4" style="background-color: #F9FAFB;">
              
              <!-- Sección: Información General (PRIMERO) -->
              <div class="bg-white rounded-lg border p-4 mb-4" style="border-color: #E2E8F0;">
                <h4 class="text-sm font-bold mb-4" style="color: #0F172A; font-family: 'Inter', sans-serif;">Información General</h4>
                
                <div class="grid grid-cols-2 gap-4">
                  <div>
                    <p class="text-xs font-medium mb-1" style="color: #94A3B8; font-family: 'Inter', sans-serif;">Cliente</p>
                    <p class="text-sm font-semibold" style="color: #0F172A; font-family: 'Inter', sans-serif;">{{ selectedReturn.customer?.name || 'Cliente General' }}</p>
                  </div>
                  
                  <div>
                    <p class="text-xs font-medium mb-1" style="color: #94A3B8; font-family: 'Inter', sans-serif;">Factura Original</p>
                    <p class="text-sm font-semibold" style="color: #0F172A; font-family: 'Inter', sans-serif;">{{ selectedReturn.original_invoice?.number || 'N/A' }}</p>
                  </div>
                  
                  <div>
                    <p class="text-xs font-medium mb-1" style="color: #94A3B8; font-family: 'Inter', sans-serif;">Método de Reembolso</p>
                    <p class="text-sm font-semibold" style="color: #0F172A; font-family: 'Inter', sans-serif;">{{ getRefundMethodLabel(selectedReturn.refund_method) }}</p>
                  </div>
                  
                  <div>
                    <p class="text-xs font-medium mb-1" style="color: #94A3B8; font-family: 'Inter', sans-serif;">Usuario</p>
                    <p class="text-sm font-semibold" style="color: #0F172A; font-family: 'Inter', sans-serif;">{{ selectedReturn.user?.name || 'N/A' }}</p>
                  </div>
                  
                  <div>
                    <p class="text-xs font-medium mb-1" style="color: #94A3B8; font-family: 'Inter', sans-serif;">Caja</p>
                    <p class="text-sm font-semibold" style="color: #0F172A; font-family: 'Inter', sans-serif;">#{{ selectedReturn.cash_session?.id || 'N/A' }}</p>
                  </div>
                  
                  <div>
                    <p class="text-xs font-medium mb-1" style="color: #94A3B8; font-family: 'Inter', sans-serif;">Fecha</p>
                    <p class="text-sm font-semibold" style="color: #0F172A; font-family: 'Inter', sans-serif;">{{ formatDate(selectedReturn.created_at) }}</p>
                  </div>
                </div>
              </div>

              <!-- Sección: Productos Devueltos -->
              <div class="bg-white rounded-lg border mb-4" style="border-color: #E2E8F0;">
                <div class="border-b px-4 py-3" style="background-color: #F8FAFC; border-color: #E2E8F0;">
                  <h4 class="text-sm font-bold" style="color: #0F172A; font-family: 'Inter', sans-serif;">Productos Devueltos</h4>
                </div>
                
                <div class="overflow-x-auto">
                  <table class="min-w-full divide-y" style="border-color: #E2E8F0;">
                    <thead style="background-color: #F8FAFC;">
                      <tr>
                        <th class="text-left text-xs font-bold uppercase tracking-wide px-4 py-3" style="color: #64748B;">Producto</th>
                        <th class="text-center text-xs font-bold uppercase tracking-wide px-4 py-3" style="color: #64748B;">Cant.</th>
                        <th class="text-right text-xs font-bold uppercase tracking-wide px-4 py-3" style="color: #64748B;">P. Unit.</th>
                        <th class="text-right text-xs font-bold uppercase tracking-wide px-4 py-3" style="color: #64748B;">Subtotal</th>
                      </tr>
                    </thead>
                    <tbody class="bg-white divide-y" style="border-color: #E2E8F0;">
                      <tr v-for="item in selectedReturn.return_items" :key="item.id" class="hover:bg-gray-50 transition-colors">
                        <td class="px-4 py-3">
                          <p class="text-sm font-semibold" style="color: #0F172A; font-family: 'Inter', sans-serif;">{{ item.product?.name || 'N/A' }}</p>
                          <p class="text-xs" style="color: #94A3B8; font-family: 'Inter', sans-serif;">{{ item.product?.code || 'N/A' }}</p>
                        </td>
                        <td class="text-center px-4 py-3">
                          <span class="text-sm font-semibold" style="color: #0F172A; font-family: 'Inter', sans-serif;">{{ item.quantity }}</span>
                        </td>
                        <td class="text-right px-4 py-3">
                          <span class="text-sm" style="color: #64748B; font-family: 'Inter', sans-serif;">${{ formatCurrency(item.unit_price) }}</span>
                        </td>
                        <td class="text-right px-4 py-3">
                          <span class="text-sm font-bold" style="color: #0F172A; font-family: 'Inter', sans-serif;">${{ formatCurrency(item.subtotal) }}</span>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                  
                  <!-- Total -->
                  <div class="border-t px-4 py-4" style="background-color: #F8FAFC; border-color: #E2E8F0;">
                    <div class="flex justify-end">
                      <div class="text-right">
                        <p class="text-xs font-medium" style="color: #94A3B8; font-family: 'Inter', sans-serif;">Total Devuelto</p>
                        <p class="text-2xl font-bold text-red-600 mt-1">${{ formatCurrency(selectedReturn.total) }}</p>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Sección: Razón de la Devolución -->
              <div class="bg-white rounded-lg border border-l-4 border-yellow-400 p-4" style="border-color: #E2E8F0; border-left-color: #F59E0B;">
                <div class="flex items-start gap-3">
                  <div class="w-9 h-9 rounded-lg flex items-center justify-center flex-shrink-0" style="background-color: #FEF3C7;">
                    <svg class="w-5 h-5" style="color: #F59E0B;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"></path>
                    </svg>
                  </div>
                  <div class="flex-1">
                    <h4 class="text-sm font-bold mb-2" style="color: #0F172A; font-family: 'Inter', sans-serif;">Razón de la Devolución</h4>
                    <p class="text-sm" style="color: #64748B; font-family: 'Inter', sans-serif;">{{ selectedReturn.reason || 'No especificada' }}</p>
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
import { ref, computed, onMounted, watch } from 'vue'
import { useToast } from '../composables/useToast.js'
import returnsService from '../services/returnsService.js'

// Composables
const { showToast } = useToast()

// Estado reactivo
const loading = ref(false)
const returns = ref([])
const selectedReturn = ref(null)
const searchTerm = ref('')
const statusFilter = ref('')
const refundMethodFilter = ref('')
const dateFrom = ref('')
const dateTo = ref('')

// Métodos
const loadReturns = async () => {
  loading.value = true
  try {
    const params = {
      per_page: 1000
    }

    const response = await returnsService.getReturns(params)
    
    // Manejar tanto estructura paginada como directa
    let returnsList = []
    if (response.data && response.data.data) {
      returnsList = response.data.data
    } else if (response.data && Array.isArray(response.data)) {
      returnsList = response.data
    } else if (response.success && response.data) {
      if (Array.isArray(response.data.data)) {
        returnsList = response.data.data
      } else if (Array.isArray(response.data)) {
        returnsList = response.data
      }
    }
    
    // Procesar cada devolución para parsear items JSON
    returns.value = returnsList.map(returnItem => {
      // Si items es string, parsear a JSON
      if (returnItem.items && typeof returnItem.items === 'string') {
        try {
          returnItem.items = JSON.parse(returnItem.items)
        } catch (e) {
          console.error('Error parsing items JSON:', e)
          returnItem.items = []
        }
      }
      
      // Si items no es array, convertirlo
      if (!Array.isArray(returnItem.items)) {
        returnItem.items = []
      }
      
      // Crear return_items a partir de items para compatibilidad con el template
      returnItem.return_items = returnItem.items
      
      return returnItem
    })
    
    // NO auto-seleccionar - el usuario debe elegir manualmente
    // selectedReturn.value permanece null hasta que el usuario haga clic
    
    showToast('Devoluciones cargadas exitosamente', 'success')
  } catch (error) {
    console.error('Error loading returns:', error)
    showToast(error.message || 'Error al cargar devoluciones', 'error')
    returns.value = []
  } finally {
    loading.value = false
  }
}

// Filtrado
const filteredReturns = computed(() => {
  let filtered = returns.value

  // Filtro de búsqueda
  if (searchTerm.value) {
    const term = searchTerm.value.toLowerCase()
    filtered = filtered.filter(returnItem => 
      (returnItem.number || '').toLowerCase().includes(term) ||
      (returnItem.customer?.name || '').toLowerCase().includes(term) ||
      (returnItem.customer?.email || '').toLowerCase().includes(term) ||
      (returnItem.original_invoice?.number || '').toLowerCase().includes(term)
    )
  }

  // Filtro por estado
  if (statusFilter.value) {
    filtered = filtered.filter(returnItem => returnItem.status === statusFilter.value)
  }

  // Filtro por método de reembolso
  if (refundMethodFilter.value) {
    filtered = filtered.filter(returnItem => returnItem.refund_method === refundMethodFilter.value)
  }

  // Filtro por fechas
  if (dateFrom.value) {
    filtered = filtered.filter(returnItem => returnItem.return_date >= dateFrom.value)
  }
  
  if (dateTo.value) {
    filtered = filtered.filter(returnItem => returnItem.return_date <= dateTo.value)
  }

  return filtered
})

// Estadísticas
const stats = computed(() => {
  const all = returns.value
  const completed = all.filter(r => r.status === 'completed')
  const pending = all.filter(r => r.status === 'pending')
  
  return {
    totalReturns: all.length,
    totalRefunded: all.reduce((sum, r) => sum + parseFloat(r.total || 0), 0),
    completedCount: completed.length,
    completedAmount: completed.reduce((sum, r) => sum + parseFloat(r.total || 0), 0),
    pendingCount: pending.length,
    pendingAmount: pending.reduce((sum, r) => sum + parseFloat(r.total || 0), 0),
    totalItemsReturned: all.reduce((sum, r) => 
      sum + ((r.return_items || r.items || []).reduce((s, i) => s + parseInt(i.quantity || 0), 0)), 0
    )
  }
})

// Acciones
const selectReturn = async (returnItem) => {
  try {
    loading.value = true
    const response = await returnsService.getReturnDetails(returnItem.id)
    
    // El backend ya devuelve return_items con productos incluidos
    selectedReturn.value = response.data
  } catch (error) {
    console.error('Error loading return details:', error)
    showToast(error.message || 'Error al cargar detalles', 'error')
  } finally {
    loading.value = false
  }
}

const clearFilters = () => {
  searchTerm.value = ''
  statusFilter.value = ''
  refundMethodFilter.value = ''
  dateFrom.value = ''
  dateTo.value = ''
}

const exportData = () => {
  const csvContent = [
    ['Número', 'Factura Original', 'Cliente', 'Fecha', 'Total', 'Estado', 'Método', 'Motivo'].join(','),
    ...filteredReturns.value.map(returnItem => [
      returnItem.number,
      returnItem.original_invoice?.number || 'N/A',
      returnItem.customer?.name || 'Cliente General',
      formatDate(returnItem.return_date),
      returnItem.total,
      getStatusLabel(returnItem.status),
      getRefundMethodLabel(returnItem.refund_method),
      returnItem.reason || ''
    ].join(','))
  ].join('\n')

  const blob = new Blob([csvContent], { type: 'text/csv' })
  const url = window.URL.createObjectURL(blob)
  const a = document.createElement('a')
  a.href = url
  a.download = `devoluciones_${new Date().toISOString().slice(0, 10)}.csv`
  document.body.appendChild(a)
  a.click()
  document.body.removeChild(a)
  window.URL.revokeObjectURL(url)

  showToast('Reporte exportado exitosamente', 'success')
}

const printReturn = () => {
  if (!selectedReturn.value) return
  showToast('Función de impresión en desarrollo', 'info')
}

const sendByEmail = () => {
  if (!selectedReturn.value) return
  showToast('Función de envío por email en desarrollo', 'info')
}

// Formateo
const formatCurrency = (value) => {
  return new Intl.NumberFormat('es-CO').format(value)
}

const formatDate = (dateString) => {
  if (!dateString) return '-'
  
  try {
    // Manejar diferentes formatos de fecha
    let date
    
    if (typeof dateString === 'string') {
      // Si es formato YYYY-MM-DD, agregar hora local
      if (/^\d{4}-\d{2}-\d{2}$/.test(dateString)) {
        date = new Date(dateString + 'T12:00:00')
      } else {
        date = new Date(dateString)
      }
    } else {
      date = new Date(dateString)
    }
    
    // Verificar si la fecha es válida
    if (isNaN(date.getTime())) return '-'
    
    const day = date.getDate()
    const month = date.getMonth() + 1
    const year = date.getFullYear()
    
    return `${day.toString().padStart(2, '0')}/${month.toString().padStart(2, '0')}/${year}`
  } catch (error) {
    console.error('Error formatting date:', error, dateString)
    return '-'
  }
}

const getStatusLabel = (status) => {
  const labels = {
    pending: 'Pendiente',
    completed: 'Completada',
    cancelled: 'Cancelada'
  }
  return labels[status] || status
}

const getRefundMethodLabel = (method) => {
  const labels = {
    cash: 'Efectivo',
    card: 'Tarjeta',
    transfer: 'Transferencia',
    store_credit: 'Crédito'
  }
  return labels[method] || method
}

// Watchers
watch([searchTerm, statusFilter, refundMethodFilter, dateFrom, dateTo], () => {
  // NO auto-seleccionar - dejar que el usuario elija manualmente
  selectedReturn.value = null
}, { deep: true })

onMounted(() => {
  loadReturns()
})
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
