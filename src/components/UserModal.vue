<template>
  <!-- Overlay con blur -->
  <Teleport to="body">
  <Transition
    enter-active-class="transition-all duration-200 ease-out"
    enter-from-class="opacity-0"
    enter-to-class="opacity-100"
    leave-active-class="transition-all duration-150 ease-in"
    leave-from-class="opacity-100"
    leave-to-class="opacity-0"
  >
  <div 
    v-if="show"
    class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50 p-4"
    @click.self="closeModal"
  >
    <Transition
      enter-active-class="transition-all duration-200 ease-out"
      enter-from-class="opacity-0 scale-95 translate-y-2"
      enter-to-class="opacity-100 scale-100 translate-y-0"
      leave-active-class="transition-all duration-150 ease-in"
      leave-from-class="opacity-100 scale-100"
      leave-to-class="opacity-0 scale-95"
    >
    <!-- Modal más ancho - 3xl -->
    <div v-if="show" class="bg-white dark:bg-zinc-900 rounded-2xl shadow-2xl dark:shadow-black/60 border border-gray-200 dark:border-zinc-800 w-full max-w-3xl max-h-[92vh] overflow-hidden flex flex-col">

      <!-- Header con identidad visual -->
      <div class="relative overflow-hidden bg-gradient-to-r from-slate-900 to-slate-700 dark:from-slate-800 dark:to-slate-900 px-7 py-5 flex-shrink-0">
        <!-- Patrón decorativo sutil -->
        <div class="absolute inset-0 opacity-5">
          <svg width="100%" height="100%"><defs><pattern id="grid" width="20" height="20" patternUnits="userSpaceOnUse"><path d="M 20 0 L 0 0 0 20" fill="none" stroke="white" stroke-width="0.5"/></pattern></defs><rect width="100%" height="100%" fill="url(#grid)"/></svg>
        </div>
        <div class="relative flex items-center justify-between">
          <div class="flex items-center gap-4">
            <!-- Avatar dinámico con iniciales -->
            <div class="w-12 h-12 rounded-xl flex items-center justify-center text-white font-bold text-lg flex-shrink-0 shadow-lg"
                 :style="{ background: avatarColor }">
              {{ avatarInitials }}
            </div>
            <div>
              <h3 class="text-lg font-bold text-white leading-tight">
                {{ isEdit ? (form.name || 'Editar Usuario') : 'Nuevo Colaborador' }}
              </h3>
              <p class="text-sm text-slate-300 mt-0.5">
                {{ isEdit ? 'Modifica los datos del perfil' : 'Completa el formulario para registrar el acceso' }}
              </p>
            </div>
          </div>
          <button
            @click="closeModal"
            class="p-2 rounded-lg text-slate-300 hover:text-white hover:bg-white/10 transition-all flex-shrink-0"
          >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
            </svg>
          </button>
        </div>
      </div>

      <!-- Body con scroll -->
      <div class="overflow-y-auto flex-1 px-7 py-6">
        <form @submit.prevent="handleSubmit" class="space-y-6">

          <!-- ── SECCIÓN: Información Personal ── -->
          <div>
            <div class="flex items-center gap-2 mb-4">
              <div class="w-1 h-4 bg-blue-500 rounded-full"></div>
              <span class="text-xs font-bold text-gray-500 dark:text-zinc-400 uppercase tracking-widest">Información Personal</span>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <!-- Nombre Completo -->
              <div class="md:col-span-2">
                <label class="block text-xs font-semibold text-gray-600 dark:text-zinc-400 uppercase tracking-wide mb-1.5">
                  Nombre Completo <span class="text-rose-500">*</span>
                </label>
                <div class="relative">
                  <div class="absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 dark:text-zinc-500">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/></svg>
                  </div>
                  <input
                    v-model="form.name"
                    type="text"
                    required
                    class="w-full pl-10 pr-4 py-2.5 text-sm rounded-xl border border-gray-200 dark:border-zinc-700 bg-gray-50 dark:bg-zinc-800 text-gray-900 dark:text-zinc-200 placeholder-gray-400 dark:placeholder-zinc-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 focus:bg-white dark:focus:bg-zinc-800 transition-all"
                    placeholder="Ej: María González"
                  />
                </div>
              </div>

              <!-- Email -->
              <div class="md:col-span-2">
                <label class="block text-xs font-semibold text-gray-600 dark:text-zinc-400 uppercase tracking-wide mb-1.5">
                  Correo Electrónico <span class="text-rose-500">*</span>
                </label>
                <div class="relative">
                  <div class="absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 dark:text-zinc-500">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75"/></svg>
                  </div>
                  <input
                    v-model="form.email"
                    type="email"
                    required
                    class="w-full pl-10 pr-4 py-2.5 text-sm rounded-xl border border-gray-200 dark:border-zinc-700 bg-gray-50 dark:bg-zinc-800 text-gray-900 dark:text-zinc-200 placeholder-gray-400 dark:placeholder-zinc-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 focus:bg-white dark:focus:bg-zinc-800 transition-all"
                    placeholder="colaborador@empresa.com"
                  />
                </div>
              </div>

              <!-- Cédula -->
              <div>
                <label class="block text-xs font-semibold text-gray-600 dark:text-zinc-400 uppercase tracking-wide mb-1.5">Cédula / Documento</label>
                <div class="relative">
                  <div class="absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 dark:text-zinc-500">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 9h3.75M15 12h3.75M15 15h3.75M4.5 19.5h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5zm6-10.125a1.875 1.875 0 11-3.75 0 1.875 1.875 0 013.75 0zm1.294 6.336a6.721 6.721 0 01-3.17.789 6.721 6.721 0 01-3.168-.789 3.376 3.376 0 016.338 0z"/></svg>
                  </div>
                  <input
                    v-model="form.cc"
                    type="text"
                    class="w-full pl-10 pr-4 py-2.5 text-sm rounded-xl border border-gray-200 dark:border-zinc-700 bg-gray-50 dark:bg-zinc-800 text-gray-900 dark:text-zinc-200 placeholder-gray-400 dark:placeholder-zinc-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 focus:bg-white dark:focus:bg-zinc-800 transition-all"
                    placeholder="1234567890"
                  />
                </div>
              </div>

              <!-- Teléfono -->
              <div>
                <label class="block text-xs font-semibold text-gray-600 dark:text-zinc-400 uppercase tracking-wide mb-1.5">Teléfono</label>
                <div class="relative">
                  <div class="absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 dark:text-zinc-500">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 01-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z"/></svg>
                  </div>
                  <input
                    v-model="form.phone"
                    type="text"
                    class="w-full pl-10 pr-4 py-2.5 text-sm rounded-xl border border-gray-200 dark:border-zinc-700 bg-gray-50 dark:bg-zinc-800 text-gray-900 dark:text-zinc-200 placeholder-gray-400 dark:placeholder-zinc-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 focus:bg-white dark:focus:bg-zinc-800 transition-all"
                    placeholder="3001234567"
                  />
                </div>
              </div>
            </div>
          </div>

          <!-- ── SECCIÓN: Acceso y Seguridad ── -->
          <div>
            <div class="flex items-center gap-2 mb-4">
              <div class="w-1 h-4 bg-purple-500 rounded-full"></div>
              <span class="text-xs font-bold text-gray-500 dark:text-zinc-400 uppercase tracking-widest">Acceso y Seguridad</span>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <!-- Contraseña (solo nuevo usuario) -->
              <div v-if="!isEdit" class="md:col-span-2">
                <label class="block text-xs font-semibold text-gray-600 dark:text-zinc-400 uppercase tracking-wide mb-1.5">
                  Contraseña <span class="text-rose-500">*</span>
                </label>
                <div class="relative">
                  <div class="absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 dark:text-zinc-500">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/></svg>
                  </div>
                  <input
                    v-model="form.password"
                    :type="showPassword ? 'text' : 'password'"
                    :required="!isEdit"
                    class="w-full pl-10 pr-11 py-2.5 text-sm rounded-xl border border-gray-200 dark:border-zinc-700 bg-gray-50 dark:bg-zinc-800 text-gray-900 dark:text-zinc-200 placeholder-gray-400 dark:placeholder-zinc-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 focus:bg-white dark:focus:bg-zinc-800 transition-all"
                    placeholder="Mínimo 8 caracteres"
                    minlength="8"
                  />
                  <button type="button" @click="showPassword = !showPassword"
                          class="absolute right-3.5 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 dark:hover:text-zinc-300 transition-colors">
                    <svg v-if="!showPassword" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88"/></svg>
                  </button>
                </div>
              </div>

              <!-- Rol -->
              <div>
                <label class="block text-xs font-semibold text-gray-600 dark:text-zinc-400 uppercase tracking-wide mb-1.5">
                  Rol del Sistema <span class="text-rose-500">*</span>
                </label>
                <div class="relative">
                  <div class="absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 dark:text-zinc-500 pointer-events-none">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/></svg>
                  </div>
                  <select
                    v-model="form.role_id"
                    required
                    class="w-full pl-10 pr-4 py-2.5 text-sm rounded-xl border border-gray-200 dark:border-zinc-700 bg-gray-50 dark:bg-zinc-800 text-gray-900 dark:text-zinc-200 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 focus:bg-white dark:focus:bg-zinc-800 transition-all appearance-none cursor-pointer"
                  >
                    <option value="" disabled>Selecciona un rol...</option>
                    <option v-for="role in roles" :key="role.id" :value="role.id">{{ role.name }}</option>
                  </select>
                  <div class="absolute right-3.5 top-1/2 -translate-y-1/2 pointer-events-none text-gray-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                  </div>
                </div>
                <!-- Descripción del rol seleccionado -->
                <p v-if="selectedRoleDescription" class="text-xs text-blue-600 dark:text-blue-400 mt-1.5 flex items-center gap-1">
                  <svg class="w-3 h-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/></svg>
                  {{ selectedRoleDescription }}
                </p>
              </div>

              <!-- Sede Asignada -->
              <div>
                <label class="block text-xs font-semibold text-gray-600 dark:text-zinc-400 uppercase tracking-wide mb-1.5">Sede Asignada</label>
                <div class="relative">
                  <div class="absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 dark:text-zinc-500 pointer-events-none">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                  </div>
                  <select
                    v-model="form.warehouse_id"
                    class="w-full pl-10 pr-4 py-2.5 text-sm rounded-xl border border-gray-200 dark:border-zinc-700 bg-gray-50 dark:bg-zinc-800 text-gray-900 dark:text-zinc-200 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 focus:bg-white dark:focus:bg-zinc-800 transition-all appearance-none cursor-pointer"
                  >
                    <option value="">Sin sede asignada</option>
                    <option v-for="wh in warehouses" :key="wh.id" :value="wh.id">{{ wh.name }}</option>
                  </select>
                  <div class="absolute right-3.5 top-1/2 -translate-y-1/2 pointer-events-none text-gray-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- ── SECCIÓN: Estado de la Cuenta ── -->
          <div>
            <div class="flex items-center gap-2 mb-4">
              <div class="w-1 h-4 bg-emerald-500 rounded-full"></div>
              <span class="text-xs font-bold text-gray-500 dark:text-zinc-400 uppercase tracking-widest">Estado de la Cuenta</span>
            </div>
            <label for="user-active"
                   class="flex items-center gap-4 p-4 rounded-xl border cursor-pointer transition-all"
                   :class="form.active
                     ? 'bg-emerald-50 dark:bg-emerald-950/30 border-emerald-200 dark:border-emerald-800'
                     : 'bg-gray-50 dark:bg-zinc-800/50 border-gray-200 dark:border-zinc-700'">
              <!-- Toggle visual -->
              <div class="relative flex-shrink-0">
                <input v-model="form.active" type="checkbox" id="user-active" class="sr-only" />
                <div class="w-11 h-6 rounded-full transition-colors duration-200"
                     :class="form.active ? 'bg-emerald-500' : 'bg-gray-300 dark:bg-zinc-600'">
                  <div class="absolute top-0.5 left-0.5 w-5 h-5 bg-white rounded-full shadow transition-transform duration-200"
                       :class="form.active ? 'translate-x-5' : 'translate-x-0'"></div>
                </div>
              </div>
              <div class="flex-1">
                <p class="text-sm font-semibold" :class="form.active ? 'text-emerald-700 dark:text-emerald-400' : 'text-gray-600 dark:text-zinc-400'">
                  {{ form.active ? 'Cuenta Activa' : 'Cuenta Inactiva' }}
                </p>
                <p class="text-xs text-gray-500 dark:text-zinc-500 mt-0.5">
                  {{ form.active ? 'El usuario puede iniciar sesión y operar en el sistema' : 'El usuario no puede iniciar sesión' }}
                </p>
              </div>
              <!-- Badge de estado -->
              <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wide border"
                    :class="form.active
                      ? 'bg-emerald-100 dark:bg-emerald-900/40 text-emerald-700 dark:text-emerald-400 border-emerald-200 dark:border-emerald-800'
                      : 'bg-gray-100 dark:bg-zinc-700 text-gray-600 dark:text-zinc-400 border-gray-200 dark:border-zinc-600'">
                {{ form.active ? 'Activo' : 'Inactivo' }}
              </span>
            </label>
          </div>

        </form>
      </div>

      <!-- Footer -->
      <div class="px-7 py-4 border-t border-gray-200 dark:border-zinc-800 bg-gray-50 dark:bg-zinc-900/80 flex items-center justify-between flex-shrink-0">
        <p class="text-xs text-gray-400 dark:text-zinc-500">
          <span class="text-rose-500">*</span> Campos obligatorios
        </p>
        <div class="flex items-center gap-3">
          <button
            @click="closeModal"
            type="button"
            class="px-5 py-2.5 bg-white dark:bg-zinc-900 hover:bg-gray-50 dark:hover:bg-zinc-800 text-gray-600 dark:text-zinc-200 text-sm font-bold rounded-xl border border-gray-200 dark:border-zinc-700 shadow-sm transition-all duration-200"
          >
            Cancelar
          </button>
          <button
            @click="handleSubmit"
            type="submit"
            :disabled="loading"
            class="px-6 py-2.5 bg-slate-900 dark:bg-slate-700 hover:bg-black dark:hover:bg-slate-600 text-white text-sm font-bold rounded-xl shadow-lg shadow-slate-400/40 dark:shadow-slate-900/50 transition-all duration-300 disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2"
          >
            <svg v-if="loading" class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"/>
            </svg>
            <svg v-else-if="isEdit" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
            <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
            {{ loading ? 'Guardando...' : (isEdit ? 'Guardar Cambios' : 'Crear Colaborador') }}
          </button>
        </div>
      </div>

    </div>
    </Transition>
  </div>
  </Transition>
  </Teleport>
