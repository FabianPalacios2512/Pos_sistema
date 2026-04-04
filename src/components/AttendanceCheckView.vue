<template>
  <div class="min-h-screen font-sans bg-gradient-to-b from-gray-50 via-gray-100 to-gray-200 dark:bg-gradient-to-b dark:from-[#141417] dark:via-slate-900 dark:to-[#0a0a0c] transition-colors duration-300 px-3 lg:px-5">
    <div class="p-3 lg:p-5 space-y-6 pb-8 animate-fade-in">

      <!-- Header -->
      <div class="flex items-center justify-between pb-4">
        <div>
          <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Punteo de Jornada</h1>
          <p class="text-sm text-gray-600 dark:text-zinc-400 mt-1">Control de asistencia y auditoría de personal</p>
        </div>
        <div class="flex items-center gap-3">
          <button @click="refreshAll"
                  class="px-5 py-2.5 bg-white dark:bg-zinc-900 hover:bg-slate-50 dark:hover:bg-zinc-800 text-slate-600 dark:text-zinc-200 text-sm font-bold rounded-xl border border-slate-200 dark:border-zinc-800 shadow-sm transition-all duration-200">
            <svg class="w-4 h-4 inline mr-1.5 -mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
            Refrescar
          </button>
        </div>
      </div>

      <!-- KPIs — Métricas corporativas de alta densidad -->
      <div class="grid grid-cols-2 lg:grid-cols-4 gap-px bg-gray-200 dark:bg-zinc-800 rounded-lg overflow-hidden border border-gray-200 dark:border-zinc-800">

        <!-- Entradas Hoy -->
        <div class="bg-white dark:bg-zinc-900 px-5 py-4">
          <p class="text-[10px] font-semibold text-gray-500 dark:text-zinc-500 uppercase tracking-widest">Entradas Hoy</p>
          <p class="text-4xl font-extrabold text-gray-900 dark:text-white mt-1 tabular-nums leading-none">{{ summary.entries_today ?? 0 }}</p>
        </div>

        <!-- Salidas Hoy -->
        <div class="bg-white dark:bg-zinc-900 px-5 py-4">
          <p class="text-[10px] font-semibold text-gray-500 dark:text-zinc-500 uppercase tracking-widest">Salidas Hoy</p>
          <p class="text-4xl font-extrabold text-gray-900 dark:text-white mt-1 tabular-nums leading-none">{{ summary.exits_today ?? 0 }}</p>
        </div>

        <!-- Perfiles Biométricos -->
        <div class="bg-white dark:bg-zinc-900 px-5 py-4">
          <p class="text-[10px] font-semibold text-gray-500 dark:text-zinc-500 uppercase tracking-widest">Perfiles Biométricos</p>
          <p class="text-4xl font-extrabold text-gray-900 dark:text-white mt-1 tabular-nums leading-none">
            {{ summary.enrolled_users ?? 0 }}<span class="text-lg font-normal text-gray-400 dark:text-zinc-600">/{{ summary.total_users ?? 0 }}</span>
          </p>
        </div>

        <!-- Sin Enrolar -->
        <div class="bg-white dark:bg-zinc-900 px-5 py-4">
          <p class="text-[10px] font-semibold text-gray-500 dark:text-zinc-500 uppercase tracking-widest">Sin Enrolar</p>
          <p class="text-4xl font-extrabold mt-1 tabular-nums leading-none"
             :class="(summary.pending_enroll ?? 0) > 0 ? 'text-amber-600 dark:text-amber-400' : 'text-gray-900 dark:text-white'">
            {{ summary.pending_enroll ?? 0 }}
          </p>
        </div>

      </div>

      <!-- Layout Principal: Timeline (70%) + Gestión (30%) -->
      <div class="grid grid-cols-1 lg:grid-cols-[1fr_340px] gap-6">

        <!-- Timeline de Registros del Día -->
        <div class="bg-white dark:bg-zinc-900 rounded-lg shadow-sm border border-gray-200 dark:border-zinc-800 overflow-hidden flex flex-col">

          <!-- Panel Header -->
          <div class="px-5 py-3 border-b border-gray-100 dark:border-zinc-800">
            <div class="flex items-center justify-between">
              <div class="flex items-center gap-3">
                <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Registro de Asistencia</h3>
                <span class="text-xs text-gray-400 dark:text-zinc-500 tabular-nums">
                  {{ groupedHistory.length }} empleado{{ groupedHistory.length !== 1 ? 's' : '' }} · {{ history.length }} punteo{{ history.length !== 1 ? 's' : '' }}
                </span>
              </div>
              <div class="flex items-center gap-2">
                <input type="date" v-model="historyDate" @change="loadHistory"
                       class="px-3 py-1.5 text-xs border border-gray-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-gray-700 dark:text-zinc-200 rounded focus:ring-1 focus:ring-gray-400 focus:border-gray-400 dark:focus:ring-zinc-600">
              </div>
            </div>
          </div>

          <!-- Timeline Content -->
          <div class="flex-1 p-5 overflow-y-auto" style="max-height: 520px;">

            <!-- Loading -->
            <div v-if="historyLoading" class="flex items-center justify-center py-16">
              <div class="text-center">
                <svg class="animate-spin w-8 h-8 text-slate-300 dark:text-zinc-600 mx-auto mb-3" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                <p class="text-sm text-gray-400 dark:text-zinc-500 font-medium">Cargando registros...</p>
              </div>
            </div>

            <!-- Empty State -->
            <div v-else-if="history.length === 0" class="flex items-center justify-center py-20">
              <div class="text-center max-w-xs">
                <svg class="w-8 h-8 text-gray-300 dark:text-zinc-700 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <p class="text-sm font-medium text-gray-500 dark:text-zinc-400">Sin registros de asistencia</p>
                <p class="text-xs text-gray-400 dark:text-zinc-500 mt-1 leading-relaxed">
                  No hay entradas ni salidas para el {{ formatDateLabel(historyDate) }}.
                </p>
              </div>
            </div>

            <!-- Employee Cards (Grouped by Employee) -->
            <div v-else class="space-y-2">
              <div v-for="emp in groupedHistory" :key="emp.user_id"
                   class="rounded-lg border border-gray-200 dark:border-zinc-700/60 overflow-hidden transition-all duration-200"
                   :class="expandedEmployees.has(emp.user_id) ? 'ring-1 ring-gray-300 dark:ring-zinc-600' : ''">

                <!-- Summary Row (clickable) -->
                <button @click="toggleEmployee(emp.user_id)"
                        class="w-full flex items-center gap-4 px-5 py-4 text-left hover:bg-gray-50 dark:hover:bg-zinc-800/40 transition-colors">

                  <!-- Avatar -->
                  <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0 text-base font-bold bg-gray-100 dark:bg-zinc-700 text-gray-700 dark:text-zinc-300 border border-gray-200 dark:border-zinc-600">
                    {{ getInitials(emp.user_name) }}
                  </div>

                  <!-- Name + entry/exit summary -->
                  <div class="flex-1 min-w-0">
                    <p class="text-base font-semibold text-gray-900 dark:text-white truncate">{{ emp.user_name }}</p>
                    <div class="flex items-center gap-2 mt-1 text-xs text-gray-500 dark:text-zinc-400">
                      <span v-if="emp.firstEntry" class="flex items-center gap-1">
                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                        {{ formatTime(emp.firstEntry.event_at) }}
                      </span>
                      <span v-if="emp.firstEntry && emp.lastExit" class="text-gray-300 dark:text-zinc-600">→</span>
                      <span v-if="emp.lastExit" class="flex items-center gap-1">
                        <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                        {{ formatTime(emp.lastExit.event_at) }}
                      </span>
                      <span v-if="!emp.firstEntry" class="italic text-gray-400 dark:text-zinc-500">Sin entrada registrada</span>
                    </div>
                  </div>

                  <!-- Active / On-break / Completed badge -->
                  <div class="flex-shrink-0">
                    <span v-if="emp.isActive && emp.onBreak"
                          class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wide bg-amber-50 dark:bg-amber-950 text-amber-700 dark:text-amber-400 border border-amber-100 dark:border-amber-800">
                      <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                      En Break
                    </span>
                    <span v-else-if="emp.isActive"
                          class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wide bg-emerald-50 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-400 border border-emerald-100 dark:border-emerald-800">
                      <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                      Turno en curso
                    </span>
                    <span v-else-if="emp.lastExit"
                          class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wide bg-blue-50 dark:bg-blue-950 text-blue-700 dark:text-blue-400 border border-blue-100 dark:border-blue-800">
                      Jornada completa
                    </span>
                  </div>

                  <!-- Hours worked -->
                  <div class="flex-shrink-0 text-right min-w-[80px]">
                    <p class="text-xl font-bold text-gray-900 dark:text-white tabular-nums leading-tight">{{ emp.workedLabel }}</p>
                    <p class="text-[10px] text-gray-400 dark:text-zinc-500 uppercase tracking-wider font-medium">trabajado</p>
                  </div>

                  <!-- Chevron -->
                  <svg class="w-5 h-5 text-gray-400 dark:text-zinc-500 transition-transform duration-200 flex-shrink-0"
                       :class="expandedEmployees.has(emp.user_id) ? 'rotate-180' : ''"
                       fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                  </svg>
                </button>

                <!-- Expanded Detail -->
                <div v-if="expandedEmployees.has(emp.user_id)" class="border-t border-gray-100 dark:border-zinc-800">
                  <div class="px-4 py-3 bg-gray-50 dark:bg-zinc-900">
                    <p class="text-[11px] font-semibold text-gray-500 dark:text-zinc-500 uppercase tracking-widest mb-3">
                      Detalle de Punteos · {{ emp.events.length }} registro{{ emp.events.length !== 1 ? 's' : '' }}
                    </p>
                    <div class="relative">
                      <div class="absolute left-[9px] top-2 bottom-2 w-px bg-gray-200 dark:bg-zinc-700/60"></div>
                      <div class="space-y-1">
                        <div v-for="log in emp.events" :key="log.id"
                             class="relative flex items-center gap-3.5 py-2.5">
                          <!-- Dot -->
                          <div class="relative z-10 flex-shrink-0">
                            <div class="w-[18px] h-[18px] rounded-full border-[2.5px]"
                                 :class="getEventDotClass(log.event_type)"></div>
                          </div>
                          <!-- Event info -->
                          <div class="flex-1 flex items-center gap-2.5 flex-wrap">
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider border"
                                  :class="getEventBadgeClass(log.event_type)">
                              {{ getEventLabel(log.event_type) }}
                            </span>
                            <span class="text-sm text-gray-700 dark:text-zinc-300 font-medium tabular-nums">
                              {{ formatTime(log.event_at) }}
                            </span>
                            <span class="text-xs font-medium"
                                  :class="getScoreColor(log.verification_score)">
                              Confianza: {{ getScorePercent(log.verification_score) }}%
                            </span>
                          </div>
                          <!-- Score label -->
                          <div class="flex-shrink-0">
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold border"
                                  :class="getScoreBadgeClass(log.verification_score)">
                              {{ getScoreLabel(log.verification_score) }}
                            </span>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Panel Lateral: Gestión Biométrica -->
        <div class="space-y-5">

          <!-- Tarjeta CTA: Gestión Biométrica -->
          <div class="bg-white dark:bg-zinc-900 rounded-lg shadow-sm border border-gray-200 dark:border-zinc-800 overflow-hidden">
            <div class="px-5 py-3.5 border-b border-gray-100 dark:border-zinc-800">
              <h3 class="text-base font-semibold text-gray-900 dark:text-white">Gestión Biométrica</h3>
            </div>

            <div class="p-5 space-y-4">
              <!-- Status info -->
              <div class="space-y-0">
                <div class="flex items-center justify-between py-3 border-b border-gray-100 dark:border-zinc-800">
                  <span class="text-sm text-gray-600 dark:text-zinc-400">Total empleados</span>
                  <span class="text-base font-bold text-gray-900 dark:text-white tabular-nums">{{ summary.total_users ?? 0 }}</span>
                </div>
                <div class="flex items-center justify-between py-3 border-b border-gray-100 dark:border-zinc-800">
                  <span class="text-sm text-gray-600 dark:text-zinc-400">Perfiles activos</span>
                  <span class="text-base font-bold text-emerald-700 dark:text-emerald-400 tabular-nums">{{ summary.enrolled_users ?? 0 }}</span>
                </div>
                <div class="flex items-center justify-between py-3">
                  <span class="text-sm text-gray-600 dark:text-zinc-400">Requieren enrolamiento</span>
                  <span class="text-base font-bold tabular-nums" :class="(summary.pending_enroll ?? 0) > 0 ? 'text-amber-600 dark:text-amber-400' : 'text-gray-900 dark:text-white'">{{ summary.pending_enroll ?? 0 }}</span>
                </div>
              </div>

              <!-- Alert for pending -->
              <div v-if="(summary.pending_enroll ?? 0) > 0"
                   class="rounded-md p-3 border border-gray-200 dark:border-zinc-700 border-l-4 border-l-amber-500 bg-white dark:bg-zinc-800">
                <p class="text-xs text-gray-700 dark:text-zinc-300 leading-relaxed">
                  <span class="font-semibold text-gray-900 dark:text-white">{{ summary.pending_enroll }}</span> empleado{{ (summary.pending_enroll ?? 0) !== 1 ? 's' : '' }} sin perfil facial registrado.
                </p>
              </div>

              <!-- Enroll CTA -->
              <button @click="openEnrollModal()"
                      class="w-full py-3 bg-gray-900 dark:bg-slate-700 hover:bg-black dark:hover:bg-slate-600 text-white text-sm font-semibold rounded-lg transition-colors duration-200">
                Enrolar Nuevo Empleado
              </button>
            </div>
          </div>

          <!-- Tarjeta: Verificación Rápida (Admin) -->
          <div class="bg-white dark:bg-zinc-900 rounded-lg shadow-sm border border-gray-200 dark:border-zinc-800 overflow-hidden">
            <div class="px-5 py-3.5 border-b border-gray-100 dark:border-zinc-800">
              <h3 class="text-base font-semibold text-gray-900 dark:text-white">Verificación Rápida</h3>
            </div>

            <div class="p-5 space-y-4">
              <p class="text-sm text-gray-600 dark:text-zinc-400 leading-relaxed">
                Registre la entrada o salida de un empleado con reconocimiento facial en tiempo real.
              </p>

              <button @click="openVerifyModal()"
                      class="w-full py-3 bg-white dark:bg-zinc-800 hover:bg-gray-50 dark:hover:bg-zinc-700 text-gray-800 dark:text-zinc-200 text-sm font-semibold rounded-lg border border-gray-300 dark:border-zinc-600 transition-colors duration-200">
                Abrir Cámara
              </button>
            </div>
          </div>

          <!-- Resumen rápido del día -->
          <div class="bg-white dark:bg-zinc-900 rounded-lg shadow-sm border border-gray-200 dark:border-zinc-800 overflow-hidden">
            <div class="px-5 py-3.5 border-b border-gray-100 dark:border-zinc-800">
              <h3 class="text-base font-semibold text-gray-900 dark:text-white">Resumen del Día</h3>
            </div>
            <div class="p-5 space-y-0">
              <div class="flex items-center justify-between py-3 border-b border-gray-100 dark:border-zinc-800">
                <span class="text-sm text-gray-600 dark:text-zinc-400">Total movimientos</span>
                <span class="text-base font-bold text-gray-900 dark:text-white tabular-nums">{{ (summary.entries_today ?? 0) + (summary.exits_today ?? 0) }}</span>
              </div>
              <div class="flex items-center justify-between py-3 border-b border-gray-100 dark:border-zinc-800">
                <span class="text-sm text-gray-600 dark:text-zinc-400">Entradas</span>
                <span class="text-base font-bold text-gray-900 dark:text-white tabular-nums">{{ summary.entries_today ?? 0 }}</span>
              </div>
              <div class="flex items-center justify-between py-3">
                <span class="text-sm text-gray-600 dark:text-zinc-400">Salidas</span>
                <span class="text-base font-bold text-gray-900 dark:text-white tabular-nums">{{ summary.exits_today ?? 0 }}</span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Branding footer -->
      <div class="flex items-center justify-between pt-1">
        <p class="text-[9px] text-gray-400 dark:text-zinc-600 font-medium tracking-wide">105 POS Pro · Módulo de Asistencia Biométrica</p>
      </div>

      <!-- Modal de Verificación Facial (Punteo) -->
      <Teleport to="body">
        <Transition name="modal">
          <div v-if="showVerifyModal" class="fixed inset-0 z-[60] flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-black/50" @click="closeVerifyModal"></div>
            <div class="relative bg-white dark:bg-zinc-900 rounded-md max-w-5xl w-full shadow-xl border border-gray-200 dark:border-zinc-800 overflow-hidden">

              <!-- Top bar -->
              <div class="h-11 px-4 flex items-center justify-between border-b border-gray-200 dark:border-zinc-800 bg-white dark:bg-zinc-900">
                <div class="flex items-center gap-2.5">
                  <svg class="w-4 h-4 text-gray-500 dark:text-zinc-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M7.864 4.243A7.5 7.5 0 0119.5 10.5c0 2.92-.556 5.709-1.568 8.268M5.742 6.364A7.465 7.465 0 004.5 10.5a48.667 48.667 0 00-1.409 8.239M12 10.5a3 3 0 11-6 0 3 3 0 016 0zm-1.5 0a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0z"/></svg>
                  <span class="text-sm font-semibold text-gray-900 dark:text-white">Punteo de Jornada</span>
                  <span class="text-xs text-gray-400 dark:text-zinc-500">·</span>
                  <span class="text-xs text-gray-400 dark:text-zinc-500 tabular-nums">{{ new Date().toLocaleDateString('es-CO', { day: '2-digit', month: 'short', year: 'numeric' }) }}</span>
                </div>
                <button @click="closeVerifyModal"
                        class="p-1.5 rounded-md text-gray-400 dark:text-zinc-500 hover:text-gray-700 dark:hover:text-zinc-200 hover:bg-gray-100 dark:hover:bg-zinc-800 transition-colors">
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
              </div>

              <div class="grid grid-cols-1 lg:grid-cols-[1fr_340px]">

                <!-- ===== ZONE 1: CAMERA (input principal) ===== -->
                <div class="bg-gray-900 dark:bg-zinc-950 flex flex-col">
                  <div class="relative flex-1">
                    <video ref="verifyVideoRef" class="w-full h-full object-cover block" playsinline muted></video>
                    <canvas ref="verifyOverlayRef" class="absolute inset-0 w-full h-full pointer-events-none"></canvas>

                    <!-- Camera off -->
                    <div v-if="!isCameraActive && !isModelLoading" class="absolute inset-0 flex items-center justify-center bg-zinc-950">
                      <div class="text-center">
                        <svg class="w-10 h-10 text-zinc-700 mx-auto mb-2" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                        <p class="text-xs text-zinc-600 font-medium">Iniciando cámara...</p>
                      </div>
                    </div>

                    <!-- Loading models -->
                    <div v-if="isModelLoading" class="absolute inset-0 flex items-center justify-center bg-zinc-950">
                      <div class="text-center">
                        <svg class="animate-spin w-7 h-7 text-zinc-500 mx-auto mb-2" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                        <p class="text-xs text-zinc-500 font-medium">Cargando modelos...</p>
                      </div>
                    </div>

                    <!-- Camera HUD overlay -->
                    <template v-if="isCameraActive">
                      <!-- Top-left: status badges -->
                      <div class="absolute top-3 left-3 flex items-center gap-1.5">
                        <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded-sm text-[9px] font-bold bg-red-500/80 text-white uppercase tracking-wider">
                          <span class="w-1 h-1 rounded-full bg-white animate-pulse"></span>
                          REC
                        </span>
                        <span v-if="identifyingFace" class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded-sm text-[9px] font-semibold bg-zinc-800/80 text-zinc-300">
                          <svg class="w-2.5 h-2.5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                          BUSCANDO
                        </span>
                      </div>

                      <!-- Top-right: profiles loaded -->
                      <div class="absolute top-3 right-3">
                        <span class="px-1.5 py-0.5 rounded-sm text-[9px] font-medium bg-zinc-800/80 text-zinc-400 tabular-nums">
                          {{ allProfiles.length }} perfil{{ allProfiles.length !== 1 ? 'es' : '' }}
                        </span>
                      </div>

                      <!-- Bottom: match confirmed -->
                      <div v-if="identifiedUser" class="absolute bottom-2 left-2 right-2">
                        <div class="flex items-center justify-between px-2.5 py-1.5 rounded-sm bg-emerald-600/85 text-white">
                          <div class="flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span class="text-[10px] font-semibold tracking-wide">IDENTIDAD CONFIRMADA</span>
                          </div>
                          <span class="text-[10px] font-semibold tabular-nums">{{ getScorePercent(identifiedUser.distance) }}%</span>
                        </div>
                      </div>
                    </template>
                  </div>

                  <!-- Error / warnings below camera -->
                  <div v-if="modelError" class="px-4 py-2 border-t border-zinc-800 bg-zinc-900">
                    <p class="text-[11px] text-rose-400 font-medium">{{ modelError }}</p>
                  </div>
                  <div v-if="isCameraActive && allProfiles.length === 0 && !isModelLoading"
                       class="px-4 py-2 border-t border-zinc-800 bg-zinc-900">
                    <p class="text-[11px] text-amber-400 font-medium">Sin perfiles biométricos — enrole empleados primero</p>
                  </div>
                </div>

                <!-- ===== RIGHT PANEL: Identity + Action ===== -->
                <div class="border-t lg:border-t-0 lg:border-l border-gray-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 flex flex-col">

                  <!-- ---- ZONE 2: IDENTITY ---- -->
                  <div class="px-5 pt-5 pb-4">
                    <!-- Waiting state -->
                    <div v-if="!identifiedUser">
                      <p class="text-[10px] font-semibold text-gray-400 dark:text-zinc-500 uppercase tracking-widest mb-3">Identidad</p>
                      <div class="flex items-center gap-3 py-4">
                        <div class="w-12 h-12 rounded bg-gray-100 dark:bg-zinc-800 flex items-center justify-center border border-gray-200 dark:border-zinc-700">
                          <svg class="w-6 h-6 text-gray-300 dark:text-zinc-600" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/></svg>
                        </div>
                        <div>
                          <p class="text-sm text-gray-400 dark:text-zinc-500 font-medium">Esperando detección...</p>
                          <p class="text-[11px] text-gray-300 dark:text-zinc-600">Coloque el rostro frente a la cámara</p>
                        </div>
                      </div>
                    </div>

                    <!-- Identified state -->
                    <div v-if="identifiedUser">
                      <p class="text-[10px] font-semibold text-gray-400 dark:text-zinc-500 uppercase tracking-widest mb-3">Identidad verificada</p>
                      <div class="flex items-center gap-3">
                        <div class="w-12 h-12 rounded bg-gray-700 dark:bg-zinc-700 flex items-center justify-center flex-shrink-0">
                          <span class="text-base font-bold text-white">{{ getInitials(identifiedUser.name) }}</span>
                        </div>
                        <div class="flex-1 min-w-0">
                          <p class="text-base font-bold text-gray-800 dark:text-white truncate leading-tight">{{ identifiedUser.name }}</p>
                          <p class="text-xs text-gray-500 dark:text-zinc-400 tabular-nums mt-0.5">CC {{ identifiedUser.cc }}</p>
                        </div>
                      </div>
                      <!-- Verification metrics -->
                      <div class="mt-3 grid grid-cols-2 gap-px bg-gray-200 dark:bg-zinc-700 rounded overflow-hidden">
                        <div class="bg-white dark:bg-zinc-800 px-3 py-2">
                          <p class="text-[9px] text-gray-400 dark:text-zinc-500 uppercase tracking-wider font-medium">Precisión</p>
                          <p class="text-lg font-bold tabular-nums leading-tight" :class="identifiedUser.distance < 0.3 ? 'text-emerald-600 dark:text-emerald-400' : 'text-amber-600 dark:text-amber-400'">{{ getScorePercent(identifiedUser.distance) }}%</p>
                        </div>
                        <div class="bg-white dark:bg-zinc-800 px-3 py-2">
                          <p class="text-[9px] text-gray-400 dark:text-zinc-500 uppercase tracking-wider font-medium">Estado</p>
                          <p class="text-sm font-semibold leading-tight mt-0.5" :class="identifiedUser.distance < 0.3 ? 'text-emerald-600 dark:text-emerald-400' : 'text-amber-600 dark:text-amber-400'">
                            {{ identifiedUser.distance < 0.3 ? 'Confirmado' : 'Aceptable' }}
                          </p>
                        </div>
                      </div>
                    </div>
                  </div>

                  <!-- Divider -->
                  <div class="border-t border-gray-200 dark:border-zinc-800"></div>

                  <!-- ---- ZONE 3: ACTION ---- -->
                  <div class="px-5 pt-4 pb-5 flex-1 flex flex-col">
                    <!-- No user yet -->
                    <div v-if="!identifiedUser" class="flex-1 flex items-center justify-center">
                      <p class="text-xs text-gray-300 dark:text-zinc-700 font-medium">Las acciones se habilitarán al detectar un empleado</p>
                    </div>

                    <!-- User identified -->
                    <div v-if="identifiedUser" class="flex-1 flex flex-col">
                      <p class="text-[10px] font-semibold text-gray-400 dark:text-zinc-500 uppercase tracking-widest mb-3">Acción</p>

                      <!-- Shift completed -->
                      <div v-if="allowedEvents.length === 0" class="py-4 text-center">
                        <svg class="w-8 h-8 text-gray-300 dark:text-zinc-600 mx-auto mb-2" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <p class="text-sm font-semibold text-gray-700 dark:text-zinc-300">Jornada completada</p>
                        <p class="text-xs text-gray-400 dark:text-zinc-500 mt-0.5">No hay más acciones disponibles hoy</p>
                      </div>

                      <template v-if="allowedEvents.length > 0">
                        <!-- Auto-suggested action (first = recommended) -->
                        <div v-if="!selectedEvent" class="space-y-2 flex-1">
                          <!-- Primary suggested action -->
                          <button @click="selectedEvent = allowedEvents[0].type"
                                  class="w-full py-2.5 px-4 rounded bg-gray-800 dark:bg-zinc-200 text-white dark:text-gray-900 font-semibold text-sm transition-colors hover:bg-gray-900 dark:hover:bg-white text-left flex items-center justify-between">
                            <span>{{ allowedEvents[0].label }}</span>
                            <span class="text-[10px] uppercase tracking-wider font-medium opacity-50">Sugerido</span>
                          </button>

                          <!-- Other options (if more than 1) -->
                          <template v-for="(evt, idx) in allowedEvents.slice(1)" :key="evt.type">
                            <button @click="selectedEvent = evt.type"
                                    class="w-full py-2.5 px-4 rounded border border-gray-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-gray-700 dark:text-zinc-300 font-medium text-sm transition-colors hover:bg-gray-50 dark:hover:bg-zinc-700 text-left">
                              {{ evt.label }}
                            </button>
                          </template>
                        </div>

                        <!-- Selected → Confirm step -->
                        <div v-if="selectedEvent" class="flex-1 flex flex-col">
                          <!-- Selected action display -->
                          <div class="rounded bg-gray-50 dark:bg-zinc-800 border border-gray-200 dark:border-zinc-700 px-4 py-3 mb-3">
                            <p class="text-[10px] text-gray-400 dark:text-zinc-500 uppercase tracking-wider font-medium">Tipo de registro</p>
                            <p class="text-base font-bold text-gray-800 dark:text-white mt-0.5">{{ getEventLabel(selectedEvent) }}</p>
                          </div>

                          <!-- Last action context -->
                          <div v-if="lastActionForUser" class="text-[11px] text-gray-400 dark:text-zinc-500 mb-3 flex items-center gap-1.5">
                            <svg class="w-3 h-3 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            Último registro: {{ getEventLabel(lastActionForUser.event_type) }} a las {{ formatTime(lastActionForUser.event_at) }}
                          </div>

                          <div class="mt-auto space-y-2">
                            <!-- Confirm button -->
                            <button @click="registerEvent(selectedEvent)"
                                    :disabled="recording"
                                    class="w-full py-2.5 rounded bg-gray-800 dark:bg-zinc-200 text-white dark:text-gray-900 font-semibold text-sm transition-colors hover:bg-gray-900 dark:hover:bg-white disabled:opacity-40 disabled:cursor-not-allowed flex items-center justify-center gap-2">
                              <svg v-if="recording" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                              <span>{{ recording ? 'Registrando...' : 'Confirmar registro' }}</span>
                            </button>

                            <!-- Back to selection -->
                            <button v-if="!recording && allowedEvents.length > 1" @click="selectedEvent = null"
                                    class="w-full py-2 rounded border border-gray-300 dark:border-zinc-700 text-gray-500 dark:text-zinc-400 font-medium text-xs transition-colors hover:bg-gray-50 dark:hover:bg-zinc-800">
                              Cambiar acción
                            </button>
                          </div>
                        </div>
                      </template>

                      <!-- Reset identification -->
                      <button v-if="!recording" @click="resetIdentification"
                              class="mt-3 w-full py-2 text-[11px] font-medium text-gray-400 dark:text-zinc-500 hover:text-gray-600 dark:hover:text-zinc-300 transition-colors">
                        Identificar otro empleado
                      </button>
                    </div>
                  </div>

                  <!-- Bottom bar -->
                  <div class="px-4 py-1.5 border-t border-gray-100 dark:border-zinc-800 flex items-center justify-between">
                    <span class="text-[9px] text-gray-300 dark:text-zinc-700 font-medium tracking-wide">105 POS Pro · Biometría v1.0</span>
                    <span v-if="allProfiles.length > 0" class="text-[9px] text-gray-300 dark:text-zinc-700 tabular-nums">{{ allProfiles.length }} perfil{{ allProfiles.length !== 1 ? 'es' : '' }}</span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </Transition>
      </Teleport>

    <!-- Toast -->
    <Transition name="fade">
      <div v-if="toast.show" class="fixed bottom-6 right-6 z-[70] max-w-sm">
        <div class="rounded-md px-4 py-3 shadow-lg border text-sm font-medium" :class="toast.type === 'success'
              ? 'bg-white dark:bg-zinc-900 border-gray-200 dark:border-zinc-700 text-gray-800 dark:text-zinc-200 border-l-4 border-l-emerald-600'
              : 'bg-white dark:bg-zinc-900 border-gray-200 dark:border-zinc-700 text-gray-800 dark:text-zinc-200 border-l-4 border-l-rose-600'">
          {{ toast.message }}
        </div>
      </div>
    </Transition>

    <!-- Modal de Enrolamiento -->
    <BiometricEnrollModal
      :visible="showEnrollModal"
      @close="showEnrollModal = false"
      @enrolled="handleEnrolled"
      @navigate="handleNavigate"
    />

    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted, nextTick } from 'vue'
