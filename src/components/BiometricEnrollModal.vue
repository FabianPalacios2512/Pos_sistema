<template>
  <Transition name="fade">
    <div v-if="visible" class="fixed inset-0 z-[60] flex items-center justify-center p-4">
      <div class="absolute inset-0 bg-black/40" @click="handleClose"></div>
      <div class="relative bg-white dark:bg-[#1e1e24] rounded-lg w-full max-w-4xl shadow-xl dark:shadow-black/50 border border-gray-200 dark:border-zinc-800 overflow-hidden">

        <!-- Header -->
        <div class="px-6 py-4 border-b border-gray-200 dark:border-zinc-800">
          <div class="flex items-center justify-between">
            <div>
              <h3 class="text-base font-semibold text-gray-900 dark:text-white">Enrolamiento Biométrico</h3>
              <p class="text-xs text-gray-500 dark:text-zinc-400 mt-0.5">
                <template v-if="phase === 'search'">Busque un empleado por su número de cédula</template>
                <template v-else-if="phase === 'found'">Perfil del empleado <span class="font-medium">{{ foundUser.name }}</span></template>
                <template v-else>Captura facial de <span class="font-medium">{{ foundUser.name }}</span></template>
              </p>
            </div>
            <button @click="handleClose"
                    class="p-1.5 rounded text-gray-400 dark:text-zinc-500 hover:text-gray-600 dark:hover:text-zinc-300 hover:bg-gray-100 dark:hover:bg-zinc-800 transition-colors">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
          </div>
        </div>

        <!-- Body: 2-column layout -->
        <div class="grid grid-cols-1 lg:grid-cols-[340px_1fr]" style="min-height: 420px;">

          <!-- ========== LEFT PANEL — Info & Controls ========== -->
          <div class="bg-gray-50 dark:bg-zinc-900/50 lg:border-r border-gray-200 dark:border-zinc-800 p-5 flex flex-col">

            <!-- Step indicator (compact) -->
            <div class="flex items-center gap-2 mb-5 pb-4 border-b border-gray-200 dark:border-zinc-800">
              <div v-for="(stepLabel, i) in stepLabels" :key="i"
                   class="flex items-center gap-1.5 text-[11px]"
                   :class="stepIndex === i ? 'font-medium text-gray-900 dark:text-white' : 'text-gray-400 dark:text-zinc-500'">
                <div class="w-5 h-5 rounded-full flex items-center justify-center text-[10px] font-bold border flex-shrink-0"
                     :class="stepIndex > i
                       ? 'bg-emerald-600 dark:bg-emerald-700 border-emerald-600 dark:border-emerald-700 text-white'
                       : stepIndex === i
                         ? 'bg-gray-900 dark:bg-white border-gray-900 dark:border-white text-white dark:text-gray-900'
                         : 'bg-white dark:bg-zinc-800 border-gray-300 dark:border-zinc-600 text-gray-400 dark:text-zinc-500'">
                  <template v-if="stepIndex > i">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" d="M5 13l4 4L19 7"/></svg>
                  </template>
                  <template v-else>{{ i + 1 }}</template>
                </div>
                <span class="hidden sm:inline">{{ stepLabel }}</span>
                <svg v-if="i < stepLabels.length - 1" class="w-3 h-3 text-gray-300 dark:text-zinc-700 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z" clip-rule="evenodd"/></svg>
              </div>
            </div>

            <!-- SEARCH: CC input & search -->
            <div v-if="phase === 'search'" class="space-y-4 flex-1">
              <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-zinc-300 mb-1.5">Número de Cédula (CC)</label>
                <div class="relative">
                  <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="w-4 h-4 text-gray-400 dark:text-zinc-500" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/></svg>
                  </div>
                  <input v-model="searchCC"
                         ref="ccInputRef"
                         type="text"
                         inputmode="numeric"
                         placeholder="Ingrese número de cédula"
                         @keydown.enter="searchUser"
                         class="w-full pl-9 pr-4 py-2.5 text-sm rounded-lg border border-gray-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-gray-900 dark:text-zinc-200 placeholder-gray-400 dark:placeholder-zinc-500 focus:outline-none focus:ring-2 focus:ring-gray-900 dark:focus:ring-white/30 focus:border-transparent transition-all">
                </div>
              </div>
              <button @click="searchUser"
                      :disabled="!searchCC.trim() || searching"
                      class="w-full px-4 py-2.5 bg-gray-900 dark:bg-white hover:bg-black dark:hover:bg-gray-200 disabled:opacity-40 disabled:cursor-not-allowed text-white dark:text-gray-900 text-sm font-semibold rounded-lg transition-colors flex items-center justify-center gap-2">
                <svg v-if="searching" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/></svg>
                Buscar Empleado
              </button>

              <!-- Search error -->
              <div v-if="searchError" class="space-y-2">
                <div class="bg-white dark:bg-zinc-900 rounded-lg p-3 border border-gray-200 dark:border-zinc-700 border-l-4 border-l-red-600 dark:border-l-red-500">
                  <div class="flex items-start gap-2">
                    <svg class="w-4 h-4 text-red-600 dark:text-red-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/></svg>
                    <p class="text-xs text-gray-800 dark:text-zinc-300">{{ searchError }}</p>
                  </div>
                </div>
                <button @click="goToUsersPanel"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-zinc-600 bg-white dark:bg-zinc-800 hover:bg-gray-50 dark:hover:bg-zinc-700 text-gray-700 dark:text-zinc-200 text-xs font-semibold rounded-lg transition-colors flex items-center justify-center gap-2">
                  <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" d="M18 7.5v3m0 0v3m0-3h3m-3 0h-3m-2.25-4.125a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zM3 19.235v-.11a6.375 6.375 0 0112.75 0v.109A12.318 12.318 0 019.374 21c-2.331 0-4.512-.645-6.374-1.766z"/></svg>
                  Registrar Nuevo Usuario
                </button>
              </div>
            </div>

            <!-- FOUND / CAMERA / CAPTURE: Employee info sidebar -->
            <div v-else class="space-y-4 flex-1">
              <!-- User card -->
              <div class="bg-white dark:bg-zinc-800/60 rounded-lg p-3.5 border border-gray-200 dark:border-zinc-700/60">
                <div class="flex items-center gap-3">
                  <div class="w-11 h-11 rounded-lg flex items-center justify-center flex-shrink-0 text-sm font-bold bg-gray-100 dark:bg-zinc-700 text-gray-700 dark:text-zinc-300 border border-gray-200 dark:border-zinc-600">
                    {{ foundUser.name?.charAt(0)?.toUpperCase() }}
                  </div>
                  <div class="flex-1 min-w-0">
                    <h4 class="text-sm font-semibold text-gray-900 dark:text-white truncate">{{ foundUser.name }}</h4>
                    <p class="text-xs text-gray-500 dark:text-zinc-400 mt-0.5">CC: {{ foundUser.cc }}</p>
                    <p class="text-[11px] text-gray-400 dark:text-zinc-500">{{ foundUser.role }}</p>
                  </div>
                </div>
              </div>

              <!-- Status badge -->
              <div class="flex items-center gap-2">
                <span v-if="foundUser.enrolled"
                      class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded text-[10px] font-semibold uppercase tracking-wide bg-white dark:bg-zinc-800 text-gray-800 dark:text-zinc-200 border border-emerald-500 dark:border-emerald-600">
                  <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                  Enrolado
                </span>
                <span v-else
                      class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded text-[10px] font-semibold uppercase tracking-wide bg-white dark:bg-zinc-800 text-gray-800 dark:text-zinc-200 border border-amber-500 dark:border-amber-500">
                  <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                  Pendiente
                </span>
              </div>

              <!-- Enrolled info alerts -->
              <div v-if="foundUser.enrolled" class="space-y-2">
                <div class="bg-white dark:bg-zinc-900 rounded-lg p-2.5 border border-gray-200 dark:border-zinc-700 border-l-4 border-l-emerald-600 dark:border-l-emerald-500">
                  <p class="text-[11px] text-gray-700 dark:text-zinc-300">
                    Perfil activo<span v-if="foundUser.enrolled_at"> — {{ formatDate(foundUser.enrolled_at) }}</span>
                  </p>
                </div>
                <div class="bg-white dark:bg-zinc-900 rounded-lg p-2.5 border border-gray-200 dark:border-zinc-700 border-l-4 border-l-amber-500">
                  <p class="text-[11px] text-gray-700 dark:text-zinc-300">Re-enrolar reemplazará el perfil anterior.</p>
                </div>
              </div>

              <!-- Not enrolled info -->
              <div v-else-if="phase === 'found'" class="bg-white dark:bg-zinc-900 rounded-lg p-2.5 border border-gray-200 dark:border-zinc-700 border-l-4 border-l-gray-400 dark:border-l-zinc-500">
                <p class="text-[11px] text-gray-700 dark:text-zinc-300">Sin perfil biométrico. Proceda con la captura facial.</p>
              </div>

              <!-- Capture success alert -->
              <div v-if="capturedImage && phase === 'capture'" class="bg-white dark:bg-zinc-900 rounded-lg p-2.5 border border-gray-200 dark:border-zinc-700 border-l-4 border-l-emerald-600 dark:border-l-emerald-500">
                <p class="text-[11px] text-gray-700 dark:text-zinc-300">Descriptor facial calculado. Puede guardar el perfil.</p>
              </div>
            </div>

            <!-- LEFT PANEL FOOTER: Action buttons -->
            <div class="mt-auto space-y-2 pt-4 border-t border-gray-200 dark:border-zinc-800">
              <!-- Found: Start enrollment -->
              <button v-if="phase === 'found'"
                      @click="startEnrollment"
                      class="w-full px-4 py-2.5 bg-gray-900 dark:bg-white hover:bg-black dark:hover:bg-gray-200 text-white dark:text-gray-900 text-sm font-semibold rounded-lg transition-colors flex items-center justify-center gap-2">
                {{ foundUser.enrolled ? 'Re-enrolar' : 'Iniciar Captura' }}
              </button>

              <!-- Camera: Manual capture -->
              <button v-if="phase === 'camera' && !capturedImage"
                      @click="captureProfile"
                      :disabled="!faceDetected || autoCapturing"
                      class="w-full px-4 py-2.5 bg-white dark:bg-zinc-800 hover:bg-gray-50 dark:hover:bg-zinc-700 disabled:opacity-40 disabled:cursor-not-allowed text-gray-700 dark:text-zinc-200 text-sm font-semibold rounded-lg border border-gray-300 dark:border-zinc-600 transition-colors flex items-center justify-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                Captura Manual
              </button>

              <!-- Capture: Retake + Save -->
              <button v-if="phase === 'capture'"
                      @click="saveProfile"
                      :disabled="saving"
                      class="w-full px-4 py-2.5 bg-gray-900 dark:bg-white hover:bg-black dark:hover:bg-gray-200 disabled:opacity-50 text-white dark:text-gray-900 text-sm font-semibold rounded-lg transition-colors flex items-center justify-center gap-2">
                <svg v-if="saving" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                {{ saving ? 'Guardando...' : 'Guardar Perfil' }}
              </button>
              <button v-if="phase === 'capture' && !saving"
                      @click="retake"
                      class="w-full px-4 py-2 bg-white dark:bg-zinc-800 hover:bg-gray-50 dark:hover:bg-zinc-700 text-gray-700 dark:text-zinc-200 text-xs font-semibold rounded-lg border border-gray-300 dark:border-zinc-600 transition-colors">
                Repetir Captura
              </button>

              <!-- Cancel / Back (always visible) -->
              <button @click="phase === 'found' || phase === 'models' ? goBackToSearch() : handleClose()"
                      class="w-full px-4 py-2 text-gray-500 dark:text-zinc-400 hover:text-gray-700 dark:hover:text-zinc-200 text-xs font-medium transition-colors">
                {{ phase === 'search' ? 'Cancelar' : 'Volver' }}
              </button>
            </div>
          </div>

          <!-- ========== RIGHT PANEL — Camera / Content ========== -->
          <div class="p-5 flex flex-col justify-center">

            <!-- Search phase: empty state -->
            <div v-if="phase === 'search'" class="text-center py-10">
              <div class="w-16 h-16 rounded-2xl mx-auto flex items-center justify-center bg-gray-100 dark:bg-zinc-800 border border-gray-200 dark:border-zinc-700 mb-4">
                <svg class="w-8 h-8 text-gray-300 dark:text-zinc-600" fill="none" stroke="currentColor" stroke-width="1" viewBox="0 0 24 24"><path stroke-linecap="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/></svg>
              </div>
              <p class="text-sm font-medium text-gray-400 dark:text-zinc-500">Busque un empleado para iniciar</p>
              <p class="text-xs text-gray-300 dark:text-zinc-600 mt-1">Ingrese la cédula en el panel izquierdo</p>
            </div>

            <!-- Found phase: ready state -->
            <div v-if="phase === 'found'" class="text-center py-10">
              <div class="w-16 h-16 rounded-2xl mx-auto flex items-center justify-center bg-gray-100 dark:bg-zinc-800 border border-gray-200 dark:border-zinc-700 mb-4">
                <svg class="w-8 h-8 text-gray-400 dark:text-zinc-500" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" d="M6.827 6.175A2.31 2.31 0 015.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 00-1.134-.175 2.31 2.31 0 01-1.64-1.055l-.822-1.316a2.192 2.192 0 00-1.736-1.039 48.774 48.774 0 00-5.232 0 2.192 2.192 0 00-1.736 1.039l-.821 1.316z"/><path stroke-linecap="round" d="M16.5 12.75a4.5 4.5 0 11-9 0 4.5 4.5 0 019 0zM18.75 10.5h.008v.008h-.008V10.5z"/></svg>
              </div>
              <p class="text-sm font-medium text-gray-700 dark:text-zinc-300">Listo para captura facial</p>
              <p class="text-xs text-gray-400 dark:text-zinc-500 mt-1">Presione "{{ foundUser.enrolled ? 'Re-enrolar' : 'Iniciar Captura' }}" para continuar</p>
            </div>

            <!-- Models loading -->
            <div v-if="phase === 'models'" class="text-center py-10">
              <template v-if="isModelLoading">
                <svg class="animate-spin w-8 h-8 text-gray-400 dark:text-zinc-500 mx-auto mb-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                <p class="text-sm font-medium text-gray-700 dark:text-zinc-300">Cargando modelos de reconocimiento...</p>
                <p class="text-xs text-gray-400 dark:text-zinc-500 mt-1">Esto puede tomar unos segundos la primera vez</p>
              </template>
              <template v-else-if="modelError">
                <svg class="w-8 h-8 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
                <p class="text-sm text-gray-800 dark:text-zinc-300">{{ modelError }}</p>
                <button @click="startEnrollment" class="mt-3 px-4 py-2 bg-gray-900 dark:bg-white text-white dark:text-gray-900 text-sm font-semibold rounded-lg transition-colors hover:bg-black dark:hover:bg-gray-200">Reintentar</button>
              </template>
            </div>

            <!-- Camera / Capture -->
            <div v-show="phase === 'camera' || phase === 'capture'">
              <div class="relative rounded-lg overflow-hidden bg-black aspect-video">
                <video ref="videoRef" class="w-full h-full object-cover" playsinline muted></video>
                <canvas ref="overlayRef" class="absolute inset-0 w-full h-full pointer-events-none"></canvas>

                <!-- Camera status badges -->
                <div class="absolute top-3 left-3 flex items-center gap-2">
                  <span v-if="isCameraActive" class="inline-flex items-center gap-1.5 px-2 py-1 rounded text-[10px] font-semibold bg-black/60 text-white/90 backdrop-blur-sm">
                    <span class="w-1.5 h-1.5 rounded-full bg-red-500 animate-pulse"></span>
                    EN VIVO
                  </span>
                  <span v-if="faceDetected && !autoCapturing" class="inline-flex items-center gap-1.5 px-2 py-1 rounded text-[10px] font-semibold bg-black/60 text-white/90 backdrop-blur-sm">
                    <svg class="w-3 h-3 text-emerald-400" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" d="M5 13l4 4L19 7"/></svg>
                    ROSTRO DETECTADO
                  </span>
                  <span v-if="faceDetected && autoCaptureCountdown > 0 && !autoCapturing" class="inline-flex items-center gap-1.5 px-2 py-1 rounded text-[10px] font-semibold bg-black/60 text-emerald-400 backdrop-blur-sm">
                    <svg class="w-3 h-3 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                    CAPTURANDO EN {{ autoCaptureCountdown }}s
                  </span>
                  <span v-if="autoCapturing" class="inline-flex items-center gap-1.5 px-2 py-1 rounded text-[10px] font-semibold bg-black/60 text-emerald-400 backdrop-blur-sm">
                    <svg class="w-3 h-3 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                    PROCESANDO...
                  </span>
                </div>

                <!-- User info badge -->
                <div class="absolute top-3 right-3">
                  <span class="inline-flex items-center gap-1.5 px-2 py-1 rounded text-[10px] font-semibold bg-black/60 text-white/90 backdrop-blur-sm">
                    {{ foundUser.name }} · {{ foundUser.cc }}
                  </span>
                </div>

                <!-- Captured preview overlay -->
                <div v-if="capturedImage" class="absolute inset-0 bg-black/70 flex items-center justify-center">
                  <div class="text-center">
                    <img :src="capturedImage" alt="Captura" class="w-40 h-40 rounded-lg object-cover border-2 border-white/30 mx-auto">
                    <p class="text-white/80 text-xs font-medium mt-2">Captura realizada</p>
                  </div>
                </div>
              </div>

              <!-- Instructions -->
              <div v-if="phase === 'camera' && !capturedImage" class="mt-3 bg-white dark:bg-zinc-900 rounded-lg p-2.5 border border-gray-200 dark:border-zinc-700 border-l-4 border-l-gray-400 dark:border-l-zinc-500">
                <div class="flex items-start gap-2">
                  <svg class="w-4 h-4 text-gray-500 dark:text-zinc-400 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                  <div class="text-[11px] text-gray-600 dark:text-zinc-400">
                    <span class="font-medium text-gray-800 dark:text-zinc-200">Captura automática — </span>
                    Centre el rostro. Se capturará automáticamente tras 2s de estabilidad.
                  </div>
                </div>
              </div>
            </div>

          </div>
        </div>

      </div>
    </div>
  </Transition>
