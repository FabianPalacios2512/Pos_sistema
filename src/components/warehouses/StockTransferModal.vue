<template>
  <Teleport to="body">
    <div 
      class="fixed inset-0 bg-gray-900/60 dark:bg-black/70 backdrop-blur-sm flex items-center justify-center p-4"
      style="z-index: 99999;"
      @mousedown.self="$emit('close')">
      <div class="bg-white dark:bg-zinc-900 rounded-lg max-w-5xl w-full max-h-[90vh] overflow-hidden border border-gray-200 dark:border-zinc-800 shadow-xl dark:shadow-black/50">
        
        <!-- Header -->
        <div class="border-b border-gray-200 dark:border-zinc-800 px-6 py-4 flex items-center justify-between">
          <div class="flex items-center gap-3">
            <div class="w-9 h-9 bg-gray-100 dark:bg-zinc-800 rounded-lg flex items-center justify-center border border-gray-200 dark:border-zinc-700">
              <svg class="w-4.5 h-4.5 text-gray-600 dark:text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
              </svg>
            </div>
            <div>
              <h3 class="text-base font-semibold text-gray-900 dark:text-white">Nuevo Traslado</h3>
              <p class="text-xs text-gray-500 dark:text-zinc-500">Transfiere productos entre sedes</p>
            </div>
          </div>
          <button @click="$emit('close')" class="p-2 hover:bg-gray-100 dark:hover:bg-zinc-800 rounded-lg transition-colors">
            <svg class="w-4 h-4 text-gray-400 dark:text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
          </button>
        </div>

        <!-- Body -->
        <div class="p-6 overflow-y-auto max-h-[calc(90vh-140px)] space-y-6">
          
          <!-- Sedes Origen y Destino -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div>
              <label class="block text-xs font-semibold text-gray-700 dark:text-zinc-300 mb-2">
                Sede Origen <span class="text-rose-500">*</span>
              </label>
              <select 
                v-model="form.source_warehouse_id"
                required
                class="w-full px-3.5 py-3 border border-gray-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-gray-900 dark:text-zinc-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent transition-colors">
                <option value="">Seleccionar sede origen</option>
                <option v-for="w in availableSourceWarehouses" :key="w.id" :value="w.id">{{ w.name }}</option>
              </select>
            </div>

            <div>
              <label class="block text-xs font-semibold text-gray-700 dark:text-zinc-300 mb-2">
                Sede Destino <span class="text-rose-500">*</span>
              </label>
              <select 
                v-model="form.destination_warehouse_id"
                required
                :disabled="!form.source_warehouse_id"
                class="w-full px-3.5 py-3 border border-gray-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-gray-900 dark:text-zinc-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent transition-colors disabled:bg-gray-50 dark:disabled:bg-zinc-900 disabled:opacity-50 disabled:cursor-not-allowed">
                <option value="">Seleccionar sede destino</option>
                <option v-for="w in availableDestinationWarehouses" :key="w.id" :value="w.id">{{ w.name }}</option>
              </select>
            </div>
          </div>

          <!-- Notas -->
          <div>
            <label class="block text-xs font-semibold text-gray-700 dark:text-zinc-300 mb-2">Notas <span class="text-gray-400 dark:text-zinc-600 font-normal">(opcional)</span></label>
            <textarea 
              v-model="form.notes"
              rows="2"
              placeholder="Motivo del traslado, observaciones, etc."
              class="w-full px-3.5 py-3 border border-gray-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-gray-900 dark:text-zinc-200 placeholder-gray-400 dark:placeholder-zinc-500 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent transition-colors resize-none"
            ></textarea>
          </div>

          <!-- Productos Section -->
          <div class="border border-gray-200 dark:border-zinc-800 rounded-xl">
            <div class="flex items-center justify-between px-5 py-3.5 bg-gray-50 dark:bg-zinc-800/50 border-b border-gray-200 dark:border-zinc-800 rounded-t-xl">
              <h4 class="text-xs font-semibold text-gray-700 dark:text-zinc-300 uppercase tracking-wide">Productos a Trasladar</h4>
              <button 
                type="button"
                @click="addProductRow"
                :disabled="!form.source_warehouse_id || availableProducts.length === 0"
                class="px-3.5 py-2 text-xs font-medium rounded-lg border border-gray-300 dark:border-zinc-700 text-gray-700 dark:text-zinc-300 bg-white dark:bg-zinc-800 hover:bg-gray-50 dark:hover:bg-zinc-700 transition-colors flex items-center gap-1.5 disabled:opacity-40 disabled:cursor-not-allowed">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"></path>
                </svg>
                Agregar Producto
              </button>
            </div>

            <!-- Empty state -->
            <div v-if="form.items.length === 0" class="py-12 text-center">
              <svg class="w-10 h-10 mx-auto text-gray-300 dark:text-zinc-700 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1">
                <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.75 7.5h16.5"></path>
              </svg>
              <p class="text-sm text-gray-400 dark:text-zinc-600">{{ !form.source_warehouse_id ? 'Selecciona una sede origen primero' : 'No hay productos agregados' }}</p>
            </div>

            <!-- Product rows -->
            <div v-else class="divide-y divide-gray-100 dark:divide-zinc-800">
              <div 
                v-for="(item, index) in form.items" 
                :key="index"
                class="px-5 py-4">
                
                <div class="flex items-start gap-4">
                  <!-- Searchable product selector -->
                  <div class="flex-1 min-w-0 relative" :ref="el => { if (el) productSearchRefs[index] = el }">
                    <label class="block text-[10px] font-medium text-gray-500 dark:text-zinc-500 uppercase tracking-wide mb-1.5">Producto</label>
                    <div class="relative">
                      <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 dark:text-zinc-500 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                      </svg>
                      <input
                        type="text"
                        :value="getItemDisplayName(item)"
                        @input="onProductSearch($event, index)"
                        @focus="openProductDropdown(index)"
                        @blur="closeProductDropdownDelayed(index)"
                        :placeholder="'Buscar por nombre, SKU o variante...'"
                        class="w-full pl-10 pr-9 py-2.5 border border-gray-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-gray-900 dark:text-zinc-200 placeholder-gray-400 dark:placeholder-zinc-500 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent transition-colors"
                      />
                      <button v-if="item.product_id" type="button" @mousedown.prevent="clearProduct(index)" 
                        class="absolute right-2.5 top-1/2 -translate-y-1/2 p-0.5 text-gray-400 hover:text-gray-600 dark:text-zinc-500 dark:hover:text-zinc-300">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path></svg>
                      </button>
                    </div>
                    
                    <!-- Dropdown -->
                    <div v-if="activeDropdownIndex === index && filteredProductsForRow(index).length > 0"
                      class="absolute z-[99999] mt-1.5 w-full max-h-64 overflow-y-auto bg-white dark:bg-zinc-800 border border-gray-200 dark:border-zinc-700 rounded-lg shadow-xl dark:shadow-black/50">
                      
                      <template v-for="entry in filteredProductsForRow(index)" :key="entry._key">
                        <!-- Product header for variable products -->
                        <div v-if="entry._isHeader" class="px-3.5 py-2 bg-gray-50 dark:bg-zinc-900/50 border-b border-gray-100 dark:border-zinc-700/50 sticky top-0">
                          <div class="flex items-center gap-2">
                            <span class="px-1.5 py-0.5 bg-purple-50 dark:bg-purple-950 text-purple-700 dark:text-purple-400 text-[9px] font-bold uppercase tracking-wide rounded border border-purple-100 dark:border-purple-800">Variable</span>
                            <span class="text-xs font-semibold text-gray-700 dark:text-zinc-300 truncate">{{ entry.name }}</span>
                            <span class="text-[10px] text-gray-400 dark:text-zinc-500 font-mono ml-auto flex-shrink-0">{{ entry.sku || '' }}</span>
                          </div>
                        </div>
                        
                        <!-- Variant row (indented) -->
                        <button v-else-if="entry._isVariant"
                          type="button"
                          @mousedown.prevent="selectVariant(index, entry)"
                          class="w-full text-left px-3.5 pl-7 py-2.5 hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-colors flex items-center justify-between gap-3 border-b border-gray-50 dark:border-zinc-800/50">
                          <div class="min-w-0 flex-1">
                            <div class="flex items-center gap-1.5 flex-wrap">
                              <template v-for="(opt, optIdx) in normalizeOptions(entry.options_summary)" :key="optIdx">
                                <span v-if="opt.name.toLowerCase() === 'color' && String(opt.value).startsWith('#')"
                                  class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded text-[10px] font-medium bg-gray-100 dark:bg-zinc-700 text-gray-700 dark:text-zinc-300 border border-gray-200 dark:border-zinc-600">
                                  <span class="w-3 h-3 rounded-full border border-gray-300 dark:border-zinc-500 flex-shrink-0" :style="{ backgroundColor: opt.value }"></span>
                                  {{ opt.name }}: {{ hexToColorName(opt.value) }}
                                </span>
                                <span v-else
                                  class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-medium bg-gray-100 dark:bg-zinc-700 text-gray-700 dark:text-zinc-300 border border-gray-200 dark:border-zinc-600">
                                  {{ opt.name }}: {{ opt.value }}
                                </span>
                              </template>
                            </div>
                            <p class="text-[10px] text-gray-400 dark:text-zinc-500 font-mono mt-0.5">{{ entry.variant_sku || '' }}</p>
                          </div>
                          <span class="text-[11px] font-semibold tabular-nums flex-shrink-0 px-2 py-0.5 rounded-md border"
                            :class="entry.variant_stock > 0 ? 'bg-emerald-50 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-400 border-emerald-100 dark:border-emerald-800' : 'bg-gray-100 dark:bg-zinc-800 text-gray-400 dark:text-zinc-600 border-gray-200 dark:border-zinc-700'">
                            {{ entry.variant_stock }} uds
                          </span>
                        </button>

                        <!-- Simple product row -->
                        <button v-else
                          type="button"
                          @mousedown.prevent="selectProduct(index, entry)"
                          class="w-full text-left px-3.5 py-2.5 hover:bg-gray-50 dark:hover:bg-zinc-700 transition-colors flex items-center justify-between gap-3 border-b border-gray-50 dark:border-zinc-800/50">
                          <div class="min-w-0 flex-1">
                            <p class="text-sm font-medium text-gray-900 dark:text-zinc-200 truncate">{{ entry.name }}</p>
                            <p class="text-[10px] text-gray-400 dark:text-zinc-500 font-mono">{{ entry.barcode || entry.sku || entry.code || '' }}</p>
                          </div>
                          <span class="text-[11px] font-semibold tabular-nums flex-shrink-0 px-2 py-0.5 rounded-md bg-gray-100 dark:bg-zinc-700 text-gray-600 dark:text-zinc-300 border border-gray-200 dark:border-zinc-600">
                            {{ entry.stock }} uds
                          </span>
                        </button>
                      </template>
                    </div>

                    <div v-else-if="activeDropdownIndex === index && (item._searchQuery || '').length >= 1 && filteredProductsForRow(index).length === 0"
                      class="absolute z-[99999] mt-1.5 w-full bg-white dark:bg-zinc-800 border border-gray-200 dark:border-zinc-700 rounded-lg shadow-xl p-4 text-center">
                      <p class="text-xs text-gray-400 dark:text-zinc-500">Sin resultados para "{{ item._searchQuery }}"</p>
                    </div>

                    <!-- Selected variant badge -->
                    <div v-if="item.product_variant_id && (item._variantOptions?.length || item._variantLabel)" class="mt-1.5 flex items-center gap-1.5 flex-wrap">
                      <span class="px-1.5 py-0.5 bg-purple-50 dark:bg-purple-950 text-purple-700 dark:text-purple-400 text-[10px] font-semibold rounded border border-purple-100 dark:border-purple-800">
                        Variante
                      </span>
                      <template v-if="item._variantOptions?.length" v-for="(opt, optIdx) in item._variantOptions" :key="optIdx">
                        <span v-if="opt.name.toLowerCase() === 'color' && String(opt.value).startsWith('#')"
                          class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded bg-gray-100 dark:bg-zinc-700 border border-gray-200 dark:border-zinc-600">
                          <span class="w-3.5 h-3.5 rounded-full border border-gray-300 dark:border-zinc-500 flex-shrink-0 shadow-inner" :style="{ backgroundColor: opt.value }"></span>
                          <span class="text-[10px] font-medium text-gray-700 dark:text-zinc-300">{{ opt.name }}: {{ hexToColorName(opt.value) }}</span>
                        </span>
                        <span v-else
                          class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-medium bg-gray-100 dark:bg-zinc-700 text-gray-700 dark:text-zinc-300 border border-gray-200 dark:border-zinc-600">
                          {{ opt.name }}: {{ opt.value }}
                        </span>
                      </template>
                      <span v-else class="text-[11px] text-gray-600 dark:text-zinc-400">{{ item._variantLabel }}</span>
                    </div>
                  </div>

                  <!-- Quantity -->
                  <div class="w-28 flex-shrink-0">
                    <label class="block text-[10px] font-medium text-gray-500 dark:text-zinc-500 uppercase tracking-wide mb-1.5">Cantidad</label>
                    <input 
                      v-model.number="item.quantity"
                      type="number"
                      min="1"
                      :max="getItemStock(item)"
                      required
                      placeholder="Cant."
                      class="w-full px-3 py-2.5 border border-gray-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-gray-900 dark:text-zinc-200 placeholder-gray-400 dark:placeholder-zinc-500 rounded-lg text-sm text-center tabular-nums focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent transition-colors">
                  </div>

                  <!-- Stock indicator -->
                  <div v-if="item.product_id" class="w-20 text-center flex-shrink-0 pt-5">
                    <span class="text-[10px] font-medium text-gray-400 dark:text-zinc-500 block mb-0.5">Disponible</span>
                    <span class="text-sm font-bold tabular-nums" :class="getItemStock(item) <= 5 ? 'text-amber-600 dark:text-amber-400' : 'text-gray-700 dark:text-zinc-300'">
                      {{ getItemStock(item) }}
                    </span>
                  </div>

                  <!-- Remove -->
                  <div class="flex-shrink-0 pt-5">
                    <button type="button" @click="removeProduct(index)"
                      class="p-2 text-gray-400 dark:text-zinc-600 hover:text-rose-600 dark:hover:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-900/20 rounded-lg transition-colors">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                      </svg>
                    </button>
                  </div>
                </div>
              </div>
            </div>

            <!-- Summary bar -->
            <div v-if="form.items.length > 0" class="bg-gray-50 dark:bg-zinc-800/50 border-t border-gray-200 dark:border-zinc-800 px-5 py-3 flex items-center justify-between rounded-b-xl">
              <span class="text-xs text-gray-500 dark:text-zinc-500">{{ form.items.filter(i => i.product_id).length }} producto(s) seleccionado(s)</span>
              <span class="text-sm font-bold text-gray-700 dark:text-zinc-300 tabular-nums">Total: {{ totalQuantity }} unidades</span>
            </div>
          </div>

          <!-- Info notice -->
          <div class="flex items-start gap-3 px-4 py-3 bg-amber-50 dark:bg-amber-950/30 border border-amber-200 dark:border-amber-800/40 rounded-lg">
            <svg class="w-4 h-4 text-amber-600 dark:text-amber-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
              <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"></path>
            </svg>
            <p class="text-xs text-amber-800 dark:text-amber-300 leading-relaxed">
              El traslado se creará como <span class="font-semibold">"Pendiente"</span>. Deberás completarlo manualmente para que el stock se mueva entre las sedes.
            </p>
          </div>

        </div>

        <!-- Footer -->
        <div class="border-t border-gray-200 dark:border-zinc-800 px-6 py-4 flex justify-end gap-3">
          <button 
            @click="$emit('close')"
            type="button"
            class="px-5 py-2.5 bg-white dark:bg-zinc-800 border border-gray-300 dark:border-zinc-700 text-gray-700 dark:text-zinc-300 text-sm font-medium rounded-lg hover:bg-gray-50 dark:hover:bg-zinc-700 transition-colors">
            Cancelar
          </button>
          <button 
            @click="handleSubmit"
            :disabled="saving || !canSubmit"
            class="px-6 py-2.5 bg-slate-900 dark:bg-slate-700 hover:bg-black dark:hover:bg-slate-600 text-white text-sm font-semibold rounded-lg shadow-sm transition-colors disabled:opacity-40 disabled:cursor-not-allowed flex items-center gap-2">
            <svg v-if="saving" class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span>{{ saving ? 'Creando...' : 'Crear Traslado' }}</span>
          </button>
        </div>

      </div>
    </div>
  </Teleport>