import { useFaceRecognition } from '../composables/useFaceRecognition.js'
import biometricService from '../services/biometricService.js'
import BiometricEnrollModal from './BiometricEnrollModal.vue'

const emit = defineEmits(['change-module'])

const {
  isModelLoading,
  modelError,
  isCameraActive,
  faceDetected,
  MATCH_THRESHOLD,
  loadModels,
  startCamera,
  stopCamera,
  captureImage,
  extractDescriptor,
  startFaceGuide,
} = useFaceRecognition()

// State
const showEnrollModal = ref(false)
const showVerifyModal = ref(false)
const verifyVideoRef = ref(null)
const verifyOverlayRef = ref(null)
const allProfiles = ref([])       // All enrolled profiles with descriptors
const identifiedUser = ref(null)  // { user_id, name, cc, distance }
const recording = ref(false)
const identifyingFace = ref(false)
const selectedEvent = ref(null)   // Selected event type before confirm

// Auto-identification interval
let identifyTick = null
const IDENTIFY_STABLE_DELAY = 800 // ms face must be stable before identifying

// Summary
const summary = ref({})

// History
const history = ref([])
const historyDate = ref(new Date().toISOString().split('T')[0])
const historyLoading = ref(false)

// Accordion state for employee cards
const expandedEmployees = ref(new Set())

