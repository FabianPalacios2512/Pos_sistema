<template>
  <!-- Contenido del formulario - Diseño Profesional SaaS -->
  <div class="space-y-5">
    
    <!-- ═══════════════════════════════════════════════════════════════ -->
    <!-- SECCIÓN 1: INFORMACIÓN BÁSICA (Compacta) -->
    <!-- ═══════════════════════════════════════════════════════════════ -->
    <div class="space-y-4">
      
      <!-- Nombre del Producto -->
      <div>
        <label class="block text-xs font-semibold text-gray-700 dark:text-zinc-300 mb-1.5">
          Nombre del Producto <span class="text-rose-500">*</span>
        </label>
        <input 
          v-model="form.name"
          type="text" 
          required
          placeholder="Ej: Camiseta Polo Premium"
          class="w-full px-4 py-2.5 bg-white dark:bg-zinc-800/80 border border-gray-200 dark:border-zinc-700 rounded-xl text-sm text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-zinc-500 focus:outline-none focus:ring-2 focus:ring-slate-400 dark:focus:ring-zinc-500 focus:border-transparent transition-all"
        >
      </div>

      <!-- Fila Compacta: Categoría + SKU + Proveedor -->
      <div class="grid grid-cols-3 gap-3">
        <div>
          <label class="block text-xs font-semibold text-gray-700 dark:text-zinc-300 mb-1.5">Categoría *</label>
          <select 
            v-model="form.category_id"
            @change="handleCategoryChange"
            class="w-full px-3 py-2.5 bg-white dark:bg-zinc-800/80 border border-gray-200 dark:border-zinc-700 rounded-xl text-sm text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-slate-400 dark:focus:ring-zinc-500"
          >
            <option value="" disabled>Seleccionar</option>
            <option value="__new__" class="font-medium text-blue-600">＋ Nueva</option>
            <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
          </select>
        </div>

        <div>
          <label class="block text-xs font-semibold text-gray-700 dark:text-zinc-300 mb-1.5">SKU</label>
          <input 
            v-model="form.sku"
            type="text" 
            placeholder="Auto-generado"
            class="w-full px-3 py-2.5 bg-gray-50 dark:bg-zinc-800/50 border border-gray-200 dark:border-zinc-700 rounded-xl text-sm text-gray-600 dark:text-zinc-400 font-mono placeholder-gray-400 dark:placeholder-zinc-500 focus:outline-none focus:ring-2 focus:ring-slate-400 dark:focus:ring-zinc-500"
          >
        </div>

        <div>
          <label class="block text-xs font-semibold text-gray-700 dark:text-zinc-300 mb-1.5">Proveedor</label>
          <select 
            v-model="form.supplier_id"
            @change="handleSupplierChange"
            class="w-full px-3 py-2.5 bg-white dark:bg-zinc-800/80 border border-gray-200 dark:border-zinc-700 rounded-xl text-sm text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-slate-400 dark:focus:ring-zinc-500"
          >
            <option :value="null">Sin proveedor</option>
            <option value="__new__" class="font-medium text-blue-600">＋ Nuevo</option>
            <option v-for="sup in suppliers" :key="sup.id" :value="sup.id">{{ sup.name }}</option>
          </select>
        </div>
      </div>
    </div>

    <!-- ═══════════════════════════════════════════════════════════════ -->
    <!-- SECCIÓN 2: MULTIMEDIA + DESCRIPCIÓN (Grid 2 columnas) -->
    <!-- ═══════════════════════════════════════════════════════════════ -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
      
      <!-- Área de Imágenes -->
      <div>
        <label class="block text-xs font-semibold text-gray-700 dark:text-zinc-300 mb-1.5">Imágenes del Producto</label>
        
        <!-- Dropzone Compacto -->
        <div 
          @click="triggerFileInput"
          @dragover.prevent="isDragging = true"
          @dragleave.prevent="isDragging = false"
          @drop.prevent="handleDrop"
          :class="[
            'relative border-2 border-dashed rounded-xl p-4 text-center transition-all cursor-pointer',
            isDragging 
              ? 'border-slate-400 bg-slate-50 dark:bg-zinc-800' 
              : 'border-gray-200 dark:border-zinc-700 hover:border-gray-300 dark:hover:border-zinc-600 hover:bg-gray-50/50 dark:hover:bg-zinc-800/30'
          ]"
        >
          <input 
            type="file" 
            ref="fileInput" 
            class="hidden" 
            multiple 
            accept="image/*"
            @change="handleFileChange"
          >
          
          <!-- Sin imágenes -->
          <div v-if="form.images.length === 0" class="py-4">
            <div class="w-12 h-12 mx-auto rounded-xl bg-gray-100 dark:bg-zinc-800 flex items-center justify-center mb-2">
              <svg class="w-6 h-6 text-gray-400 dark:text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
              </svg>
            </div>
            <p class="text-sm font-medium text-gray-600 dark:text-zinc-400">Arrastra imágenes aquí</p>
            <p class="text-xs text-gray-400 dark:text-zinc-500 mt-0.5">o haz clic para seleccionar • Máx 5 fotos</p>
          </div>

          <!-- Con imágenes - Grid de miniaturas -->
          <div v-else class="grid grid-cols-5 gap-2">
            <div v-for="(img, index) in form.images" :key="index" 
                 class="relative group aspect-square bg-gray-100 dark:bg-zinc-800 rounded-lg overflow-hidden border border-gray-200 dark:border-zinc-700">
              <img :src="img.preview" class="w-full h-full object-cover">
              <button 
                type="button"
                @click.stop="removeImage(index)"
                class="absolute inset-0 bg-black/50 text-white flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity"
              >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
              </button>
              <span v-if="index === 0" class="absolute bottom-0 left-0 right-0 bg-black/60 text-white text-[8px] font-bold text-center py-0.5">PRINCIPAL</span>
            </div>
            <!-- Slot para agregar más -->
            <div v-if="form.images.length < 5" 
                 class="aspect-square rounded-lg border-2 border-dashed border-gray-200 dark:border-zinc-700 flex items-center justify-center text-gray-400 dark:text-zinc-600 hover:border-gray-300 dark:hover:border-zinc-600 transition-colors">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            </div>
          </div>
        </div>
      </div>

      <!-- Descripción -->
      <div>
        <label class="block text-xs font-semibold text-gray-700 dark:text-zinc-300 mb-1.5">Descripción</label>
        <textarea 
          v-model="form.description"
          rows="5"
          placeholder="Describe el material, ajuste, instrucciones de cuidado..."
          class="w-full px-4 py-3 bg-white dark:bg-zinc-800/80 border border-gray-200 dark:border-zinc-700 rounded-xl text-sm text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-zinc-500 resize-none focus:outline-none focus:ring-2 focus:ring-slate-400 dark:focus:ring-zinc-500 h-[130px]"
        ></textarea>
      </div>
    </div>

    <!-- ═══════════════════════════════════════════════════════════════ -->
    <!-- SECCIÓN 3: VARIANTES (Atributos + Generación) -->
    <!-- ═══════════════════════════════════════════════════════════════ -->
    <div class="border-t border-gray-100 dark:border-zinc-800 pt-5">
      
      <!-- Header de Atributos -->
      <div class="flex items-center justify-between mb-3">
        <div>
          <h3 class="text-sm font-bold text-gray-900 dark:text-white">Atributos y Variantes</h3>
          <p class="text-xs text-gray-500 dark:text-zinc-400 mt-0.5">Define tallas, colores u otras opciones</p>
        </div>
        <button 
          type="button"
          @click="addOption"
          class="px-3 py-1.5 text-xs font-semibold text-slate-600 dark:text-zinc-300 hover:text-slate-900 dark:hover:text-white bg-gray-100 dark:bg-zinc-800 hover:bg-gray-200 dark:hover:bg-zinc-700 rounded-lg flex items-center gap-1.5 transition-all"
        >
          <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
          Añadir Atributo
        </button>
      </div>

      <!-- Estado Vacío -->
      <div v-if="form.options.length === 0" 
           class="text-center py-8 bg-gray-50 dark:bg-zinc-900/50 rounded-xl border border-dashed border-gray-200 dark:border-zinc-800">
        <svg class="w-8 h-8 mx-auto text-gray-300 dark:text-zinc-600 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
        </svg>
        <p class="text-xs text-gray-400 dark:text-zinc-500">Sin atributos configurados</p>
        <p class="text-[10px] text-gray-400 dark:text-zinc-600 mt-0.5">El producto se guardará como simple</p>
      </div>

      <!-- Lista de Atributos -->
      <div v-else class="space-y-3">
        <div v-for="(option, index) in form.options" :key="index" 
             class="flex items-center gap-3 p-3 bg-white dark:bg-zinc-900/60 rounded-xl border border-gray-100 dark:border-zinc-800">
          
          <!-- Nombre del Atributo -->
          <input 
            v-model="option.name"
            type="text" 
            placeholder="Nombre"
            class="w-28 px-3 py-2 bg-gray-50 dark:bg-zinc-800 border border-gray-200 dark:border-zinc-700 rounded-lg text-sm font-semibold text-gray-900 dark:text-white text-center focus:ring-2 focus:ring-slate-400 dark:focus:ring-zinc-600 focus:border-transparent outline-none transition-all"
          >

          <!-- Valores - Color Picker Compacto o Input Tags -->
          <div class="flex-1">
            <!-- Si es "Color" - Picker Inline Compacto -->
            <div v-if="isColorOption(option.name)" class="flex items-center gap-2 flex-wrap">
              <!-- Colores seleccionados -->
              <button
                type="button"
                v-for="val in option.values" 
                :key="val"
                @click="removeColorValue(index, val)"
                :style="{ backgroundColor: val }"
                class="w-8 h-8 rounded-lg border-2 border-white dark:border-zinc-800 shadow-md hover:scale-110 transition-transform relative group"
                :title="`Quitar ${val}`"
              >
                <div class="absolute inset-0 bg-black/50 rounded-lg opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                  <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"/></svg>
                </div>
              </button>
              
              <!-- Botón Agregar Color -->
              <label class="w-8 h-8 rounded-lg bg-gradient-to-br from-red-400 via-green-400 to-blue-400 flex items-center justify-center cursor-pointer hover:scale-110 transition-transform shadow-md border-2 border-white dark:border-zinc-800">
                <input 
                  type="color"
                  @change="addColorFromPicker(index, $event)"
                  class="sr-only"
                >
                <svg class="w-4 h-4 text-white drop-shadow" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
              </label>
            </div>

            <!-- Si NO es "Color" - Input con Tags -->
            <div v-else class="flex flex-wrap items-center gap-1.5 p-2 bg-gray-50 dark:bg-zinc-800 border border-gray-200 dark:border-zinc-700 rounded-lg min-h-[40px]">
              <span 
                v-for="(val, vIndex) in option.values" 
                :key="vIndex"
                class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-semibold bg-white dark:bg-zinc-700 text-gray-700 dark:text-zinc-200 border border-gray-200 dark:border-zinc-600 shadow-sm"
              >
                {{ val }}
                <button type="button" @click="removeValue(index, vIndex)" class="ml-1.5 text-gray-400 hover:text-red-500 transition-colors">×</button>
              </span>
              <input 
                type="text"
                v-model="option.tempValue"
                @keydown.enter.prevent="addOptionValue(index)"
                @keydown.backspace="handleBackspace(index)"
                placeholder="Escribir y Enter"
                class="flex-1 min-w-[100px] bg-transparent border-none focus:ring-0 text-sm text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-zinc-500 p-0"
              >
            </div>
          </div>

          <!-- Botón Eliminar Atributo -->
          <button 
            type="button" 
            @click="removeOption(index)"
            class="p-2 text-gray-400 hover:text-rose-500 hover:bg-rose-50 dark:hover:bg-rose-900/20 rounded-lg transition-all"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
          </button>
        </div>
      </div>

      <!-- Botón Generar / Continuar -->
      <div class="mt-4">
        <button 
          type="button"
          ref="generateButton"
          @click="handleContinueOrGenerate"
          :class="[
            'w-full py-3 rounded-xl text-sm font-bold shadow-sm transition-all flex items-center justify-center gap-2',
            hasValidOptions 
              ? 'bg-slate-800 dark:bg-slate-700 hover:bg-slate-900 dark:hover:bg-slate-600 text-white' 
              : 'bg-gray-100 dark:bg-zinc-800 hover:bg-gray-200 dark:hover:bg-zinc-700 text-gray-700 dark:text-zinc-300 border border-gray-200 dark:border-zinc-700'
          ]"
        >
          <svg v-if="!hasValidOptions" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
          <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
          <span>{{ buttonText }}</span>
        </button>
        <p class="text-center text-[11px] text-gray-400 dark:text-zinc-500 mt-2">
          {{ !hasValidOptions ? '💡 Sin atributos = Producto simple' : '🎯 Genera la matriz para asignar precios y stock' }}
        </p>
      </div>
    </div>

    <!-- ═══════════════════════════════════════════════════════════════ -->
    <!-- SECCIÓN 4: FORMULARIO SIMPLE (Sin Variantes) -->
    <!-- ═══════════════════════════════════════════════════════════════ -->
    <section v-if="showSimpleForm" ref="simpleFormSection" class="animate-fade-in">
      <div class="bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-950/20 dark:to-indigo-950/20 border border-blue-100 dark:border-blue-900/30 rounded-xl p-4 mb-4">
        <div class="flex items-center gap-3">
          <div class="w-9 h-9 bg-blue-100 dark:bg-blue-900/50 rounded-lg flex items-center justify-center flex-shrink-0">
            <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
            </svg>
          </div>
          <div>
            <h3 class="text-sm font-bold text-blue-900 dark:text-blue-300">Producto Simple</h3>
            <p class="text-xs text-blue-600 dark:text-blue-400/80">Sin variantes de talla o color</p>
          </div>
        </div>
      </div>

      <div class="grid grid-cols-3 gap-3">
        <div>
          <label class="block text-xs font-semibold text-gray-700 dark:text-zinc-300 mb-1.5">
            Costo <span class="text-rose-500">*</span>
          </label>
          <div class="relative">
            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-sm text-gray-400 dark:text-zinc-500 font-medium">$</span>
            <input 
              v-model.number="simpleProduct.cost"
              type="number"
              step="0.01"
              min="0"
              required
              class="w-full pl-8 pr-3 py-2.5 bg-white dark:bg-zinc-800/80 border border-gray-200 dark:border-zinc-700 rounded-xl text-sm font-semibold text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent tabular-nums"
              placeholder="0"
            >
          </div>
        </div>

        <div>
          <label class="block text-xs font-semibold text-gray-700 dark:text-zinc-300 mb-1.5">
            Precio Venta <span class="text-rose-500">*</span>
          </label>
          <div class="relative">
            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-sm text-gray-400 dark:text-zinc-500 font-medium">$</span>
            <input 
              v-model.number="simpleProduct.price"
              type="number"
              step="0.01"
              min="0"
              required
              class="w-full pl-8 pr-3 py-2.5 bg-white dark:bg-zinc-800/80 border border-gray-200 dark:border-zinc-700 rounded-xl text-sm font-semibold text-emerald-600 dark:text-emerald-400 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent tabular-nums"
              placeholder="0"
            >
          </div>
        </div>

        <div>
          <label class="block text-xs font-semibold text-gray-700 dark:text-zinc-300 mb-1.5">
            Stock <span class="text-rose-500">*</span>
          </label>
          <input 
            v-model.number="simpleProduct.stock"
            type="number"
            min="0"
            required
            class="w-full px-3 py-2.5 bg-white dark:bg-zinc-800/80 border border-gray-200 dark:border-zinc-700 rounded-xl text-sm font-semibold text-gray-900 dark:text-white text-center focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent tabular-nums"
            placeholder="0"
          >
        </div>
      </div>

      <!-- Indicador de Margen -->
      <div v-if="simpleProduct.cost > 0 && simpleProduct.price > 0" 
           class="mt-3 flex items-center justify-between px-3 py-2 bg-gray-50 dark:bg-zinc-800/50 rounded-lg">
        <span class="text-xs text-gray-500 dark:text-zinc-400">Margen de Ganancia</span>
        <span class="text-sm font-bold" :class="profitMargin >= 20 ? 'text-emerald-600 dark:text-emerald-400' : profitMargin >= 10 ? 'text-amber-600 dark:text-amber-400' : 'text-rose-600 dark:text-rose-400'">
          {{ profitMargin }}%
        </span>
      </div>
    </section>

    <!-- ═══════════════════════════════════════════════════════════════ -->
    <!-- SECCIÓN 5: TABLA DE VARIANTES (Generada) -->
    <!-- ═══════════════════════════════════════════════════════════════ -->
    <section v-if="form.variants.length > 0 && !showSimpleForm" ref="variantsSection" class="animate-fade-in">
      <div class="flex items-center justify-between mb-3">
        <h3 class="text-sm font-bold text-gray-900 dark:text-white flex items-center gap-2">
          <svg class="w-4 h-4 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
          Variantes
          <span class="text-xs font-normal text-gray-400 dark:text-zinc-500">({{ form.variants.length }} combinaciones)</span>
        </h3>
      </div>

      <!-- Tabla Compacta -->
      <div class="overflow-hidden border border-gray-200 dark:border-zinc-800 rounded-xl">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-zinc-800">
          <thead class="bg-gray-50 dark:bg-zinc-900/80">
            <tr>
              <th class="px-3 py-2.5 text-left text-[10px] font-bold text-gray-500 dark:text-zinc-400 uppercase tracking-wider">Variante</th>
              <th class="px-3 py-2.5 text-left text-[10px] font-bold text-gray-500 dark:text-zinc-400 uppercase tracking-wider w-32">SKU</th>
              <th class="px-3 py-2.5 text-center text-[10px] font-bold text-gray-500 dark:text-zinc-400 uppercase tracking-wider w-28">Costo</th>
              <th class="px-3 py-2.5 text-center text-[10px] font-bold text-gray-500 dark:text-zinc-400 uppercase tracking-wider w-28">Precio</th>
              <th class="px-3 py-2.5 text-center text-[10px] font-bold text-gray-500 dark:text-zinc-400 uppercase tracking-wider w-20">Stock</th>
              <th class="px-2 py-2.5 w-10"></th>
            </tr>
          </thead>
          <tbody class="bg-white dark:bg-zinc-900/40 divide-y divide-gray-100 dark:divide-zinc-800/50">
            <tr v-for="(variant, index) in form.variants" :key="index" class="hover:bg-gray-50/50 dark:hover:bg-zinc-800/30 transition-colors">
              <td class="px-3 py-2">
                <span class="text-sm font-medium text-gray-900 dark:text-white">{{ getVariantName(variant) }}</span>
              </td>
              <td class="px-3 py-1.5">
                <input 
                  v-model="variant.sku"
                  type="text" 
                  class="w-full px-2 py-1.5 text-xs font-mono border border-gray-200 dark:border-zinc-700 rounded-lg bg-gray-50 dark:bg-zinc-800 text-gray-600 dark:text-zinc-400 focus:outline-none focus:ring-1 focus:ring-slate-400"
                >
              </td>
              <td class="px-3 py-1.5">
                <div class="relative">
                  <span class="absolute left-2 top-1/2 -translate-y-1/2 text-xs text-gray-400">$</span>
                  <input 
                    v-model.number="variant.cost_price"
                    type="number"
                    step="1"
                    min="0"
                    class="w-full pl-5 pr-2 py-1.5 text-sm font-semibold text-center border border-gray-200 dark:border-zinc-700 rounded-lg bg-white dark:bg-zinc-800 text-gray-900 dark:text-white focus:outline-none focus:ring-1 focus:ring-slate-400 tabular-nums"
                  >
                </div>
              </td>
              <td class="px-3 py-1.5">
                <div class="relative">
                  <span class="absolute left-2 top-1/2 -translate-y-1/2 text-xs text-gray-400">$</span>
                  <input 
                    v-model.number="variant.price"
                    type="number" 
                    step="1"
                    min="0"
                    class="w-full pl-5 pr-2 py-1.5 text-sm font-semibold text-center border border-gray-200 dark:border-zinc-700 rounded-lg bg-white dark:bg-zinc-800 text-emerald-600 dark:text-emerald-400 focus:outline-none focus:ring-1 focus:ring-slate-400 tabular-nums"
                  >
                </div>
              </td>
              <td class="px-3 py-1.5">
                <input 
                  v-model.number="variant.stock"
                  type="number" 
                  min="0"
                  class="w-full px-2 py-1.5 text-sm font-semibold border border-gray-200 dark:border-zinc-700 rounded-lg bg-white dark:bg-zinc-800 text-gray-900 dark:text-white text-center focus:outline-none focus:ring-1 focus:ring-slate-400 tabular-nums"
                >
              </td>
              <td class="px-2 py-1.5 text-center">
                <button 
                  type="button" 
                  @click="removeVariant(index)"
                  class="p-1 text-gray-300 dark:text-zinc-600 hover:text-rose-500 dark:hover:text-rose-400 transition-colors"
                >
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </section>
  </div>
  <!-- FIN contenedor principal -->
