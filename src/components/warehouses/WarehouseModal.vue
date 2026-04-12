<template>
  <div class="fixed inset-0 bg-black/50 backdrop-blur-[2px] flex items-center justify-center z-50 p-4" @click.self="$emit('close')">
    <Transition
      enter-active-class="transition ease-out duration-200"
      enter-from-class="opacity-0 scale-[0.97]"
      enter-to-class="opacity-100 scale-100"
      leave-active-class="transition ease-in duration-150"
      leave-from-class="opacity-100 scale-100"
      leave-to-class="opacity-0 scale-[0.97]"
    >
    <div class="bg-white dark:bg-zinc-900 rounded-2xl max-w-[640px] w-full overflow-hidden border border-gray-200 dark:border-zinc-800 shadow-2xl dark:shadow-black/50">
      
      <!-- Header -->
      <div class="px-7 py-5 border-b border-gray-100 dark:border-zinc-800 flex items-center justify-between">
        <div>
          <h3 class="text-lg font-bold text-gray-900 dark:text-white">
            {{ isEditing ? 'Editar Sede' : 'Nueva Sede' }}
          </h3>
          <p class="text-sm text-gray-500 dark:text-zinc-500 mt-0.5">Información de la sucursal</p>
        </div>
        <button 
          @click="$emit('close')"
          class="p-2 hover:bg-gray-100 dark:hover:bg-zinc-800 rounded-lg transition-colors duration-150">
          <svg class="w-5 h-5 text-gray-400 dark:text-zinc-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
          </svg>
        </button>
      </div>

      <!-- Body -->
      <div class="px-7 py-6">
        <form @submit.prevent="handleSubmit" class="space-y-5">
          
          <!-- Fila 1: Nombre + Teléfono -->
          <div class="grid grid-cols-3 gap-4">
            <div class="col-span-2">
              <label class="block text-[13px] font-semibold text-gray-700 dark:text-zinc-300 mb-1.5">
                Nombre de la Sede <span class="text-rose-500">*</span>
              </label>
              <input 
                v-model="form.name"
                type="text" 
                required
                placeholder="Ej: Sede Centro, Sucursal Norte"
                class="w-full px-4 py-3 border border-gray-200 dark:border-zinc-700 rounded-lg bg-white dark:bg-zinc-800 text-gray-900 dark:text-zinc-200 placeholder-gray-400 dark:placeholder-zinc-500 focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 text-[15px] transition-all duration-200"
              />
            </div>
            <div>
              <label class="block text-[13px] font-semibold text-gray-700 dark:text-zinc-300 mb-1.5">
                Teléfono
              </label>
              <input 
                v-model="form.phone"
                type="tel" 
                placeholder="3001234567"
                class="w-full px-4 py-3 border border-gray-200 dark:border-zinc-700 rounded-lg bg-white dark:bg-zinc-800 text-gray-900 dark:text-zinc-200 placeholder-gray-400 dark:placeholder-zinc-500 focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 text-[15px] transition-all duration-200"
              />
            </div>
          </div>

          <!-- Fila 2: Dirección full width -->
          <div>
            <label class="block text-[13px] font-semibold text-gray-700 dark:text-zinc-300 mb-1.5">
              Dirección
            </label>
            <input 
              v-model="form.address"
              type="text" 
              placeholder="Calle 123 #45-67, Barrio, Ciudad"
              class="w-full px-4 py-3 border border-gray-200 dark:border-zinc-700 rounded-lg bg-white dark:bg-zinc-800 text-gray-900 dark:text-zinc-200 placeholder-gray-400 dark:placeholder-zinc-500 focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 text-[15px] transition-all duration-200"
            />
          </div>

          <!-- Toggles en fila horizontal -->
          <div class="flex items-center gap-8 pt-2">
            <!-- Sede principal -->
            <label class="flex items-center gap-3 cursor-pointer group">
              <button 
                type="button"
                @click="form.is_default = !form.is_default"
                :class="[
                  'relative inline-flex h-6 w-11 items-center rounded-full transition-colors duration-200 flex-shrink-0',
                  form.is_default ? 'bg-blue-600 dark:bg-blue-500' : 'bg-gray-300 dark:bg-zinc-700'
                ]">
                <span :class="[
                  'inline-block h-4 w-4 transform rounded-full bg-white shadow-sm transition-transform duration-200',
                  form.is_default ? 'translate-x-[22px]' : 'translate-x-[3px]'
                ]" />
              </button>
              <div>
                <span class="text-sm font-medium text-gray-900 dark:text-zinc-200 group-hover:text-gray-700 dark:group-hover:text-white transition-colors">Sede principal</span>
                <p class="text-xs text-gray-400 dark:text-zinc-500">Se usa por defecto al abrir caja</p>
              </div>
            </label>

            <!-- Separador -->
            <div class="h-9 w-px bg-gray-200 dark:bg-zinc-800"></div>

            <!-- Sede activa -->
            <label class="flex items-center gap-3 cursor-pointer group">
              <button 
                type="button"
                @click="form.active = !form.active"
                :class="[
                  'relative inline-flex h-6 w-11 items-center rounded-full transition-colors duration-200 flex-shrink-0',
                  form.active ? 'bg-emerald-600 dark:bg-emerald-500' : 'bg-gray-300 dark:bg-zinc-700'
                ]">
                <span :class="[
                  'inline-block h-4 w-4 transform rounded-full bg-white shadow-sm transition-transform duration-200',
                  form.active ? 'translate-x-[22px]' : 'translate-x-[3px]'
                ]" />
              </button>
              <div>
                <span class="text-sm font-medium text-gray-900 dark:text-zinc-200 group-hover:text-gray-700 dark:group-hover:text-white transition-colors">Activa</span>
                <p class="text-xs text-gray-400 dark:text-zinc-500">Las inactivas no pueden operar</p>
              </div>
            </label>
          </div>

          <!-- Nota sutil -->
          <div v-if="!isEditing" class="flex items-start gap-2 pt-1">
            <svg class="w-4 h-4 text-gray-400 dark:text-zinc-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <p class="text-xs text-gray-400 dark:text-zinc-500 leading-relaxed">Al crear una sede, los productos se agregarán con stock 0. Podrás ajustar inventario o hacer un traslado después.</p>
          </div>

        </form>
      </div>

      <!-- Footer -->
      <div class="px-7 py-5 border-t border-gray-100 dark:border-zinc-800/60 flex items-center justify-end gap-3">
        <button 
          @click="$emit('close')"
          type="button"
          class="px-6 py-2.5 text-sm font-medium text-gray-500 dark:text-zinc-400 hover:text-gray-700 dark:hover:text-zinc-200 hover:bg-gray-100 dark:hover:bg-zinc-800 rounded-xl transition-all duration-150">
          Cancelar
        </button>
        <button 
          @click="handleSubmit"
          :disabled="saving || !form.name"
          class="px-7 py-2.5 bg-slate-900 dark:bg-slate-700 hover:bg-black dark:hover:bg-slate-600 text-white text-sm font-bold rounded-xl shadow-sm transition-all duration-150 disabled:bg-gray-200 dark:disabled:bg-zinc-800 disabled:text-gray-400 dark:disabled:text-zinc-600 disabled:shadow-none active:scale-[0.98] flex items-center gap-2">
          <svg v-if="saving" class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
          </svg>
          <span>{{ saving ? 'Guardando...' : isEditing ? 'Actualizar' : 'Crear Sede' }}</span>
        </button>
      </div>

    </div>
    </Transition>
  </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue';