const toggleEmployee = (userId) => {
  const next = new Set(expandedEmployees.value)
  if (next.has(userId)) {
    next.delete(userId)
  } else {
    next.add(userId)
  }
  expandedEmployees.value = next
}

// Group history by employee with hours calculation
const groupedHistory = computed(() => {
  if (!history.value.length) return []

  const groups = {}
  for (const log of history.value) {
    const uid = log.user_id
    if (!groups[uid]) {
      groups[uid] = {
        user_id: uid,
        user_name: log.user_name,
        user_cc: log.user_cc,
        events: [],
      }
    }
    groups[uid].events.push(log)
  }

  return Object.values(groups).map(group => {
    const events = [...group.events].sort((a, b) => new Date(a.event_at) - new Date(b.event_at))

    const entries = events.filter(e => e.event_type === 'entry')
    const exits = events.filter(e => e.event_type === 'exit')
    const breakStarts = events.filter(e => e.event_type === 'break_start')
    const breakEnds = events.filter(e => e.event_type === 'break_end')

    const firstEntry = entries[0] || null
    const lastExit = exits[exits.length - 1] || null
    const isActive = entries.length > 0 && exits.length === 0
    const onBreak = breakStarts.length > breakEnds.length

    let workedMs = 0
    if (firstEntry) {
      const endTime = lastExit ? new Date(lastExit.event_at) : new Date()
      workedMs = endTime - new Date(firstEntry.event_at)

      for (let i = 0; i < breakStarts.length; i++) {
        const bStart = new Date(breakStarts[i].event_at)
        const bEnd = breakEnds[i] ? new Date(breakEnds[i].event_at) : (isActive ? new Date() : null)
        if (bEnd) {
          workedMs -= (bEnd - bStart)
        }
      }
    }

    workedMs = Math.max(0, workedMs)
    const workedHours = Math.floor(workedMs / 3600000)
    const workedMinutes = Math.floor((workedMs % 3600000) / 60000)

    return {
      ...group,
      events,
      firstEntry,
      lastExit,
      isActive,
      onBreak,
      workedMs,
      workedLabel: firstEntry ? `${workedHours}h ${workedMinutes}m` : '—',
      totalEvents: events.length,
    }
  }).sort((a, b) => {
    if (a.isActive !== b.isActive) return a.isActive ? -1 : 1
    return new Date(b.firstEntry?.event_at || 0) - new Date(a.firstEntry?.event_at || 0)
  })
})

