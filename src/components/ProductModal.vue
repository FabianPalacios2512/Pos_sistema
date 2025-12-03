<template>
  <div class="fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center z-50 p-4 animate-fade-in" @click.self="$emit('close')">
    <div class="bg-white dark:bg-zinc-900 rounded-2xl shadow-2xl max-w-5xl w-full max-h-[95vh] overflow-hidden animate-scale-in">
      
      <!-- Header Ejecutivo con Stepper -->
      <div class="bg-white dark:bg-zinc-900 border-b border-zinc-200 dark:border-zinc-800">
        <div class="px-6 py-4 flex items-center justify-between">
          <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-zinc-100 dark:bg-zinc-800 rounded-lg flex items-center justify-center">
              <svg class="w-5 h-5 text-zinc-700 dark:text-zinc-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
              </svg>
            </div>
            <div>
              <h3 class="text-lg font-bold text-zinc-900 dark:text-zinc-100">
                {{ product?.id ? 'Editar Producto' : 'Crear Nuevo Producto' }}
              </h3>
              <p class="text-xs text-zinc-500 dark:text-zinc-400">Complete la información en 3 pasos simples</p>
            </div>
          </div>
          <button @click="$emit('close')" class="text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-300 transition-colors p-2 hover:bg-zinc-100 dark:hover:bg-zinc-800 rounded-lg">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
          </button>
        </div>
        
        <!-- Stepper Visual Profesional -->
        <div class="px-6 pb-4">
          <div class="flex items-center justify-between">
            <!-- Paso 1 -->
            <div class="flex items-center flex-1">
              <div :class="[
                'flex items-center justify-center w-8 h-8 rounded-full font-bold text-sm transition-all',
                currentStep >= 1 
                  ? 'bg-zinc-900 dark:bg-zinc-100 text-white dark:text-zinc-900' 
                  : 'bg-zinc-200 dark:bg-zinc-700 text-zinc-400 dark:text-zinc-500'
              ]">
                <svg v-if="currentStep > 1" class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                </svg>
                <span v-else>1</span>
              </div>
              <div class="ml-3 min-w-0">
                <p :class="['text-xs font-semibold', currentStep >= 1 ? 'text-zinc-900 dark:text-zinc-100' : 'text-zinc-400 dark:text-zinc-500']">
                  Identidad
                </p>
                <p class="text-[10px] text-zinc-500 dark:text-zinc-400 truncate">Nombre y categoría</p>
              </div>
            </div>
            
            <!-- Línea conectora 1 -->
            <div class="flex-1 h-0.5 mx-2" :class="currentStep > 1 ? 'bg-zinc-900 dark:bg-zinc-100' : 'bg-zinc-200 dark:bg-zinc-700'"></div>
            
            <!-- Paso 2 -->
            <div class="flex items-center flex-1">
              <div :class="[
                'flex items-center justify-center w-8 h-8 rounded-full font-bold text-sm transition-all',
                currentStep >= 2 
                  ? 'bg-zinc-900 dark:bg-zinc-100 text-white dark:text-zinc-900' 
                  : 'bg-zinc-200 dark:bg-zinc-700 text-zinc-400 dark:text-zinc-500'
              ]">
                <svg v-if="currentStep > 2" class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                </svg>
                <span v-else>2</span>
              </div>
              <div class="ml-3 min-w-0">
                <p :class="['text-xs font-semibold', currentStep >= 2 ? 'text-zinc-900 dark:text-zinc-100' : 'text-zinc-400 dark:text-zinc-500']">
                  Precio y Medida
                </p>
                <p class="text-[10px] text-zinc-500 dark:text-zinc-400 truncate">Cómo se vende</p>
              </div>
            </div>
            
            <!-- Línea conectora 2 -->
            <div class="flex-1 h-0.5 mx-2" :class="currentStep > 2 ? 'bg-zinc-900 dark:bg-zinc-100' : 'bg-zinc-200 dark:bg-zinc-700'"></div>
            
            <!-- Paso 3 -->
            <div class="flex items-center flex-1">
              <div :class="[
                'flex items-center justify-center w-8 h-8 rounded-full font-bold text-sm transition-all',
                currentStep >= 3 
                  ? 'bg-zinc-900 dark:bg-zinc-100 text-white dark:text-zinc-900' 
                  : 'bg-zinc-200 dark:bg-zinc-700 text-zinc-400 dark:text-zinc-500'
              ]">
                3
              </div>
              <div class="ml-3 min-w-0">
                <p :class="['text-xs font-semibold', currentStep >= 3 ? 'text-zinc-900 dark:text-zinc-100' : 'text-zinc-400 dark:text-zinc-500']">
                  Inventario
                </p>
                <p class="text-[10px] text-zinc-500 dark:text-zinc-400 truncate">Stock y tiendas</p>
              </div>
            </div>
          </div>
        </div>
      </div>

      <form @submit.prevent="handleSubmit" class="flex flex-col h-full">
        <!-- Contenedor del Paso Actual (con scroll) -->
        <div class="flex-1 overflow-y-auto p-6" style="max-height: calc(95vh - 240px);">
          
          <!-- ========================= PASO 1: IDENTIDAD DEL PRODUCTO ========================= -->
          <div v-if="currentStep === 1" class="space-y-6 animate-fade-in">
          
          <!-- Columna Izquierda: Información Básica -->
          <div class="space-y-6">
            
            <!-- Información General -->
            <div class="bg-gray-50 dark:bg-gray-900 rounded-lg p-4">
              <h4 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Información General</h4>
              
              <!-- Nombre del Producto -->
              <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                  Nombre del Producto *
                </label>
                <input
                  v-model="formData.name"
                  type="text"
                  required
                  placeholder="Ej: Coca Cola 600ml"
                  class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                />
              </div>

              <!-- Descripción -->
              <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                  Descripción
                </label>
                <textarea
                  v-model="formData.description"
                  rows="3"
                  placeholder="Descripción detallada del producto..."
                  class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                ></textarea>
              </div>

              <!-- Categoría -->
            <!-- Categoría -->
            <div class="mb-4">
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                Categoría *
                <span class="text-xs text-gray-500 ml-1">(Para organizar productos)</span>
              </label>
              <select
                v-model="formData.category_id"
                required
                class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
              >
                <option value="">Seleccionar categoría</option>
                <option
                  v-for="category in categories"
                  :key="category.id"
                  :value="category.id"
                >
                  {{ category.name }}
                </option>
              </select>
              <p class="text-xs text-gray-500 mt-1">Seleccione la categoría donde aparecerá este producto</p>
            </div>              <!-- Estado Activo -->
              <div class="flex items-center">
                <input
                  v-model="formData.active"
                  type="checkbox"
                  class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded"
                />
                <label class="ml-2 text-sm text-gray-700 dark:text-gray-300">
                  Producto activo (disponible para venta)
                </label>
              </div>
            </div>

            <!-- Códigos e Identificación -->
            <div class="bg-gray-50 dark:bg-gray-900 rounded-lg p-4">
              <h4 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Identificación</h4>
              
              <!-- Código de Barras -->
              <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                  Código de Barras
                  <span class="text-xs text-gray-500 ml-1">(Opcional - para lectores de códigos)</span>
                </label>
                <div class="flex space-x-2">
                  <input
                    v-model="formData.barcode"
                    type="text"
                    placeholder="Ej: 7894900011517 (Opcional)"
                    class="flex-1 px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                  />
                  <button
                    type="button"
                    @click="generateBarcode"
                    class="px-3 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors"
                    title="Generar código automático"
                  >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                  </button>
                </div>
                <p class="text-xs text-gray-500 mt-1">
                  <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                  </svg>
                  Necesario solo si usa pistola lectora de códigos de barras
                </p>
              </div>

              <!-- SKU -->
              <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                  SKU (Código Interno)
                  <span class="text-xs text-gray-500 ml-1">(Opcional - se genera automáticamente si no se proporciona)</span>
                </label>
                <div class="flex space-x-2">
                  <input
                    v-model="formData.sku"
                    type="text"
                    placeholder="Ej: COC-600-001 (Opcional)"
                    class="flex-1 px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                  />
                  <button
                    type="button"
                    @click="generateSKU"
                    class="px-3 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors"
                    title="Generar SKU automático"
                  >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                  </button>
                </div>
                <p class="text-xs text-gray-500 mt-1">
                  <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                  </svg>
                  El SKU es un código único para identificar el producto internamente
                </p>
              </div>
            </div>
          </div>

          <!-- Columna Derecha: Precios e Inventario -->
          <div class="space-y-6">
            
            <!-- Precios -->
            <div class="bg-gray-50 dark:bg-gray-900 rounded-lg p-4">
              <h4 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Precios</h4>
              
              <!-- Precio de Costo -->
              <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                  Precio de Costo *
                  <span class="text-xs text-gray-500 ml-1">(Cuánto le cuesta el producto)</span>
                </label>
                <div class="relative">
                  <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <span class="text-gray-500 dark:text-gray-400">$</span>
                  </div>
                  <input
                    v-model.number="formData.cost"
                    type="number"
                    step="0.01"
                    min="0.01"
                    required
                    placeholder="1.00"
                    class="block w-full pl-8 pr-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                  />
                </div>
                <p class="text-xs text-gray-500 mt-1">Ingrese cuánto le cuesta este producto (precio de compra)</p>
              </div>

                            <!-- Precio de Venta -->
              <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                  Precio de Venta *
                  <span class="text-xs text-gray-500 ml-1">(Precio al cliente)</span>
                </label>
                <div class="relative">
                  <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <span class="text-gray-500 dark:text-gray-400">$</span>
                  </div>
                  <input
                    v-model.number="formData.price"
                    type="number"
                    step="0.01"
                    min="0.01"
                    required
                    placeholder="2.50"
                    class="block w-full pl-8 pr-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                  />
                </div>
                <p class="text-xs text-gray-500 mt-1">Precio al que venderá este producto a sus clientes</p>
              </div>

              <!-- Margen de Ganancia -->
              <div v-if="formData.cost && formData.price" class="mb-4 p-3 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg">
                <div class="flex justify-between text-sm">
                  <span class="text-blue-800 dark:text-blue-200">Margen:</span>
                  <span class="font-medium text-blue-600 dark:text-blue-400">
                    {{ ((formData.price - formData.cost) / formData.price * 100).toFixed(1) }}%
                  </span>
                </div>
                <div class="flex justify-between text-sm">
                  <span class="text-blue-800 dark:text-blue-200">Ganancia:</span>
                  <span class="font-medium text-blue-600 dark:text-blue-400">
                    ${{ (formData.price - formData.cost).toLocaleString() }}
                  </span>
                </div>
              </div>

              <!-- 📏 UNIDAD DE MEDIDA (NUEVO) -->
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <!-- Unidad de Medida -->
                <div>
                  <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Unidad de Medida *
                    <span class="text-xs text-gray-500 ml-1">(¿Cómo se vende?)</span>
                  </label>
                  <select
                    v-model="formData.measurement_unit"
                    @change="updateAllowDecimal"
                    class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                  >
                    <option value="unit">🔢 Unidades (und) - iPhones, TVs, etc</option>
                    <option value="kg">⚖️ Kilogramos (kg) - Papas, Carne, etc</option>
                    <option value="g">⚖️ Gramos (g) - Especias, Café, etc</option>
                    <option value="m">📏 Metros (m) - Tela, Cable, etc</option>
                    <option value="cm">📏 Centímetros (cm) - Cinta, etc</option>
                    <option value="l">🧴 Litros (L) - Gasolina, Leche, etc</option>
                    <option value="ml">🧴 Mililitros (ml) - Perfumes, etc</option>
                  </select>
                  <p class="text-xs text-gray-500 mt-1">Seleccione cómo se vende este producto</p>
                </div>

                <!-- Permite Decimales -->
                <div>
                  <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Cantidad con Decimales
                    <span class="text-xs text-gray-500 ml-1">(Auto-detectado)</span>
                  </label>
                  <div class="flex items-center h-10">
                    <label class="relative inline-flex items-center cursor-pointer">
                      <input
                        type="checkbox"
                        v-model="formData.allow_decimal"
                        class="sr-only peer"
                      />
                      <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"></div>
                      <span class="ml-3 text-sm font-medium text-gray-900 dark:text-gray-300">
                        {{ formData.allow_decimal ? 'Sí (ej: 1.5 kg)' : 'No (ej: 2 unidades)' }}
                      </span>
                    </label>
                  </div>
                  <p class="text-xs text-gray-500 mt-1">
                    {{ formData.allow_decimal ? 'Permite cantidades como 0.5, 1.25, etc' : 'Solo números enteros (1, 2, 3)' }}
                  </p>
                </div>
              </div>

              <!-- Información Visual de Precio con Unidad -->
              <div v-if="formData.price" class="mb-4 p-3 bg-gradient-to-r from-emerald-50 to-teal-50 dark:from-emerald-900/20 dark:to-teal-900/20 border border-emerald-200 dark:border-emerald-800 rounded-lg">
                <div class="flex items-center justify-between">
                  <span class="text-sm text-emerald-800 dark:text-emerald-200">Precio Final:</span>
                  <span class="text-lg font-bold text-emerald-600 dark:text-emerald-400">
                    ${{ formData.price.toLocaleString() }} / {{ getUnitAbbreviation(formData.measurement_unit) }}
                  </span>
                </div>
                <p class="text-xs text-emerald-700 dark:text-emerald-300 mt-1">
                  Así se mostrará en el POS
                </p>
              </div>
            </div>

            <!-- Inventario Multi-Tienda -->
            <div class="bg-gray-50 dark:bg-gray-900 rounded-lg p-4">
              <div class="flex items-center justify-between mb-4">
                <h4 class="text-lg font-medium text-gray-900 dark:text-white">Inventario por Tienda</h4>
                <button
                  type="button"
                  @click="showWarehouseStockHelp = !showWarehouseStockHelp"
                  class="text-blue-600 hover:text-blue-700"
                >
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                  </svg>
                </button>
              </div>

              <!-- Ayuda contextual -->
              <div v-if="showWarehouseStockHelp" class="mb-4 p-3 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg text-xs text-blue-900 dark:text-blue-100">
                <p class="font-semibold mb-1">💡 Gestión Multi-Tienda:</p>
                <ul class="space-y-1 ml-4 list-disc">
                  <li>Puedes asignar stock diferente en cada tienda para el mismo producto</li>
                  <li>El mismo producto (ej: "Cuaderno") puede tener 4 unidades en Tienda A y 10 en Tienda B</li>
                  <li>Si dejas una tienda sin stock, el producto NO estará disponible en esa tienda</li>
                </ul>
              </div>
              
              <!-- Stock por cada tienda -->
              <div class="space-y-3">
                <div v-for="warehouse in warehouses" :key="warehouse.id" 
                     class="p-3 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
                  <div class="flex items-center justify-between mb-2">
                    <div class="flex items-center gap-2">
                      <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                      </svg>
                      <span class="text-sm font-semibold text-gray-900 dark:text-white">{{ warehouse.name }}</span>
                    </div>
                  </div>
                  <input
                    v-model.number="formData.warehouseStock[warehouse.id]"
                    type="number"
                    min="0"
                    placeholder="0"
                    class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                  />
                </div>
              </div>

              <!-- Stock Total (calculado) -->
              <div class="mt-4 p-3 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg">
                <div class="flex justify-between items-center">
                  <span class="text-sm font-medium text-blue-900 dark:text-blue-100">Stock Total:</span>
                  <span class="text-lg font-bold text-blue-600 dark:text-blue-400">
                    {{ totalStock }} unidades
                  </span>
                </div>
              </div>

              <!-- Stock Mínimo Global -->
              <div class="mt-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                  Stock Mínimo (Alerta) *
                </label>
                <input
                  v-model.number="formData.min_stock"
                  type="number"
                  min="0"
                  required
                  placeholder="0"
                  class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                />
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                  Se generará una alerta cuando el stock total esté por debajo de este nivel
                </p>
              </div>

              <!-- Unidad de Medida -->
              <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                  Unidad de Medida
                </label>
                <select
                  v-model="formData.unit"
                  class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                >
                  <option value="unidad">Unidad</option>
                  <option value="kg">Kilogramo (kg)</option>
                  <option value="gr">Gramo (gr)</option>
                  <option value="litro">Litro (L)</option>
                  <option value="ml">Mililitro (ml)</option>
                  <option value="metro">Metro (m)</option>
                  <option value="cm">Centímetro (cm)</option>
                  <option value="caja">Caja</option>
                  <option value="paquete">Paquete</option>
                </select>
              </div>
            </div>

            <!-- Imagen del Producto -->
            <div class="bg-gray-50 dark:bg-gray-900 rounded-lg p-4">
              <h4 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Imagen</h4>
              
              <div class="space-y-4">
                <!-- Preview de imagen -->
                <div v-if="imagePreview" class="flex justify-center">
                  <img :src="imagePreview" alt="Preview" class="h-32 w-32 object-cover rounded-lg border border-gray-300 dark:border-gray-600" />
                </div>
                
                <!-- Upload de imagen -->
                <div>
                  <input
                    ref="fileInput"
                    type="file"
                    accept="image/*"
                    @change="handleImageUpload"
                    class="hidden"
                  />
                  <button
                    type="button"
                    @click="$refs.fileInput.click()"
                    class="w-full flex items-center justify-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
                  >
                    <svg class="w-5 h-5 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    <span class="text-sm text-gray-700 dark:text-gray-300">
                      {{ imagePreview ? 'Cambiar imagen' : 'Seleccionar imagen' }}
                    </span>
                  </button>
                </div>

                <!-- URL de imagen -->
                <div class="text-center text-xs text-gray-500 dark:text-gray-400">o</div>
                <input
                  v-model="formData.image"
                  type="url"
                  placeholder="https://ejemplo.com/imagen.jpg"
                  class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                />
              </div>
            </div>
          </div>
        </div>

        <!-- Mensaje de validación -->
        <div v-if="!isFormValid" class="mb-4 p-3 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-lg">
          <div class="flex items-start">
            <div class="flex-shrink-0">
              <svg class="h-5 w-5 text-amber-400" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
              </svg>
            </div>
            <div class="ml-3">
              <h3 class="text-sm font-medium text-amber-800 dark:text-amber-200">
                Complete los campos requeridos
              </h3>
              <div class="mt-2 text-sm text-amber-700 dark:text-amber-300">
                <ul class="list-disc pl-5 space-y-1">
                  <li v-if="!formData.name?.trim()">Nombre del producto</li>
                  <li v-if="!formData.category_id">Categoría</li>
                  <li v-if="!(formData.cost > 0)">Precio de costo (mayor a 0)</li>
                  <li v-if="!(formData.price > 0)">Precio de venta (mayor a 0)</li>
                </ul>
              </div>
            </div>
          </div>
        </div>

        <!-- Footer con botones -->
        <div class="flex justify-end space-x-3 mt-8 pt-6 border-t border-gray-200 dark:border-gray-700">
          <button
            type="button"
            @click="$emit('close')"
            class="px-6 py-2 bg-gray-300 dark:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-400 dark:hover:bg-gray-500 transition-colors"
          >
            Cancelar
          </button>
          
          <button
            type="submit"
            :disabled="!isFormValid"
            :class="[
              'px-6 py-2 rounded-lg font-medium transition-colors',
              isFormValid
                ? 'bg-primary-600 hover:bg-primary-700 text-white'
                : 'bg-gray-400 text-gray-600 cursor-not-allowed'
            ]"
          >
            {{ product?.id ? 'Actualizar' : 'Crear' }} Producto
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch, onMounted } from 'vue'