</template>

<script setup>
import { ref, computed, watch, reactive } from 'vue'
import { stockTransferService } from '@/services/stockTransferService'
import { warehouseService } from '@/services/warehouseService'
import { useToast } from '@/composables/useToast'

const props = defineProps({
  warehouses: {
    type: Array,
    required: true
  }
})

const emit = defineEmits(['close', 'saved'])

const { showSuccess, showError, showWarning } = useToast()
const saving = ref(false)
const availableProducts = ref([])
const warehouseInventory = ref({})
const variantInventory = ref({})

// Searchable dropdown state
const activeDropdownIndex = ref(-1)
const productSearchRefs = reactive({})
let dropdownCloseTimer = null

const form = ref({
  source_warehouse_id: '',
  destination_warehouse_id: '',
  notes: '',
  items: []
})

const availableSourceWarehouses = computed(() => {
  return props.warehouses.filter(w => w.active)
})

const availableDestinationWarehouses = computed(() => {
  return props.warehouses.filter(w => 
    w.active && w.id !== form.value.source_warehouse_id
  )
})

const canSubmit = computed(() => {
  return form.value.source_warehouse_id &&
         form.value.destination_warehouse_id &&
         form.value.items.length > 0 &&
         form.value.items.every(item => item.product_id && item.quantity > 0)
})