// Toast
const toast = ref({ show: false, message: '', type: 'success' })

// Smart allowed events — determine what the identified user can do based on today's history
const allowedEvents = computed(() => {
  if (!identifiedUser.value) return []

  const uid = identifiedUser.value.user_id
  const todayLogs = history.value.filter(l => l.user_id === uid)
  const types = todayLogs.map(l => l.event_type)

  const hasEntry = types.includes('entry')
  const hasExit = types.includes('exit')
  const breakStarts = types.filter(t => t === 'break_start').length
  const breakEnds = types.filter(t => t === 'break_end').length
  const onBreak = breakStarts > breakEnds

  // If shift is complete (entry + exit), no more actions
  if (hasEntry && hasExit) return []

  const events = []

  const EVENT_DEFS = {
    entry: {
      label: 'Inicio Jornada', description: 'Registrar entrada',
      iconPath: 'M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9',
      iconBg: 'bg-gray-100 dark:bg-zinc-800',
      iconColor: 'text-slate-700 dark:text-zinc-300',
      hoverClass: 'border-l-2 border-l-emerald-600 dark:border-l-emerald-500 hover:bg-gray-50 dark:hover:bg-zinc-800',
    },
    break_start: {
      label: 'Inicio Break', description: 'Pausa — almuerzo o descanso',
      iconPath: 'M14.25 9v6m-4.5 0V9M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
      iconBg: 'bg-gray-100 dark:bg-zinc-800',
      iconColor: 'text-slate-700 dark:text-zinc-300',
      hoverClass: 'border-l-2 border-l-amber-500 dark:border-l-amber-400 hover:bg-gray-50 dark:hover:bg-zinc-800',
    },
    break_end: {
      label: 'Fin Break', description: 'Retorno de almuerzo o descanso',
      iconPath: 'M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
      iconBg: 'bg-gray-100 dark:bg-zinc-800',
      iconColor: 'text-slate-700 dark:text-zinc-300',
      hoverClass: 'border-l-2 border-l-blue-600 dark:border-l-blue-500 hover:bg-gray-50 dark:hover:bg-zinc-800',
    },
    exit: {
      label: 'Fin Jornada', description: 'Registrar salida',
      iconPath: 'M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75',
      iconBg: 'bg-gray-100 dark:bg-zinc-800',
      iconColor: 'text-slate-700 dark:text-zinc-300',
      hoverClass: 'border-l-2 border-l-slate-500 dark:border-l-slate-400 hover:bg-gray-50 dark:hover:bg-zinc-800',
    },
  }

  // No entry yet → only allow entry
  if (!hasEntry) {
    events.push({ type: 'entry', ...EVENT_DEFS.entry })
    return events
  }

  // Has entry, no exit → allow break or exit
  if (onBreak) {
    // Currently on break → only allow end break
    events.push({ type: 'break_end', ...EVENT_DEFS.break_end })
  } else {
    // Working → allow start break or exit
    events.push({ type: 'break_start', ...EVENT_DEFS.break_start })
    events.push({ type: 'exit', ...EVENT_DEFS.exit })
  }

  return events
})