</template>

<script setup>
import { reactive, computed, ref, watch } from 'vue'
import { useToast } from '../composables/useToast'

const { showWarning } = useToast()

const props = defineProps({
  categories: {
    type: Array,
    default: () => []
  },
  suppliers: {
    type: Array,
    default: () => []
  },
  editingProduct: {
    type: Object,
    default: null
  }
})

const emit = defineEmits(['save', 'cancel', 'create-category', 'create-supplier'])

// Refs para scroll y color pickers
const generateButton = ref(null)
const variantsSection = ref(null)
const simpleFormSection = ref(null)
const colorInputRefs = ref({})

// Estado para producto simple (sin variantes)
const showSimpleForm = ref(false)
const simpleProduct = reactive({
  cost: 0,
  price: 0,
  stock: 0
})

// Computed: Calcular margen de ganancia para producto simple
const profitMargin = computed(() => {
  if (simpleProduct.cost <= 0 || simpleProduct.price <= 0) return 0
  const margin = ((simpleProduct.price - simpleProduct.cost) / simpleProduct.price) * 100
  return Math.round(margin * 100) / 100
})

// Estado Reactivo del Formulario
const form = reactive({
  id: null, // ID del producto (para edición)
  name: '',
  sku: '',
  description: '',
  category_id: '',
  supplier_id: null,
  options: [
    { name: 'Talla', values: [], tempValue: '' },
    { name: 'Color', values: [], tempValue: '' }
  ],
  variants: [],
  images: []
})