const totalQuantity = computed(() => {
  return form.value.items.reduce((sum, item) => sum + (item.quantity || 0), 0)
})

// Normalize options_summary to [{name, value}] format
function normalizeOptions(optionsSummary) {
  if (!optionsSummary) return []
  if (Array.isArray(optionsSummary)) {
    return optionsSummary.filter(o => o && o.name)
  }
  if (typeof optionsSummary === 'object') {
    return Object.entries(optionsSummary).map(([k, v]) => ({ name: k, value: v }))
  }
  return []
}

function hexToColorName(hex) {
  const map = {
    '#FF0000': 'Rojo', '#DC2626': 'Rojo', '#EF4444': 'Rojo', '#B91C1C': 'Rojo',
    '#FF6600': 'Naranja', '#EA580C': 'Naranja', '#F97316': 'Naranja',
    '#FFFF00': 'Amarillo', '#EAB308': 'Amarillo', '#CA8A04': 'Amarillo',
    '#00FF00': 'Verde', '#16A34A': 'Verde', '#22C55E': 'Verde', '#15803D': 'Verde',
    '#008000': 'Verde Oscuro', '#166534': 'Verde Oscuro',
    '#0000FF': 'Azul', '#2563EB': 'Azul', '#3B82F6': 'Azul', '#1D4ED8': 'Azul',
    '#1133DF': 'Azul', '#1E3A8A': 'Azul Oscuro', '#1E40AF': 'Azul Oscuro',
    '#800080': 'Morado', '#7C3AED': 'Morado', '#9333EA': 'Morado', '#6D28D9': 'Morado',
    '#FFC0CB': 'Rosa', '#EC4899': 'Rosa', '#F472B6': 'Rosa', '#FF1493': 'Rosa Fuerte', '#FF69B4': 'Rosa',
    '#FFA500': 'Naranja', '#F59E0B': 'Ámbar', '#D97706': 'Ámbar',
    '#A52A2A': 'Café', '#92400E': 'Café', '#78350F': 'Café Oscuro', '#8B4513': 'Café',
    '#FFFFFF': 'Blanco', '#F9FAFB': 'Blanco', '#F3F4F6': 'Blanco',
    '#000000': 'Negro', '#111827': 'Negro',
    '#1F2937': 'Gris Oscuro', '#374151': 'Gris Oscuro',
    '#808080': 'Gris', '#6B7280': 'Gris', '#9CA3AF': 'Gris Claro',
    '#C0C0C0': 'Plateado', '#D1D5DB': 'Gris Claro', '#E5E7EB': 'Gris Muy Claro',
    '#FFD700': 'Dorado',
    '#00FFFF': 'Cian', '#06B6D4': 'Cian', '#0891B2': 'Cian',
    '#FF00FF': 'Magenta', '#DB2777': 'Magenta',
    '#4B0082': 'Índigo', '#4F46E5': 'Índigo', '#6366F1': 'Índigo',
    '#F5F5DC': 'Beige', '#E0F2FE': 'Celeste',
  }
  const upper = hex?.toUpperCase()
  return map[upper] || map[hex] || 'Color'
}

