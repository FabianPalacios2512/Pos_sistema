<template>
  <div class="bg-white dark:bg-zinc-900 rounded-2xl shadow-xl dark:shadow-black/50 border border-gray-200 dark:border-zinc-800 overflow-hidden h-full flex flex-col">
    
    <!-- Header del Formulario -->
    <div class="px-6 py-4 border-b border-gray-200 dark:border-zinc-800 flex justify-between items-center bg-white dark:bg-zinc-900">
      <div class="flex items-center gap-3">
        <h3 class="text-xl font-bold text-gray-900 dark:text-white">Nuevo Producto de Moda</h3>
        <span class="px-3 py-1 bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300 text-xs font-bold rounded-full border border-purple-200 dark:border-purple-800">
          Moda & Variantes Activo
        </span>
      </div>
      <button @click="$emit('cancel')" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
      </button>
    </div>

    <form @submit.prevent="handleSubmit" class="flex-1 overflow-y-auto p-6 space-y-8">
      
      <!-- SECCIÓN 1: INFORMACIÓN BÁSICA Y MULTIMEDIA -->
      <section class="space-y-4">
        <h4 class="text-sm font-bold text-gray-900 dark:text-white uppercase tracking-wide border-b border-gray-100 dark:border-zinc-800 pb-2">
          1. Detalles Generales y Multimedia
        </h4>
        
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
          <!-- Columna Izquierda: Inputs (2/3 ancho) -->
          <div class="lg:col-span-2 space-y-5">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
              <!-- Nombre -->
              <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 dark:text-zinc-300 mb-1.5">Nombre del Producto <span class="text-red-500">*</span></label>
                <input 
                  v-model="form.name"
                  type="text" 
                  required
                  placeholder="Ej: Camiseta Polo Premium"
                  class="w-full px-4 py-2.5 bg-gray-50 dark:bg-zinc-800 border border-gray-300 dark:border-zinc-700 rounded-xl focus:ring-2 focus:ring-purple-500 dark:focus:ring-purple-400 focus:border-transparent outline-none transition-all text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-zinc-500 shadow-sm"
                >
              </div>

              <!-- Categoría -->
              <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-zinc-300 mb-1.5">Categoría</label>
                <select 
                  v-model="form.category_id"
                  @change="handleCategoryChange"
                  class="w-full px-4 py-2.5 bg-gray-50 dark:bg-zinc-800 border border-gray-300 dark:border-zinc-700 rounded-xl focus:ring-2 focus:ring-purple-500 dark:focus:ring-purple-400 outline-none text-gray-900 dark:text-white shadow-sm"
                >
                  <option value="" disabled>Seleccionar...</option>
                  <option value="__new__" class="font-bold text-purple-600 dark:text-purple-400">+ Nueva Categoría</option>
                  <option v-for="cat in categories" :key="cat.id" :value="cat.id">
                    {{ cat.name }}
                  </option>
                </select>
              </div>

              <!-- SKU -->
              <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-zinc-300 mb-1.5">SKU (Código Referencia)</label>
                <input 
                  v-model="form.sku"
                  type="text" 
                  placeholder="Auto-generado si vacío"
                  class="w-full px-4 py-2.5 bg-gray-50 dark:bg-zinc-800 border border-gray-300 dark:border-zinc-700 rounded-xl focus:ring-2 focus:ring-purple-500 dark:focus:ring-purple-400 focus:border-transparent outline-none transition-all text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-zinc-500 shadow-sm"
                >
              </div>

              <!-- Precio Costo -->
              <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-zinc-300 mb-1.5">Precio de Costo Base <span class="text-red-500">*</span></label>
                <div class="relative">
                  <span class="absolute left-3 top-2.5 text-gray-500 dark:text-zinc-400">$</span>
                  <input 
                    v-model.number="form.cost_price"
                    type="number" 
                    min="0"
                    step="0.01"
                    required
                    class="w-full pl-7 pr-4 py-2.5 bg-gray-50 dark:bg-zinc-800 border border-gray-300 dark:border-zinc-700 rounded-xl focus:ring-2 focus:ring-purple-500 dark:focus:ring-purple-400 focus:border-transparent outline-none transition-all text-gray-900 dark:text-white shadow-sm"
                  >
                </div>
              </div>

              <!-- Descripción -->
              <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 dark:text-zinc-300 mb-1.5">Descripción</label>
                <textarea 
                  v-model="form.description"
                  rows="3"
                  placeholder="Detalles del material, ajuste, etc."
                  class="w-full px-4 py-2.5 bg-gray-50 dark:bg-zinc-800 border border-gray-300 dark:border-zinc-700 rounded-xl focus:ring-2 focus:ring-purple-500 dark:focus:ring-purple-400 outline-none transition-all text-gray-900 dark:text-white resize-none shadow-sm"
                ></textarea>
              </div>
            </div>
          </div>

          <!-- Columna Derecha: Imágenes (1/3 ancho) -->
          <div class="lg:col-span-1">
            <label class="block text-sm font-medium text-gray-700 dark:text-zinc-300 mb-1.5">Imágenes del Producto</label>
            
            <!-- Dropzone -->
            <div 
              @click="triggerFileInput"
              @dragover.prevent="isDragging = true"
              @dragleave.prevent="isDragging = false"
              @drop.prevent="handleDrop"
              :class="[
                'border-2 border-dashed rounded-xl p-6 text-center transition-all cursor-pointer h-[280px] flex flex-col items-center justify-center gap-3',
                isDragging 
                  ? 'border-purple-500 bg-purple-50 dark:bg-purple-900/20' 
                  : 'border-gray-300 dark:border-zinc-700 hover:bg-gray-50 dark:hover:bg-zinc-800/50 bg-gray-50/50 dark:bg-zinc-800/30'
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
              
              <div class="w-16 h-16 bg-white dark:bg-zinc-800 rounded-full flex items-center justify-center shadow-sm border border-gray-100 dark:border-zinc-700">
                <svg class="w-8 h-8 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
              </div>
              
              <div>
                <p class="text-sm font-bold text-gray-700 dark:text-zinc-200">Arrastra fotos aquí</p>
                <p class="text-xs text-gray-500 dark:text-zinc-400 mt-1 px-4">Soporta JPG, PNG. Máx 5 fotos.</p>
              </div>

              <!-- Mini Previews dentro del dropzone si hay espacio o superpuestas -->
              <div v-if="form.images.length > 0" class="flex -space-x-2 mt-2">
                <div v-for="(img, index) in form.images.slice(0, 4)" :key="index" class="w-8 h-8 rounded-full border-2 border-white dark:border-zinc-900 overflow-hidden">
                  <img :src="img.preview" class="w-full h-full object-cover">
                </div>
                <div v-if="form.images.length > 4" class="w-8 h-8 rounded-full border-2 border-white dark:border-zinc-900 bg-gray-100 dark:bg-zinc-800 flex items-center justify-center text-[10px] font-bold text-gray-600">
                  +{{ form.images.length - 4 }}
                </div>
              </div>
            </div>

            <!-- Lista de Imágenes (Grid debajo) -->
            <div v-if="form.images.length > 0" class="grid grid-cols-4 gap-2 mt-3">
              <div v-for="(img, index) in form.images" :key="index" class="relative group aspect-square bg-gray-100 dark:bg-zinc-800 rounded-lg overflow-hidden border border-gray-200 dark:border-zinc-700">
                <img :src="img.preview" class="w-full h-full object-cover">
                <button 
                  type="button"
                  @click.stop="removeImage(index)"
                  class="absolute top-1 right-1 p-1 bg-red-500 text-white rounded-full opacity-0 group-hover:opacity-100 transition-opacity shadow-sm"
                >
                  <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
                <div v-if="index === 0" class="absolute bottom-0 left-0 right-0 bg-black/50 text-white text-[10px] text-center py-0.5">
                  Principal
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- SECCIÓN 2: DEFINIR VARIANTES (Tallas, Colores) -->
      <section class="space-y-4">
        <div class="flex justify-between items-end border-b border-gray-100 dark:border-zinc-800 pb-2">
          <h4 class="text-sm font-bold text-gray-900 dark:text-white uppercase tracking-wide">
            2. Definir Variantes (Tallas y Colores)
          </h4>
          <button 
            type="button" 
            @click="addOption"
            class="text-xs font-bold text-purple-600 dark:text-purple-400 hover:text-purple-800 dark:hover:text-purple-300 flex items-center gap-1 transition-colors"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Agregar Opción
          </button>
        </div>

        <div class="space-y-4 bg-gray-50 dark:bg-zinc-800/30 p-4 rounded-xl border border-gray-200 dark:border-zinc-800/50">
          <div v-if="form.options.length === 0" class="text-center py-8 text-gray-500 dark:text-zinc-500 text-sm italic flex flex-col items-center gap-2">
            <svg class="w-8 h-8 text-gray-300 dark:text-zinc-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
            <span>No hay opciones definidas. Agrega "Talla" o "Color" para comenzar.</span>
          </div>

          <div v-for="(option, index) in form.options" :key="index" class="bg-white dark:bg-zinc-900 p-4 rounded-lg border border-gray-200 dark:border-zinc-700 shadow-sm relative group">
            <!-- Botón Eliminar Opción -->
            <button 
              type="button" 
              @click="removeOption(index)"
              class="absolute top-2 right-2 text-gray-400 hover:text-red-500 transition-colors p-1"
            >
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>

            <div class="grid grid-cols-1 md:grid-cols-12 gap-4 items-start">
              <!-- Nombre de la Opción -->
              <div class="md:col-span-3">
                <label class="block text-xs font-bold text-gray-500 dark:text-zinc-400 mb-1">Nombre Opción</label>
                <input 
                  v-model="option.name"
                  type="text" 
                  placeholder="Ej: Talla, Color"
                  class="w-full px-3 py-2 bg-gray-50 dark:bg-zinc-800 border border-gray-300 dark:border-zinc-700 rounded-lg text-sm font-bold text-gray-900 dark:text-white focus:ring-2 focus:ring-purple-500 outline-none"
                >
              </div>

              <!-- Valores (Tags Input) -->
              <div class="md:col-span-9">
                <label class="block text-xs font-bold text-gray-500 dark:text-zinc-400 mb-1">Valores (Presiona Enter)</label>
                <div class="flex flex-wrap gap-2 p-2 bg-gray-50 dark:bg-zinc-800 border border-gray-300 dark:border-zinc-700 rounded-lg min-h-[42px]">
                  
                  <!-- Tags existentes -->
                  <span 
                    v-for="(val, vIndex) in option.values" 
                    :key="vIndex"
                    class="inline-flex items-center px-2.5 py-0.5 rounded-md text-sm font-medium bg-purple-100 dark:bg-purple-900/40 text-purple-800 dark:text-purple-200 border border-purple-200 dark:border-purple-800"
                  >
                    {{ val }}
                    <button type="button" @click="removeValue(index, vIndex)" class="ml-1.5 text-purple-600 dark:text-purple-400 hover:text-purple-800 focus:outline-none">×</button>
                  </span>

                  <!-- Input para nuevos tags -->
                  <input 
                    type="text"
                    v-model="option.tempValue"
                    @keydown.enter.prevent="addOptionValue(index)"
                    @keydown.backspace="handleBackspace(index)"
                    placeholder="Escribe y Enter..."
                    class="flex-1 bg-transparent border-none focus:ring-0 text-sm text-gray-900 dark:text-white min-w-[120px] p-0"
                  >
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Botón Generar (Full Width) -->
        <button 
          type="button"
          @click="generateVariants"
          :disabled="form.options.length === 0"
          class="w-full py-3 bg-purple-600 hover:bg-purple-700 text-white text-sm font-bold rounded-xl shadow-lg shadow-purple-500/30 transition-all disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.384-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/></svg>
          Generar Matriz de Variantes
        </button>
      </section>

      <!-- SECCIÓN 3: TABLA DE VARIANTES -->
      <section class="space-y-4 animate-fade-in">
        <h4 class="text-sm font-bold text-gray-900 dark:text-white uppercase tracking-wide border-b border-gray-100 dark:border-zinc-800 pb-2">
          3. Variantes Generadas <span v-if="form.variants.length > 0">({{ form.variants.length }})</span>
        </h4>

        <!-- Placeholder si no hay variantes -->
        <div v-if="form.variants.length === 0" class="bg-gray-50 dark:bg-zinc-800/30 border-2 border-dashed border-gray-200 dark:border-zinc-700 rounded-xl p-8 text-center">
          <div class="w-12 h-12 bg-gray-100 dark:bg-zinc-800 rounded-full flex items-center justify-center mx-auto mb-3">
            <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
          </div>
          <p class="text-gray-500 dark:text-zinc-400 font-medium">La matriz de variantes aparecerá aquí</p>
          <p class="text-xs text-gray-400 dark:text-zinc-500 mt-1">Define opciones arriba y haz clic en "Generar"</p>
        </div>

        <!-- Tabla de Variantes -->
        <div v-else class="overflow-x-auto rounded-xl border border-gray-200 dark:border-zinc-800 shadow-sm">
          <table class="min-w-full divide-y divide-gray-200 dark:divide-zinc-800">
            <thead class="bg-gray-50 dark:bg-zinc-900">
              <tr>
                <th scope="col" class="px-4 py-3 text-left text-xs font-bold text-gray-500 dark:text-zinc-400 uppercase tracking-wider">Variación</th>
                <th scope="col" class="px-4 py-3 text-left text-xs font-bold text-gray-500 dark:text-zinc-400 uppercase tracking-wider w-40">SKU</th>
                <th scope="col" class="px-4 py-3 text-left text-xs font-bold text-gray-500 dark:text-zinc-400 uppercase tracking-wider w-32">Precio</th>
                <th scope="col" class="px-4 py-3 text-left text-xs font-bold text-gray-500 dark:text-zinc-400 uppercase tracking-wider w-24">Stock</th>
                <th scope="col" class="px-4 py-3 text-center text-xs font-bold text-gray-500 dark:text-zinc-400 uppercase tracking-wider w-16">Acción</th>
              </tr>
            </thead>
            <tbody class="bg-white dark:bg-zinc-900 divide-y divide-gray-200 dark:divide-zinc-800">
              <tr v-for="(variant, index) in form.variants" :key="index" class="hover:bg-gray-50 dark:hover:bg-zinc-800/50 transition-colors">
                <td class="px-4 py-3 whitespace-nowrap">
                  <div class="flex items-center gap-2">
                    <span class="text-sm font-medium text-gray-900 dark:text-white">
                      {{ getVariantName(variant) }}
                    </span>
                  </div>
                </td>
                <td class="px-4 py-2">
                  <input 
                    v-model="variant.sku"
                    type="text" 
                    class="w-full px-2 py-1.5 text-xs border border-gray-300 dark:border-zinc-700 rounded bg-white dark:bg-zinc-800 text-gray-900 dark:text-white focus:ring-1 focus:ring-purple-500"
                  >
                </td>
                <td class="px-4 py-2">
                  <div class="relative">
                    <span class="absolute left-2 top-1.5 text-xs text-gray-500">$</span>
                    <input 
                      v-model.number="variant.price"
                      type="number" 
                      class="w-full pl-5 pr-2 py-1.5 text-xs border border-gray-300 dark:border-zinc-700 rounded bg-white dark:bg-zinc-800 text-gray-900 dark:text-white focus:ring-1 focus:ring-purple-500"
                    >
                  </div>
                </td>
                <td class="px-4 py-2">
                  <input 
                    v-model.number="variant.stock"
                    type="number" 
                    class="w-full px-2 py-1.5 text-xs border border-gray-300 dark:border-zinc-700 rounded bg-white dark:bg-zinc-800 text-gray-900 dark:text-white focus:ring-1 focus:ring-purple-500"
                  >
                </td>
                <td class="px-4 py-2 text-center">
                  <button 
                    type="button" 
                    @click="removeVariant(index)"
                    class="text-gray-400 hover:text-red-500 transition-colors"
                  >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-2.002-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </section>

      <!-- Footer Actions -->
      <div class="pt-6 border-t border-gray-200 dark:border-zinc-800 flex justify-end gap-3 bg-white dark:bg-zinc-900 sticky bottom-0 z-10">
        <button 
          type="button"
          @click="$emit('cancel')"
          class="px-5 py-2.5 bg-white dark:bg-zinc-900 hover:bg-gray-50 dark:hover:bg-zinc-800 text-gray-700 dark:text-zinc-200 text-sm font-bold rounded-xl border border-gray-200 dark:border-zinc-700 shadow-sm transition-all"
        >
          Cancelar
        </button>
        <button 
          type="submit"
          :disabled="form.variants.length === 0"
          class="px-6 py-2.5 bg-slate-900 dark:bg-slate-700 hover:bg-black dark:hover:bg-slate-600 text-white text-sm font-bold rounded-xl shadow-lg shadow-slate-400/40 dark:shadow-slate-900/50 transition-all duration-300 disabled:opacity-50 disabled:cursor-not-allowed"
        >
          Guardar Producto
        </button>
      </div>

    </form>
  </div>