// 🔄 Cargar datos cuando se edita un producto
watch(() => props.editingProduct, (product) => {
  if (product) {
    form.id = product.id || null
    form.name = product.name || ''
    form.sku = product.sku || ''
    form.description = product.description || ''
    form.category_id = product.category_id || ''
    form.supplier_id = product.supplier_id || null
    
    // Cargar opciones
    if (product.options && Array.isArray(product.options)) {
      form.options = product.options.map(opt => {
        // 🛡️ Asegurar que values sea un array de strings, no objetos
        let cleanValues = []
        if (opt.values && Array.isArray(opt.values)) {
          cleanValues = opt.values
            .map(v => {
              // Si v es string, usarlo directamente
              if (typeof v === 'string') return v
              // Si v es objeto con .value string, usar eso
              if (v && typeof v.value === 'string') return v.value
              // Si v es objeto, intentar JSON.stringify pero evitarlo
              if (typeof v === 'object') return ''
              return String(v)
            })
            .filter(v => v) // Filtrar vacíos
        }
        
        return {
          name: opt.name || '',
          values: cleanValues,
          tempValue: ''
        }
      })
    } else {
      // Reset a opciones por defecto si no hay
      form.options = [
        { name: 'Talla', values: [], tempValue: '' },
        { name: 'Color', values: [], tempValue: '' }
      ]
    }
    
    // Cargar variantes
    if (product.variants && Array.isArray(product.variants)) {
      form.variants = product.variants.map(variant => {
        // 🔧 Procesar options correctamente
        let processedOptions = []
        
        if (variant.options) {
          // Si options es un array de objetos con estructura {name, value}
          if (Array.isArray(variant.options)) {
            processedOptions = variant.options.map(opt => {
              // Si el objeto tiene propiedades 'name' y 'value' como strings, úsalas
              if (opt && typeof opt.name === 'string' && typeof opt.value === 'string') {
                return { name: opt.name, value: opt.value }
              }
              // Si tiene value como string
              if (opt && typeof opt.value === 'string') {
                return { name: opt.name || '', value: opt.value }
              }
              // 🛡️ FIX: Si value es un objeto (viene del backend mal parseado), extraer solo el value interno
              if (opt && typeof opt.value === 'object' && opt.value !== null) {
                const innerValue = opt.value.value || ''
                return { name: opt.name || '', value: typeof innerValue === 'string' ? innerValue : '' }
              }
              // Si el opt mismo es un string
              if (typeof opt === 'string') {
                return { name: '', value: opt }
              }
              // Fallback: devolver vacío para evitar [object Object]
              return { name: '', value: '' }
            }).filter(o => o.value) // Filtrar opciones vacías
          }
        }
        
        // Si options_summary existe y es string JSON, parsearlo
        if (variant.options_summary) {
          try {
            const summary = typeof variant.options_summary === 'string' 
              ? JSON.parse(variant.options_summary) 
              : variant.options_summary
              
            if (Array.isArray(summary)) {
              processedOptions = summary.map(s => ({ name: s.name, value: s.value }))
            }
          } catch (e) {
            console.warn('Error parsing options_summary:', e)
          }
        }
        
        return {
          sku: variant.sku,
          price: parseFloat(variant.price || 0),
          cost_price: parseFloat(variant.cost_price || variant.cost || 0),
          stock: parseInt(variant.stock || 0),
          active: variant.active !== false,
          options: processedOptions
        }
      })
    } else {
      form.variants = []
    }
    
    // Detectar si es producto simple (1 variante sin opciones O sin variantes)
    const hasNoVariants = form.variants.length === 0
    const isSimpleProduct = form.variants.length === 1 && 
                           (!form.variants[0].options || form.variants[0].options.length === 0)
    
    if (isSimpleProduct) {
      // Es un producto simple - cargar datos en el formulario simple
      const variant = form.variants[0]
      
      simpleProduct.cost = variant.cost_price || variant.cost || 0
      simpleProduct.price = variant.price
      simpleProduct.stock = variant.stock
      showSimpleForm.value = true
      
      // Limpiar las opciones (no tiene atributos)
      form.options = [
        { name: 'Talla', values: [], tempValue: '' },
        { name: 'Color', values: [], tempValue: '' }
      ]
    } else if (hasNoVariants) {
      // No tiene variantes - intentar cargar desde product padre
      simpleProduct.cost = parseFloat(product.cost_price || 0)
      simpleProduct.price = parseFloat(product.sale_price || product.price || 0)
      simpleProduct.stock = parseInt(product.current_stock || product.stock || 0)
      showSimpleForm.value = true
      
      // Limpiar opciones
      form.options = [
        { name: 'Talla', values: [], tempValue: '' },
        { name: 'Color', values: [], tempValue: '' }
      ]
    } else if (form.variants.length > 1) {
      // Producto con múltiples variantes - ocultar formulario simple
      showSimpleForm.value = false
    } else {
      // No hay variantes
      showSimpleForm.value = false
    }
    
    // Cargar imágenes - Primero las de la galería, luego la principal
    form.images = []
    
    // 1. Cargar imágenes de la galería (product.images)
    if (product.images && Array.isArray(product.images) && product.images.length > 0) {
      product.images.forEach(img => {
        const imageUrl = img.url || img.image_url
        if (imageUrl) {
          form.images.push({
            preview: imageUrl.startsWith('http') ? imageUrl : (imageUrl.startsWith('/') ? imageUrl : `/storage/${imageUrl}`),
            file: null,
            id: img.id || null
          })
        }
      })
    }
    
    // 2. Si no hay imágenes en galería pero hay image_url principal, usarla
    if (form.images.length === 0 && product.image_url) {
      form.images.push({
        preview: product.image_url.startsWith('http') ? product.image_url : (product.image_url.startsWith('/') ? product.image_url : `/storage/${product.image_url}`),
        file: null
      })
    }
  } else {
    // Limpiar formulario cuando no hay producto (modo crear)
    form.id = null
    form.name = ''
    form.sku = ''
    form.description = ''
    form.category_id = ''
    form.supplier_id = null
    form.options = [
      { name: 'Talla', values: [], tempValue: '' },
      { name: 'Color', values: [], tempValue: '' }
    ]
    form.variants = []
    form.images = []
    showSimpleForm.value = false
    simpleProduct.cost = 0
    simpleProduct.price = 0
    simpleProduct.stock = 0
  }
}, { immediate: true })