function formatVariantLabel(optionsSummary) {
  const opts = normalizeOptions(optionsSummary)
  if (opts.length === 0) return ''
  return opts.map(o => {
    if (o.name.toLowerCase() === 'color' && String(o.value).startsWith('#')) {
      return o.name + ': ' + hexToColorName(o.value)
    }
    return o.name + ': ' + o.value
  }).join(', ')
}

// Load source warehouse inventory
watch(() => form.value.source_warehouse_id, async (warehouseId) => {
  if (warehouseId) {
    try {
      const response = await warehouseService.getInventory(warehouseId)
      const products = response.data?.products || response.products || []
      availableProducts.value = products.filter(p => p.stock > 0)
      warehouseInventory.value = {}
      variantInventory.value = {}
      availableProducts.value.forEach(p => {
        warehouseInventory.value[p.id] = p.stock
        if (p.variants && p.variants.length > 0) {
          p.variants.forEach(v => {
            variantInventory.value[p.id + '_' + v.id] = v.stock
          })
        }
      })
      form.value.items = []
    } catch (error) {
      showError('Error al cargar productos de la sede. Por favor intenta de nuevo.')
    }
  } else {
    availableProducts.value = []
    warehouseInventory.value = {}
    variantInventory.value = {}
    form.value.items = []
  }
})