</template>


<script setup>
import { ref, watch, computed } from 'vue'

const props = defineProps({
  show: { type: Boolean, required: true },
  user: { type: Object, default: null },
  roles: { type: Array, required: true },
  warehouses: { type: Array, default: () => [] }
})

const emit = defineEmits(['close', 'save'])

const loading = ref(false)
const showPassword = ref(false)
const form = ref({
  name: '',
  email: '',
  cc: '',
  phone: '',
  password: '',
  role_id: '',
  warehouse_id: '',
  active: true
})

const isEdit = computed(() => !!props.user)

// Avatar: iniciales dinámicas desde el nombre
const avatarInitials = computed(() => {
  const name = form.value.name?.trim()
  if (!name) return isEdit.value ? 'ED' : 'NU'
  const parts = name.split(' ').filter(Boolean)
  if (parts.length >= 2) return (parts[0][0] + parts[1][0]).toUpperCase()
  return name.substring(0, 2).toUpperCase()
})

// Color del avatar según la primera letra
const avatarColor = computed(() => {
  const name = form.value.name?.trim() || ''
  const colors = [
    'linear-gradient(135deg,#3B82F6,#1D4ED8)',
    'linear-gradient(135deg,#8B5CF6,#6D28D9)',
    'linear-gradient(135deg,#EC4899,#BE185D)',
    'linear-gradient(135deg,#10B981,#047857)',
    'linear-gradient(135deg,#F59E0B,#B45309)',
    'linear-gradient(135deg,#EF4444,#B91C1C)',
    'linear-gradient(135deg,#06B6D4,#0E7490)',
    'linear-gradient(135deg,#6366F1,#4338CA)',
  ]
  const idx = name ? name.charCodeAt(0) % colors.length : 0
  return colors[idx]
})