// Last recorded action for the identified user (context display)
const lastActionForUser = computed(() => {
  if (!identifiedUser.value) return null
  const uid = identifiedUser.value.user_id
  const userLogs = history.value
    .filter(l => l.user_id === uid)
    .sort((a, b) => new Date(b.event_at) - new Date(a.event_at))
  return userLogs[0] || null
})

// Computed
const summaryBarWidth = computed(() => {
  const total = (summary.value.entries_today ?? 0) + (summary.value.exits_today ?? 0)
  if (total === 0) return 0
  return Math.min(100, (total / Math.max(summary.value.total_users ?? 1, 1)) * 50)
})

// ===== ENROLAMIENTO =====
const openEnrollModal = () => {
  showEnrollModal.value = true
}

const handleEnrolled = async () => {
  showEnrollModal.value = false
  await loadSummary()
}

const handleNavigate = (module) => {
  showEnrollModal.value = false
  emit('change-module', module)
}

// ===== VERIFICACIÓN 1:N (Modal) =====
const openVerifyModal = async () => {
  showVerifyModal.value = true
  identifiedUser.value = null
  selectedEvent.value = null
  await nextTick()
  await initializeCamera()
}

const closeVerifyModal = () => {
  stopIdentifyWatch()
  stopCamera()
  allProfiles.value = []
  identifiedUser.value = null
  selectedEvent.value = null
  showVerifyModal.value = false
}