const getItemStock = (item) => {
  if (item.product_variant_id) {
    return variantInventory.value[item.product_id + '_' + item.product_variant_id] || 0
  }
  return warehouseInventory.value[item.product_id] || 0
}

const getItemDisplayName = (item) => {
  if (!item.product_id) return item._searchQuery || ''
  const p = availableProducts.value.find(pr => pr.id === item.product_id)
  if (!p) return ''
  if (item.product_variant_id && item._variantLabel) {
    return p.name + ' — ' + item._variantLabel
  }
  return p.name
}

// --- Searchable dropdown methods ---
function onProductSearch(event, index) {
  const query = event.target.value
  form.value.items[index]._searchQuery = query
  form.value.items[index].product_id = ''
  form.value.items[index].product_variant_id = null
  form.value.items[index]._variantLabel = ''
  activeDropdownIndex.value = index
}

function openProductDropdown(index) {
  clearTimeout(dropdownCloseTimer)
  activeDropdownIndex.value = index
}

function closeProductDropdownDelayed(index) {
  dropdownCloseTimer = setTimeout(() => {
    if (activeDropdownIndex.value === index) {
      activeDropdownIndex.value = -1
    }
  }, 200)
}

function selectProduct(index, product) {
  form.value.items[index].product_id = product.id
  form.value.items[index].product_variant_id = null
  form.value.items[index]._variantLabel = ''
  form.value.items[index]._variantOptions = []
  form.value.items[index]._searchQuery = ''
  activeDropdownIndex.value = -1
  const maxStock = getItemStock(form.value.items[index])
  if (form.value.items[index].quantity > maxStock) {
    form.value.items[index].quantity = maxStock
  }
}