// Props
const props = defineProps({
  product: {
    type: Object,
    default: null
  },
  categories: {
    type: Array,
    required: true
  },
  warehouses: {
    type: Array,
    required: true
  }
})

// Emits
const emit = defineEmits(['save', 'close'])

// Estado
const formData = ref({
  name: '',
  description: '',
  category_id: '',
  barcode: '',
  sku: '',
  price: 0,
  cost: 0,
  stock: 0,
  min_stock: 0,
  unit: 'unidad',
  measurement_unit: 'unit',  // 📏 Nueva: Unidad de medida
  allow_decimal: false,       // ✅ Nueva: Permite decimales
  image: '',
  active: true,
  warehouseStock: {} // { warehouse_id: stock_quantity }
})

const imagePreview = ref('')
const fileInput = ref(null)
const showWarehouseStockHelp = ref(false)

// Computed
const totalStock = computed(() => {
  return Object.values(formData.value.warehouseStock).reduce((sum, stock) => sum + (parseInt(stock) || 0), 0)
})

const isFormValid = computed(() => {
  return formData.value.name?.trim() &&
         formData.value.category_id &&
         formData.value.cost > 0 &&
         formData.value.price > 0 &&
         formData.value.min_stock >= 0
         // SKU no es requerido porque se genera automáticamente
})

