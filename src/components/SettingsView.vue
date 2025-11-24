<template>
  <div class="min-h-screen bg-gray-100 font-sans">
    <div class="p-4 lg:p-6 space-y-6 animate-fade-in">
      
      <!-- Header Elegante Empresarial -->
      <div class="flex items-center justify-between pb-4 border-b border-gray-300">
        <div class="flex items-center space-x-4">
          <div class="w-12 h-12 bg-black rounded-xl flex items-center justify-center shadow-lg">
            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
          </div>
          <div>
            <h1 class="text-2xl font-bold text-gray-900">Configuración del Sistema</h1>
            <p class="text-sm text-gray-600">Gestión y parametrización empresarial</p>
          </div>
        </div>
        
        <div class="flex items-center space-x-3">
          <button @click="resetToDefaults" 
                  class="px-4 py-2 bg-slate-50 hover:bg-slate-100 text-slate-600 text-sm font-semibold rounded-xl border border-slate-200 shadow-sm transition-all duration-200 flex items-center space-x-2 hover:shadow-md hover:border-slate-300">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
            </svg>
            <span>Restaurar</span>
          </button>
          <button @click="saveAllSettings" :disabled="loading"
                  class="px-5 py-2 bg-gradient-to-r from-lime-400 to-green-400 hover:from-lime-500 hover:to-green-500 text-white text-sm font-bold rounded-xl shadow-lg transition-all duration-300 transform hover:scale-105 flex items-center space-x-2">
            <svg v-if="!loading" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/>
            </svg>
            <div v-else class="w-5 h-5 border-2 border-white border-t-transparent rounded-full animate-spin"></div>
            <span>{{ loading ? 'Guardando...' : 'Guardar Todo' }}</span>
          </button>
        </div>
      </div>

      <!-- Métricas de Configuración -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white rounded-2xl p-5 border border-gray-300 hover:border-gray-400 transition-all duration-200 hover:shadow-lg">
          <div class="flex items-center space-x-4">
            <div class="w-12 h-12 bg-gray-100 rounded-xl flex items-center justify-center flex-shrink-0">
              <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
              </svg>
            </div>
            <div class="flex-1 min-w-0">
              <div class="flex items-center justify-between mb-2">
                <h3 class="text-sm font-semibold text-gray-700">Empresa</h3>
                <span class="text-xs font-medium text-blue-700 bg-blue-50 px-2 py-1 rounded-lg border border-blue-200">
                  Configurado
                </span>
              </div>
              <p class="text-2xl font-bold text-gray-900 mb-1">{{ systemSettings.company_name || 'Sin configurar' }}</p>
              <p class="text-sm text-gray-500">Información corporativa</p>
            </div>
          </div>
        </div>

        <div class="bg-white rounded-2xl p-5 border border-gray-300 hover:border-gray-400 transition-all duration-200 hover:shadow-lg">
          <div class="flex items-center space-x-4">
            <div class="w-12 h-12 bg-gray-100 rounded-xl flex items-center justify-center flex-shrink-0">
              <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
              </svg>
            </div>
            <div class="flex-1 min-w-0">
              <div class="flex items-center justify-between mb-2">
                <h3 class="text-sm font-semibold text-gray-700">IVA/Impuestos</h3>
                <span :class="[
                  'text-xs font-medium px-2 py-1 rounded-lg border',
                  systemSettings.iva_enabled 
                    ? 'text-green-700 bg-green-50 border-green-200' 
                    : 'text-red-700 bg-red-50 border-red-200'
                ]">
                  {{ systemSettings.iva_enabled ? 'Activo' : 'Inactivo' }}
                </span>
              </div>
              <p class="text-2xl font-bold text-gray-900 mb-1">{{ systemSettings.iva_percentage }}%</p>
              <p class="text-sm text-gray-500">{{ systemSettings.iva_display_name || 'IVA' }}</p>
            </div>
          </div>
        </div>

        <div class="bg-white rounded-2xl p-5 border border-gray-300 hover:border-gray-400 transition-all duration-200 hover:shadow-lg">
          <div class="flex items-center space-x-4">
            <div class="w-12 h-12 bg-gray-100 rounded-xl flex items-center justify-center flex-shrink-0">
              <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
              </svg>
            </div>
            <div class="flex-1 min-w-0">
              <div class="flex items-center justify-between mb-2">
                <h3 class="text-sm font-semibold text-gray-700">Facturación</h3>
                <span class="text-xs font-medium text-blue-700 bg-blue-50 px-2 py-1 rounded-lg border border-blue-200">
                  Activo
                </span>
              </div>
              <p class="text-2xl font-bold text-gray-900 mb-1">{{ systemSettings.invoice_prefix }}{{ systemSettings.invoice_current_number }}</p>
              <p class="text-sm text-gray-500">Próximo número</p>
            </div>
          </div>
        </div>

        <div class="bg-white rounded-2xl p-5 border border-gray-300 hover:border-gray-400 transition-all duration-200 hover:shadow-lg">
          <div class="flex items-center space-x-4">
            <div class="w-12 h-12 bg-gray-100 rounded-xl flex items-center justify-center flex-shrink-0">
              <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
              </svg>
            </div>
            <div class="flex-1 min-w-0">
              <div class="flex items-center justify-between mb-2">
                <h3 class="text-sm font-semibold text-gray-700">Métodos de Pago</h3>
                <span class="text-xs font-medium text-green-700 bg-green-50 px-2 py-1 rounded-lg border border-green-200">
                  {{ paymentMethods.filter(m => m.active).length }}
                </span>
              </div>
              <p class="text-2xl font-bold text-gray-900 mb-1">{{ paymentMethods.length }}</p>
              <p class="text-sm text-gray-500">Total configurados</p>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Navegación Profesional - Submenu de Botones -->
      <div class="bg-white rounded-2xl p-3 border border-gray-300">
        <div class="flex items-center space-x-2 overflow-x-auto">
          <button v-for="section in sections" :key="section.id"
                  @click="activeSection = section.id"
                  :class="[
                    'px-4 py-2 rounded-xl text-sm font-semibold whitespace-nowrap transition-all duration-200 flex items-center space-x-2 min-w-0',
                    activeSection === section.id
                      ? 'bg-gradient-to-r from-sky-500 to-blue-500 text-white shadow-md'
                      : 'bg-gray-50 hover:bg-gray-100 text-gray-700 border border-gray-200 hover:border-gray-300 hover:shadow-sm'
                  ]">
            <span class="text-base">{{ section.icon }}</span>
            <span>{{ section.name }}</span>
            <div v-if="activeSection === section.id" class="w-1.5 h-1.5 bg-white rounded-full"></div>
          </button>
        </div>
      </div>
      
      <!-- Configuración General -->
      <div v-if="activeSection === 'general'" class="space-y-6">
        
        <!-- Información de la Empresa -->
        <div class="bg-white rounded-2xl border border-gray-300 overflow-hidden">
          <div class="bg-gray-50 border-b border-gray-200 px-5 py-4 flex items-center justify-between">
            <div>
              <h2 class="text-base font-bold text-gray-900">Información Empresarial</h2>
              <p class="text-xs text-gray-500 mt-0.5">Datos corporativos y contacto</p>
            </div>
          </div>
          <div class="p-5">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
              <div>
                <label class="block text-xs font-semibold text-gray-700 mb-2">Nombre de la Empresa</label>
                <input v-model="systemSettings.company_name" 
                       type="text" 
                       class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
              </div>
              
              <div>
                <label class="block text-xs font-semibold text-gray-700 mb-2">NIT/Documento</label>
                <input v-model="systemSettings.company_document" 
                       type="text" 
                       class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
              </div>
              
              <div>
                <label class="block text-xs font-semibold text-gray-700 mb-2">Teléfono</label>
                <input v-model="systemSettings.company_phone" 
                       type="text" 
                       class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
              </div>
              
              <div>
                <label class="block text-xs font-semibold text-gray-700 mb-2">Email</label>
                <input v-model="systemSettings.company_email" 
                       type="email" 
                       class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
              </div>
              
              <div class="md:col-span-2">
                <label class="block text-xs font-semibold text-gray-700 mb-2">Dirección</label>
                <input v-model="systemSettings.company_address" 
                       type="text" 
                       class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
              </div>
            </div>
          </div>
        </div>

        <!-- Vista Previa de Factura -->
        <div class="bg-white rounded-2xl border border-gray-300 overflow-hidden">
          <div class="bg-gray-50 border-b border-gray-200 px-5 py-4 flex items-center justify-between">
            <div>
              <h2 class="text-base font-bold text-gray-900">Vista Previa de Factura</h2>
              <p class="text-xs text-gray-500 mt-0.5">Cómo se verá tu factura con la configuración actual</p>
            </div>
          </div>
          <div class="p-5">
            <!-- Mini preview de la factura -->
            <div class="max-w-sm mx-auto bg-white border-2 border-dashed border-gray-300 rounded-lg p-4 text-center">
              <div class="text-center border-b border-gray-300 pb-2 mb-2">
                <h3 class="text-sm font-bold text-gray-900">{{ systemSettings.company_name || 'Mi Empresa' }}</h3>
                <p class="text-xs text-gray-600">{{ systemSettings.company_address || 'Dirección no configurada' }}</p>
                <p class="text-xs text-gray-600">{{ systemSettings.company_phone || 'Teléfono no configurado' }}</p>
                <p v-if="systemSettings.company_document" class="text-xs text-gray-600">NIT: {{ systemSettings.company_document }}</p>
              </div>
              
              <div class="text-xs mb-2 space-y-0.5">
                <div class="flex justify-between">
                  <span>Factura #:</span>
                  <span class="font-semibold">FV-000123</span>
                </div>
                <div class="flex justify-between">
                  <span>Fecha:</span>
                  <span>{{ new Date().toLocaleDateString('es-CO') }}</span>
                </div>
                <div class="flex justify-between">
                  <span>Cliente:</span>
                  <span>Cliente Demo</span>
                </div>
              </div>
              
              <div class="border-t border-gray-300 pt-2 mb-2">
                <div class="text-xs font-semibold mb-1">PRODUCTOS</div>
                <div class="text-xs">Producto ejemplo - $10,000</div>
              </div>
              
              <div class="border-t border-gray-300 pt-2 mb-2">
                <div class="flex justify-between text-xs font-bold">
                  <span>TOTAL:</span>
                  <span>$10,000</span>
                </div>
              </div>
              
              <div class="border-t border-dashed border-gray-300 pt-2">
                <p class="text-xs font-semibold">¡GRACIAS POR SU COMPRA!</p>
                <div class="text-xs text-gray-500 mt-1 space-y-0.5">
                  <p>Régimen Común - No responsable de IVA</p>
                  <p>Factura de venta Art. 617 del E.T.</p>
                </div>
                <div class="text-xs text-gray-500 mt-2 pt-1 border-t border-gray-300">
                  <p class="font-medium">{{ systemSettings.company_name || '105 POS' }}</p>
                  <p>Sistema de facturación</p>
                </div>
              </div>
            </div>
            <p class="text-xs text-gray-500 text-center mt-3">
              Esta es una vista previa de cómo se verá tu factura. Los cambios se reflejan automáticamente.
            </p>
          </div>
        </div>
        
        <!-- Configuración de IVA -->
        <div class="bg-white rounded-2xl border border-gray-300 overflow-hidden">
          <div class="bg-gray-50 border-b border-gray-200 px-5 py-4 flex items-center justify-between">
            <div>
              <h2 class="text-base font-bold text-gray-900">Configuración de IVA/Impuestos</h2>
              <p class="text-xs text-gray-500 mt-0.5">Gestión de impuestos y tasas aplicables</p>
            </div>
          </div>
          <div class="p-5">
            <div class="space-y-4">
              <div class="flex items-center justify-between p-3 bg-blue-50 rounded-lg border border-blue-100">
                <div>
                  <p class="text-sm font-semibold text-gray-900">Aplicar IVA en ventas</p>
                  <p class="text-xs text-gray-600">Incluir IVA automáticamente en todas las ventas</p>
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                  <input v-model="systemSettings.iva_enabled" type="checkbox" class="sr-only peer">
                  <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                </label>
              </div>
              
              <div v-if="systemSettings.iva_enabled" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                  <label class="block text-xs font-semibold text-gray-700 mb-2">Porcentaje de IVA (%)</label>
                  <input v-model.number="systemSettings.iva_percentage" 
                         type="number" 
                         min="0" 
                         max="100" 
                         step="0.1"
                         class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                </div>
                
                <div>
                  <label class="block text-xs font-semibold text-gray-700 mb-2">Nombre para mostrar</label>
                  <input v-model="systemSettings.iva_display_name" 
                         type="text" 
                         placeholder="IVA, IGV, Tax..."
                         class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Configuración de Facturas -->
        <div class="bg-white rounded-2xl border border-gray-300 overflow-hidden">
          <div class="bg-gray-50 border-b border-gray-200 px-5 py-4 flex items-center justify-between">
            <div>
              <h2 class="text-base font-bold text-gray-900">Configuración de Facturas</h2>
              <p class="text-xs text-gray-500 mt-0.5">Numeración y formato de documentos</p>
            </div>
          </div>
          <div class="p-5">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
              <div>
                <label class="block text-xs font-semibold text-gray-700 mb-2">Prefijo de Factura</label>
                <input v-model="systemSettings.invoice_prefix" 
                       type="text" 
                       placeholder="FACT-, INV-, etc."
                       class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
              </div>
              
              <div>
                <label class="block text-xs font-semibold text-gray-700 mb-2">Número inicial</label>
                <input v-model.number="systemSettings.invoice_number_start" 
                       type="number" 
                       min="1"
                       class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
              </div>
            </div>
            
            <div class="mb-4">
              <label class="block text-xs font-semibold text-gray-700 mb-2">Mensaje de pie de página</label>
              <textarea v-model="systemSettings.invoice_footer_message" 
                        rows="2"
                        placeholder="Mensaje que aparece al final de las facturas..."
                        class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"></textarea>
            </div>
            
            <div class="p-3 bg-blue-50 rounded-lg border border-blue-100">
              <div class="flex items-center space-x-2 text-blue-700">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span class="text-sm font-semibold">Próximo número: {{ systemSettings.invoice_prefix }}{{ systemSettings.invoice_current_number }}</span>
              </div>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Configuración POS -->
      <div v-if="activeSection === 'pos'" class="space-y-6">
        
        <!-- Configuración de Ventas -->
        <div class="bg-white rounded-2xl border border-gray-300 overflow-hidden">
          <div class="bg-gray-50 border-b border-gray-200 px-5 py-4 flex items-center justify-between">
            <div>
              <h2 class="text-base font-bold text-gray-900">Configuración de Ventas POS</h2>
              <p class="text-xs text-gray-500 mt-0.5">Comportamiento y configuraciones del punto de venta</p>
            </div>
          </div>
          <div class="p-5">
            <div class="space-y-4">
              <div class="flex items-center justify-between p-3 bg-blue-50 rounded-lg border border-blue-100">
                <div>
                  <p class="text-sm font-semibold text-gray-900">Requerir cliente en ventas</p>
                  <p class="text-xs text-gray-600">Obligar a seleccionar cliente antes de vender</p>
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                  <input v-model="systemSettings.require_customer" type="checkbox" class="sr-only peer">
                  <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                </label>
              </div>

              <div class="flex items-center justify-between p-3 bg-yellow-50 rounded-lg border border-yellow-200">
                <div>
                  <p class="text-sm font-semibold text-gray-900">Requerir cliente en cotizaciones</p>
                  <p class="text-xs text-gray-600">Obligar a seleccionar cliente específico (no "Cliente General")</p>
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                  <input v-model="systemSettings.require_customer_quotations" type="checkbox" class="sr-only peer">
                  <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-yellow-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-yellow-600"></div>
                </label>
              </div>

              <div class="flex items-center justify-between p-3 bg-green-50 rounded-lg border border-green-100">
                <div>
                  <p class="text-sm font-semibold text-gray-900">Mostrar imágenes de productos</p>
                  <p class="text-xs text-gray-600">Mostrar fotos en el POS para mejor identificación</p>
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                  <input v-model="systemSettings.show_product_images" type="checkbox" class="sr-only peer">
                  <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-green-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-green-600"></div>
                </label>
              </div>

              <div class="flex items-center justify-between p-3 bg-red-50 rounded-lg border border-red-100">
                <div>
                  <p class="text-sm font-semibold text-gray-900">Alertas de stock bajo</p>
                  <p class="text-xs text-gray-600">Notificar cuando productos tengan poco inventario</p>
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                  <input v-model="systemSettings.low_stock_alerts" type="checkbox" class="sr-only peer">
                  <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-red-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-red-600"></div>
                </label>
              </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-6">
              <div>
                <label class="block text-xs font-semibold text-gray-700 mb-2">Productos por página</label>
                <select v-model.number="systemSettings.products_per_page" 
                        class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white transition-all">
                  <option :value="8">8 productos</option>
                  <option :value="12">12 productos</option>
                  <option :value="16">16 productos</option>
                  <option :value="20">20 productos</option>
                </select>
              </div>
              
              <div>
                <label class="block text-xs font-semibold text-gray-700 mb-2">Umbral de stock bajo</label>
                <input v-model.number="systemSettings.low_stock_threshold" 
                       type="number" 
                       min="1" 
                       max="100"
                       class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Nueva Sección: Descuentos y Promociones -->
      <div v-if="activeSection === 'discounts'" class="space-y-6">
        
        <!-- Header con botón de agregar -->
        <div class="bg-white rounded-2xl border border-gray-300 overflow-hidden">
          <div class="bg-gray-50 border-b border-gray-200 px-5 py-4 flex items-center justify-between">
            <div>
              <h2 class="text-base font-bold text-gray-900">Descuentos y Promociones</h2>
              <p class="text-xs text-gray-500 mt-0.5">Gestión de códigos promocionales y descuentos automáticos</p>
            </div>
            <button @click="showAddDiscountModal = true" 
                    class="px-4 py-2 bg-gradient-to-r from-sky-500 to-blue-500 hover:from-sky-600 hover:to-blue-600 text-white rounded-xl text-sm font-semibold transition-all duration-200 flex items-center space-x-2">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
              </svg>
              <span>Nuevo Descuento</span>
            </button>
          </div>
          <div class="p-5">
            <!-- Configuración global de descuentos -->
            <div class="space-y-4 mb-6">
              <div class="flex items-center justify-between p-3 bg-green-50 rounded-lg border border-green-100">
                <div>
                  <p class="text-sm font-semibold text-gray-900">Habilitar descuentos</p>
                  <p class="text-xs text-gray-600">Permitir aplicar descuentos en el POS</p>
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                  <input v-model="systemSettings.discounts_enabled" type="checkbox" class="sr-only peer">
                  <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-green-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-green-600"></div>
                </label>
              </div>

              <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                <div class="flex items-center justify-between p-3 bg-blue-50 rounded-lg border border-blue-100">
                  <div>
                    <p class="text-sm font-semibold text-gray-900">Códigos promocionales</p>
                    <p class="text-xs text-gray-600">Usar códigos de descuento</p>
                  </div>
                  <label class="relative inline-flex items-center cursor-pointer">
                    <input v-model="systemSettings.promo_codes_enabled" type="checkbox" class="sr-only peer">
                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                  </label>
                </div>

                <div class="flex items-center justify-between p-3 bg-amber-50 rounded-lg border border-amber-100">
                  <div>
                    <p class="text-sm font-semibold text-gray-900">Automáticos</p>
                    <p class="text-xs text-gray-600">Aplicar sin código</p>
                  </div>
                  <label class="relative inline-flex items-center cursor-pointer">
                    <input v-model="systemSettings.auto_apply_discounts" type="checkbox" class="sr-only peer">
                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-amber-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-amber-600"></div>
                  </label>
                </div>

                <div class="flex items-center justify-between p-3 bg-purple-50 rounded-lg border border-purple-100">
                  <div>
                    <p class="text-sm font-semibold text-gray-900">Por cliente</p>
                    <p class="text-xs text-gray-600">Específicos por cliente</p>
                  </div>
                  <label class="relative inline-flex items-center cursor-pointer">
                    <input v-model="systemSettings.customer_discounts_enabled" type="checkbox" class="sr-only peer">
                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-purple-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-purple-600"></div>
                  </label>
                </div>
              </div>
            </div>

            <!-- Lista de descuentos existentes -->
            <div v-if="discounts.length > 0" class="space-y-3">
              <h4 class="text-sm font-bold text-gray-700 mb-3">Descuentos Activos</h4>
              <div v-for="discount in discounts" :key="discount.id" 
                   class="flex items-center justify-between p-4 border border-gray-200 rounded-xl hover:bg-gray-50 transition-all">
                <div class="flex-1">
                  <div class="flex items-center space-x-3">
                    <span class="px-2 py-1 text-xs font-semibold rounded-full"
                          :class="discount.active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'">
                      {{ discount.active ? 'Activo' : 'Inactivo' }}
                    </span>
                    <span class="text-sm font-semibold text-gray-900">{{ discount.name }}</span>
                    <span class="px-2 py-1 text-xs bg-blue-100 text-blue-800 rounded-full font-mono">{{ discount.code }}</span>
                  </div>
                  <p class="text-xs text-gray-600 mt-1">{{ discount.description }}</p>
                  <div class="flex items-center space-x-4 mt-2 text-xs text-gray-500">
                    <span>{{ discount.type === 'percentage' ? discount.value + '%' : '$' + Number(discount.value).toLocaleString() }}</span>
                    <span v-if="discount.expires_at">Exp: {{ formatDate(discount.expires_at) }}</span>
                    <span v-if="discount.usage_limit">{{ discount.used_count }}/{{ discount.usage_limit }} usos</span>
                  </div>
                </div>
                <div class="flex items-center space-x-2">
                  <button @click="editDiscount(discount)" 
                          class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                  </button>
                  <button @click="deleteDiscount(discount.id)" 
                          class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                  </button>
                </div>
              </div>
            </div>
            
            <div v-else class="text-center py-8 text-gray-500">
              <svg class="w-16 h-16 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.99 1.99 0 013 12V7a4 4 0 014-4z"/>
              </svg>
              <p class="text-lg font-medium">No hay descuentos configurados</p>
              <p class="text-sm">Crea el primer descuento haciendo clic en "Nuevo Descuento"</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Nueva Sección: Métodos de Pago -->
      <div v-if="activeSection === 'payments'" class="space-y-6">
        
        <!-- Header con botón de agregar -->
        <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100">
          <div class="flex justify-between items-center mb-6">
            <h3 class="text-xl font-bold text-gray-800 flex items-center">
              💳 Métodos de Pago
            </h3>
            <button @click="showAddPaymentMethodModal = true" 
                    class="px-4 py-2 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white rounded-xl shadow-lg hover:shadow-xl transition-all duration-200 flex items-center space-x-2 font-medium">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
              </svg>
              <span>Nuevo Método</span>
            </button>
          </div>

          <!-- Lista de métodos de pago -->
          <div v-if="paymentMethods.length > 0" class="space-y-3">
            <div v-for="method in paymentMethods" :key="method.id" 
                 class="flex items-center justify-between p-4 border border-gray-200 rounded-xl hover:bg-gray-50 transition-all">
              <div class="flex items-center space-x-4">
                <span class="text-2xl">{{ method.icon || '💳' }}</span>
                <div class="flex-1">
                  <div class="flex items-center space-x-3">
                    <span class="font-semibold text-gray-900">{{ method.name }}</span>
                    <span class="px-2 py-1 text-xs font-semibold rounded-full"
                          :class="method.active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'">
                      {{ method.active ? 'Activo' : 'Inactivo' }}
                    </span>
                    <span v-if="method.type" class="px-2 py-1 text-xs bg-blue-100 text-blue-800 rounded-full">{{ method.type }}</span>
                  </div>
                  <p class="text-sm text-gray-600 mt-1">{{ method.description || 'Sin descripción' }}</p>
                  <div class="flex items-center space-x-4 mt-2 text-xs text-gray-500">
                    <span v-if="method.fee_percentage > 0">Comisión: {{ method.fee_percentage }}%</span>
                    <span v-if="method.fee_fixed > 0">Comisión fija: ${{ Number(method.fee_fixed).toLocaleString() }}</span>
                    <span v-if="method.requires_change">Permite cambio</span>
                    <span>Orden: {{ method.sort_order }}</span>
                  </div>
                </div>
              </div>
              <div class="flex items-center space-x-2">
                <button @click="togglePaymentMethodStatus(method)" 
                        class="p-2 hover:bg-gray-100 rounded-lg transition-all"
                        :class="method.active ? 'text-red-600' : 'text-green-600'">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path v-if="method.active" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                  </svg>
                </button>
                <button @click="editPaymentMethod(method)" 
                        class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-all">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                  </svg>
                </button>
                <button @click="deletePaymentMethod(method.id)" 
                        class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-all">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                  </svg>
                </button>
              </div>
            </div>
          </div>
          
          <div v-else class="text-center py-8 text-gray-500">
            <svg class="w-16 h-16 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
            </svg>
            <p class="text-lg font-medium">No hay métodos de pago configurados</p>
            <p class="text-sm">Agrega el primer método de pago haciendo clic en "Nuevo Método"</p>
          </div>
        </div>
      </div>

    </div>
  </div>

  <!-- Modal: Agregar/Editar Descuento -->
  <div v-if="showAddDiscountModal" class="fixed inset-0 bg-black/40 flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-4xl max-h-[85vh] overflow-hidden">
      <!-- Header Profesional Simple -->
      <div class="bg-white border-b border-gray-200 px-6 py-4 flex items-center justify-between">
        <div class="flex items-center space-x-3">
          <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center">
            <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
            </svg>
          </div>
          <div>
            <h3 class="text-lg font-bold text-gray-900">
              {{ editingDiscount ? 'Editar Descuento' : 'Nuevo Descuento' }}
            </h3>
            <p class="text-sm text-gray-500">Configuración de promociones y descuentos</p>
          </div>
        </div>
        <button @click="closeDiscountModal" class="text-gray-400 hover:text-gray-600 p-2 hover:bg-gray-100 rounded-lg transition-all">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
          </svg>
        </button>
      </div>

      <!-- Contenido en Grid Horizontal -->
      <div class="p-6">
        <form @submit.prevent="saveDiscount" class="space-y-6">
          
          <!-- Fila 1: Información Básica -->
          <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Nombre del descuento *</label>
              <input v-model="discountForm.name" type="text" required
                     placeholder="Descuento de Bienvenida"
                     class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>
            
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Código promocional</label>
              <div class="relative">
                <input v-model="discountForm.code" type="text"
                       placeholder="AUTO (se genera automático)"
                       class="w-full pr-10 px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                <button type="button" @click="generateCode"
                        class="absolute right-1 top-1/2 -translate-y-1/2 px-2 py-1 bg-gray-100 hover:bg-gray-200 text-gray-600 rounded-md transition-colors"
                        title="Generar código">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                  </svg>
                </button>
              </div>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Descripción</label>
              <input v-model="discountForm.description" type="text"
                     placeholder="Descripción opcional..."
                     class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>
          </div>

          <!-- Fila 2: Configuración del Descuento -->
          <div class="grid grid-cols-2 lg:grid-cols-5 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Tipo *</label>
              <select v-model="discountForm.type" required
                      class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white">
                <option value="percentage">Porcentaje (%)</option>
                <option value="fixed_amount">Monto Fijo ($)</option>
              </select>
            </div>
            
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">
                Valor {{ discountForm.type === 'percentage' ? '(%)' : '($)' }} *
              </label>
              <input v-model.number="discountForm.value" type="number" required min="0" step="0.01"
                     :max="discountForm.type === 'percentage' ? 100 : undefined"
                     :placeholder="discountForm.type === 'percentage' ? '10' : '5000'"
                     class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Monto mínimo ($)</label>
              <input v-model.number="discountForm.minimum_amount" type="number" min="0" step="0.01"
                     placeholder="0"
                     class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>
            
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Descuento máximo ($)</label>
              <input v-model.number="discountForm.maximum_discount" type="number" min="0" step="0.01"
                     placeholder="Ilimitado"
                     class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Límite de usos</label>
              <input v-model.number="discountForm.usage_limit" type="number" min="1"
                     placeholder="Ilimitado"
                     class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>
          </div>

          <!-- Fila 3: Vigencia -->
          <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Fecha de inicio</label>
              <input v-model="discountForm.starts_at" type="datetime-local"
                     class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>
            
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Fecha de expiración</label>
              <input v-model="discountForm.expires_at" type="datetime-local"
                     class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>
          </div>

          <!-- Fila 4: Opciones -->
          <div class="border-t pt-4">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
              <!-- Opciones Básicas -->
              <div class="space-y-3">
                <h4 class="text-sm font-semibold text-gray-700 mb-3">Configuración</h4>
                <div class="space-y-2">
                  <label class="flex items-center space-x-3 cursor-pointer">
                    <input v-model="discountForm.active" type="checkbox" 
                           class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                    <span class="text-sm text-gray-700">Descuento activo</span>
                  </label>
                  
                  <label class="flex items-center space-x-3 cursor-pointer">
                    <input v-model="discountForm.stackable" type="checkbox" 
                           class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                    <span class="text-sm text-gray-700">Acumulable con otros descuentos</span>
                  </label>
                </div>
              </div>
              
              <!-- Restricción de Usuario -->
              <div class="space-y-3">
                <h4 class="text-sm font-semibold text-gray-700 mb-3">Restricciones por Cliente</h4>
                <div class="bg-gray-50 border border-gray-200 rounded-lg p-3">
                  <label class="flex items-start space-x-3 cursor-pointer">
                    <input v-model="discountForm.allow_multiple_uses_per_user" type="checkbox" 
                           class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500 mt-0.5">
                    <div>
                      <span class="text-sm font-medium text-gray-900">Permitir múltiples usos por cliente</span>
                      <p class="text-xs text-gray-600 mt-1">
                        Si no está marcado, cada cliente solo podrá usar este descuento una vez
                      </p>
                    </div>
                  </label>
                </div>
              </div>
            </div>
          </div>
        </form>
      </div>

      <!-- Footer -->
      <div class="bg-gray-50 border-t px-6 py-3 flex justify-between items-center">
        <span class="text-xs text-gray-500">* Campos obligatorios</span>
        
        <div class="flex space-x-3">
          <button type="button" @click="closeDiscountModal" 
                  class="px-4 py-2 text-sm font-medium text-gray-600 border border-gray-300 rounded-lg hover:bg-gray-100 transition-colors">
            Cancelar
          </button>
          <button type="submit" @click="saveDiscount" :disabled="loading"
                  class="px-4 py-2 text-sm font-medium bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors flex items-center space-x-2">
            <svg v-if="loading" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="m4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span>{{ loading ? 'Guardando...' : (editingDiscount ? 'Actualizar' : 'Crear') }}</span>
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axiosInstance from '../services/apiClient'

