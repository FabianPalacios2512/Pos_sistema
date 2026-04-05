<template>
  <div class="h-full font-sans bg-gray-50 dark:bg-[#111113] transition-colors duration-200 flex flex-col overflow-hidden">

    <!-- ═══ TOP BAR: Title + Date + Refresh ═══ -->
    <div class="flex items-center justify-between px-8 py-4 border-b border-gray-200 dark:border-zinc-800 flex-shrink-0">
      <div class="flex items-baseline gap-3">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Mi Jornada</h1>
        <span class="text-sm text-gray-500 dark:text-zinc-400 tabular-nums capitalize">{{ todayFormatted }}</span>
      </div>
      <button @click="refreshHistory" class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-zinc-200 bg-white dark:bg-zinc-900 border border-gray-200 dark:border-zinc-700 rounded-md hover:bg-gray-50 dark:hover:bg-zinc-800 transition-colors">
        Refrescar
      </button>
    </div>

      <!-- Loading -->
      <div v-if="!ready" class="flex-1 flex items-center justify-center">
        <svg class="animate-spin w-5 h-5 text-gray-300 dark:text-zinc-600" fill="none" viewBox="0 0 24 24">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
        </svg>
        <span class="ml-3 text-sm text-gray-400 dark:text-zinc-500">Cargando jornada...</span>
      </div>

      <!-- Not enrolled -->
      <div v-else-if="!isEnrolled" class="flex-1 flex items-center justify-center px-6">
        <div class="max-w-md text-center">
          <div class="w-10 h-10 rounded-md bg-amber-50 dark:bg-amber-950 border border-amber-200 dark:border-amber-800 flex items-center justify-center mx-auto mb-4">
            <svg class="w-5 h-5 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/>
            </svg>
          </div>
          <p class="text-sm font-semibold text-gray-900 dark:text-white">Perfil biométrico no registrado</p>
          <p class="text-xs text-gray-500 dark:text-zinc-400 mt-1.5 leading-relaxed">Contacte al administrador para registrar su perfil facial.</p>
        </div>
      </div>

      <!-- ═══ MAIN CONTENT ═══ -->
      <template v-else>
        <div class="flex-1 min-h-0 grid grid-cols-1 lg:grid-cols-[1fr_380px]">

          <!-- ═══ LEFT PANEL ═══ -->
          <div class="border-r-0 lg:border-r border-gray-200 dark:border-zinc-800 flex flex-col min-h-0">

            <!-- Clock + Status + CTA Block — vertically centered, takes remaining space -->
            <div class="flex-1 min-h-0 flex flex-col items-center justify-center px-8 py-8 bg-white dark:bg-zinc-900/50 border-b border-gray-200 dark:border-zinc-800">

              <!-- Employee + Badge row -->
              <div class="flex items-center gap-3 mb-8">
                <div class="w-8 h-8 rounded-md bg-gray-900 dark:bg-zinc-700 flex items-center justify-center flex-shrink-0">
                  <span class="text-xs font-bold text-white leading-none">{{ userInitials }}</span>
                </div>
                <div class="min-w-0">
                  <p class="text-sm font-medium text-gray-900 dark:text-white">{{ currentUser?.name }}</p>
                  <p class="text-xs text-gray-400 dark:text-zinc-500 tabular-nums">CC {{ currentUser?.cc || '—' }}</p>
                </div>
                <span class="ml-3 px-3 py-1 rounded-md text-xs font-bold uppercase tracking-wide border" :class="heroBadgeClass">
                  <span class="inline-block w-1.5 h-1.5 rounded-sm mr-1.5 align-middle" :class="heroBadgeDot"></span>
                  {{ currentStatusLabel }}
                </span>
              </div>

              <!-- Live Clock -->
              <p class="text-6xl font-black text-gray-900 dark:text-white tracking-tight tabular-nums leading-none">{{ liveClock }}</p>
              <p class="text-base text-gray-500 dark:text-zinc-400 mt-2 tabular-nums capitalize">{{ liveDate }}</p>

              <!-- Worked Time -->
              <div v-if="workedTime !== '0h 0m'" class="mt-5 text-center">
                <p class="text-xs font-semibold text-gray-400 dark:text-zinc-500 uppercase tracking-wider mb-1">Tiempo trabajado</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white tabular-nums">{{ workedTime }}</p>
              </div>

              <!-- CTA -->
              <div v-if="suggestedAction" class="mt-8 flex items-center gap-3">
                <button
                  @click="handleCtaClick(suggestedAction)"
                  :disabled="checkingCash"
                  class="py-3.5 px-10 rounded-md bg-indigo-600 dark:bg-indigo-500 hover:bg-indigo-700 dark:hover:bg-indigo-400 text-white text-sm font-semibold transition-colors flex items-center gap-2.5 disabled:opacity-50 disabled:cursor-not-allowed"
                >
                  <svg v-if="checkingCash" class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                  <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 3.75H6A2.25 2.25 0 0 0 3.75 6v1.5M16.5 3.75H18A2.25 2.25 0 0 1 20.25 6v1.5M20.25 16.5V18A2.25 2.25 0 0 1 18 20.25h-1.5M3.75 16.5V18A2.25 2.25 0 0 0 6 20.25h1.5M9 12a3 3 0 1 0 6 0 3 3 0 0 0-6 0Z"/>
                  </svg>
                  {{ checkingCash ? 'Verificando caja...' : ctaLabel }}
                </button>
                <button
                  v-if="suggestedAction === 'exit' && !onBreak"
                  @click="openModal('break_start')"
                  class="py-3.5 px-5 rounded-md bg-white dark:bg-zinc-800 hover:bg-gray-50 dark:hover:bg-zinc-700 text-gray-600 dark:text-zinc-300 text-sm font-medium border border-gray-200 dark:border-zinc-700 transition-colors flex items-center gap-2"
                >
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 5.25v13.5m-7.5-13.5v13.5"/></svg>
                  Break
                </button>
                <button
                  v-if="onBreak"
                  @click="openModal('break_end')"
                  class="py-3.5 px-5 rounded-md bg-white dark:bg-zinc-800 hover:bg-gray-50 dark:hover:bg-zinc-700 text-amber-600 dark:text-amber-400 text-sm font-medium border border-amber-200 dark:border-amber-700 transition-colors flex items-center gap-2"
                >
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5.25 5.653c0-.856.917-1.398 1.667-.986l11.54 6.347a1.125 1.125 0 0 1 0 1.972l-11.54 6.347a1.125 1.125 0 0 1-1.667-.986V5.653Z"/></svg>
                  Fin Break
                </button>
              </div>

              <!-- Shift completed -->
              <div v-else class="mt-8 flex items-center gap-2">
                <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span class="text-base font-medium text-gray-700 dark:text-zinc-300">Jornada completada</span>
              </div>
            </div>

            <!-- ═══ METRICS BAR ═══ -->
            <div class="flex-shrink-0 grid grid-cols-4 divide-x divide-gray-200 dark:divide-zinc-800 bg-white dark:bg-zinc-900/50">
              <div class="px-6 py-5">
                <p class="text-xs font-semibold text-gray-500 dark:text-zinc-400 uppercase tracking-wide">Entrada</p>
                <p class="text-xl font-bold text-gray-900 dark:text-white tabular-nums mt-1">{{ entryTimeDisplay }}</p>
              </div>
              <div class="px-6 py-5">
                <p class="text-xs font-semibold text-gray-500 dark:text-zinc-400 uppercase tracking-wide">Salida</p>
                <p class="text-xl font-bold text-gray-900 dark:text-white tabular-nums mt-1">{{ exitTimeDisplay }}</p>
              </div>
              <div class="px-6 py-5">
                <p class="text-xs font-semibold text-gray-500 dark:text-zinc-400 uppercase tracking-wide">Breaks</p>
                <p class="text-xl font-bold text-gray-900 dark:text-white tabular-nums mt-1">{{ breaksCount }}</p>
              </div>
              <div class="px-6 py-5">
                <p class="text-xs font-semibold text-gray-500 dark:text-zinc-400 uppercase tracking-wide">Trabajado</p>
                <p class="text-xl font-bold text-gray-900 dark:text-white tabular-nums mt-1">{{ workedTime }}</p>
              </div>
            </div>
          </div>

          <!-- ═══ RIGHT PANEL: Today's Records ═══ -->
          <div class="bg-white dark:bg-zinc-900/50 flex flex-col min-h-0">

            <!-- Header -->
            <div class="px-6 py-3.5 border-b border-gray-200 dark:border-zinc-800 flex items-center justify-between flex-shrink-0">
              <p class="text-sm font-semibold text-gray-900 dark:text-white">Registros de hoy</p>
              <span class="text-xs text-gray-500 dark:text-zinc-400 tabular-nums">{{ myHistory.length }} {{ myHistory.length === 1 ? 'evento' : 'eventos' }}</span>
            </div>

            <!-- Event list -->
            <ul v-if="myHistory.length > 0" class="flex-1 overflow-y-auto">
              <li v-for="log in sortedHistory" :key="log.id" class="px-6 py-3.5 border-b border-gray-100 dark:border-zinc-800/60 flex items-center justify-between">
                <div class="flex items-center gap-3">
                  <span class="w-2 h-2 rounded-sm flex-shrink-0" :class="eventDotColor(log.event_type)"></span>
                  <div>
                    <p class="text-sm font-medium text-gray-900 dark:text-white">{{ actionLabels[log.event_type] || log.event_type }}</p>
                    <p class="text-xs text-gray-400 dark:text-zinc-500 tabular-nums mt-0.5">{{ formatTime(log.event_at) }}</p>
                  </div>
                </div>
                <span class="text-xs font-semibold px-2.5 py-0.5 rounded-md border tabular-nums" :class="scoreBadgeClass(log.verification_score)">{{ scorePercent(log.verification_score) }}%</span>
              </li>
            </ul>

            <!-- Empty state -->
            <div v-else class="flex-1 flex flex-col items-center justify-center px-5 py-16">
              <svg class="w-6 h-6 text-gray-300 dark:text-zinc-600 mb-3" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
              <p class="text-sm font-medium text-gray-500 dark:text-zinc-400">Sin registros</p>
              <p class="text-xs text-gray-400 dark:text-zinc-500 mt-1">Registre su entrada para comenzar</p>
            </div>

            <!-- Footer -->
            <div class="flex-shrink-0 px-6 py-2.5 border-t border-gray-100 dark:border-zinc-800 flex items-center justify-between">
              <span class="text-[10px] text-gray-300 dark:text-zinc-700 font-medium tracking-wide">105 POS Pro · Biometría</span>
              <span class="px-2 py-0.5 rounded-md text-[10px] font-semibold uppercase tracking-wide border" :class="enrollBadgeClass">Activa</span>
            </div>
          </div>

        </div>
      </template>
  </div>

  <!-- ===== MODAL: Cash Open Recommendation (Flat Enterprise) ===== -->
  <Teleport to="body">
    <Transition
      enter-active-class="transition-all duration-200 ease-out"
      enter-from-class="opacity-0"
      enter-to-class="opacity-100"
      leave-active-class="transition-all duration-150 ease-in"
      leave-from-class="opacity-100"
      leave-to-class="opacity-0"
    >
      <div v-if="showCashBlockModal" class="fixed inset-0 z-[70] flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/50 backdrop-blur-[2px]" @click="showCashBlockModal = false"></div>

        <div class="relative bg-white dark:bg-zinc-900 rounded-md max-w-lg w-full border border-gray-200 dark:border-zinc-800 shadow-2xl overflow-hidden">

          <!-- Header -->
          <div class="px-6 py-5 border-b border-gray-200 dark:border-zinc-800">
            <div class="flex items-start gap-4">
              <div class="w-10 h-10 rounded-md bg-amber-50 dark:bg-amber-950 border border-amber-200 dark:border-amber-800 flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/>
                </svg>
              </div>
              <div>
                <h3 class="text-base font-black text-gray-900 dark:text-white uppercase tracking-tight">Caja Abierta Detectada</h3>
                <p class="text-sm text-gray-500 dark:text-zinc-400 mt-0.5">Recomendación antes de finalizar jornada</p>
              </div>
            </div>
          </div>

          <!-- Body -->
          <div class="px-6 py-5">
            <!-- Info grid -->
            <div class="rounded-md border border-gray-200 dark:border-zinc-700 divide-y divide-gray-200 dark:divide-zinc-700">
              <div class="flex">
                <div class="w-1/2 px-4 py-3 border-r border-gray-200 dark:border-zinc-700">
                  <p class="text-[11px] font-semibold text-gray-400 dark:text-zinc-500 uppercase tracking-widest">Sede</p>
                  <p class="text-sm font-bold text-gray-900 dark:text-white mt-0.5">{{ cashBlockWarehouse }}</p>
                </div>
                <div class="w-1/2 px-4 py-3">
                  <p class="text-[11px] font-semibold text-gray-400 dark:text-zinc-500 uppercase tracking-widest">Abierta desde</p>
                  <p class="text-sm font-bold text-gray-900 dark:text-white mt-0.5">{{ cashOpenedAtFormatted }}</p>
                </div>
              </div>
              <div class="px-4 py-3">
                <p class="text-[11px] font-semibold text-gray-400 dark:text-zinc-500 uppercase tracking-widest">Estado</p>
                <div class="flex items-center gap-2 mt-1">
                  <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                  <p class="text-sm font-semibold text-amber-600 dark:text-amber-400">Sesión de caja activa sin cerrar</p>
                </div>
              </div>
            </div>

            <!-- Recommendation -->
            <div class="mt-4 px-4 py-3 bg-amber-50 dark:bg-amber-950/50 border border-amber-200 dark:border-amber-800/50 rounded-md">
              <p class="text-sm text-amber-800 dark:text-amber-300 leading-relaxed">
                <span class="font-bold">Recomendación:</span> Realice el cierre de caja y arqueo antes de finalizar su jornada para evitar inconsistencias en el cuadre del día.
              </p>
            </div>
          </div>

          <!-- Footer -->
          <div class="px-6 py-4 border-t border-gray-200 dark:border-zinc-800 flex items-center justify-end gap-3">
            <button @click="proceedExitAnyway"
              class="px-5 py-2.5 text-sm font-semibold text-gray-600 dark:text-zinc-300 bg-white dark:bg-zinc-800 border border-gray-200 dark:border-zinc-700 rounded-md hover:bg-gray-50 dark:hover:bg-zinc-700 transition-colors">
              Hacer Después
            </button>
            <button @click="goToPos"
              class="px-5 py-2.5 text-sm font-bold text-white bg-gray-900 dark:bg-gray-700 hover:bg-black dark:hover:bg-gray-600 rounded-md transition-colors flex items-center gap-2">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 00-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 01-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 003 15h-.75M15 10.5a3 3 0 11-6 0 3 3 0 016 0zm3 0h.008v.008H18V10.5zm-12 0h.008v.008H6V10.5z"/></svg>
              Cerrar Caja
            </button>
          </div>
        </div>
      </div>
    </Transition>
  </Teleport>

  <!-- ===== MODAL: Camera Verification ===== -->
  <Teleport to="body">
    <Transition name="modal">
      <div v-if="showModal" class="fixed inset-0 z-[60] flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/50 backdrop-blur-[2px]" @click="closeModal"></div>

        <div class="relative bg-white dark:bg-zinc-900 rounded-md max-w-5xl w-full border border-gray-200 dark:border-zinc-800 overflow-hidden">

          <!-- Top bar -->
          <div class="h-11 px-4 flex items-center justify-between border-b border-gray-200 dark:border-zinc-800">
            <div class="flex items-center gap-2.5">
              <svg class="w-4 h-4 text-gray-400 dark:text-zinc-500" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
              <span class="text-sm font-semibold text-[#111827] dark:text-white">Verificación facial</span>
              <span class="text-xs text-gray-300 dark:text-zinc-600">·</span>
              <span class="text-xs text-gray-400 dark:text-zinc-500 tabular-nums">{{ actionLabels[pendingAction] }}</span>
            </div>
            <button @click="closeModal" class="p-1.5 rounded text-gray-400 dark:text-zinc-500 hover:text-gray-700 dark:hover:text-zinc-200 hover:bg-gray-100 dark:hover:bg-zinc-800 transition-colors">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
          </div>

          <!-- Grid: Camera + Panel -->
          <div class="grid grid-cols-1 lg:grid-cols-[1fr_320px]">

            <!-- Camera -->
            <div class="relative bg-[#111827] dark:bg-zinc-950" style="min-height: 420px;">
              <video ref="videoRef" class="absolute inset-0 w-full h-full object-cover" playsinline muted></video>
              <canvas ref="overlayRef" class="absolute inset-0 w-full h-full pointer-events-none"></canvas>

              <!-- Loading -->
              <div v-if="!isCameraActive" class="absolute inset-0 flex items-center justify-center">
                <div class="text-center">
                  <svg v-if="isModelLoading" class="animate-spin w-6 h-6 text-gray-500 mx-auto mb-2" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                  </svg>
                  <svg v-else-if="modelError" class="w-7 h-7 text-red-400 mx-auto mb-2" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/>
                  </svg>
                  <svg v-else class="w-7 h-7 text-gray-600 mx-auto mb-2" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                  </svg>
                  <p class="text-xs" :class="modelError ? 'text-red-300' : 'text-gray-500'">{{ modelError || (isModelLoading ? 'Cargando modelos...' : 'Iniciando cámara...') }}</p>
                </div>
              </div>

              <!-- HUD -->
              <template v-if="isCameraActive">
                <div class="absolute top-3 left-3">
                  <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded text-[10px] font-semibold tracking-wide bg-black/40 text-white/80 backdrop-blur-sm">
                    <span class="w-1.5 h-1.5 rounded-full bg-red-500 animate-pulse"></span>
                    EN VIVO
                  </span>
                </div>
                <div v-if="scanning && !verified" class="absolute top-3 right-3">
                  <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded text-[10px] font-medium bg-black/40 text-white/60 backdrop-blur-sm">
                    <svg class="w-3 h-3 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                    Escaneando
                  </span>
                </div>
                <!-- Confirmed bar -->
                <div v-if="verified" class="absolute bottom-0 left-0 right-0">
                  <div class="bg-gradient-to-t from-black/50 to-transparent px-4 pt-8 pb-3 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                      <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                      <span class="text-xs font-semibold text-white">Identidad confirmada</span>
                    </div>
                    <span class="text-xs text-white/60 tabular-nums font-medium">{{ precision }}%</span>
                  </div>
                </div>
              </template>
            </div>

            <!-- Right Panel -->
            <div class="border-t lg:border-t-0 lg:border-l border-gray-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 flex flex-col">
              <div class="flex-1 px-5 py-5 flex flex-col">

                <!-- Scanning state -->
                <div v-if="!verified" class="flex-1 flex flex-col items-center justify-center">
                  <div class="w-14 h-14 rounded-full border-2 border-gray-200 dark:border-zinc-700 flex items-center justify-center mb-4">
                    <svg class="w-6 h-6 text-gray-300 dark:text-zinc-600" :class="{ 'animate-pulse': scanning }" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                      <path stroke-linecap="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/>
                    </svg>
                  </div>
                  <p class="text-sm font-medium text-gray-500 dark:text-zinc-400">Escaneando rostro...</p>
                  <p class="text-[11px] text-gray-400 dark:text-zinc-500 mt-1">Mire directamente a la cámara</p>
                  <p v-if="showScanHint" class="text-[11px] text-amber-500 dark:text-amber-400 mt-4 text-center max-w-[200px] leading-relaxed">
                    Asegúrese de estar bien iluminado y mirando hacia la cámara.
                  </p>
                </div>

                <!-- Verified state -->
                <div v-else class="flex-1 flex flex-col">

                  <!-- Identity -->
                  <div class="mb-5">
                    <p class="text-[10px] font-medium text-gray-400 dark:text-zinc-500 uppercase tracking-wider mb-1.5">Empleado</p>
                    <p class="text-lg font-bold text-[#111827] dark:text-white leading-tight">{{ currentUser?.name }}</p>
                    <p class="text-xs text-gray-500 dark:text-zinc-400 mt-0.5 tabular-nums">CC {{ currentUser?.cc || '—' }}</p>
                  </div>

                  <!-- Precision -->
                  <div class="mb-5">
                    <p class="text-[10px] font-medium text-gray-400 dark:text-zinc-500 uppercase tracking-wider mb-1">Precisión</p>
                    <div class="flex items-center gap-2.5">
                      <span class="text-2xl font-bold tabular-nums" :class="precisionColor">{{ precision }}%</span>
                      <span class="px-2 py-0.5 rounded text-[10px] font-bold" :class="statusBadgeClass">{{ statusLabel }}</span>
                    </div>
                  </div>

                  <!-- Action -->
                  <div class="mb-5">
                    <p class="text-[10px] font-medium text-gray-400 dark:text-zinc-500 uppercase tracking-wider mb-1">Acción</p>
                    <p class="text-base font-semibold text-[#111827] dark:text-white">{{ actionLabels[pendingAction] }}</p>
                  </div>

                  <!-- Last action context -->
                  <div v-if="lastAction" class="mb-5">
                    <p class="text-[10px] font-medium text-gray-400 dark:text-zinc-500 uppercase tracking-wider mb-0.5">Última acción</p>
                    <p class="text-xs text-gray-600 dark:text-zinc-300 font-medium">{{ actionLabels[lastAction.event_type] }} — {{ formatTime(lastAction.event_at) }}</p>
                  </div>

                  <!-- Confirm -->
                  <div class="mt-auto">
                    <button
                      @click="confirmRegistration"
                      :disabled="recording"
                      class="w-full py-3 rounded-md bg-gray-900 dark:bg-white text-white dark:text-gray-900 font-semibold text-sm transition-colors hover:bg-black dark:hover:bg-gray-100 disabled:opacity-40 disabled:cursor-not-allowed flex items-center justify-center gap-2"
                    >
                      <svg v-if="recording" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                      </svg>
                      {{ recording ? 'Registrando...' : 'Confirmar registro' }}
                    </button>
                  </div>
                </div>

              </div>

              <!-- Footer -->
              <div class="px-4 py-1.5 border-t border-gray-100 dark:border-zinc-800 flex items-center justify-between">
                <span class="text-[9px] text-gray-300 dark:text-zinc-700 font-medium tracking-wide">105 POS Pro · Biometría</span>
                <span class="text-[9px] text-gray-300 dark:text-zinc-700 tabular-nums">Verificación 1:1</span>
              </div>
            </div>

          </div>
        </div>
      </div>
    </Transition>
  </Teleport>

  <!-- Toast -->
  <Teleport to="body">
    <Transition
      enter-active-class="transition-all duration-300 ease-out"
      enter-from-class="translate-y-4 opacity-0"
      enter-to-class="translate-y-0 opacity-100"
      leave-active-class="transition-all duration-200 ease-in"
      leave-from-class="translate-y-0 opacity-100"
      leave-to-class="translate-y-4 opacity-0"
    >
      <div
        v-if="toast.show"
        class="fixed bottom-6 right-6 z-[9999] flex items-center gap-3 px-4 py-2.5 rounded-md border text-xs font-medium"
        :class="toast.type === 'success'
          ? 'bg-emerald-50 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-400 border-emerald-200 dark:border-emerald-800'
          : 'bg-rose-50 dark:bg-rose-950 text-rose-700 dark:text-rose-400 border-rose-200 dark:border-rose-800'"
      >
        <svg v-if="toast.type === 'success'" class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        <svg v-else class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        {{ toast.message }}
      </div>
    </Transition>
  </Teleport>