function selectVariant(index, entry) {
  form.value.items[index].product_id = entry.product_id
  form.value.items[index].product_variant_id = entry.variant_id
  form.value.items[index]._variantLabel = formatVariantLabel(entry.options_summary)
  form.value.items[index]._variantOptions = normalizeOptions(entry.options_summary)
  form.value.items[index]._searchQuery = ''
  activeDropdownIndex.value = -1
  const maxStock = getItemStock(form.value.items[index])
  if (form.value.items[index].quantity > maxStock) {
    form.value.items[index].quantity = maxStock
  }
}

function clearProduct(index) {
  form.value.items[index].product_id = ''
  form.value.items[index].product_variant_id = null
  form.value.items[index]._variantLabel = ''
  form.value.items[index]._variantOptions = []
  form.value.items[index]._searchQuery = ''
}

function filteredProductsForRow(index) {
  const query = (form.value.items[index]._searchQuery || '').toLowerCase().trim()
  const selectedKeys = form.value.items
    .filter((it, i) => i !== index && it.product_id)
    .map(it => it.product_variant_id ? it.product_id + '_v' + it.product_variant_id : String(it.product_id))
  
  let results = []
  
  for (const p of availableProducts.value) {
    const nameMatch = (p.name || '').toLowerCase().includes(query)
    const skuMatch = (p.barcode || p.sku || p.code || '').toLowerCase().includes(query)
    const matches = query.length === 0 || nameMatch || skuMatch
    
    if (!matches) {
      if (p.product_type === 'variable' && p.variants && p.variants.length > 0) {
        let anyVariantMatch = false
        for (const v of p.variants) {
          const vSkuMatch = (v.sku || '').toLowerCase().includes(query)
          let vOptMatch = false
          const opts = normalizeOptions(v.options_summary)
          vOptMatch = opts.some(o => 
            String(o.value || '').toLowerCase().includes(query) ||
            String(o.name || '').toLowerCase().includes(query)
          )
          if (vSkuMatch || vOptMatch) {
            anyVariantMatch = true
            break
          }
        }
        if (!anyVariantMatch) continue
      } else {
        continue
      }
    }
    
    if (p.product_type === 'variable' && p.variants && p.variants.length > 0) {
      const availableVariants = p.variants.filter(v => {
        const key = p.id + '_v' + v.id
        return !selectedKeys.includes(key) && v.stock > 0
      })
      
      if (availableVariants.length === 0) continue
      
      results.push({
        _key: 'header_' + p.id,
        _isHeader: true,
        name: p.name,
        sku: p.sku
      })
      
      for (const v of availableVariants) {
        if (query.length > 0 && !nameMatch && !skuMatch) {
          const vSkuMatch = (v.sku || '').toLowerCase().includes(query)
          const opts = normalizeOptions(v.options_summary)
          const vOptMatch = opts.some(o =>
            String(o.value || '').toLowerCase().includes(query) ||
            String(o.name || '').toLowerCase().includes(query)
          )
          if (!vSkuMatch && !vOptMatch) continue
        }
        
        results.push({
          _key: 'variant_' + p.id + '_' + v.id,
          _isVariant: true,
          product_id: p.id,
          variant_id: v.id,
          variant_sku: v.sku,
          variant_stock: v.stock,
          options_summary: v.options_summary,
          name: p.name
        })
      }
    } else {
      if (selectedKeys.includes(String(p.id))) continue
      
      results.push({
        _key: 'product_' + p.id,
        id: p.id,
        name: p.name,
        sku: p.sku,
        barcode: p.barcode,
        code: p.code,
        stock: p.stock
      })
    }
  }
  
  return results.slice(0, 50)
}