// Descripción del rol seleccionado
const selectedRoleDescription = computed(() => {
  if (!form.value.role_id) return null
  const role = props.roles.find(r => r.id === form.value.role_id || r.id === parseInt(form.value.role_id))
  return role?.description || null
})


// Definir resetForm ANTES del watch
const resetForm = () => {
  form.value = {
    name: '',
    email: '',
    cc: '',
    phone: '',
    password: '',
    role_id: '',
    warehouse_id: '',
    active: true
  }
}

// Watch para cargar datos cuando se edita
watch(() => props.user, (newUser) => {
  if (newUser) {
    form.value = {
      name: newUser.name || '',
      email: newUser.email || '',
      cc: newUser.cc || '',
      phone: newUser.phone || '',
      password: '', // No se carga la contraseña por seguridad
      role_id: newUser.role_id || '',
      warehouse_id: newUser.warehouse_id || '',
      active: newUser.active !== undefined ? newUser.active : true
    }
  } else {
    resetForm()
  }
}, { immediate: true })

const closeModal = () => {
  resetForm()
  emit('close')
}

const handleSubmit = async () => {
  loading.value = true
  try {
    // Preparar datos para enviar
    const userData = { ...form.value }
    
    // Si es edición, remover password vacío
    if (isEdit.value && !userData.password) {
      delete userData.password
    }

    emit('save', userData)
  } catch (error) {
    console.error('Error al guardar usuario:', error)
  } finally {
    loading.value = false
  }
}

// Exponer form y métodos para que la IA pueda llenar campos
const setFieldValue = (campo, valor) => {
  const campoMap = {
    'name': 'name',
    'nombre': 'name',
    'email': 'email',
    'correo': 'email',
    'password': 'password',
    'contraseña': 'password',
    'clave': 'password',
    'cc': 'cc',
    'cedula': 'cc',
    'documento': 'cc',
    'phone': 'phone',
    'telefono': 'phone',
    'role_id': 'role_id',
    'rol': 'role_id',
    'warehouse_id': 'warehouse_id',
    'sede': 'warehouse_id',
    'bodega': 'warehouse_id'
  }
  
  const campoReal = campoMap[campo.toLowerCase()] || campo
  
  if (campoReal in form.value) {
    form.value[campoReal] = valor
    return true
  }
  return false
}

defineExpose({
  form,
  setFieldValue,
  handleSubmit,
  resetForm
})
</script>

<style scoped>
@keyframes fade-in {
  from {
    opacity: 0;
    transform: scale(0.95);
  }
  to {
    opacity: 1;
    transform: scale(1);
  }
}

.animate-fade-in {
  animation: fade-in 0.2s ease-out;
}
</style>
