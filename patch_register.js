const fs = require('fs');

let content = fs.readFileSync('src/views/SaasRegister.vue', 'utf8');

// Replace LEFT PANEL
const leftStartRef = "<!-- 📸 LEFT PANEL: Premium Branding (45%) -->";
const rightStartRef = "<!-- 📝 RIGHT PANEL: Formulario Premium (55%) -->";

let startIndex = content.indexOf(leftStartRef);
let endIndex = content.indexOf(rightStartRef);

if (startIndex === -1 || endIndex === -1) {
    // try fallback regex or substrings
    startIndex = content.indexOf('LEFT PANEL');
    endIndex = content.indexOf('RIGHT PANEL');
}

const newLeftPanel = `<!-- LADO IZQUIERDO: Panel de Marca Premium (30% Asimetría Moderna) -->
    <div class="hidden lg:flex lg:fixed lg:left-0 lg:top-0 lg:w-[32%] relative overflow-hidden bg-slate-900 flex-col justify-between p-10 xl:p-12 text-white shadow-2xl z-10" style="height: 100%;">
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

    `;

if (startIndex !== -1 && endIndex !== -1) {
    let pre = content.substring(0, startIndex);
    let post = content.substring(endIndex);
    
    // Now fix the RIGHT panel width in post
    post = post.replace('lg:ml-[45%]', 'lg:ml-[32%]');
    post = post.replace('lg:w-[55%]', 'lg:w-[68%]');
    
    // Set explicit colors for emerald branding in post form to match login view
    post = post.replaceAll('text-emerald-400', 'text-[#00C896]');
    post = post.replaceAll('text-emerald-500', 'text-[#00C896]');
    post = post.replaceAll('bg-emerald-600', 'bg-slate-900');
    post = post.replaceAll('hover:bg-emerald-700', 'hover:bg-slate-800');
    
    // Replace the right panel background completely:
    const oldBgPattern = '<div class="absolute inset-0 bg-[radial-gradient(#e5e7eb_1px,transparent_1px)] [background-size:20px_20px] opacity-40 pointer-events-none"></div>';
    const newBgPattern = `
      <div class="absolute inset-0 bg-[#f8fafc] opacity-60 pointer-events-none"></div>
      <div class="absolute inset-0 bg-[linear-gradient(to_right,#e2e8f0_1px,transparent_1px),linear-gradient(to_bottom,#e2e8f0_1px,transparent_1px)] bg-[size:4rem_4rem] [mask-image:radial-gradient(ellipse_60%_50%_at_50%_0%,#000_70%,transparent_100%)] opacity-30 pointer-events-none"></div>`;
    
    // Only replace if found
    if (post.includes(oldBgPattern)) {
       post = post.replace(oldBgPattern, newBgPattern);
    } else {
       post = post.replace(
         '<div class="absolute inset-0 bg-[radial-gradient',
         newBgPattern + '\n      <!--'
       );
    }
    
    // Also change Google login button text to match
    post = post.replace('class="w-full h-14', 'class="w-full py-4');

    content = pre + newLeftPanel + post;

    fs.writeFileSync('src/views/SaasRegister.vue', content, 'utf8');
    console.log("REGISTER PANEL ACTUALIZADO!");
} else {
    console.log("NOT FOUND", startIndex, endIndex);
}
