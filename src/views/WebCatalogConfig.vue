<template>
  <div class="h-full overflow-hidden">

  <!-- ═══════════════════════════════════════════════════════════════════ -->
  <!-- ESTADO CARGANDO: mismo splash que la app                          -->
  <!-- ═══════════════════════════════════════════════════════════════════ -->
  <div v-if="catalogUiMode === 'loading'" class="h-full bg-gradient-to-b from-gray-50 via-gray-100 to-gray-200 dark:from-[#141417] dark:via-slate-900 dark:to-[#0a0a0c] flex items-center justify-center">
    <div class="flex flex-col items-center justify-center space-y-6">
      <img src="/logo.png" alt="105 POS" class="w-20 h-20 object-contain">
      <h1 class="text-3xl font-bold text-slate-800 dark:text-white tracking-tight">105 POS</h1>
      <svg class="w-8 h-8 text-slate-400 dark:text-slate-500 animate-spin" fill="none" viewBox="0 0 24 24">
        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3"/>
        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
      </svg>
    </div>
  </div>

  <!-- ═══════════════════════════════════════════════════════════════════ -->
  <!-- ONBOARDING: PANTALLA DE BIENVENIDA (primera vez, sin identidad)   -->
  <!-- ═══════════════════════════════════════════════════════════════════ -->
  <Transition
    enter-active-class="transition-all duration-700 ease-out"
    enter-from-class="opacity-0"
    enter-to-class="opacity-100"
    leave-active-class="transition-all duration-500 ease-in"
    leave-from-class="opacity-100"
    leave-to-class="opacity-0"
  >
  <div v-if="catalogUiMode === 'welcome'" class="h-full flex items-center justify-center bg-white dark:bg-[#0a0a0c] relative overflow-hidden">
    <!-- Fondo: gradiente muy suave, casi imperceptible -->
    <div class="absolute inset-0 pointer-events-none">
      <div class="absolute inset-0 bg-gradient-to-br from-slate-50 via-white to-indigo-50/40 dark:from-[#0a0a0c] dark:via-[#0f0f12] dark:to-[#0a0a0c]"></div>
    </div>

    <div class="relative z-10 max-w-3xl mx-auto px-8 text-center">

      <!-- Icono desnudo — sin fondo, sin sombra de color -->
      <div class="inline-flex items-center justify-center mb-10">
        <svg class="w-20 h-20 text-gray-800 dark:text-zinc-200" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.0">
          <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 21v-7.5a.75.75 0 01.75-.75h3a.75.75 0 01.75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349m-16.5 11.65V9.35m0 0a3.001 3.001 0 003.75-.615A2.993 2.993 0 009.75 9.75c.896 0 1.7-.393 2.25-1.016a2.993 2.993 0 002.25 1.016c.896 0 1.7-.393 2.25-1.016a3.001 3.001 0 003.75.614m-16.5 0a3.004 3.004 0 01-.621-4.72L4.318 3.44A1.5 1.5 0 015.378 3h13.243a1.5 1.5 0 011.06.44l1.19 1.189a3 3 0 01-.621 4.72m-13.5 8.65h3.75a.375.375 0 00.375-.375v-1.5a.375.375 0 00-.375-.375h-3.75a.375.375 0 00-.375.375v1.5c0 .207.168.375.375.375z" />
        </svg>
      </div>

      <!-- Título: sólido, tracking apretado, sin gradiente -->
      <h1 class="text-[3.8rem] font-bold tracking-tight text-gray-900 dark:text-white leading-[1.05] mb-6">
        Tu tienda online,<br>
        <span class="text-[#1a73e8]">lista en minutos</span>
      </h1>
      <p class="text-[1.15rem] text-gray-500 dark:text-zinc-400 mb-14 leading-relaxed max-w-xl mx-auto">
        Describe tu negocio con tus propias palabras y nuestra IA construirá una identidad visual completa para tu catálogo web — colores, tipografías, textos y más.
      </p>

      <!-- Feature icons: desnudos, sin contenedor -->
      <div class="flex items-start justify-center gap-16 mb-14">
        <div v-for="feat in [
          { icon: 'M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09zM18.259 8.715L18 9.75l-.259-1.035a3.375 3.375 0 00-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 002.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 002.455 2.456L21.75 6l-1.036.259a3.375 3.375 0 00-2.455 2.456z', label: 'IA Generativa' },
          { icon: 'M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z', label: 'Menos de 30 seg' },
          { icon: 'M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z M15 12a3 3 0 11-6 0 3 3 0 016 0z', label: 'Vista previa en vivo' }
        ]" :key="feat.label" class="flex flex-col items-center gap-3">
          <svg class="w-7 h-7 text-gray-700 dark:text-zinc-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.2">
            <path stroke-linecap="round" stroke-linejoin="round" :d="feat.icon" />
          </svg>
          <span class="text-[13px] font-medium text-gray-500 dark:text-zinc-400 tracking-wide">{{ feat.label }}</span>
        </div>
      </div>

      <!-- CTA: sólido, sin gradiente, con sombra limpia -->
      <button
        @click="catalogUiMode = 'brief-only'"
        class="inline-flex items-center gap-3 px-10 py-[18px] bg-[#1a73e8] hover:bg-[#1557b0] text-white text-[17px] font-semibold rounded-2xl shadow-xl shadow-blue-500/25 transition-all duration-200 hover:shadow-blue-500/35 hover:-translate-y-px"
      >
        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
          <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09zM18.259 8.715L18 9.75l-.259-1.035a3.375 3.375 0 00-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 002.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 002.455 2.456L21.75 6l-1.036.259a3.375 3.375 0 00-2.455 2.456z" />
        </svg>
        Crear mi tienda web ahora
      </button>

      <p class="mt-5 text-[13px] text-gray-400 dark:text-zinc-600 tracking-wide">Gratis · Sin necesidad de diseñador · Todo con IA</p>
    </div>
  </div>
  </Transition>

  <!-- ═══════════════════════════════════════════════════════════════════ -->
  <!-- ONBOARDING: SOLO CHAT (usuario escribe su descripción)            -->
  <!-- ═══════════════════════════════════════════════════════════════════ -->
  <Transition
    enter-active-class="transition-all duration-600 ease-out"
    enter-from-class="opacity-0 translate-y-4"
    enter-to-class="opacity-100 translate-y-0"
    leave-active-class="transition-all duration-400 ease-in"
    leave-from-class="opacity-100 translate-y-0"
    leave-to-class="opacity-0 translate-y-4"
  >
  <div v-if="catalogUiMode === 'brief-only'" class="h-full flex flex-col items-center justify-center bg-white dark:bg-[#0a0a0c] px-6 relative overflow-hidden">
    <!-- Fondo mínimo -->
    <div class="absolute inset-0 pointer-events-none bg-gradient-to-br from-slate-50 via-white to-indigo-50/30 dark:from-[#0a0a0c] dark:to-[#0f0f12]"></div>

    <div class="relative z-10 w-full max-w-3xl">
      <!-- Header -->
      <div class="text-center mb-10">
        <!-- Badge discreta -->
        <div class="inline-flex items-center gap-1.5 px-4 py-1.5 bg-gray-100 dark:bg-zinc-800 rounded-full mb-6">
          <span class="w-1.5 h-1.5 rounded-full bg-gray-400 dark:bg-zinc-500"></span>
          <span class="text-[11px] text-gray-600 dark:text-zinc-400 uppercase tracking-widest font-semibold">IA lista para empezar</span>
        </div>
        <h2 class="text-[2.8rem] font-bold tracking-tight text-gray-900 dark:text-white mb-3">Cuéntame sobre tu negocio</h2>
        <p class="text-[16px] text-gray-500 dark:text-zinc-400 leading-relaxed">Describe tu marca: estilo, público objetivo, colores que te gustan, qué vendes...</p>
      </div>

      <!-- Tarjeta del textarea: blanco puro, sombra editorial -->
      <div class="bg-white dark:bg-[#111113] rounded-2xl border border-gray-200 dark:border-zinc-800 shadow-[0_12px_40px_rgb(0,0,0,0.06)] dark:shadow-[0_12px_40px_rgb(0,0,0,0.4)] overflow-hidden">
        <textarea
          v-model="aiBrandDescription"
          rows="10"
          placeholder="Ejemplo: Tengo una tienda de ropa femenina, estilo romántico y boho. Mi clienta tiene entre 25 y 40 años. Me gustan los colores tierra, beige y rosado polvoso. Quiero que mi tienda se vea elegante pero accesible..."
          class="w-full px-8 pt-8 pb-4 bg-transparent text-[16px] text-gray-900 dark:text-zinc-100 placeholder-gray-300 dark:placeholder-zinc-600 resize-none leading-relaxed focus:outline-none focus:ring-0"
          @keydown.ctrl.enter="generateAiBrand"
        ></textarea>
        <!-- Footer del card -->
        <div class="px-8 pb-6 flex items-center justify-between border-t border-gray-100 dark:border-zinc-800 pt-5">
          <span class="text-[13px] text-gray-400 dark:text-zinc-600">{{ aiBrandDescription.length }} caracteres · Ctrl+Enter para generar</span>
          <div class="flex items-center gap-2">
            <!-- Voz -->
            <button
              @click="toggleVoiceRecording"
              :title="isRecordingVoice ? 'Detener grabación' : 'Dictar con voz'"
              class="w-11 h-11 rounded-xl flex items-center justify-center transition-all duration-150"
              :class="isRecordingVoice
                ? 'bg-red-500 text-white'
                : 'bg-gray-50 dark:bg-zinc-800 text-gray-400 dark:text-zinc-500 hover:text-gray-700 dark:hover:text-zinc-300 border border-gray-200 dark:border-zinc-700'"
            >
              <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 18.75a6 6 0 006-6v-1.5m-6 7.5a6 6 0 01-6-6v-1.5m6 7.5v3.75m-3.75 0h7.5M12 15.75a3 3 0 01-3-3V4.5a3 3 0 116 0v8.25a3 3 0 01-3 3z" />
              </svg>
            </button>
            <!-- Generar -->
            <button
              @click="generateAiBrand"
              :disabled="!aiBrandDescription || aiBrandDescription.trim().length < 10 || isGeneratingBrand"
              class="inline-flex items-center gap-2.5 px-7 py-3 bg-[#1a73e8] hover:bg-[#1557b0] text-white text-[15px] font-semibold rounded-xl shadow-md shadow-blue-500/20 transition-all duration-150 disabled:opacity-35 disabled:cursor-not-allowed disabled:shadow-none"
            >
              <svg v-if="!isGeneratingBrand" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09zM18.259 8.715L18 9.75l-.259-1.035a3.375 3.375 0 00-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 002.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 002.455 2.456L21.75 6l-1.036.259a3.375 3.375 0 00-2.455 2.456z" />
              </svg>
              <svg v-else class="animate-spin w-3.5 h-3.5" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
              </svg>
              {{ isGeneratingBrand ? aiGenerationProgress || 'Generando...' : 'Generar identidad' }}
            </button>
          </div>
        </div>
      </div>

      <!-- Progress bar mientras genera -->
      <div v-if="isGeneratingBrand" class="mt-5">
        <div class="w-full bg-gray-100 dark:bg-zinc-800 rounded-full h-px overflow-hidden mb-2">
          <div class="h-full bg-gray-800 dark:bg-zinc-300 rounded-full animate-[loading_2.5s_ease-in-out_infinite]" style="width: 70%"></div>
        </div>
        <p class="text-center text-[11px] text-gray-400 dark:text-zinc-600">{{ aiGenerationProgress }}</p>
      </div>

      <!-- Volver -->
      <div class="text-center mt-7">
        <button @click="catalogUiMode = 'welcome'" class="text-[11px] text-gray-400 dark:text-zinc-600 hover:text-gray-700 dark:hover:text-zinc-300 transition-colors tracking-wide">
          ← Volver al inicio
        </button>
      </div>
    </div>
  </div>
  </Transition>

  <!-- ═══════════════════════════════════════════════════════════════════ -->
  <!-- CHOOSING: Carrusel de 5 diseños generados por IA                 -->
  <!-- ═══════════════════════════════════════════════════════════════════ -->
  <div v-if="catalogUiMode === 'choosing'" class="h-full flex flex-col items-center justify-center bg-white dark:bg-[#0a0a0c] px-4 relative overflow-hidden">
    <!-- Fondo mínimo -->
    <div class="absolute inset-0 pointer-events-none bg-gradient-to-br from-slate-50 via-white to-indigo-50/20 dark:from-[#0a0a0c] dark:to-[#0f0f12]"></div>

    <!-- Header -->
    <div class="relative z-10 text-center mb-8 flex-shrink-0">
      <div v-if="designsReady < 5" class="inline-flex items-center gap-2 px-4 py-1.5 bg-gray-100 dark:bg-zinc-800 rounded-full mb-5">
        <svg class="animate-spin w-3 h-3 text-gray-500 dark:text-zinc-400" fill="none" viewBox="0 0 24 24">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
        </svg>
        <span class="text-[11px] text-gray-600 dark:text-zinc-400 uppercase tracking-widest font-semibold">Generando · {{ designsReady }}/5 listos</span>
      </div>
      <div v-else class="inline-flex items-center gap-2 px-4 py-1.5 bg-gray-100 dark:bg-zinc-800 rounded-full mb-5">
        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
        <span class="text-[11px] text-gray-600 dark:text-zinc-400 uppercase tracking-widest font-semibold">5 diseños únicos listos</span>
      </div>
      <h2 class="text-[2.4rem] font-bold tracking-tight text-gray-900 dark:text-white mb-2">Elige tu diseño favorito</h2>
      <p class="text-[15px] text-gray-500 dark:text-zinc-400">La IA creó 5 identidades únicas para tu marca.</p>
    </div>

    <!-- Área principal: flechas + teléfono + moodboard -->
    <div class="relative z-10 flex items-center gap-8 flex-shrink-0">

      <!-- Flecha Anterior -->
      <button
        @click="currentDesignIdx = (currentDesignIdx - 1 + generatedDesigns.length) % generatedDesigns.length"
        :disabled="generatedDesigns.length < 2"
        class="w-12 h-12 rounded-full bg-white dark:bg-zinc-900 border border-gray-300 dark:border-zinc-700 shadow-md flex items-center justify-center text-gray-600 dark:text-zinc-300 hover:text-gray-900 dark:hover:text-white hover:bg-gray-50 dark:hover:bg-zinc-800 hover:border-gray-400 dark:hover:border-zinc-600 hover:shadow-lg transition-all disabled:opacity-20 flex-shrink-0"
      >
        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
        </svg>
      </button>

      <!-- TELÉFONO hiperrealista (375×740 escalado a 0.95 → 356×703) -->
      <div style="width: 356px; height: 703px; position: relative; flex-shrink: 0;">
        <div style="position: absolute; top: 0; left: 0; width: 375px; height: 740px; transform: scale(0.95); transform-origin: top left;">
          <!-- Marco negro real del teléfono -->
          <div class="absolute inset-0 rounded-[3rem] border-[12px] border-black shadow-2xl pointer-events-none z-50">
            <div class="absolute top-0 left-1/2 -translate-x-1/2 w-36 h-6 bg-black rounded-b-2xl -mt-[12px]"></div>
            <div class="absolute -left-[12px] top-24 w-1 h-10 bg-black rounded-l"></div>
            <div class="absolute -left-[12px] top-36 w-1 h-14 bg-black rounded-l"></div>
            <div class="absolute -right-[12px] top-28 w-1 h-16 bg-black rounded-r"></div>
          </div>
          <!-- Pantalla -->
          <div class="w-full h-full overflow-hidden bg-white relative rounded-[2.4rem]" style="isolation: isolate; transform: translateZ(0);">
            <div v-if="isChoosingIframeLoading" class="absolute inset-0 flex flex-col items-center justify-center gap-3 bg-white z-10">
              <div class="w-5 h-5 border border-gray-200 border-t-gray-500 rounded-full animate-spin"></div>
              <p class="text-[10px] text-gray-400 tracking-wide">Cargando diseño...</p>
            </div>
            <iframe
              v-if="generatedDesigns[currentDesignIdx]"
              ref="choosingIframeEl"
              :key="choosingIframeKey"
              :src="`${catalogUrl}?ai_preview=1`"
              class="w-full h-full border-0"
              style="width: 375px; height: 740px;"
              title="Vista Previa del Diseño"
              @load="isChoosingIframeLoading = false"
            ></iframe>
            <div v-else class="w-full h-full flex flex-col items-center justify-center gap-3 bg-gray-50 dark:bg-zinc-900">
              <svg class="animate-spin w-6 h-6 text-gray-300" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
              </svg>
              <p class="text-[10px] text-gray-400">Diseño {{ currentDesignIdx + 1 }} llegando...</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Moodboard / Especificaciones — estilo Manual de Marca -->
      <div v-if="generatedDesigns[currentDesignIdx]" class="w-56 space-y-3 flex-shrink-0">

        <!-- Tipografías -->
        <div class="bg-white dark:bg-zinc-900 rounded-xl border border-gray-100 dark:border-zinc-800 p-4">
          <p class="text-[10px] text-gray-400 dark:text-zinc-500 uppercase tracking-widest font-semibold mb-3">Tipografías</p>
          <p class="text-[15px] font-semibold text-gray-900 dark:text-white truncate"
            :style="{ fontFamily: generatedDesigns[currentDesignIdx].fonts?.heading + ', serif' }"
          >{{ generatedDesigns[currentDesignIdx].fonts?.heading }}</p>
          <p class="text-[12px] text-gray-400 dark:text-zinc-500 truncate mt-1"
            :style="{ fontFamily: generatedDesigns[currentDesignIdx].fonts?.body + ', sans-serif' }"
          >{{ generatedDesigns[currentDesignIdx].fonts?.body }}</p>
        </div>

        <!-- Composición -->
        <div class="bg-white dark:bg-zinc-900 rounded-xl border border-gray-100 dark:border-zinc-800 p-4 space-y-2">
          <p class="text-[10px] text-gray-400 dark:text-zinc-500 uppercase tracking-widest font-semibold mb-3">Composición</p>
          <div v-for="(val, key) in { Header: generatedDesigns[currentDesignIdx].layout_config?.header_style, Hero: generatedDesigns[currentDesignIdx].layout_config?.hero_style, Hook: generatedDesigns[currentDesignIdx].layout_config?.hook_style, Trust: generatedDesigns[currentDesignIdx].layout_config?.trust_strip_style }" :key="key" class="flex items-center gap-2">
            <span class="text-[10px] text-gray-400 dark:text-zinc-500 w-10 flex-shrink-0">{{ key }}</span>
            <span class="text-[10px] bg-gray-50 dark:bg-zinc-800 text-gray-600 dark:text-zinc-300 border border-gray-200 dark:border-zinc-700 px-2 py-0.5 rounded truncate">{{ val || '—' }}</span>
          </div>
        </div>

        <!-- Paleta -->
        <div class="bg-white dark:bg-zinc-900 rounded-xl border border-gray-100 dark:border-zinc-800 p-4">
          <p class="text-[10px] text-gray-400 dark:text-zinc-500 uppercase tracking-widest font-semibold mb-3">Paleta</p>
          <div class="flex gap-2 flex-wrap">
            <div
              v-for="(clr, key) in generatedDesigns[currentDesignIdx].color_palette"
              :key="key"
              class="w-8 h-8 rounded-lg shadow-sm ring-1 ring-gray-200/60 dark:ring-zinc-700/40"
              :style="{ backgroundColor: clr }"
              :title="key + ': ' + clr"
            ></div>
          </div>
        </div>
      </div>

      <!-- Flecha Siguiente -->
      <button
        @click="currentDesignIdx = (currentDesignIdx + 1) % generatedDesigns.length"
        :disabled="generatedDesigns.length < 2"
        class="w-12 h-12 rounded-full bg-white dark:bg-zinc-900 border border-gray-300 dark:border-zinc-700 shadow-md flex items-center justify-center text-gray-600 dark:text-zinc-300 hover:text-gray-900 dark:hover:text-white hover:bg-gray-50 dark:hover:bg-zinc-800 hover:border-gray-400 dark:hover:border-zinc-600 hover:shadow-lg transition-all disabled:opacity-20 flex-shrink-0"
      >
        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
        </svg>
      </button>
    </div>

    <!-- Dots + contador -->
    <div class="relative z-10 flex flex-col items-center gap-2.5 mt-6 flex-shrink-0">
      <div class="flex items-center gap-1.5">
        <button
          v-for="(design, i) in generatedDesigns"
          :key="i"
          @click="currentDesignIdx = i"
          class="transition-all duration-200 rounded-full"
          :class="i === currentDesignIdx ? 'w-5 h-1.5 bg-gray-800 dark:bg-zinc-200' : 'w-1.5 h-1.5 bg-gray-200 dark:bg-zinc-700 hover:bg-gray-400 dark:hover:bg-zinc-500'"
        ></button>
        <button
          v-for="j in (5 - generatedDesigns.length)"
          :key="'ph-' + j"
          disabled
          class="w-1.5 h-1.5 rounded-full bg-gray-100 dark:bg-zinc-800 cursor-default"
        ></button>
      </div>
      <p class="text-[10px] text-gray-400 dark:text-zinc-600 tracking-wide">{{ currentDesignIdx + 1 }} / {{ generatedDesigns.length }}</p>
    </div>

    <!-- Botones de acción -->
    <div class="relative z-10 flex flex-col items-center gap-3 mt-6 mb-8 flex-shrink-0 w-full max-w-sm">
      <button
        @click="chooseDesign(currentDesignIdx)"
        :disabled="!generatedDesigns[currentDesignIdx] || isChoosingApplying"
        class="w-full py-4 bg-gray-900 dark:bg-white hover:bg-black dark:hover:bg-gray-100 text-white dark:text-gray-900 font-semibold text-[15px] rounded-xl shadow-md shadow-black/10 transition-all duration-150 disabled:opacity-30 disabled:cursor-not-allowed flex items-center justify-center gap-2.5"
      >
        <svg v-if="!isChoosingApplying" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
          <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/>
        </svg>
        <svg v-else class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
        </svg>
        {{ isChoosingApplying ? 'Aplicando...' : 'Elegir este diseño' }}
      </button>
      <button
        @click="() => { try { localStorage.removeItem('ai_design_preview') } catch(e) {}; catalogUiMode = 'brief-only' }"
        class="text-[13px] text-gray-400 dark:text-zinc-600 hover:text-gray-700 dark:hover:text-zinc-300 transition-colors tracking-wide"
      >
        ← Cambiar descripción y regenerar
      </button>
    </div>
  </div>

  <!-- ═══════════════════════════════════════════════════════════════════ -->
  <!-- UPLOAD IMAGES: Paso de subida de imágenes del hook elegido        -->
  <!-- ═══════════════════════════════════════════════════════════════════ -->
  <div v-if="catalogUiMode === 'upload-images'" class="h-full flex flex-col bg-white dark:bg-[#0a0a0c] overflow-hidden">

    <!-- ── HEADER ──────────────────────────────────────────────────────── -->
    <div class="flex-shrink-0 px-8 py-4 flex items-center justify-between border-b border-gray-200 dark:border-zinc-800">
      <button
        @click="catalogUiMode = 'choosing'"
        class="flex items-center gap-2 text-sm font-medium text-gray-700 dark:text-zinc-300 hover:text-black dark:hover:text-white hover:underline transition-colors"
      >
        <svg class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
          <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
        </svg>
        Cambiar diseño
      </button>

      <!-- Progress pill: sección X de 3 -->
      <div class="flex items-center gap-3">
        <div class="flex items-center gap-1.5">
          <div v-for="s in 3" :key="s" class="rounded-full transition-all duration-300 cursor-pointer"
            :class="s <= uploadSubStep ? 'w-8 h-[3px] bg-gray-900 dark:bg-white' : 'w-5 h-[3px] bg-gray-200 dark:bg-zinc-700'"
            @click="uploadSubStep = s">
          </div>
        </div>
        <span class="text-xs font-medium text-gray-500 dark:text-zinc-400">Fotos {{ uploadSubStep }} de 3</span>
      </div>

      <button
        @click="saveAndExitOnboarding"
        :disabled="isOnboardingImagesSaving"
        class="text-sm font-medium text-gray-700 dark:text-zinc-300 hover:text-black dark:hover:text-white hover:underline transition-colors disabled:opacity-40"
      >
        Guardar y retomar después
      </button>
    </div>

    <!-- ── Breadcrumb de pasos ─────────────────────────────────────────── -->
    <div class="flex-shrink-0 px-8 py-2.5 flex items-center gap-2 bg-gray-50/70 dark:bg-zinc-900/50 border-b border-gray-100 dark:border-zinc-900">
      <button @click="uploadSubStep = 1"
        class="flex items-center gap-1.5 text-xs font-medium transition-colors"
        :class="uploadSubStep === 1 ? 'text-gray-900 dark:text-white' : 'text-gray-400 dark:text-zinc-600 hover:text-gray-600 dark:hover:text-zinc-400'">
        <div class="w-4 h-4 rounded-full text-[10px] flex items-center justify-center border transition-colors"
          :class="uploadSubStep === 1 ? 'bg-gray-900 dark:bg-white border-gray-900 dark:border-white text-white dark:text-gray-900' : uploadSubStep > 1 ? 'bg-emerald-500 border-emerald-500 text-white' : 'border-gray-300 dark:border-zinc-600 text-gray-400'">
          <svg v-if="uploadSubStep > 1" class="w-2.5 h-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
          <span v-else>1</span>
        </div>
        Portada del banner
      </button>
      <svg class="w-3 h-3 text-gray-300 dark:text-zinc-700 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
      <button @click="uploadSubStep = 2"
        class="flex items-center gap-1.5 text-xs font-medium transition-colors"
        :class="uploadSubStep === 2 ? 'text-gray-900 dark:text-white' : 'text-gray-400 dark:text-zinc-600 hover:text-gray-600 dark:hover:text-zinc-400'">
        <div class="w-4 h-4 rounded-full text-[10px] flex items-center justify-center border transition-colors"
          :class="uploadSubStep === 2 ? 'bg-gray-900 dark:bg-white border-gray-900 dark:border-white text-white dark:text-gray-900' : uploadSubStep > 2 ? 'bg-emerald-500 border-emerald-500 text-white' : 'border-gray-300 dark:border-zinc-600 text-gray-400'">
          <svg v-if="uploadSubStep > 2" class="w-2.5 h-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
          <span v-else>2</span>
        </div>
        Bloque especial
      </button>
      <svg class="w-3 h-3 text-gray-300 dark:text-zinc-700 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
      <button @click="uploadSubStep = 3"
        class="flex items-center gap-1.5 text-xs font-medium transition-colors"
        :class="uploadSubStep === 3 ? 'text-gray-900 dark:text-white' : 'text-gray-400 dark:text-zinc-600 hover:text-gray-600 dark:hover:text-zinc-400'">
        <div class="w-4 h-4 rounded-full text-[10px] flex items-center justify-center border transition-colors"
          :class="uploadSubStep === 3 ? 'bg-gray-900 dark:bg-white border-gray-900 dark:border-white text-white dark:text-gray-900' : 'border-gray-300 dark:border-zinc-600 text-gray-400'">
          3
        </div>
        Historia visual
      </button>
      <!-- Nota de tranquilidad -->
      <span class="ml-auto text-[11px] text-gray-400 dark:text-zinc-600 italic">Todas las fotos son opcionales. Tu tienda funciona igual sin ellas.</span>
    </div>

    <!-- ── Contenido principal: panel izquierdo + preview ─────────────── -->
    <div class="flex-1 flex overflow-hidden">

      <!-- IZQUIERDA -->
      <div class="w-[450px] flex-shrink-0 flex flex-col px-8 py-7 overflow-y-auto border-r border-gray-200 dark:border-zinc-800">

        <!-- ══════════════════════════════════════════════════════════════ -->
        <!-- PASO 1: Fotos del banner / carrusel hero                     -->
        <!-- ══════════════════════════════════════════════════════════════ -->
        <template v-if="uploadSubStep === 1">
          <div class="mb-6">
            <div class="text-[10px] font-bold uppercase tracking-widest text-gray-400 dark:text-zinc-500 mb-2">Sección 1 de 3 · Portada del banner</div>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">Fotos del banner principal</h2>
            <p class="text-sm text-gray-600 dark:text-zinc-400 leading-relaxed">
              Estas fotos rotan como carrusel en la parte superior de tu tienda. Sube hasta 3 — la primera es la más importante. Si no tienes fotos ahora, puedes agregarlas después.
            </p>
          </div>

          <!-- 3 slots hero en columnas, formato retrato -->
          <div class="flex gap-2.5 mb-5" style="height: 280px;">
            <div
              v-for="i in 3" :key="'h'+i"
              @click="triggerOnboardingFile('hero_images', i-1)"
              @dragover.prevent
              @drop.prevent="e => { const f = e.dataTransfer.files[0]; if (f) { const ev = { target: { files: [f], value: '' } }; handleOnboardingFile(ev, 'hero_images', i-1) } }"
              class="group relative flex-1 rounded-lg border-2 border-dashed cursor-pointer transition-all duration-200 overflow-hidden"
              :class="onboardingImages.hero_images[i-1]
                ? 'border-gray-200 dark:border-zinc-700'
                : 'border-gray-300 dark:border-zinc-700 bg-gray-50 dark:bg-zinc-900/60 hover:bg-gray-100 dark:hover:bg-zinc-800/50 hover:border-gray-400 dark:hover:border-zinc-500'"
            >
              <template v-if="onboardingImages.hero_images[i-1]">
                <img :src="onboardingImages.hero_images[i-1]" class="w-full h-full object-cover" />
                <div class="absolute inset-0 bg-black/0 group-hover:bg-black/30 transition-all flex items-center justify-center">
                  <span class="opacity-0 group-hover:opacity-100 text-white text-[11px] font-medium bg-black/60 px-3 py-1.5 rounded-full transition-all">Cambiar</span>
                </div>
                <button @click.stop="onboardingImages.hero_images[i-1] = ''; updateOnboardingPreview()"
                  class="absolute top-2 right-2 w-6 h-6 bg-black/50 hover:bg-black/80 text-white rounded-full flex items-center justify-center transition-all shadow-sm">
                  <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
                <div class="absolute bottom-2 left-2 bg-black/50 text-white text-[10px] font-semibold px-2 py-0.5 rounded-full">{{ i === 1 ? 'Foto 1' : i === 2 ? 'Foto 2' : 'Foto 3' }}</div>
              </template>
              <template v-else>
                <div class="absolute inset-0 flex flex-col items-center justify-center gap-3 p-3">
                  <div class="w-10 h-10 rounded-full border-2 border-dashed border-gray-300 dark:border-zinc-600 group-hover:border-gray-500 flex items-center justify-center transition-colors flex-shrink-0">
                    <svg class="w-4 h-4 text-gray-500 dark:text-zinc-400 group-hover:text-gray-700 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                  </div>
                  <div class="text-center">
                    <p class="text-xs font-semibold text-gray-600 dark:text-zinc-400 group-hover:text-gray-800 transition-colors">{{ i === 1 ? 'Foto principal' : i === 2 ? 'Foto 2' : 'Foto 3' }}</p>
                    <p class="text-[10px] text-gray-400 dark:text-zinc-500 mt-0.5">JPG o PNG</p>
                  </div>
                </div>
              </template>
            </div>
          </div>
          <p class="text-xs text-gray-400 dark:text-zinc-500 mb-6">Arrastra o haz clic en cada celda · máx. 2MB por foto</p>
          <!-- Hidden inputs -->
          <input v-for="i in 3" :key="'ih'+i" :ref="el => { if (el) ob_hero[i-1] = el }" type="file" class="hidden" accept="image/*" @change="e => handleOnboardingFile(e, 'hero_images', i-1)" />

          <!-- CTAs -->
          <div class="mt-auto space-y-2.5">
            <button @click="uploadSubStep = 2; saveOnboardingDraft()"
              class="w-full py-4 bg-gray-900 dark:bg-white hover:bg-black dark:hover:bg-gray-100 text-white dark:text-gray-900 font-bold text-base rounded-xl shadow-md shadow-black/10 transition-all flex items-center justify-center gap-2.5">
              Siguiente: Bloque especial
              <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg>
            </button>
            <button @click="uploadSubStep = 2" class="w-full py-2 text-sm text-gray-400 dark:text-zinc-500 hover:text-gray-700 dark:hover:text-zinc-300 hover:underline transition-colors text-center">
              Omitir esta sección →
            </button>
          </div>
        </template>

        <!-- ══════════════════════════════════════════════════════════════ -->
        <!-- PASO 2: Fotos del hook (bloque especial)                     -->
        <!-- ══════════════════════════════════════════════════════════════ -->
        <template v-else-if="uploadSubStep === 2">
          <div class="mb-6">
            <div class="text-[10px] font-bold uppercase tracking-widest text-gray-400 dark:text-zinc-500 mb-2">Sección 2 de 3 · Bloque especial</div>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">Foto del bloque destacado</h2>
            <p class="text-sm text-gray-600 dark:text-zinc-400 leading-relaxed">
              Tu diseño incluye un bloque especial que aparece justo después del banner.
              <span v-if="onboardingHookStyle === 'editorial-story'"> Es una gran foto editorial que acompaña tu mensaje principal.</span>
              <span v-else-if="onboardingHookStyle === 'urban-lookbook'"> Es un carrusel de hasta 3 fotos en pantalla completa — perfecto para mostrar outfits o productos.</span>
              <span v-else-if="onboardingHookStyle === 'dynamic-bento'"> Es una cuadrícula de hasta 3 fotos en panel — destaca tus mejores productos o colecciones.</span>
            </p>
          </div>

          <!-- ── editorial-story: 1 zona retrato ── -->
          <template v-if="onboardingHookStyle === 'editorial-story'">
            <div
              @click="triggerOnboardingFile('editorial_image')"
              @dragover.prevent
              @drop.prevent="e => { const f = e.dataTransfer.files[0]; if (f) { const ev = { target: { files: [f], value: '' } }; handleOnboardingFile(ev, 'editorial_image') } }"
              class="group relative w-full rounded-lg border-2 border-dashed cursor-pointer transition-all duration-200 overflow-hidden mb-3"
              :class="onboardingImages.editorial_image ? 'border-gray-200 dark:border-zinc-700' : 'border-gray-300 dark:border-zinc-700 bg-gray-50 dark:bg-zinc-900/60 hover:bg-gray-100 dark:hover:bg-zinc-800/50 hover:border-gray-400 dark:hover:border-zinc-500'"
              style="aspect-ratio: 3/4;"
            >
              <template v-if="onboardingImages.editorial_image">
                <img :src="onboardingImages.editorial_image" class="w-full h-full object-cover" />
                <div class="absolute inset-0 bg-black/0 group-hover:bg-black/30 transition-all flex items-center justify-center">
                  <span class="opacity-0 group-hover:opacity-100 text-white text-sm font-medium bg-black/60 px-4 py-2 rounded-full transition-all">Cambiar foto</span>
                </div>
                <button @click.stop="onboardingImages.editorial_image = ''; updateOnboardingPreview()"
                  class="absolute top-3 right-3 w-8 h-8 bg-black/50 hover:bg-black/80 text-white rounded-full flex items-center justify-center transition-all shadow-md">
                  <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
              </template>
              <template v-else>
                <div class="absolute inset-0 flex flex-col items-center justify-center gap-4 px-6 text-center">
                  <div v-if="aiBrandData?.banner_texts?.headline" class="space-y-1">
                    <p class="text-sm font-semibold text-gray-400 dark:text-zinc-500 uppercase tracking-wider">{{ aiBrandData.banner_texts.headline }}</p>
                    <p v-if="aiBrandData?.banner_texts?.subheadline" class="text-xs text-gray-400 dark:text-zinc-500 uppercase tracking-widest">{{ aiBrandData.banner_texts.subheadline }}</p>
                  </div>
                  <div class="flex flex-col items-center gap-2">
                    <div class="w-12 h-12 rounded-full border-2 border-dashed border-gray-300 dark:border-zinc-600 group-hover:border-gray-500 flex items-center justify-center transition-colors">
                      <svg class="w-5 h-5 text-gray-500 dark:text-zinc-400 group-hover:text-gray-700 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                    </div>
                    <p class="text-sm font-semibold text-gray-700 dark:text-zinc-300 group-hover:text-gray-900 transition-colors">Subir foto aquí</p>
                    <p class="text-xs text-gray-400 dark:text-zinc-500">Formato retrato recomendado · JPG, PNG</p>
                  </div>
                </div>
              </template>
            </div>
            <input ref="ob_editorial" type="file" class="hidden" accept="image/*" @change="e => handleOnboardingFile(e, 'editorial_image')" />
          </template>

          <!-- ── urban-lookbook: 3 columnas ── -->
          <template v-else-if="onboardingHookStyle === 'urban-lookbook'">
            <div class="flex gap-2.5 mb-2" style="height: 280px;">
              <div
                v-for="i in 3" :key="'lk'+i"
                @click="triggerOnboardingFile('lookbook_images', i-1)"
                @dragover.prevent
                @drop.prevent="e => { const f = e.dataTransfer.files[0]; if (f) { const ev = { target: { files: [f], value: '' } }; handleOnboardingFile(ev, 'lookbook_images', i-1) } }"
                class="group relative flex-1 rounded-lg border-2 border-dashed cursor-pointer transition-all duration-200 overflow-hidden"
                :class="onboardingImages.lookbook_images[i-1] ? 'border-gray-200 dark:border-zinc-700' : 'border-gray-300 dark:border-zinc-700 bg-gray-50 dark:bg-zinc-900/60 hover:bg-gray-100 dark:hover:bg-zinc-800/50 hover:border-gray-400 dark:hover:border-zinc-500'"
              >
                <template v-if="onboardingImages.lookbook_images[i-1]">
                  <img :src="onboardingImages.lookbook_images[i-1]" class="w-full h-full object-cover" />
                  <div class="absolute inset-0 bg-black/0 group-hover:bg-black/30 transition-all flex items-center justify-center">
                    <span class="opacity-0 group-hover:opacity-100 text-white text-[11px] font-medium bg-black/60 px-3 py-1.5 rounded-full transition-all">Cambiar</span>
                  </div>
                  <button @click.stop="onboardingImages.lookbook_images[i-1] = ''; updateOnboardingPreview()"
                    class="absolute top-2 right-2 w-6 h-6 bg-black/50 hover:bg-black/80 text-white rounded-full flex items-center justify-center transition-all">
                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                  </button>
                  <div class="absolute bottom-2 left-2 bg-black/50 text-white text-[10px] font-semibold px-2 py-0.5 rounded-full">{{ i === 1 ? 'Slide 1' : i === 2 ? 'Slide 2' : 'Slide 3' }}</div>
                </template>
                <template v-else>
                  <div class="absolute inset-0 flex flex-col items-center justify-center gap-2 p-3">
                    <div class="w-9 h-9 rounded-full border-2 border-dashed border-gray-300 dark:border-zinc-600 group-hover:border-gray-500 flex items-center justify-center transition-colors flex-shrink-0">
                      <svg class="w-4 h-4 text-gray-500 dark:text-zinc-400 group-hover:text-gray-700 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                    </div>
                    <p class="text-[11px] font-semibold text-gray-600 dark:text-zinc-400 text-center">{{ i === 1 ? 'Slide principal' : 'Slide ' + i }}</p>
                  </div>
                </template>
              </div>
            </div>
            <input v-for="i in 3" :key="'ilk'+i" :ref="el => { if (el) ob_lookbook[i-1] = el }" type="file" class="hidden" accept="image/*" @change="e => handleOnboardingFile(e, 'lookbook_images', i-1)" />
          </template>

          <!-- ── dynamic-bento: grilla bento ── -->
          <template v-else-if="onboardingHookStyle === 'dynamic-bento'">
            <!-- Celda principal grande -->
            <div
              @click="triggerOnboardingFile('bento_main')"
              @dragover.prevent
              @drop.prevent="e => { const f = e.dataTransfer.files[0]; if (f) { const ev = { target: { files: [f], value: '' } }; handleOnboardingFile(ev, 'bento_main') } }"
              class="group relative w-full rounded-lg border-2 border-dashed cursor-pointer transition-all duration-200 overflow-hidden mb-2.5"
              :class="onboardingImages.bento_main ? 'border-gray-200 dark:border-zinc-700' : 'border-gray-300 dark:border-zinc-700 bg-gray-50 dark:bg-zinc-900/60 hover:bg-gray-100 dark:hover:bg-zinc-800/50 hover:border-gray-400 dark:hover:border-zinc-500'"
              style="height: 180px;"
            >
              <template v-if="onboardingImages.bento_main">
                <img :src="onboardingImages.bento_main" class="w-full h-full object-cover" />
                <div class="absolute inset-0 bg-black/0 group-hover:bg-black/30 transition-all flex items-center justify-center">
                  <span class="opacity-0 group-hover:opacity-100 text-white text-sm font-medium bg-black/60 px-4 py-2 rounded-full transition-all">Cambiar</span>
                </div>
                <button @click.stop="onboardingImages.bento_main = ''; updateOnboardingPreview()"
                  class="absolute top-3 right-3 w-8 h-8 bg-black/50 hover:bg-black/80 text-white rounded-full flex items-center justify-center transition-all shadow-md">
                  <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
              </template>
              <template v-else>
                <div class="absolute inset-0 flex flex-col items-center justify-center gap-3">
                  <div v-if="aiBrandData?.banner_texts?.headline" class="text-xs font-semibold text-gray-400 dark:text-zinc-500 uppercase tracking-wider text-center px-4">{{ aiBrandData.banner_texts.headline }}</div>
                  <div class="flex flex-col items-center gap-1.5">
                    <div class="w-10 h-10 rounded-full border-2 border-dashed border-gray-300 dark:border-zinc-600 group-hover:border-gray-500 flex items-center justify-center transition-colors">
                      <svg class="w-4 h-4 text-gray-500 dark:text-zinc-400 group-hover:text-gray-700 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                    </div>
                    <p class="text-xs font-semibold text-gray-600 dark:text-zinc-400">Imagen principal del panel</p>
                  </div>
                </div>
              </template>
            </div>
            <input ref="ob_bento_main" type="file" class="hidden" accept="image/*" @change="e => handleOnboardingFile(e, 'bento_main')" />

            <!-- Fila de 3 celdas: texto AI + bento_detail + bento_secondary -->
            <div class="flex gap-2.5" style="height: 130px;">
              <!-- Celda texto AI -->
              <div class="w-[36%] flex-shrink-0 rounded-lg border border-gray-200 dark:border-zinc-800 bg-gray-50 dark:bg-zinc-900/40 flex flex-col justify-end p-3 overflow-hidden">
                <p v-if="aiBrandData?.banner_texts?.subheadline" class="text-[10px] font-semibold uppercase tracking-widest text-gray-500 dark:text-zinc-500 mb-1 leading-tight">{{ aiBrandData.banner_texts.subheadline }}</p>
                <p v-if="aiBrandData?.banner_texts?.cta_text" class="text-[10px] font-medium text-gray-500 dark:text-zinc-500 border border-gray-200 dark:border-zinc-700 rounded-full px-2.5 py-0.5 inline-block w-fit">{{ aiBrandData.banner_texts.cta_text }}</p>
                <p v-if="!aiBrandData?.banner_texts" class="text-[10px] text-gray-400 dark:text-zinc-600 uppercase tracking-widest">Texto del diseño</p>
              </div>
              <!-- bento_detail -->
              <div
                @click="triggerOnboardingFile('bento_detail')"
                @dragover.prevent
                @drop.prevent="e => { const f = e.dataTransfer.files[0]; if (f) { const ev = { target: { files: [f], value: '' } }; handleOnboardingFile(ev, 'bento_detail') } }"
                class="group relative flex-1 rounded-lg border-2 border-dashed cursor-pointer transition-all duration-200 overflow-hidden"
                :class="onboardingImages.bento_detail ? 'border-gray-200 dark:border-zinc-700' : 'border-gray-300 dark:border-zinc-700 bg-gray-50 dark:bg-zinc-900/60 hover:bg-gray-100 dark:hover:bg-zinc-800/50 hover:border-gray-400 dark:hover:border-zinc-500'"
              >
                <template v-if="onboardingImages.bento_detail">
                  <img :src="onboardingImages.bento_detail" class="w-full h-full object-cover" />
                  <div class="absolute inset-0 bg-black/0 group-hover:bg-black/30 transition-all flex items-center justify-center">
                    <span class="opacity-0 group-hover:opacity-100 text-white text-[11px] font-medium bg-black/60 px-3 py-1.5 rounded-full transition-all">Cambiar</span>
                  </div>
                  <button @click.stop="onboardingImages.bento_detail = ''; updateOnboardingPreview()"
                    class="absolute top-2 right-2 w-5 h-5 bg-black/50 hover:bg-black/80 text-white rounded-full flex items-center justify-center transition-all">
                    <svg class="w-2.5 h-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                  </button>
                </template>
                <template v-else>
                  <div class="absolute inset-0 flex flex-col items-center justify-center gap-1.5">
                    <div class="w-7 h-7 rounded-full border-2 border-dashed border-gray-300 dark:border-zinc-600 group-hover:border-gray-500 flex items-center justify-center transition-colors">
                      <svg class="w-3 h-3 text-gray-500 dark:text-zinc-400 group-hover:text-gray-700 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                    </div>
                    <p class="text-[10px] font-semibold text-gray-500 dark:text-zinc-400">Detalle</p>
                  </div>
                </template>
              </div>
              <input ref="ob_bento_detail" type="file" class="hidden" accept="image/*" @change="e => handleOnboardingFile(e, 'bento_detail')" />
              <!-- bento_secondary -->
              <div
                @click="triggerOnboardingFile('bento_secondary')"
                @dragover.prevent
                @drop.prevent="e => { const f = e.dataTransfer.files[0]; if (f) { const ev = { target: { files: [f], value: '' } }; handleOnboardingFile(ev, 'bento_secondary') } }"
                class="group relative flex-1 rounded-lg border-2 border-dashed cursor-pointer transition-all duration-200 overflow-hidden"
                :class="onboardingImages.bento_secondary ? 'border-gray-200 dark:border-zinc-700' : 'border-gray-300 dark:border-zinc-700 bg-gray-50 dark:bg-zinc-900/60 hover:bg-gray-100 dark:hover:bg-zinc-800/50 hover:border-gray-400 dark:hover:border-zinc-500'"
              >
                <template v-if="onboardingImages.bento_secondary">
                  <img :src="onboardingImages.bento_secondary" class="w-full h-full object-cover" />
                  <div class="absolute inset-0 bg-black/0 group-hover:bg-black/30 transition-all flex items-center justify-center">
                    <span class="opacity-0 group-hover:opacity-100 text-white text-[11px] font-medium bg-black/60 px-3 py-1.5 rounded-full transition-all">Cambiar</span>
                  </div>
                  <button @click.stop="onboardingImages.bento_secondary = ''; updateOnboardingPreview()"
                    class="absolute top-2 right-2 w-5 h-5 bg-black/50 hover:bg-black/80 text-white rounded-full flex items-center justify-center transition-all">
                    <svg class="w-2.5 h-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                  </button>
                </template>
                <template v-else>
                  <div class="absolute inset-0 flex flex-col items-center justify-center gap-1.5">
                    <div class="w-7 h-7 rounded-full border-2 border-dashed border-gray-300 dark:border-zinc-600 group-hover:border-gray-500 flex items-center justify-center transition-colors">
                      <svg class="w-3 h-3 text-gray-500 dark:text-zinc-400 group-hover:text-gray-700 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                    </div>
                    <p class="text-[10px] font-semibold text-gray-500 dark:text-zinc-400">Acento</p>
                  </div>
                </template>
              </div>
              <input ref="ob_bento_secondary" type="file" class="hidden" accept="image/*" @change="e => handleOnboardingFile(e, 'bento_secondary')" />
            </div>
          </template>

          <!-- CTAs paso 2 -->
          <div class="mt-6 space-y-2.5">
            <button @click="uploadSubStep = 3; saveOnboardingDraft()"
              class="w-full py-4 bg-gray-900 dark:bg-white hover:bg-black dark:hover:bg-gray-100 text-white dark:text-gray-900 font-bold text-base rounded-xl shadow-md shadow-black/10 transition-all flex items-center justify-center gap-2.5">
              Siguiente: Historia visual
              <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg>
            </button>
            <div class="flex items-center justify-between">
              <button @click="uploadSubStep = 1" class="text-sm text-gray-400 dark:text-zinc-500 hover:text-gray-700 dark:hover:text-zinc-300 hover:underline transition-colors">← Anterior</button>
              <button @click="uploadSubStep = 3" class="text-sm text-gray-400 dark:text-zinc-500 hover:text-gray-700 dark:hover:text-zinc-300 hover:underline transition-colors">Omitir esta sección →</button>
            </div>
          </div>
        </template>

        <!-- ══════════════════════════════════════════════════════════════ -->
        <!-- PASO 3: Historia visual (story_image)                        -->
        <!-- ══════════════════════════════════════════════════════════════ -->
        <template v-else-if="uploadSubStep === 3">
          <div class="mb-6">
            <div class="text-[10px] font-bold uppercase tracking-widest text-gray-400 dark:text-zinc-500 mb-2">Sección 3 de 3 · Historia visual</div>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">La foto de tu colección</h2>
            <p class="text-sm text-gray-600 dark:text-zinc-400 leading-relaxed">
              Esta foto aparece en la sección de colección de tu tienda y le da personalidad a tu marca. Usa una imagen que cuente quién eres: un ambiente, una modelo, o tu producto estrella.
            </p>
          </div>

          <!-- 1 zona retrato -->
          <div
            @click="triggerOnboardingFile('story_image')"
            @dragover.prevent
            @drop.prevent="e => { const f = e.dataTransfer.files[0]; if (f) { const ev = { target: { files: [f], value: '' } }; handleOnboardingFile(ev, 'story_image') } }"
            class="group relative w-full rounded-lg border-2 border-dashed cursor-pointer transition-all duration-200 overflow-hidden mb-4"
            :class="onboardingImages.story_image ? 'border-gray-200 dark:border-zinc-700' : 'border-gray-300 dark:border-zinc-700 bg-gray-50 dark:bg-zinc-900/60 hover:bg-gray-100 dark:hover:bg-zinc-800/50 hover:border-gray-400 dark:hover:border-zinc-500'"
            style="aspect-ratio: 4/3;"
          >
            <template v-if="onboardingImages.story_image">
              <img :src="onboardingImages.story_image" class="w-full h-full object-cover" />
              <div class="absolute inset-0 bg-black/0 group-hover:bg-black/30 transition-all flex items-center justify-center">
                <span class="opacity-0 group-hover:opacity-100 text-white text-sm font-medium bg-black/60 px-4 py-2 rounded-full transition-all">Cambiar foto</span>
              </div>
              <button @click.stop="onboardingImages.story_image = ''; updateOnboardingPreview()"
                class="absolute top-3 right-3 w-8 h-8 bg-black/50 hover:bg-black/80 text-white rounded-full flex items-center justify-center transition-all shadow-md">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
              </button>
            </template>
            <template v-else>
              <div class="absolute inset-0 flex flex-col items-center justify-center gap-4 px-6 text-center">
                <div class="flex flex-col items-center gap-2">
                  <div class="w-14 h-14 rounded-full border-2 border-dashed border-gray-300 dark:border-zinc-600 group-hover:border-gray-500 flex items-center justify-center transition-colors">
                    <svg class="w-6 h-6 text-gray-500 dark:text-zinc-400 group-hover:text-gray-700 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                  </div>
                  <p class="text-sm font-semibold text-gray-700 dark:text-zinc-300 group-hover:text-gray-900 transition-colors">Subir foto aquí</p>
                  <p class="text-xs text-gray-500 dark:text-zinc-500">JPG o PNG · arrastra o haz clic · máx. 2MB</p>
                </div>
              </div>
            </template>
          </div>
          <input ref="ob_story" type="file" class="hidden" accept="image/*" @change="e => handleOnboardingFile(e, 'story_image')" />

          <!-- CTAs paso 3 / final -->
          <div class="mt-auto space-y-2.5">
            <button
              @click="finishOnboarding"
              :disabled="isOnboardingImagesSaving"
              class="w-full py-4 bg-gray-900 dark:bg-white hover:bg-black dark:hover:bg-gray-100 text-white dark:text-gray-900 font-bold text-lg rounded-xl shadow-lg shadow-black/15 transition-all disabled:opacity-30 disabled:cursor-not-allowed flex items-center justify-center gap-3"
            >
              <svg v-if="!isOnboardingImagesSaving" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/>
              </svg>
              <svg v-else class="animate-spin w-5 h-5" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
              </svg>
              {{ isOnboardingImagesSaving ? 'Guardando...' : '¡Ir a mi tienda!' }}
            </button>
            <div class="flex items-center justify-between">
              <button @click="uploadSubStep = 2" :disabled="isOnboardingImagesSaving" class="text-sm text-gray-400 dark:text-zinc-500 hover:text-gray-700 dark:hover:text-zinc-300 hover:underline transition-colors disabled:opacity-30">← Anterior</button>
              <button @click="skipOnboarding" :disabled="isOnboardingImagesSaving" class="text-sm text-gray-400 dark:text-zinc-500 hover:text-gray-700 dark:hover:text-zinc-300 hover:underline transition-colors disabled:opacity-30">Omitir y entrar →</button>
            </div>
          </div>
        </template>

      </div>

      <!-- ── DERECHA: Preview en teléfono ─────────────────────────────── -->
      <div class="flex-1 flex items-center justify-center relative overflow-hidden bg-gray-50 dark:bg-[#0d0d0f]">
        <div class="absolute inset-0 pointer-events-none bg-gradient-to-br from-slate-50/80 via-white/50 to-indigo-50/30 dark:from-[#0d0d0f] dark:to-[#0a0a0c]"></div>

        <div class="relative" style="width: 356px; height: 703px;">
          <div style="position: absolute; top: 0; left: 0; width: 375px; height: 740px; transform: scale(0.95); transform-origin: top left;">
            <div class="absolute inset-0 rounded-[3rem] border-[12px] border-black shadow-2xl pointer-events-none z-50">
              <div class="absolute top-0 left-1/2 -translate-x-1/2 w-36 h-6 bg-black rounded-b-2xl -mt-[12px]"></div>
              <div class="absolute -left-[12px] top-24 w-1 h-10 bg-black rounded-l"></div>
              <div class="absolute -left-[12px] top-36 w-1 h-14 bg-black rounded-l"></div>
              <div class="absolute -right-[12px] top-28 w-1 h-16 bg-black rounded-r"></div>
            </div>
            <div class="w-full h-full overflow-hidden bg-white relative rounded-[2.4rem]" style="isolation: isolate; transform: translateZ(0);">
              <div v-if="isOnboardingPreviewLoading" class="absolute inset-0 flex flex-col items-center justify-center gap-3 bg-white z-10">
                <div class="w-5 h-5 border border-gray-200 border-t-gray-500 rounded-full animate-spin"></div>
                <p class="text-[10px] text-gray-400 tracking-wide">Actualizando preview...</p>
              </div>
              <iframe
                ref="onboardingPreviewEl"
                :key="onboardingPreviewKey"
                :src="`${catalogUrl}?ai_preview=1`"
                class="w-full h-full border-0"
                style="width: 375px; height: 740px;"
                title="Vista Previa"
                @load="isOnboardingPreviewLoading = false"
              ></iframe>
            </div>
          </div>
        </div>

        <!-- Hint flotante -->
        <div class="absolute bottom-8 left-0 right-0 flex justify-center pointer-events-none">
          <div class="inline-flex items-center gap-2 px-4 py-2 bg-white/90 dark:bg-zinc-900/90 backdrop-blur-sm border border-gray-200 dark:border-zinc-700 rounded-full shadow-md">
            <svg class="w-3.5 h-3.5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/>
            </svg>
            <span class="text-xs font-medium text-gray-600 dark:text-zinc-400">El preview se actualiza en tiempo real</span>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- ═══════════════════════════════════════════════════════════════════ -->
  <!-- PANEL COMPLETO: 3 COLUMNAS (solo cuando ya hay identidad creada)  -->
  <!-- ═══════════════════════════════════════════════════════════════════ -->
  <div v-if="catalogUiMode === 'configured'" class="flex overflow-hidden bg-[#f8f9fa] dark:bg-gradient-to-b dark:from-[#131314] dark:via-[#1e1f20] dark:to-[#131314] h-full">
    
    <!-- SIDEBAR IZQUIERDO - Menú de Navegación - Gemini -->
    <Transition
      enter-active-class="transition-all duration-500 ease-out"
      enter-from-class="opacity-0 -translate-x-8"
      enter-to-class="opacity-100 translate-x-0"
    >
    <aside v-if="showConfigPanel" class="w-52 bg-white dark:bg-[#1e1f20] border-r border-[#e8eaed] dark:border-[#3a3a3f] flex flex-col" style="min-height: 0;">
      <!-- Header Sidebar - Gemini -->
      <div class="px-4 py-3 border-b border-[#e8eaed] dark:border-[#3a3a3f] flex-shrink-0">
        <div class="flex items-center gap-2 mb-3">
          <div class="w-8 h-8 bg-[#1e1f20] dark:bg-[#3a3a3f] rounded-full flex items-center justify-center flex-shrink-0">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
            </svg>
          </div>
          <div>
            <h2 class="text-xs font-medium text-[#1e1f20] dark:text-[#e3e3e3] tracking-tight">Configuración</h2>
            <p class="text-[10px] text-[#5f6368] dark:text-[#9aa0a6]">Catálogo Web</p>
          </div>
        </div>
        
        <!-- Estado de la Tienda - Gemini -->
        <div class="flex items-center justify-between p-2 bg-[#f8f9fa] dark:bg-[#282a2c] rounded-full">
          <span class="text-[10px] font-medium text-[#5f6368] dark:text-[#9aa0a6] ml-1">Estado:</span>
          <div class="flex items-center gap-1.5">
            <button 
              @click="config.storeActive = !config.storeActive"
              class="relative inline-flex h-5 w-9 items-center rounded-full transition-colors focus:outline-none"
              :class="config.storeActive ? 'bg-[#1e8e3e]' : 'bg-[#dadce0] dark:bg-[#5f6368]'"
            >
              <span
                class="inline-block h-3.5 w-3.5 transform rounded-full bg-white transition-transform"
                :class="config.storeActive ? 'translate-x-5' : 'translate-x-0.5'"
              />
            </button>
            <span class="text-[9px] font-medium px-2 py-0.5 rounded-full" 
                  :class="config.storeActive 
                    ? 'bg-[#e6f4ea] dark:bg-[#1e8e3e]/20 text-[#1e8e3e] dark:text-[#81c995]' 
                    : 'bg-[#f0f4f9] dark:bg-[#3a3a3f] text-[#5f6368] dark:text-[#9aa0a6]'">
              {{ config.storeActive ? 'Activa' : 'Inactiva' }}
            </span>
          </div>
        </div>
      </div>
      
      <!-- Navegación - Gemini -->
      <nav class="flex-1 px-3 py-3 space-y-0.5 overflow-y-auto">
        <button 
          v-for="tab in tabs" 
          :key="tab.id"
          @click="activeTab = tab.id"
          class="w-full text-left px-3 py-2.5 rounded-full transition-all duration-150 flex items-center gap-2.5 group"
          :class="activeTab === tab.id 
            ? 'bg-[#e8f0fe] dark:bg-[#1a73e8]/20 text-[#1a73e8] dark:text-[#8ab4f8]' 
            : 'text-[#5f6368] dark:text-[#9aa0a6] hover:bg-[#f0f4f9] dark:hover:bg-[#282a2c]'"
        >
          <div class="w-4 h-4 flex items-center justify-center flex-shrink-0">
            <svg v-if="tab.icon === 'sparkles'" xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" 
                 :class="activeTab === tab.id ? 'text-[#1a73e8] dark:text-[#8ab4f8]' : 'text-[#5f6368] dark:text-[#9aa0a6]'" 
                 fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09zM18.259 8.715L18 9.75l-.259-1.035a3.375 3.375 0 00-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 002.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 002.455 2.456L21.75 6l-1.036.259a3.375 3.375 0 00-2.455 2.456z" />
            </svg>
            <svg v-else-if="tab.icon === 'palette'" xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" 
                 :class="activeTab === tab.id ? 'text-[#1a73e8] dark:text-[#8ab4f8]' : 'text-[#5f6368] dark:text-[#9aa0a6]'" 
                 fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01" />
            </svg>
            <svg v-else-if="tab.icon === 'box'" xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" 
                 :class="activeTab === tab.id ? 'text-[#1a73e8] dark:text-[#8ab4f8]' : 'text-[#5f6368] dark:text-[#9aa0a6]'" 
                 fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
            </svg>
            <svg v-else-if="tab.icon === 'message'" xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" 
                 :class="activeTab === tab.id ? 'text-[#1a73e8] dark:text-[#8ab4f8]' : 'text-[#5f6368] dark:text-[#9aa0a6]'" 
                 fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
            </svg>
            <svg v-else-if="tab.icon === 'settings'" xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" 
                 :class="activeTab === tab.id ? 'text-[#1a73e8] dark:text-[#8ab4f8]' : 'text-[#5f6368] dark:text-[#9aa0a6]'" 
                 fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
              <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
          </div>
          <span class="text-[15px] font-medium">{{ tab.label }}</span>
        </button>
      </nav>
      
      <!-- Footer Sidebar con botones de acción - Gemini -->
      <div class="px-3 pb-3 border-t border-[#e8eaed] dark:border-[#3a3a3f] pt-3 space-y-1.5 flex-shrink-0 bg-[#f8f9fa] dark:bg-[#282a2c]">
        <button 
          @click="saveConfiguration"
          :disabled="isSaving"
          class="w-full px-3 py-2.5 bg-[#1e8e3e] hover:bg-[#168936] text-white text-xs font-medium rounded-full transition-all duration-150 flex items-center justify-center gap-1.5 disabled:opacity-50 disabled:cursor-not-allowed"
        >
          <svg v-if="!isSaving" xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
          </svg>
          <svg v-else class="animate-spin h-3.5 w-3.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
          </svg>
          {{ isSaving ? 'Guardando...' : 'Guardar Todo' }}
        </button>
        
        <button 
          @click="copyStoreLink"
          class="w-full px-3 py-2 bg-white dark:bg-[#3a3a3f] hover:bg-[#f0f4f9] dark:hover:bg-[#4a4a4f] text-[#1e1f20] dark:text-[#e3e3e3] text-xs font-medium rounded-full transition-all duration-150 flex items-center justify-center gap-1.5">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3" />
          </svg>
          Copiar Enlace
        </button>
        
        <button 
          @click="openCatalogInNewWindow"
          class="w-full px-3 py-2 bg-[#1e1f20] dark:bg-[#e3e3e3] hover:bg-black dark:hover:bg-white text-white dark:text-[#1e1f20] text-xs font-medium rounded-full transition-all duration-150 flex items-center justify-center gap-1.5"
        >
          <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
          </svg>
          Ver Página
        </button>

        <!-- Separador -->
        <div class="border-t border-[#e8eaed] dark:border-[#3a3a3f] my-1"></div>

        <!-- Cargar Plantilla + Restablecer -->
        <div class="grid grid-cols-2 gap-1.5">
          <button
            @click="showRestoreModal = true; restoreData = null; restoreFileName = ''"
            class="px-2 py-2 bg-transparent hover:bg-blue-50 dark:hover:bg-blue-900/20 text-[#5f6368] dark:text-[#9aa0a6] hover:text-blue-600 dark:hover:text-blue-400 text-[11px] font-medium rounded-full transition-all duration-150 flex items-center justify-center gap-1 border border-transparent hover:border-blue-100 dark:hover:border-blue-900/30"
          >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5" />
            </svg>
            Cargar
          </button>

          <button
            @click="resetAiBrand"
            :disabled="!aiBrandData && !aiBrandDescription"
            class="px-2 py-2 bg-transparent hover:bg-red-50 dark:hover:bg-red-900/20 text-[#5f6368] dark:text-[#9aa0a6] hover:text-red-600 dark:hover:text-red-400 text-[11px] font-medium rounded-full transition-all duration-150 flex items-center justify-center gap-1 disabled:opacity-30 disabled:cursor-not-allowed border border-transparent hover:border-red-100 dark:hover:border-red-900/30"
          >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99" />
            </svg>
            Restablecer
          </button>
        </div>
      </div>
    </aside>
    </Transition>
    
    <!-- CONTENIDO CENTRAL - Gemini -->
    <Transition
      enter-active-class="transition-all duration-500 ease-out delay-100"
      enter-from-class="opacity-0 translate-y-6"
      enter-to-class="opacity-100 translate-y-0"
    >
    <main v-if="showConfigPanel" class="flex-1 flex flex-col overflow-hidden bg-[#f8f9fa] dark:bg-transparent">

      <!-- Sticky Action Bar -->
      <div class="flex-shrink-0 px-8 py-3 bg-white/95 dark:bg-[#1e1f20]/95 backdrop-blur-sm border-b border-[#e8eaed] dark:border-[#3a3a3f] flex items-center justify-between z-10 shadow-[0_1px_0_0_rgba(0,0,0,0.06)]">
        <div>
          <h2 class="text-base font-semibold text-[#1e1f20] dark:text-[#e3e3e3]">{{ currentTabLabel }}</h2>
          <p class="text-sm text-[#9aa0a6] mt-0.5">{{ currentTabDescription }}</p>
        </div>
        <button
          @click="saveConfiguration"
          :disabled="isSaving"
          class="px-4 py-2 bg-[#1e8e3e] hover:bg-[#168936] text-white text-sm font-medium rounded-full transition-all duration-150 flex items-center gap-1.5 disabled:opacity-50 disabled:cursor-not-allowed shadow-sm"
        >
          <svg v-if="!isSaving" xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
          </svg>
          <svg v-else class="animate-spin h-3.5 w-3.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
          </svg>
          {{ isSaving ? 'Guardando...' : 'Guardar cambios' }}
        </button>
      </div>

      <!-- Área de contenido con scroll independiente -->
      <div class="flex-1 overflow-y-auto">
      <div class="px-5 py-6 md:px-8 space-y-6 max-w-7xl mx-auto w-full">
        
        <!-- Barra de Advertencia - Configuración Incompleta - Gemini -->
        <div v-if="showWarningMessage" 
             class="bg-[#fef7e0] dark:bg-[#ea8600]/15 border-l-4 border-[#ea8600] rounded-2xl p-4 flex items-start gap-3 animate-fade-in">
          <svg class="w-5 h-5 text-[#ea8600] dark:text-[#fdd663] flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
          </svg>
          <div class="flex-1">
            <h4 class="text-sm font-medium text-[#ea8600] dark:text-[#fdd663]">Configuración Incompleta</h4>
            <p v-if="warningType === 'categories'" class="text-xs text-[#b06000] dark:text-[#fcc934] mt-1">
              No puedes activar el catálogo sin seleccionar ninguna categoría para mostrar. 
              Ve a la pestaña <span class="font-medium">"Catálogo"</span> y selecciona al menos una categoría.
            </p>
            <p v-else-if="warningType === 'whatsapp'" class="text-xs text-[#b06000] dark:text-[#fcc934] mt-1">
              No puedes activar el catálogo sin configurar un número de WhatsApp válido. 
              Ve a la pestaña <span class="font-medium">"Pedidos"</span> e ingresa tu número completo (ej: +573001234567).
            </p>
          </div>
          <button 
            @click="activeTab = warningType === 'categories' ? 'catalog' : 'orders'; showWarningMessage = false" 
            class="px-3 py-1.5 bg-[#ea8600] hover:bg-[#b06000] text-white text-xs font-medium rounded-full transition-colors flex-shrink-0">
            {{ warningType === 'categories' ? 'Ir a Catálogo' : 'Ir a Pedidos' }}
          </button>
        </div>
          
          <!-- ============================================================ -->
          <!-- SECCIÓN: IDENTIDAD VISUAL — Módulo Unificado (Solo Moda)   -->
          <!-- ============================================================ -->
          <div v-if="activeTab === 'identidad-visual'" class="space-y-5 animate-fade-in pb-10">

            <!-- 01: BRIEF DE MARCA -->
            <div class="bg-white dark:bg-[#1e1f20] rounded-xl border border-gray-100 dark:border-[#2a2a30] p-6 shadow-[0_2px_10px_-3px_rgba(0,0,0,0.05)] dark:shadow-[0_2px_12px_-2px_rgba(0,0,0,0.3)] transition-shadow duration-200" :class="isLoading ? 'animate-pulse' : ''">
              <p class="text-[15px] text-gray-700 dark:text-gray-300 mb-4">Cuéntanos sobre tu negocio en tus propias palabras. Nuestra IA hará el resto.</p>

              <!-- Clean textarea -->
              <textarea
                v-model="aiBrandDescription"
                rows="5"
                placeholder="Describe tu marca: estilo, público objetivo, valores, estética visual... Cuanto más detallado, mejor será el resultado."
                class="w-full p-4 min-h-[120px] bg-white dark:bg-[#282a2c] text-base text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 border border-gray-200 dark:border-[#3a3a3f] rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-gray-900 dark:focus:ring-white focus:border-transparent resize-none leading-relaxed transition-all duration-200"
                :disabled="isGeneratingBrand || isLoading"
                maxlength="2000"
              ></textarea>

              <!-- Character counter below textarea -->
              <p class="text-xs text-gray-400 dark:text-gray-500 mt-2 text-right select-none">{{ aiBrandDescription.length }}/2000</p>

              <!-- Actions row -->
              <div class="flex items-center justify-end gap-3 mt-4">
                <!-- Mic button -->
                <button
                  @click="isRecordingVoice ? stopVoiceRecording() : startVoiceRecording()"
                  :class="isRecordingVoice
                    ? 'bg-[#ea4335] text-white animate-pulse shadow-[0_0_12px_rgba(234,67,53,0.4)]'
                    : 'bg-gray-100 dark:bg-[#2a2a30] text-gray-600 dark:text-gray-400 hover:bg-gray-200 dark:hover:bg-[#3a3a3f]'"
                  class="w-10 h-10 rounded-lg flex items-center justify-center transition-all flex-shrink-0"
                  :title="isRecordingVoice ? 'Detener grabación' : 'Dictar descripción'"
                  :disabled="isLoading"
                >
                  <svg v-if="!isRecordingVoice" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 18.75a6 6 0 006-6v-1.5m-6 7.5a6 6 0 01-6-6v-1.5m6 7.5v3.75m-3.75 0h7.5M12 15.75a3 3 0 01-3-3V4.5a3 3 0 116 0v8.25a3 3 0 01-3 3z" />
                  </svg>
                  <svg v-else class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5.25 7.5A2.25 2.25 0 017.5 5.25h9a2.25 2.25 0 012.25 2.25v9a2.25 2.25 0 01-2.25 2.25h-9a2.25 2.25 0 01-2.25-2.25v-9z" />
                  </svg>
                </button>

                <!-- AI Generate button -->
                <button
                  @click="generateAiBrand"
                  :disabled="isGeneratingBrand || aiBrandDescription.trim().length < 10 || isLoading"
                  class="px-6 py-2.5 bg-gray-900 hover:bg-black dark:bg-white dark:hover:bg-gray-100 text-white dark:text-black text-sm font-medium rounded-lg transition-colors flex items-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed"
                >
                  <svg v-if="!isGeneratingBrand" class="w-4 h-4 flex-shrink-0" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09zM18.259 8.715L18 9.75l-.259-1.035a3.375 3.375 0 00-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 002.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 002.456 2.456L21.75 6l-1.035.259a3.375 3.375 0 00-2.456 2.456zM16.894 20.567L16.5 21.75l-.394-1.183a2.25 2.25 0 00-1.423-1.423L13.5 18.75l1.183-.394a2.25 2.25 0 001.423-1.423l.394-1.183.394 1.183a2.25 2.25 0 001.423 1.423l1.183.394-1.183.394a2.25 2.25 0 00-1.423 1.423z" />
                  </svg>
                  <svg v-else class="animate-spin w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                  </svg>
                  {{ isGeneratingBrand ? 'Creando la magia de tu tienda...' : 'Generar Identidad con IA' }}
                </button>
              </div>

              <!-- Recording status -->
              <p v-if="isRecordingVoice" class="text-[15px] text-[#ea4335] dark:text-[#f28b82] flex items-center gap-1.5 mt-3">
                <span class="w-2 h-2 bg-[#ea4335] rounded-full animate-pulse"></span>
                Grabando... Habla sobre tu negocio y presiona el botón para terminar.
              </p>
            </div>

            <!-- Contenedor Grid para Color y Tipografía -->
            <div :class="[aiBrandData?.fonts ? 'grid grid-cols-1 md:grid-cols-2 gap-6 items-stretch' : '', isGeneratingBrand ? 'opacity-50 blur-[2px] pointer-events-none transition-all duration-500' : 'transition-all duration-500']">
            <!-- 02: IDENTIDAD DE COLOR -->
            <div class="bg-white dark:bg-[#1e1f20] rounded-xl border border-gray-100 dark:border-[#2a2a30] overflow-hidden shadow-[0_2px_10px_-3px_rgba(0,0,0,0.05)] dark:shadow-[0_2px_12px_-2px_rgba(0,0,0,0.3)] transition-shadow duration-200 h-full flex flex-col" :class="isLoading ? 'animate-pulse' : ''">
              <div class="px-6 py-5 border-b border-gray-100 dark:border-[#2a2a30] flex items-center justify-between">
                <div>
                  <p class="text-xs font-bold tracking-widest text-gray-500 dark:text-gray-400 uppercase mb-0.5">02</p>
                  <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Identidad de Color</h3>
                </div>
                <!-- Pencil: toggle refinement panel -->
                <button
                  v-if="aiBrandData?.color_palette"
                  @click="showColorRefinement = !showColorRefinement"
                  :class="showColorRefinement
                    ? 'text-[#1a73e8] dark:text-[#8ab4f8] border-[#1a73e8]/30 dark:border-[#8ab4f8]/30'
                    : 'text-gray-400 dark:text-[#9aa0a6] hover:text-gray-600 dark:hover:text-[#e3e3e3] border-gray-100 dark:border-[#3a3a3f] hover:border-gray-200 dark:hover:border-[#5f6368]'"
                  class="flex items-center gap-1.5 px-3 py-1.5 text-sm font-medium rounded-full border transition-all duration-150"
                  title="Refinar colores con IA"
                >
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                  </svg>
                  Refinar
                </button>
              </div>
              <div class="p-6 flex-1 flex flex-col">
                <!-- Color Primario Manual — siempre visible -->
                <p class="text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-3">Color Principal</p>
                <div class="flex items-center gap-4 p-4 border border-gray-100 dark:border-[#2a2a30] rounded-xl bg-gray-50 dark:bg-[#282a2c]">
                  <!-- Circular color swatch — hides ugly native square -->
                  <label class="relative cursor-pointer flex-shrink-0">
                    <div class="w-12 h-12 rounded-full shadow-md ring-1 ring-black/10 dark:ring-white/10 transition-transform hover:scale-105" :style="{ backgroundColor: config.brandIdentity.primaryColor }"></div>
                    <input type="color" v-model="config.brandIdentity.primaryColor" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer rounded-full" tabindex="-1" />
                  </label>
                  <div class="flex-1">
                    <input
                      type="text"
                      v-model="config.brandIdentity.primaryColor"
                      class="w-32 px-3 py-1.5 rounded-lg border border-gray-200 dark:border-[#3a3a3f] text-[15px] font-mono text-gray-900 dark:text-[#e3e3e3] bg-white dark:bg-[#1e1f20] focus:outline-none focus:ring-1 focus:ring-gray-300 dark:focus:ring-gray-600 uppercase"
                      placeholder="#000000"
                    />
                    <p class="text-[13px] font-medium text-gray-600 dark:text-gray-400 mt-1.5">Aplicado en botones, acentos y llamadas a la acción</p>
                  </div>
                </div>

                <!-- Paleta Generada por IA -->
                <template v-if="aiBrandData?.color_palette">
                  <div class="mt-5 pt-5 border-t border-gray-100 dark:border-[#2a2a30]">
                    <div class="flex items-center justify-between mb-4">
                      <p class="text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Paleta IA</p>
                      <button
                        @click="applyAiColors"
                        class="text-sm font-medium px-3 py-1.5 rounded-full border border-gray-100 dark:border-[#3a3a3f] text-gray-400 dark:text-[#9aa0a6] hover:border-gray-300 dark:hover:border-[#5f6368] hover:text-gray-700 dark:hover:text-[#e3e3e3] transition-colors"
                      >
                        Usar primario de IA
                      </button>
                    </div>
                    <div class="flex gap-2">
                      <template v-for="(color, key) in aiBrandData.color_palette" :key="key">
                        <div
                          v-if="!['text_dark', 'text_light', 'TEXT_DARK', 'TEXT_LIGHT'].includes(key.toLowerCase())"
                          class="flex-1 group cursor-pointer"
                          :title="color"
                        >
                        <div
                          class="h-14 rounded-xl mb-1.5 transition-transform group-hover:scale-105 shadow-sm"
                          :style="{ backgroundColor: color }"
                        ></div>
                        <p class="text-[11px] text-center text-gray-700 dark:text-gray-300 uppercase tracking-wider font-medium truncate">{{ key.replace('_', '\u00a0') }}</p>
                        <p class="text-[11px] text-center font-mono text-gray-600 dark:text-gray-400 uppercase tracking-wider truncate">{{ color }}</p>
                      </div>
                      </template>
                    </div>
                  </div>

                  <!-- Refinement panel — slides in with transition -->
                  <Transition
                    enter-active-class="transition-all duration-300 ease-out"
                    enter-from-class="opacity-0 -translate-y-2"
                    enter-to-class="opacity-100 translate-y-0"
                    leave-active-class="transition-all duration-200 ease-in"
                    leave-from-class="opacity-100 translate-y-0"
                    leave-to-class="opacity-0 -translate-y-2"
                  >
                    <div v-if="showColorRefinement" class="mt-5">
                      <div class="relative rounded-2xl border border-[#e8eaed] dark:border-[#3a3a3f] bg-[#f8f9fa] dark:bg-[#282a2c] overflow-hidden focus-within:border-[#1a73e8]/50 dark:focus-within:border-[#8ab4f8]/40 focus-within:shadow-[0_0_0_3px_rgba(26,115,232,0.08)] transition-all duration-200">
                        <!-- Label strip -->
                        <div class="px-5 pt-4 pb-0">
                          <p class="text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Guía para la IA</p>
                        </div>
                        <!-- Borderless textarea -->
                        <textarea
                          v-model="colorRefinementPrompt"
                          rows="3"
                          placeholder="Ej: Quiero tonos tierra más cálidos, menos saturación. Prefiero paleta seria y minimalista con acentos dorados..."
                          class="w-full px-5 pt-3 pb-3 bg-transparent text-[15px] text-gray-900 dark:text-white placeholder-gray-500 focus:outline-none resize-none leading-relaxed"
                          :disabled="isRegeneratingColors"
                        ></textarea>
                        <!-- Divider -->
                        <div class="h-px bg-[#e8eaed] dark:bg-[#3a3a3f]"></div>
                        <!-- Action row -->
                        <div class="flex items-center justify-end gap-2 px-4 py-3">
                          <button
                            @click="showColorRefinement = false; colorRefinementPrompt = ''"
                            class="px-3 py-2 text-sm text-[#5f6368] dark:text-[#9aa0a6] hover:text-[#1e1f20] dark:hover:text-[#e3e3e3] transition-colors rounded-xl"
                          >
                            Cancelar
                          </button>
                          <button
                            @click="regenerateColors"
                            :disabled="isRegeneratingColors"
                            class="px-5 py-2.5 bg-gradient-to-r from-[#0d0d1f] via-[#151535] to-[#0d1528] hover:from-[#080812] hover:via-[#0f0f28] hover:to-[#08101e] dark:from-[#c8d6f5] dark:via-[#b0c4f0] dark:to-[#a0b8eb] dark:hover:from-[#d8e6ff] dark:hover:to-[#b8cffa] text-white dark:text-[#0d0d1f] text-sm font-semibold rounded-xl transition-all duration-200 flex items-center gap-2 disabled:opacity-40 disabled:cursor-not-allowed shadow-[0_2px_14px_rgba(13,13,31,0.3)] hover:shadow-[0_3px_22px_rgba(13,13,31,0.42)] dark:shadow-[0_2px_14px_rgba(160,184,235,0.18)]"
                          >
                            <svg v-if="!isRegeneratingColors" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                              <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09z" />
                            </svg>
                            <svg v-else class="animate-spin w-3.5 h-3.5" fill="none" viewBox="0 0 24 24">
                              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            {{ isRegeneratingColors ? 'Regenerando...' : 'Regenerar Colores' }}
                          </button>
                        </div>
                      </div>
                    </div>
                  </Transition>
                </template>
              </div>
            </div>

            <!-- 03: TIPOGRAFÍA (Solo cuando hay datos de IA) -->
            <div v-if="aiBrandData?.fonts" class="bg-white dark:bg-[#1e1f20] rounded-xl border border-gray-100 dark:border-[#2a2a30] overflow-hidden shadow-[0_2px_10px_-3px_rgba(0,0,0,0.05)] dark:shadow-[0_2px_12px_-2px_rgba(0,0,0,0.3)] transition-shadow duration-200 h-full flex flex-col" :class="isLoading ? 'animate-pulse' : ''">
              <div class="px-6 py-5 border-b border-gray-100 dark:border-[#2a2a30] flex items-center justify-between">
                <div>
                  <p class="text-xs font-bold tracking-widest text-gray-500 dark:text-gray-400 uppercase mb-0.5">03</p>
                  <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Tipografía</h3>
                </div>
                <button
                  @click="showFontModal = true"
                  class="flex items-center gap-1.5 px-3 py-1.5 text-sm font-medium text-gray-400 dark:text-[#9aa0a6] hover:text-gray-700 dark:hover:text-[#e3e3e3] rounded-full border border-gray-100 dark:border-[#3a3a3f] hover:border-gray-200 dark:hover:border-[#5f6368] transition-all duration-150"
                  title="Cambiar par tipográfico"
                >
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                  </svg>
                  Modificar
                </button>
              </div>
              <div class="p-6 flex-1 flex flex-col">
                <div class="grid grid-cols-2 gap-4">
                  <div class="p-5 rounded-xl border border-gray-100 dark:border-[#2a2a30] bg-gray-50 dark:bg-[#282a2c]">
                    <p class="text-[11px] font-medium text-gray-500 dark:text-[#9aa0a6] uppercase tracking-wider mb-3">Títulos</p>
                    <p class="text-4xl text-[#1e1f20] dark:text-[#e3e3e3] leading-none mb-2" :style="{ fontFamily: aiBrandData.fonts.heading + ', serif' }">Aa</p>
                    <p class="text-sm text-[#5f6368] dark:text-[#9aa0a6] font-medium">{{ aiBrandData.fonts.heading }}</p>
                  </div>
                  <div class="p-5 rounded-xl border border-gray-100 dark:border-[#2a2a30] bg-gray-50 dark:bg-[#282a2c]">
                    <p class="text-[11px] font-medium text-gray-600 dark:text-gray-400 uppercase tracking-wider mb-3">Cuerpo</p>
                    <p class="text-[15px] text-gray-900 dark:text-white leading-relaxed mb-2" :style="{ fontFamily: aiBrandData.fonts.body + ', sans-serif' }">Texto base</p>
                    <p class="text-[13px] text-gray-700 dark:text-gray-300 font-medium">{{ aiBrandData.fonts.body }}</p>
                  </div>
                </div>
                <p v-if="aiBrandData.fonts.style_rationale" class="text-[15px] text-gray-700 dark:text-gray-300 mt-4 leading-relaxed italic border-l-2 border-[#e8eaed] dark:border-[#3a3a3f] pl-3">
                  {{ aiBrandData.fonts.style_rationale }}
                </p>
              </div>
            </div>
            </div> <!-- End Grid Contenedor -->

            <!-- 04 / 03: ACTIVOS VISUALES -->
            <div class="bg-white dark:bg-[#1e1f20] rounded-xl border border-gray-100 dark:border-[#2a2a30] overflow-hidden shadow-[0_2px_10px_-3px_rgba(0,0,0,0.05)] dark:shadow-[0_2px_12px_-2px_rgba(0,0,0,0.3)] transition-all duration-500" :class="[isLoading ? 'animate-pulse' : '', isGeneratingBrand ? 'opacity-50 blur-[2px] pointer-events-none' : '']">
              <div class="px-6 py-5 border-b border-gray-100 dark:border-[#2a2a30]">
                <p class="text-xs font-bold tracking-widest text-gray-500 dark:text-gray-400 uppercase mb-0.5">{{ aiBrandData?.fonts ? '04' : '03' }}</p>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Activos Visuales</h3>
              </div>
              <div class="p-6">
                <!-- Fila 1: Logo + Hero Imagen 1 -->
                <div class="grid grid-cols-2 gap-5">
                  <!-- Logotipo -->
                  <div class="space-y-2">
                    <p class="text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Logotipo</p>
                    <div
                      @click="triggerFileUpload('logo')"
                      class="relative w-full h-28 border-2 border-dashed border-gray-300 dark:border-[#4a4a4f] rounded-xl hover:bg-gray-50 dark:hover:bg-[#282a2c] hover:border-gray-400 dark:hover:border-gray-500 transition-all cursor-pointer group bg-white dark:bg-[#1e1f20] flex items-center justify-center overflow-hidden"
                    >
                      <div v-if="config.brandIdentity.logo" class="absolute inset-0 p-3 flex items-center justify-center">
                        <img :src="config.brandIdentity.logo" class="max-w-full max-h-full object-contain" />
                        <div class="absolute inset-0 bg-black/60 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center rounded-xl">
                          <button @click.stop="config.brandIdentity.logo = ''" class="text-white text-xs font-medium px-3 py-1.5 bg-white/20 hover:bg-white/30 rounded-full transition-colors">Eliminar</button>
                        </div>
                      </div>
                      <div v-else class="flex flex-col items-center gap-2 text-gray-500 dark:text-gray-400 group-hover:text-gray-800 dark:group-hover:text-gray-200 transition-colors">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5" />
                        </svg>
                        <span class="text-sm font-medium">PNG · SVG · JPG</span>
                      </div>
                      <input type="file" ref="logoInput" class="hidden" accept="image/*" @change="(e) => handleFileUpload(e, 'logo')" />
                    </div>
                  </div>

                  <!-- Hero imagen 1 (campo legacy banner_url mantenido por compatibilidad) -->
                  <div class="space-y-2">
                    <p class="text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Hero – Imagen Principal</p>
                    <div
                      @click="triggerFileUpload('hero_image', 0)"
                      class="relative w-full h-28 border-2 border-dashed border-gray-300 dark:border-[#4a4a4f] rounded-xl hover:bg-gray-50 dark:hover:bg-[#282a2c] hover:border-gray-400 dark:hover:border-gray-500 transition-all cursor-pointer group bg-white dark:bg-[#1e1f20] flex items-center justify-center overflow-hidden"
                    >
                      <div v-if="config.catalogMedia.hero_images[0]" class="absolute inset-0 overflow-hidden rounded-xl">
                        <img :src="config.catalogMedia.hero_images[0]" class="w-full h-full object-cover" />
                        <div class="absolute inset-0 bg-black/60 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                          <button @click.stop="config.catalogMedia.hero_images[0] = ''" class="text-white text-xs font-medium px-3 py-1.5 bg-white/20 hover:bg-white/30 rounded-full transition-colors">Eliminar</button>
                        </div>
                      </div>
                      <div v-else class="flex flex-col items-center gap-2 text-gray-500 dark:text-gray-400 group-hover:text-gray-800 dark:group-hover:text-gray-200 transition-colors">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
                        </svg>
                        <span class="text-sm font-medium">Foto principal</span>
                      </div>
                      <input
                        :ref="(el) => heroImageInputs[0] = el"
                        type="file" class="hidden" accept="image/*"
                        @change="(e) => handleFileUpload(e, 'hero_image', 0)"
                      />
                    </div>
                  </div>
                </div>

                <!-- Hero Carrusel: imágenes 2 y 3 -->
                <div class="mt-5 pt-5 border-t border-gray-100 dark:border-[#2a2a30]">
                  <div class="flex items-center justify-between mb-3">
                    <div>
                      <p class="text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Carrusel del Hero</p>
                      <p class="text-[13px] text-gray-500 dark:text-gray-400 mt-0.5">Opcional · hasta 2 fotos adicionales que rotan con la principal</p>
                    </div>
                    <span class="text-xs font-medium text-[#9aa0a6] bg-[#f8f9fa] dark:bg-[#282a2c] px-2 py-1 rounded-full border border-[#e8eaed] dark:border-[#3a3a3f]">
                      {{ config.catalogMedia.hero_images.filter(Boolean).length }}/3
                    </span>
                  </div>
                  <div class="grid grid-cols-2 gap-3">
                    <div v-for="idx in [1, 2]" :key="'hero-extra-'+idx" class="space-y-1.5">
                      <p class="text-[11px] font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Foto {{ idx + 1 }}</p>
                      <div
                        @click="triggerFileUpload('hero_image', idx)"
                        class="relative w-full h-24 border-2 border-dashed border-gray-300 dark:border-[#4a4a4f] rounded-xl hover:bg-gray-50 dark:hover:bg-[#282a2c] hover:border-gray-400 dark:hover:border-gray-500 transition-all cursor-pointer group bg-white dark:bg-[#1e1f20] flex items-center justify-center overflow-hidden"
                      >
                        <div v-if="config.catalogMedia.hero_images[idx]" class="absolute inset-0 overflow-hidden rounded-xl">
                          <img :src="config.catalogMedia.hero_images[idx]" class="w-full h-full object-cover" />
                          <div class="absolute inset-0 bg-black/60 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                            <button @click.stop="config.catalogMedia.hero_images[idx] = ''" class="text-white text-xs font-medium px-3 py-1.5 bg-white/20 hover:bg-white/30 rounded-full transition-colors">Eliminar</button>
                          </div>
                        </div>
                        <div v-else class="flex flex-col items-center gap-1.5 text-gray-500 dark:text-gray-400 group-hover:text-gray-800 dark:group-hover:text-gray-200 transition-colors">
                          <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                          </svg>
                          <span class="text-xs font-medium">Agregar</span>
                        </div>
                        <input
                          :ref="(el) => heroImageInputs[idx] = el"
                          type="file" class="hidden" accept="image/*"
                          @change="(e) => handleFileUpload(e, 'hero_image', idx)"
                        />
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- 04b: MEDIOS DEL DISEÑO ACTIVO — solo visible si la IA asignó un hook_style -->
            <div
              v-if="activeHookStyle"
              class="bg-white dark:bg-[#1e1f20] rounded-xl border border-gray-100 dark:border-[#2a2a30] overflow-hidden shadow-[0_2px_10px_-3px_rgba(0,0,0,0.05)] dark:shadow-[0_2px_12px_-2px_rgba(0,0,0,0.3)] transition-shadow duration-200"
            >
              <div class="px-6 py-5 border-b border-gray-100 dark:border-[#2a2a30] flex items-center justify-between">
                <div>
                  <p class="text-xs font-bold tracking-widest text-gray-500 dark:text-gray-400 uppercase mb-0.5">Medios del Diseño</p>
                  <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                    {{ activeHookStyle === 'urban-lookbook' ? 'Urban Lookbook' : activeHookStyle === 'dynamic-bento' ? 'Dynamic Bento' : 'Editorial Story' }}
                  </h3>
                  <p class="text-xs text-[#9aa0a6] mt-1">
                    {{ activeHookStyle === 'urban-lookbook'
                        ? 'Hasta 4 imágenes + 1 video opcional para el bloque tipo carrusel urbano'
                        : activeHookStyle === 'dynamic-bento'
                        ? '2 imágenes para el grid bento: una lifestyle grande y un detalle de producto'
                        : 'Una imagen inspiracional para el bloque editorial de tu marca' }}
                  </p>
                </div>
                <!-- Badge del estilo activo -->
                <span class="flex-shrink-0 text-[10px] font-bold uppercase tracking-widest px-3 py-1.5 rounded-full"
                  :class="activeHookStyle === 'urban-lookbook'
                    ? 'bg-violet-50 dark:bg-violet-950 text-violet-700 dark:text-violet-400 border border-violet-100 dark:border-violet-800'
                    : activeHookStyle === 'dynamic-bento'
                    ? 'bg-blue-50 dark:bg-blue-950 text-blue-700 dark:text-blue-400 border border-blue-100 dark:border-blue-800'
                    : 'bg-amber-50 dark:bg-amber-950 text-amber-700 dark:text-amber-400 border border-amber-100 dark:border-amber-800'"
                >
                  Activo
                </span>
              </div>

              <div class="p-6">

                <!-- ══ URBAN LOOKBOOK: 4 imágenes + 1 video ══ -->
                <template v-if="activeHookStyle === 'urban-lookbook'">
                  <div class="grid grid-cols-2 gap-4">
                    <div v-for="idx in [0,1,2,3]" :key="'lkb-'+idx" class="space-y-1.5">
                      <p class="text-[11px] font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                        {{ idx === 0 ? 'Imagen Principal' : `Imagen ${idx + 1}` }}
                      </p>
                      <div
                        @click="triggerFileUpload('lookbook_image', idx)"
                        class="relative w-full h-32 border-2 border-dashed border-gray-300 dark:border-[#4a4a4f] rounded-xl hover:bg-gray-50 dark:hover:bg-[#282a2c] hover:border-violet-400 dark:hover:border-violet-600 transition-all cursor-pointer group bg-white dark:bg-[#1e1f20] flex items-center justify-center overflow-hidden"
                      >
                        <div v-if="config.catalogMedia.lookbook_images[idx]" class="absolute inset-0 overflow-hidden rounded-xl">
                          <img :src="config.catalogMedia.lookbook_images[idx]" class="w-full h-full object-cover" />
                          <div class="absolute inset-0 bg-black/60 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                            <button @click.stop="config.catalogMedia.lookbook_images[idx] = ''" class="text-white text-xs font-medium px-3 py-1.5 bg-white/20 hover:bg-white/30 rounded-full transition-colors">Eliminar</button>
                          </div>
                        </div>
                        <div v-else class="flex flex-col items-center gap-2 text-gray-500 dark:text-gray-400 group-hover:text-violet-600 dark:group-hover:text-violet-400 transition-colors">
                          <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
                          </svg>
                          <span class="text-xs font-medium">{{ idx === 0 ? 'Foto hero' : 'Agregar foto' }}</span>
                        </div>
                        <input
                          :ref="(el) => lookbookImageInputs[idx] = el"
                          type="file" class="hidden" accept="image/*"
                          @change="(e) => handleFileUpload(e, 'lookbook_image', idx)"
                        />
                      </div>
                    </div>
                  </div>

                  <!-- Video opcional -->
                  <div class="mt-5 pt-5 border-t border-gray-100 dark:border-[#2a2a30]">
                    <div class="flex items-center justify-between mb-3">
                      <div>
                        <p class="text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Video Opcional</p>
                        <p class="text-[13px] text-gray-500 dark:text-gray-400 mt-0.5">MP4 · MOV · WebM · máx. 50 MB · se muestra como 5to elemento del carrusel</p>
                      </div>
                      <span v-if="config.catalogMedia.lookbook_video" class="text-[10px] text-violet-600 dark:text-violet-400 font-semibold">Cargado ✓</span>
                    </div>
                    <div
                      @click="triggerFileUpload('lookbook_video')"
                      class="relative w-full h-20 border-2 border-dashed border-gray-300 dark:border-[#4a4a4f] rounded-xl hover:bg-gray-50 dark:hover:bg-[#282a2c] hover:border-violet-400 dark:hover:border-violet-600 transition-all cursor-pointer group bg-white dark:bg-[#1e1f20] flex items-center justify-center gap-3 px-5 overflow-hidden"
                    >
                      <div v-if="config.catalogMedia.lookbook_video" class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-violet-50 dark:bg-violet-950 border border-violet-100 dark:border-violet-800 flex items-center justify-center flex-shrink-0">
                          <svg class="w-5 h-5 text-violet-600 dark:text-violet-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5l4.72-4.72a.75.75 0 011.28.53v11.38a.75.75 0 01-1.28.53l-4.72-4.72M4.5 18.75h9a2.25 2.25 0 002.25-2.25v-9a2.25 2.25 0 00-2.25-2.25h-9A2.25 2.25 0 002.25 7.5v9a2.25 2.25 0 002.25 2.25z" />
                          </svg>
                        </div>
                        <div>
                          <p class="text-sm font-medium text-[#1e1f20] dark:text-[#e3e3e3]">Video cargado</p>
                          <button @click.stop="config.catalogMedia.lookbook_video = ''" class="text-xs text-[#ea4335] dark:text-[#f28b82] hover:underline">Eliminar</button>
                        </div>
                      </div>
                      <div v-else class="flex items-center gap-3 text-gray-500 dark:text-gray-400 group-hover:text-violet-600 dark:group-hover:text-violet-400 transition-colors">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5l4.72-4.72a.75.75 0 011.28.53v11.38a.75.75 0 01-1.28.53l-4.72-4.72M4.5 18.75h9a2.25 2.25 0 002.25-2.25v-9a2.25 2.25 0 00-2.25-2.25h-9A2.25 2.25 0 002.25 7.5v9a2.25 2.25 0 002.25 2.25z" />
                        </svg>
                        <div>
                          <p class="text-sm font-medium">Subir video opcional</p>
                          <p class="text-[13px]">MP4, MOV o WebM</p>
                        </div>
                      </div>
                      <input ref="lookbookVideoInput" type="file" class="hidden" accept="video/*" @change="(e) => handleFileUpload(e, 'lookbook_video')" />
                    </div>
                  </div>
                </template>

                <!-- ══ DYNAMIC BENTO: imagen principal + imagen detalle ══ -->
                <template v-else-if="activeHookStyle === 'dynamic-bento'">
                  <div class="grid grid-cols-2 gap-5">
                    <!-- Imagen principal (grande, lifestyle) -->
                    <div class="space-y-2">
                      <p class="text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Imagen Lifestyle</p>
                      <p class="text-[13px] text-gray-500 dark:text-gray-400">Foto grande que ocupa 2/3 del bento · retrato o acción deportiva</p>
                      <div
                        @click="triggerFileUpload('bento_main')"
                        class="relative w-full h-36 border-2 border-dashed border-gray-300 dark:border-[#4a4a4f] rounded-xl hover:border-blue-400 dark:hover:border-blue-600 hover:bg-gray-50 dark:hover:bg-[#282a2c] transition-all cursor-pointer group bg-white dark:bg-[#1e1f20] flex items-center justify-center overflow-hidden"
                      >
                        <div v-if="config.catalogMedia.bento_main" class="absolute inset-0 overflow-hidden rounded-xl">
                          <img :src="config.catalogMedia.bento_main" class="w-full h-full object-cover" />
                          <div class="absolute inset-0 bg-black/60 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                            <button @click.stop="config.catalogMedia.bento_main = ''" class="text-white text-xs font-medium px-3 py-1.5 bg-white/20 hover:bg-white/30 rounded-full transition-colors">Eliminar</button>
                          </div>
                        </div>
                        <div v-else class="flex flex-col items-center gap-2 text-gray-500 dark:text-gray-400 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">
                          <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
                          </svg>
                          <span class="text-sm font-medium">Foto lifestyle</span>
                        </div>
                        <input ref="bentoMainInput" type="file" class="hidden" accept="image/*" @change="(e) => handleFileUpload(e, 'bento_main')" />
                      </div>
                    </div>

                    <!-- Imagen detalle (producto de cerca) -->
                    <div class="space-y-2">
                      <p class="text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Imagen Detalle</p>
                      <p class="text-[13px] text-gray-500 dark:text-gray-400">Primer plano de producto · textura, material o detalle técnico</p>
                      <div
                        @click="triggerFileUpload('bento_detail')"
                        class="relative w-full h-36 border-2 border-dashed border-gray-300 dark:border-[#4a4a4f] rounded-xl hover:border-blue-400 dark:hover:border-blue-600 hover:bg-gray-50 dark:hover:bg-[#282a2c] transition-all cursor-pointer group bg-white dark:bg-[#1e1f20] flex items-center justify-center overflow-hidden"
                      >
                        <div v-if="config.catalogMedia.bento_detail" class="absolute inset-0 overflow-hidden rounded-xl">
                          <img :src="config.catalogMedia.bento_detail" class="w-full h-full object-cover" />
                          <div class="absolute inset-0 bg-black/60 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                            <button @click.stop="config.catalogMedia.bento_detail = ''" class="text-white text-xs font-medium px-3 py-1.5 bg-white/20 hover:bg-white/30 rounded-full transition-colors">Eliminar</button>
                          </div>
                        </div>
                        <div v-else class="flex flex-col items-center gap-2 text-gray-500 dark:text-gray-400 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">
                          <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607zM10.5 7.5v6m3-3h-6" />
                          </svg>
                          <span class="text-sm font-medium">Foto detalle</span>
                        </div>
                        <input ref="bentoDetailInput" type="file" class="hidden" accept="image/*" @change="(e) => handleFileUpload(e, 'bento_detail')" />
                      </div>
                    </div>
                  </div>
                </template>

                <!-- ══ EDITORIAL STORY: 1 imagen inspiracional ══ -->
                <template v-else-if="activeHookStyle === 'editorial-story'">
                  <div class="space-y-2">
                    <p class="text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Imagen Inspiracional</p>
                    <p class="text-[13px] text-gray-500 dark:text-gray-400">Foto que acompaña el relato de marca · proporción vertical 3:4 recomendada</p>
                    <div
                      @click="triggerFileUpload('editorial_image')"
                      class="relative w-full h-52 border-2 border-dashed border-gray-300 dark:border-[#4a4a4f] rounded-xl hover:border-amber-400 dark:hover:border-amber-600 hover:bg-gray-50 dark:hover:bg-[#282a2c] transition-all cursor-pointer group bg-white dark:bg-[#1e1f20] flex items-center justify-center overflow-hidden"
                    >
                      <div v-if="config.catalogMedia.editorial_image" class="absolute inset-0 overflow-hidden rounded-xl">
                        <img :src="config.catalogMedia.editorial_image" class="w-full h-full object-cover" />
                        <div class="absolute inset-0 bg-black/60 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                          <button @click.stop="config.catalogMedia.editorial_image = ''" class="text-white text-xs font-medium px-3 py-1.5 bg-white/20 hover:bg-white/30 rounded-full transition-colors">Eliminar</button>
                        </div>
                      </div>
                      <div v-else class="flex flex-col items-center gap-3 text-gray-500 dark:text-gray-400 group-hover:text-amber-600 dark:group-hover:text-amber-400 transition-colors">
                        <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.2">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
                        </svg>
                        <div class="text-center">
                          <p class="text-sm font-medium">Subir imagen editorial</p>
                          <p class="text-[13px] mt-0.5">Retrato · ambiente · still life · 3:4 ideal</p>
                        </div>
                      </div>
                      <input ref="editorialImageInput" type="file" class="hidden" accept="image/*" @change="(e) => handleFileUpload(e, 'editorial_image')" />
                    </div>
                  </div>
                </template>

              </div>
            </div>

            <!-- CONTENIDO GENERADO POR IA -->
            <template v-if="aiBrandData">

              <!-- Texto del Banner -->
              <div v-if="aiBrandData.banner_texts" class="bg-white dark:bg-[#1e1f20] rounded-xl border border-gray-100 dark:border-[#2a2a30] overflow-hidden shadow-[0_2px_10px_-3px_rgba(0,0,0,0.05)] dark:shadow-[0_2px_12px_-2px_rgba(0,0,0,0.3)] transition-shadow duration-200">
                <div class="px-6 py-5 border-b border-gray-100 dark:border-[#2a2a30]">
                  <p class="text-xs font-bold tracking-widest text-[#9aa0a6] uppercase mb-0.5">Copy</p>
                  <h3 class="text-xl font-semibold text-[#1e1f20] dark:text-[#e3e3e3]">Texto del Banner</h3>
                </div>
                <div class="px-6 py-5 space-y-5">
                  <!-- Headline -->
                  <div>
                    <p class="text-[11px] font-medium text-gray-400 dark:text-[#9aa0a6] uppercase tracking-wider mb-2">Titular</p>
                    <p class="text-lg font-medium text-gray-900 dark:text-[#e3e3e3] leading-snug border-b border-gray-100 dark:border-[#2a2a30] pb-3">{{ aiBrandData.banner_texts.headline }}</p>
                  </div>
                  <!-- Subheadline -->
                  <div>
                    <p class="text-[11px] font-medium text-gray-400 dark:text-[#9aa0a6] uppercase tracking-wider mb-2">Subtítulo</p>
                    <p class="text-sm text-gray-500 dark:text-[#9aa0a6] uppercase tracking-widest border-b border-gray-100 dark:border-[#2a2a30] pb-3">{{ aiBrandData.banner_texts.subheadline }}</p>
                  </div>
                  <!-- CTA -->
                  <div v-if="aiBrandData.banner_texts.cta_text">
                    <p class="text-[11px] font-medium text-gray-400 dark:text-[#9aa0a6] uppercase tracking-wider mb-2">Llamada a la acción</p>
                    <span class="inline-flex items-center px-4 py-1.5 border border-gray-200 dark:border-[#3a3a3f] rounded-full text-sm font-medium text-gray-700 dark:text-[#e3e3e3] bg-gray-50 dark:bg-[#282a2c]">{{ aiBrandData.banner_texts.cta_text }}</span>
                  </div>
                </div>
              </div>

              <!-- Sobre Nosotros -->
              <div v-if="aiBrandData.about_us" class="bg-white dark:bg-[#1e1f20] rounded-xl border border-gray-100 dark:border-[#2a2a30] overflow-hidden shadow-[0_2px_10px_-3px_rgba(0,0,0,0.05)] dark:shadow-[0_2px_12px_-2px_rgba(0,0,0,0.3)] transition-shadow duration-200">
                <div class="px-6 py-5 border-b border-gray-100 dark:border-[#2a2a30]">
                  <p class="text-xs font-bold tracking-widest text-[#9aa0a6] uppercase mb-0.5">Historia</p>
                  <h3 class="text-xl font-semibold text-[#1e1f20] dark:text-[#e3e3e3]">Sobre Nosotros</h3>
                </div>
                <div class="p-6">
                  <p class="text-base text-[#5f6368] dark:text-[#9aa0a6] leading-relaxed whitespace-pre-line">{{ aiBrandData.about_us }}</p>
                </div>
              </div>

              <!-- Aplicar Todo -->
              <div class="flex items-center justify-between py-1">
                <p v-if="aiBrandData.generated_at" class="text-sm text-[#9aa0a6]">
                  Generado: {{ new Date(aiBrandData.generated_at).toLocaleString('es-CO') }}
                </p>
                <button
                  @click="applyAllAiSettings"
                  class="px-5 py-2.5 bg-[#1e1f20] dark:bg-[#e3e3e3] hover:bg-black dark:hover:bg-white text-white dark:text-[#1e1f20] text-sm font-medium rounded-xl transition-all flex items-center gap-2"
                >
                  <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                  </svg>
                  Aplicar Todo y Guardar
                </button>
              </div>

            </template>

          </div>
          <!-- /IDENTIDAD VISUAL -->

          <!-- SECCIÓN: IA MARCA - Motor de Diseño Adaptativo -->
          <div v-if="activeTab === 'ai-brand'" class="space-y-6 animate-fade-in">

            <!-- TARJETA 1: Describe tu Negocio -->
            <div class="bg-white dark:bg-[#1e1f20] rounded-2xl border border-[#e8eaed] dark:border-[#3a3a3f] overflow-hidden">
              <div class="px-6 py-4 border-b border-[#e8eaed] dark:border-[#3a3a3f]">
                <div class="flex items-center gap-2">
                  <svg class="w-5 h-5 text-[#1a73e8] dark:text-[#8ab4f8]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09zM18.259 8.715L18 9.75l-.259-1.035a3.375 3.375 0 00-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 002.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 002.455 2.456L21.75 6l-1.036.259a3.375 3.375 0 00-2.455 2.456z" />
                  </svg>
                  <h3 class="text-base font-medium text-[#1e1f20] dark:text-[#e3e3e3]">Describe tu Negocio</h3>
                </div>
                <p class="text-sm text-[#5f6368] dark:text-[#9aa0a6] mt-1">Cuéntanos sobre tu tienda y la IA creará una identidad de marca completa</p>
              </div>
              
              <div class="p-6 space-y-4">
                <div class="relative">
                  <textarea 
                    v-model="aiBrandDescription"
                    rows="5"
                    placeholder="Ej: Vendo ropa casual para mujer, mi estilo es boho chic y minimalista. Me enfoco en telas naturales y colores tierra. Mi público son mujeres de 25-40 años que buscan comodidad con estilo..."
                    class="w-full px-4 py-3 rounded-2xl border border-[#e8eaed] dark:border-[#3a3a3f] bg-[#f8f9fa] dark:bg-[#282a2c] text-sm text-[#1e1f20] dark:text-[#e3e3e3] placeholder-[#5f6368] dark:placeholder-[#9aa0a6] focus:outline-none focus:ring-2 focus:ring-[#1a73e8] dark:focus:ring-[#8ab4f8] focus:border-transparent resize-none"
                    :disabled="isGeneratingBrand"
                  ></textarea>
                  <span class="absolute bottom-3 right-3 text-[10px] text-[#9aa0a6]">{{ aiBrandDescription.length }}/2000</span>
                </div>

                <div class="flex items-center gap-3">
                  <!-- Botón Generar -->
                  <button 
                    @click="generateAiBrand"
                    :disabled="isGeneratingBrand || aiBrandDescription.trim().length < 10"
                    class="flex-1 px-6 py-3 bg-[#1a73e8] hover:bg-[#1557b0] text-white text-sm font-medium rounded-full transition-all duration-200 flex items-center justify-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed"
                  >
                    <svg v-if="!isGeneratingBrand" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09z" />
                    </svg>
                    <svg v-else class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                      <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                      <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    {{ isGeneratingBrand ? aiGenerationProgress || 'Generando...' : 'Generar Identidad con IA' }}
                  </button>

                  <!-- Botón Micrófono -->
                  <button 
                    @click="isRecordingVoice ? stopVoiceRecording() : startVoiceRecording()"
                    class="w-12 h-12 rounded-full flex items-center justify-center transition-all duration-200 flex-shrink-0"
                    :class="isRecordingVoice 
                      ? 'bg-[#ea4335] text-white animate-pulse shadow-lg shadow-[#ea4335]/30' 
                      : 'bg-[#f8f9fa] dark:bg-[#282a2c] text-[#5f6368] dark:text-[#9aa0a6] hover:bg-[#e8eaed] dark:hover:bg-[#3a3a3f] border border-[#e8eaed] dark:border-[#3a3a3f]'"
                    :title="isRecordingVoice ? 'Detener grabación' : 'Dictar descripción'"
                  >
                    <svg v-if="!isRecordingVoice" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M12 18.75a6 6 0 006-6v-1.5m-6 7.5a6 6 0 01-6-6v-1.5m6 7.5v3.75m-3.75 0h7.5M12 15.75a3 3 0 01-3-3V4.5a3 3 0 116 0v8.25a3 3 0 01-3 3z" />
                    </svg>
                    <svg v-else class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M5.25 7.5A2.25 2.25 0 017.5 5.25h9a2.25 2.25 0 012.25 2.25v9a2.25 2.25 0 01-2.25 2.25h-9a2.25 2.25 0 01-2.25-2.25v-9z" />
                    </svg>
                  </button>
                </div>

                <p v-if="isRecordingVoice" class="text-xs text-[#ea4335] dark:text-[#f28b82] flex items-center gap-1.5">
                  <span class="w-2 h-2 bg-[#ea4335] rounded-full animate-pulse"></span>
                  Grabando... Habla sobre tu negocio y presiona el botón rojo para terminar.
                </p>
              </div>
            </div>

            <!-- RESULTADOS DE IA (Solo si hay datos generados) -->
            <template v-if="aiBrandData">
              
              <!-- TARJETA 2: Paleta de Colores Generada -->
              <div v-if="aiBrandData.color_palette" class="bg-white dark:bg-[#1e1f20] rounded-2xl border border-[#e8eaed] dark:border-[#3a3a3f] overflow-hidden">
                <div class="px-6 py-4 border-b border-[#e8eaed] dark:border-[#3a3a3f] flex items-center justify-between">
                  <div>
                    <h3 class="text-base font-medium text-[#1e1f20] dark:text-[#e3e3e3]">Paleta de Colores</h3>
                    <p class="text-sm text-[#5f6368] dark:text-[#9aa0a6] mt-0.5">Generada por IA para tu identidad de marca</p>
                  </div>
                  <button 
                    @click="applyAiColors"
                    class="px-4 py-2 bg-[#1a73e8] hover:bg-[#1557b0] text-white text-xs font-medium rounded-full transition-all"
                  >
                    Aplicar Color
                  </button>
                </div>
                <div class="p-6">
                  <div class="grid grid-cols-6 gap-3">
                    <div v-for="(color, key) in aiBrandData.color_palette" :key="key" class="text-center">
                      <div 
                        class="w-full aspect-square rounded-2xl border-2 border-[#e8eaed] dark:border-[#3a3a3f] mb-2 cursor-pointer hover:scale-105 transition-transform"
                        :style="{ backgroundColor: color }"
                        :title="color"
                        @click="navigator.clipboard.writeText(color)"
                      ></div>
                      <p class="text-[9px] font-medium text-[#5f6368] dark:text-[#9aa0a6] uppercase tracking-wide">{{ key.replace('_', ' ') }}</p>
                      <p class="text-[10px] font-mono text-[#1e1f20] dark:text-[#e3e3e3]">{{ color }}</p>
                    </div>
                  </div>
                </div>
              </div>

              <!-- TARJETA 3: Tipografías -->
              <div v-if="aiBrandData.fonts" class="bg-white dark:bg-[#1e1f20] rounded-2xl border border-[#e8eaed] dark:border-[#3a3a3f] overflow-hidden">
                <div class="px-6 py-4 border-b border-[#e8eaed] dark:border-[#3a3a3f]">
                  <h3 class="text-base font-medium text-[#1e1f20] dark:text-[#e3e3e3]">Tipografías Recomendadas</h3>
                  <p class="text-sm text-[#5f6368] dark:text-[#9aa0a6] mt-0.5">Par de fuentes seleccionado para tu marca</p>
                </div>
                <div class="p-6">
                  <div class="grid grid-cols-2 gap-6">
                    <div class="p-4 rounded-2xl bg-[#f8f9fa] dark:bg-[#282a2c] border border-[#e8eaed] dark:border-[#3a3a3f]">
                      <p class="text-[10px] font-medium text-[#5f6368] dark:text-[#9aa0a6] uppercase tracking-wide mb-2">Títulos</p>
                      <p class="text-2xl text-[#1e1f20] dark:text-[#e3e3e3]" :style="{ fontFamily: aiBrandData.fonts.heading + ', serif' }">
                        {{ aiBrandData.fonts.heading }}
                      </p>
                    </div>
                    <div class="p-4 rounded-2xl bg-[#f8f9fa] dark:bg-[#282a2c] border border-[#e8eaed] dark:border-[#3a3a3f]">
                      <p class="text-[10px] font-medium text-[#5f6368] dark:text-[#9aa0a6] uppercase tracking-wide mb-2">Cuerpo de texto</p>
                      <p class="text-base text-[#1e1f20] dark:text-[#e3e3e3]" :style="{ fontFamily: aiBrandData.fonts.body + ', sans-serif' }">
                        {{ aiBrandData.fonts.body }}
                      </p>
                    </div>
                  </div>
                  <p v-if="aiBrandData.fonts.style_rationale" class="text-xs text-[#5f6368] dark:text-[#9aa0a6] mt-4 italic">
                    {{ aiBrandData.fonts.style_rationale }}
                  </p>
                </div>
              </div>

              <!-- TARJETA 4: Plantilla Recomendada -->
              <div v-if="aiBrandData.recommended_template" class="bg-white dark:bg-[#1e1f20] rounded-2xl border border-[#e8eaed] dark:border-[#3a3a3f] overflow-hidden">
                <div class="px-6 py-4 border-b border-[#e8eaed] dark:border-[#3a3a3f] flex items-center justify-between">
                  <div>
                    <h3 class="text-base font-medium text-[#1e1f20] dark:text-[#e3e3e3]">Plantilla Recomendada</h3>
                    <p class="text-sm text-[#5f6368] dark:text-[#9aa0a6] mt-0.5">
                      La IA recomienda: <span class="font-medium text-[#1a73e8] dark:text-[#8ab4f8]">
                        {{ aiBrandData.recommended_template === 'visual-story' ? 'Historia Visual' : aiBrandData.recommended_template === 'speed-market' ? 'Mercado Rápido' : aiBrandData.recommended_template === 'urban-street' ? 'Urban Streetwear' : 'Cuadrícula Moderna' }}
                      </span>
                    </p>
                  </div>
                  <button 
                    @click="applyAiTemplate"
                    class="px-4 py-2 bg-[#1a73e8] hover:bg-[#1557b0] text-white text-xs font-medium rounded-full transition-all"
                  >
                    Aplicar Plantilla
                  </button>
                </div>
              </div>

              <!-- TARJETA 5: Textos del Banner -->
              <div v-if="aiBrandData.banner_texts" class="bg-white dark:bg-[#1e1f20] rounded-2xl border border-[#e8eaed] dark:border-[#3a3a3f] overflow-hidden">
                <div class="px-6 py-4 border-b border-[#e8eaed] dark:border-[#3a3a3f]">
                  <h3 class="text-base font-medium text-[#1e1f20] dark:text-[#e3e3e3]">Textos del Banner</h3>
                  <p class="text-sm text-[#5f6368] dark:text-[#9aa0a6] mt-0.5">Copy profesional generado para tu banner principal</p>
                </div>
                <div class="p-6 space-y-4">
                  <div class="relative rounded-2xl overflow-hidden" :style="{ backgroundColor: aiBrandData.color_palette?.primary || '#1a1a1a' }">
                    <div class="p-8 text-center">
                      <h2 class="text-2xl font-light text-white mb-2" style="text-shadow: 0 2px 12px rgba(0,0,0,0.3);">
                        {{ aiBrandData.banner_texts.headline }}
                      </h2>
                      <p class="text-sm text-white/80 tracking-wider uppercase">
                        {{ aiBrandData.banner_texts.subheadline }}
                      </p>
                      <span v-if="aiBrandData.banner_texts.cta_text" class="inline-block mt-4 px-6 py-2 bg-white text-gray-900 text-xs font-semibold uppercase tracking-wide rounded-full">
                        {{ aiBrandData.banner_texts.cta_text }}
                      </span>
                    </div>
                  </div>
                </div>
              </div>

              <!-- TARJETA 6: Sobre Nosotros -->
              <div v-if="aiBrandData.about_us" class="bg-white dark:bg-[#1e1f20] rounded-2xl border border-[#e8eaed] dark:border-[#3a3a3f] overflow-hidden">
                <div class="px-6 py-4 border-b border-[#e8eaed] dark:border-[#3a3a3f]">
                  <h3 class="text-base font-medium text-[#1e1f20] dark:text-[#e3e3e3]">Nuestra Historia</h3>
                  <p class="text-sm text-[#5f6368] dark:text-[#9aa0a6] mt-0.5">Sección "Sobre Nosotros" generada por IA</p>
                </div>
                <div class="p-6">
                  <p class="text-sm text-[#1e1f20] dark:text-[#e3e3e3] leading-relaxed whitespace-pre-line">
                    {{ aiBrandData.about_us }}
                  </p>
                </div>
              </div>

              <!-- TARJETA 7: Mensajes de Valor + Anuncios -->
              <div class="grid grid-cols-2 gap-4">
                <div v-if="aiBrandData.value_messages" class="bg-white dark:bg-[#1e1f20] rounded-2xl border border-[#e8eaed] dark:border-[#3a3a3f] overflow-hidden">
                  <div class="px-5 py-3 border-b border-[#e8eaed] dark:border-[#3a3a3f]">
                    <h4 class="text-sm font-medium text-[#1e1f20] dark:text-[#e3e3e3]">Mensajes de Valor</h4>
                  </div>
                  <div class="p-5 space-y-2">
                    <div v-for="(msg, i) in aiBrandData.value_messages" :key="'val-'+i" class="flex items-start gap-2">
                      <span class="w-5 h-5 rounded-full bg-[#e6f4ea] dark:bg-[#1e8e3e]/20 flex items-center justify-center flex-shrink-0 mt-0.5">
                        <svg class="w-3 h-3 text-[#1e8e3e]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                        </svg>
                      </span>
                      <span class="text-xs text-[#1e1f20] dark:text-[#e3e3e3]">{{ msg }}</span>
                    </div>
                  </div>
                </div>

                <div v-if="aiBrandData.announcements" class="bg-white dark:bg-[#1e1f20] rounded-2xl border border-[#e8eaed] dark:border-[#3a3a3f] overflow-hidden">
                  <div class="px-5 py-3 border-b border-[#e8eaed] dark:border-[#3a3a3f]">
                    <h4 class="text-sm font-medium text-[#1e1f20] dark:text-[#e3e3e3]">Barra de Anuncios</h4>
                  </div>
                  <div class="p-5 space-y-2">
                    <div v-for="(ann, i) in aiBrandData.announcements" :key="'ann-'+i" class="flex items-center gap-2">
                      <span class="w-1.5 h-1.5 rounded-full bg-[#1a73e8] dark:bg-[#8ab4f8] flex-shrink-0"></span>
                      <span class="text-xs text-[#1e1f20] dark:text-[#e3e3e3]">{{ ann }}</span>
                    </div>
                  </div>
                </div>
              </div>

              <!-- TARJETA 8: Mensajes de Venta Cruzada -->
              <div v-if="aiBrandData.cross_sell_messages" class="bg-white dark:bg-[#1e1f20] rounded-2xl border border-[#e8eaed] dark:border-[#3a3a3f] overflow-hidden">
                <div class="px-6 py-4 border-b border-[#e8eaed] dark:border-[#3a3a3f]">
                  <h3 class="text-base font-medium text-[#1e1f20] dark:text-[#e3e3e3]">Mensajes de Recomendación</h3>
                  <p class="text-sm text-[#5f6368] dark:text-[#9aa0a6] mt-0.5">Venta cruzada profesional para tu catálogo</p>
                </div>
                <div class="p-6 space-y-3">
                  <div v-for="(msg, i) in aiBrandData.cross_sell_messages" :key="'cross-'+i" 
                       class="px-4 py-3 rounded-xl bg-[#f8f9fa] dark:bg-[#282a2c] border border-[#e8eaed] dark:border-[#3a3a3f]">
                    <p class="text-sm text-[#1e1f20] dark:text-[#e3e3e3]">{{ msg }}</p>
                  </div>
                </div>
              </div>

              <!-- Botón Aplicar Todo + Timestamp -->
              <div class="flex items-center justify-between">
                <p v-if="aiBrandData.generated_at" class="text-[10px] text-[#9aa0a6]">
                  Generado: {{ new Date(aiBrandData.generated_at).toLocaleString('es-CO') }}
                </p>
                <button 
                  @click="applyAllAiSettings"
                  class="px-6 py-3 bg-[#1e8e3e] hover:bg-[#168936] text-white text-sm font-medium rounded-full transition-all duration-200 flex items-center gap-2"
                >
                  <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                  </svg>
                  Aplicar Todo y Guardar
                </button>
              </div>

            </template>

          </div>

          <!-- SECCIÓN: DISEÑO - Estilo Gemini -->
          <div v-if="activeTab === 'identity'" class="space-y-6 animate-fade-in">
            
            <!-- TARJETA 1: Logo y Color Primario - Gemini -->
            <div class="bg-white dark:bg-[#1e1f20] rounded-2xl border border-[#e8eaed] dark:border-[#3a3a3f] overflow-hidden">
              <div class="px-6 py-4 border-b border-[#e8eaed] dark:border-[#3a3a3f]">
                <h3 class="text-base font-medium text-[#1e1f20] dark:text-[#e3e3e3]">Identidad Visual</h3>
                <p class="text-sm text-[#5f6368] dark:text-[#9aa0a6] mt-0.5">Logo y color principal de tu tienda</p>
              </div>
              
              <div class="p-6">
                <div class="grid grid-cols-2 gap-6">
                  
                  <!-- Logo Upload - Gemini -->
                  <div class="space-y-3">
                    <label class="block text-xs font-medium uppercase tracking-wide text-[#5f6368] dark:text-[#9aa0a6]">Logo de la Tienda</label>
                    <div 
                      @click="triggerFileUpload('logo')"
                      class="relative w-full h-32 border-2 border-dashed border-[#dadce0] dark:border-[#3a3a3f] rounded-2xl hover:border-[#1a73e8] dark:hover:border-[#8ab4f8] hover:bg-[#e8f0fe] dark:hover:bg-[#1a73e8]/10 transition-all cursor-pointer group bg-[#f8f9fa] dark:bg-[#282a2c] flex items-center justify-center"
                    >
                      <div v-if="config.brandIdentity.logo" class="absolute inset-0 p-3 flex items-center justify-center">
                        <img :src="config.brandIdentity.logo" class="max-w-full max-h-full object-contain" />
                        <div class="absolute inset-0 bg-black/60 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center gap-2 rounded-2xl">
                          <button 
                            @click.stop="config.brandIdentity.logo = ''"
                            class="text-white text-xs font-medium px-3 py-2 bg-[#ea4335] hover:bg-[#d33426] rounded-full transition-colors flex items-center gap-1.5"
                          >
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                              <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                            Eliminar
                          </button>
                          <span class="text-white text-xs font-medium px-3 py-2 bg-white/20 rounded-full">Cambiar</span>
                        </div>
                      </div>
                      <div v-else class="text-[#5f6368] dark:text-[#9aa0a6] group-hover:text-[#1a73e8] dark:group-hover:text-[#8ab4f8] transition-colors flex flex-col items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <div class="text-center">
                          <span class="text-xs font-medium block">Subir logo</span>
                          <span class="text-[10px] text-[#5f6368] dark:text-[#9aa0a6]">PNG, JPG o SVG</span>
                        </div>
                      </div>
                      <input type="file" ref="logoInput" class="hidden" accept="image/*" @change="(e) => handleFileUpload(e, 'logo')" />
                    </div>
                  </div>
                  
                  <!-- Color Picker - Gemini -->
                  <div class="space-y-3">
                    <label class="block text-xs font-medium uppercase tracking-wide text-[#5f6368] dark:text-[#9aa0a6]">Color Primario</label>
                    <div class="flex items-center gap-3 p-4 border border-[#e8eaed] dark:border-[#3a3a3f] rounded-2xl bg-white dark:bg-[#282a2c] hover:border-[#1a73e8] dark:hover:border-[#8ab4f8] transition-all">
                      <input 
                        type="color" 
                        v-model="config.brandIdentity.primaryColor"
                        class="w-12 h-12 rounded-xl cursor-pointer border border-[#e8eaed] dark:border-[#3a3a3f]"
                      />
                      <div class="flex-1">
                        <input 
                          type="text"
                          v-model="config.brandIdentity.primaryColor"
                          class="w-full px-3 py-2 rounded-xl border border-[#e8eaed] dark:border-[#3a3a3f] text-sm font-mono text-[#1e1f20] dark:text-[#e3e3e3] bg-[#f8f9fa] dark:bg-[#1e1f20] focus:outline-none focus:ring-2 focus:ring-[#1a73e8] dark:focus:ring-[#8ab4f8] uppercase"
                          placeholder="#10B981"
                        />
                        <p class="text-xs text-[#5f6368] dark:text-[#9aa0a6] mt-1.5">Este color se aplicará en botones y acentos</p>
                      </div>
                    </div>
                  </div>
                  
                </div>
              </div>
            </div>
            
            <!-- TARJETA 2: Selección de Plantilla - Gemini -->
            <div class="bg-white dark:bg-[#1e1f20] rounded-2xl border border-[#e8eaed] dark:border-[#3a3a3f] overflow-hidden">
              <div class="px-6 py-4 border-b border-[#e8eaed] dark:border-[#3a3a3f]">
                <h3 class="text-base font-medium text-[#1e1f20] dark:text-[#e3e3e3]">Plantilla de Diseño</h3>
                <p class="text-sm text-[#5f6368] dark:text-[#9aa0a6] mt-0.5">Elige el estilo visual de tu catálogo web</p>
              </div>
              
              <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                  
                  <!-- Visual Story (Solo para Fashion) - Gemini -->
                  <button 
                    v-if="isFashionStore"
                    @click="config.brandIdentity.template = 'visual-story'"
                    class="group relative p-5 rounded-2xl border transition-all text-left"
                    :class="config.brandIdentity.template === 'visual-story' 
                      ? 'border-[#1a73e8] dark:border-[#8ab4f8] bg-[#e8f0fe] dark:bg-[#1a73e8]/15' 
                      : 'border-[#e8eaed] dark:border-[#3a3a3f] hover:border-[#1a73e8]/50 dark:hover:border-[#8ab4f8]/50 bg-white dark:bg-[#282a2c]'"
                  >
                    <div class="flex flex-col items-center text-center space-y-3">
                      <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-[#e8eaed] to-[#f0f4f9] dark:from-[#3a3a3f] dark:to-[#282a2c] flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-7 h-7 text-[#5f6368] dark:text-[#9aa0a6]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                      </div>
                      <div>
                        <div class="text-sm font-medium" :class="config.brandIdentity.template === 'visual-story' ? 'text-[#1a73e8] dark:text-[#8ab4f8]' : 'text-[#1e1f20] dark:text-[#e3e3e3]'">
                          Historia Visual
                        </div>
                        <div class="text-xs text-[#5f6368] dark:text-[#9aa0a6] mt-1">Boutique / Gourmet</div>
                      </div>
                      <div v-if="config.brandIdentity.template === 'visual-story'" class="absolute top-3 right-3 w-5 h-5 rounded-full bg-[#1a73e8] dark:bg-[#8ab4f8] flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 text-white dark:text-[#1e1f20]" viewBox="0 0 20 20" fill="currentColor">
                          <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                        </svg>
                      </div>
                    </div>
                  </button>

                  <!-- Speed Market - Gemini -->
                  <button 
                    @click="config.brandIdentity.template = 'speed-market'"
                    class="group relative p-5 rounded-2xl border transition-all text-left"
                    :class="config.brandIdentity.template === 'speed-market' 
                      ? 'border-[#1a73e8] dark:border-[#8ab4f8] bg-[#e8f0fe] dark:bg-[#1a73e8]/15' 
                      : 'border-[#e8eaed] dark:border-[#3a3a3f] hover:border-[#1a73e8]/50 dark:hover:border-[#8ab4f8]/50 bg-white dark:bg-[#282a2c]'"
                  >
                    <div class="flex flex-col items-center text-center space-y-3">
                      <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-[#e8eaed] to-[#f0f4f9] dark:from-[#3a3a3f] dark:to-[#282a2c] flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-7 h-7 text-[#5f6368] dark:text-[#9aa0a6]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                      </div>
                      <div>
                        <div class="text-sm font-medium" :class="config.brandIdentity.template === 'speed-market' ? 'text-[#1a73e8] dark:text-[#8ab4f8]' : 'text-[#1e1f20] dark:text-[#e3e3e3]'">
                          Mercado Rápido
                        </div>
                        <div class="text-xs text-[#5f6368] dark:text-[#9aa0a6] mt-1">Supermercado / Rápido</div>
                      </div>
                      <div v-if="config.brandIdentity.template === 'speed-market'" class="absolute top-3 right-3 w-5 h-5 rounded-full bg-[#1a73e8] dark:bg-[#8ab4f8] flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 text-white dark:text-[#1e1f20]" viewBox="0 0 20 20" fill="currentColor">
                          <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                        </svg>
                      </div>
                    </div>
                  </button>

                  <!-- Modern Grid - Gemini -->
                  <button 
                    @click="config.brandIdentity.template = 'modern-grid'"
                    class="group relative p-5 rounded-2xl border transition-all text-left"
                    :class="config.brandIdentity.template === 'modern-grid' 
                      ? 'border-[#1a73e8] dark:border-[#8ab4f8] bg-[#e8f0fe] dark:bg-[#1a73e8]/15' 
                      : 'border-[#e8eaed] dark:border-[#3a3a3f] hover:border-[#1a73e8]/50 dark:hover:border-[#8ab4f8]/50 bg-white dark:bg-[#282a2c]'"
                  >
                    <div class="flex flex-col items-center text-center space-y-3">
                      <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-[#e8eaed] to-[#f0f4f9] dark:from-[#3a3a3f] dark:to-[#282a2c] flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-7 h-7 text-[#5f6368] dark:text-[#9aa0a6]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M4 5a1 1 0 011-1h4a1 1 0 011 1v7a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM14 5a1 1 0 011-1h4a1 1 0 011 1v7a1 1 0 01-1 1h-4a1 1 0 01-1-1V5zM4 16a1 1 0 011-1h4a1 1 0 011 1v3a1 1 0 01-1 1H5a1 1 0 01-1-1v-3zM14 16a1 1 0 011-1h4a1 1 0 011 1v3a1 1 0 01-1 1h-4a1 1 0 01-1-1v-3z" />
                        </svg>
                      </div>
                      <div>
                        <div class="text-sm font-medium" :class="config.brandIdentity.template === 'modern-grid' ? 'text-[#1a73e8] dark:text-[#8ab4f8]' : 'text-[#1e1f20] dark:text-[#e3e3e3]'">
                          Cuadrícula Moderna
                        </div>
                        <div class="text-xs text-[#5f6368] dark:text-[#9aa0a6] mt-1">Clásico / Versátil</div>
                      </div>
                      <div v-if="config.brandIdentity.template === 'modern-grid'" class="absolute top-3 right-3 w-5 h-5 rounded-full bg-[#1a73e8] dark:bg-[#8ab4f8] flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 text-white dark:text-[#1e1f20]" viewBox="0 0 20 20" fill="currentColor">
                          <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                        </svg>
                      </div>
                    </div>
                  </button>

                  <!-- Urban Street - PlantillaUrbana01 -->
                  <button 
                    @click="config.brandIdentity.template = 'urban-street'"
                    class="group relative p-5 rounded-2xl border transition-all text-left"
                    :class="config.brandIdentity.template === 'urban-street' 
                      ? 'border-[#1a73e8] dark:border-[#8ab4f8] bg-[#e8f0fe] dark:bg-[#1a73e8]/15' 
                      : 'border-[#e8eaed] dark:border-[#3a3a3f] hover:border-[#1a73e8]/50 dark:hover:border-[#8ab4f8]/50 bg-white dark:bg-[#282a2c]'"
                  >
                    <div class="flex flex-col items-center text-center space-y-3">
                      <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-[#1e1f20] to-[#3a3a3f] dark:from-[#0a0a0a] dark:to-[#282a2c] flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-7 h-7 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09zM18.259 8.715L18 9.75l-.259-1.035a3.375 3.375 0 00-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 002.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 002.455 2.456L21.75 6l-1.036.259a3.375 3.375 0 00-2.455 2.456z" />
                        </svg>
                      </div>
                      <div>
                        <div class="text-sm font-medium" :class="config.brandIdentity.template === 'urban-street' ? 'text-[#1a73e8] dark:text-[#8ab4f8]' : 'text-[#1e1f20] dark:text-[#e3e3e3]'">
                          Urban Streetwear
                        </div>
                        <div class="text-xs text-[#5f6368] dark:text-[#9aa0a6] mt-1">Urbano / Premium</div>
                      </div>
                      <div v-if="config.brandIdentity.template === 'urban-street'" class="absolute top-3 right-3 w-5 h-5 rounded-full bg-[#1a73e8] dark:bg-[#8ab4f8] flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 text-white dark:text-[#1e1f20]" viewBox="0 0 20 20" fill="currentColor">
                          <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                        </svg>
                      </div>
                    </div>
                  </button>
                  
                </div>
              </div>
            </div>
            
            <!-- TARJETA 3: Banner Promocional (Solo Fashion) - Gemini -->
            <div v-if="isFashionStore" class="bg-white dark:bg-[#1e1f20] rounded-2xl border border-[#e8eaed] dark:border-[#3a3a3f] overflow-hidden">
              <div class="px-6 py-4 border-b border-[#e8eaed] dark:border-[#3a3a3f]">
                <h3 class="text-base font-medium text-[#1e1f20] dark:text-[#e3e3e3]">Banner Promocional</h3>
                <p class="text-sm text-[#5f6368] dark:text-[#9aa0a6] mt-0.5">Imagen destacada en la parte superior (exclusivo para tiendas de moda)</p>
              </div>
              
              <div class="p-6">
                <div 
                  @click="triggerFileUpload('banner')"
                  class="relative w-full h-48 border-2 border-dashed border-[#dadce0] dark:border-[#3a3a3f] rounded-2xl hover:border-[#1a73e8] dark:hover:border-[#8ab4f8] hover:bg-[#e8f0fe] dark:hover:bg-[#1a73e8]/10 transition-all cursor-pointer group bg-[#f8f9fa] dark:bg-[#282a2c] flex items-center justify-center overflow-hidden"
                >
                  <div v-if="config.brandIdentity.banner" class="absolute inset-0 p-2">
                    <img :src="config.brandIdentity.banner" class="w-full h-full object-cover rounded-xl" />
                    <div class="absolute inset-0 bg-black/60 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center gap-3 rounded-xl">
                      <button 
                        @click.stop="config.brandIdentity.banner = ''"
                        class="text-white text-sm font-medium px-4 py-2.5 bg-[#ea4335] hover:bg-[#d33426] rounded-full transition-colors flex items-center gap-2"
                      >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                        Eliminar Banner
                      </button>
                      <span class="text-white text-sm font-medium px-4 py-2.5 bg-white/20 rounded-full">Cambiar Imagen</span>
                    </div>
                  </div>
                  <div v-else class="text-[#5f6368] dark:text-[#9aa0a6] group-hover:text-[#1a73e8] dark:group-hover:text-[#8ab4f8] transition-colors flex flex-col items-center gap-3">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <div class="text-center">
                      <span class="text-sm font-medium block">Subir banner promocional</span>
                      <span class="text-xs text-[#5f6368] dark:text-[#9aa0a6] mt-1 block">Recomendado: 1200x400px</span>
                    </div>
                  </div>
                  <input type="file" ref="bannerInput" class="hidden" accept="image/*" @change="(e) => handleFileUpload(e, 'banner')" />
                </div>
              </div>
            </div>

          </div>

          <!-- SECCIÓN: PRODUCTOS - Estilo Gemini -->
          <div v-else-if="activeTab === 'catalog'" class="space-y-6 animate-fade-in pb-8">
            
            <section>
              <div class="mb-4">
                <h3 class="text-base font-medium text-[#1e1f20] dark:text-[#e3e3e3]">Visibilidad del Catálogo</h3>
                <p class="text-xs text-[#5f6368] dark:text-[#9aa0a6] mt-1">Controla qué categorías y productos se muestran en tu tienda online.</p>
              </div>
              
              <!-- Advertencia si no hay categorías cargadas -->
              <div v-if="availableCategories.length === 0" 
                   class="bg-[#e8f0fe] dark:bg-[#1a73e8]/15 border-l-4 border-[#1a73e8] dark:border-[#8ab4f8] rounded-xl p-4 flex items-start gap-3">
                <svg class="w-5 h-5 text-[#1a73e8] dark:text-[#8ab4f8] flex-shrink-0 animate-spin" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <p class="text-sm text-[#1a73e8] dark:text-[#8ab4f8]">Cargando categorías desde la base de datos...</p>
              </div>
              
              <div class="bg-white dark:bg-[#1e1f20] rounded-2xl p-5 border border-[#e8eaed] dark:border-[#3a3a3f]">
                <label class="block text-xs font-medium uppercase tracking-wide text-[#5f6368] dark:text-[#9aa0a6] mb-3">Categorías Visibles</label>
                <div class="flex flex-wrap gap-2">
                  <button 
                    v-for="category in availableCategories" 
                    :key="category.id"
                    @click="toggleCategory(category.id)"
                    class="px-3 py-1.5 rounded-full text-xs font-medium transition-all duration-150 flex items-center gap-2"
                    :class="config.inventoryVisibility.visibleCategories.includes(category.id) 
                      ? 'bg-[#e8f0fe] dark:bg-[#1a73e8]/20 text-[#1a73e8] dark:text-[#8ab4f8] ring-2 ring-[#1a73e8]/30 dark:ring-[#8ab4f8]/30' 
                      : 'bg-[#f8f9fa] dark:bg-[#282a2c] text-[#5f6368] dark:text-[#9aa0a6] hover:bg-[#e8eaed] dark:hover:bg-[#3a3a3f]'"
                  >
                    <span>{{ category.name }}</span>
                    <div v-if="config.inventoryVisibility.visibleCategories.includes(category.id)" class="bg-[#1a73e8] dark:bg-[#8ab4f8] rounded-full p-0.5">
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-2 w-2 text-white dark:text-[#1e1f20]" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                      </svg>
                    </div>
                  </button>
                </div>
              </div>
            </section>

            <section>
              <div class="bg-white dark:bg-[#1e1f20] rounded-2xl p-5 border border-[#e8eaed] dark:border-[#3a3a3f] flex items-center justify-between">
                <div>
                  <h3 class="text-sm font-medium text-[#1e1f20] dark:text-[#e3e3e3]">Ocultar productos sin stock</h3>
                  <p class="text-xs text-[#5f6368] dark:text-[#9aa0a6] mt-0.5">No mostrar productos agotados en el catálogo</p>
                </div>
                <button 
                  @click="config.inventoryVisibility.hideOutOfStock = !config.inventoryVisibility.hideOutOfStock"
                  class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors focus:outline-none"
                  :class="config.inventoryVisibility.hideOutOfStock ? 'bg-[#1a73e8]' : 'bg-[#dadce0] dark:bg-[#5f6368]'"
                >
                  <span
                    class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform"
                    :class="config.inventoryVisibility.hideOutOfStock ? 'translate-x-6' : 'translate-x-1'"
                  />
                </button>
              </div>
            </section>
            
          </div>

          <!-- SECCIÓN: WHATSAPP - Estilo Gemini -->
          <div v-else-if="activeTab === 'orders'" class="space-y-6 animate-fade-in pb-8">
            
            <section>
              <div class="mb-4">
                <h3 class="text-base font-medium text-[#1e1f20] dark:text-[#e3e3e3]">Configuración de Pedidos</h3>
                <p class="text-xs text-[#5f6368] dark:text-[#9aa0a6] mt-1">Define cómo tus clientes realizarán pedidos a través de WhatsApp.</p>
              </div>
              
              <!-- Grid 2 Columnas: Número + País/Horario - Gemini -->
              <div class="grid grid-cols-2 gap-6">
                
                <div class="bg-white dark:bg-[#1e1f20] rounded-2xl p-4 border border-[#e8eaed] dark:border-[#3a3a3f]">
                  <label class="block text-xs font-medium uppercase tracking-wide text-[#5f6368] dark:text-[#9aa0a6] mb-2">Número de WhatsApp</label>
                  <input 
                    type="text" 
                    v-model="config.ordersConfig.whatsappNumber"
                    placeholder="+57 300 123 4567"
                    class="w-full h-10 px-3 rounded-xl border border-[#e8eaed] dark:border-[#3a3a3f] bg-[#f8f9fa] dark:bg-[#282a2c] text-sm text-[#1e1f20] dark:text-[#e3e3e3] placeholder-[#5f6368] dark:placeholder-[#9aa0a6] focus:outline-none focus:ring-2 focus:ring-[#1a73e8] dark:focus:ring-[#8ab4f8] focus:border-transparent"
                  />
                  <p class="text-[10px] text-[#5f6368] dark:text-[#9aa0a6] mt-2">Es el número donde recibirás los pedidos.</p>
                </div>

                <div class="bg-white dark:bg-[#1e1f20] rounded-2xl p-4 border border-[#e8eaed] dark:border-[#3a3a3f] opacity-60">
                  <label class="block text-xs font-medium uppercase tracking-wide text-[#5f6368] dark:text-[#9aa0a6] mb-2">Horario de Atención</label>
                  <input 
                    type="text" 
                    placeholder="Lun-Vie: 9AM - 6PM"
                    class="w-full h-10 px-3 rounded-xl border border-[#e8eaed] dark:border-[#3a3a3f] bg-[#f8f9fa] dark:bg-[#282a2c] text-sm text-[#1e1f20] dark:text-[#e3e3e3] placeholder-[#5f6368] dark:placeholder-[#9aa0a6]"
                    disabled
                  />
                  <p class="text-[10px] text-[#5f6368] dark:text-[#9aa0a6] mt-2">Muestra tu disponibilidad (próximamente).</p>
                </div>
                
              </div>

              <!-- Mensaje Inicial - Full Width - Gemini -->
              <div class="bg-white dark:bg-[#1e1f20] rounded-2xl p-4 border border-[#e8eaed] dark:border-[#3a3a3f] mt-6">
                <label class="block text-xs font-medium uppercase tracking-wide text-[#5f6368] dark:text-[#9aa0a6] mb-2">Mensaje Inicial Personalizado</label>
                <textarea 
                  v-model="config.ordersConfig.customMessage"
                  rows="3"
                  placeholder="Hola, quiero hacer el siguiente pedido:"
                  class="w-full px-3 py-2.5 rounded-xl border border-[#e8eaed] dark:border-[#3a3a3f] bg-[#f8f9fa] dark:bg-[#282a2c] text-sm text-[#1e1f20] dark:text-[#e3e3e3] placeholder-[#5f6368] dark:placeholder-[#9aa0a6] focus:outline-none focus:ring-2 focus:ring-[#1a73e8] dark:focus:ring-[#8ab4f8] focus:border-transparent resize-none"
                ></textarea>
                <p class="text-[10px] text-[#5f6368] dark:text-[#9aa0a6] mt-2">Este mensaje aparecerá automáticamente al iniciar la conversación.</p>
              </div>
              
            </section>
            
          </div>

          <!-- SECCIÓN: REGLAS - Estilo Gemini -->
          <div v-else-if="activeTab === 'rules'" class="space-y-6 animate-fade-in pb-8">
            
            <section>
              <div class="mb-4">
                <h3 class="text-base font-medium text-[#1e1f20] dark:text-[#e3e3e3]">Reglas de Negocio</h3>
                <p class="text-xs text-[#5f6368] dark:text-[#9aa0a6] mt-1">Define los parámetros operativos de tu tienda online.</p>
              </div>
              
              <!-- Grid 2 Columnas: Costo + Mínimo - Gemini -->
              <div class="grid grid-cols-2 gap-6">
                
                <div class="bg-white dark:bg-[#1e1f20] rounded-2xl p-4 border border-[#e8eaed] dark:border-[#3a3a3f]">
                  <label class="block text-xs font-medium uppercase tracking-wide text-[#5f6368] dark:text-[#9aa0a6] mb-2">Costo de Domicilio</label>
                  <div class="relative">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-[#5f6368] dark:text-[#9aa0a6] text-sm font-medium">$</span>
                    <input 
                      type="number" 
                      v-model="config.businessRules.deliveryCost"
                      class="w-full h-10 pl-7 pr-3 rounded-xl border border-[#e8eaed] dark:border-[#3a3a3f] bg-[#f8f9fa] dark:bg-[#282a2c] text-base font-medium text-[#1e1f20] dark:text-[#e3e3e3] focus:outline-none focus:ring-2 focus:ring-[#1a73e8] dark:focus:ring-[#8ab4f8] focus:border-transparent"
                      placeholder="0"
                    />
                  </div>
                  <p class="text-[10px] text-[#5f6368] dark:text-[#9aa0a6] mt-2">Precio del envío a domicilio.</p>
                </div>

                <div class="bg-white dark:bg-[#1e1f20] rounded-2xl p-4 border border-[#e8eaed] dark:border-[#3a3a3f]">
                  <label class="block text-xs font-medium uppercase tracking-wide text-[#5f6368] dark:text-[#9aa0a6] mb-2">Pedido Mínimo</label>
                  <div class="relative">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-[#5f6368] dark:text-[#9aa0a6] text-sm font-medium">$</span>
                    <input 
                      type="number" 
                      v-model="config.businessRules.minimumOrder"
                      class="w-full h-10 pl-7 pr-3 rounded-xl border border-[#e8eaed] dark:border-[#3a3a3f] bg-[#f8f9fa] dark:bg-[#282a2c] text-base font-medium text-[#1e1f20] dark:text-[#e3e3e3] focus:outline-none focus:ring-2 focus:ring-[#1a73e8] dark:focus:ring-[#8ab4f8] focus:border-transparent"
                      placeholder="0"
                    />
                  </div>
                  <p class="text-[10px] text-[#5f6368] dark:text-[#9aa0a6] mt-2">Valor mínimo para aceptar pedidos.</p>
                </div>
                
              </div>
            </section>

            <section>
              <div class="mb-4">
                <h3 class="text-base font-medium text-[#1e1f20] dark:text-[#e3e3e3]">Integraciones</h3>
                <p class="text-xs text-[#5f6368] dark:text-[#9aa0a6] mt-1">Conecta tu tienda con otros sistemas.</p>
              </div>
              
              <div class="bg-white dark:bg-[#1e1f20] rounded-2xl p-5 border border-[#e8eaed] dark:border-[#3a3a3f] flex items-center justify-between">
                <div>
                  <h3 class="text-sm font-medium text-[#1e1f20] dark:text-[#e3e3e3]">Sincronizar con Caja Registradora</h3>
                  <p class="text-xs text-[#5f6368] dark:text-[#9aa0a6] mt-0.5">Registrar pedidos online automáticamente en el POS</p>
                </div>
                <button 
                  @click="config.businessRules.syncWithCashRegister = !config.businessRules.syncWithCashRegister"
                  class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors focus:outline-none"
                  :class="config.businessRules.syncWithCashRegister ? 'bg-[#1a73e8]' : 'bg-[#dadce0] dark:bg-[#5f6368]'"
                >
                  <span
                    class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform"
                    :class="config.businessRules.syncWithCashRegister ? 'translate-x-6' : 'translate-x-1'"
                  />
                </button>
              </div>
            </section>
            
          </div>
      
      </div> <!-- Cierra p-8 space-y-6 max-w-5xl mx-auto -->
      </div> <!-- Cierra overflow-y-auto scroll area -->
    </main>
    </Transition>
    
    <!-- PREVIEW DERECHO - Solo Vista Móvil (Fija) - Gemini -->
    <Transition
      enter-active-class="transition-all duration-700 ease-out"
      enter-from-class="opacity-0 translate-x-10"
      enter-to-class="opacity-100 translate-x-0"
    >
    <aside v-if="showPreviewPanel" class="flex-shrink-0 w-[440px] bg-white dark:bg-[#1e1f20] border-l border-[#e8eaed] dark:border-[#3a3a3f] flex flex-col overflow-hidden">
      <div class="flex-1 flex flex-col overflow-hidden py-4 px-4">
        <div class="flex flex-col h-full">
          <!-- Preview Header - Gemini -->
          <div class="flex-shrink-0 mb-5 flex items-center justify-between">
            <div class="flex items-center gap-2">
              <span class="w-2 h-2 rounded-full bg-[#1e8e3e] animate-pulse"></span>
              <h3 class="text-[#1e1f20] dark:text-[#e3e3e3] font-medium text-sm">Vista Previa</h3>
            </div>
            
            <button 
              @click="refreshPreview"
              class="text-[#5f6368] dark:text-[#9aa0a6] hover:text-[#1e1f20] dark:hover:text-[#e3e3e3] transition-colors p-2 rounded-full hover:bg-[#f0f4f9] dark:hover:bg-[#282a2c]"
              title="Recargar Vista"
            >
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
              </svg>
            </button>
          </div>
        
          <!-- Marco de Dispositivo Móvil con Iframe Real -->
          <div class="flex-1 flex items-center justify-center overflow-hidden">
            <div 
              class="relative bg-white transition-all duration-300 overflow-hidden isolate rounded-[3rem] shadow-2xl dark:shadow-black/80 flex-shrink-0"
              style="container-type: inline-size; width: 375px; height: 740px; transform: scale(0.95); transform-origin: top center; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.4), 0 0 0 1px rgba(0, 0, 0, 0.1);"
            >
              <!-- Borde exterior del teléfono (marco negro) -->
              <div class="absolute inset-0 rounded-[3rem] border-[14px] border-black pointer-events-none z-50">
                <!-- Notch -->
                <div class="absolute top-0 left-1/2 -translate-x-1/2 w-40 h-7 bg-black rounded-b-3xl -mt-[14px]"></div>
                
                <!-- Botones laterales -->
                <div class="absolute -left-[14px] top-24 w-1 h-12 bg-black rounded-l"></div>
                <div class="absolute -left-[14px] top-40 w-1 h-16 bg-black rounded-l"></div>
                <div class="absolute -right-[14px] top-32 w-1 h-20 bg-black rounded-r"></div>
              </div>
              
              <!-- Pantalla del teléfono - Iframe Real del Catálogo -->
              <div class="w-full h-full overflow-hidden bg-white relative rounded-[2.2rem]" style="isolation: isolate; transform: translateZ(0);">
                <!-- Loading state mientras la config carga (solo en la primera carga) -->
                <div v-if="isLoading" class="w-full h-full flex flex-col items-center justify-center gap-3 bg-white">
                  <div class="w-7 h-7 border-2 border-gray-100 border-t-gray-500 rounded-full animate-spin"></div>
                  <p class="text-[11px] text-gray-400 font-medium tracking-wide">Cargando catálogo...</p>
                </div>
                <!-- Iframe: aparece una sola vez, ya con el caché listo para render instantáneo -->
                <iframe
                  v-else
                  :src="catalogUrl"
                  :key="previewKey"
                  class="w-full h-full border-0"
                  style="width: 375px; height: 740px;"
                  title="Vista Previa del Catálogo"
                ></iframe>
              </div>
            </div>
          </div>
        </div>
      </div>
    </aside>
    </Transition>

  </div> <!-- Cierra flex h-full (configured) -->

  <!-- ======================================================= -->
  <!-- MODAL: Selector de Pares Tipográficos                   -->
  <!-- ======================================================= -->
  <Teleport to="body">
    <Transition
      enter-active-class="transition duration-200 ease-out"
      enter-from-class="opacity-0"
      enter-to-class="opacity-100"
      leave-active-class="transition duration-150 ease-in"
      leave-from-class="opacity-100"
      leave-to-class="opacity-0"
    >
      <div
        v-if="showFontModal"
        class="fixed inset-0 z-[9999] flex items-center justify-center p-4"
        @keydown.escape="showFontModal = false"
      >
        <!-- Backdrop -->
        <div
          class="absolute inset-0 bg-black/55 backdrop-blur-sm"
          @click="showFontModal = false"
        ></div>

        <!-- Modal Panel -->
        <div
          class="relative bg-white dark:bg-[#1e1f20] rounded-3xl shadow-[0_32px_64px_-12px_rgba(0,0,0,0.35)] dark:shadow-[0_32px_64px_-12px_rgba(0,0,0,0.7)] border border-[#e8eaed]/60 dark:border-[#3a3a3f] w-full max-w-2xl max-h-[88vh] flex flex-col overflow-hidden"
          style="animation: modalSlideIn 0.22s cubic-bezier(0.34, 1.56, 0.64, 1) forwards;"
        >
          <!-- Modal Header -->
          <div class="flex-shrink-0 px-7 py-5 border-b border-[#f0f2f5] dark:border-[#3a3a3f] flex items-start justify-between">
            <div>
              <h2 class="text-base font-semibold text-[#1e1f20] dark:text-[#e3e3e3]">Pares Tipográficos</h2>
              <p class="text-[11px] text-[#9aa0a6] mt-0.5">Elige cómo se verán los textos de tu tienda. Verás la fuente aplicada en tiempo real.</p>
            </div>
            <button
              @click="showFontModal = false"
              class="ml-4 w-8 h-8 flex items-center justify-center rounded-full text-[#9aa0a6] hover:text-[#1e1f20] dark:hover:text-[#e3e3e3] hover:bg-[#f0f4f9] dark:hover:bg-[#282a2c] transition-all flex-shrink-0"
            >
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>

          <!-- Modal Body — Font Pair Grid -->
          <div class="flex-1 overflow-y-auto p-6">
            <div class="grid grid-cols-2 gap-3">
              <button
                v-for="pair in fontPairs"
                :key="pair.id"
                @click="applyFontPair(pair)"
                class="group relative text-left p-5 rounded-2xl border-2 transition-all duration-150 hover:shadow-[0_4px_16px_0_rgba(0,0,0,0.1)] dark:hover:shadow-[0_4px_16px_0_rgba(0,0,0,0.35)]"
                :class="(selectedFontPairId === pair.id || (aiBrandData?.fonts?.heading === pair.heading && aiBrandData?.fonts?.body === pair.body))
                  ? 'border-[#1e1f20] dark:border-[#e3e3e3] bg-[#f8f9fa] dark:bg-[#282a2c]'
                  : 'border-[#e8eaed] dark:border-[#3a3a3f] bg-white dark:bg-[#1e1f20] hover:border-[#c0c4c8] dark:hover:border-[#5f6368]'"
              >
                <!-- Selected indicator -->
                <div
                  v-if="selectedFontPairId === pair.id || (aiBrandData?.fonts?.heading === pair.heading && aiBrandData?.fonts?.body === pair.body)"
                  class="absolute top-3 right-3 w-5 h-5 rounded-full bg-[#1e1f20] dark:bg-[#e3e3e3] flex items-center justify-center"
                >
                  <svg class="w-3 h-3 text-white dark:text-[#1e1f20]" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                  </svg>
                </div>

                <!-- Heading preview -->
                <p
                  class="text-[22px] leading-tight text-[#1e1f20] dark:text-[#e3e3e3] mb-2 pr-6 truncate"
                  :style="{ fontFamily: pair.heading + ', serif' }"
                >{{ pair.sampleHeading }}</p>

                <!-- Body preview -->
                <p
                  class="text-[11px] text-[#5f6368] dark:text-[#9aa0a6] leading-relaxed line-clamp-2 mb-4"
                  :style="{ fontFamily: pair.body + ', sans-serif' }"
                >{{ pair.sampleBody }}</p>

                <!-- Meta -->
                <div class="flex items-center justify-between">
                  <div>
                    <p class="text-[9px] font-semibold text-[#9aa0a6] uppercase tracking-wider">{{ pair.heading }}</p>
                    <p class="text-[9px] text-[#9aa0a6]/70">+ {{ pair.body }}</p>
                  </div>
                  <span class="text-[9px] px-2 py-0.5 rounded-full bg-[#f0f4f9] dark:bg-[#282a2c] text-[#5f6368] dark:text-[#9aa0a6] font-medium border border-[#e8eaed] dark:border-[#3a3a3f]">
                    {{ pair.tag }}
                  </span>
                </div>
              </button>
            </div>
          </div>

          <!-- Modal Footer -->
          <div class="flex-shrink-0 px-7 py-4 border-t border-[#f0f2f5] dark:border-[#3a3a3f] bg-[#fafafa] dark:bg-[#1a1a1e] flex items-center justify-between">
            <p class="text-[10px] text-[#9aa0a6]">Haz clic en un par para aplicarlo instantáneamente</p>
            <button
              @click="showFontModal = false"
              class="px-4 py-2 text-xs font-medium text-[#5f6368] dark:text-[#9aa0a6] hover:text-[#1e1f20] dark:hover:text-[#e3e3e3] hover:bg-[#f0f4f9] dark:hover:bg-[#282a2c] rounded-full border border-[#e8eaed] dark:border-[#3a3a3f] transition-all"
            >
              Cerrar
            </button>
          </div>
        </div>
      </div>
    </Transition>
  </Teleport>

  <!-- ======================================================= -->
  <!-- MODAL: Restablecer Identidad Visual                     -->
  <!-- ======================================================= -->
  <Teleport to="body">
    <Transition
      enter-active-class="transition duration-250 ease-out"
      enter-from-class="opacity-0 scale-95"
      enter-to-class="opacity-100 scale-100"
      leave-active-class="transition duration-150 ease-in"
      leave-from-class="opacity-100 scale-100"
      leave-to-class="opacity-0 scale-95"
    >
      <div
        v-if="showResetModal"
        class="fixed inset-0 z-[9999] flex items-center justify-center p-6"
      >
        <!-- Backdrop premium -->
        <div class="absolute inset-0 bg-black/70 backdrop-blur-md" @click="showResetModal = false"></div>

        <!-- Panel -->
        <div class="relative bg-white dark:bg-[#18181b] rounded-3xl shadow-[0_40px_80px_-12px_rgba(0,0,0,0.5)] dark:shadow-[0_40px_80px_-12px_rgba(0,0,0,0.9)] border border-[#e8eaed] dark:border-[#27272a] w-full max-w-2xl flex flex-col overflow-hidden">

          <!-- Paso 1: Copia de seguridad -->
          <template v-if="resetStep === 1">
            <!-- Header con borde inferior -->
            <div class="px-10 pt-10 pb-7 border-b border-[#f0f2f5] dark:border-[#27272a]">
              <!-- Pill de paso -->
              <div class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-amber-50 dark:bg-amber-950/60 border border-amber-100 dark:border-amber-900/40 rounded-full mb-5">
                <span class="w-2 h-2 rounded-full bg-amber-500 dark:bg-amber-400"></span>
                <span class="text-xs font-semibold text-amber-600 dark:text-amber-400 uppercase tracking-widest">Paso 1 de 2</span>
              </div>
              <h2 class="text-2xl font-bold text-[#1e1f20] dark:text-white mb-2">Guarda una copia antes de continuar</h2>
              <p class="text-[15px] text-[#5f6368] dark:text-[#9aa0a6] leading-relaxed">
                Tu identidad visual actual — colores, tipografías, textos y configuración — puede descargarse como archivo de respaldo. Si en el futuro quieres volver, solo tienes que cargarlo.
              </p>
            </div>

            <!-- Cuerpo: tarjeta visual de lo que se va a perder -->
            <div class="px-10 py-7">
              <div class="rounded-2xl border border-[#e8eaed] dark:border-[#27272a] overflow-hidden">
                <div class="px-5 py-3.5 bg-[#f8f9fa] dark:bg-[#27272a] border-b border-[#e8eaed] dark:border-[#3a3a3f]">
                  <p class="text-xs font-bold text-[#9aa0a6] uppercase tracking-widest">Se restablecerá</p>
                </div>
                <div class="px-5 py-4 grid grid-cols-2 gap-3">
                  <div v-for="item in ['Paleta de colores IA', 'Tipografías', 'Textos del banner', 'Mensajes de valor', 'Anuncios', 'Configuración visual']" :key="item"
                    class="flex items-center gap-2.5 text-sm text-[#5f6368] dark:text-[#9aa0a6]">
                    <span class="w-1.5 h-1.5 rounded-full bg-[#dadce0] dark:bg-[#3a3a3f] flex-shrink-0"></span>
                    {{ item }}
                  </div>
                </div>
              </div>
            </div>

            <!-- Acciones -->
            <div class="px-10 pb-10 flex flex-col gap-3">
              <button
                @click="downloadBackupAndContinue"
                :disabled="isDownloadingBackup"
                class="w-full px-6 py-4 bg-[#1e1f20] dark:bg-white hover:bg-black dark:hover:bg-gray-100 text-white dark:text-[#1e1f20] text-[15px] font-semibold rounded-2xl transition-all duration-200 flex items-center justify-center gap-2.5 disabled:opacity-50 shadow-lg shadow-black/10"
              >
                <svg v-if="!isDownloadingBackup" class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
                </svg>
                <svg v-else class="animate-spin w-5 h-5" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                </svg>
                Descargar copia y continuar
              </button>

              <button
                @click="resetStep = 2"
                class="w-full px-6 py-3.5 bg-transparent hover:bg-[#f8f9fa] dark:hover:bg-[#27272a] text-[#5f6368] dark:text-[#9aa0a6] hover:text-[#1e1f20] dark:hover:text-white text-[15px] font-medium rounded-2xl border border-[#e8eaed] dark:border-[#27272a] transition-all duration-200"
              >
                Continuar sin guardar copia
              </button>

              <button
                @click="showResetModal = false"
                class="w-full px-6 py-2.5 text-sm text-[#9aa0a6] hover:text-[#5f6368] dark:hover:text-[#e3e3e3] transition-colors"
              >
                Cancelar
              </button>
            </div>
          </template>

          <!-- Paso 2: Confirmación final -->
          <template v-else-if="resetStep === 2">
            <div class="px-10 pt-10 pb-7 border-b border-[#f0f2f5] dark:border-[#27272a]">
              <div class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-red-50 dark:bg-red-950/60 border border-red-100 dark:border-red-900/40 rounded-full mb-5">
                <span class="w-2 h-2 rounded-full bg-red-500 dark:bg-red-400"></span>
                <span class="text-xs font-semibold text-red-600 dark:text-red-400 uppercase tracking-widest">Paso 2 de 2 · Irreversible</span>
              </div>
              <h2 class="text-2xl font-bold text-[#1e1f20] dark:text-white mb-2">¿Confirmar restablecimiento?</h2>
              <p class="text-[15px] text-[#5f6368] dark:text-[#9aa0a6] leading-relaxed">
                Tu tienda volverá a los valores por defecto. La identidad generada por IA se eliminará permanentemente.
              </p>
            </div>

            <div class="px-10 py-7">
              <div class="rounded-2xl bg-red-50 dark:bg-red-950/30 border border-red-100 dark:border-red-900/40 px-5 py-4 flex items-start gap-3">
                <svg class="w-5 h-5 text-red-500 dark:text-red-400 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M8.485 2.495c.673-1.167 2.357-1.167 3.03 0l6.28 10.875c.673 1.167-.17 2.625-1.516 2.625H3.72c-1.347 0-2.189-1.458-1.515-2.625L8.485 2.495zM10 5a.75.75 0 01.75.75v3.5a.75.75 0 01-1.5 0v-3.5A.75.75 0 0110 5zm0 9a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" />
                </svg>
                <p class="text-sm text-red-700 dark:text-red-400 leading-relaxed">Esta acción <strong>no se puede deshacer</strong>. Si no descargaste una copia de seguridad, perderás toda tu identidad visual permanentemente.</p>
              </div>
            </div>

            <div class="px-10 pb-10 flex flex-col gap-3">
              <button
                @click="executeReset"
                :disabled="isResetting"
                class="w-full px-6 py-4 bg-red-600 hover:bg-red-700 text-white text-[15px] font-semibold rounded-2xl transition-all duration-200 flex items-center justify-center gap-2.5 disabled:opacity-50 shadow-lg shadow-red-600/20"
              >
                <svg v-if="!isResetting" class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99" />
                </svg>
                <svg v-else class="animate-spin w-5 h-5" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                </svg>
                {{ isResetting ? 'Restableciendo...' : 'Sí, restablecer todo' }}
              </button>

              <button
                @click="resetStep = 1"
                class="w-full px-6 py-3.5 bg-transparent hover:bg-[#f8f9fa] dark:hover:bg-[#27272a] text-[#5f6368] dark:text-[#9aa0a6] hover:text-[#1e1f20] dark:hover:text-white text-[15px] font-medium rounded-2xl border border-[#e8eaed] dark:border-[#27272a] transition-all duration-200"
              >
                ← Volver y descargar copia
              </button>
            </div>
          </template>
        </div>
      </div>
    </Transition>
  </Teleport>

  <!-- ======================================================= -->
  <!-- MODAL: Cargar Plantilla de Copia de Seguridad           -->
  <!-- ======================================================= -->
  <Teleport to="body">
    <Transition
      enter-active-class="transition duration-250 ease-out"
      enter-from-class="opacity-0 scale-95"
      enter-to-class="opacity-100 scale-100"
      leave-active-class="transition duration-150 ease-in"
      leave-from-class="opacity-100 scale-100"
      leave-to-class="opacity-0 scale-95"
    >
      <div
        v-if="showRestoreModal"
        class="fixed inset-0 z-[9999] flex items-center justify-center p-6"
      >
        <div class="absolute inset-0 bg-black/70 backdrop-blur-md" @click="showRestoreModal = false"></div>
        <div
          class="relative bg-white dark:bg-[#18181b] rounded-3xl shadow-[0_40px_80px_-12px_rgba(0,0,0,0.5)] dark:shadow-[0_40px_80px_-12px_rgba(0,0,0,0.9)] border border-[#e8eaed] dark:border-[#27272a] w-full max-w-2xl overflow-hidden"
        >
          <!-- Header -->
          <div class="px-10 pt-10 pb-7 border-b border-[#f0f2f5] dark:border-[#27272a]">
            <div class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-blue-50 dark:bg-blue-950/60 border border-blue-100 dark:border-blue-900/40 rounded-full mb-5">
              <span class="w-2 h-2 rounded-full bg-blue-500 dark:bg-blue-400"></span>
              <span class="text-xs font-semibold text-blue-600 dark:text-blue-400 uppercase tracking-widest">Restaurar plantilla</span>
            </div>
            <h2 class="text-2xl font-bold text-[#1e1f20] dark:text-white mb-2">Cargar copia de seguridad</h2>
            <p class="text-[15px] text-[#5f6368] dark:text-[#9aa0a6] leading-relaxed">
              Selecciona el archivo <code class="text-sm bg-[#f0f4f9] dark:bg-[#27272a] px-1.5 py-0.5 rounded font-mono">.json</code> que exportaste antes. Tu tienda quedará exactamente igual a como estaba en ese momento.
            </p>
          </div>

          <!-- Drop zone -->
          <div class="px-10 py-7">
            <div
              class="group border-2 border-dashed rounded-2xl p-10 text-center cursor-pointer transition-all duration-200"
              :class="restoreData
                ? 'border-[#1a73e8] dark:border-[#8ab4f8] bg-blue-50/50 dark:bg-blue-950/20'
                : 'border-[#dadce0] dark:border-[#3a3a3f] hover:border-[#1a73e8] dark:hover:border-[#8ab4f8] hover:bg-[#f8f9fa] dark:hover:bg-[#27272a]/50'"
              @click="backupFileInput?.click()"
              @dragover.prevent
              @drop.prevent="handleBackupDrop"
            >
              <template v-if="restoreData">
                <div class="w-12 h-12 rounded-xl bg-blue-100 dark:bg-blue-900/40 flex items-center justify-center mx-auto mb-3">
                  <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                  </svg>
                </div>
                <p class="text-base font-semibold text-[#1e1f20] dark:text-white">{{ restoreFileName }}</p>
                <p class="text-sm text-[#5f6368] dark:text-[#9aa0a6] mt-1">Archivo listo para restaurar · <button class="text-[#1a73e8] dark:text-[#8ab4f8] underline" @click.stop="restoreData = null; restoreFileName = ''">Cambiar</button></p>
              </template>
              <template v-else>
                <svg class="w-10 h-10 text-[#dadce0] dark:text-[#3a3a3f] mx-auto mb-3 group-hover:text-[#1a73e8] dark:group-hover:text-[#8ab4f8] transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5" />
                </svg>
                <p class="text-[15px] font-medium text-[#5f6368] dark:text-[#9aa0a6]">Haz clic o arrastra tu archivo aquí</p>
                <p class="text-sm text-[#9aa0a6] dark:text-[#5f6368] mt-1">Archivos <code class="font-mono">.json</code> de copia de seguridad</p>
              </template>
            </div>
            <input ref="backupFileInput" type="file" accept=".json" class="hidden" @change="handleBackupFileSelect" />
          </div>

          <div class="px-10 pb-10 flex flex-col gap-3">
            <button
              @click="executeRestore"
              :disabled="!restoreData || isRestoring"
              class="w-full px-6 py-4 bg-[#1a73e8] hover:bg-[#1557b0] text-white text-[15px] font-semibold rounded-2xl transition-all duration-200 flex items-center justify-center gap-2.5 disabled:opacity-40 disabled:cursor-not-allowed shadow-lg shadow-blue-600/20"
            >
              <svg v-if="!isRestoring" class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5" />
              </svg>
              <svg v-else class="animate-spin w-5 h-5" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
              </svg>
              {{ isRestoring ? 'Restaurando identidad...' : 'Restaurar esta plantilla' }}
            </button>
            <button
              @click="showRestoreModal = false; restoreData = null; restoreFileName = ''"
              class="w-full px-6 py-2.5 text-sm text-[#9aa0a6] hover:text-[#5f6368] dark:hover:text-[#e3e3e3] transition-colors"
            >
              Cancelar
            </button>
          </div>
        </div>
      </div>
    </Transition>
  </Teleport>

  <!-- Toast de Éxito -->
  <Transition
    enter-active-class="transition duration-300 ease-out"
    enter-from-class="opacity-0 translate-y-4"
    enter-to-class="opacity-100 translate-y-0"
    leave-active-class="transition duration-200 ease-in"
    leave-from-class="opacity-100 translate-y-0"
    leave-to-class="opacity-0 translate-y-4"
  >
    <div 
      v-if="showSuccessToast" 
      :class="toastIsError
        ? 'bg-red-600 border-red-500/50'
        : 'bg-[#1e8e3e] border-[#1e8e3e]/50'"
      class="fixed bottom-6 right-6 text-white px-6 py-4 rounded-2xl shadow-2xl flex items-center gap-4 z-[99999] border"
    >
      <div class="w-10 h-10 bg-white/20 rounded-full flex items-center justify-center flex-shrink-0">
        <svg v-if="!toastIsError" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
        </svg>
        <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
        </svg>
      </div>
      <div>
        <h4 class="font-medium text-sm">{{ toastMessage.title }}</h4>
        <p class="text-xs opacity-90 mt-0.5">{{ toastMessage.description }}</p>
      </div>
      <button @click="showSuccessToast = false" class="ml-2 text-white/70 hover:text-white transition-colors">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
          <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
        </svg>
      </button>
    </div>
  </Transition>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted, computed, watch, nextTick } from 'vue'
import { appStore } from '../store/appStore.js'
import apiClient from '../services/apiClient.js'
import axios from 'axios'
import { useToast } from '../composables/useToast.js'

const { showError, showWarning } = useToast()

// Props & Emits
const props = defineProps({
  moduleName: {
    type: String,
    default: ''
  },
  queryParams: {
    type: Object,
    default: () => ({})
  }
})

const emit = defineEmits(['navigate', 'changeModule', 'openQuotationInPos', 'openReturnInPos', 'refresh'])

// Refs
const logoInput = ref(null)
const bannerInput = ref(null)
const previewKey = ref(0)
// Refs para medios del hero (carrusel) y componentes de diseño
const heroImageInputs = ref([])
const lookbookImageInputs = ref([])
const lookbookVideoInput = ref(null)
const bentoMainInput = ref(null)
const bentoDetailInput = ref(null)
const editorialImageInput = ref(null)

// Catalog URL - Se construye dinámicamente según el entorno
const catalogUrl = computed(() => {
  // En desarrollo usa la ruta relativa /catalog
  return `${window.location.origin}/catalog`
})

// State
const isSaving = ref(false)
const showSuccessToast = ref(false)
const toastIsError = ref(false)
const toastMessage = reactive({
  title: '¡Guardado!',
  description: 'Vista previa actualizada.'
})
const isLoading = ref(true)
const isInitializing = ref(true) // Bloquea los watchers durante la carga inicial para evitar guardados falsos
const activeTab = ref('identity') // Will be corrected by isFashionStore watch (immediate)
const showWarningMessage = ref(false) // Control independiente del mensaje de alerta
const warningType = ref('categories') // 'categories' | 'whatsapp' - tipo de advertencia

// AI Brand State
const aiBrandDescription = ref('')
const isGeneratingBrand = ref(false)
const aiBrandData = ref(null)
const isRecordingVoice = ref(false)
const voiceRecognition = ref(null)
const aiGenerationProgress = ref('')

// Store name reactive computed (used in choosing phone preview)
const storeName = computed(() => appStore.systemSettings?.store_name || 'Mi Tienda')

// Onboarding UX State
// 'loading' → esperando servidor | 'welcome' → sin identidad | 'brief-only' → chat activo
// 'choosing' → carrusel de 5 diseños | 'configured' → con identidad, panel 3 columnas
const catalogUiMode = ref('loading')
const showConfigPanel = ref(false)   // animates in after AI generation
const showPreviewPanel = ref(false)  // loads last for wow effect

// ── Choosing mode: carousel de diseños ──────────────────────────────────
const generatedDesigns = ref([])    // array de hasta 5 diseños generados
const currentDesignIdx = ref(0)     // índice activo en el carrusel
const designsReady = ref(0)         // cuántos diseños han llegado (para progress)
const isChoosingApplying = ref(false) // aplicando el diseño elegido
const choosingIframeKey = ref(0)    // incrementar para forzar recarga del iframe
const isChoosingIframeLoading = ref(true) // spinner mientras carga el iframe
const choosingIframeEl = ref(null)          // ref al elemento iframe

// ── Upload-images mode: paso de subida de imágenes del hook ─────────────
const ONBOARDING_DRAFT_KEY = 'ai_onboarding_draft'
const uploadSubStep = ref(1) // 1=hero, 2=hook, 3=historia
const onboardingImages = ref({
  // Paso 1: Hero / carrusel banner
  hero_images: ['', '', ''],
  // Paso 2: Hook (según hook_style)
  editorial_image: '',
  lookbook_images: ['', '', ''],
  bento_main: '',
  bento_detail: '',
  bento_secondary: '',
  // Paso 3: Historia visual
  story_image: ''
})
const isOnboardingImagesSaving = ref(false)
const onboardingPreviewKey = ref(0)
const isOnboardingPreviewLoading = ref(true)
const onboardingPreviewEl = ref(null)
// file input refs para onboarding
const ob_editorial = ref(null)
const ob_lookbook = ref([])
const ob_bento_main = ref(null)
const ob_bento_detail = ref(null)
const ob_bento_secondary = ref(null)
const ob_hero = ref([])
const ob_story = ref(null)

// Hook del diseño elegido (leído de aiBrandData)
const onboardingHookStyle = computed(() => {
  return aiBrandData.value?.layout_config?.hook_style || 'editorial-story'
})

// Construye el payload de preview incluyendo las imágenes del onboarding
const buildOnboardingPreviewPayload = () => {
  if (!aiBrandData.value) return null
  return JSON.parse(JSON.stringify({
    ...buildPreviewPayload(aiBrandData.value),
    _catalog_media: {
      hero_images: onboardingImages.value.hero_images.filter(Boolean),
      editorial_image: onboardingImages.value.editorial_image || '',
      lookbook_images: onboardingImages.value.lookbook_images.filter(Boolean),
      bento_main: onboardingImages.value.bento_main || '',
      bento_detail: onboardingImages.value.bento_detail || '',
      bento_secondary: onboardingImages.value.bento_secondary || '',
      story_image: onboardingImages.value.story_image || '',
    }
  }))
}

// Escribe al localStorage y recarga el iframe de onboarding
const updateOnboardingPreview = () => {
  const payload = buildOnboardingPreviewPayload()
  if (!payload) return
  try { localStorage.setItem('ai_design_preview', JSON.stringify(payload)) } catch (e) {}
  isOnboardingPreviewLoading.value = true
  onboardingPreviewKey.value++
}

// Trigger de file input
const triggerOnboardingFile = (type, idx = null) => {
  if (type === 'editorial_image') ob_editorial.value?.click()
  else if (type === 'bento_main') ob_bento_main.value?.click()
  else if (type === 'bento_detail') ob_bento_detail.value?.click()
  else if (type === 'bento_secondary') ob_bento_secondary.value?.click()
  else if (type === 'lookbook_images' && idx !== null) ob_lookbook.value[idx]?.click()
  else if (type === 'hero_images' && idx !== null) ob_hero.value[idx]?.click()
  else if (type === 'story_image') ob_story.value?.click()
}

// Manejo de archivo seleccionado
const handleOnboardingFile = (event, type, idx = null) => {
  const file = event.target.files[0]
  if (!file) return
  if (file.size > 2 * 1024 * 1024) { showWarning('El archivo es muy grande. Máximo 2MB.'); return }
  const reader = new FileReader()
  reader.onload = (e) => {
    const b64 = e.target.result
    if (type === 'editorial_image') onboardingImages.value.editorial_image = b64
    else if (type === 'bento_main') onboardingImages.value.bento_main = b64
    else if (type === 'bento_detail') onboardingImages.value.bento_detail = b64
    else if (type === 'bento_secondary') onboardingImages.value.bento_secondary = b64
    else if (type === 'lookbook_images' && idx !== null) onboardingImages.value.lookbook_images[idx] = b64
    else if (type === 'hero_images' && idx !== null) onboardingImages.value.hero_images[idx] = b64
    else if (type === 'story_image') onboardingImages.value.story_image = b64
    updateOnboardingPreview()
    // Auto-save draft
    saveOnboardingDraft()
  }
  reader.readAsDataURL(file)
  // reset input so same file can be re-selected
  event.target.value = ''
}

// Guardar borrador en localStorage
const saveOnboardingDraft = () => {
  try {
    localStorage.setItem(ONBOARDING_DRAFT_KEY, JSON.stringify({
      pending: true,
      hook: onboardingHookStyle.value,
      uploadSubStep: uploadSubStep.value,
      ...onboardingImages.value,
      saved_at: Date.now()
    }))
  } catch (e) {}
}

// Restaurar borrador (si existe) al entrar al modo upload-images
const restoreOnboardingDraft = () => {
  try {
    const raw = localStorage.getItem(ONBOARDING_DRAFT_KEY)
    if (!raw) return
    const draft = JSON.parse(raw)
    if (!draft.pending) return
    uploadSubStep.value = draft.uploadSubStep || 1
    onboardingImages.value = {
      hero_images: draft.hero_images || ['', '', ''],
      editorial_image: draft.editorial_image || '',
      lookbook_images: draft.lookbook_images || ['', '', ''],
      bento_main: draft.bento_main || '',
      bento_detail: draft.bento_detail || '',
      bento_secondary: draft.bento_secondary || '',
      story_image: draft.story_image || '',
    }
  } catch (e) {}
}

// Guardar borrador y QUEDARSE en upload-images (no navegar al panel)
const saveAndExitOnboarding = () => {
  saveOnboardingDraft()
  showSuccess('Borrador guardado. Puedes retomarlo cuando quieras.')
}

// Omitir completamente el paso de fotos (sí navega al panel, sin guardar borrador)
const skipOnboarding = async () => {
  try { localStorage.removeItem(ONBOARDING_DRAFT_KEY) } catch (e) {}
  catalogUiMode.value = 'configured'
  await loadConfiguration()
  await nextTick()
  setTimeout(() => { showConfigPanel.value = true }, 80)
  setTimeout(() => { showPreviewPanel.value = true; refreshPreview() }, 600)
}

// Finalizar onboarding: guardar imágenes + ir al panel
const finishOnboarding = async () => {
  isOnboardingImagesSaving.value = true
  try {
    // Copiar imágenes al config.catalogMedia para que saveConfiguration las incluya
    const heroFiltered = onboardingImages.value.hero_images.filter(Boolean)
    if (heroFiltered.length > 0) config.catalogMedia.hero_images = heroFiltered
    if (onboardingImages.value.editorial_image)
      config.catalogMedia.editorial_image = onboardingImages.value.editorial_image
    if (onboardingImages.value.bento_main)
      config.catalogMedia.bento_main = onboardingImages.value.bento_main
    if (onboardingImages.value.bento_detail)
      config.catalogMedia.bento_detail = onboardingImages.value.bento_detail
    if (onboardingImages.value.bento_secondary)
      config.catalogMedia.bento_secondary = onboardingImages.value.bento_secondary
    if (onboardingImages.value.story_image)
      config.catalogMedia.story_image = onboardingImages.value.story_image
    const cleanLookbook = onboardingImages.value.lookbook_images.filter(Boolean)
    if (cleanLookbook.length > 0)
      config.catalogMedia.lookbook_images = [...cleanLookbook, ...config.catalogMedia.lookbook_images].slice(0, 4)

    await saveConfiguration()
    // Eliminar borrador
    try { localStorage.removeItem(ONBOARDING_DRAFT_KEY) } catch (e) {}

    catalogUiMode.value = 'configured'
    await loadConfiguration()
    await nextTick()
    setTimeout(() => { showConfigPanel.value = true }, 80)
    setTimeout(() => { showPreviewPanel.value = true; refreshPreview() }, 600)
  } catch (err) {
    showWarning('No se pudieron guardar las imágenes. Intenta de nuevo.')
  } finally {
    isOnboardingImagesSaving.value = false
  }
}

// Construye el objeto de preview con datos de la tienda incluidos
const buildPreviewPayload = (design) => {
  // JSON round-trip ensures plain object (no Vue Proxy) — required for postMessage structured clone
  return JSON.parse(JSON.stringify({
    ...design,
    _store_name: storeName.value,
    _logo_url: config.brandIdentity?.logo || '',
    _whatsapp: config.ordersConfig?.whatsappNumber || '',
  }))
}

// Escribe el diseño actual al localStorage para que el iframe lo lea
const writeDesignPreview = (design) => {
  if (!design) return
  try { localStorage.setItem('ai_design_preview', JSON.stringify(buildPreviewPayload(design))) } catch (e) {}
}

// Cuando cambia el índice: escribir en localStorage y recargar el iframe
// (el iframe carga instantáneo en ai_preview mode porque no hace llamadas API)
watch(currentDesignIdx, (idx) => {
  const design = generatedDesigns.value[idx]
  if (!design) return
  writeDesignPreview(design)
  isChoosingIframeLoading.value = true
  choosingIframeKey.value++
})

// Cuando llega el primer diseño, inicializar el iframe (una sola vez)
watch(generatedDesigns, (designs) => {
  if (designs.length > 0 && catalogUiMode.value === 'choosing') {
    const current = designs[currentDesignIdx.value] ?? designs[0]
    writeDesignPreview(current)
    if (choosingIframeKey.value === 0) {
      isChoosingIframeLoading.value = true
      choosingIframeKey.value = 1
    }
  }
}, { deep: false })

// Color Refinement State
const showColorRefinement = ref(false)
const colorRefinementPrompt = ref('')
const isRegeneratingColors = ref(false)

// Reset Modal State
const showResetModal = ref(false)
const resetStep = ref(1)
const isResetting = ref(false)
const isDownloadingBackup = ref(false)

// Restore Modal State
const showRestoreModal = ref(false)
const backupFileInput = ref(null)
const restoreData = ref(null)
const restoreFileName = ref('')
const isRestoring = ref(false)

// Font Modal State
const showFontModal = ref(false)
const selectedFontPairId = ref(null)

const fontPairs = [
  {
    id: 'playfair-lato',
    name: 'Luxury Editorial',
    heading: 'Playfair Display',
    body: 'Lato',
    tag: 'Lujo · Boutique',
    sampleHeading: 'La Moda es Arte',
    sampleBody: 'Descubre piezas únicas diseñadas para durar más que una temporada.'
  },
  {
    id: 'montserrat-inter',
    name: 'Modern Minimal',
    heading: 'Montserrat',
    body: 'Inter',
    tag: 'Moderno · Limpio',
    sampleHeading: 'New Collection',
    sampleBody: 'Simplicidad y sofisticación en cada detalle de nuestra colección.'
  },
  {
    id: 'cormorant-work',
    name: 'High Fashion',
    heading: 'Cormorant Garamond',
    body: 'Work Sans',
    tag: 'Alta Moda · Elegante',
    sampleHeading: 'Élégance Intemporelle',
    sampleBody: 'Para quienes entienden que el buen gusto nunca pasa de moda.'
  },
  {
    id: 'dm-serif-dm-sans',
    name: 'Contemporary',
    heading: 'DM Serif Display',
    body: 'DM Sans',
    tag: 'Contemporáneo',
    sampleHeading: 'Colección Otoño',
    sampleBody: 'Texturas cálidas y siluetas modernas para la nueva temporada.'
  },
  {
    id: 'raleway-open',
    name: 'Soft Elegance',
    heading: 'Raleway',
    body: 'Open Sans',
    tag: 'Elegante · Suave',
    sampleHeading: 'Studio Collection',
    sampleBody: 'Líneas limpias y proporciones perfectas en cada prenda.'
  },
  {
    id: 'bebas-nunito',
    name: 'Urban Bold',
    heading: 'Bebas Neue',
    body: 'Nunito',
    tag: 'Urbano · Bold',
    sampleHeading: 'STREET CULTURE',
    sampleBody: 'Lo auténtico nunca pasa de moda. Estilo urbano sin concesiones.'
  },
  {
    id: 'josefin-josefin',
    name: 'Geometric',
    heading: 'Josefin Sans',
    body: 'Josefin Slab',
    tag: 'Geométrico · Minimal',
    sampleHeading: 'Form & Function',
    sampleBody: 'Geometría perfecta aplicada al diseño de moda contemporánea.'
  },
  {
    id: 'fraunces-dm',
    name: 'Artisan Craft',
    heading: 'Fraunces',
    body: 'DM Sans',
    tag: 'Artesanal · Cálido',
    sampleHeading: 'Hecho con Alma',
    sampleBody: 'Cada pieza cuenta la historia de manos que crean con pasión.'
  }
]

const loadedFonts = new Set()
const loadGoogleFont = (fontName) => {
  if (loadedFonts.has(fontName)) return
  const safeName = fontName.replace(/ /g, '+')
  const link = document.createElement('link')
  link.rel = 'stylesheet'
  link.href = `https://fonts.googleapis.com/css2?family=${safeName}:wght@400;600;700&display=swap`
  document.head.appendChild(link)
  loadedFonts.add(fontName)
}

const applyFontPair = (pair) => {
  if (!aiBrandData.value) {
    aiBrandData.value = { fonts: { heading: pair.heading, body: pair.body } }
  } else {
    if (!aiBrandData.value.fonts) aiBrandData.value.fonts = {}
    aiBrandData.value.fonts.heading = pair.heading
    aiBrandData.value.fonts.body = pair.body
  }
  selectedFontPairId.value = pair.id
  showFontModal.value = false
  // Pre-load font in browser
  loadGoogleFont(pair.heading)
  loadGoogleFont(pair.body)
}

// Helper: Verificar si el número de WhatsApp es válido (más de solo el código de país)
const isValidWhatsappNumber = (number) => {
  if (!number) return false
  // Remover espacios y caracteres especiales
  const cleanNumber = number.replace(/[\s\-\(\)]/g, '')
  // Debe tener más de 4 caracteres (mínimo código país + algunos dígitos)
  // +57 solo tiene 3 caracteres, necesitamos al menos el código + número real
  return cleanNumber.length >= 10
}

// Tabs Configuration - Computed based on store type
const tabs = computed(() => {
  if (isFashionStore.value) {
    return [
      {
        id: 'identidad-visual',
        label: 'Identidad Visual',
        icon: 'sparkles',
        description: 'Define la esencia visual y estética de tu marca'
      },
      {
        id: 'catalog',
        label: 'Catálogo',
        icon: 'box',
        description: 'Controla la visibilidad de categorías y productos'
      },
      {
        id: 'orders',
        label: 'Pedidos',
        icon: 'message',
        description: 'Configura el sistema de pedidos por WhatsApp'
      },
      {
        id: 'rules',
        label: 'Reglas',
        icon: 'settings',
        description: 'Establece las reglas de negocio y límites'
      }
    ]
  } else {
    return [
      {
        id: 'identity',
        label: 'Diseño',
        icon: 'palette',
        description: 'Define el estilo visual de tu catálogo online'
      },
      {
        id: 'catalog',
        label: 'Catálogo',
        icon: 'box',
        description: 'Controla la visibilidad de categorías y productos'
      },
      {
        id: 'orders',
        label: 'Pedidos',
        icon: 'message',
        description: 'Configura el sistema de pedidos por WhatsApp'
      },
      {
        id: 'rules',
        label: 'Reglas',
        icon: 'settings',
        description: 'Establece las reglas de negocio y límites'
      }
    ]
  }
})

const currentTabLabel = computed(() => {
  const tab = tabs.value.find(t => t.id === activeTab.value)
  return tab ? tab.label : ''
})

const currentTabDescription = computed(() => {
  const tab = tabs.value.find(t => t.id === activeTab.value)
  return tab ? tab.description : ''
})

// Detectar si la tienda es tipo Fashion (para mostrar/ocultar plantillas y banner)
const isFashionStore = computed(() => {
  // Primero intentar desde appStore.systemSettings
  if (appStore.systemSettings?.store_type) {
    return appStore.systemSettings.store_type === 'fashion'
  }
  
  // Si no está en systemSettings, buscar en localStorage
  const storedType = localStorage.getItem('pending_store_type')
  return storedType === 'fashion'
})

// Sync activeTab with store type — immediate so it runs on first render
watch(isFashionStore, (isFashion) => {
  activeTab.value = isFashion ? 'identidad-visual' : 'identity'
}, { immediate: true })

// Preload all Google Fonts when the font modal opens
watch(showFontModal, (open) => {
  if (open) {
    fontPairs.forEach(pair => {
      loadGoogleFont(pair.heading)
      loadGoogleFont(pair.body)
    })
  }
})

// Función helper para obtener plantilla válida según tipo de tienda
const getValidTemplate = (template) => {
  // Si es fashion, puede usar cualquier plantilla
  if (isFashionStore.value) {
    return template || 'speed-market'
  }
  // Si NO es fashion, NUNCA puede ser visual-story
  if (template === 'visual-story') {
    console.warn('Plantilla "visual-story" no disponible para tiendas no-fashion. Usando "speed-market"')
    return 'speed-market'
  }
  return template || 'speed-market'
}

// Hook/Spotlight style activo según la identidad IA generada
const activeHookStyle = computed(() => {
  return aiBrandData.value?.layout_config?.hook_style || null
})

// Configuration Object (Reactive)
const config = reactive({
  storeActive: false, // Por defecto inactivo hasta que seleccionen categorías
  brandIdentity: {
    logo: '', 
    banner: '',
    primaryColor: '#10B981', 
    template: getValidTemplate('speed-market') // Plantilla por defecto validada
  },
  // Medios por componente de diseño — solo se envían los del estilo activo
  catalogMedia: {
    hero_images: ['', '', ''],         // Carrusel del hero (siempre, máx 3)
    lookbook_images: ['', '', '', ''], // Urban Lookbook: 4 imágenes
    lookbook_video: '',                // Urban Lookbook: video opcional
    bento_main: '',                    // Dynamic Bento: imagen principal
    bento_detail: '',                  // Dynamic Bento: imagen detalle
    bento_secondary: '',               // Dynamic Bento: imagen secundaria
    editorial_image: '',               // Editorial Story: imagen inspiracional
    story_image: ''                    // Historia visual
  },
  inventoryVisibility: {
    visibleCategories: [], 
    hideOutOfStock: false
  },
  ordersConfig: {
    whatsappNumber: '+57',
    customMessage: 'Hola, quiero hacer el siguiente pedido:'
  },
  businessRules: {
    deliveryCost: 0,
    minimumOrder: 0,
    syncWithCashRegister: false
  }
})

// Mock categories (will be loaded from appStore)
const availableCategories = ref([])

// Load categories from store
onMounted(async () => {
  isLoading.value = true

  // ⚡ Las 3 llamadas en paralelo — reduce el tiempo de carga ~60-70%
  const [categoriesRes] = await Promise.allSettled([
    apiClient.get('/categories-pos'),
    loadConfiguration(),
    loadAiBrandData()
  ])

  // Procesar categorías
  if (categoriesRes.status === 'fulfilled') {
    const categoriesData = categoriesRes.value?.data?.data || categoriesRes.value?.data
    if (categoriesData && Array.isArray(categoriesData)) {
      availableCategories.value = categoriesData.map(cat => ({ id: cat.id, name: cat.name }))
    } else {
      availableCategories.value = appStore.categories?.length ? appStore.categories : []
    }
  } else {
    availableCategories.value = appStore.categories?.length ? appStore.categories : []
  }
  
  // Validación final: Asegurar que la plantilla sea válida
  config.brandIdentity.template = getValidTemplate(config.brandIdentity.template)

  // Pre-poblar el caché del catálogo público con la config fresca
  // Así cuando el iframe aparezca, renderiza instantáneamente sin spinner
  try {
    const cacheData = {
      template: config.brandIdentity.template,
      primary_color: config.brandIdentity.primaryColor,
      logo_url: config.brandIdentity.logo || '',
      banner_url: config.brandIdentity.banner || '',
      whatsapp_number: config.ordersConfig.whatsappNumber || '',
      currency_symbol: '$',
      delivery_cost: parseFloat(config.businessRules.deliveryCost || 0),
      min_order_value: parseFloat(config.businessRules.minimumOrder || 0),
      custom_message: config.ordersConfig.customMessage || 'Hola, quiero hacer el siguiente pedido:',
      store_name: appStore.systemSettings?.store_name || 'Mi Tienda',
      ai_color_palette: aiBrandData.value?.color_palette || null,
      ai_fonts: aiBrandData.value?.fonts || null,
      ai_banner_texts: aiBrandData.value?.banner_texts || null,
      ai_about_us: aiBrandData.value?.about_us || null,
      ai_value_messages: aiBrandData.value?.value_messages || null,
      ai_announcements: aiBrandData.value?.announcements || null,
      ai_cross_sell_messages: aiBrandData.value?.cross_sell_messages || null,
      ai_layout_config: aiBrandData.value?.layout_config || null
    }
    localStorage.setItem('pos_catalog_config_cache', JSON.stringify(cacheData))
  } catch (e) {}

  // Liberar el flag de inicialización ANTES de isLoading para que los watchers
  // ya no tengan efecto secundario aunque algo cambie en el siguiente tick
  isInitializing.value = false
  isLoading.value = false

  // Determinar modo de UI: si no hay identidad de marca, mostrar onboarding
  // Solo cambiar el modo si NO estamos ya en 'configured' o 'upload-images'
  if (catalogUiMode.value !== 'configured' && catalogUiMode.value !== 'upload-images') {
    if (aiBrandData.value && aiBrandData.value.color_palette) {
      // Ya tiene identidad creada — verificar si hay borrador de imágenes pendiente
      let hasDraft = false
      try {
        const raw = localStorage.getItem(ONBOARDING_DRAFT_KEY)
        if (raw) {
          const draft = JSON.parse(raw)
          hasDraft = draft.pending === true
        }
      } catch (e) {}

      if (hasDraft) {
        // Retomar el paso de subida de imágenes
        catalogUiMode.value = 'upload-images'
        restoreOnboardingDraft()
        await nextTick()
        updateOnboardingPreview()
      } else {
        // Ya tiene identidad y no hay borrador → ir directo al panel completo
        catalogUiMode.value = 'configured'
        showConfigPanel.value = true
        showPreviewPanel.value = true
      }
    } else {
      // Primera vez → mostrar pantalla de bienvenida
      catalogUiMode.value = 'welcome'
    }
  }
})

// Validación: Desactivar catálogo automáticamente si no hay categorías O número WhatsApp
watch(() => config.storeActive, async (newValue) => {
  // Ignorar cambios durante la carga inicial (evita guardados falsos y recarga del iframe)
  if (isInitializing.value) return

  if (newValue) {
    // Verificar categorías
    if (config.inventoryVisibility.visibleCategories.length === 0) {
      console.warn('Intento de activar catálogo sin categorías seleccionadas')
      
      // Desactivar INMEDIATAMENTE el toggle
      config.storeActive = false
      
      // Esperar a que se actualice el DOM
      await nextTick()
      
      // Guardar automáticamente el cambio en el backend
      await saveConfiguration()
      // Mostrar mensaje de alerta por 40 segundos
      warningType.value = 'categories'
      showWarningMessage.value = true
      setTimeout(() => {
        showWarningMessage.value = false
      }, 40000)
      return
    }
    
    // Verificar número de WhatsApp
    if (!isValidWhatsappNumber(config.ordersConfig.whatsappNumber)) {
      console.warn('Intento de activar catálogo sin número de WhatsApp válido')
      
      // Desactivar INMEDIATAMENTE el toggle
      config.storeActive = false
      
      // Esperar a que se actualice el DOM
      await nextTick()
      
      // Guardar automáticamente el cambio en el backend
      await saveConfiguration()
      // Mostrar mensaje de alerta por 40 segundos
      warningType.value = 'whatsapp'
      showWarningMessage.value = true
      setTimeout(() => {
        showWarningMessage.value = false
      }, 40000)
      return
    }
    
    // Si llegó aquí, el cambio es válido - Guardar automáticamente
    await nextTick()
    await saveConfiguration()
  } else if (!newValue) {
    // Usuario desactivó la tienda manualmente - Guardar automáticamente
    await nextTick()
    await saveConfiguration()
  }
})

// Validación inteligente al cambiar categorías
watch(() => config.inventoryVisibility.visibleCategories.length, async (newLength, oldLength) => {
  if (isInitializing.value) return

  // 1️⃣ Si se selecciona al menos una categoría, cerrar mensaje de alerta inmediatamente
  if (newLength > 0 && showWarningMessage.value) {
    showWarningMessage.value = false
  }
  
  // 2️⃣ Si se quitan TODAS las categorías y el catálogo está activo, desactivarlo automáticamente
  if (config.storeActive && newLength === 0 && oldLength > 0) {
    console.warn('Se quitaron todas las categorías - Desactivando catálogo automáticamente')
    
    // Desactivar el toggle
    config.storeActive = false
    
    // Esperar a que se actualice el DOM
    await nextTick()
    
    // Guardar automáticamente el cambio en el backend
    await saveConfiguration()
    // Mostrar mensaje de alerta por 40 segundos
    warningType.value = 'categories'
    showWarningMessage.value = true
    setTimeout(() => {
      showWarningMessage.value = false
    }, 40000)
  }
})

// Validación del número de WhatsApp
watch(() => config.ordersConfig.whatsappNumber, (newValue) => {
  if (isInitializing.value) return

  // Si se ingresa un número válido, cerrar mensaje de alerta si era de WhatsApp
  if (isValidWhatsappNumber(newValue) && showWarningMessage.value && warningType.value === 'whatsapp') {
    showWarningMessage.value = false
  }
})

// Refresh Preview - Recarga el iframe completamente
const refreshPreview = () => {
  previewKey.value++
  // Forzar recarga completa del iframe con timestamp para evitar caché
  const iframe = document.querySelector('iframe')
  if (iframe) {
    const currentSrc = iframe.src.split('?')[0] // Remover query params anteriores
    iframe.src = `${currentSrc}?t=${Date.now()}` // Agregar timestamp único
  }
}

// Open catalog in new window (always mobile view)
const openCatalogInNewWindow = () => {
  const catalogUrl = `${window.location.origin}/catalog`
  const windowFeatures = 'width=414,height=896,left=100,top=100'
  window.open(catalogUrl, 'CatalogPreview', windowFeatures)
}

// Copy store link to clipboard
const copyStoreLink = async () => {
  const catalogUrl = `${window.location.origin}/catalog`
  try {
    await navigator.clipboard.writeText(catalogUrl)
    
    // Mostrar toast de éxito
    toastMessage.title = '¡Enlace copiado!'
    toastMessage.description = 'El enlace se copió al portapapeles.'
    showSuccessToast.value = true
    setTimeout(() => {
      showSuccessToast.value = false
    }, 3000)
    
  } catch (error) {
    console.error('Error al copiar enlace:', error)
    // Fallback para navegadores antiguos
    const textArea = document.createElement('textarea')
    textArea.value = catalogUrl
    textArea.style.position = 'fixed'
    textArea.style.left = '-999999px'
    document.body.appendChild(textArea)
    textArea.select()
    try {
      document.execCommand('copy')
      toastMessage.title = '¡Enlace copiado!'
      toastMessage.description = 'El enlace se copió al portapapeles.'
      showSuccessToast.value = true
      setTimeout(() => {
        showSuccessToast.value = false
      }, 3000)
    } catch (err) {
      console.error('Error en fallback de copia:', err)
      showError('No se pudo copiar el enlace. Por favor, cópialo manualmente.')
    }
    document.body.removeChild(textArea)
  }
}

// Toggle category selection
const toggleCategory = (categoryId) => {
  const index = config.inventoryVisibility.visibleCategories.indexOf(categoryId)
  if (index > -1) {
    config.inventoryVisibility.visibleCategories.splice(index, 1)
  } else {
    config.inventoryVisibility.visibleCategories.push(categoryId)
  }
}

// Trigger file upload — soporta logo, banner, hero_image_N, lookbook_image_N,
// lookbook_video, bento_main, bento_detail, editorial_image
const triggerFileUpload = (type, index = null) => {
  if (type === 'logo') {
    logoInput.value?.click()
  } else if (type === 'banner') {
    bannerInput.value?.click()
  } else if (type === 'hero_image' && index !== null) {
    heroImageInputs.value[index]?.click()
  } else if (type === 'lookbook_image' && index !== null) {
    lookbookImageInputs.value[index]?.click()
  } else if (type === 'lookbook_video') {
    lookbookVideoInput.value?.click()
  } else if (type === 'bento_main') {
    bentoMainInput.value?.click()
  } else if (type === 'bento_detail') {
    bentoDetailInput.value?.click()
  } else if (type === 'editorial_image') {
    editorialImageInput.value?.click()
  }
}

// Handle file upload — base64 para todos los tipos de media
const handleFileUpload = (event, type, index = null) => {
  const file = event.target.files[0]
  if (!file) return

  const isVideo = file.type.startsWith('video/')
  const maxSize = isVideo ? 50 * 1024 * 1024 : 2 * 1024 * 1024
  const maxLabel = isVideo ? '50MB' : '2MB'

  if (file.size > maxSize) {
    showWarning(`El archivo es muy grande. Máximo ${maxLabel}.`)
    return
  }

  const reader = new FileReader()
  reader.onload = (e) => {
    const base64String = e.target.result

    if (type === 'logo') {
      config.brandIdentity.logo = base64String
    } else if (type === 'banner') {
      config.brandIdentity.banner = base64String
    } else if (type === 'hero_image' && index !== null) {
      config.catalogMedia.hero_images[index] = base64String
    } else if (type === 'lookbook_image' && index !== null) {
      config.catalogMedia.lookbook_images[index] = base64String
    } else if (type === 'lookbook_video') {
      config.catalogMedia.lookbook_video = base64String
    } else if (type === 'bento_main') {
      config.catalogMedia.bento_main = base64String
    } else if (type === 'bento_detail') {
      config.catalogMedia.bento_detail = base64String
    } else if (type === 'editorial_image') {
      config.catalogMedia.editorial_image = base64String
    }
  }
  reader.readAsDataURL(file)
}

// Load configuration from backend
const loadConfiguration = async () => {
  try {
    const response = await apiClient.get('/web-catalog/config')
    
    if (response.data.success) {
      const data = response.data.data
      
      // Por defecto inactivo si no está explícitamente configurado
      config.storeActive = data.store_active ?? false
      config.brandIdentity.logo = data.logo_url || ''
      config.brandIdentity.banner = data.banner_url || ''
      config.brandIdentity.primaryColor = data.primary_color || '#10B981'
      // Validar plantilla usando helper
      const loadedTemplate = data.template || 'speed-market'
      config.brandIdentity.template = getValidTemplate(loadedTemplate)
      
      const visibleCats = Array.isArray(data.visible_categories) ? data.visible_categories : []
      
      // Respetar la configuración guardada, incluso si está vacía
      config.inventoryVisibility.visibleCategories = visibleCats
      
      // REGLA: Si no hay categorías seleccionadas, FORZAR catálogo inactivo
      if (visibleCats.length === 0) {
        console.warn('No hay categorías seleccionadas - Forzando catálogo inactivo')
        config.storeActive = false
      }
      
      config.inventoryVisibility.hideOutOfStock = data.hide_out_of_stock ?? false
      config.ordersConfig.whatsappNumber = data.whatsapp_number || '+57'
      config.ordersConfig.customMessage = data.custom_message || 'Hola, quiero hacer el siguiente pedido:'
      config.businessRules.deliveryCost = parseFloat(data.delivery_cost || 0)
      config.businessRules.minimumOrder = parseFloat(data.minimum_order || 0)
      config.businessRules.syncWithCashRegister = data.sync_with_cash_register ?? false

      // Cargar medios del catálogo (catalog_media puede venir como string o como objeto)
      const rawMedia = (typeof data.catalog_media === 'string'
        ? JSON.parse(data.catalog_media)
        : data.catalog_media) || {}
      config.catalogMedia.hero_images       = rawMedia.hero_images       || ['', '', '']
      config.catalogMedia.lookbook_images   = rawMedia.lookbook_images   || ['', '', '', '']
      config.catalogMedia.lookbook_video    = rawMedia.lookbook_video    || ''
      config.catalogMedia.bento_main        = rawMedia.bento_main        || ''
      config.catalogMedia.bento_detail      = rawMedia.bento_detail      || ''
      config.catalogMedia.bento_secondary   = rawMedia.bento_secondary   || ''
      config.catalogMedia.editorial_image   = rawMedia.editorial_image   || ''
      config.catalogMedia.story_image       = rawMedia.story_image       || ''
    }
  } catch (error) {
    console.error('Error loading configuration:', error)
    // No forzar selección de todas las categorías en caso de error
    // config.inventoryVisibility.visibleCategories permanecerá vacío
  }
}

// Save configuration to backend
const saveConfiguration = async () => {
  isSaving.value = true
  
  try {
    // Validar plantilla antes de guardar
    const validTemplate = getValidTemplate(config.brandIdentity.template)
    if (validTemplate !== config.brandIdentity.template) {
      config.brandIdentity.template = validTemplate
    }
    
    // Transformar estructura del frontend al formato que espera el backend
    const payload = {
      storeActive: config.storeActive,
      brandIdentity: {
        logo: config.brandIdentity.logo,
        banner: config.brandIdentity.banner,
        primaryColor: config.brandIdentity.primaryColor,
        template: config.brandIdentity.template
      },
      products: {
        visibleCategories: config.inventoryVisibility.visibleCategories, // ← MAPEO CORRECTO
        showPrices: true, // Por ahora hardcodeado
        hideOutOfStock: config.inventoryVisibility.hideOutOfStock
      },
      orders: {
        allowOrders: true, // Por ahora hardcodeado
        whatsappNumber: config.ordersConfig.whatsappNumber,
        customMessage: config.ordersConfig.customMessage
      },
      businessRules: {
        deliveryCost: config.businessRules.deliveryCost,
        minimumOrder: config.businessRules.minimumOrder,
        syncWithCashRegister: config.businessRules.syncWithCashRegister
      },
      // Medios contextuales por diseño activo
      catalog_media: {
        hero_images: config.catalogMedia.hero_images?.filter(Boolean) ?? [],
        lookbook_images: config.catalogMedia.lookbook_images?.filter(Boolean) ?? [],
        lookbook_video: config.catalogMedia.lookbook_video || null,
        bento_main: config.catalogMedia.bento_main || null,
        bento_detail: config.catalogMedia.bento_detail || null,
        bento_secondary: config.catalogMedia.bento_secondary || null,
        editorial_image: config.catalogMedia.editorial_image || null,
        story_image: config.catalogMedia.story_image || null
      }
    }
    
    
    const response = await apiClient.post('/web-catalog/config', payload)
    
    if (response.data.success) {
      toastMessage.title = '¡Guardado!'
      toastMessage.description = 'Configuración actualizada. Vista previa recargada.'
      showSuccessToast.value = true
      setTimeout(() => {
        showSuccessToast.value = false
      }, 3000)

      // Actualizar caché local para que el catálogo cargue instantáneamente
      try {
        const cacheData = {
          template: config.brandIdentity.template,
          primary_color: config.brandIdentity.primaryColor,
          logo_url: config.brandIdentity.logo || '',
          banner_url: config.brandIdentity.banner || '',
          whatsapp_number: config.ordersConfig.whatsappNumber || '',
          currency_symbol: '$',
          delivery_cost: parseFloat(config.businessRules.deliveryCost || 0),
          min_order_value: parseFloat(config.businessRules.minimumOrder || 0),
          custom_message: config.ordersConfig.customMessage || 'Hola, quiero hacer el siguiente pedido:',
          store_name: appStore.systemSettings?.store_name || 'Mi Tienda',
          ai_color_palette: aiBrandData.value?.color_palette || null,
          ai_fonts: aiBrandData.value?.fonts || null,
          ai_banner_texts: aiBrandData.value?.banner_texts || null,
          ai_about_us: aiBrandData.value?.about_us || null,
          ai_value_messages: aiBrandData.value?.value_messages || null,
          ai_announcements: aiBrandData.value?.announcements || null,
          ai_cross_sell_messages: aiBrandData.value?.cross_sell_messages || null,
          ai_layout_config: aiBrandData.value?.layout_config || null,
          catalog_media: {
            hero_images: config.catalogMedia.hero_images?.filter(Boolean) ?? [],
            lookbook_images: config.catalogMedia.lookbook_images?.filter(Boolean) ?? [],
            lookbook_video: config.catalogMedia.lookbook_video || null,
            bento_main: config.catalogMedia.bento_main || null,
            bento_detail: config.catalogMedia.bento_detail || null,
            bento_secondary: config.catalogMedia.bento_secondary || null,
            editorial_image: config.catalogMedia.editorial_image || null,
            story_image: config.catalogMedia.story_image || null
          }
        }
        localStorage.setItem('pos_catalog_config_cache', JSON.stringify(cacheData))
      } catch (e) {}

      // Recargar iframe automáticamente después de guardar
      refreshPreview()
    }
  } catch (error) {
    console.error('Error saving configuration:', error)
    showError('Error al guardar la configuración.')
  } finally {
    isSaving.value = false
  }
}

// ==================== AI BRAND METHODS ====================

// Open reset modal (replaces confirm())
const resetAiBrand = () => {
  resetStep.value = 1
  showResetModal.value = true
}

// Download current brand identity as JSON backup and advance to step 2
const downloadBackupAndContinue = () => {
  isDownloadingBackup.value = true
  try {
    const storeName = (appStore.systemSettings?.store_name || 'mi-tienda')
      .toLowerCase().replace(/\s+/g, '-').replace(/[^a-z0-9-]/g, '')
    const timestamp = new Date().toISOString().slice(0, 10)

    const backup = {
      version: '1.0',
      exported_at: new Date().toISOString(),
      store_name: appStore.systemSettings?.store_name || '',
      business_description: aiBrandDescription.value || '',
      color_palette: aiBrandData.value?.color_palette || null,
      fonts: aiBrandData.value?.fonts || null,
      recommended_template: aiBrandData.value?.recommended_template || null,
      banner_texts: aiBrandData.value?.banner_texts || null,
      about_us: aiBrandData.value?.about_us || null,
      value_messages: aiBrandData.value?.value_messages || null,
      announcements: aiBrandData.value?.announcements || null,
      cross_sell_messages: aiBrandData.value?.cross_sell_messages || null,
      layout_config: aiBrandData.value?.layout_config || null,
    }

    const blob = new Blob([JSON.stringify(backup, null, 2)], { type: 'application/json' })
    const url = URL.createObjectURL(blob)
    const a = document.createElement('a')
    a.href = url
    a.download = `identidad-${storeName}-${timestamp}.json`
    a.click()
    URL.revokeObjectURL(url)

    // Advance to confirmation step
    resetStep.value = 2
  } finally {
    isDownloadingBackup.value = false
  }
}

// Execute the actual reset
const executeReset = async () => {
  isResetting.value = true
  try {
    await apiClient.post('/web-catalog/ai-brand/reset')
  } catch (error) {
    // Silent: reset local state regardless
  }

  aiBrandData.value = null
  aiBrandDescription.value = ''
  showColorRefinement.value = false
  colorRefinementPrompt.value = ''
  selectedFontPairId.value = null
  showResetModal.value = false

  // Limpiar draft e imágenes de onboarding
  try { localStorage.removeItem(ONBOARDING_DRAFT_KEY) } catch (e) {}
  try { localStorage.removeItem('ai_design_preview') } catch (e) {}
  try { localStorage.removeItem('pos_catalog_config_cache') } catch (e) {}
  uploadSubStep.value = 1
  onboardingImages.value = {
    hero_images: ['', '', ''],
    editorial_image: '',
    lookbook_images: ['', '', ''],
    bento_main: '',
    bento_detail: '',
    bento_secondary: '',
    story_image: ''
  }

  // Limpiar imágenes también en la base de datos
  config.catalogMedia.hero_images     = ['', '', '']
  config.catalogMedia.lookbook_images = ['', '', '', '']
  config.catalogMedia.lookbook_video  = ''
  config.catalogMedia.bento_main      = ''
  config.catalogMedia.bento_detail    = ''
  config.catalogMedia.bento_secondary = ''
  config.catalogMedia.editorial_image = ''
  config.catalogMedia.story_image     = ''
  try {
    await apiClient.post('/web-catalog/config', {
      storeActive: false,
      brandIdentity: { logo: config.brandIdentity.logo, banner: '', primaryColor: config.brandIdentity.primaryColor, template: config.brandIdentity.template },
      products: { visibleCategories: config.inventoryVisibility.visibleCategories, showPrices: true, hideOutOfStock: false },
      orders: { allowOrders: true, whatsappNumber: config.ordersConfig.whatsappNumber, customMessage: config.ordersConfig.customMessage },
      businessRules: { deliveryCost: 0, minimumOrder: 0, syncWithCashRegister: false },
      catalog_media: null
    })
  } catch (e) { /* silent */ }

  // Volver a la pantalla de bienvenida del onboarding
  showConfigPanel.value = false
  showPreviewPanel.value = false
  setTimeout(() => { catalogUiMode.value = 'welcome' }, 200)

  toastIsError.value = false
  toastMessage.title = 'Identidad restablecida'
  toastMessage.description = 'Puedes empezar de cero describiendo tu negocio.'
  showSuccessToast.value = true
  setTimeout(() => showSuccessToast.value = false, 4000)
  isResetting.value = false
}

// Handle file drop on restore zone
const handleBackupDrop = (event) => {
  const file = event.dataTransfer?.files?.[0]
  if (file) parseBackupFile(file)
}

// Handle file input change
const handleBackupFileSelect = (event) => {
  const file = event.target.files?.[0]
  if (file) parseBackupFile(file)
  // Reset input so same file can be selected again
  if (backupFileInput.value) backupFileInput.value.value = ''
}

// Parse and validate backup JSON
const parseBackupFile = (file) => {
  if (!file.name.endsWith('.json')) {
    toastMessage.title = 'Archivo no válido'
    toastMessage.description = 'Por favor selecciona un archivo .json de copia de seguridad.'
    showSuccessToast.value = true
    setTimeout(() => showSuccessToast.value = false, 4000)
    return
  }
  const reader = new FileReader()
  reader.onload = (e) => {
    try {
      const data = JSON.parse(e.target.result)
      if (!data.version || !data.exported_at) {
        throw new Error('Formato no reconocido')
      }
      restoreData.value = data
      restoreFileName.value = file.name
    } catch {
      toastMessage.title = 'Archivo inválido'
      toastMessage.description = 'El archivo no es una copia de seguridad válida del sistema.'
      showSuccessToast.value = true
      setTimeout(() => showSuccessToast.value = false, 4000)
    }
  }
  reader.readAsText(file)
}

// Send backup data to backend and restore
const executeRestore = async () => {
  if (!restoreData.value) return
  isRestoring.value = true
  try {
    const response = await apiClient.post('/web-catalog/ai-brand/restore', restoreData.value)
    if (response.data.success) {
      // Reload ALL config so colors, template and AI data update without page refresh
      await loadConfiguration()
      await loadAiBrandData()
      refreshPreview()
      showRestoreModal.value = false
      restoreData.value = null
      restoreFileName.value = ''

      toastIsError.value = false
      toastMessage.title = 'Plantilla restaurada'
      toastMessage.description = 'Tu identidad visual ha sido recuperada exitosamente.'
      showSuccessToast.value = true
      setTimeout(() => showSuccessToast.value = false, 4000)
    } else {
      throw new Error(response.data.message || 'Error al restaurar')
    }
  } catch (error) {
    const msg = error.response?.data?.message
      || error.response?.data?.errors && Object.values(error.response.data.errors).flat().join(' ')
      || error.message
      || 'No se pudo restaurar la plantilla. Intenta de nuevo.'
    toastIsError.value = true
    toastMessage.title = 'Error al restaurar'
    toastMessage.description = msg
    showSuccessToast.value = true
    setTimeout(() => { showSuccessToast.value = false; toastIsError.value = false }, 6000)
  } finally {
    isRestoring.value = false
  }
}

// Load existing AI brand data
const loadAiBrandData = async () => {
  try {
    const response = await apiClient.get('/web-catalog/ai-brand/data')
    if (response.data.success && response.data.data) {
      aiBrandData.value = response.data.data
      if (response.data.data.business_description) {
        aiBrandDescription.value = response.data.data.business_description
      }
    }
  } catch (error) {
    // Silent fail - AI data is optional
  }
}

// Generate 5 brand identities with Groq AI and open the choosing carousel
const generateAiBrand = async () => {
  if (!aiBrandDescription.value || aiBrandDescription.value.trim().length < 10) {
    toastMessage.title = 'Descripción muy corta'
    toastMessage.description = 'Escribe al menos 10 caracteres describiendo tu negocio.'
    showSuccessToast.value = true
    setTimeout(() => showSuccessToast.value = false, 4000)
    return
  }

  isGeneratingBrand.value = true
  aiGenerationProgress.value = 'Generando 5 diseños únicos para tu marca...'
  generatedDesigns.value = []
  designsReady.value = 0
  currentDesignIdx.value = 0

  // Transition to choosing state immediately so the user sees the progress
  catalogUiMode.value = 'choosing'

  try {
    // Generate 5 designs in parallel — each slot gets a DISTINCT mood direction
    const requests = Array.from({ length: 5 }, (_, i) =>
      apiClient.post('/web-catalog/ai-brand/generate', {
        business_description: aiBrandDescription.value.trim(),
        slot: i
      }).then(res => {
        if (res.data?.success && res.data?.data) {
          const design = {
            ...res.data.data,
            business_description: aiBrandDescription.value,
            generated_at: new Date().toISOString()
          }
          // Push as they arrive so the phone preview updates progressively
          generatedDesigns.value = [...generatedDesigns.value, design]
          designsReady.value++
        }
        return null
      }).catch(() => null)
    )

    await Promise.allSettled(requests)

    if (generatedDesigns.value.length === 0) {
      toastMessage.title = 'Error'
      toastMessage.description = 'No se pudo generar ningún diseño. Intenta de nuevo.'
      toastIsError.value = true
      showSuccessToast.value = true
      setTimeout(() => showSuccessToast.value = false, 5000)
      catalogUiMode.value = 'brief-only'
    }
  } catch (error) {
    toastMessage.title = 'Error de conexión'
    toastMessage.description = 'No se pudo conectar con el servicio de IA.'
    toastIsError.value = true
    showSuccessToast.value = true
    setTimeout(() => showSuccessToast.value = false, 5000)
    catalogUiMode.value = 'brief-only'
  } finally {
    isGeneratingBrand.value = false
    aiGenerationProgress.value = ''
  }
}

// Apply the chosen design and transition to the full configured view
const chooseDesign = async (idx) => {
  const design = generatedDesigns.value[idx]
  if (!design) return

  isChoosingApplying.value = true
  // Limpiar preview temporal
  try { localStorage.removeItem('ai_design_preview') } catch (e) {}

  try {
    // Save the chosen design via the apply endpoint
    await apiClient.post('/web-catalog/ai-brand/apply', {
      brand_data: design,
      apply_colors: true,
      apply_template: true
    })

    // Reflect locally for instant preview
    aiBrandData.value = design
    if (design.color_palette?.primary) {
      config.brandIdentity.primaryColor = design.color_palette.primary
    }
    if (design.recommended_template) {
      config.brandIdentity.template = getValidTemplate(design.recommended_template)
    }

    // Cambiar a 'upload-images' para el paso de fotos del hook
    uploadSubStep.value = 1
    catalogUiMode.value = 'upload-images'

    // Restaurar borrador si hay uno guardado
    restoreOnboardingDraft()
    // Inicializar preview del onboarding con el diseño elegido
    await nextTick()
    updateOnboardingPreview()

    toastMessage.title = 'Diseño aplicado'
    toastMessage.description = 'Ahora personaliza tu tienda con tus propias fotos.'
    toastIsError.value = false
    showSuccessToast.value = true
    setTimeout(() => showSuccessToast.value = false, 4000)
  } catch (error) {
    toastMessage.title = 'Error al aplicar'
    toastMessage.description = 'No se pudo guardar el diseño. Intenta de nuevo.'
    toastIsError.value = true
    showSuccessToast.value = true
    setTimeout(() => showSuccessToast.value = false, 5000)
  } finally {
    isChoosingApplying.value = false
  }
}

// Apply AI colors to catalog config
const applyAiColors = () => {
  if (aiBrandData.value?.color_palette?.primary) {
    config.brandIdentity.primaryColor = aiBrandData.value.color_palette.primary
    toastMessage.title = 'Color aplicado'
    toastMessage.description = 'El color primario de IA se aplicó a tu tienda.'
    showSuccessToast.value = true
    setTimeout(() => showSuccessToast.value = false, 3000)
  }
}

// Regenerate only colors with refinement hint
const regenerateColors = async () => {
  if (isRegeneratingColors.value) return
  isRegeneratingColors.value = true
  try {
    const combinedPrompt = aiBrandDescription.value.trim()
      + (colorRefinementPrompt.value.trim() ? '. Preferencias de color adicionales: ' + colorRefinementPrompt.value.trim() : '')
    const response = await apiClient.post('/web-catalog/ai-brand/generate', {
      business_description: combinedPrompt
    })
    if (response.data.success) {
      aiBrandData.value = {
        ...aiBrandData.value,
        color_palette: response.data.data.color_palette,
        generated_at: new Date().toISOString()
      }
      if (response.data.data.color_palette?.primary) {
        config.brandIdentity.primaryColor = response.data.data.color_palette.primary
      }
      showColorRefinement.value = false
      colorRefinementPrompt.value = ''
      toastMessage.title = 'Paleta regenerada'
      toastMessage.description = 'Los colores se actualizaron con tus preferencias.'
      showSuccessToast.value = true
      setTimeout(() => showSuccessToast.value = false, 3000)
    }
  } catch (error) {
    toastMessage.title = 'Error al regenerar'
    toastMessage.description = 'No se pudo regenerar la paleta. Intenta de nuevo.'
    showSuccessToast.value = true
    setTimeout(() => showSuccessToast.value = false, 4000)
  } finally {
    isRegeneratingColors.value = false
  }
}

// Apply AI recommended template
const applyAiTemplate = () => {
  if (aiBrandData.value?.recommended_template) {
    const template = aiBrandData.value.recommended_template
    const validTemplate = getValidTemplate(template)
    config.brandIdentity.template = validTemplate
    toastMessage.title = 'Plantilla aplicada'
    toastMessage.description = `Se seleccionó "${validTemplate === 'visual-story' ? 'Historia Visual' : validTemplate === 'speed-market' ? 'Mercado Rápido' : 'Cuadrícula Moderna'}".`
    showSuccessToast.value = true
    setTimeout(() => showSuccessToast.value = false, 3000)
  }
}

// Apply ALL AI settings at once
const applyAllAiSettings = async () => {
  if (!aiBrandData.value) return

  applyAiColors()
  applyAiTemplate()

  // Save config after applying
  await saveConfiguration()
}

// Voice-to-Text (Web Speech API)
const startVoiceRecording = () => {
  if (!('webkitSpeechRecognition' in window) && !('SpeechRecognition' in window)) {
    toastMessage.title = 'No soportado'
    toastMessage.description = 'Tu navegador no soporta reconocimiento de voz. Usa Chrome.'
    showSuccessToast.value = true
    setTimeout(() => showSuccessToast.value = false, 4000)
    return
  }

  const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition
  voiceRecognition.value = new SpeechRecognition()
  voiceRecognition.value.lang = 'es-CO'
  voiceRecognition.value.continuous = true
  voiceRecognition.value.interimResults = true

  voiceRecognition.value.onstart = () => {
    isRecordingVoice.value = true
  }

  voiceRecognition.value.onresult = (event) => {
    let transcript = ''
    for (let i = 0; i < event.results.length; i++) {
      transcript += event.results[i][0].transcript
    }
    aiBrandDescription.value = transcript
  }

  voiceRecognition.value.onerror = () => {
    isRecordingVoice.value = false
  }

  voiceRecognition.value.onend = () => {
    isRecordingVoice.value = false
  }

  voiceRecognition.value.start()
}

const stopVoiceRecording = () => {
  if (voiceRecognition.value) {
    voiceRecognition.value.stop()
    isRecordingVoice.value = false
  }
}
</script>

<style scoped>
@keyframes fade-in {
  from { opacity: 0; transform: translateY(10px); }
  to { opacity: 1; transform: translateY(0); }
}
.animate-fade-in {
  animation: fade-in 0.3s ease-out forwards;
}

@keyframes slide-up {
  from { transform: translateY(100%); opacity: 0; }
  to { transform: translateY(0); opacity: 1; }
}
.animate-slide-up {
  animation: slide-up 0.3s ease-out forwards;
}

@keyframes modalSlideIn {
  from { opacity: 0; transform: translateY(12px) scale(0.97); }
  to { opacity: 1; transform: translateY(0) scale(1); }
}

@keyframes loading {
  0%   { transform: translateX(-100%); }
  50%  { transform: translateX(0%); }
  100% { transform: translateX(100%); }
}

/* Simular viewport móvil real */
.mobile-preview-viewport {
  width: 375px !important;
  max-width: 375px !important;
}

/* Forzar estilos móviles en la preview */
.mobile-preview-viewport * {
  max-width: 100%;
}

/* Line clamp para el body preview en el modal */
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>