// Watchers
watch(() => formData.value.image, (newUrl) => {
  if (newUrl && newUrl.startsWith('http')) {
    imagePreview.value = newUrl
  }
})

// Métodos
const initializeForm = () => {
  // Inicializar warehouseStock con todas las tiendas en 0
  const warehouseStock = {}
  props.warehouses?.forEach(warehouse => {
    warehouseStock[warehouse.id] = 0
  })

  if (props.product) {
    // Editar producto existente
    formData.value = {
      id: props.product.id,
      name: props.product.name,
      description: props.product.description || '',
      category_id: props.product.category_id,
      barcode: props.product.barcode || '',
      sku: props.product.sku || '',
      price: props.product.price,
      cost: props.product.cost || 0,
      stock: props.product.stock,
      min_stock: props.product.min_stock,
      unit: props.product.unit || 'unidad',
      measurement_unit: props.product.measurement_unit || 'unit',  // 📏 Nueva
      allow_decimal: props.product.allow_decimal || false,         // ✅ Nueva
      image: props.product.image || '',
      active: props.product.active,
      warehouseStock: { ...warehouseStock }
    }

    // Cargar el stock por tienda si existe
    if (props.product.alternative_warehouses && Array.isArray(props.product.alternative_warehouses)) {
      props.product.alternative_warehouses.forEach(warehouse => {
        if (warehouse.id) {
          formData.value.warehouseStock[warehouse.id] = warehouse.stock || 0
        }
      })
    }
    // Si el producto tiene warehouse_id (tienda actual), cargar su stock
    if (props.product.warehouse_id) {
      formData.value.warehouseStock[props.product.warehouse_id] = props.product.stock || 0
    }

    imagePreview.value = props.product.image || ''
  } else {
    // Crear nuevo producto
    formData.value = {
      id: null,
      name: '',
      description: '',
      category_id: '',
      barcode: '',
      sku: '',
      price: '', // Dejar vacío para que el usuario ingrese valor
      cost: '', // Dejar vacío para que el usuario ingrese valor
      stock: 0, // Stock inicial puede ser 0
      min_stock: 0,
      unit: 'unidad',
      measurement_unit: 'unit',  // 📏 Nueva: Por defecto unidades
      allow_decimal: false,       // ✅ Nueva: Por defecto no permite decimales
      image: '',
      active: true,
      warehouseStock: { ...warehouseStock }
    }
    imagePreview.value = ''
  }
}