// Props para evitar warnings de Vue
const props = defineProps({
  moduleName: String,
  companyInfo: Object,
  systemSettings: Object
})

// Emits para eventos
defineEmits(['navigate', 'changeModule'])

// Sistema de notificaciones simple sin Vuex
const showNotification = (message, type = 'success') => {
  console.log(`[${type.toUpperCase()}] ${message}`)
  // Aquí podrías usar toast notifications o alertas simples
  if (type === 'success') {
    alert(`✅ ${message}`)
  } else if (type === 'error') {
    alert(`❌ ${message}`)
  }
}

// Estado reactivo
const activeSection = ref('general')
const loading = ref(false)

// Modales
const showAddDiscountModal = ref(false)
const showAddPaymentMethodModal = ref(false)

// Estados de edición
const editingDiscount = ref(null)
const editingPaymentMethod = ref(null)

// Secciones de configuración
const sections = ref([
  { id: 'general', name: 'General', icon: '🏢' },
  { id: 'pos', name: 'POS', icon: '🛒' },
  { id: 'discounts', name: 'Descuentos', icon: '🎯' },
  { id: 'payments', name: 'Pagos', icon: '💳' }
])

// Configuraciones del sistema desde API
const systemSettings = ref({
  company_name: 'Mi Empresa',
  company_document: '',
  company_phone: '',
  company_email: '',
  company_address: '',
  company_logo: null,
  iva_enabled: true,
  iva_percentage: 19,
  iva_display_name: 'IVA',
  invoice_prefix: 'FACT-',
  invoice_number_start: 1,
  invoice_current_number: 1,
  invoice_footer_message: '',
  require_customer: false,
  require_customer_quotations: false,
  discounts_enabled: true,
  customer_discounts_enabled: true,
  promo_codes_enabled: true,
  auto_apply_discounts: true,
  show_product_images: true,
  products_per_page: 12,
  low_stock_alerts: true,
  low_stock_threshold: 5
})

