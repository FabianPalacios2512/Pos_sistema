<template>
  <!-- 🎯 CREDIT PORTAL - Clean Premium Design -->
  <div class="credit-portal min-h-screen font-['Inter',system-ui,sans-serif] antialiased">
    
    <!-- ==================== LOGIN SCREEN ==================== -->
    <div v-if="!isAuthenticated" class="min-h-screen bg-slate-50 dark:bg-[#0C0C0E] flex flex-col">
      
      <!-- Header -->
      <header class="px-6 py-6 lg:py-8">
        <div class="max-w-md mx-auto flex items-center justify-center lg:justify-start gap-3">
          <img src="/logo.png" alt="105 POS" class="h-10 w-auto" />
          <div>
            <h1 class="text-lg font-semibold text-slate-900 dark:text-white tracking-tight">Credit Portal</h1>
            <p class="text-xs text-slate-500 dark:text-slate-400">{{ businessName }}</p>
          </div>
        </div>
      </header>
      
      <!-- Form Container -->
      <main class="flex-1 flex items-center justify-center px-6 py-8">
        <div class="w-full max-w-md">
          
          <!-- Form Card -->
          <div class="bg-white dark:bg-[#161618] rounded-2xl border border-slate-200 dark:border-slate-800 shadow-xl shadow-slate-200/50 dark:shadow-black/20 overflow-hidden">
            
            <form @submit.prevent="handleLogin" class="p-8">
              
              <!-- Header -->
              <div class="text-center mb-8">
                <h2 class="text-2xl font-bold text-slate-900 dark:text-white tracking-tight">Accede a tu cuenta</h2>
                <p class="text-sm text-slate-500 dark:text-slate-400 mt-2">Ingresa tus credenciales para continuar</p>
              </div>
              
              <!-- Inputs -->
              <div class="space-y-5">
                
                <!-- Credit ID Input -->
                <div class="space-y-2">
                  <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider">
                    ID de Crédito
                  </label>
                  <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                      <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5z"/>
                      </svg>
                    </div>
                    <input
                      v-model="loginForm.creditId"
                      type="text"
                      placeholder="CRD-000001"
                      class="w-full pl-12 pr-4 py-3.5 bg-slate-50 dark:bg-slate-800/50 border-2 border-slate-200 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 font-mono text-center tracking-widest focus:outline-none focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition-all"
                      :disabled="loading"
                    />
                  </div>
                </div>
                
                <!-- Last Name Input -->
                <div class="space-y-2">
                  <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider">
                    Apellido
                  </label>
                  <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                      <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/>
                      </svg>
                    </div>
                    <input
                      v-model="loginForm.lastName"
                      type="text"
                      placeholder="Tu apellido"
                      class="w-full pl-12 pr-4 py-3.5 bg-slate-50 dark:bg-slate-800/50 border-2 border-slate-200 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 text-center focus:outline-none focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition-all"
                      :disabled="loading"
                    />
                  </div>
                </div>
              </div>
              
              <!-- Error Message -->
              <div v-if="error" class="mt-5 bg-rose-50 dark:bg-rose-900/20 border border-rose-200 dark:border-rose-800/50 rounded-xl px-4 py-3 flex items-center gap-3">
                <svg class="w-5 h-5 text-rose-500 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/>
                </svg>
                <p class="text-sm text-rose-600 dark:text-rose-400">{{ error }}</p>
              </div>
              
              <!-- Submit Button -->
              <button
                type="submit"
                :disabled="loading || !loginForm.creditId || !loginForm.lastName"
                class="w-full mt-8 py-4 bg-indigo-600 hover:bg-indigo-700 disabled:bg-slate-300 dark:disabled:bg-slate-700 text-white font-semibold rounded-xl shadow-lg shadow-indigo-500/30 disabled:shadow-none disabled:cursor-not-allowed flex items-center justify-center gap-3 transition-all duration-200 group"
              >
                <svg v-if="loading" class="animate-spin w-5 h-5" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                </svg>
                <span>{{ loading ? 'Verificando...' : 'Continuar' }}</span>
                <svg v-if="!loading" class="w-5 h-5 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/>
                </svg>
              </button>
            </form>
            
            <!-- Security Badge -->
            <div class="px-8 py-4 bg-slate-50 dark:bg-slate-800/30 border-t border-slate-100 dark:border-slate-800">
              <div class="flex items-center justify-center gap-2 text-slate-500 dark:text-slate-400">
                <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/>
                </svg>
                <span class="text-xs font-medium">Conexión segura</span>
              </div>
            </div>
          </div>
          
          <!-- Help Link -->
          <p class="text-center mt-8 text-sm text-slate-500 dark:text-slate-400">
            ¿No tienes acceso? 
            <a href="#" class="text-indigo-600 dark:text-indigo-400 font-medium hover:underline">Contacta al establecimiento</a>
          </p>
        </div>
      </main>
      
      <!-- Footer -->
      <footer class="px-6 py-6 text-center">
        <p class="text-xs text-slate-400 dark:text-slate-600">© 2026 {{ businessName }}. Todos los derechos reservados.</p>
      </footer>
    </div>

    <!-- ==================== DASHBOARD (AUTHENTICATED) ==================== -->
    <div v-else class="min-h-screen flex flex-col bg-slate-50 dark:bg-[#0C0C0E]">
      
      <!-- Compact Header -->
      <header class="px-4 py-4 border-b border-slate-200 dark:border-slate-800 bg-white dark:bg-[#161618]">
        <div class="flex items-center justify-between">
          <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-full bg-indigo-600 flex items-center justify-center text-white text-sm font-semibold">
              {{ creditData.customer?.name?.charAt(0)?.toUpperCase() }}
            </div>
            <div>
              <p class="text-sm font-semibold text-slate-900 dark:text-white leading-tight">{{ creditData.customer?.name }}</p>
              <p class="text-xs text-slate-500 dark:text-slate-400 font-mono">{{ creditData.customer?.credit_id }}</p>
            </div>
          </div>
          <button @click="logout" class="p-2 text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9"/>
            </svg>
          </button>
        </div>
      </header>
      
      <!-- Main Content -->
      <main class="flex-1 px-4 py-6 space-y-6">
        
        <!-- Status Banner -->
        <div :class="[
          'rounded-2xl p-5 border',
          creditData.credit?.balance > 0 
            ? 'bg-amber-50 dark:bg-amber-950/30 border-amber-200 dark:border-amber-800/50' 
            : 'bg-emerald-50 dark:bg-emerald-950/30 border-emerald-200 dark:border-emerald-800/50'
        ]">
          <div class="flex items-start justify-between">
            <div>
              <p class="text-xs font-medium uppercase tracking-wider mb-1" :class="creditData.credit?.balance > 0 ? 'text-amber-600 dark:text-amber-400' : 'text-emerald-600 dark:text-emerald-400'">
                {{ creditData.credit?.balance > 0 ? 'Balance pendiente' : 'Estado de cuenta' }}
              </p>
              <p class="text-3xl font-semibold tracking-tight" :class="creditData.credit?.balance > 0 ? 'text-amber-900 dark:text-amber-100' : 'text-emerald-900 dark:text-emerald-100'">
                {{ creditData.credit?.balance > 0 ? '$' + formatNumber(creditData.credit?.balance) : 'Al día' }}
              </p>
            </div>
            <div :class="[
              'w-10 h-10 rounded-xl flex items-center justify-center',
              creditData.credit?.balance > 0 ? 'bg-amber-100 dark:bg-amber-900/50' : 'bg-emerald-100 dark:bg-emerald-900/50'
            ]">
              <svg v-if="creditData.credit?.balance > 0" :class="['w-5 h-5', 'text-amber-600 dark:text-amber-400']" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
              </svg>
              <svg v-else class="w-5 h-5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
              </svg>
            </div>
          </div>
          
          <!-- Debt Breakdown -->
          <div v-if="creditData.credit?.balance > 0" class="mt-4 pt-4 border-t border-amber-200 dark:border-amber-800/50">
            <div class="grid grid-cols-2 gap-4 text-sm">
              <div>
                <p class="text-xs text-amber-600 dark:text-amber-400 mb-0.5">Productos</p>
                <p class="font-mono font-semibold text-amber-900 dark:text-amber-100">${{ formatNumber(creditData.credit?.balance_breakdown?.products || 0) }}</p>
              </div>
              <div class="text-right">
                <p class="text-xs text-amber-600 dark:text-amber-400 mb-0.5">Recargo {{ creditData.credit?.balance_breakdown?.surcharge_percentage || 10 }}%</p>
                <p class="font-mono font-semibold text-amber-900 dark:text-amber-100">+${{ formatNumber(creditData.credit?.balance_breakdown?.surcharge || 0) }}</p>
              </div>
            </div>
          </div>
        </div>
        
        <!-- Metrics Grid -->
        <div class="grid grid-cols-2 gap-3">
          <!-- Credit Limit -->
          <div class="bg-white dark:bg-[#161618] rounded-xl p-4 border border-slate-200 dark:border-slate-800">
            <div class="flex items-center gap-2 mb-3">
              <div class="w-8 h-8 rounded-lg bg-slate-100 dark:bg-slate-800 flex items-center justify-center">
                <svg class="w-4 h-4 text-slate-600 dark:text-slate-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5z"/>
                </svg>
              </div>
              <p class="text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">Cupo</p>
            </div>
            <p class="text-2xl font-semibold text-slate-900 dark:text-white font-mono tracking-tight">
              ${{ formatNumber(creditData.credit?.limit || 0) }}
            </p>
          </div>
          
          <!-- Available -->
          <div class="bg-white dark:bg-[#161618] rounded-xl p-4 border border-slate-200 dark:border-slate-800">
            <div class="flex items-center gap-2 mb-3">
              <div :class="[
                'w-8 h-8 rounded-lg flex items-center justify-center',
                creditData.credit?.available > 0 ? 'bg-emerald-100 dark:bg-emerald-900/30' : 'bg-slate-100 dark:bg-slate-800'
              ]">
                <svg :class="['w-4 h-4', creditData.credit?.available > 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-slate-400']" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
              </div>
              <p class="text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">Disponible</p>
            </div>
            <p :class="[
              'text-2xl font-semibold font-mono tracking-tight',
              creditData.credit?.available > 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-slate-400'
            ]">
              ${{ formatNumber(creditData.credit?.available || 0) }}
            </p>
          </div>
        </div>
        
        <!-- Tab Navigation -->
        <div class="flex gap-1 p-1.5 bg-slate-100 dark:bg-slate-800 rounded-xl">
          <button 
            @click="activeTab = 'compras'"
            :class="[
              'flex-1 flex items-center justify-center gap-2 py-3 text-sm font-medium rounded-lg transition-all',
              activeTab === 'compras' 
                ? 'bg-white dark:bg-[#161618] text-indigo-600 dark:text-indigo-400 shadow-sm' 
                : 'text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-300'
            ]"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993l1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 01-1.12-1.243l1.264-12A1.125 1.125 0 015.513 7.5h12.974c.576 0 1.059.435 1.119 1.007zM8.625 10.5a.375.375 0 11-.75 0 .375.375 0 01.75 0zm7.5 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z"/>
            </svg>
            Compras
          </button>
          <button 
            @click="activeTab = 'pagos'"
            :class="[
              'flex-1 flex items-center justify-center gap-2 py-3 text-sm font-medium rounded-lg transition-all',
              activeTab === 'pagos' 
                ? 'bg-white dark:bg-[#161618] text-indigo-600 dark:text-indigo-400 shadow-sm' 
                : 'text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-300'
            ]"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 00-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 01-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 003 15h-.75M15 10.5a3 3 0 11-6 0 3 3 0 016 0zm3 0h.008v.008H18V10.5zm-12 0h.008v.008H6V10.5z"/>
            </svg>
            Abonos
          </button>
        </div>
        
        <!-- Tab: Purchases -->
        <div v-if="activeTab === 'compras'" class="space-y-4">
          
          <!-- Current Credit (Pending Invoices) -->
          <div v-if="pendingInvoices.length > 0">
            <p class="text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-3 px-1">
              Pendiente · {{ pendingInvoices.length }}
            </p>
            <div class="bg-white dark:bg-[#161618] rounded-xl border border-slate-200 dark:border-slate-800 divide-y divide-slate-100 dark:divide-slate-800">
              <div 
                v-for="invoice in pendingInvoices" 
                :key="invoice.number"
                @click="openInvoiceModal(invoice)"
                class="p-4 hover:bg-slate-50 dark:hover:bg-slate-800/50 cursor-pointer transition-colors first:rounded-t-xl last:rounded-b-xl"
              >
                <div class="flex items-center justify-between">
                  <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-lg bg-slate-100 dark:bg-slate-800 flex items-center justify-center">
                      <svg class="w-5 h-5 text-slate-500 dark:text-slate-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/>
                      </svg>
                    </div>
                    <div>
                      <p class="text-sm font-medium text-slate-900 dark:text-white">{{ invoice.number }}</p>
                      <p class="text-xs text-slate-500 dark:text-slate-400">{{ formatDate(invoice.date) }}</p>
                    </div>
                  </div>
                  <div class="text-right">
                    <p class="text-sm font-semibold text-slate-900 dark:text-white font-mono">${{ formatNumber(invoice.total) }}</p>
                    <span :class="[
                      'inline-flex items-center px-2 py-0.5 rounded text-[10px] font-medium uppercase tracking-wide',
                      invoice.status === 'overdue' 
                        ? 'bg-rose-50 dark:bg-rose-900/30 text-rose-700 dark:text-rose-400' 
                        : 'bg-amber-50 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400'
                    ]">
                      {{ invoice.status === 'overdue' ? 'Vencida' : 'Pendiente' }}
                    </span>
                  </div>
                </div>
              </div>
            </div>
          </div>
          
          <!-- History (Paid Invoices) -->
          <div v-if="paidInvoices.length > 0">
            <button 
              @click="showHistory = !showHistory" 
              class="w-full flex items-center justify-between text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-3 px-1 py-2 hover:text-slate-700 dark:hover:text-slate-300 transition-colors"
            >
              <span>Historial · {{ paidInvoices.length }} pagadas</span>
              <svg :class="['w-4 h-4 transition-transform', showHistory ? 'rotate-180' : '']" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/>
              </svg>
            </button>
            
            <div v-if="showHistory" class="bg-white dark:bg-[#161618] rounded-xl border border-slate-200 dark:border-slate-800 divide-y divide-slate-100 dark:divide-slate-800">
              <div 
                v-for="invoice in paidInvoices" 
                :key="invoice.number"
                @click="openInvoiceModal(invoice)"
                class="p-4 hover:bg-slate-50 dark:hover:bg-slate-800/50 cursor-pointer transition-colors first:rounded-t-xl last:rounded-b-xl"
              >
                <div class="flex items-center justify-between">
                  <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-lg bg-emerald-50 dark:bg-emerald-900/30 flex items-center justify-center">
                      <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                      </svg>
                    </div>
                    <div>
                      <p class="text-sm font-medium text-slate-900 dark:text-white">{{ invoice.number }}</p>
                      <p class="text-xs text-slate-500 dark:text-slate-400">{{ formatDate(invoice.date) }}</p>
                    </div>
                  </div>
                  <div class="text-right">
                    <p class="text-sm font-semibold text-slate-900 dark:text-white font-mono">${{ formatNumber(invoice.total) }}</p>
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-medium uppercase tracking-wide bg-emerald-50 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400">
                      Pagada
                    </span>
                  </div>
                </div>
              </div>
            </div>
          </div>
          
          <!-- Empty State -->
          <div v-if="!creditData.invoices?.length" class="text-center py-16">
            <div class="w-12 h-12 rounded-xl bg-slate-100 dark:bg-slate-800 mx-auto flex items-center justify-center mb-4">
              <svg class="w-6 h-6 text-slate-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/>
              </svg>
            </div>
            <p class="text-sm text-slate-500 dark:text-slate-400">Sin compras registradas</p>
          </div>
        </div>
        
        <!-- Tab: Payments -->
        <div v-if="activeTab === 'pagos'" class="space-y-4">
          <div v-if="creditData.payments?.length > 0" class="bg-white dark:bg-[#161618] rounded-xl border border-slate-200 dark:border-slate-800 divide-y divide-slate-100 dark:divide-slate-800">
            <div 
              v-for="(payment, index) in creditData.payments" 
              :key="index"
              class="p-4 first:rounded-t-xl last:rounded-b-xl"
            >
              <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                  <div class="w-10 h-10 rounded-lg bg-emerald-50 dark:bg-emerald-900/30 flex items-center justify-center">
                    <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                  </div>
                  <div>
                    <p class="text-sm font-medium text-slate-900 dark:text-white">Abono</p>
                    <p class="text-xs text-slate-500 dark:text-slate-400">{{ formatDate(payment.date) }}</p>
                  </div>
                </div>
                <div class="text-right">
                  <p class="text-sm font-semibold text-emerald-600 dark:text-emerald-400 font-mono">+${{ formatNumber(payment.amount) }}</p>
                  <p v-if="payment.reference" class="text-xs text-slate-400 dark:text-slate-500 font-mono">{{ payment.reference }}</p>
                </div>
              </div>
            </div>
          </div>
          
          <!-- Empty State -->
          <div v-else class="text-center py-16">
            <div class="w-12 h-12 rounded-xl bg-slate-100 dark:bg-slate-800 mx-auto flex items-center justify-center mb-4">
              <svg class="w-6 h-6 text-slate-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 00-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 01-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 003 15h-.75M15 10.5a3 3 0 11-6 0 3 3 0 016 0zm3 0h.008v.008H18V10.5zm-12 0h.008v.008H6V10.5z"/>
              </svg>
            </div>
            <p class="text-sm text-slate-500 dark:text-slate-400">Sin abonos registrados</p>
          </div>
        </div>
        
      </main>
      
      <!-- Footer Info -->
      <footer class="px-4 py-4 border-t border-slate-200 dark:border-slate-800 bg-white dark:bg-[#161618]">
        <div class="flex items-center justify-between text-xs text-slate-500 dark:text-slate-400">
          <span class="font-mono">{{ creditData.customer?.credit_id }}</span>
          <span>{{ businessName }}</span>
        </div>
      </footer>
    </div>
    
    <!-- ==================== INVOICE DETAIL MODAL ==================== -->
    <Teleport to="body">
      <div v-if="showInvoiceModal" class="fixed inset-0 z-50 flex items-end sm:items-center justify-center">
        <!-- Backdrop -->
        <div class="absolute inset-0 bg-black/40 " @click="closeInvoiceModal"></div>
        
        <!-- Modal -->
        <div class="relative bg-white dark:bg-[#161618] w-full sm:max-w-md sm:rounded-2xl rounded-t-2xl max-h-[85vh] overflow-hidden shadow-2xl animate-slide-up">
          <!-- Header -->
          <div class="px-5 py-4 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between">
            <div>
              <h3 class="text-base font-semibold text-slate-900 dark:text-white">{{ invoiceDetail?.number }}</h3>
              <p class="text-xs text-slate-500 dark:text-slate-400">{{ invoiceDetail?.date }}</p>
            </div>
            <button @click="closeInvoiceModal" class="p-2 text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg transition-colors">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
              </svg>
            </button>
          </div>
          
          <!-- Loading -->
          <div v-if="loadingInvoice" class="p-8 text-center">
            <svg class="animate-spin w-6 h-6 mx-auto text-indigo-500" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
            </svg>
            <p class="text-slate-500 dark:text-slate-400 mt-3 text-sm">Cargando...</p>
          </div>
          
          <!-- Content -->
          <div v-else-if="invoiceDetail" class="overflow-y-auto max-h-[calc(85vh-140px)]">
            <!-- Products -->
            <div class="p-5">
              <p class="text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-3">Productos</p>
              
              <div v-if="!invoiceDetail.items?.length" class="text-center py-8">
                <p class="text-sm text-slate-400 dark:text-slate-500">Detalle no disponible</p>
              </div>
              
              <div v-else class="space-y-3">
                <div 
                  v-for="(item, idx) in invoiceDetail.items" 
                  :key="idx" 
                  class="flex items-center justify-between py-2"
                >
                  <div class="flex-1">
                    <p class="text-sm text-slate-900 dark:text-white">{{ item.product_name }}</p>
                    <p class="text-xs text-slate-500 dark:text-slate-400">{{ item.quantity }} × ${{ formatNumber(item.unit_price) }}</p>
                  </div>
                  <p class="text-sm font-mono font-medium text-slate-900 dark:text-white">${{ formatNumber(item.total) }}</p>
                </div>
              </div>
            </div>
            
            <!-- Totals -->
            <div class="px-5 py-4 bg-slate-50 dark:bg-slate-800/50 border-t border-slate-200 dark:border-slate-800">
              <div class="space-y-2">
                <div class="flex justify-between text-sm">
                  <span class="text-slate-500 dark:text-slate-400">Subtotal</span>
                  <span class="font-mono text-slate-700 dark:text-slate-300">${{ formatNumber(invoiceDetail.subtotal) }}</span>
                </div>
                <div class="flex justify-between text-sm">
                  <span class="text-slate-500 dark:text-slate-400">Recargo ({{ invoiceDetail.surcharge_percentage || creditData.credit?.balance_breakdown?.surcharge_percentage || 10 }}%)</span>
                  <span class="font-mono text-amber-600 dark:text-amber-400">+${{ formatNumber(invoiceDetail.surcharge) }}</span>
                </div>
                <div class="flex justify-between text-base font-semibold pt-2 border-t border-slate-200 dark:border-slate-700">
                  <span class="text-slate-900 dark:text-white">Total</span>
                  <span class="font-mono text-slate-900 dark:text-white">${{ formatNumber(invoiceDetail.total) }}</span>
                </div>
              </div>
            </div>
            
            <!-- Status -->
            <div class="px-5 py-4 border-t border-slate-200 dark:border-slate-800">
              <div :class="[
                'flex items-center justify-center gap-2 py-3 rounded-xl text-sm font-medium',
                invoiceDetail.status === 'paid' 
                  ? 'bg-emerald-50 dark:bg-emerald-900/20 text-emerald-700 dark:text-emerald-400' 
                  : invoiceDetail.status === 'overdue'
                    ? 'bg-rose-50 dark:bg-rose-900/20 text-rose-700 dark:text-rose-400'
                    : 'bg-amber-50 dark:bg-amber-900/20 text-amber-700 dark:text-amber-400'
              ]">
                <svg v-if="invoiceDetail.status === 'paid'" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <svg v-else-if="invoiceDetail.status === 'overdue'" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/>
                </svg>
                <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                {{ invoiceDetail.status === 'paid' ? 'Pagada' : invoiceDetail.status === 'overdue' ? 'Vencida' : 'Pendiente' }}
              </div>
            </div>
          </div>
        </div>
      </div>
    </Teleport>
    
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import axios from 'axios'