const generateBarcode = () => {
  // Generar código de barras EAN-13 simulado
  const prefix = '789' // Código de país (ejemplo)
  const company = '4900' // Código de empresa (ejemplo)
  const random = Math.floor(Math.random() * 100000).toString().padStart(5, '0')
  const partial = prefix + company + random
  
  // Calcular dígito de verificación EAN-13
  let sum = 0
  for (let i = 0; i < partial.length; i++) {
    const digit = parseInt(partial[i])
    sum += i % 2 === 0 ? digit : digit * 3
  }
  const checkDigit = (10 - (sum % 10)) % 10
  
  formData.value.barcode = partial + checkDigit
}

const generateSKU = () => {
  // Generar SKU basado en categoría y timestamp
  const selectedCategory = props.categories.find(c => c.id == formData.value.category_id)
  const categoryPrefix = selectedCategory?.name.substring(0, 3).toUpperCase() || 'GEN'
  const timestamp = Date.now().toString().slice(-6) // Últimos 6 dígitos del timestamp
  formData.value.sku = `${categoryPrefix}-${timestamp}`
}

const handleImageUpload = (event) => {
  const file = event.target.files[0]
  if (file) {
    const reader = new FileReader()
    reader.onload = (e) => {
      imagePreview.value = e.target.result
      formData.value.image = e.target.result
    }
    reader.readAsDataURL(file)
  }
}