const handleCategoryChange = (event) => {
  if (event.target.value === '__new__') {
    form.category_id = '' // Reset selection
    emit('create-category')
  }
}

const setCategory = (id) => {
  form.category_id = id
}

const handleSupplierChange = (event) => {
  if (event.target.value === '__new__') {
    form.supplier_id = null // Reset selection
    emit('create-supplier')
  }
}

// --- Lógica de Opciones ---

const addOption = () => {
  form.options.push({
    name: '',
    values: [],
    tempValue: ''
  })
}

const removeOption = (index) => {
  form.options.splice(index, 1)
}

const addOptionValue = (optionIndex) => {
  const option = form.options[optionIndex]
  const val = option.tempValue.trim()
  
  if (val && !option.values.includes(val)) {
    option.values.push(val)
    option.tempValue = '' // Limpiar input
  }
}

const removeValue = (optionIndex, valueIndex) => {
  form.options[optionIndex].values.splice(valueIndex, 1)
}

// 🎨 Funciones para colores personalizados (solo hex)
const isColorOption = (optionName) => {
  return optionName && (
    optionName.toLowerCase().includes('color') || 
    optionName.toLowerCase().includes('colour')
  )
}

// Trigger color picker nativo
const triggerColorPicker = (index) => {
  if (colorInputRefs.value[index]) {
    colorInputRefs.value[index].click()
  }
}