// State
const isAuthenticated = ref(false)
const loading = ref(false)
const error = ref('')
const businessName = ref('Credit Portal')
const creditData = ref({})
const activeTab = ref('compras')
const showHistory = ref(false)

// Invoice Modal
const showInvoiceModal = ref(false)
const loadingInvoice = ref(false)
const invoiceDetail = ref(null)
const currentToken = ref('')

// Original zoom
let originalZoom = ''

const loginForm = ref({
  creditId: '',
  lastName: ''
})

// Computed
const pendingInvoices = computed(() => {
  return creditData.value.invoices?.filter(inv => inv.status !== 'paid') || []
})

const paidInvoices = computed(() => {
  return creditData.value.invoices?.filter(inv => inv.status === 'paid') || []
})

// Lifecycle
onMounted(async () => {
  const html = document.documentElement
  originalZoom = html.style.zoom || ''
  html.style.zoom = '1'
  
  const urlParams = new URLSearchParams(window.location.search)
  const token = urlParams.get('token')
  
  // Get business name from subdomain
  const hostname = window.location.hostname
  const subdomain = hostname.split('.')[0]
  if (subdomain && subdomain !== 'localhost' && subdomain !== 'www') {
    businessName.value = subdomain.charAt(0).toUpperCase() + subdomain.slice(1)
  }
  
  if (token) {
    await accessByToken(token)
  }
})