const initializeCamera = async () => {
  const loaded = await loadModels()
  if (!loaded) return

  await nextTick()
  if (!verifyVideoRef.value) return
  const started = await startCamera(verifyVideoRef.value)
  if (!started) return

  // Load all enrolled descriptors
  try {
    const response = await biometricService.getAllDescriptors()
    if (response.success && response.data?.length > 0) {
      allProfiles.value = response.data

      const startGuide = () => {
        if (verifyOverlayRef.value) {
          verifyOverlayRef.value.width = verifyVideoRef.value.videoWidth
          verifyOverlayRef.value.height = verifyVideoRef.value.videoHeight
        }
        startFaceGuide(verifyVideoRef.value, verifyOverlayRef.value)
        startIdentifyWatch()
      }

      if (verifyVideoRef.value.readyState >= 1) {
        startGuide()
      } else {
        verifyVideoRef.value.addEventListener('loadedmetadata', startGuide, { once: true })
      }
    }
  } catch {
    allProfiles.value = []
  }
}

// 1:N identification — compare live face against all enrolled profiles
const startIdentifyWatch = () => {
  stopIdentifyWatch()
  let faceStableStart = null

  identifyTick = setInterval(async () => {
    if (!isCameraActive.value || identifiedUser.value || identifyingFace.value || recording.value) return

    if (faceDetected.value) {
      if (!faceStableStart) faceStableStart = Date.now()
      const elapsed = Date.now() - faceStableStart

      if (elapsed >= IDENTIFY_STABLE_DELAY) {
        identifyingFace.value = true
        faceStableStart = null

        try {
          const descriptor = await extractDescriptor(verifyVideoRef.value)
          if (!descriptor) {
            identifyingFace.value = false
            return
          }

          const faceapi = (await import('face-api.js'))
          let bestMatch = null
          let bestDistance = Infinity

          for (const profile of allProfiles.value) {
            const baseFloat32 = new Float32Array(profile.descriptors)
            const distance = faceapi.euclideanDistance(descriptor, baseFloat32)
            if (distance < bestDistance) {
              bestDistance = distance
              bestMatch = profile
            }
          }

          if (bestMatch && bestDistance < MATCH_THRESHOLD) {
            identifiedUser.value = {
              user_id: bestMatch.user_id,
              name: bestMatch.name,
              cc: bestMatch.cc,
              distance: Math.round(bestDistance * 10000) / 10000,
            }
            // Stop polling — user now picks action
            stopIdentifyWatch()
          }
        } catch {
          // Silently retry on next tick
        } finally {
          identifyingFace.value = false
        }
      }
    } else {
      faceStableStart = null
    }
  }, 250)
}