</template>

<script setup>
import { ref, computed, nextTick, onMounted, onBeforeUnmount } from 'vue'
import { useRouter } from 'vue-router'
import { useFaceRecognition } from '../composables/useFaceRecognition.js'
import biometricService from '../services/biometricService.js'
import authService from '../services/authService.js'

const router = useRouter()

defineOptions({ inheritAttrs: false })

const {
  isModelLoading,
  modelError,
  isCameraActive,
  faceDetected,
  MATCH_THRESHOLD,
  loadModels,
  startCamera,
  stopCamera,
  compareFace,
  captureImage,
  startFaceGuide,
} = useFaceRecognition()

// ── Refs ──
const videoRef = ref(null)
const overlayRef = ref(null)
const ready = ref(false)
const isEnrolled = ref(false)
const myDescriptor = ref(null)
const myHistory = ref([])

// Live clock
const now = ref(new Date())
let clockInterval = null

// Modal
const showModal = ref(false)
const pendingAction = ref(null)
const verified = ref(false)
const verificationDistance = ref(null)
const scanning = ref(false)
const recording = ref(false)
const showScanHint = ref(false)

// Cash validation before exit
const checkingCash = ref(false)
const showCashBlockModal = ref(false)
const cashBlockWarehouse = ref('')
const cashBlockOpenedAt = ref('')