</template>

<script setup>
import { reactive, computed, ref } from 'vue'

const props = defineProps({
  categories: {
    type: Array,
    default: () => []
  },
  suppliers: {
    type: Array,
    default: () => []
  }
})

const emit = defineEmits(['save', 'cancel', 'create-category'])

// Estado Reactivo del Formulario
const form = reactive({
  name: '',
  sku: '', // Nuevo campo SKU
  description: '',
  category_id: '',
  cost_price: 0, // Nuevo campo requerido
  options: [
    { name: 'Talla', values: [], tempValue: '' }, // Opción por defecto
    { name: 'Color', values: [], tempValue: '' }  // Opción por defecto
  ],
  variants: [],
  images: []
})

const handleCategoryChange = (event) => {
  if (event.target.value === '__new__') {
    form.category_id = '' // Reset selection
    emit('create-category')
  }
}

const setCategory = (id) => {
  form.category_id = id
}

defineExpose({
  setCategory
})

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

const handleBackspace = (optionIndex) => {
  const option = form.options[optionIndex]
  if (option.tempValue === '' && option.values.length > 0) {
    option.values.pop()
  }
}

// --- Lógica Cartesiana (Generación de Variantes) ---

const generateVariants = () => {
  // Filtrar opciones que tengan valores
  const validOptions = form.options.filter(opt => opt.values.length > 0 && opt.name.trim() !== '')
  
  if (validOptions.length === 0) {
    alert('Debes agregar al menos una opción con valores (ej: Talla: S, M)')
    return
  }

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
      price: 0,
      stock: 0,
      active: true
    }
  })
}

const removeVariant = (index) => {
  form.variants.splice(index, 1)
}

const getVariantName = (variant) => {
  return variant.options.map(o => o.value).join(' / ')
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
    alert('El nombre es requerido')
    return
  }
  if (form.variants.length === 0) {
    alert('Debes generar al menos una variante')
    return
  }

  // Preparar payload final
  const payload = {
    ...form,
    type: 'variable', // CRÍTICO para el backend
    // Limpiar campos temporales antes de enviar
    options: form.options.map(o => ({
      name: o.name,
      values: o.values
    }))
  }

  console.log('📤 Enviando producto variable:', payload)
  emit('save', payload)
}
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