</template>

<script setup>
import { ref, computed, watch, nextTick, onUnmounted } from 'vue'
import { useFaceRecognition } from '../composables/useFaceRecognition.js'
import biometricService from '../services/biometricService.js'

const props = defineProps({
  visible: { type: Boolean, default: false },
})

const emit = defineEmits(['close', 'enrolled', 'navigate'])

const {
  isModelLoading,
  modelError,
  isCameraActive,
  faceDetected,
  loadModels,
  startCamera,
  stopCamera,
  extractDescriptor,
  captureImage,
  startFaceGuide,
} = useFaceRecognition()

// Refs
const videoRef = ref(null)
const overlayRef = ref(null)
const ccInputRef = ref(null)

// Phase: search → found → models → camera → capture
const phase = ref('search')

// Search state
const searchCC = ref('')
const searching = ref(false)
const searchError = ref('')
const foundUser = ref({})

// Capture state
const capturedImage = ref(null)
const capturedDescriptor = ref(null)
const saving = ref(false)
const autoCapturing = ref(false)
const autoCaptureCountdown = ref(0)
let faceStableStart = null
let autoCaptureTick = null
const AUTO_CAPTURE_DELAY = 2000 // ms de rostro estable antes de capturar

// Step labels & index for the progress indicator
const stepLabels = computed(() => {
  if (phase.value === 'search') return ['Buscar empleado', 'Verificar estado', 'Captura facial']
  return ['Buscar empleado', 'Verificar estado', 'Captura facial']
})