onUnmounted(() => {
  const html = document.documentElement
  if (originalZoom) {
    html.style.zoom = originalZoom
  }
})

// Methods
const accessByToken = async (token) => {
  loading.value = true
  error.value = ''
  
  try {
    const response = await axios.post('/api/credit-portal/access-by-token', { token })
    
    if (response.data.success) {
      creditData.value = response.data.data
      currentToken.value = token
      isAuthenticated.value = true
    } else {
      error.value = response.data.message || 'Error de acceso'
    }
  } catch (e) {
    error.value = e.response?.data?.message || 'Enlace inválido'
  } finally {
    loading.value = false
  }
}

const handleLogin = async () => {
  loading.value = true
  error.value = ''
  
  try {
    const response = await axios.post('/api/credit-portal/access-by-credentials', {
      credit_id: loginForm.value.creditId.toUpperCase(),
      last_name: loginForm.value.lastName
    })
    
    if (response.data.success) {
      creditData.value = response.data.data
      isAuthenticated.value = true
    } else {
      error.value = response.data.message || 'Datos incorrectos'
    }
  } catch (e) {
    error.value = e.response?.data?.message || 'Error de conexión'
  } finally {
    loading.value = false
  }
}

const logout = () => {
  isAuthenticated.value = false
  creditData.value = {}
  loginForm.value = { creditId: '', lastName: '' }
  currentToken.value = ''
  window.history.replaceState({}, '', window.location.pathname)
}