import { warehouseService } from '@/services/warehouseService';
import { useToast } from '@/composables/useToast';

const props = defineProps({
  warehouse: {
    type: Object,
    default: null
  }
});

const emit = defineEmits(['close', 'saved']);

const { showSuccess, showError } = useToast();
const saving = ref(false);
const form = ref({
  name: '',
  address: '',
  phone: '',
  is_default: false,
  active: true
});

const isEditing = computed(() => !!props.warehouse);

// Cargar datos si es edición
watch(() => props.warehouse, (newWarehouse) => {
  if (newWarehouse) {
    form.value = {
      name: newWarehouse.name || '',
      address: newWarehouse.address || '',
      phone: newWarehouse.phone || '',
      is_default: newWarehouse.is_default || false,
      active: newWarehouse.active ?? true
    };
  }
}, { immediate: true });

const handleSubmit = async () => {
  if (!form.value.name) {
    showError('El nombre de la sede es obligatorio');
    return;
  }

  saving.value = true;
  try {
    if (isEditing.value) {
      await warehouseService.update(props.warehouse.id, form.value);
      showSuccess('Sede actualizada correctamente');
    } else {
      await warehouseService.create(form.value);
      showSuccess('Sede creada exitosamente');
    }
    emit('saved');
  } catch (error) {
    console.error('Error al guardar sede:', error);
    showError(error.response?.data?.message || 'Error al guardar la sede. Intenta nuevamente.');
  } finally {
    saving.value = false;
  }
};
</script>