// Agregar color desde picker (se guarda el HEX directamente)
const addColorFromPicker = (optionIndex, event) => {
  const hexColor = event.target.value.toUpperCase()
  const option = form.options[optionIndex]
  
  // Agregar el hex si no existe ya
  if (!option.values.includes(hexColor)) {
    option.values.push(hexColor)
  }
}

const removeColorValue = (optionIndex, colorHex) => {
  const option = form.options[optionIndex]
  const index = option.values.indexOf(colorHex)
  if (index > -1) {
    option.values.splice(index, 1)
  }
}

const handleBackspace = (optionIndex) => {
  const option = form.options[optionIndex]
  if (option.tempValue === '' && option.values.length > 0) {
    option.values.pop()
  }
}

// --- Lógica Cartesiana (Generación de Variantes) ---

// Computed: Determinar si hay atributos con valores
const hasValidOptions = computed(() => {
  return form.options.some(opt => opt.values.length > 0 && opt.name.trim() !== '')
})

// Computed: Texto dinámico del botón
const buttonText = computed(() => {
  if (!hasValidOptions.value) {
    return 'Continuar como Producto Simple'
  }
  return form.variants.length > 0 ? 'Regenerar Tabla de Variantes' : 'Generar Tabla de Variantes'
})

const generateVariants = () => {
  // Filtrar opciones que tengan valores
  const validOptions = form.options.filter(opt => opt.values.length > 0 && opt.name.trim() !== '')
  
  if (validOptions.length === 0) {
    showWarning('⚠️ Debes agregar al menos un atributo con valores (ej: Talla: S, M, L)', 4000)
    return
  }

  // Ocultar formulario simple cuando se generan variantes
  showSimpleForm.value = false

  // Algoritmo de Producto Cartesiano
  const cartesian = (args) => {
    const r = [], max = args.length - 1
    function helper(arr, i) {
      for (let j = 0, l = args[i].values.length; j < l; j++) {
        const a = arr.slice(0) // clonar
        a.push({ 
          name: args[i].name, 
          value: args[i].values[j] 
        })
        if (i === max) r.push(a)
        else helper(a, i + 1)
      }
    }
    helper([], 0)
    return r
  }

  const combinations = cartesian(validOptions)

  // Mapear combinaciones a estructura de variante
  form.variants = combinations.map(combo => {
    // Generar nombre legible: "S / Rojo"
    const variantName = combo.map(c => c.value).join(' / ')
    
    // Generar SKU sugerido: "NOM-S-ROJ"
    const skuSuffix = combo.map(c => c.value.substring(0, 3).toUpperCase()).join('-')
    // Usar SKU del formulario si existe, sino usar las primeras 3 letras del nombre
    const baseSku = form.sku ? form.sku : (form.name ? form.name.substring(0, 3).toUpperCase() : 'PROD')
    
    return {
      options: combo, // Guardar la combinación exacta
      sku: `${baseSku}-${skuSuffix}`,
      cost_price: 0,
      price: 0,
      stock: 0,
      active: true
    }
  })
}