const openInvoiceModal = async (invoice) => {
  showInvoiceModal.value = true
  loadingInvoice.value = true
  invoiceDetail.value = null
  
  try {
    const response = await axios.post('/api/credit-portal/invoice-detail', {
      invoice_id: invoice.id,
      token: currentToken.value || null,
      credit_id: creditData.value.customer?.credit_id || null
    })
    
    if (response.data.success) {
      invoiceDetail.value = response.data.data
    } else {
      invoiceDetail.value = {
        ...invoice,
        items: [],
        subtotal: invoice.subtotal || invoice.total,
        surcharge: 0,
        total: invoice.total
      }
    }
  } catch (e) {
    invoiceDetail.value = {
      ...invoice,
      items: [],
      subtotal: invoice.subtotal || invoice.total,
      surcharge: 0,
      total: invoice.total
    }
  } finally {
    loadingInvoice.value = false
  }
}

const closeInvoiceModal = () => {
  showInvoiceModal.value = false
  invoiceDetail.value = null
}

const formatNumber = (num) => {
  return new Intl.NumberFormat('es-CO').format(num || 0)
}

const formatDate = (date) => {
  if (!date) return '-'
  return new Date(date).toLocaleDateString('es-CO', {
    day: 'numeric',
    month: 'short',
    year: 'numeric'
  })
}
</script>

<style scoped>
@keyframes slideUp {
  from { 
    transform: translateY(100%); 
    opacity: 0; 
  }
  to { 
    transform: translateY(0); 
    opacity: 1; 
  }
}

.animate-slide-up {
  animation: slideUp 0.3s ease-out;
}

/* Custom scrollbar */
::-webkit-scrollbar {
  width: 4px;
}

::-webkit-scrollbar-track {
  background: transparent;
}

::-webkit-scrollbar-thumb {
  background: #e2e8f0;
  border-radius: 2px;
}

.dark ::-webkit-scrollbar-thumb {
  background: #334155;
}
</style>