const addProductRow = () => {
  form.value.items.push({
    product_id: '',
    product_variant_id: null,
    quantity: 1,
    _searchQuery: '',
    _variantLabel: '',
    _variantOptions: []
  })
}

const removeProduct = (index) => {
  form.value.items.splice(index, 1)
}

const handleSubmit = async () => {
  if (!canSubmit.value) {
    showWarning('Por favor completa todos los campos requeridos')
    return
  }

  for (const item of form.value.items) {
    const availableStock = getItemStock(item)
    if (item.quantity > availableStock) {
      const product = availableProducts.value.find(p => p.id === item.product_id)
      const label = item._variantLabel ? product?.name + ' (' + item._variantLabel + ')' : product?.name
      showError('Stock insuficiente para ' + label + '. Disponible: ' + availableStock)
      return
    }
  }

  saving.value = true
  try {
    const payload = {
      source_warehouse_id: form.value.source_warehouse_id,
      destination_warehouse_id: form.value.destination_warehouse_id,
      notes: form.value.notes,
      items: form.value.items.map(item => ({
        product_id: item.product_id,
        product_variant_id: item.product_variant_id || null,
        quantity: item.quantity
      }))
    }
    await stockTransferService.create(payload)
    showSuccess('Traslado creado exitosamente')
    emit('saved')
  } catch (error) {
    showError(error.response?.data?.message || 'Error al crear el traslado')
  } finally {
    saving.value = false
  }
}
</script>