// Descuentos y métodos de pago
const discounts = ref([])
const paymentMethods = ref([])

// Formularios
const discountForm = ref({
  name: '',
  description: '',
  code: '',
  type: 'percentage',
  value: 0,
  applies_to: 'all_products',
  applicable_items: null,
  minimum_amount: null,
  maximum_discount: null,
  usage_limit: null,
  starts_at: '',
  expires_at: '',
  active: true,
  stackable: false,
  allow_multiple_uses_per_user: false
})

// Métodos
const loadSystemSettings = async () => {
  try {
    const response = await axiosInstance.get('/system-settings')
    if (response.data.success) {
      Object.assign(systemSettings.value, response.data.data)
    }
  } catch (error) {
    console.error('Error al cargar configuración:', error)
  }
}

const loadDiscounts = async () => {
  try {
    const response = await axiosInstance.get('/discounts')
    if (response.data.success) {
      discounts.value = response.data.data.data || response.data.data
    }
  } catch (error) {
    console.error('Error al cargar descuentos:', error)
  }
}

const loadPaymentMethods = async () => {
  try {
    const response = await axiosInstance.get('/payment-methods')
    if (response.data.success) {
      paymentMethods.value = response.data.data
    }
  } catch (error) {
    console.error('Error al cargar métodos de pago:', error)
  }
}