// 📏 Métodos para Unidades de Medida
const getUnitAbbreviation = (unit) => {
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

const updateAllowDecimal = () => {
  // Auto-detectar si debe permitir decimales según la unidad
  const decimalUnits = ['kg', 'g', 'm', 'cm', 'l', 'ml']
  formData.value.allow_decimal = decimalUnits.includes(formData.value.measurement_unit)
}

const handleSubmit = () => {
  // Si no hay SKU, generar uno automáticamente
  if (!formData.value.sku || formData.value.sku.trim().length === 0) {
    generateSKU()
  }
  
  if (!isFormValid.value) return
  
  // Calcular stock total sumando todas las tiendas
  const totalStockValue = totalStock.value
  
  // Preparar datos con stock total y stock por tienda
  const dataToSend = {
    ...formData.value,
    stock: totalStockValue, // Stock total calculado
    warehouse_stocks: formData.value.warehouseStock // Stock por cada tienda
  }
  
  emit('save', dataToSend)
}

// Lifecycle
onMounted(() => {
  initializeForm()
})
</script>

<style scoped>
@keyframes scaleIn {
  from {
    opacity: 0;
    transform: scale(0.95);
  }
  to {
    opacity: 1;
    transform: scale(1);
  }
}

.animate-scale-in {
  animation: scaleIn 0.2s ease-out;
}
</style>