const stepIndex = computed(() => {
  switch (phase.value) {
    case 'search': return 0
    case 'found': return 1
    case 'models': return 2
    case 'camera': return 2
    case 'capture': return 3
    default: return 0
  }
})

// Format date helper
const formatDate = (dateStr) => {
  if (!dateStr) return ''
  const d = new Date(dateStr)
  return d.toLocaleDateString('es-CO', { day: 'numeric', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' })
}

// ===== SEARCH USER =====
const searchUser = async () => {
  const cc = searchCC.value.trim()
  if (!cc) return

  searching.value = true
  searchError.value = ''

  try {
    const response = await biometricService.lookupUserByCC(cc)

    if (!response.found) {
      searchError.value = response.message || 'No se encontró un empleado activo con esa cédula. Verifique el número e intente de nuevo.'
      return
    }

    foundUser.value = response.data
    phase.value = 'found'
  } catch (error) {
    searchError.value = error.response?.data?.message || 'Error al buscar el empleado. Intente de nuevo.'
  } finally {
    searching.value = false
  }
}

// ===== START ENROLLMENT (load models + camera) =====
const startEnrollment = async () => {
  phase.value = 'models'
  modelError.value = null

  const loaded = await loadModels()
  if (loaded) {
    phase.value = 'camera'
    await nextTick()
    await initCamera()
  }
}

const initCamera = async () => {
  if (!videoRef.value) return
  const started = await startCamera(videoRef.value)
  if (started && overlayRef.value) {
    const startGuideAndAutoCapture = () => {
      overlayRef.value.width = videoRef.value.videoWidth
      overlayRef.value.height = videoRef.value.videoHeight
      startFaceGuide(videoRef.value, overlayRef.value)
      startAutoCaptureWatch()
    }
    if (videoRef.value.readyState >= 1) {
      startGuideAndAutoCapture()
    } else {
      videoRef.value.addEventListener('loadedmetadata', startGuideAndAutoCapture, { once: true })
    }
  }
}

// ===== AUTO-CAPTURE: detect stable face for 2s then capture automatically =====
const startAutoCaptureWatch = () => {
  stopAutoCaptureWatch()
  faceStableStart = null
  autoCapturing.value = false
  autoCaptureCountdown.value = 0

  autoCaptureTick = setInterval(async () => {
    if (phase.value !== 'camera' || capturedImage.value || autoCapturing.value) return

    if (faceDetected.value) {
      if (!faceStableStart) {
        faceStableStart = Date.now()
      }
      const elapsed = Date.now() - faceStableStart
      autoCaptureCountdown.value = Math.max(0, Math.ceil((AUTO_CAPTURE_DELAY - elapsed) / 1000))

      if (elapsed >= AUTO_CAPTURE_DELAY) {
        autoCapturing.value = true
        autoCaptureCountdown.value = 0
        stopAutoCaptureWatch()
        await captureProfile()
        if (capturedImage.value) {
          await saveProfile()
        }
        autoCapturing.value = false
      }
    } else {
      faceStableStart = null
      autoCaptureCountdown.value = 0
    }
  }, 200)
}

const stopAutoCaptureWatch = () => {
  if (autoCaptureTick) {
    clearInterval(autoCaptureTick)
    autoCaptureTick = null
  }
  faceStableStart = null
  autoCapturing.value = false
  autoCaptureCountdown.value = 0
}

// ===== CAPTURE =====
const captureProfile = async () => {
  if (!videoRef.value || !faceDetected.value) return

  const descriptor = await extractDescriptor(videoRef.value)
  if (!descriptor) {
    modelError.value = 'No se pudo obtener el descriptor facial. Intente de nuevo.'
    return
  }

  capturedDescriptor.value = descriptor
  capturedImage.value = captureImage(videoRef.value)
  phase.value = 'capture'
}

const retake = async () => {
  capturedImage.value = null
  capturedDescriptor.value = null
  phase.value = 'camera'
  await nextTick()
  if (overlayRef.value && videoRef.value) {
    startFaceGuide(videoRef.value, overlayRef.value)
    startAutoCaptureWatch()
  }
}

// ===== SAVE =====
const saveProfile = async () => {
  if (!capturedDescriptor.value || !capturedImage.value || !foundUser.value.id) return

  saving.value = true
  try {
    // Duplicate face check: compare against all enrolled profiles
    const allResponse = await biometricService.getAllDescriptors()
    if (allResponse.success && allResponse.data?.length > 0) {
      const faceapi = await import('face-api.js')
      const newDescriptor = new Float32Array(capturedDescriptor.value)

      for (const profile of allResponse.data) {
        // Skip the same user (re-enrollment is allowed)
        if (profile.user_id === foundUser.value.id) continue

        const existingDescriptor = new Float32Array(profile.descriptors)
        const distance = faceapi.euclideanDistance(newDescriptor, existingDescriptor)

        if (distance < 0.4) {
          modelError.value = `Este rostro ya está registrado como "${profile.name}" (CC: ${profile.cc}). No se puede enrolar la misma cara bajo otro usuario.`
          saving.value = false
          return
        }
      }
    }

    const response = await biometricService.enrollProfile(
      foundUser.value.id,
      capturedImage.value,
      capturedDescriptor.value
    )

    stopAutoCaptureWatch()
    stopCamera()
    emit('enrolled', response)
    handleClose()
  } catch (error) {
    const msg = error.response?.data?.message || 'Error al guardar el perfil biométrico'
    modelError.value = msg
  } finally {
    saving.value = false
  }
}

// ===== NAVIGATION =====
const goBackToSearch = () => {
  stopAutoCaptureWatch()
  stopCamera()
  phase.value = 'search'
  foundUser.value = {}
  capturedImage.value = null
  capturedDescriptor.value = null
  searchError.value = ''
}

const handleClose = () => {
  stopAutoCaptureWatch()
  stopCamera()
  phase.value = 'search'
  searchCC.value = ''
  searchError.value = ''
  foundUser.value = {}
  capturedImage.value = null
  capturedDescriptor.value = null
  emit('close')
}

const goToUsersPanel = () => {
  handleClose()
  emit('navigate', 'users-management')
}

// ===== WATCHERS =====
watch(() => props.visible, async (val) => {
  if (val) {
    phase.value = 'search'
    searchCC.value = ''
    searchError.value = ''
    foundUser.value = {}
    await nextTick()
    ccInputRef.value?.focus()
  } else {
    stopCamera()
  }
})

onUnmounted(() => {
  stopAutoCaptureWatch()
  stopCamera()
})
</script>

<style scoped>
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.2s ease;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>