const saveAllSettings = async () => {
  loading.value = true
  try {
    const response = await axiosInstance.put('/system-settings', systemSettings.value)
    if (response.data.success) {
      showNotification('Configuraciones guardadas exitosamente', 'success')
    }
  } catch (error) {
    console.error('Error al guardar configuración:', error)
    showNotification('Error al guardar configuración', 'error')
  } finally {
    loading.value = false
  }
}

const resetToDefaults = async () => {
  if (confirm('¿Estás seguro de restaurar las configuraciones por defecto?')) {
    try {
      const response = await axiosInstance.post('/system-settings/reset')
      if (response.data.success) {
        await loadSystemSettings()
        showNotification('🔄 Configuraciones restauradas', 'success')
      }
    } catch (error) {
      console.error('Error al restaurar configuración:', error)
    }
  }
}

// Gestión de descuentos
const generateCode = async () => {
  try {
    const response = await axiosInstance.get('/discounts-generate-code')
    if (response.data.success) {
      discountForm.value.code = response.data.data.code
    }
  } catch (error) {
    console.error('Error al generar código:', error)
  }
}

const saveDiscount = async () => {
  loading.value = true
  try {
    let response
    if (editingDiscount.value) {
      response = await axiosInstance.put(`/discounts/${editingDiscount.value.id}`, discountForm.value)
    } else {
      response = await axiosInstance.post('/discounts', discountForm.value)
    }
    
    if (response.data.success) {
      await loadDiscounts()
      closeDiscountModal()
      showNotification(editingDiscount.value ? '✅ Descuento actualizado' : '✅ Descuento creado', 'success')
    }
  } catch (error) {
    console.error('Error al guardar descuento:', error)
    showNotification('❌ Error al guardar descuento', 'error')
  } finally {
    loading.value = false
  }
}