// 🚀 Generar variantes CON scroll automático
const generateVariantsWithScroll = () => {
  generateVariants()
  
  // Scroll suave a la sección de variantes después de un pequeño delay
  setTimeout(() => {
    if (variantsSection.value) {
      variantsSection.value.scrollIntoView({ 
        behavior: 'smooth', 
        block: 'start' 
      })
    }
  }, 100)
}

// 🎯 Manejar click en el botón dinámico
const handleContinueOrGenerate = () => {
  if (!hasValidOptions.value) {
    // No hay atributos, mostrar formulario simple
    showSimpleForm.value = true
    
    // Scroll suave al formulario simple
    setTimeout(() => {
      if (simpleFormSection.value) {
        simpleFormSection.value.scrollIntoView({ 
          behavior: 'smooth', 
          block: 'start' 
        })
      }
    }, 100)
  } else {
    // Hay atributos, generar variantes
    showSimpleForm.value = false
    generateVariantsWithScroll()
  }
}

const removeVariant = (index) => {
  form.variants.splice(index, 1)
}

const getVariantName = (variant) => {
  if (!variant.options || !Array.isArray(variant.options)) {
    return variant.sku || 'Variante'
  }
  
  return variant.options
    .filter(o => o && o.value && typeof o.value === 'string')
    .map(o => o.value)
    .join(' / ') || variant.sku || 'Variante'
}