let scanInterval = null
let scanHintTimer = null
let consecutiveMatches = 0
const STABLE_MATCHES = 2

// ── Toast ──
const toast = ref({ show: false, message: '', type: 'success' })
let toastTimer = null
const showToast = (msg, type = 'success') => {
  if (toastTimer) clearTimeout(toastTimer)
  toast.value = { show: true, message: msg, type }
  toastTimer = setTimeout(() => { toast.value.show = false }, 4000)
}

// ── User ──
const currentUser = computed(() => authService.getUser())
const userId = computed(() => currentUser.value?.id)

// ── Labels ──
const actionLabels = {
  entry: 'Entrada',
  exit: 'Salida',
  break_start: 'Inicio de Break',
  break_end: 'Fin de Break',
}

// ── Live Clock ──
const liveClock = computed(() =>
  now.value.toLocaleTimeString('es-ES', { hour: '2-digit', minute: '2-digit', hour12: true }).toUpperCase()
)
const liveDate = computed(() =>
  now.value.toLocaleDateString('es-CO', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' })
)

// ── CTA label ──
const ctaLabel = computed(() => {
  const labels = { entry: 'REGISTRAR ENTRADA', exit: 'REGISTRAR SALIDA', break_start: 'INICIAR BREAK', break_end: 'FINALIZAR BREAK' }
  return labels[suggestedAction.value] || 'REGISTRAR'
})

// ── Main page computeds ──
const todayFormatted = computed(() =>
  new Date().toLocaleDateString('es-CO', { weekday: 'long', day: '2-digit', month: 'long', year: 'numeric' })
)

const sortedHistory = computed(() =>
  [...myHistory.value].sort((a, b) => new Date(a.event_at) - new Date(b.event_at))
)

const onBreak = computed(() => {
  const types = myHistory.value.map(l => l.event_type)
  return types.filter(t => t === 'break_start').length > types.filter(t => t === 'break_end').length
})

const suggestedAction = computed(() => {
  const types = myHistory.value.map(l => l.event_type)
  const hasEntry = types.includes('entry')
  const hasExit = types.includes('exit')
  if (hasEntry && hasExit) return null
  if (!hasEntry) return 'entry'
  return 'exit'
})

const currentStatusLabel = computed(() => {
  if (!myHistory.value.length) return 'Sin registro'
  const types = myHistory.value.map(l => l.event_type)
  if (types.includes('exit')) return 'Jornada completada'
  if (onBreak.value) return 'En break'
  if (types.includes('entry')) return 'Trabajando'
  return 'Sin registro'
})

const statusDotColor = computed(() => {
  const s = currentStatusLabel.value
  if (s === 'Trabajando') return 'bg-emerald-500'
  if (s === 'En break') return 'bg-amber-500'
  if (s === 'Jornada completada') return 'bg-blue-500'
  return 'bg-gray-300 dark:bg-zinc-600'
})

// ── Hero badge classes ──
const heroBadgeClass = computed(() => {
  const s = currentStatusLabel.value
  if (s === 'Trabajando') return 'bg-emerald-50 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-400 border-emerald-100 dark:border-emerald-800'
  if (s === 'En break') return 'bg-amber-50 dark:bg-amber-950 text-amber-700 dark:text-amber-400 border-amber-100 dark:border-amber-800'
  if (s === 'Jornada completada') return 'bg-blue-50 dark:bg-blue-950 text-blue-700 dark:text-blue-400 border-blue-100 dark:border-blue-800'
  return 'bg-gray-100 dark:bg-zinc-800 text-gray-600 dark:text-zinc-400 border-gray-200 dark:border-zinc-700'
})

const heroBadgeDot = computed(() => {
  const s = currentStatusLabel.value
  if (s === 'Trabajando') return 'bg-emerald-500 animate-pulse'
  if (s === 'En break') return 'bg-amber-500 animate-pulse'
  if (s === 'Jornada completada') return 'bg-blue-500'
  return 'bg-gray-400 dark:bg-zinc-500'
})

// ── KPI displays ──
const entryTimeDisplay = computed(() => {
  const entry = sortedHistory.value.find(l => l.event_type === 'entry')
  return entry ? formatTime(entry.event_at) : '—'
})

const exitTimeDisplay = computed(() => {
  const exit = sortedHistory.value.find(l => l.event_type === 'exit')
  return exit ? formatTime(exit.event_at) : '—'
})

const breaksCount = computed(() => myHistory.value.filter(l => l.event_type === 'break_start').length)

// ── User initials ──
const userInitials = computed(() => {
  const name = currentUser.value?.name || ''
  return name.split(' ').slice(0, 2).map(w => w[0]).join('').toUpperCase()
})

const enrollBadgeClass = 'bg-emerald-50 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-400 border-emerald-100 dark:border-emerald-800'

// ── Timeline dot classes ──
const timelineDotClass = (type) => ({
  entry: 'border-emerald-500 bg-emerald-50 dark:bg-emerald-950 text-emerald-600 dark:text-emerald-400',
  exit: 'border-blue-500 bg-blue-50 dark:bg-blue-950 text-blue-600 dark:text-blue-400',
  break_start: 'border-amber-500 bg-amber-50 dark:bg-amber-950 text-amber-600 dark:text-amber-400',
  break_end: 'border-indigo-500 bg-indigo-50 dark:bg-indigo-950 text-indigo-600 dark:text-indigo-400',
})[type] || 'border-gray-400 bg-gray-50 dark:bg-zinc-800 text-gray-500'

const workedTime = computed(() => {
  const logs = sortedHistory.value
  let totalMs = 0, entryTime = null, breakStart = null
  for (const log of logs) {
    const t = new Date(log.event_at).getTime()
    if (log.event_type === 'entry') entryTime = t
    else if (log.event_type === 'break_start') breakStart = t
    else if (log.event_type === 'break_end' && breakStart) { totalMs -= (t - breakStart); breakStart = null }
    else if (log.event_type === 'exit' && entryTime) { totalMs += (t - entryTime); entryTime = null }
  }
  if (entryTime) totalMs += (Date.now() - entryTime)
  if (breakStart) totalMs -= (Date.now() - breakStart)
  if (totalMs <= 0) return '0h 0m'
  return `${Math.floor(totalMs / 3600000)}h ${Math.floor((totalMs % 3600000) / 60000)}m`
})

const lastAction = computed(() => {
  if (!myHistory.value.length) return null
  return [...myHistory.value].sort((a, b) => new Date(b.event_at) - new Date(a.event_at))[0]
})

// ── Modal precision computeds ──
const precision = computed(() => {
  if (verificationDistance.value == null) return 0
  return Math.round((1 - verificationDistance.value) * 100)
})

const precisionColor = computed(() => {
  const p = precision.value
  if (p >= 80) return 'text-emerald-600 dark:text-emerald-400'
  if (p >= 65) return 'text-amber-600 dark:text-amber-400'
  return 'text-red-600 dark:text-red-400'
})

const statusLabel = computed(() => {
  const p = precision.value
  if (p >= 80) return 'Confirmado'
  if (p >= 65) return 'Dudoso'
  return 'Error'
})

const statusBadgeClass = computed(() => {
  const p = precision.value
  if (p >= 80) return 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-400'
  if (p >= 65) return 'bg-amber-50 text-amber-700 dark:bg-amber-950 dark:text-amber-400'
  return 'bg-red-50 text-red-700 dark:bg-red-950 dark:text-red-400'
})

// ── History helpers ──
const eventDotColor = (type) => ({
  entry: 'bg-emerald-500', exit: 'bg-blue-500', break_start: 'bg-amber-500', break_end: 'bg-indigo-500',
})[type] || 'bg-gray-400'

const scorePercent = (s) => s == null ? 0 : Math.round((1 - s) * 100)
const scoreBadgeClass = (s) => {
  if (s == null) return 'bg-gray-100 text-gray-500 dark:bg-zinc-800 dark:text-zinc-400'
  if (s < 0.2) return 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-400'
  if (s < 0.35) return 'bg-amber-50 text-amber-700 dark:bg-amber-950 dark:text-amber-400'
  return 'bg-red-50 text-red-700 dark:bg-red-950 dark:text-red-400'
}

const formatTime = (d) => d ? new Date(d).toLocaleTimeString('es-ES', { hour: '2-digit', minute: '2-digit', hour12: true }).toUpperCase() : ''

// ── API ──
const checkEnrollment = async () => {
  if (!userId.value) return
  try {
    const res = await biometricService.checkEnrollment(userId.value)
    isEnrolled.value = res.enrolled === true
    if (isEnrolled.value) {
      const d = await biometricService.getDescriptor(userId.value)
      if (d.success && d.data?.descriptors) myDescriptor.value = d.data.descriptors
    }
  } catch { isEnrolled.value = false }
}

const refreshHistory = async () => {
  if (!userId.value) return
  try {
    const res = await biometricService.getAttendanceHistory({ user_id: userId.value })
    if (res.success) myHistory.value = res.data || []
  } catch { /* silent */ }
}

// ── Cash validation before exit ──
const handleCtaClick = async (action) => {
  if (action === 'exit') {
    checkingCash.value = true
    try {
      const res = await biometricService.checkCashBeforeExit(userId.value)
      if (res.success && res.has_open_cash) {
        cashBlockWarehouse.value = res.warehouse_name || 'desconocida'
        cashBlockOpenedAt.value = res.opened_at || ''
        showCashBlockModal.value = true
        return
      }
    } catch {
      // If check fails, allow exit attempt (backend will validate again)
    } finally {
      checkingCash.value = false
    }
  }
  openModal(action)
}

const cashOpenedAtFormatted = computed(() => {
  if (!cashBlockOpenedAt.value) return '—'
  const d = new Date(cashBlockOpenedAt.value)
  return d.toLocaleTimeString('es-CO', { hour: '2-digit', minute: '2-digit', hour12: true }).toUpperCase()
})

const goToPos = () => {
  showCashBlockModal.value = false
  router.push({ name: 'POS' })
}

const proceedExitAnyway = () => {
  showCashBlockModal.value = false
  openModal('exit')
}

// ── Modal open/close ──
const openModal = async (action) => {
  if (!myDescriptor.value) return
  pendingAction.value = action
  showModal.value = true
  verified.value = false
  verificationDistance.value = null
  showScanHint.value = false
  await nextTick()
  await initCamera()
}

const closeModal = () => {
  stopScanning()
  stopCamera()
  showModal.value = false
  verified.value = false
  verificationDistance.value = null
  pendingAction.value = null
}

const initCamera = async () => {
  const loaded = await loadModels()
  if (!loaded || !showModal.value) return
  await nextTick()
  if (!videoRef.value) return
  const started = await startCamera(videoRef.value)
  if (!started || !videoRef.value) return
  const begin = () => {
    if (!videoRef.value) return
    // Set up face guide canvas (green box on face)
    if (overlayRef.value && videoRef.value) {
      overlayRef.value.width = videoRef.value.videoWidth
      overlayRef.value.height = videoRef.value.videoHeight
    }
    startFaceGuide(videoRef.value, overlayRef.value, 250)
    startScanning()
  }
  if (videoRef.value.readyState >= 1) begin()
  else videoRef.value.addEventListener('loadedmetadata', begin, { once: true })
}

// ── Scanning ──
const startScanning = () => {
  if (scanInterval || !myDescriptor.value) return
  scanning.value = true
  consecutiveMatches = 0
  showScanHint.value = false
  if (scanHintTimer) clearTimeout(scanHintTimer)
  scanHintTimer = setTimeout(() => { showScanHint.value = true }, 12000)

  scanInterval = setInterval(async () => {
    if (verified.value || recording.value || !isCameraActive.value) return
    // Only attempt expensive comparison when face is detected by the guide
    if (!faceDetected.value) { consecutiveMatches = 0; return }
    try {
      const result = await compareFace(videoRef.value, myDescriptor.value)
      if (result && result.match) {
        consecutiveMatches++
        if (consecutiveMatches >= STABLE_MATCHES) {
          verified.value = true
          verificationDistance.value = result.distance
          stopScanning()
        }
      } else {
        consecutiveMatches = 0
      }
    } catch { /* retry */ }
  }, 350)
}

const stopScanning = () => {
  if (scanInterval) { clearInterval(scanInterval); scanInterval = null }
  if (scanHintTimer) { clearTimeout(scanHintTimer); scanHintTimer = null }
  scanning.value = false
  showScanHint.value = false
}

// ── Confirm ──
const confirmRegistration = async () => {
  if (!verified.value || !pendingAction.value || recording.value) return
  recording.value = true
  try {
    const res = await biometricService.recordAttendance(userId.value, pendingAction.value, verificationDistance.value)
    showToast(res.message || 'Registro exitoso', 'success')
    closeModal()
    await refreshHistory()
  } catch (err) {
    showToast(err.response?.data?.message || 'Error al registrar', 'error')
  } finally {
    recording.value = false
  }
}

// ── Lifecycle ──
onMounted(async () => {
  clockInterval = setInterval(() => { now.value = new Date() }, 1000)
  // Refresh user data from /me to ensure fields like cc are up to date
  try {
    const res = await authService.getCurrentUser()
    if (res?.data?.user) localStorage.setItem('user', JSON.stringify(res.data.user))
  } catch { /* keep cached user */ }
  await Promise.all([checkEnrollment(), refreshHistory()])
  ready.value = true
})

onBeforeUnmount(() => {
  if (clockInterval) clearInterval(clockInterval)
  stopScanning()
  stopCamera()
  if (toastTimer) clearTimeout(toastTimer)
})
</script>

<style scoped>
.modal-enter-active { transition: all 0.2s ease-out; }
.modal-leave-active { transition: all 0.15s ease-in; }
.modal-enter-from { opacity: 0; }
.modal-enter-from > div:last-child { transform: scale(0.97) translateY(8px); }
.modal-leave-to { opacity: 0; }
</style>
