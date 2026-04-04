const fs = require('fs');
const content = fs.readFileSync('src/components/LoginView.vue', 'utf8');

const newTemplate = `<template>
  <div class="flex min-h-screen bg-slate-50 font-sans" style="height: 100%; min-height: 100%;">
    
    <!-- LADO IZQUIERDO: Panel de Marca Premium (30% Asimetría Moderna) -->
    <div class="hidden lg:flex lg:w-[32%] relative overflow-hidden bg-slate-900 flex-col justify-between p-10 xl:p-12 text-white shadow-2xl z-10">
      <div class="absolute inset-0 bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900"></div>
      <div class="absolute inset-0 bg-[radial-gradient(#00C896_1px,transparent_1px)] [background-size:24px_24px] opacity-[0.06]"></div>
      
      <div class="relative z-10 flex items-center gap-3">
        <div class="w-12 h-12 rounded-2xl bg-[#00C896]/20 border border-[#00C896]/30 flex items-center justify-center backdrop-blur-md shadow-lg shadow-[#00C896]/20">
          <svg class="w-7 h-7 text-[#00C896]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
          </svg>
        </div>
        <div>
          <h1 class="text-2xl font-black tracking-tight text-white">105 POS Pro</h1>
          <p class="text-xs text-[#00C896] font-bold uppercase tracking-widest mt-0.5">Plataforma Cloud</p>
        </div>
      </div>

      <div class="relative z-10 flex flex-col gap-5 mt-12 mb-auto pt-10">
        <div class="group flex items-center gap-4 bg-slate-800/40 rounded-2xl p-4 xl:p-5 border border-white/5 backdrop-blur-md transition-all duration-300 hover:bg-slate-800/80 hover:-translate-y-1 hover:border-white/10 hover:shadow-xl hover:shadow-black/20">
          <div class="w-14 h-14 bg-[#00C896]/20 rounded-xl flex flex-shrink-0 items-center justify-center border border-[#00C896]/30 shadow-[0_0_15px_rgba(0,200,150,0.15)] group-hover:scale-110 transition-transform duration-300">
            <svg class="w-7 h-7 text-[#00C896]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
          </div>
          <div>
            <p class="text-3xl font-black text-white tracking-tighter">99.9%</p>
            <p class="text-sm text-slate-400 font-semibold mt-0.5">Uptime Garantizado</p>
          </div>
        </div>
        
        <div class="group flex items-center gap-4 bg-slate-800/40 rounded-2xl p-4 xl:p-5 border border-white/5 backdrop-blur-md transition-all duration-300 hover:bg-slate-800/80 hover:-translate-y-1 hover:border-white/10 hover:shadow-xl hover:shadow-black/20">
           <div class="w-14 h-14 bg-indigo-500/20 rounded-xl flex flex-shrink-0 items-center justify-center border border-indigo-500/30 shadow-[0_0_15px_rgba(99,102,241,0.15)] group-hover:scale-110 transition-transform duration-300">
            <svg class="w-7 h-7 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
          </div>
          <div>
            <p class="text-xl font-bold text-white tracking-tight mt-1">Conexión Segura</p>
            <p class="text-sm text-slate-400 font-semibold mt-0.5">Datos Encriptados TLS</p>
          </div>
        </div>
      </div>

      <div class="relative z-10 border-t border-slate-800/80 pt-8">
        <div class="flex items-center gap-1 mb-4">
           <template v-for="i in 5" :key="i">
              <svg class="w-5 h-5 text-[#00C896] drop-shadow-md" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
           </template>
        </div>
        <p class="text-slate-300 text-[15px] italic mb-6 leading-relaxed font-medium">"El diseño más limpio y rápido. Controlar las ventas de mis sucursales nunca fue tan fácil como con 105 POS."</p>
        <div class="flex items-center gap-4">
           <div class="w-12 h-12 rounded-full bg-gradient-to-br from-[#00C896] to-emerald-700 flex items-center justify-center text-lg font-black shadow-lg shadow-[#00C896]/30 border-2 border-slate-900 ring-2 ring-white/10 text-white">
             MJ
           </div>
           <div>
             <p class="text-[15px] font-bold text-white tracking-wide">María José G.B.</p>
             <p class="text-xs text-[#00C896] font-bold mt-0.5">Dueña de Múltiples Minimarkets</p>
           </div>
        </div>
      </div>
    </div>

    <!-- LADO DERECHO: Formulario (68%) -->
    <div class="flex-1 lg:w-[68%] flex flex-col px-6 py-10 sm:px-8 lg:px-12 xl:px-20 bg-white relative">
      <div class="absolute inset-0 bg-[#f8fafc] opacity-60"></div>
      <div class="absolute inset-0 bg-[linear-gradient(to_right,#e2e8f0_1px,transparent_1px),linear-gradient(to_bottom,#e2e8f0_1px,transparent_1px)] bg-[size:4rem_4rem] [mask-image:radial-gradient(ellipse_60%_50%_at_50%_0%,#000_70%,transparent_100%)] opacity-30"></div>
      
      <div class="w-full flex justify-end relative z-20 mb-8 sm:mb-16">
        <p class="text-sm font-semibold text-slate-500 bg-white/80 px-4 py-2 rounded-xl border border-slate-100 shadow-sm backdrop-blur-sm">
          ¿Nuevo en 105 POS? 
          <router-link to="/register" class="font-bold text-slate-900 hover:text-[#00C896] transition-colors ml-1">
            Crea tu cuenta ahora
          </router-link>
        </p>
      </div>

      <div class="w-full max-w-[460px] mx-auto relative z-10 flex-1 flex flex-col justify-center pb-20">
        
        <div class="flex lg:hidden items-center justify-center gap-3 mb-10">
          <div class="w-12 h-12 rounded-2xl bg-[#00C896]/10 border border-[#00C896]/20 flex items-center justify-center shadow-inner">
             <svg class="w-7 h-7 text-[#00C896]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
          </div>
          <div>
             <h1 class="text-2xl font-black text-slate-900 tracking-tight leading-none">105 POS Pro</h1>
             <p class="text-[10px] text-slate-500 font-bold uppercase tracking-widest mt-1">Sistema de Ventas</p>
          </div>
        </div>

        <div class="mb-10 text-center lg:text-left">
          <h2 class="text-3xl font-black text-slate-900 mb-3 tracking-tight">Te damos la bienvenida</h2>
          <p class="text-slate-500 font-semibold text-base">Ingresa a tu panel de control para continuar</p>
        </div>

        <div v-if="message.text" 
             :class="message.type === 'error' ? 'bg-rose-50 text-rose-700 border-rose-200 shadow-rose-100/50' : 
                     message.type === 'info' ? 'bg-indigo-50 text-indigo-700 border-indigo-200 shadow-indigo-100/50' : 
                     'bg-[#00C896]/10 text-emerald-800 border-[#00C896]/30 shadow-[#00C896]/10'" 
             class="mb-8 p-4 rounded-2xl border text-sm font-bold flex items-start gap-3 animate-fade-in shadow-lg">
          <span class="leading-relaxed pt-0.5">{{ message.text }}</span>
        </div>

        <button 
           @click="loginWithGoogle" 
           :disabled="isGoogleLoading"
           class="w-full flex items-center justify-center gap-3 px-6 py-4 bg-white border-2 border-slate-200 hover:border-slate-300 hover:bg-slate-50/80 rounded-2xl text-slate-700 font-black shadow-sm transition-all duration-300 focus:ring-4 focus:ring-[#00C896]/10 focus:outline-none disabled:opacity-70 disabled:cursor-not-allowed group active:scale-[0.98]"
        >
          <img v-if="!isGoogleLoading" src="https://www.svgrepo.com/show/475656/google-color.svg" alt="Google" class="w-6 h-6 group-hover:scale-110 transition-transform duration-300" />
          <span class="text-[15px] tracking-wide">{{ isGoogleLoading ? 'Conectando seguro...' : 'Continuar con Google' }}</span>
        </button>

        <div class="relative my-10">
          <div class="absolute inset-0 flex items-center"><div class="w-full border-t-2 border-slate-100"></div></div>
          <div class="relative flex justify-center text-xs font-bold tracking-widest uppercase"><span class="px-5 bg-[#f8fafc] text-slate-400">O ingresa con tu correo</span></div>
        </div>

        <form @submit.prevent="handleLogin" class="space-y-6">
          <div class="space-y-2.5">
            <label class="block text-sm font-bold text-slate-700">Correo Electrónico</label>
            <div class="relative">
              <input 
                v-model="credentials.email" 
                type="email" 
                placeholder="tu@empresa.com"
                class="w-full px-5 py-4 bg-white border-2 border-slate-200 rounded-2xl text-slate-900 placeholder-slate-400 focus:bg-white focus:outline-none focus:border-[#00C896] focus:ring-4 focus:ring-[#00C896]/10 transition-all font-bold shadow-sm"
                :class="{'border-rose-400 ring-4 ring-rose-500/10 bg-rose-50/30': errors.email}"
              >
            </div>
            <p v-if="errors.email" class="text-sm font-bold text-rose-500 ml-1 mt-1.5 flex items-center gap-1.5">
              {{ errors.email }}
            </p>
          </div>

          <div class="space-y-2.5">
            <div class="flex items-center justify-between">
              <label class="block text-sm font-bold text-slate-700">Contraseña</label>
              <router-link to="/forgot-password" class="text-sm font-bold text-[#00C896] hover:text-emerald-700 hover:underline underline-offset-4 transition-all">
                ¿Olvidaste tu contraseña?
              </router-link>
            </div>
            <div class="relative group">
              <input 
                v-model="credentials.password" 
                :type="showPassword ? 'text' : 'password'" 
                placeholder="••••••••"
                class="w-full px-5 pr-14 py-4 bg-white border-2 border-slate-200 rounded-2xl text-slate-900 placeholder-slate-400 focus:bg-white focus:outline-none focus:border-[#00C896] focus:ring-4 focus:ring-[#00C896]/10 transition-all font-bold shadow-sm"
                :class="{'border-rose-400 ring-4 ring-rose-500/10 bg-rose-50/30': errors.password}"
              >
              <button 
                type="button" 
                @click="showPassword = !showPassword" 
                class="absolute inset-y-0 right-0 pr-4 flex items-center text-slate-400 hover:text-[#00C896] transition-colors focus:outline-none font-bold text-xs"
              >
                 {{ showPassword ? 'OCULTAR' : 'VER' }}
              </button>
            </div>
            <p v-if="errors.password" class="text-sm font-bold text-rose-500 ml-1 mt-1.5 flex items-center gap-1.5">
              {{ errors.password }}
            </p>
          </div>

          <div class="flex items-center pt-2">
            <input id="remember" type="checkbox" v-model="credentials.remember" class="w-5 h-5 rounded-md border-2 border-slate-300 text-[#00C896] focus:ring-[#00C896]/20 transition-all cursor-pointer">
            <label for="remember" class="ml-3 block text-sm font-bold text-slate-600 cursor-pointer select-none">Recordar mi sesión en este dispositivo</label>
          </div>

          <div class="pt-6">
            <button 
              type="submit" 
              :disabled="loading"
              class="w-full py-4 bg-slate-900 border-2 border-slate-900 text-white rounded-2xl font-black text-lg shadow-xl shadow-slate-900/30 hover:-translate-y-1 hover:shadow-2xl hover:shadow-[#00C896]/25 hover:bg-slate-800 hover:border-slate-800 active:translate-y-0 transition-all duration-300 flex justify-center items-center gap-3 group disabled:opacity-75 disabled:cursor-not-allowed disabled:hover:translate-y-0 focus:ring-4 focus:ring-slate-900/20 focus:outline-none"
            >
              <span>{{ loading ? 'Iniciando Sesión...' : 'Ingresar al Panel' }}</span>
              <svg v-if="!loading" class="w-6 h-6 text-[#00C896] group-hover:translate-x-1.5 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
            </button>
          </div>
        </form>
        
        <div v-show="isDevelopment" class="mt-8 flex gap-3 justify-center opacity-50 hover:opacity-100 transition-opacity">
           <button @click="setDemoCredentials('admin')" class="text-xs px-4 py-2 bg-slate-200/50 font-black text-slate-700 rounded-xl hover:bg-slate-200 transition-colors uppercase tracking-wider">Demo Admin</button>
           <button @click="setDemoCredentials('cajero')" class="text-xs px-4 py-2 bg-slate-200/50 font-black text-slate-700 rounded-xl hover:bg-slate-200 transition-colors uppercase tracking-wider">Demo Cajero</button>
        </div>
      </div>

      <!-- Security Footer Absoluto -->
      <div class="absolute bottom-6 left-0 right-0 flex justify-center gap-6 text-xs font-bold text-slate-400 hidden sm:flex">
          <div class="flex items-center gap-1.5 hover:text-[#00C896] transition-colors cursor-default bg-white px-3 py-1.5 rounded-full shadow-sm border border-slate-100"><svg class="w-4 h-4 text-[#00C896]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg> Conexión segura</div>
          <div class="flex items-center gap-1.5 hover:text-[#00C896] transition-colors cursor-default bg-white px-3 py-1.5 rounded-full shadow-sm border border-slate-100"><svg class="w-4 h-4 text-[#00C896]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" /></svg> Encriptación SSL/TLS</div>
      </div>
    </div>
  </div>
</template>`;

const startIndex = content.indexOf('<template>');
const endIndex = content.lastIndexOf('</template>') + '</template>'.length;

if (startIndex !== -1 && endIndex !== -1) {
  const newContent = newTemplate + "\n\n" + content.substring(endIndex);
  fs.writeFileSync('src/components/LoginView.vue', newContent, 'utf8');
  console.log("LOGIN ACTUALIZADO CON ÉXITO");
} else {
  console.log("NO SE ENCONTRARON LAS ETIQUETAS TEMPLATE");
}