const editDiscount = (discount) => {
  editingDiscount.value = discount
  Object.assign(discountForm.value, discount)
  showAddDiscountModal.value = true
}

const deleteDiscount = async (id) => {
  if (confirm('¿Estás seguro de eliminar este descuento?')) {
    try {
      const response = await axiosInstance.delete(`/discounts/${id}`)
      if (response.data.success) {
        await loadDiscounts()
        showNotification('✅ Descuento eliminado', 'success')
      }
    } catch (error) {
      console.error('Error al eliminar descuento:', error)
    }
  }
}

const closeDiscountModal = () => {
  showAddDiscountModal.value = false
  editingDiscount.value = null
  discountForm.value = {
    name: '',
    description: '',
    code: '',
    type: 'percentage',
    value: 0,
    applies_to: 'all_products',
    applicable_items: null,
    minimum_amount: null,
    maximum_discount: null,
    usage_limit: null,
    starts_at: '',
    expires_at: '',
    active: true,
    stackable: false,
    allow_multiple_uses_per_user: false
  }
}

// Gestión de métodos de pago
const togglePaymentMethodStatus = async (method) => {
  try {
    const response = await axiosInstance.patch(`/payment-methods/${method.id}/toggle-status`)
    if (response.data.success) {
      await loadPaymentMethods()
      showNotification('✅ Estado actualizado', 'success')
    }
  } catch (error) {
    console.error('Error al cambiar estado:', error)
  }
}

const editPaymentMethod = (method) => {
  editingPaymentMethod.value = method
  showAddPaymentMethodModal.value = true
}

const deletePaymentMethod = async (id) => {
  if (confirm('¿Estás seguro de eliminar este método de pago?')) {
    try {
      const response = await axiosInstance.delete(`/payment-methods/${id}`)
      if (response.data.success) {
        await loadPaymentMethods()
        showNotification('✅ Método de pago eliminado', 'success')
      }
    } catch (error) {
      console.error('Error al eliminar método de pago:', error)
    }
  }
}

// Utilidades
const formatDate = (dateString) => {
  if (!dateString) return ''
  return new Date(dateString).toLocaleDateString('es-ES')
}

// Inicialización
onMounted(async () => {
  await loadSystemSettings()
  await loadDiscounts()
  await loadPaymentMethods()
})
</script>