const stopIdentifyWatch = () => {
  if (identifyTick) {
    clearInterval(identifyTick)
    identifyTick = null
  }
}

// User picks event type → register attendance
const registerEvent = async (eventType) => {
  if (!identifiedUser.value || recording.value) return

  recording.value = true
  try {
    const capturedImg = captureImage(verifyVideoRef.value)

    const response = await biometricService.recordAttendance(
      identifiedUser.value.user_id,
      eventType,
      identifiedUser.value.distance,
      capturedImg
    )

    showToast(
      `${identifiedUser.value.name}: ${response.message || 'Punteo registrado'}`,
      'success'
    )
    closeVerifyModal()
    await refreshAll()
  } catch (error) {
    const msg = error.response?.data?.message || 'Error al registrar punteo'
    showToast(msg, 'error')
  } finally {
    recording.value = false
  }
}

// Reset to identify another employee
const resetIdentification = () => {
  identifiedUser.value = null
  selectedEvent.value = null
  startIdentifyWatch()
}

// ===== DATA LOADING =====
const refreshAll = async () => {
  await Promise.all([loadSummary(), loadHistory()])
}

const loadSummary = async () => {
  try {
    const response = await biometricService.getTodaySummary()
    summary.value = response.data || {}
  } catch {
    summary.value = {}
  }
}