// --- Lógica de Imágenes ---
const fileInput = ref(null)
const isDragging = ref(false)

const triggerFileInput = () => {
  fileInput.value.click()
}

const handleFileChange = (event) => {
  const files = Array.from(event.target.files)
  processFiles(files)
}

const handleDrop = (event) => {
  isDragging.value = false
  const files = Array.from(event.dataTransfer.files)
  processFiles(files)
}

const processFiles = (files) => {
  files.forEach(file => {
    if (file.type.startsWith('image/')) {
      const reader = new FileReader()
      reader.onload = (e) => {
        form.images.push({
          file: file,
          preview: e.target.result
        })
      }
      reader.readAsDataURL(file)
    }
  })
}

const removeImage = (index) => {
  form.images.splice(index, 1)
}

// --- Submit ---

const handleSubmit = () => {
  // Validaciones básicas
  if (!form.name) {
    showWarning('⚠️ El nombre del producto es requerido', 3000)
    return
  }
  
  if (!form.category_id) {
    showWarning('⚠️ Debes seleccionar una categoría', 3000)
    return
  }
  
  // ✅ Manejar productos simples (sin variantes o con 1 variante sin opciones)
  if (showSimpleForm.value) {
    // Validar campos del producto simple
    if (simpleProduct.price <= 0) {
      showWarning('⚠️ El precio de venta debe ser mayor a 0', 3000)
      return
    }
    
    if (simpleProduct.stock < 0) {
      showWarning('⚠️ El stock no puede ser negativo', 3000)
      return
    }
    
    // Actualizar o crear variante simple con los datos del formulario
    form.variants = [{
      sku: form.sku || `SKU-${Date.now()}`,
      price: simpleProduct.price,
      cost_price: simpleProduct.cost,
      stock: simpleProduct.stock,
      active: true,
      options: []
    }]
  }
  
  // Si no hay variantes ni formulario simple mostrado, crear variante por defecto
  if (form.variants.length === 0) {
    form.variants = [{
      sku: form.sku || `SKU-${Date.now()}`,
      price: 0,
      cost_price: 0,
      stock: 0,
      active: true,
      options: []
    }]
  }

  // Preparar payload final
  // Detectar si es producto simple o con variantes
  const isSimpleProduct = form.variants.length === 1 && 
                         (!form.variants[0].options || form.variants[0].options.length === 0)
  
  const payload = {
    ...form,
    type: isSimpleProduct ? 'simple' : 'variable', // CRÍTICO: distinguir tipo
    store_type: 'fashion', // ✅ SIEMPRE marcar como producto de moda
    // Limpiar campos temporales y solo enviar opciones que tengan valores
    options: form.options
      .filter(o => o.values && o.values.length > 0) // Solo opciones con valores
      .map(o => ({
        name: o.name,
        values: o.values
      }))
  }

  emit('save', payload)
}

// Exponer métodos y datos al componente padre
defineExpose({
  setCategory,
  handleSubmit,
  form
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
</style>