const loadHistory = async () => {
  historyLoading.value = true
  try {
    const response = await biometricService.getAttendanceHistory({ date: historyDate.value })
    history.value = response.data || []
  } catch {
    history.value = []
  } finally {
    historyLoading.value = false
  }
}

// ===== HELPERS =====
const EVENT_TYPE_CONFIG = {
  entry:       { label: 'Entrada',     color: 'emerald' },
  exit:        { label: 'Salida',      color: 'blue' },
  break_start: { label: 'Inicio Break', color: 'amber' },
  break_end:   { label: 'Fin Break',   color: 'indigo' },
}

const getEventLabel = (type) => EVENT_TYPE_CONFIG[type]?.label || type

const getEventDotClass = (type) => {
  const map = {
    entry:       'border-emerald-500 bg-emerald-50 dark:bg-emerald-950',
    exit:        'border-blue-500 bg-blue-50 dark:bg-blue-950',
    break_start: 'border-amber-500 bg-amber-50 dark:bg-amber-950',
    break_end:   'border-indigo-500 bg-indigo-50 dark:bg-indigo-950',
  }
  return map[type] || 'border-gray-500 bg-gray-50 dark:bg-gray-950'
}

const getEventAvatarClass = (type) => {
  const map = {
    entry:       'bg-emerald-50 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-400',
    exit:        'bg-blue-50 dark:bg-blue-950 text-blue-700 dark:text-blue-400',
    break_start: 'bg-amber-50 dark:bg-amber-950 text-amber-700 dark:text-amber-400',
    break_end:   'bg-indigo-50 dark:bg-indigo-950 text-indigo-700 dark:text-indigo-400',
  }
  return map[type] || 'bg-gray-50 dark:bg-gray-950 text-gray-700 dark:text-gray-400'
}

const getEventBadgeClass = (type) => {
  const map = {
    entry:       'bg-emerald-50 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-400 border-emerald-100 dark:border-emerald-800',
    exit:        'bg-blue-50 dark:bg-blue-950 text-blue-700 dark:text-blue-400 border-blue-100 dark:border-blue-800',
    break_start: 'bg-amber-50 dark:bg-amber-950 text-amber-700 dark:text-amber-400 border-amber-100 dark:border-amber-800',
    break_end:   'bg-indigo-50 dark:bg-indigo-950 text-indigo-700 dark:text-indigo-400 border-indigo-100 dark:border-indigo-800',
  }
  return map[type] || 'bg-gray-50 dark:bg-gray-950 text-gray-700 dark:text-gray-400 border-gray-100 dark:border-gray-800'
}

const formatTime = (dateString) => {
  if (!dateString) return ''
  const date = new Date(dateString)
  return date.toLocaleTimeString('es-ES', { hour: '2-digit', minute: '2-digit', hour12: true }).toUpperCase()
}

const formatDateLabel = (dateStr) => {
  if (!dateStr) return 'hoy'
  const today = new Date().toISOString().split('T')[0]
  if (dateStr === today) return 'día de hoy'
  const date = new Date(dateStr + 'T12:00:00')
  return date.toLocaleDateString('es-ES', { day: 'numeric', month: 'long', year: 'numeric' })
}

const getInitials = (name) => {
  if (!name) return '?'
  return name.split(' ').map(w => w[0]).slice(0, 2).join('').toUpperCase()
}

const getScorePercent = (score) => {
  if (score == null) return 0
  return ((1 - score) * 100).toFixed(0)
}

const getScoreColor = (score) => {
  if (score == null) return 'text-gray-400 dark:text-zinc-500'
  if (score < 0.2) return 'text-emerald-600 dark:text-emerald-400'
  if (score < 0.35) return 'text-amber-600 dark:text-amber-400'
  return 'text-rose-600 dark:text-rose-400'
}

const getScoreBadgeClass = (score) => {
  if (score == null) return 'bg-gray-50 dark:bg-zinc-800 text-gray-500 dark:text-zinc-400 border-gray-200 dark:border-zinc-700'
  if (score < 0.2) return 'bg-emerald-50 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-400 border-emerald-100 dark:border-emerald-800'
  if (score < 0.35) return 'bg-amber-50 dark:bg-amber-950 text-amber-700 dark:text-amber-400 border-amber-100 dark:border-amber-800'
  return 'bg-rose-50 dark:bg-rose-950 text-rose-700 dark:text-rose-400 border-rose-100 dark:border-rose-800'
}

const getScoreLabel = (score) => {
  if (score == null) return 'N/A'
  if (score < 0.2) return 'Excelente'
  if (score < 0.35) return 'Aceptable'
  return 'Bajo'
}

const showToast = (message, type = 'success') => {
  toast.value = { show: true, message, type }
  setTimeout(() => { toast.value.show = false }, 4000)
}

onMounted(async () => {
  await refreshAll()
})

onUnmounted(() => {
  stopIdentifyWatch()
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
.modal-enter-active {
  transition: all 0.25s ease-out;
}
.modal-leave-active {
  transition: all 0.2s ease-in;
}
.modal-enter-from {
  opacity: 0;
}
.modal-enter-from > div:last-child {
  transform: scale(0.95) translateY(10px);
}
.modal-leave-to {
  opacity: 0;
}
</style>
